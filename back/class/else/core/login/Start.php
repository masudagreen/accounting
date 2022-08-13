<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Login_Start  extends Code_Else_Core_Login_Login
{
	protected $_extSelf = array(
		'pathVarsLockAdmin'   => 'back/tpl/vars/else/core/login/<strLang>/mail/lockAdmin.php',
		'pathTplLockAdmin'    => 'back/tpl/vars/else/core/login/<strLang>/mail/lockAdmin.tpl',
		'pathVarsLogin'       => 'back/tpl/vars/else/core/login/<strLang>/mail/login.php',
		'pathTplLogin'        => 'back/tpl/vars/else/core/login/<strLang>/mail/login.tpl',
		'pathVarsLoginSecond' => 'back/tpl/vars/else/core/login/<strLang>/mail/loginSecond.php',
		'pathTplLoginSecond'  => 'back/tpl/vars/else/core/login/<strLang>/mail/loginSecond.tpl',
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
	}

    /**
     *
     */
	protected function _iniValue()
	{
		$vars = $this->getVars(array('path' => $this->_self['path']['file']['varsPortal']));
		$values = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['varsDetail']['start']['varsDetail'],
		));
		$values = $this->checkValue(array('values' => $values));
		$this->_checkValueLogin(array('values' => $values['arr']));

		$this->_checkLoginType();

	}

	/**
     */
	protected function _checkLoginType()
	{
		global $varsPreference;
		global $varsAccount;

		if ($varsPreference['flagLoginSecond']) {
			$this->_setValueSecond();
		} else {
			if ($varsAccount['flagLoginSecond']) {
				$this->_setValueSecond();
			} else {
				$this->_setValue();
			}
		}
	}

    /**
     * $arr = array(
     *     'values' => array(),
     * )
     */
	protected function _checkValueLogin($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $varsPreference;
		global $varsAccounts;
		global $varsTerm;
		global $varsAccount;

		$strPassword = hash('sha256', $arr['values']['strPassword']);

		$flag = 0;
		$tm = TIMESTAMP;
		$array = $varsAccounts;

        foreach ($array as $key => $value) {
			if ($arr['values']['idLogin'] == $array[$key]['idLogin']) {
				$flag = 1;
				//lock
				if ($array[$key]['flagLock']) {
					$this->_sendValueError(array('values' => $arr['values'], 'strError' => 'lock'));
				}

				//pass
				if ($strPassword != $array[$key]['strPassword']) {

					$this->_checkValueMiss(array('values' => $arr['values']));
				}

				//term
				$stmt = $dbh->prepare('select * from baseAccount where idLogin = ?;');
				$stmt->execute(array($arr['values']['idLogin']));
				$vars = array();
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$vars = $row;
					break;
				}
				$varsAccount = $vars;

				$flagPasswordLimit = 0;
				if ($varsPreference['numPasswordLimit']) {
					$stampUpdateLimit = $varsAccount['stampUpdatePassword'] + $varsPreference['numPasswordLimit'] * 86400;
					if ($tm > $stampUpdateLimit) {
						$flagPasswordLimit = 1;
					}
				}

				if ($varsAccount['flagWebmaster']) {
					if ($flagPasswordLimit) {
						$this->_sendValueError(array('values' => $arr['values'], 'strError' => 'password limit'));
					}
					return;
				}

				if ($varsPreference['numPasswordLimit']) {
					$stampLimit = $tm - $varsPreference['numPasswordLimit'] * 86400;
					if ($stampLimit > $varsAccount['stampUpdatePassword']) {
						$this->_sendValueError(array('values' => $arr['values'], 'strError' => 'limit password'));
					}
				}

				if ($varsTerm) {
					$idTerm = $array[$key]['idTerm'];

					//not yet
					if (!($varsTerm[$idTerm]['stampStart'] <= $tm
						&& (!$varsTerm[$idTerm]['stampEnd'] || $varsTerm[$idTerm]['stampEnd'] >= $tm)
					)) {
						$this->_sendValueError(array('values' => $arr['values'], 'strError' => 'term account'));
					}

				}

				if ($varsPreference['numPasswordLimit']) {
					$stampUpdateLimit = $varsAccount['stampUpdatePassword'] + $varsPreference['numPasswordLimit'] * 86400;
					if ($tm > $stampUpdateLimit) {
						var_dump(1);
						exit;
						$this->_sendValueError(array('values' => $arr['values'], 'strError' => 'password account'));
					}
				}

				if ($flagPasswordLimit) {
					$this->_sendValueError(array('values' => $arr['values'], 'strError' => 'password limit'));
				}
				return;
			}
		}

		if (!$flag) {
			$this->_sendValueError(array('values' => $arr['values'], 'strError' => 'id'));
		}
	}

    /**
     * $arr = array(
     *     'values' => array(),
     * )
     */
	protected function _checkValueMiss($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $varsMedia;
		global $varsAccounts;
		global $varsAccount;
		global $varsPreference;

		$flag = 0;
		$tm = TIMESTAMP;
		$stampLimit = $tm - NUM_SESSION;
		$varsPreference['jsonStampUpdate']['lock'] = $tm;
		$jsonStampUpdate = json_encode($varsPreference['jsonStampUpdate']);

		try {
			$dbh->beginTransaction();

			$stmt = $dbh->prepare('insert into baseLoginMiss(stampRegister, ip, idLogin, strPassword, strError) values (?, ?, ?, ?, ?);');
			$stmt->execute(array($tm, $varsMedia['ip'], $arr['values']['idLogin'], $arr['values']['strPassword'], 'password'));

			$stmt = $dbh->prepare('select * from baseLoginMiss where idLogin = ? and stampRegister > ?;');
			$stmt->execute(array($arr['values']['idLogin'], $stampLimit));
			$num = $stmt->rowCount();

			//vars
			$stmt = $dbh->prepare('select * from baseAccount where idLogin = ?;');
			$stmt->execute(array($arr['values']['idLogin']));
			$vars = array();
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$vars = $row;
				break;
			}
			$varsAccount = $vars;


			if ($num > $varsPreference['numAutoLock']) {
				//db account lock

				$stmt = $dbh->prepare('update baseAccount set flagLock = ?, stampUpdate = ? where idLogin = ?;');
				$stmt->execute(array(1, $tm, $arr['values']['idLogin']));


				$varsAccount['flagLock'] = 1;
				$varsAccounts[$varsAccount['id']] = $varsAccount;
				if (FLAG_APC) {
					apc_store('varsAccounts', $varsAccounts);
				}

				$stmt = $dbh->prepare('insert into baseLock(stampRegister, idAccount) values(?, ?);');
				$stmt->execute(array($tm, $varsAccount['id']));

				$stmt = $dbh->prepare('update basePreference set stampUpdate = ?, jsonStampUpdate = ?;');
				$stmt->execute(array($tm, $jsonStampUpdate));

				$varsPreference['stampUpdate'] = $tm;
				$varsPreference['jsonStampUpdate'] = $jsonStampUpdate;

				if (FLAG_APC) {
					apc_store('varsPreference', $varsPreference);
				}

				$flag = 1;
			}

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		if ($flag) {
			$this->_sendMailAdmin(array(
				'pathVars' => $this->_extSelf['pathVarsLockAdmin'],
				'pathTpl'  => $this->_extSelf['pathTplLockAdmin'],
				'arrValue' => array(
					'strName'  => $varsPreference['strSiteName'],
					'strUrl'   => $varsPreference['strSiteUrl'],
				),
			));
		}

		$this->sendValue(array(
			'flag' => 403,
			'data' => '',
		));
	}

    /**
     *
     */
	protected function _sendMailAdmin($arr)
	{
		global $classMail;

		global $varsAccounts;
		global $varsModule;
		global $varsPreference;

		$array = $varsAccounts;
		$arrayNew = array();
        foreach ($array as $key => $value) {
			if ($value['flagWebmaster']) {
				$arrayNew[] = $value['strMailPc'];
				continue;
			}
			$id = $value['idModule'];
			$data = $varsModule[$id]['arrCommaIdModuleAdmin'];
			if (preg_match( "/,base,/", $data)) {
				$arrayNew[] = $value['strMailPc'];
			}
		}

		$array = $arrayNew;
		foreach ($array as $key => $value) {
			$flag = $classMail->setMail(array(
				'pathVars'    => $arr['pathVars'],
				'pathTpl'     => $arr['pathTpl'],
				'arrValue'    => $arr['arrValue'],
				'mailTo'      => $value,
				'arrMailBcc'  => array(),
				'arrMailCc'   => array(),
				'mailFrom'    => $varsPreference['strSiteMailPc'],
				'strNameFrom' => $varsPreference['strSiteName'],
			));
		}
	}

    /**
     * $arr = array(
     *     'values'   => array(),
     *     'strError' => string,
     * )
     */
	protected function _sendValueError($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $varsMedia;


		try {
			$dbh->beginTransaction();

			$stmt = $dbh->prepare('insert into baseLoginMiss(stampRegister, ip, idLogin, strPassword, strError) values (?, ?, ?, ?, ?);');
			$stmt->execute(array(TIMESTAMP, $varsMedia['ip'], $arr['values']['idLogin'], $arr['values']['strPassword'], $arr['strError']));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}
		$this->sendValue(array(
			'flag' => 403,
			'data' => '',
		));
	}

	/**
     *
     */
	protected function _setValueSecond()
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $classDisplay;
		global $classMail;

		global $varsPreference;
		global $varsMedia;
		global $varsAccount;

		$classTime = new Code_Else_Lib_Time();

		$stampRegister = TIMESTAMP;
		$str = MICROTIMESTAMP . $classDisplay->getPassword(array(
			'numMark'  => 5,
			'numNum'   => 5,
			'numBig'   => 5,
			'numSmall' => 5,
		));
		$session = hash('sha256', $str);
        $pathConfirm = $varsPreference['strTopUrl'] . 'confirm.php?type=login&id=' . $session;
		$strName = $varsPreference['strSiteName'];
		$strUrl = $varsPreference['strSiteUrl'];
		$strIp = $varsMedia['ip'];

		try {
			$dbh->beginTransaction();

			$stampLimit = TIMESTAMP - NUM_SESSION_LOGIN_SECOND;
			$stmt = $dbh->prepare('delete from baseLoginSecond where stampRegister < ?;');
			$stmt->execute(array($stampLimit));

			$stmt = $dbh->prepare('delete from baseLoginSecond where idAccount = ?;');
			$stmt->execute(array($varsAccount['id']));

			$stmt = $dbh->prepare('insert into baseLoginSecond(stampRegister, ip, session, idAccount) values (?, ?, ?, ?);');
			$stmt->execute(array($stampRegister, $strIp, $session, $varsAccount['id']));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$classTime->setTimeZone(array('data' => $varsAccount['numTimeZone']));
		$strTime = $classTime->getDisplay(array(
			'stamp'    => TIMESTAMP,
			'flagType' => 'year-sec',
		));

		$flag = $classMail->setMail(array(
			'pathVars'    => $this->_extSelf['pathVarsLoginSecond'],
			'pathTpl'     => $this->_extSelf['pathTplLoginSecond'],
			'arrValue'    => array(
				'strTime'  => $strTime,
				'strName'  => $strName,
				'session'  => $pathConfirm,
				'strUrl'   => $strUrl,
				'strIp'    => $strIp,
			),
			'mailTo'      => $varsAccount['strMailPc'],
			'arrMailBcc'  => array(),
			'arrMailCc'   => array(),
			'mailFrom'    => $varsPreference['strSiteMailPc'],
			'strNameFrom' => $varsPreference['strSiteName'],
		));

		$this->sendValue(array(
			'flag' => 'second',
			'vars' => array(),
		));
	}

    /**
     *
     */
	protected function _setValue()
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $classDisplay;

		global $varsMedia;
		global $varsSession;
		global $varsAccount;
		global $varsPreference;
		$classTime = new Code_Else_Lib_Time();

		$tm = TIMESTAMP;
		$str = MICROTIMESTAMP . $classDisplay->getPassword(array(
			'numMark'  => 5,
			'numNum'   => 5,
			'numBig'   => 5,
			'numSmall' => 5,
		));
		$idCookie = hash('sha256', $str);

		try {
			$dbh->beginTransaction();

			$stampLimit = TIMESTAMP - NUM_SESSION * 1;
			$stmt = $dbh->prepare('delete from baseSession where stampRegister < ?;');
			$stmt->execute(array($stampLimit));

			if (!FLAG_TEST) {
				$stmt = $dbh->prepare('delete from baseSession where idAccount = ? and flagAPI = 0;');
				$stmt->execute(array($varsAccount['id']));
			}

			$stmt = $dbh->prepare('insert into baseSession(stampRegister, ip, idCookie, idAccount) values (?, ?, ?, ?);');
			$stmt->execute(array($tm, $varsMedia['ip'], $idCookie, $varsAccount['id']));

			$stmt = $dbh->prepare('select * from baseSession where idCookie = ?;');
			$stmt->execute(array($idCookie));
			$session = array();
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$session = $row;
				break;
			}
			$varsSession[$idCookie] = $session;

			if (FLAG_APC) {
				apc_store('varsSession', $varsSession);
			}

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$classTime->setTimeZone(array('data' => $varsAccount['numTimeZone']));
		$strTime = $classTime->getDisplay(array(
			'stamp'    => TIMESTAMP,
			'flagType' => 'year-sec',
		));

		if ($varsPreference['flagLoginMail']) {
			$this->_sendMailAdmin(array(
				'pathVars' => $this->_extSelf['pathVarsLogin'],
				'pathTpl'  => $this->_extSelf['pathTplLogin'],
				'arrValue' => array(
					'strName'     => $varsPreference['strSiteName'],
					'strUrl'      => $varsPreference['strSiteUrl'],
					'id'          => $varsAccount['id'],
					'strCodeName' => $varsAccount['strCodeName'],
					'strIp'       => $varsMedia['ip'],
					'strHost'     => $varsMedia['host'],
					'strTime'     => $strTime,
				),
			));
		}
		if ($varsAccount['flagLoginMail']) {
			$this->_sendMailAccount(array(
				'pathVars' => $this->_extSelf['pathVarsLogin'],
				'pathTpl'  => $this->_extSelf['pathTplLogin'],
				'strMail'  => $varsAccount['strMailPc'],
				'arrValue' => array(
					'strName'     => $varsPreference['strSiteName'],
					'strUrl'      => $varsPreference['strSiteUrl'],
					'id'          => $varsAccount['id'],
					'strCodeName' => $varsAccount['strCodeName'],
					'strIp'       => $varsMedia['ip'],
					'strHost'     => $varsMedia['host'],
					'strTime'     => $strTime,
				),
			));
		}

		$this->sendValue(array(
			'flag' => 1,
			'vars' => array('id' => $idCookie),
		));
	}

	/**
     *
     */
	protected function _sendMailAccount($arr)
	{
		global $classMail;

		global $varsAccounts;
		global $varsModule;
		global $varsPreference;

		$array = $varsAccounts;
		if ($varsPreference['flagLoginMail']) {
	        foreach ($array as $key => $value) {
				if ($value['flagWebmaster']) {
					if ($value['strMailPc'] == $arr['strMail']) {
						return;
					}
				}
				$id = $value['idModule'];
				$data = $varsModule[$id]['arrCommaIdModuleAdmin'];
				if (preg_match( "/,base,/", $data)) {
					if ($value['strMailPc'] == $arr['strMail']) {
						return;
					}
				}
			}
		}

		$array = array($arr['strMail']);
		foreach ($array as $key => $value) {
			$flag = $classMail->setMail(array(
				'pathVars'    => $arr['pathVars'],
				'pathTpl'     => $arr['pathTpl'],
				'arrValue'    => $arr['arrValue'],
				'mailTo'      => $value,
				'arrMailBcc'  => array(),
				'arrMailCc'   => array(),
				'mailFrom'    => $varsPreference['strSiteMailPc'],
				'strNameFrom' => $varsPreference['strSiteName'],
			));
		}
	}

}
