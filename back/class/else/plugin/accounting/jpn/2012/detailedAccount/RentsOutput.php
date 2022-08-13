<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_2012_DetailedAccount_RentsOutput extends Code_Else_Plugin_Accounting_Jpn_2012_DetailedAccount_Rents
{
	protected $_childSelf = array();

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;

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

	protected function _iniDetailOutput()
	{
		$this->_extDetailOutput();
	}

	protected function _iniDetailPrint()
	{
		$this->_extDetailPrint();
	}

	/**
		(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
			'varsItem' => $varsItem,
		))

	 */
	protected function _getVarsPrint($arr)
	{
		global $varsPluginAccountingAccount;

		$varsData = $this->_getVarsStatus(array(
			'vars'     => $arr['vars'],
			'varsFlag' => $arr['varsFlag'],
			'varsItem' => $arr['varsItem'],
		));

		$class15Output = $this->_getClassExtra(array('strClass' => '15Output'));
		$varsPrint = $class15Output->allot(array(
			'flagStatus'      => 'print',
			'varsPrint'       => $arr['vars']['varsPrint'],
			'varsFlag'        => $arr['varsFlag'],
			'varsData'        => $varsData,
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		return $varsPrint;
	}

	/**
		(array(
			'flagCount'  => $arr['flagCount'],
			'flagFirst'  => 1,
			'varsData'   => $varsData,
			'arrayCsv'   => $arr['arrayCsv'],
			'vars'       => $arr['vars'],
			'varsItem'   => $arr['varsItem'],
			'varsFlag'   => $arr['varsFlag'],
		))
	 */
	protected function _getVarsLoopCsv($arr)
	{
		global $classEscape;

		$flagMenu = $arr['varsFlag']['flagMenu'];
		$varsValue = $arr['varsItem']['varsSave']['jsonData'][$flagMenu];

		$arrayCsv = &$arr['arrayCsv'];

		if ($arr['flagFirst']) {
			$arr['flagFirst'] = 0;
			if (!$arr['flagCount']) {
				$arrayCsv[] = array($classEscape->toComma(array('data' => $arr['varsData']['strEntityExt'])));
				$arrayCsv[] = array($arr['varsData']['strNumExt']);
			}

			$arrayCsv[] = array();
			$arrayCsv[] = array($arr['varsData']['strPageExt']);

			$rowData = array();
			$rowData[] = $arr['varsData']['strCategory'];
			$rowData[] = $arr['varsData']['strUse'];
			$rowData[] = $arr['varsData']['strPlace'];
			$rowData[] = $arr['varsData']['strName'];
			$rowData[] = $arr['varsData']['strAddress'];
			$rowData[] = $arr['varsData']['strSpan'];
			$rowData[] = '';
			$rowData[] = '';
			$rowData[] = '';
			$rowData[] = '';
			$rowData[] = '';
			$rowData[] = $arr['varsData']['strValue'];
			$rowData[] = $arr['varsData']['strMemo'];
			$arrayCsv[] = $rowData;
		}

		$array = $arr['varsItem']['varsCommon']['varsTmpl'][$flagMenu];
		$numEnd = $arr['varsData']['numRows'];
		for ($i = 1; $i <= $numEnd; $i++) {
			$strTextCategory = $classEscape->toComma(array('data' => $varsValue['valueTextCategory' . $i]));
			$strTextUse = $classEscape->toComma(array('data' => $varsValue['valueTextUse' . $i]));
			$strTextAddress = $classEscape->toComma(array('data' => $varsValue['valueTextAddress' . $i]));
			$strTextPlace = $classEscape->toComma(array('data' => $varsValue['valueTextPlace' . $i]));

			$strTextName = $classEscape->toComma(array('data' => $varsValue['valueTextName' . $i]));
			$strTextRelation = $classEscape->toComma(array('data' => $varsValue['valueTextRelation' . $i]));

			$strTextYearStart = $classEscape->toComma(array('data' => $varsValue['valueTextYearStart' . $i]));
			$strTextMonthStart = $classEscape->toComma(array('data' => $varsValue['valueTextMonthStart' . $i]));
			$strTextDateStart = $classEscape->toComma(array('data' => $varsValue['valueTextDateStart' . $i]));

			$strTextYearEnd = $classEscape->toComma(array('data' => $varsValue['valueTextYearEnd' . $i]));
			$strTextMonthEnd = $classEscape->toComma(array('data' => $varsValue['valueTextMonthEnd' . $i]));
			$strTextDateEnd = $classEscape->toComma(array('data' => $varsValue['valueTextDateEnd' . $i]));

			$dataValue = $varsValue['valueTextValue' . $i];
			$dataValue = ($dataValue === '' || $dataValue == 0)?  '' : $dataValue;
			$strTextValue = $dataValue;

			$strTextMemo = $classEscape->toComma(array('data' => $varsValue['valueTextMemo' . $i]));

			$arrayCsv[] = array(
				$strTextCategory,
				$strTextUse,
				$strTextPlace,
				$strTextName,
				$strTextAddress,
				$strTextYearStart,
				$strTextMonthStart,
				$strTextDateStart,
				$strTextYearEnd,
				$strTextMonthEnd,
				$strTextDateEnd,
				$strTextValue,
				$strTextMemo,
			);
		}

		$dataValue = $varsValue['valueTextSum'];
		$dataValue = ($dataValue === '' || $dataValue == 0)?  '' : $dataValue;
		$strTextSum = $dataValue;

		$arrayCsv[] = array($arr['varsData']['strSum'], '', '', '', '', '', '', '', '', '', '', $strTextSum);


		return $arrayCsv;
	}


}
