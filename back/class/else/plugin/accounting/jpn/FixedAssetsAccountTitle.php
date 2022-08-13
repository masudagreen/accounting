<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_FixedAssetsAccountTitle extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference'    => 'fixedAssetsWindow',
		'pathTplJs'       => 'else/plugin/accounting/js/jpn/fixedAssetsAccountTitle.js',
		'tplDetail'       => 'else/plugin/accounting/html/fixedAssetsAccountTitle.html',
		'pathVarsJs'      => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/fixedAssetsAccountTitle.php',
		'varsDefault'     => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/fixedAssetsAccountTitle.php',
		'varsDefaultElse' => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/fixedAssetsAccountTitleElse.php',
		'varsOption'      => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/depreciation.php',
	);

	/**
	 *
	 */
	public function run()
	{
		$this->_checkCorporationClass(array('flagChild' => 0));

		global $varsRequest;

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
		global $classSmarty;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'idTarget' => $this->_extendSelf['idFixedAssets'],
		));

		$vars = $this->_updateVars(array(
			'idTarget' => $this->_extendSelf['idFixedAssets'],
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
		global $varsPluginAccountingAccount;

		$varsDefault = $this->getVars(array(
			'path' => $this->_extSelf['varsDefault'],
		));

		$varsDefaultElse = $this->getVars(array(
			'path' => $this->_extSelf['varsDefaultElse'],
		));

		$varsFixedAssets = $this->_getVarsFixedAssets(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent']
		));
		$varsFixedAssets = $varsFixedAssets['jsonAccountTitle'];

		$arrAccountTitle = $this->_getAccountTitle(array(
			'vars'               => $this->_getVarsFSItem(),
			'arrSubAccountTitle' => array(),
			'numFiscalPeriod'    => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsTree = $this->_getTree(array(
			'idTarget'        => $this->_extendSelf['idFixedAssets'],
			'varsDefault'     => $varsDefault,
			'varsDefaultElse' => $varsDefaultElse,
			'varsFixedAssets' => $varsFixedAssets,
			'arrAccountTitle' => $arrAccountTitle,
		));

		$arrAccountTitle['arrStrTitle']['none'] = $arr['vars']['varsItem']['strNone'];

		$data = array('strTitle' => $arr['vars']['varsItem']['strNone'], 'value' => 'none');
		array_unshift($arrAccountTitle['arrSelectTag'], $data);

		$varsOptions = $this->_getVarsOptions();

		$data = array(
			'idTarget'        => $this->_extendSelf['idFixedAssets'],
			'varsDefault'     => $varsDefault,
			'varsDefaultElse' => $varsDefaultElse,
			'varsFixedAssets' => $varsFixedAssets,
			'varsOptions'     => $varsOptions,
			'varsTree'        => $varsTree,
			'arrAccountTitle' => $arrAccountTitle,
		);

		return $data;
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
			'idTarget' => $arr['idTarget'],
		));

		$varsBlock = $this->_setTreeBlock(array(
			'vars'            => $varsBlock,
			'varsDefault'     => $arr['varsDefault'],
			'varsDefaultElse' => $arr['varsDefaultElse'],
			'varsFixedAssets' => $arr['varsFixedAssets'],
			'arrAccountTitle' => $arr['arrAccountTitle'],
		));

		return $varsBlock;
	}

	/**
		(array(

		))
	 */
	protected function _setTreeBlock($arr)
	{
		$array = &$arr['vars'];
		foreach ($array as $key => $value) {
			if (!is_null($value['vars']['flagUse'])) {
				if ($value['vars']['flagDebit'] == 1) {
					$idTarget = $value['vars']['idTarget'];
					if ($arr['varsDefault'][$idTarget]) {
						$array[$key]['vars']['varsFixedAssets'] = $arr['varsDefault'][$idTarget];
					}
					if ($arr['varsFixedAssets'][$idTarget]) {
						$array[$key]['vars']['varsFixedAssets'] = $arr['varsFixedAssets'][$idTarget];
					}
					if (!$arr['varsDefault'][$idTarget] && !$arr['varsFixedAssets'][$idTarget]) {
						$idParent = $arr['idParent'];
						$array[$key]['vars']['varsFixedAssets'] = $arr['varsDefaultElse'][$idParent];
					}

					if ($arr['varsDefault'][$idTarget]) {
						$array[$key]['vars']['varsFixedAssetsIni'] = $arr['varsDefault'][$idTarget];
					} else {
						$idParent = $arr['idParent'];
						$array[$key]['vars']['varsFixedAssetsIni'] = $arr['varsDefaultElse'][$idParent];
					}

					$arrayStr = array('lossOnDisposalOfFixedAssets', 'accumulatedDepreciation', 'sellingAdminCost', 'productsCost', 'nonOperatingExpenses', 'agricultureCost');
					foreach ($arrayStr as $keyStr => $valueStr) {
						$str = $array[$key]['vars']['varsFixedAssets'][$valueStr];
						if (!$arr['arrAccountTitle']['arrStrTitle'][$str]) {
							$array[$key]['vars']['varsFixedAssets'][$valueStr] = 'none';
						}
						$str = $array[$key]['vars']['varsFixedAssetsIni'][$valueStr];
						if (!$arr['arrAccountTitle']['arrStrTitle'][$str]) {
							$array[$key]['vars']['varsFixedAssetsIni'][$valueStr] = 'none';
						}
					}

					$arrayStr = array('numRatioSellingAdminCost', 'numRatioProductsCost', 'numRatioNonOperatingExpenses', 'numRatioAgricultureCost');
					foreach ($arrayStr as $keyStr => $valueStr) {
						$data = $array[$key]['vars']['varsFixedAssets'][$valueStr];
						$array[$key]['vars']['varsFixedAssets'][$valueStr] = sprintf("%.2f", $data);

						$data = $array[$key]['vars']['varsFixedAssetsIni'][$valueStr];
						$array[$key]['vars']['varsFixedAssetsIni'][$valueStr] = sprintf("%.2f", $data);
					}
				}
			}

			if ($value['child']) {
				$array[$key]['child'] = $this->_setTreeBlock(array(
					'vars'            => $array[$key]['child'],
					'idParent'        => $value['vars']['idTarget'],
					'varsDefault'     => $arr['varsDefault'],
					'varsDefaultElse' => $arr['varsDefaultElse'],
					'varsFixedAssets' => $arr['varsFixedAssets'],
					'arrAccountTitle' => $arr['arrAccountTitle'],
				));
			}
		}

		return $array;
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
			'idTarget' => $vars['idTarget'],
			'vars'       => $vars,
			'varsItem'   => $varsItem,
		))
	 */
	protected function _updateVars($arr)
	{
		global $classDb;

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
		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^done$/", $flagCurrentFlagNow)) {
			$vars['portal']['varsDetail']['varsBtn'] = array();
			$vars['portal']['varsDetail']['view']['varsEdit'] = array();
		}

		$vars['portal']['varsDetail']['templateDetail'] = $this->_updateVarsTemplateDetail((array(
			'vars'              => $vars,
			'varsEntityNation'  => $varsEntityNation,
			'varsItem'          => $arr['varsItem'],
			'varsAccountTitle'  => $arr['varsItem']['arrAccountTitle']['arrSelectTag'],
		)));

		$vars['portal']['varsList']['varsDetail'] = $this->_updateVarsList(array(
			'vars'             => $vars,
			'varsFS'           => $arr['varsItem']['varsTree'],
			'varsItem'         => $arr['varsItem'],
			'varsAuthority'    => array(
				'flagUpdate' => ($varsAuthority['flagAllUpdate'])? 1 : 0,
			),
			'varsEntityNation' => $varsEntityNation,
			'varsAccountTitle' => $arr['varsItem']['arrAccountTitle']['arrStrTitle'],
		));

		return $vars;
	}

	/**
		(array(
			'vars'              => $vars,
			'varsEntityNation'  => $varsEntityNation,
			'varsItem'          => $arr['varsItem'],
		))
	 */
	protected function _updateVarsTemplateDetail($arr)
	{
		global $classEscape;

		$vars = &$arr['vars'];

		$arrayNew = array();
		$array = $vars['portal']['varsDetail']['templateDetail'];
		foreach ($array as $key => $value) {
			$flag = 0;
			if ($value['id'] == 'SellingAdminCost' || $value['id'] == 'NumRatioSellingAdminCost') {
				//個人一般
				if ($arr['varsEntityNation']['flagCorporation'] == 2) {
					$flag = 1;

				//個人不動産
				} elseif ($arr['varsEntityNation']['flagCorporation'] == 3) {
					$flag = 1;

				//個人農業
				} elseif ($arr['varsEntityNation']['flagCorporation'] == 4) {

				//法人
				} else {
					$flag = 1;
				}

			} elseif ($value['id'] == 'ProductsCost' || $value['id'] == 'NumRatioProductsCost') {
				//個人一般
				if ($arr['varsEntityNation']['flagCorporation'] == 2) {
					if ($arr['varsEntityNation']['flagCR']) {
						$flag = 1;
					}

				//個人不動産
				} elseif ($arr['varsEntityNation']['flagCorporation'] == 3) {

				//個人農業
				} elseif ($arr['varsEntityNation']['flagCorporation'] == 4) {

				//法人
				} else {
					if ($arr['varsEntityNation']['flagCR']) {
						$flag = 1;
					}
				}

			} elseif ($value['id'] == 'NonOperatingExpenses' || $value['id'] == 'NumRatioNonOperatingExpenses') {
				//個人一般
				if ($arr['varsEntityNation']['flagCorporation'] == 2) {

				//個人不動産
				} elseif ($arr['varsEntityNation']['flagCorporation'] == 3) {

				//個人農業
				} elseif ($arr['varsEntityNation']['flagCorporation'] == 4) {

				//法人
				} else {
					$flag = 1;
				}

			} elseif ($value['id'] == 'AgricultureCost' || $value['id'] == 'NumRatioAgricultureCost') {
				//個人一般
				if ($arr['varsEntityNation']['flagCorporation'] == 2) {

				//個人不動産
				} elseif ($arr['varsEntityNation']['flagCorporation'] == 3) {

				//個人農業
				} elseif ($arr['varsEntityNation']['flagCorporation'] == 4) {
					if ($arr['varsEntityNation']['flagCR']) {
						$flag = 1;
					}

				//法人
				} else {

				}
			} elseif ($value['id'] == 'FlagFraction') {
				$str = $classEscape->toLower(array('str' => $value['id']));
				$value['arrayOption'] = $this->_getFlagFractionOption(array(
					'varsEntityNation' => $arr['varsEntityNation'],
					'arrayOption'      => $arr['varsItem']['varsOptions'][$str]['arrSelectTag'],
				));
				$flag = 1;

			} elseif ($value['id'] == 'NumValueRemainingBook') {
				$flag = 1;

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
					$value['arrayOption'] = $arr['varsAccountTitle'];
				}
				$arrayNew[] = $value;
			}

		}

		return $arrayNew;
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
		(array(
			'vars'             => $vars,
			'varsFS'           => $arr['varsItem']['varsTree'],
			'varsItem'         => $arr['varsItem'],
			'varsAuthority'    => array(
				'flagUpdate' => ($varsAuthority['flagAllUpdate'])? 1 : 0,
			),
			'varsEntityNation' => $varsEntityNation,
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
			$array[$key]['vars']['varsAuthority'] = $arr['varsAuthority'];
			$array[$key]['varsHtml'] = array();

			if ($value['vars']['flagDebit'] != 1
				|| $value['vars']['flagCalc']
				|| $value['vars']['idTarget'] == 'accumulatedDepreciation'
				|| $value['vars']['idTarget'] == 'allowanceForBadDebtsLongTermOther'
			) {
				$array[$key]['flagBtnUse'] = 0;
				$array[$key]['flagMoveUse'] = 0;
				$array[$key]['strClassFont'] = $arr['vars']['varsItem']['strClassNone'];
			}

			if (!is_null($value['vars']['flagUse'])) {
				if ($value['vars']['flagDebit'] == 1) {
					$array[$key]['varsHtml'] = $this->_getHtml(array(
						'varsDetail'       => $arr['vars']['portal']['varsDetail']['templateDetail'],
						'varsHtml'         => $arr['vars']['varsItem']['varsHtml'],
						'varsEntityNation' => $arr['varsEntityNation'],
						'varsFixedAssets'  => $value['vars']['varsFixedAssets'],
						'varsOptions'      => $arr['varsItem']['varsOptions'],
						'varsAccountTitle' => $arr['varsAccountTitle'],
					));
				}
			}

			if ($value['child']) {
				$array[$key]['child'] = $this->_updateVarsList(array(
					'vars'             => $arr['vars'],
					'varsFS'           => $array[$key]['child'],
					'varsItem'         => $arr['varsItem'],
					'varsAuthority'    => $arr['varsAuthority'],
					'varsEntityNation' => $arr['varsEntityNation'],
					'varsAccountTitle' => $arr['varsAccountTitle'],
				));
			}
		}

		return $array;
	}


	/**
		(array(
			'varsHtml'         => $arr['vars']['varsItem']['varsHtml'],
			'varsEntityNation' => $arr['varsEntityNation'],
			'varsFixedAssets'  => $arr['varsFixedAssets'],
			'varsOptions'  => $arr['varsItem']['varsOptions'],
			'varsAccountTitle'  => $arr['varsAccountTitle'],
		))
	 */
	protected function _getHtml($arr)
	{
		global $classSmarty;

		$pathTpl = $this->_extSelf['tplDetail'];

		$vars = $arr['varsHtml'];

		$array = $arr['varsAccountTitle'];
		foreach ($array as $key => $value) {
			$varsAccountTitle[$key] = $value['strTitleFS'];
		}

		$vars['flagCorporation'] = $arr['varsEntityNation']['flagCorporation'];
		$vars['flagCR'] = $arr['varsEntityNation']['flagCR'];
		$array = $arr['varsFixedAssets'];
		foreach ($array as $key => $value) {
			if (!preg_match("/^num/", $key)) {
				$arrStrTitle = $arr['varsOptions'][$key]['arrStrTitle'];
				$flag = $arr['varsFixedAssets'][$key];
				$vars[$key . 'Str'] = $arrStrTitle[$flag];
				$vars[$key] = $flag;

				if ($varsAccountTitle[$flag]) {
					$vars[$key . 'Str'] = $varsAccountTitle[$flag];
					$vars[$key] = $flag;

				}

			} else {
				if ($key == 'numSurvivalRate' || $key == 'numSurvivalRateLimit' || $key == 'numValueRemainingBook') {
					$vars[$key] = $value;
					$vars[$key . 'Str'] = $value;

				} else {
					$vars[$key] = sprintf("%.2f", $value);
					$vars[$key . 'Str'] = sprintf("%.2f", $value);
				}
			}
		}

		if ($arr['varsFixedAssets']['flagTaxFixed'] == 'none'
			 || $arr['varsFixedAssets']['flagTaxFixed'] == 'free'
		) {
			$vars['flagTaxFixedTypeStr'] = '';
		}
		if ($arr['varsFixedAssets']['flagTaxFixed'] == 'none') {
			$vars['flagTaxFixedStr'] = $arr['varsHtml']['flagNone'];
		}

		$array = $arr['varsDetail'];
		foreach ($array as $key => $value) {
			$str = 'flag' . $value['id'];
			$vars[$str] = 1;
		}

		$str = 'sumWrite';
		$vars[$str] = 6;
		$array = array('flagProductsCost', 'flagNonOperatingExpenses', 'flagAgricultureCost');
		foreach ($array as $key => $value) {
			if (!$vars[$value]) {
				$vars[$str]--;
			}
		}

		$str = 'sumRatio';
		$vars[$str] = 5;
		$array = array('flagNumRatioProductsCost', 'flagNumRatioNonOperatingExpenses', 'flagNumRatioAgricultureCost');
		foreach ($array as $key => $value) {
			if (!$vars[$value]) {
				$vars[$str]--;
			}
		}


		$array = $vars;
		foreach ($array as $key => $value) {
			$classSmarty->assign($key, $value);
		}
		$path = $pathTpl;
		$data = $classSmarty->fetch($path);

		return $data;
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

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'idTarget' => $this->_extendSelf['idFixedAssets'],
		));

		$vars = $this->_updateVars(array(
			'idTarget' => $this->_extendSelf['idFixedAssets'],
			'vars'       => $vars,
			'varsItem'   => $varsItem,
		));

		$this->sendVars(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'varsDetail'     => $vars['portal']['varsList']['varsDetail'],
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
	protected function _checkValue($arr)
	{
		$array = $arr['vars'];
		$flag = 0;
		foreach ($array as $key => $value) {
			if ($value['value'] == $arr['idTarget']) {
				$flag = 1;
			}
		}
		if (!$flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}

		return $arr['idTarget'];
	}

	/**
	 *
	 */
	protected function _iniListReload()
	{
		$this->_setSearch();
	}


}
