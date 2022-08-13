<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_BanksAccount extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference' => 'banksWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/banksAccount.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/banksAccount.php',
		'varsOption'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/banks.php',
		'pathDir'      => 'back/tpl/vars/else/plugin/accounting/ja/dat/jpn/banks/',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;
		global $classCheck;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'select',
			'idTarget'      => $this->_extSelf['idPreference'],
		));
		if (!$flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}

		$this->_checkEntity();

		if ($varsRequest['query']['child']) {
			$strChild = ucwords($varsRequest['query']['child']);
			if ($strChild == 'EditorTemp') {
				$strChild = 'Editor';
			}
			$strExt = ucwords($varsRequest['query']['ext']);
			$str = $strExt . $strChild;

			$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/jpn/' . $str . ".php";
			if (!file_exists($path)) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
				}
				exit;
			}
			require_once($path);
			$strClass = 'Code_Else_Plugin_Accounting_Jpn_' . $str;
			$classCall = new $strClass;
			$classCall->run();

		} else {
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
		}
		exit;
	}

	/**
		$insCurrent->getDBAuthority(array(
			'flagSqlType' => $arr['flagSqlType'],ex) Select,Update,Delete,Output
			'arrData'     => ($arr['arrData']),ex) flag
		));
		return $array = array(
			'strSql'   => '',
			'arrValue' => array(),
		);
		or
		return 0
	 */
	public function getDBAuthority($arr)
	{
		global $classCheck;
		global $varsPluginAccountingAccount;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];

		$flagAuthority = $classCheck->checkModuleAuthority(array(
			'idModule' => 'accounting',
		));

		if ($flagAuthority) {
			$array = array(
				'strSql'   => 'idEntity = ? && numFiscalPeriod = ? ',
				'arrValue' => array($idEntity, $numFiscalPeriod),
			);
			return $array;
		}
		return 0;

	}


	/**
	 *
	 */
	protected function _iniJs()
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$this->_setJs(array(
			'pathVars'        => $this->_extSelf['pathVarsJs'],
			'pathTpl'         => $this->_extSelf['pathTplJs'],
			'arrFolder' => array(),
			'arrSearch'       => array(
				'idModule'  => 'accounting',
				'numLotNow' => 0,
				'strTable'  => 'accountingLogBanksAccount',
				'arrJoin'   => array(),
				'arrOrder'  => array(
					'strColumn' => 'id',
					'flagDesc'  => 1,
				),
				'insCurrent' => $this,
				'arrWhere'  => array(
				),
			),
		));

	}


	/**
		$this->_setJs(array(
			'pathVars'        => '',
			'pathTpl'         => '',
			'arrFolder'       => array(),
			'strTableSearch'  => '',
			'strColumnSearch' => '',
			'arrSearch'       => array(
				'idModule' => '',
				'numLotNow' => 0,
				'strTable'  => '',
				'arrOrder'  => array(),
				'arrWhere'  => array(),
			),
		));
	 */
	protected function _setJs($arr)
	{
		global $classSmarty;

		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
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

		$rows = $this->getSearch($arr['arrSearch']);

		$vars = $this->_updateSearch(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'rows'     => $rows,
		));

		$vars['portal']['varsNavi'] = array();

		$json = json_encode($vars);
		$classSmarty->assign('varsLoad', $json);

		$classSmarty->assign('numNews', $this->getNumNews());
		$contents = $classSmarty->fetch($arr['pathTpl']);

		$this->sendJs(array(
			'data' => $contents,
		));
	}

	/**
		(array(
			'vars' => $vars,
		))
	 */
	protected function _getVarsItem($arr)
	{
		global $varsPluginAccountingAccount;

		$varsOption = $this->getVars(array(
			'path' => $this->_extSelf['varsOption'],
		));

		$varsBanksList = array();
		$array = scandir($this->_extSelf['pathDir']);
		foreach ($array as $key => $value) {
			if ( preg_match( "/^\.{1,2}$/", $value)) {
				continue;
			}
			$path = $this->_extSelf['pathDir'] . $value;
			preg_match("/^(.*?)\.php$/", $value, $arrMatch);
			list($str, $id) = $arrMatch;
			$varsBanksList[$id] = $this->getVars(array(
				'path' => $path,
			));
		}

		$array = $this->_getLog(array());
		$varsBanksAccountList = array(
			'arrSelectTag' => array(),
			'arrStrTitle'  => array(),
		);
		$varsCheck = array();
		foreach ($array as $key => $value) {

			if (!$varsCheck[$value['flagBank']]) {
				$varsBanksAccountList['arrSelectTag'][] = array(
					'strTitle' => $varsBanksList[$value['flagBank']]['strTitle'],
					'value'    => $value['flagBank'],
				);
				$varsCheck[$value['flagBank']] = 1;
			}
			$varsBanksAccountList['arrStrTitle'][$value['idLogAccount']] = $value;
		}

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$data = array(
			'varsOption'           => $varsOption,
			'varsBanksList'        => $varsBanksList,
			'varsBanksAccountList' => $varsBanksAccountList,
			'varsEntityNation'     => $varsEntityNation,
		);

		return $data;

	}

	/**
		(array(
			'idTarget' => '',
		))
	 */
	protected function _checkVarsLogBanks($arr)
	{
		global $classDb;
		global $varsRequest;

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
				'flagType'      => 'num',
				'strColumn'     => 'idLogAccount',
				'flagCondition' => 'eq',
				'value'         => $arr['idTarget'],
			),
		);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogBanks',
			'arrLimit' => array(
				'numStart' => 0, 'numEnd' => 1,
			),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => $arrWhere,
		));

		return ($rows['numRows'])? 1 : 0;
	}

	/**
		(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		))
	 */
	protected function _updateVars($arr)
	{
		$arr['vars']['portal']['varsNavi']['search']['varsDetail']['varsSearchItem']['templateDetail']['restOption']['flagBank']
		 = $arr['varsItem']['varsBanksAccountList']['arrSelectTag'];

		$arr['vars']['portal']['varsNavi']['search']['varsDetail']['varsSearchItem']['templateDetail']['firstOption']
		 = $this->_updateVarsTemplateNavi(array(
			'vars'     => $arr['vars'],
			'varsItem' => $arr['varsItem'],
		));

		return $arr['vars'];
	}

	/**
		(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		))
	 */
	protected function _updateVarsTemplateNavi($arr)
	{
		global $classEscape;

		$arrayNew = array();
		$array = $arr['vars']['portal']['varsNavi']['search']['varsDetail']['varsSearchItem']['templateDetail']['firstOption'];
		foreach ($array as $key => $value) {
			list($dummy, $str) = preg_split("/-/", $value['value']);
			if ($str == 'flagBank') {
				if (!count($arr['varsItem']['varsBanksAccountList']['arrSelectTag'])) {
					continue;
				}
			}
			$arrayNew[] = $value;
		}

		return $arrayNew;
	}


	/**
		(array(
			'vars'             => $vars,
			'varsItem'         => $arr['varsItem'],
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _updateVarsTemplateDetail($arr)
	{
		$arrayNew = array();
		$array = &$arr['vars']['portal']['varsDetail']['templateDetail'];
		foreach ($array as $key => $value) {
			$method = '_updateVarsTemplateDetail' . $value['id'];
			if (method_exists($this, $method)) {
				$value = $this->$method(array(
					'value'    => $value,
					'vars'     => $arr['vars'],
					'varsItem' => $arr['varsItem'],
				));
			}
			$arrayNew[] = $value;
		}

		return $arrayNew;
	}

	/**
		(array(
			'value'    => $value,
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		))
	 */
	protected function _updateVarsTemplateDetailFlagBank($arr)
	{
		$arr['value']['arrayOption'] = $arr['varsItem']['varsOption']['arrSelectTag'];

		return $arr['value'];
	}

	/**
		(array(
			'value'    => $value,
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		))
	 */
	protected function _updateVarsTemplateDetailStampCheck($arr)
	{
		global $classTime;

		$data = $this->_getNumFiscalTermStamp(array(
			'varsEntityNation' => $arr['varsItem']['varsEntityNation']
		));

		$stampMin = $data['stampMin'];
		$strMin = $classTime->getDisplay(array(
			'flagType' => 'year/date',
			'stamp'    => $stampMin,
		));

		$stampMax = $data['stampMax'];
		$strMax = $classTime->getDisplay(array(
			'flagType' => 'year/date',
			'stamp'    => $stampMax,
		));
		$stampMain = TIMESTAMP;
		if ($stampMin > $stampMain) {
			$stampMain = $stampMin;
		}

		$arr['value']['strExplain'] = str_replace('<%stampMin%>', $strMin, $arr['value']['strExplain']);
		$arr['value']['strExplain'] = str_replace('<%stampMax%>', $strMax, $arr['value']['strExplain']);
		$arr['value']['varsFormCalender']['varsStatus']['stampMin'] = $stampMin * 1000;
		$arr['value']['varsFormCalender']['varsStatus']['stampMain'] = TIMESTAMP * 1000;
		$arr['value']['varsFormCalender']['varsStatus']['stampMax'] = $data['stampMaxLimit'] * 1000;

		return $arr['value'];
	}

	/**
	 *
	 */
	protected function _getNumFiscalTermStamp($arr)
	{
		$numTimeZone = PLUGIN_ACCOUNTING_NUM_TIME_ZONE;

		$varsEntityNation = $arr['varsEntityNation'];
		$numFiscalBeginningYear = $varsEntityNation['numFiscalBeginningYear'];
		$numCurrentYear = $numFiscalBeginningYear;

		$strTimeZone = (-1 * $numTimeZone) . 'hours';
		$numYear = $numCurrentYear;
		$numMonth = $varsEntityNation['numFiscalBeginningMonth'];
		$dateTime = new DateTime("$numYear-$numMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stampMin = $dateTime->format('U');
		$numEndMonth = $varsEntityNation['numFiscalBeginningMonth'] + $varsEntityNation['numFiscalTermMonth'];
		if ($numEndMonth > 12) {
			$numCurrentYear++;
			$numEndMonth -= 12;
		}

		$numYear = $numCurrentYear;
		$numMonth = $numEndMonth;
		$dateTime = new DateTime("$numYear-$numMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stampMax = $dateTime->format('U') - 1;

		$numEndMonth = 1;
		$numAppLimit = 100;
		$numYear = $numCurrentYear + $numAppLimit;
		$numMonth = $numEndMonth;
		$dateTime = new DateTime("$numYear-$numMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stampMaxLimit = $dateTime->format('U') - 1;

		$data = array(
			'stampMin'      => $stampMin,
			'stampMax'      => $stampMax,
			'stampMaxLimit' => $stampMaxLimit,
		);

		return $data;

	}

	/**
		(array(
			'idTarget' => ''
		))
	 */
	protected function _getNumBalance($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogBanks',
			'arrLimit' => array(
				'numStart' => 0, 'numEnd' => 1,
			),
			'arrOrder'  => array(
				'strColumn' => 'stampBook',
				'flagDesc'  => 1,
			),
			'flagAnd'  => 1,
			'arrWhere' => array(
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
					'value'         =>  $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				),
				array(
					'flagType'      => '',
					'strColumn'     => 'idLogAccount',
					'flagCondition' => 'eq',
					'value'         => $arr['idTarget'],
				),
			),
		));

		if (is_null($rows['arrRows'][0]['numBalance'])) {
			return '';
		}

		return $rows['arrRows'][0]['numBalance'];
	}

	/**
	 *
	 */
	protected function _updateSearch($arr)
	{
		global $classHtml;
		global $classEscape;

		global $varsAccount;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccountsEntity;
		global $varsPluginAccountingAuthority;

		global $varsPluginAccountingAccount;

		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$idEntity  = $varsPluginAccountingAccount['idEntityCurrent'];
		$strCheckStamp = 'accountingLogBanksAccount_' . $idEntity . '_' . $numFiscalPeriod;

		$vars = $arr['vars'];
		$rows = $arr['rows'];

		$flagCurrent = 1;
		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done)$/", $flagCurrentFlagNow)) {
			$flagCurrent = 0;
		}

		$classCalcLog = $this->_getClassCalc(array('flagType' => 'Log'));

		$varsAuthority = $this->_getVarsAuthority(array());
		$dataAuthority = array(
			'flagInsert' => ($varsAuthority == 'admin' || $varsAuthority['flagAllInsert'])? 1 : 0,
			'flagUpdate' => ($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'])? 1 : 0,
			'flagDelete' => ($varsAuthority == 'admin' || $varsAuthority['flagAllDelete'])? 1 : 0,
		);

		$flagAddUse = 0;
		if ($varsAuthority == 'admin' || $varsAuthority['flagAllInsert']) {
			$flagAddUse = 1;
		}
		$vars['portal']['varsDetail']['varsStart']['varsEdit']['flagAddUse'] = $flagAddUse;

		$numLine = 0;
		$array = $rows['arrRows'];
		$arrayNew = array();
		$num = 0;
		$flag = 0;
		foreach ($array as $key => $value) {
			$varsTmpl = $vars['portal']['varsList']['templateDetail'];
			$varsTmpl['id'] = $value['idLogAccount'];
			$varsTmpl['vars']['idTarget'] = $value['idLogAccount'];
			$varsTmpl['numSort'] = (int) $key;

			if ($varsAccount['jsonStampCheck'][$strCheckStamp] < $value['stampRegister']) {
				$flag = 1;

			} else {
				$varsTmpl['strClassLoad'] = '';

			}

			$varsTmpl['strTitle'] = $value['strTitle'];

			$varsTmpl['stampRegister'] = $value['stampRegister'];
			$varsTmpl['stampUpdate'] = $value['stampUpdate'];
			$varsTmpl['stampCheck'] = $value['stampCheck'];
			$varsTmpl['arrSpaceStrTag'] = preg_replace('/^ /', '', $value['arrSpaceStrTag']);
			$varsTmpl['varsAuthority'] = $dataAuthority;
			$varsTmpl['jsonDetail'] = $this->_getVarsJsonDetail(array(
				'value'    => $value,
				'varsItem' => $arr['varsItem']
			));

			if (!($varsAuthority == 'admin' || $varsAuthority['flagAllDelete'])) {
				$varsTmpl['flagDefault'] = 1;
			}

			$flagLogBanks = $this->_checkVarsLogBanks(array(
				'idTarget' => $varsTmpl['id'],
			));

			$varsTmpl['flagBtnDelete'] = 0;
			$varsTmpl['flagBtnWrite'] = 0;
			$varsTmpl['flagBtnAdd'] = 0;
			$varsTmpl['flagBtnEdit'] = 0;
			$varsTmpl['flagCheckboxUse'] = 0;
			$varsTmpl['flagAdmin'] = ($varsAuthority == 'admin')? 1 : 0;
			$varsTmpl['flagCurrent'] = $flagCurrent;
			if ($flagCurrent) {
				if ($varsAuthority == 'admin') {
					$varsTmpl['flagBtnDelete'] = 1;
					$varsTmpl['flagBtnEdit'] = 1;
					$varsTmpl['flagBtnAdd'] = 1;

				} else {
					if ($varsAuthority['flagAllDelete']) {
						$varsTmpl['flagBtnDelete'] = 1;
					}

					if ($varsAuthority['flagAllUpdate']) {
						$varsTmpl['flagBtnEdit'] = 1;
					}

					if ($varsAuthority['flagAllInsert']) {
						$varsTmpl['flagBtnAdd'] = 1;
					}
				}

				if ($flagLogBanks) {
					$varsTmpl['flagBtnDelete'] = 0;
				}

				if ($varsTmpl['flagBtnDelete']) {
					$varsTmpl['flagCheckboxUse'] = 1;
				}
			}

			$numBalance = $this->_getNumBalance(array(
				'idTarget' => $varsTmpl['id'],
			));

			$varsTmpl['varsColumnDetail']['id'] = $varsTmpl['id'];
			$varsTmpl['varsColumnDetail']['strTitle'] = $varsTmpl['strTitle'];
			$varsTmpl['varsColumnDetail']['stampRegister'] = $value['stampRegister'];
			$varsTmpl['varsColumnDetail']['stampUpdate'] = $value['stampUpdate'];
			$varsTmpl['varsColumnDetail']['stampCheck'] = $value['stampCheck'];
			$varsTmpl['varsColumnDetail']['numBalance'] = number_format($numBalance);
			$numDateCheck = $value['numDateCheck'] . $arr['vars']['varsItem']['strDate'];
			$varsTmpl['varsColumnDetail']['numDateCheck'] = ($value['numDateCheck'])? $numDateCheck : '-';
			$varsTmpl['varsColumnDetail']['flagBank'] = $arr['varsItem']['varsBanksList'][$value['flagBank']]['strTitle'];
			$varsTmpl['vars']['flagBank'] = $value['flagBank'];
			$varsTmpl['vars']['id'] = $varsTmpl['id'];
			$varsTmpl['vars']['numBalance'] = $numBalance;
			$varsTmpl['vars']['numDateCheck'] = $value['numDateCheck'];
			$varsTmpl['vars']['stampCheck'] = $value['stampCheck'];
			$varsTmpl['vars']['strTitle'] = $value['strTitle'];
			$varsTmpl['vars']['arrSpaceStrTag'] = $classEscape->splitSpaceArrayData(array('data' => $varsTmpl['arrSpaceStrTag']));
			$varsTmpl['varsScheduleDetail']['stamp'] = $value['stampRegister'];

			$arrayNew[] = $varsTmpl;
		}
		$vars['portal']['varsList']['varsPage']['varsStatus']['numRows'] = $rows['numRows'];
		$vars['portal']['varsList']['varsDetail'] = $arrayNew;

		if (!$arr['flagVars']) {
			$varsTemp = $classHtml->allot(array(
				'strClass'    => 'Table',
				'flagStatus'  => 'Html',
				'numTimeZone' => $varsAccount['numTimeZone'],
				'varsDetail'  => $arrayNew,
				'varsColumn'  => $vars['portal']['varsList']['table']['varsDetail']['varsColumn'],
				'varsStatus'  => $vars['portal']['varsList']['table']['varsDetail']['varsStatus'],
			));
			$vars['portal']['varsList']['varsHtml'] = $varsTemp['strHtml'];
			if ($flag) {
				$this->_setDbStampCheck(array(
					'strColumnAccount'    => $strCheckStamp,
					'strColumnPreference' => 'accounts',
				));
			}
		}

		return $vars;
	}

	/**
	 *
	 */
	protected function _getVarsJsonDetail($arr)
	{
		global $classCrypte;

		if (!$arr['value']['blobDetail']) {
			return array();
		}
		$jsonDetail = $classCrypte->setDecrypt(array('data' => $arr['value']['blobDetail']));
		$varsDetail = json_decode($jsonDetail, true);

		$array = $arr['varsItem']['varsBanksList'][$arr['value']['flagBank']]['varsDetail'];
		foreach ($array as $key => $value) {
			if ($value['flagSecret']) {
				$varsDetail[$value['id']] = '';
			}
		}

		return $varsDetail;
	}

	/**
	 *
	 */
	protected function _iniNaviSearchSave()
	{
		$this->_setNaviSearchSave(array(
			'pathVars'    => $this->_extSelf['pathVarsJs'],
			'strTable'    => 'accountingAccountMemo',
			'strColumn'   => 'jsonBanksAccountNaviSearch',
			'flagEntity'  => 1,
			'flagAccount' => 1,
		));
	}

	/**
	 *
	 */
	protected function _iniNaviSearchDelete()
	{
		$this->_setNaviSearchDelete(array(
			'strTable'  => 'accountingAccountMemo',
			'strColumn' => 'jsonBanksAccountNaviSearch',
			'flagEntity'  => 1,
			'flagAccount' => 1,
		));
	}

	/**
		$this->_setNaviSearchSave(array(
			'pathVars'  => '',
			'strTable'  => '',
			'strColumn' => '',
			'flagEntity' => 0,
			'flagAccount' => 0,
		));
	 */
	protected function _setNaviSearchSave($arr)
	{
		global $varsRequest;
		global $classDb;
		$dbh = $classDb->getHandle();
		global $varsAccount;
		global $varsPluginAccountingAccount;

		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
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

		$varsJson = $this->checkValueSearch(array(
			'varsValue'  => ($varsRequest['query']['jsonValue']['vars'])? $varsRequest['query']['jsonValue']['vars'] : array(),
			'varsSearch' => $vars['portal']['varsNavi']['search'],
		));

		$strJson = json_encode($varsJson);
		$this->checkTextSize(array(
			'flag' => 'errorDataMax',
			'str'  => $strJson,
		));
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$idAccount = $varsAccount['id'];
		$arrWhere = array();
		if ($arr['flagEntity']) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idEntity',
				'flagCondition' => 'eq',
				'value'         => $idEntity,
			);
		} else {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idEntity',
				'flagCondition' => 'eq',
				'value'         => 0,
			);
		}

		if ($arr['flagAccount']) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idAccount',
				'flagCondition' => 'eq',
				'value'         => $idAccount,
			);
		}

		$arrWhere[] = array(
			'flagType'      => '',
			'strColumn'     => 'flagColumn',
			'flagCondition' => 'eq',
			'value'         => $arr['strColumn'],
		);

		try {
			$dbh->beginTransaction();

			$classDb->updateRow(array(
				'idModule' => 'accounting',
				'strTable'  => $arr['strTable'],
				'arrColumn' => array('jsonData'),
				'flagAnd'   => 1,
				'arrWhere'  => $arrWhere,
				'arrValue'  => array($strJson),
			));

			if ($arr['flagAccount']) {
				$this->updateDbAccountStamp();
				$this->_updateDbPreferenceStamp(array('strColumn' => 'accounts'));
			}

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$this->sendVars(array(
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $this->_getMemo(array(
				'strTable'    => $arr['strTable'],
				'strColumn'   => $arr['strColumn'],
				'flagEntity'  => $arr['flagEntity'],
				'flagAccount' => $arr['flagAccount'],
				'varsItem'    => $vars['varsItem'],
			)),
		));
	}

	/**
	 *
	 */
	protected function _iniNaviSearchReload()
	{
		global $varsPluginAccountingAccount;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingAccount['stampUpdate'],
				'flagSearch'  => 0,
			));
		}

		$this->_setNaviSearchReload(array(
			'pathVars'  => $this->_extSelf['pathVarsJs'],
			'strTable'  => 'accountingAccountMemo',
			'strColumn' => 'jsonBanksAccountNaviSearch',
			'flagEntity'  => 1,
			'flagAccount' => 1,
		));
	}

	/**
		$this->_setNaviSearchReload(array(
			'pathVars'  => '',
			'strTable'  => '',
			'strColumn' => '',
			'flagEntity' => 0,
			'flagAccount' => 0,
		));
	 */
	protected function _setNaviSearchReload($arr)
	{
		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
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

		$vars['portal']['varsNavi']['search']['varsDetail']['varsMyRecord']['varsFormList']['varsDetail'] = $this->_getMemo(array(
			'strTable'    => $arr['strTable'],
			'strColumn'   => $arr['strColumn'],
			'flagEntity'  => $arr['flagEntity'],
			'flagAccount' => $arr['flagAccount'],
		));

		$this->sendVars(array(
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $vars['portal']['varsNavi']['search']['varsDetail'],
		));
	}

	/**
	 *
	 */
	protected function _iniNaviSearch()
	{
		$this->_setSearch(array('flag' => 1));
	}

	/**
	 * array(
	 * 	'flag' => int
	 * )
	 */
	protected function _setSearch($arr)
	{
		global $varsRequest;
		global $varsPluginAccountingPreference;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingPreference['jsonStampUpdate']['entity'],
				'flagSearch'  => 1,
			));
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

		$this->checkSearch(array(
			'arrOrder' => $vars['portal']['varsNavi']['search']['varsDetail']['varsSearchSort'],
			'arrWhere' => $vars['portal']['varsNavi']['search']['varsDetail']['varsSearchItem'],
		));

		if (!$varsRequest['query']['jsonSearch']['ph']['arrOrder']) {
			$varsRequest['query']['jsonSearch']['ph']['arrOrder']= array(
				'strColumn' => 'id',
				'flagDesc'  => 1,
			);
		}

		$rows = $this->getSearch(array(
			'idModule'   => 'accounting',
			'numLotNow'  => $varsRequest['query']['jsonSearch']['numLotNow'],
			'strTable'   => 'accountingLogBanksAccount',
			'arrOrder'   => $varsRequest['query']['jsonSearch']['ph']['arrOrder'],
			'insCurrent' => $this,
			'arrWhere'   => $varsRequest['query']['jsonSearch']['ph']['arrWhere'],
		));

		$vars = $this->_updateSearch(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'rows'     => $rows,
		));

		$this->sendVars(array(
			'flag'    => ($arr['flag'])? $arr['flag'] : 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'numRows'    => $rows['numRows'],
				'varsDetail' => $vars['portal']['varsList']['varsDetail'],
				'varsHtml'   => $vars['portal']['varsList']['varsHtml'],
				'varsItem'   => $vars['varsItem'],
			),
		));
	}

	/**
	 *
	 */
	protected function _iniListReload()
	{
		$this->_setSearch(array('flag' => 1));
	}

	/**
	 *
	 */
	protected function _iniDetailReload()
	{
		$this->_iniSearchDetail();
	}

	/**
	 *
	 */
	protected function _iniSearchDetail()
	{
		global $varsRequest;
		global $varsPluginAccountingPreference;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingPreference['jsonStampUpdate']['banks'],
				'flagSearch'  => 1,
			));
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

		$this->checkSearch(array(
			'arrOrder' => $vars['portal']['varsNavi']['search']['varsDetail']['varsSearchSort'],
			'arrWhere' => $vars['portal']['varsNavi']['search']['varsDetail']['varsSearchItem'],
		));

		if (!$varsRequest['query']['jsonSearch']['ph']['arrOrder']) {
			$varsRequest['query']['jsonSearch']['ph']['arrOrder']= array(
				'strColumn' => 'id',
				'flagDesc'  => 1,
			);
		}

		$rows = $this->getSearch(array(
			'idModule'   => 'accounting',
			'numLotNow'  => $varsRequest['query']['jsonSearch']['numLotNow'],
			'strTable'   => 'accountingLogBanksAccount',
			'arrOrder'   => $varsRequest['query']['jsonSearch']['ph']['arrOrder'],
			'insCurrent' => $this,
			'arrWhere'   => $varsRequest['query']['jsonSearch']['ph']['arrWhere'],
		));

		$vars = $this->_updateSearch(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'rows'     => $rows,
		));

		$varsTarget = $vars;

		$rowsTarget = $this->getSearch(array(
			'idModule'  => 'accounting',
			'numLotNow' => 0,
			'strTable'  => 'accountingLogBanksAccount',
			'arrOrder'  => array(),
			'flagAnd'   => 1,
			'arrWhere'  => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idLogAccount',
					'flagCondition' => 'eq',
					'value'         => $varsRequest['query']['jsonValue']['idTarget'],
				),
			),
			'insCurrent'  => $this,
		));

		if (!$rowsTarget['numRows']) {
			$this->_sendOldError();
		}

		$varsTarget = $this->_updateSearch(array(
			'vars'     => $varsTarget,
			'rows'     => $rowsTarget,
			'varsItem' => $varsItem,
			'flagVars' => 1,
		));

		$this->sendVars(array(
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'varsDetail' => $varsTarget['portal']['varsList']['varsDetail'][0],
				'numRows'    => $rows['numRows'],
				'numLotNow'  => $varsRequest['query']['jsonSearch']['numLotNow'],
				'varsList'   => $vars['portal']['varsList']['varsDetail'],
				'varsHtml'   => $vars['portal']['varsList']['varsHtml'],
				'varsItem'   => $vars['varsItem'],
			),
		));
	}

	/**
	 *
	 */
	protected function _iniDetailDelete()
	{
		global $varsRequest;

		$this->_setDelete(array(
			'arrId' => array($varsRequest['query']['jsonValue']['idTarget']),
		));
	}

	/**
	 *
	 */
	protected function _iniListDelete()
	{
		global $varsRequest;

		$this->_setDelete(array(
			'arrId' => $varsRequest['query']['jsonValue']['vars'],
		));
	}

	/**
	 (array(

	 ))
	 */
	protected function _getVarsLog($arr)
	{
		global $classDb;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogBanksAccount',
			'arrLimit' => array(),
			'arrOrder' => array(),
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
					'flagCondition' => 'eq',
					'value'         => $arr['numFiscalPeriod'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idLogAccount',
					'flagCondition' => 'eq',
					'value'         => $arr['idLogAccount'],
				),
			),
		));

		return $rows['arrRows'];
	}

	/**
		$this->_setDelete(array(
			'arrId' => array(),
		));
	 */
	protected function _setDelete($arr)
	{
		global $classDb;
		global $classCheck;

		$dbh = $classDb->getHandle();
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllDelete'])) {
			$this->_sendOldFlag();
		}

		$array = $arr['arrId'];
		$flag = $classCheck->checkValueFormat(array(
			'flagType' => 'num',
			'flagArr'  => 1,
			'value'    => $array,
		));

		if ($flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
			}
			exit;
		}

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());

		try {
			$dbh->beginTransaction();
			foreach ($array as $key => $value) {
				$flag = $this->_checkVarsLogBanks(array(
					'idTarget' => $value,
				));
				if ($flag) {
					continue;
				}
				$classDb->deleteRow(array(
					'idModule'  => 'accounting',
					'strTable'  => 'accountingLogBanksAccount',
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
							'strColumn'     => 'numFiscalPeriod',
							'flagCondition' => 'eq',
							'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
						),
						array(
							'flagType'      => 'num',
							'strColumn'     => 'idLogAccount',
							'flagCondition' => 'eq',
							'value'         => $value,
						),
					),
				));
			}

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
		(array(
			'flagType' => 'LogCalc'
		))
	 */
	protected function _getLog($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogBanksAccount',
			'arrLimit' => array(),
			'arrOrder'  => array(
				'strColumn' => 'id',
				'flagDesc'  => 1,
			),
			'flagAnd'  => 1,
			'arrWhere' => array(
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
			),
		));

		return $rows['arrRows'];
	}
}
