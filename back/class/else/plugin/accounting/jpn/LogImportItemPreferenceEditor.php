<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_LogImportItemPreferenceEditor extends Code_Else_Plugin_Accounting_Jpn_LogImportItemPreference
{
	protected $_childSelf = array(
		'pathTplJs'  => 'else/plugin/accounting/js/jpn/logImportItemPreferenceEditor.js',
		'pathVarsJs' => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/logImportItemPreferenceEditor.php',
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

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'insert',
			'idTarget'      => $this->_extSelf['idPreference'],
		));
		if (!$flag) {
			$this->_sendOldError();
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$vars['portal']['varsDetail']['templateDetail'] = $this->_updateVarsTemplateDetail((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$vars['portal']['varsDetail']['templateDetail'] = $this->_checkVarsTemplateDetail((array(
			'vars' => &$vars,
		)));

		$varsTarget = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget
		));

		$arrValue['arr']['idSubAccountTitle'] = $varsRequest['query']['jsonValue']['vars']['IdSubAccountTitle'];

		$this->_checkValueDetail(array(
			'arrValue' => $arrValue,
			'varsItem' => $varsItem,
		));

		$this->_checkValueSame(array(
			'arrValue' => $arrValue,
			'idTarget' => 0,
		));

		$stampRegister = TIMESTAMP;
		$stampUpdate = TIMESTAMP;
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$strTitle = $arrValue['arr']['strTitle'];
		$flagAttest = $arrValue['arr']['flagAttest'];
		$flagReverse = $arrValue['arr']['flagReverse'];
		$idAccountTitle = $arrValue['arr']['idAccountTitle'];
		$idSubAccountTitle = $arrValue['arr']['idSubAccountTitle'];
		$idDepartment = $arrValue['arr']['idDepartment'];
		$arrSpaceStrTag = $classEscape->splitSpaceArray(array('data' => $arrValue['arr']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));
		try {
			$dbh->beginTransaction();

			$varsIdNumber = $this->_getIdAutoIncrement(array(
				'idTarget' => 'idLogImport'
			));
			if (!$varsIdNumber[$idEntity]) {
				$varsIdNumber[$idEntity] = 1;
			}
			$idLogImport = $varsIdNumber[$idEntity];

			$arrDbColumn = array('stampRegister', 'stampUpdate', 'idEntity', 'idLogImport', 'strTitle', 'flagAttest', 'flagReverse', 'idAccountTitle', 'idSubAccountTitle', 'idDepartment', 'arrSpaceStrTag');
			$arrDbValue = array($stampRegister, $stampUpdate, $idEntity, $idLogImport, $strTitle, $flagAttest, $flagReverse, $idAccountTitle, $idSubAccountTitle, $idDepartment, $arrSpaceStrTag);

			$classDb->insertRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingLogImport' . $strNation,
				'arrColumn' => $arrDbColumn,
				'arrValue'  => $arrDbValue,
			));

			$varsIdNumber[$idEntity]++;
			$this->_updateIdAutoIncrement(array(
				'idTarget'   => 'idLogImport',
				'varsTarget' => $varsIdNumber
			));

			$array = array('logImport');
			foreach ($array as $key => $value) {
				$this->_updateDbPreferenceStamp(array('strColumn' => $value));
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
			'arrValue' => $arrValue,
			'varsItem' => $varsItem,
		))
	 */
	protected function _checkVarsTemplateDetail($arr)
	{
		$arrayNew = array();
		$array = $arr['vars']['portal']['varsDetail']['templateDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'IdSubAccountTitle') {
				continue;
			}
			$arrayNew[] = $value;
		}

		return $arrayNew;
	}

	/**
		(array(
			'arrValue' => $arrValue,
			'varsItem' => $varsItem,
		))
	 */
	protected function _checkValueDetail($arr)
	{
		$idAccountTitle = $arr['arrValue']['arr']['idAccountTitle'];
		$idSubAccountTitle = $arr['arrValue']['arr']['idSubAccountTitle'];
		if ($idSubAccountTitle
			&& !$arr['varsItem']['arrSubAccountTitle']['arrStrTitle'][$idAccountTitle][$idSubAccountTitle]
		) {
			$this->_sendOldError();
		}
	}

