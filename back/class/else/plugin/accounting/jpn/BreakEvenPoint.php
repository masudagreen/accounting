<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_BreakEvenPoint extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference'   => 'breakEvenPointWindow',
		'pathTplJs'      => 'else/plugin/accounting/js/jpn/breakEvenPoint.js',
		'pathVarsJs'     => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/breakEvenPoint.php',
		'varsDefaultPL'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/breakEvenPointPL.php',
		'varsDefaultCR'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/breakEvenPointCR.php',
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
			'vars'     => $vars,
			'varsFlag' => $vars['varsFlag'],
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

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
			'vars'     => $vars,
			'varsFlag' => $vars['varsFlag'],
		))
	 */
	protected function _getVarsItem($arr)
	{
		global $varsPluginAccountingAccount;

		$varsDepartment = $this->_getVarsDepartment(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		if (!$varsDepartment['arrStrTitle'][$arr['varsFlag']['idDepartment']]) {
			$arr['varsFlag']['idDepartment'] = 0;
		}

		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsFSValue = $this->_getVarsFSValue(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		if ($arr['varsFlag']['idDepartment'] != 0) {
			$varsFSValue = $this->_getVarsFSValueDepartment(array(
				'idDepartment'    => $arr['varsFlag']['idDepartment'],
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			));
		}

		$varsSave = $this->_getVarsSave(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idDepartment'    => $arr['varsFlag']['idDepartment'],
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsFlagFiscalPeriod = $this->_getVarsFlagFiscalPeriod(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsStrFlagFiscalPeriod = $this->_getVarsStrFlagFiscalPeriod(array(
			'vars'             => $arr['vars']['varsItem']['tmplFiscalPeriod'],
			'varsEntityNation' => $varsEntityNation,
		));

		$varsCollect = array();
		$arrayFlagFiscalPeriod = $varsFlagFiscalPeriod;
		foreach ($arrayFlagFiscalPeriod as $keyFlagFiscalPeriod => $valueFlagFiscalPeriod) {
			$varsValue = array(
				'numSales'      => 0,
				'numVariable'   => 0,
				'numFixed'      => 0,
			);
			$array = array('PL', 'CR');
			foreach ($array as $key => $value) {
				if (!$varsEntityNation['flagCR'] && $value == 'CR') {
					continue;
				}
				$varsDefault = $this->getVars(array(
					'path' => $this->_extSelf['varsDefault' . $value],
				));
				$str = 'jsonJgaapAccountTitle'. $value;
				$this->_setVarsValue(array(
					'flagFiscalPeriod' => $valueFlagFiscalPeriod,
					'varsValue'        => &$varsValue,
					'varsFS'           => $varsFS[$str],
					'varsFSValue'      => $varsFSValue[$str],
					'flagFS'           => $value,
					'varsSave'         => ($varsSave[$str])? $varsSave[$str] : array(),
					'varsDefault'      => $varsDefault,
				));
				$varsCollect[$valueFlagFiscalPeriod] = $varsValue;
			}
		}

		$data = array(
			'varsValue'               => $varsValue,
			'varsCollect'             => $varsCollect,
			'varsFlag'                => $arr['varsFlag'],
			'varsEntityNation'        => $varsEntityNation,
			'varsDepartment'          => $varsDepartment,
			'varsFlagFiscalPeriod'    => $varsFlagFiscalPeriod,
			'varsStrFlagFiscalPeriod' => $varsStrFlagFiscalPeriod,
		);

		return $data;

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
			'varsFlag'     => $arr['varsFlag'],
			'varsValue'    => &$varsValue,
			'varsFS'       => $varsFS[$str],
			'varsFSValue'  => $varsFSValue[$str],
			'flagFS'       => $value,
			'varsSave'     => ($varsSave[$str])? $varsSave[$str] : array(),
			'varsDefault'  => $varsDefault,
		));
	 */
	protected function _setVarsValue($arr)
	{
		$array = &$arr['varsFS'];
		foreach ($array as $key => $value) {
			if (!is_null($value['vars']['varsValue'])) {
				if (!($value['vars']['flagCalc'] == 'sum' || $value['vars']['flagCalc'] == 'net')) {
					$idTarget = $value['vars']['idTarget'];
					$flagDebit = $value['vars']['flagDebit'];
					$varsType = array();
					if ($arr['varsDefault'][$idTarget]) {
						$varsType = $arr['varsDefault'][$idTarget];
					}
					if ($arr['varsSave'][$idTarget]) {
						$varsType = $arr['varsSave'][$idTarget];
					}
					if ($varsType) {
						$num = $arr['varsFSValue'][$arr['flagFiscalPeriod']][$idTarget]['sumNext'];
						if (is_null($num)) {
							$num = 0;
						}
						if ($varsType['flagType'] == 'sales') {
							if ($flagDebit) {
								$num *= -1;
							}
							$arr['varsValue']['numSales'] += $num;

						} elseif ($varsType['flagType'] == 'variable') {
							if (!$flagDebit) {
								$num *= -1;
							}
							$arr['varsValue']['numVariable'] += $num;

						} elseif ($varsType['flagType'] == 'fixed') {
							if (!$flagDebit) {
								$num *= -1;
							}
							$arr['varsValue']['numFixed'] += $num;
						}
					}
				}
			}
			if ($value['child']) {
				$array[$key]['child'] = $this->_setVarsValue(array(
					'flagFiscalPeriod' => $arr['flagFiscalPeriod'],
					'varsValue'        => &$arr['varsValue'],
					'varsFS'           => $array[$key]['child'],
					'varsFSValue'      => $arr['varsFSValue'],
					'flagFS'           => $arr['flagFS'],
					'varsSave'         => $arr['varsSave'],
					'varsDefault'      => $arr['varsDefault'],
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
			if (method_exists($this, $method)) {
				$value = $this->$method(array(
					'vars'      => $value,
					'varsItem'  => $varsItem,
				));
			}

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
			'vars'             => $vars,
			'varsItem'         => $arr['varsItem'],
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _updateVarsDetail($arr)
	{
		$vars = &$arr['vars'];
		$varsItem = &$arr['varsItem'];

		$arrayNew = array();
		$array = &$vars['portal']['varsDetail']['templateDetail'];
		foreach ($array as $key => $value) {
			$method = '_updateVarsDetail' . $value['id'];
			if (method_exists($this, $method)) {
				$value = $this->$method(array(
					'value'     => $value,
					'vars'      => $vars,
					'varsItem'  => $varsItem,
					'varsBase'  => $arr['varsBase'],
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
	protected function _updateVarsDetailFlagFiscalPeriod($arr)
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

	protected function _updateVarsDetailTableF1($arr)
	{
		return $this-> _updateVarsDetailTable($arr);
	}

	protected function _updateVarsDetailTableF2($arr)
	{
		return $this-> _updateVarsDetailTable($arr);
	}

	protected function _updateVarsDetailTableF4($arr)
	{
		return $this-> _updateVarsDetailTable($arr);
	}

	protected function _updateVarsDetailTableMonth($arr)
	{
		return $this-> _updateVarsDetailTable($arr);
	}

	/**
		(array(
			'vars'      => $value,
			'varsItem'  => $varsItem,
			'varsData'  => $arr['varsData'],
		))
	 */
	protected function _updateVarsDetailTable($arr)
	{
		global $classHtml;

		$varsStrFlagFiscalPeriod = $arr['varsItem']['varsStrFlagFiscalPeriod'];
		$varsBase = $arr['varsBase'];

		$varsColumn = array('');
		$varsColumnWidth = array($arr['value']['tmplTable']['numWidthItem']);
		$varsColumnId = array('item');
		$array = $varsStrFlagFiscalPeriod;
		$numWidth = 0;
		foreach ($array as $key => $value) {
			if ($arr['value']['id'] == 'TableF1') {
				if (!preg_match("/^f1$/", $key)) {
					continue;
				}

			} else if ($arr['value']['id'] == 'TableF2') {
				if (!preg_match("/^f2/", $key)) {
					continue;
				}

			} else if ($arr['value']['id'] == 'TableF4') {
				if (!preg_match("/^f4/", $key)) {
					continue;
				}

			} else if ($arr['value']['id'] == 'TableMonth') {
				if (preg_match("/^f/", $key)) {
					continue;
				}
			}
			$varsColumn[] = $value;
			$varsColumnId[] = $key;
			$varsColumnWidth[] = $arr['value']['tmplTable']['numWidth'];
			$numWidth += $arr['value']['tmplTable']['numWidth'];
		}

		$arrayNew = array();
		$array = $arr['vars']['varsItem']['varsRow'];
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
				$varsDetail['varsDetail'][$valueColumn]['value'] = $varsBase[$valueColumn][$key . 'Comma'];
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
		(array(
			'vars'             => $vars,
			'varsEntityNation' => $varsEntityNation,
			'varsItem'         => $varsItem,
			'varsFlag'         => array(
				'flagFiscalPeriod'  => $flagFiscalPeriod,
			),
			'flagOutput'       => ($arr['flagOutput'])? 1 : 0,
		))
	 */
	protected function _updateVars($arr)
	{
		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllOutput'])) {
			$arr['vars']['portal']['varsDetail']['varsStart']['varsEdit']['flagOutputUse'] = 0;
			$arr['vars']['portal']['varsDetail']['varsEdit']['flagOutputUse'] = 0;
		}

		$varsBase = $arr['varsItem']['varsCollect'];
		foreach ($varsBase as $key => $value) {
			$varsBase[$key] = $this->_getVarsDetailValue(array(
				'varsData' => $value,
			));
		}
		$arr['vars']['portal']['varsDetail']['varsCollect']['varsBase'] = $varsBase;

		$varsLabelId = array();
		$varsLabel = array();
		$array = $arr['vars']['varsItem']['varsRow'];
		foreach ($array as $key => $value) {
			if (preg_match("/Rate$/", $key)) {
				continue;
			}
			$varsLabel[$key] = $value;
			$varsLabelId[] = $key;
		}
		$arr['vars']['portal']['varsDetail']['varsCollect']['varsLabel'] = $varsLabel;
		$arr['vars']['portal']['varsDetail']['varsCollect']['varsLabelId'] = $varsLabelId;

		//varsFlagFiscalPeriod
		$arr['vars']['portal']['varsDetail']['varsCollect']['varsFlagFiscalPeriod'] = $arr['varsItem']['varsFlagFiscalPeriod'];
		$arr['vars']['portal']['varsDetail']['varsCollect']['varsStrFlagFiscalPeriod'] = $arr['varsItem']['varsStrFlagFiscalPeriod'];

		$arr['vars']['portal']['varsDetail']['varsDetail'] = $this->_updateVarsDetail((array(
			'vars'     => &$arr['vars'],
			'varsItem' => $arr['varsItem'],
			'varsBase' => $varsBase,
		)));

		return $arr['vars'];
	}


	/**

	 */
	protected function _getVarsDetailValue($arr)
	{
		/*
		 *  'numSales'      => 0,
			'numVariable'   => 0,
			'numFixed'      => 0,
		 *
		 * */
		$varsData = $arr['varsData'];

		$varsData['numSalesComma'] = number_format($varsData['numSales']);
		$varsData['numFixedComma'] = number_format($varsData['numFixed']);
		$varsData['numVariableComma'] = number_format($varsData['numVariable']);

		if ($varsData['numSales'] == 0) {
			$varsData['numVariableRate'] = '0.000';

		} else {
			$varsData['numVariableRate'] = number_format($varsData['numVariable'] / $varsData['numSales'], 3, '.', '');
		}
		$varsData['numVariableRateComma'] = number_format($varsData['numVariableRate'], 3);

		$varsData['numMargin'] = $varsData['numSales'] - $varsData['numVariable'];
		$varsData['numMarginComma'] = number_format($varsData['numMargin']);

		if ($varsData['numSales'] == 0) {
			$varsData['numMarginRate'] = '0.000';

		} else {
			$varsData['numMarginRate'] = number_format($varsData['numMargin'] / $varsData['numSales'], 3, '.', '');
		}
		$varsData['numMarginRateComma'] = number_format($varsData['numMarginRate'], 3);

		if ($varsData['numMarginRate'] == 0) {
			$varsData['numPoint'] = 0;

		} else {
			$varsData['numPoint'] = number_format($varsData['numFixed'] / $varsData['numMarginRate'], 0, '.', '');
		}
		$varsData['numPointComma'] = number_format($varsData['numPoint']);



		if ($varsData['numSales'] == 0) {
			$varsData['numSafeRate'] = '0.000';

		} else {
			$varsData['numSafeRate'] = number_format(($varsData['numSales'] - $varsData['numPoint']) / $varsData['numSales'], 3, '.', '');
		}
		$varsData['numSafeRateComma'] = number_format($varsData['numSafeRate'], 3);

		$varsData['numSafe'] = number_format($varsData['numSales'] - $varsData['numPoint'], 0, '.', '');
		$varsData['numSafeComma'] = number_format($varsData['numSafe']);

		return $varsData;
	}

	/**
	 *
	 */
	protected function _iniDetailOutput()
	{
		$this->_setClassExt(array('strClass' => 'BreakEvenPointOutput'));
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
		global $varsPluginAccountingAccount;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsFlag = array(
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

		$this->_checkValueDetail(array(
			'varsDetail' => $vars['portal']['varsNavi']['varsDetail'],
			'varsFlag'   => $varsFlag,
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
				'varsBase'   => $vars['portal']['varsDetail']['varsCollect']['varsBase'],
				'varsDetail' => $vars['portal']['varsDetail']['varsDetail'],
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
	protected function _iniDetailReload()
	{
		$this->_setSearch();
	}

}
