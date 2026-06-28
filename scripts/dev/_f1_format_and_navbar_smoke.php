<?php

declare(strict_types=1);

/**
 * F-1 smoke: confirm the journal UI no longer emits `.0000` padded amounts
 * and that the navbar's fiscal-term selector renders as a `<select>` with
 * "第 N 期" labels.
 *
 * Steps:
 *   1. GET /ui/journals — body must NOT contain `.0000`; totalAmount column
 *      shows thousands-separated integers.
 *   2. GET /ui/journals/{id} — show payload: lines render integer amounts,
 *      tax rate / tax amount have no fractional tail.
 *   3. GET /ui/journals/new — render. recent_journals JSON must not contain
 *      `.0000`. Navbar block contains <select id="nav-fiscal-select"> with
 *      "第 N 期" option text.
 *   4. GET /ui/dashboard — navbar block contains the same <select>.
 *
 *   docker exec accounting_renewal_app php /var/www/html/scripts/dev/_f1_format_and_navbar_smoke.php
 */

use Rucaro\Http\Controller\Ui\DashboardController;
use Rucaro\Http\Controller\Ui\Journal\JournalListController;
use Rucaro\Http\Controller\Ui\Journal\JournalNewController;
use Rucaro\Http\Controller\Ui\Journal\JournalShowController;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Container\ContainerBootstrap;
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

// --- bench fixtures ---
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

$stmt = $pdo->prepare("SELECT id FROM users WHERE login_id = 'admin' LIMIT 1");
$stmt->execute();
$userRow = $stmt->fetch(PDO::FETCH_ASSOC);
if ($userRow === false) { fail('admin user not found', 2); }
$userId = UlidGenerator::encode($userRow['id']);

// --- container + controllers ---
$container = ContainerBootstrap::build($pdo);
$listCtrl  = $container->get(JournalListController::class);
$newCtrl   = $container->get(JournalNewController::class);
$showCtrl  = $container->get(JournalShowController::class);
$dashCtrl  = $container->get(DashboardController::class);
foreach ([
    'list' => $listCtrl,
    'new'  => $newCtrl,
    'show' => $showCtrl,
    'dash' => $dashCtrl,
] as $k => $v) {
    if ($v === null) { fail("controller {$k} not wired", 3); }
}

$session = $container->get(SessionStore::class);
$_SESSION = [];
$_SESSION[SessionStore::KEY_USER_ID] = $userId;
$_SESSION[SessionStore::KEY_TOKEN] = 'smoke-token';
$_SESSION[SessionStore::KEY_DISPLAY_NAME] = '管理者';
$_SESSION[SessionStore::KEY_EMAIL] = 'masudagreen@gmail.com';
$session->setSelectedEntity($entityId);
$session->setSelectedFiscalTerm($fiscalTermId);

/**
 * Return all `.0000`-padded substrings found in $body (small report to help
 * debugging). Filters out occurrences inside <script> blocks ONLY if the
 * caller passes ignoreScripts=true.
 */
function findDot0000(string $body): array
{
    $hits = [];
    if (preg_match_all('/[0-9](?:\.[0-9]{4})/', $body, $m, PREG_OFFSET_CAPTURE)) {
        foreach ($m[0] as [$frag, $off]) {
            $start = max(0, $off - 40);
            $hits[] = substr($body, $start, 80);
        }
    }
    return array_slice($hits, 0, 5);
}

// --- step 1: GET /ui/journals ---
$req = new ServerRequest('GET', '/ui/journals', [], [], null, '');
$resp = $listCtrl->invoke($req);
if ($resp->status !== 200) { fail('GET /ui/journals status=' . $resp->status, 4); }
$listBody = $resp->body;
// Look for ".0000" inside the table body (we extract just the <tbody>...</tbody> for the journal table).
if (preg_match('#<tbody>(.*?)</tbody>#s', $listBody, $tb)) {
    if (str_contains($tb[1], '.0000')) {
        $hits = findDot0000($tb[1]);
        fail("list: tbody still contains .0000. Examples: " . implode(' | ', $hits), 5);
    }
}
// Confirm at least one thousands-separated amount appears
if (!preg_match('/<td class="text-end">[0-9]{1,3}(,[0-9]{3})+<\/td>/', $listBody)) {
    fail('list: no thousands-separated amount cells found (table may be empty?)', 5);
}
echo "step1: /ui/journals OK (no .0000 in tbody, thousands separators detected)\n";

// --- step 2: pick one journal from the list and GET /ui/journals/{id} ---
preg_match('#/ui/journals/([0-9A-HJKMNP-TV-Z]{26})#', $listBody, $m);
$pickJid = $m[1] ?? '';
if ($pickJid === '') { fail('list: no journal id href found', 6); }
$req = new ServerRequest('GET', '/ui/journals/' . $pickJid, [], [], null, '');
$resp = $showCtrl->invoke($req, $pickJid);
if ($resp->status !== 200) { fail('GET /ui/journals/{id} status=' . $resp->status, 6); }
$showBody = $resp->body;
// Strip the form datalist + JSON blocks (which can legitimately contain
// fractional decimals such as `8.00` if there are reduced-tax lines), then
// scan the human-visible region for `.0000`.
$visibleShow = preg_replace('#<script[^>]*>.*?</script>#s', '', $showBody) ?? $showBody;
$visibleShow = preg_replace('#<datalist[^>]*>.*?</datalist>#s', '', $visibleShow) ?? $visibleShow;
// form.html.tpl uses value="…" so any persisted line amount/tax_amount would
// land in the visible region; assert no .0000 survives.
if (str_contains($visibleShow, '.0000')) {
    $hits = findDot0000($visibleShow);
    fail("show: .0000 still in visible body. Examples: " . implode(' | ', $hits), 7);
}
echo "step2: /ui/journals/{$pickJid} OK (no .0000 in visible body)\n";

