<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_Preference extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'pathTplJs' => 'else/plugin/accounting/js/jpn/preference.js',
		'pathVarsJs'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/preference.php',
		'pathVarsJsConfig'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/preferenceConfig.php',
		'pathVarsItem'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/preferenceItem.php',
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
		global $classSmarty;

		$vars = $this->_getVarsJs();

		$json = json_encode($vars);
		$classSmarty->assign('varsLoad', $json);

		$classSmarty->assign('numNews', $this->getNumNews());
		$contents = $classSmarty->fetch($this->_extSelf['pathTplJs']);

		$this->sendJs(array(
			'data' => $contents,
		));

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

		$vars = $this->getVars(array(
			'path' => $this->_self['pathVarsJs'],
		));

		if (!$varsPluginAccountingEntity) {
			if ($this->_getVarsFlagAdmin()) {
				$array = array('IdEntityCurrent', 'EntityCurrent');
				foreach ($array as $key => $value) {
					$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_removeVarsTree(array(
						'vars'       => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
						'idTarget'   => $value,
					));
				}

			} else {
				$array = array('Admin', 'IdEntityCurrent', 'EntityCurrent');
				foreach ($array as $key => $value) {
					$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_removeVarsTree(array(
						'vars'       => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
						'idTarget'   => $value,
					));
				}
			}

		} else {
			$id = $varsPluginAccountingAccount['idEntityCurrent'];
			if ($id) {
				if ($varsPluginAccountingEntity[$id]['flagConfig']) {
					if ($this->_getVarsFlagAdmin()) {
						$varsEntityConfig = $this->getVars(array(
							'path' => $this->_extSelf['pathVarsJsConfig'],
						));
						$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_insertVarsTree(array(
							'vars'       => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
							'idTarget'   => 'EntityCurrent',
							'varsTarget' => $varsEntityConfig,
						));
					}

				} else {
					$varsEntity = $this->getVars(array(
						'path' => $this->_extSelf['pathVarsJs'],
					));
					if (!$this->_getVarsFlagAdmin()) {
						$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_removeVarsTree(array(
							'vars'       => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
							'idTarget'   => 'Admin',
						));
						$varsEntity = $this->_removeVarsTree(array(
							'vars'       => $varsEntity,
							'idTarget'   => 'AdminEntityCurrent',
						));
					}
					$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_insertVarsTree(array(
						'vars'       => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
						'idTarget'   => 'EntityCurrent',
						'varsTarget' => $varsEntity,
					));

					if (!$this->_getVarsFlagAdmin()) {
						$array = array('Admin', 'AdminEntityCurrent');
						foreach ($array as $key => $value) {
							$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_removeVarsTree(array(
								'vars'       => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
								'idTarget'   => $value,
							));
						}
					}
				}

			} else {
				if ($this->_getVarsFlagAdmin()) {
					$array = array('EntityCurrent');
					foreach ($array as $key => $value) {
						$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_removeVarsTree(array(
							'vars'       => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
							'idTarget'   => $value,
						));
					}

				} else {
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
						'strMatch' => '^(consumptionTaxSheetWindow)$',
					));
					$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_removeTreeData(array(
						'vars'     => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
						'idTarget' => 'details',
						'strMatch' => '^(consumptionTaxWindow)$',
					));
				}
				if ($varsPluginAccountingAccount['numFiscalPeriodCurrent'] != $varsPluginAccountingEntity[$idEntity]['numFiscalPeriodStart']) {
					$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_removeTreeData(array(
						'vars'     => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
						'idTarget' => 'user',
						'strMatch' => '^(balanceData)$',
					));
				}
			}
		}

		if (!$this->_getVarsFlagAdmin()) {
			$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_updateVarsAccess(
				$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail']
			);

		}


		$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_updateVars(
			$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail']
		);

		return $vars;
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

		$method = '_updateVars' . ucwords($varsTarget['vars']['idTarget']);

		if (method_exists($this, $method)) {
			$varsTarget = $this->$method(array('vars' => $varsTarget));
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

		$flagCurrent = $this->_getCurrentFlagNow();

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
	protected function _updateVarsJsonFileType($arr)
	{
		return $this->_updateVarsPreferenceJson(array('vars' => $arr['vars']));
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

			} elseif ($idTarget != 'jsonFileType') {
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

		if (($formValue['FlagCorporation'] && $formValue['numFiscalBeginningYear'] < 1997)
			|| ($formValue['FlagCorporation'] && $formValue['numFiscalBeginningYear'] == 1997 && $formValue['NumFiscalBeginningMonth'] < 4)
			|| (!$formValue['FlagCorporation'] && $formValue['numFiscalBeginningYear'] < 1998)
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
		if (!$formValue['FlagCorporation']) {
			$formValue['NumFiscalBeginningMonth'] = 1;
		}
		$arrayCheck[] = array('idTarget' => 'NumFiscalBeginningMonth', 'tmplTarget' => 'numFiscalBeginningMonth',);
		$arrayCheck[] = array('idTarget' => 'FlagCR', 'tmplTarget' => 'flagCR',);

		if ($formValue['IdEntityAccount'] > 0 && !$varsPluginAccountingEntity[$formValue['IdEntityAccount']]) {
			$this->sendVars(array(
				'flag'    => 'entityConfigEnd',
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(
					'idTarget' => 'entityConfigEnd',
				),
			));
		}

		if ($varsPluginAccountingAccount['idEntityCurrent'] == $formValue['IdEntityAccount']) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}

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

		if (!$formValue['IdEntityAccount'] && $formValue['FlagCR']) {
			$data = array(
				'flagFs' => 0,
				'entity' => $arrEntity,
				'fs'     => array(),
			);

		} else {
			if ($varsPluginAccountingEntity[$formValue['IdEntityAccount']]) {
				$numFiscalPeriod = $varsPluginAccountingEntity[$formValue['IdEntityAccount']]['numFiscalPeriod'];
				$strNation = $varsPluginAccountingEntity[$formValue['IdEntityAccount']]['strNation'];
				$IdEntity = $formValue['IdEntityAccount'];

			} else {
				$numFiscalPeriod = $varsPluginAccountingEntity[$varsPluginAccountingAccount['idEntityCurrent']]['numFiscalPeriod'];
				$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);
				$IdEntity = $varsPluginAccountingAccount['idEntityCurrent'];

			}
			$rows = $classDb->getSelect(array(
				'idModule' => 'accounting',
				'strTable' => 'accountingFS' . $strNation,
				'arrLimit' => array(),
				'arrOrder' => array(),
				'flagAnd'  => 1,
				'arrWhere' => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idEntity',
						'flagCondition' => 'eq',
						'value'         => $IdEntity,
					),
					array(
						'flagType'      => 'num',
						'strColumn'     => 'numFiscalPeriod',
						'flagCondition' => 'eq',
						'value'         => $numFiscalPeriod,
					),
				),
			));

			$arrColumn[] = 'jsonJgaapFSPL';
			if (!$formValue['FlagCR']) {
				$rows['arrRows'][0]['jsonJgaapFSPL'] = $this->_removeTreeData(array(
					'vars'     => $rows['arrRows'][0]['jsonJgaapFSPL'],
					'idTarget' => 'costOfSales',
					'strMatch' => '^(products|productsSum)$',
				));
			}
			$arrValue[] = json_encode($rows['arrRows'][0]['jsonJgaapFSPL']);

			$arrColumn[] = 'jsonJgaapFSBS';
			$arrValue[] = json_encode($rows['arrRows'][0]['jsonJgaapFSBS']);

			$arrColumn[] = 'jsonJgaapFSCR';
			$arrValue[] = ($formValue['FlagCR'])? json_encode($rows['arrRows'][0]['jsonJgaapFSCR']) : '';

			$arrColumn[] = 'jsonJgaapAccountTitlePL';
			if (!$formValue['FlagCR']) {
				$rows['arrRows'][0]['jsonJgaapAccountTitlePL'] = $this->_removeTreeData(array(
					'vars'     => $rows['arrRows'][0]['jsonJgaapAccountTitlePL'],
					'idTarget' => 'costOfSales',
					'strMatch' => '^(products|productsSum)$',
				));
			}
			$arrValue[] = json_encode($rows['arrRows'][0]['jsonJgaapAccountTitlePL']);

			$arrColumn[] = 'jsonJgaapAccountTitleBS';
			$arrValue[] = json_encode($rows['arrRows'][0]['jsonJgaapAccountTitleBS']);

			$arrColumn[] = 'jsonJgaapAccountTitleCR';
			$arrValue[] = ($formValue['FlagCR'])? json_encode($rows['arrRows'][0]['jsonJgaapAccountTitleCR']) : '';

			$arrFs = array(
				'arrColumn' => $arrColumn,
				'arrValue'  => $arrValue,
			);
			$data = array(
				'flagFs' => 1,
				'entity' => $arrEntity,
				'fs'     => $arrFs,
			);
		}

		return $data;
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
			if (preg_match("/^($idTarget)$/", $value['vars']['idTarget'])) {
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
				if (!preg_match("/^($idTarget)$/", $value['vars']['idTarget'])) {
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
	protected function _updateDbJsonFileType($arr)
	{
		$this->_updateDbPreference($arr);
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

		$flagCurrentFlagNow = $this->_getCurrentFlagNow();
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriodTempNext = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];

		$classCalcLogConsumptionTax = $this->_getClassCalc(array('flagType' => 'LogConsumptionTax'));
		$flagErrorVars = $classCalcLogConsumptionTax->allot(array(
			'flagStatus'              => 'update',
			'flagType'                => $arr['flagType'],
			'numFiscalPeriod'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagTempPrev'            => (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow))? 1 : 0,
			'numFiscalPeriodTempNext' => $numFiscalPeriodTempNext,
			'varsValue'               => $arr,
		));
		if ($flagErrorVars) {
			if ($flagErrorVars['flag'] == 'textMaxOver') {
				$varsItem = $this->getVars(array(
					'path' => $this->_extSelf['pathVarsItem'],
				));
				$arrayNew = array();
				$array = $flagErrorVars['arrIdLog'];
				foreach ($array as $key => $value) {
					$arrayNew[] = '( ' . $value . ' )';
				}
				$str = join('  ', $arrayNew);
				$varsItem['textMaxOver'] = str_replace('<%replace%>', $str, $varsItem['textMaxOver']);
				$this->sendVars(array(
					'flag'    => $flagErrorVars['flag'],
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(
						'idTarget' => $flagErrorVars['flag'],
						'vars'     => $varsItem['textMaxOver'],
					),
				));

			} elseif ($flagErrorVars['flag'] == 'errorDataMax') {
				$this->sendVars(array(
					'flag'    => $flagErrorVars['flag'],
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
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
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$strNation = PLUGIN_ACCOUNTING_STR_NATION;

		$strFile = ucwords($arr['flagType']);
		require_once(PATH_BACK_CLASS_ELSE_PLUGIN . $this->_self['strTitle'] . '/' . $strNation . '/preference/' . $strFile .'.php');
		$strNation = ucwords($strNation);
		$strClass = 'Code_Else_Plugin_Accounting_' . $strNation .'_Preference_' . $strFile;
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
