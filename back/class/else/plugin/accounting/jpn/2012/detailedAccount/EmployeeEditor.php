<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_2012_DetailedAccount_EmployeeEditor extends Code_Else_Plugin_Accounting_Jpn_2012_DetailedAccount_Employee
{
	protected $_childSelf = array(
		'pathTplJs'   => 'else/plugin/accounting/js/jpn/2012/detailedAccount/employeeEditor.js',
		'pathVarsJs'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/2012/detailedAccount/employeeEditor.php',
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
			'numPage' => $varsRequest['query']['jsonValue']['vars']['VarsFlag']['numPage'],
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
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
		));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		));

		$arrValue = $this->_checkValueDetailSave(array(
			'varsItem' => $varsItem,
			'varsList' => $vars['varsItem']['varsList'],
			'varsData' => $varsRequest['query']['jsonValue']['vars']['JsonData'],
			'varsFlag' => $varsFlag,
		));

		try {
			$dbh->beginTransaction();

			$method = '_updateDb' . ucwords($varsFlag['flagMenu']);
			if (method_exists($this, $method)) {
				$this->$method(array(
					'vars'     => $vars,
					'varsItem' => $varsItem,
					'arrValue' => $arrValue,
					'varsFlag' => $varsFlag,
				));
			}

			$arrayStr = array('RewardSum', 'Employee', 'EmployeeRegular', 'EmployeePrev', 'EmployeeProfit', 'Others' ,'');
			foreach ($arrayStr as $keyStr => $valueStr) {
				$this->_updateDbPage(array(
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					'flagReport'      => $this->_extSelf['flagReport'],
					'flagDetail'      => $this->_extSelf['flagDetail'],
					'flagMenu'        => 'detail',
					'numRows'         => $varsItem['varsCommon']['varsStr']['detail']['numRows'],
					'valueStr'        => $valueStr,
				));
			}

			$arrayStr = array('All', 'AllElse');
			foreach ($arrayStr as $keyStr => $valueStr) {
				$this->_updateDbPage(array(
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					'flagReport'      => $this->_extSelf['flagReport'],
					'flagDetail'      => $this->_extSelf['flagDetail'],
					'flagMenu'        => 'detail',
					'numRows'         => $varsItem['varsCommon']['varsStr']['detail']['numRowsLabor'],
					'valueStr'        => $valueStr,
				));
			}

			$this->_updateDbPreferenceStamp(array('strColumn' => 'detailedAccount'));

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

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		));

		$this->_sendData(array(
			'flag'     => 1,
			'vars'    => array(
				'varsNavi'           => $vars['portal']['varsNavi']['varsDetail'],
				'varsPreference'     => $vars['varsItem']['varsPreference'],
				'varsDetail'         => $vars['portal']['varsDetail'],
				'varsFlag'           => $varsFlag,
				'varsIni'            => $vars['varsItem']['varsIni'],
				'varsList'           => $vars['varsItem']['varsList'],
				'flagBtnCalc'        => $vars['varsItem']['flagBtnCalc'],
			),
		));
	}

	/**
		(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'flagReport'      => $arr['flagReport'],
			'flagDetail'      => $arr['flagDetail'],
		))
	 */
	protected function _updateDbPage($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$stampUpdate = TIMESTAMP;
		$flagReport = $arr['flagReport'];
		$flagDetail = $arr['flagDetail'];
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $arr['numFiscalPeriod'];

		$varsPage = $this->_getVarsSave(array(
			'numFiscalPeriod'    => $arr['numFiscalPeriod'],
			'flagReport'         => $arr['flagReport'],
			'flagDetail'         => $arr['flagDetail'],
			'flagRows'           => 1,
		));

		$varsPreference = $this->_getVarsPreference(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'flagReport'      => $arr['flagReport'],
			'flagDetail'      => $arr['flagDetail'],
		));
		$numPageMax = $varsPreference['jsonData']['numPageMax'];
		$flagMenu = $arr['flagMenu'];
		$numEnd = $arr['numRows'];
		$arrColumn = array(
			'stampUpdate',
			'jsonData',
		);

		if (!$varsPage) {
			return;
		}

		$sum = 0;
		$array = $varsPage;
		foreach ($array as $key => $value) {
			if ($value['numPage'] == 0) {
				continue;
			}
			$varsSave = $value['jsonData'][$flagMenu];

			for ($i = 1; $i <= $numEnd; $i++) {
				$idTarget = 'valueTextValue' . $arr['valueStr'] . $i;
				$data = $varsSave[$idTarget];
				if ($data) {
					$sum += $data;
				}
			}

			if ($value['numPage'] == $numPageMax) {
				$value['jsonData'][$flagMenu]['valueTextSum' . $arr['valueStr']] = $sum;

			} else {
				$value['jsonData'][$flagMenu]['valueTextSum' . $arr['valueStr']] = '';
			}

			$jsonData = json_encode($value['jsonData']);
			$arrValue = array(
				$stampUpdate,
				$jsonData,
			);

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingDetailedAccount' . $strNation,
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
						'value'         => $arr['numFiscalPeriod'],
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
					array(
						'flagType'      => 'num',
						'strColumn'     => 'numPage',
						'flagCondition' => 'eq',
						'value'         => $value['numPage'],
					),
				),
				'arrValue'  => $arrValue,
			));
		}
	}

	/**
			'varsList' => $vars['varsItem']['varsList'],
			'varsData' => $varsRequest['query']['jsonValue']['vars']['JsonData'],
			'varsFlag' => $varsFlag,
	 */
	protected function _checkValueDetailSave($arr)
	{
		global $varsPluginAccountingAccount;

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
			if ($data['flagTag'] == 'select') {
				$flag = 0;
				$arrayOption = $data['arrayOption'];
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
			if ($data['flagForm']) {
				$varsValue[$key] = $value;
			}
		}

		if ($numAll != 0) {
			$this->_sendOld();
		}

		if (preg_match("/^detail/", $arr['varsFlag']['flagMenu'])) {
			$varsData = $arr['varsItem']['varsSave']['jsonData'];
		} else {
			$varsData = $arr['varsItem']['varsPreference']['jsonData'];
		}

		if (!$varsData) {
			$varsData = array();
		}

		$varsData[$arr['varsFlag']['flagMenu']] = $varsValue;
		$arrValue['arr']['jsonData'] = $varsData;

		return $arrValue;
	}

	/**

	 */
	protected function _updateDbDetail($arr)
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

		$flagMenu = $arr['varsFlag']['flagMenu'];
		$numPage = $arr['varsFlag']['numPage'];

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];

		$jsonData = json_encode($arr['arrValue']['arr']['jsonData']);

		$arrColumn = array(
			'stampRegister',
			'stampUpdate',
			'idEntity',
			'numFiscalPeriod',
			'flagReport',
			'flagDetail',
			'numPage',
			'jsonData',
		);
		$arrValue = array(
			$stampRegister,
			$stampUpdate,
			$idEntity,
			$numFiscalPeriod,
			$flagReport,
			$flagDetail,
			$numPage,
			$jsonData,
		);
		if (is_null($arr['varsItem']['varsPreference']['id'])) {
			$numPage = 0;
			$varsData = array();
			$varsData['numPageMax'] = 1;
			$jsonData = json_encode($varsData);

			$arrValuePreference = array(
				$stampRegister,
				$stampUpdate,
				$idEntity,
				$numFiscalPeriod,
				$flagReport,
				$flagDetail,
				$numPage,
				$jsonData,
			);
			$id = $classDb->insertRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingDetailedAccount' . $strNation,
				'arrColumn' => $arrColumn,
				'arrValue'  => $arrValuePreference,
			));
		}

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
				'strTable' => 'accountingDetailedAccount' . $strNation,
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
					array(
						'flagType'      => 'num',
						'strColumn'     => 'numPage',
						'flagCondition' => 'eq',
						'value'         => $numPage,
					),
				),
				'arrValue'  => $arrValue,
			));

		//insert
		} else {
			$id = $classDb->insertRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingDetailedAccount' . $strNation,
				'arrColumn' => $arrColumn,
				'arrValue'  => $arrValue,
			));
		}


	}

}
