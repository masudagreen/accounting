<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_AccountTitle_2012_Public extends Code_Else_Plugin_Accounting_Jpn_AccountTitle
{
	protected $_extSelf = array(
		'idPreference' => 'accountTitleWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/2012/public/accountTitle.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/2012/public/accountTitle.php',
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
			$str = ucwords($varsRequest['query']['ext']);
			$strChild = ucwords($varsRequest['query']['child']);
			if ($strChild == 'EditorTemp') {
				$strChild = 'Editor';
			}
			$str = $str . $strChild;

			$path = PATH_BACK_CLASS_ELSE_PLUGIN
				. 'accounting'
				. '/' . PLUGIN_ACCOUNTING_STR_NATION
				. '/' . $str . '.php';

			require_once($path);

			$path = PATH_BACK_CLASS_ELSE_PLUGIN
				. 'accounting'
				. '/' . PLUGIN_ACCOUNTING_STR_NATION
				. '/' . PLUGIN_ACCOUNTING_NUM_YEAR_SHEET
				. '/' . PLUGIN_ACCOUNTING_FLAG_CORPORATION
				. '/' . $str . '.php';

			$strClass = 'Code_Else_Plugin_Accounting'
				. '_' . ucwords(PLUGIN_ACCOUNTING_STR_NATION)
				. '_' . $str
				. '_' . PLUGIN_ACCOUNTING_NUM_YEAR_SHEET
				. '_' . ucwords(PLUGIN_ACCOUNTING_FLAG_CORPORATION);

			require_once($path);
			$classCall = new $strClass;
			$classCall->run();
			exit;

		} else {
			if ($varsRequest['query']['func']) {
				$method = '_ini' . $varsRequest['query']['func'];
				if (method_exists($this, $method)) {
					if ($method == '_iniJs') {
						$this->$method();

					} else {
						$this->$method();
					}

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
			'flag' => '',
			'vars' => $vars,
		))
	 */
	protected function _getVarsItem($arr)
	{
		global $classDb;
		global $varsAccount;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsConsumptionTax = $this->_getVarsConsumptionTax(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsTax = $this->_getVarsItemTax(array(
			'varsEntityNation'   => $varsEntityNation,
			'varsConsumptionTax' => $varsConsumptionTax,
			'numFiscalPeriod'    => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		//select form view prepare
		$str = 'jsonJgaapFS' . $arr['flag'];
		$varsFS[$str] = $this->_setTreeId(array(
			'idParent' => '',
			'vars'     => $varsFS[$str],
		));

		//select form view
		$varsJgaapFS = $this->_getVarsItemJgaapFS(array(
			'arrStrTitle'  => array(),
			'arrSelectTag' => array(),
			'vars'         => $varsFS[$str],
			'varsItem'     => $arr['vars']['varsItem'],
		));

		$arrayFSList = $this->_getFSList(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsFSItem = $this->_getVarsFSItem();

		$varsFSRows = $this->_getVarsFSRows(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$data = array(
			'varsFS'             => $varsFS,
			'varsFSRows'         => $varsFSRows,
			'varsTax'            => $varsTax,
			'varsConsumptionTax' => $varsConsumptionTax,
			'varsJgaapFS'        => $varsJgaapFS,
			'arrayFSList'        => $arrayFSList,
			'varsEntityNation'   => $varsEntityNation,
			'varsFSItem'         => $varsFSItem,
		);

		return $data;

	}

	/**
		(array(
			'arrStrTitle'  => array(),
			'vars'         => array(),
			'arrSelectTag' => array(),
		))
	 */
	protected function _getVarsItemJgaapFS($arr)
	{
		$arrSelectTag = &$arr['arrSelectTag'];
		$arrStrTitle = &$arr['arrStrTitle'];

		$array = &$arr['vars'];
		foreach ($array as $key => $value) {
			if ($value['strTitle'] == '') {
				$value['strTitle'] = $arr['varsItem']['strBlank'];
			}
			$arr['arrStrTitle'][$value['vars']['idTarget']] = $value['strTitle'];

			$arrLevel = preg_split("/-/", $value['id']);
			$num = count($arrLevel) - 1;
			$arrLevel = array();
			for ($i = 0 ; $i < $num; $i++) {
				$arrLevel[$i] = '';
			}
			$str =  ' ' . join('.', $arrLevel) . ' ';
			$strTitle = $str . $value['strTitle'];

			if (is_null($value['vars']['flagUse'])) {
				$arr['arrSelectTag'][] = array(
					'strTitle'     => $strTitle,
					'value'        => '',
					'flagDisabled' => 1,
				);

			} else {
				$arr['arrSelectTag'][] = array(
					'strTitle'     => $strTitle,
					'value'        => $value['vars']['idTarget'],
				);
			}

			if ($value['child']) {
				$data = $this->_getVarsItemJgaapFS(array(
					'vars'          => $array[$key]['child'],
					'arrSelectTag'  => $arr['arrSelectTag'],
					'arrStrTitle'   => $arr['arrStrTitle'],
					'varsItem'      => $arr['varsItem'],
					'idParent'      => $value['vars']['idTarget'],
				));
				$array[$key]['child'] = $data['vars'];
				$arrSelectTag =  $data['arrSelectTag'];
				$arrStrTitle =  $data['arrStrTitle'];
			}
		}

		return $arr;
	}
}
