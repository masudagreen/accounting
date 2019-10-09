<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_Budget extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference' => 'budgetWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/budget.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/budget.php',
		'tplTable'     => 'else/plugin/accounting/html/budget.html',
		'tplTableItem' => 'else/plugin/accounting/html/budgetItem.html',
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
			'vars'     => $vars,
			'varsFlag' => $vars['varsFlag'],
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$vars['portal']['varsDetail']['varsDetail'] = $this->_updateVarsDetail((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $vars['varsFlag'],
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
			'vars'             => $vars,
			'varsItem'         => $arr['varsItem'],
		))
	 */
	protected function _updateVarsDetail($arr)
	{
		$vars = &$arr['vars'];
		$varsItem = &$arr['varsItem'];

		$arrayNew = array();
		$array = &$vars['portal']['varsDetail']['templateDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'JsonFiscalPeriod' || $value['id'] == 'JsonData') {
				$method = '_updateVarsDetail' . $value['id'];
				$value = $this->$method(array(
					'vars'     => $value,
					'varsItem' => $varsItem,
				));

			}
			$arrayNew[] = $value;
		}
		$vars['portal']['varsDetail']['templateDetail'] = $arrayNew;


		return $vars['portal']['varsDetail']['templateDetail'];
	}

	/**
		(array(
			'vars'             => $value,
			'varsItem'         => $varsItem,
		))
	 */
	protected function _updateVarsDetailJsonFiscalPeriod($arr)
	{
		$varsEntityNation = $arr['varsItem']['varsEntityNation'];

		$arrayOption = array();
		if ($varsEntityNation['numFiscalTermMonth'] == 12) {
			$arrayOption = $arr['vars']['varsTmpl']['arrayOption'];

		} else {
			$arrayOption = $arr['vars']['varsTmpl']['varsPeriod'];
		}

		$numMonth = (int) $varsEntityNation['numFiscalBeginningMonth'];
		$numEnd = (int) $varsEntityNation['numFiscalTermMonth'];
		for ($i = 0; $i < $numEnd; $i++) {
			$data = array(
				'strTitle' => $numMonth . $arr['vars']['varsTmpl']['strMonth'],
				'value'    => $numMonth,
			);
			$arrayOption[] = $data;
			$numMonth++;
			if ($numMonth > 12) {
				$numMonth = 1;
			}
		}

		$arr['vars']['arrayOption'] = $arrayOption;

		return $arr['vars'];
	}

	/**
		(array(
			'vars'             => $value,
			'varsItem'         => $varsItem,
		))
	 */
	protected function _updateVarsDetailJsonData($arr)
	{
		$arr['vars']['varsFormSensitive']['varsTmpl']['tmplTable'] = $this->_getHtml(array(
			'varsStr' => $arr['vars']['varsStr'],
			'pathTpl' => $this->_extSelf['tplTable'],
		));

		$arr['vars']['varsFormSensitive']['varsTmpl']['tmplTableItem'] = $this->_getHtml(array(
			'varsStr' => array(),
			'pathTpl' => $this->_extSelf['tplTableItem'],
		));

		return $arr['vars'];
	}

	/**
		(array(
			'varsStr' => $arrayStr
		))
	 */
	protected function _getHtml($arr)
	{
		global $classSmarty;

		$pathTpl = $arr['pathTpl'];

		$array = $arr['varsStr'];
		foreach ($array as $key => $value) {
			$classSmarty->assign($key, $value);
		}
		$path = $pathTpl;
		$data = $classSmarty->fetch($path);

		return $data;
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

		$varsFSValue = $this->_getVarsFSValue(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsDepartment = $this->_getVarsDepartment(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		if ($arr['varsFlag']['idDepartment'] != 0) {
			$varsFSValue = $this->_getVarsFSValueDepartment(array(
				'idDepartment'    => $arr['varsFlag']['idDepartment'],
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			));
		}

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsFS = $varsFS['jsonJgaapAccountTitle' . $arr['varsFlag']['flagFS']];
		$varsBudget = $this->_getVarsBedget(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'varsFlag'        => $arr['varsFlag'],
			'varsFS'          => $varsFS,
		));

		$data = array(
			'varsFS'           => $varsFS,
			'varsFSValue'      => $varsFSValue['jsonJgaapAccountTitle' . $arr['varsFlag']['flagFS']],
			'varsDepartment'   => $varsDepartment,
			'varsEntityNation' => $varsEntityNation,
			'varsBudget'       => $varsBudget,
		);

		return $data;

	}

	/**

	 */
	protected function _getVarsBedget($arr)
	{
		$arrFlagFiscalPeriod = array();
		$flagFiscalPeriod = $arr['varsFlag']['flagFiscalPeriod'];
		if (preg_match("/sum$/", $flagFiscalPeriod)) {
			if (preg_match("/^f1/", $flagFiscalPeriod)) {
				$arrFlagFiscalPeriod = array('f1');

			} elseif (preg_match("/^f2/", $flagFiscalPeriod)) {
				$arrFlagFiscalPeriod = array('f21', 'f22');

			} elseif (preg_match("/^f4/", $flagFiscalPeriod)) {
				$arrFlagFiscalPeriod = array('f41', 'f42', 'f43', 'f44');

			} else {
				$numEnd = 12;
				for ($i = 1; $i <= $numEnd; $i++) {
					$arrFlagFiscalPeriod[] = $i;
				}
			}

		} else {
			$arrFlagFiscalPeriod = array($arr['varsFlag']['flagFiscalPeriod']);
		}

		$varsValue = $this->_setVarsBedgetValue(array(
			'arrFlagFiscalPeriod' => $arrFlagFiscalPeriod,
			'varsFlag'            => $arr['varsFlag'],
			'numFiscalPeriod'     => $arr['numFiscalPeriod'],
		));

		$varsFS = $this->_setVarsListValue(array(
			'varsFS'    => $arr['varsFS'],
			'varsValue' => &$varsValue,
		));

		$this->_loopVarsCalc(array(
			'varsFS'    => $varsFS,
			'varsValue' => &$varsValue,
		));

		return $varsValue;
	}

	/**
		(array(
			'varsValue' => &$varsValue,
			'varsData'  => $rows['arrRows'][0]['jsonData'],
		))
	 */
	protected function _setVarsBedgetValue($arr)
	{
		$varsValue = array();
		$array = $arr['arrFlagFiscalPeriod'];
		foreach ($array as $key => $value) {
			$rows = $this->_getLog(array(
				'flagFiscalPeriod' => $value,
				'numFiscalPeriod'  => $arr['numFiscalPeriod'],
				'flagFS'           => $arr['varsFlag']['flagFS'],
				'idDepartment'     => $arr['varsFlag']['idDepartment'],
			));
			if (!$rows['numRows']) {
				continue;
			}
			$this->_updateVarsBedgetValue(array(
				'varsValue' => &$varsValue,
				'varsData'  => $rows['arrRows'][0]['jsonData'],
			));
		}

		return $varsValue;
	}

		/**
		(array(
			'varsFS'    => $arr['varsItem']['varsFS'][$strFS . 'BS'],
			'varsValue' => &$arr['varsFSValue'][$strFS . 'BS'][$value],
		));
	 */
	protected function _loopVarsCalc($arr)
	{
		$array = &$arr['varsFS'];
		$arraySum = array();
		$arrayNet = array();
		$flag = 0;
		foreach ($array as $key => $value) {
			if ($value['child']) {
				$arraySum = $this->_loopVarsCalc(array(
					'varsFS'    => $array[$key]['child'],
					'varsValue' => &$arr['varsValue'],
				));
			}

			if (!is_null($value['vars']['varsValue'])) {
				$numNext = 0;
				if ($value['vars']['flagCalc'] == 'sum') {
					foreach ($arraySum as $keySum => $valueSum) {
						if ((int) $value['vars']['flagDebit']) {
							if ($valueSum['flagDebit']) {
								$numNext += $valueSum['numNext'];

							} else {
								$numNext -= $valueSum['numNext'];
							}
						} else {
							if ($valueSum['flagDebit']) {
								$numNext -= $valueSum['numNext'];

							} else {
								$numNext += $valueSum['numNext'];
							}
						}
					}
					$arraySum = array();
					$arr['varsValue'][$value['vars']['idTarget']] = $numNext;

				} elseif ($value['vars']['flagCalc'] == 'net') {
					foreach ($arrayNet as $keyNet => $valueNet) {
						if ((int) $value['vars']['flagDebit']) {
							if ($valueNet['flagDebit']) {
								$numNext += $valueNet['numNext'];

							} else {
								$numNext -= $valueNet['numNext'];
							}
						} else {
							if ($valueNet['flagDebit']) {
								$numNext -= $valueNet['numNext'];

							} else {
								$numNext += $valueNet['numNext'];
							}
						}
					}
					$arr['varsValue'][$value['vars']['idTarget']] = $numNext;

				} else {
					if ($value['varsValue']['numBudget'] != '') {
						$numNext = $value['varsValue']['numBudget'];
						$arr['varsValue'][$value['vars']['idTarget']] = $numNext;
					}
				}
				$data = array(
					'flagDebit' => (int) $value['vars']['flagDebit'],
					'numNext'   => $numNext,
				);

				if ($value['vars']['flagCalc'] == 'net') {
					$arrayNet = array();
				}
				$arrayNet[$value['vars']['idTarget']] = $data;
			}
		}

		return $arrayNet;
	}


	/**
		(array(
			'varsFS'    => $arr['varsItem']['varsFS'][$strFS . 'BS'],
			'varsValue' => array(),
		));
	 */
	protected function _setVarsListValue($arr)
	{
		$array = &$arr['varsFS'];
		foreach ($array as $key => $value) {
			if (!is_null($value['vars']['varsValue'])) {
				if (!($value['vars']['flagCalc'] == 'sum' || $value['vars']['flagCalc'] == 'net')) {
					$idTarget = $value['vars']['idTarget'];
					$num = $arr['varsValue'][$idTarget];
					if (!is_null($num)) {
						$array[$key]['varsValue']['numBudget'] = $num;
					}
				}
			}
			if ($value['child']) {
				$array[$key]['child'] = $this->_setVarsListValue(array(
					'varsFS'    => $array[$key]['child'],
					'varsValue' => &$arr['varsValue'],
				));
			}
		}

		return $array;
	}

	/**
		(array(
			'varsValue' => &$varsValue,
			'varsData'  => $rows['arrRows'][0]['jsonData'],
		))
	 */
	protected function _updateVarsBedgetValue($arr)
	{
		$array = $arr['varsData'];
		$arrayNew = array();
		foreach ($array as $key => $value) {
			$num = $arr['varsValue'][$key];
			if (is_null($num)) {
				$arr['varsValue'][$key] = $value;

			} else {
				$arr['varsValue'][$key] += $value;
			}
		}
	}

	/**
		(array(
			'value' => 0,
		))
	 */
	protected function _getLog($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingBudget' . $strNation,
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
					'flagType'      => '',
					'strColumn'     => 'flagFiscalPeriod',
					'flagCondition' => 'eq',
					'value'         => $arr['flagFiscalPeriod'],
				),
				array(
					'flagType'      => '',
					'strColumn'     => 'idDepartment',
					'flagCondition' => 'eq',
					'value'         => $arr['idDepartment'],
				),
				array(
					'flagType'      => '',
					'strColumn'     => 'flagFS',
					'flagCondition' => 'eq',
					'value'         => $arr['flagFS'],
				),
			),
		));

		return $rows;
	}

	/**

	 */
	protected function _getVarsFlagUnit($arr)
	{
		global $classDb;
		global $varsAccount;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$array = $arr['vars']['portal']['varsNavi']['templateDetail'];
		$arrStrTitle = array();
		foreach ($array as $key => $value) {
			if ($value['id'] == 'FlagUnit') {
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

	 */
	protected function _getVarsFlagFiscalPeriod($arr)
	{
		global $classDb;
		global $varsAccount;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$array = $arr['vars']['portal']['varsNavi']['templateDetail'];
		$arrStrTitle = array();
		foreach ($array as $key => $value) {
			if ($value['id'] == 'FlagFiscalPeriod') {
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
			'vars'             => $vars,
			'varsItem'         => $arr['varsItem'],
		))
	 */
	protected function _updateVarsNavi($arr)
	{
		$vars = &$arr['vars'];
		$varsItem = &$arr['varsItem'];

		$arrayNew = array();
		$array = &$vars['portal']['varsNavi']['templateDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'FlagFiscalPeriod'
				|| $value['id'] == 'FlagFS'
				|| $value['id'] == 'IdDepartment'
			) {
				$method = '_updateVarsNavi' . $value['id'];
				$value = $this->$method(array(
					'vars'     => $value,
					'varsItem' => $varsItem,
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

		return $arr['vars'];
	}

	/**
		(array(
			'vars'             => $value,
			'varsItem'         => $varsItem,
		))
	 */
	protected function _updateVarsNaviFlagFiscalPeriod($arr)
	{
		$varsEntityNation = $arr['varsItem']['varsEntityNation'];

		$arrayOption = array();
		if ($varsEntityNation['numFiscalTermMonth'] == 12) {
			$arrayOption = $arr['vars']['varsTmpl']['arrayOption'];

		} else {
			$arrayOption = $arr['vars']['varsTmpl']['varsPeriod'];
		}

		$numMonth = (int) $varsEntityNation['numFiscalBeginningMonth'];
		$numEnd = (int) $varsEntityNation['numFiscalTermMonth'];
		for ($i = 0; $i < $numEnd; $i++) {
			$data = array(
				'strTitle' => $numMonth . $arr['vars']['varsTmpl']['strMonth'],
				'value'    => $numMonth,
			);
			$arrayOption[] = $data;
			$numMonth++;
			if ($numMonth > 12) {
				$numMonth = 1;
			}
		}

		$arr['vars']['arrayOption'] = $arrayOption;
		$arr['vars']['numSize'] = count($arrayOption);

		return $arr['vars'];
	}

	/**
		(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		))
	 */
	protected function _updateVars($arr)
	{
		global $classHtml;

		global $varsAccount;

		$arr['vars']['portal']['varsList']['varsIni'] = $this->_getAccountTitleValueColumn(array(
			'varsFS'      => $arr['varsItem']['varsFS'],
			'varsFSValue' => $arr['varsItem']['varsFSValue'],
			'varsBudget'  => array(),
			'varsFlag'    => $arr['varsFlag'],
		));
		$arr['vars']['portal']['varsList']['varsDetail'] = $this->_getAccountTitleValueColumn(array(
			'varsFS'      => $arr['varsItem']['varsFS'],
			'varsFSValue' => $arr['varsItem']['varsFSValue'],
			'varsBudget'  => $arr['varsItem']['varsBudget'],
			'varsFlag'    => $arr['varsFlag'],
		));

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllOutput'])) {
			$arr['vars']['portal']['varsList']['varsStart']['varsEdit']['flagOutputUse'] = 0;
		}
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllInsert'])) {
			$arr['vars']['portal']['varsList']['varsStart']['varsEdit']['flagEditUse'] = 0;
		}
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'])) {
			$arr['vars']['portal']['varsList']['varsStart']['varsEdit']['flagEditUse'] = 0;
		}

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done)$/", $flagCurrentFlagNow)) {
			$arr['vars']['portal']['varsList']['varsStart']['varsEdit']['flagEditUse'] = 0;
		}

		$varsDetail = $this->_setTreeId(array(
			'idParent' => '-',
			'vars'     => $arr['vars']['portal']['varsList']['varsDetail'],
		));
		$arr['vars']['portal']['varsList']['varsDetail'] = $varsDetail;

		$arr['vars']['portal']['varsList']['varsIni'] = $this->_setTreeId(array(
			'idParent' => '-',
			'vars'     => $arr['vars']['portal']['varsList']['varsIni'],
		));

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
			'varsFS'      => $arr['varsItem']['varsFS'],
			'varsFSValue' => $arr['varsItem']['varsFSValue'],
			'varsBudget'  => $arr['varsItem']['varsBudget'],
			'varsFlag'    => $arr['varsFlag'],
		))

	 */
	protected function _getAccountTitleValueColumn($arr)
	{
		$array = &$arr['varsFS'];

		$flagUnit = (int) $arr['varsFlag']['flagUnit'];
		$flagFiscalPeriod = $arr['varsFlag']['flagFiscalPeriod'];
		if (preg_match("/sum$/", $flagFiscalPeriod)) {
			$flagFiscalPeriod = 'f1';
		}

		$flagCalc = $arr['varsFlag']['flagCalc'];
		$arrayValue = array('numBudget', 'numNext', 'numDiff', 'numRate');
		foreach ($array as $key => $value) {
			foreach ($arrayValue as $keyValue => $valueValue) {
				$array[$key]['varsValue'][$valueValue] = '';
				$array[$key]['varsColumnDetail'][$valueValue] = '';
			}
			$array[$key]['flagBtnUse'] = 1;

			if (!is_null($array[$key]['vars']['varsValue'])) {
				$idTarget = $value['vars']['idTarget'];

				//numBudget
				$numBudget = $arr['varsBudget'][$idTarget];
				if (is_null($numBudget)) {
					$numBudget = 0;
				}
				$array[$key]['varsValue']['numBudget'] = $numBudget;
				$array[$key]['varsColumnDetail']['numBudget'] = number_format($numBudget);


				//numNext
				$numNext = $arr['varsFSValue'][$flagFiscalPeriod][$idTarget]['sumNext'];
				if (is_null($numNext)) {
					$numNext = 0;
				}

				$array[$key]['varsValue']['numNext'] = $numNext;
				$array[$key]['varsColumnDetail']['numNext'] = number_format($numNext);

				//numDiff
				$numDiff = $numNext - $numBudget;
				$array[$key]['varsValue']['numDiff'] = $numDiff;
				$array[$key]['varsColumnDetail']['numDiff'] = number_format($numDiff);

				//numRate
				$numRate = 0;
				if ($numBudget != 0) {
					$numData =  (1 - (($numBudget - $numNext) / $numBudget)) * 100;
					$numRate = ceil($numData);
				}

				$array[$key]['varsValue']['numRate'] = number_format($numRate, 3);
				$array[$key]['varsColumnDetail']['numRate'] = number_format($numRate, 3);
				$array[$key]['vars']['numRate'] = number_format($numRate, 3, '.', '');

			}

			if ($value['child']) {
				$array[$key]['child'] = $this->_getAccountTitleValueColumn(array(
					'varsFlag'    => $arr['varsFlag'],
					'varsFS'      => $array[$key]['child'],
					'varsFSValue' => $arr['varsFSValue'],
					'varsBudget'  => $arr['varsBudget'],
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
		global $varsPluginAccountingAccount;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsFlag = array(
			'flagFiscalPeriod' => $varsRequest['query']['jsonValue']['vars']['FlagFiscalPeriod'],
			'idDepartment'     => $varsRequest['query']['jsonValue']['vars']['IdDepartment'],
			'flagFS'           => $varsRequest['query']['jsonValue']['vars']['FlagFS'],
			'flagUnit'         => (int) $varsRequest['query']['jsonValue']['vars']['FlagUnit'],
			'flagCalc'         => $varsRequest['query']['jsonValue']['vars']['FlagCalc'],
		);

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
		));

		$this->_checkValueFS(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
			'varsItem' => $varsItem,
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$vars['portal']['varsDetail']['varsDetail'] = $this->_updateVarsDetail((array(
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
			'varsFlag' => $varsFlag,
		));

		$this->sendVars(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'varsDetail' => $vars['portal']['varsList']['varsDetail'],
				'varsIni'    => $vars['portal']['varsList']['varsIni'],
				'varsHtml'   => $vars['portal']['varsList']['varsHtml'],
				'varsColumn' => $vars['portal']['varsList']['tableTree']['varsDetail']['varsColumn'],
				'varsFlag'   => $varsFlag,
			),
		));
	}

	/**
		(array(
			'vars' => array(),
			'flagFS' =>
		))
	 */
	protected function _checkValueFS($arr)
	{
		$flagFS = $arr['varsFlag']['flagFS'];
		$array = $arr['vars']['varsItem']['arrayFS'];
		$flag = 0;
		foreach ($array as $key => $value) {
			if ($key == 'CR' && !$arr['varsItem']['varsEntityNation']['flagCR']) {
				continue;
			}
			if ($key == $flagFS) {
				$flag = 1;
			}
		}

		if (!$flag) {
			$this->sendValue(array(
				'flag'    => 8,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}

		return $flagFS;
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
	protected function _iniListOutput()
	{
		$this->_setClassExt(array('strClass' => 'BudgetOutput'));
	}

	/**
	 *
	 */
	protected function _iniListReload()
	{
		$this->_setSearch();
	}
}
