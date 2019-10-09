<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_LogPermit extends Code_Else_Plugin_Accounting_Jpn_Log
{
	protected $_childSelf = array(

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
	protected function _iniDetailPermit()
	{
		global $varsRequest;

		$this->_setPermit(array(
			'arrId' => array($varsRequest['query']['jsonValue']['idTarget']),
		));
	}

	/**
	 *
	 */
	protected function _iniListPermit()
	{
		global $varsRequest;

		$this->_setPermit(array(
			'arrId' => $varsRequest['query']['jsonValue']['vars'],
		));

	}

	/**
		(array(
			'arrId' => array(),
		))
	 */
	protected function _setPermit($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $classCheck;
		global $classEscape;

		global $varsAccount;
		global $varsRequest;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$flagCurrent = $this->_checkCurrent();

		if (!$flagCurrent) {
			$this->_sendOldError();
		}

		$varsAuthority = $this->_getVarsAuthority(array());

		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'])) {
			$this->_sendOldError();
		}

		$array = $arr['arrId'];
		$flag = $classCheck->checkValueFormat(array(
			'flagType' => 'num',
			'flagArr'  => 1,
			'value'    => $array,
		));
		if ($flag) {
			$this->_sendOldError();
		}
		$tm = TIMESTAMP;
		$arrVarsLog = array();
		foreach ($array as $key => $value) {
			$varsLog = $this->_getVarsLog(array('idTarget' => $value));
			if (!$varsLog) {
				$this->_sendOldError();
			}

			$id = $varsAccount['id'];
			if (!preg_match( "/,$id,/", $varsLog['arrCommaIdAccountPermit'])
				|| !(int) $varsLog['flagApply']
				|| (int) $varsLog['flagRemove']
				|| (int) $varsLog['flagBack']
			) {
				continue;
			}

			$arrayPermit = $varsLog['jsonPermitHistory'];
			$numEnd = count($arrayPermit) - 1;
			$jsonPermitHistory = $arrayPermit[$numEnd];
			$arrayIdPermit = &$jsonPermitHistory['arrIdAccountPermit'];

			foreach ($arrayIdPermit as $keyIdPermit => $valueIdPermit) {
				if ($valueIdPermit['idAccount'] == $varsAccount['id']) {
					$arrayIdPermit[$keyIdPermit]['flagPermit'] = 'done';
					$arrayIdPermit[$keyIdPermit]['stampRegister'] = $tm;
				}
			}
			$numSumPermit = 0;
			$numSum = count($arrayIdPermit);
			$arraySumPermit = $jsonPermitHistory['arrIdAccountPermit'];
			foreach ($arraySumPermit as $keySumPermit => $valueSumPermit) {
				if ($valueSumPermit['flagPermit'] == 'done') {
					$numSumPermit++;
				}
			}
			$numSumMax = $numSum - $numSumPermit;

			if ($numSumPermit >= $varsLog['numSumMax']) {
				$varsLog['flagApply'] = 0;
				$varsLog['flagApplyBack'] = 0;
				$jsonPermitHistory['stampPermit'] = $tm;
			}
			$arrayPermit[$numEnd] = $jsonPermitHistory;
			$varsLog['jsonPermitHistory'] = json_encode($arrayPermit);
			$this->checkTextSize(array(
				'flag' => 'errorDataMax',
				'str'  => $varsLog['jsonPermitHistory'],
			));
			$arrVarsLog[$value] = $varsLog;
		}
		if (!$arrVarsLog) {
			$this->_sendOldError();
		}

		try {
			$dbh->beginTransaction();

			$arrayNew = array();
			$array = $arrVarsLog;
			foreach ($array as $key => $value) {
				$jsonPermitHistory = $value['jsonPermitHistory'];
				$flagApply = $value['flagApply'];
				$flagApplyBack = $value['flagApplyBack'];
				$classDb->updateRow(array(
					'idModule'  => 'accounting',
					'strTable'  => 'accountingLog',
					'arrColumn' => array('flagApply', 'flagApplyBack', 'jsonPermitHistory',),
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
							'value'         => $value['idLog'],
						),
					),
					'arrValue'  => array($flagApply, $flagApplyBack, $jsonPermitHistory),
				));
				if ($flagApply) {
					continue;
				}
				$arrayNew[] = $value;
			}

			$arrRows = $this->_getVarsLogCalcLoop(array(
				'arrVarsLog'      => $arrayNew,
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			));
			$classCalcAccountTitle = $this->_getClassCalc(array('flagType' => 'AccountTitle'));
			$classCalcSubAccountTitle = $this->_getClassCalc(array('flagType' => 'SubAccountTitle'));
			$classCalcConsumptionTax = $this->_getClassCalc(array('flagType' => 'ConsumptionTax'));
			$classCalcLogCalc = $this->_getClassCalc(array('flagType' => 'LogCalc'));

			$flag = $classCalcAccountTitle->allot(array(
				'flagStatus'      => 'add',
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
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

			$flag = $classCalcSubAccountTitle->allot(array(
				'flagStatus'      => 'add',
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
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

			$flag = $classCalcConsumptionTax->allot(array(
				'flagStatus'      => 'add',
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
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

			$classCalcLogCalc->allot(array(
				'flagStatus'      => 'add',
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				'arrRows'         => $arrRows,
			));

			$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
			if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
				$classCalcTempNextLog = $this->_getClassCalc(array(
					'flagType'   => 'TempNext',
					'flagDetail' => 'Log',
				));
				$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
				$numFiscalPeriod = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];

				$flag = $classCalcTempNextLog->allot(array(
					'flagStatus'      => 'add',
					'numFiscalPeriod' => $numFiscalPeriod,
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
		$this->_setSearch(array('flag' => 1));
	}

}
