<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Access extends Code_Else_Plugin_Accounting_Accounting
{
	protected $_extSelf = array(
		'pathTplJs'                     => 'else/plugin/accounting/js/access.js',
		'pathVarsJs'                    => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/access.php',
		'pathVarsPreference'            => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/<strNation>/portal.php',
		'pathVarsPreferenceCorporation' => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/<strNation>/<numYearSheet>/<flagCorporation>/portal.php',
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
		$flagDefault = 1;

		$array = array(
			'strSql'   => 'idEntity = ? || flagDefault = ?',
			'arrValue' => array($idEntity, $flagDefault),
		);

		return $array;
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
				'strTable'  => 'accountingAccess',
				'arrOrder'  => array(
					'strColumn' => 'id',
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

		$vars = $this->_updateVars(array(
			'vars' => $vars
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

	 */
	private function _getVarsPreferencePath()
	{
		global $varsPluginAccountingAccount;

		$path = $this->_extSelf['pathVarsPreference'];

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		if ($varsEntityNation['flagCorporation'] == 2) {
			$path = $this->_extSelf['pathVarsPreferenceCorporation'];
			$path = str_replace('<numYearSheet>', $varsEntityNation['numYearSheet'], $path);
			$path = str_replace('<flagCorporation>', 'public', $path);
		}

		return $path;
	}



	/**
	 *  don't confuse this is private function
	 */
	private function _getVarsEntityNation($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = $this->_getVarsNation();
		$strNation = ucwords($strNation);

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		if ($arr['idEntity']) {
			$idEntity = $arr['idEntity'];
		}

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingEntity' . $strNation,
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idEntity',
					'flagCondition' => 'eq',
					'value'         => $idEntity,
				),
				array(
					'flagType'      => '',
					'strColumn'     => 'numFiscalPeriod',
					'flagCondition' => 'eq',
					'value'         => $arr['numFiscalPeriod'],
				),
			),
		));

		if (!$rows['numRows']) {
			return array();
		}

		$array = $rows['arrRows'][0];
		foreach ($array as $key => $value) {
			if (preg_match("/^json/", $key)) {
				$array[$key] = $value;

			} else {
				$array[$key] = (int) $value;
			}
		}

		return $array;
	}


	/**
	 *
	 */
	protected function _updateVars($arr)
	{
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];

		$path = $this->_getVarsPreferencePath();

		$vars = $this->_getVars(array(
			'path'      => $path,
			'strLang'   => STR_LANG,
			'strNation' => $varsPluginAccountingEntity[$idEntity]['strNation'],
		));

		$arrayBlock = $this->_getTreeBlock(array(
			'vars'     => $vars,
			'idTarget' => 'User',
		));

		$arrayBlock = $this->_setTreeId(array(
			'idParent' => '-',
			'vars'     => $arrayBlock,
		));

		$varsItem = $this->_getArrSelectOption(array(
			'arrStrTitle'  => array(),
			'arrSelectTag' => array(),
			'vars'         => $arrayBlock,
		));

		$array =  $arr['vars']['portal']['varsDetail']['templateDetail'];
		if ($array) {
			foreach ($array as $key => $value) {
				if ($value['id'] == 'JsonData') {
					$arrayOption = $varsItem['arrSelectTag'];
					foreach ($arrayOption as $keyOption => $valueOption) {
						if ($varsItem['arrStrTitle'][$valueOption['value']]['flagAllUse']) {
							$arrayOption[$keyOption]['strTitle'] .= $value['varsTmpl']['strNeed'];
						}
					}
					$array[$key]['arrayOption'] = $arrayOption;
				}
			}
			$arr['vars']['portal']['varsDetail']['templateDetail'] = $array;
		}

		$arr['vars']['varsItem'] = $varsItem;

		return $arr['vars'];
	}

	/**
		(array(
			'arrStrTitle'  => array(),
			'vars'         => array(),
			'arrSelectTag' => array(),
		))
	 */
	protected function _getArrSelectOption($arr)
	{
		$arrSelectTag = &$arr['arrSelectTag'];
		$arrStrTitle = &$arr['arrStrTitle'];

		$array = &$arr['vars'];
		foreach ($array as $key => $value) {

			$data = array(
				'strTitle'      => $value['strTitle'],
				'flagAccessUse' => (int) $value['vars']['flagAccessUse'],
				'flagAllUse'    => (int) $value['vars']['flagAllUse'],
			);

			if ((int) $value['vars']['flagAccessUse']) {
				$arr['arrStrTitle'][$value['vars']['idTarget']] = $data;
			}

			$arrLevel = preg_split("/-/", $value['id']);
			$num = count($arrLevel) - 2;
			$arrLevel = array();
			for ($i = 0 ; $i < $num; $i++) {
				$arrLevel[$i] = '';
			}
			$str =  ' ' . join('.', $arrLevel) . ' ';
			$strTitle = $str . $value['strTitle'];

			if (!$value['vars']['flagAccessUse']) {
				$arr['arrSelectTag'][] = array(
					'strTitle'     => $strTitle,
					'value'        => '',
					'flagDisabled' => 1,
				);

			} elseif ($value['vars']['flagAccessUse']) {
				$arr['arrSelectTag'][] = array(
					'strTitle'     => $strTitle,
					'value'        => $value['vars']['idTarget'],
				);
			}

			if ($value['child']) {
				$data = $this->_getArrSelectOption(array(
					'vars'          => $array[$key]['child'],
					'arrSelectTag'  => $arr['arrSelectTag'],
					'arrStrTitle'   => $arr['arrStrTitle'],
				));
				$array[$key]['child'] = $data['vars'];
				$arrSelectTag =  $data['arrSelectTag'];
				$arrStrTitle =  $data['arrStrTitle'];
			}
		}

		return $arr;
	}

	/**
		(array(

		))
	 */
	protected function _getTreeBlock($arr)
	{
		$array = &$arr['vars'];
		foreach ($array as $key => $value) {
			if ($value['id'] == $arr['idTarget']) {
				return $array[$key]['child'];
			}
			if ($value['child']) {
				$flagArr = $this->_getTreeBlock(array(
					'vars'     => $array[$key]['child'],
					'idTarget' => $arr['idTarget'],
				));
				if ($flagArr) {
					return $flagArr;
				}
			}
		}

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
				'stampTarget' => $varsPluginAccountingPreference['jsonStampUpdate']['access'],
				'flagSearch'  => 1,
			));
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$vars = $this->_updateVars(array(
			'vars' => $vars
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
			'idModule'   => 'accounting',
			'numLotNow'  => $varsRequest['query']['jsonSearch']['numLotNow'],
			'strTable'   => 'accountingAccess',
			'arrOrder'   => $varsRequest['query']['jsonSearch']['ph']['arrOrder'],
			'arrWhere'   => $varsRequest['query']['jsonSearch']['ph']['arrWhere'],
			'insCurrent' => $this,
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

		global $classEscape;
		global $classHtml;
		global $varsPluginAccountingAccount;

		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$idEntity  = $varsPluginAccountingAccount['idEntityCurrent'];
		$strCheckStamp = 'accountingAccess_' . $idEntity . '_' . $numFiscalPeriod;

		$vars = $arr['vars'];
		$rows = $arr['rows'];

		$array = $rows['arrRows'];
		$arrayNew = array();
		$num = 0;
		$flag = 0;
		foreach ($array as $key => $value) {
			$varsTmpl = $vars['portal']['varsList']['templateDetail'];
			$varsTmpl['id'] = $value['idAccess'];
			$varsTmpl['vars']['idTarget'] = $value['idAccess'];
			$varsTmpl['numSort'] = (int) $key;
			$varsTmpl['strTitle'] = $value['strTitle'];
			$varsTmpl['stampRegister'] = $value['stampRegister'];
			$varsTmpl['stampUpdate'] = $value['stampUpdate'];

			if ($varsAccount['jsonStampCheck'][$strCheckStamp] < $value['stampRegister']) {
				$flag = 1;

			} else {
				$varsTmpl['strClassLoad'] = '';
			}

			$varsTmpl['jsonData'] = $this->_getVarsJsonData(array(
				'varsItem'    => $vars['varsItem'],
				'arr'         => $value['jsonData'],
				'id'          => $value['id'],
				'flagDefault' => ($value['flagDefault'])? 1 : 0,
			));

			$varsTmpl['arrSpaceStrTag'] = preg_replace('/^ /', '', $value['arrSpaceStrTag']);
			$varsTmpl['flagDefault'] = (int) $value['flagDefault'];
			$varsTmpl['flagCheckboxUse'] = ($value['flagDefault'])? 0 : 1;

			$varsTmpl['varsColumnDetail']['id'] = $varsTmpl['id'];
			$varsTmpl['varsColumnDetail']['strTitle'] = $value['strTitle'];
			$varsTmpl['varsColumnDetail']['stampRegister'] = $value['stampRegister'];
			$varsTmpl['varsColumnDetail']['stampUpdate'] = $value['stampUpdate'];
			$varsTmpl['varsColumnDetail']['jsonData'] = $this->_getStrJson(array(
				'varsItem' => $vars['varsItem'],
				'arr'      => $varsTmpl['jsonData'],
				'strNone'  => $vars['strNone'],
			));

			$varsTmpl['vars']['id'] = $varsTmpl['id'];
			$varsTmpl['vars']['strTitle'] = $value['strTitle'];
			$varsTmpl['vars']['arrSpaceStrTag'] = $classEscape->splitSpaceArrayData(array('data' => $varsTmpl['arrSpaceStrTag']));

			$varsTmpl['varsScheduleDetail']['stamp'] = $value['stampRegister'];
			$arrayColumnDetail = &$varsTmpl['varsColumnDetail'];
			foreach ($arrayColumnDetail as $keyColumnDetail => $valueColumnDetail) {
				if (is_null($valueColumnDetail)) {
					$arrayColumnDetail[$keyColumnDetail] = '';
				}
			}
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
	protected function _getVarsJsonData($arr)
	{
		$arrayNew = array();
		$array = $arr['arr'];
		foreach ($array as $key => $value) {
			$arrayNew[$key] = (int) $value;
		}

		if ($arr['flagDefault']) {
			$array = $arr['varsItem']['arrSelectTag'];
			foreach ($array as $key => $value) {
				if (!(int) $value['flagDisabled']) {
					$arrayNew[$value['value']] = ($arr['id'] == 1)? 1 : 0;
				}
			}
		}

		return $arrayNew;
	}

	/**
	 *
	 */
	protected function _getStrJson($arr)
	{
		$arrayNew = array();
		$array = $arr['arr'];
		foreach ($array as $key => $value) {
			if ((int) $value) {
				$arrayNew[] = $arr['varsItem']['arrStrTitle'][$key]['strTitle'];
			}
		}

		if (!$arrayNew) {
			return $arr['strNone'];
		}

		$str =  join('<br>', $arrayNew);

		return $str;
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
			'strColumn'   => 'jsonAccessNaviSearch',
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
			'strColumn' => 'jsonAccessNaviSearch',
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
			'strColumn' => 'jsonAccessNaviSearch',
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
		global $varsPluginAccountingAccess;
		global $varsRequest;
		global $varsPluginAccountingAccount;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];

		if (!$varsPluginAccountingAccess[$idEntity][$varsRequest['query']['jsonValue']['idTarget']]) {
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
		global $varsPluginAccountingAccount;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingPreference['jsonStampUpdate']['access'],
				'flagSearch'  => 0,
			));
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$vars = $this->_updateVars(array(
			'vars' => $vars
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
			'idModule'   => 'accounting',
			'numLotNow'  => $varsRequest['query']['jsonSearch']['numLotNow'],
			'strTable'   => 'accountingAccess',
			'arrOrder'   => $varsRequest['query']['jsonSearch']['ph']['arrOrder'],
			'arrWhere'   => $varsRequest['query']['jsonSearch']['ph']['arrWhere'],
			'insCurrent' => $this,
		));

		$vars = $this->_updateSearch(array(
			'vars' => $vars,
			'rows' => $rows,
		));

		$varsTarget = $vars;

		$rowsTarget = $this->getSearch(array(
			'idModule'  => 'accounting',
			'numLotNow' => 0,
			'strTable'  => 'accountingAccess',
			'arrOrder'  => array(),
			'flagAnd'  => 1,
			'arrWhere'  => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idAccess',
					'flagCondition' => 'eq',
					'value'         => $varsRequest['query']['jsonValue']['idTarget'],
				),
			),
			'insCurrent' => $this,
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
		global $varsPluginAccountingAccess;
		global $varsPluginAccountingAccount;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];

		$array = $arr['arrId'];

		try {
			$dbh->beginTransaction();

			$this->_setDeleteAccess(array(
				'arrIdLost' => $array,
			));

			foreach ($array as $key => $value) {
				if (!$varsPluginAccountingAccess[$idEntity][$value]) {
					continue;
				}
				if ($varsPluginAccountingAccess[$idEntity][$value]['flagDefault']) {
					continue;
				}
				$classDb->deleteRow(array(
					'idModule'  => 'accounting',
					'strTable'  => 'accountingAccess',
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
							'strColumn'     => 'idAccess',
							'flagCondition' => 'eq',
							'value'         => $value,
						),
					),
				));

			}
			$array = array('account','access');
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

		$classPluginAccountingInit->updateInitAccount();
		$classPluginAccountingInit->updateInitAccounts();
		$classPluginAccountingInit->updateInitAccountsEntity();
		$classPluginAccountingInit->updateInitPreference();
		$classPluginAccountingInit->updateInitAccess();

		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
		$this->_setSearch(array('flag' => 1));
	}

	/**
		(array(
			'arrIdLost' => array(),
		))
	 */
	protected function _setDeleteAccess($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsPluginAccountingAccess;
		global $varsPluginAccountingAccount;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];

		$array = $arr['arrIdLost'];

		$idAccess = 1;
		foreach ($array as $key => $value) {
			if (!$varsPluginAccountingAccess[$idEntity][$value]) {
				continue;
			}
			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingAccountEntity',
				'arrColumn' => array('idAccess'),
				'flagAnd'   => 1,
				'arrWhere'  => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idAccess',
						'flagCondition' => 'eq',
						'value'         => $value,
					),
				),
				'arrValue'  => array($idAccess),
			));
		}
	}

	/**
	 *
	 */
	protected function _iniDetailDelete()
	{
		global $varsPluginAccountingAccess;
		global $varsPluginAccountingAccount;
		global $varsRequest;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];

		if (!$varsPluginAccountingAccess[$idEntity][$varsRequest['query']['jsonValue']['idTarget']]
			|| !$varsPluginAccountingAccount['idEntityCurrent']
		) {
			$varsRequest['query']['jsonSearch']['flagReload'] = 0;
			$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
			$this->_setSearch(array('flag' => 1));
		}

		$this->_setDelete(array(
			'arrId' => array($varsRequest['query']['jsonValue']['idTarget']),
		));
	}
}
