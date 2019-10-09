<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_2012_DetailedAccount_IndustrialPropertyOutput extends Code_Else_Plugin_Accounting_Jpn_2012_DetailedAccount_IndustrialProperty
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
			$rowData[] = '';
			$rowData[] = '';
			$rowData[] = '';
			$rowData[] = '';
			$rowData[] = '';
			$rowData[] = '';
			$rowData[] = '';
			$rowData[] = $arr['varsData']['strPay'];
			$rowData[] = '';
			$rowData[] = '';
			$rowData[] = '';
			$rowData[] = '';
			$rowData[] = '';
			$arrayCsv[] = $rowData;
			$rowData = array();
			$rowData[] = $arr['varsData']['strLicense'];
			$rowData[] = $arr['varsData']['strAddress'];
			$rowData[] = $arr['varsData']['strName'];
			$rowData[] = $arr['varsData']['strSpan'];
			$rowData[] = '';
			$rowData[] = '';
			$rowData[] = '';
			$rowData[] = $arr['varsData']['strSpanPay'];
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
			$strTextLicense = $classEscape->toComma(array('data' => $varsValue['valueTextLicense' . $i]));
			$strTextAddress = $classEscape->toComma(array('data' => $varsValue['valueTextAddress' . $i]));
			$strTextName = $classEscape->toComma(array('data' => $varsValue['valueTextName' . $i]));
			$strTextYearSpanStart = $classEscape->toComma(array('data' => $varsValue['valueTextYearSpanStart' . $i]));
			$strTextMonthSpanStart = $classEscape->toComma(array('data' => $varsValue['valueTextMonthSpanStart' . $i]));
			$strTextYearSpanEnd = $classEscape->toComma(array('data' => $varsValue['valueTextYearSpanEnd' . $i]));
			$strTextMonthSpanEnd = $classEscape->toComma(array('data' => $varsValue['valueTextMonthSpanEnd' . $i]));

			$strTextYearSpanPayStart = $classEscape->toComma(array('data' => $varsValue['valueTextYearSpanPayStart' . $i]));
			$strTextMonthSpanPayStart = $classEscape->toComma(array('data' => $varsValue['valueTextMonthSpanPayStart' . $i]));
			$strTextYearSpanPayEnd = $classEscape->toComma(array('data' => $varsValue['valueTextYearSpanPayEnd' . $i]));
			$strTextMonthSpanPayEnd = $classEscape->toComma(array('data' => $varsValue['valueTextMonthSpanPayEnd' . $i]));

			$dataValue = $varsValue['valueTextValue' . $i];
			$dataValue = ($dataValue === '' || $dataValue == 0)?  '' : $dataValue;
			$strTextValue = $dataValue;

			$strTextMemo = $classEscape->toComma(array('data' => $varsValue['valueTextMemo' . $i]));

			$arrayCsv[] = array(
				$strTextLicense,
				$strTextAddress,
				$strTextName,
				$strTextYearSpanStart,
				$strTextMonthSpanStart,
				$strTextYearSpanEnd,
				$strTextMonthSpanEnd,
				$strTextYearSpanPayStart,
				$strTextMonthSpanPayStart,
				$strTextYearSpanPayEnd,
				$strTextMonthSpanPayEnd,
				$strTextValue,
				$strTextMemo,
			);
		}

		$dataValue = $varsValue['valueTextSum'];
		$dataValue = ($dataValue === '' || $dataValue == 0)?  '' : $dataValue;
		$strTextSum = $dataValue;

		$arrayCsv[] = array($arr['varsData']['strSum'], '', '', '', '', '', '', '', '', '', '', $strTextSum, '');


		return $arrayCsv;
	}


}
