<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Login_Sign  extends Code_Else_Core_Login_Login
{
	protected $_extSelf = array(
		'pathVarsSignUser' => 'back/tpl/vars/else/core/login/<strLang>/mail/signUser.php',
		'pathTplSignUser' => 'back/tpl/vars/else/core/login/<strLang>/mail/signUser.tpl',
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
			'vars' => $vars['portal']['varsDetail']['varsDetail']['sign']['varsDetail'],
		));
		$values = $this->checkValue(array('values' => $values));
		$this->_checkValueSign(array('values' => $values['arr']));
		$this->_setValue(array('values' => $values['arr']));
	}

    /**
     * $arr = array(
     *     'values' => array(),
     * )
     */
	protected function _checkValueSign($arr)
	{
		global $classCheck;
		global $classMail;

		global $varsPreference;
		global $varsMedia;

		$flag = $classCheck->ipRange(array(
			'ip'  => $varsMedia['ip'],
			'arr' => $varsPreference['jsonIpSubnetSignReject'],
		));

		if ($flag) {
			sleep(1);
			$this->sendValue(array(
				'flag' => 1,
				'data' => '',
			));
		}

		$flag = $classCheck->ipRange(array(
			'ip'  => $varsMedia['ip'],
			'arr' => $varsPreference['jsonIpSignReject'],
		));

		if ($flag) {
			sleep(1);
			$this->sendValue(array(
				'flag' => 1,
				'data' => '',
			));
		}

		$array = $varsPreference['jsonMailSignReject'];
		foreach ($array as $key => $value) {
			if ($value == $arr['values']['strMailPc']) {
				$this->sendValue(array(
					'flag' => 40,
					'data' => '',
				));
			}
		}

		$arrHost = preg_split('/@/', $arr['values']['strMailPc']);
		$host = $arrHost[1];
		$array = $varsPreference['jsonMailHostSignReject'];
		foreach ($array as $key => $value) {
			if ($value == $host) {
				$this->sendValue(array(
					'flag' => 41,
					'data' => '',
				));
			}
		}

	}



    /**
     *
     */
	protected function _setValue($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $classMail;
		global $classDisplay;

		global $varsPreference;
		global $varsMedia;

		$tm = TIMESTAMP;
		$str = MICROTIMESTAMP . $classDisplay->getPassword(array(
			'numMark'  => 5,
			'numNum'   => 5,
			'numBig'   => 5,
			'numSmall' => 5,
		));
		$session = hash('sha256', $str);
        $pathConfirm = $varsPreference['strTopUrl'] . 'confirm.php?type=sign&id=' . $session;

		$stampRegister = $tm;
		$ip = $varsMedia['ip'];
		$strCodeName = $arr['values']['strCodeName'];
		$idLogin = $arr['values']['idLogin'];
		$strMailPc = $arr['values']['strMailPc'];


		try {
			$dbh->beginTransaction();

			$stmt = $dbh->prepare('insert into baseApplySign(stampRegister, session, ip, strCodeName, idLogin, strMailPc) values (?, ?, ?, ?, ?, ?);');
			$stmt->execute(array($stampRegister, $session, $ip, $strCodeName, $idLogin, $strMailPc));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$numLimit = round(NUM_SESSION /60/60);
        $strName = $varsPreference['strSiteName'];
        $strUrl = $varsPreference['strSiteUrl'];

        $flag = $classMail->setMail(array(
			'pathVars'    => $this->_extSelf['pathVarsSignUser'],
			'pathTpl'     => $this->_extSelf['pathTplSignUser'],
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

		$this->sendValue(array(
			'flag' => 1,
			'data' => '',
		));
	}

}
