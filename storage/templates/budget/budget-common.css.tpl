{strip}
{if $hasJapaneseFont}@font-face { font-family: "ipaexg"; src: url("{$fontDir}/ipaexg.ttf") format("truetype"); font-weight: normal; font-style: normal; }{/if}
body, h1, h2, h3, h4, h5, p, div, span, td, th, table, li, a { font-family: "{$defaultFont|default:'dejavu sans'}", sans-serif; }
body { color:#222; font-size: 8.2pt; }
h1 { font-size: 14pt; margin: 0 0 4pt 0; letter-spacing: 0.04em; }
h2 { font-size: 10pt; margin: 6pt 0 3pt 0; padding: 2pt 5pt; background: #eef4ff; border-left: 4pt solid #4a6d9a; }
.meta { color:#555; font-size: 8pt; margin: 0 0 8pt 0; }
.meta span { margin-right: 12pt; }
.status-badge { display: inline-block; padding: 1pt 6pt; border-radius: 2pt; font-size: 7.5pt; letter-spacing: 0.08em; }
.status-draft { background: #f2eae1; color: #8b5a1a; border: 0.3pt solid #d4b58a; }
.status-approved { background: #e4f0e4; color: #2d6a2d; border: 0.3pt solid #a6ccaa; }
.status-locked { background: #e6e6ee; color: #40407a; border: 0.3pt solid #a2a2c2; }
.budget-summary { margin: 0 0 6pt 0; font-size: 8.5pt; color: #333; }
.budget-summary span { margin-right: 14pt; }
.budget-summary .label { color: #777; }
.budget-table { width: 100%; border-collapse: collapse; margin: 0 0 6pt 0; }
.budget-table th, .budget-table td { border: 0.3pt solid #bbb; padding: 1.5pt 3pt; vertical-align: top; font-size: 7.7pt; }
.budget-table th { background: #f3f3f3; text-align: center; font-weight: normal; }
.budget-table td.amount { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
.budget-table td.label { text-align: left; }
.budget-table td.code { text-align: left; color: #666; font-size: 7.4pt; }
.budget-table tr.total-row td { background: #e8eef7; font-weight: bold; }
.budget-table tr.over-budget td { background: #fdf0ee; }
.budget-table tr.under-budget td { background: #f3f8f1; }
.budget-table td.usage { text-align: right; font-variant-numeric: tabular-nums; }
.budget-table td.usage.over { color: #a23; font-weight: bold; }
.budget-table td.usage.under { color: #276; }
.footer { color:#888; font-size: 7.5pt; margin-top: 12pt; border-top: 0.3pt solid #ddd; padding-top: 3pt; }
.note { color:#999; font-size: 7.5pt; font-style: italic; margin-top: 4pt; }
{/strip}
