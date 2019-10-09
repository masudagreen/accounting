<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_API_Jpn_FinancialStatement extends Code_Else_Plugin_Accounting_Jpn_API
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
		'method'  => 'getFS',
		'params'  => array(
			'idEntity'        => 1,
			'numFiscalPeriod' => 1,
			'flagFS'          => 'BS',
			'idDepartment'    => 1,
		),
	 */
	protected function _iniGetFS()
	{
		$varsItem = $this->_checkParams();

		$varsItem = $this->_getVarsItem(array(
			'varsItem' => $varsItem,
		));

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

		))
	 */
	protected function _checkParams()
	{
		global $varsRequest;
		global $varsPluginAccountingAccount;

		$idEntity = $this->_checkIdEntityCurrent();
		$numFiscalPeriod = $this->_checkNumFiscalPeriodCurrent(array('idEntity' => $idEntity));

		$flagFS = $varsRequest['query']['api']['params']['flagFS'];
		$varsFSList = $this->_getFSList(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));
		if (!$varsFSList[$flagFS]) {
			$this->_sendJSON(array('data' => array('flag' => 'flagFSNotExist', 'data' => (FLAG_TEST)? __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__ : array()),));
		}

		$varsDepartment = $this->_getVarsDepartment(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));
		$idDepartment = $varsRequest['query']['api']['params']['idDepartment'];
		if (!is_null($idDepartment)) {
			if (!$varsDepartment['arrStrTitle'][$idDepartment]) {
				$this->_sendJSON(array('data' => array('flag' => 'idDepartmentNotExist', 'data' => (FLAG_TEST)? __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__ : array()),));
			}
		}

		$varsFlag = array();
		$varsFlag['flagFS'] = $flagFS;
		$varsFlag['flagUnit'] = 0;
		$varsFlag['flagCalc'] = 'floor';
		$varsFlag['idDepartment'] = $idDepartment;
		if (is_null($idDepartment)) {
			$varsFlag['idDepartment'] = 'none';
		}

		$varsItem = array();
		$varsItem['varsDepartment'] = $varsDepartment;
		$varsItem['varsFlag'] = $varsFlag;

		return $varsItem;
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

		$varsItem = $arr['varsItem'];

		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsFSValue = $this->_getVarsFSValue(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsDepartment = &$arr['varsItem']['varsDepartment'];

		if ($arr['varsItem']['varsFlag']['idDepartment'] != 'none') {
			$varsFSValue = $this->_getVarsFSValueDepartment(array(
				'idDepartment'    => $arr['varsItem']['varsFlag']['idDepartment'],
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			));
		}

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsFlagFiscalPeriod = $this->_getVarsFlagFiscalPeriod(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsItem['varsFS']               = $varsFS;
		$varsItem['varsFSValue']          = $varsFSValue;
		$varsItem['varsDepartment']       = $varsDepartment;
		$varsItem['varsEntityNation']     = $varsEntityNation;
		$varsItem['varsFlagFiscalPeriod'] = $varsFlagFiscalPeriod;

		return $varsItem;
	}

	/**
		(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $vars['varsFlag'],
		))
	 */
	protected function _updateVars($arr)
	{
		global $varsPluginAccountingPreference;
		global $classEscape;

		$varsFS = $arr['varsItem']['varsFS']['jsonJgaapFS' . $arr['varsItem']['varsFlag']['flagFS']];
		if ($arr['varsItem']['varsFlag']['flagFS'] == 'BS' && $arr['varsItem']['varsFlag']['idDepartment'] != 'none') {
			$varsDepartmentTreeItem = $this->_getVarsDepartmentTreeItem();
			$arrayNew = array();
			$array = $varsFS;
			foreach ($array as $key => $value) {
				$arrayNew[] = $value;
				if ($value['vars']['idTarget'] == 'netAssetsSum') {
					$arrayNew[] = $varsDepartmentTreeItem;
				}
			}
			$varsFS = $arrayNew;
		}

		$varsData = $this->_getAccountTitleValue(array(
			'vars'        => $arr['vars'],
			'varsFS'      => $varsFS,
			'varsFSValue' => $arr['varsItem']['varsFSValue']['jsonJgaapFS' . $arr['varsItem']['varsFlag']['flagFS']],
			'varsItem'    => $arr['varsItem'],
		));

		$stampUpdate = $arr['varsItem']['varsFSValue']['stampUpdate'];
		if (!$stampUpdate) {
			$stampUpdate = 0;
		}
		$stampUpdate = $classEscape->toInt(array('data' => $stampUpdate));

		$data = array(
			'stampUpdate' => $stampUpdate,
			'varsTree'    => $varsData,
		);

		return $data;
	}

	/**
		(array(
			'vars'        => $arr['vars'],
			'varsFS'      => $varsFS,
			'varsFSValue' => $arr['varsItem']['varsFSValue']['jsonJgaapFS' . $arr['varsFlag']['flagFS']],
			'varsFlag'    => $arr['varsFlag'],
		))

	 */
	protected function _getAccountTitleValue($arr)
	{
		global $classEscape;

		$array = &$arr['varsFS'];

		$flagUnit = (int) $arr['varsItem']['varsFlag']['flagUnit'];
		$flagCalc = $arr['varsItem']['varsFlag']['flagCalc'];
		$varsFSValue = $arr['varsFSValue'];

		foreach ($array as $key => $value) {
			if (!is_null($array[$key]['vars']['varsValue'])) {
				$idTarget = $value['vars']['idTarget'];

				$arrayData = $arr['varsItem']['varsFlagFiscalPeriod'];
				foreach ($arrayData as $keyData => $valueData) {
					$flagFiscalPeriod = $valueData;
					$numData = $varsFSValue[$flagFiscalPeriod][$idTarget]['sumNext'];
					if (!is_null($numData)) {
						$numData =  $numData;
						if ($flagUnit == 0) {
							$numValue = $numData;

						} else {
							if ($flagCalc == 'floor') {
								$numValue = floor($numData / $flagUnit);

							} elseif ($flagCalc == 'round') {
								$numValue = round($numData / $flagUnit);

							} elseif ($flagCalc == 'ceil') {
								$numValue = ceil($numData / $flagUnit);
							}
						}
					} else {
						$numValue = 0;
					}
					$numValue = $classEscape->toInt(array('data' => $numValue));
					$value['vars']['varsValue'][$flagFiscalPeriod] = $numValue;
				}
			}

			$arrayUnset = array(
				'id', 'flagUse', 'flagMoveUse', 'flagInsertUse', 'flagBtnUse', 'flagFoldUse',
				'flagFoldNow', 'flagChildrenUse', 'strClass', 'flagDefault', 'strTitle'
			);
			foreach ($arrayUnset as $keyUnset => $valueUnset) {
				unset($array[$key][$valueUnset]);
			}

			$array[$key]['id'] = $value['vars']['idTarget'];
			$array[$key]['strTitle'] = $value['strTitle'];
			$str = 'flagUse';
			if (!is_null($value['vars'][$str])) {
				$array[$key][$str] = $classEscape->toInt(array('data' => $value['vars'][$str]));
			}
			$str = 'flagDebit';
			if (!is_null($value['vars'][$str])) {
				$array[$key][$str] = $classEscape->toInt(array('data' => $value['vars'][$str]));
			}
			$str = 'flagDefault';
			if (!is_null($value[$str])) {
				$array[$key][$str] = $classEscape->toInt(array('data' => $value[$str]));
			}
			$str = 'varsValue';
			if (!is_null($value['vars'][$str])) {
				$array[$key][$str] = $value['vars'][$str];
			}

			$str = 'flagCalc';
			if (!is_null($value['vars'][$str])) {
				$array[$key][$str] = $value['vars'][$str];
				if ($array[$key]['id'] == 'liabilitiesNetAssetsNet') {
					$array[$key][$str] = 'sum';
				}
			}

			$arrayUnset = array(
				'vars'
			);
			foreach ($arrayUnset as $keyUnset => $valueUnset) {
				unset($array[$key][$valueUnset]);
			}

			if ($value['child']) {
				$array[$key]['child'] = $this->_getAccountTitleValue(array(
					'varsItem'    => $arr['varsItem'],
					'vars'        => $arr['vars'],
					'varsFS'      => $array[$key]['child'],
					'varsFSValue' => $arr['varsFSValue'],
					'idParent'    => $value['vars']['idTarget'],
				));
			}
		}

		return $array;
	}
}
