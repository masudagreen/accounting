<?php

$vars = array(
	'flagFS'  => 'PL',
	'flagDirect' => 1,
	'varsItem'  => array(
		'strClassNone' => 'codeLibBaseFontCcc',
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
					'id' => 'FlagAccountTitle',
					'strTitle' => '勘定科目選択',
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
						array( 'strTitle' => '貸借科目', 'value' => 'BS', ),
						array( 'strTitle' => '損益科目', 'value' => 'PL', ),
						array( 'strTitle' => '製造原価科目', 'value' => 'CR', ),
					),
					'numSize' => 3,
				),
				array(
					'flagMustUse' => 1,
					'id' => 'FlagDirect',
					'strTitle' => '表示方法',
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
						array( 'strTitle' => '直接法', 'value' => 1, ),
						array( 'strTitle' => '間接法', 'value' => 0, ),
					),
					'numSize' => 2,
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
					'strTitle' => '勘定科目(CS)',
					'strClass' => 'codeLibBaseImgFolder',
				),
				'varsEdit' => array(
					'flagReloadUse' => 1,
				),
				'varsDetail' => array(),
			),
			'varsStatus' => array(
				'flagNow' => 'tableTree',
				'flagCakeUse' => 0,
				'flagTableUse' => 0,
				'flagScheduleUse' => 0,
				'flagThumbnailUse' => 0,
				'flagTableTreeUse' => 1,
				'flagTreeUse' => 0,
				'flagBoldUse' => 0,
				'flagFontUse' => 0,
				'flagBgUse' => 0,
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
					'strTitle' => '',
					'stampRegister' => 0,
					'stampUpdate' => 0,
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
				'varsPage' => array(),
				'varsHtml' => array(),
				'varsEdit' => array(
					'flagReloadUse' => 1,
				),
				'varsDetail' => array(
					'varsStatus' => array(
						'flagMenuUse' => 1,
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

						'flagHeaderLeftWidth' => 1,
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
							'id' => 'FlagPlus',
							'strTitle' => '【加算】決算項目-集計対象',
							'numSort' => 1,
							'numWidth' => 300,
							'numWidthMin' => 26,
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
							'id' => 'FlagMinus',
							'strTitle' => '【減算】決算項目-集計対象',
							'numSort' => 2,
							'numWidth' => 300,
							'numWidthMin' => 26,
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
					'id' => 'DummyFlagPlus',
					'strTitle' => '【加算】決算項目-集計対象',
					'strExplain' => '',
					'value' => '',
					'flagErrorNow' => 0,
					'arrayError' => array(),
					'flagContentUse' => 0,
					'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 100,
					'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(), 
					'flagFoldUse' => 1, 'flagFoldNow' => 0,
				),
				array(
					'flagMustUse' => 0,
					'id' => 'DummyFlagMinus',
					'strTitle' => '【減算】決算項目-集計対象',
					'strExplain' => '',
					'value' => '',
					'flagErrorNow' => 0,
					'arrayError' => array(),
					'flagContentUse' => 0,
					'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 100,
					'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(), 
					'flagFoldUse' => 1, 'flagFoldNow' => 0,
				),
				array(
					'flagMustUse' => 0,
					'id' => 'DummyComment',
					'strTitle' => '',
					'strExplain' => '',
					'value' => '',
					'flagErrorNow' => 0,
					'arrayError' => array(),
					'flagContentUse' => 0,
					'flagCommentUse' => 1, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 100,
					'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(), 
					'flagFoldUse' => 1, 'flagFoldNow' => 0,
					'varsTmpl' => array(
						'strNormal' => '<span class="codeLibBaseFontOrange" style="float:none;"><%replace%></span>',
					),
				),
				array(
					'flagMustUse' => 0,
					'id' => 'StrTitle',
					'strTitle' => '',
					'strExplain' => '',
					'value' => '',
					'flagErrorNow' => 0,
					'arrayError' => array(),
					'flagContentUse' => 0,
					'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
					'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(),
					'flagFoldUse' => 1, 'flagFoldNow' => 0,
					'flagHideUse' => 1, 'flagHideNow' => 1,
				),
				array(
					'flagMustUse' => 1,
					'id' => 'IdAccountTitlePlus',
					'strTitle' => '【加算】決算項目',
					'strExplain' => '',
					'value' => '',
					'flagErrorNow' => 0,
					'arrayError' => array(
						array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
						array( 'flagCheck' => 'attest', 'flagUse' => 1, 'strComment' => array('cashPlus' => '【減算】決算項目で現金及び現金同等物を選択した場合、【加算】決算項目も現金及び現金同等物でないといけないようです。',),),
					),
					'flagContentUse' => 0,
					'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
					'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(),
					'flagFoldUse' => 1, 'flagFoldNow' => 0,
					'flagHideUse' => 1, 'flagHideNow' => 0,
					'flagDisabled' => 0,
					'varsTmpl' => array(
						'strNormal' => '加算集計する決算項目を設定してください。',
						'strLog' => '<span class="codeLibBaseFontOrange" style="float:none;">※ 設定値が既にシステム上で使用されているため変更できません。</span><br>現在選択しているのは、『 <%replace%> 』です。',
						'strLogTemp' => '<span class="codeLibBaseFontOrange" style="float:none;">※ 仮繰越処理中の繰越残高が確定するまで変更できません。</span><br>現在選択しているのは、『 <%replace%> 』です。',
					),
				),
				array(
					'flagMustUse' => 1,
					'id' => 'FlagMethodPlus',
					'strTitle' => '【加算】集計対象',
					'strExplain' => '',
					'value' => '',
					'flagErrorNow' => 0,
					'arrayError' => array(
						array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
						array( 'flagCheck' => 'attest', 'flagUse' => 1, 'strComment' => array('netPlus' => '【減算】の決算項目で対象外を選んだ場合、【加算】決算項目は、貸借差額でなければならないようです。',),),


					),
					'flagContentUse' => 0,
					'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
					'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(),
					'flagFoldUse' => 1, 'flagFoldNow' => 0,
					'flagHideUse' => 1, 'flagHideNow' => 0,
					'flagDisabled' => 0,
					'varsTmpl' => array(
						'strNormal' => '加算集計する対象を設定してください。',
						'strLog' => '<span class="codeLibBaseFontOrange" style="float:none;">※ 設定値が既にシステム上で使用されているため変更できません。</span><br>現在選択しているのは、『 <%replace%> 』です。',
						'strLogTemp' => '<span class="codeLibBaseFontOrange" style="float:none;">※ 仮繰越処理中の繰越残高が確定するまで変更できません。</span><br>現在選択しているのは、『 <%replace%> 』です。',
					),
				),
				array(
					'flagMustUse' => 1,
					'id' => 'IdAccountTitleMinus',
					'strTitle' => '【減算】決算項目',
					'strExplain' => '',
					'value' => '',
					'flagErrorNow' => 0,
					'arrayError' => array(
						array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
						array( 'flagCheck' => 'attest', 'flagUse' => 1, 'strComment' => array('cashMinus' => '【加算】決算項目で現金及び現金同等物を選択した場合、【減算】決算項目も現金及び現金同等物でないといけないようです。',),),

					),
					'flagContentUse' => 0,
					'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
					'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(),
					'flagFoldUse' => 1, 'flagFoldNow' => 0,
					'flagHideUse' => 1, 'flagHideNow' => 0,
					'flagDisabled' => 0,
					'varsTmpl' => array(
						'strNormal' => '減算集計する決算項目を設定してください。',
						'strLog' => '<span class="codeLibBaseFontOrange" style="float:none;">※ 設定値が既にシステム上で使用されているため変更できません。</span><br>現在選択しているのは、『 <%replace%> 』です。',
						'strLogTemp' => '<span class="codeLibBaseFontOrange" style="float:none;">※ 仮繰越処理中の繰越残高が確定するまで変更できません。</span><br>現在選択しているのは、『 <%replace%> 』です。',
					),
				),
				array(
					'flagMustUse' => 1,
					'id' => 'FlagMethodMinus',
					'strTitle' => '【減算】集計対象',
					'strExplain' => '',
					'value' => '',
					'flagErrorNow' => 0,
					'arrayError' => array(
						array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
						array( 'flagCheck' => 'attest', 'flagUse' => 1, 'strComment' => array('netMinus' => '【加算】決算項目で対象外を選んだ場合、【減算】決算項目は、貸借差額でなければならないようです。', 'sumDebitMinus' => '【加算】集計対象で貸方合計額を選んだ場合、【減算】集計対象は、借方合計額でなければならないようです。', 'sumCreditMinus' => '【加算】集計対象で借方合計額を選んだ場合、【減算】集計対象は、貸方合計額でなければならないようです。', 'sumNetMinus' => '【加算】集計対象で貸借差額を選んだ場合、【減算】の集計対象は、貸借差額しか選べないようです。',),),
					),
					'flagContentUse' => 0,
					'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
					'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(),
					'flagFoldUse' => 1, 'flagFoldNow' => 0,
					'flagHideUse' => 1, 'flagHideNow' => 0,
					'flagDisabled' => 0,
					'varsTmpl' => array(
						'strNormal' => '減算集計する対象を設定してください。',
						'strLog' => '<span class="codeLibBaseFontOrange" style="float:none;">※ 設定値が既にシステム上で使用されているため変更できません。</span><br>現在選択しているのは、『 <%replace%> 』です。',
						'strLogTemp' => '<span class="codeLibBaseFontOrange" style="float:none;">※ 仮繰越処理中の繰越残高が確定するまで変更できません。</span><br>現在選択しているのは、『 <%replace%> 』です。',
					),
				),
			),
			'varsPage' => array(),
			'varsDetail' => array(),
			'varsBtn' => array(
			),
			'varsStart' => array(
				'strTitle' => '詳細',
				'strClass' => 'codeLibBaseImgSheet',
			),
			'view' => array(
				'varsFormat' => array(
					'id' => '',
					'flagType' => 'normalFormat',
					'numHeight' => 0,
					'numWidth' => 0,
					'flagHeaderLeftUse' => 1,
					'strTitleHeaderLeft' => '詳細',
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
					'flagEditUse' => 1,
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
							'flagUse' => 1, 'flagNow' => 0,
							'id' => 'Edit',
							'strClass' => 'codeLibBtnImgEdit',
							'strClassOver' => 'codeLibBtnImgEditOver',
							'strClassNoactive' => 'codeLibBtnImgEditNoactive',
							'strTitle' => '修正',
						),
					),
				),
			),
		),
	),
	'child' => array(
		'varsTitle' => array(
			'editor' => 'エディタ',
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
			'numHeight' => 450,//180 285
			'numWidthMin' => 275,
			'numHeightMin' => 400,
			'numZIndex' => 0
		),
	),
);


