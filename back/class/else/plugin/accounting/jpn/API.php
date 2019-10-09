<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_API extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extMethodClass = array(
		'getEntity'       => 'Entity',
		'setCSVLog'       => 'LogImport',
		'getTrialBalance' => 'TrialBalance',
		'getFS'           => 'FinancialStatement',
		'getFSCS'         => 'FinancialStatementCS',
		'getDepartment'   => 'EntityDepartment',
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
	 *
	 * */
	public function allot()
	{
		global $varsRequest;

		$this->_setInitJpn();

		$str = $this->_extMethodClass[$varsRequest['query']['api']['method']];
		if (is_null($str)) {
			$this->_sendJSON(array('data' => array('flag' => 'methodNotExist', 'data' => (FLAG_TEST)? __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__ : ''),));
			exit;
		}

		$str = $this->_extMethodClass[$varsRequest['query']['api']['method']];
		$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/' . PLUGIN_ACCOUNTING_STR_NATION . '/api/' . $str . ".php";
		$strClass = 'Code_Else_Plugin_Accounting_API_' . ucwords(PLUGIN_ACCOUNTING_STR_NATION) . '_' . $str;
		if (!file_exists($path)) {
			$this->_sendJSON(array('data' => array('flag' => 'fileNotExist', 'data' => (FLAG_TEST)? __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__ : ''),));
			exit;
		}
		require_once($path);
		$classCall = new $strClass;
		$classCall->run();
	}

	/**
		(array(

		))
	 */
	protected function _checkIdEntityCurrent()
	{
		global $varsRequest;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$idEntity = $varsRequest['query']['api']['params']['idEntity'];
		$varsEntity = $varsPluginAccountingEntity[$idEntity];
		if (!$varsEntity) {
			$this->_sendJSON(array('data' => array('flag' => 'entityNotExist',),));
		}
		$varsPluginAccountingAccount['idEntityCurrent'] = $idEntity;

		return $idEntity;
	}

	/**
		(array(

		))
	 */
	protected function _checkNumFiscalPeriodCurrent($arr)
	{
		global $varsRequest;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$idEntity = $arr['idEntity'];
		$varsEntity = $varsPluginAccountingEntity[$idEntity];

		$numFiscalPeriod = $varsRequest['query']['api']['params']['numFiscalPeriod'];
		$numFiscalPeriodStart = $varsEntity['numFiscalPeriodStart'];
		$numFiscalPeriodEnd = $varsEntity['numFiscalPeriod'];
		if (!($numFiscalPeriodStart <= $numFiscalPeriod && $numFiscalPeriod <= $numFiscalPeriodEnd)) {
			$this->_sendJSON(array('data' => array('flag' => 'numFiscalPeriodNotExist',),));
		}
		$varsPluginAccountingAccount['numFiscalPeriodCurrent'] = $numFiscalPeriod;

		return $numFiscalPeriod;
	}

	/**
     *
     */
	protected function _sendJSON($arr)
	{
		global $classRequest;

		$json = json_encode($arr['data']);

		$classRequest->send(array(
			'flagType' => 'json',
			'data'     => $json,
		));
		exit;
	}
}
