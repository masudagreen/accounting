<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_LogImportItemPreference extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference' => 'logWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/logImportItemPreference.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/logImportItemPreference.php',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;
		global $classCheck;
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

		$flagCurrentFlagNow = $this->_getCurrentFlagNow();
		if (preg_match("/^(done)$/", $flagCurrentFlagNow)) {
			$this->_sendOld();
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
						var_dump(__CLASS__ . '/' .__FUNCTION__);
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
				'strSql'   => 'idEntity = ?',
				'arrValue' => array($idEntity),
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
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;


		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$this->_setJs(array(
			'pathVars'        => $this->_extSelf['pathVarsJs'],
			'pathTpl'         => $this->_extSelf['pathTplJs'],
			'arrFolder' => array(
				array(
					'flagType'  => 'folder',
					'strTable'  => 'accountingAccountMemo',
					'strColumn' => 'jsonLogImportItemPreferenceNaviFolder',
					'flagEntity'  => 1,
					'flagAccount' => 1,
				),
				array(
					'flagType'  => 'folderAdmin',
					'strTable'  => 'accountingAdminMemo',
					'strColumn' => 'jsonLogImportItemPreferenceNaviFolder',
					'flagEntity'  => 1,
					'flagAccount' => 0,
				),
			),
			'strTableSearch'    => 'accountingAccountMemo',
			'strColumnSearch'   => 'jsonLogImportItemPreferenceNaviSearch',
			'flagEntitySearch'  => 1,
			'flagAccountSearch' => 1,
			'arrSearch'       => array(
				'idModule'  => 'accounting',
				'numLotNow' => 0,
				'strTable'  => 'accountingLogImport' . $strNation,
				'arrJoin'   => array(),
				'arrOrder'  => array(
					'strColumn' => 'id',
					'flagDesc'  => 1,
				),
				'insCurrent' => $this,
				'arrWhere'  => array(
				),
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

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$vars = $this->_updateFolderAdmin($vars);

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$vars['portal']['varsDetail']['templateDetail'] = $this->_updateVarsTemplateDetail((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$array = $arr['arrFolder'];
		foreach ($array as $key => $value) {
			$vars['portal']['varsNavi']['varsFolder'][$value['flagType']]['varsDetail'] = $this->_getMemo(array(
				'strTable'    => $value['strTable'],
				'strColumn'   => $value['strColumn'],
				'flagEntity'  => $value['flagEntity'],
				'flagAccount' => $value['flagAccount'],
			));
			if (!$vars['portal']['varsNavi']['varsFolder'][$value['flagType']]['varsDetail']) {
				$varsDetail = $vars['portal']['varsNavi']['templateFolder']['varsDetail']['templateDetail']['dir'];
				$vars['portal']['varsNavi']['varsFolder'][$value['flagType']]['varsDetail'][] = $varsDetail;
			}
		}

		$vars['portal']['varsNavi']['search']['varsDetail']['varsMyRecord']['varsFormList']['varsDetail'] = $this->_getMemo(array(
			'strTable'    => $arr['strTableSearch'],
			'strColumn'   => $arr['strColumnSearch'],
			'flagEntity'  => $arr['flagEntitySearch'],
			'flagAccount' => $arr['flagAccountSearch'],
		));

		$rows = $this->getSearch($arr['arrSearch']);

		$vars = $this->_updateSearch(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'rows'     => $rows,
		));

		$vars['varsRule'] = $varsItem;

		$json = json_encode($vars);
		$classSmarty->assign('varsLoad', $json);

		$classSmarty->assign('numNews', $this->getNumNews());
		$contents = $classSmarty->fetch($arr['pathTpl']);

		$this->sendJs(array(
			'data' => $contents,
		));
	}

	/**
		(array(
			'vars' => $vars,
		))
	 */
	protected function _getVarsItem($arr)
	{
		global $varsPluginAccountingAccount;

		$varsDepartment = $this->_getVarsDepartment(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrSubAccountTitle = $this->_getVarsSubAccountTitle(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrAccountTitle = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'numFiscalPeriod'    => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrayFSList = $this->_getFSList(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsFlagAttest = $this->_getVarsFlagAttest(array(
			'vars' => &$arr['vars'],
		));

		$varsFlagReverse = $this->_getVarsFlagReverse(array(
			'vars' => &$arr['vars'],
		));

		$data = array(
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'arrAccountTitle'    => $arrAccountTitle,
			'varsDepartment'     => $varsDepartment,
			'varsEntityNation'   => $varsEntityNation,
			'arrayFSList'        => $arrayFSList,
			'varsFlagAttest'     => $varsFlagAttest,
			'varsFlagReverse'    => $varsFlagReverse,
		);

		return $data;

	}

	/**
		(array(
			'vars'             => $vars,
			'varsItem'         => $arr['varsItem'],
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _getVarsFlagReverse($arr)
	{
		$arrayNew = array();
		$array = &$arr['vars']['portal']['varsDetail']['templateDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'FlagReverse') {
				$arrayOption = $value['arrayOption'];
				foreach ($arrayOption as $keyOption => $valueOption) {
					$arrayNew['arrStrTitle'][$valueOption['value']] = $valueOption;
				}
				return $arrayNew;
			}
		}
	}

	/**
		(array(
			'vars'             => $vars,
			'varsItem'         => $arr['varsItem'],
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _getVarsFlagAttest($arr)
	{
		$arrayNew = array();
		$array = &$arr['vars']['portal']['varsDetail']['templateDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'FlagAttest') {
				$arrayOption = $value['arrayOption'];
				foreach ($arrayOption as $keyOption => $valueOption) {
					$arrayNew['arrStrTitle'][$valueOption['value']] = $valueOption;
				}
				return $arrayNew;
			}
		}
	}

	/**
		(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		))
	 */
	protected function _updateVars($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;
		global $varsPluginAccountingAccountsEntity;
		global $varsPluginAccountingAuthority;

		$idAccount = $varsPluginAccountingAccount['id'];
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$idAuthority = $varsPluginAccountingAccountsEntity[$idAccount][$idEntity]['idAuthority'];
		$varsAuthority = $varsPluginAccountingAuthority[$idAuthority];

		if (!$varsAuthority['flagAllInsert']) {
			$arr['vars']['portal']['varsDetail']['varsStart']['varsEdit']['flagAddUse'] = 0;
		}

		$arr['vars']['portal']['varsNavi']['search']['varsDetail']['varsSearchItem']['templateDetail']['restOption']['fs']
			= $arr['varsItem']['arrAccountTitle']['arrSelectTag'];

		return $arr['vars'];
	}

	/**
		(array(
			'vars'             => $vars,
			'varsItem'         => $arr['varsItem'],
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _updateVarsTemplateDetail($arr)
	{
		$arrayNew = array();
		$array = &$arr['vars']['portal']['varsDetail']['templateDetail'];
		foreach ($array as $key => $value) {
			$method = '_updateVarsTemplateDetail' . $value['id'];
			if (method_exists($this, $method)) {
				$value = $this->$method(array(
					'value'    => $value,
					'vars'     => $arr['vars'],
					'varsItem' => $arr['varsItem'],
				));
			}
			$arrayNew[] = $value;
		}

		return $arrayNew;
	}

	/**
		(array(
			'value'    => $value,
			'vars'     => $arr['vars'],
			'varsItem' => $arr['varsItem'],
		))
	 */
	protected function _updateVarsTemplateDetailIdDepartment($arr)
	{
		if (!$arr['varsItem']['varsDepartment']['arrSelectTag']) {
			return $arr['value'];
		}
		$arrSelectTag = $arr['varsItem']['varsDepartment']['arrSelectTag'];
		array_unshift($arrSelectTag, $arr['value']['varsTmpl']['varsNone']);
		$arr['value']['arrayOption'] = $arrSelectTag;

		return $arr['value'];
	}

	/**
		(array(
			'value'    => $value,
			'vars'     => $arr['vars'],
			'varsItem' => $arr['varsItem'],
		))
	 */
	protected function _updateVarsTemplateDetailIdAccountTitle($arr)
	{
		$arr['value']['arrayOption'] = $arr['varsItem']['arrAccountTitle']['arrSubAccountSelectTag'];

		return $arr['value'];
	}

	/**
	 *
	 */
	protected function _updateSearch($arr)
	{
		global $varsAccount;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingAccountsEntity;
		global $varsPluginAccountingAuthority;

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
		foreach ($array as $key => $value) {
			$varsTmpl = $vars['portal']['varsList']['templateDetail'];
			$varsTmpl['id'] = $value['idLogImport'];
			$varsTmpl['vars']['idTarget'] = $value['idLogImport'];
			$varsTmpl['numSort'] = (int) $key;

			if ($varsAccount['jsonStampCheck']['accountingLogImportJpn'] < $value['stampRegister']) {
				$flag = 1;

			} else {
				$varsTmpl['strClassLoad'] = '';

			}
			$varsTmpl['strTitle'] = $value['strTitle'];
			$varsTmpl['stampRegister'] = (int) $value['stampRegister'];
			$varsTmpl['stampUpdate'] = (int) $value['stampUpdate'];
			$varsTmpl['arrSpaceStrTag'] = preg_replace('/^ /', '', $value['arrSpaceStrTag']);
			$varsTmpl['varsAuthority'] = $dataAuthority;

			if (!$varsAuthority['flagAllDelete']) {
				$varsTmpl['flagDefault'] = 1;
			}

			$varsTmpl['flagCheckboxUse'] = ($varsTmpl['flagDefault'])? 0 : 1;
			$varsTmpl['idAccountTitle'] = $value['idAccountTitle'];
			$varsTmpl['idSubAccountTitle'] = $value['idSubAccountTitle'];
			$varsTmpl['idDepartment'] = $value['idDepartment'];
			$varsTmpl['flagAttest'] = $value['flagAttest'];
			$varsTmpl['flagReverse'] = $value['flagReverse'];


			$varsTmpl['varsColumnDetail']['id'] = $varsTmpl['id'];
			$varsTmpl['varsColumnDetail']['strTitle'] = $value['strTitle'];
			$varsTmpl['varsColumnDetail']['stampRegister'] = (int) $value['stampRegister'];
			$varsTmpl['varsColumnDetail']['stampUpdate'] = (int) $value['stampUpdate'];

			$varsTmpl['varsColumnDetail']['flagAttest']
				= $arr['varsItem']['varsFlagAttest']['arrStrTitle'][$value['flagAttest']]['strTitle'];

			$varsTmpl['varsColumnDetail']['flagReverse']
			= $arr['varsItem']['varsFlagReverse']['arrStrTitle'][$value['flagReverse']]['strTitle'];

			$varsTmpl['varsColumnDetail']['idAccountTitle']
				 = $arr['varsItem']['arrAccountTitle']['arrStrTitle'][$value['idAccountTitle']]['strTitleFS'];
			if ($value['idAccountTitle'] && !$varsTmpl['varsColumnDetail']['idAccountTitle']) {
				$varsTmpl['varsColumnDetail']['idAccountTitle'] = $vars['varsItem']['strLostColumn'];
				$varsTmpl['strClassFont'] = $vars['varsItem']['strClassNone'];
			}

			$varsTmpl['varsColumnDetail']['idSubAccountTitle']
				 = $arr['varsItem']['arrSubAccountTitle']['arrStrTitle'][$value['idAccountTitle']][$value['idSubAccountTitle']]['strTitle'];
			if ($value['idSubAccountTitle'] && !$varsTmpl['varsColumnDetail']['idSubAccountTitle']) {
				$varsTmpl['varsColumnDetail']['idSubAccountTitle'] = $vars['varsItem']['strLostColumn'];
				$varsTmpl['strClassFont'] = $vars['varsItem']['strClassNone'];
			}
			if (!$value['idSubAccountTitle']) {
				$varsTmpl['varsColumnDetail']['idSubAccountTitle'] = $vars['varsItem']['strNone'];
			}

			$varsTmpl['varsColumnDetail']['idDepartment']
				 = $arr['varsItem']['varsDepartment']['arrStrTitle'][$value['idDepartment']]['strTitle'];
			if ($value['idDepartment'] && !$varsTmpl['varsColumnDetail']['idDepartment']) {
				$varsTmpl['varsColumnDetail']['idDepartment'] = $vars['varsItem']['strLostColumn'];
				$varsTmpl['strClassFont'] = $vars['varsItem']['strClassNone'];
			}
			if (!$value['idDepartment']) {
				$varsTmpl['varsColumnDetail']['idDepartment'] = $vars['varsItem']['strNone'];
			}


			$varsTmpl['varsScheduleDetail']['stamp'] = (int) $value['stampRegister'];
			$arrayNew[$num] = $varsTmpl;
			$num++;
		}
		$vars['portal']['varsList']['varsPage']['varsStatus']['numRows'] = $rows['numRows'];
		$vars['portal']['varsList']['varsDetail'] = $arrayNew;

		if ($flag) {
			$this->_setDbStampCheck(array(
				'strColumnAccount'    => 'accountingLogImportJpn',
				'strColumnPreference' => 'accounts',
			));
		}

		return $vars;
	}

	/**
	 *
	 */
	protected function _iniNaviSearchSave()
	{
		$this->_setNaviSearchSave(array(
			'pathVars'    => $this->_extSelf['pathVarsJs'],
			'strTable'    => 'accountingAccountMemo',
			'strColumn'   => 'jsonLogImportItemPreferenceNaviSearch',
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
			'strColumn' => 'jsonLogImportItemPreferenceNaviSearch',
			'flagEntity'  => 1,
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

		$vars = $this->_updateVars(array(
			'vars' => $vars
		));

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
				$this->_updateDbPreferenceStamp(array('strColumn' => 'accounts'));

			} else {
				$this->_updateDbPreferenceStamp(array('strColumn' => 'adminMemo'));
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
				'strTable'  => $arr['strTable'],
				'strColumn' => $arr['strColumn'],
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
			'strColumn' => 'jsonLogImportItemPreferenceNaviSearch',
			'flagEntity'  => 1,
			'flagAccount' => 1,
		));
	}

	/**
	 *
	 */
	protected function _iniNaviFolderSave()
	{
		$this->_setNaviFolderSave(array(
			'pathVars'  => $this->_extSelf['pathVarsJs'],
			'strTable'  => 'accountingAccountMemo',
			'strColumn' => 'jsonLogImportItemPreferenceNaviFolder',
			'flagEntity'  => 1,
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

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

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
	protected function _iniNaviFolderReload()
	{
		global $varsPluginAccountingAccount;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingAccount['stampUpdate'],
				'flagSearch'  => 0,
			));
		}

		$this->_setNaviFolderReload(array(
			'pathVars'  => $this->_extSelf['pathVarsJs'],
			'strTable'  => 'accountingAccountMemo',
			'strColumn' => 'jsonLogImportItemPreferenceNaviFolder',
			'flagEntity'  => 1,
			'flagAccount' => 1,
		));
	}

	/**
	 *
	 */
	protected function _iniNaviFolderAdminSave()
	{
		$this->_setNaviFolderSave(array(
			'pathVars'  => $this->_extSelf['pathVarsJs'],
			'strTable'  => 'accountingAdminMemo',
			'strColumn' => 'jsonLogImportItemPreferenceNaviFolder',
			'flagEntity'  => 1,
			'flagAccount' => 0,
		));
	}

	/**
	 *
	 */
	protected function _iniNaviFolderAdminReload()
	{
		global $varsPluginAccountingPreference;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingPreference['jsonStampUpdate']['adminMemo'],
				'flagSearch'  => 0,
			));
		}

		$this->_setNaviFolderReload(array(
			'pathVars'  => $this->_extSelf['pathVarsJs'],
			'strTable'  => 'accountingAdminMemo',
			'strColumn' => 'jsonLogImportItemPreferenceNaviFolder',
			'flagEntity'  => 1,
			'flagAccount' => 0,
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
	 * array(
	 * 	'flag' => int
	 * )
	 */
	protected function _setSearch($arr)
	{
		global $varsRequest;
		global $varsPluginAccountingPreference;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingPreference['jsonStampUpdate']['entity'],
				'flagSearch'  => 1,
			));
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
			'strTable'   => 'accountingLogImport' . $strNation,
			'arrOrder'   => $varsRequest['query']['jsonSearch']['ph']['arrOrder'],
			'insCurrent' => $this,
			'arrWhere'   => $varsRequest['query']['jsonSearch']['ph']['arrWhere'],
		));

		$vars = $this->_updateSearch(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'rows'     => $rows,
		));

		$this->sendVars(array(
			'flag'    => ($arr['flag'])? $arr['flag'] : 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'numRows'    => $rows['numRows'],
				'varsDetail' => $vars['portal']['varsList']['varsDetail'],
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
		$this->_iniSearchDetail();
	}

	/**
	 *
	 */
	protected function _iniSearchDetail()
	{
		global $varsRequest;
		global $varsPluginAccountingPreference;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;


		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingPreference['jsonStampUpdate']['entity'],
				'flagSearch'  => 1,
			));
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

		$rows = $this->getSearch(array(
			'idModule'  => 'accounting',
			'numLotNow' => 0,
			'strTable'  => 'accountingLogImport' . $strNation,
			'arrOrder'  => array(),
			'flagAnd'   => 1,
			'arrWhere'  => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idLogImport',
					'flagCondition' => 'eq',
					'value'         => $varsRequest['query']['jsonValue']['idTarget'],
				),
			),
			'insCurrent'  => $this,
		));
		$vars = $this->_updateSearch(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'rows'     => $rows,
		));

		$this->sendVars(array(
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $vars['portal']['varsList']['varsDetail'][0],
		));
	}

	/**
	 *
	 */
	protected function _iniDetailDelete()
	{
		global $varsRequest;

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
		global $classCheck;

		$dbh = $classDb->getHandle();
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;


		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'delete',
			'idTarget'      => $this->_extSelf['idPreference'],
		));
		if (!$flag) {
			$this->_sendOldError();
		}

		$array = $arr['arrId'];
		$flag = $classCheck->checkValueFormat(array(
			'flagType' => 'num',
			'flagArr'  => 1,
			'value'    => $array,
		));

		if ($flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
			}
			exit;
		}

		try {
			$dbh->beginTransaction();
			foreach ($array as $key => $value) {
				$classDb->deleteRow(array(
					'idModule'  => 'accounting',
					'strTable'  => 'accountingLogImport' . $strNation,
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
							'strColumn'     => 'idLogImport',
							'flagCondition' => 'eq',
							'value'         => $value,
						),
					),
				));
			}

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
}
