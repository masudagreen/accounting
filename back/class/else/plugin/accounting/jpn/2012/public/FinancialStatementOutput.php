<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_FinancialStatementOutput_2012_Public extends Code_Else_Plugin_Accounting_Jpn_FinancialStatementOutput
{

	protected $_childSelf = array(
		'pathVarsPrint'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/printFinancialStatement.php',
		'pathVarsPrintBS' => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/2012/public/printFinancialStatementBS.php',
	);

	/**
		(array(
			'strFileType'   => '',
			'varsFlag'   => $arr['varsFlag'],
			'varsItem'   => $arr['varsItem'],
			'vars'       => $arr['vars'],
		))

	 */
	protected function _updateVarsItem($arr)
	{
		$varsPrintItem = $this->_getVarsPrintItem();
		$varsPrintItemBS = $this->_getVarsPrintItemBS();

		$vars = &$arr['vars'];
		$varsFlagFiscalPeriod = array();
		$varsFlagUnit = array();
		$varsFlagFS = array();

		$array = &$vars['portal']['varsNavi']['templateDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'FlagFiscalPeriod') {
				$arrStrTitle = array();
				$arrayOption = $value['arrayOption'];
				foreach ($arrayOption as $keyOption => $valueOption) {
					$arrStrTitle[$valueOption['value']]['strTitle'] = $valueOption['strTitle'];
				}
				$varsFlagFiscalPeriod['arrStrTitle'] = $arrStrTitle;

			} elseif ($value['id'] == 'FlagUnit') {
				$arrStrTitle = array();
				$arrayOption = $value['arrayOption'];
				foreach ($arrayOption as $keyOption => $valueOption) {
					$arrStrTitle[$valueOption['value']]['strTitle'] = $valueOption['strTitle'];
				}
				$varsFlagUnit['arrStrTitle'] = $arrStrTitle;

			} elseif ($value['id'] == 'FlagFS') {
				$arrStrTitle = array();
				$arrayOption = $value['arrayOption'];
				foreach ($arrayOption as $keyOption => $valueOption) {
					$arrStrTitle[$valueOption['value']]['strTitle'] = $valueOption['strTitle'];
				}
				$varsFlagFS['arrStrTitle'] = $arrStrTitle;
			}
		}

		$arr['varsItem']['varsPrintItem'] = $varsPrintItem;
		$arr['varsItem']['varsPrintItemBS'] = $varsPrintItemBS;
		$arr['varsItem']['varsFlagFS'] = $varsFlagFS;
		$arr['varsItem']['varsFlagFiscalPeriod'] = $varsFlagFiscalPeriod;
		$arr['varsItem']['varsFlagUnit'] = $varsFlagUnit;

		return $arr['varsItem'];
	}

	/**
		(array(
			'flagCount'  => $flagCount,
			'vars'       => $arr['vars'],
			'varsItem'   => $arr['varsItem'],
			'varsFlag'   => $arr['varsFlag'],
			'varsPrint'  => $varsPrint,
			'varsFS'     => $arr['vars']['portal']['varsList']['varsDetail'],
		))
	 */
	protected function _getVarsPrintLoop($arr)
	{
		$varsData = array();

		$varsData = $this->_getVarsStatus(array(
			'vars'     => $arr['vars'],
			'varsFlag' => $arr['varsFlag'],
			'varsItem' => $arr['varsItem'],
		));

		$varsPrint = $arr['varsPrint'];
		if (!$varsPrint) {
			$varsPrint = $arr['vars']['varsPrint'];
			$varsPrint['varsStatus'] = $this->_getVarsStatusPrint(array(
				'varsData'   => $varsData,
				'vars'       => $arr['vars'],
				'varsPrint'  => $varsPrint,
				'varsItem'   => $arr['varsItem'],
				'varsFlag'   => $arr['varsFlag'],
			));
		}

		$varsPrint = $this->_getVarsLoopPrint(array(
			'flagCount'  => $arr['flagCount'],
			'flagFirst'  => 1,
			'varsData'   => $varsData,
			'varsPrint'  => $varsPrint,
			'vars'       => $arr['vars'],
			'varsFS'     => $arr['varsFS'],
			'varsItem'   => $arr['varsItem'],
			'varsFlag'   => $arr['varsFlag'],
		));

		return $varsPrint;
	}

	/**
		(array(
			'varsData'   => $varsData,
			'vars'       => $arr['vars'],
			'varsDetail' => $arr['varsDetail'],
		))
	 */
	protected function _getVarsStatusPrint($arr)
	{
		$varsPrint = $arr['varsPrint'];
		$varsPrintItem = $arr['varsItem']['varsPrintItem'];
		if ($arr['varsFlag']['flagFS'] == 'BS' && $arr['varsFlag']['flagFiscalPeriod'] == 'f1') {
			$varsPrintItem = $arr['varsItem']['varsPrintItemBS'];
		}

		//tmplWrap
		$varsPrint['varsStatus']['varsTmpl']['tmplWrap'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'tmplStr'  => $varsPrintItem['tmplWrap'],
		));

		$varsPrint['varsStatus']['varsTmpl']['tmplColumn'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'tmplStr'  => $varsPrintItem['tmplColumn'],
		));

		$varsPrint['varsStatus']['varsTmpl']['tmplTable'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'tmplStr'  => $varsPrintItem['tmplTable'],
		));

		$varsPrint['varsStatus']['varsTmpl']['tmplTableTop'] = $varsPrintItem['tmplTableTop'];

		$varsPrint['varsStatus']['varsTmpl']['tmplPage'] = $varsPrintItem['tmplPage'];

		$varsPrint['varsStatus']['strTitle'] = $this->_getStrTitle(array(
			'varsFlag' => $arr['varsFlag'],
			'varsItem' => $arr['varsItem'],
			'vars'     => $arr['vars'],
		));

		return $varsPrint['varsStatus'];
	}

	/**
		(array(
			'flagCount'  => $arr['flagCount'],
			'varsData'   => $varsData,
			'varsPrint'  => $varsPrint,
			'vars'       => $arr['vars'],
			'varsFS'     => $arr['varsFS'],
			'varsItem'   => $arr['varsItem'],
			'varsFlag'   => $arr['varsFlag'],
		))

	 */
	protected function _getVarsLoopPrint($arr)
	{
		$varsPrint = &$arr['varsPrint'];
		$varsPrintItem = $arr['varsItem']['varsPrintItem'];
		if ($arr['varsFlag']['flagFS'] == 'BS' && $arr['varsFlag']['flagFiscalPeriod'] == 'f1') {
			$varsPrintItem = $arr['varsItem']['varsPrintItemBS'];
		}

		$array = &$arr['varsFS'];
		foreach ($array as $key => $value) {

			$arrLevel = preg_split("/-/", $value['id']);
			$num = count($arrLevel) - 2;
			$strLevel = '';
			for ($i = 0 ; $i < $num; $i++) {
				$strLevel .= $arr['varsData']['strBlank'];
			}

			$value['varsPrint']['strTitle'] = $strLevel . $value['varsPrint']['strTitle'];

			$tmplRow = $varsPrint['varsDetailTmpl'];
			$tmplRow['id'] = $arr['varsFlag']['flagFS'] . $value['id'];
			$strFirst = '';
			if ($arr['flagFirst']) {
				$arr['flagFirst'] = 0;
				$strFirst = $varsPrintItem['tmplRow']['tmplTrTop'];
				if ($arr['flagCount']) {
					$tmplRow['flagBreak'] = 1;
					$tmplRow['idTmplTableTop'] = 'tmplTableTop';
				}
				$tmplRow['strTitle'] = $arr['varsData']['strTitle'];
				$tmplRow['strPeriodSub'] = $arr['varsData']['strPeriodSub'];
				$tmplRow['strTitleSub'] = $arr['varsData']['strTitleSub'];
				$tmplRow['strUnit'] = $arr['varsData']['strUnit'];
			}
			$tmplRow['numTr'] = 1;
			$tmplRow['strRow'] = $strFirst . $this->_getVarsHtml(array(
				'varsData' => $arr['varsData'],
				'value'    => $value['varsPrint'],
				'tmplStr'  => $varsPrintItem['tmplRow']['tmplTr1'],
			));



			if (!$value['varsPrint']['flagHide']) {
				$varsPrint['varsDetail'][] = $tmplRow;
			}

			if ($value['child']) {
				$data = $this->_getVarsLoopPrint(array(
					'varsFS'    => $array[$key]['child'],
					'flagCount' => $arr['flagCount'],
					'flagFirst' => $arr['flagFirst'],
					'varsData'  => $arr['varsData'],
					'varsPrint' => $arr['varsPrint'],
					'vars'      => $arr['vars'],
					'varsItem'  => $arr['varsItem'],
					'varsFlag'  => $arr['varsFlag'],
				));
				$varsPrint =  $data['varsPrint'];
			}
		}

		return $arr;
	}

	/**

	 */
	protected function _getVarsPrintItemBS()
	{
		$vars = $this->getVars(array(
			'path' => $this->_childSelf['pathVarsPrintBS'],
		));

		return $vars;
	}

	/**
		(array(
			'flagCount'  => $arr['flagCount'],
			'varsData'   => $varsData,
			'vars'       => $arr['vars'],
			'varsDetail' => $arr['varsDetail'],
			'varsItem'   => $arr['varsItem'],
			'varsFlag'   => $arr['varsFlag'],
		))
	 */
	protected function _getVarsLoopCsv($arr)
	{
		global $classEscape;

		$arrayCsv = &$arr['arrayCsv'];

		if ($arr['flagFirst']) {
			$arr['flagFirst'] = 0;
			if (!$arr['flagCount']) {
				$arrayCsv[] = array($classEscape->toComma(array('data' => $arr['varsData']['strEntityExt'])));
				$arrayCsv[] = array($arr['varsData']['strNumExt']);
				$arrayCsv[] = array($arr['varsData']['strUnit']);
			}

			$arrayCsv[] = array();
			$arrayCsv[] = array($arr['varsData']['strTitle']);
			$arrayCsv[] = array($arr['varsData']['strPeriodSub']);
			$arrayCsv[] = array($classEscape->toComma(array('data' => $arr['varsData']['strTitleSub'])));
			$rowData = $this->_getVarsLoopCsvColumn(array(
				'varsData' => $arr['varsData'],
				'varsFlag' => $arr['varsFlag'],
			));
			$arrayCsv[] = $rowData;
		}

		$array = &$arr['varsFS'];
		foreach ($array as $key => $value) {

			$strTitle = $classEscape->toComma(array('data' => $value['strTitle']));
			if (!is_null($value['vars']['varsValue'])) {

				$sumPrev = $value['vars']['varsValue']['sumPrev'];
				$sumNext = $value['vars']['varsValue']['sumNext'];
				$flag = 0;
				if (!is_null($value['vars']['flagUse'])) {
					if ((int) $value['vars']['flagUse']) {
						$flag = 1;
					}
				} else {
					$flag = 1;
				}

				if ($flag) {
					if ($arr['varsFlag']['flagFiscalPeriod'] == 'f1' && $arr['varsFlag']['flagFS'] == 'BS') {
						$arrayCsv[] = array('', $strTitle, $sumPrev, $sumNext);
					} else {
						$arrayCsv[] = array('', $strTitle, $sumNext);
					}
				}

			} else {
				$arrayCsv[] = array($strTitle);
			}

			if ($value['child']) {
				$data = $this->_getVarsLoopCsv(array(
					'varsFS'    => $array[$key]['child'],
					'flagCount' => $arr['flagCount'],
					'flagFirst' => $arr['flagFirst'],
					'varsData'  => $arr['varsData'],
					'arrayCsv'  => $arr['arrayCsv'],
					'vars'      => $arr['vars'],
					'varsItem'  => $arr['varsItem'],
					'varsFlag'  => $arr['varsFlag'],
				));
				$arrayCsv =  $data['arrayCsv'];
			}
		}

		return $arr;
	}

	protected function _getVarsLoopCsvColumn($arr)
	{
		$rowData = array();
		$rowData[] = $arr['varsData']['strAccountTitle'];
		$rowData[] = '';
		if ($arr['varsFlag']['flagFiscalPeriod'] == 'f1' && $arr['varsFlag']['flagFS'] == 'BS') {
			$rowData[] = $arr['varsData']['strStart'];
			$rowData[] = $arr['varsData']['strEnd'];
		} else {
			$rowData[] = $arr['varsData']['strNext'];
		}
		return $rowData;
	}
















	//-----------------------------------------------------------
	// Code_Else_Plugin_Accounting_Jpn_FinancialStatement _2012_Public
	//-----------------------------------------------------------

	protected $_extSelf = array(
		'idPreference' => 'financialStatementWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/financialStatement.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/2012/public/financialStatement.php',
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
						var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
					}
					exit;
				}
			}
		}
		exit;
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
			$array[$key]['varsPrint']['sumPrev'] = '';
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
				$arrayStr = array('sumPrev', 'sumNext');
				foreach ($arrayStr as $keyStr => $valueStr) {
					$numData = $varsFSValue[$flagFiscalPeriod][$idTarget][$valueStr];
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
					$flag = 0;
					if ($arr['varsFlag']['flagFS'] == 'BS' && $arr['varsFlag']['flagFiscalPeriod'] == 'f1') {
						if ($valueStr == 'sumPrev') {
							if ($idTarget == 'accountsReceivables'
								|| $idTarget == 'accountsPayables'
								|| $idTarget == 'unappropriatedRetainedEarnings'
							) {
								$flag = 1;
							}
						}
					}
					if ($flag) {
						$array[$key]['vars']['varsValue'][$valueStr] = '';
						$array[$key]['varsColumnDetail']['numValue'] = '-';
						$array[$key]['varsColumnDetail'][$valueStr] = '-';
						$array[$key]['varsPrint'][$valueStr] = '-';

					} else {
						$array[$key]['vars']['varsValue'][$valueStr] = $numValue;
						$array[$key]['varsColumnDetail']['numValue'] = number_format($numValue);
						$array[$key]['varsColumnDetail'][$valueStr] = number_format($numValue);
						$array[$key]['varsPrint'][$valueStr] = number_format($numValue);
					}

				}

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

		if ($arr['varsFlag']['flagFS'] == 'BS' && $arr['varsFlag']['flagFiscalPeriod'] == 'f1') {
			$arr['vars']['portal']['varsList']['tableTree']['varsDetail']['varsColumn']
			 = $arr['vars']['portal']['varsList']['tableTree']['varsDetail']['varsColumnBS'];
		}

		$varsFS = $arr['varsItem']['varsFS']['jsonJgaapFS' . $arr['varsFlag']['flagFS']];

		if ($arr['varsFlag']['flagFS'] == 'BS' && $arr['varsFlag']['idDepartment'] != 'none') {
			$varsDepartmentTreeItem = $this->_getVarsDepartmentTreeItem();
			$arrayNew = array();
			$array = $varsFS;
			foreach ($array as $key => $value) {
				$arrayNew[] = $value;
//unique start
				if ($value['vars']['idTarget'] == 'liabilities') {
//unique end
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
					&& $array[$key]['vars']['varsValue']['sumPrev'] == 0
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
}
