<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_API extends Code_Else_Plugin_Accounting_Accounting
{
	/**
	 *
	 */
	public function run()
	{
		if (FLAG_TEST) {
			var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
		}
		exit;
	}

	/*
	 *
	 * */
	public function allot()
	{
		global $varsRequest;

		$this->_setInitNation();
		$this->_setInitLang();

		$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/' . PLUGIN_ACCOUNTING_STR_NATION . '/' . ucwords(PLUGIN_ACCOUNTING_STR_NATION) . ".php";
		require_once($path);

		$str = 'API';
		$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/' . PLUGIN_ACCOUNTING_STR_NATION . '/' . $str . ".php";
		$strClass = 'Code_Else_Plugin_Accounting_' . ucwords(PLUGIN_ACCOUNTING_STR_NATION) . '_' . $str;
		require_once($path);
		$classCall = new $strClass;
		$classCall->allot();
	}

	protected function _setInitNation()
	{
		global $varsRequest;

		$strNation = $varsRequest['query']['api']['nation'];
		if (is_null($strNation)) {
			$strNation = $this->_self['strIniNation'];
		}

		define('PLUGIN_ACCOUNTING_STR_NATION', $strNation);
	}

	/**
	 */
	protected function _setInitLang()
	{
		global $varsRequest;

		$strLang = $varsRequest['query']['api']['lang'];
		if (is_null($strLang)) {
			$strLang = $this->_self['strIniLang'];
		}

		define('PLUGIN_ACCOUNTING_STR_LANG', $strLang);
	}
}
