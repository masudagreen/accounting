<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_Jpn extends Code_Else_Plugin_Accounting_Accounting
{

	protected $_extendSelf = array(
		'arrFS'         => array('CR', 'PL', 'BS'),
		'pathFSItem'    => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/FSItem.php',
		'idFixedAssets' => 'tangibleFixedAssets',
		'arrStation'    => array('SummaryStatement', 'DetailedAccount', 'ConsumptionTax'),
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;

		if (preg_match("/^jpn$/i", $varsRequest['query']['ext'])) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}

		$this->_setInitJpn();

		$str = ucwords($varsRequest['query']['ext']);
		$array = $this->_extendSelf['arrStation'];
		foreach ($array as $key => $value) {
			if (preg_match("/^$value/", $str)
				&& !preg_match("/^(ConsumptionTaxSheet|ConsumptionTaxList)/", $str)
			) {
				$str = $value;
				break;
			}
		}

		$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/' . PLUGIN_ACCOUNTING_STR_NATION . '/' . $str . ".php";
		$strClass = 'Code_Else_Plugin_Accounting_' . ucwords(PLUGIN_ACCOUNTING_STR_NATION) . '_' . $str;

		if (!file_exists($path)) {
			$this->sendValue(array(
				'flag'    => 8,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}

		require_once($path);
		$classCall = new $strClass;
		$classCall->run();
	}

	/**

	 */
	protected function _checkCorporationClass($arr)
	{
		global $varsRequest;

		if (!PLUGIN_ACCOUNTING_FLAG_CORPORATION) {
			return;
		}

		$str = ucwords($varsRequest['query']['ext']);
		if ($arr['flagChild']) {
			$strChild = ucwords($varsRequest['query']['child']);
			$str = $str . $strChild;
		}

		$path = PATH_BACK_CLASS_ELSE_PLUGIN
			. 'accounting'
			. '/' . PLUGIN_ACCOUNTING_STR_NATION
			. '/' . $str . '.php';

		require_once($path);

		$path = PATH_BACK_CLASS_ELSE_PLUGIN
			. 'accounting'
			. '/' . PLUGIN_ACCOUNTING_STR_NATION
			. '/' . PLUGIN_ACCOUNTING_NUM_YEAR_SHEET
			. '/' . PLUGIN_ACCOUNTING_FLAG_CORPORATION
			. '/' . $str . '.php';

		if (!file_exists($path)) {
			return;
		}

		$strClass = 'Code_Else_Plugin_Accounting'
			. '_' . ucwords(PLUGIN_ACCOUNTING_STR_NATION)
			. '_' . $str
			. '_' . PLUGIN_ACCOUNTING_NUM_YEAR_SHEET
			. '_' . ucwords(PLUGIN_ACCOUNTING_FLAG_CORPORATION);

		require_once($path);
		$classCall = new $strClass;
		$classCall->run();
		exit;
	}

	/**

	 */
	protected function _setInitJpn()
	{
		global $varsPluginAccountingAccount;
		global $classTime;

		define('PLUGIN_ACCOUNTING_NUM_TIME_ZONE', 9);

		$classTime->setTimeZone(array('data' => PLUGIN_ACCOUNTING_NUM_TIME_ZONE));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		define('PLUGIN_ACCOUNTING_NUM_YEAR_SHEET', $varsEntityNation['numYearSheet']);
		if ($varsEntityNation['flagCorporation'] == 2) {
			define('PLUGIN_ACCOUNTING_FLAG_CORPORATION', 'public');
		} else {
			define('PLUGIN_ACCOUNTING_FLAG_CORPORATION', '');
		}
	}

	/**

	 */
	protected function _getVarsFSItem()
	{
		$vars = $this->getVars(array(
			'path' => $this->_extendSelf['pathFSItem'],
		));

		return $vars;
	}

	/**
		(array(
			'vars'             => array(),
			'varsEntityNation' => array(),
		))
	 */
	protected function _updateVarsFS($arr)
	{
		$vars = $arr['vars'];
		$varsEntityNation = $arr['varsEntityNation'];

		$arrayNew = array();
		$array = $vars;
		foreach ($array as $key => $value) {
			if ($key == 'CR' && !$varsEntityNation['flagCR']) {

			} else {
				$arrayNew[] = $key;
			}
		}

		return $arrayNew;
	}

	/**
		$this->_getVarsStampFiscalPeriod(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'varsEntityNation' => $arr['varsEntityNation'],
		));
	 */
	protected function _getVarsStampFiscalPeriod($arr)
	{
		$arrayNew = array();
		$array = $this->_getVarsFlagFiscalPeriod(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		foreach ($array as $key => $value) {
			$arrayNew[$value] = $this->_getVarsStampTerm(array(
				'varsFlag'         => array('flagFiscalPeriod' => $value),
				'varsEntityNation' => $arr['varsEntityNation'],
				'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			));
		}

		return $arrayNew;
	}

	/**
		(array(
			'varsFlag'         => $arr['varsFlag'],
			'varsEntityNation' => $arr['varsEntityNation'],
			'numFiscalPeriod'  => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		))
	 */
	protected function _getVarsStampTerm($arr)
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		if ($arr['idEntity']) {
			$idEntity = $arr['idEntity'];
		}

		$numTimeZone = PLUGIN_ACCOUNTING_NUM_TIME_ZONE;
		$strTimeZone = (-1 * $numTimeZone) . 'hours';
		$numFiscalPeriod = $arr['numFiscalPeriod'];
		$numFiscalPeriodStart = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriodStart'];
		$numFiscalBeginningYear = $arr['varsEntityNation']['numFiscalBeginningYear'];
		$numCurrentYear = $arr['varsEntityNation']['numFiscalBeginningYear'];
		$numCurrentMonth = $arr['varsEntityNation']['numFiscalBeginningMonth'];

		if ($arr['varsFlag']['flagFiscalPeriod'] == 'f1') {

			$dateTime = new DateTime("$numCurrentYear-$numCurrentMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
			$stampMin = $dateTime->format('U');

			$numCurrentMonth += $arr['varsEntityNation']['numFiscalTermMonth'];

		} elseif ($arr['varsFlag']['flagFiscalPeriod'] == 'f21') {

			$dateTime = new DateTime("$numCurrentYear-$numCurrentMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
			$stampMin = $dateTime->format('U');

			$numCurrentMonth += 6;

		} elseif ($arr['varsFlag']['flagFiscalPeriod'] == 'f22') {
			$numCurrentMonth += 6;
			if ($numCurrentMonth > 12) {
				$numCurrentYear++;
				$numCurrentMonth -= 12;
			}

			$dateTime = new DateTime("$numCurrentYear-$numCurrentMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
			$stampMin = $dateTime->format('U');

			$numCurrentMonth += 6;

		} elseif ($arr['varsFlag']['flagFiscalPeriod'] == 'f41') {

			$dateTime = new DateTime("$numCurrentYear-$numCurrentMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
			$stampMin = $dateTime->format('U');

			$numCurrentMonth += 3;

		} elseif ($arr['varsFlag']['flagFiscalPeriod'] == 'f42') {
			$numCurrentMonth += 3;
			if ($numCurrentMonth > 12) {
				$numCurrentYear++;
				$numCurrentMonth -= 12;
			}

			$dateTime = new DateTime("$numCurrentYear-$numCurrentMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
			$stampMin = $dateTime->format('U');

			$numCurrentMonth += 3;

		} elseif ($arr['varsFlag']['flagFiscalPeriod'] == 'f43') {
			$numCurrentMonth += 6;
			if ($numCurrentMonth > 12) {
				$numCurrentYear++;
				$numCurrentMonth -= 12;
			}

			$dateTime = new DateTime("$numCurrentYear-$numCurrentMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
			$stampMin = $dateTime->format('U');

			$numCurrentMonth += 3;

		} elseif ($arr['varsFlag']['flagFiscalPeriod'] == 'f44') {
			$numCurrentMonth += 9;
			if ($numCurrentMonth > 12) {
				$numCurrentYear++;
				$numCurrentMonth -= 12;
			}

			$dateTime = new DateTime("$numCurrentYear-$numCurrentMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
			$stampMin = $dateTime->format('U');

			$numCurrentMonth += 3;

		} else {
			$numCurrentMonth = (int) $arr['varsFlag']['flagFiscalPeriod'];
			if ($numCurrentMonth < $arr['varsEntityNation']['numFiscalBeginningMonth']) {
				$numCurrentYear++;
			}

			$dateTime = new DateTime("$numCurrentYear-$numCurrentMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
			$stampMin = $dateTime->format('U');

			$numCurrentMonth += 1;

		}

		if ($numCurrentMonth > 12) {
			$numCurrentYear++;
			$numCurrentMonth -= 12;
		}

		$dateTime = new DateTime("$numCurrentYear-$numCurrentMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stampMax = $dateTime->format('U') - 1;

		$data = array(
			'stampMin' => $stampMin,
			'stampMax' => $stampMax,
		);

		return $data;
	}

	/**

	 */
	protected function _getVarsFiscalPeriodMonth($arr)
	{
		$varsEntityNation = $arr['varsEntityNation'];
		if (!$varsEntityNation) {
			$varsEntityNation = $this->_getVarsEntityNation(array(
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
			));
		}

		$arrayNew = array();
		$numMonth = (int) $varsEntityNation['numFiscalBeginningMonth'];
		$numEnd = (int) $varsEntityNation['numFiscalTermMonth'];
		for ($i = 0; $i < $numEnd; $i++) {
			$arrayNew[] = $numMonth;
			$numMonth++;
			if ($numMonth > 12) {
				$numMonth = 1;
			}
		}

		return $arrayNew;
	}

	/**
		$this->_getVarsFlagFiscalPeriod(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
	 */
	protected function _getVarsFlagFiscalPeriod($arr)
	{
		$varsEntityNation = $arr['varsEntityNation'];
		if (!$varsEntityNation) {
			$varsEntityNation = $this->_getVarsEntityNation(array(
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
			));
		}

		$arrayNew = array();
		$arrayNew[] = 'f1';
		if ($varsEntityNation['numFiscalTermMonth'] == 12) {
			$arrayNew[] = 'f21';
			$arrayNew[] = 'f22';
			$arrayNew[] = 'f41';
			$arrayNew[] = 'f42';
			$arrayNew[] = 'f43';
			$arrayNew[] = 'f44';
		}

		$array = $this->_getVarsFiscalPeriodMonth(array(
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'varsEntityNation' => $varsEntityNation,
		));
		foreach ($array as $key => $value) {
			$arrayNew[] = $value;
		}

		return $arrayNew;
	}

	/**
		(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
	 */
	protected function _getVarsFS($arr)
	{
		global $classDb;
		global $varsAccount;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

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
					'value'         => ($arr['idEntity'])? $arr['idEntity'] : $varsPluginAccountingAccount['idEntityCurrent'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'numFiscalPeriod',
					'flagCondition' => 'eq',
					'value'         => $arr['numFiscalPeriod'],
				),
			),
		));
		$varsFS = $rows['arrRows'][0];

		return $varsFS;
	}

		/**
		(array(
			'vars' => array(),
		))
	 */
	protected function _getFSList($arr)
	{
		$vars = $this->_getVarsFSItem();
		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		$flag = 0;
		$arrayNew = array();
		$array = $vars['varsItem']['arrayFS'];
		foreach ($array as $key => $value) {
			if ($key == 'CR' && !$varsEntityNation['flagCR']) {
				continue;
			}
			$arrayNew[$key] = $value;
		}

		return $arrayNew;
	}

	/**

	 */
	protected function _getVarsConsumptionTax($arr)
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$strNation = PLUGIN_ACCOUNTING_STR_NATION;

		$strLang = $varsPluginAccountingEntity[$varsPluginAccountingAccount['idEntityCurrent']]['strLang'];
		if ($arr['strLang']) {
			$strLang = $arr['strLang'];
		}

		$vars = $this->_getVars(array(
			'path'      => $this->_self['varsTax'],
			'strLang'   => $strLang,
			'strNation' => $strNation,
		));

		$arrayStr = array('generalProration', 'generalEach', 'simple');
		foreach ($arrayStr as $keyStr => $valueStr) {
			$array = $vars[$valueStr];
			foreach ($array as $key => $value) {
				$str = 'arrStr' . ucwords($valueStr);
				$vars[$str][$value['value']] = $value['strTitle'];
			}
		}

		return $vars;
	}

	/**

	 */
	protected function _getVarsDepartmentTreeItem()
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$strNation = PLUGIN_ACCOUNTING_STR_NATION;

		$vars = $this->_getVars(array(
			'path'      => $this->_self['varsDepartment'],
			'strLang'   => $varsPluginAccountingEntity[$varsPluginAccountingAccount['idEntityCurrent']]['strLang'],
			'strNation' => $strNation,
		));

		return $vars;
	}

	/**
		(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));
	 */
	protected function _getVarsSubAccountTitle($arr)
	{
		global $classDb;
		global $varsAccount;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingSubAccountTitle' . $strNation,
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
					'value'         => $arr['numFiscalPeriod'],
				),
			),
		));

		$array = $rows['arrRows'];
		$arrStrTitle = array();
		$arrSelectTag = array();
		foreach ($array as $key => $value) {
			$data = array();
			$data['strTitle'] = $value['strTitle'];
			$data['value'] = $value['idSubAccountTitle'];
			$idAccountTitle = $value['idAccountTitle'];
			$arrStrTitle[$idAccountTitle][$value['idSubAccountTitle']]['strTitle'] = $value['strTitle'];
			if (is_null($arrSelectTag[$idAccountTitle])) {
				$arrSelectTag[$idAccountTitle] = array($data);

			} else {
				$arrSelectTag[$idAccountTitle][] = $data;
			}
		}

		$data = array(
			'arrStrTitle'  => $arrStrTitle,
			'arrSelectTag' => $arrSelectTag,
		);

		return $data;
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
					'vars'               => $varsFS[$str],
					'flagFS'             => $key,
					'strCR'              => $strCR,
				));
				$arrSubAccountSelectTag = array_merge($arrSubAccountSelectTag, $varsSubAccountTitle['arrSelectTag']);
				$arrSubAccountSelectTags[$key] = $varsSubAccountTitle['arrSelectTag'];
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
			}

			if ($value['child']) {
				$data = $this->_getAccountTitleSubAccountTitle(array(
					'vars'               => $array[$key]['child'],
					'arrSelectTag'       => $arr['arrSelectTag'],
					'arrSubAccountTitle' => $arr['arrSubAccountTitle'],
					'flagFS'             => $arr['flagFS'],
					'strCR'              => $arr['strCR'],
				));
				$array[$key]['child'] = $data['vars'];
				$arrSelectTag =  $data['arrSelectTag'];
			}
		}

		return $arr;
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
			$strTitleFS = ($arr['flagFS'] == 'CR')? '['. $arr['strCR']  .']' . $value['strTitle'] : $value['strTitle'];
			$data = array(
				'strTitle'                               => $value['strTitle'],
				'strTitleFS'                             => $strTitleFS,
				'flagDebit'                              => (int) $value['vars']['flagDebit'],
				'flagUse'                                => (is_null($value['vars']['flagUse']))? '' : (int) $value['vars']['flagUse'],
				'flagConsumptionTaxGeneralRuleEach'      => (is_null($value['vars']['flagConsumptionTaxGeneralRuleEach']))? 'none' : $value['vars']['flagConsumptionTaxGeneralRuleEach'],
				'flagConsumptionTaxGeneralRuleProration' => (is_null($value['vars']['flagConsumptionTaxGeneralRuleProration']))? 'none' : $value['vars']['flagConsumptionTaxGeneralRuleProration'],
				'flagConsumptionTaxSimpleRule'           => (is_null($value['vars']['flagConsumptionTaxSimpleRule']))? 'none' : $value['vars']['flagConsumptionTaxSimpleRule'],
				'idAccountTitleJgaapFS'                  => (is_null($value['vars']['idAccountTitleJgaapFS']))? '' : $value['vars']['idAccountTitleJgaapFS'],
				'varsJgaapCS'                            => (is_null($value['vars']['varsJgaapCS']))? array() : $value['vars']['varsJgaapCS'],
				'flagFS'                                 => $arr['flagFS'],
				'idParent'                               => $arr['idParent'],
			);


			$arrLevel = preg_split("/-/", $value['id']);
			$num = count($arrLevel) - 2;
			$arrLevel = array();
			for ($i = 0 ; $i < $num; $i++) {
				$arrLevel[$i] = '';
			}
			$str =  ' ' . join('.', $arrLevel) . ' ';
			$strTitle = $str . $value['strTitle'];
			$strTitleFSTag = $str .  $strTitleFS;

			if (is_null($value['vars']['flagUse'])) {
				$arr['arrSelectTag'][] = array(
					'strTitle'     => $strTitle,
					'value'        => '',
					'flagDisabled' => 1,
				);

			} else {
				$arr['arrSelectTag'][] = array(
					'strTitle'     => $strTitleFSTag,
					'value'        => $value['vars']['idTarget'],
				);

				$arr['arrStrTitle'][$value['vars']['idTarget']] = $data;
			}

			if ($value['child']) {
				$dataTemp = $this->_getArrSelectOption(array(
					'vars'            => $array[$key]['child'],
					'arrSelectTag'    => $arr['arrSelectTag'],
					'arrStrTitle'     => $arr['arrStrTitle'],
					'flagBS'          => $arr['flagBS'],
					'flagFS'          => $arr['flagFS'],
					'strCR'           => $arr['strCR'],
					'idParent'        => $value['vars']['idTarget'],
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
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _getStrCR($arr)
	{
		$varsFSItem = $this->_getVarsFSItem();
		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		if ($varsEntityNation['flagCorporation'] == 4) {
			return $varsFSItem['varsItem']['strCRAgri'];
		} else {
			return $varsFSItem['varsItem']['strCR'];
		}
	}

	/**
	 */
	protected function _getVarsEntityNation($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

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
		(array(
			'flagFiscalPeriod' => 'f1',
			'numFiscalPeriod'  => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		))
	 */
	protected function _getVarsFiscalPeriod($arr)
	{
		global $classTime;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		$flagFiscalPeriod = $arr['flagFiscalPeriod'];

		$numFiscalPeriod = $arr['numFiscalPeriod'];
		$numFiscalPeriodStart = $varsPluginAccountingEntity[$varsPluginAccountingAccount['idEntityCurrent']]['numFiscalPeriodStart'];

		$numFiscalBeginningYear = $varsEntityNation['numFiscalBeginningYear'];
		$numYear = $numFiscalBeginningYear;
		$numStartYear = $numYear;
		$numEndYear = $numStartYear;
		$numStartMonth = (int) $varsEntityNation['numFiscalBeginningMonth'];

		if (preg_match( "/^f/", $flagFiscalPeriod)) {
			if ($varsEntityNation['numFiscalTermMonth'] == 12) {
				if (preg_match( "/^(f1)$/", $flagFiscalPeriod)) {
					$numEndMonth = $numStartMonth + 12;

				} elseif (preg_match( "/^(f21)$/", $flagFiscalPeriod)) {
					$numEndMonth = $numStartMonth + 6;

				} elseif (preg_match( "/^(f22)$/", $flagFiscalPeriod)) {
					$numEndMonth = $numStartMonth + 12;
					$numStartMonth = $numEndMonth - 6;

				} elseif (preg_match( "/^(f41)$/", $flagFiscalPeriod)) {
					$numEndMonth = $numStartMonth + 3;

				} elseif (preg_match( "/^(f42)$/", $flagFiscalPeriod)) {
					$numEndMonth = $numStartMonth + 6;
					$numStartMonth = $numEndMonth - 3;

				} elseif (preg_match( "/^(f43)$/", $flagFiscalPeriod)) {
					$numEndMonth = $numStartMonth + 9;
					$numStartMonth = $numEndMonth - 3;

				}elseif (preg_match( "/^(f44)$/", $flagFiscalPeriod)) {
					$numEndMonth = $numStartMonth + 12;
					$numStartMonth = $numEndMonth - 3;
				}
				$numEndMonth -= 1;

			} else {
				if (preg_match( "/^(f1)$/", $flagFiscalPeriod)) {
					$numEndMonth = $numStartMonth + $varsEntityNation['numFiscalTermMonth'] - 1;
				}
			}
			if ($numEndMonth > 12) {
				$numEndMonth -= 12;
				$numEndYear++;
			}
			if ($numStartMonth > 12) {
				$numStartMonth -= 12;
				$numStartYear++;
			}

		} else {
			$numStartMonth = (int) $flagFiscalPeriod;
			$numStart = (int) $varsEntityNation['numFiscalBeginningMonth'];
			$numEnd = $numStart + (int) $varsEntityNation['numFiscalTermMonth'];
			$numMonth = $numStart;
			for ($i = $numStart; $i < $numEnd; $i++) {
				if ($numMonth == $numStartMonth) {
					if ($i > 12) {
						$numStartYear++;
					}
					break;
				}
				if ($numMonth > 12) {
					$numMonth -= 12;
				}
				$numMonth++;

			}
			$numEndMonth = $numStartMonth;
			$numEndYear = $numStartYear;

		}

		$data = $this->_getVarsStampTerm(array(
			'varsFlag'         => array('flagFiscalPeriod' => $flagFiscalPeriod),
			'varsEntityNation' => $varsEntityNation,
			'numFiscalPeriod'  => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		/*

		 * */
		$numStartHeisei = ($data['stampMin'] >= 600188400)? $numStartYear - 1988 : '';
		$numEndHeisei = ($data['stampMax'] >= 600188400)? $numEndYear - 1988 : '';

		$arrStartDate = $classTime->getLocal(array('stamp' => $data['stampMin']));
		$arrEndDate = $classTime->getLocal(array('stamp' => $data['stampMax']));

		/*20190401 start*/
		$flagStartNengo = $classTime->getFlagNengo(array('stamp' => $data['stampMin']));
		$flagEndNengo = $classTime->getFlagNengo(array('stamp' => $data['stampMax']));
		$numStartNengoYear = $classTime->getNengoYear(array('stamp' => $data['stampMin'], 'numYear' => $numStartYear));
		$numEndNengoYear = $classTime->getNengoYear(array('stamp' => $data['stampMax'], 'numYear' => $numEndYear));

		$strStartNengoYear = $classTime->getStrNengoYear(array('stamp' => $data['stampMin'], 'numYear' => $numStartYear));
		$strEndNengoYear = $classTime->getStrNengoYear(array('stamp' => $data['stampMax'], 'numYear' => $numEndYear));

		/*20190401 end*/

		$data = array(
			'stampStart'     => $data['stampMin'],
			'stampEnd'       => $data['stampMax'],

			/*20190401 start*/
		    'flagStartNengo'    => $flagStartNengo,
		    'flagEndNengo'      => $flagEndNengo,
		    'numStartNengoYear' => $numStartNengoYear,
		    'numEndNengoYear'   => $numEndNengoYear,
		    'strStartNengoYear' => $strStartNengoYear,
		    'strEndNengoYear'   => $strEndNengoYear,
		    /*20190401 end*/

			'numStartHeisei' => $numStartHeisei,
			'numStartYear'   => $numStartYear,
			'numEndHeisei'   => $numEndHeisei,
			'numEndYear'     => $numEndYear,

			'numStartMonth'  => $arrStartDate['numMonth'],
			'strStartMonth'  => ($arrStartDate['numMonth'] < 10)? '0' . $arrStartDate['numMonth'] : $arrStartDate['numMonth'],
			'numEndMonth'    => $arrEndDate['numMonth'],
			'strEndMonth'    => ($arrEndDate['numMonth'] < 10)? '0' . $arrEndDate['numMonth'] : $arrEndDate['numMonth'],

			'numStartDate'   => $arrStartDate['numDate'],
			'strStartDate'   => ($arrStartDate['numDate'] < 10)? '0' . $arrStartDate['numDate'] : $arrStartDate['numDate'],
			'numEndDate'     => $arrEndDate['numDate'],
			'strEndDate'     => ($arrEndDate['numDate'] < 10)? '0' . $arrEndDate['numDate'] : $arrEndDate['numDate'],
			'numStartDay'    => $arrStartDate['numDay'],
			'numEndDay'      => $arrEndDate['numDay'],
		);

		return $data;
	}

	/**
		(array(
			'idDepartment'    => $key,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))

	 */
	protected function _getVarsFSValueDepartment($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingEntityDepartmentFSValue' . $strNation,
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
					'value'         => $arr['numFiscalPeriod'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idDepartment',
					'flagCondition' => 'eq',
					'value'         => $arr['idDepartment'],
				),
			),
		));

		return $rows['arrRows'][0];
	}

	/**

	 */
	protected function _getVarsFSValue($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingFSValue' . $strNation,
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
					'value'         => $arr['numFiscalPeriod'],
				),
			),
		));

		return $rows['arrRows'][0];
	}

	/**
		(array(
			'arrVarsLog'      => $arrayNew,
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		))
	 */
	protected function _getVarsLogCalcLoop($arr)
	{
		$vars = $this->_getVarsFSItem();

		$arrAccountTitle = $this->_getAccountTitle(array(
			'vars'               => $vars,
			'arrSubAccountTitle' => array(),
			'numFiscalPeriod'    => $arr['numFiscalPeriod'],
		));

		$arrayNew = array();
		$array = $arr['arrVarsLog'];
		foreach ($array as $key => $value) {
			$arrayData = $this->_getVarsLogCalc(array(
				'varsLog'         => $value,
				'arrAccountTitle' => $arrAccountTitle,
				'varsJournal'     => $vars['varsJournal'],
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
			));
			foreach ($arrayData as $keyData => $valueData) {
				$arrayNew[] = $valueData;
			}
		}

		return $arrayNew;
	}

	/**
		(array(
			'varsLog'         => $value,
			'arrAccountTitle' => $arrAccountTitle,
			'varsJournal'     => $vars['varsJournal'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _getVarsLogCalc($arr)
	{
		global $classTime;

		$varsLog = $arr['varsLog'];
		$idLog = $varsLog['idLog'];
		$stampRegister = $varsLog['stampRegister'];
		$stampBook = $varsLog['stampBook'];
		$idEntity = $varsLog['idEntity'];
		$numFiscalPeriod = $varsLog['numFiscalPeriod'];
		$idAccount = $varsLog['idAccount'];
		$strTitle = $varsLog['strTitle'];
		$flagFiscalReport = $varsLog['flagFiscalReport'];
		$numEnd = count($varsLog['jsonVersion']) - 1;

		$arrayNew = array();
		$array = $this->_updateVarsJournalTax(array(
			'varsJournal'     => $arr['varsJournal'],
			'varsDetail'      => $varsLog['jsonVersion'][$numEnd]['jsonDetail']['varsDetail'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		foreach ($array as $key => $value) {
			$arrayStr = array('Debit', 'Credit');
			foreach ($arrayStr as $keyStr => $valueStr) {
				$flagDebit = ($valueStr == 'Debit')? 1 : 0;
				$strSide = 'arr' . $valueStr;
				$idAccountTitle = $value[$strSide]['idAccountTitle'];
				$numValue = $value[$strSide]['numValue'];
				$numRateConsumptionTax = $value[$strSide]['numRateConsumptionTax'];
				$idDepartment = ($value[$strSide]['idDepartment'])? $value[$strSide]['idDepartment'] : null;
				$idSubAccountTitle = ($value[$strSide]['idSubAccountTitle'])? $value[$strSide]['idSubAccountTitle'] : null;

				/*
    			 * 20191001 start
    			 */
				$flagRateConsumptionTaxReduced = ($value[$strSide]['flagRateConsumptionTaxReduced'])? 1 : 0;
				/*
				 * 20191001 end
				 */

				$flagConsumptionTaxIncluding = $value[$strSide]['flagConsumptionTaxIncluding'];
				$flagConsumptionTaxGeneralRuleEach = $value[$strSide]['flagConsumptionTaxGeneralRuleEach'];
				$flagConsumptionTaxGeneralRuleProration = $value[$strSide]['flagConsumptionTaxGeneralRuleProration'];
				$flagConsumptionTaxSimpleRule = $value[$strSide]['flagConsumptionTaxSimpleRule'];
				$flagConsumptionTaxWithoutCalc = $value[$strSide]['flagConsumptionTaxWithoutCalc'];
				$flagConsumptionTaxCalc = $value[$strSide]['flagConsumptionTaxCalc'];
				$idAccountTitleCredit = $varsLog['jsonVersion'][$numEnd]['jsonDetail']['idAccountTitleCredit'];
				$idAccountTitleDebit = $varsLog['jsonVersion'][$numEnd]['jsonDetail']['idAccountTitleDebit'];
				if ($flagDebit) {
					$idAccountTitleContra = $idAccountTitleCredit;
				} else {
					$idAccountTitleContra = $idAccountTitleDebit;
				}

				$idDepartmentContra = $this->_checkVarsLogCalcContra(array(
					'varsDetail' => $array,
					'strDebit'   => ($flagDebit)? 'arrCredit' : 'arrDebit',
					'idTarget'   => 'idDepartment',
				));
				$idSubAccountTitleContra = $this->_checkVarsLogCalcContra(array(
					'varsDetail' => $array,
					'strDebit'  => ($flagDebit)? 'arrCredit' : 'arrDebit',
					'idTarget'   => 'idSubAccountTitle',
				));

				if ($idAccountTitle) {
					$data = array(
						'idLog'                         => $idLog,
						'stampRegister'                 => $stampRegister,
						'stampBook'                     => $stampBook,
						'idEntity'                      => $idEntity,
						'numFiscalPeriod'               => $numFiscalPeriod,
						'idAccount'                     => $idAccount,
						'strTitle'                      => $strTitle,
						'flagFiscalReport'              => $flagFiscalReport,
						'flagDebit'                     => $flagDebit,
						'idAccountTitle'                => $idAccountTitle,
						'idAccountTitleContra'          => $idAccountTitleContra,
						'idDepartmentContra'            => $idDepartmentContra,
						'idSubAccountTitleContra'       => $idSubAccountTitleContra,
						'numValue'                      => $numValue,
						/*
						 * 20191001 start
						 */
					    'flagRateConsumptionTaxReduced' => $flagRateConsumptionTaxReduced,
					    /*
					     * 20191001 end
					     */
						'idDepartment'                  => $idDepartment,
						'idSubAccountTitle'             => $idSubAccountTitle,
					);
					$arrayData = $data;
					$arrColumn = array();
					$arrValue = array();
					$num = 0;
					foreach ($arrayData as $keyData => $valueData) {
						$arrColumn[$num] = $keyData;
						$arrValue[$num] = $valueData;
						$num++;
					}
					$data['flagConsumptionTaxIncluding'] = $flagConsumptionTaxIncluding;
					$data['flagConsumptionTaxGeneralRuleEach'] = $flagConsumptionTaxGeneralRuleEach;
					$data['flagConsumptionTaxGeneralRuleProration'] = $flagConsumptionTaxGeneralRuleProration;
					$data['flagConsumptionTaxSimpleRule'] = $flagConsumptionTaxSimpleRule;
					$data['flagConsumptionTaxWithoutCalc'] = $flagConsumptionTaxWithoutCalc;
					$data['flagConsumptionTaxCalc'] = $flagConsumptionTaxCalc;
					$data['numRateConsumptionTax'] = $numRateConsumptionTax;
					$data['arrDate'] = $classTime->getLocal(array('stamp' => $stampBook));
					$data['flagDebitAccountTitle'] = $arr['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['flagDebit'];
					$data['flagFS'] = $arr['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['flagFS'];
					$data['idAccountTitleJgaapFS'] = $arr['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['idAccountTitleJgaapFS'];
					$data['varsJgaapCS'] = $arr['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['varsJgaapCS'];
					$data['arrColumn'] = $arrColumn;
					$data['arrValue'] = $arrValue;
					$arrayNew[] = $data;
				}
			}
		}

		return $arrayNew;
	}
	/**
		(array(
			'varsDetail' => $array,
			'strDebit'  => ($flagDebit)? 'arrCredit' : 'arrDebit',
			'idTarget'   => 'idDepartment',
		))
	 */
	protected function _checkVarsLogCalcContra($arr)
	{
		$arrayCheck = array();
		$array = $arr['varsDetail'];
		$data = '';
		foreach ($array as $key => $value) {
			$idAccountTitle = $value[$arr['strDebit']]['idAccountTitle'];
			if ($idAccountTitle) {
				if ($value[$arr['strDebit']][$arr['idTarget']]) {
					$arrayCheck[$value[$arr['strDebit']][$arr['idTarget']]] = 1;
					$data = $value[$arr['strDebit']][$arr['idTarget']];

				} else {
					$arrayCheck['dummy'] = 1;
				}
			}
		}
		if (count($arrayCheck) == 1 && !$arrayCheck['dummy']) {
			return $data;
		}

		return 0;

	}

	/**
		'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		'varsDetail'      => $vars['portal']['varsList']['varsDetail'],

	protected function _updateVarsJournal($arr)
	{
		$vars = $this->_getVarsFSItem();
		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$arr['varsDetail'] = $this->_updateVarsJournalElse(array(
			'varsEntityNation' => $varsEntityNation,
			'varsDetail'       => $arr['varsDetail'],
		));

		$array = $arr['varsDetail'];
		foreach ($array as $key => $value) {
			if ($value['flagRemove']) {
				continue;
			}
			if ($value['flagApply']) {
				continue;
			}
			$array[$key]['jsonDetail']['jsonDetail']['varsDetail'] = $this->_updateVarsJournalTax(array(
				'varsJournal'     => $vars['varsJournal'],
				'varsDetail'      => $value['jsonDetail']['jsonDetail']['varsDetail'],
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
			));
		}

		return $array;
	} */

	/**
		(array(
			'varsJournal'     => $arr['vars']['varsJournal'],
			'varsDetail'      => $value['jsonDetail']['jsonDetail']['varsDetail'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _updateVarsJournalTax($arr)
	{
		global $varsPluginAccountingAccount;

		$varsEntityNation = $arr['varsEntityNation'];
		if (!$varsEntityNation) {
			$varsEntityNation = $this->_getVarsEntityNation(array(
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
			));
		}

		$arrayNew = array();
		$arraySide = array('arrDebit', 'arrCredit');

		$array = $arr['varsDetail'];
		foreach ($array as $key => $value) {
			$flagInsert = 0;
			$varsTmpl = $arr['varsJournal']['varsTmpl'];
			foreach ($arraySide as $keySide => $valueSide) {
				$strSide = $valueSide;
				if (!$value[$strSide]['idAccountTitle']) {
					continue;
				}
				$flagConsumptionTaxWithoutCalc = (int) $value[$strSide]['flagConsumptionTaxWithoutCalc'];
				$flagConsumptionTaxCalc = (int) $value[$strSide]['flagConsumptionTaxCalc'];

				$flagTax = 0;
				$flagDebit = 0;
				$data = array();
				if (!(int) $varsEntityNation['flagConsumptionTaxFree']
					 && !(int) $varsEntityNation['flagConsumptionTaxIncluding']
					 && $value[$strSide]['idAccountTitle'] != 'suspenseReceiptOfConsumptionTaxes'
					 && $value[$strSide]['idAccountTitle'] != 'suspensePaymentConsumptionTaxes'
				) {
					$flagConsumptionTaxGeneralRuleEach = $value[$strSide]['flagConsumptionTaxGeneralRuleEach'];
					if (preg_match("/^tax/", $flagConsumptionTaxGeneralRuleEach)
						&& (int) $varsEntityNation['flagConsumptionTaxGeneralRule']
						&& (int) $varsEntityNation['flagConsumptionTaxDeducted']
					) {
						$flagTax = 1;
						$flagDebit = (preg_match("/^taxDebit/", $flagConsumptionTaxGeneralRuleEach))? 1 : 0;
						$data['flagConsumptionTaxGeneralRuleEach'] = $flagConsumptionTaxGeneralRuleEach;
					}

					$flagConsumptionTaxGeneralRuleProration = $value[$strSide]['flagConsumptionTaxGeneralRuleProration'];
					if (preg_match("/^tax/", $flagConsumptionTaxGeneralRuleProration)
						&& (int) $varsEntityNation['flagConsumptionTaxGeneralRule']
						&& !(int) $varsEntityNation['flagConsumptionTaxDeducted']
					) {
						$flagTax = 1;
						$flagDebit = (preg_match("/^taxDebit/", $flagConsumptionTaxGeneralRuleProration))? 1 : 0;
						$data['flagConsumptionTaxGeneralRuleProration'] = $flagConsumptionTaxGeneralRuleProration;
					}

					$flagConsumptionTaxSimpleRule = $value[$strSide]['flagConsumptionTaxSimpleRule'];
					if (preg_match("/^tax/", $flagConsumptionTaxSimpleRule)
						&& !(int) $varsEntityNation['flagConsumptionTaxGeneralRule']
					) {
						$flagTax = 1;
						$flagDebit = (preg_match("/^taxDebit/", $flagConsumptionTaxSimpleRule))? 1 : 0;
						$data['flagConsumptionTaxSimpleRule'] = $flagConsumptionTaxSimpleRule;
					}

					if ($flagTax) {
						$flagInsert = 1;
						$data['flagInsertTax'] = 1;
						$numValue =  $value[$strSide]['numValue'];
						$numValueConsumptionTax =  $value[$strSide]['numValueConsumptionTax'];

						$data['numRateConsumptionTax'] = $value[$strSide]['numRateConsumptionTax'];

						/*
						 * 20191001 start
						 */
						$data['flagRateConsumptionTaxReduced'] = $value[$strSide]['flagRateConsumptionTaxReduced'];
						/*
						 * 20191001 end
						 */

						$flagConsumptionTaxCalc = $value[$strSide]['flagConsumptionTaxCalc'];
						$flagConsumptionTaxWithoutCalc = $value[$strSide]['flagConsumptionTaxWithoutCalc'];
						if ($flagDebit) {
							$data['idAccountTitle'] = $arr['varsJournal']['idAccountDebit'];

						} else {
							$data['idAccountTitle'] = $arr['varsJournal']['idAccountCredit'];
						}

						$data['idDepartment'] = $value[$strSide]['idDepartment'];

						//wrong -> $data['idSubAccountTitle'] = $value[$strSide]['idSubAccountTitle'];
						//ok -> $data['idSubAccountTitle'] = '';
						$data['idSubAccountTitle'] = '';
						$data['flagConsumptionTaxFree'] = 0;
						$data['flagConsumptionTaxIncluding'] = 0;
						$data['flagConsumptionTaxWithoutCalc'] = $flagConsumptionTaxWithoutCalc;
						$data['flagConsumptionTaxCalc'] = $flagConsumptionTaxCalc;

						//insert ConsumptionTax
						if ($flagConsumptionTaxWithoutCalc == 1) {
							$value[$strSide]['numValue'] = $numValue - $numValueConsumptionTax;
							$data['numValue'] = $numValueConsumptionTax;

						} elseif ($flagConsumptionTaxWithoutCalc == 2) {
							$data['numValue'] = $numValueConsumptionTax;
						}
						$varsTmpl[$strSide] = $data;
					}
				}
			}
			$arrayNew[] = $value;
			if ($flagInsert) {
				$arrayNew[] = $varsTmpl;
			}
		}

		return $arrayNew;
	}

	/**
		(array(
			'varsEntityNation' => $varsEntityNation,
			'varsDetail'       => $arr['varsDetail'],
		))

	protected function _updateVarsJournalElse($arr)
	{
		$array = &$arr['varsDetail'];

		foreach ($array as $key => $value) {
			if ($value['flagRemove']) {
				continue;
			}
			if ($value['flagApply']) {
				continue;
			}
			$flagConsumptionTaxDebit = 0;
			$flagConsumptionTaxCredit = 0;
			$arraySide = array('arrDebit', 'arrCredit');
			foreach ($arraySide as $keySide => $valueSide) {
				$arrayDetail = $value['jsonDetail']['jsonDetail']['varsDetail'];
				$strSide = $valueSide;
				foreach ($arrayDetail as $keyDetail => $valueDetail) {
					if (!$valueDetail[$strSide]['idAccountTitle']) {
						continue;
					}
					$arrConsumptionTax = $this->_checkVarsJournalTaxSide(array(
						'varsEntityNation' => $arr['varsEntityNation'],
						'valueSide'        => $valueSide,
						'valueDetail'      => &$valueDetail
					));
					if (!$arrConsumptionTax['flagTax']) {
						continue;
					}
					if ($arrConsumptionTax['flagDebit']) {
						$flagConsumptionTaxDebit++;

					} else {
						$flagConsumptionTaxCredit++;
					}
				}
			}
			if ($numConsumptionTaxDebit > 0 && $numConsumptionTaxCredit == 0) {
				$array[$key]['jsonDetail']['jsonDetail']['idAccountTitleDebit'] = 'else';

			} elseif ($numConsumptionTaxDebit == 0 && $numConsumptionTaxCredit > 0) {
				$array[$key]['jsonDetail']['jsonDetail']['idAccountTitleCredit'] = 'else';

			} elseif ($numConsumptionTaxDebit > 0 && $numConsumptionTaxCredit > 0) {
				$array[$key]['jsonDetail']['jsonDetail']['idAccountTitleDebit'] = 'else';
				$array[$key]['jsonDetail']['jsonDetail']['idAccountTitleCredit'] = 'else';
			}
		}

		return $array;
	}*/

	/**
		(array(
			'varsEntityNation' => $arr['varsEntityNation'],
			'valueSide'        => $valueSide,
			'valueDetail'      => &$valueDetail
		))
	 */
	protected function _checkVarsJournalTaxSide($arr)
	{
		$valueSide = $arr['valueSide'];
		$numValue =  $arr['valueDetail'][$valueSide]['numValue'];
		$flagConsumptionTaxWithoutCalc = (int) $arr['valueDetail'][$valueSide]['flagConsumptionTaxWithoutCalc'];
		$flagConsumptionTaxCalc = (int) $arr['valueDetail'][$valueSide]['flagConsumptionTaxCalc'];

		$flagTax = 0;
		$flagDebit = 0;

		if (!(int) $arr['varsEntityNation']['flagConsumptionTaxFree']
			 && !(int) $arr['varsEntityNation']['flagConsumptionTaxIncluding']
			 && $arr['valueDetail'][$valueSide]['idAccountTitle'] != 'suspenseReceiptOfConsumptionTaxes'
			 && $arr['valueDetail'][$valueSide]['idAccountTitle'] != 'suspensePaymentConsumptionTaxes'
		) {
			$flagConsumptionTaxGeneralRule = $arr['valueDetail'][$valueSide]['flagConsumptionTaxGeneralRule'];
			if (preg_match("/^tax/", $flagConsumptionTaxGeneralRule)
				&& (int) $arr['varsEntityNation']['flagConsumptionTaxGeneralRule']
			) {
				$flagTax = 1;
			}

			$flagConsumptionTaxSimpleRule = $arr['valueDetail'][$valueSide]['flagConsumptionTaxSimpleRule'];
			if (preg_match("/^tax/", $flagConsumptionTaxSimpleRule)
				&& !(int) $arr['varsEntityNation']['flagConsumptionTaxGeneralRule']
			) {
				$flagTax = 1;
			}

			if ($flagTax) {
				$flagConsumptionTaxWithoutCalc = $arr['valueDetail'][$valueSide]['flagConsumptionTaxWithoutCalc'];
				if ($flagConsumptionTaxWithoutCalc == 1 || $flagConsumptionTaxWithoutCalc == 2) {
					if ($valueSide == 'arrCredit') {
						$flagDebit = 0;

					} elseif ($valueSide == 'arrDebit') {
						$flagDebit = 1;
					}
				}
			}

		}

		$data = array(
			'flagTax'   => $flagTax,
			'flagDebit' => $flagDebit,
		);

		return $data;
	}

	/**
		(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent']
		))
	 */
	protected function _getVarsFixedAssets($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingFixedAssets' . $strNation,
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
					'value'         => $arr['numFiscalPeriod'],
				),
			),
		));

		return $rows['arrRows'][0];
	}


	/**
	 *
	 */
	protected function _getClass($arr)
	{
		$str = $arr['strClass'];

		if ($arr['flagAccounting']) {
			$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/' . $str . ".php";
		} else {
			$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/jpn/' . $str . ".php";
		}
		if (!file_exists($path)) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}
		require_once($path);
		if ($arr['flagAccounting']) {
			$strClass = 'Code_Else_Plugin_Accounting_' . $str;
		} else {
			$strClass = 'Code_Else_Plugin_Accounting_Jpn_' . $str;
		}
		$classCall = new $strClass;

		return $classCall;
	}

	/**
		$this->_getClassCalc(array(
			'flagType'   => 'LogCalc',
			'flagDetail' => 'LogCalc'
		))
	 */
	protected function _getClassCalc($arr)
	{
		$strNation = PLUGIN_ACCOUNTING_STR_NATION;
		$flag = 0;
		if ($arr['flagDetail']) {
			$strDir = 'calc' . ucwords($arr['flagType']);
			$strFile = $arr['flagDetail'];
			require_once(PATH_BACK_CLASS_ELSE_PLUGIN . $this->_self['strTitle'] . '/' . $strNation . '/' . $strDir . '/' . $strFile .'.php');
			if (PLUGIN_ACCOUNTING_FLAG_CORPORATION) {
				$path = PATH_BACK_CLASS_ELSE_PLUGIN . $this->_self['strTitle'] . '/' . $strNation . '/' . PLUGIN_ACCOUNTING_NUM_YEAR_SHEET . '/' . PLUGIN_ACCOUNTING_FLAG_CORPORATION  . '/' . $strDir . '/' . $strFile .'.php';
				if (!file_exists($path)) {
					$flag = 1;
				} else {
					require_once($path);
					$strClass = 'Code_Else_Plugin_Accounting_' . ucwords($strNation) . '_' . ucwords($strDir) .'_' . $strFile .'_' . PLUGIN_ACCOUNTING_NUM_YEAR_SHEET .'_' . ucwords(PLUGIN_ACCOUNTING_FLAG_CORPORATION);
				}
			} else {
				$flag = 1;
			}
			if ($flag) {
				$strNation = ucwords($strNation);
				$strDir = ucwords($strDir);
				$strClass = 'Code_Else_Plugin_Accounting_' . $strNation .'_' . $strDir .'_' . $strFile;
			}

		} else {
			require_once(PATH_BACK_CLASS_ELSE_PLUGIN . $this->_self['strTitle'] . '/' . $strNation . '/' . 'Calc' . $arr['flagType'] . '.php');
			if (PLUGIN_ACCOUNTING_FLAG_CORPORATION) {
				$strFile = 'Calc' . $arr['flagType'];
				$path = PATH_BACK_CLASS_ELSE_PLUGIN . $this->_self['strTitle'] . '/' . $strNation . '/' . PLUGIN_ACCOUNTING_NUM_YEAR_SHEET . '/' . PLUGIN_ACCOUNTING_FLAG_CORPORATION  . '/' . $strFile .'.php';
				if (!file_exists($path)) {
					$flag = 1;
				} else {
					require_once($path);
					$strNation = ucwords($strNation);
					$strClass = 'Code_Else_Plugin_Accounting_' . ucwords($strNation) . '_' . $strFile . '_' . PLUGIN_ACCOUNTING_NUM_YEAR_SHEET . '_' . ucwords(PLUGIN_ACCOUNTING_FLAG_CORPORATION);
				}
			} else {
				$flag = 1;
			}
			if ($flag) {
				$strNation = ucwords($strNation);
				$strClass = 'Code_Else_Plugin_Accounting_' . $strNation .'_' . 'Calc' . $arr['flagType'];
			}
		}

		$classCall = new $strClass($arr);
		return $classCall;
	}

	/**
		_getJsonFlag(array(
			'numFiscalPeriod' => '',
			'idTarget'      => '',
		));
	 */
	protected function _getJsonFlag($arr)
	{
		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$data = $varsEntityNation['jsonFlag'][$arr['idTarget']];

		return ($data)? $data : array();
	}

	/**
		_updateJsonFlag(array(
			'idTarget'   => '',
			'varsTarget' => array(),
			'numFiscalPeriod'=> '',
		));
	 */
	protected function _updateJsonFlag($arr)
	{
		global $varsPluginAccountingAccount;

		global $classDb;
		$dbh = $classDb->getHandle();

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$arrData = ($varsEntityNation['jsonFlag'])? $varsEntityNation['jsonFlag'] : array();
		$str = $arr['idTarget'];
		$arrData[$str] = ($arr['varsTarget'])? $arr['varsTarget'] : array();
		$jsonFlag = json_encode($arrData);

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingEntity' . $strNation,
			'arrColumn' => array('jsonFlag'),
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
					'value'         => $arr['numFiscalPeriod'],
				),
			),
			'arrValue'  => array($jsonFlag),
		));

		$this->_updateDbPreferenceStamp(array('strColumn' => 'entity'));
	}

	/**
		array(
			'vars'                => $arr['varsItem']['varsFS'][$str],
			'varsAuthority' => array(
				'flagInsert' => ($varsAuthority['flagAllInsert'])? 1 : 0,
				'flagUpdate' => ($varsAuthority['flagAllUpdate'])? 1 : 0,
				'flagDelete' => ($varsAuthority['flagAllDelete'])? 1 : 0,
			),
			'arrIdAccountTitle'   => $arr['arrIdAccountTitle'],
			'arrIdAccountTitleTemp' => $arr['varsItem']['varsJgaapFSTemp']['arrIdAccountTitle'],
			'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
			'idTarget'            => $arr['idTarget'],
			'flagFS'              => $arr['flagFS'],
		)
	 */
	protected function _getFlagUseLog($arr)
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		if (is_null($arr['varsAuthority'])) {
			$arr['varsAuthority'] = array();
		}

		$classCalcAccountTitle = &$arr['classCalcAccountTitle'];

		$array = &$arr['vars'];
		foreach ($array as $key => $value) {
			if (!is_null($value['vars']['flagUse'])) {

				$flag = 0;
				if (is_null($arr['idTarget'])) {
					$flag = 1;

				} else {
					if ($value['vars']['idTarget'] == $arr['idTarget']) {
						$flag = 1;
					}
				}

				if ($flag) {
					$flagCurrentFlagNow = '';
					$flagFS = '';
					$flagUseAccountTitle = 0;
					$flagUseAccountTitleTemp = 0;
					$flagUseAccountTitles = 0;
					$flagUseLog = 0;
					$flagUseLogElse = 0;
					$flagUseLogTemp = 0;
					$flagUseLogElseTemp = 0;
					$flagUseAllLog = 0;
					$flagUseAllLogElse = 0;
					$flagUseStock = 0;
					$flagUseStockTemp = 0;
					$flagUseCash = 0;
					$flagUseCashTemp = 0;

					//mean start stock
					$flagUseAllStock = 0;

					$array[$key]['vars']['varsAuthority'] = $arr['varsAuthority'];

					$arrIdTarget = $arr['arrIdAccountTitle'][$value['vars']['idTarget']];
					if ($arrIdTarget) {
						//AccountTitleFS FSCS
						$flagUseAccountTitle = 1;
						$flagUseAccountTitleTemp = 1;

					} else {
						if ($arr['arrIdAccountTitleTemp']) {
							$flagUseAccountTitleTemp = $arr['arrIdAccountTitleTemp'][$value['vars']['idTarget']];
							$flagUseAccountTitleTemp = ($flagUseAccountTitleTemp)? 1 : 0;
						}
						if ($arr['arrIdAccountTitles']) {
							$arrayLoop = $arr['arrIdAccountTitles'];
							foreach ($arrayLoop as $keyLoop => $valueLoop) {
								$flagUseAccountTitles = $valueLoop['arrIdAccountTitle'][$value['vars']['idTarget']];
								if ($flagUseAccountTitles) {
									$flagUseAccountTitles = 1;
									break;
								}
							}
						}
						$arrIdTarget = array();
						$arrIdTarget[$value['vars']['idTarget']] = 'dummy';
					}

					//AccountTitle only use
					if ($arr['varsCash'][$value['vars']['idTarget']]) {
						$flagUseCash = 1;
					}

					//AccountTitle only use
					if ($arr['varsCashTemp'][$value['vars']['idTarget']]) {
						$flagUseCashTemp = 1;
					}

					//AccountTitle AccountTitleCS only use these flag
					if (is_null($arr['arrIdAccountTitle'])) {

						if ($arr['flagFS'] == 'BS') {
							$arrIdTargetCheck = $classCalcAccountTitle->allot(array(
								'flagStatus'        => 'balanceCheck',
								'arrIdAccountTitle' => $arrIdTarget,
								'numFiscalPeriod'   => $arr['numFiscalPeriod'],
							));
							foreach ($arrIdTargetCheck as $keyTargetCheck => $valueTargetCheck) {
								if ($valueTargetCheck) {
									$flagUseStock = 1;
									$flagUseAllStock = 1;
									break;
								}
							}

							if (!$flagUseStock) {
								$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
								$numFiscalPeriod = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriodStart'];
								$arrIdTargetCheck = $classCalcAccountTitle->allot(array(
									'flagStatus'        => 'balanceCheck',
									'arrIdAccountTitle' => $arrIdTarget,
									'numFiscalPeriod'   => $numFiscalPeriod,
								));
								foreach ($arrIdTargetCheck as $keyTargetCheck => $valueTargetCheck) {
									if ($valueTargetCheck) {
										$flagUseAllStock = 1;
										break;
									}
								}
							}

							if ($arr['numFiscalPeriodTemp']) {
								$arrIdTargetCheck = $classCalcAccountTitle->allot(array(
									'flagStatus'        => 'balanceCheck',
									'arrIdAccountTitle' => $arrIdTarget,
									'numFiscalPeriod'   => $arr['numFiscalPeriodTemp'],
								));
								foreach ($arrIdTargetCheck as $keyTargetCheck => $valueTargetCheck) {
									if ($valueTargetCheck) {
										$flagUseStockTemp = 1;
										break;
									}
								}
							}
						}

						if ($flagUseStock) {
							$flagUseLog = 1;
							$flagUseLogElse = 1;
							$flagUseAllLog = 1;
							$flagUseAllLogElse = 1;
						}

						if ($flagUseAllStock) {
							$flagUseAllLog = 1;
							$flagUseAllLogElse = 1;
						}

						if ($flagUseStockTemp) {
							$flagUseLogTemp = 1;
							$flagUseLogElseTemp = 1;
						}

						if (!$flagUseLogElse) {
							$flagUseLogElse = $this->_checkUseLog(array(
								'numFiscalPeriod' => $arr['numFiscalPeriod'],
								'arrIdTarget'     => $arrIdTarget,
								'flagElse'        => 1,
							));

							if ($flagUseLogElse) {
								$flagUseLog = 1;
								$flagUseLogElse = 1;
								$flagUseAllLog = 1;
								$flagUseAllLogElse = 1;
							}
						}

						if (!$flagUseLog) {
							$flagUseLog = $this->_checkUseLog(array(
								'numFiscalPeriod' => $arr['numFiscalPeriod'],
								'arrIdTarget'     => $arrIdTarget,
							));
							if ($flagUseLog) {
								$flagUseAllLog = 1;
							}
						}

						if ($arr['numFiscalPeriodTemp']) {
							if (!$flagUseLogElseTemp) {
								$flagUseLogElseTemp = $this->_checkUseLog(array(
									'numFiscalPeriod' => $arr['numFiscalPeriodTemp'],
									'arrIdTarget'     => $arrIdTarget,
									'flagElse'        => 1,
								));
								if ($flagUseLogElseTemp) {
									$flagUseLogTemp = 1;
								}
							}
							if (!$flagUseLogTemp) {
								$flagUseLogTemp = $this->_checkUseLog(array(
									'numFiscalPeriod' => $arr['numFiscalPeriodTemp'],
									'arrIdTarget'     => $arrIdTarget,
								));
							}
						}

						if (!$flagUseAllLogElse) {
							$flagUseAllLogElse = $this->_checkUseLog(array(
								'arrIdTarget' => $arrIdTarget,
								'flagElse'    => 1,
								'flagAll'     => 1,
							));
							if ($flagUseAllLogElse) {
								$flagUseAllLog = 1;
							}
						}

						if (!$flagUseAllLog) {
							$flagUseAllLog = $this->_checkUseLog(array(
								'arrIdTarget' => $arrIdTarget,
								'flagAll'     => 1,
							));
						}

					}

					if ($arr['flagFS']) {
						$flagFS = $arr['flagFS'];
					}

					if ($arr['flagCurrentFlagNow']) {
						$flagCurrentFlagNow = $arr['flagCurrentFlagNow'];
					}

					$array[$key]['vars']['flagFS'] = $flagFS;
					$array[$key]['vars']['flagUseCash'] = $flagUseCash;
					$array[$key]['vars']['flagUseCashTemp'] = $flagUseCashTemp;
					$array[$key]['vars']['flagUseStock'] = $flagUseStock;
					$array[$key]['vars']['flagUseStockTemp'] = $flagUseStockTemp;
					$array[$key]['vars']['flagUseAllStock'] = $flagUseAllStock;
					$array[$key]['vars']['flagUseLog'] = $flagUseLog;
					$array[$key]['vars']['flagUseLogElse'] = $flagUseLogElse;
					$array[$key]['vars']['flagUseLogTemp'] = $flagUseLogTemp;
					$array[$key]['vars']['flagUseLogElseTemp'] = $flagUseLogElseTemp;
					$array[$key]['vars']['flagUseAllLog'] = $flagUseAllLog;
					$array[$key]['vars']['flagUseAllLogElse'] = $flagUseAllLogElse;
					$array[$key]['vars']['flagUseAccountTitle'] = $flagUseAccountTitle;
					$array[$key]['vars']['flagUseAccountTitleTemp'] = $flagUseAccountTitleTemp;
					$array[$key]['vars']['flagUseAccountTitles'] = $flagUseAccountTitles;
					$array[$key]['vars']['flagCurrentFlagNow'] = $flagCurrentFlagNow;
				}
			}

			if ($value['child']) {
				$array[$key]['child'] = $this->_getFlagUseLog(array(
					'vars'                  => $array[$key]['child'],
					'varsCash'              => $arr['varsCash'],
					'varsCashTemp'          => $arr['varsCashTemp'],
					'arrIdAccountTitle'     => $arr['arrIdAccountTitle'],
					'arrIdAccountTitleTemp' => $arr['arrIdAccountTitleTemp'],
					'arrIdAccountTitles'    => $arr['arrIdAccountTitles'],
					'numFiscalPeriod'       => $arr['numFiscalPeriod'],
					'numFiscalPeriodTemp'   => $arr['numFiscalPeriodTemp'],
					'varsAuthority'         => $arr['varsAuthority'],
					'idTarget'              => $arr['idTarget'],
					'flagFS'                => $arr['flagFS'],
					'flagCurrentFlagNow'    => $arr['flagCurrentFlagNow'],
					'classCalcAccountTitle' => $arr['classCalcAccountTitle'],
				));
			}
		}

		return $array;
	}

	/**
		(array(
			''arrIdTarget' => array(),
			'idTarget' => '',
			'flagAll'  => '',
			'flagElse' => '',
			'numFiscalPeriod' => '',
		))
	 */
	protected function _checkUseLog($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;

		$array = $arr['arrIdTarget'];
		if (!$array) {
			$array = array();
			$array[$arr['idTarget']] = 1;
		}
		foreach ($array as $key => $value) {
			$strId = ',' . $key . ',';

			$arrWhere = array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idEntity',
					'flagCondition' => 'eq',
					'value'         => $varsPluginAccountingAccount['idEntityCurrent'],
				),
				array(
					'flagType'      => '',
					'strColumn'     => 'arrCommaIdAccountTitleVersion',
					'flagCondition' => 'like',
					'value'         => $strId,
				),
			);
			if ($arr['flagElse']) {
				$arrWhere[] = array(
					'flagType'      => 'num',
					'strColumn'     => 'flagRemove',
					'flagCondition' => 'ne',
					'value'         => 1,
				);
			}
			if (!$arr['flagAll']) {
				$arrWhere[] = array(
					'flagType'      => 'num',
					'strColumn'     => 'numFiscalPeriod',
					'flagCondition' => 'eq',
					'value'         => $arr['numFiscalPeriod'],
				);

			}
			$rows = $classDb->getSelect(array(
				'idModule' => 'accounting',
				'strTable' => 'accountingLog',
				'arrLimit' => array(
					'numStart' => 0, 'numEnd' => 1,
				),
				'arrOrder' => array(),
				'flagAnd'  => 1,
				'arrWhere' => $arrWhere,
			));

			if ($rows['numRows']) {
				return 1;
			}
		}

		return 0;
	}

	/**
	 (array(
		 'numFiscalPeriod' => '',
	 ))
	 */
	protected function _getVarsFSRows($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);
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
					'value'         => $varsPluginAccountingAccount['idEntityCurrent'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'numFiscalPeriod',
					'flagCondition' => 'eqSmall',
					'value'         => $arr['numFiscalPeriod'],
				),
			),
		));

		return $rows['arrRows'];
	}

	/**
		array(
			'strClass' => ''
		)
	 */
	protected function _setClassExt($arr)
	{
		$str = $arr['strClass'];
		$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/jpn/' . $str . ".php";
		if (!file_exists($path)) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}
		require_once($path);

		if (PLUGIN_ACCOUNTING_FLAG_CORPORATION) {
			$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/jpn/' . PLUGIN_ACCOUNTING_NUM_YEAR_SHEET . '/' . PLUGIN_ACCOUNTING_FLAG_CORPORATION  . '/' . $str .'.php';
			if (!file_exists($path)) {
				$flag = 1;
			} else {
				require_once($path);
				$strClass = 'Code_Else_Plugin_Accounting_Jpn_' . $str .'_' . PLUGIN_ACCOUNTING_NUM_YEAR_SHEET .'_' . ucwords(PLUGIN_ACCOUNTING_FLAG_CORPORATION);
			}
		} else {
			$flag = 1;
		}
		if ($flag) {
			$strClass = 'Code_Else_Plugin_Accounting_Jpn_' . $str;
		}

		if (class_exists($strClass)) {
			$classCall = new $strClass;
			$classCall->run();
		}

		exit;
	}
}
