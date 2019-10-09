<?php
/*
	varsValue => array(
		'f1' => array(
			sumNext   => 0,
		),
	),
*/
$vars = array(
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
		'strTitle' => '売上(収入)金額(雑収入を含む)',
		'strClass' => 'codeLibBaseImgFolder',
		'vars' => array(
			'idTarget' => 'sales', 'flagDebit' => 0,
		),
		'child' => array(
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '売上(収入)金額',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'netSales',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '家事消費等',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'selfConsumption',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '雑収入',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'miscellaneousIncome',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
		),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '計',
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
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '期首商品(製品)棚卸高',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'goodsOpeningInventory',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,

				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '仕入金額(製品製造原価)',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'goodsPurcheses',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '小計',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'goodsDebitNet', 'flagDebit' => 1, 'flagCalc' => 'net', 'varsValue' => array(),
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '期末商品(製品)棚卸高',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'goodsClosingInventory',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
		),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '差引原価',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'goodsSum',
			'flagUse' => 1, 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(), 'flagSortUse' => 0,
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '差引金額',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'grossProfitNet', 'flagDebit' => 0, 'flagCalc' => 'net', 'varsValue' => array(),
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
		'strTitle' => '経費',
		'strClass' => 'codeLibBaseImgFolder',
		'vars' => array(
			'idTarget' => 'sellingGeneralAndAdministrationExpenses', 'flagDebit' => 1,
		),
		'child' => array(
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '租税公課',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'taxesAndDues',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '荷造運賃',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'packingAndFreight',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '水道光熱費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'waterPowerExpenses',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '旅費交通費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'transportationExpenses',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '通信費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'correspondenceExpenses',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '広告宣伝費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'advertisingExpenses',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '接待交際費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'entertainmentExpenses',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '損害保険料',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'insuranceExpenses',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '修繕費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'repair',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '消耗品費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'suppliesExpenses',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '減価償却費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'depreciation',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '福利厚生費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'welfareExpenses',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '給料賃金',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'employeeSalariesAndAllowances', 'varsValue' => array(), 'flagSortUse' => 0,
					'flagUse' => 1, 'flagDebit' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '外注工賃',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'outsourcingExpenses',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '利子割引料',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'commissionPaid',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '地代家賃',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'rents',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '貸倒金',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'provisionOfAllowanceForDoubtfulAccounts',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'blankPL1',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'blankPL2',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'blankPL3',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'blankPL4',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'blankPL5',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => 'その他の経費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'sellingGeneralAndAdministrationExpensesElse',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '雑費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'miscellaneousExpenses',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
		),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '計',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'sellingGeneralAndAdministrationExpensesSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '差引金額',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'operatingIncomeProfitOrLossNet', 'flagDebit' => 0, 'flagCalc' => 'net', 'varsValue' => array(),
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
		'strTitle' => '各種引当金・準備金等',
		'strClass' => 'codeLibBaseImgFolder',
		'vars' => array(
			'idTarget' => 'reserveFundElseWrap', 'flagDebit' => 1,
		),
		'child' => array(
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
				'strTitle' => '繰戻額等',
				'strClass' => 'codeLibBaseImgFolder',
				'vars' => array(
					'idTarget' => 'reserveFundInElse', 'flagDebit' => 0,
				),
				'child' => array(
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '貸倒引当金',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'reversalOfAllowanceForDoubtfulReceivables',
							'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'blankPLElse1',
							'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'blankPLElse2',
							'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
						),
						'child' => array(),
					),
				),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '計',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'reserveFundInElseSum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
				'strTitle' => '繰入額等',
				'strClass' => 'codeLibBaseImgFolder',
				'vars' => array(
					'idTarget' => 'reserveFundOutElse', 'flagDebit' => 1,
				),
				'child' => array(
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '専従者給与',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'familyEmployee',
							'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '貸倒引当金',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'provisionOfAllowanceForDoubtfulAccountsNon',
							'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'blankPLElse3',
							'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
						),
						'child' => array(),
					),
					array(
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'blankPLElse4',
							'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
						),
						'child' => array(),
					),
				),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '計',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'reserveFundOutElseSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
				),
				'child' => array(),
			),
		),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '青色申告特別控除前の所得金額',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'currentTermProfitOrLossNet', 'flagDebit' => 0, 'flagCalc' => 'net', 'varsValue' => array(),
		),
		'child' => array(),
	),
/*
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '青色申告特別控除額',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'blueTax', 'flagDebit' => 1, 'varsValue' => array(),
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '所得金額',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'blueNet', 'flagDebit' => 0, 'flagCalc' => 'net', 'varsValue' => array(),
		),
		'child' => array(),
	),
*/
);
