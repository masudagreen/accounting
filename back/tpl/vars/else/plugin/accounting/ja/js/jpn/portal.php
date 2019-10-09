<?php

$vars = array(
	array(
		'id' => 'AdminEntityCurrent', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 0, 'flagChildrenUse' => 1, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
		'strTitle' => '管理者項目',
		'strClass' => 'codeLibBaseImgFolder',
		'vars' => array(
			'idTarget' => 'adminEntityCurrent',
		),
		'child' => array(
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
				'strTitle' => 'アクセス構成',
				'strClass' => 'codeLibBaseImgDetail',
				'vars' => array(
					'idTarget' => 'accountEntityAuthorityWindow',
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
				'strTitle' => 'アクセス可能項目パターン',
				'strClass' => 'codeLibBaseImgDetail',
				'vars' => array(
					'idTarget' => 'accessWindow',
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
				'id' => 'UserBoard', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
				'strTitle' => 'ダッシュボード',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'userBoard', 'flagAccessUse' => 0, 'flagAllUse' => 0,
					'varsEdit' => array( 'flagReloadUse' => 1, ),
					'varsBtn' => array(),
					'varsDetail' => array(
						array(
							'flagMustUse' => 0,
							'id' => 'Board',
							'strTitle' => 'お知らせ', 'strExplain' => '', 'value' => '',
							'flagErrorNow' => 0,
							'arrayError' => array(),
							'flagContentUse' => 1,
							'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
							'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 0,
							'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
							'arrayOption' => array(),
							'flagFoldUse' => 0, 'flagFoldNow' => 0,
							'varsBoard' => array(
								'varsStatus' => array(
									'flagCommentUse'=> 1,
									'flagProfitUse'=> 1,
									'flagCashUse'=> 1,
								),
								'varsComment' => array(
									'strTitle'=> 'お知らせ',
									'varsDetail' => array(
										array(
											'flagDisabled' => 1, 'id' => '', 'strTitle' => '<p class="codeLibBaseFontCcc" style="font-size:10px;">入力に関する新たなお知らせはありません。</p>', 'vars' => array(),
										),
									),
									'tmplDetail' => array(
										'id' => '', 'strTitle' => '', 'vars' => array(),
									),
									'varsTmpl' => array(
										'varsLog' => array(
											'numRegister' => '・ 『 仕訳帳 』に新しい仕訳があるようです。',
											'numMail' => '・ 『 仕訳帳 』で設定したメールサーバに新着があったためインポート処理が行われたようです。',
											'numRetry' => '・ 『 仕訳帳 』の基本メニューにある『 フィルタリトライ 』に新しいフィルタ不一致ログがあるようです。',
											'numImport' => '・ 『 仕訳帳 』の基本メニューにある『 インポートフィルタ 』に新しいフィルタがあるようです。',
											'numHouse' => '・ 『 仕訳帳 』の基本メニューにある『 家事按分 』に新しいログがあるようです。',
										),
										'varsFixedAssets' => array(
											'numRegister' => '・ 『 固定資産管理 』に新しい固定資産があるようです。',
										),
										'varsFile' => array(
											'numRegister' => '・ 『 証憑ファイル 』に新しい証憑ファイルがあるようです。',
											'numMail' => '・ 『 証憑ファイル 』で設定したメールサーバに新着があったためインポート処理が行われたようです。',
										),
										'varsCash' => array(
											'numRegister' => '・ 『 収支管理 』に新しいログがあるようです。',
											'numDefer' => '・ 『 収支管理 』の基本メニューにある『 留保ログ 』に新しいログがあるようです。',
										),
										'varsBanks' => array(
											'numRegister' => '・ 『 口座管理 』に新しいログがあるようです。',
											'numAccount' => '・ 『 口座管理 』の基本メニューにある『 金融機関管理 』に新しいログがあるようです。',
										),
									),
								),
								'varsProfit' => array(
									'strTitle'=> '＜当期採算ライン＞',
									'varsSpace' => array(
										'varsStatus' => array(
											'strBorderColor' => '',
											'flagOverflowXUse' => 0,
											'flagOverflowYUse' => 0,
											'numMargin' => 15,
											'numPadding' => 0,
											'numHeight' => 250,
											'numWidth' => '90',
											'unitWidth' => '%',
											'flagChartUse' => 1,
										),
										'varsDetail' => array(
											'varsData' => array(),
											'varsOptions' => array(),
										),
									),
									'varsCollect' => array(
										'varsBase' => array(),
										'tmplOptions' => array(
											'legend' => array('show' => true),
											'bars' => array('show' => true),
											'points' => array('show' => false),
											'grid' => array('clickable' => true),
											'yaxis' => array('unit'=> '円', 'comma'=> 1),
											'xaxis' => array('min' => 1, 'max' => 13, 'ticks' => array()),
										),
										'varsLabelId' => array(),
										'varsLabel' => array(),
										'varsFlagFiscalPeriod' => array(),
										'varsStrFlagFiscalPeriod' => array(),
										'strPeriodCurrent' => '当期',
									),
									'varsRow' => array(
										'numSales' => '売上高',
										'numPoint' => '損益分岐点売上高',
										'numSafe' => '差額(安全余裕額)',
									),
									'tmplTable' => array(
										'varsStatus' => array(
											'flagIdNoneUse' => 1,
											'flagBgUse' => 1,
											'flagOverflowUse' => 1,
											'numFontSize' => 10,
											'varsColumnId' => array(),
											'numWidthTable' => 0,
											'varsColumnWidth' => array(),
										),

										'varsColumn' => array('売上高', '損益分岐点', '差額'),
										'varsDetail' => array(),
										'tmplDetail' => array(
											'id' => '',
											'varsDetail' => array(),
										),
										'tmplData' => array(
											'value' => '',
											'strClass' => 'codeLibBaseTableRowRight',
										),
										'numWidth' => 100,
									),
								),
								'varsCash' => array(
									'strTitle'=> '＜当期収支ライン＞',
									'varsSpace' => array(
										'varsStatus' => array(
											'strBorderColor' => '',
											'flagOverflowXUse' => 0,
											'flagOverflowYUse' => 0,
											'numMargin' => 15,
											'numPadding' => 0,
											'numHeight' => 250,
											'numWidth' => '90',
											'unitWidth' => '%',
											'flagChartUse' => 1,
										),
										'varsDetail' => array(
											'varsData' => array(),
											'varsOptions' => array(),
										),
									),
									'varsCollect' => array(
										'varsBase' => array(),
										'tmplOptions' => array(
											'legend' => array('show' => true),
											'bars' => array('show' => true),
											'points' => array('show' => false),
											'grid' => array('clickable' => true),
											'yaxis' => array('unit'=> '円', 'comma'=> 1),
											'xaxis' => array('min' => 1, 'max' => 13, 'ticks' => array()),
										),
										'varsLabelId' => array(),
										'varsLabel' => array(),
										'strPeriodCurrent' => '当期',
									),
									'varsRow' => array(
										'numIn' => '入金',
										'numOut' => '出金',
										'numNet' => '差額',
									),
									'tmplTable' => array(
										'varsStatus' => array(
											'flagIdNoneUse' => 1,
											'flagBgUse' => 1,
											'flagOverflowUse' => 1,
											'numFontSize' => 10,
											'varsColumnId' => array(),
											'numWidthTable' => 0,
											'varsColumnWidth' => array(),
										),
										'varsColumn' => array('入金', '出金', '差額'),
										'varsDetail' => array(),
										'tmplDetail' => array(
											'id' => '',
											'varsDetail' => array(),
										),
										'tmplData' => array(
											'value' => '',
											'strClass' => 'codeLibBaseTableRowRight',
										),
										'numWidth' => 100,
									),
								),
							),
						),
					),

				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 0, 'flagChildrenUse' => 1, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
				'strTitle' => '設定',
				'strClass' => 'codeLibBaseImgFolder',
				'vars' => array(
					'idTarget' => 'setteing', 'flagAccessUse' => 0, 'flagAllUse' => 1,
				),
				'child' => array(
					array(
						'id' => 'Consumption', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 0, 'flagChildrenUse' => 1, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
						'strTitle' => '消費税',
						'strClass' => 'codeLibBaseImgFolder',
						'vars' => array(
							'idTarget' => 'consumption', 'flagAccessUse' => 1, 'flagAllUse' => 1,
						),
						'child' => array(
							array(
								'id' => 'FlagConsumptionTaxFree', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '事業者区分',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'flagConsumptionTaxFree', 'flagAccessUse' => 0, 'flagAllUse' => 1,
									'varsEdit' => array( 'flagReloadUse' => 1, ),
									'varsBtn' => array(
										array(
											'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
											'id' => 'FlagConsumptionTaxFree',
											'vars' => array( 'idTarget' => 'flagConsumptionTaxFree',  ),
											'strTitle' => '保存',
										),
									),
									'varsDetail' => array(
										array(
											'flagMustUse' => 1,
											'id' => 'FlagConsumptionTaxFree',
											'strTitle' => '事業者区分',
											'strExplain' => '',
											'value' => 0,
											'flagErrorNow' => 0,
											'arrayError' => array(
												array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
												array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字以外の文字が混入したようです。', ), ),
												array( 'flagCheck' => 'attest', 'flagUse' => 1, 'strComment' => array(
													'textMaxOver' => '',
												)),
											),
											'flagContentUse' => 0,
											'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
											'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
											'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
											'arrayOption' => array(
												array( 'strTitle' => '課税', 'value' => 0, ),
												array( 'strTitle' => '免税', 'value' => 1, ),
											),
											'numSize' => 2,
											'flagDisabled' => 0,
											'varsTmpl' => array(
												'strNormal' => '事業者区分を設定してください。<br>　※ 保存後、ページを<span class="codeLibBaseFontTypeCheck" style="float:none;">自動更新</span>します。',
												'strPast'   => '<span class="codeLibBaseFontOrange" style="float:none;">※ データが確定しているため変更できません。</span><br>現在選択しているのは、『 <%replace%> 』です。',
											),
										),
									),
								),
								'child' => array(),
							),
							array(
								'id' => 'FlagConsumptionTaxGeneralRule', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '課税方式',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'flagConsumptionTaxGeneralRule', 'flagAccessUse' => 0, 'flagAllUse' => 1,
									'varsEdit' => array( 'flagReloadUse' => 1, ),
									'varsBtn' => array(
										array(
											'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
											'id' => 'FlagConsumptionTaxGeneralRule',
											'vars' => array( 'idTarget' => 'flagConsumptionTaxGeneralRule',  ),
											'strTitle' => '保存',
										),
									),
									'varsDetail' => array(
										array(
											'flagMustUse' => 1,
											'id' => 'FlagConsumptionTaxGeneralRule',
											'strTitle' => '課税方式',
											'strExplain' => '',
											'value' => 0,
											'flagErrorNow' => 0,
											'arrayError' => array(
												array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
												array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字以外の文字が混入したようです。', ), ),
												array( 'flagCheck' => 'attest', 'flagUse' => 1, 'strComment' => array(
													'textMaxOver' => '',
												)),
											),
											'flagContentUse' => 0,
											'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
											'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
											'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
											'arrayOption' => array(
												array( 'strTitle' => '本則課税', 'value' => 1, ),
												array( 'strTitle' => '簡易課税', 'value' => 0, ),
											),
											'numSize' => 2,
											'flagDisabled' => 0,
											'varsTmpl' => array(
												'strNormal' => '消費税の課税方式を設定してください。<br>　※ 保存後、ページを<span class="codeLibBaseFontTypeCheck" style="float:none;">自動更新</span>します。',
												'strPast'   => '<span class="codeLibBaseFontOrange" style="float:none;">※ データが確定しているため変更できません。</span><br>現在選択しているのは、『 <%replace%> 』です。',
											),
										),
									),
								),
								'child' => array(),
							),
							array(
								'id' => 'FlagConsumptionTaxDeducted', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '本則課税',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'flagConsumptionTaxDeducted', 'flagAccessUse' => 0, 'flagAllUse' => 1,
									'varsEdit' => array( 'flagReloadUse' => 1, ),
									'varsBtn' => array(
										array(
											'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
											'id' => 'FlagConsumptionTaxDeducted',
											'vars' => array( 'idTarget' => 'flagConsumptionTaxDeducted',  ),
											'strTitle' => '保存',
										),
									),
									'varsDetail' => array(
										array(
											'flagMustUse' => 1,
											'id' => 'FlagConsumptionTaxDeducted',
											'strTitle' => '仕入税額控除方式',
											'strExplain' => '',
											'value' => 0,
											'flagErrorNow' => 0,
											'arrayError' => array(
												array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
												array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字以外の文字が混入したようです。', ), ),
												array( 'flagCheck' => 'attest', 'flagUse' => 1, 'strComment' => array(
													'textMaxOver' => '',
												)),
											),
											'flagContentUse' => 0,
											'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
											'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
											'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
											'arrayOption' => array(
												array( 'strTitle' => '比例配分', 'value' => 0, ),
												array( 'strTitle' => '個別対応', 'value' => 1, ),
											),
											'numSize' => 2,
											'flagDisabled' => 0,
											'varsTmpl' => array(
												'strNormal' => '仕入税額控除方式を設定してください。<br>　※ 保存後、ページを<span class="codeLibBaseFontTypeCheck" style="float:none;">自動更新</span>します。',
												'strPast'   => '<span class="codeLibBaseFontOrange" style="float:none;">※ データが確定しているため変更できません。</span><br>現在選択しているのは、『 <%replace%> 』です。',
											),
										),
									),
								),
								'child' => array(),
							),
							array(
								'id' => 'FlagConsumptionTaxBusinessType', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '簡易課税',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'flagConsumptionTaxBusinessType', 'flagAccessUse' => 0, 'flagAllUse' => 1,
									'varsEdit' => array( 'flagReloadUse' => 0, ),
									'varsBtn' => array(
										array(
											'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
											'id' => 'FlagConsumptionTaxBusinessType',
											'vars' => array( 'idTarget' => 'flagConsumptionTaxBusinessType',  ),
											'strTitle' => '保存',
										),
									),
									'varsDetail' => array(
										array(
											'flagMustUse' => 1,
											'id' => 'FlagConsumptionTaxBusinessType',
											'strTitle' => '簡易課税事業区分',
											'strExplain' => '',
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
												array( 'strTitle' => '第一種事業', 'value' => 1, ),
												array( 'strTitle' => '第二種事業', 'value' => 2, ),
												array( 'strTitle' => '第三種事業', 'value' => 3, ),
												array( 'strTitle' => '第四種事業', 'value' => 4, ),
												array( 'strTitle' => '第五種事業', 'value' => 5, ),
											),
											'numSize' => 5,
											'flagDisabled' => 0,
											'varsTmpl' => array(
												'strNormal' => '簡易課税事業区分を設定してください。<br>　※ 保存後、ページを<span class="codeLibBaseFontTypeCheck" style="float:none;">自動更新</span>します。',
												'strPast'   => '<span class="codeLibBaseFontOrange" style="float:none;">※ データが確定しているため変更できません。</span><br>現在選択しているのは、『 <%replace%> 』です。',
											),
										),
									),
								),
								'child' => array(),
							),
							array(
								'id' => 'FlagConsumptionTaxIncluding', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '経理方式',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'flagConsumptionTaxIncluding', 'flagAccessUse' => 0, 'flagAllUse' => 1,
									'varsEdit' => array( 'flagReloadUse' => 1, ),
									'varsBtn' => array(
										array(
											'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
											'id' => 'FlagConsumptionTaxIncluding',
											'vars' => array( 'idTarget' => 'flagConsumptionTaxIncluding',  ),
											'strTitle' => '保存',
										),
									),
									'varsDetail' => array(
										array(
											'flagMustUse' => 1,
											'id' => 'FlagConsumptionTaxIncluding',
											'strTitle' => '経理処理方式',
											'strExplain' => '',
											'value' => 0,
											'flagErrorNow' => 0,
											'arrayError' => array(
												array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
												array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字以外の文字が混入したようです。', ), ),
												array( 'flagCheck' => 'attest', 'flagUse' => 1, 'strComment' => array(
													'textMaxOver' => '',
												)),
											),
											'flagContentUse' => 0,
											'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
											'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
											'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
											'arrayOption' => array(
												array( 'strTitle' => '税抜処理', 'value' => 0, ),
												array( 'strTitle' => '税込処理', 'value' => 1, ),
											),
											'numSize' => 2,
											'flagDisabled' => 0,
											'varsTmpl' => array(
												'strNormal' => '消費税の経理処理方式を設定してください。<br>　※ 保存後、ページを<span class="codeLibBaseFontTypeCheck" style="float:none;">自動更新</span>します。',
												'strPast'   => '<span class="codeLibBaseFontOrange" style="float:none;">※ データが確定しているため変更できません。</span><br>現在選択しているのは、『 <%replace%> 』です。',
											),
										),
										array(
											'flagMustUse' => 1,
											'id' => 'FlagConsumptionTaxCalc',
											'strTitle' => '消費税端数処理方法',
											'strExplain' => '',
											'value' => 1,
											'flagErrorNow' => 0,
											'arrayError' => array(
												array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
												array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角文字以外の文字が混入したようです。', ), ),
											),
											'flagContentUse' => 0,
											'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
											'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
											'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
											'arrayOption' => array(
												array( 'strTitle' => '切り捨て', 'value' => 1, ),
												array( 'strTitle' => '四捨五入', 'value' => 2, ),
												array( 'strTitle' => '切り上げ', 'value' => 3, ),
											),
											'numSize' => 3,
											'flagDisabled' => 0,
											'varsTmpl' => array(
												'strNormal' => '消費税の端数処理方法を設定してください。<br>　※ 保存後、ページを<span class="codeLibBaseFontTypeCheck" style="float:none;">自動更新</span>します。',
												'strPast'   => '<span class="codeLibBaseFontOrange" style="float:none;">※ データが確定しているため変更できません。</span><br>現在選択しているのは、『 <%replace%> 』です。',
											),
										),
									),
								),
								'child' => array(),
							),
							array(
								'id' => 'FlagConsumptionTaxWithoutCalc', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '消費税入力方法',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'flagConsumptionTaxWithoutCalc', 'flagAccessUse' => 0, 'flagAllUse' => 1,
									'varsEdit' => array( 'flagReloadUse' => 1, ),
									'varsBtn' => array(
										array(
											'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
											'id' => 'FlagConsumptionTaxWithoutCalc',
											'vars' => array( 'idTarget' => 'flagConsumptionTaxWithoutCalc',  ),
											'strTitle' => '保存',
										),
									),
									'varsDetail' => array(
										array(
											'flagMustUse' => 1,
											'id' => 'FlagConsumptionTaxWithoutCalc',
											'strTitle' => '消費税入力方法',
											'strExplain' => '',
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
												array( 'strTitle' => '内税', 'value' => 1, ),
												array( 'strTitle' => '外税', 'value' => 2, ),
												array( 'strTitle' => '別記', 'value' => 3, ),
											),
											'numSize' => 3,
											'flagDisabled' => 0,
											'varsTmpl' => array(
												'strNormal' => '税抜処理を使用する場合の消費税の入力方法を設定してください。<br>　※ 保存後、ページを<span class="codeLibBaseFontTypeCheck" style="float:none;">自動更新</span>します。',
												'strPast'   => '<span class="codeLibBaseFontOrange" style="float:none;">※ データが確定しているため変更できません。</span><br>現在選択しているのは、『 <%replace%> 』です。',
											),
										),
									),
								),
								'child' => array(),
							),
						),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 0, 'flagChildrenUse' => 1, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
						'strTitle' => '科目',
						'strClass' => 'codeLibBaseImgFolder',
						'vars' => array(
							'idTarget' => 'accountTitle', 'flagAccessUse' => 0, 'flagAllUse' => 1,
						),
						'child' => array(
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '勘定科目',
								'strClass' => 'codeLibBaseImgDetail',
								'vars' => array(
									'idTarget' => 'accountTitleWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '勘定科目(CS)',
								'strClass' => 'codeLibBaseImgDetail',
								'vars' => array(
									'idTarget' => 'accountTitleCSWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '補助科目',
								'strClass' => 'codeLibBaseImgDetail',
								'vars' => array(
									'idTarget' => 'subAccountTitleWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '決算科目',
								'strClass' => 'codeLibBaseImgDetail',
								'vars' => array(
									'idTarget' => 'accountTitleFSWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '決算項目(CS)',
								'strClass' => 'codeLibBaseImgDetail',
								'vars' => array(
									'idTarget' => 'accountTitleFSCSWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
								),
								'child' => array(),
							),
						),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 0, 'flagChildrenUse' => 1, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
						'strTitle' => '部門',
						'strClass' => 'codeLibBaseImgFolder',
						'vars' => array(
							'idTarget' => 'department', 'flagAccessUse' => 0, 'flagAllUse' => 1,
						),
						'child' => array(
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '部門設定',
								'strClass' => 'codeLibBaseImgDetail',
								'vars' => array(
									'idTarget' => 'entityDepartmentWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
								),
								'child' => array(),
							),
						),
					),
					array(
						'id' => 'BalanceData', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 0, 'flagChildrenUse' => 1, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
						'strTitle' => '期首残高',
						'strClass' => 'codeLibBaseImgFolder',
						'vars' => array(
							'idTarget' => 'balanceData', 'flagAccessUse' => 0, 'flagAllUse' => 1,
						),
						'child' => array(
							array(
								'id' => 'BalanceWindow', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '期首残高',
								'strClass' => 'codeLibBaseImgDetail',
								'vars' => array(
									'idTarget' => 'balanceWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
								),
								'child' => array(),
							),
						),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
						'strTitle' => '担当者引継ぎ',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'charge', 'flagAccessUse' => 1, 'flagAllUse' => 1,
							'varsEdit' => array( 'flagReloadUse' => 1, ),
							'varsBtn' => array(
								array(
									'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
									'id' => 'Charge',
									'vars' => array( 'idTarget' => 'charge',  ),
									'strTitle' => '保存',
								),
							),
							'varsDetail' => array(
								array(
									'flagMustUse' => 1,
									'id' => 'Charge',
									'strTitle' => '担当者引継ぎ', 'strExplain' => '引継ぎを行いたいアカウントを設定してください。', 'value' => '',
									'flagErrorNow' => 0,
									'arrayError' => array(
										array( 'flagCheck' => 'blank', 'flagUse' => 0, 'flagNow' => 0, 'flagArr' => 'comma', 'flagType' => 'empty', 'strComment' => array( 'common' => '無記入項目があるようです。', ), ),
										array( 'flagCheck' => 'attest', 'flagUse' => 1, 'strComment' => array('strBlank' => '引継元と引継先が正しく選択されていないようです。', 'strSame' => '引継元と引継先が同じようです。',),),
									),
									'flagContentUse' => 1,
									'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
									'flagTag' => 'textarea', 'flagInputType' => '', 'numMaxlength' => 0,
									'unitWidth' => '%', 'numWidth' => 90, 'unitHeight' => 'px', 'numHeight' => 100,
									'arrayOption' => array(),
									'varsFormCheck' => array(
										'varsStatus' => array(
											'flagBtnUse' => 0,
											'strTitleBtn' => '',
										),
										'varsColumn' => array(
											array(
												'id' => 'StrCharge',
												'flagType' => '',
												'strTitle' => '',
												'numWidth' => 60,
											),
											array(
												'id' => 'BtnEdit',
												'flagType' => 'btn',
												'strTitle' => '修正',
												'numWidth' => 40,
											),
											array(
												'id' => 'StrAccount',
												'flagType' => '',
												'strTitle' => 'アカウント',
												'numWidth' => 200,
											),
										),
										'varsDetail' => array(
											array(
												'id' => 'ChargeNow',
												'idAccount' => 0,
												'varsColumnDetail' => array(
													'strCharge' => '引継元',
													'btnEditUse' => 1,
													'btnEditLock' => 0,
													'btnEditStrClass' => 'codeLibFormCheckImgEdit',
													'btnEditStrClassLock' => 'codeLibFormCheckImgEditLock',
													'strAccount' => '未選択',
												),
											),
											array(
												'id' => 'ChargeNext',
												'idAccount' => 0,
												'varsColumnDetail' => array(
													'strCharge' => '引継先',
													'btnEditUse' => 1,
													'btnEditLock' => 0,
													'btnEditStrClass' => 'codeLibFormCheckImgEdit',
													'btnEditStrClassLock' => 'codeLibFormCheckImgEditLock',
													'strAccount' => '未選択',
												),
											),
										),
										'varsTmpl' => array(
											'strBlank' => '未選択'
										),
										'varsChoice' => array(
											'idTarget' => 'PluginAccountingAccount', 'idModule' => 'Accounting', 'flagCheckUse' => 0,
											'flagId' => 'Charge',
										),
									),
									'flagDisabled' => 0,
									'varsTmpl' => array(
										'strPast'   => '<span class="codeLibBaseFontOrange" style="float:none;">※ データが確定しているため変更できません。</span>',
									),
								),
							),
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
						'strTitle' => '繰越処理',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'nextData', 'flagAccessUse' => 1, 'flagAllUse' => 1,
							'varsEdit' => array( 'flagReloadUse' => 1, ),
							'varsBtn' => array(
								array(
									'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
									'id' => 'NextData',
									'vars' => array( 'idTarget' => 'nextData',  ),
									'strTitle' => '繰越処理',
								),
							),
							'varsDetail' => array(
								array(
									'flagMustUse' => 1,
									'id' => 'NextData',
									'strTitle' => '会計データ',
									'strExplain' => '',
									'value' => 'complete',
									'flagErrorNow' => 0,
									'arrayError' => array(
										array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
										array( 'flagCheck' => 'attest', 'flagUse' => 1, 'strComment' => array(
											'suspenseReceiptOfConsumptionTaxes' => '',
											'suspensePaymentConsumptionTaxes' => '',
											'log' => '仕訳帳にまだ申請中の仕訳があるようです。当該仕訳の処理を確定させてください。',
											'logRetry' => '仕訳帳のフィルタリトライでまだ判定が済んでいないログがあるようです。当該ログの処理を確定させてください。',
											'logCashPay' => '収支管理でまだ決済処理が済んでいないログがあるようです。当該ログの処理を確定させてください。',
											'logCashDefer' => '収支管理の留保ログにまだ判定が済んでいないログがあるようです。当該ログの処理を確定させてください。',
											'logFiexedAssets' => '固定資産管理で登録されている資産の中に、作業領域不足のためバージョン更新ができないものがあるようです。固定資産管理に登録されている資産でバージョンが更新がなされ過ぎて修正が拒否されるものを削除扱いにし、新しく新規に登録し直してください。',),),
									),
									'flagContentUse' => 0,
									'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
									'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
									'numWidth' => 60, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
									'arrayOption' => array(
										array('strTitle' => '繰越処理をする', 'value' => 1),
										array('strTitle' => '仮繰越処理をする', 'value' => 0),
									),
									'flagDisabled' => 0,
									'varsTmpl' => array(
										'strNormal' => '会計データの処理を選択してください。<br>※ 仮繰越処理は、当期データを確定しないまま次期の作業を同時並行で行いたい場合に使用します。<br>※ 予め<span class="codeLibBaseFontTypeCheck" style="float:none;">データベースのバックアップ</span>をしておくことを推奨致します。<br>※ 手続後、ページを<span class="codeLibBaseFontTypeCheck" style="float:none;">自動更新</span>し、次期に移動します。',
										'strDone'   => '<span class="codeLibBaseFontOrange" style="float:none;">※ 繰越処理は既に完了しています。</span>',
										'strPast'   => '<span class="codeLibBaseFontOrange" style="float:none;">※ 次期データが既に作成されているため、『 繰越処理をする 』しか選択できません。</span><br>※ 手続後、ページを<span class="codeLibBaseFontTypeCheck" style="float:none;">自動更新</span>し、次期に移動します。',
										'strForgot' => '<span class="codeLibBaseFontOrange" style="float:none;">※ 前期データが確定するまで繰越処理はできません。</span>',
										'unknown' => 'まだ簡易課税区分に課税売上不明または課税売上返還不明が設定されてある仕訳があるようです。<p>仕訳帳にある以下の通番を適切な課税区分に振替えてください。</p><p><%strTitle%></p>',
										'suspenseReceiptOfConsumptionTaxes' => 'まだ<%strTitle%>に残高があるようです。適切な勘定に振替えて、当期残高をゼロにしてください。',
										'suspensePaymentConsumptionTaxes' => 'まだ<%strTitle%>に残高があるようです。適切な勘定に振替えて、当期残高をゼロにしてください。'
									),
								),
								array(
									'flagMustUse' => 1,
									'id' => 'NumFiscalClosingMonth',
									'strTitle' => '年度会計期末',
									'strExplain' => '',
									'value' => 0,
									'flagErrorNow' => 0,
									'arrayError' => array(
										array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
									),
									'flagContentUse' => 0,
									'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
									'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
									'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
									'arrayOption' => array(),
									'flagDisabled' => 0,
									'flagHideUse' => 1, 'flagHideNow' => 0,
									'varsTmpl' => array(
										'strNormal' => '次期の年度会計期末を変更したい場合は、任意の月末を選択してください。<br>　※ 現在の年度期末は、<%replace%>月末です。',
										'strDone'   => '<span class="codeLibBaseFontOrange" style="float:none;">※ 繰越処理は既に完了しています。</span>',
										'strPast'   => '<span class="codeLibBaseFontOrange" style="float:none;">※ 次期データが既に作成されているため変更できません。</span>',
										'strForgot' => '<span class="codeLibBaseFontOrange" style="float:none;">※ 前期データが確定するまで繰越処理はできません。</span>',
										'strMonth' => '月末',
										'arrClosingMonth' => array('strTitle' => '', 'value' => 0),
									),
								),
								array(
									'flagMustUse' => 1,
									'id' => 'FlagCR',
									'strTitle' => '製造原価報告書',
									'strExplain' => '',
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
										array( 'strTitle' => '使用する', 'value' => 1, ),
										array( 'strTitle' => '使用しない', 'value' => 0, ),
									),
									'flagHideUse' => 1, 'flagHideNow' => 1,
									'varsTmpl' => array(
										'strNormal' => '次期に製造原価報告書の勘定科目を採用するか選択してください。',
										'strDone'   => '<span class="codeLibBaseFontOrange" style="float:none;">※ 繰越処理は既に完了しています。</span>',
										'strPast'   => '<span class="codeLibBaseFontOrange" style="float:none;">※ 次期データが既に作成されているため変更できません。</span>',
										'strForgot' => '<span class="codeLibBaseFontOrange" style="float:none;">※ 前期データが確定するまで繰越処理はできません。</span>',
									),
								),
							),
						),
						'child' => array(),
					),
				),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 0, 'flagChildrenUse' => 1, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
				'strTitle' => '入力',
				'strClass' => 'codeLibBaseImgFolder',
				'vars' => array(
					'idTarget' => 'input', 'flagAccessUse' => 0, 'flagAllUse' => 0,
				),
				'child' => array(
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
						'strTitle' => '仕訳帳',
						'strClass' => 'codeLibBaseImgDetail',
						'vars' => array(
							'idTarget' => 'logWindow', 'flagAccessUse' => 1, 'flagAllUse' => 0,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
						'strTitle' => '証憑ファイル',
						'strClass' => 'codeLibBaseImgDetail',
						'vars' => array(
							'idTarget' => 'fileWindow', 'flagAccessUse' => 1, 'flagAllUse' => 0,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
						'strTitle' => '収支管理',
						'strClass' => 'codeLibBaseImgDetail',
						'vars' => array(
							'idTarget' => 'cashWindow', 'flagAccessUse' => 1, 'flagAllUse' => 0,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
						'strTitle' => '固定資産管理',
						'strClass' => 'codeLibBaseImgDetail',
						'vars' => array(
							'idTarget' => 'fixedAssetsWindow', 'flagAccessUse' => 1, 'flagAllUse' => 0,
						),
						'child' => array(),
					),
				),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 0, 'flagChildrenUse' => 1, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
				'strTitle' => '集計',
				'strClass' => 'codeLibBaseImgFolder',
				'vars' => array(
					'idTarget' => 'collect', 'flagAccessUse' => 0, 'flagAllUse' => 1,
				),
				'child' => array(
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
						'strTitle' => '元帳',
						'strClass' => 'codeLibBaseImgDetail',
						'vars' => array(
							'idTarget' => 'ledgerWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
						'strTitle' => '残高試算表',
						'strClass' => 'codeLibBaseImgDetail',
						'vars' => array(
							'idTarget' => 'trialBalanceWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1, 'numWidth' => 1150, 'numHeight' => 600,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
						'strTitle' => '消費税集計表',
						'strClass' => 'codeLibBaseImgDetail',
						'vars' => array(
							'idTarget' => 'consumptionTaxSheetWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
						'strTitle' => '科目別税区分表',
						'strClass' => 'codeLibBaseImgDetail',
						'vars' => array(
							'idTarget' => 'consumptionTaxListWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
						),
						'child' => array(),
					),
				),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 0, 'flagChildrenUse' => 1, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
				'strTitle' => '分析',
				'strClass' => 'codeLibBaseImgFolder',
				'vars' => array(
					'idTarget' => 'analize', 'flagAccessUse' => 0, 'flagAllUse' => 1,
				),
				'child' => array(
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
						'strTitle' => '財務分析',
						'strClass' => 'codeLibBaseImgDetail',
						'vars' => array(
							'idTarget' => 'financialAnalyzeWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
						'strTitle' => '損益分岐点分析',
						'strClass' => 'codeLibBaseImgDetail',
						'vars' => array(
							'idTarget' => 'breakEvenPointWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
						'strTitle' => '収支分析',
						'strClass' => 'codeLibBaseImgDetail',
						'vars' => array(
							'idTarget' => 'cashAnalyzeWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
						'strTitle' => '資金繰り分析',
						'strClass' => 'codeLibBaseImgDetail',
						'vars' => array(
							'idTarget' => 'cashPlanWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
						'strTitle' => '予算実績比較表',
						'strClass' => 'codeLibBaseImgDetail',
						'vars' => array(
							'idTarget' => 'budgetWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
						'strTitle' => '比較決算',
						'strClass' => 'codeLibBaseImgDetail',
						'vars' => array(
							'idTarget' => 'financialStatementMultiWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
						'strTitle' => '比較決算(CS)',
						'strClass' => 'codeLibBaseImgDetail',
						'vars' => array(
							'idTarget' => 'financialStatementMultiCSWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
						),
						'child' => array(),
					),
				),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 0, 'flagChildrenUse' => 1, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
				'strTitle' => '報告',
				'strClass' => 'codeLibBaseImgFolder',
				'vars' => array(
					'idTarget' => 'details', 'flagAccessUse' => 0, 'flagAllUse' => 1,
				),
				'child' => array(
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
						'strTitle' => '決算',
						'strClass' => 'codeLibBaseImgDetail',
						'vars' => array(
							'idTarget' => 'financialStatementWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
						'strTitle' => '決算(CS)',
						'strClass' => 'codeLibBaseImgDetail',
						'vars' => array(
							'idTarget' => 'financialStatementCSWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
						'strTitle' => '株主資本等変動計算書',
						'strClass' => 'codeLibBaseImgDetail',
						'vars' => array(
							'idTarget' => 'financialStatementSSWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
						'strTitle' => '販売費及び一般管理費の明細',
						'strClass' => 'codeLibBaseImgDetail',
						'vars' => array(
							'idTarget' => 'detailsSellingAndAdminWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
						'strTitle' => '個別注記表',
						'strClass' => 'codeLibBaseImgDetail',
						'vars' => array(
							'idTarget' => 'notesFSWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
						),
						'child' => array(),
					),
				),
			),
			array(
				'id' => 'Data', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 0, 'flagChildrenUse' => 1, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
				'strTitle' => '申告',
				'strClass' => 'codeLibBaseImgFolder',
				'vars' => array(
					'idTarget' => 'nation', 'flagAccessUse' => 0, 'flagAllUse' => 1,
				),
				'child' => array(
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
						'strTitle' => '法人事業概況説明書',
						'strClass' => 'codeLibBaseImgDetail',
						'vars' => array(
							'idTarget' => 'summaryStatementPublicWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 0, 'flagChildrenUse' => 1, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
						'strTitle' => '勘定科目内訳明細書',
						'strClass' => 'codeLibBaseImgFolder',
						'vars' => array(
							'idTarget' => 'detailedAccount', 'flagAccessUse' => 0, 'flagAllUse' => 1,
						),
						'child' => array(
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '預貯金等の内訳書',
								'strClass' => 'codeLibBaseImgDetail',
								'vars' => array(
									'idTarget' => 'detailedAccountDepositsWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '受取手形の内訳書',
								'strClass' => 'codeLibBaseImgDetail',
								'vars' => array(
									'idTarget' => 'detailedAccountNotesReceivableWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '売掛金(未収入金)の内訳書',
								'strClass' => 'codeLibBaseImgDetail',
								'vars' => array(
									'idTarget' => 'detailedAccountAccountsReceivableWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '仮払金(前渡金)の内訳書',
								'strClass' => 'codeLibBaseImgDetail',
								'vars' => array(
									'idTarget' => 'detailedAccountSuspensePaymentWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '貸付金及び受取利息の内訳書',
								'strClass' => 'codeLibBaseImgDetail',
								'vars' => array(
									'idTarget' => 'detailedAccountLoansReceivableWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '棚卸資産(商品...)の内訳書',
								'strClass' => 'codeLibBaseImgDetail',
								'vars' => array(
									'idTarget' => 'detailedAccountInventriesWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '有価証券の内訳書',
								'strClass' => 'codeLibBaseImgDetail',
								'vars' => array(
									'idTarget' => 'detailedAccountSecuritiesWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '固定資産(土地...)の内訳書',
								'strClass' => 'codeLibBaseImgDetail',
								'vars' => array(
									'idTarget' => 'detailedAccountFixedAssetsWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '支払手形の内訳書',
								'strClass' => 'codeLibBaseImgDetail',
								'vars' => array(
									'idTarget' => 'detailedAccountNotesPayableWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '買掛金(未払金・未払費用)の内訳書',
								'strClass' => 'codeLibBaseImgDetail',
								'vars' => array(
									'idTarget' => 'detailedAccountAccountsPayableWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '未払配当金の内訳書',
								'strClass' => 'codeLibBaseImgDetail',
								'vars' => array(
									'idTarget' => 'detailedAccountDividendsPayableWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '未払役員賞与の内訳書',
								'strClass' => 'codeLibBaseImgDetail',
								'vars' => array(
									'idTarget' => 'detailedAccountAccruedBonusToDirectorsWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '仮受金(前受金・預り金)の内訳書',
								'strClass' => 'codeLibBaseImgDetail',
								'vars' => array(
									'idTarget' => 'detailedAccountSuspenseReceiptWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '源泉所得税預り金の内訳',
								'strClass' => 'codeLibBaseImgDetail',
								'vars' => array(
									'idTarget' => 'detailedAccountIncomeTaxWithholdingWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '借入金及び支払利子の内訳書',
								'strClass' => 'codeLibBaseImgDetail',
								'vars' => array(
									'idTarget' => 'detailedAccountLoansPayableWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '土地の売上高等の内訳書',
								'strClass' => 'codeLibBaseImgDetail',
								'vars' => array(
									'idTarget' => 'detailedAccountLandWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '売上高等の事業所別の内訳書',
								'strClass' => 'codeLibBaseImgDetail',
								'vars' => array(
									'idTarget' => 'detailedAccountSalesWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '役員報酬手当等及び人件費の内訳書',
								'strClass' => 'codeLibBaseImgDetail',
								'vars' => array(
									'idTarget' => 'detailedAccountEmployeeWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '地代家賃の内訳書',
								'strClass' => 'codeLibBaseImgDetail',
								'vars' => array(
									'idTarget' => 'detailedAccountRentsWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '権利金等の期中支払の内訳',
								'strClass' => 'codeLibBaseImgDetail',
								'vars' => array(
									'idTarget' => 'detailedAccountKeyMoneyWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '工業所有権等の使用料の内訳書',
								'strClass' => 'codeLibBaseImgDetail',
								'vars' => array(
									'idTarget' => 'detailedAccountIndustrialPropertyWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '雑損失等の内訳書',
								'strClass' => 'codeLibBaseImgDetail',
								'vars' => array(
									'idTarget' => 'detailedAccountBadMiscellaneousExpensesWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
								'strTitle' => '雑益等の内訳書',
								'strClass' => 'codeLibBaseImgDetail',
								'vars' => array(
									'idTarget' => 'detailedAccountMiscellaneousIncomeWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
								),
								'child' => array(),
							),
						),
					),
				),
			),
		),
	),

);


