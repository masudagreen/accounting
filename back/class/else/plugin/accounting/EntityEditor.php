<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_EntityEditor extends Code_Else_Plugin_Accounting_Entity
{
	protected $_childSelf = array(
		'pathTplJs'    => 'else/plugin/accounting/js/entityEditor.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/entityEditor.php',
		'pathCurrency' => 'back/dat/currency/<strLang>/list.csv',
		'pathConfig'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/config.php',
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
		global $classEscape;
		global $classPluginAccountingInit;

		global $varsPluginAccountingEntity;

		$dbh = $classDb->getHandle();

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$vars['portal']['varsDetail']['templateDetail'] = $this->_updateVars(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail'],
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

		if ($arrValue['arr']['idEntity'] > 0 && !$varsPluginAccountingEntity[$arrValue['arr']['idEntity']]) {
			$this->sendVars(array(
				'flag'    => 'idEntity',
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}
		if ($varsPluginAccountingEntity[$arrValue['arr']['idEntity']]['flagConfig']) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}

		$tm = TIMESTAMP;
		$stampRegister = $tm;
		$stampUpdate = $tm;
		$strTitle = $arrValue['arr']['strTitle'];
		$strNation = $arrValue['arr']['strNation'];
		$strLang = $arrValue['arr']['strLang'];

		$numFiscalPeriodStart = (int) $arrValue['arr']['numFiscalPeriod'];
		$numFiscalPeriod = $numFiscalPeriodStart;
		$numFiscalPeriodLock = $numFiscalPeriod - 1;

		$idEntityCopy = $arrValue['arr']['idEntity'];
		$flagConfig = (!$idEntityCopy)? 1 : 0;
		$strCurrency = $this->_getStrCurrency($strNation);
		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrValue['arr']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));
		$numFiscalPeriodCopy = $varsPluginAccountingEntity[$arrValue['arr']['idEntity']]['numFiscalPeriod'];

		try {
			$dbh->beginTransaction();

			$idEntity = $classDb->insertRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingEntity',
				'arrColumn' => array('stampRegister', 'stampUpdate', 'strTitle', 'strNation', 'strLang', 'strCurrency', 'numFiscalPeriodStart', 'numFiscalPeriod', 'numFiscalPeriodLock', 'arrSpaceStrTag', 'flagConfig'),
				'arrValue'  => array($stampRegister, $stampUpdate, $strTitle, $strNation, $strLang, $strCurrency, $numFiscalPeriodStart, $numFiscalPeriod, $numFiscalPeriodLock, $arrSpaceStrTag, $flagConfig),
			));

			$this->_setDetailAddAccountMemoEntity($idEntity);
			$this->_setDetailAddAccountEntity($idEntity);
			$this->_setDetailAddFile($idEntity, $numFiscalPeriod);
			$this->_setDetailAddLogMail($idEntity, $strNation, $numFiscalPeriod);
			$this->_setDetailAddCash($idEntity, $numFiscalPeriod);
			$this->_setDetailAddCashValue($idEntity, $numFiscalPeriod);
			$this->_setDetailAddBanks($idEntity, $numFiscalPeriod);

			if (!$flagConfig) {
				$this->_setDetailAddAccountingFSCopy($numFiscalPeriod, $idEntity, $strNation, $numFiscalPeriodCopy, $idEntityCopy);
				$this->_setDetailAddAccountingEntityCopy($idEntity, $strNation, $numFiscalPeriod, $numFiscalPeriodCopy, $idEntityCopy);
				$this->_setDetailAddAccountingFixedAssetsCopy($numFiscalPeriod, $idEntity, $strNation, $numFiscalPeriodCopy, $idEntityCopy);

			} else {
				$this->_setDetailAddAccountingFS($numFiscalPeriod, $idEntity, $strNation, $strLang);
				$this->_setDetailAddEntity($idEntity, $strNation, $numFiscalPeriod);
				$this->_setDetailAddAccountingFixedAssets($numFiscalPeriod, $idEntity, $strNation, $strLang);
			}

			$this->_setDetailAddAccountingFSValue($numFiscalPeriod, $idEntity, $strNation);
			$this->_setDetailAddAccountingFSId($idEntity, $strNation);

			$array = array('fs', 'entity', 'account', 'adminMemo');
			foreach ($array as $key => $value) {
				$this->_updateDbPreferenceStamp(array('strColumn' => $value));
			}

			$path = PATH_BACK_DAT_FILE . 'accounting/';
			if (!is_dir($path)) {
				mkdir($path);
			}

			$path = PATH_BACK_DAT_FILE . 'accounting/' . $idEntity;
			if (!is_dir($path)) {
				mkdir($path);
			}

			$dbh->commit();

		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$classPluginAccountingInit->updateInitEntity();
		$classPluginAccountingInit->updateInitPreference();
		$classPluginAccountingInit->updateInitAccounts();
		$classPluginAccountingInit->updateInitAccountsEntity();

		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
		$this->_setSearch(array('flag' => 1));
	}

    /**
		$this->_getStrCurrency($strNation);
     */
	protected function _getStrCurrency($strNation)
	{
		global $classFile;

		$path = $this->getPath(array(
			'path' => $this->_childSelf['pathCurrency'],
		));
		$array = $classFile->getCsvRows(array('path' => $path));
		foreach ($array as $key => $value) {
			if ($strNation == $value['nation']) {
				return $value['code'];
			}
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

			foreach ($arrayColumn as $keyColumn => $valueColumn) {
				$flagColumn = $valueColumn;
				$stmt = $dbh->prepare('insert into accountingAccountMemo (stampRegister, stampUpdate, idAccount, idEntity, flagColumn) values (?, ?, ?, ?, ?);');
				$stmt->execute(array($stampRegister, $stampUpdate, $idAccount, $idEntity, $flagColumn));
			}
		}
	}

	/**

	*/
	protected function _setDetailAddBanks($idEntity, $numFiscalPeriod)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$stampRegister = TIMESTAMP;
		$stampUpdate = TIMESTAMP;
		$strTable = 'accountingBanks';

		$stmt = $dbh->prepare('insert into ' . $strTable . '(stampRegister, stampUpdate, idEntity, numFiscalPeriod) values (?, ?, ?, ?);');
		$stmt->execute(array($stampRegister, $stampUpdate, $idEntity, $numFiscalPeriod));
	}

	/**

	*/
	protected function _setDetailAddCash($idEntity, $numFiscalPeriod)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$stampRegister = TIMESTAMP;
		$stampUpdate = TIMESTAMP;
		$arrJson = array('cash' => 1, 'prettyCash' => 1, 'checkingAccounts' => 1, 'ordinaryDeposit' => 1, 'depositAtNotice' => 1, 'jpDeposit' => 1,);
		$jsonCash = json_encode($arrJson);

		$strTable = 'accountingCash';

		$stmt = $dbh->prepare('insert into ' . $strTable . '(stampRegister, stampUpdate, idEntity, numFiscalPeriod, jsonCash) values (?, ?, ?, ?, ?);');
		$stmt->execute(array($stampRegister, $stampUpdate, $idEntity, $numFiscalPeriod, $jsonCash));
	}

	/**
		$this->_setDetailAddFile($idEntity)
	*/
	protected function _setDetailAddCashValue($idEntity, $numFiscalPeriod)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$stampRegister = TIMESTAMP;
		$stampUpdate = TIMESTAMP;

		$flagPay = 1;
		$numFiscalPeriodValue = $numFiscalPeriod;
		$stmt = $dbh->prepare('insert into accountingCashValue(stampRegister, stampUpdate, idEntity, numFiscalPeriod, numFiscalPeriodValue, flagPay) values (?, ?, ?, ?, ?, ?);');
		$stmt->execute(array($stampRegister, $stampUpdate, $idEntity, $numFiscalPeriod, $numFiscalPeriodValue, $flagPay));

		for ($i = 0; $i < 3; $i++) {
			$flagPay = 0;
			$numFiscalPeriodValue = $numFiscalPeriod + $i;
			$stmt = $dbh->prepare('insert into accountingCashValue(stampRegister, stampUpdate, idEntity, numFiscalPeriod, numFiscalPeriodValue, flagPay) values (?, ?, ?, ?, ?, ?);');
			$stmt->execute(array($stampRegister, $stampUpdate, $idEntity, $numFiscalPeriod, $numFiscalPeriodValue, $flagPay));
		}
	}

	/**
		$this->_setDetailAddFile($idEntity)
	*/
	protected function _setDetailAddLogMail($idEntity, $strNation, $numFiscalPeriod)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$stampRegister = TIMESTAMP;
		$stampUpdate = TIMESTAMP;

		$strTable = 'accountingLogMail' . ucwords($strNation);

		$stmt = $dbh->prepare('insert into ' . $strTable . '(stampRegister, stampUpdate, idEntity, numFiscalPeriod) values (?, ?, ?, ?);');
		$stmt->execute(array($stampRegister, $stampUpdate, $idEntity, $numFiscalPeriod));
	}

	/**
		$this->_setDetailAddFile($idEntity)
	*/
	protected function _setDetailAddFile($idEntity, $numFiscalPeriod)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$stampRegister = TIMESTAMP;
		$stampUpdate = TIMESTAMP;
		$jsonFileType = array('pdf' => 1, 'jpeg' => 1, 'jpg' => 1, 'png' => 1, 'gif' => 1,);
		$jsonFileType = json_encode($jsonFileType);

		$stmt = $dbh->prepare('insert into accountingFile (stampRegister, stampUpdate, idEntity, numFiscalPeriod, jsonFileType) values (?, ?, ?, ?, ?);');
		$stmt->execute(array($stampRegister, $stampUpdate, $idEntity, $numFiscalPeriod, $jsonFileType));
	}

	/**
		$this->_setDetailAddAccountEntity($idEntity)
	*/
	protected function _setDetailAddAccountEntity($idEntity)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $varsAccounts;
		global $varsPluginAccountingAccounts;

		$array = &$varsAccounts;
		foreach ($array as $key => $value) {
			$idAccount = $value['id'];
			$idAccess = 1;
			$idAuthority = 1;

			$stmt = $dbh->prepare('insert into accountingAccountEntity (idAccount, idEntity, idAccess, idAuthority) values (?, ?, ?, ?);');
			$stmt->execute(array($idAccount, $idEntity, $idAccess, $idAuthority));
		}

	}

	/**
		$this->_setDetailAddAccountingFSCopy($numFiscalPeriod, $idEntity, $strNation, $numFiscalPeriodCopy, $idEntityCopy);
	*/
	protected function _setDetailAddAccountingFSCopy($numFiscalPeriod, $idEntity, $strNation, $numFiscalPeriodCopy, $idEntityCopy)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingFS' . ucwords($strNation),
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idEntity',
					'flagCondition' => 'eq',
					'value'         => $idEntityCopy,
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'numFiscalPeriod',
					'flagCondition' => 'eq',
					'value'         => $numFiscalPeriodCopy,
				),
			),
		));

		$stampRegister = TIMESTAMP;
		$stampUpdate = TIMESTAMP;

		$flagCR = 0;
		if ($rows['arrRows'][0]['jsonJgaapFSCR']) {
			$flagCR = 1;
		}

		$jsonJgaapFSPL = json_encode($rows['arrRows'][0]['jsonJgaapFSPL']);
		$jsonJgaapFSBS = json_encode($rows['arrRows'][0]['jsonJgaapFSBS']);
		$jsonJgaapFSCR = ($flagCR)? json_encode($rows['arrRows'][0]['jsonJgaapFSCR']) : '';
		$jsonJgaapFSCS = json_encode($rows['arrRows'][0]['jsonJgaapFSCS']);
		$jsonJgaapAccountTitlePL = json_encode($rows['arrRows'][0]['jsonJgaapAccountTitlePL']);
		$jsonJgaapAccountTitleBS = json_encode($rows['arrRows'][0]['jsonJgaapAccountTitleBS']);
		$jsonJgaapAccountTitleCR = ($flagCR)? json_encode($rows['arrRows'][0]['jsonJgaapAccountTitleCR']) : '';


		$strTable = 'accountingFS' . ucwords($strNation);

		$stmt = $dbh->prepare('insert into ' . $strTable . '(stampRegister, stampUpdate, numFiscalPeriod, idEntity, jsonJgaapAccountTitlePL, jsonJgaapAccountTitleBS, jsonJgaapAccountTitleCR, jsonJgaapFSPL, jsonJgaapFSBS, jsonJgaapFSCR, jsonJgaapFSCS) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);');
		$stmt->execute(array($stampRegister, $stampUpdate, $numFiscalPeriod, $idEntity, $jsonJgaapAccountTitlePL, $jsonJgaapAccountTitleBS, $jsonJgaapAccountTitleCR, $jsonJgaapFSPL, $jsonJgaapFSBS, $jsonJgaapFSCR, $jsonJgaapFSCS));
	}

	/**
		$this->_setDetailAddAccountingFixedAssetsCopy($numFiscalPeriod, $idEntity, $strNation, $numFiscalPeriodCopy, $idEntityCopy);
	*/
	protected function _setDetailAddAccountingFixedAssetsCopy($numFiscalPeriod, $idEntity, $strNation, $numFiscalPeriodCopy, $idEntityCopy)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingFixedAssets' . ucwords($strNation),
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idEntity',
					'flagCondition' => 'eq',
					'value'         => $idEntityCopy,
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'numFiscalPeriod',
					'flagCondition' => 'eq',
					'value'         => $numFiscalPeriodCopy,
				),
			),
		));

		$tm = TIMESTAMP;
		$stampRegister = $tm;
		$stampUpdate = $tm;

		$data = $rows['arrRows'][0];
		$flagDepWrite = $data['flagDepWrite'];
		$flagLossWrite = $data['flagLossWrite'];
		$flagFractionDepWrite = $data['flagFractionDepWrite'];
		$flagFractionDep = $data['flagFractionDep'];
		$flagFractionDepSurvivalRate = $data['flagFractionDepSurvivalRate'];
		$flagFractionDepSurvivalRateLimit = $data['flagFractionDepSurvivalRateLimit'];
		$flagFractionRatioOperate = $data['flagFractionRatioOperate'];
		$jsonAccountTitle = ($data['jsonAccountTitle'])? json_encode($data['jsonAccountTitle']) : '';

		$strTable = 'accountingFixedAssets' . ucwords($strNation);

		$stmt = $dbh->prepare('insert into ' . $strTable . '(stampRegister, stampUpdate, numFiscalPeriod, idEntity, flagDepWrite, flagLossWrite, flagFractionDepWrite, flagFractionDep, flagFractionDepSurvivalRate, flagFractionDepSurvivalRateLimit, flagFractionRatioOperate, jsonAccountTitle) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);');
		$stmt->execute(array($stampRegister, $stampUpdate, $numFiscalPeriod, $idEntity, $flagDepWrite, $flagLossWrite, $flagFractionDepWrite, $flagFractionDep, $flagFractionDepSurvivalRate, $flagFractionDepSurvivalRateLimit, $flagFractionRatioOperate, $jsonAccountTitle));
	}

	/**
		$this->_setDetailAddAccountingEntityCopy($idEntity, $strNation, $numFiscalPeriod, $numFiscalPeriodCopy, $idEntityCopy)
	*/
	protected function _setDetailAddAccountingEntityCopy($idEntity, $strNation, $numFiscalPeriod, $numFiscalPeriodCopy, $idEntityCopy)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingEntity' .  ucwords($strNation),
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idEntity',
					'flagCondition' => 'eq',
					'value'         => $idEntityCopy,
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'numFiscalPeriod',
					'flagCondition' => 'eq',
					'value'         => $numFiscalPeriodCopy,
				),
			),
		));

		$data = $rows['arrRows'][0];
		$stampFiscalBeginning = $data['stampFiscalBeginning'];
		$numFiscalBeginningYear = $data['numFiscalBeginningYear'];
		$numFiscalBeginningMonth = $data['numFiscalBeginningMonth'];
		$flagCorporation = $data['flagCorporation'];
		$flagCR = $data['flagCR'];
		$flagConsumptionTaxFree = $data['flagConsumptionTaxFree'];
		$flagConsumptionTaxGeneralRule = $data['flagConsumptionTaxGeneralRule'];
		$flagConsumptionTaxDeducted = $data['flagConsumptionTaxDeducted'];
		$flagConsumptionTaxIncluding = $data['flagConsumptionTaxIncluding'];
		$flagConsumptionTaxCalc = $data['flagConsumptionTaxCalc'];
		$flagConsumptionTaxWithoutCalc = $data['flagConsumptionTaxWithoutCalc'];

		$strTable = 'accountingEntity' . ucwords($strNation);

		$stmt = $dbh->prepare('insert into ' . $strTable . '(idEntity, numFiscalPeriod, stampFiscalBeginning, numFiscalBeginningYear, numFiscalBeginningMonth, flagCorporation, flagCR, flagConsumptionTaxFree, flagConsumptionTaxGeneralRule, flagConsumptionTaxDeducted, flagConsumptionTaxIncluding, flagConsumptionTaxCalc, flagConsumptionTaxWithoutCalc) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);');
		$stmt->execute(array($idEntity, $numFiscalPeriod, $stampFiscalBeginning, $numFiscalBeginningYear, $numFiscalBeginningMonth, $flagCorporation, $flagCR, $flagConsumptionTaxFree, $flagConsumptionTaxGeneralRule, $flagConsumptionTaxDeducted, $flagConsumptionTaxIncluding, $flagConsumptionTaxCalc, $flagConsumptionTaxWithoutCalc));
	}

	/**
		$this->_setDetailAddAccountingFSId($idEntity, $strNation);
	*/
	protected function _setDetailAddAccountingFSId($idEntity, $strNation)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $varsAccount;

		$tm = TIMESTAMP;
		$stampRegister = $tm;
		$stampUpdate = $tm;

		$strTable = 'accountingFSId' . ucwords($strNation);

		$stmt = $dbh->prepare('insert into ' . $strTable . '(stampRegister, stampUpdate, idEntity) values (?, ?, ?);');
		$stmt->execute(array($stampRegister, $stampUpdate, $idEntity));
	}

	/**
		$this->_setDetailAddAccountingFSValue($numFiscalPeriod, $idEntity, $strNation);
	*/
	protected function _setDetailAddAccountingFSValue($numFiscalPeriod, $idEntity, $strNation)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $varsAccount;

		$tm = TIMESTAMP;
		$stampRegister = $tm;
		$stampUpdate = $tm;

		$strTable = 'accountingFSValue' . ucwords($strNation);

		$stmt = $dbh->prepare('insert into ' . $strTable . '(stampRegister, stampUpdate, numFiscalPeriod, idEntity) values (?, ?, ?, ?);');
		$stmt->execute(array($stampRegister, $stampUpdate, $numFiscalPeriod, $idEntity));
	}

	/**
		$this->_setDetailAddAccountingFS($numFiscalPeriod, $idEntity, $strNation, $strLang);
	*/
	protected function _setDetailAddAccountingFS($numFiscalPeriod, $idEntity, $strNation, $strLang)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $varsAccount;

		$tm = TIMESTAMP;
		$stampRegister = $tm;
		$stampUpdate = $tm;

		$jsonJgaapAccountTitlePL = json_encode($this->_getVars(array(
			'path'    => $this->_self['varsJgaapAccountTitlePL'],
			'strLang' => $strLang,
			'strNation'  => $strNation,
		)));

		$jsonJgaapAccountTitleBS = json_encode($this->_getVars(array(
			'path'    => $this->_self['varsJgaapAccountTitleBS'],
			'strLang' => $strLang,
			'strNation'  => $strNation,
		)));

		$jsonJgaapAccountTitleCR = json_encode($this->_getVars(array(
			'path'    => $this->_self['varsJgaapAccountTitleCR'],
			'strLang' => $strLang,
			'strNation'  => $strNation,
		)));

		$jsonJgaapFSPL = json_encode($this->_getVars(array(
			'path'    => $this->_self['varsJgaapFSPL'],
			'strLang' => $strLang,
			'strNation'  => $strNation,
		)));
		$jsonJgaapFSBS = json_encode($this->_getVars(array(
			'path'    => $this->_self['varsJgaapFSBS'],
			'strLang' => $strLang,
			'strNation'  => $strNation,
		)));

		$jsonJgaapFSCR = json_encode($this->_getVars(array(
			'path'    => $this->_self['varsJgaapFSCR'],
			'strLang' => $strLang,
			'strNation'  => $strNation,
		)));

		$jsonJgaapFSCS = json_encode($this->_getVars(array(
			'path'      => $this->_self['varsJgaapFSCS'],
			'strLang'   => $strLang,
			'strNation' => $strNation,
		)));

		$strTable = 'accountingFS' . ucwords($strNation);

		$stmt = $dbh->prepare('insert into ' . $strTable . '(stampRegister, stampUpdate, numFiscalPeriod, idEntity, jsonJgaapAccountTitlePL, jsonJgaapAccountTitleBS, jsonJgaapAccountTitleCR, jsonJgaapFSPL, jsonJgaapFSBS, jsonJgaapFSCR, jsonJgaapFSCS) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);');
		$stmt->execute(array($stampRegister, $stampUpdate, $numFiscalPeriod, $idEntity, $jsonJgaapAccountTitlePL, $jsonJgaapAccountTitleBS, $jsonJgaapAccountTitleCR, $jsonJgaapFSPL, $jsonJgaapFSBS, $jsonJgaapFSCR, $jsonJgaapFSCS));
	}

	/**
		$this->_setDetailAddEntity($idEntity, $strNation)
	*/
	protected function _setDetailAddEntity($idEntity, $strNation, $numFiscalPeriod)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$strTable = 'accountingEntity' . ucwords($strNation);

		$stmt = $dbh->prepare('insert into ' . $strTable . '(idEntity, numFiscalPeriod) values (?, ?);');
		$stmt->execute(array($idEntity, $numFiscalPeriod));
	}

	/**
		$this->_setDetailAddAccountingFixedAssets($numFiscalPeriod, $idEntity, $strNation, $strLang);
	*/
	protected function _setDetailAddAccountingFixedAssets($numFiscalPeriod, $idEntity, $strNation, $strLang)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $varsAccount;

		$stampRegister = TIMESTAMP;
		$stampUpdate = TIMESTAMP;

		$jsonAccountTitle = '';

		$strTable = 'accountingFixedAssets' . ucwords($strNation);

		$stmt = $dbh->prepare('insert into ' . $strTable . '(stampRegister, stampUpdate, numFiscalPeriod, idEntity, jsonAccountTitle) values (?, ?, ?, ?, ?);');
		$stmt->execute(array($stampRegister, $stampUpdate, $numFiscalPeriod, $idEntity, $jsonAccountTitle));
	}

	/**
	 *
	 */
	protected function _iniDetailEdit()
	{
		global $classDb;
		global $classEscape;
		global $varsPluginAccountingEntity;
		global $classPluginAccountingInit;
		global $varsRequest;

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

		$this->_checkStrTitle(array(
			'strTitle' => $arrValue['arr']['strTitle'],
			'idTarget' => $varsRequest['query']['jsonValue']['idTarget'],
		));

		$tm = TIMESTAMP;
		$strTitle = $arrValue['arr']['strTitle'];
		$id = $varsRequest['query']['jsonValue']['idTarget'];
		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrValue['arr']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));

		if (!$varsPluginAccountingEntity[$id]) {
			$this->_sendOldError();
		}

		$arrDbColumn = array('strTitle', 'arrSpaceStrTag');
		$arrDbValue = array($strTitle, $arrSpaceStrTag);


		try {
			$dbh->beginTransaction();

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingEntity',
				'arrColumn' => $arrDbColumn,
				'arrWhere'  => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'id',
						'flagCondition' => 'eq',
						'value'         => $id,
					),
				),
				'arrValue'  => $arrDbValue,
			));

			$this->_updateDbPreferenceStamp(array('strColumn' => 'entity'));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$classPluginAccountingInit->updateInitEntity();
		$classPluginAccountingInit->updateInitAccountsEntity();
		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$this->_iniSearchDetail();
	}

	/**
		$this->_updateVarsEdit(array(
			'vars' => array(),
		));
	 */
	protected function _updateVarsEdit($arr)
	{
		$array = &$arr['vars'];
		$arrayNew = array();
		foreach ($array as $key => $value) {
			if ($value['id'] == 'StrTitle' || $value['id'] == 'ArrSpaceStrTag') {
				$arrayNew[] = $value;
			}
		}

		return $arrayNew;
	}


	/**
		$this->_checkStrTitle(array(
			'strTitle' => '',
			'idTarget' => 0,
		));
	 */
	protected function _checkStrTitle($arr)
	{
		global $varsPluginAccountingEntity;

		$array = &$varsPluginAccountingEntity;
		foreach ($array as $key => $value) {
			$flag = 0;
			if ($arr['idTarget']) {
				if ($value['strTitle'] == $arr['strTitle'] && $arr['idTarget'] != $value['id']) {
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



}
