<?php

$vars = array(
	'varsRule'  => array(),
	'varsCheck'  => array(),
	'varsItem'  => array(
		'varsComment' => array(
			'strStatus' => '【 <%replace%>行 】',
			'strStatusLog' => '
				<table cellspacing="1" cellpadding="3" border="0" bgcolor="#222" width="100%">
					<tbody>
						<tr valign="middle">
							<td class="codePluginAccountingLibTableColumnMiddle" >結果(#{strTitle})</td>
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
								フィルタ一致行番号<br>
								<span class="codeLibBaseFontSizeSeventy">※ 行番号の前に『※』があるデータは収支管理の留保ログにあります。</span><br>
								#{replaceImport}
							</td>
						</tr>
						<tr valign="top">
							<td class="codePluginAccountingLibTableRowStr">
								フィルタ不一致行番号<br>
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
			'strStatusRow' => '【 <%replace%>行 】',
			'strStatusRowCash' => '【 ※<%replace%>行 】',
			'strStatusRowError' => '<%replace%><br>',
			'strStatusNone' => '該当なし',
			'strSpaceMax' => '許容データ領域をオーバーしているため登録できないようです。',
			'strUploadSize' => '管理者が許容しているアップロードサイズを超過しているため登録できないようです。',
			'strUploadError' => 'アップロードに失敗しました。',
			'strMissStampBook' => '日付がないようです。',
			'strMissNumValue' => '金額がないようです。',
			'strMissStrTitle' => '理由がないようです。',
			'strTime' => '当期の会計期間に属さないようです。',
			'strNumMin' => '金額は、0以下は設定できないようです。',
			'strNumMax' => '金額の上限は、11桁までとなっているようです。',
			'strMonetaryClaim' => '消費税区分「金銭債権譲渡」は2014年4月1日以後から設定できるようです。',
			'strFormat' => '日付のフォーマットが正しくないようです。',
			'strFormatNumValue' => '金額のフォーマットが正しくないようです。',
		),
	),
	'pathCss' => '',
	'portal' => array(
		'varsNavi' => array(),
		'varsList' => array(),
		'varsDetail' => array(
			'varsStart' => array(
				'strTitle' => 'フィルタインポート',
				'strClass' => 'codeLibBaseImgDb',
				'varsEdit' => array( 'flagReloadUse' => 1, 'flagPreferenceUse' => 1,),
			),
			'varsEnd' => array(
				'strTitle' => '処理完了',
				'strClass' => 'codeLibBaseImgSheet',
				'varsEdit' => array(),
				'varsBtn' => array(
					array(
						'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
						'id' => 'Retry',
						'vars' => array( 'flagRetry' => 1, ),
						'strTitle' => 'フィルタリトライ',
					),
					array(
						'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
						'id' => 'Back',
						'vars' => array( 'flagBack' => 1, ),
						'strTitle' => '戻る',
					),
					array(
						'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
						'id' => 'Hide',
						'vars' => array( 'flagHide' => 1, ),
						'strTitle' => '閉じる',
					),
				),
				'templateDetail' => array(
					array(
						'flagMustUse' => 0,
						'id' => 'End',
						'strTitle' => '', 'strExplain' => '', 'value' => '',
						'flagErrorNow' => 0,
						'arrayError' => array(),
						'flagContentUse' => 0,
						'flagCommentUse' => 1, 'strCommentTitle' => '', 'strComment' => '処理が完了しました。',
						'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 0,
						'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
						'arrayOption' => array(), 
						'flagFoldUse' => 0, 'flagFoldNow' => 0,
						'flagHideUse' => 1, 'flagHideNow' => 0,
					),
					array(
						'flagMustUse' => 0,
						'id' => 'Table',
						'strTitle' => '', 'strExplain' => '', 'value' => '',
						'flagErrorNow' => 0,
						'arrayError' => array(),
						'flagContentUse' => 1,
						'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
						'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 0,
						'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
						'arrayOption' => array(), 
						'flagFoldUse' => 0, 'flagFoldNow' => 0,
						'varsSpace' => array(
							'varsStatus' => array(
								'strBorderColor' => '',
								'flagOverflowXUse' => 0,
								'flagOverflowYUse' => 0,
								'numMargin' => 0,
								'numPadding' => 0,
								'numHeight' => 140,
								'numWidth' => '90',
								'unitWidth' => '%'
							),
							'varsDetail' => array(
								'strHtml' => '',
							),
						),
					),
				),
				'varsDetail' => array(),
			),
			'varsStatus' => array(
				'flagUse' => 1,
				'flagNow' => 'form',
				'flagCakeUse' => 1,
				'flagFoldUse' => 0,
				'flagViewUse' => 0,
				'flagFormUse' => 1,
				'switchList' => array('form'),
			),
			'varsEdit' => array( 'flagReloadUse' => 1, 'flagPreferenceUse' => 1,),
			'templateDetail' => array(
				array(
					'flagMustUse' => 0,
					'id' => 'DummyEditCurrent',
					'strTitle' => '',
					'strExplain' => '',
					'value' => '',
					'flagErrorNow' => 0,
					'arrayError' => array(),
					'flagContentUse' => 0,
					'flagCommentUse' => 1, 'strCommentTitle' => '【お知らせ】', 'strComment' => '<span class="codeLibBaseFontOrange" style="float:none;">※ 既にデータが確定しているため使用できません。</span>',
					'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 100,
					'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(), 
					'flagFoldUse' => 1, 'flagFoldNow' => 0,
				),
				array(
					'flagMustUse' => 1,
					'id' => 'Upload',
					'strTitle' => 'CSVファイルアップロード', 'strExplain' => '　※ 複数選択可。', 'value' => 'dummy',
					'flagErrorNow' => 0,
					'arrayError' => array(
						array( 'flagCheck' => 'attest', 'flagUse' => 1, 'strComment' => array(
							'strFileType' => 'アップロードが許可されていない拡張子が選択されたようです。',
							'strBlank' => 'ファイルが選択されていないようです。',
							'strError' => 'アップロードできなかったファイルがあったようです。',
							'strSize' => '許容されているファイルサイズを超えていたファイルがあったようです。',
						),),
					),
					'flagContentUse' => 0,
					'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'input', 'flagInputType' => 'file', 'numMaxlength' => 0, 'flagMultiple' => 1,
					'numWidth' => 90, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(), 
					'arrayHidden' => array(),
					'arrFileType' => array('csv' => 1,),
				),
			),
			'varsPage' => array(),
			'varsDetail' => array(),
			'varsBtn' => array(
				array(
					'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
					'id' => 'SaveBtn',
					'vars' => array( 'idTarget' => 'save'),
					'strTitle' => 'インポート',
				),
			),
			'view' => array(),
			'form' => array(
				'varsFormat' => array(
					'id' => '',
					'flagType' => 'normalFormat',
					'numHeight' => 0,
					'numWidth' => 0,
					'flagHeaderLeftUse' => 1,
					'strTitleHeaderLeft' => '',
					'strClassHeaderLeft' => 'codeLibBaseImgBlank',
					'flagHeaderRightUse' => 1,
					'strTitleHeaderRight' => '',
					'strClassHeaderRight' => 'codeLibBaseImgUnloading',
					'flagBodyAutoUse' => 1,
					'strBody' => '',
					'strClassBody' => '',
					'flagFooderUse' => 1,
					'flagFooderLeftUse' => 1,
					'strTitleFooderLeft' => '',
					'strClassFooderLeft' => '',
					'flagFooderRightUse' => 1,
					'strTitleFooderRight' => '',
					'strClassFooderRight' => '',
					'flagHeaderLeftWidth' => 1,
					'numWidthHeaderLeft' => 0,
					'flagHeaderRightWidth' => 0,
					'numWidthHeaderRight' => 0,
					'flagFooderLeftWidth' => 0,
					'numWidthFooderLeft' => 0,
					'flagFooderRightWidth' => 0,
					'numWidthFooderRight' => 0,
				),
				'varsEdit' => array( 'flagReloadUse' => 1, 'flagPreferenceUse' => 1,),
				'varsDetail' => array(
					'varsStatus' => array(
						'flagBtnBottomUse' => 1,
					),
					'varsBtn' => array(),
					'varsDetail' => array(),
				),
			),
		),
		'varsTemplateLayout' => array(
			'varsStatus' => array(
				'flagNaviUse' => 0,
				'flagNaviToolUse' => 0,
				'flagListUse' => 0,
				'flagListToolUse' => 0,
				'flagDetailUse' => 1,
				'flagDetailToolUse' => 1,
			),
			'varsLayout' => array(
				'varsStatus' => array(
					'flagNow' => 1,
					'flagResizeUse' => 1,
					'flagMoveUse' => 0,
					'flagCakeUse' => 1,
					'flagSwitchUse' => 0,
					'flagSwitchNow' => 'Standard',
					'switchList' => array('Standard'),
				),
				'varsDetail' => array(
					array(
						'id' => 'Detail',
						'flagBoxUse' => 1,
						'numPriority' => 0,
						'numSort' => 2,
						'numWidth' => 200,			'numHeight' => 200,
						'numWidthMin' => 200,		'numHeightMin' => 200,
						'numWidthStandard' => 200,	'numHeightStandard' => 200,
						'numWidthWide' => 200,		'numHeightWide' => 200,
						'numWidthClassic' => 200,	'numHeightClassic' => 200,
						'numHeightBox' => 0,		'numWidthBox' => 0,//auto
					),
				)
			),
			'navi' => array(),
			'list' => array(),
			'detail' => array(
				'varsFormat' => array(
					'id' => '',
					'flagType' => 'normalFormat',
					'numHeight' => 0,
					'numWidth' => 0,
					'flagHeaderLeftUse' => 1,
					'strTitleHeaderLeft' => '',
					'strClassHeaderLeft' => '',
					'flagHeaderRightUse' => 1,
					'strTitleHeaderRight' => '',
					'strClassHeaderRight' => 'codeLibBaseImgUnloading',
					'flagBodyAutoUse' => 0,
					'strBody' => '',
					'strClassBody' => '',
					'flagFooderUse' => 1,
					'flagFooderLeftUse' => 1,
					'strTitleFooderLeft' => '',
					'strClassFooderLeft' => '',
					'flagFooderRightUse' => 1,
					'strTitleFooderRight' => '',
					'strClassFooderRight' => '',
					'flagHeaderLeftWidth' => 1,
					'numWidthHeaderLeft' => 0,
					'flagHeaderRightWidth' => 0,
					'numWidthHeaderRight' => 0,
					'flagFooderLeftWidth' => 0,
					'numWidthFooderLeft' => 0,
					'flagFooderRightWidth' => 1,
					'numWidthFooderRight' => 0,
				),
				'varsTool' => array(
					'varsDetail' => array(
						array(
							'flagUse' => 1,
							'flagNow' => 0,
							'id' => 'Reload',
							'strClass' => 'codeLibBtnImgReload',
							'strClassOver' => 'codeLibBtnImgReloadOver',
							'strClassNoactive' => 'codeLibBtnImgReloadNoactive',
							'strTitle' => '更新',
						),
						array(
							'flagUse' => 1,
							'flagNow' => 0,
							'id' => 'Preference',
							'strClass' => 'codeLibBtnImgPreference',
							'strClassOver' => 'codeLibBtnImgPreferenceOver',
							'strClassNoactive' => 'codeLibBtnImgPreferenceNoactive',
							'strTitle' => 'フィルタ設定',
						),
					),
				),
			),
		),
	),
	'child' => array(),
);


