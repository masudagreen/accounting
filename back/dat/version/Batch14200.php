<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
require_once(PATH_BACK_DAT_VERSION . 'Batch14200/class/Accounting.php');
require_once(PATH_BACK_DAT_VERSION . 'Batch14200/class/jpn/Jpn.php');
/* 改訂方法
 * 定数排除、Batch14200を付ける
* */

class Code_Batch14200 extends Code_Else_Plugin_Accounting_Jpn_JpnBatch14200
{
	protected $_selfBatch = array(
		'numVersion'     => 0,
		'numVersionThis' => 14200,
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
		$this->_setBatchPath();
		$this->_setBatchAccounting();
		$this->_setInitEntity();

		if (FLAG_TEST) {

		} else {
			$this->_setBatchTable();
			$this->_setBatchColumn();
			$this->_updateBatchAccountTitle();
		}
    }

    /**

	 */
	protected function _setBatchColumn()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$stmt = $dbh->prepare('alter table accountingFixedAssetsJpn add numRatioOperateDepSum decimal(5, 2) default "100.00" after jsonDepSum;');
		$stmt->execute();
	}

    /*

	 * */
	protected function _setBatchJpn($arr)
	{
		global $varsPluginAccountingAccount;
		global $classTime;

		global $batch14200PLUGIN_ACCOUNTING_NUM_TIME_ZONE;

		$batch14200PLUGIN_ACCOUNTING_NUM_TIME_ZONE = 9;
		$classTime->setTimeZone(array('data' => $batch14200PLUGIN_ACCOUNTING_NUM_TIME_ZONE));

		global $batch14200PLUGIN_ACCOUNTING_NUM_YEAR_SHEET;
		$batch14200PLUGIN_ACCOUNTING_NUM_YEAR_SHEET = $arr['varsEntityNation']['numYearSheet'];

		global $batch14200PLUGIN_ACCOUNTING_FLAG_CORPORATION;
		if ($arr['varsEntityNation']['flagCorporation'] == 2) {
			$batch14200PLUGIN_ACCOUNTING_FLAG_CORPORATION = 'public';
		} else {
			$batch14200PLUGIN_ACCOUNTING_FLAG_CORPORATION = '';
		}
	}

    	/*
	 *
	 * */
	protected function _setBatchAccounting()
	{
		$this->_setInit();
		//本来はentityごとに呼び出す。
		$this->_setInitNation();
		$this->_setInitLang();
		$varsEntityNation = array();
		$varsEntityNation['flagCorporation'] = 2;
		$this->_setBatchJpn(array('varsEntityNation' => $varsEntityNation));
	}

    /*
     *
    * */
    protected function _setInitEntity()
    {
    	global $classInit;
    	global $varsPluginAccountingEntity;

    	$varsPluginAccountingEntity = (FLAG_APC)? apc_fetch('varsPluginAccountingEntity'): null;
    	if (is_null($varsPluginAccountingEntity)) {
    		$this->_updateInitEntity();
    	}
    }

    /*
     *
    * */
    protected function _updateInitEntity()
    {
    	global $classInit;
    	global $varsPluginAccountingEntity;

    	$classInit->updateVarsAll(array(
    		'vars'      => &$varsPluginAccountingEntity,
    		'strVars'   => 'varsPluginAccountingEntity',
    		'strTable'  => 'accountingEntity',
    	));
    }

    /*
	 *
	 * */
	protected function _setBatchPath()
	{
		define('PATH_BATCH14200_CLASS',   PATH_BACK_DAT_VERSION . 'Batch14200/class/');
		//define('PATH_BATCH14200_VARS',   PATH_BACK_DAT_VERSION . 'Batch14200/vars/');
		define('PATH_BATCH14200_TEMPLATES',   PATH_BACK_DAT_VERSION . 'Batch14200/templates/');
	}

    /**

	 */
	protected function _setBatchTable()
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $classEscape;

		$vars = $classEscape->getVars(array(
			'data' => PATH_BATCH14200_TEMPLATES . 'config.php',
			'arr'  => array(),
		));

		$flag55 = $classDb->checkVersion55();

		$array = $vars;
		foreach ($array as $key => $value) {
			if ($value['table'] == 'accountingBlueSheetJpn') {
				//drop
				$sql = 'drop table if exists ' . $value['table'] . ';';
				$stmt = $dbh->prepare($sql);
				$stmt->execute();

				//create
				$sql = 'create table ';
				$sql .= $value['table'] . '(';

				$arrayChild = $value['index'];
				$numLimit = count($arrayChild) - 1;
				$strColumn = '';
				foreach ($arrayChild as $keyChild => $valueChild) {
					$strColumn .= ' '
								. $arrayChild[$keyChild]['column']
								. ' '
								. $arrayChild[$keyChild]['type'];

					if ($keyChild != $numLimit) {
						$strColumn .= ',';
					}
				}

				if ($flag55) {
					$value['db'] = str_replace('type', 'engine', $value['db']);
				}
				$sql .= $strColumn . ')' . $value['db'] . ';';

				$stmt = $dbh->prepare($sql);
				$stmt->execute();
			}
		}
	}

	/**

	*/
	protected function _updateBatchAccountTitle()
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;
		global $batch14200PLUGIN_ACCOUNTING_STR_NATION;

		$strNation = ucwords($batch14200PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingFS' . $strNation,
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(),
		));

		$arrayColumn = array(
			'jsonJgaapFSPL',
		);

		$array = $rows['arrRows'];
		foreach ($array as $key => $value) {
			if (!$arrVarsEntityNation[$value['idEntity']][$value['numFiscalPeriod']]) {
				$arrVarsEntityNation[$value['idEntity']][$value['numFiscalPeriod']] = $this->_getBatchVarsEntityNation(array(
					'idEntity'        => $value['idEntity'],
					'numFiscalPeriod' => $value['numFiscalPeriod'],
				));
			}

			$varsPluginAccountingAccount['idEntityCurrent'] = $value['idEntity'];
			$varsPluginAccountingAccount['numFiscalPeriodCurrent'] = $value['numFiscalPeriod'];

			$this->_setInitNation();
			$this->_setInitLang();
			$this->_setBatchJpn(array('varsEntityNation' => $arrVarsEntityNation[$value['idEntity']][$value['numFiscalPeriod']]));

			if ($arrVarsEntityNation[$value['idEntity']][$value['numFiscalPeriod']]['flagCorporation'] == 2) {
				$arrDbColumn = array();
				$arrDbValue = array();

				foreach ($arrayColumn as $keyColumn => $valueColumn) {
					if (!$value[$valueColumn]) {
						continue;
					}
					if ($valueColumn == 'jsonJgaapFSPL') {
						$varsFS = $this->_updateBatchAccountTitleVarsJgaapFSPL(array(
							'vars' => $value[$valueColumn],
						));
					}

					$jsonAccountTitle = json_encode($varsFS);
					$strAccountTitle = $valueColumn;
					$arrDbColumn[] = $strAccountTitle;
					$arrDbValue[] = $jsonAccountTitle;
				}

				$strNation = ucwords($batch14200PLUGIN_ACCOUNTING_STR_NATION);

				$classDb->updateRow(array(
					'idModule'  => 'accounting',
					'strTable' => 'accountingFS' . $strNation,
					'arrColumn' => $arrDbColumn,
					'flagAnd'  => 1,
					'arrWhere' => array(
						array(
							'flagType'      => 'num',
							'strColumn'     => 'id',
							'flagCondition' => 'eq',
							'value'         => $value['id'],
						),
					),
					'arrValue'  => $arrDbValue,
				));
			}

			if ((int) $varsPluginAccountingEntity[$value['idEntity']]['flagConfig']) {
				continue;
			}

			//都度呼び出す、$varsEntityNation['flagCorporation']の関係で
			$classCalcAccountTitle = $this->_getClassCalc(array('flagType' => 'AccountTitle'));
			$classCalcAccountTitle->allot(array(
				'flagStatus'      => 'calc',
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			));

		}
	}

	/**
	 */
	protected function _getBatchVarsEntityNation($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;
		global $batch14200PLUGIN_ACCOUNTING_STR_NATION;

		$strNation = ucwords($batch14200PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingEntity' . $strNation,
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idEntity',
					'flagCondition' => 'eq',
					'value'         => $arr['idEntity'],
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

	protected function _updateBatchAccountTitleVarsJgaapFSPL($arr)
	{
		$array = &$arr['vars'];

		foreach ($array as $key => $value) {
			if ($value['vars']['idTarget'] == 'selfConsumption') {
				$array[$key]['vars']['flagDebit'] = 0;
			}
			if ($value['child']) {
				$array[$key]['child'] = $this->_updateBatchAccountTitleVarsJgaapFSPL(array(
					'vars'          => $array[$key]['child'],
				));
			}
		}

		return $array;
	}



}