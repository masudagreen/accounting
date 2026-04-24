{strip}
{if $hasJapaneseFont}@font-face { font-family: "ipaexg"; src: url("{$fontDir}/ipaexg.ttf") format("truetype"); font-weight: normal; font-style: normal; }{/if}
body, h1, h2, h3, h4, p, div, span, td, th, table, li, a { font-family: "{$defaultFont|default:'dejavu sans'}", sans-serif; }
body { color:#222; font-size: 9pt; }
h1 { font-size: 15pt; margin: 0 0 4pt 0; letter-spacing: 0.04em; }
h2 { font-size: 11pt; margin: 8pt 0 4pt 0; padding: 2pt 5pt; background: #eef4ff; border-left: 4pt solid #4a6d9a; }
.meta { color:#555; font-size: 8pt; margin: 0 0 8pt 0; }
.meta span { margin-right: 12pt; }
.section-table { width: 100%; border-collapse: collapse; margin: 0 0 6pt 0; }
.section-table th, .section-table td { border: 0.3pt solid #bbb; padding: 2.5pt 5pt; vertical-align: top; font-size: 8.5pt; }
.section-table th { background: #f3f3f3; text-align: center; font-weight: normal; }
.section-table td.amount { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
.section-table td.label { text-align: left; }
.summary-table { width: 100%; border-collapse: collapse; margin: 6pt 0 10pt 0; }
.summary-table th, .summary-table td { border: 0.3pt solid #bbb; padding: 2.5pt 5pt; font-size: 8.5pt; }
.summary-table th { background: #f3f3f3; text-align: left; font-weight: normal; width: 40%; }
.summary-table td.amount { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
.summary-table tr.highlight td, .summary-table tr.highlight th { background: #dde8f5; font-weight: bold; }
.footer { color:#888; font-size: 7.5pt; margin-top: 16pt; border-top: 0.3pt solid #ddd; padding-top: 3pt; }
.note { color:#999; font-size: 7.5pt; font-style: italic; margin-top: 4pt; }
{/strip}
