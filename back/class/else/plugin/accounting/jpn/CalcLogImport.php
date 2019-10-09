<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcLogImport extends Code_Else_Plugin_Accounting_Jpn_Jpn
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
			'flagStatus'      => 'check',
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $idEntity,
			'arrOrder'         => array(),
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
			'flagStatus'       => 'UpdateVarsTax',
			'arrRows'          => $arrVarsLogAll,
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'idEntity'         => $arr['idEntity'],
		))
	 */
	protected function _iniUpdateVarsTax($arr)
	{
		$classCalcLogConsumptionTax = $this->_getClassCalc(array('flagType' => 'LogConsumptionTax'));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$arrRows = $classCalcLogConsumptionTax->allot(array(
			'flagStatus'       => 'UpdateVars',
			'arrRows'          => $arr['arrRows'],
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'idEntity'         => $arr['idEntity'],
		));

		$flagErrorVars = $this->_updateDbTax(array(
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'idEntity'         => $arr['idEntity'],
			'arrVarsLog'       => $arrRows,
			'varsEntityNation' => $varsEntityNation,
		));

		if ($flagErrorVars) {
			return $flagErrorVars;
		}
	}

	/**
		(array(
			'flagStatus'       => 'UpdateTax',
			'arrRows'          => $arrVarsLogAll,
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'idEntity'         => $arr['idEntity'],
			'varsEntityNation' => $arr['varsItem']['varsEntityNationUpdate'],
		))
	 */
	protected function _iniUpdateTax($arr)
	{
		$flagErrorVars = $this->_updateDbTax(array(
			'varsItem'         => $arr['varsItem'],
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'idEntity'         => $arr['idEntity'],
			'arrVarsLog'       => $arr['arrRows'],
			'varsEntityNation' => $arr['varsEntityNation'],
		));

		if ($flagErrorVars) {
			return $flagErrorVars;
		}
	}

	/**
		(array(
			'varsItem'         => $arr['varsItem'],
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'idEntity'         => $arr['idEntity'],
			'arrVarsLog'       => $arr['arrRows'],
			'varsEntityNation' => $arr['varsEntityNation'],
		));
	 */
	protected function _updateDbTax($arr)
	{
		global $classDb;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);
		$classCalcLog = $this->_getClassCalc(array('flagType' => 'Log'));
		$flagMax = 0;
		$arrayIdLog = array();

		$array = &$arr['arrVarsLog'];
		foreach ($array as $key => $value) {

			$dataVarsion = end($value['jsonVersion']);
			$tempValue = array(
				'numFiscalPeriod'         => $arr['numFiscalPeriod'],
				'idEntity'                => $arr['idEntity'],
				'idAccount'               => '',
				'flagFiscalReport'        => '',
				'stampBook'               => '',
				'strTitle'                => '',
				'jsonDetail'              => $dataVarsion['jsonDetail'],
				'arrCommaIdLogFile'       => '',
				'arrCommaIdAccountPermit' => '',
				'numSumMax'               => '',
				'arrSpaceStrTag'          => '',
			);

			$varsVersion = $classCalcLog->allot(array(
				'flagStatus'       => 'varsVersion',
				'arrValue'         => $tempValue,
				'numFiscalPeriod'  => $arr['numFiscalPeriod'],
				'varsEntityNation' => $arr['varsEntityNation'],
			));

			$arrCommaConsumptionTaxDebit = $varsVersion['arrCommaConsumptionTaxDebit'];
			$arrCommaRateConsumptionTaxDebit = $varsVersion['arrCommaRateConsumptionTaxDebit'];
			$arrCommaConsumptionTaxWithoutCalcDebit = $varsVersion['arrCommaConsumptionTaxWithoutCalcDebit'];
			$arrCommaTaxPaymentDebit = $varsVersion['arrCommaTaxPaymentDebit'];
			$arrCommaTaxReceiptDebit = $varsVersion['arrCommaTaxReceiptDebit'];

			$arrCommaConsumptionTaxCredit = $varsVersion['arrCommaConsumptionTaxCredit'];
			$arrCommaRateConsumptionTaxCredit = $varsVersion['arrCommaRateConsumptionTaxCredit'];
			$arrCommaConsumptionTaxWithoutCalcCredit = $varsVersion['arrCommaConsumptionTaxWithoutCalcCredit'];
			$arrCommaTaxPaymentCredit = $varsVersion['arrCommaTaxPaymentCredit'];
			$arrCommaTaxReceiptCredit = $varsVersion['arrCommaTaxReceiptCredit'];

			$jsonVersion = json_encode($value['jsonVersion']);
			$stampUpdate = TIMESTAMP;

			$flag = $this->checkTextSize(array(
				'flag'        => 'errorDataMax',
				'str'         => $jsonVersion,
				'flagReturn'  => 1,
			));

			if ($flag) {
				$flagMax = 1;
				$arrayIdLog[] = $value['idLogImport'];
				continue;
			}

			$arrayTemp = compact(
				'stampUpdate',
				'jsonVersion',
				'arrCommaConsumptionTaxDebit',
				'arrCommaRateConsumptionTaxDebit',
				'arrCommaConsumptionTaxWithoutCalcDebit',
				'arrCommaTaxPaymentDebit',
				'arrCommaTaxReceiptDebit',
				'arrCommaConsumptionTaxCredit',
				'arrCommaRateConsumptionTaxCredit',
				'arrCommaConsumptionTaxWithoutCalcCredit',
				'arrCommaTaxPaymentCredit',
				'arrCommaTaxReceiptCredit'
			);
			$arrColumn = array();
			$arrValue = array();
			foreach ($arrayTemp as $keyTemp => $valueTemp) {
				$arrColumn[] = $keyTemp;
				$arrValue[] = $valueTemp;
			}

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingLogImport' . $strNation,
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
						'strColumn'     => 'idLogImport',
						'flagCondition' => 'eq',
						'value'         => $value['idLogImport'],
					),
				),
				'arrValue'  => $arrValue,
			));
		}

		if ($flagMax) {
			$data = array(
				'flag'      => 'textMaxOver',
				'arrIdLog'  => $arrayIdLog,
			);
			return $data;
		}
	}

	/**
		 (array(
			'flagStatus'      => 'insertDbLog',
			'arrValue'        => $valueLog,
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'classCalcLog'    => $classCalcLog
		 ))
	 */
	protected function _iniInsertDbLog($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
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

		$arrSpaceStrTag = $arrValue['arrSpaceStrTag'];
		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrSpaceStrTag));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));
		$arrValue['arrSpaceStrTag'] = $arrSpaceStrTag;

		$arrCommaIdLogFile = $arrValue['arrCommaIdLogFile'];
		$arrCommaIdLogFile = $classEscape->splitCommaArrayData(array('data' => $arrCommaIdLogFile));
		$arrCommaIdLogFile = $classEscape->joinCommaArray(array('arr' => $arrCommaIdLogFile));
		$arrValue['arrCommaIdLogFile'] = $arrCommaIdLogFile;

		$flagApply = ($arrValue['flagApply'])? $arrValue['flagApply'] : 0;
		$flagApplyBack = 0;
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

		$varsIdNumber = $this->_getIdAutoIncrement(array(
			'idTarget' => 'idLog'
		));
		if (!$varsIdNumber[$idEntity][$numFiscalPeriod]) {
			$varsIdNumber[$idEntity][$numFiscalPeriod] = 1;
		}
		$idLog = $varsIdNumber[$idEntity][$numFiscalPeriod];

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
			'arrWhere' => array(
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

	/**
		 (array(
			'flagStatus'          => 'insertDbRetry',
			'flagType'            => 'mail',
			'vars'                => $data['varsRetry'],
			'idEntity'            => $arr['idEntity'],
			'idAccount'           => $value['idAccount'],
			'numFiscalPeriod'     => $arr['numFiscalPeriod'],
			'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
		 ))
	 */
	protected function _iniInsertDbRetry($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $classEscape;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$stampRegister = TIMESTAMP;
		$stampUpdate = TIMESTAMP;
		$flagType = $arr['flagType'];
		$idEntity = $arr['idEntity'];
		$numFiscalPeriod = $arr['numFiscalPeriod'];
		$idAccount = $arr['idAccount'];

		$arrSpaceStrTag = (!is_null($arr['arrSpaceStrTag']))? $arr['arrSpaceStrTag'] : '';
		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrSpaceStrTag));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));

		$jsonData = json_encode($arr['vars']);

		if (preg_match("/null/", $jsonData)) {
			return 'invalid';
		}

		$varsIdNumber = $this->_getIdAutoIncrement(array(
			'idTarget' => 'idLogRetry'
		));
		if (!$varsIdNumber[$idEntity]) {
			$varsIdNumber[$idEntity] = 1;
		}
		$idLogRetry = $varsIdNumber[$idEntity];

		$arrayTemp = compact(
			'stampRegister',
			'stampUpdate',
			'idEntity',
			'numFiscalPeriod',
			'idLogRetry',
			'idAccount',
			'flagType',
			'strTitle',
			'jsonData',
			'arrSpaceStrTag'
		);

		$arrColumn = array();
		$arrValue = array();
		foreach ($arrayTemp as $keyTemp => $valueTemp) {
			$arrColumn[] = $keyTemp;
			$arrValue[] = $valueTemp;
		}

		$id = $classDb->insertRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingLogImportRetry' . $strNation,
			'arrColumn' => $arrColumn,
			'arrValue'  => $arrValue,
		));

		$varsIdNumber[$idEntity]++;
		$this->_updateIdAutoIncrement(array(
			'idTarget'   => 'idLogRetry',
			'varsTarget' => $varsIdNumber
		));

		if ($arr['numFiscalPeriodTemp']) {
			$numFiscalPeriod = $arr['numFiscalPeriodTemp'];
			$arrayTemp = compact(
				'stampRegister',
				'stampUpdate',
				'idEntity',
				'numFiscalPeriod',
				'idLogRetry',
				'idAccount',
				'flagType',
				'strTitle',
				'jsonData',
				'arrSpaceStrTag'
			);
			$arrColumn = array();
			$arrValue = array();
			foreach ($arrayTemp as $keyTemp => $valueTemp) {
				$arrColumn[] = $keyTemp;
				$arrValue[] = $valueTemp;
			}

			$id = $classDb->insertRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingLogImportRetry' . $strNation,
				'arrColumn' => $arrColumn,
				'arrValue'  => $arrValue,
			));
		}
		$this->_updateDbPreferenceStamp(array('strColumn' => 'logImportRetry'));
	}

	/**
		 (array(
			'flagStatus'          => 'updateDbRetry',
			'idTarget'            => $idTarget,
			'vars'                => $data['varsRetry'],
			'idEntity'            => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
		 ))
	 */
	protected function _iniUpdateDbRetry($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$stampUpdate = TIMESTAMP;
		$jsonData = json_encode($arr['vars']);

		if (preg_match("/null/", $jsonData)) {
			return 'invalid';
		}

		$arrColumn = array('stampUpdate', 'jsonData');
		$arrValue = array($stampUpdate, $jsonData);

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingLogImportRetry' . $strNation,
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
					'strColumn'     => 'idLogRetry',
					'flagCondition' => 'eq',
					'value'         => $arr['idTarget'],
				),
			),
			'arrValue'  => $arrValue,
		));

		if ($arr['numFiscalPeriodTemp']) {
			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingLogImportRetry' . $strNation,
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
						'value'         => $arr['numFiscalPeriodTemp'],
					),
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idLogRetry',
						'flagCondition' => 'eq',
						'value'         => $arr['idTarget'],
					),
				),
				'arrValue'  => $arrValue,
			));
		}

		$this->_updateDbPreferenceStamp(array('strColumn' => 'logImportRetry'));
	}

	/**
		 (array(
			'flagStatus'          => 'deleteDbRetry',
			'idTarget'            => $idTarget,
			'idEntity'            => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
		 ))
	 */
	protected function _iniDeleteDbRetry($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$classDb->deleteRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingLogImportRetry' . $strNation,
			'flagAnd'   => 1,
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
					'strColumn'     => 'idLogRetry',
					'flagCondition' => 'eq',
					'value'         => $arr['idTarget'],
				),
			),
		));
		if ($arr['numFiscalPeriodTemp']) {
			$classDb->deleteRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingLogImportRetry' . $strNation,
				'flagAnd'   => 1,
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
						'value'         => $arr['numFiscalPeriodTemp'],
					),
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idLogRetry',
						'flagCondition' => 'eq',
						'value'         => $arr['idTarget'],
					),
				),
			));
		}

		$this->_updateDbPreferenceStamp(array('strColumn' => 'logImportRetry'));
	}

	/**
		 (array(
			'flagStatus'      => 'check',
			'arrOrder'        => $arrayCSV,
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idAccount'       => ($varsAccounts[$value['idAccount']])? $value['idAccount'] : $varsAccount['id'],

		 ))
	 */
	protected function _iniCheck($arr)
	{
		$varsItem = $this->_extChildSelf['varsItem'][$arr['idEntity']][$arr['numFiscalPeriod']];
		if (!$varsItem) {
			$varsItem = $this->_getVarsItem(array(
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
				'idEntity'        => $arr['idEntity'],
			));
			$this->_extChildSelf['varsItem'][$arr['idEntity']][$arr['numFiscalPeriod']] = $varsItem;
		}

		$varsItemTemp = array();
		if ($arr['numFiscalPeriodTemp']) {
			$varsItemTemp = $this->_extChildSelf['varsItem'][$arr['idEntity']][$arr['numFiscalPeriodTemp']];
			if (!$varsItemTemp) {
				$varsItemTemp = $this->_getVarsItem(array(
					'numFiscalPeriod' => $arr['numFiscalPeriodTemp'],
					'idEntity'        => $arr['idEntity'],
				));
			}
			$this->_extChildSelf['varsItem'][$arr['idEntity']][$arr['numFiscalPeriodTemp']] = $varsItemTemp;
		}

		$varsCSV = $this->_checkVarsCSVFormat(array(
			'idEntity'            => $arr['idEntity'],
			'numFiscalPeriod'     => $arr['numFiscalPeriod'],
			'numFiscalPeriodTemp' => $arr['numFiscalPeriodTemp'],
			'varsItem'            => $varsItem,
			'varsItemTemp'        => $varsItemTemp,
			'arrayLog'            => $arr['arrOrder'],
			'idAccount'           => $arr['idAccount'],
		));

		return $varsCSV;
	}

	/**
		(array(
			'flag' => '',
			'vars' => $vars,
		))
	 */
	protected function _getVarsItem($arr)
	{
		$varsDepartment = $this->_getVarsDepartment(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

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

		$varsFSItem = $this->_getVarsFSItem();

		$data = array(
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'arrAccountTitle'    => $arrAccountTitle,
			'varsDepartment'     => $varsDepartment,
			'varsEntityNation'   => $varsEntityNation,
			'varsFSItem'         => $varsFSItem,
		);

		$varsImport = $this->_getVarsImport(array(
			'idEntity'        => $arr['idEntity'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'varsItem'        => &$data,
		));

		$data['varsImport'] = $varsImport;

		return $data;
	}

	/**
		(array(

		))
	 */
	protected function _getVarsImport($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogImport' . $strNation,
			'arrLimit' => array(),
			'arrOrder' => array(
				'strColumn' => 'idLogImport',
				'flagDesc'  => 1,
			),
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
			),
		));

		$classCalcLog = $this->_getClassCalc(array('flagType' => 'Log'));
		$arraySide = array('Debit', 'Credit');
		$arrayNew = array();
		$array = $rows['arrRows'];
		foreach ($array as $key => $value) {

			$numVersionEnd = count($value['jsonVersion']) - 1;
			$arrayDetail = $value['jsonVersion'][$numVersionEnd]['jsonDetail']['varsDetail'];

			$flagIdLost = '';
			foreach ($arrayDetail as $keyDetail => $valueDetail) {
				foreach ($arraySide as $keySide => $valueSide) {
					$idAccountTitle = $valueDetail['arr' . $valueSide]['idAccountTitle'];
					$idDepartment = $valueDetail['arr' . $valueSide]['idDepartment'];
					$idSubAccountTitle = $valueDetail['arr' . $valueSide]['idSubAccountTitle'];

					if ($idAccountTitle) {
						$strAccountTitle = $arr['varsItem']['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['strTitleFS'];
						if (!$strAccountTitle) {
							$flagIdLost = 'idAccountTitle';
							break;
						}

						$strSubAccountTitle = $arr['varsItem']['arrSubAccountTitle']['arrStrTitle'][$idAccountTitle][$idSubAccountTitle]['strTitle'];
						if ($idSubAccountTitle && !$strSubAccountTitle) {
							$flagIdLost = 'idSubAccountTitle';
							break;
						}

						$strDepartment = $arr['varsItem']['varsDepartment']['arrStrTitle'][$idDepartment]['strTitle'];
						if ($idDepartment && !$strDepartment) {
							$flagIdLost = 'idDepartment';
							break;
						}
					}
				}
				if ($flagIdLost) {
					break;
				}
			}
			if ($flagIdLost) {
				continue;
			}

			$flagPermitLost = $this->_checkPermitLost(array(
				'classCalcLog' => $classCalcLog,
				'value'        => $value,
			));
			if ($flagPermitLost) {
				continue;
			}

			$arrayNew[] = $value;
		}

		return $arrayNew;
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

		if (!$varsPermitHistory) {
			return 0;
		}

		$varsOrder = array(
			'numFiscalPeriod'         => $value['numFiscalPeriod'],
			'idEntity'                => $value['idEntity'],
			'idAccount'               => '',
			'idAccountApply'          => $value['idAccountApply'],
			'flagFiscalReport'        => '',
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
			'numFiscalPeriod' => $value['numFiscalPeriod'],
			'idEntity'        => $value['idEntity'],
			'flagCheck'       => 'Permit',
			'varsItem'        => array('dummy'),
		));

		return ($flag)? 1 : 0;
	}

	/**
		(array(
			'varsItem'        => $arr['varsItem'],
			'idEntity'        => $arr['idEntity'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _getNumFiscalTermStamp($arr)
	{
		global $varsPluginAccountingEntity;

		$numTimeZone = PLUGIN_ACCOUNTING_NUM_TIME_ZONE;

		$numFiscalPeriod = $arr['numFiscalPeriod'];
		$numFiscalPeriodStart = $varsPluginAccountingEntity[$arr['idEntity']]['numFiscalPeriodStart'];

		$varsEntityNation = $arr['varsItem']['varsEntityNation'];

		$numFiscalBeginningYear = $varsEntityNation['numFiscalBeginningYear'];
		$numCurrentYear = $numFiscalBeginningYear;
		$numCurrentYear2 = $numFiscalBeginningYear;

		$strTimeZone = (-1 * $numTimeZone) . 'hours';
		$numYear = $numCurrentYear;
		$numMonth = $varsEntityNation['numFiscalBeginningMonth'];
		$dateTime = new DateTime("$numYear-$numMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stampMin = $dateTime->format('U');

		$numEndMonth = $varsEntityNation['numFiscalBeginningMonth'] + $varsEntityNation['numFiscalTermMonth'];
		if ($numEndMonth > 12) {
			$numCurrentYear++;
			$numEndMonth -= 12;
		}

		$numEndMonth2 = $varsEntityNation['numFiscalBeginningMonth'] + 6;
		if ($numEndMonth2 > 12) {
			$numCurrentYear2++;
			$numEndMonth2 -= 12;
		}

		$numYear = $numCurrentYear;
		$numMonth = $numEndMonth;
		$dateTime = new DateTime("$numYear-$numMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stampMax = $dateTime->format('U') - 1;

		$numYear = $numCurrentYear2;
		$numMonth = $numEndMonth2;
		$dateTime = new DateTime("$numYear-$numMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stampMax2 = $dateTime->format('U') - 1;

		$data = array(
			'stampMin'  => $stampMin,
			'stampMax'  => $stampMax,
			'stampMax2' => $stampMax2,
		);

		return $data;

	}

	/**
		(array(
			'strBook'  => $stampBook,
		))
	 */
	protected function _getStampBook($arr)
	{
		global $classCheck;

		global $varsAccount;

		$strStamp = $arr['strBook'];
		if (preg_match( "/^([0-9]{4})\/([0-9]{1,2})\/([0-9]{1,2})-([0-9]{1,2}):([0-9]{1,2})$/", $strStamp)) {
			$flag = $classCheck->checkValueFormat(array(
				'flagType' => 'date-time',
				'value'    => $strStamp
			));
			if ($flag) {
				return 0;
			}
			preg_match( "/^([0-9]{4})\/([0-9]{1,2})\/([0-9]{1,2})-([0-9]{1,2}):([0-9]{1,2})$/", $strStamp, $arrMatch);
			list($strStamp, $numYear, $numMonth, $numDate, $numHour, $numMin) = $arrMatch;

		} elseif (preg_match( "/^([0-9]{4})\/([0-9]{1,2})\/([0-9]{1,2})$/", $strStamp)) {
			$flag = $classCheck->checkValueFormat(array(
				'flagType' => 'date',
				'value'    => $strStamp
			));
			if ($flag) {
				return 0;
			}
			preg_match( "/^([0-9]{4})\/([0-9]{1,2})\/([0-9]{1,2})$/", $strStamp, $arrMatch);
			list($strStamp, $numYear, $numMonth, $numDate) = $arrMatch;
			$numHour = 0;
			$numMin = 0;

		} elseif (preg_match( "/^([0-9]{4})\.([0-9]{1,2})\.([0-9]{1,2})$/", $strStamp)) {
			preg_match( "/^([0-9]{4})\.([0-9]{1,2})\.([0-9]{1,2})$/", $strStamp, $arrMatch);
			list($strStamp, $numYear, $numMonth, $numDate) = $arrMatch;
			$numHour = 0;
			$numMin = 0;
			$flag = $classCheck->checkValueFormat(array(
				'flagType' => 'date',
				'value'    => $numYear . '/' . $numMonth . '/' . $numDate
			));
			if ($flag) {
				return 0;
			}

		} elseif (preg_match( "/^([0-9]{2})\.([0-9]{1,2})\.([0-9]{1,2})$/", $strStamp)) {
			preg_match( "/^([0-9]{2})\.([0-9]{1,2})\.([0-9]{1,2})$/", $strStamp, $arrMatch);
			list($strStamp, $numYear, $numMonth, $numDate) = $arrMatch;
			$numYear += 2000;
			$numHour = 0;
			$numMin = 0;
			$flag = $classCheck->checkValueFormat(array(
				'flagType' => 'date',
				'value'    => $numYear . '/' . $numMonth . '/' . $numDate
			));
			if ($flag) {
				return 0;
			}

		} elseif (preg_match( "/^([0-9]{8})$/", $strStamp)) {
			$numYear = substr($strStamp, 0, 4);
			$numMonth = substr($strStamp, 4, 2);
			$numDate = substr($strStamp, 6, 2);
			$numHour = 0;
			$numMin = 0;
			$flag = $classCheck->checkValueFormat(array(
				'flagType' => 'date',
				'value'    => $numYear . '/' . $numMonth . '/' . $numDate
			));
			if ($flag) {
				return 0;
			}

		} elseif (preg_match( "/^([0-9]{4})\-([0-9]{1,2})\-([0-9]{1,2})$/", $strStamp)) {
			preg_match( "/^([0-9]{4})\-([0-9]{1,2})\-([0-9]{1,2})$/", $strStamp, $arrMatch);
			list($strStamp, $numYear, $numMonth, $numDate) = $arrMatch;
			$numHour = 0;
			$numMin = 0;
			$flag = $classCheck->checkValueFormat(array(
				'flagType' => 'date',
				'value'    => $numYear . '/' . $numMonth . '/' . $numDate
			));
			if ($flag) {
				return 0;
			}

		} else {
			return 0;
		}

		$strTimeZone = (-1 * $varsAccount['numTimeZone']) . 'hours';
		$dateTime = new DateTime("$numYear-$numMonth-$numDate 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stampBook = $dateTime->format('U') + $numHour * 3600 + $numMin * 60;

		return $stampBook;
	}

	/**
		(array(
			'idEntity'        => $arr['idEntity'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'varsItem'        => $varsItem,
			'arrayLog'        => $arr['arrOrder'],
			'idAccount'       => $arr['idAccount'],
		))
	 */
	protected function _checkVarsCSVFormat($arr)
	{
		global $classCheck;

		$varsStatus = array(
			'arrImport'      => array(),
			'arrCashDefer'   => array(),
			'arrNone'        => array(),
			'arrErrorNumRow' => array(),
			'arrError'       => array(),
			'numAll'         => count($arr['arrayLog']),
		);

		$varsStamp = $this->_getNumFiscalTermStamp(array(
			'varsItem'        => $arr['varsItem'],
			'idEntity'        => $arr['idEntity'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$varsStampTemp = array();
		if ($arr['numFiscalPeriodTemp']) {
			$varsStampTemp = $this->_getNumFiscalTermStamp(array(
				'varsItem'        => $arr['varsItemTemp'],
				'idEntity'        => $arr['idEntity'],
				'numFiscalPeriod' => $arr['numFiscalPeriodTemp'],
			));
		}

		$arrayColumnNew = array();
		$array = reset($arr['arrayLog']);
		foreach ($array as $key => $value) {
			if (is_null($key) || $key === '') {
				continue;
			}
			$arrayColumnNew[$key] = 1;
		}

		$arrayLogNew = array();
		$arrayLog = $arr['arrayLog'];
		foreach ($arrayLog as $keyLog => $valueLog) {
			$tempRow = array();
			$arrayColumn = $arrayColumnNew;
			foreach ($arrayColumn as $keyColumn => $valueColumn) {
				$tempRow[$keyColumn] = $valueLog[$keyColumn];
			}
			$arrayLogNew[$keyLog] = $tempRow;
		}

		$varsColumn = array('id');
		$arrayColumn = array();
		$array = reset($arrayLogNew);
		$num = 1;
		foreach ($array as $key => $value) {
			$varsColumn[] = $key;
			$arrayColumn[$num] = $key;
			$num++;
		}

		$varsRetry = array(
			'varsColumn' => $varsColumn,
			'varsDetail' => array(),
		);

		$arrayRequests = array();
		$arrayRequestsTemp = array();
		$arrayLog = $arrayLogNew;
		foreach ($arrayLog as $keyLog => $valueLog) {
			$flagError = 0;
			$numRow = $keyLog + 1;

			if (count($varsColumn) < 4) {
				$varsStatus['arrNone'][] = $numRow;
				continue;
			}

			$varsTarget = $this->_checkValueImport(array(
				'varsImport'     => $arr['varsItem']['varsImport'],
				'varsImportTemp' => $arr['varsItemTemp']['varsImport'],
				'varsLog'        => $valueLog,
				'varsStamp'      => $varsStamp,
				'varsStampTemp'  => $varsStampTemp,
				'arrayColumn'    => $arrayColumn,
			));

			if (!$varsTarget) {
				$arrayNew = array(
					array(
						'value' => $numRow
					),
				);
				$array = $valueLog;
				foreach ($array as $key => $value) {
					if (is_null($value)) {
						$value = '';
					}
					$arrayNew[] = array(
						'value' => $value
					);
				}
				$tmpl = array(
					'id'         => $numRow,
					'varsDetail' => $arrayNew,
				);
				$varsRetry['varsDetail'][] = $tmpl;
				$varsStatus['arrNone'][] = $numRow;
				continue;
			}

			if ($varsTarget['flagCurrentStamp'] == 'stampBook') {
				$temp = $this->_checkVarsCSVFormatDetail(array(
					'valueLog'          => $valueLog,
					'varsRequest'       => $arr['varsItem']['varsFSItem']['varsJournalRequest']['varsRequest'],
					'varsTarget'        => $varsTarget,
					'varsStamp'         => $varsStamp,
					'arrayRequests'     => $arrayRequests,
					'varsStatus'        => $varsStatus,
					'idAccount'         => $arr['idAccount'],
					'numRow'            => $numRow,
				));
				$arrayRequests = $temp['arrayRequests'];

			} elseif ($varsTarget['flagCurrentStamp'] == 'stampBookTemp') {
				$temp = $this->_checkVarsCSVFormatDetail(array(
					'valueLog'          => $valueLog,
					'varsRequest'       => $arr['varsItem']['varsFSItem']['varsJournalRequest']['varsRequest'],
					'varsTarget'        => $varsTarget,
					'arrayRequests'     => $arrayRequestsTemp,
					'varsStamp'         => $varsStampTemp,
					'varsStatus'        => $varsStatus,
					'idAccount'         => $arr['idAccount'],
					'numRow'            => $numRow,
				));
				$arrayRequestsTemp = $temp['arrayRequests'];

			} else {
				$temp = $this->_checkVarsCSVFormatStampBook(array(
					'varsTarget'        => $varsTarget,
					'varsStamp'         => $varsStamp,
					'varsStampTemp'     => $varsStampTemp,
					'varsStatus'        => $varsStatus,
					'numRow'            => $numRow,
				));
			}
			$varsStatus = $temp['varsStatus'];
		}

		$data = array(
			'varsRetry'         => $varsRetry,
			'arrayRequests'     => $arrayRequests,
			'arrayRequestsTemp' => $arrayRequestsTemp,
			'varsStatus'        => $varsStatus,
		);

		return $data;
	}

	/**
			(array(
				'valueLog'          => $valueLog,
				'varsRequest'       => $arr['varsItem']['varsFSItem']['varsJournalRequest']['varsRequest'],
				'varsTarget'        => $varsTarget,
				'arrayRequests'     => $arrayRequestsTemp,
				'varsStamp'         => $varsStampTemp,
				'varsStatus'        => $varsStatus,
				'idAccount'         => $arr['idAccount'],
				'numRow'            => $numRow,
			))
	 */
	protected function _checkVarsCSVFormatStampBook($arr)
	{
		$stampBook = $arr['varsTarget']['stampBook'];

		if ($stampBook == '') {
			$flagError = __LINE__;
			$strError = '';
			if ($stampBook == '') {
				$strError = 'strMissStampBook';
			}
			$arr['varsStatus']['arrErrorNumRow'][] = $arr['numRow'];
			$arr['varsStatus']['arrError'][] = $strError;
		}

		//stampBook
		if (!$flagError) {
			$stampBook = $this->_getStampBook(array(
				'strBook'  => $stampBook,
			));
			if (!$stampBook) {
				$flagError = __LINE__;
				$arr['varsStatus']['arrErrorNumRow'][] = $arr['numRow'];
				$arr['varsStatus']['arrError'][] = 'strFormat';
			}
		}

		if (!$flagError) {
			if ($arr['varsStampTemp']) {
				if (!($arr['varsStamp']['stampMin'] <= $stampBook && $stampBook <= $arr['varsStamp']['stampMax']
					&& $arr['varsStampTemp']['stampMin'] <= $stampBook && $stampBook <= $arr['varsStampTemp']['stampMax']
				)) {
					$arr['varsStatus']['arrErrorNumRow'][] = $arr['numRow'];
					$arr['varsStatus']['arrError'][] = 'strTime';
				}

			} else {
				if (!($arr['varsStamp']['stampMin'] <= $stampBook && $stampBook <= $arr['varsStamp']['stampMax'])) {
					$arr['varsStatus']['arrErrorNumRow'][] = $arr['numRow'];
					$arr['varsStatus']['arrError'][] = 'strTime';
				}
			}
		}

		return $arr;
	}

	/**
			(array(
				'valueLog'          => $valueLog,
				'varsRequest'       => $arr['varsItem']['varsFSItem']['varsJournalRequest']['varsRequest'],
				'varsTarget'        => $varsTarget,
				'arrayRequests'     => $arrayRequestsTemp,
				'varsStamp'         => $varsStampTemp,
				'varsStatus'        => $varsStatus,
				'idAccount'         => $arr['idAccount'],
				'numRow'            => $numRow,
			))
	 */
	protected function _checkVarsCSVFormatDetail($arr)
	{
		global $classCheck;

		$stampBook = $arr['varsTarget']['stampBook'];
		$numValue = $arr['varsTarget']['numValue'];
		$strTitle = $arr['varsTarget']['strTitle'];

		if ($stampBook == '' || $numValue === '') {
			$flagError = __LINE__;
			$strError = '';
			if ($stampBook == '') {
				$strError = 'strMissStampBook';

			} elseif ($numValue == '') {
				$strError = 'strMissNumValue';
			}
			$arr['varsStatus']['arrErrorNumRow'][] = $arr['numRow'];
			$arr['varsStatus']['arrError'][] = $strError;
		}

		//stampBook
		if (!$flagError) {
			$stampBook = $this->_getStampBook(array(
				'strBook'  => $stampBook,
			));
			if (!$stampBook) {
				$flagError = __LINE__;
				$arr['varsStatus']['arrErrorNumRow'][] = $arr['numRow'];
				$arr['varsStatus']['arrError'][] = 'strFormat';
			}
		}

		if (!$flagError) {
			if (!($arr['varsStamp']['stampMin'] <= $stampBook && $stampBook <= $arr['varsStamp']['stampMax'])) {
				$flagError = __LINE__;
				$arr['varsStatus']['arrErrorNumRow'][] = $arr['numRow'];
				$arr['varsStatus']['arrError'][] = 'strTime';
			}
		}

		//numValue
		if (!$flagError) {
			if ($numValue <= 0) {
				$flagError = __LINE__;
				$arr['varsStatus']['arrErrorNumRow'][] = $arr['numRow'];
				$arr['varsStatus']['arrError'][] = 'strNumMin';
			}
		}

		if (!$flagError) {
			$flag = $classCheck->checkValueWord(array(
				'flagType' => 'num',
				'value'    => $numValue
			));
			if ($flag) {
				$flagError = __LINE__;
				$arr['varsStatus']['arrErrorNumRow'][] = $arr['numRow'];
				$arr['varsStatus']['arrError'][] = 'strFormatNumValue';
			}
		}

		if (!$flagError) {
			if ($numValue > 99999999999) {
				$flagError = __LINE__;
				$arr['varsStatus']['arrErrorNumRow'][] = $arr['numRow'];
				$arr['varsStatus']['arrError'][] = 'strNumMax';
			}
		}

		if (!$flagError) {
			$varsVersion = end($arr['varsTarget']['varsImport']['jsonVersion']);
			$varsRequest = $arr['varsRequest'];
			$varsRequest['jsonDetail'] = $varsVersion['jsonDetail'];
			$varsDetail = end($varsRequest['jsonDetail']['varsDetail']);
			$flag = $this->_chechVarsFlagConsumptionTaxMonetaryClaim(array(
				'vars'      => $varsDetail['arrDebit'],
				'stampBook' => $stampBook,
			));
			if ($flag) {
				$flagError = __LINE__;
				$arr['varsStatus']['arrErrorNumRow'][] = $arr['numRow'];
				$arr['varsStatus']['arrError'][] = 'strMonetaryClaim';
			}
			$flag = $this->_chechVarsFlagConsumptionTaxMonetaryClaim(array(
				'vars'      => $varsDetail['arrCredit'],
				'stampBook' => $stampBook,
			));
			if ($flag) {
				$flagError = __LINE__;
				$arr['varsStatus']['arrErrorNumRow'][] = $arr['numRow'];
				$arr['varsStatus']['arrError'][] = 'strMonetaryClaim';
			}
		}
		/*
		 * 20191001 start
		 */
		if (!$flagError) {
		    $varsVersion = end($arr['varsTarget']['varsImport']['jsonVersion']);
		    $varsRequest = $arr['varsRequest'];
		    $varsRequest['jsonDetail'] = $varsVersion['jsonDetail'];
		    $varsDetail = end($varsRequest['jsonDetail']['varsDetail']);
		    $flag = $this->_chechVarsFlagRateConsumptionTaxReduced(array(
		        'vars'      => $varsDetail['arrDebit'],
		        'stampBook' => $stampBook,
		    ));
		    if ($flag) {
		        $flagError = __LINE__;
		        $arr['varsStatus']['arrErrorNumRow'][] = $arr['numRow'];
		        $arr['varsStatus']['arrError'][] = 'strRateConsumptionTaxReduced';
		    }
		    $flag = $this->_chechVarsFlagRateConsumptionTaxReduced(array(
		        'vars'      => $varsDetail['arrCredit'],
		        'stampBook' => $stampBook,
		    ));
		    if ($flag) {
		        $flagError = __LINE__;
		        $arr['varsStatus']['arrErrorNumRow'][] = $arr['numRow'];
		        $arr['varsStatus']['arrError'][] = 'strRateConsumptionTaxReduced';
		    }
		}
		/*
		 * 20191001 end
		 */

		if ($flagError) {
			return $arr;
		}

		$arr['varsStatus']['arrImport'][] = $arr['numRow'];
		$varsVersion = end($arr['varsTarget']['varsImport']['jsonVersion']);
		$varsRequest = $arr['varsRequest'];

		$varsRequest['id'] = $arr['numRow'];
		$varsRequest['idAccount'] = $arr['idAccount'];

		$varsRequest['flagApply'] = $arr['varsTarget']['varsImport']['flagApply'];
		$varsRequest['idAccountApply'] = ($varsRequest['flagApply'])? $arr['idAccount'] : null;
		$varsRequest['arrCommaIdAccountPermit'] = $arr['varsTarget']['varsImport']['arrCommaIdAccountPermit'];
		$varsRequest['jsonPermitHistory'] = $arr['varsTarget']['varsImport']['jsonPermitHistory'];
		if ($varsRequest['jsonPermitHistory']) {
			$varsRequest['jsonPermitHistory'][0]['idAccountApply'] = $varsRequest['idAccountApply'];
			$varsRequest['jsonPermitHistory'][0]['stampRegister'] = TIMESTAMP;
		}

		$varsRequest['flagFiscalReport'] = '0';
		$varsRequest['stampBook'] = $stampBook;
		$varsRequest['strTitle'] = $strTitle;
		$varsRequest['jsonDetail'] = $varsVersion['jsonDetail'];
		$varsRequest['jsonDetail']['numSum'] = $numValue;
		$varsRequest['jsonDetail']['numSumDebit'] = $numValue;
		$varsRequest['jsonDetail']['numSumCredit'] = $numValue;

		$varsDetail = end($varsRequest['jsonDetail']['varsDetail']);

		$varsDetail['arrDebit']['numValue'] = $numValue;
		$varsDetail['arrCredit']['numValue'] = $numValue;

		$varsDetail['arrDebit'] = $this->_updateVarsDebit(array(
			'vars'             => $varsDetail['arrDebit'],
			'stampBook'        => $stampBook,
			'varsEntityNation' => $varsVersion['jsonDetail']['varsEntityNation'],
		));

		$varsDetail['arrCredit'] = $this->_updateVarsDebit(array(
			'vars'             => $varsDetail['arrCredit'],
			'stampBook'        => $stampBook,
			'varsEntityNation' => $varsVersion['jsonDetail']['varsEntityNation'],
		));
		$varsRequest['jsonDetail']['varsDetail'] = array();
		$varsRequest['jsonDetail']['varsDetail'][] = $varsDetail;

		$arr['arrayRequests'][] = $varsRequest;

		return $arr;
	}



	/**
		(array(
			'varsImport'     => $arr['varsItem']['varsImport'],
			'varsImportTemp' => $arr['varsItemTemp']['varsImport'],
			'varsLog'        => $valueLog,
			'varsStamp'      => $varsStamp,
			'varsStampTemp'  => $varsStampTemp,
			'arrayColumn'    => $arrayColumn,
		))
	 */
	protected function _checkValueImport($arr)
	{
		$array = &$arr['varsImport'];
		foreach ($array as $key => $value) {
			$varsData = array();
			$varsData['varsImport'] = $value;
			$varsData['flagCurrentStamp'] = '';

			$strColumn = $arr['arrayColumn'][$value['numColStampBook']];
			$varsData['stampBook'] = $arr['varsLog'][$strColumn];
			$strBook = $arr['varsLog'][$strColumn];

			if ($strBook != '') {
				$stampBook = $this->_getStampBook(array(
					'strBook'  => $strBook,
				));
				if ($stampBook) {
					if ($arr['varsStamp']['stampMin'] <= $stampBook && $stampBook <= $arr['varsStamp']['stampMax']) {
						$varsData['flagCurrentStamp'] = 'stampBook';

					} elseif ($arr['varsStampTemp']['stampMin'] <= $stampBook && $stampBook <= $arr['varsStampTemp']['stampMax']) {
						$varsData['flagCurrentStamp'] = 'stampBookTemp';
						$value = $arr['varsImportTemp'][$key];
						$varsData['varsImport'] = $value;
					}
				}
			}

			$strColumn = $arr['arrayColumn'][$value['numColNumValue']];
			$varsData['numValue'] = str_replace(',', '', $arr['varsLog'][$strColumn]);

			$strColumn = $arr['arrayColumn'][$value['numColStrTitle']];
			$varsData['strTitle'] = mb_substr($arr['varsLog'][$strColumn], 0, 100);


			$strTitle = preg_quote($value['strTitle']);
			$strTitle = str_replace('/', '\/', $strTitle);
			if ($value['flagAttest'] == 'eq') {
				if (preg_match( "/^$strTitle$/", $varsData['strTitle'])) {
					return $varsData;
				}

			} elseif ($value['flagAttest'] == 'like') {
				if (preg_match( "/$strTitle/", $varsData['strTitle'])) {
					return $varsData;
				}

			} elseif ($value['flagAttest'] == 'start') {
				if (preg_match( "/^$strTitle/", $varsData['strTitle'])) {
					return $varsData;
				}

			} elseif ($value['flagAttest'] == 'end') {
				if (preg_match( "/$strTitle$/", $varsData['strTitle'])) {
					return $varsData;
				}
			}
		}
	}


	/*
	 * 20191001 start
	 */
	/**
	(array(
	'vars'             => $varsDetail['arrDebit'],
	'varsEntityNation' => $varsVersion['jsonDetail']['varsEntityNation'],
	))
	*/
	protected function _chechVarsFlagRateConsumptionTaxReduced($arr)
	{
	    global $classTime;

	    $flagConsumptionTax = $this->_getCalcFlagConsumptionTax(array('vars' => $arr['vars'],));

	    if (!(preg_match( "/^tax/", $flagConsumptionTax)
	        || preg_match( "/^else/", $flagConsumptionTax)
	        )) {
	            return;
        }

        $numRate = $classTime->checkRateConsumptionTax(array('stamp' => $arr['stampBook']));
        /*
         * 20191001 start
         */
        if ($arr['vars']['flagRateConsumptionTaxReduced']) {
            if ($numRate != 10) {
                return 1;
            }
        }
	}

	  /*
	* 20191001 end
	*/

	/**
		(array(
			'vars'             => $varsDetail['arrDebit'],
			'varsEntityNation' => $varsVersion['jsonDetail']['varsEntityNation'],
		))
	 */
	protected function _chechVarsFlagConsumptionTaxMonetaryClaim($arr)
	{
		global $classTime;

		$flagConsumptionTax = $this->_getCalcFlagConsumptionTax(array('vars' => $arr['vars'],));
		$numRate = $classTime->checkRateConsumptionTax(array('stamp' => $arr['stampBook']));

		if ($flagConsumptionTax == 'free-MonetaryClaim') {
			if ($numRate == 5) {
				return 1;
			}
		}
	}



	/**
		(array(
			'vars'             => $varsDetail['arrDebit'],
			'varsEntityNation' => $varsVersion['jsonDetail']['varsEntityNation'],
		))
	 */
	protected function _updateVarsDebit($arr)
	{
		global $classTime;

		$flagTax = 0;
		$idAccountTitle = $arr['vars']['idAccountTitle'];
		$numValue = $arr['vars']['numValue'];
		$numValueConsumptionTax = '';

		$numRateConsumptionTax = $this->_getCalcRateConsumptionTax(array(
			'vars'      => $arr['vars'],
			'stampBook' => $arr['stampBook'],
		));

		$flagConsumptionTaxCalc = (int) $arr['vars']['flagConsumptionTaxCalc'];
		$flagConsumptionTaxWithoutCalc = (int) $arr['vars']['flagConsumptionTaxWithoutCalc'];

		if (!(int) $arr['varsEntityNation']['flagConsumptionTaxFree']
			&& !(int) $arr['varsEntityNation']['flagConsumptionTaxIncluding']
			&& $idAccountTitle != 'suspenseReceiptOfConsumptionTaxes'
			&& $idAccountTitle != 'suspensePaymentConsumptionTaxes'
			&& $numValue
		) {
			if ((int) $arr['varsEntityNation']['flagConsumptionTaxGeneralRule']) {
				if ((int) $arr['varsEntityNation']['flagConsumptionTaxDeducted']) {
					if (preg_match("/^tax/", $arr['vars']['flagConsumptionTaxGeneralRuleEach'])) {
						$flagTax = 1;
					}

				} else {
					if (preg_match("/^tax/", $arr['vars']['flagConsumptionTaxGeneralRuleProration'])) {
						$flagTax = 1;
					}
				}

			} else {
				if (preg_match("/^tax/", $arr['vars']['flagConsumptionTaxSimpleRule'])) {
					$flagTax = 1;
				}
			}
		}

		if ($flagTax) {
			$numValueConsumptionTax = 0;
			if ($flagConsumptionTaxWithoutCalc == 1) {
				$numValueConsumptionTax = $numValue *  $numRateConsumptionTax / (100 + $numRateConsumptionTax);
				if ($flagConsumptionTaxCalc == 1) {
					$numValueConsumptionTax = floor($numValueConsumptionTax);

				} elseif ($flagConsumptionTaxCalc == 2) {
					$numValueConsumptionTax = round($numValueConsumptionTax);

				} elseif ($flagConsumptionTaxCalc == 3) {
					$numValueConsumptionTax = ceil($numValueConsumptionTax);
				}

			} elseif ($flagConsumptionTaxWithoutCalc == 2) {
				//this is ok not wrong
				$numValueConsumptionTax = $numValue *  $numRateConsumptionTax / (100 + $numRateConsumptionTax);
				if ($flagConsumptionTaxCalc == 1) {
					$numValueConsumptionTax = floor($numValueConsumptionTax);

				} elseif ($flagConsumptionTaxCalc == 2) {
					$numValueConsumptionTax = round($numValueConsumptionTax);

				} elseif ($flagConsumptionTaxCalc == 3) {
					$numValueConsumptionTax = ceil($numValueConsumptionTax);
				}
				$arr['vars']['numValue'] = $numValue - $numValueConsumptionTax;

			} elseif ($flagConsumptionTaxWithoutCalc == 3) {
				$numValueConsumptionTax = '';
			}
		}

		$arr['vars']['numValueConsumptionTax'] = $numValueConsumptionTax;
		$arr['vars']['numRateConsumptionTax'] = $numRateConsumptionTax;

		return $arr['vars'];
	}

	/**
	 (array(
		'flagConsumptionTax' => $flagConsumptionTaxGeneralRuleEach,
		'vars'               => $arr['vars'],
		'stampBook'          => $arr['stampBook'],
	 ));
	 */
	protected function _getCalcRateConsumptionTax($arr)
	{
		global $classTime;

		$flagConsumptionTax = $this->_getCalcFlagConsumptionTax(array('vars' => $arr['vars'],));

		if (!(preg_match( "/^tax/", $flagConsumptionTax)
			|| preg_match( "/^else/", $flagConsumptionTax)
		)) {
			return '';
		}

		$numRate = $classTime->checkRateConsumptionTax(array('stamp' => $arr['stampBook']));
/*
 * 20191001 start
 */
		if ($numRate == 10) {
		    if ($arr['vars']['numRateConsumptionTax'] == 8 && $arr['vars']['flagRateConsumptionTaxReduced']) {
		        $numRate = 8;
		    }
		}


/*
 * 20191001 end
*/
		return $numRate;
	}

	/**
	 (array(
		'vars' => $arr['vars'],
	 ));
	 */
	protected function _getCalcFlagConsumptionTax($arr)
	{
		if ($arr['vars']['flagConsumptionTaxGeneralRuleEach']) {
			return $arr['vars']['flagConsumptionTaxGeneralRuleEach'];
		}

		if ($arr['vars']['flagConsumptionTaxGeneralRuleProration']) {
			return $arr['vars']['flagConsumptionTaxGeneralRuleProration'];
		}

		if ($arr['vars']['flagConsumptionTaxSimpleRule']) {
			return $arr['vars']['flagConsumptionTaxSimpleRule'];
		}

		return '';
	}
}
