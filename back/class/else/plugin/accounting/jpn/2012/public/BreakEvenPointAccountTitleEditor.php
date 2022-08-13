<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_BreakEvenPointAccountTitleEditor_2012_Public extends Code_Else_Plugin_Accounting_Jpn_BreakEvenPointAccountTitleEditor
{
	//-----------------------------------------------------------
	// Code_Else_Plugin_Accounting_Jpn_BreakEvenPointAccountTitle overwrite _2012_Public
	//-----------------------------------------------------------

	protected $_extSelf = array(
		'idPreference'   => 'breakEvenPointWindow',
		'pathTplJs'      => 'else/plugin/accounting/js/jpn/breakEvenPointAccountTitle.js',
		'pathVarsJs'     => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/breakEvenPointAccountTitle.php',
		'varsDefaultPL'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/2012/public/breakEvenPointPL.php',
		'varsDefaultCR'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/2012/public/breakEvenPointCR.php',
	);
}
