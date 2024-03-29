<?php

$vars = array(
	'varsItem' => array(
		'numWidthColumn' => 80,
		'numFontSize' => 10,
		'strColNumValue' => '【金額】',
		'strColStampBook' => '【日付】',
		'strColStrTitle' => '【理由】',
		'strColNum' => '行',
		'strColId' => '通番',
		'strApi' => 'API',
		'strMail' => 'フィルタメールインポート',
		'strItem' => 'フィルタインポート',
		'strPost' => 'フィルタポストインポート',
		'strBanksFile' => '口座管理-ファイル明細インポート',
		'strBanksWeb' => '口座管理-WEB明細インポート',
		'strBanksWrite' => '口座管理書出',
		'strCol' => '<%num%>列目 : <%strTitle%>',
		'varsComment' => array(
			'strStatusRow' => '【 <%replace%>行 】',
			'strStatusRowError' => '【 <%numRow%>行 】<%replace%><br>',
			'strStatusRowCash' => '【 ※<%replace%>行 】',

			'strStatusRowBanks' => '【 <%replace%> 】',
			'strStatusRowErrorBanks' => '【 <%numRow%> 】<%replace%><br>',
			'strStatusRowCashBanks' => '【 ※<%replace%> 】',

			'strStatusNone' => '該当なし',
			'strSpaceMax' => '許容データ領域をオーバーしているため登録できないようです。',
			'strUploadSize' => '管理者が許容しているアップロードサイズを超過しているため登録できないようです。',
			'strUploadError' => 'アップロードに失敗しました。',
			'strMissStampBook' => '日付がないようです。',
			'strMissNumValue' => '金額がないようです。',
			'strMissStrTitle' => '理由がないようです。',
			'strTime' => '当期の会計期間に属さないようです。',
			/*
			 * 20191001 start
			 */
		    'strRateConsumptionTaxReduced' => '軽減税率施行前の日付のため登録できないようです。',
		    /*
		     * 20191001 end
		     */
			'strNumMin' => '金額は、0以下は設定できないようです。',
			'strNumMax' => '金額の上限は、11桁までとなっているようです。',
			'strFormat' => '日付のフォーマットが正しくないようです。',
			'strFormatNumValue' => '金額のフォーマットが正しくないようです。',
			'strMonetaryClaim' => '「金銭債権譲渡」は2014年4月1日以後から設定できるようです。',
		),
		'varsStr' => array(
			'strTitle' => ' のログ結果',
			'idLog' => '',
			'strLoadAll' => '読込総数',
			'numLoadAll' => '',
			'strUnit' => '件',
			'strImportAll' => 'フィルタ一致数',
			'numImportAll' => '',
			'strNoneAll' => 'フィルタ不一致数',
			'numNoneAll' => '',
			'strErrorAll' => 'エラー数',
			'numErrorAll' => '',

			'strImportNum' => 'フィルタ一致 行番号<br><span class="codeLibBaseFontSizeSeventy">※ 行番号の前に、『※』があるデータは収支管理の留保ログにあります。</span>',
			'strImportNumBanks' => 'フィルタ一致 口座管理通番<br><span class="codeLibBaseFontSizeSeventy">※ 通番の前に、『※』があるデータは収支管理の留保ログにあります。</span>',
			'strNoneNum' => 'フィルタ不一致 行番号',
			'strNoneNumBanks' => 'フィルタ不一致 口座管理通番',
			'strErrorNum' => 'エラー原因',
		),
		'varsMatch' => array(
			array('str' => '日', 'id' => 'StampBook'),
			array('str' => '金', 'id' => 'NumValue'),
			array('str' => '摘要', 'id' => 'StrTitle'),
			array('str' => '内容', 'id' => 'StrTitle'),
			array('str' => '理由', 'id' => 'StrTitle'),
			array('str' => 'メモ', 'id' => 'StrTitle'),
		),
	),
	'portal' => array(
		'varsNavi' => array(
			'varsStatus' => array(
				'flagNow' => 'tree',
				'flagCakeUse' => 1,
				'flagTreeUse' => 1,
				'flagSearchUse' => 0,
				'flagFolderUse' => 0,
				'switchList' => array('tree'),
			),
			'varsDetail' => array(),
			'tree' => array(
				'varsFormat' => array(
					'id' => '',
					'flagType' => 'normalFormat',
					'numHeight' => 0,
					'numWidth' => 0,
					'flagHeaderLeftUse' => 1,
					'strTitleHeaderLeft' => 'フィルタ不一致ログ',
					'strClassHeaderLeft' => 'codeLibBaseImgDb',
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
					'flagFooderRightWidth' => 0,
					'numWidthFooderRight' => 0,
				),
				'varsEdit' => array(
					'flagReloadUse' => 1,
				),
				'templateDetail' => array(
					'id' => 'Dummy', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 0, 'flagEditUse' => 0,
					'strTitle' => '',
					'strClass' => 'codeLibBaseImgSheet',
					'strClassLoad' => 'codeLibTreeLoad',
					'vars' => array(
						'idTarget' => 0,
					),
					'child' => array(),
				),
				'varsDetail' => array(
					'varsStatus' => array(
						'flagUse' => 1,
						'flagMoveUse' => 0,
						'flagInsertUse' => 0,
						'flagFoldNow' => 0,
						'flagFoldUse' => 0,
						'flagCakeUse' => 1,
						'flagRemoveUse' => 0,
						'flagCheckUse' => 0,
						'flagCheckNow' => 1,
						'flagBarUse' => 0,
						'flagBtnBottomUse' => 1,
						'flagBtnUse' => 1,
						'flagLockUse' => 0,
						'flagLockNow' => 0,
						'flagPageUse' => 1,
						'flagInnerPageUse' => 1,
						'flagFindUse' => 0,
						'id' => 'tree',
					),
					'varsFormat' => array(
						'id' => '',
						'flagType' => 'singleFormat',
						'numHeight' => 0,
						'numWidth' => 0,
						'flagHeaderUse' => 0,
						'flagHeaderLeftUse' => 1,
						'strTitleHeaderLeft' => '',
						'strClassHeaderLeft' => '',
						'flagHeaderRightUse' => 1,
						'strTitleHeaderRight' => '',
						'strClassHeaderRight' => '',
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
					'varsFind' => array(
						'value' => '',
						'flag' => 0,
						'num' => 0,
						'numWidth' => 70,
						'unitWidth' => '%',
						'strTitle' => '絞込検索',
					),
					'varsPage' => array(
						'varsStatus' => array(
							'flagStatusUse' => 1,
							'flagLockUse' => 1,
							'flagLockNow' => 0,
							'flagTopUse' => 1,
							'flagEndUse' => 1,
							'flagNextUse' => 1,
							'flagPrevUse' => 1,
							'numRows' => 0,
							'numLotNow' => 0,
						),
					),
					'varsBtn' => array(
						array(
							'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
							'id' => 'DeleteAllBtn',
							'strTitle' => 'ログ全削除',
							'vars' => array( 'idTarget' => 'deleteAll'),
						),
					),
					'varsDetail' => array(),
				),
			),
			'item' => array(),
			'folder' => array(),
		),
		'varsDetail' => array(
			'varsStart' => array(
				'strTitle' => '詳細',
				'strClass' => 'codeLibBaseImgSheet',
			),
			'varsEnd' => array(
				'strTitle' => '処理完了',
				'strClass' => 'codeLibBaseImgSheet',
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
			'varsEdit' => array( 'flagReloadUse' => 1, ),
			'tmplBtn' => array(
				'varsStart' => array(
					array(
						'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
						'id' => 'DeleteBtn',
						'strTitle' => 'ログ削除',
						'vars' => array( 'idTarget' => 'delete'),
					),
					array(
						'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
						'id' => 'FilterBtn',
						'strTitle' => 'フィルタ設定',
						'vars' => array( 'idTarget' => 'filter'),
					),
				),
				'varsEnd' => array(
					array(
						'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
						'id' => 'BackStartBtn',
						'strTitle' => '戻る',
						'vars' => array( 'idTarget' => 'backStart'),
					),
					array(
						'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
						'id' => 'Hide',
						'vars' => array( 'idTarget' => 'backHide', 'flagHide' => 1,),
						'strTitle' => '閉じる',
					),
				),
			),
			'tmplDetail' => array(
				'varsStart' => array(
					array(
						'flagMustUse' => 0,
						'id' => 'Status',
						'strTitle' => '', 'strExplain' => '', 'value' => '',
						'flagErrorNow' => 0,
						'arrayError' => array(),
						'flagContentUse' => 0,
						'flagCommentUse' => 1, 'strCommentTitle' => '', 'strComment' => '',
						'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 0,
						'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
						'arrayOption' => array(),
						'flagFoldUse' => 0, 'flagFoldNow' => 0,
						'varsTmpl' => array(
							'strComment' => '【 経由 】 : <%flagType%><br>【 記録日時 】 : <%stampRegister%><br>【 更新日時 】 : <%stampUpdate%><br>【 不一致数 】 : <%numAll%>件'
						),
					),
					array(
						'flagMustUse' => 0,
						'id' => 'CsvTable',
						'strTitle' => '', 'strExplain' => '', 'value' => '',
						'flagErrorNow' => 0,
						'arrayError' => array(
							array( 'flagCheck' => 'attest', 'flagUse' => 1, 'strComment' => array('strCol' => '列が重複しているようです。',),),
						),
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
								'flagOverflowYUse' => 1,
								'numMargin' => 10,
								'numPadding' => 5,
								'numHeight' => 200,
								'numWidth' => '90',
								'unitWidth' => '%'
							),
							'varsDetail' => array(
								'strHtml' => '',
							),
						),
					),
					array(
						'flagMustUse' => 1,
						'id' => 'NumColStampBook',
						'strTitle' => '日付',
						'strExplain' => '『日付』に該当する列を指定してください。',
						'value' => 1,
						'flagErrorNow' => 0,
						'arrayError' => array(
							array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),

						),
						'flagContentUse' => 0,
						'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
						'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
						'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
						'arrayOption' => array(),
					),
					array(
						'flagMustUse' => 1,
						'id' => 'NumColNumValue',
						'strTitle' => '金額',
						'strExplain' => '『金額』に該当する列を指定してください。',
						'value' => 2,
						'flagErrorNow' => 0,
						'arrayError' => array(
							array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
						),
						'flagContentUse' => 0,
						'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
						'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
						'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
						'arrayOption' => array(),
					),
					array(
						'flagMustUse' => 1,
						'id' => 'NumColStrTitle',
						'strTitle' => '理由',
						'strExplain' => '『理由』に該当する列を指定してください。',
						'value' => 3,
						'flagErrorNow' => 0,
						'arrayError' => array(
							array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
						),
						'flagContentUse' => 0,
						'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
						'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
						'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
						'arrayOption' => array(),
					),
				),
				'varsEnd' => array(
					array(
						'flagMustUse' => 0,
						'id' => 'End',
						'strTitle' => '', 'strExplain' => '', 'value' => '',
						'flagErrorNow' => 0,
						'arrayError' => array(),
						'flagContentUse' => 0,
						'flagCommentUse' => 1, 'strCommentTitle' => '', 'strComment' => '処理が無事完了しました。',
						'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 0,
						'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
						'arrayOption' => array(),
						'flagFoldUse' => 0, 'flagFoldNow' => 0,
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
			),
			'varsAnalyze' => array(
				'varsColumn' => array(),
				'varsDetail' => array(),
			),
			'varsPage' => array(),
			'varsDetail' => array(),
			'varsBtn' => array(),
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
				'varsEdit' => array( 'flagReloadUse' => 1, ),
				'varsDetail' => array(
					'varsStatus' => array(
						'flagBtnBottomUse' => 1,
					),
					'varsFormat' => array(
						'id' => '',
						'flagType' => 'singleFormat',
						'numHeight' => 0,
						'numWidth' => 0,
						'flagHeaderUse' => 0,
						'flagHeaderLeftUse' => 1,
						'strTitleHeaderLeft' => '',
						'strClassHeaderLeft' => '',
						'flagHeaderRightUse' => 0,
						'strTitleHeaderRight' => '',
						'strClassHeaderRight' => '',
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

						'flagHeaderLeftWidth' => 0,
						'numWidthHeaderLeft' => 0,
						'flagHeaderRightWidth' => 0,
						'numWidthHeaderRight' => 0,
						'flagFooderLeftWidth' => 0,
						'numWidthFooderLeft' => 0,
						'flagFooderRightWidth' => 0,
						'numWidthFooderRight' => 0,
					),
					'varsBtn' => array(),
					'varsDetail' => array(),
				),
			),
		),
		'varsTemplateLayout' => array(
			'varsStatus' => array(
				'flagNaviUse' => 1,
				'flagNaviToolUse' => 1,
				'flagListUse' => 0,
				'flagListToolUse' => 0,
				'flagDetailUse' => 1,
				'flagDetailToolUse' => 1,
			),
			'varsLayout' => array(
				'varsStatus' => array(
					'flagNow' => 2,
					'flagResizeUse' => 1,
					'flagMoveUse' => 1,
					'flagCakeUse' => 1,
					'flagSwitchUse' => 1,
					'flagSwitchNow' => 'Standard',
					'switchList' => array('Standard','Wide'),
				),
				'varsDetail' => array(
					array(
						'id' => 'Navi',
						'flagBoxUse' => 1,
						'numPriority' => 0,
						'numSort' => 0,
						'numWidth' => 250,			'numHeight' => 200,
						'numWidthMin' => 200,		'numHeightMin' => 200,
						'numWidthStandard' => 200,	'numHeightStandard' => 200,
						'numWidthWide' => 200,		'numHeightWide' => 200,
						'numWidthClassic' => 200,	'numHeightClassic' => 200,
						'numHeightBox' => 0,		'numWidthBox' => 0,//auto
					),
					array(
						'id' => 'Detail',
						'flagBoxUse' => 1,
						'numPriority' => 2,
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
			'navi' => array(
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
					),
				),
			),
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
					),
				),
			),
		),
	),
	'child' => array(

	),
);


