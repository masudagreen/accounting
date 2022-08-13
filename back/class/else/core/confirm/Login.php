<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Confirm_Login  extends Code_Else_Core_Confirm_Confirm
{
	protected $_extSelf = array(
		'pathVarsLogin' => 'back/tpl/vars/else/core/confirm/<strLang>/mail/login.php',
		'pathTplLogin'  => 'back/tpl/vars/else/core/confirm/<strLang>/mail/login.tpl',
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
	protected function _iniVars()
	{
		global $varsRequest;
		global $varsMedia;
		global $varsAccounts;
		global $varsAccount;
		global $varsPreference;

		global $classDb;

		$vars = $this->getVars(array(
			'path' => $this->_self['path']['file']['varsPortal'],
		));

		$stampLimit = TIMESTAMP - NUM_SESSION_LOGIN_SECOND;

		$idAccount = $classDb->getColumnValue(array(
			'strSql'    => 'select * from baseLoginSecond where ip = ? and session = ? and stampRegister > ?;',
			'arrValue'  => array($varsMedia['ip'], $varsRequest['query']['id'], $stampLimit),
			'strColumn' => 'idAccount',
		));
		$varsAccount = $varsAccounts[$idAccount];

		$flag = 0;
		if ($varsAccount) {
			if ($varsPreference['flagLoginSecond']) {
				$flag = 1;

			} else {
				if ($varsAccount['flagLoginSecond']) {
					$flag = 1;
				}
			}
		}

		if ($flag) {
			$vars['portal']['varsDetail']['varsDetail'] = $vars['portal']['varsDetail']['varsDetailLogin'];
			unset($vars['portal']['varsDetail']['varsDetailCaution']);
			unset($vars['portal']['varsDetail']['varsDetailEnd']);
			unset($vars['portal']['varsDetail']['varsDetailPassword']);
			unset($vars['portal']['varsDetail']['varsDetailAccount']);
			$vars = $this->_setValue(array('vars' => $vars));
		}


		$this->sendVars(array(
			'vars' => $vars,
		));
	}

	/**
     *
     */
	protected function _setValue($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $classDisplay;

		global $varsMedia;
		global $varsSession;
		global $varsAccount;
		global $varsPreference;
		global $varsRequest;


		$classTime = new Code_Else_Lib_Time();

		$tm = TIMESTAMP;
		$str = MICROTIMESTAMP . $classDisplay->getPassword(array(
			'numMark'  => 5,
			'numNum'   => 5,
			'numBig'   => 5,
			'numSmall' => 5,
		));
		$idCookie = hash('sha256', $str);
		$arr['vars']['portal']['varsDetail']['varsDetail']['idCookie'] = $idCookie;

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

			$stmt = $dbh->prepare('delete from baseLoginSecond where idAccount = ?;');
			$stmt->execute(array($varsAccount['id']));

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

		return $arr['vars'];
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
