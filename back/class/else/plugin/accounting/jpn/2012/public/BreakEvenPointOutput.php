<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_BreakEvenPointOutput_2012_Public extends Code_Else_Plugin_Accounting_Jpn_BreakEvenPointOutput
{
	protected $_extSelf = array(
		'idPreference'   => 'breakEvenPointWindow',
		'pathTplJs'      => 'else/plugin/accounting/js/jpn/breakEvenPoint.js',
		'pathVarsJs'     => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/breakEvenPoint.php',
		'varsDefaultPL'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/2012/public/breakEvenPointPL.php',
		'varsDefaultCR'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/2012/public/breakEvenPointCR.php',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;

		$this->_checkEntity();

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

}
