<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_AccountEntity extends Code_Else_Plugin_Accounting_Accounting
{
	protected $_extSelf = array(
		'pathTplJs' => '',
		'pathVarsJs'  => '',
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

		if (!$varsRequest['query']['child'] == 'EntityChoice') {
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
			'arrFolder' => array(
				array(
					'flagType'  => 'folder',
					'strTable'  => 'accountingAccountMemo',
					'strColumn' => 'jsonAccountNaviFolder',
					'flagEntity'  => 0,
					'flagAccount' => 1,
				),
			),
			'strTableSearch'    => 'accountingAccountMemo',
			'strColumnSearch'   => 'jsonAccountNaviSearch',
			'flagEntitySearch'  => 0,
			'flagAccountSearch' => 1,
			'arrSearch'       => array(
				'idModule'  => 'accounting',
				'numLotNow' => 0,
				'strTable'  => 'accountingAccountEntity',
				'arrJoins'   => array(
					'strLeftTable'  => 'accountingAccountEntity',
					'strLeftKey'    => 'idAccount',
					'strRightTable' => 'baseAccount',
					'strRightKey'   => 'id',
					'strThirdTable' => 'accountingAccount',
					'strThirdKey'   => 'idAccount',
				),
				'arrOrder'  => array(
					'strColumn' => 'baseAccount.id',
					'flagDesc'  => 1,
				),
				'insCurrent' => $this,
				'arrWhere'  => array(),
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
		global $varsPluginAccountingAccounts;
		global $varsAccounts;

		global $classHtml;
		global $classEscape;
		global $varsPluginAccountingAccount;

		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$idEntity  = $varsPluginAccountingAccount['idEntityCurrent'];
		$strCheckStamp = 'accountingAccount_' . $idEntity . '_' . $numFiscalPeriod;

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

			if ($varsAccount['jsonStampCheck'][$strCheckStamp] < $value['stampRegister']) {
				$flag = 1;

			} else {
				$varsTmpl['strClassLoad'] = '';
			}

			$varsTmpl['strTitle'] = $value['strCodeName'];
			$varsTmpl['stampRegister'] = $value['stampRegister'];
			$varsTmpl['stampUpdate'] = $value['stampUpdate'];
			$varsTmpl['arrSpaceStrTag'] = preg_replace('/^ /', '', $value['arrSpaceStrTag']);
			$varsTmpl['idAccount'] = $value['idAccount'];
			$varsTmpl['flagAdmin'] = $this->_checkModuleAdmin(array('idAccount' => $value['idAccount'],));

			$varsEntity = $this->_updateSearchArrCommaIdEntity($value['arrCommaIdEntity']);
			$varsTmpl['varsEntity'] = $varsEntity;

			$varsTmpl['varsColumnDetail']['id'] = $varsTmpl['id'];
			$varsTmpl['varsColumnDetail']['strTitle'] = $value['strCodeName'];
			$varsTmpl['varsColumnDetail']['stampRegister'] = $value['stampRegister'];
			$varsTmpl['varsColumnDetail']['stampUpdate'] = $value['stampUpdate'];
			if (!$this->_checkModuleAdmin(array('idAccount' => $value['idAccount'],))) {
				$varsTmpl['varsColumnDetail']['flagAdmin'] = '';
			}
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
	 *
	 */
	protected function _updateSearchArrCommaIdEntity($arrCommaIdEntity)
	{
		global $classEscape;
		global $varsPluginAccountingEntity;

		if (!$arrCommaIdEntity) {
			return array();
		}

		$arrId = $classEscape->splitCommaArrayData(array('data' => $arrCommaIdEntity));

		$num = 0;
		$arrayNew = array();
		foreach ($arrId as $keyId => $valueId) {
			$data = array();
			$data['strTitle'] = $varsPluginAccountingEntity[$valueId]['strTitle'];
			$data['id'] = $valueId;
			$arrayNew[$num] = $data;
			$num++;
		}

		return $arrayNew;
	}

	/**
	 *
	 */
	protected function _iniNaviSearchSave()
	{
		$this->_setNaviSearchSave(array(
			'pathVars'    => $this->_extSelf['pathVarsJs'],
			'strTable'    => 'accountingAccountMemo',
			'strColumn'   => 'jsonAccountNaviSearch',
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
			'strColumn' => 'jsonAccountNaviSearch',
			'flagEntity'  => 0,
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
			'strColumn' => 'jsonAccountNaviSearch',
			'flagEntity'  => 0,
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
			'strColumn' => 'jsonAccountNaviFolder',
			'flagEntity'  => 0,
			'flagAccount' => 1,
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
			'strColumn' => 'jsonAccountNaviFolder',
			'flagEntity'  => 0,
			'flagAccount' => 1,
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

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingPreference['jsonStampUpdate']['accounts'],
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
				'strColumn' => 'baseAccount.id',
				'flagDesc'  => 1,
			);
		}

		$rows = $this->getSearch(array(
			'idModule'  => 'accounting',
			'numLotNow' => $varsRequest['query']['jsonSearch']['numLotNow'],
			'strTable'  => 'accountingAccount',
			'arrJoins'   => array(
				'strLeftTable'  => 'accountingAccountEntity',
				'strLeftKey'    => 'idAccount',
				'strRightTable' => 'baseAccount',
				'strRightKey'   => 'id',
				'strThirdTable' => 'accountingAccount',
				'strThirdKey'   => 'idAccount',
			),
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
	protected function _iniListReload()
	{
		$this->_setSearch(array('flag' => 1));
	}

	/**
	 *
	 */
	protected function _iniDetailReload()
	{
		global $varsPluginAccountingAccounts;
		global $varsRequest;

		if (!$varsPluginAccountingAccounts[$varsRequest['query']['jsonValue']['idTarget']]) {
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
				'stampTarget' => $varsPluginAccountingPreference['jsonStampUpdate']['account'],
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
				'strColumn' => 'baseAccount.id',
				'flagDesc'  => 1,
			);
		}

		$rows = $this->getSearch(array(
			'idModule'  => 'accounting',
			'numLotNow' => $varsRequest['query']['jsonSearch']['numLotNow'],
			'strTable'  => 'accountingAccount',
			'arrJoins'   => array(
				'strLeftTable'  => 'accountingAccountEntity',
				'strLeftKey'    => 'idAccount',
				'strRightTable' => 'baseAccount',
				'strRightKey'   => 'id',
				'strThirdTable' => 'accountingAccount',
				'strThirdKey'   => 'idAccount',
			),
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
			'strTable'  => 'accountingAccountEntity',
			'arrJoins'   => array(
				'strLeftTable'  => 'accountingAccountEntity',
				'strLeftKey'    => 'idAccount',
				'strRightTable' => 'baseAccount',
				'strRightKey'   => 'id',
				'strThirdTable' => 'accountingAccount',
				'strThirdKey'   => 'idAccount',
			),
			'arrOrder'  => array(),
			'arrWhere'  => array(array(
				'flagType'      => 'num',
				'strColumn'     => 'baseAccount.id',
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
}
