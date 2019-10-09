<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_LogEditor extends Code_Else_Plugin_Accounting_Jpn_Log
{
	protected $_childSelf = array(
		'pathTplJs'  => 'else/plugin/accounting/js/jpn/logEditor.js',
		'pathVarsJs' => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/logEditor.php',
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
					'strColumn' => 'jsonLogEditorNaviFormat',
					'flagEntity'  => 1,
					'flagAccount' => 1,
				),
			),
		));
	}

	/**
	 *
	 */
	protected function _iniNaviFormatSave()
	{
		$this->_setNaviFormatSave(array(
			'pathVars'  => $this->_childSelf['pathVarsJs'],
			'strColumn' => 'jsonLogEditorNaviFormat',
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
			'pathVars'  => $this->_childSelf['pathVarsJs'],
			'strColumn' => 'jsonLogEditorNaviFormat',
			'strTable'  => 'accountingAccountMemo',
			'flagEntity'  => 1,
			'flagAccount' => 1,
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
	protected function _iniDetailAdd()
	{
		global $classDb;
		global $classEscape;

		global $varsRequest;
		global $varsAccount;
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


		$this->_checkValueDetail(array(
			'vars'     => $vars,
			'arrValue' => $arrValue,
		));

		$flagCashInsert = $this->_checkAccess(array(
			'flagAllUse'    => 0,
			'flagAuthority' => 'insert',
			'idTarget'      => $this->_extSelf['idCash'],
		));

		$flagCashSelect = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'select',
			'idTarget'      => $this->_extSelf['idCash'],
		));


		$classCalcCashPay = $this->_getClassCalc(array('flagType' => 'CashPay'));
		$classCalcCash = $this->_getClassCalc(array('flagType' => 'Cash'));
		$classCalcLog = $this->_getClassCalc(array('flagType' => 'Log'));

		$flagSearch = '';
		try {
			$dbh->beginTransaction();

			if ($arrValue['arr']['flagFiscalReport'] != 'none' || !$flagCashInsert) {
				$this->_setDbLog(array(
					'vars'         => $vars,
					'arrValue'     => $arrValue,
					'classCalcLog' => $classCalcLog,
				));

			} else {

				$arrOrder = array(array(
					'numFiscalPeriod'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					'idEntity'                => $varsPluginAccountingAccount['idEntityCurrent'],
					'idAccount'               => $varsAccount['id'],
					'idAccountApply'          => $varsAccount['id'],
					'flagFiscalReport'        => $arrValue['arr']['flagFiscalReport'],
					'stampBook'               => $arrValue['arr']['stampBook'],
					'strTitle'                => $arrValue['arr']['strTitle'],
					'jsonDetail'              => $arrValue['arr']['jsonDetail'],
					'arrCommaIdLogFile'       => $arrValue['arr']['arrCommaIdLogFile'],
					'arrCommaIdAccountPermit' => $arrValue['arr']['arrCommaIdAccountPermit'],
					'numSumMax'               => $arrValue['arr']['numSumMax'],
					'arrSpaceStrTag'          => $arrValue['arr']['arrSpaceStrTag'],
				),);

				$arrVarsDbLog = $classCalcLog->allot(array(
					'flagStatus'      => 'varsDbLog',
					'arrOrder'        => $arrOrder,
					'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				));

				$varsDbLog = end($arrVarsDbLog);
				$tempValue = array(
					'idAccount'               => $varsDbLog['idAccount'],
					'idAccountApply'          => $varsDbLog['idAccountApply'],
					'flagApply'               => $varsDbLog['flagApply'],
					'flagFiscalReport'        => $varsDbLog['flagFiscalReport'],
					'stampBook'               => $varsDbLog['stampBook'],
					'strTitle'                => $varsDbLog['strTitle'],
					'jsonDetail'              => $arrValue['arr']['jsonDetail'],
					'arrCommaIdLogFile'       => $varsDbLog['arrCommaIdLogFile'],
					'arrCommaIdAccountPermit' => $varsDbLog['arrCommaIdAccountPermit'],
					'jsonPermitHistory'       => json_decode($varsDbLog['jsonPermitHistory'], true),
					'arrSpaceStrTag'          => $varsDbLog['arrSpaceStrTag'],
				);

				$flagCashVars = $classCalcCashPay->allot(array(
					'flagStatus'      => 'check',
					'arrValue'        => $tempValue,
					'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					'classCalcLog'    => $classCalcLog,
				));

				if ($flagCashVars['flag']) {
					if ($flagCashVars['flag'] == 'pay' || $flagCashVars['flag'] == 'paid') {
						$flagVars = $classCalcCashPay->allot(array(
							'flagStatus'      => $flagCashVars['flag'],
							'arrValue'        => $tempValue,
							'arrRowsCash'     => $flagCashVars['arrRowsCash'],
							'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
							'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
							'classCalcCash'   => $classCalcCash,
						));
						if ($flagVars == 'errorDataMax') {
							$this->sendVars(array(
								'flag'    => $flagVars,
								'stamp'   => $this->getStamp(),
								'numNews' => $this->getNumNews(),
								'vars'    => array(),
							));
						}

						$arrValue['arr']['arrSpaceStrTag'] = $flagVars['arrValue']['arrSpaceStrTag'];

						$arrVarsLog = $this->_setDbLog(array(
							'vars'         => $vars,
							'arrValue'     => $arrValue,
							'classCalcLog' => $classCalcLog,
						));

						$flagVars = $classCalcCashPay->allot(array(
							'flagStatus'      => 'WriteHistory',
							'varsLog'         => end($arrVarsLog),
							'varsLogCash'     => $flagVars['arrVarsLogAdd'][0],
							'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
							'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
						));
						if ($flagVars == 'errorDataMax') {
							$this->sendVars(array(
								'flag'    => $flagVars,
								'stamp'   => $this->getStamp(),
								'numNews' => $this->getNumNews(),
								'vars'    => array(),
							));
						}

					} elseif ($flagCashVars['flag'] == 'caution') {
						$flagVars = $classCalcCashPay->allot(array(
							'flagStatus'      => $flagCashVars['flag'],
							'varsLog'         => $flagCashVars['varsLog'],
							'arrRowsCash'     => $flagCashVars['arrRowsCash'],
							'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
							'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
						));
						if ($flagCashSelect) {
							$flagSearch = 'defer';
						} else {
							$flagSearch = 'deferReject';
						}
					}

				} else {
					$this->_setDbLog(array(
						'vars'         => $vars,
						'arrValue'     => $arrValue,
						'classCalcLog' => $classCalcLog,
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
		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
		$this->_setSearch(array('flag' => $flagSearch));
	}

	/**

	 */
	protected function _updateVarsDetail($arr)
	{
		global $varsRequest;
		global $classEscape;

		$array = $arr['vars'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'StampBook') {
				$flagFiscalReport = $varsRequest['query']['jsonValue']['vars']['FlagFiscalReport'];
				if ($flagFiscalReport != 'none') {
					$array[$key]['flagMustUse'] = 0;
				}

			} elseif ($value['id'] == 'NumSumMax') {
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

		$this->_checkValueDetailPermit(array(
			'arrValue' => $arr['arrValue'],
		));

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
			'varsItem'        => &$arr['vars']['varsRule'],
		));


		if ($flag) {
			if ($flag == 'term' || $flag == 'noneFile') {
				$this->sendVars(array(
					'flag'    => $flag,
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));
			} else {
				if (FLAG_TEST) {
					var_dump($flag);
					exit;
				}
				$this->_sendOldError();
			}
		}
	}

	/**

	 */
	protected function _checkValueDetailPermit($arr)
	{
		global $classEscape;
		global $classDb;

		global $varsRequest;
		global $varsAccount;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingAccounts;
		global $varsPluginAccountingAccountsEntity;

		$arrValue = $arr['arrValue'];

		$data = $arrValue['arr']['arrCommaIdAccountPermit'];
		$array = $classEscape->splitCommaArrayData(array('data' => $data));
		foreach ($array as $key => $value) {
			if (!$varsPluginAccountingAccountsEntity[$value][$varsPluginAccountingAccount['idEntityCurrent']]) {
				$this->sendVars(array(
					'flag'    => 'none',
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));
			}
			$varsAuthority = $this->_getVarsAuthority(array('idAccount' => $value));
			if ($varsAuthority == 'admin') {
				continue;
			}

			$str = ',' . $varsPluginAccountingAccount['idEntityCurrent'] . ',';
			$arrCommaIdEntity = $varsPluginAccountingAccounts[$value]['arrCommaIdEntity'];

			if (!preg_match("/$str/", $arrCommaIdEntity)) {
				$this->sendVars(array(
					'flag'    => 'none',
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));

			} else {
				if (!$varsAuthority['flagAllUpdate']) {
					$this->sendVars(array(
						'flag'    => 'none',
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(),
					));
				}
			}
		}

		if (count($array)) {
			if ((int) $arrValue['arr']['numSumMax'] < 1) {
				$this->_sendOldError();
			}
		}
	}

	/**
				'vars'     => $vars,
				'arrValue' => $arrValue,
	 */
	protected function _setDbLog($arr)
	{
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;
		global $varsAccount;

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriodTempNext = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];

		$arrOrder = array(array(
			'numFiscalPeriod'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'                => $idEntity,
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
		),);

		$classCalcLog = &$arr['classCalcLog'];
		$flag = $classCalcLog->allot(array(
			'flagStatus'              => 'add',
			'arrOrder'                => $arrOrder,
			'idEntity'                => $idEntity,
			'numFiscalPeriod'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagTempPrev'            => (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow))? 1 : 0,
			'numFiscalPeriodTempNext' => $numFiscalPeriodTempNext,
		));
		$flagType = gettype($flag);
		if ($flag == 'errorDataMax') {
			$this->sendVars(array(
				'flag'    => $flag,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));

		} else if (gettype($flag) != 'array') {
			$this->_sendOldError();
		}
		$arrVarsLog = $flag;

		return $arrVarsLog;

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
			'idTarget'   => $varsRequest['query']['jsonValue']['idTarget'],
			'flagRemove' => 0,
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

		$this->_checkValueDetail(array(
			'vars'     => $vars,
			'arrValue' => $arrValue,
			'varsLog'  => $varsLog,
		));

		try {
			$dbh->beginTransaction();

			$this->_updateDbLog(array(
				'vars'     => $vars,
				'arrValue' => $arrValue,
				'varsLog'  => $varsLog,
			));

			$this->_updateDbPreferenceStamp(array('strColumn' => 'log'));

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
	protected function _getDbLogStampBook($arr)
	{
		global $varsAccounts;
		global $varsAccount;
		global $varsPluginAccountingAccount;

		$vars = $arr['vars'];
		$arrValue = $arr['arrValue'];

		$numTimeZone = PLUGIN_ACCOUNTING_NUM_TIME_ZONE;
		$strTimeZone = (-1 * $numTimeZone) . 'hours';

		$data = $this->_getNumFiscalTermStamp();
		$stampMin = $data['stampMin'];
		$stampMax = $data['stampMax'];
		$varsEntityNation = $arr['vars']['varsRule']['varsEntityNation'];
		$numFiscalBeginningYear = $varsEntityNation['numFiscalBeginningYear'];
		$numCurrentYear = $numFiscalBeginningYear;
		$numFiscalBeginningMonth = $varsEntityNation['numFiscalBeginningMonth'];

		if ($varsEntityNation['numFiscalTermMonth'] != 12) {
			if ($arrValue['arr']['flagFiscalReport'] == 'f1') {
				$stampBook = $stampMax;

			} elseif (preg_match( "/^f/", $arrValue['arr']['flagFiscalReport'])) {
				$this->_sendOldError();
			}

		} else {
			if ($arrValue['arr']['flagFiscalReport'] == 'f1') {
				$stampBook = $stampMax;

			} elseif (preg_match( "/^f/", $arrValue['arr']['flagFiscalReport'])) {
				if ($arrValue['arr']['flagFiscalReport'] == 'f21' || $arrValue['arr']['flagFiscalReport'] == 'f42') {
					$numMonth = $numFiscalBeginningMonth + 6;

				} elseif ($arrValue['arr']['flagFiscalReport'] == 'f41') {
					$numMonth = $numFiscalBeginningMonth + 3;

				} elseif ($arrValue['arr']['flagFiscalReport'] == 'f43') {
					$numMonth = $numFiscalBeginningMonth + 9;
				}

				if ($numMonth > 12) {
					$numCurrentYear++;
					$numMonth -= 12;
				}

				$numYearTemp = $numCurrentYear;
				$dateTime = new DateTime("$numYearTemp-$numMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
				$stampBook = $dateTime->format('U') - 1;
			}
		}

		if (!preg_match( "/^f/", $arrValue['arr']['flagFiscalReport'])) {
			$strStamp = $arrValue['arr']['stampBook'];
			preg_match( "/^([0-9]{4})\/([0-9]{1,2})\/([0-9]{1,2})-([0-9]{1,2}):([0-9]{1,2})$/", $strStamp, $arrMatch);
			list($strStamp, $numYear, $numMonth, $numDate, $numHour, $numMin) = $arrMatch;

			$strTimeZone = (-1 * $varsAccount['numTimeZone']) . 'hours';
			$dateTime = new DateTime("$numYear-$numMonth-$numDate 0:0 $strTimeZone", new DateTimeZone("UTC"));
			$stampBook = $dateTime->format('U') + $numHour * 3600 + $numMin * 60;

		}

		preg_replace("/\.?0+$/",'', $stampBook);

		return $stampBook;
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

		$varsLogDelete = $varsLog;
		$stampUpdate = TIMESTAMP;
		$stampRegister = TIMESTAMP;
		$stampBook = $this->_getDbLogStampBook(array(
			'vars'     => $vars,
			'arrValue' => $arrValue,
		));

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = (int) $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$idAccount = $varsAccount['id'];
		if ($arrValue['arr']['flagFiscalReport'] == 'none') {
			$arrValue['arr']['flagFiscalReport'] = '0';
		}
		$flagFiscalReport = $arrValue['arr']['flagFiscalReport'];
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

		$arrCommaIdLogFile = $classEscape->splitCommaArrayData(array('data' => $arrValue['arr']['arrCommaIdLogFile']));
		$arrCommaIdLogFile = $classEscape->joinCommaArray(array('arr' => $arrCommaIdLogFile));

		$arrValue['arr']['stampBook'] = $stampBook;
		$classCalcLog = $this->_getClassCalc(array('flagType' => 'Log'));
		$varsVersion = $classCalcLog->allot(array(
			'flagStatus'      => 'varsVersion',
			'arrValue'        => $arrValue['arr'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrVersion = $varsVersion['arrVersion'][0];
		$arrVersion['jsonDetail']['numVersionConsumptionTax'] = count($varsLog['jsonVersion']);
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

		$varsVersionId = $this->_getVarsVersionId(array(
			'vars' => $varsLog['jsonVersion']
		));
		$arrCommaIdDepartmentVersion = $varsVersionId['arrCommaIdDepartment'];
		$arrCommaIdAccountTitleVersion = $varsVersionId['arrCommaIdAccountTitle'];
		$arrCommaIdSubAccountTitleVersion = $varsVersionId['arrCommaIdSubAccountTitle'];

		$arrPermitHistory = $varsLog['jsonPermitHistory'];

		if ($arrPermitHistory) {
			$array = &$arrPermitHistory;
			foreach ($array as $key => $value) {
				$array[$key]['flagInvalid'] = 1;
			}
		}

		if ($flagApply) {
			$arrPermitHistory[] = array(
				'flagInvalid'        => 0,
				'stampRegister'      => TIMESTAMP,
				'numSumMax'          => (int) $arrValue['arr']['numSumMax'],
				'idAccountApply'     => $idAccount,
				'arrIdAccountPermit' => $this->_getDbLogArrIdAccountPermit($arrValue),
			);
		}

		$jsonPermitHistory = json_encode($arrPermitHistory);
		$this->checkTextSize(array(
			'flag' => 'errorDataMax',
			'str'  => $jsonPermitHistory,
		));

		if (!$flagApply) {
			$arrCommaIdAccountPermit = '';

		} else {
			$arrCommaIdAccountPermit = $classEscape->splitCommaArrayData(array('data' => $arrValue['arr']['arrCommaIdAccountPermit']));
			$arrCommaIdAccountPermit = $classEscape->joinCommaArray(array('arr' => $arrCommaIdAccountPermit));
		}

		$arrayTemp = compact(
			'stampUpdate',
			'stampBook',
			'flagFiscalReport',
			'strTitle',
			'arrSpaceStrTag',
			'flagApply',
			'idAccountApply',
			'flagApplyBack',
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
			'arrCommaIdDepartmentVersion',
			'arrCommaIdAccountTitleVersion',
			'arrCommaIdSubAccountTitleVersion',
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
			'strTable' => 'accountingLog',
			'arrColumn' => $arrColumn,
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
					'flagType'      => 'num',
					'strColumn'     => 'idLog',
					'flagCondition' => 'eq',
					'value'         => $varsRequest['query']['jsonValue']['idTarget'],
				),
			),
			'arrValue'  => $arrValue,
		));

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLog',
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
					'flagType'      => 'num',
					'strColumn'     => 'idLog',
					'flagCondition' => 'eq',
					'value'         => $varsRequest['query']['jsonValue']['idTarget'],
				),
			),
		));
		$varsLog = reset($rows['arrRows']);

		$classCalcAccountTitle = $this->_getClassCalc(array('flagType' => 'AccountTitle'));
		$classCalcSubAccountTitle = $this->_getClassCalc(array('flagType' => 'SubAccountTitle'));
		$classCalcConsumptionTax = $this->_getClassCalc(array('flagType' => 'ConsumptionTax'));
		$classCalcLogCalc = $this->_getClassCalc(array('flagType' => 'LogCalc'));

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		$classCalcTempNextLog = $this->_getClassCalc(array(
			'flagType'   => 'TempNext',
			'flagDetail' => 'Log',
		));
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriodTempNext = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];

		if ((int) $varsLogDelete['flagApply']) {
			if ($flagApply) {

			} else {
				$arrRows = $this->_getVarsLogCalcLoop(array(
					'arrVarsLog'      => array($varsLog),
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
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

				if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
					$flag = $classCalcTempNextLog->allot(array(
						'flagStatus'      => 'add',
						'numFiscalPeriod' => $numFiscalPeriodTempNext,
						'arrRows'         => $arrRows,
					));
					if ($flag) {
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
			$arrRowsDelete = $this->_getVarsLogCalcLoop(array(
				'arrVarsLog'      => array($varsLogDelete),
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			));

			if ($flagApply) {
				$flag = $classCalcAccountTitle->allot(array(
					'flagStatus'      => 'delete',
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					'arrRows'         => $arrRowsDelete,
				));
				if ($flag == 'errorDataMax') {
					$this->sendVars(array(
						'flag'    => $flag,
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(),
					));
				}
				$classCalcSubAccountTitle->allot(array(
					'flagStatus'      => 'delete',
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					'arrRows'         => $arrRowsDelete,
				));
				$classCalcConsumptionTax->allot(array(
					'flagStatus'      => 'delete',
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					'arrRows'         => $arrRowsDelete,
				));
				$classCalcLogCalc->allot(array(
					'flagStatus'      => 'delete',
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					'arrRows'         => $arrRowsDelete,
				));
				if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
					$classCalcTempNextLog = $this->_getClassCalc(array(
						'flagType'   => 'TempNext',
						'flagDetail' => 'Log',
					));
					$flag = $classCalcTempNextLog->allot(array(
						'flagStatus'      => 'delete',
						'numFiscalPeriod' => $numFiscalPeriodTempNext,
						'arrRows'         => $arrRowsDelete,
					));
					if ($flag) {
						$this->sendVars(array(
							'flag'    => $flag,
							'stamp'   => $this->getStamp(),
							'numNews' => $this->getNumNews(),
							'vars'    => array(),
						));
					}
				}

			} else {
				$arrRowsAdd = $this->_getVarsLogCalcLoop(array(
					'arrVarsLog'      => array($varsLog),
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				));
				$flag = $classCalcAccountTitle->allot(array(
					'flagStatus'      => 'edit',
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					'arrRowsAdd'      => $arrRowsAdd,
					'arrRowsDelete'   => $arrRowsDelete,
				));
				if ($flag == 'errorDataMax') {
					$this->sendVars(array(
						'flag'    => $flag,
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(),
					));
				}
				$classCalcSubAccountTitle->allot(array(
					'flagStatus'      => 'edit',
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					'arrRowsAdd'      => $arrRowsAdd,
					'arrRowsDelete'   => $arrRowsDelete,
				));
				$classCalcConsumptionTax->allot(array(
					'flagStatus'      => 'edit',
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					'arrRowsAdd'      => $arrRowsAdd,
					'arrRowsDelete'   => $arrRowsDelete,
				));
				$classCalcLogCalc->allot(array(
					'flagStatus'      => 'edit',
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					'arrRowsAdd'      => $arrRowsAdd,
					'arrRowsDelete'   => $arrRowsDelete,
				));
				if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
					$flag = $classCalcTempNextLog->allot(array(
						'flagStatus'      => 'edit',
						'numFiscalPeriod' => $numFiscalPeriodTempNext,
						'arrRowsAdd'      => $arrRowsAdd,
						'arrRowsDelete'   => $arrRowsDelete,
					));
					if ($flag) {
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
		(array(
			'vars' => $varsLog['jsonVersion']
		))
	 */
	protected function _getVarsVersionId($arr)
	{
		global $classEscape;

		$arrayIdDepartment = array();
		$arrayIdAccountTitle = array();
		$arrayIdSubAccountTitle = array();

		$arrayIdDepartmentCheck = array();
		$arrayIdAccountTitleCheck = array();
		$arrayIdSubAccountTitleCheck = array();

		$array = $arr['vars'];
		foreach ($array as $key => $value) {
			$arrayDetail = $value['jsonDetail']['varsDetail'];
			foreach ($arrayDetail as $keyDetail => $valueDetail) {
				$arrayStr = array('Debit', 'Credit');
				foreach ($arrayStr as $keyStr => $valueStr) {
					$strSide = 'arr' . $valueStr;
					if ($valueDetail[$strSide]['idAccountTitle']) {
						$arrayIdAccountTitleCheck[$valueDetail[$strSide]['idAccountTitle']] = 1;
					}
					if ($valueDetail[$strSide]['idDepartment']) {
						$arrayIdDepartmentCheck[$valueDetail[$strSide]['idDepartment']] = 1;
					}
					if ($valueDetail[$strSide]['idSubAccountTitle']) {
						$arrayIdSubAccountTitleCheck[$valueDetail[$strSide]['idSubAccountTitle']] = 1;
					}
				}
			}
		}
		$array = $arrayIdDepartmentCheck;
		foreach ($array as $key => $value) {
			$arrayIdDepartment[] = $key;
		}
		$array = $arrayIdAccountTitleCheck;
		foreach ($array as $key => $value) {
			$arrayIdAccountTitle[] = $key;
		}
		$array = $arrayIdSubAccountTitleCheck;
		foreach ($array as $key => $value) {
			$arrayIdSubAccountTitle[] = $key;
		}

		$data = array(
			'arrCommaIdDepartment'      => $classEscape->joinCommaArray(array('arr' => $arrayIdDepartment)),
			'arrCommaIdAccountTitle'    => $classEscape->joinCommaArray(array('arr' => $arrayIdAccountTitle)),
			'arrCommaIdSubAccountTitle' => $classEscape->joinCommaArray(array('arr' => $arrayIdSubAccountTitle)),
		);

		return $data;
	}
}
