<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_API_Jpn_EntityDepartment extends Code_Else_Plugin_Accounting_Jpn_API
{
	protected $_extSelf = array(

	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;

		$method = '_ini' . $varsRequest['query']['api']['method'];
		$this->$method();
		exit;
	}

	/**
		'session' => string,
		'module'  => 'accounting',
		'method'  => 'getDepartment',
		'params'  => array(
			'idEntity'        => 1,
			'numFiscalPeriod' => 1,
		),
	 */
	protected function _iniGetDepartment()
	{
		$this->_checkParams();

		$varsItem = $this->_getVarsItem(array());

		$data = $this->_updateVars(array(
			'varsItem' => $varsItem,
		));

		$this->_sendJSON(array(
			'data' => array(
				'flag' => 'success',
				'data' => $data,
			)
		));
		exit;
	}

	/**
		(array(
			'vars'     => $vars,
			'varsFlag' => $vars['varsFlag'],
		))
	 */
	protected function _getVarsItem($arr)
	{
		global $varsPluginAccountingAccount;

		$varsDepartment = $this->_getVarsDepartment(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsItem = array();
		$varsItem['varsDepartment'] = $varsDepartment;

		return $varsItem;
	}

	/**
		(array(

		))
	 */
	protected function _checkParams()
	{
		$idEntity = $this->_checkIdEntityCurrent();
		$numFiscalPeriod = $this->_checkNumFiscalPeriodCurrent(array('idEntity' => $idEntity));
	}

	/**

	 */
	protected function _updateVars($arr)
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingPreference;
		global $classEscape;

		$varsData = array();
		$array = $arr['varsItem']['varsDepartment']['arrStrTitle'];
		foreach ($array as $key => $value) {
			$temp = array();
			$temp['id'] = $classEscape->toInt(array('data' => $key));
			$temp['strTitle'] = $value['strTitle'];
			$varsData[$key] = $temp;
		}

		$stampUpdate = $varsPluginAccountingPreference['jsonStampUpdate']['entityDepartment'];
		if (!$stampUpdate || !count($varsData)) {
			$stampUpdate = 0;
		}
		$stampUpdate = $classEscape->toInt(array('data' => $stampUpdate));

		$data = array(
			'stampUpdate' => $stampUpdate,
			'keyValue'    => $varsData,
		);

		return $data;
	}


}
