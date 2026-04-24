{strip}
{if $hasJapaneseFont}@font-face { font-family: "ipaexg"; src: url("{$fontDir}/ipaexg.ttf") format("truetype"); font-weight: normal; font-style: normal; }{/if}
body, h1, h2, h3, h4, h5, p, div, span, td, th, table, li, a { font-family: "{$defaultFont|default:'dejavu sans'}", sans-serif; }
body { color:#1b1b1b; font-size: 9pt; line-height: 1.45; }
h1 { font-size: 15pt; margin: 0 0 4pt 0; letter-spacing: 0.05em; }
h2 { font-size: 11pt; margin: 10pt 0 4pt 0; padding: 3pt 6pt; background: #eef2f8; border-left: 4pt solid #36537f; }
.meta { color:#555; font-size: 8.5pt; margin: 0 0 10pt 0; }
.meta span { margin-right: 14pt; }
.note-item { page-break-inside: avoid; margin: 0 0 7pt 0; padding: 3pt 0 4pt 0; border-bottom: 0.3pt dotted #cfcfcf; }
.note-item:last-child { border-bottom: 0; }
.note-label { font-weight: bold; color: #243248; margin: 0 0 2pt 0; font-size: 9.3pt; }
.note-label .tpl { color:#96a; font-weight: normal; font-size: 7.6pt; margin-left: 4pt; }
.note-body { white-space: pre-wrap; color:#222; }
.empty { color:#888; font-style: italic; margin: 10pt 0; }
.footer { color:#888; font-size: 7.5pt; margin-top: 14pt; border-top: 0.3pt solid #ddd; padding-top: 3pt; }
.notice { color:#b0651e; font-size: 7.8pt; font-style: italic; margin-top: 4pt; }
{/strip}
