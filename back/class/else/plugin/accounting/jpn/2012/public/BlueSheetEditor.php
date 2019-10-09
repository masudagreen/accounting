<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
 class Code_Else_Plugin_Accounting_Jpn_BlueSheetEditor_2012_Public extends Code_Else_Plugin_Accounting_Jpn_BlueSheet_2012_Public
{
	protected $_extSelf = array(
		'idPreference'          => 'blueSheetWindow',
		'numYearSheet'          => '2014',
		'pathTplJs'             => 'else/plugin/accounting/js/jpn/2012/public/blueSheet.js',
		'pathVarsJs'            => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/2012/public/blueSheet.php',
		'varsFixedAssetsOption' => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/depreciation.php',
		'pathItem'              => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/2012/public/blueSheet.php',
		'pathItemZeimusho'      => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/2012/public/zeimushoList.csv',
		'pathTplHtml'           => 'else/plugin/accounting/html/2012/public/blueSheet<%replace%>.html',
	);

	protected $_childSelf = array(
		'pathTplJs'   => 'else/plugin/accounting/js/jpn/2012/public/blueSheetEditor.js',
		'pathVarsJs'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/2012/public/blueSheetEditor.php',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;
		global $varsPluginAccountingAccount;

		$varsFiscalPeriod = $this->_getVarsFiscalPeriod(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagFiscalPeriod' => 'f1',
		));

		if ($varsFiscalPeriod['numStartYear'] >= 2015) {
			$this->_extSelf['numYearSheet'] = '2015';
		}

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
			$this->_updateDbPreferenceStamp(array('strColumn' => 'blueSheet'));

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
			if (preg_match("/^select/", $data['flagValueType'])) {
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

			} elseif ($data['flagValueType'] == 'rate') {
				if ($data['value'] != '') {
					if (!preg_match("/^[0-9]{1,3}\.[0-9]{2,2}$/", $data['value'])) {
						$this->_sendOld();
					}
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
		global $classCrypte;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$stampRegister = TIMESTAMP;
		$stampUpdate = TIMESTAMP;
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];

		$jsonData = json_encode($arr['arrValue']['arr']['jsonData']);
		$blobData = $classCrypte->setEncrypt(array('data' => $jsonData));
		$numYearSheet = $this->_extSelf['numYearSheet'];

		//update
		if ($arr['varsItem']['varsSave']['jsonData']) {
			$arrColumn = array(
				'stampUpdate',
				'blobData',
			);
			$arrValue = array(
				$stampUpdate,
				$blobData,
			);
			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingBlueSheet' . $strNation,
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
				'numYearSheet',
				'blobData',
			);
			$arrValue = array(
				$stampRegister,
				$stampUpdate,
				$idEntity,
				$numFiscalPeriod,
				$numYearSheet,
				$blobData,
			);

			$id = $classDb->insertRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingBlueSheet' . $strNation,
				'arrColumn' => $arrColumn,
				'arrValue'  => $arrValue,
			));
		}

	}
}
