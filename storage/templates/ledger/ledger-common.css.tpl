{strip}
{if $hasJapaneseFont}@font-face { font-family: "ipaexg"; src: url("{$fontDir}/ipaexg.ttf") format("truetype"); font-weight: normal; font-style: normal; }{/if}
body, h1, h2, h3, h4, h5, p, div, span, td, th, table, li, a { font-family: "{$defaultFont|default:'dejavu sans'}", sans-serif; }
body { color:#222; font-size: 9pt; }
h1 { font-size: 15pt; margin: 0 0 4pt 0; letter-spacing: 0.04em; }
h2 { font-size: 10.5pt; margin: 12pt 0 4pt 0; padding: 2pt 6pt; background: #eef2f6; border-left: 3pt solid #3a5; }
.meta { color:#555; font-size: 8.5pt; margin: 0 0 10pt 0; }
.meta span { margin-right: 12pt; }
.ledger-table { width: 100%; border-collapse: collapse; margin: 0 0 6pt 0; }
.ledger-table th, .ledger-table td { border: 0.4pt solid #bbb; padding: 2.5pt 5pt; vertical-align: top; }
.ledger-table th { background: #f3f3f3; text-align: center; font-weight: normal; font-size: 8.5pt; }
.ledger-table td.amount { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
.ledger-table td.date { white-space: nowrap; width: 58pt; }
.ledger-table td.counter { width: 110pt; }
.ledger-table tr.opening td, .ledger-table tr.closing td { background: #fafafa; font-weight: bold; }
.ledger-table tr.closing td { border-top: 1pt double #444; }
.ledger-book { page-break-inside: avoid; margin-bottom: 12pt; }
.ledger-book + .ledger-book { page-break-before: always; }
.empty-book { color: #888; font-style: italic; font-size: 9pt; margin: 4pt 0 8pt 2pt; }
.footer { color:#888; font-size: 8pt; margin-top: 18pt; border-top: 0.3pt solid #ddd; padding-top: 4pt; }
.note { color:#999; font-size: 8pt; font-style: italic; margin-top: 6pt; }
{/strip}
