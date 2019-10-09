<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_AccountTitle extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference' => 'accountTitleWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/accountTitle.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/accountTitle.php',
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

		$this->_checkEntity();

		if ($varsRequest['query']['child']) {
			$strChild = ucwords($varsRequest['query']['child']);
			if ($strChild == 'EditorTemp') {
				$strChild = 'Editor';
			}
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
					if ($method == '_iniJs') {
						$this->$method();

					} else {
						$this->$method();
					}

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
		global $classSmarty;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
			'flag' => $vars['flagFS'],
		));

		$vars = $this->_updateVars(array(
			'flag'     => $vars['flagFS'],
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$json = json_encode($vars);
		$classSmarty->assign('varsLoad', $json);
		$classSmarty->assign('numNews', $this->getNumNews());
		$contents = $classSmarty->fetch($this->_extSelf['pathTplJs']);

		$this->sendJs(array(
			'data' => $contents,
		));
	}


	/**
		(array(
			'flag' => '',
			'vars' => $vars,
		))
	 */
	protected function _getVarsItem($arr)
	{
		global $classDb;
		global $varsAccount;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsConsumptionTax = $this->_getVarsConsumptionTax(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsTax = $this->_getVarsItemTax(array(
			'varsEntityNation'   => $varsEntityNation,
			'varsConsumptionTax' => $varsConsumptionTax,
			'numFiscalPeriod'    => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		//select form view prepare
		$str = 'jsonJgaapFS' . $arr['flag'];
		$varsFS[$str] = $this->_setTreeId(array(
			'idParent' => '',
			'vars'     => $varsFS[$str],
		));

		//select form view
		$varsJgaapFS = $this->_getVarsItemJgaapFS(array(
			'arrStrTitle'  => array(),
			'arrSelectTag' => array(),
			'vars'         => $varsFS[$str],
		));

		$arrayFSList = $this->_getFSList(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsFSItem = $this->_getVarsFSItem();

		$varsFSRows = $this->_getVarsFSRows(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$data = array(
			'varsFS'             => $varsFS,
			'varsFSRows'         => $varsFSRows,
			'varsTax'            => $varsTax,
			'varsConsumptionTax' => $varsConsumptionTax,
			'varsJgaapFS'        => $varsJgaapFS,
			'arrayFSList'        => $arrayFSList,
			'varsEntityNation'   => $varsEntityNation,
			'varsFSItem'         => $varsFSItem,
		);

		return $data;

	}

	/**
		(array(
			'arrStrTitle'  => array(),
			'vars'         => array(),
			'arrSelectTag' => array(),
		))
	 */
	protected function _getVarsItemJgaapFS($arr)
	{
		$arrSelectTag = &$arr['arrSelectTag'];
		$arrStrTitle = &$arr['arrStrTitle'];

		$array = &$arr['vars'];
		foreach ($array as $key => $value) {
			$arr['arrStrTitle'][$value['vars']['idTarget']] = $value['strTitle'];

			$arrLevel = preg_split("/-/", $value['id']);
			$num = count($arrLevel) - 2;
			$arrLevel = array();
			for ($i = 0 ; $i < $num; $i++) {
				$arrLevel[$i] = '';
			}
			$str =  ' ' . join('.', $arrLevel) . ' ';
			$strTitle = $str . $value['strTitle'];

			if (is_null($value['vars']['flagUse'])) {
				$arr['arrSelectTag'][] = array(
					'strTitle'     => $strTitle,
					'value'        => '',
					'flagDisabled' => 1,
				);

			} else {
				$arr['arrSelectTag'][] = array(
					'strTitle'     => $strTitle,
					'value'        => $value['vars']['idTarget'],
				);
			}

			if ($value['child']) {
				$data = $this->_getVarsItemJgaapFS(array(
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

	 */
	protected function _getVarsItemTax($arr)
	{
		$vars = $arr['varsConsumptionTax'];

		$varsItem = array();
		$arrayNew = array();
		$array = $vars['simple'];
		foreach ($array as $key => $value) {
			$arrayNew[$value['value']] = $value['strTitle'];
		}
		$varsItem['simple'] = $arrayNew;

		$array = $vars['generalProration'];
		if ((int) $arr['varsEntityNation']['flagConsumptionTaxDeducted']) {
			$array = $vars['generalEach'];
		}
		foreach ($array as $key => $value) {
			$arrayNew[$value['value']] = $value['strTitle'];
		}

		$varsItem['general'] = $arrayNew;

		$varsItem['generalOption'] = $vars['generalProration'];
		if ((int) $arr['varsEntityNation']['flagConsumptionTaxDeducted']) {
			$varsItem['generalOption'] = $vars['generalEach'];
		}

		$varsItem['simpleOption'] = $vars['simple'];

		return $varsItem;
	}

	/**
		(array(
			'flag' => '',
			'vars' => array(),
			'varsItem' => array(),
		))
	 */
	protected function _updateVars($arr)
	{
		global $classDb;
		global $classHtml;

		global $varsAccount;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingAccountsEntity;
		global $varsPluginAccountingAuthority;

		$idAccount = $varsPluginAccountingAccount['id'];
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$idAuthority = $varsPluginAccountingAccountsEntity[$idAccount][$idEntity]['idAuthority'];
		$varsAuthority = $varsPluginAccountingAuthority[$idAuthority];

		$vars = &$arr['vars'];
		$varsEntityNation = $arr['varsItem']['varsEntityNation'];

		$vars['varsItem']['varsEntityNation'] = $varsEntityNation;
		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done)$/", $flagCurrentFlagNow)) {
			$vars['portal']['varsDetail']['varsBtn'] = array();
			$vars['portal']['varsDetail']['view']['varsEdit'] = array();

		} else {
			$numFiscalPeriodTemp = '';
			if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
				$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] + 1;

			} elseif (preg_match("/^(tempNext)$/", $flagCurrentFlagNow)) {
				$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] - 1;
			}

			$classCalcCash = $this->_getClassCalc(array('flagType' => 'Cash'));
			$varsCash = $classCalcCash->allot(array(
				'flagStatus'      => 'varsPreference',
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			));
			$varsCashTemp = array();
			if ($numFiscalPeriodTemp) {
				$varsCashTemp = $classCalcCash->allot(array(
					'flagStatus'      => 'varsPreference',
					'numFiscalPeriod' => $numFiscalPeriodTemp,
					'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
				));
			}

			$str = 'jsonJgaapAccountTitle'. $arr['flag'];
			$arr['varsItem']['varsFS'][$str] = $this->_getFlagUseLog(array(
				'vars'                  => $arr['varsItem']['varsFS'][$str],
				'numFiscalPeriod'       => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				'numFiscalPeriodTemp'   => $numFiscalPeriodTemp,
				'flagFS'                => $arr['flag'],
				'varsCash'              => $varsCash['jsonCash'],
				'varsCashTemp'          => $varsCashTemp['jsonCash'],
				'flagCurrentFlagNow'    => $flagCurrentFlagNow,
				'classCalcAccountTitle' => $this->_getClassCalc(array('flagType' => 'AccountTitle')),
				'varsAuthority'         => array(
					'flagInsert' => ($varsAuthority['flagAllInsert'])? 1 : 0,
					'flagUpdate' => ($varsAuthority['flagAllUpdate'])? 1 : 0,
					'flagDelete' => ($varsAuthority['flagAllDelete'])? 1 : 0,
				),
			));

		}

		$vars['portal']['varsNavi']['templateDetail'] = $this->_updateVarsNaviFS((array(
			'vars'             => $vars,
			'varsEntityNation' => $varsEntityNation,
		)));

		$str = 'jsonJgaapAccountTitle'. $arr['flag'];
		$varsDetail = $this->_updateVarsList(array(
			'vars'             => $vars,
			'varsFS'           => $arr['varsItem']['varsFS'][$str],
			'varsItem'         => $arr['varsItem'],
			'varsEntityNation' => $varsEntityNation,
		));

		$vars['portal']['varsList']['varsDetail'] = $varsDetail;


		$vars['portal']['varsList']['tableTree']['varsDetail']['varsColumn'] = $this->_updateVarsColumn((array(
			'vars'             => $vars,
			'varsEntityNation' => $varsEntityNation,
		)));

		$vars['portal']['varsDetail']['templateDetail'] = $this->_updateVarsTemplateDetail((array(
			'vars'                => $vars,
			'varsEntityNation'    => $varsEntityNation,
			'varsTax'             => $arr['varsItem']['varsTax'],
			'arrSelectTagJgaapFS' => $arr['varsItem']['varsJgaapFS']['arrSelectTag'],
		)));

		$varsTemp = $classHtml->allot(array(
			'strClass'    => 'TableTree',
			'flagStatus'  => 'Html',
			'numTimeZone' => $varsAccount['numTimeZone'],
			'varsDetail'  => $varsDetail,
			'varsColumn'  => $vars['portal']['varsList']['tableTree']['varsDetail']['varsColumn'],
			'varsStatus'  => $vars['portal']['varsList']['tableTree']['varsDetail']['varsStatus'],
		));
		$vars['portal']['varsList']['varsHtml'] = $varsTemp['strHtml'];

		return $vars;
	}

	/**
		(array(
			'vars' => array(),
			'varsEntityNation' => array(),
		))
	 */
	protected function _updateVarsNaviFS($arr)
	{
		$vars = &$arr['vars'];
		$varsEntityNation = &$arr['varsEntityNation'];
		$arrayNew = array();
		$array = $vars['portal']['varsNavi']['templateDetail'][0]['arrayOption'];
		foreach ($array as $key => $value) {
			if ($value['value'] == 'CR' && !(int) $varsEntityNation['flagCR']) {

			} else {
				$arrayNew[] = $value;
			}
		}

		$vars['portal']['varsNavi']['templateDetail'][0]['arrayOption'] = $arrayNew;
		$vars['portal']['varsNavi']['templateDetail'][0]['numSize'] = count($arrayNew);

		return $vars['portal']['varsNavi']['templateDetail'];

	}

	/**
		(array(
			'vars'             => array(),
			'varsFS'             => array(),
			'varsItem'         => array(),
			'varsEntityNation' => $arr['varsEntityNation'],
		))

	 */
	protected function _updateVarsList($arr)
	{
		$array = &$arr['varsFS'];

		foreach ($array as $key => $value) {
			$array[$key]['id'] = '';
			$array[$key]['flagBoldNow'] = 0;
			$array[$key]['strClassFont'] = '';
			$array[$key]['strClassBg'] = '';
			$array[$key]['vars']['flagUseComment'] = 1;
			$array[$key]['vars']['idTarget'] = $value['vars']['idTarget'];
			$array[$key]['varsEntityNation']['flagConsumptionTaxDeducted'] = (int) $arr['varsEntityNation']['flagConsumptionTaxDeducted'];
			$array[$key]['varsColumnDetail'] = array(
				'flagDebit' => '',
				'flagUse' => '',
				'flagConsumptionTaxGeneralRule' => '',
				'flagConsumptionTaxSimpleRule' => '',
				'strAccountTitleJgaapFS' => '',
			);

			if (!is_null($value['vars']['flagUse'])) {

				//flagDebit
				if ($value['vars']['flagDebit']) {
					$array[$key]['varsColumnDetail']['flagDebit'] = $arr['vars']['varsItem']['strDebit'];

				} else {
					$array[$key]['varsColumnDetail']['flagDebit'] = $arr['vars']['varsItem']['strCredit'];
				}

				//flagUse
				if ((int) $value['vars']['flagUse']) {
					$array[$key]['varsColumnDetail']['flagUse'] = $arr['vars']['varsItem']['strShow'];

				} else {
					$array[$key]['varsColumnDetail']['flagUse'] = $arr['vars']['varsItem']['strHide'];
					$array[$key]['strClassFont'] = $arr['vars']['varsItem']['strClassNone'];
				}

				//General
				if (!(int) $arr['varsEntityNation']['flagConsumptionTaxFree']) {
					if ((int) $arr['varsEntityNation']['flagConsumptionTaxDeducted']) {
						if (is_null($value['vars']['flagConsumptionTaxGeneralRuleEach'])) {
							$array[$key]['varsColumnDetail']['flagConsumptionTaxGeneralRule']
							= $arr['varsItem']['varsTax']['general']['none'];

						} else {
							$array[$key]['varsColumnDetail']['flagConsumptionTaxGeneralRule']
							= $arr['varsItem']['varsTax']['general'][$value['vars']['flagConsumptionTaxGeneralRuleEach']];
						}

					} else {
						if (is_null($value['vars']['flagConsumptionTaxGeneralRuleProration'])) {
							$array[$key]['varsColumnDetail']['flagConsumptionTaxGeneralRule']
							= $arr['varsItem']['varsTax']['general']['none'];

						} else {
							$array[$key]['varsColumnDetail']['flagConsumptionTaxGeneralRule']
							= $arr['varsItem']['varsTax']['general'][$value['vars']['flagConsumptionTaxGeneralRuleProration']];
						}
					}

				}

				//Simple
				if (!(int) $arr['varsEntityNation']['flagConsumptionTaxFree']) {

					if (is_null($value['vars']['flagConsumptionTaxSimpleRule'])) {
						$array[$key]['varsColumnDetail']['flagConsumptionTaxSimpleRule']
							= $arr['varsItem']['varsTax']['simple']['none'];

					} else {
						$flagConsumptionTaxBusinessType = $arr['varsEntityNation']['flagConsumptionTaxBusinessType'];

						if ($value['vars']['flagConsumptionTaxSimpleRule'] == 'tax-Default') {
							$array[$key]['varsColumnDetail']['flagConsumptionTaxSimpleRule']
								= $arr['varsItem']['varsTax']['simple']['tax-' . $flagConsumptionTaxBusinessType];

						} elseif ($value['vars']['flagConsumptionTaxSimpleRule'] == 'tax-Back-Default') {
							$array[$key]['varsColumnDetail']['flagConsumptionTaxSimpleRule']
								= $arr['varsItem']['varsTax']['simple']['tax-Back-' . $flagConsumptionTaxBusinessType];

						} else {
							$array[$key]['varsColumnDetail']['flagConsumptionTaxSimpleRule']
								= $arr['varsItem']['varsTax']['simple'][$value['vars']['flagConsumptionTaxSimpleRule']];
						}
					}
				}

				//JgaapFS
				$array[$key]['varsColumnDetail']['strAccountTitleJgaapFS']
					= $arr['varsItem']['varsJgaapFS']['arrStrTitle'][$value['vars']['idAccountTitleJgaapFS']];
			}

			if ($value['child']) {
				$array[$key]['child'] = $this->_updateVarsList(array(
					'vars'             => $arr['vars'],
					'varsFS'           => $array[$key]['child'],
					'varsItem'         => $arr['varsItem'],
					'varsEntityNation' => $arr['varsEntityNation'],
				));
			}
		}

		return $array;
	}

	/**
		(array(
			'vars' => array(),
			'varsEntityNation' => array(),
		))
	 */
	protected function _updateVarsColumn($arr)
	{
		$vars = &$arr['vars'];
		$varsEntityNation = &$arr['varsEntityNation'];
		$arrayNew = array();
		$array = $vars['portal']['varsList']['tableTree']['varsDetail']['varsColumn'];
		foreach ($array as $key => $value) {

			if ((int) $varsEntityNation['flagConsumptionTaxFree']) {
				if ($value['id'] == 'FlagConsumptionTaxGeneralRule' || $value['id'] == 'FlagConsumptionTaxSimpleRule') {

				} else {
					$arrayNew[] = $value;
				}

			} else {
				if ($value['id'] == 'FlagConsumptionTaxGeneralRule') {
					if ((int) $varsEntityNation['flagConsumptionTaxGeneralRule']) {
						$arrayNew[] = $value;
					}

				} elseif ($value['id'] == 'FlagConsumptionTaxSimpleRule') {
					if (!(int) $varsEntityNation['flagConsumptionTaxGeneralRule']) {
						$arrayNew[] = $value;
					}

				} else {
					$arrayNew[] = $value;
				}
			}
		}

		return $arrayNew;

	}

	/**
		(array(
			'vars'             => array(),
			'varsEntityNation' => array(),
			'varsTax'          => array(),
		))
	 */
	protected function _updateVarsTemplateDetail($arr)
	{
		$vars = &$arr['vars'];
		$varsEntityNation = &$arr['varsEntityNation'];
		$arrayNew = array();
		$array = $vars['portal']['varsDetail']['templateDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'FlagConsumptionTaxGeneralRule'
				|| $value['id'] == 'DummyFlagConsumptionTaxGeneralRule'
			) {
				if (!(int) $varsEntityNation['flagConsumptionTaxFree']
					&& (int) $varsEntityNation['flagConsumptionTaxGeneralRule']
				) {
					$value['arrayOption'] = $arr['varsTax']['generalOption'];
					$arrayNew[] = $value;
				}

			} elseif ($value['id'] == 'FlagConsumptionTaxSimpleRule'
				|| $value['id'] == 'DummyFlagConsumptionTaxSimpleRule'
			) {
				if (!(int) $varsEntityNation['flagConsumptionTaxFree']
					&& !(int) $varsEntityNation['flagConsumptionTaxGeneralRule']
				) {
					$value['arrayOption'] = $arr['varsTax']['simpleOption'];
					$arrayNew[] = $value;
				}

			} elseif ($value['id'] == 'StrAccountTitleJgaapFS') {
				$value['arrayOption'] = $arr['arrSelectTagJgaapFS'];
				$arrayNew[] = $value;

			} else {
				$arrayNew[] = $value;
			}
		}
		return $arrayNew;
	}

	/**

	 */
	protected function _iniNaviSearch()
	{
		$this->_setSearch();
	}

	/**

	 */
	protected function _setSearch()
	{
		global $varsRequest;
		global $varsPluginAccountingPreference;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$flagFS = $this->_checkValueFS(array(
			'vars' => $vars,
		));

		if (FLAG_CHECK_UPDATE) {
			$str = 'acountTitle';
			if ($varsPluginAccountingPreference['jsonStampUpdate'][$str] <= $varsRequest['query']['jsonStamp']['stamp']) {
				$this->sendVars(array(
					'flag'    => 10,
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => '',
				));
			}
		}

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
			'flag' => $flagFS,
		));

		$vars = $this->_updateVars(array(
			'flag'     => $flagFS,
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$this->sendVars(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'varsDetail'     => $vars['portal']['varsList']['varsDetail'],
				'varsHtml'       => $vars['portal']['varsList']['varsHtml'],
				'varsColumn'     => $vars['portal']['varsList']['tableTree']['varsDetail']['varsColumn'],
				'templateDetail' => $vars['portal']['varsDetail']['templateDetail']
			),
		));
	}

	/**
		(array(
			'vars' => array(),
		))
	 */
	protected function _checkValueFS($arr)
	{
		global $varsRequest;
		global $varsPluginAccountingAccount;

		$flagFS = $varsRequest['query']['jsonValue']['vars']['FlagAccountTitle'];
		$array = $arr['vars']['portal']['varsNavi']['templateDetail'][0]['arrayOption'];
		$varsEntity = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));
		$flag = 0;
		foreach ($array as $key => $value) {
			if ($value['value'] == 'CR' && !$varsEntity['flagCR']) {
				continue;
			}
			if ($value['value'] == $flagFS) {
				$flag = 1;
			}
		}
		if (!$flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}

		return $flagFS;
	}

	/**
	 *
	 */
	protected function _iniListReload()
	{
		$this->_setSearch();
	}

	/**
	 *
	 */
	protected function _iniDetailDelete()
	{
		global $varsRequest;

		$this->_setDelete();
	}

	/**

	 */
	protected function _setDelete()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsRequest;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		if (!$this->_checkCurrent()) {
			$this->_sendOld();
		}

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'delete',
			'idTarget'      => $this->_extSelf['idPreference'],
		));

		if (!$flag) {
			$this->_sendOld();
		}

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$flagFS = $this->_checkValueFS(array(
			'vars' => $vars,
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
			'flag' => $flagFS,
		));

		$this->_checkValueDetailDelete(array(
			'varsItem' => $varsItem,
			'flagFS'   => $flagFS,
			'vars'     => $vars,
			'idTarget' => $idTarget,
		));

		$str = 'jsonJgaapAccountTitle'. $flagFS;
		$varsFS = $this->_getVarsDelete(array(
			'vars'     => $varsItem['varsFS'][$str],
			'idTarget' => $idTarget,
		));

		$jsonAccountTitle = json_encode($varsFS);
		$this->checkTextSize(array(
			'flag' => 'errorDataMax',
			'str'  => $jsonAccountTitle,
		));
		$strAccountTitle = 'jsonJgaapAccountTitle'. $flagFS;

		$arrDbColumn = array($strAccountTitle);
		$arrDbValue = array($jsonAccountTitle);

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
		);

		try {
			$dbh->beginTransaction();

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingFS' . $strNation,
				'arrColumn' => $arrDbColumn,
				'flagAnd'  => 1,
				'arrWhere' => $arrWhere,
				'arrValue'  => $arrDbValue,
			));

			$rows = $classDb->getSelect(array(
				'idModule' => 'accounting',
				'strTable' => 'accountingFSId' . $strNation,
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
				),
			));
			$varsId = ($rows['arrRows'][0][$strAccountTitle])? $rows['arrRows'][0][$strAccountTitle] : array();
			$varsId[$idTarget] = 1;

			$jsonId = json_encode($varsId);
			$this->checkTextSize(array(
				'flag' => 'errorDataMax',
				'str'  => $jsonId,
			));
			$arrDbValue = array($jsonId);

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingFSId' . $strNation,
				'arrColumn' => $arrDbColumn,
				'flagAnd'  => 1,
				'arrWhere' => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idEntity',
						'flagCondition' => 'eq',
						'value'         => $varsPluginAccountingAccount['idEntityCurrent'],
					),
				),
				'arrValue'  => $arrDbValue,
			));

			$this->_setDeleteSubAccountTitle();

			if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
				$classCalcTempNextAccountTitle = $this->_getClassCalc(array(
					'flagType'   => 'TempNext',
					'flagDetail' => 'AccountTitle',
				));
				$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
				$numFiscalPeriod = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];

				$classCalcTempNextAccountTitle->allot(array(
					'flagStatus'      => 'delete',
					'numFiscalPeriod' => $numFiscalPeriod,
					'flagFS'          => $flagFS,
					'idTarget'        => $idTarget,
				));
			}

			$this->_updateDbPreferenceStamp(array('strColumn' => 'fSId'));
			$this->_updateDbPreferenceStamp(array('strColumn' => 'fS'));
			$this->_updateDbPreferenceStamp(array('strColumn' => 'subAccountTitle'));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$this->_setSearch();
	}

	/**
		array(
			'varsItem' => $varsItem,
			'flagFS'   => $flagFS,
			'vars'     => $vars,
			'idTarget' => $idTarget,
		)
	 */
	protected function _checkValueDetailDelete($arr)
	{
		global $varsPluginAccountingAccount;

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());

		$numFiscalPeriodTemp = '';
		if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
			$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] + 1;
		}

		$classCalcCash = $this->_getClassCalc(array('flagType' => 'Cash'));
		$varsCash = $classCalcCash->allot(array(
			'flagStatus'      => 'varsPreference',
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
		));
		$varsCashTemp = array();
		if ($numFiscalPeriodTemp) {
			$varsCashTemp = $classCalcCash->allot(array(
				'flagStatus'      => 'varsPreference',
				'numFiscalPeriod' => $numFiscalPeriodTemp,
				'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			));
		}

		$varsFS = $arr['varsItem']['varsFS']['jsonJgaapAccountTitle'. $arr['flagFS']];
		$varsFS = $this->_getFlagUseLog(array(
			'vars'                  => $varsFS,
			'idTarget'              => $arr['idTarget'],
			'numFiscalPeriod'       => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'numFiscalPeriodTemp'   => $numFiscalPeriodTemp,
			'varsCash'              => $varsCash['jsonCash'],
			'varsCashTemp'          => $varsCashTemp['jsonCash'],
			'classCalcAccountTitle' => $this->_getClassCalc(array('flagType' => 'AccountTitle')),
			'flagFS'                => $arr['flagFS'],
		));

		$arrayBlock = $this->_getTreeBlock(array(
			'vars'     => $varsFS,
			'idTarget' => $arr['idTarget'],
			'arrNew'   => array(),
		));
		if (!$arrayBlock) {
			$this->_sendOld();
		}

		$varsTarget = array();
		foreach ($arrayBlock as $key => $value) {
			if ($value['vars']['idTarget'] == $arr['idTarget']) {
				$varsTarget = $value;
				break;
			}
		}

		if ($value['flagDefault']
			|| $varsTarget['vars']['flagUseCash']
			|| $varsTarget['vars']['flagUseCashTemp']
			|| $varsTarget['vars']['flagUseLog']
			|| $varsTarget['vars']['flagUseLogTemp']
		) {
			$this->_sendOld();
		}
	}

	protected function _setDeleteSubAccountTitle()
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $varsRequest;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);
		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];

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
				'flagType'      => '',
				'strColumn'     => 'idAccountTitle',
				'flagCondition' => 'eq',
				'value'         => $idTarget,
			),
		);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingSubAccountTitle' . $strNation,
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => $arrWhere,
		));
		$array = $rows['arrRows'];

		$classDb->deleteRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingSubAccountTitle' . $strNation,
			'flagAnd'  => 1,
			'arrWhere'  => $arrWhere,
		));

		foreach ($array as $key => $value) {
			$classDb->deleteRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingSubAccountTitleValue' . $strNation,
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
						'strColumn'     => 'numFiscalPeriod',
						'flagCondition' => 'eq',
						'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					),
					array(
						'flagType'      => '',
						'strColumn'     => 'idSubAccountTitle',
						'flagCondition' => 'eq',
						'value'         => $value['idSubAccountTitle'],
					),
				),
			));
		}
	}
}
