<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcBanksImport extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extChildSelf = array(
		'varsOption' => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/banks.php',
	);

	/**
	 *
	 */
	public function run()
	{
		if (FLAG_TEST) {
			var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
		}
		exit;
	}

	public function allot($arr)
	{
		$method = '_ini' . ucwords($arr['flagStatus']);
		if (method_exists($this, $method)) {
			return $this->$method($arr);

		} else {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__ . $method);
			}
			exit;
		}
	}

	/**
		 (array(
			'flagStatus' => 'varsItem',
			'idEntity'   => $varsPluginAccountingAccount['idEntityCurrent'],
		 ))
	 */
	protected function _iniVarsItem($arr)
	{
		$varsItem = $this->_extChildSelf['varsItem'][$arr['idEntity']];
		if (!$varsItem) {
			$varsItem = $this->_getVarsItem(array(
				'idEntity' => $arr['idEntity'],
			));
			$this->_extChildSelf['varsItem'][$arr['idEntity']] = $varsItem;
		}

		return $varsItem;
	}

	/**
		(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $arr['idEntity'],
		))
	 */
	protected function _getVarsItem($arr)
	{
		$varsOption = $this->getVars(array(
			'path' => $this->_extChildSelf['varsOption'],
		));

		$data = array(
			'varsOption' => $varsOption,
		);

		$data['classCalcLogImport'] = $this->_getClassCalc(array('flagType' => 'LogImport'));
		$data['classCalcCashPay'] = $this->_getClassCalc(array('flagType' => 'CashPay'));
		$data['classCalcCash'] = $this->_getClassCalc(array('flagType' => 'Cash'));
		$data['classCalcLog'] = $this->_getClassCalc(array('flagType' => 'Log'));

		$data['classCalcAccountTitle'] = $this->_getClassCalc(array('flagType' => 'AccountTitle'));
		$data['classCalcSubAccountTitle'] = $this->_getClassCalc(array('flagType' => 'SubAccountTitle'));
		$data['classCalcConsumptionTax'] = $this->_getClassCalc(array('flagType' => 'ConsumptionTax'));
		$data['classCalcLogCalc'] = $this->_getClassCalc(array('flagType' => 'LogCalc'));
		$data['classCalcTempNextLog'] = $this->_getClassCalc(array(
			'flagType'   => 'TempNext',
			'flagDetail' => 'Log',
		));

		return $data;
	}

	/**
		(array(
			'flagStatus'          => 'checkVarsCSVBanks',
			'flagType'            => 'banksFile',
			'arrayCSV'            => $value['arrayCSV'],
			'strTitle'            => $value['strTitle'],
			'strUrl'              => $value['strUrl'],
			'strTime'             => $strTime,
			'idLogAccount'        => $varsFlag['idLogAccount'],
			'flagBank'            => $varsBanksAccount['flagBank'],
			'classCalcBanks'      => $classCalcBanks,
			'idEntity'            => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
			'idAccount'           => $varsAccount['id'],
		))
	 */
	protected function _iniCheckVarsCSVBanks($arr)
	{
		$varsItem = $this->_iniVarsItem(array(
			'idEntity' => $arr['idEntity'],
		));
		$varsComment = $varsItem['varsOption']['varsComment'];

		$classCalcBanks = &$arr['classCalcBanks'];
		$varsCSV = $classCalcBanks->allot(array(
			'flagStatus'          => 'check',
			'flagType'            => $arr['flagType'],
			'arrayCSV'            => $arr['arrayCSV'],
			'flagBank'            => $arr['flagBank'],
			'idEntity'            => $arr['idEntity'],
			'numFiscalPeriod'     => $arr['numFiscalPeriod'],
			'numFiscalPeriodTemp' => $arr['numFiscalPeriodTemp'],
			'idAccount'           => $arr['idAccount'],
			'arrValue'            => array(
				'arrSpaceStrTag' => $arr['strTitle'],
				'idLogAccount'   => $arr['idLogAccount'],
			),
		));

		$arrPass = &$varsCSV['varsStatus']['arrPass'];
		$array = $varsCSV['varsStatus']['arrPassNumRow'];
		foreach ($array as $key => $value) {
			$numRow = $value;
			$strStatus = $varsComment['strStatus'];
			if ($arr['flagType'] == 'banksWeb') {
				$numRow = $varsCSV['varsStatus']['arrPassTime'][$key];
				$strStatus = $varsComment['strStatusRowBanks'];
			}
			$strStatus = str_replace("<%replace%>", $numRow, $strStatus);
			$strComment = str_replace("<%replace%>", $arr['strTime'], $varsComment[$arrPass[$key]]);
			$arrPass[$key] = $strStatus . $strComment;
		}

		$arrError = &$varsCSV['varsStatus']['arrError'];
		$array = $varsCSV['varsStatus']['arrErrorNumRow'];
		foreach ($array as $key => $value) {
			$numRow = $value;
			$strStatus = $varsComment['strStatus'];
			if ($arr['flagType'] == 'banksWeb') {
				$numRow = $varsCSV['varsStatus']['arrErrorTime'][$key];
				$strStatus = $varsComment['strStatusRowBanks'];
			}
			$strStatus = str_replace("<%replace%>", $numRow, $strStatus);
			$arrError[$key] = $strStatus . $varsComment[$arrError[$key]];
		}

		$data = array(
			'strUrl'     => $arr['strUrl'],
			'strTitle'   => $arr['strTitle'],
			'flagWeb'    => ($arr['flagWeb'])? $arr['flagWeb'] : '',
			'strComment' => ($arr['strComment'])? $arr['strComment'] : '',
		);

		$array = $varsCSV;
		foreach ($array as $key => $value) {
			$data[$key] = $value;
		}

		return $data;
	}

	/**
		(array(
			'flagStatus'          => 'runAdd',
			'arrayDataBanks'      => $arrayDataBanks,
			'idEntity'            => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
			'idAccount'           => $varsAccount['id'],
			'classCalcBanks'      => $classCalcBanks,
		))
	 */
	protected function _iniRunAdd($arr)
	{
		global $varsPluginAccountingEntity;

		$flagVarsData = array(
			'flag'            => '',
			'arrayCSV'        => array(),
			'arrayCSVTemp'    => array(),
			'arrLogBanks'     => array(),
			'arrLogBanksTemp' => array(),
		);

		$classCalcBanks = &$arr['classCalcBanks'];
		if (!$arr['arrayDataBanks']) {
			return $flagVarsData;
		}
		foreach ($arr['arrayDataBanks'] as $key => $value) {
			if ($value['arrValues']) {
				$arrayValue = $value['arrValues'];
				foreach ($arrayValue as $keyValue => $valueValue) {
					$varsLogBanks = $classCalcBanks->allot(array(
						'flagStatus'      => 'add',
						'idEntity'        => $arr['idEntity'],
						'numFiscalPeriod' => $arr['numFiscalPeriod'],
						'arrValue'        => $valueValue,
						'idAccount'       => $arr['idAccount'],
					));

					//for import
					$numRowForCalcLogImport = $varsLogBanks['idLogBanks'] - 1;
					$flagVarsData['arrayCSV']['arrayCSV'][$key][$numRowForCalcLogImport] = $value['arrayCSV'][$keyValue];
					$flagVarsData['arrayCSV']['strTitle'][$key] = $value['strTitle'];
					$flagVarsData['arrayCSV']['flagTemp'][$key] = 0;

					//for write
					$flagVarsData['arrLogBanks'][$varsLogBanks['idLogBanks']] = $varsLogBanks;
				}
			}
			if ($value['arrValuesTemp']) {
				$arrayValue = $value['arrValuesTemp'];
				foreach ($arrayValue as $keyValue => $valueValue) {
					$varsLogBanks = $classCalcBanks->allot(array(
						'flagStatus'      => 'add',
						'idEntity'        => $arr['idEntity'],
						'numFiscalPeriod' => $arr['numFiscalPeriodTemp'],
						'arrValue'        => $valueValue,
						'idAccount'       => $arr['idAccount'],
					));
					$numRowForCalcLogImport = $varsLogBanks['idLogBanks'] - 1;
					$flagVarsData['arrayCSVTemp']['arrayCSV'][$key][$numRowForCalcLogImport] = $value['arrayCSVTemp'][$keyValue];
					$flagVarsData['arrayCSVTemp']['strTitle'][$key] = $value['strTitle'];
					$flagVarsData['arrayCSVTemp']['flagTemp'][$key] = 1;

					//for write
					$flagVarsData['arrLogBanksTemp'][$varsLogBanks['idLogBanks']] = $varsLogBanks;
				}
			}
			if ($value['arrLogCaution']) {
				$arrayLogCaution = $value['arrLogCaution'];
				foreach ($arrayLogCaution as $keyLogCaution => $valueLogCaution) {
					$classCalcBanks->allot(array(
						'flagStatus'      => 'flagUpdate',
						'idEntity'        => $arr['idEntity'],
						'numFiscalPeriod' => $arr['numFiscalPeriod'],
						'idTarget'        => $valueLogCaution['idLogBanks'],
						'flagCaution'     => 1,
					));
				}
			}
			if ($value['arrLogCautionTemp']) {
				$arrayLogCaution = $value['arrLogCautionTemp'];
				foreach ($arrayLogCaution as $keyLogCaution => $valueLogCaution) {
					$classCalcBanks->allot(array(
						'flagStatus'      => 'flagUpdate',
						'idEntity'        => $arr['idEntity'],
						'numFiscalPeriod' => $arr['numFiscalPeriod'],
						'idTarget'        => $valueLogCaution['idLogBanks'],
						'flagCaution'     => 1,
					));
				}
			}

			if ($value['arrLogFlagUpdate']) {
				$arrayLogFlagUpdate = $value['arrLogFlagUpdate'];
				foreach ($arrayLogFlagUpdate as $keyLogFlagUpdate => $valueLogFlagUpdate) {
					$classCalcBanks->allot(array(
						'flagStatus'      => 'flagUpdate',
						'idEntity'        => $arr['idEntity'],
						'numFiscalPeriod' => $arr['numFiscalPeriod'],
						'idTarget'        => $valueLogFlagUpdate['idLogBanks'],
						'flagCaution'     => 0,
					));
				}
			}
		}

		return $flagVarsData;
	}

	/**
		'flagStatus'          => 'checkVarsCSVLog',
		'arrayCSV'            => $value,
		'strTitle'            => $valueLoop['strTitle'][$key],
		'flagTemp'            => $valueLoop['flagTemp'][$key],
		'idEntity'            => $varsPluginAccountingAccount['idEntityCurrent'],
		'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
		'idAccount'           => $varsAccount['id'],
	 */
	protected function _iniCheckVarsCSVLog($arr)
	{
		global $varsAccount;

		$varsItem = $this->_iniVarsItem(array(
			'idEntity' => $arr['idEntity'],
		));
		$varsComment = $varsItem['varsOption']['varsComment'];

		$classCalcLogImport = &$varsItem['classCalcLogImport'];
		$varsCSV = $classCalcLogImport->allot(array(
			'flagStatus'          => 'check',
			'arrOrder'            => $arr['arrayCSV'],
			'idEntity'            => $arr['idEntity'],
			'numFiscalPeriod'     => $arr['numFiscalPeriod'],
			'numFiscalPeriodTemp' => $arr['numFiscalPeriodTemp'],
			'idAccount'           => $arr['idAccount'],
		));

		$arrError = &$varsCSV['varsStatus']['arrError'];
		$array = $varsCSV['varsStatus']['arrErrorNumRow'];
		foreach ($array as $key => $value) {
			$numRow = $value;
			$strStatus = $varsComment['strStatus'];
			$strStatus = str_replace("<%replace%>", $numRow, $strStatus);
			$arrError[$key] = $strStatus . $varsComment[$arrError[$key]];
		}

		$data = array(
			'strTitle'          => $arr['strTitle'],
			'flagTemp'          => $arr['flagTemp'],
			'arrayRequests'     => $varsCSV['arrayRequests'],
			'arrayRequestsTemp' => $varsCSV['arrayRequestsTemp'],
			'varsStatus'        => $varsCSV['varsStatus'],
			'varsRetry'         => $varsCSV['varsRetry'],
		);

		return $data;
	}

	/**
		(array(
			'flagStatus'          => 'runAddLog',
			'flagType'            => 'banksFile',
			'arrayData'           => $arrayData,
			'idEntity'            => $arr['idEntity'],
			'numFiscalPeriod'     => $arr['numFiscalPeriod'],,
			'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
			'flagCurrentFlagNow'  => $flagCurrentFlagNow,
			'idAccount'           => $varsAccount['id'],
			'flagCashInsert'      => $flagCashInsert,
		))
	 */
	protected function _iniRunAddLog($arr)
	{
		global $varsPluginAccountingEntity;

		$varsItem = $this->_iniVarsItem(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $arr['idEntity'],
		));
		$varsComment = $varsItem['varsOption']['varsComment'];

		$flagVarsData = array(
			'flag'                => '',
			'arrVarsLog'          => array(),
			'arrVarsLogTemp'      => array(),
			'arrVarsLogWrite'     => array(),
			'arrVarsLogWriteTemp' => array(),
			'arrayData'           => &$arr['arrayData'],
		);
		$numFiscalPeriodTemp = $arr['numFiscalPeriodTemp'];
		$flagCurrentFlagNow = $arr['flagCurrentFlagNow'];

		$classCalcLogImport = &$varsItem['classCalcLogImport'];
		$classCalcCashPay = &$varsItem['classCalcCashPay'];
		$classCalcCash = &$varsItem['classCalcCash'];
		$classCalcLog = &$varsItem['classCalcLog'];

		$classCalcAccountTitle = &$varsItem['classCalcAccountTitle'];
		$classCalcSubAccountTitle = &$varsItem['classCalcSubAccountTitle'];
		$classCalcConsumptionTax = &$varsItem['classCalcConsumptionTax'];
		$classCalcLogCalc = &$varsItem['classCalcLogCalc'];
		$classCalcTempNextLog = &$varsItem['classCalcTempNextLog'];

		if (!$flagVarsData['arrayData']) {
			return $flagVarsData;
		}

		foreach ($flagVarsData['arrayData'] as $keyData => $valueData) {
			$data = $valueData;
			$strTitle = $data['strTitle'];
			$arrayLog = $data['arrayRequests'];
			foreach ($arrayLog as $keyLog => $valueLog) {
				$numberRow = $valueLog['id'];
				$valueLog['arrSpaceStrTag'] = $varsComment['strTitleTag'];
				$valueLog['arrSpaceStrTag'] .= ' ' . $strTitle;
				$valueLog['arrSpaceStrTag'] .= ' banks:' . $numberRow;
				$valueLog['flagType'] = $arr['flagType'];
				$valueLog['numRow'] = $numberRow;
				$flagCashVars = $classCalcCashPay->allot(array(
					'flagStatus'      => 'check',
					'arrValue'        => $valueLog,
					'idEntity'        => $arr['idEntity'],
					'numFiscalPeriod' => $arr['numFiscalPeriod'],
					'classCalcLog'    => $classCalcLog,
				));
				$dataLog = array();

				if ($flagCashVars['flag'] && $arr['flagCashInsert']) {
					if ($flagCashVars['flag'] == 'pay' || $flagCashVars['flag'] == 'paid') {
						$flagVars = $classCalcCashPay->allot(array(
							'flagStatus'      => $flagCashVars['flag'],
							'arrValue'        => $valueLog,
							'arrRowsCash'     => $flagCashVars['arrRowsCash'],
							'idEntity'        => $arr['idEntity'],
							'numFiscalPeriod' => $arr['numFiscalPeriod'],
							'classCalcCash'   => $classCalcCash,
						));
						if ($flagVars == 'errorDataMax') {
							$flagVarsData['flag'] = $flagVars;
							return $flagVarsData;
						}
						$dataLog = $classCalcLogImport->allot(array(
							'flagStatus'      => 'insertDbLog',
							'arrValue'        => $flagVars['arrValue'],
							'idEntity'        => $arr['idEntity'],
							'numFiscalPeriod' => $arr['numFiscalPeriod'],
							'classCalcLog'    => $classCalcLog,
						));
						if (!$dataLog['flagApply']) {
							$flagVarsData['arrVarsLog'][] = $dataLog;
						}

						$flagVars = $classCalcCashPay->allot(array(
							'flagStatus'      => 'WriteHistory',
							'varsLog'         => $dataLog,
							'varsLogCash'     => $flagVars['arrVarsLogAdd'][0],
							'idEntity'        => $arr['idEntity'],
							'numFiscalPeriod' => $arr['numFiscalPeriod'],
						));
						if ($flagVars == 'errorDataMax') {
							$flagVarsData['flag'] = $flagVars;
							return $flagVarsData;
						}

					} elseif ($flagCashVars['flag'] == 'caution') {
						$flagVarsData['arrayData'][$keyData]['varsStatus']['arrCashDefer'][$numberRow] = 1;
						$flagVars = $classCalcCashPay->allot(array(
							'flagStatus'      => $flagCashVars['flag'],
							'varsLog'         => $flagCashVars['varsLog'],
							'arrRowsCash'     => $flagCashVars['arrRowsCash'],
							'idEntity'        => $arr['idEntity'],
							'numFiscalPeriod' => $arr['numFiscalPeriod'],
						));
					}

				} else {
					$dataLog = $classCalcLogImport->allot(array(
						'flagStatus'      => 'insertDbLog',
						'arrValue'        => $valueLog,
						'idEntity'        => $arr['idEntity'],
						'numFiscalPeriod' => $arr['numFiscalPeriod'],
						'classCalcLog'    => $classCalcLog,
					));
					if (!$dataLog['flagApply']) {
						$flagVarsData['arrVarsLog'][] = $dataLog;
					}
				}
				$flagVarsData['arrVarsLogWrite'][$numberRow] = $dataLog;
			}

			$arrayLog = $data['arrayRequestsTemp'];
			foreach ($arrayLog as $keyLog => $valueLog) {
				$numberRow = $valueLog['id'];
				$valueLog['arrSpaceStrTag'] = $varsComment['strTitleTag'];
				$valueLog['arrSpaceStrTag'] .= ' ' . $strTitle;
				$valueLog['arrSpaceStrTag'] .= ' banks:' . $numberRow;
				$valueLog['flagType'] = $arr['flagType'];
				$valueLog['numRow'] = $numberRow;
				$flagCashVars = $classCalcCashPay->allot(array(
					'flagStatus'      => 'check',
					'arrValue'        => $valueLog,
					'idEntity'        => $arr['idEntity'],
					'numFiscalPeriod' => $numFiscalPeriodTemp,
					'classCalcLog'    => $classCalcLog,
				));
				$dataLog = array();
				if ($flagCashVars['flag'] && $arr['flagCashInsert']) {
					if ($flagCashVars['flag'] == 'pay' || $flagCashVars['flag'] == 'paid') {
						$flagVars = $classCalcCashPay->allot(array(
							'flagStatus'      => $flagCashVars['flag'],
							'arrValue'        => $valueLog,
							'arrRowsCash'     => $flagCashVars['arrRowsCash'],
							'idEntity'        => $arr['idEntity'],
							'numFiscalPeriod' => $numFiscalPeriodTemp,
							'classCalcCash'   => $classCalcCash,
						));
						if ($flagVars == 'errorDataMax') {
							$flagVarsData['flag'] = $flagVars;
							return $flagVarsData;
						}
						$dataLog = $classCalcLogImport->allot(array(
							'flagStatus'      => 'insertDbLog',
							'arrValue'        => $flagVars['arrValue'],
							'idEntity'        => $arr['idEntity'],
							'numFiscalPeriod' => $numFiscalPeriodTemp,
							'classCalcLog'    => $classCalcLog,
						));
						if (!$dataLog['flagApply']) {
							$flagVarsData['arrVarsLogTemp'][] = $dataLog;
						}
						$flagVars = $classCalcCashPay->allot(array(
							'flagStatus'      => 'WriteHistory',
							'varsLog'         => $dataLog,
							'varsLogCash'     => $flagVars['arrVarsLogAdd'][0],
							'idEntity'        => $arr['idEntity'],
							'numFiscalPeriod' => $numFiscalPeriodTemp,
						));
						if ($flagVars == 'errorDataMax') {
							$flagVarsData['flag'] = $flagVars;
							return $flagVarsData;
						}

					} elseif ($flagCashVars['flag'] == 'caution') {
						$flagVarsData['arrayData'][$keyData]['varsStatus']['arrCashDefer'][$numberRow] = 1;
						$flagVars = $classCalcCashPay->allot(array(
							'flagStatus'      => $flagCashVars['flag'],
							'varsLog'         => $flagCashVars['varsLog'],
							'arrRowsCash'     => $flagCashVars['arrRowsCash'],
							'idEntity'        => $arr['idEntity'],
							'numFiscalPeriod' => $numFiscalPeriodTemp,
						));
					}

				} else {
					$dataLog = $classCalcLogImport->allot(array(
						'flagStatus'      => 'insertDbLog',
						'arrValue'        => $valueLog,
						'idEntity'        => $arr['idEntity'],
						'numFiscalPeriod' => $numFiscalPeriodTemp,
						'classCalcLog'    => $classCalcLog,
					));
					if (!$dataLog['flagApply']) {
						$flagVarsData['arrVarsLogTemp'][] = $dataLog;
					}
				}
				$flagVarsData['arrVarsLogWriteTemp'][$numberRow] = $dataLog;
			}

			if ($data['varsRetry']['varsDetail']) {
				$arrSpaceStrTag = $varsComment['strTitleTag'];
				$arrSpaceStrTag .= ' ' . $strTitle;

				$flagVarsData['arrayData'][$keyData]['flagConvertError'] = $classCalcLogImport->allot(array(
					'flagStatus'          => 'insertDbRetry',
					'flagType'            => 'banksFile',
					'arrSpaceStrTag'      => $arrSpaceStrTag,
					'vars'                => $data['varsRetry'],
					'idEntity'            => $arr['idEntity'],
					'idAccount'           => $arr['idAccount'],
					'numFiscalPeriod'     => $arr['numFiscalPeriod'],
					'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
				));
			}
		}

		if ($flagVarsData['arrVarsLog']) {
			$arrRows = $this->_getVarsLogCalcLoop(array(
				'arrVarsLog'      => $flagVarsData['arrVarsLog'],
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
			));

			$flag = $classCalcAccountTitle->allot(array(
				'flagStatus'      => 'add',
				'arrRows'         => $arrRows,
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
			));
			if ($flag == 'errorDataMax') {
				$flagVarsData['flag'] = $flag;
				return $flagVarsData;
			}

			$flag = $classCalcSubAccountTitle->allot(array(
				'flagStatus'      => 'add',
				'arrRows'         => $arrRows,
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
			));
			if ($flag == 'errorDataMax') {
				$flagVarsData['flag'] = $flag;
				return $flagVarsData;
			}

			$flag = $classCalcConsumptionTax->allot(array(
				'flagStatus'      => 'add',
				'arrRows'         => $arrRows,
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
			));
			if ($flag == 'errorDataMax') {
				$flagVarsData['flag'] = $flag;
				return $flagVarsData;
			}

			$classCalcLogCalc->allot(array(
				'flagStatus'      => 'add',
				'arrRows'         => $arrRows,
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
			));

			$idEntity = $arr['idEntity'];
			$numFiscalPeriod = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];
			if ((preg_match("/^(tempPrev)$/", $flagCurrentFlagNow))) {
				$flag = $classCalcTempNextLog->allot(array(
					'flagStatus'      => 'add',
					'numFiscalPeriod' => $numFiscalPeriod,
					'arrRows'         => $arrRows,
				));
				if ($flag == 'errorDataMax') {
					$flagVarsData['flag'] = $flag;
					return $flagVarsData;
				}
			}
			$this->_updateDbPreferenceStamp(array('strColumn' => 'log'));
		}

		if ($flagVarsData['arrVarsLogTemp']) {
			if ((preg_match("/^(tempPrev)$/", $flagCurrentFlagNow))) {
				$numFiscalPeriodTemp = $arr['numFiscalPeriod'] + 1;

			} elseif ((preg_match("/^(tempNext)$/", $flagCurrentFlagNow))) {
				$numFiscalPeriodTemp = $arr['numFiscalPeriod'] - 1;
			}
			$arrRows = $this->_getVarsLogCalcLoop(array(
				'arrVarsLog'      => $flagVarsData['arrVarsLogTemp'],
				'numFiscalPeriod' => $numFiscalPeriodTemp,
			));

			$flag = $classCalcAccountTitle->allot(array(
				'flagStatus'      => 'add',
				'arrRows'         => $arrRows,
				'numFiscalPeriod' => $numFiscalPeriodTemp,
			));
			if ($flag == 'errorDataMax') {
				$flagVarsData['flag'] = $flag;
				return $flagVarsData;
			}

			$flag = $classCalcSubAccountTitle->allot(array(
				'flagStatus'      => 'add',
				'arrRows'         => $arrRows,
				'numFiscalPeriod' => $numFiscalPeriodTemp,
			));
			if ($flag == 'errorDataMax') {
				$flagVarsData['flag'] = $flag;
				return $flagVarsData;
			}

			$flag = $classCalcConsumptionTax->allot(array(
				'flagStatus'      => 'add',
				'arrRows'         => $arrRows,
				'numFiscalPeriod' => $numFiscalPeriodTemp,
			));
			if ($flag == 'errorDataMax') {
				$flagVarsData['flag'] = $flag;
				return $flagVarsData;
			}

			$classCalcLogCalc->allot(array(
				'flagStatus'      => 'add',
				'arrRows'         => $arrRows,
				'numFiscalPeriod' => $numFiscalPeriodTemp,
			));

			//tempNext is not wrong
			if ((preg_match("/^(tempNext)$/", $flagCurrentFlagNow))) {
				$numFiscalPeriod = $arr['numFiscalPeriod'];
				$flag = $classCalcTempNextLog->allot(array(
					'flagStatus'      => 'add',
					'numFiscalPeriod' => $numFiscalPeriod,
					'arrRows'         => $arrRows,
				));
				if ($flag == 'errorDataMax') {
					$flagVarsData['flag'] = $flag;
					return $flagVarsData;
				}
			}
			$this->_updateDbPreferenceStamp(array('strColumn' => 'log'));
		}

		return $flagVarsData;
	}

	/**
		(array(
			'flagStatus'          => 'runWriteHistory',
			'arrayData'           => $flagVarsAddLog['arrayData'],
			'arrLogBanks'         => $flagVarsAdd['arrLogBanks'],
			'arrLogBanksTemp'     => $flagVarsAdd['arrLogBanksTemp'],
			'arrVarsLogWrite'     => $flagVarsAddLog['arrVarsLogWrite'],
			'arrVarsLogWriteTemp' => $flagVarsAddLog['arrVarsLogWriteTemp'],
			'classCalcBanks'      => $classCalcBanks,
			'idEntity'            => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
			'idAccount'           => $varsAccount['id'],
		))
	 */
	protected function _iniRunWriteHistory($arr)
	{
		global $varsPluginAccountingEntity;

		$flagVarsData = array(
			'flag'      => '',
			'arrayData' => &$arr['arrayData'],
		);

		$classCalcBanks = &$arr['classCalcBanks'];
		if (!$flagVarsData['arrayData']) {
			return $flagVarsData;
		}
		foreach ($flagVarsData['arrayData'] as $keyData => $valueData) {
			$data = $valueData;
			$arrayStr = array('arrImport', 'arrCashDefer', 'arrNone');
			foreach ($arrayStr as $keyStr => $valueStr) {
				$arrayId = $data['varsStatus'][$valueStr];
				if ($arrayId) {
					foreach ($arrayId as $keyId => $valueId) {
						$numberRow = $valueId;
						if ($valueData['flagTemp'] === 0) {
							$flag = $classCalcBanks->allot(array(
								'flagStatus'      => 'WriteHistory',
								'varsLog'         => $arr['arrVarsLogWrite'][$numberRow],
								'varsLogBanks'    => $arr['arrLogBanks'][$numberRow],
								'idEntity'        => $arr['idEntity'],
								'numFiscalPeriod' => $arr['numFiscalPeriod'],
								'idAccount'       => $arr['idAccount'],
							));
							if ($flag == 'errorDataMax') {
								$flagVarsData['flag'] = $flag;
								return $flagVarsData;
							}
						}
						if ($valueData['flagTemp'] === 1) {
							$flag = $classCalcBanks->allot(array(
								'flagStatus'      => 'WriteHistory',
								'varsLog'         => $arr['arrVarsLogWriteTemp'][$numberRow],
								'varsLogBanks'    => $arr['arrLogBanksTemp'][$numberRow],
								'idEntity'        => $arr['idEntity'],
								'numFiscalPeriod' => $arr['numFiscalPeriodTemp'],
								'idAccount'       => $arr['idAccount'],
							));
							if ($flag == 'errorDataMax') {
								$flagVarsData['flag'] = $flag;
								return $flagVarsData;
							}
						}
					}
				}
			}
		}

		return $flagVarsData;
	}

	/**
		(array(
			'flagStatus'      => 'logImportWriteHistory',
			'classCalcBanks'  => $classCalcBanks,
			'idLogBanks'      => $arr['idLogBanks'],
			'varsLog'         => $arr['arrVarsLog'][$key],
			'idEntity'        => $arr['idEntity'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idAccount'       => $varsAccount['id'],
		))
	 */
	protected function _iniLogImportWriteHistory($arr)
	{
		global $varsPluginAccountingEntity;

		$classCalcBanks = &$arr['classCalcBanks'];

		$varsLogBanks = $this->_getVarsLogBanks(array(
			'idEntity'        => $arr['idEntity'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idLogBanks'      => $arr['idLogBanks'],
		));
		if (!$varsLogBanks) {
			return;
		}

		$flag = $classCalcBanks->allot(array(
			'flagStatus'      => 'WriteHistory',
			'varsLog'         => $arr['varsLog'],
			'varsLogBanks'    => $varsLogBanks,
			'idEntity'        => $arr['idEntity'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idAccount'       => $arr['idAccount'],
		));

		if ($flag == 'errorDataMax') {
			return $flag;
		}
	}

	/**
		(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $arr['idEntity'],
			'idLogAccount'    => $arr['arrValue']['idLogAccount'],
			'stampBook'       => $stampCheck,
		))
	 */
	protected function _getVarsLogBanks($arr)
	{
		global $classDb;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogBanks',
			'arrLimit' => array(),
			'arrOrder'  => array(),
			'arrOrder'  => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idEntity',
					'flagCondition' => 'eq',
					'value'         => $arr['idEntity'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'numFiscalPeriod',
					'flagCondition' => 'eq',
					'value'         => $arr['numFiscalPeriod'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idLogBanks',
					'flagCondition' => 'eq',
					'value'         => $arr['idLogBanks'],
				),
				array(
					'flagType'      => '',
					'strColumn'     => 'flagRemove',
					'flagCondition' => 'eq',
					'value'         => 0,
				),
			),
		));

		return $rows['arrRows'][0];
	}

	/**
		(array(
			'flagStatus'      => 'varsStatusBanks',
			'flagType'        => 'banksWeb',
			'varsStatus'      => $data['varsStatus'],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		))
	 */
	protected function _iniVarsStatusBanks($arr)
	{
		$varsItem = $this->_iniVarsItem(array(
			'idEntity' => $arr['idEntity'],
		));
		$varsComment = $varsItem['varsOption']['varsComment'];

		$data = array();
		$data['replaceAll'] = $arr['varsStatus']['numAll'];
		$data['replaceAllImport'] = count($arr['varsStatus']['arrImport']);
		$data['replaceAllPass'] = count($arr['varsStatus']['arrPass']);
		$data['replaceAllError'] = count($arr['varsStatus']['arrError']);

		$arrImport = array();
		$array = $arr['varsStatus']['arrImport'];
		foreach ($array as $key => $value) {
			$strStatusRow = $varsComment['strStatusRow'];
			if ($arr['flagType'] == 'banksWeb') {
				$strStatusRow = $varsComment['strStatusRowBanks'];
				$value = $arr['varsStatus']['arrImportTime'][$key];
			}
			$arrImport[] = str_replace("<%replace%>", $value, $strStatusRow);
		}
		if (!$arrImport) {
			$strImport = $varsComment['strStatusNone'];
		} else {
			$strImport = join(' ', $arrImport);
		}
		$data['replaceImport'] = $strImport;


		$arrPass = array();
		$array = $arr['varsStatus']['arrPass'];
		foreach ($array as $key => $value) {
			$strStatusRow = $varsComment['strStatusRowPass'];
			$arrPass[] = str_replace("<%replace%>", $value, $strStatusRow);
		}
		if (!$arrPass) {
			$strPass = $varsComment['strStatusNone'];
		} else {
			$strPass = join(' ', $arrPass);
		}
		$data['replacePass'] = $strPass;

		$arrError = array();
		$array = $arr['varsStatus']['arrError'];
		foreach ($array as $key => $value) {
			$strStatusRowError = $varsComment['strStatusRowError'];
			$arrError[] = str_replace("<%replace%>", $value, $strStatusRowError);
		}
		if (!$arrError) {
			$strError = $varsComment['strStatusNone'];
		} else {
			$strError = join(' ', $arrError);
		}
		$data['replaceError'] = $strError;

		return $data;
	}

	/**
		(array(

			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $arr['idEntity'],
			'varsStatus' => $data['varsStatus'],
		))
	 */
	protected function _iniVarsStatusLog($arr)
	{
		$varsItem = $this->_iniVarsItem(array(
			'idEntity' => $arr['idEntity'],
		));
		$varsComment = $varsItem['varsOption']['varsComment'];

		$data = array();
		$data['replaceAll'] = $arr['varsStatus']['numAll'];

		$data['replaceAllImport'] = count($arr['varsStatus']['arrImport']);
		$data['replaceAllNone'] = count($arr['varsStatus']['arrNone']);
		$data['replaceAllError'] = count($arr['varsStatus']['arrError']);

		$arrImport = array();
		$array = $arr['varsStatus']['arrImport'];
		foreach ($array as $key => $value) {
			$strStatusRow = $varsComment['strStatusRowBanks'];
			$strStatusRowCash = $varsComment['strStatusRowCashBanks'];
			if ($arr['varsStatus']['arrCashDefer'][$value]) {
				$arrImport[] = str_replace("<%replace%>", $value, $strStatusRowCash);
			} else {
				$arrImport[] = str_replace("<%replace%>", $value, $strStatusRow);
			}
		}
		if (!$arrImport) {
			$strImport = $varsComment['strStatusNone'];
		} else {
			$strImport = join(' ', $arrImport);
		}
		$data['replaceImport'] = $strImport;

		$arrError = array();
		$array = $arr['varsStatus']['arrError'];
		foreach ($array as $key => $value) {
			$strStatusRowError = $varsComment['strStatusRowError'];
			$arrError[] = str_replace("<%replace%>", $value, $strStatusRowError);
		}
		if (!$arrError) {
			$strError = $varsComment['strStatusNone'];
		} else {
			$strError = join(' ', $arrError);
		}
		$data['replaceError'] = $strError;

		$arrNone = array();
		$array = $arr['varsStatus']['arrNone'];
		foreach ($array as $key => $value) {
			$strStatusRow = $varsComment['strStatusRowBanks'];
			$arrNone[] = str_replace("<%replace%>", $value, $strStatusRow);
		}
		if (!$arrNone) {
			$strNone = $varsComment['strStatusNone'];
		} else {
			$strNone = join(' ', $arrNone);
		}
		$data['replaceNone'] = $strNone;

		return $data;
	}
}
