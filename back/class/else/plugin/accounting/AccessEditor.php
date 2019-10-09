<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_AccessEditor extends Code_Else_Plugin_Accounting_Access
{
	protected $_childSelf = array(
		'pathTplJs'   => 'else/plugin/accounting/js/accessEditor.js',
		'pathVarsJs'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/accessEditor.php',
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
					var_dump(__CLASS__ . '/' .__FUNCTION__);
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
	protected function _iniDetailAdd()
	{
		global $classDb;
		global $varsRequest;
		global $classPluginAccountingInit;
		global $varsPluginAccountingAccount;

		$dbh = $classDb->getHandle();

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$vars = $this->_updateVars(array(
			'vars' => $vars
		));

		$varsTarget['vars']['varsDetail'] = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget['vars']['varsDetail']
		));

		$this->_checkStrTitle(array(
			'strTitle' => $arrValue['arr']['strTitle'],
			'idTarget' => 0,
		));

		$arrValue = $this->_checkValueDetail(array(
			'vars'     => $vars,
			'arrValue' => $arrValue,
		));

		try {
			$dbh->beginTransaction();

			$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
			$varsIdNumber = $this->_getIdAutoIncrement(array(
				'idTarget' => 'idAccess'
			));
			if (!$varsIdNumber[$idEntity]) {
				$varsIdNumber[$idEntity] = 3;
			}

			$arrSql = $this->_updateDbValue(array(
				'arr'          => $arrValue['arr'],
				'varsIdNumber' => $varsIdNumber,
			));

			$classDb->insertRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingAccess',
				'arrColumn' => $arrSql['arrColumn'],
				'arrValue'  => $arrSql['arrValue'],
			));

			$varsIdNumber[$idEntity]++;
			$this->_updateIdAutoIncrement(array(
				'idTarget'   => 'idAccess',
				'varsTarget' => $varsIdNumber
			));

			$this->_updateDbPreferenceStamp(array('strColumn' => 'access'));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$classPluginAccountingInit->updateInitAccess();
		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
		$this->_setSearch(array('flag' => 1));
	}

	/**
		$this->_checkStrTitle(array(
			'strTitle' => '',
			'idTarget' => 0,
		));
	 */
	protected function _checkStrTitle($arr)
	{
		global $varsPluginAccountingAccess;
		global $varsPluginAccountingAccount;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];

		$array = &$varsPluginAccountingAccess[$idEntity];
		foreach ($array as $key => $value) {
			$flag = 0;
			if ($arr['idTarget']) {
				if ($value['strTitle'] == $arr['strTitle'] && $arr['idTarget'] != $value['idAccess']) {
					$flag = 1;
				}

			} else {
				if ($value['strTitle'] == $arr['strTitle']) {
					$flag = 1;
				}
			}

			if ($flag) {
				$this->sendVars(array(
					'flag'    => 'strTitle',
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));
			}
		}
	}

	/**
		(array(
			'vars'     => $vars,
			'arrValue' => $arrValue,
		))
	 */
	protected function _checkValueDetail($arr)
	{
		global $varsRequest;
		global $varsPluginAccountingAccount;

		$arrValue = $arr['arrValue'];

		if (!$arrValue['arr']['jsonData']) {
			$arrValue['arr']['jsonData'] =  array();

		} else {
			$arrayNew = array();
			$array = $arr['vars']['varsItem']['arrStrTitle'];
			foreach ($array as $key => $value) {
				if (is_null($arrValue['arr']['jsonData'][$key])) {
					continue;
				}
				if ($value['flagAccessUse']) {
					$arrayNew[$key] = ($arrValue['arr']['jsonData'][$key])? 1 : 0;
				}
			}
			$arrValue['arr']['jsonData'] = $arrayNew;
		}

		return $arrValue;
	}

	/**
		$this->_updateDbValue(array(
			'arr'      => array(),
			'flagEdit' => 0,
			'varsIdNumber' => $varsIdNumber,
		));
	 */
	protected function _updateDbValue($arr)
	{
		global $classEscape;
		global $varsPluginAccountingAccount;

		$tm = TIMESTAMP;
		$stampRegister = $tm;
		$stampUpdate = $tm;

		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arr['arr']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));
		$strTitle = $arr['arr']['strTitle'];

		$jsonData = json_encode($arr['arr']['jsonData']);
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];

		if ($arr['flagEdit']) {
			$data = array(
				'arrColumn' => array('strTitle', 'jsonData', 'arrSpaceStrTag'),
				'arrValue' => array($strTitle, $jsonData, $arrSpaceStrTag),
			);

		} else {
			$idAccess = $arr['varsIdNumber'][$idEntity];
			$data = array(
				'arrColumn' => array('idEntity', 'stampRegister', 'stampUpdate', 'idAccess', 'strTitle', 'jsonData', 'arrSpaceStrTag'),
				'arrValue' => array($idEntity, $stampRegister, $stampUpdate, $idAccess, $strTitle, $jsonData, $arrSpaceStrTag),
			);
		}

		return $data;
	}

	/**
	 *
	 */
	protected function _iniDetailEdit()
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $classPluginAccountingInit;

		global $varsAccount;
		global $varsPluginAccountingAccess;
		global $varsRequest;
		global $varsPluginAccountingAccount;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$vars = $this->_updateVars(array(
			'vars' => $vars
		));

		$varsTarget['vars']['varsDetail'] = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget['vars']['varsDetail']
		));

		$this->_checkStrTitle(array(
			'strTitle' => $arrValue['arr']['strTitle'],
			'idTarget' => $varsRequest['query']['jsonValue']['idTarget'],
		));

		$arrValue = $this->_checkValueDetail(array(
			'vars'     => $vars,
			'arrValue' => $arrValue,
		));

		$arrSql = $this->_updateDbValue(array(
			'arr'      => $arrValue['arr'],
			'flagEdit' => 1,
		));

		if (!$varsPluginAccountingAccess[$idEntity][$varsRequest['query']['jsonValue']['idTarget']]) {
			$this->_sendOldError();
		}

		try {
			$dbh->beginTransaction();

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingAccess',
				'arrColumn' => $arrSql['arrColumn'],
				'flagAnd'   => 1,
				'arrWhere'  => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idEntity',
						'flagCondition' => 'eq',
						'value'         => $varsPluginAccountingAccount['idEntityCurrent'],
					),
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idAccess',
						'flagCondition' => 'eq',
						'value'         => $varsRequest['query']['jsonValue']['idTarget'],
					),
				),
				'arrValue'  => $arrSql['arrValue'],
			));

			$this->_updateDbPreferenceStamp(array('strColumn' => 'access'));


			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$classPluginAccountingInit->updateInitAccess();
		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$this->_iniSearchDetail();
	}



}
