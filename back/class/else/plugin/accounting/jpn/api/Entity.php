<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_API_Jpn_Entity extends Code_Else_Plugin_Accounting_Jpn_API
{
	protected $_extSelf = array(
		'pathVars' => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/api/entity.php',
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
		'session' => $session,
		'module'  => 'accounting',
		'method'  => 'getEntity',
		'params'  => array(),
	 */
	protected function _iniGetEntity()
	{
		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVars'],
		));

		$data = $this->_updgetVars(array(
			'vars' => $vars,
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

	 */
	protected function _updgetVars($arr)
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingPreference;
		global $classEscape;

		$varsData = array();
		$array = $varsPluginAccountingEntity;
		foreach ($array as $key => $value) {
			if ($value['flagConfig']) {
				continue;
			}
			$temp = array();
			$temp['id'] = $classEscape->toInt(array('data' => $key));
			$temp['strTitle'] = $value['strTitle'];
			$temp['numFiscalPeriodStart'] = $classEscape->toInt(array('data' => $value['numFiscalPeriodStart']));
			$temp['numFiscalPeriod'] = $classEscape->toInt(array('data' => $value['numFiscalPeriod']));
			$temp['numFiscalPeriodLock'] = $classEscape->toInt(array('data' => $value['numFiscalPeriodLock']));

			$varsEntityNation = $this->_getVarsEntityNation(array(
				'numFiscalPeriod' => $value['numFiscalPeriodStart'],
			));
			if ($varsEntityNation['flagCorporation'] != 1) {
				continue;
			}
			//$temp['flagCorporation'] = $varsEntityNation['flagCorporation'];
			$temp['flagCR'] = $classEscape->toInt(array('data' => $varsEntityNation['flagCR']));
			$temp['strCorporation'] = $arr['vars']['varsFlagCorporation'][$varsEntityNation['flagCorporation']];
			$varsPeriod = array();
			$numFiscalPeriodStart = $value['numFiscalPeriodStart'];
			$numFiscalPeriodEnd = $value['numFiscalPeriod'];
			for ($i = $numFiscalPeriodStart ; $i <= $numFiscalPeriodEnd; $i++) {
				$numFiscalPeriod = $i;
				$varsPeriod[$numFiscalPeriod]['numFiscalPeriod'] = $classEscape->toInt(array('data' => $numFiscalPeriod));
				$varsPeriod[$numFiscalPeriod]['numFiscalTermMonth'] = $classEscape->toInt(array('data' => $varsEntityNation['numFiscalTermMonth']));
				$varsPeriod[$numFiscalPeriod]['stampFiscalBeginning'] = $classEscape->toInt(array('data' => $varsEntityNation['stampFiscalBeginning']));
			}
			$temp['varsPeriod'] = $varsPeriod;
			$varsData[$key] = $temp;
		}

		$stampUpdate = $varsPluginAccountingPreference['jsonStampUpdate']['entity'];
		if (!$stampUpdate) {
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
