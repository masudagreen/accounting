<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcLogModify extends Code_Else_Plugin_Accounting_Jpn_Jpn
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
	 * $varsPluginAccountingAccount['idEntityCurrent'] fixed
		(array(
			'flagStatus'           => 'delete',
			'numFiscalPeriodStart' => $numFiscalPeriodStart,
			'numFiscalPeriodEnd'   => $numFiscalPeriodEnd,
			'arrIdTarget'          => array(),
		))
	 */
	protected function _iniDelete($arr)
	{
		$classCalcAccountTitle = $this->_getClassCalc(array('flagType' => 'AccountTitle'));
		$classCalcSubAccountTitle = $this->_getClassCalc(array('flagType' => 'SubAccountTitle'));
		$classCalcConsumptionTax = $this->_getClassCalc(array('flagType' => 'ConsumptionTax'));
		$classCalcLogCalc = $this->_getClassCalc(array('flagType' => 'LogCalc'));
		$classCalcTempNextLog = $this->_getClassCalc(array(
			'flagType'   => 'TempNext',
			'flagDetail' => 'Log',
		));

		$arrVarsLogPeriod = array();
		$numStart = $arr['numFiscalPeriodStart'];
		$numEnd = $arr['numFiscalPeriodEnd'];
		for ($i = $numStart ; $i <= $numEnd; $i++) {
			$numFiscalPeriod = $i;
			$arrVarsLog = $this->_getVarsLog(array(
				'numFiscalPeriod' => $numFiscalPeriod,
				'arrIdTarget'     => $arr['arrIdTarget'],
			));
			$arrVarsLogPeriod[$numFiscalPeriod] = $arrVarsLog;
			if (!$arrVarsLog) {
				continue;
			}
			$arrRows = $this->_getVarsLogCalcLoop(array(
				'arrVarsLog'      => $arrVarsLog,
				'numFiscalPeriod' => $numFiscalPeriod,
			));
			$flag = $classCalcAccountTitle->allot(array(
				'flagStatus'      => 'delete',
				'numFiscalPeriod' => $numFiscalPeriod,
				'arrRows'         => $arrRows,
			));
			$classCalcSubAccountTitle->allot(array(
				'flagStatus'      => 'delete',
				'numFiscalPeriod' => $numFiscalPeriod,
				'arrRows'         => $arrRows,
			));
			$classCalcConsumptionTax->allot(array(
				'flagStatus'      => 'delete',
				'numFiscalPeriod' => $numFiscalPeriod,
				'arrRows'         => $arrRows,
			));
			$classCalcLogCalc->allot(array(
				'flagStatus'      => 'delete',
				'numFiscalPeriod' => $numFiscalPeriod,
				'arrRows'         => $arrRows,
			));
			if ($numFiscalPeriod == $numEnd) {
				break;
			}
			$numStartNext = $numFiscalPeriod + 1;
			for ($j = $numStartNext ; $j <= $numEnd; $j++) {
				$numFiscalPeriodNext = $j;
				$flag = $classCalcTempNextLog->allot(array(
					'flagStatus'      => 'delete',
					'numFiscalPeriod' => $numFiscalPeriodNext,
					'arrRows'         => $arrRows,
				));
				if ($flag == 'errorDataMax') {
					return $flag;
				}
			}
		}

		$this->_updateDbPreferenceStamp(array('strColumn' => 'fsValue'));
		$this->_updateDbPreferenceStamp(array('strColumn' => 'entityDepartmentFSValue'));

		return $arrVarsLogPeriod;
	}

	/**
		(array(
			'numFiscalPeriod' => $numFiscalPeriod,
			'arrIdTarget'     => $arr['arrIdTarget'],
		))
	 */
	protected function _getVarsLog($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$arrayCheck = array();
		$array = $arr['arrIdTarget'];
		foreach ($array as $key => $value) {
			$strId = ',' . $value . ',';
			$rows = $classDb->getSelect(array(
				'idModule' => 'accounting',
				'strTable' => 'accountingLog',
				'arrLimit' => array(),
				'arrOrder' => array(),
				'flagAnd'  => 1,
				'arrWhere' => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idEntity',
						'flagCondition' => 'eq',
						'value'         => $varsPluginAccountingAccount['idEntityCurrent'],
					),
					array(
						'flagType'      => 'num',
						'strColumn'     => 'numFiscalPeriod',
						'flagCondition' => 'eq',
						'value'         => $arr['numFiscalPeriod'],
					),
					array(
						'flagType'      => '',
						'strColumn'     => 'flagApply',
						'flagCondition' => 'ne',
						'value'         => 1,
					),
					array(
						'flagType'      => '',
						'strColumn'     => 'flagRemove',
						'flagCondition' => 'ne',
						'value'         => 1,
					),
					array(
						'flagType'      => '',
						'strColumn'     => 'arrCommaIdAccountTitle',
						'flagCondition' => 'like',
						'value'         => $strId,
					),
				),
			));
			$arrayLog = $rows['arrRows'];
			foreach ($arrayLog as $keyLog => $valueLog) {
				$arrayCheck[$valueLog['idLog']] = $valueLog;
			}
		}

		$arrayNew = array();
		$array = $arrayCheck;
		foreach ($array as $key => $value) {
			$arrayNew[] = $value;
		}

		return $arrayNew;
	}

	/**
	 * $varsPluginAccountingAccount['idEntityCurrent'] fixed
		(array(
			'flagStatus'           => 'edit',
			'numFiscalPeriodStart' => $numFiscalPeriodStart,
			'numFiscalPeriodEnd'   => $numFiscalPeriodEnd,
			'arrIdTarget'          => array(),
		))
	 */
	protected function _iniEdit($arr)
	{
		$arrVarsLogPeriod = $this->_iniDelete(array(
			'numFiscalPeriodStart' => $arr['numFiscalPeriodStart'],
			'numFiscalPeriodEnd'   => $arr['numFiscalPeriodEnd'],
			'arrIdTarget'          => $arr['arrIdTarget'],
		));
		if ($arrVarsLogPeriod == 'errorDataMax') {
			return $flag;
		}

		$flag = $this->_iniAdd(array(
			'numFiscalPeriodStart' => $arr['numFiscalPeriodStart'],
			'numFiscalPeriodEnd'   => $arr['numFiscalPeriodEnd'],
			'arrVarsLogPeriod'     => $arrVarsLogPeriod,
		));
		if ($flag) {
			return $flag;
		}
	}

	/**
	 * $varsPluginAccountingAccount['idEntityCurrent'] fixed
		(array(
			'flagStatus'           => 'delete',
			'numFiscalPeriodStart' => $numFiscalPeriodStart,
			'numFiscalPeriodEnd'   => $numFiscalPeriodEnd,
			'arrVarsLogPeriod'     => array(),
		))
	 */
	protected function _iniAdd($arr)
	{
		$classCalcAccountTitle = $this->_getClassCalc(array('flagType' => 'AccountTitle'));
		$classCalcSubAccountTitle = $this->_getClassCalc(array('flagType' => 'SubAccountTitle'));
		$classCalcConsumptionTax = $this->_getClassCalc(array('flagType' => 'ConsumptionTax'));
		$classCalcLogCalc = $this->_getClassCalc(array('flagType' => 'LogCalc'));
		$classCalcTempNextLog = $this->_getClassCalc(array(
			'flagType'   => 'TempNext',
			'flagDetail' => 'Log',
		));

		$arrVarsLogPeriod = $arr['arrVarsLogPeriod'];
		$numStart = $arr['numFiscalPeriodStart'];
		$numEnd = $arr['numFiscalPeriodEnd'];
		for ($i = $numStart ; $i <= $numEnd; $i++) {
			$numFiscalPeriod = $i;
			$arrVarsLog = $arrVarsLogPeriod[$numFiscalPeriod];
			if (!$arrVarsLog) {
				continue;
			}
			$arrRows = $this->_getVarsLogCalcLoop(array(
				'arrVarsLog'      => $arrVarsLog,
				'numFiscalPeriod' => $numFiscalPeriod,
			));
			$flag = $classCalcAccountTitle->allot(array(
				'flagStatus'      => 'add',
				'arrRows'         => $arrRows,
				'numFiscalPeriod' => $numFiscalPeriod,
			));
			if ($flag == 'errorDataMax') {
				return $flag;
			}

			$flag = $classCalcSubAccountTitle->allot(array(
				'flagStatus'      => 'add',
				'arrRows'         => $arrRows,
				'numFiscalPeriod' => $numFiscalPeriod,
			));
			if ($flag == 'errorDataMax') {
				return $flag;
			}

			$flag = $classCalcConsumptionTax->allot(array(
				'flagStatus'      => 'add',
				'arrRows'         => $arrRows,
				'numFiscalPeriod' => $numFiscalPeriod,
			));
			if ($flag == 'errorDataMax') {
				return $flag;
			}

			$classCalcLogCalc->allot(array(
				'flagStatus'      => 'add',
				'arrRows'         => $arrRows,
				'numFiscalPeriod' => $numFiscalPeriod,
			));

			if ($numFiscalPeriod == $numEnd) {
				break;
			}

			$numStartNext = $numFiscalPeriod + 1;
			for ($j = $numStartNext ; $j <= $numEnd; $j++) {
				$numFiscalPeriodNext = $j;
				$flag = $classCalcTempNextLog->allot(array(
					'flagStatus'      => 'add',
					'numFiscalPeriod' => $numFiscalPeriodNext,
					'arrRows'         => $arrRows,
				));
				if ($flag == 'errorDataMax') {
					return $flag;
				}
			}
		}
		$this->_updateDbPreferenceStamp(array('strColumn' => 'fsValue'));
		$this->_updateDbPreferenceStamp(array('strColumn' => 'entityDepartmentFSValue'));
	}
}
