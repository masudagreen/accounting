<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_FinancialStatementMultiOutput extends Code_Else_Plugin_Accounting_Jpn_FinancialStatementMulti
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

		global $varsRequest;
		global $varsPluginAccountingPreference;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

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
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsFlag = array(
			'idDepartment' => $varsRequest['query']['jsonValue']['vars']['IdDepartment'],
			'flagFS'       => $varsRequest['query']['jsonValue']['vars']['FlagFS'],
			'flagZero'     => $varsRequest['query']['jsonValue']['vars']['FlagZero'],
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
			'flagOutput' => 1,
		));

		$varsItem = $this->_updateVarsItem(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		));

		$text = $this->_getCsv(array(
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
	protected function _getStrTitle($arr)
	{
		$strMenu = $arr['vars']['varsItem']['varsOutput']['strTitleFile'];

		$strFlagFiscalPeriod = $arr['varsItem']['varsFlagFiscalPeriod']['arrStrTitle'][$arr['varsFlag']['flagFiscalPeriod']]['strTitle'];

		$strFlagFS = $arr['varsItem']['varsFlagFS']['arrStrTitle'][$arr['varsFlag']['flagFS']]['strTitle'];

		$strMenu .= '_' . $strFlagFS;

		if ($arr['varsFlag']['flagType'] == 'span') {
			$strMenu .= '_' . $strFlagFiscalPeriod;
		}

		$strDepartment = $arr['varsItem']['varsDepartment']['arrStrTitle'][$arr['varsFlag']['idDepartment']]['strTitle'];
		if ($arr['varsFlag']['idDepartment'] != 'none') {
			$strMenu .= '_' . $strDepartment;
		}

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
		$varsFlagFS = array();
		//$varsFlagUnit = array();

		$array = &$vars['portal']['varsNavi']['templateDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'FlagFS') {
				$arrStrTitle = array();
				$arrayOption = $value['arrayOption'];
				foreach ($arrayOption as $keyOption => $valueOption) {
					$arrStrTitle[$valueOption['value']]['strTitle'] = $valueOption['strTitle'];
				}
				$varsFlagFS['arrStrTitle'] = $arrStrTitle;
			} /*elseif ($value['id'] == 'FlagUnit') {
				$arrStrTitle = array();
				$arrayOption = $value['arrayOption'];
				foreach ($arrayOption as $keyOption => $valueOption) {
					$arrStrTitle[$valueOption['value']]['strTitle'] = $valueOption['strTitle'];
				}
				$varsFlagUnit['arrStrTitle'] = $arrStrTitle;

			}*/
		}

		$varsPeriodData = array();
		$arrayF1 = array();
		$arrayF2 = array();
		$arrayF4 = array();
		$arrayMonth = array();
		$arrayNumPeriod = $arr['varsItem']['varsPeriod'];
		foreach ($arrayNumPeriod as $keyNumPeriod => $valueNumPeriod) {
			$arrayPeriod = $arr['varsItem']['varsFlagFiscalPeriod'][$valueNumPeriod];
			foreach ($arrayPeriod as $keyPeriod => $valuePeriod) {
				$tempData = array();
				$tempData['flagFiscalPeriod'] = $valuePeriod;
				$tempData['numFiscalPeriod'] = $valueNumPeriod;
				if (preg_match( "/^(f1)$/", $valuePeriod)) {
					$arrayF1[] = $tempData;

				} elseif (preg_match( "/^f2/", $valuePeriod)) {
					$arrayF2[] = $tempData;

				} elseif (preg_match( "/^f4/", $valuePeriod)) {
					$arrayF4[] = $tempData;

				} else {
					$arrayMonth[] = $tempData;
				}
			}
		}
		$array = $arrayF1;
		foreach ($array as $key => $value) {
			$varsPeriodData[] = $value;
		}
		$array = $arrayF2;
		foreach ($array as $key => $value) {
			$varsPeriodData[] = $value;
		}
		$array = $arrayF4;
		foreach ($array as $key => $value) {
			$varsPeriodData[] = $value;
		}
		$array = $arrayMonth;
		foreach ($array as $key => $value) {
			$varsPeriodData[] = $value;
		}
		$arr['varsItem']['varsPeriodData'] = $varsPeriodData;

		$arr['varsItem']['varsFlagFS'] = $varsFlagFS;
		//$arr['varsItem']['varsFlagUnit'] = $varsFlagUnit;

		return $arr['varsItem'];
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
	protected function _getVarsCsvLoop($arr)
	{
		$varsData = array();

		$varsData = $this->_getVarsStatus(array(
			'vars'     => $arr['vars'],
			'varsFlag' => $arr['varsFlag'],
			'varsItem' => $arr['varsItem'],
		));

		$data = $this->_getVarsLoopCsvSpan(array(
			'flagCount'  => $arr['flagCount'],
			'flagFirst'  => 1,
			'varsData'   => $varsData,
			'arrayCsv'   => $arr['arrayCsv'],
			'vars'       => $arr['vars'],
			'varsFS'     => $arr['varsFS'],
			'varsItem'   => $arr['varsItem'],
			'varsFlag'   => $arr['varsFlag'],
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
		$strNumRep = reset($arr['varsItem']['varsPeriod']);
		$varsData['strNumExt'] = str_replace('<%replaceStart%>', $strNumRep, $arr['vars']['varsItem']['varsOutput']['strNumExt']);
		$strNumRep = end($arr['varsItem']['varsPeriod']);
		$varsData['strNumExt'] = str_replace('<%replaceEnd%>', $strNumRep, $varsData['strNumExt']);

		//strFS
		$varsData['strFS'] = $arr['varsItem']['varsFlagFS']['arrStrTitle'][$arr['varsFlag']['flagFS']]['strTitle'];

		//
		/*
		$strUnitRep = $arr['varsItem']['varsFlagUnit']['arrStrTitle'][$arr['varsFlag']['flagUnit']]['strTitle'];
		$strUnit = str_replace('<%replace%>', $strUnitRep, $arr['vars']['varsItem']['varsOutput']['strUnitExt']);
		$varsData['strUnitExt'] = $strUnit;
		*/

		//strDepartment
		$strDepartment = '';
		$strDepartmentRep = '';
		if ($arr['varsFlag']['idDepartment'] != 0) {
			$strDepartmentRep = $arr['varsItem']['varsDepartment']['arrStrTitle'][$arr['varsFlag']['idDepartment']]['strTitle'];
			$strDepartment = str_replace('<%replace%>', $strDepartmentRep, $varsData['strDepartmentExt']);
		}
		$varsData['strDepartmentExt'] = $strDepartment;
		$varsData['strDepartment'] = $strDepartmentRep;

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
	protected function _getVarsLoopCsvSpan($arr)
	{
		global $classEscape;

		$arrayCsv = &$arr['arrayCsv'];

		$arrayCsv[] = array($classEscape->toComma(array('data' => $arr['varsData']['strEntityExt'])));
		$arrayCsv[] = array($arr['varsData']['strNumExt']);
		if ($arr['varsFlag']['idDepartment'] != 'none') {
			$arrayCsv[] = array($classEscape->toComma(array('data' => $arr['varsData']['strDepartmentExt'])));
		}
		$arrayCsv[] = array($arr['varsData']['strUnitExt']);
		$arrayCsv[] = array($classEscape->toComma(array('data' => $arr['varsData']['strFS'])));
		$arrayCsv[] = array();

		$strPeriod = $arr['vars']['varsItem']['tmplFiscalPeriod']['strPeriod'];
		$varsColumn = array('','',);
		$varsColumnId = array('item','item');
		$arrayPeriodData = $arr['varsItem']['varsPeriodData'];
		foreach ($arrayPeriodData as $keyPeriodData => $valuePeriodData) {
			$numFiscalPeriod = $valuePeriodData['numFiscalPeriod'];
			$flagFiscalPeriod = $valuePeriodData['flagFiscalPeriod'];
			$str = $numFiscalPeriod . $strPeriod;
			$str .= $arr['varsItem']['varsStrFlagFiscalPeriod'][$numFiscalPeriod][$flagFiscalPeriod];
			$varsColumn[] = $str;
			$varsColumnId[] = $numFiscalPeriod . '_' . $flagFiscalPeriod;
		}
		$arrayCsv[] = $varsColumn;
		$varsBase = &$arr['vars']['varsCollect']['varsBase'];
		$arrayNew = array();
		$array = $arr['vars']['varsCollect']['arrStrTitle'];

		foreach ($array as $key => $value) {
			$strTitle = $classEscape->toComma(array('data' => $value['strTitle']));
			if (!is_null($value['vars']['varsValue'])) {

				$arrayColumn = $varsColumnId;
				$tempData = array('', $strTitle);
				foreach ($arrayColumn as $keyColumn => $valueColumn) {
					if ($valueColumn == 'item') {
						continue;
					}
					$arrLevel = preg_split("/_/", $valueColumn);
					$numFiscalPeriod = reset($arrLevel);
					$flagFiscalPeriod = end($arrLevel);
					$arrLevel = array();
					$tempData[] = $varsBase[$numFiscalPeriod]['varsNum'][$flagFiscalPeriod][$key]['sumNext'];
				}

				$flag = 0;
				if (!is_null($value['vars']['flagUse'])) {
					//flagUse
					if ((int) $value['vars']['flagUse']) {
						$flag = 1;
					}
				} else {
					$flag = 1;
				}

				if (!$arr['varsFlag']['flagZero']) {
					if (is_null($value['vars']['flagCalc'])) {
						$flagCheck = $arr['vars']['varsCollect']['varsZero']['arrStrTitle'][$key];
						if (!$flagCheck) {
							continue;
						}
					}
				}
				if ($flag) {
					$arrayCsv[] = $tempData;
				}

			} else {
				$arrayCsv[] = array($strTitle);
			}
		}

		$data = array(
			'arrayCsv' => $arrayCsv
		);

		return $data;
	}
}
