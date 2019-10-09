<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Init
{
	protected $_self = array(

	);

	function __construct()
	{
	}


	/*
	 *
	 * */
	public function run()
	{
		define('PLUGIN_ACCOUNTING_NUM_VERSION', NUM_VERSION);

		$this->setInitAccounts();
		$this->setInitAccountsId();
		$this->setInitAccountsEntity();
		$this->setInitAccount();
		$this->setInitPreference();
		$this->setInitAuthority();
		$this->setInitEntity();
		$this->setInitAccess();

	}

	/*
	 *
	 * */
	public function setInitAccountsId()
	{
		global $classInit;
		global $varsPluginAccountingAccountsId;

		$varsPluginAccountingAccountsId = (FLAG_APC)? apc_fetch('varsPluginAccountingAccountsId') : null;
		if (is_null($varsPluginAccountingAccountsId)) {
			$this->updateInitAccountsId();
		}

	}

    /**
     */
	public function updateInitAccountsId()
	{
		global $varsPluginAccountingAccountsId;
		global $classDb;
		$dbh = $classDb->getHandle();

		$strSql = 'select * from accountingAccountId;';
		$stmt = $dbh->prepare($strSql);
		$stmt->execute();
		$array = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$str = $row['id'];
			$array[$str] = $row;
		}
		$varsPluginAccountingAccountsId = $array;

		if (FLAG_APC) {
			apc_store('varsPluginAccountingAccountsId', $varsPluginAccountingAccountsId);
		}

	}

	/*
	 *
	 * */
	public function setInitAccounts()
	{
		global $classInit;
		global $varsPluginAccountingAccounts;

		$varsPluginAccountingAccounts = (FLAG_APC)? apc_fetch('varsPluginAccountingAccounts') : null;
		if (is_null($varsPluginAccountingAccounts)) {
			$this->updateInitAccounts();
		}

	}

    /**
     */
	public function updateInitAccounts()
	{
		global $varsPluginAccountingAccounts;
		global $classDb;
		$dbh = $classDb->getHandle();

		$strSql = 'select * from accountingAccount;';
		$stmt = $dbh->prepare($strSql);
		$stmt->execute();
		$array = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			foreach ($row as $key => $value) {
				if (preg_match("/^json/", $key)) {
					$row[$key] = (!is_null($value))? json_decode($value, true) : array();
				}
			}
			$str = $row['idAccount'];
			$array[$str] = $row;
		}
		$varsPluginAccountingAccounts = $array;

		if (FLAG_APC) {
			apc_store('varsPluginAccountingAccounts', $varsPluginAccountingAccounts);
		}

	}

	/*
	 *
	 * */
	public function setInitAccountsEntity()
	{
		global $classInit;
		global $varsPluginAccountingAccountsEntity;

		$varsPluginAccountingAccountsEntity = (FLAG_APC)? apc_fetch('varsPluginAccountingAccountsEntity'): null;
		if (is_null($varsPluginAccountingAccountsEntity)) {
			$this->updateInitAccountsEntity();
		}

	}

    /**
     */
	public function updateInitAccountsEntity()
	{
		global $varsPluginAccountingAccountsEntity;
		global $classDb;
		$dbh = $classDb->getHandle();

		$strSql = 'select * from accountingAccountEntity;';
		$stmt = $dbh->prepare($strSql);
		$stmt->execute();
		$array = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			foreach ($row as $key => $value) {
				if (preg_match("/^json/", $key)) {
					$row[$key] = (!is_null($value))? json_decode($value, true) : array();
				}
			}
			$array[$row['idAccount']][$row['idEntity']] = $row;
		}
		$varsPluginAccountingAccountsEntity = $array;

		if (FLAG_APC) {
			apc_store('varsPluginAccountingAccountsEntity', $varsPluginAccountingAccountsEntity);
		}

	}
	/*
	 *
	 * */
	public function setInitAccount()
	{
		$this->updateInitAccount();
	}

    /**
     */
	public function updateInitAccount()
	{
		global $varsAccount;
		global $varsPluginAccountingAccounts;
		global $varsPluginAccountingAccount;

		$varsPluginAccountingAccount = $varsPluginAccountingAccounts[$varsAccount['id']];
	}

	/*
	 *
	 * */
	public function setInitPreference()
	{
		global $classInit;
		global $varsPluginAccountingPreference;

		$varsPluginAccountingPreference = (FLAG_APC)? apc_fetch('varsPluginAccountingPreference'): null;
		if (is_null($varsPluginAccountingPreference)) {
			$this->updateInitPreference();
		}
	}

   /**
     */
	public function updateInitPreference()
	{
		global $classInit;
		global $varsPluginAccountingPreference;

 		$classInit->updateVar(array(
			'vars'     => &$varsPluginAccountingPreference,
			'strVars'  => 'varsPluginAccountingPreference',
			'strTable' => 'accountingPreference',
		));
	}

	/*
	 *
	 * */
	public function setInitAuthority()
	{
		global $classInit;
		global $varsPluginAccountingAuthority;

		$varsPluginAccountingAuthority = (FLAG_APC)? apc_fetch('varsPluginAccountingAuthority'): null;
		if (is_null($varsPluginAccountingAuthority)) {
			$this->updateInitAuthority();
		}
	}

    /**
     */
	public function updateInitAuthority()
	{
		global $classInit;
		global $varsPluginAccountingAuthority;

 		$classInit->updateVarsAll(array(
			'vars'     => &$varsPluginAccountingAuthority,
			'strVars'  => 'varsPluginAccountingAuthority',
			'strTable' => 'accountingAuthority',
		));

	}

	/*
	 *
	 * */
	public function setInitAccess()
	{
		global $classInit;
		global $varsPluginAccountingAccess;

		$varsPluginAccountingAccess = (FLAG_APC)? apc_fetch('varsPluginAccountingAccess'): null;
		if (is_null($varsPluginAccountingAccess)) {
			$this->updateInitAccess();
		}
	}

    /**
     */
	public function updateInitAccess()
	{
		global $varsPluginAccountingAccess;
		global $classDb;
		$dbh = $classDb->getHandle();

		$strSql = 'select id from accountingEntity;';
		$stmt = $dbh->prepare($strSql);
		$stmt->execute();
		$array = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$array[$row['id']] = 1;
		}

		$dataDefault1 = $this->_getAccessData(array(
			'id' => 1
		));
		$dataDefault2 = $this->_getAccessData(array(
			'id' => 2
		));

		$arrayNew = array();
		foreach ($array as $key => $value) {
			$idEntity = $key;
			$strSql = 'select * from accountingAccess where idEntity = ?;';
			$stmt = $dbh->prepare($strSql);
			$stmt->execute(array($idEntity));
			$arrayNew[$idEntity]['1'] = $dataDefault1;
			$arrayNew[$idEntity]['2'] = $dataDefault2;
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				foreach ($row as $keyData => $valueData) {
					if (preg_match("/^json/", $keyData)) {
						$row[$keyData] = (!is_null($valueData))? json_decode($valueData, true) : array();
					}
				}
				$arrayNew[$idEntity][$row['idAccess']] = $row;
			}
		}
		$varsPluginAccountingAccess = $arrayNew;

		if (FLAG_APC) {
			apc_store('varsPluginAccountingAccess', $varsPluginAccountingAccess);
		}
	}

	/*
	 * */
	protected function _getAccessData($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$strSql = 'select * from accountingAccess where id = ?;';
		$stmt = $dbh->prepare($strSql);
		$stmt->execute(array($arr['id']));
		$arrayNew = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			foreach ($row as $keyData => $valueData) {
				if (preg_match("/^json/", $keyData)) {
					$row[$keyData] = (!is_null($valueData))? json_decode($valueData, true) : array();
				}
			}
			$arrayNew = $row;
		}

		return $arrayNew;
	}

	/*
	 *
	 * */
	public function setInitEntity()
	{
		global $classInit;
		global $varsPluginAccountingEntity;

		$varsPluginAccountingEntity = (FLAG_APC)? apc_fetch('varsPluginAccountingEntity'): null;
		if (is_null($varsPluginAccountingEntity)) {
			$this->updateInitEntity();
		}
	}

	/*
	 *
	 * */
	public function updateInitEntity()
	{
		global $classInit;
		global $varsPluginAccountingEntity;

 		$classInit->updateVarsAll(array(
			'vars'     => &$varsPluginAccountingEntity,
			'strVars'  => 'varsPluginAccountingEntity',
			'strTable' => 'accountingEntity',
		));
	}

}
