<?php

declare(strict_types=1);

/**
 * F-4 smoke: drive the re-redesigned journal form (independent
 * credit-left / debit-right tables, vanilla JS combobox with romaji
 * search, summary optional, entity auto-select).
 *
 *   1. GET /ui/journals/new with NO entity in session  → controller must
 *      auto-select the first owned entity AND populate fiscal_terms.
 *   2. Verify the rendered template:
 *        - new HTML markers (independent tables, combobox, JSON tags)
 *        - old F-2 markers gone (no `pairs[…]`, no datalist, no "ペア")
 *        - account_titles JSON includes `romaji` field
 *        - tax_defaults JSON tag is present (may be empty, but the tag exists)
 *   3. Simple journal POST (lines[] schema): credit 売上 1000 / debit 現金 1000.
 *   4. Compound journal POST: credit 現金 1100 / debit 通信費 1000 + 仮払消費税 100.
 *   5. Summary-less POST succeeds (F-4: 摘要 is optional).
 *
 * Run inside the renewal app container:
 *   docker exec accounting_renewal_app php /var/www/html/scripts/dev/_f4_journal_form_smoke.php
 */

use Rucaro\Http\Controller\Ui\Journal\JournalNewController;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Container\ContainerBootstrap;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\SessionStore;

require __DIR__ . '/../../vendor/autoload.php';

$pdo = new PDO(
    'mysql:host=db;port=3306;dbname=rucaro;charset=utf8mb4',
    'root',
    'root',
    [
        PDO::ATTR_ERRMODE          => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
);
$pdo->exec("SET time_zone = '+00:00'");

function fail(string $m, int $code = 1): never
{
    fwrite(STDERR, "FAIL: {$m}\n");
    exit($code);
}

// --- Resolve test bench (株式会社アクセク, period 20) ---
$stmt = $pdo->prepare("SELECT id FROM entities WHERE name='株式会社アクセク'");
$stmt->execute();
$entityRow = $stmt->fetch(PDO::FETCH_ASSOC);
if ($entityRow === false) { fail('entity not found', 2); }
$entityId = UlidGenerator::encode($entityRow['id']);

$stmt = $pdo->prepare('SELECT id FROM fiscal_terms WHERE entity_id = :e AND fiscal_period = 20');
$stmt->bindValue(':e', $entityRow['id'], PDO::PARAM_LOB);
$stmt->execute();
$termRow = $stmt->fetch(PDO::FETCH_ASSOC);
if ($termRow === false) { fail('fiscal term 20 not found', 2); }
$fiscalTermId = UlidGenerator::encode($termRow['id']);

// Resolve a few representative account titles
$stmt = $pdo->prepare(
    "SELECT id, code, name FROM account_titles
      WHERE entity_id = :e AND deleted_at IS NULL AND code IN ('L0010','L0004','L0011','L0001','L0095')"
);
$stmt->bindValue(':e', $entityRow['id'], PDO::PARAM_LOB);
$stmt->execute();
$accounts = [];
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
    $accounts[$r['code']] = ['id' => UlidGenerator::encode($r['id']), 'name' => $r['name']];
}
foreach (['L0010', 'L0004'] as $must) {
    if (!isset($accounts[$must])) { fail('expected account title ' . $must . ' missing', 2); }
}

// Pick any expense / revenue / liability account for compound journal lines.
// We don't need exact codes — just cooperate with whatever is in master.
$stmt = $pdo->prepare(
    "SELECT id, code, name FROM account_titles
      WHERE entity_id = :e AND deleted_at IS NULL AND name = '仮払消費税' LIMIT 1"
);
$stmt->bindValue(':e', $entityRow['id'], PDO::PARAM_LOB);
$stmt->execute();
$taxRow = $stmt->fetch(PDO::FETCH_ASSOC);
$taxAccountId = $taxRow ? UlidGenerator::encode($taxRow['id']) : $accounts['L0010']['id'];

