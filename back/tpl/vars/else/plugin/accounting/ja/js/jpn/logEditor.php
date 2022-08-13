<?php

$vars = array(
	'pathCss' => '',
/*
	'varsRule'  => array(
		'arrSelectTagAccountTitle' => array(),
		'arrSelectTagDepartment' => array(),
		'arrIdSubAccountTitle' => array(),
		'varsConsumptionTax' => array(),
		'arrStrTitleReport' => array(),
	),
	'varsItem'  => array(
		'flagStatus' => '',
		'arrBlank' => array(
			'strTitle' => '指定なし',
			'value' => '',
		),
		'arrayFS' => array( 'BS' => '貸借科目', 'PL' => '損益科目', 'CR' => '製造原価科目',  ),
		'arrayOptionNone' => array( 'strTitle' => '指定なし', 'value' => 'none', ),
	),
	*/
	'portal' => array(
		'varsNavi' => array(
			'varsStatus' => array(
				'flagNow' => 'folder',
				'flagCakeUse' => 1,
				'flagTreeUse' => 0,
				'flagSearchUse' => 0,
				'flagFolderUse' => 1,
				'switchList' => array('folder',),
			),
			'varsFolder' => array(
				'folder' => array(
					'strTitle' => 'Myフォルダ',
					'varsDetail' => array(),
				),
			),
			'varsDetail' => array(),
			'tree' => array(),
			'search' => array(),
			'templateFolder' => array(
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
				'varsDetail' => array(
					'varsStatus' => array(),
					'templateDetail' => array(
						'dir' => array(
							'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 0, 'flagEditUse' => 0,
							'strTitle' => '履歴',
							'strClass' => 'codeLibBaseImgFolder',
							'vars' => array(),
							'child' => array()
						),
						'file' => array(
							'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
							'strTitle' => '',
							'strClass' => 'codeLibBaseImgSheet',
							'vars' => array(),
							'stamp' => 0,
							'child' => array()
						),
						'log' => array(
							'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 0,
							'strTitle' => '',
							'strClass' => 'codeLibBaseImgSheet',
							'vars' => array(),
							'stamp' => 0,
							'child' => array()
						),
						'templateVars' => array( 'StrTitle' => '', 'StampBook' => '', 'FlagFiscalReport' => '', 'JsonDetail' => '', 'ArrCommaIdLogFile' => '', 'ArrCommaIdAccountPermit' => '', 'IdAccountCharge' => '', 'ArrSpaceStrTag' => '', 'NumSumMax' => '', ),
					),
					'varsDetail' => array(),
					'varsTree' => array(
						'varsStatus' => array(
							'flagUse' => 1,
							'flagMoveUse' => 1,
							'flagInsertUse' => 1,
							'flagSortUse' => 1,
							'flagFoldNow' => 0,
							'flagFoldUse' => 1,
							'flagCakeUse' => 1,
							'flagCheckUse' => 0,
							'flagCheckNow' => 1,
							'flagBarUse' => 1,
							'flagBtnBottomUse' => 1,
							'flagBtnUse' => 1,
							'flagLockUse' => 0,
							'flagLockNow' => 0,
							'flagInnerFindUse' => 1,
							'flagFindUse' => 1,
							'flagAddUse' => 0,
							'flagEditUse' => 1,
							'flagEditNow' => 1,
							'flagRemoveUse' => 1,
							'flagRemoveMenuNoneUse' => 1,
							'flagRemoveNow' => 1,
							'flagPageUse' => 0,
							'flagInnerPageUse' => 0,
							'id' => 'Tree',
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
							'value' => '',
							'flag' => 0,
							'num' => 0,
							'numWidth' => 70,
							'unitWidth' => '%',
							'strTitle' => '絞込検索',
						),
						'varsBtn' => array(
							array(
								'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
								'id' => 'eventFormBtnSave',
								'vars' => array( 'idTarget' => 'eventFormBtnSave', ),
								'strTitle' => 'フォルダ保存',
							),
						),
						'varsDetail' => array(),
					),
				),
			),
		),
		'varsList' => array(),
		'varsDetail' => array(
			'varsStatus' => array(
				'flagUse' => 1,
				'flagNow' => 'form',
				'flagCakeUse' => 0,
				'flagFoldUse' => 0,
				'flagViewUse' => 0,
				'flagFormUse' => 1,
				'flagMoveUse' => 0,
				'switchList' => array('form'),
				'flagReloadUse' => 1,
				'flagReloadNow' => 'same',
				'switchReloadList' => array('same', 'start'),
			),
			'varsPage' => array(),
			'varsDetail' => array(),
			'varsBtn' => array(
				array(
					'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
					'id' => 'BtnFolder',
					'vars' => array( 'idTarget' => 'folder', ),
					'strTitle' => 'Myフォルダ挿入',
				),
				array(
					'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
					'id' => 'BtnSave',
					'vars' => array( 'idTarget' => 'save', ),
					'strTitle' => '保存',
				),
			),
			'varsStart' => array(
				'strTitleAdd' => ' ( 新規 )',
				'strTitleEdit' => '( 修正 )',
			),
			'varsEnd' => array(
				'strTitle' => '処理完了',
				'strClass' => 'codeLibBaseImgSheet',
				'varsEdit' => array( 'flagReloadUse' => 0, ),
				'varsBtn' => array(
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
				'varsDetail' => array(
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
				),
			),
			'varsDefer' => array(
				'strTitle' => '処理完了',
				'strClass' => 'codeLibBaseImgSheet',
				'varsEdit' => array( 'flagReloadUse' => 0, ),
				'varsBtn' => array(
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
					array(
						'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
						'id' => 'Defer',
						'vars' => array( 'flagDefer' => 1, ),
						'strTitle' => '留保ログ',
					),
				),
				'varsDetail' => array(
					array(
						'flagMustUse' => 0,
						'id' => 'End',
						'strTitle' => '', 'strExplain' => '', 'value' => '',
						'flagErrorNow' => 0,
						'arrayError' => array(),
						'flagContentUse' => 0,
						'flagCommentUse' => 1, 'strCommentTitle' => '', 'strComment' => '仕訳の新規作成が留保されたようです。留保中の仕訳は、収支管理の基本メニューにある『 留保ログ 』にあるので下記ボタンをクリックして呼び出したウィンドウの案内に従ってください。',
						'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 0,
						'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
						'arrayOption' => array(), 
						'flagFoldUse' => 0, 'flagFoldNow' => 0,
					),
				),
			),
			'varsDeferReject' => array(
				'strTitle' => '処理完了',
				'strClass' => 'codeLibBaseImgSheet',
				'varsEdit' => array( 'flagReloadUse' => 0, ),
				'varsBtn' => array(
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
				'varsDetail' => array(
					array(
						'flagMustUse' => 0,
						'id' => 'End',
						'strTitle' => '', 'strExplain' => '', 'value' => '',
						'flagErrorNow' => 0,
						'arrayError' => array(),
						'flagContentUse' => 0,
						'flagCommentUse' => 1, 'strCommentTitle' => '', 'strComment' => '仕訳の新規作成が留保されたようです。仕訳の新規作成処理が留保されたようです。留保中の仕訳は、収支管理の基本メニューにある『 留保ログ 』にありますが、あなたには閲覧する権限が付与されていないようです。然るべき担当者に対処を依頼してください。',
						'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 0,
						'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
						'arrayOption' => array(), 
						'flagFoldUse' => 0, 'flagFoldNow' => 0,
					),
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
				'varsEdit' => array( 'flagReloadUse' => 1, ),
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
						'numWidthMin' => 250,		'numHeightMin' => 200,
						'numWidthStandard' => 250,	'numHeightStandard' => 200,
						'numWidthWide' => 250,		'numHeightWide' => 200,
						'numWidthClassic' => 250,	'numHeightClassic' => 200,
						'numHeightBox' => 0,		'numWidthBox' => 0,//auto
					),
					array(
						'id' => 'Detail',
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
							'varsContext' => array(
								'varsStatus' => array(
									'numTop' => 0,
									'numLeft' => 0,
									'flagNow' => 'same',
								),
								'varsDetail' => array(
									array(
										'id' => 'Start', 'flagCheckUse' => 1, 'flagCheckNow' => 0,
										'strTitle' => '初期化', 'strClass' => 'codeLibBaseImgSheet',
										'vars' => array( 'idTarget' => 'start',),
										'child' => array(),
									),
									array(
										'id' => 'Same', 'flagCheckUse' => 1, 'flagCheckNow' => 0,
										'strTitle' => 'やり直し', 'strClass' => 'codeLibBaseImgSheet',
										'vars' => array( 'idTarget' => 'same',),
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


