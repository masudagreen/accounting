<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_Balance extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference'    => 'balanceWindow',
		'pathTplJs'       => 'else/plugin/accounting/js/jpn/balance.js',
		'pathVarsJs'      => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/balance.php',
		'tplTable'        => 'else/plugin/accounting/html/balance.html',
		'tplTableItem'    => 'else/plugin/accounting/html/balanceItem.html',
		'tplTableSub'     => 'else/plugin/accounting/html/balanceSub.html',
		'tplTableItemSub' => 'else/plugin/accounting/html/balanceSubItem.html',
	);

	/**
	 *
	 */
	public function run()
	{
		$this->_checkCorporationClass(array('flagChild' => 0));

		global $varsRequest;
		global $classCheck;
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
		global $classSmarty;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsFlag' => $vars['varsFlag'],
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$vars['portal']['varsList']['templateDetailEditor'] = $this->_updateVarsList((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$vars['portal']['varsDetail']['varsDetail'] = $this->_updateVarsDetail((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $vars['varsFlag'],
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
			'vars'             => $vars,
			'varsItem'         => $arr['varsItem'],
		))
	 */
	protected function _updateVarsList($arr)
	{
		$vars = &$arr['vars'];
		$varsItem = &$arr['varsItem'];

		$arrayNew = array();
		$array = &$vars['portal']['varsList']['templateDetailEditor'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'JsonData') {
				$method = '_updateVarsList' . $value['id'];
				$value = $this->$method(array(
					'vars'     => $value,
					'varsItem' => $varsItem,
				));
			}
			$arrayNew[] = $value;
		}
		$vars['portal']['varsList']['templateDetailEditor'] = $arrayNew;


		return $vars['portal']['varsList']['templateDetailEditor'];
	}

	/**
		(array(
			'vars'             => $vars,
			'varsItem'         => $arr['varsItem'],
		))
	 */
	protected function _updateVarsDetail($arr)
	{
		$vars = &$arr['vars'];
		$varsItem = &$arr['varsItem'];

		$arrayNew = array();
		$array = &$vars['portal']['varsDetail']['templateDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'JsonData') {
				$method = '_updateVarsDetail' . $value['id'];
				$value = $this->$method(array(
					'vars'     => $value,
					'varsItem' => $varsItem,
				));

			}
			$arrayNew[] = $value;
		}
		$vars['portal']['varsDetail']['templateDetail'] = $arrayNew;


		return $vars['portal']['varsDetail']['templateDetail'];
	}

	/**
		(array(
			'vars'             => $value,
			'varsItem'         => $varsItem,
		))
	 */
	protected function _updateVarsDetailJsonData($arr)
	{
		$arr['vars']['varsFormSensitive']['varsTmpl']['tmplTable'] = $this->_getHtml(array(
			'varsStr' => $arr['vars']['varsStr'],
			'pathTpl' => $this->_extSelf['tplTableSub'],
		));

		$arr['vars']['varsFormSensitive']['varsTmpl']['tmplTableItem'] = $this->_getHtml(array(
			'varsStr' => array(),
			'pathTpl' => $this->_extSelf['tplTableItemSub'],
		));

		return $arr['vars'];
	}

	/**
		(array(
			'vars'             => $value,
			'varsItem'         => $varsItem,
		))
	 */
	protected function _updateVarsListJsonData($arr)
	{
		$arr['vars']['varsFormSensitive']['varsTmpl']['tmplTable'] = $this->_getHtml(array(
			'varsStr' => $arr['vars']['varsStr'],
			'pathTpl' => $this->_extSelf['tplTable'],
		));

		$arr['vars']['varsFormSensitive']['varsTmpl']['tmplTableItem'] = $this->_getHtml(array(
			'varsStr' => array(),
			'pathTpl' => $this->_extSelf['tplTableItem'],
		));

		return $arr['vars'];
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
		(array(
			'flag' => '',
			'vars' => $vars,
		))
	 */
	protected function _getVarsItem($arr)
	{
		global $varsPluginAccountingAccount;

		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsFSValue = $this->_getVarsFSValue(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		if ($arr['varsFlag']['idDepartment'] != 0) {
			$varsFSValue = $this->_getVarsFSValueDepartment(array(
				'idDepartment'    => $arr['varsFlag']['idDepartment'],
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			));
		}

		$varsDepartment = $this->_getVarsDepartment(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$this->_getVarsNone(array(
			'varsTarget' => &$varsDepartment,
			'strValue'   => 0,
			'strNone'    => $arr['vars']['varsItem']['strNone'],
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrSubAccountTitle = $this->_getVarsSubAccountTitle(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrAccountTitle = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'numFiscalPeriod'    => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsSubValue = $this->_getVarsSubValue(array(
			'varsDepartment'     => $varsDepartment,
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'numFiscalPeriod'    => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$data = array(
			'varsFS'                  => $varsFS['jsonJgaapAccountTitleBS'],
			'varsFSValue'             => $varsFSValue['jsonJgaapAccountTitleBS'],
			'varsSubValue'            => $varsSubValue,
			'varsDepartment'          => $varsDepartment,
			'varsEntityNation'        => $varsEntityNation,
			'arrAccountTitle'         => $arrAccountTitle,
			'arrSubAccountTitle'      => $arrSubAccountTitle,
		);

		return $data;

	}

	/**
		(array(
			'numFiscalPeriod'   => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idDepartment'      => $arr['varsFlag']['idDepartment'],
			'idAccountTitle'    => $arr['varsFlag']['idAccountTitle'],
			'idSubAccountTitle' => '',
		))
	 */
	protected function _getVarsSubValue($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule'    => 'accounting',
			'strTable'    => 'accountingSubAccountTitleValue' . $strNation,
			'arrJoin'     => '',
			'arrLimit'    => array(),
			'arrOrder'  => array(),
			'arrWhere'    => array(
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
					'value'         => $arr['numFiscalPeriod'],
				),
			),
			'flagAnd'     => 1,
		));

		if (!$rows['numRows']) {
			return array();
		}

		$arrayNew = array();
		$array = $rows['arrRows'];
		foreach ($array as $key => $value) {
			$idSubAccountTitle = $value['idSubAccountTitle'];
			$arrayData = $value['jsonData'];
			foreach ($arrayData as $keyData => $valueData) {
				$idDepartment = $keyData;
				$numValue = $valueData['f1']['sumPrev'];
				if (is_null($numValue)) {
					$numValue = 0;
				}
				$arrayNew[$idSubAccountTitle][$idDepartment]['numValue'] = $numValue;
				$arrayNew[$idSubAccountTitle][$idDepartment]['numValueComma'] = number_format($numValue);
			}
		}

		return $arrayNew;
	}

	/**
		(array(
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'numFiscalPeriod'    => $numFiscalPeriod,
		))
	 */
	protected function _getAccountTitle($arr)
	{
		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$arrayFSList = $this->_getFSList(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$strCR = $this->_getStrCR(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$arrStrTitleVarsDetail = array();
		$arrStrTitle = array();
		$arrStrTitles = array();
		$arrSelectTag = array();
		$arrSelectTags = array();

		$arrSubAccountStrTitles = array();
		$arrSubAccountSelectTag = array();
		$arrSubAccountSelectTags = array();

		$array = $arrayFSList;
		foreach ($array as $key => $value) {
			$str = 'jsonJgaapAccountTitle'. $key;
			$varsFS[$str] = $this->_setTreeId(array(
				'idParent' => '-',
				'vars'     => $varsFS[$str],
			));

			$varsAccountTitle = $this->_getArrSelectOption(array(
				'arrStrTitle'  => array(),
				'arrSelectTag' => array(),
				'vars'         => $varsFS[$str],
				'flagBS'       => ($key == 'BS')? 1 : 0,
				'flagFS'       => $key,
				'strCR'        => $strCR,
			));

			$arrSelectTags[$key] = $varsAccountTitle['arrSelectTag'];

			$arrStrTitle = array_merge($arrStrTitle, $varsAccountTitle['arrStrTitle']);
			$arrStrTitles[$key] = $varsAccountTitle['arrStrTitle'];

			if ($arr['arrSubAccountTitle']) {
				$varsSubAccountTitle = $this->_getAccountTitleSubAccountTitle(array(
					'arrSubAccountTitle' => $arr['arrSubAccountTitle'],
					'arrSelectTag'       => array(),
					'arrStrTitle'        => array(),
					'vars'               => $varsFS[$str],
					'flagFS'             => $key,
					'strCR'              => $strCR,
				));
				$arrSubAccountSelectTag = array_merge($arrSubAccountSelectTag, $varsSubAccountTitle['arrSelectTag']);
				$arrSubAccountSelectTags[$key] = $varsSubAccountTitle['arrSelectTag'];
				$arrSubAccountStrTitles[$key] = $varsSubAccountTitle['arrStrTitle'];
			}

			$dataStrTitleVarsDetail = array(
				'strTitle'     => $value,
				'value'        => 'dummy' . $key,
				'flagDisabled' => 1,
			);
			$arrStrTitleVarsDetail[] = $dataStrTitleVarsDetail;
			$arrStrTitleVarsDetail = array_merge($arrStrTitleVarsDetail, $varsAccountTitle['arrSelectTag']);
			$arrSelectTag = $arrStrTitleVarsDetail;
		}

		$data = array(
			'arrStrTitle'             => $arrStrTitle,
			'arrStrTitles'            => $arrStrTitles,
			'arrSelectTag'            => $arrSelectTag,
			'arrSelectTags'           => $arrSelectTags,
			'arrSubAccountStrTitles'  => $arrSubAccountStrTitles,
			'arrSubAccountSelectTag'  => $arrSubAccountSelectTag,
			'arrSubAccountSelectTags' => $arrSubAccountSelectTags,
		);

		return $data;
	}



	/**
		(array(
			'vars'         => array(),
			'arrSelectTag' => array(),
			'arrSubAccountTitle' => array(),
		))
	 */
	protected function _getAccountTitleSubAccountTitle($arr)
	{
		$arrSelectTag = &$arr['arrSelectTag'];
		$arrStrTitle = &$arr['arrStrTitle'];

		$array = &$arr['vars'];
		foreach ($array as $key => $value) {
			$strTitleFS = ($arr['flagFS'] == 'CR')? '['. $arr['strCR']  .']' . $value['strTitle'] : $value['strTitle'];

			$arrLevel = preg_split("/-/", $value['id']);
			$num = count($arrLevel) - 2;
			$arrLevel = array();
			for ($i = 0 ; $i < $num; $i++) {
				$arrLevel[$i] = '';
			}
			$str =  ' ' . join('.', $arrLevel) . ' ';
			$strTitle = $str . $value['strTitle'];
			$strTitleFSTag = $str . $strTitleFS;
			$strTitleTree = $strTitleFS;
			$numSub = count($arr['arrSubAccountTitle']['arrStrTitle'][$value['vars']['idTarget']]);
			if (is_null($value['vars']['flagUse'])) {
				$arr['arrSelectTag'][] = array(
					'strTitle'     => $strTitle,
					'value'        => '',
					'flagDisabled' => 1,
				);

			} else {
				$strTitleFSTag .= '['. count($arr['arrSubAccountTitle']['arrStrTitle'][$value['vars']['idTarget']]) .']';
				$arr['arrSelectTag'][] = array(
					'strTitle'     => $strTitleFSTag,
					'value'        => $value['vars']['idTarget'],
				);
				$strTitleTree = $strTitleFS . '['. $numSub .']';
			}

			$data = array(
				'strTitle'      => $value['strTitle'],
				'strTitleFS'    => $strTitleFS,
				'strTitleTree'  => $strTitleTree,
				'flagFS'        => $arr['flagFS'],
				'numSub'        => $numSub,
			);
			$arr['arrStrTitle'][$value['vars']['idTarget']] = $data;

			if ($value['child']) {
				$data = $this->_getAccountTitleSubAccountTitle(array(
					'vars'               => $array[$key]['child'],
					'arrSelectTag'       => $arr['arrSelectTag'],
					'arrStrTitle'        => $arr['arrStrTitle'],
					'arrSubAccountTitle' => $arr['arrSubAccountTitle'],
					'flagFS'             => $arr['flagFS'],
					'strCR'              => $arr['strCR'],
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
			'vars'             => $vars,
			'varsItem'         => $arr['varsItem'],
		))
	 */
	protected function _updateVarsNavi($arr)
	{
		$vars = &$arr['vars'];
		$varsItem = &$arr['varsItem'];

		$arrayNew = array();
		$array = &$vars['portal']['varsNavi']['templateDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'IdDepartment') {
				$value['arrayOption'] = $arr['varsItem']['varsDepartment']['arrSelectTag'];
				/*
				$value['numSize'] = count($value['arrayOption']);
				if ($value['numSize'] > 10) {
					$value['numSize'] = 10;
				}
				*/
			}
			$arrayNew[] = $value;
		}
		$vars['portal']['varsNavi']['templateDetail'] = $arrayNew;


		return $vars['portal']['varsNavi']['templateDetail'];
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
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		))
	 */
	protected function _updateVars($arr)
	{
		global $classHtml;

		global $varsAccount;

		if ($arr['varsFlag']['idDepartment'] != 0) {
			$varsDepartmentTreeItem = $this->_getVarsDepartmentTreeItem();
			$arrayNew = array();
			$array = $arr['varsItem']['varsFS'];
			foreach ($array as $key => $value) {
				$arrayNew[] = $value;
				if ($value['vars']['idTarget'] == 'netAssetsSum') {
					$arrayNew[] = $varsDepartmentTreeItem;
				}
			}
			$arr['varsItem']['varsFS'] = $arrayNew;
		}

		$arr['vars']['varsItem']['varsSubValue'] = $arr['varsItem']['varsSubValue'];
		$arr['vars']['varsItem']['arrSubAccountTitle'] = $arr['varsItem']['arrSubAccountTitle'];
		$arr['vars']['varsItem']['arrAccountTitle'] = $arr['varsItem']['arrAccountTitle'];


		$arr['vars']['portal']['varsList']['varsDetail'] = $this->_getAccountTitleValueColumn(array(
			'varsFS'      => $arr['varsItem']['varsFS'],
			'varsFSValue' => $arr['varsItem']['varsFSValue'],
			'varsItem'    => $arr['varsItem'],
		));

		$arr['vars']['portal']['varsList']['varsIni'] = $this->_getAccountTitleValueColumn(array(
			'varsFS'      => $arr['varsItem']['varsFS'],
			'varsItem'    => $arr['varsItem'],
			'varsFSValue' => array(),
		));

		$varsAuthority = $this->_getVarsAuthority(array());

		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'])) {
			$arr['vars']['portal']['varsList']['varsStart']['varsEdit']['flagEditUse'] = 0;
			$arr['vars']['portal']['varsDetail']['view']['varsEdit']['flagEditUse'] = 0;
		}

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done)$/", $flagCurrentFlagNow)) {
			$arr['vars']['portal']['varsList']['varsStart']['varsEdit']['flagEditUse'] = 0;
			$arr['vars']['portal']['varsDetail']['view']['varsEdit']['flagEditUse'] = 0;
		}

		$varsDetail = $this->_setTreeId(array(
			'idParent' => '-',
			'vars'     => $arr['vars']['portal']['varsList']['varsDetail'],
		));
		$arr['vars']['portal']['varsList']['varsDetail'] = $varsDetail;

		$varsIni = $this->_setTreeId(array(
			'idParent' => '-',
			'vars'     => $arr['vars']['portal']['varsList']['varsIni'],
		));
		$arr['vars']['portal']['varsList']['varsIni'] = $varsIni;

		$varsTemp = $classHtml->allot(array(
			'strClass'    => 'TableTree',
			'flagStatus'  => 'Html',
			'numTimeZone' => $varsAccount['numTimeZone'],
			'varsDetail'  => $varsDetail,
			'varsColumn'  => $arr['vars']['portal']['varsList']['tableTree']['varsDetail']['varsColumn'],
			'varsStatus'  => $arr['vars']['portal']['varsList']['tableTree']['varsDetail']['varsStatus'],
		));

		$arr['vars']['portal']['varsList']['varsHtml'] = $varsTemp['strHtml'];

		return $arr['vars'];
	}

	/**
		(array(
			'varsFS'                 => $arr['varsItem']['varsFS'],
			'varsFSValue'            => $arr['varsItem']['varsFSValue'],
		))

	 */
	protected function _getAccountTitleValueColumn($arr)
	{
		$array = &$arr['varsFS'];

		foreach ($array as $key => $value) {
			$array[$key]['varsValue']['numBalance'] = '';
			$array[$key]['varsColumnDetail']['numBalance'] = '';
			//$array[$key]['flagBtnUse'] = 0;

			if (!is_null($array[$key]['vars']['varsValue'])) {

				$idTarget = $value['vars']['idTarget'];

				//numBalance
				$numBalance = $arr['varsFSValue']['f1'][$idTarget]['sumPrev'];
				if (is_null($numBalance)) {
					$numBalance = 0;
				}
				$cut = &$arr['varsItem']['arrAccountTitle']['arrSubAccountStrTitles']['BS'][$idTarget];
				//$array[$key]['flagBtnUse'] = 1;
				if ($idTarget == 'profitBroughtForward'
					|| $idTarget == 'suspenseReceiptOfConsumptionTaxes'
					|| $idTarget == 'suspensePaymentConsumptionTaxes'
				) {
					$array[$key]['flagBtnUse'] = 0;
				}
				$array[$key]['vars']['idAccountTitle'] = $idTarget;
				$array[$key]['strTitle'] = ($idTarget == 'departmentNet')? $array[$key]['strTitle'] : $cut['strTitleTree'];
				$array[$key]['varsValue']['numBalance'] = $numBalance;
				$array[$key]['varsColumnDetail']['numBalance'] = number_format($numBalance);
			}

			if ($value['child']) {
				$array[$key]['child'] = $this->_getAccountTitleValueColumn(array(
					'varsFS'      => $array[$key]['child'],
					'varsFSValue' => $arr['varsFSValue'],
					'varsItem'    => $arr['varsItem'],
					'idParent'    => $idTarget,
				));
			}
		}

		return $array;
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
		global $varsPluginAccountingAccount;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsFlag = array(
			'idDepartment' => $varsRequest['query']['jsonValue']['vars']['IdDepartment'],
		);

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$vars['portal']['varsList']['templateDetailEditor'] = $this->_updateVarsList((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$vars['portal']['varsDetail']['varsDetail'] = $this->_updateVarsDetail((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		if (!$varsItem['varsDepartment']['arrStrTitle'][$varsFlag['idDepartment']]) {
			$varsFlag['idDepartment'] = 0;
		}

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		));

		$this->sendVars(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'varsDetail' => $vars['portal']['varsList']['varsDetail'],
				'varsIni'    => $vars['portal']['varsList']['varsIni'],
				'varsHtml'   => $vars['portal']['varsList']['varsHtml'],
				'varsColumn' => $vars['portal']['varsList']['tableTree']['varsDetail']['varsColumn'],
				'varsFlag'   => $varsFlag,
			),
		));
	}

	/**
	 *
	 */
	protected function _iniListReload()
	{
		$this->_setSearch();
	}
}
