<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcCash extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extChildSelf = array(
		'varsItem'   => array(),
		'numCompare' => 3,
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
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__ . $method);
			}
			exit;
		}
	}

	/**
		(array(
			'flagStatus'      => 'varsPreference',
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
		))
	 */
	protected function _iniVarsPreference($arr)
	{
		$varsPreference = $this->_getVarsPreference(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $arr['idEntity'],
		));

		return $varsPreference;
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
			'arrOrder'  => array(),
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
		Cash->this
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

		$flag = $this->_checkVarsEntityNationUpdate(array(
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'idEntity'         => $arr['idEntity'],
			'arrVarsLog'       => $arr['arrRows'],
			'varsEntityNation' => $varsEntityNation,
		));

		if (!$flag) {
			return;
		}

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

		return $flagErrorVars;
	}

	/**
		(array(
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'idEntity'         => $arr['idEntity'],
			'arrVarsLog'       => $arr['arrRows'],
			'varsEntityNation' => $varsEntityNation,
		));
	 */
	protected function _checkVarsEntityNationUpdate($arr)
	{
		$classCalcLog = $this->_getClassCalc(array('flagType' => 'Log'));

		$array = &$arr['arrVarsLog'];
		foreach ($array as $key => $value) {
			$dataVarsion = end($value['jsonVersion']);
			$varsOrder = array(
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

			$flag = $classCalcLog->allot(array(
				'flagStatus'      => 'check',
				'varsOrder'       => $varsOrder,
				'idEntity'        => $arr['idEntity'],
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
				'flagCheck'       => 'VarsEntityNation',
				'varsItem'        => array(
					'varsEntityNation' => $arr['varsEntityNation']
				),
			));
			if ($flag) {
				return 1;
			}
			break;
		}
	}

	/**


	/**
		Portal->CalcLogConsumptionTax->this
		(array(
			'flagStatus'       => 'UpdateTax',
			'arrRows'          => $arrVarsLogAll,
			'arrRowsPrev'      => $arrRows,
			'numFiscalPeriod'  => $arr['varsItem']['numFiscalPeriod'],
			'idEntity'         => $arr['idEntity'],
			'varsEntityNation' => $arr['varsItem']['varsEntityNationUpdate'],
		))
	 */
	protected function _iniUpdateTax($arr)
	{
		$flagErrorVars = $this->_updateDbTax(array(
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'idEntity'         => $arr['idEntity'],
			'arrVarsLog'       => $arr['arrRows'],
			'arrVarsLogPrev'   => $arr['arrRowsPrev'],
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
			'arrVarsLogPrev'   => $arr['arrRowsPrev'],
			'varsEntityNation' => $arr['varsEntityNation'],
		));
	 */
	protected function _updateDbTax($arr)
	{
		global $classDb;

		$classCalcLog = $this->_getClassCalc(array('flagType' => 'Log'));
		$flagMax = 0;
		$arrayIdLog = array();

		$tempData = array(
			'arrVarsPre'        => array(),
			'arrVarsPreUpdate'  => array(),
			'arrVarsDone'       => array(),
			'arrVarsDoneUpdate' => array(),
		);

		$array = $arr['arrVarsLog'];
		foreach ($array as $key => $value) {
			if ($value['flagPay']) {
				$tempData['arrVarsDone'][$key] = $arr['arrVarsLogPrev'][$key];
				$tempData['arrVarsDoneUpdate'][$key] = $value;

			} else {
				$tempData['arrVarsPre'][$key] = $arr['arrVarsLogPrev'][$key];
				$tempData['arrVarsPreUpdate'][$key] = $value;
			}

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
				'varsEntityNation' => $arr['varsEntityNation']
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

			$flag = $this->checkTextSize(array(
				'flag'        => 'errorDataMax',
				'str'         => $jsonVersion,
				'flagReturn'  => 1,
			));

			if ($flag) {
				$flagMax = 1;
				$arrayIdLog[] = $value['idLogCash'];
				continue;
			}

			$stampUpdate = TIMESTAMP;
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

				if ($keyTemp == 'jsonVersion') {
					$valueTemp = $value['jsonVersion'];
				}
				if ($value['flagPay']) {
					$tempData['arrVarsDoneUpdate'][$key][$keyTemp] = $valueTemp;

				} else {
					$tempData['arrVarsPreUpdate'][$key][$keyTemp] = $valueTemp;
				}
			}

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
						'value'         => $value['idLogCash'],
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

		return $tempData;
	}

	/**
		(array(
			'flagStatus'           => 'addDone',
			'arrRows'              => array($tempLog['varsLog']),
			'numFiscalPeriod'      => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'             => $varsPluginAccountingAccount['idEntityCurrent'],
		))
	 */
	protected function _iniAddDone($arr)
	{
		$varsItem = $this->_extChildSelf['varsItemDone'][$arr['idEntity']][$arr['numFiscalPeriod']];
		if (!$varsItem) {
			$varsItem = $this->_getVarsItem(array(
				'numFiscalPeriod'      => $arr['numFiscalPeriod'],
				'numFiscalPeriodValue' => $arr['numFiscalPeriod'],//not wrong
				'idEntity'             => $arr['idEntity'],
			));
			$this->_extChildSelf['varsItemDone'][$arr['idEntity']][$arr['numFiscalPeriod']] = $varsItem;
		}

		$flag = $this->_setVarsValue(array(
			'arrRows'              => $arr['arrRows'],
			'numFiscalPeriod'      => $arr['numFiscalPeriod'],
			'numFiscalPeriodValue' => $arr['numFiscalPeriod'],
			'varsItem'             => $varsItem,
			'flagDelete'           => ($arr['flagDelete'])? 1 : 0,
			'flagPay'              => 1,
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}
		$this->_updateDbPreferenceStamp(array('strColumn' => 'cashValue'));
	}

	/**
		(array(
			'flagStatus'      => 'editPre',
			'numFiscalPeriod'      => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'             => $varsPluginAccountingAccount['idEntityCurrent'],
			'arrRowsAdd'      => $arrRowsAdd,
			'arrRowsDelete'   => $arrRowsDelete,
		))
	 */
	protected function _iniEditDone($arr)
	{
		$this->_iniDeleteDone(array(
			'arrRows'         => $arr['arrRowsDelete'],
			'idEntity'        => $arr['idEntity'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		$this->_iniAddDone(array(
			'arrRows'         => $arr['arrRowsAdd'],
			'idEntity'        => $arr['idEntity'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
	}

	/**
		(array(
			'flagStatus'      => 'deleteDone',
			'arrRows'         => $arrRows,
			'numFiscalPeriod'      => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'numFiscalPeriodValue' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'             => $varsPluginAccountingAccount['idEntityCurrent'],
		))
	 */
	protected function _iniDeleteDone($arr)
	{
		$this->_iniAddDone(array(
			'arrRows'         => $arr['arrRows'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $arr['idEntity'],
			'flagDelete'      => 1,
		));
	}

	/**
		(array(
			'flagStatus'      => 'addPre',
			'arrRows'         => array($tempLog['varsLogTempUpdate']),
			'numFiscalPeriod' => $tempLog['numFiscalPeriodTemp'],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
		))
	 */
	protected function _iniAddPre($arr)
	{
		$array = array();
		$numAll = $this->_extChildSelf['numCompare'];
		for ($i = 0; $i < $numAll; $i++) {
			$numFiscalPeriod = $arr['numFiscalPeriod'] + $i;

			$varsEntityNation = $this->_extChildSelf['varsEntityNation'][$arr['idEntity']][$numFiscalPeriod];
			if (!$varsEntityNation) {
				$varsEntityNation = $this->_getVarsEntityNation(array(
					'numFiscalPeriod' => $numFiscalPeriod,
				));
				$this->_extChildSelf['varsEntityNation'][$arr['idEntity']][$numFiscalPeriod] = $varsEntityNation;
			}

			if (!$varsEntityNation) {
				$varsEntityNation = $this->_getVarsEntityNationData(array(
					'numFiscalPeriod'    => $numFiscalPeriod,
					'numFiscalTermMonth' => 12,
					'varsEntityNation'   => $varsEntityNationPrev,
				));
			}
			$array[$numFiscalPeriod]['varsEntityNation'] = $varsEntityNation;
			$varsEntityNationPrev = $varsEntityNation;
		}

		foreach ($array as $key => $value) {
			$numFiscalPeriod = $key;
			$varsItem = $this->_extChildSelf['varsItemPre'][$arr['idEntity']][$numFiscalPeriod];
			if (!$varsItem) {
				$varsItem = $this->_getVarsItem(array(
					'numFiscalPeriod'  => $numFiscalPeriod,
					'idEntity'         => $arr['idEntity'],
					'varsEntityNation' => $value['varsEntityNation'],
				));
			}
			$this->_extChildSelf['varsItemPre'][$arr['idEntity']][$numFiscalPeriod] = $varsItem;
			$flag = $this->_setVarsValue(array(
				'arrRows'              => $arr['arrRows'],
				'numFiscalPeriod'      => $arr['numFiscalPeriod'],
				'numFiscalPeriodValue' => $numFiscalPeriod,
				'varsItem'             => $varsItem,
				'flagDelete'           => ($arr['flagDelete'])? 1 : 0,
				'flagPay'              => 0,
			));
			if ($flag == 'errorDataMax') {
				return $flag;
			}
		}

		$this->_updateDbPreferenceStamp(array('strColumn' => 'cashValue'));
	}

	/**
		(array(
			'numFiscalPeriod'    => $numFiscalPeriod,
			'numFiscalTermMonth' => 12,
			'varsEntityNation'   => $varsEntityNation,
		))
	 */
	protected function _getVarsEntityNationData($arr)
	{
		global $classTime;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);
		$numTimeZone = PLUGIN_ACCOUNTING_NUM_TIME_ZONE;

		$arrDate = $classTime->getLocal(array('stamp' => $arr['varsEntityNation']['stampFiscalBeginning']));
		$numFiscalBeginningYear = $arrDate['year'];
		$numFiscalBeginningMonth = $arrDate['month'] + $arr['varsEntityNation']['numFiscalTermMonth'];
		if ($numFiscalBeginningMonth > 12) {
			$numFiscalBeginningMonth -= 12;
			$numFiscalBeginningYear++;
		}

		$strTimeZone = (-1 * $numTimeZone) . 'hours';
		$numYear = $numFiscalBeginningYear;
		$numMonth = $numFiscalBeginningMonth;
		$dateTime = new DateTime("$numYear-$numMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stamp = $dateTime->format('U');

		$stampFiscalBeginning = $stamp;

		$array = $arr['varsEntityNation'];
		foreach ($array as $key => $value) {
			if ($key == 'numFiscalPeriod') {
				$array[$key] = $arr['numFiscalPeriod'];

			} elseif ($key == 'numFiscalBeginningYear') {
				$array[$key] = $numFiscalBeginningYear;

			} elseif ($key == 'stampFiscalBeginning') {
				$array[$key] = $stampFiscalBeginning;

			} elseif ($key == 'numFiscalBeginningMonth') {
				$array[$key] = $numFiscalBeginningMonth;

			} elseif ($key == 'numFiscalTermMonth') {
				$array[$key] = $arr['numFiscalTermMonth'];
			}
		}

		return $array;
	}

	/**
		(array(
			'flagStatus'      => 'editPre',
			'numFiscalPeriod'      => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'             => $varsPluginAccountingAccount['idEntityCurrent'],
			'arrRowsAdd'      => $arrRowsAdd,
			'arrRowsDelete'   => $arrRowsDelete,
		))
	 */
	protected function _iniEditPre($arr)
	{
		$this->_iniDeletePre(array(
			'arrRows'         => $arr['arrRowsDelete'],
			'idEntity'        => $arr['idEntity'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		$this->_iniAddPre(array(
			'arrRows'         => $arr['arrRowsAdd'],
			'idEntity'        => $arr['idEntity'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
	}

	/**
		(array(
			'flagStatus'      => 'deletePre',
			'arrRows'         => $arrRows,
			'numFiscalPeriod'      => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'numFiscalPeriodValue' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'             => $varsPluginAccountingAccount['idEntityCurrent'],
		))
	 */
	protected function _iniDeletePre($arr)
	{
		$this->_iniAddPre(array(
			'arrRows'         => $arr['arrRows'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $arr['idEntity'],
			'flagDelete'      => 1,
		));
	}

	/**
		(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $arr['idEntity'],
		))
	 */
	protected function _getVarsItem($arr)
	{
		$varsEntityNation = $arr['varsEntityNation'];
		if (!$varsEntityNation) {
			$varsEntityNation = $this->_getVarsEntityNation(array(
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
			));
		}

		$array = $this->_getVarsFlagFiscalPeriod(array(
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'varsEntityNation' => $varsEntityNation,
		));
		$varsFiscalPeriod = array();
		foreach ($array as $key => $value) {
			$varsFiscalPeriod[$value] = $this->_getVarsStampTerm(array(
				'varsFlag'         => array('flagFiscalPeriod' => $value),
				'varsEntityNation' => $varsEntityNation,
				'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			));
		}

		$varsFiscalPeriodMonth = $this->_getVarsFiscalPeriodMonth(array(
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'varsEntityNation' => $varsEntityNation,
		));

		$varsFSItem = $this->_getVarsFSItem();

		$data = array(
			'varsEntityNation'       => $varsEntityNation,
			'varsFiscalPeriod'       => $varsFiscalPeriod,
			'varsFiscalPeriodMonth'  => $varsFiscalPeriodMonth,
			'idEntity'               => $arr['idEntity'],
			'varsFSItem'             => $varsFSItem,
		);

		return $data;
	}

	/**
		(array(
			'arrRows'              => $arr['arrRows'],
			'numFiscalPeriod'      => $arr['numFiscalPeriod'],
			'numFiscalPeriodValue' => $numFiscalPeriod,
			'varsItem'             => $varsItem,
			'flagDelete'           => ($arr['flagDelete'])? 1 : 0,
			'flagPay'              => 0,
		))
	 */
	protected function _setVarsValue($arr)
	{
		$varsLogValue = $this->_getVarsValueCash(array(
			'numFiscalPeriod'      => $arr['numFiscalPeriod'],
			'numFiscalPeriodValue' => $arr['numFiscalPeriodValue'],
			'idEntity'             => $arr['varsItem']['idEntity'],
			'flagPay'              => $arr['flagPay'],
		));

		$varsValue = $this->_getValueData(array(
			'arrRows'         => $arr['arrRows'],
			'varsValue'       => $varsLogValue['jsonData'],
			'varsItem'        => $arr['varsItem'],
			'flagDelete'      => $arr['flagDelete'],
			'flagPay'         => $arr['flagPay'],
		));


		$flag = $this->_updateDb(array(
			'varsValue'            => $varsValue,
			'numFiscalPeriod'      => $arr['numFiscalPeriod'],
			'numFiscalPeriodValue' => $arr['numFiscalPeriodValue'],
			'idEntity'             => $arr['varsItem']['idEntity'],
			'flagPay'              => $arr['flagPay'],
		));

		if ($flag == 'errorDataMax') {
			return $flag;
		}
	}

	/**

	 */
	protected function _getVarsValueCash($arr)
	{
		global $classDb;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingCashValue',
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
				array(
					'flagType'      => 'num',
					'strColumn'     => 'numFiscalPeriodValue',
					'flagCondition' => 'eq',
					'value'         => $arr['numFiscalPeriodValue'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'flagPay',
					'flagCondition' => 'eq',
					'value'         => $arr['flagPay'],
				),
			),
		));

		if (!$rows['numRows']) {
			return array();
		}

		return $rows['arrRows'][0];
	}



	/**
		(array(
			'arrRows'      => $arr['arrRows'],
			'varsValue'    => $varsValue,
			'varsItem'     => $arr['varsItem'],
			'flagDelete'   => $arr['flagDelete'],
			'flagPay'      => $arr['flagPay'],
		))
	 */
	protected function _getValueData($arr)
	{
		global $classTime;

		$varsValue = array();
		if ($arr['varsValue']) {
			$varsValue = $arr['varsValue'];
		}
		$varsFiscalPeriod = $arr['varsItem']['varsFiscalPeriod'];

		$dataTmpl = array(
			'sumIn'   => 0,
			'sumOut'  => 0,
			'varsContra' => array(),
		);

		$array = $arr['varsItem']['varsFiscalPeriodMonth'];
		$arrayRows = &$arr['arrRows'];
		foreach ($arrayRows as $keyRows => $valueRows) {
			$arrDate = $classTime->getLocal(array('stamp' => $valueRows['stampBook']));
			if (!($varsFiscalPeriod['f1']['stampMin'] <= $valueRows['stampBook']
				&& $valueRows['stampBook'] <= $varsFiscalPeriod['f1']['stampMax']
			)) {
				continue;
			}
			$numEnd = count($valueRows['jsonVersion']) - 1;
			$varsLogDetail = $this->_updateVarsJournalTax(array(
				'varsJournal'      => $arr['varsItem']['varsFSItem']['varsJournal'],
				'varsDetail'       => $valueRows['jsonVersion'][$numEnd]['jsonDetail']['varsDetail'],
				'varsEntityNation' => $valueRows['jsonVersion'][$numEnd]['jsonDetail']['varsEntityNation'],
			));

			$arrayStrIn = array();
			if ($valueRows['flagIn'] == 1) {
				$arrayStrIn = array('sumIn');

			} elseif ($valueRows['flagIn'] == 2) {
				$arrayStrIn = array('sumIn', 'sumOut');

			} else {
				$arrayStrIn = array('sumOut');
			}

			$flag = 0;
			$arrayFlagCheck = array();
			foreach ($array as $key => $value) {
				$numMonth = $value;
				if ($numMonth == $arrDate['month']) {
					$flag = 1;
				}
				if ($flag) {
					foreach ($arrayStrIn as $keyStrIn => $valueStrIn) {
						$strIn = $valueStrIn;
						$numValue = $valueRows['numValue'];
						if ($arr['flagDelete']) {
							$numValue *= -1;
						}
						//init
						//f1
						if (is_null($varsValue['f1'])) {
							$varsValue['f1'] = $dataTmpl;
						}

						//month
						if (is_null($varsValue[$numMonth])) {
							$varsValue[$numMonth] = $dataTmpl;
						}

						//f2
						if ($varsFiscalPeriod['f21']) {
							$arrayReport = array('f21', 'f22');
							foreach ($arrayReport as $keyReport => $valueReport) {
								if (is_null($varsValue[$valueReport])) {
									$varsValue[$valueReport] = $dataTmpl;
								}
							}
						}

						//f4
						if ($varsFiscalPeriod['f41']) {
							$arrayReport = array('f41', 'f42', 'f43', 'f44');
							foreach ($arrayReport as $keyReport => $valueReport) {
								if (is_null($varsValue[$valueReport])) {
									$varsValue[$valueReport] = $dataTmpl;
								}
							}
						}

						//f1
						if ((!$arrayFlagCheck['f1'] && $valueRows['flagIn'] != 2)
							|| ((int) $arrayFlagCheck['f1'] < 2 && $valueRows['flagIn'] == 2)
						 ) {
							$varsValue['f1'][$strIn] += $numValue;
							$varsValue['f1']['varsContra'] = $this->_getValueContra(array(
								'varsDetail' => $varsLogDetail,
								'varsValue'  => $varsValue['f1']['varsContra'],
								'flagIn'     => ($strIn == 'sumIn')? 1 : 0,
								'strIn'      => $strIn,
								'flagDelete' => $arr['flagDelete'],

							));
							$arrayFlagCheck['f1']++;
						}

						//month
						if ($valueRows['stampBook'] >= $varsFiscalPeriod[$numMonth]['stampMin']
							&& $valueRows['stampBook'] <= $varsFiscalPeriod[$numMonth]['stampMax']
						) {
							$varsValue[$numMonth][$strIn] += $numValue;
							$varsValue[$numMonth]['varsContra'] = $this->_getValueContra(array(
								'varsDetail' => $varsLogDetail,
								'varsValue'  => $varsValue[$numMonth]['varsContra'],
								'flagIn'     => ($strIn == 'sumIn')? 1 : 0,
								'strIn'      => $strIn,
								'flagDelete' => $arr['flagDelete'],
							));
						}

						//f2
						if ($varsFiscalPeriod['f21']) {
							$arrayReport = array('f21', 'f22');
							foreach ($arrayReport as $keyReport => $valueReport) {
								if ((!$arrayFlagCheck[$valueReport] && $valueRows['flagIn'] != 2)
									|| ((int) $arrayFlagCheck[$valueReport] < 2 && $valueRows['flagIn'] == 2)
								) {
									if ($valueRows['stampBook'] >= $varsFiscalPeriod[$valueReport]['stampMin']
										&& $valueRows['stampBook'] <= $varsFiscalPeriod[$valueReport]['stampMax']
									) {
										$varsValue[$valueReport][$strIn] += $numValue;
										$varsValue[$valueReport]['varsContra'] = $this->_getValueContra(array(
											'varsDetail' => $varsLogDetail,
											'varsValue'  => $varsValue[$valueReport]['varsContra'],
											'flagIn'     => ($strIn == 'sumIn')? 1 : 0,
											'strIn'      => $strIn,
											'flagDelete' => $arr['flagDelete'],
										));
										$arrayFlagCheck[$valueReport]++;
									}
								}
							}

						//f4
						}
						if ($varsFiscalPeriod['f41']) {
							$arrayReport = array('f41', 'f42', 'f43', 'f44');
							foreach ($arrayReport as $keyReport => $valueReport) {
								if ((!$arrayFlagCheck[$valueReport] && $valueRows['flagIn'] != 2)
									|| ((int) $arrayFlagCheck[$valueReport] < 2 && $valueRows['flagIn'] == 2)
								) {
									if ($valueRows['stampBook'] >= $varsFiscalPeriod[$valueReport]['stampMin']
										&& $valueRows['stampBook'] <= $varsFiscalPeriod[$valueReport]['stampMax']
									) {
										$varsValue[$valueReport][$strIn] += $numValue;
										$varsValue[$valueReport]['varsContra'] = $this->_getValueContra(array(
											'varsDetail' => $varsLogDetail,
											'varsValue'  => $varsValue[$valueReport]['varsContra'],
											'flagIn'     => ($strIn == 'sumIn')? 1 : 0,
											'strIn'      => $strIn,
											'flagDelete' => $arr['flagDelete'],
										));
										$arrayFlagCheck[$valueReport]++;
									}
								}
							}
						}
					}
					if ($varsFiscalPeriod['f21']) {
						$arrayReport = array('f21', 'f22');
						foreach ($arrayReport as $keyReport => $valueReport) {
							if (is_null($varsValue[$valueReport])) {
								$varsValue[$valueReport] = array();
							}
						}
					}
					if ($varsFiscalPeriod['f41']) {
						$arrayReport = array('f41', 'f42', 'f43', 'f44');
						foreach ($arrayReport as $keyReport => $valueReport) {
							if (is_null($varsValue[$valueReport])) {
								$varsValue[$valueReport] = array();
							}
						}
					}
					if (is_null($varsValue[$numMonth])) {
						$varsValue[$numMonth] = array();
					}
				}
			}
		}

		return $varsValue;
	}

	/**
		(array(
			'varsDetail' => $varsLogDetail,
			'varsValue'  => $varsValue[$numMonth]['varsDetail'],
			'flagIn'     => $valueRows['flagIn'],
			'strIn'      => $strIn,
		))
	 */
	protected function _getValueContra($arr)
	{
		$dataTmpl = array(
			'sumIn'  => 0,
			'sumOut' => 0,
		);

		$array = $arr['varsDetail'];
		foreach ($array as $key => $value) {
			$arrayStr = array('Debit', 'Credit');
			foreach ($arrayStr as $keyStr => $valueStr) {
				$flagDebit = ($valueStr == 'Debit')? 1 : 0;
				if ($arr['flagIn'] == 1 && $flagDebit
					|| $arr['flagIn'] == 0 && !$flagDebit
				) {
					continue;
				}

				$strSide = 'arr' . $valueStr;
				$idAccountTitle = $value[$strSide]['idAccountTitle'];
				if (!$idAccountTitle) {
					continue;
				}

				$numValue = $value[$strSide]['numValue'];
				if ($arr['flagDelete']) {
					$numValue *= -1;
				}

				//ini
				if (is_null($arr['varsValue'][$idAccountTitle]['all'])) {
					$arr['varsValue'][$idAccountTitle]['all'] = $dataTmpl;
				}
				$arr['varsValue'][$idAccountTitle]['all'][$arr['strIn']] += $numValue;

				$idSubAccountTitle = ($value[$strSide]['idSubAccountTitle'])? $value[$strSide]['idSubAccountTitle'] : '';
				if ($idSubAccountTitle) {
					if (is_null($arr['varsValue'][$idAccountTitle][$idSubAccountTitle])) {
						$arr['varsValue'][$idAccountTitle][$idSubAccountTitle] = $dataTmpl;
					}
					$arr['varsValue'][$idAccountTitle][$idSubAccountTitle][$arr['strIn']] += $numValue;
				}
			}
		}

		return $arr['varsValue'];
	}

	/**
		(array(
			'varsValue'       => $varsValue,
			'numFiscalPeriod' => $arr['varsItem']['numFiscalPeriod'],
			'idEntity'        => $arr['varsItem']['idEntity'],
			'flagPay'         => $arr['flagPay'],
		))
	 */
	protected function _updateDb($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$arrColumn = array();
		$arrValue = array();

		$arrColumn[] = 'jsonData';

		if ($arr['varsValue']) {
			$json = json_encode($arr['varsValue']);

			$flag = $this->checkTextSize(array(
				'flagReturn' => 1,
				'str'        => $json,
			));
			if ($flag) {
				return 'errorDataMax';
			}
			$arrValue[] = $json;

		} else {
			$arrValue[] = json_encode(array());
		}

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
				'strColumn'     => 'numFiscalPeriodValue',
				'flagCondition' => 'eq',
				'value'         => $arr['numFiscalPeriodValue'],
			),
			array(
				'flagType'      => 'num',
				'strColumn'     => 'flagPay',
				'flagCondition' => 'eq',
				'value'         => $arr['flagPay'],
			),
		);

		$classDb->updateRow(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingCashValue',
			'arrColumn' => $arrColumn,
			'flagAnd'   => 1,
			'arrWhere'  => $arrWhere,
			'arrValue'  => $arrValue,
		));
	}
}
