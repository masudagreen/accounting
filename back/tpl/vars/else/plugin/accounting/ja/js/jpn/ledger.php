<?php

$vars = array(
	'pathCss' => '',
	'flagAuthorityLog' => 0,
	'varsFlag' => array(
		'numLotNow'         => 0,
		'flagFiscalPeriod'  => 'f1',
		'idDepartment'      => 'none',
		'flagFS'            => 'BS',
		'idAccountTitle'    => 'cash',
		'idSubAccountTitle' => 'none',
	),
	'varsItem'  => array(
		'arrayFS' => array( 'BS' => '貸借科目', 'PL' => '損益科目', 'CR' => '製造原価科目',  ),
		'strSundries' => '諸口',
		'strPrev' => '繰越金額',
		'strClassFont' => 'codeLibBaseFontCcc',
		'strClass' => 'codeLibBaseImgBlank',
		'strPrevTerm' => '前期繰越',
		'strNextTerm' => '次期繰越',
		'strPrevMonth' => '前月繰越',
		'strFlagFiscalReport1' => '年度決算整理仕訳',
		'strFlagFiscalReport2' => '中間決算整理仕訳',
		/*
		 * 20191001 start
		 */
	    'strMarkReduced' => '※',
	    /*
	     * 20191001 end
	     */

		'varsOutput' => array(
			'strEntityExt' => '事業体(<%replace%>)',
			'strNumExt' => '会期(第<%replace%>期)',
		    /*20190401 start*/
			'strPeriodExt' => '会計期間(自 平成<%strStartHeisei%>年<%strStartMonth%>月1日　至 平成<%strEndHeisei%>年<%strEndMonth%>月末日)',
		    'strPeriodExt20190401' => '会計期間(自 <%strStartNengoYear%>年<%strStartMonth%>月1日　至 <%strEndNengoYear%>年<%strEndMonth%>月末日)',
		    /*20190401 end*/
			'strDepartmentExt' => '部門(<%replace%>)',
			'strUnit' => '単位(円)',
			'strAccountTitleExt' => '勘定(<%replace%>)',
			'strSubAccountTitleExt' => '補助科目(<%replace%>)',
			'strBlank'   => '　',
			'strTitleMenu' => '元帳',
			'strTitleSubMenu' => '補助元帳',
			'strTitle' => '',
			'strTitleSub' => '',
			'strEntity' => '',
			'strNum'    => '第<%replace%>期',
			'strDepartmentColumn' => '部門',
			'strSubAccountTitleColumn' => '補助科目',
			'strAccountTitleColumn' => '勘定科目',
			'strDepartment' => '',
			'strSubAccountTitle' => '',
			'strAccountTitle' => '',
			'strDate' => '日付',
			'strFiscalReport' => '決整',
			'strFiscalReport1' => '年決',
			'strFiscalReport2' => '中決',
			'strId' => '仕番',
			'strMemo' => '摘要',
			'strContra' => '相手',
			'strDebit' => '借方',
			'strCredit' => '貸方',
			'strBalance' => '残高',
			/*
			 * 20191001 start
			 */
		    'strReduced' => '※軽減税率対象品目',
		    /*
		     * 20191001 end
		     */

		),
	),
	'varsRule' => array(
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
			'numTr' => 3,
		),
		'varsDetail' => array(),
	),
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
					'id' => 'IdAccountTitle',
					'strTitle' => '勘定科目',
					'strExplain' => '',
					'value' => 'dummy',
					'flagErrorNow' => 0,
					'arrayError' => array(
						array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
					),
					'flagContentUse' => 0,
					'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
					'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(),
					'varsTmpl' => array(),
				),
				array(
					'flagMustUse' => 1,
					'id' => 'IdSubAccountTitle',
					'strTitle' => '補助科目',
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
				'varsEdit' => array( 'flagReloadUse' => 0, ),
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
				'varsEdit' => array( 'flagReloadUse' => 0,),
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
					'strTitle' => 'リスト',
					'strClass' => 'codeLibBaseImgFolder',
				),
				'varsEdit' => array(
					'flagReloadUse' => 1,
					'flagSwitchUse' => 1,
					'flagOutputUse' => 1,
					'flagPrintUse' => 1,
				),
				'varsDetail' => array(),
			),
			'varsStatus' => array(
				'flagNow' => 'table',
				'flagPrintUse' => 1,
				'flagPrintNow' => 'search',
				'flagOutputUse' => 1,
				'flagOutputNow' => 'search',
				'flagCakeUse' => 1,
				'flagTableUse' => 1,
				'flagScheduleUse' => 1,
				'flagThumbnailUse' => 0,
				'flagTreeUse' => 0,
				'flagBoldUse' => 0,
				'flagFontUse' => 0,
				'flagBgUse' => 0,
				'switchList' => array('table','schedule'),
				'switchOutputList' => array('search', 'accountTitle', 'subAccountTitle'),
				'switchPrintList' => array('search', 'accountTitle', 'subAccountTitle'),
			),
			'templateDetail' => array(
				'flagBtnUse' => 1,
				'flagMoveUse' => 0,
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
				'vars' => array(
					'idTarget' => '0',
				),
				'varsColumnDetail' => array(
					'id' => '',
					'strTitle' => '',
					'stampBook' => '',
					'stampRegister' => '',
					'flagDebit' => '',
					'flagCredit' => '',
					'numBalance' => '',
					'flagFiscalReport' => '',
					/*
					 * 20191001 start
					 */
				    'flagRateConsumptionTaxReduced' => '',
				    /*
				     * 20191001 end
				     */
					'idLog' => '',
					'idDepartment' => '',
					'idSubAccountTitle' => '',
					'idDepartmentContra' => '',
					'idAccountTitleContra' => '',
					'idSubAccountTitleContra' => '',
				),
				'varsScheduleDetail' => array(
					'flagType' => 'stamp',//stamp,term,loop
					'flagResizeUse' => 1,
					'strTitle' => '',
					'stamp' => 0,
					'term' => array(),
					'loop' => array(),
				),
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
			'varsEdit' => array(
				'flagReloadUse' => 1,
				'flagSwitchUse' => 1,
				'flagOutputUse' => 1,
				'flagPrintUse' => 1,
			),
			'varsDetail' => array(),
			'varsBtn' => array(),
			'table' => array(
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
				'varsEdit' => array(
					'flagReloadUse' => 1,
					'flagSwitchUse' => 1,
					'flagOutputUse' => 1,
					'flagPrintUse' => 1,
				),
				'varsDetail' => array(
					'varsStatus' => array(
						'flagMenuUse' => 1,
						'flagColumnUse' => 0,
						'flagResizeUse' => 1,
						'flagSortColumnUse' => 1,
						'flagSortColumnLineUse' => 0,
						'flagSortColumnLineNow' => '',
						'flagMoveUse' => 1,
						'flagMoveSortUse' => 0,
						'flagCakeUse' => 1,
						'flagPageUse' => 1,
						'flagInnerPageUse' => 1,
						'flagBtnBottomUse' => 1,
						'flagBtnUse' => 0,
						'flagKeyBtnUse' => 0,
						'flagBgUse' => 1,
						'flagFontUse' => 1,
						'flagBoldUse' => 0,
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
					'varsPage' => array(),
					'varsHtml' => array(),
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
							'flagCheckUse' => 1,
							'flagCheckNow' => 1,
							'id' => 'StampBook',
							'flagSortColumnLineUse' => 0,
							'flagSortColumnLineNow' => 0,
							'strTitle' => '取引日時',
							'numSort' => 1,
							'numWidth' => 100,
							'numWidthMin' => 26,
							'flagType' => 'stamp',
							'flagTimeType' => '1',
							'flagAllCheckbox' => 0,
							'flagAllCheckboxNow' => 0,
							'vars' => array(),
							'child' => array(),
						),
						array(
							'flagUse' => 1,
							'flagCheckUse' => 1,
							'flagCheckNow' => 1,
							'id' => 'FlagFiscalReport',
							'flagSortColumnLineUse' => 0,
							'flagSortColumnLineNow' => 0,
							'strTitle' => '決算整理仕訳',
							'numSort' => 2,
							'numWidth' => 50,
							'numWidthMin' => 26,
							'flagType' => 'str',
							'flagTimeType' => '',
							'flagAllCheckbox' => 0,
							'flagAllCheckboxNow' => 0,
							'vars' => array(),
							'child' => array(),
						),
						array(
							'flagUse' => 1,
							'flagCheckUse' => 1,
							'flagCheckNow' => 1,
							'id' => 'IdAccountTitleContra',
							'flagSortColumnLineUse' => 0,
							'flagSortColumnLineNow' => 0,
							'strTitle' => '相手勘定科目',
							'numSort' => 3,
							'numWidth' => 80,
							'numWidthMin' => 26,
							'flagType' => 'str',
							'flagTimeType' => '',
							'flagAllCheckbox' => 0,
							'flagAllCheckboxNow' => 0,
							'vars' => array(),
							'child' => array(),
						),
						array(
							'flagUse' => 1,
							'flagCheckUse' => 1,
							'flagCheckNow' => 1,
							'id' => 'IdDepartmentContra',
							'flagSortColumnLineUse' => 0,
							'flagSortColumnLineNow' => 0,
							'strTitle' => '相手部門',
							'numSort' => 4,
							'numWidth' => 80,
							'numWidthMin' => 26,
							'flagType' => 'str',
							'flagTimeType' => '',
							'flagAllCheckbox' => 0,
							'flagAllCheckboxNow' => 0,
							'vars' => array(),
							'child' => array(),
						),
						array(
							'flagUse' => 1,
							'flagCheckUse' => 1,
							'flagCheckNow' => 1,
							'id' => 'IdSubAccountTitleContra',
							'flagSortColumnLineUse' => 0,
							'flagSortColumnLineNow' => 0,
							'strTitle' => '相手補助科目',
							'numSort' => 5,
							'numWidth' => 80,
							'numWidthMin' => 26,
							'flagType' => 'str',
							'flagTimeType' => '',
							'flagAllCheckbox' => 0,
							'flagAllCheckboxNow' => 0,
							'vars' => array(),
							'child' => array(),
						),
						/*
						 * 20191001 start
						 */
					    array(
						    'flagUse' => 1,
						    'flagCheckUse' => 1,
						    'flagCheckNow' => 1,
						    'id' => 'FlagRateConsumptionTaxReduced',
						    'flagSortColumnLineUse' => 0,
						    'flagSortColumnLineNow' => 0,
						    'strTitle' => '※軽減税率対象品目',
						    'numSort' => 6,
						    'numWidth' => 50,
						    'numWidthMin' => 26,
						    'flagType' => 'str',
						    'flagTimeType' => '',
						    'flagAllCheckbox' => 0,
						    'flagAllCheckboxNow' => 0,
						    'vars' => array(),
						    'child' => array(),
						),
						/*
						 * 20191001 end
						 */
						array(
							'flagUse' => 1,
							'flagCheckUse' => 1,
							'flagCheckNow' => 1,
							'id' => 'StrTitle',
							'flagSortColumnLineUse' => 0,
							'flagSortColumnLineNow' => 0,
							'strTitle' => '摘要',
							'numSort' => 7,
							'numWidth' => 100,
							'numWidthMin' => 26,
							'flagType' => 'str',
							'flagTimeType' => '',
							'flagAllCheckbox' => 0,
							'flagAllCheckboxNow' => 0,
							'vars' => array(),
							'child' => array(),
						),
						array(
							'flagUse' => 1,
							'flagCheckUse' => 1,
							'flagCheckNow' => 1,
							'id' => 'IdDepartment',
							'flagSortColumnLineUse' => 0,
							'flagSortColumnLineNow' => 0,
							'strTitle' => '部門',
							'numSort' => 8,
							'numWidth' => 80,
							'numWidthMin' => 26,
							'flagType' => 'str',
							'flagTimeType' => '',
							'flagAllCheckbox' => 0,
							'flagAllCheckboxNow' => 0,
							'vars' => array(),
							'child' => array(),
						),
						array(
							'flagUse' => 1,
							'flagCheckUse' => 1,
							'flagCheckNow' => 1,
							'id' => 'IdSubAccountTitle',
							'flagSortColumnLineUse' => 0,
							'flagSortColumnLineNow' => 0,
							'strTitle' => '補助科目',
							'numSort' => 9,
							'numWidth' => 80,
							'numWidthMin' => 26,
							'flagType' => 'str',
							'flagTimeType' => '',
							'flagAllCheckbox' => 0,
							'flagAllCheckboxNow' => 0,
							'vars' => array(),
							'child' => array(),
						),
						array(
							'flagUse' => 1,
							'flagCheckUse' => 1,
							'flagCheckNow' => 1,
							'id' => 'FlagDebit',
							'flagSortColumnLineUse' => 0,
							'flagSortColumnLineNow' => 0,
							'strTitle' => '借方',
							'numSort' => 10,
							'numWidth' => 80,
							'numWidthMin' => 26,
							'flagType' => 'str',
							'flagAlign' => 'right',
							'flagAllCheckbox' => 0,
							'flagAllCheckboxNow' => 0,
							'vars' => array(),
							'child' => array(),
						),
						array(
							'flagUse' => 1,
							'flagCheckUse' => 1,
							'flagCheckNow' => 1,
							'id' => 'FlagCredit',
							'flagSortColumnLineUse' => 0,
							'flagSortColumnLineNow' => 0,
							'strTitle' => '貸方',
							'numSort' => 11,
							'numWidth' => 80,
							'numWidthMin' => 26,
							'flagType' => 'str',
							'flagAlign' => 'right',
							'flagTimeType' => '',
							'flagAllCheckbox' => 0,
							'flagAllCheckboxNow' => 0,
							'vars' => array(),
							'child' => array(),
						),
						array(
							'flagUse' => 1,
							'flagCheckUse' => 1,
							'flagCheckNow' => 1,
							'id' => 'NumBalance',
							'flagSortColumnLineUse' => 0,
							'flagSortColumnLineNow' => 0,
							'strTitle' => '残高',
							'numSort' => 12,
							'numWidth' => 80,
							'numWidthMin' => 26,
							'flagType' => 'str',
							'flagAlign' => 'right',
							'flagTimeType' => '',
							'flagAllCheckbox' => 0,
							'flagAllCheckboxNow' => 0,
							'vars' => array(),
							'child' => array(),
						),
						array(
							'flagUse' => 1,
							'flagCheckUse' => 1,
							'flagCheckNow' => 1,
							'id' => 'IdLog',
							'flagSortColumnLineUse' => 0,
							'flagSortColumnLineNow' => 0,
							'strTitle' => '仕訳通番',
							'numSort' => 13,
							'numWidth' => 50,
							'numWidthMin' => 26,
							'flagType' => 'str',
							'flagTimeType' => '',
							'flagAllCheckbox' => 0,
							'flagAllCheckboxNow' => 0,
							'vars' => array(),
							'child' => array(),
						),


					),
					'varsDetail' => array(),
				),
			),
			'schedule' => array(
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
				'varsEdit' => array(
					'flagReloadUse' => 1,
					'flagSwitchUse' => 1,
					'flagOutputUse' => 1,
					'flagPrintUse' => 1,
				),
				'varsDetail' => array(
					'varsStatus' => array(
						'flagNow' => 'month',// 'month','week'
						'flagFoldUse' => 1,
						'flagBgUse' => 1,
						'flagFontUse' => 1,
						'flagBoldUse' => 0,
						'flagMoveUse' => 1,
						'flagMoveRangeUse' => 0,
						'flagResizeUse' => 0,
						'flagDateEventUse' => 0,
						'flagMaxUse' => 0,
						'flagMaxAutoUse' => 0,
						'stampMax' => 0,
						'flagMainUse' => 1,
						'flagMainAutoUse' => 1,
						'stampMain' => 0,
						'flagMinUse' => 0,
						'flagMinAutoUse' => 0,
						'stampMin' => 0,
						'flagBtnBottomUse' => 0,
						'flagPageUse' => 1,
						'flagInnerPageUse' => 1,
					),
					'varsFormat' => array(
						'id' => '',
						'flagType' => 'scheduleFormat',
						'numHeight' => 0,
						'numWidth' => 0,
						'flagFooderUse' => 1,
					),
					'varsBtn' => array(),
					'varsPage' => array(),
					'varsDetail' => array(),
					'month' => array(
						'varsFold' => array(
							array( 'flagFoldNow' => 1,),array( 'flagFoldNow' => 1,),array( 'flagFoldNow' => 1,),array( 'flagFoldNow' => 1,),array( 'flagFoldNow' => 1,),array( 'flagFoldNow' => 1,),
						),
					),
					'week' => array(
						'varsFold' => array(
							array( 'flagFoldNow' => 1,),array( 'flagFoldNow' => 1,),array( 'flagFoldNow' => 1,),array( 'flagFoldNow' => 1,),array( 'flagFoldNow' => 1,),array( 'flagFoldNow' => 1,),array( 'flagFoldNow' => 1,),
						),
					),
				),
			),
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
						'numWidth' => 250,			'numHeight' => 200,
						'numWidthMin' => 200,		'numHeightMin' => 200,
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
							'flagUse' => 1,
							'flagNow' => 1,
							'id' => 'Switch',
							'strClass' => 'codeLibBtnImgSwitch',
							'strClassOver' => 'codeLibBtnImgSwitchOver',
							'strClassNoactive' => 'codeLibBtnImgSwitchNoactive',
							'strTitle' => '表示切替',
							'varsContext' => array(
								'varsStatus' => array(
									'numTop' => 0,
									'numLeft' => 0,
									'flagNow' => 'table',
								),
								'varsDetail' => array(
									array(
										'id' => 'Table', 'flagCheckUse' => 1, 'flagCheckNow' => 0,
										'strTitle' => 'テーブル形式', 'strClass' => 'codeLibBaseImgSheet',
										'vars' => array( 'idTarget' => 'table',),
										'child' => array(),
									),
									array(
										'id' => 'Schedule', 'flagCheckUse' => 1, 'flagCheckNow' => 0,
										'strTitle' => 'カレンダー形式', 'strClass' => 'codeLibBaseImgSheet',
										'vars' => array( 'idTarget' => 'schedule',),
										'child' => array(),
									),
								),
							),
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
									'flagNow' => 'search',
								),
								'varsDetail' => array(
									array(
										'id' => 'Search', 'flagCheckUse' => 1, 'flagCheckNow' => 0,
										'strTitle' => '選択した検索条件', 'strClass' => 'codeLibBaseImgSheet',
										'vars' => array( 'idTarget' => 'search',),
										'child' => array(),
									),
									array(
										'id' => 'AccountTitle', 'flagCheckUse' => 1, 'flagCheckNow' => 0,
										'strTitle' => '全ての勘定科目', 'strClass' => 'codeLibBaseImgSheet',
										'vars' => array( 'idTarget' => 'accountTitle',),
										'child' => array(),
									),
									array(
										'id' => 'SubAccountTitle', 'flagCheckUse' => 1, 'flagCheckNow' => 0,
										'strTitle' => '全ての補助科目', 'strClass' => 'codeLibBaseImgSheet',
										'vars' => array( 'idTarget' => 'subAccountTitle',),
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
									'flagNow' => 'search',
								),
								'varsDetail' => array(
									array(
										'id' => 'Search', 'flagCheckUse' => 1, 'flagCheckNow' => 0,
										'strTitle' => '選択した検索条件', 'strClass' => 'codeLibBaseImgSheet',
										'vars' => array( 'idTarget' => 'search',),
										'child' => array(),
									),
									array(
										'id' => 'AccountTitle', 'flagCheckUse' => 1, 'flagCheckNow' => 0,
										'strTitle' => '全ての勘定科目', 'strClass' => 'codeLibBaseImgSheet',
										'vars' => array( 'idTarget' => 'accountTitle',),
										'child' => array(),
									),
									array(
										'id' => 'SubAccountTitle', 'flagCheckUse' => 1, 'flagCheckNow' => 0,
										'strTitle' => '全ての補助科目', 'strClass' => 'codeLibBaseImgSheet',
										'vars' => array( 'idTarget' => 'subAccountTitle',),
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


