<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_LogEditor_2012_Public extends Code_Else_Plugin_Accounting_Jpn_LogEditor
{

	//-----------------------------------------------------------
	// Code_Else_Plugin_Accounting_Jpn_Log overwrite _2012_Public
	//-----------------------------------------------------------

	protected $_extSelf = array(
		'idPreference'   => 'logWindow',
		'idLedger'       => 'ledgerWindow',
		'idFile'         => 'fileWindow',
		'idBanks'        => 'banksWindow',
		'idCash'         => 'cashWindow',
		'idFixedAssets'  => 'fixedAssetsWindow',
		'pathTplJs'      => 'else/plugin/accounting/js/jpn/log.js',
		'pathVarsJs'     => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/log.php',
		'pathVarsJournal'=> 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/2012/public/dictionary.php',
	);


}
