<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcEntityDepartmentImport extends Code_Else_Plugin_Accounting_Jpn_Jpn
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
			'flagStatus'      => 'add',
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			'flagTempPrev'    => (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow))? 1 : 0,
			'arrValue'        => array(
				'strTitle'       => $keyData,
				'arrSpaceStrTag' => '',
			),
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

	/*
		(array(
			'flagStatus'      => 'add',
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			'flagTempPrev'    => (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow))? 1 : 0,
			'arrValue'        => array(
				'strTitle'       => $keyData,
				'arrSpaceStrTag' => '',
			),
		))
	 * */
	protected function _iniAdd($arr)
	{
		global $classDb;
		global $classEscape;
		$dbh = $classDb->getHandle();

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$stampRegister = TIMESTAMP;
		$stampUpdate = TIMESTAMP;
		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arr['arrValue']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));
		$strTitle = $arr['arrValue']['strTitle'];
		$idEntity = $arr['idEntity'];
		$numFiscalPeriod = $arr['numFiscalPeriod'];

		$varsIdNumber = $this->_getIdAutoIncrement(array(
			'idTarget' => 'idDepartment'
		));
		if (!$varsIdNumber[$idEntity]) {
			$varsIdNumber[$idEntity] = 1;
		}
		$idDepartment = $varsIdNumber[$idEntity];

		$arrDbColumn = array('stampRegister', 'stampUpdate', 'idDepartment', 'idEntity', 'numFiscalPeriod', 'strTitle', 'arrSpaceStrTag',);
		$arrDbValue = array($stampRegister, $stampUpdate, $idDepartment, $idEntity, $numFiscalPeriod, $strTitle, $arrSpaceStrTag,);

		$classDb->insertRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingEntityDepartment',
			'arrColumn' => $arrDbColumn,
			'arrValue'  => $arrDbValue,
		));

		$arrDbColumn = array('stampRegister', 'stampUpdate', 'idEntity', 'idDepartment', 'numFiscalPeriod');
		$arrDbValue = array($stampRegister, $stampUpdate, $idEntity, $idDepartment, $numFiscalPeriod);

		$classDb->insertRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingEntityDepartmentFSValue' . $strNation,
			'arrColumn' => $arrDbColumn,
			'arrValue'  => $arrDbValue,
		));

		$varsIdNumber[$idEntity]++;
		$this->_updateIdAutoIncrement(array(
			'idTarget'   => 'idDepartment',
			'varsTarget' => $varsIdNumber
		));

		if ($arr['flagTempPrev']) {
			$classCalcTempNextEntityDepartment = $this->_getClassCalc(array(
				'flagType'   => 'TempNext',
				'flagDetail' => 'EntityDepartment',
			));
			$idEntity = $arr['idEntity'];
			$numFiscalPeriod = $arr['numFiscalPeriod'] + 1;

			$flag = $classCalcTempNextEntityDepartment->allot(array(
				'flagStatus'      => 'add',
				'idTarget'        => $idDepartment,
				'numFiscalPeriod' => $numFiscalPeriod,
				'arrValue' => array(
					'strTitle'       => $arr['arrValue']['strTitle'],
					'arrSpaceStrTag' => $arrSpaceStrTag,
				),
			));
			if ($flag) {
				return $flag;
			}
		}

		$this->_updateDbPreferenceStamp(array('strColumn' => 'entityDepartment'));
		$this->_updateDbPreferenceStamp(array('strColumn' => 'entityDepartmentValue'));
	}

	/**
		(array(
			'flagStatus'      => 'check',
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			'strTitle'        => $keyData,
		));
	 */
	protected function _iniCheck($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$arrWhere = array(
			array(
				'flagType'      => 'num',
				'strColumn'     => 'idEntity',
				'flagCondition' => 'eq',
				'value'         => $arr['idEntity'],
			),
			array(
				'flagType'      => '',
				'strColumn'     => 'strTitle',
				'flagCondition' => 'eq',
				'value'         => $arr['strTitle'],
			),
		);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingEntityDepartment',
			'arrLimit' => array(
				'numStart' => 0, 'numEnd' => 1,
			),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => $arrWhere,
		));
		if ($rows['numRows']) {
			return 1;
		}
	}

}
