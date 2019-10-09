<?php

$vars = array(
	'varsItem'  => array(),
	'pathCss' => '',
	'portal' => array(
		'varsNavi' => array(),
		'varsList' => array(),
		'varsDetail' => array(
			'varsStart' => array(
				'strTitle' => 'ファイル明細インポート',
				'strClass' => 'codeLibBaseImgDb',
				'varsEdit' => array( 'flagReloadUse' => 1, 'flagPreferenceUse' => 1,),
			),
			'varsEnd' => array(
				'strTitle' => '処理完了',
				'strClass' => 'codeLibBaseImgSheet',
				'varsEdit' => array(),
				'varsBtn' => array(
					array(
						'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
						'id' => 'Retry',
						'vars' => array( 'flagRetry' => 1, ),
						'strTitle' => 'フィルタリトライ',
					),
					array(
						'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
						'id' => 'Log',
						'vars' => array( 'flagLog' => 1, ),
						'strTitle' => '仕訳帳',
					),
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
				'templateDetail' => array(
					array(
						'flagMustUse' => 0,
						'id' => 'End',
						'strTitle' => '', 'strExplain' => '', 'value' => '',
						'flagErrorNow' => 0,
						'arrayError' => array(),
						'flagContentUse' => 0,
						'flagCommentUse' => 1, 'strCommentTitle' => '', 'strComment' => '処理が完了しました。',
						'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 0,
						'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
						'arrayOption' => array(), 
						'flagFoldUse' => 0, 'flagFoldNow' => 0,
						'flagHideUse' => 1, 'flagHideNow' => 0,
					),
					array(
						'flagMustUse' => 0,
						'id' => 'Table',
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
								'numMargin' => 0,
								'numPadding' => 0,
								'numHeight' => 140,
								'numWidth' => '90',
								'unitWidth' => '%'
							),
							'varsDetail' => array(
								'strHtml' => '',
							),
						),
					),
				),
				'varsDetail' => array(),
			),
			'varsStatus' => array(
				'flagUse' => 1,
				'flagNow' => 'form',
				'flagCakeUse' => 1,
				'flagFoldUse' => 0,
				'flagViewUse' => 0,
				'flagFormUse' => 1,
				'switchList' => array('form'),
			),
			'varsEdit' => array( 'flagReloadUse' => 1, 'flagPreferenceUse' => 1,),
			'templateDetail' => array(
				array(
					'flagMustUse' => 0,
					'id' => 'DummyEditCurrent',
					'strTitle' => '',
					'strExplain' => '',
					'value' => '',
					'flagErrorNow' => 0,
					'arrayError' => array(),
					'flagContentUse' => 0,
					'flagCommentUse' => 1, 'strCommentTitle' => '【お知らせ】', 'strComment' => '<span class="codeLibBaseFontOrange" style="float:none;">※ 既にデータが確定しているため使用できません。</span>',
					'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 100,
					'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(), 
					'flagFoldUse' => 1, 'flagFoldNow' => 0,
				),
				array(
					'flagMustUse' => 1,
					'id' => 'IdLogAccount',
					'strTitle' => '金融機関(口座)' , 'strExplain' => '金融機関(口座)の選択を行ってください。' ,
					'value' => '',
					'flagErrorNow' => 0,
					'arrayError' => array(
						array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '選択漏れがあるようです。',  ),),
					),
					'flagContentUse' => 0,
					'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'selectShortCut', 'flagInputType' => '', 'numMaxlength' => 0,
					'numWidth' => 90, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(
						array('strTitle' => '未選択', 'value' => ''),
					),
					'varsFormTemp' => array(
						'varsStatus' => array(
							'numLeft' => 0,
							'numTop' => 0,
						),
						'varsDetail' => array(
							'flagTag'       => '',
							'flagInputType' => '',
							'numMaxlength'  => 100,
							'numWidth'      => 0,
							'unitWidth'     => 'px',
							'numHeight'     => 0,
							'unitHeight'    => 'px',
							'arrayOption'   => array(),
							'value'         => '',
							'vars'          => array(),
						),
					),
					'flagFoldUse' => 0, 'flagFoldNow' => 0,
				),
				array(
					'flagMustUse' => 1,
					'id' => 'Upload',
					'strTitle' => 'ファイルアップロード', 'strExplain' => '　※ 複数選択可。', 'value' => 'dummy',
					'flagErrorNow' => 0,
					'arrayError' => array(
						array( 'flagCheck' => 'attest', 'flagUse' => 1, 'strComment' => array(
							'strFileType' => 'インポート対応していないファイル拡張子が選択されたようです。',
							'strBlank' => 'ファイルが選択されていないようです。',
							'strError' => 'アップロードできなかったファイルがあったようです。',
							'strSize' => '許容されているファイルサイズを超えていたファイルがあったようです。',
						),),
					),
					'flagContentUse' => 0,
					'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'input', 'flagInputType' => 'file', 'numMaxlength' => 0, 'flagMultiple' => 1,
					'numWidth' => 90, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(), 
					'arrayHidden' => array(),
				),
			),
			'varsPage' => array(),
			'varsDetail' => array(),
			'varsBtn' => array(
				array(
					'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
					'id' => 'SaveBtn',
					'vars' => array( 'idTarget' => 'save'),
					'strTitle' => 'インポート',
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
				'varsEdit' => array( 'flagReloadUse' => 1, 'flagPreferenceUse' => 1,),
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
						'numPriority' => 0,
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
					),
				),
			),
		),
	),
	'child' => array(),
);


