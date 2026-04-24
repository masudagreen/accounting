{strip}
{if $hasJapaneseFont}@font-face { font-family: "ipaexg"; src: url("{$fontDir}/ipaexg.ttf") format("truetype"); font-weight: normal; font-style: normal; }{/if}
body, h1, h2, h3, h4, h5, p, div, span, td, th, table, li, a { font-family: "{$defaultFont|default:'dejavu sans'}", sans-serif; }
@page { size: A4 landscape; margin: 12mm 10mm; }
body { color:#222; font-size: 9.5pt; }
h1 { font-size: 15pt; margin: 0 0 4pt 0; letter-spacing: 0.04em; }
h2 { font-size: 11pt; margin: 10pt 0 4pt 0; padding-bottom: 2pt; border-bottom: 0.6pt solid #444; }
.meta { color:#555; font-size: 8.5pt; margin: 0 0 6pt 0; }
.meta span { margin-right: 10pt; }
.multi-table { width: 100%; border-collapse: collapse; margin: 0 0 8pt 0; table-layout: fixed; }
.multi-table th, .multi-table td { border: 0.3pt solid #bbb; padding: 2.5pt 5pt; vertical-align: top; }
.multi-table th { background: #f3f3f3; text-align: left; font-weight: normal; font-size: 9pt; }
.multi-table th.amount, .multi-table td.amount { text-align: right; font-variant-numeric: tabular-nums; }
.multi-table th.variance, .multi-table td.variance { background: #fafafa; }
.multi-table th.label, .multi-table td.label { width: 28%; }
.multi-table tr.subtotal td { background: #fafafa; font-weight: bold; border-top: 0.6pt solid #444; }
.multi-table tr.total td { background: #eee; font-weight: bold; border-top: 1pt double #222; border-bottom: 1pt double #222; }
.multi-table td.neg { color: #a00; }
.footer { color:#888; font-size: 8pt; margin-top: 10pt; border-top: 0.3pt solid #ddd; padding-top: 3pt; }
.note { color:#999; font-size: 7.5pt; font-style: italic; margin-top: 4pt; }
{/strip}
