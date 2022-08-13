<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Base_AccountEditor extends Code_Else_Core_Base_Account
{
	protected $_childSelf = array(
		'pathTplJs'        => 'else/core/base/js/accountEditor.js',
		'pathVarsJs'       => 'back/tpl/vars/else/core/base/<strLang>/js/accountEditor.php',
		'pathVarsMailUser' => 'back/tpl/vars/else/core/base/<strLang>/mail/accountUser.php',
		'pathTplMailUser'  => 'back/tpl/vars/else/core/base/<strLang>/mail/accountUser.tpl',
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
	protected function _iniDetailEdit()
	{
		global $classDb;
		global $classInit;
		global $classEscape;
		$dbh = $classDb->getHandle();

		global $varsAccounts;
		global $varsPreference;
		global $varsRequest;
		global $varsTerm;
		global $varsModule;

		$temp = $varsRequest['query']['jsonValue']['vars']['StrMailPc'];
		$varsRequest['query']['jsonValue']['vars']['StrMailPc'] = strtolower($temp);

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsTarget['vars']['varsDetail'] = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget['vars']['varsDetail']
		));

		$this->_checkValueDetailAccount(array(
			'flagSelf' => 0,
			'arrValue' => $arrValue,
		));

		$this->_checkValueDetailPassword($arrValue);

		if ($varsRequest['query']['jsonValue']['idTarget'] != 1) {

			//idterm exist
			if (!$varsTerm[$arrValue['arr']['idTerm']]) {
				$this->sendVars(array(
					'flag'    => 'idTerm',
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));
			}
			if (!$varsModule[$arrValue['arr']['idModule']]) {
				$this->sendVars(array(
					'flag'    => 'idModule',
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));
			}
		}

		$tm = TIMESTAMP;
		$stampRegister = $tm;
		$strCodeName = $arrValue['arr']['strCodeName'];
		$idLogin = $arrValue['arr']['idLogin'];

		$strPassword = '';
		$id = $varsRequest['query']['jsonValue']['idTarget'];
		if ($arrValue['arr']['strPassword']) {
			$strPassword = hash('sha256', $arrValue['arr']['strPassword']);

		} else {
			$strPassword = $varsAccounts[$id]['strPassword'];

		}

		$strMailPc = $arrValue['arr']['strMailPc'];
		$idTerm = $arrValue['arr']['idTerm'];
		$idModule = $arrValue['arr']['idModule'];
		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrValue['arr']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));

		if (!$varsAccounts[$id]) {
			$varsRequest['query']['jsonSearch']['flagReload'] = 0;
			$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
			$this->_setSearch(array('flag' => 40));
		}

		if ($varsRequest['query']['jsonValue']['idTarget'] == 1) {
			if (!$arrValue['arr']['strPassword']) {
				$arrDbColumn = array('strCodeName', 'idLogin', 'strPassword', 'strMailPc', 'arrSpaceStrTag');
				$arrDbValue = array($strCodeName, $idLogin, $strPassword, $strMailPc, $arrSpaceStrTag);

			} else {
				$arrDbColumn = array('strCodeName', 'idLogin', 'strPassword', 'stampUpdatePassword', 'strMailPc', 'arrSpaceStrTag');
				$arrDbValue = array($strCodeName, $idLogin, $strPassword, $tm, $strMailPc, $arrSpaceStrTag);
			}

		} else {
			$arrDbColumn = array('strCodeName', 'idLogin', 'strPassword', 'stampUpdatePassword', 'strMailPc', 'idTerm', 'idModule', 'arrSpaceStrTag');
			$arrDbValue = array($strCodeName, $idLogin, $strPassword, $tm, $strMailPc, $idTerm, $idModule, $arrSpaceStrTag);
		}

		try {
			$dbh->beginTransaction();

			$classDb->updateRow(array(
				'idModule'  => 'base',
				'strTable'  => 'baseAccount',
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

			if ($varsAccounts[$id]['idLogin'] != $idLogin) {
				$classDb->insertRow(array(
					'idModule'  => 'base',
					'strTable'  => 'baseLoginIdLogin',
					'arrColumn' => array('stampRegister', 'idLogin'),
					'arrValue'  => array($stampRegister, $idLogin),
				));
			}

			if ($varsAccounts[$id]['strCodeName'] != $strCodeName) {
				$classDb->insertRow(array(
					'idModule'  => 'base',
					'strTable'  => 'baseAccountId',
					'arrColumn' => array('id', 'strCodeName'),
					'arrValue'  => array($id, $strCodeName),
				));
			}

			if ($varsRequest['query']['jsonValue']['idTarget'] != 1
				&& $varsAccounts[$varsRequest['query']['jsonValue']['idTarget']]['idModule'] != $idModule
			) {
				$this->_loopPluginUpdate(array(
					'varsModulePast' => $varsModule[$varsAccounts[$varsRequest['query']['jsonValue']['idTarget']]['idModule']],
					'varsModuleNew'  => $varsModule[$idModule],
					'varsIdAccount'  => $varsAccounts[$varsRequest['query']['jsonValue']['idTarget']],
				));
			}


			$this->updateDbPreferenceStamp(array('strColumn' => 'account'));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}


		$classInit->updateVarsAll(array(
			'vars'     => &$varsAccounts,
			'strVars'  => 'varsAccounts',
			'strTable' => 'baseAccount',
		));

		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$this->_iniSearchDetail();
	}

	/**
		$this->_loopPluginUpdate(array(
			'idModule'    => 0,
			'varsAccount' => array(),
		));
	 */
	protected function _loopPluginUpdate($arr)
	{
		global $varsPreference;

		global $classEscape;

		$array = $varsPreference['jsonModule'];

		foreach ($array as $key => $value) {
			$strDir = $key;
			$strFile = ucwords($key);
			$path = PATH_BACK_CLASS_ELSE_PLUGIN . $strDir . '/' . $strFile . '.php';
			if (!file_exists($path)) {
				continue;
			}
			require_once($path);
			$strClass = 'Code_Else_Plugin_' . $strFile . '_' . $strFile;
			$classCall = new $strClass;
			$classCall->loop(array(
				'flagType'       => 'accountStatus',
				'flagStatus'     => 'updateModule',
				'varsModuleNew'  => $arr['varsModuleNew'],
				'varsModulePast' => $arr['varsModulePast'],
				'arrId'          => array($arr['varsIdAccount']['id']),
			));
		}
	}

	/**
		$this->_checkValueDetailPassword($arrValue);
	 */
	protected function _checkValueDetailPassword($arrValue)
	{
		global $varsAccounts;
		global $varsPreference;
		global $varsRequest;

		global $classDb;

		if ($arrValue['arr']['strPassword'] == '' && $arrValue['arr']['strPasswordConfirm'] == '') {
			return;
		}

		if ($arrValue['arr']['strPassword'] != $arrValue['arr']['strPasswordConfirm']
			||  mb_strlen($arrValue['arr']['strPassword']) < $varsPreference['numPassword']
		) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__);
			}
			exit;
		}



		$strPassword = hash('sha256', $arrValue['arr']['strPassword']);
		$rows = $classDb->getSelect(array(
			'idModule' => 'base',
			'strTable' => 'baseLoginPassword',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idAccount',
					'flagCondition' => 'eq',
					'value'         => $id,
				),
				array(
					'flagType'      => '',
					'strColumn'     => 'strPassword',
					'flagCondition' => 'eq',
					'value'         => $strPassword,
				),
			),
		));

		if ($rows['numRows']) {
			$this->sendVars(array(
				'flag'    => 'strPassword',
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));

		}


	}

	/**
		$this->_checkValueDetailAccount(array(
			'flagSelf' => int,
			'arrValue' => array(),
		));
	 */
	protected function _checkValueDetailAccount($arr)
	{
		global $varsAccounts;
		global $varsRequest;
		global $varsPreference;

		global $classDb;
		global $classInit;

		$arrValue = $arr['arrValue'];

		$array = &$varsAccounts;
		foreach ($array as $key => $value) {
			if (!$arr['flagSelf']) {
				if ($value['id'] == $varsRequest['query']['jsonValue']['idTarget']) {
					if ($value['strCodeName'] != $arrValue['arr']['strCodeName']) {
						$rows = $classDb->getSelect(array(
							'idModule' => 'base',
							'strTable' => 'baseAccountId',
							'arrLimit' => array(),
							'arrOrder' => array(),
							'arrWhere' => array(
								array(
									'flagType'      => '',
									'strColumn'     => 'strCodeName',
									'flagCondition' => 'eq',
									'value'         => $arrValue['arr']['strCodeName'],
								),
							),
						));
						if ($rows['numRows']) {
							$this->sendValue(array(
								'flag'    => 'strCodeNamePast',
								'stamp'   => $this->getStamp(),
								'numNews' => $this->getNumNews(),
								'vars'    => array(),
							));
						}
					}
					if ($value['idLogin'] != $arrValue['arr']['idLogin']) {
						$rows = $classDb->getSelect(array(
							'idModule' => 'base',
							'strTable' => 'baseLoginIdLogin',
							'arrLimit' => array(),
							'arrOrder' => array(),
							'arrWhere' => array(
								array(
									'flagType'      => '',
									'strColumn'     => 'idLogin',
									'flagCondition' => 'eq',
									'value'         => $arrValue['arr']['idLogin'],
								),
							),
						));
						if ($rows['numRows']) {
							$this->sendValue(array(
								'flag'    => 'idLoginPast',
								'stamp'   => $this->getStamp(),
								'numNews' => $this->getNumNews(),
								'vars'    => array(),
							));
						}
					}
					if ($value['strMailPc'] != $arrValue['arr']['strMailPc']) {
						$arrayAccount = &$varsAccounts;
						foreach ($arrayAccount as $keyAccount => $valueAccount) {
							if ($valueAccount['strMailPc'] == $arrValue['arr']['strMailPc']
								|| $varsPreference['strSiteMailPc'] == $arrValue['arr']['strMailPc']
							) {
								$this->sendVars(array(
									'flag'    => 'strMailPc',
									'stamp'   => $this->getStamp(),
									'numNews' => $this->getNumNews(),
									'vars'    => array(),
								));
							}
						}
					}
				}

			} else {
				if ($value['strCodeName'] == $arrValue['arr']['strCodeName']) {
					$this->sendVars(array(
						'flag'    => 'strCodeName',
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(),
					));
				}
				if ($value['idLogin'] == $arrValue['arr']['idLogin']) {
					$this->sendVars(array(
						'flag'    => 'idLogin',
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(),
					));

				}
				if ($value['strMailPc'] == $arrValue['arr']['strMailPc']
					|| $varsPreference['strSiteMailPc'] == $arrValue['arr']['strMailPc']
				) {
					$this->sendVars(array(
						'flag'    => 'strMailPc',
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(),
					));
				}
			}


		}


	}

	/**
	 *
	 */
	protected function _iniDetailAdd()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $classDisplay;
		global $classInit;
		global $classMail;
		global $classEscape;

		global $varsPreference;
		global $varsRequest;

		global $varsTerm;
		global $varsModule;

		$temp = $varsRequest['query']['jsonValue']['vars']['StrMailPc'];
		$varsRequest['query']['jsonValue']['vars']['StrMailPc'] = strtolower($temp);

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsTarget['vars']['varsDetail'] = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget['vars']['varsDetail']
		));

		$this->_checkValueDetailAccount(array(
			'flagSelf' => 1,
			'arrValue' => $arrValue,
		));

		//idterm exist
		if (!$varsTerm[$arrValue['arr']['idTerm']]) {
			$this->sendVars(array(
				'flag'    => 'idTerm',
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}
		if (!$varsModule[$arrValue['arr']['idModule']]) {
			$this->sendVars(array(
				'flag'    => 'idModule',
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}

		$tm = TIMESTAMP;
		$stampRegister = $tm;
		$stampUpdate = $tm;
		$strCodeName = $arrValue['arr']['strCodeName'];
		$idLogin = $arrValue['arr']['idLogin'];
		$strPassword = $classDisplay->getPassword(array(
			'numMark'  => 1,
			'numNum'   => 1,
			'numBig'   => 1,
			'numSmall' => abs($varsPreference['numPassword '] - 3),
		));
		$strPassword = hash('sha256', $strPassword);
		$stampUpdatePassword = $tm;
		$strMailPc = $arrValue['arr']['strMailPc'];
		$numTimeZone = NUM_SYSTEM_TIME_ZONE;
		$strLang = STR_SYSTEM_LANG;
		$strHoliday = STR_SYSTEM_HOLIDAY;
		$idTerm = $arrValue['arr']['idTerm'];
		$idModule = $arrValue['arr']['idModule'];
		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrValue['arr']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));

		$str = MICROTIMESTAMP . $classDisplay->getPassword(array(
			'numMark'  => 5,
			'numNum'   => 5,
			'numBig'   => 5,
			'numSmall' => 5,
		));
		$session = hash('sha256', $str);
        $pathConfirm = $varsPreference['strTopUrl'] . 'confirm.php?type=publish&id=' . $session;
		$numLimit = round(NUM_SESSION /60/60/24*7);
		$strName = $varsPreference['strSiteName'];
		$strUrl = $varsPreference['strSiteUrl'];

		try {
			$dbh->beginTransaction();

			//account
			$classDb->insertRow(array(
				'idModule'  => 'base',
				'strTable'  => 'baseAccount',
				'arrColumn' => array('stampRegister', 'stampUpdate', 'strCodeName', 'idLogin', 'strPassword', 'stampUpdatePassword', 'strMailPc', 'numTimeZone', 'strLang', 'strHoliday', 'idTerm', 'idModule', 'arrSpaceStrTag'),
				'arrValue'  => array($stampRegister, $stampUpdate, $strCodeName, $idLogin, $strPassword, $stampUpdatePassword, $strMailPc, $numTimeZone, $strLang, $strHoliday, $idTerm, $idModule, $arrSpaceStrTag),
			));

			$rows = $classDb->getSelect(array(
				'idModule' => 'base',
				'strTable' => 'baseAccount',
				'arrLimit' => array(),
				'arrOrder' => array(),
				'arrWhere' => array(
					array(
						'flagType'      => '',
						'strColumn'     => 'idLogin',
						'flagCondition' => 'eq',
						'value'         => $idLogin,
					),
				),
			));

			$varsAccount = $rows['arrRows'][0];
			$idAccount = $varsAccount['id'];

			//memo
			$array = array(
				'jsonTermNaviSearch',
				'jsonModuleNaviSearch',
				'jsonAccountNaviSearch',
				'jsonLogNaviSearch',
				'jsonApiAccountNaviSearch',
			);

			foreach ($array as $key => $value) {
				$flagColumn = $value;
				$stmt = $dbh->prepare('insert into baseAccountMemo (stampRegister, stampUpdate, idAccount, flagColumn) values (?, ?, ?, ?);');
				$stmt->execute(array($stampRegister, $stampUpdate, $idAccount, $flagColumn));
			}

			$classDb->insertRow(array(
				'idModule'  => 'base',
				'strTable'  => 'baseLoginIdLogin',
				'arrColumn' => array('stampRegister', 'idLogin'),
				'arrValue'  => array($stampRegister, $idLogin),
			));

			$this->_loopPluginInsert(array(
				'varsAccount' => $varsAccount,
			));

			$classDb->insertRow(array(
				'idModule'  => 'base',
				'strTable'  => 'basePublish',
				'arrColumn' => array('stampRegister', 'session', 'idAccount'),
				'arrValue'  => array($stampRegister, $session, $idAccount),
			));

			$this->updateDbPreferenceStamp(array('strColumn' => 'account'));

			$dbh->commit();

		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}


		$classInit->updateVarsAll(array(
			'vars'     => &$varsAccounts,
			'strVars'  => 'varsAccounts',
			'strTable' => 'baseAccount',
		));

		$flag = $classMail->setMail(array(
			'pathVars'    => $this->_childSelf['pathVarsMailUser'],
			'pathTpl'     => $this->_childSelf['pathTplMailUser'],
			'arrValue'    => array(
				'numLimit' => $numLimit,
				'strName'  => $strName,
				'session'  => $pathConfirm,
				'strUrl'   => $strUrl,
			),
			'mailTo'      => $strMailPc,
			'arrMailBcc'  => array(),
			'arrMailCc'   => array(),
			'mailFrom'    => $varsPreference['strSiteMailPc'],
			'strNameFrom' => $varsPreference['strSiteName'],
		));


		if (!$flag) {
			$this->sendVars(array(
				'flag'    => 42,
				'vars'    => array(),
			));
		}

		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
		$this->_setSearch(array('flag' => 1));
	}


	/**
		(array('varsAccount' => array()));
	 */
	protected function _loopPluginInsert($arr)
	{
		global $varsPreference;
		global $varsModule;

		global $classEscape;

		$array = array();
		$varsAccount = $arr['varsAccount'];
		$id = $varsAccount['idModule'];

		if (preg_match( "/,base,/", $varsModule[$id]['arrCommaIdModuleAdmin'])
		) {
			$array = $varsPreference['jsonModule'];

		} else {
			$array = $classEscape->splitCommaArrayData(array(
				'data' => $varsModule[$id]['arrCommaIdModuleUser']
			));
		}
		$array = $varsPreference['jsonModule'];

		foreach ($array as $key => $value) {
			$strDir = $key;
			$strFile = ucwords($key);
			$path = PATH_BACK_CLASS_ELSE_PLUGIN . $strDir . '/' . $strFile . '.php';
			if (!file_exists($path)) {
				continue;
			}
			require_once($path);
			$strClass = 'Code_Else_Plugin_' . $strFile . '_' . $strFile;
			$classCall = new $strClass;
			$classCall->loop(array(
				'flagType'     => 'accountStatus',
				'flagStatus'   => 'insert',
				'varsAccount'  => $varsAccount,
			));
		}
	}

}
