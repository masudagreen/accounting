<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Base_Module extends Code_Else_Core_Base_Base
{
	protected $_extSelf = array(
		'pathTplJs'  => 'else/core/base/js/module.js',
		'pathVarsJs' => 'back/tpl/vars/else/core/base/<strLang>/js/module.php',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;
		global $classCheck;

		$flag = $classCheck->checkModule(array(
			'idModule' => $varsRequest['query']['module'],
			'flagType' => 'Admin'
		));
		if (!$flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__);
			}
			exit;
		}

		if ($varsRequest['query']['child']) {
			$str = ucwords($varsRequest['query']['child']);
			$path = PATH_BACK_CLASS_ELSE_CORE_BASE . 'Module' . $str . ".php";
			if (!file_exists($path)) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__);
				}
				exit;
			}
			require_once($path);
			$strClass = 'Code_Else_Core_Base_Module' . $str;
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
	 *
	 */
	protected function _iniJs()
	{
		$this->_setJs(array(
			'pathVars'        => $this->_extSelf['pathVarsJs'],
			'pathTpl'         => $this->_extSelf['pathTplJs'],
			'arrFolder' => array(),
			'arrSearch'       => array(
				'idModule'  => 'base',
				'numLotNow' => 0,
				'strTable'  => 'baseModule',
				'arrOrder'  => array(
					'strColumn' => 'id',
					'flagDesc'  => 1,
				),
				'arrWhere'  => array(),
			),
		));
	}

	/**
	 *
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
			'pathVars'  => $this->_extSelf['pathVarsJs'],
			'strTable'  => 'baseAccountMemo',
			'strColumn' => 'jsonModuleNaviSearch',
		));
	}

	/**
	 *
	 */
	protected function _iniNaviSearchDelete()
	{
		$this->_setNaviSearchDelete(array(
			'strTable'  => 'baseAccountMemo',
			'strColumn' => 'jsonModuleNaviSearch',
		));
	}


	/**
	 *
	 */
	protected function _iniNaviSearchReload()
	{
		global $varsAccount;
		global $varsRequest;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsAccount['stampUpdate'],
				'flagSearch'  => 0,
			));
		}

		$this->_setNaviSearchReload(array(
			'pathVars'  => $this->_extSelf['pathVarsJs'],
			'strTable'  => 'baseAccountMemo',
			'strColumn' => 'jsonModuleNaviSearch',
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
		global $varsPreference;
		global $varsAccount;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPreference['jsonStampUpdate']['module'],
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
			$varsRequest['query']['jsonSearch']['ph']['arrOrder'] = array(
				'strColumn' => 'id',
				'flagDesc'  => 1,
			);
		}
		$rows = $this->getSearch(array(
			'idModule'  => 'base',
			'numLotNow' => $varsRequest['query']['jsonSearch']['numLotNow'],
			'strTable'  => 'baseModule',
			'arrOrder'  => $varsRequest['query']['jsonSearch']['ph']['arrOrder'],
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
				'varsHtml'   => $vars['portal']['varsList']['varsHtml'],
				'varsDetail' => $vars['portal']['varsList']['varsDetail'],
			),
		));
	}

	/**
		$this->_updateVars(array(
			'vars' => array(),
		));
	 */
	protected function _updateVars($arr)
	{
		global $varsPreference;

		$arrStrModule = $this->getStrModuleTitle();
		$varsModule = $this->_updateSearchJsonModule(array(
			'arrAdmin'      => array(),
			'arrUser'       => array(),
			'arrStrModule'  => $arrStrModule,
		));

		$array = $arr['vars'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'JsonModule') {
				$array[$key]['varsFormCheck']['jsonModule'] = $varsModule;
			}
		}

		return $array;
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

		$arrStrModule = $this->getStrModuleTitle();

		$array = $rows['arrRows'];
		$arrayNew = array();
		$varsModule = array();
		$num = 0;
		$flag = 0;
		foreach ($array as $key => $value) {
			$varsTmpl = $vars['portal']['varsList']['templateDetail'];
			$varsTmpl['id'] = $value['id'];
			$varsTmpl['vars']['idTarget'] = $value['id'];
			$varsTmpl['numSort'] = (int) $key;

			if ($varsAccount['jsonStampCheck']['baseModule'] < $value['stampRegister']) {
				$flag = 1;

			} else {
				$varsTmpl['strClassLoad'] = '';

			}

			$varsTmpl['strTitle'] = $value['strTitle'];
			$varsTmpl['stampRegister'] = $value['stampRegister'];
			$varsTmpl['stampUpdate'] = $value['stampUpdate'];

			$varsModule = $this->_updateSearchJsonModule(array(
				'arrAdmin' => $classEscape->splitCommaArrayData(array(
					'data' => $value['arrCommaIdModuleAdmin'],
				)),
				'arrUser'  => $classEscape->splitCommaArrayData(array(
					'data' => $value['arrCommaIdModuleUser'],
				)),
				'arrStrModule'  => $arrStrModule,
			));
			$varsTmpl['jsonModule'] = $varsModule;

			$varsTmpl['arrSpaceStrTag'] = preg_replace('/^ /', '', $value['arrSpaceStrTag']);
			$varsTmpl['flagDefault'] = (int) $value['flagDefault'];
			$varsTmpl['flagCheckboxUse'] = ($value['flagDefault'])? 0 : 1;

			$varsTmpl['varsColumnDetail']['id'] = $varsTmpl['id'];
			$varsTmpl['varsColumnDetail']['strTitle'] = $value['strTitle'];
			$varsTmpl['varsColumnDetail']['stampRegister'] = $value['stampRegister'];
			$varsTmpl['varsColumnDetail']['stampUpdate'] = $value['stampUpdate'];

			$varsTmpl['vars']['id'] = $value['id'];
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

			$vars['portal']['varsDetail']['templateDetail'] = $this->_updateVars(array(
				'vars' => $vars['portal']['varsDetail']['templateDetail'],
			));

			if ($flag) {
				$this->_setDbStampCheck(array(
					'strColumnAccount'    => 'baseModule',
					'strColumnPreference' => 'accounts',
				));
			}
		}

		return $vars;
	}


	/**
		$this->_updateSearchJsonModule(array(
			'arrAdmin' => array(),
			'arrUser'  => array(),
			'arrStrModule' => array(),
		));
	 */
	protected function _updateSearchJsonModule($arr)
	{
		global $varsPreference;

		$array = $arr['arrAdmin'];
		$arrayNew = array();
		$varsBase = array();
		$arrayCheck = array();
		$num = 0;
		foreach ($array as $key => $value) {
			$row = array();
			$row['idModule'] = $value;
			$row['value'] = 'Admin';
			$row['strModule'] = $arr['arrStrModule'][$value];
			$arrayCheck[$value] = 1;
			if ($value == 'base') {
				$varsBase = $row;
				continue;
			}
			$arrayNew[$num] = $row;
			$num++;
		}
		$array = $arr['arrUser'];
		foreach ($array as $key => $value) {
			if (!$arrayCheck[$value]) {
				$row = array();
				$row['idModule'] = $value;
				$row['value'] = 'User';
				$row['strModule'] = $arr['arrStrModule'][$value];
				$arrayCheck[$value] = 1;
				if ($value == 'base') {
					$varsBase = $row;
					continue;
				}
				$arrayNew[$num] = $row;
				$num++;
			}
		}
		$array = $varsPreference['jsonModule'];
		foreach ($array as $key => $value) {
			if (!$arrayCheck[$key]) {
				$row = array();
				$row['idModule'] = $key;
				$row['value'] = '';
				$row['strModule'] = $arr['arrStrModule'][$key];
				if ($key == 'base') {
					$varsBase = $row;
					continue;
				}
				$arrayNew[$num] = $row;
				$num++;
			}
		}
		array_unshift($arrayNew, $varsBase);

		return $arrayNew;
	}

	/**
	 *
	 */
	protected function _iniSearchDetail()
	{
		global $varsRequest;
		global $varsPreference;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPreference['jsonStampUpdate']['module'],
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
			$varsRequest['query']['jsonSearch']['ph']['arrOrder'] = array(
				'strColumn' => 'id',
				'flagDesc'  => 1,
			);
		}
		$rows = $this->getSearch(array(
			'idModule'  => 'base',
			'numLotNow' => $varsRequest['query']['jsonSearch']['numLotNow'],
			'strTable'  => 'baseModule',
			'arrOrder'  => $varsRequest['query']['jsonSearch']['ph']['arrOrder'],
			'arrWhere'  => $varsRequest['query']['jsonSearch']['ph']['arrWhere'],
		));

		$vars = $this->_updateSearch(array(
			'vars' => $vars,
			'rows' => $rows,
		));

		$varsTarget = $vars;

		$rowsTarget = $this->getSearch(array(
			'idModule' => 'base',
			'numLotNow' => 0,
			'strTable'  => 'baseModule',
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
		$this->_setDelete(array(
			'arrId' => array(),
		));
	 */
	protected function _setDelete($arr)
	{
		global $classDb;
		global $varsAccounts;
		global $varsModule;
		global $classInit;
		$dbh = $classDb->getHandle();

		$array = $arr['arrId'];

		$this->_checkTableUpdateId(array(
			'varsTable' => &$varsModule,
			'arrId'     => $array,
		));

		$idModuleNew = 2;


		try {
			$dbh->beginTransaction();


			foreach ($array as $key => $value) {
				if ($varsModule[$value]['flagDefault']) {
					continue;
				}
				$classDb->deleteRow(array(
					'idModule'  => 'base',
					'strTable'  => 'baseModule',
					'arrWhere'  => array(
						array(
							'flagType'      => 'num',
							'strColumn'     => 'id',
							'flagCondition' => 'eq',
							'value'         => $value,
						),
					),
				));

				$this->_loopPluginDelete(array(
					'idModulePast' => $value,
					'idModuleNew'  => $idModuleNew,
				));

				$classDb->updateRow(array(
					'idModule'  => 'base',
					'strTable'  => 'baseAccount',
					'arrColumn' => array('idModule'),
					'arrWhere'  => array(
						array(
							'flagType'      => 'num',
							'strColumn'     => 'idModule',
							'flagCondition' => 'eq',
							'value'         => $value,
						),
					),
					'arrValue'  => array($idModuleNew),
				));
			}

			$this->updateDbPreferenceStamp(array('strColumn' => 'account'));
			$this->updateDbPreferenceStamp(array('strColumn' => 'module'));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$classInit->updateVarsAll(array(
			'vars'     => &$varsModule,
			'strVars'  => 'varsModule',
			'strTable' => 'baseModule',
		));

		$classInit->updateVarsAll(array(
			'vars'     => &$varsAccounts,
			'strVars'  => 'varsAccounts',
			'strTable' => 'baseAccount',
		));

		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
		$this->_setSearch(array('flag' => 1));
	}

	/**
			'idModulePast' => $value,
			'idModuleNew'  => $idModuleNew,
	 */
	protected function _loopPluginDelete($arr)
	{
		global $varsPreference;
		global $varsAccounts;
		global $classEscape;
		global $varsModule;

		$array = $varsPreference['jsonModule'];
		foreach ($array as $key => $value) {
			$strDir = $key;
			$strFile = ucwords($key);
			$path = PATH_BACK_CLASS_ELSE_PLUGIN . $strDir . '/' . $strFile . '.php';
			if (!file_exists($path)) {
				continue;
			}
			require_once($path);
			$strClass = 'Code_Else_Plugin_' . $strFile . '_' . $strFile;
			$classCall = new $strClass;

			$arrayAccount = $varsAccounts;
			$arrayNew = array();
			$num = 0;

			foreach ($arrayAccount as $keyAccount => $valueAccount) {
				if ($arr['idModulePast'] == $valueAccount['idModule']) {
					$arrayNew[$num] = $valueAccount['id'];
				}
				$num++;
			}

			$classCall->loop(array(
				'flagType'       => 'accountStatus',
				'flagStatus'     => 'updateModule',
				'varsModuleNew'  => $varsModule[$arr['idModuleNew']],
				'varsModulePast' => $varsModule[$arr['idModulePast']],
				'arrId'          => $arrayNew,
			));
		}
	}

	/**
	 *
	 */
	protected function _iniDetailReload()
	{
		global $varsModule;
		global $varsRequest;

		if (!$varsModule[$varsRequest['query']['jsonValue']['idTarget']]) {
			$varsRequest['query']['jsonSearch']['flagReload'] = 0;
			$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
			$this->_setSearch(array('flag' => 40));
		}

		$this->_iniSearchDetail();
	}

	/**
	 *
	 */
	protected function _iniDetailDelete()
	{
		global $varsModule;
		global $varsRequest;

		if (!$varsModule[$varsRequest['query']['jsonValue']['idTarget']]) {
			$varsRequest['query']['jsonSearch']['flagReload'] = 0;
			$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
			$this->_setSearch(array('flag' => 1));
		}

		$this->_setDelete(array(
			'arrId' => array($varsRequest['query']['jsonValue']['idTarget']),
		));
	}

}
