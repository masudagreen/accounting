<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Confirm_Publish  extends Code_Else_Core_Confirm_Confirm
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
			'strSql'    => 'select * from basePublish where session = ?;',
			'arrValue'  => array($varsRequest['query']['id']),
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

			$vars['portal']['varsDetail']['varsDetail'] = $vars['portal']['varsDetail']['varsDetailAccount'];
			unset($vars['portal']['varsDetail']['varsDetailCaution']);
			unset($vars['portal']['varsDetail']['varsDetailEnd']);
			unset($vars['portal']['varsDetail']['varsDetailPassword']);
			unset($vars['portal']['varsDetail']['varsDetailAccount']);
			unset($vars['portal']['varsDetail']['varsDetailLogin']);
			$vars['portal']['varsDetail']['varsDetail']['varsDetail'][0]['strComment'] = str_replace('<%strPassword%>', $strPassword, $vars['portal']['varsDetail']['varsDetail']['varsDetail'][0]['strComment']);
			$vars['portal']['varsDetail']['varsDetail']['varsDetail'][0]['strComment'] = str_replace('<%idLogin%>', $varsAccount['idLogin'], $vars['portal']['varsDetail']['varsDetail']['varsDetail'][0]['strComment']);
			$this->_setDb(array('password' => $password));
		}

		$this->sendVars(array(
			'vars' => $vars,
		));
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

			$stmt = $dbh->prepare('delete from basePublish where session = ?;');
			$stmt->execute(array($varsRequest['query']['id']));

			$stmt = $dbh->prepare('update baseAccount set stampUpdate = ?, stampUpdatePassword = ?, strPassword = ? where id = ?;');
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
