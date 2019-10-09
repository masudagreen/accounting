<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcLogBoard extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extChildSelf = array(

	);

	/**

	 */
	public function run()
	{
		if (FLAG_TEST) {
			var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
		}
		exit;
	}

	/*
		(array(

		))
	 * */
	public function allot($arr)
	{
		$method = '_ini' . ucwords($arr['flagStatus']);
		if (method_exists($this, $method)) {
			return $this->$method($arr);

		} else {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}
	}

	/**
		(array(
			'flagStatus'      => 'num',
			'strNation'       => ucwords(PLUGIN_ACCOUNTING_STR_NATION),
			'varsAccount'     => $varsAccount,
			'varsAuthority'   => $varsAuthority,
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
		))
	 */
	protected function _iniNum($arr)
	{
		$data = array();
		$data['numMail'] = $this->_checkNumMail($arr);
		$data['numRegister'] = $this->_checkNumRegister($arr);
		$data['numRetry'] = $this->_checkNumRetry($arr);
		$data['numImport'] = $this->_checkNumImport($arr);
		$data['numHouse'] = $this->_checkNumHouse($arr);

		return $data;
	}

	/**
		(array(

		))
	 */
	protected function _checkNumMail($arr)
	{
		global $classDb;

		$idEntity = $arr['idEntity'];
		$numFiscalPeriod = $arr['numFiscalPeriod'];

		$classCalcLogImportMail = $this->_getClassCalc(array('flagType' => 'LogImportMail'));
		$varsRows = $classCalcLogImportMail->allot(array(
			'flagStatus'      => 'check',
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $arr['idEntity'],
		));

		if (!$varsRows['numRows']) {
			return $varsRows['numRows'];
		}

		$arrIdTarget = array();
		$arrVarsLog = array();
		$arrVarsLogTemp = array();

		$classCalcLogImport = $this->_getClassCalc(array('flagType' => 'LogImport'));
		$classCalcCashPay = $this->_getClassCalc(array('flagType' => 'CashPay'));
		$classCalcCash = $this->_getClassCalc(array('flagType' => 'Cash'));
		$classCalcLog = $this->_getClassCalc(array('flagType' => 'Log'));

		$numFiscalPeriodTemp = $this->_getNumFiscalPeriodTemp(array(
			'idEntity'        => $arr['idEntity'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		$dbh = $classDb->setDbhMaster();
		try {
			$dbh->beginTransaction();

			$array = $varsRows['arrRows'];
			foreach ($array as $key => $value) {
				$data = $this->_checkVarsCSV(array(
					'varsFile'           => $value,
					'vars'               => $vars,
					'classCalcLogImport' => $classCalcLogImport,
					'numFiscalPeriod'    => $arr['numFiscalPeriod'],
					'idEntity'           => $arr['idEntity'],
				));

				$arrayLog = $data['arrayRequests'];
				foreach ($arrayLog as $keyLog => $valueLog) {
					$valueLog['stampArrive'] = $value['stampArrive'];
					$valueLog['arrSpaceStrTag'] = $value['arrSpaceStrTag'];
					$flagCashVars = $classCalcCashPay->allot(array(
						'flagStatus'      => 'check',
						'arrValue'        => $valueLog,
						'idEntity'        => $arr['idEntity'],
						'numFiscalPeriod' => $arr['numFiscalPeriod'],
						'classCalcLog'    => $classCalcLog,
					));
					//操作ユーザの権限で違うことで取込が違うと問題があるので収支管理のインポート権限は不要
					if ($flagCashVars['flag']) {
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
								$this->sendVars(array(
									'flag'    => $flagVars,
									'stamp'   => $this->getStamp(),
									'numNews' => $this->getNumNews(),
									'vars'    => array(),
								));
							}
							$dataLog = $classCalcLogImport->allot(array(
								'flagStatus'      => 'insertDbLog',
								'arrValue'        => $flagVars['arrValue'],
								'idEntity'        => $arr['idEntity'],
								'numFiscalPeriod' => $arr['numFiscalPeriod'],
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
								'idEntity'        => $arr['idEntity'],
								'numFiscalPeriod' => $arr['numFiscalPeriod'],
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
						$arrIdTarget[$dataLog['idLog']] = 1;
						if (!$dataLog['flagApply']) {
							$arrVarsLog[] = $dataLog;
						}
					}
				}

				$arrayLog = $data['arrayRequestsTemp'];
				foreach ($arrayLog as $keyLog => $valueLog) {
					$valueLog['stampArrive'] = $value['stampArrive'];
					$valueLog['arrSpaceStrTag'] = $value['arrSpaceStrTag'];
					$flagCashVars = $classCalcCashPay->allot(array(
						'flagStatus'      => 'check',
						'arrValue'        => $valueLog,
						'idEntity'        => $arr['idEntity'],
						'numFiscalPeriod' => $numFiscalPeriodTemp,
						'classCalcLog'    => $classCalcLog,
					));
					if ($flagCashVars['flag']) {
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
								$this->sendVars(array(
									'flag'    => $flagVars,
									'stamp'   => $this->getStamp(),
									'numNews' => $this->getNumNews(),
									'vars'    => array(),
								));
							}
							$dataLog = $classCalcLogImport->allot(array(
								'flagStatus'      => 'insertDbLog',
								'arrValue'        => $flagVars['arrValue'],
								'idEntity'        => $arr['idEntity'],
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
								'idEntity'        => $arr['idEntity'],
								'numFiscalPeriod' => $numFiscalPeriodTemp,
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
							$numberRow = $valueLog['id'];
							$arrayData[$keyData]['varsStatus']['arrCashDefer'][$numberRow] = 1;
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
						$arrIdTarget[$dataLog['idLog']] = 1;
						if (!$dataLog['flagApply']) {
							$arrVarsLogTemp[] = $dataLog;
						}
					}
				}

				if ($data['varsRetry']['varsDetail']) {
					$classCalcLogImport->allot(array(
						'flagStatus'          => 'insertDbRetry',
						'flagType'            => 'mail',
						'arrSpaceStrTag'      => $value['arrSpaceStrTag'],
						'vars'                => $data['varsRetry'],
						'idEntity'            => $arr['idEntity'],
						'idAccount'           => $value['idAccount'],
						'numFiscalPeriod'     => $arr['numFiscalPeriod'],
						'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
					));
				}
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
					'numFiscalPeriod' => $arr['numFiscalPeriod'],
				));

				$flag = $classCalcAccountTitle->allot(array(
					'flagStatus'      => 'add',
					'arrRows'         => $arrRows,
					'numFiscalPeriod' => $arr['numFiscalPeriod'],
				));
				if ($flag == 'errorDataMax') {
					return 0;
				}

				$flag = $classCalcSubAccountTitle->allot(array(
					'flagStatus'      => 'add',
					'arrRows'         => $arrRows,
					'numFiscalPeriod' => $arr['numFiscalPeriod'],
				));
				if ($flag == 'errorDataMax') {
					return 0;
				}

				$flag = $classCalcConsumptionTax->allot(array(
					'flagStatus'      => 'add',
					'arrRows'         => $arrRows,
					'numFiscalPeriod' => $arr['numFiscalPeriod'],
				));
				if ($flag == 'errorDataMax') {
					return 0;
				}

				$classCalcLogCalc->allot(array(
					'flagStatus'      => 'add',
					'arrRows'         => $arrRows,
					'numFiscalPeriod' => $arr['numFiscalPeriod'],
				));

				$flagCurrentFlagNow = $this->_getCurrentFlagNow(array(
					'idEntity'        => $arr['idEntity'],
					'numFiscalPeriod' => $arr['numFiscalPeriod'],
				));
				if ((preg_match("/^(tempPrev)$/", $flagCurrentFlagNow))) {
					$numFiscalPeriodTempNext = $numFiscalPeriod + 1;
					$flag = $classCalcTempNextLog->allot(array(
						'flagStatus'      => 'add',
						'numFiscalPeriod' => $numFiscalPeriodTempNext,
						'arrRows'         => $arrRows,
					));
					if ($flag == 'errorDataMax') {
						return 0;
					}
				}
				$this->_updateDbPreferenceStamp(array('strColumn' => 'log'));
			}

			if ($arrVarsLogTemp) {
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
					return 0;
				}

				$flag = $classCalcSubAccountTitle->allot(array(
					'flagStatus'      => 'add',
					'arrRows'         => $arrRows,
					'numFiscalPeriod' => $numFiscalPeriodTemp,
				));
				if ($flag == 'errorDataMax') {
					return 0;
				}

				$flag = $classCalcConsumptionTax->allot(array(
					'flagStatus'      => 'add',
					'arrRows'         => $arrRows,
					'numFiscalPeriod' => $numFiscalPeriodTemp,
				));
				if ($flag == 'errorDataMax') {
					return 0;
				}

				$classCalcLogCalc->allot(array(
					'flagStatus'      => 'add',
					'arrRows'         => $arrRows,
					'numFiscalPeriod' => $numFiscalPeriodTemp,
				));

				if ((preg_match("/^(tempNext)$/", $flagCurrentFlagNow))) {
					$flag = $classCalcTempNextLog->allot(array(
						'flagStatus'      => 'add',
						'numFiscalPeriod' => $arr['numFiscalPeriod'],
						'arrRows'         => $arrRows,
					));
					if ($flag == 'errorDataMax') {
						return 0;
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

		return $varsRows['numRows'];
	}

	/**

	 */
	protected function _getNumFiscalPeriodTemp($arr)
	{
		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array(
			'idEntity'        => $arr['idEntity'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		$numFiscalPeriodTemp = 0;
		if ((preg_match("/^(tempPrev)$/", $flagCurrentFlagNow))) {
			$numFiscalPeriodTemp = $arr['numFiscalPeriod'] + 1;

		} elseif ((preg_match("/^(tempNext)$/", $flagCurrentFlagNow))) {
			$numFiscalPeriodTemp = $arr['numFiscalPeriod'] - 1;
		}

		return $numFiscalPeriodTemp;
	}

	/**
		(array(
			'varsFile' => $value,
			'vars'     => $vars,
		))
	 */
	protected function _checkVarsCSV($arr)
	{
		global $classFile;
		global $varsPluginAccountingAccount;

		$arrayCSV = $classFile->getCsvRows(array(
			'path' => $arr['varsFile']['strUrl'],
		));

		unlink($arr['varsFile']['strUrl']);

		$numFiscalPeriodTemp = $this->_getNumFiscalPeriodTemp(array(
			'idEntity'        => $arr['idEntity'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$classCalcLogImport = &$arr['classCalcLogImport'];
		$varsCSV = $classCalcLogImport->allot(array(
			'flagStatus'          => 'check',
			'arrOrder'            => $arrayCSV,
			'idEntity'            => $arr['idEntity'],
			'numFiscalPeriod'     => $arr['numFiscalPeriod'],
			'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
			'idAccount'           => $arr['varsFile']['idAccount'],
		));

		return $varsCSV;
	}

	/**
		(array(
		))
	 */
	protected function _checkNumHouse($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		if (!($arr['varsAuthority'] == 'admin' || $this->batchIllegalStringOffset($arr['varsAuthority'], 'flagAllSelect'))) {
			return 0;
		}

		$strNation = $arr['strNation'];

		$arrWhere = array(
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
		);
		$idEntity = $arr['idEntity'];
		$numFiscalPeriod = $arr['numFiscalPeriod'];
		$stampRegister = $arr['varsAccount']['jsonStampCheck']['accountingLogHouseJpn_' . $idEntity . '_' . $numFiscalPeriod];
		if (!is_null($stampRegister)) {
			$arrWhere[] = array(
				'flagType'      => '',
				'strColumn'     => 'stampRegister',
				'flagCondition' => 'big',
				'value'         => $stampRegister,
			);
		}

		$rows = $classDb->getSelect(array(
			'idModule'   => 'accounting',
			'strTable'   => 'accountingLogHouse' . $strNation,
			'arrLimit'    => array(
				'numStart' => 0, 'numEnd' => 1,
			),
			'arrOrder'   => array(),
			'flagAnd'    => 1,
			'arrWhere'   => $arrWhere,
		));

		if (!$rows['numRows']) {
			return 0;
		}

		return $rows['numRows'];
	}

	/**
		(array(
		))
	 */
	protected function _checkNumImport($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		if (!($arr['varsAuthority'] == 'admin' || $this->batchIllegalStringOffset($arr['varsAuthority'], 'flagAllSelect'))) {
			return 0;
		}

		$strNation = $arr['strNation'];

		$arrWhere = array(
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
		);

		$idEntity = $arr['idEntity'];
		$numFiscalPeriod = $arr['numFiscalPeriod'];
		$stampRegister = $arr['varsAccount']['jsonStampCheck']['accountingLogImportJpn_' . $idEntity . '_' . $numFiscalPeriod];
		if (!is_null($stampRegister)) {
			$arrWhere[] = array(
				'flagType'      => '',
				'strColumn'     => 'stampRegister',
				'flagCondition' => 'big',
				'value'         => $stampRegister,
			);
		}

		$rows = $classDb->getSelect(array(
			'idModule'   => 'accounting',
			'strTable'   => 'accountingLogImport' . $strNation,
			'arrLimit'    => array(
				'numStart' => 0, 'numEnd' => 1,
			),
			'arrOrder'   => array(),
			'flagAnd'    => 1,
			'arrWhere'   => $arrWhere,
		));

		if (!$rows['numRows']) {
			return 0;
		}

		return $rows['numRows'];
	}

	/**
		(array(
		))
	 */
	protected function _checkNumRetry($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		if (!($arr['varsAuthority'] == 'admin' || $this->batchIllegalStringOffset($arr['varsAuthority'], 'flagAllSelect'))) {
			return 0;
		}

		$strNation = $arr['strNation'];

		$arrWhere = array(
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
		);
		$idEntity = $arr['idEntity'];
		$numFiscalPeriod = $arr['numFiscalPeriod'];
		$stampRegister = $arr['varsAccount']['jsonStampCheck']['accountingLogImportRetryJpn_' . $idEntity . '_' . $numFiscalPeriod];
		if (!is_null($stampRegister)) {
			$arrWhere[] = array(
				'flagType'      => '',
				'strColumn'     => 'stampRegister',
				'flagCondition' => 'big',
				'value'         => $stampRegister,
			);
		}

		$rows = $classDb->getSelect(array(
			'idModule'   => 'accounting',
			'strTable'   => 'accountingLogImportRetry' . $strNation,
			'arrLimit'    => array(
				'numStart' => 0, 'numEnd' => 1,
			),
			'arrOrder'   => array(),
			'flagAnd'    => 1,
			'arrWhere'   => $arrWhere,
		));

		if (!$rows['numRows']) {
			return 0;
		}

		return $rows['numRows'];
	}

	/**
		(array(

		))
	 */
	protected function _checkNumRegister($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsPluginAccountingAccountsEntity;
		global $varsPluginAccountingAuthority;
		$varsAccount = $arr['varsAccount'];

		$idAccount = $varsAccount['id'];
		if (!($arr['varsAuthority'] == 'admin'
			|| $this->batchIllegalStringOffset($arr['varsAuthority'], 'flagMySelect')
			|| $this->batchIllegalStringOffset($arr['varsAuthority'], 'flagAllSelect')
		)) {
			return 0;
		}

		$arrWhere = array(
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
				'strColumn'     => 'flagRemove',
				'flagCondition' => 'eq',
				'value'         => 0,
			),
		);

		if ($this->batchIllegalStringOffset($arr['varsAuthority'], 'flagAllSelect')
			|| $arr['varsAuthority'] == 'admin'
		) {

		} elseif ($this->batchIllegalStringOffset($arr['varsAuthority'], 'flagMySelect')) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idAccount',
				'flagCondition' => 'eq',
				'value'         => $idAccount,
			);
		}
		$idEntity = $arr['idEntity'];
		$numFiscalPeriod = $arr['numFiscalPeriod'];
		$stampRegister = $arr['varsAccount']['jsonStampCheck']['accountingLog_' . $idEntity . '_' . $numFiscalPeriod];
		if (!is_null($stampRegister)) {
			$arrWhere[] = array(
				'flagType'      => '',
				'strColumn'     => 'stampRegister',
				'flagCondition' => 'big',
				'value'         => $stampRegister,
			);
		}

		$rows = $classDb->getSelect(array(
			'idModule'   => 'accounting',
			'strTable'   => 'accountingLog',
			'arrLimit'    => array(
				'numStart' => 0, 'numEnd' => 1,
			),
			'arrOrder'   => array(),
			'flagAnd'    => 1,
			'arrWhere'   => $arrWhere,
		));

		if (!$rows['numRows']) {
			return 0;
		}

		return $rows['numRows'];
	}


}
