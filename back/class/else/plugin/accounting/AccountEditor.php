<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_AccountEditor extends Code_Else_Plugin_Accounting_Account
{
	protected $_childSelf = array(
		'pathTplJs'   => 'else/plugin/accounting/js/accountEditor.js',
		'pathVarsJs'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/accountEditor.php',
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
	protected function _iniDetailEdit()
	{
		global $classDb;
		global $classEscape;
		global $classPluginAccountingInit;

		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccounts;
		global $varsPluginAccountingAccount;

		global $varsRequest;
		global $varsAccounts;

		$strDir = $this->_self['strTitle'];
		$strFile = ucwords($this->_self['strTitle']);
		require_once(PATH_BACK_CLASS_ELSE_PLUGIN . $strDir . '/' . $strFile . '.php');
		$classCall = new Code_Else_Plugin_Accounting_Accounting;

		$dbh = $classDb->getHandle();

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$vars['portal']['varsDetail']['templateDetail'] = $this->_updateVarsEdit(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail'],
		));

		$varsTarget['vars']['varsDetail'] = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget['vars']['varsDetail']
		));

		$tm = TIMESTAMP;
		$stampRegister = $tm;
		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrValue['arr']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));

		$arrCommaIdEntity = $arrValue['arr']['arrCommaIdEntity'];
		$array = $classEscape->splitCommaArrayData(array('data' => $arrCommaIdEntity));

		$arrIdEntity = $array;
		foreach ($array as $key => $value) {
			$id = $value;
			if (!$varsPluginAccountingEntity[$id]) {
				$this->_sendOldError();
			}
		}


		$idAccount = $varsRequest['query']['jsonValue']['idTarget'];

		if (!$varsAccounts[$idAccount]) {
			$this->_sendOldError();
		}

		$arrIdEntityDrop = $this->_checkIdEntity($arrIdEntity);

		$flag = 0;
		if ($arrIdEntityDrop) {
			$array = $arrIdEntityDrop;
			foreach ($array as $key => $value) {
				if ($value == $varsPluginAccountingAccounts[$idAccount]['idEntityCurrent']) {
					$flag = 1;
					break;
				}
			}
		}
		if ($flag) {
			$arrDbColumn = array('arrCommaIdEntity', 'numFiscalPeriodCurrent', 'idEntityCurrent');
			$arrDbValue = array($arrCommaIdEntity, null, null);

		} else {
			$arrDbColumn = array('arrCommaIdEntity');
			$arrDbValue = array($arrCommaIdEntity);
		}

		try {
			$dbh->beginTransaction();

			if ($arrIdEntityDrop) {
				$classCall->loop(array(
					'flagType'    => 'accountStatus',
					'flagStatus'  => 'updateAccountAuthority',
					'arrId'       => array($idAccount),
					'arrIdEntity' => $arrIdEntityDrop,
				));
			}

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingAccount',
				'arrColumn' => $arrDbColumn,
				'arrWhere'  => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idAccount',
						'flagCondition' => 'eq',
						'value'         => $idAccount,
					),
				),
				'arrValue'  => $arrDbValue,
			));


			$this->updateDbAccountArrSpaceStrTag(array(
				'idTarget'       => $idAccount,
				'arrSpaceStrTag' => $arrSpaceStrTag,
			));


			$this->updateDbAccountStamp();
			$this->_updateDbPreferenceStamp(array('strColumn' => 'account'));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$classPluginAccountingInit->updateInitAccounts();
		$classPluginAccountingInit->updateInitAccountsEntity();
		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$this->_iniSearchDetail();
	}


	/**
		$this->_checkIdEntity($arrIdEntity)
	 */
	protected function _checkIdEntity($arrIdEntity)
	{
		global $varsPluginAccountingAccounts;
		global $varsRequest;
		global $classEscape;

		$idAccount = $varsRequest['query']['jsonValue']['idTarget'];

		$array = $arrIdEntity;
		$arrayNew = array();
		foreach ($array as $key => $value) {
			$arrayNew[$value] = 1;
		}

		$array = $classEscape->splitCommaArrayData(array('data' => $varsPluginAccountingAccounts[$idAccount]['arrCommaIdEntity']));
		$arrayPast = array();
		foreach ($array as $key => $value) {
			$arrayPast[$value] = 1;
		}

		$array = $arrIdEntity;
		$arrayAdd = array();
		$num = 0;
		foreach ($array as $key => $value) {
			if (!$arrayPast[$value]) {
				$arrayAdd[$num] = $value;
				$num++;
			}
		}

		$array = $classEscape->splitCommaArrayData(array('data' => $varsPluginAccountingAccounts[$idAccount]['arrCommaIdEntity']));
		$arrayDelete = array();
		$num = 0;
		foreach ($array as $key => $value) {
			if (!$arrayNew[$value]) {
				$arrayDelete[$num] = $value;
				$num++;
			}
		}

		return $arrayDelete;
	}


	/**
		$this->_updateVarsEdit(array(
			'vars' => array(),
		));
	 */
	protected function _updateVarsEdit($arr)
	{
		global $varsPluginAccountingEntity;
		$array = &$arr['vars'];
		$arrayNew = array();
		foreach ($array as $key => $value) {
			if ($value['id'] == 'ArrSpaceStrTag' || $value['id'] == 'ArrCommaIdEntity') {
				$arrayNew[] = $value;
			}
		}

		return $arrayNew;
	}





}
