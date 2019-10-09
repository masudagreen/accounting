<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_LogBack extends Code_Else_Plugin_Accounting_Jpn_Log
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
	protected function _iniDetailBack()
	{
		global $varsRequest;

		$this->_setBack(array(
			'arrId' => array($varsRequest['query']['jsonValue']['idTarget']),
		));
	}

	/**
	 *
	 */
	protected function _iniListBack()
	{
		global $varsRequest;

		$this->_setBack(array(
			'arrId' => $varsRequest['query']['jsonValue']['vars'],
		));

	}

		/**
		$this->_setBack(array(
			'arrId' => array(),
		));
	 */
	protected function _setBack($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $classCheck;
		global $classEscape;

		global $varsAccount;
		global $varsRequest;
		global $varsPluginAccountingAccount;

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
					$arrayIdPermit[$keyIdPermit]['flagPermit'] = 'back';
					$arrayIdPermit[$keyIdPermit]['stampRegister'] = $tm;
				}
			}
			$arrayPermit[$numEnd] = $jsonPermitHistory;

			$numSumBack = 0;
			$numSum = count($arrayIdPermit);
			$arraySumPermit = $jsonPermitHistory['arrIdAccountPermit'];
			foreach ($arraySumPermit as $keySumPermit => $valueSumPermit) {
				if ($valueSumPermit['flagPermit'] == 'back') {
					$numSumBack++;
				}
			}
			$numSumMax = $numSum - $numSumBack;
			$flagInvalid = 0;
			if ($numSumMax < $jsonPermitHistory['numSumMax']) {
				$flagInvalid = 1;
			}

			if ($flagInvalid) {
				foreach ($arrayPermit as $keyPermit => $valuePermit) {
					$arrayPermit[$keyPermit]['flagInvalid'] = 1;
				}
				$varsLog['flagApply'] = 1;
				$varsLog['flagApplyBack'] = 1;
			}
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
			$array = $arrVarsLog;
			foreach ($array as $key => $value) {
				$jsonPermitHistory = $value['jsonPermitHistory'];
				$flagApplyBack = $value['flagApplyBack'];
				$classDb->updateRow(array(
					'idModule'  => 'accounting',
					'strTable'  => 'accountingLog',
					'arrColumn' => array('flagApplyBack', 'jsonPermitHistory',),
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
					'arrValue'  => array($flagApplyBack, $jsonPermitHistory),
				));

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
