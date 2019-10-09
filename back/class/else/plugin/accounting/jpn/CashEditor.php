<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CashEditor extends Code_Else_Plugin_Accounting_Jpn_Cash
{
	protected $_childSelf = array(
		'pathTplJs'  => 'else/plugin/accounting/js/jpn/cashEditor.js',
		'pathVarsJs' => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/cashEditor.php',
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
	protected function _iniJs()
	{
		$this->_setJsEditor(array(
			'pathVars'  => $this->_childSelf['pathVarsJs'],
			'pathTpl'   => $this->_childSelf['pathTplJs'],
			'arrFolder' => array(
				array(
					'flagType'  => 'folder',
					'strTable'  => 'accountingAccountMemo',
					'strColumn' => 'jsonCashEditorNaviFormat',
					'flagEntity'  => 1,
					'flagAccount' => 1,
				),
			),
		));
	}

	/**
	 *
	 */
	protected function _iniDetailEstimate()
	{
		global $varsRequest;

		$strTitle = $varsRequest['query']['jsonValue']['vars']['StrTitle'];
		$flagIn = $varsRequest['query']['jsonValue']['vars']['FlagIn'];
		$classCalcDictionary = $this->_getClassCalc(array('flagType' => 'Dictionary'));
		$vars = $classCalcDictionary->allot((array(
			'flagStatus' => 'data',
			'strTitle'   => $strTitle,
		)));
		$vars['flagIn'] = (int) $flagIn;

		$this->sendVars(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $vars,
		));
	}

	/**
	 *
	 */
	protected function _iniNaviFormatSave()
	{
		$this->_setNaviFormatSave(array(
			'pathVars'  => $this->_childSelf['pathVarsJs'],
			'strColumn' => 'jsonCashEditorNaviFormat',
			'strTable'  => 'accountingAccountMemo',
			'flagEntity'  => 1,
			'flagAccount' => 1,
		));
	}

	/**
		$this->_setNaviFolderReload(array(
			'pathVars'  => '',
			'strColumn' => '',
			'strTable'  => '',
			'flagEntity' => 0,
			'flagAccount' => 0,
		));
	 */
	protected function _setNaviFolderReload($arr)
	{
		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
		));

		$vars['portal']['varsNavi']['templateFolder']['varsDetail']['varsDetail'] = $this->_getMemo(array(
			'strTable'    => $arr['strTable'],
			'strColumn'   => $arr['strColumn'],
			'flagEntity'  => $arr['flagEntity'],
			'flagAccount' => $arr['flagAccount'],
		));

		if (!$vars['portal']['varsNavi']['templateFolder']['varsDetail']['varsDetail']) {
			$varsDetail = $vars['portal']['varsNavi']['templateFolder']['varsDetail']['templateDetail']['dir'];
			$vars['portal']['varsNavi']['templateFolder']['varsDetail']['varsDetail'][] = $varsDetail;
		}

		$this->sendVars(array(
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'varsDetail' => $vars['portal']['varsNavi']['templateFolder']['varsDetail']['varsDetail'],
			),
		));
	}

	/**
	 *
	 */
	protected function _iniNaviFormatReload()
	{
		global $varsPluginAccountingAccount;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingAccount['stampUpdate'],
				'flagSearch'  => 0,
			));
		}

		$this->_setNaviFormatReload(array(
			'pathVars'    => $this->_childSelf['pathVarsJs'],
			'strColumn'   => 'jsonCashEditorNaviFormat',
			'strTable'    => 'accountingAccountMemo',
			'flagEntity'  => 1,
			'flagAccount' => 1,
		));
	}

	/**
	 *
	 */
	protected function _iniDetailAdd()
	{
		global $classDb;
		global $classEscape;

		global $varsRequest;
		$dbh = $classDb->getHandle();
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		if (!$this->_checkCurrent()) {
			$this->_sendOldError();
		}

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllInsert'] || $varsAuthority['flagMyInsert'])) {
			$this->_sendOldError();
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$vars = $this->_updateVars(array(
			'vars' => $vars,
		));

		$varsTarget = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$varsTarget = $this->_updateVarsDetail(array(
			'vars' => $varsTarget,
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget
		));

		$arrValue['arr']['flagFiscalReport'] = 'none';
		$this->_checkValueDetail(array(
			'vars'     => $vars,
			'arrValue' => $arrValue,
		));

		$classCalcCash = $this->_getClassCalc(array('flagType' => 'Cash'));
		try {
			$dbh->beginTransaction();

			$tempLog = $this->_setDbLog(array(
				'vars'     => $vars,
				'arrValue' => $arrValue,
			));

			$flag = $classCalcCash->allot(array(
				'flagStatus'      => 'addPre',
				'arrRows'         => array($tempLog['varsLogUpdate']),
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			));
			if ($flag == 'errorDataMax') {
				$this->sendVars(array(
					'flag'    => $flag,
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));
			}

			if ($tempLog['numFiscalPeriodTemp']) {
				$flag = $classCalcCash->allot(array(
					'flagStatus'      => 'addPre',
					'arrRows'         => array($tempLog['varsLogTempUpdate']),
					'numFiscalPeriod' => $tempLog['numFiscalPeriodTemp'],
					'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
				));
				if ($flag == 'errorDataMax') {
					$this->sendVars(array(
						'flag'    => $flag,
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(),
					));
				}
			}
			$this->_updateDbPreferenceStamp(array('strColumn' => 'cash'));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}
		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
		$this->_setSearch(array('flag' => 1));
	}

	/**

	 */
	protected function _updateVarsDetail($arr)
	{
		global $varsRequest;
		global $classEscape;

		$array = $arr['vars'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'NumSumMax') {
				$data = $varsRequest['query']['jsonValue']['vars']['ArrCommaIdAccountPermit'];
				$arrCommaIdAccountPermit = $classEscape->splitCommaArrayData(array('data' => $data));
				$numAll = count($arrCommaIdAccountPermit);
				$arrayOption = array();
				for ($i = 0; $i <= $numAll; $i++) {
					$rowData = array();
					$rowData['strTitle'] = $i;
					$rowData['value'] = $i;
					$arrayOption[] = $rowData;
				}
				$array[$key]['arrayOption'] = $arrayOption;
				break;
			}
		}

		return $array;
	}

	/**

	 */
	protected function _checkValueDetail($arr)
	{
		global $varsPluginAccountingAccount;
		global $varsAccount;

		$varsOrder = array(
			'numFiscalPeriod'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'                => $varsPluginAccountingAccount['idEntityCurrent'],
			'idAccount'               => $varsAccount['id'],
			'idAccountApply'          => $varsAccount['id'],
			'flagFiscalReport'        => $arr['arrValue']['arr']['flagFiscalReport'],
			'stampBook'               => $arr['arrValue']['arr']['stampBook'],
			'strTitle'                => $arr['arrValue']['arr']['strTitle'],
			'jsonDetail'              => $arr['arrValue']['arr']['jsonDetail'],
			'arrCommaIdLogFile'       => $arr['arrValue']['arr']['arrCommaIdLogFile'],
			'arrCommaIdAccountPermit' => $arr['arrValue']['arr']['arrCommaIdAccountPermit'],
			'numSumMax'               => $arr['arrValue']['arr']['numSumMax'],
			'arrSpaceStrTag'          => $arr['arrValue']['arr']['arrSpaceStrTag'],
		);

		$classCalcLog = $this->_getClassCalc(array('flagType' => 'Log'));

		$flag = $classCalcLog->allot(array(
			'flagStatus'      => 'check',
			'varsOrder'       => $varsOrder,
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagCheck'       => 'VarsEntityNation',
			'varsItem'        => &$arr['vars']['varsRule'],
		));
		if ($flag) {
			$this->_sendOldError();
		}

		$this->_checkValueDetailStampBook($arr);
		$this->_checkValueDetailJournal($arr);

		$flag = $classCalcLog->allot(array(
			'flagStatus'      => 'check',
			'varsOrder'       => $varsOrder,
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagCheck'       => 'File',
			'varsItem'        => &$arr['vars']['varsRule'],
		));
		if ($flag) {
			if ($flag == 'noneFile') {
				$this->sendVars(array(
					'flag'    => $flag,
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));

			} else {
				$this->_sendOldError();
			}
		}

		$flag = $classCalcLog->allot(array(
			'flagStatus'      => 'check',
			'varsOrder'       => $varsOrder,
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagCheck'       => 'Permit',
			'varsItem'        => &$arr['vars']['varsRule'],
		));
		if ($flag) {
			$this->_sendOldError();
		}
	}

	/**

	*/
	protected function _checkValueDetailStampBook($arr)
	{
		global $varsAccount;

		$numTimeZone = PLUGIN_ACCOUNTING_NUM_TIME_ZONE;
		$flagFiscalReport = $arr['arrValue']['arr']['flagFiscalReport'];
		$array = $arr['arrValue']['arr']['jsonDetail']['varsDetail'];

		if ($flagFiscalReport == 'none') {
			$data = $this->_getNumFiscalTermStamp(array(
				'varsEntityNation' => $arr['vars']['varsRule']['varsEntityNation']
			));
			$strStamp = $arr['arrValue']['arr']['stampBook'];
			preg_match( "/^([0-9]{4})\/([0-9]{1,2})\/([0-9]{1,2})-([0-9]{1,2}):([0-9]{1,2})$/", $strStamp, $arrMatch);
			list($strStamp, $numYear, $numMonth, $numDate, $numHour, $numMin) = $arrMatch;

			$strTimeZone = (-1 * $varsAccount['numTimeZone']) . 'hours';
			$dateTime = new DateTime("$numYear-$numMonth-$numDate 0:0 $strTimeZone", new DateTimeZone("UTC"));
			$stampBook = $dateTime->format('U') + $numHour * 3600 + $numMin * 60;

			$stampMin = $data['stampMin'];
			$stampMax = $data['stampMax'];
			$stampMaxLimit = $data['stampMaxLimit'];

			if (!($stampMin <= $stampBook && $stampBook <= $stampMaxLimit)) {
				$this->sendVars(array(
					'flag'    => 'term',
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));
			}

			$arrayTax = array(
				'flagConsumptionTaxGeneralRuleEach',
				'flagConsumptionTaxGeneralRuleProration',
				'flagConsumptionTaxSimpleRule'
			);
			foreach ($array as $key => $value) {
				$arrayStr = array('Debit', 'Credit');
				foreach ($arrayStr as $keyStr => $valueStr) {
					$strSide = 'arr' . $valueStr;
					$idAccountTitle = $value[$strSide]['idAccountTitle'];
					if ($idAccountTitle) {
						foreach ($arrayTax as $keyTax => $valueTax) {
							$flagTax = $value[$strSide][$valueTax];
							if (!($flagTax == 'none' || $flagTax == '')) {
								if (!($stampMin <= $stampBook && $stampBook <= $stampMax)) {
									$this->sendVars(array(
										'flag'    => 'termTax',
										'stamp'   => $this->getStamp(),
										'numNews' => $this->getNumNews(),
										'vars'    => array(),
									));
								}
							}
						}
					}
				}
			}
		}
	}

	/**

	*/
	protected function _getValueDetailStampBook($arr)
	{
		global $varsAccount;

		$numTimeZone = PLUGIN_ACCOUNTING_NUM_TIME_ZONE;
		$flagFiscalReport = $arr['arrValue']['arr']['flagFiscalReport'];

		$stampBook = 0;
		if ($flagFiscalReport == 'none') {
			$strStamp = $arr['arrValue']['arr']['stampBook'];
			preg_match( "/^([0-9]{4})\/([0-9]{1,2})\/([0-9]{1,2})-([0-9]{1,2}):([0-9]{1,2})$/", $strStamp, $arrMatch);
			list($strStamp, $numYear, $numMonth, $numDate, $numHour, $numMin) = $arrMatch;

			$strTimeZone = (-1 * $varsAccount['numTimeZone']) . 'hours';
			$dateTime = new DateTime("$numYear-$numMonth-$numDate 0:0 $strTimeZone", new DateTimeZone("UTC"));
			$stampBook = $dateTime->format('U') + $numHour * 3600 + $numMin * 60;
		}

		return $stampBook;

	}

	/**

	 */
	protected function _checkValueDetailJournal($arr)
	{
		global $classEscape;
		global $classCheck;
		global $classTime;

		$vars = $arr['vars'];
		$arrValue = $arr['arrValue'];

		$arrNum = array(
			'Debit' => 0,
			'Credit' => 0,
		);
		$flag = 0;
		$array = $arrValue['arr']['jsonDetail']['varsDetail'];

		$stampBook = $this->_getValueDetailStampBook($arr);
		$numRate = $classTime->checkRateConsumptionTax(array('stamp' => $stampBook));

		$flagConsumptionTaxFree = (int) $vars['varsRule']['varsEntityNation']['flagConsumptionTaxFree'];
		$flagConsumptionTaxIncluding = (int) $vars['varsRule']['varsEntityNation']['flagConsumptionTaxIncluding'];

		foreach ($array as $key => $value) {
			$arrayStr = array('Debit', 'Credit');
			foreach ($arrayStr as $keyStr => $valueStr) {
				$strSide = 'arr' . $valueStr;
				$idAccountTitle = $value[$strSide]['idAccountTitle'];
				if ($idAccountTitle) {
					if (!$vars['varsRule']['arrAccountTitle']['arrStrTitle'][$idAccountTitle]) {
						$flag = __LINE__;
						break;
					}
					if ($value[$strSide]['numValue'] <= 0) {
						$flag = __LINE__;
						break;
					}

					if ($value[$strSide]['numValueConsumptionTax'] != '') {
						$flagCheck = $classCheck->checkValueWord(array(
								'flagType' => 'num',
								'value'    => $value[$strSide]['numValueConsumptionTax']
						));
						if ($flagCheck) {
							$flag = __LINE__;
							break;
						}
					}

					$flagRate = 0;
					$flagRateTax = 0;
					if ($value[$strSide]['numRateConsumptionTax'] != '') {
						$flagRate = 1;
						//----2014-2015---//
						//if (!preg_match("/^(5|8|10)$/", $value[$strSide]['numRateConsumptionTax'])) {
						if (!preg_match("/^(5|8)$/", $value[$strSide]['numRateConsumptionTax'])) {
							$flag = __LINE__;
							break;

						} else {
							if ($numRate == 8) {
								if ($value[$strSide]['numRateConsumptionTax'] == 10) {
									$flag = __LINE__;
									break;
								}

							} elseif ($numRate == 5) {
								if ($value[$strSide]['numRateConsumptionTax'] == 8
									|| $value[$strSide]['numRateConsumptionTax'] == 10
								) {
									$flag = __LINE__;
									break;
								}
							}
						}
					}

					$idSubAccountTitle = $value[$strSide]['idSubAccountTitle'];
					if ($idSubAccountTitle) {
						if (!$vars['varsRule']['arrSubAccountTitle']['arrStrTitle'][$idAccountTitle][$idSubAccountTitle]) {
							$flag = __LINE__;
							break;
						}
					}
					$idDepartment = $value[$strSide]['idDepartment'];
					if ($idDepartment) {
						if (!$vars['varsRule']['arrDepartment']['arrStrTitle'][$idDepartment]) {
							$flag = __LINE__;
							break;
						}
					}
					if ($flagConsumptionTaxFree != $value[$strSide]['flagConsumptionTaxFree']) {
						$flag = __LINE__;
						break;
					}
					if ($flagConsumptionTaxIncluding != $value[$strSide]['flagConsumptionTaxIncluding']) {
						$flag = __LINE__;
						break;
					}

					$arrNum[$valueStr] += $value[$strSide]['numValue'];

					$flagTax = 0;

					$flagConsumptionTaxGeneralRuleEach = $value[$strSide]['flagConsumptionTaxGeneralRuleEach'];
					$flagConsumptionTaxGeneralRuleProration = $value[$strSide]['flagConsumptionTaxGeneralRuleProration'];
					$flagConsumptionTaxSimpleRule = $value[$strSide]['flagConsumptionTaxSimpleRule'];

					if ($flagConsumptionTaxGeneralRuleEach) {
						if (!$vars['varsRule']['varsConsumptionTax']['arrStrGeneralEach'][$flagConsumptionTaxGeneralRuleEach]) {
							$flag = __LINE__;
							break;
						}
						if ((int) $vars['varsRule']['varsEntityNation']['flagConsumptionTaxGeneralRule']
							&& (int) $vars['varsRule']['varsEntityNation']['flagConsumptionTaxDeducted']
							&& preg_match( "/^tax/", $flagConsumptionTaxGeneralRuleEach)
						) {
							$flagTax = 1;
						}
						if ($flagConsumptionTaxGeneralRuleProration || $flagConsumptionTaxSimpleRule) {
							$flag = __LINE__;
							break;
						}
						if ($flagRate) {
							if (!$flagConsumptionTaxFree) {
								if (preg_match( "/^tax/", $flagConsumptionTaxGeneralRuleEach)
									|| preg_match( "/^else/", $flagConsumptionTaxGeneralRuleEach)
								) {
									$flagRateTax = 1;
								}
							}
						}
					}


					if ($flagConsumptionTaxGeneralRuleProration) {
						if (!$vars['varsRule']['varsConsumptionTax']['arrStrGeneralProration'][$flagConsumptionTaxGeneralRuleProration]) {
							$flag = __LINE__;
							break;
						}
						if ((int) $vars['varsRule']['varsEntityNation']['flagConsumptionTaxGeneralRule']
							&& !(int) $vars['varsRule']['varsEntityNation']['flagConsumptionTaxDeducted']
							&& preg_match( "/^tax/", $flagConsumptionTaxGeneralRuleProration)
						) {
							$flagTax = 1;
						}
						if ($flagConsumptionTaxGeneralRuleEach || $flagConsumptionTaxSimpleRule) {
							$flag = __LINE__;
							break;
						}
						if ($flagRate) {
							if (!$flagConsumptionTaxFree) {
								if (preg_match( "/^tax/", $flagConsumptionTaxGeneralRuleProration)
									|| preg_match( "/^else/", $flagConsumptionTaxGeneralRuleProration)
								) {
									$flagRateTax = 1;
								}
							}
						}
					}

					if ($flagConsumptionTaxSimpleRule) {
						if (!$vars['varsRule']['varsConsumptionTax']['arrStrSimple'][$flagConsumptionTaxSimpleRule]) {
							$flag = __LINE__;
							break;
						}
						if (!(int) $vars['varsRule']['varsEntityNation']['flagConsumptionTaxGeneralRule']
							&& preg_match( "/^tax/", $value[$strSide]['flagConsumptionTaxSimpleRule'])
						) {
							$flagTax = 1;
						}
						if ($flagConsumptionTaxGeneralRuleEach || $flagConsumptionTaxGeneralRuleProration) {
							$flag = __LINE__;
							break;
						}
						if ($flagRate) {
							if (!$flagConsumptionTaxFree) {
								if (preg_match( "/^tax/", $flagConsumptionTaxSimpleRule)
									|| preg_match( "/^else/", $flagConsumptionTaxSimpleRule)
								) {
									$flagRateTax = 1;
								}
							}
						}
					}

					if ($idAccountTitle == 'suspenseReceiptOfConsumptionTaxes' || $idAccountTitle == 'suspensePaymentConsumptionTaxes') {
						$flagTax = 0;
					}
/*
					if ($flagConsumptionTaxFree
						&& ($idAccountTitle == 'suspenseReceiptOfConsumptionTaxes' || $idAccountTitle == 'suspensePaymentConsumptionTaxes')
					) {
						$flag = __LINE__;
						break;
					}
*/
					$flagConsumptionTaxWithoutCalc = $value[$strSide]['flagConsumptionTaxWithoutCalc'];
					if ($flagConsumptionTaxWithoutCalc) {
						if (!($flagConsumptionTaxWithoutCalc == 1
							|| $flagConsumptionTaxWithoutCalc == 2
							|| $flagConsumptionTaxWithoutCalc == 3
						)) {
							$flag = __LINE__;
							break;
						}
					}

					$flagConsumptionTaxCalc = $value[$strSide]['flagConsumptionTaxCalc'];
					if ($flagConsumptionTaxCalc) {
						if (!($flagConsumptionTaxCalc == 1
							|| $flagConsumptionTaxCalc == 2
							|| $flagConsumptionTaxCalc == 3
						)) {
							$flag = __LINE__;
							break;
						}
					}

					if ($flagTax) {
						if ($flagConsumptionTaxWithoutCalc == 2) {
							$numValue = $value[$strSide]['numValue'];
							$numValueConsumptionTax = $value[$strSide]['numValueConsumptionTax'];
							if ($numValue < $numValueConsumptionTax) {
								$flag = __LINE__;
								break;
							}
							$arrNum[$valueStr] += $numValueConsumptionTax;
						}
					}
					if ($flagRate) {
						if (!$flagRateTax) {
							$flag = __LINE__;
							break;
						}
					}
					if ($arrValue['arr']['flagIn'] == 1) {
						if ($valueStr == 'Debit') {
							if (!$vars['varsRule']['arrAccountTitleCash']['arrStrTitle'][$idAccountTitle]) {
								$flag = __LINE__;
								break;
							}

						} else {
							if ($vars['varsRule']['arrAccountTitleCash']['arrStrTitle'][$idAccountTitle]) {
								$flag = __LINE__;
								break;
							}
						}

					} elseif ($arrValue['arr']['flagIn'] == 2) {
						if (!$vars['varsRule']['arrAccountTitleCash']['arrStrTitle'][$idAccountTitle]) {
							$flag = __LINE__;
							break;
						}

					} else {
						if ($valueStr == 'Credit') {
							if (!$vars['varsRule']['arrAccountTitleCash']['arrStrTitle'][$idAccountTitle]) {
								$flag = __LINE__;
								break;
							}

						} else {
							if ($vars['varsRule']['arrAccountTitleCash']['arrStrTitle'][$idAccountTitle]) {
								$flag = __LINE__;
								break;
							}
						}
					}

					/*
					 * free-MonetaryClaim is H26.4.1~
					* */
					if ($value[$strSide]['flagConsumptionTaxGeneralRuleEach'] == 'free-MonetaryClaim'
						|| $value[$strSide]['flagConsumptionTaxGeneralRuleProration'] == 'free-MonetaryClaim'
						|| $value[$strSide]['flagConsumptionTaxSimpleRule'] == 'free-MonetaryClaim'
					) {
						if ($numRate == 5) {
							$flag = 'free-MonetaryClaim';
							break;
						}
					}

				} else {
					$flag = 'idAccountTitle';
					break;
				}
			}
			$idAccountTitleDebit = $value['arrDebit']['idAccountTitle'];
			$idAccountTitleCredit = $value['arrCredit']['idAccountTitle'];
			if (!$idAccountTitleDebit && !$idAccountTitleCredit) {
				$flag = __LINE__;
			}
			if ($flag) {
				break;
			}
		}

		if ($flag) {
			$this->_sendOldError();
		}

		if ($arrValue['arr']['jsonDetail']['numSumDebit'] != $arrValue['arr']['jsonDetail']['numSumCredit']
			|| $arrValue['arr']['jsonDetail']['numSumDebit'] != $arrNum['Debit']
			|| $arrValue['arr']['jsonDetail']['numSumCredit'] != $arrNum['Credit']
			|| $arrValue['arr']['jsonDetail']['numSumCredit'] > 99999999999
			|| count($array) > 10
		) {
			$flag = __LINE__;
		}

		if ($flag) {
			$this->_sendOldError();
		}
	}

	/**
				'vars'     => $vars,
				'arrValue' => $arrValue,
	 */
	protected function _setDbLog($arr)
	{
		global $classEscape;
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsAccount;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$vars = $arr['vars'];
		$arrValue = $arr['arrValue'];

		$stampRegister = TIMESTAMP;
		$stampUpdate = TIMESTAMP;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = (int) $varsPluginAccountingAccount['numFiscalPeriodCurrent'];

		$idAccount = $varsAccount['id'];

		$flagIn = $arrValue['arr']['flagIn'];
		$strTitle = $arrValue['arr']['strTitle'];

		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrValue['arr']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));

		$flagApply = 1;
		$idAccountApply = $idAccount;
		$flagApplyBack = 0;
		$array = $classEscape->splitCommaArrayData(array('data' => $arrValue['arr']['arrCommaIdAccountPermit']));
		if (!count($array)) {
			$flagApply = 0;
			$arrValue['arr']['numSumMax'] = 0;
			$idAccountApply = null;
		}

		$arrChargeHistory = array(
			array(
				'stampRegister' => TIMESTAMP,
				'idAccount'     => $idAccount,
			),
		);
		$jsonChargeHistory = json_encode($arrChargeHistory);

		$arrPermitHistory = array(
			array(
				'flagInvalid'        => 0,
				'stampRegister'      => TIMESTAMP,
				'numSumMax'          => (int) $arrValue['arr']['numSumMax'],
				'idAccountApply'     => $idAccountApply,
				'arrIdAccountPermit' => $this->_getDbLogArrIdAccountPermit($arrValue),
			),
		);
		if (!$flagApply) {
			$arrPermitHistory = array();
		}
		$jsonPermitHistory = json_encode($arrPermitHistory);
		$arrCommaIdAccountPermit = $this->_getDbLogArrCommaIdAccountPermit($arrPermitHistory);

		$arrCommaIdLogFile = $classEscape->splitCommaArrayData(array('data' => $arrValue['arr']['arrCommaIdLogFile']));
		$arrCommaIdLogFile = $classEscape->joinCommaArray(array('arr' => $arrCommaIdLogFile));

		if ($arrValue['arr']['flagFiscalReport'] == 'none') {
			$arrValue['arr']['flagFiscalReport'] = '0';
		}

		$stampBook = $this->_getDbLogStampBook(array(
			'arrValue' => $arrValue,
		));
		$arrValue['arr']['stampBook'] = $stampBook;

		$classCalcLog = $this->_getClassCalc(array('flagType' => 'Log'));
		$varsVersion = $classCalcLog->allot(array(
			'flagStatus'      => 'varsVersion',
			'arrValue'        => $arrValue['arr'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrVersion = &$varsVersion['arrVersion'][0];
		$arrVersion['flagIn'] = $flagIn;

		$arrVersion['jsonPermitHistory'] = $arrPermitHistory;
		$jsonVersion = json_encode($varsVersion['arrVersion']);

		$numValue = $varsVersion['numValue'];

		$arrCommaIdDepartmentDebit = $varsVersion['arrCommaIdDepartmentDebit'];
		$arrCommaIdAccountTitleDebit = $varsVersion['arrCommaIdAccountTitleDebit'];
		$arrCommaIdSubAccountTitleDebit = $varsVersion['arrCommaIdSubAccountTitleDebit'];
		$arrCommaRateConsumptionTaxDebit = $varsVersion['arrCommaRateConsumptionTaxDebit'];
		$arrCommaConsumptionTaxDebit = $varsVersion['arrCommaConsumptionTaxDebit'];
		$arrCommaConsumptionTaxWithoutCalcDebit = $varsVersion['arrCommaConsumptionTaxWithoutCalcDebit'];
		$arrCommaTaxPaymentDebit = $varsVersion['arrCommaTaxPaymentDebit'];
		$arrCommaTaxReceiptDebit = $varsVersion['arrCommaTaxReceiptDebit'];

		$arrCommaIdDepartmentCredit = $varsVersion['arrCommaIdDepartmentCredit'];
		$arrCommaIdAccountTitleCredit = $varsVersion['arrCommaIdAccountTitleCredit'];
		$arrCommaIdSubAccountTitleCredit = $varsVersion['arrCommaIdSubAccountTitleCredit'];
		$arrCommaRateConsumptionTaxCredit = $varsVersion['arrCommaRateConsumptionTaxCredit'];
		$arrCommaConsumptionTaxCredit = $varsVersion['arrCommaConsumptionTaxCredit'];
		$arrCommaConsumptionTaxWithoutCalcCredit = $varsVersion['arrCommaConsumptionTaxWithoutCalcCredit'];
		$arrCommaTaxPaymentCredit = $varsVersion['arrCommaTaxPaymentCredit'];
		$arrCommaTaxReceiptCredit = $varsVersion['arrCommaTaxReceiptCredit'];

		$varsIdNumber = $this->_getIdAutoIncrement(array(
			'idTarget' => 'idLogCash'
		));
		if (!$varsIdNumber[$idEntity]) {
			$varsIdNumber[$idEntity] = 1;
		}
		$idLogCash = $varsIdNumber[$idEntity];

		$arrayTemp = compact(
			'stampRegister',
			'stampUpdate',
			'stampBook',
			'idLogCash',
			'idEntity',
			'numFiscalPeriod',
			'idAccount',
			'flagIn',
			'strTitle',
			'arrSpaceStrTag',
			'flagApply',
			'idAccountApply',
			'arrCommaIdAccountPermit',
			'arrCommaIdLogFile',
			'jsonVersion',
			'numValue',
			'arrCommaIdDepartmentDebit',
			'arrCommaIdAccountTitleDebit',
			'arrCommaIdSubAccountTitleDebit',
			'arrCommaConsumptionTaxDebit',
			'arrCommaRateConsumptionTaxDebit',
			'arrCommaConsumptionTaxWithoutCalcDebit',
			'arrCommaTaxPaymentDebit',
			'arrCommaTaxReceiptDebit',
			'arrCommaIdDepartmentCredit',
			'arrCommaIdAccountTitleCredit',
			'arrCommaIdSubAccountTitleCredit',
			'arrCommaConsumptionTaxCredit',
			'arrCommaRateConsumptionTaxCredit',
			'arrCommaConsumptionTaxWithoutCalcCredit',
			'arrCommaTaxPaymentCredit',
			'arrCommaTaxReceiptCredit',
			'jsonChargeHistory',
			'jsonPermitHistory'
		);
		$arrColumn = array();
		$arrValue = array();
		foreach ($arrayTemp as $keyTemp => $valueTemp) {
			$arrColumn[] = $keyTemp;
			$arrValue[] = $valueTemp;
		}

		$id = $classDb->insertRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingLogCash',
			'arrColumn' => $arrColumn,
			'arrValue'  => $arrValue,
		));

		$varsIdNumber[$idEntity]++;
		$this->_updateIdAutoIncrement(array(
			'idTarget'   => 'idLogCash',
			'varsTarget' => $varsIdNumber
		));

		$data = $this->_getNumFiscalTermStamp(array(
			'varsEntityNation' => $arr['vars']['varsRule']['varsEntityNation']
		));

		$varsLogUpdate = $this->_getVarsLog(array(
			'idTarget'        => $idLogCash,
			'numFiscalPeriod' => $numFiscalPeriod,
			'idEntity'        => $idEntity,
		));

		$numFiscalPeriodTemp = 0;
		$varsLogTempUpdate = array();
		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)
			&& $stampBook > $data['stampMax']
		) {
			$numFiscalPeriodTemp = $numFiscalPeriod + 1;
			$arrColumn = array();
			$arrValue = array();
			foreach ($arrayTemp as $keyTemp => $valueTemp) {
				if ($keyTemp == 'numFiscalPeriod') {
					$valueTemp = $numFiscalPeriodTemp;
				}
				$arrColumn[] = $keyTemp;
				$arrValue[] = $valueTemp;
			}
			$id = $classDb->insertRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingLogCash',
				'arrColumn' => $arrColumn,
				'arrValue'  => $arrValue,
			));

			$varsLogTempPrev = $this->_getVarsLog(array(
				'idTarget'        => $idLogCash,
				'numFiscalPeriod' => $numFiscalPeriodTemp,
				'idEntity'        => $idEntity,
			));

			$classCalcCash = $this->_getClassCalc(array('flagType' => 'Cash'));
			$flagErrorVars = $classCalcCash->allot(array(
				'flagStatus'       => 'UpdateVarsTax',
				'arrRows'          => array($varsLogTempPrev),
				'numFiscalPeriod'  => $numFiscalPeriodTemp,
				'idEntity'         => $idEntity,
			));
			if ($flagErrorVars['flag'] == 'textMaxOver') {
				$this->sendVars(array(
					'flag'    => 'errorDataMax',
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));
			}
			$varsLogTempUpdate = $this->_getVarsLog(array(
				'idTarget'        => $idLogCash,
				'numFiscalPeriod' => $numFiscalPeriodTemp,
				'idEntity'        => $idEntity,
			));

		}

		$tempData = array(
			'varsLogUpdate'       => $varsLogUpdate,
			'varsLogTempUpdate'   => $varsLogTempUpdate,
			'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
		);

		return $tempData;
	}

	/**

	 */
	protected function _getDbLogArrCommaIdAccountPermit($arrPermitHistory)
	{
		global $classEscape;

		$array = $arrPermitHistory;
		$arrayCheck = array();
		foreach ($array as $key => $value) {
			$arrayPermit = $value['arrIdAccountPermit'];
			foreach ($arrayPermit as $keyPermit => $valuePermit) {
				$arrayCheck[$valuePermit['idAccount']] = 1;
			}
		}

		$arrayNew = array();
		$array = $arrayCheck;
		foreach ($array as $key => $value) {
			$arrayNew[] = $key;
		}

		$str = $classEscape->joinCommaArray(array('arr' => $arrayNew));

		return $str;
	}

	/**

	 */
	protected function _getDbLogArrIdAccountPermit($arrValue)
	{
		global $classEscape;
		global $varsAccounts;

		$array = $classEscape->splitCommaArrayData(array('data' => $arrValue['arr']['arrCommaIdAccountPermit']));
		$arrayNew = array();
		foreach ($array as $key => $value) {
			$data = array(
				'stampRegister' => 0,
				'idAccount'     => $value,
				'flagPermit'    => 'none',
			);
			$arrayNew[] = $data;
		}

		return $arrayNew;
	}

	/**
	 *
	 */
	protected function _getDbLogStampBook($arr)
	{
		global $varsAccount;

		$arrValue = $arr['arrValue'];

		$numTimeZone = PLUGIN_ACCOUNTING_NUM_TIME_ZONE;
		$strTimeZone = (-1 * $numTimeZone) . 'hours';

		$strStamp = $arrValue['arr']['stampBook'];
		preg_match( "/^([0-9]{4})\/([0-9]{1,2})\/([0-9]{1,2})-([0-9]{1,2}):([0-9]{1,2})$/", $strStamp, $arrMatch);
		list($strStamp, $numYear, $numMonth, $numDate, $numHour, $numMin) = $arrMatch;

		$strTimeZone = (-1 * $varsAccount['numTimeZone']) . 'hours';
		$dateTime = new DateTime("$numYear-$numMonth-$numDate 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stampBook = $dateTime->format('U') + $numHour * 3600 + $numMin * 60;

		return $stampBook;
	}

	/**
	 *
	 */
	protected function _iniDetailEdit()
	{
		global $classDb;
		global $classEscape;

		global $varsRequest;
		global $varsAccount;
		$dbh = $classDb->getHandle();
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$flagCurrent = $this->_checkCurrent();
		if (!$flagCurrent) {
			$this->_sendOldError();
		}

		$varsAuthority = $this->_getVarsAuthority(array());

		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'] || $varsAuthority['flagMyUpdate'])) {
			$this->_sendOldError();
		}

		$varsLog = $this->_getVarsLog(array(
			'idTarget'        => $varsRequest['query']['jsonValue']['idTarget'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			'flagRemove'      => 0,
		));

		if (!$varsLog) {
			$this->_sendOldError();

		} else {
			if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'])) {
				if ($varsAuthority['flagMyUpdate']) {
					if ($varsLog['idAccount'] != $varsAccount['id']) {
						$this->_sendOldError();
					}
				}
			}
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$vars = $this->_updateVars(array(
			'vars' => $vars,
		));

		$varsTarget['vars']['varsDetail'] = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$varsTarget['vars']['varsDetail'] = $this->_updateVarsDetail(array(
			'vars' => $varsTarget['vars']['varsDetail'],
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget['vars']['varsDetail']
		));

		$arrValue['arr']['flagFiscalReport'] = 'none';
		$this->_checkValueDetail(array(
			'vars'     => $vars,
			'arrValue' => $arrValue,
		));

		$dataTerm = $this->_getNumFiscalTermStamp(array(
			'varsEntityNation' => $vars['varsRule']['varsEntityNation']
		));

		$classCalcCash = $this->_getClassCalc(array('flagType' => 'Cash'));
		try {
			$dbh->beginTransaction();

			$tempLog = $this->_updateDbLog(array(
				'vars'     => $vars,
				'arrValue' => $arrValue,
				'varsLog'  => $varsLog,
				'dataTerm' => $dataTerm,
			));

			if (!$tempLog['varsLog']['flagPay']) {
				$flag = $classCalcCash->allot(array(
					'flagStatus'      => 'editPre',
					'arrRowsAdd'      => array($tempLog['varsLogUpdate']),
					'arrRowsDelete'   => array($tempLog['varsLog']),
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
				));
				if ($flag == 'errorDataMax') {
					$this->sendVars(array(
						'flag'    => $flag,
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(),
					));
				}

			} else {
				$flag = $classCalcCash->allot(array(
					'flagStatus'      => 'addPre',
					'arrRows'         => array($tempLog['varsLogUpdate']),
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
				));
				if ($flag == 'errorDataMax') {
					$this->sendVars(array(
						'flag'    => $flag,
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(),
					));
				}
				$flag = $classCalcCash->allot(array(
					'flagStatus'      => 'deleteDone',
					'arrRows'         => array($tempLog['varsLog']),
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
				));
				if ($flag == 'errorDataMax') {
					$this->sendVars(array(
						'flag'    => $flag,
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(),
					));
				}
			}

			if ($tempLog['numFiscalPeriodTemp']) {
				if ($tempLog['varsLogTemp']) {
					if ($tempLog['flagPeriodOver']) {
						if (!$tempLog['varsLogTemp']['flagRemove']) {
							if (!$tempLog['varsLogTemp']['flagPay']) {
								$flag = $classCalcCash->allot(array(
									'flagStatus'      => 'editPre',
									'arrRowsAdd'      => array($tempLog['varsLogTempUpdate']),
									'arrRowsDelete'   => array($tempLog['varsLogTemp']),
									'numFiscalPeriod' => $tempLog['numFiscalPeriodTemp'],
									'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
								));
								if ($flag == 'errorDataMax') {
									$this->sendVars(array(
										'flag'    => $flag,
										'stamp'   => $this->getStamp(),
										'numNews' => $this->getNumNews(),
										'vars'    => array(),
									));
								}

							} else {
								$flag = $classCalcCash->allot(array(
									'flagStatus'      => 'addPre',
									'arrRows'         => array($tempLog['varsLogTempUpdate']),
									'numFiscalPeriod' => $tempLog['numFiscalPeriodTemp'],
									'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
								));
								if ($flag == 'errorDataMax') {
									$this->sendVars(array(
										'flag'    => $flag,
										'stamp'   => $this->getStamp(),
										'numNews' => $this->getNumNews(),
										'vars'    => array(),
									));
								}
								$flag = $classCalcCash->allot(array(
									'flagStatus'      => 'deleteDone',
									'arrRows'         => array($tempLog['varsLogTemp']),
									'numFiscalPeriod' => $tempLog['numFiscalPeriodTemp'],
									'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
								));
								if ($flag == 'errorDataMax') {
									$this->sendVars(array(
										'flag'    => $flag,
										'stamp'   => $this->getStamp(),
										'numNews' => $this->getNumNews(),
										'vars'    => array(),
									));
								}
							}
						}

					} else {
						if (!$tempLog['varsLogTemp']['flagRemove']) {
							if (!$tempLog['varsLogTemp']['flagPay']) {
								$flag = $classCalcCash->allot(array(
									'flagStatus'      => 'deletePre',
									'arrRows'         => array($tempLog['varsLogTemp']),
									'numFiscalPeriod' => $tempLog['numFiscalPeriodTemp'],
									'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
								));
								if ($flag == 'errorDataMax') {
									$this->sendVars(array(
										'flag'    => $flag,
										'stamp'   => $this->getStamp(),
										'numNews' => $this->getNumNews(),
										'vars'    => array(),
									));
								}

							} else {
								$flag = $classCalcCash->allot(array(
									'flagStatus'      => 'deleteDone',
									'arrRows'         => array($tempLog['varsLogTemp']),
									'numFiscalPeriod' => $tempLog['numFiscalPeriodTemp'],
									'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
								));
								if ($flag == 'errorDataMax') {
									$this->sendVars(array(
										'flag'    => $flag,
										'stamp'   => $this->getStamp(),
										'numNews' => $this->getNumNews(),
										'vars'    => array(),
									));
								}
							}
						}
					}

				} else {
					if ($tempLog['flagPeriodOver']) {
						$flag = $classCalcCash->allot(array(
							'flagStatus'      => 'addPre',
							'arrRows'         => array($tempLog['varsLogTempUpdate']),
							'numFiscalPeriod' => $tempLog['numFiscalPeriodTemp'],
							'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
						));
						if ($flag == 'errorDataMax') {
							$this->sendVars(array(
								'flag'    => $flag,
								'stamp'   => $this->getStamp(),
								'numNews' => $this->getNumNews(),
								'vars'    => array(),
							));
						}
					}
				}
			}
			$this->_updateDbPreferenceStamp(array('strColumn' => 'cash'));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}
		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$this->_iniSearchDetail();

	}

	/**

	 */
	protected function _updateDbLog($arr)
	{
		global $classEscape;
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsAccount;
		global $varsRequest;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$vars = $arr['vars'];
		$arrValue = $arr['arrValue'];
		$varsLog = $arr['varsLog'];

		$stampUpdate = TIMESTAMP;
		$stampRegister = TIMESTAMP;
		$stampBook = $this->_getDbLogStampBook(array(
			'arrValue' => $arrValue,
		));

		$idLogCash = $varsRequest['query']['jsonValue']['idTarget'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];

		$idAccount = $varsLog['idAccount'];
		$flagIn = $arrValue['arr']['flagIn'];
		$flagPay = 0;
		$stampPay = 0;
		$strTitle = $arrValue['arr']['strTitle'];
		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrValue['arr']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));

		$flagApply = 1;
		$idAccountApply = $varsAccount['id'];
		$flagApplyBack = 0;
		$array = $classEscape->splitCommaArrayData(array('data' => $arrValue['arr']['arrCommaIdAccountPermit']));
		if (!count($array)) {
			$flagApply = 0;
			$arrValue['arr']['numSumMax'] = 0;
			$idAccountApply = null;
		}

		$arrPermitHistory = array(
			array(
				'flagInvalid'        => 0,
				'stampRegister'      => TIMESTAMP,
				'numSumMax'          => (int) $arrValue['arr']['numSumMax'],
				'idAccountApply'     => $idAccountApply,
				'arrIdAccountPermit' => $this->_getDbLogArrIdAccountPermit($arrValue),
			),
		);
		if (!$flagApply) {
			$arrPermitHistory = array();
		}
		$jsonPermitHistory = json_encode($arrPermitHistory);
		$this->checkTextSize(array(
			'flag' => 'errorDataMax',
			'str'  => $jsonPermitHistory,
		));
		$arrCommaIdAccountPermit = $this->_getDbLogArrCommaIdAccountPermit($arrPermitHistory);

		$arrCommaIdLogFile = $arrValue['arr']['arrCommaIdLogFile'];
		if ($arrValue['arr']['flagFiscalReport'] == 'none') {
			$arrValue['arr']['flagFiscalReport'] = '0';
		}
		$arrValue['arr']['stampBook'] = $stampBook;

		$classCalcLog = $this->_getClassCalc(array('flagType' => 'Log'));
		$varsVersion = $classCalcLog->allot(array(
			'flagStatus'      => 'varsVersion',
			'arrValue'        => $arrValue['arr'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrVersion = $varsVersion['arrVersion'][0];
		$arrVersion['jsonDetail']['numVersionConsumptionTax'] = count($varsLog['jsonVersion']);
		$arrVersion['flagIn'] = $flagIn;
		$arrVersion['jsonPermitHistory'] = $arrPermitHistory;
		$varsLog['jsonVersion'][] = $arrVersion;

		$jsonVersion = json_encode($varsLog['jsonVersion']);
		$this->checkTextSize(array(
			'flag' => 'errorDataMax',
			'str'  => $jsonVersion,
		));

		$numValue = $varsVersion['numValue'];

		$arrCommaIdDepartmentDebit = $varsVersion['arrCommaIdDepartmentDebit'];
		$arrCommaIdAccountTitleDebit = $varsVersion['arrCommaIdAccountTitleDebit'];
		$arrCommaIdSubAccountTitleDebit = $varsVersion['arrCommaIdSubAccountTitleDebit'];
		$arrCommaRateConsumptionTaxDebit = $varsVersion['arrCommaRateConsumptionTaxDebit'];
		$arrCommaConsumptionTaxDebit = $varsVersion['arrCommaConsumptionTaxDebit'];
		$arrCommaConsumptionTaxWithoutCalcDebit = $varsVersion['arrCommaConsumptionTaxWithoutCalcDebit'];
		$arrCommaTaxPaymentDebit = $varsVersion['arrCommaTaxPaymentDebit'];
		$arrCommaTaxReceiptDebit = $varsVersion['arrCommaTaxReceiptDebit'];

		$arrCommaIdDepartmentCredit = $varsVersion['arrCommaIdDepartmentCredit'];
		$arrCommaIdAccountTitleCredit = $varsVersion['arrCommaIdAccountTitleCredit'];
		$arrCommaIdSubAccountTitleCredit = $varsVersion['arrCommaIdSubAccountTitleCredit'];
		$arrCommaRateConsumptionTaxCredit = $varsVersion['arrCommaRateConsumptionTaxCredit'];
		$arrCommaConsumptionTaxCredit = $varsVersion['arrCommaConsumptionTaxCredit'];
		$arrCommaConsumptionTaxWithoutCalcCredit = $varsVersion['arrCommaConsumptionTaxWithoutCalcCredit'];
		$arrCommaTaxPaymentCredit = $varsVersion['arrCommaTaxPaymentCredit'];
		$arrCommaTaxReceiptCredit = $varsVersion['arrCommaTaxReceiptCredit'];

		$arrayTemp = compact(
			'stampUpdate',
			'stampBook',
			'flagIn',
			'strTitle',
			'arrSpaceStrTag',
			'stampPay',
			'flagPay',
			'flagApply',
			'idAccountApply',
			'arrCommaIdAccountPermit',
			'arrCommaIdLogFile',
			'jsonVersion',
			'numValue',
			'arrCommaIdDepartmentDebit',
			'arrCommaIdAccountTitleDebit',
			'arrCommaIdSubAccountTitleDebit',
			'arrCommaConsumptionTaxDebit',
			'arrCommaRateConsumptionTaxDebit',
			'arrCommaConsumptionTaxWithoutCalcDebit',
			'arrCommaTaxPaymentDebit',
			'arrCommaTaxReceiptDebit',
			'arrCommaIdDepartmentCredit',
			'arrCommaIdAccountTitleCredit',
			'arrCommaIdSubAccountTitleCredit',
			'arrCommaConsumptionTaxCredit',
			'arrCommaRateConsumptionTaxCredit',
			'arrCommaConsumptionTaxWithoutCalcCredit',
			'arrCommaTaxPaymentCredit',
			'arrCommaTaxReceiptCredit',
			'jsonPermitHistory'
		);
		$arrColumn = array();
		$arrValue = array();
		foreach ($arrayTemp as $keyTemp => $valueTemp) {
			$arrColumn[] = $keyTemp;
			$arrValue[] = $valueTemp;
		}

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable' => 'accountingLogCash',
			'arrColumn' => $arrColumn,
			'flagAnd'  => 1,
			'arrWhere'  => array(
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
					'strColumn'     => 'idLogCash',
					'flagCondition' => 'eq',
					'value'         => $idLogCash,
				),
			),
			'arrValue'  => $arrValue,
		));

		$varsLogUpdate = $this->_getVarsLog(array(
			'idTarget'        => $idLogCash,
			'numFiscalPeriod' => $numFiscalPeriod,
			'idEntity'        => $idEntity,
		));

		$numFiscalPeriodTemp = 0;
		$varsLogTemp = array();
		$varsLogTempUpdate = array();
		$varsLogTempAdd = array();
		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
			$numFiscalPeriodTemp = $numFiscalPeriod + 1;
			$arrColumn = array();
			$arrValue = array();
			foreach ($arrayTemp as $keyTemp => $valueTemp) {
				if ($keyTemp == 'numFiscalPeriod') {
					$valueTemp = $numFiscalPeriodTemp;
				}
				$arrColumn[] = $keyTemp;
				$arrValue[] = $valueTemp;
			}

			$varsLogTemp = $this->_getVarsLog(array(
				'idTarget'        => $idLogCash,
				'numFiscalPeriod' => $numFiscalPeriodTemp,
				'idEntity'        => $idEntity,
			));

			if ($varsLogTemp) {
				if ($stampBook > $arr['dataTerm']['stampMax']) {
					$classDb->updateRow(array(
						'idModule'  => 'accounting',
						'strTable' => 'accountingLogCash',
						'arrColumn' => $arrColumn,
						'flagAnd'  => 1,
						'arrWhere'  => array(
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
								'value'         => $numFiscalPeriodTemp,
							),
							array(
								'flagType'      => 'num',
								'strColumn'     => 'idLogCash',
								'flagCondition' => 'eq',
								'value'         => $idLogCash,
							),
						),
						'arrValue'  => $arrValue,
					));

				} else {
					$classDb->deleteRow(array(
						'idModule'  => 'accounting',
						'strTable' => 'accountingLogCash',
						'flagAnd'   => 1,
						'arrWhere'  => array(
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
								'value'         => $numFiscalPeriodTemp,
							),
							array(
								'flagType'      => 'num',
								'strColumn'     => 'idLogCash',
								'flagCondition' => 'eq',
								'value'         => $idLogCash,
							),
						),
					));
				}

			} else {
				if ($stampBook > $arr['dataTerm']['stampMax']) {
					$arrColumn = array();
					$arrValue = array();
					$arrayTemp = $varsLogUpdate;
					foreach ($arrayTemp as $keyTemp => $valueTemp) {
						if ($keyTemp == 'id') {
							continue;

						} elseif ($keyTemp == 'numFiscalPeriod') {
							$valueTemp = $numFiscalPeriodTemp;

						} elseif (preg_match("/^jsonWriteHistory$/", $keyTemp)) {
							$valueTemp = json_encode(array());

						} elseif (preg_match("/^json/", $keyTemp)) {
							if (!$valueTemp) {
								$valueTemp = array();
							}
							$valueTemp = json_encode($valueTemp);
						}
						$arrColumn[] = $keyTemp;
						$arrValue[] = $valueTemp;
					}
					$id = $classDb->insertRow(array(
						'idModule'  => 'accounting',
						'strTable'  => 'accountingLogCash',
						'arrColumn' => $arrColumn,
						'arrValue'  => $arrValue,
					));
					$varsLogTempAdd = $this->_getVarsLogId(array(
						'idTarget' => $id,
					));
					$idLogCash = $varsLogTempAdd['idLogCash'];
				}
			}

			$varsLogTempPrev = $this->_getVarsLog(array(
				'idTarget'        => $idLogCash,
				'numFiscalPeriod' => $numFiscalPeriodTemp,
				'idEntity'        => $idEntity,
			));

			if ($varsLogTempPrev) {
				$classCalcCash = $this->_getClassCalc(array('flagType' => 'Cash'));
				$flagErrorVars = $classCalcCash->allot(array(
					'flagStatus'       => 'UpdateVarsTax',
					'arrRows'          => array($varsLogTempPrev),
					'numFiscalPeriod'  => $numFiscalPeriodTemp,
					'idEntity'         => $idEntity,
				));

				if ($flagErrorVars['flag'] == 'textMaxOver') {
					$this->sendVars(array(
						'flag'    => 'errorDataMax',
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(),
					));
				}
				$varsLogTempUpdate = $this->_getVarsLog(array(
					'idTarget'        => $idLogCash,
					'numFiscalPeriod' => $numFiscalPeriodTemp,
					'idEntity'        => $idEntity,
				));
			}
		}

		$tempData = array(
			'varsLog'             => $arr['varsLog'],
			'varsLogUpdate'       => $varsLogUpdate,
			'flagPeriodOver'      => ($stampBook > $arr['dataTerm']['stampMax'])? 1 : 0,
			'varsLogTemp'         => $varsLogTemp,
			'varsLogTempUpdate'   => $varsLogTempUpdate,
			'varsLogTempAdd'      => $varsLogTempAdd,
			'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
		);

		return $tempData;
	}

	/**
		(array(
			'idTarget' => '',
		))
	 */
	protected function _getVarsLogId($arr)
	{
		global $classDb;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogCash',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'id',
					'flagCondition' => 'eq',
					'value'         => $arr['idTarget'],
				),
			),
		));

		if ($rows['numRows']) {
			return $rows['arrRows'][0];
		}

		return array();
	}
}
