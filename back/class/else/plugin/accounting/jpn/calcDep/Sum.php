<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcDep_Sum extends Code_Else_Plugin_Accounting_Jpn_CalcDep
{
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

	/**
		(array(
			'flagStatus'      => 'check',
			'flagDepMethod'   => 'straight',
			'varsValue'       => $varsValue,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	public function allot($arr)
	{
		$method = '_ini' . ucwords($arr['flagStatus']);
		if (method_exists($this, $method)) {
			return $this->$method($arr);

		} else {
			return $this->_varsCalc;
		}
	}

	/**
		(array(
				'flagStatus'      => 'start',
				'flagDepMethod'   => $arrValue['arr']['flagDepMethod'],
				'arrValue'        => ($varsValue)? $varsValue : array(),
				'numFiscalPeriod' => $numFiscalPeriod,
		))
	 */
	protected function _iniStart($arr)
	{
		$varsItem = $this->_getVarsItem(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		$data = array();
		$array = $varsItem['varsTimeTable'];
		foreach ($array as $key => $value) {
			$numFiscalPeriod = $value['numFiscalPeriod'];
			if (!$numFiscalPeriod) {
				continue;
			}
			$varsValue = $this->_getVarsValue(array(
				'varsItem'             => $varsItem,
				'numFiscalPeriodStart' => $value['numFiscalPeriod'],
				'numFiscalPeriod'      => $arr['numFiscalPeriod'],
			));
			$varsValue = $this->_updateVarsValue(array(
				'varsValue'            => $varsValue,
				'varsItem'             => $varsItem,
				'numFiscalPeriodStart' => $value['numFiscalPeriod'],
				'numFiscalPeriod'      => $arr['numFiscalPeriod'],
			));
			$data[$numFiscalPeriod] = $varsValue;

		}

		return $data;
	}

		/**
		(array(
				'varsValue'       => $varsValue,
				'varsItem'        => $varsItem,
				'numFiscalPeriod' => $value['numFiscalPeriod'],
		))
	 */
	protected function _updateVarsValue($arr)
	{
		$varsValue = $arr['varsValue'];

		$numValueNet = $varsValue['numValue'] - $varsValue['numValueCompression'];

		$flagType = $arr['varsItem']['varsFixedAssets']['flagFractionDep'];
		$numMonths = 36;
		$sumDepLimit = 0;
		$varsEntityNation = array();
		$array = $arr['varsItem']['varsTimeTable'];
		foreach ($array as $key => $value) {
			if ($value['numFiscalPeriod'] < $arr['numFiscalPeriodStart']) {
				continue;
			}
			if ($value['numFiscalPeriod'] == $arr['numFiscalPeriod']) {
				$varsEntityNation = $value;
				break;
			}

			$numValueDepLimit = $this->_updateCalc(array(
				'flagType' => $flagType,
				'num'      => $numValueNet * ($value['numFiscalTermMonth'] / 36),
				'numLevel' => 0
			));

			if ($value['numFiscalPeriod'] > 0) {
				$varsValue['varsDetail'][$value['numFiscalPeriod']]['numValueDepLimit'] = $numValueDepLimit;
			}
			$sumDepLimit += $numValueDepLimit;
			$numMonths -= $value['numFiscalTermMonth'];
		}

		//last
		if ($numMonths > $varsEntityNation['numFiscalTermMonth']) {
			$numValueDepLimit = $this->_updateCalc(array(
				'flagType' => $flagType,
				'num'      => $numValueNet * ($varsEntityNation['numFiscalTermMonth'] / 36),
				'numLevel' => 0
			));

		} else {
			$numValueDepLimit = $numValueNet - $sumDepLimit;
			if ($numValueDepLimit < 0) {
				$numValueDepLimit = 0;
			}
		}
		$varsValue['varsDetail'][$varsEntityNation['numFiscalPeriod']]['numValueDepLimit'] = $numValueDepLimit;

		return $varsValue;
	}


	/**
		(array(

		));
	 */
	protected function _getVarsValue($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $varsPluginAccountingAccount;

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $arr['numFiscalPeriodStart'],
		));

		$varsStampFiscalPeriod = $this->_getVarsStampFiscalPeriod(array(
			'numFiscalPeriod'  => $arr['numFiscalPeriodStart'],
			'varsEntityNation' => $varsEntityNation,
		));

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);
		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogFixedAssets' . $strNation,
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
					'flagType'      => '',
					'strColumn'     => 'flagDepMethod',
					'flagCondition' => 'eq',
					'value'         => 'sum',
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'numFiscalPeriod',
					'flagCondition' => 'eq',
					'value'         => $arr['numFiscalPeriod'],
				),
				array(
					'flagType'      => '',
					'strColumn'     => 'stampStart',
					'flagCondition' => 'eqBig',
					'value'         => $varsStampFiscalPeriod['f1']['stampMin'],
				),
				array(
					'flagType'      => '',
					'strColumn'     => 'stampStart',
					'flagCondition' => 'eqSmall',
					'value'         => $varsStampFiscalPeriod['f1']['stampMax'],
				),
			),
		));

		$data = array(
			'numValue'            => 0,
			'numValueCompression' => 0,
			'varsDetail'          => array(),
		);

		$array = $rows['arrRows'];
		foreach ($array as $key => $value) {
			$data['numValue'] += $value['numValue'];
			$data['numValueCompression'] += $value['numValueCompression'];
		}

		return $data;
	}

	/**
		_getVarsItem(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _getVarsItem($arr)
	{
		$varsFixedAssets = $this->_getVarsFixedAssets(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$varsStampFiscalPeriod = $this->_getVarsStampFiscalPeriod(array(
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'varsEntityNation' => $varsEntityNation,
		));

		$varsTimeTable = $this->_getVarsTimeTable(array(
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
		));

		$data = array(
			'varsFixedAssets'       => $varsFixedAssets,
			'varsEntityNation'      => $varsEntityNation,
			'varsTimeTable'         => $varsTimeTable,
			'varsStampFiscalPeriod' => $varsStampFiscalPeriod,
		);

		return $data;
	}

	/**
		(array(
			'varsItem'        => $varsItem,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'arrValue'        => $arr['arrValue'],
		))
	 */
	protected function _getVarsTimeTable($arr)
	{
		$arrayNew = array();

		$numMonths = 36 - 1;
		$numStart = $arr['numFiscalPeriod'] - 1;
		for ($i = $numStart; $i > 0; $i--) {
			$numFiscalPeriod = $i;
			$varsEntityNation = $this->_getVarsEntityNation(array(
				'numFiscalPeriod' => $numFiscalPeriod,
			));
			if (!$varsEntityNation) {
				break;
			}
			$numMonths -= $varsEntityNation['numFiscalTermMonth'];
			array_unshift($arrayNew, $varsEntityNation);
			if ($numMonths <= 0) {
				break;
			}
		}

		$numStart = 36 - 1;
		for ($i = $numStart; $i > 0; $i--) {
			$varsEntityNation = array(
				'numFiscalPeriod' => 0,
				'numFiscalTermMonth' => 12,
			);
			$numMonths -= $varsEntityNation['numFiscalTermMonth'];
			array_unshift($arrayNew, $varsEntityNation);
			if ($numMonths <= 0) {
				break;
			}
		}
		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		$arrayNew[] = $varsEntityNation;

		return $arrayNew;
	}








}
