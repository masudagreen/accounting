<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Rebuild extends Code_Else_Lib_Rebuild
{
	protected $_extSelf = array(
		'path' => array(
			'file' => array(
				'db'         => 'back/tpl/templates/else/plugin/accounting/db/config.php',
				'varsConfig' => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/config.php',
				'cssPlugin'  => 'back/tpl/templates/else/plugin/accounting/css/rebuild.css',
				'varsPlugin' => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/rebuild.php',
		    ),
		),
		'vars' => array(
			'strNation' => 'jpn',
		),
	);

    function __construct()
    {

    }


    /**
     *
     */
	public function run($arr)
	{
		if ($arr['flagType'] == 'rebuildCss') {
			$this->_iniCss(array());

		} elseif ($arr['flagType'] == 'rebuildJsRoot') {
			return $this->_getJsRoot($arr);

		} elseif ($arr['flagType'] == 'rebuildDbTable') {
			$this->_iniDbTable(array());

		} elseif ($arr['flagType'] == 'rebuildDbInsert') {
			return $this->_iniDbInsert(array());

		} elseif ($arr['flagType'] == 'rebuildDbInsertAccount') {
			return $this->_iniDbInsertAccount($arr);

		}
	}

    /**
		$this->_getVars(array(
			'path'    => '',
			'strLang' => '',
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
			'data'    => $arr['path'],
			'arr' => array(
				array('before' => '<strTitle>', 'after' => $arr['strTitle'],),
				array('before' => '<strLang>', 'after' => $arr['strLang'],),
				array('before' => '<strNation>', 'after' => $arr['strNation'],),
			),
		));

		return $vars;
	}

	/**
	 *
	 */
	protected function _iniCss($arr)
	{
		$this->_setCss();
	}

	/**
	 *
	 */
	protected function _setCss()
	{
		global $classFile;

		$contents = file_get_contents($this->_extSelf['path']['file']['cssPlugin']);

		$classFile->addData(array(
			'path' => $this->_self['path']['file']['outCss'],
			'data' => $contents,
		));

	}

	/**
	 *
	 */
	protected function _getJsRoot($arr)
	{
		$vars = $this->_getVars(array(
			'path'     => $this->_extSelf['path']['file']['varsPlugin'],
			'strLang'  => $arr['strLang'],
		));

		return $vars;
	}

	/**
	 *
	 */
	protected function _iniDbTable($arr)
	{
		return $this->_setDbTable(array(
			'path'  => $this->_extSelf['path']['file']['db'],
		));
	}

    /**
     *
     */
	protected function _iniDbInsertAccount($arr)
	{
		$this->_setDbInsertAccount($arr);
	}

	/**
	 *
	 */
	protected function _setDbInsertAccount($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$tm = TIMESTAMP;
		$idAccount = $arr['varsAccount']['id'];
		$arrCommaIdEntity = '';
		$idEntityCurrent = null;
		$numFiscalPeriodCurrent = null;

		$stmt = $dbh->prepare('insert into accountingAccount (stampRegister, stampUpdate, idAccount, arrCommaIdEntity, idEntityCurrent, numFiscalPeriodCurrent) values (?, ?, ?, ?, ?, ?);');
		$stmt->execute(array($tm, $tm, $idAccount, $arrCommaIdEntity, $idEntityCurrent, $numFiscalPeriodCurrent));

		//account memo
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

			$stmt = $dbh->prepare('insert into accountingAccountEntity (idAccount, idEntity) values (?, ?);');
			$stmt->execute(array($idAccount, $idEntity));

			$arrayColumn = array(
				'jsonAccountEntityAuthorityNaviSearch',
				'jsonFileAccountEntityNaviSearch',
				'jsonEntityDepartmentNaviSearch',
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

			//account entity memo
			foreach ($arrayColumn as $keyColumn => $valueColumn) {
				$flagColumn = $valueColumn;
				$stmt = $dbh->prepare('insert into accountingAccountMemo (stampRegister, stampUpdate, idAccount, idEntity, flagColumn) values (?, ?, ?, ?, ?);');
				$stmt->execute(array($tm, $tm, $idAccount, $idEntity, $flagColumn));
			}

		}

		$this->_updatePreferenceStampAccounts();

	}

	/*
	 *
	 * */
	protected function _updatePreferenceStampAccounts()
	{
		global $classDb;

		$this->_setInitPreference();
		global $varsPluginAccountingPreference;

		$varsPluginAccountingPreference['jsonStampUpdate']['accounts'] = TIMESTAMP;
		$jsonStampUpdate = json_encode($varsPluginAccountingPreference['jsonStampUpdate']);
		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingPreference',
			'arrColumn' => array('jsonStampUpdate'),
			'arrWhere'  => array(),
			'arrValue'  => array($jsonStampUpdate),
		));
		$this->_updateInitPreference();
	}

	/*
	 *
	 * */
	protected function _setInitPreference()
	{
		global $classInit;
		global $varsPluginAccountingPreference;

		$varsPluginAccountingPreference = (FLAG_APC)? apc_fetch('varsPluginAccountingPreference'): null;
		if (is_null($varsPluginAccountingPreference)) {
			$this->_updateInitPreference();
		}
	}

   /**
     */
	protected function _updateInitPreference()
	{
		global $classInit;
		global $varsPluginAccountingPreference;

 		$classInit->updateVar(array(
			'vars'     => &$varsPluginAccountingPreference,
			'strVars'  => 'varsPluginAccountingPreference',
			'strTable' => 'accountingPreference',
		));
	}

	/**
	 *
	 */
	protected function _iniDbInsert($arr)
	{
		$this->_setDbInsert(array(
			'path'  => $this->_extSelf['path']['file']['db'],
		));

	}

	/**
	 *
	 */
	protected function _setDbInsertAccountingPreference()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$tm = TIMESTAMP;
		$stampRegister = $tm;
		$stampUpdate = $tm;
		$arrVersion = array();
		$arrVersion[NUM_VERSION] = 1;
		$jsonVersion = json_encode($arrVersion);
		$strVersion = NUM_VERSION;

		$stmt = $dbh->prepare('insert into accountingPreference (stampRegister, stampUpdate, jsonVersion, strVersion) values (?, ?, ?, ?);');
		$stmt->execute(array($stampRegister, $stampUpdate, $jsonVersion, $strVersion));

	}

	/**
	 *
	 */
	protected function _setDbInsertAccountingAuthority()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$vars = $this->_getVars(array(
			'path'    => $this->_extSelf['path']['file']['varsConfig'],
			'strLang' => STR_SYSTEM_LANG,
		));
		$tm = TIMESTAMP;
		$stampRegister = $tm;
		$stampUpdate = $tm;
		$flagDefault = 1;
		$strTitle = $vars['strAuthorityAll'];

		$stmt = $dbh->prepare('insert into accountingAuthority (stampRegister, stampUpdate, strTitle, flagDefault) values (?, ?, ?, ?);');
		$stmt->execute(array($stampRegister, $stampUpdate, $strTitle, $flagDefault));

		$flagMySelect = 1;
		$flagMyInsert = 0;
		$flagMyDelete = 0;
		$flagMyUpdate = 0;
		$flagMyOutput = 0;
		$flagAllSelect = 0;
		$flagAllInsert = 0;
		$flagAllDelete = 0;
		$flagAllUpdate = 0;
		$flagAllOutput = 0;

		$strTitle = $vars['strAuthorityMy'];
		$stmt = $dbh->prepare('insert into accountingAuthority (stampRegister, stampUpdate, strTitle, flagMySelect, flagMyInsert, flagMyDelete, flagMyUpdate, flagMyOutput, flagAllSelect, flagAllInsert, flagAllDelete, flagAllUpdate, flagAllOutput, flagDefault) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);');
		$stmt->execute(array($stampRegister, $stampUpdate, $strTitle, $flagMySelect, $flagMyInsert, $flagMyDelete, $flagMyUpdate, $flagMyOutput, $flagAllSelect, $flagAllInsert, $flagAllDelete, $flagAllUpdate, $flagAllOutput, $flagDefault));
	}

	/**
	 *
	 */
	protected function _setDbInsertAccountingAccess()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$vars = $this->_getVars(array(
			'path'    => $this->_extSelf['path']['file']['varsConfig'],
			'strLang' => STR_SYSTEM_LANG,
		));
		$tm = TIMESTAMP;
		$stampRegister = $tm;
		$stampUpdate = $tm;
		$flagDefault = 1;
		$idEntity = 0;
		$idAccess = 1;
		$strTitle = $vars['strAccessAll'];

		$stmt = $dbh->prepare('insert into accountingAccess (stampRegister, stampUpdate, idEntity, idAccess, strTitle, flagDefault) values (?, ?, ?, ?, ?, ?);');
		$stmt->execute(array($stampRegister, $stampUpdate, $idEntity, $idAccess, $strTitle, $flagDefault));

		$idAccess = 2;
		$strTitle = $vars['strAccessNone'];
		$stmt = $dbh->prepare('insert into accountingAccess (stampRegister, stampUpdate, idEntity, idAccess, strTitle, flagDefault) values (?, ?, ?, ?, ?, ?);');
		$stmt->execute(array($stampRegister, $stampUpdate, $idEntity, $idAccess, $strTitle, $flagDefault));
	}



}
