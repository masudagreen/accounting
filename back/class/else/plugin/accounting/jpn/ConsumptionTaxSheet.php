<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_ConsumptionTaxSheet extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference' => 'consumptionTaxSheetWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/consumptionTaxSheet.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/consumptionTaxSheet.php',
		'pathTplHtml'  => 'else/plugin/accounting/html/consumptionTax.html',
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

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));
		$flagConsumptionTaxFree = (int) $varsEntityNation['flagConsumptionTaxFree'];

		if ($flagConsumptionTaxFree) {
			$this->_sendOld();
			exit;
		}

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
		global $varsPluginAccountingAccount;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsFlag' => $vars['varsFlag'],
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars'             => &$vars,
			'varsItem'         => $varsItem,
			'varsEntityNation' => $varsEntityNation,
		)));

		$vars = $this->_updateVars(array(
			'vars'             => $vars,
			'varsEntityNation' => $varsEntityNation,
			'varsItem'         => $varsItem,
			'varsFlag'         => $vars['varsFlag'],
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
			'vars'     => $vars,
			'varsFlag' => $vars['varsFlag'],
		))
	 */
	protected function _getVarsItem($arr)
	{
		global $classDb;
		global $varsAccount;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrAccountTitle = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => array(),
			'numFiscalPeriod'    => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsFSValue = $this->_getVarsFSValue(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsAccountTitleFSList = array();
		$varsAccountTitleList = array();
		$array = $this->_extendSelf['arrFS'];
		foreach ($array as $key => $value) {
			if ($value == 'CR') {
				if (!(int) $varsEntityNation['flagCR']) {
					continue;
				}
			}
			$varsAccountTitle = $this->_getArrSelectOption(array(
				'arrStrTitle'     => array(),
				'arrSelectTag'    => array(),
				'vars'            => $varsFS['jsonJgaapAccountTitle' . $value],
				'flagBS'          => ($value == 'BS')? 1 : 0,
				'flagFS'          => $value,
			));

			$varsAccountTitleList = array_merge($varsAccountTitleList, $varsAccountTitle['arrStrTitle']);
			$varsAccountTitleFSList[$value] = $varsAccountTitle['arrStrTitle'];
		}

		$varsFSValues = array();
		$flagFiscalPeriod = $arr['varsFlag']['flagFiscalPeriod'];
		$varsFSValues = $varsFSValue['jsonConsumptionTax'][$flagFiscalPeriod];

		$varsConsumptionTax = $this->_getVarsConsumptionTax(array());

		$data = array(
			'arrAccountTitle'        => $arrAccountTitle,
			'varsFS'                 => $varsFSValues,
			'varsConsumptionTax'     => $varsConsumptionTax,
			'varsEntityNation'       => $varsEntityNation,
			'varsAccountTitleList'   => $varsAccountTitleList,
			'varsAccountTitleFSList' => $varsAccountTitleFSList,
		);

		return $data;

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
		$varsEntityNation = &$arr['varsEntityNation'];
		$varsItem = &$arr['varsItem'];

		$arrayNew = array();
		$array = &$vars['portal']['varsNavi']['templateDetail'];
		foreach ($array as $key => $value) {
			$method = '_updateVarsNavi' . $value['id'];
			if (method_exists($this, $method)) {
				$value = $this->$method(array(
					'vars'             => $value,
					'varsItem'         => $varsItem,
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
			'vars'             => $value,
			'varsItem'         => $varsItem,
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _updateVarsNaviIdAccountTitle($arr)
	{
		$arrSelectTag = $arr['varsItem']['arrAccountTitle']['arrSelectTag'];
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
	protected function _updateVarsNaviFlagFiscalPeriod($arr)
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
		$arr['vars']['numSize'] = count($arrayOption);

		return $arr['vars'];
	}

	/**
		(array(
			'vars'             => $vars,
			'varsEntityNation' => $varsEntityNation,
			'varsItem'         => $varsItem,
			'varsFlag'         => array(
				'flagFiscalPeriod'  => $flagFiscalPeriod,
			),
			'flagOutput'       => ($arr['flagOutput'])? 1 : 0,
		))
	 */
	protected function _updateVars($arr)
	{
		$arr['vars']['portal']['varsDetail']['varsDetail']['varsHtml'] = $this->_getDetailHtml(array(
			'varsFS'           => $arr['varsItem']['varsFS'],
			'varsItem'         => $arr['varsItem'],
			'varsFlag'         => $arr['varsFlag'],
			'varsEntityNation' => $arr['varsEntityNation'],
			'flagOutput'       => ($arr['flagOutput'])? 1 : 0,
		));

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllOutput'])) {
			$arr['vars']['portal']['varsDetail']['varsStart']['varsEdit']['flagOutputUse'] = 0;
		}

		return $arr['vars'];
	}


	/**

	 */
	protected function _getDetailHtml($arr)
	{
		global $classSmarty;

		$flagConsumptionTaxIncluding = (int) $arr['varsEntityNation']['flagConsumptionTaxIncluding'];
		$varsValue = $arr['varsItem']['varsFS'];
		$flagFiscalPeriod = $arr['varsFlag']['flagFiscalPeriod'];
		$numRateConsumptionTax = $arr['varsFlag']['numRateConsumptionTax'];
		$arrayRate = array(5, 8, 10);
		if ($numRateConsumptionTax) {
			$arrayRate = array($numRateConsumptionTax);;
		}

		$varsData = $arr['varsItem']['varsConsumptionTax']['arrStr'];

		$str = 'simple';
		if ((int) $arr['varsEntityNation']['flagConsumptionTaxGeneralRule']) {
			if ((int) $arr['varsEntityNation']['flagConsumptionTaxDeducted']) {
				$str = 'generalEach';

			} else {
				$str = 'generalProration';
			}
		}

		$varsIni = array();
		$varsIni['inBody'] = 0;
		$varsIni['outBody'] = 0;
		$varsIni['otherBody'] = 0;
		$varsIni['includeBody'] = 0;
		$varsIni['totalBody'] = 0;
		$varsIni['inTax'] = 0;
		$varsIni['outTax'] = 0;
		$varsIni['otherTax'] = 0;
		$varsIni['totalTax'] = 0;
		$varsIni['inSum'] = 0;
		$varsIni['outSum'] = 0;
		$varsIni['otherSum'] = 0;
		$varsIni['includeSum'] = 0;
		$varsIni['totalSum'] = 0;

		$strBlank = ($arr['flagOutput'])? '' : '-';
		$arrayNew = array();
		$array = $arr['varsItem']['varsConsumptionTax'][$str];
		foreach ($array as $key => $value) {
			$value['flagTax'] = 0;
			if (preg_match("/^tax/", $value['value'])) {
				$value['flagTax'] = 1;
			}

			foreach ($varsIni as $keyIni => $valueIni) {
				$value[$keyIni] = $valueIni;
			}

			if ($varsValue[$value['value']]) {
				$dataValue = array();
				$arrayValue = array();
				if (preg_match("/^tax/", $value['value'])
					|| preg_match("/^else/", $value['value'])
				) {
					foreach ($arrayRate as $keyRate => $valueRate) {
						$arrayValue = $varsValue[$value['value']][$valueRate];
						foreach ($arrayValue as $keyValue => $valueValue) {
							$dataValue[$keyValue] += $valueValue;
							$value[$keyValue] = ($arr['flagOutput'])? $dataValue[$keyValue] : number_format($dataValue[$keyValue]);
						}
					}

				} else {
					$arrayValue = $varsValue[$value['value']];
					foreach ($arrayValue as $keyValue => $valueValue) {
						$dataValue[$keyValue] = $valueValue;
						$value[$keyValue] = ($arr['flagOutput'])? $dataValue[$keyValue] : number_format($dataValue[$keyValue]);
					}
				}
			}

			if ($value['flagTax']) {
				if ($flagConsumptionTaxIncluding) {
					$value['inBody'] = $strBlank;
					$value['outBody'] = $strBlank;
					$value['otherBody'] = $strBlank;
					$value['inTax'] = $strBlank;
					$value['outTax'] = $strBlank;
					$value['inSum'] = $strBlank;
					$value['outSum'] = $strBlank;

				} else {
					$value['includeBody'] = $strBlank;
					$value['includeSum'] = $strBlank;
				}
			}

			if ($value['value'] == 'free-Securities' || $value['value'] == 'free-MonetaryClaim') {
				$totalSum = 0;
				if (!($dataValue['totalSum'] == '' || $dataValue['totalSum'] == 0)) {
					$totalSum = $this->_updateCalc(array(
						'flagType' => 'floor',
						'num'      => $dataValue['totalSum'] * 5 / 100,
						'numLevel' => 0
					));
				}
				$value['totalSum5'] = ($arr['flagOutput'])? $totalSum : number_format($totalSum);
			}

			if ($value['value'] != 'none') {
				$arrayNew[] = $value;
			}
		}
		$varsData['arrData'] = $arrayNew;
		if ($arr['flagOutput']) {
			return $varsData;
		}

		$array = $varsData;
		foreach ($array as $key => $value) {
			$classSmarty->assign($key, $value);
		}
		$contents = $classSmarty->fetch($this->_extSelf['pathTplHtml']);

		return $contents;
	}

	/**
	 *
	 */
	protected function _iniDetailOutput()
	{
		global $classRequest;

		global $varsRequest;
		global $varsPluginAccountingPreference;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'output',
			'idTarget'      => $this->_extSelf['idPreference'],
		));

		if (!$flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsFlag = array(
			'flagFiscalPeriod'      => $varsRequest['query']['jsonValue']['vars']['FlagFiscalPeriod'],
			'numRateConsumptionTax' => $varsRequest['query']['jsonValue']['vars']['NumRateConsumptionTax'],
		);

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars'             => &$vars,
			'varsItem'         => $varsItem,
			'varsEntityNation' => $varsEntityNation,
		)));

		$this->_checkValueDetail(array(
			'varsDetail' => $vars['portal']['varsNavi']['varsDetail'],
			'varsFlag'   => $varsFlag,
			'flagOutput' => 1,
		));

		$vars = $this->_updateVars(array(
			'vars'             => $vars,
			'varsEntityNation' => $varsEntityNation,
			'varsItem'         => $varsItem,
			'varsFlag'         => $varsFlag,
			'flagOutput'       => 1,
		));

		$text = $this->_getCsv(array(
			'varsFlag' => $varsFlag,
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$text = mb_convert_encoding($text, 'sjis', 'utf8');

		$strFileName = $this->_getFileTitle(array(
			'strMenu'     => $vars['varsItem']['varsMenu']['strList'],
			'strFileType' => 'csv',
		));

		$classRequest->output(array(
			'text'         => $text,
			'strFileType'  => 'csv',
			'strFileName'  => $strFileName,
		));
	}

	/**
		'varsFlag' => $varsFlag,
		'vars'     => $vars,
		'varsItem' => $varsItem,
	 */
	protected function _getCsv($arr)
	{
		global $classFile;

		global $varsRequest;
		global $varsAccounts;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];

		$arrayCsv = array();

		//strEntity
		$strEntityRep = $varsPluginAccountingEntity[$idEntity]['strTitle'];
		$strEntity = str_replace('<%replace%>', $strEntityRep, $arr['vars']['varsItem']['strEntity']);
		$arrayCsv[] = array($strEntity);

		//strNum
		if (preg_match("/^f1$/", $arr['varsFlag']['flagFiscalPeriod'])) {
			$strNumRep = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
			$strNum = str_replace('<%replace%>', $strNumRep, $arr['vars']['varsItem']['strNum']);
			$arrayCsv[] = array($strNum);
		}

		//strPeriod
		$varsPeriod = $this->_getVarsFiscalPeriod(array(
			'flagFiscalPeriod' => $arr['varsFlag']['flagFiscalPeriod'],
			'numFiscalPeriod'  => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$str = $arr['vars']['varsItem']['strPeriod'];
		$strPeriod = str_replace('<%strStartHeisei%>', $varsPeriod['numStartHeisei'], $str);
		$strPeriod = str_replace('<%strEndHeisei%>', $varsPeriod['numEndHeisei'], $strPeriod);
		$strPeriod = str_replace('<%strStartMonth%>', $varsPeriod['numStartMonth'], $strPeriod);
		$strPeriod = str_replace('<%strEndMonth%>', $varsPeriod['numEndMonth'], $strPeriod);
		$arrayCsv[] = array($strPeriod);

		//strRate
		if ((int) $arr['varsFlag']['numRateConsumptionTax'] != 0) {
			$strNumRep = $arr['varsFlag']['numRateConsumptionTax'];
			$strNum = str_replace('<%replace%>', $strNumRep, $arr['vars']['varsItem']['strRate']);
			$arrayCsv[] = array($strNum);
		}

		//strUnit
		$arrayCsv[] = array($arr['vars']['varsItem']['strUnit']);

		$varsCsv = $arr['vars']['portal']['varsDetail']['varsDetail']['varsHtml'];
		//column
		$arrayCsv[] = array($varsCsv['strItem'], '', $varsCsv['strIn'], $varsCsv['strOut'], $varsCsv['strOther'], $varsCsv['strInclude'], $varsCsv['strTotal']);

		$array = $varsCsv['arrData'];
		foreach ($array as $key => $value) {
			if ($value['flagTax']) {
				$arrayCsv[] = array($value['strTitle'], $varsCsv['strBody'], $value['inBody'], $value['outBody'], $value['otherBody'], $value['includeBody'], $value['totalBody']);
				$arrayCsv[] = array('', $varsCsv['strTax'], $value['inTax'], $value['outTax'], $value['otherTax'], '', $value['totalTax']);
				$arrayCsv[] = array('', $varsCsv['strSum'], $value['inSum'], $value['outSum'], $value['otherSum'], $value['includeSum'], $value['totalSum']);
			} else {
				if ($value['value'] == 'free-Securities' || $value['value'] == 'free-MonetaryClaim') {
					$arrayCsv[] = array($value['strTitle'], $varsCsv['strSecurities5'], '', '', '', '', $value['totalSum5']);
					$arrayCsv[] = array('', $varsCsv['strSecurities'], '', '', '', '', $value['totalSum']);

				} else {
					$arrayCsv[] = array($value['strTitle'], '', '', '', '', '', $value['totalSum']);
				}
			}

		}

		$text = $classFile->getCsvText(array(
			'delimiter' => ',',
			'rows'      => $arrayCsv,
		));

		return $text;
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
			'flagFiscalPeriod'      => $varsRequest['query']['jsonValue']['vars']['FlagFiscalPeriod'],
			'numRateConsumptionTax' => $varsRequest['query']['jsonValue']['vars']['NumRateConsumptionTax'],
		);

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars'             => &$vars,
			'varsItem'         => $varsItem,
			'varsEntityNation' => $varsEntityNation,
		)));

		$this->_checkValueDetail(array(
			'varsDetail' => $vars['portal']['varsNavi']['varsDetail'],
			'varsFlag'   => $varsFlag,
		));

		$vars = $this->_updateVars(array(
			'vars'             => $vars,
			'varsEntityNation' => $varsEntityNation,
			'varsItem'         => $varsItem,
			'varsFlag'         => $varsFlag,
		));

		$this->sendVars(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'varsDetail' => $vars['portal']['varsDetail']['varsDetail'],
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
	protected function _iniDetailReload()
	{
		$this->_setSearch();
	}

}
