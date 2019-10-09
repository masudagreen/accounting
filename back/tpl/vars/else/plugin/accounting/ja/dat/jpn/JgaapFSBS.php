<?php

$vars = array(
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
		'strTitle' => '資産の部',
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
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '現金及び預金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'cash',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
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
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '受取手形',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'notesReceivable',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '不渡手形',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'dishonoredBill',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '売掛金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'accountsReceivable',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '貸倒引当金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'allowanceForBadDebtsTrade',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
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
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '有価証券',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'securities',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
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
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '商品',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'merchandise',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '製品',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'finishedGoods',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '半製品',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'semiFinishedGoods',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '原材料',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'materials',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '仕掛品',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'workInProcess',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '貯蔵品',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'stockGoods',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
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
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '前渡金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'advancesAccount',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '立替金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'advances',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '前払費用',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'prepaidExpenses',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '繰延税金資産',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'shortTermDeferredTaxAssets',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '未収収益',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'accruedIncome',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '短期貸付金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'shortTermLoansReceivable',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '未収入金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'accruedRevenue',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '仮払金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'suspensePaymentAccount',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '預け金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'keyMoney',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '仮払消費税等',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'suspensePaymentConsumptionTaxes',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '未収消費税等',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'accruedConsumptionTaxes',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '貸倒引当金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'allowanceForBadDebtsShortTermOther',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
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
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '建物',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'buildings',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '建物附属設備',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'buildingsAndAccessoyEquipment',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '構築物',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'structures',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '機械装置',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'machineryAndEquipment',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '車両運搬具',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'car',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '工具器具備品',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'furnitureAndFixture',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '一括償却資産',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'lumpSumDepreciableAsset',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '減価償却累計額',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'accumulatedDepreciation',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '土地',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'land',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '建設仮勘定',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'constructionInProgress',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
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
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '電話加入権',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'telephoneSubscriptionRight',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '営業権',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'goodwill',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '借地権',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'leaseholdRight',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => 'ソフトウェア',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'software',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
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
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '投資有価証券',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'investmentsInSecurities',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '関係会社株式',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'stocksOfAffiliatedCompanies',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '出資金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'investments',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '関係会社出資金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'investmentsOfAffiliatedCompanie',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '敷金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'leaseDepositsAndSecurityDeposits',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '差入保証金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'guaranteeDeposits',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '長期貸付金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'longTermLoansReceivable',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '長期性預金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'longTermDeposits',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '長期滞留債権',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'stayCredit',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '長期前払費用',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'longTermPrepaidExpenses',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '繰延税金資産',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'longTermDefferedTaxAssets',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '預託金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'deposit',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '貸倒引当金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'allowanceForBadDebtsLongTermOther',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
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
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '創立費',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'inauguralExpenses',
							'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '開業費',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'developmentExpenses',
							'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '開発費',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'startUpCosts',
							'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '試験研究費',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'experimentationAndResearchExpenses',
							'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
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
		'strTitle' => '負債の部',
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
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '支払手形',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'notesPayable',
									'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '買掛金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'accountsAmountPayable',
									'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
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
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '営業外支払手形',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'nonOperatingNotesPayable',
									'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '短期借入金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'shortTermLoansPayable',
									'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '未払金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'shortTermAccruedAmountPayable',
									'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '未払費用',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'accruedExpenses',
									'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '未払配当金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'dividendsPayable',
									'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '未払役員賞与',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'accruedBonusToDirectors',
									'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '未払法人税等',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'corporationTaxesPayable',
									'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '未払消費税等',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'consumptionTaxesRepayable',
									'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '繰延税金負債',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'shortTermDeferredTaxLiability',
									'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '前受金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'advancesByCustomers',
									'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '預り金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'depositePayable',
									'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '前受収益',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'deferredIncome',
									'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '仮受金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'suspenseReceipt',
									'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '預り保証金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'shortTermGuaranteeDeposited',
									'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '割引手形',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'discountedBill',
									'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '裏書手形',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'endorsementBill',
									'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '仮受消費税等',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'suspenseReceiptOfConsumptionTaxes',
									'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
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
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '長期借入金',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'longTermLoansPayable',
							'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '長期未払金',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'longTermAccruedAmountPayable',
							'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '繰延税金負債',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'longTermDeferredTaxLiability',
							'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '退職給付引当金',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'reserveForRetirementAllowance',
							'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
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
		'strTitle' => '純資産の部',
		'strClass' => 'codeLibBaseImgFolder',
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
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '資本金',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'commonStock',
							'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
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
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '新株式申込証拠金',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'advanceOnSubscriptionForNewStock',
									'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
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
										'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
										'strTitle' => '資本準備金',
										'strClass' => 'codeLibBaseImgSheet',
										'vars' => array(
											'idTarget' => 'capitalReserve',
											'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
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
									'idTarget' => 'otherCapitalSurplusWrap', 'flagDebit' => 0,
								),
								'child' => array(
									array(
										'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
										'strTitle' => 'その他資本剰余金',
										'strClass' => 'codeLibBaseImgSheet',
										'vars' => array(
											'idTarget' => 'otherCapitalSurplus',
											'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
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
									'idTarget' => 'otherCapitalSurplusWrapSum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
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
										'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
										'strTitle' => '利益準備金',
										'strClass' => 'codeLibBaseImgSheet',
										'vars' => array(
											'idTarget' => 'legalReserveOfRetainedEarnings',
											'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
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
												'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
												'strTitle' => '別途積立金',
												'strClass' => 'codeLibBaseImgSheet',
												'vars' => array(
													'idTarget' => 'otherReserves',
													'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
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
										'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
										'strTitle' => '繰越利益剰余金',
										'strClass' => 'codeLibBaseImgFolder',
										'vars' => array(
											'idTarget' => 'unappropriatedRetainedEarningsWrap', 'flagDebit' => 0,
										),
										'child' => array(
											array(
												'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
												'strTitle' => '繰越利益剰余金',
												'strClass' => 'codeLibBaseImgSheet',
												'vars' => array(
													'idTarget' => 'unappropriatedRetainedEarnings',
													'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
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
											'idTarget' => 'unappropriatedRetainedEarningsWrapSum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
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
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '自己株式',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'treasuryStock',
							'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '自己株式申込証拠金',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'advanceOnSubscriptionForReissuanceOfTreasuryStock',
							'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
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
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => 'その他有価証券評価差額金',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'unrealizedGainOnMarketableSecurities',
							'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '繰延ヘッジ損益',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'gainOnDeferredHedge',
							'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '土地再評価差額金',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'landRevaluationDifference',
							'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
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
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '新株予約権',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'shareSubscriptionRights',
							'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
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
