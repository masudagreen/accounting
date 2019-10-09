<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_Log_2012_Public extends Code_Else_Plugin_Accounting_Jpn_Log
{
	protected $_extSelf = array(
		'idPreference'   => 'logWindow',
		'idLedger'       => 'ledgerWindow',
		'idFile'         => 'fileWindow',
		'idCash'         => 'cashWindow',
		'idBanks'        => 'banksWindow',
		'idFixedAssets'  => 'fixedAssetsWindow',
		'pathTplJs'      => 'else/plugin/accounting/js/jpn/log.js',
		'pathVarsJs'     => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/log.php',
		'pathVarsJournal'=> 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/2012/public/dictionary.php',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 0,
			'flagAuthority' => 'select',
			'idTarget'      => $this->_extSelf['idPreference'],
		));

		if (!$flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}

		$this->_checkEntity();

		if ($varsRequest['query']['child']) {
			$this->_checkCorporationClass(array('flagChild' => 1));

		} else {
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
		}

		exit;
	}
}
