<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcAccountTitleFS_2012_Public extends Code_Else_Plugin_Accounting_Jpn_CalcAccountTitleFS
{
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
//unique start
				//CR->PL
				$arr['varsFSValue'][$strFS . 'PL'][$key]['goodsPurcheses']['sumNext']
					 += $arr['varsFSValue'][$strFS . 'CR'][$key]['currentWorkInProcessNet']['sumNext'];
//unique end
			}

			//PL Loop
			$this->_loopVarsCalc(array(
				'varsFS'    => $arr['varsItem']['varsFS'][$strFS . 'PL'],
				'varsValue' => &$arr['varsFSValue'][$strFS . 'PL'][$key],
			));

			//PL->BS
			$arr['varsFSValue'][$strFS . 'BS'][$key]['unappropriatedRetainedEarnings']['sumNext']
				  = $arr['varsFSValue']['jsonJgaapAccountTitleBS'][$key]['netIncome']['sumNext'];

			//BS Loop
			$this->_loopVarsCalc(array(
				'varsFS'    => $varsFSBS,
				'varsValue' => &$arr['varsFSValue'][$strFS . 'BS'][$key],
			));
//unique start

			$arrayStr = array('sumPrev', 'sumNext');
			foreach ($arrayStr as $keyStr => $valueStr) {
				$numAssetsSum = $arr['varsFSValue'][$strFS . 'BS'][$key]['assetsSum'][$valueStr];
				$numLiabilitiesNetAssetsSum = $arr['varsFSValue'][$strFS . 'BS'][$key]['liabilitiesNetAssetsSum'][$valueStr];
				$arr['varsFSValue'][$strFS . 'BS'][$key]['liabilitiesNetAssetsSum'][$valueStr] = $numAssetsSum;

				if ($arr['flagDepartment']) {
					$arr['varsFSValue'][$strFS . 'BS'][$key]['departmentNet'][$valueStr]
					= $numAssetsSum - $numLiabilitiesNetAssetsSum;
				}
			}

//unique end
		}

		return $arr['varsFSValue'];

	}
}