// --- step 3: GET /ui/journals/new — recent_journals JSON + navbar markers ---
$resp = $newCtrl->show(new ServerRequest('GET', '/ui/journals/new', [], [], null, ''));
if ($resp->status !== 200) { fail('GET /ui/journals/new status=' . $resp->status, 8); }
$newBody = $resp->body;
// recent_journals JSON: extract the <script id="recent-journals-data">...</script> block.
if (preg_match('#<script type="application/json" id="recent-journals-data">(.*?)</script>#s', $newBody, $rj)) {
    $rjJson = $rj[1];
    if (str_contains($rjJson, '.0000')) {
        $hits = findDot0000($rjJson);
        fail('new: recent_journals JSON still contains .0000. Examples: ' . implode(' | ', $hits), 9);
    }
    // Confirm at least one numeric `amount` key with no fractional tail
    if ($rjJson !== '[]' && !preg_match('/"amount":"[0-9,]+"/', $rjJson)) {
        fail('new: recent_journals JSON has no integer amount field shape', 9);
    }
} else {
    fail('new: recent_journals JSON script block not found', 9);
}
// Navbar markers
$mustContainNav = [
    'id="nav-fiscal-select"',
    'name="fiscal_term_id"',
    '第 ',                   // 期 label prefix
];
foreach ($mustContainNav as $needle) {
    if (!str_contains($newBody, $needle)) {
        fail("new: navbar missing marker: {$needle}", 10);
    }
}
// Old free-text input must be gone
if (str_contains($newBody, 'id="nav-fiscal-input"')) {
    fail('new: navbar still has old <input id="nav-fiscal-input">', 10);
}
echo "step3: /ui/journals/new OK (recent JSON clean, navbar shows <select> with 第 N 期 label)\n";

// --- step 4: GET /ui/dashboard — same navbar markers ---
$resp = ($dashCtrl)(new ServerRequest('GET', '/ui/dashboard', [], [], null, ''));
if ($resp->status !== 200) { fail('GET /ui/dashboard status=' . $resp->status, 11); }
$dashBody = $resp->body;
foreach ($mustContainNav as $needle) {
    if (!str_contains($dashBody, $needle)) {
        fail("dashboard: navbar missing marker: {$needle}", 12);
    }
}
if (str_contains($dashBody, 'id="nav-fiscal-input"')) {
    fail('dashboard: navbar still has old free-text fiscal input', 12);
}
// dashboard's recent_journals card — confirm totals are formatted ints, no .0000.
if (preg_match('#<table[^>]*>(.*?)</table>#s', $dashBody, $tb) && str_contains($tb[1], '.0000')) {
    $hits = findDot0000($tb[1]);
    fail('dashboard: recent journals table still contains .0000: ' . implode(' | ', $hits), 13);
}
echo "step4: /ui/dashboard OK (navbar shows <select> with 第 N 期 label)\n";

// --- step 5: Reports (TB / Ledger / PL / BS / CS) — confirm navbar select. ---
$reports = [
    'TrialBalance' => ['path' => '/ui/trial-balance', 'class' => Rucaro\Http\Controller\Ui\Report\TrialBalanceViewController::class],
    'Ledger'       => ['path' => '/ui/ledger',        'class' => Rucaro\Http\Controller\Ui\Report\LedgerViewController::class],
    'PL'           => ['path' => '/ui/pl',            'class' => Rucaro\Http\Controller\Ui\Report\PlViewController::class],
    'BS'           => ['path' => '/ui/bs',            'class' => Rucaro\Http\Controller\Ui\Report\BsViewController::class],
    'CS'           => ['path' => '/ui/cs',            'class' => Rucaro\Http\Controller\Ui\Report\CsViewController::class],
];
foreach ($reports as $label => $cfg) {
    $reportCtrl = $container->get($cfg['class']);
    if ($reportCtrl === null) { fail("report controller {$label} not wired", 14); }
    $req = new ServerRequest('GET', $cfg['path'], [], [], null, '');
    $resp = $reportCtrl->__invoke($req);
    if ($resp->status !== 200) {
        fail("GET {$cfg['path']} status=" . $resp->status, 15);
    }
    $body = $resp->body;
    if (!str_contains($body, 'id="nav-fiscal-select"')) {
        fail("{$label}: navbar missing <select id=\"nav-fiscal-select\">", 16);
    }
    if (!str_contains($body, '第 ')) {
        fail("{$label}: navbar missing '第 N 期' label", 16);
    }
}
echo "step5: TB/Ledger/PL/BS/CS navbars OK (all show fiscal_term <select>)\n";

echo "F-1 SMOKE PASS\n";
