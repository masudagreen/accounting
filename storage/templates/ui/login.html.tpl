<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ログイン — Rucaro Accounting</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" crossorigin="anonymous">
  <style>
    body {
      font-family: system-ui, "Hiragino Sans", "Yu Gothic UI", "Meiryo", sans-serif;
      background: linear-gradient(160deg, #f7f7f5 0%, #e9edf2 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .rucaro-login-card {
      background: #fff;
      border-radius: 1rem;
      border: 1px solid #e5e7eb;
      box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
      padding: 2.5rem 2.25rem;
      width: min(420px, 95vw);
    }
    .rucaro-login-card h1 {
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 0.25rem;
    }
    .rucaro-login-card .subtitle {
      color: #6b7280;
      font-size: 0.9rem;
      margin-bottom: 1.75rem;
    }
  </style>
</head>
<body>
  <div class="rucaro-login-card">
    <h1><i class="bi bi-journal-bookmark-fill text-primary"></i> Rucaro Accounting</h1>
    <p class="subtitle">会計業務ポータルへログインしてください。</p>

    {if isset($flash_messages) && count($flash_messages) > 0}
      {foreach $flash_messages as $msg}
        {assign var="alert_class" value="alert-info"}
        {if $msg.kind == 'error'}{assign var="alert_class" value="alert-danger"}{/if}
        {if $msg.kind == 'success'}{assign var="alert_class" value="alert-success"}{/if}
        <div class="alert {$alert_class}" role="alert">{$msg.message|escape}</div>
      {/foreach}
    {/if}

    {if isset($form_errors) && count($form_errors) > 0}
      <div class="alert alert-danger" role="alert">
        <strong>ログインに失敗しました。</strong>
        <ul class="mb-0 ps-3">
          {foreach $form_errors as $field => $messages}
            {foreach $messages as $m}
              <li>{$m|escape}</li>
            {/foreach}
          {/foreach}
        </ul>
      </div>
    {/if}

    <form method="post" action="/ui/login" novalidate>
      <input type="hidden" name="_csrf" value="{$csrf_token|default:''|escape}">
      <div class="mb-3">
        <label for="email" class="form-label">メールアドレス</label>
        <input type="email" class="form-control" id="email" name="email"
               value="{$form_email|default:''|escape}" autocomplete="username" required autofocus>
      </div>
      <div class="mb-4">
        <label for="password" class="form-label">パスワード</label>
        <input type="password" class="form-control" id="password" name="password"
               autocomplete="current-password" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">
        <i class="bi bi-box-arrow-in-right"></i> ログイン
      </button>
    </form>

    <p class="text-center text-muted small mt-4 mb-0">Rucaro Accounting Web v1</p>
  </div>
</body>
</html>
