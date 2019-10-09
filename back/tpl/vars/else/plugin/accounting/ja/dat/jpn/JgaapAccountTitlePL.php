<?php
$vars = array(
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
		'strTitle' => '売上高',
		'strClass' => 'codeLibBaseImgFolder',
		'vars' => array(
			'idTarget' => 'sales', 'flagDebit' => 0,
		),
		'child' => array(
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '売上高',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'netSales', 'idAccountTitleJgaapFS' => 'netSales',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleIn', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleIn', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 0,
					'flagConsumptionTaxGeneralRuleEach' => 'tax', 'flagConsumptionTaxGeneralRuleProration' => 'tax', 'flagConsumptionTaxSimpleRule' => 'tax-Default',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '売上値引高',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'salesAllowance', 'idAccountTitleJgaapFS' => 'salesAllowance',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleIn', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleIn', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1,
					'flagConsumptionTaxGeneralRuleEach' => 'tax-Back', 'flagConsumptionTaxGeneralRuleProration' => 'tax-Back', 'flagConsumptionTaxSimpleRule' => 'tax-Back-Default',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '売上戻り高',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'salesBack', 'idAccountTitleJgaapFS' => 'salesBack',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleIn', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleIn', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1,
					'flagConsumptionTaxGeneralRuleEach' => 'tax-Back', 'flagConsumptionTaxGeneralRuleProration' => 'tax-Back', 'flagConsumptionTaxSimpleRule' => 'tax-Back-Default',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '売上割戻し高',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'salesRebate', 'idAccountTitleJgaapFS' => 'salesRebate',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleIn', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleIn', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1,
					'flagConsumptionTaxGeneralRuleEach' => 'tax-Back', 'flagConsumptionTaxGeneralRuleProration' => 'tax-Back', 'flagConsumptionTaxSimpleRule' => 'tax-Back-Default',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '役務収益',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'revenueFromServiceOperations', 'idAccountTitleJgaapFS' => 'revenueFromServiceOperations',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleIn', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleIn', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 0,
					'flagConsumptionTaxGeneralRuleEach' => 'tax', 'flagConsumptionTaxGeneralRuleProration' => 'tax', 'flagConsumptionTaxSimpleRule' => 'tax-Default',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
		),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '売上高合計',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'salesSum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
		'strTitle' => '売上原価',
		'strClass' => 'codeLibBaseImgFolder',
		'vars' => array(
			'idTarget' => 'costOfSales', 'flagDebit' => 1,
		),
		'child' => array(
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
				'strTitle' => '商品',
				'strClass' => 'codeLibBaseImgFolder',
				'vars' => array(
					'idTarget' => 'goods', 'flagDebit' => 1,
				),
				'child' => array(
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
						'strTitle' => '期首商品棚卸高',
						'strClass' => 'codeLibBaseImgFolder',
						'vars' => array(
							'idTarget' => 'goodsOpeningInventoryWrap', 'flagDebit' => 1,
						),
						'child' => array(
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '期首商品棚卸高',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'goodsOpeningInventory', 'idAccountTitleJgaapFS' => 'goodsOpeningInventory',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
											'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1,
									'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
						),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '期首商品棚卸高合計',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'goodsOpeningInventoryWrapSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
						'strTitle' => '当期商品仕入高',
						'strClass' => 'codeLibBaseImgFolder',
						'vars' => array(
							'idTarget' => 'goodsPurchesesWrap', 'flagDebit' => 1,
						),
						'child' => array(
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '当期商品仕入高',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'goodsPurcheses', 'idAccountTitleJgaapFS' => 'goodsPurcheses',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleOut', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
											'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
										),
									),
									'flagUse' => 1, 'flagDebit' => 1,
									'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '仕入値引高',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'goodsAllowance', 'idAccountTitleJgaapFS' => 'goodsAllowance',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleOut', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
											'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
										),
									),
									'flagUse' => 1, 'flagDebit' => 0,
									'flagConsumptionTaxGeneralRuleEach' => 'taxDebit-Getback', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit-Getback', 'flagConsumptionTaxSimpleRule' => 'taxDebit-Getback',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '仕入戻し高',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'goodsBack', 'idAccountTitleJgaapFS' => 'goodsBack',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleOut', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
											'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
										),
									),
									'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit-Getback', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit-Getback', 'flagConsumptionTaxSimpleRule' => 'taxDebit-Getback',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '仕入割戻し高',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'goodsRevate', 'idAccountTitleJgaapFS' => 'goodsRevate',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleOut', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
											'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
										),
									),
									'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit-Getback', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit-Getback', 'flagConsumptionTaxSimpleRule' => 'taxDebit-Getback',
									'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
						),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '当期商品仕入高合計',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'goodsPurchesesWrapSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '合計',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'goodsDebitNet', 'flagDebit' => 1, 'flagCalc' => 'net', 'varsValue' => array(),
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
						'strTitle' => '他勘定振替高(商品)',
						'strClass' => 'codeLibBaseImgFolder',
						'vars' => array(
							'idTarget' => 'goodsRemoveWrap', 'flagDebit' => 0,
						),
						'child' => array(
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '他勘定振替高(商品)',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'goodsRemove', 'idAccountTitleJgaapFS' => 'goodsRemove',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleOut', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
											'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
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
						'strTitle' => '他勘定振替高(商品)合計',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'goodsRemoveWrapSum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
						'strTitle' => '期末商品棚卸高',
						'strClass' => 'codeLibBaseImgFolder',
						'vars' => array(
							'idTarget' => 'goodsClosingInventoryWrap', 'flagDebit' => 0,
						),
						'child' => array(
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '期末商品棚卸高',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'goodsClosingInventory', 'idAccountTitleJgaapFS' => 'goodsClosingInventory',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
											'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
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
						'strTitle' => '期末商品棚卸高合計',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'goodsClosingInventoryWrapSum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
						),
						'child' => array(),
					),
				),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '商品売上原価',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'goodsSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
				'strTitle' => '製品',
				'strClass' => 'codeLibBaseImgFolder',
				'vars' => array(
					'idTarget' => 'products', 'flagDebit' => 1,
				),
				'child' => array(
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
						'strTitle' => '期首製品棚卸高',
						'strClass' => 'codeLibBaseImgFolder',
						'vars' => array(
							'idTarget' => 'productsOpeningInventoryWrap', 'flagDebit' => 1,
						),
						'child' => array(
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '期首製品棚卸高',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'productsOpeningInventory', 'idAccountTitleJgaapFS' => 'productsOpeningInventory',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
											'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
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
						'strTitle' => '期首製品棚卸高合計',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'productsOpeningInventoryWrapSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '当期製品製造原価',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'currentTermProductsCost', 'flagDebit' => 1, 'varsValue' => array(),
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '合計',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'productsDebitNet', 'flagDebit' => 1, 'flagCalc' => 'net', 'varsValue' => array(),
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
						'strTitle' => '他勘定振替高(製品)',
						'strClass' => 'codeLibBaseImgFolder',
						'vars' => array(
							'idTarget' => 'productsRemoveWrap', 'flagDebit' => 0,
						),
						'child' => array(
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '他勘定振替高(製品)',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'productsRemove', 'idAccountTitleJgaapFS' => 'productsRemove',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'saleOut', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'saleOut', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
											'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
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
						'strTitle' => '他勘定振替高(製品)合計',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'productsRemoveWrapSum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
						'strTitle' => '期末製品棚卸高',
						'strClass' => 'codeLibBaseImgFolder',
						'vars' => array(
							'idTarget' => 'productsClosingInventoryWrap', 'flagDebit' => 0,
						),
						'child' => array(
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '期末製品棚卸高',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'productsClosingInventory', 'idAccountTitleJgaapFS' => 'productsClosingInventory',
									'varsJgaapCS' => array(
										'varsDirect' => array(
											'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'sumDebit',
											'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'sumCredit',
										),
										'varsInDirect' => array(
											'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
											'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
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
						'strTitle' => '期末製品棚卸高合計',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'productsClosingInventoryWrapSum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
						),
						'child' => array(),
					),
				),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '製品売上原価',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'productsSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
				),
				'child' => array(),
			),
		),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '売上原価合計',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'costOfSalesSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '売上総利益',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'grossProfitOrLossNet', 'flagDebit' => 0, 'flagCalc' => 'net', 'varsValue' => array(),
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
		'strTitle' => '販管費',
		'strClass' => 'codeLibBaseImgFolder',
		'vars' => array(
			'idTarget' => 'sellingGeneralAndAdministrationExpenses', 'flagDebit' => 1,
		),
		'child' => array(
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '役員報酬',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'directorsCompensations', 'idAccountTitleJgaapFS' => 'directorsCompensations',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'laborOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'laborOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '役員賞与',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'bonusToDirectors', 'idAccountTitleJgaapFS' => 'bonusToDirectors',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'laborOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'laborOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '給料手当',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'employeeSalariesAndAllowances', 'idAccountTitleJgaapFS' => 'employeeSalariesAndAllowances',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'laborOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'laborOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '雑給',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'miscellaneousSalaries', 'idAccountTitleJgaapFS' => 'miscellaneousSalaries',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'laborOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'laborOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '賞与',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'bonus', 'idAccountTitleJgaapFS' => 'bonus',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'laborOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'laborOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '退職金',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'retirementPayments', 'idAccountTitleJgaapFS' => 'retirementPayments',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'laborOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'laborOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '法定福利費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'legalWelfareExpenses', 'idAccountTitleJgaapFS' => 'legalWelfareExpenses',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'laborOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'laborOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '福利厚生費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'welfareExpenses', 'idAccountTitleJgaapFS' => 'welfareExpenses',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'laborOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'laborOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '退職給付引当金繰入額',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'provisionForLiabilityForRetirementBenefits', 'idAccountTitleJgaapFS' => 'provisionForLiabilityForRetirementBenefits',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'laborOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'laborOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '採用教育費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'trainingExpenses', 'idAccountTitleJgaapFS' => 'trainingExpenses',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '外注費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'outsourcingExpenses', 'idAccountTitleJgaapFS' => 'outsourcingExpenses',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '荷造運賃',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'packingAndFreight', 'idAccountTitleJgaapFS' => 'packingAndFreight',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '広告宣伝費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'advertisingExpenses', 'idAccountTitleJgaapFS' => 'advertisingExpenses',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '交際費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'entertainmentExpenses', 'idAccountTitleJgaapFS' => 'entertainmentExpenses',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '会議費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'conferenceExpense', 'idAccountTitleJgaapFS' => 'conferenceExpense',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '旅費交通費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'transportationExpenses', 'idAccountTitleJgaapFS' => 'transportationExpenses',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '通信費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'correspondenceExpenses', 'idAccountTitleJgaapFS' => 'correspondenceExpenses',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '販売手数料',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'sellingConcession', 'idAccountTitleJgaapFS' => 'sellingConcession',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '販売促進費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'salesPromotionExpenses', 'idAccountTitleJgaapFS' => 'salesPromotionExpenses',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '消耗品費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'suppliesExpenses', 'idAccountTitleJgaapFS' => 'suppliesExpenses',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '事務用品費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'stationeryExpenses', 'idAccountTitleJgaapFS' => 'stationeryExpenses',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '修繕費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'repair', 'idAccountTitleJgaapFS' => 'repair',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '水道光熱費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'waterPowerExpenses', 'idAccountTitleJgaapFS' => 'waterPowerExpenses',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '新聞図書費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'booksExpense', 'idAccountTitleJgaapFS' => 'booksExpense',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '諸会費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'dues', 'idAccountTitleJgaapFS' => 'dues',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '支払手数料',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'commissionPaid', 'idAccountTitleJgaapFS' => 'commissionPaid',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '車両費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'vehicleExpense', 'idAccountTitleJgaapFS' => 'vehicleExpense',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '地代家賃',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'rents', 'idAccountTitleJgaapFS' => 'rents',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '賃借料',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'rentExpense', 'idAccountTitleJgaapFS' => 'rentExpense',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => 'リース料',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'leaseChargesPaid', 'idAccountTitleJgaapFS' => 'leaseChargesPaid',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '支払保険料',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'insuranceExpenses', 'idAccountTitleJgaapFS' => 'insuranceExpenses',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '支払報酬料',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'compensations', 'idAccountTitleJgaapFS' => 'compensations',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '租税公課',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'taxesAndDues', 'idAccountTitleJgaapFS' => 'taxesAndDues',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '寄付金',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'contribution', 'idAccountTitleJgaapFS' => 'contribution',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '研究開発費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'amortizationOfResearchAndDevelopmentExpenses', 'idAccountTitleJgaapFS' => 'amortizationOfResearchAndDevelopmentExpenses',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '減価償却費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'depreciation', 'idAccountTitleJgaapFS' => 'depreciation',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'tangibleFixedAssetsIn', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'tangibleFixedAssetsIn', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'tangibleFixedAssetsIn', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'depreciation', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '長期前払費用償却',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'longTermPrepaidExpensesDep', 'idAccountTitleJgaapFS' => 'longTermPrepaidExpensesDep',
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '繰延資産償却(販管)',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'shortTermDeferredTaxDepreciation', 'idAccountTitleJgaapFS' => 'shortTermDeferredTaxDepreciation',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'investElseIn', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'investElseIn', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'investElseIn', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'depreciation', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '貸倒損失(販管)',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'badDebtsExpenses', 'idAccountTitleJgaapFS' => 'badDebtsExpenses',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'tax-Bad', 'flagConsumptionTaxGeneralRuleProration' => 'tax-Bad',
'flagConsumptionTaxSimpleRule' => 'tax-Bad',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '貸倒繰入額(販管)',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'provisionOfAllowanceForDoubtfulAccounts', 'idAccountTitleJgaapFS' => 'provisionOfAllowanceForDoubtfulAccounts',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
					'varsValue' => array(), 'flagSortUse' => 1,

				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '雑費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'miscellaneousExpenses', 'idAccountTitleJgaapFS' => 'miscellaneousExpenses',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
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
		'strTitle' => '販管費合計',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'sellingGeneralAndAdministrationExpensesSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '営業利益',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'operatingIncomeProfitOrLossNet', 'flagDebit' => 0, 'flagCalc' => 'net', 'varsValue' => array(),
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
		'strTitle' => '営業外収益',
		'strClass' => 'codeLibBaseImgFolder',
		'vars' => array(
			'idTarget' => 'nonOperatingIncome', 'flagDebit' => 0,
		),
		'child' => array(
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '受取利息',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'interestAndDiscountReceived', 'idAccountTitleJgaapFS' => 'interestAndDiscountReceived',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'interestAndDiscountReceivedIn', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'interestAndDiscountReceivedIn', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'interestAndDiscountReceived', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'interestAndDiscountReceivedIn', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'free', 'flagConsumptionTaxGeneralRuleProration' => 'free', 'flagConsumptionTaxSimpleRule' => 'free',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '受取配当金',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'dividendsReceived', 'idAccountTitleJgaapFS' => 'dividendsReceived',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'interestAndDiscountReceivedIn', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'interestAndDiscountReceivedIn', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'interestAndDiscountReceived', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'interestAndDiscountReceivedIn', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '仕入割引',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'purchaseDiscounts', 'idAccountTitleJgaapFS' => 'purchaseDiscounts',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseIn', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseIn', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit-Getback', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit-Getback', 'flagConsumptionTaxSimpleRule' => 'taxDebit-Getback',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '有価証券売却益',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'gainInValuationInSecurities', 'idAccountTitleJgaapFS' => 'gainInValuationInSecurities',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'securitiesIn', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'securitiesIn', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'gainInValuationInSecurities', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'securitiesIn', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'free-Securities', 'flagConsumptionTaxGeneralRuleProration' => 'free-Securities', 'flagConsumptionTaxSimpleRule' => 'free-Securities',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '雑収入',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'miscellaneousIncome', 'idAccountTitleJgaapFS' => 'miscellaneousIncome',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseIn', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseIn', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
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
		'strTitle' => '営業外収益合計',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'nonOperatingIncomeSum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
		'strTitle' => '営業外費用',
		'strClass' => 'codeLibBaseImgFolder',
		'vars' => array(
			'idTarget' => 'nonOperatingExpenses', 'flagDebit' => 1,
		),
		'child' => array(
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '支払利息',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'interestPaid', 'idAccountTitleJgaapFS' => 'interestPaid',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'interestPaidOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'interestPaidOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'interestPaidOut', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'interestPaid', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '支払割引料',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'discountPaid', 'idAccountTitleJgaapFS' => 'discountPaid',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'interestPaidOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'interestPaidOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'interestPaidOut', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'interestPaid', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '手形売却損',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'lossesNotesReceivable', 'idAccountTitleJgaapFS' => 'lossesNotesReceivable',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'accoutsReceivableTradeFluctuation', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'lossesNotesReceivable', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '売上割引',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'salesDiscount', 'idAccountTitleJgaapFS' => 'salesDiscount',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'tax-Back', 'flagConsumptionTaxGeneralRuleProration' => 'tax-Back', 'flagConsumptionTaxSimpleRule' => 'tax-Back-Default',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '有価証券売却損',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'lossesInValuationInSecurities', 'idAccountTitleJgaapFS' => 'lossesInValuationInSecurities',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'securitiesIn', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'securitiesIn', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'securitiesIn', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'lossesInValuationInSecurities', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '繰延資産償却(営業外)',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'shortTermDeferredTaxDepreciationNon', 'idAccountTitleJgaapFS' => 'shortTermDeferredTaxDepreciationNon',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'investElseIn', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'investElseIn', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'investElseIn', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'depreciation', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '貸倒損失(営業外)',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'badDebtsExpensesNon', 'idAccountTitleJgaapFS' => 'badDebtsExpensesNon',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'tax-Bad', 'flagConsumptionTaxGeneralRuleProration' => 'tax-Bad', 'flagConsumptionTaxSimpleRule' => 'tax-Bad',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '貸倒繰入額(営業外)',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'provisionOfAllowanceForDoubtfulAccountsNon', 'idAccountTitleJgaapFS' => 'provisionOfAllowanceForDoubtfulAccountsNon',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '雑損失',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'badMiscellaneousExpenses', 'idAccountTitleJgaapFS' => 'badMiscellaneousExpenses',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElseOut', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElseOut', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
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
		'strTitle' => '営業外費用合計',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'nonOperatingExpensesSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '経常利益',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'ordinaryProfitNet', 'flagDebit' => 0, 'flagCalc' => 'net', 'varsValue' => array(),
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
		'strTitle' => '特別利益',
		'strClass' => 'codeLibBaseImgFolder',
		'vars' => array(
			'idTarget' => 'extraordinaryIncome', 'flagDebit' => 0,
		),
		'child' => array(
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '前期損益修正益',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'gainFromThePriorTermAdjustment', 'idAccountTitleJgaapFS' => 'gainFromThePriorTermAdjustment',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElse', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElse', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'gainFromThePriorTermAdjustment', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'saleElse', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '固定資産売却益',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'gainOnSaleOfFixedAssets', 'idAccountTitleJgaapFS' => 'gainOnSaleOfFixedAssets',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'tangibleFixedAssetsIn', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'tangibleFixedAssetsIn', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'gainOnSaleOfFixedAssets', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'tangibleFixedAssetsIn', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'tax', 'flagConsumptionTaxGeneralRuleProration' => 'tax', 'flagConsumptionTaxSimpleRule' => 'tax-Default',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '投資有価証券売却益',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'gainInValuationOfInvestmentInSecurities', 'idAccountTitleJgaapFS' => 'gainInValuationOfInvestmentInSecurities',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'investmentsInSecuritiesIn', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'investmentsInSecuritiesIn', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'gainOnSaleOfFixedAssets', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'investmentsInSecuritiesIn', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'free-Securities', 'flagConsumptionTaxGeneralRuleProration' => 'free-Securities', 'flagConsumptionTaxSimpleRule' => 'tax-Default',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '貸倒引当金戻入益',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'reversalOfAllowanceForDoubtfulReceivables', 'idAccountTitleJgaapFS' => 'reversalOfAllowanceForDoubtfulReceivables',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'none', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'none', 'flagMethodPlus' => 'net',
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
		'strTitle' => '特別利益合計',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'extraordinaryIncomeSum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
		'strTitle' => '特別損失',
		'strClass' => 'codeLibBaseImgFolder',
		'vars' => array(
			'idTarget' => 'extraordinaryLosses', 'flagDebit' => 1,
		),
		'child' => array(
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '前期損益修正損',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'lossFromThePriorTermAdjustments', 'idAccountTitleJgaapFS' => 'lossFromThePriorTermAdjustments',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'saleElse', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'saleElse', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'saleElse', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'lossFromThePriorTermAdjustments', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '固定資産売却損',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'lossesOnSaleOfFixedAssets', 'idAccountTitleJgaapFS' => 'lossesOnSaleOfFixedAssets',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'tangibleFixedAssetsIn', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'tangibleFixedAssetsIn', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'tangibleFixedAssetsIn', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'lossesOnSaleOfFixedAssets', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '固定資産除却損',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'lossOnDisposalOfFixedAssets', 'idAccountTitleJgaapFS' => 'lossOnDisposalOfFixedAssets',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'tangibleFixedAssetsIn', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'tangibleFixedAssetsIn', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'tangibleFixedAssetsIn', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'lossOnDisposalOfFixedAssets', 'flagMethodPlus' => 'net',
						),
					),
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
					'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '投資有価証券売却損',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'lossesInValuationOfInvestmentInSecurities', 'idAccountTitleJgaapFS' => 'lossesInValuationOfInvestmentInSecurities',
					'varsJgaapCS' => array(
						'varsDirect' => array(
							'idAccountTitleMinus' => 'investmentsInSecuritiesIn', 'flagMethodMinus' => 'sumDebit',
							'idAccountTitlePlus' => 'investmentsInSecuritiesIn', 'flagMethodPlus' => 'sumCredit',
						),
						'varsInDirect' => array(
							'idAccountTitleMinus' => 'investmentsInSecuritiesIn', 'flagMethodMinus' => 'net',
							'idAccountTitlePlus' => 'lossesInValuationOfInvestmentInSecurities', 'flagMethodPlus' => 'net',
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
		'strTitle' => '特別損失合計',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'extraordinaryLossesSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '税引前当期純利益',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'currentTermProfitOrLossPreNet', 'flagDebit' => 0, 'flagCalc' => 'net', 'varsValue' => array(),
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '法人税等',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'corporateInhabitantAndEnterpriseTax', 'idAccountTitleJgaapFS' => 'corporateInhabitantAndEnterpriseTax',
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
			'varsValue' => array(), 'flagSortUse' => 0,
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '法人税等調整額',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'corporateTaxAdjustments', 'idAccountTitleJgaapFS' => 'corporateTaxAdjustments',
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
			'varsValue' => array(), 'flagSortUse' => 0,
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '当期純利益',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'currentTermProfitOrLossNet', 'flagDebit' => 0, 'flagCalc' => 'net', 'varsValue' => array(),
		),
		'child' => array(),
	),
);
