<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_FixedAssets extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference' => 'fixedAssetsWindow',
		'idLog'        => 'logWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/fixedAssets.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/fixedAssets.php',
		'tplDetail'    => 'else/plugin/accounting/html/fixedAssets.html',
		'tplComment'   => 'else/plugin/accounting/html/fixedAssetsComment.html',
		'varsDefault'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/fixedAssetsAccountTitle.php',
		'varsOption'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/depreciation.php',
	);

	/**
	 *
	 */
	public function run()
	{
		$this->_checkCorporationClass(array('flagChild' => 0));

		global $varsRequest;

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
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__ . '/' . $path);
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
		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$this->_setJs(array(
			'pathVars'        => $this->_extSelf['pathVarsJs'],
			'pathTpl'         => $this->_extSelf['pathTplJs'],
			'arrFolder' => array(),
			'arrSearch'       => array(
				'idModule'  => 'accounting',
				'numLotNow' => 0,
				'strTable'  => 'accountingLogFixedAssets' . $strNation,
				'arrOrder'  => array(
					'strColumn' => 'id',
					'flagDesc'  => 1,
				),
				'insCurrent' => $this,
				'arrWhere'  => array(
				),
			),
		));

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
		global $varsRequest;
		global $varsAccount;

		$idAccount = $varsAccount['id'];
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];

		$strStatus = 'Select';
		if ($varsRequest['query']['func'] == 'ListOutput'
			|| $varsRequest['query']['func'] == 'ListPrint'
		) {
			$strStatus = 'Output';
		}

		$varsAuthority = $this->_getVarsAuthority(array());

		$strSql = 'idEntity = ? && numFiscalPeriod = ? ';
		$arrValue = array($idEntity, $numFiscalPeriod);

		$flag = $varsRequest['query']['jsonSearch']['ph']['flagApply'];
		if (!$flag) {
			$flag = 'none';
		}

		if ($flag != 'none') {
			$flagRemove = ($flag == 'remove')? 1 : 0;
			$strSql .= '&& flagRemove = ? ';
			$arrValue[] = $flagRemove;
		}

		$flagSql = 0;
		if ($varsAuthority == 'admin' || $varsAuthority['flagAll' . $strStatus]) {
			$flagSql = 1;

		} elseif ($varsAuthority['flagMy' . $strStatus]) {
			$strSql .= '&& idAccount = ?';
			$arrValue[] = $idAccount;
			$flagSql = 1;
		}

		if ($flagSql) {
			$array = array(
				'strSql'   => $strSql,
				'arrValue' => $arrValue,
			);
			return $array;
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

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$vars['varsItem']['arrAccountTitle'] = $varsItem['arrAccountTitle'];
		$vars['varsItem']['arrDepartment'] = $varsItem['arrDepartment'];
		$vars['varsItem']['varsOptions'] = $varsItem['varsOptions'];
		$vars['varsItem']['arrAccountTitleFixedAssets'] = $varsItem['arrAccountTitleFixedAssets'];
		$vars['varsItem']['varsStampTerm'] = $varsItem['varsStampTerm'];
		$vars['varsItem']['numFiscalTermMonth'] = $varsItem['varsEntityNation']['numFiscalTermMonth'];
		$vars['varsItem']['numFiscalBeginningMonth'] = $varsItem['varsEntityNation']['numFiscalBeginningMonth'];
		$vars['varsItem']['varsCalc'] = array(
			'flagFractionDepSurvivalRate' => $varsItem['varsFixedAssets']['flagFractionDepSurvivalRate'],
			'flagFractionDepSurvivalRateLimit' => $varsItem['varsFixedAssets']['flagFractionDepSurvivalRateLimit'],
			'flagFractionRatioOperate' => $varsItem['varsFixedAssets']['flagFractionRatioOperate'],
		);

		$varsAuthority = $this->_getVarsAuthority(array());

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$flagCurrent = 1;
		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done)$/", $flagCurrentFlagNow)) {
			$flagCurrent = 0;
		}

		$vars['portal']['varsList']['varsBtn'] = $this->_updateVarsListBtn(array(
			'vars'        => $vars['portal']['varsList']['varsBtn'],
			'flagCurrent' => $flagCurrent,
		));

		if (!($varsAuthority == 'admin' || $varsAuthority['flagMyOutput'] || $varsAuthority['flagAllOutput'])) {
			$vars['portal']['varsList']['varsEdit']['flagOutputUse'] = 0;
			$vars['portal']['varsList']['varsEdit']['flagPrintUse'] = 0;
			$vars['portal']['varsList']['varsStart']['varsEdit']['flagOutputUse'] = 0;
			$vars['portal']['varsList']['varsStart']['varsEdit']['flagPrintUse'] = 0;
			$vars['portal']['varsDetail']['varsEdit']['flagOutputUse'] = 0;
			$vars['portal']['varsDetail']['view']['varsEdit']['flagOutputUse'] = 0;
		}

		$flagPreferenceUse = 0;
		if ($varsAuthority == 'admin' || $varsAuthority['flagAllSelect']) {
			$flagPreferenceUse = 1;
		}

		$vars['portal']['varsList']['varsEdit']['flagPreferenceUse'] = $flagPreferenceUse;
		$vars['portal']['varsList']['varsStart']['varsEdit']['flagPreferenceUse'] = $flagPreferenceUse;


		if (!$flagCurrent) {
			$vars['portal']['varsDetail']['varsBtn'] = array();
			$this->_updateVarsEditLock(array(
				'vars' => $vars['portal']['varsDetail']['view']['varsEdit'],
			));
		}

		$rows = $this->getSearch($arr['arrSearch']);

		$vars = $this->_updateSearch(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'rows'     => $rows,
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
			if ($key == 'flagReloadUse' || $key == 'flagOutputUse') {
				continue;
			}
			$array[$key] = 0;
		}
	}

	/**
		(array(
			'vars' => $vars,
		))
	 */
	protected function _getVarsItem($arr)
	{
		global $varsPluginAccountingAccount;

		$varsDefault = $this->getVars(array(
			'path' => $this->_extSelf['varsDefault'],
		));

		$varsFixedAssets = $this->_getVarsFixedAssets(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent']
		));

		$arrAccountTitle = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => array(),
			'numFiscalPeriod'    => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrAccountTitleFixedAssets = $this->_getTree(array(
			'idTarget'        => $this->_extendSelf['idFixedAssets'],
			'varsDefault'     => $varsDefault,
			'varsFixedAssets' => $varsFixedAssets['jsonAccountTitle'],
			'arrAccountTitle' => $arrAccountTitle,
		));

		$this->_getVarsNone(array(
			'varsTarget' => &$arrAccountTitleFixedAssets,
			'strValue'   => 'none',
			'strNone'    => $arr['vars']['varsItem']['strNone'],
		));

		$this->_getVarsNone(array(
			'varsTarget' => &$arrAccountTitle,
			'strValue'   => 'none',
			'strNone'    => $arr['vars']['varsItem']['strNone'],
		));

		$arrDepartment = $this->_getVarsDepartment(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$this->_getVarsNone(array(
			'varsTarget' => &$arrDepartment,
			'strValue'   => 0,
			'strNone'    => $arr['vars']['varsItem']['strNone'],
		));

		$varsOptions = $this->_getVarsOptions();

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsFlag = array(
			'flagFiscalPeriod'  => 'f1',
		);

		$varsStampTerm = $this->_getVarsStampTerm(array(
			'varsFlag'         => $varsFlag,
			'varsEntityNation' => $varsEntityNation,
			'numFiscalPeriod'  => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsFlagAllot = $this->_getVarsFlagAllot(array(
			'varsEntityNation' => $varsEntityNation,
		));

		$varsStampFiscalPeriod = $this->_getVarsStampFiscalPeriod(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'varsEntityNation' => $varsEntityNation,
		));

		$varsNumUsefulLife = $this->_getVarsNumUsefulLife(array(
			'strYear' => $arr['vars']['varsItem']['strYear'],
		));
		$this->_getVarsNone(array(
			'varsTarget' => &$varsNumUsefulLife,
			'strValue'   => 0,
			'strNone'    => $arr['vars']['varsItem']['strNone'],
		));

		$data = array(
			'varsStampTerm'              => $varsStampTerm,
			'varsDefault'                => $varsDefault,
			'varsFixedAssets'            => $varsFixedAssets,
			'varsEntityNation'           => $varsEntityNation,
			'varsNumUsefulLife'          => $varsNumUsefulLife,
			'varsOptions'                => $varsOptions,
			'arrDepartment'              => $arrDepartment,
			'arrAccountTitle'            => $arrAccountTitle,
			'arrAccountTitleFixedAssets' => $arrAccountTitleFixedAssets,
			'varsFlagAllot'              => $varsFlagAllot,
			'varsStampFiscalPeriod'      => $varsStampFiscalPeriod,
		);

		return $data;
	}

		/**
		(array(
			'strYear' => $arr['vars']['varsItem']['strYear'],
		))
	 */
	protected function _getVarsNumUsefulLife($arr)
	{
		$array = array(
			'arrStrTitle'  => array(),
			'arrSelectTag' => array(),
		);

		for ($j = 2; $j <= 50; $j++) {
			$data = array(
				'strTitle' => $j . $arr['strYear'],
				'value' => $j,
			);
			$array['arrStrTitle'][$j] = $j . $arr['strYear'];
			$array['arrSelectTag'][] = $data;
		}

		return $array;
	}



	/**
		(array(
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _getVarsFlagAllot($arr)
	{
		$data = array(
			'flagSellingAdminCost' => 1,
			'flagProductsCost' => 0,
			'flagNonOperatingExpenses' => 0,
			'flagAgricultureCost' => 0,
		);

		//ProductsCost
		//個人一般
		if ($arr['varsEntityNation']['flagCorporation'] == 2) {
			if ($arr['varsEntityNation']['flagCR']) {
				$data['flagProductsCost'] = 1;
			}

		//個人不動産
		} elseif ($arr['varsEntityNation']['flagCorporation'] == 3) {

		//個人農業
		} elseif ($arr['varsEntityNation']['flagCorporation'] == 4) {

		//法人
		} else {
			if ($arr['varsEntityNation']['flagCR']) {
				$data['flagProductsCost'] = 1;
			}
		}

		//NonOperatingExpenses

		//個人一般
		if ($arr['varsEntityNation']['flagCorporation'] == 2) {

		//個人不動産
		} elseif ($arr['varsEntityNation']['flagCorporation'] == 3) {

		//個人農業
		} elseif ($arr['varsEntityNation']['flagCorporation'] == 4) {

		//法人
		} else {
			$data['flagNonOperatingExpenses'] = 1;
		}

		//AgricultureCost
		//個人一般
		if ($arr['varsEntityNation']['flagCorporation'] == 2) {

		//個人不動産
		} elseif ($arr['varsEntityNation']['flagCorporation'] == 3) {

		//個人農業
		} elseif ($arr['varsEntityNation']['flagCorporation'] == 4) {
			if ($arr['varsEntityNation']['flagCR']) {
				$data['flagAgricultureCost'] = 1;
			}

		//法人
		} else {

		}

		return $data;

	}

	/**
		(array(
			'varsTarget' => &$arrDepartment,
			'strValue'   => 'none',
			'strNone'    => $arr['vars']['varsItem']['strNone'],
		))
	 */
	protected function _getVarsNone($arr)
	{
		$arr['varsTarget']['arrStrTitle'][$arr['strValue']] = $arr['strNone'];
		$data = array('strTitle' => $arr['strNone'], 'value' => $arr['strValue']);
		array_unshift($arr['varsTarget']['arrSelectTag'], $data);

		return $arr['varsTarget'];
	}

	/**
		(array(

		))
	 */
	protected function _getTree($arr)
	{
		global $varsPluginAccountingAccount;

		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsBlock = $this->_getTreeBlock(array(
			'vars'     => $varsFS['jsonJgaapAccountTitleBS'],
			'idTarget' => $this->_extendSelf['idFixedAssets'],
		));

		$varsBlock = $this->_setTreeId(array(
			'idParent' => '',
			'vars'     => $varsBlock,
		));

		$varsData = $this->_setTreeBlock(array(
			'arrStrTitle'     => array(),
			'arrSelectTag'    => array(),
			'vars'            => $varsBlock,
			'varsDefault'     => $arr['varsDefault'],
			'varsFixedAssets' => $arr['varsFixedAssets'],
			'arrAccountTitle' => $arr['arrAccountTitle'],
		));

		$array = array(
			'arrStrTitle'     => $varsData['arrStrTitle'],
			'arrSelectTag'    => $varsData['arrSelectTag'],
		);

		return $array;
	}



	/**
		(array(
			'arrStrTitle'     => array(),
			'arrSelectTag'    => array(),
			'vars'            => $varsBlock,
			'varsDefault'     => $arr['varsDefault'],
			'varsFixedAssets' => $arr['varsFixedAssets'],
		))
	 */
	protected function _setTreeBlock($arr)
	{
		$arrSelectTag = &$arr['arrSelectTag'];
		$arrStrTitle = &$arr['arrStrTitle'];

		$array = &$arr['vars'];
		foreach ($array as $key => $value) {
			$strTitleFS = $value['strTitle'];

			$varsFixedAssets = array();
			if (!is_null($value['vars']['flagUse'])) {
				if ($value['vars']['flagDebit'] == 1) {
					$idTarget = $value['vars']['idTarget'];
					if ($arr['varsDefault'][$idTarget]) {
						$varsFixedAssets = $arr['varsDefault'][$idTarget];
					}
					if ($arr['varsFixedAssets'][$idTarget]) {
						$varsFixedAssets = $arr['varsFixedAssets'][$idTarget];
					}

					$arrayStr = array('lossOnDisposalOfFixedAssets', 'accumulatedDepreciation', 'sellingAdminCost', 'productsCost', 'nonOperatingExpenses', 'agricultureCost');
					foreach ($arrayStr as $keyStr => $valueStr) {
						$str = $varsFixedAssets[$valueStr];
						if (!$arr['arrAccountTitle']['arrStrTitle'][$str]) {
							$varsFixedAssets[$valueStr] = 'none';
						}
					}

					$arrayStr = array('numRatioSellingAdminCost', 'numRatioProductsCost', 'numRatioNonOperatingExpenses', 'numRatioAgricultureCost');
					foreach ($arrayStr as $keyStr => $valueStr) {
						$data = $varsFixedAssets[$valueStr];
						$varsFixedAssets[$valueStr] = sprintf("%.2f", $data);
					}
				}
			}

			$data = array(
				'strTitle'        => $value['strTitle'],
				'strTitleFS'      => $strTitleFS,
				'flagDebit'       => (int) $value['vars']['flagDebit'],
				'flagUse'         => (is_null($value['vars']['flagUse']))? '' : (int) $value['vars']['flagUse'],
				'varsFixedAssets' => $varsFixedAssets
			);

			$arrLevel = preg_split("/-/", $value['id']);
			$num = count($arrLevel) - 1;
			$arrLevel = array();
			for ($i = 0 ; $i < $num; $i++) {
				$arrLevel[$i] = '';
			}
			$str =  ' ' . join('.', $arrLevel) . ' ';
			$strTitle = $str . $value['strTitle'];

			if (is_null($value['vars']['flagUse'])
				|| $value['vars']['idTarget'] == 'accumulatedDepreciation'
				|| $value['vars']['idTarget'] == 'allowanceForBadDebtsLongTermOther'
			) {
				$arr['arrSelectTag'][] = array(
					'strTitle'     => $strTitle,
					'value'        => '',
					'flagDisabled' => 1,
				);

			} else {
				if ($value['vars']['flagDebit'] == 1) {
					$arr['arrSelectTag'][] = array(
						'strTitle' => $strTitle,
						'value'    => $value['vars']['idTarget'],
					);

				} else {
					$arr['arrSelectTag'][] = array(
						'strTitle'     => $strTitle,
						'value'        => '',
						'flagDisabled' => 1,
					);
				}
				if ($value['vars']['flagDebit'] == 1) {
					$arr['arrStrTitle'][$value['vars']['idTarget']] = $data;
				}
			}

			if ($value['child']) {
				$dataTemp = $this->_setTreeBlock(array(
					'vars'            => $array[$key]['child'],
					'arrSelectTag'    => $arr['arrSelectTag'],
					'arrStrTitle'     => $arr['arrStrTitle'],
					'varsDefault'     => $arr['varsDefault'],
					'varsFixedAssets' => $arr['varsFixedAssets'],
					'arrAccountTitle' => $arr['arrAccountTitle'],
				));
				$array[$key]['child'] = $dataTemp['vars'];
				$arrSelectTag =  $dataTemp['arrSelectTag'];
				$arrStrTitle =  $dataTemp['arrStrTitle'];

			}
		}

		return $arr;
	}

	/**
		(array(

		))
	 */
	protected function _getVarsOptions()
	{
		$arrStrTitle = $this->getVars(array(
			'path' => $this->_extSelf['varsOption'],
		));

		$arrayNew = array();

		$array = $arrStrTitle;
		foreach ($array as $key => $value) {
			$arrayNew[$key]['arrStrTitle'] = $value;

			$arrSelectTag = array();

			$arrayOption = $value;
			foreach ($arrayOption as $keyOption => $valueOption) {
				$data = array();
				$data['value'] = $keyOption;
				$data['strTitle'] = $valueOption;
				$arrSelectTag[] = $data;
			}
			$arrayNew[$key]['arrSelectTag'] = $arrSelectTag;
		}

		return $arrayNew;
	}


	/**
		(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		))
	 */
	protected function _updateVars($arr)
	{
		global $varsPluginAccountingPreference;
		global $varsPluginAccountingAccount;

		$varsEntityNation = $arr['varsItem']['varsEntityNation'];
		$flagFractionOption = $this->_getFlagFractionOption(array(
			'varsEntityNation' => $varsEntityNation,
			'arrayOption'      => $arr['varsItem']['varsOptions']['flagFraction']['arrSelectTag'],
		));

		$array = array(
			'fsfixed'    => $arr['varsItem']['arrAccountTitleFixedAssets']['arrSelectTag'],
			'fs'         => $arr['varsItem']['arrAccountTitle']['arrSelectTag'],
			'department' => $arr['varsItem']['arrDepartment']['arrSelectTag'],
			'fraction'   => $flagFractionOption,
			'method'     => $arr['varsItem']['varsOptions']['flagDepMethod']['arrSelectTag'],
			'life'       => $arr['varsItem']['varsNumUsefulLife']['arrSelectTag'],
			'tax'        => $arr['varsItem']['varsOptions']['flagTaxFixed']['arrSelectTag'],
			'taxtype'    => $arr['varsItem']['varsOptions']['flagTaxFixedType']['arrSelectTag'],
			'reasonup'   => $arr['varsItem']['varsOptions']['flagDepUp']['arrSelectTag'],
			'reasondown' => $arr['varsItem']['varsOptions']['flagDepDown']['arrSelectTag'],
		);

		$arr['vars']['portal']['varsNavi']['search']['varsDetail']['varsSearchItem']['templateDetail']['restOption'] = $array;

		$arr['vars']['portal']['varsNavi']['search']['varsDetail']['varsSearchItem']['templateDetail']['firstOption'] = $this->_updateVarsTemplateNavi(array(
			'vars'     => $arr['vars'],
			'varsItem' => $arr['varsItem'],
		));

		$arr['vars']['portal']['varsDetail']['templateDetail'] = $this->_updateVarsTemplateDetail(array(
			'vars'     => $arr['vars'],
			'varsItem' => $arr['varsItem'],
		));

		return $arr['vars'];
	}

	/**
		(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		))
	 */
	protected function _updateVarsTemplateNavi($arr)
	{
		global $classEscape;

		$varsEntityNation = $arr['varsItem']['varsEntityNation'];
		$arrayNew = array();
		$array = $arr['vars']['portal']['varsNavi']['search']['varsDetail']['varsSearchItem']['templateDetail']['firstOption'];
		foreach ($array as $key => $value) {
			list($dummy, $str) = preg_split("/-/", $value['value']);
			$id = $classEscape->toLower(array('str' => $str));
			$idCap = ucwords($id);
			if ($idCap == 'ProductsCost' || $idCap == 'NumRatioProductsCost') {
				//個人一般
				if ($varsEntityNation['flagCorporation'] == 2) {
					if ($varsEntityNation['flagCR']) {
						$arrayNew[] = $value;
					} else {
						continue;
					}

					//個人不動産
				} elseif ($varsEntityNation['flagCorporation'] == 3) {
					continue;

					//個人農業
				} elseif ($varsEntityNation['flagCorporation'] == 4) {
					continue;

					//法人
				} else {
					if ($varsEntityNation['flagCR']) {
						$arrayNew[] = $value;
					} else {
						continue;
					}
				}

			} elseif ($idCap == 'NonOperatingExpenses' || $idCap == 'NumRatioNonOperatingExpenses') {
				//個人一般
				if ($varsEntityNation['flagCorporation'] == 2) {
					continue;

					//個人不動産
				} elseif ($varsEntityNation['flagCorporation'] == 3) {
					continue;

					//個人農業
				} elseif ($varsEntityNation['flagCorporation'] == 4) {
					continue;

					//法人
				} else {
					$arrayNew[] = $value;
				}

			} elseif ($idCap == 'AgricultureCost' || $idCap == 'NumRatioAgricultureCost') {
				//個人一般
				if ($varsEntityNation['flagCorporation'] == 2) {
					continue;

					//個人不動産
				} elseif ($varsEntityNation['flagCorporation'] == 3) {
					continue;

					//個人農業
				} elseif ($varsEntityNation['flagCorporation'] == 4) {
					if ($varsEntityNation['flagCR']) {
						$arrayNew[] = $value;
					} else {
						continue;
					}

					//法人
				} else {
					continue;
				}

			} elseif ($idCap == 'NumRatioOperate') {
				if ($varsEntityNation['flagCorporation'] != 1) {
					$arrayNew[] = $value;
				} else {
					continue;
				}

			} elseif ($idCap == 'IdDepartment') {
				if ($arr['varsItem']['arrDepartment']['arrSelectTag']) {
					$arrayNew[] = $value;
				} else {
					continue;
				}

			} else {
				$arrayNew[] = $value;
			}
		}

		return $arrayNew;
	}

	/**
		(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		))
	 */
	protected function _updateVarsTemplateDetail($arr)
	{
		global $classEscape;

		$varsEntityNation = $arr['varsItem']['varsEntityNation'];
		$array = $arr['vars']['portal']['varsDetail']['templateDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'JsonDetail') {
				$data = $this->_updateVarsTemplateDetailJsonDetail(array(
					'varsStr'    => $arr['vars']['varsItem']['varsHtml'],
					'varsItem'   => $arr['varsItem'],
					'varsDetail' => $value['varsFormSensitive']['varsTmpl']['varsDetail'],
				));
				$array[$key]['varsFormSensitive']['varsTmpl']['varsDetail'] = $data['varsDetail'];
				$array[$key]['varsFormSensitive']['varsHtml'] = $data['varsHtml'];

				break;
			}
		}

		return $array;
	}

	/**
		(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		))
	 */
	protected function _updateVarsTemplateDetailJsonDetail($arr)
	{
		global $classEscape;

		$arrayNew = array();
		$arrayStr = $arr['varsStr'];
		$varsEntityNation = $arr['varsItem']['varsEntityNation'];
		$array = $arr['varsDetail'];
		foreach ($array as $key => $value) {
			$str = 'str' . ucwords($value['id']);
			$arrayStr[$str] = $value['strTitle'];
			$strFlag = 'flag' . ucwords($value['id']);
			$arrayStr[$strFlag] = 1;

			$flag = 0;
			if ($value['id'] == 'IdAccountTitle') {
				$flag = 1;
				$value['arrayOption'] = $arr['varsItem']['arrAccountTitleFixedAssets']['arrSelectTag'];

			} elseif ($value['id'] == 'FlagDepMethod') {
				$str = $classEscape->toLower(array('str' => $value['id']));
				if ($arr['varsItem']['varsOptions'][$str]) {
					$value['arrayOption'] = $arr['varsItem']['varsOptions'][$str]['arrSelectTag'];
				}
				$flag = 1;

			} elseif ($value['id'] == 'IdDepartment') {
				if (count($arr['varsItem']['arrDepartment']['arrSelectTag']) == 1) {
					$flag = 0;
					$arrayStr[$strFlag] = 0;

				} else {
					$flag = 1;
					$value['arrayOption'] = $arr['varsItem']['arrDepartment']['arrSelectTag'];
				}

			} elseif (preg_match("/^Stamp/", $value['id'])) {
				$flag = 1;
				$value['varsFormCalender']['varsStatus']['stampMax'] = $arr['varsItem']['varsStampTerm']['stampMax']*1000;

			} elseif ($value['id'] == 'NumUsefulLife') {
				$flag = 1;
				$value['arrayOption'] = $arr['varsItem']['varsNumUsefulLife']['arrSelectTag'];

			} elseif ($value['id'] == 'StrDepMonths') {
				$flag = 1;
				$value['varsTmpl']['numMonths'] = $varsEntityNation['numFiscalTermMonth'];

			} elseif ($value['id'] == 'NumValueDepSpecialShortPrev'
				|| $value['id'] == 'NumValueDepPrevOver'
				|| $value['id'] == 'NumValueDepSpecialShortCurrent'
			) {
				if ($varsEntityNation['flagCorporation'] == 1) {
					$flag = 1;

				} else {
					$arrayStr[$strFlag] = 0;
				}

			} elseif ($value['id'] == 'ProductsCost' || $value['id'] == 'NumRatioProductsCost') {
				//個人一般
				if ($varsEntityNation['flagCorporation'] == 2) {
					if ($varsEntityNation['flagCR']) {
						$flag = 1;
					} else {
						$arrayStr[$strFlag] = 0;
					}

				//個人不動産
				} elseif ($varsEntityNation['flagCorporation'] == 3) {
					$arrayStr[$strFlag] = 0;

				//個人農業
				} elseif ($varsEntityNation['flagCorporation'] == 4) {
					$arrayStr[$strFlag] = 0;

				//法人
				} else {
					if ($varsEntityNation['flagCR']) {
						$flag = 1;
					} else {
						$arrayStr[$strFlag] = 0;
					}
				}

			} elseif ($value['id'] == 'NonOperatingExpenses' || $value['id'] == 'NumRatioNonOperatingExpenses') {
				//個人一般
				if ($varsEntityNation['flagCorporation'] == 2) {
					$arrayStr[$strFlag] = 0;

				//個人不動産
				} elseif ($varsEntityNation['flagCorporation'] == 3) {
					$arrayStr[$strFlag] = 0;

				//個人農業
				} elseif ($varsEntityNation['flagCorporation'] == 4) {
					$arrayStr[$strFlag] = 0;

				//法人
				} else {
					$flag = 1;
				}

			} elseif ($value['id'] == 'AgricultureCost' || $value['id'] == 'NumRatioAgricultureCost') {
				//個人一般
				if ($varsEntityNation['flagCorporation'] == 2) {
					$arrayStr[$strFlag] = 0;

				//個人不動産
				} elseif ($varsEntityNation['flagCorporation'] == 3) {
					$arrayStr[$strFlag] = 0;

				//個人農業
				} elseif ($varsEntityNation['flagCorporation'] == 4) {
					if ($varsEntityNation['flagCR']) {
						$flag = 1;
					} else {
						$arrayStr[$strFlag] = 0;
					}

				//法人
				} else {
					$arrayStr[$strFlag] = 0;

				}
			} elseif ($value['id'] == 'FlagFraction') {
				$str = $classEscape->toLower(array('str' => $value['id']));
				$value['arrayOption'] = $this->_getFlagFractionOption(array(
					'varsEntityNation' => $varsEntityNation,
					'arrayOption'      => $arr['varsItem']['varsOptions'][$str]['arrSelectTag'],
				));
				$flag = 1;

			} elseif ($value['id'] == 'NumRatioOperate' || $value['id'] == 'NumValueDepOperate') {
				if ($varsEntityNation['flagCorporation'] != 1) {
					$flag = 1;
				} else {
					$arrayStr[$strFlag] = 0;
				}

			} elseif ($value['id'] == 'NumSurvivalRate' || $value['id'] == 'NumSurvivalRateLimit') {
				$flag = 1;
				$arrayOption = array();
				for ($j = 0; $j <= 100; $j++) {
					$data = array(
						'strTitle' => $j . '%',
						'value' => $j,
					);
					$arrayOption[$j] = $data;
				}
				$value['arrayOption'] = $arrayOption;

			} else {
				$str = $classEscape->toLower(array('str' => $value['id']));
				if ($arr['varsItem']['varsOptions'][$str]) {
					$value['arrayOption'] = $arr['varsItem']['varsOptions'][$str]['arrSelectTag'];
				}
				$flag = 1;
			}

			if ($flag) {
				if ($value['id'] == 'LossOnDisposalOfFixedAssets'
					|| $value['id'] == 'AccumulatedDepreciation'
					|| $value['id'] == 'SellingAdminCost'
					|| $value['id'] == 'ProductsCost'
					|| $value['id'] == 'NonOperatingExpenses'
					|| $value['id'] == 'AgricultureCost'
				) {
					$value['arrayOption'] = $arr['varsItem']['arrAccountTitle']['arrSelectTag'];
				}

				$arrayNew[] = $value;
			}
		}

		$str = 'sumBasic';
		$arrayStr[$str] = 6;
		$array = array('flagIdDepartment');
		foreach ($array as $key => $value) {
			if (!$arrayStr[$value]) {
				$arrayStr[$str]--;
			}
		}

		$str = 'sumCurrentDep';
		$arrayStr[$str] = 16;
		$array = array('flagNumValueDepPrevOver', 'flagNumValueDepSpecialShortPrev', 'flagNumRatioOperate', 'NumValueDepOperate');
		foreach ($array as $key => $value) {
			if (!$arrayStr[$value]) {
				$arrayStr[$str]--;
			}
		}

		$str = 'sumWrite';
		$arrayStr[$str] = 6;
		$array = array('flagProductsCost', 'flagNonOperatingExpenses', 'flagAgricultureCost');
		foreach ($array as $key => $value) {
			if (!$arrayStr[$value]) {
				$arrayStr[$str]--;
			}
		}

		$str = 'sumRatio';
		$arrayStr[$str] = 5;
		$array = array('flagNumRatioProductsCost', 'flagNumRatioNonOperatingExpenses', 'flagNumRatioAgricultureCost');
		foreach ($array as $key => $value) {
			if (!$arrayStr[$value]) {
				$arrayStr[$str]--;
			}
		}

		$varsHtml = $this->_getHtml(array(
			'varsStr' => $arrayStr,
			'pathTpl' => $this->_extSelf['tplDetail']
		));

		$data = array(
			'varsDetail' => $arrayNew,
			'varsHtml'   => $varsHtml,
		);

		return $data;
	}

	/**
		(array(
			'varsStr' => $arrayStr
		))
	 */
	protected function _getHtml($arr)
	{
		global $classSmarty;

		$pathTpl = $arr['pathTpl'];

		$array = $arr['varsStr'];
		foreach ($array as $key => $value) {
			$classSmarty->assign($key, $value);
		}
		$path = $pathTpl;
		$data = $classSmarty->fetch($path);

		return $data;
	}

	/**
	 */
	protected function _getFlagFractionOption($arr)
	{
		$arrayNew = array();
		$array = $arr['arrayOption'];
		foreach ($array as $key => $value) {
			if ($value['value'] == 'numRatioSellingAdminCost') {
				$arrayNew[] = $value;

			} elseif ($value['value'] == 'numRatioNonOperatingExpenses') {
				if ($arr['varsEntityNation']['flagCorporation'] == 1) {
					$arrayNew[] = $value;
				}

			} elseif ($value['value'] == 'numRatioProductsCost') {
				if ($arr['varsEntityNation']['flagCorporation'] == 1
					|| $arr['varsEntityNation']['flagCorporation'] == 2
				) {
					if ($arr['varsEntityNation']['flagCR']) {
						$arrayNew[] = $value;
					}
				}

			} elseif ($value['value'] == 'numRatioAgricultureCost') {
				if ($arr['varsEntityNation']['flagCorporation'] == 4) {
					if ($arr['varsEntityNation']['flagCR']) {
						$arrayNew[] = $value;
					}
				}
			}

		}

		return $arrayNew;
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
		(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'rows'     => $rows,
		))
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
		$strCheckStamp = 'accountingLogFixedAssets_' . $idEntity . '_' . $numFiscalPeriod;

		$vars = $arr['vars'];
		$varsItem = $arr['varsItem'];
		$rows = $arr['rows'];

		$flagCurrent = 1;
		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done)$/", $flagCurrentFlagNow)) {
			$flagCurrent = 0;
		}

		$flagLogSelect = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'select',
			'idTarget'      => $this->_extSelf['idLog'],
		));
		$flagLogInsert = $this->_checkAccess(array(
			'flagAllUse'    => 0,
			'flagAuthority' => 'insert',
			'idTarget'      => $this->_extSelf['idLog'],
		));

		$varsAuthority = $this->_getVarsAuthority(array());

		$idAccount = $varsAccount['id'];

		$varsDetailId = array();
		$array = $vars['portal']['varsDetail']['templateDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'JsonDetail') {
				$arrayLoop = $value['varsFormSensitive']['varsTmpl']['varsDetail'];
				foreach ($arrayLoop as $keyLoop => $valueLoop) {
					$varsDetailId[$valueLoop['id']] = $valueLoop;
				}
				break;
			}
		}

		$array = $rows['arrRows'];
		$arrayNew = array();
		$num = 0;
		$flag = 0;
		foreach ($array as $key => $value) {
			$varsTmpl = $vars['portal']['varsList']['templateDetail'];
			$varsTmpl['id'] = $value['idFixedAssets'];
			$varsTmpl['vars']['idTarget'] = $value['idFixedAssets'];
			$varsTmpl['vars']['numFiscalPeriod'] = $value['numFiscalPeriod'];
			$varsTmpl['numSort'] = (int) $key;

			if ($varsAccount['jsonStampCheck'][$strCheckStamp] < $value['stampRegister']) {
				$flag = 1;

			} else {
				$varsTmpl['strClassLoad'] = '';
			}
			$varsTmpl['idAccount'] = $value['idAccount'];
			$varsTmpl['idAccountSelf'] = $idAccount;
			$varsTmpl['strTitle'] = ($value['strTitle'])? $value['strTitle'] : '';

			$varsTmpl['stampRegister'] = $value['stampRegister'];
			$varsTmpl['stampUpdate'] = $value['stampUpdate'];
			$varsTmpl['flagRemove'] = (int) $value['flagRemove'];
			$varsTmpl['stampRemove'] = $value['stampRemove'];

			$varsTmpl['jsonVersion'] = $this->_updateSearchJsonVersion(array(
				'vars'  => $vars,
				'value' => $value['jsonVersion'],
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

			$varsTmpl['jsonWriteHistory'] = '';
			$varsTmpl['vars']['jsonWriteHistory'] = array();
			if ($value['jsonWriteHistory']) {
				$tempData = $this->_getJsonWriteHistoryVarsDetail(array(
					'vars'    => $vars['varsItem']['varsJsonWriteHistory'],
					'value'   => $value['jsonWriteHistory'],
					'flagLog' => $flagLogSelect,
				));
				if (!$flagLogSelect) {
					$arrayColumndNew = array();
					$arrayColumnIdNew = array();
					$arrayColumn = $vars['varsItem']['varsJsonWriteHistory']['varsColumn'];
					$arrayColumnId = $vars['varsItem']['varsJsonWriteHistory']['varsStatus']['varsColumnId'];
					foreach ($arrayColumnId as $keyColumnId => $valueColumnId) {
						if ($valueColumnId == 'idLog') {
							continue;
						}
						$arrayColumndNew[] = $arrayColumn[$keyColumnId];
						$arrayColumnIdNew[] = $valueColumnId;
					}
					$vars['varsItem']['varsJsonWriteHistory']['varsStatus']['varsColumnId'] = $arrayColumnIdNew;
					$vars['varsItem']['varsJsonWriteHistory']['varsColumn'] = $arrayColumndNew;
				}
				$temp = $classHtml->allot(array(
					'strClass'    => 'TableSimple',
					'flagStatus'  => 'Html',
					'varsDetail'  => $tempData['varsDetail'],
					'varsColumn'  => $vars['varsItem']['varsJsonWriteHistory']['varsColumn'],
					'varsStatus'  => $vars['varsItem']['varsJsonWriteHistory']['varsStatus'],
				));
				$varsTmpl['jsonWriteHistory'] = $temp['strHtml'];
				$varsTmpl['vars']['jsonWriteHistory'] = $tempData['varsData'];
			}


			$varsTmpl['arrSpaceStrTag'] = preg_replace('/^ /', '', $value['arrSpaceStrTag']);
			$varsTmpl['strMemo'] = $value['strMemo'];

			$varsTmpl['flagBtnDelete'] = 0;
			$varsTmpl['flagBtnWrite'] = 0;
			$varsTmpl['flagBtnAdd'] = 0;
			$varsTmpl['flagBtnEdit'] = 0;
			$varsTmpl['flagCheckboxUse'] = 0;
			$varsTmpl['flagAdmin'] = ($varsAuthority == 'admin')? 1 : 0;
			$varsTmpl['flagCurrent'] = $flagCurrent;

			if ($flagCurrent) {
				if ($varsAuthority == 'admin') {
					if (!$varsTmpl['flagRemove']) {
						$varsTmpl['flagBtnDelete'] = 1;
						$varsTmpl['flagBtnEdit'] = 1;
						$varsTmpl['flagBtnWrite'] = 1;
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

						if ($flagLogInsert) {
							if (($value['idAccount'] == $varsAccount['id'] && $varsAuthority['flagMyInsert'])
								|| $varsAuthority['flagAllInsert']
							) {
								$varsTmpl['flagBtnWrite'] = 1;
							}
						}
					}

					if ($varsAuthority['flagMyInsert'] || $varsAuthority['flagAllInsert']) {
						$varsTmpl['flagBtnAdd'] = 1;
					}
				}

				if ($varsTmpl['flagBtnDelete'] || $varsTmpl['flagBtnWrite']) {
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

			$numWrite = count($varsTmpl['vars']['jsonWriteHistory']);
			if (!$numWrite) {
				$numWrite = '';
			}
			$varsTmpl['varsColumnDetail']['numWrite'] = $numWrite;
			$varsTmpl['varsColumnDetail']['strVersion'] = 'Ver.' . count($varsTmpl['jsonVersion']);
			$varsTmpl['varsColumnDetail']['stampRegister'] = $value['stampRegister'];
			$varsTmpl['varsColumnDetail']['stampUpdate'] = $value['stampUpdate'];

			$tempVars = $varsTmpl['jsonDetail']['jsonDetail'];

			$varsTmpl['varsColumnDetail']['stampStart'] = $tempVars['stampStart'];

			$arrayId = array(
				'numValue',
				'numValueDepCalcBase',
				'numValueDep',
				'numValueAccumulatedClosing',
				'numValueNetClosing',
			);
			foreach ($arrayId as $keyId => $valueId) {
				if ($tempVars[$valueId] === 0) {
					if (!$varsDetailId[ucwords($valueId)]['varsForm']['FlagDepMethod'][$tempVars['flagDepMethod']]) {
						$varsTmpl['varsColumnDetail'][$valueId] = '';
					} else {
						$varsTmpl['varsColumnDetail'][$valueId] = 0;
					}

				} elseif ($tempVars[$valueId] == '') {
					$varsTmpl['varsColumnDetail'][$valueId] = '';

				} else {
					if ($varsDetailId[ucwords($valueId)]['varsForm']['FlagDepMethod'][$tempVars['flagDepMethod']]) {
						$varsTmpl['varsColumnDetail'][$valueId] = number_format($tempVars[$valueId]);
					}
				}
			}

			$varsTmpl['varsColumnDetail']['flagDepMethod'] = '';
			if ($tempVars['flagDepMethod'] != 'none') {
				$varsTmpl['varsColumnDetail']['flagDepMethod'] = $varsItem['varsOptions']['flagDepMethod']['arrStrTitle'][$tempVars['flagDepMethod']];
			}

			$varsTmpl['varsColumnDetail']['numUsefulLife'] = '';
			if ($varsDetailId['NumUsefulLife']['varsForm']['FlagDepMethod'][$tempVars['flagDepMethod']]) {
				$varsTmpl['varsColumnDetail']['numUsefulLife'] = $tempVars['numUsefulLife'] . $vars['varsItem']['strYear'];
			}

			$varsTmpl['varsColumnDetail']['arrCommaDepMonth'] = '';
			$str = '';
			if ($tempVars['arrCommaDepMonth'] == '') {
				$str = 0;

			} else {
				$arrCommaDepMonth = $classEscape->splitCommaArrayData(array('data' => $tempVars['arrCommaDepMonth']));
				$numCommaDepMonth = count($arrCommaDepMonth);
				$str = $numCommaDepMonth;
			}

			if ($varsDetailId['ArrCommaDepMonth']['varsForm']['FlagDepMethod'][$tempVars['flagDepMethod']]) {
				$varsTmpl['varsColumnDetail']['arrCommaDepMonth'] = $str . '/' . $varsItem['varsEntityNation']['numFiscalTermMonth'];
			}

			if ($varsDetailId['NumRateDep']['varsForm']['FlagDepMethod'][$tempVars['flagDepMethod']]) {
				$varsTmpl['varsColumnDetail']['numRateDep'] = $tempVars['numRateDep'];
			}

			if ($value['flagRemove']) {
				$varsTmpl['strClassFont'] = $vars['varsItem']['strClassNone'];
				$varsTmpl['varsColumnDetail']['flagStatus'] = $vars['varsItem']['strRemoveFake'];

			} else {
				$varsTmpl['varsColumnDetail']['flagStatus'] = $vars['varsItem']['strDone'];
			}
			$varsTmpl['varsColumnDetail']['strTitle'] = $varsTmpl['strTitle'];

			$strCodeName = $varsAccounts[$value['idAccount']]['strCodeName'];
			if (!$strCodeName) {
				$strCodeName = $varsPluginAccountingAccountsId[$value['idAccount']]['strCodeName'];
			}
			$varsTmpl['varsColumnDetail']['idAccount'] = $strCodeName;

			$varsTmpl['vars']['id'] = $varsTmpl['id'];
			$varsTmpl['vars']['strTitle'] = $value['strTitle'];
			$varsTmpl['vars']['idAccountTitle'] = $value['idAccountTitle'];
			$varsTmpl['vars']['numValue'] = $value['numValue'];
			$varsTmpl['vars']['strMemo'] = $value['strMemo'];
			$varsTmpl['vars']['idAccount'] = $value['idAccount'];
			$varsTmpl['vars']['flagRemove'] = $value['flagRemove'];


			$varsTmpl['vars']['numVolume'] = $value['numVolume'];
			$varsTmpl['vars']['flagDepUnit'] = $value['flagDepUnit'];

			$varsTmpl['vars']['numRatioOperate'] = $value['numRatioOperate'];

			$varsTmpl['vars']['lossOnDisposalOfFixedAssets'] = $value['lossOnDisposalOfFixedAssets'];
			$varsTmpl['vars']['sellingAdminCost'] = $value['sellingAdminCost'];
			$varsTmpl['vars']['accumulatedDepreciation'] = $value['accumulatedDepreciation'];
			$varsTmpl['vars']['sellingAdminCost'] = $value['sellingAdminCost'];
			$varsTmpl['vars']['productsCost'] = $value['productsCost'];
			$varsTmpl['vars']['nonOperatingExpenses'] = $value['nonOperatingExpenses'];
			$varsTmpl['vars']['agricultureCost'] = $value['agricultureCost'];
			$varsTmpl['vars']['numRatioSellingAdminCost'] = $value['numRatioSellingAdminCost'];
			$varsTmpl['vars']['numRatioProductsCost'] = $value['numRatioProductsCost'];
			$varsTmpl['vars']['numRatioNonOperatingExpenses'] = $value['numRatioNonOperatingExpenses'];
			$varsTmpl['vars']['numRatioAgricultureCost'] = $value['numRatioAgricultureCost'];
			$varsTmpl['vars']['flagFraction'] = $value['flagFraction'];

			$varsTmpl['vars']['arrSpaceStrTag'] = $classEscape->splitSpaceArrayData(array('data' => $varsTmpl['arrSpaceStrTag']));

			$arrayColumnDetail = &$varsTmpl['varsColumnDetail'];
			foreach ($arrayColumnDetail as $keyColumnDetail => $valueColumnDetail) {
				if (is_null($valueColumnDetail)) {
					$arrayColumnDetail[$keyColumnDetail] = '';
				}
			}

			$varsTmpl['varsScheduleDetail']['stamp'] = $value['stampRegister'];
			$varsTmpl['varsScheduleDetail']['strTitle'] = ($varsTmpl['strTitle'])? $varsTmpl['strTitle'] : '-';
			$arrayNew[$num] = $varsTmpl;
			$num++;
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
		(array(
			'vars'  => array,
			'value' => array,
		))
	 */
	protected function _updateSearchJsonVersion($arr)
	{
		global $classCheck;
		global $classEscape;

		$array = $arr['value'];
		$arrayNew = array();
		$num = 1;
		foreach ($array as $key => $value) {
			$data = array();
			$data['stampRegister'] = $value['stampRegister'];
			$data['stampUpdate'] = $value['stampUpdate'];
			$data['strTitle'] = $value['strTitle'];
			$data['jsonDetail'] = $value['jsonDetail'];
			$data['strMemo'] = $value['strMemo'];
			$data['arrSpaceStrTag'] = preg_replace('/^ /', '', $value['arrSpaceStrTag']);
			$data['vars']['arrSpaceStrTag'] = $classEscape->splitSpaceArrayData(array('data' => $value['arrSpaceStrTag']));
			$data['strVersion'] = 'Ver.' . $num;
			$data['numVersion'] = $num;
			$arrayNew[] = $data;
			$num++;
		}

		return $arrayNew;
	}

	/**
		(array(

			'vars'  => $vars['varsItem']['varsJsonChargeHistory'],
			'value' => $value['jsonChargeHistory'],

		))
	 */
	protected function _getJsonWriteHistoryVarsDetail($arr)
	{
		$classTime = new Code_Else_Lib_Time();
		global $varsPluginAccountingAccount;


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
			$tmplDetail = $arr['tmplDetail'];
			$tmplDetail['id'] = $num;

			$strNo = $num;
			$tmplData = $arr['tmplData'];
			$tmplData['value'] = $strNo;
			$tmplDetail['varsDetail']['id'] = $tmplData;

			$classTime->setTimeZone(array('data' => $varsAccount['numTimeZone']));
			$strTime = $classTime->getDisplay(array(
				'stamp'    => $value['stampRegister'],
				'flagType' => 1,
			));
			$tmplData = $arr['tmplData'];
			$tmplData['value'] = $strTime;
			$tmplDetail['varsDetail']['stampRegister'] = $tmplData;

			$strCodeName = $varsAccounts[$value['idAccount']]['strCodeName'];
			if (!$varsAccounts[$value['idAccount']]['strCodeName']) {
				$strCodeName = $varsPluginAccountingAccountsId[$value['idAccount']]['strCodeName'];
			}
			$tmplData = $arr['tmplData'];
			$tmplData['value'] = $strCodeName;
			$tmplDetail['varsDetail']['idAccount'] = $tmplData;

			if ($arr['flagLog']) {
				$tmplData = $arr['tmplData'];
				$tmplData['value'] = $value['idLog'];
				$tmplDetail['varsDetail']['idLog'] = $tmplData;
			}

			$varsDetail[] = $tmplDetail;
			$tempVars = array();
			$tempVars['idAccount'] = $value['idAccount'];
			$tempVars['strCodeName'] = $strCodeName;
			if ($arr['flagLog']) {
				$tempVars['idLog'] = $value['idLog'];
			}
			$varsData[] = $tempVars;

			$num++;
		}

		$data = array(
			'varsDetail' => $varsDetail,
			'varsData'   => $varsData,
		);

		return $data;
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
			$tmplDetail = $arr['tmplDetail'];
			$tmplDetail['id'] = $num;

			$strNo = $num;
			$tmplData = $arr['tmplData'];
			$tmplData['value'] = $strNo;
			$tmplDetail['varsDetail']['id'] = $tmplData;

			$classTime->setTimeZone(array('data' => $varsAccount['numTimeZone']));
			$strTime = $classTime->getDisplay(array(
				'stamp'    => $value['stampRegister'],
				'flagType' => 1,
			));
			$tmplData = $arr['tmplData'];
			$tmplData['value'] = $strTime;
			$tmplDetail['varsDetail']['stampRegister'] = $tmplData;


			$strCodeName = $varsAccounts[$value['idAccount']]['strCodeName'];
			if (!$varsAccounts[$value['idAccount']]['strCodeName']) {
				$strCodeName = $varsPluginAccountingAccountsId[$value['idAccount']]['strCodeName'];
			}
			$tmplData = $arr['tmplData'];
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
			'idTarget' => '',
		))
	 */
	protected function _getVarsLog($arr)
	{
		global $classDb;
		global $varsRequest;

		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

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
				'strColumn'     => 'idFixedAssets',
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
			'strTable' => 'accountingLogFixedAssets' . $strNation,
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => $arrWhere,
		));

		$array = &$rows['arrRows'];
		foreach ($array as $key => $value) {
			if ($array[$key]['stampEnd'] === '0') {
				$array[$key]['stampEnd'] = '';
			}
			if ($array[$key]['stampDrop'] === '0') {
				$array[$key]['stampDrop'] = '';
			}
		}

		return $rows['arrRows'][0];
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

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingPreference['jsonStampUpdate']['logFixedAssets'],
				'flagSearch'  => 1,
			));
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
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
			'strTable'   => 'accountingLogFixedAssets' . $strNation,
			'arrJoin'    => array(),
			'arrOrder'   => $varsRequest['query']['jsonSearch']['ph']['arrOrder'],
			'insCurrent' => $this,
			'arrWhere'   => $varsRequest['query']['jsonSearch']['ph']['arrWhere'],
		));

		$vars = $this->_updateSearch(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'rows'     => $rows,
		));

		$this->sendVars(array(
			'flag'    => ($arr['flag'])? $arr['flag'] : 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'numRows'    => $rows['numRows'],
				'varsDetail' => $vars['portal']['varsList']['varsDetail'],
				'strComment' => ($arr['strComment'])? $arr['strComment'] : '',
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

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingPreference['jsonStampUpdate']['logFixedAssets'],
				'flagSearch'  => 1,
			));
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
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
			'strTable'   => 'accountingLogFixedAssets' . $strNation,
			'arrJoin'    => array(),
			'arrOrder'   => $varsRequest['query']['jsonSearch']['ph']['arrOrder'],
			'insCurrent' => $this,
			'arrWhere'   => $varsRequest['query']['jsonSearch']['ph']['arrWhere'],
		));

		$vars = $this->_updateSearch(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'rows'     => $rows,
		));

		$varsTarget = $vars;

		$rowsTarget = $this->getSearch(array(
			'idModule'  => 'accounting',
			'numLotNow' => 0,
			'strTable'   => 'accountingLogFixedAssets' . $strNation,
			'arrJoin'   => array(),
			'arrOrder'  => array(),
			'arrWhere'  => array(array(
				'flagType'      => 'num',
				'strColumn'     => 'idFixedAssets',
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
			'varsItem' => $varsItem,
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
				'idTarget'   => $varsRequest['query']['jsonValue']['idTarget'],
			),
		));
	}

	/**
	 *
	 */
	protected function _iniNaviSearchSave()
	{
		$this->_setNaviSearchSave(array(
			'pathVars'    => $this->_extSelf['pathVarsJs'],
			'strTable'    => 'accountingAccountMemo',
			'strColumn'   => 'jsonFixedAssetsNaviSearch',
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

		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
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
				$this->_updateDbPreferenceStamp(array('strColumn' => 'accounts'));
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
			'vars'    => $this->_getMemo(array(
				'strTable'    => $arr['strTable'],
				'strColumn'   => $arr['strColumn'],
				'flagEntity'  => $arr['flagEntity'],
				'flagAccount' => $arr['flagAccount'],
			)),
		));
	}

	/**
	 *
	 */
	protected function _iniNaviSearchDelete()
	{
		$this->_setNaviSearchDelete(array(
			'strTable'  => 'accountingAccountMemo',
			'strColumn' => 'jsonFixedAssetsNaviSearch',
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
			'strColumn' => 'jsonFixedAssetsNaviSearch',
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
		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
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
			'vars'    => $vars['portal']['varsNavi']['search']['varsDetail'],
		));
	}

	/**
	 *
	 */
	protected function _iniDetailDelete()
	{
		global $varsRequest;

		$this->_setDelete(array(
			'arrId' => array($varsRequest['query']['jsonValue']['idTarget']),
		));
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
		$dbh = $classDb->getHandle();
		global $classCheck;

		global $varsRequest;
		global $varsAccount;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (!$this->_checkCurrent()) {
			$this->_sendOldError();
		}

		$varsAuthority = $this->_getVarsAuthority(array());

		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllDelete'] || $varsAuthority['flagMyDelete'])) {
			$this->_sendOldError();
		}

		$array = $arr['arrId'];
		$flag = $classCheck->checkValueFormat(array(
			'flagType' => 'num',
			'flagArr'  => 1,
			'value'    => $array,
		));
		if ($flag) {
			$this->_sendOldError();
		}
		$arrVarsLog = array();
		foreach ($array as $key => $value) {
			$varsLog = $this->_getVarsLog(array('idTarget' => $value, 'flagRemove' => 0));
			if (!$varsLog) {
				$this->_sendOldError();

			} else {
				if (($varsAuthority != 'admin' && !$varsAuthority['flagAllDelete'] && $varsAuthority['flagMyDelete'])
					&& $varsLog['idAccount'] != $varsAccount['id']
				) {
					continue;
				}
			}
			$arrVarsLog[$value] = $varsLog;
		}
		if (!$arrVarsLog) {
			$this->_sendOldError();
		}

		$stampRemove = TIMESTAMP;
		$flagRemove = 1;

		try {
			$dbh->beginTransaction();

			$arrayNew = array();
			foreach ($array as $key => $value) {
				$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
				$classDb->updateRow(array(
					'idModule'  => 'accounting',
					'strTable'  => 'accountingLogFixedAssets' . $strNation,
					'arrColumn' => array('stampRemove', 'flagRemove'),
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
							'value'         => $numFiscalPeriod,
						),
						array(
							'flagType'      => 'num',
							'strColumn'     => 'idFixedAssets',
							'flagCondition' => 'eq',
							'value'         => $value,
						),
					),
					'arrValue'  => array($stampRemove, $flagRemove),
				));

				if ($value['flagDepMethod'] == 'sum') {
					$this->_updateDbFixedAssets(array(
						'numFiscalPeriod' => $numFiscalPeriod,
					));
				}

				if ($flagCurrentFlagNow == 'tempPrev') {
					$numFiscalPeriod++;
					$classDb->deleteRow(array(
						'idModule'  => 'accounting',
						'strTable'  => 'accountingLogFixedAssets' . $strNation,
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
								'value'         => $numFiscalPeriod,
							),
							array(
								'flagType'      => 'num',
								'strColumn'     => 'idFixedAssets',
								'flagCondition' => 'eq',
								'value'         => $value,
							),
						),
					));
					if ($value['flagDepMethod'] == 'sum') {
						$this->_updateDbFixedAssets(array(
							'numFiscalPeriod' => $numFiscalPeriod,
						));
					}
				}
			}
			$this->_updateDbPreferenceStamp(array('strColumn' => 'logFixedAssets'));
			$this->_updateDbPreferenceStamp(array('strColumn' => 'fixedAssets'));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}
		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
		$this->_setSearch(array('flag' => 1));
	}

	/**
	 *
	 */
	protected function _iniDetailWrite()
	{
		$this->_setClassExt(array('strClass' => 'FixedAssetsWrite'));
	}

	/**
	 *
	 */
	protected function _iniListWrite()
	{
		$this->_setClassExt(array('strClass' => 'FixedAssetsWrite'));
	}

	/**
	 *
	 */
	protected function _iniListOutput()
	{
		$this->_setClassExt(array('strClass' => 'FixedAssetsOutput'));
	}
	/**
	 *
	 */
	protected function _iniDetailOutput()
	{
		$this->_setClassExt(array('strClass' => 'FixedAssetsOutput'));
	}

	/**
	 *
	 */
	protected function _iniListPrint()
	{
		$this->_setClassExt(array('strClass' => 'FixedAssetsOutput'));
	}
	/**
	 *
	 */
	protected function _iniDetailPrint()
	{
		$this->_setClassExt(array('strClass' => 'FixedAssetsOutput'));
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

	 */
	protected function _updateDbFixedAssets($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$numFiscalPeriod = $arr['numFiscalPeriod'];
		$varsFixedAssets = $this->_getVarsFixedAssets(array(
			'numFiscalPeriod' => $numFiscalPeriod,
		));

		if (!$varsFixedAssets['jsonDepSum']) {
			$varsFixedAssets['jsonDepSum'] = array();
		}
		$varsValue = $varsFixedAssets['jsonDepSum'][$numFiscalPeriod];
		if (!$varsValue) {
			$varsValue = array();
		}

		$classCall = $this->_getClassCalcFixedAssets();
		$varsDepSum = $classCall->allot((array(
			'flagStatus'      => 'start',
			'flagDepMethod'   => 'sum',
			'arrValue'        => $varsValue,
			'numFiscalPeriod' => $numFiscalPeriod,
		)));
		$jsonDepSum = json_encode($varsDepSum);
		$arrDbColumn = array('jsonDepSum');
		$arrDbValue = array($jsonDepSum);

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable' => 'accountingFixedAssets' . $strNation,
			'arrColumn' => $arrDbColumn,
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
					'value'         => $numFiscalPeriod,
				),
			),
			'arrValue'  => $arrDbValue,
		));
	}

	/**
	 *
	 */
	protected function _getClassCalcFixedAssets()
	{
		$str = 'CalcDep';
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

		return $classCall;
	}

	/**
		(array(
			'flagType' => 'LogCalc'
		))
	 */
	protected function _getClassPreference($arr)
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$strNation = PLUGIN_ACCOUNTING_STR_NATION;

		$strFile = ucwords($arr['flagType']);
		require_once(PATH_BACK_CLASS_ELSE_PLUGIN . $this->_self['strTitle'] . '/' . $strNation . '/Portal.php');
		require_once(PATH_BACK_CLASS_ELSE_PLUGIN . $this->_self['strTitle'] . '/' . $strNation . '/portal/' . $strFile .'.php');
		$strNation = ucwords($strNation);
		$strClass = 'Code_Else_Plugin_Accounting_' . $strNation .'_Portal_' . $strFile;
		$classCall = new $strClass();

		return $classCall;
	}

	/**
		(array(
			'flagType' => 'LogCalc'
		))
	 */
	protected function _getDetailLogTarget($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);
		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogFixedAssets' . $strNation,
			'arrLimit' => array(),
			'arrOrder'  => array(
				'strColumn' => 'numFiscalPeriod',
				'flagDesc'  => 0,
			),
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
					'flagCondition' => 'eqSmall',
					'value'         =>  $arr['numFiscalPeriod'],
				),
				array(
					'flagType'      => '',
					'strColumn'     => 'idFixedAssets',
					'flagCondition' => 'eq',
					'value'         => $arr['idTarget'],
				),
			),
		));

		$array = &$rows['arrRows'];
		foreach ($array as $key => $value) {
			if ($array[$key]['stampEnd'] === '0' || is_null($array[$key]['stampEnd'])) {
				$array[$key]['stampEnd'] = '';
			}
			if ($array[$key]['stampDrop'] === '0' || is_null($array[$key]['stampDrop'])) {
				$array[$key]['stampDrop'] = '';
			}
		}

		return $rows;
	}



}
