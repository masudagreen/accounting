<?php

$vars = array(
	'token' => '',
	'strClass' => 'Core',
	'idModule' => 'Base',
	'varsItem' => array(
		'strDomain' => 'システムメールアドレスに関して、システムを設置したドメイン( <%strDomainUrl%> )とメールアドレスに含まれるドメイン( <%strDomainMail%> )が違いますが、よろしいですか？',
	),
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
							'vars' => array(
							),
							'child' => array(
								array(
									'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
									'strTitle' => 'システム',
									'strClass' => 'codeLibBaseImgFolder',
									'vars' => array(),
									'child' => array(
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => '運用ステータス',
											'strClass' => 'codeLibBaseImgSheet',
											'vars' => array(
												'idTarget' => 'flagMaintenance',
												'varsEdit' => array( 'flagReloadUse' => 1, ),
												'varsBtn' => array(
													array(
														'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
														'id' => 'FlagMaintenance',
														'vars' => array( 'idTarget' => 'flagMaintenance',  ),
														'strTitle' => '保存',
													),
												),
												'varsDetail' => array(
													array(
														'flagMustUse' => 1,
														'id' => 'FlagMaintenance',
														'strTitle' => '運用ステータス',
														'strExplain' => '
															メンテナンス中は、ログイン中のウェブマスター、メンテナンス要員以外アクセスできなくなります。
															<br>　※ ウェブマスターやメンテナンス要員がログイン中であることを確認してから運用ステータスを変更してください。
															<br>　※ メンテナンス中は、API機能はすべて停止します。
														', 'value' => 0,
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字以外の文字が混入したようです。', ), ),
														),
														'flagContentUse' => 0,
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
														'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
														'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
														'arrayOption' => array(
															array( 'strTitle' => '稼動中', 'value' => 0, ),
															array( 'strTitle' => 'メンテナンス中', 'value' => 1, ),
														),
													),
												),
											),
											'child' => array(),
										),
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => 'システム運営者',
											'strClass' => 'codeLibBaseImgSheet',
											'vars' => array(
												'idTarget' => 'strSiteName',
												'varsEdit' => array( 'flagReloadUse' => 1, ),
												'varsBtn' => array(
													array(
														'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
														'id' => 'StrSiteName',
														'strTitle' => '保存',
														'vars' => array( 'idTarget' => 'strSiteName', ),
													),
												),
												'varsDetail' => array(
													array(
														'flagMustUse' => 1,
														'id' => 'StrSiteName',
														'strTitle' => 'システム運営者名', 'strExplain' => 'システム運営者名を50文字以内に設定してください。', 'value' => '',
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
															array(	'flagCheck' => 'max', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'str', 'num' => 50, 'strComment' => array( 'common' => '入力文字数が50文字を超えているようです。', ),),
														),
														'flagContentUse' => 0,
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
														'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 50,
														'numWidth' => 90, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
														'arrayOption' => array(),
													),
													array(
														'flagMustUse' => 1,
														'id' => 'StrSiteMailPc',
														'strTitle' => '送信用システムメールアドレス', 'strExplain' => '運用しているサーバでメール送信が許可されているメールアドレスを半角英数で設定してください。<br>※通常、URLのドメインとメールアドレスに含まれるドメインが異なる場合メール送信が正しく処理されません。', 'value' => '',
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'mail', 'strComment' => array( 'common' => 'メールで通常、予定されていない文字が使用されているようです。', ), ),
															array( 'flagCheck' => 'format', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'mail', 'strComment' => array( 'common' => 'メールのフォーマットにエラーがあるようです。', ), ),
															array( 'flagCheck' => 'max', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'str', 'num' => 1000, 'strComment' => array( 'common' => '入力文字数が多すぎるようです。', ),),
															array( 'flagCheck' => 'attest', 'flagUse' => 1, 'strComment' => array(
																'strSiteMailPc' => '既にそのメールアドレスは使われているようです。',
															),),
														),
														'flagContentUse' => 0,
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
														'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 1000,
														'numWidth' => 90, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
														'arrayOption' => array(),
													),
													array(
														'flagMustUse' => 0,
														'id' => 'StrSiteUrl',
														'strTitle' => '連絡先URL', 'strExplain' => '連絡先となるURLがあれば半角英数で設定してください。', 'value' => '',
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'url', 'strComment' => array( 'common' => 'URLで通常、予定されていない文字が使用されているようです。', ), ),
															array( 'flagCheck' => 'format', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'url', 'strComment' => array( 'common' => 'URLのフォーマットにエラーがあるようです。', ), ),
															array( 'flagCheck' => 'max', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'str', 'num' => 1000, 'strComment' => array( 'common' => '入力文字数が多すぎるようです。', ),),
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
											'strTitle' => 'メンテナンス要員',
											'strClass' => 'codeLibBaseImgSheet',
											'vars' => array(
												'idTarget' => 'arrCommaIdAccountMaintenance',
												'varsEdit' => array( 'flagReloadUse' => 1, ),
												'varsBtn' => array(
													array(
														'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
														'id' => 'ArrCommaIdAccountMaintenance',
														'vars' => array( 'idTarget' => 'arrCommaIdAccountMaintenance', ),
														'strTitle' => '保存',
													),
												),
												'varsDetail' => array(
													array(
														'flagMustUse' => 0,
														'id' => 'ArrCommaIdAccountMaintenance',
														'strTitle' => 'メンテナンス要員', 'strExplain' => 'メンテナンス中にアクセスを許可するユーザを設定してください。', 'value' => '',
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'comma', 'flagType' => 'empty', 'strComment' => array( 'common' => '無記入項目があるようです。', ), ),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'comma', 'flagType' => 'num', 'strComment' => array( 'common' => '予定されていない処理が起こりました。システムエラー。', ), ),
														),
														'flagContentUse' => 1,
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
														'flagTag' => 'textarea', 'flagInputType' => '', 'numMaxlength' => 0,
														'unitWidth' => '%', 'numWidth' => 90, 'unitHeight' => 'px', 'numHeight' => 100,
														'arrayOption' => array(),
														'varsFormArea' => array(
															'varsStatus' => array(
																'numWidth' => 0,
																'numHeight' => 200,
																'varsFormArea' => 0,
															),
															'templateDetail' => array(
																'id' => 'Tmpl', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
																'strTitle' => '',
																'strClass' => 'codeLibBaseImgSheet',
																'vars' => array(
																	'idTarget' => '',
																),
																'child' => array()
															),
															'varsChoice' => array(
																'idTarget' => 'Account', 'idModule' => 'Base', 'flagCheckUse' => 1,
																'flagId' => 'ArrCommaIdAccountMaintenance',
															),
															'varsTree' => array(
																'varsStatus' => array(
																	'flagUse' => 1,
																	'flagMoveUse' => 0,
																	'flagInsertUse' => 0,
																	'flagSortUse' => 0,
																	'flagFoldNow' => 0,
																	'flagFoldUse' => 0,
																	'flagCakeUse' => 0,
																	'flagCheckUse' => 0,
																	'flagCheckNow' => 1,
																	'flagBarUse' => 1,
																	'flagInnerBtnBottomUse' => 0,
																	'flagBtnBottomUse' => 0,
																	'flagBtnUse' => 0,
																	'flagFindUse' => 0,
																	'flagAddUse' => 0,
																	'flagEditUse' => 0,
																	'flagEditNow' => 0,
																	'flagRemoveUse' => 1,
																	'flagRemoveNow' => 1,
																	'flagPageUse' => 1,
																	'flagLinkUse' => 1,
																	'strLinkTitle' => '選択',
																	'strRemoveTitle' => '全選択解除',
																	'flagInnerPageUse' => 1,
																	'id' => 'JsonTree',
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

																	'flagHeaderLeftWidth' => 0,
																	'numWidthHeaderLeft' => 0,
																	'flagHeaderRightWidth' => 0,
																	'numWidthHeaderRight' => 0,
																	'flagFooderLeftWidth' => 0,
																	'numWidthFooderLeft' => 0,
																	'flagFooderRightWidth' => 0,
																	'numWidthFooderRight' => 0,
																),
																'varsFind' => array(),
																'varsBtnBottom' => array(),
																'varsPage' => array(
																	'varsStatus' => array(
																		'flagStatusUse' => 1,
																		'flagTopUse' => 1,
																		'flagEndUse' => 1,
																		'flagNextUse' => 1,
																		'flagPrevUse' => 1,
																		'numRows' => 0,
																		'numLotNow' => 0,
																	),
																),
																'varsDetail' => array(),
															),
															'varsDetail' => array(),
														),
													),
												),
											),
											'child' => array(),
										),
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => 'ログイン通知',
											'strClass' => 'codeLibBaseImgSheet',
											'vars' => array(
												'idTarget' => 'flagLoginMail',
												'varsEdit' => array( 'flagReloadUse' => 1, ),
												'varsBtn' => array(
													array(
														'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
														'id' => 'FlagLoginMail',
														'vars' => array( 'idTarget' => 'flagLoginMail',  ),
														'strTitle' => '保存',
													),
												),
												'varsDetail' => array(
													array(
														'flagMustUse' => 1,
														'id' => 'FlagLoginMail',
														'strTitle' => 'ログイン通知',
														'strExplain' => '
															ログインフォームやAPIを通じてアカウントに対してセッションが発行された場合、管理者にメールで通知します。
														', 'value' => 0,
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字以外の文字が混入したようです。', ), ),
														),
														'flagContentUse' => 0,
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
														'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
														'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
														'arrayOption' => array(
															array( 'strTitle' => '通知しない', 'value' => 0, ),
															array( 'strTitle' => '通知する', 'value' => 1, ),
														),
													),
												),
											),
											'child' => array(),
										),
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => 'アクセス通知',
											'strClass' => 'codeLibBaseImgSheet',
											'vars' => array(
												'idTarget' => 'flagAccessUnknownMail',
												'varsEdit' => array( 'flagReloadUse' => 1, ),
												'varsBtn' => array(
													array(
														'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
														'id' => 'FlagAccessUnknownMail',
														'vars' => array( 'idTarget' => 'flagAccessUnknownMail',  ),
														'strTitle' => '保存',
													),
												),
												'varsDetail' => array(
													array(
														'flagMustUse' => 1,
														'id' => 'FlagAccessUnknownMail',
														'strTitle' => 'アクセス通知',
														'strExplain' => '
															未知のIPアドレスが確認された場合、管理者にメールで通知します。
														', 'value' => 0,
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字以外の文字が混入したようです。', ), ),
														),
														'flagContentUse' => 0,
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
														'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
														'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
														'arrayOption' => array(
															array( 'strTitle' => '通知しない', 'value' => 0, ),
															array( 'strTitle' => '通知する', 'value' => 1, ),
														),
													),
													array(
														'flagMustUse' => 0,
														'id' => 'FlagAccessUnknownMailReset',
														'strTitle' => 'リセット',
														'strExplain' => '
															アクセス通知を有効にしてから今まで検知したIPアドレスをすべてリセットすることができます。
														',
														'value' => 0,
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字以外の文字が混入したようです。', ), ),
														),
														'flagContentUse' => 0,
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
														'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
														'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
														'arrayOption' => array(
															array( 'strTitle' => 'リセットしない', 'value' => 0, ),
															array( 'strTitle' => 'リセットする', 'value' => 1, ),
														),
													),
												),
											),
											'child' => array(),
										),


