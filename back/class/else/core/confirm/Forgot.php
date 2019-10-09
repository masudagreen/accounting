<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Confirm_Forgot  extends Code_Else_Core_Confirm_Confirm
{
	protected $_extSelf = array(

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
		global $varsPreference;
		global $varsAccounts;
		global $varsAccount;

		global $classDb;
		global $classCheck;
		global $classDisplay;

		$vars = $this->getVars(array(
			'path' => $this->_self['path']['file']['varsPortal'],
		));

		$row = $classDb->getColumnValue(array(
			'strSql'    => 'select * from baseApplyForgot where ip = ? and session = ?;',
			'arrValue'  => array($varsMedia['ip'], $varsRequest['query']['id']),
			'strColumn' => 'idAccount',
		));

		if ($row) {
			$varsAccount = $varsAccounts[$row['idAccount']];

			$strPassword = $classDisplay->getPassword(array(
				'numMark'  => 1,
				'numNum'   => 1,
				'numBig'   => 1,
				'numSmall' => abs($varsPreference['numPassword '] - 3),
			));

			$password = hash('sha256', $strPassword);

			$flag = $this->_checkValue();

			if (!$flag) {
				$vars['portal']['varsDetail']['varsDetail'] = $vars['portal']['varsDetail']['varsDetailPassword'];
				unset($vars['portal']['varsDetail']['varsDetailCaution']);
				unset($vars['portal']['varsDetail']['varsDetailEnd']);
				unset($vars['portal']['varsDetail']['varsDetailPassword']);
				unset($vars['portal']['varsDetail']['varsDetailAccount']);
				unset($vars['portal']['varsDetail']['varsDetailLogin']);
				$vars['portal']['varsDetail']['varsDetail']['varsDetail'][0]['strComment'] = str_replace('<%replace%>', $strPassword, $vars['portal']['varsDetail']['varsDetail']['varsDetail'][0]['strComment']);
				$this->_setDb(array('password' => $password));
			}
		}

		$this->sendVars(array(
			'vars' => $vars,
		));
	}

    /**
     */
	protected function _checkValue()
	{
		global $varsAccounts;
		global $varsPreference;
		global $varsTerm;
		global $varsAccount;

		$flag = 0;
		$tm = TIMESTAMP;

		if ($varsAccount['flagLock']) {
			$flag = 1;
		}

		if ($varsPreference['numPasswordLimit']) {
			$stampUpdateLimit = $varsAccount['stampUpdatePassword'] + $varsPreference['numPasswordLimit'] * 86400;
			if ($tm > $stampUpdateLimit) {
				$flag = 1;
			}
		}

		if ($varsTerm  && !$varsAccount['flagWebmaster']) {
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

		return $flag;
	}

	/**
	 *
	 */
	protected function _setDb($arr)
	{
		global $varsRequest;
		global $varsAccount;

		global $classDb;
		$dbh = $classDb->getHandle();
		global $classInit;

		$tm = TIMESTAMP;
		try {
			$dbh->beginTransaction();

			$stmt = $dbh->prepare('delete from baseApplyForgot where session = ?;');
			$stmt->execute(array($varsRequest['query']['id']));

			$stmt = $dbh->prepare('update baseAccount set stapUpdate = ?, stampUpdatePassword = ?, strPassword = ? where id = ?;');
			$stmt->execute(array($tm, $tm, $arr['password'], $varsAccount['id']));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$classInit->updateVarsAccount();

	}

}
