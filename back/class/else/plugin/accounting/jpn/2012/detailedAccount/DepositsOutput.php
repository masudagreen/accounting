<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_2012_DetailedAccount_DepositsOutput extends Code_Else_Plugin_Accounting_Jpn_2012_DetailedAccount_Deposits
{
	protected $_childSelf = array(
		'pathVarsPrint' => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/2012/detailedAccount/depositsPrint.php',
	);

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
			'flagCount'  => $arr['flagCount'],
			'flagFirst'  => 1,
			'varsData'   => $varsData,
			'varsPrint'  => $varsPrint,
			'vars'       => $arr['vars'],
			'varsItem'   => $arr['varsItem'],
			'varsFlag'   => $arr['varsFlag'],
		))

	 */
	protected function _getVarsLoopPrint($arr)
	{
		$flagMenu = $arr['varsFlag']['flagMenu'];
		$varsValue = $arr['varsItem']['varsSave']['jsonData'][$flagMenu];

		$varsPrint = &$arr['varsPrint'];
		$varsPrintItem = $arr['varsItem']['varsPrintItem'];

		$array = $arr['varsItem']['varsCommon']['varsTmpl'][$flagMenu];
		$numEnd = $arr['varsData']['numRows'];
		for ($i = 1; $i <= $numEnd; $i++) {
			$tmplRow = $varsPrint['varsDetailTmpl'];
			if ($arr['flagFirst']) {
				$arr['flagFirst'] = 0;
				if ($arr['flagCount']) {
					$tmplRow['flagBreak'] = 1;
					$tmplRow['idTmplTableTop'] = 'tmplTableTop';
				}
			}
			$tmplRow['id'] = $arr['flagCount'] . '_' . $i;

			$varsStr = array();
			foreach ($array as $key => $value) {
				if (!$value['flagLoop']) {
					continue;
				}
				$idValueTarget = 'value' . $value['id'] . $i;
				$idStrTarget = 'str' . $value['id'];
				$varsStr[$idStrTarget] = (!$varsValue[$idValueTarget])? $arr['varsData']['strBlank'] : $varsValue[$idValueTarget];
			}

			$dataValue = $varsValue['valueTextValue' . $i];
			$dataValue = ($dataValue === '' || $dataValue == 0)? $arr['varsData']['strBlank'] : number_format($dataValue);

			$tmplRow['numTr'] = 1;
			$tmplRow['strRow'] = $this->_getVarsHtml(array(
				'varsData' => $arr['varsData'],
				'value'    => $varsStr,
				'tmplStr'  => $varsPrintItem['tmplRow']['tmplTr1'],
			));

			$varsPrint['varsDetail'][] = $tmplRow;
		}

		$tmplRow = $varsPrint['varsDetailTmpl'];
		$tmplRow['id'] = $arr['flagCount'] . '_' . 'sum';
		$tmplRow['numTr'] = 1;
		$varsStr = array();
		$dataValue = $varsValue['valueTextSum'];
		$dataValue = ($dataValue === '' || $dataValue == 0)? $arr['varsData']['strBlank'] : number_format($dataValue);
		$varsStr['strTextSum'] = $dataValue;

		$tmplRow['strRow'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'value'    => $varsStr,
			'tmplStr'  => $varsPrintItem['tmplRow']['tmplTr2'],
		));
		$varsPrint['varsDetail'][] = $tmplRow;

		$tmplRow = $varsPrint['varsDetailTmpl'];
		$tmplRow['id'] = $arr['flagCount'] . '_' . 'strCautionLaw';
		$tmplRow['numTr'] = 1;
		$tmplRow['flagColumnNone'] = 1;
		$tmplRow['idTmplTable'] = 'tmplTableBottom';
		$varsStr = array();
		$tmplRow['strRow'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'value'    => $varsStr,
			'tmplStr'  => $varsPrintItem['tmplRow']['tmplTrCautionLaw'],
		));
		$varsPrint['varsDetail'][] = $tmplRow;

		$tmplRow = $varsPrint['varsDetailTmpl'];
		$tmplRow['id'] = $arr['flagCount'] . '_' . 'strCautionMark';
		$tmplRow['numTr'] = 1;
		$tmplRow['flagColumnNone'] = 1;
		$tmplRow['idTmplTable'] = 'tmplTableBottomCaution';
		$varsStr = array();
		$tmplRow['strRow'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'value'    => $varsStr,
			'tmplStr'  => $varsPrintItem['tmplRow']['tmplTrCaution'],
		));
		$varsPrint['varsDetail'][] = $tmplRow;

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
			$rowData = $this->_getVarsLoopCsvColumn(array(
				'varsData' => $arr['varsData'],
			));
			$arrayCsv[] = $rowData;
		}

		$array = $arr['varsItem']['varsCommon']['varsTmpl'][$flagMenu];
		$numEnd = $arr['varsData']['numRows'];
		for ($i = 1; $i <= $numEnd; $i++) {

			$strTextBank = (!$varsValue['valueTextBank' . $i])? '' : $varsValue['valueTextBank' . $i];
			$strTextBranch = (!$varsValue['valueTextBranch' . $i])? '' : $varsValue['valueTextBranch' . $i];

			$strTextType = $varsValue['valueTextType' . $i];
			$strTextAccount = $varsValue['valueTextAccount' . $i];

			$dataValue = $varsValue['valueTextValue' . $i];
			$dataValue = ($dataValue === '' || $dataValue == 0)?  '' : $dataValue;
			$strTextValue = $dataValue;

			$strTextMemo = $varsValue['valueTextMemo' . $i];

			$arrayCsv[] = array($strTextBank, $strTextBranch, $strTextType, $strTextAccount, $strTextValue, $strTextMemo);
		}

		$dataValue = $varsValue['valueTextSum'];
		$dataValue = ($dataValue === '' || $dataValue == 0)?  '' : $dataValue;
		$strTextSum = $dataValue;

		$arrayCsv[] = array($arr['varsData']['strSum'], '', '', '', $strTextSum, '');

		return $arrayCsv;
	}

	protected function _getVarsLoopCsvColumn($arr)
	{
		$rowData = array();
		$rowData[] = $arr['varsData']['strBank'];
		$rowData[] = '';
		$rowData[] = $arr['varsData']['strType'];
		$rowData[] = $arr['varsData']['strAccount'];
		$rowData[] = $arr['varsData']['strValue'];
		$rowData[] = $arr['varsData']['strMemo'];

		return $rowData;
	}

}