// Resolve admin user
$stmt = $pdo->prepare("SELECT id FROM users WHERE login_id = 'admin' LIMIT 1");
$stmt->execute();
$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
if ($userRow === false) { fail('admin user not found', 2); }
$userId = UlidGenerator::encode($userRow['id']);

// --- Build container + controller ---
$container = ContainerBootstrap::build($pdo);
$controller = $container->get(JournalNewController::class);
if (!$controller instanceof JournalNewController) { fail('JournalNewController not wired', 3); }
$session = $container->get(SessionStore::class);
$csrf    = $container->get(CsrfTokenManager::class);
if (!$session instanceof SessionStore || !$csrf instanceof CsrfTokenManager) {
    fail('SessionStore / CsrfTokenManager not wired', 3);
}

// ============================================================
// Step 1: GET with NO entity in session — controller must auto-select.
// ============================================================
$_SESSION = [];
$_SESSION[SessionStore::KEY_USER_ID] = $userId;
// Intentionally *not* setting selected_entity / selected_fiscal_term.
$showResp = $controller->show(new ServerRequest('GET', '/ui/journals/new', [], [], null, ''));
if ($showResp->status !== 200) {
    if ($showResp->status === 303) {
        fail('GET 303 (auto-select did not engage; redirect=' . ($showResp->headers['Location'] ?? '?') . ')', 4);
    }
    fail('GET /ui/journals/new status=' . $showResp->status, 4);
}
$picked = $session->getSelectedEntity();
if ($picked === null) {
    fail('entity was not auto-selected after GET', 4);
}
echo "step1: GET /ui/journals/new auto-selected entity " . substr($picked, 0, 8) . "...\n";

$body = $showResp->body;

// New markers (template structure)
$mustContain = [
    'id="journal-form"',
    'id="account-titles-data"',
    'id="sub-accounts-data"',
    'id="tax-defaults-data"',
    'id="recent-journals-data"',
    'id="balance-bar"',
    'id="tax-mode-inclusive"',
    'id="tax-mode-exclusive"',
    'class="balance-sticky-bar',
    'data-side="credit"',
    'data-side="debit"',
    'class="combobox',
    '[side]" value="credit"',
    '[side]" value="debit"',
    'side-add-btn',
    'remove-line-btn',
    // F-4: per-line memo input (one per line, not pair-level).
    'class="form-control form-control-sm line-memo"',
];
foreach ($mustContain as $needle) {
    if (!str_contains($body, $needle)) { fail("template missing required marker: {$needle}", 5); }
}

// Old / wrong markers must NOT appear
$mustNotContain = [
    // F-2 schema gone
    'pairs[0]',
    // F-2 css class gone
    'pair-journal-table',
    // HTML5 datalist replaced with JS combobox
    'id="account-titles-datalist"',
    // No "ペア" terminology in the redesigned form
    'ペア',
];
foreach ($mustNotContain as $needle) {
    if (str_contains($body, $needle)) { fail("template should NOT contain: {$needle}", 5); }
}

// account_titles JSON must include romaji field. Locate the JSON tag.
$tagPos = strpos($body, 'id="account-titles-data"');
if ($tagPos === false) { fail('account-titles-data tag missing', 5); }
$jsonStart = strpos($body, '>', $tagPos) + 1;
$jsonEnd = strpos($body, '</script>', $jsonStart);
$json = substr($body, $jsonStart, $jsonEnd - $jsonStart);
$decoded = json_decode($json, true);
if (!is_array($decoded) || $decoded === []) { fail('account-titles-data JSON empty', 5); }
$first = $decoded[0];
if (!array_key_exists('romaji', $first)) { fail('account-titles JSON missing romaji field', 5); }

// Make sure at least one expected romaji alias is present (consumption tax for shoumou, communications for tsuushin)
$haveShoumou = false; $haveTsuushin = false;
foreach ($decoded as $a) {
    if (isset($a['romaji']) && str_contains($a['romaji'], 'shoumou')) $haveShoumou = true;
    if (isset($a['romaji']) && str_contains($a['romaji'], 'tsuushin')) $haveTsuushin = true;
}
if (!$haveShoumou)  { fail('no account has romaji containing "shoumou" — RomajiAlias not wired?', 5); }
if (!$haveTsuushin) { fail('no account has romaji containing "tsuushin" — RomajiAlias not wired?', 5); }

