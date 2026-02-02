<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_TrialBalance extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference' => 'trialBalanceWindow',
		'idLedger' => 'ledgerWindow',
		'pathTplJs' => 'else/plugin/accounting/js/jpn/trialBalance.js',
		'pathVarsJs' => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/trialBalance.php',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;
		global $classCheck;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$flag = $this->_checkAccess(array(
			'flagAllUse' => 1,
			'flagAuthority' => 'select',
			'idTarget' => $this->_extSelf['idPreference'],
		));

		if (!$flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' . __FUNCTION__ . '/' . __LINE__);
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
					var_dump(__CLASS__ . '/' . __FUNCTION__ . '/' . __LINE__);
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
						var_dump(__CLASS__ . '/' . __FUNCTION__ . '/' . __LINE__);
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
		global $varsPluginAccountingAccount;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
			'varsFlag' => $vars['varsFlag'],
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars' => &$vars,
			'varsItem' => $varsItem,
		)));

		$vars = $this->_updateVars(array(
			'vars' => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $vars['varsFlag'],
		));

		$vars['flagAuthorityLedger'] = $this->_checkAccess(array(
			'flagAllUse' => 1,
			'flagAuthority' => 'select',
			'idTarget' => $this->_extSelf['idLedger'],
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

		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsFSValue = $this->_getVarsFSValue(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsDepartment = $this->_getVarsDepartment(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		if ($arr['varsFlag']['idDepartment'] != 'none') {
			$varsFSValue = $this->_getVarsFSValueDepartment(array(
				'idDepartment' => $arr['varsFlag']['idDepartment'],
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			));
		}

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));



		$arrSubAccountTitle = $this->_getVarsSubAccountTitle(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrAccountTitle = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$flagFiscalPeriod = $arr['varsFlag']['flagFiscalPeriod'];
		$numAssetsSum = $varsFSValue['jsonJgaapAccountTitleBS'][$flagFiscalPeriod]['assetsSum']['sumNext'];
		if (is_null($numAssetsSum) || $numAssetsSum === '') {
			$numAssetsSum = 0;
		}
		$numNetSales = $varsFSValue['jsonJgaapAccountTitlePL'][$flagFiscalPeriod]['netSales']['sumNext'];
		if (is_null($numNetSales) || $numNetSales === '') {
			$numNetSales = 0;
		}

		$varsSubValue = $this->_getVarsSubValue(array(
			'varsFlag' => $arr['varsFlag'],
			'varsDepartment' => $varsDepartment,
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'varsFSValue' => $varsFSValue,
			'numAssetsSum' => $numAssetsSum,
			'numNetSales' => $numNetSales,
		));

		$data = array(
			'varsFS' => $varsFS,
			'varsFSValue' => $varsFSValue,
			'varsSubValue' => $varsSubValue,
			'varsDepartment' => $varsDepartment,
			'varsEntityNation' => $varsEntityNation,
			'arrAccountTitle' => $arrAccountTitle,
			'arrSubAccountTitle' => $arrSubAccountTitle,
		);

		return $data;
	}

	/**
		(array(
			'varsFlag'           => $varsFlag,
			'varsDepartment'     => $varsDepartment,
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'numFiscalPeriod'    => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'numAssetsSum'       => $numAssetsSum,
			'numNetSales'        => $numNetSales,
		))
	 */
	protected function _getVarsSubValue($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingSubAccountTitleValue' . $strNation,
			'arrJoin' => '',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'arrWhere' => array(
				array(
					'flagType' => 'num',
					'strColumn' => 'idEntity',
					'flagCondition' => 'eq',
					'value' => $varsPluginAccountingAccount['idEntityCurrent'],
				),
				array(
					'flagType' => 'num',
					'strColumn' => 'numFiscalPeriod',
					'flagCondition' => 'eq',
					'value' => $arr['numFiscalPeriod'],
				),
			),
			'flagAnd' => 1,
		));

		if (!$rows['numRows']) {
			return array();
		}

		$arrSubAccountIdAccountTitle = array();
		$array = $arr['arrSubAccountTitle']['arrStrTitle'];
		foreach ($array as $key => $value) {
			$idAccountTitle = $key;
			$arraySub = $value;
			foreach ($arraySub as $keySub => $valueSub) {
				$idSubAccountTitle = $keySub;
				$arrSubAccountIdAccountTitle[$idSubAccountTitle] = $idAccountTitle;
			}
		}

		$flagFS = $arr['varsFlag']['flagFS'];
		$flagFiscalPeriod = $arr['varsFlag']['flagFiscalPeriod'];
		$flagCalc = $arr['varsFlag']['flagCalc'];
		$flagUnit = $arr['varsFlag']['flagUnit'];
		$arrayValue = array('sumPrev', 'sumDebit', 'sumCredit', 'sumNext', 'numRate');

		$varsSum = array();
		$arrayNew = array();
		$array = $rows['arrRows'];
		foreach ($array as $key => $value) {
			$idSubAccountTitle = $value['idSubAccountTitle'];
			$arrayData = $value['jsonData'];
			foreach ($arrayData as $keyData => $valueData) {
				$idDepartment = $keyData;
				if ($arr['varsFlag']['idDepartment'] != $idDepartment) {

				}
				foreach ($arrayValue as $keyValue => $valueValue) {
					if ($valueValue == 'numRate') {
						continue;
					}
					$numData = $valueData[$flagFiscalPeriod][$valueValue];
					$numValue = $this->_getValueData(array(
						'num' => $numData,
						'flagUnit' => $flagUnit,
						'flagCalc' => $flagCalc,
					));

					$flag = 0;
					if ($arr['varsFlag']['flagFS'] == 'BS') {
						$flag = 1;

					} else {
						if ($valueValue != 'sumPrev') {
							$flag = 1;
						}
					}
					$varsSum[$idSubAccountTitle][$idDepartment][$valueValue] += $numValue;

					if ($flag) {
						$arrayNew[$idSubAccountTitle][$idDepartment][$valueValue] = $numValue;
						$arrayNew[$idSubAccountTitle][$idDepartment][$valueValue . 'Comma'] = number_format($numValue);

					} else {
						$arrayNew[$idSubAccountTitle][$idDepartment][$valueValue] = '-';
						$arrayNew[$idSubAccountTitle][$idDepartment][$valueValue . 'Comma'] = '-';
					}
				}
			}
		}


		foreach ($array as $key => $value) {
			$idSubAccountTitle = $value['idSubAccountTitle'];
			$idAccountTitle = $arrSubAccountIdAccountTitle[$idSubAccountTitle];
			$numDataAll = $arr['numNetSales'];
			if ($flagFS == 'BS') {
				$numDataAll = $arr['numAssetsSum'];
			}
			//$numDataAll = $arr['varsFSValue']['jsonJgaapAccountTitle' . $flagFS][$flagFiscalPeriod][$idAccountTitle]['sumNext'];
			$numValueAll = $this->_getValueData(array(
				'num' => $numDataAll,
				'flagUnit' => $flagUnit,
				'flagCalc' => $flagCalc,
			));
			$arrayData = $value['jsonData'];
			foreach ($arrayData as $keyData => $valueData) {
				$idDepartment = $keyData;
				$numRate = 0;

				$numRate = ($numValueAll == 0) ? 0 : $arrayNew[$idSubAccountTitle][$idDepartment]['sumNext'] / $numValueAll;
				$numRate *= 100;
				$arrayNew[$idSubAccountTitle][$idDepartment]['numRate'] = number_format($numRate, 3);
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
			$str = 'jsonJgaapAccountTitle' . $key;
			$varsFS[$str] = $this->_setTreeId(array(
				'idParent' => '-',
				'vars' => $varsFS[$str],
			));

			$varsAccountTitle = $this->_getArrSelectOption(array(
				'arrStrTitle' => array(),
				'arrSelectTag' => array(),
				'vars' => $varsFS[$str],
				'flagBS' => ($key == 'BS') ? 1 : 0,
				'flagFS' => $key,
				'strCR' => $strCR,
			));

			$arrSelectTags[$key] = $varsAccountTitle['arrSelectTag'];

			$arrStrTitle = array_merge($arrStrTitle, $varsAccountTitle['arrStrTitle']);
			$arrStrTitles[$key] = $varsAccountTitle['arrStrTitle'];

			if ($arr['arrSubAccountTitle']) {
				$varsSubAccountTitle = $this->_getAccountTitleSubAccountTitle(array(
					'arrSubAccountTitle' => $arr['arrSubAccountTitle'],
					'arrSelectTag' => array(),
					'arrStrTitle' => array(),
					'vars' => $varsFS[$str],
					'flagFS' => $key,
					'strCR' => $strCR,
				));
				$arrSubAccountSelectTag = array_merge($arrSubAccountSelectTag, $varsSubAccountTitle['arrSelectTag']);
				$arrSubAccountSelectTags[$key] = $varsSubAccountTitle['arrSelectTag'];
				$arrSubAccountStrTitles[$key] = $varsSubAccountTitle['arrStrTitle'];
			}

			$dataStrTitleVarsDetail = array(
				'strTitle' => $value,
				'value' => 'dummy' . $key,
				'flagDisabled' => 1,
			);
			$arrStrTitleVarsDetail[] = $dataStrTitleVarsDetail;
			$arrStrTitleVarsDetail = array_merge($arrStrTitleVarsDetail, $varsAccountTitle['arrSelectTag']);
			$arrSelectTag = $arrStrTitleVarsDetail;
		}

		$data = array(
			'arrStrTitle' => $arrStrTitle,
			'arrStrTitles' => $arrStrTitles,
			'arrSelectTag' => $arrSelectTag,
			'arrSelectTags' => $arrSelectTags,
			'arrSubAccountStrTitles' => $arrSubAccountStrTitles,
			'arrSubAccountSelectTag' => $arrSubAccountSelectTag,
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
			$strTitleFS = ($arr['flagFS'] == 'CR') ? '[' . $arr['strCR'] . ']' . $value['strTitle'] : $value['strTitle'];

			$arrLevel = preg_split("/-/", $value['id']);
			$num = count($arrLevel) - 2;
			$arrLevel = array();
			for ($i = 0; $i < $num; $i++) {
				$arrLevel[$i] = '';
			}
			$str = ' ' . join('.', $arrLevel) . ' ';
			$strTitle = $str . $value['strTitle'];
			$strTitleFSTag = $str . $strTitleFS;
			$strTitleTree = $strTitleFS;
			$numSub = 0;
			if (!empty($arr['arrSubAccountTitle']['arrStrTitle'][$value['vars']['idTarget']])) {
				$numSub = count($arr['arrSubAccountTitle']['arrStrTitle'][$value['vars']['idTarget']]);
			}
			if (is_null($value['vars']['flagUse'])) {
				$arr['arrSelectTag'][] = array(
					'strTitle' => $strTitle,
					'value' => '',
					'flagDisabled' => 1,
				);

			} else {
				$strTitleFSTag .= '[' . $numSub . ']';
				$arr['arrSelectTag'][] = array(
					'strTitle' => $strTitleFSTag,
					'value' => $value['vars']['idTarget'],
				);
				$strTitleTree = $strTitleFS . '[' . $numSub . ']';
			}

			$data = array(
				'strTitle' => $value['strTitle'],
				'strTitleFS' => $strTitleFS,
				'strTitleTree' => $strTitleTree,
				'flagFS' => $arr['flagFS'],
				'numSub' => $numSub,
			);
			$arr['arrStrTitle'][$value['vars']['idTarget']] = $data;

			if ($value['child']) {
				$data = $this->_getAccountTitleSubAccountTitle(array(
					'vars' => $array[$key]['child'],
					'arrSelectTag' => $arr['arrSelectTag'],
					'arrStrTitle' => $arr['arrStrTitle'],
					'arrSubAccountTitle' => $arr['arrSubAccountTitle'],
					'flagFS' => $arr['flagFS'],
					'strCR' => $arr['strCR'],
				));
				$array[$key]['child'] = $data['vars'];
				$arrSelectTag = $data['arrSelectTag'];
				$arrStrTitle = $data['arrStrTitle'];
			}
		}

		return $arr;
	}

	/**
		(array(
			'vars'             => $vars,
			'varsItem'         => $arr['varsItem'],
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _updateVarsNavi($arr)
	{
		$vars = &$arr['vars'];
		$varsEntityNation = &$arr['varsItem']['varsEntityNation'];
		$varsItem = &$arr['varsItem'];

		$arrayNew = array();
		$array = &$vars['portal']['varsNavi']['templateDetail'];
		foreach ($array as $key => $value) {
			if (
				$value['id'] == 'FlagFiscalPeriod'
				|| $value['id'] == 'FlagFS'
				|| $value['id'] == 'IdDepartment'
			) {
				$method = '_updateVarsNavi' . $value['id'];
				$value = $this->$method(array(
					'vars' => $value,
					'varsItem' => $varsItem,
					'varsEntityNation' => $varsEntityNation,
				));

			}
			$arrayNew[] = $value;
		}
		$vars['portal']['varsNavi']['templateDetail'] = $arrayNew;


		return $vars['portal']['varsNavi']['templateDetail'];
	}

	/**
		(array(
			'value'    => $value,
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		))
	 */
	protected function _updateVarsNaviIdDepartment($arr)
	{
		if (!$arr['varsItem']['varsDepartment']['arrSelectTag']) {
			return $arr['vars'];
		}
		$arrSelectTag = $arr['varsItem']['varsDepartment']['arrSelectTag'];
		array_unshift($arrSelectTag, $arr['vars']['varsTmpl']['varsNone']);
		$arr['vars']['arrayOption'] = $arrSelectTag;

		return $arr['vars'];
	}


	/**
		(array(
			'vars'             => $value,
			'varsItem'         => $varsItem,
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _updateVarsNaviFlagFS($arr)
	{
		$vars = $arr['vars'];
		$varsEntityNation = $arr['varsEntityNation'];

		$arrayNew = array();
		$array = $vars['arrayOption'];
		foreach ($array as $key => $value) {
			if ($value['value'] == 'CR' && !$varsEntityNation['flagCR']) {

			} else {
				$arrayNew[] = $value;
			}
		}
		$arr['vars']['arrayOption'] = $arrayNew;

		return $arr['vars'];
	}

	/**
		(array(
			'vars'             => $value,
			'varsItem'         => $varsItem,
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _updateVarsNaviFlagFiscalPeriod($arr)
	{
		$varsEntityNation = $arr['varsEntityNation'];

		$arrayNew = array();
		$array = $arr['vars']['varsTmpl']['arrayOption'];
		foreach ($array as $key => $value) {
			if ($varsEntityNation['numFiscalTermMonth'] == 12) {
				$arrayNew[] = $value;

			} else {
				if (preg_match("/^(f1)$/", $value['value'])) {
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
				'value' => $numMonth,
			);
			$arrayOption[] = $data;
			$numMonth++;
			if ($numMonth > 12) {
				$numMonth = 1;
			}
		}

		$arr['vars']['arrayOption'] = $arrayOption;
		$arr['vars']['numSize'] = count($arrayOption);

		return $arr['vars'];
	}

	/**
		(array(
			'vars'             => $vars,
			'varsEntityNation' => $varsEntityNation,
			'varsItem'         => $varsItem,
			'varsFlagFS'       => $varsFlagFS,
			'varsFlag'         => array(
				'flagFiscalPeriod'  => $flagFiscalPeriod,
				'idDepartment'      => $idDepartment,
				'flagFS'            => $flagFS,
				'flagUnit'          => $flagUnit,
				'flagCalc'          => $flagCalc,
			),
		))
	 */
	protected function _updateVars($arr)
	{
		global $classHtml;

		global $varsAccount;

		$varsFS = $arr['varsItem']['varsFS']['jsonJgaapAccountTitle' . $arr['varsFlag']['flagFS']];

		if ($arr['varsFlag']['flagFS'] == 'BS' && $arr['varsFlag']['idDepartment'] != 'none') {
			$varsDepartmentTreeItem = $this->_getVarsDepartmentTreeItem();
			$arrayNew = array();
			$array = $varsFS;
			foreach ($array as $key => $value) {
				$arrayNew[] = $value;
				if ($value['vars']['idTarget'] == 'netAssetsSum') {
					$arrayNew[] = $varsDepartmentTreeItem;
				}
			}
			$varsFS = $arrayNew;
		}

		$arr['vars']['varsItem']['varsSubValue'] = $arr['varsItem']['varsSubValue'];
		$arr['vars']['varsItem']['arrSubAccountTitle'] = $arr['varsItem']['arrSubAccountTitle'];

		$arr['vars']['portal']['varsList']['tableTree']['varsDetail']['varsColumn'] = $this->_updateAccountTitleValueColumn(array(
			'vars' => $arr['vars']['portal']['varsList']['tableTree']['varsDetail']['varsColumn'],
			'varsFlag' => $arr['varsFlag'],
			'varsItem' => $arr['varsItem'],
		));

		$flagFiscalPeriod = $arr['varsFlag']['flagFiscalPeriod'];

		$numAssetsSum = $arr['varsItem']['varsFSValue']['jsonJgaapAccountTitleBS'][$flagFiscalPeriod]['assetsSum']['sumNext'];
		if (is_null($numAssetsSum) || $numAssetsSum === '') {
			$numAssetsSum = 0;
		}
		$numNetSales = $arr['varsItem']['varsFSValue']['jsonJgaapAccountTitlePL'][$flagFiscalPeriod]['netSales']['sumNext'];
		if (is_null($numNetSales) || $numNetSales === '') {
			$numNetSales = 0;
		}

		$arr['vars']['portal']['varsList']['varsDetail'] = $this->_getAccountTitleValueColumn(array(
			'vars' => $arr['vars'],
			'varsItem' => $arr['varsItem'],
			'varsFS' => $varsFS,
			'varsFSValue' => $arr['varsItem']['varsFSValue']['jsonJgaapAccountTitle' . $arr['varsFlag']['flagFS']],
			'varsFlag' => $arr['varsFlag'],
			'numAssetsSum' => $numAssetsSum,
			'numNetSales' => $numNetSales,
		));

		if (!$arr['varsFlag']['flagZero']) {
			$arr['vars']['portal']['varsList']['varsDetail'] = $this->_getAccountTitleValueZero(array(
				'varsFS' => $arr['vars']['portal']['varsList']['varsDetail'],
				'varsFSNew' => array(),
			));
		}


		$varsDetail = $this->_setTreeId(array(
			'idParent' => '-',
			'vars' => $arr['vars']['portal']['varsList']['varsDetail'],
		));

		$arr['vars']['portal']['varsList']['varsDetail'] = $varsDetail;

		$varsTemp = $classHtml->allot(array(
			'strClass' => 'TableTree',
			'flagStatus' => 'Html',
			'numTimeZone' => $varsAccount['numTimeZone'],
			'varsDetail' => $varsDetail,
			'varsColumn' => $arr['vars']['portal']['varsList']['tableTree']['varsDetail']['varsColumn'],
			'varsStatus' => $arr['vars']['portal']['varsList']['tableTree']['varsDetail']['varsStatus'],
		));
		$arr['vars']['portal']['varsList']['varsHtml'] = $varsTemp['strHtml'];

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllOutput'])) {
			$arr['vars']['portal']['varsList']['varsStart']['varsEdit']['flagOutputUse'] = 0;
			$arr['vars']['portal']['varsList']['varsStart']['varsEdit']['flagPrintUse'] = 0;
		}

		return $arr['vars'];
	}

	/**
		(array(

		))
	 */
	protected function _getAccountTitleValueZero($arr)
	{
		$array = &$arr['varsFS'];
		foreach ($array as $key => $value) {
			$flag = 0;
			if (!is_null($array[$key]['vars']['varsValue']) && is_null($array[$key]['vars']['flagCalc'])) {
				if (
					$array[$key]['vars']['varsValue']['sumDebit'] == 0
					&& $array[$key]['vars']['varsValue']['sumCredit'] == 0
					&& $array[$key]['vars']['varsValue']['sumNext'] == 0
					&& $array[$key]['vars']['idTarget'] != 'commonStock'
					&& $array[$key]['vars']['idTarget'] != 'profitBroughtForward'
					&& $array[$key]['vars']['idTarget'] != 'netIncome'
				) {
					$flag = 1;
				}
				if ($flag) {
					unset($array[$key]);
				}
			}

			if ($array[$key]['child']) {
				$array[$key]['child'] = $this->_getAccountTitleValueZero(array(
					'varsFS' => $array[$key]['child'],
				));
			}
		}

		$arrayTemp = array();
		foreach ($array as $key => $value) {
			$arrayTemp[] = $value;
		}
		$array = $arrayTemp;

		return $array;
	}


	/**
	 (array(

	 ))
	 */
	protected function _updateAccountTitleValueColumn($arr)
	{
		$numMonth = $arr['varsItem']['varsEntityNation']['numFiscalBeginningMonth'];

		$array = &$arr['vars'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'sumPrev') {
				if (
					!preg_match("/^(f)/", $arr['varsFlag']['flagFiscalPeriod'])
					&& $numMonth != $arr['varsFlag']['flagFiscalPeriod']
				) {
					$array[$key]['strTitle'] = $value['strTitleMonth'];
				}
			} elseif ($value['id'] == 'numRate') {
				if ($arr['varsFlag']['flagFS'] == 'BS') {
					$array[$key]['strTitle'] = $value['strTitleBS'];
				}
			}
		}

		return $arr['vars'];
	}

	/**
		(array(
			'varsFS'      => $arr['varsItem']['varsFS'],
			'varsFSValue' => $arr['varsItem']['varsFSValue'],
			'varsFlag'         => array(
				'flagFiscalPeriod'  => $flagFiscalPeriod,
				'idDepartment'      => $idDepartment,
				'flagFS'            => $flagFS,
				'flagUnit'          => $flagUnit,
				'flagCalc'          => $flagCalc,
			),
		))

	 */
	protected function _getAccountTitleValueColumn($arr)
	{
		global $classDisplay;

		$array = &$arr['varsFS'];

		$flagUnit = (int) $arr['varsFlag']['flagUnit'];
		$flagFiscalPeriod = $arr['varsFlag']['flagFiscalPeriod'];
		$flagCalc = $arr['varsFlag']['flagCalc'];

		$varsFSValue = $arr['varsFSValue'];
		$arrayValue = array('sumPrev', 'sumDebit', 'sumCredit', 'sumNext', 'numRate');

		foreach ($array as $key => $value) {
			foreach ($arrayValue as $keyValue => $valueValue) {
				$array[$key]['varsColumnDetail'][$valueValue] = '';
				$array[$key]['varsPrint'][$valueValue] = '';
			}
			$array[$key]['varsPrint']['strTitle'] = '';
			$array[$key]['varsPrint']['flagHide'] = 0;
			//$array[$key]['flagBtnUse'] = 1;

			if (!is_null($value['vars']['flagUse'])) {
				//flagUse
				if ((int) $value['vars']['flagUse']) {

				} else {
					$array[$key]['varsPrint']['flagHide'] = 1;
					$array[$key]['strClassFont'] = $arr['vars']['varsItem']['strClassNone'];
				}
			}

			$array[$key]['varsPrint']['strTitle'] = $value['strTitle'];

			if (!is_null($array[$key]['vars']['varsValue'])) {
				$idTarget = $value['vars']['idTarget'];
				foreach ($arrayValue as $keyValue => $valueValue) {
					if ($valueValue == 'numRate') {
						continue;
					}
					$numData = $varsFSValue[$flagFiscalPeriod][$idTarget][$valueValue];
					$numValue = $this->_getValueData(array(
						'num' => $numData,
						'flagUnit' => $flagUnit,
						'flagCalc' => $flagCalc,
					));
					$array[$key]['vars']['varsValue'][$valueValue] = $numValue;

					$flag = 0;
					if ($arr['varsFlag']['flagFS'] == 'BS') {
						$flag = 1;
					} else {
						if ($valueValue != 'sumPrev') {
							$flag = 1;
						}
					}
					if ($flag) {
						$array[$key]['varsColumnDetail'][$valueValue] = number_format($numValue);
						$array[$key]['varsPrint'][$valueValue] = number_format($numValue);
					} else {
						$array[$key]['varsColumnDetail'][$valueValue] = '-';
						$array[$key]['varsPrint'][$valueValue] = '-';
					}
				}
				$cut = &$arr['varsItem']['arrAccountTitle']['arrSubAccountStrTitles'][$arr['varsFlag']['flagFS']][$idTarget];
				$array[$key]['strTitle'] = ($idTarget == 'departmentNet') ? $array[$key]['strTitle'] : $cut['strTitleTree'];
				$numRate = 0;
				if ($arr['varsFlag']['flagFS'] == 'BS') {
					$numRate = ($arr['numAssetsSum'] == 0) ? 0 : $array[$key]['vars']['varsValue']['sumNext'] / $arr['numAssetsSum'];
				} else {
					$numRate = ($arr['numNetSales'] == 0) ? 0 : $array[$key]['vars']['varsValue']['sumNext'] / $arr['numNetSales'];
				}
				$numRate *= 100;
				$array[$key]['varsColumnDetail']['numRate'] = number_format($numRate, 3);
				$array[$key]['varsPrint']['numRate'] = number_format($numRate, 3);
				$array[$key]['vars']['varsValue']['numRate'] = number_format($numRate, 3, '.', '');
			}

			if ($value['child']) {
				$array[$key]['child'] = $this->_getAccountTitleValueColumn(array(
					'varsFlag' => $arr['varsFlag'],
					'varsItem' => $arr['varsItem'],
					'varsFS' => $array[$key]['child'],
					'varsFSValue' => $arr['varsFSValue'],
					'vars' => $arr['vars'],
					'numAssetsSum' => $arr['numAssetsSum'],
					'numNetSales' => $arr['numNetSales'],
				));
			}
		}

		return $array;
	}

	/**

	 */
	protected function _getValueData($arr)
	{
		$numValue = 0;
		$numData = $arr['num'];
		if (!is_null($numData)) {

			if ($arr['flagUnit'] == 0) {
				$numValue = $numData;

			} else {
				if ($arr['flagCalc'] == 'floor') {
					$numValue = floor($numData / $arr['flagUnit']);

				} elseif ($arr['flagCalc'] == 'round') {
					$numValue = round($numData / $arr['flagUnit']);

				} elseif ($arr['flagCalc'] == 'ceil') {
					$numValue = ceil($numData / $arr['flagUnit']);
				}

			}
		} else {
			$numValue = 0;
		}

		return $numValue;
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
		global $varsPluginAccountingAccount;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsFlag = array(
			'flagFiscalPeriod' => $varsRequest['query']['jsonValue']['vars']['FlagFiscalPeriod'],
			'idDepartment' => $varsRequest['query']['jsonValue']['vars']['IdDepartment'],
			'flagFS' => $varsRequest['query']['jsonValue']['vars']['FlagFS'],
			'flagUnit' => (int) $varsRequest['query']['jsonValue']['vars']['FlagUnit'],
			'flagCalc' => $varsRequest['query']['jsonValue']['vars']['FlagCalc'],
			'flagZero' => $varsRequest['query']['jsonValue']['vars']['FlagZero'],
		);

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
			'varsFlag' => $varsFlag,
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars' => &$vars,
			'varsItem' => $varsItem,
		)));

		$this->_checkValueDetail(array(
			'varsDetail' => $vars['portal']['varsNavi']['varsDetail'],
			'varsFlag' => $varsFlag,
		));

		$vars = $this->_updateVars(array(
			'vars' => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		));

		$this->sendVars(array(
			'flag' => 1,
			'stamp' => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars' => array(
				'varsFlag' => $varsFlag,
				'varsSubValue' => $varsItem['varsSubValue'],
				'varsDetail' => $vars['portal']['varsList']['varsDetail'],
				'varsHtml' => $vars['portal']['varsList']['varsHtml'],
				'varsColumn' => $vars['portal']['varsList']['tableTree']['varsDetail']['varsColumn'],
			),
		));
	}

	/**
		(array(
			'varsDetail'       => $varsDetail,
			'FlagFiscalPeriod' => $flagFiscalPeriod,
			'FlagUnit'         => $flagUnit,
		))
	 */
	protected function _checkValueDetail($arr)
	{
		global $varsRequest;
		global $classEscape;

		$flag = 0;
		$array = $arr['varsDetail'];
		foreach ($array as $key => $value) {
			$idTarget = $classEscape->toLower(array('str' => $value['id']));
			$arrayOption = $value['arrayOption'];
			foreach ($arrayOption as $keyOption => $valueOption) {
				if ($valueOption['value'] == $arr['varsFlag'][$idTarget]) {
					$flag = 1;
				}
			}
			if (!$flag) {
				if ($arr['flagOutput']) {
					$this->_send404Output();
				} else {
					$this->_sendOld();
				}
			}
			$flag = 0;
		}
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
	protected function _iniListPrint()
	{
		$this->_setClassExt(array('strClass' => 'TrialBalanceOutput'));
	}

	/**
	 *
	 */
	protected function _iniListOutput()
	{
		$this->_setClassExt(array('strClass' => 'TrialBalanceOutput'));
	}
}
