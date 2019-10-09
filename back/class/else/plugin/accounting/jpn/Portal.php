<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_Portal extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idLog'            => 'logWindow',
		'idFile'           => 'fileWindow',
		'idCash'           => 'cashWindow',
		'idBanks'          => 'banksWindow',
		'idFixedAssets'    => 'fixedAssetsWindow',
		'pathDirLibJs'     => 'else/plugin/accounting/js/lib/',
		'pathDirLibVars'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/lib/',
		'pathTplJs'        => 'else/plugin/accounting/js/jpn/portal.js',
		'pathVarsJs'       => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/portal.php',
		'pathVarsJsElse'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/<numYearSheet>/<flagCorporation>/portal.php',
		'pathVarsJsConfig' => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/portalConfig.php',
		'pathVarsItem'     => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/portalItem.php',
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
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
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
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingPreference;

		global $classSmarty;

		$contents = $this->_getJsLib();

		$vars = $this->_getVarsJs();

		$vars['token'] = $this->setToken();

		/*20190401 start*/
		$vars['strTitle'] = $this->_getStrTitle(array(
			'strTitle'       => $vars['strTitle20190401'],
			'strTitleConfig' => $vars['strTitleConfig'],
			'varsItem'       => $vars['varsItem'],
		));
		/*20190401 end*/

		$array = array('Log', 'File', 'Banks', 'Cash', 'FixedAssets');
		foreach ($array as $key => $value) {
			$vars['flagAuthority' . $value] = $this->_checkAccess(array(
				'flagAllUse'    => 0,
				'flagAuthority' => 'select',
				'idTarget'      => $this->_extSelf['id' . $value],
			));
		}

		$varsAuthority = $this->_getVarsAuthority(array());

		$vars['accessCode'] = ($varsPluginAccountingPreference['accessCode'])? $varsPluginAccountingPreference['accessCode'] : '';
		$vars['flagIdAccountTitle'] = ($varsPluginAccountingPreference['flagIdAccountTitle'])? 1 : 0;
		$vars['flagAdmin'] = ($varsAuthority == 'admin')? 1 : 0;

		$json = json_encode($vars);
		$classSmarty->assign('varsLoad', $json);
		$classSmarty->assign('numNews', $this->getNumNews());
		$contents .= $classSmarty->fetch($this->_extSelf['pathTplJs']);

		$this->sendJs(array(
			'data' => $contents,
		));
	}

	/**
	 *
	 */
	protected function _getJsLib()
	{
		global $classSmarty;

		$array = scandir(PATH_BACK_TPL_TEMPLATES . $this->_extSelf['pathDirLibJs']);
		$contents = '';
		foreach ($array as $key => $value) {
			if ( preg_match( "/^\.{1,2}$/", $value)) {
				continue;
			}
			$arr = preg_split("/\./", $value);
			$fileName = $arr[0];
			$vars = $this->getVars(array(
				'path' => $this->_extSelf['pathDirLibVars'] . $fileName . '.php',
			));
			if ($vars) {
				$json = json_encode($vars);
				$classSmarty->assign('varsLoad', $json);

			}
			$contents .= $classSmarty->fetch($this->_extSelf['pathDirLibJs'] . $value);

		}

		return $contents;

	}

	/**
	 *
	 */
	protected function _getStrTitle($arr)
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		if ($varsPluginAccountingEntity) {
			if ($varsPluginAccountingAccount['idEntityCurrent']) {
				if ($varsPluginAccountingEntity[$varsPluginAccountingAccount['idEntityCurrent']]['flagConfig']) {
					if ($this->_getVarsFlagAdmin()) {
						$arr['strTitle'] = $arr['strTitleConfigAdmin'];
					} else {
						$arr['strTitle'] = $arr['strTitleConfig'];
					}
				}

				$arr['strTitle'] = $this->_getStrTitleData(array(
					'strTitle' => $arr['strTitle'],
					'varsItem' => $arr['varsItem'],
				));




			} else {
				$arr['strTitle'] = '';
			}

		} else {
			$arr['strTitle'] = '';
		}

		return $arr['strTitle'];
	}
	/**
	 *
	 */
	protected function _getStrTitleData($arr)
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$strTitle = $varsPluginAccountingEntity[$idEntity]['strTitle'];

		$arr['strTitle'] = str_replace("<%idEntityCurrent%>", $strTitle, $arr['strTitle']);
		$arr['strTitle'] = str_replace("<%numFiscalPeriodCurrent%>", $varsPluginAccountingAccount['numFiscalPeriodCurrent'], $arr['strTitle']);

		$varsPeriod = $this->_getVarsFiscalPeriod(array(
			'flagFiscalPeriod' => 'f1',
			'numFiscalPeriod'  => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$strStatus = '';
		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done)$/", $flagCurrentFlagNow)) {
			$strStatus = $arr['varsItem']['strLock'];

		} elseif (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
			$strStatus = $arr['varsItem']['strTempLock'];
		}
		$arr['strTitle'] = str_replace('<%strStatus%>', $strStatus, $arr['strTitle']);
		$arr['strTitle'] = str_replace('<%strStartYear%>', $varsPeriod['numStartYear'], $arr['strTitle']);
		$arr['strTitle'] = str_replace('<%strEndYear%>', $varsPeriod['numEndYear'], $arr['strTitle']);

		$arr['strTitle'] = str_replace('<%strStartNengoYear%>', $varsPeriod['strStartNengoYear'], $arr['strTitle']);
		$arr['strTitle'] = str_replace('<%strEndNengoYear%>', $varsPeriod['strEndNengoYear'], $arr['strTitle']);

		$arr['strTitle'] = str_replace('<%strStartMonth%>', $varsPeriod['numStartMonth'], $arr['strTitle']);
		$arr['strTitle'] = str_replace('<%strEndMonth%>', $varsPeriod['numEndMonth'], $arr['strTitle']);

		return $arr['strTitle'];
	}

	/**
	 *
	 */
	protected function _getVarsFlagAdmin()
	{
		global $varsAccount;

		return ($this->_checkModuleAdmin(array('idAccount' => $varsAccount['id'],)));

	}

	/**
	 *
	 */
	protected function _getVarsJs()
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;
		global $varsAccount;

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent']
		));

		$vars = $this->getVars(array(
			'path' => $this->_self['pathVarsJs'],
		));

		$varsStart = $vars['portal']['varsDetail']['varsStart'];
		$varsStartTmpl = $vars['portal']['varsDetail']['varsStartTmpl'];

		if (!$varsPluginAccountingEntity) {
			if ($this->_getVarsFlagAdmin()) {

				$varsStart = $varsStartTmpl['InfoEntityNone'];

				$array = array('IdEntityCurrent', 'EntityCurrent');
				foreach ($array as $key => $value) {
					$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_removeVarsTree(array(
						'vars'       => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
						'idTarget'   => $value,
					));
				}

			} else {

				$varsStart = $varsStartTmpl['InfoEntityNoneUser'];

				$array = array('Admin', 'IdEntityCurrent', 'EntityCurrent');
				foreach ($array as $key => $value) {
					$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_removeVarsTree(array(
						'vars'       => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
						'idTarget'   => $value,
					));
				}
			}

		} else {
			if ($varsPluginAccountingAccount['idEntityCurrent']) {
				if ($varsPluginAccountingEntity[$varsPluginAccountingAccount['idEntityCurrent']]['flagConfig']) {
					if ($this->_getVarsFlagAdmin()) {

						$varsStart = $varsStartTmpl['InfoEntityCurrentConfig'];

						$varsEntityConfig = $this->getVars(array(
							'path' => $this->_extSelf['pathVarsJsConfig'],
						));
						$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_insertVarsTree(array(
							'vars'       => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
							'idTarget'   => 'EntityCurrent',
							'varsTarget' => $varsEntityConfig,
						));
					} else {

						$varsStart = $varsStartTmpl['InfoEntityNoneUser'];

						$array = array('Admin');
						foreach ($array as $key => $value) {
							$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_removeVarsTree(array(
								'vars'       => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
								'idTarget'   => $value,
							));
						}
					}

				} else {
					$path = $this->_extSelf['pathVarsJs'];
					if ($varsEntityNation['flagCorporation'] == 2) {
						$path = $this->_extSelf['pathVarsJsElse'];
						$path = str_replace('<numYearSheet>', PLUGIN_ACCOUNTING_NUM_YEAR_SHEET, $path);
						$path = str_replace('<flagCorporation>', PLUGIN_ACCOUNTING_FLAG_CORPORATION, $path);
					}
					$varsEntity = $this->getVars(array(
						'path' => $path,
					));
					if (!$this->_getVarsFlagAdmin()) {
						if (!$varsPluginAccountingAccount['arrCommaIdEntity']) {

							$varsStart = $varsStartTmpl['InfoEntityNoneUser'];

							$array = array('Admin');
							foreach ($array as $key => $value) {
								$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_removeVarsTree(array(
									'vars'       => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
									'idTarget'   => $value,
								));
							}

						} else {
							$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_removeVarsTree(array(
								'vars'       => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
								'idTarget'   => 'Admin',
							));
							$varsEntity = $this->_removeVarsTree(array(
								'vars'       => $varsEntity,
								'idTarget'   => 'AdminEntityCurrent',
							));
							$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_insertVarsTree(array(
								'vars'       => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
								'idTarget'   => 'EntityCurrent',
								'varsTarget' => $varsEntity,
							));
						}

					} else {
						$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_insertVarsTree(array(
							'vars'       => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
							'idTarget'   => 'EntityCurrent',
							'varsTarget' => $varsEntity,
						));
					}
				}

			} else {
				if ($this->_getVarsFlagAdmin()) {

					$varsStart = $varsStartTmpl['InfoEntityCurrent'];

					$array = array('EntityCurrent');
					foreach ($array as $key => $value) {
						$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_removeVarsTree(array(
							'vars'       => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
							'idTarget'   => $value,
						));
					}

				} else {

					$varsStart = $varsStartTmpl['InfoEntityCurrent'];

					$array = array('Admin');
					foreach ($array as $key => $value) {
						$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_removeVarsTree(array(
							'vars'       => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
							'idTarget'   => $value,
						));
					}
				}
			}
		}

		if ($varsPluginAccountingEntity) {
			$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
			if ($idEntity) {
				$varsEntityNation = $this->_getVarsEntityNation(array(
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent']
				));
				if ($varsEntityNation['flagConsumptionTaxFree']) {
					$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_removeTreeData(array(
						'vars'     => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
						'idTarget' => 'collect',
						'strMatch' => '^(consumptionTaxSheetWindow|consumptionTaxListWindow)$',
					));
					/*
					$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_removeTreeData(array(
						'vars'     => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
						'idTarget' => 'details',
						'strMatch' => '^(consumptionTaxWindow)$',
					));
					*/
				}
				if ($varsPluginAccountingAccount['numFiscalPeriodCurrent'] != $varsPluginAccountingEntity[$idEntity]['numFiscalPeriodStart']) {
					$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_removeTreeData(array(
						'vars'     => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
						'idTarget' => 'setteing',
						'strMatch' => '^(balanceData)$',
					));
				}
			}
		}

		if (!$this->_getVarsFlagAdmin()) {
			$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_updateVarsAccess(
				$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail']
			);
			$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_deleteVarsAccess(
				$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail']
			);
		}

		$vars['portal']['varsDetail']['varsStart'] = $varsStart;

		$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_updateVars(
			$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail']
		);

		return $vars;
	}

	/**
	 *
	 */
	protected function _deleteVarsAccess($vars)
	{
		$arrayNew = array();
		foreach ($vars as $key => $value) {
			if ($value['child']) {
				$value['child'] = $this->_deleteVarsAccess($value['child']);
			}
			if (!is_null($value['vars']['flagAccessUse'])) {
				if ($value['flagUse']) {
					$arrayNew[] = $value;
				}
			} else {
				$arrayNew[] = $value;
			}
		}

		return $arrayNew;
	}

	/**
	 *
	 */
	protected function _updateVarsAccess($vars)
	{
		global $varsAccount;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingAuthority;
		global $varsPluginAccountingAccess;
		global $varsPluginAccountingAccountsEntity;

		$idAccount = $varsAccount['id'];
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$idAuthority = $varsPluginAccountingAccountsEntity[$idAccount][$idEntity]['idAuthority'];
		$varsAuthority = $varsPluginAccountingAuthority[$idAuthority];
		$idAccess = $varsPluginAccountingAccountsEntity[$idAccount][$idEntity]['idAccess'];
		$varsAccess = $varsPluginAccountingAccess[$idEntity][$idAccess];

		foreach ($vars as $key => $value) {
			if (!is_null($value['vars']['flagAccessUse'])) {
				if ($value['vars']['flagAccessUse']) {
					$idTarget = $value['vars']['idTarget'];
					if ((int) $varsAccess['jsonData'][$idTarget] || $idAccess == 1) {
						if ($value['vars']['flagAllUse']) {
							if ((int) $varsAuthority['flagAllSelect']) {
								$flagParent = 1;
								$vars[$key]['flagUse'] = 1;

							} else {
								$vars[$key]['flagUse'] = 0;
							}

						} else {
							$vars[$key]['flagUse'] = 1;
						}

					} else {
						$vars[$key]['flagUse'] = 0;
					}
				}
			}

			if ($value['child']) {
				$vars[$key]['child'] = $this->_updateVarsAccess($vars[$key]['child']);
			}
		}

		return $vars;
	}

	/**
	 *
	 */
	protected function _updateVars($vars)
	{
		foreach ($vars as $key => $value) {
			$idTarget = $value['vars']['idTarget'];
			$id = $value['id'];

			if ($id == 'Consumption') {
				$method = '_updateVars' . ucwords($id);
				if (method_exists($this, $method)) {
					$vars[$key] = $this->$method(array('vars' => $vars[$key]));
				}

			} elseif ($id == 'BalanceData') {
				$method = '_updateVars' . ucwords($id);
				if (method_exists($this, $method)) {
					$vars[$key] = $this->$method(array('vars' => $vars[$key]));
				}

			} elseif (!preg_match( "/Window$/", $idTarget) && !$value['flagFoldUse']) {
				$method = '_updateVars' . ucwords($idTarget);
				if (method_exists($this, $method)) {
					$vars[$key] = $this->$method(array('vars' => $vars[$key]));
				}
			}

			if ($value['child']) {
				$vars[$key]['child'] = $this->_updateVars($vars[$key]['child']);
			}
		}

		return $vars;
	}


	/**
	 *
	 */
	protected function _iniNaviReload()
	{
		global $varsPluginAccountingPreference;
		global $varsRequest;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingPreference['stampUpdate'],
				'flagSearch'  => 0,
			));
		}

		$vars = $this->_getVarsJs();

		$this->sendValue(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
		));
	}


	/**
	 *
	 */
	protected function _iniDetailReload()
	{
		global $varsPluginAccountingPreference;
		global $varsRequest;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingPreference['stampUpdate'],
				'flagSearch'  => 0,
			));
		}

		$vars = $this->_getVarsJs();
		$varsTarget = $this->getVarsTarget(array(
			'vars' => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail']
		));
		if (!$varsTarget) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}
