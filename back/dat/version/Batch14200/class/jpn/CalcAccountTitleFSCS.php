<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcAccountTitleFSCSBatch14200 extends Code_Else_Plugin_Accounting_Jpn_CalcAccountTitleBatch14200
{
	protected $_extChildSelf = array(
		'arrFSCS'   => array('varsDirect', 'varsInDirect'),
		'arrMethod' => array('Plus', 'Minus'),
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
			'flagStatus' => 'calc',
		))
	 */
	protected function _iniCalc($arr)
	{
		return $this->_setCalc(array(
			'varsItem' => $arr['varsItem'],
		));
	}

	/**

	 */
	protected function _setCalc($arr)
	{
		$varsFSValue = $this->_loopVarsValue(array(
			'varsFSValue'  => $this->_getVarsFSValue(array(
				'numFiscalPeriod' => $arr['varsItem']['numFiscalPeriod'],
			)),
			'varsItem'     => $arr['varsItem'],
		));

		$flag = $this->_updateDb(array(
			'varsValue' => $varsFSValue,
			'varsItem'  => $arr['varsItem'],
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}

	}

	/**
		(array(
			'varsValue'    => $varsFSValue,
			'varsItem'     => $arr['varsItem'],
		))
	 */
	protected function _updateDb($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsPluginAccountingAccount;
		global $batch14200PLUGIN_ACCOUNTING_STR_NATION;

		$strNation = ucwords($batch14200PLUGIN_ACCOUNTING_STR_NATION);

		$arrColumn = array('jsonJgaapFSCS');

		$json = json_encode($arr['varsValue']['jsonJgaapFSCS']);
		$flag = $this->checkTextSize(array(
			'flagReturn' => 1,
			'str'        => $json,
		));
		if ($flag) {
			return 'errorDataMax';
		}
		$arrValue = array($json);
		$classDb->updateRow(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingFSValue' . $strNation,
			'arrColumn' => $arrColumn,
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
			'arrValue'  => $arrValue,
		));
	}




	/**
		(array(
			'varsFSValue'  => $this->_getVarsFSValue(),
			'varsItem'     => $arr['varsItem'],
		))
	 */
	protected function _loopVarsValue($arr)
	{
		$arrayCS = $this->_extChildSelf['arrFSCS'];
		foreach ($arrayCS as $keyCS => $valueCS) {
			$arr['varsFSValue']['jsonJgaapFSCS'][$valueCS] = array();
		}
		$varsJgaapFS = array();

		$array = $this->_extendSelf['arrFS'];
		$arrayCheck = array();
		foreach ($array as $key => $value) {
			if ($value == 'CR') {
				if (!(int) $arr['varsItem']['varsEntityNation']['flagCR']) {
					continue;
				}
			}
			foreach ($arrayCS as $keyCS => $valueCS) {
				$arr['varsFSValue']['jsonJgaapFSCS'][$valueCS] = $this->_getValueFS(array(
					'flagCS'                => $valueCS,
					'varsAccountTitle'      => $arr['varsItem']['varsAccountTitle'][$value],
					'varsItem'              => $arr['varsItem'],
					'varsValue'             => $arr['varsFSValue']['jsonJgaapFSCS'][$valueCS],
					'varsValueAccountTitle' => $arr['varsFSValue']['jsonJgaapAccountTitle' . $value],
				));
			}
		}

		$array = $arr['varsItem']['varsFiscalPeriod'];
		foreach ($array as $key => $value) {

			$arr['varsFSValue']['jsonJgaapFSCS']['varsInDirect'][$key]['currentTermProfitOrLossPre']['sumNext']
				 = $arr['varsFSValue']['jsonJgaapAccountTitlePL'][$key]['currentTermProfitOrLossPreNet']['sumNext'];

			foreach ($arrayCS as $keyCS => $valueCS) {
				$this->_loopVarsCalc(array(
					'varsFS'    => $arr['varsItem']['varsFS']['jsonJgaapFSCS'][$valueCS],
					'varsValue' => &$arr['varsFSValue']['jsonJgaapFSCS'][$valueCS][$key],
				));
			}

		}

		return $arr['varsFSValue'];

	}

	/**
		(array(
			'varsFS'    => $arr['varsItem']['varsFS'][$strFS . 'BS'],
			'varsValue' => &$arr['varsFSValue'][$strFS . 'BS'][$value],
		));
	 */
	protected function _loopVarsCalc($arr)
	{
		$array = &$arr['varsFS'];
		$arraySum = array();
		$arrayNet = array();
		$flag = 0;
		foreach ($array as $key => $value) {
			if ($value['child']) {
				$arraySum = $this->_loopVarsCalc(array(
					'varsFS'    => $array[$key]['child'],
					'varsValue' => &$arr['varsValue'],
				));
			}

			if (!is_null($value['vars']['varsValue'])) {
				$numNext = 0;
				if ($value['vars']['flagCalc'] == 'sum') {
					foreach ($arraySum as $keySum => $valueSum) {
						$numNext += $valueSum['numNext'];
					}
					$arraySum = array();
					$arr['varsValue'][$value['vars']['idTarget']]['sumNext'] = $numNext;

				} elseif ($value['vars']['flagCalc'] == 'net') {
					foreach ($arrayNet as $keyNet => $valueNet) {
						$numNext += $valueNet['numNext'];
					}
					$arr['varsValue'][$value['vars']['idTarget']]['sumNext'] = $numNext;

				} else {
					if (!is_null($arr['varsValue'][$value['vars']['idTarget']])) {
						$numNext =  $arr['varsValue'][$value['vars']['idTarget']]['sumNext'];
					}
				}
				$data = array(
					'numNext'   => $numNext,
				);

				if ($value['vars']['flagCalc'] == 'net') {
					$arrayNet = array();
				}
				$arrayNet[$value['vars']['idTarget']] = $data;
			}
		}

		return $arrayNet;
	}

	/**
		(array(
			'flagCS'                => $valueCS,
			'varsAccountTitle'      => $arr['varsItem']['varsAccountTitle'][$value],
			'varsItem'              => $arr['varsItem'],
			'varsValue'             => $arr['varsFSValue']['jsonJgaapFSCS'][$valueCS],
			'varsValueAccountTitle' => $arr['varsFSValue']['jsonJgaapAccountTitle' . $value],
		))
	 */
	protected function _getValueFS($arr)
	{
		$varsValue = $arr['varsValue'];
		$array = $arr['varsItem']['varsFiscalPeriod'];
		$arrayAccountTitle = $arr['varsAccountTitle'];
		$arrayMethod = $this->_extChildSelf['arrMethod'];

		foreach ($arrayAccountTitle as $keyAccountTitle => $valueAccountTitle) {

			if (is_null($valueAccountTitle['varsJgaapCS'])) {
				continue;
			}

			$idAccountTitle = $keyAccountTitle;
			$varsJgaapCS = $valueAccountTitle['varsJgaapCS'][$arr['flagCS']];

			foreach ($arrayMethod as $keyMethod => $valueMethod) {

				$idAccountTitleCS = $varsJgaapCS['idAccountTitle' . $valueMethod];
				$flagMethod = $varsJgaapCS['flagMethod' . $valueMethod];

				foreach ($array as $key => $value) {

					if (is_null($varsValue[$key])) {
						$varsValue[$key] = array();
					}

					if (is_null($arr['varsValueAccountTitle'][$key][$idAccountTitle])) {
						continue;
					}

					if ($idAccountTitleCS == 'none') {
						continue;
					}

					if ($idAccountTitleCS == 'cash') {
						if ($valueMethod == 'Plus') {
							if (is_null($varsValue[$key]['cashOpening'])) {
								$varsValue[$key]['cashOpening']['sumNext'] = 0;
							}
							if (is_null($varsValue[$key]['cashClosing'])) {
								$varsValue[$key]['cashClosing']['sumNext'] = 0;
							}
							$arrayCheck[$arr['flagFS']][$idAccountTitle][$arr['flagCS']] = 1;
							$varsValue[$key]['cashOpening']['sumNext'] +=  $arr['varsValueAccountTitle'][$key][$idAccountTitle]['sumPrev'];
							$varsValue[$key]['cashClosing']['sumNext'] +=  $arr['varsValueAccountTitle'][$key][$idAccountTitle]['sumNext'];
						}
						continue;
					}

					if (is_null($varsValue[$key][$idAccountTitleCS])) {
						$varsValue[$key][$idAccountTitleCS]['sumNext'] = 0;
					}

					$sumNext = 0;
					if ($flagMethod == 'net') {
						$sumNext =  $arr['varsValueAccountTitle'][$key][$idAccountTitle]['sumNext'];

					} elseif ($flagMethod == 'sumDebit') {
						$sumNext =  $arr['varsValueAccountTitle'][$key][$idAccountTitle]['sumDebit'];

					} elseif ($flagMethod == 'sumCredit') {
						$sumNext =  $arr['varsValueAccountTitle'][$key][$idAccountTitle]['sumCredit'];
					}

					if ($valueMethod == 'Minus') {
						$sumNext *= (-1);
					}

					$varsValue[$key][$idAccountTitleCS]['sumNext'] += $sumNext;

				}
			}
		}

		return $varsValue;
	}


}
