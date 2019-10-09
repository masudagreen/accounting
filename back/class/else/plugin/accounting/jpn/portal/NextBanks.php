<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_Portal_NextBanks extends Code_Else_Plugin_Accounting_Jpn_Portal
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

	public function allot($arr)
	{
		$method = '_ini' . ucwords($arr['flagStatus']);
		if (method_exists($this, $method)) {
			$this->$method($arr);

		} else {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}
	}

	/**
		(array(
			'numFiscalPeriod'  => $numFiscalPeriod,
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _iniInsert($arr)
	{
		$this->_setInsertPreference($arr);
		$this->_setInsertLogAccount($arr);
	}

	/**
		(array(
			'numFiscalPeriod'  => $numFiscalPeriod,
			'varsEntityNation'   => $varsEntityNation,
		))
	 */
	protected function _setInsertPreference($arr)
	{
		$rows = $this->_getVarsPreference(array(
			'numFiscalPeriod' => $arr['varsEntityNation']['numFiscalPeriod'],
		));

		$array = $rows['arrRows'];
		foreach ($array as $key => $value) {
			$this->_setInsertPreferenceLoop(array(
				'varsLog'          => $value,
				'varsEntityNation' => $arr['varsEntityNation'],
				'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			));
			break;
		}
	}

	/**
		(array(

		))
	 */
	protected function _getVarsPreference($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingBanks',
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
			),
		));

		return $rows;
	}

	/**
		(array(
			'varsLog'          => $value,
			'varsEntityNation' => $arr['varsEntityNation'],
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
		))
	 */
	protected function _setInsertPreferenceLoop($arr)
	{
		global $classDb;

		$arrDbColumn = array();
		$arrDbValue = array();
		$array = $arr['varsLog'];
		foreach ($array as $key => $value) {
			if ($key == 'id') {
				continue;

			} elseif ($key == 'numFiscalPeriod') {
				$value = $arr['numFiscalPeriod'];

			} elseif (preg_match("/^json/", $key)) {
				if (!$value) {
					$value = array();
				}
				$value = json_encode($value);
			}

			$arrDbColumn[] = $key;
			$arrDbValue[] = $value;
		}

		$classDb->insertRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingBanks',
			'arrColumn' => $arrDbColumn,
			'arrValue'  => $arrDbValue,
		));
	}

	/**
		(array(
			'numFiscalPeriod'  => $numFiscalPeriod,
			'varsEntityNation'   => $varsEntityNation,
		))
	 */
	protected function _setInsertLogAccount($arr)
	{
		$rows = $this->_getVarsLogAccount(array(
			'numFiscalPeriod' => $arr['varsEntityNation']['numFiscalPeriod'],
		));

		$array = $rows['arrRows'];
		foreach ($array as $key => $value) {
			$this->_setInsertLogAccountLoop(array(
				'varsLog'          => $value,
				'varsEntityNation' => $arr['varsEntityNation'],
				'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			));
		}
	}

	/**
		(array(

		))
	 */
	protected function _getVarsLogAccount($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogBanksAccount',
			'arrLimit' => array(),
			'arrOrder'  => array(
				'strColumn' => 'idLogAccount',
				'flagDesc'  => 0,
			),
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
			),
		));

		return $rows;
	}

	/**
		(array(
			'varsLog'          => $value,
			'varsEntityNation' => $arr['varsEntityNation'],
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
		))
	 */
	protected function _setInsertLogAccountLoop($arr)
	{
		global $classDb;

		$arrDbColumn = array();
		$arrDbValue = array();
		$array = $arr['varsLog'];
		foreach ($array as $key => $value) {
			if ($key == 'id') {
				continue;

			} elseif ($key == 'stampUpdate') {
				$value = TIMESTAMP;

			} elseif ($key == 'numFiscalPeriod') {
				$value = $arr['numFiscalPeriod'];

			} elseif (preg_match("/^json/", $key)) {
				if (!$value) {
					$value = array();
				}
				$value = json_encode($value);
			}

			$arrDbColumn[] = $key;
			$arrDbValue[] = $value;
		}

		$classDb->insertRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingLogBanksAccount',
			'arrColumn' => $arrDbColumn,
			'arrValue'  => $arrDbValue,
		));
	}
}
