<?php

$vars = array(
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
		'strTitle' => '材料費',
		'strClass' => 'codeLibBaseImgFolder',
		'vars' => array(
			'idTarget' => 'materialsCost', 'flagDebit' => 1,
		),
		'child' => array(
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
				'strTitle' => '期首材料棚卸高',
				'strClass' => 'codeLibBaseImgFolder',
				'vars' => array(
					'idTarget' => 'materialsCostOpeningInventoryWrap', 'flagDebit' => 1,
				),
				'child' => array(
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '期首材料棚卸高',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'materialsCostOpeningInventory', 'idAccountTitleJgaapFS' => 'materialsCostOpeningInventory',
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
				'strTitle' => '期首材料棚卸高合計',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'materialsCostOpeningInventoryWrapSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
				'strTitle' => '当期材料仕入高',
				'strClass' => 'codeLibBaseImgFolder',
				'vars' => array(
					'idTarget' => 'materialsCostPurchaseWrap', 'flagDebit' => 1,
				),
				'child' => array(
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '当期材料仕入高',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'materialsCostPurchase', 'idAccountTitleJgaapFS' => 'materialsCostPurchase',
							'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
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
							'varsValue' => array(), 'flagSortUse' => 1,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '仕入値引高',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'materialsCostAllowance', 'idAccountTitleJgaapFS' => 'materialsCostAllowance',
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
						'strTitle' => '仕入戻し高',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'materialsCostBack', 'idAccountTitleJgaapFS' => 'materialsCostBack',
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
							'idTarget' => 'materialsCostRevate', 'idAccountTitleJgaapFS' => 'materialsCostRevate',
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
				'strTitle' => '当期材料仕入高合計',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'materialsCostPurchaseWrapSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
				'strTitle' => '期末材料棚卸高',
				'strClass' => 'codeLibBaseImgFolder',
				'vars' => array(
					'idTarget' => 'materialsCostClosingInventoryWrap', 'flagDebit' => 0,
				),
				'child' => array(
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '期末材料棚卸高',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'materialsCostClosingInventory', 'idAccountTitleJgaapFS' => 'materialsCostClosingInventory',
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
				'strTitle' => '期末材料棚卸高合計',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'materialsCostClosingInventoryWrapSum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
				),
				'child' => array(),
			),
		),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '材料費合計',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'materialsCostSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
		'strTitle' => '労務費',
		'strClass' => 'codeLibBaseImgFolder',
		'vars' => array(
			'idTarget' => 'laborCost', 'flagDebit' => 1,
		),
		'child' => array(
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '役員報酬',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'laborDirectorsCompensations', 'idAccountTitleJgaapFS' => 'laborDirectorsCompensations',
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
					'idTarget' => 'laborEmployeeSalariesAndAllowances', 'idAccountTitleJgaapFS' => 'laborEmployeeSalariesAndAllowances',
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
					'idTarget' => 'laborMiscellaneousSalaries', 'idAccountTitleJgaapFS' => 'laborMiscellaneousSalaries',
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
					'idTarget' => 'laborBonus', 'idAccountTitleJgaapFS' => 'laborBonus',
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
					'idTarget' => 'laborRetirementPayments', 'idAccountTitleJgaapFS' => 'laborRetirementPayments',
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
					'idTarget' => 'laborLegalWelfareExpenses', 'idAccountTitleJgaapFS' => 'laborLegalWelfareExpenses',
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
					'idTarget' => 'laborWelfareExpenses', 'idAccountTitleJgaapFS' => 'laborWelfareExpenses',
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
					'idTarget' => 'laborProvisionForLiabilityForRetirementBenefits', 'idAccountTitleJgaapFS' => 'laborProvisionForLiabilityForRetirementBenefits',
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
		),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '労務費合計',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'laborCostSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
		'strTitle' => '製造経費',
		'strClass' => 'codeLibBaseImgFolder',
		'vars' => array(
			'idTarget' => 'manufactureCost', 'flagDebit' => 1,
		),
		'child' => array(
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '外注加工費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'manufactureOutsourcingManufactueingExpenses', 'idAccountTitleJgaapFS' => 'manufactureOutsourcingManufactueingExpenses',
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
				'strTitle' => '動力費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'manufacturePowerExpense', 'idAccountTitleJgaapFS' => 'manufacturePowerExpense',
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
					'idTarget' => 'manufacturePackingAndFreight', 'idAccountTitleJgaapFS' => 'manufacturePackingAndFreight',
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
					'idTarget' => 'manufactureConferenceExpense', 'idAccountTitleJgaapFS' => 'manufactureConferenceExpense',
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
					'idTarget' => 'manufactureTransportationExpenses', 'idAccountTitleJgaapFS' => 'manufactureTransportationExpenses',
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
					'idTarget' => 'manufactureCorrespondenceExpenses', 'idAccountTitleJgaapFS' => 'manufactureCorrespondenceExpenses',
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
					'idTarget' => 'manufactureSuppliesExpenses', 'idAccountTitleJgaapFS' => 'manufactureSuppliesExpenses',
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
					'idTarget' => 'manufactureStationeryExpenses', 'idAccountTitleJgaapFS' => 'manufactureStationeryExpenses',
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
					'idTarget' => 'manufactureVehicleExpense', 'idAccountTitleJgaapFS' => 'manufactureVehicleExpense',
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
					'idTarget' => 'manufactureRepair', 'idAccountTitleJgaapFS' => 'manufactureRepair',
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
					'idTarget' => 'manufactureWaterPowerExpenses', 'idAccountTitleJgaapFS' => 'manufactureWaterPowerExpenses',
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
					'idTarget' => 'manufactureBooksExpense', 'idAccountTitleJgaapFS' => 'manufactureBooksExpense',
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
					'idTarget' => 'manufactureDues', 'idAccountTitleJgaapFS' => 'manufactureDues',
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
				'strTitle' => '減価償却費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'manufactureDepreciation', 'idAccountTitleJgaapFS' => 'manufactureDepreciation',
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
				'strTitle' => '地代家賃',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'manufactureRents', 'idAccountTitleJgaapFS' => 'manufactureRents',
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
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
				'strTitle' => '賃借料',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'manufactureRentExpense', 'idAccountTitleJgaapFS' => 'manufactureRentExpense',
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
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
				'strTitle' => '租税公課',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'manufactureTaxesAndDues', 'idAccountTitleJgaapFS' => 'manufactureTaxesAndDues',
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
				'strTitle' => '支払保険料',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'manufactureInsuranceExpenses', 'idAccountTitleJgaapFS' => 'manufactureInsuranceExpenses',
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
				'strTitle' => '支払報酬料',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'manufactureCompensations', 'idAccountTitleJgaapFS' => 'manufactureCompensations',
					'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => 'taxDebit', 'flagConsumptionTaxGeneralRuleProration' => 'taxDebit', 'flagConsumptionTaxSimpleRule' => 'taxDebit',
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
				'strTitle' => '寄付金',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'manufactureContribution', 'idAccountTitleJgaapFS' => 'manufactureContribution',
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
					'idTarget' => 'manufactureAmortizationOfResearchAndDevelopmentExpenses', 'idAccountTitleJgaapFS' => 'manufactureAmortizationOfResearchAndDevelopmentExpenses',
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
				'strTitle' => '保管料',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'manufactureStorageFee', 'idAccountTitleJgaapFS' => 'manufactureStorageFee',
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
				'strTitle' => '棚卸減耗損',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'manufactureStockLosses', 'idAccountTitleJgaapFS' => 'manufactureStockLosses',
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
				'strTitle' => '雑費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'manufactureMiscellaneousExpenses', 'idAccountTitleJgaapFS' => 'manufactureMiscellaneousExpenses',
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
		'strTitle' => '製造経費合計',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'manufactureCostSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '総製造費用',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'grossProductCostNet', 'flagDebit' => 1, 'flagCalc' => 'net', 'varsValue' => array(),
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
		'strTitle' => '期首棚卸高',
		'strClass' => 'codeLibBaseImgFolder',
		'vars' => array(
			'idTarget' => 'workInProcessOpeningInventoryWrap', 'flagDebit' => 1,
		),
		'child' => array(
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '期首仕掛品棚卸高',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'workInProcessOpeningInventory', 'idAccountTitleJgaapFS' => 'workInProcessOpeningInventory',
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
					'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '期首半製品棚卸高',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'semiProductsOpeningInventory', 'idAccountTitleJgaapFS' => 'semiProductsOpeningInventory',
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
					'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
		),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '期首棚卸高合計',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'workInProcessOpeningInventoryWrapSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
		'strTitle' => '期末棚卸高',
		'strClass' => 'codeLibBaseImgFolder',
		'vars' => array(
			'idTarget' => 'workInProcessClosingInventoryWrap', 'flagDebit' => 0,
		),
		'child' => array(
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '期末仕掛品棚卸高',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'workInProcessClosingInventory', 'idAccountTitleJgaapFS' => 'workInProcessClosingInventory',
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
					'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '期末半製品棚卸高',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'semiProductsClosingInventory', 'idAccountTitleJgaapFS' => 'semiProductsClosingInventory',
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
					'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
		),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '期末棚卸高合計',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'workInProcessClosingInventoryWrapSum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 1, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
		'strTitle' => '他勘定振替高',
		'strClass' => 'codeLibBaseImgFolder',
		'vars' => array(
			'idTarget' => 'workInProcessRemoveWrap', 'flagDebit' => 0,
		),
		'child' => array(
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '他勘定振替高',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'workInProcessRemove', 'idAccountTitleJgaapFS' => 'workInProcessRemove',
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
					'flagUse' => 1, 'flagDebit' => 0, 'flagConsumptionTaxGeneralRuleEach' => 'none', 'flagConsumptionTaxGeneralRuleProration' => 'none', 'flagConsumptionTaxSimpleRule' => 'none',
					'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
		),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '他勘定振替高合計',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'workInProcessRemoveWrapSum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '当期製品製造原価',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'currentWorkInProcessNet', 'flagDebit' => 1, 'flagCalc' => 'net', 'varsValue' => array(),
		),
		'child' => array(),
	),

);
