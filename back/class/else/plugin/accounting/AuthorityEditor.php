<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_AuthorityEditor extends Code_Else_Plugin_Accounting_Authority
{
	protected $_childSelf = array(
		'pathTplJs'   => 'else/plugin/accounting/js/authorityEditor.js',
		'pathVarsJs'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/authorityEditor.php',
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
		global $varsRequest;
		global $classPluginAccountingInit;

		$dbh = $classDb->getHandle();

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsTarget['vars']['varsDetail'] = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget['vars']['varsDetail']
		));

		$this->_checkStrTitle(array(
			'strTitle' => $arrValue['arr']['strTitle'],
			'idTarget' => 0,
		));

		$arrValue = $this->_checkValueDetail($arrValue);

		$arrSql = $this->_updateDbValue(array(
			'arr' => $arrValue['arr']
		));

		try {
			$dbh->beginTransaction();

			$classDb->insertRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingAuthority',
				'arrColumn' => $arrSql['arrColumn'],
				'arrValue'  => $arrSql['arrValue'],
			));

			$this->_updateDbPreferenceStamp(array('strColumn' => 'authority'));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$classPluginAccountingInit->updateInitAuthority();
		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
		$this->_setSearch(array('flag' => 1));
	}

	/**
		$this->_checkStrTitle(array(
			'strTitle' => '',
			'idTarget' => 0,
		));
	 */
	protected function _checkStrTitle($arr)
	{
		global $varsPluginAccountingAuthority;

		$array = &$varsPluginAccountingAuthority;
		foreach ($array as $key => $value) {
			$flag = 0;
			if ($arr['idTarget']) {
				if ($value['strTitle'] == $arr['strTitle'] && $arr['idTarget'] != $value['id']) {
					$flag = 1;
				}

			} else {
				if ($value['strTitle'] == $arr['strTitle']) {
					$flag = 1;
				}
			}

			if ($flag) {
				$this->sendVars(array(
					'flag'    => 'strTitle',
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));
			}
		}
	}

	/**
		$this->_checkValueDetail($arrValue);
	 */
	protected function _checkValueDetail($arrValue)
	{
		global $varsRequest;

		$array = $varsRequest['query']['jsonValue']['vars']['ArrAuthority'];

		foreach ($array as $key => $value) {
			$arrValue['arr'][$key] = ($value)? 1 : 0;
		}
		if ($arrValue['arr']['flagAllInsert']) {
			$arrValue['arr']['flagMyInsert'] = 1;
		}
		if ($arrValue['arr']['flagAllDelete']) {
			$arrValue['arr']['flagMyDelete'] = 1;
		}
		if ($arrValue['arr']['flagAllUpdate']) {
			$arrValue['arr']['flagMyUpdate'] = 1;
		}
		if ($arrValue['arr']['flagAllOutput']) {
			$arrValue['arr']['flagMyOutput'] = 1;
		}
		if (!$arrValue['arr']['flagAllSelect']) {
			$arrValue['arr']['flagAllInsert'] = 0;
			$arrValue['arr']['flagAllDelete'] = 0;
			$arrValue['arr']['flagAllUpdate'] = 0;
			$arrValue['arr']['flagAllOutput'] = 0;
		}
		$arrValue['arr']['flagMySelect'] = 1;

		return $arrValue;
	}

	/**
		$this->_updateDbValue(array(
			'arr'      => array(),
			'flagEdit' => 0,
		));
	 */
	protected function _updateDbValue($arr)
	{
		global $classEscape;

		$tm = TIMESTAMP;
		$stampRegister = $tm;
		$stampUpdate = $tm;

		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arr['arr']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));
		$strTitle = $arr['arr']['strTitle'];

		$flagMySelect = $arr['arr']['flagMySelect'];
		$flagMyInsert = $arr['arr']['flagMyInsert'];
		$flagMyDelete = $arr['arr']['flagMyDelete'];
		$flagMyUpdate = $arr['arr']['flagMyUpdate'];
		$flagMyOutput = $arr['arr']['flagMyOutput'];

		$flagAllSelect = $arr['arr']['flagAllSelect'];
		$flagAllInsert = $arr['arr']['flagAllInsert'];
		$flagAllDelete = $arr['arr']['flagAllDelete'];
		$flagAllUpdate = $arr['arr']['flagAllUpdate'];
		$flagAllOutput = $arr['arr']['flagAllOutput'];

		if ($arr['flagEdit']) {
			$data = array(
				'arrColumn' => array('strTitle', 'flagMySelect', 'flagMyInsert', 'flagMyDelete', 'flagMyUpdate', 'flagMyOutput', 'flagAllSelect', 'flagAllInsert', 'flagAllDelete', 'flagAllUpdate', 'flagAllOutput', 'arrSpaceStrTag'),
				'arrValue' => array($strTitle, $flagMySelect, $flagMyInsert, $flagMyDelete, $flagMyUpdate, $flagMyOutput, $flagAllSelect, $flagAllInsert, $flagAllDelete, $flagAllUpdate, $flagAllOutput, $arrSpaceStrTag),
			);

		} else {
			$data = array(
				'arrColumn' => array('stampRegister', 'stampUpdate', 'strTitle', 'flagMySelect', 'flagMyInsert', 'flagMyDelete', 'flagMyUpdate', 'flagMyOutput', 'flagAllSelect', 'flagAllInsert', 'flagAllDelete', 'flagAllUpdate', 'flagAllOutput', 'arrSpaceStrTag'),
				'arrValue' => array($stampRegister, $stampUpdate, $strTitle, $flagMySelect, $flagMyInsert, $flagMyDelete, $flagMyUpdate, $flagMyOutput, $flagAllSelect, $flagAllInsert, $flagAllDelete, $flagAllUpdate, $flagAllOutput, $arrSpaceStrTag),
			);
		}

		return $data;
	}

	/**
	 *
	 */
	protected function _iniDetailEdit()
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $classPluginAccountingInit;

		global $varsAccount;
		global $varsPluginAccountingAuthority;
		global $varsRequest;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsTarget['vars']['varsDetail'] = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget['vars']['varsDetail']
		));

		$this->_checkStrTitle(array(
			'strTitle' => $arrValue['arr']['strTitle'],
			'idTarget' => $varsRequest['query']['jsonValue']['idTarget'],
		));

		$arrValue = $this->_checkValueDetail($arrValue);

		$arrSql = $this->_updateDbValue(array(
			'arr'      => $arrValue['arr'],
			'flagEdit' => 1,
		));

		if (!$varsPluginAccountingAuthority[$varsRequest['query']['jsonValue']['idTarget']]) {
			$this->_sendOldError();
		}

		try {
			$dbh->beginTransaction();

			$this->_updateAccountAuthority(array(
				'arrId'       => array($varsRequest['query']['jsonValue']['idTarget']),
				'flagAllPast' => $varsPluginAccountingAuthority[$varsRequest['query']['jsonValue']['idTarget']]['flagAllUpdate'],
				'flagAllNew'  => $arrValue['arr']['flagAllUpdate'],
			));

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingAuthority',
				'arrColumn' => $arrSql['arrColumn'],
				'arrWhere'  => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'id',
						'flagCondition' => 'eq',
						'value'         => $varsRequest['query']['jsonValue']['idTarget'],
					),
				),
				'arrValue'  => $arrSql['arrValue'],
			));

			$this->_updateDbPreferenceStamp(array('strColumn' => 'authority'));


			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$classPluginAccountingInit->updateInitAccount();
		$classPluginAccountingInit->updateInitAccounts();
		$classPluginAccountingInit->updateInitAccountsEntity();
		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$this->_iniSearchDetail();
	}



}
