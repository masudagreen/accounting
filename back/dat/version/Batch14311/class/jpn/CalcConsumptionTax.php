<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcConsumptionTaxBatch14311 extends Code_Else_Plugin_Accounting_Jpn_JpnBatch14311
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
			'flagStatus'    => 'edit',
			'arrRowsAdd'    => $arrRowsAdd,
			'arrRowsDelete' => $arrRowsDelete,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _iniEdit($arr)
	{
		$this->_iniDelete(array(
			'arrRows'         => $arr['arrRowsDelete'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		$flag = $this->_iniAdd(array(
			'arrRows'         => $arr['arrRowsAdd'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}
	}

	/**
		(array(
			'flagStatus' => 'add',
			'arrRows'    => $arrayNew,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _iniAdd($arr)
	{
		$varsItem = $this->_getVarsItem(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		if ($varsItem['varsEntityNation']['flagConsumptionTaxFree']) {
			return;
		}
		$varsValue = $this->_getValueAdd(array(
			'arrRows'  => $arr['arrRows'],
			'varsItem' => $varsItem,
		));

		$flag = $this->_updateDb(array(
			'varsValue' => $varsValue,
			'varsItem'  => $varsItem,
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}

		$this->_updateDbPreferenceStamp(array('strColumn' => 'fsValue'));
	}


	/**
		(array(
			'flagStatus' => 'Delete',
			'arrRows'    => array,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _iniDelete($arr)
	{
		$arr['arrRows'] = $this->_getArrRowsReverse(array(
			'arrRows'         => $arr['arrRows'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		$flag = $this->_iniAdd(array(
			'arrRows'         => $arr['arrRows'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}
	}

	/**
		(array(
			'arrRows'    => array,
		))
	 */
	protected function _getArrRowsReverse($arr)
	{
		$array = &$arr['arrRows'];
		foreach ($array as $key => $value) {
			$array[$key]['flagDebit'] = ($array[$key]['flagDebit'])? 0 : 1;
		}

		return $array;
	}


	/**

	 */
	protected function _getVarsItem($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

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
		$str = 'jsonConsumptionTax';
		$varsFS = $rows['arrRows'][0];

		$varsConsumptionTax = $this->_getVarsConsumptionTax(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		$arrayFiscalPeriod = $this->_getVarsFlagFiscalPeriod(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		$array = $arrayFiscalPeriod;
		$varsFiscalPeriod = array();
		foreach ($array as $key => $value) {
			$varsFiscalPeriod[$value] = $this->_getVarsStampTerm(array(
				'varsFlag'         => array('flagFiscalPeriod' => $value),
				'varsEntityNation' => $varsEntityNation,
				'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			));
		}

		$data = array(
			'varsFS'             => $varsFS[$str],
			'varsConsumptionTax' => $varsConsumptionTax,
			'varsEntityNation'   => $varsEntityNation,
			'varsFiscalPeriod'   => $varsFiscalPeriod,
			'numFiscalPeriod'    => $arr['numFiscalPeriod'],
		);

		return $data;

	}

	/**
		(array(
			'arrRows'  => $arr['arrRows'],
			'varsItem' => $varsItem,
		))
	 */
	protected function _getValueAdd($arr)
	{
		$arrayNew = array();
		$array = $arr['varsItem']['varsFiscalPeriod'];
		foreach ($array as $key => $value) {
			$arrayNew[$key] = array();
			$arrayRowsNew = array();
			$num = 0;
			$arrayRows = &$arr['arrRows'];
			foreach ($arrayRows as $keyRows => $valueRows) {
				if ($value['stampMin'] <= $valueRows['stampBook'] && $valueRows['stampBook'] <= $value['stampMax']) {
					$arrayRowsNew[$num] = $valueRows;
					$num++;
				}
			}
			if ($arrayRowsNew) {
				$arrayNew[$key] = $this->_getValueAddLoop(array(
					'arrRows'   => $arrayRowsNew,
					'varsItem'  => $arr['varsItem'],
					'varsValue' => $arr['varsItem']['varsFS'][$key],
				));

			}
		}

		return $arrayNew;
	}

	/**
		(array(
			'arrRows'  => $arr['arrRows'],
			'varsItem' => $varsItem,
		))
	 */
	protected function _getValueAddLoop($arr)
	{
		$flagConsumptionTaxIncluding = (int) $arr['varsItem']['varsEntityNation']['flagConsumptionTaxIncluding'];
		$varsValue = $arr['varsValue'];

		$strTax = 'simple';
		$strTaxRule = 'flagConsumptionTaxSimpleRule';
		if ((int) $arr['varsItem']['varsEntityNation']['flagConsumptionTaxGeneralRule']) {
			if ((int) $arr['varsItem']['varsEntityNation']['flagConsumptionTaxDeducted']) {
				$strTax = 'generalEach';
				$strTaxRule = 'flagConsumptionTaxGeneralRuleEach';

			} else {
				$strTax = 'generalProration';
				$strTaxRule = 'flagConsumptionTaxGeneralRuleProration';
			}
		}

		$arrayNew = array();
		$arrayTax = $arr['varsItem']['varsConsumptionTax'][$strTax];
		$array = &$arr['arrRows'];

		foreach ($array as $key => $value) {
			if ($value[$strTaxRule] == 'none' || !$value[$strTaxRule]) {
				continue;
			}
			foreach ($arrayTax as $keyTax => $valueTax) {
				if ($valueTax['value'] == 'none') {
					continue;
				}

				$data = array();
				if (is_null($arrayNew[$valueTax['value']])) {
					if (is_null($varsValue[$valueTax['value']])) {
						if (preg_match("/^tax/", $valueTax['value'])) {
							$data['inBody'] = 0;
							$data['outBody'] = 0;
							$data['otherBody'] = 0;
							$data['includeBody'] = 0;
							$data['totalBody'] = 0;

							$data['inTax'] = 0;
							$data['outTax'] = 0;
							$data['otherTax'] = 0;
							$data['includeTax'] = 0;
							$data['totalTax'] = 0;

							$data['inSum'] = 0;
							$data['outSum'] = 0;
							$data['otherSum'] = 0;
							$data['includeSum'] = 0;
							$data['totalSum'] = 0;

						} else {
							$data['inSum'] = 0;
							$data['outSum'] = 0;
							$data['otherSum'] = 0;
							$data['includeSum'] = 0;
							$data['totalSum'] = 0;
						}

					} else {
						$data = $varsValue[$valueTax['value']];
					}

				} else {
					$data = $arrayNew[$valueTax['value']];
				}

				if ($valueTax['value'] == $value[$strTaxRule]) {
					$numValue = 0;
					$strType = '';

					if ($flagConsumptionTaxIncluding) {
						if (preg_match("/^tax/", $valueTax['value'])) {
							if ($value['flagConsumptionTaxWithoutCalc'] == 3) {
								$strType = 'otherTax';

							} else {
								$strType = 'includeBody';
							}

						} else {
							$strType = 'totalSum';
						}

					} else {
						$flagTax = 0;
						if ($value['idAccountTitle'] == 'suspensePaymentConsumptionTaxes'
							 || $value['idAccountTitle'] == 'suspenseReceiptOfConsumptionTaxes'
						) {
							$flagTax = 1;
						}

						if (preg_match("/^tax/", $valueTax['value'])) {
							if ($value['flagConsumptionTaxWithoutCalc'] == 1) {
								$strType = ($flagTax)? 'inTax' : 'inBody';

							} else if ($value['flagConsumptionTaxWithoutCalc'] == 2) {
								$strType = ($flagTax)? 'outTax' : 'outBody';

							} else if ($value['flagConsumptionTaxWithoutCalc'] == 3) {
								$strType = ($flagTax)? 'otherTax' : 'otherBody';
							}

						} else {
							$strType = 'totalSum';
						}

					}
					$numValue = $value['numValue'];
					if ($valueTax['flagDebit']) {
						if (!$value['flagDebit']) {
							$numValue *= (-1);
						}

					} else {
						if ($value['flagDebit']) {
							$numValue *= (-1);
						}
					}

					if ($strType != 'totalSum') {
						$data[$strType] += $numValue;
						if (preg_match("/^include/", $strType)) {
							$strTypeItemSum = 'includeSum';

						} elseif (preg_match("/^out/", $strType)) {
							$strTypeItemSum = 'outSum';

						} elseif (preg_match("/^other/", $strType)) {
							$strTypeItemSum = 'otherSum';

						} elseif (preg_match("/^in/", $strType)) {
							$strTypeItemSum = 'inSum';
						}
						$data[$strTypeItemSum] += $numValue;

						if (preg_match("/Tax$/", $strType)) {
							$strTypeSum = 'totalTax';

						} elseif (preg_match("/Body$/", $strType)) {
							$strTypeSum = 'totalBody';
						}
						$data[$strTypeSum] += $numValue;
						$strType = 'totalSum';
					}

					//totalSum
					$data[$strType] += $numValue;
					$arrayNew[$valueTax['value']] = $data;

				}
				$arrayNew[$valueTax['value']] = $data;
			}
		}

		return $arrayNew;
	}

	/**
		(array(
			'varsValue' => $varsValue,
		))
	 */
	protected function _updateDb($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$jsonConsumptionTax = json_encode($arr['varsValue']);
		$flag = $this->checkTextSize(array(
			'flagReturn' => 1,
			'str'        => $jsonConsumptionTax,
		));
		if ($flag) {
			return 'errorDataMax';
		}

		$classDb->updateRow(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingFSValue' . $strNation,
			'arrColumn' => array('jsonConsumptionTax'),
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
					'value'         => $arr['varsItem']['numFiscalPeriod'],
				),
			),
			'arrValue'  => array($jsonConsumptionTax),
		));

	}
}
