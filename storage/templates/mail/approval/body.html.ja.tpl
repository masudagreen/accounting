<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>Rucaro Accounting - 承認依頼</title>
</head>
<body style="font-family: -apple-system, 'Hiragino Sans', 'Noto Sans JP', sans-serif; color:#222;">
<div style="max-width:560px; margin:0 auto; padding:24px;">
  <h1 style="font-size:18px; margin:0 0 12px;">Rucaro Accounting - 承認依頼</h1>
  <p>以下の {$target.kind|escape} について、承認または却下をお願いします。</p>
  <table style="width:100%; border-collapse:collapse; margin:16px 0;">
    <tr><th style="text-align:left; padding:6px; border-bottom:1px solid #ddd;">概要</th><td style="padding:6px; border-bottom:1px solid #ddd;">{$target.summary|escape}</td></tr>
    <tr><th style="text-align:left; padding:6px; border-bottom:1px solid #ddd;">対象ID</th><td style="padding:6px; border-bottom:1px solid #ddd;"><code>{$target.id|escape}</code></td></tr>
    {if isset($target.details.journal_date)}<tr><th style="text-align:left; padding:6px; border-bottom:1px solid #ddd;">日付</th><td style="padding:6px; border-bottom:1px solid #ddd;">{$target.details.journal_date|escape}</td></tr>{/if}
    {if isset($target.details.total_amount)}<tr><th style="text-align:left; padding:6px; border-bottom:1px solid #ddd;">金額</th><td style="padding:6px; border-bottom:1px solid #ddd;">{$target.details.total_amount|escape} {$target.details.currency_code|default:''|escape}</td></tr>{/if}
  </table>
  <p style="margin:24px 0;">
    <a href="{$approveUrl|escape}" style="display:inline-block; background:#2a7; color:#fff; text-decoration:none; padding:10px 16px; border-radius:4px; margin-right:8px;">承認する</a>
    <a href="{$rejectUrl|escape}" style="display:inline-block; background:#c33; color:#fff; text-decoration:none; padding:10px 16px; border-radius:4px;">却下する</a>
  </p>
  <p style="color:#666; font-size:12px;">期限: {$expiresAt|escape}</p>
  <hr style="border:none; border-top:1px solid #eee; margin:24px 0;">
  <p style="color:#999; font-size:11px;">このメールは Rucaro Accounting から自動送信されています。</p>
</div>
</body>
</html>
