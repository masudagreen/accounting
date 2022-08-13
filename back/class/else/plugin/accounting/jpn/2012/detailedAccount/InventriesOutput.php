<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_2012_DetailedAccount_InventriesOutput extends Code_Else_Plugin_Accounting_Jpn_2012_DetailedAccount_Inventries
{
	protected $_childSelf = array(
		'pathVarsPrint' => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/2012/detailedAccount/inventriesPrint.php',
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
			'varsData'   => $varsData,
			'vars'       => $arr['vars'],
			'varsDetail' => $arr['varsDetail'],
		))
	 */
	protected function _getVarsStatusPrint($arr)
	{
		$varsPrint = $arr['varsPrint'];
		$varsPrintItem = $arr['varsItem']['varsPrintItem'];

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

		$varsPrint['varsStatus']['varsTmpl']['tmplTableTop'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'tmplStr'  => $varsPrintItem['tmplTableTop'],
		));

		$varsPrint['varsStatus']['varsTmpl']['tmplTableBottom'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'tmplStr'  => $varsPrintItem['tmplTableBottom'],
		));

		$varsPrint['varsStatus']['varsTmpl']['tmplTableBottom2'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'tmplStr'  => $varsPrintItem['tmplTableBottom2'],
		));

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
			$varsStr['strTextValue'] = $dataValue;

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
			'tmplStr'  => $varsPrintItem['tmplRow']['tmplTrSum'],
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
		$tmplRow['id'] = $arr['flagCount'] . '_' . 'strBottom';
		$tmplRow['numTr'] = 1;
		$tmplRow['flagColumnNone'] = 1;
		$tmplRow['idTmplTable'] = 'tmplTableBottom2';
		$varsStr = array();

		$dataSelectMethod = (int) $varsValue['valueSelectMethod'];
		$varsStr['strSelectA'] = $arr['varsData']['strA'] . ' ' . $arr['varsData']['strSelectA'];
		$varsStr['strSelectB'] = $arr['varsData']['strB'] . ' ' . $arr['varsData']['strSelectB'];
		$varsStr['strSelectC'] = $arr['varsData']['strC'] . ' ' . $arr['varsData']['strSelectC'];
		if ($dataSelectMethod == 1) {
			$strSelect = str_replace('<%replace%>', $arr['varsData']['strA'], $arr['varsData']['strSelect']);
			$varsStr['strSelectA'] = $strSelect . ' ' . $arr['varsData']['strSelectA'];

		} elseif ($dataSelectMethod == 2) {
			$strSelect = str_replace('<%replace%>', $arr['varsData']['strB'], $arr['varsData']['strSelect']);
			$varsStr['strSelectB'] = $strSelect . ' ' . $arr['varsData']['strSelectB'];

		} elseif ($dataSelectMethod == 3) {
			$strSelect = str_replace('<%replace%>', $arr['varsData']['strC'], $arr['varsData']['strSelect']);
			$varsStr['strSelectC'] = $strSelect . ' ' . $arr['varsData']['strSelectC'];
		}

		$varsStr['strTextTime'] = $varsValue['valueTextYear'] . $arr['varsData']['strYear'];
		if (!$varsValue['valueTextMonth']) {
			$varsStr['strTextTime'] .=  $arr['varsData']['strBlank'];
		}
		$varsStr['strTextTime'] .= $varsValue['valueTextMonth'] . $arr['varsData']['strMonth'];
		if (!$varsValue['valueTextDate']) {
			$varsStr['strTextTime'] .=  $arr['varsData']['strBlank'];
		}
		$varsStr['strTextTime'] .= $varsValue['valueTextDate'] . $arr['varsData']['strDate'];

		$tmplRow['strRow'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'value'    => $varsStr,
			'tmplStr'  => $varsPrintItem['tmplRow']['tmplTrBottom'],
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

			$strTextAccountTitle = $classEscape->toComma(array('data' => $varsValue['valueTextAccountTitle' . $i]));
			$strTextType = $classEscape->toComma(array('data' => $varsValue['valueTextType' . $i]));
			$strTextNum = $classEscape->toComma(array('data' => $varsValue['valueTextNum' . $i]));

			$strTextUnit = $classEscape->toComma(array('data' => $varsValue['valueTextUnit' . $i]));

			$dataValue = $varsValue['valueTextValue' . $i];
			$dataValue = ($dataValue === '' || $dataValue == 0)?  '' : $dataValue;
			$strTextValue = $dataValue;

			$strTextMemo = $classEscape->toComma(array('data' => $varsValue['valueTextMemo' . $i]));

			$arrayCsv[] = array(
				$strTextAccountTitle,
				$strTextType,
				$strTextNum,
				$strTextUnit,
				$strTextValue,
				$strTextMemo
			);
		}

		$dataValue = $varsValue['valueTextSum'];
		$dataValue = ($dataValue === '' || $dataValue == 0)?  '' : $dataValue;
		$strTextSum = $dataValue;

		$arrayCsv[] = array($arr['varsData']['strSum'], '', '', '', $strTextSum, '',);
		$arrayCsv[] = array();

		$varsStr = array();
		$dataSelectMethod = (int) $varsValue['valueSelectMethod'];
		$varsStr['strSelectA'] = $arr['varsData']['strA'] . ' ' . $arr['varsData']['strSelectA'];
		$varsStr['strSelectB'] = $arr['varsData']['strB'] . ' ' . $arr['varsData']['strSelectB'];
		$varsStr['strSelectC'] = $arr['varsData']['strC'] . ' ' . $arr['varsData']['strSelectC'];
		if ($dataSelectMethod == 1) {
			$strSelect = $arr['varsData']['strA'];
			$varsStr['strSelectA'] = '[' . $strSelect . '] ' . $arr['varsData']['strSelectA'];

		} elseif ($dataSelectMethod == 2) {
			$strSelect = $arr['varsData']['strB'];
			$varsStr['strSelectB'] = '[' . $strSelect . '] ' . $arr['varsData']['strSelectB'];

		} elseif ($dataSelectMethod == 3) {
			$strSelect = $arr['varsData']['strC'];
			$varsStr['strSelectC'] = '[' . $strSelect . '] ' . $arr['varsData']['strSelectC'];
		}

		$varsStr['strTextTime'] = $varsValue['valueTextYear'] . $arr['varsData']['strYear'];
		if (!$varsValue['valueTextMonth']) {
			$varsStr['strTextTime'] .=  $arr['varsData']['strBlank'];
		}
		$varsStr['strTextTime'] .= $varsValue['valueTextMonth'] . $arr['varsData']['strMonth'];
		if (!$varsValue['valueTextDate']) {
			$varsStr['strTextTime'] .=  $arr['varsData']['strBlank'];
		}
		$varsStr['strTextTime'] .= $varsValue['valueTextDate'] . $arr['varsData']['strDate'];

		$arrayCsv[] = array($arr['varsData']['strMethod'], $arr['varsData']['strTime']);
		$arrayCsv[] = array($varsStr['strSelectA'], $varsStr['strTextTime']);
		$arrayCsv[] = array($varsStr['strSelectB'], '');
		$arrayCsv[] = array($varsStr['strSelectC'], '');

		return $arrayCsv;
	}

	protected function _getVarsLoopCsvColumn($arr)
	{
		$rowData = array();
		$rowData[] = $arr['varsData']['strAccountTitle'];
		$rowData[] = $arr['varsData']['strType'];
		$rowData[] = $arr['varsData']['strNum'];
		$rowData[] = $arr['varsData']['strUnitTitle'];
		$rowData[] = $arr['varsData']['strValue'];
		$rowData[] = $arr['varsData']['strMemo'];

		return $rowData;
	}

}
