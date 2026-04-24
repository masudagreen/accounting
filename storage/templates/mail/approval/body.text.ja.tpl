Rucaro Accounting - 承認依頼

以下の {$target.kind} について、承認または却下をお願いします。

概要: {$target.summary}
対象ID: {$target.id}
{if isset($target.details.journal_date)}日付: {$target.details.journal_date}
{/if}{if isset($target.details.total_amount)}金額: {$target.details.total_amount} {$target.details.currency_code|default:''}
{/if}

承認: {$approveUrl}
却下: {$rejectUrl}

期限: {$expiresAt}

--
このメールは Rucaro Accounting から自動送信されています。
