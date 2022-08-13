<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_AccountEntityAuthority extends Code_Else_Plugin_Accounting_Accounting
{
	protected $_extSelf = array(
		'pathTplJs'  => 'else/plugin/accounting/js/accountEntityAuthority.js',
		'pathVarsJs' => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/accountEntityAuthority.php',
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

			$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/' . $str . ".php";
			$strClass = 'Code_Else_Plugin_Accounting_' . $str;

			if (!file_exists($path)) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
				}
				exit;
			}
			require_once($path);

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
		$flagAuthority = $classCheck->checkModuleAuthority(array(
			'idModule' => 'accounting',
		));
		$strArrCommaIdEntity = '%,' . $idEntity . ',%';
		$arrCommaIdEntity = $strArrCommaIdEntity;
		$flagAdmin = 1;

		if ($flagAuthority == 'webmaster' || $flagAuthority == 'admin') {
			$array = array(
				'strSql'   => 'idEntity = ? && (arrCommaIdEntity like ? || flagAdmin = ?)',
				'arrValue' => array($idEntity, $arrCommaIdEntity, $flagAdmin),
			);
			return $array;

		} elseif ($flagAuthority == 'user') {
			return 0;

		}

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

	protected function _setJs($arr)
	{
		global $varsPluginAccountingAccount;
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
	protected function _updateSearch($arr)
	{
		global $varsRequest;
		global $varsAccount;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingAuthority;
		global $varsPluginAccountingAccess;
		global $varsPluginAccountingAccount;

		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$idEntity  = $varsPluginAccountingAccount['idEntityCurrent'];
		$strCheckStamp = 'accountingAccountEntityAuthority_' . $idEntity . '_' . $numFiscalPeriod;

		global $classEscape;
		global $classHtml;

		$vars = $arr['vars'];
		$rows = $arr['rows'];

		$array = $vars['portal']['varsDetail']['templateDetail'];

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];

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

			$varsTmpl['idAccount'] = $value['id'];
			$varsTmpl['strTitle'] = $value['strCodeName'];
			$varsTmpl['stampRegister'] = $value['stampRegister'];
			$varsTmpl['stampUpdate'] = $value['stampUpdate'];
			$varsTmpl['arrSpaceStrTag'] = preg_replace('/^ /', '', $value['arrSpaceStrTag']);
			$varsTmpl['flagAdmin'] = (int) $this->_checkModuleAdmin(array('idAccount' => $value['idAccount'],));

			$varsTmpl['strAuthority'] = $varsPluginAccountingAuthority[$value['idAuthority']]['strTitle'];
			$varsTmpl['strAccess'] = $varsPluginAccountingAccess[$idEntity][$value['idAccess']]['strTitle'];

			$varsTmpl['idAuthority'] = $value['idAuthority'];
			$varsTmpl['idAccess'] = $value['idAccess'];

			$varsTmpl['varsColumnDetail']['id'] = $varsTmpl['id'];
			$varsTmpl['varsColumnDetail']['strTitle'] = $value['strCodeName'];
			$varsTmpl['varsColumnDetail']['stampRegister'] = $value['stampRegister'];
			$varsTmpl['varsColumnDetail']['stampUpdate'] = $value['stampUpdate'];

			$varsTmpl['varsColumnDetail']['strAuthority'] = $varsPluginAccountingAuthority[$value['idAuthority']]['strTitle'];
			$varsTmpl['varsColumnDetail']['strAccess'] = $varsPluginAccountingAccess[$idEntity][$value['idAccess']]['strTitle'];

			if (!$this->_checkModuleAdmin(array('idAccount' => $value['idAccount'],))) {
				$varsTmpl['varsColumnDetail']['flagAdmin'] = '-';
			}

			$varsTmpl['vars']['id'] = $varsTmpl['id'];
			$varsTmpl['vars']['strTitle'] = $value['strCodeName'];
			$varsTmpl['vars']['idAuthority'] = $value['idAuthority'];
			$varsTmpl['vars']['idAccess'] = $value['idAccess'];
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
	protected function _iniNaviSearchSave()
	{
		$this->_setNaviSearchSave(array(
			'pathVars'    => $this->_extSelf['pathVarsJs'],
			'strTable'    => 'accountingAccountMemo',
			'strColumn'   => 'jsonAccountEntityAuthorityNaviSearch',
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
			'strColumn' => 'jsonAccountEntityAuthorityNaviSearch',
			'flagEntity'  => 1,
			'flagAccount' => 1,
		));
	}

	/**
	 *
	 */
	protected function _iniNaviSearchReload()
	{
		global $varsPluginAccountingPreference;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingPreference['jsonStampUpdate']['accounts'],
				'flagSearch'  => 0,
			));
		}

		$this->_setNaviSearchReload(array(
			'pathVars'  => $this->_extSelf['pathVarsJs'],
			'strTable'  => 'accountingAccountMemo',
			'strColumn' => 'jsonAccountEntityAuthorityNaviSearch',
			'flagEntity'  => 1,
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
		global $varsPluginAccountingAccount;

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
			'idModule'   => 'accounting',
			'numLotNow'  => $varsRequest['query']['jsonSearch']['numLotNow'],
			'strTable'   => 'accountingAccountEntity',
			'arrJoins'   => array(
				'strLeftTable'  => 'accountingAccountEntity',
				'strLeftKey'    => 'idAccount',
				'strRightTable' => 'baseAccount',
				'strRightKey'   => 'id',
				'strThirdTable' => 'accountingAccount',
				'strThirdKey'   => 'idAccount',
			),
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
				'stampTarget' => $varsPluginAccountingPreference['jsonStampUpdate']['accounts'],
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
			'idModule'   => 'accounting',
			'numLotNow'  => $varsRequest['query']['jsonSearch']['numLotNow'],
			'strTable'   => 'accountingAccountEntity',
			'arrJoins'   => array(
				'strLeftTable'  => 'accountingAccountEntity',
				'strLeftKey'    => 'idAccount',
				'strRightTable' => 'baseAccount',
				'strRightKey'   => 'id',
				'strThirdTable' => 'accountingAccount',
				'strThirdKey'   => 'idAccount',
			),
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

}
