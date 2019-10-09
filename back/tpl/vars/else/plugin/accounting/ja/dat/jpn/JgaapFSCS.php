<?php

$vars = array(
	'varsDirect' => array(
		array(
			'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
			'strTitle' => '1. 営業活動によるキャッシュ・フロー',
			'strClass' => 'codeLibBaseImgFolder',
			'vars' => array(
				'idTarget' => 'sale',
			),
			'child' => array(
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '営業収入',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'saleIn', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '原材料又は商品の仕入による支出',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'saleOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '人件費による支出',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'laborOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => 'その他の営業収入',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'saleElseIn', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => 'その他の営業支出',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'saleElseOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '未払消費税等の増減額',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'consumptionTaxesRepayable', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => 'その他',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'saleElse', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '小計',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'saleNet', 'flagSortUse' => 1, 'flagCalc' => 'net', 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '利息及び配当金の受取額',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'interestAndDiscountReceivedIn', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '利息の支払額',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'interestPaidOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '損害賠償金の支払額',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'compensationForDamageOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '法人税等の支払額',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'corporateInhabitantAndEnterpriseTaxOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
			),
		),
		array(
			'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
			'strTitle' => '営業活動によるキャッシュ・フロー合計',
			'strClass' => 'codeLibBaseImgSheet',
			'vars' => array(
				'idTarget' => 'saleSum', 'flagCalc' => 'sum', 'varsValue' => array(),
			),
			'child' => array(),
		),
		array(
			'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
			'strTitle' => '2. 投資活動によるキャッシュ・フロー',
			'strClass' => 'codeLibBaseImgFolder',
			'vars' => array(
				'idTarget' => 'invest',
			),
			'child' => array(
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '定期預金等の預入による支出',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'fixedDepositOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '定期預金等の払戻による収入',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'fixedDepositIn', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '有価証券の取得による支出',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'securitiesOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '有価証券の売却による収入',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'securitiesIn', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '有形固定資産の取得による支出',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'tangibleFixedAssetsOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '有形固定資産の売却による収入',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'tangibleFixedAssetsIn', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '無形固定資産の取得による支出',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'intangibleFixedAssetsOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '無形固定資産の売却による収入',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'intangibleFixedAssetsIn', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '投資有価証券の取得による支出',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'investmentsInSecuritiesOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '投資有価証券の売却による収入',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'investmentsInSecuritiesIn', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '貸付けによる支出',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'loansReceivableOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '貸付金の回収による収入',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'loansReceivableIn', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => 'その他投資活動による支出',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'investElseOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => 'その他投資活動による収入',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'investElseIn', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
			),
		),
		array(
			'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
			'strTitle' => '投資活動によるキャッシュ・フロー合計',
			'strClass' => 'codeLibBaseImgSheet',
			'vars' => array(
				'idTarget' => 'investSum', 'flagCalc' => 'sum', 'varsValue' => array(),
			),
			'child' => array(),
		),
		array(
			'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
			'strTitle' => '3. 財務活動によるキャッシュ・フロー',
			'strClass' => 'codeLibBaseImgFolder',
			'vars' => array(
				'idTarget' => 'finance',
			),
			'child' => array(
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '短期借入れによる収入',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'shortTermLoansPayableIn', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '短期借入金の返済による支出',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'shortTermLoansPayableOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '長期借入れによる収入',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'longTermLoansPayableIn', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '長期借入金の返済による支出',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'longTermLoansPayableOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '社債発行による収入',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'bondIn', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '社債償還による支出',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'bondOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '株式発行による収入',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'stockIn', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '自己株式の取得による支出',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'stockSelfOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '配当金の支払額',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'dividendsOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => 'その他財務活動による支出',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'financeElseOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => 'その他財務活動による収入',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'financeElseIn', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
			),
		),
		array(
			'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
			'strTitle' => '財務活動によるキャッシュ・フロー合計',
			'strClass' => 'codeLibBaseImgSheet',
			'vars' => array(
				'idTarget' => 'financeSum', 'flagCalc' => 'sum', 'varsValue' => array(),
			),
			'child' => array(),
		),
		array(
			'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
			'strTitle' => '4. 現金及び現金同等物に係る為替差額',
			'strClass' => 'codeLibBaseImgSheet',
			'vars' => array(
				'idTarget' => 'cashRate', 'varsValue' => array(),
			),
			'child' => array(),
		),
		array(
			'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
			'strTitle' => '5. 現金及び現金同等物の増加額',
			'strClass' => 'codeLibBaseImgSheet',
			'vars' => array(
				'idTarget' => 'cashNet', 'flagCalc' => 'net', 'varsValue' => array(),
			),
			'child' => array(),
		),
		array(
			'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
			'strTitle' => '6. 現金及び現金同等物の期首残高',
			'strClass' => 'codeLibBaseImgSheet',
			'vars' => array(
				'idTarget' => 'cashOpening', 'varsValue' => array(),
			),
			'child' => array(),
		),
		array(
			'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
			'strTitle' => '7. 現金及び現金同等物の期末残高',
			'strClass' => 'codeLibBaseImgSheet',
			'vars' => array(
				'idTarget' => 'cashClosing', 'varsValue' => array(),
			),
			'child' => array(),
		),
	),
	'varsInDirect' => array(
		array(
			'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
			'strTitle' => '1. 営業活動によるキャッシュ・フロー',
			'strClass' => 'codeLibBaseImgFolder',
			'vars' => array(
				'idTarget' => '',
			),
			'child' => array(
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '税引前当期純利益(又は純損失)',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'currentTermProfitOrLossPre', 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '減価償却費',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'depreciation', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '貸倒引当金増加',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'allowanceForBadDebtsTrade', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '退職給付引当金増加',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'reserveForRetirementAllowance', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '受取利息及び受取配当金',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'interestAndDiscountReceived', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '支払利息',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'interestPaid', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '手形売却損',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'lossesNotesReceivable', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '為替差損',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'rateLoss', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '有価証券売却益',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'gainInValuationInSecurities', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '有価証券売却損',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'lossesInValuationInSecurities', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '投資有価証券売却益',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'gainInValuationOfInvestmentInSecurities', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '投資有価証券売却損',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'lossesInValuationOfInvestmentInSecurities', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '有形固定資産売却益',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'gainOnSaleOfFixedAssets', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '有形固定資産売却損',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'lossesOnSaleOfFixedAssets', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '有形固定資産除却損',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'lossOnDisposalOfFixedAssets', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '前期損益修正益',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'gainFromThePriorTermAdjustment', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '前期損益修正損',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'lossFromThePriorTermAdjustments', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '損害賠償損失',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'compensationForDamage', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => 'その他非資金損益項目の増減額',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'cashElseFluctuation', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '売上債権の増減額',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'accoutsReceivableTradeFluctuation', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '棚卸資産の増減額',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'inventriesFluctuation', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '仕入債務の増減額',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'accountsPayableTradeFluctuation', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '割引手形の増減額',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'discountedBillFluctuation', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '未払消費税等の増減額',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'consumptionTaxesRepayableFluctuation', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '役員賞与の支払額',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'bonusToDirectorsOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => 'その他負債の増減額',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'liabilitiesElseFluctuation', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => 'その他資産の増減額',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'assetsElseFluctuation', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => 'その他',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'saleElse', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '小計',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'saleNet', 'flagCalc' => 'net', 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '利息及び配当金の受取額',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'interestAndDiscountReceivedIn', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '利息の支払額',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'interestPaidOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '損害賠償金の支払額',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'compensationForDamageOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '法人税等の支払額',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'corporateInhabitantAndEnterpriseTaxOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
			),
		),
		array(
			'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
			'strTitle' => '営業活動によるキャッシュ・フロー合計',
			'strClass' => 'codeLibBaseImgSheet',
			'vars' => array(
				'idTarget' => 'saleSum', 'flagCalc' => 'sum', 'varsValue' => array(),
			),
			'child' => array(),
		),
		array(
			'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
			'strTitle' => '2. 投資活動によるキャッシュ・フロー',
			'strClass' => 'codeLibBaseImgFolder',
			'vars' => array(
				'idTarget' => 'invest',
			),
			'child' => array(
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '定期預金等の預入による支出',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'fixedDepositOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '定期預金等の払戻による収入',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'fixedDepositIn', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '有価証券の取得による支出',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'securitiesOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '有価証券の売却による収入',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'securitiesIn', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '有形固定資産の取得による支出',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'tangibleFixedAssetsOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '有形固定資産の売却による収入',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'tangibleFixedAssetsIn', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '無形固定資産の取得による支出',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'intangibleFixedAssetsOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '無形固定資産の売却による収入',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'intangibleFixedAssetsIn', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '投資有価証券の取得による支出',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'investmentsInSecuritiesOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '投資有価証券の売却による収入',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'investmentsInSecuritiesIn', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '貸付けによる支出',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'loansReceivableOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '貸付金の回収による収入',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'loansReceivableIn', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => 'その他投資活動による支出',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'investElseOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => 'その他投資活動による収入',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'investElseIn', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
			),
		),
		array(
			'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
			'strTitle' => '投資活動によるキャッシュ・フロー合計',
			'strClass' => 'codeLibBaseImgSheet',
			'vars' => array(
				'idTarget' => 'investSum', 'flagCalc' => 'sum', 'varsValue' => array(),
			),
			'child' => array(),
		),
		array(
			'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
			'strTitle' => '3. 財務活動によるキャッシュ・フロー',
			'strClass' => 'codeLibBaseImgFolder',
			'vars' => array(
				'idTarget' => 'finance',
			),
			'child' => array(
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '短期借入れによる収入',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'shortTermLoansPayableIn', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '短期借入金の返済による支出',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'shortTermLoansPayableOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '長期借入れによる収入',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'longTermLoansPayableIn', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '長期借入金の返済による支出',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'longTermLoansPayableOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '社債発行による収入',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'bondIn', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '社債償還による支出',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'bondOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '株式発行による収入',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'stockIn', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '自己株式の取得による支出',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'stockSelfOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => '配当金の支払額',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'dividendsOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => 'その他財務活動による支出',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'financeElseOut', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
				array(
					'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
					'strTitle' => 'その他財務活動による収入',
					'strClass' => 'codeLibBaseImgSheet',
					'vars' => array(
						'idTarget' => 'financeElseIn', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
					),
					'child' => array(),
				),
			),
		),
		array(
			'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
			'strTitle' => '財務活動によるキャッシュ・フロー合計',
			'strClass' => 'codeLibBaseImgSheet',
			'vars' => array(
				'idTarget' => 'financeSum', 'flagCalc' => 'sum', 'varsValue' => array(),
			),
			'child' => array(),
		),
		array(
			'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
			'strTitle' => '4. 現金及び現金同等物に係る為替差額',
			'strClass' => 'codeLibBaseImgSheet',
			'vars' => array(
				'idTarget' => 'cashRate', 'varsValue' => array(),
			),
			'child' => array(),
		),
		array(
			'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
			'strTitle' => '5. 現金及び現金同等物の増加額',
			'strClass' => 'codeLibBaseImgSheet',
			'vars' => array(
				'idTarget' => 'cashNet', 'flagCalc' => 'net', 'varsValue' => array(),
			),
			'child' => array(),
		),
		array(
			'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
			'strTitle' => '6. 現金及び現金同等物の期首残高',
			'strClass' => 'codeLibBaseImgSheet',
			'vars' => array(
				'idTarget' => 'cashOpening', 'varsValue' => array(),
			),
			'child' => array(),
		),
		array(
			'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
			'strTitle' => '7. 現金及び現金同等物の期末残高',
			'strClass' => 'codeLibBaseImgSheet',
			'vars' => array(
				'idTarget' => 'cashClosing', 'varsValue' => array(),
			),
			'child' => array(),
		),
	),
);
