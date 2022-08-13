<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Entity extends Code_Else_Plugin_Accounting_Accounting
{
	protected $_extSelf = array(
		'pathTplJs'  => 'else/plugin/accounting/js/entity.js',
		'pathVarsJs' => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/entity.php',
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

		$flag = $classCheck->checkModule(array(
			'idModule' => $varsRequest['query']['module'],
			'flagType' => 'Admin'
		));

		if (!($varsRequest['query']['child'] == 'Choice' || $varsRequest['query']['child'] == 'ChoiceWithoutConfig')) {
			if (!$flag) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
				}
				exit;
			}
		}

		if ($varsRequest['query']['child']) {
			$strChild = ucwords($varsRequest['query']['child']);
			$strExt = ucwords($varsRequest['query']['ext']);
			$str = $strExt . $strChild;

			$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/' . $str . ".php";
			if (!file_exists($path)) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
				}
				exit;
			}
			require_once($path);
			$strClass = 'Code_Else_Plugin_Accounting_' . $str;
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
				'strTable'  => 'accountingEntity',
				'arrOrder'  => array(
					'strColumn' => 'id',
					'flagDesc'  => 1,
				),
				'insCurrent'  => $this,
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
	 *
	 */
	protected function _iniNaviSearchSave()
	{
		$this->_setNaviSearchSave(array(
			'pathVars'    => $this->_extSelf['pathVarsJs'],
			'strTable'    => 'accountingAccountMemo',
			'strColumn'   => 'jsonEntityNaviSearch',
			'flagEntity'  => 0,
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
			'strColumn' => 'jsonEntityNaviSearch',
			'flagEntity'  => 0,
			'flagAccount' => 1,
		));
	}

	/**
		$this->_setNaviSearchSave(array(
			'pathVars'  => '',
			'strTable'  => '',
			'strColumn' => '',
			'flagEntity' => 0,
			'flagAccount' => 0,
		));
	 */
	protected function _setNaviSearchSave($arr)
	{
		global $varsRequest;
		global $classDb;
		$dbh = $classDb->getHandle();
		global $varsAccount;
		global $varsPluginAccountingAccount;

		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
		));

		$vars['portal']['varsNavi']['search']['varsDetail']['varsSearchItem']['templateDetail']['restOption']['nation'] = $this->_getArrOptionNation($flagHash);

		$varsJson = $this->checkValueSearch(array(
			'varsValue'  => ($varsRequest['query']['jsonValue']['vars'])? $varsRequest['query']['jsonValue']['vars'] : array(),
			'varsSearch' => $vars['portal']['varsNavi']['search'],
		));

		$strJson = json_encode($varsJson);
		$this->checkTextSize(array(
			'flag' => 'errorDataMax',
			'str'  => $strJson,
		));
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$idAccount = $varsAccount['id'];
		$arrWhere = array();
		if ($arr['flagEntity']) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idEntity',
				'flagCondition' => 'eq',
				'value'         => $idEntity,
			);
		} else {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idEntity',
				'flagCondition' => 'eq',
				'value'         => 0,
			);
		}

		if ($arr['flagAccount']) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idAccount',
				'flagCondition' => 'eq',
				'value'         => $idAccount,
			);
		}

		$arrWhere[] = array(
			'flagType'      => '',
			'strColumn'     => 'flagColumn',
			'flagCondition' => 'eq',
			'value'         => $arr['strColumn'],
		);

		try {
			$dbh->beginTransaction();

			$classDb->updateRow(array(
				'idModule' => 'accounting',
				'strTable'  => $arr['strTable'],
				'arrColumn' => array('jsonData'),
				'flagAnd'   => 1,
				'arrWhere'  => $arrWhere,
				'arrValue'  => array($strJson),
			));

			if ($arr['flagAccount']) {
				$this->updateDbAccountStamp();
				$this->_updateDbPreferenceStamp(array('strColumn' => 'account'));
			}

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$this->sendVars(array(
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $this->_getMemo(array(
				'strTable'    => $arr['strTable'],
				'strColumn'   => $arr['strColumn'],
				'flagEntity'  => $arr['flagEntity'],
				'flagAccount' => $arr['flagAccount'],
			)),
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
			'strColumn' => 'jsonEntityNaviSearch',
			'flagEntity'  => 0,
			'flagAccount' => 1,
		));
	}

	/**
		$this->_setNaviSearchReload(array(
			'pathVars'  => '',
			'strTable'  => '',
			'strColumn' => '',
			'flagEntity' => 0,
			'flagAccount' => 0,
		));
	 */
	protected function _setNaviSearchReload($arr)
	{
		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
		));
