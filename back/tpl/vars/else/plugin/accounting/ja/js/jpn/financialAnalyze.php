<?php

$vars = array(
	'varsItem'  => array(
		'varsOutput' => array(
			'strTitle' => '財務分析',
			'strTitleFile' => '財務分析',
			'strEntityExt' => '事業体(<%replace%>)',
			'strNumExt' => '会期(第<%replace%>期)',
			/*20190401 start*/
		    'strPeriodExt' => '会計期間(自 平成<%strStartHeisei%>年<%strStartMonth%>月1日　至 平成<%strEndHeisei%>年<%strEndMonth%>月末日)',
		    'strPeriodExt20190401' => '会計期間(自 <%strStartNengoYear%>年<%strStartMonth%>月1日　至 <%strEndNengoYear%>年<%strEndMonth%>月末日)',
		    /*20190401 end*/
		),
		'varsRow' => array(
			'numRoa' => 'ROA（総資本利益率）',
			'numRoe' => 'ROE（自己資本利益率）',
			'numRos' => '売上高利益率',
			'numLoop' => '資産回転率',
			'numLeverage' => '財務レバレッジ',
			'numQuick' => '流動比率',
			'numTemp' => '当座比率',
			'numCapital' => '自己資本比率',
			'numFix' => '固定長期適合率',
		),
		'tmplFiscalPeriod' => array(
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
	'pathCss' => '',
	'portal' => array(
		'varsNavi' => array(),
		'varsList' => array(),
		'varsDetail' => array(
			'varsStart' => array(
				'strTitle' => '期間比較',
				'strClass' => 'codeLibBaseImgSheet',
				'varsEdit' => array(
					'flagReloadUse' => 1,
					'flagOutputUse' => 1,
				),
			),
			'varsStatus' => array(
				'flagUse' => 1,
				'flagNow' => 'form',
				'flagOutputUse' => 1,
				'flagOutputNow' => 'spanAll',
				'flagCakeUse' => 1,
				'flagFoldUse' => 0,
				'flagViewUse' => 0,
				'flagFormUse' => 1,
				'switchList' => array('form'),
				'switchOutputList' => array('spanAll'),
			),
			'varsEdit' => array( 'flagReloadUse' => 1, 'flagOutputUse' => 1,),
			'templateDetail' => array(
				array(
					'flagMustUse' => 0,
					'id' => 'FlagFiscalPeriod',
					'strTitle' => '会計期間',
					'strExplain' => '',
					'value' => 'month',
					'flagErrorNow' => 0,
					'arrayError' => array(
						array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
					),
					'flagContentUse' => 0,
					'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
					'numWidth' => 20, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(),
					'varsTmpl' => array(
						'varsPeriod' => array(
							array( 'strTitle' => '年度', 'value' => 'f1', ),
							array( 'strTitle' => '月期', 'value' => 'month', ),
						),
						'arrayOption' => array(
							array( 'strTitle' => '年度', 'value' => 'f1', ),
							array( 'strTitle' => '中間', 'value' => 'f2', ),
							array( 'strTitle' => '四半期', 'value' => 'f4', ),
							array( 'strTitle' => '月期', 'value' => 'month', ),
						),
					),
				),
				array(
					'flagMustUse' => 0,
					'id' => 'Graph',
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
							'numMargin' => 5,
							'numPadding' => 0,
							'numHeight' => 250,
							'numWidth' => '90',
							'unitWidth' => '%',
							'flagChartUse' => 1,
						),
						'varsDetail' => array(
							'varsData' => array(),
							'varsOptions' => array(),
						),
					),
				),
				array(
					'flagMustUse' => 0,
					'id' => 'TableF1',
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
							'numMargin' => 10,
							'numPadding' => 0,
							'numHeight' => 0,
							'numWidth' => '90',
							'unitWidth' => '%',
						),
						'varsDetail' => array(
							'strHtml' => '',
						),
					),
					'tmplTable' => array(
						'varsStatus' => array(
							'flagIdNoneUse' => 1,
							'flagBgUse' => 1,
							'flagOverflowUse' => 1,
							'numFontSize' => 10,
							'varsColumnId' => array(),
							'numWidthTable' => 0,
							'varsColumnWidth' => array(),
						),
						'varsColumn' => array(),
						'varsDetail' => array(),
						'tmplDetail' => array(
							'id' => '',
							'varsDetail' => array(),
						),
						'tmplData' => array(
							'value' => '',
							'strClass' => 'codeLibBaseTableRowRight',
						),
						'numWidth' => 100,
						'numWidthItem' => 100,
						'strClassLeft' => 'codeLibBaseTableColumn',
					),
					'flagHideUse' => 1, 'flagHideNow' => 1,
				),
				array(
					'flagMustUse' => 0,
					'id' => 'TableF2',
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
							'numMargin' => 10,
							'numPadding' => 0,
							'numHeight' => 0,
							'numWidth' => '90',
							'unitWidth' => '%',
						),
						'varsDetail' => array(
							'strHtml' => '',
						),
					),
					'tmplTable' => array(
						'varsStatus' => array(
							'flagIdNoneUse' => 1,
							'flagBgUse' => 1,
							'flagOverflowUse' => 1,
							'numFontSize' => 10,
							'varsColumnId' => array(),
							'numWidthTable' => 0,
							'varsColumnWidth' => array(),
						),
						'varsColumn' => array(),
						'varsDetail' => array(),
						'tmplDetail' => array(
							'id' => '',
							'varsDetail' => array(),
						),
						'tmplData' => array(
							'value' => '',
							'strClass' => 'codeLibBaseTableRowRight',
						),
						'numWidth' => 100,
						'numWidthItem' => 100,
						'strClassLeft' => 'codeLibBaseTableColumn',
					),
					'flagHideUse' => 1, 'flagHideNow' => 1,
				),
				array(
					'flagMustUse' => 0,
					'id' => 'TableF4',
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
							'numMargin' => 10,
							'numPadding' => 0,
							'numHeight' => 0,
							'numWidth' => '90',
							'unitWidth' => '%',
						),
						'varsDetail' => array(
							'strHtml' => '',
						),
					),
					'tmplTable' => array(
						'varsStatus' => array(
							'flagIdNoneUse' => 1,
							'flagBgUse' => 1,
							'flagOverflowUse' => 1,
							'numFontSize' => 10,
							'varsColumnId' => array(),
							'numWidthTable' => 0,
							'varsColumnWidth' => array(),
						),
						'varsColumn' => array(),
						'varsDetail' => array(),
						'tmplDetail' => array(
							'id' => '',
							'varsDetail' => array(),
						),
						'tmplData' => array(
							'value' => '',
							'strClass' => 'codeLibBaseTableRowRight',
						),
						'numWidth' => 100,
						'numWidthItem' => 100,
						'strClassLeft' => 'codeLibBaseTableColumn',
					),
					'flagHideUse' => 1, 'flagHideNow' => 1,
				),
				array(
					'flagMustUse' => 0,
					'id' => 'TableMonth',
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
							'numMargin' => 10,
							'numPadding' => 0,
							'numHeight' => 0,
							'numWidth' => '90',
							'unitWidth' => '%',
						),
						'varsDetail' => array(
							'strHtml' => '',
						),
					),
					'tmplTable' => array(
						'varsStatus' => array(
							'flagIdNoneUse' => 1,
							'flagBgUse' => 1,
							'flagOverflowUse' => 1,
							'numFontSize' => 10,
							'varsColumnId' => array(),
							'numWidthTable' => 0,
							'varsColumnWidth' => array(),
						),
						'varsColumn' => array(),
						'varsDetail' => array(),
						'tmplDetail' => array(
							'id' => '',
							'varsDetail' => array(),
						),
						'tmplData' => array(
							'value' => '',
							'strClass' => 'codeLibBaseTableRowRight',
						),
						'numWidth' => 50,
						'numWidthItem' => 50,
						'strClassLeft' => 'codeLibBaseTableColumn',
					),
					'flagHideUse' => 1, 'flagHideNow' => 0,
				),
			),
			'varsCollect' => array(
				'varsBase' => array(),
				'tmplOptions' => array(
					'legend' => array('show' => true),
					'bars' => array('show' => true),
					'points' => array('show' => false),
					'grid' => array('clickable' => true),
					'yaxis' => array('unit'=> '', 'comma'=> 1),
					'xaxis' => array('min' => 1, 'max' => 13, 'ticks' => array()),
				),
				'varsLabelId' => array('numSales', 'numVariable', 'numMargin', 'numFixed', 'numPoint'),
				'varsLabel' => array('numSales' => '売上高', 'numVariable' => '変動費', 'numMargin' => '限界利益', 'numFixed' => '固定費', 'numPoint' => '損益分岐点売上高'),
				'varsFlagFiscalPeriod' => array(),
				'varsStrFlagFiscalPeriod' => array(),
				'strMonth' => '月',
				'strUnit' => '円',
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
				'varsEdit' => array( 'flagReloadUse' => 1, 'flagOutputUse' => 1,),
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
							'flagNow' => 1,
							'id' => 'Output',
							'strClass' => 'codeLibBtnImgDownload',
							'strClassOver' => 'codeLibBtnImgDownloadOver',
							'strClassNoactive' => 'codeLibBtnImgDownloadNoactive',
							'strTitle' => 'エクスポート',
						),
					),
				),
			),
		),
	),
	'child' => array(),
);


