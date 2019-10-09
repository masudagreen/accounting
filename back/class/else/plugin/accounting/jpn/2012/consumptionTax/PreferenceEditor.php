<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_2012_ConsumptionTax_PreferenceEditor extends Code_Else_Plugin_Accounting_Jpn_2012_ConsumptionTax_Preference
{
	protected $_childSelf = array(
		'pathTplJs'   => 'else/plugin/accounting/js/jpn/2012/consumptionTax/preferenceEditor.js',
		'pathVarsJs'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/2012/consumptionTax/preferenceEditor.php',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;

		$flag = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done|tempNext)$/", $flag)) {
			$this->_sendOld();
		}

		if ($varsRequest['query']['func']) {
			$method = '_ini' . $varsRequest['query']['func'];
			if (method_exists($this, $method)) {
				$this->$method();

			} else {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
				}
				exit;
			}
		}

		exit;
	}

	/**
	 *
	 */
	protected function _iniJs()
	{
		$this->_setJsEditor(array(
			'pathVars'  => $this->_childSelf['pathVarsJs'],
			'pathTpl'   => $this->_childSelf['pathTplJs'],
			'arrFolder' => array(),
		));
	}


	/**
			'varsList' => $vars['varsItem']['varsList'],
			'varsData' => $varsRequest['query']['jsonValue']['vars']['JsonData'],
			'varsFlag' => $varsFlag,
	 */
	protected function _checkVarsValue($arr)
	{
		global $classCheck;

		$flagError = 0;
		$array = &$arr['varsValue'];
		foreach ($array as $key => $value) {
			if ($value == '') {
				continue;
			}
			$flag = $classCheck->checkValueMax(array(
				'flagType' => 'str',
				'value'    => $value,
				'num'      => 9,
			));
			if ($flag) {
				$array[$key] = '';
				$flagError = 1;
			}
		}
		if ($flagError) {
			return 'strOver';
		}
		return 1;
	}


	/**
	 *
	 */
	protected function _iniDetailEdit()
	{
		global $classDb;
		global $classEscape;

		global $varsRequest;
		global $varsAccount;
		$dbh = $classDb->getHandle();
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'])) {
			$this->_sendOld();
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsFlag = array(
			'flagMenu' => $varsRequest['query']['jsonValue']['vars']['VarsFlag']['flagMenu'],
		);

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$this->_checkValueDetail(array(
			'varsDetail' => $vars['portal']['varsNavi']['varsDetail'],
			'varsFlag'   => $varsFlag,
		));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		));

		$arrValue = $this->_checkValueDetailList(array(
			'varsItem' => $varsItem,
			'varsList' => $vars['varsItem']['varsList'],
			'varsData' => $varsRequest['query']['jsonValue']['vars']['JsonData'],
			'varsFlag' => $varsFlag,
		));

		try {
			$dbh->beginTransaction();

			$this->_updateDbLog(array(
				'vars'     => $vars,
				'varsItem' => $varsItem,
				'arrValue' => $arrValue,
				'varsFlag' => $varsFlag,
			));
			$this->_updateDbPreferenceStamp(array('strColumn' => 'consumptionTax'));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
		));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		));

		$this->sendVars(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'varsDetail'         => $vars['portal']['varsDetail']['varsDetail'],
				'varsFlag'           => $varsFlag,
				'varsSave'           => $vars['varsItem']['varsSave'],
				'varsList'           => $vars['varsItem']['varsList'],
				'flagBtnCalc'        => $vars['varsItem']['flagBtnCalc'],
			),
		));
	}


	/**
			'varsList' => $vars['varsItem']['varsList'],
			'varsData' => $varsRequest['query']['jsonValue']['vars']['JsonData'],
			'varsFlag' => $varsFlag,
	 */
	protected function _checkValueDetailList($arr)
	{
		$arrayCheck = array();
		$array = $arr['varsList'];
		foreach ($array as $key => $value) {
			$arrayCheck[$value['idTarget']] = $value;
		}
		$numAll = count($arrayCheck);

		$varsValue = array();
		$array = $arr['varsData'];

		foreach ($array as $key => $value) {
			$data = $arrayCheck[$key];
			if (is_null($data)) {
				$this->_sendOld();
			}
			$data['value'] = $value;
			$numAll--;
			$dataValue = $this->checkValue(array(
				'values' => array($data),
			));
			if ($data['flagValueType'] == 'select') {
				$arrayOption = $data['arrayOption'];
				$flag = 0;
				foreach ($arrayOption as $keyOption => $valueOption) {
					if ($data['value'] == $valueOption['value']) {
						$flag = 1;
						break;
					}
				}
				if (!$flag) {
					$this->_sendOld();
				}

			}
			if ($data['flagForm'] == 'active') {
				$varsValue[$key] = $value;
			}
		}

		if ($numAll != 0) {
			$this->_sendOld();
		}

		$varsData = $arr['varsItem']['varsSave']['jsonData'];

		if (!$varsData) {
			$varsData = array();
		}
		$varsData[$arr['varsFlag']['flagMenu']] = $varsValue;

		$arrValue['arr']['jsonData'] = $varsData;

		return $arrValue;

	}

	/**

	 */
	protected function _updateDbLog($arr)
	{
		global $classEscape;
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsRequest;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$stampRegister = TIMESTAMP;
		$stampUpdate = TIMESTAMP;
		$flagReport = $this->_extSelf['flagReport'];
		$flagDetail = $this->_extSelf['flagDetail'];
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];

		$jsonData = json_encode($arr['arrValue']['arr']['jsonData']);

		//update
		if ($arr['varsItem']['varsSave']) {
			$arrColumn = array(
				'stampUpdate',
				'jsonData',
			);
			$arrValue = array(
				$stampUpdate,
				$jsonData,
			);
			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingConsumptionTax' . $strNation,
				'arrColumn' => $arrColumn,
				'flagAnd'  => 1,
				'arrWhere'  => array(
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
						'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					),
					array(
						'flagType'      => '',
						'strColumn'     => 'flagReport',
						'flagCondition' => 'eq',
						'value'         => $flagReport,
					),
					array(
						'flagType'      => '',
						'strColumn'     => 'flagDetail',
						'flagCondition' => 'eq',
						'value'         => $flagDetail,
					),
				),
				'arrValue'  => $arrValue,
			));

		//insert
		} else {
			$arrColumn = array(
				'stampRegister',
				'stampUpdate',
				'idEntity',
				'numFiscalPeriod',
				'flagReport',
				'flagDetail',
				'jsonData',
			);
			$arrValue = array(
				$stampRegister,
				$stampUpdate,
				$idEntity,
				$numFiscalPeriod,
				$flagReport,
				$flagDetail,
				$jsonData,
			);

			$id = $classDb->insertRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingConsumptionTax' . $strNation,
				'arrColumn' => $arrColumn,
				'arrValue'  => $arrValue,
			));
		}

	}

}
