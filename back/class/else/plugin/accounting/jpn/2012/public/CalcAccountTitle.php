<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcAccountTitle_2012_Public extends Code_Else_Plugin_Accounting_Jpn_CalcAccountTitle
{
	/**
		(array(
			'flagDepartment' => 1,
			'varsFSValue'    => $varsFSValue,
			'varsItem'       => $arr['varsItem'],
			'varsItemNext'   => $arr['varsItemNext'],
		))
	 */
	protected function _loopVarsValueNext($arr)
	{
		$varsFSValueNext = array();
		$array = $arr['varsFSValue'];
		foreach ($array as $key => $value) {
			if ($key == 'id') {
				continue;

			} elseif ($key == 'stampRegister') {
				$varsFSValueNext[$key] = $value;

			} elseif ($key == 'stampUpdate') {
				$varsFSValueNext[$key] = TIMESTAMP;

			} elseif ($key == 'numFiscalPeriod') {
				$varsFSValueNext[$key] = $arr['varsItemNext']['numFiscalPeriod'];

			} elseif ($key == 'idEntity') {
				$varsFSValueNext[$key] = $value;

			} elseif ($key == 'idDepartment') {
				$varsFSValueNext[$key] = $value;

			} else {
				$varsFSValueNext[$key] = '';
			}
		}

		$array = $arr['varsItemNext']['varsFiscalPeriod'];
		foreach ($array as $key => $value) {
			$data = $this->_loopVarsNext(array(
				'varsFS'        => $arr['varsItemNext']['varsFS']['jsonJgaapAccountTitleBS'],
				'varsValue'     => &$arr['varsFSValue']['jsonJgaapAccountTitleBS']['f1'],
				'varsValueNext' => array(),
				'idParent'      => '',
			));

			if ($arr['flagDepartment']) {
				$sumNext = $arr['varsFSValue']['jsonJgaapAccountTitleBS']['f1']['departmentNet']['sumNext'];
				if (is_null($sumNext)) {
					$sumNext = 0;
				}
				$dataTmpl = array(
					'sumPrev'   => $sumNext,
					'sumDebit'  => 0,
					'sumCredit' => 0,
					'sumNext'   => $sumNext,
					'varsAdjust' => array(
						'sumPrev'   => 0,
						'sumDebit'  => 0,
						'sumCredit' => 0,
						'sumNext'   => 0,
					),
					'varsAdjust2' => array(
						'sumPrev'   => 0,
						'sumDebit'  => 0,
						'sumCredit' => 0,
						'sumNext'   => 0,
					),
				);
				$data['varsValueNext']['departmentNet'] = $dataTmpl;
			}

			if(!isset($varsFSValueNext['jsonJgaapAccountTitleBS'][$key])){
			    if(!is_array($varsFSValueNext['jsonJgaapAccountTitleBS'])){
			        $varsFSValueNext['jsonJgaapAccountTitleBS'] = array();
			        $varsFSValueNext['jsonJgaapAccountTitleBS'][$key] = array();
			    }
			}

			$varsFSValueNext['jsonJgaapAccountTitleBS'][$key] = $data['varsValueNext'];

			$data = $this->_loopVarsNext(array(
				'varsFS'        => $arr['varsItemNext']['varsFS']['jsonJgaapFSBS'],
				'varsValue'     => &$arr['varsFSValue']['jsonJgaapFSBS']['f1'],
				'varsValueNext' => array(),
				'idParent'      => '',
			));

			if(!isset($varsFSValueNext['jsonJgaapFSBS'][$key])){
			    if(!is_array($varsFSValueNext['jsonJgaapFSBS'])){
			        $varsFSValueNext['jsonJgaapFSBS'] = array();
			        $varsFSValueNext['jsonJgaapFSBS'][$key] = array();
			    }
			}

			$varsFSValueNext['jsonJgaapFSBS'][$key] = $data['varsValueNext'];


		}

		$array = $varsFSValueNext;
		foreach ($array as $key => $value) {
			if (preg_match("/^json/", $key)) {
				$varsFSValueNext[$key] = '';
				if (preg_match("/(.*?)BS$/", $key) && $value) {
					$varsFSValueNext[$key] =  json_encode($value);
					$flag = $this->checkTextSize(array(
						'flagReturn' => 1,
						'str'        => $varsFSValueNext[$key],
					));
					if ($flag) {
						return 'errorDataMax';
					}
				}
			}
		}

		return $varsFSValueNext;

	}

	/**
		(array(
			'varsFS'        => $arr['varsItemNext']['varsFS']['jsonJgaapAccountTitleBS'],
			'varsValue'     => &$arr['varsFSValue']['jsonJgaapAccountTitleBS'][$key],
			'varsValueNext' => array(),
		));
	 */
	protected function _loopVarsNext($arr)
	{
		$varsValue = &$arr['varsValue'];
		$varsValueNext = &$arr['varsValueNext'];
		$array = &$arr['varsFS'];

		if (is_null($arr['varsValueNext']['profitBroughtForward'])) {
			$arr['varsValueNext']['profitBroughtForward'] = array(
				'sumPrev'   => 0,
				'sumDebit'  => 0,
				'sumCredit' => 0,
				'sumNext'   => 0,
				'varsAdjust' => array(
					'sumPrev'   => 0,
					'sumDebit'  => 0,
					'sumCredit' => 0,
					'sumNext'   => 0,
				),
				'varsAdjust2' => array(
					'sumPrev'   => 0,
					'sumDebit'  => 0,
					'sumCredit' => 0,
					'sumNext'   => 0,
				),
			);
		}

		foreach ($array as $key => $value) {
			if (!is_null($value['vars']['varsValue'])) {
				if (!is_null($varsValue[$value['vars']['idTarget']])) {

					$dataTmpl = array(
						'sumPrev'   => $varsValue[$value['vars']['idTarget']]['sumNext'],
						'sumDebit'  => 0,
						'sumCredit' => 0,
						'sumNext'   => $varsValue[$value['vars']['idTarget']]['sumNext'],
						'varsAdjust' => array(
							'sumPrev'   => 0,
							'sumDebit'  => 0,
							'sumCredit' => 0,
							'sumNext'   => 0,
						),
						'varsAdjust2' => array(
							'sumPrev'   => 0,
							'sumDebit'  => 0,
							'sumCredit' => 0,
							'sumNext'   => 0,
						),
					);

					if ($value['vars']['idTarget'] == 'netIncome'
						|| $arr['idParent'] == 'accountsReceivablesWrap'
						|| $arr['idParent'] == 'accountsPayablesWrap'
					) {
						$sumPrev = $arr['varsValueNext']['profitBroughtForward']['sumPrev'] + $dataTmpl['sumPrev'];
						if ($value['vars']['flagDebit']) {
							$sumPrev = $arr['varsValueNext']['profitBroughtForward']['sumPrev'] - $dataTmpl['sumPrev'];
						}
						$arr['varsValueNext']['profitBroughtForward']['sumPrev'] = $sumPrev;
						$arr['varsValueNext']['profitBroughtForward']['sumNext'] = $sumPrev;

						//reset
						$dataTmpl['sumPrev'] = 0;
						$dataTmpl['sumNext'] = 0;

						$arr['varsValueNext'][$value['vars']['idTarget']] = $dataTmpl;

					} elseif ($value['vars']['idTarget'] == 'profitBroughtForward') {
						$arr['varsValueNext']['profitBroughtForward']['sumPrev'] += $dataTmpl['sumPrev'];
						$arr['varsValueNext']['profitBroughtForward']['sumNext'] += $dataTmpl['sumPrev'];

					} else {
						$arr['varsValueNext'][$value['vars']['idTarget']] = $dataTmpl;
					}
				}
			}
			if ($value['child']) {
				$data = $this->_loopVarsNext(array(
					'varsFS'        => $array[$key]['child'],
					'varsValue'     => $arr['varsValue'],
					'varsValueNext' => $arr['varsValueNext'],
					'idParent'      => $value['vars']['idTarget'],
				));
				$array[$key]['child'] = $data['varsFS'];
				$varsValueNext =  $data['varsValueNext'];
				$varsTemp = $data['varsTemp'];
			}
		}

		return $arr;
	}



}
