<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_BanksAccountEditor extends Code_Else_Plugin_Accounting_Jpn_BanksAccount
{
	protected $_childSelf = array(
		'pathTplJs'  => 'else/plugin/accounting/js/jpn/banksAccountEditor.js',
		'pathVarsJs' => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/banksAccountEditor.php',
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
		global $varsRequest;

		$pathTpl = $this->_childSelf['pathTplJs'];
		$this->_setJsEditor(array(
			'pathVars'  => $this->_childSelf['pathVarsJs'],
			'pathTpl'   => $pathTpl,
			'arrFolder' => array(),
		));
	}
	/**
	 *
	 */
	protected function _iniDetailSign()
	{
		global $varsPluginAccountingPreference;

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllInsert'] || $varsAuthority['flagAllUpdate'])) {
			$this->_sendOldFlag();
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$array = $varsItem;
		foreach ($array as $key => $value) {
			$vars['varsItem'][$key] = $value;
		}

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$vars['portal']['varsDetail']['templateDetail'] = $this->_updateVarsTemplateDetail((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$varsTarget = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget
		));

		$arrValue = $this->_checkValueDetail(array(
			'arrValue' => $arrValue,
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$flagBank = $arrValue['arr']['flagBank'];
		$varsJsonDetail = $arrValue['arr']['jsonDetail'];

		$params = array(
			'cache'      => MICROTIMESTAMP,
			'jsonData'   => json_encode($arrayNew),
			'accessCode' => ($varsPluginAccountingPreference['accessCode'])? $varsPluginAccountingPreference['accessCode'] : '',
			'version'    => NUM_VERSION,
		);

		$path = PATH_INFO_SSL . 'banks.php';
		if (FLAG_TEST) {
			$path = 'http://localhost/site/rucaro.org/banks.php';
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $path);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		$output = curl_exec($ch);
		curl_close($ch);



	}


	/**
	 *
	 */
	protected function _iniDetailAdd()
	{
		global $classDb;
		global $classEscape;
		global $classCrypte;

		global $varsRequest;
		$dbh = $classDb->getHandle();
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;
		global $varsAccounts;

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllInsert'])) {
			$this->_sendOldFlag();
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$array = $varsItem;
		foreach ($array as $key => $value) {
			$vars['varsItem'][$key] = $value;
		}

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$vars['portal']['varsDetail']['templateDetail'] = $this->_updateVarsTemplateDetail((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$varsTarget = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget
		));

		$arrValue = $this->_checkValueDetail(array(
			'arrValue' => $arrValue,
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$this->_checkStrTitle(array(
			'strTitle' => $arrValue['arr']['strTitle'],
			'idTarget' => 0,
		));

		$stampRegister = TIMESTAMP;
		$stampUpdate = TIMESTAMP;
		$strTitle = $arrValue['arr']['strTitle'];
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$flagBank = $arrValue['arr']['flagBank'];
		$stampCheck = $this->_getDbLogStampCheck(array(
			'arrValue' => $arrValue,
			'vars'     => $vars,
		));
		$jsonDetail = json_encode($arrValue['arr']['jsonDetail']);
		$blobDetail = $classCrypte->setEncrypt(array('data' => $jsonDetail));
		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrValue['arr']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));
		try {
			$dbh->beginTransaction();

			$varsIdNumber = $this->_getIdAutoIncrement(array(
				'idTarget' => 'idLogAccount'
			));
			if (!$varsIdNumber[$idEntity]) {
				$varsIdNumber[$idEntity] = 1;
			}
			$idLogAccount = $varsIdNumber[$idEntity];

			$arrayTemp = compact(
				'stampRegister',
				'stampUpdate',
				'idEntity',
				'numFiscalPeriod',
				'idLogAccount',
				'strTitle',
				'flagBank',
				'blobDetail',
				'stampCheck',
				'arrSpaceStrTag'
			);

			$arrDbColumn = array();
			$arrDbValue = array();
			foreach ($arrayTemp as $keyTemp => $valueTemp) {
				$arrDbColumn[] = $keyTemp;
				$arrDbValue[] = $valueTemp;
			}

			$id = $classDb->insertRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingLogBanksAccount',
				'arrColumn' => $arrDbColumn,
				'arrValue'  => $arrDbValue,
			));

			$varsIdNumber[$idEntity]++;
			$this->_updateIdAutoIncrement(array(
				'idTarget'   => 'idLogAccount',
				'varsTarget' => $varsIdNumber
			));

			$array = array('logBanksAccount');
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
		global $classDb;
		$dbh = $classDb->getHandle();
		global $varsPluginAccountingAccount;

		$arrWhere = array(
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
				'strColumn'     => 'strTitle',
				'flagCondition' => 'eq',
				'value'         => $arr['strTitle'],
			),
		);

		if ($arr['idTarget']) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idLogAccount',
				'flagCondition' => 'ne',
				'value'         => $arr['idTarget'],
			);
		}

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogBanksAccount',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => $arrWhere,
		));
		if ($rows['numRows']) {
			$this->sendVars(array(
				'flag'    => 'strTitle',
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}
	}

	/**
	 *
	 */
	protected function _getDbLogStampCheck($arr)
	{
		global $varsAccount;

		$arrValue = $arr['arrValue'];

		if (!$arrValue['arr']['stampCheck']) {
			return 0;
		}

		$numTimeZone = PLUGIN_ACCOUNTING_NUM_TIME_ZONE;
		$strTimeZone = (-1 * $numTimeZone) . 'hours';

		$strStamp = $arrValue['arr']['stampCheck'];
		preg_match( "/^([0-9]{4})\/([0-9]{1,2})\/([0-9]{1,2})$/", $strStamp, $arrMatch);
		list($strStamp, $numYear, $numMonth, $numDate, $numHour, $numMin) = $arrMatch;

		$strTimeZone = (-1 * $varsAccount['numTimeZone']) . 'hours';
		$dateTime = new DateTime("$numYear-$numMonth-$numDate 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stampCheck = $dateTime->format('U') + $numHour * 3600 + $numMin * 60;

		preg_replace("/\.?0+$/",'', $stampCheck);

		return $stampCheck;
	}

	/**

	 */
	protected function _checkValueDetail($arr)
	{
		global $varsPluginAccountingAccount;

		if (!$arr['varsItem']['varsBanksList'][$arr['arrValue']['arr']['flagBank']]) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}

		return $arr['arrValue'];
	}


	/**
	 *
	 */
	protected function _iniDetailEdit()
	{
		global $classDb;
		global $classEscape;
		global $classCrypte;

		global $varsRequest;
		$dbh = $classDb->getHandle();
		global $varsPluginAccountingAccount;

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'])) {
			$this->_sendOldFlag();
		}

		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$array = $varsItem;
		foreach ($array as $key => $value) {
			$vars['varsItem'][$key] = $value;
		}

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$vars['portal']['varsDetail']['templateDetail'] = $this->_updateVarsTemplateDetail((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$varsTarget = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget,
		));

		$arrValue = $this->_checkValueDetail(array(
			'arrValue' => $arrValue,
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$varsTarget = $this->_getVarsTarget(array(
			'idTarget'        => $idTarget,
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));
		if (!$varsTarget) {
			$this->_sendOldError();
		}

		$this->_checkStrTitle(array(
			'strTitle' => $arrValue['arr']['strTitle'],
			'idTarget' => $idTarget,
		));

		$strTitle = $arrValue['arr']['strTitle'];
		$flagBank = $arrValue['arr']['flagBank'];
		$stampCheck = $this->_getDbLogStampCheck(array(
			'arrValue' => $arrValue,
			'vars'     => $vars,
		));
		$jsonDetail = json_encode($arrValue['arr']['jsonDetail']);
		$blobDetail = $classCrypte->setEncrypt(array('data' => $jsonDetail));
		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrValue['arr']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));

		$arrayTemp = compact(
			'strTitle',
			'flagBank',
			'blobDetail',
			'stampCheck',
			'arrSpaceStrTag'
		);
		$arrDbColumn = array();
		$arrDbValue = array();
		foreach ($arrayTemp as $keyTemp => $valueTemp) {
			$arrDbColumn[] = $keyTemp;
			$arrDbValue[] = $valueTemp;
		}

		try {
			$dbh->beginTransaction();

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingLogBanksAccount',
				'arrColumn' => $arrDbColumn,
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
						'strColumn'     => 'idLogAccount',
						'flagCondition' => 'eq',
						'value'         => $idTarget,
					),
				),
				'arrValue'  => $arrDbValue,
			));

			$this->_updateDbPreferenceStamp(array('strColumn' => 'logBanksAccount'));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$this->_iniSearchDetail();
	}


	/**
	 *
	 */
	protected function _getVarsTarget($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsPluginAccountingAccount;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogBanksAccount',
			'arrLimit' => array(
				'numStart' => 0, 'numEnd' => 1,
			),
			'arrOrder' => array(),
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
					'strColumn'     => 'idLogAccount',
					'flagCondition' => 'eq',
					'value'         => $arr['idTarget'],
				),
			),
		));

		$data = $rows['arrRows'][0];

		if (!$data) {
			$data = array();
		}

		return $data;
	}
}