echo "step2: form template structure OK (no pairs, no datalist, no \"ペア\"; combobox + romaji JSON present)\n";

// ============================================================
// Step 3: simple POST with new lines[] schema
// ============================================================
$session->setSelectedEntity($entityId);
$session->setSelectedFiscalTerm($fiscalTermId);
$csrfTok = $csrf->generateToken(JournalNewController::CSRF_FORM_ID);
$body3 = http_build_query([
    '_csrf'          => $csrfTok,
    'journal_date'   => date('Y-m-d'),
    'fiscal_term_id' => $fiscalTermId,
    'summary'        => 'F-4 simple smoke',
    'lines' => [
        ['side' => 'credit', 'account_title_id' => $accounts['L0004']['id'], 'amount' => '1000', 'memo' => 'C1', 'tax_rate_percent' => '0.00', 'tax_amount' => '0.0000', 'is_tax_reduced' => '0'],
        ['side' => 'debit',  'account_title_id' => $accounts['L0010']['id'], 'amount' => '1000', 'memo' => 'D1', 'tax_rate_percent' => '0.00', 'tax_amount' => '0.0000', 'is_tax_reduced' => '0'],
    ],
]);
$req = new ServerRequest('POST', '/ui/journals/new', ['content-type' => 'application/x-www-form-urlencoded'], [], null, $body3);
$resp = $controller->submit($req);
if ($resp->status !== 303) {
    echo substr($resp->body, 0, 1500) . "\n";
    fail('simple POST status=' . $resp->status, 6);
}
$loc = $resp->headers['Location'] ?? '';
if (!preg_match('#^/ui/journals/([0-9A-HJKMNP-TV-Z]{26})$#', $loc, $m)) { fail('bad redirect ' . $loc, 6); }
$jid1 = $m[1];

// Verify per-line memos persisted
$stmt = $pdo->prepare('SELECT side, amount, memo FROM journal_entry_lines WHERE entry_id = :j ORDER BY line_no');
$stmt->bindValue(':j', UlidGenerator::decode($jid1), PDO::PARAM_LOB);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (count($rows) !== 2) { fail('expected 2 lines, got ' . count($rows), 6); }
$memos = []; foreach ($rows as $r) { $memos[$r['side']] = $r['memo']; }
if (($memos['credit'] ?? '') !== 'C1' || ($memos['debit'] ?? '') !== 'D1') {
    fail('per-line memo not persisted: ' . json_encode($memos), 6);
}
echo "step3: simple POST 303 → /ui/journals/{$jid1} (per-line memos C1/D1 persisted)\n";

