<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcTempNext_SubAccountTitle_2012_Public extends Code_Else_Plugin_Accounting_Jpn_CalcTempNext_SubAccountTitle
{

	/**
		(array(
			'flag' => '',
			'vars' => $vars,
		))
	 */
	protected function _getVarsItemCheck($arr)
	{
		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$arrayFSList = $this->_getFSList(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$varsJgaapFS = $this->_getVarsItemJgaapFS(array(
			'arrayFSList'  => $arrayFSList,
			'varsFS'       => $varsFS,
		));
/*
		$varsJgaapFSCS = $this->_getVarsItemJgaapFSCS(array(
			'varsFS' => $varsFS,
		));
*/
		$data = array(
			'varsJgaapFS'   => $varsJgaapFS,
			//'varsJgaapFSCS' => $varsJgaapFSCS,
		);

		return $data;
	}

	/**
		(array(
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'varsAccountTitle' => $arr['varsAccountTitle'],
			'arrValue'         => $arr['arrValue'],
		))
	 */
	protected function _checkVarsBackData($arr)
	{
		$varsItemCheck = $this->_getVarsItemCheck(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$idAccountTitleJgaapFS = $arr['varsAccountTitle']['idAccountTitleJgaapFS'];
		if (!$varsItemCheck['varsJgaapFS']['arrStrTitle'][$idAccountTitleJgaapFS]) {
			$classCalcTempNextAccountTitleFS = $this->_getClassCalc(array(
				'flagType'   => 'TempNext',
				'flagDetail' => 'AccountTitleFS',
			));
			$flag = $classCalcTempNextAccountTitleFS->allot(array(
				'flagStatus'      => 'back',
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
				'flagFS'          => $arr['varsAccountTitle']['flagFS'],
				'idTarget'        => $idAccountTitleJgaapFS,
			));
			if ($flag) {
				return $flag;
			}
		}
/*
		$arrayStrDirect = array('varsDirect', 'varsInDirect');
		$arrayStrSide = array('idAccountTitlePlus', 'idAccountTitleMinus');
		$arrayIdAccountTitleFSCS = array();
		foreach ($arrayStrDirect as $keyStrDirect => $valueStrDirect) {
			foreach ($arrayStrSide as $keyStrSide => $valueStrSide) {
				$idTarget = $arr['varsAccountTitle']['varsJgaapCS'][$valueStrDirect][$valueStrSide];
				if ($idTarget) {
					if (!$varsItemCheck['varsJgaapFSCS']['arrStrTitle'][$valueStrDirect][$idTarget]) {
						$arrayIdAccountTitleFSCS[$valueStrDirect][$idTarget] = $arr['varsAccountTitle'];
					}
				}
			}
		}

		$classCalcTempNextAccountTitleFSCS = $this->_getClassCalc(array(
			'flagType'   => 'TempNext',
			'flagDetail' => 'AccountTitleFSCS',
		));
		foreach ($arrayStrDirect as $keyStrDirect => $valueStrDirect) {
			$array = $arrayIdAccountTitleFSCS[$valueStrDirect];
			if (!$array) {
				continue;
			}
			foreach ($array as $key => $value) {
				$flag = $classCalcTempNextAccountTitleFSCS->allot(array(
					'flagStatus'      => 'back',
					'numFiscalPeriod' => $arr['numFiscalPeriod'],
					'flagFS'          => 'CS',
					'idTarget'        => $key,
					'flagDirect'      => ($valueStrDirect == 'varsDirect')? 1 : 0,
				));
				if ($flag) {
					return $flag;
				}
			}
		}
*/
		$classCalcTempNextAccountTitle = $this->_getClassCalc(array(
			'flagType'   => 'TempNext',
			'flagDetail' => 'AccountTitle',
		));
		$flag = $classCalcTempNextAccountTitle->allot(array(
			'flagStatus'      => 'back',
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'flagFS'          => $arr['varsAccountTitle']['flagFS'],
			'idTarget'        => $arr['arrValue']['idAccountTitle'],
		));
		//'errorDataMax'
		if ($flag) {
			return $flag;
		}

	}
}
