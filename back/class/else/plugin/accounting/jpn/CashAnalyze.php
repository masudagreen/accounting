<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CashAnalyze extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference' => 'cashAnalyzeWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/cashAnalyze.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/cashAnalyze.php',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;

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
						var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
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

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$vars = $this->_updateVars(array(
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

		$arrSubAccountTitle = $this->_getVarsSubAccountTitle(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrAccountTitle = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'numFiscalPeriod'    => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsPreference = $this->_getVarsPreference(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsCashValue = $this->_getVarsCashValue(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsFlagFiscalPeriod = $this->_getVarsFlagFiscalPeriod(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsStrFlagFiscalPeriod = $this->_getVarsStrFlagFiscalPeriod(array(
			'vars'             => $arr['vars']['varsItem']['tmplFiscalPeriod'],
			'varsEntityNation' => $varsEntityNation,
		));

		$data = array(
			'varsEntityNation'        => $varsEntityNation,
			'arrAccountTitle'         => $arrAccountTitle,
			'arrSubAccountTitle'      => $arrSubAccountTitle,
			'varsCashValue'           => $varsCashValue,
			'varsPreference'          => $varsPreference,
			'varsFlagFiscalPeriod'    => $varsFlagFiscalPeriod,
			'varsStrFlagFiscalPeriod' => $varsStrFlagFiscalPeriod,
		);

		return $data;
	}

	/**

	 */
	protected function _getVarsCashValue($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingCashValue',
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
					'strColumn'     => 'flagPay',
					'flagCondition' => 'eq',
					'value'         => 1,
				),
			),
		));

		if (!$rows['arrRows'][0]['jsonData']) {
			return array();
		}

		return $rows['arrRows'][0]['jsonData'];
	}

	/**
		(array(
		))
	 */
	protected function _getVarsPreference($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingCash',
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
			),
		));

		return $rows['arrRows'][0];
	}

	/**
		(array(
			'vars'             => $value,
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _getVarsStrFlagFiscalPeriod($arr)
	{
		$varsEntityNation = $arr['varsEntityNation'];

		$arrayNew = array();
		$array = $arr['vars']['arrayOption'];
		foreach ($array as $key => $value) {
			if ($varsEntityNation['numFiscalTermMonth'] == 12) {
				$arrayNew[] = $value;

			} else {
				if (preg_match( "/^(f1)$/", $value['value'])) {
					$arrayNew[] = $value;
				}
			}
		}
		$arr['vars']['arrayOption'] = $arrayNew;

		$arrayOption = array();
		if ($varsEntityNation['numFiscalTermMonth'] == 12) {
			$arrayOption = $arr['vars']['arrayOption'];

		} else {
			$arrayOption[] = $arr['vars']['varsPeriod'];
		}

		$numMonth = (int) $varsEntityNation['numFiscalBeginningMonth'];
		$numEnd = (int) $varsEntityNation['numFiscalTermMonth'];
		for ($i = 0; $i < $numEnd; $i++) {
			$data = array(
				'strTitle' => $numMonth . $arr['vars']['strMonth'],
				'value'    => $numMonth,
			);
			$arrayOption[] = $data;
			$numMonth++;
			if ($numMonth > 12) {
				$numMonth = 1;
			}
		}

		$varsStr = array();
		$array = $arrayOption;
		foreach ($array as $key => $value) {
			$varsStr[$value['value']] = $value['strTitle'];
		}

		return $varsStr;
	}

	/**
		(array(
			'vars'             => $vars,
			'varsEntityNation' => $varsEntityNation,
			'varsItem'         => $varsItem,
			'varsFlag'         => array(
				'idDepartment'      => $idDepartment,
				'flagFS'            => $flagFS,
			),
		))
	 */
	protected function _updateVars($arr)
	{
		global $classHtml;
		global $varsPluginAccountingAccount;

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllOutput'])) {
			$arr['vars']['portal']['varsList']['varsStart']['varsEdit']['flagOutputUse'] = 0;
			$arr['vars']['portal']['varsList']['varsEdit']['flagOutputUse'] = 0;
			$arr['vars']['portal']['varsDetail']['varsEdit']['flagOutputUse'] = 0;
		}

		$varsBase = array();
		$array = $arr['varsItem']['varsStrFlagFiscalPeriod'];
		foreach ($array as $key => $value) {
			$varsBase[$key] = $this->_getVarsDetailValue(array(
				'varsValue' => &$arr['varsItem']['varsCashValue'][$key],
			));
		}

		$arr['vars']['varsCollect']['varsCashValue'] = $this->_updateVarsCashValue(array(
			'varsValue' => $arr['varsItem']['varsCashValue'],
			'varsItem'  => $arr['varsItem'],
		));

		$arr['vars']['varsCollect']['varsBase'] = $varsBase;
		$arr['vars']['varsCollect']['varsFlagFiscalPeriod'] = $arr['varsItem']['varsFlagFiscalPeriod'];
		$arr['vars']['varsCollect']['varsStrFlagFiscalPeriod'] = $arr['varsItem']['varsStrFlagFiscalPeriod'];
		$arr['vars']['varsCollect']['arrAccountTitle'] = $arr['varsItem']['arrAccountTitle'];
		$arr['vars']['varsCollect']['arrSubAccountTitle'] = $arr['varsItem']['arrSubAccountTitle'];

		$arr['vars']['portal']['varsList']['varsDetail'] = $this->_updateVarsList((array(
			'vars'     => &$arr['vars'],
			'varsItem' => $arr['varsItem'],
		)));

		$arr['vars']['portal']['varsDetail']['templateDetail'] = $this->_updateVarsTemplateDetail((array(
			'vars'     => &$arr['vars'],
			'varsItem' => $arr['varsItem'],
		)));

		return $arr['vars'];
	}

	/**
		(array(
			'vars'             => $vars,
			'varsItem'         => $arr['varsItem'],
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _updateVarsTemplateDetail($arr)
	{
		$vars = &$arr['vars'];
		$varsItem = &$arr['varsItem'];

		$arrayNew = array();
		$array = &$vars['portal']['varsDetail']['templateDetail'];
		foreach ($array as $key => $value) {
			$method = '_updateVarsTemplateDetail' . $value['id'];
			if (method_exists($this, $method)) {
				$value = $this->$method(array(
					'value'     => $value,
					'vars'      => $vars,
					'varsItem'  => $varsItem,
				));
			}
			$arrayNew[] = $value;
		}

		$vars['portal']['varsDetail']['templateDetail'] = $arrayNew;

		return $vars['portal']['varsDetail']['templateDetail'];
	}

	/**
		(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		))
	 */
	protected function _updateVarsTemplateDetailIdAccountTitle($arr)
	{
		$arrSelectTag = array();
		$varsCash = $arr['varsItem']['varsPreference']['jsonCash'];

		$array = $arr['varsItem']['arrAccountTitle']['arrSubAccountSelectTag'];
		foreach ($array as $key => $value) {
			if ($varsCash[$value['value']]) {
				continue;
			}
			$arrSelectTag[] = $value;
		}
		$arr['value']['arrayOption'] = $arrSelectTag;

		if ($varsCash[$arr['value']['valueIni']]) {
			$array = $arr['value']['arrayOption'];
			foreach ($array as $key => $value) {
				if ($value['flagDisabled']) {
					continue;
				}
				$arr['value']['valueIni'] = $value['value'];
				break;
			}
		}

		return $arr['value'];
	}

	/**

	 */
	protected function _updateVarsCashValue($arr)
	{
		$array = &$arr['varsValue'];
		foreach ($array as $key => $value) {
			$flagFiscalPeriod = $key;
			$array[$key] = $this->_getVarsDetailValue(array(
				'varsValue' => $value,
			));
			if ($value['varsContra']) {
				$array[$key]['varsContra'] = $this->_getVarsDetailValueContra(array(
					'varsValue' => $value['varsContra'],
					'varsItem'  => $arr['varsItem'],
				));
			}
		}

		return $array;
	}

	/**

	 */
	protected function _getVarsDetailValueContra($arr)
	{
		$array = &$arr['varsValue'];
		foreach ($array as $key => $value) {
			$idAccountTitle = $key;
			$array[$key] = $this->_getVarsDetailValueContraSub(array(
				'varsValue'      => $value,
				'idAccountTitle' => $idAccountTitle,
				'varsItem'       => $arr['varsItem'],
			));
			$strAccountTitle = $arr['varsItem']['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['strTitleFS'];
			if (!$strAccountTitle) {
				$array[$key]['flagLost'] = 1;
			}
		}

		return $array;
	}

	/**

	 */
	protected function _getVarsDetailValueContraSub($arr)
	{
		$array = &$arr['varsValue'];
		foreach ($array as $key => $value) {
			$idSubAccountTitle = $key;
			$idAccountTitle = $arr['idAccountTitle'];
			$array[$key] = $this->_getVarsDetailValue(array(
				'varsValue' => $value,
			));
			if ($idSubAccountTitle == 'all') {
				continue;
			}
			$strSubAccountTitle = $arr['varsItem']['arrSubAccountTitle']['arrStrTitle'][$idAccountTitle][$idSubAccountTitle]['strTitle'];
			if (!$strSubAccountTitle) {
				$array[$key]['flagLost'] = 1;
			}
		}

		return $array;
	}

	/**

	 */
	protected function _getVarsDetailValue($arr)
	{
		$varsData = $arr['varsValue'];

		$sumIn = $varsData['sumIn'];
		$sumIn = (is_null($sumIn))? 0 : $sumIn;
		$varsData['sumIn'] = $sumIn;

		$sumOut = $varsData['sumOut'];
		$sumOut = (is_null($sumOut))? 0 : $sumOut;
		$varsData['sumOut'] = $sumOut;

		$sumNet = $sumIn - $sumOut;
		$varsData['sumNet'] = $sumNet;

		return $varsData;
	}

	/**
		(array(
			'vars'             => $vars,
			'varsItem'         => $arr['varsItem'],
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _updateVarsList($arr)
	{
		$vars = &$arr['vars'];
		$varsItem = &$arr['varsItem'];

		$arrayNew = array();
		$array = &$vars['portal']['varsList']['templateDetail'];
		foreach ($array as $key => $value) {
			$method = '_updateVarsList' . $value['id'];
			if (method_exists($this, $method)) {
				$value = $this->$method(array(
					'value'     => $value,
					'vars'      => $vars,
					'varsItem'  => $varsItem,
				));
			}
			$arrayNew[] = $value;
		}

		$vars['portal']['varsList']['templateDetail'] = $arrayNew;

		return $vars['portal']['varsList']['templateDetail'];
	}

	/**
		(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		))
	 */
	protected function _updateVarsListFlagFiscalPeriod($arr)
	{
		$varsEntityNation = $arr['varsItem']['varsEntityNation'];

		$arrayOption = array();
		if ($varsEntityNation['numFiscalTermMonth'] == 12) {
			$arrayOption = $arr['value']['varsTmpl']['arrayOption'];

		} else {
			$arrayOption = $arr['value']['varsTmpl']['varsPeriod'];
		}

		$arr['value']['arrayOption'] = $arrayOption;

		return $arr['value'];
	}

	protected function _updateVarsListTableF1($arr)
	{
		return $this-> _updateVarsListTable($arr);
	}

	protected function _updateVarsListTableF2($arr)
	{
		return $this-> _updateVarsListTable($arr);
	}

	protected function _updateVarsListTableF4($arr)
	{
		return $this-> _updateVarsListTable($arr);
	}

	protected function _updateVarsListTableMonth($arr)
	{
		return $this-> _updateVarsListTable($arr);
	}

	/**
		(array(
			'value'     => $value,
			'vars'      => $vars,
			'varsItem'  => $varsItem,
			'varsBase'  => $varsBase,
		))
	 */
	protected function _updateVarsListTable($arr)
	{
		global $classHtml;
		global $varsPluginAccountingAccount;


		$varsStrFlagFiscalPeriod = $arr['varsItem']['varsStrFlagFiscalPeriod'];
		$varsBase = $arr['varsBase'];

		$varsColumn = array('');
		$varsColumnWidth = array($arr['value']['tmplTable']['numWidthItem']);
		$varsColumnId = array('item');
		$numWidth = 0;

		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$arrayFlagFiscalPeriod = $arr['varsItem']['varsStrFlagFiscalPeriod'];
		foreach ($arrayFlagFiscalPeriod as $keyFlagFiscalPeriod => $valueFlagFiscalPeriod) {
			if ($arr['value']['id'] == 'TableF1') {
				if (!preg_match("/^f1$/", $keyFlagFiscalPeriod)) {
					continue;
				}

			} else if ($arr['value']['id'] == 'TableF2') {
				if (!preg_match("/^f2/", $keyFlagFiscalPeriod)) {
					continue;
				}

			} else if ($arr['value']['id'] == 'TableF4') {
				if (!preg_match("/^f4/", $keyFlagFiscalPeriod)) {
					continue;
				}

			} else if ($arr['value']['id'] == 'TableMonth') {
				if (preg_match("/^f/", $keyFlagFiscalPeriod)) {
					continue;
				}
			}
			$varsColumn[] = $valueFlagFiscalPeriod;
			$varsColumnId[] = $keyFlagFiscalPeriod;
			$varsColumnWidth[] = $arr['value']['tmplTable']['numWidth'];
			$numWidth += $arr['value']['tmplTable']['numWidth'];
		}

		$varsBase = &$arr['vars']['varsCollect']['varsBase'];

		$arrayNew = array();
		$array = $arr['vars']['varsCollect']['varsLabel'];
		foreach ($array as $key => $value) {
			$varsDetail = $arr['value']['tmplTable']['tmplDetail'];
			$arrayColumn = $varsColumnId;
			foreach ($arrayColumn as $keyColumn => $valueColumn) {
				$varsDetail['varsDetail'][$valueColumn] = $arr['value']['tmplTable']['tmplData'];
				if ($valueColumn == 'item') {
					$varsDetail['varsDetail'][$valueColumn]['value'] = $value;
					$varsDetail['varsDetail'][$valueColumn]['strClass'] = $arr['value']['tmplTable']['strClassLeft'];
					$varsDetail['varsDetail'][$valueColumn]['flagOverflowUse'] = 1;
					continue;
				}
				$varsDetail['varsDetail'][$valueColumn]['value'] = number_format($varsBase[$valueColumn][$key]);
			}
			$arrayNew[] = $varsDetail;
		}

		$arr['value']['tmplTable']['varsStatus']['varsColumnId'] = $varsColumnId;
		$arr['value']['tmplTable']['varsStatus']['varsColumnWidth'] = $varsColumnWidth;

		$varsTemp = $classHtml->allot(array(
			'strClass'     => 'TableSimple',
			'flagStatus'   => 'Html',
			'varsDetail'   => $arrayNew,
			'varsColumn'   => $varsColumn,
			'varsStatus'   => $arr['value']['tmplTable']['varsStatus'],
		));
		$arr['value']['varsSpace']['varsDetail']['strHtml'] = $varsTemp['strHtml'];

		return $arr['value'];
	}

	/**

	 */
	protected function _setSearch()
	{
		global $varsRequest;
		global $varsPluginAccountingPreference;
		global $varsPluginAccountingAccount;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$this->sendVars(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'varsCollect' => $vars['varsCollect'],
				'varsDetail'  => $vars['portal']['varsList']['varsDetail'],
			),
		));
	}

	/**
	 *
	 */
	protected function _iniListReload()
	{
		$this->_setSearch();
	}

	/**
	 *
	 */
	protected function _iniListOutput()
	{
		$this->_setClassExt(array('strClass' => 'CashAnalyzeOutput'));
	}

	/**
	 *
	 */
	protected function _iniDetailOutput()
	{
		$this->_setClassExt(array('strClass' => 'CashAnalyzeOutput'));
	}
}
