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
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '現金',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'cash',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '当座預金',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'checkingAccounts',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '定期預金',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'fixedDeposit',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => 'その他の預金',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'depositElse',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '受取手形',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'notesReceivable',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '売掛金',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'accountsReceivable',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '有価証券',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'securities',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '棚卸資産',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'inventries',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '前払金',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'advancesAccount',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '貸付金',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'loansReceivable',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '建物',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'buildings',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '建物附属設備',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'buildingsAndAccessoyEquipment',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '機械装置',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'machineryAndEquipment',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '車両運搬具',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'car',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '工具器具備品',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'furnitureAndFixture',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '土地',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'land',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'blankBSDebit1',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'blankBSDebit2',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'blankBSDebit3',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'blankBSDebit4',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'blankBSDebit5',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'blankBSDebit6',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => 'その他の資産',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'assetsElse',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '事業主貸',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'accountsReceivables',
					'flagUse' => 1, 'flagDebit' => 1, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
		),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
		'strTitle' => '合計',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'assetsSum', 'flagDebit' => 1, 'flagCalc' => 'sum', 'varsValue' => array(),
		),
		'child' => array(),
	),
	array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 0, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 1,
		'strTitle' => '負債・資本の部',
		'strClass' => 'codeLibBaseImgFolder',
		'vars' => array(
			'idTarget' => 'liabilities', 'flagDebit' => 0,
		),
		'child' => array(
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '支払手形',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'notesPayable',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '買掛金',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'accountsAmountPayable',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '借入金',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'loansPayable',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '未払金',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'accruedAmountPayable',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '前受金',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'advancesByCustomers',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '預り金',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'depositePayable',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'blankBSCredit1',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'blankBSCredit2',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'blankBSCredit3',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'blankBSCredit4',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'blankBSCredit5',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'blankBSCredit6',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'blankBSCredit7',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '貸倒引当金',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'allowanceForBadDebtsTrade',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'blankBSCredit8',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'blankBSCredit9',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'blankBSCredit10',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'blankBSCredit11',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'blankBSCredit12',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'blankBSCredit13',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => 'その他の負債',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'liabilitiesElse',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '事業主借',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'accountsPayables',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '元入金',
				'strClass' => 'codeLibBaseImgSheet',
				'vars' => array(
					'idTarget' => 'profitBroughtForward',
					'flagUse' => 1, 'flagDebit' => 0, 'varsValue' => array(), 'flagSortUse' => 0,
				),
				'child' => array(),
			),
			array(
				'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 0, 'flagFoldUse' => 1, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 1,
				'strTitle' => '青色申告特別控除前の所得金額',
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
		'strTitle' => '合計',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => 'liabilitiesNetAssetsSum', 'flagDebit' => 0, 'flagCalc' => 'sum', 'varsValue' => array(),
		),
		'child' => array(),
	),
);
