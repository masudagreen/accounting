<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CashAnalyzeOutput extends Code_Else_Plugin_Accounting_Jpn_CashAnalyze
{

	protected $_childSelf = array(

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

	/**
	 *
	 */
	protected function _iniListOutput()
	{
		global $classRequest;

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'output',
			'idTarget'      => $this->_extSelf['idPreference'],
		));

		if (!$flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$text = $this->_getCsv(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$text = mb_convert_encoding($text, 'sjis', 'utf8');

		$strFileName = $this->_getStrTitle(array(
			'varsItem'    => $varsItem,
			'vars'        => $vars,
			'strFileType' => 'csv',
		));

		$classRequest->output(array(
			'text'         => $text,
			'strFileType'  => 'csv',
			'strFileName'  => $strFileName,
		));
	}

	/**
		(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
			'varsItem' => $varsItem,
		))

	 */
	protected function _getCsv($arr)
	{
		global $classFile;

		$arrayCsv = array();
		$data = $this->_getVarsCsvLoop(array(
			'vars'       => $arr['vars'],
			'varsItem'   => $arr['varsItem'],
			'arrayCsv'   => $arrayCsv,
		));
		$arrayCsv = $data['arrayCsv'];

		$text = $classFile->getCsvText(array(
			'delimiter' => ',',
			'rows'      => $arrayCsv,
		));

		return $text;
	}

	/**
		(array(
			'flagCount'  => $flagCount,
			'vars'       => $arr['vars'],
			'arrayCsv'   => $arrayCsv,
			'varsItem'   => $arr['varsItem'],
			'varsFlag'   => $arr['varsFlag'],
			'varsFS'     => $arr['vars']['portal']['varsList']['varsDetail'],
		))
	 */
	protected function _getVarsCsvLoop($arr)
	{
		$varsData = array();

		$varsData = $this->_getVarsStatus(array(
			'vars'     => $arr['vars'],
			'varsItem' => $arr['varsItem'],
		));

		$data = $this->_getVarsLoopCsvSpan(array(
			'varsData' => $varsData,
			'arrayCsv' => $arr['arrayCsv'],
			'vars'     => $arr['vars'],
			'varsItem' => $arr['varsItem'],
		));

		return $data;
	}

	/**
		(array(
			'vars'     => $arr['vars'],
			'varsFlag' => $arr['varsFlag'],
			'varsItem' => $arr['varsItem'],
		))
	 */
	protected function _getVarsStatus($arr)
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];

		$varsData = $arr['vars']['varsItem']['varsOutput'];

		//strEntity
		$strEntity = $varsPluginAccountingEntity[$idEntity]['strTitle'];
		$varsData['strEntityExt'] = str_replace('<%replace%>', $strEntity, $arr['vars']['varsItem']['varsOutput']['strEntityExt']);
		$varsData['strEntity'] = $strEntity;

		//strNum
		$strNumRep = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$varsData['strNumExt'] = str_replace('<%replace%>', $strNumRep, $arr['vars']['varsItem']['varsOutput']['strNumExt']);

		//strPeriod
		$varsPeriod = $this->_getVarsFiscalPeriod(array(
			'flagFiscalPeriod' => 'f1',
			'numFiscalPeriod'  => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));
		$str = $arr['vars']['varsItem']['varsOutput']['strPeriodExt'];
		$strPeriod = str_replace('<%strStartHeisei%>', $varsPeriod['numStartHeisei'], $str);
		$strPeriod = str_replace('<%strEndHeisei%>', $varsPeriod['numEndHeisei'], $strPeriod);
		$strPeriod = str_replace('<%strStartMonth%>', $varsPeriod['numStartMonth'], $strPeriod);
		$strPeriod = str_replace('<%strEndMonth%>', $varsPeriod['numEndMonth'], $strPeriod);
		$varsData['strPeriodExt'] = $strPeriod;

		return $varsData;
	}

	/**
		(array(

		))
	 */
	protected function _getVarsLoopCsvSpan($arr)
	{
		global $classEscape;

		$arrayCsv = &$arr['arrayCsv'];

		$arrayCsv[] = array($classEscape->toComma(array('data' => $arr['varsData']['strEntityExt'])));
		$arrayCsv[] = array($arr['varsData']['strNumExt']);
		$arrayCsv[] = array($arr['varsData']['strPeriodExt']);
		$arrayCsv[] = array();

		$varsBase = $arr['vars']['varsCollect']['varsBase'];
		$array  = $arr['varsItem']['varsStrFlagFiscalPeriod'];
		$dataRow = array('');
		foreach ($array as $key => $value) {
			$dataRow[] = $value;
		}
		$arrayCsv[] = $dataRow;

		$array  = $arr['vars']['varsCollect']['varsLabel'];
		foreach ($array as $key => $value) {
			$tempRow = array($value);
			$arrayPeriod = $arr['varsItem']['varsFlagFiscalPeriod'];
			foreach ($arrayPeriod as $keyPeriod => $valuePeriod) {
				$tempRow[] = $varsBase[$valuePeriod][$key];
			}
			$arrayCsv[] = $tempRow;
		}

		$data = array(
			'arrayCsv' => $arrayCsv
		);

		return $data;
	}

	/**
	 *
	 */
	protected function _iniDetailOutput()
	{
		global $classRequest;

		global $varsRequest;

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'output',
			'idTarget'      => $this->_extSelf['idPreference'],
		));

		if (!$flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}

		$varsFlag = array(
			'idAccountTitle'    => $varsRequest['query']['jsonValue']['vars']['IdAccountTitle'],
			'idSubAccountTitle' => $varsRequest['query']['jsonValue']['vars']['IdSubAccountTitle'],
			'flagType'          => $varsRequest['query']['jsonValue']['vars']['FlagType'],
		);

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$varsItem = $this->_updateVarsItem(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$this->_checkValueDetail(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
			'varsItem' => $varsItem,
		));

		$text = $this->_getCsvDetail(array(
			'varsFlag' => $varsFlag,
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$text = mb_convert_encoding($text, 'sjis', 'utf8');

		$strFileName = $this->_getStrTitle(array(
			'varsFlag'    => $varsFlag,
			'varsItem'    => $varsItem,
			'vars'        => $vars,
			'strFileType' => 'csv',
		));

		$classRequest->output(array(
			'text'         => $text,
			'strFileType'  => 'csv',
			'strFileName'  => $strFileName,
		));
	}

	/**
		(array(
			'strFileType'   => '',
			'varsFlag'   => $arr['varsFlag'],
			'varsItem'   => $arr['varsItem'],
			'vars'       => $arr['vars'],
		))

	 */
	protected function _checkValueDetail($arr)
	{
		$idAccountTitle = $arr['varsFlag']['idAccountTitle'];
		$idSubAccountTitle = $arr['varsFlag']['idSubAccountTitle'];

		if (!$arr['varsItem']['varsAccountTitle']['arrStrTitle'][$idAccountTitle]) {
			$this->_send404Output();
		}

		if ($idSubAccountTitle != 'none') {
			if (!$arr['varsItem']['arrSubAccountTitle']['arrStrTitle'][$idAccountTitle][$idSubAccountTitle]) {
				$this->_send404Output();
			}
		}

		$flag = 0;
		$array = $arr['vars']['portal']['varsDetail']['varsStatus']['switchOutputList'];
		foreach ($array as $key => $value) {
			if ($value == $arr['varsFlag']['flagType']) {
				$flag = 1;
			}
		}
		if (!$flag) {
			$this->_send404Output();
		}
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
		$strMenu = $arr['vars']['varsItem']['varsOutput']['strTitleFile'];

		if ($arr['varsFlag']['flagType'] == 'period') {
			$strMenu .= '_' . $arr['vars']['varsItem']['varsOutput']['strTitlePeriod'];

		} elseif ($arr['varsFlag']['flagType'] == 'sum') {
			$strMenu .= '_' . $arr['vars']['varsItem']['varsOutput']['strTitleSum'];
		}

		$strFlagFiscalPeriod = $arr['varsItem']['varsFlagFiscalPeriod']['arrStrTitle'][$arr['varsFlag']['flagFiscalPeriod']]['strTitle'];
		$strMenu .= '_' . $strFlagFiscalPeriod;



		$strFileName = $this->_getFileTitle(array(
			'strMenu'     => $strMenu,
			'strFileType' => $arr['strFileType'],
		));

		return $strFileName;
	}

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
		$vars = &$arr['vars'];
		$varsAccountTitle = array();

		$array = &$vars['portal']['varsDetail']['templateDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'IdAccountTitle') {
				$arrStrTitle = array();
				$arrayOption = $value['arrayOption'];
				foreach ($arrayOption as $keyOption => $valueOption) {
					$arrStrTitle[$valueOption['value']]['strTitle'] = $valueOption['strTitle'];
				}
				$varsAccountTitle['arrStrTitle'] = $arrStrTitle;
			}
		}
		$arr['varsItem']['varsAccountTitle'] = $varsAccountTitle;

		return $arr['varsItem'];
	}

	/**
		(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
			'varsItem' => $varsItem,
		))

	 */
	protected function _getCsvDetail($arr)
	{
		global $classFile;

		$arrayCsv = array();
		$data = $this->_getVarsCsvLoopDetail(array(
			'vars'       => $arr['vars'],
			'arrayCsv'   => $arrayCsv,
			'varsItem'   => $arr['varsItem'],
			'varsFlag'   => $arr['varsFlag'],
		));
		$arrayCsv = $data['arrayCsv'];

		$text = $classFile->getCsvText(array(
			'delimiter' => ',',
			'rows'      => $arrayCsv,
		));

		return $text;
	}

	/**
		(array(
			'flagCount'  => $flagCount,
			'vars'       => $arr['vars'],
			'arrayCsv'   => $arrayCsv,
			'varsItem'   => $arr['varsItem'],
			'varsFlag'   => $arr['varsFlag'],
			'varsFS'     => $arr['vars']['portal']['varsList']['varsDetail'],
		))
	 */
	protected function _getVarsCsvLoopDetail($arr)
	{
		$varsData = array();

		$varsData = $this->_getVarsStatusDetail(array(
			'vars'     => $arr['vars'],
			'varsFlag' => $arr['varsFlag'],
			'varsItem' => $arr['varsItem'],
		));

		if ($arr['varsFlag']['flagType'] == 'period') {
			$numLoop = 0;
			$array = $arr['vars']['varsCollect']['varsFlagFiscalPeriod'];
			foreach ($array as $key => $value) {
				$arr['varsFlag']['flagFiscalPeriod'] = $value;
				if ($arr['varsFlag']['idSubAccountTitle'] == 'none') {
					$data = $this->_getVarsLoopCsvSpanDetailPeriodAccountTitle(array(
						'numLoop'    => $numLoop,
						'varsData'   => $varsData,
						'arrayCsv'   => $arr['arrayCsv'],
						'vars'       => $arr['vars'],
						'varsItem'   => $arr['varsItem'],
						'varsFlag'   => $arr['varsFlag'],
					));
					$arr['arrayCsv'] = $data['arrayCsv'];

				} else {
					$data = $this->_getVarsLoopCsvSpanDetailPeriodSubAccountTitle(array(
						'numLoop'    => $numLoop,
						'varsData'   => $varsData,
						'arrayCsv'   => $arr['arrayCsv'],
						'vars'       => $arr['vars'],
						'varsItem'   => $arr['varsItem'],
						'varsFlag'   => $arr['varsFlag'],
					));
					$arr['arrayCsv'] = $data['arrayCsv'];
				}
				$numLoop++;
			}

		} elseif ($arr['varsFlag']['flagType'] == 'sum') {
			$numLoop = 0;
			$array = $arr['vars']['varsCollect']['varsLabelId'];
			foreach ($array as $key => $value) {
				$arr['varsFlag']['flagIn'] = $value;
				if ($arr['varsFlag']['idSubAccountTitle'] == 'none') {
					$data = $this->_getVarsLoopCsvSpanDetailSumAccountTitle(array(
						'numLoop'    => $numLoop,
						'varsData'   => $varsData,
						'arrayCsv'   => $arr['arrayCsv'],
						'vars'       => $arr['vars'],
						'varsItem'   => $arr['varsItem'],
						'varsFlag'   => $arr['varsFlag'],
					));
					$arr['arrayCsv'] = $data['arrayCsv'];

				} else {
					$data = $this->_getVarsLoopCsvSpanDetailSumSubAccountTitle(array(
						'numLoop'    => $numLoop,
						'varsData'   => $varsData,
						'arrayCsv'   => $arr['arrayCsv'],
						'vars'       => $arr['vars'],
						'varsItem'   => $arr['varsItem'],
						'varsFlag'   => $arr['varsFlag'],
					));
					$arr['arrayCsv'] = $data['arrayCsv'];
				}
				$numLoop++;
			}
		}

		return $data;
	}

	/**
		(array(
			'vars'     => $arr['vars'],
			'varsFlag' => $arr['varsFlag'],
			'varsItem' => $arr['varsItem'],
		))
	 */
	protected function _getVarsStatusDetail($arr)
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$varsData = $arr['vars']['varsItem']['varsOutput'];

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		//strEntity
		$strEntity = $varsPluginAccountingEntity[$idEntity]['strTitle'];
		$varsData['strEntityExt'] = str_replace('<%replace%>', $strEntity, $arr['vars']['varsItem']['varsOutput']['strEntityExt']);
		$varsData['strEntity'] = $strEntity;

		//strNum
		$strNumRep = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$varsData['strNumExt'] = str_replace('<%replace%>', $strNumRep, $arr['vars']['varsItem']['varsOutput']['strNumExt']);

		//strPeriod
		$varsPeriod = $this->_getVarsFiscalPeriod(array(
			'flagFiscalPeriod' => 'f1',
			'numFiscalPeriod'  => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));
		$str = $arr['vars']['varsItem']['varsOutput']['strPeriodExt'];
		$strPeriod = str_replace('<%strStartHeisei%>', $varsPeriod['numStartHeisei'], $str);
		$strPeriod = str_replace('<%strEndHeisei%>', $varsPeriod['numEndHeisei'], $strPeriod);
		$strPeriod = str_replace('<%strStartMonth%>', $varsPeriod['numStartMonth'], $strPeriod);
		$strPeriod = str_replace('<%strEndMonth%>', $varsPeriod['numEndMonth'], $strPeriod);
		$varsData['strPeriodExt'] = $strPeriod;

		$strIdAccountTitleRep = $arr['varsItem']['arrAccountTitle']['arrStrTitle'][$arr['varsFlag']['idAccountTitle']]['strTitleFS'];
		$strIdAccountTitle = str_replace('<%replace%>', $strIdAccountTitleRep, $arr['vars']['varsItem']['varsOutput']['strIdAccountTitleExt']);
		$varsData['strIdAccountTitleExt'] = $strIdAccountTitle;

		if ($arr['varsFlag']['idSubAccountTitle'] != 'none') {
			$strIdSubAccountTitleRep = $arr['varsItem']['arrSubAccountTitle']['arrStrTitle'][$arr['varsFlag']['idAccountTitle']][$arr['varsFlag']['idSubAccountTitle']]['strTitle'];
		} else {
			$strIdSubAccountTitleRep = $arr['vars']['varsItem']['varsOutput']['strNone'];
		}
		$strIdSubAccountTitle = str_replace('<%replace%>', $strIdSubAccountTitleRep, $arr['vars']['varsItem']['varsOutput']['strIdAccountTitleExt']);
		$varsData['strIdSubAccountTitleExt'] = $strIdSubAccountTitle;


		return $varsData;
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
	protected function _getVarsLoopCsvSpanDetailSumAccountTitle($arr)
	{
		global $classEscape;

		$arrayCsv = &$arr['arrayCsv'];

		if (!$arr['numLoop']) {
			$arrayCsv[] = array($classEscape->toComma(array('data' => $arr['varsData']['strEntityExt'])));
			$arrayCsv[] = array($arr['varsData']['strNumExt']);
			$arrayCsv[] = array($arr['varsData']['strPeriodExt']);
			$arrayCsv[] = array($arr['varsData']['strUnitExt']);
		}
		$arrayCsv[] = array();
		$arrayCsv[] = array($arr['vars']['varsCollect']['varsLabel'][$arr['varsFlag']['flagIn']]);

		$idAccountTitle = $arr['varsFlag']['idAccountTitle'];
		$flagIn = $arr['varsFlag']['flagIn'];

		$strTitleColumn = $arr['vars']['varsItem']['varsOutput']['strAccountTitle'];

		$array  = $arr['varsItem']['varsStrFlagFiscalPeriod'];
		$dataRow = array($strTitleColumn);
		foreach ($array as $key => $value) {
			$dataRow[] = $value;
		}
		$arrayCsv[] = $dataRow;

		$varsCash = $arr['varsItem']['varsPreference']['jsonCash'];
		$arrayPeriod = $arr['varsItem']['varsFlagFiscalPeriod'];

		$arrSelectTag = $arr['vars']['varsCollect']['arrAccountTitle']['arrSelectTag'];
		$arrStrTitle = $arr['vars']['varsCollect']['arrAccountTitle']['arrStrTitle'];

		$array = $arrSelectTag;
		foreach ($array as $key => $value) {
			$id = $value['value'];
			if ($varsCash[$value['value']]) {
				continue;
			}
			if (!$arrStrTitle[$id]) {
				/*
				$arrTemp = preg_split("/\./", $value['strTitle']);
				$arrTemp = preg_split("/ /", $value['strTitle']);
				$strTitle = end($arrTemp);
				$strTitle = $classEscape->toComma(array('data' => $strTitle));
				$tempData = array($strTitle);
				$arrayCsv[] = $tempData;
				*/
				continue;
			}
			$strDetail = 'all';
			$idAccountTitle = $id;
			$tempData = array();
			$tempData[] = $classEscape->toComma(array('data' => $arrStrTitle[$id]['strTitleFS']));
			foreach ($arrayPeriod as $keyPeriod => $valuePeriod) {
				$flagFiscalPeriod = $valuePeriod;
				$numValue = $arr['vars']['varsCollect']['varsCashValue'][$flagFiscalPeriod]['varsContra'][$idAccountTitle][$strDetail][$flagIn];
				if (!$arr['vars']['varsCollect']['varsCashValue'][$flagFiscalPeriod]['varsContra'][$idAccountTitle][$strDetail]) {
					$numValue = 0;
				}
				$tempData[] = $numValue;
			}
			$arrayCsv[] = $tempData;
		}

		$data = array(
			'arrayCsv' => $arrayCsv
		);

		return $data;
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
	protected function _getVarsLoopCsvSpanDetailSumSubAccountTitle($arr)
	{
		global $classEscape;

		$arrayCsv = &$arr['arrayCsv'];

		if (!$arr['numLoop']) {
			$arrayCsv[] = array($classEscape->toComma(array('data' => $arr['varsData']['strEntityExt'])));
			$arrayCsv[] = array($arr['varsData']['strNumExt']);
			$arrayCsv[] = array($arr['varsData']['strPeriodExt']);
			$arrayCsv[] = array($arr['varsData']['strIdAccountTitleExt']);
			$arrayCsv[] = array($arr['varsData']['strIdSubAccountTitleExt']);
			$arrayCsv[] = array($arr['varsData']['strUnitExt']);
		}
		$arrayCsv[] = array();
		$arrayCsv[] = array($arr['vars']['varsCollect']['varsLabel'][$arr['varsFlag']['flagIn']]);

		$idAccountTitle = $arr['varsFlag']['idAccountTitle'];
		$flagIn = $arr['varsFlag']['flagIn'];

		$strTitleColumn = $arr['vars']['varsItem']['varsOutput']['strSubAccountTitle'];

		$array  = $arr['varsItem']['varsStrFlagFiscalPeriod'];
		$dataRow = array($strTitleColumn);
		foreach ($array as $key => $value) {
			$dataRow[] = $value;
		}
		$arrayCsv[] = $dataRow;

		$arrayPeriod = $arr['varsItem']['varsFlagFiscalPeriod'];
		$arrSelectTag = $arr['vars']['varsCollect']['arrSubAccountTitle']['arrSelectTag'][$idAccountTitle];
		$arrStrTitle = $arr['vars']['varsCollect']['arrSubAccountTitle']['arrStrTitle'][$idAccountTitle];

		if (!$arrStrTitle) {
			$tempData = array();
			$tempData[] = $arr['vars']['varsCollect']['strNone'];
			foreach ($arrayPeriod as $keyPeriod => $valuePeriod) {
				$flagFiscalPeriod = $valuePeriod;
				$strDetail = 'all';
				$numValue = $arr['vars']['varsCollect']['varsCashValue'][$flagFiscalPeriod]['varsContra'][$idAccountTitle][$strDetail][$flagIn];
				if (!$arr['vars']['varsCollect']['varsCashValue'][$flagFiscalPeriod]['varsContra'][$idAccountTitle][$strDetail]) {
					$numValue = 0;
				}
				$tempData[] = $numValue;
			}
			$arrayCsv[] = $tempData;

			$tempData = $arr['vars']['varsItem']['varsOutput']['arrSubAccountTitleSummary'];
			foreach ($arrayPeriod as $keyPeriod => $valuePeriod) {
				$flagFiscalPeriod = $valuePeriod;
				$strDetail = 'all';
				$numValue = $arr['vars']['varsCollect']['varsCashValue'][$flagFiscalPeriod]['varsContra'][$idAccountTitle][$strDetail][$flagIn];
				if (!$arr['vars']['varsCollect']['varsCashValue'][$flagFiscalPeriod]['varsContra'][$idAccountTitle][$strDetail]) {
					$numValue = 0;
				}
				$tempData[] = $numValue;
			}
			$arrayCsv[] = $tempData;

			$data = array(
				'arrayCsv' => $arrayCsv
			);
			return $data;
		}

		$varsDataAll = array();
		$tempDataAll = $arr['vars']['varsItem']['varsOutput']['arrSubAccountTitleSummary'];
		foreach ($arrayPeriod as $keyPeriod => $valuePeriod) {
			$flagFiscalPeriod = $valuePeriod;
			$strDetail = 'all';
			$numValue = $arr['vars']['varsCollect']['varsCashValue'][$flagFiscalPeriod]['varsContra'][$idAccountTitle][$strDetail][$flagIn];
			if (!$arr['vars']['varsCollect']['varsCashValue'][$flagFiscalPeriod]['varsContra'][$idAccountTitle][$strDetail]) {
				$numValue = 0;
			}
			$tempDataAll[] = $numValue;
			$varsDataAll[$flagFiscalPeriod] = $numValue;
		}

		$varsDataSum = array();
		$arrSort = array();
		$array = $arrSelectTag;
		foreach ($array as $key => $value) {
			$id = $value['value'];
			$strDetail = $id;
			$tempData = array();
			$tempData[] = $classEscape->toComma(array('data' => $arrStrTitle[$id]['strTitle']));
			foreach ($arrayPeriod as $keyPeriod => $valuePeriod) {
				$flagFiscalPeriod = $valuePeriod;
				$numValue = $arr['vars']['varsCollect']['varsCashValue'][$flagFiscalPeriod]['varsContra'][$idAccountTitle][$strDetail][$flagIn];
				if (!$arr['vars']['varsCollect']['varsCashValue'][$flagFiscalPeriod]['varsContra'][$idAccountTitle][$strDetail]) {
					$numValue = 0;
				}
				$tempData[] = $numValue;
				$varsDataSum[$flagFiscalPeriod] += $numValue;
			}
			$arrSort[] = $tempData;
		}

		$tempData = array();
		$tempData[] = $arr['vars']['varsCollect']['strNone'];
		foreach ($arrayPeriod as $keyPeriod => $valuePeriod) {
			$flagFiscalPeriod = $valuePeriod;
			$numValue = $varsDataAll[$flagFiscalPeriod] - $varsDataSum[$flagFiscalPeriod];
			$tempData[] = $numValue;
		}
		$arrayCsv[] = $tempData;

		$array  = $arrSort;
		foreach ($array as $key => $value) {
			$arrayCsv[] = $value;
		}

		$arrayCsv[] = $tempDataAll;

		$data = array(
			'arrayCsv' => $arrayCsv
		);

		return $data;
	}

	/**
		(array(
			'numLoop'    => $numLoop,
			'varsData'   => $varsData,
			'arrayCsv'   => $arr['arrayCsv'],
			'vars'       => $arr['vars'],
			'varsItem'   => $arr['varsItem'],
			'varsFlag'   => $arr['varsFlag'],
		))
	 */
	protected function _getVarsLoopCsvSpanDetailPeriodAccountTitle($arr)
	{
		global $classEscape;

		$arrayCsv = &$arr['arrayCsv'];

		if (!$arr['numLoop']) {
			$arrayCsv[] = array($classEscape->toComma(array('data' => $arr['varsData']['strEntityExt'])));
			$arrayCsv[] = array($arr['varsData']['strNumExt']);
			$arrayCsv[] = array($arr['varsData']['strPeriodExt']);
			$arrayCsv[] = array($arr['varsData']['strUnitExt']);
		}
		$arrayCsv[] = array();
		$arrayCsv[] = array($arr['vars']['varsCollect']['varsStrFlagFiscalPeriod'][$arr['varsFlag']['flagFiscalPeriod']]);

		$idAccountTitle = $arr['varsFlag']['idAccountTitle'];
		$flagFiscalPeriod = $arr['varsFlag']['flagFiscalPeriod'];

		$arrayCsv[] = $arr['vars']['varsItem']['varsOutput']['arrAccountTitle'];

		$arrSelectTag = $arr['vars']['varsCollect']['arrAccountTitle']['arrSelectTag'];
		$arrStrTitle = $arr['vars']['varsCollect']['arrAccountTitle']['arrStrTitle'];

		$arrSort = array();
		$array = $arrSelectTag;
		foreach ($array as $key => $value) {
			$idAccountTitle = $value['value'];
			$strDetail = 'all';
			if (!$arrStrTitle[$idAccountTitle]) {
				/*
				$arrTemp = preg_split("/\./", $value['strTitle']);
				$arrTemp = preg_split("/ /", $value['strTitle']);
				$strTitle = end($arrTemp);
				$strTitle = $classEscape->toComma(array('data' => $strTitle));
				$tempData = array();
				$tempData['strTitle'] = $strTitle;
				$tempData['sumIn'] = '';
				$tempData['sumOut'] = '';
				$tempData['sumNet'] = '';
				$tempData['flagLeft'] = 1;
				$arrSort[] = $tempData;
				*/
				continue;
			}
			if (!$arr['vars']['varsCollect']['varsCashValue'][$flagFiscalPeriod]['varsContra'][$idAccountTitle][$strDetail]) {
				$arr['vars']['varsCollect']['varsCashValue'][$flagFiscalPeriod]['varsContra'][$idAccountTitle][$strDetail]['sumIn'] = 0;
				$arr['vars']['varsCollect']['varsCashValue'][$flagFiscalPeriod]['varsContra'][$idAccountTitle][$strDetail]['sumOut'] = 0;
				$arr['vars']['varsCollect']['varsCashValue'][$flagFiscalPeriod]['varsContra'][$idAccountTitle][$strDetail]['sumNet'] = 0;
			}
			$tempData = array();
			$tempData['strTitle'] = $arrStrTitle[$idAccountTitle]['strTitleFS'];
			$tempData['sumIn'] = $arr['vars']['varsCollect']['varsCashValue'][$flagFiscalPeriod]['varsContra'][$idAccountTitle][$strDetail]['sumIn'];
			$tempData['sumOut'] = $arr['vars']['varsCollect']['varsCashValue'][$flagFiscalPeriod]['varsContra'][$idAccountTitle][$strDetail]['sumOut'];
			$tempData['sumNet'] = $arr['vars']['varsCollect']['varsCashValue'][$flagFiscalPeriod]['varsContra'][$idAccountTitle][$strDetail]['sumNet'];
			$arrSort[] = $tempData;
		}

		$array = $arrSort;
		foreach ($array as $key => $value) {
			$dataTemp = array();
			$dataTemp[] = $classEscape->toComma(array('data' => $value['strTitle']));
			$dataTemp[] = $value['sumIn'];
			$dataTemp[] = $value['sumOut'];
			$dataTemp[] = $value['sumNet'];
			$arrayCsv[] = $dataTemp;
		}

		$data = array(
			'arrayCsv' => $arrayCsv
		);

		return $data;
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
	protected function _getVarsLoopCsvSpanDetailPeriodSubAccountTitle($arr)
	{
		global $classEscape;

		$arrayCsv = &$arr['arrayCsv'];

		if (!$arr['numLoop']) {
			$arrayCsv[] = array($classEscape->toComma(array('data' => $arr['varsData']['strEntityExt'])));
			$arrayCsv[] = array($arr['varsData']['strNumExt']);
			$arrayCsv[] = array($arr['varsData']['strPeriodExt']);
			$arrayCsv[] = array($arr['varsData']['strIdAccountTitleExt']);
			$arrayCsv[] = array($arr['varsData']['strIdSubAccountTitleExt']);
			$arrayCsv[] = array($arr['varsData']['strUnitExt']);
		}
		$arrayCsv[] = array();
		$arrayCsv[] = array($arr['vars']['varsCollect']['varsStrFlagFiscalPeriod'][$arr['varsFlag']['flagFiscalPeriod']]);

		$idAccountTitle = $arr['varsFlag']['idAccountTitle'];
		$idSubAccountTitle = $arr['varsFlag']['idSubAccountTitle'];
		$flagFiscalPeriod = $arr['varsFlag']['flagFiscalPeriod'];

		$arrayCsv[] = $arr['vars']['varsItem']['varsOutput']['arrSubAccountTitle'];

		$dataNone = array();
		$dataNone['strTitle'] = $arr['vars']['varsCollect']['strNone'];
		$dataNone['sumIn'] = 0;
		$dataNone['sumOut'] = 0;
		$dataNone['sumNet'] = 0;

		$varsValueAll = array();
		$varsValueAll['sumIn'] = 0;
		$varsValueAll['sumOut'] = 0;
		$varsValueAll['sumNet'] = 0;

		$varsValueSum = array();
		$varsValueSum['sumIn'] = 0;
		$varsValueSum['sumOut'] = 0;
		$varsValueSum['sumNet'] = 0;

		$arrSelectTag = $arr['vars']['varsCollect']['arrSubAccountTitle']['arrSelectTag'][$idAccountTitle];
		$arrStrTitle = $arr['vars']['varsCollect']['arrSubAccountTitle']['arrStrTitle'][$idAccountTitle];

		$arrSort = array();
		$array = $arrSelectTag;
		foreach ($array as $key => $value) {
			$id = $value['value'];
			$strDetail = $id;
			if (!$arr['vars']['varsCollect']['varsCashValue'][$flagFiscalPeriod]['varsContra'][$idAccountTitle][$strDetail]) {
				$arr['vars']['varsCollect']['varsCashValue'][$flagFiscalPeriod]['varsContra'][$idAccountTitle][$strDetail]['sumIn'] = 0;
				$arr['vars']['varsCollect']['varsCashValue'][$flagFiscalPeriod]['varsContra'][$idAccountTitle][$strDetail]['sumOut'] = 0;
				$arr['vars']['varsCollect']['varsCashValue'][$flagFiscalPeriod]['varsContra'][$idAccountTitle][$strDetail]['sumNet'] = 0;
			}
			$tempData = array();
			$tempData['strTitle'] = $arrStrTitle[$id]['strTitle'];
			$tempData['sumIn'] = $arr['vars']['varsCollect']['varsCashValue'][$flagFiscalPeriod]['varsContra'][$idAccountTitle][$strDetail]['sumIn'];
			$tempData['sumOut'] = $arr['vars']['varsCollect']['varsCashValue'][$flagFiscalPeriod]['varsContra'][$idAccountTitle][$strDetail]['sumOut'];
			$tempData['sumNet'] = $arr['vars']['varsCollect']['varsCashValue'][$flagFiscalPeriod]['varsContra'][$idAccountTitle][$strDetail]['sumNet'];
			$arrSort[] = $tempData;

			$varsValueSum['sumIn'] += $tempData['sumIn'];
			$varsValueSum['sumOut'] += $tempData['sumOut'];
			$varsValueSum['sumNet'] += $tempData['sumNet'];
		}

		$varsValueAll['sumIn'] = $arr['vars']['varsCollect']['varsCashValue'][$flagFiscalPeriod]['varsContra'][$idAccountTitle]['all']['sumIn'];
		$varsValueAll['sumOut'] = $arr['vars']['varsCollect']['varsCashValue'][$flagFiscalPeriod]['varsContra'][$idAccountTitle]['all']['sumOut'];
		$varsValueAll['sumNet'] = $arr['vars']['varsCollect']['varsCashValue'][$flagFiscalPeriod]['varsContra'][$idAccountTitle]['all']['sumNet'];

		$varsValueAll['sumIn'] = (is_null($varsValueAll['sumIn']))? 0 : $varsValueAll['sumIn'];
		$varsValueAll['sumOut'] = (is_null($varsValueAll['sumOut']))? 0 : $varsValueAll['sumOut'];
		$varsValueAll['sumNet'] = (is_null($varsValueAll['sumNet']))? 0 : $varsValueAll['sumNet'];

		$dataNone['sumIn'] = $varsValueAll['sumIn'] - $varsValueSum['sumIn'];
		$dataNone['sumOut'] = $varsValueAll['sumOut'] - $varsValueSum['sumOut'];
		$dataNone['sumNet'] = $varsValueAll['sumNet'] - $varsValueSum['sumNet'];

		$dataTemp = array();
		$dataTemp[] = $dataNone['strTitle'];
		$dataTemp[] = $dataNone['sumIn'];
		$dataTemp[] = $dataNone['sumOut'];
		$dataTemp[] = $dataNone['sumNet'];
		$arrayCsv[] = $dataTemp;

		$array = $arrSort;
		foreach ($array as $key => $value) {
			$dataTemp = array();
			$dataTemp[] = $classEscape->toComma(array('data' => $value['strTitle']));
			$dataTemp[] = $value['sumIn'];
			$dataTemp[] = $value['sumOut'];
			$dataTemp[] = $value['sumNet'];
			$arrayCsv[] = $dataTemp;
		}


		$dataTemp = $arr['vars']['varsItem']['varsOutput']['arrSubAccountTitleSummary'];
		$dataTemp[] = $varsValueAll['sumIn'];
		$dataTemp[] = $varsValueAll['sumOut'];
		$dataTemp[] = $varsValueAll['sumNet'];
		$arrayCsv[] = $dataTemp;

		$data = array(
			'arrayCsv' => $arrayCsv
		);

		return $data;
	}
}
