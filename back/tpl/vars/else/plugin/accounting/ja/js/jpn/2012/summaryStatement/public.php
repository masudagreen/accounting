<?php

$vars = array(
	'varsFlag' => array(
		'flagMenu' => '0',
	),
	'varsItem'  => array(
		'strEscape' => '、',
		'tmplList' => array(
			'flagMustUse' => 0,
			'id' => '',
			'strTitle' => '',
			'strExplain' => '',
			'value' => '',
			'valueStr' => '　',
			'flagValueType' => 'num',
			'flagErrorNow' => 0,
			'arrayError' => array(
				array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ),),
				array( 'flagCheck' => 'format', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'numminus', 'strComment' => array( 'common' => '半角数字で記入する必要があるようです。',  ), ),
				array( 'flagCheck' => 'max', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'num', 'num' => 999999999, 'strComment' => array( 'common' => '最大値を上回ってしまうようです。', ),),
				array( 'flagCheck' => 'min', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'num', 'num' => -99999999, 'strComment' => array( 'common' => '最小値を下回ってしまうようです。', ),),
			),
			'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 9,
			'numWidth' => 40, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => 'px',
			'arrayOption' => array(),
			'flagForm' => 'active',
		),
		'tmplListOthers' => array(
			'flagMustUse' => 0,
			'id' => '',
			'strTitle' => '',
			'strExplain' => '',
			'value' => '',
			'valueStr' => '　',
			'flagValueType' => 'num',
			'flagErrorNow' => 0,
			'arrayError' => array(
				array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ),),
				array( 'flagCheck' => 'format', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'numminus', 'strComment' => array( 'common' => '半角数字で記入する必要があるようです。',  ), ),
				array( 'flagCheck' => 'max', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'num', 'num' => 99999999, 'strComment' => array( 'common' => '最大値を上回ってしまうようです。', ),),
				array( 'flagCheck' => 'min', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'num', 'num' => -9999999, 'strComment' => array( 'common' => '最小値を下回ってしまうようです。', ),),
			),
			'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 8,
			'numWidth' => 40, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => 'px',
			'arrayOption' => array(),
			'flagForm' => 'active',
		),
		'tmplListStr' => array(
			'flagMustUse' => 0,
			'id' => '',
			'strTitle' => '',
			'strExplain' => '',
			'value' => '',
			'valueStr' => '　',
			'flagValueType' => 'str',
			'flagErrorNow' => 0,
			'arrayError' => array(
				array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ),),
				array( 'flagCheck' => 'max', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'str', 'num' => 5, 'strComment' => array( 'common' => '入力文字数が多すぎるようです。', ),),
			),
			'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 5,
			'numWidth' => 40, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => 'px',
			'arrayOption' => array(),
			'flagForm' => 'active',
		),
		'tmplListWorker' => array(
			'flagMustUse' => 0,
			'id' => '',
			'strTitle' => '',
			'strExplain' => '',
			'value' => '',
			'valueStr' => '　',
			'flagValueType' => 'num',
			'flagErrorNow' => 0,
			'arrayError' => array(
				array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ),),
				array( 'flagCheck' => 'format', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'numminus', 'strComment' => array( 'common' => '半角数字で記入する必要があるようです。',  ), ),
				array( 'flagCheck' => 'max', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'num', 'num' => 99999, 'strComment' => array( 'common' => '最大値を上回ってしまうようです。', ),),
				array( 'flagCheck' => 'min', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'num', 'num' => 0, 'strComment' => array( 'common' => '最小値を下回ってしまうようです。', ),),
			),
			'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 5,
			'numWidth' => 40, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => 'px',
			'arrayOption' => array(),
			'flagForm' => 'active',
		),
		'strSpace' => '　',
		'flagBtnCalc' => 0,
		'varsList' => array(),
		'varsSave' => array(),
		'varsCommon' => array(),
		'strMonth' => '月',
		'strEntity' => '事業体(<%replace%>)',
		/*20190401 start*/
	    'strPeriod' => '事業年度((自)平成<%strStartHeisei%>年<%strStartMonth%>月1日 ～ (至)平成<%strEndHeisei%>年<%strEndMonth%>月末日)',
	    'strPeriod20190401' => '事業年度((自)<%strStartNengoYear%>年<%strStartMonth%>月1日 ～ (至)<%strEndNengoYear%>年<%strEndMonth%>月末日)',
	    /*20190401 end*/
		'varsMenu' => array(
			'strList' => '法人事業概況説明書',
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
					'strTitle' => '設定項目',
					'strExplain' => '',
					'value' => '0',
					'flagErrorNow' => 0,
					'arrayError' => array(
						array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
					),
					'flagContentUse' => 0,
					'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
					'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(
						array( 'strTitle' => ' 0 : 無題', 'value' => '0', ),
						array( 'strTitle' => ' 1 : 事業内容', 'value' => '1', ),
						array( 'strTitle' => ' 2 : 支店・海外取引状況', 'value' => '2', ),
						array( 'strTitle' => ' 3 : 期末従業員等の状況', 'value' => '3', ),
						array( 'strTitle' => ' 4 : 電子計算機の利用状況', 'value' => '4', ),
						array( 'strTitle' => ' 5 : 経理の状況', 'value' => '5', ),
						array( 'strTitle' => ' 6 : 株主又は株式所有異動の有無', 'value' => '6', ),
						array( 'strTitle' => ' 7 : 主要科目・損益', 'value' => '7PL', ),
						array( 'strTitle' => ' 7 : 主要科目・貸借', 'value' => '7BS', ),
						array( 'strTitle' => ' 8 : インターネットバンキング等の利用の有無', 'value' => '8', ),
						array( 'strTitle' => ' 9 : 役員又は役員報酬額の異動の有無', 'value' => '9', ),
						array( 'strTitle' => '10 : 代表者に対する報酬等の金額', 'value' => '10', ),
						array( 'strTitle' => '11 : 事業形態', 'value' => '11', ),
						array( 'strTitle' => '12 : 主な設備等の状況', 'value' => '12', ),
						array( 'strTitle' => '13 : 決済日等の状況', 'value' => '13', ),
						array( 'strTitle' => '14 : 帳簿類の備付状況', 'value' => '14', ),
						array( 'strTitle' => '15 : 税理士の関与状況', 'value' => '15', ),
						array( 'strTitle' => '16 : 加入組合等の状況', 'value' => '16', ),
						array( 'strTitle' => '17 : 月別の売上等の状況・売上(収入)金額', 'value' => '17Sales', ),
						array( 'strTitle' => '17 : 月別の売上等の状況・仕入金額', 'value' => '17Purchase', ),
						array( 'strTitle' => '17 : 月別の売上等の状況・外注費', 'value' => '17Outsourcing', ),
						array( 'strTitle' => '17 : 月別の売上等の状況・人件費', 'value' => '17Employee', ),
						array( 'strTitle' => '17 : 月別の売上等の状況・源泉徴収税額', 'value' => '17Tax', ),
						array( 'strTitle' => '17 : 月別の売上等の状況・余り枠', 'value' => '17Others', ),
						array( 'strTitle' => '17 : 月別の売上等の状況・従事員数', 'value' => '17Worker', ),
						array( 'strTitle' => '18 : 当期の営業成績の概要', 'value' => '18', ),
					),
					'numSize' => 26,
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
				'varsEdit' => array( 'flagReloadUse' => 0, 'flagPreferenceUse' => 1,),
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
				'varsEdit' => array( 'flagReloadUse' => 0, 'flagPreferenceUse' => 1,),
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
				'flagCakeUse' => 0,
				'flagFoldUse' => 0,
				'flagViewUse' => 0,
				'flagSheetUse' => 1,
				'flagFormUse' => 0,
				'flagMoveUse' => 0,
				'switchList' => array('sheet'),
			),
			'templateDetail' => array(
				array(
					'flagMustUse' => 1,
					'id' => 'JsonData',
					'strTitle' => '内容', 'strExplain' => '', 'value' => 'dummy',
					'flagErrorNow' => 0,
					'arrayError' => array(
						array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ),),
						array( 'flagCheck' => 'attest', 'flagUse' => 1, 'strComment' => array('strOver' => '入力文字数上限の9文字を超えてしまう項目があったため、一部集計値を挿入できなかったようです。',),),
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
					'varsTmpl' => array(
						'varsExplain' => array(
							'7' => '単位：千円(千円未満切捨)',
							'17' => '単位：千円(千円未満切捨)',
						),
					),
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
						'numWidth' => 250,			'numHeight' => 200,
						'numWidthMin' => 200,		'numHeightMin' => 200,
						'numWidthStandard' => 200,	'numHeightStandard' => 200,
						'numWidthWide' => 200,		'numHeightWide' => 200,
						'numWidthClassic' => 200,	'numHeightClassic' => 200,
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
							'strTitle' => '集計設定',
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
	'child' => array(
		'varsTitle' => array(
			'Preference' => '集計設定',
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


