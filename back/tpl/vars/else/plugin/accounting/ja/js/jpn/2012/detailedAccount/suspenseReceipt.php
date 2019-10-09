<?php

$vars = array(
	'varsFlag' => array(
		'flagMenu' => 'detail',
		'numPage' => 1,
	),
	'varsItem'  => array(
		'flagPageEnd' => 1,
		'strEscape' => '、',
		'strSpace' => '　',
		'flagBtnCalc' => 0,
		'varsList' => array(),
		'varsSave' => array(),
		'varsPreference' => array(),
		'varsCommon' => array(),
		'arrNoneSub' => array(
			'strTitle' => '補助科目未指定残高',
			'value' => 0,
		),
		'arrNoneAccountTitle' => array(
			'strTitle' => '指定なし',
			'value' => 'none',
		),
		'varsOutput' => array(
			'strEntity' => '事業体(<%replace%>)',
			'strNum' => '会期(第<%replace%>期)',
			'strPage' => '<%replace%>頁',
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
			'flagPageHide'  => 0,
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
					'flagMustUse' => 0,
					'id' => 'DummyEditPrev',
					'strTitle' => '',
					'strExplain' => '',
					'value' => '',
					'flagErrorNow' => 0,
					'arrayError' => array(),
					'flagContentUse' => 0,
					'flagCommentUse' => 1, 'strCommentTitle' => '【お知らせ】', 'strComment' => '<span class="codeLibBaseFontOrange" style="float:none;">※ 前期データが確定するまで利用できません。</span>',
					'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 100,
					'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(),  
					'flagFoldUse' => 1, 'flagFoldNow' => 0,
				),
				array(
					'flagMustUse' => 1,
					'id' => 'FlagMenu',
					'strTitle' => '内訳書',
					'strExplain' => '',
					'value' => 'detail',
					'flagErrorNow' => 0,
					'arrayError' => array(
						array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
					),
					'flagContentUse' => 0,
					'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
					'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(
						array( 'strTitle' => '仮受金（前受金・預り金）の内訳書', 'value' => 'detail', ),
					),
					'numSize' => 1,
					'flagHideUse' => 1, 'flagHideNow' => 1,
				),
				array(
					'flagMustUse' => 1,
					'id' => 'NumPage',
					'strTitle' => '作業頁',
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
					'arrayOption' => array(),
					'varsTmpl' => array(
						'strPage' => '頁目',
					),
					'numSize' => 10,
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
				'varsEdit' => array( 'flagReloadUse' => 1, 'flagPreferenceUse' => 1,),
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
				'varsEdit' => array( 'flagReloadUse' => 1, 'flagPreferenceUse' => 1, ),
				'varsDetail' => array(
					'varsStatus' => array(
						'flagBtnBottomUse' => 1,
					),
					'varsBtn' => array(),
					'varsDetail' => array(),
				),
			),
		),
		'varsList' => array(),
		'varsDetail' => array(
			'varsStatus' => array(
				'flagUse' => 1,
				'flagNow' => 'sheet',
				'flagPrintUse' => 1,
				'flagPrintNow' => 'item',
				'flagOutputUse' => 1,
				'flagOutputNow' => 'item',
				'flagCakeUse' => 1,
				'flagFoldUse' => 0,
				'flagViewUse' => 0,
				'flagSheetUse' => 1,
				'flagFormUse' => 0,
				'flagMoveUse' => 0,
				'switchList' => array('sheet'),
				'switchOutputList' => array('item', 'itemAll'),
				'switchPrintList' => array('item', 'itemAll'),
			),
			'templateDetail' => array(
				array(
					'flagMustUse' => 0,
					'id' => 'DummyPage',
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
						'strNormal' => '<span class="codeLibBaseFontOrange" style="float:none;">内訳書<%replace%>頁目</span>',
					),
				),
				array(
					'flagMustUse' => 1,
					'id' => 'JsonData',
					'strTitle' => '内容', 'strExplain' => '', 'value' => 'dummy',
					'flagErrorNow' => 0,
					'arrayError' => array(
						array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ),),
						array( 'flagCheck' => 'attest', 'flagUse' => 1, 'strComment' => array(
							'strOver' => '設定可能な文字数を超過してしまうためデータを挿入できなかった項目があったようです。',
							'strDouble' => '内訳書の中に重複設定されているデータ参照先があるようです。',
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
							'varsFormTemp' => array(
								'varsStatus' => array(
									'numLeft' => 0,
									'numTop' => 0,
								),
								'varsDetail' => array(
									'flagTag'       => '',
									'flagInputType' => '',
									'numMaxlength'  => 9,
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
					'flagHideUse' => 1, 'flagHideNow' => 0,
				),
			),
			'varsPage' => array(),
			'varsDetail' => array(
				'varsHtml' => 'test',
			),
			'varsBtn' => array(),
			'varsStart' => array(
				'strTitle' => '内容',
				'strClass' => 'codeLibBaseImgSheet',
				'varsEdit' => array(
					'flagReloadUse' => 1,
					'flagOutputUse' => 1,
					'flagPrintUse' => 1,
					'flagEditUse' => 1,
				),
			),
			'sheet' => array(
				'varsFormat' => array(
					'id' => '',
					'flagType' => 'normalFormat',
					'numHeight' => 0,
					'numWidth' => 0,
					'flagHeaderLeftUse' => 1,
					'strTitleHeaderLeft' => '内容',
					'strClassHeaderLeft' => 'codeLibBaseImgSheet',
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
					'flagReloadUse' => 1,
					'flagOutputUse' => 1,
					'flagPrintUse' => 1,
					'flagEditUse' => 1,
				),
				'varsPage' => array(),
				'varsBtn' => array(),
				'varsDetail' => array(),
			),
			'view' => array(),
			'form' => array(),
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
						'numWidth' => 175,			'numHeight' => 200,
						'numWidthMin' => 175,		'numHeightMin' => 200,
						'numWidthStandard' => 175,	'numHeightStandard' => 200,
						'numWidthWide' => 175,		'numHeightWide' => 200,
						'numWidthClassic' => 175,	'numHeightClassic' => 200,
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
						array(
							'flagUse' => 1,
							'flagNow' => 1,
							'id' => 'Preference',
							'strClass' => 'codeLibBtnImgPreference',
							'strClassOver' => 'codeLibBtnImgPreferenceOver',
							'strClassNoactive' => 'codeLibBtnImgPreferenceNoactive',
							'strTitle' => '基本メニュー',
						),
					),
				),
			),
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
							'flagUse' => 1, 'flagNow' => 0,
							'id' => 'Edit',
							'strClass' => 'codeLibBtnImgEdit',
							'strClassOver' => 'codeLibBtnImgEditOver',
							'strClassNoactive' => 'codeLibBtnImgEditNoactive',
							'strTitle' => '修正',
						),
						array(
							'flagUse' => 1,
							'flagNow' => 0,
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
										'strTitle' => '選択した頁', 'strClass' => 'codeLibBaseImgSheet',
										'vars' => array( 'idTarget' => 'item',),
										'child' => array(),
									),
									array(
										'id' => 'ItemAll', 'flagCheckUse' => 1, 'flagCheckNow' => 0,
										'strTitle' => '全ての頁', 'strClass' => 'codeLibBaseImgSheet',
										'vars' => array( 'idTarget' => 'itemAll',),
										'child' => array(),
									),
								),
							),
						),
						array(
							'flagUse' => 1,
							'flagNow' => 0,
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
										'strTitle' => '選択した頁', 'strClass' => 'codeLibBaseImgSheet',
										'vars' => array( 'idTarget' => 'item',),
										'child' => array(),
									),
									array(
										'id' => 'ItemAll', 'flagCheckUse' => 1, 'flagCheckNow' => 0,
										'strTitle' => '全ての頁', 'strClass' => 'codeLibBaseImgSheet',
										'vars' => array( 'idTarget' => 'itemAll',),
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
	'child' => array(
		'varsTitle' => array(
			'Preference' => '基本メニュー',
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
	),
);


