<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_FixedAssetsConfig
{
	protected $_extSelf = array(
		'db' => 'back/tpl/templates/else/plugin/accounting/db/config.php',
		'strNation' => 'jpn',
	);

	function __construct($arr = null)
	{
		// $arr = @func_get_arg(0);
		if (!$arr) {
			return;
		}
		foreach ($arr as $key => $value) {
			$this->_extSelf[$key] = $value;
		}
	}


	/**
	 *
	 */
	public function run()
	{
		if (FLAG_TEST) {
			var_dump(__CLASS__ . '/' . __FUNCTION__ . '/' . __LINE__);
		}
		exit;
	}

	/**

	 */
	public function allot($arr)
	{
		global $classDb;


		if (!$this->_checkTable()) {
			if (!$classDb->getFlagMaster()) {
				$dbh = $classDb->setDbhMaster();
			} else {
				$dbh = $classDb->getHandle();
			}

			//$this->_setTable();

			try {
				$dbh->beginTransaction();

				$this->_setInsert();

				$dbh->commit();
			} catch (PDOException $e) {
				$dbh->rollBack();
				if (FLAG_TEST) {
					var_dump($e->getMessage());
				}
				exit;
			}
		}
	}

	/**
		(array(
			'path'      => '',
			'strLang'   => '',
			'strNation' => '',
		));
	 */
	protected function _getVars($arr)
	{

		global $classEscape;

		if (!$arr['strTitle']) {
			$arr['strTitle'] = '';
		}

		if (!$arr['strLang']) {
			$arr['strLang'] = '';
		}

		if (!$arr['strNation']) {
			$arr['strNation'] = '';
		}

		$vars = $classEscape->getVars(array(
			'data' => $arr['path'],
			'arr' => array(
				array('before' => '<strTitle>', 'after' => $arr['strTitle'], ),
				array('before' => '<strLang>', 'after' => $arr['strLang'], ),
				array('before' => '<strNation>', 'after' => $arr['strNation'], ),
			),
		));

		return $vars;
	}

	/**

	 */
	protected function _checkTable()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$strNation = ucwords($this->_extSelf['strNation']);
		$dbname = $classDb->getSelf(array('key' => 'dbname'));

		$sql = 'show tables from ' . $dbname . ' like ? ;';
		$str = 'accountingFixedAssets' . $strNation;

		$stmt = $dbh->prepare($sql);
		$stmt->execute(array($str));

		$flag = 1;
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$flag = 0;
		}

		return $flag;
	}


	/**

	 */
	protected function _setInsert()
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		global $classDb;
		$dbh = $classDb->getHandle();

		$stampUpdate = TIMESTAMP;
		$stampRegister = TIMESTAMP;
		$strNation = ucwords($this->_extSelf['strNation']);

		$array = $varsPluginAccountingEntity;
		foreach ($array as $key => $value) {
			$numFiscalPeriodStart = $value['numFiscalPeriodStart'];
			$numFiscalPeriodEnd = $value['numFiscalPeriod'];
			$idEntity = $value['id'];
			for ($i = $numFiscalPeriodStart; $i <= $numFiscalPeriodEnd; $i++) {
				$numFiscalPeriod = $i;
				$arrDbColumn = array('stampRegister', 'stampUpdate', 'idEntity', 'numFiscalPeriod');
				$arrDbValue = array($stampRegister, $stampUpdate, $idEntity, $numFiscalPeriod);
				$classDb->insertRow(array(
					'idModule' => 'accounting',
					'strTable' => 'accountingFixedAssets' . $strNation,
					'arrColumn' => $arrDbColumn,
					'arrValue' => $arrDbValue,
				));
			}
			$this->_setDetailAddAdminMemoEntity($idEntity);
			$$this->_setDetailAddAccountMemoEntity($idEntity);
		}
	}

	/**
		$this->_setDetailAddAdminMemoEntity($idEntity)
	*/
	protected function _setDetailAddAdminMemoEntity($idEntity)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$tm = TIMESTAMP;
		$stampRegister = $tm;
		$stampUpdate = $tm;

		$arrayColumn = array(
			'jsonFixedAssetsNaviFolder',
			'jsonFixedAssetsEditorNaviFormat',
		);

		foreach ($arrayColumn as $keyColumn => $valueColumn) {
			$flagColumn = $valueColumn;
			$stmt = $dbh->prepare('insert into accountingAdminMemo (stampRegister, stampUpdate, idEntity, flagColumn) values (?, ?, ?, ?);');
			$stmt->execute(array($stampRegister, $stampUpdate, $idEntity, $flagColumn));
		}

	}

	/**
		$this->_setDetailAddAccountMemoEntity($idEntity)
	*/
	protected function _setDetailAddAccountMemoEntity($idEntity)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $varsAccounts;

		$tm = TIMESTAMP;
		$stampRegister = $tm;
		$stampUpdate = $tm;
		$array = &$varsAccounts;
		foreach ($array as $key => $value) {
			$idAccount = $value['id'];
			$arrayColumn = array(
				'jsonFixedAssetsNaviFolder',
				'jsonFixedAssetsNaviSearch',
				'jsonFixedAssetsEditorNaviFormat',
			);

			foreach ($arrayColumn as $keyColumn => $valueColumn) {
				$flagColumn = $valueColumn;
				$stmt = $dbh->prepare('insert into accountingAccountMemo (stampRegister, stampUpdate, idAccount, idEntity, flagColumn) values (?, ?, ?, ?, ?);');
				$stmt->execute(array($stampRegister, $stampUpdate, $idAccount, $idEntity, $flagColumn));
			}
		}
	}

	/**
	 */
	protected function _getVarsEntityNation($arr)
	{

		global $classDb;

		global $varsPluginAccountingAccount;

		$strNation = ucwords($this->_extSelf['strNation']);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingEntity' . $strNation,
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd' => 1,
			'arrWhere' => array(
				array(
					'flagType' => 'num',
					'strColumn' => 'idEntity',
					'flagCondition' => 'eq',
					'value' => $varsPluginAccountingAccount['idEntityCurrent'],
				),
				array(
					'flagType' => 'num',
					'strColumn' => 'numFiscalPeriod',
					'flagCondition' => 'eq',
					'value' => $arr['numFiscalPeriod'],
				),
			),
		));
		$array = $rows['arrRows'][0];
		foreach ($array as $key => $value) {
			$array[$key] = (int) $value;
		}

		return $array;
	}

	/**

	 */
	protected function _setTable()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$vars = $this->_getVars(array(
			'path' => $this->_extSelf['db'],
		));

		$strNation = ucwords($this->_extSelf['strNation']);

		$array = $vars;
		foreach ($array as $key => $value) {
			if (
				$value['table'] == 'accountingLogFixedAssets' . $strNation
				|| $value['table'] == 'accountingFixedAssets' . $strNation
			) {
				//drop
				$sql = 'drop table if exists ' . $value['table'] . ';';
				$stmt = $dbh->prepare($sql);
				$stmt->execute();

				//create
				$sql = 'create table ';
				$sql .= $value['table'] . '(';

				$arrayChild = $value['index'];
				$numLimit = count($arrayChild) - 1;
				$strColumn = '';
				foreach ($arrayChild as $keyChild => $valueChild) {
					$strColumn .= ' '
						. $arrayChild[$keyChild]['column']
						. ' '
						. $arrayChild[$keyChild]['type'];

					if ($keyChild != $numLimit) {
						$strColumn .= ',';

					}
				}
				$sql .= $strColumn . ')' . $value['db'] . ';';
				try {
					$dbh->beginTransaction();

					$stmt = $dbh->prepare($sql);
					$stmt->execute();

					$dbh->commit();
				} catch (PDOException $e) {
					$dbh->rollBack();

					$strTable = 'accountingLogFixedAssets' . $strNation;
					$sql = 'drop table if exists ' . $strTable . ';';
					$stmt = $dbh->prepare($sql);
					$stmt->execute();

					$strTable = 'accountingFixedAssets' . $strNation;
					$sql = 'drop table if exists ' . $strTable . ';';
					$stmt = $dbh->prepare($sql);
					$stmt->execute();

					if (FLAG_TEST) {
						var_dump($e->getMessage());
					}
					exit;
				}

			}
		}
	}
}
