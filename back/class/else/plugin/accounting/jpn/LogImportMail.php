<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_LogImportMail extends Code_Else_Plugin_Accounting_Jpn_Log
{
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
	protected function _iniListImport()
	{
		global $classDb;
		global $varsRequest;
		$dbh = $classDb->getHandle();

		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 0,
			'flagAuthority' => 'insert',
			'idTarget'      => $this->_extSelf['idPreference'],
		));

		if (!$flag) {
			$this->_sendEnd();
		}

		if (!$this->_checkCurrent()) {
			$this->_sendEnd();
		}

		$classCalcLogImportMail = $this->_getClassCalc(array('flagType' => 'LogImportMail'));
		$varsRows = $classCalcLogImportMail->allot(array(
			'flagStatus'      => 'check',
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
		));

		$arrIdTarget = array();
		$arrVarsLog = array();
		$arrVarsLogTemp = array();

		$classCalcLogImport = $this->_getClassCalc(array('flagType' => 'LogImport'));
		$classCalcCashPay = $this->_getClassCalc(array('flagType' => 'CashPay'));
		$classCalcCash = $this->_getClassCalc(array('flagType' => 'Cash'));
		$classCalcLog = $this->_getClassCalc(array('flagType' => 'Log'));
		$numFiscalPeriodTemp = $this->_getNumFiscalPeriodTemp();
		$flagSearch = 1;

		$flagSelect = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'select',
			'idTarget'      => $this->_extSelf['idPreference'],
		));

		try {
			$dbh->beginTransaction();

			$array = $varsRows['arrRows'];
			foreach ($array as $key => $value) {
				$data = $this->_checkVarsCSV(array(
					'varsFile'           => $value,
					'vars'               => $vars,
					'classCalcLogImport' => $classCalcLogImport,
				));

				$arrayLog = $data['arrayRequests'];
				foreach ($arrayLog as $keyLog => $valueLog) {
					$valueLog['stampArrive'] = $value['stampArrive'];
					$valueLog['arrSpaceStrTag'] = $value['arrSpaceStrTag'];
					$flagCashVars = $classCalcCashPay->allot(array(
						'flagStatus'      => 'check',
						'arrValue'        => $valueLog,
						'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
						'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
						'classCalcLog'    => $classCalcLog,
					));
					//操作ユーザの権限で違うことで取込が違うと問題があるので収支管理のインポート権限は不要
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
						if ($flagSelect) {
							$flagSearch = 'retry';
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
						'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
						'numFiscalPeriod' => $numFiscalPeriodTemp,
						'classCalcLog'    => $classCalcLog,
					));
					//操作ユーザの権限で違うことで取込が違うと問題があるので収支管理のインポート権限は不要
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
						'flagType'            => 'mail',
						'arrSpaceStrTag'      => $value['arrSpaceStrTag'],
						'vars'                => $data['varsRetry'],
						'idEntity'            => $varsPluginAccountingAccount['idEntityCurrent'],
						'idAccount'           => $value['idAccount'],
						'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
						'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
					));
					if ($flagSelect) {
						$flagSearch = 'retry';
					}
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
						$this->sendVars(array(
							'flag'    => $flag,
							'stamp'   => $this->getStamp(),
							'numNews' => $this->getNumNews(),
							'vars'    => array(),
						));
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
					'numFiscalPeriod' => $numFiscalPeriodTemp,
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
					'numFiscalPeriod' => $numFiscalPeriodTemp,
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
						$this->sendVars(array(
							'flag'    => $flag,
							'stamp'   => $this->getStamp(),
							'numNews' => $this->getNumNews(),
							'vars'    => array(),
						));
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

		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
		$this->_setSearch(array('flag' => $flagSearch, 'arrIdTarget' => $arrIdTarget));
	}

	protected function _sendEnd()
	{
		global $varsRequest;

		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
		$this->_setSearch(array('flag' => 1));
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

		$numFiscalPeriodTemp = $this->_getNumFiscalPeriodTemp();

		$classCalcLogImport = &$arr['classCalcLogImport'];
		$varsCSV = $classCalcLogImport->allot(array(
			'flagStatus'          => 'check',
			'arrOrder'            => $arrayCSV,
			'idEntity'            => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
			'idAccount'           => $arr['varsFile']['idAccount'],
		));

		return $varsCSV;
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
