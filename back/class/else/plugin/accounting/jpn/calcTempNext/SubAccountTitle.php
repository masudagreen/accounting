<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcTempNext_SubAccountTitle extends Code_Else_Plugin_Accounting_Jpn_Jpn
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
			'idTarget'        => $idSubAccountTitle,
			'numFiscalPeriod' => $numFiscalPeriod,
			'varsAccountTitle' => $varsAccountTitle,
			'arrValue' => array(
				'strTitle'       => $arrValue['arr']['strTitle'],
				'idAccountTitle' => $arrValue['arr']['idAccountTitle'],
				'arrSpaceStrTag' => $arrSpaceStrTag,
			),
		))
	 */
	protected function _iniAdd($arr)
	{
		$varsItem = $this->_getVarsItem(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$flag = $this->_checkAddValue(array(
			'varsItem'         => $varsItem,
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'arrValue'         => $arr['arrValue'],
			'varsAccountTitle' => $arr['varsAccountTitle'],
		));
		if ($flag) {
			return $flag;
		}

		$flag = $this->_setAddDb(array(
			'varsItem'        => $varsItem,
			'idTarget'        => $arr['idTarget'],
			'arrValue'        => $arr['arrValue'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		if ($flag) {
			return $flag;
		}
	}

	/**

	 */
	protected function _getVarsItem($arr)
	{
		$arrAccountTitle = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => array(),
			'numFiscalPeriod'    => $arr['numFiscalPeriod'],
		));

		$data = array(
			'arrAccountTitle' => $arrAccountTitle,
		);

		return $data;
	}

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

		$varsJgaapFSCS = $this->_getVarsItemJgaapFSCS(array(
			'varsFS' => $varsFS,
		));

		$data = array(
			'varsJgaapFS'   => $varsJgaapFS,
			'varsJgaapFSCS' => $varsJgaapFSCS,
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



	/**
	 (array(
		'varsItem'         => $varsItem,
		'numFiscalPeriod'  => $arr['numFiscalPeriod'],
		'arrValue'         => $arr['arrValue'],
		'varsAccountTitle' => $arr['varsAccountTitle'],
	 ))
	 */
	protected function _checkAddValue($arr)
	{
		$flag = $this->_checkStrTitle(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'strTitle'        => $arr['arrValue']['strTitle'],
			'idAccountTitle'  => $arr['arrValue']['idAccountTitle'],
		));
		if ($flag) {
			return 'strTitleTempNext';
		}

		$idAccountTitle = $arr['arrValue']['idAccountTitle'];
		$varsTargetFlag = $arr['varsItem']['arrAccountTitle']['arrStrTitle'][$idAccountTitle];

		if (!$varsTargetFlag) {
			$flag = $this->_checkVarsBackData(array(
				'numFiscalPeriod'  => $arr['numFiscalPeriod'],
				'varsAccountTitle' => $arr['varsAccountTitle'],
				'arrValue'         => $arr['arrValue'],
			));
			if ($flag) {
				return $flag;
			}
		}
	}



	/**
		(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'strTitle'        => $arr['arrValue']['strTitle'],
		))
	 */
	protected function _checkStrTitle($arr)
	{
		global $classDb;

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
			array(
				'flagType'      => '',
				'strColumn'     => 'idAccountTitle',
				'flagCondition' => 'eq',
				'value'         => $arr['idAccountTitle'],
			),
		);
		if ($arr['idTarget']) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idSubAccountTitle',
				'flagCondition' => 'ne',
				'value'         => $arr['idTarget'],
			);
		}

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

	/**
		(array(
			'varsItem'        => $varsItem,
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
		$idAccountTitle = $arr['arrValue']['idAccountTitle'];
		$arrSpaceStrTag = $arr['arrValue']['arrSpaceStrTag'];

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $arr['numFiscalPeriod'];

		$idSubAccountTitle = $arr['idTarget'];

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
	}


	/**
		 (array(
			 'flagStatus'      => 'edit',
			 'idTarget'        => $idSubAccountTitle,
			 'numFiscalPeriod' => $numFiscalPeriod,
			 'varsAccountTitle' => $arr['varsAccountTitle'],
			 'arrValue' => array(
				 'strTitle'       => $arrValue['arr']['strTitle'],
				 'idAccountTitle' => $arrValue['arr']['idAccountTitle'],
				 'arrSpaceStrTag' => $arrSpaceStrTag,
			),
		))
	 */
	protected function _iniEdit($arr)
	{
		$varsItem = $this->_getVarsItem(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$flag = $this->_checkEditValue(array(
			'varsItem'         => $varsItem,
			'idTarget'         => $arr['idTarget'],
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'arrValue'         => &$arr['arrValue'],
			'varsAccountTitle' => $arr['varsAccountTitle'],
		));
		if ($flag) {
			if ($flag == 'none') {
				return;
			}
			return $flag;
		}

		$flag = $this->_setEditDb(array(
			'varsItem'        => $varsItem,
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
			'idAccountTitle'  => $arr['arrValue']['idAccountTitle'],
			'idTarget'        => $arr['idTarget'],
		));
		if ($flag) {
			return 'strTitleTempNext';
		}

		if ($arr['arrValue']['idAccountTitle'] != $varsTarget['idAccountTitle']) {
			$idAccountTitle = $arr['arrValue']['idAccountTitle'];
			$varsTargetFlag = $arr['varsItem']['arrAccountTitle']['arrStrTitle'][$idAccountTitle];
			if (!$varsTargetFlag) {
				$flag = $this->_checkVarsBackData(array(
					'numFiscalPeriod'  => $arr['numFiscalPeriod'],
					'varsAccountTitle' => $arr['varsAccountTitle'],
					'arrValue'         => $arr['arrValue'],
				));
				if ($flag) {
					return $flag;
				}
			}
			$flagUseLogAll = $this->_checkUseLog(array(
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
				'idTarget'        => $arr['idTarget'],
				'flagAll'         => 1,
			));
			if ($flagUseLogAll) {
				$arr['arrValue']['idAccountTitle'] = $varsTarget['idAccountTitle'];
			}
		}

		return;
	}

	/**
	 *
	 */
	protected function _getVarsTarget($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingSubAccountTitle' . $strNation,
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
					'strColumn'     => 'idSubAccountTitle',
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
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idTarget'        => $varsRequest['query']['jsonValue']['idTarget'],
			'flagAll'         => 1,
		))
	 */
	protected function _checkUseLog($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;

		$strId = ',' . $arr['idTarget'] . ',';

		$arrWhere = array(
			array(
				'flagType'      => 'num',
				'strColumn'     => 'idEntity',
				'flagCondition' => 'eq',
				'value'         => $varsPluginAccountingAccount['idEntityCurrent'],
			),
			array(
				'flagType'      => '',
				'strColumn'     => 'arrCommaIdSubAccountTitleVersion',
				'flagCondition' => 'like',
				'value'         => $strId,
			),
		);
		if (!$arr['flagAll']) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'numFiscalPeriod',
				'flagCondition' => 'eq',
				'value'         => $arr['numFiscalPeriod'],
			);
		}

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLog',
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

		return 0;
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

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$strTitle = $arr['arrValue']['strTitle'];
		$idAccountTitle = $arr['arrValue']['idAccountTitle'];
		$arrSpaceStrTag = $arr['arrValue']['arrSpaceStrTag'];

		$arrDbColumn = array('strTitle', 'idAccountTitle', 'arrSpaceStrTag');
		$arrDbValue = array($strTitle, $idAccountTitle, $arrSpaceStrTag);

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable' => 'accountingSubAccountTitle' . $strNation,
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
					'flagType'      => '',
					'strColumn'     => 'idSubAccountTitle',
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
			'idTarget'        => $idSubAccountTitle,
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

		$idSubAccountTitle = $arr['idTarget'];

		$classDb->deleteRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingSubAccountTitle' . $strNation,
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
					'strColumn'     => 'idSubAccountTitle',
					'flagCondition' => 'eq',
					'value'         => $idSubAccountTitle,
				),
			),
		));

		$classDb->deleteRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingSubAccountTitleValue' . $strNation,
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
					'strColumn'     => 'idSubAccountTitle',
					'flagCondition' => 'eq',
					'value'         => $idSubAccountTitle,
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
		$idAccountTitle = $arr['varsTarget']['idAccountTitle'];
		$arrSpaceStrTag = $arr['varsTarget']['arrSpaceStrTag'];

		$idEntity = $arr['varsTarget']['idEntity'];
		$numFiscalPeriod = $arr['numFiscalPeriod'];

		$idSubAccountTitle = $arr['varsTarget']['idSubAccountTitle'];

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
	}

}
