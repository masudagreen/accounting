<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_LogPreference extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference' => 'logWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/logPreference.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/logPreference.php',
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

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done)$/", $flagCurrentFlagNow)) {
			$this->_sendOldFlag();
		}

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
		global $classSmarty;

		$vars = $this->_getVarsJs();

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_updateVars(array(
			'vars'     => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
			'varsItem' => $varsItem,
		));

		$json = json_encode($vars);
		$classSmarty->assign('varsLoad', $json);

		$classSmarty->assign('numNews', $this->getNumNews());
		$contents = $classSmarty->fetch($this->_extSelf['pathTplJs']);

		$this->sendJs(array(
			'data' => $contents,
		));

	}

	/**
	 *
	 */
	protected function _getVarsJs()
	{
		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		if (!$this->_getVarsFlagAdmin()) {
			$array = array('Admin');
			foreach ($array as $key => $value) {
				$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_removeVarsTree(array(
					'vars'       => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
					'idTarget'   => $value,
				));
			}
		}

		return $vars;
	}

	/**
	 *
	 */
	protected function _getVarsFlagAdmin()
	{
		global $varsAccount;

		return ($this->_checkModuleAdmin(array('idAccount' => $varsAccount['id'],)));

	}

	/**
		(array(
			'vars'     => $vars,
		))
	 */
	protected function _getVarsItem($arr)
	{
		global $varsPluginAccountingAccount;

		$varsLogPreference = $this->_getVarsLogPreference(array());

		$data = array(
			'varsLogPreference' => $varsLogPreference,
		);

		return $data;
	}

	/**
		(array(
		))
	 */
	protected function _getVarsLogPreference($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogMail' . $strNation,
			'arrLimit' => array(),
			'arrOrder'  => array(),
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

		return $rows['arrRows'][0];
	}

	/**
	 *
	 */
	protected function _updateVars($arr)
	{
		$vars = $arr['vars'];
		foreach ($vars as $key => $value) {
			$idTarget = $value['vars']['idTarget'];
			$id = $value['id'];

			if (!preg_match( "/Window$/", $idTarget) && !$value['flagFoldUse']) {
				$method = '_updateVars' . ucwords($idTarget);
				if (method_exists($this, $method)) {
					$vars[$key] = $this->$method(array(
						'vars'     => $vars[$key],
						'varsItem' => $arr['varsItem'],
					));
				}
			}

			if ($value['child']) {
				$vars[$key]['child'] = $this->_updateVars(array(
					'vars'     => $vars[$key]['child'],
					'varsItem' => $arr['varsItem'],
				));
			}
		}

		return $vars;
	}

	/**
	 *
	 */
	protected function _updateVarsStrHost($arr)
	{
		global $classEscape;

		$varsLogPreference = $arr['varsItem']['varsLogPreference'];

		$array = &$arr['vars']['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			$str = $classEscape->toLower(array('str' => $array[$key]['id']));
			$array[$key]['value'] = (is_null($varsLogPreference[$str]))? '' : $varsLogPreference[$str];
			if ($str == 'strPassword') {
				$array[$key]['value'] = '';
			}
		}

		return $arr['vars'];
	}

	/**
	 *
	 */
	protected function _updateVarsJsonMail($arr)
	{
		return $this->_updateVarsLogPreferenceJson($arr);
	}

	/**
	 *
	 */
	protected function _updateVarsLogPreferenceJson($arr)
	{
		global $classEscape;

		$varsLogPreference = $arr['varsItem']['varsLogPreference'];

		$array = &$arr['vars']['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			$str = $classEscape->toLower(array('str' => $array[$key]['id']));
			$arrData = (!$varsLogPreference[$str])? array() : $varsLogPreference[$str];
			$num = 0;
			$arrayNew = array();
			foreach ($arrData as $keyData => $valueData) {
				$varsTmpl = $array[$key]['varsFormList']['templateDetail'];
				$varsTmpl['id'] = $num;
				$varsTmpl['numSort'] = $num;
				$varsTmpl['value'] = $keyData;
				$arrayNew[$num] = $varsTmpl;
				$num++;
			}
			$array[$key]['varsFormList']['varsDetail'] = $arrayNew;
		}

		return $arr['vars'];
	}

	/**
	 *
	 */
	protected function _iniNaviReload()
	{
		global $varsPluginAccountingPreference;
		global $varsRequest;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingPreference['stampUpdate'],
				'flagSearch'  => 0,
			));
		}

		$vars = $this->_getVarsJs();

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_updateVars(array(
			'vars'     => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
			'varsItem' => $varsItem,
		));

		$this->sendValue(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
		));
	}



	/**
	 *
	 */
	protected function _iniDetailReload()
	{
		global $varsPluginAccountingPreference;
		global $varsRequest;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingPreference['stampUpdate'],
				'flagSearch'  => 0,
			));
		}

		$vars = $this->_getVarsJs();

		$varsTarget = $this->getVarsTarget(array(
			'vars' => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail']
		));
		if (!$varsTarget) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}
		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$method = '_updateVars' . ucwords($varsTarget['vars']['idTarget']);
		if (method_exists($this, $method)) {
			$varsTarget = $this->$method(array(
				'vars'     => $varsTarget,
				'varsItem' => $varsItem,
			));
		}

		$this->sendValue(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $varsTarget,
		));
	}

	/**
	 *
	 */
	protected function _iniDetailEdit()
	{
		global $classInit;
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsRequest;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];

		$vars = $this->_getVarsJs();

		if ($idTarget == 'strReset') {
			$varsRequest['query']['jsonValue']['idTarget'] = 'strHost';
			$this->_resetDb(array(
				'vars' => $vars,
			));
			return;
		}

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$varsTarget = $this->getVarsTarget(array(
			'vars' => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail']
		));

		if (!$varsTarget) {
			$this->sendValue(array(
				'flag'    => 8,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}

		$varsTarget['vars']['varsDetail'] = $this->getValue(array(
			'vars' => $varsTarget['vars']['varsDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget['vars']['varsDetail']
		));

		try {
			$dbh->beginTransaction();

			$this->_updateDb(array(
				'arrValue'   => $arrValue,
				'varsTarget' => $varsTarget,
				'varsItem'   => $varsItem,
			));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$method = '_updateVars' . ucwords($varsTarget['vars']['idTarget']);
		if (method_exists($this, $method)) {
			$varsItem = $this->_getVarsItem(array(
				'vars' => $vars,
			));
			$varsTarget = $this->$method(array(
				'vars'     => $varsTarget,
				'varsItem' => $varsItem,
			));
		}

		$this->sendValue(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $varsTarget,
		));
	}

	/**
	 *
	 */
	protected function _resetDb($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsPluginAccountingAccount;
		global $varsRequest;

		$vars = $arr['vars'];

		$strHost = '';
		$strUser = '';
		$strPassword = '';
		$numPort = '993';
		$flagSecure = 'ssl';
		$strMail = '';

		$arrayTemp = compact(
			'strHost',
			'strUser',
			'strPassword',
			'numPort',
			'flagSecure',
			'strMail'
		);

		$arrColumn = array();
		$arrValue = array();
		foreach ($arrayTemp as $keyTemp => $valueTemp) {
			$arrColumn[] = $keyTemp;
			$arrValue[] = $valueTemp;
		}

		try {
			$dbh->beginTransaction();

			$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingLogMail' . $strNation,
				'arrColumn' => $arrColumn,
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
				'arrValue'  => $arrValue,
			));

			$this->_updateDbPreferenceStamp(array('strColumn' => 'log'));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}
		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];
		$method = '_updateVars' . ucwords($idTarget);
		if (method_exists($this, $method)) {
			$varsItem = $this->_getVarsItem(array(
				'vars' => $vars,
			));
			$varsTarget = $this->getVarsTarget(array(
				'vars' => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail']
			));
			$varsTarget = $this->$method(array(
				'vars'     => $varsTarget,
				'varsItem' => $varsItem,
			));
		}

		$this->sendValue(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $varsTarget,
		));
	}

	/**
	 *
	 */
	protected function _updateDb($arr)
	{
		global $varsRequest;

		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];
		$method = '_updateDb' . ucwords($idTarget);
		if (method_exists($this, $method)) {
			$this->$method($arr);
		}
	}

	/**

	 */
	protected function _updateDbJsonMail($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$jsonMail = $this->_checkJson(array(
			'json' => $arr['arrValue']['arr']['jsonMail']
		));

		$jsonMailHost = $this->_checkJson(array(
			'json' => $arr['arrValue']['arr']['jsonMailHost']
		));

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingLogMail' . $strNation,
			'arrColumn' => $arr['arrValue']['arrColumn'],
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
			'arrValue'  => array($jsonMail, $jsonMailHost),
		));
	}

	/**

	 */
	protected function _checkJson($arr)
	{
		$json = $arr['json'];
		$array = json_decode($json, true);
		if (!$array) {
			$array = array();
		}
		$arrayCheck = array();
		foreach ($array as $key => $value) {
			$arrayCheck[strtolower($value)] = 1;
		}
		$json = json_encode($arrayCheck);

		return $json;
	}

	/**

	 */
	protected function _updateDbStrHost($arr)
	{
		global $classDb;
		global $classCrypte;

		global $varsPluginAccountingAccount;

		$strHost = $arr['arrValue']['arr']['strHost'];
		$strUser = $arr['arrValue']['arr']['strUser'];
		$strPassword = $arr['arrValue']['arr']['strPassword'];
		$numPort = $arr['arrValue']['arr']['numPort'];
		$flagSecure = $arr['arrValue']['arr']['flagSecure'];
		$strMail = strtolower($arr['arrValue']['arr']['strMail']);

		$strSecure = '';
		if ($flagSecure == 'none') {
			$strSecure = '/imap/notls';

		} elseif ($flagSecure == 'start') {
			$strSecure = '/imap/tls/novalidate-cert';

		} elseif ($flagSecure == 'ssl') {
			$strSecure = '/imap/ssl/novalidate-cert';
		}

		$strServer = '{' . $strHost . ':' . $numPort . $strSecure . '}INBOX';
		if (preg_match("/^(localhost)$/i", $strHost)) {
			$strServer = '{localhost:' . $numPort . $strSecure . '}INBOX';
		}

		$classFilePreference = $this->_getClass(array(
			'strClass'       => 'FilePreference',
			'flagAccounting' => 1
		));

		$flag = $classFilePreference->checkMail(array(
			'flag'    => 'host',
			'strHost' => $strHost,
			'strUser' => $strUser,
		));
		if ($flag) {
			$this->sendValue(array(
				'flag'    => 'double',
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(
					'idTarget' => $arr['varsTarget']['vars']['idTarget']
				),
			));
		}
		$flag = $classFilePreference->checkMail(array(
			'flag'    => 'mail',
			'strMail' => $strMail,
		));
		if ($flag) {
			$this->sendValue(array(
				'flag'    => 'strMail',
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(
					'idTarget' => $arr['varsTarget']['vars']['idTarget']
				),
			));
		}

		if (($mbox = @imap_open($strServer, $strUser, $strPassword)) == false) {
			$this->sendValue(array(
				'flag'    => 'common',
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(
					'idTarget' => $arr['varsTarget']['vars']['idTarget']
				),
			));
		}

		$strPass = $classCrypte->setEncrypt(array('data' => $strPassword));
		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingLogMail' . $strNation,
			'arrColumn' => $arr['arrValue']['arrColumn'],
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
			'arrValue'  => array($strHost, $strUser, $strPass, $numPort, $flagSecure, $strMail),
		));

		$this->_updateDbPreferenceStamp(array('strColumn' => 'log'));
	}

	/**
		(array(
		))
	 */
	public function checkMail($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		if ($arr['flag'] == 'host') {
			$rows = $classDb->getSelect(array(
				'idModule' => 'accounting',
				'strTable' => 'accountingLogMail' . $strNation,
				'arrLimit' => array(),
				'arrOrder'  => array(),
				'flagAnd'  => 1,
				'arrWhere' => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idEntity',
						'flagCondition' => 'eq',
						'value'         => $varsPluginAccountingAccount['idEntityCurrent'],
					),
					array(
						'flagType'      => '',
						'strColumn'     => 'strHost',
						'flagCondition' => 'eq',
						'value'         => $arr['strHost'],
					),
					array(
						'flagType'      => '',
						'strColumn'     => 'strUser',
						'flagCondition' => 'eq',
						'value'         => $arr['strUser'],
					),
				),
			));

		} else if ($arr['flag'] == 'mail') {
			$rows = $classDb->getSelect(array(
				'idModule' => 'accounting',
				'strTable' => 'accountingLogMail' . $strNation,
				'arrLimit' => array(),
				'arrOrder'  => array(),
				'flagAnd'  => 1,
				'arrWhere' => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idEntity',
						'flagCondition' => 'eq',
						'value'         => $varsPluginAccountingAccount['idEntityCurrent'],
					),
					array(
						'flagType'      => '',
						'strColumn'     => 'strMail',
						'flagCondition' => 'eq',
						'value'         => $arr['strMail'],
					),
				),
			));
		}

		if ($rows['numRows']) {
			return 1;
		}

		return 0;
	}



}
