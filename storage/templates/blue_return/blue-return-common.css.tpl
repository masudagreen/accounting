{strip}
{if $hasJapaneseFont}@font-face { font-family: "ipaexg"; src: url("{$fontDir}/ipaexg.ttf") format("truetype"); font-weight: normal; font-style: normal; }{/if}
{if $hasJapaneseFont}@font-face { font-family: "ipaexg"; src: url("{$fontDir}/ipaexg.ttf") format("truetype"); font-weight: bold; font-style: normal; }{/if}
body, h1, h2, h3, h4, p, div, span, td, th, table, li, a { font-family: "{$defaultFont|default:'dejavu sans'}", sans-serif; }
body { color:#1a1a1a; font-size: 9pt; margin: 0; padding: 0; }
.page { page-break-after: always; padding: 18pt 22pt 22pt 22pt; }
.page:last-child { page-break-after: auto; }
h1 { font-size: 16pt; margin: 0 0 4pt 0; letter-spacing: 0.06em; border-bottom: 1pt solid #223a66; padding-bottom: 3pt; color: #223a66; }
h2 { font-size: 11pt; margin: 10pt 0 4pt 0; padding: 2pt 6pt; background: #e9effa; border-left: 4pt solid #3f5c90; color: #1a2a4a; }
h3 { font-size: 9.5pt; margin: 7pt 0 3pt 0; color: #3a3a3a; font-weight: bold; }
.meta { color:#4a4a4a; font-size: 8.3pt; margin: 0 0 6pt 0; }
.meta span { margin-right: 14pt; }
.status-badge { display: inline-block; padding: 1pt 7pt; border-radius: 2pt; font-size: 7.5pt; letter-spacing: 0.1em; font-weight: bold; }
.status-draft { background: #fdf3e4; color: #8b5a1a; border: 0.4pt solid #d4b58a; }
.status-finalized { background: #dfeee0; color: #25642a; border: 0.4pt solid #a3cca7; }
.form-type-badge { display: inline-block; padding: 1pt 7pt; font-size: 7.5pt; background:#eef4ff; color:#2a4578; border: 0.4pt solid #b3c5e1; margin-left: 6pt; }
.br-table { width: 100%; border-collapse: collapse; margin: 0 0 7pt 0; }
.br-table th, .br-table td { border: 0.3pt solid #999; padding: 2pt 5pt; vertical-align: top; font-size: 8.3pt; }
.br-table th { background: #f1f1f1; text-align: center; font-weight: bold; color: #222; }
.br-table td.amount { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
.br-table td.label { text-align: left; }
.br-table td.month { text-align: center; color: #555; width: 5em; }
.br-table tr.total-row td { background: #eef2fa; font-weight: bold; }
.br-table tr.net-row td { background: #fbf6e6; font-weight: bold; color: #7a4f17; }
.half-row { display: table; width: 100%; table-layout: fixed; }
.half-col { display: table-cell; width: 50%; padding-right: 4pt; vertical-align: top; }
.half-col + .half-col { padding-left: 4pt; padding-right: 0; }
.footer { color:#888; font-size: 7.5pt; margin-top: 10pt; border-top: 0.3pt solid #ccc; padding-top: 3pt; }
.empty { color: #999; font-style: italic; padding: 4pt 6pt; font-size: 8pt; }
.note { color:#999; font-size: 7.5pt; font-style: italic; margin-top: 3pt; }
{/strip}
