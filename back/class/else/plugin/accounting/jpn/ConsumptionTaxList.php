<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_ConsumptionTaxList extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference' => 'consumptionTaxListWindow',
		'idLog'        => 'logWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/consumptionTaxList.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/consumptionTaxList.php',
		'pathTplHtml'  => 'else/plugin/accounting/html/consumptionTaxList.html',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;
		global $classCheck;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'select',
			'idTarget'      => $this->_extSelf['idPreference'],
		));

		if (!$flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}

		$this->_checkEntity();

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));
		$flagConsumptionTaxFree = (int) $varsEntityNation['flagConsumptionTaxFree'];

		if ($flagConsumptionTaxFree) {
			$this->_sendOld();
			exit;
		}

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
	protected function _iniJs()
	{
		global $classSmarty;
		global $varsPluginAccountingAccount;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$vars['varsStampTerm'] = $varsItem['varsStampTerm'];

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$vars['varsFlag'] = array(
			'stampStart'            => $varsItem['varsStampTerm']['stampMin'],
			'stampEnd'              => $varsItem['varsStampTerm']['stampMax'],
			'flagConsumptionTax'    => $vars['varsFlag']['flagConsumptionTax'],
			'numRateConsumptionTax' => $vars['varsFlag']['numRateConsumptionTax'],
			'idDepartment'          => $vars['varsFlag']['idDepartment'],
		);

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $vars['varsFlag'],
		));

		$vars['flagAuthorityLog'] = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'select',
			'idTarget'      => $this->_extSelf['idLog'],
		));

		$json = json_encode($vars);
		$classSmarty->assign('varsLoad', $json);
		$classSmarty->assign('numNews', $this->getNumNews());
		$contents = $classSmarty->fetch($this->_extSelf['pathTplJs']);

		$this->sendJs(array(
			'data' => $contents,
		));

	}


	/**
		(array(
			'vars'     => $vars,
			'varsFlag' => $vars['varsFlag'],
		))
	 */
	protected function _getVarsItem($arr)
	{
		global $varsPluginAccountingAccount;

		$varsDepartment = $this->_getVarsDepartment(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrAccountTitle = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => array(),
			'numFiscalPeriod'    => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsStampTerm = $this->_getVarsStampTerm(array(
			'varsFlag'         => array('flagFiscalPeriod' => 'f1'),
			'varsEntityNation' => $varsEntityNation,
			'numFiscalPeriod'  => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsConsumptionTax = $this->_getVarsConsumptionTax(array());

		$varsFSItem = $this->_getVarsFSItem();

		$data = array(
			'arrAccountTitle'    => $arrAccountTitle,
			'varsConsumptionTax' => $varsConsumptionTax,
			'varsEntityNation'   => $varsEntityNation,
			'varsDepartment'     => $varsDepartment,
			'varsStampTerm'      => $varsStampTerm,
			'varsFSItem'         => $varsFSItem,
		);

		return $data;

	}


	/**
		(array(
			'vars'             => $vars,
			'varsItem'         => $arr['varsItem'],
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _updateVarsNavi($arr)
	{
		$vars = &$arr['vars'];
		$varsEntityNation = &$arr['varsItem']['varsEntityNation'];
		$varsItem = &$arr['varsItem'];

		$arrayNew = array();
		$array = &$vars['portal']['varsNavi']['templateDetail'];
		foreach ($array as $key => $value) {
			$method = '_updateVarsNavi' . $value['id'];
			if (method_exists($this, $method)) {
				$value = $this->$method(array(
					'vars'             => $value,
					'varsItem'         => $varsItem,
					'varsEntityNation' => $varsEntityNation,
				));
			}
			$arrayNew[] = $value;
		}

		$vars['portal']['varsNavi']['templateDetail'] = $arrayNew;

		return $vars['portal']['varsNavi']['templateDetail'];
	}

	/**
		(array(
			'vars'             => $value,
			'varsItem'         => $varsItem,
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _updateVarsNaviStampStart($arr)
	{
		global $classTime;

		$data = $arr['varsItem']['varsStampTerm'];

		$stampMin = $data['stampMin'];
		$strMin = $classTime->getDisplay(array(
			'flagType' => 'year/date',
			'stamp'    => $stampMin,
		));

		$stampMax = $data['stampMax'];
		$strMax = $classTime->getDisplay(array(
			'flagType' => 'year/date',
			'stamp'    => $stampMax,
		));

		$stampMain = $stampMin;

		$arr['vars']['varsFormCalender']['varsStatus']['stampMin'] = $stampMin * 1000;
		$arr['vars']['varsFormCalender']['varsStatus']['stampMain'] = $stampMin * 1000;
		$arr['vars']['varsFormCalender']['varsStatus']['stampMax'] = $stampMax * 1000;

		$arr['vars']['value'] = $strMin;

		return $arr['vars'];
	}

	/**
		(array(
			'vars'             => $value,
			'varsItem'         => $varsItem,
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _updateVarsNaviStampEnd($arr)
	{
		global $classTime;

		$data = $arr['varsItem']['varsStampTerm'];

		$stampMin = $data['stampMin'];
		$strMin = $classTime->getDisplay(array(
			'flagType' => 'year/date',
			'stamp'    => $stampMin,
		));

		$stampMax = $data['stampMax'];
		$strMax = $classTime->getDisplay(array(
			'flagType' => 'year/date',
			'stamp'    => $stampMax,
		));

		$stampMain = $stampMax;

		$arr['vars']['varsFormCalender']['varsStatus']['stampMin'] = $stampMin * 1000;
		$arr['vars']['varsFormCalender']['varsStatus']['stampMain'] = $stampMax * 1000;
		$arr['vars']['varsFormCalender']['varsStatus']['stampMax'] = $stampMax * 1000;

		$arr['vars']['value'] = $strMax;

		return $arr['vars'];
	}

	/**
		(array(
			'vars'             => $value,
			'varsItem'         => $varsItem,
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _updateVarsNaviFlagConsumptionTax($arr)
	{
		$arrayOption = array();

		if ($arr['varsItem']['varsEntityNation']['flagConsumptionTaxGeneralRule']) {
			if ($arr['varsItem']['varsEntityNation']['flagConsumptionTaxDeducted']) {
				$arrayOption = $arr['varsItem']['varsConsumptionTax']['generalEach'];

			} else {
				$arrayOption = $arr['varsItem']['varsConsumptionTax']['generalProration'];
			}

		} else {
			$arrayOption = $arr['varsItem']['varsConsumptionTax']['simple'];
		}

		array_unshift($arrayOption, $arr['vars']['varsTmpl']['varsAll']);
		$arr['vars']['arrayOption'] = $arrayOption;

		return $arr['vars'];
	}

	/**
		(array(
			'value'    => $value,
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		))
	 */
	protected function _updateVarsNaviIdDepartment($arr)
	{
		if (!$arr['varsItem']['varsDepartment']['arrSelectTag']) {
			return $arr['vars'];
		}
		$arrSelectTag = $arr['varsItem']['varsDepartment']['arrSelectTag'];
		array_unshift($arrSelectTag, $arr['vars']['varsTmpl']['varsNone']);
		$arr['vars']['arrayOption'] = $arrSelectTag;

		return $arr['vars'];
	}

	/**
		(array(
			'vars'             => $vars,
			'varsEntityNation' => $varsEntityNation,
			'varsItem'         => $varsItem,
			'varsFlag'         => array(
				'flagFiscalPeriod'  => $flagFiscalPeriod,
			),
			'flagOutput'       => ($arr['flagOutput'])? 1 : 0,
		))
	 */
	protected function _updateVars($arr)
	{
		$temp = $this->_getDetailHtml(array(
			'vars'     => $arr['vars'],
			'varsItem' => $arr['varsItem'],
			'varsFlag' => $arr['varsFlag'],
		));

		$arr['vars']['portal']['varsDetail']['varsDetail']['varsHtml'] = $temp['varsHtml'];
		$arr['vars']['portal']['varsDetail']['varsDetail']['varsList'] = $temp['varsList'];

		return $arr['vars'];
	}

	/**
		(array(
			'numFiscalPeriod' => $numFiscalPeriod,
			'arrIdTarget'     => $arr['arrIdTarget'],
		))
	 */
	protected function _getVarsLog($arr)
	{
		global $classDb;
		global $classEscape;
		global $varsPluginAccountingAccount;

		$arrWhere = array(
			array(
				'flagType'      => 'num',
				'strColumn'     => 'idEntity',
				'flagCondition' => 'eq',
				'value'         => $varsPluginAccountingAccount['idEntityCurrent'],
			),
			array(
				'flagType'      => 'num',
				'strColumn'     => 'numFiscalPeriod',
				'flagCondition' => 'eq',
				'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			),
			array(
				'flagType'      => '',
				'strColumn'     => 'flagApply',
				'flagCondition' => 'ne',
				'value'         => 1,
			),
			array(
				'flagType'      => '',
				'strColumn'     => 'flagRemove',
				'flagCondition' => 'ne',
				'value'         => 1,
			),
			array(
				'flagType'      => '',
				'strColumn'     => 'stampBook',
				'flagCondition' => 'eqBig',
				'value'         => $arr['varsFlag']['stampStart'],
			),
			array(
				'flagType'      => '',
				'strColumn'     => 'stampBook',
				'flagCondition' => 'eqSmall',
				'value'         => $arr['varsFlag']['stampEnd'],
			),
		);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLog',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => $arrWhere,
		));

		return $rows['arrRows'];
	}


	/**

	 */
	protected function _getDetailHtml($arr)
	{
		global $classSmarty;
		global $classEscape;
		global $varsPluginAccountingAccount;

		$varsData = $arr['vars']['varsItem'];

		$arrVarsLog = $this->_getVarsLog(array(
			'varsFlag' => $arr['varsFlag'],
		));

		$arrRows = $this->_getVarsLogCalcLoop(array(
			'arrVarsLog'      => $arrVarsLog,
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$temp = $this->_getDetailHtmlLoop(array(
			'varsItem' => $arr['varsItem'],
			'varsFlag' => $arr['varsFlag'],
			'arrRows'  => $arrRows,
			'varsStr'  => $arr['vars']['varsItem']
		));

		$array = $temp;
		foreach ($array as $key => $value) {
			$varsData[$key] = $value;
		}

		$array = $varsData;
		foreach ($array as $key => $value) {
			$classSmarty->assign($key, $value);
		}
		$contents = $classSmarty->fetch($this->_extSelf['pathTplHtml']);

		$contents = $classEscape->obfuscate(array(
			'data' => $contents,
		));

		$tempData = array(
			'varsHtml' => $contents,
			'varsList' => $temp['arrData'],
		);

		return $tempData;
	}

	/**

	 */
	protected function _getDetailHtmlLoop($arr)
	{
		$strTax = 'arrStrSimple';
		$strTaxRule = 'flagConsumptionTaxSimpleRule';
		if ((int) $arr['varsItem']['varsEntityNation']['flagConsumptionTaxGeneralRule']) {
			if ((int) $arr['varsItem']['varsEntityNation']['flagConsumptionTaxDeducted']) {
				$strTax = 'arrStrGeneralEach';
				$strTaxRule = 'flagConsumptionTaxGeneralRuleEach';

			} else {
				$strTax = 'arrStrGeneralProration';
				$strTaxRule = 'flagConsumptionTaxGeneralRuleProration';
			}
		}
		$varsTax = $arr['varsItem']['varsConsumptionTax'][$strTax];

		$varsSum = array();
		$array = $arr['arrRows'];
		foreach ($array as $key => $value) {
			if ($value['flagInsertTax']) {
				continue;
			}

			$idLog = $value['idLog'];

			if ($arr['varsFlag']['idDepartment'] != 'none') {
				if ($arr['varsFlag']['idDepartment'] != $value['idDepartment']) {
					continue;
				}
			}

			$rowData = array();
			$idAccountTitle = $value['idAccountTitle'];

			$flagConsumptionTax = '';
			if ($value['flagConsumptionTaxGeneralRuleEach']) {
				$flagConsumptionTax = $value['flagConsumptionTaxGeneralRuleEach'];

			} elseif ($value['flagConsumptionTaxGeneralRuleProration']) {
				$flagConsumptionTax = $value['flagConsumptionTaxGeneralRuleProration'];

			} elseif ($value['flagConsumptionTaxSimpleRule']) {
				$flagConsumptionTax = $value['flagConsumptionTaxSimpleRule'];
			}

			$varsAccountTitle = $arr['varsItem']['arrAccountTitle']['arrStrTitle'][$idAccountTitle];

			$numValue = $value['numValue'];
			$numValueConsumptionTax = $value['numValueConsumptionTax'];
			$numRateConsumptionTax = $value['numRateConsumptionTax'];

			/*
			 * 20191001 start
			 */
			if ($value['flagRateConsumptionTaxReduced']) {
			    $numRateConsumptionTax = '8_reduced';
			}
			/*
			 * 20191001 end
			 */

			$strBodyDebit = 'numBodyCredit';
			$strTaxDebit = 'numTaxCredit';
			if ($value['flagDebit']) {
				$strBodyDebit = 'numBodyDebit';
				$strTaxDebit = 'numTaxDebit';
			}

			if ($arr['varsFlag']['flagConsumptionTax'] != 'all') {
				if ($arr['varsFlag']['flagConsumptionTax'] != $flagConsumptionTax) {
					continue;
				}
			}

			if (preg_match("/^tax/", $flagConsumptionTax)
				|| preg_match("/^else/", $flagConsumptionTax)
			) {
			    /*
			     * 20191001 start
			     */
			    /*
			    if ((int) $arr['varsFlag']['numRateConsumptionTax'] != 0) {
			        if ($arr['varsFlag']['numRateConsumptionTax'] != $value['numRateConsumptionTax']) {
			            continue;
			        }
			    }
			    */
			    if ($arr['varsFlag']['numRateConsumptionTax'] != 0) {
    			    if ($arr['varsFlag']['numRateConsumptionTax'] != $numRateConsumptionTax) {
    			        continue;
    			    }
			    }

			    /*
			     * 20191001 end
			     */

				if ($idAccountTitle == 'suspensePaymentConsumptionTaxes'
					 || $idAccountTitle == 'suspenseReceiptOfConsumptionTaxes'
				) {
					$varsSum[$idAccountTitle][$flagConsumptionTax][$numRateConsumptionTax][$strBodyDebit] += $numValue;
					$varsSum[$idAccountTitle][$flagConsumptionTax][$numRateConsumptionTax][$strTaxDebit] += $numValue;

					$varsSum[$idAccountTitle][$flagConsumptionTax]['0'][$strBodyDebit] += $numValue;
					$varsSum[$idAccountTitle][$flagConsumptionTax]['0'][$strTaxDebit] += $numValue;

				} else {
					if (preg_match("/^tax/", $flagConsumptionTax)) {
						if ($value['flagConsumptionTaxIncluding']) {
							$varsSum[$idAccountTitle][$flagConsumptionTax][$numRateConsumptionTax][$strBodyDebit] += $numValue;
							$varsSum[$idAccountTitle][$flagConsumptionTax]['0'][$strBodyDebit] += $numValue;

						} else {
							if ($value['flagConsumptionTaxWithoutCalc'] == 1 || $value['flagConsumptionTaxWithoutCalc'] == 2) {
								$varsSum[$idAccountTitle][$flagConsumptionTax][$numRateConsumptionTax][$strBodyDebit] += $numValue;
								$varsSum[$idAccountTitle][$flagConsumptionTax][$numRateConsumptionTax][$strTaxDebit] += $numValueConsumptionTax;

								$varsSum[$idAccountTitle][$flagConsumptionTax]['0'][$strBodyDebit] += $numValue;
								$varsSum[$idAccountTitle][$flagConsumptionTax]['0'][$strTaxDebit] += $numValueConsumptionTax;

								$flagDebit = (preg_match("/^taxDebit/", $flagConsumptionTax))? 1 : 0;

								$idAccountTitleTax = $arr['varsItem']['varsFSItem']['varsJournal']['idAccountCredit'];
								if ($flagDebit) {
									$idAccountTitleTax = $arr['varsItem']['varsFSItem']['varsJournal']['idAccountDebit'];
								}

								$varsSum[$idAccountTitleTax][$flagConsumptionTax][$numRateConsumptionTax][$strBodyDebit] += $numValueConsumptionTax;

								$varsSum[$idAccountTitleTax][$flagConsumptionTax]['0'][$strBodyDebit] += $numValueConsumptionTax;

							} elseif ($value['flagConsumptionTaxWithoutCalc'] == 3) {
								$varsSum[$idAccountTitle][$flagConsumptionTax][$numRateConsumptionTax][$strBodyDebit] += $numValue;
								$varsSum[$idAccountTitle][$flagConsumptionTax]['0'][$strBodyDebit] += $numValue;
							}
						}

					} elseif (preg_match("/^else/", $flagConsumptionTax)) {
						$varsSum[$idAccountTitle][$flagConsumptionTax][$numRateConsumptionTax][$strBodyDebit] += $numValue;
						$varsSum[$idAccountTitle][$flagConsumptionTax]['0'][$strBodyDebit] += $numValue;
					}
				}

			} else {
				if ($idAccountTitle == 'suspensePaymentConsumptionTaxes'
					 || $idAccountTitle == 'suspenseReceiptOfConsumptionTaxes'
				) {
					$varsSum[$idAccountTitle][$flagConsumptionTax][$strBodyDebit] += $numValue;
					$varsSum[$idAccountTitle][$flagConsumptionTax][$strTaxDebit] += $numValue;

				} else {
					$varsSum[$idAccountTitle][$flagConsumptionTax][$strBodyDebit] += $numValue;
				}
			}
		}

		$id = 0;
		$arrayNew = array();
		$arrayIdAccountTitle = $varsSum;
		foreach ($arrayIdAccountTitle as $keyIdAccountTitle => $valueIdAccountTitle) {
			$idAccountTitle = $keyIdAccountTitle;
			$varsAccountTitle = $arr['varsItem']['arrAccountTitle']['arrStrTitle'][$idAccountTitle];

			$array = $valueIdAccountTitle;
			foreach ($array as $key => $value) {
				$flagConsumptionTax = $key;
				if (preg_match("/^tax/", $flagConsumptionTax)
					|| preg_match("/^else/", $flagConsumptionTax)
				) {
					$arrayRate = $value;
					foreach ($arrayRate as $keyRate => $valueRate) {
						$numRateConsumptionTax = $keyRate;

						if ($arr['varsFlag']['numRateConsumptionTax'] != $numRateConsumptionTax) {
							continue;
						}

						$rowData = array();
						$rowData['idAccountTitle'] = $idAccountTitle;
						$rowData['idDepartment'] = $arr['varsFlag']['idDepartment'];
						$rowData['strTitle'] = $varsAccountTitle['strTitleFS'];
						$rowData['flagTax'] = $flagConsumptionTax;
						$rowData['strTax'] = $varsTax[$flagConsumptionTax];
						$rowData['numRate'] = $numRateConsumptionTax;
						$rowData['numBodyDebit'] = $valueRate['numBodyDebit'];
						$rowData['numTaxDebit'] = $valueRate['numTaxDebit'];
						$rowData['numBodyCredit'] = $valueRate['numBodyCredit'];
						$rowData['numTaxCredit'] = $valueRate['numTaxCredit'];
						$rowData['id'] = $id;
						$arrayNew[$id] = $rowData;
						$id++;

					}

				} else {
					$rowData = array();
					$rowData['idAccountTitle'] = $idAccountTitle;
					$rowData['idDepartment'] = $arr['varsFlag']['idDepartment'];
					$rowData['strTitle'] = $varsAccountTitle['strTitleFS'];
					$rowData['flagTax'] = $flagConsumptionTax;
					$rowData['strTax'] = $varsTax[$flagConsumptionTax];
					$rowData['numRate'] = '';
					$rowData['numBodyDebit'] = $value['numBodyDebit'];
					$rowData['numTaxDebit'] = $value['numTaxDebit'];
					$rowData['numBodyCredit'] = $value['numBodyCredit'];
					$rowData['numTaxCredit'] = $value['numTaxCredit'];
					$rowData['id'] = $id;
					$arrayNew[$id] = $rowData;
					$id++;
				}
			}
		}

		$dataTemp = array(
			'sumBodyDebit'  => 0,
			'sumTaxDebit'   => 0,
			'sumBodyCredit' => 0,
			'sumTaxCredit'  => 0,
			'sumDebit'      => 0,
			'sumCredit'     => 0,
			'arrData'       => array(),
		);

		$array = &$arrayNew;
		foreach ($array as $key => $value) {
			$dataTemp['sumBodyDebit'] += $value['numBodyDebit'];
			$dataTemp['sumTaxDebit'] += $value['numTaxDebit'];
			$dataTemp['sumBodyCredit'] += $value['numBodyCredit'];
			$dataTemp['sumTaxCredit'] += $value['numTaxCredit'];
			if (!(is_null($value['numRate']) || $value['numRate'] === '')) {
				if ($value['numRate'] == 0) {
					$array[$key]['numRate'] = '';

				} else {
					$array[$key]['numRate'] .= '%';
				}

			}
			$array[$key]['numBodyDebit'] = number_format($value['numBodyDebit']);
			$array[$key]['numTaxDebit'] = number_format($value['numTaxDebit']);
			$array[$key]['numBodyCredit'] = number_format($value['numBodyCredit']);
			$array[$key]['numTaxCredit'] = number_format($value['numTaxCredit']);
		}

		$dataTemp['sumDebit'] = $dataTemp['sumBodyDebit'] + $dataTemp['sumTaxDebit'];
		$dataTemp['sumCredit'] = $dataTemp['sumBodyCredit'] + $dataTemp['sumTaxCredit'];


		//sort
		$arraySort = array();
		foreach ($array as $key => $value) {
			$arraySort[$value['idAccountTitle']][$value['flagTax']] = $value;
		}

		$temp = array();
		$arrayIdAccountTitle = $arr['varsItem']['arrAccountTitle']['arrStrTitle'];
		foreach ($arrayIdAccountTitle as $keyIdAccountTitle => $valueIdAccountTitle) {
			$idAccountTitle = $keyIdAccountTitle;
			$arrayTax = $varsTax;
			foreach ($arrayTax as $keyTax => $valueTax) {
				$flagConsumptionTax = $keyTax;
				if ($arraySort[$idAccountTitle][$flagConsumptionTax]) {
					$temp[$idAccountTitle][$flagConsumptionTax] = $arraySort[$idAccountTitle][$flagConsumptionTax];
				}
			}

		}

		$arraySorted = array();
		$array = $temp;
		foreach ($array as $key => $value) {
			$arrayTax = $value;
			foreach ($arrayTax as $keyTax => $valueTax) {
				$arraySorted[] = $valueTax;
			}
		}

		$dataTemp['arrData'] = $arraySorted;

		$array = &$dataTemp;
		foreach ($array as $key => $value) {
			if ($key == 'arrData') {
				continue;
			}
			$array[$key] = number_format($value);
		}

		return $array;
	}

	/**

	 */
	protected function _iniNaviSearch()
	{
		$this->_setSearch();
	}

	/**

	 */
	protected function _setSearch()
	{
		global $varsRequest;
		global $varsPluginAccountingPreference;
		global $varsPluginAccountingAccount;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$varsTarget = $this->getValue(array(
			'vars' => $vars['portal']['varsNavi']['varsDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget,
		));

		$arrValue = $this->_checkValueDetail(array(
			'varsItem' => $varsItem,
			'arrValue' => $arrValue,
		));

		$varsFlag = array(
			'stampStart'            => $arrValue['arr']['stampStart'],
			'stampEnd'              => $arrValue['arr']['stampEnd'],
			'flagConsumptionTax'    => $arrValue['arr']['flagConsumptionTax'],
			'numRateConsumptionTax' => $arrValue['arr']['numRateConsumptionTax'],
			'idDepartment'          => $arrValue['arr']['idDepartment'],
		);

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		));

		$this->sendVars(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'varsDetail' => $vars['portal']['varsDetail']['varsDetail'],
				'varsFlag'   => $varsFlag,
			),
		));
	}


	/**
		_checkValueDetail(array(
			'varsDetail'       => $varsDetail,
			'FlagFiscalPeriod' => $flagFiscalPeriod,
			'FlagUnit'         => $flagUnit,
		))
	 */
	protected function _checkValueDetail($arr)
	{
		global $classEscape;
		global $varsAccount;

		$numTimeZone = (int) $varsAccount['numTimeZone'];
		list($numYear, $numMonth, $numDate) = preg_split("/\//", $arr['arrValue']['arr']['stampStart']);

		$strTimeZone = (-1 * $numTimeZone) . 'hours';
		$dateTime = new DateTime("$numYear-$numMonth-$numDate 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stampStart = $dateTime->format('U');

		list($numYear, $numMonth, $numDate) = preg_split("/\//", $arr['arrValue']['arr']['stampEnd']);
		$dateTime = new DateTime("$numYear-$numMonth-$numDate 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stampEnd = $dateTime->format('U') + 86400 - 1;

		if ($stampStart > $stampEnd) {
			$this->_sendOld();
		}

		$stampMin = $arr['varsItem']['varsStampTerm']['stampMin'];
		$stampMax = $arr['varsItem']['varsStampTerm']['stampMax'];

		if (!(($stampMin <= $stampStart && $stampStart <= $stampMax)
			&& ($stampMin <= $stampEnd && $stampEnd <= $stampMax)
		)) {
			$this->_sendOld();
		}

		$arr['arrValue']['arr']['stampStart'] = $stampStart;
		$arr['arrValue']['arr']['stampEnd'] = $stampEnd;

		return $arr['arrValue'];
	}


	/**
	 *
	 */
	protected function _iniDetailReload()
	{
		$this->_setSearch();
	}

	/**
		overwrite Jpn
	 */
	protected function _getVarsLogCalc($arr)
	{
		global $classTime;

		$varsLog = $arr['varsLog'];
		$idLog = $varsLog['idLog'];
		$stampRegister = $varsLog['stampRegister'];
		$stampBook = $varsLog['stampBook'];
		$idEntity = $varsLog['idEntity'];
		$numFiscalPeriod = $varsLog['numFiscalPeriod'];
		$idAccount = $varsLog['idAccount'];
		$strTitle = $varsLog['strTitle'];
		$flagFiscalReport = $varsLog['flagFiscalReport'];
		$numEnd = count($varsLog['jsonVersion']) - 1;

		$arrayNew = array();
		$array = $this->_updateVarsJournalTax(array(
			'varsJournal'     => $arr['varsJournal'],
			'varsDetail'      => $varsLog['jsonVersion'][$numEnd]['jsonDetail']['varsDetail'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		foreach ($array as $key => $value) {
			$arrayStr = array('Debit', 'Credit');
			foreach ($arrayStr as $keyStr => $valueStr) {
				$flagDebit = ($valueStr == 'Debit')? 1 : 0;
				$strSide = 'arr' . $valueStr;
				$idAccountTitle = $value[$strSide]['idAccountTitle'];
				$numValue = $value[$strSide]['numValue'];
				$numRateConsumptionTax = $value[$strSide]['numRateConsumptionTax'];
				$idDepartment = ($value[$strSide]['idDepartment'])? $value[$strSide]['idDepartment'] : null;
				$idSubAccountTitle = ($value[$strSide]['idSubAccountTitle'])? $value[$strSide]['idSubAccountTitle'] : null;

				/*
				 * 20191001 start
				 */
				$flagRateConsumptionTaxReduced = ($value[$strSide]['flagRateConsumptionTaxReduced'])? 1 : 0;
				/*
				 * 20191001 end
				 */

				$flagConsumptionTaxIncluding = $value[$strSide]['flagConsumptionTaxIncluding'];
				$flagConsumptionTaxGeneralRuleEach = $value[$strSide]['flagConsumptionTaxGeneralRuleEach'];
				$flagConsumptionTaxGeneralRuleProration = $value[$strSide]['flagConsumptionTaxGeneralRuleProration'];
				$flagConsumptionTaxSimpleRule = $value[$strSide]['flagConsumptionTaxSimpleRule'];
				$flagConsumptionTaxWithoutCalc = $value[$strSide]['flagConsumptionTaxWithoutCalc'];
				$flagConsumptionTaxCalc = $value[$strSide]['flagConsumptionTaxCalc'];
				$idAccountTitleCredit = $varsLog['jsonVersion'][$numEnd]['jsonDetail']['idAccountTitleCredit'];
				$idAccountTitleDebit = $varsLog['jsonVersion'][$numEnd]['jsonDetail']['idAccountTitleDebit'];
				if ($flagDebit) {
					$idAccountTitleContra = $idAccountTitleCredit;
				} else {
					$idAccountTitleContra = $idAccountTitleDebit;
				}

				$idDepartmentContra = $this->_checkVarsLogCalcContra(array(
					'varsDetail' => $array,
					'strDebit'   => ($flagDebit)? 'arrCredit' : 'arrDebit',
					'idTarget'   => 'idDepartment',
				));
				$idSubAccountTitleContra = $this->_checkVarsLogCalcContra(array(
					'varsDetail' => $array,
					'strDebit'  => ($flagDebit)? 'arrCredit' : 'arrDebit',
					'idTarget'   => 'idSubAccountTitle',
				));

				if ($idAccountTitle) {
					$data = array(
						'idLog'                   => $idLog,
						'stampRegister'           => $stampRegister,
						'stampBook'               => $stampBook,
						'idEntity'                => $idEntity,
						'numFiscalPeriod'         => $numFiscalPeriod,
						'idAccount'               => $idAccount,
						'strTitle'                => $strTitle,
						'flagFiscalReport'        => $flagFiscalReport,
						'flagDebit'               => $flagDebit,
						'idAccountTitle'          => $idAccountTitle,
						'idAccountTitleContra'    => $idAccountTitleContra,
						'idDepartmentContra'      => $idDepartmentContra,
						'idSubAccountTitleContra' => $idSubAccountTitleContra,
						'numValue'                => $numValue,
						/*
						 * 20191001 start
						 */
					    'flagRateConsumptionTaxReduced' => $flagRateConsumptionTaxReduced,
					    /*
					     * 20191001 end
					     */
						'idDepartment'            => $idDepartment,
						'idSubAccountTitle'       => $idSubAccountTitle,
					);
					$arrayData = $data;
					$arrColumn = array();
					$arrValue = array();
					$num = 0;
					foreach ($arrayData as $keyData => $valueData) {
						$arrColumn[$num] = $keyData;
						$arrValue[$num] = $valueData;
						$num++;
					}
					$data['flagConsumptionTaxIncluding'] = $flagConsumptionTaxIncluding;
					$data['flagConsumptionTaxGeneralRuleEach'] = $flagConsumptionTaxGeneralRuleEach;
					$data['flagConsumptionTaxGeneralRuleProration'] = $flagConsumptionTaxGeneralRuleProration;
					$data['flagConsumptionTaxSimpleRule'] = $flagConsumptionTaxSimpleRule;
					$data['flagConsumptionTaxWithoutCalc'] = $flagConsumptionTaxWithoutCalc;
					$data['flagConsumptionTaxCalc'] = $flagConsumptionTaxCalc;


					//overwrite
					$data['numValueConsumptionTax'] = $value[$strSide]['numValueConsumptionTax'];
					$data['flagInsertTax'] = $value[$strSide]['flagInsertTax'];


					$data['numRateConsumptionTax'] = $numRateConsumptionTax;
					//$data['arrDate'] = $classTime->getLocal(array('stamp' => $stampBook));
					$data['flagDebitAccountTitle'] = $arr['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['flagDebit'];
					$data['flagFS'] = $arr['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['flagFS'];
					//$data['idAccountTitleJgaapFS'] = $arr['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['idAccountTitleJgaapFS'];
					//$data['varsJgaapCS'] = $arr['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['varsJgaapCS'];
					//$data['arrColumn'] = $arrColumn;
					//$data['arrValue'] = $arrValue;
					$arrayNew[] = $data;
				}
			}
		}

		return $arrayNew;
	}

}
