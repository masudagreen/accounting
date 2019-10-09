<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_LogDelete extends Code_Else_Plugin_Accounting_Jpn_Log
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
	protected function _iniDetailDelete()
	{
		global $varsRequest;

		$this->_setDelete(array(
			'arrId' => array($varsRequest['query']['jsonValue']['idTarget']),
		));
	}

	/**
	 *
	 */
	protected function _iniListDelete()
	{
		global $varsRequest;

		$this->_setDelete(array(
			'arrId' => $varsRequest['query']['jsonValue']['vars'],
		));

	}

	/**
		$this->_setDelete(array(
			'arrId' => array(),
		));
	 */
	protected function _setDelete($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $classCheck;

		global $varsRequest;
		global $varsAccount;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$flagCurrent = $this->_checkCurrent();
		if (!$flagCurrent) {
			$this->_sendOldError();
		}

		$varsAuthority = $this->_getVarsAuthority(array());

		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllDelete'] || $varsAuthority['flagMyDelete'])) {
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
		$arrVarsLog = array();
		foreach ($array as $key => $value) {
			$varsLog = $this->_getVarsLog(array('idTarget' => $value, 'flagRemove' => 0,));
			if (!$varsLog) {
				$this->_sendOldError();

			} else {
				if (($varsAuthority != 'admin' && !$varsAuthority['flagAllDelete'] && $varsAuthority['flagMyDelete'])
					&& $varsLog['idAccount'] != $varsAccount['id']
				) {
					continue;
				}
			}
			if ($varsLog['jsonPermitHistory']) {
				$arrayPermit = $varsLog['jsonPermitHistory'];
				foreach ($arrayPermit as $keyPermit => $valuePermit) {
					$arrayPermit[$keyPermit]['flagInvalid'] = 1;
				}
				$varsLog['jsonPermitHistory'] = $arrayPermit;

			}
			$varsLog['jsonPermitHistory'] = json_encode($varsLog['jsonPermitHistory']);
			$this->checkTextSize(array(
				'flag' => 'errorDataMax',
				'str'  => $varsLog['jsonPermitHistory'],
			));
			$arrVarsLog[$value] = $varsLog;
		}
		if (!$arrVarsLog) {
			$this->_sendOldError();
		}

		$tm = TIMESTAMP;
		$stampRemove = $tm;
		$flagRemove = 1;
		$flagApply = 0;
		$idAccountApply = 0;
		$flagApplyBack = 0;

		try {
			$dbh->beginTransaction();

			$arrayNew = array();
			foreach ($array as $key => $value) {
				$jsonPermitHistory = $arrVarsLog[$value]['jsonPermitHistory'];
				$classDb->updateRow(array(
					'idModule'  => 'accounting',
					'strTable'  => 'accountingLog',
					'arrColumn' => array('flagApply', 'idAccountApply', 'flagApplyBack', 'jsonPermitHistory', 'stampRemove', 'flagRemove'),
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
							'value'         => $value,
						),
					),
					'arrValue'  => array($flagApply, $idAccountApply, $flagApplyBack, $jsonPermitHistory, $stampRemove, $flagRemove),
				));
				if ($arrVarsLog[$value]['flagApply']) {
					continue;
				}
				$arrayNew[] = $arrVarsLog[$value];

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
				'flagStatus'      => 'delete',
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
				'flagStatus'      => 'delete',
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
				'flagStatus'      => 'delete',
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
				'flagStatus'      => 'delete',
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
					'flagStatus'      => 'delete',
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
