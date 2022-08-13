<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_BudgetOutput extends Code_Else_Plugin_Accounting_Jpn_Budget
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
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];

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

		$varsFlag = array(
			'flagFiscalPeriod' => $varsRequest['query']['jsonValue']['vars']['FlagFiscalPeriod'],
			'idDepartment'     => $varsRequest['query']['jsonValue']['vars']['IdDepartment'],
			'flagFS'           => $varsRequest['query']['jsonValue']['vars']['FlagFS'],
			'flagUnit'         => (int) $varsRequest['query']['jsonValue']['vars']['FlagUnit'],
			'flagCalc'         => $varsRequest['query']['jsonValue']['vars']['FlagCalc'],
			'flagType'         => $varsRequest['query']['jsonValue']['vars']['FlagType'],
		);

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
		));

		$this->_checkValueFS(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
			'varsItem' => $varsItem,
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$vars['portal']['varsDetail']['varsDetail'] = $this->_updateVarsDetail((array(
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

		if ($arr['varsFlag']['flagType'] == 'span') {
			$strMenu .= '_' . $strFlagFiscalPeriod;
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

		$arr['varsItem']['varsFlagFS'] = $varsFlagFS;
		$arr['varsItem']['varsFlagFiscalPeriod'] = $varsFlagFiscalPeriod;
		$arr['varsItem']['varsFlagUnit'] = $varsFlagUnit;

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
		if ($arr['varsFlag']['flagType'] == 'span') {
			$arr['vars'] = $this->_updateVars(array(
				'vars'       => $arr['vars'],
				'varsItem'   => $arr['varsItem'],
				'varsFlag'   => $arr['varsFlag'],
			));
			$data = $this->_getVarsCsvLoop(array(
				'vars'       => $arr['vars'],
				'arrayCsv'   => $arrayCsv,
				'varsItem'   => $arr['varsItem'],
				'varsFlag'   => $arr['varsFlag'],
				'varsFS'     => $arr['vars']['portal']['varsList']['varsDetail'],
			));
			$arrayCsv = $data['arrayCsv'];

		} elseif ($arr['varsFlag']['flagType'] == 'spanAll') {
			$flagCount = 0;
			$array = $arr['varsItem']['varsFlagFiscalPeriod']['arrStrTitle'];
			foreach ($array as $key => $value) {
				$flagFiscalPeriod = $key;
				$arr['varsFlag']['flagFiscalPeriod'] = $flagFiscalPeriod;
				$arr['vars']['portal']['varsList']['varsDetail'] = array();
				$arr['vars'] = $this->_updateVars(array(
					'vars'       => $arr['vars'],
					'varsItem'   => $arr['varsItem'],
					'varsFlag'   => $arr['varsFlag'],
				));
				$data = $this->_getVarsCsvLoop(array(
					'flagCount'  => $flagCount,
					'vars'       => $arr['vars'],
					'arrayCsv'   => $arrayCsv,
					'varsItem'   => $arr['varsItem'],
					'varsFlag'   => $arr['varsFlag'],
					'varsFS'     => $arr['vars']['portal']['varsList']['varsDetail'],
				));
				$arrayCsv = $data['arrayCsv'];
				$flagCount++;
			}
		}

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

		$varsData['strPeriod'] = $arr['varsItem']['varsFlagFiscalPeriod']['arrStrTitle'][$arr['varsFlag']['flagFiscalPeriod']]['strTitle'];

		//strNum
		$strNumRep = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$varsData['strNumExt'] = str_replace('<%replace%>', $strNumRep, $arr['vars']['varsItem']['varsOutput']['strNumExt']);

		//strFS
		$varsData['strFS'] = $arr['varsItem']['varsFlagFS']['arrStrTitle'][$arr['varsFlag']['flagFS']]['strTitle'];

		//strPeriod
		$varsPeriod = $this->_getVarsFiscalPeriod(array(
			'flagFiscalPeriod' => 'f1',
			'numFiscalPeriod'  => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));
		/*20190401 start*/
		/*
		 $str = $arr['vars']['varsItem']['varsOutput']['strPeriodExt'];
		 $strPeriod = str_replace('<%strStartHeisei%>', $varsPeriod['numStartHeisei'], $str);
		 $strPeriod = str_replace('<%strEndHeisei%>', $varsPeriod['numEndHeisei'], $strPeriod);
		 */
		$str = $arr['vars']['varsItem']['varsOutput']['strPeriodExt20190401'];
		$strPeriod = str_replace('<%strStartNengoYear%>', $varsPeriod['strStartNengoYear'], $str);
		$strPeriod = str_replace('<%strEndNengoYear%>', $varsPeriod['strEndNengoYear'], $strPeriod);
		/*20190401 end*/
		$strPeriod = str_replace('<%strStartMonth%>', $varsPeriod['numStartMonth'], $strPeriod);
		$strPeriod = str_replace('<%strEndMonth%>', $varsPeriod['numEndMonth'], $strPeriod);
		$varsData['strPeriodExt'] = $strPeriod;

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

		if ($arr['flagFirst']) {
			$arr['flagFirst'] = 0;
			if (!$arr['flagCount']) {
				$arrayCsv[] = array($classEscape->toComma(array('data' => $arr['varsData']['strEntityExt'])));
				$arrayCsv[] = array($arr['varsData']['strNumExt']);
				$arrayCsv[] = array($arr['varsData']['strPeriodExt']);
				if ($arr['varsFlag']['idDepartment'] != 0) {
					$arrayCsv[] = array($classEscape->toComma(array('data' => $arr['varsData']['strDepartmentExt'])));
				}
				$arrayCsv[] = array($arr['varsData']['strUnitExt']);
				$arrayCsv[] = array($classEscape->toComma(array('data' => $arr['varsData']['strFS'])));
				$arrayCsv[] = array();

			} else {
				$arrayCsv[] = array();
				$arrayCsv[] = array();
			}
			$arrayCsv[] = array($arr['varsData']['strPeriod']);
			$arrayCsv[] = $arr['vars']['varsItem']['arrColumn'];
		}

		$array = &$arr['varsFS'];
		foreach ($array as $key => $value) {

			$strTitle = $classEscape->toComma(array('data' => $value['strTitle']));
			if (!is_null($value['vars']['varsValue'])) {

				$numBudget = $value['varsValue']['numBudget'];
				$numNext = $value['varsValue']['numNext'];
				$numDiff = $value['varsValue']['numDiff'];
				$numRate = $value['vars']['numRate'];

				if (!is_null($value['vars']['flagUse'])) {
					if ((int) $value['vars']['flagUse']) {
						$arrayCsv[] = array('', $strTitle, $numBudget, $numNext, $numDiff, $numRate);
					}
				} else {
					$arrayCsv[] = array('', $strTitle, $numBudget, $numNext, $numDiff, $numRate);
				}

			} else {
				$arrayCsv[] = array($strTitle);
			}

			if ($value['child']) {
				$data = $this->_getVarsLoopCsvSpan(array(
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
}
