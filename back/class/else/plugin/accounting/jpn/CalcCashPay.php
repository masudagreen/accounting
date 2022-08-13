<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcCashPay extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extChildSelf = array(
		'varsItem' => array(),
		'pathItem' => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/cash.php',
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
			'flagStatus'      => 'check',
			'arrValue'        => $valueLog,
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'classCalcLog'    => $classCalcLog
		))
	 */
	protected function _iniCheck($arr)
	{
		$varsItem = $this->_extChildSelf['varsItem'][$arr['idEntity']][$arr['numFiscalPeriod']];
		if (!$varsItem) {
			$varsItem = $this->_getVarsItem(array(
				'idEntity'        => $arr['idEntity'],
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
			));
			$this->_extChildSelf['varsItem'][$arr['idEntity']][$arr['numFiscalPeriod']] = $varsItem;
		}

		$data = array(
			'flag'        => '',
			'varsLog'     => array(),
			'arrRowsCash' => array(),
		);

		if (!$varsItem['varsPreference']['flagAutoImport']) {
			return $data;
		}

		$arrVarsLog = $this->_getVarsDbLog(array(
			'arrValue'        => $arr['arrValue'],
			'varsItem'        => $varsItem,
			'idEntity'        => $arr['idEntity'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'classCalcLog'    => $arr['classCalcLog'],
		));

		$rows = $this->_checkLogCash(array(
			'arrVarsLog' => $arrVarsLog,
		));

		if ($rows['numRows'] == 1) {
			if ($varsItem['varsPreference']['flagPermitImport']) {
				$flag = $this->_checkPermitLost(array(
					'value'        => $rows['arrRows'][0],
					'classCalcLog' => $arr['classCalcLog'],
				));
				if ($flag) {
					return $data;
				}
			}
		}

		if ($rows['numRows'] >= 2) {
			$data['flag'] = 'caution';
			$data['varsLog'] = $arrVarsLog['arrDefer'];
			$data['arrRowsCash'] = $rows['arrRows'];

		} else if ($rows['numRows'] == 1) {
			$data['flag'] = 'pay';
			$data['varsLog'] = $arrVarsLog['arrDefer'];
			$data['arrRowsCash'] = $rows['arrRows'];

		} else if ($arrVarsLog['arrCash']) {
			$data['flag'] = 'paid';
			$data['varsLog'] = $arrVarsLog['arrDefer'];
			$data['arrRowsCash'] = array($arrVarsLog['arrCash']);
		}

		return $data;
	}

	/**
		(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $arr['idEntity'],
		))
	 */
	protected function _getVarsItem($arr)
	{
		$varsPreference = $this->_getVarsPreference(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $arr['idEntity'],
		));

		$varsCashItem = $this->_getVarsCashItem();

		$data = array(
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'idEntity'         => $arr['idEntity'],
			'varsPreference'   => $varsPreference,
			'varsCashItem'     => $varsCashItem,
		);

		return $data;
	}

	/**

	 */
	protected function _getVarsCashItem()
	{
		$vars = $this->getVars(array(
			'path' => $this->_extChildSelf['pathItem'],
		));

		return $vars;
	}

	/**
		(array(
		))
	 */
	protected function _getVarsPreference($arr)
	{
		global $classDb;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingCash',
			'arrLimit' => array(),
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
					'flagCondition' => 'eq',
					'value'         => $arr['numFiscalPeriod'],
				),
			),
		));

		return $rows['arrRows'][0];
	}

	/**
	 (array(
		'arrValue'        => $arr['arrValue'],
		'idEntity'        => $arr['idEntity'],
		'numFiscalPeriod' => $arr['numFiscalPeriod'],
	 ))
	 */
	protected function _getVarsDbLog($arr)
	{
		global $classEscape;

		$arrValue = $arr['arrValue'];

		$stampRegister = TIMESTAMP;
		$stampArrive = ($arrValue['stampArrive'])? $arrValue['stampArrive'] : null;
		$stampUpdate = TIMESTAMP;
		$stampBook = $arrValue['stampBook'];
		$idEntity = $arr['idEntity'];
		$numFiscalPeriod = (int) $arr['numFiscalPeriod'];
		$idAccount = $arrValue['idAccount'];
		$flagFiscalReport = $arrValue['flagFiscalReport'];
		$strTitle = $arrValue['strTitle'];

		$flagType = $arrValue['flagType'];
		$numRow = $arrValue['numRow'];

		$arrSpaceStrTag = $arrValue['arrSpaceStrTag'];
		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrSpaceStrTag));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));
		$arrValue['arrSpaceStrTag'] = $arrSpaceStrTag;

		$arrCommaIdLogFile = $arrValue['arrCommaIdLogFile'];
		$arrCommaIdLogFile = $classEscape->splitCommaArrayData(array('data' => $arrCommaIdLogFile));
		$arrCommaIdLogFile = $classEscape->joinCommaArray(array('arr' => $arrCommaIdLogFile));
		$arrValue['arrCommaIdLogFile'] = $arrCommaIdLogFile;

		$flagApply = ($arrValue['flagApply'])? $arrValue['flagApply'] : 0;
		$idAccountApply = ($arrValue['idAccountApply'])? $arrValue['idAccountApply'] : null;
		$arrCommaIdAccountPermit = ($arrValue['arrCommaIdAccountPermit'])? $arrValue['arrCommaIdAccountPermit'] : '';
		$jsonPermitHistory = ($arrValue['jsonPermitHistory'])? json_encode($arrValue['jsonPermitHistory']) : json_encode(array());

		$classCalcLog = &$arr['classCalcLog'];
		if (!$classCalcLog) {
			$classCalcLog = $this->_getClassCalc(array('flagType' => 'Log'));
		}
		$varsVersion = $classCalcLog->allot(array(
			'flagStatus'      => 'varsVersion',
			'arrValue'        => $arrValue,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
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

		$data = array(
			'arrDefer' => array(),
			'arrCheck' => array(),
			'arrCash' => array(),
		);

		$flagVarsCash = $this->_checkFlagVarsCash(array(
			'varsVersion' => $varsVersion,
			'varsItem'    => $arr['varsItem'],
		));

		$data['arrDefer'] = compact(
			'stampRegister',
			'stampUpdate',
			'stampArrive',
			'stampBook',
			'flagType',
			'numRow',
			'idEntity',
			'numFiscalPeriod',
			'idAccount',
			'flagFiscalReport',
			'strTitle',
			'arrSpaceStrTag',
			'flagApply',
			'idAccountApply',
			'arrCommaIdAccountPermit',
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

		if ($flagVarsCash['flagCash']) {
			$idLogCash = 'dummy';
			$flagIn = $flagVarsCash['flagIn'];
			$flagPay = 1;
			$stampPay = TIMESTAMP;
			$arrVersion = &$varsVersion['arrVersion'][0];
			$arrVersion['flagIn'] = $flagIn;
			$arrPermitHistory = ($arrValue['jsonPermitHistory'])? $arrValue['jsonPermitHistory'] : array();
			$arrVersion['jsonPermitHistory'] = $arrPermitHistory;
			$jsonVersion = json_encode($varsVersion['arrVersion']);
			$data['arrCash'] = compact(
				'stampRegister',
				'stampUpdate',
				'stampBook',
				'idLogCash',
				'idEntity',
				'numFiscalPeriod',
				'idAccount',
				'flagIn',
				'flagPay',
				'stampPay',
				'strTitle',
				'arrSpaceStrTag',
				'flagApply',
				'idAccountApply',
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
				'jsonChargeHistory',
				'jsonPermitHistory'
			);
		}

		$flagPay = 0;
		$flagRemove = 0;
		$data['arrCheck'] = compact(
			'stampBook',
			'idEntity',
			'numFiscalPeriod',
			'flagPay',
			'flagRemove',
			'numValue',
			'arrCommaIdDepartmentDebit',
			'arrCommaIdAccountTitleDebit',
			'arrCommaIdSubAccountTitleDebit',
			'arrCommaConsumptionTaxDebit',
			'arrCommaRateConsumptionTaxDebit',
			'arrCommaConsumptionTaxWithoutCalcDebit',
			'arrCommaIdDepartmentCredit',
			'arrCommaIdAccountTitleCredit',
			'arrCommaIdSubAccountTitleCredit',
			'arrCommaConsumptionTaxCredit',
			'arrCommaRateConsumptionTaxCredit',
			'arrCommaConsumptionTaxWithoutCalcCredit'
		);

		return $data;
	}

	/**
		(array(
			'varsVersion' => $varsVersion,
			'varsItem'    => $arr['varsItem'],
		))
	 */
	protected function _checkFlagVarsCash($arr)
	{
		$data = array(
			'flagCash' => 0,
			'flagIn'   => 0,
		);

		$varsVersionLast = reset($arr['varsVersion']['arrVersion']);
		$varsDetail = reset($varsVersionLast['jsonDetail']['varsDetail']);

		$idAccountTitleDebit = $varsDetail['arrDebit']['idAccountTitle'];
		$flagCashDebit = 0;
		if ($arr['varsItem']['varsPreference']['jsonCash'][$idAccountTitleDebit]) {
			$flagCashDebit = 1;
		}

		$idAccountTitleCredit = $varsDetail['arrCredit']['idAccountTitle'];
		$flagCashCredit = 0;
		if ($arr['varsItem']['varsPreference']['jsonCash'][$idAccountTitleCredit]) {
			$flagCashCredit = 1;
		}

		if (!(!$flagCashDebit && !$flagCashCredit)) {
			$data['flagCash'] = 1;
			if ($flagCashDebit && $flagCashCredit) {
				$data['flagIn'] = 2;

			} elseif ($flagCashDebit) {
				$data['flagIn'] = 1;

			} elseif ($flagCashCredit) {
				$data['flagIn'] = 0;
			}
		}

		return $data;
	}

	/**
		(array(
			'classCalcLog' => $classCalcLog,
			'value'        => $value,
		))
	 */
	protected function _checkPermitLost($arr)
	{
		$value = $arr['value'];
		$classCalcLog = &$arr['classCalcLog'];

		$varsPermitHistory = end($value['jsonPermitHistory']);
		$varsOrder = array(
			'numFiscalPeriod'         => $value['numFiscalPeriod'],
			'idEntity'                => $value['idEntity'],
			'idAccount'               => $value['idAccount'],
			'idAccountApply'          => $value['idAccountApply'],
			'flagFiscalReport'        => 'none',
			'stampBook'               => '',
			'strTitle'                => '',
			'jsonDetail'              => '',
			'arrCommaIdLogFile'       => '',
			'arrCommaIdAccountPermit' => $value['arrCommaIdAccountPermit'],
			'numSumMax'               => $varsPermitHistory['numSumMax'],
			'arrSpaceStrTag'          => '',
		);

		$flag = $classCalcLog->allot(array(
			'flagStatus'      => 'check',
			'varsOrder'       => $varsOrder,
			'idEntity'        => $value['idEntity'],
			'numFiscalPeriod' => $value['numFiscalPeriod'],
			'flagCheck'       => 'Permit',
			'varsItem'        => array('dummy'),
		));

		return ($flag)? 1 : 0;
	}

	/**
		(array(

		))
	 */
	protected function _checkLogCash($arr)
	{
		global $classDb;

		$arrWhere = array();
		$array = $arr['arrVarsLog']['arrCheck'];
		foreach ($array as $key => $value) {
			$arrWhere[] = array(
				'flagType'      => '',
				'strColumn'     => $key,
				'flagCondition' => 'eq',
				'value'         => $value,
			);
		}

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogCash',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'   => 1,
			'arrWhere'  => $arrWhere,
		));

		return $rows;
	}



	/**
		(array(
			'flagStatus'      => $flagCashVars['flag'],
			'varsLog'         => $flagCashVars['varsLog'],
			'arrRowsCash'     => $flagCashVars['arrRowsCash'],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod' => $numFiscalPeriodTemp,
		))
	 */
	protected function _iniCaution($arr)
	{
		$this->_insertCautionDb(array(
			'varsLog'         => $arr['varsLog'],
			'idEntity'        => $arr['idEntity'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
	}

	/**
	 (array(
		'varsLog'         => $arr['varsLog'],
		'idEntity'        => $arr['idEntity'],
		'numFiscalPeriod' => $arr['numFiscalPeriod'],
	 ))
	 */
	protected function _insertCautionDb($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$arrayTemp = $arr['varsLog'];
		$arrColumn = array();
		$arrValue = array();
		foreach ($arrayTemp as $keyTemp => $valueTemp) {
			$arrColumn[] = $keyTemp;
			$arrValue[] = $valueTemp;
		}

		$id = $classDb->insertRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingLogCashDefer',
			'arrColumn' => $arrColumn,
			'arrValue'  => $arrValue,
		));

		$this->_updateDbPreferenceStamp(array('strColumn' => 'logCashDefer'));
	}

	/**
		(array(
			'flagStatus'      => $flagCashVars['flag'],
			'varsLog'         => $flagCashVars['varsLog'],
			'arrRowsCash'     => $flagCashVars['arrRowsCash'],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'classCalcLog'    => $classCalcLog,
			'numTimeZone'     => $arr['numTimeZone'],
		))
	 */
	protected function _iniPay($arr)
	{
		$varsItem = $this->_extChildSelf['varsItem'][$arr['idEntity']][$arr['numFiscalPeriod']];
		if (!$varsItem) {
			$varsItem = $this->_getVarsItem(array(
				'idEntity'        => $arr['idEntity'],
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
			));
			$this->_extChildSelf['varsItem'][$arr['idEntity']][$arr['numFiscalPeriod']] = $varsItem;
		}

		$tempData = $this->_checkPay(array(
			'arrValue' => $arr['arrValue'],
			'arrRows'  => $arr['arrRowsCash'],
			'varsItem' => $varsItem,
		));

		$classCalcCash = &$arr['classCalcCash'];
		$flag = $this->_setPay(array(
			'arrVarsLogAdd'    => $tempData['arrVarsLogAdd'],
			'arrVarsLogDelete' => $tempData['arrVarsLogDelete'],
			'idEntity'         => $arr['idEntity'],
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'classCalcCash'    => $classCalcCash,
		));
		if ($flag) {
			return $flag;
		}

		return $tempData;
	}

	/**
		(array(
			'flagStatus'      => $flagCashVars['flag'],
			'varsLog'         => $flagCashVars['varsLog'],
			'arrRowsCash'     => $flagCashVars['arrRowsCash'],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'classCalcLog'    => $classCalcLog,
			'numTimeZone'     => $arr['numTimeZone'],
		))
	 */
	protected function _iniPaid($arr)
	{
		$varsItem = $this->_extChildSelf['varsItem'][$arr['idEntity']][$arr['numFiscalPeriod']];
		if (!$varsItem) {
			$varsItem = $this->_getVarsItem(array(
				'idEntity'        => $arr['idEntity'],
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
			));
			$this->_extChildSelf['varsItem'][$arr['idEntity']][$arr['numFiscalPeriod']] = $varsItem;
		}

		$tempData = $this->_checkPaid(array(
			'arrValue' => $arr['arrValue'],
			'arrRows'  => $arr['arrRowsCash'],
			'varsItem' => $varsItem,
		));

		$classCalcCash = &$arr['classCalcCash'];
		$flag = $this->_setPaid(array(
			'arrVarsLogAdd'    => $tempData['arrVarsLogAdd'],
			'arrValue'         => &$tempData['arrValue'],
			'idEntity'         => $arr['idEntity'],
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'classCalcCash'    => $classCalcCash,
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}

		$tempData['arrVarsLogAdd'] = $flag;

		return $tempData;
	}

	/**
		(array(
			'varsLog'  => $arr['varsLog'],
			'arrRows'  => $arr['arrRowsCash'],
			'varsItem' => $varsItem,
		))
	 */
	protected function _checkPaid($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $classEscape;

		$arrValue = $arr['arrValue'];

		$arrVarsLogAdd = array();
		$array = $arr['arrRows'];
		foreach ($array as $key => $value) {
			$varsLog = $value;

			$arrSpaceStrTag = $arrValue['arrSpaceStrTag'];
			$strAddTag = $arr['varsItem']['varsCashItem']['strTagTitle'];

			if ($value['flagIn'] == 1) {
				$strAddTag .= ' ' . $arr['varsItem']['varsCashItem']['strTagIn'];

			} elseif ($value['flagIn'] == 2) {
				$strAddTag .= ' ' . $arr['varsItem']['varsCashItem']['strTagMove'];

			} else {
				$strAddTag .= ' ' . $arr['varsItem']['varsCashItem']['strTagOut'];
			}

			if (!$arrSpaceStrTag) {
				$arrSpaceStrTag = $strAddTag;

			} else {
				$arrSpaceStrTag .= ' ' . $strAddTag;
			}
			$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrSpaceStrTag));
			$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));
			$arrValue['arrSpaceStrTag'] = $arrSpaceStrTag;
			$varsLog['arrSpaceStrTag'] = $arrSpaceStrTag;

			$arrVarsLogAdd[] = $varsLog;
		}

		$data = array(
			'arrVarsLogAdd' => $arrVarsLogAdd,
			'arrValue'      => $arrValue,
		);

		return $data;
	}

	/**
		(array(
			'arrVarsLogAdd'    => $tempData['arrVarsLogAdd'],
			'idEntity'         => $arr['idEntity'],
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'classCalcCash'    => $classCalcCash,
		))
	 */
	protected function _setPaid($arr)
	{
		global $classDb;
		global $classEscape;

		$classCalcCash = &$arr['classCalcCash'];

		$arrVarsLogAdd = $arr['arrVarsLogAdd'];
		$arrVarsLog = array();

		$array = $arrVarsLogAdd;
		foreach ($array as $key => $value) {

			$idEntity = $value['idEntity'];
			$numFiscalPeriod = $value['numFiscalPeriod'];

			$varsIdNumber = $this->_getIdAutoIncrement(array(
				'idTarget' => 'idLogCash'
			));
			if (!$varsIdNumber[$idEntity]) {
				$varsIdNumber[$idEntity] = 1;
			}
			$idLogCash = $varsIdNumber[$idEntity];

			$value['idLogCash'] = $idLogCash;
			$arrayTemp = $value;
			$arrColumn = array();
			$arrValue = array();
			foreach ($arrayTemp as $keyTemp => $valueTemp) {
				$arrColumn[] = $keyTemp;
				$arrValue[] = $valueTemp;
			}

			$id = $classDb->insertRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingLogCash',
				'arrColumn' => $arrColumn,
				'arrValue'  => $arrValue,
			));

			$varsIdNumber[$idEntity]++;
			$this->_updateIdAutoIncrement(array(
				'idTarget'   => 'idLogCash',
				'varsTarget' => $varsIdNumber
			));

			$varsLog = $this->_getVarsLog(array(
				'idTarget'        => $idLogCash,
				'numFiscalPeriod' => $numFiscalPeriod,
				'idEntity'        => $idEntity,
			));

			$arrSpaceStrTag = $varsLog['arrSpaceStrTag'] . ' cash:' . $idLogCash;
			$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrSpaceStrTag));
			$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));
			$varsLog['arrSpaceStrTag'] = $arrSpaceStrTag;
			$arr['arrValue']['arrSpaceStrTag'] = $arrSpaceStrTag;

			$arrVarsLog[] = $varsLog;
		}

		$flag = $classCalcCash->allot(array(
			'flagStatus'      => 'addDone',
			'arrRows'         => $arrVarsLog,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $arr['idEntity'],
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}

		$this->_updateDbPreferenceStamp(array('strColumn' => 'cash'));

		return $arrVarsLog;
	}

	/**
		(array(
			'idTarget' => '',
		))
	 */
	protected function _getVarsLog($arr)
	{
		global $classDb;

		$arrWhere = array(
			array(
				'flagType'      => 'num',
				'strColumn'     => 'idEntity',
				'flagCondition' => 'eq',
				'value'         => $arr['idEntity'],
			),
			array(
				'flagType'      => 'num',
				'strColumn'     => 'numFiscalPeriod',
				'flagCondition' => 'eq',
				'value'         => $arr['numFiscalPeriod'],
			),
			array(
				'flagType'      => 'num',
				'strColumn'     => 'idLogCash',
				'flagCondition' => 'eq',
				'value'         => $arr['idTarget'],
			),
		);

		if (!is_null($arr['flagRemove'])) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'flagRemove',
				'flagCondition' => 'eq',
				'value'         => $arr['flagRemove'],
			);
		}

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogCash',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => $arrWhere,
		));

		if ($rows['numRows']) {
			return $rows['arrRows'][0];
		}

		return array();
	}



	/**
		(array(
			'varsLog'  => $arr['varsLog'],
			'arrRows'  => $arr['arrRowsCash'],
			'varsItem' => $varsItem,
		))
	 */
	protected function _checkPay($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $classEscape;

		$arrValue = $arr['arrValue'];

		$arrVarsLogAdd = array();
		$arrVarsLogDelete = array();
		$array = $arr['arrRows'];
		foreach ($array as $key => $value) {
			$varsLog = $value;
			$arrVarsLogDelete[] = $varsLog;

			if ($arr['varsItem']['varsPreference']['flagPermitImport']) {
				$arrValue['idAccount'] = $varsLog['idAccount'];
				$arrValue['arrCommaIdAccountPermit'] = $varsLog['arrCommaIdAccountPermit'];
				$arrValue['idAccountApply'] = $varsLog['idAccountApply'];
				$arrValue['jsonPermitHistory'] = $varsLog['jsonPermitHistory'];
			}

			$arrValue['arrCommaIdLogFile'] = $varsLog['arrCommaIdLogFile'];

			$arrSpaceStrTag = $arrValue['arrSpaceStrTag'];
			$strAddTag = $arr['varsItem']['varsCashItem']['strTagTitle'];

			if ($value['flagIn'] == 1) {
				$strAddTag .= ' ' . $arr['varsItem']['varsCashItem']['strTagIn'];

			} elseif ($value['flagIn'] == 2) {
				$strAddTag .= ' ' . $arr['varsItem']['varsCashItem']['strTagMove'];

			} else {
				$strAddTag .= ' ' . $arr['varsItem']['varsCashItem']['strTagOut'];
			}

			if (!$arrSpaceStrTag) {
				$arrSpaceStrTag = $strAddTag;

			} else {
				$arrSpaceStrTag .= ' ' . $strAddTag;
			}

			$arrSpaceStrTag .= ' cash:' . $value['idLogCash'];

			$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrSpaceStrTag));
			$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));
			$arrValue['arrSpaceStrTag'] = $arrSpaceStrTag;
			$varsLog['arrSpaceStrTag'] = $arrSpaceStrTag;

			$varsLog['flagPay'] = 1;
			$varsLog['stampPay'] = TIMESTAMP;

			$arrVarsLogAdd[] = $varsLog;
		}

		$data = array(
			'arrVarsLogAdd'    => $arrVarsLogAdd,
			'arrVarsLogDelete' => $arrVarsLogDelete,
			'arrValue'         => $arrValue,
		);

		return $data;
	}

	/**
		(array(
			'arrVarsLogAdd'    => $tempData['arrVarsLogAdd'],
			'arrVarsLogDelete' => $tempData['arrVarsLogDelete'],
			'idEntity'         => $arr['idEntity'],
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'classCalcCash'    => $classCalcCash,
		))
	 */
	protected function _setPay($arr)
	{
		global $classDb;

		$classCalcCash = &$arr['classCalcCash'];

		$arrVarsLogAdd = $arr['arrVarsLogAdd'];
		$arrVarsLogDelete = $arr['arrVarsLogDelete'];

		$array = $arrVarsLogAdd;
		foreach ($array as $key => $value) {

			$flagPay = $value['flagPay'];
			$stampPay = $value['stampPay'];

			$arrayTemp = compact(
				'flagPay',
				'stampPay'
			);
			$arrColumn = array();
			$arrValue = array();
			foreach ($arrayTemp as $keyTemp => $valueTemp) {
				$arrColumn[] = $keyTemp;
				$arrValue[] = $valueTemp;
			}

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingLogCash',
				'arrColumn' => $arrColumn,
				'flagAnd'  => 1,
				'arrWhere'  => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idEntity',
						'flagCondition' => 'eq',
						'value'         => $arr['idEntity'],
					),
					array(
						'flagType'      => 'num',
						'strColumn'     => 'numFiscalPeriod',
						'flagCondition' => 'eq',
						'value'         => $arr['numFiscalPeriod'],
					),
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idLogCash',
						'flagCondition' => 'eq',
						'value'         => $value['idLogCash'],
					),
				),
				'arrValue'  => $arrValue,
			));
		}

		$flag = $classCalcCash->allot(array(
			'flagStatus'      => 'addDone',
			'arrRows'         => $arrVarsLogAdd,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $arr['idEntity'],
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}

		$flag = $classCalcCash->allot(array(
			'flagStatus'      => 'deletePre',
			'arrRows'         => $arrVarsLogDelete,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $arr['idEntity'],
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}

		$this->_updateDbPreferenceStamp(array('strColumn' => 'cash'));
	}

	/**
		(array(
			'flagStatus'              => 'WriteHistory',
			'varsLog'                 => $dataLog,
			'varsLogCash'             => $flagVars,
			'idEntity'                => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		))
	 */
	protected function _iniWriteHistory($arr)
	{
		$flag = $this->_setWriteHistory(array(
			'idEntity'        => $arr['idEntity'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'varsLog'         => $arr['varsLog'],
			'varsLogCash'     => $arr['varsLogCash'],
		));
		if ($flag) {
			return $flag;
		}
	}

	/**
		(array(
			'idEntity'        => $arr['idEntity'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'varsLog'         => $arr['varsLog'],
			'varsLogCash'     => $arr['varsLogCash'],
		))
	 */
	protected function _setWriteHistory($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsAccount;

		$arrColumn = array(
			'jsonWriteHistory',
		);

		if (!$arr['varsLogCash']['jsonWriteHistory']) {
			$arrWriteHistory = array();

		} else {
			$arrWriteHistory = $arr['varsLogCash']['jsonWriteHistory'];
		}

		$arrWriteHistory[] = array(
			'stampRegister'   => TIMESTAMP,
			'idAccount'       => $varsAccount['id'],
			'idLog'           => $arr['varsLog']['idLog'],
		);

		$jsonWriteHistory = json_encode($arrWriteHistory);

		$flag = $this->checkTextSize(array(
			'flag'       => 'errorDataMax',
			'str'        => $jsonWriteHistory,
			'flagReturn' => 1,
		));
		if ($flag) {
			return $flag;
		}
		$arrValue = array($jsonWriteHistory);

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable' => 'accountingLogCash',
			'arrColumn' => $arrColumn,
			'flagAnd'  => 1,
			'arrWhere'  => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idEntity',
					'flagCondition' => 'eq',
					'value'         => $arr['idEntity'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'numFiscalPeriod',
					'flagCondition' => 'eq',
					'value'         => $arr['numFiscalPeriod'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idLogCash',
					'flagCondition' => 'eq',
					'value'         => $arr['varsLogCash']['idLogCash'],
				),
			),
			'arrValue'  => $arrValue,
		));
	}
}
