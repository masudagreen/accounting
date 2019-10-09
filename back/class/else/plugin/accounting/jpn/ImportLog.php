<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_ImportLog extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference' => 'importLogWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/importLog.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/importLog.php',
		'varsIframe'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/tmplIframe.php',
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
			'vars'     => $vars,
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars'             => &$vars,
			'varsItem'         => $varsItem,
			'varsEntityNation' => $varsEntityNation,
		)));

		$vars = $this->_updateVars(array(
			'vars'             => $vars,
			'varsEntityNation' => $varsEntityNation,
			'varsItem'         => $varsItem,
		));

		if (!$this->_checkCurrent()) {
			$vars['portal']['varsNavi']['varsBtn'] = array();
		}

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
		))
	 */
	protected function _getVarsItem($arr)
	{
		global $classDb;
		global $varsAccount;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$data = array(
			'varsEntityNation' => $varsEntityNation,
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
		$varsEntityNation = &$arr['varsEntityNation'];
		$varsItem = &$arr['varsItem'];

		$arrayNew = array();
		$array = &$vars['portal']['varsNavi']['templateDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'DummyEditCurrent') {
				if ($this->_checkCurrent()) {
					continue;
				}
			}
			$arrayNew[] = $value;
		}

		$vars['portal']['varsNavi']['templateDetail'] = $arrayNew;

		return $vars['portal']['varsNavi']['templateDetail'];
	}

	/**
		(array(
			'vars'             => $vars,
			'varsEntityNation' => $varsEntityNation,
			'varsItem'         => $varsItem,
		))
	 */
	protected function _updateVars($arr)
	{
		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllInsert'])) {
			$arr['vars']['portal']['varsNavi']['varsBtn'] = array();
		}

		return $arr['vars'];
	}

	/**
	 *
	 */
	protected function _iniNaviAdd()
	{
		global $classDb;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		global $varsRequest;
		$dbh = $classDb->getHandle();

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'insert',
			'idTarget'      => $this->_extSelf['idPreference'],
		));

		if (!$flag) {
			$this->_sendMessage(array('flag' => 40));
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$vars = $this->_updateVarsRule(array(
			'vars' => $vars,
		));
		$vars = $this->_updateVarsCheck(array(
			'vars' => $vars,
		));

		$arrValueFile = $this->_checkFileValue(array(
			'vars' => $vars,
		));

		$this->_checkValueElse(array(
			'vars' => $vars,
		));

		$data = $this->_checkVarsCSV(array(
			'arrValueFile' => $arrValueFile,
			'vars'         => $vars,
		));

		try {
			$dbh->beginTransaction();

			$arrVarsLog = array();
			$arrayLog = $data['arrayRequests'];
			foreach ($arrayLog as $keyLog => $valueLog) {
				$dataLog = $this->_setDbLog($vars, $valueLog, $data['strUrl']);
				$arrVarsLog[] = $dataLog;
			}

			$arrRows = $this->_getVarsLogCalcLoop(array(
				'arrVarsLog'      => $arrVarsLog,
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			));

			$classCalcAccountTitle = $this->_getClassCalc(array('flagType' => 'AccountTitle'));
			$classCalcSubAccountTitle = $this->_getClassCalc(array('flagType' => 'SubAccountTitle'));
			$classCalcConsumptionTax = $this->_getClassCalc(array('flagType' => 'ConsumptionTax'));
			$classCalcLogCalc = $this->_getClassCalc(array('flagType' => 'LogCalc'));
			$classCalcTempNextLog = $this->_getClassCalc(array(
				'flagType'   => 'TempNext',
				'flagDetail' => 'Log',
			));

			$flag = $classCalcAccountTitle->allot(array(
				'flagStatus'      => 'add',
				'arrRows'         => $arrRows,
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			));
			if ($flag == 'errorDataMax') {
				$this->sendVars(array(
					'flag'    => $flag,
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));
			}

			$flag = $classCalcSubAccountTitle->allot(array(
				'flagStatus'      => 'add',
				'arrRows'         => $arrRows,
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			));
			if ($flag == 'errorDataMax') {
				$this->sendVars(array(
					'flag'    => $flag,
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));
			}

			$flag = $classCalcConsumptionTax->allot(array(
				'flagStatus'      => 'add',
				'arrRows'         => $arrRows,
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			));
			if ($flag == 'errorDataMax') {
				$this->sendVars(array(
					'flag'    => $flag,
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));
			}

			$classCalcLogCalc->allot(array(
				'flagStatus'      => 'add',
				'arrRows'         => $arrRows,
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			));

			$flagCurrentFlagNow = $this->_getCurrentFlagNow();
			$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
			$numFiscalPeriod = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];

			if ((preg_match("/^(tempPrev)$/", $flagCurrentFlagNow))) {
				$flag = $classCalcTempNextLog->allot(array(
					'flagStatus'      => 'add',
					'numFiscalPeriod' => $numFiscalPeriod,
					'arrRows'         => $arrRows,
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
			$this->_updateDbPreferenceStamp(array('strColumn' => 'log'));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		unlink($data['strUrl']);
		$this->_sendMessage(array('flag' => 1));
	}

	/**

	 */
	protected function _setDbLog($vars, $arrValue, $strUrl)
	{
		global $classEscape;
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsAccount;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$varsComment = $vars['varsItem']['varsComment'];
		$strStatus = $varsComment['strStatus'];
		$strStatus = str_replace("<%replace%>", $arrValue['id'], $strStatus);

		$tm = TIMESTAMP;
		$stampRegister = $tm;
		$stampUpdate = $tm;
		$stampBook = $arrValue['stampBook'];
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = (int) $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$idAccount = $arrValue['idAccount'];
		$flagFiscalReport = $arrValue['flagFiscalReport'];
		$strTitle = $arrValue['strTitle'];
		$arrSpaceStrTag = $arrValue['jsonDetail']['arrSpaceStrTag'];
		$arrCommaIdLogFile = $arrValue['jsonDetail']['arrCommaIdLogFile'];
		$arrCommaIdAccountPermit = $arrValue['jsonDetail']['arrCommaIdAccountPermit'];
		$jsonPermitHistory = json_encode(array());
		$flagApply = 0;
		$flagApplyBack = 0;
		$idAccountApply = null;
		$varsVersion = $this->_getDbLogVarsVersion($vars, $arrValue, $stampBook);
		$jsonVersion = $varsVersion['jsonVersion'];

		$numValue = $varsVersion['numValue'];
		$arrCommaIdDepartment = $varsVersion['arrCommaIdDepartment'];
		$arrCommaIdAccountTitle = $varsVersion['arrCommaIdAccountTitle'];
		$arrCommaIdSubAccountTitle = $varsVersion['arrCommaIdSubAccountTitle'];

		$arrCommaIdDepartmentVersion = $varsVersion['arrCommaIdDepartment'];
		$arrCommaIdAccountTitleVersion = $varsVersion['arrCommaIdAccountTitle'];
		$arrCommaIdSubAccountTitleVersion = $varsVersion['arrCommaIdSubAccountTitle'];

		$arrChargeHistory = array(
			array(
				'stampRegister' => $tm,
				'idAccount'     => $idAccount,
			),
		);
		$jsonChargeHistory = json_encode($arrChargeHistory);

		$varsIdNumber = $this->_getIdAutoIncrement(array(
			'idTarget' => 'idLog'
		));
		if (!$varsIdNumber[$idEntity][$numFiscalPeriod]) {
			$varsIdNumber[$idEntity][$numFiscalPeriod] = 1;
		}
		$idLog = $varsIdNumber[$idEntity][$numFiscalPeriod];

		$arrColumn = array('stampRegister', 'stampUpdate', 'stampBook', 'idLog', 'idEntity', 'numFiscalPeriod', 'idAccount', 'flagFiscalReport', 'strTitle', 'arrSpaceStrTag', 'flagApply', 'idAccountApply', 'flagApplyBack', 'arrCommaIdAccountPermit', 'arrCommaIdLogFile', 'jsonVersion', 'numValue', 'arrCommaIdDepartment', 'arrCommaIdAccountTitle', 'arrCommaIdSubAccountTitle', 'arrCommaIdDepartmentVersion', 'arrCommaIdAccountTitleVersion', 'arrCommaIdSubAccountTitleVersion', 'jsonChargeHistory', 'jsonPermitHistory');
		$arrValue = array($stampRegister, $stampUpdate, $stampBook, $idLog, $idEntity, $numFiscalPeriod, $idAccount, $flagFiscalReport, $strTitle, $arrSpaceStrTag, $flagApply, $idAccountApply, $flagApplyBack, $arrCommaIdAccountPermit, $arrCommaIdLogFile, $jsonVersion, $numValue, $arrCommaIdDepartment, $arrCommaIdAccountTitle, $arrCommaIdSubAccountTitle, $arrCommaIdDepartmentVersion, $arrCommaIdAccountTitleVersion, $arrCommaIdSubAccountTitleVersion, $jsonChargeHistory, $jsonPermitHistory);

		$id = $classDb->insertRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingLog',
			'arrColumn' => $arrColumn,
			'arrValue'  => $arrValue,
		));

		$varsIdNumber[$idEntity][$numFiscalPeriod]++;
		$this->_updateIdAutoIncrement(array(
			'idTarget'   => 'idLog',
			'varsTarget' => $varsIdNumber
		));

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLog',
			'arrLimit' => array(
				'numStart' => 0, 'numEnd' => 1,
			),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'id',
					'flagCondition' => 'eq',
					'value'         => $id,
				),
			),
		));
		$varsLog = $rows['arrRows'][0];

		return $varsLog;
	}


	/**

	 */
	protected function _getDbLogVarsVersion($vars, $arrValue, $stampBook)
	{
		global $classEscape;

		global $varsAccount;
		global $varsPluginAccountingAccount;

		$arrSpaceStrTag = $arrValue['jsonDetail']['arrSpaceStrTag'];

		$varsEntityNation = $vars['varsRule']['varsEntityNation'];

		$varsDetail = $arrValue['jsonDetail']['varsDetail'];
		$varsVersion = $this->_getDbLogVarsVersionDetail($vars, $arrValue);

		$arrVersion = array(
			array(
				'stampRegister'     => TIMESTAMP,
				'stampUpdate'       => TIMESTAMP,
				'stampBook'         => $stampBook,
				'strTitle'          => $arrValue['strTitle'],
				'flagFiscalReport'  => $arrValue['flagFiscalReport'],
				'arrSpaceStrTag'    => $arrSpaceStrTag,
				'arrCommaIdLogFile' => $arrValue['jsonDetail']['arrCommaIdLogFile'],
				'jsonDetail' => array(
					'idAccountTitleDebit'  => $varsVersion['idAccountTitleDebit'],
					'idAccountTitleCredit' => $varsVersion['idAccountTitleCredit'],
					'numSum'               => $arrValue['jsonDetail']['numSumDebit'],
					'numSumDebit'          => $arrValue['jsonDetail']['numSumDebit'],
					'numSumCredit'         => $arrValue['jsonDetail']['numSumCredit'],
					'varsEntityNation'     => array(
						'numConsumptionTax'              => $varsEntityNation['numConsumptionTax'],
						'flagConsumptionTaxFree'         => $varsEntityNation['flagConsumptionTaxFree'],
						'flagConsumptionTaxGeneralRule'  => $varsEntityNation['flagConsumptionTaxGeneralRule'],
						'flagConsumptionTaxDeducted'     => $varsEntityNation['flagConsumptionTaxDeducted'],
						'flagConsumptionTaxIncluding'    => $varsEntityNation['flagConsumptionTaxIncluding'],
						/*journal.js insert
						'flagConsumptionTaxCalc'         => $varsEntityNation['flagConsumptionTaxCalc'],
						'flagConsumptionTaxWithoutCalc'  => $varsEntityNation['flagConsumptionTaxWithoutCalc'],
						'flagConsumptionTaxBusinessType' => $varsEntityNation['flagConsumptionTaxBusinessType'],
						*/
					),
					'varsDetail'               => $varsVersion['varsDetail'],
					'numVersionConsumptionTax' => count($varsVersion['varsDetail']) - 1,
				),
			),
		);

		$data = array(
			'idAccountTitleDebit'       => $varsVersion['idAccountTitleDebit'],
			'idAccountTitleCredit'      => $varsVersion['idAccountTitleCredit'],
			'jsonVersion'               => json_encode($arrVersion),
			'arrVersion'                => $arrVersion,
			'varsDetail'                => $varsVersion['varsDetail'],
			'numValue'                  => $arrValue['jsonDetail']['numSumDebit'],
			'arrCommaIdDepartment'      => $varsVersion['arrCommaIdDepartment'],
			'arrCommaIdAccountTitle'    => $varsVersion['arrCommaIdAccountTitle'],
			'arrCommaIdSubAccountTitle' => $varsVersion['arrCommaIdSubAccountTitle'],
		);

		return $data;
	}

	/**

	 */
	protected function _updateVarsOmit($arr)
	{
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
			$varsDetail = $arr['varsDetail'];
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

		return $arrDetail;
	}

	/**

	 */
	protected function _getDbLogVarsVersionDetail($vars, $arrValue)
	{
		global $classEscape;

		$varsEntityNation = $vars['varsRule']['varsEntityNation'];

		$arrayIdDepartment = array();
		$arrayIdAccountTitle = array();
		$arrayIdSubAccountTitle = array();
		$arrayDetail = array();
		$arraySide = array(
			'arrDebit' => array(),
			'arrCredit' => array(),
		);
		$array = $this->_updateVarsOmit(array(
			'arrDetail'  => $arrValue['jsonDetail']['varsDetail'],
			'varsDetail' => $vars['varsItem']['varsDetail'],
		));
		$num = 0;
		foreach ($array as $key => $value) {
			$arrayNew = array();
			$arrayNew['id'] = $num;
			$arrayNew['flagFoldNow'] = 0;
			$arrayStr = array('Debit', 'Credit');
			foreach ($arrayStr as $keyStr => $valueStr) {
				$strSide = 'arr' . $valueStr;
				$idAccountTitle = ($value[$strSide]['idAccountTitle'])? $value[$strSide]['idAccountTitle'] : '';
				$numValue = '';
				$numValueConsumptionTax = '';
				$idDepartment = '';
				$idSubAccountTitle = '';
				$flagConsumptionTaxGeneralRuleEach = '';
				$flagConsumptionTaxGeneralRuleProration = '';
				$flagConsumptionTaxSimpleRule = '';
				$flagConsumptionTaxWithoutCalc = '';
				$flagConsumptionTaxCalc = '';
				$flagConsumptionTaxIncluding = '';
				$flagConsumptionTaxFree = '';
				if ($idAccountTitle) {
					$arraySide[$strSide][] = $idAccountTitle;

					$numValue = $value[$strSide]['numValue'];
					$numValueConsumptionTax = $value[$strSide]['numValueConsumptionTax'];
					$idDepartment = ($value[$strSide]['idDepartment'])? $value[$strSide]['idDepartment'] : '';
					$idSubAccountTitle = ($value[$strSide]['idSubAccountTitle'])? $value[$strSide]['idSubAccountTitle'] : '';
					$flagConsumptionTaxGeneralRuleEach = ($value[$strSide]['flagConsumptionTaxGeneralRuleEach'])? $value[$strSide]['flagConsumptionTaxGeneralRuleEach'] : '';
					$flagConsumptionTaxGeneralRuleProration = ($value[$strSide]['flagConsumptionTaxGeneralRuleProration'])? $value[$strSide]['flagConsumptionTaxGeneralRuleProration'] : '';
					$flagConsumptionTaxSimpleRule = ($value[$strSide]['flagConsumptionTaxSimpleRule'])? $value[$strSide]['flagConsumptionTaxSimpleRule'] : '';
					$flagConsumptionTaxWithoutCalc = ($value[$strSide]['flagConsumptionTaxWithoutCalc'] != '')? $value[$strSide]['flagConsumptionTaxWithoutCalc'] : '';
					$flagConsumptionTaxCalc = ($value[$strSide]['flagConsumptionTaxCalc'] != '')? $value[$strSide]['flagConsumptionTaxCalc'] : '';
					$flagConsumptionTaxIncluding = ($value[$strSide]['flagConsumptionTaxIncluding'] != '')? $value[$strSide]['flagConsumptionTaxIncluding'] : '';
					$flagConsumptionTaxFree = ($value[$strSide]['flagConsumptionTaxFree'] != '')? $value[$strSide]['flagConsumptionTaxFree'] : '';

					if ($idAccountTitle == 'suspenseReceiptOfConsumptionTaxes' || $idAccountTitle == 'suspensePaymentConsumptionTaxes') {
						$flagConsumptionTaxWithoutCalc = 3;
					}

					if (!(int) $varsEntityNation['flagConsumptionTaxFree']
						 && !(int) $varsEntityNation['flagConsumptionTaxIncluding']
						 && $idAccountTitle != 'suspenseReceiptOfConsumptionTaxes'
						 && $idAccountTitle != 'suspensePaymentConsumptionTaxes'
					) {
						if ((int) $varsEntityNation['flagConsumptionTaxGeneralRule']) {
							if ((int) $varsEntityNation['flagConsumptionTaxDeducted']) {
								if (preg_match("/^tax/", $flagConsumptionTaxGeneralRuleEach)) {
									$flagTax = 1;
								}

							} else {
								if (preg_match("/^tax/", $flagConsumptionTaxGeneralRuleProration)) {
									$flagTax = 1;
								}
							}
						}

						if (preg_match("/^tax/", $flagConsumptionTaxSimpleRule)
							&& !(int) $varsEntityNation['flagConsumptionTaxGeneralRule']
						) {
							$flagTax = 1;
						}
					}
					if ($flagTax) {
						if ($flagConsumptionTaxWithoutCalc == 1 || $flagConsumptionTaxWithoutCalc == 2) {
							$arraySide[$strSide][] = 'dummy';
						}
					}

				}
				$arrayNew[$strSide] = array(
					'idAccountTitle'                         => $idAccountTitle,
					'numValue'                               => $numValue,
					'numValueConsumptionTax'                 => $numValueConsumptionTax,
					'idDepartment'                           => (int) $idDepartment,
					'idSubAccountTitle'                      => $idSubAccountTitle,
					'flagConsumptionTaxFree'                 => $flagConsumptionTaxFree,
					'flagConsumptionTaxIncluding'            => $flagConsumptionTaxIncluding,
					'flagConsumptionTaxGeneralRuleEach'      => $flagConsumptionTaxGeneralRuleEach,
					'flagConsumptionTaxGeneralRuleProration' => $flagConsumptionTaxGeneralRuleProration,
					'flagConsumptionTaxSimpleRule'           => $flagConsumptionTaxSimpleRule,
					'flagConsumptionTaxWithoutCalc'          => (int) $flagConsumptionTaxWithoutCalc,
					'flagConsumptionTaxCalc'                 => (int) $flagConsumptionTaxCalc,
				);
				if ($idDepartment) {
					$arrayIdDepartment[] = $idDepartment;
				}
				if ($idAccountTitle) {
					$arrayIdAccountTitle[] = $idAccountTitle;
				}
				if ($idSubAccountTitle) {
					$arrayIdSubAccountTitle[] = $idSubAccountTitle;
				}
			}
			$arrayDetail[$num] = $arrayNew;
			$num++;
		}

		$data = array(
			'idAccountTitleDebit'       => (count($arraySide['arrDebit']) == 1)? $arraySide['arrDebit'][0] : 'else',
			'idAccountTitleCredit'      => (count($arraySide['arrCredit']) == 1)? $arraySide['arrCredit'][0] : 'else',
			'varsDetail'                => $arrayDetail,
			'arrCommaIdDepartment'      => $classEscape->joinCommaArray(array('arr' => $arrayIdDepartment)),
			'arrCommaIdAccountTitle'    => $classEscape->joinCommaArray(array('arr' => $arrayIdAccountTitle)),
			'arrCommaIdSubAccountTitle' => $classEscape->joinCommaArray(array('arr' => $arrayIdSubAccountTitle)),
		);

		return $data;
	}

	/**
		(array(
			'vars' => $vars,
		))
	 */
	protected function _updateVarsCheck($arr)
	{
		global $varsAccounts;

		$vars = $arr['vars'];

		$arrAccountTitle = array();
		$array = $vars['varsRule']['arrAccountTitle']['arrStrTitle'];
		foreach ($array as $key => $value) {
			$arrAccountTitle[$value['strTitle']] = $key;
		}
		$arrAccountTitles = array();
		$array = $vars['varsRule']['arrAccountTitle']['arrStrTitles'];
		foreach ($array as $key => $value) {
			$arrayData = $value;
			foreach ($arrayData as $keyData => $valueData) {
				$arrAccountTitles[$key][$valueData['strTitle']] = $keyData;
			}
		}

		$arrSubAccountTitle = array();
		$array = $vars['varsRule']['arrSubAccountTitle']['arrStrTitle'];
		foreach ($array as $key => $value) {
			$arrayChild = $value;
			foreach ($arrayChild as $keyChild => $valueChild) {
				$arrSubAccountTitle[$valueChild['strTitle']] = $keyChild;
			}
		}

		$arrDepartment = array();
		$array = $vars['varsRule']['arrDepartment']['arrStrTitle'];
		foreach ($array as $key => $value) {
			$arrDepartment[$value['strTitle']] = $key;
		}

		$arrAccounts = array();
		$array = $varsAccounts;
		foreach ($array as $key => $value) {
			$arrAccounts[$value['strCodeName']] = $key;
		}

		$flagConsumptionTaxFree = (int) $vars['varsRule']['varsEntityNation']['flagConsumptionTaxFree'];
		$flagConsumptionTaxIncluding = (int) $vars['varsRule']['varsEntityNation']['flagConsumptionTaxIncluding'];
		$flagConsumptionTaxGeneralRule = (int) $vars['varsRule']['varsEntityNation']['flagConsumptionTaxGeneralRule'];
		$flagConsumptionTaxDeducted = (int) $vars['varsRule']['varsEntityNation']['flagConsumptionTaxDeducted'];

		$arrConsumptionTax = array();
		if (!$flagConsumptionTaxFree) {
			$str = 'arrStrSimple';
			if ($flagConsumptionTaxGeneralRule) {
				if ($flagConsumptionTaxDeducted) {
					$str = 'arrStrGeneralEach';
				} else {
					$str = 'arrStrGeneralProration';
				}
			}
			$array = $vars['varsRule']['varsConsumptionTax'][$str];
			foreach ($array as $key => $value) {
				$arrConsumptionTax[$value] = $key;
			}
		}

		$arrWithoutCalc = array();
		$array = $vars['varsRule']['varsConsumptionTax']['arrStrWithoutCalc'];
		foreach ($array as $key => $value) {
			$arrWithoutCalc[$value] = $key;
		}

		$vars['varsCheck'] = array(
			'arrAccounts'        => $arrAccounts,
			'arrAccountTitle'    => $arrAccountTitle,
			'arrAccountTitles'   => $arrAccountTitles,
			'arrDepartment'      => $arrDepartment,
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'arrConsumptionTax'  => $arrConsumptionTax,
			'arrWithoutCalc'     => $arrWithoutCalc,
		);

		return $vars;
	}

	/**
		(array(
			'vars' => $vars,
		))
	 */
	protected function _updateVarsRule($arr)
	{
		global $varsPluginAccountingPreference;
		global $varsPluginAccountingAccount;

		$vars = $arr['vars'];
		$arrSubAccountTitle = $this->_getVarsSubAccountTitle(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrayFSList = $this->_getFSList(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrAccountTitle = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'numFiscalPeriod'    => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$vars['varsRule'] = array(
			'arrayFSList'        => $arrayFSList,
			'arrAccountTitle'    => $arrAccountTitle,
			'arrDepartment'      => $this->_getVarsDepartment(array(
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			)),
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'varsEntityNation'   => $varsEntityNation,
			'varsConsumptionTax' => $this->_getVarsConsumptionTax(array()),
			'varsFSItem'         => $this->_getVarsFSItem(),
		);

		return $vars;
	}

	/**
	 *
	 */
	protected function _getNumFiscalTermStamp()
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$numTimeZone = PLUGIN_ACCOUNTING_NUM_TIME_ZONE;

		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$numFiscalPeriodStart = $varsPluginAccountingEntity[$varsPluginAccountingAccount['idEntityCurrent']]['numFiscalPeriodStart'];
		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));
		$numFiscalBeginningYear = $varsEntityNation['numFiscalBeginningYear'];
		$numCurrentYear = $numFiscalBeginningYear;
		$numCurrentYear2 = $numFiscalBeginningYear;

		$strTimeZone = (-1 * $numTimeZone) . 'hours';
		$numYear = $numCurrentYear;
		$numMonth = $varsEntityNation['numFiscalBeginningMonth'];
		$dateTime = new DateTime("$numYear-$numMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stampMin = $dateTime->format('U');

		$numEndMonth = $varsEntityNation['numFiscalBeginningMonth'] + $varsEntityNation['numFiscalTermMonth'];
		if ($numEndMonth > 12) {
			$numCurrentYear++;
			$numEndMonth -= 12;
		}

		$numEndMonth2 = $varsEntityNation['numFiscalBeginningMonth'] + 6;
		if ($numEndMonth2 > 12) {
			$numCurrentYear2++;
			$numEndMonth2 -= 12;
		}

		$numYear = $numCurrentYear;
		$numMonth = $numEndMonth;
		$dateTime = new DateTime("$numYear-$numMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stampMax = $dateTime->format('U') - 1;

		$numYear = $numCurrentYear2;
		$numMonth = $numEndMonth2;
		$dateTime = new DateTime("$numYear-$numMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stampMax2 = $dateTime->format('U') - 1;

		$data = array(
			'stampMin'  => $stampMin,
			'stampMax'  => $stampMax,
			'stampMax2' => $stampMax2,
		);

		return $data;

	}

	/**

	 */
	protected function _checkVarsCSV($arr)
	{
		global $varsRequest;

		global $classFile;
		global $classEscape;

		$arrValueFile = $arr['arrValueFile'];

		$strCode = $varsRequest['query']['StrCode'];

		if ($strCode != 'utf8') {
			$classFile->setConvert(array(
				'pathFrom'    => $arrValueFile['strUrl'],
				'strFromCode' => $strCode,
				'strToCode'   => 'utf-8',
			));
		}

		$arrayCSV = $classFile->getArray(array(
			'path' => $arrValueFile['strUrl'],
		));

		$array = $arrayCSV;
		foreach ($array as $key => $value) {
			$arrayCSV[$key] = $classEscape->to(array( 'data' => $arrayCSV[$key] ));
		}

		$array = $arr['vars']['varsItem']['varsId'];
		foreach ($array as $key => $value) {
			$arrayCSV[0] = str_replace("$value", $key, $arrayCSV[0]);
		}

		file_put_contents($arrValueFile['strUrl'], $arrayCSV);

		$arrayCSV = $classFile->getCsvRows(array(
			'path' => $arrValueFile['strUrl'],
		));

		$arrayLog = array();
		$array = $arrayCSV;
		foreach ($array as $key => $value) {
			$arrayLog[$value['id']][] = $value;
		}

		$arrayRequests = $this->_checkVarsCSVFormat(array(
			'vars'      => $arr['vars'],
			'arrayLog'  => $arrayLog,
			'strUrl'    => $arrValueFile['strUrl'],
		));

		$data = array(
			'strUrl'        => $arrValueFile['strUrl'],
			'arrayRequests' => $arrayRequests,
		);

		return $data;
	}

	/**
	 (array(
		'varsRule' => $varsRule,
		'idTarget' => $value[$id],
	 ))
	 */
	protected function _checkIdAccountTitle($arr)
	{
		if ($arr['varsRule']['arrAccountTitle']['arrStrTitle'][$arr['idTarget']]) {
			return $arr['idTarget'];
		}
		$array = $arr['varsRule']['arrayFSList'];
		foreach ($array as $key => $value) {
			$flagFS = $key;
			$strId = 'custom_' . $flagFS . '_' . $arr['idTarget'];
			if ($arr['varsRule']['arrAccountTitle']['arrStrTitle'][$strId]) {
				return $strId;
			}
		}

		return '';
	}

	/**
		(array(
			'vars'      => $arr['vars'],
			'arrayLog'  => $arrayLog,
			'strUrl'    => $arrValueFile['strUrl'],
		))
	 */
	protected function _checkVarsCSVFormat($arr)
	{
		global $classCheck;
		global $classEscape;

		global $varsPluginAccountingAccounts;
		global $varsPluginAccountingAccount;


		$varsId = $arr['vars']['varsItem']['varsId'];
		$varsComment = $arr['vars']['varsItem']['varsComment'];

		$varsCheck = $arr['vars']['varsCheck'];
		$varsRule = $arr['vars']['varsRule'];
		$flagConsumptionTaxFree = (int) $arr['vars']['varsRule']['varsEntityNation']['flagConsumptionTaxFree'];
		$flagConsumptionTaxIncluding = (int) $arr['vars']['varsRule']['varsEntityNation']['flagConsumptionTaxIncluding'];
		$flagConsumptionTaxGeneralRule = (int) $arr['vars']['varsRule']['varsEntityNation']['flagConsumptionTaxGeneralRule'];
		$flagConsumptionTaxDeducted = (int) $arr['vars']['varsRule']['varsEntityNation']['flagConsumptionTaxDeducted'];
		$flagConsumptionTaxCalc = (int) $arr['vars']['varsRule']['varsEntityNation']['flagConsumptionTaxCalc'];

		$flagConsumptionTaxWithoutCalcDefault = (int) $arr['vars']['varsRule']['varsEntityNation']['flagConsumptionTaxWithoutCalc'];
		$numConsumptionTax = (int) $arr['vars']['varsRule']['varsEntityNation']['numConsumptionTax'];

		$arrayRequests = array();
		$arrayLog = $arr['arrayLog'];
		foreach ($arrayLog as $keyLog => $valueLog) {

			$strStatus = $varsComment['strStatus'];
			$strStatus = str_replace("<%replace%>", $keyLog, $strStatus);

			if (count($valueLog) > 10) {
				$this->_sendError(array('comment' => $strStatus . $varsComment['strRowMax'], 'strUrl' => $arr['strUrl'],));
			}

			$varsRequest = $arr['vars']['varsItem']['varsRequest'];

			$array = &$arrayLog[$keyLog];
			$flagFirst = 1;
			$numSumDebit = 0;
			$numSumCredit = 0;
			$nomRows = 0;
			foreach ($array as $key => $value) {

				$id = 'id';
				if ($value[$id] == '') {
					$this->_sendError(array('comment' => $varsComment['strIdBlank'], 'strUrl' => $arr['strUrl'],));
				}
				$varsRequest[$id] = $value[$id];

				$flag = $classCheck->checkValueWord(array(
					'flagType' => 'num',
					'value'    => $value[$id]
				));
				if ($flag) {
					$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strFormat'], 'strUrl' => $arr['strUrl'],));
				}

				$varsDetail = $arr['vars']['varsItem']['varsDetail'];

				if ($flagFirst) {
					$id = 'stampBook';
					if ($value[$id] == '') {
						$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strMust'], 'strUrl' => $arr['strUrl'],));
					}

					$id = 'flagFiscalReport';
					if (!($value[$id] == 1 || $value[$id] == 0 || $value[$id] == 2)) {
						$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strFormat'], 'strUrl' => $arr['strUrl'],));
					}
					if ((int) $value[$id] == 1) {
						$varsRequest[$id] = 'f1';

					} elseif ((int) $value[$id] == 2) {
						$varsRequest[$id] = 'f21';
						if ((int) $arr['vars']['varsRule']['varsEntityNation']['numFiscalTermMonth'] < 12) {
							$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strTime2'], 'strUrl' => $arr['strUrl'],));
						}

					} else {
						$varsRequest[$id] = '0';
					}

					$stampBook = $this->_getStampBook(array(
						'flagFiscalReport' => $varsRequest[$id],
						'strBook'          => $value['stampBook'],
					));

					if (!$stampBook) {
						$this->_sendError(array('comment' => $strStatus . $varsId['stampBook'] . $varsComment['strFormat'], 'strUrl' => $arr['strUrl'],));
					}

					$data = $this->_getNumFiscalTermStamp();
					if (!($data['stampMin'] <= $stampBook && $stampBook <= $data['stampMax'])) {
						$this->_sendError(array('comment' => $strStatus . $varsId['stampBook'] . $varsComment['strTime'], 'strUrl' => $arr['strUrl'],));
					}
					$varsRequest['stampBook'] = $stampBook;


					$varsRequest['strTitle'] = mb_substr($value['strTitle'], 0, 100);

					$id = 'idAccount';
					if ($value[$id] == '') {
						$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strMust'], 'strUrl' => $arr['strUrl'],));
					}
					if (!$varsCheck['arrAccounts'][$value[$id]]) {
						$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strNone'], 'strUrl' => $arr['strUrl'],));
					}
					$varsRequest[$id] = $varsCheck['arrAccounts'][$value[$id]];

					$varsAuthority = $this->_getVarsAuthority(array('idAccount' => $varsRequest[$id]));
					if (!($varsAuthority == 'admin' || $varsAuthority['flagAllInsert'] || $varsAuthority['flagMyInsert'])) {
						$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strAuthority'], 'strUrl' => $arr['strUrl'],));
					}

					$str = ',' . $varsPluginAccountingAccount['idEntityCurrent'] . ',';
					$arrCommaIdEntity = $varsPluginAccountingAccounts[$varsRequest[$id]]['arrCommaIdEntity'];

					if ($varsAuthority != 'admin') {
						if (!preg_match("/$str/", $arrCommaIdEntity)) {
							$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strAuthorityEntity'], 'strUrl' => $arr['strUrl'],));
						}
					}

					$id = 'arrSpaceStrTag';
					if ($value[$id] != '') {
						$flagStrMax = $classCheck->checkValueMax(array(
							'flagType' => 'str',
							'num'      => 1000,
							'value'    => $value[$id],
						));
						if ($flagStrMax) {
							$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strStrMax'], 'strUrl' => $arr['strUrl'],));
						}
					}

					$arrSpaceStrTag = $classEscape->splitSpaceArray(array('data' => $value[$id]));
					$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));
					$varsRequest['jsonDetail'][$id] = $arrSpaceStrTag;

					$arrayStr = array('Debit', 'Credit');
					foreach ($arrayStr as $keyStr => $valueStr) {
						$flagTax = 0;

						$id = 'numValueConsumptionTax' . $valueStr;
						if ($value[$id] == '') {
							$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strMust'], 'strUrl' => $arr['strUrl'],));
						}
						$flag = $classCheck->checkValueWord(array(
							'flagType' => 'num',
							'value'    => $value[$id]
						));
						if ($flag) {
							$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strFormat'], 'strUrl' => $arr['strUrl'],));
						}
						$numValueConsumptionTax =  $value[$id];
						$varsDetail['arr' . $valueStr]['numValueConsumptionTax'] = (!$numValueConsumptionTax)? '': $numValueConsumptionTax;

						$id = 'numValue' . $valueStr;
						if ($value[$id] == '') {
							$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strMust'], 'strUrl' => $arr['strUrl'],));
						}
						$flag = $classCheck->checkValueWord(array(
							'flagType' => 'num',
							'value'    => $value[$id]
						));
						if ($flag) {
							$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strFormat'], 'strUrl' => $arr['strUrl'],));
						}
						$numValue =  $value[$id];

						$id = 'idAccountTitle' . $valueStr;
						if ($value[$id] == '') {
							$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strMust'], 'strUrl' => $arr['strUrl'],));
						}

						$flagIdTarget = $this->_checkIdAccountTitle(array(
							'varsRule' => $varsRule,
							'idTarget' => $value[$id],
						));
						if (!$flagIdTarget) {
							$flagFS = ($value['flagFS' . $valueStr])? $value['flagFS' . $valueStr] : '';
							if ($flagFS) {
								$flagIdTarget = $varsCheck['arrAccountTitles'][$flagFS][$value[$id]];

							} else {
								$flagIdTarget = $varsCheck['arrAccountTitle'][$value[$id]];
							}

							if (!$flagIdTarget) {
								$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strNone'], 'strUrl' => $arr['strUrl'],));
							}
						}
						$idAccountTitle = $flagIdTarget;
						$varsDetail['arr' . $valueStr]['idAccountTitle'] = $idAccountTitle;

						if ($numValue <= 0) {
							$this->_sendError(array('comment' => $strStatus . $varsId['numValue' . $valueStr] . $varsComment['strNumValue'], 'strUrl' => $arr['strUrl'],));
						}

						$id = 'idSubAccountTitle' . $valueStr;
						if ($value[$id] != '') {
							$idSubAccountTitle = $varsCheck['arrSubAccountTitle'][$value[$id]];
							if (!$varsRule['arrSubAccountTitle']['arrStrTitle'][$idAccountTitle][$idSubAccountTitle]) {
								$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strNone'], 'strUrl' => $arr['strUrl'],));
							}
							$varsDetail['arr' . $valueStr]['idSubAccountTitle'] = $idSubAccountTitle;
						}

						$id = 'idDepartment' . $valueStr;
						if ($value[$id] != '') {
							if (!$varsCheck['arrDepartment'][$value[$id]]) {
								$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strNone'], 'strUrl' => $arr['strUrl'],));
							}
							$idDepartment = $varsCheck['arrDepartment'][$value[$id]];
							$varsDetail['arr' . $valueStr]['idDepartment'] = $idDepartment;
						}

						$flagConsumptionTax = '';
						$id = 'flagConsumptionTax' . $valueStr;
						if ($value[$id] != '') {
							if (!$varsCheck['arrConsumptionTax'][$value[$id]]) {
								$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strNone'], 'strUrl' => $arr['strUrl'],));
							}
							$flagConsumptionTax = $varsCheck['arrConsumptionTax'][$value[$id]];
						}

						$flagConsumptionTaxWithoutCalc = $flagConsumptionTaxWithoutCalcDefault;
						$id = 'flagConsumptionTaxWithoutCalc' . $valueStr;
						if ($value[$id] != '') {
							if (!$varsCheck['arrWithoutCalc'][$value[$id]]) {
								$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strNone'], 'strUrl' => $arr['strUrl'],));
							}
							$flagConsumptionTaxWithoutCalc = $varsCheck['arrWithoutCalc'][$value[$id]];
						}

						$varsDetail['arr' . $valueStr]['flagConsumptionTaxCalc'] = $flagConsumptionTaxCalc;
						if ($flagConsumptionTaxFree) {
							$varsDetail['arr' . $valueStr]['flagConsumptionTaxFree'] = 1;
							$varsDetail['arr' . $valueStr]['flagConsumptionTaxIncluding'] = 1;
							$varsDetail['arr' . $valueStr]['flagConsumptionTaxGeneralRuleEach'] = '';
							$varsDetail['arr' . $valueStr]['flagConsumptionTaxGeneralRuleProration'] = '';
							$varsDetail['arr' . $valueStr]['flagConsumptionTaxSimpleRule'] = '';
							$varsDetail['arr' . $valueStr]['flagConsumptionTaxWithoutCalc'] = 1;
							if ($idAccountTitle == 'suspenseReceiptOfConsumptionTaxes' || $idAccountTitle == 'suspensePaymentConsumptionTaxes') {
								$this->_sendError(array('comment' => $strStatus . $varsId['idAccountTitle' . $valueStr] . $varsComment['strTaxFree'], 'strUrl' => $arr['strUrl'],));
							}

						} else {
							$varsDetail['arr' . $valueStr]['flagConsumptionTaxFree'] = 0;
							if ($flagConsumptionTaxGeneralRule) {
								if ($flagConsumptionTaxDeducted) {
									$varsDetail['arr' . $valueStr]['flagConsumptionTaxGeneralRuleEach'] = $flagConsumptionTax;
								} else {
									$varsDetail['arr' . $valueStr]['flagConsumptionTaxGeneralRuleProration'] = $flagConsumptionTax;
								}

							} else {
								$varsDetail['arr' . $valueStr]['flagConsumptionTaxSimpleRule'] = $flagConsumptionTax;
							}
							if ($flagConsumptionTaxIncluding) {
								$varsDetail['arr' . $valueStr]['flagConsumptionTaxIncluding'] = 1;
								$varsDetail['arr' . $valueStr]['flagConsumptionTaxWithoutCalc'] = 1;

							} else {
								$varsDetail['arr' . $valueStr]['flagConsumptionTaxIncluding'] = 0;
								$varsDetail['arr' . $valueStr]['flagConsumptionTaxWithoutCalc'] = $flagConsumptionTaxWithoutCalc;
							}

							if (!$flagConsumptionTaxIncluding && preg_match( "/^tax/", $flagConsumptionTax)) {
								$flagTax = 1;
							}

							if ($idAccountTitle == 'suspenseReceiptOfConsumptionTaxes' || $idAccountTitle == 'suspensePaymentConsumptionTaxes') {
								$flagTax = 0;
							}
						}

						if ($valueStr == 'Debit') {
							$numSumDebit += $numValue;
						} else {
							$numSumCredit += $numValue;
						}

						if ($flagTax) {
							if ($flagConsumptionTaxWithoutCalc == 2) {
								$numValue -= $numValueConsumptionTax;
							}
						}
						if ($numValue < 0) {
							$this->_sendError(array('comment' => $strStatus . $varsId['numValueConsumptionTax' . $valueStr] . $varsComment['strTax'], 'strUrl' => $arr['strUrl'],));
						}
						$varsDetail['arr' . $valueStr]['numValue'] = $numValue;


						if ($flagTax) {
							if ($flagConsumptionTaxWithoutCalc != 3) {

							} else {
								if ($numValueConsumptionTax != 0) {
									$this->_sendError(array('comment' => $strStatus . $varsId['numValueConsumptionTax' . $valueStr] . $varsComment['strTaxOut'], 'strUrl' => $arr['strUrl'],));
								}
							}
						}
					}

					$varsRequest['jsonDetail']['varsDetail'][] = $varsDetail;
					$varsRequest['jsonDetail']['numSum'] = $numSumDebit;
					$varsRequest['jsonDetail']['numSumDebit'] = $numSumDebit;
					$varsRequest['jsonDetail']['numSumCredit'] = $numSumCredit;
					$flagFirst = 0;
					continue;
				}

				$arrayStr = array('Debit', 'Credit');
				foreach ($arrayStr as $keyStr => $valueStr) {
					$flagTax = 0;

					$id = 'numValueConsumptionTax' . $valueStr;
					if ($value[$id] == '') {
						$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strMust'], 'strUrl' => $arr['strUrl'],));
					}
					$flag = $classCheck->checkValueWord(array(
						'flagType' => 'num',
						'value'    => $value[$id]
					));
					if ($flag) {
						$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strFormat'], 'strUrl' => $arr['strUrl'],));
					}
					$numValueConsumptionTax =  $value[$id];
					$varsDetail['arr' . $valueStr]['numValueConsumptionTax'] = (!$numValueConsumptionTax)? '': $numValueConsumptionTax;

					$id = 'numValue' . $valueStr;
					if ($value[$id] == '') {
						$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strMust'], 'strUrl' => $arr['strUrl'],));
					}
					$flag = $classCheck->checkValueWord(array(
						'flagType' => 'num',
						'value'    => $value[$id]
					));
					if ($flag) {
						$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strFormat'], 'strUrl' => $arr['strUrl'],));
					}
					$numValue = $value[$id];

					$id = 'idAccountTitle' . $valueStr;
					if ($value[$id] != '') {

						$flagIdTarget = $this->_checkIdAccountTitle(array(
							'varsRule' => $varsRule,
							'idTarget' => $value[$id],
						));
						if (!$flagIdTarget) {
							$flagFS = ($value['flagFS' . $valueStr])? $value['flagFS' . $valueStr] : '';
							if ($flagFS) {
								$flagIdTarget = $varsCheck['arrAccountTitles'][$flagFS][$value[$id]];

							} else {
								$flagIdTarget = $varsCheck['arrAccountTitle'][$value[$id]];
							}

							if (!$flagIdTarget) {
								$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strNone'], 'strUrl' => $arr['strUrl'],));
							}
						}
						$idAccountTitle = $flagIdTarget;
						$varsDetail['arr' . $valueStr]['idAccountTitle'] = $idAccountTitle;

						if ($numValue <= 0) {
							$this->_sendError(array('comment' => $strStatus . $varsId['numValue' . $valueStr] . $varsComment['strNumValue'], 'strUrl' => $arr['strUrl'],));
						}

						$id = 'idSubAccountTitle' . $valueStr;
						if ($value[$id] != '') {
							if (!$varsCheck['arrSubAccountTitle'][$value[$id]]) {
								$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strNone'], 'strUrl' => $arr['strUrl'],));
							}
							$idSubAccountTitle = $varsCheck['arrSubAccountTitle'][$value[$id]];
							if (!$varsRule['arrSubAccountTitle']['arrStrTitle'][$idAccountTitle][$idSubAccountTitle]) {
								$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strNone'], 'strUrl' => $arr['strUrl'],));
							}
							$varsDetail['arr' . $valueStr]['idSubAccountTitle'] = $idSubAccountTitle;
						}

						$id = 'idDepartment' . $valueStr;
						if ($value[$id] != '') {
							if (!$varsCheck['arrDepartment'][$value[$id]]) {
								$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strNone'], 'strUrl' => $arr['strUrl'],));
							}
							$idDepartment = $varsCheck['arrDepartment'][$value[$id]];
							$varsDetail['arr' . $valueStr]['idDepartment'] = $idDepartment;
						}

						$flagConsumptionTax = '';
						$id = 'flagConsumptionTax' . $valueStr;
						if ($value[$id] != '') {
							if (!$varsCheck['arrConsumptionTax'][$value[$id]]) {
								$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strNone'], 'strUrl' => $arr['strUrl'],));
							}
							$flagConsumptionTax = $varsCheck['arrConsumptionTax'][$value[$id]];
						}

						$flagConsumptionTaxWithoutCalc = $flagConsumptionTaxWithoutCalcDefault;
						$id = 'flagConsumptionTaxWithoutCalc' . $valueStr;
						if ($value[$id] != '') {
							if (!$varsCheck['arrWithoutCalc'][$value[$id]]) {
								$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strNone'], 'strUrl' => $arr['strUrl'],));
							}
							$flagConsumptionTaxWithoutCalc = $varsCheck['arrWithoutCalc'][$value[$id]];
						}

						$varsDetail['arr' . $valueStr]['flagConsumptionTaxCalc'] = $flagConsumptionTaxCalc;
						if ($flagConsumptionTaxFree) {
							$varsDetail['arr' . $valueStr]['flagConsumptionTaxFree'] = 1;
							$varsDetail['arr' . $valueStr]['flagConsumptionTaxIncluding'] = 1;
							$varsDetail['arr' . $valueStr]['flagConsumptionTaxGeneralRuleEach'] = '';
							$varsDetail['arr' . $valueStr]['flagConsumptionTaxGeneralRuleProration'] = '';
							$varsDetail['arr' . $valueStr]['flagConsumptionTaxSimpleRule'] = '';
							$varsDetail['arr' . $valueStr]['flagConsumptionTaxWithoutCalc'] = 1;
							if ($idAccountTitle == 'suspenseReceiptOfConsumptionTaxes' || $idAccountTitle == 'suspensePaymentConsumptionTaxes') {
								$this->_sendError(array('comment' => $strStatus . $varsId['idAccountTitle' . $valueStr] . $varsComment['strTaxFree'], 'strUrl' => $arr['strUrl'],));
							}

						} else {
							$varsDetail['arr' . $valueStr]['flagConsumptionTaxFree'] = 0;
							if ($flagConsumptionTaxGeneralRule) {
								if ($flagConsumptionTaxDeducted) {
									$varsDetail['arr' . $valueStr]['flagConsumptionTaxGeneralRuleEach'] = $flagConsumptionTax;
								} else {
									$varsDetail['arr' . $valueStr]['flagConsumptionTaxGeneralRuleProration'] = $flagConsumptionTax;
								}

							} else {
								$varsDetail['arr' . $valueStr]['flagConsumptionTaxSimpleRule'] = $flagConsumptionTax;
							}
							if ($flagConsumptionTaxIncluding) {
								$varsDetail['arr' . $valueStr]['flagConsumptionTaxIncluding'] = 1;
								$varsDetail['arr' . $valueStr]['flagConsumptionTaxWithoutCalc'] = 1;

							} else {
								$varsDetail['arr' . $valueStr]['flagConsumptionTaxIncluding'] = 0;
								$varsDetail['arr' . $valueStr]['flagConsumptionTaxWithoutCalc'] = $flagConsumptionTaxWithoutCalc;
							}

							if (!$flagConsumptionTaxIncluding && preg_match( "/^tax/", $flagConsumptionTax)) {
								$flagTax = 1;
							}

							if ($idAccountTitle == 'suspenseReceiptOfConsumptionTaxes' || $idAccountTitle == 'suspensePaymentConsumptionTaxes') {
								$flagTax = 0;
							}
						}

						if ($valueStr == 'Debit') {
							$numSumDebit += $numValue;
						} else {
							$numSumCredit += $numValue;
						}

						if ($flagTax) {
							if ($flagConsumptionTaxWithoutCalc == 2) {
								$numValue -= $numValueConsumptionTax;
							}
						}
						if ($numValue < 0) {
							$this->_sendError(array('comment' => $strStatus . $varsId['numValueConsumptionTax' . $valueStr] . $varsComment['strTax'], 'strUrl' => $arr['strUrl'],));
						}
						$varsDetail['arr' . $valueStr]['numValue'] =  $numValue;

						if ($flagTax) {
							if ($flagConsumptionTaxWithoutCalc != 3) {

							} else {
								if ($numValueConsumptionTax != 0) {
									$this->_sendError(array('comment' => $strStatus . $varsId['numValueConsumptionTax' . $valueStr] . $varsComment['strTaxOut'], 'strUrl' => $arr['strUrl'],));
								}
							}
						}

					} else {
						if ($value['numValue' . $valueStr] != 0) {
							$this->_sendError(array('comment' => $strStatus . $varsId['numValue' . $valueStr] . $varsComment['strAccountTitle'], 'strUrl' => $arr['strUrl'],));

						} elseif ($value['numValueConsumptionTax' . $valueStr] != 0) {
							$this->_sendError(array('comment' => $strStatus . $varsId['numValueConsumptionTax' . $valueStr] . $varsComment['strAccountTitle'], 'strUrl' => $arr['strUrl'],));
						}
						$arrayEr = array(
							'idSubAccountTitle',
							'idDepartment',
							'flagConsumptionTax',
							'flagConsumptionTaxWithoutCalc',
						);
						foreach ($arrayEr as $keyEr => $valueEr) {
							$id = $valueEr . $valueStr;
							if ($value[$id] != '') {
								$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strAccountTitle'], 'strUrl' => $arr['strUrl'],));
							}
						}
					}
				}
				$varsRequest['jsonDetail']['varsDetail'][] = $varsDetail;
				$varsRequest['jsonDetail']['numSum'] = $numSumDebit;
				$varsRequest['jsonDetail']['numSumDebit'] = $numSumDebit;
				$varsRequest['jsonDetail']['numSumCredit'] = $numSumCredit;
			}

			if ($varsRequest['jsonDetail']['numSumDebit'] != $varsRequest['jsonDetail']['numSumCredit']) {
				$this->_sendError(array('comment' => $strStatus . $varsComment['strSum'], 'strUrl' => $arr['strUrl'],));
			}


			if ($varsRequest['jsonDetail']['numSum'] > 99999999999) {
				$this->_sendError(array('comment' => $strStatus . $varsComment['strSumMax'], 'strUrl' => $arr['strUrl'],));
			}

			$arrayRequests[] = $varsRequest;
		}

		return $arrayRequests;

	}

	/**
	 *
	 */
	protected function _getStampBook($arr)
	{
		global $classCheck;

		global $varsAccounts;
		global $varsAccount;
		global $varsPluginAccountingAccount;

		$strStamp = $arr['strBook'];
		if (preg_match( "/^([0-9]{4})\/([0-9]{1,2})\/([0-9]{1,2})-([0-9]{1,2}):([0-9]{1,2})$/", $strStamp)) {
			$flag = $classCheck->checkValueFormat(array(
				'flagType' => 'date-time',
				'value'    => $strStamp
			));
			if ($flag) {
				return 0;
			}
			preg_match( "/^([0-9]{4})\/([0-9]{1,2})\/([0-9]{1,2})-([0-9]{1,2}):([0-9]{1,2})$/", $strStamp, $arrMatch);
			list($strStamp, $numYear, $numMonth, $numDate, $numHour, $numMin) = $arrMatch;

		} elseif (preg_match( "/^([0-9]{4})\/([0-9]{1,2})\/([0-9]{1,2})$/", $strStamp)) {
			$flag = $classCheck->checkValueFormat(array(
				'flagType' => 'date',
				'value'    => $strStamp
			));
			if ($flag) {
				return 0;
			}
			preg_match( "/^([0-9]{4})\/([0-9]{1,2})\/([0-9]{1,2})$/", $strStamp, $arrMatch);
			list($strStamp, $numYear, $numMonth, $numDate) = $arrMatch;
			$numHour = 0;
			$numMin = 0;

		} else {
			return 0;
		}

		$data = $this->_getNumFiscalTermStamp();
		$stampBook = 0;
		if ($arr['flagFiscalReport'] == 'f1') {
			$stampBook = $data['stampMax'];

		} elseif ($arr['flagFiscalReport'] == 'f21') {
			$stampBook = $data['stampMax2'];

		} else {
			$strTimeZone = (-1 * $varsAccount['numTimeZone']) . 'hours';
			$dateTime = new DateTime("$numYear-$numMonth-$numDate 0:0 $strTimeZone", new DateTimeZone("UTC"));
			$stampBook = $dateTime->format('U') + $numHour * 3600 + $numMin * 60;
		}



		return $stampBook;
	}

	/**

	 */
	protected function _sendError($arr)
	{
		unlink($arr['strUrl']);
		$this->_sendMessage(array('flag' => 'dummy', 'vars' => $arr['comment']));
	}

	/**

	 */
	protected function _sendMessage($arr)
	{
		$this->_sendVars(array(
			'flagIframe' => 1,
			'flag'       => ($arr['flag'])? $arr['flag'] : 1,
			'stamp'      => $this->getStamp(),
			'numNews'    => $this->getNumNews(),
			'vars'       => ($arr['vars'])? $arr['vars'] : array(),
		));
	}

	/**

	 */
	protected function _checkFileValue()
	{
		global $classCheck;
		global $classEscape;

		global $varsRequest;
		global $varsPluginAccountingPreference;
		global $varsPluginAccountingAccount;

		$idAccount = $varsAccount['id'];

		if	(!$this->_checkCurrent()) {
			$this->_sendMessage(array('flag' => 40));
		}

		$varsAuthority = $this->_getVarsAuthority(array());
		if	(is_null($varsAuthority)) {
			$this->_sendMessage(array('flag' => 40));
		}

		$arrFile = $this->_checkValueFile();

		$id = $varsRequest['query']['idTag'];
		$strFileType = $classEscape->getFileType(array('strUrl' => $_FILES[$id]['name']));
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$numYear = date('Y');
		$numMonth = date('m');
		$strFileName = hash('sha256', MICROTIMESTAMP). '.' . $strFileType . '-' . $idEntity . '-' . $numFiscalPeriod . '.cgi';
		$path = PATH_BACK_DAT_TEMP;
		if (!is_dir($path)) {
			mkdir($path);
		}
		$strUrl = $path . $strFileName;

		if (!move_uploaded_file($_FILES[$id]['tmp_name'], $strUrl)) {
			$this->_sendMessage(array('flag' => 'strError'));
		}

		$data = array(
			'strUrl'      => $strUrl,
			'strFileType' => $strFileName,
			'strFileType' => $arrFile['strFileType'],
			'numByte'     => $arrFile['numByte'],
		);

		return $data;
	}

	/**

	 */
	protected function _checkValueFile()
	{
		global $classEscape;

		global $varsRequest;
		global $varsPluginAccountingPreference;

		$id = $varsRequest['query']['idTag'];

		$strFileType = $classEscape->getFileType(array('strUrl' => $_FILES[$id]['name']));

		$array = array('csv');
		foreach ($array as $key => $value) {
			$arrayCheck[$value] = 1;
		}

		if (!$arrayCheck[$strFileType]) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
			}
			exit;
		}

		$numByte = $_FILES[$id]['size'];
		if ($numByte > NUM_MAX_UPLOAD_SIZE) {
			$this->_sendMessage(array('flag' => 'strSize'));
		}

		if ($_FILES[$id]['error']) {
			$this->_sendMessage(array('flag' => 'strError'));
		}

		$data = array(
			'strFileType' => $strFileType,
			'numByte'     => $numByte,
		);

		return $data;
	}

	/**
		array(
			'flagIframe' => int,
			'flag'       => mix,
			'stamp'      => array,
			'numNews'    => int,
			'vars'       => array,
		)
	 */
	protected function _sendVars($arr)
	{
		global $varsRequest;

		$array = array(
			'flag'    => (!is_null($arr['flag']))? $arr['flag'] : 1,
			'numNews' => ($arr['numNews'])? $arr['numNews'] : 0,
			'stamp'   => ($arr['stamp'])? $arr['stamp'] : '',
			'data'    => $arr['vars'],
		);

		$jsonVars = json_encode($array);
		$jsonIdUpload = json_encode($varsRequest['query']['idUpload']);

		$varsIframe = $this->getVars(array(
			'path' => $this->_extSelf['varsIframe'],
		));
		$tmplIframe = str_replace('<%idUpload%>', $jsonIdUpload, $varsIframe['tmpl']);
		$tmplIframe = str_replace('<%vars%>', $jsonVars, $tmplIframe);

		print $tmplIframe;
		exit;
	}

	/**

	 */
	protected function _checkValueElse($arr)
	{
		global $classCheck;
		global $varsRequest;

		$strCode = $varsRequest['query']['StrCode'];
		$array = $arr['vars']['portal']['varsNavi']['templateDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'StrCode') {
				$flag = 0;
				$arrayOption = $value['arrayOption'];
				foreach ($arrayOption as $keyOption => $valueOption) {
					if ($valueOption['value'] == $strCode) {
						$flag = 1;
						break;
					}
				}
				if (!$flag) {
					if (FLAG_TEST) {
						var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
					}
					exit;
				}
			}
		}

	}
}