/*
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => 'プラグイン再構築',
											'strClass' => 'codeLibBaseImgSheet',
											'vars' => array(
												'idTarget' => 'rebuild',
												'varsEdit' => array( 'flagReloadUse' => 1, ),
												'varsBtn' => array(
													array(
														'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
														'id' => 'Rebuild',
														'vars' => array( 'idTarget' => 'rebuild', ),
														'strTitle' => '再構築開始',
													),
												),
												'templateDetail' => array(
													'flagMustUse' => 0,
													'id' => '',
													'strTitle' => '', 'strExplain' => '', 'value' => 0,
													'flagErrorNow' => 0,
													'arrayError' => array(),
													'flagContentUse' => 0,
													'flagCommentUse' => 1, 'strComment' => '『<span class="codeLibBaseFontTypeCheck" style="float:none;"> <%replace%> </span>』  というプラグインを発見しました。',
													'flagTag' => '', 'flagInputType' => '', 'numMaxlength' => 0,
													'unitWidth' => '', 'numWidth' => 0, 'unitHeight' => '', 'numHeight' => 0,
													'arrayOption' => array(),
												),
												'varsDetail' => array(
													array(
														'flagMustUse' => 0,
														'id' => 'DummyNg',
														'strTitle' => '', 'strExplain' => '', 'value' => 0,
														'flagErrorNow' => 0,
														'arrayError' => array(),
														'flagContentUse' => 0,
														'flagCommentUse' => 1, 'strComment' => '<span class="codeLibBaseFontTypeCheck" style="float:none;">新しいプラグインを発見できませんでした。</span><br>　※ プラグインを追加したい場合は、事前に所定の位置に配置してください。',
														'flagTag' => '', 'flagInputType' => '', 'numMaxlength' => 0,
														'unitWidth' => '', 'numWidth' => 0, 'unitHeight' => '', 'numHeight' => 0,
														'arrayOption' => array(),
													),
													array(
														'flagMustUse' => 0,
														'id' => 'DummyOk',
														'strTitle' => '', 'strExplain' => '', 'value' => 0,
														'flagErrorNow' => 0,
														'arrayError' => array(),
														'flagContentUse' => 0,
														'flagCommentUse' => 1, 'strComment' => '新たなプラグインを追加する前に、必ず<span class="codeLibBaseFontTypeCheck" style="float:none;">バックアップ</span>を行ってください。',
														'flagTag' => '', 'flagInputType' => '', 'numMaxlength' => 0,
														'unitWidth' => '', 'numWidth' => 0, 'unitHeight' => '', 'numHeight' => 0,
														'arrayOption' => array(),
													),
												),
											),
											'child' => array(),
										),
*/
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => 'システムログ',
											'strClass' => 'codeLibBaseImgDetail',
											'vars' => array(
												'idTarget' => 'logWindow',
											),
											'child' => array(),
										),


									),
								),

