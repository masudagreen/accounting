<?php

$vars = array(
	'varsJournalRequest' => array(
		'varsRequest' => array(
			'flagFiscalReport' => '',
			'stampBook' => '',
			'strTitle' => '',
			'jsonDetail' => array(
				'numSum' => '',
				'numSumDebit' => '',
				'numSumCredit' => '',
				'idAccountTitleCredit' => '',
				'idAccountTitleDebit' => '',
				'varsDetail' => array(),
			),
			'id' => '',
			'idAccount' => '',
			'arrCommaIdLogFile' => '',
			'arrCommaIdAccountPermit' => '',
			'arrSpaceStrTag' => '',
		),
		'varsDetail' => array(
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
	),
	'varsJournal' => array(
		'idAccountDebit' => 'suspensePaymentConsumptionTaxes',
		'idAccountCredit' => 'suspenseReceiptOfConsumptionTaxes',
		'varsTmpl' => array(
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
		'jsonDetail' => array(
			'idAccountTitleDebit' => '',
			'idAccountTitleCredit' => '',
			'numSum' => 0,
			'numSumDebit' => 0,
			'numSumCredit' => 0,
			'varsEntityNation' => array(
				'flagConsumptionTaxFree' => 0,
				'flagConsumptionTaxGeneralRule' => 0,
				'flagConsumptionTaxDeducted' => 0,
				'flagConsumptionTaxIncluding' => 0,
				'flagConsumptionTaxCalc' => 0,
				'flagConsumptionTaxWithoutCalc' => 0,
				'flagConsumptionTaxBusinessType' => 0,
			),
			'varsDetail' => array(),
		),
	),
	'varsItem'  => array(
		'strCR' => '製',
		'strCRAgri' => '生',
		'arrayFS' => array( 'BS' => '貸借科目', 'PL' => '損益科目', 'CR' => '製造原価科目',  ),
		'arrayFSAgri' => array( 'BS' => '貸借科目', 'PL' => '損益科目', 'CR' => '生産原価科目',  ),
	),
	'varsAccountTitle'  => array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 0,
		'strTitle' => '',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => '', 'idAccountTitleJgaapFS' => '',
			'varsJgaapCS' => array(
				'varsDirect' => array(
					'idAccountTitleMinus' => '', 'flagMethodPlus' => '',
					'idAccountTitlePlus' => '', 'flagMethodMinus' => '',
				),
				'varsInDirect' => array(
					'idAccountTitleMinus' => '', 'flagMethodPlus' => '',
					'idAccountTitlePlus' => '', 'flagMethodMinus' => '',
				),
			),
			'flagUse' => 1, 'flagDebit' => 1, 'flagConsumptionTaxGeneralRuleEach' => '', 'flagConsumptionTaxGeneralRuleProration' => '', 'flagConsumptionTaxSimpleRule' => '',
			'strKeyRome' => '', 'strKeyHira' => '', 'varsValue' => array(),
		),
		'child' => array(),
	),
	'varsCSOption'  => array(
		'arrayOption' => array(
			array('strTitle' => '貸借差額', 'value' => 'net',),
			array('strTitle' => '借方合計額', 'value' => 'sumDebit',),
			array('strTitle' => '貸方合計額', 'value' => 'sumCredit',),
		),
		'arrayOptionCash' => array( 'strTitle' => '現金及び現金同等物', 'value' => 'cash', ),
		'arrayOptionNone' => array( 'strTitle' => '対象外', 'value' => 'none', ),
	),
	'varsAccountTitleFS'  => array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 0,
		'strTitle' => '',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => '', 'flagUse' => 1, 'flagDebit' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
		),
		'child' => array(),
	),
	'varsAccountTitleFSCS'  => array(
		'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 0, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 1, 'flagChildrenUse' => 0, 'flagDefault' => 0,
		'strTitle' => '',
		'strClass' => 'codeLibBaseImgSheet',
		'vars' => array(
			'idTarget' => '', 'flagUse' => 1, 'flagSortUse' => 1, 'varsValue' => array(),
		),
		'child' => array(),
	),
);
