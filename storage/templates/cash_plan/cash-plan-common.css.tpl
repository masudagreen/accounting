{strip}
{if $hasJapaneseFont}@font-face { font-family: "ipaexg"; src: url("{$fontDir}/ipaexg.ttf") format("truetype"); font-weight: normal; font-style: normal; }{/if}
body, h1, h2, h3, h4, h5, p, div, span, td, th, table, li, a { font-family: "{$defaultFont|default:'dejavu sans'}", sans-serif; }
body { color:#222; font-size: 8.2pt; }
h1 { font-size: 14pt; margin: 0 0 4pt 0; letter-spacing: 0.04em; }
h2 { font-size: 10pt; margin: 6pt 0 3pt 0; padding: 2pt 5pt; background: #eef4ff; border-left: 4pt solid #4a6d9a; }
.meta { color:#555; font-size: 8pt; margin: 0 0 8pt 0; }
.meta span { margin-right: 12pt; }
.plan-summary { margin: 0 0 6pt 0; font-size: 8.5pt; color: #333; }
.plan-summary span { margin-right: 14pt; }
.plan-summary .label { color: #777; }
.plan-table { width: 100%; border-collapse: collapse; margin: 0 0 6pt 0; }
.plan-table th, .plan-table td { border: 0.3pt solid #bbb; padding: 1.5pt 3pt; vertical-align: top; font-size: 7.8pt; }
.plan-table th { background: #f3f3f3; text-align: center; font-weight: normal; }
.plan-table td.amount { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
.plan-table td.label { text-align: left; }
.plan-table tr.group-operating td { background: #fcfdff; }
.plan-table tr.group-investing td { background: #fffbf4; }
.plan-table tr.group-financing td { background: #f7fbf6; }
.plan-table tr.outflow td.label::before { content: '▲ '; color: #a44; }
.plan-table tr.total-row td { background: #e8eef7; font-weight: bold; }
.plan-table tr.closing-row td { background: #d9e5f3; font-weight: bold; }
.footer { color:#888; font-size: 7.5pt; margin-top: 12pt; border-top: 0.3pt solid #ddd; padding-top: 3pt; }
.note { color:#999; font-size: 7.5pt; font-style: italic; margin-top: 4pt; }
{/strip}
