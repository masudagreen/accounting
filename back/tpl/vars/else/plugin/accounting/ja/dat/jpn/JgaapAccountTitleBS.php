<?php

$vars = array(
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
		'strTitle' => '資産',
		'strClass' => 'codeLibBaseImgFolder',
		'vars' => array(
			'idTarget' => 'assets', 'flagDebit' => 1,
		),
		'child' => array(
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
				'strTitle' => '流動資産',
				'strClass' => 'codeLibBaseImgFolder',
				'vars' => array(
					'idTarget' => 'currentAssets', 'flagDebit' => 1,
				),
				'child' => array(
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
						'strTitle' => '現金及び預金',
						'strClass' => 'codeLibBaseImgFolder',
						'vars' => array(
							'idTarget' => 'cashAndTimeDeposits', 'flagDebit' => 1,
						),
						'child' => array(
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '現金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'cash', 'idAccountTitleJgaapFS' => 'cash',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'cash', 'flagMethodMinus' => '',
											'idAccountTitlePlus' => 'cash', 'flagMethodPlus' => '',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'cash', 'flagMethodMinus' => '',
											'idAccountTitlePlus' => 'cash', 'flagMethodPlus' => '',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '小口現金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'prettyCash', 'idAccountTitleJgaapFS' => 'cash',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'cash', 'flagMethodMinus' => '',
											'idAccountTitlePlus' => 'cash', 'flagMethodPlus' => '',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'cash', 'flagMethodMinus' => '',
											'idAccountTitlePlus' => 'cash', 'flagMethodPlus' => '',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '当座預金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'checkingAccounts', 'idAccountTitleJgaapFS' => 'cash',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'cash', 'flagMethodMinus' => '',
											'idAccountTitlePlus' => 'cash', 'flagMethodPlus' => '',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'cash', 'flagMethodMinus' => '',
											'idAccountTitlePlus' => 'cash', 'flagMethodPlus' => '',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '普通預金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'ordinaryDeposit', 'idAccountTitleJgaapFS' => 'cash',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'cash', 'flagMethodMinus' => '',
											'idAccountTitlePlus' => 'cash', 'flagMethodPlus' => '',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'cash', 'flagMethodMinus' => '',
											'idAccountTitlePlus' => 'cash', 'flagMethodPlus' => '',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '定期預金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'fixedDeposit', 'idAccountTitleJgaapFS' => 'cash',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'fixedDepositOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'fixedDepositIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'fixedDepositOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'fixedDepositIn', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '通知預金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'depositAtNotice', 'idAccountTitleJgaapFS' => 'cash',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'cash', 'flagMethodMinus' => '',
											'idAccountTitlePlus' => 'cash', 'flagMethodPlus' => '',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'cash', 'flagMethodMinus' => '',
											'idAccountTitlePlus' => 'cash', 'flagMethodPlus' => '',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '定期積立預金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'cumulativeTimeDeposit', 'idAccountTitleJgaapFS' => 'cash',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'fixedDepositOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'fixedDepositIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'fixedDepositOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'fixedDepositIn', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '別段預金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'miscDeposit', 'idAccountTitleJgaapFS' => 'cash',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'fixedDepositOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'fixedDepositIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'fixedDepositOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'fixedDepositIn', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '郵便貯金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'jpDeposit', 'idAccountTitleJgaapFS' => 'cash',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'cash', 'flagMethodMinus' => '',
											'idAccountTitlePlus' => 'cash', 'flagMethodPlus' => '',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'cash', 'flagMethodMinus' => '',
											'idAccountTitlePlus' => 'cash', 'flagMethodPlus' => '',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
						),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '現金及び預金合計',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'cashAndTimeDepositsSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
						'strTitle' => '売上債権',
						'strClass' => 'codeLibBaseImgFolder',
						'vars' => array(
							'idTarget' => 'accoutsReceivableTrade', 'flagDebit' => 1,
						),
						'child' => array(
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '受取手形',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'notesReceivable', 'idAccountTitleJgaapFS' => 'notesReceivable',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleIn', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'accoutsReceivableTradeFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'accoutsReceivableTradeFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '不渡手形',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'dishonoredBill', 'idAccountTitleJgaapFS' => 'dishonoredBill',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleIn', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'accoutsReceivableTradeFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'accoutsReceivableTradeFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '売掛金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'accountsReceivable', 'idAccountTitleJgaapFS' => 'accountsReceivable',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleIn', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'accoutsReceivableTradeFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'accoutsReceivableTradeFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '貸倒引当金(営業債権)',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'allowanceForBadDebtsTrade', 'idAccountTitleJgaapFS' => 'allowanceForBadDebtsTrade',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'allowanceForBadDebtsTrade', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'allowanceForBadDebtsTrade', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
						),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '売上債権合計',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'accoutsReceivableTradeSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
						'strTitle' => '有価証券',
						'strClass' => 'codeLibBaseImgFolder',
						'vars' => array(
							'idTarget' => 'securitiesWrap', 'flagDebit' => 1,
						),
						'child' => array(
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '有価証券',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'securities', 'idAccountTitleJgaapFS' => 'securities',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'securitiesOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'securitiesIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'securitiesOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'securitiesIn', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
						),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '有価証券合計',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'securitiesWrapSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
						'strTitle' => '棚卸資産',
						'strClass' => 'codeLibBaseImgFolder',
						'vars' => array(
							'idTarget' => 'inventries', 'flagDebit' => 1,
						),
						'child' => array(
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '商品',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'merchandise', 'idAccountTitleJgaapFS' => 'merchandise',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'inventriesFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'inventriesFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '製品',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'finishedGoods', 'idAccountTitleJgaapFS' => 'finishedGoods',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'inventriesFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'inventriesFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '副産物',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'byProduct', 'idAccountTitleJgaapFS' => 'finishedGoods',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'inventriesFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'inventriesFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '作業屑',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'byWork', 'idAccountTitleJgaapFS' => 'finishedGoods',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'inventriesFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'inventriesFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '半製品',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'semiFinishedGoods', 'idAccountTitleJgaapFS' => 'semiFinishedGoods',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'inventriesFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'inventriesFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '原材料',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'materials', 'idAccountTitleJgaapFS' => 'materials',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'inventriesFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'inventriesFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '仕掛品',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'workInProcess', 'idAccountTitleJgaapFS' => 'workInProcess',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'inventriesFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'inventriesFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '貯蔵品',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'stockGoods', 'idAccountTitleJgaapFS' => 'stockGoods',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'inventriesFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'inventriesFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
						),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '棚卸資産合計',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'inventriesSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
						'strTitle' => 'その他流動資産',
						'strClass' => 'codeLibBaseImgFolder',
						'vars' => array(
							'idTarget' => 'otherCurrentAssets', 'flagDebit' => 1,
						),
						'child' => array(
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '前渡金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'advancesAccount', 'idAccountTitleJgaapFS' => 'advancesAccount',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'assetsElseFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'assetsElseFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '立替金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'advances', 'idAccountTitleJgaapFS' => 'advances',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'assetsElseFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'assetsElseFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '前払費用',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'prepaidExpenses', 'idAccountTitleJgaapFS' => 'prepaidExpenses',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'assetsElseFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'assetsElseFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '繰延税金資産(流)',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'shortTermDeferredTaxAssets', 'idAccountTitleJgaapFS' => 'shortTermDeferredTaxAssets',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'corporateInhabitantAndEnterpriseTaxOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'corporateInhabitantAndEnterpriseTaxOut', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'corporateInhabitantAndEnterpriseTaxOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'corporateInhabitantAndEnterpriseTaxOut', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '未収収益',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'accruedIncome', 'idAccountTitleJgaapFS' => 'accruedIncome',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleElseIn', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleElseIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'assetsElseFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'assetsElseFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '短期貸付金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'shortTermLoansReceivable', 'idAccountTitleJgaapFS' => 'shortTermLoansReceivable',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'loansReceivableOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'loansReceivableIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'loansReceivableOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'loansReceivableIn', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '未収入金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'accruedRevenue', 'idAccountTitleJgaapFS' => 'accruedRevenue',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleElseIn', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleElseIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'assetsElseFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'assetsElseFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '仮払金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'suspensePaymentAccount', 'idAccountTitleJgaapFS' => 'suspensePaymentAccount',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'assetsElseFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'assetsElseFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '預け金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'keyMoney', 'idAccountTitleJgaapFS' => 'keyMoney',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'assetsElseFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'assetsElseFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '仮払消費税等',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'suspensePaymentConsumptionTaxes', 'idAccountTitleJgaapFS' => 'suspensePaymentConsumptionTaxes',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'consumptionTaxesRepayable', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'consumptionTaxesRepayable', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'consumptionTaxesRepayableFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'consumptionTaxesRepayableFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '貸倒引当金(営業外)',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'allowanceForBadDebtsShortTermOther', 'idAccountTitleJgaapFS' => 'allowanceForBadDebtsShortTermOther',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'allowanceForBadDebtsTrade', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'allowanceForBadDebtsTrade', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),

						),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => 'その他流動資産合計',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'otherCurrentAssetsSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
						),
						'child' => array(),
					),
				),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '流動資産合計',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'currentAssetsSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
				'strTitle' => '固定資産',
				'strClass' => 'codeLibBaseImgFolder',
				'vars' => array(
					'idTarget' => 'fixedAssets', 'flagDebit' => 1,
				),
				'child' => array(
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
						'strTitle' => '有形固定資産',
						'strClass' => 'codeLibBaseImgFolder',
						'vars' => array(
							'idTarget' => 'tangibleFixedAssets', 'flagDebit' => 1,
						),
						'child' => array(
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '建物',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'buildings', 'idAccountTitleJgaapFS' => 'buildings',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'tangibleFixedAssetsOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'tangibleFixedAssetsIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'tangibleFixedAssetsOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'tangibleFixedAssetsIn', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '附属設備',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'buildingsAndAccessoyEquipment', 'idAccountTitleJgaapFS' => 'buildingsAndAccessoyEquipment',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'tangibleFixedAssetsOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'tangibleFixedAssetsIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'tangibleFixedAssetsOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'tangibleFixedAssetsIn', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '構築物',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'structures', 'idAccountTitleJgaapFS' => 'structures',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'tangibleFixedAssetsOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'tangibleFixedAssetsIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'tangibleFixedAssetsOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'tangibleFixedAssetsIn', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '機械装置',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'machineryAndEquipment', 'idAccountTitleJgaapFS' => 'machineryAndEquipment',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'tangibleFixedAssetsOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'tangibleFixedAssetsIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'tangibleFixedAssetsOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'tangibleFixedAssetsIn', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '車両運搬具',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'car', 'idAccountTitleJgaapFS' => 'car',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'tangibleFixedAssetsOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'tangibleFixedAssetsIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'tangibleFixedAssetsOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'tangibleFixedAssetsIn', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '工具器具備品',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'furnitureAndFixture', 'idAccountTitleJgaapFS' => 'furnitureAndFixture',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'tangibleFixedAssetsOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'tangibleFixedAssetsIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'tangibleFixedAssetsOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'tangibleFixedAssetsIn', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '一括償却資産',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'lumpSumDepreciableAsset', 'idAccountTitleJgaapFS' => 'lumpSumDepreciableAsset',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'tangibleFixedAssetsOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'tangibleFixedAssetsIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'tangibleFixedAssetsOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'tangibleFixedAssetsIn', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '減価償却累計額',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'accumulatedDepreciation', 'idAccountTitleJgaapFS' => 'accumulatedDepreciation',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'tangibleFixedAssetsIn', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'tangibleFixedAssetsIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'tangibleFixedAssetsIn', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'tangibleFixedAssetsIn', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '土地',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'land', 'idAccountTitleJgaapFS' => 'land',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'tangibleFixedAssetsOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'tangibleFixedAssetsIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'tangibleFixedAssetsOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'tangibleFixedAssetsIn', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '建設仮勘定',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'constructionInProgress', 'idAccountTitleJgaapFS' => 'constructionInProgress',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'tangibleFixedAssetsOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'tangibleFixedAssetsOut', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'tangibleFixedAssetsOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'tangibleFixedAssetsOut', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
						),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '有形固定資産合計',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'tangibleFixedAssetsSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
						'strTitle' => '無形固定資産',
						'strClass' => 'codeLibBaseImgFolder',
						'vars' => array(
							'idTarget' => 'intangibleFixedAssets', 'flagDebit' => 1,
						),
						'child' => array(
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '電話加入権',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'telephoneSubscriptionRight', 'idAccountTitleJgaapFS' => 'telephoneSubscriptionRight',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'intangibleFixedAssetsOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'intangibleFixedAssetsIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'intangibleFixedAssetsOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'intangibleFixedAssetsIn', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '営業権',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'goodwill', 'idAccountTitleJgaapFS' => 'goodwill',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'intangibleFixedAssetsOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'intangibleFixedAssetsIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'intangibleFixedAssetsOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'intangibleFixedAssetsIn', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '借地権',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'leaseholdRight', 'idAccountTitleJgaapFS' => 'leaseholdRight',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'intangibleFixedAssetsOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'intangibleFixedAssetsIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'intangibleFixedAssetsOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'intangibleFixedAssetsIn', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => 'ソフトウェア',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'software', 'idAccountTitleJgaapFS' => 'software',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'intangibleFixedAssetsOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'intangibleFixedAssetsIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'intangibleFixedAssetsOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'intangibleFixedAssetsIn', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
						),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '無形固定資産合計',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'intangibleFixedAssetsSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
						'strTitle' => '投資その他の資産',
						'strClass' => 'codeLibBaseImgFolder',
						'vars' => array(
							'idTarget' => 'investmentsAndOtherAssets', 'flagDebit' => 1,
						),
						'child' => array(
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '投資有価証券',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'investmentsInSecurities', 'idAccountTitleJgaapFS' => 'investmentsInSecurities',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'investmentsInSecuritiesOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'investmentsInSecuritiesIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'investmentsInSecuritiesOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'investmentsInSecuritiesIn', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '関係会社株式',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'stocksOfAffiliatedCompanies', 'idAccountTitleJgaapFS' => 'stocksOfAffiliatedCompanies',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'investmentsInSecuritiesOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'investmentsInSecuritiesIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'investmentsInSecuritiesOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'investmentsInSecuritiesIn', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '出資金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'investments', 'idAccountTitleJgaapFS' => 'investments',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'investmentsInSecuritiesOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'investmentsInSecuritiesIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'investmentsInSecuritiesOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'investmentsInSecuritiesIn', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '関係会社出資金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'investmentsOfAffiliatedCompanie', 'idAccountTitleJgaapFS' => 'investmentsOfAffiliatedCompanie',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'investmentsInSecuritiesOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'investmentsInSecuritiesIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'investmentsInSecuritiesOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'investmentsInSecuritiesIn', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '敷金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'leaseDepositsAndSecurityDeposits', 'idAccountTitleJgaapFS' => 'leaseDepositsAndSecurityDeposits',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'assetsElseFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'assetsElseFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '差入保証金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'guaranteeDeposits', 'idAccountTitleJgaapFS' => 'guaranteeDeposits',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'assetsElseFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'assetsElseFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '長期貸付金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'longTermLoansReceivable', 'idAccountTitleJgaapFS' => 'longTermLoansReceivable',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'loansReceivableOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'loansReceivableIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'loansReceivableOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'loansReceivableIn', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '長期性預金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'longTermDeposits', 'idAccountTitleJgaapFS' => 'longTermDeposits',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'fixedDepositOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'fixedDepositIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'fixedDepositOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'fixedDepositIn', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '長期滞留債権',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'stayCredit', 'idAccountTitleJgaapFS' => 'stayCredit',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleElseIn', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleElseIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'accoutsReceivableTradeFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'accoutsReceivableTradeFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '長期前払費用',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'longTermPrepaidExpenses', 'idAccountTitleJgaapFS' => 'longTermPrepaidExpenses',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'assetsElseFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'assetsElseFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '繰延税金資産(固)',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'longTermDefferedTaxAssets', 'idAccountTitleJgaapFS' => 'longTermDefferedTaxAssets',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'corporateInhabitantAndEnterpriseTaxOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'corporateInhabitantAndEnterpriseTaxOut', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'corporateInhabitantAndEnterpriseTaxOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'corporateInhabitantAndEnterpriseTaxOut', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '預託金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'deposit', 'idAccountTitleJgaapFS' => 'deposit',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'assetsElseFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'assetsElseFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,

								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '未収消費税等',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'accruedConsumptionTaxes', 'idAccountTitleJgaapFS' => 'accruedConsumptionTaxes',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'consumptionTaxesRepayable', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'consumptionTaxesRepayable', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'consumptionTaxesRepayableFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'consumptionTaxesRepayableFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '貸倒引当金(営外固)',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'allowanceForBadDebtsLongTermOther', 'idAccountTitleJgaapFS' => 'allowanceForBadDebtsLongTermOther',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'allowanceForBadDebtsTrade', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'allowanceForBadDebtsTrade', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
						),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '投資その他の資産合計',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'investmentsAndOtherAssetsSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
						),
						'child' => array(),
					),
				),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '固定資産合計',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'fixedAssetsSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
				'strTitle' => '繰延資産',
				'strClass' => 'codeLibBaseImgFolder',
				'vars' => array(
					'idTarget' => 'deferredAssets', 'flagDebit' => 1,
				),
				'child' => array(
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '創立費',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'inauguralExpenses', 'idAccountTitleJgaapFS' => 'inauguralExpenses',
							'varsJgaapCS' => array(
								'varsDirect' => array(
									'idAccountTitleMinus' => 'investElseOut', 'flagMethodMinus' => 'sumDebit',
									'idAccountTitlePlus' => 'investElseIn', 'flagMethodPlus' => 'sumCredit',
								),
								'varsInDirect' => array(
									'idAccountTitleMinus' => 'investElseOut', 'flagMethodMinus' => 'sumDebit',
									'idAccountTitlePlus' => 'investElseIn', 'flagMethodPlus' => 'sumCredit',
								),
							),
							'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
							'varsValue' => array(), 'flagSortUse' => 1,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '開業費',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'developmentExpenses', 'idAccountTitleJgaapFS' => 'developmentExpenses',
							'varsJgaapCS' => array(
								'varsDirect' => array(
									'idAccountTitleMinus' => 'investElseOut', 'flagMethodMinus' => 'sumDebit',
									'idAccountTitlePlus' => 'investElseIn', 'flagMethodPlus' => 'sumCredit',
								),
								'varsInDirect' => array(
									'idAccountTitleMinus' => 'investElseOut', 'flagMethodMinus' => 'sumDebit',
									'idAccountTitlePlus' => 'investElseIn', 'flagMethodPlus' => 'sumCredit',
								),
							),

							'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
							'varsValue' => array(), 'flagSortUse' => 1,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '開発費',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'startUpCosts', 'idAccountTitleJgaapFS' => 'startUpCosts',
							'varsJgaapCS' => array(
								'varsDirect' => array(
									'idAccountTitleMinus' => 'investElseOut', 'flagMethodMinus' => 'sumDebit',
									'idAccountTitlePlus' => 'investElseIn', 'flagMethodPlus' => 'sumCredit',
								),
								'varsInDirect' => array(
									'idAccountTitleMinus' => 'investElseOut', 'flagMethodMinus' => 'sumDebit',
									'idAccountTitlePlus' => 'investElseIn', 'flagMethodPlus' => 'sumCredit',
								),
							),

							'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
							'varsValue' => array(), 'flagSortUse' => 1,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '試験研究費',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'experimentationAndResearchExpenses', 'idAccountTitleJgaapFS' => 'experimentationAndResearchExpenses',
							'varsJgaapCS' => array(
								'varsDirect' => array(
									'idAccountTitleMinus' => 'investElseOut', 'flagMethodMinus' => 'sumDebit',
									'idAccountTitlePlus' => 'investElseIn', 'flagMethodPlus' => 'sumCredit',
								),
								'varsInDirect' => array(
									'idAccountTitleMinus' => 'investElseOut', 'flagMethodMinus' => 'sumDebit',
									'idAccountTitlePlus' => 'investElseIn', 'flagMethodPlus' => 'sumCredit',
								),
							),

							'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
							'varsValue' => array(), 'flagSortUse' => 1,
						),
						'child' => array(),
					),
				),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '繰延資産合計',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'deferredAssetsSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
				),
				'child' => array(),
			),
		),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '資産の部合計',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'assetsSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
		'strTitle' => '負債',
		'strClass' => 'codeLibBaseImgFolder',
		'vars' => array(
			'idTarget' => 'liabilities', 'flagDebit' => 0,
		),
		'child' => array(
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
				'strTitle' => '流動負債',
				'strClass' => 'codeLibBaseImgFolder',
				'vars' => array(
					'idTarget' => 'currentLiabilities', 'flagDebit' => 0,
				),
				'child' => array(
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
						'strTitle' => '仕入債務',
						'strClass' => 'codeLibBaseImgFolder',
						'vars' => array(
							'idTarget' => 'accountsPayableTrade', 'flagDebit' => 0,
						),
						'child' => array(
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '支払手形',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'notesPayable', 'idAccountTitleJgaapFS' => 'notesPayable',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleOut', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'accountsPayableTradeFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'accountsPayableTradeFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '買掛金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'accountsAmountPayable', 'idAccountTitleJgaapFS' => 'accountsAmountPayable',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleOut', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'accountsPayableTradeFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'accountsPayableTradeFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
						),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '仕入債務合計',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'accountsPayableTradeSum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
						'strTitle' => 'その他流動負債',
						'strClass' => 'codeLibBaseImgFolder',
						'vars' => array(
							'idTarget' => 'otherCurrentLiabilities', 'flagDebit' => 0,
						),
						'child' => array(
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '営業外支払手形',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'nonOperatingNotesPayable', 'idAccountTitleJgaapFS' => 'nonOperatingNotesPayable',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'tangibleFixedAssetsOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'tangibleFixedAssetsOut', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'tangibleFixedAssetsOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'tangibleFixedAssetsOut', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '短期借入金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'shortTermLoansPayable', 'idAccountTitleJgaapFS' => 'shortTermLoansPayable',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'shortTermLoansPayableOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'shortTermLoansPayableIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'shortTermLoansPayableOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'shortTermLoansPayableIn', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '未払金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'shortTermAccruedAmountPayable', 'idAccountTitleJgaapFS' => 'shortTermAccruedAmountPayable',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'liabilitiesElseFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'liabilitiesElseFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),

									'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '未払費用',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'accruedExpenses', 'idAccountTitleJgaapFS' => 'accruedExpenses',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'liabilitiesElseFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'liabilitiesElseFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '未払配当金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'dividendsPayable', 'idAccountTitleJgaapFS' => 'dividendsPayable',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'dividendsOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'dividendsOut', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'dividendsOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'dividendsOut', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '未払役員賞与',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'accruedBonusToDirectors', 'idAccountTitleJgaapFS' => 'accruedBonusToDirectors',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'laborOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'laborOut', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'liabilitiesElseFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'liabilitiesElseFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '未払法人税等',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'corporationTaxesPayable', 'idAccountTitleJgaapFS' => 'corporationTaxesPayable',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'corporateInhabitantAndEnterpriseTaxOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'corporateInhabitantAndEnterpriseTaxOut', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'corporateInhabitantAndEnterpriseTaxOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'corporateInhabitantAndEnterpriseTaxOut', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '未払消費税等',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'consumptionTaxesRepayable', 'idAccountTitleJgaapFS' => 'consumptionTaxesRepayable',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'consumptionTaxesRepayable', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'consumptionTaxesRepayable', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'consumptionTaxesRepayableFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'consumptionTaxesRepayableFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '繰延税金負債(流)',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'shortTermDeferredTaxLiability', 'idAccountTitleJgaapFS' => 'shortTermDeferredTaxLiability',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'corporateInhabitantAndEnterpriseTaxOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'corporateInhabitantAndEnterpriseTaxOut', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'corporateInhabitantAndEnterpriseTaxOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'corporateInhabitantAndEnterpriseTaxOut', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '前受金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'advancesByCustomers', 'idAccountTitleJgaapFS' => 'advancesByCustomers',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleIn', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'liabilitiesElseFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'liabilitiesElseFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '預り金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'depositePayable', 'idAccountTitleJgaapFS' => 'depositePayable',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleElseIn', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleElseIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'liabilitiesElseFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'liabilitiesElseFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '前受収益',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'deferredIncome', 'idAccountTitleJgaapFS' => 'deferredIncome',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleElseIn', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleElseIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'liabilitiesElseFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'liabilitiesElseFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '仮受金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'suspenseReceipt', 'idAccountTitleJgaapFS' => 'suspenseReceipt',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleElseIn', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleElseIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'liabilitiesElseFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'liabilitiesElseFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '預り保証金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'shortTermGuaranteeDeposited', 'idAccountTitleJgaapFS' => 'shortTermGuaranteeDeposited',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleIn', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'liabilitiesElseFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'liabilitiesElseFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '割引手形',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'discountedBill', 'idAccountTitleJgaapFS' => 'discountedBill',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleIn', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'discountedBillFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'discountedBillFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '裏書手形',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'endorsementBill', 'idAccountTitleJgaapFS' => 'endorsementBill',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleOut', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'accountsPayableTradeFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'accountsPayableTradeFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '仮受消費税等',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'suspenseReceiptOfConsumptionTaxes', 'idAccountTitleJgaapFS' => 'suspenseReceiptOfConsumptionTaxes',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'consumptionTaxesRepayable', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'consumptionTaxesRepayable', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'consumptionTaxesRepayableFluctuation', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'consumptionTaxesRepayableFluctuation', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'tax', 'flagConsumptionTaxGeneralRuleProration' => 'tax', 'flagConsumptionTaxSimpleRule' => 'tax-Default',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
						),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => 'その他流動負債合計',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'otherCurrentLiabilitiesSum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
						),
						'child' => array(),
					),
				),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '流動負債合計',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'currentLiabilitiesSum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
				'strTitle' => '固定負債',
				'strClass' => 'codeLibBaseImgFolder',
				'vars' => array(
					'idTarget' => 'fixedLiabilities', 'flagDebit' => 0,
				),
				'child' => array(
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '長期借入金',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'longTermLoansPayable', 'idAccountTitleJgaapFS' => 'longTermLoansPayable',
							'varsJgaapCS' => array(
								'varsDirect' => array(
									'idAccountTitleMinus' => 'longTermLoansPayableOut', 'flagMethodMinus' => 'sumDebit',
									'idAccountTitlePlus' => 'longTermLoansPayableIn', 'flagMethodPlus' => 'sumCredit',
								),
								'varsInDirect' => array(
									'idAccountTitleMinus' => 'longTermLoansPayableOut', 'flagMethodMinus' => 'sumDebit',
									'idAccountTitlePlus' => 'longTermLoansPayableIn', 'flagMethodPlus' => 'sumCredit',
								),
							),
							'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
							'varsValue' => array(), 'flagSortUse' => 1,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '長期未払金',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'longTermAccruedAmountPayable', 'idAccountTitleJgaapFS' => 'longTermAccruedAmountPayable',
							'varsJgaapCS' => array(
								'varsDirect' => array(
									'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
									'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
								),
								'varsInDirect' => array(
									'idAccountTitleMinus' => 'liabilitiesElseFluctuation', 'flagMethodMinus' => 'sumDebit',
									'idAccountTitlePlus' => 'liabilitiesElseFluctuation', 'flagMethodPlus' => 'sumCredit',
								),
							),

							'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
							'varsValue' => array(), 'flagSortUse' => 1,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '繰延税金負債',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'longTermDeferredTaxLiability', 'idAccountTitleJgaapFS' => 'longTermDeferredTaxLiability',
							'varsJgaapCS' => array(
								'varsDirect' => array(
									'idAccountTitleMinus' => 'corporateInhabitantAndEnterpriseTaxOut', 'flagMethodMinus' => 'sumDebit',
									'idAccountTitlePlus' => 'corporateInhabitantAndEnterpriseTaxOut', 'flagMethodPlus' => 'sumCredit',
								),
								'varsInDirect' => array(
									'idAccountTitleMinus' => 'corporateInhabitantAndEnterpriseTaxOut', 'flagMethodMinus' => 'sumDebit',
									'idAccountTitlePlus' => 'corporateInhabitantAndEnterpriseTaxOut', 'flagMethodPlus' => 'sumCredit',
								),
							),
							'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
							'varsValue' => array(), 'flagSortUse' => 1,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '退職給付引当金',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'reserveForRetirementAllowance', 'idAccountTitleJgaapFS' => 'reserveForRetirementAllowance',
							'varsJgaapCS' => array(
								'varsDirect' => array(
									'idAccountTitleMinus' => 'laborOut', 'flagMethodMinus' => 'sumDebit',
									'idAccountTitlePlus' => 'laborOut', 'flagMethodPlus' => 'sumCredit',
								),
								'varsInDirect' => array(
									'idAccountTitleMinus' => 'reserveForRetirementAllowance', 'flagMethodMinus' => 'sumDebit',
									'idAccountTitlePlus' => 'reserveForRetirementAllowance', 'flagMethodPlus' => 'sumCredit',
								),
							),
							'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
							'varsValue' => array(), 'flagSortUse' => 1,
						),
						'child' => array(),
					),
				),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '固定負債合計',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'fixedLiabilitiesSum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
				),
				'child' => array(),
			),
		),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '負債の部合計',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'liabilitiesSum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
		'strTitle' => '純資産',
		'strClass' => 'codeLibBaseImgFolder', 'flagDebit' => 0,
		'vars' => array(
			'idTarget' => 'netAssets', 'flagDebit' => 0,
		),
		'child' => array(
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
				'strTitle' => '株主資本',
				'strClass' => 'codeLibBaseImgFolder',
				'vars' => array(
					'idTarget' => 'shareholdersEquity', 'flagDebit' => 0,
				),
				'child' => array(
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '資本金',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'commonStock', 'idAccountTitleJgaapFS' => 'commonStock',
							'varsJgaapCS' => array(
								'varsDirect' => array(
									'idAccountTitleMinus' => 'financeElseOut', 'flagMethodMinus' => 'sumDebit',
									'idAccountTitlePlus' => 'stockIn', 'flagMethodPlus' => 'sumCredit',
								),
								'varsInDirect' => array(
									'idAccountTitleMinus' => 'financeElseOut', 'flagMethodMinus' => 'sumDebit',
									'idAccountTitlePlus' => 'stockIn', 'flagMethodPlus' => 'sumCredit',
								),
							),
							'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
							'varsValue' => array(), 'flagSortUse' => 0,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
						'strTitle' => '新株式申込証拠金',
						'strClass' => 'codeLibBaseImgFolder',
						'vars' => array(
							'idTarget' => 'advanceOnSubscriptionForNewStockWrap', 'flagDebit' => 0,
						),
						'child' => array(
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '新株式申込証拠金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'advanceOnSubscriptionForNewStock', 'idAccountTitleJgaapFS' => 'advanceOnSubscriptionForNewStock',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'stockIn', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'stockIn', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'stockIn', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'stockIn', 'flagMethodPlus' => 'sumCredit',
										),
									),
									'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
						),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '新株式申込証拠金合計',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'advanceOnSubscriptionForNewStockWrapSum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
						'strTitle' => '資本剰余金',
						'strClass' => 'codeLibBaseImgFolder',
						'vars' => array(
							'idTarget' => 'capitalSurplus', 'flagDebit' => 0,
						),
						'child' => array(
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
								'strTitle' => '資本準備金',
								'strClass' => 'codeLibBaseImgFolder',
								'vars' => array(
									'idTarget' => 'capitalReserveWrap', 'flagDebit' => 0,
								),
								'child' => array(
									array(
										'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
										'strTitle' => '資本準備金',
										'strClass' => 'codeLibBaseImgSheet',
										'vars' => array(
											'idTarget' => 'capitalReserve', 'idAccountTitleJgaapFS' => 'capitalReserve',
											'varsJgaapCS' => array(
												'varsDirect' => array(
													'idAccountTitleMinus' => 'financeElseOut', 'flagMethodMinus' => 'sumDebit',
													'idAccountTitlePlus' => 'stockIn', 'flagMethodPlus' => 'sumCredit',
												),
												'varsInDirect' => array(
													'idAccountTitleMinus' => 'financeElseOut', 'flagMethodMinus' => 'sumDebit',
													'idAccountTitlePlus' => 'stockIn', 'flagMethodPlus' => 'sumCredit',
												),
											),
											'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
											'varsValue' => array(), 'flagSortUse' => 1,
										),
										'child' => array(),
									),
								),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '資本準備金合計',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'capitalReserveWrapSum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
								'strTitle' => 'その他資本剰余金',
								'strClass' => 'codeLibBaseImgFolder',
								'vars' => array(
									'idTarget' => 'otherCapitalSurplus', 'flagDebit' => 0,
								),
								'child' => array(
									array(
										'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
										'strTitle' => '資本金及び準備金減少差益',
										'strClass' => 'codeLibBaseImgSheet',
										'vars' => array(
											'idTarget' => 'transferFromCommonStockAndCapitalSurplusReserve', 'idAccountTitleJgaapFS' => 'otherCapitalSurplus',
											'varsJgaapCS' => array(
												'varsDirect' => array(
													'idAccountTitleMinus' => 'financeElseOut', 'flagMethodMinus' => 'sumDebit',
													'idAccountTitlePlus' => 'financeElseOut', 'flagMethodPlus' => 'sumCredit',
												),
												'varsInDirect' => array(
													'idAccountTitleMinus' => 'financeElseOut', 'flagMethodMinus' => 'sumDebit',
													'idAccountTitlePlus' => 'financeElseOut', 'flagMethodPlus' => 'sumCredit',
												),
											),
											'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
											'varsValue' => array(), 'flagSortUse' => 1,
										),
										'child' => array(),
									),
									array(
										'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
										'strTitle' => '自己株式処分差益',
										'strClass' => 'codeLibBaseImgSheet',
										'vars' => array(
											'idTarget' => 'disposalOfTreasuryStock', 'idAccountTitleJgaapFS' => 'otherCapitalSurplus',
											'varsJgaapCS' => array(
												'varsDirect' => array(
													'idAccountTitleMinus' => 'financeElseIn', 'flagMethodMinus' => 'sumDebit',
													'idAccountTitlePlus' => 'financeElseIn', 'flagMethodPlus' => 'sumCredit',
												),
												'varsInDirect' => array(
													'idAccountTitleMinus' => 'financeElseIn', 'flagMethodMinus' => 'sumDebit',
													'idAccountTitlePlus' => 'financeElseIn', 'flagMethodPlus' => 'sumCredit',
												),
											),
											'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
											'varsValue' => array(), 'flagSortUse' => 1,
										),
										'child' => array(),
									),
								),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => 'その他資本剰余金合計',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'otherCapitalSurplusSum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
								),
								'child' => array(),
							),
						),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '資本剰余金合計',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'capitalSurplusSum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
						'strTitle' => '利益剰余金',
						'strClass' => 'codeLibBaseImgFolder',
						'vars' => array(
							'idTarget' => 'retainedEarnings', 'flagDebit' => 0,
						),
						'child' => array(
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
								'strTitle' => '利益準備金',
								'strClass' => 'codeLibBaseImgFolder',
								'vars' => array(
									'idTarget' => 'legalReserveOfRetainedEarningsWrap', 'flagDebit' => 0,
								),
								'child' => array(
									array(
										'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
										'strTitle' => '利益準備金',
										'strClass' => 'codeLibBaseImgSheet',
										'vars' => array(
											'idTarget' => 'legalReserveOfRetainedEarnings', 'idAccountTitleJgaapFS' => 'legalReserveOfRetainedEarnings',
											'varsJgaapCS' => array(
												'varsDirect' => array(
													'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'sumDebit',
													'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'sumCredit',
												),
												'varsInDirect' => array(
													'idAccountTitleMinus' => 'cashElseFluctuation', 'flagMethodMinus' => 'sumDebit',
													'idAccountTitlePlus' => 'cashElseFluctuation', 'flagMethodPlus' => 'sumCredit',
												),
											),
											'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
											'varsValue' => array(), 'flagSortUse' => 1,
										),
										'child' => array(),
									),
								),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '利益準備金合計',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'legalReserveOfRetainedEarningsWrapSum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
								'strTitle' => 'その他利益剰余金',
								'strClass' => 'codeLibBaseImgFolder',
								'vars' => array(
									'idTarget' => 'otherSurplus', 'flagDebit' => 0,
								),
								'child' => array(
									array(
										'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
										'strTitle' => '任意積立金',
										'strClass' => 'codeLibBaseImgFolder',
										'vars' => array(
											'idTarget' => 'voluntaryReserves', 'flagDebit' => 0,
										),
										'child' => array(
											array(
												'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
												'strTitle' => '別途積立金',
												'strClass' => 'codeLibBaseImgSheet',
												'vars' => array(
													'idTarget' => 'otherReserves', 'idAccountTitleJgaapFS' => 'otherReserves',
													'varsJgaapCS' => array(
														'varsDirect' => array(
															'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'sumDebit',
															'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'sumCredit',
														),
														'varsInDirect' => array(
															'idAccountTitleMinus' => 'cashElseFluctuation', 'flagMethodMinus' => 'sumDebit',
															'idAccountTitlePlus' => 'cashElseFluctuation', 'flagMethodPlus' => 'sumCredit',
														),
													),
													'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
													'varsValue' => array(), 'flagSortUse' => 1,
												),
												'child' => array(),
											),
										),
									),
									array(
										'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
										'strTitle' => '任意積立金合計',
										'strClass' => 'codeLibBaseImgSheet',
										'vars' => array(
											'idTarget' => 'voluntaryReservesSum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
										),
										'child' => array(),
									),
									array(
										'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
										'strTitle' => '繰越利益剰余金',
										'strClass' => 'codeLibBaseImgFolder',
										'vars' => array(
											'idTarget' => 'unappropriatedRetainedEarnings', 'flagDebit' => 0,
										),
										'child' => array(
											array(
												'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
												'strTitle' => '繰越利益',
												'strClass' => 'codeLibBaseImgSheet',
												'vars' => array(
													'idTarget' => 'profitBroughtForward', 'idAccountTitleJgaapFS' => 'unappropriatedRetainedEarnings',
													'varsJgaapCS' => array(
														'varsDirect' => array(
															'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'sumDebit',
															'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'sumCredit',
														),
														'varsInDirect' => array(
															'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'sumDebit',
															'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'sumCredit',
														),
													),
													'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
													'varsValue' => array(), 'flagSortUse' => 0,
												),
												'child' => array(),
											),
											array(
												'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
												'strTitle' => '当期純利益',
												'strClass' => 'codeLibBaseImgSheet',
												'vars' => array(
													'idTarget' => 'netIncome', 'idAccountTitleJgaapFS' => 'unappropriatedRetainedEarnings', 'varsValue' => array(),
												),
												'child' => array(),
											),
										),
									),
									array(
										'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
										'strTitle' => '繰越利益剰余金合計',
										'strClass' => 'codeLibBaseImgSheet',
										'vars' => array(
											'idTarget' => 'unappropriatedRetainedEarningsSum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
										),
										'child' => array(),
									),
								),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => 'その他利益剰余金合計',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'otherSurplusSum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
								),
								'child' => array(),
							),
						),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '利益剰余金合計',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'retainedEarningsSum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '自己株式',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'treasuryStock', 'idAccountTitleJgaapFS' => 'treasuryStock',
							'varsJgaapCS' => array(
								'varsDirect' => array(
									'idAccountTitleMinus' => 'stockSelfOut', 'flagMethodMinus' => 'sumDebit',
									'idAccountTitlePlus' => 'financeElseIn', 'flagMethodPlus' => 'sumCredit',
								),
								'varsInDirect' => array(
									'idAccountTitleMinus' => 'stockSelfOut', 'flagMethodMinus' => 'sumDebit',
									'idAccountTitlePlus' => 'financeElseIn', 'flagMethodPlus' => 'sumCredit',
								),
							),
							'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
							'varsValue' => array(), 'flagSortUse' => 0,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '自己株式申込証拠金',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'advanceOnSubscriptionForReissuanceOfTreasuryStock', 'idAccountTitleJgaapFS' => 'advanceOnSubscriptionForReissuanceOfTreasuryStock',
							'varsJgaapCS' => array(
								'varsDirect' => array(
									'idAccountTitleMinus' => 'stockSelfOut', 'flagMethodMinus' => 'sumDebit',
									'idAccountTitlePlus' => 'financeElseIn', 'flagMethodPlus' => 'sumCredit',
								),
								'varsInDirect' => array(
									'idAccountTitleMinus' => 'stockSelfOut', 'flagMethodMinus' => 'sumDebit',
									'idAccountTitlePlus' => 'financeElseIn', 'flagMethodPlus' => 'sumCredit',
								),
							),
							'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
							'varsValue' => array(), 'flagSortUse' => 0,
						),
						'child' => array(),
					),
				),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '株主資本合計',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'shareholdersEquitySum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
				'strTitle' => '評価・換算差額等',
				'strClass' => 'codeLibBaseImgFolder',
				'vars' => array(
					'idTarget' => 'valuationAndTranslationAdjustments', 'flagDebit' => 0,
				),
				'child' => array(
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => 'その他有価証券評価差額金',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'unrealizedGainOnMarketableSecurities', 'idAccountTitleJgaapFS' => 'unrealizedGainOnMarketableSecurities',
							'varsJgaapCS' => array(
								'varsDirect' => array(
									'idAccountTitleMinus' => 'investmentsInSecuritiesIn', 'flagMethodMinus' => 'sumDebit',
									'idAccountTitlePlus' => 'investmentsInSecuritiesOut', 'flagMethodPlus' => 'sumCredit',
								),
								'varsInDirect' => array(
									'idAccountTitleMinus' => 'investmentsInSecuritiesIn', 'flagMethodMinus' => 'sumDebit',
									'idAccountTitlePlus' => 'investmentsInSecuritiesOut', 'flagMethodPlus' => 'sumCredit',
								),
							),
							'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
							'varsValue' => array(), 'flagSortUse' => 1,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '繰延ヘッジ損益',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'gainOnDeferredHedge', 'idAccountTitleJgaapFS' => 'gainOnDeferredHedge',
							'varsJgaapCS' => array(
								'varsDirect' => array(
									'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'sumDebit',
									'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'sumCredit',
								),
								'varsInDirect' => array(
									'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'sumDebit',
									'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'sumCredit',
								),
							),
							'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
							'varsValue' => array(), 'flagSortUse' => 1,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '土地再評価差額金',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'landRevaluationDifference', 'idAccountTitleJgaapFS' => 'landRevaluationDifference',
							'varsJgaapCS' => array(
								'varsDirect' => array(
									'idAccountTitleMinus' => 'tangibleFixedAssetsIn', 'flagMethodMinus' => 'sumDebit',
									'idAccountTitlePlus' => 'tangibleFixedAssetsOut', 'flagMethodPlus' => 'sumCredit',
								),
								'varsInDirect' => array(
									'idAccountTitleMinus' => 'tangibleFixedAssetsIn', 'flagMethodMinus' => 'sumDebit',
									'idAccountTitlePlus' => 'tangibleFixedAssetsOut', 'flagMethodPlus' => 'sumCredit',
								),
							),
							'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
							'varsValue' => array(), 'flagSortUse' => 1,
						),
						'child' => array(),
					),
				),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '評価・換算差額等合計',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'valuationAndTranslationAdjustmentsSum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
				'strTitle' => '新株予約権',
				'strClass' => 'codeLibBaseImgFolder',
				'vars' => array(
					'idTarget' => 'shareSubscriptionRightsWrap', 'flagDebit' => 0,
				),
				'child' => array(
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '新株予約権',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'shareSubscriptionRights', 'idAccountTitleJgaapFS' => 'shareSubscriptionRights',
							'varsJgaapCS' => array(
								'varsDirect' => array(
									'idAccountTitleMinus' => 'financeElseIn', 'flagMethodMinus' => 'sumDebit',
									'idAccountTitlePlus' => 'laborOut', 'flagMethodPlus' => 'sumCredit',
								),
								'varsInDirect' => array(
									'idAccountTitleMinus' => 'financeElseIn', 'flagMethodMinus' => 'sumDebit',
									'idAccountTitlePlus' => 'cashElseFluctuation', 'flagMethodPlus' => 'sumCredit',
								),
							),
							'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
							'varsValue' => array(), 'flagSortUse' => 1,

						),
						'child' => array(),
					),
				),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '新株予約権合計',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'shareSubscriptionRightsWrapSum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
				),
				'child' => array(),
			),
		),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '純資産の部合計',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'netAssetsSum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '負債及び純資産の部合計',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'liabilitiesNetAssetsNet', 'flagDebit' => 0, 'flagCalc' => 'net', 'varsValue' => array(),
		),
		'child' => array(),
	),

);