/*
		$method = '_updateVars' . ucwords($varsTarget['vars']['idTarget']);
		if (method_exists($this, $method)) {
			$varsTarget = $this->$method(array('vars' => $varsTarget));
		}
*/

		$this->sendValue(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $varsTarget,
		));
	}

	/**
	 *
	 */
	protected function _updateVarsUserBoard($arr)
	{
		global $varsPluginAccountingAccount;
		global $varsRequest;
		global $varsAccount;
		global $classCheck;

		$vars = $arr['vars'];

		$flagAdmin = $classCheck->checkModule(array(
			'idModule' => $varsRequest['query']['module'],
			'flagType' => 'Admin'
		));

		$flagBreakEvenPoint = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'select',
			'idTarget'      => 'breakEvenPointWindow',
		));

		$flagCash = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'select',
			'idTarget'      => 'cashWindow',
		));

		$flagLog = $this->_checkAccess(array(
			'flagAllUse'    => 0,
			'flagAuthority' => 'select',
			'idTarget'      => 'logWindow',
		));

		$flagBanks = $this->_checkAccess(array(
			'flagAllUse'    => 0,
			'flagAuthority' => 'select',
			'idTarget'      => 'banksWindow',
		));

		$flagFixedAssets = $this->_checkAccess(array(
			'flagAllUse'    => 0,
			'flagAuthority' => 'select',
			'idTarget'      => 'fixedAssetsWindow',
		));

		$flagFile = $this->_checkAccess(array(
			'flagAllUse'    => 0,
			'flagAuthority' => 'select',
			'idTarget'      => 'fileWindow',
		));

		$flagLogCash = $this->_checkAccess(array(
			'flagAllUse'    => 0,
			'flagAuthority' => 'select',
			'idTarget'      => 'cashWindow',
		));

		$varsAuthority = $this->_getVarsAuthority(array());

		$flagCurrent = $this->_checkCurrent();

		$array = &$vars['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			$varsTmpl = $value['varsBoard']['varsComment']['varsTmpl'];
			if ($flagCurrent) {
				$arrayNew = array();
				if ($flagLog) {
					$idWindow = 'logWindow';
					$classCalcLogBoard = $this->_getClassCalc(array('flagType' => 'LogBoard'));
					$arrayNum = $classCalcLogBoard->allot(array(
						'flagStatus'      => 'num',
						'strNation'       => ucwords(PLUGIN_ACCOUNTING_STR_NATION),
						'varsAccount'     => $varsAccount,
						'varsAuthority'   => $varsAuthority,
						'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
						'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
					));
					foreach ($arrayNum as $keyNum => $valueNum) {
						if ($valueNum) {
							$tmplDetail = $value['varsBoard']['varsComment']['tmplDetail'];
							$tmplDetail['strTitle'] = $varsTmpl['varsLog'][$keyNum];
							$tmplDetail['vars']['id'] = $keyNum;
							$idTarget = 'Log';
							if ($keyNum == 'numRetry') {
								$idTarget = 'LogImportRetry';

							} elseif ($keyNum == 'numImport') {
								$idTarget = 'LogImport';
							}
							$tmplDetail['vars']['idTarget'] = $idTarget;
							$arrayNew[] = $tmplDetail;
						}
					}
				}
				if ($flagBanks) {
					$idWindow = 'banksWindow';
					$classCalcBanksBoard = $this->_getClassCalc(array('flagType' => 'BanksBoard'));
					$arrayNum = $classCalcBanksBoard->allot(array(
						'flagStatus'      => 'num',
						'varsAccount'     => $varsAccount,
						'varsAuthority'   => $varsAuthority,
						'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
						'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
					));
					foreach ($arrayNum as $keyNum => $valueNum) {
						if ($valueNum) {
							$tmplDetail = $value['varsBoard']['varsComment']['tmplDetail'];
							$tmplDetail['strTitle'] = $varsTmpl['varsBanks'][$keyNum];
							$tmplDetail['vars']['id'] = $keyNum;
							$idTarget = 'Banks';
							if ($keyNum == 'numAccount') {
								$idTarget = 'BanksAccount';
							}
							$tmplDetail['vars']['idTarget'] = $idTarget;
							$arrayNew[] = $tmplDetail;
						}
					}
				}
				if ($flagFixedAssets) {
					$idWindow = 'fixedAssetsWindow';
					$classCalcFixedAssetsBoard = $this->_getClassCalc(array('flagType' => 'FixedAssetsBoard'));
					$arrayNum = $classCalcFixedAssetsBoard->allot(array(
						'flagStatus'      => 'num',
						'varsAccount'     => $varsAccount,
						'varsAuthority'   => $varsAuthority,
						'strNation'       => ucwords(PLUGIN_ACCOUNTING_STR_NATION),
						'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
						'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
					));
					foreach ($arrayNum as $keyNum => $valueNum) {
						if ($valueNum) {
							$tmplDetail = $value['varsBoard']['varsComment']['tmplDetail'];
							$tmplDetail['strTitle'] = $varsTmpl['varsFixedAssets'][$keyNum];
							$tmplDetail['vars']['id'] = $keyNum;
							$tmplDetail['vars']['idTarget'] = 'FixedAssets';
							$arrayNew[] = $tmplDetail;
						}
					}
				}

				if ($flagFile) {
					$idWindow = 'fileWindow';
					$numFiscalPeriodTemp = 0;
					$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
					if (preg_match("/^temp/", $flagCurrentFlagNow)) {
						if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
							$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] + 1;

						} elseif (preg_match("/^(tempNext)$/", $flagCurrentFlagNow)) {
							$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] - 1;
						}
					}
					$classCalcFileBoard = $this->_getClassAccountingCalc(array('flagType' => 'FileBoard'));
					$arrayNum = $classCalcFileBoard->allot(array(
						'flagStatus'          => 'num',
						'varsAccount'         => $varsAccount,
						'flagAdmin'           => $flagAdmin,
						'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
						'idEntity'            => $varsPluginAccountingAccount['idEntityCurrent'],
						'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
					));

					foreach ($arrayNum as $keyNum => $valueNum) {
						if ($valueNum) {
							$tmplDetail = $value['varsBoard']['varsComment']['tmplDetail'];
							$tmplDetail['strTitle'] = $varsTmpl['varsFile'][$keyNum];
							$tmplDetail['vars']['id'] = $keyNum;
							$tmplDetail['vars']['idTarget'] = 'File';
							$arrayNew[] = $tmplDetail;
						}
					}
				}
				if ($flagLogCash) {
					$classCalcCashBoard = $this->_getClassCalc(array('flagType' => 'CashBoard'));
					$arrayNum = $classCalcCashBoard->allot(array(
						'flagStatus'      => 'num',
						'varsAccount'     => $varsAccount,
						'varsAuthority'   => $varsAuthority,
						'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
						'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
					));

					foreach ($arrayNum as $keyNum => $valueNum) {
						if ($valueNum) {
							$tmplDetail = $value['varsBoard']['varsComment']['tmplDetail'];
							$tmplDetail['strTitle'] = $varsTmpl['varsCash'][$keyNum];
							$tmplDetail['vars']['id'] = $keyNum;
							$idTarget = 'Cash';
							if ($keyNum == 'numDefer') {
								$idTarget = 'CashDefer';
							}
							$tmplDetail['vars']['idTarget'] = $idTarget;
							$arrayNew[] = $tmplDetail;
						}
					}
				}
				if ($arrayNew) {
					$array[$key]['varsBoard']['varsComment']['varsDetail'] = $arrayNew;
				}
			}

			if (!$flagCurrent || !$flagBreakEvenPoint) {
				$array[$key]['varsBoard']['varsStatus']['flagProfitUse'] = 0;

			} else {
				$classCalcBreakEvenPoint = $this->_getClassCalc(array('flagType' => 'BreakEvenPoint'));
				$tempData = $classCalcBreakEvenPoint->allot(array(
					'flagStatus'      => 'data',
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					'varsDetail'      => array(),
					'varsCollect'     => $value['varsBoard']['varsProfit']['varsCollect'],
					'varsRow'         => $value['varsBoard']['varsProfit']['varsRow'],
				));
				$array[$key]['varsBoard']['varsProfit']['varsCollect'] = $tempData['varsCollect'];
			}

			if (!$flagCurrent || !$flagCash) {
				$array[$key]['varsBoard']['varsStatus']['flagCashUse'] = 0;

			} else {
				$classCalcCashBoard = $this->_getClassCalc(array('flagType' => 'CashBoard'));
				$array[$key]['varsBoard']['varsCash'] = $classCalcCashBoard->allot(array(
					'flagStatus'      => 'data',
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
					'varsCash'        => $value['varsBoard']['varsCash'],
					'strNation'       => ucwords(PLUGIN_ACCOUNTING_STR_NATION),
				));
			}

		}
		return $vars;
	}

	/**
	 *
	 */
	protected function _updateVarsPreference($arr)
	{
		global $varsPluginAccountingPreference;
		global $classEscape;

		$vars = $arr['vars'];

		$array = &$vars['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			$str = $classEscape->toLower(array('str' => $array[$key]['id']));
			$array[$key]['value'] = (is_null($varsPluginAccountingPreference[$str]))? '' : $varsPluginAccountingPreference[$str];
		}

		return $vars;
	}

	/**
	 *
	 */
	protected function _updateVarsAccount($arr)
	{
		global $varsPluginAccountingAccount;
		global $classEscape;

		$vars = $arr['vars'];

		$array = &$vars['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			$str = $classEscape->toLower(array('str' => $array[$key]['id']));
			$array[$key]['value'] = (is_null($varsPluginAccountingAccount[$str]))? '' : $varsPluginAccountingAccount[$str];
		}

		return $vars;
	}

	/**
	 *
	 */
	protected function _updateVarsCharge($arr)
	{
		$vars = $arr['vars'];

		if (!$this->_checkCurrent()) {
			$vars['vars']['varsBtn'] = array();
			$vars['vars']['varsEdit'] = array();
			$vars['vars']['varsDetail'][0]['strExplain']
				 = $vars['vars']['varsDetail'][0]['varsTmpl']['strPast'];

			$array = &$vars['vars']['varsDetail'][0]['varsFormCheck']['varsDetail'];
			foreach ($array as $key => $value) {
				$array[$key]['varsColumnDetail']['btnEditLock'] = 1;
			}
		}

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'])) {
			$vars['vars']['varsBtn'] = array();
		}

		return $vars;
	}

	/**


	 */
	protected function _getOptionTitle($arr)
	{
		$array = $arr['arrayOption'];
		foreach ($array as $key => $value) {
			if ($value['value'] == $arr['idTarget']) {
				return $value['strTitle'];
			}
		}

		return '';
	}

	/**
	 *
	 */
	protected function _updateVarsConsumption($arr)
	{
		global $classDb;
		global $varsAccount;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;
		global $classEscape;

		$vars = $arr['vars'];

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

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
					'value'         => $varsPluginAccountingAccount['idEntityCurrent'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'numFiscalPeriod',
					'flagCondition' => 'eq',
					'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				),
			),
		));

		$varsEntity = $rows['arrRows'][0];

		$flagCurrent = $this->_getCurrentFlagNow(array());

		$varsAuthority = $this->_getVarsAuthority(array());

		$array = &$vars['child'];
		$arrValue = array();
		$arrVars = array();
		foreach ($array as $key => $value) {

			$arrayDetail = $array[$key]['vars']['varsDetail'];
			foreach ($arrayDetail as $keyDetail => $valueDetail) {
				$str = $classEscape->toLower(array('str' => $arrayDetail[$keyDetail]['id']));
				$array[$key]['vars']['varsDetail'][$keyDetail]['value'] = (is_null($varsEntity[$str]))? 0 : (int) $varsEntity[$str];

				$array[$key]['vars']['varsDetail'][$keyDetail]['strExplain']
					 = $array[$key]['vars']['varsDetail'][$keyDetail]['varsTmpl']['strNormal'];

				if (preg_match("/^(done)$/", $flagCurrent)) {
					$array[$key]['vars']['varsDetail'][$keyDetail]['strExplain']
						 = $array[$key]['vars']['varsDetail'][$keyDetail]['varsTmpl']['strPast'];
					$array[$key]['vars']['varsDetail'][$keyDetail]['flagDisabled'] = 1;
					$array[$key]['vars']['varsBtn'] = array();

				}
				$arrValue[$arrayDetail[$keyDetail]['id']] = $array[$key]['vars']['varsDetail'][$keyDetail]['value'];

				$strTitle = $this->_getOptionTitle(array(
					'arrayOption' => $valueDetail['arrayOption'],
					'idTarget'    => $array[$key]['vars']['varsDetail'][$keyDetail]['value']
				));
				$array[$key]['vars']['varsDetail'][$keyDetail]['strExplain']
					= str_replace('<%replace%>', $strTitle, $array[$key]['vars']['varsDetail'][$keyDetail]['strExplain']);
			}
			if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'])) {
				$array[$key]['vars']['varsBtn'] = array();
			}
			$arrVars[$array[$key]['id']] = $array[$key];
		}

		$arrayNew = array();
		$arrayNew[] = $arrVars['FlagConsumptionTaxFree'];

		if (!$arrValue['FlagConsumptionTaxFree']) {
			$arrayNew[] = $arrVars['FlagConsumptionTaxGeneralRule'];
			if ($arrValue['FlagConsumptionTaxGeneralRule']) {
				$arrayNew[] = $arrVars['FlagConsumptionTaxDeducted'];

			} else {
				$arrayNew[] = $arrVars['FlagConsumptionTaxBusinessType'];
			}
			$arrayNew[] = $arrVars['FlagConsumptionTaxIncluding'];
			if (!$arrValue['FlagConsumptionTaxIncluding']) {
				$arrayNew[] = $arrVars['FlagConsumptionTaxWithoutCalc'];
			}

		}

		$vars['child'] = $arrayNew;

		return $vars;
	}

	/**
	 *
	 */
	protected function _updateVarsIdEntityCurrent($arr)
	{
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$vars = $arr['vars'];

		if (!$varsPluginAccountingEntity
			 || ($varsPluginAccountingEntity && !$varsPluginAccountingAccount['idEntityCurrent'])
		) {
			return $vars;
		}

		$varsEntityConfig = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJsConfig'],
		));
		$varsEntity = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent']
		));

		$array = &$vars['vars']['varsDetail'];
		$arrayNew = array();
		foreach ($array as $key => $value) {
			if ($value['id'] == 'IdEntityCurrent') {
				if ($varsPluginAccountingAccount['idEntityCurrent']) {
					$id = $varsPluginAccountingAccount['idEntityCurrent'];
					$strTitle = $varsPluginAccountingEntity[$id]['strTitle'];
					$strItem = '';
					if (!(int) $varsPluginAccountingEntity[$id]['flagConfig']) {
						$varsPeriod = $this->_getVarsFiscalPeriod(array(
							'flagFiscalPeriod' => 'f1',
							'numFiscalPeriod'  => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
						));

						$str = $value['varsTmplTerm'];
						$strTerm = str_replace('<%strStartHeisei%>', $varsPeriod['numStartHeisei'], $str);
						$strTerm = str_replace('<%strEndHeisei%>', $varsPeriod['numEndHeisei'], $strTerm);
						$strTerm = str_replace('<%strStartMonth%>', $varsPeriod['numStartMonth'], $strTerm);
						$strTerm = str_replace('<%strEndMonth%>', $varsPeriod['numEndMonth'], $strTerm);

						/*20190401 start*/
						/*
						 $str = $value['varsTmplTerm'];
						 $strTerm = str_replace('<%strStartHeisei%>', $varsPeriod['numStartHeisei'], $str);
						 $strTerm = str_replace('<%strEndHeisei%>', $varsPeriod['numEndHeisei'], $strTerm);
						 $strTerm = str_replace('<%strStartMonth%>', $varsPeriod['numStartMonth'], $strTerm);
						 $strTerm = str_replace('<%strEndMonth%>', $varsPeriod['numEndMonth'], $strTerm);
						 */
						$str = $value['varsTmplTerm20190401'];
						$strTerm = str_replace('<%strStartNengoYear%>', $varsPeriod['strStartNengoYear'], $str);
						$strTerm = str_replace('<%strEndNengoYear%>', $varsPeriod['strEndNengoYear'], $strTerm);

						$strTerm = str_replace('<%strStartMonth%>', $varsPeriod['numStartMonth'], $strTerm);
						$strTerm = str_replace('<%strEndMonth%>', $varsPeriod['numEndMonth'], $strTerm);
						/*20190401 end*/



						$strItem .= $this->_getVarsIdEntityCurrentStrTitle(array(
							'vars'       => $varsEntityConfig,
							'tmplTarget' => 'flagCorporation',
							'idTarget'   => 'flagCorporation',
							'varsTmpl'   => $value['varsTmplItem'],
							'varsEntity' => $varsEntity,
						));
						$strItem .= $this->_getVarsIdEntityCurrentStrTitle(array(
							'vars'         => $varsEntityConfig,
							'tmplTarget'   => 'numFiscalBeginningYear',
							'idTarget'     => 'numFiscalBeginningYear',
							'varsTmpl'     => $value['varsTmplItem'],
							'varsEntity'   => $varsEntity,
						));
						$strItem .= $this->_getVarsIdEntityCurrentStrTitle(array(
							'vars'       => $varsEntityConfig,
							'tmplTarget' => 'numFiscalBeginningMonth',
							'idTarget'   => 'numFiscalBeginningMonth',
							'varsTmpl'   => $value['varsTmplItem'],
							'varsEntity' => $varsEntity,
							'strTerm'    => $strTerm,
						));
						$strItem .= $this->_getVarsIdEntityCurrentStrTitle(array(
							'vars'       => $varsEntityConfig,
							'tmplTarget' => 'flagCR',
							'idTarget'   => 'flagCR',
							'varsTmpl'   => $value['varsTmplItem'],
							'varsEntity' => $varsEntity,
						));
						$strItem .= $this->_getVarsIdEntityCurrentStrTitle(array(
							'vars'       => $varsEntityConfig,
							'tmplTarget' => 'flagConsumptionTaxFree',
							'idTarget'   => 'flagConsumptionTaxFree',
							'varsTmpl'   => $value['varsTmplItem'],
							'varsEntity' => $varsEntity,
						));
						if (!(int) $varsEntity['flagConsumptionTaxFree']) {
							$strItem .= $this->_getVarsIdEntityCurrentStrTitle(array(
								'vars'       => $varsEntityConfig,
								'tmplTarget' => 'flagConsumptionTaxGeneralRule',
								'idTarget'   => 'flagConsumptionTaxGeneralRule',
								'varsTmpl'   => $value['varsTmplItem'],
								'varsEntity' => $varsEntity,
							));
							if ((int) $varsEntity['flagConsumptionTaxGeneralRule']) {
								$strItem .= $this->_getVarsIdEntityCurrentStrTitle(array(
									'vars'       => $varsEntityConfig,
									'tmplTarget' => 'flagConsumptionTaxDeducted',
									'idTarget'   => 'flagConsumptionTaxDeducted',
									'varsTmpl'   => $value['varsTmplItem'],
									'varsEntity' => $varsEntity,
								));

							} else {
								$strItem .= $this->_getVarsIdEntityCurrentStrTitle(array(
									'vars'       => $varsEntityConfig,
									'tmplTarget' => 'flagConsumptionTaxBusinessType',
									'idTarget'   => 'flagConsumptionTaxBusinessType',
									'varsTmpl'   => $value['varsTmplItem'],
									'varsEntity' => $varsEntity,
								));

							}
							$strItem .= $this->_getVarsIdEntityCurrentStrTitle(array(
								'vars'       => $varsEntityConfig,
								'tmplTarget' => 'flagConsumptionTaxIncluding',
								'idTarget'   => 'flagConsumptionTaxIncluding',
								'varsTmpl'   => $value['varsTmplItem'],
								'varsEntity' => $varsEntity,
							));
							$strItem .= $this->_getVarsIdEntityCurrentStrTitle(array(
								'vars'       => $varsEntityConfig,
								'tmplTarget' => 'flagConsumptionTaxIncluding',
								'idTarget'   => 'flagConsumptionTaxCalc',
								'varsTmpl'   => $value['varsTmplItem'],
								'varsEntity' => $varsEntity,
							));

							if (!(int) $varsEntity['flagConsumptionTaxIncluding']) {
								$strItem .= $this->_getVarsIdEntityCurrentStrTitle(array(
									'vars'       => $varsEntityConfig,
									'tmplTarget' => 'flagConsumptionTaxWithoutCalc',
									'idTarget'   => 'flagConsumptionTaxWithoutCalc',
									'varsTmpl'   => $value['varsTmplItem'],
									'varsEntity' => $varsEntity,
								));
							}

						} else {
							$strItem .= $this->_getVarsIdEntityCurrentStrTitle(array(
								'vars'       => $varsEntityConfig,
								'tmplTarget' => 'flagConsumptionTaxIncluding',
								'idTarget'   => 'flagConsumptionTaxIncluding',
								'varsTmpl'   => $value['varsTmplItem'],
								'varsEntity' => $varsEntity,
							));
						}
					}

					$array[$key]['strExplain'] = str_replace("<%idEntityCurrent%>", $strTitle, $value['varsTmpl']);
					$array[$key]['strExplain'] = str_replace("<%numFiscalPeriodCurrent%>", $varsPluginAccountingAccount['numFiscalPeriodCurrent'], $array[$key]['strExplain']);
					$array[$key]['strExplain'] = str_replace("<%item%>", $strItem, $array[$key]['strExplain']);
				}
			}
		}

		return $this->_updateVarsPreference(array('vars' => $vars));
	}

	/**
	 *
	 */
	protected function _getVarsIdEntityCurrentStrTitle($arr)
	{
		$array = &$arr['vars'][0]['varsTmpl'][$arr['tmplTarget']]['varsDetail'];

		foreach ($array as $key => $value) {
			if ($value['id'] == ucwords($arr['idTarget'])) {
				$arr['varsTmpl'] = str_replace("<%strTitle%>", $value['strTitle'], $arr['varsTmpl']);
				if ($value['id'] == 'NumFiscalBeginningMonth') {
					$arr['varsTmpl'] = str_replace("<%strValue%>", $arr['strTerm'], $arr['varsTmpl']);

					return $arr['varsTmpl'];

				} else {
					$arrayOption = $value['arrayOption'];
				}
				$data = 0;
				foreach ($arrayOption as $keyOption => $valueOption) {
					if ((int) $arr['varsEntity'][$arr['idTarget']] == $valueOption['value']) {
						$arr['varsTmpl'] = str_replace("<%strValue%>", $valueOption['strTitle'], $arr['varsTmpl']);

						return $arr['varsTmpl'];
					}
				}
			}

		}
		return '';
	}

	/**
	 *
	 */
	protected function _updateVarsFlagDepWrite($arr)
	{
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;
		global $classEscape;

		$vars = $arr['vars'];

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent']
		));

		$varsFixedAssets = $this->_getVarsFixedAssets(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent']
		));

		$flagCurrent = $this->_checkCurrent();

		if (!$flagCurrent) {
			$vars['vars']['varsBtn'] = array();
		}

		$arrayNewDetail = array();
		$array = $vars['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			$str = $classEscape->toLower(array('str' => $value['id']));
			$arrData = $value['arrayOption'];
			if ($value['id'] == 'FlagDepWrite') {
				$value = $this->_updateVarsFlagDepWriteFlagFiscalPeriod(array(
					'vars'             => $value,
					'varsEntityNation' => $varsEntityNation,
				));

			} elseif ($value['id'] == 'FlagFractionRatioOperate') {
				if ($varsEntityNation['flagCorporation'] == 1) {
					continue;
				}
			}

			$arrStrTitle = array();
			foreach ($arrData as $keyData => $valueData) {
				$arrStrTitle[$valueData['value']] = $valueData['strTitle'];
			}
			$value['value'] = $varsFixedAssets[$str];

			if (!$flagCurrent) {
				$value['flagDisabled'] = 1;
				$value['strExplain'] = $value['varsTmpl']['strDone'];
				$str = $arrStrTitle[$value['value']];
				$value['strExplain']
							= str_replace("<%replace%>", $str, $value['strExplain']);

			} else {
				$value['strExplain'] = $value['varsTmpl']['strNormal'];
			}
			$arrayNewDetail[] = $value;
		}
		$vars['vars']['varsDetail'] = $arrayNewDetail;

		return $vars;
	}

	/**
		(array(
			'vars'             => $value,
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _updateVarsFlagDepWriteFlagFiscalPeriod($arr)
	{
		$varsEntityNation = $arr['varsEntityNation'];

		$arrayNew = array();
		$array = $arr['vars']['varsTmpl']['arrayOption'];
		foreach ($array as $key => $value) {
			if ($varsEntityNation['numFiscalTermMonth'] == 12) {
				$arrayNew[] = $value;

			} else {
				if (preg_match( "/^(f1)$/", $value['value'])) {
					$arrayNew[] = $value;
				}
			}
		}
		$arr['vars']['varsTmpl']['arrayOption'] = $arrayNew;

		$arrayOption = array();
		if ($varsEntityNation['numFiscalTermMonth'] == 12) {
			$arrayOption = $arr['vars']['varsTmpl']['arrayOption'];

		} else {
			$arrayOption[] = $arr['vars']['varsTmpl']['varsPeriod'];
		}

		$numMonth = (int) $varsEntityNation['numFiscalBeginningMonth'];
		$numEnd = (int) $varsEntityNation['numFiscalTermMonth'];
		for ($i = 0; $i < $numEnd; $i++) {
			$data = array(
				'strTitle' => $numMonth . $arr['vars']['varsTmpl']['strMonth'],
				'value'    => $numMonth,
			);
			$arrayOption[] = $data;
			$numMonth++;
			if ($numMonth > 12) {
				$numMonth = 1;
			}
		}
		$arr['vars']['arrayOption'] = $arrayOption;

		return $arr['vars'];
	}

	/**
	 *
	 */
	protected function _updateVarsPreferenceJson($arr)
	{
		global $varsPluginAccountingPreference;
		global $classEscape;

		$vars = $arr['vars'];

		$array = &$vars['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			$str = $classEscape->toLower(array('str' => $value['id']));
			$arrData = (!$varsPluginAccountingPreference[$str])? array('') : $varsPluginAccountingPreference[$str];
			$num = 0;
			$arrayNew = array();
			foreach ($arrData as $keyData => $valueData) {
				$varsTmpl = $value['varsFormList']['templateDetail'];
				$varsTmpl['id'] = $keyData;
				$varsTmpl['numSort'] = $keyData;
				$varsTmpl['value'] = $valueData;
				$arrayNew[$num] = $varsTmpl;
				$num++;
			}
			$array[$key]['varsFormList']['varsDetail'] = $arrayNew;
		}

		return $vars;
	}

	/**

	 */
	protected function _updateVarsNextData($arr)
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingAccountsEntity;

		$vars = $arr['vars'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $numFiscalPeriod,
		));

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];
		$numFiscalPeriodLock = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriodLock'];
		$net = $numFiscalPeriod - $numFiscalPeriodLock;

		$numFiscalClosingMonth = $varsEntityNation['numFiscalBeginningMonth'] + $varsEntityNation['numFiscalTermMonth'] - 1;
		if ($numFiscalClosingMonth > 12) {
			$numFiscalClosingMonth -= 12;
		}

		$array = $vars['vars']['varsDetail'];
		if ($this->_checkCurrent()) {
			if ($net == 2) {
				//forgot after
				if ($this->_checkEditPrev()) {
					foreach ($array as $key => $value) {
						$array[$key]['flagDisabled'] = 1;
						if ($value['id'] == 'NextData') {
							$array[$key]['strExplain'] = $value['varsTmpl']['strForgot'];

						} elseif ($value['id'] == 'NumFiscalClosingMonth') {
							$varsTmpl = $value['varsTmpl']['arrClosingMonth'];
							$varsTmpl['strTitle'] = $numFiscalClosingMonth . $value['varsTmpl']['strMonth'];
							$varsTmpl['value'] = $numFiscalClosingMonth;
							$array[$key]['arrayOption'] = array($varsTmpl);
							$array[$key]['flagDisabled'] = 1;
							$array[$key]['flagHideNow'] = 1;
							$array[$key]['vars']['flagDisabled'] = $array[$key]['flagDisabled'];
							$array[$key]['strExplain'] = $value['varsTmpl']['strForgot'];
							$array[$key]['value'] = $numFiscalClosingMonth;

						} elseif ($value['id'] == 'FlagCR') {
							$array[$key]['strExplain'] = $value['varsTmpl']['strForgot'];
							$array[$key]['flagDisabled'] = 1;
							$array[$key]['flagHideNow'] = 1;
							$array[$key]['value'] = $varsEntityNation['flagCR'];
						}
					}
					$vars['vars']['varsBtn'] = array();

				//past before
				} else {
					foreach ($array as $key => $value) {
						$array[$key]['flagDisabled'] = 1;
						if ($value['id'] == 'NextData') {
							$array[$key]['strExplain'] = $value['varsTmpl']['strPast'];
							$array[$key]['value'] = 1;

						} elseif ($value['id'] == 'NumFiscalClosingMonth') {
							$varsTmpl = $value['varsTmpl']['arrClosingMonth'];
							$varsTmpl['strTitle'] = $numFiscalClosingMonth . $value['varsTmpl']['strMonth'];
							$varsTmpl['value'] = $numFiscalClosingMonth;
							$array[$key]['arrayOption'] = array($varsTmpl);
							$array[$key]['flagDisabled'] = 1;
							$array[$key]['flagHideNow'] = 1;
							$array[$key]['strExplain'] = $value['varsTmpl']['strPast'];
							$array[$key]['value'] = $numFiscalClosingMonth;

						} elseif ($value['id'] == 'FlagCR') {
							$array[$key]['strExplain'] = $value['varsTmpl']['strPast'];
							$array[$key]['flagDisabled'] = 1;
							$array[$key]['flagHideNow'] = 1;
							$array[$key]['value'] = $varsEntityNation['flagCR'];
						}
					}
				}
			//normal
			} else {
				foreach ($array as $key => $value) {
					if ($value['id'] == 'NextData') {
						$array[$key]['strExplain'] = $value['varsTmpl']['strNormal'];

					} elseif ($value['id'] == 'NumFiscalClosingMonth') {
						$array[$key]['arrayOption'] = $this->_getVarsNextDataOption($value['varsTmpl']['strMonth'], $varsEntityNation);

						if (!count($array[$key]['arrayOption'])) {
							$varsTmpl = $value['varsTmpl']['arrClosingMonth'];
							$varsTmpl['strTitle'] = $numFiscalClosingMonth . $value['varsTmpl']['strMonth'];
							$varsTmpl['value'] = $numFiscalClosingMonth;
							$array[$key]['arrayOption'] = array($varsTmpl);
							$array[$key]['flagDisabled'] = 1;

						}
						$array[$key]['value'] = $numFiscalClosingMonth;
						$array[$key]['strExplain'] = $value['varsTmpl']['strNormal'];
						$array[$key]['strExplain']
							= str_replace("<%replace%>", $numFiscalClosingMonth, $array[$key]['strExplain']);


					} elseif ($value['id'] == 'FlagCR') {
						$array[$key]['strExplain'] = $value['varsTmpl']['strNormal'];
						$array[$key]['value'] = $varsEntityNation['flagCR'];
					}
				}
			}

		//done
		} else {
			foreach ($array as $key => $value) {
				$array[$key]['flagDisabled'] = 1;
				if ($value['id'] == 'NextData') {
					$array[$key]['strExplain'] = $value['varsTmpl']['strDone'];

				} elseif ($value['id'] == 'NumFiscalClosingMonth') {
					$array[$key]['strExplain'] = $value['varsTmpl']['strDone'];
					$varsTmpl = $value['varsTmpl']['arrClosingMonth'];
					$varsTmpl['strTitle'] = $numFiscalClosingMonth . $value['varsTmpl']['strMonth'];
					$varsTmpl['value'] = $numFiscalClosingMonth;
					$array[$key]['arrayOption'] = array($varsTmpl);
					$array[$key]['flagDisabled'] = 1;
					$array[$key]['flagHideNow'] = 1;
					$array[$key]['value'] = $numFiscalClosingMonth;

				} elseif ($value['id'] == 'FlagCR') {
					$array[$key]['flagHideNow'] = 1;
					$array[$key]['strExplain'] = $value['varsTmpl']['strDone'];
					$array[$key]['value'] = $varsEntityNation['flagCR'];
				}
			}
			$vars['vars']['varsBtn'] = array();

		}

		$vars['vars']['varsDetail'] = $array;

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'])) {
			$vars['vars']['varsBtn'] = array();
		}

		return $vars;
	}

	/**

	 */
	protected function _getVarsNextDataOption($strMonth, $varsEntityNation)
	{
		$arrayOption = array();
		$numMonth = $varsEntityNation['numFiscalBeginningMonth'];
		$numEnd = 12;

		for ($i = 0; $i < $numEnd; $i++) {
			$data = array(
				'strTitle' => $numMonth . $strMonth,
				'value'    => $numMonth,
			);
			$arrayOption[] = $data;
			$numMonth++;
			if ($numMonth > 12) {
				$numMonth = 1;
			}
		}

		return $arrayOption;
	}

	/**

	 */
	protected function _updateVarsFlagIdAccountTitle($arr)
	{
		global $varsPluginAccountingPreference;
		global $classEscape;

		$vars = $arr['vars'];

		$array = $vars['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			$id = $classEscape->toLower(array('str' => $array[$key]['id']));
			if (preg_match("/^dummy/", $id)) {
				continue;
			}
			$array[$key]['value'] = $varsPluginAccountingPreference[$id];
		}
		$vars['vars']['varsDetail'] = $array;

		return $vars;
	}

	/**

	 */
	protected function _updateVarsAccessCode($arr)
	{
		global $varsPluginAccountingPreference;
		global $classEscape;

		$vars = $arr['vars'];

		$array = $vars['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			$id = $classEscape->toLower(array('str' => $array[$key]['id']));
			if (preg_match("/^dummy/", $id)) {
				continue;
			}
			$array[$key]['value'] = $varsPluginAccountingPreference[$id];
		}
		$vars['vars']['varsDetail'] = $array;

		return $vars;
	}

	/**

	 */
	protected function _updateVarsRelease($arr)
	{
		$vars = $arr['vars'];

		$array = $vars['vars']['varsBtn'];
		foreach ($array as $key => $value) {
			if ($array[$key]['id'] == 'Release') {
				$array[$key]['path'] = PATH_INFO;
				break;
			}
		}
		$vars['vars']['varsBtn'] = $array;

		return $vars;
	}

	/**
	 *
	 */
	protected function _iniDetailEdit()
	{
		global $classInit;
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsRequest;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];

		if ($idTarget == 'entityConfigEnd') {
			if (!$varsPluginAccountingEntity || !$varsPluginAccountingAccount['idEntityCurrent']) {
				$this->sendValue(array(
					'flag'    => 8,
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));
			}
			$arrValue = $this->_checkDetailEditEntityConfig();

		} else {
			$vars = $this->_getVarsJs();

			if ($idTarget == 'idEntityCurrent') {
				if (!$varsPluginAccountingEntity) {
					$this->sendValue(array(
						'flag'    => 8,
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(),
					));
				}

			} elseif ($idTarget == 'flagIdAccountTitle' || $idTarget == 'accessCode') {


			} else {
				$this->_checkEntity();
			}

			$varsTarget = $this->getVarsTarget(array(
				'vars' => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail']
			));
			if (!$varsTarget) {
				$this->sendValue(array(
					'flag'    => 8,
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));
			}

			$varsTarget['vars']['varsDetail'] = $this->getValue(array(
				'vars' => $varsTarget['vars']['varsDetail']
			));

			$arrValue = $this->checkValue(array(
				'values' => $varsTarget['vars']['varsDetail']
			));

			if ($idTarget != 'idEntityCurrent') {
				if ($idTarget == 'flagConsumptionTaxFree'
					 || $idTarget == 'flagConsumptionTaxGeneralRule'
					 || $idTarget == 'flagConsumptionTaxDeducted'
					 || $idTarget == 'flagConsumptionTaxBusinessType'
					 || $idTarget == 'flagConsumptionTaxIncluding'
					 || $idTarget == 'flagConsumptionTaxWithoutCalc'
				) {
					$flag = $this->_checkAccess(array(
						'flagAllUse'    => 1,
						'flagAuthority' => 'update',
						'idTarget'      => 'consumption',
					));

				} else {
					$flag = $this->_checkAccess(array(
						'flagAllUse'    => 1,
						'flagAuthority' => 'update',
						'idTarget'      => $idTarget,
					));
				}

				if (!$flag) {
					$this->sendVars(array(
						'flag'    => 8,
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => '',
					));
					exit;
				}
			}
		}

		try {
			$dbh->beginTransaction();

			$this->_updateDb($arrValue);

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		if ($idTarget == 'entityConfigEnd') {
			$vars = $this->_getVarsJs();
			$this->sendValue(array(
				'flag'    => 1,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(
					'idTarget'   => $idTarget,
				),
			));

		} elseif ($varsTarget['vars']['idTarget'] == 'flagConsumptionTaxFree'
			|| $varsTarget['vars']['idTarget'] == 'flagConsumptionTaxGeneralRule'
			|| $varsTarget['vars']['idTarget'] == 'flagConsumptionTaxDeducted'
			|| $varsTarget['vars']['idTarget'] == 'flagConsumptionTaxBusinessType'
			|| $varsTarget['vars']['idTarget'] == 'flagConsumptionTaxIncluding'
			|| $varsTarget['vars']['idTarget'] == 'flagConsumptionTaxWithoutCalc'
		) {
			$vars = $this->_getVarsJs();
			$this->sendValue(array(
				'flag'    => 1,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(
					'vars'       => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
					'varsTarget' => $varsTarget,
					'idTarget'   => $varsTarget['vars']['idTarget'],
				),
			));

		} else {
			$method = '_updateVars' . ucwords($varsTarget['vars']['idTarget']);
			if (method_exists($this, $method)) {
				$varsTarget = $this->$method(array(
					'vars'     => $varsTarget,
					'flagDone' => 1,
				));
			}
		}



		$this->sendValue(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $varsTarget,
		));
	}



	/**
	 *
	 */
	protected function _checkDetailEditEntityConfig()
	{
		global $varsRequest;
		global $classEscape;
		global $classDb;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;



		$formValue = $varsRequest['query']['jsonValue']['vars'];

		if (!$varsPluginAccountingEntity[$varsPluginAccountingAccount['idEntityCurrent']]['flagConfig']) {
			$vars = $this->_getVarsJs();
			$this->sendValue(array(
				'flag'    => 1,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(
					'idTarget' => 'entityConfigEnd',
				),
			));
		}

		if (($formValue['FlagCorporation'] == 1 && $formValue['numFiscalBeginningYear'] < 1997)
			|| ($formValue['FlagCorporation'] == 1 && $formValue['numFiscalBeginningYear'] == 1997 && $formValue['NumFiscalBeginningMonth'] < 4)
			|| ($formValue['FlagCorporation'] != 1 && $formValue['numFiscalBeginningYear'] < 1998)
		) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJsConfig'],
		));

		$arrColumn = array();
		$arrValue = array();

		$arrayCheck = array();
		$arrayCheck[] = array('idTarget' => 'FlagCorporation', 'tmplTarget' => 'flagCorporation',);
		if ($formValue['FlagCorporation'] != 1) {
			$formValue['NumFiscalBeginningMonth'] = 1;
		}
		$arrayCheck[] = array('idTarget' => 'NumFiscalBeginningMonth', 'tmplTarget' => 'numFiscalBeginningMonth',);
		$arrayCheck[] = array('idTarget' => 'FlagCR', 'tmplTarget' => 'flagCR',);

		$arrayCheck[] = array('idTarget' => 'FlagConsumptionTaxFree', 'tmplTarget' => 'flagConsumptionTaxFree',);
		if ($formValue['FlagConsumptionTaxFree']) {
			$formValue['FlagConsumptionTaxIncluding'] = 1;
			$arrayCheck[] = array('idTarget' => 'FlagConsumptionTaxIncluding', 'tmplTarget' => 'flagConsumptionTaxIncluding',);

		} else {
			$arrayCheck[] = array('idTarget' => 'FlagConsumptionTaxGeneralRule', 'tmplTarget' => 'flagConsumptionTaxGeneralRule',);
			if ($formValue['FlagConsumptionTaxGeneralRule']) {
				$arrayCheck[] = array('idTarget' => 'FlagConsumptionTaxDeducted', 'tmplTarget' => 'flagConsumptionTaxDeducted',);

			} else {
				$arrayCheck[] = array('idTarget' => 'FlagConsumptionTaxBusinessType', 'tmplTarget' => 'flagConsumptionTaxBusinessType',);
			}

			$arrayCheck[] = array('idTarget' => 'FlagConsumptionTaxIncluding', 'tmplTarget' => 'flagConsumptionTaxIncluding',);
			$arrayCheck[] = array('idTarget' => 'FlagConsumptionTaxCalc', 'tmplTarget' => 'flagConsumptionTaxIncluding',);
			if (!$formValue['FlagConsumptionTaxIncluding']) {
				$arrayCheck[] = array('idTarget' => 'FlagConsumptionTaxWithoutCalc', 'tmplTarget' => 'flagConsumptionTaxWithoutCalc',);
			}

		}

		foreach ($arrayCheck as $key => $value) {
			$id = $classEscape->toLower(array('str' => $value['idTarget']));
			$arrColumn[] = $id;
			$arrValue[] = $this->_checkDetailEditEntityConfigValue(array(
				'idTarget'   => $value['idTarget'],
				'tmplTarget' => $value['tmplTarget'],
				'formValue'  => $formValue,
				'vars'       => $vars
			));
		};
		$arrColumn[] = 'numFiscalBeginningYear';
		$arrValue[] = $formValue['numFiscalBeginningYear'];
		$arrColumn[] = 'stampFiscalBeginning';

		$strTimeZone = (-1 * PLUGIN_ACCOUNTING_NUM_TIME_ZONE) . 'hours';
		$numMonth = $formValue['NumFiscalBeginningMonth'];
		$numYear = $formValue['numFiscalBeginningYear'];
		$dateTime = new DateTime("$numYear-$numMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stamp = $dateTime->format('U');

		$arrValue[] = $stamp;

		$arrEntity = array(
			'arrColumn' => $arrColumn,
			'arrValue'  => $arrValue,
		);

		$arrColumn = array();
		$arrValue = array();
		$data = array();

		$numFiscalPeriod = $varsPluginAccountingEntity[$varsPluginAccountingAccount['idEntityCurrent']]['numFiscalPeriod'];
		$strNation = PLUGIN_ACCOUNTING_STR_NATION;
		$strLang = PLUGIN_ACCOUNTING_STR_LANG;
		$IdEntity = $varsPluginAccountingAccount['idEntityCurrent'];

		$varsFS = $this->_getVarsFSConfig(array(
			'idEntity'        => $IdEntity,
			'strNation'       => $strNation,
			'strLang'         => $strLang,
			'numFiscalPeriod' => $numFiscalPeriod,
			'flagCorporation' => $formValue['FlagCorporation'],
		));

		$arrColumn[] = 'jsonJgaapFSPL';
		if (!$formValue['FlagCR']) {
			$varsFS['jsonJgaapFSPL'] = $this->_removeTreeData(array(
				'vars'     => $varsFS['jsonJgaapFSPL'],
				'idTarget' => 'costOfSales',
				'strMatch' => '^(products|productsSum)$',
			));
		}
		$arrValue[] = json_encode($varsFS['jsonJgaapFSPL']);

		$arrColumn[] = 'jsonJgaapFSBS';
		$arrValue[] = json_encode($varsFS['jsonJgaapFSBS']);

		$arrColumn[] = 'jsonJgaapFSCR';
		$arrValue[] = ($formValue['FlagCR'])? json_encode($varsFS['jsonJgaapFSCR']) : '';

		if ($formValue['FlagCorporation'] != 1) {
			$arrColumn[] = 'jsonJgaapFSCS';
			$arrValue[] = '';
		}

		$arrColumn[] = 'jsonJgaapAccountTitlePL';
		if (!$formValue['FlagCR']) {
			$varsFS['jsonJgaapAccountTitlePL'] = $this->_removeTreeData(array(
				'vars'     => $varsFS['jsonJgaapAccountTitlePL'],
				'idTarget' => 'costOfSales',
				'strMatch' => '^(products|productsSum)$',
			));
		}
		$arrValue[] = json_encode($varsFS['jsonJgaapAccountTitlePL']);

		$arrColumn[] = 'jsonJgaapAccountTitleBS';
		$arrValue[] = json_encode($varsFS['jsonJgaapAccountTitleBS']);

		$arrColumn[] = 'jsonJgaapAccountTitleCR';
		$arrValue[] = ($formValue['FlagCR'])? json_encode($varsFS['jsonJgaapAccountTitleCR']) : '';

		$arrFs = array(
			'arrColumn' => $arrColumn,
			'arrValue'  => $arrValue,
		);
		$data = array(
			'flagFs' => 1,
			'entity' => $arrEntity,
			'fs'     => $arrFs,
		);

		return $data;
	}

	/**
		(array(
				'idEntity'        => $IdEntity,
				'strNation'       => $strNation,
				'strLang'         => $strLang,
				'numFiscalPeriod' => $numFiscalPeriod,
				'flagCorporation' => $formValue['FlagCorporation'],
		));
	 */
	protected function _getVarsFSConfig($arr)
	{
		global $varsPluginAccountingAccount;

		$varsFS = array();
		if ($arr['flagCorporation'] == 1) {
			$varsFS = $this->_getVarsFS(array(
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
				'idEntity'        => $arr['idEntity'],
			));

		} elseif ($arr['flagCorporation'] == 2) {
			$flagCorporation = 'public';
			$path = str_replace('<flagCorporation>', $flagCorporation, $this->_self['pathAccountTitlePL']);
			$path = str_replace('<numYearSheet>', PLUGIN_ACCOUNTING_NUM_YEAR_SHEET, $path);

			$varsFS['jsonJgaapAccountTitlePL'] = $this->_getVars(array(
				'path'      => $path,
				'strLang'   => $arr['strLang'],
				'strNation' => $arr['strNation'],
			));

			$path = str_replace('<flagCorporation>', $flagCorporation, $this->_self['pathAccountTitleBS']);
			$path = str_replace('<numYearSheet>', PLUGIN_ACCOUNTING_NUM_YEAR_SHEET, $path);
			$varsFS['jsonJgaapAccountTitleBS'] = $this->_getVars(array(
				'path'      => $path,
				'strLang'   => $arr['strLang'],
				'strNation' => $arr['strNation'],
			));
			$path = str_replace('<flagCorporation>', $flagCorporation, $this->_self['pathAccountTitleCR']);
			$path = str_replace('<numYearSheet>', PLUGIN_ACCOUNTING_NUM_YEAR_SHEET, $path);
			$varsFS['jsonJgaapAccountTitleCR'] = $this->_getVars(array(
				'path'      => $path,
				'strLang'   => $arr['strLang'],
				'strNation' => $arr['strNation'],
			));
			$path = str_replace('<flagCorporation>', $flagCorporation, $this->_self['pathFSPL']);
			$path = str_replace('<numYearSheet>', PLUGIN_ACCOUNTING_NUM_YEAR_SHEET, $path);
			$varsFS['jsonJgaapFSPL'] = $this->_getVars(array(
				'path'      => $path,
				'strLang'   => $arr['strLang'],
				'strNation' => $arr['strNation'],
			));
			$path = str_replace('<flagCorporation>', $flagCorporation, $this->_self['pathFSBS']);
			$path = str_replace('<numYearSheet>', PLUGIN_ACCOUNTING_NUM_YEAR_SHEET, $path);
			$varsFS['jsonJgaapFSBS'] = $this->_getVars(array(
				'path'      => $path,
				'strLang'   => $arr['strLang'],
				'strNation' => $arr['strNation'],
			));
			$path = str_replace('<flagCorporation>', $flagCorporation, $this->_self['pathFSCR']);
			$path = str_replace('<numYearSheet>', PLUGIN_ACCOUNTING_NUM_YEAR_SHEET, $path);
			$varsFS['jsonJgaapFSCR'] = $this->_getVars(array(
				'path'      => $path,
				'strLang'   => $arr['strLang'],
				'strNation' => $arr['strNation'],
			));
		}

		return $varsFS;
	}

	/**
		$this->_removeVarsTree(array(
			'vars'       => array(),
			'idTarget'   => array(),
		));
	 */
	protected function _removeVarsTreeFS($arr)
	{
		$array = &$arr['vars'];
		$flag = 0;
		foreach ($array as $key => $value) {
			$idTarget = $arr['idTarget'];
			if ($idTarget == $value['vars']['idTarget']) {
				$flag = 1;
			}

			if ($value['child']) {
				$arrayNew[$key]['child'] = $this->_removeVarsTreeFS(array(
					'vars'       => $array[$key]['child'],
					'idTarget'   => $arr['idTarget'],
				));

			}
		}
		$arrayNew = array();
		if ($flag) {
			foreach ($array as $key => $value) {
				if ($idTarget != $value['vars']['idTarget']) {
					$arrayNew[] = $value;
				}
			}
			return $arrayNew;

		}

		return $array;
	}

	/**
	 *
	 */
	protected function _checkDetailEditEntityConfigValue($arr)
	{
		$array = &$arr['vars'][0]['varsTmpl'][$arr['tmplTarget']]['varsDetail'];

		foreach ($array as $key => $value) {
			if ($value['id'] == $arr['idTarget']) {
				$arrayOption = $value['arrayOption'];
				if ($value['id'] == 'NumFiscalBeginningMonth') {
					$arrayOption = $value['varsTmpl'];

				} else {
					$arrayOption = $value['arrayOption'];
				}
				$flag = 0;
				$data = 0;

				foreach ($arrayOption as $keyOption => $valueOption) {

					if ($arr['formValue'][$value['id']] === $valueOption['value']) {
						$flag = 1;
						$data = $valueOption['value'];
						break;
					}
				}
				if (!$flag) {
					if (FLAG_TEST) {
						var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
					}
					exit;
				}
				return $data;
			}

		}
	}

	/**
	 *
	 */
	protected function _updateDbEntityConfigEnd($arr)
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;
		global $classPluginAccountingInit;

		if (!$varsPluginAccountingAccount['idEntityCurrent']) {
			$this->_sendOld();
		}

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);
		$strNation = ucwords($strNation);

		global $classDb;
		global $classInit;

		$flagConfig = 0;
		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingEntity',
			'arrColumn' => array('flagConfig'),
			'arrWhere'  => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'id',
					'flagCondition' => 'eq',
					'value'         => $varsPluginAccountingAccount['idEntityCurrent'],
				),
			),
			'arrValue'  => array($flagConfig),
		));

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingEntity' . $strNation,
			'arrColumn' => $arr['entity']['arrColumn'],
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
			),
			'arrValue'  => $arr['entity']['arrValue'],
		));

		if ($arr['flagFs']) {
			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingFS' . $strNation,
				'arrColumn' => $arr['fs']['arrColumn'],
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
				),
				'arrValue'  => $arr['fs']['arrValue'],
			));
			$this->_updateDbPreferenceStamp(array('strColumn' => 'fS'));
		}

		$this->_updateDbPreferenceStamp(array('strColumn' => 'entity'));
		$classPluginAccountingInit->updateInitEntity();

	}

	/**
	 *
	 */
	protected function _updateDb($arr)
	{
		global $varsRequest;

		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];
		$method = '_updateDb' . ucwords($idTarget);
		if (method_exists($this, $method)) {
			$this->$method($arr);
		}
	}

	/**
	 * $arr = array(
	 *     'arrColumn' => array(),
	 *     'arrValue'  => array(),
	 * )
	 */
	protected function _updateDbPreference($arr)
	{
		global $varsPluginAccountingPreference;
		global $classPluginAccountingInit;

		global $classDb;
		global $classInit;

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingPreference',
			'arrColumn' => $arr['arrColumn'],
			'arrWhere'  => array(),
			'arrValue'  => $arr['arrValue'],
		));

		$classPluginAccountingInit->updateInitPreference();

	}

	/**
	 *
	 */
	protected function _updateDbFlagIdAccountTitle($arr)
	{
		$arrColumn = array();
		$arrValue = array();

		$array = $arr['arr'];
		foreach ($array as $key => $value) {
			if (preg_match("/^dummy/", $key)) {
				continue;
			}
			$arrColumn[] = $key;
			$arrValue[] = $value;
		}

		$this->_updateDbPreference(array(
			'arrValue'  => $arrValue,
			'arrColumn' => $arrColumn,
		));
	}

	/**
	 *
	 */
	protected function _updateDbAccessCode($arr)
	{
		$arrColumn = array();
		$arrValue = array();

		$array = $arr['arr'];
		foreach ($array as $key => $value) {
			if (preg_match("/^dummy/", $key)) {
				continue;
			}
			$arrColumn[] = $key;
			$arrValue[] = $value;
		}

		$this->_updateDbPreference(array(
			'arrValue'  => $arrValue,
			'arrColumn' => $arrColumn,
		));
	}

	/**
	 * $arr = array(
	 *     'arrColumn' => array(),
	 *     'arrValue'  => array(),
	 * )
	 */
	protected function _updateDbAccount($arr)
	{
		global $classDb;
		global $classPluginAccountingInit;
		global $varsAccount;
		global $classInit;

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingAccount',
			'arrColumn' => $arr['arrColumn'],
			'arrWhere'  => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'id',
					'flagCondition' => 'eq',
					'value'         => $varsAccount['id'],
				),
			),
			'arrValue'  => $arr['arrValue'],
		));

		$this->_updateDbPreferenceStamp(array('strColumn' => 'account'));
		$classPluginAccountingInit->updateInitAccounts();
		$classPluginAccountingInit->updateInitAccount();
	}

	/**
	 *
	 */
	protected function _updateDbIdEntityCurrent($arr)
	{
		global $varsPluginAccountingEntity;
		global $varsRequest;

		if (!$varsPluginAccountingEntity[$arr['arr']['idEntityCurrent']]) {
			$this->sendVars(array(
				'flag'    => 'idEntity',
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}

		$numFiscalPeriodCurrent = (int) $varsRequest['query']['jsonValue']['vars']['NumFiscalPeriodCurrent'];
		if ($numFiscalPeriodCurrent < $varsPluginAccountingEntity[$arr['arr']['idEntityCurrent']]['numFiscalPeriodStart']
			||  $numFiscalPeriodCurrent > $varsPluginAccountingEntity[$arr['arr']['idEntityCurrent']]['numFiscalPeriod']
		) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}
		$arr['arrColumn'][] = 'numFiscalPeriodCurrent';
		$arr['arrValue'][] = $numFiscalPeriodCurrent;

		$this->_updateDbAccount($arr);
	}

	/**
	 *
	 */
	protected function _updateDbFlagConsumptionTaxFree($arr)
	{
		global $varsPluginAccountingAccount;

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent']
		));

		if ($arr['arr']['flagConsumptionTaxFree']) {
			$arr['arr']['flagConsumptionTaxIncluding'] = 1;
			$arr['arrColumn'][] = 'flagConsumptionTaxIncluding';
			$arr['arrValue'][] = 1;
		}

		$arr['flagType'] = 'flagConsumptionTaxFree';

 		$this->_updateDbConsumption($arr);
	}

	/**
	 *
	 */
	protected function _updateDbFlagConsumptionTaxGeneralRule($arr)
	{
		global $varsPluginAccountingAccount;

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent']
		));

		if ($varsEntityNation['flagConsumptionTaxFree']) {
			$this->sendVars(array(
				'flag'    => 8,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => '',
			));
		}

		$arr['flagType'] = 'flagConsumptionTaxGeneralRule';

 		$this->_updateDbConsumption($arr);
	}

	/**
	 *
	 */
	protected function _updateDbFlagConsumptionTaxBusinessType($arr)
	{
		global $varsPluginAccountingAccount;

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent']
		));

		if ($varsEntityNation['flagConsumptionTaxFree']
			|| $varsEntityNation['flagConsumptionTaxGeneralRule']
		) {
			$this->sendVars(array(
				'flag'    => 8,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => '',
			));
		}

		$arr['flagType'] = 'flagConsumptionTaxBusinessType';

 		$this->_updateDbConsumption($arr);
	}

	/**
	 *
	 */
	protected function _updateDbFlagConsumptionTaxIncluding($arr)
	{
		global $varsPluginAccountingAccount;

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent']
		));

		if ($varsEntityNation['flagConsumptionTaxFree']) {
			$this->sendVars(array(
				'flag'    => 8,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => '',
			));
		}

		$arr['flagType'] = 'flagConsumptionTaxIncluding';

 		$this->_updateDbConsumption($arr);
	}


	/**
	 *
	 */
	protected function _updateDbFlagConsumptionTaxDeducted($arr)
	{
		global $varsPluginAccountingAccount;

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent']
		));

		if ($varsEntityNation['flagConsumptionTaxFree']
			|| !$varsEntityNation['flagConsumptionTaxGeneralRule']
		) {
			$this->sendVars(array(
				'flag'    => 8,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => '',
			));
		}

		$arr['flagType'] = 'flagConsumptionTaxDeducted';

 		$this->_updateDbConsumption($arr);
	}

	/**
	 *
	 */
	protected function _updateDbFlagConsumptionTaxWithoutCalc($arr)
	{
		global $varsPluginAccountingAccount;

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent']
		));

		if ($varsEntityNation['flagConsumptionTaxFree']
			|| $varsEntityNation['flagConsumptionTaxIncluding']
		) {
			$this->sendVars(array(
				'flag'    => 8,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => '',
			));
		}

		$arr['flagType'] = 'flagConsumptionTaxWithoutCalc';

 		$this->_updateDbConsumption($arr);
	}

	/**
		(array(

		))
	 */
	protected function _updateDbConsumption($arr)
	{
		global $classDb;

		global $varsRequest;
		global $varsAccount;
		global $varsPluginAccountingAccount;
		global $classPluginAccountingInit;
		global $varsPluginAccountingEntity;

		if (!$this->_checkCurrent()) {
			$this->_sendOld();
		}

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriodTempNext = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];

		$classCalcLogConsumptionTax = $this->_getClassCalc(array('flagType' => 'LogConsumptionTax'));

		$flagErrorVars = $classCalcLogConsumptionTax->allot(array(
			'flagStatus'              => 'update',
			'flagType'                => $arr['flagType'],
			'idEntity'                => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagTempPrev'            => (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow))? 1 : 0,
			'numFiscalPeriodTempNext' => $numFiscalPeriodTempNext,
			'varsValue'               => $arr,
		));
		if ($flagErrorVars) {
			if ($flagErrorVars == 'errorDataMax') {
				$this->sendVars(array(
					'flag'    => 'errorDataMax',
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));

			} elseif ($flagErrorVars['flag'] == 'textMaxOver') {
				$varsItem = $this->getVars(array(
					'path' => $this->_extSelf['pathVarsItem'],
				));

				$strLog = '';
				$array = $flagErrorVars['arrIdLog'];
				if ($array) {
					$arrayNew = array();
					foreach ($array as $key => $value) {
						$arrayNew[] = '( ' . $value . ' )';
					}
					$str = join('  ', $arrayNew);
					$strLog = str_replace('<%replace%>', $str, $varsItem['textMaxOver']);
				}

				$arrayStr = array('Cash', 'CashDefer', 'Import', 'House');
				foreach ($arrayStr as $keyStr => $valueStr) {
					$array = $flagErrorVars['arrIdLog' . $valueStr];
					if ($array) {
						$arrayNew = array();
						foreach ($array as $key => $value) {
							$arrayNew[] = '( ' . $value . ' )';
						}
						$str = join('  ', $arrayNew);
						$strData = str_replace('<%replace%>', $str, $varsItem['textMaxOver' . $valueStr]);
					}
					if ($strLog) {
						$strLog .= '<br>' . $strData;
					}
				}

				$this->sendVars(array(
					'flag'    => $flagErrorVars['flag'],
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(
						'idTarget' => $flagErrorVars['flag'],
						'vars'     => $strLog,
					),
				));

			}
		}

	}

	/**
	 *
	 */
	protected function _updateDbCharge($arr)
	{
		global $varsAccounts;
		global $classEscape;
		global $classPluginAccountingInit;

		global $varsPluginAccountingAccounts;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccountsEntity;

		if (!$this->_checkCurrent()) {
			$this->_sendOld();
		}

		$strDir = $this->_self['strTitle'];
		$strFile = ucwords($this->_self['strTitle']);
		require_once(PATH_BACK_CLASS_ELSE_PLUGIN . $strDir . '/' . $strFile . '.php');
		$classCall = new Code_Else_Plugin_Accounting_Accounting;

		$idAccountNow = $arr['arr']['charge']['ChargeNow'];
		$idAccountNext = $arr['arr']['charge']['ChargeNext'];
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$strEntityCurrent = ',' . $idEntity . ',';
		$arrCommaIdEntityNow = $varsPluginAccountingAccounts[$idAccountNow]['arrCommaIdEntity'];
		$arrCommaIdEntityNext = $varsPluginAccountingAccounts[$idAccountNext]['arrCommaIdEntity'];
		$flagAdminNow = $this->checkModuleAdmin(array(
			'idAccount' => $idAccountNow,
			'strModule' => $this->_self['strTitle'],
		));
		$flagAdminNext = $this->checkModuleAdmin(array(
			'idAccount' => $idAccountNext,
			'strModule' => $this->_self['strTitle'],
		));

		if ((!$varsAccounts[$idAccountNow] || !$varsAccounts[$idAccountNext])) {
			$this->_sendOld();
		}
		if (!$flagAdminNext && !preg_match( "/$strEntityCurrent/", $arrCommaIdEntityNext)) {
			$this->_sendOld();
		}
		if (!$flagAdminNow && !preg_match( "/$strEntityCurrent/", $arrCommaIdEntityNow)) {
			$this->_sendOld();
		}
		$classCall->loop(array(
			'flagType'        => 'accountStatus',
			'flagStatus'      => 'updateChargeEntity',
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idAccountNow'    => $idAccountNow,
			'idAccountNext'   => $idAccountNext,
			'idEntity'        => $idEntity,
			'stampRegister'   => TIMESTAMP,
		));

		$this->_updateDbChargeLogFixedAssets(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idAccountNow'    => $idAccountNow,
			'idAccountNext'   => $idAccountNext,
			'idEntity'        => $idEntity,
			'stampRegister'   => TIMESTAMP,
		));

		$numFiscalPeriodTemp = 0;
		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
			$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] + 1;
		}
		$this->_updateDbChargeLogCash(array(
			'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
			'idAccountNow'        => $idAccountNow,
			'idAccountNext'       => $idAccountNext,
			'idEntity'            => $idEntity,
			'stampRegister'       => TIMESTAMP,
		));
	}

	/**
		(array(
			'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
			'idAccountNow'        => $idAccountNow,
			'idAccountNext'       => $idAccountNext,
			'idEntity'            => $idEntity,
			'stampRegister'       => TIMESTAMP,
		))
	 */
	protected function _updateDbChargeLogCash($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogCash',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idEntity',
					'flagCondition' => 'eq',
					'value'         => $arr['idEntity'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'numFiscalPeriod',
					'flagCondition' => 'eq',
					'value'         => $arr['numFiscalPeriod'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idAccount',
					'flagCondition' => 'eq',
					'value'         => $arr['idAccountNow'],
				),
			),
		));
		$classCalcCash = $this->_getClassCalc(array('flagType' => 'Cash'));

		$array = &$rows['arrRows'];
		foreach ($array as $key => $value) {
			$data = array(
				'stampRegister' => $arr['stampRegister'],
				'idAccount'     => $arr['idAccountNext'],
			);

			$value['jsonChargeHistory'][] = $data;
			$jsonChargeHistory = json_encode($value['jsonChargeHistory']);
			$this->checkTextSize(array(
				'flag' => 'errorDataMax',
				'str'  => $jsonChargeHistory,
			));
			$idAccount = $arr['idAccountNext'];

			$arrColumn = array('idAccount', 'jsonChargeHistory');
			$arrValue = array($idAccount, $jsonChargeHistory);
			if ($value['flagApply']) {
				$idAccountApply = $idAccount;

				$varsPermitHistory = array();
				$dataPermitHistory = end($value['jsonPermitHistory']);
				$dataPermitHistory['idAccountApply'] = $idAccountApply;
				$varsPermitHistory[] = $dataPermitHistory;
				$jsonPermitHistory = json_encode($varsPermitHistory);

				$arrVersion = $value['jsonVersion'];
				$varsVersionEnd = end($arrVersion);
				$varsVersionEnd['stampRegister'] = TIMESTAMP;
				$varsVersionEnd['stampUpdate'] = TIMESTAMP;
				$varsVersionEnd['jsonPermitHistory'] = $varsPermitHistory;
				$arrVersion[] = $varsVersionEnd;
				$jsonVersion = json_encode($arrVersion);
				$this->checkTextSize(array(
					'flag' => 'errorDataMax',
					'str'  => $jsonVersion,
				));

				$arrColumn = array('idAccount', 'jsonChargeHistory', 'idAccountApply', 'jsonVersion', 'jsonPermitHistory');
				$arrValue = array($idAccount, $jsonChargeHistory, $idAccountApply, $jsonVersion, $jsonPermitHistory);
			}

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingLogCash',
				'arrColumn' => $arrColumn,
				'flagAnd'  => 1,
				'arrWhere' => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idEntity',
						'flagCondition' => 'eq',
						'value'         => $arr['idEntity'],
					),
					array(
						'flagType'      => 'num',
						'strColumn'     => 'numFiscalPeriod',
						'flagCondition' => 'eqBig',
						'value'         => $arr['numFiscalPeriod'],
					),
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idLogCash',
						'flagCondition' => 'eq',
						'value'         => $value['idLogCash'],
					),
				),
				'arrValue'  => $arrValue,
			));

			if ($arr['numFiscalPeriodTemp']) {
				$varsLogCash = $this->_getVarsLogCash(array(
					'idTarget'        => $value['idLogCash'],
					'numFiscalPeriod' => $arr['numFiscalPeriodTemp'],
					'idEntity'        => $arr['idEntity'],
				));
				$flagErrorVars = $classCalcCash->allot(array(
					'flagStatus'       => 'UpdateVarsTax',
					'arrRows'          => array($varsLogCash),
					'numFiscalPeriod'  => $arr['numFiscalPeriodTemp'],
					'idEntity'         => $arr['idEntity'],
				));

				if ($flagErrorVars['flag'] == 'textMaxOver') {
					$this->sendVars(array(
						'flag'    => 'errorDataMax',
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(),
					));
				}
			}
		}
		$array = array('cash');
		foreach ($array as $key => $value) {
			$this->_updateDbPreferenceStamp(array('strColumn' => $value));
		}

	}

	/**
		(array(
			'idTarget' => '',
		))
	 */
	protected function _getVarsLogCash($arr)
	{
		global $classDb;

		$arrWhere = array(
			array(
				'flagType'      => 'num',
				'strColumn'     => 'idEntity',
				'flagCondition' => 'eq',
				'value'         => $arr['idEntity'],
			),
			array(
				'flagType'      => 'num',
				'strColumn'     => 'numFiscalPeriod',
				'flagCondition' => 'eq',
				'value'         => $arr['numFiscalPeriod'],
			),
			array(
				'flagType'      => 'num',
				'strColumn'     => 'idLogCash',
				'flagCondition' => 'eq',
				'value'         => $arr['idTarget'],
			),
		);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogCash',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => $arrWhere,
		));

		if ($rows['numRows']) {
			return $rows['arrRows'][0];
		}

		return array();
	}

	/**
		(array(
			'idEntity'         => $arr['idEntity'],
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'idAccountTarget'  => $arr['idAccountNow'],
			'idAccountCharge'  => $arr['idAccountNext'],
			'stampRegister'    => $arr['stampRegister'],
		))
	 */
	protected function _updateDbChargeLogFixedAssets($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogFixedAssets' . $strNation,
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idEntity',
					'flagCondition' => 'eq',
					'value'         => $arr['idEntity'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'numFiscalPeriod',
					'flagCondition' => 'eq',
					'value'         => $arr['numFiscalPeriod'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idAccount',
					'flagCondition' => 'eq',
					'value'         => $arr['idAccountNow'],
				),
			),
		));

		$array = &$rows['arrRows'];
		foreach ($array as $key => $value) {
			$data = array(
				'stampRegister' => $arr['stampRegister'],
				'idAccount'     => $arr['idAccountNext'],
			);

			$value['jsonChargeHistory'][] = $data;
			$jsonChargeHistory = json_encode($value['jsonChargeHistory']);
			$this->checkTextSize(array(
				'flag' => 'errorDataMax',
				'str'  => $jsonChargeHistory,
			));
			$idAccount = $arr['idAccountNext'];

			$arrColumn = array('idAccount', 'jsonChargeHistory');
			$arrValue = array($idAccount, $jsonChargeHistory);

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingLogFixedAssets' . $strNation,
				'arrColumn' => $arrColumn,
				'flagAnd'  => 1,
				'arrWhere' => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idEntity',
						'flagCondition' => 'eq',
						'value'         => $arr['idEntity'],
					),
					array(
						'flagType'      => 'num',
						'strColumn'     => 'numFiscalPeriod',
						'flagCondition' => 'eqBig',
						'value'         => $arr['numFiscalPeriod'],
					),
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idFixedAssets',
						'flagCondition' => 'eq',
						'value'         => $value['idFixedAssets'],
					),
				),
				'arrValue'  => $arrValue,
			));
		}

		$array = array('logFixedAssets');
		foreach ($array as $key => $value) {
			$this->_updateDbPreferenceStamp(array('strColumn' => $value));
		}
	}

	/**
		(array(

		))
	 */
	protected function _updateDbNextData($arr)
	{
		$classCall = $this->_getClass(array('flagType' => 'NextData'));
		$classCall->allot(array(
			'flagStatus' => 'calc',
		));
		global $varsPluginAccountingAccount;

		$numFiscalPeriodCurrent = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$numFiscalPeriodCurrent++;

		$arrColumn = array('numFiscalPeriodCurrent');
		$arrValue = array($numFiscalPeriodCurrent);

		$this->_updateDbAccount(array(
			'arrColumn' => $arrColumn,
			'arrValue' => $arrValue,
		));
	}

	/**
		(array(

		))
	 */
	protected function _updateDbFlagDepWrite($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;
		global $classPluginAccountingInit;
		global $varsPluginAccountingEntity;

		if (!$this->_checkCurrent()) {
			$this->_sendOld();
		}

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable' => 'accountingFixedAssets' . $strNation,
			'arrColumn' => $arr['arrColumn'],
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
			),
			'arrValue'  => $arr['arrValue'],
		));

		$this->_updateDbPreferenceStamp(array('strColumn' => 'fixedAssets'));
	}

	/**
		(array(
			'flagType' => 'LogCalc'
		))
	 */
	protected function _getClass($arr)
	{
		$strNation = PLUGIN_ACCOUNTING_STR_NATION;

		$strFile = ucwords($arr['flagType']);
		require_once(PATH_BACK_CLASS_ELSE_PLUGIN . $this->_self['strTitle'] . '/' . $strNation . '/portal/' . $strFile .'.php');
		$strNation = ucwords($strNation);
		$flag = 0;
		if (PLUGIN_ACCOUNTING_FLAG_CORPORATION) {
			$path = PATH_BACK_CLASS_ELSE_PLUGIN . $this->_self['strTitle'] . '/' . $strNation . '/' . PLUGIN_ACCOUNTING_NUM_YEAR_SHEET . '/' . PLUGIN_ACCOUNTING_FLAG_CORPORATION  . '/portal/' . $strFile .'.php';
			if (!file_exists($path)) {
				$flag = 1;

			} else {
				require_once($path);
				$strClass = 'Code_Else_Plugin_Accounting_' . ucwords($strNation) . '_Portal_' . $strFile .'_' . PLUGIN_ACCOUNTING_NUM_YEAR_SHEET .'_' . ucwords(PLUGIN_ACCOUNTING_FLAG_CORPORATION);
			}
		} else {
			$flag = 1;
		}

		if ($flag) {
			$strClass = 'Code_Else_Plugin_Accounting_' . ucwords($strNation) .'_Portal_' . $strFile;
		}

		$classCall = new $strClass();

		return $classCall;
	}


	/**
		(array(
			'vars'            => $value
			'arrAccountTitle' => $arrAccountTitle
		))
	 */
	protected function _checkJsonAccountTitle($arr)
	{
		$arrayNew = array();
		$array = $arr['vars'];

		foreach ($array as $key => $value) {
			if ($arr['arrAccountTitle']['arrStrTitle'][$key]) {
				$arrayNew[$key] = $value;
			}
		}

		$jsonAccountTitle = $arrayNew;
		if (!$jsonAccountTitle) {
			$jsonAccountTitle = array();
		}

		$jsonAccountTitle = json_encode($jsonAccountTitle);

		return $jsonAccountTitle;
	}


}
