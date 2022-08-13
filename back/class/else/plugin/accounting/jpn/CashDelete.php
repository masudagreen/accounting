<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CashDelete extends Code_Else_Plugin_Accounting_Jpn_Cash
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

		$numFiscalPeriodTemp = 0;
		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^temp/", $flagCurrentFlagNow)) {
			if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
				$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] + 1;
			}
		}

		$arrVarsLogTemp = array();
		$arrVarsLog = array();
		foreach ($array as $key => $value) {
			$varsLog = $this->_getVarsLog(array(
				'idTarget'        => $value,
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
				'flagRemove'      => 0,
			));

			if (!$varsLog) {
				$this->_sendOldError();

			} else {
				if (($varsAuthority != 'admin' && !$varsAuthority['flagAllDelete'] && $varsAuthority['flagMyDelete'])
					&& $varsLog['idAccount'] != $varsAccount['id']
				) {
					continue;
				}
			}
			$arrVarsLog[$value] = $varsLog;
			if ($numFiscalPeriodTemp) {
				$varsLogTemp = $this->_getVarsLog(array(
					'idTarget'        => $value,
					'numFiscalPeriod' => $numFiscalPeriodTemp,
					'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
					'flagRemove'      => 0,
				));
				if ($varsLogTemp) {
					$arrVarsLogTemp[$value] = $varsLogTemp;
				}
			}
		}
		if (!$arrVarsLog) {
			$this->_sendOldError();
		}

		$stampRemove = TIMESTAMP;
		$flagRemove = 1;
		$classCalcCash = $this->_getClassCalc(array('flagType' => 'Cash'));
		try {
			$dbh->beginTransaction();

			$arrVarsLogPre = array();
			$arrVarsLogDone = array();
			$array = $arrVarsLog;
			foreach ($array as $key => $value) {
				if ($value['flagPay']) {
					$arrVarsLogDone[] = $value;
				} else {
					$arrVarsLogPre[] = $value;
				}
				$classDb->updateRow(array(
					'idModule'  => 'accounting',
					'strTable'  => 'accountingLogCash',
					'arrColumn' => array('stampRemove', 'flagRemove'),
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
							'strColumn'     => 'idLogCash',
							'flagCondition' => 'eq',
							'value'         => $value['idLogCash'],
						),
					),
					'arrValue'  => array($stampRemove, $flagRemove),
				));
			}

			if ($arrVarsLogPre) {
				$flag = $classCalcCash->allot(array(
					'flagStatus'      => 'deletePre',
					'arrRows'         => $arrVarsLogPre,
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
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

			if ($arrVarsLogDone) {
				$flag = $classCalcCash->allot(array(
					'flagStatus'      => 'deleteDone',
					'arrRows'         => $arrVarsLogDone,
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
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

			if ($arrVarsLogTemp) {
				$arrVarsLogPre = array();
				$arrVarsLogDone = array();
				$array = $arrVarsLogTemp;
				foreach ($array as $key => $value) {
					$classDb->deleteRow(array(
						'idModule'  => 'accounting',
						'strTable' => 'accountingLogCash',
						'flagAnd'   => 1,
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
								'flagType'      => 'num',
								'strColumn'     => 'idLogCash',
								'flagCondition' => 'eq',
								'value'         => $value['idLogCash'],
							),
						),
					));
					if ($value['flagRemove']) {
						continue;
					}
					if ($value['flagPay']) {
						$arrVarsLogDone[] = $value;
					} else {
						$arrVarsLogPre[] = $value;
					}
				}
				if ($arrVarsLogPre) {
					$flag = $classCalcCash->allot(array(
						'flagStatus'      => 'deletePre',
						'arrRows'         => $arrVarsLogPre,
						'numFiscalPeriod' => $numFiscalPeriodTemp,
						'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
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

				if ($arrVarsLogDone) {
					$flag = $classCalcCash->allot(array(
						'flagStatus'      => 'deleteDone',
						'arrRows'         => $arrVarsLogDone,
						'numFiscalPeriod' => $numFiscalPeriodTemp,
						'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
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
			}

			$this->_updateDbPreferenceStamp(array('strColumn' => 'cash'));

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
