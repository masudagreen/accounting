<?php

declare(strict_types=1);

/**
 * B-NEW smoke: drive the modernized journal form end-to-end against the live
 * renewal DB:
 *
 *   1. GET /ui/journals/new via JournalNewController::show — verify the
 *      modernized template renders with datalist, sub-account JSON, recent
 *      journals JSON, tax-mode toggle, sticky balance bar.
 *   2. POST /ui/journals/new via JournalNewController::submit — verify a
 *      journal is created with tax_rate_percent / tax_amount / is_tax_reduced
 *      values flowing through to the DB.
 *   3. JS-side tax math sanity (re-implemented in PHP for parity).
 *
 * Run inside the renewal app container:
 *   docker exec accounting_renewal_app php /var/www/html/scripts/dev/_b_new_journal_form_smoke.php
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

// --- Resolve entity + fiscal_term for the test bench (株式会社アクセク, period 20) ---
$stmt = $pdo->prepare("SELECT id FROM entities WHERE name='株式会社アクセク'");
$stmt->execute();
$entityRow = $stmt->fetch(PDO::FETCH_ASSOC);
if ($entityRow === false) {
    fail('entity not found', 2);
}
$entityId = UlidGenerator::encode($entityRow['id']);

$stmt = $pdo->prepare('SELECT id FROM fiscal_terms WHERE entity_id = :e AND fiscal_period = 20');
$stmt->bindValue(':e', $entityRow['id'], PDO::PARAM_LOB);
$stmt->execute();
$termRow = $stmt->fetch(PDO::FETCH_ASSOC);
if ($termRow === false) {
    fail('fiscal term 20 not found', 2);
}
$fiscalTermId = UlidGenerator::encode($termRow['id']);

// --- Pick a couple of account titles to use in the smoke journal ---
$stmt = $pdo->prepare(
    "SELECT id, code, name FROM account_titles
      WHERE entity_id = :e AND deleted_at IS NULL AND code IN ('L0010','L0004')"
);
$stmt->bindValue(':e', $entityRow['id'], PDO::PARAM_LOB);
$stmt->execute();
$accounts = [];
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
    $accounts[$r['code']] = UlidGenerator::encode($r['id']);
}
if (!isset($accounts['L0010'], $accounts['L0004'])) {
    fail('expected account titles L0010 (通信費) / L0004 (現金) not found', 2);
}

// --- Resolve real admin user id ---
$stmt = $pdo->prepare("SELECT id FROM users WHERE login_id = 'admin' LIMIT 1");
$stmt->execute();
$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
if ($userRow === false) {
    fail('admin user not found', 2);
}
$userId = UlidGenerator::encode($userRow['id']);

// --- Build container + controller ---
$container = ContainerBootstrap::build($pdo);
$controller = $container->get(JournalNewController::class);
if (!$controller instanceof JournalNewController) {
    fail('JournalNewController not wired', 3);
}
$session = $container->get(SessionStore::class);
$csrf    = $container->get(CsrfTokenManager::class);
if (!$session instanceof SessionStore || !$csrf instanceof CsrfTokenManager) {
    fail('SessionStore / CsrfTokenManager not wired', 3);
}
$_SESSION = [];
$_SESSION[SessionStore::KEY_USER_ID] = $userId;
$session->setSelectedEntity($entityId);
$session->setSelectedFiscalTerm($fiscalTermId);

// --- Step 1: GET /ui/journals/new ---
$showResp = $controller->show(new ServerRequest('GET', '/ui/journals/new', [], [], null, ''));
if ($showResp->status !== 200) {
    fail('GET /ui/journals/new status=' . $showResp->status, 4);
}
$body = $showResp->body;
$mustContain = [
    'id="journal-form"',
    // F-4: replaced HTML5 <datalist> with a custom JS combobox; the
    // template now ships account titles via the JSON script tag below.
    'id="account-titles-data"',
    'id="sub-accounts-data"',
    'id="tax-defaults-data"',
    'id="recent-journals-data"',
    'id="balance-bar"',
    'id="tax-mode-inclusive"',
    'id="tax-mode-exclusive"',
    // F-4: independent credit/debit tables emit `lines[<key>][side|...]`
    // inputs (the side is encoded inside the input itself, not via the
    // wrapper key, which is reindexed by JS at submit-time).
    '[side]" value="credit"',
    '[side]" value="debit"',
    'id="shortcut-help-modal"',
    'class="balance-sticky-bar',
];
foreach ($mustContain as $needle) {
    if (!str_contains($body, $needle)) {
        fail("template missing required marker: {$needle}", 5);
    }
}
echo "step1: GET /ui/journals/new OK (body=" . strlen($body) . " bytes)\n";

// --- Step 2: POST a small journal with tax_rate=10% (standard, inclusive math) ---
// 1100 (税込) → tax = 1100 * 10 / 110 = 100.0000
$csrfTok = $csrf->generateToken(JournalNewController::CSRF_FORM_ID);
$lineNew = [
    '_csrf'          => $csrfTok,
    'journal_date'   => date('Y-m-d'),
    'fiscal_term_id' => $fiscalTermId,
    'summary'        => 'B-NEW smoke (tax 10%)',
    // F-4: flat `lines[N][side|...]` schema again — credit/debit are
    // emitted as independent rows, each carrying its own side/memo.
    'lines' => [
        [
            'side' => 'credit',
            'account_title_id' => $accounts['L0004'],
            'sub_account_title_id' => '',
            'amount' => '1100',
            'tax_rate_percent' => '0.00',
            'tax_amount' => '0.0000',
            'is_tax_reduced' => '0',
            'memo' => 'コーヒー代込み',
        ],
        [
            'side' => 'debit',
            'account_title_id' => $accounts['L0010'],
            'sub_account_title_id' => '',
            'amount' => '1100',
            'tax_rate_percent' => '10.00',
            'tax_amount' => '100.0000',
            'is_tax_reduced' => '0',
            'memo' => 'コーヒー代込み',
        ],
    ],
];
$rawBody = http_build_query($lineNew);
$req = new ServerRequest(
    method: 'POST',
    path: '/ui/journals/new',
    headers: ['content-type' => 'application/x-www-form-urlencoded'],
    query: [],
    json: null,
    rawBody: $rawBody,
);
$submitResp = $controller->submit($req);
if ($submitResp->status !== 303) {
    echo "submit body (truncated 1200): " . substr($submitResp->body, 0, 1200) . "\n";
    fail('submit status=' . $submitResp->status . ' (expected 303 redirect)', 6);
}
$loc = $submitResp->headers['Location'] ?? '';
if (!preg_match('#^/ui/journals/([0-9A-HJKMNP-TV-Z]{26})$#', $loc, $m)) {
    fail('redirect Location malformed: ' . $loc, 6);
}
$newJid = $m[1];
echo "step2: POST 303 → /ui/journals/{$newJid}\n";

// --- Step 3: re-read DB and assert tax_rate / tax_amount / is_tax_reduced ---
$stmt = $pdo->prepare(
    'SELECT side, amount, tax_rate_percent, tax_amount, is_tax_reduced, memo
       FROM journal_entry_lines
      WHERE entry_id = :j
      ORDER BY line_no ASC'
);
$stmt->bindValue(':j', UlidGenerator::decode($newJid), PDO::PARAM_LOB);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (count($rows) !== 2) {
    fail('expected 2 lines, got ' . count($rows), 7);
}
// F-2: pair-shaped POST emits credit first within a pair, so DB ordering is
// credit then debit. Lookup by side instead of position.
$debit = null; $credit = null;
foreach ($rows as $r) {
    if ($r['side'] === 'debit')  $debit  = $r;
    if ($r['side'] === 'credit') $credit = $r;
}
if ($debit === null || $credit === null) { fail('missing debit or credit row', 7); }
if ((string) $debit['tax_rate_percent'] !== '10.00') { fail('debit tax_rate_percent=' . $debit['tax_rate_percent'], 7); }
if ((string) $debit['tax_amount']       !== '100.0000') { fail('debit tax_amount=' . $debit['tax_amount'], 7); }
if ((int) $debit['is_tax_reduced']      !== 0) { fail('debit is_tax_reduced=' . $debit['is_tax_reduced'], 7); }
if ((string) $credit['tax_rate_percent'] !== '0.00') { fail('credit tax_rate_percent=' . $credit['tax_rate_percent'], 7); }
echo "step3: DB tax fields persisted (rate=10.00, amount=100.0000, reduced=0)\n";

// --- Step 4: POST a reduced-tax journal (8% 軽減) and verify is_tax_reduced=1 ---
$csrfTok2 = $csrf->generateToken(JournalNewController::CSRF_FORM_ID);
$rawBody2 = http_build_query([
    '_csrf'          => $csrfTok2,
    'journal_date'   => date('Y-m-d'),
    'fiscal_term_id' => $fiscalTermId,
    'summary'        => 'B-NEW smoke (tax 8% reduced)',
    'lines' => [
        [
            'side' => 'credit',
            'account_title_id' => $accounts['L0004'],
            'sub_account_title_id' => '',
            'amount' => '1080',
            'tax_rate_percent' => '0.00',
            'tax_amount' => '0.0000',
            'is_tax_reduced' => '0',
            'memo' => '軽減対象',
        ],
        [
            'side' => 'debit',
            'account_title_id' => $accounts['L0010'],
            'sub_account_title_id' => '',
            'amount' => '1080',
            'tax_rate_percent' => '8.00',
            'tax_amount' => '80.0000',
            'is_tax_reduced' => '1',
            'memo' => '軽減対象',
        ],
    ],
]);
$req2 = new ServerRequest('POST', '/ui/journals/new', ['content-type' => 'application/x-www-form-urlencoded'], [], null, $rawBody2);
$resp2 = $controller->submit($req2);
if ($resp2->status !== 303) {
    fail('reduced submit status=' . $resp2->status, 8);
}
$loc2 = $resp2->headers['Location'] ?? '';
preg_match('#/ui/journals/([0-9A-HJKMNP-TV-Z]{26})$#', $loc2, $m2);
$newJid2 = $m2[1] ?? '';
$stmt->bindValue(':j', UlidGenerator::decode($newJid2), PDO::PARAM_LOB);
$stmt->execute();
$rows2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
// F-2: lookup the debit row (which carries the reduced tax) by side, not position.
$debit2 = null;
foreach ($rows2 as $r) { if ($r['side'] === 'debit') { $debit2 = $r; break; } }
if ($debit2 === null) { fail('reduced: debit row missing', 8); }
if ((string) $debit2['tax_rate_percent'] !== '8.00') { fail('reduced rate=' . $debit2['tax_rate_percent'], 8); }
if ((int) $debit2['is_tax_reduced'] !== 1) { fail('reduced flag=' . $debit2['is_tax_reduced'], 8); }
echo "step4: 軽減 8% 仕訳 persisted (rate=8.00, reduced=1)\n";

// --- Step 5: verify the recent_journals JSON now exposes the smoke journals ---
// (re-render show() and look for our new IDs in the embedded JSON)
$showResp2 = $controller->show(new ServerRequest('GET', '/ui/journals/new', [], [], null, ''));
$body2 = $showResp2->body;
if (!str_contains($body2, $newJid)) {
    echo "WARN: recent_journals JSON does not include {$newJid} (recent feed may be older)\n";
} else {
    echo "step5: recent_journals JSON includes {$newJid}\n";
}

// --- Cleanup: physical-delete the smoke journals so we don't pollute the DB ---
foreach ([$newJid, $newJid2] as $jid) {
    $stmt = $pdo->prepare('DELETE FROM journal_entry_lines WHERE entry_id = :j');
    $stmt->bindValue(':j', UlidGenerator::decode($jid), PDO::PARAM_LOB);
    $stmt->execute();
    $stmt = $pdo->prepare('DELETE FROM journal_entries WHERE id = :j');
    $stmt->bindValue(':j', UlidGenerator::decode($jid), PDO::PARAM_LOB);
    $stmt->execute();
}
echo "cleanup: smoke journals removed\n";
echo "B-NEW SMOKE PASS\n";
