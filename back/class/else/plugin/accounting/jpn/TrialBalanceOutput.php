<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_TrialBalanceOutput extends Code_Else_Plugin_Accounting_Jpn_TrialBalance
{
	protected $_childSelf = array(
		'pathVarsPrint' => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/printTrialBalance.php',
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
	protected function _iniListPrint()
	{
		global $classRequest;

		global $varsRequest;
		global $varsPluginAccountingAccount;

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
			'flagZero'         => $varsRequest['query']['jsonValue']['vars']['FlagZero'],
			'flagType'         => $varsRequest['query']['jsonValue']['vars']['FlagType'],
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
		));

		$varsItem = $this->_updateVarsItem(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$varsPrint = $this->_getVarsPrint(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
			'varsItem' => $varsItem,
		));

		$this->sendVars(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $varsPrint,
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
	protected function _updateVarsItem($arr)
	{
		$varsPrintItem = $this->_getVarsPrintItem();

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
		$arr['varsItem']['varsFlagFS'] = $varsFlagFS;
		$arr['varsItem']['varsFlagFiscalPeriod'] = $varsFlagFiscalPeriod;
		$arr['varsItem']['varsFlagUnit'] = $varsFlagUnit;

		return $arr['varsItem'];
	}

	/**

	 */
	protected function _getVarsPrintItem()
	{
		$vars = $this->getVars(array(
			'path' => $this->_childSelf['pathVarsPrint'],
		));

		return $vars;
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
		$varsPrint = array();
		if ($arr['varsFlag']['flagType'] == 'item') {
			$arr['vars'] = $this->_updateVars(array(
				'vars'     => $arr['vars'],
				'varsItem' => $arr['varsItem'],
				'varsFlag' => $arr['varsFlag'],
			));
			$data = $this->_getVarsPrintLoop(array(
				'vars'       => $arr['vars'],
				'varsPrint'  => $varsPrint,
				'varsItem'   => $arr['varsItem'],
				'varsFlag'   => $arr['varsFlag'],
				'varsFS'     => $arr['vars']['portal']['varsList']['varsDetail'],
			));
			$varsPrint = $data['varsPrint'];

		} elseif ($arr['varsFlag']['flagType'] == 'itemAll') {
			$flagCount = 0;
			$array = $arr['varsItem']['varsFlagFS']['arrStrTitle'];
			foreach ($array as $key => $value) {
				$flagFS = $key;
				$arr['varsFlag']['flagFS'] = $flagFS;
				$arr['vars']['portal']['varsList']['varsDetail'] = array();
				$arr['vars'] = $this->_updateVars(array(
					'vars'     => $arr['vars'],
					'varsItem' => $arr['varsItem'],
					'varsFlag' => $arr['varsFlag'],
				));
				$data = $this->_getVarsPrintLoop(array(
					'flagCount'  => $flagCount,
					'vars'       => $arr['vars'],
					'varsItem'   => $arr['varsItem'],
					'varsFlag'   => $arr['varsFlag'],
					'varsPrint'  => $varsPrint,
					'varsFS'     => $arr['vars']['portal']['varsList']['varsDetail'],
				));
				$varsPrint = $data['varsPrint'];
				$flagCount++;
			}
		}

		return $varsPrint;
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
		$varsData['strNum'] = str_replace('<%replace%>', $strNumRep, $arr['vars']['varsItem']['varsOutput']['strNum']);
		$varsData['strNumExt'] = str_replace('<%replace%>', $strNumRep, $arr['vars']['varsItem']['varsOutput']['strNumExt']);

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
		if ($arr['varsFlag']['idDepartment'] != 'none') {
			$strDepartmentRep = $arr['varsItem']['varsDepartment']['arrStrTitle'][$arr['varsFlag']['idDepartment']]['strTitle'];
			$strDepartment = str_replace('<%replace%>', $strDepartmentRep, $varsData['strDepartmentExt']);
		}
		$varsData['strDepartmentExt'] = $strDepartment;
		$varsData['strDepartment'] = $strDepartmentRep;

		//
		$strUnitRep = $arr['varsItem']['varsFlagUnit']['arrStrTitle'][$arr['varsFlag']['flagUnit']]['strTitle'];
		$strUnit = str_replace('<%replace%>', $strUnitRep, $arr['vars']['varsItem']['varsOutput']['strUnit']);
		$varsData['strUnit'] = $strUnit;

		$numMonth = $arr['varsItem']['varsEntityNation']['numFiscalBeginningMonth'];

		if (!preg_match( "/^(f)/", $arr['varsFlag']['flagFiscalPeriod'])
				&& $numMonth != $arr['varsFlag']['flagFiscalPeriod']
		) {
			$varsData['strPrev'] = $varsData['strPrevMonth'];
		} else {
			$varsData['strPrev'] = $varsData['strPrevTerm'];
		}

		//
		if ($arr['varsFlag']['flagFS'] == 'BS') {

		    /*20190401 start*/
		    /*
		    $str = $arr['vars']['varsItem']['varsOutput']['strPointExt'];
			$strPoint = str_replace('<%strEndHeisei%>', $varsPeriod['numEndHeisei'], $str);
			*/
		    $str = $arr['vars']['varsItem']['varsOutput']['strPointExt20190401'];
		    $strPoint = str_replace('<%strEndNengoYear%>', $varsPeriod['strEndNengoYear'], $str);
			/*20190401 end*/

			$strPoint = str_replace('<%strEndMonth%>', $varsPeriod['numEndMonth'], $strPoint);
			$varsData['strPointExt'] = $strPoint;
		}

		//strFS
		$varsData['strFS'] = $arr['varsItem']['varsFlagFS']['arrStrTitle'][$arr['varsFlag']['flagFS']]['strTitle'];

		$varsData['strTitleSub'] = $strEntity . '(' . $varsData['strNum'] . ') ' . $varsData['strFS'];
		if ($strDepartmentRep) {
			$varsData['strTitleSub'] .= ' ' . $varsData['strDepartment'];
		}

		$varsData['strTitleSub'] .= ' ' . $varsData['strPeriod'];

		return $varsData;
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

		$array = &$arr['varsFS'];
		foreach ($array as $key => $value) {

			$arrLevel = preg_split("/-/", $value['id']);
			$num = count($arrLevel) - 3;
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
				$tmplRow['strTitleSub'] = $arr['varsData']['strTitleSub'];
				$tmplRow['strUnit'] = $arr['varsData']['strUnit'];
			}
			$tmplRow['numTr'] = 1;
			$tmplRow['strRow'] = $strFirst . $this->_getVarsHtml(array(
				'varsData' => $arr['varsData'],
				'value'    => $value['varsPrint'],
				'tmplStr'  => $varsPrintItem['tmplRow']['tmplTr1'],
			));
			if ($arr['varsFlag']['flagFS'] == 'BS') {
				$tmplRow['idTmplColumn'] = 'tmplColumn2';
			} else {
				$tmplRow['idTmplColumn'] = 'tmplColumn';
			}

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

		$varsPrint['varsStatus']['varsTmpl']['tmplColumn2'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'tmplStr'  => $varsPrintItem['tmplColumn2'],
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
			'strFileType'   => '',
			'varsFlag'   => $arr['varsFlag'],
			'varsItem'   => $arr['varsItem'],
			'vars'       => $arr['vars'],
		))

	 */
	protected function _getStrTitle($arr)
	{
		$strMenu = $arr['vars']['varsItem']['varsOutput']['strTitle'];

		if ($arr['varsFlag']['flagType'] == 'item' || $arr['varsFlag']['flagType'] == 'spanAll') {
			$strFS = $arr['varsItem']['varsFlagFS']['arrStrTitle'][$arr['varsFlag']['flagFS']]['strTitle'];
			$strMenu .= '_' . $strFS;
		}

		if ($arr['varsFlag']['flagType'] != 'spanAll') {
			$strFlagFiscalPeriod = $arr['varsItem']['varsFlagFiscalPeriod']['arrStrTitle'][$arr['varsFlag']['flagFiscalPeriod']]['strTitle'];
			$strMenu .= '_' . $strFlagFiscalPeriod;
		}

		//strDepartment
		if ($arr['varsFlag']['idDepartment'] != 'none') {
			$strDepartment = $arr['varsItem']['varsDepartment']['arrStrTitle'][$arr['varsFlag']['idDepartment']]['strTitle'];
			$strMenu .= '_' . $strDepartment;
		}

		$strFileName = $this->_getFileTitle(array(
			'strMenu'     => $strMenu,
			'strFileType' => $arr['strFileType'],
		));

		return $strFileName;
	}

	/**
	 *
	 */
	protected function _iniListOutput()
	{
		global $classRequest;

		global $varsRequest;
		global $varsPluginAccountingAccount;

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
			'flagZero'         => $varsRequest['query']['jsonValue']['vars']['FlagZero'],
			'flagType'         => $varsRequest['query']['jsonValue']['vars']['FlagType'],
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
		));

		$varsItem = $this->_updateVarsItem(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$text = $this->_getCsv(array(
			'vars'      => $vars,
			'varsFlag'  => $varsFlag,
			'varsItem'  => $varsItem,
		));

		$strFileName = $this->_getStrTitle(array(
			'varsFlag'    => $varsFlag,
			'varsItem'    => $varsItem,
			'vars'        => $vars,
			'strFileType' => 'csv',
		));
		$text = mb_convert_encoding($text, 'sjis', 'utf8');
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

		if ($arr['varsFlag']['flagType'] == 'item') {
			$arr['vars'] = $this->_updateVars(array(
				'vars'     => $arr['vars'],
				'varsItem' => $arr['varsItem'],
				'varsFlag' => $arr['varsFlag'],
			));

			$data = $this->_getVarsCsvLoop(array(
				'vars'       => $arr['vars'],
				'arrayCsv'   => $arrayCsv,
				'varsItem'   => $arr['varsItem'],
				'varsFlag'   => $arr['varsFlag'],
				'varsFS'     => $arr['vars']['portal']['varsList']['varsDetail'],
			));
			$arrayCsv = $data['arrayCsv'];

		} elseif ($arr['varsFlag']['flagType'] == 'itemAll') {
			$flagCount = 0;
			$array = $arr['varsItem']['varsFlagFS']['arrStrTitle'];
			foreach ($array as $key => $value) {
				$flagFS = $key;
				$arr['varsFlag']['flagFS'] = $flagFS;
				$arr['vars']['portal']['varsList']['varsDetail'] = array();
				$arr['vars'] = $this->_updateVars(array(
					'vars'     => $arr['vars'],
					'varsItem' => $arr['varsItem'],
					'varsFlag' => $arr['varsFlag'],
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

		} elseif ($arr['varsFlag']['flagType'] == 'spanAll') {
			$flagCount = 0;
			$varsList = array();
			$array = $arr['varsItem']['varsFlagFiscalPeriod']['arrStrTitle'];
			foreach ($array as $key => $value) {
				$flagFiscalPeriod = $key;
				$arr['varsFlag']['flagFiscalPeriod'] = $flagFiscalPeriod;
				$arr['vars']['portal']['varsList']['varsDetail'] = array();
				$arr['vars'] = $this->_updateVars(array(
					'vars'     => $arr['vars'],
					'varsItem' => $arr['varsItem'],
					'varsFlag' => $arr['varsFlag'],
				));
				$data = $this->_getVarsCsvLoop(array(
					'flagCount'  => $flagCount,
					'vars'       => $arr['vars'],
					'arrayCsv'   => $arrayCsv,
					'varsItem'   => $arr['varsItem'],
					'varsFlag'   => $arr['varsFlag'],
					'varsList'   => $varsList,
					'varsFS'     => $arr['vars']['portal']['varsList']['varsDetail'],
				));
				$arrayCsv = $data['arrayCsv'];
				$varsList = $data['varsList'];
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

		if ($arr['varsFlag']['flagType'] == 'spanAll') {
			$data = $this->_getVarsLoopCsvSpan(array(
				'flagCount'  => $arr['flagCount'],
				'flagFirst'  => 1,
				'varsData'   => $varsData,
				'arrayCsv'   => $arr['arrayCsv'],
				'vars'       => $arr['vars'],
				'varsFS'     => $arr['varsFS'],
				'varsItem'   => $arr['varsItem'],
				'varsFlag'   => $arr['varsFlag'],
				'varsList'   => $arr['varsList'],
			));

		} else {
			$data = $this->_getVarsLoopCsv(array(
				'flagCount'  => $arr['flagCount'],
				'flagFirst'  => 1,
				'varsData'   => $varsData,
				'arrayCsv'   => $arr['arrayCsv'],
				'vars'       => $arr['vars'],
				'varsFS'     => $arr['varsFS'],
				'varsItem'   => $arr['varsItem'],
				'varsFlag'   => $arr['varsFlag'],
			));
		}


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
			'varsList'   => $arr['varsList'],
		))
	 */
	protected function _getVarsLoopCsvSpan($arr)
	{
		global $classEscape;

		$arrayCsv = &$arr['arrayCsv'];
		$varsList = &$arr['varsList'];

		if ($arr['flagFirst']) {
			$arr['flagFirst'] = 0;
			if (!$arr['flagCount']) {
				$arrayCsv[] = array($classEscape->toComma(array('data' => $arr['varsData']['strEntityExt'])));
				$arrayCsv[] = array($arr['varsData']['strNumExt']);
				$arrayCsv[] = array($arr['varsData']['strPeriodExt']);
				if ($arr['varsFlag']['flagFS'] == 'BS') {
					$arrayCsv[] = array($arr['varsData']['strPointExt']);
				}
				$arrayCsv[] = array($arr['varsData']['strUnit']);
				$arrayCsv[] = array($classEscape->toComma(array('data' => $arr['varsData']['strFS'])));
				$arrayCsv[] = array();
				$rowData = array();
				$rowData[] = $arr['varsData']['strAccountTitle'];
				$rowData[] = '';
				$arrayCsv[] = $rowData;
				end($arrayCsv);
				$arr['varsList']['flagFirst'] = key($arrayCsv);
			}
			$arrayCsv[$arr['varsList']['flagFirst']][] = $arr['varsItem']['varsFlagFiscalPeriod']['arrStrTitle'][$arr['varsFlag']['flagFiscalPeriod']]['strTitle'];
		}

		$array = &$arr['varsFS'];
		foreach ($array as $key => $value) {

			$strTitle = $classEscape->toComma(array('data' => $value['varsPrint']['strTitle']));
			if (!is_null($value['vars']['varsValue'])) {

				$sumNext = $value['vars']['varsValue']['sumNext'];

				if (!is_null($value['vars']['flagUse'])) {
					//flagUse
					if ((int) $value['vars']['flagUse']) {
						if (!$arr['flagCount']) {
							$arrayCsv[] = array('', $strTitle, $sumNext);
							end($arrayCsv);
							$arr['varsList'][$value['vars']['idTarget']] = key($arrayCsv);

						} else {
							$arrayCsv[$arr['varsList'][$value['vars']['idTarget']]][] = $sumNext;
						}
					}
				} else {
					if (!$arr['flagCount']) {
						$arrayCsv[] = array('', $strTitle, $sumNext);
						end($arrayCsv);
						$arr['varsList'][$value['vars']['idTarget']] = key($arrayCsv);

					} else {
						$arrayCsv[$arr['varsList'][$value['vars']['idTarget']]][] = $sumNext;
					}
				}

			} else {
				if (!$arr['flagCount']) {
					$arrayCsv[] = array($strTitle);
				}
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
					'varsList'  => $arr['varsList'],
				));
				$arrayCsv =  $data['arrayCsv'];
				$varsList =  $data['varsList'];
			}
		}

		return $arr;
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
				$arrayCsv[] = array($arr['varsData']['strPeriodExt']);
				if ($arr['varsFlag']['flagFS'] == 'BS') {
					$arrayCsv[] = array($arr['varsData']['strPointExt']);
				}
				$arrayCsv[] = array($arr['varsData']['strUnit']);
			}

			$arrayCsv[] = array();
			$arrayCsv[] = array($arr['varsData']['strTitle']);
			$arrayCsv[] = array($classEscape->toComma(array('data' => $arr['varsData']['strTitleSub'])));
			$rowData = $this->_getVarsLoopCsvColumn(array(
				'varsData' => $arr['varsData'],
				'varsFlag' => $arr['varsFlag'],
			));
			$arrayCsv[] = $rowData;
		}

		$array = &$arr['varsFS'];
		foreach ($array as $key => $value) {

			$strTitle = $classEscape->toComma(array('data' => $value['varsPrint']['strTitle']));
			if (!is_null($value['vars']['varsValue'])) {

				$sumPrev = $value['vars']['varsValue']['sumPrev'];
				$sumDebit = $value['vars']['varsValue']['sumDebit'];
				$sumCredit = $value['vars']['varsValue']['sumCredit'];
				$sumNext = $value['vars']['varsValue']['sumNext'];
				$numRate = $value['vars']['varsValue']['numRate'];

				if (!is_null($value['vars']['flagUse'])) {
					//flagUse
					if ((int) $value['vars']['flagUse']) {
						$arrayCsv[] = array('', $strTitle, $sumPrev, $sumDebit, $sumCredit, $sumNext, $numRate);
					}
				} else {
					$arrayCsv[] = array('', $strTitle, $sumPrev, $sumDebit, $sumCredit, $sumNext, $numRate);
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
		$strRate = $arr['varsData']['strRatePLCR'];
		if ($arr['varsFlag']['flagFS'] == 'BS') {
			$strRate = $arr['varsData']['strRateBS'];
		}
		$rowData = array();
		$rowData[] = $arr['varsData']['strAccountTitle'];
		$rowData[] = '';
		$rowData[] = $arr['varsData']['strPrev'];
		$rowData[] = $arr['varsData']['strDebit'];
		$rowData[] = $arr['varsData']['strCredit'];
		$rowData[] = $arr['varsData']['strBalance'];
		$rowData[] = $strRate;

		return $rowData;
	}
}
