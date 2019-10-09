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
					'idTarget' => 'netSales',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '売上値引高',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'salesAllowance',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '売上戻り高',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'salesBack',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '売上割戻し高',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'salesRebate',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '役務収益',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'revenueFromServiceOperations',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
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
									'idTarget' => 'goodsOpeningInventory',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,

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
									'idTarget' => 'goodsPurcheses',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '仕入値引高',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'goodsAllowance',
									'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '仕入戻し高',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'goodsBack',
									'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
								),
								'child' => array(),
							),
							array(
								'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
								'strTitle' => '仕入割戻し高',
								'strClass' => 'codeLibBaseImgSheet',
								'vars' => array(
									'idTarget' => 'goodsRevate',
									'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
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
									'idTarget' => 'goodsRemove',
									'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
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
									'idTarget' => 'goodsClosingInventory',
									'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
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
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '商品売上原価',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'goodsSum',
					'flagUse' => 1, 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(), 'flagSortUse' => 0,
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
									'idTarget' => 'productsOpeningInventory',
									'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
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
						'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
						'strTitle' => '当期製品製造原価',
						'strClass' => 'codeLibBaseImgSheet',
						'vars' => array(
							'idTarget' => 'currentTermProductsCost', 'idAccountTitleJgaapFS' => 'currentTermProductsCost',
							'flagUse' => 1, 'flagDebit' => 1,
							'strKeyRome' => 'toukiseihinseizougenka', 'strKeyHira' => 'とうきせいひんせいぞうげんか', 'varsValue' => array(), 'flagSortUse' => 0,
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
									'idTarget' => 'productsRemove',
									'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
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
									'idTarget' => 'productsClosingInventory',
									'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
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
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '製品売上原価',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'productsSum',
					'flagUse' => 1, 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(), 'flagSortUse' => 0,
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
			'idTarget' => 'grossProfitNet', 'flagDebit' => 0, 'flagCalc' => 'net', 'varsValue' => array(),
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
		'strTitle' => '販売管及び一般管理費',
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
					'idTarget' => 'directorsCompensations',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '役員賞与',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'bonusToDirectors',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '給料手当',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'employeeSalariesAndAllowances', 'varsValue' => array(), 'flagSortUse' => 1,
					'flagUse' => 1, 'flagDebit' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '雑給',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'miscellaneousSalaries',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '賞与',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'bonus',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '退職金',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'retirementPayments',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '法定福利費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'legalWelfareExpenses',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '福利厚生費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'welfareExpenses',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '退職給付引当金繰入額',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'provisionForLiabilityForRetirementBenefits',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '採用教育費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'trainingExpenses',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '外注費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'outsourcingExpenses',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '荷造運賃',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'packingAndFreight',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '広告宣伝費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'advertisingExpenses',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '接待交際費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'entertainmentExpenses',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '会議費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'conferenceExpense',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '旅費交通費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'transportationExpenses',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '通信費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'correspondenceExpenses',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '販売手数料',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'sellingConcession',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '販売促進費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'salesPromotionExpenses',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '消耗品費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'suppliesExpenses',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '事務用品費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'stationeryExpenses',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '修繕費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'repair',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '水道光熱費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'waterPowerExpenses',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '新聞図書費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'booksExpense',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '諸会費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'dues',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '支払手数料',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'commissionPaid',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '車両費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'vehicleExpense',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '地代家賃',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'rents',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '賃借料',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'rentExpense',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => 'リース料',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'leaseChargesPaid',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '支払保険料',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'insuranceExpenses',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '支払報酬料',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'compensations',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '租税公課',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'taxesAndDues',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '寄付金',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'contribution',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '研究開発費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'amortizationOfResearchAndDevelopmentExpenses',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '減価償却費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'depreciation',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '長期前払費用償却',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'longTermPrepaidExpensesDep',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '繰延税金資産償却',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'shortTermDeferredTaxDepreciation',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '貸倒損失',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'badDebtsExpenses',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '貸倒繰入額',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'provisionOfAllowanceForDoubtfulAccounts',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '雑費',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'miscellaneousExpenses',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
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
					'idTarget' => 'interestAndDiscountReceived',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '受取配当金',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'dividendsReceived',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '仕入割引',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'purchaseDiscounts',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '有価証券売却益',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'gainInValuationInSecurities',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '雑収入',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'miscellaneousIncome',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
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
					'idTarget' => 'interestPaid',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '支払割引料',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'discountPaid',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '手形売却損',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'lossesNotesReceivable',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '売上割引',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'salesDiscount',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '有価証券売却損',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'lossesInValuationInSecurities',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '繰延税金資産償却',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'shortTermDeferredTaxDepreciationNon',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '貸倒損失',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'badDebtsExpensesNon',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '貸倒繰入額',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'provisionOfAllowanceForDoubtfulAccountsNon',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '雑損失',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'badMiscellaneousExpenses',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
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
					'idTarget' => 'gainFromThePriorTermAdjustment',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '固定資産売却益',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'gainOnSaleOfFixedAssets',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '投資有価証券売却益',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'gainInValuationOfInvestmentInSecurities',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '貸倒引当金戻入益',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'reversalOfAllowanceForDoubtfulReceivables',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 1,
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
					'idTarget' => 'lossFromThePriorTermAdjustments',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '固定資産売却損',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'lossesOnSaleOfFixedAssets',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '固定資産除却損',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'lossOnDisposalOfFixedAssets',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '投資有価証券売却損',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'lossesInValuationOfInvestmentInSecurities',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 1,
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
		'strTitle' => '法人税・住民税及び事業税',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'corporateInhabitantAndEnterpriseTax',
			'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '法人税等調整額',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'corporateTaxAdjustments',
			'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
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
