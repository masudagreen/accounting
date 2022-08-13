<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_AccountEntityChoicePermit extends Code_Else_Plugin_Accounting_AccountEntity
{
	protected $_childSelf = array(

	);

	protected $_extSelf = array(
		'pathTplJs'  => 'else/plugin/accounting/js/accountEntityChoicePermit.js',
		'pathVarsJs' => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/accountEntityChoicePermit.php',
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
	public function getDBAuthority($arr)
	{
		global $classCheck;
		global $varsPluginAccountingAccount;

		$flagAuthority = $classCheck->checkModuleAuthority(array(
			'idModule' => 'accounting',
		));

		$strIdAuthority = $this->_getStrIdAuthority();

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$arrCommaIdEntity = '%,' . $idEntity . ',%';
		$flagAdmin = 1;

		$array = array(
			'strSql'   => 'idEntity = ? && (flagAdmin = ? || (arrCommaIdEntity like ? && idAuthority regexp ?))',
			'arrValue' => array($idEntity, $flagAdmin, $arrCommaIdEntity, $strIdAuthority),
		);

		if ($flagAuthority) {
			return $array;
		}

		return 0;

	}

	/**
	 *
	 */
	protected function _getStrIdAuthority()
	{
		global $varsPluginAccountingAuthority;

		$array = $varsPluginAccountingAuthority;
		$arrayIdAuthority = array();
		foreach ($array as $key => $value) {
			if ($value['flagAllSelect'] && $value['flagAllUpdate']) {
				$arrayIdAuthority[] = $value['id'];
			}
		}
		$strIdAuthority = '';
		if ($arrayIdAuthority) {
			$strIdAuthority = join('|', $arrayIdAuthority);
		}

		return $strIdAuthority;
	}


	/**
	 *
	 */
	protected function _updateSearch($arr)
	{
		global $varsAccounts;
		global $varsAccount;

		$vars = $arr['vars'];
		$rows = $arr['rows'];

		$array = $rows['arrRows'];
		$arrayNew = array();
		$num = 0;
		foreach ($array as $key => $value) {
			$var = $vars['portal']['varsList']['templateDetail'];
			$var['id'] = $value['id'];
			$var['vars']['idTarget'] = $value['idAccount'];
			$var['strTitle'] = $varsAccounts[$value['idAccount']]['strCodeName'];

			if ($varsAccount['id'] == $value['idAccount']) {
				$var['strClassFont'] = $vars['varsItem']['strClassAttention'];
			}

			$arrayNew[$num] = $var;
			$num++;
		}

		$vars['portal']['varsList']['varsPage']['varsStatus']['numRows'] = $rows['numRows'];
		$vars['portal']['varsList']['varsDetail'] = $arrayNew;

		return $vars;
	}


}
