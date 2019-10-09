<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_FilePreference extends Code_Else_Plugin_Accounting_Accounting
{
	protected $_extSelf = array(
		'idPreference' => 'fileWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/filePreference.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/filePreference.php',
		'datPath'      => 'back/dat/file/accounting/',
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
			'flagAllUse'    => 0,
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
			$this->_sendOld();
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
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;
		global $varsAccount;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsAuthority = $this->_getVarsAuthority(array());

		if ($varsAuthority == 'admin') {
			return $vars;
		}

		$array = array('Admin');
		foreach ($array as $key => $value) {
			$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_removeVarsTree(array(
				'vars'     => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
				'idTarget' => $value,
			));
		}

		return $vars;
	}

	/**
		(array(
			'vars'     => $vars,
		))
	 */
	protected function _getVarsItem($arr)
	{
		global $varsPluginAccountingAccount;

		$varsPreference = $this->_getVarsPreference(array());

		$data = array(
			'varsPreference' => $varsPreference,
		);

		return $data;
	}

	/**
		(array(
		))
	 */
	protected function _getVarsPreference($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingFile',
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
	protected function _updateVarsStrMail($arr)
	{
		global $varsPluginAccountingAccountsEntity;
		global $varsPluginAccountingAccount;
		global $classCheck;

		$varsPreference = $arr['varsItem']['varsPreference'];

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$idAccount = $varsPluginAccountingAccount['idAccount'];

		$strMail = $varsPluginAccountingAccountsEntity[$idAccount][$idEntity]['strMailFile'];

		$array = &$arr['vars']['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'Status') {
				$strMailSend = $varsPreference['strMail'];
				if (!$strMailSend) {
					$strMailSend = $value['varsTmpl']['none'];
				}
				$strSize = $classCheck->getDisc(array(
					'flagType' => 'str',
					'numByte'  =>  NUM_MAX_UPLOAD_SIZE
				));
				$strComment = $value['strComment'];
				$strComment = str_replace('<%replaceMail%>', $strMailSend, $strComment);
				$array[$key]['strComment'] = str_replace('<%replaceSize%>', $strSize, $strComment);

			} elseif ($value['id'] == 'StrMail') {
				$array[$key]['value'] = ($strMail)? $strMail : '';
			}
		}

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllInsert'] || $varsAuthority['flagMyInsert'])) {
			$arr['vars']['vars']['varsBtn'] = array();
		}
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'] || $varsAuthority['flagMyUpdate'])) {
			$arr['vars']['vars']['varsBtn'] = array();
		}

		return $arr['vars'];
	}

	/**
	 *
	 */
	protected function _updateVarsStrHost($arr)
	{
		global $classEscape;

		$varsPreference = $arr['varsItem']['varsPreference'];

		$array = &$arr['vars']['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			$str = $classEscape->toLower(array('str' => $array[$key]['id']));
			$array[$key]['value'] = (is_null($varsPreference[$str]))? '' : $varsPreference[$str];
			if ($str == 'strPassword') {
				$array[$key]['value'] = '';
			}
		}

		return $arr['vars'];
	}

	/**
	 *
	 */
	protected function _updateVarsJsonFileType($arr)
	{
		return $this->_updateVarsPreferenceJson($arr);
	}

	/**
	 *
	 */
	protected function _updateVarsJsonMail($arr)
	{
		return $this->_updateVarsPreferenceJson($arr);
	}

	/**
	 *
	 */
	protected function _updateVarsPreferenceJson($arr)
	{
		global $classEscape;

		$varsPreference = $arr['varsItem']['varsPreference'];

		$array = &$arr['vars']['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			$str = $classEscape->toLower(array('str' => $array[$key]['id']));
			$arrData = (!$varsPreference[$str])? array() : $varsPreference[$str];
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
				'strTable'  => 'accountingFile',
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

			$this->_updateDbPreferenceStamp(array('strColumn' => 'file'));

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

		$varsAuthority = $this->_getVarsAuthority(array());
		if ($varsAuthority != 'admin') {
			$this->_sendOld();
		}

		$jsonMail = $this->_checkJson(array(
			'json' => $arr['arrValue']['arr']['jsonMail']
		));
		$jsonMailHost = $this->_checkJson(array(
			'json' => $arr['arrValue']['arr']['jsonMailHost']
		));

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingFile',
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
	protected function _updateDbJsonFileType($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$varsAuthority = $this->_getVarsAuthority(array());
		if ($varsAuthority != 'admin') {
			$this->_sendOld();
		}

		$jsonFileType = $this->_checkJson(array(
			'json' => $arr['arrValue']['arr']['jsonFileType']
		));

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingFile',
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
			'arrValue'  => array($jsonFileType),
		));

		$this->_updateDbPreferenceStamp(array('strColumn' => 'file'));
	}

	/**

	 */
	protected function _updateDbStrMail($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;
		global $classPluginAccountingInit;

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllInsert'] || $varsAuthority['flagMyInsert'])) {
			$this->_sendOld();
		}
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'] || $varsAuthority['flagMyUpdate'])) {
			$this->_sendOld();
		}

		$strMail = strtolower($arr['arrValue']['arr']['strMail']);

		$flag = $this->_checkStrMail(array(
			'strMail'   => $strMail,
			'idAccount' => $varsPluginAccountingAccount['idAccount']
		));

		if (!$flag) {
			$varsPreference = $arr['varsItem']['varsPreference'];
			$varsMail = $varsPreference['jsonMail'];
			if ($varsMail[$strMail]) {
				$flag = 1;
			}
		}

		$varsAuthority = $this->_getVarsAuthority(array());

		if ($flag) {
			if ($varsAuthority == 'admin') {
				$this->sendValue(array(
					'flag'    => 'strMail',
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(
						'idTarget' => $arr['varsTarget']['vars']['idTarget']
					),
				));

			} else {
				$this->sendValue(array(
					'flag'    => 'strMailUser',
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(
						'idTarget' => $arr['varsTarget']['vars']['idTarget']
					),
				));
			}
		}

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingAccountEntity',
			'arrColumn' => array('strMailFile'),
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
					'strColumn'     => 'idAccount',
					'flagCondition' => 'eq',
					'value'         => $varsPluginAccountingAccount['idAccount'],
				),
			),
			'arrValue'  => array($strMail),
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
					'value'         => $varsPluginAccountingAccount['idAccount'],
				),
			),
			'arrValue'  => $arrDbValue,
		));

		$classPluginAccountingInit->updateInitAccount();
		$classPluginAccountingInit->updateInitAccounts();
		$classPluginAccountingInit->updateInitAccountsEntity();
		$this->_updateDbPreferenceStamp(array('strColumn' => 'account'));
	}

	/**
		(array(
		))
	 */
	protected function _checkStrMail($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingAccountEntity',
			'arrLimit' => array(),
			'arrOrder'  => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
				array(
					'flagType'      => '',
					'strColumn'     => 'idEntity',
					'flagCondition' => 'eq',
					'value'         => $varsPluginAccountingAccount['idEntityCurrent'],
				),
				array(
					'flagType'      => '',
					'strColumn'     => 'strMailFile',
					'flagCondition' => 'eq',
					'value'         => $arr['strMail'],
				),
				array(
					'flagType'      => '',
					'strColumn'     => 'idAccount',
					'flagCondition' => 'ne',
					'value'         => $arr['idAccount'],
				),
			),
		));

		if ($rows['numRows']) {
			return 1;
		}

		return 0;
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

		$classLogPreference = $this->_getClassNation(array(
			'strClass' => 'LogPreference'
		));

		$flag = $classLogPreference->checkMail(array(
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
		$flag = $classLogPreference->checkMail(array(
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

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingFile',
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

		$this->_updateDbPreferenceStamp(array('strColumn' => 'file'));
	}

	/**
		(array(
		))
	 */
	public function checkMail($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		if ($arr['flag'] == 'host') {
			$rows = $classDb->getSelect(array(
				'idModule' => 'accounting',
				'strTable' => 'accountingFile',
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
				'strTable' => 'accountingFile',
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