// ============================================================
// Step 4: compound POST (1 credit, 2 debits)
// ============================================================
$csrfTok2 = $csrf->generateToken(JournalNewController::CSRF_FORM_ID);
$body4 = http_build_query([
    '_csrf'          => $csrfTok2,
    'journal_date'   => date('Y-m-d'),
    'fiscal_term_id' => $fiscalTermId,
    'summary'        => 'F-4 compound smoke',
    'lines' => [
        ['side' => 'credit', 'account_title_id' => $accounts['L0004']['id'], 'amount' => '1100', 'memo' => '現金支払',   'tax_rate_percent' => '0.00',  'tax_amount' => '0.0000',  'is_tax_reduced' => '0'],
        ['side' => 'debit',  'account_title_id' => $accounts['L0010']['id'], 'amount' => '1000', 'memo' => '通信費',     'tax_rate_percent' => '10.00', 'tax_amount' => '90.9091', 'is_tax_reduced' => '0'],
        ['side' => 'debit',  'account_title_id' => $taxAccountId,            'amount' => '100',  'memo' => '仮払消費税',  'tax_rate_percent' => '0.00',  'tax_amount' => '0.0000',  'is_tax_reduced' => '0'],
    ],
]);
$req = new ServerRequest('POST', '/ui/journals/new', ['content-type' => 'application/x-www-form-urlencoded'], [], null, $body4);
$resp = $controller->submit($req);
if ($resp->status !== 303) {
    echo substr($resp->body, 0, 1500) . "\n";
    fail('compound POST status=' . $resp->status, 7);
}
$loc = $resp->headers['Location'] ?? '';
preg_match('#/ui/journals/([0-9A-HJKMNP-TV-Z]{26})$#', $loc, $m);
$jid2 = $m[1] ?? '';
$stmt->bindValue(':j', UlidGenerator::decode($jid2), PDO::PARAM_LOB);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$creditCt = 0; $debitCt = 0; $creditTotal = 0.0; $debitTotal = 0.0;
foreach ($rows as $r) {
    if ($r['side'] === 'credit') { $creditCt++; $creditTotal += (float) $r['amount']; }
    if ($r['side'] === 'debit')  { $debitCt++;  $debitTotal  += (float) $r['amount']; }
}
if ($creditCt !== 1 || $debitCt !== 2) { fail("compound shape wrong: c={$creditCt} d={$debitCt}", 7); }
if (abs($creditTotal - $debitTotal) > 0.0001) { fail("compound totals not balanced: c={$creditTotal} d={$debitTotal}", 7); }
echo "step4: compound POST 303 → /ui/journals/{$jid2} (1 credit + 2 debits, balanced at " . (int) $creditTotal . ")\n";

// ============================================================
// Step 5: POST with summary OMITTED — must succeed (F-4: optional)
// ============================================================
$csrfTok3 = $csrf->generateToken(JournalNewController::CSRF_FORM_ID);
$body5 = http_build_query([
    '_csrf'          => $csrfTok3,
    'journal_date'   => date('Y-m-d'),
    'fiscal_term_id' => $fiscalTermId,
    'summary'        => '', // explicitly empty
    'lines' => [
        ['side' => 'credit', 'account_title_id' => $accounts['L0004']['id'], 'amount' => '500', 'memo' => '', 'tax_rate_percent' => '0.00', 'tax_amount' => '0.0000', 'is_tax_reduced' => '0'],
        ['side' => 'debit',  'account_title_id' => $accounts['L0010']['id'], 'amount' => '500', 'memo' => '', 'tax_rate_percent' => '0.00', 'tax_amount' => '0.0000', 'is_tax_reduced' => '0'],
    ],
]);
$req = new ServerRequest('POST', '/ui/journals/new', ['content-type' => 'application/x-www-form-urlencoded'], [], null, $body5);
$resp = $controller->submit($req);
if ($resp->status !== 303) {
    echo substr($resp->body, 0, 1500) . "\n";
    fail('summary-less POST status=' . $resp->status . ' (summary should be optional)', 8);
}
preg_match('#/ui/journals/([0-9A-HJKMNP-TV-Z]{26})$#', $resp->headers['Location'] ?? '', $m);
$jid3 = $m[1] ?? '';
echo "step5: summary-less POST 303 → /ui/journals/{$jid3} (summary is optional)\n";

// ============================================================
// Cleanup: physical-delete smoke journals
// ============================================================
foreach ([$jid1, $jid2, $jid3] as $jid) {
    if (!$jid) continue;
    $stmt = $pdo->prepare('DELETE FROM journal_entry_lines WHERE entry_id = :j');
    $stmt->bindValue(':j', UlidGenerator::decode($jid), PDO::PARAM_LOB);
    $stmt->execute();
    $stmt = $pdo->prepare('DELETE FROM journal_entries WHERE id = :j');
    $stmt->bindValue(':j', UlidGenerator::decode($jid), PDO::PARAM_LOB);
    $stmt->execute();
}
echo "cleanup: smoke journals removed\n";
echo "F-4 SMOKE PASS\n";
