<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_API_Jpn_LogImport extends Code_Else_Plugin_Accounting_Jpn_API
{
	protected $_extSelf = array(

	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;

		$method = '_ini' . $varsRequest['query']['api']['method'];
		$this->$method();
		exit;
	}

	/**
		'session' => string,
		'module'  => 'accounting',
		'method'  => 'setCSVLog',
		'params'  => array(
			'idEntity' => 1,
			'arrCSV' => array(),
		),
	 */
	protected function _iniSetCSVLog()
	{
		global $classDb;
		global $varsRequest;
		$dbh = $classDb->getHandle();
		global $classEscape;

		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$this->_checkParams();

		$idEntity = $varsRequest['query']['api']['params']['idEntity'];
		$arrCSV = $varsRequest['query']['api']['params']['arrCSV'];
		$varsEntity = $varsPluginAccountingEntity[$idEntity];
		if (!$varsEntity) {
			$this->_sendJSON(array('data' => array('flag' => 'entityNotExist',)));
		}
		$varsPluginAccountingAccount['idEntityCurrent'] = $idEntity;
		$varsPluginAccountingAccount['numFiscalPeriodCurrent'] = $varsEntity['numFiscalPeriod'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];

		$arrSpaceStrTag = $varsRequest['query']['api']['params']['arrSpaceStrTag'];

		if ($arrSpaceStrTag && is_array($arrSpaceStrTag)) {
			$arrSpaceStrTag[] = 'API';
		} else {
			$arrSpaceStrTag = array('API');
		}
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));

		$arrIdTarget = array();
		$arrVarsLog = array();
		$arrVarsLogTemp = array();

		$classCalcLogImport = $this->_getClassCalc(array('flagType' => 'LogImport'));
		$classCalcCashPay = $this->_getClassCalc(array('flagType' => 'CashPay'));
		$classCalcCash = $this->_getClassCalc(array('flagType' => 'Cash'));
		$classCalcLog = $this->_getClassCalc(array('flagType' => 'Log'));
		$numFiscalPeriodTemp = $this->_getNumFiscalPeriodTemp();
		$classDb->setDbhMaster();
		try {
			$dbh->beginTransaction();

			$data = $this->_checkVarsCSV(array(
				'arrCSV'             => $arrCSV,
				'classCalcLogImport' => $classCalcLogImport,
			));

			$arrayLog = $data['arrayRequests'];
			foreach ($arrayLog as $keyLog => $valueLog) {
				$valueLog['stampArrive'] = TIMESTAMP;
				$valueLog['arrSpaceStrTag'] = $arrSpaceStrTag;

				$flagCashVars = $classCalcCashPay->allot(array(
					'flagStatus'      => 'check',
					'arrValue'        => $valueLog,
					'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					'classCalcLog'    => $classCalcLog,
				));

				if ($flagCashVars['flag']) {
					if ($flagCashVars['flag'] == 'pay' || $flagCashVars['flag'] == 'paid') {
						$flagVars = $classCalcCashPay->allot(array(
							'flagStatus'      => $flagCashVars['flag'],
							'arrValue'        => $valueLog,
							'arrRowsCash'     => $flagCashVars['arrRowsCash'],
							'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
							'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
							'classCalcCash'   => $classCalcCash,
						));
						if ($flagVars == 'errorDataMax') {
							$this->_sendJSON(array('data' => array('flag' => $flagVars),));
						}
						$dataLog = $classCalcLogImport->allot(array(
							'flagStatus'      => 'insertDbLog',
							'arrValue'        => $flagVars['arrValue'],
							'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
							'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
							'classCalcLog'    => $classCalcLog,
						));
						$arrIdTarget[$dataLog['idLog']] = 1;
						if (!$dataLog['flagApply']) {
							$arrVarsLog[] = $dataLog;
						}

						$flagVars = $classCalcCashPay->allot(array(
							'flagStatus'      => 'WriteHistory',
							'varsLog'         => $dataLog,
							'varsLogCash'     => $flagVars['arrVarsLogAdd'][0],
							'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
							'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
						));
						if ($flagVars == 'errorDataMax') {
							$this->_sendJSON(array('data' => array('flag' => $flagVars),));
						}

					} elseif ($flagCashVars['flag'] == 'caution') {
						$flagVars = $classCalcCashPay->allot(array(
							'flagStatus'      => $flagCashVars['flag'],
							'varsLog'         => $flagCashVars['varsLog'],
							'arrRowsCash'     => $flagCashVars['arrRowsCash'],
							'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
							'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
						));
					}

				} else {
					$dataLog = $classCalcLogImport->allot(array(
						'flagStatus'      => 'insertDbLog',
						'arrValue'        => $valueLog,
						'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
						'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
						'classCalcLog'    => $classCalcLog,
					));
					$arrIdTarget[$dataLog['idLog']] = 1;
					if (!$dataLog['flagApply']) {
						$arrVarsLog[] = $dataLog;
					}
				}
			}

			$arrayLog = $data['arrayRequestsTemp'];
			foreach ($arrayLog as $keyLog => $valueLog) {
				$valueLog['stampArrive'] = TIMESTAMP;
				$valueLog['arrSpaceStrTag'] = $arrSpaceStrTag;

				$flagCashVars = $classCalcCashPay->allot(array(
					'flagStatus'      => 'check',
					'arrValue'        => $valueLog,
					'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
					'numFiscalPeriod' => $numFiscalPeriodTemp,
					'classCalcLog'    => $classCalcLog,
				));
				if ($flagCashVars['flag']) {
					if ($flagCashVars['flag'] == 'pay' || $flagCashVars['flag'] == 'paid') {
						$flagVars = $classCalcCashPay->allot(array(
							'flagStatus'      => $flagCashVars['flag'],
							'arrValue'        => $valueLog,
							'arrRowsCash'     => $flagCashVars['arrRowsCash'],
							'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
							'numFiscalPeriod' => $numFiscalPeriodTemp,
							'classCalcCash'   => $classCalcCash,
						));
						if ($flagVars == 'errorDataMax') {
							$this->_sendJSON(array('data' => array('flag' => $flagVars),));
						}
						$dataLog = $classCalcLogImport->allot(array(
							'flagStatus'      => 'insertDbLog',
							'arrValue'        => $flagVars['arrValue'],
							'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
							'numFiscalPeriod' => $numFiscalPeriodTemp,
							'classCalcLog'    => $classCalcLog,
						));
						$arrIdTarget[$dataLog['idLog']] = 1;
						if (!$dataLog['flagApply']) {
							$arrVarsLogTemp[] = $dataLog;
						}
						$flagVars = $classCalcCashPay->allot(array(
							'flagStatus'      => 'WriteHistory',
							'varsLog'         => $dataLog,
							'varsLogCash'     => $flagVars['arrVarsLogAdd'][0],
							'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
							'numFiscalPeriod' => $numFiscalPeriodTemp,
						));
						if ($flagVars == 'errorDataMax') {
							$this->_sendJSON(array('data' => array('flag' => $flagVars),));
						}

					} elseif ($flagCashVars['flag'] == 'caution') {
						$flagVars = $classCalcCashPay->allot(array(
							'flagStatus'      => $flagCashVars['flag'],
							'varsLog'         => $flagCashVars['varsLog'],
							'arrRowsCash'     => $flagCashVars['arrRowsCash'],
							'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
							'numFiscalPeriod' => $numFiscalPeriodTemp,
						));
					}

				} else {
					$dataLog = $classCalcLogImport->allot(array(
						'flagStatus'      => 'insertDbLog',
						'arrValue'        => $valueLog,
						'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
						'numFiscalPeriod' => $numFiscalPeriodTemp,
						'classCalcLog'    => $classCalcLog,
					));
					$arrIdTarget[$dataLog['idLog']] = 1;
					if (!$dataLog['flagApply']) {
						$arrVarsLogTemp[] = $dataLog;
					}
				}
			}

			if ($data['varsRetry']['varsDetail']) {
				$classCalcLogImport->allot(array(
					'flagStatus'          => 'insertDbRetry',
					'flagType'            => 'api',
					'arrSpaceStrTag'      => $arrSpaceStrTag,
					'vars'                => $data['varsRetry'],
					'idEntity'            => $varsPluginAccountingAccount['idEntityCurrent'],
					'idAccount'           => $varsPluginAccountingAccount['idAccount'],
					'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
				));
			}

			$classCalcAccountTitle = $this->_getClassCalc(array('flagType' => 'AccountTitle'));
			$classCalcSubAccountTitle = $this->_getClassCalc(array('flagType' => 'SubAccountTitle'));
			$classCalcConsumptionTax = $this->_getClassCalc(array('flagType' => 'ConsumptionTax'));
			$classCalcLogCalc = $this->_getClassCalc(array('flagType' => 'LogCalc'));
			$classCalcTempNextLog = $this->_getClassCalc(array(
				'flagType'   => 'TempNext',
				'flagDetail' => 'Log',
			));

			if ($arrVarsLog) {
				$arrRows = $this->_getVarsLogCalcLoop(array(
					'arrVarsLog'      => $arrVarsLog,
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				));


				$flag = $classCalcAccountTitle->allot(array(
					'flagStatus'      => 'add',
					'arrRows'         => $arrRows,
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				));
				if ($flag == 'errorDataMax') {
					$this->_sendJSON(array('data' => array('flag' => $flag),));
				}

				$flag = $classCalcSubAccountTitle->allot(array(
					'flagStatus'      => 'add',
					'arrRows'         => $arrRows,
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				));
				if ($flag == 'errorDataMax') {
					$this->_sendJSON(array('data' => array('flag' => $flag),));
				}

				$flag = $classCalcConsumptionTax->allot(array(
					'flagStatus'      => 'add',
					'arrRows'         => $arrRows,
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				));
				if ($flag == 'errorDataMax') {
					$this->_sendJSON(array('data' => array('flag' => $flag),));
				}

				$classCalcLogCalc->allot(array(
					'flagStatus'      => 'add',
					'arrRows'         => $arrRows,
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				));

				$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
				$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
				$numFiscalPeriod = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];

				if ((preg_match("/^(tempPrev)$/", $flagCurrentFlagNow))) {
					$flag = $classCalcTempNextLog->allot(array(
						'flagStatus'      => 'add',
						'numFiscalPeriod' => $numFiscalPeriod,
						'arrRows'         => $arrRows,
					));
					if ($flag == 'errorDataMax') {
						$this->_sendJSON(array('data' => array('flag' => $flag),));
					}
				}

				$this->_updateDbPreferenceStamp(array('strColumn' => 'log'));
			}

			if ($arrVarsLogTemp) {
				$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
				if ((preg_match("/^(tempPrev)$/", $flagCurrentFlagNow))) {
					$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] + 1;

				} elseif ((preg_match("/^(tempNext)$/", $flagCurrentFlagNow))) {
					$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] - 1;
				}
				$arrRows = $this->_getVarsLogCalcLoop(array(
					'arrVarsLog'      => $arrVarsLogTemp,
					'numFiscalPeriod' => $numFiscalPeriodTemp,
				));

				$flag = $classCalcAccountTitle->allot(array(
					'flagStatus'      => 'add',
					'arrRows'         => $arrRows,
					'numFiscalPeriod' => $numFiscalPeriodTemp,
				));
				if ($flag == 'errorDataMax') {
					$this->_sendJSON(array('data' => array('flag' => $flag),));
				}

				$flag = $classCalcSubAccountTitle->allot(array(
					'flagStatus'      => 'add',
					'arrRows'         => $arrRows,
					'numFiscalPeriod' => $numFiscalPeriodTemp,
				));
				if ($flag == 'errorDataMax') {
					$this->_sendJSON(array('data' => array('flag' => $flag),));
				}

				$flag = $classCalcConsumptionTax->allot(array(
					'flagStatus'      => 'add',
					'arrRows'         => $arrRows,
					'numFiscalPeriod' => $numFiscalPeriodTemp,
				));
				if ($flag == 'errorDataMax') {
					$this->_sendJSON(array('data' => array('flag' => $flag),));
				}

				$classCalcLogCalc->allot(array(
					'flagStatus'      => 'add',
					'arrRows'         => $arrRows,
					'numFiscalPeriod' => $numFiscalPeriodTemp,
				));

				//tempNext is not wrong
				if ((preg_match("/^(tempNext)$/", $flagCurrentFlagNow))) {
					$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
					$flag = $classCalcTempNextLog->allot(array(
						'flagStatus'      => 'add',
						'numFiscalPeriod' => $numFiscalPeriod,
						'arrRows'         => $arrRows,
					));
					if ($flag == 'errorDataMax') {
						$this->_sendJSON(array('data' => array('flag' => $flag),));
					}
				}
				$this->_updateDbPreferenceStamp(array('strColumn' => 'log'));
			}

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$this->_sendJSON(array(
			'data' => array(
				'flag' => 'success',
			)
		));
	}

	/**
		(array(

		))
	 */
	protected function _checkParams()
	{
		global $varsRequest;
		global $varsPluginAccountingEntity;

		$idEntity = $varsRequest['query']['api']['params']['idEntity'];
		$varsEntity = $varsPluginAccountingEntity[$idEntity];
		if (!$varsEntity) {
			$this->_sendJSON(array('data' => array('flag' => 'entityNotExist'),));
		}

		$arrCSV = $varsRequest['query']['api']['params']['arrCSV'];
		if (count($arrCSV) < 2) {
			$this->_sendJSON(array('data' => array('flag' => 'errorCSV'),));
		}
	}

	/**
		(array(
			'arrCSV'             => $arrCSV,
			'classCalcLogImport' => $classCalcLogImport,
		))
	 */
	protected function _checkVarsCSV($arr)
	{
		global $varsPluginAccountingAccount;

		$arrayCSV = $this->_getVarsCSV(array(
			'arrCSV' => $arr['arrCSV'],
		));

		$numFiscalPeriodTemp = $this->_getNumFiscalPeriodTemp();

		$classCalcLogImport = &$arr['classCalcLogImport'];
		$varsCSV = $classCalcLogImport->allot(array(
			'flagStatus'          => 'check',
			'arrOrder'            => $arrayCSV,
			'idEntity'            => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
			'idAccount'           => $varsPluginAccountingAccount['idAccount'],
		));

		return $varsCSV;
	}

	protected function _getVarsCSV($arr)
	{
		$flag = 0;
		$arrRows = array();
		$arrColumn = array();
		$array = $arr['arrCSV'];
		foreach ($array as $key => $value) {
			if (!$flag) {
				$arrColumn = $value;
				$flag = 1;
			} else {
				$temp = array();
				foreach ($arrColumn as $keyColumn => $valueColumn) {
					$temp[$valueColumn] = $value[$keyColumn];
				}
				$arrRows[] = $temp;
			}
		}

		return $arrRows;
	}

	/**

	 */
	protected function _getNumFiscalPeriodTemp()
	{
		global $varsPluginAccountingAccount;

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		$numFiscalPeriodTemp = 0;
		if ((preg_match("/^(tempPrev)$/", $flagCurrentFlagNow))) {
			$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] + 1;

		} elseif ((preg_match("/^(tempNext)$/", $flagCurrentFlagNow))) {
			$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] - 1;
		}

		return $numFiscalPeriodTemp;
	}
}
