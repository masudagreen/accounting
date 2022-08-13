<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_2012_DetailedAccount_EmployeeOutput extends Code_Else_Plugin_Accounting_Jpn_2012_DetailedAccount_Employee
{
	protected $_childSelf = array(
		'pathVarsPrint' => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/2012/detailedAccount/employeePrint.php',
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

			$arrayValue = array(
				'ValueRewardSum',
				'ValueEmployee',
				'ValueEmployeeRegular',
				'ValueEmployeePrev',
				'ValueEmployeeProfit',
				'ValueOthers',
				'Value',
			);
			foreach ($arrayValue as $keyValue => $valueValue) {
				$dataValue = $varsValue['valueText' . $valueValue . $i];
				$dataValue = ($dataValue === '' || $dataValue == 0)? $arr['varsData']['strBlank'] : number_format($dataValue);
				$varsStr['strText' . $valueValue] = $dataValue;
			}

			$dataValue = (int) $varsValue['valueSelectType' . $i];

			$varsStr['strSelectType1'] = $arr['varsData']['strType1'];
			$varsStr['strSelectType2'] = $arr['varsData']['strType2'];
			if ($dataValue == 1) {
				$strSelect = str_replace('<%replace%>', $arr['varsData']['strType1'], $arr['varsData']['strSelect']);
				$varsStr['strSelectType1'] = $strSelect;

			} elseif ($dataValue == 2) {
				$strSelect = str_replace('<%replace%>', $arr['varsData']['strType2'], $arr['varsData']['strSelect']);
				$varsStr['strSelectType2'] = $strSelect;
			}


			$tmplRow['numTr'] = 2;
			$tmplRow['strRow'] = $this->_getVarsHtml(array(
				'varsData' => $arr['varsData'],
				'value'    => $varsStr,
				'tmplStr'  => $varsPrintItem['tmplRow']['tmplTr1'],
			));
			$tmplRow['strRow'] .= $this->_getVarsHtml(array(
				'varsData' => $arr['varsData'],
				'value'    => $varsStr,
				'tmplStr'  => $varsPrintItem['tmplRow']['tmplTr2'],
			));

			$varsPrint['varsDetail'][] = $tmplRow;
		}

		$tmplRow = $varsPrint['varsDetailTmpl'];
		$tmplRow['id'] = $arr['flagCount'] . '_' . 'sum';
		$tmplRow['numTr'] = 1;
		$varsStr = array();

		$arrayValue = array(
			'SumRewardSum',
			'SumEmployee',
			'SumEmployeeRegular',
			'SumEmployeePrev',
			'SumEmployeeProfit',
			'SumOthers',
			'Sum',
		);
		foreach ($arrayValue as $keyValue => $valueValue) {
			$dataValue = $varsValue['valueText' . $valueValue];
			$dataValue = ($dataValue === '' || $dataValue == 0)? $arr['varsData']['strBlank'] : number_format($dataValue);
			$varsStr['strText' . $valueValue] = $dataValue;
		}

		$tmplRow['strRow'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'value'    => $varsStr,
			'tmplStr'  => $varsPrintItem['tmplRow']['tmplTrSum'],
		));
		$varsPrint['varsDetail'][] = $tmplRow;

		$tmplRow = $varsPrint['varsDetailTmpl'];
		$tmplRow['id'] = $arr['flagCount'] . '_' . 'Bottom1';
		$tmplRow['numTr'] = 5;
		$tmplRow['flagColumnNone'] = 1;
		$tmplRow['idTmplTable'] = 'tmplTable';
		$varsStr = array();
		$arrayValue = array(
			'ValueAll',
			'ValueAllElse',
		);
		for ($i = 1; $i <= 3; $i++) {
			foreach ($arrayValue as $keyValue => $valueValue) {
				$dataValue = $varsValue['valueText' . $valueValue . $i];
				$dataValue = ($dataValue === '' || $dataValue == 0)? $arr['varsData']['strBlank'] : number_format($dataValue);
				$varsStr['strText' . $valueValue . $i] = $dataValue;
			}
		}
		$arrayValue = array(
			'SumAll',
			'SumAllElse',
		);

		foreach ($arrayValue as $keyValue => $valueValue) {
			$dataValue = $varsValue['valueText' . $valueValue];
			$dataValue = ($dataValue === '' || $dataValue == 0)? $arr['varsData']['strBlank'] : number_format($dataValue);
			$varsStr['strText' . $valueValue] = $dataValue;
		}

		for ($i = 1; $i <= 6; $i++) {
			$tmplRow['strRow'] .= $this->_getVarsHtml(array(
				'varsData' => $arr['varsData'],
				'value'    => $varsStr,
				'tmplStr'  => $varsPrintItem['tmplRow']['tmplTrBottom' . $i],
			));
		}
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

			$rowData = array();
			$rowData[] = $arr['varsData']['strTitleEmployee'];
			$rowData = array();
			$rowData[] = '';
			$rowData[] = '';
			$rowData[] = '';
			$rowData[] = '';
			$rowData[] = '';
			$rowData[] = $arr['varsData']['strDetail'];
			$arrayCsv[] = $rowData;
			$rowData = array();
			$rowData[] = '';
			$rowData[] = '';
			$rowData[] = '';
			$rowData[] = '';
			$rowData[] = '';
			$rowData[] = '';
			$rowData[] = '';
			$rowData[] = $arr['varsData']['strEmployeeElse'];
			$arrayCsv[] = $rowData;
			$rowData = array();
			$rowData[] = $arr['varsData']['strPosition'];
			$rowData[] = $arr['varsData']['strName'];
			$rowData[] = $arr['varsData']['strRelation'];
			$rowData[] = $arr['varsData']['strAddress'];
			$rowData[] = $arr['varsData']['strType'];
			$rowData[] = $arr['varsData']['strValueRewardSum'];
			$rowData[] = $arr['varsData']['strValueEmployee'];
			$rowData[] = $arr['varsData']['strValueEmployeeRegular'];
			$rowData[] = $arr['varsData']['strValueEmployeePrev'];
			$rowData[] = $arr['varsData']['strValueEmployeeProfit'];
			$rowData[] = $arr['varsData']['strValueOthers'];
			$rowData[] = $arr['varsData']['strValue'];
			$arrayCsv[] = $rowData;
		}

		$array = $arr['varsItem']['varsCommon']['varsTmpl'][$flagMenu];
		$numEnd = $arr['varsData']['numRows'];
		for ($i = 1; $i <= $numEnd; $i++) {

			$strTextPosition = $classEscape->toComma(array('data' => $varsValue['valueTextPosition' . $i]));
			$strTextName = $classEscape->toComma(array('data' => $varsValue['valueTextName' . $i]));
			$strTextRelation = $classEscape->toComma(array('data' => $varsValue['valueTextRelation' . $i]));
			$strTextAddress = $classEscape->toComma(array('data' => $varsValue['valueTextAddress' . $i]));

			$strTextType = '';
			if ((int) $varsValue['valueSelectType' . $i] == 1) {
				$strTextType = $arr['varsData']['strType1'];

			} elseif ((int) $varsValue['valueSelectType' . $i] == 2) {
				$strTextType = $arr['varsData']['strType2'];
			}

			$dataValue = $varsValue['valueTextValueRewardSum' . $i];
			$dataValue = ($dataValue === '' || $dataValue == 0)?  '' : $dataValue;
			$strTextValueRewardSum = $dataValue;

			$dataValue = $varsValue['valueTextValueEmployee' . $i];
			$dataValue = ($dataValue === '' || $dataValue == 0)?  '' : $dataValue;
			$strTextValueEmployee = $dataValue;

			$dataValue = $varsValue['valueTextValueEmployeePrev' . $i];
			$dataValue = ($dataValue === '' || $dataValue == 0)?  '' : $dataValue;
			$strTextValueEmployeePrev = $dataValue;

			$dataValue = $varsValue['valueTextValueEmployeeRegular' . $i];
			$dataValue = ($dataValue === '' || $dataValue == 0)?  '' : $dataValue;
			$strTextValueEmployeeRegular = $dataValue;

			$dataValue = $varsValue['valueTextValueEmployeeProfit' . $i];
			$dataValue = ($dataValue === '' || $dataValue == 0)?  '' : $dataValue;
			$strTextValueEmployeeProfit = $dataValue;

			$dataValue = $varsValue['valueTextValueOthers' . $i];
			$dataValue = ($dataValue === '' || $dataValue == 0)?  '' : $dataValue;
			$strTextValueOthers = $dataValue;

			$dataValue = $varsValue['valueTextValue' . $i];
			$dataValue = ($dataValue === '' || $dataValue == 0)?  '' : $dataValue;
			$strTextValue = $dataValue;

			$arrayCsv[] = array(
				$strTextPosition,
				$strTextName,
				$strTextRelation,
				$strTextAddress,
				$strTextType,
				$strTextValueRewardSum,
				$strTextValueEmployee,
				$strTextValueEmployeeRegular,
				$strTextValueEmployeePrev,
				$strTextValueEmployeeProfit,
				$strTextValueOthers,
				$strTextValue,
			);
		}

		$dataValue = $varsValue['valueTextSumRewardSum'];
		$dataValue = ($dataValue === '' || $dataValue == 0)?  '' : $dataValue;
		$strTextSumRewardSum = $dataValue;

		$dataValue = $varsValue['valueTextSumEmployee'];
		$dataValue = ($dataValue === '' || $dataValue == 0)?  '' : $dataValue;
		$strTextSumEmployee = $dataValue;

		$dataValue = $varsValue['valueTextSumEmployeeRegular'];
		$dataValue = ($dataValue === '' || $dataValue == 0)?  '' : $dataValue;
		$strTextSumEmployeeRegular = $dataValue;

		$dataValue = $varsValue['valueTextSumEmployeePrev'];
		$dataValue = ($dataValue === '' || $dataValue == 0)?  '' : $dataValue;
		$strTextSumEmployeePrev = $dataValue;

		$dataValue = $varsValue['valueTextSumEmployeeProfit'];
		$dataValue = ($dataValue === '' || $dataValue == 0)?  '' : $dataValue;
		$strTextSumEmployeeProfit = $dataValue;

		$dataValue = $varsValue['valueTextSumOthers'];
		$dataValue = ($dataValue === '' || $dataValue == 0)?  '' : $dataValue;
		$strTextSumOthers = $dataValue;

		$dataValue = $varsValue['valueTextSum'];
		$dataValue = ($dataValue === '' || $dataValue == 0)?  '' : $dataValue;
		$strTextSum = $dataValue;

		$arrayCsv[] = array(
			$arr['varsData']['strSum'],
			'',
			'',
			'',
			'',
			$strTextSumRewardSum,
			$strTextSumEmployee,
			$strTextSumEmployeeRegular,
			$strTextSumEmployeePrev,
			$strTextSumEmployeeProfit,
			$strTextSumOthers,
			$strTextSum,
		);

		$rowData = array();
		$rowData[] = $arr['varsData']['strTitleLaborCost'];
		$rowData = array();
		$rowData[] = '';
		$rowData[] = $arr['varsData']['strCategory'];
		$rowData[] = $arr['varsData']['strValueAll'];
		$rowData[] = $arr['varsData']['strValueAllElse'];
		$arrayCsv[] = $rowData;

		$dataValue = $varsValue['valueTextValueAll1'];
		$dataValue = ($dataValue === '' || $dataValue == 0)?  '' : $dataValue;
		$strTextValueAll1 = $dataValue;

		$dataValue = $varsValue['valueTextValueAllElse1'];
		$dataValue = ($dataValue === '' || $dataValue == 0)?  '' : $dataValue;
		$strTextValueAllElse1 = $dataValue;

		$rowData = array();
		$rowData[] = '';
		$rowData[] = $arr['varsData']['strDirectors'];
		$rowData[] = $strTextValueAll1;
		$rowData[] = $strTextValueAllElse1;
		$arrayCsv[] = $rowData;

		$dataValue = $varsValue['valueTextValueAll2'];
		$dataValue = ($dataValue === '' || $dataValue == 0)?  '' : $dataValue;
		$strTextValueAll2 = $dataValue;

		$dataValue = $varsValue['valueTextValueAllElse2'];
		$dataValue = ($dataValue === '' || $dataValue == 0)?  '' : $dataValue;
		$strTextValueAllElse2 = $dataValue;

		$rowData = array();
		$rowData[] = $arr['varsData']['strLabor'];
		$rowData[] = $arr['varsData']['strLaborValue'];
		$rowData[] = $strTextValueAll2;
		$rowData[] = $strTextValueAllElse2;
		$arrayCsv[] = $rowData;

		$dataValue = $varsValue['valueTextValueAll3'];
		$dataValue = ($dataValue === '' || $dataValue == 0)?  '' : $dataValue;
		$strTextValueAll3 = $dataValue;

		$dataValue = $varsValue['valueTextValueAllElse3'];
		$dataValue = ($dataValue === '' || $dataValue == 0)?  '' : $dataValue;
		$strTextValueAllElse3 = $dataValue;

		$rowData = array();
		$rowData[] = '';
		$rowData[] = $arr['varsData']['strWagesValue'];
		$rowData[] = $strTextValueAll3;
		$rowData[] = $strTextValueAllElse3;
		$arrayCsv[] = $rowData;

		$dataValue = $varsValue['valueTextSumAll'];
		$dataValue = ($dataValue === '' || $dataValue == 0)?  '' : $dataValue;
		$strTextSumAll = $dataValue;

		$dataValue = $varsValue['valueTextSumAllElse'];
		$dataValue = ($dataValue === '' || $dataValue == 0)?  '' : $dataValue;
		$strTextSumAllElse = $dataValue;

		$rowData = array();
		$rowData[] = '';
		$rowData[] = $arr['varsData']['strSum'];
		$rowData[] = $strTextSumAll;
		$rowData[] = $strTextSumAllElse;
		$arrayCsv[] = $rowData;

		return $arrayCsv;
	}