/*
								array(
									'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
									'strTitle' => 'フォーム表示',
									'strClass' => 'codeLibBaseImgFolder',
									'vars' => array(
									),
									'child' => array(
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => '新規登録フォーム',
											'strClass' => 'codeLibBaseImgSheet',
											'vars' => array(
												'idTarget' => 'flagSign',
												'varsEdit' => array( 'flagReloadUse' => 1, ),
												'varsBtn' => array(
													array(
														'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
														'id' => 'FlagSign',
														'vars' => array( 'idTarget' => 'flagSign', ),

														'strTitle' => '保存',
													),
												),
												'varsDetail' => array(
													array(
														'flagMustUse' => 1,
														'id' => 'FlagSign',
														'strTitle' => '新規登録フォーム', 'strExplain' => '新規登録フォームを表示するかどうか。', 'value' => 1,
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ), ),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字以外の文字が混入したようです。', ), ),
														),
														'flagContentUse' => 0,
														'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
														'unitWidth' => '%', 'numWidth' => 40, 'unitHeight' => '', 'numHeight' => 0,
														'arrayOption' => array(
															array( 'strTitle' => '表示する', 'value' => 1, ),
															array( 'strTitle' => '表示しない', 'value' => 0, ),
														),
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
													),
												),
											),
											'child' => array(),
										),

										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => 'パスワード再発行フォーム',
											'strClass' => 'codeLibBaseImgSheet',
											'vars' => array(
												'idTarget' => 'flagForgot',
												'varsEdit' => array( 'flagReloadUse' => 1, ),
												'varsBtn' => array(
													array(
														'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
														'id' => 'FlagForgot',
														'vars' => array( 'idTarget' => 'flagForgot', ),

														'strTitle' => '保存',
													),
												),
												'varsDetail' => array(
													array(
														'flagMustUse' => 1,
														'id' => 'FlagForgot',
														'strTitle' => 'パスワード再発行フォーム', 'strExplain' => 'パスワード再発行フォームを表示するかどうか。', 'value' => 1,
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ), ),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字以外の文字が混入したようです。', ), ),
														),
														'flagContentUse' => 0,
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
														'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
														'unitWidth' => '%', 'numWidth' => 40, 'unitHeight' => '', 'numHeight' => 0,
														'arrayOption' => array(
															array( 'strTitle' => '表示する', 'value' => 1, ),
															array( 'strTitle' => '表示しない', 'value' => 0, ),
														),
													),
												),
											),
											'child' => array(),
										),
									),
								),
*/
								array(
									'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
									'strTitle' => '外部連携',
									'strClass' => 'codeLibBaseImgFolder',
									'vars' => array(
									),
									'child' => array(
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => 'バージョン更新',
											'strClass' => 'codeLibBaseImgSheet',
											'vars' => array(
												'idTarget' => 'version',
												'varsEdit' => array( 'flagReloadUse' => 1, ),
												'varsBtn' => array(
													array(
														'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
														'id' => 'Version',
														'vars' => array( 'idTarget' => 'version', ),
														'strTitle' => '更新確定',
													),
													array(
														'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
														'id' => 'Download',
														'vars' => array( 'idTarget' => 'version', ),
														'strTitle' => '自動更新',
													),
													array(
														'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
														'id' => 'FlagTrue',
														'vars' => array( 'idTarget' => 'version', ),
														'strTitle' => 'バージョン更新機能を有効にする',
													),
													array(
														'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
														'id' => 'FlagFalse',
														'vars' => array( 'idTarget' => 'version', ),
														'strTitle' => 'バージョン更新機能を無効にする',
													),
												),
												'strLastVersion' => '',
												'varsDetail' => array(
													array(
														'flagMustUse' => 0,
														'id' => 'DummyUpdate',
														'strTitle' => '', 'strExplain' => '', 'value' => 0,
														'flagErrorNow' => 0,
														'arrayError' => array(),
														'flagContentUse' => 0,
														'flagCommentUse' => 1, 'strComment' => '<span class="codeLibBaseFontTypeCheck" style="float:none;">【通知】バージョン更新の準備が整いました。更新確定ボタンを押してください。</span>',
														'flagTag' => '', 'flagInputType' => '', 'numMaxlength' => 0,
														'unitWidth' => '', 'numWidth' => 0, 'unitHeight' => '', 'numHeight' => 0,
														'arrayOption' => array(),
													),
													array(
														'flagMustUse' => 0,
														'id' => 'DummyNone',
														'strTitle' => '', 'strExplain' => '', 'value' => 0,
														'flagErrorNow' => 0,
														'arrayError' => array(),
														'flagContentUse' => 0,
														'flagCommentUse' => 1,
														'strComment' => '

															<p style="margin:5px;">
															現在のバージョンは、『 <span class="codeLibBaseFontBold" style="float:none;"><%replace%></span> 』 です。
															</p>
															<p class="codeLibBaseFontSizeSeventy">　※新しいバージョンはリリースされていないようです。</p>
															<p class="codeLibBaseFontSizeSeventy">　※バージョンが更新されたらすぐに更新してください。</p>
															<p class="codeLibBaseFontSizeSeventy">　※バージョン更新を放置すると致命的なバグやセキュリティリスクを招く可能性があります。</p>
<table cellspacing="1" cellpadding="3" border="0" bgcolor="#cccccc" width="95%" style="margin:5px;font-size:10px;">
	<tbody>
		<tr>
			<td class="codeLibBaseTableColumn" style="width:100px;">機能概要</td>
			<td class="codeLibBaseTableRow">オフィシャルサイトから新バージョンのリリースを検知しバージョンを更新します。</td>
		</tr>
		<tr>
			<td class="codeLibBaseTableColumn">送信データ</td>
			<td class="codeLibBaseTableRow">現在のバージョン番号をオフィシャルサイトに送信します。</td>
		</tr>
		<tr>
			<td class="codeLibBaseTableColumn">受信データ</td>
			<td class="codeLibBaseTableRow">最新バージョン番号や最新バージョンをダウンロードします。</td>
		</tr>
		<tr>
			<td class="codeLibBaseTableColumn">送信データ用途</td>
			<td class="codeLibBaseTableRow">送信データは、匿名データとして集計しバージョン更新の必要性を周知するために用います。</td>
		</tr>
	</tbody>
</table>
														',
														'flagTag' => '', 'flagInputType' => '', 'numMaxlength' => 0,
														'unitWidth' => '', 'numWidth' => 0, 'unitHeight' => '', 'numHeight' => 0,
														'arrayOption' => array(),
													),
													array(
														'flagMustUse' => 0,
														'id' => 'DummyUse',
														'strTitle' => '', 'strExplain' => '', 'value' => 0,
														'flagErrorNow' => 0,
														'arrayError' => array(),
														'flagContentUse' => 0,
														'flagCommentUse' => 1,
														'strComment' => '
															<span class="codeLibBaseFontTypeCaution" style="float:none;">
																※バージョン更新が有効になっていません。
															</span>
															<p style="margin:5px;">
															現在のバージョンは、『 <span class="codeLibBaseFontBold" style="float:none;"><%replace%></span> 』 です。
															</p>
															<p class="codeLibBaseFontSizeSeventy">　※バージョン更新を放置すると致命的なバグやセキュリティリスクを招く可能性があります。</p>
<table cellspacing="1" cellpadding="3" border="0" bgcolor="#cccccc" width="95%" style="margin:5px;font-size:10px;">
	<tbody>
		<tr>
			<td class="codeLibBaseTableColumn" style="width:100px;">機能概要</td>
			<td class="codeLibBaseTableRow">オフィシャルサイトから新バージョンのリリースを検知しバージョンを更新します。</td>
		</tr>
		<tr>
			<td class="codeLibBaseTableColumn">送信データ</td>
			<td class="codeLibBaseTableRow">現在のバージョン番号をオフィシャルサイトに送信します。</td>
		</tr>
		<tr>
			<td class="codeLibBaseTableColumn">受信データ</td>
			<td class="codeLibBaseTableRow">最新バージョン番号や最新バージョンをダウンロードします。</td>
		</tr>
		<tr>
			<td class="codeLibBaseTableColumn">送信データ用途</td>
			<td class="codeLibBaseTableRow">送信データは、匿名データとして集計しバージョン更新の必要性を周知するために用います。</td>
		</tr>
	</tbody>
</table>

														',
														'flagTag' => '', 'flagInputType' => '', 'numMaxlength' => 0,
														'unitWidth' => '', 'numWidth' => 0, 'unitHeight' => '', 'numHeight' => 0,
														'arrayOption' => array(),
													),
													array(
														'flagMustUse' => 0,
														'id' => 'DummyChange',
														'strTitle' => '', 'strExplain' => '', 'value' => 0,
														'flagErrorNow' => 0,
														'arrayError' => array(),
														'flagContentUse' => 0,
														'flagCommentUse' => 1,
														'strComment' => '
															<span class="codeLibBaseFontTypeCheck" style="float:none;">
																【通知】新バージョンリリース。早急に更新してください。
															</span>
															<p class="codeLibBaseFontSizeSeventy" style="margin-top:5px;font-weight: bold;">
																■ 以下の手順で行ってください。
															</p>
															<p class="codeLibBaseFontSizeSeventy" style="margin:5px;">
																(1) 自動更新ボタンを押してください。
																<br>
																(2) バージョン更新の手続完了後、自動でページ更新されます。
															</p>
															<p class="codeLibBaseFontSizeSeventy" style="margin:5px;">
																※ 上記手続中に、余計な動作が入ると不具合が生じる可能性があります。
																<br>
																※ 一度バージョン更新をすると元のバージョンに戻せません。
																<br>
																※ バージョン更新前にバックアップ(データベース及びシステムファイル等)を推奨します。
																<br>
																※ 現在のバージョンは、『 <%replace%> 』 です。
																<br>
																※ 新しいバージョンは、『 <%replaceNew%> 』 です。
															</p>
														',
														'flagTag' => '', 'flagInputType' => '', 'numMaxlength' => 0,
														'unitWidth' => '', 'numWidth' => 0, 'unitHeight' => '', 'numHeight' => 0,
														'arrayOption' => array(),
													),
													array(
														'flagMustUse' => 0,
														'id' => 'DummyUpdateNow',
														'strTitle' => '', 'strExplain' => '', 'value' => 0,
														'flagErrorNow' => 0,
														'arrayError' => array(),
														'flagContentUse' => 0,
														'flagCommentUse' => 1,
														'strComment' => '
															<span class="codeLibBaseFontTypeCheck" style="float:none;">
																【通知】現在、更新手続中です... 何もせずしばらくお待ちください。
															</span>
														',
														'flagTag' => '', 'flagInputType' => '', 'numMaxlength' => 0,
														'unitWidth' => '', 'numWidth' => 0, 'unitHeight' => '', 'numHeight' => 0,
														'arrayOption' => array(),
														'flagHideUse' => 1, 'flagHideNow' => 1,
													),
													array(
														'flagMustUse' => 0,
														'id' => 'DummyError',
														'strTitle' => '', 'strExplain' => '', 'value' => 0,
														'flagErrorNow' => 0,
														'arrayError' => array(),
														'flagContentUse' => 0,
														'flagCommentUse' => 1,
														'strComment' => '
																<span class="codeLibBaseFontRed" style="float:none;font-weight: bold;">
																	【通知】更新ファイルのダウンロードに失敗したようです。
																</span>
																<p style="margin-top:5px;font-weight: bold;">
																	■ 更新ファイルのダウンロードに失敗した場合は、以下の手順で行ってください。
																</p>
																<p  class="codeLibBaseFontSizeSeventy" style="margin:5px;">
																	<p class="codeLibBaseFontOrange" >※ 必ず現在の『バージョン更新』項目を表示した状態で作業をしてください。</p>
																	(1) 現在の『バージョン更新』項目をあらかじめ表示しておきます。
																	<br>
																	(2) オフィシャルサイトで新しいバージョンを入手します。
																	<br>
																	(3) 入手したzipファイルを解凍しFTPで新しいバージョンを上書きアップロードします。
																	<br>
																	(4) アップロードが完了したらボックス上部にあるツールエリアの更新ボタンを押します。
																	<br>
																	(5) ボックス下部に更新確定ボタンが表示されたら更新確定ボタンを押します。
																	<br>
																	(6) 手続完了後、自動でページ更新されます。
																</p>
																<p  class="codeLibBaseFontSizeSeventy" style="margin:5px;">
																※ 上記一連の手順を間違えたり、余計な動作が入ると重大な不具合が生じる可能性があります。
																<br>
																※ 作業前にバックアップ(データベース及びシステムファイル等)を推奨します。
																</p>
														',
														'flagTag' => '', 'flagInputType' => '', 'numMaxlength' => 0,
														'unitWidth' => '', 'numWidth' => 0, 'unitHeight' => '', 'numHeight' => 0,
														'arrayOption' => array(),
														'flagHideUse' => 1, 'flagHideNow' => 1,
													),

												),
											),
											'child' => array(),
										),
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => 'API',
											'strClass' => 'codeLibBaseImgDetail',
											'vars' => array(
												'idTarget' => 'apiAccountWindow',
											),
											'child' => array(),
										),
									),
								),
								array(
									'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
									'strTitle' => '拒否設定',
									'strClass' => 'codeLibBaseImgFolder',
									'vars' => array(
									),
									'child' => array(
/*
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => '登録拒否IP',
											'strClass' => 'codeLibBaseImgSheet',
											'vars' => array(
												'idTarget' => 'jsonIpSignReject',
												'varsEdit' => array( 'flagReloadUse' => 1, ),
												'varsBtn' => array(
													array(
														'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
														'id' => 'JsonIpSignReject',
														'vars' => array( 'idTarget' => 'jsonIpSignReject', ),
														'strTitle' => '保存',
													),
												),
												'varsDetail' => array(
													array(
														'flagMustUse' => 0,
														'id' => 'JsonIpSignReject',
														'strTitle' => 'IPアドレス',
														'strExplain' => '登録を拒否したいIPアドレスを設定してください。',
														'value' => '',
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'empty', 'strComment' => array( 'common' => '無記入項目があるようです。', ), ),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'ip', 'strComment' => array( 'common' => 'IPで通常、予定されていない文字が含まれているようです。', ), ),
															array( 'flagCheck' => 'format', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'ip', 'strComment' => array( 'common' => 'IPのフォーマットにエラーがあるようです。', ), ),
														),
														'flagContentUse' => 1,
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
														'flagTag' => 'textarea', 'flagInputType' => '', 'numMaxlength' => 0,
														'unitWidth' => '%', 'numWidth' => 90, 'unitHeight' => 'px', 'numHeight' => 100,
														'arrayOption' => array(),
														'varsFormList' => array(
															'varsStatus' => array( 'flagSortUse' => 1, 'flagCopyUse' => 1, 'flagRemoveUse' => 1, 'flagAddUse' => 1, 'flagEditUse' => 0, 'flagFormUse' => 1, 'flagBtnUse' => 0, ),
															'templateDetail' => array( 'id' => '', 'flagSortUse' => 1, 'flagCopyUse' => 1, 'flagRemoveUse' => 1, 'flagEditUse' => 0, 'flagFormUse' => 1, 'flagBtnUse' => 0, 'numSort' => 0, 'value' => '', ),
															'varsDetail' => array(),
														),
													),
													array(
														'flagMustUse' => 0,
														'id' => 'JsonIpSubnetSignReject',
														'strTitle' => 'IPアドレス範囲（IPアドレス/サブネット）', 'strExplain' => '登録を拒否したいIPアドレス範囲（IPアドレス/サブネット）を設定してください。', 'value' => '',
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'empty', 'strComment' => array( 'common' => '無記入項目があるようです。', ), ),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'ipSubnet', 'strComment' => array( 'common' => 'IPアドレス/サブネットで通常、予定されていない文字が含まれているようです。', ),),
															array( 'flagCheck' => 'format', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'ipSubnet', 'strComment' => array( 'common' => 'IPアドレス/サブネットのフォーマットにエラーがあるようです。', ), ),
														),
														'flagContentUse' => 1,
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
														'flagTag' => 'textarea', 'flagInputType' => '', 'numMaxlength' => 0,
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
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => '登録拒否メール',
											'strClass' => 'codeLibBaseImgSheet',
											'vars' => array(
												'idTarget' => 'jsonMailSignReject',
												'varsEdit' => array( 'flagReloadUse' => 1, ),
												'varsBtn' => array(
													array(
														'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
														'id' => 'JsonMailSignReject',
														'vars' => array( 'idTarget' => 'jsonMailSignReject', ),
														'strTitle' => '保存',
													),
												),
												'varsDetail' => array(
													array(
														'flagMustUse' => 0,
														'id' => 'JsonMailSignReject',
														'strTitle' => 'メールアドレス', 'strExplain' => '登録を拒否したいメールアドレスを設定してください。', 'value' => '',
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'empty', 'strComment' => array( 'common' => '無記入項目があるようです。', ), ),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'mail', 'strComment' => array( 'common' => 'メールアドレスで通常、予定されていない文字が含まれているようです。', ), ),
															array( 'flagCheck' => 'format', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'mail', 'strComment' => array( 'common' => 'メールアドレスのフォーマットにエラーがあるようです。', ), ),
															array( 'flagCheck' => 'max', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'str', 'num' => 1000, 'strComment' => array( 'common' => '入力文字数が多すぎるようです。', ), 	),
														),
														'flagContentUse' => 1,
														'flagTag' => 'textarea', 'flagInputType' => '', 'numMaxlength' => 1000,
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
														'flagMustUse' => 0,
														'id' => 'JsonMailHostSignReject',
														'strTitle' => 'メールホスト', 'strExplain' => '登録を拒否したいメールホストを設定してください。', 'value' => '',
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'empty', 'strComment' => array( 'common' => '無記入項目があるようです。', ), ),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'mailHost', 'strComment' => array( 'common' => 'メールホストで通常、予定されていない文字が含まれているようです。', ),),
															array( 'flagCheck' => 'format', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'mailHost', 'strComment' => array( 'common' => 'メールホストのフォーマットにエラーがあるようです。', ), ),
															array( 'flagCheck' => 'max', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'str', 'num' => 1000, 'strComment' => array( 'common' => '入力文字数が多すぎるようです。', ), ),
														),
														'flagContentUse' => 1,
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
														'flagTag' => 'textarea', 'flagInputType' => '', 'numMaxlength' => 1000,
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
*/


										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => 'アクセス許可IP',
											'strClass' => 'codeLibBaseImgSheet',
											'vars' => array(
												'idTarget' => 'jsonIpAccessAccept',
												'varsEdit' => array( 'flagReloadUse' => 1, ),
												'varsBtn' => array(
													array(
														'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
														'id' => 'JsonIpAccessAccept',
														'vars' => array( 'idTarget' => 'jsonIpAccessAccept', ),
														'strTitle' => '保存',
													),
												),
												'varsDetail' => array(
													array(
														'flagMustUse' => 0,
														'id' => 'JsonIpAccessAccept',
														'strTitle' => 'IPアドレスまたはホスト名', 'strExplain' => 'アクセスを許可したいIPアドレスまたはホスト名の一部ないし全てを設定してください。<br>※設定する際は、あなたのIPアドレスまたはホスト名を必ず含めて設定してください。<br>『 <%replace%> 』<br>※許可されたIPアドレスまたはホスト名以外はすべて拒否されます。', 'value' => '',
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'empty', 'strComment' => array( 'common' => '無記入項目があるようです。', ), ),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'mailHost', 'strComment' => array('common' => 'IPアドレスまたはホスト名で通常、予定されていない文字が含まれているようです。', ), ),
															array( 'flagCheck' => 'attest', 'flagUse' => 1, 'strComment' => array('strIpSelf' => '設定する際は、必ず自分のIPまたはホスト名を設定する必要があるようです。',),),
														),
														'flagContentUse' => 1,
														'flagTag' => 'textarea', 'flagInputType' => '', 'numMaxlength' => 0,
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
														'flagMustUse' => 0,
														'id' => 'JsonIpSubnetAccessAccept',
														'strTitle' => 'IPアドレス範囲（IPアドレス/サブネット）', 'strExplain' => 'アクセスを許可したいIPアドレス範囲（IPアドレス/サブネット）を設定してください。<br>※許可されたIPアドレス範囲以外はすべて拒否されます。', 'value' => '',
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'empty', 'strComment' => array( 'common' => '無記入項目があるようです。', ), ),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'ipSubnet', 'strComment' => array( 'common' => 'IPアドレス/サブネットで通常、予定されていない文字が含まれているようです。', ),),
															array( 'flagCheck' => 'format', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'ipSubnet', 'strComment' => array( 'common' => 'IPアドレス/サブネットのフォーマットにエラーがあるようです。', ), ),
															array( 'flagCheck' => 'attest', 'flagUse' => 1, 'strComment' => array('strIpSelf' => '設定する際は、必ず自分のIPを設定する必要があるようです。',),),
														),
														'flagContentUse' => 1,
														'flagTag' => 'textarea', 'flagInputType' => '', 'numMaxlength' => 0,
														'unitWidth' => '%', 'numWidth' => 90, 'unitHeight' => 'px', 'numHeight' => 100,
														'arrayOption' => array(),
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
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
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => 'アクセス拒否IP',
											'strClass' => 'codeLibBaseImgSheet',
											'vars' => array(
												'idTarget' => 'jsonIpAccessReject',
												'varsEdit' => array( 'flagReloadUse' => 1, ),
												'varsBtn' => array(
													array(
														'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
														'id' => 'JsonIpAccessReject',
														'vars' => array( 'idTarget' => 'jsonIpAccessReject', ),
														'strTitle' => '保存',
													),
												),
												'varsDetail' => array(
													array(
														'flagMustUse' => 0,
														'id' => 'JsonIpAccessReject',
														'strTitle' => 'IPアドレスまたはホスト名', 'strExplain' => 'アクセスを拒否したいIPアドレスまたはホスト名の一部ないし全てを設定してください。', 'value' => '',
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'empty', 'strComment' => array( 'common' => '無記入項目があるようです。', ), ),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'mailHost', 'strComment' => array('common' => 'IPアドレスまたはホスト名で通常、予定されていない文字が含まれているようです。', ), ),
															array( 'flagCheck' => 'attest', 'flagUse' => 1, 'strComment' => array('strIpSelf' => '設定する際は、自分のIPまたはホスト名を設定できないようです。',),),
														),
														'flagContentUse' => 1,
														'flagTag' => 'textarea', 'flagInputType' => '', 'numMaxlength' => 0,
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
														'flagMustUse' => 0,
														'id' => 'JsonIpSubnetAccessReject',
														'strTitle' => 'IPアドレス範囲（IPアドレス/サブネット）', 'strExplain' => 'アクセスを拒否したいIPアドレス範囲（IPアドレス/サブネット）を設定してください。', 'value' => '',
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'empty', 'strComment' => array( 'common' => '無記入項目があるようです。', ), ),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'ipSubnet', 'strComment' => array( 'common' => 'IPアドレス/サブネットで通常、予定されていない文字が含まれているようです。', ),),
															array( 'flagCheck' => 'format', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'ipSubnet', 'strComment' => array( 'common' => 'IPアドレス/サブネットのフォーマットにエラーがあるようです。', ), ),
															array( 'flagCheck' => 'attest', 'flagUse' => 1, 'strComment' => array('strIpSelf' => '設定する際は、自分のIPを設定できないようです。',),),
														),
														'flagContentUse' => 1,
														'flagTag' => 'textarea', 'flagInputType' => '', 'numMaxlength' => 0,
														'unitWidth' => '%', 'numWidth' => 90, 'unitHeight' => 'px', 'numHeight' => 100,
														'arrayOption' => array(),
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
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
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => '海外IP拒否',
											'strClass' => 'codeLibBaseImgSheet',
											'vars' => array(
												'idTarget' => 'flagReject',
												'varsEdit' => array( 'flagReloadUse' => 1, ),
												'varsBtn' => array(
													array(
														'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
														'id' => 'FlagReject',
														'vars' => array( 'idTarget' => 'flagReject',  ),
														'strTitle' => '保存',
													),
												),
												'varsDetail' => array(
													array(
														'flagMustUse' => 1,
														'id' => 'FlagReject',
														'strTitle' => '海外IP拒否',
														'strExplain' => '
															海外IPを拒否するかどうか設定してください。
														',
														'value' => 1,
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字以外の文字が混入したようです。', ), ),
														),
														'flagContentUse' => 0,
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
														'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
														'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
														'arrayOption' => array(
															array( 'strTitle' => '拒否しない', 'value' => 0, ),
															array( 'strTitle' => '拒否する', 'value' => 1, ),
														),
													),
												),
											),
											'child' => array(),
										),
									),
								),
								array(
									'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
									'strTitle' => 'デスクトップ',
									'strClass' => 'codeLibBaseImgFolder',
									'vars' => array(),
									'child' => array(
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => '自動ログアウト',
											'strClass' => 'codeLibBaseImgSheet',
											'vars' => array(
												'idTarget' => 'numAutoMustLogout',
												'varsEdit' => array( 'flagReloadUse' => 1, ),
												'varsBtn' => array(
													array(
														'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
														'id' => 'NumAutoMustLogout',
														'vars' => array( 'idTarget' => 'numAutoMustLogout', ),
														'strTitle' => '保存',
													),
												),
												'varsDetail' => array(
													array(
														'flagMustUse' => 1,
														'id' => 'NumAutoMustLogout',
														'strTitle' => '自動ログアウト',
														'strExplain' => '画面操作を何分行わなかったときに、自動でログアウトするのかを設定してください。<br>　※ 自動ログアウト設定を各アカウントの判断に委ねる場合は、0に設定してください。<br>　※ 単位) 分。<br>　※ 半角数字で設定してください。',
														'value' => 0,
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字以外の文字が混入したようです。', ), ),
															array( 'flagCheck' => 'min', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'num', 'flagArr' => 0, 'num' => 0, 'strComment' => array( 'common' => 'マイナス値は、設定できないようです。', ), ),
															array( 'flagCheck' => 'max', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'num' => 4294967296, 'strComment' => array( 'common' => 'システム上、4294967296以上の数値を記入できないようです', ), ),
														),
														'flagContentUse' => 0,
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
														'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 10,
														'unitWidth' => '%', 'numWidth' => 40, 'unitHeight' => '', 'numHeight' => 0,
														'arrayOption' => array(),
													),
												),
											),
											'child' => array(),
										),
									),
								),
								array(
									'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
									'strTitle' => 'アカウント',
									'strClass' => 'codeLibBaseImgFolder',
									'vars' => array(),
									'child' => array(
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => 'アカウント',
											'strClass' => 'codeLibBaseImgDetail',
											'vars' => array(
												'idTarget' => 'accountWindow',
											),
											'child' => array(),
										),
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => '有効期間パターン',
											'strClass' => 'codeLibBaseImgDetail',
											'vars' => array(
												'idTarget' => 'termWindow',
											),
											'child' => array(),
										),
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => 'モジュールパターン',
											'strClass' => 'codeLibBaseImgDetail',
											'vars' => array(
												'idTarget' => 'moduleWindow',
											),
											'child' => array(),
										),
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => 'パスワード設定',
											'strClass' => 'codeLibBaseImgSheet',
											'vars' => array(
												'idTarget' => 'numPasswordLimit',
												'varsEdit' => array( 'flagReloadUse' => 1, ),
												'varsBtn' => array(
													array(
														'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
														'id' => 'NumPasswordLimit',
														'vars' => array( 'idTarget' => 'numPasswordLimit', ),
														'strTitle' => '保存',
													),
												),
												'varsDetail' => array(
													array(
														'flagMustUse' => 1,
														'id' => 'NumPasswordLimit',
														'strTitle' => 'パスワード有効期間',
														'strExplain' => 'ログインパスワードの有効期間を設定してください。<br>　※ パスワードに有効期間を設けない場合は、0に設定してください。<br>　※ 単位) 日にち。<br>　※ 半角数字で設定してください。<br>　※ 変更すると各アカウントのパスワード変更期限がリセットされます。',
														'value' => 0,
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字以外の文字が混入したようです。', ), ),
														),
														'flagContentUse' => 0,
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
														'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 7,
														'unitWidth' => '%', 'numWidth' => 40, 'unitHeight' => '', 'numHeight' => 0,
														'arrayOption' => array(),
													),
													array(
														'flagMustUse' => 1,
														'id' => 'NumPassword',
														'strTitle' => 'パスワード最小文字数',
														'strExplain' => 'ログインパスワードの最小文字数を設定してください。<br>　※ 文字数は、最低でも4文字以上に設定してください。<br>　※ 半角数字で設定してください。<br>　※ 既に登録されているパスワードには適用されません。',
														'value' => 0,
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字以外の文字が混入したようです。', ), ),
															array(	'flagCheck' => 'min', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'num', 'num' => 4, 'strComment' => array( 'common' => 'パスワードの文字数が、4文字以上になっていないようです。', ),),
														),
														'flagContentUse' => 0,
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
														'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 10,
														'unitWidth' => '%', 'numWidth' => 40, 'unitHeight' => '', 'numHeight' => 0,
														'arrayOption' => array(),
													),
													array(
														'flagMustUse' => 1,
														'id' => 'NumAutoLock',
														'strTitle' => 'パスワード試行回数',
														'strExplain' => 'ログインの際、パスワード試行回数の上限を設定してください。上限を超えた場合、アカウントが自動的にロックされます。<br>　※ アカウントロックをしたくない場合は、0に設定してください。<br>　※ 半角数字で設定してください。',
														'value' => 0,
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字以外の文字が混入したようです。', ), ),
														),
														'flagContentUse' => 0,
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
														'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 10,
														'unitWidth' => '%', 'numWidth' => 40, 'unitHeight' => '', 'numHeight' => 0,
														'arrayOption' => array(),
													),
												),
											),
											'child' => array(),
										),
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => '二段階認証',
											'strClass' => 'codeLibBaseImgSheet',
											'vars' => array(
												'idTarget' => 'flagLoginSecond',
												'varsEdit' => array( 'flagReloadUse' => 1, ),
												'varsBtn' => array(
													array(
														'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
														'id' => 'FlagLoginSecond',
														'vars' => array( 'idTarget' => 'flagLoginSecond',  ),
														'strTitle' => '保存',
													),
												),
												'varsDetail' => array(
													array(
														'flagMustUse' => 1,
														'id' => 'FlagLoginSecond',
														'strTitle' => '二段階認証',
														'strExplain' => '
															二段階認証を各アカウントに強制するかどうか設定してください。
														', 'value' => 0,
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字以外の文字が混入したようです。', ), ),
														),
														'flagContentUse' => 0,
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
														'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
														'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
														'arrayOption' => array(
															array( 'strTitle' => '強制しない', 'value' => 0, ),
															array( 'strTitle' => '強制する', 'value' => 1, ),
														),
													),
												),
											),
											'child' => array(),
										),
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => 'アカウントロック',
											'strClass' => 'codeLibBaseImgDetail',
											'vars' => array(
												'idTarget' => 'lockWindow',
											),
											'child' => array(),
										),
