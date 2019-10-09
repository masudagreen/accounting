<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_Portal_NextCorporateTax extends Code_Else_Plugin_Accounting_Jpn_Portal
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
		$this->_setInsertLog($arr);
	}

	/**
		(array(
			'numFiscalPeriod'  => $numFiscalPeriod,
			'varsEntityNation'   => $varsEntityNation,
		))
	 */
	protected function _setInsertLog($arr)
	{
		$rows = $this->_getVarsLog(array(
			'numFiscalPeriod' => $arr['varsEntityNation']['numFiscalPeriod'],
		));

		$array = $rows['arrRows'];
		foreach ($array as $key => $value) {
			$this->_setInsertLogLoop(array(
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
	protected function _getVarsLog($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingCorporateTax' . $strNation,
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
	protected function _setInsertLogLoop($arr)
	{
		global $classDb;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$arrAccountTitle = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => array(),
			'numFiscalPeriod'    => $arr['numFiscalPeriod'],
		));

		$arrDbColumn = array();
		$arrDbValue = array();
		$array = $arr['varsLog'];
		foreach ($array as $key => $value) {
			if ($key == 'id') {
				continue;

			} elseif ($key == 'numFiscalPeriod') {
				$value = $arr['numFiscalPeriod'];

			} elseif (preg_match("/^json/", $key)) {
				$value = array();
				$value = json_encode($value);
			}
			$arrDbColumn[] = $key;
			$arrDbValue[] = $value;
		}

		$classDb->insertRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingCorporateTax' . $strNation,
			'arrColumn' => $arrDbColumn,
			'arrValue'  => $arrDbValue,
		));
	}
}
