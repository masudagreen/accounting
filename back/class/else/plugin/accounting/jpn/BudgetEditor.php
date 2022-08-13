<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_BudgetEditor extends Code_Else_Plugin_Accounting_Jpn_Budget
{
	protected $_childSelf = array(
		'pathTplJs'   => 'else/plugin/accounting/js/jpn/budgetEditor.js',
		'pathVarsJs'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/budgetEditor.php',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;

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

		$flag = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done)$/", $flag)) {
			$this->_sendOld();
		}

		$varsAuthority = $this->_getVarsAuthority(array());

		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'])) {
			$this->_sendOld();
		}
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllInsert'])) {
			$this->_sendOld();
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsFlag = array(
			'flagFiscalPeriod' => $varsRequest['query']['jsonValue']['vars']['VarsFlag']['flagFiscalPeriod'],
			'idDepartment'     => $varsRequest['query']['jsonValue']['vars']['VarsFlag']['idDepartment'],
			'flagFS'           => $varsRequest['query']['jsonValue']['vars']['VarsFlag']['flagFS'],
			'flagUnit'         => (int) $varsRequest['query']['jsonValue']['vars']['VarsFlag']['flagUnit'],
			'flagCalc'         => $varsRequest['query']['jsonValue']['vars']['VarsFlag']['flagCalc'],
		);

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
		));

		$this->_checkValueFS(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
			'varsItem' => $varsItem,
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$vars['portal']['varsDetail']['varsDetail'] = $this->_updateVarsDetail((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		));

		$this->_checkValueDetail(array(
			'varsDetail' => $vars['portal']['varsNavi']['varsDetail'],
			'varsFlag'   => $varsFlag,
		));

		$varsTarget = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget
		));


		$arrValue = $this->_checkValueDetailValue(array(
			'vars'     => $vars,
			'arrValue' => $arrValue,
		));

		try {
			$dbh->beginTransaction();

			$this->_updateDbLog(array(
				'vars'     => $vars,
				'varsItem' => $varsItem,
				'arrValue' => $arrValue,
				'varsFlag' => $varsFlag,
			));

			$this->_updateDbPreferenceStamp(array('strColumn' => 'budget'));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$this->sendVars(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(),
		));
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
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$jsonData = json_encode($arr['arrValue']['arr']['jsonData']);
		$flagFS = $arr['varsFlag']['flagFS'];
		$idDepartment = $arr['varsFlag']['idDepartment'];

		$arrayNew = array();
		$array = $arr['arrValue']['arr']['jsonFiscalPeriod'];

		foreach ($array as $key => $value) {
			$flagFiscalPeriod = $key;
			if ($arr['arrValue']['arr']['flagSplit']) {
				if ($key == '') {
					continue;
				}
				$jsonData = $arr['arrValue']['arr']['jsonData'];
				$jsonData = $this->_getCalcJsonData(array(
					'vars'             => $arr['vars'],
					'jsonData'         => $jsonData,
					'flagFiscalPeriod' => $flagFiscalPeriod,
				));

			} else {
				if ($key == '' || $value == 0) {
					continue;
				}
			}


			$rows = $this->_getLog(array(
				'flagFiscalPeriod' => $flagFiscalPeriod,
				'numFiscalPeriod'  => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				'flagFS'           => $flagFS,
				'idDepartment'     => $idDepartment,
			));

			//update
			if ($rows['numRows']) {
				$arrColumn = array(
					'stampUpdate',
					'flagFiscalPeriod',
					'idDepartment',
					'flagFS',
					'jsonData',
				);
				$arrValue = array(
					$stampUpdate,
					$flagFiscalPeriod,
					$idDepartment,
					$flagFS,
					$jsonData,
				);
				$classDb->updateRow(array(
					'idModule'  => 'accounting',
					'strTable' => 'accountingBudget' . $strNation,
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
							'strColumn'     => 'flagFiscalPeriod',
							'flagCondition' => 'eq',
							'value'         => $flagFiscalPeriod,
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
					'flagFiscalPeriod',
					'idDepartment',
					'flagFS',
					'jsonData',
				);
				$arrValue = array(
					$stampRegister,
					$stampUpdate,
					$idEntity,
					$numFiscalPeriod,
					$flagFiscalPeriod,
					$idDepartment,
					$flagFS,
					$jsonData,
				);

				$id = $classDb->insertRow(array(
					'idModule'  => 'accounting',
					'strTable' => 'accountingBudget' . $strNation,
					'arrColumn' => $arrColumn,
					'arrValue'  => $arrValue,
				));
			}
		}
	}

	/**

	 */
	protected function _getCalcJsonData($arr)
	{
		$numCalc = 1;
		if (preg_match("/^(f1)$/", $arr['flagFiscalPeriod'])) {
			return json_encode($arr['jsonData']);

		} elseif (preg_match("/^(f21|f22)$/", $arr['flagFiscalPeriod'])) {
			$numCalc = 2;

		} elseif (preg_match("/^(f41|f42|f43|f44)$/", $arr['flagFiscalPeriod'])) {
			$numCalc = 4;

		} else {
			$numCalc = 12;
		}

		$array = $arr['jsonData'];
		foreach ($array as $key => $value) {
			$array[$key] = floor($value/$numCalc);
		}
		$varsFS = $arr['vars']['portal']['varsList']['varsDetail'];
		$varsFS = $this->_setVarsListValue(array(
			'varsFS'    => $varsFS,
			'varsValue' => &$array,
		));
		$varsValue = array();
		$this->_loopVarsCalc(array(
			'varsFS'    => $varsFS,
			'varsValue' => &$varsValue,
		));

		return json_encode($varsValue);
	}

	/**

	 */
	protected function _checkValueDetailValue($arr)
	{
		$varsFS = $arr['vars']['portal']['varsList']['varsDetail'];

		$varsFS = $this->_setVarsListValue(array(
			'varsFS'    => $varsFS,
			'varsValue' => &$arr['arrValue']['arr']['jsonData'],
		));

		$varsValue = array();
		$this->_loopVarsCalc(array(
			'varsFS'    => $varsFS,
			'varsValue' => &$varsValue,
		));
		$arr['arrValue']['arr']['jsonData'] = $varsValue;

		return $arr['arrValue'];
	}
}