/*
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => 'アカウント登録申請',
											'strClass' => 'codeLibBaseImgDetail',
											'vars' => array(
												'idTarget' => 'applySignWindow',
											),
											'child' => array(),
										),
*/
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => 'アカウント変更申請',
											'strClass' => 'codeLibBaseImgDetail',
											'vars' => array(
												'idTarget' => 'applyChangeWindow',
											),
											'child' => array(),
										),
									),
								),
							),
						),
						array(
							'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
							'strTitle' => 'ユーザ項目',
							'strClass' => 'codeLibBaseImgFolder',
							'vars' => array(
							),
							'child' => array(
								array(
									'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
									'strTitle' => 'アカウント',
									'strClass' => 'codeLibBaseImgFolder',
									'vars' => array(),
									'child' => array(
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => 'アカウント変更申請',
											'strClass' => 'codeLibBaseImgSheet',
											'vars' => array(
												'idTarget' => 'strCodeName',
												'varsEdit' => array( 'flagReloadUse' => 1, ),
												'varsBtn' => array(
													array(
															'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
															'id' => 'StrCodeName',
															'vars' => array( 'idTarget' => 'strCodeName', ),
															'strTitle' => '変更申請',
														),
													),
												'varsDetail' => array(
													array(
														'flagMustUse' => 0,
														'id' => 'DummyAccount',
														'strTitle' => '', 'strExplain' => '', 'value' => '',
														'flagErrorNow' => 0,
														'arrayError' => array(),
														'flagContentUse' => 0,
														'flagCommentUse' => 1, 'strCommentTitle' => '', 'strComment' => 'あなたのアカウントは、『<span class="codeLibBaseFontTypeCheck" style="float:none;"> <%replace%> </span>』 まで有効です。',
														'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 0,
														'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
														'arrayOption' => array(),
														'flagFoldUse' => 0, 'flagFoldNow' => 0,
														'stamp' => 0,
													),
													array(
														'flagMustUse' => 0,
														'id' => 'DummyCommentApply',
														'strTitle' => '', 'strExplain' => '', 'value' => '',
														'flagErrorNow' => 0,
														'arrayError' => array(),
														'flagContentUse' => 0,
														'flagCommentUse' => 1, 'strCommentTitle' => '', 'strComment' => '<span class="codeLibBaseFontTypeCheck" style="float:none;">現在申請中...</span><br>認証を確認しました。管理者の承認があるまでしばらくお待ちください。',
														'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 0,
														'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
														'arrayOption' => array(),
														'flagFoldUse' => 0, 'flagFoldNow' => 0,
														'stamp' => 0,
													),
													array(
														'flagMustUse' => 0,
														'id' => 'DummyCommentAttest',
														'strTitle' => '', 'strExplain' => '', 'value' => '',
														'flagErrorNow' => 0,
														'arrayError' => array(),
														'flagContentUse' => 0,
														'flagCommentUse' => 1, 'strCommentTitle' => '', 'strComment' => '<span class="codeLibBaseFontTypeCheck" style="float:none;">現在認証待ち...</span><br>認証用メールが到着したら指示に従ってください。<br>認証期限<span class="codeLibBaseFontTypeCheck" style="float:none;">約<%replace%>時間後</span><br>申請した内容を修正したい場合は、認証前にもう一度申請して頂くと上書き申請されます。',
														'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 0,
														'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
														'arrayOption' => array(),
														'flagFoldUse' => 0, 'flagFoldNow' => 0,
														'stamp' => 0,
													),
													array(
														'flagMustUse' => 0,
														'id' => 'DummyCommentStart',
														'strTitle' => '', 'strExplain' => '', 'value' => '',
														'flagErrorNow' => 0,
														'arrayError' => array(
														),
														'flagContentUse' => 0,
														'flagCommentUse' => 1, 'strCommentTitle' => '', 'strComment' => '申請後、認証用メールが到着します。到着したら指示に従ってください。変更申請した内容は、<span class="codeLibBaseFontTypeCheck" style="float:none;">管理者の承認後</span>に反映されます。',
														'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 0,
														'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
														'arrayOption' => array(),
														'flagFoldUse' => 0, 'flagFoldNow' => 0,
														'stamp' => 0,
													),
													array(
														'flagMustUse' => 1,
														'id' => 'StrCodeName',
														'strTitle' => 'システム内表示名', 'strExplain' => 'システム内で表示される名前を記入してください。', 'value' => '',
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array('common' => '記入漏れがあるようです。', ), ),
															array( 'flagCheck' => 'strUnique', 'flagUse' => 1, 'flagNow' => 0, 'strComment' => array('common' => '機種依存文字が混入しているようです。', ), ),
															array( 'flagCheck' => 'attest', 'flagUse' => 1, 'strComment' => array('common' => '手続中にシステムエラーが起きたようです。その旨、システム運営者に連絡してください。',),),
														),
														'flagContentUse' => 0,
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
														'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 0,
														'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
														'arrayOption' => array(),
														'flagFoldUse' => 0, 'flagFoldNow' => 0,
													),
													array(
														'flagMustUse' => 1,
														'id' => 'IdLogin',
														'strTitle' => 'ログインID', 'strExplain' => 'ログインの際使用するIDを設定してください。<br>　※ 半角英数で記入してください。<br>　※ 5文字以上記入してください。', 'value' => '',
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array('common' => '記入漏れがあるようです。', ), ),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'half', 'strComment' => array('common' => '半角英数で記入されていないようです。', ), ),
															array( 'flagCheck' => 'min', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'str', 'flagArr' => 0, 'num' => 5, 'strComment' => array('common' => '入力文字数が少ないようです。', ), ),
														),
														'flagContentUse' => 0,
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
														'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 1000,
														'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
														'arrayOption' => array(),
														'flagFoldUse' => 0, 'flagFoldNow' => 0,
													),
													array(
														'flagMustUse' => 1,
														'id' => 'StrMailPc',
														'strTitle' => 'メールアドレス(PC)', 'strExplain' => '　※ 半角英数で記入してください。', 'value' => '',
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array('common' => '記入漏れがあるようです。', ), ),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'mail', 'strComment' => array('common' => 'メールで通常、予定されていない文字が使用されているようです。', ), ),
															array( 'flagCheck' => 'format', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'mail', 'strComment' => array('common' => 'メールのフォーマットにエラーがあるようです。', ), ),
														),
														'flagContentUse' => 0,
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
														'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 200,
														'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
														'arrayOption' => array(),
														'flagFoldUse' => 0, 'flagFoldNow' => 0,
													),
												),
											),
											'child' => array(),
										),
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => 'ログインパスワード',
											'strClass' => 'codeLibBaseImgSheet',
											'vars' => array(
												'idTarget' => 'strPassword',
												'varsEdit' => array( 'flagReloadUse' => 1, ),
												'varsBtn' => array(
													array(
															'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
															'id' => 'StrPassword',
															'vars' => array( 'idTarget' => 'strPassword', ),
															'strTitle' => '保存',
														),
													),
												'varsDetail' => array(
													array(
														'flagMustUse' => 0,
														'id' => 'DummyPassword',
														'strTitle' => '', 'strExplain' => '', 'value' => '',
														'flagErrorNow' => 0,
														'arrayError' => array(),
														'flagContentUse' => 0,
														'flagCommentUse' => 1, 'strCommentTitle' => '', 'strComment' => 'あなたのパスワードは、『<span class="codeLibBaseFontTypeCheck" style="float:none;"> <%replace%> </span>』 まで有効です。<br><span class="codeLibBaseFontTen" style="float:none;">　※ 期限が到来する前に新しいパスワードへ変更してください。</span>',
														'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 0,
														'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
														'arrayOption' => array(),
														'flagFoldUse' => 0, 'flagFoldNow' => 0,
														'stamp' => 0,
													),
													array(
														'flagMustUse' => 0,
														'id' => 'DummyComment',
														'strTitle' => '', 'strExplain' => '', 'value' => '',
														'flagErrorNow' => 0,
														'arrayError' => array(),
														'flagContentUse' => 0,
														'flagCommentUse' => 1, 'strCommentTitle' => '', 'strComment' => '現在のパスワードは、<span class="codeLibBaseFontTypeCheck" style="float:none;">セキュリティの観点</span>から表示されません。',
														'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 0,
														'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
														'arrayOption' => array(),
														'flagFoldUse' => 0, 'flagFoldNow' => 0,
														'stamp' => 0,
													),
													array(
														'flagMustUse' => 1,
														'id' => 'StrPassword',
														'strTitle' => '新パスワード', 'strExplain' => '(1)半角英字の大文字　 (2)半角英字の小文字　 (3)半角数字　 (4)記号<br>(1) ～ (4)すべて組み合わせて、新しいパスワードを作成してください。<br>　※ <%replace%>文字以上記入してください。<br>　※ 今まで使用したパスワードは使えません。<br>　※ 使用可能記号) ! " # $ % \' ( ) = ~ | ^ @ [ ; : ] , . / ` { + * } > ? - ', 'value' => '',
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array('common' => '記入漏れがあるようです。', ), ),
															array( 'flagCheck' => 'min', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'str', 'flagArr' => 0, 'num' => 0, 'strComment' => array('common' => '入力文字数が少ないようです。', ), ),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'space', 'flagArr' => 0, 'strComment' => array('common' => 'パスワードに、空白は使用できないようです。', ), ),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'password', 'flagArr' => 0, 'strComment' => array('common' => 'パスワードに、(1)半角英字の大文字, (2)半角英字の小文字, (3)半角数字, (4)記号　以外の文字が使用されているようです。', ), ),
															array( 'flagCheck' => 'format', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'password', 'strComment' => array('common' => 'パスワードに、(1)半角英字の大文字, (2)半角英字の小文字, (3)半角数字, (4)記号　すべて含まれていないようです。', ), ),
															array( 'flagCheck' => 'attest', 'flagUse' => 1, 'strComment' => array('sameValue' => '今まで使用したパスワードとは違う値にしなければならないようです。',),),
														),
														'flagContentUse' => 0,
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
														'flagTag' => 'input', 'flagInputType' => 'password', 'numMaxlength' => 0,
														'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
														'arrayOption' => array(),
														'flagFoldUse' => 0, 'flagFoldNow' => 0,
													),
													array(
														'flagMustUse' => 1,
														'id' => 'StrPasswordConfirm',
														'strTitle' => '新パスワード確認', 'strExplain' => '確認のため再度パスワードを入力してください。', 'value' => 'test',
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array('common' => '記入漏れがあるようです。', ), ),
															array( 'flagCheck' => 'attest', 'flagUse' => 1, 'strComment' => array('common' => '新しいパスワードと一致しないようです。',),),
														),
														'flagContentUse' => 0,
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
														'flagTag' => 'input', 'flagInputType' => 'password', 'numMaxlength' => 0,
														'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
														'arrayOption' => array(),
														'flagFoldUse' => 0, 'flagFoldNow' => 0,
													),
												),
											),
											'child' => array(),
										),
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => 'ログイン通知',
											'strClass' => 'codeLibBaseImgSheet',
											'vars' => array(
												'idTarget' => 'flagLoginMailAccount',
												'varsEdit' => array( 'flagReloadUse' => 1, ),
												'varsBtn' => array(
													array(
														'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
														'id' => 'FlagLoginMailAccount',
														'vars' => array( 'idTarget' => 'flagLoginMailAccount',  ),
														'strTitle' => '保存',
													),
												),
												'varsDetail' => array(
													array(
														'flagMustUse' => 1,
														'id' => 'FlagLoginMailAccount',
														'strTitle' => 'ログイン通知',
														'strExplain' => '
															ログインフォームやAPIを通じてセッションが発行された場合、メールで通知します。
														', 'value' => 0,
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字以外の文字が混入したようです。', ), ),
														),
														'flagContentUse' => 0,
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
														'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
														'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
														'arrayOption' => array(
															array( 'strTitle' => '通知しない', 'value' => 0, ),
															array( 'strTitle' => '通知する', 'value' => 1, ),
														),
													),
												),
											),
											'child' => array(),
										),
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => '二段階認証',
											'strClass' => 'codeLibBaseImgSheet',
											'vars' => array(
												'idTarget' => 'flagLoginSecondAccount',
												'varsEdit' => array( 'flagReloadUse' => 1, ),
												'varsBtn' => array(
													array(
														'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
														'id' => 'FlagLoginSecondAccount',
														'vars' => array( 'idTarget' => 'flagLoginSecondAccount',  ),
														'strTitle' => '保存',
													),
												),
												'varsDetail' => array(
													array(
														'flagMustUse' => 1,
														'id' => 'FlagLoginSecondAccount',
														'strTitle' => '二段階認証',
														'strExplain' => '
															二段階認証は、パスワード認証後、登録してあるメールアドレス宛に送付された認証用リンクを辿ってログインができる仕組みです。
														', 'value' => 0,
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字以外の文字が混入したようです。', ), ),
														),
														'flagContentUse' => 0,
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
														'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
														'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
														'arrayOption' => array(
															array( 'strTitle' => '有効にしない', 'value' => 0, ),
															array( 'strTitle' => '有効にする', 'value' => 1, ),
														),
													),
												),
											),
											'child' => array(),
										),
									),
								),
								array(
									'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
									'strTitle' => 'デスクトップ',
									'strClass' => 'codeLibBaseImgFolder',
									'vars' => array(),
									'child' => array(
/*
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => 'タイムゾーン',
											'strClass' => 'codeLibBaseImgSheet',
											'vars' => array(
												'idTarget' => 'numTimeZone',
												'varsEdit' => array( 'flagReloadUse' => 1, ),
												'varsBtn' => array(
													array(
														'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
														'id' => 'NumTimeZone',
														'vars' => array( 'idTarget' => 'numTimeZone', ),
														'strTitle' => '保存',
													),
												),
												'varsDetail' => array(
													array(
														'flagMustUse' => 1,
														'id' => 'NumTimeZone',
														'strTitle' => 'タイムゾーン',
														'strExplain' => '表示されるタイムゾーン(時間帯)を設定することができます。<br> 　※ ページ更新後に反映されます。',
														'value' => 9,
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'number', 'strComment' => array( 'common' => '半角数字以外の文字が混入したようです。', ), ),
														),
														'flagContentUse' => 0,
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
														'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
														'unitWidth' => '%', 'numWidth' => 40, 'unitHeight' => '', 'numHeight' => 0,
														'arrayOption' => array(
															array( 'strTitle' => 'UTC +14','value' => 14,),
															array( 'strTitle' => 'UTC +13','value' => 13,),
															array( 'strTitle' => 'UTC +12:45','value' => 12.75,),
															array( 'strTitle' => 'UTC +12','value' => 12,),
															array( 'strTitle' => 'UTC +11:30','value' => 11.5,),
															array( 'strTitle' => 'UTC +11','value' => 11,),
															array( 'strTitle' => 'UTC +10:30','value' => 10.5,),
															array( 'strTitle' => 'UTC +10','value' => 10,),
															array( 'strTitle' => 'UTC +9:30','value' => 9.5,),
															array( 'strTitle' => 'UTC +9　日本標準時','value' => 9,),
															array( 'strTitle' => 'UTC +8:45','value' => 8.75,),
															array( 'strTitle' => 'UTC +8','value' => 8,),
															array( 'strTitle' => 'UTC +7','value' => 7,),
															array( 'strTitle' => 'UTC +6:30','value' => 6.5,),
															array( 'strTitle' => 'UTC +6','value' => 6,),
															array( 'strTitle' => 'UTC +5:45','value' => 5.75,),
															array( 'strTitle' => 'UTC +5:30','value' => 5.5,),
															array( 'strTitle' => 'UTC +5','value' => 5,),
															array( 'strTitle' => 'UTC +4','value' => 4,),
															array( 'strTitle' => 'UTC +3','value' => 3,),
															array( 'strTitle' => 'UTC +2','value' => 2,),
															array( 'strTitle' => 'UTC +1','value' => 1,),
															array( 'strTitle' => 'UTC','value' => 0,),
															array( 'strTitle' => 'UTC -1','value' => -1,),
															array( 'strTitle' => 'UTC -2','value' => -2,),
															array( 'strTitle' => 'UTC -3','value' => -3,),
															array( 'strTitle' => 'UTC -3:30','value' => -3.5,),
															array( 'strTitle' => 'UTC -4','value' => -4,),
															array( 'strTitle' => 'UTC -5','value' => -5,),
															array( 'strTitle' => 'UTC -6','value' => -6,),
															array( 'strTitle' => 'UTC -7','value' => -7,),
															array( 'strTitle' => 'UTC -8','value' => -8,),
															array( 'strTitle' => 'UTC -9','value' => -9,),
															array( 'strTitle' => 'UTC -9:30','value' => -9.5,),
															array( 'strTitle' => 'UTC -10','value' => -10,),
															array( 'strTitle' => 'UTC -11','value' => -11,),
															array( 'strTitle' => 'UTC -12','value' => -12,),
														),
														'numSize' => 10,
													),
												),
											),
											'child' => array(),
										),
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => '表示祝日',
											'strClass' => 'codeLibBaseImgSheet',
											'vars' => array(
												'idTarget' => 'strHoliday',
												'varsEdit' => array( 'flagReloadUse' => 1, ),
												'varsBtn' => array(
													array(
														'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
														'id' => 'StrHoliday',
														'vars' => array( 'idTarget' => 'strHoliday', ),
														'strTitle' => '保存',
													),
												),
												'varsDetail' => array(
													array(
														'flagMustUse' => 1,
														'id' => 'StrHoliday',
														'strTitle' => '表示祝日',
														'strExplain' => 'カレンダーに表示される国別の祝日を設定することができます。<br>　※ 祝日の定義ファイルが古いため表示されない祝日がある可能性があります。<br> 　※ ページ更新後に反映されます。',
														'value' => 'jp',
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'half', 'strComment' => array( 'common' => '半角英数で記入されていないようです。', ), ),
														),
														'flagContentUse' => 0,
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
														'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
														'unitWidth' => '%', 'numWidth' => 40, 'unitHeight' => '', 'numHeight' => 0,
														'arrayOption' => array(
															array( 'strTitle' => '祝日を表示しない','value' => 'xx',),
														),
													),
												),
											),
											'child' => array(),
										),

										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => '表示言語',
											'strClass' => 'codeLibBaseImgSheet',
											'vars' => array(
												'idTarget' => 'strLang',
												'varsEdit' => array( 'flagReloadUse' => 1, ),
												'varsBtn' => array(
													array(
														'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
														'id' => 'StrLang',
														'vars' => array( 'idTarget' => 'strLang', ),
														'strTitle' => '保存',
													),
												),
												'varsDetail' => array(
													array(
														'flagMustUse' => 1,
														'id' => 'StrLang',
														'strTitle' => '表示言語',
														'strExplain' => '表示言語を設定することができます。<br> 　※ ページ更新後に反映されます。',
														'value' => 'ja',
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'half', 'strComment' => array( 'common' => '半角英数以外の文字が混入しているようです。', ), ),
														),
														'flagContentUse' => 0,
														'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
														'unitWidth' => '%', 'numWidth' => 40, 'unitHeight' => '', 'numHeight' => 0,
														'arrayOption' => array(),
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
													),
												),
											),
											'child' => array(),
										),
*/
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => '検索表示件数',
											'strClass' => 'codeLibBaseImgSheet',
											'vars' => array(
												'idTarget' => 'numList',
												'varsEdit' => array( 'flagReloadUse' => 1, ),
												'varsBtn' => array(
													array(
														'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
														'id' => 'NumList',
														'vars' => array( 'idTarget' => 'numList', ),
														'strTitle' => '保存',
													),
												),
												'varsDetail' => array(
													array(
														'flagMustUse' => 1,
														'id' => 'NumList',
														'strTitle' => '検索表示件数',
														'strExplain' => '検索表示件数を設定することができます。',
														'value' => 25,
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字以外の文字が混入したようです。', ), ),
															array( 'flagCheck' => 'max', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'num', 'num' => 50, 'strComment' => array( 'common' => '表示件数が50を超えているようです。', ), ),
														),
														'flagContentUse' => 0,
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
														'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
														'unitWidth' => '%', 'numWidth' => 40, 'unitHeight' => '', 'numHeight' => 0,
														'arrayOption' => array(
															array( 'strTitle' => '15件', 'value' => 15, ),
															array( 'strTitle' => '25件', 'value' => 25, ),
															array( 'strTitle' => '50件', 'value' => 50, ),
														),
														'numSize' => 3,
													),
												),
											),
											'child' => array(),
										),
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => '自動ブートモジュール',
											'strClass' => 'codeLibBaseImgSheet',
											'vars' => array(
												'idTarget' => 'strAutoBoot',
												'varsEdit' => array( 'flagReloadUse' => 1, ),
												'varsBtn' => array(
													array(
														'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
														'id' => 'StrAutoBoot',
														'vars' => array( 'idTarget' => 'strAutoBoot', ),
														'strTitle' => '保存',
													),
												),
												'varsDetail' => array(
													array(
														'flagMustUse' => 1,
														'id' => 'StrAutoBoot',
														'strTitle' => '自動ブートモジュール',
														'strExplain' => 'ログインした際、一番最初に起動するモジュールを設定することができます。',
														'value' => '',
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'half', 'strComment' => array( 'common' => '半角英数以外の文字が混入しているようです。', ), ),
														),
														'flagContentUse' => 0,
														'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
														'unitWidth' => '%', 'numWidth' => 40, 'unitHeight' => '', 'numHeight' => 0,
														'arrayOption' => array(),
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
														'varsTmpl' => array(
															'arrayOption' => array( 'strTitle' => '統制モジュール', 'value' => 'base', ),
														),
													),
												),
											),
											'child' => array(),
										),
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => '自動ログアウト',
											'strClass' => 'codeLibBaseImgSheet',
											'vars' => array(
												'idTarget' => 'numAutoLogout',
												'varsEdit' => array( 'flagReloadUse' => 1, ),
												'varsBtn' => array(
													array(
														'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
														'id' => 'NumAutoLogout',
														'vars' => array( 'idTarget' => 'numAutoLogout', ),
														'strTitle' => '保存',
													),
												),
												'varsDetail' => array(
													array(
														'flagMustUse' => 1,
														'id' => 'NumAutoLogout',
														'strTitle' => '自動ログアウト',
														'strExplain' => 'ログイン後、設定した時間を経過しても操作を行わなかった場合、自動的にログアウトするよう設定することができます。<br>　※ 自動ログアウト機能を使用しない場合は、0に設定してください。<br>　※ 単位) 分。<br>　※ 半角数字で設定してください。<br> 　※ ページ更新後に反映されます。',
														'value' => 0,
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字以外の文字が混入したようです。', ), ),
															array( 'flagCheck' => 'min', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'num', 'flagArr' => 0, 'num' => 0, 'strComment' => array( 'common' => 'マイナス値は、設定できないようです。', ), ),
															array( 'flagCheck' => 'max', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'num' => 4294967296, 'strComment' => array( 'common' => 'システム上、4294967296以上の数値を記入できないようです。', ), ),
														),
														'flagContentUse' => 0,
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
														'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 10,
														'unitWidth' => '%', 'numWidth' => 40, 'unitHeight' => '', 'numHeight' => 0,
														'arrayOption' => array(),
													),
												),
											),
											'child' => array(),
										),