/*
		$vars['portal']['varsNavi']['search']['varsDetail']['varsSearchItem']['templateDetail']['restOption']['nation'] = $this->_getArrOptionNation($flagHash);
*/
		$vars['portal']['varsNavi']['search']['varsDetail']['varsMyRecord']['varsFormList']['varsDetail'] = $this->_getMemo(array(
			'strTable'    => $arr['strTable'],
			'strColumn'   => $arr['strColumn'],
			'flagEntity'  => $arr['flagEntity'],
			'flagAccount' => $arr['flagAccount'],
		));

		$this->sendVars(array(
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $vars['portal']['varsNavi']['search']['varsDetail'],
		));
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
	protected function _updateSearch($arr)
	{
		global $varsRequest;
		global $varsAccount;

		global $classEscape;
		global $classHtml;

		$vars = $arr['vars'];
		$rows = $arr['rows'];

		$strCheckStamp = 'accountingEntity';

		$flagHash = 1;
		$arrNation = $this->_getArrOptionNation($flagHash);
		$arrLang = $this->_getArrOptionLang($flagHash);
		$array = $rows['arrRows'];
		$arrayNew = array();
		$num = 0;
		$flag = 0;
		foreach ($array as $key => $value) {
			$varsTmpl = $vars['portal']['varsList']['templateDetail'];
			$varsTmpl['id'] = $value['id'];
			$varsTmpl['vars']['idTarget'] = $value['id'];
			$varsTmpl['numSort'] = (int) $key;

			if ($varsAccount['jsonStampCheck'][$strCheckStamp] < $value['stampRegister']) {
				$flag = 1;

			} else {
				$varsTmpl['strClassLoad'] = '';

			}
			$varsTmpl['strTitle'] = $value['strTitle'];
			$varsTmpl['stampRegister'] = $value['stampRegister'];
			$varsTmpl['stampUpdate'] = $value['stampUpdate'];
			$varsTmpl['arrSpaceStrTag'] = preg_replace('/^ /', '', $value['arrSpaceStrTag']);
			$varsTmpl['flagDefault'] = (int) $value['flagDefault'];
			$varsTmpl['flagCheckboxUse'] = ($value['flagDefault'])? 0 : 1;
			$varsTmpl['strNation'] = $value['strNation'];
			$varsTmpl['strLang'] = $value['strLang'];
			$varsTmpl['idEntity'] = $value['id'];
			$varsTmpl['numFiscalPeriod'] = $value['numFiscalPeriod'];
			$varsTmpl['flagConfig'] = $value['flagConfig'];
			if ($varsTmpl['flagConfig']) {
				$varsTmpl['strClassFont'] = 'codeLibBaseFontOrange';
			}

			$varsTmpl['varsColumnDetail']['id'] = $varsTmpl['id'];
			$varsTmpl['varsColumnDetail']['strTitle'] = $value['strTitle'];
			$varsTmpl['varsColumnDetail']['stampRegister'] = $value['stampRegister'];
			$varsTmpl['varsColumnDetail']['stampUpdate'] = $value['stampUpdate'];
			$varsTmpl['varsColumnDetail']['numFiscalPeriod'] = str_replace('<%numFiscalPeriod%>', $value['numFiscalPeriod'], $varsTmpl['varsColumnDetail']['numFiscalPeriod']);
			$varsTmpl['varsColumnDetail']['numFiscalPeriod'] = str_replace('<%numFiscalPeriodStart%>', $value['numFiscalPeriodStart'], $varsTmpl['varsColumnDetail']['numFiscalPeriod']);
			$varsTmpl['varsColumnDetail']['strNation'] = $arrNation[$value['strNation']]['strTitle'];
			$varsTmpl['varsColumnDetail']['strLang'] = $arrLang[$value['strLang']]['strTitle'];

			$varsTmpl['vars']['id'] = $varsTmpl['id'];
			$varsTmpl['vars']['strTitle'] = $value['strTitle'];
			$varsTmpl['vars']['numFiscalPeriod'] = $value['numFiscalPeriod'];
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

		$vars['portal']['varsDetail']['templateDetail'] = $this->_updateVars(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail'],
		));

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
					'strColumnPreference' => 'accounts',
				));
			}
		}

		return $vars;
	}

	/**
		$this->_updateVars(array(
			'vars' => array(),
		));
	 */
	protected function _updateVars($arr)
	{
		$array = &$arr['vars'];
		$arrayNew = array();
		foreach ($array as $key => $value) {
			if ($value['id'] == 'StrNation') {
				$value['arrayOption'] = $this->_getArrOptionNation($flagHash);

			} elseif ($value['id'] == 'StrLang') {
				$value['arrayOption'] = $this->_getArrOptionLang($flagHash);
			}
			$arrayNew[] = $value;

		}

		return $arrayNew;
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
				'stampTarget' => $varsPluginAccountingPreference['jsonStampUpdate']['entity'],
				'flagSearch'  => 1,
			));
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));
/*
		$vars['portal']['varsNavi']['search']['varsDetail']['varsSearchItem']['templateDetail']['restOption']['nation'] = $this->_getArrOptionNation($flagHash);
*/
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
			'idModule'   => 'accounting',
			'numLotNow'  => $varsRequest['query']['jsonSearch']['numLotNow'],
			'strTable'   => 'accountingEntity',
			'arrOrder'   => $varsRequest['query']['jsonSearch']['ph']['arrOrder'],
			'insCurrent' => $this,
			'arrWhere'   => $varsRequest['query']['jsonSearch']['ph']['arrWhere'],
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
	protected function _iniListReload()
	{
		$this->_setSearch(array('flag' => 1));
	}

	/**
	 *
	 */
	protected function _iniDetailReload()
	{
		global $varsPluginAccountingEntity;
		global $varsRequest;

		if (!$varsPluginAccountingEntity[$varsRequest['query']['jsonValue']['idTarget']]) {
			$this->_sendOldError();
		}

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
				'stampTarget' => $varsPluginAccountingPreference['jsonStampUpdate']['entity'],
				'flagSearch'  => 0,
			));
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));
/*
		$vars['portal']['varsNavi']['search']['varsDetail']['varsSearchItem']['templateDetail']['restOption']['nation'] = $this->_getArrOptionNation($flagHash);
*/
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
			'idModule'   => 'accounting',
			'numLotNow'  => $varsRequest['query']['jsonSearch']['numLotNow'],
			'strTable'   => 'accountingEntity',
			'arrOrder'   => $varsRequest['query']['jsonSearch']['ph']['arrOrder'],
			'insCurrent' => $this,
			'arrWhere'   => $varsRequest['query']['jsonSearch']['ph']['arrWhere'],
		));

		$vars = $this->_updateSearch(array(
			'vars' => $vars,
			'rows' => $rows,
		));

		$varsTarget = $vars;

		$rowsTarget = $this->getSearch(array(
			'idModule'  => 'accounting',
			'numLotNow' => 0,
			'strTable'  => 'accountingEntity',
			'arrOrder'  => array(),
			'arrWhere'  => array(array(
				'flagType'      => 'num',
				'strColumn'     => 'id',
				'flagCondition' => 'eq',
				'value'         => $varsRequest['query']['jsonValue']['idTarget'],
			)),
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
	 *
	 */
	protected function _iniDetailDelete()
	{
		global $varsPluginAccountingEntity;
		global $varsRequest;

		if (!$varsPluginAccountingEntity[$varsRequest['query']['jsonValue']['idTarget']]) {
			$varsRequest['query']['jsonSearch']['flagReload'] = 0;
			$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
			$this->_setSearch(array('flag' => 1));
		}

		$this->_setDelete(array(
			'arrId' => array($varsRequest['query']['jsonValue']['idTarget']),
		));
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
		$this->_setDelete(array(
			'arrId' => array(),
		));
	 */
	protected function _setDelete($arr)
	{
		global $classDb;
		global $classFile;
		global $classPluginAccountingInit;

		global $varsRequest;

		$dbh = $classDb->getHandle();
		global $varsPluginAccountingEntity;

		$array = $arr['arrId'];

		if (!$array) {
			$varsRequest['query']['jsonSearch']['flagReload'] = 0;
			$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
			$this->_setSearch(array('flag' => 1));
		}

		$this->_checkTableUpdateId(array(
			'varsTable' => &$varsPluginAccountingEntity,
			'arrId'     => $array,
		));

		try {
			$dbh->beginTransaction();

			foreach ($array as $key => $value) {

				$rows = $classDb->getSelect(array(
					'idModule' => 'accounting',
					'strTable' => 'accountingEntity',
					'arrLimit' => array(),
					'arrOrder' => array(),
					'arrWhere' => array(
						array(
							'flagType'      => 'num',
							'strColumn'     => 'id',
							'flagCondition' => 'eq',
							'value'         => $value,
						),
					),
				));
				if (!$rows['numRows']) {
					continue;
				}

				$strNation = ucwords($rows['arrRows'][0]['strNation']);

				$this->_setDeleteAccountArrCommaIdChild(array(
					'id'      => $value,
					'strType' => 'Entity',
				));
				$this->_setDeleteAccountIdChild(array(
					'id'      => $value,
					'strType' => 'EntityCurrent',
				));
				$arrayTable = array(
					'accountingEntityDepartment',
					'accountingLog',
					'accountingFile',
					'accountingLogFile',
					'accountingAccountEntity',
					'accountingAccountMemo',
					'accountingCash',
					'accountingCashValue',
					'accountingLogCash',
					'accountingLogCashDefer',
					'accountingBanks',
					'accountingLogBanks',
					'accountingLogBanksAccount',
				);
				foreach ($arrayTable as $keyTable => $valueTable) {
					$classDb->deleteRow(array(
						'idModule' => 'accounting',
						'strTable'  => $valueTable,
						'arrWhere'  => array(
							array(
								'flagType'      => 'num',
								'strColumn'     => 'idEntity',
								'flagCondition' => 'eq',
								'value'         => $value,
							),
						),
					));
				}

				$arrayTable = array(
					'accountingEntity',
					'accountingEntityDepartmentFSValue',
					'accountingFS',
					'accountingFSValue',
					'accountingFSId',
					'accountingSubAccountTitle',
					'accountingSubAccountTitleValue',
					'accountingLogCalc',
					'accountingFixedAssets',
					'accountingLogFixedAssets',
					'accountingBudget',
					'accountingSummaryStatement',
					'accountingBreakEvenPoint',
					'accountingDetailedAccount',
					'accountingNotesFS',
					'accountingLogImport',
					'accountingLogImportRetry',
					'accountingLogHouse',
					'accountingLogMail',
				);
				foreach ($arrayTable as $keyTable => $valueTable) {
					$classDb->deleteRow(array(
						'idModule' => 'accounting',
						'strTable'  => $valueTable . $strNation,
						'arrWhere'  => array(
							array(
								'flagType'      => 'num',
								'strColumn'     => 'idEntity',
								'flagCondition' => 'eq',
								'value'         => $value,
							),
						),
					));
				}
				$classDb->deleteRow(array(
					'idModule'  => 'accounting',
					'strTable'  => 'accountingEntity',
					'arrWhere'  => array(
						array(
							'flagType'      => 'num',
							'strColumn'     => 'id',
							'flagCondition' => 'eq',
							'value'         => $value,
						),
					),
				));
			}
			$array = array('adminMemo', 'account', 'authority', 'banks', 'entity', 'log', 'logHouse', 'logFile', 'fs', 'subAccountTitle', 'breakEvenPoint', 'summaryStatement', 'budget', 'fixedAssets', 'notesFS', 'detailedAccount');
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

		$classPluginAccountingInit->updateInitAccounts();
		$classPluginAccountingInit->updateInitAccount();
		$classPluginAccountingInit->updateInitPreference();
		$classPluginAccountingInit->updateInitAuthority();
		$classPluginAccountingInit->updateInitEntity();
		$classPluginAccountingInit->updateInitAccountsEntity();

		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
		$this->_setSearch(array('flag' => 1));
	}

	/**
		$this->_setDeleteAccountArrCommaIdChild(array(
			'id'      => '',
			'strType' => '',
		))
	 */
	protected function _setDeleteAccountArrCommaIdChild($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $classEscape;

		$id = $arr['id'];
		$strId = ',' . $id . ',';
		$strType = ucwords($arr['strType']);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingAccount',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'arrWhere' => array(
				array(
					'flagType'      => '',
					'strColumn'     => 'arrCommaId' . $strType,
					'flagCondition' => 'like',
					'value'         => $strId,
				),
			),
		));

		$array = &$rows['arrRows'];
		foreach ($array as $key => $value) {
			$arrCommaId = $classEscape->removeCommaArray(array(
				'data'      => $value['arrCommaId' . $strType],
				'idTarget'  => $id,
			));
			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingAccount',
				'arrColumn' => array('arrCommaId' . $strType),
				'arrWhere'  => array(
					array(
						'flagType'      => '',
						'strColumn'     => 'arrCommaId' . $strType,
						'flagCondition' => 'like',
						'value'         => $strId,
					),
				),
				'arrValue'  => array($arrCommaId),
			));
		}
	}

	/**
		$this->_setDeleteAccountId(array(
			'id'      => '',
			'strType' => '',
			'strNation'  => '',
		))
	 */
	protected function _setDeleteAccountId($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();


		$id = $arr['id'];
		$strType = ucwords($arr['strType']);
		$strNation = ($arr['strNation'])? ucwords($arr['strNation']) : '';

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accounting' . $strType . $strNation,
			'arrLimit' => array(),
			'arrOrder' => array(),
			'arrWhere' => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idEntity',
					'flagCondition' => 'eq',
					'value'         => $id,
				),
			),
		));
		$array = &$rows['arrRows'];
		foreach ($array as $key => $value) {
			$this->_setDeleteAccountIdChild(array(
				'id'      => $value['id'],
				'strType' => $strType,
				'strNation'  => $strNation,
			));
		}
	}

	/**
		$this->_setDeleteAccountIdChild(array(
			'id'      => '',
			'strType' => '',
		))
	 */
	protected function _setDeleteAccountIdChild($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$id = $arr['id'];
		$strType = ucwords($arr['strType']);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingAccount',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'arrWhere' => array(
				array(
					'flagType'      => '',
					'strColumn'     => 'id' . $strType,
					'flagCondition' => 'eq',
					'value'         => $id,
				),
			),
		));

		$array = &$rows['arrRows'];
		foreach ($array as $key => $value) {
			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingAccount',
				'arrColumn' => array('id' . $strType, 'numFiscalPeriodCurrent'),
				'arrWhere'  => array(
					array(
						'flagType'      => '',
						'strColumn'     => 'id' . $strType,
						'flagCondition' => 'eq',
						'value'         => $id,
					),
				),
				'arrValue'  => array(null, null),
			));
		}
	}
}
