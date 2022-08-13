<?php

$vars = array(
	'varsFlag' => array(
		'flagFiscalPeriod' => 'f1',
		'idDepartment'     => 0,
		'flagFS'           => 'PL',
		'flagUnit'         => 0,
		'flagCalc'         => 'floor',
	),
	'varsItem'  => array(
		'arrColumn' => array('項目', '','予算金額', '実績金額', '差額', '達成率(%)'),
		'arrayFS' => array('PL' => '損益科目', 'CR' => '製造原価科目',  ),
		'strEscape' => '、',
		'varsOutput' => array(
			'strTitle' => '予算実績比較表',
			'strTitleFile' => '予算実績比較表',
			'strTitleSub' => '',
			'strBlank' => '　',
			'strEntity' => '',
			'strEntityExt' => '事業体(<%replace%>)',
			'strNumExt' => '会期(第<%replace%>期)',
			/*20190401 start*/
		    'strPeriodExt' => '会計期間(自 平成<%strStartHeisei%>年<%strStartMonth%>月1日　至 平成<%strEndHeisei%>年<%strEndMonth%>月末日)',
		    'strPeriodExt20190401' => '会計期間(自 <%strStartNengoYear%>年<%strStartMonth%>月1日　至 <%strEndNengoYear%>年<%strEndMonth%>月末日)',
		    /*20190401 end*/
			'strDepartmentExt' => '部門(<%replace%>)',
			'strUnitExt' => '単位(円)',
		),
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
						'varsPeriod' => array(
							array( 'strTitle' => '年度予算', 'value' => 'f1', ),
							array( 'strTitle' => '年度予算(月期予算累計)', 'value' => 'msum', ),
						),
						'arrayOption' => array(
							array( 'strTitle' => '年度予算', 'value' => 'f1', ),
							array( 'strTitle' => '年度予算(中間予算累計)', 'value' => 'f2sum', ),
							array( 'strTitle' => '前半期予算', 'value' => 'f21', ),
							array( 'strTitle' => '後半期予算', 'value' => 'f22', ),
							array( 'strTitle' => '年度予算(四半期予算累計)', 'value' => 'f4sum', ),
							array( 'strTitle' => '第1四半期予算', 'value' => 'f41', ),
							array( 'strTitle' => '第2四半期予算', 'value' => 'f42', ),
							array( 'strTitle' => '第3四半期予算', 'value' => 'f43', ),
							array( 'strTitle' => '第4四半期予算', 'value' => 'f44', ),
							array( 'strTitle' => '年度予算(月期予算累計)', 'value' => 'msum', ),
						),
						'strMonth' => '月期予算',
					),
				),
				array(
					'flagMustUse' => 1,
					'id' => 'IdDepartment',
					'strTitle' => '部門',
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
						array( 'strTitle' => '指定なし', 'value' => 0, ),
					),
					'varsTmpl' => array(
						'varsNone' => array( 'strTitle' => '指定なし', 'value' => 0, ),
					),
					'flagHideUse' => 1, 'flagHideNow' => 0,
				),
				array(
					'flagMustUse' => 1,
					'id' => 'FlagFS',
					'strTitle' => '財務諸表科目',
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
						array( 'strTitle' => '損益科目', 'value' => 'PL', ),
						array( 'strTitle' => '製造原価科目', 'value' => 'CR', ),
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
				'varsEdit' => array( 'flagReloadUse' => 0,'flagOutputUse' => 1,'flagEditUse' => 1 ),
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
				'varsEdit' => array( 'flagReloadUse' => 0,'flagOutputUse' => 1,'flagEditUse' => 1 ),
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
					'strTitle' => '予算実績比較表（単位:円）',
					'strClass' => 'codeLibBaseImgFolder',
				),
				'varsEdit' => array(
					'flagReloadUse' => 1,
					'flagOutputUse' => 1,
					'flagEditUse' => 1,
				),
				'varsDetail' => array(),
			),
			'varsStatus' => array(
				'flagNow' => 'tableTree',
				'flagOutputUse' => 1,
				'flagOutputNow' => 'span',
				'flagCakeUse' => 1,
				'flagTableUse' => 0,
				'flagScheduleUse' => 0,
				'flagThumbnailUse' => 0,
				'flagTableTreeUse' => 1,
				'flagTreeUse' => 0,
				'flagBoldUse' => 0,
				'flagFontUse' => 0,
				'flagBgUse' => 0,
				'switchOutputList' => array('span', 'spanAll'),
				'switchList' => array('tableTree'),
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
				'varsHtml' => array(),
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
				'varsEdit' => array(
					'flagReloadUse' => 1,
					'flagOutputUse' => 1,
					'flagEditUse' => 1,
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
							'strTitle' => '勘定体系',
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
							'id' => 'numBudget',
							'strTitle' => '予算金額',
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
						array(
							'flagUse' => 1,
							'flagCheckUse' => 1,
							'flagCheckNow' => 1,
							'id' => 'numNext',
							'strTitle' => '実績金額',
							'numSort' => 2,
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
						array(
							'flagUse' => 1,
							'flagCheckUse' => 1,
							'flagCheckNow' => 1,
							'id' => 'numDiff',
							'strTitle' => '差額',
							'numSort' => 3,
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
						array(
							'flagUse' => 1,
							'flagCheckUse' => 1,
							'flagCheckNow' => 1,
							'id' => 'numRate',
							'strTitle' => '達成率(%)',
							'numSort' => 4,
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
		'varsDetail' => array(
			'varsStatus' => array(
				'flagUse' => 0,
				'flagNow' => '',
				'flagCakeUse' => 1,
				'flagFoldUse' => 1,
				'flagViewUse' => 0,
				'flagFormUse' => 0,
				'flagMoveUse' => 1,
				'switchList' => array(''),
			),
			'templateDetail' => array(
				array(
					'flagMustUse' => 0,
					'id' => 'DummyStatus',
					'strTitle' => '',
					'strExplain' => '',
					'value' => '',
					'flagErrorNow' => 0,
					'arrayError' => array(),
					'flagContentUse' => 0,
					'flagCommentUse' => 1, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 1000,
					'numWidth' => 90, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(),
					'flagFoldUse' => 1, 'flagFoldNow' => 0,
					'varsTmpl' => array(
						'strDepartment' => '部門 : ',
						'strFS' => '財務諸表科目 : ',
						'strNormal' => '<span class="codeLibBaseFontOrange" style="float:none;"><%replace%></span>',
					),
				),
				array(
					'flagMustUse' => 1,
					'id' => 'JsonFiscalPeriod',
					'strTitle' => '予算対象期間', 'strExplain' => '予算を設定したい期間を選択してください。<br>※複数選択可。',
					'value' => 'f1',
					'flagErrorNow' => 0,
					'arrayError' => array(
						array( 'flagCheck' => 'attest', 'flagUse' => 1, 'strComment' => array(
							'strBlank' => '必ず選択する必要があるようです。',
						),),
					),
					'flagContentUse' => 0,
					'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
					'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'flagMultiple' => 1,
					'arrayOption' => array(),
					'varsTmpl' => array(
						'varsPeriod' => array(
							array( 'strTitle' => '年度予算', 'value' => 'f1', ),
							array( 'strTitle' => '月期予算', 'value' => '', 'flagDisabled' => 1),
						),
						'arrayOption' => array(
							array( 'strTitle' => '年度予算', 'value' => 'f1', ),
							array( 'strTitle' => '半期予算', 'value' => '', 'flagDisabled' => 1),
							array( 'strTitle' => '前半期予算', 'value' => 'f21', ),
							array( 'strTitle' => '後半期予算', 'value' => 'f22', ),
							array( 'strTitle' => '四半期予算', 'value' => '', 'flagDisabled' => 1),
							array( 'strTitle' => '第1四半期予算', 'value' => 'f41', ),
							array( 'strTitle' => '第2四半期予算', 'value' => 'f42', ),
							array( 'strTitle' => '第3四半期予算', 'value' => 'f43', ),
							array( 'strTitle' => '第4四半期予算', 'value' => 'f44', ),
							array( 'strTitle' => '月期予算', 'value' => '', 'flagDisabled' => 1),
						),
						'strMonth' => '月期予算',
					),
					'numSize' => 5,
				),
				array(
					'flagMustUse' => 1,
					'id' => 'FlagSplit',
					'strTitle' => '年間予算均等分割', 'strExplain' => '年間予算を均等分割した値を全予算期間に設定できます。<br>※端数各期切り捨て',
					'value' => 0,
					'flagErrorNow' => 0,
					'arrayError' => array(
						array( 'flagCheck' => 'attest', 'flagUse' => 1, 'strComment' => array(
							'strBlank' => '必ず選択する必要があるようです。',
						),),
					),
					'flagContentUse' => 0,
					'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
					'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'flagMultiple' => 0,
					'arrayOption' => array(),
					'varsTmpl' => array(
						'arrayOption' => array(
							array( 'strTitle' => '均等分割する', 'value' => 1,),
							array( 'strTitle' => '均等分割しない', 'value' => 0, ),
						),
					),
					'flagHideUse' => 1,
					'flagHideNow' => 0,
				),
				array(
					'flagMustUse' => 1,
					'id' => 'JsonData',
					'strTitle' => '予算設定', 'strExplain' => '', 'value' => 'dummy',
					'flagErrorNow' => 0,
					'arrayError' => array(
						array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ),),
						array( 'flagCheck' => 'attest', 'flagUse' => 1, 'strComment' => array(
							'strFS' => '保存されていた財務諸表科目が表示されている財務諸表科目と異なるため挿入できないようです。',
						),),
					),
					'flagContentUse' => 1,
					'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 0,
					'numWidth' => 90, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(),
					'varsFormSensitive' => array(
						'varsStatus' => array(
							'id' => 'Sensitive',
							'numLeft' => 0,
							'numTop' => 0,
						),
						'varsHtml' => array(),
						'varsDetail' => array(),
						'varsTmpl' => array(
							'varsDetail' => array(),
							'tmplTable' => '',
							'tmplTableItem' => '',
							'tmplDetail' => array(
								'flagMustUse' => 0,
								'id' => '',
								'strTitle' => '',
								'strExplain' => '',
								'value' => '',
								'flagErrorNow' => 0,
								'arrayError' => array(
									array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ),),
									array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字で記入する必要があるようです。',  ), ),
									array( 'flagCheck' => 'max', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'num', 'num' => 999999999999, 'strComment' => array( 'common' => '入力文字数が多すぎるようです。', ),),
								),
								'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 12,
								'numWidth' => 40, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => '',
								'arrayOption' => array(),
								'flagForm' => 'active',
							),
							'varsFormTemp' => array(
								'varsStatus' => array(
									'numLeft' => 0,
									'numTop' => 0,
								),
								'varsDetail' => array(
									'flagTag'       => '',
									'flagInputType' => '',
									'numMaxlength'  => 12,
									'numWidth'      => 0,
									'unitWidth'     => 'px',
									'numHeight'     => 0,
									'unitHeight'    => 'px',
									'arrayOption'   => array(),
									'value'         => '',
									'vars'          => array(),
								),
							),
						),
					),
					'varsStr' => array(
						'strTree' => '勘定体系',
						'strBudget' => '予算金額'
					),
				),
			),
			'varsPage' => array(),
			'varsDetail' => array(),
			'varsBtn' => array(),
			'varsStart' => array(),
			'view' => array(),
			'form' => array(),
		),
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
						'numWidth' => 250,			'numHeight' => 200,
						'numWidthMin' => 150,		'numHeightMin' => 200,
						'numWidthStandard' => 150,	'numHeightStandard' => 200,
						'numWidthWide' => 150,		'numHeightWide' => 200,
						'numWidthClassic' => 150,	'numHeightClassic' => 200,
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
							'flagUse' => 1, 'flagNow' => 0,
							'id' => 'Edit',
							'strClass' => 'codeLibBtnImgEdit',
							'strClassOver' => 'codeLibBtnImgEditOver',
							'strClassNoactive' => 'codeLibBtnImgEditNoactive',
							'strTitle' => '予算設定',
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
									'flagNow' => 'span',
								),
								'varsDetail' => array(
									array(
										'id' => 'Span', 'flagCheckUse' => 1, 'flagCheckNow' => 0,
										'strTitle' => '選択した検索条件', 'strClass' => 'codeLibBaseImgSheet',
										'vars' => array( 'idTarget' => 'span',),
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
					),
				),
			),
			'detail' => array(),
		),
	),
	'child' => array(
		'varsTitle' => array(
			'editor' => 'エディタ',
		),
		'editor' => array(
			'id' => '',
			'strTitle' => '<%child%> - <%parent%>',
			'strClass' => 'codePluginAccountingImgIcon',
			'flagLockUse' => 0,
			'flagLockNow' => '',
			'flagCakeUse' => 1,
			'flagRemoveUse' => 0,
			'flagCoverUse' => 1,
			'flagHideUse' => 1,
			'flagHideNow' => 0,
			'flagReloadUse' => 0,
			'flagFoldUse' => 1,
			'flagFoldNow' => 0,
			'flagMoveUse' => 1,
			'flagZIndexUse' => 0,
			'flagResizeUse' => 1,
			'flagResizeIni' => 'all',
			'flagResizeNow' => 'all',
			'flagSkeletonUse' => 0,
			'flagBootUse' => 'auto',
			'flagSwitchUse' => 0,
			'flagMenuUse' => 1,
			'flagMenuShowUse' => 0,
			'numWidthTitle' => 0,
			'numLeft' => 50,
			'numTop' => 50,
			'numWidth' => 500,
			'numHeight' => 600,//180 285
			'numWidthMin' => 500,
			'numHeightMin' => 600,
			'numZIndex' => 0
		),
		'templateWindow' => array(
			'id' => '',
			'strTitle' => '<%child%> - <%parent%>',
			'strClass' => 'codePluginAccountingImgIcon',
			'flagLockUse' => 0,
			'flagLockNow' => '',
			'flagCakeUse' => 1,
			'flagRemoveUse' => 0,
			'flagCoverUse' => 1,
			'flagHideUse' => 1,
			'flagHideNow' => 0,
			'flagReloadUse' => 0,
			'flagFoldUse' => 1,
			'flagFoldNow' => 0,
			'flagMoveUse' => 1,
			'flagZIndexUse' => 0,
			'flagResizeUse' => 1,
			'flagResizeIni' => 'all',
			'flagResizeNow' => 'all',
			'flagSkeletonUse' => 0,
			'flagBootUse' => 'auto',
			'flagSwitchUse' => 1,
			'flagMenuUse' => 1,
			'flagMenuShowUse' => 0,
			'numWidthTitle' => 0,
			'numLeft' => 50,
			'numTop' => 50,
			'numWidth' => 800,
			'numHeight' => 600,//180 285
			'numWidthMin' => 800,
			'numHeightMin' => 600,
			'numZIndex' => 0
		),
	),
);


