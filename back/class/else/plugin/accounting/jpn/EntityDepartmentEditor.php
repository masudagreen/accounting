<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_EntityDepartmentEditor extends Code_Else_Plugin_Accounting_Jpn_EntityDepartment
{
	protected $_childSelf = array(
		'pathTplJs'   => 'else/plugin/accounting/js/jpn/entityDepartmentEditor.js',
		'pathVarsJs'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/entityDepartmentEditor.php',
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
					var_dump(__CLASS__ . '/' .__FUNCTION__);
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
		$dbh = $classDb->getHandle();

		global $varsRequest;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

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

		$varsTarget['vars']['varsDetail'] = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget['vars']['varsDetail']
		));

		$this->_checkStrTitle(array(
			'strTitle' => $arrValue['arr']['strTitle'],
			'idTarget' => 0,
		));

		$tm = TIMESTAMP;
		$stampRegister = $tm;
		$stampUpdate = $tm;
		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrValue['arr']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));
		$strTitle = $arrValue['arr']['strTitle'];
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];

		try {
			$dbh->beginTransaction();

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

			if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
				$classCalcTempNextEntityDepartment = $this->_getClassCalc(array(
					'flagType'   => 'TempNext',
					'flagDetail' => 'EntityDepartment',
				));
				$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
				$numFiscalPeriod = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];

				$flag = $classCalcTempNextEntityDepartment->allot(array(
					'flagStatus'      => 'add',
					'idTarget'        => $idDepartment,
					'numFiscalPeriod' => $numFiscalPeriod,
					'arrValue' => array(
						'strTitle'       => $arrValue['arr']['strTitle'],
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

			$this->_updateDbPreferenceStamp(array('strColumn' => 'entityDepartment'));
			$this->_updateDbPreferenceStamp(array('strColumn' => 'entityDepartmentValue'));

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
				'flagCondition' => 'eqSmall',
				'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
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
			$this->sendVars(array(
				'flag'    => 'strTitle',
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}
	}

	/**
	 *
	 */
	protected function _iniDetailEdit()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsAccount;
		global $varsRequest;
		global $classEscape;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		if (!$this->_checkCurrent()) {
			$this->_sendOldError();
		}

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());

		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];

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

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsTarget = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget
		));

		$this->_checkStrTitle(array(
			'strTitle' => $arrValue['arr']['strTitle'],
			'idTarget' => $idTarget,
		));

		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrValue['arr']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));
		$strTitle = $arrValue['arr']['strTitle'];

		$arrDbColumn = array('strTitle', 'arrSpaceStrTag');
		$arrDbValue = array($strTitle, $arrSpaceStrTag);
		try {
			$dbh->beginTransaction();

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
						'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					),
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idDepartment',
						'flagCondition' => 'eq',
						'value'         => $idTarget,
					),
				),
				'arrValue'  => $arrDbValue,
			));

			if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
				$classCalcTempNextEntityDepartment = $this->_getClassCalc(array(
					'flagType'   => 'TempNext',
					'flagDetail' => 'EntityDepartment',
				));
				$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
				$numFiscalPeriod = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];

				$flag = $classCalcTempNextEntityDepartment->allot(array(
					'flagStatus'      => 'edit',
					'idTarget'        => $idTarget,
					'numFiscalPeriod' => $numFiscalPeriod,
					'arrValue' => array(
						'strTitle'       => $arrValue['arr']['strTitle'],
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

			$this->_updateDbPreferenceStamp(array('strColumn' => 'entityDepartment'));

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

}
