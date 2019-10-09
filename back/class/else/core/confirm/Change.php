<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Confirm_Change  extends Code_Else_Core_Confirm_Confirm
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
		global $varsAccount;
		global $varsMedia;

		global $classDb;

		$vars = $this->getVars(array(
			'path' => $this->_self['path']['file']['varsPortal'],
		));

		if (!$varsAccount) {
			$vars['portal']['varsDetail']['varsDetail'] = $vars['portal']['varsDetail']['varsDetailCaution'];
				unset($vars['portal']['varsDetail']['varsDetailCaution']);
				unset($vars['portal']['varsDetail']['varsDetailEnd']);
				unset($vars['portal']['varsDetail']['varsDetailPassword']);
				unset($vars['portal']['varsDetail']['varsDetailAccount']);
				unset($vars['portal']['varsDetail']['varsDetailLogin']);
				$this->sendVars(array(
				'vars' => $vars,
			));
		}

		$rows = $classDb->getSelect(array(
			'idModule' => 'base',
			'strTable' => 'baseApplyChange',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
				array(
					'flagType'      => '',
					'strColumn'     => 'session',
					'flagCondition' => 'eq',
					'value'         => $varsRequest['query']['id'],
				),
				array(
					'flagType'      => 'ip',
					'strColumn'     => 'ip',
					'flagCondition' => 'eq',
					'value'         => $varsMedia['ip'],
				),
			),
			'arrColumn' => array(),
		));

		if ($rows['numRows'] && !$rows['arrRows'][0]['flagAttest']) {
			$vars['portal']['varsDetail']['varsDetail'] = $vars['portal']['varsDetail']['varsDetailEnd'];
			unset($vars['portal']['varsDetail']['varsDetailCaution']);
			unset($vars['portal']['varsDetail']['varsDetailEnd']);
			unset($vars['portal']['varsDetail']['varsDetailPassword']);
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
		global $varsAccount;
		global $varsMedia;

		global $classDb;
		$dbh = $classDb->getHandle();

		$arrColumn = array('flagAttest');
		$arrValue = array(1);
		try {
			$dbh->beginTransaction();

			$classDb->updateRow(array(
				'idModule'  => 'base',
				'strTable'  => 'baseApplyChange',
				'arrColumn' => $arrColumn,
				'arrWhere'  => array(
					array(
						'flagType'      => '',
						'strColumn'     => 'session',
						'flagCondition' => 'eq',
						'value'         => $varsRequest['query']['id'],
					),
				),
				'arrValue'  => $arrValue,
			));

			$this->updateDbPreferenceStamp(array(
				'strColumn' => 'applyChange',
			));

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
		$strMailPc = '';
        $strName = $varsPreference['strSiteName'];
        $strUrl = $varsPreference['strSiteUrl'];

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
				'pathVars'    => $this->_self['path']['file']['varsChangeAdmin'],
				'pathTpl'     => $this->_self['path']['file']['tplChangeAdmin'],
				'arrValue'    => array(
					'strName'  => $strName,
					'strUrl'   => $strUrl,
				),
				'mailTo'      =>  $value,
				'arrMailBcc'  => array(),
				'arrMailCc'   => array(),
				'mailFrom'    => $varsPreference['strSiteMailPc'],
				'strNameFrom' => $varsPreference['strSiteName'],
			));

		}


	}

}
