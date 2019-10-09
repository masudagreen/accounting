<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcLog extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extChildSelf = array(
		'varsItem' => array(),
	);

	/**
	 *
	 */
	public function run()
	{
		if (FLAG_TEST) {
			var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
		}
		exit;
	}

	/*
		(array(
			'flagStatus'              => 'add',
			'arrOrder'                => $arr['arrOrder'],
			'idEntity'                => $idEntity,
			'numFiscalPeriod'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagTempPrev'            => (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow))? 1 : 0,
			'numFiscalPeriodTempNext' => $numFiscalPeriodTempNext,
		))
	 * */
	public function allot($arr)
	{
		$method = '_ini' . ucwords($arr['flagStatus']);
		if (method_exists($this, $method)) {
			return $this->$method($arr);

		} else {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}
	}

	/**
		(array(
			array(
				'numFiscalPeriod' => ,
				'idEntity' => ,
				'idAccount' => ,
				'flagFiscalReport' => ,
				'stampBook' => ,
				'jsonDetail' => array(
					'idAccountTitleDebit' => '',
					'idAccountTitleCredit' => '',
					'numSum' => 0,
					'numSumDebit' => 0,
					'numSumCredit' => 0,
					'varsEntityNation' => array(
						'flagConsumptionTaxFree' => int,
						'flagConsumptionTaxGeneralRule' => int,
						'flagConsumptionTaxDeducted' => int,
						'flagConsumptionTaxIncluding' => int,
					),
					'varsDetail' => array(
						array(
							'id' => '',
							'flagFoldNow' => 0,
							'arrDebit' => array(
								'idAccountTitle' => '',
								'numValue' => '',
								'numValueConsumptionTax' => '',
								'numRateConsumptionTax' => '',
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
					'numVersionConsumptionTax' => 0,
				),
				'strTitle' => ,//摘要
				'arrSpaceStrTag' => ,
				'arrCommaIdAccountPermit' => ,
				'numSumMax' => ,
				'arrCommaIdLogFile' => ,
			),
		))
	 */
	protected function _iniAdd($arr)
	{
		global $classDb;
		global $classEscape;

		$varsItem = $this->_extChildSelf['varsItem'][$arr['numFiscalPeriod']];
		if (!$varsItem) {
			$varsItem = $this->_getVarsItem(array(
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
				'idEntity'        => $arr['idEntity'],
			));
			$this->_extChildSelf['varsItem'][$arr['numFiscalPeriod']] = $varsItem;
		}

		$array = $arr['arrOrder'];
		foreach ($array as $key => $value) {
			$flag = $this->_checkValueDetail(array(
				'arrValue' => $value,
				'varsItem' => $varsItem,
			));
			if ($flag) {
				return $flag;
			}
		}

		$arrVarsLogValue = array();
		$arrVarsLog = array();
		foreach ($array as $key => $value) {
			$temp = $this->_setDbLog(array(
				'arrValue' => $value,
				'varsItem' => $varsItem,
			));
			$arrVarsLog[] = $temp['varsLog'];
			if ($temp['varsLogValue']) {
				$arrVarsLogValue[] = $temp['varsLogValue'];
			}
		}
		if ($arrVarsLogValue) {
			$flag = $this->_setDbLogCalc($arr, $arrVarsLogValue);
			if ($flag == 'errorDataMax') {
				return $flag;
			}
		}
		$this->_updateDbPreferenceStamp(array('strColumn' => 'log'));

		return $arrVarsLog;
	}

	/**
		(array(
			array(
				'numFiscalPeriod' => ,
				'idEntity' => ,
				'idAccount' => ,
				'flagFiscalReport' => ,
				'stampBook' => ,
				'jsonDetail' => array(
					'idAccountTitleDebit' => '',
					'idAccountTitleCredit' => '',
					'numSum' => 0,
					'numSumDebit' => 0,
					'numSumCredit' => 0,
					'varsEntityNation' => array(
						'flagConsumptionTaxFree' => int,
						'flagConsumptionTaxGeneralRule' => int,
						'flagConsumptionTaxDeducted' => int,
						'flagConsumptionTaxIncluding' => int,
					),
					'varsDetail' => array(
						array(
							'id' => '',
							'flagFoldNow' => 0,
							'arrDebit' => array(
								'idAccountTitle' => '',
								'numValue' => '',
								'numValueConsumptionTax' => '',
								'numRateConsumptionTax' => '',
								'flagRateConsumptionTaxReduced' => '',
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
								'flagRateConsumptionTaxReduced' => '',
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
					'numVersionConsumptionTax' => 0,
				),
				'strTitle' => ,//摘要
				'arrSpaceStrTag' => ,
				'arrCommaIdAccountPermit' => ,
				'numSumMax' => ,
				'arrCommaIdLogFile' => ,
			),
		))
	 */
	protected function _iniVarsDbLog($arr)
	{
		global $classDb;
		global $classEscape;

		$varsItem = $this->_extChildSelf['varsItem'][$arr['numFiscalPeriod']];
		if (!$varsItem) {
			$varsItem = $this->_getVarsItem(array(
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
				'idEntity'        => $arr['idEntity'],
			));
			$this->_extChildSelf['varsItem'][$arr['numFiscalPeriod']] = $varsItem;
		}

		$array = $arr['arrOrder'];
		foreach ($array as $key => $value) {
			$flag = $this->_checkValueDetail(array(
				'arrValue' => $value,
				'varsItem' => $varsItem,
			));
			if ($flag) {
				return $flag;
			}
		}

		$arrVars = array();
		foreach ($array as $key => $value) {
			$temp = $this->_getVarsDbLog(array(
				'arrValue' => $value,
				'varsItem' => $varsItem,
			));
			$arrVars[] = $temp;
		}

		return $arrVars;
	}

	/**
		 (array(
				'arrValue' => $value,
				'varsItem' => $varsItem,
		 ))
	 */
	protected function _getVarsDbLog($arr)
	{
		global $classEscape;

		$stampRegister = TIMESTAMP;
		$stampUpdate = TIMESTAMP;

		$stampBook = $this->_getDbLogStampBook(array(
			'varsItem' => $arr['varsItem'],
			'arrValue' => $arr['arrValue'],
		));
		$arr['arrValue']['stampBook'] = $stampBook;

		$idEntity = $arr['arrValue']['idEntity'];
		$numFiscalPeriod = $arr['arrValue']['numFiscalPeriod'];

		$idAccount = $arr['arrValue']['idAccount'];
		$stampArrive = ($arr['arrValue']['stampArrive'])? $arr['arrValue']['stampArrive'] : null;

		$flagFiscalReport = $arr['arrValue']['flagFiscalReport'];
		if ($flagFiscalReport == 'none') {
			$flagFiscalReport = '0';
			$arr['arrValue']['flagFiscalReport'] = '0';
		}

		$strTitle = $arr['arrValue']['strTitle'];

		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arr['arrValue']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));

		$flagApply = 1;
		$idAccountApply = $idAccount;
		if ($arr['arrValue']['idAccountApply']) {
			$idAccountApply = $arr['arrValue']['idAccountApply'];
		}
		$flagApplyBack = 0;
		$array = $classEscape->splitCommaArrayData(array('data' => $arr['arrValue']['arrCommaIdAccountPermit']));
		if (!count($array)) {
			$flagApply = 0;
			$arr['arrValue']['numSumMax'] = 0;
			$idAccountApply = null;
		}

		$arrCommaIdLogFile = $classEscape->splitCommaArrayData(array('data' => $arr['arrValue']['arrCommaIdLogFile']));
		$arrCommaIdLogFile = $classEscape->joinCommaArray(array('arr' => $arrCommaIdLogFile));

		$varsVersion = $this->_getDbLogVarsVersion(array(
			'varsEntityNation' => $arr['varsItem']['varsEntityNation'],
			'varsFSItem'       => $arr['varsItem']['varsFSItem'],
			'arrValue'         => $arr['arrValue'],
		));
		$jsonVersion = $varsVersion['jsonVersion'];

		$numValue = $varsVersion['numValue'];

		$arrCommaIdDepartmentDebit = $varsVersion['arrCommaIdDepartmentDebit'];
		$arrCommaIdAccountTitleDebit = $varsVersion['arrCommaIdAccountTitleDebit'];
		$arrCommaIdSubAccountTitleDebit = $varsVersion['arrCommaIdSubAccountTitleDebit'];
		$arrCommaConsumptionTaxDebit = $varsVersion['arrCommaConsumptionTaxDebit'];
		$arrCommaRateConsumptionTaxDebit = $varsVersion['arrCommaRateConsumptionTaxDebit'];
		$arrCommaConsumptionTaxWithoutCalcDebit = $varsVersion['arrCommaConsumptionTaxWithoutCalcDebit'];

		$arrCommaTaxPaymentDebit = $varsVersion['arrCommaTaxPaymentDebit'];
		$arrCommaTaxReceiptDebit = $varsVersion['arrCommaTaxReceiptDebit'];

		$arrCommaIdDepartmentCredit = $varsVersion['arrCommaIdDepartmentCredit'];
		$arrCommaIdAccountTitleCredit = $varsVersion['arrCommaIdAccountTitleCredit'];
		$arrCommaIdSubAccountTitleCredit = $varsVersion['arrCommaIdSubAccountTitleCredit'];
		$arrCommaConsumptionTaxCredit = $varsVersion['arrCommaConsumptionTaxCredit'];
		$arrCommaRateConsumptionTaxCredit = $varsVersion['arrCommaRateConsumptionTaxCredit'];
		$arrCommaConsumptionTaxWithoutCalcCredit = $varsVersion['arrCommaConsumptionTaxWithoutCalcCredit'];

		$arrCommaTaxPaymentCredit = $varsVersion['arrCommaTaxPaymentCredit'];
		$arrCommaTaxReceiptCredit = $varsVersion['arrCommaTaxReceiptCredit'];

		$arrCommaIdDepartmentVersion = $varsVersion['arrCommaIdDepartment'];
		$arrCommaIdAccountTitleVersion = $varsVersion['arrCommaIdAccountTitle'];
		$arrCommaIdSubAccountTitleVersion = $varsVersion['arrCommaIdSubAccountTitle'];

		$arrChargeHistory = array(
			array(
				'stampRegister' => TIMESTAMP,
				'idAccount'     => $idAccount,
			),
		);
		$jsonChargeHistory = json_encode($arrChargeHistory);
		$arrPermitHistory = array(
			array(
				'flagInvalid'        => 0,
				'stampRegister'      => TIMESTAMP,
				'numSumMax'          => (int) $arr['arrValue']['numSumMax'],
				'idAccountApply'     => $idAccountApply,
				'arrIdAccountPermit' => $this->_getDbLogArrIdAccountPermit($arr),
			),
		);
		if (!$flagApply) {
			$arrPermitHistory = array();
		}
		$jsonPermitHistory = json_encode($arrPermitHistory);
		$arrCommaIdAccountPermit = $this->_getDbLogArrCommaIdAccountPermit($arrPermitHistory);

		$idLog = 'dummy';

		$arrayTemp = compact(
			'stampRegister',
			'stampUpdate',
			'stampArrive',
			'stampBook',
			'idLog',
			'idEntity',
			'numFiscalPeriod',
			'idAccount',
			'flagFiscalReport',
			'strTitle',
			'arrSpaceStrTag',
			'flagApply',
			'idAccountApply',
			'flagApplyBack',
			'arrCommaIdAccountPermit',
			'arrCommaIdLogFile',
			'jsonVersion',
			'numValue',
			'arrCommaIdDepartmentDebit',
			'arrCommaIdAccountTitleDebit',
			'arrCommaIdSubAccountTitleDebit',
			'arrCommaConsumptionTaxDebit',
			'arrCommaRateConsumptionTaxDebit',
			'arrCommaConsumptionTaxWithoutCalcDebit',
			'arrCommaTaxPaymentDebit',
			'arrCommaTaxReceiptDebit',
			'arrCommaIdDepartmentCredit',
			'arrCommaIdAccountTitleCredit',
			'arrCommaIdSubAccountTitleCredit',
			'arrCommaConsumptionTaxCredit',
			'arrCommaRateConsumptionTaxCredit',
			'arrCommaConsumptionTaxWithoutCalcCredit',
			'arrCommaTaxPaymentCredit',
			'arrCommaTaxReceiptCredit',
			'arrCommaIdDepartmentVersion',
			'arrCommaIdAccountTitleVersion',
			'arrCommaIdSubAccountTitleVersion',
			'jsonChargeHistory',
			'jsonPermitHistory'
		);

		return $arrayTemp;
	}

	/*
		(array(
			'flagStatus'              => 'check',
			'arrValue'                => $arrValue['arr'],
			'idEntity'                => $idEntity,
			'numFiscalPeriod'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagCheck'               => '',
			'varsItem'                => array(),
		))
	 * */
	protected function _iniCheck($arr)
	{
		$varsItem = $arr['varsItem'];
		if (!$varsItem) {
			$varsItem = $this->_extChildSelf['varsItem'][$arr['numFiscalPeriod']];
			if (!$varsItem) {
				$varsItem = $this->_getVarsItem(array(
					'numFiscalPeriod' => $arr['numFiscalPeriod'],
					'idEntity'        => $arr['idEntity'],
				));
				$this->_extChildSelf['varsItem'][$arr['numFiscalPeriod']] = $varsItem;
			}
		}

		$flag = $this->_checkValueDetail(array(
			'arrValue'  => $arr['varsOrder'],
			'varsItem'  => $varsItem,
			'flagCheck' => $arr['flagCheck'],
		));
		if ($flag) {
			return $flag;
		}
	}

	/**

	 */
	protected function _getVarsItem($arr)
	{
		global $varsPluginAccountingEntity;

		$arrSubAccountTitle = $this->_getVarsSubAccountTitle(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$arrAccountTitle = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'numFiscalPeriod'    => $arr['numFiscalPeriod'],
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$arrDepartment = $this->_getVarsDepartment(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$varsConsumptionTax = $this->_getVarsConsumptionTax(array(
			'strLang'         => $varsPluginAccountingEntity[$arr['idEntity']]['strLang'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$data = array(
			'arrAccountTitle'    => $arrAccountTitle,
			'arrDepartment'      => $arrDepartment,
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'varsEntityNation'   => $varsEntityNation,
			'varsConsumptionTax' => $varsConsumptionTax,
			'varsFSItem'         => $varsFSItem,
		);

		return $data;
	}

	/**
			(array(
				'arrValue' => $value,
				'varsItem' => $varsItem,
			))
	 */
	protected function _checkValueDetail($arr)
	{
		if ($arr['flagCheck']) {
			$method = '_checkValueDetail' . ucwords($arr['flagCheck']);
			if (method_exists($this, $method)) {
				return $this->$method($arr);
			}
			return;
		}

		$flag = $this->_checkValueDetailVarsEntityNation($arr);
		if ($flag) {
			return $flag;
		}
		$flag = $this->_checkValueDetailStampBook($arr);
		if ($flag) {
			return $flag;
		}
		$flag = $this->_checkValueDetailJournal($arr);
		if ($flag) {
			return $flag;
		}
		$flag = $this->_checkValueDetailFile($arr);
		if ($flag) {
			return $flag;
		}
		$flag = $this->_checkValueDetailPermit($arr);
		if ($flag) {
			return $flag;
		}
	}

	/**

	 */
	protected function _checkValueDetailVarsEntityNation($arr)
	{
		$array = array(
			'flagConsumptionTaxFree',
			'flagConsumptionTaxGeneralRule',
			'flagConsumptionTaxDeducted',
			'flagConsumptionTaxIncluding',
			/*
			'flagConsumptionTaxCalc',
			'flagConsumptionTaxWithoutCalc',
			'flagConsumptionTaxBusinessType',
			*/
		);
		$arrayCheck = array();
		foreach ($array as $key => $value) {
			$arrayCheck[$value] = 1;
		}

		$varsEntityNation = $arr['arrValue']['jsonDetail']['varsEntityNation'];
		$array = $arr['varsItem']['varsEntityNation'];
		foreach ($array as $key => $value) {
			if ($arrayCheck[$key]) {
				if ($varsEntityNation[$key] != $value) {
					return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;
				}
			}
		}

	}

	/**

	*/
	protected function _checkValueDetailStampBook($arr)
	{
		global $varsAccount;

		$numTimeZone = PLUGIN_ACCOUNTING_NUM_TIME_ZONE;
		$flagFiscalReport = $arr['arrValue']['flagFiscalReport'];

		if ($flagFiscalReport == 'none') {
			$data = $this->_getNumFiscalTermStamp(array(
				'arrValue' => $arr['arrValue'],
				'varsItem' => $arr['varsItem'],
			));
			$strStamp = $arr['arrValue']['stampBook'];
			preg_match( "/^([0-9]{4})\/([0-9]{1,2})\/([0-9]{1,2})-([0-9]{1,2}):([0-9]{1,2})$/", $strStamp, $arrMatch);
			list($strStamp, $numYear, $numMonth, $numDate, $numHour, $numMin) = $arrMatch;

			$strTimeZone = (-1 * $varsAccount['numTimeZone']) . 'hours';
			$dateTime = new DateTime("$numYear-$numMonth-$numDate 0:0 $strTimeZone", new DateTimeZone("UTC"));
			$stampBook = $dateTime->format('U') + $numHour * 3600 + $numMin * 60;

			$stampMin = $data['stampMin'];
			$stampMax = $data['stampMax'];
			if (!($stampMin <= $stampBook && $stampBook <= $stampMax)) {
				return 'term';
			}
		}

		$varsEntityNation = $arr['varsItem']['varsEntityNation'];
		if ($varsEntityNation['numFiscalTermMonth'] != 12) {
			if ($arr['arrValue']['flagFiscalReport'] == 'f1') {

			} elseif (preg_match( "/^f/", $arr['arrValue']['flagFiscalReport'])) {
				return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;
			}
		}
	}

	/**
	 *
	 */
	protected function _getNumFiscalTermStamp($arr)
	{
		global $varsPluginAccountingEntity;

		$numTimeZone = PLUGIN_ACCOUNTING_NUM_TIME_ZONE;

		$numFiscalPeriod = $arr['arrValue']['numFiscalPeriod'];
		$numFiscalPeriodStart = $varsPluginAccountingEntity[$arr['arrValue']['idEntity']]['numFiscalPeriodStart'];
		$numFiscalBeginningYear = $arr['varsItem']['varsEntityNation']['numFiscalBeginningYear'];
		$numCurrentYear = $numFiscalBeginningYear;

		$strTimeZone = (-1 * $numTimeZone) . 'hours';
		$numYear = $numCurrentYear;
		$numMonth = $arr['varsItem']['varsEntityNation']['numFiscalBeginningMonth'];
		$dateTime = new DateTime("$numYear-$numMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stampMin = $dateTime->format('U');

		$numEndMonth = $arr['varsItem']['varsEntityNation']['numFiscalBeginningMonth'] + $arr['varsItem']['varsEntityNation']['numFiscalTermMonth'];
		if ($numEndMonth > 12) {
			$numCurrentYear++;
			$numEndMonth -= 12;
		}

		$numYear = $numCurrentYear;
		$numMonth = $numEndMonth;
		$dateTime = new DateTime("$numYear-$numMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stampMax = $dateTime->format('U') - 1;

		$data = array(
			'stampMin' => $stampMin,
			'stampMax' => $stampMax,
		);

		return $data;
	}

	/**

	*/
	protected function _getValueDetailStampBook($arr)
	{
		global $varsAccount;
		global $classTime;

		$numTimeZone = PLUGIN_ACCOUNTING_NUM_TIME_ZONE;
		$flagFiscalReport = $arr['arrValue']['flagFiscalReport'];

		$stampBook = 0;
		if ($flagFiscalReport == 'none') {

			$strStamp = $arr['arrValue']['stampBook'];
			preg_match( "/^([0-9]{4})\/([0-9]{1,2})\/([0-9]{1,2})-([0-9]{1,2}):([0-9]{1,2})$/", $strStamp, $arrMatch);
			list($strStamp, $numYear, $numMonth, $numDate, $numHour, $numMin) = $arrMatch;

			$strTimeZone = (-1 * $varsAccount['numTimeZone']) . 'hours';
			$dateTime = new DateTime("$numYear-$numMonth-$numDate 0:0 $strTimeZone", new DateTimeZone("UTC"));
			$stampBook = $dateTime->format('U') + $numHour * 3600 + $numMin * 60;

		} elseif ($flagFiscalReport == 'f1' || $flagFiscalReport == 'f21') {
			$data = $this->_getVarsStampTerm(array(
				'varsFlag'         => array('flagFiscalPeriod' => $flagFiscalReport),
				'varsEntityNation' => $arr['varsItem']['varsEntityNation'],
				'numFiscalPeriod'  => $arr['arrValue']['numFiscalPeriod'],
			));
			$stampBook = $data['stampMax'];
		}

		return $stampBook;

	}

	/**

	 */
	protected function _checkValueDetailJournal($arr)
	{
		global $classEscape;
		global $classCheck;
		global $classTime;

		$arrNum = array(
			'Debit' => 0,
			'Credit' => 0,
		);

		$stampBook = $this->_getValueDetailStampBook($arr);
		$numRate = $classTime->checkRateConsumptionTax(array('stamp' => $stampBook));

		$flag = 0;
		$array = $arr['arrValue']['jsonDetail']['varsDetail'];
		$flagConsumptionTaxFree = (int) $arr['varsItem']['varsEntityNation']['flagConsumptionTaxFree'];
		$flagConsumptionTaxIncluding = (int) $arr['varsItem']['varsEntityNation']['flagConsumptionTaxIncluding'];

		foreach ($array as $key => $value) {
			$arrayStr = array('Debit', 'Credit');
			foreach ($arrayStr as $keyStr => $valueStr) {
				$strSide = 'arr' . $valueStr;
				$idAccountTitle = $value[$strSide]['idAccountTitle'];
				if ($idAccountTitle) {
					if (!$arr['varsItem']['arrAccountTitle']['arrStrTitle'][$idAccountTitle]) {
						$flag = 'idAccountTitle';
						break;
					}

					if ($value[$strSide]['numValue'] <= 0) {
						$flag = 'numValue';
						break;
					}

					if ($value[$strSide]['numValueConsumptionTax'] != '') {
						$flagCheck = $classCheck->checkValueWord(array(
							'flagType' => 'num',
							'value'    => $value[$strSide]['numValueConsumptionTax']
						));
						if ($flagCheck) {
							$flag = 'numValueConsumptionTax';
							break;
						}
					}

					$flagRate = 0;
					$flagRateTax = 0;
					if ($value[$strSide]['numRateConsumptionTax'] != '') {
						$flagRate = 1;
						/*
						 * 20191001 start
						 */
						if ($value[$strSide]['flagRateConsumptionTaxReduced'] == 1) {
						    if ($value[$strSide]['numRateConsumptionTax'] != 8) {
						        $flag = 'numRateConsumptionTax';
						        break;
						    }
						}

						if (!preg_match("/^(5|8|10)$/", $value[$strSide]['numRateConsumptionTax'])) {
						//if (!preg_match("/^(5|8)$/", $value[$strSide]['numRateConsumptionTax'])) {
						/*
						 * 20191001 end
						*/
							$flag = 'numRateConsumptionTax';
							break;

						} else {
						    if ($numRate == 8) {
								if ($value[$strSide]['numRateConsumptionTax'] == 10
								/*
								 * 20191001 start
								 */
								    || $value[$strSide]['flagRateConsumptionTaxReduced'] == 1
								/*
								 * 20191001 start
								 */
								  ) {
									$flag = 'numRateConsumptionTax';
									break;
								}

							} elseif ($numRate == 5) {
								if ($value[$strSide]['numRateConsumptionTax'] == 8
									|| $value[$strSide]['numRateConsumptionTax'] == 10
									/*
									 * 20191001 start
									 */
								    || $value[$strSide]['flagRateConsumptionTaxReduced'] == 1
								    /*
								     * 20191001 start
								     */
								) {
									$flag = 'numRateConsumptionTax';
									break;
								}
							}
						}
					}

					$idSubAccountTitle = $value[$strSide]['idSubAccountTitle'];
					if ($idSubAccountTitle) {
						if (!$arr['varsItem']['arrSubAccountTitle']['arrStrTitle'][$idAccountTitle][$idSubAccountTitle]) {
							$flag = 'idSubAccountTitle';
							break;
						}
					}
					$idDepartment = $value[$strSide]['idDepartment'];
					if ($idDepartment) {
						if (!$arr['varsItem']['arrDepartment']['arrStrTitle'][$idDepartment]) {
							$flag = 'idDepartment';
							break;
						}
					}
					if ($flagConsumptionTaxFree != $value[$strSide]['flagConsumptionTaxFree']) {
						$flag = 'flagConsumptionTaxFree';
						break;
					}
					if ($flagConsumptionTaxIncluding != $value[$strSide]['flagConsumptionTaxIncluding']) {
						$flag = 'flagConsumptionTaxIncluding';
						break;
					}

					$arrNum[$valueStr] += $value[$strSide]['numValue'];

					$flagTax = 0;

					$flagConsumptionTaxGeneralRuleEach = $value[$strSide]['flagConsumptionTaxGeneralRuleEach'];
					if ($flagConsumptionTaxGeneralRuleEach) {
						if (!$arr['varsItem']['varsConsumptionTax']['arrStrGeneralEach'][$flagConsumptionTaxGeneralRuleEach]) {
							$flag = 'flagConsumptionTaxGeneralRuleEach';
							break;
						}
						if ((int) $arr['varsItem']['varsEntityNation']['flagConsumptionTaxGeneralRule']
							&& (int) $arr['varsItem']['varsEntityNation']['flagConsumptionTaxDeducted']
							&& preg_match( "/^tax/", $flagConsumptionTaxGeneralRuleEach)
						) {
							$flagTax = 1;
						}
						if ($flagRate) {
							if (!$flagConsumptionTaxFree) {
								if (preg_match( "/^tax/", $flagConsumptionTaxGeneralRuleEach)
									|| preg_match( "/^else/", $flagConsumptionTaxGeneralRuleEach)
								) {
									$flagRateTax = 1;
								}
							}
						}
					}

					$flagConsumptionTaxGeneralRuleProration = $value[$strSide]['flagConsumptionTaxGeneralRuleProration'];
					if ($flagConsumptionTaxGeneralRuleProration) {
						if (!$arr['varsItem']['varsConsumptionTax']['arrStrGeneralProration'][$flagConsumptionTaxGeneralRuleProration]) {
							$flag = 'flagConsumptionTaxGeneralRuleProration';
							break;
						}
						if ((int) $arr['varsItem']['varsEntityNation']['flagConsumptionTaxGeneralRule']
							&& !(int) $arr['varsItem']['varsEntityNation']['flagConsumptionTaxDeducted']
							&& preg_match( "/^tax/", $flagConsumptionTaxGeneralRuleProration)
						) {
							$flagTax = 1;
						}
						if ($flagRate) {
							if (!$flagConsumptionTaxFree) {
								if (preg_match( "/^tax/", $flagConsumptionTaxGeneralRuleProration)
									|| preg_match( "/^else/", $flagConsumptionTaxGeneralRuleProration)
								) {
									$flagRateTax = 1;
								}
							}
						}
					}

					$flagConsumptionTaxSimpleRule = $value[$strSide]['flagConsumptionTaxSimpleRule'];
					if ($flagConsumptionTaxSimpleRule) {
						if (!$arr['varsItem']['varsConsumptionTax']['arrStrSimple'][$flagConsumptionTaxSimpleRule]) {
							$flag = 'flagConsumptionTaxSimpleRule';
							break;
						}
						if (!(int) $arr['varsItem']['varsEntityNation']['flagConsumptionTaxGeneralRule'] &&
							preg_match( "/^tax/", $value[$strSide]['flagConsumptionTaxSimpleRule'])
						) {
							$flagTax = 1;
						}
						if ($flagRate) {
							if (!$flagConsumptionTaxFree) {
								if (preg_match( "/^tax/", $flagConsumptionTaxSimpleRule)
									|| preg_match( "/^else/", $flagConsumptionTaxSimpleRule)
								) {
									$flagRateTax = 1;
								}
							}
						}
					}

					if ($idAccountTitle == 'suspenseReceiptOfConsumptionTaxes' || $idAccountTitle == 'suspensePaymentConsumptionTaxes') {
						$flagTax = 0;
					}

					$flagConsumptionTaxWithoutCalc = $value[$strSide]['flagConsumptionTaxWithoutCalc'];
					if ($flagConsumptionTaxWithoutCalc) {
						if (!($flagConsumptionTaxWithoutCalc == 1
							|| $flagConsumptionTaxWithoutCalc == 2
							|| $flagConsumptionTaxWithoutCalc == 3
						)) {
							$flag = 'flagConsumptionTaxWithoutCalc';
							break;
						}
					}

					$flagConsumptionTaxCalc = $value[$strSide]['flagConsumptionTaxCalc'];
					if ($flagConsumptionTaxCalc) {
						if (!($flagConsumptionTaxCalc == 1
							|| $flagConsumptionTaxCalc == 2
							|| $flagConsumptionTaxCalc == 3
						)) {
							$flag = 'flagConsumptionTaxCalc';
							break;
						}
					}

					if ($flagTax) {
						if ($flagConsumptionTaxWithoutCalc == 2) {
							$numValue = $value[$strSide]['numValue'];
							$numValueConsumptionTax = $value[$strSide]['numValueConsumptionTax'];
							if ($numValue < $numValueConsumptionTax) {
								$flag = 'numValueConsumptionTax';
								break;
							}
							$arrNum[$valueStr] += $numValueConsumptionTax;
						}
					}
					if ($flagRate) {
						if (!$flagRateTax) {
							$flag = 'numRateConsumptionTax';
							break;
						}
					}

					/*
					 * free-MonetaryClaim is H26.4.1~
					* */
					if ($value[$strSide]['flagConsumptionTaxGeneralRuleEach'] == 'free-MonetaryClaim'
						|| $value[$strSide]['flagConsumptionTaxGeneralRuleProration'] == 'free-MonetaryClaim'
						|| $value[$strSide]['flagConsumptionTaxSimpleRule'] == 'free-MonetaryClaim'
					) {
						if ($numRate == 5) {
							$flag = 'free-MonetaryClaim';
							break;
						}
					}
				}
			}
			$idAccountTitleDebit = $value['arrDebit']['idAccountTitle'];
			$idAccountTitleCredit = $value['arrCredit']['idAccountTitle'];
			if (!$idAccountTitleDebit && !$idAccountTitleCredit) {
				$flag = __LINE__;
			}
			if ($flag) {
				break;
			}
		}

		if ($flag) {
			return $flag;
		}

		if ($arr['arrValue']['jsonDetail']['numSumDebit'] != $arr['arrValue']['jsonDetail']['numSumCredit']
			|| $arr['arrValue']['jsonDetail']['numSumDebit'] != $arrNum['Debit']
			|| $arr['arrValue']['jsonDetail']['numSumCredit'] != $arrNum['Credit']
			|| $arr['arrValue']['jsonDetail']['numSumCredit'] > 99999999999
			|| count($array) > 10
		) {
			$flag = __LINE__;
		}

		if ($flag) {
			return $flag;
		}
	}

	/**

	 */
	protected function _checkValueDetailPermit($arr)
	{
		global $classEscape;

		global $varsPluginAccountingAccounts;
		global $varsPluginAccountingAccountsEntity;

		$data = $arr['arrValue']['arrCommaIdAccountPermit'];
		$array = $classEscape->splitCommaArrayData(array('data' => $data));
		foreach ($array as $key => $value) {
			if (!$varsPluginAccountingAccountsEntity[$value][$arr['arrValue']['idEntity']]) {
				return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;
			}

			$varsAuthority = $this->_getVarsAuthority(array(
				'idAccount' => $value,
				'idEntity'  => $arr['arrValue']['idEntity'],
			));

			if ($varsAuthority == 'admin') {
				continue;
			}

			$str = ',' . $arr['arrValue']['idEntity'] . ',';
			$arrCommaIdEntity = $varsPluginAccountingAccounts[$value]['arrCommaIdEntity'];

			if (!preg_match("/$str/", $arrCommaIdEntity)) {
				return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;

			} else {
				if (!$varsAuthority['flagAllUpdate']) {
					return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;
				}
			}
		}

		if ($arr['arrValue']['idAccountApply']) {
			if (!$varsPluginAccountingAccountsEntity[$arr['arrValue']['idAccountApply']][$arr['arrValue']['idEntity']]) {
				return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;
			}
			$varsAuthority = $this->_getVarsAuthority(array(
				'idAccount' => $arr['arrValue']['idAccountApply'],
				'idEntity'  => $arr['arrValue']['idEntity'],
			));

			if ($varsAuthority != 'admin') {
				$str = ',' . $arr['arrValue']['idEntity'] . ',';
				$arrCommaIdEntity = $varsPluginAccountingAccounts[$arr['arrValue']['idAccountApply']]['arrCommaIdEntity'];

				if (!preg_match("/$str/", $arrCommaIdEntity)) {
					return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;

				} else {
					if (!($varsAuthority['flagMyInsert'] || $varsAuthority['flagAllInsert'])) {
						return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;
					}
				}
			}
		}

		if (count($array)) {
			if ((int) $arr['arrValue']['numSumMax'] < 1) {
				return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;
			}
		}
	}

	/**

	 */
	protected function _checkValueDetailFile($arr)
	{
		global $classEscape;
		global $classDb;
		global $varsPluginAccountingAccount;

		$data = $arr['arrValue']['arrCommaIdLogFile'];
		$array = $classEscape->splitCommaArrayData(array('data' => $data));

		foreach ($array as $key => $value) {
			$rows = $classDb->getSelect(array(
				'idModule' => 'accounting',
				'strTable' => 'accountingLogFile',
				'arrLimit' => array(
					'numStart' => 0, 'numEnd' => 1,
				),
				'arrOrder' => array(),
				'flagAnd'  => 1,
				'arrWhere' => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idEntity',
						'flagCondition' => 'eq',
						'value'         => $arr['arrValue']['idEntity'],
					),
					array(
						'flagType'      => 'num',
						'strColumn'     => 'numFiscalPeriod',
						'flagCondition' => 'eq',
						'value'         => $arr['arrValue']['numFiscalPeriod'],
					),
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idLogFile',
						'flagCondition' => 'eq',
						'value'         => $value,
					),
				),
			));
			if (!$rows['numRows']) {
				return 'noneFile';
			}
		}
	}

	/**
		 (array(
				'arrValue' => $value,
				'varsItem' => $varsItem,
		 ))
	 */
	protected function _setDbLog($arr)
	{
		global $classEscape;
		global $classDb;
		$dbh = $classDb->getHandle();

		$idEntity = $arr['arrValue']['idEntity'];
		$numFiscalPeriod = $arr['arrValue']['numFiscalPeriod'];

		$varsIdNumber = $this->_getIdAutoIncrement(array(
			'idTarget' => 'idLog'
		));
		if (!$varsIdNumber[$idEntity][$numFiscalPeriod]) {
			$varsIdNumber[$idEntity][$numFiscalPeriod] = 1;
		}
		$idLog = $varsIdNumber[$idEntity][$numFiscalPeriod];

		$arrayTemp = $this->_getVarsDbLog(array(
			'arrValue' => $arr['arrValue'],
			'varsItem' => $arr['varsItem'],
		));
		$arrayTemp['idLog'] = $idLog;

		$arrColumn = array();
		$arrValue = array();
		foreach ($arrayTemp as $keyTemp => $valueTemp) {
			$arrColumn[] = $keyTemp;
			$arrValue[] = $valueTemp;
		}

		$id = $classDb->insertRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingLog',
			'arrColumn' => $arrColumn,
			'arrValue'  => $arrValue,
		));

		$varsIdNumber[$idEntity][$numFiscalPeriod]++;
		$this->_updateIdAutoIncrement(array(
			'idTarget'   => 'idLog',
			'varsTarget' => $varsIdNumber
		));

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLog',
			'arrLimit' => array(
				'numStart' => 0, 'numEnd' => 1,
			),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere'  => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'id',
					'flagCondition' => 'eq',
					'value'         => $id,
				),
			),
		));
		$varsLog = $rows['arrRows'][0];

		$data = array();
		$data['varsLog'] = $varsLog;
		$data['varsLogValue'] = $varsLog;

		if ($flagApply) {
			$data['varsLogValue'] = array();
		}

		return $data;
	}

	/**
	 *
	 */
	protected function _getDbLogStampBook($arr)
	{
		global $varsAccount;

		$numTimeZone = PLUGIN_ACCOUNTING_NUM_TIME_ZONE;
		$strTimeZone = (-1 * $numTimeZone) . 'hours';

		$data = $this->_getNumFiscalTermStamp(array(
			'arrValue' => $arr['arrValue'],
			'varsItem' => $arr['varsItem'],
		));
		$stampMin = $data['stampMin'];
		$stampMax = $data['stampMax'];
		$varsEntityNation = $arr['varsItem']['varsEntityNation'];
		$numFiscalBeginningYear = $varsEntityNation['numFiscalBeginningYear'];
		$numCurrentYear = $numFiscalBeginningYear;
		$numFiscalBeginningMonth = $varsEntityNation['numFiscalBeginningMonth'];

		if ($varsEntityNation['numFiscalTermMonth'] != 12) {
			if ($arr['arrValue']['flagFiscalReport'] == 'f1') {
				$stampBook = $stampMax;
			}

		} else {
			if ($arr['arrValue']['flagFiscalReport'] == 'f1') {
				$stampBook = $stampMax;

			} elseif (preg_match( "/^f/", $arr['arrValue']['flagFiscalReport'])) {
				if ($arr['arrValue']['flagFiscalReport'] == 'f21' || $arr['arrValue']['flagFiscalReport'] == 'f42') {
					$numMonth = $numFiscalBeginningMonth + 6;

				} elseif ($arr['arrValue']['flagFiscalReport'] == 'f41') {
					$numMonth = $numFiscalBeginningMonth + 3;

				} elseif ($arr['arrValue']['flagFiscalReport'] == 'f43') {
					$numMonth = $numFiscalBeginningMonth + 9;
				}

				if ($numMonth > 12) {
					$numCurrentYear++;
					$numMonth -= 12;
				}

				$numYearTemp = $numCurrentYear;
				$dateTime = new DateTime("$numYearTemp-$numMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
				$stampBook = $dateTime->format('U') - 1;
			}
		}

		if (!preg_match( "/^f/", $arr['arrValue']['flagFiscalReport'])) {
			$strStamp = $arr['arrValue']['stampBook'];
			preg_match( "/^([0-9]{4})\/([0-9]{1,2})\/([0-9]{1,2})-([0-9]{1,2}):([0-9]{1,2})$/", $strStamp, $arrMatch);
			list($strStamp, $numYear, $numMonth, $numDate, $numHour, $numMin) = $arrMatch;

			$strTimeZone = (-1 * $varsAccount['numTimeZone']) . 'hours';
			$dateTime = new DateTime("$numYear-$numMonth-$numDate 0:0 $strTimeZone", new DateTimeZone("UTC"));
			$stampBook = $dateTime->format('U') + $numHour * 3600 + $numMin * 60;

		}
		preg_replace("/\.?0+$/", '', $stampBook);

		return $stampBook;
	}

	protected function _setDbLogCalc($arr, $arrVarsLog)
	{
		$arrRows = $this->_getVarsLogCalcLoop(array(
			'arrVarsLog'      => $arrVarsLog,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$classCalcAccountTitle = $this->_getClassCalc(array('flagType' => 'AccountTitle'));
		$classCalcSubAccountTitle = $this->_getClassCalc(array('flagType' => 'SubAccountTitle'));
		$classCalcConsumptionTax = $this->_getClassCalc(array('flagType' => 'ConsumptionTax'));
		$classCalcLogCalc = $this->_getClassCalc(array('flagType' => 'LogCalc'));
		$classCalcTempNextLog = $this->_getClassCalc(array(
			'flagType'   => 'TempNext',
			'flagDetail' => 'Log',
		));

		$flag = $classCalcAccountTitle->allot(array(
			'flagStatus'      => 'add',
			'arrRows'         => $arrRows,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}
		$flag = $classCalcSubAccountTitle->allot(array(
			'flagStatus'      => 'add',
			'arrRows'         => $arrRows,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}

		$flag = $classCalcConsumptionTax->allot(array(
			'flagStatus'      => 'add',
			'arrRows'         => $arrRows,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}
		$classCalcLogCalc->allot(array(
			'flagStatus'      => 'add',
			'arrRows'         => $arrRows,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		if ($arr['flagTempPrev']) {
			$flag = $classCalcTempNextLog->allot(array(
				'flagStatus'      => 'add',
				'numFiscalPeriod' => $arr['numFiscalPeriodTempNext'],
				'arrRows'         => $arrRows,
			));
			if ($flag == 'errorDataMax') {
				return $flag;
			}
		}
	}

	/**
		 (array(
			'numFiscalPeriod' => $numFiscalPeriod,
			'arrValue'        => $arr['varsItem'],
		 ))
	 */
	protected function _iniVarsVersion($arr)
	{
		$varsFSItem = $this->_extChildSelf['varsFSItem'];
		if (!$varsFSItem) {
			$varsFSItem = $this->_getVarsFSItem();
			$this->_extChildSelf['varsFSItem'] = $varsFSItem;
		}

		$varsEntityNation = $arr['varsEntityNation'];
		if (!$varsEntityNation) {
			$varsEntityNation = $this->_extChildSelf['varsEntityNation'][$arr['numFiscalPeriod']];
			if (!$varsEntityNation) {
				$varsEntityNation = $this->_getVarsEntityNation(array(
					'numFiscalPeriod' => $arr['numFiscalPeriod'],
				));
				$this->_extChildSelf['varsEntityNation'][$arr['numFiscalPeriod']] = $varsEntityNation;
			}
		}

		$varsVersion = $this->_getDbLogVarsVersion(array(
			'varsEntityNation' => $varsEntityNation,
			'varsFSItem'       => $varsFSItem,
			'arrValue'         => $arr['arrValue'],
		));

		return $varsVersion;
	}

	/**
		 (array(
			'varsEntityNation' => $varsEntityNation,
			'varsFSItem'       => $varsFSItem,
			'arrValue'         => $arr['arrValue'],
		 ))
	 */
	protected function _getDbLogVarsVersion($arr)
	{
		global $classEscape;

		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arr['arrValue']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));

		$varsEntityNation = $arr['varsEntityNation'];

		$varsDetail = $arr['arrValue']['jsonDetail']['varsDetail'];
		$varsVersion = $this->_getDbLogVarsVersionDetail(array(
			'arrValue'         => $arr['arrValue'],
			'varsEntityNation' => $arr['varsEntityNation'],
			'varsFSItem'       => $arr['varsFSItem'],
		));

		$arrVersion = array(
			array(
				'stampRegister'     => (!is_null($arr['arrValue']['stampRegister']))? $arr['arrValue']['stampRegister'] : TIMESTAMP,
				'stampUpdate'       => (!is_null($arr['arrValue']['stampUpdate']))? $arr['arrValue']['stampUpdate'] : TIMESTAMP,
				'stampBook'         => $arr['arrValue']['stampBook'],
				'strTitle'          => $arr['arrValue']['strTitle'],
				'flagFiscalReport'  => $arr['arrValue']['flagFiscalReport'],
				'arrSpaceStrTag'    => $arrSpaceStrTag,
				'arrCommaIdLogFile' => (is_null($arr['arrValue']['arrCommaIdLogFile']))? '' : $arr['arrValue']['arrCommaIdLogFile'],
				'jsonDetail' => array(
					'idAccountTitleDebit'  => $varsVersion['idAccountTitleDebit'],
					'idAccountTitleCredit' => $varsVersion['idAccountTitleCredit'],
					'numSum'               => $arr['arrValue']['jsonDetail']['numSumDebit'],
					'numSumDebit'          => $arr['arrValue']['jsonDetail']['numSumDebit'],
					'numSumCredit'         => $arr['arrValue']['jsonDetail']['numSumCredit'],
					'varsEntityNation' => array(
						'flagConsumptionTaxFree'         => $varsEntityNation['flagConsumptionTaxFree'],
						'flagConsumptionTaxGeneralRule'  => $varsEntityNation['flagConsumptionTaxGeneralRule'],
						'flagConsumptionTaxDeducted'     => $varsEntityNation['flagConsumptionTaxDeducted'],
						'flagConsumptionTaxIncluding'    => $varsEntityNation['flagConsumptionTaxIncluding'],
						/* journal.js insert
						'flagConsumptionTaxCalc'         => $varsEntityNation['flagConsumptionTaxCalc'],
						'flagConsumptionTaxWithoutCalc'  => $varsEntityNation['flagConsumptionTaxWithoutCalc'],
						'flagConsumptionTaxBusinessType' => $varsEntityNation['flagConsumptionTaxBusinessType'],
						*/
					),
					'varsDetail'               => $varsVersion['varsDetail'],
					'numVersionConsumptionTax' => 0,
				),
			),
		);

		$data = array(
			'idAccountTitleDebit'                     => $varsVersion['idAccountTitleDebit'],
			'idAccountTitleCredit'                    => $varsVersion['idAccountTitleCredit'],
			'jsonVersion'                             => json_encode($arrVersion),
			'arrVersion'                              => $arrVersion,
			'varsDetail'                              => $varsVersion['varsDetail'],
			'numValue'                                => $arr['arrValue']['jsonDetail']['numSumDebit'],
			'arrCommaIdDepartmentDebit'               => $varsVersion['arrCommaIdDepartmentDebit'],
			'arrCommaIdAccountTitleDebit'             => $varsVersion['arrCommaIdAccountTitleDebit'],
			'arrCommaIdSubAccountTitleDebit'          => $varsVersion['arrCommaIdSubAccountTitleDebit'],
			'arrCommaConsumptionTaxDebit'             => $varsVersion['arrCommaConsumptionTaxDebit'],
			'arrCommaRateConsumptionTaxDebit'         => $varsVersion['arrCommaRateConsumptionTaxDebit'],
			'arrCommaConsumptionTaxWithoutCalcDebit'  => $varsVersion['arrCommaConsumptionTaxWithoutCalcDebit'],
			'arrCommaTaxPaymentDebit'                 => $varsVersion['arrCommaTaxPaymentDebit'],
			'arrCommaTaxReceiptDebit'                 => $varsVersion['arrCommaTaxReceiptDebit'],
			'arrCommaIdDepartmentCredit'              => $varsVersion['arrCommaIdDepartmentCredit'],
			'arrCommaIdAccountTitleCredit'            => $varsVersion['arrCommaIdAccountTitleCredit'],
			'arrCommaIdSubAccountTitleCredit'         => $varsVersion['arrCommaIdSubAccountTitleCredit'],
			'arrCommaConsumptionTaxCredit'            => $varsVersion['arrCommaConsumptionTaxCredit'],
			'arrCommaRateConsumptionTaxCredit'        => $varsVersion['arrCommaRateConsumptionTaxCredit'],
			'arrCommaConsumptionTaxWithoutCalcCredit' => $varsVersion['arrCommaConsumptionTaxWithoutCalcCredit'],
			'arrCommaTaxPaymentCredit'                => $varsVersion['arrCommaTaxPaymentCredit'],
			'arrCommaTaxReceiptCredit'                => $varsVersion['arrCommaTaxReceiptCredit'],
			'arrCommaIdDepartment'                    => $varsVersion['arrCommaIdDepartment'],
			'arrCommaIdAccountTitle'                  => $varsVersion['arrCommaIdAccountTitle'],
			'arrCommaIdSubAccountTitle'               => $varsVersion['arrCommaIdSubAccountTitle'],

		);

		return $data;
	}

	/**
		 (array(
			'arrValue'         => $arr['arrValue'],
			'varsEntityNation' => $arr['varsEntityNation'],
			'varsFSItem'       => $arr['varsFSItem'],
		 ))
	 */
	protected function _getDbLogVarsVersionDetail($arr)
	{
		global $classEscape;

		$varsEntityNation = $arr['varsEntityNation'];


		$arrayStr = array('Debit', 'Credit');
		$varsCollect = array();
		foreach ($arrayStr as $keyStr => $valueStr) {
			$varsCollect['idDepartment' . $valueStr] = array();
			$varsCollect['idAccountTitle' . $valueStr] = array();
			$varsCollect['idSubAccountTitle' . $valueStr] = array();
			$varsCollect['rateConsumptionTax' . $valueStr] = array();
			$varsCollect['consumptionTax' . $valueStr] = array();
			$varsCollect['consumptionTaxWithoutCalc' . $valueStr] = array();

			$varsCollect['arrCommaTaxPayment' . $valueStr] = array();
			$varsCollect['arrCommaTaxReceipt' . $valueStr] = array();

		}
		$arrayIdDepartment = array();
		$arrayIdAccountTitle = array();
		$arrayIdSubAccountTitle = array();

		$arrayDetail = array();
		$arraySide = array(
			'arrDebit' => array(),
			'arrCredit' => array(),
		);

		$array = $this->_updateVarsOmit(array(
			'arrDetail'  => $arr['arrValue']['jsonDetail']['varsDetail'],
			'varsDetail' => $arr['varsFSItem']['varsJournalRequest']['varsDetail'],
		));

		$num = 0;
		foreach ($array as $key => $value) {
			$arrayNew = array();
			$arrayNew['id'] = $num;
			$arrayNew['flagFoldNow'] = 0;

			foreach ($arrayStr as $keyStr => $valueStr) {
				$strSide = 'arr' . $valueStr;
				$idAccountTitle = ($value[$strSide]['idAccountTitle'])? $value[$strSide]['idAccountTitle'] : '';
				$numValue = '';
				$numValueConsumptionTax = '';
				$numRateConsumptionTax = '';
				/*
				 * 20191001 start
				 */
				$flagRateConsumptionTaxReduced = '';
				/*
				 * 20191001 end
				 */
				$idDepartment = '';
				$idSubAccountTitle = '';
				$flagConsumptionTaxGeneralRuleProration = '';
				$flagConsumptionTaxGeneralRuleEach = '';
				$flagConsumptionTaxSimpleRule = '';
				$flagConsumptionTaxWithoutCalc = '';
				$flagConsumptionTaxCalc = '';
				$flagConsumptionTaxIncluding = '';
				$flagConsumptionTaxFree = '';
				$strConsumptionTax = '';
				$strConsumptionTaxWithoutCalc = '';
				$flagTax = 0;
				$flagDebit = 0;
				if ($idAccountTitle) {
					$arraySide[$strSide][] = $idAccountTitle;

					$numValue = $value[$strSide]['numValue'];
					$numValueConsumptionTax = $value[$strSide]['numValueConsumptionTax'];
					$numRateConsumptionTax = ($value[$strSide]['numRateConsumptionTax'])? $value[$strSide]['numRateConsumptionTax'] : '';
					/*
					 * 20191001 start
					 */
					$flagRateConsumptionTaxReduced = ($value[$strSide]['flagRateConsumptionTaxReduced'])? $value[$strSide]['flagRateConsumptionTaxReduced'] : '';
					/*
					 * 20191001 end
					 */
					$idDepartment = ($value[$strSide]['idDepartment'])? $value[$strSide]['idDepartment'] : '';
					$idSubAccountTitle = ($value[$strSide]['idSubAccountTitle'])? $value[$strSide]['idSubAccountTitle'] : '';
					$flagConsumptionTaxGeneralRuleProration = ($value[$strSide]['flagConsumptionTaxGeneralRuleProration'])? $value[$strSide]['flagConsumptionTaxGeneralRuleProration'] : '';
					$flagConsumptionTaxGeneralRuleEach = ($value[$strSide]['flagConsumptionTaxGeneralRuleEach'])? $value[$strSide]['flagConsumptionTaxGeneralRuleEach'] : '';
					$flagConsumptionTaxSimpleRule = ($value[$strSide]['flagConsumptionTaxSimpleRule'])? $value[$strSide]['flagConsumptionTaxSimpleRule'] : '';
					$flagConsumptionTaxWithoutCalc = ($value[$strSide]['flagConsumptionTaxWithoutCalc'] != '')? $value[$strSide]['flagConsumptionTaxWithoutCalc'] : '';
					$flagConsumptionTaxCalc = ($value[$strSide]['flagConsumptionTaxCalc'] != '')? $value[$strSide]['flagConsumptionTaxCalc'] : '';
					$flagConsumptionTaxIncluding = ($value[$strSide]['flagConsumptionTaxIncluding'] != '')? $value[$strSide]['flagConsumptionTaxIncluding'] : '';
					$flagConsumptionTaxFree = ($value[$strSide]['flagConsumptionTaxFree'] != '')? $value[$strSide]['flagConsumptionTaxFree'] : '';

					if ($idAccountTitle == 'suspenseReceiptOfConsumptionTaxes' || $idAccountTitle == 'suspensePaymentConsumptionTaxes') {
						$flagConsumptionTaxWithoutCalc = 3;
					}

					if (!(int) $varsEntityNation['flagConsumptionTaxFree']
						 && !(int) $varsEntityNation['flagConsumptionTaxIncluding']
						 && $idAccountTitle != 'suspenseReceiptOfConsumptionTaxes'
						 && $idAccountTitle != 'suspensePaymentConsumptionTaxes'
					) {
						if ((int) $varsEntityNation['flagConsumptionTaxGeneralRule']) {
							if ((int) $varsEntityNation['flagConsumptionTaxDeducted']) {
								if (preg_match("/^tax/", $flagConsumptionTaxGeneralRuleEach)) {
									$flagTax = 1;
									$flagDebit = (preg_match("/^taxDebit/", $flagConsumptionTaxGeneralRuleEach))? 1 : 0;
								}

							} else {
								if (preg_match("/^tax/", $flagConsumptionTaxGeneralRuleProration)) {
									$flagTax = 1;
									$flagDebit = (preg_match("/^taxDebit/", $flagConsumptionTaxGeneralRuleProration))? 1 : 0;
								}
							}
						}

						if (preg_match("/^tax/", $flagConsumptionTaxSimpleRule)
							&& !(int) $varsEntityNation['flagConsumptionTaxGeneralRule']
						) {
							$flagTax = 1;
							$flagDebit = (preg_match("/^taxDebit/", $flagConsumptionTaxSimpleRule))? 1 : 0;
						}
					}
					if ($flagTax) {
						if ($flagConsumptionTaxWithoutCalc == 1 || $flagConsumptionTaxWithoutCalc == 2) {
							$arraySide[$strSide][] = 'dummy';
						}
					}
				}
				if ((int) $varsEntityNation['flagConsumptionTaxFree']) {
					$flagConsumptionTaxGeneralRuleEach = '';
					$flagConsumptionTaxGeneralRuleProration = '';
					$flagConsumptionTaxSimpleRule = '';

				} else {
					if ((int) $varsEntityNation['flagConsumptionTaxGeneralRule']) {
						if ((int) $varsEntityNation['flagConsumptionTaxDeducted']) {
							$flagConsumptionTaxGeneralRuleProration = '';

						} else {
							$flagConsumptionTaxGeneralRuleEach = '';
						}
						$flagConsumptionTaxSimpleRule = '';

					} else {
						$flagConsumptionTaxGeneralRuleProration = '';
						$flagConsumptionTaxGeneralRuleEach = '';
					}
				}

				if ($idAccountTitle) {
					if (!$flagConsumptionTaxFree) {
						//strConsumptionTax
						if ((int) $varsEntityNation['flagConsumptionTaxGeneralRule']) {
							if ((int) $varsEntityNation['flagConsumptionTaxDeducted']) {
								if ($flagConsumptionTaxGeneralRuleEach) {
									$strConsumptionTax = $flagConsumptionTaxGeneralRuleEach;
								}

							} else {
								if ($flagConsumptionTaxGeneralRuleProration) {
									$strConsumptionTax = $flagConsumptionTaxGeneralRuleProration;
								}
							}

						} else {
							if ($flagConsumptionTaxSimpleRule) {
								$strConsumptionTax = $flagConsumptionTaxSimpleRule;
							}
						}

						//strConsumptionTaxWithoutCalc
						if ($flagTax
							&& $idAccountTitle != 'suspenseReceiptOfConsumptionTaxes'
							&& $idAccountTitle != 'suspensePaymentConsumptionTaxes'
							&& !$flagConsumptionTaxIncluding
						) {
							if (!$flagConsumptionTaxWithoutCalc) {
								$flagConsumptionTaxWithoutCalc = (int) $varsEntityNation['flagConsumptionTaxWithoutCalc'];
							}
							$strConsumptionTaxWithoutCalc = $flagConsumptionTaxWithoutCalc;
						}
					}


					if ($idAccountTitle == 'suspensePaymentConsumptionTaxes') {
						if ($strConsumptionTax) {
							if ($numRateConsumptionTax) {
							    /*
							     * 20191001 start
							     */
							    if ($numRateConsumptionTax == 8 && $flagRateConsumptionTaxReduced) {
							        $varsCollect['arrCommaTaxPayment' . $valueStr][] = $idDepartment . '_' . $strConsumptionTax . '_' . $numRateConsumptionTax . '_reduced';
							    } else {
							        $varsCollect['arrCommaTaxPayment' . $valueStr][] = $idDepartment . '_' . $strConsumptionTax . '_' . $numRateConsumptionTax;
							    }
							    /*
							    * 20191001 end
							    */

							} else {
								$varsCollect['arrCommaTaxPayment' . $valueStr][] = $idDepartment . '_' . $strConsumptionTax . '_';
							}
						}

					} else {
						if ($flagTax
							&& !$flagConsumptionTaxIncluding
							&& $flagConsumptionTaxWithoutCalc != 3
							&& $flagDebit
						) {
							if ($strConsumptionTax) {
							    /*
							     * 20191001 start
							     */
							    if ($numRateConsumptionTax == 8 && $flagRateConsumptionTaxReduced) {
							        $varsCollect['arrCommaTaxPayment' . $valueStr][] = $idDepartment . '_' . $strConsumptionTax . '_' . $numRateConsumptionTax . '_reduced';
							    } else {
							        $varsCollect['arrCommaTaxPayment' . $valueStr][] = $idDepartment . '_' . $strConsumptionTax . '_' . $numRateConsumptionTax;
							    }
							    /*
							     * 20191001 end
							     */

							}
						}
					}

					if ($idAccountTitle == 'suspenseReceiptOfConsumptionTaxes') {
						if ($strConsumptionTax) {
							if ($numRateConsumptionTax) {
							    /*
							     * 20191001 start
							     */
							    if ($numRateConsumptionTax == 8 && $flagRateConsumptionTaxReduced) {
							        $varsCollect['arrCommaTaxReceipt' . $valueStr][] = $idDepartment . '_' . $strConsumptionTax . '_' . $numRateConsumptionTax . '_reduced';
							    } else {
							        $varsCollect['arrCommaTaxReceipt' . $valueStr][] = $idDepartment . '_' . $strConsumptionTax . '_' . $numRateConsumptionTax;
							    }
							    /*
							     * 20191001 end
							     */

							} else {
								$varsCollect['arrCommaTaxReceipt' . $valueStr][] = $idDepartment . '_' . $strConsumptionTax . '_';
							}
						}

					} else {
						if ($flagTax
							&& !$flagConsumptionTaxIncluding
							&& $flagConsumptionTaxWithoutCalc != 3
							&& !$flagDebit
						) {
							if ($strConsumptionTax) {
							    /*
							     * 20191001 start
							     */
							    if ($numRateConsumptionTax == 8 && $flagRateConsumptionTaxReduced) {
							        $varsCollect['arrCommaTaxReceipt' . $valueStr][] = $idDepartment . '_' . $strConsumptionTax . '_' . $numRateConsumptionTax . '_reduced';
							    } else {
							        $varsCollect['arrCommaTaxReceipt' . $valueStr][] = $idDepartment . '_' . $strConsumptionTax . '_' . $numRateConsumptionTax;
							    }
							    /*
							     * 20191001 end
							     */
							}
						}
					}
				}

				$arrayNew[$strSide] = array(
					'idAccountTitle'                         => $idAccountTitle,
					'numValue'                               => $numValue,
					'numValueConsumptionTax'                 => $numValueConsumptionTax,
					'numRateConsumptionTax'                  => $numRateConsumptionTax,
					/*
					 * 20191001 start
					 */
				    'flagRateConsumptionTaxReduced'          => $flagRateConsumptionTaxReduced,
				    /*
				     * 20191001 end
				     */
					'idDepartment'                           => (int) $idDepartment,
					'idSubAccountTitle'                      => $idSubAccountTitle,
					'flagConsumptionTaxFree'                 => $flagConsumptionTaxFree,
					'flagConsumptionTaxIncluding'            => $flagConsumptionTaxIncluding,
					'flagConsumptionTaxGeneralRuleEach'      => $flagConsumptionTaxGeneralRuleEach,
					'flagConsumptionTaxGeneralRuleProration' => $flagConsumptionTaxGeneralRuleProration,
					'flagConsumptionTaxSimpleRule'           => $flagConsumptionTaxSimpleRule,
					'flagConsumptionTaxWithoutCalc'          => (int) $flagConsumptionTaxWithoutCalc,
					'flagConsumptionTaxCalc'                 => (int) $flagConsumptionTaxCalc,
				);

				if ($idDepartment) {
					$varsCollect['idDepartment' . $valueStr][] = $idDepartment;
					$arrayIdDepartment[] = $idDepartment;
				}
				if ($idAccountTitle) {
					$varsCollect['idAccountTitle' . $valueStr][] = $idAccountTitle;
					$arrayIdAccountTitle[] = $idAccountTitle;
				}
				if ($idSubAccountTitle) {
					$varsCollect['idSubAccountTitle' . $valueStr][] = $idSubAccountTitle;
					$arrayIdSubAccountTitle[] = $idSubAccountTitle;
				}
				if ($numRateConsumptionTax) {
				    /*
				     * 20191001 start
				     */
				    if ($numRateConsumptionTax == 8 && $flagRateConsumptionTaxReduced) {
				        $varsCollect['rateConsumptionTax' . $valueStr][] = $numRateConsumptionTax . '_reduced';
				    } else {
				        $varsCollect['rateConsumptionTax' . $valueStr][] = $numRateConsumptionTax;
				    }
				    /*
				     * 20191001 end
				     */
				}
				if ($strConsumptionTax) {
					$varsCollect['consumptionTax' . $valueStr][] = $strConsumptionTax;
				}
				if ($strConsumptionTaxWithoutCalc) {
					$varsCollect['consumptionTaxWithoutCalc' . $valueStr][] = $strConsumptionTaxWithoutCalc;
				}
			}
			$arrayDetail[$num] = $arrayNew;
			$num++;
		}

		$data = array(
			'idAccountTitleDebit'       => (count($arraySide['arrDebit']) == 1)? $arraySide['arrDebit'][0] : 'else',
			'idAccountTitleCredit'      => (count($arraySide['arrCredit']) == 1)? $arraySide['arrCredit'][0] : 'else',
			'varsDetail'                => $arrayDetail,
			'arrCommaIdDepartment'      => $classEscape->joinCommaArray(array('arr' => $arrayIdDepartment)),
			'arrCommaIdAccountTitle'    => $classEscape->joinCommaArray(array('arr' => $arrayIdAccountTitle)),
			'arrCommaIdSubAccountTitle' => $classEscape->joinCommaArray(array('arr' => $arrayIdSubAccountTitle)),
		);
//var_dump($varsCollect);exit;
		foreach ($arrayStr as $keyStr => $valueStr) {
			$data['arrCommaIdDepartment' . $valueStr] = $classEscape->joinCommaArray(array('arr' => $varsCollect['idDepartment' . $valueStr]));
			$data['arrCommaIdAccountTitle' . $valueStr] = $classEscape->joinCommaArray(array('arr' => $varsCollect['idAccountTitle' . $valueStr]));
			$data['arrCommaIdSubAccountTitle' . $valueStr] = $classEscape->joinCommaArray(array('arr' => $varsCollect['idSubAccountTitle' . $valueStr]));
			$data['arrCommaRateConsumptionTax' . $valueStr] = $classEscape->joinCommaArray(array('arr' => $varsCollect['rateConsumptionTax' . $valueStr]));
			$data['arrCommaConsumptionTax' . $valueStr] = $classEscape->joinCommaArray(array('arr' => $varsCollect['consumptionTax' . $valueStr]));
			$data['arrCommaConsumptionTaxWithoutCalc' . $valueStr] = $classEscape->joinCommaArray(array('arr' => $varsCollect['consumptionTaxWithoutCalc' . $valueStr]));
			$data['arrCommaTaxPayment' . $valueStr] = $classEscape->joinCommaArray(array('arr' => $varsCollect['arrCommaTaxPayment' . $valueStr]));
			$data['arrCommaTaxReceipt' . $valueStr] = $classEscape->joinCommaArray(array('arr' => $varsCollect['arrCommaTaxReceipt' . $valueStr]));
		}

		return $data;
	}

	/**
		 (array(
			'arrDetail'  => $arr['arrValue']['jsonDetail']['varsDetail'],
			'varsDetail' => $arr['varsFSItem']['varsJournalRequest']['varsDetail'],
		 ))
	 */
	protected function _updateVarsOmit($arr)
	{
		$arrDebit = array();
		$arrCredit = array();
		$arrDetail = array();
		$array = $arr['arrDetail'];

		foreach ($array as $key => $value) {
			if ($value['arrDebit']['idAccountTitle'] != '') {
				$arrDebit[] = $value['arrDebit'];
			}
			if ($value['arrCredit']['idAccountTitle'] != '') {
				$arrCredit[] = $value['arrCredit'];
			}
		}

		foreach ($array as $key => $value) {
			$varsDetail = $arr['varsDetail'];
			if ($arrDebit[$key]) {
				$varsDetail['arrDebit'] = $arrDebit[$key];
			}
			if ($arrCredit[$key]) {
				$varsDetail['arrCredit'] = $arrCredit[$key];
			}
			if ($varsDetail['arrDebit']['idAccountTitle'] == ''
				&& $varsDetail['arrCredit']['idAccountTitle'] == ''
			) {
				continue;
			}
			$arrDetail[] = $varsDetail;
		}

		return $arrDetail;
	}

	/**

	 */
	protected function _getDbLogArrIdAccountPermit($arr)
	{
		global $classEscape;

		$array = $classEscape->splitCommaArrayData(array('data' => $arr['arrValue']['arrCommaIdAccountPermit']));
		$arrayNew = array();
		foreach ($array as $key => $value) {
			$data = array(
				'stampRegister' => 0,
				'idAccount'     => $value,
				'flagPermit'    => 'none',
			);
			$arrayNew[] = $data;
		}

		return $arrayNew;
	}

	/**

	 */
	protected function _getDbLogArrCommaIdAccountPermit($arrPermitHistory)
	{
		global $classEscape;

		$array = $arrPermitHistory;
		$arrayCheck = array();
		foreach ($array as $key => $value) {
			$arrayPermit = $value['arrIdAccountPermit'];
			foreach ($arrayPermit as $keyPermit => $valuePermit) {
				$arrayCheck[$valuePermit['idAccount']] = 1;
			}
		}

		$arrayNew = array();
		$array = $arrayCheck;
		foreach ($array as $key => $value) {
			$arrayNew[] = $key;
		}

		$str = $classEscape->joinCommaArray(array('arr' => $arrayNew));

		return $str;
	}
}
