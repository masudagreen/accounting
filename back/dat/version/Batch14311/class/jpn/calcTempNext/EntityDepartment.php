<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcTempNext_EntityDepartmentBatch14311 extends Code_Else_Plugin_Accounting_Jpn_JpnBatch14311
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
			'idTarget'        => $idDepartment,
			'numFiscalPeriod' => $numFiscalPeriod,
			'arrValue' => array(
				'strTitle'       => $arrValue['arr']['strTitle'],
				'arrSpaceStrTag' => $arrSpaceStrTag,
			),
		))
	 */
	protected function _iniAdd($arr)
	{
		$flag = $this->_checkAddValue(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'arrValue'        => $arr['arrValue'],
		));
		if ($flag) {
			return $flag;
		}
		$flag = $this->_setAddDb(array(
			'idTarget'        => $arr['idTarget'],
			'arrValue'        => $arr['arrValue'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		if ($flag) {
			return $flag;
		}
	}

	/**
	 (array(
		'varsItem'        => $varsItem,
		'numFiscalPeriod' => $arr['numFiscalPeriod'],
		'arrValue'        => $arr['arrValue'],
	 ))
	 */
	protected function _checkAddValue($arr)
	{
		//
		$flag = $this->_checkStrTitle(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'strTitle'        => $arr['arrValue']['strTitle'],
		));
		if ($flag) {
			return 'strTitleTempNext';
		}
	}

	/**
		$this->_checkStrTitle(array(
			'strTitle' => '',
			'idTarget' => 0,
		));
	 */
	protected function _checkStrTitle($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $varsPluginAccountingAccount;

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
			array(
				'flagType'      => '',
				'strColumn'     => 'strTitle',
				'flagCondition' => 'eq',
				'value'         => $arr['strTitle'],
			),
		);

		if ($arr['idTarget']) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idDepartment',
				'flagCondition' => 'ne',
				'value'         => $arr['idTarget'],
			);
		}

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingEntityDepartment',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => $arrWhere,
		));
		if ($rows['numRows']) {
			return 1;
		}
		return 0;
	}

	/**
		(array(
			'idTarget'        => $arr['idTarget'],
			'arrValue'        => $arr['arrValue'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _setAddDb($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$stampRegister = TIMESTAMP;
		$stampUpdate = TIMESTAMP;

		$strTitle = $arr['arrValue']['strTitle'];
		$arrSpaceStrTag = $arr['arrValue']['arrSpaceStrTag'];

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $arr['numFiscalPeriod'];

		$idDepartment = $arr['idTarget'];

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
	}


	/**
		 (array(
			 'flagStatus'      => 'edit',
			 'idTarget'        => $idDepartment,
			 'numFiscalPeriod' => $numFiscalPeriod,
			 'arrValue' => array(
				 'strTitle'       => $arrValue['arr']['strTitle'],
				 'idAccountTitle' => $arrValue['arr']['idAccountTitle'],
				 'arrSpaceStrTag' => $arrSpaceStrTag,
			),
		))
	 */
	protected function _iniEdit($arr)
	{
		$flag = $this->_checkEditValue(array(
			'idTarget'        => $arr['idTarget'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'arrValue'        => &$arr['arrValue'],
		));
		if ($flag) {
			if ($flag == 'none') {
				return;
			}
			return $flag;
		}

		$flag = $this->_setEditDb(array(
			'idTarget'        => $arr['idTarget'],
			'arrValue'        => $arr['arrValue'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		if ($flag) {
			return $flag;
		}
	}

	/**
		(array(
			'varsItem'        => $varsItem,
			'idTarget'        => $arr['idTarget'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'arrValue'        => $arr['arrValue'],
		))
	 */
	protected function _checkEditValue($arr)
	{
		$varsTarget = $this->_getVarsTarget(array(
			'idTarget'        => $arr['idTarget'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		if (!$varsTarget) {
			return 'none';
		}

		$flag = $this->_checkStrTitle(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'strTitle'        => $arr['arrValue']['strTitle'],
			'idTarget'        => $arr['idTarget'],
		));
		if ($flag) {
			return 'strTitleTempNext';
		}
	}

	/**
	 *
	 */
	protected function _getVarsTarget($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $varsPluginAccountingAccount;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingEntityDepartment',
			'arrLimit' => array(
				'numStart' => 0, 'numEnd' => 1,
			),
			'arrOrder' => array(),
			'flagAnd'  => 1,
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
					'value'         => $arr['numFiscalPeriod'],
				),
				array(
					'flagType'      => '',
					'strColumn'     => 'idDepartment',
					'flagCondition' => 'eq',
					'value'         => $arr['idTarget'],
				),
			),
		));

		$data = $rows['arrRows'][0];

		if (!$data) {
			$data = array();
		}

		return $data;
	}

	/**
		(array(
			'varsItem'        => $varsItem,
			'idTarget'        => $arr['idTarget'],
			'arrValue'        => $arr['arrValue'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _setEditDb($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strTitle = $arr['arrValue']['strTitle'];
		$arrSpaceStrTag = $arr['arrValue']['arrSpaceStrTag'];

		$arrDbColumn = array('strTitle', 'arrSpaceStrTag');
		$arrDbValue = array($strTitle, $arrSpaceStrTag);

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingEntityDepartment',
			'arrColumn' => $arrDbColumn,
			'flagAnd'  => 1,
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
					'value'         => $arr['numFiscalPeriod'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idDepartment',
					'flagCondition' => 'eq',
					'value'         => $arr['idTarget'],
				),
			),
			'arrValue'  => $arrDbValue,
		));
	}

	/**
		(array(
			'flagStatus'      => 'delete',
			'numFiscalPeriod' => $numFiscalPeriod,
			'idTarget'        => $idDepartment,
		))
	 */
	protected function _iniDelete($arr)
	{

		$flag = $this->_checkDeleteValue(array(
			'idTarget'        => $arr['idTarget'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		if ($flag) {
			return;
		}

		$this->_setDeleteDb(array(
			'idTarget'        => $arr['idTarget'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
	}

	/**
		(array(
			'idTarget'        => $arr['idTarget'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _checkDeleteValue($arr)
	{
		$varsTarget = $this->_getVarsTarget(array(
			'idTarget'        => $arr['idTarget'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		if (!$varsTarget) {
			return __LINE__;
		}

		$flagUseLog = $this->_checkUseLog(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idTarget'        => $arr['idTarget'],
		));
		if ($flagUseLog) {
			return __LINE__;
		}
	}

	/**
	 *
	 */
	protected function _checkUseLog($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $varsPluginAccountingAccount;

		$strId = ',' . $arr['idTarget'] . ',';

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLog',
			'arrLimit' => array(
				'numStart' => 0, 'numEnd' => 1,
			),
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
					'flagType'      => '',
					'strColumn'     => 'arrCommaIdDepartmentVersion',
					'flagCondition' => 'like',
					'value'         => $strId,
				),
			),
		));

		if ($rows['numRows']) {
			return 1;
		}

		return 0;
	}

	/**
		(array(
			'idTarget'        => $arr['idTarget'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _setDeleteDb($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$classDb->deleteRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingEntityDepartment',
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
					'value'         => $arr['numFiscalPeriod'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idDepartment',
					'flagCondition' => 'eq',
					'value'         => $arr['idTarget'],
				),
			),
		));

		$classDb->deleteRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingEntityDepartmentFSValue' . $strNation,
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
					'value'         => $arr['numFiscalPeriod'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idDepartment',
					'flagCondition' => 'eq',
					'value'         => $arr['idTarget'],
				),
			),
		));
	}

	/**
		(array(
			'flagStatus'      => 'back',
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idTarget'        => $key,
			'flagReverse'    => 1,
		))
	 */
	protected function _iniBack($arr)
	{
		$numFiscalPeriodPrev = $arr['numFiscalPeriod'] - 1;
		if ($arr['flagReverse']) {
			$numFiscalPeriodPrev = $arr['numFiscalPeriod'];
			$arr['numFiscalPeriod'] = $numFiscalPeriodPrev - 1;
		}

		$varsTarget = $this->_getVarsTarget(array(
			'idTarget'        => $arr['idTarget'],
			'numFiscalPeriod' => $numFiscalPeriodPrev,
		));

		$this->_setBackDb(array(
			'varsTarget'      => $varsTarget,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
	}

	/**
		(array(
			'varsTarget'      => $varsTarget,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _setBackDb($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$stampRegister = TIMESTAMP;
		$stampUpdate = TIMESTAMP;

		$strTitle = $arr['varsTarget']['strTitle'];
		$arrSpaceStrTag = $arr['varsTarget']['arrSpaceStrTag'];

		$idEntity = $arr['varsTarget']['idEntity'];
		$numFiscalPeriod = $arr['numFiscalPeriod'];

		$idDepartment = $arr['varsTarget']['idDepartment'];

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
	}
}
