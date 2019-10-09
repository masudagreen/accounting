<?php

$vars = array(

	'portal' => array(
		'varsNavi' => array(
			'varsStatus' => array(
				'flagNow' => 'tree',
				'flagCakeUse' => 1,
				'flagTreeUse' => 1,
				'flagSearchUse' => 0,
				'flagFolderUse' => 0,
				'switchList' => array('tree'),
			),
			'varsDetail' => array(),
			'tree' => array(
				'varsFormat' => array(
					'id' => '',
					'flagType' => 'normalFormat',
					'numHeight' => 0,
					'numWidth' => 0,
					'flagHeaderLeftUse' => 1,
					'strTitleHeaderLeft' => 'メニュー',
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
					'varsStatus' => array(
						'flagUse' => 1,
						'flagMoveUse' => 0,
						'flagInsertUse' => 0,
						'flagFoldNow' => 0,
						'flagFoldUse' => 1,
						'flagFontUse' => 1,
						'flagCakeUse' => 1,
						'flagRemoveUse' => 0,
						'flagCheckUse' => 0,
						'flagCheckNow' => 1,
						'flagBarUse' => 1,
						'flagBtnBottomUse' => 0,
						'flagBtnUse' => 1,
						'flagLockUse' => 0,
						'flagLockNow' => 0,
						'flagPageUse' => 0,
						'flagInnerPageUse' => 0,
						'flagFindUse' => 1,
						'id' => 'tree',
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
						'flagFooderUse' => 0,
						'flagFooderLeftUse' => 0,

						'strTitleFooderLeft' => '',
						'strClassFooderLeft' => '',
						'flagFooderRightUse' => 0,

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
					'varsBtn' => array(),
					'varsDetail' => array(
						array(
							'id' => 'Admin', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
							'strTitle' => '管理者項目',
							'strClass' => 'codeLibBaseImgFolder',
							'vars' => array(),
							'child' => array(
								array(
									'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
									'strTitle' => '受信メール(IMAP)サーバ',
									'strClass' => 'codeLibBaseImgSheet',
									'vars' => array(
										'idTarget' => 'strHost',
										'varsEdit' => array( 'flagReloadUse' => 1, ),
										'varsBtn' => array(
											array(
												'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
												'id' => 'StrReset',
												'strTitle' => '初期化',
												'vars' => array( 'idTarget' => 'strReset', ),
											),
											array(
												'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
												'id' => 'StrHost',
												'strTitle' => '保存',
												'vars' => array( 'idTarget' => 'strHost', ),
											),
										),
										'varsDetail' => array(
											array(
												'flagMustUse' => 1,
												'id' => 'StrHost',
												'strTitle' => 'サーバ名' , 'strExplain' => 'メールから会計データを登録したい場合は、受信メール(IMAP)サーバのサーバ名の設定をしてください。<br> ※1つの事業体につき1つの受信メールサーバを設定してください。<br> ※会計データを収集するためだけの専用の受信メールサーバを用意してください。' , 'value' => '',
												'flagErrorNow' => 0,
												'arrayError' => array(
													array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
													array( 'flagCheck' =>  'attest', 'flagUse' =>  1, 'strComment' => array(
														'common' =>  '受信メール(IMAP)サーバに接続ができませんでした。',
														'double' =>  'もう既に当該受信メール(IMAP)サーバはシステム上で登録されているようです。',
													),),
												),
												'flagContentUse' => 0,
												'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
												'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 200,
												'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
												'arrayOption' => array(), 
												'flagFoldUse' => 0, 'flagFoldNow' => 0,
											),
											array(
												'flagMustUse' => 1,
												'id' => 'StrUser',
												'strTitle' => 'ユーザ名' , 'strExplain' => 'ユーザ名の設定を行ってください。' , 'value' => '',
												'flagErrorNow' => 0,
												'arrayError' => array(
													array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
													array( 'flagCheck' =>  'attest', 'flagUse' =>  1, 'strComment' => array('common' =>  '受信メール(IMAP)サーバに接続ができませんでした。', ),),
												),
												'flagContentUse' => 0,
												'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
												'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 200,
												'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
												'arrayOption' => array(), 
												'flagFoldUse' => 0, 'flagFoldNow' => 0,
											),
											array(
												'flagMustUse' => 1,
												'id' => 'StrPassword',
												'strTitle' => 'パスワード' , 'strExplain' => 'パスワードの設定を行ってください。<br>※ 設定されたパスワードはセキュリティの観点から表示されません。<br>※ 受信メールサーバの各種設定値が変更された場合はその都度設定してください。' , 'value' => '',
												'flagErrorNow' => 0,
												'arrayError' => array(
													array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
													array( 'flagCheck' =>  'attest', 'flagUse' =>  1, 'strComment' => array('common' =>  '受信メール(IMAP)サーバに接続ができませんでした。', ),),
												),
												'flagContentUse' => 0,
												'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
												'flagTag' => 'input', 'flagInputType' => 'password', 'numMaxlength' => 200,
												'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
												'arrayOption' => array(), 
												'flagFoldUse' => 0, 'flagFoldNow' => 0,
											),
											array(
												'flagMustUse' => 1,
												'id' => 'NumPort',
												'strTitle' => 'ポート番号' , 'strExplain' => '使用するポート番号の設定を行ってください。' , 'value' => '',
												'flagErrorNow' => 0,
												'arrayError' => array(
													array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。',  ),),
													array( 'flagCheck' =>  'attest', 'flagUse' =>  1, 'strComment' => array('common' =>  '受信メール(IMAP)サーバに接続ができませんでした。', ),),
												),
												'flagContentUse' => 0,
												'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
												'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 5,
												'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
												'arrayOption' => array(), 
												'flagFoldUse' => 0, 'flagFoldNow' => 0,
											),
											array(
												'flagMustUse' => 1,
												'id' => 'FlagSecure',
												'strTitle' => '接続保護' , 'strExplain' => '接続保護の設定を行ってください。' ,
												'value' => 'ssl',
												'flagErrorNow' => 0,
												'arrayError' => array(
													array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。',  ),),
													array( 'flagCheck' =>  'attest', 'flagUse' =>  1, 'strComment' => array('common' =>  '受信メール(IMAP)サーバに接続ができませんでした。', ),),
												),
												'flagContentUse' => 0,
												'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
												'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
												'unitWidth' => '%', 'numWidth' => 40, 'unitHeight' => '', 'numHeight' => 0,
												'arrayOption' => array(
													array( 'strTitle' => 'なし', 'value' => 'none', ),
													array( 'strTitle' => 'STARTTLS', 'value' => 'start', ),
													array( 'strTitle' => 'SSL/TLS', 'value' => 'ssl', ),
												),
												'flagFoldUse' => 0, 'flagFoldNow' => 0,
											),
											array(
												'flagMustUse' => 1,
												'id' => 'StrMail',
												'strTitle' => 'メールアドレス', 'strExplain' => '会計データの送付先となるメールアドレスを半角英数で設定してください。', 'value' => '',
												'flagErrorNow' => 0,
												'arrayError' => array(
													array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
													array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'mail', 'strComment' => array( 'common' => 'メールで通常、予定されていない文字が使用されているようです。', ), ),
													array( 'flagCheck' => 'format', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'mail', 'strComment' => array( 'common' => 'メールのフォーマットにエラーがあるようです。', ), ),
													array( 'flagCheck' => 'max', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'str', 'num' => 1000, 'strComment' => array( 'common' => '入力文字数が多すぎるようです。', ),),
													array( 'flagCheck' => 'attest', 'flagUse' => 1, 'strComment' => array(
														'strMail' => '既にそのメールアドレスは使われているようです。',
													),),
												),
												'flagContentUse' => 0,
												'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
												'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 1000,
												'numWidth' => 90, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
												'arrayOption' => array(), 
											),
										),
									),
									'child' => array(),
								),

								array(
									'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
									'strTitle' => '受信可能メール',
									'strClass' => 'codeLibBaseImgSheet',
									'vars' => array(
										'idTarget' => 'jsonMail',
										'varsEdit' => array( 'flagReloadUse' => 1, ),
										'varsBtn' => array(
											array(
												'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
												'id' => 'JsonMail',
												'vars' => array( 'idTarget' => 'jsonMail', ),
												'strTitle' => '保存',
											),
										),
										'varsDetail' => array(
											array(
												'flagMustUse' => 1,
												'id' => 'JsonMail',
												'strTitle' => '受信可能メールアドレス', 'strExplain' => '受信サーバ経由で会計データをインポートしたいメールアドレスがあれば設定してください。', 'value' => '',
												'flagErrorNow' => 0,
												'arrayError' => array(
													array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'empty', 'strComment' => array( 'common' => '無記入項目があるようです。', ), ),
													array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'mail', 'strComment' => array( 'common' => 'メールアドレスで通常、予定されていない文字が含まれているようです。', ), ),
													array( 'flagCheck' => 'format', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'mail', 'strComment' => array( 'common' => 'メールアドレスのフォーマットにエラーがあるようです。', ), ),
													array( 'flagCheck' => 'max', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'str', 'num' => 1000, 'strComment' => array( 'common' => '入力文字数が多すぎるようです。', ), 	),
												),
												'flagContentUse' => 1,
												'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 0,
												'unitWidth' => '%', 'numWidth' => 90, 'unitHeight' => 'px', 'numHeight' => 100,
												'arrayOption' => array(), 
												'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
												'varsFormList' => array(
													'varsStatus' => array( 'flagSortUse' => 1, 'flagCopyUse' => 1, 'flagRemoveUse' => 1, 'flagAddUse' => 1, 'flagEditUse' => 0, 'flagFormUse' => 1, 'flagBtnUse' => 0, ),
													'templateDetail' => array( 'id' => '', 'flagSortUse' => 1, 'flagCopyUse' => 1, 'flagRemoveUse' => 1, 'flagEditUse' => 0, 'flagFormUse' => 1, 'flagBtnUse' => 0, 'numSort' => 0, 'value' => '', ),
													'varsDetail' => array(),
												),
											),
											array(
												'flagMustUse' => 1,
												'id' => 'JsonMailHost',
												'strTitle' => '受信可能メールホスト', 'strExplain' => '受信サーバ経由で会計データをインポートしたいメールホストがあれば設定してください。', 'value' => '',
												'flagErrorNow' => 0,
												'arrayError' => array(
													array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'empty', 'strComment' => array( 'common' => '無記入項目があるようです。', ), ),
													array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'mailHost', 'strComment' => array( 'common' => 'メールホストで通常、予定されていない文字が含まれているようです。', ),),
													array( 'flagCheck' => 'format', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'mailHost', 'strComment' => array( 'common' => 'メールホストのフォーマットにエラーがあるようです。', ), ),
													array( 'flagCheck' => 'max', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'str', 'num' => 1000, 'strComment' => array( 'common' => '入力文字数が多すぎるようです。', ), ),
												),
												'flagContentUse' => 1,
												'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
												'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 0,
												'unitWidth' => '%', 'numWidth' => 90, 'unitHeight' => 'px', 'numHeight' => 100,
												'arrayOption' => array(), 
												'varsFormList' => array(
													'varsStatus' => array( 'flagSortUse' => 1, 'flagCopyUse' => 1, 'flagRemoveUse' => 1, 'flagAddUse' => 1, 'flagEditUse' => 0, 'flagFormUse' => 1, 'flagBtnUse' => 0, ),
													'templateDetail' => array( 'id' => '', 'flagSortUse' => 1, 'flagCopyUse' => 1, 'flagRemoveUse' => 1, 'flagEditUse' => 0, 'flagFormUse' => 1, 'flagBtnUse' => 0, 'numSort' => 0, 'value' => '', ),
													'varsDetail' => array(),
												),
											),
										),
									),
									'child' => array(),
								),
							),
						),
						array(
							'id' => 'User', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
							'strTitle' => 'ユーザ項目',
							'strClass' => 'codeLibBaseImgFolder',
							'vars' => array(
								'idTarget' => 'user',
							),
							'child' => array(
								array(
									'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
									'strTitle' => 'インポートフィルタ',
									'strClass' => 'codeLibBaseImgDetail',
									'vars' => array(
										'idTarget' => 'logImportWindow',
									),
									'child' => array(),
								),
								array(
									'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
									'strTitle' => 'フィルタリトライ',
									'strClass' => 'codeLibBaseImgDetail',
									'vars' => array(
										'idTarget' => 'logImportRetryWindow',
									),
									'child' => array(),
								),
								array(
									'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
									'strTitle' => '家事按分',
									'strClass' => 'codeLibBaseImgDetail',
									'vars' => array(
										'idTarget' => 'logHouseWindow',
									),
									'child' => array(),
								),

							),
						),
					),
				),
			),
			'item' => array(),
			'folder' => array(),
		),
		'varsDetail' => array(
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
			'varsStatus' => array(
				'flagUse' => 1,
				'flagNow' => 'form',
				'flagCakeUse' => 0,
				'flagViewUse' => 0,
				'flagFormUse' => 1,
				'switchList' => array('form'),
			),
			'varsBtn' => array(),
			'varsPage' => array(),
			'varsDetail' => array(),
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
	'child' => array(
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
			'flagMenuShowUse' => 1,
			'numWidthTitle' => 0,
			'numLeft' => 50,
			'numTop' => 50,
			'numWidth' => 950,
			'numHeight' => 600,
			'numWidthMin' => 800,
			'numHeightMin' => 600,
			'numZIndex' => 0
		),
	),

);
