<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_Log extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference'   => 'logWindow',
		'idLedger'       => 'ledgerWindow',
		'idFile'         => 'fileWindow',
		'idCash'         => 'cashWindow',
		'idBanks'        => 'banksWindow',
		'idFixedAssets'  => 'fixedAssetsWindow',
		'pathTplJs'      => 'else/plugin/accounting/js/jpn/log.js',
		'pathVarsJs'     => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/log.php',
		'pathVarsJournal'=> 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/dictionary.php',
	);

	/**
	 *
	 */
	public function run()
	{
		$this->_checkCorporationClass(array('flagChild' => 0));

		global $varsRequest;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 0,
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
				'idModule'  => 'accounting',
				'numLotNow' => 0,
				'strTable'  => 'accountingLog',
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
	 */
	public function getDBAuthority($arr)
	{
		global $classCheck;
		global $varsAccount;
		global $varsRequest;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingAccountsEntity;
		global $varsPluginAccountingAuthority;

		$idAccount = $varsAccount['id'];
		$idAccountApply = $varsAccount['id'];
		$idAuthority = $varsPluginAccountingAccountsEntity[$varsAccount['id']][$varsPluginAccountingAccount['idEntityCurrent']]['idAuthority'];
		$varsAuthority = $varsPluginAccountingAuthority[$idAuthority];
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];

		$strStatus = 'Select';
		if ($varsRequest['query']['func'] == 'ListOutput'
			|| $varsRequest['query']['func'] == 'ListPrint'
		) {
			$strStatus = 'Output';
		}

		$varsAuthority = $this->_getVarsAuthority(array());
		if (is_null($varsAuthority)) {
			return 0;
		}

		$strSql = 'idEntity = ? && numFiscalPeriod = ? ';
		$arrValue = array($idEntity, $numFiscalPeriod);

		$flag = $varsRequest['query']['jsonSearch']['ph']['flagApply'];
		if (!$flag) {
			$flag = 'none';
		}

		if ($flag != 'none') {
			$flagRemove = ($flag == 'remove')? 1 : 0;
			$strSql .= '&& flagRemove = ?';
			$arrValue[] = $flagRemove;
		}

		$flagSql = 0;
		if ($varsAuthority == 'admin' || $varsAuthority['flagAll' . $strStatus]) {
			if ($flag == 'done') {
				$flagApply = 0;
				$strSql .= '&& flagApply = ?';
				$arrValue[] = $flagApply;

			} elseif ($flag == 'apply') {
				$flagApply = 1;
				$strSql .= '&& flagApply = ? ';
				$arrValue[] = $flagApply;

			} elseif ($flag == 'back') {
				$flagApply = 1;
				$flagApplyBack = 1;
				$strSql .= '&& flagApply = ? && flagApplyBack = ? ';
				$arrValue[] = $flagApply;
				$arrValue[] = $flagApplyBack;

			}
			$flagSql = 1;

		} elseif ($varsAuthority['flagMy' . $strStatus]) {
			if ($flag == 'done') {
				$flagApply = 0;
				$strSql .= '&& idAccount = ? && flagApply = ?';
				$arrValue[] = $idAccount;
				$arrValue[] = $idAccountApply;

			} elseif ($flag == 'apply') {
				$flagApply = 1;
				$strSql .= '&& idAccount = ? && flagApply = ?';
				$arrValue[] = $idAccount;
				$arrValue[] = $idAccountApply;

			} elseif ($flag == 'back') {
				$flagApply = 1;
				$flagApplyBack = 1;
				$strSql .= '&& idAccount = ? && flagApply = ? && flagApplyBack = ? ';
				$arrValue[] = $idAccount;
				$arrValue[] = $idAccountApply;
				$arrValue[] = $flagApplyBack;

			} else {
				$strSql .= '&& idAccount = ?';
				$arrValue[] = $idAccount;
			}

			$flagSql = 1;

		}

		if ($flagSql) {
			$arrSql = array(
				'strSql'   => $strSql,
				'arrValue' => $arrValue,
			);

			return $arrSql;
		}

		return 0;
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
		global $varsPluginAccountingAccount;
		global $classSmarty;

		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
		));

		$varsAuthority = $this->_getVarsAuthority(array());

		$vars = $this->_updateVars(array(
			'vars' => $vars,
		));

		$flagCurrent = $this->_checkCurrent();

		$vars['portal']['varsList']['varsBtn'] = $this->_updateVarsListBtn(array(
			'vars'        => $vars['portal']['varsList']['varsBtn'],
			'flagCurrent' => $flagCurrent,
		));

		if (!($varsAuthority == 'admin' || $varsAuthority['flagMyOutput'] || $varsAuthority['flagAllOutput'])) {
			$vars['portal']['varsList']['varsEdit']['flagOutputUse'] = 0;
			$vars['portal']['varsList']['varsStart']['varsEdit']['flagOutputUse'] = 0;
			$vars['portal']['varsList']['varsEdit']['flagPrintUse'] = 0;
			$vars['portal']['varsList']['varsStart']['varsEdit']['flagPrintUse'] = 0;
		}

		if (!($varsAuthority == 'admin' || $varsAuthority['flagMyInsert'] || $varsAuthority['flagAllInsert'])) {
			$vars['portal']['varsList']['varsEdit']['flagImportUse'] = 0;
			$vars['portal']['varsList']['varsStart']['varsEdit']['flagImportUse'] = 0;
		}

		$flagPreferenceUse = 0;
		if ($flagCurrent) {
			if ($varsAuthority == 'admin' || $varsAuthority['flagAllSelect']) {
				$flagPreferenceUse = 1;
			}
		}
		$vars['portal']['varsList']['varsEdit']['flagPreferenceUse'] = $flagPreferenceUse;
		$vars['portal']['varsList']['varsStart']['varsEdit']['flagPreferenceUse'] = $flagPreferenceUse;

		if (!$flagCurrent) {
			$vars['portal']['varsDetail']['varsBtn'] = array();
			$this->_updateVarsEditLock(array(
				'vars' => $vars['portal']['varsDetail']['view']['varsEdit'],
			));
			$vars['portal']['varsList']['varsEdit']['flagImportUse'] = 0;
			$vars['portal']['varsList']['varsStart']['varsEdit']['flagImportUse'] = 0;
		}

		$rows = $this->getSearch($arr['arrSearch']);

		$vars = $this->_updateSearch(array(
			'vars'        => $vars,
			'rows'        => $rows,
			'arrIdTarget' => ($arr['arrIdTarget'])? $arr['arrIdTarget'] : array(),
		));

		$vars['flagAuthorityLedger'] = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'select',
			'idTarget'      => $this->_extSelf['idLedger'],
		));
		$vars['flagAuthorityFixedAssets'] = $this->_checkAccess(array(
			'flagAllUse'    => 0,
			'flagAuthority' => 'select',
			'idTarget'      => $this->_extSelf['idFixedAssets'],
		));
		$vars['flagAuthorityCash'] = $this->_checkAccess(array(
			'flagAllUse'    => 0,
			'flagAuthority' => 'select',
			'idTarget'      => $this->_extSelf['idCash'],
		));
		$vars['flagAuthorityLogHouse'] = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'select',
			'idTarget'      => $this->_extSelf['idPreference'],
		));
		$vars['flagAuthorityBanks'] = $this->_checkAccess(array(
			'flagAllUse'    => 0,
			'flagAuthority' => 'select',
			'idTarget'      => $this->_extSelf['idBanks'],
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
	protected function _updateVarsEditLock($arr)
	{
		$array = &$arr['vars'];
		foreach ($array as $key => $value) {
			if ($key == 'flagReloadUse') {
				continue;
			}
			$array[$key] = 0;
		}
	}

	/**
	 *
	 */
	protected function _updateVarsListBtn($arr)
	{
		$array = $arr['vars'];
		$arrayNew = array();
		foreach ($array as $key => $value) {
			if ($arr['flagCurrent']) {
				$arrayNew[] = $value;
			}
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
			'strColumn'   => 'jsonLogNaviSearch',
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
		global $varsPluginAccountingPreference;

		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
		));

		$vars = $this->_updateVars(array(
			'vars' => $vars,
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
			'vars'    => array(
				'varsDetail' => $this->_getMemo(array(
					'strTable'    => $arr['strTable'],
					'strColumn'   => $arr['strColumn'],
					'flagEntity'  => $arr['flagEntity'],
					'flagAccount' => $arr['flagAccount'],
				)),
			),
		));
	}

	/**
	 *
	 */
	protected function _iniNaviSearchDelete()
	{
		$this->_setNaviSearchDelete(array(
			'strTable'  => 'accountingAccountMemo',
			'strColumn' => 'jsonLogNaviSearch',
			'flagEntity'  => 1,
			'flagAccount' => 1,
		));
	}

	/**
		$this->_setNaviSearchDelete(array(
			'strTable'  => '',
			'strColumn' => '',
			'flagEntity' => 0,
			'flagAccount' => 0,
		));
	 */
	protected function _setNaviSearchDelete($arr)
	{
		global $varsRequest;
		global $classDb;
		global $varsAccount;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingPreference;
		$dbh = $classDb->getHandle();

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
				'arrValue'  => array(null),
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
			'vars'    => array(
				'varsDetail' => array(),
			),
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
			'strColumn' => 'jsonLogNaviSearch',
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
		global $varsPluginAccountingPreference;
		global $varsRequest;

		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
		));
		$vars = $this->_updateVars(array(
			'vars' => $vars,
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
			'vars'    => array(
				'varsDetail' => $vars['portal']['varsNavi']['search']['varsDetail'],
			),
		));
	}

	/**
		$this->_setNaviFolderReload(array(
			'pathVars'  => '',
			'strColumn' => '',
			'strTable'  => '',
			'flagEntity' => 0,
			'flagAccount' => 0,
		));
	*/
	protected function _setNaviFolderReload($arr)
	{
		global $varsRequest;
		global $varsPluginAccountingPreference;

		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
		));

		$vars = $this->_updateVars(array(
			'vars' => $vars,
		));

		$vars['portal']['varsNavi']['templateFolder']['varsDetail']['varsDetail'] = $this->_getMemo(array(
			'strTable'    => $arr['strTable'],
			'strColumn'   => $arr['strColumn'],
			'flagEntity'  => $arr['flagEntity'],
			'flagAccount' => $arr['flagAccount'],
		));

		if (!$vars['portal']['varsNavi']['templateFolder']['varsDetail']['varsDetail']) {
			$varsDetail = $vars['portal']['varsNavi']['templateFolder']['varsDetail']['templateDetail']['dir'];
			$vars['portal']['varsNavi']['templateFolder']['varsDetail']['varsDetail'][] = $varsDetail;
		}

		$this->sendVars(array(
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'varsDetail' => $vars['portal']['varsNavi']['templateFolder']['varsDetail']['varsDetail'],
			),
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
		global $classCheck;
		global $classEscape;

		global $classHtml;
		global $varsRequest;
		global $varsPluginAccountingAccounts;
		global $varsPluginAccountingAccountsId;

		global $varsAccounts;
		global $varsAccount;

		global $varsPluginAccountingAccount;

		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$idEntity  = $varsPluginAccountingAccount['idEntityCurrent'];
		$strCheckStamp = 'accountingLog_' . $idEntity . '_' . $numFiscalPeriod;

		$flagFileAccess = $this->_checkAccess(array(
			'flagAllUse'    => 0,
			'flagAuthority' => 'select',
			'idTarget'      => $this->_extSelf['idFile'],
		));

		$vars = $arr['vars'];
		$rows = $arr['rows'];

		$flagCurrent = $this->_checkCurrent();
		$varsAuthority = $this->_getVarsAuthority(array());

		$idAccount = $varsAccount['id'];

		$varsEntityNation = $vars['varsRule']['varsEntityNation'];

		$numLine = 0;
		$array = $rows['arrRows'];
		$arrayNew = array();
		$arraySide = array('Debit', 'Credit');
		$flag = 0;
		foreach ($array as $key => $value) {
			$varsTmpl = $vars['portal']['varsList']['templateDetail'];
			$varsTmpl['id'] = $value['idLog'];
			$varsTmpl['vars']['idTarget'] = $value['idLog'];
			$varsTmpl['numSort'] = (int) $key;

			if ($varsAccount['jsonStampCheck'][$strCheckStamp] < $value['stampRegister']) {
				$flag = 1;

			} else {
				$varsTmpl['strClassLoad'] = '';
			}

			$varsTmpl['idAccount'] = $value['idAccount'];
			$varsTmpl['idAccountApply'] = ($value['idAccountApply'])? $value['idAccountApply'] : '';
			$varsTmpl['idAccountSelf'] = $idAccount;
			$varsTmpl['strTitle'] = ($value['strTitle'])? $value['strTitle'] : '';

			$varsTmpl['stampRegister'] = $value['stampRegister'];
			$varsTmpl['stampUpdate'] = $value['stampUpdate'];

			$varsTmpl['stampBook'] = $value['stampBook'];
			$varsTmpl['flagRemove'] = (int) $value['flagRemove'];
			$varsTmpl['stampRemove'] = $value['stampRemove'];

			$varsTmpl['jsonVersion'] = $this->_updateSearchJsonVersion(array(
				'vars'           => $vars,
				'value'          => $value['jsonVersion'],
				'varsAuthority'  => $varsAuthority,
				'idAccount'      => $value['idAccount'],
				'flagFileAccess' => $flagFileAccess,
			));

			$numVersionEnd = count($varsTmpl['jsonVersion']) - 1;

			$varsTmpl['jsonDetail'] = $varsTmpl['jsonVersion'][$numVersionEnd];
			$varsTmpl['numVersion'] = count($varsTmpl['jsonVersion']);

			$tempData = $this->_getJsonChargeHistoryVarsDetail(array(
				'vars'  => $vars['varsItem']['varsJsonChargeHistory'],
				'value' => $value['jsonChargeHistory'],
			));
			$temp = $classHtml->allot(array(
				'strClass'    => 'TableSimple',
				'flagStatus'  => 'Html',
				'varsDetail'  => $tempData['varsDetail'],
				'varsColumn'  => $vars['varsItem']['varsJsonChargeHistory']['varsColumn'],
				'varsStatus'  => $vars['varsItem']['varsJsonChargeHistory']['varsStatus'],
			));
			$varsTmpl['jsonChargeHistory'] = $temp['strHtml'];
			$varsTmpl['vars']['jsonChargeHistory'] = $tempData['varsData'];

			$varsTmpl['arrCommaIdLogFile'] = $this->_updateSearchArrCommaIdLogFile(array(
				'arrCommaIdLogFile' => $classEscape->splitCommaArrayData(array('data' => $value['arrCommaIdLogFile'])),
			));

			$varsTmpl['jsonFile'] = $this->_updateSearchJsonFile(array(
				'value'          => $value['arrCommaIdLogFile'],
				'varsAuthority'  => $varsAuthority,
				'idAccount'      => $value['idAccount'],
				'flagFileAccess' => $flagFileAccess,
			));

			$varsTmpl['jsonPermitHistory'] = $this->_updateSearchJsonPermitHistory(array(
				'vars'  => $vars,
				'value' => $value['jsonPermitHistory'],
			));

			$numPermitEnd = count($varsTmpl['jsonPermitHistory']) - 1;
			if (!$value['arrCommaIdAccountPermit']) {
				$varsTmpl['arrCommaIdAccountPermit'] = array();

			} else {
				$varsTmpl['arrCommaIdAccountPermit'] = $this->_updateSearchArrIdAccountPermit(array(
					'arrIdAccountPermit' => $value['jsonPermitHistory'][$numPermitEnd]['arrIdAccountPermit'],
				));
			}

			$varsTmpl['arrSpaceStrTag'] = preg_replace('/^ /', '', $value['arrSpaceStrTag']);

			$varsTmpl['flagApply'] = (int) $value['flagApply'];
			$varsTmpl['flagApplyBack'] = (int) $value['flagApplyBack'];
			$varsTmpl['flagRemove'] = (int) $value['flagRemove'];

			$varsTmpl['flagBtnDelete'] = 0;
			$varsTmpl['flagBtnPermit'] = 0;
			$varsTmpl['flagBtnBack'] = 0;
			$varsTmpl['flagBtnAdd'] = 0;
			$varsTmpl['flagBtnEdit'] = 0;
			$varsTmpl['flagCheckboxUse'] = 0;
			$varsTmpl['flagAdmin'] = ($varsAuthority == 'admin')? 1 : 0;
			$varsTmpl['flagCurrent'] = $flagCurrent;
			$varsTmpl['flagFiscalReport'] = ($value['flagFiscalReport'])? $value['flagFiscalReport'] : 'none';

			if ($flagCurrent) {
				if ($varsAuthority == 'admin') {
					if (!$varsTmpl['flagRemove']) {
						$varsTmpl['flagBtnDelete'] = 1;
						$varsTmpl['flagBtnEdit'] = 1;
					}
					$varsTmpl['flagBtnAdd'] = 1;

				} else {
					if (!$varsTmpl['flagRemove']) {
						if (($value['idAccount'] == $varsAccount['id'] && $varsAuthority['flagMyDelete'])
							|| $varsAuthority['flagAllDelete']
						) {
							$varsTmpl['flagBtnDelete'] = 1;
						}

						if (($value['idAccount'] == $varsAccount['id'] && $varsAuthority['flagMyUpdate'])
							|| $varsAuthority['flagAllUpdate']
						) {
							$varsTmpl['flagBtnEdit'] = 1;
						}
					}

					if ($varsAuthority['flagMyInsert'] || $varsAuthority['flagAllInsert']) {
						$varsTmpl['flagBtnAdd'] = 1;
					}

				}

				if (!$varsTmpl['flagRemove']) {
					if ($varsTmpl['flagApply'] && !$varsTmpl['flagApplyBack']) {
						if ($this->_checkIdAccountPermit(array('vars' => $value))) {
							$varsTmpl['flagBtnPermit'] = 1;
							$varsTmpl['flagBtnBack'] = 1;
						}
					}
				}

				if ($varsTmpl['flagBtnDelete'] || $varsTmpl['flagBtnPermit'] || $varsTmpl['flagBtnBack']) {
					$varsTmpl['flagCheckboxUse'] = 1;

				}
			}

			$varsTmpl['flagBtnOutput'] = 0;
			if ($varsAuthority == 'admin' || $varsAuthority['flagAllOutput']) {
				$varsTmpl['flagBtnOutput'] = 1;
				$varsTmpl['flagBtnPrint'] = 1;

			} elseif ($varsAuthority['flagMyOutput'] && $varsTmpl['idAccount'] == $varsTmpl['idAccountSelf']) {
				$varsTmpl['flagBtnOutput'] = 1;
				$varsTmpl['flagBtnPrint'] = 1;
			}

			$varsTmpl['varsColumnDetail']['id'] = $varsTmpl['id'];
			$varsTmpl['varsColumnDetail']['strVersion'] = 'Ver.' . count($varsTmpl['jsonVersion']);

			$numFile = count($varsTmpl['arrCommaIdLogFile']);
			if (!$numFile) {
				$numFile = '';
			}
			$varsTmpl['varsColumnDetail']['numFile'] = $numFile;
			$varsTmpl['varsColumnDetail']['stampBook'] = $value['stampBook'];

			$varsTmpl['varsColumnDetail']['stampRegister'] = $value['stampRegister'];
			$varsTmpl['varsColumnDetail']['stampUpdate'] = $value['stampUpdate'];


			if ($vars['varsRule']['arrFlagReport']['arrStrTitle'][$value['flagFiscalReport']]['strTitle']) {
				$varsTmpl['varsColumnDetail']['flagFiscalReport'] = $vars['varsRule']['arrFlagReport']['arrStrTitle'][$value['flagFiscalReport']]['strTitle'];

			} else {
				$varsTmpl['varsColumnDetail']['flagFiscalReport'] = '';
			}

			if ($value['flagRemove']) {
				$varsTmpl['strClassFont'] = $vars['varsItem']['strClassNone'];
				$varsTmpl['varsColumnDetail']['flagApply'] = $vars['varsItem']['strRemoveFake'];

			} else {
				if ($varsTmpl['jsonPermitHistory']) {
					$numEnd = count($varsTmpl['jsonPermitHistory']) - 1;
					$varsTmpl['varsColumnDetail']['flagApply'] = $varsTmpl['jsonPermitHistory'][$numEnd]['strStatus'];
				}
				if ($varsTmpl['flagApply']) {
					$varsTmpl['strClassFont'] = $vars['varsItem']['strClassApply'];
					if ($varsTmpl['flagApplyBack']) {
						$varsTmpl['strClassFont'] = $vars['varsItem']['strClassBack'];
					}
				} else {
					$varsTmpl['varsColumnDetail']['flagApply'] = $vars['varsItem']['strDone'];
				}
			}

			$varsTmpl['varsColumnDetail']['strTitle'] = ($varsTmpl['strTitle'])? $varsTmpl['strTitle'] : '';

			$strCodeName = $varsAccounts[$value['idAccount']]['strCodeName'];
			if (!$strCodeName) {
				$strCodeName = $varsPluginAccountingAccountsId[$value['idAccount']]['strCodeName'];
			}
			$varsTmpl['varsColumnDetail']['idAccount'] = $strCodeName;

			$varsTmpl['vars']['id'] = $varsTmpl['id'];
			$varsTmpl['vars']['strTitle'] = $value['strTitle'];
			$varsTmpl['vars']['stampBook'] = $value['stampBook'];
			$varsTmpl['vars']['flagFiscalReport'] = $value['flagFiscalReport'];
			$varsTmpl['vars']['idAccount'] = $value['idAccount'];
			$varsTmpl['vars']['flagApply'] = (int) $value['flagApply'];
			$varsTmpl['vars']['flagApplyBack'] = (int) $value['flagApplyBack'];
			$varsTmpl['vars']['flagRemove'] = (int) $value['flagRemove'];
			$varsTmpl['vars']['arrSpaceStrTag'] = $classEscape->splitSpaceArrayData(array('data' => $varsTmpl['arrSpaceStrTag']));

			$varsTmpl['varsScheduleDetail']['stamp'] = $value['stampBook'];
			$varsTmpl['varsScheduleDetail']['strTitle'] = ($varsTmpl['strTitle'])? $varsTmpl['strTitle'] : '-';

			if ($numLine % 2 == 0) {
				$varsTmpl['strClassBg'] = $vars['varsItem']['strClassBg'];
			}

			$numLine++;
			$numLoop = 0;
			$arrayDetail = $varsTmpl['jsonVersion'][$numVersionEnd]['jsonDetail']['varsDetail'];
			foreach ($arrayDetail as $keyDetail => $valueDetail) {
				foreach ($arraySide as $keySide => $valueSide) {

					$varsTmpl['vars']['idAccountTitle' . $valueSide] = '';
					$varsTmpl['vars']['idSubAccountTitle' . $valueSide] = '';
					$varsTmpl['vars']['idDepartment' . $valueSide] = '';

					$varsTmpl['varsColumnDetail']['idAccountTitle' . $valueSide] = '';
					$varsTmpl['varsColumnDetail']['idSubAccountTitle' . $valueSide] = '';
					$varsTmpl['varsColumnDetail']['idDepartment' . $valueSide] = '';
					$varsTmpl['varsColumnDetail']['numValue' . $valueSide] = '';
					$varsTmpl['varsColumnDetail']['numRateConsumptionTax' . $valueSide] = '';
					$varsTmpl['varsColumnDetail']['numValueConsumptionTax' . $valueSide] = '';
					$varsTmpl['varsColumnDetail']['flagConsumptionTax' . $valueSide] = '';

					$idAccountTitle = $valueDetail['arr' . $valueSide]['idAccountTitle'];
					$numValue = $valueDetail['arr' . $valueSide]['numValue'];
					$numValueConsumptionTax = $valueDetail['arr' . $valueSide]['numValueConsumptionTax'];
					$numRateConsumptionTax = $valueDetail['arr' . $valueSide]['numRateConsumptionTax'];
					$idDepartment = $valueDetail['arr' . $valueSide]['idDepartment'];
					$idSubAccountTitle = $valueDetail['arr' . $valueSide]['idSubAccountTitle'];
					$flagConsumptionTaxGeneralRuleEach = $valueDetail['arr' . $valueSide]['flagConsumptionTaxGeneralRuleEach'];
					$flagConsumptionTaxGeneralRuleProration = $valueDetail['arr' . $valueSide]['flagConsumptionTaxGeneralRuleProration'];
					$flagConsumptionTaxSimpleRule = $valueDetail['arr' . $valueSide]['flagConsumptionTaxSimpleRule'];
					$flagConsumptionTaxWithoutCalc = $valueDetail['arr' . $valueSide]['flagConsumptionTaxWithoutCalc'];
					$flagConsumptionTaxCalc = $valueDetail['arr' . $valueSide]['flagConsumptionTaxCalc'];
					$flagConsumptionTaxIncluding = $valueDetail['arr' . $valueSide]['flagConsumptionTaxIncluding'];
					$flagConsumptionTaxFree = $valueDetail['arr' . $valueSide]['flagConsumptionTaxFree'];

					if ($idAccountTitle) {

						//strAccountTitle
						$strAccountTitle = $vars['varsRule']['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['strTitleFS'];
						$varsTmpl['varsColumnDetail']['idAccountTitle' . $valueSide] = $strAccountTitle;
						$varsTmpl['vars']['idAccountTitle' . $valueSide] = $idAccountTitle;

						//strValue
						$varsTmpl['varsColumnDetail']['numValue' . $valueSide] = number_format($numValue);

						//strSubAccountTitle
						$idSubAccountTitle = $valueDetail['arr' . $valueSide]['idSubAccountTitle'];
						$strSubAccountTitle = $vars['varsRule']['arrSubAccountTitle']['arrStrTitle'][$idAccountTitle][$idSubAccountTitle]['strTitle'];
						if ($strSubAccountTitle) {
							$varsTmpl['varsColumnDetail']['idSubAccountTitle' . $valueSide] = $strSubAccountTitle;
							$varsTmpl['vars']['idSubAccountTitle' . $valueSide] = $idSubAccountTitle;
						}

						if (!$flagConsumptionTaxFree) {
							$flagTax = 0;
							$flagRate = 0;

							//strNumValueConsumptionTax
							if ((int) $varsEntityNation['flagConsumptionTaxGeneralRule']) {
								if ((int) $varsEntityNation['flagConsumptionTaxDeducted']) {
									if (preg_match("/^tax/", $flagConsumptionTaxGeneralRuleEach)) {
										$flagTax = 1;
									}
									if (preg_match("/^tax/", $flagConsumptionTaxGeneralRuleEach)
										|| preg_match("/^else/", $flagConsumptionTaxGeneralRuleEach)
									) {
										$flagRate = 1;
									}

								} else {
									if (preg_match("/^tax/", $flagConsumptionTaxGeneralRuleProration)) {
										$flagTax = 1;
									}
									if (preg_match("/^tax/", $flagConsumptionTaxGeneralRuleProration)
										|| preg_match("/^else/", $flagConsumptionTaxGeneralRuleProration)
									) {
										$flagRate = 1;
									}
								}

							} else {
								if (preg_match("/^tax/", $flagConsumptionTaxSimpleRule)) {
									$flagTax = 1;
								}
								if (preg_match("/^tax/", $flagConsumptionTaxSimpleRule)
									|| preg_match("/^else/", $flagConsumptionTaxSimpleRule)
								) {
									$flagRate = 1;
								}
							}

							if ($numValue
								&& $flagTax
								&& $idAccountTitle != 'suspenseReceiptOfConsumptionTaxes'
								&& $idAccountTitle != 'suspensePaymentConsumptionTaxes'
								&& $flagConsumptionTaxWithoutCalc != 3
								&& !$flagConsumptionTaxIncluding
								&& $numValueConsumptionTax != ''
							) {
								if ($flagConsumptionTaxWithoutCalc == 1) {
									$varsTmpl['varsColumnDetail']['numValueConsumptionTax' . $valueSide] = '( ' . number_format($numValueConsumptionTax);

								} elseif ($flagConsumptionTaxWithoutCalc == 2) {
									$varsTmpl['varsColumnDetail']['numValueConsumptionTax' . $valueSide] = number_format($numValueConsumptionTax);
								}
							}

							//strConsumptionTax
							if ((int) $varsEntityNation['flagConsumptionTaxGeneralRule']) {
								if ((int) $varsEntityNation['flagConsumptionTaxDeducted']) {
									if ($flagConsumptionTaxGeneralRuleEach
										&& $flagConsumptionTaxGeneralRuleEach != 'none'
									) {
										$strConsumptionTax = $vars['varsRule']['varsConsumptionTax']['arrStrGeneralEach'][$flagConsumptionTaxGeneralRuleEach];
										$varsTmpl['varsColumnDetail']['flagConsumptionTax' . $valueSide] = $strConsumptionTax;
									}

								} else {
									if ($flagConsumptionTaxGeneralRuleProration
										&& $flagConsumptionTaxGeneralRuleProration != 'none'
									) {
										$strConsumptionTax = $vars['varsRule']['varsConsumptionTax']['arrStrGeneralProration'][$flagConsumptionTaxGeneralRuleProration];
										$varsTmpl['varsColumnDetail']['flagConsumptionTax' . $valueSide] = $strConsumptionTax;
									}
								}

							} else {
								if ($flagConsumptionTaxSimpleRule
									&& $flagConsumptionTaxSimpleRule != 'none'
								) {
									$strConsumptionTax = $vars['varsRule']['varsConsumptionTax']['arrStrSimple'][$flagConsumptionTaxSimpleRule];
									$varsTmpl['varsColumnDetail']['flagConsumptionTax' . $valueSide] = $strConsumptionTax;
								}
							}

							//strConsumptionTaxCalc
							if ($flagTax
								&& $idAccountTitle != 'suspenseReceiptOfConsumptionTaxes'
								&& $idAccountTitle != 'suspensePaymentConsumptionTaxes'
								&& !$flagConsumptionTaxIncluding
							) {
								if (!$flagConsumptionTaxIncluding) {
									if (!$flagConsumptionTaxWithoutCalc) {
										$flagConsumptionTaxWithoutCalc = (int) $varsEntityNation['flagConsumptionTaxWithoutCalc'];
									}
									//$rowData['strConsumptionTaxCalc' . $valueSide] = $vars['varsRule']['varsConsumptionTax']['arrStrWithoutCalc'][$flagConsumptionTaxWithoutCalc];
								}
							}
							if ($flagRate) {
								$varsTmpl['varsColumnDetail']['numRateConsumptionTax' . $valueSide] = $numRateConsumptionTax . '%';
							}
						}

						//strDepartment
						$strDepartment = $vars['varsRule']['arrDepartment']['arrStrTitle'][$idDepartment]['strTitle'];
						if ($strDepartment) {
							$varsTmpl['varsColumnDetail']['idDepartment' . $valueSide] = $strDepartment;
							$varsTmpl['vars']['idDepartment' . $valueSide] = $idDepartment;
						}
					}
				}
				$varsTmpl['id'] .= '_' . $numLoop;
				if ($numLoop) {
					$varsTmpl['flagCheckboxUse'] = 0;
					$varsTmpl['strClassLoad'] = '';
					$varsTmpl['strClass'] = $vars['varsItem']['strClassBlank'];
					$varsTmpl['flagBtnUse'] = 0;
					$varsTmpl['flagMoveUse'] = 0;
					$varsTmpl['varsColumnDetail']['flagFiscalReport'] = '';
					$varsTmpl['varsColumnDetail']['stampBook'] = '';
					$varsTmpl['varsColumnDetail']['strTitle'] = '';
					$varsTmpl['varsColumnDetail']['id'] = '';
					$varsTmpl['varsScheduleDetail']['flagType'] = '';
				}
				$numLoop++;

				$arrayNew[] = $varsTmpl;
			}
		}
		$vars['portal']['varsList']['varsPage']['varsStatus']['numRows'] = $rows['numRows'];
		$vars['portal']['varsList']['varsDetail'] = $arrayNew;

		$flagAddUse = 0;
		if ($flagCurrent) {
			if ($varsAuthority == 'admin' || $varsAuthority['flagAllInsert'] || $varsAuthority['flagMyInsert']) {
				$flagAddUse = 1;
			}
		}
		$vars['portal']['varsDetail']['varsStart']['varsEdit']['flagAddUse'] = $flagAddUse;


		if (!$arr['flagVars']) {
			$varsTemp = $classHtml->allot(array(
				'strClass'    => 'Table',
				'flagStatus'  => 'Html',
				'flagBgNone'  => 1,
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
	 	$this->_checkIdAccountPermit($arr)
	 */
	protected function _checkIdAccountPermit($arr)
	{
		global $varsAccount;

		$vars = $arr['vars'];
		$numEnd = count($vars['jsonPermitHistory']) - 1;
		$array = $vars['jsonPermitHistory'][$numEnd]['arrIdAccountPermit'];
		$varsTarget = array();
		foreach ($array as $key => $value) {
			if ($varsAccount['id'] == $value['idAccount']) {
				$varsTarget = $value;
				break;
			}
		}
		if ($varsTarget) {
			if ($varsTarget['flagPermit'] == 'none') {
				return 1;
			}
		}

	}

	/**
	 	'arrIdAccountPermit' => $value['jsonPermitHistory'][$numPermitEnd]['arrIdAccountPermit'],
	 */
	protected function _updateSearchArrIdAccountPermit($arr)
	{
		global $varsAccounts;
		global $classEscape;
		global $varsPluginAccountingAccountsId;

		$arrayNew = array();

		if (!$arr['arrIdAccountPermit']) {
			return $arrayNew;
		}

		$array = $arr['arrIdAccountPermit'];
		$num = 0;
		foreach ($array as $key => $value) {
			$data = array();
			$strCodeName = $varsAccounts[$value['idAccount']]['strCodeName'];
			if (!$strCodeName) {
				$strCodeName = $varsPluginAccountingAccountsId[$value['idAccount']]['strCodeName'];
			}
			$data['strTitle'] = $strCodeName;
			$data['id'] = $value['idAccount'];
			$arrayNew[$num] = $data;
			$num++;
		}

		return $arrayNew;
	}

	/**
	 	$this->_updateSearchArrCommaIdLogFile($arr)
	 */
	protected function _updateSearchArrCommaIdLogFile($arr)
	{
		global $varsAccounts;
		global $classEscape;
		global $classDb;
		$dbh = $classDb->getHandle();

		$array = $arr['arrCommaIdLogFile'];
		$num = 0;
		$arrayNew = array();
		foreach ($array as $key => $value) {
			$vars = $this->_getFileLog(array(
				'value' => $value,
			));
			$data = array();
			$data['strTitle'] = $vars['strTitle'];
			$data['id'] = $value;
			$arrayNew[$num] = $data;
			$num++;
		}

		return $arrayNew;
	}

	/**
		(array(
				'vars'          => $vars,
				'value'         => $value['jsonVersion'],
				'varsAuthority' => $varsAuthority,
				'idAccount'     => $value['idAccount'],
		))
	 */
	protected function _updateSearchJsonVersion($arr)
	{
		global $classCheck;
		global $classEscape;

		/*
		 * 20191001 start
		 */
		$classCalcConsumptionTax = $this->_getClassCalc(array('flagType' => 'ConsumptionTax'));
		/*
		 * 20191001 end
		 */

		$array = $arr['value'];
		$arrayNew = array();
		$num = 1;
		foreach ($array as $key => $value) {
			$data = array();
			$data['stampRegister'] = $value['stampRegister'];
			$data['stampUpdate'] = $value['stampUpdate'];
			$data['stampBook'] = $value['stampBook'];
			$data['flagFiscalReport'] = ($value['flagFiscalReport'] == '0')? 'none' : $value['flagFiscalReport'];
			$data['strTitle'] = $value['strTitle'];
			$data['arrSpaceStrTag'] = preg_replace('/^ /', '', $value['arrSpaceStrTag']);
			$data['vars']['arrSpaceStrTag'] = $classEscape->splitSpaceArrayData(array('data' => $value['arrSpaceStrTag']));

			$data['jsonFile'] = $this->_updateSearchJsonFile(array(
				'value'          => $value['arrCommaIdLogFile'],
				'idAccount'      => $arr['idAccount'],
				'varsAuthority'  => $arr['varsAuthority'],
				'flagFileAccess' => $arr['flagFileAccess'],
			));
			/*
			 * 20191001 start
			 */
			$value['jsonDetail'] = $classCalcConsumptionTax->allot(array(
			    'flagStatus' => 'sendValueConsumptionTaxReduced',
			    'jsonDetail'   => $value['jsonDetail'],
			));
			/*
			 * 20191001 end
			 */
			$data['jsonDetail'] = $value['jsonDetail'];
			$data['strVersion'] = 'Ver.' . $num;
			$data['numVersion'] = $num;
			$arrayNew[] = $data;
			$num++;

		}

		return $arrayNew;
	}


	/**
		(array(
				'value'          => $value['arrCommaIdLogFile'],
				'idAccount'      => $arr['idAccount'],
				'varsAuthority'  => $arr['varsAuthority'],
				'flagFileAccess' => $arr['flagFileAccess'],
		))
	 */
	protected function _updateSearchJsonFile($arr)
	{
		global $classEscape;
		global $varsAccount;

		$varsAuthority = $arr['varsAuthority'];
		$array = $classEscape->splitCommaArrayData(array('data' => $arr['value']));

		$arrayNew = array();
		foreach ($array as $key => $value) {
			if (!(int) $value) {
				continue;
			}
			$vars = $this->_getFileLog(array(
				'value' => $value,
			));
			$data = array();
			$data['strTitle'] = $vars['strTitle'] . '.' . $vars['strFileType'];
			$data['numByte'] =  $vars['numByte'];
			$data['id'] = $vars['idLogFile'];
			$data['strFileType'] = $vars['strFileType'];
			$data['flagRemove'] = (int) $vars['flagRemove'];
			$data['flagFileAccess'] = $arr['flagFileAccess'];
			$data['flagAuthority'] = 0;
			if ($varsAuthority == 'admin' || $varsAuthority['flagAllOutput']) {
				$data['flagAuthority'] = 1;

			} elseif ($varsAuthority['flagMyOutput']) {
				if ($arr['idAccount'] == $varsAccount['id']) {
					$data['flagAuthority'] = 1;
				}
			}
			$data['numVersion'] = count($vars['jsonVersion']);
			$arrayNew[] = $data;

		}

		return $arrayNew;
	}

	/**
		(array(
			'value' => 0,
		))
	 */
	protected function _getFileLog($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogFile',
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
					'strColumn'     => 'idLogFile',
					'flagCondition' => 'eq',
					'value'         => $arr['value'],
				),
			),
		));

		return $rows['arrRows'][0];
	}

	/**
		(array(

				'vars'  => $vars['varsItem']['varsJsonChargeHistory'],
				'value' => $value['jsonChargeHistory'],

		))
	 */
	protected function _getJsonChargeHistoryVarsDetail($arr)
	{
		$classTime = new Code_Else_Lib_Time();

		global $varsAccount;
		global $varsAccounts;
		global $varsPluginAccountingAccountsId;

		$array = $arr['value'];
		if (!$arr['value']) {
			$array = array();
		}
		$varsDetail = array();
		$varsData = array();
		$num = 1;
		foreach ($array as $key => $value) {
			$tmplDetail = $arr['vars']['tmplDetail'];
			$tmplDetail['id'] = $num;

			$strNo = $num;
			$tmplData = $arr['vars']['tmplData'];
			$tmplData['value'] = $strNo;
			$tmplDetail['varsDetail']['id'] = $tmplData;

			$classTime->setTimeZone(array('data' => $varsAccount['numTimeZone']));
			$strTime = $classTime->getDisplay(array(
				'stamp'    => $value['stampRegister'],
				'flagType' => 1,
			));
			$tmplData = $arr['vars']['tmplData'];
			$tmplData['value'] = $strTime;
			$tmplDetail['varsDetail']['stampRegister'] = $tmplData;


			$strCodeName = $varsAccounts[$value['idAccount']]['strCodeName'];
			if (!$varsAccounts[$value['idAccount']]['strCodeName']) {
				$strCodeName = $varsPluginAccountingAccountsId[$value['idAccount']]['strCodeName'];
			}
			$tmplData = $arr['vars']['tmplData'];
			$tmplData['value'] = $strCodeName;
			$tmplDetail['varsDetail']['idAccount'] = $tmplData;

			$varsDetail[] = $tmplDetail;

			$tempVars = array();
			$tempVars['idAccount'] = $value['idAccount'];
			$tempVars['strCodeName'] = $strCodeName;
			$varsData[] = $tempVars;

			$num++;
		}

		$data = array(
			'varsDetail' => $varsDetail,
			'varsData' => $varsData,
		);

		return $data;
	}

	/**
		(array(
			'vars' => array,
			'value' => array,
		))
	 */
	protected function _updateSearchJsonPermitHistory($arr)
	{
		global $varsAccounts;
		global $varsAccount;
		global $varsPluginAccountingAccountsId;

		$array = $arr['value'];
		$arrayNew = array();
		foreach ($array as $key => $value) {
			$data = array();
			$data['stampRegister'] = $value['stampRegister'];
			$strCodeName = $varsAccounts[$value['idAccountApply']]['strCodeName'];
			if (!$strCodeName) {
				$strCodeName = $varsPluginAccountingAccountsId[$value['idAccountApply']]['strCodeName'];
			}
			$data['strCodeName'] = $strCodeName;
			$data['flagInvalid'] = (int) $value['flagInvalid'];
			$data['numSumMax'] = (int) $value['numSumMax'];

			$arrayNewPermit = array();
			$numPermit = 0;
			$numPermitBack = 0;
			$stampPermit = 0;
			$arrayPermit = $value['arrIdAccountPermit'];
			$strStatus = '';
			foreach ($arrayPermit as $keyPermit => $valuePermit) {
				if ($valuePermit['flagPermit'] == 'done') {
					$numPermit++;
					if ($stampPermit < $valuePermit['stampRegister']) {
						$stampPermit = $valuePermit['stampRegister'];
					}

				} elseif ($valuePermit['flagPermit'] == 'back') {
					$numPermitBack++;
				}

				$dataPermit = $valuePermit;
				$strCodeName = $varsAccounts[$dataPermit['idAccount']]['strCodeName'];
				if (!$varsAccounts[$dataPermit['idAccount']]['strCodeName']) {
					$strCodeName = $varsPluginAccountingAccountsId[$dataPermit['idAccount']]['strCodeName'];
				}
				$dataPermit['strCodeName'] = $strCodeName;
				if ($varsAccount['id'] == $dataPermit['idAccount']) {
					if ($valuePermit['flagPermit'] == 'done') {
						$strStatus = $arr['vars']['varsItem']['strPermitDone'];

					} elseif ($valuePermit['flagPermit'] == 'back') {
						$strStatus = $arr['vars']['varsItem']['strPermitDone'];

					} elseif ($valuePermit['flagPermit'] == 'none') {
						$strStatus = $arr['vars']['varsItem']['strPermitNeed'];
					}
				}
				$arrayNewPermit[] = $dataPermit;
			}
			$data['stampPermit'] = $stampPermit;
			$data['numSumPermit'] = $numPermit;
			$data['numSumBack'] = $numPermitBack;

			$numSumMax = count($arrayPermit) - $data['numSumBack'];
			if ($data['flagInvalid']) {
				$data['stampPermit'] = 0;
				if ($numSumMax < $data['numSumMax']) {
					$data['strStatus'] = $arr['vars']['varsItem']['strApplyBack'];
				} else {
					$data['strStatus'] = $arr['vars']['varsItem']['strRevocation'];
				}


			} else {
				if ($data['numSumPermit'] >= $data['numSumMax']) {
					$data['strStatus'] = $arr['vars']['varsItem']['strDone'];

				} elseif ($numSumMax < $data['numSumMax']) {
					$data['stampPermit'] = 0;
					$data['strStatus'] = $arr['vars']['varsItem']['strApplyBack'];

				} else {
					$data['stampPermit'] = 0;
					if ($strStatus) {
						$data['strStatus'] = $strStatus;

					} else {
						$data['strStatus'] = $arr['vars']['varsItem']['strApply'];
					}
				}

			}

			$data['arrIdAccountPermit'] = $arrayNewPermit;

			$arrayNew[] = $data;
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

		$vars = $this->_updateVars(array(
			'vars' => $vars,
		));

		$this->checkSearch(array(
			'arrOrder' => $vars['portal']['varsNavi']['search']['varsDetail']['varsSearchSort'],
			'arrWhere' => $vars['portal']['varsNavi']['search']['varsDetail']['varsSearchItem'],
		));

		$this->_checkSearchDetail(array('vars' => &$vars));

		if (!$varsRequest['query']['jsonSearch']['ph']['arrOrder']) {
			$varsRequest['query']['jsonSearch']['ph']['arrOrder'] = array(
				'strColumn' => 'id',
				'flagDesc'  => 1,
			);
		}

		$rows = $this->getSearch(array(
			'idModule'   => 'accounting',
			'numLotNow'  => $varsRequest['query']['jsonSearch']['numLotNow'],
			'strTable'   => 'accountingLog',
			'arrJoin'    => array(),
			'arrOrder'   => $varsRequest['query']['jsonSearch']['ph']['arrOrder'],
			'insCurrent' => $this,
			'arrWhere'   => $varsRequest['query']['jsonSearch']['ph']['arrWhere'],
			'flagAnd'    => ($varsRequest['query']['jsonSearch']['ph']['flagAnd'])? 1 : 0,
		));

		$vars = $this->_updateSearch(array(
			'vars'        => $vars,
			'rows'        => $rows,
			'arrIdTarget' => ($arr['arrIdTarget'])? $arr['arrIdTarget'] : array(),
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
	protected function _checkSearchDetail($arr)
	{
		global $varsRequest;
		global $classCheck;


		$array = &$varsRequest['query']['jsonSearch']['ph']['arrWhere'];
		foreach ($array as $key => $value) {
			if (preg_match("/^arrCommaTax/", $value['strColumn'])) {
				preg_match("/^(.*?)_(.*?)_(.*?)$/", $value['value'], $arrMatch);
				list($dummy, $idDepartment, $flagConsumptionTax, $numRateConsumptionTax) = $arrMatch;
				if ($idDepartment) {
					$flag = $classCheck->checkValueWord(array(
						'flagType' => 'num',
						'value'    => $idDepartment,
					));
					if ($flag) {
						if (FLAG_TEST) {
							var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
						}
						exit;
					}
				}

				$varsTax = array();
				//strConsumptionTax
				if ((int) $arr['vars']['varsRule']['varsEntityNation']['flagConsumptionTaxGeneralRule']) {
					if ((int) $arr['vars']['varsRule']['varsEntityNation']['flagConsumptionTaxDeducted']) {
						$varsTax = $arr['vars']['varsRule']['varsConsumptionTax']['arrStrGeneralEach'];

					} else {
						$varsTax = $arr['vars']['varsRule']['varsConsumptionTax']['arrStrGeneralProration'];
					}

				} else {
					$varsTax = $arr['vars']['varsRule']['varsConsumptionTax']['arrStrSimple'];
				}

				if (!$varsTax[$flagConsumptionTax]) {
					if (FLAG_TEST) {
						var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
					}
					exit;
				}

				if ($numRateConsumptionTax) {
					/*
					 * 20191001 start
					 */
				    if (!preg_match("/^(5|8|8_reduced|10)$/", $numRateConsumptionTax)) {
					//if (!preg_match("/^(5|8)$/", $numRateConsumptionTax)) {
					/*
					 * 20191001 end
					*/
						if (FLAG_TEST) {
							var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
						}
						exit;
					}
				}
			}
		}
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
				'stampTarget' => $varsPluginAccountingPreference['jsonStampUpdate']['log'],
				'flagSearch'  => 1,
			));
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$vars = $this->_updateVars(array(
			'vars' => $vars,
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
			'idModule'   => 'accounting',
			'numLotNow'  => $varsRequest['query']['jsonSearch']['numLotNow'],
			'strTable'   => 'accountingLog',
			'arrJoin'    => array(),
			'arrOrder'   => $varsRequest['query']['jsonSearch']['ph']['arrOrder'],
			'insCurrent' => $this,
			'arrWhere'   => $varsRequest['query']['jsonSearch']['ph']['arrWhere'],
		));

		$vars = $this->_updateSearch(array(
			'vars'        => $vars,
			'rows'        => $rows,
			'arrIdTarget' => ($arr['arrIdTarget'])? $arr['arrIdTarget'] : array(),
		));

		$varsTarget = $vars;

		$rowsTarget = $this->getSearch(array(
			'idModule'  => 'accounting',
			'numLotNow' => 0,
			'strTable'  => 'accountingLog',
			'arrJoin'   => array(),
			'arrOrder'  => array(),
			'arrWhere'  => array(array(
				'flagType'      => 'num',
				'strColumn'     => 'idLog',
				'flagCondition' => 'eq',
				'value'         => $varsRequest['query']['jsonValue']['idTarget'],
			)),
			'insCurrent'  => $this,
		));

		if (!$rowsTarget['numRows']) {
			$this->_sendOldError();
		}

		$varsTarget = $this->_updateSearch(array(
			'vars'        => $varsTarget,
			'rows'        => $rowsTarget,
			'arrIdTarget' => array(),
			'flagVars'    => 1,
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
	protected function _iniVarsRule()
	{
		global $varsRequest;
		global $varsPluginAccountingPreference;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$vars = $this->_updateVars(array(
			'vars' => $vars,
		));

		$this->sendVars(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'varsRule' => $vars['varsRule'],
			),
		));
	}


	/**
		(array(
			'vars' => $vars,
		))
	 */
	protected function _updateVars($arr)
	{
		global $varsPluginAccountingPreference;
		global $varsPluginAccountingAccount;
		global $varsAccount;

		$vars = $arr['vars'];
		$varsSearch = &$vars['portal']['varsNavi']['search']['varsDetail']['varsSearchItem']['templateDetail'];

		$arrSubAccountTitle = $this->_getVarsSubAccountTitle(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrAccountTitle = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'numFiscalPeriod'    => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsSearch['restOption']['commaFs'] = $arrAccountTitle['arrSelectTag'];

		$arrDepartment = $this->_getVarsDepartment(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		if (!$arrDepartment['arrSelectTag']) {
			$arrayNew = array();
			$array = $varsSearch['firstOption'];
			foreach ($array as $key => $value) {
				if (preg_match("/^commaDepartment/", $value['value'])) {
					continue;
				}
				$arrayNew[] = $value;
			}
			$varsSearch['firstOption'] = $arrayNew;

		} else {
			$varsSearch['restOption']['commaDepartment'] = $arrDepartment['arrSelectTag'];
		}

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsConsumptionTax = $this->_getVarsConsumptionTax(array());

		if (!$varsEntityNation['flagConsumptionTaxFree']) {
			$arrayOption = array();
			if ($varsEntityNation['flagConsumptionTaxGeneralRule']) {
				if ($varsEntityNation['flagConsumptionTaxDeducted']) {
					$arrayOption = $varsConsumptionTax['generalEach'];

				} else {
					$arrayOption = $varsConsumptionTax['generalProration'];
				}

			} else {
				$arrayOption = $varsConsumptionTax['simple'];
			}
			$varsSearch['restOption']['commaTax'] = $arrayOption;

		} else {
			$arrayNew = array();
			$array = $varsSearch['firstOption'];
			foreach ($array as $key => $value) {
				if (preg_match("/^commaTax\-/", $value['value'])) {
					continue;
				}
				$arrayNew[] = $value;
			}
			$varsSearch['firstOption'] = $arrayNew;
		}

		if ($varsEntityNation['flagConsumptionTaxFree']) {
			$arrayNew = array();
			$array = $varsSearch['firstOption'];
			foreach ($array as $key => $value) {
				if (preg_match("/^commaTaxRate\-/", $value['value'])) {
					continue;
				}
				$arrayNew[] = $value;
			}
			$varsSearch['firstOption'] = $arrayNew;
		}

		if (!$varsEntityNation['flagConsumptionTaxFree'] && !$varsEntityNation['flagConsumptionTaxIncluding']) {
			$arrWithoutCalc = $this->_getWithoutCalc(array('vars' => $varsConsumptionTax['arrStrWithoutCalc']));
			$varsSearch['restOption']['commaTaxWithoutCalc'] = $arrWithoutCalc['arrSelectTag'];

		} else {
			$arrayNew = array();
			$array = $varsSearch['firstOption'];
			foreach ($array as $key => $value) {
				if (preg_match("/^commaTaxWithoutCalc/", $value['value'])) {
					continue;
				}
				$arrayNew[] = $value;
			}
			$varsSearch['firstOption'] = $arrayNew;
		}

		$arrFlagReport = $this->_getFlagReport(array('vars' => $vars));
		$varsSearch['restOption']['report'] = $arrFlagReport['arrSelectTag'];

		$varsStampTerm = $this->_getVarsStampTerm(array(
			'varsFlag'         => array(
				'flagFiscalPeriod' => 'f1',
			),
			'varsEntityNation' => $varsEntityNation,
			'numFiscalPeriod'  => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsStampTerm2 = $this->_getVarsStampTerm(array(
			'varsFlag'         => array(
				'flagFiscalPeriod' => 'f21',
			),
			'varsEntityNation' => $varsEntityNation,
			'numFiscalPeriod'  => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$classCalcDictionary = $this->_getClassCalc(array('flagType' => 'Dictionary'));
		$varsDictionaryItem = $classCalcDictionary->allot((array('flagStatus' => 'varsItem')));

		$idAccount = $varsAccount['id'];

		$vars['varsRule'] = array(
			'varsStampTerm'      => $varsStampTerm,
			'varsStampTerm2'     => $varsStampTerm2,
			'stampUpdate'        => $varsPluginAccountingPreference['stampUpdate'],
			'arrAccountTitle'    => $arrAccountTitle,
			'arrDepartment'      => $arrDepartment,
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'varsEntityNation'   => $varsEntityNation,
			'varsConsumptionTax' => $varsConsumptionTax,
			'arrFlagReport'      => $arrFlagReport,
			'varsFSItem'         => $this->_getVarsFSItem(),
			'idAccount'          => $idAccount,
			'varsDictionaryItem' => $varsDictionaryItem,
		);

		$this->_updateVarsDetailTemplate($vars);

		return $vars;
	}

	/**
		(array(
			'vars' => $vars,
		))
	 */
	protected function _getRate($arr)
	{
		$vars = $arr['vars'];

		$arrayNew = array();
		$array = $vars;
		foreach ($array as $key => $value) {
			$temp = array();
			$temp['strTitle'] = $value;
			$temp['value'] = $key;
			$arrayNew[] = $temp;
		}
		$data = array(
			'arrStrTitle'  => $vars,
			'arrSelectTag' => $arrayNew,
		);

		return $data;
	}

	/**
		(array(
			'vars' => $vars,
		))
	 */
	protected function _getWithoutCalc($arr)
	{
		$vars = $arr['vars'];

		$arrayNew = array();
		$array = $vars;
		foreach ($array as $key => $value) {
			$temp = array();
			$temp['strTitle'] = $value;
			$temp['value'] = $key;
			$arrayNew[] = $temp;
		}
		$data = array(
			'arrStrTitle'  => $vars,
			'arrSelectTag' => $arrayNew,
		);

		return $data;
	}

	/**

	 */
	protected function _updateVarsDetailTemplate(&$vars)
	{
		global $classTime;

		$array = &$vars['portal']['varsDetail']['templateDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'FlagFiscalReport') {
				$array[$key]['arrayOption'] = $vars['varsRule']['arrFlagReport']['arrSelectTag'];
				array_unshift($array[$key]['arrayOption'], $vars['varsItem']['arrayOptionNone']);

			} elseif ($value['id'] == 'ArrCommaIdLogFile') {
				$flagSelect = $this->_checkAccess(array(
					'flagAllUse'    => 0,
					'flagAuthority' => 'select',
					'idTarget'      => $this->_extSelf['idFile'],
				));
				$flagInsert = $this->_checkAccess(array(
					'flagAllUse'    => 0,
					'flagAuthority' => 'insert',
					'idTarget'      => $this->_extSelf['idFile'],
				));
				if (!$flagSelect || !$flagInsert) {
					$array[$key]['varsFormArea']['varsTree']['varsStatus']['flagAddUse'] = 0;
				}
				if (!$flagSelect) {
					$array[$key]['varsFormArea']['varsTree']['varsStatus']['flagLinkUse'] = 0;
				}

			} elseif ($value['id'] == 'StampBook') {
				$data = $this->_getNumFiscalTermStamp();

				$stampMin = $data['stampMin'];
				$strMin = $classTime->getDisplay(array(
					'flagType' => 'year/date',
					'stamp'    => $stampMin,
				));

				$stampMax = $data['stampMax'];
				$strMax = $classTime->getDisplay(array(
					'flagType' => 'year/date',
					'stamp'    => $stampMax,
				));
				$stampMain = TIMESTAMP;
				if ($stampMin > $stampMain) {
					$stampMain = $stampMin;
				}

				$array[$key]['strExplain'] = str_replace('<%stampMin%>', $strMin, $array[$key]['strExplain']);
				$array[$key]['strExplain'] = str_replace('<%stampMax%>', $strMax, $array[$key]['strExplain']);
				$array[$key]['varsFormCalender']['varsStatus']['stampMin'] = $stampMin * 1000;
				$array[$key]['varsFormCalender']['varsStatus']['stampMain'] = TIMESTAMP * 1000;
				$array[$key]['varsFormCalender']['varsStatus']['stampMax'] = $stampMax * 1000;

			} elseif ($value['id'] == 'JsonDetail') {
				$varsJournalItem = $this->_getVarsJournalItem();
				$arrayOption = $varsJournalItem['arrayOption'];
				$i = 0;
				foreach ($arrayOption as $keyOption => $valueOption) {
					//$i dummy data cause not same value
					$arrayOption[$keyOption]['value'] = $valueOption['value'] . ',' . $i;
					$i++;
				}
				$array[$key]['varsFormJournal']['varsTmpl']['varsSelectTag']['varsBtnDictionary'] = $arrayOption;
			}
		}
	}

	/**

	 */
	protected function _getVarsJournalItem()
	{
		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJournal'],
		));

		return $vars;
	}

	/**
	 *
	 */
	protected function _getNumFiscalTermStamp()
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$numTimeZone = PLUGIN_ACCOUNTING_NUM_TIME_ZONE;

		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$numFiscalPeriodStart = $varsPluginAccountingEntity[$varsPluginAccountingAccount['idEntityCurrent']]['numFiscalPeriodStart'];
		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));
		$numFiscalBeginningYear = $varsEntityNation['numFiscalBeginningYear'];
		$numCurrentYear = $numFiscalBeginningYear;

		$strTimeZone = (-1 * $numTimeZone) . 'hours';
		$numYear = $numCurrentYear;
		$numMonth = $varsEntityNation['numFiscalBeginningMonth'];
		$dateTime = new DateTime("$numYear-$numMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stampMin = $dateTime->format('U');

		$numEndMonth = $varsEntityNation['numFiscalBeginningMonth'] + $varsEntityNation['numFiscalTermMonth'];
		if ($numEndMonth > 12) {
			$numCurrentYear++;
			$numEndMonth -= 12;
		}

		$numYear = $numCurrentYear;
		$numMonth = $numEndMonth;
		$dateTime = new DateTime("$numYear-$numMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stampMax = $dateTime->format('U') - 1;

		$data = array(
			'stampMin' => $stampMin,
			'stampMax' => $stampMax,
		);

		return $data;

	}

	/**
		(array(
			'vars' => $vars,
		))
	 */
	protected function _getFlagReport($arr)
	{
		global $varsPluginAccountingAccount;

		$vars = $arr['vars'];

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrayNew = array();
		$arrStrTitle = array();
		$array = $vars['portal']['varsNavi']['search']['varsDetail']['varsSearchItem']['templateDetail']['restOption']['report'];

		foreach ($array as $key => $value) {
			if ($value['value'] == 'f1') {
				$arrayNew[] = $value;
				$arrStrTitle[$value['value']]['strTitle'] = $value['strTitle'];
			}
		}

		if ($varsEntityNation['numFiscalTermMonth'] == 12 && !PLUGIN_ACCOUNTING_FLAG_CORPORATION) {
			foreach ($array as $key => $value) {
				if (preg_match("/^(f2|f4)/", $value['value'])) {
					$arrayNew[] = $value;
					$arrStrTitle[$value['value']]['strTitle'] = $value['strTitle'];
				}
			}
		}

		$data = array(
			'arrStrTitle'  => $arrStrTitle,
			'arrSelectTag' => $arrayNew,
		);

		return $data;
	}


	/**
		$varsJson = $this->checkValueSearch(array(
			'varsValue'  => array(),
			'varsSearch' => array(),
		));
	 */
	public function checkValueSearch($arr)
	{
		$array = $arr['varsValue'];
		$arrayNew = array();
		$num = 0;
		foreach ($array as $key => $value) {

			$tmplVars = $arr['varsSearch']['varsDetail']['varsMyRecord']['varsFormList']['templateDetail'];
			$tmplVars['id'] = $key + 1;
			$tmplVars['numSort'] = $key + 1;
			$tmplVars['value'] = $value['value'];
			if (is_null($value['vars'])) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
				}
				exit;
			}
			$tmplVars['vars'] = $value['vars'];

			$tmplVars['vars']['flagNow'] = $this->_checkValueSearchFlagNow(array(
				'varsSearch' => $arr['varsSearch'],
				'flagNow'    => $tmplVars['vars']['flagNow'],
			));

			$tmplVars['vars']['flagApply'] = $this->_checkValueFlagApply(array(
				'varsOption' => $arr['varsSearch']['varsDetail']['varsDetail']['templateDetail']['logApply']['arrayOption'],
				'flagApply'  => $tmplVars['vars']['flagApply'],
			));

			$tmplVars['vars']['varsSort'] = $this->_checkValueSearchSort(array(
				'varsSearch' => $arr['varsSearch'],
				'vars'       => $tmplVars['vars']['varsSort'],
			));

			if ($tmplVars['vars']['flagNow'] == 'item') {
				$tmplVars['vars']['varsItem'] = $this->_checkValueSearchItem(array(
					'varsSearch' => $arr['varsSearch'],
					'vars'       => $tmplVars['vars']['varsItem'],
				));
			}
			$arrayNew[$num] = $tmplVars;
			$num++;
		}

		return $arrayNew;
	}

	/**
	 *
	 */
	protected function _checkValueFlagApply($arr)
	{
		$array = $arr['varsOption'];
		$flag = 0;
		foreach ($array as $key => $value) {
			if ($arr['flagApply'] == $value['value']) {
				$flag = 1;
				break;
			}
		}
		if (!$flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
			}
			exit;
		}

		return $arr['flagApply'];
	}


	/**
	 *
	 */
	protected function _iniDetailDelete()
	{
		$this->_setClassExt(array('strClass' => 'LogDelete'));
	}

	/**
	 *
	 */
	protected function _iniListDelete()
	{
		$this->_setClassExt(array('strClass' => 'LogDelete'));
	}

	/**
		(array(
			'idTarget' => '',
		))
	 */
	protected function _getVarsLog($arr)
	{
		global $classDb;
		global $varsRequest;

		global $varsPluginAccountingAccount;

		$arrWhere = array(
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
				'strColumn'     => 'idLog',
				'flagCondition' => 'eq',
				'value'         => $arr['idTarget'],
			),
		);

		if (!is_null($arr['flagRemove'])) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'flagRemove',
				'flagCondition' => 'eq',
				'value'         => $arr['flagRemove'],
			);
		}

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLog',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => $arrWhere,
		));


		return $rows['arrRows'][0];

	}

	/**
	 *
	 */
	protected function _iniDetailBack()
	{
		$this->_setClassExt(array('strClass' => 'LogBack'));
	}

	/**
	 *
	 */
	protected function _iniListImport()
	{
		$this->_setClassExt(array('strClass' => 'LogImportMail'));
	}

	/**
	 *
	 */
	protected function _iniListBack()
	{
		$this->_setClassExt(array('strClass' => 'LogBack'));
	}

	/**
	 *
	 */
	protected function _iniDetailPermit()
	{
		$this->_setClassExt(array('strClass' => 'LogPermit'));
	}

	/**
	 *
	 */
	protected function _iniListPermit()
	{
		$this->_setClassExt(array('strClass' => 'LogPermit'));
	}

	/**
	 *
	 */
	protected function _iniListPrint()
	{
		$this->_setClassExt(array('strClass' => 'LogOutput'));
	}

	/**
	 *
	 */
	protected function _iniListOutput()
	{
		$this->_setClassExt(array('strClass' => 'LogOutput'));
	}

	/**
	 *
	 */
	protected function _iniDetailOutput()
	{
		$this->_setClassExt(array('strClass' => 'LogOutput'));
	}


}
