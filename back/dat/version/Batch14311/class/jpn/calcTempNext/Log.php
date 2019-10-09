<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcTempNext_LogBatch14311 extends Code_Else_Plugin_Accounting_Jpn_JpnBatch14311
{
	protected $_extChildSelf = array(

	);

	/**
	 * tempNext only
	 */
	public function run()
	{
		if (FLAG_TEST) {
			var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
		}
		exit;
	}

	/*

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
			'flagStatus'      => 'add',
			'numFiscalPeriod' => $numFiscalPeriod,
			'arrRows'         => $arrRows,
		))
	 */
	protected function _iniAdd($arr)
	{
		$varsItem = $this->_getVarsItem(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$arrRows = $this->_updateArrRows(array(
			'varsItem'        => $varsItem,
			'arrRows'         => $arr['arrRows'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$flag = $this->_checkArrRows(array(
			'varsItem'        => $varsItem,
			'arrRows'         => $arrRows,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		if ($flag) {
			return $flag;
		}

		$classCalcAccountTitle = $this->_getClassCalc(array('flagType' => 'AccountTitle'));
		$classCalcSubAccountTitle = $this->_getClassCalc(array('flagType' => 'SubAccountTitle'));
		$classCalcLogCalc = $this->_getClassCalc(array('flagType' => 'LogCalc'));

		$flag = $classCalcAccountTitle->allot(array(
			'flagStatus'      => 'add',
			'arrRows'         => $arrRows,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'flagTempNext'    => 1,
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}

		$flag = $classCalcSubAccountTitle->allot(array(
			'flagStatus'      => 'add',
			'arrRows'         => $arrRows,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'flagTempNext'    => 1,
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}

		$classCalcLogCalc->allot(array(
			'flagStatus'      => 'update',
			'arrRows'         => $arrRows,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
	}

	/**
		(array(
			'flag' => '',
			'vars' => $vars,
		))
	 */
	protected function _getVarsItem($arr)
	{
		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$arrayFSList = $this->_getFSList(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$varsFSItem = $this->_getVarsFSItem();

		$arrDepartment = $this->_getVarsDepartment(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$arrSubAccountTitle = $this->_getVarsSubAccountTitle(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$arrAccountTitle = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'numFiscalPeriod'    => $arr['numFiscalPeriod'],
		));

		$varsPeriod = $this->_getVarsFiscalPeriod(array(
			'flagFiscalPeriod' => 'f1',
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
		));

		$varsJgaapFS = $this->_getVarsItemJgaapFS(array(
			'arrayFSList'  => $arrayFSList,
			'varsFS'       => $varsFS,
		));

		$varsJgaapFSCS = $this->_getVarsItemJgaapFSCS(array(
			'varsFS' => $varsFS,
		));

		$data = array(
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'arrAccountTitle'    => $arrAccountTitle,
			'arrDepartment'      => $arrDepartment,
			'varsJgaapFS'        => $varsJgaapFS,
			'varsJgaapFSCS'      => $varsJgaapFSCS,
			'varsPeriod'         => $varsPeriod,
		);

		return $data;
	}

	/**
		(array(
			'varsFS'       => $varsFS,
		))
	 */
	protected function _getVarsItemJgaapFSCS($arr)
	{
		$arrStrTitle = array();
		$arrayStrDirect = array('varsDirect', 'varsInDirect');
		foreach ($arrayStrDirect as $keyStrDirect => $valueStrDirect) {
			$data = $this->_getVarsItemJgaapFSLoop(array(
				'arrStrTitle'   => array(),
				'vars'          => $arr['varsFS']['jsonJgaapFSCS'][$valueStrDirect],
			));
			$arrStrTitle[$valueStrDirect] = $data['arrStrTitle'];
			$arrStrTitle[$valueStrDirect]['cash'] = 1;
			$arrStrTitle[$valueStrDirect]['none'] = 1;
		}

		$data = array(
			'arrStrTitle' => $arrStrTitle,
		);

		return $data;
	}

	/**
		(array(
			'arrayFSList'  => $arrayFSList,
			'varsFS'       => $varsFS,
		))
	 */
	protected function _getVarsItemJgaapFS($arr)
	{
		$arrStrTitle = array();
		$array = $arr['arrayFSList'];
		foreach ($array as $key => $value) {
			$data = $this->_getVarsItemJgaapFSLoop(array(
				'arrStrTitle'  => array(),
				'vars'         => $arr['varsFS']['jsonJgaapFS'. $key],
			));
			$arrStrTitle = array_merge($arrStrTitle, $data['arrStrTitle']);
		}

		$data = array(
			'arrStrTitle' => $arrStrTitle,
		);

		return $data;
	}

	/**
		(array(
			'arrStrTitle'  => array(),
			'vars'         => array(),
		))
	 */
	protected function _getVarsItemJgaapFSLoop($arr)
	{
		$arrStrTitle = &$arr['arrStrTitle'];

		$array = &$arr['vars'];
		foreach ($array as $key => $value) {
			$arr['arrStrTitle'][$value['vars']['idTarget']] = $value['strTitle'];

			if ($value['child']) {
				$data = $this->_getVarsItemJgaapFSLoop(array(
					'vars'          => $array[$key]['child'],
					'arrStrTitle'   => $arr['arrStrTitle'],
				));
				$array[$key]['child'] = $data['vars'];
				$arrStrTitle =  $data['arrStrTitle'];
			}
		}

		return $arr;
	}

	/**
	 (array(
		'varsItem' => $varsItem,
		'arrRows'  => $arr['arrRows'],
	 ))
	 */
	protected function _checkArrRows($arr)
	{
		$arrayIdDepartment = array();
		$arrayIdAccountTitle = array();
		$arrayIdSubAccountTitle = array();
		$arrayIdAccountTitleFS = array();
		$arrayIdAccountTitleFSCS = array(
			'varsDirect' => array(),
			'varsInDirect' => array(),
		);

		$arrayStrDirect = array('varsDirect', 'varsInDirect');
		$arrayStrSide = array('idAccountTitlePlus', 'idAccountTitleMinus');

		$array = $arr['arrRows'];
		foreach ($array as $key => $value) {
			if ($value['idDepartment']) {
				if (!$arr['varsItem']['arrDepartment']['arrStrTitle'][$value['idDepartment']]) {
					$arrayIdDepartment[$value['idDepartment']] = $value;
				}
			}

			if ($value['idAccountTitleJgaapFS']) {
				if (!$arr['varsItem']['varsJgaapFS']['arrStrTitle'][$value['idAccountTitleJgaapFS']]) {
					$arrayIdAccountTitleFS[$value['idAccountTitleJgaapFS']] = $value;
				}
			}

			foreach ($arrayStrDirect as $keyStrDirect => $valueStrDirect) {
				foreach ($arrayStrSide as $keyStrSide => $valueStrSide) {
					$idTarget = $value['varsJgaapCS'][$valueStrDirect][$valueStrSide];
					if ($idTarget) {
						if (!$arr['varsItem']['varsJgaapFSCS']['arrStrTitle'][$valueStrDirect][$idTarget]) {
							$arrayIdAccountTitleFSCS[$valueStrDirect][$idTarget] = $value;
						}
					}

				}
			}

			if ($value['idAccountTitle']) {
				if (!$arr['varsItem']['arrAccountTitle']['arrStrTitle'][$value['idAccountTitle']]) {
					$arrayIdAccountTitle[$value['idAccountTitle']] = $value;
				}
			}

			if ($value['idSubAccountTitle']) {
				if (!$arr['varsItem']['arrSubAccountTitle']['arrStrTitle'][$value['idAccountTitle']][$value['idSubAccountTitle']]) {
					$arrayIdSubAccountTitle[$value['idSubAccountTitle']] = $value;
				}
			}
		}

		$classCalcTempNextEntityDepartment = $this->_getClassCalc(array(
			'flagType'   => 'TempNext',
			'flagDetail' => 'EntityDepartment',
		));
		$array = $arrayIdDepartment;
		foreach ($array as $key => $value) {
			$flag = $classCalcTempNextEntityDepartment->allot(array(
				'flagStatus'      => 'back',
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
				'idTarget'        => $key,
			));
			if ($flag) {
				return $flag;
			}
		}

		$classCalcTempNextAccountTitleFS = $this->_getClassCalc(array(
			'flagType'   => 'TempNext',
			'flagDetail' => 'AccountTitleFS',
		));
		$array = $arrayIdAccountTitleFS;
		foreach ($array as $key => $value) {
			$flag = $classCalcTempNextAccountTitleFS->allot(array(
				'flagStatus'      => 'back',
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
				'flagFS'          => $value['flagFS'],
				'idTarget'        => $key,
			));
			if ($flag) {
				return $flag;
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

		$classCalcTempNextAccountTitle = $this->_getClassCalc(array(
			'flagType'   => 'TempNext',
			'flagDetail' => 'AccountTitle',
		));
		$array = $arrayIdAccountTitle;
		foreach ($array as $key => $value) {
			$flag = $classCalcTempNextAccountTitle->allot(array(
				'flagStatus'      => 'back',
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
				'flagFS'          => $value['flagFS'],
				'idTarget'        => $key,
			));
			//'errorDataMax'
			if ($flag) {
				return $flag;
			}
		}

		$classCalcTempNextSubAccountTitle = $this->_getClassCalc(array(
			'flagType'   => 'TempNext',
			'flagDetail' => 'SubAccountTitle',
		));
		$array = $arrayIdSubAccountTitle;
		foreach ($array as $key => $value) {
			$flag = $classCalcTempNextSubAccountTitle->allot(array(
				'flagStatus'      => 'back',
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
				'idTarget'        => $key,
			));
			if ($flag) {
				return $flag;
			}
		}
	}

	/**
	 (array(
		'varsItem' => $varsItem,
		'arrRows'  => $arr['arrRows'],
	 ))
	 */
	protected function _updateArrRows($arr)
	{
		global $classTime;

		//as beginning of a term
		$flagFiscalReport = '0';
		$stampBook = $arr['varsItem']['varsPeriod']['stampStart'];
		$arrDate = $classTime->getLocal(array('stamp' => $stampBook));

		//profitBroughtForward
		$idAccountTitle = 'profitBroughtForward';
		$flagDebitAccountTitle = $arr['varsItem']['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['flagDebit'];
		$idAccountTitleJgaapFS = $arr['varsItem']['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['idAccountTitleJgaapFS'];
		$flagFS = $arr['varsItem']['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['flagFS'];
		$varsJgaapCS = $arr['varsItem']['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['varsJgaapCS'];

		$array = &$arr['arrRows'];
		foreach ($array as $key => $value) {
			if ($value['flagFS'] != 'BS') {
				$array[$key]['flagDebitAccountTitle'] = $flagDebitAccountTitle;
				$array[$key]['idAccountTitle'] = $idAccountTitle;
				$array[$key]['idAccountTitleJgaapFS'] = $idAccountTitleJgaapFS;
				$array[$key]['varsJgaapCS'] = $varsJgaapCS;
				$array[$key]['idSubAccountTitle'] = '';

				/*through vars
					$array[$key]['flagConsumptionTaxGeneralRuleEach'] = '';
					$array[$key]['flagConsumptionTaxGeneralRuleProration'] = '';
					$array[$key]['flagConsumptionTaxSimpleRule'] = '';
					$array[$key]['flagConsumptionTaxWithoutCalc'] = '';
					$array[$key]['flagConsumptionTaxCalc'] = '';
				*/
			}

			$array[$key]['stampBook'] = $stampBook;
			$array[$key]['numFiscalPeriod'] = $arr['numFiscalPeriod'];
			$array[$key]['flagFiscalReport'] = $flagFiscalReport;
			$array[$key]['arrDate'] = $arrDate;

			/*through vars
				$array[$key]['idSubAccountTitleContra'] = '';
				$array[$key]['idDepartmentContra'] = '';
				$array[$key]['idAccountTitleContra'] = '';
				$array[$key]['arrColumn'] = array();
				$array[$key]['arrValue'] = array();
			*/
		}

		return $array;
	}

	/**
		(array(
			'flagStatus'      => 'edit',
			'numFiscalPeriod' => $numFiscalPeriod,
			'arrRowsAdd'      => $arrRowsAdd,
			'arrRowsDelete'   => $arrRowsDelete,
			'flagBalance'     => ($varsFlag['idDepartment'] == 0)? 'all' : 'department',
			'flagBalanceSub'  => 1,
		))
	 */
	protected function _iniEdit($arr)
	{
		$varsItem = $this->_getVarsItem(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$arrRowsAdd = $this->_updateArrRows(array(
			'varsItem'        => $varsItem,
			'arrRows'         => $arr['arrRowsAdd'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$arrRowsDelete = $this->_updateArrRows(array(
			'varsItem'        => $varsItem,
			'arrRows'         => $arr['arrRowsDelete'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$arrRows = $arrRowsAdd;
		$array = $arrRowsDelete;
		foreach ($array as $key => $value) {
			$arrRows[] = $value;
		}

		$flag = $this->_checkArrRows(array(
			'varsItem'        => $varsItem,
			'arrRows'         => $arrRows,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		if ($flag) {
			return $flag;
		}

		$classCalcAccountTitle = $this->_getClassCalc(array('flagType' => 'AccountTitle'));
		$classCalcSubAccountTitle = $this->_getClassCalc(array('flagType' => 'SubAccountTitle'));
		$classCalcLogCalc = $this->_getClassCalc(array('flagType' => 'LogCalc'));

		if ($arr['flagBalanceSub']) {
			$flag = $classCalcSubAccountTitle->allot(array(
				'flagStatus'      => 'edit',
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
				'arrRowsAdd'      => $arrRowsAdd,
				'arrRowsDelete'   => $arrRowsDelete,
				'flagTempNext'    => 1,
				'flagBalance'     => ($arr['flagBalance'])? $arr['flagBalance'] : '',
			));
			if ($flag == 'errorDataMax') {
				return $flag;
			}

		} else {
			$flag = $classCalcAccountTitle->allot(array(
				'flagStatus'      => 'edit',
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
				'arrRowsAdd'      => $arrRowsAdd,
				'arrRowsDelete'   => $arrRowsDelete,
				'flagTempNext'    => 1,
				'flagBalance'     => ($arr['flagBalance'])? $arr['flagBalance'] : '',
			));
			if ($flag == 'errorDataMax') {
				return $flag;
			}
			$flag = $classCalcSubAccountTitle->allot(array(
				'flagStatus'      => 'edit',
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
				'arrRowsAdd'      => $arrRowsAdd,
				'arrRowsDelete'   => $arrRowsDelete,
				'flagTempNext'    => 1,
				'flagBalance'     => ($arr['flagBalance'])? $arr['flagBalance'] : '',
			));
			if ($flag == 'errorDataMax') {
				return $flag;
			}
		}

		$classCalcLogCalc->allot(array(
			'flagStatus'      => 'update',
			'arrRows'         => $arrRows,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
	}

	/**
		(array(
			'flagStatus'      => 'delete',
			'numFiscalPeriod' => $numFiscalPeriod,
			'arrRows'         => $arrRows,
		))
	 */
	protected function _iniDelete($arr)
	{
		$varsItem = $this->_getVarsItem(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$arrRows = $this->_updateArrRows(array(
			'varsItem'        => $varsItem,
			'arrRows'         => $arr['arrRows'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$flag = $this->_checkArrRows(array(
			'varsItem'        => $varsItem,
			'arrRows'         => $arrRows,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		if ($flag) {
			return $flag;
		}

		$classCalcAccountTitle = $this->_getClassCalc(array('flagType' => 'AccountTitle'));
		$classCalcSubAccountTitle = $this->_getClassCalc(array('flagType' => 'SubAccountTitle'));
		$classCalcLogCalc = $this->_getClassCalc(array('flagType' => 'LogCalc'));

		$flag = $classCalcAccountTitle->allot(array(
			'flagStatus'      => 'delete',
			'arrRows'         => $arrRows,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'flagTempNext'    => 1,
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}

		$flag = $classCalcSubAccountTitle->allot(array(
			'flagStatus'      => 'delete',
			'arrRows'         => $arrRows,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'flagTempNext'    => 1,
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}
		$classCalcLogCalc->allot(array(
			'flagStatus'      => 'update',
			'arrRows'         => $arrRows,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
	}
}
