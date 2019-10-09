<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_LogOutput extends Code_Else_Plugin_Accounting_Jpn_Log
{
	protected $_childSelf = array(
		'pathVarsPrint'      => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/printLog.php',
		'pathVarsYayoiConvert' => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/yayoiConvert.php',
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
	protected function _iniDetailOutput()
	{
		global $classSmarty;
		global $classEscape;
		global $classRequest;

		global $varsAccount;
		global $varsRequest;

		$varsAuthority = $this->_getVarsAuthority(array());

		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllOutput'] || $varsAuthority['flagMyOutput'])) {
			$this->_send404Output();
		}

		$varsLog = $this->_getVarsLog(array('idTarget' => $varsRequest['query']['jsonValue']['idTarget']));
		if (!$varsLog) {
			$this->_send404Output();
		}

		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllOutput']) && $varsAuthority['flagMyOutput']) {
			if ($varsLog['idAccount'] != $varsAccount['id']) {
				$this->_send404Output();
			}
		}

		$id = $varsRequest['query']['jsonValue']['vars']['idTarget'];
		if (!preg_match( "/,$id,/", $varsLog['arrCommaIdLogFile'])) {
			$this->_send404Output();
		}

		$vars = $this->_getFileLog(array(
			'value' => $varsRequest['query']['jsonValue']['vars']['idTarget'],
		));
		if (!$vars) {
			$this->_send404Output();
		}

		$varsVersion = $this->_getVarsVersion(array(
			'vars'       => $vars,
			'numVersion' => $varsRequest['query']['jsonValue']['vars']['numVersion'],
		));
		$strFileName = $varsVersion['strTitle'] . '.' . $varsVersion['strFileType'];

		if (!file_exists($varsVersion['strUrl'])) {
			$this->_send404Output();
		}

		$classRequest->output(array(
			'path'         => $varsVersion['strUrl'],
			'strFileType'  => $varsVersion['strFileType'],
			'strFileName'  => $strFileName,
		));
	}

	/**
		(array(
			'vars'       => array(),
			'numVersion' => 0,
		))
	 */
	protected function _getVarsVersion($arr)
	{
		$array = $arr['vars']['jsonVersion'];
		$num = 1;
		foreach ($array as $key => $value) {
			if ($arr['numVersion'] == $num) {
				return $value;
			}
			$num++;
		}
		if (FLAG_TEST) {
			var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
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
		global $varsPluginAccountingEntity;

		$varsAuthority = $this->_getVarsAuthority(array());

		if (!($varsAuthority == 'admin' || $varsAuthority['flagMyOutput'] || $varsAuthority['flagAllOutput'])) {
			$this->_sendOldError();
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$vars = $this->_updateVars(array(
			'vars' => $vars,
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
			'strTable'   => 'accountingLog',
			'arrJoin'    => array(),
			'arrOrder'   => $varsRequest['query']['jsonSearch']['ph']['arrOrder'],
			'insCurrent' => $this,
			'arrWhere'   => $varsRequest['query']['jsonSearch']['ph']['arrWhere'],
		));

		$varsPrint = $this->_getVarsPrint(array(
			'vars' => $vars,
			'rows' => $rows,
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
			'vars' => $vars,
			'rows' => $rows,
		))
	 */
	protected function _getVarsPrint($arr)
	{
		$varsData = array();

		$varsData = $this->_getVarsStatus(array(
			'vars' => $arr['vars'],
		));

		$varsData['arrLoop'] = $this->_getVarsLoop(array(
			'vars' => $arr['vars'],
			'rows' => $arr['rows'],
		));

		$varsPrint = $this->_getVarsLoopPrint(array(
			'varsData' => $varsData,
			'vars'     => $arr['vars'],
		));

		return $varsPrint;
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
		*/
		$str = $vars['varsItem']['varsOutput']['strPeriodExt20190401'];
		$strPeriod = str_replace('<%strStartNengoYear%>', $varsPeriod['strStartNengoYear'], $str);
		$strPeriod = str_replace('<%strEndNengoYear%>', $varsPeriod['strEndNengoYear'], $strPeriod);
		$strPeriod = str_replace('<%strStartMonth%>', $varsPeriod['numStartMonth'], $strPeriod);
		$strPeriod = str_replace('<%strEndMonth%>', $varsPeriod['numEndMonth'], $strPeriod);
		$varsData['strPeriodExt'] = $strPeriod;

		return $varsData;
	}

	/**
	 *
	 */
	protected function _getVarsLoop($arr)
	{
		global $classTime;
		global $classEscape;

		$vars = &$arr['vars'];

		$varsEntityNation = $vars['varsRule']['varsEntityNation'];

		$sumDebit = 0;
		$sumCredit = 0;
		$sumInDebit = 0;
		$sumInCredit = 0;
		$sumOutDebit = 0;
		$sumOutCredit = 0;
		$array = $arr['rows']['arrRows'];
		$numAllLog = count($array);
		$numAllLogReport1 = 0;
		$numAllLogReport2 = 0;
		$arrayData = array();
		foreach ($array as $key => $value) {

			$rowData = array();
			$arrDate = $classTime->getLocal(array('stamp' => $value['stampBook']));

			//date
			$rowData['strDate'] = $arrDate['strMonth'] . '/' . $arrDate['strDate'];
			$rowData['strDateYear'] = $arrDate['strYear'] . '/' . $arrDate['strMonth'] . '/' . $arrDate['strDate'];

			//status
			if ((int) $value['flagRemove']) {
				$rowData['strStatus'] = $vars['varsItem']['strRemoveFake'];

			} elseif ((int) $value['flagApply']) {
				if ((int) $value['flagApplyBack']) {
					$rowData['strStatus'] = $vars['varsItem']['strBack'];

				} else {
					$rowData['strStatus'] = $vars['varsItem']['strApply'];
				}

			} else {
				$rowData['strStatus'] = $vars['varsItem']['strDone'];
			}

			//flagFiscalReport
			$rowData['strFiscalReport'] = $vars['varsItem']['varsOutput']['strBlank'];
			if ($value['flagFiscalReport'] === 'f1') {
				$rowData['strFiscalReport'] = $vars['varsItem']['varsOutput']['strFiscalReport1'];
				$numAllLogReport1++;

			} elseif ($value['flagFiscalReport'] === 'f21') {
				$rowData['strFiscalReport'] = $vars['varsItem']['varsOutput']['strFiscalReport2'];
				$numAllLogReport2++;
			}

			//id
			$rowData['strId'] = $value['idLog'];

			//idFile
			$arrCommaIdLogFile = $classEscape->splitCommaArrayData(array('data' => $value['arrCommaIdLogFile']));
			$rowData['strIdFile'] = join($vars['varsItem']['strEscape'], $arrCommaIdLogFile);
			if (!$rowData['strIdFile']) {
				$rowData['strIdFile'] = $vars['varsItem']['varsOutput']['strBlank'];
			}

			//idCharge
			$rowData['strIdCharge'] = $value['idAccount'];

			//memo
			$rowData['strMemo'] = str_replace(',', $vars['varsItem']['strEscape'], $value['strTitle']);
			if (!$value['strTitle']) {
				$rowData['strMemo'] = '';
			}

			//jsonDetail
			$jsonDetail = end($value['jsonVersion']);

			//first row
			$rowData['flagFirst'] = 1;

			$sumDebit +=  $jsonDetail['jsonDetail']['numSumDebit'];
			$sumCredit +=  $jsonDetail['jsonDetail']['numSumCredit'];

			$arraySide = array('Debit', 'Credit');
			$arrayDetail = $jsonDetail['jsonDetail']['varsDetail'];
			foreach ($arrayDetail as $keyDetail => $valueDetail) {
				foreach ($arraySide as $keySide => $valueSide) {
					$rowData['strAccountTitle' . $valueSide] = '';
					$rowData['strValue' . $valueSide] = '';
					$rowData['numValue' . $valueSide] = '';
					$rowData['strNumRateConsumptionTax' . $valueSide] = '';
					$rowData['strSubAccountTitle' . $valueSide] = '';
					$rowData['strConsumptionTax' . $valueSide] = '';
					/*
					 * 20191001 start
					 */
					$rowData['strRateConsumptionTaxReduced' . $valueSide] = '';
					/*
					 * 20191001 end
					 */
					$rowData['strNumValueConsumptionTax' . $valueSide] = '';
					$rowData['numValueConsumptionTax' . $valueSide] = '';
					$rowData['strConsumptionTaxCalc' . $valueSide] = '';
					$rowData['strDepartment' . $valueSide] = '';

					$idAccountTitle = $valueDetail['arr' . $valueSide]['idAccountTitle'];
					$numValue = $valueDetail['arr' . $valueSide]['numValue'];
					$numValueConsumptionTax = $valueDetail['arr' . $valueSide]['numValueConsumptionTax'];
					$numRateConsumptionTax = $valueDetail['arr' . $valueSide]['numRateConsumptionTax'];
					/*
					 * 20191001 start
					 */
					$flagRateConsumptionTaxReduced = $valueDetail['arr' . $valueSide]['flagRateConsumptionTaxReduced'];
					/*
					 * 20191001 end
					 */
					$idDepartment = $valueDetail['arr' . $valueSide]['idDepartment'];
					$idSubAccountTitle = $valueDetail['arr' . $valueSide]['idSubAccountTitle'];
					$flagConsumptionTaxGeneralRuleEach = $valueDetail['arr' . $valueSide]['flagConsumptionTaxGeneralRuleEach'];
					$flagConsumptionTaxGeneralRuleProration = $valueDetail['arr' . $valueSide]['flagConsumptionTaxGeneralRuleProration'];
					$flagConsumptionTaxSimpleRule = $valueDetail['arr' . $valueSide]['flagConsumptionTaxSimpleRule'];
					$flagConsumptionTaxWithoutCalc = $valueDetail['arr' . $valueSide]['flagConsumptionTaxWithoutCalc'];
					$flagConsumptionTaxCalc = $valueDetail['arr' . $valueSide]['flagConsumptionTaxCalc'];
					$flagConsumptionTaxIncluding = $valueDetail['arr' . $valueSide]['flagConsumptionTaxIncluding'];
					$flagConsumptionTaxFree = $valueDetail['arr' . $valueSide]['flagConsumptionTaxFree'];
					$flagTax = 0;
					$flagRate = 0;
					if ($idAccountTitle) {

						//strAccountTitle
						$strAccountTitle = $vars['varsRule']['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['strTitle'];
						$rowData['strAccountTitle' . $valueSide] = str_replace(',', $vars['varsItem']['strEscape'], $strAccountTitle);

						//strValue
						$rowData['strValue' . $valueSide] = number_format($numValue);
						$rowData['numValue' . $valueSide] = $numValue;

						//strSubAccountTitle
						$idSubAccountTitle = $valueDetail['arr' . $valueSide]['idSubAccountTitle'];
						$strSubAccountTitle = $vars['varsRule']['arrSubAccountTitle']['arrStrTitle'][$idAccountTitle][$idSubAccountTitle]['strTitle'];
						if ($strSubAccountTitle) {
							$rowData['strSubAccountTitle' . $valueSide] = str_replace(',', $vars['varsItem']['strEscape'], $strSubAccountTitle);
						}

						if (!$flagConsumptionTaxFree) {


							//strNumValueConsumptionTax
							if ((int) $varsEntityNation['flagConsumptionTaxGeneralRule']) {
								if ((int) $varsEntityNation['flagConsumptionTaxDeducted']) {
									if (preg_match("/^tax/", $flagConsumptionTaxGeneralRuleEach)) {
										$flagTax = 1;
									}
									if (preg_match("/^tax/", $flagConsumptionTaxGeneralRuleEach)
										|| preg_match("/^else/", $flagConsumptionTaxGeneralRuleEach)
									) {
										$flagRate = 1;
									}

								} else {
									if (preg_match("/^tax/", $flagConsumptionTaxGeneralRuleProration)) {
										$flagTax = 1;
									}
									if (preg_match("/^tax/", $flagConsumptionTaxGeneralRuleProration)
										|| preg_match("/^else/", $flagConsumptionTaxGeneralRuleProration)
									) {
										$flagRate = 1;
									}
								}

							} else {
								if (preg_match("/^tax/", $flagConsumptionTaxSimpleRule)) {
									$flagTax = 1;
								}
								if (preg_match("/^tax/", $flagConsumptionTaxSimpleRule)
									|| preg_match("/^else/", $flagConsumptionTaxSimpleRule)
								) {
									$flagRate = 1;
								}
							}

							if ($numValue
								&& $flagTax
								&& $idAccountTitle != 'suspenseReceiptOfConsumptionTaxes'
								&& $idAccountTitle != 'suspensePaymentConsumptionTaxes'
								&& $flagConsumptionTaxWithoutCalc != 3
								&& !$flagConsumptionTaxIncluding
								&& $numValueConsumptionTax != ''
							) {
								if ($flagConsumptionTaxWithoutCalc == 1) {
									$rowData['strNumValueConsumptionTax' . $valueSide] = '( ' . number_format($numValueConsumptionTax);
									$rowData['numValueConsumptionTax' . $valueSide] = '( ' . $numValueConsumptionTax;
									if ($valueSide == 'Debit') {
										$sumInDebit += $numValueConsumptionTax;
									} else {
										$sumInCredit += $numValueConsumptionTax;
									}

								} elseif ($flagConsumptionTaxWithoutCalc == 2) {
									$rowData['strNumValueConsumptionTax' . $valueSide] = number_format($numValueConsumptionTax);
									$rowData['numValueConsumptionTax' . $valueSide] = $numValueConsumptionTax;
									if ($valueSide == 'Debit') {
										$sumOutDebit += $numValueConsumptionTax;
									} else {
										$sumOutCredit += $numValueConsumptionTax;
									}
								}
							}


							//strConsumptionTax
							if ((int) $varsEntityNation['flagConsumptionTaxGeneralRule']) {
								if ((int) $varsEntityNation['flagConsumptionTaxDeducted']) {
									if ($flagConsumptionTaxGeneralRuleEach
										&& $flagConsumptionTaxGeneralRuleEach != 'none'
									) {
										$strConsumptionTax = $vars['varsRule']['varsConsumptionTax']['arrStrGeneralEach'][$flagConsumptionTaxGeneralRuleEach];
										$rowData['strConsumptionTax' . $valueSide] = $strConsumptionTax;
									}

								} else {
									if ($flagConsumptionTaxGeneralRuleProration
										&& $flagConsumptionTaxGeneralRuleProration != 'none'
									) {
										$strConsumptionTax = $vars['varsRule']['varsConsumptionTax']['arrStrGeneralProration'][$flagConsumptionTaxGeneralRuleProration];
										$rowData['strConsumptionTax' . $valueSide] = $strConsumptionTax;
									}
								}

							} else {
								if ($flagConsumptionTaxSimpleRule
									&& $flagConsumptionTaxSimpleRule != 'none'
								) {
									$strConsumptionTax = $vars['varsRule']['varsConsumptionTax']['arrStrSimple'][$flagConsumptionTaxSimpleRule];
									$rowData['strConsumptionTax' . $valueSide] = $strConsumptionTax;
								}
							}

							//strConsumptionTaxCalc
							if ($flagTax
								&& $idAccountTitle != 'suspenseReceiptOfConsumptionTaxes'
								&& $idAccountTitle != 'suspensePaymentConsumptionTaxes'
								&& !$flagConsumptionTaxIncluding
							) {
								if (!$flagConsumptionTaxWithoutCalc) {
									$flagConsumptionTaxWithoutCalc = (int) $varsEntityNation['flagConsumptionTaxWithoutCalc'];
								}
								$rowData['strConsumptionTaxCalc' . $valueSide] = $vars['varsRule']['varsConsumptionTax']['arrStrWithoutCalc'][$flagConsumptionTaxWithoutCalc];
							}
							if ($flagRate) {
								$rowData['strNumRateConsumptionTax' . $valueSide] = $numRateConsumptionTax . '%';

								/*
								 * 20191001 start
								 */
								if ($flagRateConsumptionTaxReduced) {
								    $rowData['strNumRateConsumptionTax' . $valueSide] .= $vars['varsItem']['varsOutput']['strRateConsumptionTaxReduced'];
								}
								/*
								 * 20191001 end
								 */
							}
						}
						//strDepartment
						$strDepartment = $vars['varsRule']['arrDepartment']['arrStrTitle'][$idDepartment]['strTitle'];
						if ($strDepartment) {
							$rowData['strDepartment' . $valueSide] = str_replace(',', $vars['varsItem']['strEscape'], $strDepartment);
						}

					}
				}
				$arrayData[] = $rowData;
				$rowData['flagFirst'] = 0;
				$rowData['strDate'] = '';
				$rowData['strFiscalReport'] = '';
				$rowData['strStatus'] = $vars['varsItem']['varsOutput']['strBlank'];
				$rowData['strId'] = $vars['varsItem']['varsOutput']['strBlank'];
				$rowData['strIdFile'] = $vars['varsItem']['varsOutput']['strBlank'];
				$rowData['strIdCharge'] = $vars['varsItem']['varsOutput']['strBlank'];
				$rowData['strMemo'] = '';
			}
		}

		$rowData = array();
		$rowData['flagSum'] = 1;
		$rowData['sumDebit'] = $sumDebit;
		$rowData['sumCredit'] = $sumCredit;
		$rowData['sumInDebit'] = $sumInDebit;
		$rowData['sumInCredit'] = $sumInCredit;
		$rowData['sumOutDebit'] = $sumOutDebit;
		$rowData['sumOutCredit'] = $sumOutCredit;

		$rowData['strSumDebit'] = number_format($sumDebit);
		$rowData['strSumCredit'] = number_format($sumCredit);
		$rowData['strSumInDebit'] = number_format($sumInDebit);
		$rowData['strSumInCredit'] = number_format($sumInCredit);
		$rowData['strSumOutDebit'] = number_format($sumOutDebit);
		$rowData['strSumOutCredit'] = number_format($sumOutCredit);

		$rowData['numAllLog'] = $numAllLog;
		$rowData['numAllLogReport1'] = $numAllLogReport1;
		$rowData['numAllLogReport2'] = $numAllLogReport2;

		$rowData['strNumAllLog'] = number_format($numAllLog);
		$rowData['strNumAllLogReport1'] = number_format($numAllLogReport1);
		$rowData['strNumAllLogReport2'] = number_format($numAllLogReport2);


		$arrayData[] = $rowData;

		return $arrayData;
	}

	/**
		(array(
			'vars' => $varsLoop,
		))
	 */
	protected function _getVarsLoopPrint($arr)
	{
		$varsPrint = $arr['vars']['varsPrint'];
		$varsPrintItem = $this->_getVarsPrintItem();

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

		$varsPrint['varsStatus']['varsTmpl']['tmplPage'] = $varsPrintItem['tmplPage'];

		$varsPrint['varsStatus']['strTitle'] = $this->_getFileTitle(array(
			'strMenu' => $arr['vars']['varsItem']['varsOutput']['strTitle'],
		));

		$arrayDetail = array();

		$array = $arr['varsData']['arrLoop'];
		$num = 0;
		foreach ($array as $key => $value) {
			$tmplRow = $varsPrint['varsDetailTmpl'];
			$tmplRow['id'] = $num;
			$num++;
			$strFirst = '';
			if ($value['flagFirst']) {
				$strFirst = $varsPrintItem['tmplRow']['tmplTrTop'];
			}
			if ($value['flagSum']) {
				$tmplRow['numTr'] = 3;
				$tmplRow['strRow'] = $strFirst . $this->_getVarsHtml(array(
					'varsData' => $arr['varsData'],
					'value'    => $value,
					'tmplStr'  => $varsPrintItem['tmplRow']['tmplTrSum1'],
				));
				$tmplRow['strRow'] .= $this->_getVarsHtml(array(
					'varsData' => $arr['varsData'],
					'value'    => $value,
					'tmplStr'  => $varsPrintItem['tmplRow']['tmplTrSum2'],
				));
				$tmplRow['strRow'] .= $this->_getVarsHtml(array(
					'varsData' => $arr['varsData'],
					'value'    => $value,
					'tmplStr'  => $varsPrintItem['tmplRow']['tmplTrSum3'],
				));
				$arrayDetail[] = $tmplRow;

			} else {
				$tmplRow['numTr'] = 4;
				$tmplRow['strRow'] = $strFirst . $this->_getVarsHtml(array(
					'varsData' => $arr['varsData'],
					'value'    => $value,
					'tmplStr'  => $varsPrintItem['tmplRow']['tmplTr1'],
				));
				$tmplRow['strRow'] .= $this->_getVarsHtml(array(
					'varsData' => $arr['varsData'],
					'value'    => $value,
					'tmplStr'  => $varsPrintItem['tmplRow']['tmplTr2'],
				));
				$tmplRow['strRow'] .= $this->_getVarsHtml(array(
					'varsData' => $arr['varsData'],
					'value'    => $value,
					'tmplStr'  => $varsPrintItem['tmplRow']['tmplTr3'],
				));
				$tmplRow['strRow'] .= $this->_getVarsHtml(array(
					'varsData' => $arr['varsData'],
					'value'    => $value,
					'tmplStr'  => $varsPrintItem['tmplRow']['tmplTr4'],
				));
				$arrayDetail[] = $tmplRow;
			}
		}
		$varsPrint['varsDetail'] = $arrayDetail;

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
	 *
	 */
	protected function _iniListOutput()
	{
		global $classRequest;

		global $varsRequest;
		global $varsPluginAccountingPreference;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$varsAuthority = $this->_getVarsAuthority(array());

		if (!($varsAuthority == 'admin' || $varsAuthority['flagMyOutput'] || $varsAuthority['flagAllOutput'])) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__);
			}
			exit;
		}

		$varsFlag = array(
			'flagType' => $varsRequest['query']['flagType'],
		);

		if ($varsFlag['flagType'] == 'listAll') {
			$this->_iniListOutputListAll();

		} elseif ($varsFlag['flagType'] == 'listAllYayoi') {
			$this->_iniListOutputListAllYayoi();
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$vars = $this->_updateVars(array(
			'vars' => $vars,
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
			'strTable'   => 'accountingLog',
			'arrJoin'    => array(),
			'arrOrder'   => $varsRequest['query']['jsonSearch']['ph']['arrOrder'],
			'insCurrent' => $this,
			'arrWhere'   => $varsRequest['query']['jsonSearch']['ph']['arrWhere'],
		));

		$text = $this->_getCsv(array(
			'vars' => $vars,
			'rows' => $rows,
		));

		$strFileName = $this->_getFileTitle(array(
			'strMenu'     => $vars['varsItem']['varsOutput']['strTitle'],
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
			'vars' => $vars,
			'rows' => $rows,
		))
	 */
	protected function _getCsv($arr)
	{
		global $classFile;

		$varsData = array();
		$varsData = $this->_getVarsStatus(array(
			'vars' => $arr['vars'],
		));

		$varsData['arrLoop'] = $this->_getVarsLoop(array(
			'vars' => $arr['vars'],
			'rows' => $arr['rows'],
		));

		$arrayCsv = array();
		$array = $this->_getVarsStatusCsv(array(
			'vars' => $varsData,
		));
		foreach ($array as $key => $value) {
			$arrayCsv[] = $value;
		}
		$array = $this->_getVarsLoopCsv(array(
			'vars' => $varsData,
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

		return $arrayCsv;
	}

	/**
		(array(
			'vars' => $varsLoop,
		))
	 */
	protected function _getVarsLoopCsv($arr)
	{
		$vars = &$arr['vars'];

		$arrayCsv = array();
		$rowData = array();
		$rowData[] = $vars['strDate'];
		$rowData[] = $vars['strStatus'];
		$rowData[] = $vars['strFiscalReport'];
		$rowData[] = $vars['strDebit'] . $vars['strAccountTitle'];
		$rowData[] = $vars['strDebit'] . $vars['strSubAccountTitle'];
		$rowData[] = $vars['strDebit'] . $vars['strDepartment'];
		$rowData[] = $vars['strDebit'] . $vars['strConsumptionTax'];
		$rowData[] = $vars['strDebit'] . $vars['strRateConsumptionTax'];
		$rowData[] = $vars['strDebit'] . $vars['strConsumptionTaxCalc'];
		$rowData[] = $vars['strDebit'] . $vars['strValue'];
		$rowData[] = $vars['strDebit'] . $vars['strNumValueConsumptionTax'];

		$rowData[] = $vars['strCredit'] . $vars['strAccountTitle'];
		$rowData[] = $vars['strCredit'] . $vars['strSubAccountTitle'];
		$rowData[] = $vars['strCredit'] . $vars['strDepartment'];
		$rowData[] = $vars['strCredit'] . $vars['strConsumptionTax'];
		$rowData[] = $vars['strCredit'] . $vars['strRateConsumptionTax'];
		$rowData[] = $vars['strCredit'] . $vars['strConsumptionTaxCalc'];
		$rowData[] = $vars['strCredit'] . $vars['strValue'];
		$rowData[] = $vars['strCredit'] . $vars['strNumValueConsumptionTax'];

		$rowData[] = $vars['strMemo'];
		$rowData[] = $vars['strId'];
		$rowData[] = $vars['strIdCharge'];
		$rowData[] = $vars['strIdFile'];
		$arrayCsv[] = $rowData;

		$array = $vars['arrLoop'];
		foreach ($array as $key => $value) {
			if ($value['flagSum']) {
				$rowData = array();
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = $value['sumDebit'];
				$rowData[] = $value['numValueConsumptionTaxDebit'];

				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = $value['sumCredit'];
				$rowData[] = $value['numValueConsumptionTaxCredit'];

				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = '';
				$arrayCsv[] = $rowData;

				$arrayCsv[] = array();
				$rowData = array();
				$rowData[] = '';
				$rowData[] = $vars['strNumAllLog'];
				$rowData[] = $value['numAllLog'];
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = $vars['strDebit'] . $vars['strSumIn'];
				$rowData[] = $value['sumInDebit'];
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = $vars['strCredit'] . $vars['strSumIn'];
				$rowData[] = $value['sumInCredit'];

				$arrayCsv[] = $rowData;
				$rowData = array();
				$rowData[] = '';
				$rowData[] = $vars['strNumAllLogReport1'];
				$rowData[] = $value['numAllLogReport1'];
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = $vars['strDebit'] . $vars['strSumOut'];
				$rowData[] = $value['sumOutDebit'];
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = '';
				$rowData[] = $vars['strCredit'] . $vars['strSumOut'];
				$rowData[] = $value['sumOutCredit'];
				$arrayCsv[] = $rowData;

				$rowData = array();
				$rowData[] = '';
				$rowData[] = $vars['strNumAllLogReport2'];
				$rowData[] = $value['numAllLogReport2'];
				$arrayCsv[] = $rowData;

			} else {
				$rowData = array();
				$rowData[] = $value['strDateYear'];
				$rowData[] = $value['strStatus'];
				$rowData[] = $value['strFiscalReport'];
				$rowData[] = $value['strAccountTitleDebit'];
				$rowData[] = $value['strSubAccountTitleDebit'];
				$rowData[] = $value['strDepartmentDebit'];
				$rowData[] = $value['strConsumptionTaxDebit'];
				$rowData[] = $value['strNumRateConsumptionTaxDebit'];
				$rowData[] = $value['strConsumptionTaxCalcDebit'];
				$rowData[] = $value['numValueDebit'];
				$rowData[] = $value['numValueConsumptionTaxDebit'];

				$rowData[] = $value['strAccountTitleCredit'];
				$rowData[] = $value['strSubAccountTitleCredit'];
				$rowData[] = $value['strDepartmentCredit'];
				$rowData[] = $value['strConsumptionTaxCredit'];
				$rowData[] = $value['strNumRateConsumptionTaxCredit'];
				$rowData[] = $value['strConsumptionTaxCalcCredit'];
				$rowData[] = $value['numValueCredit'];
				$rowData[] = $value['numValueConsumptionTaxCredit'];

				$rowData[] = $value['strMemo'];
				$rowData[] = $value['strId'];
				$rowData[] = $value['strIdCharge'];
				$rowData[] = $value['strIdFile'];
				$arrayCsv[] = $rowData;
			}
		}
		return $arrayCsv;
	}

	/**
	 *
	 */
	protected function _iniListOutputListAllYayoi()
	{
		global $classDb;
		global $classRequest;

		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;
		global $varsRequest;

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagMyOutput'] || $varsAuthority['flagAllOutput'])) {
			$this->_send404Output();
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsOutputItem(array(
			'vars' => $vars,
		));

		$vars = $this->_updateVars(array(
			'vars' => $vars,
		));

		$rows = $this->_getLog();

		$text = $this->_getCsvListAllYayoi(array(
			'vars'     => $vars,
			'rows'     => $rows,
			'varsItem' => $varsItem,
		));
		$text = mb_convert_encoding($text, 'sjis', 'utf8');

		$strFileName = $this->_getFileTitle(array(
			'strMenu'     => $vars['varsItem']['varsOutput']['strTitleListYayoi'],
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
			'vars' => $vars,
		))
	 */
	protected function _getVarsOutputItem($arr)
	{
		global $varsPluginAccountingAccount;

		$varsTaxConvert = $this->getVars(array(
			'path' => $this->_childSelf['pathVarsYayoiConvert'],
		));

		$arrayNew = array();
		$array = $varsTaxConvert;
		foreach ($array as $key => $value) {
			if (preg_match("/^vars/", $key)) {
				continue;
			}
			$arrayNew[$key] = $value;
			$temp = array();
			$arrayData = $value;
			foreach ($arrayData as $keyData => $valueData) {
				$temp[$valueData['strTitle']] = $valueData;
			}
			$arrayNew[$key . 'StrTitle'] = $temp;
		}

		$data = array(
			'varsTaxConvert' => $arrayNew,
		);

		return $data;
	}

	/**
		array(
			'vars' => $vars,
			'rows' => $rows,
		)
	 */
	protected function _getCsvListAllYayoi($arr)
	{
		global $classTime;
		global $classFile;

		global $varsRequest;
		global $varsAccounts;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingAccountsId;

		$vars = &$arr['vars'];
		$arrayCsv = array();

		if (!(int) $arr['rows']['numRows']) {
			return ' ';
		}

		$array = $arr['rows']['arrRows'];
		$flag = 0;
		foreach ($array as $key => $value) {
			$varsVersion = end($value['jsonVersion']);

			$dataDetail = $this->_getCsvJsonDetail(array(
				'vars'      => $vars,
				'arrDetail' => $varsVersion['jsonDetail']['varsDetail'],
			));

			$num = 0;
			$numAll = count($dataDetail);
			$arrayDetail = $dataDetail;
			foreach ($arrayDetail as $keyDetail => $valueDetail) {
				$num++;
				$rowCsv = array();

				//1 識別フラグ
				$flags = 2000;
				if ($numAll > 1) {
					if ($num == 1) {
						$flags = 2110;

					} elseif ($num == $numAll) {
						$flags = 2101;

					} else {
						$flags = 2100;
					}
				}
				$rowCsv[] = $flags;

				//2 伝票no
				$no = $value['idLog'];
				$rowCsv[] = $no;

				//3 決算
				$fiscalPeriod = '';
				if (preg_match("/^f1$/", $value['flagFiscalReport'])) {
					$fiscalPeriod = $vars['varsItem']['varsOutput']['strFiscalReportYayoi1'];

				} elseif (preg_match("/^f21$/", $value['flagFiscalReport'])) {
					$fiscalPeriod = $vars['varsItem']['varsOutput']['strFiscalReport2'];
				}
				$rowCsv[] = $fiscalPeriod;


				//4 取引日付
				$stampBook = '';
				$stampBook = $classTime->getDisplay(array(
					'flagType' => 'year/date',
					'stamp'    => $value['stampBook'],
				));
				$rowCsv[] = $stampBook;

				$arrayStr = array('Debit', 'Credit');
				foreach ($arrayStr as $keyStr => $valueStr) {

					//5 11 勘定科目
					$strAccountTitle = str_replace(',', $vars['varsItem']['strEscape'], $valueDetail['idAccountTitle' . $valueStr]);
					$strAccountTitle = mb_substr($strAccountTitle, 0, 24);
					$rowCsv[] = $strAccountTitle;


					//6 12 補助科目
					$strSubAccountTitle = str_replace(',', $vars['varsItem']['strEscape'], $valueDetail['idSubAccountTitle' . $valueStr]);
					$strSubAccountTitle = mb_substr($strSubAccountTitle, 0, 24);
					$rowCsv[] = $strSubAccountTitle;


					//7 13 部門
					$strDepartment = str_replace(',', $vars['varsItem']['strEscape'], $valueDetail['idDepartment' . $valueStr]);
					$strDepartment = mb_substr($strDepartment, 0, 24);
					$rowCsv[] = $strDepartment;


					//8 14 税区分
					$flagConsumptionTax = $this->_getCsvListAllYayoiTaxConsumptionTax(array(
						'varsItem'         => $arr['varsItem'],
						'varsEntityNation' => $vars['varsRule']['varsEntityNation'],
						'valueDetail'      => $valueDetail,
						'valueStr'         => $valueStr,
						'varsStr'          => $vars['varsItem']['varsOutput'],
					));
					$rowCsv[] = $flagConsumptionTax;


					//9 15 金額
					$numValue = $valueDetail['numValue' . $valueStr];
					$rowCsv[] = $numValue;


					//10 16 税金額
					$numValueConsumptionTax = $valueDetail['numValueConsumptionTax' . $valueStr];
					$rowCsv[] = $numValueConsumptionTax;
				}

				//17 摘要
				$strTitle = str_replace(',', $vars['varsItem']['strEscape'], $value['strTitle']);
				$strTitle = mb_substr($strTitle, 0, 64);
				$rowCsv[] = $strTitle;

				//18 番号
				$rowCsv[] = '';

				//19 期日
				$rowCsv[] = '';

				//20 タイプ
				$flagType = 0;
				if ($numAll > 1) {
					$flagType = 3;
				}
				$rowCsv[] = $flagType;

				//21 生成元
				$rowCsv[] = '';

				//22 仕訳メモ
				$rowCsv[] = '';

				//23 付箋1
				$rowCsv[] = '0';

				//24 付箋2
				$rowCsv[] = '0';

				//25 調整
				$rowCsv[] = 'no';

				$arrayCsv[] = $rowCsv;

			}
		}

		$text = $classFile->getCsvText(array(
			'delimiter' => ',',
			'rows'      => $arrayCsv,
		));

		return $text;
	}

	/**
		array(
			'varsItem'         => $arr['varsItem'],
			'varsEntityNation' => $vars['varsRule']['varsEntityNation'],
			'valueDetail'      => $valueDetail,
			'valueStr'         => $valueStr,
		)
	 */
	protected function _getCsvListAllYayoiTaxConsumptionTax($arr)
	{
		$flagConsumptionTaxFree = (int) $arr['varsEntityNation']['flagConsumptionTaxFree'];
		$flagConsumptionTaxIncluding = (int) $arr['varsEntityNation']['flagConsumptionTaxIncluding'];
		$flagConsumptionTaxGeneralRule = (int) $arr['varsEntityNation']['flagConsumptionTaxGeneralRule'];
		$flagConsumptionTaxDeducted = (int) $arr['varsEntityNation']['flagConsumptionTaxDeducted'];

		if ($flagConsumptionTaxFree) {
			return $arr['varsStr']['strNone'];
		}

		$flagConsumptionTax = $arr['valueDetail']['flagConsumptionTax' . $arr['valueStr']];
		if (!$flagConsumptionTax) {
			return $arr['varsItem']['varsTaxConvert']['freeStrTitle']['none']['strYayoi'];
		}

		$varsStrTitle = $arr['varsItem']['varsTaxConvert']['simpleStrTitle'];
		if ($flagConsumptionTaxGeneralRule) {
			if ($flagConsumptionTaxDeducted) {
				$varsStrTitle = $arr['varsItem']['varsTaxConvert']['generalEachStrTitle'];
			} else {
				$varsStrTitle = $arr['varsItem']['varsTaxConvert']['generalProrationStrTitle'];
			}
		}

		$varsTax = $varsStrTitle[$flagConsumptionTax];
		$flagConsumptionTax = $varsStrTitle[$flagConsumptionTax]['strYayoi'];
		if ($flagConsumptionTaxIncluding) {
			//込
			$flagConsumptionTax = str_replace('<>', $arr['varsStr']['strIncluding'], $flagConsumptionTax);

		} else {
			$flagConsumptionTaxWithoutCalc = mb_substr($arr['valueDetail']['flagConsumptionTaxWithoutCalc' . $arr['valueStr']], 0, 1);
			//内税 外税 別記
			if ($flagConsumptionTaxWithoutCalc) {
				$flagConsumptionTax= str_replace('<>', $flagConsumptionTaxWithoutCalc, $flagConsumptionTax);
			}
		}

		if (preg_match( "/^tax/", $varsTax['value'])) {
			$numRate = $arr['valueDetail']['numRateConsumptionTax' . $arr['valueStr']];
			if ($numRate) {
				$numRate .= '%';
				if ($arr['valueDetail']['flagRateConsumptionTaxReduced' . $arr['valueStr']]) {
				    $numRate = $arr['varsStr']['strRateConsumptionTaxReduced2'] . $numRate;
				}
				$flagConsumptionTax= str_replace('[]', $numRate, $flagConsumptionTax);
			}

		} elseif (preg_match( "/^else/", $varsTax['value'])) {
			$numRate = $arr['valueDetail']['numRateConsumptionTax' . $arr['valueStr']];
			if ($numRate) {
				$str = '';
				if (preg_match( "/^else-TaxLocal$/", $varsTax['value'])) {
					if ($numRate == 10) {
						$str = '2.2%';
					} elseif ($numRate == 8) {
					    /*
					     * 20191001 start
					     */
					    if ($arr['valueDetail']['flagRateConsumptionTaxReduced' . $arr['valueStr']]) {
					        $str = '1.76%';
					    } else {
				        /*
				         * 20191001 end
				         */
					        $str = '1.7%';
					    }

					} elseif ($numRate == 5) {
						$str = '1%';
					}
				} else {
					if ($numRate == 10) {
						$str = '7.8%';
					} elseif ($numRate == 8) {
					    /*
					     * 20191001 start
					     */
					    if ($arr['valueDetail']['flagRateConsumptionTaxReduced' . $arr['valueStr']]) {
					        $str = '6.24%';
					    } else {
					        /*
					         * 20191001 start
					         */

					        $str = '6.3%';
					    }
					} elseif ($numRate == 5) {
						$str = '4%';
					}
				}
				$numRate = $str;
				$flagConsumptionTax= str_replace('[]', $numRate, $flagConsumptionTax);
			}

		}

		return $flagConsumptionTax;
	}

	/**
	 *
	 */
	protected function _iniListOutputListAll()
	{
		global $classDb;
		global $classRequest;

		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;
		global $varsRequest;

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagMyOutput'] || $varsAuthority['flagAllOutput'])) {
			$this->_send404Output();
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$vars = $this->_updateVars(array(
			'vars' => $vars,
		));

		$rows = $this->_getLog();

		$text = $this->_getCsvListAll(array(
			'vars' => $vars,
			'rows' => $rows,
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

	 */
	protected function _getLog()
	{
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingAccountsEntity;
		global $varsAccount;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];

		$arrWhere = array(
			array(
				'flagType'      => 'num',
				'strColumn'     => 'idEntity',
				'flagCondition' => 'eq',
				'value'         => $idEntity,
			),
			array(
				'flagType'      => 'num',
				'strColumn'     => 'numFiscalPeriod',
				'flagCondition' => 'eq',
				'value'         => $numFiscalPeriod,
			),
			array(
				'flagType'      => 'num',
				'strColumn'     => 'flagRemove',
				'flagCondition' => 'eq',
				'value'         => 0,
			),
			array(
				'flagType'      => 'num',
				'strColumn'     => 'flagApply',
				'flagCondition' => 'eq',
				'value'         => 0,
			),
		);

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllOutput']) && $varsAuthority['flagMyOutput']) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idAccount',
				'flagCondition' => 'eq',
				'value'         => $varsAccount['id'],
			);
		}

		$rows = $this->getSearch(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingLog',
			'arrOrder'  => array(
				'strColumn' => 'idLog',
				'flagDesc'  => 1,
			),
			'flagAnd'  => 1,
			'arrWhere' => $arrWhere,
		));

		return $rows;
	}

	/**
		array(
			'vars'      => $vars,
			'arrDetail' => $value['jsonDetail']['varsDetail'],
		)
	 */
	protected function _getCsvJsonDetail($arr)
	{
		$vars = &$arr['vars'];
		$flagConsumptionTaxFree = (int) $vars['varsRule']['varsEntityNation']['flagConsumptionTaxFree'];
		$flagConsumptionTaxIncluding = (int) $vars['varsRule']['varsEntityNation']['flagConsumptionTaxIncluding'];
		$flagConsumptionTaxGeneralRule = (int) $vars['varsRule']['varsEntityNation']['flagConsumptionTaxGeneralRule'];
		$flagConsumptionTaxDeducted = (int) $vars['varsRule']['varsEntityNation']['flagConsumptionTaxDeducted'];
		$flagConsumptionTaxCalc = (int) $vars['varsRule']['varsEntityNation']['flagConsumptionTaxCalc'];

		$arrDebit = array();
		$arrCredit = array();
		$arrDetail = array();
		$array = $arr['arrDetail'];

		foreach ($array as $key => $value) {
			if ($value['arrDebit']['idAccountTitle'] != '') {
				$arrDebit[] = $value['arrDebit'];
			}
			if ($value['arrCredit']['idAccountTitle'] != '') {
				$arrCredit[] = $value['arrCredit'];
			}
		}
		foreach ($array as $key => $value) {
			$varsDetail = $vars['varsItem']['varsDetail'];
			if ($arrDebit[$key]) {
				$varsDetail['arrDebit'] = $arrDebit[$key];
			}
			if ($arrCredit[$key]) {
				$varsDetail['arrCredit'] = $arrCredit[$key];
			}
			if ($varsDetail['arrDebit']['idAccountTitle'] == ''
				&& $varsDetail['arrCredit']['idAccountTitle'] == ''
			) {
				continue;
			}
			$arrDetail[] = $varsDetail;
		}

		$arrayNew = array();
		$array = $arrDetail;
		foreach ($array as $key => $value) {
			$data = array();
			$arrayStr = array('Debit', 'Credit');
			foreach ($arrayStr as $keyStr => $valueStr) {
				$str = 'arr' . $valueStr;
				$idAccountTitle = $value[$str]['idAccountTitle'];
				if ($idAccountTitle != '') {

					$data['idAccountTitle' . $valueStr] = $vars['varsRule']['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['strTitleFS'];
					$data['flagFS' . $valueStr] = $vars['varsRule']['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['flagFS'];

					$data['numValue' . $valueStr] = $value[$str]['numValue'];

					$data['idSubAccountTitle' . $valueStr] = '';
					if ($value[$str]['idSubAccountTitle'] != '') {
						$data['idSubAccountTitle' . $valueStr] = $vars['varsRule']['arrSubAccountTitle']['arrStrTitle'][$idAccountTitle][$value[$str]['idSubAccountTitle']]['strTitle'];
					}

					$data['idDepartment' . $valueStr] = '';
					if ($value[$str]['idDepartment'] != '') {
						$data['idDepartment' . $valueStr] = $vars['varsRule']['arrDepartment']['arrStrTitle'][$value[$str]['idDepartment']]['strTitle'];
					}

					$data['numValueConsumptionTax' . $valueStr] = 0;
					/*
					 * 20191001 start
					 */
					$data['flagRateConsumptionTaxReduced' . $valueStr] = 0;
					/*
					 * 20191001 end
					 */
					$data['numRateConsumptionTax' . $valueStr] = '';
					$data['flagConsumptionTax' . $valueStr] = '';
					$data['flagConsumptionTaxWithoutCalc' . $valueStr] = '';
					$flagTax = 0;
					$flagRate = 0;
					if (!$flagConsumptionTaxFree) {
						if ($flagConsumptionTaxGeneralRule) {
							if ($flagConsumptionTaxDeducted) {
								$data['flagConsumptionTax' . $valueStr] = $vars['varsRule']['varsConsumptionTax']['arrStrGeneralEach'][$value[$str]['flagConsumptionTaxGeneralRuleEach']];
								if (preg_match("/^tax/", $value[$str]['flagConsumptionTaxGeneralRuleEach'])
									|| preg_match("/^else/", $value[$str]['flagConsumptionTaxGeneralRuleEach'])
								) {
									$flagRate = 1;
								}
							} else {
								$data['flagConsumptionTax' . $valueStr] = $vars['varsRule']['varsConsumptionTax']['arrStrGeneralProration'][$value[$str]['flagConsumptionTaxGeneralRuleProration']];
								if (preg_match("/^tax/", $value[$str]['flagConsumptionTaxGeneralRuleProration'])
									|| preg_match("/^else/", $value[$str]['flagConsumptionTaxGeneralRuleProration'])
								) {
									$flagRate = 1;
								}
							}

						} else {
							$data['flagConsumptionTax' . $valueStr] = $vars['varsRule']['varsConsumptionTax']['arrStrSimple'][$value[$str]['flagConsumptionTaxSimpleRule']];
							if (preg_match("/^tax/", $value[$str]['flagConsumptionTaxSimpleRule'])
								|| preg_match("/^else/", $value[$str]['flagConsumptionTaxSimpleRule'])
							) {
								$flagRate = 1;
							}
						}


						if (!$flagConsumptionTaxIncluding) {
							if ($flagConsumptionTaxGeneralRule) {
								if ($flagConsumptionTaxDeducted) {
									if (preg_match( "/^tax/", $value[$str]['flagConsumptionTaxGeneralRuleEach'])) {
										$flagTax = 1;
									}

								} else {
									if (preg_match( "/^tax/", $value[$str]['flagConsumptionTaxGeneralRuleProration'])) {
										$flagTax = 1;
									}
								}

							} else {
								if (preg_match( "/^tax/", $value[$str]['flagConsumptionTaxSimpleRule'])) {
									$flagTax = 1;
								}
							}
							if ($flagTax) {
								$data['flagConsumptionTaxWithoutCalc' . $valueStr] = $vars['varsRule']['varsConsumptionTax']['arrStrWithoutCalc'][$value[$str]['flagConsumptionTaxWithoutCalc']];
							}
							if ($idAccountTitle == 'suspenseReceiptOfConsumptionTaxes' || $idAccountTitle == 'suspensePaymentConsumptionTaxes') {
								$flagTax = 0;
								$data['flagConsumptionTaxWithoutCalc' . $valueStr] = '';
							}
						}
					}

					if ($flagTax) {
						$numValue =  $value[$str]['numValue'];
						$numValueConsumptionTax =  $value[$str]['numValueConsumptionTax'];
						$flagConsumptionTaxWithoutCalc = $value[$str]['flagConsumptionTaxWithoutCalc'];

						if ($flagConsumptionTaxWithoutCalc == 1) {
							$data['numValueConsumptionTax' . $valueStr] = $numValueConsumptionTax;

						} elseif ($flagConsumptionTaxWithoutCalc == 2) {
							$data['numValueConsumptionTax' . $valueStr] = $numValueConsumptionTax;
							$data['numValue' . $valueStr] = $value[$str]['numValue'] + $numValueConsumptionTax;
						}
					}

					/*
					 * 20191001 start
					 */
					/*
					if ($flagRate) {
					    $data['numRateConsumptionTax' . $valueStr] = $value[$str]['numRateConsumptionTax'];
					}*/
					if ($flagRate) {
						$data['numRateConsumptionTax' . $valueStr] = $value[$str]['numRateConsumptionTax'];
						if ($value[$str]['flagRateConsumptionTaxReduced']) {
						    $data['flagRateConsumptionTaxReduced' . $valueStr] = $value[$str]['flagRateConsumptionTaxReduced'];
						}
					}
					/*
					 * 20191001 end
					 */

				} else {
					$data['idAccountTitle' . $valueStr] = '';
					$data['flagFS' . $valueStr] = '';
					$data['numValue' . $valueStr] = 0;
					$data['numValueConsumptionTax' . $valueStr] = 0;
					$data['numRateConsumptionTax' . $valueStr] = '';
					/*
					 * 20191001 start
					 */
					$data['flagRateConsumptionTaxReduced' . $valueStr] = '';
					/*
					 * 20191001 end
					 */
					$data['idSubAccountTitle' . $valueStr] = '';
					$data['idDepartment' . $valueStr] = '';
					$data['flagConsumptionTax' . $valueStr] = '';
					$data['flagConsumptionTaxWithoutCalc' . $valueStr] = '';
				}
			}
			$arrayNew[] = $data;
		}

		return $arrayNew;
	}

	/**
		array(
			'vars' => $vars,
			'rows' => $rows,
		)
	 */
	protected function _getCsvListAll($arr)
	{
		global $classTime;
		global $classFile;

		global $varsRequest;
		global $varsAccounts;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingAccountsId;

		$vars = &$arr['vars'];
		$varsId = $arr['vars']['varsItem']['varsId'];

		//id
		$arrayCsv = array();
		$rowCsv = array();
		$array = $varsId;
		foreach ($array as $key => $value) {
			$rowCsv[] = $value;
		}
		$arrayCsv[] = $rowCsv;

		if (!(int) $arr['rows']['numRows']) {
			$text = $classFile->getCsvText(array(
				'delimiter' => ',',
				'rows'      => $arrayCsv,
			));

			return $text;
		}

		$array = $arr['rows']['arrRows'];
		$num = 1;
		$flag = 0;
		foreach ($array as $key => $value) {
			$varsVersion = end($value['jsonVersion']);

			$dataDetail = $this->_getCsvJsonDetail(array(
				'vars'      => $vars,
				'arrDetail' => $varsVersion['jsonDetail']['varsDetail'],
			));

			$arrayDetail = $dataDetail;
			foreach ($arrayDetail as $keyDetail => $valueDetail) {
				$rowCsv = array();

				$rowCsv[] = $num;

				$rowCsv[] = $classTime->getDisplay(array(
					'flagType' => 'yearmin',
					'stamp'    => $value['stampBook'],
				));

				$fiscalPeriod = '';
				if (preg_match("/^f1$/", $value['flagFiscalReport'])) {
					$fiscalPeriod = $vars['varsItem']['varsOutput']['strFiscalReport1'];

				} elseif (preg_match("/^f21$/", $value['flagFiscalReport'])) {
					$fiscalPeriod = $vars['varsItem']['varsOutput']['strFiscalReport2'];
				}
				$rowCsv[] = $fiscalPeriod;

				$rowCsv[] = str_replace(',', $vars['varsItem']['strEscape'], $value['strTitle']);

				$arrayStr = array('Debit', 'Credit');
				foreach ($arrayStr as $keyStr => $valueStr) {

					$rowCsv[] = str_replace(',', $vars['varsItem']['strEscape'], $valueDetail['idAccountTitle' . $valueStr]);

					$rowCsv[] = str_replace(',', $vars['varsItem']['strEscape'], $valueDetail['flagFS' . $valueStr]);

					$rowCsv[] = $valueDetail['numValue' . $valueStr];

					$rowCsv[] = str_replace(',', $vars['varsItem']['strEscape'], $valueDetail['idSubAccountTitle' . $valueStr]);

					$rowCsv[] = str_replace(',', $vars['varsItem']['strEscape'], $valueDetail['idDepartment' . $valueStr]);

					$rowCsv[] = $valueDetail['flagConsumptionTax' . $valueStr];

					//$rowCsv[] = $valueDetail['numRateConsumptionTax' . $valueStr];

					/*
					 * 20191001 start
					 */
					if ($valueDetail['flagRateConsumptionTaxReduced' . $valueStr]) {
					    $rowCsv[] = $valueDetail['numRateConsumptionTax' . $valueStr] . $vars['varsItem']['varsOutput']['strRateConsumptionTaxReduced'];

					} else {
					    $rowCsv[] = $valueDetail['numRateConsumptionTax' . $valueStr];
					}
					/*
					 * 20191001 end
					 */

					$rowCsv[] = $valueDetail['flagConsumptionTaxWithoutCalc' . $valueStr];

					$rowCsv[] = $valueDetail['numValueConsumptionTax' . $valueStr];
				}

				$idAccount = $varsAccounts[$value['idAccount']]['strCodeName'];
				if (!$idAccount) {
					$idAccount = $varsPluginAccountingAccountsId[$value['idAccount']]['strCodeName'];
				}
				$rowCsv[] = str_replace(',', $vars['varsItem']['strEscape'], $idAccount);

				$value['arrSpaceStrTag'] = str_replace(',', $vars['varsItem']['strEscape'], $value['arrSpaceStrTag']);
				$rowCsv[] = preg_replace('/^ /', '', $value['arrSpaceStrTag']);

				$arrayCsv[] = $rowCsv;
			}
			$num++;
		}

		$text = $classFile->getCsvText(array(
			'delimiter' => ',',
			'rows'      => $arrayCsv,
		));

		return $text;
	}



}
