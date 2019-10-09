<?php

$vars = array(
	'pathCss' => '',
	'varsItem' => array(),
	'strNone' => 'アクセス可能項目なし',
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
					'strTitleHeaderLeft' => '詳細検索',
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
				),
				'varsDetail' => array(
					'varsStatus' => array(
						'flagNow' => 'item',
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
								'firstValue' => 'num-idAccess',
								'secondValue' => 'eq',
								'restValue' => '',
							),
						),
						'templateDetail' => array(
							'varsDetail' => array(
								'id' => 0,
								'flagType' => 'num',
								'flagOption' => '',//set option str ex)moduleOption
								'firstValue' => 'num-idAccess',
								'secondValue' => 'eq',
								'restValue' => '',
							),
							'firstOption' => array(
								array( 'strTitle' => '通番', 'value' => 'num-idAccess', ),
								array( 'strTitle' => '記録日時', 'value' => 'stamp-stampRegister', ),
								array( 'strTitle' => '更新日時', 'value' => 'stamp-stampUpdate', ),
								array( 'strTitle' => 'タイトル', 'value' => 'str-strTitle', ),
								array( 'strTitle' => 'タグ', 'value' => 'tag-arrSpaceStrTag', ),
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
					'strTitle' => 'リスト',
					'strClass' => 'codeLibBaseImgFolder',
				),
				'varsEdit' => array(
					'flagReloadUse' => 1,
					'flagSwitchUse' => 1,
					'flagSearchUse' => 1,
				),
				'varsDetail' => array(),
			),
			'varsStatus' => array(
				'flagNow' => 'table',
				'flagCakeUse' => 1,
				'flagTableUse' => 1,
				'flagScheduleUse' => 1,
				'flagThumbnailUse' => 0,
				'flagTreeUse' => 0,
				'flagBoldUse' => 0,
				'flagFontUse' => 0,
				'flagBgUse' => 0,
				'switchList' => array('table','schedule'),
				'flagReloadUse' => 1,
				'flagReloadNow' => 'start',
				'switchReloadList' => array('same', 'start'),
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
				'varsColumnDetail' => array(
					'id' => '',
					'strTitle' => '',
					'stampRegister' => 0,
					'stampUpdate' => 0,
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
				'flagSearchUse' => 1,
			),
			'varsDetail' => array(),
			'varsBtn' => array(
				array(
					'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
					'id' => 'eventBtnDelete',
					'vars' => array( 'idTarget' => 'Delete', ),
					'strTitle' => '一括削除',
				),
			),
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
					'flagSearchUse' => 1,
				),
				'varsDetail' => array(
					'varsStatus' => array(
						'flagMenuUse' => 1,
						'flagColumnUse' => 0,
						'flagResizeUse' => 1,
						'flagSortColumnUse' => 1,
						'flagSortColumnLineUse' => 0,
						'flagSortColumnLineNow' => '',
						'flagMoveUse' => 0,
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
							'flagCheckUse' => 0,
							'flagCheckNow' => 1,
							'id' => 'FlagCheck',
							'flagSortColumnLineUse' => 0,
							'flagSortColumnLineNow' => 0,
							'strTitle' => '',
							'numSort' => 0,
							'numWidth' => 26,
							'numWidthMin' => 26,
							'flagType' => 'checkbox',
							'flagTimeType' => '',
							'flagAllCheckbox' => 1,
							'flagAllCheckboxNow' => 0,
							'vars' => array(),
							'child' => array(),
						),
						array(
							'flagUse' => 1,
							'flagCheckUse' => 1,
							'flagCheckNow' => 1,
							'id' => 'StrTitle',
							'flagSortColumnLineUse' => 0,
							'flagSortColumnLineNow' => 0,
							'strTitle' => 'タイトル',
							'numSort' => 1,
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
							'id' => 'StampRegister',
							'flagSortColumnLineUse' => 0,
							'flagSortColumnLineNow' => 0,
							'strTitle' => '記録日時',
							'numSort' => 2,
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
							'id' => 'StampUpdate',
							'flagSortColumnLineUse' => 0,
							'flagSortColumnLineNow' => 0,
							'strTitle' => '更新日時',
							'numSort' => 3,
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
							'id' => 'Id',
							'flagSortColumnLineUse' => 0,
							'flagSortColumnLineNow' => 0,
							'strTitle' => '通番',
							'numSort' => 4,
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
					'flagSearchUse' => 1,
				),
				'varsDetail' => array(
					'varsStatus' => array(
						'flagNow' => 'month',// 'month','week'
						'flagFoldUse' => 1,
						'flagBgUse' => 1,
						'flagFontUse' => 1,
						'flagBoldUse' => 0,
						'flagMoveUse' => 0,
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
		'varsDetail' => array(
			'varsStatus' => array(
				'flagUse' => 1,
				'flagNow' => 'view',
				'flagCakeUse' => 1,
				'flagFoldUse' => 1,
				'flagViewUse' => 1,
				'flagFormUse' => 0,
				'flagMoveUse' => 0,
				'switchList' => array('view'),
			),
			'templateDetail' => array(
				array(
					'flagMustUse' => 0,
					'id' => 'DummyDefault',
					'strTitle' => '【お知らせ】',
					'strExplain' => '',
					'value' => '<span class="codeLibBaseFontOrange" style="float:none;">※ デフォルト項目のため編集できません。</span>',
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
					'flagMustUse' => 1,
					'id' => 'StrTitle',
					'strTitle' => 'タイトル', 'strExplain' => '当該アクセス可能項目パターンのタイトル名を記入してください。', 'value' => '',
					'flagErrorNow' => 0,
					'arrayError' => array(
						array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ),),
						array( 'flagCheck' => 'max', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'str', 'num' => 100, 'strComment' => array( 'common' => '入力文字数が多すぎるようです。', ),),
						array( 'flagCheck' => 'attest', 'flagUse' => 1, 'strComment' => array('strTitle' => '既に同じタイトル名が使用されているようです。',),),
					),
					'flagContentUse' => 0,
					'flagCommentUse' => 1, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 100,
					'numWidth' => 90, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(), 
					'flagFoldUse' => 1, 'flagFoldNow' => 0,
					'varsTextBtn' => array(),
				),
				array(
					'flagMustUse' => 0,
					'id' => 'JsonData',
					'strTitle' => 'アクセス可能項目', 'strExplain' => '
						ユーザ項目のうちアクセスを許可する項目を選択してください。
						<br>※管理者権限を有していないアカウントに対してのみ適用されます。
						<br>※『(※)』がある選択肢は、『 閲覧・全て 』を有するアクセス権限パターンを併せてアカウントに設定する必要があります。
						<br>※複数選択可。
					', 'value' => '',
					'flagErrorNow' => 0,
					'arrayError' => array(),
					'flagContentUse' => 0,
					'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
					'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'flagMultiple' => 1,
					'arrayOption' => array(),
					'flagFoldUse' => 1, 'flagFoldNow' => 0,
					'numSize' => 15,
					'varsTmpl' => array(
						'strNeed' => '(※)',
					),
				),
				array(
					'flagMustUse' => 0,
					'id' => 'DummyJsonData',
					'strTitle' => 'アクセス可能項目',
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
					'varsTmpl' => array(
						'strAll' => '全項目アクセス可能',
						'strNone' => '全項目アクセス不可',
					),
				),
				array(
					'flagMustUse' => 0,
					'id' => 'ArrSpaceStrTag',
					'strTitle' => 'タグ', 'strExplain' => '必要なタグを空白区切りで設定してください。', 'value' => '',
					'flagErrorNow' => 0,
					'arrayError' => array(
						array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ),),
						array(	'flagCheck' => 'max', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'str', 'num' => 1000, 'strComment' => array( 'common' => '入力文字数が多すぎるようです。', ),),
					),
					'flagContentUse' => 0,
					'flagCommentUse' => 1, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 1000,
					'numWidth' => 90, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(), 
					'flagFoldUse' => 1, 'flagFoldNow' => 0,
					'varsTextBtn' => array(),
				),
				array(
					'flagMustUse' => 0,
					'id' => 'StampRegister',
					'strTitle' => '記録日時', 'strExplain' => '', 'value' => '',
					'flagErrorNow' => 0,
					'arrayError' => array(),
					'flagContentUse' => 0,
					'flagCommentUse' => 1, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => '', 'flagInputType' => '', 'numMaxlength' => 0,
					'numWidth' => 0, 'unitWidth' => '', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(), 
					'flagFoldUse' => 1, 'flagFoldNow' => 0,
				),
				array(
					'flagMustUse' => 0,
					'id' => 'StampUpdate',
					'strTitle' => '更新日時', 'strExplain' => '', 'value' => '',
					'flagErrorNow' => 0,
					'arrayError' => array(),
					'flagContentUse' => 0,
					'flagCommentUse' => 1, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => '', 'flagInputType' => '', 'numMaxlength' => 0,
					'numWidth' => 0, 'unitWidth' => '', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(), 
					'flagFoldUse' => 1, 'flagFoldNow' => 0,
				),
				array(
					'flagMustUse' => 0,
					'id' => 'Id',
					'strTitle' => '通番', 'strExplain' => '', 'value' => '',
					'flagErrorNow' => 0,
					'arrayError' => array(),
					'flagContentUse' => 0,
					'flagCommentUse' => 1, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => '', 'flagInputType' => '', 'numMaxlength' => 0,
					'numWidth' => 0, 'unitWidth' => '', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(), 
					'flagFoldUse' => 1, 'flagFoldNow' => 0,
					'varsTextBtn' => array(),
				),
			),
			'varsPage' => array(),
			'varsDetail' => array(),
			'varsBtn' => array(
				array(
					'flagUse' => 0, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
					'id' => 'BtnDelete',
					'vars' => array( 'idTarget' => 'delete', ),
					'strTitle' => '削除',
				),
			),
			'varsStart' => array(
				'strTitle' => '詳細',
				'strClass' => 'codeLibBaseImgSheet',
				'varsEdit' => array(
					'flagAddUse' => 1,
				),
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
					'flagReloadUse' => 1,
					'flagAddUse' => 1,
					'flagCopyUse' => 1,
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
						'numWidthMin' => 300,		'numHeightMin' => 200,
						'numWidthStandard' => 200,	'numHeightStandard' => 200,
						'numWidthWide' => 300,		'numHeightWide' => 200,
						'numWidthClassic' => 300,	'numHeightClassic' => 200,
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
							'varsContext' => array(
								'varsStatus' => array(
									'numTop' => 0,
									'numLeft' => 0,
									'flagNow' => 'start',
								),
								'varsDetail' => array(
									array(
										'id' => 'Start', 'flagCheckUse' => 1, 'flagCheckNow' => 0,
										'strTitle' => '条件初期化', 'strClass' => 'codeLibBaseImgSheet',
										'vars' => array( 'idTarget' => 'start',),
										'child' => array(),
									),
									array(
										'id' => 'Same', 'flagCheckUse' => 1, 'flagCheckNow' => 0,
										'strTitle' => 'データ更新', 'strClass' => 'codeLibBaseImgSheet',
										'vars' => array( 'idTarget' => 'same',),
										'child' => array(),
									),
								),
							),
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
							'flagUse' => 1, 'flagNow' => 0,
							'id' => 'Search',
							'strClass' => 'codeLibBtnImgSearch',
							'strClassOver' => 'codeLibBtnImgSearchOver',
							'strClassNoactive' => 'codeLibBtnImgSearchNoactive',
							'strTitle' => '詳細検索',
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
							'id' => 'Add',
							'strClass' => 'codeLibBtnImgAdd',
							'strClassOver' => 'codeLibBtnImgAddOver',
							'strClassNoactive' => 'codeLibBtnImgAddNoactive',
							'strTitle' => '新規',
						),
						array(
							'flagUse' => 1, 'flagNow' => 0,
							'id' => 'Copy',
							'strClass' => 'codeLibBtnImgCopy',
							'strClassOver' => 'codeLibBtnImgCopyOver',
							'strClassNoactive' => 'codeLibBtnImgCopyNoactive',
							'strTitle' => 'コピー',
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
			'Search' => '詳細検索',
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
			'numHeight' => 650,//180 285
			'numWidthMin' => 800,
			'numHeightMin' => 650,
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
			'numHeight' => 650,//180 285
			'numWidthMin' => 275,
			'numHeightMin' => 600,
			'numZIndex' => 0
		),
		'search' => array(
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


