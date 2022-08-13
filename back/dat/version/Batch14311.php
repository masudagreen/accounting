<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */

/* 改訂注意点
 * 定数排除、Batchを先頭に付ける
* */
require_once(PATH_BACK_DAT_VERSION . 'Batch14311/class/Accounting.php');
require_once(PATH_BACK_DAT_VERSION . 'Batch14311/class/jpn/Jpn.php');
class Code_Batch14311 extends Code_Else_Plugin_Accounting_Jpn_JpnBatch14311
{
	protected $_selfBatch = array(
		'numVersion'     => 0,
		'numVersionThis' => 14311,
	);

	function __construct()
	{
		$arr = @func_get_arg(0);
		if (!$arr) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}
		$this->_selfBatch['numVersion'] = $arr['numVersion'];
	}

	/**
	  *
	  */
	public function run()
	{
		if ($this->_selfBatch['numVersion'] >= $this->_selfBatch['numVersionThis']) {
			return;
		}
		$this->_setBatchJpn();
		$this->_setBatchPath();
		if (FLAG_TEST) {
			$this->_setBatchFSValue();
			exit;

		} else {
			$this->_setBatchFSValue();
		}
	}

	/**

	 */
	protected function _setBatchJpn()
	{
		global $classTime;

		define('PLUGIN_ACCOUNTING_NUM_TIME_ZONE', 9);
		$classTime->setTimeZone(array('data' => PLUGIN_ACCOUNTING_NUM_TIME_ZONE));
	}

	/*
	 *
	 * */
	protected function _setBatchPath()
	{
		define('PATH_BATCH14311_CLASS',   PATH_BACK_DAT_VERSION . 'Batch14311/class/');
		define('PATH_BATCH14311_VARS',   PATH_BACK_DAT_VERSION . 'Batch14311/vars/');
		define('PATH_BATCH14311_TEMPLATES',   PATH_BACK_DAT_VERSION . 'Batch14311/templates/');
	}

	/*
	 *
	 * */
	protected function _setBatchFSValue()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$arrVarsItemEntityPeriod = array();
		$array = $varsPluginAccountingEntity;
		foreach ($array as $key => $value) {
			$idEntity = $key;
			if ((int) $value['flagConfig']) {
				continue;
			}

			$varsPluginAccountingAccount['idEntityCurrent'] = $idEntity;
			$numFiscalPeriodStart = $value['numFiscalPeriodStart'];

			$varsItem = $this->_getBatchVarsItem(array(
				'numFiscalPeriod' => $numFiscalPeriodStart,
				'idEntity'        => $idEntity,
			));

			if ($varsItem['varsEntityNation']['flagCorporation'] != 2) {
				continue;
			}

			$numFiscalPeriodEnd = $value['numFiscalPeriod'];
			$varsPluginAccountingAccount['numFiscalPeriodCurrent'] = $numFiscalPeriodStart;
			for ($i = $numFiscalPeriodStart ; $i <= $numFiscalPeriodEnd; $i++) {
				$numFiscalPeriod = $i;
				if ($numFiscalPeriodStart == $numFiscalPeriod) {
					continue;
				}

				$varsItem = $this->_getBatchVarsItem(array(
					'numFiscalPeriod' => $numFiscalPeriod,
					'idEntity'        => $idEntity,
				));

				$numFiscalPeriodPrev = $numFiscalPeriod - 1;
				$varsItemPrev = array();
				$varsItemPrev = $this->_getBatchVarsItem(array(
					'numFiscalPeriod' => $numFiscalPeriodPrev,
				));

				$this->_setBatchVarsValue(array(
					'varsItem'     => $varsItem,
					'varsItemPrev' => $varsItemPrev,
				));

				//カウント合計額再計算 勘定科目とFS両方
				$classCalcAccountTitle = $this->_getClassCalc(array('flagType' => 'AccountTitle'));
				$flag = $classCalcAccountTitle->allot(array(
					'flagStatus'      => 'calc',
					'numFiscalPeriod' => $numFiscalPeriod,
					'flagCalcDepartment' => 1,
				));
			}
		}


	}

	/**
		(array(
			'varsItem'     => $varsItem,
			'varsItemNext' => $varsItemNext,
		))
	 */
	protected function _setBatchVarsValue($arr)
	{
		$varsFSValue = $this->_getVarsFSValue(array(
			'numFiscalPeriod' => $arr['varsItem']['numFiscalPeriod'],
		));

		$varsFSValuePrev = $this->_getVarsFSValue(array(
			'numFiscalPeriod' => $arr['varsItemPrev']['numFiscalPeriod'],
		));

		$varsFSValue = $this->_loopBatchVarsValue(array(
			'varsFSValue'     => $varsFSValue,
			'varsFSValuePrev' => $varsFSValuePrev,
		));

		$this->_updateDb(array(
			'varsFSValue' => $varsFSValue,
			'varsItem'    => $arr['varsItem'],
		));

		$array = $arr['varsItem']['varsDepartment'];

		foreach ($array as $key => $value) {

			$varsFSValue = $this->_getVarsFSValueDepartment(array(
				'idDepartment'     => $key,
				'numFiscalPeriod'  => $arr['varsItem']['numFiscalPeriod'],
			));

			$varsFSValuePrev = $this->_getVarsFSValueDepartment(array(
				'idDepartment'     => $key,
				'numFiscalPeriod'  => $arr['varsItemPrev']['numFiscalPeriod'],
			));

			if (!$varsFSValuePrev) {
				continue;
			}
			$varsFSValue = $this->_loopBatchVarsValue(array(
				'varsFSValue'     => $varsFSValue,
				'varsFSValuePrev' => $varsFSValuePrev,
			));

			$this->_updateDb(array(
				'idDepartment'  => $key,
				'varsFSValue'  => $varsFSValue,
				'varsItem'   => $arr['varsItem'],
			));
		}

	}

	/**
		(array(
			'idDepartment'    => $key,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))

	 */
	protected function _getVarsFSValueDepartment($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingEntityDepartmentFSValueJpn',
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
					'strColumn'     => 'idDepartment',
					'flagCondition' => 'eq',
					'value'         => $arr['idDepartment'],
				),
			),
		));

		return $rows['arrRows'][0];
	}

	/**
		(array(
			'idDepartment'  => $key,
			'varsValue'     => $varsFSValueNext,
			'varsItemNext'  => $arr['varsItemNext'],
		))
	 */
	protected function _updateDb($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$arrColumn = array();
		$arrValue = array();

		$arrColumn[] = 'jsonJgaapAccountTitleBS';
		$arrValue[] = json_encode($arr['varsFSValue']['jsonJgaapAccountTitleBS']);

		if ($arr['idDepartment']) {
			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingEntityDepartmentFSValueJpn',
				'arrColumn' => $arrColumn,
				'flagAnd'  => 1,
				'arrWhere'  => array(
					array(
						'flagType'      => '',
						'strColumn'     => 'id',
						'flagCondition' => 'eq',
						'value'         => $arr['varsFSValue']['id'],
					),
				),
				'arrValue'  => $arrValue,
			));

		} else {
			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingFSValueJpn',
				'arrColumn' => $arrColumn,
				'flagAnd'  => 1,
				'arrWhere'  => array(
					array(
						'flagType'      => '',
						'strColumn'     => 'id',
						'flagCondition' => 'eq',
						'value'         => $arr['varsFSValue']['id'],
					),
				),
				'arrValue'  => $arrValue,
			));
		}
	}



	/**
		(array(
			'varsFSValue'     => $varsFSValue,
			'varsFSValuePrev' => $varsFSValuePrev,
		))
	 */
	protected function _loopBatchVarsValue($arr)
	{
		$flagFS = 'jsonJgaapAccountTitleBS';
		$array = $arr['varsFSValue'][$flagFS];
		foreach ($array as $key => $value) {
			$flagPeriod = $key;

			//
			$profitBroughtForward = $arr['varsFSValuePrev'][$flagFS][$flagPeriod]['profitBroughtForward']['sumNext'];
			$accountsPayablesSum = $arr['varsFSValuePrev'][$flagFS][$flagPeriod]['accountsPayablesSum']['sumNext'];
			$accountsReceivablesSum = $arr['varsFSValuePrev'][$flagFS][$flagPeriod]['accountsReceivablesSum']['sumNext'];
			$netIncome = $arr['varsFSValuePrev'][$flagFS][$flagPeriod]['netIncome']['sumNext'];

			$sumPrev = $profitBroughtForward + $netIncome + $accountsPayablesSum - $accountsReceivablesSum;
			$array[$key]['profitBroughtForward']['sumPrev'] = $sumPrev;

			//
			$sumNext = $sumPrev
				+ $value['profitBroughtForward']['sumCredit'] //
				- $value['profitBroughtForward']['sumDebit'];

			$array[$key]['profitBroughtForward']['sumNext'] = $sumNext;
		}
		$arr['varsFSValue'][$flagFS] = $array;


		return $arr['varsFSValue'];

	}

	/**
		(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _getBatchVarsItem($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $arr['idEntity'],
		));

		$varsDepartment = $this->_getVarsDepartment(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));


		$data = array(
			'varsEntityNation'       => $varsEntityNation,
			'varsDepartment'         => $varsDepartment,
			'numFiscalPeriod'        => $arr['numFiscalPeriod'],
		);

		return $data;

	}

	/**
	 overwrite
	 */
	protected function _getVarsDepartment($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingEntityDepartment',
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

		$arrayNew = array();
		$array = $rows['arrRows'];
		foreach ($array as $key => $value) {
			$arrayNew[$value['idDepartment']] = 1;
		}

		return $arrayNew;
	}

	/**
overwrite
*/
	protected function _getVarsDepartmentTreeItem()
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$strNation = 'jpn';

		$vars = $this->_getVars(array(
			'path'      => $this->_self['varsDepartment'],
			'strLang'   => $varsPluginAccountingEntity[$varsPluginAccountingAccount['idEntityCurrent']]['strLang'],
			'strNation' => $strNation,
		));

		return $vars;
	}

	/**
	 *
	 * overwrite

	 */
	protected function _getVarsEntityNation($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;


		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		if ($arr['idEntity']) {
			$idEntity = $arr['idEntity'];
		}

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingEntityJpn',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idEntity',
					'flagCondition' => 'eq',
					'value'         => $idEntity,
				),
				array(
					'flagType'      => '',
					'strColumn'     => 'numFiscalPeriod',
					'flagCondition' => 'eq',
					'value'         => $arr['numFiscalPeriod'],
				),
			),
		));

		if (!$rows['numRows']) {
			return array();
		}

		$array = $rows['arrRows'][0];
		foreach ($array as $key => $value) {
			if (preg_match("/^json/", $key)) {
				$array[$key] = $value;

			} else {
				$array[$key] = (int) $value;
			}
		}

		return $array;
	}

	/**
overwrite
	 */
	protected function _getVarsConsumptionTax($arr)
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$strNation = 'jpn';

		$strLang = $varsPluginAccountingEntity[$varsPluginAccountingAccount['idEntityCurrent']]['strLang'];
		if ($arr['strLang']) {
			$strLang = $arr['strLang'];
		}

		$vars = $this->_getVars(array(
			'path'      => $this->_self['varsTax'],
			'strLang'   => $strLang,
			'strNation' => $strNation,
		));

		$arrayStr = array('generalProration', 'generalEach', 'simple');
		foreach ($arrayStr as $keyStr => $valueStr) {
			$array = $vars[$valueStr];
			foreach ($array as $key => $value) {
				$str = 'arrStr' . ucwords($valueStr);
				$vars[$str][$value['value']] = $value['strTitle'];
			}
		}

		return $vars;
	}

	/**
overwrite
	 */
	protected function _getVarsFSValue($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords('jpn');

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingFSValue' . $strNation,
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

		return $rows['arrRows'][0];
	}
}