/**
		(array(
			'strTitle' => '',
			'idTarget' => 0,
		))
	 */
	protected function _checkValueSame($arr)
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
				'flagType'      => '',
				'strColumn'     => 'strTitle',
				'flagCondition' => 'eq',
				'value'         => $arr['arrValue']['arr']['strTitle'],
			),
			array(
				'flagType'      => '',
				'strColumn'     => 'flagAttest',
				'flagCondition' => 'eq',
				'value'         => $arr['arrValue']['arr']['flagAttest'],
			),
			array(
				'flagType'      => '',
				'strColumn'     => 'flagReverse',
				'flagCondition' => 'eq',
				'value'         => $arr['arrValue']['arr']['flagReverse'],
			),
			array(
				'flagType'      => '',
				'strColumn'     => 'idAccountTitle',
				'flagCondition' => 'eq',
				'value'         => $arr['arrValue']['arr']['idAccountTitle'],
			),
			array(
				'flagType'      => '',
				'strColumn'     => 'idSubAccountTitle',
				'flagCondition' => 'eq',
				'value'         => $arr['arrValue']['arr']['idSubAccountTitle'],
			),
			array(
				'flagType'      => '',
				'strColumn'     => 'idDepartment',
				'flagCondition' => 'eq',
				'value'         => $arr['arrValue']['arr']['idDepartment'],
			),
		);
		if ($arr['idTarget']) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idLogImport',
				'flagCondition' => 'ne',
				'value'         => $arr['idTarget'],
			);
		}

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);
		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogImport' . $strNation,
			'arrLimit' => array(
				'numStart' => 0, 'numEnd' => 1,
			),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => $arrWhere,
		));
		if ($rows['numRows']) {
			$this->sendVars(array(
				'flag'    => 'strSame',
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
		global $classEscape;

		global $varsRequest;
		$dbh = $classDb->getHandle();
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'update',
			'idTarget'      => $this->_extSelf['idPreference'],
		));
		if (!$flag) {
			$this->_sendOldError();
		}

		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$vars['portal']['varsDetail']['templateDetail'] = $this->_updateVarsTemplateDetail((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$vars['portal']['varsDetail']['templateDetail'] = $this->_checkVarsTemplateDetail((array(
			'vars' => &$vars,
		)));

		$varsTarget = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget,
		));

		$arrValue['arr']['idSubAccountTitle'] = $varsRequest['query']['jsonValue']['vars']['IdSubAccountTitle'];

		$this->_checkValueDetail(array(
			'arrValue' => $arrValue,
			'varsItem' => $varsItem,
		));

		$this->_checkValueSame(array(
			'arrValue' => $arrValue,
			'idTarget' => $idTarget,
		));

		$varsTarget = $this->_getVarsTarget(array(
			'idTarget'        => $idTarget,
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));
		if (!$varsTarget) {
			$this->_sendOldError();
		}

		$strTitle = $arrValue['arr']['strTitle'];
		$flagAttest = $arrValue['arr']['flagAttest'];
		$flagReverse = $arrValue['arr']['flagReverse'];
		$idAccountTitle = $arrValue['arr']['idAccountTitle'];
		$idSubAccountTitle = $arrValue['arr']['idSubAccountTitle'];
		$idDepartment = $arrValue['arr']['idDepartment'];
		$arrSpaceStrTag = $classEscape->splitSpaceArray(array('data' => $arrValue['arr']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));

		$arrDbColumn = array('strTitle', 'flagAttest', 'flagReverse', 'idAccountTitle', 'idSubAccountTitle', 'idDepartment', 'arrSpaceStrTag');
		$arrDbValue = array($strTitle, $flagAttest, $flagReverse, $idAccountTitle, $idSubAccountTitle, $idDepartment, $arrSpaceStrTag);

		try {
			$dbh->beginTransaction();

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingLogImport' . $strNation,
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
						'flagType'      => '',
						'strColumn'     => 'idLogImport',
						'flagCondition' => 'eq',
						'value'         => $idTarget,
					),
				),
				'arrValue'  => $arrDbValue,
			));

			$this->_updateDbPreferenceStamp(array('strColumn' => 'logImport'));

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
			'strTable' => 'accountingLogImport' . $strNation,
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
					'flagType'      => '',
					'strColumn'     => 'idLogImport',
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
