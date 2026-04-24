{strip}
{if $hasJapaneseFont}@font-face { font-family: "ipaexg"; src: url("{$fontDir}/ipaexg.ttf") format("truetype"); font-weight: normal; font-style: normal; }{/if}
body, h1, h2, h3, h4, h5, p, div, span, td, th, table, li, a { font-family: "{$defaultFont|default:'dejavu sans'}", sans-serif; }
body { color:#222; font-size: 9pt; }
h1 { font-size: 15pt; margin: 0 0 4pt 0; letter-spacing: 0.04em; }
h2 { font-size: 11pt; margin: 10pt 0 4pt 0; padding: 3pt 6pt; background: #efe9f6; border-left: 4pt solid #6b4d8a; }
.meta { color:#555; font-size: 8.5pt; margin: 0 0 10pt 0; }
.meta span { margin-right: 12pt; }
.summary-grid { width: 100%; border-collapse: collapse; margin: 0 0 10pt 0; }
.summary-grid th, .summary-grid td { border: 0.4pt solid #bbb; padding: 3pt 7pt; vertical-align: middle; }
.summary-grid th { background: #f3f3f3; text-align: left; font-weight: normal; width: 35%; }
.summary-grid td { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
.summary-grid tr.highlight th { background: #efe9f6; font-weight: bold; }
.summary-grid tr.highlight td { background: #f7f3fb; font-weight: bold; }
.breakdown-table { width: 100%; border-collapse: collapse; margin: 0 0 10pt 0; }
.breakdown-table th, .breakdown-table td { border: 0.3pt solid #bbb; padding: 2pt 5pt; font-size: 8.5pt; }
.breakdown-table th { background: #f3f3f3; font-weight: normal; text-align: center; }
.breakdown-table td.amount { text-align: right; font-variant-numeric: tabular-nums; }
.breakdown-table td.code { white-space: nowrap; }
.status { padding: 4pt 8pt; margin: 0 0 8pt 0; font-size: 9pt; border-radius: 2pt; }
.status.below { background: #fde8e8; border-left: 4pt solid #c44; color: #933; }
.status.above { background: #e8f7ec; border-left: 4pt solid #4c9; color: #275; }
.footer { color:#888; font-size: 8pt; margin-top: 18pt; border-top: 0.3pt solid #ddd; padding-top: 4pt; }
.note { color:#999; font-size: 8pt; font-style: italic; margin-top: 6pt; }
{/strip}
