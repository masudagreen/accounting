<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_BanksEditor extends Code_Else_Plugin_Accounting_Jpn_Banks
{
	protected $_childSelf = array(
		'pathTplJs'   => 'else/plugin/accounting/js/jpn/banksEditor.js',
		'pathVarsJs'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/banksEditor.php',
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
	 *
	 */
	protected function _iniDetailAdd()
	{
		global $classDb;
		global $classEscape;
		$dbh = $classDb->getHandle();

		global $varsRequest;

		if (!$this->_checkCurrent()) {
			$this->_sendOldError();
		}

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllInsert'] || $varsAuthority['flagMyInsert'])) {
			$this->_sendOldError();
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

		$varsTarget = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget
		));

		$stampBook = $this->_getDbLogStampBook(array(
			'arrValue' => $arrValue,
			'vars'     => $vars,
		));
		$arrValue['arr']['stampBook'] = $stampBook;

		$arrValue = $this->_checkValueDetail(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'arrValue' => $arrValue,
		));

		try {
			$dbh->beginTransaction();

			$this->_setDbLog(array(
				'vars'     => $vars,
				'varsItem' => $varsItem,
				'arrValue' => $arrValue,
			));

			$this->_updateDbPreferenceStamp(array('strColumn' => 'logBanks'));
			$this->_updateDbPreferenceStamp(array('strColumn' => 'banks'));

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

	 */
	protected function _checkValueDetail($arr)
	{
		//stamp
		$data = $this->_getNumFiscalTermStamp(array(
			'varsEntityNation' => $arr['varsItem']['varsEntityNation']
		));

		$stampMin = $data['stampMin'];
		$stampMax = $data['stampMax'];
		$stampBook = $arr['arrValue']['arr']['stampBook'];

		if (!($stampMin <= $stampBook && $stampBook <= $stampMax)) {
			$this->_sendOldError();
		}

		if ($arr['arrValue']['arr']['flagIn']) {
			$arr['arrValue']['arr']['numValueOut'] = 0;
		} else {
			$arr['arrValue']['arr']['numValueIn'] = 0;
		}

		return $arr['arrValue'];
	}

	/**

	 */
	protected function _setDbLog($arr)
	{
		global $classEscape;
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsAccount;
		global $varsPluginAccountingAccount;

		$classCalcBanks = $this->_getClassCalc(array('flagType' => 'Banks'));
		$varsLogBanks = $classCalcBanks->allot(array(
			'flagStatus'      => 'add',
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'arrValue'        => $arr['arrValue']['arr'],
			'idAccount'       => $varsAccount['id'],
		));

	}

	/**
	 *
	 */
	protected function _getDbLogStampBook($arr)
	{
		global $varsAccount;

		$arrValue = $arr['arrValue'];

		if (!$arrValue['arr']['stampBook']) {
			return 0;
		}

		$numTimeZone = PLUGIN_ACCOUNTING_NUM_TIME_ZONE;
		$strTimeZone = (-1 * $numTimeZone) . 'hours';

		$strStamp = $arrValue['arr']['stampBook'];
		preg_match( "/^([0-9]{4})\/([0-9]{1,2})\/([0-9]{1,2})-([0-9]{1,2}):([0-9]{1,2})$/", $strStamp, $arrMatch);
		list($strStamp, $numYear, $numMonth, $numDate, $numHour, $numMin) = $arrMatch;

		$strTimeZone = (-1 * $varsAccount['numTimeZone']) . 'hours';
		$dateTime = new DateTime("$numYear-$numMonth-$numDate 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stampBook = $dateTime->format('U') + $numHour * 3600 + $numMin * 60;

		preg_replace("/\.?0+$/",'', $stampBook);

		return $stampBook;
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

		$flag = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done)$/", $flag)) {
			$this->_sendOldError();
		}

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'] || $varsAuthority['flagMyUpdate'])) {
			$this->_sendOldError();
		}

		$varsLog = $this->_getVarsLog(array(
			'idTarget'   => $varsRequest['query']['jsonValue']['idTarget'],
			'flagRemove' => 0,
		));
		if (!$varsLog) {
			$this->_sendOldError();

		} else {
			if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'])) {
				if ($varsAuthority['flagMyUpdate']) {
					if ($varsLog['idAccount'] != $varsAccount['id']) {
						$this->_sendOldError();
					}
				}
			}
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

		$varsTarget = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget
		));

		$stampBook = $this->_getDbLogStampBook(array(
			'arrValue' => $arrValue,
			'vars'     => $vars,
		));

		$arrValue['arr']['stampBook'] = $stampBook;

		$arrValue = $this->_checkValueDetail(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'arrValue' => $arrValue,
		));

		try {
			$dbh->beginTransaction();

			$this->_updateDbLog(array(
				'vars'     => $vars,
				'varsItem' => $varsItem,
				'arrValue' => $arrValue,
				'varsLog'  => $varsLog,
			));

			$this->_updateDbPreferenceStamp(array('strColumn' => 'logBanks'));

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

	 */
	protected function _updateDbLog($arr)
	{
		global $classEscape;
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsAccount;
		global $varsRequest;
		global $varsPluginAccountingAccount;

		$arrValue = $arr['arrValue'];
		$vars = $arr['vars'];

		$stampUpdate = TIMESTAMP;
		$idLogAccount = $arrValue['arr']['idLogAccount'];
		$strTitle = $arrValue['arr']['strTitle'];
		$flagIn = $arrValue['arr']['flagIn'];
		$numValueIn = $arrValue['arr']['numValueIn'];
		$numValueOut = $arrValue['arr']['numValueOut'];
		$numBalance = $arrValue['arr']['numBalance'];
		$stampBook = $arrValue['arr']['stampBook'];
		$flagCaution = 0;

		$arrVersion = $arr['varsLog']['jsonVersion'];
		$classCalcBanks = $this->_getClassCalc(array('flagType' => 'Banks'));
		$arrVersion[] = $classCalcBanks->allot(array(
			'flagStatus' => 'varsVersion',
			'arrValue'   => $arrValue['arr'],
		));

		$jsonVersion = json_encode($arrVersion);
		$this->checkTextSize(array(
			'flag' => 'errorDataMax',
			'str'  => $jsonVersion,
		));

		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrValue['arr']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));

		$arrayTemp = compact(
			'stampUpdate',
			'idLogAccount',
			'idAccount',
			'stampBook',
			'strTitle',
			'flagIn',
			'numValueIn',
			'numValueOut',
			'numBalance',
			'arrSpaceStrTag',
			'jsonVersion',
			'flagCaution'
		);

		$idLogBanks = $varsRequest['query']['jsonValue']['idTarget'];

		$arrDbColumn = array();
		$arrDbValue = array();
		foreach ($arrayTemp as $keyTemp => $valueTemp) {
			$arrDbColumn[] = $keyTemp;
			$arrDbValue[] = $valueTemp;
		}

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable' => 'accountingLogBanks',
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
					'flagType'      => 'num',
					'strColumn'     => 'idLogBanks',
					'flagCondition' => 'eq',
					'value'         => $idLogBanks,
				),
			),
			'arrValue'  => $arrDbValue,
		));
	}
}
