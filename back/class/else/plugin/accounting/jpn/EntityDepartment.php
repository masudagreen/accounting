<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_EntityDepartment extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference' => 'entityDepartmentWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/entityDepartment.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/entityDepartment.php',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;
		global $classCheck;
		global $varsPluginAccountingPreference;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'select',
			'idTarget'      => $this->_extSelf['idPreference'],
		));

		if (!$flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}

		$this->_checkEntity();

		if ($varsRequest['query']['child']) {
			$strChild = ucwords($varsRequest['query']['child']);
			$strExt = ucwords($varsRequest['query']['ext']);
			$str = $strExt . $strChild;

			$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/jpn/' . $str . ".php";
			if (!file_exists($path)) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
				}
				exit;
			}
			require_once($path);
			$strClass = 'Code_Else_Plugin_Accounting_Jpn_' . $str;
			$classCall = new $strClass;
			$classCall->run();

		} else {
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
		}
		exit;
	}

	/**
		$insCurrent->getDBAuthority(array(
			'flagSqlType' => $arr['flagSqlType'],ex) Select,Update,Delete,Output
			'arrData'     => ($arr['arrData']),ex) flag
		));
		return $array = array(
			'strSql'   => '',
			'arrValue' => array(),
		);
		or
		return 0
	 */
	public function getDBAuthority($arr)
	{
		global $classCheck;
		global $varsPluginAccountingAccount;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];

		$flagAuthority = $classCheck->checkModuleAuthority(array(
			'idModule' => 'accounting',
		));

		if ($flagAuthority) {
			$array = array(
				'strSql'   => 'idEntity = ? and numFiscalPeriod = ?',
				'arrValue' => array($idEntity, $numFiscalPeriod),
			);
			return $array;

		}
		return 0;
	}

	/**
	 *
	 */
	protected function _iniJs()
	{
		$this->_setJs(array(
			'pathVars'        => $this->_extSelf['pathVarsJs'],
			'pathTpl'         => $this->_extSelf['pathTplJs'],
			'arrFolder' => array(),
			'arrSearch'       => array(
				'idModule'  => 'accounting',
				'numLotNow' => 0,
				'strTable'  => 'accountingEntityDepartment',
				'arrOrder'  => array(
					'strColumn' => 'id',
					'flagDesc'  => 1,
				),
				'insCurrent' => $this,
				'arrWhere'  => array(),
			),
		));
	}

	/**
		$this->_setJs(array(
			'pathVars'        => '',
			'pathTpl'         => '',
			'arrFolder'       => array(),
			'strTableSearch'  => '',
			'strColumnSearch' => '',
			'flagEntitySearch'  => 0,
			'flagAccountSearch' => 0,
			'arrSearch'       => array(
				'idModule' => '',
				'numLotNow' => 0,
				'strTable'  => '',
				'arrOrder'  => array(),
				'arrWhere'  => array(),
			),
		));
	 */
	protected function _setJs($arr)
	{
		global $classSmarty;

		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
		));

		$rows = $this->getSearch($arr['arrSearch']);

		$vars = $this->_updateSearch(array(
			'vars' => $vars,
			'rows' => $rows,
		));

		$vars['portal']['varsNavi'] = array();

		$json = json_encode($vars);
		$classSmarty->assign('varsLoad', $json);

		$classSmarty->assign('numNews', $this->getNumNews());
		$contents = $classSmarty->fetch($arr['pathTpl']);

		$this->sendJs(array(
			'data' => $contents,
		));
	}

	/**
	 * array(
	 * 	'flag' => int
	 * )
	 */
	protected function _setSearch($arr)
	{
		global $varsRequest;
		global $varsPluginAccountingPreference;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingPreference['jsonStampUpdate']['entityDepartment'],
				'flagSearch'  => 1,
			));
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$this->checkSearch(array(
			'arrOrder' => $vars['portal']['varsNavi']['search']['varsDetail']['varsSearchSort'],
			'arrWhere' => $vars['portal']['varsNavi']['search']['varsDetail']['varsSearchItem'],
		));

		if (!$varsRequest['query']['jsonSearch']['ph']['arrOrder']) {
			$varsRequest['query']['jsonSearch']['ph']['arrOrder']= array(
				'strColumn' => 'id',
				'flagDesc'  => 1,
			);
		}
		$rows = $this->getSearch(array(
			'idModule'  => 'accounting',
			'numLotNow' => $varsRequest['query']['jsonSearch']['numLotNow'],
			'strTable'  => 'accountingEntityDepartment',
			'arrOrder'  => $varsRequest['query']['jsonSearch']['ph']['arrOrder'],
			'insCurrent' => $this,
			'arrWhere'  => $varsRequest['query']['jsonSearch']['ph']['arrWhere'],
		));

		$vars = $this->_updateSearch(array(
			'vars' => $vars,
			'rows' => $rows,
		));

		$this->sendVars(array(
			'flag'    => ($arr['flag'])? $arr['flag'] : 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'numRows'    => $rows['numRows'],
				'varsDetail' => $vars['portal']['varsList']['varsDetail'],
				'varsHtml'   => $vars['portal']['varsList']['varsHtml'],
			),
		));

	}

	/**
	 *
	 */
	protected function _updateSearch($arr)
	{
		global $varsRequest;
		global $varsAccount;

		global $classHtml;
		global $classEscape;

		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingAccountsEntity;
		global $varsPluginAccountingAuthority;

		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$idEntity  = $varsPluginAccountingAccount['idEntityCurrent'];
		$strCheckStamp = 'accountingEntityDepartment_' . $idEntity . '_' . $numFiscalPeriod;

		$vars = $arr['vars'];
		$rows = $arr['rows'];

		$idAccount = $varsPluginAccountingAccount['id'];
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$idAuthority = $varsPluginAccountingAccountsEntity[$idAccount][$idEntity]['idAuthority'];
		$varsAuthority = $varsPluginAccountingAuthority[$idAuthority];
		$dataAuthority = array(
			'flagInsert' => ($varsAuthority['flagAllInsert'])? 1 : 0,
			'flagUpdate' => ($varsAuthority['flagAllUpdate'])? 1 : 0,
			'flagDelete' => ($varsAuthority['flagAllDelete'])? 1 : 0,
		);

		$array = $rows['arrRows'];
		$arrayNew = array();
		$num = 0;
		$flag = 0;
		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		foreach ($array as $key => $value) {
			$varsTmpl = $vars['portal']['varsList']['templateDetail'];
			$varsTmpl['id'] = $value['idDepartment'];
			$varsTmpl['vars']['idTarget'] = $value['idDepartment'];
			$varsTmpl['numSort'] = (int) $key;
			$varsTmpl['strTitle'] = $value['strTitle'];
			$varsTmpl['stampRegister'] = $value['stampRegister'];
			$varsTmpl['stampUpdate'] = $value['stampUpdate'];

			if ($varsAccount['jsonStampCheck'][$strCheckStamp] < $value['stampRegister']) {
				$flag = 1;

			} else {
				$varsTmpl['strClassLoad'] = '';
			}
			$varsTmpl['varsAuthority'] = $dataAuthority;
			$varsTmpl['arrSpaceStrTag'] = preg_replace('/^ /', '', $value['arrSpaceStrTag']);
			if (preg_match("/^(done)$/", $flagCurrentFlagNow)) {
				$varsTmpl['flagDefault'] = 1;

			} else {
				$varsTmpl['flagDefault'] = (int) $this->_checkUseLog(array(
					'idTarget'        => $varsTmpl['id'],
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				));
				if (!$varsTmpl['flagDefault'] && preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
					$numFiscalPeriodTempNext = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] + 1;
					$varsTmpl['flagDefault'] = (int) $this->_checkUseLog(array(
						'idTarget'        => $varsTmpl['id'],
						'numFiscalPeriod' => $numFiscalPeriodTempNext,
					));
				}
			}
			if (!$varsAuthority['flagAllDelete']) {
				$varsTmpl['flagDefault'] = 1;
			}
			$varsTmpl['flagLogUse'] = $varsTmpl['flagDefault'];
			$varsTmpl['flagCheckboxUse'] = ($varsTmpl['flagDefault'])? 0 : 1;

			$varsTmpl['varsColumnDetail']['id'] = $varsTmpl['id'];
			$varsTmpl['varsColumnDetail']['strTitle'] = $value['strTitle'];
			$varsTmpl['varsColumnDetail']['stampRegister'] = $value['stampRegister'];
			$varsTmpl['varsColumnDetail']['stampUpdate'] = $value['stampUpdate'];

			$varsTmpl['vars']['id'] = $varsTmpl['id'];
			$varsTmpl['vars']['strTitle'] = $value['strTitle'];
			$varsTmpl['vars']['arrSpaceStrTag'] = $classEscape->splitSpaceArrayData(array('data' => $varsTmpl['arrSpaceStrTag']));
			$arrayColumnDetail = &$varsTmpl['varsColumnDetail'];
			foreach ($arrayColumnDetail as $keyColumnDetail => $valueColumnDetail) {
				if (is_null($valueColumnDetail)) {
					$arrayColumnDetail[$keyColumnDetail] = '';
				}
			}
			$varsTmpl['varsScheduleDetail']['stamp'] = $value['stampRegister'];

			$arrayNew[$num] = $varsTmpl;
			$num++;
		}
		$vars['portal']['varsList']['varsPage']['varsStatus']['numRows'] = $rows['numRows'];
		$vars['portal']['varsList']['varsDetail'] = $arrayNew;

		if (!$varsAuthority['flagAllInsert']) {
			$vars['portal']['varsDetail']['varsStart']['varsEdit']['flagAddUse'] = 0;
		}

		if (preg_match("/^(done)$/", $flagCurrentFlagNow)) {
			$vars['portal']['varsDetail']['varsStart']['varsEdit'] = array();
			$vars['portal']['varsDetail']['varsBtn'] = array();
			$vars['portal']['varsDetail']['view']['varsEdit'] = array();
		}

		if (!$arr['flagVars']) {
			$varsTemp = $classHtml->allot(array(
				'strClass'    => 'Table',
				'flagStatus'  => 'Html',
				'numTimeZone' => $varsAccount['numTimeZone'],
				'varsDetail'  => $arrayNew,
				'varsColumn'  => $vars['portal']['varsList']['table']['varsDetail']['varsColumn'],
				'varsStatus'  => $vars['portal']['varsList']['table']['varsDetail']['varsStatus'],
			));
			$vars['portal']['varsList']['varsHtml'] = $varsTemp['strHtml'];
			if ($flag) {
				$this->_setDbStampCheck(array(
					'strColumnAccount'    => $strCheckStamp,
					'strColumnPreference' => 'entityDepartment',
				));
			}
		}

		return $vars;
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
	 *
	 */
	protected function _iniNaviSearch()
	{
		$this->_setSearch(array('flag' => 1));
	}

	/**
	 *
	 */
	protected function _iniNaviSearchSave()
	{
		$this->_setNaviSearchSave(array(
			'pathVars'    => $this->_extSelf['pathVarsJs'],
			'strTable'    => 'accountingAccountMemo',
			'strColumn'   => 'jsonEntityDepartmentNaviSearch',
			'flagEntity'  => 1,
			'flagAccount' => 1,
		));
	}

	/**
	 *
	 */
	protected function _iniNaviSearchDelete()
	{
		$this->_setNaviSearchDelete(array(
			'strTable'  => 'accountingAccountMemo',
			'strColumn' => 'jsonEntityDepartmentNaviSearch',
			'flagEntity'  => 1,
			'flagAccount' => 1,
		));
	}


	/**
	 *
	 */
	protected function _iniNaviSearchReload()
	{
		global $varsPluginAccountingAccount;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingAccount['stampUpdate'],
				'flagSearch'  => 0,
			));
		}

		$this->_setNaviSearchReload(array(
			'pathVars'  => $this->_extSelf['pathVarsJs'],
			'strTable'  => 'accountingAccountMemo',
			'strColumn' => 'jsonEntityDepartmentNaviSearch',
			'flagEntity'  => 1,
			'flagAccount' => 1,
		));
	}

	/**
	 *
	 */
	protected function _iniListReload()
	{
		$this->_setSearch(array('flag' => 1));
	}

	/**
	 *
	 */
	protected function _iniListDelete()
	{
		global $varsRequest;

		$this->_setDelete(array(
			'arrId' => $varsRequest['query']['jsonValue']['vars'],
		));

	}

	/**
	 *
	 */
	protected function _iniDetailReload()
	{
		$this->_iniSearchDetail();
	}

	/**
	 *
	 */
	protected function _iniSearchDetail()
	{
		global $varsRequest;
		global $varsPluginAccountingPreference;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingPreference['jsonStampUpdate']['entityDepartment'],
				'flagSearch'  => 0,
			));
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$this->checkSearch(array(
			'arrOrder' => $vars['portal']['varsNavi']['search']['varsDetail']['varsSearchSort'],
			'arrWhere' => $vars['portal']['varsNavi']['search']['varsDetail']['varsSearchItem'],
		));

		if (!$varsRequest['query']['jsonSearch']['ph']['arrOrder']) {
			$varsRequest['query']['jsonSearch']['ph']['arrOrder']= array(
				'strColumn' => 'id',
				'flagDesc'  => 1,
			);
		}
		$rows = $this->getSearch(array(
			'idModule'  => 'accounting',
			'numLotNow' => $varsRequest['query']['jsonSearch']['numLotNow'],
			'strTable'  => 'accountingEntityDepartment',
			'arrOrder'  => $varsRequest['query']['jsonSearch']['ph']['arrOrder'],
			'insCurrent' => $this,
			'arrWhere'  => $varsRequest['query']['jsonSearch']['ph']['arrWhere'],
		));

		$vars = $this->_updateSearch(array(
			'vars' => $vars,
			'rows' => $rows,
		));

		$varsTarget = $vars;

		$rowsTarget = $this->getSearch(array(
			'idModule'  => 'accounting',
			'numLotNow' => 0,
			'strTable'  => 'accountingEntityDepartment',
			'arrJoin'   => array(),
			'arrOrder'  => array(),
			'flagAnd'   => 1,
			'arrWhere'  => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idDepartment',
					'flagCondition' => 'eq',
					'value'         => $varsRequest['query']['jsonValue']['idTarget'],
				)
			),
			'insCurrent'  => $this,
		));

		if (!$rowsTarget['numRows']) {
			$this->_sendOldError();
		}

		$varsTarget = $this->_updateSearch(array(
			'vars'     => $varsTarget,
			'rows'     => $rowsTarget,
			'flagVars' => 1,
		));

		$this->sendVars(array(
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'varsDetail' => $varsTarget['portal']['varsList']['varsDetail'][0],
				'numRows'    => $rows['numRows'],
				'numLotNow'  => $varsRequest['query']['jsonSearch']['numLotNow'],
				'varsList'   => $vars['portal']['varsList']['varsDetail'],
				'varsHtml'   => $vars['portal']['varsList']['varsHtml'],
			),
		));
	}

	/**
		$this->_setDelete(array(
			'arrId' => array(),
		));
	 */
	protected function _setDelete($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $classPluginAccountingInit;
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
			'flagAuthority' => 'delete',
			'idTarget'      => $this->_extSelf['idPreference'],
		));

		if (!$flag) {
			$this->_sendOldError();
		}

		$array = $arr['arrId'];
		foreach ($array as $key => $value) {
			$flag = $this->_checkUseLog(array(
				'idTarget'        => $value,
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			));
			if ($flag) {
				$varsRequest['query']['jsonSearch']['flagReload'] = 0;
				$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
				$this->_setSearch(array('flag' => 1));

			} else {
				if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
					$numFiscalPeriodTempNext = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] + 1;
					$flag = (int) $this->_checkUseLog(array(
						'idTarget'        => $value,
						'numFiscalPeriod' => $numFiscalPeriodTempNext,
					));
					if ($flag) {
						$varsRequest['query']['jsonSearch']['flagReload'] = 0;
						$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
						$this->_setSearch(array('flag' => 1));
					}
				}
			}
		}

		try {
			$dbh->beginTransaction();

			foreach ($array as $key => $value) {

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
							'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
						),
						array(
							'flagType'      => 'num',
							'strColumn'     => 'idDepartment',
							'flagCondition' => 'eq',
							'value'         => $value,
						),
					),
				));

				if (!$rows['numRows']) {
					continue;
				}

				$idDepartment = $value;

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
							'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
						),
						array(
							'flagType'      => 'num',
							'strColumn'     => 'idDepartment',
							'flagCondition' => 'eq',
							'value'         => $idDepartment,
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
							'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
						),
						array(
							'flagType'      => 'num',
							'strColumn'     => 'idDepartment',
							'flagCondition' => 'eq',
							'value'         => $idDepartment,
						),
					),
				));

				if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
					$classCalcTempNextEntityDepartment = $this->_getClassCalc(array(
						'flagType'   => 'TempNext',
						'flagDetail' => 'EntityDepartment',
					));
					$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
					$numFiscalPeriod = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];

					$classCalcTempNextEntityDepartment->allot(array(
						'flagStatus'      => 'delete',
						'numFiscalPeriod' => $numFiscalPeriod,
						'idTarget'        => $idDepartment,
					));
				}

			}

			$array = array('entityDepartment', 'entityDepartmentFSValue');
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

		$classPluginAccountingInit->updateInitPreference();

		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
		$this->_setSearch(array('flag' => 1));
	}


	/**

	 */
	protected function _iniDetailDelete()
	{
		global $varsRequest;
		global $varsPluginAccountingAccount;

		if (!$varsPluginAccountingAccount['idEntityCurrent']) {
			$varsRequest['query']['jsonSearch']['flagReload'] = 0;
			$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
			$this->_setSearch(array('flag' => 1));
		}

		$this->_setDelete(array(
			'arrId' => array($varsRequest['query']['jsonValue']['idTarget']),
		));
	}
}
