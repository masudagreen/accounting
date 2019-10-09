<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcAccountTitleFSBatch14311 extends Code_Else_Plugin_Accounting_Jpn_CalcAccountTitleBatch14311
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
		(array(
			'arrStrTitle'  => array(),
			'vars'         => array(),
		))
	 */
	protected function _getVarsJgaapFS($arr)
	{
		$arrStrTitle = &$arr['arrStrTitle'];

		$array = &$arr['vars'];
		foreach ($array as $key => $value) {

			if (!is_null($value['vars']['flagUse'])) {
				$arr['arrStrTitle'][$value['vars']['idTarget']]['flagDebit'] = (int) $value['vars']['flagDebit'];
			}

			if ($value['child']) {
				$data = $this->_getVarsJgaapFS(array(
					'vars'        => $array[$key]['child'],
					'arrStrTitle' => $arr['arrStrTitle'],
				));
				$array[$key]['child'] = $data['vars'];
				$arrStrTitle =  $data['arrStrTitle'];
			}
		}

		return $arr;
	}

	/**

	 */
	protected function _setCalc($arr)
	{
		$varsFSValue = $this->_loopVarsValue(array(
			'varsItem'    => $arr['varsItem'],
			'varsFSValue' => $this->_getVarsFSValue(array(
				'numFiscalPeriod' => $arr['varsItem']['numFiscalPeriod'],
			)),
		));

		$flag = $this->_updateDb(array(
			'varsValue' => $varsFSValue,
			'varsItem'  => $arr['varsItem'],
			'strColumn' => 'jsonJgaapFS',
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}

		$array = $arr['varsItem']['varsDepartment'];
		foreach ($array as $key => $value) {
			$varsFSValue = $this->_getVarsFSValueDepartment(array(
				'idDepartment'    => $key,
				'numFiscalPeriod' => $arr['varsItem']['numFiscalPeriod'],
			));
			$varsFSValue = $this->_loopVarsValue(array(
				'flagDepartment' => 1,
				'varsFSValue'    => $varsFSValue,
				'varsItem'       => $arr['varsItem'],
			));
			$flag = $this->_updateDb(array(
				'idDepartment' => $key,
				'varsValue'    => $varsFSValue,
				'varsItem'     => $arr['varsItem'],
				'strColumn'    => 'jsonJgaapFS',
			));
			if ($flag == 'errorDataMax') {
				return $flag;
			}
		}

	}

	/**
		(array(
				'flagDepartment' => 1,
				'varsFSValue'    => $varsFSValue,
				'varsItem'       => $arr['varsItem'],
		))
	 */
	protected function _loopVarsValue($arr)
	{
		$varsJgaapFS = array();
		$array = $this->_extendSelf['arrFS'];
		foreach ($array as $key => $value) {
			if ($value == 'CR') {
				if (!(int) $arr['varsItem']['varsEntityNation']['flagCR']) {
					continue;
				}
			}
			$varsJgaapFS[$value] = $this->_getVarsJgaapFS(array(
				'arrStrTitle' => array(),
				'vars'        => $arr['varsItem']['varsFS']['jsonJgaapFS' . $value],
			));

			$arr['varsFSValue']['jsonJgaapFS' . $value] = $this->_getValueFS(array(
				'flagFS'                => $value,
				'varsJgaapFS'           => $varsJgaapFS[$value]['arrStrTitle'],
				'varsAccountTitle'      => $arr['varsItem']['varsAccountTitle'][$value],
				'varsItem'              => $arr['varsItem'],
				'varsValueAccountTitle' => $arr['varsFSValue']['jsonJgaapAccountTitle' . $value],
			));
		}

		$strFS = 'jsonJgaapFS';
		$varsFSBS = $arr['varsItem']['varsFS'][$strFS . 'BS'];
		if ($arr['flagDepartment']) {
			$arrayNew = array();
			$array = $varsFSBS;
			foreach ($array as $key => $value) {
				$arrayNew[] = $value;
				if ($value['vars']['idTarget'] == 'netAssetsSum') {
					$arrayNew[] = $arr['varsItem']['varsDepartmentTreeItem'];
				}
			}
			$varsFSBS = $arrayNew;
		}
		$array = $arr['varsItem']['varsFiscalPeriod'];
		foreach ($array as $key => $value) {

			if ((int) $arr['varsItem']['varsEntityNation']['flagCR']) {
				//CR Loop
				$this->_loopVarsCalc(array(
					'varsFS'    => $arr['varsItem']['varsFS'][$strFS . 'CR'],
					'varsValue' => &$arr['varsFSValue'][$strFS . 'CR'][$key],
				));

				//CR->PL
				$arr['varsFSValue'][$strFS . 'PL'][$key]['currentTermProductsCost']['sumNext']
					 = $arr['varsFSValue'][$strFS . 'CR'][$key]['currentWorkInProcessNet']['sumNext'];
			}

			//PL Loop
			$this->_loopVarsCalc(array(
				'varsFS'    => $arr['varsItem']['varsFS'][$strFS . 'PL'],
				'varsValue' => &$arr['varsFSValue'][$strFS . 'PL'][$key],
			));

			//PL->BS
			$arr['varsFSValue'][$strFS . 'BS'][$key]['unappropriatedRetainedEarnings']['sumNext']
				  = $arr['varsFSValue']['jsonJgaapAccountTitleBS'][$key]['unappropriatedRetainedEarningsSum']['sumNext'];

			//BS Loop
			$this->_loopVarsCalc(array(
				'varsFS'    => $varsFSBS,
				'varsValue' => &$arr['varsFSValue'][$strFS . 'BS'][$key],
			));

			$numAssetsSum = $arr['varsFSValue'][$strFS . 'BS'][$key]['assetsSum']['sumNext'];
			$arr['varsFSValue'][$strFS . 'BS'][$key]['liabilitiesNetAssetsNet']['sumNext'] = $numAssetsSum;

			$numNetAssetsSum = $arr['varsFSValue'][$strFS . 'BS'][$key]['netAssetsSum']['sumNext'];
			$numliabilitiesSum = $arr['varsFSValue'][$strFS . 'BS'][$key]['liabilitiesSum']['sumNext'];

			if ($arr['flagDepartment']) {
				$arr['varsFSValue'][$strFS . 'BS'][$key]['departmentNet']['sumNext']
					 = $numAssetsSum - $numNetAssetsSum - $numliabilitiesSum;
			}

		}

		return $arr['varsFSValue'];

	}

	/**
		(array(
			'flagFS'                => $value,
			'varsJgaapFS'           => $varsJgaapFS[$value],
			'varsAccountTitle'      => $arr['varsItem']['varsAccountTitle'][$value],
			'varsItem'              => $arr['varsItem'],
			'varsValueAccountTitle' => $arr['varsFSValue']['jsonJgaapAccountTitle' . $value],
	))
	 */
	protected function _getValueFS($arr)
	{
		$varsValue = array();

		$array = $arr['varsItem']['varsFiscalPeriod'];
		$arrayAccountTitle = $arr['varsAccountTitle'];
		foreach ($arrayAccountTitle as $keyAccountTitle => $valueAccountTitle) {

			$idAccountTitle = $keyAccountTitle;
			$idAccountTitleJgaapFS = $valueAccountTitle['idAccountTitleJgaapFS'];

			foreach ($array as $key => $value) {
				if (is_null($varsValue[$key])) {
					$varsValue[$key] = array();
				}
				if (is_null($arr['varsValueAccountTitle'][$key][$idAccountTitle])) {
					continue;
				}

				if (is_null($varsValue[$key][$idAccountTitleJgaapFS])) {
					$varsValue[$key][$idAccountTitleJgaapFS]['sumNext'] = 0;
				}
				$sumNext =  $arr['varsValueAccountTitle'][$key][$idAccountTitle]['sumNext'];
				if ((int) $arr['varsJgaapFS'][$idAccountTitleJgaapFS]['flagDebit']) {
					if (!(int) $valueAccountTitle['flagDebit']) {
						$sumNext *= (-1);
					}
				} else {
					if ((int) $valueAccountTitle['flagDebit']) {
						$sumNext *= (-1);
					}
				}
				$varsValue[$key][$idAccountTitleJgaapFS]['sumNext'] += $sumNext;
			}
		}


		return $varsValue;
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
						if ((int) $value['vars']['flagDebit']) {
							if ($valueSum['flagDebit']) {
								$numNext += $valueSum['numNext'];

							} else {
								$numNext -= $valueSum['numNext'];
							}
						} else {
							if ($valueSum['flagDebit']) {
								$numNext -= $valueSum['numNext'];

							} else {
								$numNext += $valueSum['numNext'];
							}
						}
					}
					$arraySum = array();
					$arr['varsValue'][$value['vars']['idTarget']]['sumNext'] = $numNext;

				} elseif ($value['vars']['flagCalc'] == 'net') {
					foreach ($arrayNet as $keyNet => $valueNet) {
						if ((int) $value['vars']['flagDebit']) {
							if ($valueNet['flagDebit']) {
								$numNext += $valueNet['numNext'];

							} else {
								$numNext -= $valueNet['numNext'];
							}
						} else {
							if ($valueNet['flagDebit']) {
								$numNext -= $valueNet['numNext'];

							} else {
								$numNext += $valueNet['numNext'];
							}
						}
					}
					$arr['varsValue'][$value['vars']['idTarget']]['sumNext'] = $numNext;

				} else {
					if (!is_null($arr['varsValue'][$value['vars']['idTarget']])) {
						$numNext =  $arr['varsValue'][$value['vars']['idTarget']]['sumNext'];
					}
				}
				$data = array(
					'flagDebit' => (int) $value['vars']['flagDebit'],
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
}
