<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Base_Account extends Code_Else_Core_Base_Base
{
	protected $_extSelf = array(
		'pathTplJs'  => 'else/core/base/js/account.js',
		'pathVarsJs' => 'back/tpl/vars/else/core/base/<strLang>/js/account.php',
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
			'flagType'  => 'Admin'
		));

		if (!$flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}

		if ($varsRequest['query']['child']) {
			$str = ucwords($varsRequest['query']['child']);
			$path = PATH_BACK_CLASS_ELSE_CORE_BASE . 'Account' . $str . ".php";
			if (!file_exists($path)) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
				}
				exit;
			}
			require_once($path);
			$strClass = 'Code_Else_Core_Base_Account' . $str;
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
				'idModule'  => 'base',
				'numLotNow' => 0,
				'strTable'  => 'baseAccount',
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
			'strColumn' => 'jsonAccountNaviSearch',
		));
	}

	/**
	 *
	 */
	protected function _iniNaviSearchDelete()
	{
		$this->_setNaviSearchDelete(array(
			'strTable'  => 'baseAccountMemo',
			'strColumn' => 'jsonAccountNaviSearch',
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
			'strColumn' => 'jsonAccountNaviSearch',
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
				'stampTarget' => $varsPreference['jsonStampUpdate']['accounts'],
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
			'strTable'  => 'baseAccount',
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
				'numRows'    => $vars['portal']['varsList']['varsPage']['varsStatus']['numRows'],
				'varsDetail' => $vars['portal']['varsList']['varsDetail'],
				'varsHtml'   => $vars['portal']['varsList']['varsHtml'],
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
		global $classDb;
		global $varsModule;
		global $varsTerm;

		$array = $arr['vars'];
		$arrayNew = array();
		foreach ($array as $key => $value) {
			if ($value['id'] == 'StrPassword') {
				$value['strExplain'] = str_replace('<%replace%>', $varsPreference['numPassword'], $value['strExplain']);
				$arrayError = $value['arrayError'];
				foreach ($arrayError as $keyError => $valueError) {
					if ($valueError['flagCheck'] == 'min') {
						$arrayError[$keyError]['num'] = $varsPreference['numPassword'];
						break;
					}
				}
				$value['arrayError'] = $arrayError;
			}
			$arrayNew[] = $value;

		}

		return $arrayNew;
	}

	/**
	 *
	 */
	protected function _updateSearch($arr)
	{
		global $classHtml;

		global $varsRequest;
		global $varsAccount;

		global $varsModule;
		global $varsTerm;

		global $classEscape;

		$vars = $arr['vars'];
		$rows = $arr['rows'];

		$array = $rows['arrRows'];
		$arrayNew = array();
		$num = 0;
		$flag = 0;
		foreach ($array as $key => $value) {
			$varsTmpl = $vars['portal']['varsList']['templateDetail'];
			$varsTmpl['id'] = $value['id'];
			$varsTmpl['vars']['idTarget'] = $value['id'];
			$varsTmpl['numSort'] = (int) $key;

			if ($varsAccount['jsonStampCheck']['baseAccount'] < $value['stampRegister']) {
				$flag = 1;

			} else {
				$varsTmpl['strClassLoad'] = '';

			}

			if ($varsAccount['id'] == $value['id']) {
				$value['flagDefault'] = 1;
			}

			$varsTmpl['strTitle'] = $value['strCodeName'];
			$varsTmpl['stampRegister'] = $value['stampRegister'];
			$varsTmpl['stampUpdate'] = $value['stampUpdate'];
			$varsTmpl['idTerm'] = $value['idTerm'];
			$varsTmpl['idModule'] = $value['idModule'];
			$varsTmpl['arrSpaceStrTag'] = preg_replace('/^ /', '', $value['arrSpaceStrTag']);
			$varsTmpl['flagDefault'] = (int) $value['flagDefault'];
			$varsTmpl['flagCheckboxUse'] = ($value['flagDefault'])? 0 : 1;

			$varsTmpl['varsColumnDetail']['id'] = $varsTmpl['id'];
			$varsTmpl['varsColumnDetail']['strCodeName'] = $value['strCodeName'];
			$varsTmpl['varsColumnDetail']['stampRegister'] = $value['stampRegister'];
			$varsTmpl['varsColumnDetail']['stampUpdate'] = $value['stampUpdate'];
			$varsTmpl['varsColumnDetail']['strTerm'] = $varsTerm[$value['idTerm']]['strTitle'];
			$varsTmpl['varsColumnDetail']['strModule'] = $varsModule[$value['idModule']]['strTitle'];
			$varsTmpl['varsColumnDetail']['idLogin'] = $value['idLogin'];
			$varsTmpl['varsColumnDetail']['strMailPc'] = $value['strMailPc'];

			$varsTmpl['vars']['id'] = $value['id'];
			$varsTmpl['vars']['strTerm'] = $value['idTerm'];
			$varsTmpl['vars']['strModule'] = $value['idModule'];
			$varsTmpl['vars']['strCodeName'] = $value['strCodeName'];
			$varsTmpl['vars']['idLogin'] = $value['idLogin'];
			$varsTmpl['vars']['strMailPc'] = $value['strMailPc'];

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
					'strColumnAccount'    => 'baseAccount',
					'strColumnPreference' => 'accounts',
				));
			}
		}

		return $vars;
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
				'stampTarget' => $varsPreference['jsonStampUpdate']['accounts'],
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
			'strTable'  => 'baseAccount',
			'arrOrder'  => $varsRequest['query']['jsonSearch']['ph']['arrOrder'],
			'arrWhere'  => $varsRequest['query']['jsonSearch']['ph']['arrWhere'],
		));

		$vars = $this->_updateSearch(array(
			'vars' => $vars,
			'rows' => $rows,
		));

		$varsTarget = $vars;

		$rowsTarget = $this->getSearch(array(
			'idModule'  => 'base',
			'numLotNow' => 0,
			'strTable'  => 'baseAccount',
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
		global $classInit;
		global $classRebuild;

		$dbh = $classDb->getHandle();
		global $varsAccounts;
		global $varsAccount;
		global $varsPreference;
		$array = $arr['arrId'];

		$this->_checkTableUpdateId(array(
			'varsTable' => &$varsAccounts,
			'arrId'     => $array,
		));
		foreach ($array as $key => $value) {
			if ($varsAccount['id'] == $value) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
				}
				exit;
			}
		}

		try {
			$dbh->beginTransaction();

			$this->_loopPluginDelete(array(
				'arrId' => $arr['arrId'],
			));

			foreach ($array as $key => $value) {
				$classDb->insertRow(array(
					'idModule'  => 'base',
					'strTable'  => 'baseAccountId',
					'arrColumn' => array('id', 'strCodeName'),
					'arrValue'  => array($value, $varsAccounts[$value]['strCodeName']),
				));
				$classDb->deleteRow(array(
					'idModule'  => 'base',
					'strTable'  => 'baseAccount',
					'arrWhere'  => array(
						array(
							'flagType'      => 'num',
							'strColumn'     => 'id',
							'flagCondition' => 'eq',
							'value'         => $value,
						),
					),
				));
				$arrayTable = array('baseAccountMemo', 'basePublish', 'baseLock', 'baseApplyChange', 'baseApplyForgot', 'baseSession', 'baseLoginPassword');
				foreach ($arrayTable as $keyTable => $valueTable) {
					$classDb->deleteRow(array(
						'idModule' => 'base',
						'strTable'  => $valueTable,
						'arrWhere'  => array(
							array(
								'flagType'      => 'num',
								'strColumn'     => 'idAccount',
								'flagCondition' => 'eq',
								'value'         => $value,
							),
						),
					));
				}


			}
			$this->updateDbPreferenceStamp(array('strColumn' => 'account'));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

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
		(array('arrIdAccount' => array()));
	 */
	protected function _loopPluginDelete($arr)
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
				'flagType'   => 'accountStatus',
				'flagStatus' => 'delete',
				'arrId'      => $arr['arrId'],
			));
		}
	}

	/**
	 *
	 */
	protected function _iniDetailReload()
	{
		global $varsAccounts;
		global $varsRequest;

		if (!$varsAccounts[$varsRequest['query']['jsonValue']['idTarget']]) {
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
		global $varsAccounts;
		global $varsRequest;

		if (!$varsAccounts[$varsRequest['query']['jsonValue']['idTarget']]) {
			$varsRequest['query']['jsonSearch']['flagReload'] = 0;
			$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
			$this->_setSearch(array('flag' => 1));
		}

		$this->_setDelete(array(
			'arrId' => array($varsRequest['query']['jsonValue']['idTarget']),
		));
	}

}
