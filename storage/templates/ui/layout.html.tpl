<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{if isset($page_title)}{$page_title|escape} — {/if}Rucaro Accounting</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" crossorigin="anonymous">
  <style>
    :root {
      --rucaro-surface: #f7f7f5;
      --rucaro-accent: #2b6cb0;
      --rucaro-accent-strong: #1e4c82;
      --rucaro-ink: #1b1d1f;
      --rucaro-muted: #6b7280;
    }
    body {
      font-family: system-ui, "Hiragino Sans", "Yu Gothic UI", "Meiryo", sans-serif;
      background: var(--rucaro-surface);
      color: var(--rucaro-ink);
      min-height: 100vh;
    }
    .rucaro-brand {
      font-weight: 700;
      letter-spacing: 0.04em;
    }
    .rucaro-shell {
      display: grid;
      grid-template-columns: 240px 1fr;
      gap: 0;
      min-height: calc(100vh - 64px);
    }
    @media (max-width: 768px) {
      .rucaro-shell { grid-template-columns: 1fr; }
      .rucaro-sidebar { border-right: none; border-bottom: 1px solid #e5e7eb; }
    }
    .rucaro-sidebar {
      background: #fff;
      border-right: 1px solid #e5e7eb;
      padding: 1.5rem 1rem;
    }
    .rucaro-sidebar .nav-link {
      color: var(--rucaro-ink);
      border-radius: 0.5rem;
      padding: 0.5rem 0.75rem;
    }
    .rucaro-sidebar .nav-link.active,
    .rucaro-sidebar .nav-link:hover {
      background: #eef2f7;
      color: var(--rucaro-accent-strong);
    }
    .rucaro-main { padding: 2rem 2rem 4rem; }
    .rucaro-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 0.75rem; }
    footer.rucaro-footer {
      font-size: 0.85rem;
      color: var(--rucaro-muted);
      text-align: center;
      padding: 1rem 0 2rem;
    }
    .rucaro-kpi {
      display: flex; flex-direction: column; gap: 0.25rem;
      padding: 1rem 1.25rem;
    }
    .rucaro-kpi .label { color: var(--rucaro-muted); font-size: 0.85rem; }
    .rucaro-kpi .value { font-size: 1.1rem; font-weight: 600; }
  </style>
  <meta name="csrf-logout-token" content="{if isset($csrf_logout_token)}{$csrf_logout_token|escape}{/if}">
</head>
<body>
  {include file="_components/navbar.tpl"}
  <div class="rucaro-shell">
    {include file="_components/sidebar.tpl"}
    <main class="rucaro-main">
      {include file="_components/flash.tpl"}
      {block name="content"}{/block}
    </main>
  </div>
  <footer class="rucaro-footer">
    Rucaro Accounting Web v1 — Phase 7-1 Rev.1
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
