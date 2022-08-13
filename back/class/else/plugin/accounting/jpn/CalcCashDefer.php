<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcCashDefer extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extChildSelf = array(
		'varsItem' => array(),
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
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__ . $method);
			}
			exit;
		}
	}

	/**


	/**
		Portal->CalcLogConsumptionTax->this
		(array(
			'flagStatus'       => 'UpdateTax',
			'arrRows'          => $arrVarsLogAll,
			'arrRowsPrev'      => $arrRows,
			'numFiscalPeriod'  => $arr['varsItem']['numFiscalPeriod'],
			'idEntity'         => $arr['idEntity'],
			'varsEntityNation' => $arr['varsItem']['varsEntityNationUpdate'],
		))
	 */
	protected function _iniUpdateTax($arr)
	{
		$flagErrorVars = $this->_updateDbTax(array(
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'idEntity'         => $arr['idEntity'],
			'arrVarsLog'       => $arr['arrRows'],
			'arrVarsLogPrev'   => $arr['arrRowsPrev'],
			'varsEntityNation' => $arr['varsEntityNation'],
		));

		if ($flagErrorVars) {
			return $flagErrorVars;
		}
	}

	/**
		(array(
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'idEntity'         => $arr['idEntity'],
			'arrVarsLog'       => $arr['arrRows'],
			'arrVarsLogPrev'   => $arr['arrRowsPrev'],
			'varsEntityNation' => $arr['varsEntityNation'],
		));
	 */
	protected function _updateDbTax($arr)
	{
		global $classDb;

		$classCalcLog = $this->_getClassCalc(array('flagType' => 'Log'));
		$flagMax = 0;
		$arrayIdLog = array();

		$array = $arr['arrVarsLog'];
		foreach ($array as $key => $value) {

			$dataVarsion = end($value['jsonVersion']);
			$tempValue = array(
				'numFiscalPeriod'         => $arr['numFiscalPeriod'],
				'idEntity'                => $arr['idEntity'],
				'idAccount'               => '',
				'flagFiscalReport'        => '',
				'stampBook'               => '',
				'strTitle'                => '',
				'jsonDetail'              => $dataVarsion['jsonDetail'],
				'arrCommaIdLogFile'       => '',
				'arrCommaIdAccountPermit' => '',
				'numSumMax'               => '',
				'arrSpaceStrTag'          => '',
			);

			$varsVersion = $classCalcLog->allot(array(
				'flagStatus'       => 'varsVersion',
				'arrValue'         => $tempValue,
				'numFiscalPeriod'  => $arr['numFiscalPeriod'],
				'varsEntityNation' => $arr['varsEntityNation']
			));

			$arrCommaConsumptionTaxDebit = $varsVersion['arrCommaConsumptionTaxDebit'];
			$arrCommaRateConsumptionTaxDebit = $varsVersion['arrCommaRateConsumptionTaxDebit'];
			$arrCommaConsumptionTaxWithoutCalcDebit = $varsVersion['arrCommaConsumptionTaxWithoutCalcDebit'];

			$arrCommaTaxPaymentDebit = $varsVersion['arrCommaTaxPaymentDebit'];
			$arrCommaTaxReceiptDebit = $varsVersion['arrCommaTaxReceiptDebit'];

			$arrCommaConsumptionTaxCredit = $varsVersion['arrCommaConsumptionTaxCredit'];
			$arrCommaRateConsumptionTaxCredit = $varsVersion['arrCommaRateConsumptionTaxCredit'];
			$arrCommaConsumptionTaxWithoutCalcCredit = $varsVersion['arrCommaConsumptionTaxWithoutCalcCredit'];

			$arrCommaTaxPaymentCredit = $varsVersion['arrCommaTaxPaymentCredit'];
			$arrCommaTaxReceiptCredit = $varsVersion['arrCommaTaxReceiptCredit'];


			$jsonVersion = json_encode($value['jsonVersion']);

			$flag = $this->checkTextSize(array(
				'flag'        => 'errorDataMax',
				'str'         => $jsonVersion,
				'flagReturn'  => 1,
			));

			if ($flag) {
				$flagMax = 1;
				$arrayIdLog[] = $value['idLogCash'];
				continue;
			}

			$stampUpdate = TIMESTAMP;
			$arrayTemp = compact(
				'stampUpdate',
				'jsonVersion',
				'arrCommaConsumptionTaxDebit',
				'arrCommaRateConsumptionTaxDebit',
				'arrCommaConsumptionTaxWithoutCalcDebit',
				'arrCommaTaxPaymentDebit',
				'arrCommaTaxReceiptDebit',
				'arrCommaConsumptionTaxCredit',
				'arrCommaRateConsumptionTaxCredit',
				'arrCommaConsumptionTaxWithoutCalcCredit',
				'arrCommaTaxPaymentCredit',
				'arrCommaTaxReceiptCredit'
			);
			$arrColumn = array();
			$arrValue = array();
			foreach ($arrayTemp as $keyTemp => $valueTemp) {
				$arrColumn[] = $keyTemp;
				$arrValue[] = $valueTemp;

				if ($keyTemp == 'jsonVersion') {
					$valueTemp = $value['jsonVersion'];
				}
			}

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingLogCashDefer',
				'arrColumn' => $arrColumn,
				'flagAnd'  => 1,
				'arrWhere'  => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'id',
						'flagCondition' => 'eq',
						'value'         => $value['id'],
					),
				),
				'arrValue'  => $arrValue,
			));
		}

		if ($flagMax) {
			$data = array(
				'flag'      => 'textMaxOver',
				'arrIdLog'  => $arrayIdLog,
			);
			return $data;
		}
	}


}
