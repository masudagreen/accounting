<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_2012_DetailedAccount_15Output extends Code_Else_Plugin_Accounting_Jpn_DetailedAccount
{
	protected $_extChildSelf = array(
		'pathVarsPrint' => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/2012/detailedAccount/15Print.php',
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

	/*

	 * */
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
		'flagStaus'       => 'Print',
		'varsPrint'       => $arr['varsPrint'],
		'varsFlag'        => $arr['varsFlag'],
		'varsData'        => $varsData,
		'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
	 ))
	 */
	protected function _iniPrint($arr)
	{
		$varsItem = $this->_getVarsItem(array(
			'varsFlag'        => $arr['varsFlag'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$varsPrint = $this->_getVarsPrint(array(
			'varsPrint'       => $arr['varsPrint'],
			'varsFlag'        => $arr['varsFlag'],
			'varsData'        => $arr['varsData'],
			'varsItem'        => $varsItem,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		return $varsPrint;
	}

	/**
		(array(
			'vars'     => $vars,
		))
	 */
	protected function _getVarsItem($arr)
	{
		$varsStatus = array();

		$varsStatus['rents'] = array(
			'pathItem' => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/2012/detailedAccount/rentsItem.php',
		);
		$varsStatus['keyMoney'] = array(
			'pathItem' => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/2012/detailedAccount/keyMoneyItem.php',
		);
		$varsStatus['industrialProperty'] = array(
			'pathItem' => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/2012/detailedAccount/industrialPropertyItem.php',
		);

		$varsSave = array();
		$varsCommon = array();
		$varsPreference = array();
		$numPageMax = 0;
		$flagPageMax = '';
		$array = $varsStatus;
		foreach ($array as $key => $value) {
			if (!$flagPageMax) {
				$flagPageMax = $key;
			}
			$varsSave[$key] = $this->_getVarsSave(array(
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
				'flagReport'      => $this->_extSelf['flagReport'],
				'flagDetail'      => $key,
				'numPage'         => $arr['varsFlag']['numPage'],
			));

			$varsCommon[$key] = $this->getVars(array(
				'path' => $value['pathItem'],
			));

			$varsPreference[$key] = $this->_getVarsPreference(array(
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
				'flagReport'      => $this->_extSelf['flagReport'],
				'flagDetail'      => $key,
			));
			$num = (int) $varsPreference[$key]['jsonData']['numPageMax'];
			if ($num > $numPageMax) {
				$numPageMax = $num;
				$flagPageMax = $key;
			}
		}

		$varsPrintItem = $this->getVars(array(
			'path' => $this->_extChildSelf['pathVarsPrint'],
		));

		$data = array(
			'varsStatus'     => $varsStatus,
			'varsCommon'     => $varsCommon,
			'varsPreference' => $varsPreference,
			'varsSave'       => $varsSave,
			'varsPrintItem'  => $varsPrintItem,
			'numPageMax'     => $numPageMax,
			'flagPageMax'    => $flagPageMax,
		);

		return $data;
	}

	/**
		(array(
			'varsPrint'       => $arr['varsPrint'],
			'varsFlag'        => $arr['varsFlag'],
			'varsItem'        => $varsItem,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))

	 */
	protected function _getVarsPrint($arr)
	{
		$flagCount = 0;
		$varsPrint = $arr['varsPrint'];

		if ($arr['varsFlag']['flagType'] == 'item') {
			$varsPrint = $this->_getVarsPrintLoop(array(
				'flagCount'  => $flagCount,
				'varsItem'   => $arr['varsItem'],
				'varsFlag'   => $arr['varsFlag'],
				'varsData'   => $arr['varsData'],
				'varsPrint'  => $varsPrint,
			));

		} elseif ($arr['varsFlag']['flagType'] == 'itemAll') {
			$numEnd = (int) $arr['varsItem']['numPageMax'];
			if (!$numEnd) {
				$numEnd = 1;
			}

			for ($i = 1; $i <= $numEnd; $i++) {
				$numPage = $i;
				$arr['varsFlag']['numPage'] = $numPage;
				$array = $arr['varsItem']['varsStatus'];
				foreach ($array as $key => $value) {
					$arr['varsItem']['varsSave'][$key] = $this->_getVarsSave(array(
						'numFiscalPeriod' => $arr['numFiscalPeriod'],
						'flagReport'      => $this->_extSelf['flagReport'],
						'flagDetail'      => $key,
						'numPage'         => $arr['varsFlag']['numPage'],
					));
				}
				$varsPrint = $this->_getVarsPrintLoop(array(
					'flagCount'  => $flagCount,
					'varsItem'   => $arr['varsItem'],
					'varsFlag'   => $arr['varsFlag'],
					'varsData'   => $arr['varsData'],
					'varsPrint'  => $varsPrint,
				));
				$flagCount++;
			}
		}

		return $varsPrint;
	}

	/**
		(array(
			'flagCount'  => $flagCount,
			'flagLast'   => ($numPage == $numEnd)? 1 : 0,
			'vars'       => $arr['vars'],
			'varsItem'   => $arr['varsItem'],
			'varsFlag'   => $arr['varsFlag'],
			'varsPrint'  => $varsPrint,
		))
	 */
	protected function _getVarsPrintLoop($arr)
	{
		$varsData = array();

		$varsData = $arr['varsData'];
		$varsPrint = $arr['varsPrint'];
		if (!$arr['flagCount']) {
			$varsPrint['varsStatus'] = $this->_getVarsStatusPrint(array(
				'varsData'   => $varsData,
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

		$flagStage = 'rents';
		$flagMenu = $arr['varsFlag']['flagMenu'];
		$varsData = $arr['varsItem']['varsCommon'][$flagStage]['varsStr'][$flagMenu];

		//tmplWrap
		$varsPrint['varsStatus']['varsTmpl']['tmplWrap'] = $this->_getVarsHtml(array(
			'varsData' => $varsData,
			'tmplStr'  => $varsPrintItem['tmplWrap'],
		));

		$flagMenu = $arr['varsFlag']['flagMenu'];
		$array = $arr['varsItem']['varsStatus'];
		foreach ($array as $key => $value) {

			$flagStage = $key;
			$arrayData = $arr['varsItem']['varsCommon'][$flagStage]['varsStr'][$flagMenu];
			foreach ($arrayData as $keyData => $valueData) {
				$arr['varsData'][$keyData] = $valueData;
			}

			$str = ucwords($key);
			$varsPrint['varsStatus']['varsTmpl']['tmplColumn' . $str] = $this->_getVarsHtml(array(
				'varsData' => $arr['varsData'],
				'tmplStr'  => $varsPrintItem['tmplColumn' . $str],
			));
			$varsPrint['varsStatus']['varsTmpl']['tmplTable' . $str] = $this->_getVarsHtml(array(
				'varsData' => $arr['varsData'],
				'tmplStr'  => $varsPrintItem['tmplTable' . $str],
			));
		}

		$varsPrint['varsStatus']['varsTmpl']['tmplTableTop'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'tmplStr'  => $varsPrintItem['tmplTableTop'],
		));

		$varsPrint['varsStatus']['varsTmpl']['tmplTableBottom'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'tmplStr'  => $varsPrintItem['tmplTableBottom'],
		));

		$varsPrint['varsStatus']['varsTmpl']['tmplTableBottomCaution1'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'tmplStr'  => $varsPrintItem['tmplTableBottomCaution1'],
		));

		$varsPrint['varsStatus']['varsTmpl']['tmplTableBottomCaution2'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'tmplStr'  => $varsPrintItem['tmplTableBottomCaution2'],
		));

		$varsPrint['varsStatus']['varsTmpl']['tmplPage'] = $varsPrintItem['tmplPage'];

		$varsPrint['varsStatus']['strTitle'] = $this->_getStrTitle(array(
			'varsFlag' => $arr['varsFlag'],
			'varsItem' => $arr['varsItem'],
		));

		return $varsPrint['varsStatus'];
	}

	/**
		(array(
			'strFileType'   => '',
			'varsFlag'   => $arr['varsFlag'],
			'varsItem'   => $arr['varsItem'],
			'vars'       => $arr['vars'],
		))

	 */
	protected function _getStrTitle($arr)
	{
		$flagStage = 'rents';
		$flagMenu = $arr['varsFlag']['flagMenu'];
		$varsData = $arr['varsItem']['varsCommon'][$flagStage]['varsStr'][$flagMenu];

		$strMenu = $varsData['strTitleExt'];

		$strFileName = $this->_getFileTitle(array(
			'strMenu' => $strMenu,
		));

		return $strFileName;
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
		$numPage = $arr['varsFlag']['numPage'];
		$varsPrint = &$arr['varsPrint'];
		$varsPrintItem = $arr['varsItem']['varsPrintItem'];
		$flagMenu = $arr['varsFlag']['flagMenu'];

		//['rents']
		$flagStage = 'rents';
		$varsValue = $arr['varsItem']['varsSave'][$flagStage]['jsonData'][$flagMenu];

		$array = $arr['varsItem']['varsCommon'][$flagStage]['varsStr'][$flagMenu];
		foreach ($array as $key => $value) {
			$arr['varsData'][$key] = $value;
		}

		$array = $arr['varsItem']['varsCommon'][$flagStage]['varsTmpl'][$flagMenu];
		$numEnd = $arr['varsData']['numRows'];
		$varsStr = array();
		for ($i = 1; $i <= $numEnd; $i++) {
			$tmplRow = $varsPrint['varsDetailTmpl'];
			$tmplRow['id'] = $arr['flagCount'] . '_'. $flagStage . $i;
			if ($arr['flagFirst']) {
				$arr['flagFirst'] = 0;
				if ($arr['flagCount']) {
					$tmplRow['flagBreak'] = 1;
					$tmplRow['idTmplTableTop'] = 'tmplTableTop';
				}
			}
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

			$numTr = 3;
			for ($j = 1; $j <= $numTr; $j++) {
				$tmplRow['strRow'] .= $this->_getVarsHtml(array(
					'varsData' => $arr['varsData'],
					'value'    => $varsStr,
					'tmplStr'  => $varsPrintItem['tmplRow']['tmplTr' . $j . ucwords($flagStage)],
				));
			}
			$tmplRow['idTmplColumn'] = 'tmplColumn' . ucwords($flagStage);
			$tmplRow['idTmplTable'] = 'tmplTable' . ucwords($flagStage);
			$tmplRow['numTr'] = $numTr;
			$varsPrint['varsDetail'][] = $tmplRow;
		}

		//['keyMoney']
		$flagStage = 'keyMoney';
		$varsValue = $arr['varsItem']['varsSave'][$flagStage]['jsonData'][$flagMenu];

		$array = $arr['varsItem']['varsCommon'][$flagStage]['varsStr'][$flagMenu];
		foreach ($array as $key => $value) {
			$arr['varsData'][$key] = $value;
		}

		$array = $arr['varsItem']['varsCommon'][$flagStage]['varsTmpl'][$flagMenu];
		$numEnd = $arr['varsData']['numRows'];
		$varsStr = array();
		for ($i = 1; $i <= $numEnd; $i++) {
			$tmplRow = $varsPrint['varsDetailTmpl'];
			$tmplRow['id'] = $arr['flagCount'] . '_'. $flagStage . $i;
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

			$numTr = 2;
			for ($j = 1; $j <= $numTr; $j++) {
				$tmplRow['strRow'] .= $this->_getVarsHtml(array(
					'varsData' => $arr['varsData'],
					'value'    => $varsStr,
					'tmplStr'  => $varsPrintItem['tmplRow']['tmplTr' . $j . ucwords($flagStage)],
				));
			}
			$tmplRow['idTmplColumn'] = 'tmplColumn' . ucwords($flagStage);
			$tmplRow['idTmplTable'] = 'tmplTable' . ucwords($flagStage);
			$tmplRow['numTr'] = $numTr;
			$varsPrint['varsDetail'][] = $tmplRow;
		}

		$tmplRow = $varsPrint['varsDetailTmpl'];
		$tmplRow['id'] = $arr['flagCount'] . '_' . 'strCautionMark1';
		$tmplRow['numTr'] = 1;
		$tmplRow['flagColumnNone'] = 1;
		$tmplRow['idTmplTable'] = 'tmplTableBottomCaution1';
		$varsStr = array();
		$tmplRow['strRow'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'value'    => $varsStr,
			'tmplStr'  => $varsPrintItem['tmplRow']['tmplTrCaution1'],
		));
		$varsPrint['varsDetail'][] = $tmplRow;

		//['industrialProperty']
		$flagStage = 'industrialProperty';
		$varsValue = $arr['varsItem']['varsSave'][$flagStage]['jsonData'][$flagMenu];

		$array = $arr['varsItem']['varsCommon'][$flagStage]['varsStr'][$flagMenu];
		foreach ($array as $key => $value) {
			$arr['varsData'][$key] = $value;
		}

		$array = $arr['varsItem']['varsCommon'][$flagStage]['varsTmpl'][$flagMenu];
		$numEnd = $arr['varsData']['numRows'];
		$varsStr = array();
		for ($i = 1; $i <= $numEnd; $i++) {
			$tmplRow = $varsPrint['varsDetailTmpl'];
			$tmplRow['id'] = $arr['flagCount'] . '_'. $flagStage . $i;
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

			$numTr = 2;
			for ($j = 1; $j <= $numTr; $j++) {
				$tmplRow['strRow'] .= $this->_getVarsHtml(array(
					'varsData' => $arr['varsData'],
					'value'    => $varsStr,
					'tmplStr'  => $varsPrintItem['tmplRow']['tmplTr' . $j . ucwords($flagStage)],
				));
			}
			$tmplRow['idTmplColumn'] = 'tmplColumn' . ucwords($flagStage);
			$tmplRow['idTmplTable'] = 'tmplTable' . ucwords($flagStage);
			$tmplRow['numTr'] = $numTr;
			$varsPrint['varsDetail'][] = $tmplRow;
		}

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
		$tmplRow['id'] = $arr['flagCount'] . '_' . 'strCautionMark2';
		$tmplRow['numTr'] = 1;
		$tmplRow['flagColumnNone'] = 1;
		$tmplRow['idTmplTable'] = 'tmplTableBottomCaution2';
		$varsStr = array();
		$tmplRow['strRow'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'value'    => $varsStr,
			'tmplStr'  => $varsPrintItem['tmplRow']['tmplTrCaution2'],
		));
		$varsPrint['varsDetail'][] = $tmplRow;

		return $varsPrint;
	}
}
