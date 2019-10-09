<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_OrderLog extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extChildSelf = array(

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
						'numConsumptionTax' => int,
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

		$varsItem = $this->_getVarsItem(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $arr['idEntity'],
		));

		$array = $arr['arrOrder'];
		foreach ($array as $key => $value) {
			$flag = $this->_checkValueDetail($value, $varsItem);
			if ($flag) {
				return $flag;
			}
		}

		$arrVarsLog = array();
		foreach ($array as $key => $value) {
			$varsLog = $this->_setDbLog($value, $varsItem);
			if ($varsLog) {
				$arrVarsLog[] = $varsLog;
			}
		}
		if ($arrVarsLog) {
			$flag = $this->_setDbLogCalc($arr, $arrVarsLog);
			if ($flag == 'errorDataMax') {
				return $flag;
			}
		}
		$this->_updateDbPreferenceStamp(array('strColumn' => 'log'));
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
		);

		return $data;
	}

	/**

	 */
	protected function _checkValueDetail($arr, $varsItem)
	{
		$flag = $this->_checkValueDetailStampBook($arr, $varsItem);
		if ($flag) {
			return $flag;
		}
		$flag = $this->_checkValueDetailJournal($arr, $varsItem);
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
	protected function _checkValueDetailStampBook($arr, $varsItem)
	{
		global $varsAccount;

		$numTimeZone = PLUGIN_ACCOUNTING_NUM_TIME_ZONE;
		$flagFiscalReport = $arr['flagFiscalReport'];

		if ($flagFiscalReport == 'none') {
			$data = $this->_getNumFiscalTermStamp($arr, $varsItem);
			$stampBook = $arr['stampBook'];
			$stampMin = $data['stampMin'];
			$stampMax = $data['stampMax'];

			if (!($stampMin <= $stampBook && $stampBook <= $stampMax)) {
				return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;
			}
		}
	}

	/**
	 *
	 */
	protected function _getNumFiscalTermStamp($arr, $varsItem)
	{
		global $varsPluginAccountingEntity;

		$numTimeZone = PLUGIN_ACCOUNTING_NUM_TIME_ZONE;

		$numFiscalPeriod = $arr['numFiscalPeriod'];
		$numFiscalPeriodStart = $varsPluginAccountingEntity[$arr['idEntity']]['numFiscalPeriodStart'];
		$numFiscalBeginningYear = $varsItem['varsEntityNation']['numFiscalBeginningYear'];
		$numCurrentYear = $numFiscalBeginningYear;

		$strTimeZone = (-1 * $numTimeZone) . 'hours';
		$numYear = $numCurrentYear;
		$numMonth = $varsItem['varsEntityNation']['numFiscalBeginningMonth'];
		$dateTime = new DateTime("$numYear-$numMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stampMin = $dateTime->format('U');

		$numEndMonth = $varsItem['varsEntityNation']['numFiscalBeginningMonth'] + $varsItem['varsEntityNation']['numFiscalTermMonth'];
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
	protected function _checkValueDetailJournal($arr, $varsItem)
	{
		global $classEscape;
		global $classCheck;

		$arrNum = array(
			'Debit' => 0,
			'Credit' => 0,
		);

		$flag = 0;
		$array = $arr['jsonDetail']['varsDetail'];
		$flagConsumptionTaxFree = (int) $varsItem['varsEntityNation']['flagConsumptionTaxFree'];
		$flagConsumptionTaxIncluding = (int) $varsItem['varsEntityNation']['flagConsumptionTaxIncluding'];

		foreach ($array as $key => $value) {
			$arrayStr = array('Debit', 'Credit');
			foreach ($arrayStr as $keyStr => $valueStr) {
				$strSide = 'arr' . $valueStr;
				$idAccountTitle = $value[$strSide]['idAccountTitle'];
				if ($idAccountTitle) {
					if (!$varsItem['arrAccountTitle']['arrStrTitle'][$idAccountTitle]) {
						$flag = __LINE__;
						break;
					}

					if ($value[$strSide]['numValue'] <= 0) {
						$flag = __LINE__;
						break;
					}

					if ($value[$strSide]['numValueConsumptionTax'] != '') {
						$flagCheck = $classCheck->checkValueWord(array(
								'flagType' => 'num',
								'value'    => $value[$strSide]['numValueConsumptionTax']
						));
						if ($flagCheck) {
							$flag = __LINE__;
							break;
						}
					}

					$idSubAccountTitle = $value[$strSide]['idSubAccountTitle'];
					if ($idSubAccountTitle) {
						if (!$varsItem['arrSubAccountTitle']['arrStrTitle'][$idAccountTitle][$idSubAccountTitle]) {
							$flag = __LINE__;
							break;
						}
					}
					$idDepartment = $value[$strSide]['idDepartment'];
					if ($idDepartment) {
						if (!$varsItem['arrDepartment']['arrStrTitle'][$idDepartment]) {
							$flag = __LINE__;
							break;
						}
					}
					if ($flagConsumptionTaxFree != $value[$strSide]['flagConsumptionTaxFree']) {
						$flag = __LINE__;
						break;
					}
					if ($flagConsumptionTaxIncluding != $value[$strSide]['flagConsumptionTaxIncluding']) {
						$flag = __LINE__;
						break;
					}

					$arrNum[$valueStr] += $value[$strSide]['numValue'];

					$flagTax = 0;

					$flagConsumptionTaxGeneralRuleEach = $value[$strSide]['flagConsumptionTaxGeneralRuleEach'];
					if ($flagConsumptionTaxGeneralRuleEach) {
						if (!$varsItem['varsConsumptionTax']['arrStrGeneralEach'][$flagConsumptionTaxGeneralRuleEach]) {
							$flag = __LINE__;
							break;
						}
						if ((int) $varsItem['varsEntityNation']['flagConsumptionTaxGeneralRule']
							&& (int) $varsItem['varsEntityNation']['flagConsumptionTaxDeducted']
							&& preg_match( "/^tax/", $flagConsumptionTaxGeneralRuleEach)
						) {
							$flagTax = 1;
						}
					}

					$flagConsumptionTaxGeneralRuleProration = $value[$strSide]['flagConsumptionTaxGeneralRuleProration'];
					if ($flagConsumptionTaxGeneralRuleProration) {
						if (!$varsItem['varsConsumptionTax']['arrStrGeneralProration'][$flagConsumptionTaxGeneralRuleProration]) {
							$flag = __LINE__;
							break;
						}
						if ((int) $varsItem['varsEntityNation']['flagConsumptionTaxGeneralRule']
							&& !(int) $varsItem['varsEntityNation']['flagConsumptionTaxDeducted']
							&& preg_match( "/^tax/", $flagConsumptionTaxGeneralRuleProration)
						) {
							$flagTax = 1;
						}
					}

					$flagConsumptionTaxSimpleRule = $value[$strSide]['flagConsumptionTaxSimpleRule'];
					if ($flagConsumptionTaxSimpleRule) {
						if (!$varsItem['varsConsumptionTax']['arrStrSimple'][$flagConsumptionTaxSimpleRule]) {
							$flag = __LINE__;
							break;
						}
						if (!(int) $varsItem['varsEntityNation']['flagConsumptionTaxGeneralRule'] &&
							preg_match( "/^tax/", $value[$strSide]['flagConsumptionTaxSimpleRule'])
						) {
							$flagTax = 1;
						}
					}

					if ($idAccountTitle == 'suspenseReceiptOfConsumptionTaxes' || $idAccountTitle == 'suspensePaymentConsumptionTaxes') {
						$flagTax = 0;
					}

					if ($flagConsumptionTaxFree
						&& ($idAccountTitle == 'suspenseReceiptOfConsumptionTaxes' || $idAccountTitle == 'suspensePaymentConsumptionTaxes')
					) {
						$flag = __LINE__;
						break;
					}

					$flagConsumptionTaxWithoutCalc = $value[$strSide]['flagConsumptionTaxWithoutCalc'];
					if ($flagConsumptionTaxWithoutCalc) {
						if (!($flagConsumptionTaxWithoutCalc == 1
							|| $flagConsumptionTaxWithoutCalc == 2
							|| $flagConsumptionTaxWithoutCalc == 3
						)) {
							$flag = __LINE__;
							break;
						}
					}

					$flagConsumptionTaxCalc = $value[$strSide]['flagConsumptionTaxCalc'];
					if ($flagConsumptionTaxCalc) {
						if (!($flagConsumptionTaxCalc == 1
							|| $flagConsumptionTaxCalc == 2
							|| $flagConsumptionTaxCalc == 3
						)) {
							$flag = __LINE__;
							break;
						}
					}

					if ($flagTax) {
						if ($flagConsumptionTaxWithoutCalc == 2) {
							$numValue = $value[$strSide]['numValue'];
							$numValueConsumptionTax = $value[$strSide]['numValueConsumptionTax'];
							if ($numValue < $numValueConsumptionTax) {
								$flag = __LINE__;
								break;
							}
							$arrNum[$valueStr] += $numValueConsumptionTax;
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
			return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;
		}

		if ($arr['jsonDetail']['numSumDebit'] != $arr['jsonDetail']['numSumCredit']
			|| $arr['jsonDetail']['numSumDebit'] != $arrNum['Debit']
			|| $arr['jsonDetail']['numSumCredit'] != $arrNum['Credit']
			|| $arr['jsonDetail']['numSumCredit'] > 99999999999
			|| count($array) > 10
		) {
			$flag = __LINE__;
		}

		if ($flag) {
			return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;
		}
	}

	/**

	 */
	protected function _checkValueDetailPermit($arr)
	{
		global $classEscape;

		global $varsPluginAccountingAccounts;
		global $varsPluginAccountingAccountsEntity;

		$data = $arr['arrCommaIdAccountPermit'];
		$arrayCheck = array();
		$array = $classEscape->splitCommaArray(array('data' => $data));
		foreach ($array as $key => $value) {
			if (!$varsPluginAccountingAccountsEntity[$value][$arr['idEntity']]) {
				return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;
			}

			$varsAuthority = $this->_getVarsAuthority(array(
				'idAccount' => $value,
				'idEntity'  => $arr['idEntity'],
			));

			if ($varsAuthority == 'admin') {
				$arrayCheck[$value] = 1;
				continue;
			}

			$str = ',' . $arr['idEntity'] . ',';
			$arrCommaIdEntity = $varsPluginAccountingAccounts[$value]['arrCommaIdEntity'];

			if (!preg_match("/$str/", $arrCommaIdEntity)) {
				return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;

			} else {
				if (!$varsAuthority['flagAllUpdate']) {
					return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;
				}
			}
			$arrayCheck[$value] = 1;
		}

		if ($arrayCheck[$arr['idAccount']]) {
			return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;
		}

		if (count($array)) {
			if ((int) $arr['numSumMax'] < 1) {
				return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;
			}
		}
	}

	/**

	 */
	protected function _getVarsAuthority($arr)
	{
		global $varsPluginAccountingAccountsEntity;
		global $varsPluginAccountingAuthority;

		global $classCheck;

		$idAccount = $arr['idAccount'];
		$idEntity = $arr['idEntity'];

		$flagAuthority = $classCheck->checkModuleAuthority(array(
			'idModule'  => 'accounting',
			'idAccount' => $idAccount,
		));

		if ($flagAuthority == 'webmaster' || $flagAuthority == 'admin') {
			return 'admin';

		} elseif ($flagAuthority == 'user') {
			$idAuthority = $varsPluginAccountingAccountsEntity[$idAccount][$idEntity]['idAuthority'];
			$varsAuthority = $varsPluginAccountingAuthority[$idAuthority];

			return $varsAuthority;
		}

		return null;
	}

	/**

	 */
	protected function _checkValueDetailFile($arr)
	{
		global $classEscape;
		global $classDb;
		global $varsPluginAccountingAccount;

		$data = $arr['arrCommaIdLogFile'];
		$array = $classEscape->splitCommaArray(array('data' => $data));

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
						'value'         => $arr['idEntity'],
					),
					array(
						'flagType'      => 'num',
						'strColumn'     => 'numFiscalPeriod',
						'flagCondition' => 'eqBig',
						'value'         => $arr['numFiscalPeriod'],
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
				return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;
			}
		}
	}

	protected function _setDbLog($arr, $varsItem)
	{
		global $classEscape;
		global $classDb;
		$dbh = $classDb->getHandle();

		$stampRegister = TIMESTAMP;
		$stampUpdate = TIMESTAMP;

		$stampBook = $arr['stampBook'];

		$idEntity = $arr['idEntity'];
		$numFiscalPeriod = $arr['numFiscalPeriod'];

		$idAccount = $arr['idAccount'];

		$flagFiscalReport = $arr['flagFiscalReport'];
		if ($flagFiscalReport == 'none') {
			$flagFiscalReport = 0;
		}

		$strTitle = $arr['strTitle'];

		$arrSpaceStrTag = $classEscape->splitSpaceArray(array('data' => $arr['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));

		$flagApply = 1;
		$idAccountApply = $idAccount;
		$flagApplyBack = 0;
		$array = $classEscape->splitCommaArray(array('data' => $arr['arrCommaIdAccountPermit']));
		if (!count($array)) {
			$flagApply = 0;
			$arr['numSumMax'] = 0;
			$idAccountApply = null;
		}

		$arrCommaIdLogFile = $arr['arrCommaIdLogFile'];

		$varsVersion = $this->_getDbLogVarsVersion($arr, $varsItem);
		$jsonVersion = $varsVersion['jsonVersion'];

		$numValue = $varsVersion['numValue'];
		$arrCommaIdDepartment = $varsVersion['arrCommaIdDepartment'];
		$arrCommaIdAccountTitle = $varsVersion['arrCommaIdAccountTitle'];
		$arrCommaIdSubAccountTitle = $varsVersion['arrCommaIdSubAccountTitle'];

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
				'numSumMax'          => (int) $arr['numSumMax'],
				'idAccountApply'     => $idAccount,
				'arrIdAccountPermit' => $this->_getDbLogArrIdAccountPermit($arr),
			),
		);
		if (!$flagApply) {
			$arrPermitHistory = array();
		}
		$jsonPermitHistory = json_encode($arrPermitHistory);
		$arrCommaIdAccountPermit = $this->_getDbLogArrCommaIdAccountPermit($arrPermitHistory);

		$varsIdNumber = $this->_getIdAutoIncrement(array(
			'idTarget' => 'idLog'
		));
		if (!$varsIdNumber[$idEntity][$numFiscalPeriod]) {
			$varsIdNumber[$idEntity][$numFiscalPeriod] = 1;
		}
		$idLog = $varsIdNumber[$idEntity][$numFiscalPeriod];

		$arrColumn = array('stampRegister', 'stampUpdate', 'stampBook', 'idLog', 'idEntity', 'numFiscalPeriod', 'idAccount', 'flagFiscalReport', 'strTitle', 'arrSpaceStrTag', 'flagApply', 'idAccountApply', 'flagApplyBack', 'arrCommaIdAccountPermit', 'arrCommaIdLogFile', 'jsonVersion', 'numValue', 'arrCommaIdDepartment', 'arrCommaIdAccountTitle', 'arrCommaIdSubAccountTitle', 'arrCommaIdDepartmentVersion', 'arrCommaIdAccountTitleVersion', 'arrCommaIdSubAccountTitleVersion', 'jsonChargeHistory', 'jsonPermitHistory');
		$arrValue = array($stampRegister, $stampUpdate, $stampBook, $idLog, $idEntity, $numFiscalPeriod, $idAccount, $flagFiscalReport, $strTitle, $arrSpaceStrTag, $flagApply, $idAccountApply, $flagApplyBack, $arrCommaIdAccountPermit, $arrCommaIdLogFile, $jsonVersion, $numValue, $arrCommaIdDepartment, $arrCommaIdAccountTitle, $arrCommaIdSubAccountTitle, $arrCommaIdDepartmentVersion, $arrCommaIdAccountTitleVersion, $arrCommaIdSubAccountTitleVersion, $jsonChargeHistory, $jsonPermitHistory);

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

		if ($flagApply) {
			return;
		}

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

		return $varsLog;

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

	 */
	protected function _getDbLogVarsVersion($arr, $varsItem)
	{
		global $classEscape;

		$arrSpaceStrTag = $classEscape->splitSpaceArray(array('data' => $arr['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));

		$varsEntityNation = $varsItem['varsEntityNation'];

		$varsDetail = $arr['jsonDetail']['varsDetail'];
		$varsVersion = $this->_getDbLogVarsVersionDetail($arr, $varsItem);

		$arrVersion = array(
			array(
				'stampRegister'     => TIMESTAMP,
				'stampUpdate'       => TIMESTAMP,
				'stampBook'         => $arr['stampBook'],
				'strTitle'          => $arr['strTitle'],
				'flagFiscalReport'  => $arr['flagFiscalReport'],
				'arrSpaceStrTag'    => $arrSpaceStrTag,
				'arrCommaIdLogFile' => $arr['arrCommaIdLogFile'],
				'jsonDetail' => array(
					'idAccountTitleDebit'  => $varsVersion['idAccountTitleDebit'],
					'idAccountTitleCredit' => $varsVersion['idAccountTitleCredit'],
					'numSum'               => $arr['jsonDetail']['numSumDebit'],
					'numSumDebit'          => $arr['jsonDetail']['numSumDebit'],
					'numSumCredit'         => $arr['jsonDetail']['numSumCredit'],
					'varsEntityNation' => array(
						'numConsumptionTax'              => $varsEntityNation['numConsumptionTax'],
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
					'numVersionConsumptionTax' => count($varsVersion['varsDetail']) - 1,
				),
			),
		);

		$data = array(
			'idAccountTitleDebit'       => $varsVersion['idAccountTitleDebit'],
			'idAccountTitleCredit'      => $varsVersion['idAccountTitleCredit'],
			'jsonVersion'               => json_encode($arrVersion),
			'arrVersion'                => $arrVersion,
			'varsDetail'                => $varsVersion['varsDetail'],
			'numValue'                  => $arr['jsonDetail']['numSumDebit'],
			'arrCommaIdDepartment'      => $varsVersion['arrCommaIdDepartment'],
			'arrCommaIdAccountTitle'    => $varsVersion['arrCommaIdAccountTitle'],
			'arrCommaIdSubAccountTitle' => $varsVersion['arrCommaIdSubAccountTitle'],
		);

		return $data;
	}

	/**

	 */
	protected function _getDbLogVarsVersionDetail($arr, $varsItem)
	{
		global $classEscape;

		$varsEntityNation = $varsItem['varsEntityNation'];

		$arrayIdDepartment = array();
		$arrayIdAccountTitle = array();
		$arrayIdSubAccountTitle = array();
		$arrayDetail = array();
		$arraySide = array(
			'arrDebit' => array(),
			'arrCredit' => array(),
		);
		$array = $this->_updateVarsOmit(array(
			'arrDetail' => $arr['jsonDetail']['varsDetail'],
		));

		$num = 0;
		foreach ($array as $key => $value) {
			$arrayNew = array();
			$arrayNew['id'] = $num;
			$arrayNew['flagFoldNow'] = 0;
			$arrayStr = array('Debit', 'Credit');
			foreach ($arrayStr as $keyStr => $valueStr) {
				$strSide = 'arr' . $valueStr;
				$idAccountTitle = ($value[$strSide]['idAccountTitle'])? $value[$strSide]['idAccountTitle'] : '';
				$numValue = '';
				$numValueConsumptionTax = '';
				$idDepartment = '';
				$idSubAccountTitle = '';
				$flagConsumptionTaxGeneralRuleProration = '';
				$flagConsumptionTaxGeneralRuleEach = '';
				$flagConsumptionTaxSimpleRule = '';
				$flagConsumptionTaxWithoutCalc = '';
				$flagConsumptionTaxCalc = '';
				$flagConsumptionTaxIncluding = '';
				$flagConsumptionTaxFree = '';
				if ($idAccountTitle) {
					$arraySide[$strSide][] = $idAccountTitle;

					$numValue = $value[$strSide]['numValue'];
					$numValueConsumptionTax = $value[$strSide]['numValueConsumptionTax'];
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
								}

							} else {
								if (preg_match("/^tax/", $flagConsumptionTaxGeneralRuleProration)) {
									$flagTax = 1;
								}
							}
						}

						if (preg_match("/^tax/", $flagConsumptionTaxSimpleRule)
							&& !(int) $varsEntityNation['flagConsumptionTaxGeneralRule']
						) {
							$flagTax = 1;
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

				$arrayNew[$strSide] = array(
					'idAccountTitle'                         => $idAccountTitle,
					'numValue'                               => $numValue,
					'numValueConsumptionTax'                 => $numValueConsumptionTax,
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
					$arrayIdDepartment[] = $idDepartment;
				}
				if ($idAccountTitle) {
					$arrayIdAccountTitle[] = $idAccountTitle;
				}
				if ($idSubAccountTitle) {
					$arrayIdSubAccountTitle[] = $idSubAccountTitle;
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

		return $data;
	}

	/**

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
			$varsDetail = array(
				'id' => '',
				'arrDebit' => array(
					'idAccountTitle' => '',
					'numValue' => '',
					'numValueConsumptionTax' => '',
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
			);
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

		$array = $classEscape->splitCommaArray(array('data' => $arr['arrCommaIdAccountPermit']));
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
