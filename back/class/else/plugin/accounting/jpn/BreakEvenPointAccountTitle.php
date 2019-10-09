<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_BreakEvenPointAccountTitle extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference'  => 'breakEvenPointWindow',
		'pathTplJs'     => 'else/plugin/accounting/js/jpn/breakEvenPointAccountTitle.js',
		'pathVarsJs'    => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/breakEvenPointAccountTitle.php',
		'varsDefaultPL' => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/breakEvenPointPL.php',
		'varsDefaultCR' => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/breakEvenPointCR.php',
	);

	/**
	 *
	 */
	public function run()
	{
		$this->_checkCorporationClass(array('flagChild' => 0));

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
			$strChild = ucwords($varsRequest['query']['child']);
			$strExt = ucwords($varsRequest['query']['ext']);
			$str = $strExt . $strChild;

			$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/jpn/' . $str . ".php";
			if (!file_exists($path)) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
				}
				exit;
			}
			require_once($path);
			$strClass = 'Code_Else_Plugin_Accounting_Jpn_' . $str;
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

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

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

		$varsDepartment = $this->_getVarsDepartment(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsSave = $this->_getVarsSave(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idDepartment'    => $arr['varsFlag']['idDepartment'],
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsFlagType = $this->_getVarsFlagType(array(
			'vars' => &$arr['vars'],
		));

		$array = $arr['vars']['varsItem']['varsFS'];
		foreach ($array as $key => $value) {
			if (!$varsEntityNation['flagCR'] && $key == 'CR') {
				continue;
			}
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
			'varsFS'           => $varsFS,
			'varsEntityNation' => $varsEntityNation,
			'varsDepartment'   => $varsDepartment,
			'varsSave'         => $varsSave,
			'varsFS'           => $varsFS,
			'varsFlagType'     => $varsFlagType,
		);

		return $data;

	}


	/**

	 */
	protected function _getVarsFlagType($arr)
	{
		$array = $arr['vars']['portal']['varsDetail']['templateDetail'];
		$arrStrTitle = array();
		foreach ($array as $key => $value) {
			if ($value['id'] == 'FlagType') {
				$arrayOption = $value['arrayOption'];
				foreach ($arrayOption as $keyOption => $valueOption) {
					$arrStrTitle[$valueOption['value']]['strTitle'] = $valueOption['strTitle'];
				}
				break;
			}
		}

		$data = array(
			'arrStrTitle'  => $arrStrTitle,
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
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent']
		))
	 */
	protected function _getVarsSave($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingBreakEvenPoint' . $strNation,
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idEntity',
					'flagCondition' => 'eq',
					'value'         => $varsPluginAccountingAccount['idEntityCurrent'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'numFiscalPeriod',
					'flagCondition' => 'eq',
					'value'         => $arr['numFiscalPeriod'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idDepartment',
					'flagCondition' => 'eq',
					'value'         => $arr['idDepartment'],
				),
			),
		));

		return $rows['arrRows'][0];
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
		if (preg_match("/^(done)$/", $flag)) {
			$arr['vars']['portal']['varsDetail']['varsBtn'] = array();
			$arr['vars']['portal']['varsDetail']['view']['varsEdit'] = array();
		}

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'])) {
			$arr['vars']['portal']['varsDetail']['view']['varsEdit']['flagEditUse'] = 0;
		}

		$str = 'jsonJgaapAccountTitle'. $arr['varsFlag']['flagFS'];
		$varsDetail = $this->_updateVarsList(array(
			'vars'          => $arr['vars'],
			'varsFS'        => $arr['varsItem']['varsFS'][$str],
			'varsItem'      => $arr['varsItem'],
			'flagEditNone'  => (preg_match("/^(done)$/", $flag))? 1 : 0,
			'varsAuthority' => array(
				'flagUpdate' => ($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'])? 1 : 0,
			),
			'varsFlag'      => $arr['varsFlag'],
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
		(array(
			'vars'             => $vars,
			'varsItem'         => $arr['varsItem'],
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _updateVarsNavi($arr)
	{
		$vars = &$arr['vars'];
		$varsItem = &$arr['varsItem'];

		$arrayNew = array();
		$array = &$vars['portal']['varsNavi']['templateDetail'];
		foreach ($array as $key => $value) {

			$method = '_updateVarsNavi' . $value['id'];
			$value = $this->$method(array(
				'vars'      => $value,
				'varsItem'  => $varsItem,
			));

			$arrayNew[] = $value;
		}

		$vars['portal']['varsNavi']['templateDetail'] = $arrayNew;

		return $vars['portal']['varsNavi']['templateDetail'];
	}

	/**
		(array(
			'value'    => $value,
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		))
	 */
	protected function _updateVarsNaviIdDepartment($arr)
	{
		if (!$arr['varsItem']['varsDepartment']['arrSelectTag']) {

			return $arr['vars'];
		}
		$arrSelectTag = $arr['varsItem']['varsDepartment']['arrSelectTag'];
		array_unshift($arrSelectTag, $arr['vars']['varsTmpl']['varsNone']);
		$arr['vars']['arrayOption'] = $arrSelectTag;

		return $arr['vars'];
	}

	/**
		(array(
			'vars'             => $value,
			'varsItem'         => $varsItem,
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _updateVarsNaviFlagFS($arr)
	{
		$vars = $arr['vars'];
		$varsEntityNation = $arr['varsItem']['varsEntityNation'];

		$arrayNew = array();
		$array = $vars['arrayOption'];
		foreach ($array as $key => $value) {
			if ($value['value'] == 'CR' && !$varsEntityNation['flagCR']) {

			} else {
				$arrayNew[] = $value;
			}
		}
		$arr['vars']['arrayOption'] = $arrayNew;
		$arr['vars']['numSize'] = count($arrayNew);

		return $arr['vars'];
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
				'flagType' => '',
			);
			$array[$key]['vars']['varsAuthority'] = $arr['varsAuthority'];
			$array[$key]['vars']['flagEditNone'] = $arr['flagEditNone'];
			$idTarget = $value['vars']['idTarget'];
			$arrStrTitle = $arr['varsItem']['varsFlagType']['arrStrTitle'];
			if (!is_null($value['vars']['varsValue']) && $value['flagBtnUse']) {
				$flagType = $value['varsType']['flagType'];
				$array[$key]['varsColumnDetail']['flagType'] = $arrStrTitle[$flagType]['strTitle'];
				if (!($value['varsType']['flagType'] == 'sales'
					|| $value['varsType']['flagType'] == 'variable'
					|| $value['varsType']['flagType'] == 'fixed'
				)) {
					$array[$key]['varsColumnDetail']['flagType'] = $arrStrTitle['none']['strTitle'];
					$value['varsType']['flagType'] = 'none';
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

		$varsFlag = array(
			'flagFS'       => $varsRequest['query']['jsonValue']['vars']['FlagFS'],
			'idDepartment' => $varsRequest['query']['jsonValue']['vars']['IdDepartment'],
		);

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$vars = $this->_updateVars(array(
			'varsFlag' => $varsFlag,
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$this->_checkValueDetail(array(
			'varsDetail' => $vars['portal']['varsNavi']['varsDetail'],
			'varsFlag'   => $varsFlag,
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
			'varsDetail'       => $varsDetail,
			'FlagFiscalPeriod' => $flagFiscalPeriod,
			'FlagUnit'         => $flagUnit,
		))
	 */
	protected function _checkValueDetail($arr)
	{
		global $varsRequest;
		global $classEscape;

		$flag = 0;
		$array = $arr['varsDetail'];
		foreach ($array as $key => $value) {
			$idTarget = $classEscape->toLower(array('str' => $value['id']));
			$arrayOption = $value['arrayOption'];
			foreach ($arrayOption as $keyOption => $valueOption) {
				if ($valueOption['value'] == $arr['varsFlag'][$idTarget]) {
					$flag = 1;
				}
			}
			if (!$flag) {
				if ($arr['flagOutput']) {
					$this->_send404Output();
				} else {
					$this->_sendOld();
				}
			}
			$flag = 0;
		}
	}

	/**
	 *
	 */
	protected function _iniListReload()
	{
		$this->_setSearch();
	}


}
