<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_LedgerOutput extends Code_Else_Plugin_Accounting_Jpn_Ledger
{
	protected $_childSelf = array(
		'pathVarsPrint' => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/printLedger.php',
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
			'flagFiscalPeriod'  => $varsRequest['query']['jsonValue']['vars']['FlagFiscalPeriod'],
			'idDepartment'      => $varsRequest['query']['jsonValue']['vars']['IdDepartment'],
			'idAccountTitle'    => $varsRequest['query']['jsonValue']['vars']['IdAccountTitle'],
			'idSubAccountTitle' => $varsRequest['query']['jsonValue']['vars']['IdSubAccountTitle'],
			'flagType'          => $varsRequest['query']['jsonValue']['vars']['FlagType'],
		);

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
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
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
			'varsItem' => $varsItem,
		))

	 */
	protected function _getVarsPrint($arr)
	{
		$varsPrint = array();
		if ($arr['varsFlag']['flagType'] == 'search') {
			$arr['varsFlag']['flagFS'] = $arr['varsItem']['arrAccountTitle']['arrStrTitle'][$arr['varsFlag']['idAccountTitle']]['flagFS'];
			$data = $this->_getSearch(array(
				'numLotNow' => null,
				'vars'      => $arr['vars'],
				'varsItem'  => $arr['varsItem'],
				'varsFlag'  => $arr['varsFlag'],
			));
			$arr['vars']['portal']['varsList']['varsDetail'] = array();
			$arr['vars'] = $this->_updateSearch(array(
				'numLotNow' => null,
				'vars'      => $arr['vars'],
				'varsItem'  => $arr['varsItem'],
				'varsFlag'  => $arr['varsFlag'],
				'rows'      => $data['rows'],
				'numPrev'   => $data['numPrev'],
				'flagLast'  => $data['flagLast'],
			));
			$varsPrint = $this->_getVarsPrintLoop(array(
				'vars'       => $arr['vars'],
				'varsPrint'  => $varsPrint,
				'varsItem'   => $arr['varsItem'],
				'varsFlag'   => $arr['varsFlag'],
				'varsDetail' => $arr['vars']['portal']['varsList']['varsDetail'],
			));

		} elseif ($arr['varsFlag']['flagType'] == 'accountTitle') {

			$flagCount = 0;
			$array = $arr['varsItem']['arrAccountTitle']['arrSelectTag'];
			foreach ($array as $key => $value) {
				$idAccountTitle = $value['value'];
				if ($value['flagDisabled']) {
					continue;
				}
				$flagUse = $arr['varsItem']['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['flagUse'];
				if (!$flagUse) {
					continue;
				}

				$arr['varsFlag']['idAccountTitle'] = $idAccountTitle;
				$arr['varsFlag']['idSubAccountTitle'] = 'none';
				$flagFS = $arr['varsItem']['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['flagFS'];
				$arr['varsFlag']['flagFS'] = $flagFS;

				$data = $this->_getSearch(array(
					'numLotNow' => null,
					'vars'      => $arr['vars'],
					'varsItem'  => $arr['varsItem'],
					'varsFlag'  => $arr['varsFlag'],
				));

				$arr['vars']['portal']['varsList']['varsDetail'] = array();
				$arr['vars'] = $this->_updateSearch(array(
					'numLotNow' => null,
					'vars'      => $arr['vars'],
					'varsItem'  => $arr['varsItem'],
					'varsFlag'  => $arr['varsFlag'],
					'rows'      => $data['rows'],
					'numPrev'   => $data['numPrev'],
					'flagLast'  => $data['flagLast'],
				));

				$varsPrint = $this->_getVarsPrintLoop(array(
					'flagCount'  => $flagCount,
					'vars'       => $arr['vars'],
					'varsPrint'  => $varsPrint,
					'varsItem'   => $arr['varsItem'],
					'varsFlag'   => $arr['varsFlag'],
					'varsDetail' => $arr['vars']['portal']['varsList']['varsDetail'],
				));
				$flagCount++;
			}


		} elseif ($arr['varsFlag']['flagType'] == 'subAccountTitle') {
			$flagCount = 0;
			$array = $arr['varsItem']['arrAccountTitle']['arrSelectTag'];
			foreach ($array as $key => $value) {
				$idAccountTitle = $value['value'];
				if ($value['flagDisabled']) {
					continue;
				}
				$flagUse = $arr['varsItem']['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['flagUse'];
				if (!$flagUse) {
					continue;
				}
				$arraySub = $arr['varsItem']['arrSubAccountTitle']['arrSelectTag'][$idAccountTitle];
				if (!$arraySub) {
					continue;
				}
				foreach ($arraySub as $keySub => $valueSub) {
					$arr['varsFlag']['idAccountTitle'] = $idAccountTitle;
					$arr['varsFlag']['idSubAccountTitle'] = $valueSub['value'];
					$flagFS = $arr['varsItem']['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['flagFS'];
					$arr['varsFlag']['flagFS'] = $flagFS;

					$data = $this->_getSearch(array(
						'numLotNow' => null,
						'vars'      => $arr['vars'],
						'varsItem'  => $arr['varsItem'],
						'varsFlag'  => $arr['varsFlag'],
					));

					$arr['vars']['portal']['varsList']['varsDetail'] = array();
					$arr['vars'] = $this->_updateSearch(array(
						'numLotNow' => null,
						'vars'      => $arr['vars'],
						'varsItem'  => $arr['varsItem'],
						'varsFlag'  => $arr['varsFlag'],
						'rows'      => $data['rows'],
						'numPrev'   => $data['numPrev'],
						'flagLast'  => $data['flagLast'],
					));

					$varsPrint = $this->_getVarsPrintLoop(array(
						'flagCount'  => $flagCount,
						'vars'       => $arr['vars'],
						'varsPrint'  => $varsPrint,
						'varsItem'   => $arr['varsItem'],
						'varsFlag'   => $arr['varsFlag'],
						'varsDetail' => $arr['vars']['portal']['varsList']['varsDetail'],
					));
					$flagCount++;
				}
			}
			if (!$varsPrint) {
				$varsData = $this->_getVarsStatus(array(
					'vars'      => $arr['vars'],
					'varsFlag'  => $arr['varsFlag'],
					'varsItem'  => $arr['varsItem'],
					'flagReset' => 1
				));
				$varsPrint = $arr['vars']['varsPrint'];
				$varsPrint['varsStatus'] = $this->_getVarsStatusPrint(array(
					'varsData'   => $varsData,
					'vars'       => $arr['vars'],
					'varsPrint'  => $varsPrint,
					'varsItem'   => $arr['varsItem'],
					'varsFlag'   => $arr['varsFlag'],
				));
			}
		}

		return $varsPrint;
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
		$arr['varsItem']['varsPrintItem'] = $varsPrintItem;

		$vars = &$arr['vars'];
		$varsFlagFiscalPeriod = array();
		$arrStrTitle = array();
		$array = &$vars['portal']['varsNavi']['templateDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'FlagFiscalPeriod') {
				$arrayOption = $value['arrayOption'];
				foreach ($arrayOption as $keyOption => $valueOption) {
					$arrStrTitle[$valueOption['value']] = $valueOption['strTitle'];
				}
				break;
			}
		}
		$varsFlagFiscalPeriod['arrStrTitle'] = $arrStrTitle;
		$arr['varsItem']['varsFlagFiscalPeriod'] = $varsFlagFiscalPeriod;

		return $arr['varsItem'];
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
		$strMenu = $arr['vars']['varsItem']['varsOutput']['strTitleMenu'];

		if ($arr['varsFlag']['flagType'] == 'search') {
			if ($arr['varsFlag']['idSubAccountTitle'] != 'none') {
				$strMenu = $arr['vars']['varsItem']['varsOutput']['strTitleSubMenu'];
			}

		} elseif ($arr['varsFlag']['flagType'] == 'subAccountTitle') {
			$strMenu = $arr['vars']['varsItem']['varsOutput']['strTitleSubMenu'];
		}

		//strDepartment
		if ($arr['varsFlag']['idDepartment'] != 'none') {
			$strDepartment = $arr['varsItem']['varsDepartment']['arrStrTitle'][$arr['varsFlag']['idDepartment']]['strTitle'];
			$strMenu .= '_' . $strDepartment;
		}

		if ($arr['varsFlag']['flagType'] == 'search') {
			//strAccountTitle
			$strAccountTitle = $arr['varsItem']['arrAccountTitle']['arrStrTitle'][$arr['varsFlag']['idAccountTitle']]['strTitleFS'];
			$strMenu .= '_' . $strAccountTitle;

			//strSubAccountTitle
			if ($arr['varsFlag']['idSubAccountTitle'] != 'none') {
				$strSubAccountTitle = $arr['varsItem']['arrSubAccountTitle']['arrStrTitle'][$arr['varsFlag']['idAccountTitle']][$arr['varsFlag']['idSubAccountTitle']]['strTitle'];
				$strMenu .= '_' . $strSubAccountTitle;
			}
		}

		$strFlagFiscalPeriod = $arr['varsItem']['varsFlagFiscalPeriod']['arrStrTitle'][$arr['varsFlag']['flagFiscalPeriod']];
		$strMenu .= '_' . $strFlagFiscalPeriod;

		$strFileName = $this->_getFileTitle(array(
			'strMenu'     => $strMenu,
			'strFileType' => $arr['strFileType'],
		));

		return $strFileName;
	}

	/**
		(array(
				'flagFirst'  => $flagFirst,
			'vars'             => $vars,
			'varsItem'         => $varsItem,
			'varsFlag'         => $varsFlag,
			'varsDetail'       => $vars['portal']['varsList']['varsDetail'],
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
			'varsData'   => $varsData,
			'varsPrint'  => $varsPrint,
			'vars'       => $arr['vars'],
			'varsDetail' => $arr['varsDetail'],
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
			'flagCount'  => $arr['flagCount'],
			'varsData'   => $varsData,
			'varsPrint'  => $varsPrint,
			'vars'       => $arr['vars'],
			'varsDetail' => $arr['varsDetail'],
			'varsItem'   => $arr['varsItem'],
			'varsFlag'   => $arr['varsFlag'],
		))
	 */
	protected function _getVarsLoopPrint($arr)
	{
		$varsPrint = $arr['varsPrint'];

		$varsPrintItem = $arr['varsItem']['varsPrintItem'];
		$num = 0;
		// != 'BS'
		if (!$arr['varsDetail'] && $arr['varsFlag']['flagFS'] != 'BS') {
			$tmplRow = $varsPrint['varsDetailTmpl'];
			$tmplRow['idTmplTableTop'] = 'tmplTableTop';
			$tmplRow['id'] = $arr['varsFlag']['idAccountTitle'] . $num;
			$strFirst = $varsPrintItem['tmplRow']['tmplTrTop'];

			$tmplRow['flagBreak'] = 1;
			$tmplRow['strTitle'] = $arr['varsData']['strTitle'];
			$tmplRow['strTitleSub'] = $arr['varsData']['strTitleSub'];
			$tmplRow['strUnit'] = $arr['varsData']['strUnit'];

			$tmplRow['numTr'] = 1;
			$tmplRow['strRow'] =  $strFirst;
			$varsPrint['varsDetail'][] = $tmplRow;

			return $varsPrint;
		}

		$array = $arr['varsDetail'];

		foreach ($array as $key => $value) {
			$tmplRow = $varsPrint['varsDetailTmpl'];
			$tmplRow['id'] = $arr['varsFlag']['idAccountTitle'] . $num;
			$num++;
			$strFirst = '';
			if ($value['flagFirst']) {
				$strFirst = $varsPrintItem['tmplRow']['tmplTrTop'];
			}
			if ($value['vars']['flagPrev']) {
				if ($arr['flagCount']) {
					$tmplRow['flagBreak'] = 1;
					$tmplRow['idTmplTableTop'] = 'tmplTableTop';
					$tmplRow['strTitle'] = $arr['varsData']['strTitle'];
					$tmplRow['strTitleSub'] = $arr['varsData']['strTitleSub'];
					$tmplRow['strUnit'] = $arr['varsData']['strUnit'];
					$tmplRow['strUnit'] = $arr['varsData']['strUnit'];
				}
				$tmplRow['numTr'] = 1;
				$tmplRow['strRow'] = $strFirst . $this->_getVarsHtml(array(
					'varsData' => $arr['varsData'],
					'value'    => $value['varsColumnDetail'],
					'tmplStr'  => $varsPrintItem['tmplRow']['tmplTrPrev1'],
				));
				$varsPrint['varsDetail'][] = $tmplRow;

			} elseif ($value['vars']['flagNext']) {
				$tmplRow['numTr'] = 1;

				$tmplRow['strRow'] = $strFirst . $this->_getVarsHtml(array(
					'varsData' => $arr['varsData'],
					'value'    => $value['varsColumnDetail'],
					'tmplStr'  => $varsPrintItem['tmplRow']['tmplTrNext1'],
				));
				$varsPrint['varsDetail'][] = $tmplRow;

			} else {
				if ($arr['flagCount'] && $arr['varsFlag']['flagFS'] != 'BS' && $value['vars']['flagFirstRow']) {
					$tmplRow['flagBreak'] = 1;
					$tmplRow['idTmplTableTop'] = 'tmplTableTop';
					$tmplRow['strTitle'] = $arr['varsData']['strTitle'];
					$tmplRow['strTitleSub'] = $arr['varsData']['strTitleSub'];
					$tmplRow['strUnit'] = $arr['varsData']['strUnit'];
				}
				$tmplRow['numTr'] = 3;
				$tmplRow['strRow'] = $strFirst . $this->_getVarsHtml(array(
					'varsData' => $arr['varsData'],
					'value'    => $value['varsColumnDetail'],
					'tmplStr'  => $varsPrintItem['tmplRow']['tmplTr1'],
				));
				$tmplRow['strRow'] .= $this->_getVarsHtml(array(
					'varsData' => $arr['varsData'],
					'value'    => $value['varsColumnDetail'],
					'tmplStr'  => $varsPrintItem['tmplRow']['tmplTr2'],
				));
				$tmplRow['strRow'] .= $this->_getVarsHtml(array(
					'varsData' => $arr['varsData'],
					'value'    => $value['varsColumnDetail'],
					'tmplStr'  => $varsPrintItem['tmplRow']['tmplTr3'],
				));
				$varsPrint['varsDetail'][] = $tmplRow;
			}
		}

		return $varsPrint;
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
			'vars'     => $arr['vars'],
			'varsFlag' => $arr['varsFlag'],
		))
	 */
	protected function _getVarsStatus($arr)
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$vars = &$arr['vars'];

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];

		$varsData = $vars['varsItem']['varsOutput'];

		//strEntity
		$strEntity = $varsPluginAccountingEntity[$idEntity]['strTitle'];
		$varsData['strEntityExt'] = str_replace('<%replace%>', $strEntity, $vars['varsItem']['varsOutput']['strEntityExt']);
		$varsData['strEntity'] = $strEntity;

		//strNum
		$strNumRep = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$varsData['strNum'] = str_replace('<%replace%>', $strNumRep, $vars['varsItem']['varsOutput']['strNum']);
		$varsData['strNumExt'] = str_replace('<%replace%>', $strNumRep, $vars['varsItem']['varsOutput']['strNumExt']);

		//strPeriod
		$varsPeriod = $this->_getVarsFiscalPeriod(array(
				'flagFiscalPeriod' => 'f1',
				'numFiscalPeriod'  => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		/*20190401 start*/
		/*
		$str = $vars['varsItem']['varsOutput']['strPeriodExt'];
		$strPeriod = str_replace('<%strStartHeisei%>', $varsPeriod['numStartHeisei'], $str);
		$strPeriod = str_replace('<%strEndHeisei%>', $varsPeriod['numEndHeisei'], $strPeriod);
		$strPeriod = str_replace('<%strStartMonth%>', $varsPeriod['numStartMonth'], $strPeriod);
		$strPeriod = str_replace('<%strEndMonth%>', $varsPeriod['numEndMonth'], $strPeriod);
		$varsData['strPeriodExt'] = $strPeriod;
		*/

		$str = $vars['varsItem']['varsOutput']['strPeriodExt20190401'];
		$strPeriod = str_replace('<%strStartNengoYear%>', $varsPeriod['strStartNengoYear'], $str);
		$strPeriod = str_replace('<%strEndNengoYear%>', $varsPeriod['strEndNengoYear'], $strPeriod);
		$strPeriod = str_replace('<%strStartMonth%>', $varsPeriod['numStartMonth'], $strPeriod);
		$strPeriod = str_replace('<%strEndMonth%>', $varsPeriod['numEndMonth'], $strPeriod);
		$varsData['strPeriodExt'] = $strPeriod;
		/*20190401 end*/

		//strDepartment
		$strDepartment = '';
		$strDepartmentRep = '';
		if ($arr['varsFlag']['idDepartment'] != 'none') {
			$strDepartmentRep = $arr['varsItem']['varsDepartment']['arrStrTitle'][$arr['varsFlag']['idDepartment']]['strTitle'];
			$strDepartment = str_replace('<%replace%>', $strDepartmentRep, $varsData['strDepartmentExt']);
		}
		$varsData['strDepartmentExt'] = $strDepartment;
		$varsData['strDepartment'] = $strDepartmentRep;


		//strAccountTitle
		$strAccountTitleRep = $arr['varsItem']['arrAccountTitle']['arrStrTitle'][$arr['varsFlag']['idAccountTitle']]['strTitleFS'];
		$strAccountTitle = str_replace('<%replace%>', $strAccountTitleRep, $varsData['strAccountTitleExt']);
		$varsData['strAccountTitleExt'] = $strAccountTitle;
		$varsData['strAccountTitle'] = $strAccountTitleRep;

		//strSubAccountTitle
		$strSubAccountTitleRep = '';
		$strSubAccountTitle = '';
		if ($arr['varsFlag']['idSubAccountTitle'] != 'none') {
			$strSubAccountTitleRep = $arr['varsItem']['arrSubAccountTitle']['arrStrTitle'][$arr['varsFlag']['idAccountTitle']][$arr['varsFlag']['idSubAccountTitle']]['strTitle'];
			$strSubAccountTitle = str_replace('<%replace%>', $strSubAccountTitleRep, $varsData['strSubAccountTitleExt']);
		}
		$varsData['strSubAccountTitleExt'] = $strSubAccountTitle;
		$varsData['strSubAccountTitle'] = $strSubAccountTitleRep;

		$varsData['strTitle'] = $varsData['strAccountTitle'];
		$varsData['strTitleSub'] = $strEntity . '(' . $varsData['strNum'] . ')';
		if ($strDepartmentRep) {
			$varsData['strTitleSub'] .= ' ' . $strDepartmentRep;
		}


		$strFlagFiscalPeriod = $arr['varsItem']['varsFlagFiscalPeriod']['arrStrTitle'][$arr['varsFlag']['flagFiscalPeriod']];
		$varsData['strTitleSub'] .= ' ' . $strFlagFiscalPeriod;

		if ($arr['flagReset']) {
			$array = &$varsData;
			foreach ($array as $key => $value) {
				$array[$key] = '';
			}
		}

		return $varsData;
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
			'flagFiscalPeriod'  => $varsRequest['query']['jsonValue']['vars']['FlagFiscalPeriod'],
			'idDepartment'      => $varsRequest['query']['jsonValue']['vars']['IdDepartment'],
			'idAccountTitle'    => $varsRequest['query']['jsonValue']['vars']['IdAccountTitle'],
			'idSubAccountTitle' => $varsRequest['query']['jsonValue']['vars']['IdSubAccountTitle'],
			'flagType'          => $varsRequest['query']['jsonValue']['vars']['FlagType'],
		);

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
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

		if ($arr['varsFlag']['flagType'] == 'search') {
			$arr['varsFlag']['flagFS'] = $arr['varsItem']['arrAccountTitle']['arrStrTitle'][$arr['varsFlag']['idAccountTitle']]['flagFS'];
			$data = $this->_getSearch(array(
				'numLotNow' => null,
				'vars'      => $arr['vars'],
				'varsItem'  => $arr['varsItem'],
				'varsFlag'  => $arr['varsFlag'],
			));
			$arr['vars'] = $this->_updateSearch(array(
				'numLotNow' => null,
				'vars'      => $arr['vars'],
				'varsItem'  => $arr['varsItem'],
				'varsFlag'  => $arr['varsFlag'],
				'rows'      => $data['rows'],
				'numPrev'   => $data['numPrev'],
				'flagLast'  => $data['flagLast'],
			));
			$arrayCsv = $this->_getVarsCsvLoop(array(
				'vars'       => $arr['vars'],
				'arrayCsv'   => $arrayCsv,
				'varsItem'   => $arr['varsItem'],
				'varsFlag'   => $arr['varsFlag'],
				'varsDetail' => $arr['vars']['portal']['varsList']['varsDetail'],
			));


		} elseif ($arr['varsFlag']['flagType'] == 'accountTitle') {
			$flagCount = 0;
			$array = $arr['varsItem']['arrAccountTitle']['arrSelectTag'];
			foreach ($array as $key => $value) {
				$idAccountTitle = $value['value'];
				if ($value['flagDisabled']) {
					continue;
				}
				$flagUse = $arr['varsItem']['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['flagUse'];
				if (!$flagUse) {
					continue;
				}
				//開始残高がない場合＆期中仕訳が一個もない
				//会社、資本金
				//個人一般＆





				$arr['varsFlag']['idAccountTitle'] = $idAccountTitle;
				$arr['varsFlag']['idSubAccountTitle'] = 'none';
				$flagFS = $arr['varsItem']['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['flagFS'];
				$arr['varsFlag']['flagFS'] = $flagFS;

				$data = $this->_getSearch(array(
					'numLotNow' => null,
					'vars'      => $arr['vars'],
					'varsItem'  => $arr['varsItem'],
					'varsFlag'  => $arr['varsFlag'],
				));

				$arr['vars']['portal']['varsList']['varsDetail'] = array();
				$arr['vars'] = $this->_updateSearch(array(
					'numLotNow' => null,
					'vars'      => $arr['vars'],
					'varsItem'  => $arr['varsItem'],
					'varsFlag'  => $arr['varsFlag'],
					'rows'      => $data['rows'],
					'numPrev'   => $data['numPrev'],
					'flagLast'  => $data['flagLast'],
				));

				$arrayCsv = $this->_getVarsCsvLoop(array(
					'flagCount'  => $flagCount,
					'vars'       => $arr['vars'],
					'arrayCsv'   => $arrayCsv,
					'varsItem'   => $arr['varsItem'],
					'varsFlag'   => $arr['varsFlag'],
					'varsDetail' => $arr['vars']['portal']['varsList']['varsDetail'],
				));
				$flagCount++;
			}


		} elseif ($arr['varsFlag']['flagType'] == 'subAccountTitle') {
			$flagCount = 0;
			$array = $arr['varsItem']['arrAccountTitle']['arrSelectTag'];
			foreach ($array as $key => $value) {
				$idAccountTitle = $value['value'];
				if ($value['flagDisabled']) {
					continue;
				}
				$flagUse = $arr['varsItem']['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['flagUse'];
				if (!$flagUse) {
					continue;
				}
				$arraySub = $arr['varsItem']['arrSubAccountTitle']['arrSelectTag'][$idAccountTitle];
				if (!$arraySub) {
					continue;
				}
				foreach ($arraySub as $keySub => $valueSub) {
					$arr['varsFlag']['idAccountTitle'] = $idAccountTitle;
					$arr['varsFlag']['idSubAccountTitle'] = $valueSub['value'];
					$flagFS = $arr['varsItem']['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['flagFS'];
					$arr['varsFlag']['flagFS'] = $flagFS;

					$data = $this->_getSearch(array(
						'numLotNow' => null,
						'vars'      => $arr['vars'],
						'varsItem'  => $arr['varsItem'],
						'varsFlag'  => $arr['varsFlag'],
					));
					$arr['vars']['portal']['varsList']['varsDetail'] = array();
					$arr['vars'] = $this->_updateSearch(array(
						'numLotNow' => null,
						'vars'      => $arr['vars'],
						'varsItem'  => $arr['varsItem'],
						'varsFlag'  => $arr['varsFlag'],
						'rows'      => $data['rows'],
						'numPrev'   => $data['numPrev'],
						'flagLast'  => $data['flagLast'],
					));

					$arrayCsv = $this->_getVarsCsvLoop(array(
						'flagCount'  => $flagCount,
						'vars'       => $arr['vars'],
						'arrayCsv'   => $arrayCsv,
						'varsItem'   => $arr['varsItem'],
						'varsFlag'   => $arr['varsFlag'],
						'varsDetail' => $arr['vars']['portal']['varsList']['varsDetail'],
					));
					$flagCount++;
				}
			}
			if (!$arrayCsv) {
				$varsData = $this->_getVarsStatus(array(
					'vars'     => $arr['vars'],
					'varsFlag' => $arr['varsFlag'],
					'varsItem' => $arr['varsItem'],
				));
				$arrayCsv[] = array($varsData['strEntityExt']);
				$arrayCsv[] = array($varsData['strNumExt']);
				$arrayCsv[] = array($varsData['strPeriodExt']);
				$arrayCsv[] = array($varsData['strUnit']);
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
			'varsItem'   => $arr['varsItem'],
			'varsFlag'   => $arr['varsFlag'],
			'varsDetail' => $arr['vars']['portal']['varsList']['varsDetail'],
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

		$arrayCsv = $this->_getVarsLoopCsv(array(
			'flagCount'  => $arr['flagCount'],
			'varsData'   => $varsData,
			'arrayCsv'   => $arr['arrayCsv'],
			'vars'       => $arr['vars'],
			'varsDetail' => $arr['varsDetail'],
			'varsItem'   => $arr['varsItem'],
			'varsFlag'   => $arr['varsFlag'],
		));

		return $arrayCsv;
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

		$arrayCsv = $arr['arrayCsv'];

		if (!$arr['flagCount']) {
			$arrayCsv[] = array($classEscape->toComma(array('data' => $arr['varsData']['strEntityExt'])));
			$arrayCsv[] = array($arr['varsData']['strNumExt']);
			$arrayCsv[] = array($arr['varsData']['strPeriodExt']);
			$arrayCsv[] = array($arr['varsData']['strUnit']);
		}

		$arrayCsv[] = array();
		$arrayCsv[] = array($arr['varsData']['strTitle']);
		$arrayCsv[] = array($classEscape->toComma(array('data' => $arr['varsData']['strTitleSub'])));


		// != 'BS'
		if (!$arr['varsDetail']) {
			if ($arr['varsFlag']['flagFS'] != 'BS') {
				$rowData = $this->_getVarsLoopCsvColumn(array(
					'varsData' => $arr['varsData'],
				));
				$arrayCsv[] = $rowData;
			}

			return $arrayCsv;
		}

		$array = $arr['varsDetail'];
		foreach ($array as $key => $value) {
			if ($value['vars']['flagPrev']) {
				$rowData = $this->_getVarsLoopCsvColumn(array(
					'varsData' => $arr['varsData'],
				));
				$arrayCsv[] = $rowData;

				$rowData = array();
				$rowData[] = $value['varsColumnDetail']['strDateYear'];
				$rowData[] = '';
				$rowData[] = '';

				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = '';

				$rowData[] = '';
				$rowData[] = '';

				$rowData[] = $value['strTitle'];

				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = $value['vars']['numBalance'];
				$arrayCsv[] = $rowData;


			} elseif ($value['vars']['flagNext']) {
				$rowData = array();
				$rowData[] = $value['varsColumnDetail']['strDateYear'];
				$rowData[] = '';
				$rowData[] = '';

				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = '';

				$rowData[] = '';
				$rowData[] = '';

				$rowData[] = $value['strTitle'];

				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = $value['vars']['numBalance'];
				$arrayCsv[] = $rowData;

			} else {
				if ($arr['flagCount'] && $arr['varsFlag']['flagFS'] != 'BS' && $value['vars']['flagFirstRow']) {
					$rowData = $this->_getVarsLoopCsvColumn(array(
						'varsData' => $arr['varsData'],
					));
					$arrayCsv[] = $rowData;
				}
				$rowData = array();
				$rowData[] = $value['varsColumnDetail']['strDateYear'];
				if ($arr['varsData']['strBlank'] == $value['varsColumnDetail']['flagFiscalReportCut']) {
					$value['varsColumnDetail']['flagFiscalReportCut'] = '';
				}
				$rowData[] = $value['varsColumnDetail']['flagFiscalReportCut'];
				$rowData[] = $value['varsColumnDetail']['idLog'];

				$rowData[] = $classEscape->toComma(array('data' => $value['varsColumnDetail']['idAccountTitleContra']));
				$rowData[] = $classEscape->toComma(array('data' => $value['varsColumnDetail']['idDepartmentContra']));
				$rowData[] = $classEscape->toComma(array('data' => $value['varsColumnDetail']['idSubAccountTitleContra']));

				$rowData[] = $classEscape->toComma(array('data' => $value['varsColumnDetail']['idDepartment']));
				$rowData[] = $classEscape->toComma(array('data' => $value['varsColumnDetail']['idSubAccountTitle']));

				$rowData[] = $classEscape->toComma(array('data' => $value['strTitle']));

				$rowData[] = $value['vars']['flagDebit'];
				$rowData[] = $value['vars']['flagCredit'];
				$rowData[] = $value['vars']['numBalance'];
				$arrayCsv[] = $rowData;
			}
		}

		return $arrayCsv;
	}

	protected function _getVarsLoopCsvColumn($arr)
	{
		$rowData = array();
		$rowData[] = $arr['varsData']['strDate'];
		$rowData[] = $arr['varsData']['strFiscalReport'];
		$rowData[] = $arr['varsData']['strId'];

		$rowData[] = $arr['varsData']['strContra'] . $arr['varsData']['strAccountTitleColumn'];
		$rowData[] = $arr['varsData']['strContra'] . $arr['varsData']['strDepartmentColumn'];
		$rowData[] = $arr['varsData']['strContra'] . $arr['varsData']['strSubAccountTitleColumn'];

		$rowData[] = $arr['varsData']['strDepartmentColumn'];
		$rowData[] = $arr['varsData']['strSubAccountTitleColumn'];

		$rowData[] = $arr['varsData']['strMemo'];

		$rowData[] = $arr['varsData']['strDebit'];
		$rowData[] = $arr['varsData']['strCredit'];
		$rowData[] = $arr['varsData']['strBalance'];

		return $rowData;
	}

}
