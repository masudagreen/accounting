<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Login_Forgot  extends Code_Else_Core_Login_Login
{
	protected $_extSelf = array(
		'pathVarsForgotUser' => 'back/tpl/vars/else/core/login/<strLang>/mail/forgotUser.php',
		'pathTplForgotUser' => 'back/tpl/vars/else/core/login/<strLang>/mail/forgotUser.tpl',
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
			'vars' => $vars['portal']['varsDetail']['varsDetail']['forgot']['varsDetail'],
		));
		$values = $this->checkValue(array('values' => $values));
		$this->_checkValueForgot(array('values' => $values['arr']));
		$this->_setValue(array('values' => $values['arr']));
	}

    /**
     * $arr = array(
     *     'values' => array(),
     * )
     */
	protected function _checkValueForgot($arr)
	{
		global $varsAccounts;
		global $varsPreference;
		global $varsTerm;
		global $varsAccount;

		$array = $varsAccounts;
		$varsAccount = array();
		foreach ($array as $key => $value) {
			if ($value['strMailPc'] = $arr['values']['strMailPc']) {
				$varsAccount = $value;
				break;
			}
		}

		$flag = 0;
		$tm = TIMESTAMP;

		if (!$varsAccount['strMailPc']
			|| $varsAccount['flagLock']
		) {
			$flag = 1;
		}

		if ($varsPreference['numPasswordLimit']) {
			$stampUpdateLimit = $varsAccount['stampUpdatePassword'] + $varsPreference['numPasswordLimit'] * 86400;
			if ($tm > $stampUpdateLimit) {
				$flag = 1;
			}
		}

		if ($varsTerm && !$varsAccount['flagWebmaster']) {
			$idTerm = $varsAccount['idTerm'];
			if (($varsTerm[$idTerm]['stampStart'] > 0 && !$varsTerm[$idTerm]['stampEnd'])
				&& $varsTerm[$idTerm]['stampStart'] > $tm
			) {
				$flag = 1;

			} elseif (($varsTerm[$idTerm]['stampStart'] > 0 && $varsTerm[$idTerm]['stampEnd'] > 0)
				&& $varsTerm[$idTerm]['stampStart'] > $tm && $tm > $varsTerm[$idTerm]['stampEnd']
			) {
				$flag = 1;

			}
		}

		if ($flag) {
			sleep(1);
			$this->sendVars(array(
				'flag'    => 1,
				'vars'    => array(),
			));
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
		global $varsAccount;

		$tm = TIMESTAMP;
		$str = MICROTIMESTAMP . $classDisplay->getPassword(array(
			'numMark'  => 5,
			'numNum'   => 5,
			'numBig'   => 5,
			'numSmall' => 5,
		));
		$session = hash('sha256', $str);
        $pathConfirm = $varsPreference['strTopUrl'] . 'confirm.php?type=forgot&id=' . $session;

		try {
			$dbh->beginTransaction();

			$stmt = $dbh->prepare('insert into baseApplyForgot(stampRegister, idAccount, session, ip) values (?, ?, ?, ?);');
			$stmt->execute(array($tm, $varsAccount['id'], $session, $varsMedia['ip']));

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
			'pathVars'    => $this->_extSelf['pathVarsForgotUser'],
			'pathTpl'     => $this->_extSelf['pathTplForgotUser'],
			'arrValue'    => array(
				'numLimit' => $numLimit,
				'strName'  => $strName,
				'session'  => $pathConfirm,
				'strUrl'   => $strUrl,
			),
			'mailTo'      => $arr['values']['strMailPc'],
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
