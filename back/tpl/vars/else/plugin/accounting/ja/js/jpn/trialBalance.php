<?php

$vars = array(
	'flagAuthorityLedger' => 0,
	'varsFlag' => array(
		'flagFiscalPeriod' => 'f1',
		'idDepartment'     => 'none',
		'flagFS'           => 'PL',
		'flagUnit'         => 0,
		'flagCalc'         => 'floor',
		'flagZero'         => 0,
	),
	'varsItem'  => array(
		'strClassNone' => 'codeLibBaseFontCcc',
		'strNone' => '補助科目未登録',
		'varsOutput' => array(
			'strTitle' => '試算表',
			'strTitleSub' => '',
			'strBlank' => '　',
			'strEntity' => '',
			'strEntityExt' => '事業体(<%replace%>)',

			'strFS' => '',
			'strPeriod' => '',

			'strNumExt' => '会期(第<%replace%>期)',
			'strNum' => '第<%replace%>期',

		    /*20190401 start*/
			'strPeriodExt' => '会計期間(自 平成<%strStartHeisei%>年<%strStartMonth%>月1日　至 平成<%strEndHeisei%>年<%strEndMonth%>月末日)',
		    'strPeriodExt20190401' => '会計期間(自 <%strStartNengoYear%>年<%strStartMonth%>月1日　至 <%strEndNengoYear%>年<%strEndMonth%>月末日)',
			'strPointExt' => '会期末(平成<%strEndHeisei%>年<%strEndMonth%>月末日)',
		    'strPointExt20190401' => '会期末(<%strEndNengoYear%>年<%strEndMonth%>月末日)',
		    /*20190401 end*/

			'strDepartmentExt' => '部門(<%replace%>)',
			'strDepartment' => '',

			'strUnit' => '単位(<%replace%>)',
			'strAccountTitle' => '勘定科目',
			'strDebit' => '借方',
			'strCredit' => '貸方',
			'strBalance' => '残高',
			'strPrev' => '',
			'strPrevTerm' => '前期繰越',
			'strPrevMonth' => '前月繰越',
			'strRateBS' => '構成比(%)',
			'strRatePLCR' => '売上高比(%)',
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
				'varsEdit' => array(
					'flagReloadUse' => 0,
					'flagOutputUse' => 1,
					'flagPrintUse' => 1,
				),
				'varsDetail' => array(
					'varsStatus' => array(
						'flagBtnBottomUse' => 1,
					),
					'varsBtn' => array(),
					'varsDetail' => array(),
				),
			),
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
					'strTitle' => '残高試算表（単位:円）',
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
						'flagBtnUse' => 1,
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
							'strTitle' => '勘定科目',
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
							'id' => 'sumPrev',
							'strTitle' => '前期繰越',
							'strTitleMonth' => '前月繰越',
							'numSort' => 1,
							'numWidth' => 80,
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
						array(
							'flagUse' => 1,
							'flagCheckUse' => 1,
							'flagCheckNow' => 1,
							'id' => 'sumDebit',
							'strTitle' => '借方',
							'numSort' => 2,
							'numWidth' => 80,
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
						array(
							'flagUse' => 1,
							'flagCheckUse' => 1,
							'flagCheckNow' => 1,
							'id' => 'sumCredit',
							'strTitle' => '貸方',
							'numSort' => 3,
							'numWidth' => 80,
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
						array(
							'flagUse' => 1,
							'flagCheckUse' => 1,
							'flagCheckNow' => 1,
							'id' => 'sumNext',
							'strTitle' => '残高',
							'numSort' => 4,
							'numWidth' => 80,
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
						array(
							'flagUse' => 1,
							'flagCheckUse' => 1,
							'flagCheckNow' => 1,
							'id' => 'numRate',
							'strTitleBS' => '構成比(%)',
							'strTitle' => '売上高比(%)',
							'numSort' => 4,
							'numWidth' => 80,
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
		'varsDetail' => array(
			'varsStatus' => array(
				'flagUse' => 1,
				'flagNow' => 'view',
				'flagCakeUse' => 1,
				'flagFoldUse' => 1,
				'flagViewUse' => 1,
				'flagFormUse' => 0,
				'flagMoveUse' => 1,
				'switchList' => array('view'),
			),
			'templateDetail' => array(
				array(
					'flagMustUse' => 0,
					'id' => 'TableDetail',
					'strTitle' => '補助科目', 'strExplain' => '', 'value' => '',
					'flagErrorNow' => 0,
					'arrayError' => array(),
					'flagContentUse' => 1,
					'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 0,
					'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(),
					'flagFoldUse' => 1, 'flagFoldNow' => 0,
					'varsSpace' => array(
						'varsStatus' => array(
							'strBorderColor' => '',
							'flagOverflowXUse' => 0,
							'flagOverflowYUse' => 0,
							'numMargin' => 10,
							'numPadding' => 0,
							'numHeight' => 0,
							'numWidth' => '70',
							'unitWidth' => '%',
						),
						'varsDetail' => array(
							'strHtml' => '',
						),
					),
					'varsStr' => array(
						'strTitle' => '補助科目',
						'strPrev' => '前期繰越',
						'strPrevMonth' => '前月繰越',
						'strDebit' => '借方',
						'strCredit' => '貸方',
						'strNext' => '残高',
						'strRateBS' => '構成比(%)',
						'strRatePLCR' => '売上高比(%)',
					),
					'tagTr' => '<tr valign="top">#{insertPoint}</tr>',
					'tagTdColumn' => '<td class="codePluginAccountingLibTableColumnMiddle" style="width:#{numWidth}px;"><div style="overflow:hidden;white-space:nowrap;width:#{numWidth}px;" title="#{insertPoint}">#{insertPoint}</div></td>',
					'tagTdRow' => '<td class="codeLibBaseTableRow" id="#{id}">#{insertPoint}</td>',
					'tagTdRowRight' => '<td class="codeLibBaseTableRowRight">#{insertPoint}</td>',
					'tagTable' => '
						<table class="codeLibBaseFontTen" cellspacing="1" cellpadding="3" border="0" bgcolor="#ccc" width="100%">
							<tbody>
								#{insertPoint}
							</tbody>
						</table>
					',
				),
			),
			'varsPage' => array(),
			'varsDetail' => array(),
			'varsBtn' => array(),
			'varsStart' => array(
				'strTitle' => '補助科目',
				'strClass' => 'codeLibBaseImgSheet',
			),
			'view' => array(
				'varsFormat' => array(
					'id' => '',
					'flagType' => 'normalFormat',
					'numHeight' => 0,
					'numWidth' => 0,
					'flagHeaderLeftUse' => 1,
					'strTitleHeaderLeft' => '補助科目',
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
				'varsEdit' => array(
					'flagReloadUse' => 0,
				),
				'varsDetail' => array(
					'varsStatus' => array(
						'flagCakeUse' => 1,
						'flagFoldUse' => 1,
						'flagFoldNow' => 0,
						'flagLineStatusUse' => 0,
						'flagAddUse' => 0,
						'flagRemoveUse' => 0,
						'flagEditUse' => 0,
						'flagBarUse' => 1,
						'flagPageUse' => 0,
						'flagInnerPageUse' => 0,
						'flagBtnBottomUse' => 1,
						'flagFindUse' => 0,
						'flagInnerFindUse' => 0,
					),
					'varsFormat' => array(
						'id' => '',
						'flagType' => 'singleFormat',
						'numHeight' => 0,
						'numWidth' => 0,
						'flagHeaderUse' => 1,
						'flagHeaderLeftUse' => 1,
						'strTitleHeaderLeft' => '',
						'strClassHeaderLeft' => '',
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
					'varsFind' => array(
						'strTitle' => 'ハイライト検索',
						'numWidth' => 70,//%
					),
					'varsPage' => array(),
					'varsBtn' => array(),
					'varsDetail' => array(),
				),
			),
			'form' => array(),
		),
		'varsTemplateLayout' => array(
			'varsStatus' => array(
				'flagNaviUse' => 1,
				'flagNaviToolUse' => 1,
				'flagListUse' => 1,
				'flagListToolUse' => 1,
				'flagDetailUse' => 1,
				'flagDetailToolUse' => 1,
			),
			'varsLayout' => array(
				'varsStatus' => array(
					'flagNow' => 3,
					'flagResizeUse' => 1,
					'flagMoveUse' => 1,
					'flagCakeUse' => 1,
					'flagSwitchUse' => 1,
					'flagSwitchNow' => 'Standard',
					'switchList' => array('Standard','Wide','Classic'),
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
						'numPriority' => 2,
						'numSort' => 1,
						'numWidth' => 300,			'numHeight' => 200,
						'numWidthMin' => 200,		'numHeightMin' => 200,
						'numWidthStandard' => 300,	'numHeightStandard' => 200,
						'numWidthWide' => 300,		'numHeightWide' => 300,
						'numWidthClassic' => 300,	'numHeightClassic' => 300,
						'numHeightBox' => 0,		'numWidthBox' => 0,//auto
					),
					array(
						'id' => 'Detail',
						'flagBoxUse' => 1,
						'numPriority' => 1,
						'numSort' => 2,
						'numWidth' => 300,			'numHeight' => 200,
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
										'strTitle' => '選択した検索条件', 'strClass' => 'codeLibBaseImgSheet',
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
										'strTitle' => '選択した検索条件', 'strClass' => 'codeLibBaseImgSheet',
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
							'flagUse' => 1, 'flagNow' => 0,
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
	'child' => array(),
);


