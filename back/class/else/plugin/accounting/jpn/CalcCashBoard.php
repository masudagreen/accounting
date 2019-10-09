<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcCashBoard extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extChildSelf = array();

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
			'flagStatus'      => 'num',
			'varsAccount'     => $varsAccount,
			'varsAuthority'   => $varsAuthority,
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
		))
	 */
	protected function _iniNum($arr)
	{
		$data = array();
		$data['numRegister'] = $this->_checkNumRegister($arr);
		$data['numDefer'] = $this->_checkNumDefer($arr);

		return $data;
	}

	/**
		(array(

		))
	 */
	protected function _checkNumRegister($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsPluginAccountingAccountsEntity;
		global $varsPluginAccountingAuthority;
		$varsAccount = $arr['varsAccount'];

		$idAccount = $varsAccount['id'];
		if (!($arr['varsAuthority'] == 'admin'
			|| $this->batchIllegalStringOffset($arr['varsAuthority'], 'flagAllSelect')
			|| $this->batchIllegalStringOffset($arr['varsAuthority'], 'flagMySelect')
		)) {
			return 0;
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
				'strColumn'     => 'flagRemove',
				'flagCondition' => 'eq',
				'value'         => 0,
			),
		);

		if ($this->batchIllegalStringOffset($arr['varsAuthority'], 'flagAllSelect')
			|| $arr['varsAuthority'] == 'admin'
		) {

		} elseif ($this->batchIllegalStringOffset($arr['varsAuthority'], 'flagMySelect')) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idAccount',
				'flagCondition' => 'eq',
				'value'         => $idAccount,
			);
		}
		$idEntity = $arr['idEntity'];
		$numFiscalPeriod = $arr['numFiscalPeriod'];
		$stampRegister = $varsAccount['jsonStampCheck']['accountingLogCash_' . $idEntity . '_' . $numFiscalPeriod];
		if (!is_null($stampRegister)) {
			$arrWhere[] = array(
				'flagType'      => '',
				'strColumn'     => 'stampRegister',
				'flagCondition' => 'big',
				'value'         => $stampRegister,
			);
		}

		$rows = $classDb->getSelect(array(
			'idModule'   => 'accounting',
			'strTable'   => 'accountingLogCash',
			'arrLimit'    => array(
				'numStart' => 0, 'numEnd' => 1,
			),
			'arrOrder'   => array(),
			'flagAnd'    => 1,
			'arrWhere'   => $arrWhere,
		));

		if (!$rows['numRows']) {
			return 0;
		}

		return $rows['numRows'];
	}

	/**
		(array(
		))
	 */
	protected function _checkNumDefer($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		if (!($arr['varsAuthority'] == 'admin'
			|| $this->batchIllegalStringOffset($arr['varsAuthority'], 'flagAllSelect')
		)) {
			return 0;
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
		);

		$idEntity = $arr['idEntity'];
		$numFiscalPeriod = $arr['numFiscalPeriod'];
		$stampRegister = $arr['varsAccount']['jsonStampCheck']['accountingLogCashDefer_' . $idEntity . '_' . $numFiscalPeriod];
		if (!is_null($stampRegister)) {
			$arrWhere[] = array(
				'flagType'      => '',
				'strColumn'     => 'stampRegister',
				'flagCondition' => 'big',
				'value'         => $stampRegister,
			);
		}

		$rows = $classDb->getSelect(array(
			'idModule'   => 'accounting',
			'strTable'   => 'accountingLogCashDefer',
			'arrLimit'    => array(
				'numStart' => 0, 'numEnd' => 1,
			),
			'arrOrder'   => array(),
			'flagAnd'    => 1,
			'arrWhere'   => $arrWhere,
		));

		if (!$rows['numRows']) {
			return 0;
		}

		return $rows['numRows'];
	}

	/**
		(array(
			'flagStatus'      => 'data',
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			'varsCash'        => $value['varsBoard']['varsCash'],
		))
	 */
	protected function _iniData($arr)
	{
		$varsItem = $this->_getVarsItem(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $arr['idEntity'],
		));

		$varsCash = $this->_updateVars(array(
			'varsCash' => $arr['varsCash'],
			'varsItem' => $varsItem,
		));

		return $varsCash;
	}

	/**
		(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _getVarsItem($arr)
	{
		$varsValue = $this->_getVarsValueCash(array(
			'numFiscalPeriod'      => $arr['numFiscalPeriod'],
			'numFiscalPeriodValue' => $arr['numFiscalPeriod'],
			'idEntity'             => $arr['idEntity'],
		));

		$data = array(
			'varsValue' => $varsValue,
		);

		return $data;
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
					'value'         => 1,
				),
			),
		));

		if (!$rows['numRows']) {
			return array();
		}

		$data = $rows['arrRows'][0]['jsonData']['f1'];
		if (is_null($data)) {
			return array();
		}
		return $data;
	}

	/**
		(array(
			'vars'             => $vars,
			'varsEntityNation' => $varsEntityNation,
			'varsItem'         => $varsItem,
			'varsFlag'         => array(
				'flagFiscalPeriod'  => $flagFiscalPeriod,
			),
			'flagOutput'       => ($arr['flagOutput'])? 1 : 0,
		))
	 */
	protected function _updateVars($arr)
	{
		$arr['varsCash']['varsCollect']['varsBase']['f1'] = $this->_getVarsValue(array(
			'varsItem' => $arr['varsItem'],
		));

		$varsLabelId = array();
		$varsLabel = array();
		$array = $arr['varsCash']['varsRow'];
		foreach ($array as $key => $value) {
			$varsLabel[$key] = $value;
			$varsLabelId[] = $key;
		}
		$arr['varsCash']['varsCollect']['varsLabel'] = $varsLabel;
		$arr['varsCash']['varsCollect']['varsLabelId'] = $varsLabelId;

		return $arr['varsCash'];
	}

	/**

	 */
	protected function _getVarsValue($arr)
	{
		$varsValue = $arr['varsItem']['varsValue'];

		$varsData = array();
		$varsData['numIn'] = (is_null($varsValue['sumIn']))? 0 : $varsValue['sumIn'];
		$varsData['numOut'] = (is_null($varsValue['sumOut']))? 0 : $varsValue['sumOut'];
		$varsData['numNet'] = $varsData['numIn'] - $varsData['numOut'];

		$varsData['numInComma'] = number_format($varsData['numIn']);
		$varsData['numOutComma'] = number_format($varsData['numOut']);
		$varsData['numNetComma'] = number_format($varsData['numNet']);

		return $varsData;
	}
}
