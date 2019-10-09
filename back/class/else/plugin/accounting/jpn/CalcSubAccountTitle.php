<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcSubAccountTitle extends Code_Else_Plugin_Accounting_Jpn_Jpn
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
			'flagStatus'      => 'add',
			'arrRows'         => $arrRows,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'flagTempNext'    => 1,
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
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}
		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$this->_updateDbPreferenceStamp(array('strColumn' => 'subAccountTitleValue'));
	}

	/**
		(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _getVarsItem($arr)
	{
		global $classDb;
		global $varsAccount;
		global $varsPluginAccountingEntity;
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
		$varsSubAccountTitle = $this->_getVarsSubAccountTitle(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$data = array(
			'varsEntityNation'       => $varsEntityNation,
			'varsFiscalPeriod'       => $varsFiscalPeriod,
			'varsFiscalPeriodMonth'  => $varsFiscalPeriodMonth,
			'varsAccountTitle'       => $varsAccountTitle,
			'varsSubAccountTitle'    => $varsSubAccountTitle,
			'varsDepartment'         => $varsDepartment,
			'varsDepartmentTreeItem' => $varsDepartmentTreeItem,
			'numFiscalPeriod'        => $arr['numFiscalPeriod'],
		);

		return $data;

	}



	/**
		(array(
			'arrRows'      => $arr['arrRows'],
			'varsItem'     => $varsItem,
			'flagDelete'   => ($arr['flagDelete'])? 1 : 0,
			'flagTempNext' => ($arr['flagTempNext'])? 1 : 0,
		))
	 */
	protected function _setVarsValue($arr)
	{
		$arrayLog = $arr['arrRows'];
		foreach ($arrayLog as $keyLog => $valueLog) {
			$idAccountTitle = $valueLog['idAccountTitle'];
			$arrayFS = $this->_extendSelf['arrFS'];
			$flagFS = '';
			foreach ($arrayFS as $keyFS => $valueFS) {
				if ($valueFS == 'CR') {
					if (!(int) $arr['varsItem']['varsEntityNation']['flagCR']) {
						continue;
					}
				}
				if ($arr['varsItem']['varsAccountTitle'][$valueFS][$idAccountTitle]) {
					$flagFS = $valueFS;
				}
			}
			if (!$flagFS) {
				continue;
			}

			$idSubAccountTitle = $valueLog['idSubAccountTitle'];
			if (!$arr['varsItem']['varsSubAccountTitle']['arrStrTitle'][$idAccountTitle][$idSubAccountTitle]) {
				continue;
			}

			$varsValue = $this->_getVarsSubAccountTitleValue(array(
				'idSubAccountTitle' => $idSubAccountTitle,
				'numFiscalPeriod'   => $arr['varsItem']['numFiscalPeriod'],
			));

			$arrRows = array($valueLog);
			if (!$varsValue['jsonData']) {
				$varsValue['jsonData'] = array();
			}

			if ($arr['flagBalance'] && $arr['flagBalance'] == 'department') {
				$idDepartment = $valueLog['idDepartment'];
				$array = $arr['varsItem']['varsDepartment'];
				foreach ($array as $key => $value) {
					if ($idDepartment != $key) {
						continue;
					}
					$varsValue['jsonData'][$idDepartment] = $this->_loopVarsValue(array(
						'arrRows'      => $arrRows,
						'flagFS'       => $flagFS,
						'varsValue'    => $varsValue['jsonData'][$idDepartment],
						'varsItem'     => $arr['varsItem'],
						'flagDelete'   => $arr['flagDelete'],
						'flagTempNext' => $arr['flagTempNext'],
					));
				}

			} else {
				$varsValue['jsonData']['all'] = $this->_loopVarsValue(array(
					'arrRows'      => $arrRows,
					'flagFS'       => $flagFS,
					'varsValue'    => $varsValue['jsonData']['all'],
					'varsItem'     => $arr['varsItem'],
					'flagDelete'   => $arr['flagDelete'],
					'flagTempNext' => $arr['flagTempNext'],
				));
				$idDepartment = $valueLog['idDepartment'];
				$array = $arr['varsItem']['varsDepartment'];
				foreach ($array as $key => $value) {
					if ($idDepartment != $key) {
						continue;
					}
					$varsValue['jsonData'][$idDepartment] = $this->_loopVarsValue(array(
						'arrRows'      => $arrRows,
						'flagFS'       => $flagFS,
						'varsValue'    => $varsValue['jsonData'][$idDepartment],
						'varsItem'     => $arr['varsItem'],
						'flagDelete'   => $arr['flagDelete'],
						'flagTempNext' => $arr['flagTempNext'],
					));
				}
			}

			$flag = $this->_updateDb(array(
				'varsValue' => $varsValue,
				'varsItem'  => $arr['varsItem'],
			));
			if ($flag == 'errorDataMax') {
				return $flag;
			}
		}
	}

	/**
		(array(
			'idSubAccountTitle'    => $key,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))

	 */
	protected function _getVarsSubAccountTitleValue($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingSubAccountTitleValue' . $strNation,
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
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idSubAccountTitle',
					'flagCondition' => 'eq',
					'value'         => $arr['idSubAccountTitle'],
				),
			),
		));

		return $rows['arrRows'][0];
	}

	/**
		(array(
				'varsValue'         => $varsValue,
				'varsItem'          => $arr['varsItem'],
		))
	 */
	protected function _updateDb($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$jsonData = json_encode($arr['varsValue']['jsonData']);
		$flag = $this->checkTextSize(array(
			'flagReturn' => 1,
			'str'        => $jsonData,
		));
		if ($flag) {
			return 'errorDataMax';
		}

		$classDb->updateRow(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingSubAccountTitleValue' . $strNation,
			'arrColumn' => array('jsonData'),
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
					'strColumn'     => 'id',
					'flagCondition' => 'eq',
					'value'         => $arr['varsValue']['id'],
				),
			),
			'arrValue'  => array($jsonData),
		));
	}

	/**
		(array(
			'arrRows'      => $arrRows,
			'flagFS'       => $flagFS,
			'varsValue'    => $varsValue['jsonData'][$idDepartment],
			'varsItem'     => $arr['varsItem'],
			'flagDelete'   => $arr['flagDelete'],
			'flagTempNext' => $arr['flagTempNext'],
		))
	 */
	protected function _loopVarsValue($arr)
	{
		$arr['varsValue'] = $this->_getValueFS(array(
			'flagFS'       => $arr['flagFS'],
			'flagDelete'   => $arr['flagDelete'],
			'flagTempNext' => $arr['flagTempNext'],
			'arrRows'      => $arr['arrRows'],
			'varsItem'     => $arr['varsItem'],
			'varsValue'    => $arr['varsValue'],
		));

		return $arr['varsValue'];
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

		$this->_setNextVarsValue(array(
			'varsItem'     => $varsItem,
			'varsItemNext' => $varsItemNext,
		));

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);
		$this->_updateDbPreferenceStamp(array('strColumn' => 'subAccountTitleValue'));
	}

	/**
		(array(
			'varsItem'     => $varsItem,
			'varsItemNext' => $varsItemNext,
		))
	 */
	protected function _setNextVarsValue($arr)
	{
		$rows = $this->_getVarsSubAccountTitleValueAll(array(
			'numFiscalPeriod' => $arr['varsItem']['numFiscalPeriod'],
		));
		if (!$rows['numRows']) {
			return;
		}

		$array = $rows['arrRows'];
		foreach ($array as $key => $value) {
			$varsValueNext = $this->_loopVarsValueNext(array(
				'varsValue'    => $value,
				'varsItem'     => $arr['varsItem'],
				'varsItemNext' => $arr['varsItemNext'],
			));

			$this->_insertDb(array(
				'varsValue'     => $varsValueNext,
			));
		}
	}

	/**
		(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))

	 */
	protected function _getVarsSubAccountTitleValueAll($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingSubAccountTitleValue' . $strNation,
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

		return $rows;
	}

	/**
		(array(
			'idSubAccountTitle' => $idSubAccountTitle,
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));
	 */
	protected function _getVarsSubAccountTitleData($arr)
	{
		global $classDb;
		global $varsAccount;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingSubAccountTitle' . $strNation,
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
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idSubAccountTitle',
					'flagCondition' => 'eq',
					'value'         => $arr['idSubAccountTitle'],
				),
			),
		));

		return $rows['arrRows'][0];
	}


	/**
		(array(
			'varsValue'  => $varsValue,
			'varsItem'     => $arr['varsItem'],
			'varsItemNext' => $arr['varsItemNext'],
		))
	 */
	protected function _loopVarsValueNext($arr)
	{
		global $varsPluginAccountingAccount;

		$varsValueNext = array();
		$array = $arr['varsValue'];
		foreach ($array as $key => $value) {
			if ($key == 'id') {
				continue;

			} elseif ($key == 'stampRegister') {
				$varsValueNext[$key] = $value;

			} elseif ($key == 'stampUpdate') {
				$varsValueNext[$key] = TIMESTAMP;

			} elseif ($key == 'numFiscalPeriod') {
				$varsValueNext[$key] = $arr['varsItemNext']['numFiscalPeriod'];

			} elseif ($key == 'idEntity') {
				$varsValueNext[$key] = $value;

			} elseif ($key == 'idSubAccountTitle') {
				$varsValueNext[$key] = $value;

			} elseif ($key == 'jsonData') {
				$varsValueNext[$key] = array();
			}
		}

		$varsSubAccountTitle = $this->_getVarsSubAccountTitleData(array(
			'idSubAccountTitle' => $arr['varsValue']['idSubAccountTitle'],
			'numFiscalPeriod'   => $arr['varsItem']['numFiscalPeriod'],
		));

		$idAccountTitle = $varsSubAccountTitle['idAccountTitle'];
		$arrayFS = $this->_extendSelf['arrFS'];
		$flagFS = '';
		foreach ($arrayFS as $keyFS => $valueFS) {
			if ($valueFS == 'CR') {
				if (!(int) $arr['varsItem']['varsEntityNation']['flagCR']) {
					continue;
				}
			}
			if ($arr['varsItem']['varsAccountTitle'][$valueFS][$idAccountTitle]) {
				$flagFS = $valueFS;
			}
		}

		if ($flagFS == 'BS') {
			$array = $arr['varsItemNext']['varsFiscalPeriod'];
			foreach ($array as $key => $value) {
				$varsValueNext['jsonData']['all'][$key] = $this->_loopVarsNext(array(
					'varsValue' => $arr['varsValue']['jsonData']['all']['f1'],
				));
				$arrayDepartment = $arr['varsItem']['varsDepartment'];
				foreach ($arrayDepartment as $keyDepartment => $valueDepartment) {
					$varsValueNext['jsonData'][$keyDepartment][$key] = $this->_loopVarsNext(array(
						'varsValue' => $arr['varsValue']['jsonData'][$keyDepartment]['f1'],
					));
				}
			}

			$json =  json_encode($varsValueNext['jsonData']);
			$flag = $this->checkTextSize(array(
				'flagReturn' => 1,
				'str'        => $json,
			));
			if ($flag) {
				return 'errorDataMax';
			}
			$varsValueNext['jsonData'] = $json;

		} else {
			$varsValueNext['jsonData'] = '';
		}

		return $varsValueNext;
	}

	/**
		(array(
			'varsValue'     => $arr['varsValue']['jsonData']['all']['f1'],
			'varsValueNext' => $varsValueNext['jsonData']['all'][$value],
		));
	 */
	protected function _loopVarsNext($arr)
	{
		$sumPrev =  $arr['varsValue']['sumNext'];
		if (is_null($sumPrev)) {
			$sumPrev = 0;
		}
		$sumNext = $sumPrev;
		$dataTmpl = array(
			'sumPrev'   => $sumPrev,
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

		return $dataTmpl;
	}

	/**
		(array(
			'varsValue'     => $varsValueNext,
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

		$classDb->insertRow(array(
			'idModule'  => 'accounting',
			'strTable' => 'accountingSubAccountTitleValue' . $strNation,
			'arrColumn' => $arrDbColumn,
			'arrValue'  => $arrDbValue,
		));
	}

	/**
		(array(
			'flagStatus'    => 'edit',
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'arrRowsAdd'    => $arrRowsAdd,
			'arrRowsDelete' => $arrRowsDelete,
			'flagTempNext'    => 1,
			'flagBalance'  => ($arr['flagBalance'])? $arr['flagBalance'] : '',
		))
	 */
	protected function _iniEdit($arr)
	{
		$flag = $this->_iniDelete(array(
			'arrRows'         => $arr['arrRowsDelete'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'flagTempNext'    => ($arr['flagTempNext'])? 1 : 0,
			'flagBalance'     => ($arr['flagBalance'])? $arr['flagBalance'] : '',
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}
		$flag = $this->_iniAdd(array(
			'arrRows'         => $arr['arrRowsAdd'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'flagTempNext'    => ($arr['flagTempNext'])? 1 : 0,
			'flagBalance'     => ($arr['flagBalance'])? $arr['flagBalance'] : '',
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}
	}

	/**
		(array(
			'flagStatus'      => 'delete',
			'arrRows'         => $arrRows,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'flagTempNext'    => 1,
			'flagBalance'  => ($arr['flagBalance'])? $arr['flagBalance'] : '',
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
			'flagDelete'      => 1,
			'flagTempNext'    => ($arr['flagTempNext'])? 1 : 0,
			'flagBalance'     => ($arr['flagBalance'])? $arr['flagBalance'] : '',
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

	protected function _getVarsDepartment($arr)
	{
		global $classDb;
		global $varsPluginAccountingEntity;
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
			'flagFS'       => $arr['flagFS'],
			'flagDelete'   => $arr['flagDelete'],
			'flagTempNext' => $arr['flagTempNext'],
			'arrRows'      => $arr['arrRows'],
			'varsItem'     => $arr['varsItem'],
			'varsValue'    => $arr['varsValue'],
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
		);
		$dataTmpl['varsAdjust'] = $dataTmpl;
		$dataTmpl['varsAdjust2'] = $dataTmpl;

		$array = $arr['varsItem']['varsFiscalPeriodMonth'];
		$arrayRows = &$arr['arrRows'];
		foreach ($arrayRows as $keyRows => $valueRows) {
			$flag = 0;
			$flagSumDebit = 0;
			$arrayflagSumNext = array();
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
					if (is_null($varsValue['f1'])) {
						$varsValue['f1'] = $dataTmpl;
					}

					//month
					if (is_null($varsValue[$numMonth])) {
						$varsValue[$numMonth] = $dataTmpl;
					}

					//f2
					if ($varsFiscalPeriod['f21']) {
						$arrayReport = array('f21', 'f22');
						foreach ($arrayReport as $keyReport => $valueReport) {
							if (is_null($varsValue[$valueReport])) {
								$varsValue[$valueReport] = $dataTmpl;
							}
						}
					}

					//f4
					if ($varsFiscalPeriod['f41']) {
						$arrayReport = array('f41', 'f42', 'f43', 'f44');
						foreach ($arrayReport as $keyReport => $valueReport) {
							if (is_null($varsValue[$valueReport])) {
								$varsValue[$valueReport] = $dataTmpl;
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
								$varsValue['f1'][$str] -= $valueRows['numValue'];
								if ($valueRows['flagFiscalReport']) {
									$varsValue['f1'][$flagFiscalReport][$str] -= $valueRows['numValue'];
								}

							} else {
								$varsValue['f1'][$str] += $valueRows['numValue'];
								if ($valueRows['flagFiscalReport']) {
									$varsValue['f1'][$flagFiscalReport][$str] += $valueRows['numValue'];
								}
							}

							//month
							if ($valueRows['stampBook'] >= $varsFiscalPeriod[$numMonth]['stampMin']
								&& $valueRows['stampBook'] <= $varsFiscalPeriod[$numMonth]['stampMax']
							) {
								if ($arr['flagDelete']) {
									$varsValue[$numMonth][$str] -= $valueRows['numValue'];
									if ($valueRows['flagFiscalReport']) {
										$varsValue[$numMonth][$flagFiscalReport][$str] -= $valueRows['numValue'];
									}

								} else {
									$varsValue[$numMonth][$str] += $valueRows['numValue'];
									if ($valueRows['flagFiscalReport']) {
										$varsValue[$numMonth][$flagFiscalReport][$str] += $valueRows['numValue'];
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
											$varsValue[$valueReport][$str] -= $valueRows['numValue'];
											if ($valueRows['flagFiscalReport']) {
												$varsValue[$valueReport][$flagFiscalReport][$str] -= $valueRows['numValue'];
											}

										} else {
											$varsValue[$valueReport][$str] += $valueRows['numValue'];
											if ($valueRows['flagFiscalReport']) {
												$varsValue[$valueReport][$flagFiscalReport][$str] += $valueRows['numValue'];
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
											$varsValue[$valueReport][$str] -= $valueRows['numValue'];
											if ($valueRows['flagFiscalReport']) {
												$varsValue[$valueReport][$flagFiscalReport][$str] -= $valueRows['numValue'];
											}

										} else {
											$varsValue[$valueReport][$str] += $valueRows['numValue'];
											if ($valueRows['flagFiscalReport']) {
												$varsValue[$valueReport][$flagFiscalReport][$str] += $valueRows['numValue'];
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
						$varsValue['f1']['sumNext'] += $numValue;
						if ($valueRows['flagFiscalReport']) {
							$varsValue['f1'][$flagFiscalReport]['sumNext'] += $numValue;
						}
						//sumPrev
						$varsValue['f1']['sumPrev'] = $this->_getSumPrev(array(
							'flagDebit' => $valueRows['flagDebitAccountTitle'],
							'varsValue' => $varsValue['f1'],
						));
						if ($valueRows['flagFiscalReport']) {
							$varsValue['f1'][$flagFiscalReport]['sumPrev'] = $this->_getSumPrev(array(
								'flagDebit' => $valueRows['flagDebitAccountTitle'],
								'varsValue' => $varsValue['f1'][$flagFiscalReport],
							));
						}
						$arrayflagSumNext['f1'] = 1;
					}

					if ($arr['flagFS'] == 'BS') {
						//month
						if ($valueRows['stampBook'] <= $varsFiscalPeriod[$numMonth]['stampMax']) {
							$varsValue[$numMonth]['sumNext'] += $numValue;
							if ($valueRows['flagFiscalReport']) {
								$varsValue[$numMonth][$flagFiscalReport]['sumNext'] += $numValue;
							}
							//sumPrev
							$varsValue[$numMonth]['sumPrev'] = $this->_getSumPrev(array(
								'flagDebit' => $valueRows['flagDebitAccountTitle'],
								'varsValue' => $varsValue[$numMonth],
							));
							if ($valueRows['flagFiscalReport']) {
								$varsValue[$numMonth][$flagFiscalReport]['sumPrev'] = $this->_getSumPrev(array(
									'flagDebit' => $valueRows['flagDebitAccountTitle'],
									'varsValue' => $varsValue[$numMonth][$flagFiscalReport],
								));
							}
						}

						//f2
						if ($varsFiscalPeriod['f21']) {
							$arrayReport = array('f21', 'f22');
							foreach ($arrayReport as $keyReport => $valueReport) {
								if (!$arrayflagSumNext[$valueReport]) {
									if ($valueRows['stampBook']  <= $varsFiscalPeriod[$valueReport]['stampMax']) {
										$varsValue[$valueReport]['sumNext'] += $numValue;
										if ($valueRows['flagFiscalReport']) {
											$varsValue[$valueReport][$flagFiscalReport]['sumNext'] += $numValue;
										}
										//sumPrev
										$varsValue[$valueReport]['sumPrev'] = $this->_getSumPrev(array(
											'flagDebit' => $valueRows['flagDebitAccountTitle'],
											'varsValue' => $varsValue[$valueReport],
										));
										if ($valueRows['flagFiscalReport']) {
											$varsValue[$valueReport][$flagFiscalReport]['sumPrev'] = $this->_getSumPrev(array(
												'flagDebit' => $valueRows['flagDebitAccountTitle'],
												'varsValue' => $varsValue[$valueReport][$flagFiscalReport],
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
										$varsValue[$valueReport]['sumNext'] += $numValue;
										if ($valueRows['flagFiscalReport']) {
											$varsValue[$valueReport][$flagFiscalReport]['sumNext'] += $numValue;
										}
										//sumPrev
										$varsValue[$valueReport]['sumPrev'] = $this->_getSumPrev(array(
											'flagDebit' => $valueRows['flagDebitAccountTitle'],
											'varsValue' => $varsValue[$valueReport],
										));
										if ($valueRows['flagFiscalReport']) {
											$varsValue[$valueReport][$flagFiscalReport]['sumPrev'] = $this->_getSumPrev(array(
												'flagDebit' => $valueRows['flagDebitAccountTitle'],
												'varsValue' => $varsValue[$valueReport][$flagFiscalReport],
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
							$varsValue[$numMonth]['sumNext'] += $numValue;
							if ($valueRows['flagFiscalReport']) {
								$varsValue[$numMonth][$flagFiscalReport]['sumNext'] += $numValue;
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
										$varsValue[$valueReport]['sumNext'] += $numValue;
										if ($valueRows['flagFiscalReport']) {
											$varsValue[$valueReport][$flagFiscalReport]['sumNext'] += $numValue;
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
										$varsValue[$valueReport]['sumNext'] += $numValue;
										if ($valueRows['flagFiscalReport']) {
											$varsValue[$valueReport][$flagFiscalReport]['sumNext'] += $numValue;
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
		'flagDebit' => $valueRows['flagDebitAccountTitle'],
		'varsValue' => $varsValue[$valueReport]['varsAdjust'],
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

}
