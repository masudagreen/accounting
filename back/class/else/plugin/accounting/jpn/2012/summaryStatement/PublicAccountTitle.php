<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_2012_SummaryStatement_PublicAccountTitle extends Code_Else_Plugin_Accounting_Jpn_SummaryStatement
{
	protected $_extSelf = array(
		'idPreference' => 'summaryStatementWindow',
		'flagReport' => '2012',
		'flagDetail' => 'public',
		'pathTplJs' => 'else/plugin/accounting/js/jpn/2012/summaryStatement/publicAccountTitle.js',
		'pathVarsJs'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/2012/summaryStatement/publicAccountTitle.php',
		'pathItem'     => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/2012/summaryStatement/publicItem.php',
		'varsDefaultPL'  => 'back/tpl/templates/else/plugin/accounting/dat/2012/summaryStatement/publicPL.php',
		'varsDefaultCR'  => 'back/tpl/templates/else/plugin/accounting/dat/2012/summaryStatement/publicCR.php',
		'varsDefaultBS'  => 'back/tpl/templates/else/plugin/accounting/dat/2012/summaryStatement/publicBS.php',
		'varsSave' => array(),
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


		$flag = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(tempNext)$/", $flag)) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}

		if ($varsRequest['query']['child']) {
			$strChild = ucwords($varsRequest['query']['child']);
			$strDetail = ucwords($this->_extSelf['flagDetail']) . 'AccountTitle';
			$str = $strDetail . $strChild;

			$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/jpn/' . $this->_extSelf['flagReport'] . '/summaryStatement/' . $str . ".php";
			if (!file_exists($path)) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
				}
				exit;
			}
			require_once($path);
			$strClass = 'Code_Else_Plugin_Accounting_Jpn_' . $this->_extSelf['flagReport'] . '_SummaryStatement_' . $str;
			$classCall = new $strClass;
			$classCall->run();

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
	 *
	 */
	protected function _iniJs()
	{
		global $classSmarty;
		global $varsRequest;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsFlag' => $vars['varsFlag'],
		));

		$vars = $this->_updateVars(array(
			'varsFlag' => $vars['varsFlag'],
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$json = json_encode($vars);
		$classSmarty->assign('varsLoad', $json);
		$classSmarty->assign('numNews', $this->getNumNews());
		$contents = $classSmarty->fetch($this->_extSelf['pathTplJs']);

		$this->sendJs(array(
			'data' => $contents,
		));

	}


	/**
		(array(
			'flag' => '',
			'vars' => $vars,
		))
	 */
	protected function _getVarsItem($arr)
	{
		global $varsPluginAccountingAccount;

		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsSave = $this->_getVarsSave(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagReport'      => $this->_extSelf['flagReport'],
			'flagDetail'      => $this->_extSelf['flagDetail'],
		));

		$arrayFSList = $this->_getFSList(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsCommon = $this->getVars(array(
			'path' => $this->_extSelf['pathItem'],
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$array = $arrayFSList;
		foreach ($array as $key => $value) {
			if ($arr['varsFlag']['flagFS'] != $key) {
				continue;
			}
			$varsDefault = $this->getVars(array(
				'path' => $this->_extSelf['varsDefault' . $key],
			));
			$str = 'jsonJgaapAccountTitle'. $key;
			$varsFS[$str] = $this->_loopVarsValue(array(
				'varsFS'      => $varsFS[$str],
				'varsSave'    => ($varsSave[$str])? $varsSave[$str] : array(),
				'varsDefault' => $varsDefault,
			));
		}

		$data = array(
			'arrayFSList'      => $arrayFSList,
			'varsCommon'       => $varsCommon,
			'varsFS'           => $varsFS,
			'varsEntityNation' => $varsEntityNation,
			'varsSave'         => $varsSave,
			'varsFS'           => $varsFS,
		);

		return $data;

	}


	/**
		(array(
			'varsFlag'     => $arr['varsFlag'],
			'varsValue'    => &$varsValue,
			'varsFS'       => $varsFS[$str],
			'varsFSValue'  => $varsFSValue[$str],
			'flagFS'       => $value,
			'varsSave'     => ($varsSave[$str])? $varsSave[$str] : array(),
			'varsDefault'  => $varsDefault,
		));
	 */
	protected function _loopVarsValue($arr)
	{
		$array = &$arr['varsFS'];
		foreach ($array as $key => $value) {
			if (!is_null($value['vars']['varsValue']) && $value['flagBtnUse']) {
				if (!($value['vars']['flagCalc'] == 'sum' || $value['vars']['flagCalc'] == 'net')) {
					$idTarget = $value['vars']['idTarget'];
					$varsType = array();
					if ($arr['varsDefault'][$idTarget]) {
						$varsType = $arr['varsDefault'][$idTarget];
					}
					if ($arr['varsSave'][$idTarget]) {
						$varsType = $arr['varsSave'][$idTarget];
					}
					$array[$key]['varsType'] = $varsType;
				}
			}
			if ($value['child']) {
				$array[$key]['child'] = $this->_loopVarsValue(array(
					'varsFS'      => $array[$key]['child'],
					'varsSave'    => $arr['varsSave'],
					'varsDefault' => $arr['varsDefault'],
				));
			}
		}

		return $array;
	}

	/**
		(array(
			'varsFlag' => $vars['varsFlag'],
			'vars'     => $vars,
			'varsItem' => $varsItem,
		))
	 */
	protected function _updateVars($arr)
	{
		global $classHtml;

		global $varsAccount;
		global $varsPluginAccountingAccount;

		$flag = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done|tempNext)$/", $flag)) {
			$arr['vars']['portal']['varsDetail']['varsBtn'] = array();
			$arr['vars']['portal']['varsDetail']['view']['varsEdit'] = array();
		}

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'])) {
			$arr['vars']['portal']['varsDetail']['view']['varsEdit']['flagEditUse'] = 0;
		}

		$arr['vars']['portal']['varsNavi']['templateDetail'] = $this->_updateVarsNaviFS((array(
			'vars'     => $arr['vars'],
			'varsItem' => $arr['varsItem'],
		)));

		$arr['vars']['portal']['varsDetail']['templateDetail'] = $this->_updateVarsTemplateDetail((array(
			'vars'      => $arr['vars'],
			'varsItem'  => $arr['varsItem'],
		)));

		$arr['varsItem']['varsCommon']['arrStrTitle']['7'] = $this->_getVarsCommon7(array(
			'vars' => $arr['vars'],
		));

		$arr['varsItem']['varsCommon']['arrStrTitle']['17'] = $this->_getVarsCommon17(array(
			'vars' => $arr['vars'],
		));

		$str = 'jsonJgaapAccountTitle'. $arr['varsFlag']['flagFS'];
		$varsDetail = $this->_updateVarsList(array(
			'vars'          => $arr['vars'],
			'varsFS'        => $arr['varsItem']['varsFS'][$str],
			'varsItem'      => $arr['varsItem'],
			'flagEditNone'  => (preg_match("/^(done|tempNext)$/", $flag))? 1 : 0,
			'varsAuthority' => array(
				'flagUpdate' => ($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'])? 1 : 0,
			),
			'varsFlag'       => $arr['varsFlag'],
		));

		$arr['vars']['portal']['varsList']['varsDetail'] = $varsDetail;

		$varsTemp = $classHtml->allot(array(
			'strClass'    => 'TableTree',
			'flagStatus'  => 'Html',
			'numTimeZone' => $varsAccount['numTimeZone'],
			'varsDetail'  => $varsDetail,
			'varsColumn'  => $arr['vars']['portal']['varsList']['tableTree']['varsDetail']['varsColumn'],
			'varsStatus'  => $arr['vars']['portal']['varsList']['tableTree']['varsDetail']['varsStatus'],
		));
		$arr['vars']['portal']['varsList']['varsHtml'] = $varsTemp['strHtml'];

		return $arr['vars'];
	}


	/**
		$varsCommon['arrStrTitle']['7'] = $this->getVars(array(
			'path' => $this->_extSelf['pathItem'],
		));
	 */
	protected function _getVarsCommon17($arr)
	{
		$array = $arr['vars']['portal']['varsDetail']['templateDetail'];
		$arrStrTitle = array();
		foreach ($array as $key => $value) {
			if ($value['id'] == 'Flag17') {
				$arrayOption = $value['arrayOption'];
				foreach ($arrayOption as $keyOption => $valueOption) {
					$arrStrTitle[$valueOption['value']]['strTitle'] = $valueOption['strTitle'];
				}
				break;
			}
		}

		return $arrStrTitle;
	}

	/**
		$varsCommon['arrStrTitle']['7'] = $this->getVars(array(
			'path' => $this->_extSelf['pathItem'],
		));
	 */
	protected function _getVarsCommon7($arr)
	{
		$array = $arr['vars']['portal']['varsDetail']['templateDetail'];
		$arrStrTitle = array();
		foreach ($array as $key => $value) {
			if ($value['id'] == 'Flag7') {
				$arrayOption = $value['arrayOption'];
				foreach ($arrayOption as $keyOption => $valueOption) {
					$arrStrTitle[$valueOption['value']]['strTitle'] = $valueOption['strTitle'];
				}
				break;
			}
		}

		return $arrStrTitle;
	}

	/**
		(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		))
	 */
	protected function _updateVarsTemplateDetail($arr)
	{
		global $classEscape;

		$varsEntityNation = $arr['varsItem']['varsEntityNation'];
		$array = $arr['vars']['portal']['varsDetail']['templateDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'Flag7') {
				$array[$key]['arrayOption'] = $arr['varsItem']['varsCommon']['arrSelectTag']['7'];

			} elseif ($value['id'] == 'Flag17') {
				$array[$key]['arrayOption'] = $arr['varsItem']['varsCommon']['arrSelectTag']['17'];
			}
		}

		return $array;
	}

	/**
		(array(
			'vars' => array(),
			'varsEntityNation' => array(),
		))
	 */
	protected function _updateVarsNaviFS($arr)
	{
		$vars = &$arr['vars'];
		$varsEntityNation = &$arr['varsItem']['varsEntityNation'];
		$arrayNew = array();
		$array = $vars['portal']['varsNavi']['templateDetail'][0]['arrayOption'];
		foreach ($array as $key => $value) {
			if ($value['value'] == 'CR' && !(int) $varsEntityNation['flagCR']) {

			} else {
				$arrayNew[] = $value;
			}
		}

		$vars['portal']['varsNavi']['templateDetail'][0]['arrayOption'] = $arrayNew;
		$vars['portal']['varsNavi']['templateDetail'][0]['numSize'] = count($arrayNew);

		return $vars['portal']['varsNavi']['templateDetail'];

	}

	/**
		(array(
			'vars'             => $vars,
			'flagDirect'       => $arr['flagDirect'],
			'varsFS'           => $arr['varsItem']['varsFS'][$str],
			'varsItem'         => $arr['varsItem'],
			'varsEntityNation' => $arr['varsEntityNation']
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
			$array[$key]['varsFlag'] = $arr['varsFlag'];
			$array[$key]['varsColumnDetail'] = array(
				'flag7' => '',
				'flag17' => '',
			);
			$array[$key]['vars']['varsAuthority'] = $arr['varsAuthority'];
			$array[$key]['vars']['flagEditNone'] = $arr['flagEditNone'];
			$idTarget = $value['vars']['idTarget'];

			if (!is_null($value['vars']['varsValue']) && $value['flagBtnUse']) {
				$arrStrTitle = $arr['varsItem']['varsCommon']['arrStrTitle']['7'];
				$flag7 = $value['varsType']['flag7'];

				$array[$key]['varsColumnDetail']['flag7'] = $arrStrTitle[$flag7]['strTitle'];
				if (is_null($arrStrTitle[$flag7])) {
					$array[$key]['varsColumnDetail']['flag7'] = $arrStrTitle['none']['strTitle'];
					$value['varsType']['flag7'] = 'none';
				}

				$arrStrTitle = $arr['varsItem']['varsCommon']['arrStrTitle']['17'];
				$flag17 = $value['varsType']['flag17'];
				$array[$key]['varsColumnDetail']['flag17'] = $arrStrTitle[$flag17]['strTitle'];
				if (is_null($arrStrTitle[$flag17])) {
					$array[$key]['varsColumnDetail']['flag17'] = $arrStrTitle['none']['strTitle'];
					$value['varsType']['flag17'] = 'none';
				}
				if ($arr['varsFlag']['flagFS'] == 'BS') {
					$array[$key]['varsColumnDetail']['flag17'] = '-';
					$value['varsType']['flag17'] = 'none';
				}
			}

			if ($value['child']) {
				$array[$key]['child'] = $this->_updateVarsList(array(
					'varsFS'        => $array[$key]['child'],
					'vars'          => $arr['vars'],
					'varsItem'      => $arr['varsItem'],
					'varsAuthority' => $arr['varsAuthority'],
					'flagEditNone'  => $arr['flagEditNone'],
					'varsFlag'      => $arr['varsFlag'],
				));
			}
		}

		return $array;
	}

	/**

	 */
	protected function _iniNaviSearch()
	{
		$this->_setSearch();
	}

	/**

	 */
	protected function _setSearch()
	{
		global $varsRequest;
		global $varsPluginAccountingPreference;

		if (FLAG_CHECK_UPDATE) {
			$str = 'acountTitle';
			if ($varsPluginAccountingPreference['jsonStampUpdate'][$str] <= $varsRequest['query']['jsonStamp']['stamp']) {
				$this->sendVars(array(
					'flag'    => 10,
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => '',
				));
			}
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$vars['varsFlag']['flagFS'] = $varsRequest['query']['jsonValue']['vars']['FlagFS'];

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsFlag' => $vars['varsFlag'],
		));

		$flagFS = $this->_checkValueFS(array(
			'vars'     => $vars,
			'varsItem' => &$varsItem,
		));

		$vars = $this->_updateVars(array(
			'varsFlag' => $vars['varsFlag'],
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$this->sendVars(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'varsDetail'     => $vars['portal']['varsList']['varsDetail'],
				'varsHtml'       => $vars['portal']['varsList']['varsHtml'],
				'varsColumn'     => $vars['portal']['varsList']['tableTree']['varsDetail']['varsColumn'],
				'templateDetail' => $vars['portal']['varsDetail']['templateDetail']
			),
		));
	}

	/**
		(array(
			'vars' => array(),
		))
	 */
	protected function _checkValueFS($arr)
	{
		global $varsRequest;
		global $varsPluginAccountingAccount;

		$flagFS = $varsRequest['query']['jsonValue']['vars']['FlagFS'];
		$array = $arr['vars']['portal']['varsNavi']['templateDetail'][0]['arrayOption'];
		$flag = 0;
		foreach ($array as $key => $value) {
			if ($value['value'] == 'CR' && !$arr['varsItem']['varsEntityNation']['flagCR']) {
				continue;
			}
			if ($value['value'] == $flagFS) {
				$flag = 1;
			}
		}
		if (!$flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}

		return $flagFS;
	}

	/**
	 *
	 */
	protected function _iniListReload()
	{
		$this->_setSearch();
	}


}
