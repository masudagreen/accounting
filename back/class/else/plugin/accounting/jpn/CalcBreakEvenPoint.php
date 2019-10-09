<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcBreakEvenPoint extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extChildSelf = array(
		'pathVarsJs'     => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/breakEvenPoint.php',
		'varsDefaultPL'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/breakEvenPointPL.php',
		'varsDefaultCR'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/breakEvenPointCR.php',
	);

	/**
	 *
	 */
	public function run()
	{
		if (FLAG_TEST) {
			var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
		}
		exit;
	}

	public function allot($arr)
	{
		$method = '_ini' . ucwords($arr['flagStatus']);
		if (method_exists($this, $method)) {
			return $this->$method($arr);

		} else {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}
	}

	/**
		(array(
			'flagStatus'      => 'data',
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'varsDetail'      => $vars['vars']['varsDetail'],
			'varsCollect'     => $vars['vars']['varsCollect'],
			'varsRow'         => $vars['vars']['varsRow'],
		))
	 */
	protected function _iniData($arr)
	{
		global $classSmarty;

		$vars = $this->getVars(array(
			'path' => $this->_extChildSelf['pathVarsJs'],
		));

		$vars['portal']['varsDetail']['templateDetail'] = $arr['varsDetail'];
		$vars['portal']['varsDetail']['varsCollect'] = $arr['varsCollect'];
		$vars['varsItem']['varsRow'] = $arr['varsRow'];

		$varsItem = $this->_getVarsItem(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'vars'            => $vars,
		));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		return array(
			'varsCollect' => $vars['portal']['varsDetail']['varsCollect'],
			'varsDetail'  => $vars['portal']['varsDetail']['varsDetail'],
		);
	}


	/**
		(array(
			'vars'     => $vars,
			'varsFlag' => $vars['varsFlag'],
		))
	 */
	protected function _getVarsItem($arr)
	{
		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$varsFSValue = $this->_getVarsFSValue(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$varsSave = $this->_getVarsSave(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$varsFlagFiscalPeriod = $this->_getVarsFlagFiscalPeriod(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
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
					'path' => $this->_extChildSelf['varsDefault' . $value],
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
			'varsEntityNation'        => $varsEntityNation,
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
			'numFiscalPeriod' => $arr['numFiscalPeriod']
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


}
