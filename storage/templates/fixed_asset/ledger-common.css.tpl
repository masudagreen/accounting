{strip}
{if $hasJapaneseFont}@font-face { font-family: "ipaexg"; src: url("{$fontDir}/ipaexg.ttf") format("truetype"); font-weight: normal; font-style: normal; }{/if}
body, h1, h2, h3, h4, h5, p, div, span, td, th, table, li, a { font-family: "{$defaultFont|default:'dejavu sans'}", sans-serif; }
body { color:#222; font-size: 9pt; }
h1 { font-size: 15pt; margin: 0 0 4pt 0; letter-spacing: 0.04em; }
h2 { font-size: 11pt; margin: 10pt 0 4pt 0; padding: 3pt 6pt; background: #f4f0e7; border-left: 4pt solid #a86; }
.meta { color:#555; font-size: 8.5pt; margin: 0 0 10pt 0; }
.meta span { margin-right: 12pt; }
.asset-summary { margin: 0 0 6pt 0; font-size: 9pt; color: #333; }
.asset-summary span { margin-right: 14pt; }
.asset-summary .label { color: #777; }
.schedule-table { width: 100%; border-collapse: collapse; margin: 0 0 6pt 0; }
.schedule-table th, .schedule-table td { border: 0.4pt solid #bbb; padding: 2.5pt 5pt; vertical-align: top; }
.schedule-table th { background: #f3f3f3; text-align: center; font-weight: normal; font-size: 8.5pt; }
.schedule-table td.amount { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
.schedule-table td.period, .schedule-table td.date { white-space: nowrap; text-align: center; }
.schedule-table tr.posted td { background: #f4fdf4; }
.asset-book { page-break-inside: avoid; margin-bottom: 14pt; }
.asset-book + .asset-book { page-break-before: always; }
.empty-book { color: #888; font-style: italic; font-size: 9pt; margin: 4pt 0 8pt 2pt; }
.footer { color:#888; font-size: 8pt; margin-top: 18pt; border-top: 0.3pt solid #ddd; padding-top: 4pt; }
.note { color:#999; font-size: 8pt; font-style: italic; margin-top: 6pt; }
{/strip}
