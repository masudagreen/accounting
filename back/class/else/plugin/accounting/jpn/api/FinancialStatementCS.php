<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_API_Jpn_FinancialStatementCS extends Code_Else_Plugin_Accounting_Jpn_API
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
		'method'  => 'getFSCS',
		'params'  => array(
			'idEntity'        => 1,
			'numFiscalPeriod' => 1,
			'flagDirect'      => 1,
		),
	 */
	protected function _iniGetFSCS()
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

		$idEntity = $this->_checkIdEntityCurrent();
		$numFiscalPeriod = $this->_checkNumFiscalPeriodCurrent(array('idEntity' => $idEntity));

		$flagDirect = ($varsRequest['query']['api']['params']['flagDirect'])? 1 : 0;

		$varsFlag = array();
		$varsFlag['flagFS'] = 'CS';
		$varsFlag['flagDirect'] = $flagDirect;
		$varsFlag['flagUnit'] = 0;
		$varsFlag['flagCalc'] = 'floor';

		$varsItem = array();
		$varsItem['varsFlag'] = $varsFlag;

		return $varsItem;
	}

	/**
		(array(
			'flag' => '',
			'vars' => $vars,
		))
	 */
	protected function _getVarsItem($arr)
	{
		global $varsPluginAccountingAccount;

		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsFSValue = $this->_getVarsFSValue(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$str = 'jsonJgaapFS' . $arr['varsItem']['varsFlag']['flagFS'];

		$varsFlagFiscalPeriod = $this->_getVarsFlagFiscalPeriod(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$data = array(
			'varsFS'               => $varsFS[$str],
			'varsFSValue'          => $varsFSValue[$str],
			'stampUpdate'          => $varsFSValue['stampUpdate'],
			'varsEntityNation'     => $varsEntityNation,
			'varsFlagFiscalPeriod' => $varsFlagFiscalPeriod,
			'varsFlag'             => $arr['varsItem']['varsFlag'],
		);

		return $data;
	}



	/**
		(array(
			'varsItem' => $varsItem,
		))
	 */
	protected function _updateVars($arr)
	{
		global $varsAccount;
		global $classEscape;

		$strFlagDirect = 'varsInDirect';
		if ($arr['varsItem']['varsFlag']['flagDirect']) {
			$strFlagDirect = 'varsDirect';
		}

		$varsData = $this->_getAccountTitleValue(array(
			'varsItem'    => $arr['varsItem'],
			'varsFS'      => $arr['varsItem']['varsFS'][$strFlagDirect],
			'varsFSValue' => $arr['varsItem']['varsFSValue'][$strFlagDirect],
		));

		$stampUpdate = $arr['varsItem']['stampUpdate'];
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
			'varsFS'      => $arr['varsItem']['varsFS'],
			'varsFSValue' => $arr['varsItem']['varsFSValue'],
			'varsFlag'         => array(
				'flagFiscalPeriod'  => $flagFiscalPeriod,
				'flagFS'            => $flagFS,
				'flagUnit'          => $flagUnit,
				'flagCalc'          => $flagCalc,
			),
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
				$array[$key][$str] = $value['vars'][$str];
			}
			$str = 'flagDefault';
			if (!is_null($value[$str])) {
				$array[$key][$str] = $value[$str];
			}
			$str = 'varsValue';
			if (!is_null($value['vars'][$str])) {
				$array[$key][$str] = $value['vars'][$str];
			}

			$str = 'flagCalc';
			if (!is_null($value['vars'][$str])) {
				$array[$key][$str] = $value['vars'][$str];
			}

			$arrayUnset = array(
				'vars'
			);
			foreach ($arrayUnset as $keyUnset => $valueUnset) {
				unset($array[$key][$valueUnset]);
			}

			if ($value['child']) {
				$array[$key]['child'] = $this->_getAccountTitleValue(array(
					'varsFSValue' => $arr['varsFSValue'],
					'varsItem'    => $arr['varsItem'],
					'varsFS'      => $array[$key]['child'],
					'idParent'    => $value['vars']['idTarget'],
				));
			}
		}

		return $array;
	}


}
