<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcLogHouse extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extChildSelf = array(

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

	/*
		(array(
			'flagStatus'      => 'check',
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $idEntity,
			'arrOrder'         => array(),
		))
	 * */
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
			'flagStatus'       => 'UpdateVarsTax',
			'arrRows'          => $arrVarsLogAll,
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'idEntity'         => $arr['idEntity'],
		))
	 */
	protected function _iniUpdateVarsTax($arr)
	{
		$classCalcLogConsumptionTax = $this->_getClassCalc(array('flagType' => 'LogConsumptionTax'));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$arrRows = $classCalcLogConsumptionTax->allot(array(
			'flagStatus'       => 'UpdateVars',
			'arrRows'          => $arr['arrRows'],
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'idEntity'         => $arr['idEntity'],
		));

		$flagErrorVars = $this->_updateDbTax(array(
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'idEntity'         => $arr['idEntity'],
			'arrVarsLog'       => $arrRows,
			'varsEntityNation' => $varsEntityNation,
		));

		if ($flagErrorVars) {
			return $flagErrorVars;
		}
	}

	/**
		(array(
			'flagStatus'       => 'UpdateTax',
			'arrRows'          => $arrVarsLogAll,
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'idEntity'         => $arr['idEntity'],
			'varsEntityNation' => $arr['varsItem']['varsEntityNationUpdate'],
		))
	 */
	protected function _iniUpdateTax($arr)
	{
		$flagErrorVars = $this->_updateDbTax(array(
			'varsItem'         => $arr['varsItem'],
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'idEntity'         => $arr['idEntity'],
			'arrVarsLog'       => $arr['arrRows'],
			'varsEntityNation' => $arr['varsEntityNation'],
		));

		if ($flagErrorVars) {
			return $flagErrorVars;
		}
	}

	/**
		(array(
			'varsItem'         => $arr['varsItem'],
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'idEntity'         => $arr['idEntity'],
			'arrVarsLog'       => $arr['arrRows'],
			'varsEntityNation' => $arr['varsEntityNation'],
		));
	 */
	protected function _updateDbTax($arr)
	{
		global $classDb;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);
		$classCalcLog = $this->_getClassCalc(array('flagType' => 'Log'));
		$flagMax = 0;
		$arrayIdLog = array();

		$array = &$arr['arrVarsLog'];
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
				'varsEntityNation' => $arr['varsEntityNation'],
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
			$stampUpdate = TIMESTAMP;

			$flag = $this->checkTextSize(array(
				'flag'        => 'errorDataMax',
				'str'         => $jsonVersion,
				'flagReturn'  => 1,
			));

			if ($flag) {
				$flagMax = 1;
				$arrayIdLog[] = $value['idLogHouse'];
				continue;
			}

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
			}

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingLogHouse' . $strNation,
				'arrColumn' => $arrColumn,
				'flagAnd'  => 1,
				'arrWhere'  => array(
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
						'strColumn'     => 'idLogHouse',
						'flagCondition' => 'eq',
						'value'         => $value['idLogHouse'],
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
