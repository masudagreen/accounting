<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_FileAccountEntityEditor extends Code_Else_Plugin_Accounting_FileAccountEntity
{
	protected $_childSelf = array(
		'pathTplJs'   => 'else/plugin/accounting/js/fileAccountEntityEditor.js',
		'pathVarsJs'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/fileAccountEntityEditor.php',
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
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
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

	 */
	protected function _iniDetailEdit()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $classEscape;
		global $classPluginAccountingInit;

		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccounts;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingAuthority;
		global $varsPluginAccountingAccountsEntity;
		global $varsRequest;
		global $varsAccounts;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsTarget = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget
		));

		$arrValue = $this->_checkValueDetail($arrValue);

		$idAccount = $varsRequest['query']['jsonValue']['idTarget'];
		$idEntityCurrent = $varsPluginAccountingAccount['idEntityCurrent'];

		$strMail = $arrValue['arr']['strMail'];

		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrValue['arr']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));

		$arrDbColumn = array('strMailFile');
		$arrDbValue = array($strMail);

		try {
			$dbh->beginTransaction();

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingAccountEntity',
				'arrColumn' => $arrDbColumn,
				'flagAnd'   => 1,
				'arrWhere'  => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idAccount',
						'flagCondition' => 'eq',
						'value'         => $idAccount,
					),
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idEntity',
						'flagCondition' => 'eq',
						'value'         => $varsPluginAccountingAccount['idEntityCurrent'],
					),
				),
				'arrValue'  => $arrDbValue,
			));

			$arrDbColumn = array('stampUpdate');
			$arrDbValue = array(TIMESTAMP);

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingAccount',
				'arrColumn' => $arrDbColumn,
				'flagAnd'   => 1,
				'arrWhere'  => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idAccount',
						'flagCondition' => 'eq',
						'value'         => $idAccount,
					),
				),
				'arrValue'  => $arrDbValue,
			));

			$this->updateDbAccountArrSpaceStrTag(array(
				'idTarget'       => $idAccount,
				'arrSpaceStrTag' => $arrSpaceStrTag,
			));

			$this->_updateDbPreferenceStamp(array('strColumn' => 'account'));

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


	/**
		$this->_checkValueDetail($arrValue);
	 */
	protected function _checkValueDetail($arrValue)
	{
		global $varsPluginAccountingAccount;
		global $varsAccounts;
		global $varsRequest;

		$idAccount = $varsRequest['query']['jsonValue']['idTarget'];
		if (!$varsAccounts[$idAccount]) {
			$this->_sendOldError();
		}

		$arrValue['arr']['strMail'] = strtolower($arrValue['arr']['strMail']);

		$flag = $this->_checkStrMail(array(
			'strMail'   => $arrValue['arr']['strMail'],
			'idAccount' => $idAccount
		));
		if ($flag) {
			$this->sendValue(array(
				'flag'    => 'strMail',
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}

		return $arrValue;
	}

	/**
		(array(
		))
	 */
	protected function _checkStrMail($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingAccountEntity',
			'arrLimit' => array(),
			'arrOrder'  => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
				array(
					'flagType'      => '',
					'strColumn'     => 'idEntity',
					'flagCondition' => 'eq',
					'value'         => $varsPluginAccountingAccount['idEntityCurrent'],
				),
				array(
					'flagType'      => '',
					'strColumn'     => 'strMailFile',
					'flagCondition' => 'eq',
					'value'         => $arr['strMail'],
				),
				array(
					'flagType'      => '',
					'strColumn'     => 'idAccount',
					'flagCondition' => 'ne',
					'value'         => $arr['idAccount'],
				),
			),
		));

		if ($rows['numRows']) {
			return 1;
		}

		return 0;
	}




}
