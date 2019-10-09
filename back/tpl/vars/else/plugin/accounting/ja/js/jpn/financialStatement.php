<?php

$vars = array(
	'varsFlag' => array(
		'flagFiscalPeriod' => 'f1',
		'idDepartment'     => 'none',
		'flagFS'           => 'PL',
		'flagUnit'         => 0,
		'flagCalc'         => 'floor',
		'flagZero'         => 1,
	),
	'varsItem'  => array(
		'strClassNone' => 'codeLibBaseFontCcc',
		'varsOutput' => array(
			'strTitleFile' => '決算書',
			'strTitle' => '',
			'strPeriodSub' => '',
			'strTitleSub' => '',
			'strBlank' => '　　　　',
			'strEntity' => '',
			'strEntityExt' => '事業体(<%replace%>)',

			'strFS' => '',
			'strPeriod' => '',

			'strNumExt' => '会期(第<%replace%>期)',
			'strNum' => '第<%replace%>期',

			'strPeriodExt' => '自 平成<%strStartHeisei%>年<%strStartMonth%>月1日　至 平成<%strEndHeisei%>年<%strEndMonth%>月末日',
			'strPointExt' => '平成<%strEndHeisei%>年<%strEndMonth%>月末日　現在',

			'strDepartmentExt' => '部門(<%replace%>)',
			'strDepartment' => '',

			'strUnit' => '単位(<%replace%>)',
			'strAccountTitle' => '項目',
			'strNext' => '金額',
		),
	),
	'varsPrint' => array(
		'varsStatus' => array(
			'strTitle'  => '',
			'pathCss'   => '',
			'strBlank'   => '　',
			'idInsertTable' => 'insertTable',
			'idHeight'  => 'idHeight',
			'numHeight' => 1060,
			'varsTmpl' => array(
				'tmplWrap'   => '',
				'tmplPage'   => '',
				'tmplTable'  => '',
				'tmplColumn' => '',
			),
		),
		'varsDetailTmpl' => array(
			'id' => '',
			'idTmplColumn' => 'tmplColumn',
			'idTmplTable' => 'tmplTable',
			'strRow' => '',
			'numTr' => 1,
		),
		'varsDetail' => array(),
	),
	'pathCss' => '',
	'portal' => array(
		'varsNavi' => array(
			'varsStatus' => array(
				'flagNow' => 'form',
				'flagCakeUse' => 0,
				'flagViewUse' => 0,
				'flagFormUse' => 1,
				'switchList' => array('form'),
			),
			'templateDetail' => array(
				array(
					'flagMustUse' => 1,
					'id' => 'FlagFiscalPeriod',
					'strTitle' => '会計期間',
					'strExplain' => '',
					'value' => 'f1',
					'flagErrorNow' => 0,
					'arrayError' => array(
						array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
					),
					'flagContentUse' => 0,
					'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
					'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(),
					'varsTmpl' => array(
						'varsPeriod' => array( 'strTitle' => '年度決算期', 'value' => 'f1', ),
						'arrayOption' => array(
							array( 'strTitle' => '年度決算期', 'value' => 'f1', ),
							array( 'strTitle' => '中間(前半期)', 'value' => 'f21', ),
							array( 'strTitle' => '中間(後半期)', 'value' => 'f22', ),
							array( 'strTitle' => '第1四半期', 'value' => 'f41', ),
							array( 'strTitle' => '第2四半期', 'value' => 'f42', ),
							array( 'strTitle' => '第3四半期', 'value' => 'f43', ),
							array( 'strTitle' => '第4四半期', 'value' => 'f44', ),
						),
						'strMonth' => '月期',
					),
				),
				array(
					'flagMustUse' => 1,
					'id' => 'IdDepartment',
					'strTitle' => '部門',
					'strExplain' => '',
					'value' => 'none',
					'flagErrorNow' => 0,
					'arrayError' => array(
						array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
					),
					'flagContentUse' => 0,
					'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
					'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(
						array( 'strTitle' => '指定なし', 'value' => 'none', ),
					),
					'varsTmpl' => array(
						'varsNone' => array( 'strTitle' => '指定なし', 'value' => 'none', ),
					),
					'flagHideUse' => 1, 'flagHideNow' => 0,
				),
				array(
					'flagMustUse' => 1,
					'id' => 'FlagFS',
					'strTitle' => '財務諸表',
					'strExplain' => '',
					'value' => 'PL',
					'flagErrorNow' => 0,
					'arrayError' => array(
						array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
					),
					'flagContentUse' => 0,
					'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
					'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(
						array( 'strTitle' => '貸借対照表', 'value' => 'BS', ),
						array( 'strTitle' => '損益計算書', 'value' => 'PL', ),
						array( 'strTitle' => '製造原価報告書', 'value' => 'CR', ),
					),
				),
				array(
					'flagMustUse' => 1,
					'id' => 'FlagUnit',
					'strTitle' => '表示単位',
					'strExplain' => '',
					'value' => 0,
					'flagErrorNow' => 0,
					'arrayError' => array(
						array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
					),
					'flagContentUse' => 0,
					'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
					'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(
						array( 'strTitle' => '円', 'value' => 0, ),
						array( 'strTitle' => '千円', 'value' => 1000, ),
						array( 'strTitle' => '百万円', 'value' => 1000000, ),
					),
					'flagHideUse' => 1, 'flagHideNow' => 1,
				),
				array(
					'flagMustUse' => 1,
					'id' => 'FlagCalc',
					'strTitle' => '端数処理',
					'strExplain' => '',
					'value' => 'floor',
					'flagErrorNow' => 0,
					'arrayError' => array(
						array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
					),
					'flagContentUse' => 0,
					'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
					'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(
						array( 'strTitle' => '切捨て', 'value' => 'floor', ),
						array( 'strTitle' => '四捨五入', 'value' => 'round', ),
						array( 'strTitle' => '切上げ', 'value' => 'ceil', ),
					),
					'flagHideUse' => 1, 'flagHideNow' => 1,
				),
				array(
					'flagMustUse' => 1,
					'id' => 'FlagZero',
					'strTitle' => '0円表示',
					'strExplain' => '',
					'value' => 1,
					'flagErrorNow' => 0,
					'arrayError' => array(
						array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
					),
					'flagContentUse' => 0,
					'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
					'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(
						array( 'strTitle' => '0円表示', 'value' => 1, ),
						array( 'strTitle' => '0円非表示', 'value' => 0, ),
					),
				),
			),
			'varsPage' => array(),
			'varsDetail' => array(),
			'varsBtn' => array(
				array(
					'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
					'id' => 'EventFormBtn',
					'vars' => array( 'idTarget' => 'search', ),
					'strTitle' => '検索',
				),
			),
			'varsStart' => array(
				'strTitle' => '検索',
				'strClass' => 'codeLibBaseImgDb',
				'varsEdit' => array( 'flagReloadUse' => 0,'flagOutputUse' => 1, ),
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
					'strClassHeaderLeft' => 'codeLibBaseImgDb',
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
				'varsEdit' => array( 'flagReloadUse' => 0, ),
				'varsDetail' => array(
					'varsStatus' => array(
						'flagBtnBottomUse' => 1,
					),
					'varsBtn' => array(),
					'varsDetail' => array(),
				),
			),
		),
		'varsList' => array(
			'varsStart' => array(
				'varsStatus' => array(
					'strTitle' => '決算（単位:円）',
					'strClass' => 'codeLibBaseImgFolder',
				),
				'varsEdit' => array(
					'flagReloadUse' => 1,
					'flagOutputUse' => 1,
					'flagPrintUse' => 1,
				),
				'varsDetail' => array(),
			),
			'varsStatus' => array(
				'flagNow' => 'tableTree',
				'flagPrintUse' => 1,
				'flagPrintNow' => 'item',
				'flagOutputUse' => 1,
				'flagOutputNow' => 'item',
				'flagCakeUse' => 1,
				'flagTableUse' => 0,
				'flagScheduleUse' => 0,
				'flagThumbnailUse' => 0,
				'flagTableTreeUse' => 1,
				'flagTreeUse' => 0,
				'flagBoldUse' => 0,
				'flagFontUse' => 0,
				'flagBgUse' => 0,
				'switchList' => array('tableTree'),
				'switchOutputList' => array('item', 'itemAll', 'spanAll'),
				'switchPrintList' => array('item', 'itemAll'),
			),
			'templateDetail' => array(
				'flagBtnUse' => 1,
				'flagMoveUse' => 1,
				'id' => '0',
				'idTarget' => '0',
				'strTitle' => '',
				'flagBoldNow' => 0,
				'strClassFont' => '',
				'strClassBg' => '',
				'numSort' => 0,
				'strClass' => 'codeLibBaseImgSheet',
				'strClassLoad' => 'codeLibTableLineLoad',
				'flagCheckboxUse' => 1,
				'flagCheckboxNow' => 0,
				'varsColumnDetail' => array(
					'id' => '',
				),
			),
			'varsPage' => array(
				'varsStatus' => array(
					'flagStatusUse' => 0,
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
			'varsDetail' => array(),
			'varsBtn' => array(),
			'tableTree' => array(
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
					'flagFooderRightWidth' => 0,
					'numWidthFooderRight' => 0,
				),
				'varsPage' => array(
					'varsStatus' => array(
						'flagStatusUse' => 0,
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
				'varsHtml' => array(),
				'varsEdit' => array(
					'flagReloadUse' => 1,
					'flagOutputUse' => 1,
					'flagPrintUse' => 1,
				),
				'varsDetail' => array(
					'varsStatus' => array(
						'flagMenuUse' => 1,
						'flagPageUse' => 1,
						'flagInsertUse' => 0,
						'flagSortUse' => 1,
						'flagFoldNow' => 0,
						'flagFoldUse' => 1,
						'flagColumnUse' => 0,
						'flagResizeUse' => 1,
						'flagSortColumnUse' => 1,
						'flagBgUse' => 1,
						'flagFontUse' => 1,
						'flagBoldUse' => 0,
						'flagCakeUse' => 1,
						'flagCakeColumnUse' => 1,
						'flagCakeTreeUse' => 0,
						'flagBtnUse' => 0,
						'flagMoveUse' => 1,
						'flagMoveSortUse' => 0,
						'flagBtnBottomUse' => 0,
					),
					'varsFormat' => array(
						'id' => '',
						'flagType' => 'singleFormat',
						'numHeight' => 0,
						'numWidth' => 0,
						'flagHeaderUse' => 1,
						'flagHeaderLeftUse' => 1,
						'strTitleHeaderLeft' => '',
						'strClassHeaderLeft' => 'codeLibBaseImgBlank',
						'flagHeaderRightUse' => 0,
						'strTitleHeaderRight' => '',
						'strClassHeaderRight' => '',
						'flagBodyAutoUse' => 1,
						'strBody' => '',
						'strClassBody' => '',
						'flagFooderUse' => 0,
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
					'varsContext' => array(
						'varsStatus' => array(
							'numTop' => 0,
							'numLeft' => 0,
						),
						'varsDetail' => array(),
					),
					'varsColumn' => array(
						array(
							'flagUse' => 1,
							'flagCheckUse' => 0,
							'flagCheckNow' => 1,
							'id' => 'Tree',
							'strTitle' => '項目',
							'numSort' => 0,
							'numWidth' => 150,
							'numWidthMin' => 150,
							'flagType' => 'str',
							'flagTimeType' => '',
							'flagAllCheckbox' => 0,
							'flagAllCheckboxNow' => 0,
							'flagSortColumn' => 0,
							'vars' => array(),
							'child' => array(),
						),
						array(
							'flagUse' => 1,
							'flagCheckUse' => 1,
							'flagCheckNow' => 1,
							'id' => 'numValue',
							'strTitle' => '金額',
							'numSort' => 1,
							'numWidth' => 100,
							'numWidthMin' => 26,
							'flagAlign' => 'right',
							'flagType' => 'str',
							'flagTimeType' => '',
							'flagAllCheckbox' => 0,
							'flagAllCheckboxNow' => 0,
							'flagSortColumn' => 1,
							'vars' => array(),
							'child' => array(),
						),
					),
					'varsHtml' => array(),
					'varsDetail' => array(),
				),
			),
			'table' => array(),
			'schedule' => array(),
			'thumbnail' => array(),
			'tree' => array(),
		),
		'varsDetail' => array(),
		'varsTemplateLayout' => array(
			'varsStatus' => array(
				'flagNaviUse' => 1,
				'flagNaviToolUse' => 1,
				'flagListUse' => 1,
				'flagListToolUse' => 1,
				'flagDetailUse' => 0,
				'flagDetailToolUse' => 0,
			),
			'varsLayout' => array(
				'varsStatus' => array(
					'flagNow' => 2,
					'flagResizeUse' => 1,
					'flagMoveUse' => 1,
					'flagCakeUse' => 1,
					'flagSwitchUse' => 1,
					'flagSwitchNow' => 'Standard',
					'switchList' => array('Standard','Wide',),
				),
				'varsDetail' => array(
					array(
						'id' => 'Navi',
						'flagBoxUse' => 1,
						'numPriority' => 0,
						'numSort' => 0,
						'numWidth' => 175,			'numHeight' => 200,
						'numWidthMin' => 175,		'numHeightMin' => 200,
						'numWidthStandard' => 175,	'numHeightStandard' => 200,
						'numWidthWide' => 175,		'numHeightWide' => 200,
						'numWidthClassic' => 175,	'numHeightClassic' => 200,
						'numHeightBox' => 0,		'numWidthBox' => 0,//auto
					),
					array(
						'id' => 'List',
						'flagBoxUse' => 1,
						'numPriority' => 1,
						'numSort' => 1,
						'numWidth' => 200,			'numHeight' => 200,
						'numWidthMin' => 200,		'numHeightMin' => 200,
						'numWidthStandard' => 200,	'numHeightStandard' => 200,
						'numWidthWide' => 200,		'numHeightWide' => 200,
						'numWidthClassic' => 200,	'numHeightClassic' => 200,
						'numHeightBox' => 0,		'numWidthBox' => 0,//auto
					),
				),
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
			'list' => array(
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
					'flagFooderRightWidth' => 1,
					'numWidthFooderRight' => 0,
				),
				'varsTool' => array(
					'varsDetail' => array(
						array(
							'flagUse' => 1,
							'flagNow' => 1,
							'id' => 'Reload',
							'strClass' => 'codeLibBtnImgReload',
							'strClassOver' => 'codeLibBtnImgReloadOver',
							'strClassNoactive' => 'codeLibBtnImgReloadNoactive',
							'strTitle' => '更新',
						),
						array(
							'flagUse' => 1,
							'flagNow' => 1,
							'id' => 'Output',
							'strClass' => 'codeLibBtnImgDownload',
							'strClassOver' => 'codeLibBtnImgDownloadOver',
							'strClassNoactive' => 'codeLibBtnImgDownloadNoactive',
							'strTitle' => 'エクスポート',
							'varsContext' => array(
								'varsStatus' => array(
									'numTop' => 0,
									'numLeft' => 0,
									'flagNow' => 'item',
								),
								'varsDetail' => array(
									array(
										'id' => 'Item', 'flagCheckUse' => 1, 'flagCheckNow' => 0,
										'strTitle' => '選択した財務諸表', 'strClass' => 'codeLibBaseImgSheet',
										'vars' => array( 'idTarget' => 'item',),
										'child' => array(),
									),
									array(
										'id' => 'ItemAll', 'flagCheckUse' => 1, 'flagCheckNow' => 0,
										'strTitle' => '全ての財務諸表', 'strClass' => 'codeLibBaseImgSheet',
										'vars' => array( 'idTarget' => 'itemAll',),
										'child' => array(),
									),
									array(
										'id' => 'SpanAll', 'flagCheckUse' => 1, 'flagCheckNow' => 0,
										'strTitle' => '全ての会計期間', 'strClass' => 'codeLibBaseImgSheet',
										'vars' => array( 'idTarget' => 'spanAll',),
										'child' => array(),
									),
								),
							),
						),
						array(
							'flagUse' => 1,
							'flagNow' => 1,
							'id' => 'Print',
							'strClass' => 'codeLibBtnImgPrint',
							'strClassOver' => 'codeLibBtnImgPrintOver',
							'strClassNoactive' => 'codeLibBtnImgPrintNoactive',
							'strTitle' => '印刷',
							'varsContext' => array(
								'varsStatus' => array(
									'numTop' => 0,
									'numLeft' => 0,
									'flagNow' => 'item',
								),
								'varsDetail' => array(
									array(
										'id' => 'Item', 'flagCheckUse' => 1, 'flagCheckNow' => 0,
										'strTitle' => '選択した財務諸表', 'strClass' => 'codeLibBaseImgSheet',
										'vars' => array( 'idTarget' => 'item',),
										'child' => array(),
									),
									array(
										'id' => 'ItemAll', 'flagCheckUse' => 1, 'flagCheckNow' => 0,
										'strTitle' => '全ての財務諸表', 'strClass' => 'codeLibBaseImgSheet',
										'vars' => array( 'idTarget' => 'itemAll',),
										'child' => array(),
									),
								),
							),
						),
					),
				),
			),
			'detail' => array(),
		),
	),
	'child' => array(),
);


