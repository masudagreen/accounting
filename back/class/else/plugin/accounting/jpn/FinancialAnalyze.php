<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_FinancialAnalyze extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference' => 'financialAnalyzeWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/financialAnalyze.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/financialAnalyze.php',
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
			'vars' => $vars,
		));

		$vars = $this->_updateVars(array(
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
			'vars'     => $vars,
			'varsFlag' => $vars['varsFlag'],
		))
	 */
	protected function _getVarsItem($arr)
	{
		global $varsPluginAccountingAccount;

		$varsFSValue = $this->_getVarsFSValue(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsFlagFiscalPeriod = $this->_getVarsFlagFiscalPeriod(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsStrFlagFiscalPeriod = $this->_getVarsStrFlagFiscalPeriod(array(
			'vars'             => $arr['vars']['varsItem']['tmplFiscalPeriod'],
			'varsEntityNation' => $varsEntityNation,
		));

		$data = array(
			'varsFSValue'             => $varsFSValue,
			'varsEntityNation'        => $varsEntityNation,
			'varsFlagFiscalPeriod'    => $varsFlagFiscalPeriod,
			'varsStrFlagFiscalPeriod' => $varsStrFlagFiscalPeriod,
		);

		return $data;

	}

	/**
		(array(
			'vars'             => $value,
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _getVarsStrFlagFiscalPeriod($arr)
	{
		$varsEntityNation = $arr['varsEntityNation'];

		$arrayNew = array();
		$array = $arr['vars']['arrayOption'];
		foreach ($array as $key => $value) {
			if ($varsEntityNation['numFiscalTermMonth'] == 12) {
				$arrayNew[] = $value;

			} else {
				if (preg_match( "/^(f1)$/", $value['value'])) {
					$arrayNew[] = $value;
				}
			}
		}
		$arr['vars']['arrayOption'] = $arrayNew;

		$arrayOption = array();
		if ($varsEntityNation['numFiscalTermMonth'] == 12) {
			$arrayOption = $arr['vars']['arrayOption'];

		} else {
			$arrayOption[] = $arr['vars']['varsPeriod'];
		}

		$numMonth = (int) $varsEntityNation['numFiscalBeginningMonth'];
		$numEnd = (int) $varsEntityNation['numFiscalTermMonth'];
		for ($i = 0; $i < $numEnd; $i++) {
			$data = array(
				'strTitle' => $numMonth . $arr['vars']['strMonth'],
				'value'    => $numMonth,
			);
			$arrayOption[] = $data;
			$numMonth++;
			if ($numMonth > 12) {
				$numMonth = 1;
			}
		}

		$varsStr = array();
		$array = $arrayOption;
		foreach ($array as $key => $value) {
			$varsStr[$value['value']] = $value['strTitle'];
		}

		return $varsStr;
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
		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllOutput'])) {
			$arr['vars']['portal']['varsDetail']['varsStart']['varsEdit']['flagOutputUse'] = 0;
			$arr['vars']['portal']['varsDetail']['varsEdit']['flagOutputUse'] = 0;
		}

		$varsBase = array();
		$array = $arr['varsItem']['varsStrFlagFiscalPeriod'];
		foreach ($array as $key => $value) {
			$varsBase[$key] = $this->_getVarsDetailValue(array(
				'varsFSValue'      => &$arr['varsItem']['varsFSValue'],
				'flagFiscalPeriod' => $key,
			));
		}
		$arr['vars']['portal']['varsDetail']['varsCollect']['varsBase'] = $varsBase;
		$arr['vars']['portal']['varsDetail']['varsCollect']['varsLabel'] = $arr['vars']['varsItem']['varsRow'];

		$arrayNew = array();
		$array = $arr['vars']['varsItem']['varsRow'];
		foreach ($array as $key => $value) {
			$arrayNew[] = $key;
		}
		$arr['vars']['portal']['varsDetail']['varsCollect']['varsLabelId'] = $arrayNew;

		//varsFlagFiscalPeriod
		$arr['vars']['portal']['varsDetail']['varsCollect']['varsFlagFiscalPeriod'] = $arr['varsItem']['varsFlagFiscalPeriod'];
		$arr['vars']['portal']['varsDetail']['varsCollect']['varsStrFlagFiscalPeriod'] = $arr['varsItem']['varsStrFlagFiscalPeriod'];


		$arr['vars']['portal']['varsDetail']['varsDetail'] = $this->_updateVarsDetail((array(
			'vars'     => &$arr['vars'],
			'varsItem' => $arr['varsItem'],
			'varsBase' => $varsBase,
		)));

		return $arr['vars'];
	}

	/**

	 */
	protected function _getVarsDetailValue($arr)
	{
		$varsFSValue = $arr['varsFSValue'];
		$flagFiscalPeriod = $arr['flagFiscalPeriod'];
		$varsData = array();
		$strBlank = '0.000';

		//当期純利益
		$numCurrentTermProfitOrLossNet = $varsFSValue['jsonJgaapFSPL'][$flagFiscalPeriod]['currentTermProfitOrLossNet']['sumNext'];
		$flagCurrentTermProfitOrLossNet = (is_null($numCurrentTermProfitOrLossNet))? 0 : 1;
		$numCurrentTermProfitOrLossNet = ($flagCurrentTermProfitOrLossNet)? $numCurrentTermProfitOrLossNet : 0;

		//売上高
		$numSalesSum = $varsFSValue['jsonJgaapFSPL'][$flagFiscalPeriod]['salesSum']['sumNext'];
		$flagSalesSum = (is_null($numSalesSum))? 0 : 1;
		$numSalesSum = ($flagSalesSum)? $numSalesSum : 0;

		//総資本
		$numAssetsSum = $varsFSValue['jsonJgaapFSBS'][$flagFiscalPeriod]['assetsSum']['sumNext'];
		$flagAssetsSum = (is_null($numAssetsSum))? 0 : 1;
		$numAssetsSum = ($flagAssetsSum)? $numAssetsSum : 0;

		//自己資本
		$numNetAssetsSum = $varsFSValue['jsonJgaapFSBS'][$flagFiscalPeriod]['netAssetsSum']['sumNext'];
		$flagNetAssetsSum = (is_null($numNetAssetsSum))? 0 : 1;
		$numNetAssetsSum = ($flagNetAssetsSum)? $numNetAssetsSum : 0;

		//流動資産
		$numCurrentAssetsSum = $varsFSValue['jsonJgaapFSBS'][$flagFiscalPeriod]['currentAssetsSum']['sumNext'];
		$flagCurrentAssetsSum = (is_null($numCurrentAssetsSum))? 0 : 1;
		$numCurrentAssetsSum = ($flagCurrentAssetsSum)? $numCurrentAssetsSum : 0;

		//流動負債
		$numCurrentLiabilitiesSum = $varsFSValue['jsonJgaapFSBS'][$flagFiscalPeriod]['currentLiabilitiesSum']['sumNext'];
		$flagCurrentLiabilitiesSum = (is_null($numCurrentLiabilitiesSum))? 0 : 1;
		$numCurrentLiabilitiesSum = ($flagCurrentLiabilitiesSum)? $numCurrentLiabilitiesSum : 0;

		//現金預金
		$numCashAndTimeDepositsSum = $varsFSValue['jsonJgaapFSBS'][$flagFiscalPeriod]['cashAndTimeDepositsSum']['sumNext'];
		$flagCashAndTimeDepositsSum = (is_null($numCashAndTimeDepositsSum))? 0 : 1;
		$numCashAndTimeDepositsSum = ($flagCashAndTimeDepositsSum)? $numCashAndTimeDepositsSum : 0;

		//売上債権
		$numAccoutsReceivableTradeSum = $varsFSValue['jsonJgaapFSBS'][$flagFiscalPeriod]['accoutsReceivableTradeSum']['sumNext'];
		$flagAccoutsReceivableTradeSum = (is_null($numAccoutsReceivableTradeSum))? 0 : 1;
		$numAccoutsReceivableTradeSum = ($flagAccoutsReceivableTradeSum)? $numAccoutsReceivableTradeSum : 0;

		//有価証券
		$numSecuritiesWrapSum = $varsFSValue['jsonJgaapFSBS'][$flagFiscalPeriod]['securitiesWrapSum']['sumNext'];
		$flagSecuritiesWrapSum = (is_null($numSecuritiesWrapSum))? 0 : 1;
		$numSecuritiesWrapSum = ($flagSecuritiesWrapSum)? $numSecuritiesWrapSum : 0;

		//当座
		$numTempSum = $numCashAndTimeDepositsSum + $numAccoutsReceivableTradeSum + $numSecuritiesWrapSum;

		//固定資産
		$numFixedAssetsSum = $varsFSValue['jsonJgaapFSBS'][$flagFiscalPeriod]['fixedAssetsSum']['sumNext'];
		$flagFixedAssetsSum = (is_null($numFixedAssetsSum))? 0 : 1;
		$numFixedAssetsSum = ($flagFixedAssetsSum)? $numFixedAssetsSum : 0;

		//固定負債
		$numFixedLiabilitiesSum = $varsFSValue['jsonJgaapFSBS'][$flagFiscalPeriod]['fixedLiabilitiesSum']['sumNext'];
		$flagFixedLiabilitiesSum = (is_null($numFixedLiabilitiesSum))? 0 : 1;
		$numFixedLiabilitiesSum = ($flagFixedLiabilitiesSum)? $numFixedLiabilitiesSum : 0;

		//長期
		$numLongSum = $numNetAssetsSum + $numFixedLiabilitiesSum;

		//'titleRoa' => 'ROA（総資本利益率）',
		//'strRoa' => '当期純利益 ÷ 総資本',
		$num = ($numAssetsSum == 0)? 0 : $numCurrentTermProfitOrLossNet / $numAssetsSum;
		$varsData['numRoa'] = number_format($num, 3, '.', '');
		$varsData['numRoaComma'] = number_format($num, 3);

		if (!$flagAssetsSum) {
			$varsData['numRoa'] = $strBlank;
			$varsData['numRoaComma'] = $strBlank;
		}

		//'titleRoe' => 'ROE（自己資本利益率）',
		//'strRoe' => '当期純利益 ÷ 自己資本',
		$num = ($numNetAssetsSum == 0)? 0 : $numCurrentTermProfitOrLossNet / $numNetAssetsSum;
		$varsData['numRoe'] = number_format($num, 3, '.', '');
		$varsData['numRoeComma'] = number_format($num, 3);

		if (!$flagNetAssetsSum) {
			$varsData['numRoe'] = $strBlank;
			$varsData['numRoeComma'] = $strBlank;
		}

		//'titleRos' => '売上高利益率',
		//'strRos' => '当期純利益 ÷ 売上高',
		$num = ($numSalesSum == 0)? 0 : $numCurrentTermProfitOrLossNet / $numSalesSum;
		$varsData['numRos'] = number_format($num, 3, '.', '');
		$varsData['numRosComma'] = number_format($num, 3);

		if (!$flagSalesSum) {
			$varsData['numRos'] = $strBlank;
			$varsData['numRosComma'] = $strBlank;
		}

		//'titleLoop' => '資産回転率',
		//'strLoop' => '売上高 ÷ 総資本',
		$num = ($numAssetsSum == 0)? 0 : $numSalesSum / $numAssetsSum;
		$varsData['numLoop'] = number_format($num, 3, '.', '');
		$varsData['numLoopComma'] = number_format($num, 3);
		if (!$flagAssetsSum) {
			$varsData['numLoop'] = $strBlank;
			$varsData['numLoopComma'] = $strBlank;
		}

		//'titleLeverage' => '財務レバレッジ',
		//'strLeverage' => '総資本 ÷ 自己資本',
		$num = ($numNetAssetsSum == 0)? 0 : $numAssetsSum / $numNetAssetsSum;
		$varsData['numLeverage'] = number_format($num, 3, '.', '');
		$varsData['numLeverageComma'] = number_format($num, 3);
		if (!$flagNetAssetsSum) {
			$varsData['numLeverage'] = $strBlank;
			$varsData['numLeverageComma'] = $strBlank;
		}

		//'titleQuick' => '流動比率',
		//'strQuick' => '流動資産 ÷ 流動負債',
		$num = ($numCurrentLiabilitiesSum == 0)? 0 : $numCurrentAssetsSum / $numCurrentLiabilitiesSum;
		$varsData['numQuick'] = number_format($num, 3, '.', '');
		$varsData['numQuickComma'] = number_format($num, 3);
		if (!$flagCurrentLiabilitiesSum) {
			$varsData['numQuick'] = $strBlank;
			$varsData['numQuickComma'] = $strBlank;
		}

		//'titleTemp' => '当座比率',
		//'strTemp' => '当座資産 ÷ 流動負債',
		$num = ($numCurrentLiabilitiesSum == 0)? 0 : $numTempSum / $numCurrentLiabilitiesSum;
		$varsData['numTemp'] = number_format($num, 3, '.', '');
		$varsData['numTempComma'] = number_format($num, 3);
		if (!$flagCurrentLiabilitiesSum) {
			$varsData['numTemp'] = $strBlank;
			$varsData['numTempComma'] = $strBlank;
		}

		//'titleCapital' => '自己資本比率',
		//'strCapital' => '自己資本 ÷ 総資本',
		$num = ($numAssetsSum == 0)? 0 : $numNetAssetsSum / $numAssetsSum;
		$varsData['numCapital'] = number_format($num, 3, '.', '');
		$varsData['numCapitalComma'] = number_format($num, 3);
		if (!$flagAssetsSum) {
			$varsData['numCapital'] = $strBlank;
			$varsData['numCapitalComma'] = $strBlank;
		}

		//'titleFix' => '固定長期適合率',
		//'strFix' => '固定資産 ÷ （自己資本＋固定負債）',
		$num = ($numLongSum == 0)? 0 : $numFixedAssetsSum / $numLongSum;
		$varsData['numFix'] = number_format($num, 3, '.', '');
		$varsData['numFixComma'] = number_format($num, 3);
		if (!$flagNetAssetsSum && !$flagFixedLiabilitiesSum) {
			$varsData['numFix'] = $strBlank;
			$varsData['numFixComma'] = $strBlank;
		}

		return $varsData;
	}

	/**
		(array(
			'vars'             => $vars,
			'varsItem'         => $arr['varsItem'],
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _updateVarsDetail($arr)
	{
		$vars = &$arr['vars'];
		$varsItem = &$arr['varsItem'];

		$arrayNew = array();
		$array = &$vars['portal']['varsDetail']['templateDetail'];
		foreach ($array as $key => $value) {
			$method = '_updateVarsDetail' . $value['id'];
			if (method_exists($this, $method)) {
				$value = $this->$method(array(
					'value'     => $value,
					'vars'      => $vars,
					'varsItem'  => $varsItem,
					'varsBase'  => $arr['varsBase'],
				));
			}
			$arrayNew[] = $value;
		}

		$vars['portal']['varsDetail']['templateDetail'] = $arrayNew;

		return $vars['portal']['varsDetail']['templateDetail'];
	}

	/**
		(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		))
	 */
	protected function _updateVarsDetailFlagFiscalPeriod($arr)
	{
		$varsEntityNation = $arr['varsItem']['varsEntityNation'];

		$arrayOption = array();
		if ($varsEntityNation['numFiscalTermMonth'] == 12) {
			$arrayOption = $arr['value']['varsTmpl']['arrayOption'];

		} else {
			$arrayOption = $arr['value']['varsTmpl']['varsPeriod'];
		}

		$arr['value']['arrayOption'] = $arrayOption;

		return $arr['value'];
	}

	protected function _updateVarsDetailTableF1($arr)
	{
		return $this-> _updateVarsDetailTable($arr);
	}

	protected function _updateVarsDetailTableF2($arr)
	{
		return $this-> _updateVarsDetailTable($arr);
	}

	protected function _updateVarsDetailTableF4($arr)
	{
		return $this-> _updateVarsDetailTable($arr);
	}

	protected function _updateVarsDetailTableMonth($arr)
	{
		return $this-> _updateVarsDetailTable($arr);
	}

	/**
		(array(
			'vars'      => $value,
			'varsItem'  => $varsItem,
			'varsData'  => $arr['varsData'],
		))
	 */
	protected function _updateVarsDetailTable($arr)
	{
		global $classHtml;

		$varsStrFlagFiscalPeriod = $arr['varsItem']['varsStrFlagFiscalPeriod'];
		$varsBase = $arr['varsBase'];

		$varsColumn = array('');
		$varsColumnWidth = array($arr['value']['tmplTable']['numWidthItem']);
		$varsColumnId = array('item');
		$array = $varsStrFlagFiscalPeriod;
		$numWidth = 0;
		foreach ($array as $key => $value) {
			if ($arr['value']['id'] == 'TableF1') {
				if (!preg_match("/^f1$/", $key)) {
					continue;
				}

			} else if ($arr['value']['id'] == 'TableF2') {
				if (!preg_match("/^f2/", $key)) {
					continue;
				}

			} else if ($arr['value']['id'] == 'TableF4') {
				if (!preg_match("/^f4/", $key)) {
					continue;
				}

			} else if ($arr['value']['id'] == 'TableMonth') {
				if (preg_match("/^f/", $key)) {
					continue;
				}
			}
			$varsColumn[] = $value;
			$varsColumnId[] = $key;
			$varsColumnWidth[] = $arr['value']['tmplTable']['numWidth'];
			$numWidth += $arr['value']['tmplTable']['numWidth'];
		}

		$arrayNew = array();
		$array = $arr['vars']['varsItem']['varsRow'];
		foreach ($array as $key => $value) {

			$varsDetail = $arr['value']['tmplTable']['tmplDetail'];
			$arrayColumn = $varsColumnId;
			foreach ($arrayColumn as $keyColumn => $valueColumn) {
				$varsDetail['varsDetail'][$valueColumn] = $arr['value']['tmplTable']['tmplData'];
				if ($valueColumn == 'item') {
					$varsDetail['varsDetail'][$valueColumn]['value'] = $value;
					$varsDetail['varsDetail'][$valueColumn]['strClass'] = $arr['value']['tmplTable']['strClassLeft'];
					$varsDetail['varsDetail'][$valueColumn]['flagOverflowUse'] = 1;
					continue;
				}
				$varsDetail['varsDetail'][$valueColumn]['value'] = $varsBase[$valueColumn][$key . 'Comma'];
			}
			$arrayNew[] = $varsDetail;
		}

		$arr['value']['tmplTable']['varsStatus']['varsColumnId'] = $varsColumnId;
		$arr['value']['tmplTable']['varsStatus']['varsColumnWidth'] = $varsColumnWidth;

		$varsTemp = $classHtml->allot(array(
			'strClass'     => 'TableSimple',
			'flagStatus'   => 'Html',
			'varsDetail'   => $arrayNew,
			'varsColumn'   => $varsColumn,
			'varsStatus'   => $arr['value']['tmplTable']['varsStatus'],
		));
		$arr['value']['varsSpace']['varsDetail']['strHtml'] = $varsTemp['strHtml'];

		return $arr['value'];
	}


	/**

	 */
	protected function _setSearch()
	{
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

		$this->sendVars(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'varsBase'   => $vars['portal']['varsDetail']['varsCollect']['varsBase'],
				'varsDetail' => $vars['portal']['varsDetail']['varsDetail'],
			),
		));
	}

	/**
	 *
	 */
	protected function _iniDetailOutput()
	{
		$this->_setClassExt(array('strClass' => 'FinancialAnalyzeOutput'));
	}


	/**
	 *
	 */
	protected function _iniDetailReload()
	{
		$this->_setSearch();
	}

}
