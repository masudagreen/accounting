<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_LogHouseEditor extends Code_Else_Plugin_Accounting_Jpn_LogHouse
{
	protected $_childSelf = array(
		'pathTplJs'     => 'else/plugin/accounting/js/jpn/logHouseEditor.js',
		'pathTplJsTemp' => 'else/plugin/accounting/js/jpn/logHouseEditorTemp.js',
		'pathVarsJs'    => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/logHouseEditor.php',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;
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
		global $varsRequest;

		$pathTpl = $this->_childSelf['pathTplJs'];

		$this->_setJsEditor(array(
			'pathVars'  => $this->_childSelf['pathVarsJs'],
			'pathTpl'   => $pathTpl,
			'arrFolder' => array(
				array(
					'flagType'  => 'folder',
					'strTable'  => 'accountingAccountMemo',
					'strColumn' => 'jsonLogHouseEditorNaviFormat',
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
		$classCalcDictionary = $this->_getClassCalc(array('flagType' => 'Dictionary'));
		$vars = $classCalcDictionary->allot((array(
			'flagStatus' => 'data',
			'strTitle'   => $strTitle,
		)));

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
			'strColumn' => 'jsonLogHouseEditorNaviFormat',
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
			'strColumn'   => 'jsonLogHouseEditorNaviFormat',
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
		global $varsAccounts;

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllInsert'])) {
			$this->_sendOldFlag();
		}

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

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

		$vars['varsRule'] = $varsItem;

		$vars['portal']['varsDetail']['templateDetail'] = $this->_updateVarsTemplateDetail((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$varsTarget = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$varsTarget = $this->_updateVarsDetail(array(
			'vars' => $varsTarget,
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget
		));

		/*
		 * 20191001 start
		 */
		$classCalcConsumptionTax = $this->_getClassCalc(array('flagType' => 'ConsumptionTax'));
		$arrValue['arr']['jsonDetail'] = $classCalcConsumptionTax->allot(array(
		    'flagStatus' => 'receiveValueConsumptionTaxReduced',
		    'jsonDetail'   => $arrValue['arr']['jsonDetail'],
		));
		/*
		 * 20191001 end
		 */

		$stampBook = $varsItem['varsStampFiscalPeriod']['f1']['stampMax'];
		$arrValue['arr']['stampBook'] = $stampBook;
		$arrValue['arr']['flagFiscalReport'] = 'f1';

		$classCalcLog = $this->_getClassCalc(array('flagType' => 'Log'));
		$this->_checkValueDetail(array(
			'arrValue'     => $arrValue,
			'classCalcLog' => $classCalcLog,
			'vars'         => $vars,
		));

		$varsVersion = $classCalcLog->allot(array(
			'flagStatus'      => 'varsVersion',
			'arrValue'        => $arrValue['arr'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));
		$jsonVersion = $varsVersion['jsonVersion'];

		$stampRegister = TIMESTAMP;
		$stampUpdate = TIMESTAMP;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];

		$strTitle = $arrValue['arr']['strTitle'];
		$numRatio = $arrValue['arr']['numRatio'];

		$flagApply = 1;
		$idAccountApply = $varsAccounts[1]['id'];
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

		$arrCommaIdDepartmentDebit = $varsVersion['arrCommaIdDepartmentDebit'];
		$arrCommaIdAccountTitleDebit = $varsVersion['arrCommaIdAccountTitleDebit'];
		$arrCommaIdSubAccountTitleDebit = $varsVersion['arrCommaIdSubAccountTitleDebit'];
		$arrCommaConsumptionTaxDebit = $varsVersion['arrCommaConsumptionTaxDebit'];
		$arrCommaRateConsumptionTaxDebit = $varsVersion['arrCommaRateConsumptionTaxDebit'];
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

		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrValue['arr']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));
		try {
			$dbh->beginTransaction();

			$varsIdNumber = $this->_getIdAutoIncrement(array(
				'idTarget' => 'idLogHouse'
			));
			if (!$varsIdNumber[$idEntity]) {
				$varsIdNumber[$idEntity] = 1;
			}
			$idLogHouse = $varsIdNumber[$idEntity];

			$arrayTemp = compact(
				'stampRegister',
				'stampUpdate',
				'idEntity',
				'numFiscalPeriod',
				'idLogHouse',
				'strTitle',
				'numRatio',
				'flagApply',
				'idAccountApply',
				'arrCommaIdAccountPermit',
				'jsonVersion',
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
				'arrSpaceStrTag',
				'jsonPermitHistory'
			);

			$arrDbColumn = array();
			$arrDbValue = array();
			foreach ($arrayTemp as $keyTemp => $valueTemp) {
				$arrDbColumn[] = $keyTemp;
				$arrDbValue[] = $valueTemp;
			}

			$id = $classDb->insertRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingLogHouse' . $strNation,
				'arrColumn' => $arrDbColumn,
				'arrValue'  => $arrDbValue,
			));

			$varsIdNumber[$idEntity]++;
			$this->_updateIdAutoIncrement(array(
				'idTarget'   => 'idLogHouse',
				'varsTarget' => $varsIdNumber
			));

			$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
			if (preg_match("/^temp/", $flagCurrentFlagNow)) {
				if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
					$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] + 1;

				} elseif (preg_match("/^(tempNext)$/", $flagCurrentFlagNow)) {
					$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] - 1;
				}

				$array = $arrDbColumn;
				foreach ($array as $key => $value) {
					if ($value == 'numFiscalPeriod') {
						$arrDbValue[$key] = $numFiscalPeriodTemp;
						break;
					}
				}

				$id = $classDb->insertRow(array(
					'idModule'  => 'accounting',
					'strTable'  => 'accountingLogHouse' . $strNation,
					'arrColumn' => $arrDbColumn,
					'arrValue'  => $arrDbValue,
				));

				$arrRows = $this->_getVarsLog(array(
					'idLogHouse'     => $idLogHouse,
					'numFiscalPeriod' => $numFiscalPeriodTemp,
					'idEntity'        => $idEntity,
				));

				$classCalcLogHouse = $this->_getClassCalc(array('flagType' => 'LogHouse'));

				$flagErrorVars = $classCalcLogHouse->allot(array(
					'flagStatus'       => 'UpdateVarsTax',
					'arrRows'          => $arrRows,
					'numFiscalPeriod'  => $numFiscalPeriodTemp,
					'idEntity'         => $idEntity,
				));

				if ($flagErrorVars) {
					$this->sendVars(array(
						'flag'    => 'errorDataMax',
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(),
					));
				}
			}

			$array = array('logHouse');
			foreach ($array as $key => $value) {
				$this->_updateDbPreferenceStamp(array('strColumn' => $value));
			}

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

		$flag = $this->_checkValueDetailJournal($arr);
		if ($flag) {
			$this->_sendOldError();
		}

		$numRatio = $arr['arrValue']['arr']['numRatio'];
		if (!preg_match("/^[0-9]{1,3}\.[0-9]{2,2}$/", $numRatio) || $numRatio > 100) {
			$this->_sendOldError();
		}

		$classCalcLog = &$arr['classCalcLog'];
		$varsOrder = array(
			'numFiscalPeriod'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'                => $varsPluginAccountingAccount['idEntityCurrent'],
			'idAccount'               => '',
			'idAccountApply'          => '',
			'flagFiscalReport'        => '',
			'stampBook'               => '',
			'strTitle'                => '',
			'jsonDetail'              => '',
			'arrCommaIdLogFile'       => '',
			'arrCommaIdAccountPermit' => $arr['arrValue']['arr']['arrCommaIdAccountPermit'],
			'numSumMax'               => $arr['arrValue']['arr']['numSumMax'],
			'arrSpaceStrTag'          => '',
		);

		$flag = $classCalcLog->allot(array(
			'flagStatus'      => 'check',
			'varsOrder'       => $varsOrder,
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagCheck'       => 'Permit',
			'varsItem'        => array('dummy'),
		));

		if ($flag) {
			if (FLAG_TEST) {
				var_dump($flag);
				exit;
			}
			$this->_sendOldError();
		}
	}

	/**

	 */
	protected function _checkValueDetailJournal($arr)
	{
		global $classEscape;
		global $classCheck;
		global $classTime;

		$arrNum = array(
			'Debit' => 0,
			'Credit' => 0,
		);

		$stampBook = $arr['arrValue']['arr']['stampBook'];
		$numRate = $classTime->checkRateConsumptionTax(array('stamp' => $stampBook));

		$flag = 0;
		$array = $arr['arrValue']['arr']['jsonDetail']['varsDetail'];
		$flagConsumptionTaxFree = (int) $arr['vars']['varsRule']['varsEntityNation']['flagConsumptionTaxFree'];
		$flagConsumptionTaxIncluding = (int) $arr['vars']['varsRule']['varsEntityNation']['flagConsumptionTaxIncluding'];

		foreach ($array as $key => $value) {
			$arrayStr = array('Debit', 'Credit');
			foreach ($arrayStr as $keyStr => $valueStr) {
				$strSide = 'arr' . $valueStr;
				$idAccountTitle = $value[$strSide]['idAccountTitle'];
				if ($idAccountTitle) {
					if (!$arr['vars']['varsRule']['arrAccountTitle']['arrStrTitle'][$idAccountTitle]) {
						$flag = 'idAccountTitle' . $idAccountTitle;
						break;
					}
					if ($valueStr == 'Debit') {
						if ($idAccountTitle != 'accountsReceivables') {
							$flag = 'idAccountTitle' . $valueStr;
							break;
						}
					} else {
						if (!$arr['vars']['varsRule']['arrAccountTitleCost']['arrStrTitle'][$idAccountTitle]) {
							$flag = 'idAccountTitle' . $valueStr;
							break;
						}
					}

					if ($value[$strSide]['numValueConsumptionTax'] != '') {
						$flagCheck = $classCheck->checkValueWord(array(
							'flagType' => 'num',
							'value'    => $value[$strSide]['numValueConsumptionTax']
						));
						if ($flagCheck) {
							$flag = 'numValueConsumptionTax';
							break;
						}
					}

					$flagRate = 0;
					$flagRateTax = 0;
					if ($value[$strSide]['numRateConsumptionTax'] != '') {
						$flagRate = 1;
						/*
						 * 20191001 start
						 */
						if ($value[$strSide]['flagRateConsumptionTaxReduced'] == 1) {
						    if ($value[$strSide]['numRateConsumptionTax'] != 8) {
						        $flag = 'numRateConsumptionTax';
						        break;
						    }
						}
						if (!preg_match("/^(5|8|10)$/", $value[$strSide]['numRateConsumptionTax'])) {
						//if (!preg_match("/^(5|8)$/", $value[$strSide]['numRateConsumptionTax'])) {
						/*
						 * 20191001 end
						*/
							$flag = 'numRateConsumptionTax';
							break;

						} else {
						    if ($numRate == 8) {
								if ($value[$strSide]['numRateConsumptionTax'] == 10
    								/*
    								 * 20191001 start
    								 */
								    || $value[$strSide]['flagRateConsumptionTaxReduced'] == 1
								    /*
								     * 20191001 start
								     */

								) {
									$flag = 'numRateConsumptionTax';
									break;
								}

							} elseif ($numRate == 5) {
								if ($value[$strSide]['numRateConsumptionTax'] == 8
									|| $value[$strSide]['numRateConsumptionTax'] == 10
									/*
									 * 20191001 start
									 */
								    || $value[$strSide]['flagRateConsumptionTaxReduced'] == 1
								    /*
								     * 20191001 start
								     */
								) {
									$flag = 'numRateConsumptionTax';
									break;
								}
							}
						}
					}

					$idSubAccountTitle = $value[$strSide]['idSubAccountTitle'];
					if ($idSubAccountTitle) {
						if (!$arr['vars']['varsRule']['arrSubAccountTitle']['arrStrTitle'][$idAccountTitle][$idSubAccountTitle]) {
							$flag = 'idSubAccountTitle';
							break;
						}
					}
					$idDepartment = $value[$strSide]['idDepartment'];
					if ($idDepartment) {
						if (!$arr['vars']['varsRule']['arrDepartment']['arrStrTitle'][$idDepartment]) {
							$flag = 'idDepartment';
							break;
						}
					}
					if ($flagConsumptionTaxFree != $value[$strSide]['flagConsumptionTaxFree']) {
						$flag = 'flagConsumptionTaxFree';
						break;
					}
					if ($flagConsumptionTaxIncluding != $value[$strSide]['flagConsumptionTaxIncluding']) {
						$flag = 'flagConsumptionTaxIncluding';
						break;
					}

					$arrNum[$valueStr] += $value[$strSide]['numValue'];

					$flagTax = 0;

					$flagConsumptionTaxGeneralRuleEach = $value[$strSide]['flagConsumptionTaxGeneralRuleEach'];
					if ($flagConsumptionTaxGeneralRuleEach) {
						if (!$arr['vars']['varsRule']['varsConsumptionTax']['arrStrGeneralEach'][$flagConsumptionTaxGeneralRuleEach]) {
							$flag = 'flagConsumptionTaxGeneralRuleEach';
							break;
						}
						if ((int) $arr['vars']['varsRule']['varsEntityNation']['flagConsumptionTaxGeneralRule']
							&& (int) $arr['vars']['varsRule']['varsEntityNation']['flagConsumptionTaxDeducted']
							&& preg_match( "/^tax/", $flagConsumptionTaxGeneralRuleEach)
						) {
							$flagTax = 1;
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

					$flagConsumptionTaxGeneralRuleProration = $value[$strSide]['flagConsumptionTaxGeneralRuleProration'];
					if ($flagConsumptionTaxGeneralRuleProration) {
						if (!$arr['vars']['varsRule']['varsConsumptionTax']['arrStrGeneralProration'][$flagConsumptionTaxGeneralRuleProration]) {
							$flag = 'flagConsumptionTaxGeneralRuleProration';
							break;
						}
						if ((int) $arr['vars']['varsRule']['varsEntityNation']['flagConsumptionTaxGeneralRule']
							&& !(int) $arr['vars']['varsRule']['varsEntityNation']['flagConsumptionTaxDeducted']
							&& preg_match( "/^tax/", $flagConsumptionTaxGeneralRuleProration)
						) {
							$flagTax = 1;
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

					$flagConsumptionTaxSimpleRule = $value[$strSide]['flagConsumptionTaxSimpleRule'];
					if ($flagConsumptionTaxSimpleRule) {
						if (!$arr['vars']['varsRule']['varsConsumptionTax']['arrStrSimple'][$flagConsumptionTaxSimpleRule]) {
							$flag = 'flagConsumptionTaxSimpleRule';
							break;
						}
						if (!(int) $arr['vars']['varsRule']['varsEntityNation']['flagConsumptionTaxGeneralRule'] &&
							preg_match( "/^tax/", $value[$strSide]['flagConsumptionTaxSimpleRule'])
						) {
							$flagTax = 1;
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
							$flag = 'flagConsumptionTaxWithoutCalc';
							break;
						}
					}

					$flagConsumptionTaxCalc = $value[$strSide]['flagConsumptionTaxCalc'];
					if ($flagConsumptionTaxCalc) {
						if (!($flagConsumptionTaxCalc == 1
							|| $flagConsumptionTaxCalc == 2
							|| $flagConsumptionTaxCalc == 3
						)) {
							$flag = 'flagConsumptionTaxCalc';
							break;
						}
					}

					if ($flagTax) {
						if ($flagConsumptionTaxWithoutCalc == 2) {
							$numValue = $value[$strSide]['numValue'];
							$numValueConsumptionTax = $value[$strSide]['numValueConsumptionTax'];
							if ($numValue < $numValueConsumptionTax) {
								$flag = 'numValueConsumptionTax';
								break;
							}
							$arrNum[$valueStr] += $numValueConsumptionTax;
						}
					}
					if ($flagRate) {
						if (!$flagRateTax) {
							$flag = 'numRateConsumptionTax';
							break;
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
			return $flag;
		}

		if (count($array) > 1) {
			$flag = __LINE__;
		}

		if ($flag) {
			return $flag;
		}
	}

	/**
	 *
	 */
	protected function _iniDetailEdit()
	{
		global $classDb;
		global $classEscape;

		global $varsRequest;
		$dbh = $classDb->getHandle();
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;
		global $varsAccounts;

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'])) {
			$this->_sendOldFlag();
		}

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

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

		$vars['varsRule'] = $varsItem;

		$vars['portal']['varsDetail']['templateDetail'] = $this->_updateVarsTemplateDetail((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$varsTarget = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$varsTarget = $this->_updateVarsDetail(array(
			'vars' => $varsTarget,
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget,
		));

		/*
		 * 20191001 start
		 */
		$classCalcConsumptionTax = $this->_getClassCalc(array('flagType' => 'ConsumptionTax'));
		$arrValue['arr']['jsonDetail'] = $classCalcConsumptionTax->allot(array(
		    'flagStatus' => 'receiveValueConsumptionTaxReduced',
		    'jsonDetail'   => $arrValue['arr']['jsonDetail'],
		));
		/*
		 * 20191001 end
		 */

		$stampBook = $varsItem['varsStampFiscalPeriod']['f1']['stampMax'];
		$arrValue['arr']['stampBook'] = $stampBook;
		$arrValue['arr']['flagFiscalReport'] = 'f1';
		$classCalcLog = $this->_getClassCalc(array('flagType' => 'Log'));

		$this->_checkValueDetail(array(
			'arrValue'     => $arrValue,
			'classCalcLog' => $classCalcLog,
			'vars'         => $vars,
		));

		$varsVersion = $classCalcLog->allot(array(
			'flagStatus'      => 'varsVersion',
			'arrValue'        => $arrValue['arr'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));
		$jsonVersion = $varsVersion['jsonVersion'];

		$varsTarget = $this->_getVarsTarget(array(
			'idTarget'        => $idTarget,
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));
		if (!$varsTarget) {
			$this->_sendOldError();
		}

		$flagApply = 1;
		$idAccountApply = $varsAccounts[1]['id'];
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

		$strTitle = $arrValue['arr']['strTitle'];
		$numRatio = $arrValue['arr']['numRatio'];

		$arrCommaIdDepartmentDebit = $varsVersion['arrCommaIdDepartmentDebit'];
		$arrCommaIdAccountTitleDebit = $varsVersion['arrCommaIdAccountTitleDebit'];
		$arrCommaIdSubAccountTitleDebit = $varsVersion['arrCommaIdSubAccountTitleDebit'];
		$arrCommaConsumptionTaxDebit = $varsVersion['arrCommaConsumptionTaxDebit'];
		$arrCommaRateConsumptionTaxDebit = $varsVersion['arrCommaRateConsumptionTaxDebit'];
		$arrCommaConsumptionTaxWithoutCalcDebit = $varsVersion['arrCommaConsumptionTaxWithoutCalcDebit'];
		$arrCommaTaxPaymentDebit = $varsVersion['arrCommaTaxPaymentDebit'];
		$arrCommaTaxReceiptDebit = $varsVersion['arrCommaTaxReceiptDebit'];

		$arrCommaIdDepartmentCredit = $varsVersion['arrCommaIdDepartmentCredit'];
		$arrCommaIdAccountTitleCredit = $varsVersion['arrCommaIdAccountTitleCredit'];
		$arrCommaIdSubAccountTitleCredit = $varsVersion['arrCommaIdSubAccountTitleCredit'];
		$arrCommaConsumptionTaxCredit = $varsVersion['arrCommaConsumptionTaxCredit'];
		$arrCommaRateConsumptionTaxCredit = $varsVersion['arrCommaRateConsumptionTaxCredit'];
		$arrCommaConsumptionTaxWithoutCalcCredit = $varsVersion['arrCommaConsumptionTaxWithoutCalcCredit'];
		$arrCommaTaxPaymentCredit = $varsVersion['arrCommaTaxPaymentCredit'];
		$arrCommaTaxReceiptCredit = $varsVersion['arrCommaTaxReceiptCredit'];

		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrValue['arr']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));

		$arrayTemp = compact(
			'strTitle',
			'numRatio',
			'flagApply',
			'idAccountApply',
			'arrCommaIdAccountPermit',
			'jsonVersion',
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
			'arrSpaceStrTag',
			'jsonPermitHistory'
		);
		$arrDbColumn = array();
		$arrDbValue = array();
		foreach ($arrayTemp as $keyTemp => $valueTemp) {
			$arrDbColumn[] = $keyTemp;
			$arrDbValue[] = $valueTemp;
		}

		try {
			$dbh->beginTransaction();

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingLogHouse' . $strNation,
				'arrColumn' => $arrDbColumn,
				'flagAnd'  => 1,
				'arrWhere'  => array(
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
						'strColumn'     => 'idLogHouse',
						'flagCondition' => 'eq',
						'value'         => $idTarget,
					),
				),
				'arrValue'  => $arrDbValue,
			));

			$this->_updateDbPreferenceStamp(array('strColumn' => 'logHouse'));

			$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
			if (preg_match("/^temp/", $flagCurrentFlagNow)) {
				if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
					$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] + 1;

				} elseif (preg_match("/^(tempNext)$/", $flagCurrentFlagNow)) {
					$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] - 1;
				}
				$classDb->updateRow(array(
					'idModule'  => 'accounting',
					'strTable' => 'accountingLogHouse' . $strNation,
					'arrColumn' => $arrDbColumn,
					'flagAnd'  => 1,
					'arrWhere'  => array(
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
							'value'         => $numFiscalPeriodTemp,
						),
						array(
							'flagType'      => '',
							'strColumn'     => 'idLogHouse',
							'flagCondition' => 'eq',
							'value'         => $idTarget,
						),
					),
					'arrValue'  => $arrDbValue,
				));

				$arrRows = $this->_getVarsLog(array(
					'idLogHouse'      => $idTarget,
					'numFiscalPeriod' => $numFiscalPeriodTemp,
					'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
				));

				$classCalcLogHouse = $this->_getClassCalc(array('flagType' => 'LogHouse'));

				$flagErrorVars = $classCalcLogHouse->allot(array(
					'flagStatus'       => 'UpdateVarsTax',
					'arrRows'          => $arrRows,
					'numFiscalPeriod'  => $numFiscalPeriodTemp,
					'idEntity'         => $varsPluginAccountingAccount['idEntityCurrent'],
				));

				if ($flagErrorVars) {
					$this->sendVars(array(
						'flag'    => 'errorDataMax',
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(),
					));
				}
			}

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
	 *
	 */
	protected function _getVarsTarget($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogHouse' . $strNation,
			'arrLimit' => array(
				'numStart' => 0, 'numEnd' => 1,
			),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere'  => array(
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
					'strColumn'     => 'idLogHouse',
					'flagCondition' => 'eq',
					'value'         => $arr['idTarget'],
				),
			),
		));

		$data = $rows['arrRows'][0];

		if (!$data) {
			$data = array();
		}

		return $data;
	}
}
