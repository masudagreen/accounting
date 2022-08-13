<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_SubAccountTitleEditor extends Code_Else_Plugin_Accounting_Jpn_SubAccountTitle
{
	protected $_childSelf = array(
		'pathTplJs' => 'else/plugin/accounting/js/jpn/subAccountTitleEditor.js',
		'pathVarsJs'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/subAccountTitleEditor.php',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;
		if ($varsRequest['query']['func']) {
			$method = '_ini' . $varsRequest['query']['func'];
			if (method_exists($this, $method)) {
				$this->$method();

			} else {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
				}
				exit;
			}
		}
		exit;
	}

	/**
	 *
	 */
	protected function _iniJs()
	{
		$this->_setJsEditor(array(
			'pathVars'  => $this->_childSelf['pathVarsJs'],
			'pathTpl'   => $this->_childSelf['pathTplJs'],
			'arrFolder' => array(),
		));
	}

	/**
	 *
	 */
	protected function _iniDetailAdd()
	{
		global $classDb;
		global $classEscape;

		global $varsRequest;
		$dbh = $classDb->getHandle();
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		if (!$this->_checkCurrent()) {
			$this->_sendOldError();
		}

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'insert',
			'idTarget'      => $this->_extSelf['idPreference'],
		));
		if (!$flag) {
			$this->_sendOldError();
		}

		$varsRequest['query']['jsonValue']['vars']['StrTitle'] = $classEscape->toComma(array(
			'data' => $varsRequest['query']['jsonValue']['vars']['StrTitle']
		));

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$vars = $this->_updateVars(array(
			'vars' => $vars
		));

		$varsTarget = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget
		));

		$this->_checkStrTitle(array(
			'strTitle'       => $arrValue['arr']['strTitle'],
			'idAccountTitle' => $arrValue['arr']['idAccountTitle'],
			'idTarget'       => 0,
		));

		$varsAccountTitle = array();
		$varsItemTemp = array();
		if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
			$numFiscalPeriodNext = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] + 1;
			$varsItemTemp = $this->_getVarsItemTemp(array(
				'numFiscalPeriodPrev' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				'numFiscalPeriodNext' => $numFiscalPeriodNext,
			));
			$varsAccountTitle = $varsItemTemp['arrAccountTitlePrev']['arrStrTitle'][$arrValue['arr']['idAccountTitle']];
		}

		$stampRegister = TIMESTAMP;
		$stampUpdate = TIMESTAMP;
		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrValue['arr']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));
		$strTitle = $arrValue['arr']['strTitle'];
		$idAccountTitle = $arrValue['arr']['idAccountTitle'];
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];


		try {
			$dbh->beginTransaction();

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

			if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
				$classCalcTempNextSubAccountTitle = $this->_getClassCalc(array(
					'flagType'   => 'TempNext',
					'flagDetail' => 'SubAccountTitle',
				));
				$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
				$numFiscalPeriod = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];

				$flag = $classCalcTempNextSubAccountTitle->allot(array(
					'flagStatus'       => 'add',
					'idTarget'         => $idSubAccountTitle,
					'numFiscalPeriod'  => $numFiscalPeriod,
					'varsAccountTitle' => $varsAccountTitle,
					'arrValue' => array(
						'strTitle'       => $arrValue['arr']['strTitle'],
						'idAccountTitle' => $arrValue['arr']['idAccountTitle'],
						'arrSpaceStrTag' => $arrSpaceStrTag,
					),
				));
				if ($flag) {
					$this->sendVars(array(
						'flag'    => $flag,
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(),
					));
				}
			}

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}
		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
		$this->_setSearch(array('flag' => 1));
	}

	/**
		(array(
			'strTitle' => '',
			'idTarget' => 0,
		))
	 */
	protected function _checkStrTitle($arr)
	{
		global $classDb;
		global $varsRequest;

		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

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
				'flagCondition' => 'eqSmall',
				'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
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
			$this->sendVars(array(
				'flag'    => 'strTitle',
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}
	}

	/**

	 */
	protected function _getVarsItemTemp($arr)
	{
		$arrAccountTitleNext = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => array(),
			'numFiscalPeriod'    => $arr['numFiscalPeriodNext'],
		));

		$arrAccountTitlePrev = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => array(),
			'numFiscalPeriod'    => $arr['numFiscalPeriodPrev'],
		));

		$data = array(
			'arrAccountTitleNext' => $arrAccountTitleNext,
			'arrAccountTitlePrev' => $arrAccountTitlePrev,
		);

		return $data;
	}



	/**
	 *
	 */
	protected function _iniDetailEdit()
	{
		global $classDb;
		global $classEscape;

		global $varsRequest;
		$dbh = $classDb->getHandle();
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		if (!$this->_checkCurrent()) {
			$this->_sendOldError();
		}

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'update',
			'idTarget'      => $this->_extSelf['idPreference'],
		));

		if (!$flag) {
			$this->_sendOldError();
		}
		$varsRequest['query']['jsonValue']['vars']['StrTitle'] = $classEscape->toComma(array(
			'data' => $varsRequest['query']['jsonValue']['vars']['StrTitle']
		));

		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$vars = $this->_updateVars(array(
			'vars' => $vars
		));

		$varsTarget = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget,
		));

		$this->_checkStrTitle(array(
			'strTitle'       => $arrValue['arr']['strTitle'],
			'idAccountTitle' => $arrValue['arr']['idAccountTitle'],
			'idTarget'       => $idTarget,
		));

		$varsTarget = $this->_getVarsTarget(array(
			'idTarget'        => $idTarget,
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));
		if (!$varsTarget) {
			$this->_sendOldError();
		}

		$flagEditCheck = array();
		$varsAccountTitle = array();
		$varsItemTemp = array();
		if (preg_match("/^(tempNext)$/", $flagCurrentFlagNow)) {
			$numFiscalPeriodPrev = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] - 1;
			$varsItemTemp = $this->_getVarsItemTemp(array(
				'numFiscalPeriodPrev' => $numFiscalPeriodPrev,
				'numFiscalPeriodNext' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			));
			$varsAccountTitle = $varsItemTemp['arrAccountTitleNext']['arrStrTitle'][$arrValue['arr']['idAccountTitle']];

		} elseif (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
			$numFiscalPeriodNext = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] + 1;
			$varsItemTemp = $this->_getVarsItemTemp(array(
				'numFiscalPeriodPrev' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				'numFiscalPeriodNext' => $numFiscalPeriodNext,
			));
			$varsAccountTitle = $varsItemTemp['arrAccountTitlePrev']['arrStrTitle'][$arrValue['arr']['idAccountTitle']];
		}

		if ($arrValue['arr']['idAccountTitle'] != $varsTarget['idAccountTitle']) {
			$flagUseLogAll = $this->_checkUseLog(array(
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				'idTarget'        => $idTarget,
				'flagAll'         => 1,
			));
			if ($flagUseLogAll) {
				$this->_sendOldError();
			}
			$flagEditCheck['idAccountTitle'] = 1;
			if (preg_match("/^(tempNext)$/", $flagCurrentFlagNow)) {
				if (!$varsItemTemp['arrAccountTitlePrev']['arrStrTitle'][$arrValue['arr']['idAccountTitle']]) {
					$flagEditCheck['idAccountTitleInsert'] = 1;
				}
			}
		}

		$tm = TIMESTAMP;
		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrValue['arr']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));
		$strTitle = $arrValue['arr']['strTitle'];
		$idAccountTitle = $arrValue['arr']['idAccountTitle'];

		$arrDbColumn = array('strTitle', 'idAccountTitle', 'arrSpaceStrTag');
		$arrDbValue = array($strTitle, $idAccountTitle, $arrSpaceStrTag);

		try {
			$dbh->beginTransaction();

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
						'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					),
					array(
						'flagType'      => '',
						'strColumn'     => 'idSubAccountTitle',
						'flagCondition' => 'eq',
						'value'         => $idTarget,
					),
				),
				'arrValue'  => $arrDbValue,
			));

			if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
				$classCalcTempNextSubAccountTitle = $this->_getClassCalc(array(
					'flagType'   => 'TempNext',
					'flagDetail' => 'SubAccountTitle',
				));
				$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
				$numFiscalPeriod = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];

				$flag = $classCalcTempNextSubAccountTitle->allot(array(
					'flagStatus'       => 'edit',
					'idTarget'         => $idTarget,
					'numFiscalPeriod'  => $numFiscalPeriod,
					'varsAccountTitle' => $varsAccountTitle,
					'arrValue' => array(
						'strTitle'       => $arrValue['arr']['strTitle'],
						'idAccountTitle' => $arrValue['arr']['idAccountTitle'],
						'arrSpaceStrTag' => $arrSpaceStrTag,
					),
				));
				if ($flag) {
					$this->sendVars(array(
						'flag'    => $flag,
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(),
					));
				}

			} elseif (preg_match("/^(tempNext)$/", $flagCurrentFlagNow)) {
				if ($flagEditCheck['idAccountTitle']) {
					$arrDbColumn = array('idAccountTitle');
					$arrDbValue = array($idAccountTitle);
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
								'value'         => $numFiscalPeriodPrev,
							),
							array(
								'flagType'      => '',
								'strColumn'     => 'idSubAccountTitle',
								'flagCondition' => 'eq',
								'value'         => $idTarget,
							),
						),
						'arrValue'  => $arrDbValue,
					));
				}
				if ($flagEditCheck['idAccountTitleInsert']) {
					$flag = $this->_checkVarsBackData(array(
						'numFiscalPeriod'  => $numFiscalPeriodPrev,
						'varsAccountTitle' => $varsAccountTitle,
						'arrValue'         => $arrValue['arr'],
					));
					if ($flag) {
						$this->sendVars(array(
							'flag'    => $flag,
							'stamp'   => $this->getStamp(),
							'numNews' => $this->getNumNews(),
							'vars'    => array(),
						));
					}
				}

			}
			$this->_updateDbPreferenceStamp(array('strColumn' => 'subAccountTitle'));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$this->_iniSearchDetail();
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
			'numFiscalPeriod'  => $numFiscalPeriodPrev,
			'varsAccountTitle' => $varsAccountTitle,
			'arrValue'         => $arrValue['arr'],
		))
	 */
	protected function _checkVarsBackData($arr)
	{
		global $varsPluginAccountingAccount;

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
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				'flagFS'          => $arr['varsAccountTitle']['flagFS'],
				'idTarget'        => $idAccountTitleJgaapFS,
				'flagReverse'     => 1,
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
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					'flagFS'          => 'CS',
					'idTarget'        => $key,
					'flagDirect'      => ($valueStrDirect == 'varsDirect')? 1 : 0,
					'flagReverse'     => 1,
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
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagFS'          => $arr['varsAccountTitle']['flagFS'],
			'idTarget'        => $arr['arrValue']['idAccountTitle'],
			'flagReverse'     => 1,
		));
		//'errorDataMax'
		if ($flag) {
			return $flag;
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




}
