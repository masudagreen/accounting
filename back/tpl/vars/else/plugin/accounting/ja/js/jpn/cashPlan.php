<?php

$vars = array(
	'numCompare' => 3,
	'varsItem'  => array(
		'strClassNone' => 'codeLibBaseFontCcc',
		'varsOutput' => array(
			'strTitleFile' => '資金繰り分析',
			'strTitle' => '資金繰り分析',
			'strTitlePeriod' => '期間別',
			'strTitleSum' => '収支・差額別',
			'strEntityExt' => '事業体(<%replace%>)',
			'strNumExt' => '会期(第<%replaceStart%>期 ～ 第<%replaceEnd%>期)',
			'strPeriodExt' => '会計期間(自 平成<%strStartHeisei%>年<%strStartMonth%>月1日　至 平成<%strEndHeisei%>年<%strEndMonth%>月末日)',
			'strIdAccountTitleExt' => '収支 相手勘定科目(<%replace%>)',
			'strIdSubAccountTitleExt' => '収支 相手補助科目(<%replace%>)',
			'strNone' => '指定なし',
			'strUnitExt' => '単位(円)',
			'arrAccountTitle' => array( '勘定科目', '収入', '支出', '差額'),
			'arrSubAccountTitle' => array( '補助科目', '収入', '支出', '差額'),
			'strAccountTitle' => '勘定科目',
			'strSubAccountTitle' => '補助科目',
			'arrSubAccountTitleSummary' => array('勘定科目金額'),
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
			'strPeriod' => '期',
		),
	),
	'varsCollect' => array(
		'varsCashValue' => array(),
		'varsBase' => array(),
		'tmplOptions' => array(
			'legend' => array('show' => true),
			'bars' => array('show' => true),
			'points' => array('show' => false),
			'grid' => array('clickable' => true),
			'yaxis' => array('unit'=> '円', 'comma'=> 1),
			'xaxis' => array('min' => 1, 'max' => 13, 'ticks' => array()),
		),
		'varsLabelId' => array('sumIn', 'sumOut', 'sumNet', 'sumNext'),
		'varsLabel' => array('sumIn' => '収入', 'sumOut' => '支出', 'sumNet' => '差額', 'sumNext' => '決済後資金残高'),
		'tmplOptionsCircle' => array(
			'legend' => array('show' => true),
			'grid' => array('clickable' => true),
			'pies' => array('show' => true, 'autoScale' => true, 'unit'=> '円', 'comma'=> 1,),
		),
		'varsPeriod' => array(),
		'arrStrTitle' => array(),
		'arrSelectTag' => array(),
		'varsFlagFiscalPeriod' => array(),
		'varsStrFlagFiscalPeriod' => array(),
		'strMonth' => '月',
		'strUnit' => '円',
		'strPeriod' => '期',
		'strElse' => 'その他',
		'strNone' => '指定なし',
	),
	'pathCss' => '',
	'portal' => array(
		'varsNavi' => array(),
		'varsList' => array(
			'varsStart' => array(
				'strTitle' => '期間比較(未決済収支)',
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
					'value' => 'f1',
					'flagErrorNow' => 0,
					'arrayError' => array(
						array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
					),
					'flagContentUse' => 0,
					'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
					'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(
						array( 'strTitle' => '年度', 'value' => 'f1', ),
						array( 'strTitle' => '中間', 'value' => 'f2', ),
						array( 'strTitle' => '四半期', 'value' => 'f4', ),
						array( 'strTitle' => '月期', 'value' => 'month', ),
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
						'numWidth' => 70,
						'numWidthItem' => 50,
						'strClassLeft' => 'codeLibBaseTableColumn',
					),
					'flagHideUse' => 1, 'flagHideNow' => 0,
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
						'numWidthItem' => 50,
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
						'numWidth' => 70,
						'numWidthItem' => 50,
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
					'flagHideUse' => 1, 'flagHideNow' => 1,
				),
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
		'varsDetail' => array(
			'varsStart' => array(
				'strTitle' => '詳細',
				'strClass' => 'codeLibBaseImgSheet',
			),
			'varsStatus' => array(
				'flagUse' => 1,
				'flagNow' => 'form',
				'flagCakeUse' => 1,
				'flagOutputUse' => 1,
				'flagOutputNow' => 'period',
				'flagFoldUse' => 0,
				'flagViewUse' => 0,
				'flagFormUse' => 1,
				'switchList' => array('form'),
				'switchOutputList' => array('period', 'sum'),
			),
			'varsEdit' => array(
				'flagReloadUse' => 0,
				'flagOutputUse' => 1,
			),
			'templateDetail' => array(
				array(
					'flagMustUse' => 0,
					'id' => 'IdAccountTitle',
					'strTitle' => '収支 - 相手勘定科目',
					'strExplain' => '',
					'value' => 'accountsReceivable',
					'flagErrorNow' => 0,
					'arrayError' => array(),
					'flagContentUse' => 0,
					'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
					'numWidth' => 60, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(),
					'flagFoldUse' => 1, 'flagFoldNow' => 0,
					'flagDisabled' => 0,
					'valueIni' => 'accountsReceivable',
				),
				array(
					'flagMustUse' => 0,
					'id' => 'IdSubAccountTitle',
					'strTitle' => '収支 - 相手補助科目',
					'strExplain' => '',
					'value' => 'none',
					'flagErrorNow' => 0,
					'arrayError' => array(),
					'flagContentUse' => 0,
					'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
					'numWidth' => 60, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(
						array( 'strTitle' => '指定なし', 'value' => 'none', ),
					),
					'flagFoldUse' => 1, 'flagFoldNow' => 0,
					'flagDisabled' => 0,
					'varsTmpl' => array(
						'varsNone' => array( 'strTitle' => '指定なし', 'value' => 'none', ),
					),
				),
				array(
					'flagMustUse' => 0,
					'id' => 'GraphBar',
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
							'strBorderColor' => '',//'#a4a4a4',
							'flagOverflowXUse' => 0,
							'flagOverflowYUse' => 0,
							'numMargin' => 20,
							'numPadding' => 0,
							'numHeight' => 200,
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
					'id' => 'TableDetail',
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
					'tagTr' => '<tr valign="top">#{insertPoint}</tr>',
					'tagTdColumn' => '<td class="codePluginAccountingLibTableColumnMiddle" style="width:#{numWidth}px;"><div style="overflow:hidden;white-space:nowrap;width:#{numWidth}px;" title="#{insertPoint}">#{insertPoint}</div></td>',
					'tagTdRowColumn' => '<td class="codeLibBaseTableColumn"><div style="overflow:hidden;white-space:nowrap;width:50px;">#{insertPoint}</div></td>',
					'tagTdRow' => '<td class="codeLibBaseTableRowRight">#{insertPoint}</td>',
					'tagTable' => '
						<table class="codeLibBaseFontTen" cellspacing="1" cellpadding="3" border="0" bgcolor="#ccc" width="100%">
							<tbody>
								#{insertPoint}
							</tbody>
						</table><br>
					',
				),
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
				'varsEdit' => array(

				),
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
				'flagListUse' => 1,
				'flagListToolUse' => 1,
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
						'id' => 'List',
						'flagBoxUse' => 1,
						'numPriority' => 2,
						'numSort' => 1,
						'numWidth' => 200,			'numHeight' => 200,
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
						'numWidth' => 500,			'numHeight' => 200,
						'numWidthMin' => 200,		'numHeightMin' => 200,
						'numWidthStandard' => 200,	'numHeightStandard' => 200,
						'numWidthWide' => 200,		'numHeightWide' => 200,
						'numWidthClassic' => 200,	'numHeightClassic' => 200,
						'numHeightBox' => 0,		'numWidthBox' => 0,//auto
					),
				),
			),
			'navi' => array(),
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
									'flagNow' => 'period',
								),
								'varsDetail' => array(
									array(
										'id' => 'AccountTitle', 'flagCheckUse' => 1, 'flagCheckNow' => 0,
										'strTitle' => '期間別', 'strClass' => 'codeLibBaseImgSheet',
										'vars' => array( 'idTarget' => 'period',),
										'child' => array(),
									),
									array(
										'id' => 'SubAccountTitle', 'flagCheckUse' => 1, 'flagCheckNow' => 0,
										'strTitle' => '収支・差額別', 'strClass' => 'codeLibBaseImgSheet',
										'vars' => array( 'idTarget' => 'sum',),
										'child' => array(),
									),
								),
							),
						),
					),
				),
			),
		),
	),
	'child' => array(),
);


