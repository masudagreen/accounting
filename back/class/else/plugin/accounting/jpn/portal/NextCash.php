<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_Portal_NextCash extends Code_Else_Plugin_Accounting_Jpn_Portal
{
	protected $_extChildSelf = array(
		'numCompare' => 3,
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
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}
	}

	/**
		(array(
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _iniCheckPay($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;

		$varsStamp = $this->_getVarsStampTerm(array(
			'varsFlag'         => array('flagFiscalPeriod' => 'f1'),
			'varsEntityNation' => $arr['varsEntityNation'],
			'numFiscalPeriod'  => $arr['varsEntityNation']['numFiscalPeriod'],
		));

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogCash',
			'arrLimit' => array(
				'numStart' => 0, 'numEnd' => 1,
			),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
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
					'value'         => $arr['varsEntityNation']['numFiscalPeriod'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'stampBook',
					'flagCondition' => 'eqSmall',
					'value'         => $varsStamp['stampMax'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'flagPay',
					'flagCondition' => 'ne',
					'value'         => 1,
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'flagRemove',
					'flagCondition' => 'ne',
					'value'         => 1,
				),
			),
		));

		if ($rows['numRows']) {
			return 1;
		}

		return 0;
	}

	/**
		(array(
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _iniCheckDefer($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogCashDefer',
			'arrLimit' => array(
				'numStart' => 0, 'numEnd' => 1,
			),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
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
					'value'         => $arr['varsEntityNation']['numFiscalPeriod'],
				),
			),
		));

		if ($rows['numRows']) {
			return 1;
		}

		return 0;
	}

	/**
		(array(
			'numFiscalPeriod'  => $numFiscalPeriod,
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _iniInsert($arr)
	{
		$this->_setInsertPreference($arr);
		$this->_setInsertLog($arr);
		$flag = $this->_setInsertValue($arr);
		if ($flag) {
			return $flag;
		}
	}

	/**
	 *
	 */
	protected function _getNumFiscalTermStamp($arr)
	{
		$numTimeZone = PLUGIN_ACCOUNTING_NUM_TIME_ZONE;

		$varsEntityNation = $arr['varsEntityNation'];
		$numFiscalBeginningYear = $varsEntityNation['numFiscalBeginningYear'];
		$numCurrentYear = $numFiscalBeginningYear;

		$strTimeZone = (-1 * $numTimeZone) . 'hours';
		$numYear = $numCurrentYear;
		$numMonth = $varsEntityNation['numFiscalBeginningMonth'];
		$dateTime = new DateTime("$numYear-$numMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stampMin = $dateTime->format('U');
		$numEndMonth = $varsEntityNation['numFiscalBeginningMonth'] + $varsEntityNation['numFiscalTermMonth'];
		if ($numEndMonth > 12) {
			$numCurrentYear++;
			$numEndMonth -= 12;
		}

		$numYear = $numCurrentYear;
		$numMonth = $numEndMonth;
		$dateTime = new DateTime("$numYear-$numMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stampMax = $dateTime->format('U') - 1;

		$data = array(
			'stampMin' => $stampMin,
			'stampMax' => $stampMax,
		);

		return $data;

	}

	/**
		(array(
			'numFiscalPeriod'  => $numFiscalPeriod,
			'varsEntityNation'   => $varsEntityNation,
		))
	 */
	protected function _setInsertLog($arr)
	{
		$rows = $this->_getVarsLog(array(
			'varsEntityNation' => $arr['varsEntityNation'],
		));

		$array = $rows['arrRows'];
		foreach ($array as $key => $value) {
			$this->_setInsertLogLoop(array(
				'varsLog'          => $value,
				'dataTerm'         => $dataTerm,
				'varsEntityNation' => $arr['varsEntityNation'],
				'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			));

		}
	}

	/**
		(array(

		))
	 */
	protected function _getVarsLog($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;

		$varsStamp = $this->_getVarsStampTerm(array(
			'varsFlag'         => array('flagFiscalPeriod' => 'f1'),
			'varsEntityNation' => $arr['varsEntityNation'],
			'numFiscalPeriod'  => $arr['varsEntityNation']['numFiscalPeriod'],
		));

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogCash',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
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
					'value'         => $arr['varsEntityNation']['numFiscalPeriod'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'stampBook',
					'flagCondition' => 'big',
					'value'         => $varsStamp['stampMax'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'flagRemove',
					'flagCondition' => 'ne',
					'value'         => 1,
				),
			),
		));

		return $rows;
	}

	/**
		(array(
			'varsLog'          => $value,
			'varsEntityNation' => $arr['varsEntityNation'],
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
		))
	 */
	protected function _setInsertLogLoop($arr)
	{
		global $classDb;

		$arrDbColumn = array();
		$arrDbValue = array();
		$array = $arr['varsLog'];
		foreach ($array as $key => $value) {
			if ($key == 'id') {
				continue;

			} elseif ($key == 'numFiscalPeriod') {
				$value = $arr['numFiscalPeriod'];

			} elseif (preg_match("/^jsonWriteHistory$/", $key)) {
				$value = json_encode(array());

			} elseif (preg_match("/^json/", $key)) {
				if (!$value) {
					$value = array();
				}
				$value = json_encode($value);
			}
			$arrDbColumn[] = $key;
			$arrDbValue[] = $value;
		}

		$classDb->insertRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingLogCash',
			'arrColumn' => $arrDbColumn,
			'arrValue'  => $arrDbValue,
		));
	}



	/**
		(array(
			'numFiscalPeriod'  => $numFiscalPeriod,
			'varsEntityNation'   => $varsEntityNation,
		))
	 */
	protected function _setInsertPreference($arr)
	{
		$rows = $this->_getVarsPreference(array(
			'numFiscalPeriod' => $arr['varsEntityNation']['numFiscalPeriod'],
		));

		$array = $rows['arrRows'];
		foreach ($array as $key => $value) {
			$this->_setInsertPreferenceLoop(array(
				'varsLog'          => $value,
				'varsEntityNation' => $arr['varsEntityNation'],
				'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			));

		}
	}

	/**
		(array(

		))
	 */
	protected function _getVarsPreference($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingCash',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
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
					'value'         => $arr['numFiscalPeriod'],
				),
			),
		));

		return $rows;
	}

	/**
		(array(
			'varsLog'          => $value,
			'varsEntityNation' => $arr['varsEntityNation'],
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
		))
	 */
	protected function _setInsertPreferenceLoop($arr)
	{
		global $classDb;

		$arrDbColumn = array();
		$arrDbValue = array();
		$array = $arr['varsLog'];
		foreach ($array as $key => $value) {
			if ($key == 'id') {
				continue;

			} elseif ($key == 'numFiscalPeriod') {
				$value = $arr['numFiscalPeriod'];

			} elseif (preg_match("/^json/", $key)) {
				if (!$value) {
					$value = array();
				}
				$value = json_encode($value);
			}

			$arrDbColumn[] = $key;
			$arrDbValue[] = $value;
		}

		$classDb->insertRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingCash',
			'arrColumn' => $arrDbColumn,
			'arrValue'  => $arrDbValue,
		));
	}


	/**
		(array(
			'numFiscalPeriod'  => $numFiscalPeriod,
			'varsEntityNation'   => $varsEntityNation,
		))
	 */
	protected function _setInsertValue($arr)
	{
		global $varsPluginAccountingAccount;

		$this->_setInsertValueDone(array(
			'varsEntityNation' => $arr['varsEntityNation'],
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
		));

		$this->_setInsertValuePre(array(
			'varsEntityNation' => $arr['varsEntityNation'],
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
		));

		//pre reset
		$this->_resetDbPreValue(array(
			'numFiscalPeriod' => $arr['varsEntityNation']['numFiscalPeriod'],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
		));
		$arrRows = $this->_getVarsLogData(array(
			'numFiscalPeriod' => $arr['varsEntityNation']['numFiscalPeriod'],
		));

		//reset classCalcCash hold vars
		$classCalcCash = $this->_getClassCalc(array('flagType' => 'Cash'));
		if ($arrRows) {
			$flag = $classCalcCash->allot(array(
				'flagStatus'      => 'addPre',
				'arrRows'         => $arrRows,
				'numFiscalPeriod' => $arr['varsEntityNation']['numFiscalPeriod'],
				'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			));
			if ($flag == 'errorDataMax') {
				return $flag;
			}
		}
		//pre only
		$arrRows = $this->_getVarsLogData(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		$classCalcCash = $this->_getClassCalc(array('flagType' => 'Cash'));
		if ($arrRows) {
			$flag = $classCalcCash->allot(array(
				'flagStatus'      => 'addPre',
				'arrRows'         => $arrRows,
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
				'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			));
			if ($flag == 'errorDataMax') {
				return $flag;
			}
		}

	}

	/**
		(array(
			'varsValue'       => $varsValue,
			'numFiscalPeriod' => $arr['varsItem']['numFiscalPeriod'],
			'idEntity'        => $arr['varsItem']['idEntity'],
			'flagPay'         => $arr['flagPay'],
		))
	 */
	protected function _resetDbPreValue($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$arrColumn = array();
		$arrValue = array();

		$arrColumn[] = 'jsonData';
		$arrValue[] = json_encode(array());

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
				'strColumn'     => 'flagPay',
				'flagCondition' => 'eq',
				'value'         => 0,
			),
		);

		$classDb->updateRow(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingCashValue',
			'arrColumn' => $arrColumn,
			'flagAnd'   => 1,
			'arrWhere'  => $arrWhere,
			'arrValue'  => $arrValue,
		));
	}

	/**
		(array(

		))
	 */
	protected function _getVarsLogData($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogCash',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
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
					'value'         => $arr['numFiscalPeriod'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'flagPay',
					'flagCondition' => 'eq',
					'value'         => 0,
				),
			),
		));

		return $rows['arrRows'];
	}

	/**
		(array(
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
		))
	 */
	protected function _setInsertValueDone($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$stampRegister = TIMESTAMP;
		$stampUpdate = TIMESTAMP;
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $arr['numFiscalPeriod'];
		$numFiscalPeriodValue = $arr['numFiscalPeriod'];
		$flagPay = 1;
		$jsonData = json_encode(array());

		$arrayTemp = compact(
			'stampRegister',
			'stampUpdate',
			'idEntity',
			'numFiscalPeriod',
			'numFiscalPeriodValue',
			'flagPay',
			'jsonData'
		);
		$arrColumn = array();
		$arrValue = array();
		foreach ($arrayTemp as $keyTemp => $valueTemp) {
			$arrColumn[] = $keyTemp;
			$arrValue[] = $valueTemp;
		}

		$classDb->insertRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingCashValue',
			'arrColumn' => $arrColumn,
			'arrValue'  => $arrValue,
		));
	}

	/**
		(array(
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
		))
	 */
	protected function _setInsertValuePre($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$stampRegister = TIMESTAMP;
		$stampUpdate = TIMESTAMP;
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $arr['numFiscalPeriod'];
		$flagPay = 0;
		$jsonData = json_encode(array());
		$numStart = $arr['numFiscalPeriod'];
		$numEnd = $arr['numFiscalPeriod'] + $this->_extChildSelf['numCompare'];
		for ($i = $numStart; $i < $numEnd; $i++) {
			$numFiscalPeriodValue = $i;
			$arrayTemp = compact(
				'stampRegister',
				'stampUpdate',
				'idEntity',
				'numFiscalPeriod',
				'numFiscalPeriodValue',
				'flagPay',
				'jsonData'
			);
			$arrColumn = array();
			$arrValue = array();
			foreach ($arrayTemp as $keyTemp => $valueTemp) {
				$arrColumn[] = $keyTemp;
				$arrValue[] = $valueTemp;
			}
			$classDb->insertRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingCashValue',
				'arrColumn' => $arrColumn,
				'arrValue'  => $arrValue,
			));
		}
	}
}
