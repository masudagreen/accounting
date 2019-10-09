<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_AccountEntityAuthorityEditor extends Code_Else_Plugin_Accounting_AccountEntityAuthority
{
	protected $_childSelf = array(
		'pathTplJs'   => 'else/plugin/accounting/js/accountEntityAuthorityEditor.js',
		'pathVarsJs'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/accountEntityAuthorityEditor.php',
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

	 */
	protected function _iniDetailEdit()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $classEscape;
		global $classPluginAccountingInit;

		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccounts;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingAuthority;
		global $varsPluginAccountingAccountsEntity;
		global $varsRequest;
		global $varsAccounts;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsTarget['vars']['varsDetail'] = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget['vars']['varsDetail']
		));

		$arrValue = $this->_checkValueDetail($arrValue);

		$idAccount = $varsRequest['query']['jsonValue']['idTarget'];
		$idEntityCurrent = $varsPluginAccountingAccount['idEntityCurrent'];

		$idAuthority = $arrValue['arr']['idAuthority'];
		$idAccess = $arrValue['arr']['idAccess'];

		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrValue['arr']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));

		$arrDbColumn = array('idAuthority', 'idAccess');
		$arrDbValue = array($idAuthority, $idAccess);


		try {
			$dbh->beginTransaction();

			$idAccountEntityAuthority = $varsPluginAccountingAccountsEntity[$idAccount][$idEntityCurrent]['idAuthority'];

			$this->_updateAuthority(array(
				'flagAllNew'  => $varsPluginAccountingAuthority[$arrValue['arr']['idAuthority']]['flagAllUpdate'],
				'flagAllPast' => $varsPluginAccountingAuthority[$idAccountEntityAuthority]['flagAllUpdate'],
				'arrId'       => array($idAccount),
				'arrIdEntity' => array($varsPluginAccountingAccount['idEntityCurrent']),
			));

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingAccountEntity',
				'arrColumn' => $arrDbColumn,
				'flagAnd'   => 1,
				'arrWhere'  => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idAccount',
						'flagCondition' => 'eq',
						'value'         => $idAccount,
					),
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idEntity',
						'flagCondition' => 'eq',
						'value'         => $varsPluginAccountingAccount['idEntityCurrent'],
					),
				),
				'arrValue'  => $arrDbValue,
			));

			$arrDbColumn = array('stampUpdate');
			$arrDbValue = array(TIMESTAMP);

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingAccount',
				'arrColumn' => $arrDbColumn,
				'flagAnd'   => 1,
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
			$array = array('account','log');
			foreach ($array as $key => $value) {
				$this->_updateDbPreferenceStamp(array('strColumn' => $value));
			}

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}
		$classPluginAccountingInit->updateInitAccount();
		$classPluginAccountingInit->updateInitAccounts();
		$classPluginAccountingInit->updateInitAccountsEntity();

		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$this->_iniSearchDetail();
	}


	/**
		$this->_checkValueDetail($arrValue);
	 */
	protected function _checkValueDetail($arrValue)
	{
		global $varsPluginAccountingAuthority;
		global $varsPluginAccountingAccess;
		global $varsPluginAccountingAccount;
		global $varsAccounts;
		global $varsRequest;

		$idAccount = $varsRequest['query']['jsonValue']['idTarget'];
		if (!$varsAccounts[$idAccount]) {
			$this->_sendOldError();
		}

		$idAuthority = $arrValue['arr']['idAuthority'];
		if (!$varsPluginAccountingAuthority[$idAuthority]) {
			$this->_sendOldError();
		}

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];

		$idAccess = $arrValue['arr']['idAccess'];
		if (!$varsPluginAccountingAccess[$idEntity][$idAccess]) {
			$this->_sendOldError();
		}

		return $arrValue;
	}

	/**
		(array(
			'flagAllNew'  => $varsPluginAccountingAuthority[$arrValue['arr']['idAuthority']]['flagAllUpdate'],
			'flagAllPast' => $varsPluginAccountingAuthority[$idAccountEntityAuthority]['flagAllUpdate'],
			'arrId'       => array($idAccount),
			'arrIdEntity' => array($varsPluginAccountingAccount['idEntityCurrent']),
		))
	 */
	protected function _updateAuthority($arr)
	{
		global $varsPluginAccountingAccount;

		$path = PATH_BACK_CLASS_ELSE_PLUGIN . $this->_self['strTitle'] . '/AccountStatus.php';
		require_once($path);
		$strClass = 'Code_Else_Plugin_Accounting_AccountStatus';

		//All -> my
		if ($arr['flagAllPast'] && !$arr['flagAllNew']) {

			$classCall = new $strClass();
			$classCall->allot(array(
				'flagStatus'  => 'updateAccountAuthority',
				'arrId'       => $arr['arrId'],
				'arrIdEntity' => $arr['arrIdEntity'],
			));

		//All <- my
		} elseif (!$arr['flagAllPast'] && $arr['flagAllNew']) {

		}
	}


}
