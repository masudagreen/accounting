<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcSubAccountTitleImport extends Code_Else_Plugin_Accounting_Jpn_Jpn
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
			'flagStatus'       => 'add',
			'numFiscalPeriod'  => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'         => $varsPluginAccountingAccount['idEntityCurrent'],
			'flagTempPrev'     => (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow))? 1 : 0,
			'arrValue' => array(
				'strTitle'       => $keyData,
				'idAccountTitle' => $idAccountTitle,
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
 		'flagStatus'       => 'add',
 		'numFiscalPeriod'  => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
 		'idEntity'         => $varsPluginAccountingAccount['idEntityCurrent'],
 		'flagTempPrev'     => (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow))? 1 : 0,
 		'arrValue' => array(
 				'strTitle'       => $keyData,
 				'idAccountTitle' => $idAccountTitle,
 				'arrSpaceStrTag' => '',
 		),

	 * idAccountTitleexist、strTitle checked
	 */
	protected function _iniAdd($arr)
	{
		global $classDb;
		global $classEscape;
		global  $varsPluginAccountingEntity;

		$dbh = $classDb->getHandle();

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$varsAccountTitle = array();
		$varsItemTemp = array();
		if ($arr['flagTempPrev']) {
			$numFiscalPeriodNext = $arr['numFiscalPeriod'] + 1;
			$varsItemTemp = $this->_getVarsItemTemp(array(
				'numFiscalPeriodPrev' => $arr['numFiscalPeriod'],
				'numFiscalPeriodNext' => $numFiscalPeriodNext,
			));
			$varsAccountTitle = $varsItemTemp['arrAccountTitlePrev']['arrStrTitle'][$arr['arrValue']['idAccountTitle']];
		}

		$stampRegister = TIMESTAMP;
		$stampUpdate = TIMESTAMP;
		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arr['arrValue']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));
		$strTitle = $arr['arrValue']['strTitle'];
		$idAccountTitle = $arr['arrValue']['idAccountTitle'];
		$idEntity = $arr['idEntity'];
		$numFiscalPeriod = $arr['numFiscalPeriod'];

		$varsIdNumber = $this->_getIdAutoIncrement(array(
			'idTarget' => 'idSubAccountTitle'
		));
		if (!$varsIdNumber[$idEntity]) {
			$varsIdNumber[$idEntity] = 1;
		}
		$idSubAccountTitle = $varsIdNumber[$idEntity];

		$arrDbColumn = array('stampRegister', 'stampUpdate', 'idSubAccountTitle', 'idEntity', 'numFiscalPeriod', 'strTitle', 'idAccountTitle', 'arrSpaceStrTag');
		$arrDbValue = array($stampRegister, $stampUpdate, $idSubAccountTitle, $idEntity, $numFiscalPeriod, $strTitle, $idAccountTitle, $arrSpaceStrTag);

		$classDb->insertRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingSubAccountTitle' . $strNation,
			'arrColumn' => $arrDbColumn,
			'arrValue'  => $arrDbValue,
		));

		$arrDbColumn = array('stampRegister', 'stampUpdate', 'idEntity', 'idSubAccountTitle', 'numFiscalPeriod');
		$arrDbValue = array($stampRegister, $stampUpdate, $idEntity, $idSubAccountTitle, $numFiscalPeriod);

		$classDb->insertRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingSubAccountTitleValue' . $strNation,
			'arrColumn' => $arrDbColumn,
			'arrValue'  => $arrDbValue,
		));

		$varsIdNumber[$idEntity]++;
		$this->_updateIdAutoIncrement(array(
			'idTarget'   => 'idSubAccountTitle',
			'varsTarget' => $varsIdNumber
		));

		$array = array('subAccountTitle', 'subAccountTitleValue');
		foreach ($array as $key => $value) {
			$this->_updateDbPreferenceStamp(array('strColumn' => $value));
		}

		if ($arr['flagTempPrev']) {
			$classCalcTempNextSubAccountTitle = $this->_getClassCalc(array(
				'flagType'   => 'TempNext',
				'flagDetail' => 'SubAccountTitle',
			));
			$idEntity = $arr['idEntity'];
			$numFiscalPeriod = $arr['numFiscalPeriod'] + 1;

			$flag = $classCalcTempNextSubAccountTitle->allot(array(
				'flagStatus'       => 'add',
				'idTarget'         => $idSubAccountTitle,
				'numFiscalPeriod'  => $numFiscalPeriod,
				'varsAccountTitle' => $varsAccountTitle,
				'arrValue' => array(
					'strTitle'       => $arr['arrValue']['strTitle'],
					'idAccountTitle' => $arr['arrValue']['idAccountTitle'],
					'arrSpaceStrTag' => $arrSpaceStrTag,
				),
			));
			if ($flag) {
				return $flag;
			}
		}
	}

	/**

	 */
	protected function _getVarsItemTemp($arr)
	{
		$arrAccountTitlePrev = $this->_extChildSelf['arrAccountTitlePrev'][$arr['numFiscalPeriodPrev']];
		if (is_null($arrAccountTitlePrev)) {
			$arrAccountTitlePrev = $this->_getAccountTitle(array(
				'arrSubAccountTitle' => array(),
				'numFiscalPeriod'    => $arr['numFiscalPeriodPrev'],
			));
		}

		$data = array(
			'arrAccountTitlePrev' => $arrAccountTitlePrev,
		);

		return $data;
	}

	/**
		(array(
			'flagStatus'      => 'check',
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			'strTitle'        => $keyData,
			'idAccountTitle'  => $idAccountTitle,
		))
	 */
	protected function _iniCheck($arr)
	{
		global $classDb;

		//numFiscalPeriod not need
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
			array(
				'flagType'      => '',
				'strColumn'     => 'idAccountTitle',
				'flagCondition' => 'eq',
				'value'         => $arr['idAccountTitle'],
			),
		);

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);
		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingSubAccountTitle' . $strNation,
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
