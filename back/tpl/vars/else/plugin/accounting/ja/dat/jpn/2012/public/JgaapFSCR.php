<?php

$vars = array(
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
		'strTitle' => '原材料費',
		'strClass' => 'codeLibBaseImgFolder',
		'vars' => array(
			'idTarget' => 'materialsCost', 'flagDebit' => 1,
		),
		'child' => array(
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '期首原材料棚卸高',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'materialsCostOpeningInventory',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '原材料仕入高',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'materialsCostPurchase',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '小計',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'materialsCostDebitNet', 'flagDebit' => 1, 'flagSortUse' => 0, 'flagCalc' => 'net', 'varsValue' => array(),
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '期末原材料棚卸高',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'materialsCostClosingInventory',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
		),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '差引原材料費',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'materialsCostSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '労務費',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'laborCost',
			'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
		'strTitle' => 'その他の製造経費',
		'strClass' => 'codeLibBaseImgFolder',
		'vars' => array(
			'idTarget' => 'manufactureCost', 'flagDebit' => 1,
		),
		'child' => array(
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '外注工賃',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'manufactureOutsourcingManufactueingExpenses',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '電力費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'manufacturePowerExpense',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '水道光熱費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'manufactureWaterPowerExpenses',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '修繕費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'manufactureRepair',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '減価償却費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'manufactureDepreciation',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'blankCR1',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'blankCR2',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'blankCR3',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'blankCR4',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'blankCR5',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'blankCR6',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'blankCR7',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => 'その他の製造経費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'manufactureCostElse',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '雑費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'manufactureMiscellaneousExpenses',
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
			'idTarget' => 'manufactureCostSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '総製造費',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'grossProductCostNet', 'flagDebit' => 1, 'flagCalc' => 'net', 'varsValue' => array(),
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '期首半製品・仕掛品棚卸高',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'workInProcessOpeningInventory',
			'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '小計',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'workInProcessOpeningInventoryWrapNet', 'flagDebit' => 1, 'flagCalc' => 'net', 'varsValue' => array(),
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '期末半製品・仕掛品棚卸高',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'workInProcessClosingInventory',
			'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '製品製造原価',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'currentWorkInProcessNet', 'flagDebit' => 1, 'flagCalc' => 'net', 'varsValue' => array(),
		),
		'child' => array(),
	),
);