/*
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => '新着確認スケジュール',
											'strClass' => 'codeLibBaseImgSheet',
											'vars' => array(
												'idTarget' => 'numAutoPopup',
												'varsEdit' => array( 'flagReloadUse' => 1, ),
												'varsBtn' => array(
													array(
														'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
														'id' => 'NumAutoPopup',
														'vars' => array( 'idTarget' => 'numAutoPopup', ),
														'strTitle' => '保存',
													),
												),
												'varsDetail' => array(
													array(
														'flagMustUse' => 1,
														'id' => 'NumAutoPopup',
														'strTitle' => '新着確認スケジュール', 'strExplain' => '新着ログがあるかどうか、ユーザによる操作がなくても一定間隔でサーバに問い合わせることができます。<br> 　※ ページ更新後に反映されます。', 'value' => 0,
														'flagErrorNow' => 0,
														'arrayError' => array(
															array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
															array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字以外の文字が混入したようです。', ), ),
														),
														'flagContentUse' => 0,
														'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
														'unitWidth' => '%', 'numWidth' => 40, 'unitHeight' => '', 'numHeight' => 0,
														'arrayOption' => array(
															array( 'strTitle' => '新着確認をしない。', 'value' => 0, ),
															array( 'strTitle' => '15分間隔で問い合わせをする。', 'value' => 15, ),
															array( 'strTitle' => '30分間隔で問い合わせをする。', 'value' => 30, ),
															array( 'strTitle' => '60分間隔で問い合わせをする。', 'value' => 60, ),
														),
														'numSize' => 4,
														'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
													),
												),
											),
											'child' => array(),
										),
*/
										array(
											'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
											'strTitle' => 'レイアウト初期化',
											'strClass' => 'codeLibBaseImgSheet',
											'vars' => array(
												'idTarget' => 'local',
												'varsEdit' => array( 'flagReloadUse' => 1, ),
												'varsBtn' => array(
													array(
														'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
														'id' => 'Local',
														'vars' => array( 'idTarget' => 'local', ),
														'strTitle' => '初期化',
													),
												),
												'varsDetail' => array(
													array(
														'flagMustUse' => 0,
														'id' => 'Local',
														'strTitle' => '', 'strExplain' => '', 'value' => 0,
														'flagErrorNow' => 0,
														'arrayError' => array(),
														'flagContentUse' => 0,
														'flagCommentUse' => 1, 'strCommentTitle' => '', 'strComment' => 'デスクトップにおけるウィンドウ位置といったレイアウトデータを初期化します。<br>　※ 初期化後、ページを<span class="codeLibBaseFontTypeCheck" style="float:none;">自動更新</span>します。',
														'flagTag' => '', 'flagInputType' => '', 'numMaxlength' => 0,
														'unitWidth' => '', 'numWidth' => 0, 	'unitHeight' => '', 'numHeight' => 0,
														'arrayOption' => array(),
													),
												),
											),
											'child' => array(),
										),
									),
								),
							),
						),
						array(
							'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
							'strTitle' => 'ヘルプ',
							'strClass' => 'codeLibBaseImgFolder',
							'vars' => array(),
							'child' => array(
								array(
									'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
									'strTitle' => 'オンラインマニュアル',
									'strClass' => 'codeLibBaseImgSheet',
									'vars' => array(
										'idTarget' => 'release',
										'varsEdit' => array( 'flagReloadUse' => 0, ),
										'varsBtn' => array(
											array(
												'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1, 'flagATag' => 1, 'path' => '',
												'id' => 'Release',
												'vars' => array( 'idTarget' => 'release', ),
												'strTitle' => 'オンラインマニュアルサイト',
											),
										),
										'varsDetail' => array(
											array(
												'flagMustUse' => 0,
												'id' => 'Release',
												'strTitle' => '', 'strExplain' => '', 'value' => 0,
												'flagErrorNow' => 0,
												'arrayError' => array(),
												'flagContentUse' => 0,
												'flagCommentUse' => 1, 'strComment' => 'オンラインマニュアルは下記ボタンのリンク先を参照してください。',
												'flagTag' => '', 'flagInputType' => '', 'numMaxlength' => 0,
												'unitWidth' => '', 'numWidth' => 0, 'unitHeight' => '', 'numHeight' => 0,
												'arrayOption' => array(),
											),
										),
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
			'strClass' => 'codeCoreBaseImgIcon',
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

