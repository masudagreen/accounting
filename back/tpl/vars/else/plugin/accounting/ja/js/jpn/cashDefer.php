<?php

$vars = array(
	'varsItem' => array(
		'strId' => '通番',
		'strMemo' => '摘要',
		'strBlank' => '＜空＞',
		'strTagTitle' => '収支管理',
		'strTagIn' => '収入',
		'strTagOut' => '支出',
		'strTagMove' => '移動',
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
					'strTitleHeaderLeft' => '留保ログ',
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
				'templateDetail' => array(
					'id' => 'Dummy', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 0, 'flagEditUse' => 0,
					'strTitle' => '',
					'strClass' => 'codeLibBaseImgSheet',
					'strClassLoad' => 'codeLibTreeLoad',
					'vars' => array(
						'idTarget' => 0,
					),
					'child' => array(),
				),
				'varsDetail' => array(
					'varsStatus' => array(
						'flagUse' => 1,
						'flagMoveUse' => 0,
						'flagInsertUse' => 0,
						'flagFoldNow' => 0,
						'flagFoldUse' => 0,
						'flagCakeUse' => 1,
						'flagRemoveUse' => 0,
						'flagCheckUse' => 0,
						'flagCheckNow' => 1,
						'flagBarUse' => 0,
						'flagBtnBottomUse' => 1,
						'flagBtnUse' => 1,
						'flagLockUse' => 0,
						'flagLockNow' => 0,
						'flagPageUse' => 1,
						'flagInnerPageUse' => 1,
						'flagFindUse' => 0,
						'id' => 'tree',
					),
					'varsFormat' => array(
						'id' => '',
						'flagType' => 'singleFormat',
						'numHeight' => 0,
						'numWidth' => 0,
						'flagHeaderUse' => 0,
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
						'flagHeaderLeftWidth' => 1,
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
					'varsBtn' => array(
						array(
							'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
							'id' => 'DeleteAllBtn',
							'strTitle' => 'ログ全削除',
							'vars' => array( 'idTarget' => 'deleteAll'),
						),
					),
					'varsDetail' => array(),
				),
			),
			'item' => array(),
			'folder' => array(),
		),
		'varsDetail' => array(
			'varsStart' => array(
				'strTitle' => '詳細',
				'strClass' => 'codeLibBaseImgSheet',
			),
			'varsEnd' => array(
				'strTitle' => '処理完了',
				'strClass' => 'codeLibBaseImgSheet',
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
			'varsEdit' => array( 'flagReloadUse' => 0, ),
			'tmplBtn' => array(
				'varsStart' => array(
					array(
						'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
						'id' => 'DeleteBtn',
						'strTitle' => 'ログ削除',
						'vars' => array( 'idTarget' => 'delete'),
					),
					array(
						'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
						'id' => 'AddBtn',
						'strTitle' => '保存',
						'vars' => array( 'idTarget' => 'add'),
					),
				),
				'varsEnd' => array(
					array(
						'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
						'id' => 'Hide',
						'vars' => array( 'idTarget' => 'backHide', 'flagHide' => 1,),
						'strTitle' => '閉じる',
					),
				),
			),
			'tmplDetail' => array(
				'varsStart' => array(
					array(
						'flagMustUse' => 0,
						'id' => 'Status',
						'strTitle' => '', 'strExplain' => '', 'value' => '',
						'flagErrorNow' => 0,
						'arrayError' => array(),
						'flagContentUse' => 0,
						'flagCommentUse' => 1, 'strCommentTitle' => '', 'strComment' => '',
						'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 0,
						'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
						'arrayOption' => array(),
						'flagFoldUse' => 0, 'flagFoldNow' => 0,
						'varsTmpl' => array(
							'strComment' => '【 記録日時 】 : <%stampRegister%><br>【 取引日時 】 : <%stampBook%>',
						),
					),
					array(
						'flagMustUse' => 1,
						'id' => 'IdLogCash',
						'strTitle' => '関連付け判定',
						'strExplain' => '',
						'value' => 'dummy',
						'flagErrorNow' => 1,
						'arrayError' => array(
							array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
						),
						'flagContentUse' => 0,
						'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
						'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
						'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
						'arrayOption' => array(
							array('strTitle' => '選択肢なし', 'value' => 'dummy',),
						),
						'flagFoldUse' => 1, 'flagFoldNow' => 0,
						'varsTmpl' => array(
							'strNormal' => 'どの収支管理のログを決済し仕訳帳へ書き出すか選択してください。',
							'strLost' => 'どの収支管理のログを決済し仕訳帳へ書き出すか選択してください。<br><span class="codeLibBaseFontOrange" style="float:none;">※ 陳腐化しているログのようです。</span>',
							'strNone' => 'どの収支管理のログを決済し仕訳帳へ書き出すか選択してください。<br>※ 関連付けられるログが収支管理にないようです。その場合は、確定済の収支ログを作成したうえで仕訳帳に書出します。</span>',
						),
					),
					array(
						'flagMustUse' => 0,
						'id' => 'JsonDetail',
						'strTitle' => '書出仕訳', 'strExplain' => '', 'value' => 'dummy',
						'flagErrorNow' => 0,
						'arrayError' => array(
							array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ),),
							array( 'flagCheck' => 'attest', 'flagUse' => 1, 'strComment' => array(
								'numSum' => '貸借合計金額が一致しないようです。',
								'numSumMax' => '貸借合計金額は、99,999,999,999が上限となっています。',
								'numRowMax' => '仕訳行数は、1行までが上限となっています。',
								'strDebit' => '借方に記入漏れの仕訳があるようです。',
								'strCredit' => '貸方に記入漏れの仕訳があるようです。',
								'strRow' => '借方と貸方に記入漏れの仕訳があるようです。',
								'strBlank' => '記入漏れのようです。',
								'strOldConsumption' => '保存されていた仕訳データが前提としている消費税設定が現在の設定と異なるため仕訳データを挿入できなかったようです。',
								'strOldIdAccountTitle' => '保存されていた勘定科目が現在存在しないため仕訳データを挿入できなかったようです。',
								'strOldIdAccountTitleCash' => '保存されていた勘定科目が現在現金の範囲ではないため仕訳データを挿入できなかったようです。',
								'strOldIdSubAccountTitle' => '保存されていた補助科目が現在存在しないため仕訳データを挿入できなかったようです。',
								'strOldIdDepartment' => '保存されていた部門が現在存在しないため仕訳データを挿入できなかったようです。',
								'strOld' => '仕訳が不完全か陳腐化しているため処理ができないようです。',
							),),
						),
						'varsTmpl' => array(
							'strLost' => '<span class="codeLibBaseFontOrange" style="float:none;">※ 仕訳が不完全か陳腐化しているため処理ができないようです。</span>',
							'strPermitLost' => '<span class="codeLibBaseFontOrange" style="float:none;">※ 仕訳に関連付けられている承認アカウントが陳腐化しているため処理ができないようです。</span>',
						),
						'flagContentUse' => 1,
						'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
						'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 10,
						'numWidth' => 90, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
						'arrayOption' => array(),
						'flagFoldUse' => 1, 'flagFoldNow' => 0,
						'varsFormJournal' => array(
							'varsStatus' => array(
								'flagAddUse' => 0,
								'flagSortUpUse' => 0,
								'flagSortDownUse' => 0,
								'flagSortSideUse' => 0,
								'flagSummaryUse' => 1,
								'flagEditUse' => 0,
								'flagCopyUse' => 0,
								'flagRemoveUse' => 1,
								'flagBtnTextUse' => 0,
								'flagBtnTextTaxUse' => 0,
								'numLeft' => 0,
								'numTop' => 0,
							),
							'varsSummary' => array(
								'numSumDebit' => 0,
								'numSumCredit' => 0,
								'numSumConsumptionTaxDebit' => 0,
								'numSumConsumptionTaxCredit' => 0,
								'strSumDebit' => '',
								'strSumCredit' => '',
								'strSumConsumptionTaxDebit' => '',
								'strSumConsumptionTaxCredit' => '',
							),
							'varsRule' => array(),
							'varsDetail' => array(),
							'varsTmpl' => array(
								'strLost' => '不明',
								'strDebitSum' => '借方合計',
								'strCreditSum' => '貸方合計',
								'strUnit' => '単位( 円 )',
								'strConsumptionTax' => '消費税',
								'varsSelectTag' => array(
									'flagConsumptionTaxWithoutCalc' => array(
										array('strTitle' => '内税','value' => 1,),
										array('strTitle' => '外税','value' => 2,),
										array('strTitle' => '別記','value' => 3,),
									),
									'numRateConsumptionTax' => array(
										array('strTitle' => '5%','value' => 5,),
										array('strTitle' => '8%','value' => 8,),
										/*
										 * 20191001 start
										 */
									    array('strTitle' => '8%(軽)','value' => '8_reduced',),
									    array('strTitle' => '10%','value' => 10,),
									    /*
									     * 20191001 end
									     */
									),
								),
								'varsStrTitle' => array(
									'flagConsumptionTaxWithoutCalc' => array(
										'1' => '内税',
										'2' => '外税',
										'3' => '別記',
									),
									'numRateConsumptionTax' => array(
										'5' => '5%',
										'8' => '8%',
										/*
										 * 20191001 start
										*/
									    '8_reduced' => '8%(軽)',
									    '10' => '10%',
										/*
										 * 20191001 end
										*/
									),
								),
								'varsBlank' => array(
									'strTitle' => '指定なし',
									'value' => '',
								),
								'varsDetail' => array(
									'idAccountTitleDebit' => '',
									'idAccountTitleCredit' => '',
									'numSum' => 0,
									'numSumDebit' => 0,
									'numSumCredit' => 0,
									'varsDetail' => array(),
									'varsEntityNation' => array(),
									'numVersionConsumptionTax' => 0,
								),
								'varsDetailVarsDetail' => array(
									'id' => '',
									'arrDebit' => array(
										'idAccountTitle' => '',
										'numValue' => '',
										'numValueConsumptionTax' => '',
										'numRateConsumptionTax' => '',
										/*
										 * 20191001 start
										 */
									    'flagRateConsumptionTaxReduced' => '',
									    /*
									     * 20191001 end
									     */
										'idDepartment' => '',
										'idSubAccountTitle' => '',
										'flagConsumptionTaxFree' => '',
										'flagConsumptionTaxIncluding' => '',
										'flagConsumptionTaxGeneralRuleEach' => '',
										'flagConsumptionTaxGeneralRuleProration' => '',
										'flagConsumptionTaxSimpleRule' => '',
										'flagConsumptionTaxWithoutCalc' => '',
										'flagConsumptionTaxCalc' => '',
									),
									'arrCredit' => array(
										'idAccountTitle' => '',
										'numValue' => '',
										'numValueConsumptionTax' => '',
										'numRateConsumptionTax' => '',
										/*
										 * 20191001 start
										 */
									    'flagRateConsumptionTaxReduced' => '',
									    /*
									     * 20191001 end
									     */
										'idDepartment' => '',
										'idSubAccountTitle' => '',
										'flagConsumptionTaxFree' => '',
										'flagConsumptionTaxIncluding' => '',
										'flagConsumptionTaxGeneralRuleEach' => '',
										'flagConsumptionTaxGeneralRuleProration' => '',
										'flagConsumptionTaxSimpleRule' => '',
										'flagConsumptionTaxWithoutCalc' => '',
										'flagConsumptionTaxCalc' => '',
									),
								),
								'varsFormTemp' => array(
									'varsStatus' => array(
										'numLeft' => 0,
										'numTop' => 0,
									),
									'varsDetail' => array(
										'flagTag'       => '',
										'flagInputType' => '',
										'numMaxlength'  => 11,
										'numWidth'      => 0,
										'unitWidth'     => 'px',
										'numHeight'     => 0,
										'unitHeight'    => 'px',
										'arrayOption'   => array(),
										'flagLine'      => '',
										'flagColumn'    => '',
										'value'         => '',
										'vars'          => array(),
									),
								),
							),
						),
					),
					array(
						'flagMustUse' => 0,
						'id' => 'JsonPermitHistory',
						'strTitle' => '書出仕訳 - 承認アカウント', 'strExplain' => '', 'value' => 'dummy',
						'flagErrorNow' => 0,
						'arrayError' => array(
							array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ),),
						),
						'flagContentUse' => 1,
						'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
						'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 10,
						'numWidth' => 90, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
						'arrayOption' => array(),
						'flagFoldUse' => 1, 'flagFoldNow' => 0,
						'varsFormCheck' => array(
							'varsStatus' => array(
								'flagBtnUse' => 0,
							),
							'varsColumn' => array(
								array(
									'id' => 'StrNo',
									'flagType' => '',
									'strTitle' => 'No',
									'numWidth' => 30,
								),
								array(
									'id' => 'StrCodeName',
									'flagType' => '',
									'strTitle' => '申請者',
									'numWidth' => 100,
								),
								array(
									'id' => 'StrNumSum',
									'flagType' => '',
									'strTitle' => '賛/反/要/全',
									'numWidth' => 100,
								),
								array(
									'id' => 'BtnDetail',
									'flagType' => 'btn',
									'strTitle' => '詳細',
									'numWidth' => 40,
								),
							),
							'varsDetail' => array(),
							'tmplDetail' => array(
								'id' => '',
								'varsContext' => array(),
								'varsColumnDetail' => array(
									'strNo' => '',
									'strStatus' => '',
									'strCodeName' => '',
									'stampRegister' => '',
									'stampPermit' => '',
									'strNumSum' => '',
									'btnDetailUse' => 1,
									'btnDetailLock' => 0,
									'btnDetailStrClass' => 'codeLibFormCheckImgDetail',
									'btnDetailStrClassLock' => 'codeLibFormCheckImgDetailLock',
								),
							),
							'tmplContext' => array(
								'varsStatus' => array(
									'numTop' => 0,
									'numLeft' => 0,
								),
								'varsDetail' => array(),
							),
							'tmplContextDetail' => array(
								'id' => '', 'flagCheckUse' => 0, 'flagCheckNow' => 0,
								'strTitle' => '', 'strClass' => '',
								'strClassFont' => 'codeLibBaseFontBlack',
								'vars' => array( 'idTarget' => '',),
								'child' => array(),
							),
						),
					),
				),
				'varsEnd' => array(
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
			'varsAnalyze' => array(
				'varsColumn' => array(),
				'varsDetail' => array(),
			),
			'varsPage' => array(),
			'varsDetail' => array(),
			'varsBtn' => array(),
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
					'varsFormat' => array(
						'id' => '',
						'flagType' => 'singleFormat',
						'numHeight' => 0,
						'numWidth' => 0,
						'flagHeaderUse' => 0,
						'flagHeaderLeftUse' => 1,
						'strTitleHeaderLeft' => '',
						'strClassHeaderLeft' => '',
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

						'flagHeaderLeftWidth' => 0,
						'numWidthHeaderLeft' => 0,
						'flagHeaderRightWidth' => 0,
						'numWidthHeaderRight' => 0,
						'flagFooderLeftWidth' => 0,
						'numWidthFooderLeft' => 0,
						'flagFooderRightWidth' => 0,
						'numWidthFooderRight' => 0,
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

	),
);


