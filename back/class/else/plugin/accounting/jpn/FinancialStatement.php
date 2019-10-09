<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_FinancialStatement extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference' => 'financialStatementWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/financialStatement.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/financialStatement.php',
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
		global $varsPluginAccountingAccount;

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
			'vars'     => $vars,
			'varsFlag' => $vars['varsFlag'],
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

		if ($arr['varsFlag']['idDepartment'] != 'none') {
			$varsFSValue = $this->_getVarsFSValueDepartment(array(
				'idDepartment'    => $arr['varsFlag']['idDepartment'],
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			));
		}

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$data = array(
			'varsFS'           => $varsFS,
			'varsFSValue'      => $varsFSValue,
			'varsDepartment'   => $varsDepartment,
			'varsEntityNation' => $varsEntityNation,
		);

		return $data;

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
		$varsEntityNation = &$arr['varsItem']['varsEntityNation'];
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
					'vars'             => $value,
					'varsItem'         => $varsItem,
					'varsEntityNation' => $varsEntityNation,
				));

			}
			$arrayNew[] = $value;
		}
		$vars['portal']['varsNavi']['templateDetail'] = $arrayNew;


		return $vars['portal']['varsNavi']['templateDetail'];
	}

	/**
		(array(
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
		$varsEntityNation = $arr['varsEntityNation'];

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
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _updateVarsNaviFlagFiscalPeriod($arr)
	{
		$varsEntityNation = $arr['varsEntityNation'];

		$arrayNew = array();
		$array = $arr['vars']['varsTmpl']['arrayOption'];
		foreach ($array as $key => $value) {
			if ($varsEntityNation['numFiscalTermMonth'] == 12) {
				$arrayNew[] = $value;

			} else {
				if (preg_match( "/^(f1)$/", $value['value'])) {
					$arrayNew[] = $value;
				}
			}
		}
		$arr['vars']['varsTmpl']['arrayOption'] = $arrayNew;

		$arrayOption = array();
		if ($varsEntityNation['numFiscalTermMonth'] == 12) {
			$arrayOption = $arr['vars']['varsTmpl']['arrayOption'];

		} else {
			$arrayOption[] = $arr['vars']['varsTmpl']['varsPeriod'];
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
			'varsFlag' => $vars['varsFlag'],
		))
	 */
	protected function _updateVars($arr)
	{
		global $classHtml;

		global $varsAccount;

		$varsFS = $arr['varsItem']['varsFS']['jsonJgaapFS' . $arr['varsFlag']['flagFS']];

		if ($arr['varsFlag']['flagFS'] == 'BS' && $arr['varsFlag']['idDepartment'] != 'none') {
			$varsDepartmentTreeItem = $this->_getVarsDepartmentTreeItem();
			$arrayNew = array();
			$array = $varsFS;
			foreach ($array as $key => $value) {
				$arrayNew[] = $value;
				if ($value['vars']['idTarget'] == 'netAssetsSum') {
					$arrayNew[] = $varsDepartmentTreeItem;
				}
			}
			$varsFS = $arrayNew;
		}

		$arr['vars']['portal']['varsList']['varsDetail'] = $this->_getAccountTitleValueColumn(array(
			'vars'        => $arr['vars'],
			'varsFS'      => $varsFS,
			'varsFSValue' => $arr['varsItem']['varsFSValue']['jsonJgaapFS' . $arr['varsFlag']['flagFS']],
			'varsFlag'    => $arr['varsFlag'],
		));

		if (!$arr['varsFlag']['flagZero']) {
			$arr['vars']['portal']['varsList']['varsDetail'] = $this->_getAccountTitleValueZero(array(
				'varsFS' => $arr['vars']['portal']['varsList']['varsDetail'],
			));
		}

		$varsDetail = $this->_setTreeId(array(
			'idParent' => '-',
			'vars'     => $arr['vars']['portal']['varsList']['varsDetail'],
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

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllOutput'])) {
			$arr['vars']['portal']['varsList']['varsStart']['varsEdit']['flagOutputUse'] = 0;
			$arr['vars']['portal']['varsList']['varsStart']['varsEdit']['flagPrintUse'] = 0;
		}

		return $arr['vars'];
	}

	/**
		(array(

		))
	 */
	protected function _getAccountTitleValueZero($arr)
	{
		$array = &$arr['varsFS'];
		foreach ($array as $key => $value) {
			$flag = 0;
			if (!is_null($array[$key]['vars']['varsValue']) && is_null($array[$key]['vars']['flagCalc'])) {
				if ($array[$key]['vars']['varsValue']['sumDebit'] == 0
					&& $array[$key]['vars']['varsValue']['sumCredit'] == 0
					&& $array[$key]['vars']['varsValue']['sumNext'] == 0
					&& $array[$key]['vars']['idTarget'] != 'commonStock'
					&& $array[$key]['vars']['idTarget'] != 'unappropriatedRetainedEarnings'
				) {
					$flag = 1;
				}
				if ($flag) {
					unset($array[$key]);
				}
			}

			if ($array[$key]['child']) {
				$array[$key]['child'] = $this->_getAccountTitleValueZero(array(
					'varsFS' => $array[$key]['child'],
				));
			}
		}

		$arrayTemp = array();
		foreach ($array as $key => $value) {
			$arrayTemp[] = $value;
		}
		$array = $arrayTemp;

		return $array;
	}

	/**
		(array(
			'vars'        => $arr['vars'],
			'varsFS'      => $varsFS,
			'varsFSValue' => $arr['varsItem']['varsFSValue']['jsonJgaapFS' . $arr['varsFlag']['flagFS']],
			'varsFlag'    => $arr['varsFlag'],
		))

	 */
	protected function _getAccountTitleValueColumn($arr)
	{
		$array = &$arr['varsFS'];

		$flagUnit = (int) $arr['varsFlag']['flagUnit'];
		$flagFiscalPeriod = $arr['varsFlag']['flagFiscalPeriod'];
		$flagCalc = $arr['varsFlag']['flagCalc'];
		$varsFSValue = $arr['varsFSValue'];

		foreach ($array as $key => $value) {
			$array[$key]['varsColumnDetail']['numValue'] = '';
			$array[$key]['flagBtnUse'] = 1;
			$array[$key]['varsPrint']['strTitle'] = $value['strTitle'];
			$array[$key]['varsPrint']['sumNext'] = '';
			$array[$key]['varsPrint']['flagHide'] = 0;

			if (!is_null($value['vars']['flagUse'])) {
				//flagUse
				if ((int) $value['vars']['flagUse']) {

				} else {
					$array[$key]['varsPrint']['flagHide'] = 1;
					$array[$key]['strClassFont'] = $arr['vars']['varsItem']['strClassNone'];
				}
			}

			if (!is_null($array[$key]['vars']['varsValue'])) {
				$idTarget = $value['vars']['idTarget'];
				$numData = $varsFSValue[$flagFiscalPeriod][$idTarget]['sumNext'];
				if (!is_null($numData)) {
					$numData =  $numData;
					if ($flagUnit == 0) {
						$numValue = $numData;

					} else {
						if ($flagCalc == 'floor') {
							$numValue = floor($numData / $flagUnit);

						} elseif ($flagCalc == 'round') {
							$numValue = round($numData / $flagUnit);

						} elseif ($flagCalc == 'ceil') {
							$numValue = ceil($numData / $flagUnit);
						}

					}
				} else {
					$numValue = 0;
				}
				$array[$key]['vars']['varsValue']['sumNext'] = $numValue;
				$array[$key]['varsColumnDetail']['numValue'] = number_format($numValue);
				$array[$key]['varsPrint']['sumNext'] = number_format($numValue);
			}

			if ($value['child']) {
				$array[$key]['child'] = $this->_getAccountTitleValueColumn(array(
					'varsFlag'    => $arr['varsFlag'],
					'vars'        => $arr['vars'],
					'varsFS'      => $array[$key]['child'],
					'varsFSValue' => $arr['varsFSValue'],
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
			'flagZero'         => $varsRequest['query']['jsonValue']['vars']['FlagZero'],
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
			'varsFlag' => $varsFlag,
		));

		$this->sendVars(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'varsDetail' => $vars['portal']['varsList']['varsDetail'],
				'varsHtml'   => $vars['portal']['varsList']['varsHtml'],
				'varsColumn' => $vars['portal']['varsList']['tableTree']['varsDetail']['varsColumn'],
			),
		));
	}

	/**
		(array(
			'varsDetail' => $vars['portal']['varsNavi']['varsDetail'],
			'varsFlag'   => $varsFlag,
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

	/**
	 *
	 */
	protected function _iniListPrint()
	{
		$this->_setClassExt(array('strClass' => 'FinancialStatementOutput'));
	}

	/**
	 *
	 */
	protected function _iniListOutput()
	{
		$this->_setClassExt(array('strClass' => 'FinancialStatementOutput'));
	}
}
