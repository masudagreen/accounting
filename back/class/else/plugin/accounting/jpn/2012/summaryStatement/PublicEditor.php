<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_2012_SummaryStatement_PublicEditor extends Code_Else_Plugin_Accounting_Jpn_2012_SummaryStatement_Public
{
	protected $_childSelf = array(
		'pathTplJs'   => 'else/plugin/accounting/js/jpn/2012/summaryStatement/publicEditor.js',
		'pathVarsJs'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/2012/summaryStatement/publicEditor.php',
		'varsDefaultPL'  => 'back/tpl/templates/else/plugin/accounting/dat/2012/summaryStatement/publicPL.php',
		'varsDefaultCR'  => 'back/tpl/templates/else/plugin/accounting/dat/2012/summaryStatement/publicCR.php',
		'varsDefaultBS'  => 'back/tpl/templates/else/plugin/accounting/dat/2012/summaryStatement/publicBS.php',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;

		$flag = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done|tempNext)$/", $flag)) {
			$this->_sendOld();
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
		$this->_setJsEditor(array(
			'pathVars'  => $this->_childSelf['pathVarsJs'],
			'pathTpl'   => $this->_childSelf['pathTplJs'],
			'arrFolder' => array(),
		));
	}

	/**
	 *
	 */
	protected function _iniDetailCalc()
	{
		global $classDb;
		global $classEscape;

		global $varsRequest;
		global $varsAccount;
		$dbh = $classDb->getHandle();
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$varsAuthority = $this->_getVarsAuthority(array());

		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'])) {
			$this->_sendOld();
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsFlag = array(
			'flagMenu' => $varsRequest['query']['jsonValue']['vars']['VarsFlag']['flagMenu'],
		);

		if (!preg_match("/^(7PL|7BS|17Sales|17Purchase|17Outsourcing|17Employee)$/", $varsFlag['flagMenu'])) {
			$this->sendVars(array(
				'flag'    => 1,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(
					'varsValue' => array(),
				),
			));
		}

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
			'FlagMenu'   => $varsFlag['flagMenu'],
		));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		));

		$arrValue = $this->_checkValueDetailList(array(
			'varsItem' => $varsItem,
			'varsList' => $vars['varsItem']['varsList'],
			'varsData' => $varsRequest['query']['jsonValue']['vars']['JsonData'],
			'varsFlag' => $varsFlag,
		));

		$varsValue = $this->_getVarsValue(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsList' => $vars['varsItem']['varsList'],
			'varsFlag' => $varsFlag,
		));

		$flag = $this->_checkVarsValue(array(
			'varsValue' => &$varsValue,
		));

		$this->sendVars(array(
			'flag'    => $flag,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'varsValue' => $varsValue,
			),
		));
	}

	/**
			'varsList' => $vars['varsItem']['varsList'],
			'varsData' => $varsRequest['query']['jsonValue']['vars']['JsonData'],
			'varsFlag' => $varsFlag,
	 */
	protected function _checkVarsValue($arr)
	{
		global $classCheck;

		$flagError = 0;
		$array = &$arr['varsValue'];
		foreach ($array as $key => $value) {
			if ($value == '') {
				continue;
			}
			$flag = $classCheck->checkValueMax(array(
				'flagType' => 'str',
				'value'    => $value,
				'num'      => 9,
			));
			if ($flag) {
				$array[$key] = '';
				$flagError = 1;
			}
		}
		if ($flagError) {
			return 'strOver';
		}
		return 1;
	}

	/**
			'varsList' => $vars['varsItem']['varsList'],
			'varsData' => $varsRequest['query']['jsonValue']['vars']['JsonData'],
			'varsFlag' => $varsFlag,
	 */
	protected function _getVarsValue($arr)
	{
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsFSValue = $this->_getVarsFSValue(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$numFiscalPeriodStart = $varsPluginAccountingEntity[$varsPluginAccountingAccount['idEntityCurrent']]['numFiscalPeriodStart'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] - 1;
		$varsFSValuePrev = array();
		if ($numFiscalPeriodStart <= $numFiscalPeriod) {
			$varsFSValuePrev = $this->_getVarsFSValue(array(
				'numFiscalPeriod' => $numFiscalPeriod,
			));
		}


		$varsSave = $this->_getVarsSave(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagReport'      => $this->_extSelf['flagReport'],
			'flagDetail'      => $this->_extSelf['flagDetail'],
		));

		$arrayFSList = $this->_getFSList(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsCommon = $arr['varsItem']['varsCommon'];
		$array = $varsCommon['arrSelectTag']['7'];
		foreach ($array as $key => $value) {
			$varsCommon['arrStrTitle']['7'][$value['value']] = $value;
		}
		$array = $arr['varsItem']['varsCommon']['arrSelectTag']['17'];
		foreach ($array as $key => $value) {
			$varsCommon['arrStrTitle']['17'][$value['value']] = $value;
		}


		$varsValue = array();
		$array = $arrayFSList;
		foreach ($array as $key => $value) {
			$varsDefault = $this->getVars(array(
				'path' => $this->_childSelf['varsDefault' . $key],
			));
			$str = 'jsonJgaapAccountTitle'. $key;
			$this->_loopVarsValue(array(
				'varsItem'        => $arr['varsItem'],
				'varsValue'       => &$varsValue,
				'varsCommon'      => $varsCommon,
				'varsFS'          => $varsFS[$str],
				'varsFSValue'     => $varsFSValue[$str],
				'varsFSValuePrev' => $varsFSValuePrev[$str],
				'varsSave'        => ($varsSave[$str])? $varsSave[$str] : array(),
				'varsDefault'     => $varsDefault,
				'varsFlag'        => $arr['varsFlag'],
			));
		}

		$varsValue = $this->_getVarsValueList(array(
			'varsList'    => $arr['vars']['varsItem']['varsList'],
			'varsValue'   => $varsValue,
			'varsFlag'    => $arr['varsFlag'],
		));

		if (preg_match("/^7BS$/", $arr['varsFlag']['flagMenu'])) {
			$array = array('assetsSum', 'liabilitiesSum', 'netAssetsSum');
			$arrayKey = array('assetsSum', 'liabilitiesSum', 'netAssetsSum');
			foreach ($array as $key => $value) {
				$idTarget = ucwords($arrayKey[$key]);
				$numNext = $varsFSValue['jsonJgaapAccountTitleBS']['f1'][ $value]['sumNext'];
				$varsValue[$idTarget] = (is_null($numNext) || $numNext == 0)? '' : $numNext;
			}

		} elseif (preg_match("/^7PL$/", $arr['varsFlag']['flagMenu'])) {
			$array = array(
				'grossProfitOrLossNet',
				'operatingIncomeProfitOrLossNet',
				'currentTermProfitOrLossPreNet',
				'costOfSalesSum',
			);
			$arrayKey = array(
				'grossProfit',
				'operatingIncome',
				'currentTerm',
				'costOfSales',
			);
			foreach ($array as $key => $value) {
				$idTarget = ucwords($arrayKey[$key]);
				$numNext = $varsFSValue['jsonJgaapAccountTitlePL']['f1'][ $value]['sumNext'];
				$varsValue[$idTarget] = (is_null($numNext) || $numNext == 0)? '' : $numNext;
			}

			//SalesSide
			if ($varsValue['SalesSide'] != '') {
				if (is_null($varsValue['Sales'])) {
					$varsValue['Sales'] = $varsValue['SalesSide'];
				} else {
					$varsValue['Sales'] += $varsValue['SalesSide'];
				}
			}
		}

		$array = &$varsValue;
		foreach ($array as $key => $value) {
			if ($value == '') {
				continue;
			}
			$num = $this->_updateCalc(array(
				'flagType' => 'floor',
				'num'      => $value / 1000,
				'numLevel' => 0
			));
			if ($num == 0) {
				$num = '';
			}
			$array[$key] = $num;
		}

		if (preg_match("/^7BS$/", $arr['varsFlag']['flagMenu'])) {
			if ($varsValue['AssetsSum'] != '') {
				if ($varsValue['NetAssetsSum'] != ($varsValue['AssetsSum'] - $varsValue['LiabilitiesSum'])) {
					$varsValue['NetAssetsSum'] = $varsValue['AssetsSum'] - $varsValue['LiabilitiesSum'];
				}
			}
		}

		return $varsValue;
	}


	/**
			'varsList' => $vars['varsItem']['varsList'],
			'varsData' => $varsRequest['query']['jsonValue']['vars']['JsonData'],
			'varsFlag' => $varsFlag,
	 */
	protected function _getVarsValueList($arr)
	{
		global $classEscape;

		$varsValue = array();
		$array = $arr['varsList'];
		foreach ($array as $key => $value) {
			if ($value['flagValueType'] == 'str') {
				continue;
			}
			$idTarget = $classEscape->toLower(array('str' => $value['id']));

			if (preg_match("/^7/", $arr['varsFlag']['flagMenu'])) {
				$data = $arr['varsValue']['7'][$idTarget];
			} elseif (preg_match("/^17/", $arr['varsFlag']['flagMenu'])) {
				$data = $arr['varsValue']['17'][$idTarget];
			}
			if (is_null($data) || $data == 0) {
				$varsValue[$value['id']] = '';

			} else {
				$varsValue[$value['id']] = $data;
			}
		}

		return $varsValue;

	}

	/**
		(array(
			'varsItem'    => $arr['varsItem'],
			'varsFlag'     => $arr['varsFlag'],
			'varsValue'    => &$varsValue,
			'varsFS'       => $varsFS[$str],
			'varsFSValue'  => $varsFSValue[$str],
			'varsFSValuePrev' => $varsFSValuePrev[$str],
			'flagFS'       => $value,
			'varsSave'     => ($varsSave[$str])? $varsSave[$str] : array(),
			'varsDefault'  => $varsDefault,
		));
	 */
	protected function _loopVarsValue($arr)
	{

		$array = &$arr['varsFS'];
		foreach ($array as $key => $value) {
			if (!is_null($value['vars']['varsValue'])) {
				if (!($value['vars']['flagCalc'] == 'sum' || $value['vars']['flagCalc'] == 'net')) {
					$idTarget = $value['vars']['idTarget'];
					$varsTarget = $arr['varsFSValue']['f1'][$idTarget];
					$varsTargetPrev = $arr['varsFSValuePrev']['f1'][$idTarget];

					$varsType = array();
					if ($arr['varsDefault'][$idTarget]) {
						$varsType = $arr['varsDefault'][$idTarget];
					}

					if ($arr['varsSave'][$idTarget]) {
						$varsType = $arr['varsSave'][$idTarget];
					}

					$flag7 = $varsType['flag7'];
					$flag17 = $varsType['flag17'];

					if (preg_match("/^7/", $arr['varsFlag']['flagMenu'])) {
						if ($flag7 != 'none') {
							$numNext = $varsTarget['sumNext'];
							if (is_null($numNext)) {
								$numNext = 0;
							}
							$dataFlag = $arr['varsCommon']['arrStrTitle']['7'][$flag7];
							if ($dataFlag['flagDebit'] != $value['vars']['flagDebit']) {
								$numNext *= -1;
							}
							if (is_null($arr['varsValue']['7'][$flag7])) {
								$arr['varsValue']['7'][$flag7] = $numNext;
							} else {
								$arr['varsValue']['7'][$flag7] += $numNext;
							}
						}

					} elseif (preg_match("/^17/", $arr['varsFlag']['flagMenu'])) {
						if ($flag17 != 'none') {
							$flagDebit = $arr['varsCommon']['vars17FlagDebit'][$arr['varsFlag']['flagMenu']];

							if (($arr['varsFlag']['flagMenu'] == '17Sales' && preg_match("/^(sales)/", $flag17))
								|| ($arr['varsFlag']['flagMenu'] == '17Purchase' && preg_match("/^(purchase)/", $flag17))
							) {
								$strNum = '';
								if (preg_match("/1$/", $flag17)) {
									$strNum = 1;
								} elseif (preg_match("/2$/", $flag17)) {
									$strNum = 2;
								}

								//month
								$arrayMonth = $arr['varsItem']['varsMonths'];
								foreach ($arrayMonth as $keyMonth => $valueMonth) {
									if ($valueMonth['id'] == 'Blank') {
										continue;
									}
									$numNext = $arr['varsFSValue'][$valueMonth['id']][$idTarget]['sumNext'];
									if (is_null($numNext)) {
										$numNext = 0;
									}


									if ($flagDebit != $value['vars']['flagDebit']) {
										$numNext *= -1;
									}
									$str = $strNum . 'NumValue' . $valueMonth['id'];
									if (is_null($arr['varsValue']['17'][$str])) {
										$arr['varsValue']['17'][$str] = $numNext;
									} else {
										$arr['varsValue']['17'][$str] += $numNext;
									}
								}

								//sum
								$numNext = $varsTarget['sumNext'];
								if (is_null($numNext)) {
									$numNext = 0;
								}
								if ($flagDebit != $value['vars']['flagDebit']) {
									$numNext *= -1;
								}

								$str = 'sum' . $strNum;
								if (is_null($arr['varsValue']['17'][$str])) {
									$arr['varsValue']['17'][$str] = $numNext;
								} else {
									$arr['varsValue']['17'][$str] += $numNext;
								}

								//sumPrev
								$numNext = $varsTargetPrev['sumNext'];
								if (is_null($numNext)) {
									$numNext = 0;
								}
								if ($flagDebit != $value['vars']['flagDebit']) {
									$numNext *= -1;
								}
								$str = 'sumPrev' . $strNum;
								if (is_null($arr['varsValue']['17'][$str])) {
									$arr['varsValue']['17'][$str] = $numNext;

								} else {
									$arr['varsValue']['17'][$str] += $numNext;
								}


							} elseif (($arr['varsFlag']['flagMenu'] == '17Outsourcing' && preg_match("/^(outsourcing)/", $flag17))
								|| ($arr['varsFlag']['flagMenu'] == '17Employee' && preg_match("/^(employee)/", $flag17))
							) {
								//month
								$arrayMonth = $arr['varsItem']['varsMonths'];
								foreach ($arrayMonth as $keyMonth => $valueMonth) {
									if ($valueMonth['id'] == 'Blank') {
										continue;
									}
									$numNext = $arr['varsFSValue'][$valueMonth['id']][$idTarget]['sumNext'];
									if (is_null($numNext)) {
										$numNext = 0;
									}

									if ($flagDebit != $value['vars']['flagDebit']) {
										$numNext *= -1;
									}
									$str = 'numValue' . $valueMonth['id'];
									if (is_null($arr['varsValue']['17'][$str])) {
										$arr['varsValue']['17'][$str] = $numNext;
									} else {
										$arr['varsValue']['17'][$str] += $numNext;
									}
								}

								//sum
								$numNext = $varsTarget['sumNext'];
								if (is_null($numNext)) {
									$numNext = 0;
								}
								if ($flagDebit != $value['vars']['flagDebit']) {
									$numNext *= -1;
								}

								$str = 'sum';
								if (is_null($arr['varsValue']['17'][$str])) {
									$arr['varsValue']['17'][$str] = $numNext;
								} else {
									$arr['varsValue']['17'][$str] += $numNext;
								}

								//sumPrev
								$numNext = $varsTargetPrev['sumNext'];
								if (is_null($numNext)) {
									$numNext = 0;
								}
								if ($flagDebit != $value['vars']['flagDebit']) {
									$numNext *= -1;
								}
								$str = 'sumPrev';
								if (is_null($arr['varsValue']['17'][$str])) {
									$arr['varsValue']['17'][$str] = $numNext;

								} else {
									$arr['varsValue']['17'][$str] += $numNext;
								}
							}
						}
					}
				}

			}
			if ($value['child']) {
				$array[$key]['child'] = $this->_loopVarsValue(array(
					'varsItem'        => $arr['varsItem'],
					'varsFS'          => $array[$key]['child'],
					'varsValue'       => &$arr['varsValue'],
					'varsFSValue'     => $arr['varsFSValue'],
					'varsFSValuePrev' => $arr['varsFSValuePrev'],
					'varsCommon'      => $arr['varsCommon'],
					'varsSave'        => $arr['varsSave'],
					'varsDefault'     => $arr['varsDefault'],
					'varsFlag'        => $arr['varsFlag'],
				));
			}
		}

		return $array;
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

		$varsAuthority = $this->_getVarsAuthority(array());

		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'])) {
			$this->_sendOld();
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsFlag = array(
			'flagMenu' => $varsRequest['query']['jsonValue']['vars']['VarsFlag']['flagMenu'],
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
			'FlagMenu'   => $varsFlag['flagMenu'],
		));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		));

		$arrValue = $this->_checkValueDetailList(array(
			'varsItem' => $varsItem,
			'varsList' => $vars['varsItem']['varsList'],
			'varsData' => $varsRequest['query']['jsonValue']['vars']['JsonData'],
			'varsFlag' => $varsFlag,
		));


		try {
			$dbh->beginTransaction();

			$this->_updateDbLog(array(
				'vars'     => $vars,
				'varsItem' => $varsItem,
				'arrValue' => $arrValue,
				'varsFlag' => $varsFlag,
			));
			$this->_updateDbPreferenceStamp(array('strColumn' => 'summryStatement'));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$this->sendVars(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(),
		));
	}

	/**
			'varsList' => $vars['varsItem']['varsList'],
			'varsData' => $varsRequest['query']['jsonValue']['vars']['JsonData'],
			'varsFlag' => $varsFlag,
	 */
	protected function _checkValueDetailList($arr)
	{
		$arrayCheck = array();
		$array = $arr['varsList'];
		foreach ($array as $key => $value) {
			$arrayCheck[$value['idTarget']] = $value;
		}
		$numAll = count($arrayCheck);

		$varsValue = array();
		$array = $arr['varsData'];

		foreach ($array as $key => $value) {
			$data = $arrayCheck[$key];
			if (is_null($data)) {
				$this->_sendOld();
			}
			$data['value'] = $value;
			$numAll--;
			$dataValue = $this->checkValue(array(
				'values' => array($data),
			));
			if ($data['flagValueType'] == 'select') {
				$arrayOption = $data['arrayOption'];
				$flag = 0;
				foreach ($arrayOption as $keyOption => $valueOption) {
					if ($data['value'] == $valueOption['value']) {
						$flag = 1;
						break;
					}
				}
				if (!$flag) {
					$this->_sendOld();
				}

			} elseif ($data['flagValueType'] == 'rate') {
				if ($data['value'] != '') {
					if (!preg_match("/^[0-9]{1,3}\.[0-9]{2,2}$/", $data['value'])) {
						$this->_sendOld();
					}
				}
			}
			if ($data['flagForm'] == 'active') {
				$varsValue[$key] = $value;
			}
		}

		if ($numAll != 0) {
			$this->_sendOld();
		}

		$varsData = $arr['varsItem']['varsSave']['jsonData'];

		if (!$varsData) {
			$varsData = array();
		}
		$varsData[$arr['varsFlag']['flagMenu']] = $varsValue;

		$arrValue['arr']['jsonData'] = $varsData;

		return $arrValue;

	}

	/**

	 */
	protected function _updateDbLog($arr)
	{
		global $classEscape;
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsRequest;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$stampRegister = TIMESTAMP;
		$stampUpdate = TIMESTAMP;
		$flagReport = $this->_extSelf['flagReport'];
		$flagDetail = $this->_extSelf['flagDetail'];
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];

		$jsonData = json_encode($arr['arrValue']['arr']['jsonData']);

		//update
		if ($arr['varsItem']['varsSave']) {
			$arrColumn = array(
				'stampUpdate',
				'jsonData',
			);
			$arrValue = array(
				$stampUpdate,
				$jsonData,
			);
			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingSummaryStatement' . $strNation,
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
				),
				'arrValue'  => $arrValue,
			));

		//insert
		} else {
			$arrColumn = array(
				'stampRegister',
				'stampUpdate',
				'idEntity',
				'numFiscalPeriod',
				'flagReport',
				'flagDetail',
				'jsonData',
			);
			$arrValue = array(
				$stampRegister,
				$stampUpdate,
				$idEntity,
				$numFiscalPeriod,
				$flagReport,
				$flagDetail,
				$jsonData,
			);

			$id = $classDb->insertRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingSummaryStatement' . $strNation,
				'arrColumn' => $arrColumn,
				'arrValue'  => $arrValue,
			));
		}

	}
}
