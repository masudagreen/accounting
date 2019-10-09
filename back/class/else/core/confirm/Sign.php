<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Confirm_Sign  extends Code_Else_Core_Confirm_Confirm
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

		global $classDb;

		$vars = $this->getVars(array(
			'path' => $this->_self['path']['file']['varsPortal'],
		));

		$row = $classDb->getColumnValue(array(
			'strSql'    => 'select * from baseApplySign where ip = ? and session = ? and flagAttest = 0;',
			'arrValue'  => array($varsMedia['ip'], $varsRequest['query']['id']),
			'strColumn' => 'id',
		));

		if ($row) {
			$vars['portal']['varsDetail']['varsDetail'] = $vars['portal']['varsDetail']['varsDetailEnd'];
			unset($vars['portal']['varsDetail']['varsDetailCaution']);
			unset($vars['portal']['varsDetail']['varsDetailEnd']);
			unset($vars['portal']['varsDetail']['varsDetailPassword']);
			unset($vars['portal']['varsDetail']['varsDetailAccount']);
			unset($vars['portal']['varsDetail']['varsDetailLogin']);
			$this->_setDb();
			$this->_sendMail();
		}

		$this->sendVars(array(
			'vars' => $vars,
		));
	}

	/**
	 *
	 */
	protected function _setDb()
	{
		global $varsRequest;
		global $varsPreference;

		global $classDb;
		$dbh = $classDb->getHandle();

		$tm = TIMESTAMP;
		$varsPreference['jsonStampUpdate']['applySign'] = $tm;
		$jsonStampUpdate = json_encode($varsPreference['jsonStampUpdate']);

		try {
			$dbh->beginTransaction();

			$stmt = $dbh->prepare('update baseApplySign set flagAttest = 1 where session = ?;');
			$stmt->execute(array($varsRequest['query']['id']));

			$stmt = $dbh->prepare('update basePreference set stampUpdate = ?, jsonStampUpdate = ?;');
			$stmt->execute(array($tm, $jsonStampUpdate));

			$varsPreference['stampUpdate'] = $tm;
			$varsPreference['jsonStampUpdate'] = $jsonStampUpdate;

			if (FLAG_APC) {
				apc_store('varsPreference', $varsPreference);
			}

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

	}

	/**
	 *
	 */
	protected function _sendMail()
	{
		global $classMail;

		global $varsAccounts;
		global $varsModule;
		global $varsPreference;

		$array = $varsAccounts;
		$arrayNew = array();
		$num = 0;
		$strMailPc = '';
        $strName = $varsPreference['strSiteName'];
        $strUrl = $varsPreference['strSiteUrl'];

        foreach ($array as $key => $value) {
			if ($value['flagWebmaster']) {
				$strMailPc = $value['strMailPc'];
				continue;
			}

			$id = $value['idModule'];
			$data = $varsModule[$id]['arrCommaIdModuleAdmin'];

			if (preg_match( "/,base,/", $data)) {
				$arrayNew[$num] = $value['strMailPc'];
				$num++;
			}
		}

		$flag = $classMail->setMail(array(
			'pathVars'    => $this->_self['path']['file']['varsSignAdmin'],
			'pathTpl'     => $this->_self['path']['file']['tplSignAdmin'],
			'arrValue'    => array(
				'strName'  => $strName,
				'strUrl'   => $strUrl,
			),
			'mailTo'      => $strMailPc,
			'arrMailBcc'  => $arrayNew,
			'arrMailCc'   => array(),
			'mailFrom'    => $varsPreference['strSiteMailPc'],
			'strNameFrom' => $varsPreference['strSiteName'],
		));

	}


}
