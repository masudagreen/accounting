{strip}
{if $hasJapaneseFont}@font-face { font-family: "ipaexg"; src: url("{$fontDir}/ipaexg.ttf") format("truetype"); font-weight: normal; font-style: normal; }{/if}
body, h1, h2, h3, h4, h5, p, div, span, td, th, table, li, a { font-family: "{$defaultFont|default:'dejavu sans'}", sans-serif; }
body { color:#1d1d1f; font-size: 8.5pt; }
h1 { font-size: 14pt; margin: 0 0 4pt 0; letter-spacing: 0.04em; }
h2 { font-size: 10pt; margin: 6pt 0 3pt 0; padding: 2pt 5pt; background: #eef4ff; border-left: 4pt solid #4a6d9a; }
.meta { color:#555; font-size: 8pt; margin: 0 0 8pt 0; }
.meta span { margin-right: 12pt; }
.ss-table { width: 100%; border-collapse: collapse; margin: 0 0 6pt 0; }
.ss-table th, .ss-table td {
  border: 0.3pt solid #999;
  padding: 2pt 4pt;
  vertical-align: middle;
  font-size: 7.8pt;
}
.ss-table thead th {
  background: #eaeef5;
  font-weight: bold;
  text-align: center;
}
.ss-table thead th.section { letter-spacing: 0.02em; }
.ss-table td.label {
  background: #f7f7f9;
  font-weight: bold;
  text-align: left;
  white-space: nowrap;
}
.ss-table td.amount {
  text-align: right;
  font-variant-numeric: tabular-nums;
  white-space: nowrap;
}
.ss-table tr.opening-row td,
.ss-table tr.ending-row td {
  background: #f1f6ec;
  font-weight: bold;
}
.ss-table tr.total-change-row td {
  background: #eef2f8;
  font-weight: bold;
  border-top: 0.6pt solid #333;
}
.ss-table tr.change-row td.amount.source-journal_auto {
  color: #2d6a2d;
}
.ss-table tr.change-row td.amount.source-manual {
  color: #1d1d1f;
}
.ss-table td.total-col {
  background: #fff8e6;
  font-weight: bold;
}
.footer { color:#888; font-size: 7.5pt; margin-top: 12pt; border-top: 0.3pt solid #ddd; padding-top: 3pt; }
.note { color:#999; font-size: 7.5pt; font-style: italic; margin-top: 4pt; }
.legend { color: #555; font-size: 7.2pt; margin-top: 4pt; }
.legend span { margin-right: 10pt; }
.legend .auto-marker { color: #2d6a2d; font-weight: bold; }
{/strip}