/*

		$arrayRow = array();
		$array = $arr['varsItem']['varsCommon']['varsTmpl'][$flagMenu];
		foreach ($array as $key => $value) {
			if (!$value['flagCsv']) {
				continue;
			}
			if ($value['flagLabor']) {
				continue;
			}
			if ($value['id'] == 'TextPosition') {
				$arrayRow[] = $varsData['strSum'];
				continue;

			} elseif ($value['id'] == 'TextValueRewardSum') {
				$arrayRow[] = $varsValue['valueTextSumRewardSum'];
				continue;

			} elseif ($value['id'] == 'TextValueEmployee') {
				$arrayRow[] = $varsValue['valueTextSumEmployee'];
				continue;

			} elseif ($value['id'] == 'TextValueEmployeeRegular') {
				$arrayRow[] = $varsValue['valueTextSumEmployeeRegular'];
				continue;

			} elseif ($value['id'] == 'TextValueEmployeePrev') {
				$arrayRow[] = $varsValue['valueTextSumEmployeePrev'];
				continue;

			} elseif ($value['id'] == 'TextValueEmployeeProfit') {
				$arrayRow[] = $varsValue['valueTextSumEmployeeProfit'];
				continue;

			} elseif ($value['id'] == 'TextValueOthers') {
				$arrayRow[] = $varsValue['valueTextSumOthers'];
				continue;

			} elseif ($value['id'] == 'TextValueEmployee') {
				$arrayRow[] = $varsValue['valueTextSumEmployee'];
				continue;

			} elseif ($value['id'] == 'TextValue') {
				$arrayRow[] = $varsValue['valueTextSum'];
				continue;

			}
			$arrayRow[] = '';
		}
		$varsCsv[] = $arrayRow;

		$varsCsv[] = array();
		$varsCsv[] = array($varsData['strTitleLaborCost']);
		$varsCsv[] = array('', $varsData['strCategory'], $varsData['strValueAll'], $varsData['strValueAllElse']);
		$varsCsv[] = array('', $varsData['strDirectors'], $varsValue['valueTextValueAll1'], $varsValue['valueTextValueAllElse1']);
		$varsCsv[] = array($varsData['strLabor'], $varsData['strLaborValue'], $varsValue['valueTextValueAll2'], $varsValue['valueTextValueAllElse2']);
		$varsCsv[] = array('', $varsData['strWagesValue'], $varsValue['valueTextValueAll3'], $varsValue['valueTextValueAllElse3']);
		$varsCsv[] = array('', $varsData['strSum'], $varsValue['valueTextSumAll'], $varsValue['valueTextSumAllElse']);

 */

}
