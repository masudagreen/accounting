<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_AccountTitleFS_2012_Public extends Code_Else_Plugin_Accounting_Jpn_AccountTitleFS
{
	protected $_extSelf = array(
		'idPreference' => 'accountTitleFSWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/2012/public/accountTitleFS.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/2012/public/accountTitleFS.php',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;
		global $classCheck;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 1,
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

	/**
		(array(
			'vars'             => array(),
			'varsFS'           => array(),
			'varsItem'         => array(),
		))
	 */
	protected function _updateVarsList($arr)
	{
		$array = &$arr['varsFS'];

		foreach ($array as $key => $value) {
			$array[$key]['id'] = '';
			$array[$key]['flagBoldNow'] = 0;
			$array[$key]['strClassFont'] = '';
			$array[$key]['strClassBg'] = '';
			$array[$key]['vars']['idTarget'] = $value['vars']['idTarget'];
			$array[$key]['varsColumnDetail'] = array(
				'flagDebit' => '',
				'flagUse' => '',
			);
/*unique start*/
			$array[$key]['varsValue']['strTitle'] = $array[$key]['strTitle'];
			if (!$array[$key]['strTitle']) {
				$array[$key]['strTitle'] = $arr['vars']['varsItem']['strBlank'];
			}
//unique end
			if (!is_null($value['vars']['flagUse'])) {

				//flagDebit
				if ($value['vars']['flagDebit']) {
					$array[$key]['varsColumnDetail']['flagDebit'] = $arr['vars']['varsItem']['strDebit'];

				} else {
					$array[$key]['varsColumnDetail']['flagDebit'] = $arr['vars']['varsItem']['strCredit'];
				}

				//flagUse
				if ((int) $value['vars']['flagUse']) {
					$array[$key]['varsColumnDetail']['flagUse'] = $arr['vars']['varsItem']['strShow'];

				} else {
					$array[$key]['varsColumnDetail']['flagUse'] = $arr['vars']['varsItem']['strHide'];
					$array[$key]['strClassFont'] = $arr['vars']['varsItem']['strClassNone'];
				}

			}
			if ($value['child']) {
				$array[$key]['child'] = $this->_updateVarsList(array(
					'vars'     => $arr['vars'],
					'varsFS'   => $array[$key]['child'],
				));
			}
		}

		return $array;
	}
}
