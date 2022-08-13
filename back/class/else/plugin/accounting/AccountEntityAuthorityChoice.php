<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_AccountEntityAuthorityChoice extends Code_Else_Plugin_Accounting_AccountEntityAuthority
{
	protected $_childSelf = array(

	);

	protected $_extSelf = array(
		'pathTplJs'  => 'else/plugin/accounting/js/accountEntityAuthorityChoice.js',
		'pathVarsJs' => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/accountEntityAuthorityChoice.php',
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
	protected function _updateSearch($arr)
	{
		global $varsAccounts;

		$vars = $arr['vars'];
		$rows = $arr['rows'];

		$array = $rows['arrRows'];
		$arrayNew = array();
		$num = 0;
		foreach ($array as $key => $value) {
			$var = $vars['portal']['varsList']['templateDetail'];
			$var['id'] = $value['id'];
			$var['vars']['idTarget'] = $value['id'];
			$var['strTitle'] = $value['strTitle'];
			$arrayNew[$num] = $var;
			$num++;
		}

		$vars['portal']['varsList']['varsPage']['varsStatus']['numRows'] = $rows['numRows'];
		$vars['portal']['varsList']['varsDetail'] = $arrayNew;

		return $vars;
	}
}
