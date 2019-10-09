<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcAccountTitle extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extChildSelf = array(
		'varsItemBalance' => array(),
		'varsBalance' => array(),
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
			'flagStatus'          => 'next',
			'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'numFiscalPeriodNext' => $arr['numFiscalPeriod'],
			'arrIdTarget'         => $arr['arrIdTarget'],
		))
	 */
	protected function _iniNextPart($arr)
	{
		$varsItem = $this->_getVarsItem(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$varsItemNext = $this->_getVarsItem(array(
			'numFiscalPeriod' => $arr['numFiscalPeriodNext'],
		));

		$flag = $this->_setNextVarsValuePart(array(
			'varsItem'     => $varsItem,
			'varsItemNext' => $varsItemNext,
			'arrIdTarget'  => $arr['arrIdTarget'],
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}

		$classCalcAccountTitleFS = $this->_getClassCalc(array('flagType' => 'AccountTitleFS'));
		$flag = $classCalcAccountTitleFS->allot(array(
			'flagStatus'   => 'calc',
			'varsItem'     => $varsItemNext,
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}

		$classCalcAccountTitleFSCS = $this->_getClassCalc(array('flagType' => 'AccountTitleFSCS'));
		$flag = $classCalcAccountTitleFSCS->allot(array(
			'flagStatus'   => 'calc',
			'varsItem'     => $varsItemNext,
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}

	}

	/**
		(array(
			'varsItem'     => $varsItem,
			'varsItemNext' => $varsItemNext,
			'arrIdTarget'  => $arr['arrIdTarget'],
		))
	 */
	protected function _setNextVarsValuePart($arr)
	{
		$varsFSValue = $this->_getVarsFSValue(array(
			'numFiscalPeriod' => $arr['varsItem']['numFiscalPeriod'],
		));

		$varsFSValueNext = $this->_loopVarsValueNext(array(
			'varsFSValue'  => $varsFSValue,
			'arrIdTarget'  => $arr['arrIdTarget'],
			'varsItem'     => $arr['varsItem'],
			'varsItemNext' => $arr['varsItemNext'],
		));
		if ($varsFSValueNext == 'errorDataMax') {
			return 'errorDataMax';
		}

		$this->_updateDb(array(
			'varsValue'     => $varsFSValueNext,
			'varsItemNext'  => $arr['varsItemNext'],
		));

		$array = $arr['varsItem']['varsDepartment'];
		foreach ($array as $key => $value) {
			$varsFSValue = $this->_getVarsFSValueDepartment(array(
				'idDepartment'     => $key,
				'numFiscalPeriod'  => $arr['varsItem']['numFiscalPeriod'],
			));

			$varsFSValueNext = $this->_loopVarsValueNext(array(
				'flagDepartment' => 1,
				'varsFSValue'    => $varsFSValue,
				'varsItem'       => $arr['varsItem'],
				'varsItemNext'   => $arr['varsItemNext'],
			));
			if ($varsFSValueNext == 'errorDataMax') {
				return 'errorDataMax';
			}

			$this->_insertDb(array(
				'idDepartment'  => $key,
				'varsValue'     => $varsFSValueNext,
				'varsItemNext'  => $arr['varsItemNext'],
			));
		}
	}

	/**
		(array(
			'flagStatus'          => 'next',
			'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'numFiscalPeriodNext' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _iniNext($arr)
	{
		$varsItem = $this->_getVarsItem(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$varsItemNext = $this->_getVarsItem(array(
			'numFiscalPeriod' => $arr['numFiscalPeriodNext'],
		));

		$flag = $this->_setNextVarsValue(array(
			'varsItem'     => $varsItem,
			'varsItemNext' => $varsItemNext,
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}

		$this->_iniCalc(array(
			'numFiscalPeriod'    => $arr['numFiscalPeriodNext'],
			'flagCalcDepartment' => 1,
		));

		$classCalcAccountTitleFS = $this->_getClassCalc(array('flagType' => 'AccountTitleFS'));
		$flag = $classCalcAccountTitleFS->allot(array(
			'flagStatus'   => 'calc',
			'varsItem'     => $varsItemNext,
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}

		$classCalcAccountTitleFSCS = $this->_getClassCalc(array('flagType' => 'AccountTitleFSCS'));
		$flag = $classCalcAccountTitleFSCS->allot(array(
			'flagStatus'   => 'calc',
			'varsItem'     => $varsItemNext,
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}

		$this->_updateDbPreferenceStamp(array('strColumn' => 'fsValue'));
		$this->_updateDbPreferenceStamp(array('strColumn' => 'entityDepartmentFSValue'));
	}

	/**
		(array(
			'flagStatus'      => 'calc',
			'numFiscalPeriod' => $numFiscalPeriod,
			'flagCalcDepartment'     => ($arr['flagCalcDepartment'])? 1 : 0,
		))
	 */
	protected function _iniCalc($arr)
	{
		$this->_iniAdd(array(
			'arrRows'         => array(),
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'flagTempNext'    => ($arr['flagTempNext'])? 1 : 0,
			'flagCalcDepartment' => ($arr['flagCalcDepartment'])? 1 : 0,//部門すべて再計算要フラグ
		));
	}

	/**
		(array(
			'flagStatus'   => 'add',
			'arrRows'      => $arrayNew,
			'flagTempNext' => 0,
			'flagBalance'  => ($arr['flagBalance'])? $arr['flagBalance'] : '',
		))
	 */
	protected function _iniAdd($arr)
	{
		$varsItem = $this->_getVarsItem(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$flag = $this->_setVarsValue(array(
			'arrRows'      => $arr['arrRows'],
			'varsItem'     => $varsItem,
			'flagDelete'   => ($arr['flagDelete'])? 1 : 0,
			'flagTempNext' => ($arr['flagTempNext'])? 1 : 0,
			'flagBalance'  => ($arr['flagBalance'])? $arr['flagBalance'] : '',
			'flagCalcDepartment'     => ($arr['flagCalcDepartment'])? 1 : 0,
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}

		$classCalcAccountTitleFS = $this->_getClassCalc(array('flagType' => 'AccountTitleFS'));
		$flag = $classCalcAccountTitleFS->allot(array(
			'flagStatus' => 'calc',
			'varsItem'   => $varsItem,
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}

		$classCalcAccountTitleFSCS = $this->_getClassCalc(array('flagType' => 'AccountTitleFSCS'));
		$flag = $classCalcAccountTitleFSCS->allot(array(
			'flagStatus' => 'calc',
			'varsItem'   => $varsItem,
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}

		$this->_updateDbPreferenceStamp(array('strColumn' => 'fsValue'));
		$this->_updateDbPreferenceStamp(array('strColumn' => 'entityDepartmentFSValue'));
	}

	/**
		(array(
			'varsItem'     => $varsItem,
			'varsItemNext' => $varsItemNext,
		))
	 */
	protected function _setNextVarsValue($arr)
	{
		$varsFSValue = $this->_getVarsFSValue(array(
			'numFiscalPeriod' => $arr['varsItem']['numFiscalPeriod'],
		));

		$varsFSValueNext = $this->_loopVarsValueNext(array(
			'varsFSValue'  => $varsFSValue,
			'varsItem'     => $arr['varsItem'],
			'varsItemNext' => $arr['varsItemNext'],
		));
		if ($varsFSValueNext == 'errorDataMax') {
			return 'errorDataMax';
		}

		$this->_insertDb(array(
			'varsValue'     => $varsFSValueNext,
			'varsItemNext'  => $arr['varsItemNext'],
		));

		$array = $arr['varsItem']['varsDepartment'];
		foreach ($array as $key => $value) {
			$varsFSValue = $this->_getVarsFSValueDepartment(array(
				'idDepartment'     => $key,
				'numFiscalPeriod'  => $arr['varsItem']['numFiscalPeriod'],
			));

			$varsFSValueNext = $this->_loopVarsValueNext(array(
				'flagDepartment' => 1,
				'varsFSValue'    => $varsFSValue,
				'varsItem'       => $arr['varsItem'],
				'varsItemNext'   => $arr['varsItemNext'],
			));
			if ($varsFSValueNext == 'errorDataMax') {
				return 'errorDataMax';
			}

			$this->_insertDb(array(
				'idDepartment'  => $key,
				'varsValue'     => $varsFSValueNext,
				'varsItemNext'  => $arr['varsItemNext'],
			));
		}
	}

	/**
		(array(
			'idDepartment'  => $key,
			'varsValue'     => $varsFSValueNext,
			'varsItemNext'  => $arr['varsItemNext'],
		))
	 */
	protected function _insertDb($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$arrDbColumn = array();
		$arrDbValue = array();

		$array = $arr['varsValue'];
		foreach ($array as $key => $value) {
			$arrDbColumn[] = $key;
			$arrDbValue[] = $value;
		}

		if ($arr['idDepartment']) {
			$classDb->insertRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingEntityDepartmentFSValue' . $strNation,
				'arrColumn' => $arrDbColumn,
				'arrValue'  => $arrDbValue,
			));

		} else {
			$classDb->insertRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingFSValue' . $strNation,
				'arrColumn' => $arrDbColumn,
				'arrValue'  => $arrDbValue,
			));
		}
	}

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

			$varsFSValueNext['jsonJgaapAccountTitleBS'][$key] = $data['varsValueNext'];

			$data = $this->_loopVarsNext(array(
				'varsFS'        => $arr['varsItemNext']['varsFS']['jsonJgaapFSBS'],
				'varsValue'     => &$arr['varsFSValue']['jsonJgaapFSBS']['f1'],
				'varsValueNext' => array(),
			));

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

					if ($value['vars']['idTarget'] == 'netIncome') {

						if (is_null($arr['varsValueNext']['profitBroughtForward'])) {
							$arr['varsValueNext']['profitBroughtForward'] = $dataTmpl;

						} else {
							$data = $arr['varsValueNext']['profitBroughtForward']['sumPrev'] + $dataTmpl['sumPrev'];
							$arr['varsValueNext']['profitBroughtForward']['sumPrev'] = $data;
							$arr['varsValueNext']['profitBroughtForward']['sumNext'] = $data;
						}

						//reset
						$dataTmpl['sumPrev'] = 0;
						$dataTmpl['sumNext'] = 0;
					}
					$arr['varsValueNext'][$value['vars']['idTarget']] = $dataTmpl;
				}
			}
			if ($value['child']) {
				$data = $this->_loopVarsNext(array(
					'varsFS'        => $array[$key]['child'],
					'varsValue'     => $arr['varsValue'],
					'varsValueNext' => $arr['varsValueNext'],
				));
				$array[$key]['child'] = $data['varsFS'];
				$varsValueNext =  $data['varsValueNext'];
			}
		}

		return $arr;
	}

	/**
		(array(
			'flagStatus'      => 'edit',
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'arrRowsAdd'      => $arrRowsAdd,
			'arrRowsDelete'   => $arrRowsDelete,
			'flagTempNext'    => 1,
			'flagBalance'  => ($arr['flagBalance'])? $arr['flagBalance'] : '',
		))
	 */
	protected function _iniEdit($arr)
	{
		$this->_iniDelete(array(
			'arrRows'         => $arr['arrRowsDelete'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'flagTempNext'    => ($arr['flagTempNext'])? 1 : 0,
			'flagBalance'     => ($arr['flagBalance'])? $arr['flagBalance'] : '',
		));
		$this->_iniAdd(array(
			'arrRows'         => $arr['arrRowsAdd'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'flagTempNext'    => ($arr['flagTempNext'])? 1 : 0,
			'flagBalance'     => ($arr['flagBalance'])? $arr['flagBalance'] : '',
		));
	}

	/**
		(array(
			'flagStatus'      => 'delete',
			'arrRows'         => $arrRows,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'flagTempNext'    => 1,
		))
	 */
	protected function _iniDelete($arr)
	{
		$arr['arrRows'] = $this->_getArrRowsReverse(array(
			'arrRows'         => $arr['arrRows'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		$this->_iniAdd(array(
			'arrRows'         => $arr['arrRows'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'flagDelete'      => 1,
			'flagTempNext'    => ($arr['flagTempNext'])? 1 : 0,
			'flagBalance'     => ($arr['flagBalance'])? $arr['flagBalance'] : '',
		));
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
		(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _getVarsItem($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingFS' . $strNation,
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
		$varsFS = $rows['arrRows'][0];

		$varsConsumptionTax = $this->_getVarsConsumptionTax(array());

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$array = $this->_getVarsFlagFiscalPeriod(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$varsFiscalPeriod = array();
		foreach ($array as $key => $value) {
			$varsFiscalPeriod[$value] = $this->_getVarsStampTerm(array(
				'varsFlag'         => array('flagFiscalPeriod' => $value),
				'varsEntityNation' => $varsEntityNation,
				'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			));
		}
		$varsFiscalPeriodMonth = $this->_getVarsFiscalPeriodMonth(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$varsDepartmentTreeItem = $this->_getVarsDepartmentTreeItem();

		$varsAccountTitle = array();
		$array = $this->_extendSelf['arrFS'];
		foreach ($array as $key => $value) {
			if ($value == 'CR') {
				if (!(int) $varsEntityNation['flagCR']) {
					continue;
				}
			}
			$data = $this->_getArrSelectOption(array(
				'arrStrTitle'     => array(),
				'arrSelectTag'    => array(),
				'vars'            => $varsFS['jsonJgaapAccountTitle' . $value],
				'flagBS'          => ($value == 'BS')? 1 : 0,
				'flagFS'          => $value,
			));
			$varsAccountTitle[$value] = $data['arrStrTitle'];
		}

		$varsDepartment = $this->_getVarsDepartment(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$data = array(
			'varsFS'                 => $varsFS,
			'varsConsumptionTax'     => $varsConsumptionTax,
			'varsEntityNation'       => $varsEntityNation,
			'varsFiscalPeriod'       => $varsFiscalPeriod,
			'varsFiscalPeriodMonth'  => $varsFiscalPeriodMonth,
			'varsAccountTitle'       => $varsAccountTitle,
			'varsDepartment'         => $varsDepartment,
			'varsDepartmentTreeItem' => $varsDepartmentTreeItem,
			'numFiscalPeriod'        => $arr['numFiscalPeriod'],
		);

		return $data;

	}

	protected function _getVarsDepartment($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingEntityDepartment',
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

		$arrayNew = array();
		$array = $rows['arrRows'];
		foreach ($array as $key => $value) {
			$arrayNew[$value['idDepartment']] = 1;
		}

		return $arrayNew;
	}

	/**
		(array(
			'arrRows'      => $arr['arrRows'],
			'idDepartment' => $idDepartment,
		))
	 */
	protected function _checkArrRowsDepartment($arr)
	{
		$arrayNew = array();
		$num = 0;
		$array = $arr['arrRows'];
		foreach ($array as $key => $value) {
			if ($arr['idDepartment'] == $value['idDepartment']) {
				$arrayNew[$num] = $value;
				$num++;
			}
		}

		return $arrayNew;
	}

	/**
		(array(
			'arrRows'      => $arr['arrRows'],
			'varsItem'     => $varsItem,
			'flagDelete'   => ($arr['flagDelete'])? 1 : 0,
			'flagTempNext' => ($arr['flagTempNext'])? 1 : 0,
			'flagBalance'  => $arr['flagBalance'],
			'flagCalc'     => 1 : 0,
		))
	 */
	protected function _setVarsValue($arr)
	{
		$varsFSValue = $this->_getVarsFSValue(array(
			'numFiscalPeriod' => $arr['varsItem']['numFiscalPeriod'],
		));

		if ($arr['flagBalance'] && $arr['flagBalance'] == 'department') {
			$array = $arr['varsItem']['varsDepartment'];
			foreach ($array as $key => $value) {
				$arrRows = $arr['arrRows'];

				$arrRows = $this->_checkArrRowsDepartment(array(
					'arrRows'      => $arr['arrRows'],
					'idDepartment' => $key,
				));

				if (!count($arrRows)) {
					if (!$arr['flagCalcDepartment']) {
						continue;
					}
				}

				$varsFSValue = $this->_getVarsFSValueDepartment(array(
					'idDepartment'     => $key,
					'numFiscalPeriod'  => $arr['varsItem']['numFiscalPeriod'],
				));

				$varsFSValue = $this->_loopVarsValue(array(
					'flagDepartment' => 1,
					'arrRows'        => $arrRows,
					'varsFSValue'    => $varsFSValue,
					'varsItem'       => $arr['varsItem'],
					'flagDelete'     => $arr['flagDelete'],
					'flagTempNext'   => $arr['flagTempNext'],
				));

				$flag = $this->_updateDb(array(
					'idDepartment' => $key,
					'varsValue'    => $varsFSValue,
					'varsItem'     => $arr['varsItem'],
					'strColumn'    => 'jsonJgaapAccountTitle',
				));
				if ($flag == 'errorDataMax') {
					return $flag;
				}
			}

		} else {
			$varsFSValue = $this->_loopVarsValue(array(
				'arrRows'      => $arr['arrRows'],
				'varsFSValue'  => $varsFSValue,
				'varsItem'     => $arr['varsItem'],
				'flagDelete'   => $arr['flagDelete'],
				'flagTempNext' => $arr['flagTempNext'],
				'flagBalance'  => $arr['flagBalance'],
			));

			$flag = $this->_updateDb(array(
				'varsValue' => $varsFSValue,
				'varsItem'  => $arr['varsItem'],
				'strColumn' => 'jsonJgaapAccountTitle',
			));
			if ($flag == 'errorDataMax') {
				return $flag;
			}

			$array = $arr['varsItem']['varsDepartment'];
			foreach ($array as $key => $value) {
				$arrRows = $arr['arrRows'];

				$arrRows = $this->_checkArrRowsDepartment(array(
					'arrRows'      => $arr['arrRows'],
					'idDepartment' => $key,
				));

				if (!count($arrRows)) {
					if (!$arr['flagCalcDepartment']) {
						continue;
					}
				}

				$varsFSValue = $this->_getVarsFSValueDepartment(array(
					'idDepartment'     => $key,
					'numFiscalPeriod'  => $arr['varsItem']['numFiscalPeriod'],
				));

				$varsFSValue = $this->_loopVarsValue(array(
					'flagDepartment' => 1,
					'arrRows'        => $arrRows,
					'varsFSValue'    => $varsFSValue,
					'varsItem'       => $arr['varsItem'],
					'flagDelete'     => $arr['flagDelete'],
					'flagTempNext'   => $arr['flagTempNext'],
				));

				$flag = $this->_updateDb(array(
					'idDepartment' => $key,
					'varsValue'    => $varsFSValue,
					'varsItem'     => $arr['varsItem'],
					'strColumn'    => 'jsonJgaapAccountTitle',
				));
				if ($flag == 'errorDataMax') {
					return $flag;
				}
			}
		}
	}


	/**
		(array(
			'idDepartment' => $key,
			'varsValue'    => $varsFSValue,
			'varsItem'     => $arr['varsItem'],
			'strColumn'    => 'jsonJgaapAccountTitle',
		))
	 */
	protected function _updateDb($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$arrColumn = array();
		$arrValue = array();

		$array = $this->_extendSelf['arrFS'];
		foreach ($array as $key => $value) {
			if ($value == 'CR') {
				if (!(int) $arr['varsItem']['varsEntityNation']['flagCR']) {
					continue;
				}
			}
			$arrColumn[] = $arr['strColumn'] . $value;
			if ($arr['varsValue'][$arr['strColumn'] . $value]) {
				$json = json_encode($arr['varsValue'][$arr['strColumn'] . $value]);
				$flag = $this->checkTextSize(array(
					'flagReturn' => 1,
					'str'        => $json,
				));
				if ($flag) {
					return 'errorDataMax';
				}
				$arrValue[] = $json;

			} else {

				$arrValue[] = '';
			}
		}

		if ($arr['idDepartment']) {
			$classDb->updateRow(array(
				'idModule' => 'accounting',
				'strTable' => 'accountingEntityDepartmentFSValue' . $strNation,
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
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idDepartment',
						'flagCondition' => 'eq',
						'value'         => $arr['idDepartment'],
					),
				),
				'arrValue'  => $arrValue,
			));

		} else {
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
	}

	/**
		(array(
			'flagDepartment'  => 1,
			'arrRows'         => $arrRows,
			'varsFSValue'     => $varsFSValue,
			'varsItem'        => $arr['varsItem'],
			'flagDelete'      => $arr['flagDelete'],
			'flagTempNext'    => $arr['flagTempNext'],
		))
	 */
	protected function _loopVarsValue($arr)
	{
		$array = $this->_extendSelf['arrFS'];
		foreach ($array as $key => $value) {
			if ($value == 'CR') {
				if (!(int) $arr['varsItem']['varsEntityNation']['flagCR']) {
					continue;
				}
			}
			$arr['varsFSValue']['jsonJgaapAccountTitle' . $value] = $this->_getValueFS(array(
				'flagFS'           => $value,
				'flagDelete'       => $arr['flagDelete'],
				'flagTempNext'     => $arr['flagTempNext'],
				'varsAccountTitle' => $arr['varsItem']['varsAccountTitle'][$value],
				'arrRows'          => $arr['arrRows'],
				'varsItem'         => $arr['varsItem'],
				'varsValue'        => $arr['varsFSValue']['jsonJgaapAccountTitle' . $value],
			));
		}

		$strFS = 'jsonJgaapAccountTitle';
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

		$flagMonthFirst = 1;
		$array = $arr['varsItem']['varsFiscalPeriod'];
		foreach ($array as $key => $value) {
			if ((int) $arr['varsItem']['varsEntityNation']['flagCR']) {
				//CR Loop
				$this->_loopVarsCalc(array(
					'varsFS'    => $arr['varsItem']['varsFS'][$strFS . 'CR'],
					'varsValue' => &$arr['varsFSValue'][$strFS . 'CR'][$key],
				));

				//CR->PL
				$arr['varsFSValue'][$strFS . 'PL'][$key]['currentTermProductsCost']['sumPrev'] = 0;

				$arr['varsFSValue'][$strFS . 'PL'][$key]['currentTermProductsCost']['sumDebit']
					 = $arr['varsFSValue'][$strFS . 'CR'][$key]['currentWorkInProcessNet']['sumDebit'];

				$arr['varsFSValue'][$strFS . 'PL'][$key]['currentTermProductsCost']['sumCredit']
					 = $arr['varsFSValue'][$strFS . 'CR'][$key]['currentWorkInProcessNet']['sumCredit'];

				$arr['varsFSValue'][$strFS . 'PL'][$key]['currentTermProductsCost']['sumNext']
					 = $arr['varsFSValue'][$strFS . 'CR'][$key]['currentWorkInProcessNet']['sumNext'];
			}

			//PL Loop
			$this->_loopVarsCalc(array(
				'varsFS'    => $arr['varsItem']['varsFS'][$strFS . 'PL'],
				'varsValue' => &$arr['varsFSValue'][$strFS . 'PL'][$key],
			));

			//PL->BS
			$sumPrev = 0;
			if (preg_match("/^(f1|f21|f41)$/", $key) || ($flagMonthFirst && !preg_match("/^f/", $key))) {
				$sumPrev = 0;

			} elseif (preg_match("/^(f2)/", $key)) {
				if ($key == 'f22') {
					$sumPrev =  $arr['varsFSValue'][$strFS . 'BS']['f21']['netIncome']['sumNext'];
				}

			} elseif (preg_match("/^(f4)/", $key)) {
				if ($key == 'f42') {
					$sumPrev =  $arr['varsFSValue'][$strFS . 'BS']['f41']['netIncome']['sumNext'];

				} elseif ($key == 'f43') {
					$sumPrev =  $arr['varsFSValue'][$strFS . 'BS']['f42']['netIncome']['sumNext'];

				} elseif ($key == 'f44') {
					$sumPrev =  $arr['varsFSValue'][$strFS . 'BS']['f43']['netIncome']['sumNext'];
				}

			} else {
				$numMonthPrev = (int) $key - 1;
				if ($numMonthPrev < 1) {
					$numMonthPrev += 12;
				}
				$sumPrev =  $arr['varsFSValue'][$strFS . 'BS'][$numMonthPrev]['netIncome']['sumNext'];
			}


			if ($flagMonthFirst && !preg_match("/^f/", $key)) {
				$flagMonthFirst = 0;
			}

			$arr['varsFSValue'][$strFS . 'BS'][$key]['netIncome']['sumPrev'] = $sumPrev;

			$arr['varsFSValue'][$strFS . 'BS'][$key]['netIncome']['sumDebit']
				 = $arr['varsFSValue'][$strFS . 'PL'][$key]['currentTermProfitOrLossNet']['sumDebit'];

			$arr['varsFSValue'][$strFS . 'BS'][$key]['netIncome']['sumCredit']
				 = $arr['varsFSValue'][$strFS . 'PL'][$key]['currentTermProfitOrLossNet']['sumCredit'];

			$arr['varsFSValue'][$strFS . 'BS'][$key]['netIncome']['sumNext']
				 = $arr['varsFSValue'][$strFS . 'PL'][$key]['currentTermProfitOrLossNet']['sumNext'] + $sumPrev;


			//BS Loop
			$this->_loopVarsCalc(array(
				'varsFS'    => $varsFSBS,
				'varsValue' => &$arr['varsFSValue'][$strFS . 'BS'][$key],
			));

			$sumDebit = 0;
			$sumCredit = 0;
			$arraySum = array('sumPrev', 'sumDebit', 'sumCredit', 'sumNext');
			foreach ($arraySum as $keySum => $valueSum) {

				//資産の部合計
				$numAssetsSum = $arr['varsFSValue'][$strFS . 'BS'][$key]['assetsSum'][$valueSum];

				//純資産の部合計
				$numNetAssetsSum = $arr['varsFSValue'][$strFS . 'BS'][$key]['netAssetsSum'][$valueSum];

				//負債の部合計
				$numliabilitiesSum = $arr['varsFSValue'][$strFS . 'BS'][$key]['liabilitiesSum'][$valueSum];
				$netDepartment = $numAssetsSum - $numNetAssetsSum - $numliabilitiesSum;

				if ($valueSum == 'sumCredit') {
					if ($netDepartment > 0) {
						$sumDebit -= abs($netDepartment);
					} else {
						$sumDebit += abs($netDepartment);
					}


				} elseif ($valueSum == 'sumDebit') {
					if ($netDepartment > 0) {
						$sumCredit += abs($netDepartment);
					} else {
						$sumCredit -= abs($netDepartment);
					}

				} else {
					if ($arr['flagDepartment']) {
						$arr['varsFSValue'][$strFS . 'BS'][$key]['departmentNet'][$valueSum] = $netDepartment;
					}
				}
			}

			if ($arr['flagDepartment']) {
				$arr['varsFSValue'][$strFS . 'BS'][$key]['departmentNet']['sumDebit'] = abs($sumDebit);
				$arr['varsFSValue'][$strFS . 'BS'][$key]['departmentNet']['sumCredit'] = abs($sumCredit);
			}


			//負債及び純資産の部合計
			$sumDebit = 0;
			$sumCredit = 0;
			$arraySum = array('sumPrev', 'sumDebit', 'sumCredit', 'sumNext');
			foreach ($arraySum as $keySum => $valueSum) {
				$numNetAssetsSum = $arr['varsFSValue'][$strFS . 'BS'][$key]['netAssetsSum'][$valueSum];
				$numliabilitiesSum = $arr['varsFSValue'][$strFS . 'BS'][$key]['liabilitiesSum'][$valueSum];
				$numDepartmentNet =  $arr['varsFSValue'][$strFS . 'BS'][$key]['departmentNet'][$valueSum];

				$sum = $numNetAssetsSum + $numliabilitiesSum + $numDepartmentNet;

				if ($valueSum == 'sumDebit') {
					if ($sum < 0) {
						$sumCredit += abs($sum);
					} else {
						$sumDebit += abs($sum);
					}


				} elseif ($valueSum == 'sumCredit') {
					if ($sum < 0) {
						$sumDebit += abs($sum);
					} else {
						$sumCredit += abs($sum);
					}

				} else {
					$arr['varsFSValue'][$strFS . 'BS'][$key]['liabilitiesNetAssetsNet'][$valueSum] = $sum;
				}

			}
			$arr['varsFSValue'][$strFS . 'BS'][$key]['liabilitiesNetAssetsNet']['sumDebit'] = $sumDebit;
			$arr['varsFSValue'][$strFS . 'BS'][$key]['liabilitiesNetAssetsNet']['sumCredit'] = $sumCredit;

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
				$numPrev = 0;
				$numDebit = 0;
				$numCredit = 0;
				$numNext = 0;
				if ($value['vars']['flagCalc'] == 'sum') {
					foreach ($arraySum as $keySum => $valueSum) {
						if ((int) $value['vars']['flagDebit']) {
							if ($valueSum['flagDebit']) {
								$numPrev += $valueSum['numPrev'];
								$numNext += $valueSum['numNext'];

							} else {
								$numPrev -= $valueSum['numPrev'];
								$numNext -= $valueSum['numNext'];
							}
						} else {
							if ($valueSum['flagDebit']) {
								$numPrev -= $valueSum['numPrev'];
								$numNext -= $valueSum['numNext'];

							} else {
								$numPrev += $valueSum['numPrev'];
								$numNext += $valueSum['numNext'];
							}
						}
						$numDebit += $valueSum['numDebit'];
						$numCredit += $valueSum['numCredit'];
					}
					$arraySum = array();
					$arr['varsValue'][$value['vars']['idTarget']]['sumPrev'] = $numPrev;
					$arr['varsValue'][$value['vars']['idTarget']]['sumDebit'] = $numDebit;
					$arr['varsValue'][$value['vars']['idTarget']]['sumCredit'] = $numCredit;
					$arr['varsValue'][$value['vars']['idTarget']]['sumNext'] = $numNext;

				} elseif ($value['vars']['flagCalc'] == 'net') {
					foreach ($arrayNet as $keyNet => $valueNet) {
						if ((int) $value['vars']['flagDebit']) {
							if ($valueNet['flagDebit']) {
								$numPrev += $valueNet['numPrev'];
								$numNext += $valueNet['numNext'];

							} else {
								$numPrev -= $valueNet['numPrev'];
								$numNext -= $valueNet['numNext'];
							}
						} else {
							if ($valueNet['flagDebit']) {
								$numPrev -= $valueNet['numPrev'];
								$numNext -= $valueNet['numNext'];

							} else {
								$numPrev += $valueNet['numPrev'];
								$numNext += $valueNet['numNext'];
							}
						}
						$numDebit += $valueNet['numDebit'];
						$numCredit += $valueNet['numCredit'];
					}
					$flagSide = $numDebit - $numCredit;
					if ($flagSide == 0) {
						$numDebit = 0;
						$numCredit = 0;

					} elseif ($flagSide < 0) {
						$numDebit = 0;
						$numCredit = abs($flagSide);

					} elseif ($flagSide > 0) {
						$numDebit = abs($flagSide);
						$numCredit = 0;
					}

					$arr['varsValue'][$value['vars']['idTarget']]['sumPrev'] = $numPrev;
					$arr['varsValue'][$value['vars']['idTarget']]['sumDebit'] = $numDebit;
					$arr['varsValue'][$value['vars']['idTarget']]['sumCredit'] = $numCredit;
					$arr['varsValue'][$value['vars']['idTarget']]['sumNext'] = $numNext;

				} else {
					if (!is_null($arr['varsValue'][$value['vars']['idTarget']])) {
						$numPrev =  $arr['varsValue'][$value['vars']['idTarget']]['sumPrev'];
						$numDebit =  $arr['varsValue'][$value['vars']['idTarget']]['sumDebit'];
						$numCredit =  $arr['varsValue'][$value['vars']['idTarget']]['sumCredit'];
						$numNext =  $arr['varsValue'][$value['vars']['idTarget']]['sumNext'];
					}
				}
				$data = array(
					'flagDebit' => (int) $value['vars']['flagDebit'],
					'numPrev'   => $numPrev,
					'numDebit'  => $numDebit,
					'numCredit' => $numCredit,
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
			'flagFS'           => $value,
			'flagDelete'       => $arr['flagDelete'],
			'flagTempNext'     => $arr['flagTempNext'],
			'varsAccountTitle' => $arr['varsItem']['varsAccountTitle'][$value],
			'arrRows'          => $arr['arrRows'],
			'varsItem'         => $arr['varsItem'],
			'varsValue'        => $arr['varsFSValue']['jsonJgaapAccountTitle' . $value],
		))
	 */
	protected function _getValueFS($arr)
	{
		$varsValue = array();
		if ($arr['varsValue']) {
			$varsValue = $arr['varsValue'];
		}

		$varsFiscalPeriod = $arr['varsItem']['varsFiscalPeriod'];

		$dataTmpl = array(
			'sumPrev'    => 0,
			'sumDebit'   => 0,
			'sumCredit'  => 0,
			'sumNext'    => 0,
			'varsAdjust'    => array(
				'sumPrev'    => 0,
				'sumDebit'   => 0,
				'sumCredit'  => 0,
				'sumNext'    => 0,
			),
			'varsAdjust2'    => array(
				'sumPrev'    => 0,
				'sumDebit'   => 0,
				'sumCredit'  => 0,
				'sumNext'    => 0,
			),
		);

		$array = $arr['varsItem']['varsFiscalPeriodMonth'];
		$arrayRows = &$arr['arrRows'];
		foreach ($arrayRows as $keyRows => $valueRows) {
			if (!$arr['varsAccountTitle'][$valueRows['idAccountTitle']]) {
				continue;
			}
			$flag = 0;
			$flagSumDebit = 0;
			$arrayflagSumNext = array();
			$arrayflagTax = array();
			foreach ($array as $key => $value) {
				$numMonth = $value;
				if ($numMonth == $valueRows['arrDate']['month']) {
					$flag = 1;
				}
				if ($flag) {
					$numValue = $valueRows['numValue'];
					if ($valueRows['flagDebitAccountTitle']) {
						if (!$valueRows['flagDebit']) {
							$numValue *= (-1);
						}

					} else {
						if ($valueRows['flagDebit']) {
							$numValue *= (-1);
						}
					}

					$flagFiscalReport = 'varsAdjust';
					if (preg_match( "/^f2/", $valueRows['flagFiscalReport'])) {
						$flagFiscalReport = 'varsAdjust2';
					}

					//init
					//f1
					if (is_null($varsValue['f1'][$valueRows['idAccountTitle']])) {
						$varsValue['f1'][$valueRows['idAccountTitle']] = $dataTmpl;
					}

					//month
					if (is_null($varsValue[$numMonth][$valueRows['idAccountTitle']])) {
						$varsValue[$numMonth][$valueRows['idAccountTitle']] = $dataTmpl;
					}

					//f2
					if ($varsFiscalPeriod['f21']) {
						$arrayReport = array('f21', 'f22');
						foreach ($arrayReport as $keyReport => $valueReport) {
							if (is_null($varsValue[$valueReport][$valueRows['idAccountTitle']])) {
								$varsValue[$valueReport][$valueRows['idAccountTitle']] = $dataTmpl;
							}
						}
					}

					//f4
					if ($varsFiscalPeriod['f41']) {
						$arrayReport = array('f41', 'f42', 'f43', 'f44');
						foreach ($arrayReport as $keyReport => $valueReport) {
							if (is_null($varsValue[$valueReport][$valueRows['idAccountTitle']])) {
								$varsValue[$valueReport][$valueRows['idAccountTitle']] = $dataTmpl;
							}
						}
					}

					//sumDebit sumCredit
					$str = 'sumCredit';
					if ($valueRows['flagDebit']) {
						$str = 'sumDebit';
					}
					if ($arr['flagDelete']) {
						$str = 'sumCredit';
						if (!$valueRows['flagDebit']) {
							$str = 'sumDebit';
						}
					}

					if (!$arr['flagTempNext']) {
						if (!$flagSumDebit) {
							//f1
							if ($arr['flagDelete']) {
								$varsValue['f1'][$valueRows['idAccountTitle']][$str] -= $valueRows['numValue'];
								if ($valueRows['flagFiscalReport']) {
									$varsValue['f1'][$valueRows['idAccountTitle']][$flagFiscalReport][$str] -= $valueRows['numValue'];
								}

							} else {
								$varsValue['f1'][$valueRows['idAccountTitle']][$str] += $valueRows['numValue'];
								if ($valueRows['flagFiscalReport']) {
									$varsValue['f1'][$valueRows['idAccountTitle']][$flagFiscalReport][$str] += $valueRows['numValue'];
								}
							}

							//month
							if ($valueRows['stampBook'] >= $varsFiscalPeriod[$numMonth]['stampMin']
								&& $valueRows['stampBook'] <= $varsFiscalPeriod[$numMonth]['stampMax']
							) {
								if ($arr['flagDelete']) {
									$varsValue[$numMonth][$valueRows['idAccountTitle']][$str] -= $valueRows['numValue'];
									if ($valueRows['flagFiscalReport']) {
										$varsValue[$numMonth][$valueRows['idAccountTitle']][$flagFiscalReport][$str] -= $valueRows['numValue'];
									}

								} else {
									$varsValue[$numMonth][$valueRows['idAccountTitle']][$str] += $valueRows['numValue'];
									if ($valueRows['flagFiscalReport']) {
										$varsValue[$numMonth][$valueRows['idAccountTitle']][$flagFiscalReport][$str] += $valueRows['numValue'];
									}
								}
							}

							//f2
							if ($varsFiscalPeriod['f21']) {
								$arrayReport = array('f21', 'f22');
								foreach ($arrayReport as $keyReport => $valueReport) {
									if ($valueRows['stampBook'] >= $varsFiscalPeriod[$valueReport]['stampMin']
										&& $valueRows['stampBook'] <= $varsFiscalPeriod[$valueReport]['stampMax']
									) {
										if ($arr['flagDelete']) {
											$varsValue[$valueReport][$valueRows['idAccountTitle']][$str] -= $valueRows['numValue'];
											if ($valueRows['flagFiscalReport']) {
												$varsValue[$valueReport][$valueRows['idAccountTitle']][$flagFiscalReport][$str] -= $valueRows['numValue'];
											}

										} else {
											$varsValue[$valueReport][$valueRows['idAccountTitle']][$str] += $valueRows['numValue'];
											if ($valueRows['flagFiscalReport']) {
												$varsValue[$valueReport][$valueRows['idAccountTitle']][$flagFiscalReport][$str] += $valueRows['numValue'];
											}
										}
									}
								}

							//f4
							}
							if ($varsFiscalPeriod['f41']) {
								$arrayReport = array('f41', 'f42', 'f43', 'f44');
								foreach ($arrayReport as $keyReport => $valueReport) {
									if ($valueRows['stampBook'] >= $varsFiscalPeriod[$valueReport]['stampMin']
										&& $valueRows['stampBook'] <= $varsFiscalPeriod[$valueReport]['stampMax']
									) {
										if ($arr['flagDelete']) {
											$varsValue[$valueReport][$valueRows['idAccountTitle']][$str] -= $valueRows['numValue'];
											if ($valueRows['flagFiscalReport']) {
												$varsValue[$valueReport][$valueRows['idAccountTitle']][$flagFiscalReport][$str] -= $valueRows['numValue'];
											}

										} else {
											$varsValue[$valueReport][$valueRows['idAccountTitle']][$str] += $valueRows['numValue'];
											if ($valueRows['flagFiscalReport']) {
												$varsValue[$valueReport][$valueRows['idAccountTitle']][$flagFiscalReport][$str] += $valueRows['numValue'];
											}
										}
									}
								}
							}
							$flagSumDebit = 1;
						}
					}

					//sumNext sumPrev
					//f1
					if (!$arrayflagSumNext['f1']) {
						$varsValue['f1'][$valueRows['idAccountTitle']]['sumNext'] += $numValue;
						if ($valueRows['flagFiscalReport']) {
							$varsValue['f1'][$valueRows['idAccountTitle']][$flagFiscalReport]['sumNext'] += $numValue;
						}
						//sumPrev
						$varsValue['f1'][$valueRows['idAccountTitle']]['sumPrev'] = $this->_getSumPrev(array(
							'flagDebit' => $valueRows['flagDebitAccountTitle'],
							'varsValue' => $varsValue['f1'][$valueRows['idAccountTitle']],
						));
						if ($valueRows['flagFiscalReport']) {
							$varsValue['f1'][$valueRows['idAccountTitle']][$flagFiscalReport]['sumPrev'] = $this->_getSumPrev(array(
								'flagDebit' => $valueRows['flagDebitAccountTitle'],
								'varsValue' => $varsValue['f1'][$valueRows['idAccountTitle']][$flagFiscalReport],
							));
						}
						$arrayflagSumNext['f1'] = 1;
					}

					if ($arr['flagFS'] == 'BS') {
						//month
						if ($valueRows['stampBook'] <= $varsFiscalPeriod[$numMonth]['stampMax']) {
							$varsValue[$numMonth][$valueRows['idAccountTitle']]['sumNext'] += $numValue;
							if ($valueRows['flagFiscalReport']) {
								$varsValue[$numMonth][$valueRows['idAccountTitle']][$flagFiscalReport]['sumNext'] += $numValue;
							}
							//sumPrev
							$varsValue[$numMonth][$valueRows['idAccountTitle']]['sumPrev'] = $this->_getSumPrev(array(
								'flagDebit' => $valueRows['flagDebitAccountTitle'],
								'varsValue' => $varsValue[$numMonth][$valueRows['idAccountTitle']],
							));
							if ($valueRows['flagFiscalReport']) {
								$varsValue[$numMonth][$valueRows['idAccountTitle']][$flagFiscalReport]['sumPrev'] = $this->_getSumPrev(array(
									'flagDebit' => $valueRows['flagDebitAccountTitle'],
									'varsValue' => $varsValue[$numMonth][$valueRows['idAccountTitle']][$flagFiscalReport],
								));
							}
						}

						//f2
						if ($varsFiscalPeriod['f21']) {
							$arrayReport = array('f21', 'f22');
							foreach ($arrayReport as $keyReport => $valueReport) {
								if (!$arrayflagSumNext[$valueReport]) {
									if ($valueRows['stampBook']  <= $varsFiscalPeriod[$valueReport]['stampMax']) {
										$varsValue[$valueReport][$valueRows['idAccountTitle']]['sumNext'] += $numValue;
										if ($valueRows['flagFiscalReport']) {
											$varsValue[$valueReport][$valueRows['idAccountTitle']][$flagFiscalReport]['sumNext'] += $numValue;
										}
										//sumPrev
										$varsValue[$valueReport][$valueRows['idAccountTitle']]['sumPrev'] = $this->_getSumPrev(array(
											'flagDebit' => $valueRows['flagDebitAccountTitle'],
											'varsValue' => $varsValue[$valueReport][$valueRows['idAccountTitle']],
										));
										if ($valueRows['flagFiscalReport']) {
											$varsValue[$valueReport][$valueRows['idAccountTitle']][$flagFiscalReport]['sumPrev'] = $this->_getSumPrev(array(
												'flagDebit' => $valueRows['flagDebitAccountTitle'],
												'varsValue' => $varsValue[$valueReport][$valueRows['idAccountTitle']][$flagFiscalReport],
											));
										}

										$arrayflagSumNext[$valueReport] = 1;
									}
								}
							}

						//f4
						}
						if ($varsFiscalPeriod['f41']) {
							$arrayReport = array('f41', 'f42', 'f43', 'f44');
							foreach ($arrayReport as $keyReport => $valueReport) {
								if (!$arrayflagSumNext[$valueReport]) {
									if ($valueRows['stampBook']  <= $varsFiscalPeriod[$valueReport]['stampMax']) {
										$varsValue[$valueReport][$valueRows['idAccountTitle']]['sumNext'] += $numValue;
										if ($valueRows['flagFiscalReport']) {
											$varsValue[$valueReport][$valueRows['idAccountTitle']][$flagFiscalReport]['sumNext'] += $numValue;
										}
										//sumPrev
										$varsValue[$valueReport][$valueRows['idAccountTitle']]['sumPrev'] = $this->_getSumPrev(array(
											'flagDebit' => $valueRows['flagDebitAccountTitle'],
											'varsValue' => $varsValue[$valueReport][$valueRows['idAccountTitle']],
										));
										if ($valueRows['flagFiscalReport']) {
											$varsValue[$valueReport][$valueRows['idAccountTitle']][$flagFiscalReport]['sumPrev'] = $this->_getSumPrev(array(
												'flagDebit' => $valueRows['flagDebitAccountTitle'],
												'varsValue' => $varsValue[$valueReport][$valueRows['idAccountTitle']][$flagFiscalReport],
											));
										}

										$arrayflagSumNext[$valueReport] = 1;
									}
								}
							}
						}

					} else {
						//month
						if ($valueRows['stampBook'] >= $varsFiscalPeriod[$numMonth]['stampMin']
							&& $valueRows['stampBook'] <= $varsFiscalPeriod[$numMonth]['stampMax']
						) {
							$varsValue[$numMonth][$valueRows['idAccountTitle']]['sumNext'] += $numValue;
							if ($valueRows['flagFiscalReport']) {
								$varsValue[$numMonth][$valueRows['idAccountTitle']][$flagFiscalReport]['sumNext'] += $numValue;
							}
						}

						//f2
						if ($varsFiscalPeriod['f21']) {
							$arrayReport = array('f21', 'f22');
							foreach ($arrayReport as $keyReport => $valueReport) {
								if (!$arrayflagSumNext[$valueReport]) {
									if ($valueRows['stampBook'] >= $varsFiscalPeriod[$valueReport]['stampMin']
										&& $valueRows['stampBook'] <= $varsFiscalPeriod[$valueReport]['stampMax']
									) {
										$varsValue[$valueReport][$valueRows['idAccountTitle']]['sumNext'] += $numValue;
										if ($valueRows['flagFiscalReport']) {
											$varsValue[$valueReport][$valueRows['idAccountTitle']][$flagFiscalReport]['sumNext'] += $numValue;
										}
										$arrayflagSumNext[$valueReport] = 1;
									}
								}
							}

						//f4
						}
						if ($varsFiscalPeriod['f41']) {
							$arrayReport = array('f41', 'f42', 'f43', 'f44');
							foreach ($arrayReport as $keyReport => $valueReport) {
								if (!$arrayflagSumNext[$valueReport]) {
									if ($valueRows['stampBook'] >= $varsFiscalPeriod[$valueReport]['stampMin']
										&& $valueRows['stampBook'] <= $varsFiscalPeriod[$valueReport]['stampMax']
									) {
										$varsValue[$valueReport][$valueRows['idAccountTitle']]['sumNext'] += $numValue;
										if ($valueRows['flagFiscalReport']) {
											$varsValue[$valueReport][$valueRows['idAccountTitle']][$flagFiscalReport]['sumNext'] += $numValue;
										}
										$arrayflagSumNext[$valueReport] = 1;
									}
								}
							}
						}
					}
				}
				if ($varsFiscalPeriod['f21']) {
					$arrayReport = array('f21', 'f22');
					foreach ($arrayReport as $keyReport => $valueReport) {
						if (is_null($varsValue[$valueReport])) {
							$varsValue[$valueReport] = array();
						}
					}
				}
				if ($varsFiscalPeriod['f41']) {
					$arrayReport = array('f41', 'f42', 'f43', 'f44');
					foreach ($arrayReport as $keyReport => $valueReport) {
						if (is_null($varsValue[$valueReport])) {
							$varsValue[$valueReport] = array();
						}
					}
				}
				if (is_null($varsValue[$numMonth])) {
					$varsValue[$numMonth] = array();
				}
			}
		}

		return $varsValue;
	}

	/**
	 (array(

	 ))
	 */
	protected function _getSumPrev($arr)
	{
		$sumPrev = 0;
		$sumDebit = $arr['varsValue']['sumDebit'];
		$sumCredit = $arr['varsValue']['sumCredit'];
		$sumNext = $arr['varsValue']['sumNext'];

		if ($arr['flagDebit']) {
			$sumPrev = $sumNext - $sumDebit + $sumCredit;

		} else {
			$sumPrev = $sumNext + $sumDebit - $sumCredit;
		}

		return $sumPrev;
	}

	/**
		(array(
			'flagStatus'        => 'balanceCheck',
			'arrIdAccountTitle' => $arrIdAccountTitle,
			'numFiscalPeriod'   => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		))
	 */
	protected function _iniBalanceCheck($arr)
	{
		if (!$this->_extChildSelf['varsItemBalance'][$arr['numFiscalPeriod']]) {
			$this->_extChildSelf['varsItemBalance'][$arr['numFiscalPeriod']] = $this->_getBalanceVarsItem(array(
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
			));
		}

		$varsIdAccountTitle = array();
		$array = $arr['arrIdAccountTitle'];
		foreach ($array as $key => $value) {
			$varsIdAccountTitle[$key] = 0;
		}

		$varsIdAccountTitle = $this->_getBalanceVarsFS(array(
			'varsIdAccountTitle' => $varsIdAccountTitle,
			'varsItem'           => $this->_extChildSelf['varsItemBalance'][$arr['numFiscalPeriod']],
			'numFiscalPeriod'    => $arr['numFiscalPeriod'],
		));

		return $varsIdAccountTitle;
	}

	/**
		(array(
			'arrRows'         => $arr['arrRows'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _getBalanceVarsItem($arr)
	{
		$arrSubAccountTitle = $this->_getVarsSubAccountTitle(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$arrAccountTitle = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'numFiscalPeriod'    => $arr['numFiscalPeriod'],
		));

		$varsDepartment = $this->_getVarsDepartment(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$data = array(
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'arrAccountTitle'    => $arrAccountTitle,
			'varsDepartment'     => $varsDepartment,
			'numFiscalPeriod'    => $arr['numFiscalPeriod'],
		);

		return $data;
	}

	/**
		(array(
			'varsIdAccountTitle' => $varsIdAccountTitle,
			'varsItem'           => $this->_extChildSelf['varsItemBalance'],
			'numFiscalPeriod'    => $arr['numFiscalPeriod'],
		))
	 */
	protected function _getBalanceVarsFS($arr)
	{
		$idAccountTitle='cash';

		$array = $arr['varsIdAccountTitle'];
		foreach ($array as $key => $value) {
			$idAccountTitle = $key;

			//all
			$numBalance = $this->_getNumBalance(array(
				'numFiscalPeriod'   => $arr['numFiscalPeriod'],
				'idDepartment'      => 'none',
				'idAccountTitle'    => $idAccountTitle,
				'idSubAccountTitle' => 'none',
			));

			if ($numBalance != 0) {
				$array[$key] = 1;
				continue;
			}

			$arraySub = $arr['varsItem']['arrSubAccountTitle']['arrStrTitle'][$idAccountTitle];
			if (!$arraySub) {
				$arraySub = array();
			}
			foreach ($arraySub as $keySub => $valueSub) {
				$idSubAccountTitle = $keySub;
				$numBalance = $this->_getNumBalance(array(
					'numFiscalPeriod'   => $arr['numFiscalPeriod'],
					'idDepartment'      => 'none',
					'idAccountTitle'    => $idAccountTitle,
					'idSubAccountTitle' => $idSubAccountTitle,
				));
				if ($numBalance != 0) {
					break;
				}
			}
			if ($numBalance != 0) {
				$array[$key] = 1;
				continue;
			}

			$arrayDepartment = $arr['varsItem']['varsDepartment'];
			foreach ($arrayDepartment as $keyDepartment => $valueDepartment) {
				$idDepartment = $keyDepartment;
				$numBalance = $this->_getNumBalance(array(
					'numFiscalPeriod'   => $arr['numFiscalPeriod'],
					'idDepartment'      => $idDepartment,
					'idAccountTitle'    => $idAccountTitle,
					'idSubAccountTitle' => 'none',
				));

				if ($numBalance != 0) {
					break;
				}

				$arraySub = $arr['varsItem']['arrSubAccountTitle']['arrStrTitle'][$idAccountTitle];
				if (!$arraySub) {
					$arraySub = array();
				}
				foreach ($arraySub as $keySub => $valueSub) {
					$idSubAccountTitle = $keySub;
					$numBalance = $this->_getNumBalance(array(
						'numFiscalPeriod'   => $arr['numFiscalPeriod'],
						'idDepartment'      => $idDepartment,
						'idAccountTitle'    => $idAccountTitle,
						'idSubAccountTitle' => $idSubAccountTitle,
					));
					if ($numBalance != 0) {
						break;
					}
				}
				if ($numBalance != 0) {
					break;
				}
			}
			if ($numBalance != 0) {
				$array[$key] = 1;
				continue;
			}
		}

		return $array;
	}

	/**
		(array(
			'numFiscalPeriod'   => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idDepartment'      => $arr['varsFlag']['idDepartment'],
			'idAccountTitle'    => $arr['varsFlag']['idAccountTitle'],
			'idSubAccountTitle' => '',
		))
	 */
	protected function _getNumBalance($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;

		$numValue = 0;
		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);
		$arrWhere = array(
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
		);

		if ($arr['idDepartment'] == 'none') {
			if ($arr['idSubAccountTitle'] == 'none') {
				//numBalance
				$strTable = 'accountingFSValue' . $strNation;

			} else {
				//numBalanceSubAccount
				$strTable = 'accountingSubAccountTitleValue' . $strNation;

				$arrWhere[] = array(
					'flagType'      => '',
					'strColumn'     => 'idSubAccountTitle',
					'flagCondition' => 'eq',
					'value'         => $arr['idSubAccountTitle'],
				);
			}

		} else {
			if ($arr['idSubAccountTitle'] == 'none') {
				//numBalanceDepartment
				$strTable = 'accountingEntityDepartmentFSValue' . $strNation;

				$arrWhere[] = array(
					'flagType'      => '',
					'strColumn'     => 'idDepartment',
					'flagCondition' => 'eq',
					'value'         => $arr['idDepartment'],
				);

			} else {
				//numBalanceDepartmentSubAccount
				$strTable = 'accountingSubAccountTitleValue' . $strNation;
				$arrWhere[] = array(
					'flagType'      => '',
					'strColumn'     => 'idSubAccountTitle',
					'flagCondition' => 'eq',
					'value'         => $arr['idSubAccountTitle'],
				);
			}
		}

		$varsRow = $this->_extChildSelf['varsBalance'][$arr['numFiscalPeriod']][$arr['idDepartment']][$arr['idSubAccountTitle']];
		if (!$varsRow) {
			$rows = $classDb->getSelect(array(
				'idModule'    => 'accounting',
				'strTable'    => $strTable,
				'arrJoin'     => '',
				'arrLimit'    => array(
					'numStart' => 0, 'numEnd' => 1,
				),
				'arrOrder'  => array(),
				'arrWhere'    => $arrWhere,
				'flagAnd'     => 1,
			));
			if (!$rows['numRows']) {
				return $numValue;
			}
			$varsRow = $rows['arrRows'][0];
			$this->_extChildSelf['varsBalance'][$arr['numFiscalPeriod']][$arr['idDepartment']][$arr['idSubAccountTitle']] = $varsRow;
		}

		$idAccountTitle = $arr['idAccountTitle'];
		$flagFiscalPeriod = 'f1';

		if ($arr['idDepartment'] == 'none') {
			if ($arr['idSubAccountTitle'] == 'none') {
				//numBalance
				$numValue = $varsRow['jsonJgaapAccountTitleBS'][$flagFiscalPeriod][$idAccountTitle]['sumPrev'];

			} else {
				//numBalanceSubAccount
				$numValue = $varsRow['jsonData']['all'][$flagFiscalPeriod]['sumPrev'];
			}

		} else {
			if ($arr['idSubAccountTitle'] == 'none') {
				//numBalanceDepartment
				$numValue = $varsRow['jsonJgaapAccountTitleBS'][$flagFiscalPeriod][$idAccountTitle]['sumPrev'];

			} else {
				//numBalanceDepartmentSubAccount
				$idDepartment = $arr['idDepartment'];
				$numValue = $varsRow['jsonData'][$idDepartment][$flagFiscalPeriod]['sumPrev'];
			}
		}

		if (is_null($numValue)) {
			$numValue = 0;
		}

		return $numValue;
	}
}
