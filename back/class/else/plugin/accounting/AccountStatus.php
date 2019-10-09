<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_AccountStatus extends Code_Else_Plugin_Accounting_Accounting
{
	protected $_extSelf = array(
		'varsConfig' => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/config.php',
	);

	public function run()
	{

	}

	public function allot($arr)
	{
		$method = '_ini' . ucwords($arr['flagStatus']);
		if (method_exists($this, $method)) {
			$this->$method($arr);

		} else {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}
	}

	/**
		(array(
			'flagType'        => 'accountStatus',
			'flagStatus'      => 'updateChargeEntity',
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idAccountNow'    => $idAccountNow,
			'idAccountNext'   => $idAccountNext,
			'idEntity'        => $idEntity,
			'stampRegister'   => TIMESTAMP,
		))
	 */
	protected function _iniUpdateChargeEntity($arr)
	{
		global $varsPluginAccountingEntity;

		$this->_removeLogFlagApply(array(
			'arrId'           => array($arr['idAccountNow']),
			'arrIdEntity'     => array($arr['idEntity']),
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		$this->_removeLogFlagApply(array(
			'arrId'           => array($arr['idAccountNext']),
			'arrIdEntity'     => array($arr['idEntity']),
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		$this->_updateLogCharge(array(
			'idEntity'         => $arr['idEntity'],
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'idAccountTarget'  => $arr['idAccountNow'],
			'idAccountCharge'  => $arr['idAccountNext'],
			'stampRegister'    => $arr['stampRegister'],
		));

		$this->_updateLogFileCharge(array(
			'idEntity'         => $arr['idEntity'],
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'idAccountTarget'  => $arr['idAccountNow'],
			'idAccountCharge'  => $arr['idAccountNext'],
			'stampRegister'    => $arr['stampRegister'],
		));
		$array = array('log', 'logFile');
		foreach ($array as $key => $value) {
			$this->_updateDbPreferenceStamp(array('strColumn' => $value));
		}
	}

	/**
		(array(
			'idEntity'         => $arr['idEntity'],
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'idAccountTarget'  => $arr['idAccountNow'],
			'idAccountCharge'  => $arr['idAccountNext'],
			'stampRegister'    => $arr['stampRegister'],
		))
	 */
	protected function _updateLogCharge($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLog',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idEntity',
					'flagCondition' => 'eq',
					'value'         => $arr['idEntity'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'numFiscalPeriod',
					'flagCondition' => 'eq',
					'value'         => $arr['numFiscalPeriod'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idAccount',
					'flagCondition' => 'eq',
					'value'         => $arr['idAccountTarget'],
				),
			),
		));

		$array = &$rows['arrRows'];
		foreach ($array as $key => $value) {
			$data = array(
				'stampRegister' => $arr['stampRegister'],
				'idAccount'     => $arr['idAccountCharge'],
			);

			$value['jsonChargeHistory'][] = $data;
			$jsonChargeHistory = json_encode($value['jsonChargeHistory']);
			$this->checkTextSize(array(
				'flag' => 'errorDataMax',
				'str'  => $jsonChargeHistory,
			));
			$idAccount = $arr['idAccountCharge'];

			$arrColumn = array('idAccount', 'jsonChargeHistory');
			$arrValue = array($idAccount, $jsonChargeHistory);

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingLog',
				'arrColumn' => $arrColumn,
				'arrWhere'  => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'id',
						'flagCondition' => 'eq',
						'value'         => $value['id'],
					),
				),
				'arrValue'  => $arrValue,
			));

		}
	}

	/**
		(array(
			'idEntity'         => $arr['idEntity'],
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'idAccountTarget'  => $arr['idAccountNow'],
			'idAccountCharge'  => $arr['idAccountNext'],
			'stampRegister'    => $arr['stampRegister'],
		))
	 */
	protected function _updateLogFileCharge($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogFile',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idEntity',
					'flagCondition' => 'eq',
					'value'         => $arr['idEntity'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'numFiscalPeriod',
					'flagCondition' => 'eq',
					'value'         => $arr['numFiscalPeriod'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idAccount',
					'flagCondition' => 'eq',
					'value'         => $arr['idAccountTarget'],
				),
			),
		));

		$array = &$rows['arrRows'];
		foreach ($array as $key => $value) {
			$data = array(
				'stampRegister' => $arr['stampRegister'],
				'idAccount'     => $arr['idAccountCharge'],
			);

			$value['jsonChargeHistory'][] = $data;
			$jsonChargeHistory = json_encode($value['jsonChargeHistory']);
			$this->checkTextSize(array(
				'flag' => 'errorDataMax',
				'str'  => $jsonChargeHistory,
			));
			$idAccount = $arr['idAccountCharge'];

			$arrColumn = array('idAccount', 'jsonChargeHistory');
			$arrValue = array($idAccount, $jsonChargeHistory);

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingLogFile',
				'arrColumn' => $arrColumn,
				'flagAnd'  => 1,
				'arrWhere'  => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idEntity',
						'flagCondition' => 'eq',
						'value'         => $arr['idEntity'],
					),
					array(
						'flagType'      => 'num',
						'strColumn'     => 'numFiscalPeriod',
						'flagCondition' => 'eqBig',
						'value'         => $arr['numFiscalPeriod'],
					),
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idLogFile',
						'flagCondition' => 'eq',
						'value'         => $value['idLogFile'],
					),
				),
				'arrValue'  => $arrValue,
			));

		}
	}


	/**
		(array(
			'flagType'       => 'accountStatus',
			'flagStatus'     => 'updateModule',
			'varsModuleNew'  => $arr['varsModuleNew'],
			'varsModulePast' => $arr['varsModulePast'],
			'arrId'          => $arr['arrId'],
		))
	 */
	protected function _iniUpdateModule($arr)
	{
		global $varsPluginAccountingEntity;

		$arrIdEntity = array();
		$array = $varsPluginAccountingEntity;
		foreach ($array as $key => $value) {
			$arrIdEntity[] = $key;
		}

		$flagUserPast = preg_match( "/,accounting,/", $arr['varsModulePast']['arrCommaIdModuleUser']);
		$flagUserNew = preg_match( "/,accounting,/", $arr['varsModuleNew']['arrCommaIdModuleUser']);
		$flagAdminPast = preg_match( "/(,accounting,|,base,)/", $arr['varsModulePast']['arrCommaIdModuleAdmin']);
		$flagAdminNew = preg_match( "/(,accounting,|,base,)/", $arr['varsModuleNew']['arrCommaIdModuleAdmin']);

		//Admin -> User
		if ($flagAdminPast && $flagUserPast && !$flagAdminNew && $flagUserNew) {
			$this->_removeLogFlagApply(array(
				'arrId'              => $arr['arrId'],
				'arrIdEntity'        => $arrIdEntity,
				'flagCheckAuthority' => 1,
				'flagAllUpdate'      => 0,
			));
			$this->_updateAccountFlagAdmin(array(
				'arrId'     => $arr['arrId'],
				'flagAdmin' => 0
			));
			$this->_updateDbPreferenceStamp(array('strColumn' => 'log'));

		//User -> Admin
		} elseif (!$flagAdminPast && $flagUserPast && $flagAdminNew && $flagUserNew) {
			$this->_updateAccountFlagAdmin(array(
				'arrId'     => $arr['arrId'],
				'flagAdmin' => 1
			));

		//none -> Admin
		} elseif (!$flagAdminPast && !$flagUserPast && $flagAdminNew && $flagUserNew) {
			$this->_updateAccountFlagAdmin(array(
				'arrId'     => $arr['arrId'],
				'flagAdmin' => 1
			));

		//none -> User
		} elseif (!$flagAdminPast && !$flagUserPast && !$flagAdminNew && $flagUserNew) {

		//Admin -> none
		} elseif ($flagAdminPast && $flagUserPast && !$flagAdminNew && !$flagUserNew) {
			$this->_removeLogFlagApply(array(
				'arrId'       => $arr['arrId'],
				'arrIdEntity' => $arrIdEntity,
			));
			$this->_updateAccountFlagAdmin(array(
				'arrId'     => $arr['arrId'],
				'flagAdmin' => 0
			));
			$this->_updateDbPreferenceStamp(array('strColumn' => 'log'));

		//User -> none
		} elseif (!$flagAdminPast && $flagUserPast && !$flagAdminNew && !$flagUserNew) {
			$this->_removeLogFlagApply(array(
				'arrId'              => $arr['arrId'],
				'arrIdEntity'        => $arrIdEntity,
				'flagCheckAuthority' => 1,
				'flagAllUpdate'      => 1,
			));
			$this->_removeLogFlagApply(array(
				'arrId'       => $arr['arrId'],
				'arrIdEntity' => $arrIdEntity,
			));
			$this->_updateDbPreferenceStamp(array('strColumn' => 'log'));

		//none -> none
		} else {

		}
	}

	/**
		(array(
			'flagType'    => 'accountStatus',
			'flagStatus'  => 'updateAccountEntity',
			'arrId'       => array($idAccount),
			'arrIdEntity' => array($idEntity),
		))
	 */
	protected function _iniUpdateAccountAuthority($arr)
	{
		$this->_removeLogFlagApply(array(
			'arrId'       => $arr['arrId'],
			'arrIdEntity' => $arr['arrIdEntity'],
		));
		$this->_updateDbPreferenceStamp(array('strColumn' => 'log'));
	}

	/**
		(array(
			'flagType'   => 'accountStatus',
			'flagStatus' => 'delete',
			'arrId'      => $arr['arrId'],
		))
	 */
	protected function _iniDelete($arr)
	{
		global $varsAccounts;
		global $classPluginAccountingInit;
		global $varsPluginAccountingEntity;

		$arrIdEntity = array();
		$array = $varsPluginAccountingEntity;
		foreach ($array as $key => $value) {
			$arrIdEntity[] = $key;
		}

		$this->_removeLogFlagApply(array(
			'arrId'       => $arr['arrId'],
			'arrIdEntity' => $arrIdEntity,
		));
		$this->_insertAccountId(array('arrId' => $arr['arrId']));
		$this->_deleteAccount(array('arrId' => $arr['arrId']));

		$array = array('adminMemo', 'account', 'accountId', 'log');
		foreach ($array as $key => $value) {
			$this->_updateDbPreferenceStamp(array('strColumn' => $value));
		}
		$classPluginAccountingInit->updateInitAccounts();
		$classPluginAccountingInit->updateInitAccountsId();
		$classPluginAccountingInit->updateInitAccount();
		$classPluginAccountingInit->updateInitAccountsEntity();

	}

	/**
	 */
	protected function _getEntityCurerntPeriod($arr)
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingAccountsEntity;

		$idEntity = $arr['idEntity'];
		$numFiscalPeriod = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];
		$numFiscalPeriodPrev = $numFiscalPeriod - 1;
		$numFiscalPeriodLock = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriodLock'];
		$net = $numFiscalPeriod - $numFiscalPeriodLock;

		if ($net == 2) {
			$data = array($numFiscalPeriodPrev, $numFiscalPeriod);

		} else {
			$data = array($numFiscalPeriod);
		}

		return $data;
	}

	/**
		(array(
			'idEntity'        => $idEntity,
			'numFiscalPeriod' => $valuePeriod,
		))
	 */
	protected function _getVarsApplyLog($arr)
	{
		global $classDb;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLog',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idEntity',
					'flagCondition' => 'eq',
					'value'         => $arr['idEntity'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'numFiscalPeriod',
					'flagCondition' => 'eq',
					'value'         => $arr['numFiscalPeriod'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'flagApply',
					'flagCondition' => 'eq',
					'value'         => 1,
				),
			),
		));


		return $rows['arrRows'];

	}

	/**
		(array(
			'arrId'              => $arr['arrId'],
			'arrIdEntity'        => $arrIdEntity,
			'flagCheckAuthority' => 1,
			'flagAllUpdate'      => 1,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _removeLogFlagApply($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsPluginAccountingAccount;
		global $varsPluginAccountingAccountsEntity;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAuthority;

		$stampRemove = TIMESTAMP;
		$flagRemove = 1;
		$flagApply = 0;
		$idAccountApply = 0;
		$flagApplyBack = 0;

		$array = $arr['arrId'];

		foreach ($array as $key => $value) {
			$idAccount = $value;
			$arrayEntity = $arr['arrIdEntity'];
			foreach ($arrayEntity as $keyEntity => $valueEntity) {
				$idEntity = $valueEntity;
				if ($arr['flagCheckAuthority']) {
					$varsAccountEntity = $varsPluginAccountingAccountsEntity[$idAccount][$idEntity];
					$idAuthority = $varsAccountEntity['idAuthority'];
					if ($arr['flagAllUpdate']) {
						if (!$varsPluginAccountingAuthority[$idAuthority]['flagAllUpdate']) {
							continue;
						}
					} else {
						if ($varsPluginAccountingAuthority[$idAuthority]['flagAllUpdate']) {
							continue;
						}
					}
				}

				$arrayPeriod = $this->_getEntityCurerntPeriod(array('idEntity' => $idEntity));
				if ($arr['numFiscalPeriod']) {
					$arrayPeriod = array($arr['numFiscalPeriod']);
				}

				foreach ($arrayPeriod as $keyPeriod => $valuePeriod) {
					$arrayLog = $this->_getVarsApplyLog(array(
						'idEntity'        => $idEntity,
						'numFiscalPeriod' => $valuePeriod,
					));
					foreach ($arrayLog as $keyLog => $valueLog) {
						if (!($valueLog['IdAccount'] == $idAccount || preg_match("/,$idAccount,/", $valueLog['arrCommaIdAccountPermit']))) {
							continue;
						}

						if ($valueLog['jsonPermitHistory']) {
							$arrayPermit = $valueLog['jsonPermitHistory'];
							foreach ($arrayPermit as $keyPermit => $valuePermit) {
								$arrayPermit[$keyPermit]['flagInvalid'] = 1;
							}
							$valueLog['jsonPermitHistory'] = $arrayPermit;

						}

						$valueLog['jsonPermitHistory'] = json_encode($valueLog['jsonPermitHistory']);
						$this->checkTextSize(array(
							'flag' => 'errorDataMax',
							'str'  => $valueLog['jsonPermitHistory'],
						));
						$jsonPermitHistory = $valueLog['jsonPermitHistory'];
						$classDb->updateRow(array(
							'idModule'  => 'accounting',
							'strTable'  => 'accountingLog',
							'arrColumn' => array('flagApply', 'idAccountApply', 'flagApplyBack', 'jsonPermitHistory', 'stampRemove', 'flagRemove'),
							'arrWhere'  => array(
								array(
									'flagType'      => 'num',
									'strColumn'     => 'id',
									'flagCondition' => 'eq',
									'value'         => $valueLog['id'],
								),
							),
							'arrValue'  => array($flagApply, $idAccountApply, $flagApplyBack, $jsonPermitHistory, $stampRemove, $flagRemove),
						));
					}
				}
			}
		}

	}

	/**
		(array(
			'arrId'      => $arr['arrId'],
		))
	 */
	protected function _insertAccountId($arr)
	{
		global $varsPluginAccountingAccountsId;
		global $varsAccounts;

		global $classDb;
		$dbh = $classDb->getHandle();

		$array = $arr['arrId'];
		foreach ($array as $key => $value) {
			$idAccount = $value;
			$strCodeName = $varsAccounts[$idAccount]['strCodeName'];

			if ($varsPluginAccountingAccountsId[$idAccount]){
				$classDb->updateRow(array(
					'idModule'  => 'accounting',
					'strTable'  => 'accountingAccountId',
					'arrColumn' => array('strCodeName'),
					'flagAnd'   => 1,
					'arrWhere'  => array(
						array(
							'flagType'      => 'num',
							'strColumn'     => 'id',
							'flagCondition' => 'eq',
							'value'         => $idAccount,
						),
					),
					'arrValue'  => array($strCodeName),
				));

			} else {
				$stmt = $dbh->prepare('insert into accountingAccountId (id, strCodeName) values (?, ?);');
				$stmt->execute(array($idAccount, $strCodeName));
			}
		}
	}

	/**
		(array(
			'arrId'     => $arr['arrId'],
			'flagAdmin' => 0,
		))
	 */
	protected function _updateAccountFlagAdmin($arr)
	{
		global $classPluginAccountingInit;
		global $classDb;
		$dbh = $classDb->getHandle();

		$flagAdmin = $arr['flagAdmin'];


		$array = $arr['arrId'];
		foreach ($array as $key => $value) {
			$idAccount = $value;
			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingAccount',
				'arrColumn' => array('flagAdmin'),
				'flagAnd'   => 1,
				'arrWhere'  => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idAccount',
						'flagCondition' => 'eq',
						'value'         => $idAccount,
					),
				),
				'arrValue'  => array($flagAdmin),
			));

			if ($flagAdmin) {
				$idAuthority = 1;
				$idAccess = 1;
				$classDb->updateRow(array(
					'idModule'  => 'accounting',
					'strTable'  => 'accountingAccountEntity',
					'arrColumn' => array('idAuthority', 'idAccess'),
					'flagAnd'   => 1,
					'arrWhere'  => array(
						array(
							'flagType'      => 'num',
							'strColumn'     => 'idAccount',
							'flagCondition' => 'eq',
							'value'         => $idAccount,
						),
					),
					'arrValue'  => array($idAuthority, $idAccess),
				));
			}
		}

		$this->_updateDbPreferenceStamp(array('strColumn' => 'account'));
		$classPluginAccountingInit->updateInitAccounts();
		$classPluginAccountingInit->updateInitAccount();
		$classPluginAccountingInit->updateInitAccountsEntity();
	}

	/**
		(array(
			'arrId'      => $arr['arrId'],
		))
	 */
	protected function _deleteAccount($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$arrayLoop = array(
			array('strTable'  => 'accountingAccount',),
			array('strTable'  => 'accountingAccountEntity',),
			array('strTable'  => 'accountingAccountMemo',),
		);

		$array = $arr['arrId'];
		foreach ($array as $key => $value) {
			foreach ($arrayLoop as $keyLoop => $valueLoop) {
				$classDb->deleteRow(array(
					'idModule' => 'accounting',
					'strTable'  => $valueLoop['strTable'],
					'arrWhere'  => array(
						array(
							'flagType'      => 'num',
							'strColumn'     => 'idAccount',
							'flagCondition' => 'eq',
							'value'         => $value,
						),
					),
				));
			}
		}
	}

	/**
		(array(
			'varsAccount' => $arr['varsAccount'],
		))
	 */
	protected function _checkModuleAdmin($arr)
	{
		global $varsModule;
		global $varsAccounts;

		if ($varsAccounts[$arr['varsAccount']['id']]['flagWebmaster']) {
			return 1;
		}

		$idModule = $arr['varsAccount']['idModule'];
		$strModule = ',' . $arr['strModule'] . ',';
		if ( preg_match( "/$strModule|,base,/", $varsModule[$idModule]['arrCommaIdModuleAdmin'])) {
			return 1;
		}

		return 0;
	}

	/**
	 *
	 */
	protected function _iniInsert($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $classPluginAccountingInit;

		$idModule = $arr['varsAccount']['idModule'];

		$tm = TIMESTAMP;
		$stampRegister = $tm;
		$stampUpdate = $tm;
		$idAccount = $arr['varsAccount']['id'];

		$flagAdmin = $this->_checkModuleAdmin(array('varsAccount' => $arr['varsAccount'],));

		$arrCommaIdEntity = '';
		$idEntityCurrent = null;
		$numFiscalPeriodCurrent = null;

		$stmt = $dbh->prepare('insert into accountingAccount (stampRegister, stampUpdate, idAccount, flagAdmin, arrCommaIdEntity, idEntityCurrent, numFiscalPeriodCurrent) values (?, ?, ?, ?, ?, ?, ?);');
		$stmt->execute(array($stampRegister, $stampUpdate, $idAccount, $flagAdmin, $arrCommaIdEntity, $idEntityCurrent, $numFiscalPeriodCurrent));

		$array = array(
			'jsonEntityNaviSearch',
			'jsonAccountNaviSearch',
			'jsonAuthorityNaviSearch',
		);

		foreach ($array as $key => $value) {
			$flagColumn = $value;
			$stmt = $dbh->prepare('insert into accountingAccountMemo (stampRegister, stampUpdate, idAccount, flagColumn) values (?, ?, ?, ?);');
			$stmt->execute(array($tm, $tm, $idAccount, $flagColumn));
		}


		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingEntity',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'arrWhere' => array(),
		));

		$array = $rows['arrRows'];

		foreach ($array as $key => $value) {
			$idEntity = $value['id'];
			$idAccess = 1;
			$idAuthority = 1;

			$stmt = $dbh->prepare('insert into accountingAccountEntity (idAccount, idEntity, idAccess, idAuthority) values (?, ?, ?, ?);');
			$stmt->execute(array($idAccount, $idEntity, $idAccess, $idAuthority));

			$arrayColumn = array(
				'jsonAccountEntityAuthorityNaviSearch',
				'jsonEntityDepartmentNaviSearch',
				'jsonFileAccountEntityNaviSearch',
				'jsonLogImportNaviSearch', 'jsonLogImportEditorNaviFormat',
				'jsonLogHouseNaviSearch', 'jsonLogHouseEditorNaviFormat',
				'jsonSubAccountTitleNaviSearch',
				'jsonBanksAccountNaviSearch',
				'jsonBanksNaviSearch',
				'jsonLogNaviSearch', 'jsonLogEditorNaviFormat',
				'jsonLogFileNaviSearch',
				'jsonAccessNaviSearch',
				'jsonFixedAssetsNaviSearch', 'jsonFixedAssetsEditorNaviFormat',
				'jsonCashNaviSearch', 'jsonCashEditorNaviFormat',
			);

			foreach ($arrayColumn as $keyColumn => $valueColumn) {
				$flagColumn = $valueColumn;
				$stmt = $dbh->prepare('insert into accountingAccountMemo (stampRegister, stampUpdate, idAccount, idEntity, flagColumn) values (?, ?, ?, ?, ?);');
				$stmt->execute(array($stampRegister, $stampUpdate, $idAccount, $idEntity, $flagColumn));
			}

		}

		$array = array('account', 'accountMemo', 'accountEntity', 'accountMemoEntity');
		foreach ($array as $key => $value) {
			$this->_updateDbPreferenceStamp(array('strColumn' => $value));
		}
		$classPluginAccountingInit->updateInitAccounts();
		$classPluginAccountingInit->updateInitAccountsEntity();

	}





}
