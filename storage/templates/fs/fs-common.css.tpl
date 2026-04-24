{strip}
{if $hasJapaneseFont}@font-face { font-family: "ipaexg"; src: url("{$fontDir}/ipaexg.ttf") format("truetype"); font-weight: normal; font-style: normal; }{/if}
body, h1, h2, h3, h4, h5, p, div, span, td, th, table, li, a { font-family: "{$defaultFont|default:'dejavu sans'}", sans-serif; }
body { color:#222; font-size: 10.5pt; }
h1 { font-size: 16pt; margin: 0 0 4pt 0; letter-spacing: 0.04em; }
h2 { font-size: 11pt; margin: 14pt 0 4pt 0; padding-bottom: 2pt; border-bottom: 0.6pt solid #444; }
h3 { font-size: 10pt; margin: 8pt 0 3pt 0; color: #333; font-weight: bold; }
.meta { color:#555; font-size: 9pt; margin: 0 0 10pt 0; }
.meta span { margin-right: 12pt; }
.fs-table { width: 100%; border-collapse: collapse; margin: 0 0 6pt 0; }
.fs-table th, .fs-table td { border: 0.4pt solid #bbb; padding: 3pt 6pt; }
.fs-table th { background: #f3f3f3; text-align: left; font-weight: normal; }
.fs-table td.amount { text-align: right; font-variant-numeric: tabular-nums; }
.fs-table tr.subtotal td { background: #fafafa; font-weight: bold; border-top: 0.6pt solid #444; }
.fs-table tr.total td { background: #eee; font-weight: bold; border-top: 1.2pt double #222; border-bottom: 1.2pt double #222; }
.fs-table tr.indent-1 td:first-child { padding-left: 14pt; }
.fs-table tr.indent-2 td:first-child { padding-left: 24pt; }
.bs-grid { width: 100%; border-collapse: collapse; }
.bs-grid > tbody > tr > td { vertical-align: top; width: 50%; padding: 0 8pt; }
.bs-grid h2 { margin-top: 0; }
.totals { margin-top: 10pt; border-top: 0.6pt solid #444; padding-top: 6pt; }
.totals .label { color:#555; }
.totals .value { font-weight: bold; margin-left: 6pt; }
.footer { color:#888; font-size: 8pt; margin-top: 18pt; border-top: 0.3pt solid #ddd; padding-top: 4pt; }
.note { color:#999; font-size: 8pt; font-style: italic; margin-top: 6pt; }
.fs-amount-neg { color: #a00; }
{/strip}
