<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Base_ModuleEditor extends Code_Else_Core_Base_Module
{
	protected $_childSelf = array(
		'pathTplJs'  => 'else/core/base/js/moduleEditor.js',
		'pathVarsJs' => 'back/tpl/vars/else/core/base/<strLang>/js/moduleEditor.php',
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
	protected function _iniDetailEdit()
	{
		global $classDb;
		global $classInit;
		$dbh = $classDb->getHandle();

		global $varsAccount;
		global $varsAccounts;
		global $varsModule;
		global $varsRequest;

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
			'idTarget' => $varsRequest['query']['jsonValue']['idTarget'],
		));

		$arrValue = $this->_checkValueDetail($arrValue);

		$arrSql = $this->_updateDbValue(array(
			'arr'      => $arrValue['arr'],
			'flagEdit' => 1,
		));

		if (!$varsModule[$varsRequest['query']['jsonValue']['idTarget']]) {
			$varsRequest['query']['jsonSearch']['flagReload'] = 0;
			$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
			$this->_setSearch(array('flag' => 40));
		}

		try {
			$dbh->beginTransaction();

			$classDb->updateRow(array(
				'idModule'  => 'base',
				'strTable'  => 'baseModule',
				'arrColumn' => $arrSql['arrColumn'],
				'arrWhere'  => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'id',
						'flagCondition' => 'eq',
						'value'         => $varsRequest['query']['jsonValue']['idTarget'],
					),
				),
				'arrValue'  => $arrSql['arrValue'],
			));

			$rows = $classDb->getSelect(array(
				'idModule'  => 'base',
				'strTable'  => 'baseModule',
				'arrLimit' => array(),
				'arrOrder' => array(),
				'arrWhere'  => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'id',
						'flagCondition' => 'eq',
						'value'         => $varsRequest['query']['jsonValue']['idTarget'],
					),
				),
			));
			$arrayNew = $rows['arrRows'][0];

			$rows = $classDb->getSelect(array(
				'idModule'  => 'base',
				'strTable'  => 'baseAccount',
				'arrLimit' => array(),
				'arrOrder' => array(),
				'arrWhere' => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idModule',
						'flagCondition' => 'eq',
						'value'         => $varsRequest['query']['jsonValue']['idTarget'],
					),
				),
			));
			$array = $rows['arrRows'];
			$arrayId = array();
			$num = 0;
			foreach ($array as $key => $value) {
				$arrayId[$num] = $value['id'];
				$num++;
			}
			$this->_loopPluginUpdate(array(
				'varsModulePast' => $varsModule[$value['idModule']],
				'varsModuleNew'  => $arrayNew,
				'arrId'          => $arrayId,
				'idModule'       => $varsRequest['query']['jsonValue']['idTarget'],
			));
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
		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$this->_iniSearchDetail();
	}

	/**
		$this->_loopPluginUpdate(array(
			'varsModulePast' => $varsModule[$value['idModule']],
			'varsModuleNew'  => $arrayNew,
			'arrId'          => $arrayId,
			'idModule'       => $varsRequest['query']['jsonValue']['idTarget'],
		));
	 */
	protected function _loopPluginUpdate($arr)
	{
		global $varsPreference;
		global $classEscape;

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
			$classCall->loop(array(
				'flagType'       => 'accountStatus',
				'flagStatus'     => 'updateModule',
				'varsModuleNew'  => $arr['varsModuleNew'],
				'varsModulePast' => $arr['varsModulePast'],
				'arrId'          => $arr['arrId'],
			));
		}
	}

	/**
		$this->_checkValueDetail($arrValue);
	 */
	protected function _checkValueDetail($arrValue)
	{
		global $varsPreference;
		global $varsRequest;

		global $classEscape;

		$array = $varsRequest['query']['jsonValue']['vars']['ArrModule'];
		$arrCommaIdModuleUser = array();
		$arrCommaIdModuleAdmin = array();

		$flag = 0;
		foreach ($array as $key => $value) {
			if (!$varsPreference['jsonModule'][$key]) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__);
				}
				exit;
			}
			if ($key == 'base' && $value == 'Admin') {
				$flag = 1;
			}
		}
		if ($flag) {
			foreach ($array as $key => $value) {
				$arrCommaIdModuleAdmin[] = $key;
				$arrCommaIdModuleUser[] = $key;
			}
		} else {
			foreach ($array as $key => $value) {
				if ($value == 'Admin') {
					$arrCommaIdModuleAdmin[] = $key;
					$arrCommaIdModuleUser[] = $key;

				} elseif ($value == 'User') {
					$arrCommaIdModuleUser[] = $key;

				}
			}
		}
		$arrValue['arr']['arrCommaIdModuleAdmin'] = $classEscape->joinCommaArray(array('arr' => $arrCommaIdModuleAdmin));
		$arrValue['arr']['arrCommaIdModuleUser'] = $classEscape->joinCommaArray(array('arr' => $arrCommaIdModuleUser));

		return $arrValue;
	}

	/**
		$this->_updateDbValue(array(
			'arr'      => array(),
			'flagEdit' => 0,
		));
	 */
	protected function _updateDbValue($arr)
	{
		global $classEscape;

		$tm = TIMESTAMP;
		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arr['arr']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));
		$strTitle = $arr['arr']['strTitle'];
		$arrCommaIdModuleAdmin = $arr['arr']['arrCommaIdModuleAdmin'];
		$arrCommaIdModuleUser = $arr['arr']['arrCommaIdModuleUser'];

		if ($arr['flagEdit']) {
			$data = array(
				'arrColumn' => array('strTitle', 'arrCommaIdModuleUser', 'arrCommaIdModuleAdmin', 'arrSpaceStrTag'),
				'arrValue' => array($strTitle, $arrCommaIdModuleUser, $arrCommaIdModuleAdmin, $arrSpaceStrTag),
			);

		} else {
			$data = array(
				'arrColumn' => array('stampRegister', 'stampUpdate', 'strTitle', 'arrCommaIdModuleUser', 'arrCommaIdModuleAdmin', 'arrSpaceStrTag'),
				'arrValue' => array($tm, $tm, $strTitle, $arrCommaIdModuleUser, $arrCommaIdModuleAdmin, $arrSpaceStrTag),
			);
		}

		return $data;
	}

	/**
	 *
	 */
	protected function _iniDetailAdd()
	{
		global $classDb;
		global $varsModule;

		global $classInit;
		global $varsRequest;
		$dbh = $classDb->getHandle();

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

		$arrValue = $this->_checkValueDetail($arrValue);


		$arrSql = $this->_updateDbValue(array(
			'arr' => $arrValue['arr']
		));

		try {
			$dbh->beginTransaction();

			$classDb->insertRow(array(
				'idModule'  => 'base',
				'strTable'  => 'baseModule',
				'arrColumn' => $arrSql['arrColumn'],
				'arrValue'  => $arrSql['arrValue'],
			));

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
		global $varsModule;

		$array = &$varsModule;
		foreach ($array as $key => $value) {
			$flag = 0;
			if ($arr['idTarget']) {
				if ($value['strTitle'] == $arr['strTitle'] && $arr['idTarget'] != $value['id']) {
					$flag = 1;
				}

			} else {
				if ($value['strTitle'] == $arr['strTitle']) {
					$flag = 1;
				}

			}

			if ($flag) {
				$this->sendVars(array(
					'flag'    => 'strTitle',
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));
			}
		}
	}

}
