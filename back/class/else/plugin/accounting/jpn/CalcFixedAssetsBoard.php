<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcFixedAssetsBoard extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extChildSelf = array(

	);

	/**

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
			'flagStatus'      => 'num',
			'varsAccount'     => $varsAccount,
			'strNation'       => ucwords(PLUGIN_ACCOUNTING_STR_NATION),
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
		))
	 */
	protected function _iniNum($arr)
	{
		$data = array();
		$data['numRegister'] = $this->_checkNumRegister($arr);

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

		$varsAccount = $arr['varsAccount'];

		$idAccount = $varsAccount['id'];
		if (!($arr['varsAuthority'] == 'admin'
			|| $this->batchIllegalStringOffset($arr['varsAuthority'], 'flagAllSelect')
			|| $this->batchIllegalStringOffset($arr['varsAuthority'], 'flagMySelect')
		)) {
			return 0;
		}

		$strNation = $arr['strNation'];

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
		$stampRegister = $arr['varsAccount']['jsonStampCheck']['accountingLogFixedAssets_' . $idEntity . '_' . $numFiscalPeriod];
		if (!is_null($stampRegister)) {
			$arrWhere[] = array(
				'flagType'      => '',
				'strColumn'     => 'stampRegister',
				'flagCondition' => 'big',
				'value'         => $stampRegister,
			);
		}

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogFixedAssets' . $strNation,
			'arrLimit'    => array(
				'numStart' => 0, 'numEnd' => 1,
			),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => $arrWhere,
		));

		if (!$rows['numRows']) {
			return 0;
		}

		return $rows['numRows'];
	}
}
