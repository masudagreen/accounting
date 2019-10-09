<?php

$vars = array(
	'arrSelectTag' => array(
		array('strTitle' => '未選択','value' => '',),
		array('strTitle' => '銀行','value' => 'dummy','flagDisabled' => 1, ),
		array('strTitle' => '住信SBIネット銀行','value' => 'sumisinnetbank', ),
		array('strTitle' => 'スルガ銀行','value' => 'surugabank', ),
		array('strTitle' => 'ジャパンネット銀行','value' => 'japannetbank', ),
		array('strTitle' => 'じぶん銀行','value' => 'jibunbank', ),
		array('strTitle' => 'ゆうちょ銀行','value' => 'japanpostbank', ),
	),
	'varsStr' => array(
		'strTitle' => '摘要',
		'stampBook' => '日付',
		'numValueIn' => '入金',
		'numValueOut' => '出金',
	),
	'varsComment' => array(
		'strStatus' => '【 <%replace%>行 】',
		'strStatusLog' => '
			<table cellspacing="1" cellpadding="3" border="0" bgcolor="#222" width="100%">
				<tbody>
					<tr valign="middle">
						<td class="codePluginAccountingLibTableColumnMiddle" >仕訳帳書出結果<br>#{strTitle}</td>
					</tr>
					<tr valign="top">
						<td class="codePluginAccountingLibTableRowStr">
							読込総数 #{replaceAll}件<br>
							フィルタ一致数 #{replaceAllImport}件<br>
							フィルタ不一致数 #{replaceAllNone}件<br>
							エラー数 #{replaceAllError}件<br>
						</td>
					</tr>
					<tr valign="top">
						<td class="codePluginAccountingLibTableRowStr">
							フィルタ一致 口座管理ログ通番<br>
							<span class="codeLibBaseFontSizeSeventy">※ 通番の前に『※』があるデータは収支管理の留保ログにあります。</span><br>
							#{replaceImport}
						</td>
					</tr>
					<tr valign="top">
						<td class="codePluginAccountingLibTableRowStr">
							フィルタ不一致 口座管理ログ通番<br>
							#{replaceNone}
						</td>
					</tr>
					<tr valign="top">
						<td class="codePluginAccountingLibTableRowStr">
							エラー原因<br>
							#{replaceError}
						</td>
					</tr>
				</tbody>
			</table>
		',
		'strStatusBanks' => '
			<table cellspacing="1" cellpadding="3" border="0" bgcolor="#222" width="100%">
				<tbody>
					<tr valign="middle">
						<td class="codePluginAccountingLibTableColumnMiddle">明細インポート結果<br>(#{strTitle})</td>
					</tr>
					<tr valign="top">
						<td class="codePluginAccountingLibTableRowStr">
							読込総数 #{replaceAll}件<br>
							インポート数 #{replaceAllImport}件<br>
							見送り数 #{replaceAllPass}件<br>
							エラー数 #{replaceAllError}件<br>
						</td>
					</tr>
					<tr valign="top">
						<td class="codePluginAccountingLibTableRowStr">
							インポート<br>
							#{replaceImport}
						</td>
					</tr>
					<tr valign="top">
						<td class="codePluginAccountingLibTableRowStr">
							見送り<br>
							#{replacePass}
						</td>
					</tr>
					<tr valign="top">
						<td class="codePluginAccountingLibTableRowStr">
							エラー原因<br>
							#{replaceError}
						</td>
					</tr>
				</tbody>
			</table>
		',
		'strConvertError' => '
			<table cellspacing="1" cellpadding="3" border="0" bgcolor="#222" width="100%">
				<tbody>
					<tr valign="middle">
						<td class="codePluginAccountingLibTableColumnMiddle" >エラー発生(#{strTitle})</td>
					</tr>
					<tr valign="top">
						<td class="codePluginAccountingLibTableRowStr">
							読み込んだデータに文字化け又は未知のデータがあったため処理が見送られたようです。
							<br>
							※Linuxコマンドを使用しているためOSは推奨環境であるLinuxを使用してください。
							<br>
							※インポートデータをあらかじめUTF-8にしておくと文字化けリスクを低減できます。
						</td>
					</tr>
				</tbody>
			</table>
		',
		'strWebError' => '
			<table cellspacing="1" cellpadding="3" border="0" bgcolor="#222" width="100%">
				<tbody>
					<tr valign="middle">
						<td class="codePluginAccountingLibTableColumnMiddle" >インポート見送り(#{strTitle})</td>
					</tr>
					<tr valign="top">
						<td class="codePluginAccountingLibTableRowStr">#{strReason}</td>
					</tr>
				</tbody>
			</table>
		',
		'strTitleTag' => '口座管理',
		'strId' => '通番',
		'strStatusRow' => '【 <%replace%>行 】',
		'strStatusRowCash' => '【 ※<%replace%>行 】',
		'strStatusRowBanks' => '【 <%replace%> 】',
		'strStatusRowCashBanks' => '【 ※<%replace%> 】',
		'strStatusRowPass' => '<%replace%><br>',
		'strStatusRowError' => '<%replace%><br>',
		'strUploadError' => 'アップロードに失敗しました。',
		'strFileType' => 'インポート対応していないファイル拡張子が選択されたようです。【 .<%replace%> 】',

		'strPass' => '最終確認をした取引日(<%replace%>)以降のログに重複するログがあるため登録が見送られたようです。',
		'strPassCheck' => '最終確認をした取引日(<%replace%>)より前の取引であるため登録が見送られたようです。',
		'strLockBank' => '金融機関のロックが解除されていないようです。',
		'strMissBank' => '金融機関が取引のあった会期に登録されていないようです。',
		'strMissStampBook' => '日付がないようです。',
		'strMissStrTitle' => '摘要がないようです。',
		'strMissNumValue' => '入出金がないようです。',
		'strMissNumBalance' => '残高がないようです。',
		'strFormat' => '日付のフォーマットが正しくないようです。',
		'strTime' => '登録できる会計期間がないようです。',
		'strNumMin' => '入出金は、0以下は設定できないようです。',
		'strFormatNumValue' => '入出金のフォーマットが正しくないようです。',
		'strFormatNumBalance' => '残高のフォーマットが正しくないようです。',
		'strNumMax' => '入出金の上限は、11桁までとなっているようです。',
		'strNumBalanceMax' => '残高の上下限は、12桁までとなっているようです。',
		'strStatusNone' => '該当なし',
		'strSpaceMax' => '許容データ領域をオーバーしているため登録できないようです。',
		'strUploadSize' => '許容されているアップロードサイズを超過しているため登録できないようです。',
	),
);
