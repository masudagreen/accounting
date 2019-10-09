<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Base_ApiAccountEditor extends Code_Else_Core_Base_ApiAccount
{
	protected $_childSelf = array(
		'pathTplJs'  => 'else/core/base/js/apiAccountEditor.js',
		'pathVarsJs' => 'back/tpl/vars/else/core/base/<strLang>/js/apiAccountEditor.php',
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
	protected function _iniDetailAdd()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $classEscape;

		global $varsRequest;

		$varsRequest['query']['vars']['Ip'] = strtolower($varsRequest['query']['vars']['Ip']);
		$varsRequest['query']['vars']['StrSiteUrl'] = strtolower($varsRequest['query']['vars']['StrSiteUrl']);

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsTarget = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget
		));

		$this->_checkValueDetail(array(
			'idTarget' => 0,
			'arrValue' => $arrValue,
		));

		$stampRegister = TIMESTAMP;
		$stampUpdate = TIMESTAMP;
		$idAccount = $arrValue['arr']['idAccount'];
		$ip = $arrValue['arr']['ip'];
		$strSiteUrl = $arrValue['arr']['strSiteUrl'];
		$arrSpaceStrTag = $classEscape->splitSpaceArray(array('data' => $arrValue['arr']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));

		try {
			$dbh->beginTransaction();

			$classDb->insertRow(array(
				'idModule'  => 'base',
				'strTable'  => 'baseApiAccount',
				'arrColumn' => array('stampRegister', 'stampUpdate', 'idAccount', 'ip', 'strSiteUrl', 'arrSpaceStrTag'),
				'arrValue'  => array($stampRegister, $stampUpdate, $idAccount, $ip, $strSiteUrl, $arrSpaceStrTag),
			));

			$this->updateDbPreferenceStamp(array('strColumn' => 'apiAccount'));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
		$this->_setSearch(array('flag' => 1));
	}

	/**
	 *
	 */
	protected function _iniDetailEdit()
	{
		global $classDb;
		global $classEscape;
		$dbh = $classDb->getHandle();

		global $varsRequest;

		$varsRequest['query']['vars']['Ip'] = strtolower($varsRequest['query']['vars']['Ip']);
		$varsRequest['query']['vars']['StrSiteUrl'] = strtolower($varsRequest['query']['vars']['StrSiteUrl']);
		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsTarget = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget
		));

		$this->_checkValueDetail(array(
			'idTarget' => $idTarget,
			'arrValue' => $arrValue,
		));

		$idAccount = $arrValue['arr']['idAccount'];
		$ip = $arrValue['arr']['ip'];
		$strSiteUrl = $arrValue['arr']['strSiteUrl'];
		$arrSpaceStrTag = $classEscape->splitSpaceArray(array('data' => $arrValue['arr']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));

		$arrDbColumn = array('idAccount', 'ip', 'strSiteUrl', 'arrSpaceStrTag');
		$arrDbValue = array($idAccount, $ip, $strSiteUrl, $arrSpaceStrTag);

		try {
			$dbh->beginTransaction();

			$classDb->updateRow(array(
				'idModule'  => 'base',
				'strTable'  => 'baseApiAccount',
				'arrColumn' => $arrDbColumn,
				'arrWhere'  => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'id',
						'flagCondition' => 'eq',
						'value'         => $idTarget,
					),
				),
				'arrValue'  => $arrDbValue,
			));

			$this->updateDbPreferenceStamp(array('strColumn' => 'apiAccount'));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$this->_iniSearchDetail();
	}

	/**
		$this->_checkValueDetail(array(
			'flagSelf' => int,
			'arrValue' => array(),
		));
	 */
	protected function _checkValueDetail($arr)
	{
		global $varsAccounts;

		global $classDb;

		$arrValue = $arr['arrValue'];
		$arrWhere = array();

		if ($arr['idTarget']) {
			$arrWhere[] = array(
				'flagType'      => '',
				'strColumn'     => 'id',
				'flagCondition' => 'ne',
				'value'         => $arr['idTarget'],
			);
		}

		$rows = $classDb->getSelect(array(
			'idModule' => 'base',
			'strTable' => 'baseApiAccount',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'   => 1,
			'arrWhere' => $arrWhere,
		));

		$arrayCheck = array();
		$array = $rows['arrRows'];
		foreach ($array as $key => $value) {
			$arrayCheck[$value['ip']][$value['idAccount']] = 1;
		}
		$arrayCheck[$arrValue['arr']['ip']][$arrValue['arr']['idAccount']] = 1;

		if (count($arrayCheck[$arrValue['arr']['ip']]) > 1) {
			$this->sendValue(array(
				'flag'    => 'sameAccount',
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}

		$arrWhere[] = array(
			'flagType'      => '',
			'strColumn'     => 'strSiteUrl',
			'flagCondition' => 'eq',
			'value'         => $arrValue['arr']['strSiteUrl'],
		);
		$arrWhere[] = array(
			'flagType'      => '',
			'strColumn'     => 'ip',
			'flagCondition' => 'eq',
			'value'         => $arrValue['arr']['ip'],
		);
		$arrWhere[] = array(
			'flagType'      => '',
			'strColumn'     => 'idAccount',
			'flagCondition' => 'eq',
			'value'         => $arrValue['arr']['idAccount'],
		);

		$rows = $classDb->getSelect(array(
			'idModule' => 'base',
			'strTable' => 'baseApiAccount',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'   => 1,
			'arrWhere' => $arrWhere,
		));

		if ($rows['numRows']) {
			$this->sendValue(array(
				'flag'    => 'sameData',
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}

		if (!$varsAccounts[$arrValue['arr']['idAccount']]) {
			$this->sendVars(array(
				'flag'    => 'idAccountCurrent',
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}

	}


}
