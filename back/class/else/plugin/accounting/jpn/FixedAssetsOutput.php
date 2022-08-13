<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_FixedAssetsOutput extends Code_Else_Plugin_Accounting_Jpn_FixedAssets
{
	protected $_childSelf = array(
		'pathVarsPrint' => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/printFixedAssets.php',
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
		global $varsPluginAccountingPreference;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$varsAuthority = $this->_getVarsAuthority(array());

		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllOutput'] || $varsAuthority['flagMyOutput'])) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
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

		$this->checkSearch(array(
			'arrOrder' => $vars['portal']['varsNavi']['search']['varsDetail']['varsSearchSort'],
			'arrWhere' => $vars['portal']['varsNavi']['search']['varsDetail']['varsSearchItem'],
		));

		if (!$varsRequest['query']['jsonSearch']['ph']['arrOrder']) {
			$varsRequest['query']['jsonSearch']['ph']['arrOrder']= array(
				'strColumn' => 'id',
				'flagDesc'  => 1,
			);
		}

		$rows = $this->getSearch(array(
			'idModule'   => 'accounting',
			'strTable'   => 'accountingLogFixedAssets' . $strNation,
			'arrJoin'    => array(),
			'arrOrder'   => $varsRequest['query']['jsonSearch']['ph']['arrOrder'],
			'insCurrent' => $this,
			'arrWhere'   => $varsRequest['query']['jsonSearch']['ph']['arrWhere'],
		));

		$varsItem = $this->_updateVarsItem(array(
			'varsItem' => $varsItem,
			'vars'     => $vars,
		));

		$varsPrint = $this->_getVarsPrint(array(
			'vars'     => $vars,
			'rows'     => $rows,
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

		))

	 */
	protected function _updateVarsItem($arr)
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$varsPrintItem = $this->getVars(array(
			'path' => $this->_childSelf['pathVarsPrint'],
		));

		$varsIdVarsDetail = $this->_getVarsIdVarsDetail(array(
			'vars' => $arr['vars']['portal']['varsDetail']['templateDetail']
		));

		$varsStrTitle = $this->_getCsvVarsDetailStrTitle(array(
			'vars' => $arr['vars']['portal']['varsDetail']['templateDetail']
		));

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriodStart = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriodStart'];
		$numFiscalPeriodEnd = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];

		for ($i = $numFiscalPeriodStart; $i <= $numFiscalPeriodEnd; $i++) {
			$numFiscalPeriod = $i;
			$varsPeriod[$numFiscalPeriod] = $this->_getVarsFiscalPeriod(array(
				'flagFiscalPeriod' => 'f1',
				'numFiscalPeriod'  => $numFiscalPeriod,
			));
		}

		$arr['varsItem']['varsPeriod'] = $varsPeriod;
		$arr['varsItem']['varsPrintItem'] = $varsPrintItem;
		$arr['varsItem']['varsStrTitle'] = $varsStrTitle;
		$arr['varsItem']['varsIdVarsDetail'] = $varsIdVarsDetail;

		return $arr['varsItem'];
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

		$varsData = array();

		$varsData = $this->_getVarsStatus(array(
			'vars'     => $arr['vars'],
			'varsItem' => $arr['varsItem'],
		));

		$varsPrint = $arr['vars']['varsPrint'];
		$varsPrint['varsStatus'] = $this->_getVarsStatusPrint(array(
			'varsData'   => $varsData,
			'vars'       => $arr['vars'],
			'varsPrint'  => $varsPrint,
			'varsItem'   => $arr['varsItem'],
			'rows'       => $arr['rows'],
		));

		$flagCount = 0;
		$array = $arr['rows']['arrRows'];
		foreach ($array as $key => $value) {
			if (preg_match("/^(sum|one)$/", $value['flagDepMethod'])) {
				continue;
			}
			$rows = $this->_getDetailLogTarget(array(
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				'idTarget'        => $value['idFixedAssets'],
			));
			$arr['vars']['portal']['varsList']['varsDetail'] = array();
			$arr['vars'] = $this->_updateSearch(array(
				'vars'     => $arr['vars'],
				'varsItem' => $arr['varsItem'],
				'rows'     => $rows,
			));
			$varsPrint = $this->_getVarsPrintLoop(array(
				'flagCount'  => $flagCount,
				'vars'       => $arr['vars'],
				'varsItem'   => $arr['varsItem'],
				'rows'       => $rows,
				'varsData'   => $varsData,
				'varsPrint'  => $varsPrint,
			));
			$flagCount++;
		}

		return $varsPrint;
	}

	/**
		(array(
			'varsData'   => $varsData,
			'vars'       => $arr['vars'],
			'varsPrint'  => $varsPrint,
			'varsItem'   => $arr['varsItem'],
		))
	 */
	protected function _getVarsStatusPrint($arr)
	{
		$varsPrint = $arr['varsPrint'];
		$varsPrintItem = $arr['varsItem']['varsPrintItem'];

		$varsRow = reset($arr['rows']['arrRows']);
		if (!$varsRow['flagRemove']) {
			$arr['varsData']['flagStatusFirst'] = '';
		}

		//tmplWrap
		$varsPrint['varsStatus']['varsTmpl']['tmplWrap'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'tmplStr'  => $varsPrintItem['tmplWrap'],
		));

		$varsPrint['varsStatus']['varsTmpl']['tmplColumn'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'tmplStr'  => $varsPrintItem['tmplColumn'],
		));

		$varsPrint['varsStatus']['varsTmpl']['tmplColumnStatus'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'tmplStr'  => $varsPrintItem['tmplColumnStatus'],
		));

		$varsPrint['varsStatus']['varsTmpl']['tmplTable'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'tmplStr'  => $varsPrintItem['tmplTable'],
		));

		$varsPrint['varsStatus']['varsTmpl']['tmplTableStatus'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'tmplStr'  => $varsPrintItem['tmplTableStatus'],
		));

		$varsPrint['varsStatus']['varsTmpl']['tmplTableTop'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'tmplStr'  => $varsPrintItem['tmplTableTop'],
		));

		$varsPrint['varsStatus']['varsTmpl']['tmplPage'] = $varsPrintItem['tmplPage'];

		$varsPrint['varsStatus']['strTitle'] = $this->_getFileTitle(array(
			'strMenu' => $arr['vars']['varsItem']['varsOutput']['strTitleSheet'],
		));

		return $varsPrint['varsStatus'];
	}

	/**
		(array(
			'flagCount'  => $flagCount,
			'vars'       => $arr['vars'],
			'varsItem'   => $arr['varsItem'],
			'varsPrint'  => $varsPrint,
		))
	 */
	protected function _getVarsPrintLoop($arr)
	{
		$varsValue = $this->_getVarsLoopValue(array(
			'vars'      => $arr['vars'],
			'varsItem'  => $arr['varsItem'],
			'rows'      => $arr['rows'],
			'flagPrint' => 1,
		));

		$varsPrint = $this->_getVarsLoopPrint(array(
			'flagCount'  => $arr['flagCount'],
			'flagFirst'  => 1,
			'varsData'   => $arr['varsData'],
			'varsPrint'  => $arr['varsPrint'],
			'vars'       => $arr['vars'],
			'varsItem'   => $arr['varsItem'],
			'varsValue'  => $varsValue,
		));

		return $varsPrint;
	}

	/**
	 *
	 */
	protected function _getVarsLoopValue($arr)
	{
		global $classTime;
		global $classEscape;

		$vars = &$arr['vars'];
		$varsItem = &$arr['varsItem'];

		$varsData = array();
		$arrayData = array();
		$varsStrTitle = $varsItem['varsStrTitle'];
		$array = $vars['varsItem']['varsTitle'];
		foreach ($array as $key => $value) {
			$varsStrTitle[$key] = $value;
		}

		$rowData = array();
		$rowData[] = $vars['varsItem']['varsOutput']['strStatus'];
		$array = $varsStrTitle;
		foreach ($array as $key => $value) {
			$rowData[] = $value;
		}

		$array = $vars['portal']['varsList']['varsDetail'];
		foreach ($array as $key => $value) {
			$rowData = array();
			$rowData['flagStatus'] = $value['varsColumnDetail']['flagStatus'];
			$rowData['flagRemove'] = (int) $value['flagRemove'];
			$arrayId = $varsStrTitle;
			foreach ($arrayId as $keyId => $valueId) {
				$str = '';
				if ($keyId == 'strTitle' || $keyId == 'strMemo') {
					$data = $value['jsonDetail'][$keyId];
					$str = $value['jsonDetail'][$keyId];

				} elseif (preg_match("/^(idAccountTitle)$/", $keyId)) {
					$data = $value['jsonDetail']['jsonDetail'][$keyId];
					if ($data != 'none') {
						if (!$varsItem['arrAccountTitle']['arrStrTitle'][$data]) {
							$str = $vars['varsItem']['strLost'];

						} else {
							$str = $varsItem['arrAccountTitle']['arrStrTitle'][$data]['strTitleFS'];
						}
					}

				} elseif (preg_match("/^(flagDepMethod|flagTaxFixed|flagTaxFixedType|flagDepUp|flagDepDown)$/", $keyId)) {
					$data = $value['jsonDetail']['jsonDetail'][$keyId];
					if ($data != 'none') {
						$str = $varsItem['varsOptions'][$keyId]['arrStrTitle'][$data];
					}

				} elseif (preg_match("/^(numUsefulLife)$/", $keyId)) {
					$data = $value['jsonDetail']['jsonDetail'][$keyId];
					$str = '';
					$flagDepMethod = $value['jsonDetail']['jsonDetail']['flagDepMethod'];
					if ($flagDepMethod == 'straight' || $flagDepMethod == 'declining' || $flagDepMethod == 'average') {
						$str = $data . $vars['varsItem']['varsOutput']['strYear'];
					}

				} elseif (preg_match("/^(idDepartment)$/", $keyId)) {
					$data = $value['jsonDetail']['jsonDetail'][$keyId];
					if ($data) {
						if (!$varsItem['arrDepartment']['arrStrTitle'][$data]) {
							$str = $vars['varsItem']['strLost'];

						} else {
							$str = $varsItem['arrDepartment']['arrStrTitle'][$data]['strTitle'];
						}
					}

				} elseif (preg_match("/^stamp/", $keyId)) {
					$data = $value['jsonDetail']['jsonDetail'][$keyId];
					$rowData[$keyId . 'Ext'] = '';
					if ($data) {
						$arrDate = $classTime->getLocal(array('stamp' => $data));
						$str = $arrDate['strYear'] . '/' . $arrDate['strMonth'] . '/' . $arrDate['strDate'];

						$numYear = $classTime->getNengoYear(array('stamp' => $data, 'numYear' => $arrDate['year']));
						$flagNengo = $classTime->getFlagNengo(array('stamp' => $data));
						/*20190401 start*/
						$strNengoYear = $classTime->getStrNengoYear(array('stamp' => $data, 'numYear' => $arrDate['year']));

						$rowData[$keyId . 'Ext'] = str_replace('<%replace%>', $strNengoYear, $vars['varsItem']['varsOutput']['strNengo']);
						/*20190401 end*/
						$rowData[$keyId . 'Ext'] .= $str;

					}

				} elseif (preg_match("/^arrCommaDepMonth$/", $keyId)) {
					$data = $value['jsonDetail']['jsonDetail'][$keyId];
					$numFiscalTermMonth = $varsItem['varsEntityNation']['numFiscalTermMonth'];
					$arrCommaDepMonth = $classEscape->splitCommaArrayData(array('data' => $data));
					$str = count($arrCommaDepMonth) . '/' . $numFiscalTermMonth;

				} elseif (preg_match("/^id$/", $keyId)) {
					$data = $value['id'];
					$str = $data;

				} else {
					$data = $value['jsonDetail']['jsonDetail'][$keyId];
					$str = $data;
				}

				$rowData['value_' . $keyId] = $data;
				$rowData[$keyId] = $str;
			}


			$rowData['numVolume'] .= $rowData['flagDepUnit'];
			if ($rowData['numSurvivalRate']) {
				$rowData['numSurvivalRate'] .= $vars['varsItem']['varsOutput']['strPer'];
			}
			if ($rowData['numSurvivalRateLimit']) {
				$rowData['numSurvivalRateLimit'] .= $vars['varsItem']['varsOutput']['strPer'];
			}
			if (is_null($rowData['numRatioOperate'])) {
				$rowData['numRatioOperate'] = '100.00' . $vars['varsItem']['varsOutput']['strPer'];
			}
			if (is_null($rowData['idDepartment'])) {
				$rowData['idDepartment'] = '';
			}

			$varsPeriod = $arr['varsItem']['varsPeriod'][$value['vars']['numFiscalPeriod']];
			$arrayStr = array('stampEnd', 'stampStart');
			foreach ($arrayStr as $keyStr => $valueStr) {
				$data = $varsPeriod[$valueStr];
				$arrDate = $classTime->getLocal(array('stamp' => $data));
				$str = $arrDate['strYear'] . '/' . $arrDate['strMonth'] . '/' . $arrDate['strDate'];

				$numYear = $classTime->getNengoYear(array('stamp' => $data, 'numYear' => $arrDate['year']));
				$flagNengo = $classTime->getFlagNengo(array('stamp' => $data));
				/*20190401 start*/
				$strNengoYear = $classTime->getStrNengoYear(array('stamp' => $data, 'numYear' => $arrDate['year']));

				$rowData[$valueStr . 'PeriodExt'] = str_replace('<%replace%>', $strNengoYear, $vars['varsItem']['varsOutput']['strNengo']);
				/*20190401 end*/
				$rowData[$valueStr . 'PeriodExt'] .= $str;
			}

			$rowData['numFiscalPeriod'] = $value['vars']['numFiscalPeriod'];

			$arrayData[] = $rowData;
		}

		return $arrayData;
	}

	/**
		(array(
			'flagCount'  => $arr['flagCount'],
			'flagFirst'  => 1,
			'varsData'   => $varsData,
			'varsPrint'  => $varsPrint,
			'vars'       => $arr['vars'],
			'varsItem'   => $arr['varsItem'],
			'varsValue'  => $varsValue,
		))

	 */
	protected function _getVarsLoopPrint($arr)
	{
		$vars = $arr['vars'];
		$varsItem = &$arr['varsItem'];
		$varsPrint = &$arr['varsPrint'];
		$varsPrintItem = $arr['varsItem']['varsPrintItem'];
		$varsValueLast = end($arr['varsValue']);

		$tmplRow = $varsPrint['varsDetailTmpl'];
		if ($arr['flagFirst']) {
			$arr['flagFirst'] = 0;
			if ($arr['flagCount']) {
				$tmplRow['flagBreak'] = 1;
				$tmplRow['idTmplTableTop'] = 'tmplTableTop';
			}
		}

		$tmplRow['id'] = $arr['flagCount'] . '_' . 'Status';
		$tmplRow['flagColumnNone'] = 1;
		$tmplRow['idTmplTable'] = 'tmplTableStatus';
		$tmplRow['idTmplColumn'] = 'tmplColumnStatus';

		$tmplRow['numTr'] = 9;
		$varsStr = $varsValueLast;
		$varsStr['numValue'] = $this->_getNumberFormat($varsValueLast['numValue']);
		$varsStr['numValueCompression'] = $this->_getNumberFormat($varsValueLast['numValueCompression']);


		$varsStr['numSurvivalRate'] = $varsValueLast['numSurvivalRate'];
		if ($varsValueLast['numValueNet']) {
			$numRate = $varsValueLast['value_numSurvivalRate'];
			$flagType = $arr['varsItem']['varsCalc']['flagFractionDepSurvivalRate'];
			$numValue = $this->_updateCalc(array(
				'flagType' => $flagType,
				'num'      => $varsValueLast['numValueNet'] * ($numRate / 100),
				'numLevel' => 0
			));
			if ($arr['varsItem']['varsIdVarsDetail']['numSurvivalRate']['varsForm']['FlagDepMethod'][$varsValueLast['value_flagDepMethod']]) {
				$varsStr['numSurvivalRate'] .= '( ' . $this->_getNumberFormat($numValue). ' )';
			} else {
				$varsStr['numSurvivalRate'] = $arr['varsData']['strBlank'];
			}
		}

		$varsStr['numSurvivalRateLimit'] = $varsValueLast['numSurvivalRateLimit'];
		if ($varsValueLast['numValueNet']) {
			$numRate = $varsValueLast['value_numSurvivalRateLimit'];
			$flagType = $arr['varsItem']['varsCalc']['flagFractionDepSurvivalRateLimit'];
			$numValue = $this->_updateCalc(array(
				'flagType' => $flagType,
				'num'      => $varsValueLast['numValueNet'] * ($numRate / 100),
				'numLevel' => 0
			));
			if ($arr['varsItem']['varsIdVarsDetail']['numSurvivalRateLimit']['varsForm']['FlagDepMethod'][$varsValueLast['value_flagDepMethod']]) {
				$varsStr['numSurvivalRateLimit'] .= '( ' . $this->_getNumberFormat($numValue). ' )';
			} else {
				$varsStr['numSurvivalRateLimit'] = $arr['varsData']['strBlank'];
			}
		}

		$varsStr['numValueRemainingBook'] = $this->_getNumberFormat($varsValueLast['numValueRemainingBook']);
		if ($varsStr['flagRemove']) {
			$tmplRow['flagStatus'] = $arr['varsData']['strRemoveFake'];
		}
		for ($i = 1; $i <= $tmplRow['numTr']; $i++) {
			$tmplRow['strRow'] .= $this->_getVarsHtml(array(
				'varsData' => $arr['varsData'],
				'value'    => $varsStr,
				'tmplStr'  => $varsPrintItem['tmplRow']['tmplTrStatus' . $i],
			));
		}
		$varsPrint['varsDetail'][] = $tmplRow;

		if (preg_match("/^(noneDep)$/", $varsValueLast['value_flagDepMethod'])) {
			return $varsPrint;
		}

		$numValueNetClosing = 0;
		$flagStampEnd = 0;
		$num = 1;
		$array = $arr['varsValue'];
		foreach ($array as $key => $value) {
			if ($num == 1) {
				$tmplRow = $varsPrint['varsDetailTmpl'];
				$tmplRow['id'] = $arr['flagCount'] . '_Row_' . $num;
				$tmplRow['idTmplTable'] = 'tmplTable';
				$tmplRow['idTmplColumn'] = 'tmplColumn';
				$tmplRow['numTr'] = 1;

				$strMemo = $varsValueLast['flagDepUp'];
				if ($varsValueLast['numValueCompression']) {
					$strMemo .= ' ' . $arr['varsData']['strNumValueCompression'];
				}

				$varsStr = array();
				$varsStr['strDate'] = $varsValueLast['stampBuyExt'];
				$varsStr['strMemo'] = $strMemo;
				$varsStr['numValue'] = $this->_getNumberFormat($varsValueLast['numValue']);
				$varsStr['numValueDep'] = 0;
				$varsStr['numValueNetClosing'] = $this->_getNumberFormat($varsValueLast['numValueNet']);
				$tmplRow['strRow'] = $this->_getVarsHtml(array(
					'varsData' => $arr['varsData'],
					'value'    => $varsStr,
					'tmplStr'  => $varsPrintItem['tmplRow']['tmplTr1'],
				));
				$num++;
				$varsPrint['varsDetail'][] = $tmplRow;

				if ($value['numValueAccumulated']) {
					$tmplRow = $varsPrint['varsDetailTmpl'];
					$tmplRow['id'] = $arr['flagCount'] . '_Row_' . $num;
					$tmplRow['idTmplTable'] = 'tmplTable';
					$tmplRow['idTmplColumn'] = 'tmplColumn';
					$tmplRow['numTr'] = 1;
					$varsStr = array();
					$varsStr['strDate'] = $value['stampStartPeriodExt'];
					$varsStr['strMemo'] = $arr['varsData']['strOpeningDep'];
					$varsStr['numValue'] = $arr['varsData']['strBlank'];
					$varsStr['numValueDep'] = $this->_getNumberFormat($value['numValueAccumulated']);
					$varsStr['numValueNetClosing'] = $this->_getNumberFormat($value['numValueNetOpening']);
					$tmplRow['strRow'] = $this->_getVarsHtml(array(
						'varsData' => $arr['varsData'],
						'value'    => $varsStr,
						'tmplStr'  => $varsPrintItem['tmplRow']['tmplTr1'],
					));
					$num++;
					$varsPrint['varsDetail'][] = $tmplRow;
				}
			}

			$varsPeriod = $arr['varsItem']['varsPeriod'][$value['numFiscalPeriod']];
			if ($varsValueLast['value_stampEnd']) {
				if ($varsPeriod['stampStart'] <= $varsValueLast['value_stampEnd']
					&& $varsPeriod['stampEnd'] >= $varsValueLast['value_stampEnd']
				) {
					$flagStampEnd = 1;
					$tmplRow = $varsPrint['varsDetailTmpl'];
					$tmplRow['id'] = $arr['flagCount'] . '_Row_' . $num;
					$tmplRow['idTmplTable'] = 'tmplTable';
					$tmplRow['idTmplColumn'] = 'tmplColumn';
					$tmplRow['numTr'] = 1;
					$varsStr = array();

					$numValueNetClosing = $value['numValueNetClosing'];
					if ($varsValueLast['value_stampDrop']) {
						$numValueNetClosing = $value['numValueNetOpening'] - $value['numValueDep'];
					}

					$varsStr['strDate'] = $value['stampEndExt'];
					$varsStr['strMemo'] = $arr['varsData']['strDep'];
					$varsStr['numValue'] = $arr['varsData']['strBlank'];
					$varsStr['numValueDep'] = $this->_getNumberFormat($value['numValueDep']);
					$varsStr['numValueNetClosing'] = $this->_getNumberFormat($numValueNetClosing);
					$tmplRow['strRow'] = $this->_getVarsHtml(array(
						'varsData' => $arr['varsData'],
						'value'    => $varsStr,
						'tmplStr'  => $varsPrintItem['tmplRow']['tmplTr1'],
					));
					$num++;
					$varsPrint['varsDetail'][] = $tmplRow;
				}
			}

			if ($varsValueLast['value_stampDrop']) {
				if ($varsPeriod['stampStart'] <= $varsValueLast['value_stampDrop']
					&& $varsPeriod['stampEnd'] >= $varsValueLast['value_stampDrop']
				) {
					$flagStampEnd = 1;
					$tmplRow = $varsPrint['varsDetailTmpl'];
					$tmplRow['id'] = $arr['flagCount'] . '_Row_' . $num;
					$tmplRow['idTmplTable'] = 'tmplTable';
					$tmplRow['idTmplColumn'] = 'tmplColumn';
					$tmplRow['numTr'] = 1;
					$varsStr = array();
					$varsStr['strDate'] = $value['stampDropExt'];
					$varsStr['strMemo'] = $varsValueLast['flagDepDown'];
					$varsStr['numValue'] = $arr['varsData']['strBlank'];
					$varsStr['numValueDep'] = $arr['varsData']['strBlank'];
					$varsStr['numValueNetClosing'] = $this->_getNumberFormat($value['numValueNetClosing']);
					$tmplRow['strRow'] = $this->_getVarsHtml(array(
						'varsData' => $arr['varsData'],
						'value'    => $varsStr,
						'tmplStr'  => $varsPrintItem['tmplRow']['tmplTr1'],
					));
					$num++;
					$varsPrint['varsDetail'][] = $tmplRow;
				}
			}

			if ($flagStampEnd) {
				continue;
			}
			$tmplRow = $varsPrint['varsDetailTmpl'];
			$tmplRow['id'] = $arr['flagCount'] . '_Row_' . $num;
			$tmplRow['idTmplTable'] = 'tmplTable';
			$tmplRow['idTmplColumn'] = 'tmplColumn';
			$tmplRow['numTr'] = 1;
			$varsStr = array();
			$varsStr['strDate'] = $value['stampEndPeriodExt'];
			$varsStr['strMemo'] = $arr['varsData']['strDep'];
			$varsStr['numValue'] = $arr['varsData']['strBlank'];
			$varsStr['numValueDep'] = $this->_getNumberFormat($value['numValueDep']);
			$varsStr['numValueNetClosing'] = $this->_getNumberFormat($value['numValueNetClosing']);
			$tmplRow['strRow'] = $this->_getVarsHtml(array(
				'varsData' => $arr['varsData'],
				'value'    => $varsStr,
				'tmplStr'  => $varsPrintItem['tmplRow']['tmplTr1'],
			));
			$num++;
			$varsPrint['varsDetail'][] = $tmplRow;
		}

		$tmplRow = $varsPrint['varsDetailTmpl'];
		$tmplRow['id'] = $arr['flagCount'] . '_' . 'Sum';
		$tmplRow['idTmplTable'] = 'tmplTable';
		$tmplRow['idTmplColumn'] = 'tmplColumn';

		$tmplRow['numTr'] = 1;
		$varsStr = $varsValueLast;
		if ($varsStr['flagRemove']) {
			$tmplRow['flagStatus'] = $arr['varsData']['strRemoveFake'];
		}
		$varsStr['strDate'] = $value['stampDropExt'];
		$varsStr['strMemo'] = $value['flagDepDown'];
		$varsStr['numValue'] = $this->_getNumberFormat($varsValueLast['numValue']);
		$varsStr['numValueAccumulatedClosing'] = $this->_getNumberFormat($varsValueLast['numValueAccumulatedClosing']);
		$varsStr['numValueNetClosing'] = $this->_getNumberFormat($varsValueLast['numValueNetClosing']);

		$tmplRow['strRow'] .= $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'value'    => $varsStr,
			'tmplStr'  => $varsPrintItem['tmplRow']['tmplTrSum1'],
		));

		$varsPrint['varsDetail'][] = $tmplRow;

		return $varsPrint;
	}

	/**
		(array(

		))
	 */
	protected function _getNumberFormat($num)
	{
		if ($num == '') {
			return '';
		}
		$num = number_format($num);

		return $num;
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

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$varsAuthority = $this->_getVarsAuthority(array());

		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllOutput'] || $varsAuthority['flagMyOutput'])) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
			}
			exit;
		}

		$varsFlag = array(
			'flagType' => $varsRequest['query']['jsonValue']['vars']['FlagType'],
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

		$this->checkSearch(array(
			'arrOrder' => $vars['portal']['varsNavi']['search']['varsDetail']['varsSearchSort'],
			'arrWhere' => $vars['portal']['varsNavi']['search']['varsDetail']['varsSearchItem'],
		));

		if (!$varsRequest['query']['jsonSearch']['ph']['arrOrder']) {
			$varsRequest['query']['jsonSearch']['ph']['arrOrder']= array(
				'strColumn' => 'id',
				'flagDesc'  => 1,
			);
		}

		$rows = $this->getSearch(array(
			'idModule'   => 'accounting',
			'strTable'   => 'accountingLogFixedAssets' . $strNation,
			'arrJoin'    => array(),
			'arrOrder'   => $varsRequest['query']['jsonSearch']['ph']['arrOrder'],
			'insCurrent' => $this,
			'arrWhere'   => $varsRequest['query']['jsonSearch']['ph']['arrWhere'],
		));

		$varsItem = $this->_updateVarsItem(array(
			'varsItem' => $varsItem,
			'vars'     => $vars,
		));

		$vars = $this->_updateSearch(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'rows'     => $rows,
		));

		$text = $this->_getCsv(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
			'varsItem' => $varsItem,
		));

		$text = mb_convert_encoding($text, 'sjis', 'utf8');

		$strFileName = $this->_getFileTitle(array(
			'strMenu'     => $vars['varsItem']['varsOutput']['strTitleList'],
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

		$varsData = array();
		$varsData = $this->_getVarsStatus(array(
			'vars'     => $arr['vars'],
			'varsItem' => $arr['varsItem'],
		));

		$arrayCsv = array();
		$array = $this->_getVarsStatusCsv(array(
			'vars' => $varsData,
		));
		foreach ($array as $key => $value) {
			$arrayCsv[] = $value;
		}

		$array = $this->_getVarsLoop(array(
			'vars'     => $arr['vars'],
			'varsItem' => $arr['varsItem'],
			'rows'     => $arr['rows'],
		));
		foreach ($array as $key => $value) {
			$arrayCsv[] = $value;
		}

		$text = $classFile->getCsvText(array(
			'delimiter' => ',',
			'rows'      => $arrayCsv,
		));

		return $text;
	}

	/**
		(array(
			'vars' => $varsData,
		))
	 */
	protected function _getVarsStatusCsv($arr)
	{
		$vars = &$arr['vars'];

		$arrayCsv = array();
		$arrayCsv[] = array($vars['strEntityExt']);
		$arrayCsv[] = array($vars['strNumExt']);
		$arrayCsv[] = array($vars['strPeriodExt']);
		$arrayCsv[] = array($vars['strUnit']);
		$arrayCsv[] = array();

		return $arrayCsv;
	}

	/**
	 *
	 */
	protected function _getVarsLoop($arr)
	{
		global $classTime;
		global $classEscape;

		$vars = &$arr['vars'];
		$varsItem = &$arr['varsItem'];

		$varsData = array();
		$arrayData = array();
		$varsStrTitle = $varsItem['varsStrTitle'];
		$array = $vars['varsItem']['varsTitle'];
		foreach ($array as $key => $value) {
			$varsStrTitle[$key] = $value;
		}

		$rowData = array();
		$rowData[] = $vars['varsItem']['varsOutput']['strStatus'];
		$array = $varsStrTitle;
		foreach ($array as $key => $value) {
			$rowData[] = $value;
		}
		$arrayData[] = $rowData;

		$array = $vars['portal']['varsList']['varsDetail'];
		foreach ($array as $key => $value) {
			$rowData = array();
			$rowData[] = $value['varsColumnDetail']['flagStatus'];
			$arrayId = $varsStrTitle;
			foreach ($arrayId as $keyId => $valueId) {
				$str = '';
				if ($keyId == 'strTitle' || $keyId == 'strMemo') {
					$str = $value['jsonDetail'][$keyId];

				} elseif (preg_match("/^(idAccountTitle)$/", $keyId)) {
					$data = $value['jsonDetail']['jsonDetail'][$keyId];
					if ($data != 'none') {
						if (!$varsItem['arrAccountTitle']['arrStrTitle'][$data]) {
							$str = $vars['varsItem']['strLost'];

						} else {
							$str = $varsItem['arrAccountTitle']['arrStrTitle'][$data]['strTitleFS'];
						}
					}

				} elseif (preg_match("/^(flagDepMethod|flagTaxFixed|flagTaxFixedType|flagDepUp|flagDepDown)$/", $keyId)) {
					$data = $value['jsonDetail']['jsonDetail'][$keyId];
					if ($data != 'none') {
						$str = $varsItem['varsOptions'][$keyId]['arrStrTitle'][$data];
					}

				} elseif (preg_match("/^(numUsefulLife)$/", $keyId)) {
					$data = $value['jsonDetail']['jsonDetail'][$keyId];
					$str = '';
					$flagDepMethod = $value['jsonDetail']['jsonDetail']['flagDepMethod'];
					if ($flagDepMethod == 'straight' || $flagDepMethod == 'declining' || $flagDepMethod == 'average') {
						$str = $data;
					}

				} elseif (preg_match("/^(idDepartment)$/", $keyId)) {
					$data = $value['jsonDetail']['jsonDetail'][$keyId];
					if ($data) {
						if (!$varsItem['arrDepartment']['arrStrTitle'][$data]) {
							$str = $vars['varsItem']['strLost'];

						} else {
							$str = $varsItem['arrDepartment']['arrStrTitle'][$data]['strTitle'];
						}
					}

				} elseif (preg_match("/^stamp/", $keyId)) {
					$data = $value['jsonDetail']['jsonDetail'][$keyId];
					if ($data) {
						$arrDate = $classTime->getLocal(array('stamp' => $data));
						$str = $arrDate['year'] . '/' . $arrDate['month'] . '/' . $arrDate['date'];
					}

				} elseif (preg_match("/^arrCommaDepMonth$/", $keyId)) {
					$numFiscalTermMonth = $varsItem['varsEntityNation']['numFiscalTermMonth'];
					$arrCommaDepMonth = $classEscape->splitCommaArrayData(array('data' => $value['jsonDetail']['jsonDetail'][$keyId]));
					$str = count($arrCommaDepMonth) . '/' . $numFiscalTermMonth;

				} elseif (preg_match("/^id$/", $keyId)) {
					$str = $value['id'];

				} else {
					$str = $value['jsonDetail']['jsonDetail'][$keyId];
				}
				$rowData[] = str_replace(',', $vars['varsItem']['strEscape'], $str);
			}
			$arrayData[] = $rowData;
		}

		return $arrayData;
	}

	/**
		(array(
			'vars'     => $arr['vars'],
			'flagVars' => 0,
		))
	 */
	protected function _getVarsStatus($arr)
	{
		global $varsRequest;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$vars = &$arr['vars'];

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];

		$varsData = $vars['varsItem']['varsOutput'];

		$array = $arr['varsItem']['varsStrTitle'];
		foreach ($array as $key => $value) {
			$varsData[$key] = $value;
		}

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

		/*
		$str = $vars['varsItem']['varsOutput']['strPeriodExt'];
		$strPeriod = str_replace('<%strStartHeisei%>', $varsPeriod['numStartHeisei'], $str);
		$strPeriod = str_replace('<%strEndHeisei%>', $varsPeriod['numEndHeisei'], $strPeriod);
		$strPeriod = str_replace('<%strStartMonth%>', $varsPeriod['numStartMonth'], $strPeriod);
		$strPeriod = str_replace('<%strEndMonth%>', $varsPeriod['numEndMonth'], $strPeriod);
		$varsData['strPeriodExt'] = $strPeriod;

		$str = $arr['vars']['varsItem']['varsOutput']['strPointExt'];
		$strPoint = str_replace('<%strEndHeisei%>', $varsPeriod['numEndHeisei'], $str);
		$strPoint = str_replace('<%strEndMonth%>', $varsPeriod['numEndMonth'], $strPoint);
		$varsData['strPointExt'] = $strPoint;

		*/

		$str = $vars['varsItem']['varsOutput']['strPeriodExt20190401'];
		$strPeriod = str_replace('<%strStartNengoYear%>', $varsPeriod['strStartNengoYear'], $str);
		$strPeriod = str_replace('<%strEndNengoYear%>', $varsPeriod['strEndNengoYear'], $strPeriod);
		$strPeriod = str_replace('<%strStartMonth%>', $varsPeriod['numStartMonth'], $strPeriod);
		$strPeriod = str_replace('<%strEndMonth%>', $varsPeriod['numEndMonth'], $strPeriod);
		$varsData['strPeriodExt'] = $strPeriod;

		$str = $arr['vars']['varsItem']['varsOutput']['strPointExt20190401'];
		$strPoint = str_replace('<%strEndNengoYear%>', $varsPeriod['strEndNengoYear'], $str);
		$strPoint = str_replace('<%strEndMonth%>', $varsPeriod['numEndMonth'], $strPoint);
		$varsData['strPointExt'] = $strPoint;


		return $varsData;
	}

	/**

	 */
	protected function _getVarsIdVarsDetail($arr)
	{
		global $classEscape;

		$data = array();
		$array = $arr['vars'];
		foreach ($array as $key => $value) {
			$id = $classEscape->toLower(array('str' => $value['id']));

			if ($value['id'] == 'JsonDetail') {
				$arrayDetail = $value['varsFormSensitive']['varsTmpl']['varsDetail'];
				foreach ($arrayDetail as $keyDetail => $valueDetail) {
					$id = $classEscape->toLower(array('str' => $arrayDetail[$keyDetail]['id']));
					$data[$id] = $valueDetail;
				}

			} else {
				$data[$id] = $value;
			}
		}

		return $data;
	}

	/**

	 */
	protected function _getCsvVarsDetailStrTitle($arr)
	{
		global $classEscape;

		$data = array();
		$array = $arr['vars'];
		foreach ($array as $key => $value) {
			if (!$value['flagCsvUse']) {
				continue;
			}
			$id = $classEscape->toLower(array('str' => $value['id']));

			if ($value['id'] == 'JsonDetail') {
				$arrayDetail = $value['varsFormSensitive']['varsTmpl']['varsDetail'];
				foreach ($arrayDetail as $keyDetail => $valueDetail) {
					if (!$valueDetail['flagCsvUse']) {
						continue;
					}
					$id = $classEscape->toLower(array('str' => $arrayDetail[$keyDetail]['id']));
					$strTitle = $arrayDetail[$keyDetail]['strTitle'];
					if (preg_match( "/^\((.*?)\)$/", $strTitle, $arrMatch)) {
						list($str, $strTitle) = $arrMatch;
						$data[$id] = $strTitle;
					} else {
						$data[$id] = $strTitle;
					}
				}

			} else {
				$data[$id] = $value['strTitle'];
			}
		}

		return $data;
	}

	/**
	 *
	 */
	protected function _iniDetailOutput()
	{
		global $classRequest;
		global $varsRequest;
		global $varsAccount;

		global $varsPluginAccountingPreference;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$varsAuthority = $this->_getVarsAuthority(array());

		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllOutput'] || $varsAuthority['flagMyOutput'])) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
			}
			exit;
		}

		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];

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

		$rows = $this->_getDetailLogTarget(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idTarget'        => $idTarget,
		));
		if (!$rows['numRows']) {
			$this->_send404Output();
		}

		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllOutput']) && $varsAuthority['flagMyOutput']) {
			if ($rows['arrRows'][0]['idAccount'] != $varsAccount['id']) {
				$this->_send404Output();
			}
		}

		$varsItem = $this->_updateVarsItem(array(
			'varsItem' => $varsItem,
			'vars'     => $vars,
		));

		$vars = $this->_updateSearch(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'rows'     => $rows,
		));
		$text = $this->_getCsvDetail(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));
		$text = mb_convert_encoding($text, 'sjis', 'utf8');

		$strFileName = $this->_getFileTitle(array(
			'strMenu'     => $vars['varsItem']['varsOutput']['strTitleItem'] . '_' . $rows['arrRows'][0]['strTitle'],
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
	protected function _getCsvDetail($arr)
	{
		global $classFile;

		$varsData = array();
		$varsData = $this->_getVarsStatus(array(
			'vars'     => $arr['vars'],
			'varsItem' => $arr['varsItem'],
		));

		$arrayCsv = array();
		$array = $this->_getVarsStatusCsv(array(
			'vars' => $varsData,
		));
		foreach ($array as $key => $value) {
			$arrayCsv[] = $value;
		}

		$array = $this->_getVarsLoopDetail(array(
			'vars'     => $arr['vars'],
			'varsItem' => $arr['varsItem'],
			'rows'     => $arr['rows'],
		));
		foreach ($array as $key => $value) {
			$arrayCsv[] = $value;
		}

		$text = $classFile->getCsvText(array(
			'delimiter' => ',',
			'rows'      => $arrayCsv,
		));

		return $text;
	}

	/**
	 *
	 */
	protected function _getVarsLoopDetail($arr)
	{
		global $classTime;
		global $classEscape;

		$vars = &$arr['vars'];
		$varsItem = &$arr['varsItem'];

		$arrayData = array();
		$varsStrTitle = array();
		$varsStrTitle['numFiscalPeriod'] = $vars['varsItem']['varsOutput']['strNumColumn'];
		$array = $varsItem['varsStrTitle'];

		foreach ($array as $key => $value) {
			$varsStrTitle[$key] = $value;
		}

		$array = $vars['varsItem']['varsTitle'];
		foreach ($array as $key => $value) {
			$varsStrTitle[$key] = $value;
		}

		$rowData = array();
		$rowData[] = $vars['varsItem']['varsOutput']['strStatus'];
		$array = $varsStrTitle;
		foreach ($array as $key => $value) {
			$rowData[] = $value;
		}
		$arrayData[] = $rowData;

		$array = $vars['portal']['varsList']['varsDetail'];
		foreach ($array as $key => $value) {
			$rowData = array();
			$rowData[] = $value['varsColumnDetail']['flagStatus'];
			$arrayId = $varsStrTitle;
			foreach ($arrayId as $keyId => $valueId) {
				$str = '';
				if ($keyId == 'numFiscalPeriod') {
					$strNumRep = $value['vars']['numFiscalPeriod'];
					$str = str_replace('<%replace%>', $strNumRep, $vars['varsItem']['numFiscalPeriod']);

				} elseif ($keyId == 'strTitle' || $keyId == 'strMemo') {
					$str = $value['jsonDetail'][$keyId];

				} elseif (preg_match("/^(idAccountTitle)$/", $keyId)) {
					$data = $value['jsonDetail']['jsonDetail'][$keyId];
					if ($data != 'none') {
						if (!$varsItem['arrAccountTitle']['arrStrTitle'][$data]) {
							$str = $vars['varsItem']['strLost'];

						} else {
							$str = $varsItem['arrAccountTitle']['arrStrTitle'][$data]['strTitleFS'];
						}
					}

				} elseif (preg_match("/^(flagDepMethod|flagTaxFixed|flagTaxFixedType|flagDepUp|flagDepDown)$/", $keyId)) {
					$data = $value['jsonDetail']['jsonDetail'][$keyId];
					if ($data != 'none') {
						$str = $varsItem['varsOptions'][$keyId]['arrStrTitle'][$data];
					}

				} elseif (preg_match("/^(numUsefulLife)$/", $keyId)) {
					$data = $value['jsonDetail']['jsonDetail'][$keyId];
					$str = '';
					$flagDepMethod = $value['jsonDetail']['jsonDetail']['flagDepMethod'];
					if ($flagDepMethod == 'straight' || $flagDepMethod == 'declining' || $flagDepMethod == 'average') {
						$str = $data;
					}

				} elseif (preg_match("/^(idDepartment)$/", $keyId)) {
					$data = $value['jsonDetail']['jsonDetail'][$keyId];
					if ($data) {
						if (!$varsItem['arrDepartment']['arrStrTitle'][$data]) {
							$str = $vars['varsItem']['strLost'];

						} else {
							$str = $varsItem['arrDepartment']['arrStrTitle'][$data]['strTitle'];
						}
					}

				} elseif (preg_match("/^stamp/", $keyId)) {
					$data = $value['jsonDetail']['jsonDetail'][$keyId];
					if ($data) {
						$arrDate = $classTime->getLocal(array('stamp' => $data));
						$str = $arrDate['year'] . '/' . $arrDate['month'] . '/' . $arrDate['date'];
					}

				} elseif (preg_match("/^arrCommaDepMonth$/", $keyId)) {
					$numFiscalTermMonth = $varsItem['varsEntityNation']['numFiscalTermMonth'];
					$arrCommaDepMonth = $classEscape->splitCommaArrayData(array('data' => $value['jsonDetail']['jsonDetail'][$keyId]));
					$str = count($arrCommaDepMonth) . '/' . $numFiscalTermMonth;

				} elseif (preg_match("/^id$/", $keyId)) {
					$str = $value['id'];

				} else {
					$str = $value['jsonDetail']['jsonDetail'][$keyId];
				}
				$rowData[] = str_replace(',', $vars['varsItem']['strEscape'], $str);
			}
			$arrayData[] = $rowData;
		}

		return $arrayData;
	}
}
