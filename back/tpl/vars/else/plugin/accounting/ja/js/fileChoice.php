<?php

$vars = array(
	'pathCss' => '',
	'portal' => array(
		'varsNavi' => array(
			'varsStatus' => array(
				'flagNow' => 'search',
				'flagCakeUse' => 1,
				'flagTreeUse' => 0,
				'flagSearchUse' => 1,
				'flagFolderUse' => 0,
				'switchList' => array('search',),
			),
			'varsFolder' => array(),
			'varsDetail' => array(),
			'tree' => array(),
			'search' => array(
				'varsFormat' => array(
					'id' => '',
					'flagType' => 'normalFormat',
					'numHeight' => 0,
					'numWidth' => 0,
					'flagHeaderLeftUse' => 1,
					'strTitleHeaderLeft' => '検索',
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
					'flagReloadUse' => 1,
					'flagSwitchUse' => 1,
				),
				'varsDetail' => array(
					'varsStatus' => array(
						'flagNow' => 'word',
						'flagSwitchUse' => 1,
						'flagItemUse' => 1,
						'flagWordUse' => 1,
						'flagTagUse' => 1,
						'flagMyRecordUse' => 1,
						'idColumnTagList' => array('arrSpaceStrTag'),
						'idColumnWordList' => array('strTitle'),
						'switchList' => array('word','tag','item'),
					),
					'varsDetail' => array(
						'varsStatus' => array(
							'flagBtnBottomUse' => 1,
						),
						'varsTag' => array(),
						'varsItem' => array(),
						'varsWord' => array(),
						'varsDetail' => array(),
						'varsBtn' => array(
							array(
								'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
								'id' => 'EventFormBtnSave',
								'vars' => array( 'idTarget' => 'eventFormBtnSave', ),
								'strTitle' => '保存',
							),
							array(
								'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
								'id' => 'EventFormBtnDelete',
								'vars' => array( 'idTarget' => 'eventFormBtnDelete', ),
								'strTitle' => '削除',
							),
							array(
								'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
								'id' => 'EventFormBtn',
								'vars' => array( 'idTarget' => 'eventFormBtn', ),
								'strTitle' => '検索',
							),
						),
						'templateDetail' => array(
							'switchTarget' => array(
								'flagMustUse' => 1,
								'id' => 'SwitchTarget',
								'strTitle' => '検索対象', 'strExplain' => '', 'value' => 'word',
								'flagErrorNow' => 0,
								'arrayError' => array(
									array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ), ),
								),
								'flagContentUse' => 0,
								'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
								'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
								'unitWidth' => '%', 'numWidth' => 40, 'unitHeight' => '', 'numHeight' => 0,
								'arrayOption' => array(
									array( 'strTitle' => 'ワード', 'value' => 'word', ),
									array( 'strTitle' => 'タグ', 'value' => 'tag', ),
									array( 'strTitle' => '項目', 'value' => 'item', ),
								),
							),
							'wordTarget' => array(
								'flagMustUse' => 1,
								'id' => 'Word',
								'strTitle' => '検索ワード', 'strExplain' => '', 'value' => '',
								'flagErrorNow' => 0,
								'arrayError' => array(
									array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ), ),
								),
								'flagContentUse' => 0,
								'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
								'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 0,
								'unitWidth' => '%', 'numWidth' => 80, 'unitHeight' => '', 'numHeight' => 0,
								'arrayOption' => array(),
							),
							'tagTarget' => array(
								'flagMustUse' => 1,
								'id' => 'Tag',
								'strTitle' => '検索タグ', 'strExplain' => '', 'value' => '',
								'flagErrorNow' => 0,
								'arrayError' => array(
									array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ), ),
								),
								'flagContentUse' => 0,
								'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
								'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 0,
								'unitWidth' => '%', 'numWidth' => 80, 'unitHeight' => '', 'numHeight' => 0,
								'arrayOption' => array(),
							),
							'itemTarget' => array(
								'flagMustUse' => 1,
								'id' => 'JsonItem',
								'strTitle' => '検索項目', 'strExplain' => '', 'value' => '',
								'flagErrorNow' => 0,
								'arrayError' => array(
									array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ), ),
								),
								'flagContentUse' => 1,
								'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
								'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 0,
								'unitWidth' => '%', 'numWidth' => 40, 'unitHeight' => '', 'numHeight' => 0,
								'arrayOption' => array(),
							),
							'jsonSort' => array(
								'flagMustUse' => 1,
								'id' => 'JsonSort',
								'strTitle' => '並び順', 'strExplain' => '', 'value' => '',
								'flagErrorNow' => 0,
								'arrayError' => array(
									array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ), ),
								),
								'flagContentUse' => 1,
								'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
								'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 0,
								'unitWidth' => '%', 'numWidth' => 40, 'unitHeight' => '', 'numHeight' => 0,
								'arrayOption' => array(),
							),
							'myRecord' => array(
								'flagMustUse' => 0,
								'id' => 'MyRecord',
								'strTitle' => '', 'strExplain' => '', 'value' => '',
								'flagErrorNow' => 0,
								'arrayError' => array(),
								'flagContentUse' => 0,
								'flagCommentUse' => 1, 'strCommentTitle' => 'My条件', 'strComment' => '',
								'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 0,
								'unitWidth' => '%', 'numWidth' => 40, 'unitHeight' => '', 'numHeight' => 0,
								'arrayOption' => array(),
							),
						),
					),
					'varsMyRecord' => array(
						'varsFormList' => array(
							'varsStatus' => array(
								'flagSortUse' => 1,
								'flagCopyUse' => 0,
								'flagRemoveUse' => 1,
								'flagAddUse' => 0,
								'flagEditUse' => 1,
								'flagFormUse' => 1,
								'flagBtnUse' => 1,
							),
							'templateDetail' => array(
								'id' => '',
								'flagSortUse' => 1,
								'flagCopyUse' => 0,
								'flagRemoveUse' => 1,
								'flagEditUse' => 1,
								'flagFormUse' => 0,
								'flagBtnUse' => 1,
								'numSort' => 0,
								'value' => '',
								'vars' => '',
							),
							'varsDetail' => array(),
						),
					),
					'varsSearchSort' => array(
						'varsStatus' => array(
							'flagUse' => 1,
							'flagAscUse' => 1,
							'flagDescUse' => 1,
						),
						'varsDetail' => array(
							'id' => 0,
							'itemValue' => 'id',
							'sortValue' => 'desc'
						),
						'itemOption' => array(
							array( 'strTitle' => '記録日時', 'value' => 'id', ),
							array( 'strTitle' => '更新日時', 'value' => 'stampUpdate', ),
						),
						'sortOption' => array(
							array( 'strTitle' => '降順', 'value' => 'desc', ),
							array( 'strTitle' => '昇順', 'value' => 'asc', ),
						),
					),
					'varsSearchItem' => array(
						'varsStatus' => array(
							'flagUse' => 1,
							'flagAddUse' => 1,
							'flagCopyUse' => 1,
							'flagRemoveUse' => 1,
							'flagCalenderUse' => 1,
						),
						'varsCalender' => array(
							'varsStatus' => array(
								'numLeft' => 0,
								'numTop' => 0,
								'flagMaxUse' => 0,
								'flagMaxAutoUse' => 1,
								'stampMax' => 0,//1260198000000
								'flagMainUse' => 1,
								'flagMainAutoUse' => 1,
								'stampMain' => 0,
								'flagMinUse' => 0,
								'flagMinAutoUse' => 0,
								'stampMin' => 0,
								'stampPoint' => 0,
							),
						),
						'varsDetail' => array(
							array(
								'id' => 0,
								'flagType' => 'num',
								'flagOption' => '',
								'firstValue' => 'num-idLogFile',
								'secondValue' => 'eq',
								'restValue' => '',
							),
						),
						'templateDetail' => array(
							'varsDetail' => array(
								'id' => 0,
								'flagType' => 'num',
								'flagOption' => '',//set option str ex)moduleOption
								'firstValue' => 'num-idLogFile',
								'secondValue' => 'eq',
								'restValue' => '',
							),
							'firstOption' => array(
								array( 'strTitle' => '通番', 'value' => 'num-idLogFile', ),
								array( 'strTitle' => '記録日時', 'value' => 'stamp-stampRegister', ),
								array( 'strTitle' => '更新日時', 'value' => 'stamp-stampUpdate', ),
								array( 'strTitle' => '証憑ファイル名', 'value' => 'str-strTitle', ),
								array( 'strTitle' => '拡張子', 'value' => 'type-strFileType', ),
								array( 'strTitle' => 'サイズ', 'value' => 'num-numByte', ),
								array( 'strTitle' => 'タグ', 'value' => 'tag-arrSpaceStrTag', ),
								array( 'strTitle' => '担当者アカウント/通番', 'value' => 'account-idAccount', ),
							),
							'accountOption' => array(
								array( 'strTitle' => '～と一致。', 'value' => 'eq', ),
								array( 'strTitle' => '～と不一致。', 'value' => 'ne', ),
							),
							'typeOption' => array(
								array( 'strTitle' => '～と一致。', 'value' => 'eq', ),
								array( 'strTitle' => '～と不一致。', 'value' => 'ne', ),
							),
							'tagOption' => array(
								array( 'strTitle' => '～と一致。', 'value' => 'like', ),
								array( 'strTitle' => '～と不一致。', 'value' => 'notlike', ),
							),
							'strOption' => array(
								array( 'strTitle' => '～を含む。', 'value' => 'like', ),
								array( 'strTitle' => '～を含まない。', 'value' => 'notlike', ),
								array( 'strTitle' => '～と一致。', 'value' => 'eq', ),
								array( 'strTitle' => '～と不一致。', 'value' => 'ne', ),
								array( 'strTitle' => '～で始まる。', 'value' => 'start', ),
								array( 'strTitle' => '～で終わる。', 'value' => 'end', ),
							),
							'stampOption' => array(
								array( 'strTitle' => '～と一致。', 'value' => 'eq', ),
								array( 'strTitle' => '～と不一致。', 'value' => 'ne', ),
								array( 'strTitle' => '～より前。', 'value' => 'before', ),
								array( 'strTitle' => '～より後。', 'value' => 'after', ),
								array( 'strTitle' => '～以前。', 'value' => 'eqBefore', ),
								array( 'strTitle' => '～以後。', 'value' => 'eqAfter', ),
							),
							'numOption' => array(
								array( 'strTitle' => '～と一致。', 'value' => 'eq', ),
								array( 'strTitle' => '～と不一致。', 'value' => 'ne', ),
								array( 'strTitle' => '～未満。', 'value' => 'small', ),
								array( 'strTitle' => '～超。', 'value' => 'big', ),
								array( 'strTitle' => '～以下。', 'value' => 'eqSmall', ),
								array( 'strTitle' => '～以上。', 'value' => 'eqBig', ),
							),
							'restOption' => array(
							),
						),
					),
				),
			),
			'templateFolder' => array(),
		),
		'varsList' => array(
			'varsStart' => array(
				'varsStatus' => array(
					'strTitle' => '選択リスト',
					'strClass' => 'codeLibBaseImgFolder',
				),
				'varsEdit' => array(
					'flagReloadUse' => 1,
				),
				'varsDetail' => array(),
			),
			'varsStatus' => array(
				'flagNow' => 'tree',
				'flagCakeUse' => 1,
				'flagTableUse' => 0,
				'flagScheduleUse' => 0,
				'flagThumbnailUse' => 0,
				'flagTreeUse' => 1,
				'flagBoldUse' => 0,
				'flagFontUse' => 0,
				'flagBgUse' => 0,
				'switchList' => array('tree'),
			),
			'templateDetail' => array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagCheckUse' => 1, 'flagCheckNow' => 0, 'flagRemoveUse' => 0, 'flagEditUse' => 0,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(),
				'idTarget' => '',
				'child' => array()
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
			'varsBtn' => array(
				array(
					'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
					'id' => 'eventFormBtn',
					'vars' => array( 'idTarget' => 'eventFormBtn', ),
					'strTitle' => '選択決定',
				),
			),
			'table' => array(),
			'schedule' => array(),
			'thumbnail' => array(),
			'tree' => array(
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
				),
				'varsDetail' => array(
					'varsStatus' => array(
						'flagUse' => 1,
						'flagMoveUse' => 0,
						'flagInsertUse' => 0,
						'flagSortUse' => 0,
						'flagFoldNow' => 0,
						'flagFoldUse' => 0,
						'flagCakeUse' => 1,
						'flagCheckUse' => 1,
						'flagCheckNow' => 1,
						'flagBarUse' => 1,
						'flagBtnBottomUse' => 1,
						'flagBtnUse' => 0,
						'flagLockUse' => 0,
						'flagLockNow' => 0,
						'flagInnerFindUse' => 0,
						'flagFindUse' => 0,
						'flagAddUse' => 0,
						'flagEditUse' => 0,
						'flagEditNow' => 1,
						'flagRemoveUse' => 0,
						'flagRemoveNow' => 1,
						'flagPageUse' => 1,
						'flagInnerPageUse' => 1,
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
					'varsHtml' => array(),
					'varsFind' => array(),
					'varsPage' => array(),
					'varsBtn' => array(),
					'varsDetail' => array(),
				),
			),
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
						'numHeightBox' => 0,		'numWidthBox' => 0,
					),
					array(
						'id' => 'List',
						'flagBoxUse' => 1,
						'numPriority' => 1,
						'numSort' => 1,
						'numWidth' => 150,			'numHeight' => 200,
						'numWidthMin' => 150,		'numHeightMin' => 200,
						'numWidthStandard' => 150,	'numHeightStandard' => 200,
						'numWidthWide' => 150,		'numHeightWide' => 200,
						'numWidthClassic' => 150,	'numHeightClassic' => 200,
						'numHeightBox' => 0,		'numWidthBox' => 0,
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
			'detail' => array(),
		),
	),
);


