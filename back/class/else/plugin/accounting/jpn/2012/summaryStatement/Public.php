<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_2012_SummaryStatement_Public extends Code_Else_Plugin_Accounting_Jpn_SummaryStatement
{
	protected $_extSelf = array(
		'idPreference' => 'summaryStatementWindow',
		'flagReport'   => '2012',
		'flagDetail'   => 'public',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/2012/summaryStatement/public.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/2012/summaryStatement/public.php',
		'pathItem'     => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/2012/summaryStatement/publicItem.php',
		'pathTplHtml'  => 'else/plugin/accounting/html/2012/summaryStatement/public<%replace%>.html',
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

		if ($varsRequest['query']['child']) {
			$strChild = ucwords($varsRequest['query']['child']);
			$strDetail = ucwords($this->_extSelf['flagDetail']);
			$str = $strDetail . $strChild;

			$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/jpn/' . $this->_extSelf['flagReport'] . '/summaryStatement/' . $str . ".php";
			if (!file_exists($path)) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
				}
				exit;
			}
			require_once($path);
			$strClass = 'Code_Else_Plugin_Accounting_Jpn_' . $this->_extSelf['flagReport'] . '_SummaryStatement_' . $str;
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
		global $varsPluginAccountingAccount;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsSave' => $varsSave,
			'varsFlag' => $vars['varsFlag'],
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
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
			'vars'     => $vars,
		))
	 */
	protected function _getVarsItem($arr)
	{
		global $varsPluginAccountingAccount;

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsPeriod = $this->_getVarsFiscalPeriod(array(
			'flagFiscalPeriod' => 'f1',
			'numFiscalPeriod'  => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsSave = $this->_getVarsSave(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagReport'      => $this->_extSelf['flagReport'],
			'flagDetail'      => $this->_extSelf['flagDetail'],
		));

		$varsCommon = $this->getVars(array(
			'path' => $this->_extSelf['pathItem'],
		));

		$varsMonths = $this->_getVarsMonths(array(
			'vars'             => $arr['vars'],
			'varsEntityNation' => $varsEntityNation,
		));

		$data = array(
			'varsPeriod'       => $varsPeriod,
			'varsCommon'       => $varsCommon,
			'varsSave'         => $varsSave,
			'varsEntityNation' => $varsEntityNation,
			'varsMonths'       => $varsMonths,
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
		$varsItem = &$arr['varsItem'];

		$arrayNew = array();
		$array = &$vars['portal']['varsNavi']['templateDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'DummyEditPrev') {
				if (!$this->_checkEditPrev()) {
					continue;
				}
			}
			$method = '_updateVarsNavi' . $value['id'];
			if (method_exists($this, $method)) {
				$value = $this->$method(array(
					'vars'      => $value,
					'varsItem'  => $varsItem,
				));
			}
			$arrayNew[] = $value;
		}

		$vars['portal']['varsNavi']['templateDetail'] = $arrayNew;

		return $vars['portal']['varsNavi']['templateDetail'];
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
		$flag = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done|tempNext)$/", $flag)) {
			$arr['vars']['portal']['varsDetail']['varsStart']['varsEdit']['flagEditUse'] = 0;
		}

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllOutput'])) {
			$arr['vars']['portal']['varsDetail']['varsStart']['varsEdit']['flagOutputUse'] = 0;
		}
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'])) {
			$arr['vars']['portal']['varsDetail']['varsStart']['varsEdit']['flagEditUse'] = 0;
		}

		//tempNext
		if (preg_match("/^(tempNext)$/", $flag)) {
			$arr['vars']['portal']['varsDetail']['varsDetail']['varsHtml'] = '';
			$arr['vars']['portal']['varsNavi']['varsBtn'] = array();
			$arr['vars']['portal']['varsNavi']['varsStart']['varsEdit'] = array();
			$arr['vars']['portal']['varsDetail']['varsStart']['varsEdit'] = array();

			return $arr['vars'];
		}

		$flagMenu = $arr['varsFlag']['flagMenu'];
		$varsValue = $arr['varsItem']['varsSave']['jsonData'][$flagMenu];
		$method = '_getDetailVars' . $flagMenu;

		if (method_exists($this, $method)) {
			$data = $this->$method(array(
				'flagType'  => $flagMenu,
				'varsValue' => $varsValue,
				'vars'      => $arr['vars'],
				'varsItem'  => $arr['varsItem'],
			));

		} else {
			$data = $this->_getDetailVarsCommon(array(
				'flagType'  => $flagMenu,
				'varsValue' => $varsValue,
				'vars'      => $arr['vars'],
				'varsItem'  => $arr['varsItem'],
			));
		}

		$arr['vars']['portal']['varsDetail']['varsDetail']['varsHtml'] = $this->_getDetailHtml(array(
			'strFile'    => $data['strFile'],
			'vars'       => $arr['vars'],
			'varsData'   => $data['varsData'],
			'flagOutput' => ($arr['flagOutput'])? 1 : 0,
		));

		if (method_exists($this, $method)) {
			$dataIni = $this->$method(array(
				'flagType'  => $flagMenu,
				'varsValue' => array(),
				'vars'      => $arr['vars'],
				'varsItem'  => $arr['varsItem'],
			));

		} else {
			$dataIni = $this->_getDetailVarsCommon(array(
				'flagType'  => $flagMenu,
				'varsValue' => array(),
				'vars'      => $arr['vars'],
				'varsItem'  => $arr['varsItem'],
			));
		}
		$arr['vars']['varsItem']['varsIni'] = $dataIni['varsList'];

		$arr['vars']['varsItem']['varsList'] = $data['varsList'];
		$arr['vars']['varsItem']['flagBtnCalc'] = $data['flagBtnCalc'];
		$data = $arr['varsItem']['varsSave']['varsData'][$flagMenu];
		$arr['vars']['varsItem']['varsSave'] = ($data)? $data : array();

		$arr['vars']['varsItem']['varsCommon'] = $arr['varsItem']['varsCommon'];

		return $arr['vars'];
	}

	/**
		(array(
			'vars'             => $arr['vars'],
			'varsEntityNation' => $arr['varsEntityNation'],
		))
	 */
	protected function _getVarsMonths($arr)
	{
		$varsEntityNation = $arr['varsEntityNation'];

		$arrayOption = array();
		$numMonth = (int) $varsEntityNation['numFiscalBeginningMonth'];
		$numEnd = (int) $varsEntityNation['numFiscalTermMonth'];
		$numAll = 0;
		for ($i = 0; $i < $numEnd; $i++) {
			$data = array(
				'strMonth' => $numMonth . $arr['vars']['varsItem']['strMonth'],
				'id'       => $numMonth,
			);
			$arrayOption[] = $data;
			$numMonth++;
			if ($numMonth > 12) {
				$numMonth = 1;
			}
			$numAll++;
		}

		if ($numAll < 12) {
			for ($i = $numAll; $i <= 12; $i++) {
				$data = array(
					'strMonth' => '',
					'id'       => 'Blank',
				);
				$arrayOption[] = $data;
				$numMonth++;
				if ($numMonth > 12) {
					$numMonth = 1;
				}
			}
		}

		return $arrayOption;
	}

	/**

	 */
	protected function _getDetailHtml($arr)
	{
		global $classSmarty;

		if ($arr['flagOutput']) {
			return $arr['varsData'];
		}

		$arr['varsData']['strSpace'] = $arr['vars']['varsItem']['strSpace'];
		$array = $arr['varsData'];
		foreach ($array as $key => $value) {
			$classSmarty->assign($key, $value);
		}

		$path = str_replace('<%replace%>', $arr['strFile'], $this->_extSelf['pathTplHtml']);
		$contents = $classSmarty->fetch($path);

		return $contents;
	}

	/**
				'varsValue' => $varsValue,
				'vars'      => $arr['vars'],
				'varsItem'  => $arr['varsItem'],
	 */
	protected function _getDetailVars0($arr)
	{
		$varsList = array();
		$flagType = '0';
		$varsData = $arr['varsItem']['varsCommon']['varsStr'][$flagType];
		$varsPeriod = $arr['varsItem']['varsPeriod'];

		$array = $arr['varsItem']['varsCommon']['arrSelectTag'][$flagType];
		foreach ($array as $key => $value) {
			$varsData['str' . $value['id']] = $value['strTitle'];
			$tmplList = $value;
			$tmplList['idTarget'] = 'value' . $value['id'];

			$dataValue = $arr['varsValue'][$tmplList['idTarget']];
			if (!is_null($dataValue)) {
				$tmplList['value'] = $dataValue;
				$tmplList['valueStr'] = $dataValue;
			}
			if ($value['id'] == 'StartTerm') {
				$str = $value['value'];

				/*20190401 start*/
				//$strPeriod = str_replace('<%strHeisei%>', $varsPeriod['numStartHeisei'], $str);
				$strPeriod = str_replace('<%strStartNengoYear%>', $varsPeriod['strStartNengoYear'], $str);
				/*20190401 end*/

				$strPeriod = str_replace('<%strMonth%>', $varsPeriod['strStartMonth'], $strPeriod);
				$strPeriod = str_replace('<%strDate%>', $varsPeriod['strStartDate'], $strPeriod);
				$tmplList['value'] = $strPeriod;
				$tmplList['valueStr'] = $strPeriod;

			} elseif ($value['id'] == 'EndTerm') {
				$str = $value['value'];

				/*20190401 start*/
				//$strPeriod = str_replace('<%strHeisei%>', $varsPeriod['numEndHeisei'], $str);
				$strPeriod = str_replace('<%strEndNengoYear%>', $varsPeriod['strEndNengoYear'], $str);
				/*20190401 end*/


				$strPeriod = str_replace('<%strMonth%>', $varsPeriod['strEndMonth'], $strPeriod);
				$strPeriod = str_replace('<%strDate%>', $varsPeriod['strEndDate'], $strPeriod);
				$tmplList['value'] = $strPeriod;
				$tmplList['valueStr'] = $strPeriod;
			}
			if ($tmplList['valueStr'] == '') {
				$tmplList['valueStr'] = $arr['vars']['varsItem']['strSpace'];
			}
			$varsList[] = $tmplList;
		}
		$data = array(
			'strFile'       => $flagType,
			'varsData'      => ($varsData)? $varsData : array(),
			'varsList'      => $varsList,
			'flagBtnCalc' => 0,
		);

		return $data;
	}

	/**
				'varsValue' => $varsValue,
				'vars'      => $arr['vars'],
				'varsItem'  => $arr['varsItem'],
	 */
	protected function _getDetailVarsCommon($arr)
	{
		$varsList = array();
		$flagType = $arr['flagType'];
		$varsData = $arr['varsItem']['varsCommon']['varsStr'][$flagType];

		$array = $arr['varsItem']['varsCommon']['arrSelectTag'][$flagType];
		foreach ($array as $key => $value) {
			$varsData['str' . $value['id']] = $value['strTitle'];
			$tmplList = $value;
			$tmplList['idTarget'] = 'value' . $value['id'];

			$dataValue = $arr['varsValue'][$tmplList['idTarget']];
			if (!is_null($dataValue)) {
				if (preg_match("/^num/", $value['flagValueType'])) {
					$tmplList['value'] = $dataValue;
					$tmplList['valueStr'] = ($dataValue === '')?  '' : number_format($dataValue);

				} else {
					$tmplList['value'] = $dataValue;
					$tmplList['valueStr'] = $dataValue;
				}
			}

			if ($value['flagValueType'] == 'select') {
				$arrayOption = $value['arrayOption'];
				foreach ($arrayOption as $keyOption => $valueOption) {
					if ($tmplList['value'] == $valueOption['value']) {
						$tmplList['valueStr'] = $valueOption['strTitle'];
						break;
					}
				}
			}
			if ($tmplList['valueStr'] == '') {
				$tmplList['valueStr'] = $arr['vars']['varsItem']['strSpace'];
			}
			$varsList[] = $tmplList;
		}
		$data = array(
			'strFile'       => $flagType,
			'varsData'      => ($varsData)? $varsData : array(),
			'varsList'      => $varsList,
			'flagBtnCalc' => 0,
		);

		return $data;
	}

	/**
				'varsValue' => $varsValue,
				'vars'      => $arr['vars'],
				'varsItem'  => $arr['varsItem'],
	 */
	protected function _getDetailVars7PL($arr)
	{
		$varsList = array();
		$flagType = '7';
		$array = $arr['varsItem']['varsCommon']['arrSelectTag'][$flagType];
		foreach ($array as $key => $value) {
			if ( $value['value'] == '' || $value['flagFS'] != 'PL') {
				continue;
			}
			$tmplList = $arr['vars']['varsItem']['tmplList'];
			$str = ucwords($value['value']);
			$tmplList['id'] = $str;
			$tmplList['idTarget'] = 'numValue' . $str;
			$numValue = $arr['varsValue'][$tmplList['idTarget']];
			if (!is_null($numValue)) {
				$tmplList['value'] = $numValue;
				$tmplList['valueStr'] = ($numValue == 0 || $numValue == '')? '' : number_format($numValue);
			}
			if ($tmplList['valueStr'] == '') {
				$tmplList['valueStr'] = $arr['vars']['varsItem']['strSpace'];
			}
			$varsList[] = $tmplList;
		}

		$varsData = $arr['varsItem']['varsCommon']['varsStr'][$flagType];

		$data = array(
			'strFile'       => '7PL',
			'varsData'      => ($varsData)? $varsData : array(),
			'varsList'      => $varsList,
			'flagBtnCalc' => 1,
		);

		return $data;
	}

	/**
				'varsValue' => $varsValue,
				'vars'      => $arr['vars'],
				'varsItem'  => $arr['varsItem'],
	 */
	protected function _getDetailVars7BS($arr)
	{
		$varsList = array();
		$flagType = '7';
		$array = $arr['varsItem']['varsCommon']['arrSelectTag'][$flagType];
		foreach ($array as $key => $value) {
			if ( $value['value'] == '' || $value['flagFS'] != 'BS') {
				continue;
			}
			$tmplList = $arr['vars']['varsItem']['tmplList'];
			$str = ucwords($value['value']);
			$tmplList['id'] = $str;
			$tmplList['idTarget'] = 'numValue' . $str;
			$numValue = $arr['varsValue'][$tmplList['idTarget']];
			if (!is_null($numValue)) {
				$tmplList['value'] = $numValue;
				$tmplList['valueStr'] = ($numValue == 0 || $numValue == '')? '' : number_format($numValue);
			}
			if ($tmplList['valueStr'] == '') {
				$tmplList['valueStr'] = $arr['vars']['varsItem']['strSpace'];
			}
			$varsList[] = $tmplList;
		}

		$varsData = $arr['varsItem']['varsCommon']['varsStr'][$flagType];

		$data = array(
			'strFile'       => '7BS',
			'varsData'      => ($varsData)? $varsData : array(),
			'varsList'      => $varsList,
			'flagBtnCalc' => 1,
		);

		return $data;
	}

	/**

	 */
	protected function _getDetailVars16($arr)
	{
		$varsList = array();
		$flagType = $arr['flagType'];
		$varsData = $arr['varsItem']['varsCommon']['varsStr'][$flagType];

		$array = $arr['varsItem']['varsCommon']['arrSelectTag'][$flagType];
		foreach ($array as $key => $value) {
			$varsData['str' . $value['id']] = $value['strTitle'];
			$tmplList = $value;
			$tmplList['idTarget'] = 'value' . $value['id'];

			$dataValue = $arr['varsValue'][$tmplList['idTarget']];
			if (!is_null($dataValue)) {
				if (preg_match("/^num/", $value['flagValueType'])) {
					$tmplList['value'] = $dataValue;
					$tmplList['valueStr'] = ($dataValue === '')? '' : number_format($dataValue);

				} else {
					$tmplList['value'] = $dataValue;
					$tmplList['valueStr'] = $dataValue;
				}
			}
			if ($value['id'] == 'SelecOpenHour' || $value['id'] == 'SelecCloseHour') {
				$numEnd = 25;
			}

			if ($value['flagValueType'] == 'select') {
				$arrayOption = array();
				for ($i = 0; $i < $numEnd; $i++) {
					$data = array(
						'strTitle' => ($i < 10)? '0' . $i : $i,
						'value'    => $i,
					);
					$arrayOption[$i] = $data;
				}
				$tmplList['arrayOption'] = $arrayOption;
				foreach ($arrayOption as $keyOption => $valueOption) {
					if ($tmplList['value'] == $valueOption['value']) {
						$tmplList['valueStr'] = $valueOption['strTitle'];
						break;
					}
				}
			}
			if ($tmplList['valueStr'] == '') {
				$tmplList['valueStr'] = $arr['vars']['varsItem']['strSpace'];
			}
			$varsList[] = $tmplList;
		}
		$data = array(
			'strFile'     => $flagType,
			'varsData'    => ($varsData)? $varsData : array(),
			'varsList'    => $varsList,
			'flagBtnCalc' => 0,
		);

		return $data;
	}

	/**

	 */
	protected function _getDetailVars17Sales($arr)
	{
		$varsData = $arr['varsItem']['varsCommon']['varsStr']['17'];
		$varsData['strTitle'] = $varsData['sales1'];
		$varsData['strUnit'] = $varsData['str1000'];
		$varsData['arrRows'] = $arr['varsItem']['varsMonths'];

		$varsList = $this->_getDetailVars17Two(array(
			'varsValue'  => $arr['varsValue'],
			'vars'       => $arr['vars'],
			'varsMonths' => $varsData['arrRows'],
		));

		$data = array(
			'strFile'       => '17Two',
			'varsData'      => $varsData,
			'varsList'      => $varsList,
			'flagBtnCalc' => 1,
		);

		return $data;
	}

	/**

	 */
	protected function _getDetailVars17Two($arr)
	{
		$varsList = array();
		for ($i = 1; $i <= 2; $i++) {
			$tmplList = $arr['vars']['varsItem']['tmplListStr'];
			$tmplList['idTarget'] = 'strTitle' . $i;
			$tmplList['id'] = ucwords($tmplList['idTarget']);
			$str = $arr['varsValue'][$tmplList['idTarget']];
			if (!is_null($str)) {
				$tmplList['value'] = $str;
				$tmplList['valueStr'] = $tmplList['value'];
			}
			if ($tmplList['valueStr'] == '') {
				$tmplList['valueStr'] = $arr['vars']['varsItem']['strSpace'];
			}
			$varsList[] = $tmplList;

			$array = $arr['varsMonths'];
			foreach ($array as $key => $value) {
				if ($value['id'] == 'Blank') {
					continue;
				}
				$tmplList = $arr['vars']['varsItem']['tmplList'];
				$tmplList['idTarget'] = $i . 'NumValue' . $value['id'];
				$tmplList['id'] = $tmplList['idTarget'];
				$numValue = $arr['varsValue'][$tmplList['idTarget']];
				if (!is_null($numValue)) {
					$tmplList['value'] = $numValue;
					$tmplList['valueStr'] = ($numValue == 0 || $numValue == '')? '' : number_format($numValue);
				}
				if ($tmplList['valueStr'] == '') {
					$tmplList['valueStr'] = $arr['vars']['varsItem']['strSpace'];
				}
				$varsList[] = $tmplList;
			}

			$tmplList = $arr['vars']['varsItem']['tmplList'];

			$tmplList['idTarget'] = 'sum' . $i;
			$tmplList['id'] = ucwords($tmplList['idTarget']);
			$numValue = $arr['varsValue'][$tmplList['idTarget']];
			if (!is_null($numValue)) {
				$tmplList['value'] = $numValue;
				$tmplList['valueStr'] = ($numValue == 0 || $numValue == '')? '' : number_format($numValue);
			}
			if ($tmplList['valueStr'] == '') {
				$tmplList['valueStr'] = $arr['vars']['varsItem']['strSpace'];
			}
			$varsList[] = $tmplList;

			$tmplList = $arr['vars']['varsItem']['tmplList'];
			$tmplList['idTarget'] = 'sumPrev' . $i;
			$tmplList['id'] = ucwords($tmplList['idTarget']);
			$numValue = $arr['varsValue'][$tmplList['idTarget']];
			if (!is_null($numValue)) {
				$tmplList['value'] = $numValue;
				$tmplList['valueStr'] = ($numValue == 0 || $numValue == '')? '' : number_format($numValue);
			}
			if ($tmplList['valueStr'] == '') {
				$tmplList['valueStr'] = $arr['vars']['varsItem']['strSpace'];
			}
			$varsList[] = $tmplList;
		}

		return $varsList;
	}



	/**

	 */
	protected function _getDetailVars17Purchase($arr)
	{
		$varsData = $arr['varsItem']['varsCommon']['varsStr']['17'];
		$varsData['strTitle'] = $varsData['purchase1'];
		$varsData['strUnit'] = $varsData['str1000'];
		$varsData['arrRows'] = $arr['varsItem']['varsMonths'];

		$varsList = $this->_getDetailVars17Two(array(
			'varsValue'  => $arr['varsValue'],
			'vars'       => $arr['vars'],
			'varsMonths' => $varsData['arrRows'],
		));

		$data = array(
			'strFile'       => '17Two',
			'varsData'      => $varsData,
			'varsList'      => $varsList,
			'flagBtnCalc' => 1,
		);

		return $data;
	}

	/**

	 */
	protected function _getDetailVars17Outsourcing($arr)
	{
		$varsData = $arr['varsItem']['varsCommon']['varsStr']['17'];
		$varsData['strTitle'] = $varsData['outsourcing'];
		$varsData['strUnit'] = $varsData['str1000'];
		$varsData['arrRows'] = $arr['varsItem']['varsMonths'];

		$varsList = $this->_getDetailVars17One(array(
			'varsValue'  => $arr['varsValue'],
			'vars'       => $arr['vars'],
			'varsMonths' => $varsData['arrRows'],
		));

		$data = array(
			'strFile'       => '17One',
			'varsData'      => $varsData,
			'varsList'      => $varsList,
			'flagBtnCalc' => 1,
		);

		return $data;
	}

	/**

	 */
	protected function _getDetailVars17Tax($arr)
	{
		$varsData = $arr['varsItem']['varsCommon']['varsStr']['17'];
		$varsData['strTitle'] = $varsData['tax'];
		$varsData['strUnit'] = $varsData['strYen'];
		$varsData['arrRows'] = $arr['varsItem']['varsMonths'];

		$varsList = $this->_getDetailVars17One(array(
			'varsValue'  => $arr['varsValue'],
			'vars'       => $arr['vars'],
			'varsMonths' => $varsData['arrRows'],
		));

		$data = array(
			'strFile'       => '17One',
			'varsData'      => $varsData,
			'varsList'      => $varsList,
			'flagBtnCalc' => 0,
		);

		return $data;
	}

	/**

	 */
	protected function _getDetailVars17Worker($arr)
	{
		$varsData = $arr['varsItem']['varsCommon']['varsStr']['17'];
		$varsData['strTitle'] = $varsData['worker'];
		$varsData['strUnit'] = $varsData['strHuman'];
		$varsData['arrRows'] = $arr['varsItem']['varsMonths'];

		$varsList = $this->_getDetailVars17One(array(
			'tmplList'   => $arr['vars']['varsItem']['tmplListWorker'],
			'varsValue'  => $arr['varsValue'],
			'vars'       => $arr['vars'],
			'varsMonths' => $varsData['arrRows'],
		));

		$data = array(
			'strFile'       => '17One',
			'varsData'      => $varsData,
			'varsList'      => $varsList,
			'flagBtnCalc' => 0,
		);

		return $data;
	}

	/**

	 */
	protected function _getDetailVars17One($arr)
	{
		$varsList = array();

		$array = $arr['varsMonths'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'Blank') {
				continue;
			}
			$tmplList = ($arr['tmplList'])? $arr['tmplList'] : $arr['vars']['varsItem']['tmplList'];
			$tmplList['idTarget'] = 'numValue' . $value['id'];
			$tmplList['id'] = ucwords($tmplList['idTarget']);
			$numValue = $arr['varsValue'][$tmplList['idTarget']];
			if (!is_null($numValue)) {
				$tmplList['value'] = $numValue;
				$tmplList['valueStr'] = ($numValue == 0 || $numValue == '')? '' : number_format($numValue);
			}
			if ($tmplList['valueStr'] == '') {
				$tmplList['valueStr'] = $arr['vars']['varsItem']['strSpace'];
			}
			$varsList[] = $tmplList;
		}

		$tmplList = ($arr['tmplList'])? $arr['tmplList'] : $arr['vars']['varsItem']['tmplList'];
		$tmplList['idTarget'] = 'sum';
		$tmplList['id'] = ucwords($tmplList['idTarget']);
		$numValue = $arr['varsValue'][$tmplList['idTarget']];
		if (!is_null($numValue)) {
			$tmplList['value'] = $numValue;
			$tmplList['valueStr'] = ($numValue == 0 || $numValue == '')? '' : number_format($numValue);
		}
		if ($tmplList['valueStr'] == '') {
			$tmplList['valueStr'] = $arr['vars']['varsItem']['strSpace'];
		}
		$varsList[] = $tmplList;

		$tmplList = ($arr['tmplList'])? $arr['tmplList'] : $arr['vars']['varsItem']['tmplList'];
		$tmplList['idTarget'] = 'sumPrev';
		$tmplList['id'] = ucwords($tmplList['idTarget']);
		$numValue = $arr['varsValue'][$tmplList['idTarget']];
		if (!is_null($numValue)) {
			$tmplList['value'] = $numValue;
			$tmplList['valueStr'] = ($numValue == 0 || $numValue == '')? '' : number_format($numValue);
		}
		if ($tmplList['valueStr'] == '') {
			$tmplList['valueStr'] = $arr['vars']['varsItem']['strSpace'];
		}
		$varsList[] = $tmplList;

		return $varsList;
	}

	/**

	 */
	protected function _getDetailVars17Employee($arr)
	{
		$varsData = $arr['varsItem']['varsCommon']['varsStr']['17'];
		$varsData['strTitle'] = $varsData['employee'];
		$varsData['strUnit'] = $varsData['str1000'];
		$varsData['arrRows'] = $arr['varsItem']['varsMonths'];

		$varsList = $this->_getDetailVars17One(array(
			'varsValue'  => $arr['varsValue'],
			'vars'       => $arr['vars'],
			'varsMonths' => $varsData['arrRows'],
		));

		$data = array(
			'strFile'       => '17One',
			'varsData'      => $varsData,
			'varsList'      => $varsList,
			'flagBtnCalc' => 1,
		);

		return $data;
	}
	/**

	 */
	protected function _getDetailVars17Others($arr)
	{
		$varsData = $arr['varsItem']['varsCommon']['varsStr']['17'];
		$varsData['strUnit'] = $varsData['str1000'];
		$varsData['arrRows'] = $arr['varsItem']['varsMonths'];

		$varsList = array();

		$array = array(1, 2);
		foreach ($array as $key => $value) {
			$tmplList = $arr['vars']['varsItem']['tmplListStr'];
			$tmplList['idTarget'] = 'strTitle' . $value;
			$tmplList['id'] = ucwords($tmplList['idTarget']);
			$str = $arr['varsValue'][$tmplList['idTarget']];
			if (!is_null($str)) {
				$tmplList['value'] = $str;
				$tmplList['valueStr'] = $tmplList['value'];
			}
			if ($tmplList['valueStr'] == '') {
				$tmplList['valueStr'] = $arr['vars']['varsItem']['strSpace'];
			}
			$varsList[] = $tmplList;
		}

		$array = $varsData['arrRows'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'Blank') {
				continue;
			}
			$tmplList = $arr['vars']['varsItem']['tmplListOthers'];
			$tmplList['idTarget'] = 'numValue' . $value['id'];
			$tmplList['id'] = ucwords($tmplList['idTarget']);
			$numValue = $arr['varsValue'][$tmplList['idTarget']];
			if (!is_null($numValue)) {
				$tmplList['value'] = $numValue;
				$tmplList['valueStr'] = ($numValue == 0 || $numValue == '')? '' : number_format($numValue);
			}
			if ($tmplList['valueStr'] == '') {
				$tmplList['valueStr'] = $arr['vars']['varsItem']['strSpace'];
			}
			$varsList[] = $tmplList;
		}

		$tmplList = $arr['vars']['varsItem']['tmplListOthers'];
		$tmplList['idTarget'] = 'sum';
		$tmplList['id'] = ucwords($tmplList['idTarget']);
		$numValue = $arr['varsValue'][$tmplList['idTarget']];
		if (!is_null($numValue)) {
			$tmplList['value'] = $numValue;
			$tmplList['valueStr'] = ($numValue == 0 || $numValue == '')? '' : number_format($numValue);
		}
		if ($tmplList['valueStr'] == '') {
			$tmplList['valueStr'] = $arr['vars']['varsItem']['strSpace'];
		}
		$varsList[] = $tmplList;

		$tmplList = $arr['vars']['varsItem']['tmplListOthers'];
		$tmplList['idTarget'] = 'sumPrev';
		$tmplList['id'] = ucwords($tmplList['idTarget']);
		$numValue = $arr['varsValue'][$tmplList['idTarget']];
		if (!is_null($numValue)) {
			$tmplList['value'] = $numValue;
			$tmplList['valueStr'] = ($numValue == 0 || $numValue == '')? '' : number_format($numValue);
		}
		if ($tmplList['valueStr'] == '') {
			$tmplList['valueStr'] = $arr['vars']['varsItem']['strSpace'];
		}
		$varsList[] = $tmplList;

		$data = array(
			'strFile'       => '17OneOthers',
			'varsData'      => $varsData,
			'varsList'      => $varsList,
			'flagBtnCalc' => 0,
		);

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

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsFlag = array(
			'flagMenu' => $varsRequest['query']['jsonValue']['vars']['FlagMenu'],
		);

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$this->_checkValueDetail(array(
			'varsDetail' => $vars['portal']['varsNavi']['varsDetail'],
			'FlagMenu'   => $varsFlag['flagMenu'],
		));

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
				'varsDetail'    => $vars['portal']['varsDetail']['varsDetail'],
				'varsFlag'      => $varsFlag,
				'varsSave'      => $vars['varsItem']['varsSave'],
				'varsList'      => $vars['varsItem']['varsList'],
				'varsIni'       => $vars['varsItem']['varsIni'],
				'flagBtnCalc'   => $vars['varsItem']['flagBtnCalc'],
			),
		));
	}

	/**
		(array(
			'varsDetail'       => $vars['portal']['varsNavi']['varsDetail'],
			'varsItem'         => $varsItem,
			'FlagFiscalPeriod' => $varsFlag['flagFiscalPeriod'],
		))
	 */
	protected function _checkValueDetail($arr)
	{
		global $varsRequest;

		$flag = 0;
		$array = $arr['varsDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'FlagMenu') {
				$arrayOption = $value['arrayOption'];
				foreach ($arrayOption as $keyOption => $valueOption) {
					if ($valueOption['value'] == $arr[$value['id']]) {
						$flag = 1;
					}
				}
				if (!$flag) {
					$this->sendValue(array(
						'flag'    => 8,
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(),
					));
				}

			}
		}
	}


	/**
	 *
	 */
	protected function _iniDetailReload()
	{
		$this->_setSearch();
	}


	/**
	 *
	 */
	protected function _iniDetailOutput()
	{
		global $classRequest;

		global $varsRequest;

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

		$flag = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(tempNext)$/", $flag)) {
			$this->_send404Output();
		}

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];


		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsFlag = array(
			'flagMenu' => $varsRequest['query']['jsonValue']['vars']['FlagMenu'],
		);

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$this->_checkValueDetail(array(
			'varsDetail' => $vars['portal']['varsNavi']['varsDetail'],
			'FlagMenu'   => $varsFlag['flagMenu'],
		));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
			'flagOutput' => 1,
		));

		$strTitleMenu = $vars['varsItem']['varsMenu']['strList'];
		$strTitleMenu .= '_' . $this->_getStrTitleMenu(array(
			'varsDetail' => $vars['portal']['varsNavi']['varsDetail'],
			'flagMenu'   => $varsFlag['flagMenu'],
		));

		$text = $this->_getCsv(array(
			'varsFlag' => $varsFlag,
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$text = mb_convert_encoding($text, 'sjis', 'utf8');

		$strFileName = $this->_getFileTitle(array(
			'strMenu'     => $strTitleMenu,
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
	protected function _getStrTitleMenu($arr)
	{
		$array = $arr['varsDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'FlagMenu') {
				$arrayOption = $value['arrayOption'];
				foreach ($arrayOption as $keyOption => $valueOption) {
					if ($valueOption['value'] == $arr['flagMenu']) {
						$str = str_replace(': ', '', $valueOption['strTitle']);

						return $str;
					}
				}

			}
		}
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

		//strPeriod
		$varsPeriod = $this->_getVarsFiscalPeriod(array(
			'flagFiscalPeriod' => 'f1',
			'numFiscalPeriod'  => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));
		/*20190401 start*/
        /*
		$str = $arr['vars']['varsItem']['strPeriod'];
		$strPeriod = str_replace('<%strStartHeisei%>', $varsPeriod['numStartHeisei'], $str);
		$strPeriod = str_replace('<%strEndHeisei%>', $varsPeriod['numEndHeisei'], $strPeriod);
		*/
		$str = $arr['vars']['varsItem']['strPeriod20190401'];
		$strPeriod = str_replace('<%strStartNengoYear%>', $varsPeriod['strStartNengoYear'], $str);
		$strPeriod = str_replace('<%strEndNengoYear%>', $varsPeriod['strEndNengoYear'], $strPeriod);

		$strPeriod = str_replace('<%strStartMonth%>', $varsPeriod['numStartMonth'], $strPeriod);
		$strPeriod = str_replace('<%strEndMonth%>', $varsPeriod['numEndMonth'], $strPeriod);
		$arrayCsv[] = array($strPeriod);

		$array = array();
		$method = '_getVarsCsv' . $arr['varsFlag']['flagMenu'];
		if (preg_match("/^(17)/", $arr['varsFlag']['flagMenu'])) {
			$method = '_getVarsCsv17';
		}

		if (method_exists($this, $method)) {
			$array = $this->$method(array(
				'varsStr'   => $arr['vars']['portal']['varsDetail']['varsDetail']['varsHtml'],
				'varsList'  => $arr['vars']['varsItem']['varsList'],
				'strSpace'  => $arr['vars']['varsItem']['strSpace'],
				'strEscape' => $arr['vars']['varsItem']['strEscape'],
			));
			foreach ($array as $key => $value) {
				$arrayCsv[] = $value;
			}
		} else {
			$array = $this->_getVarsCsvCommon(array(
				'varsStr'  => $arr['vars']['portal']['varsDetail']['varsDetail']['varsHtml'],
				'varsList' => $arr['vars']['varsItem']['varsList'],
				'strSpace' => $arr['vars']['varsItem']['strSpace'],
			));
			foreach ($array as $key => $value) {
				$value[1] = str_replace(',', $arr['vars']['varsItem']['strEscape'], $value[1]);
				$arrayCsv[] = $value;
			}

		}



		$text = $classFile->getCsvText(array(
			'delimiter' => ',',
			'rows'      => $arrayCsv,
		));

		return $text;
	}

	/**
		'varsStr'  => $arr['vars']['portal']['varsDetail']['varsDetail']['varsHtml'],
		'varsList' => $arr['vars']['varsItem']['varsList'],
		'strSpace' => $arr['vars']['varsItem']['strSpace'],
	 */
	protected function _getVarsCsvCommon($arr)
	{
		$varsCsv = array();
		$array = $arr['varsList'];
		foreach ($array as $key => $value) {
			$str = 'str' . $value['id'];
			$varsCsv[] = array($arr['varsStr'][$str], $value['value']);
		}

		return $varsCsv;
	}

	protected function _getVarsCsv0($arr)
	{
		$varsCsv = array();
		$array = $arr['varsList'];
		foreach ($array as $key => $value) {
			$str = 'str' . $value['id'];
			$strTitle = $arr['varsStr'][$str];
			if (preg_match("/^(StartTerm|EndTerm)$/", $value['id'])) {
				$strTitle = $arr['varsStr']['strNum'] . $arr['strSpace'] .$strTitle;

			} elseif (preg_match("/^(ZipCode)$/", $value['id'])) {
				$strTitle = $arr['varsStr']['strMail'] . $arr['strSpace'] .$strTitle;
			}
			$value['value'] = str_replace(',', $arr['strEscape'], $value['value']);
			$varsCsv[] = array($strTitle, $value['value']);
		}

		return $varsCsv;
	}

	protected function _getVarsCsv2($arr)
	{
		$varsCsv = array();
		$array = $arr['varsList'];
		foreach ($array as $key => $value) {
			$str = 'str' . $value['id'];
			$strTitle = $arr['varsStr'][$str];
			if (preg_match("/^(Branch|BranchAddress|BranchOverseas|BranchOverseasNation|BranchOverseasEmployee)$/", $value['id'])) {
				$strTitle = $arr['varsStr']['strTitle1'] . $arr['strSpace'] .$strTitle;

			} elseif (preg_match("/^(SubsidiaryOverseas|SubsidiaryOverseasNation|SubsidiaryOverseasCapitalRate)$/", $value['id'])) {
				$strTitle = $arr['varsStr']['strTitle2'] . $arr['strSpace'] .$strTitle;

			} elseif (preg_match("/^(SelectTypeImport|SelectTypeExport|SelectTypeNone|ImportTargetNation|ImportTargetGoods|ImportTargetPrice|ExportTargetNation|ExportTargetGoods|ExportTargetPrice)$/", $value['id'])) {
				$strTitle = $arr['varsStr']['strTitle3'] . $arr['strSpace'] .$strTitle;

			} else {
				$strTitle = $arr['varsStr']['strTitle4'] . $arr['strSpace'] .$strTitle;
			}
			$value['value'] = str_replace(',', $arr['strEscape'], $value['value']);
			$varsCsv[] = array($strTitle, $value['value']);
		}

		return $varsCsv;
	}

	protected function _getVarsCsv3($arr)
	{
		$varsCsv = array();
		$array = $arr['varsList'];
		foreach ($array as $key => $value) {
			$str = 'str' . $value['id'];
			$strTitle = $arr['varsStr'][$str];
			if (preg_match("/^(Director|Text|Num|Sum)/", $value['id'])) {
				$strTitle = $arr['varsStr']['strTitle1'] . $arr['strSpace'] .$strTitle;

			} elseif (preg_match("/^(SelectSalaryFixed|SelectPercent|SelectHybrid)$/", $value['id'])) {
				$strTitle = $arr['varsStr']['strTitle2'] . $arr['strSpace'] .$strTitle;

			} else {
				$strTitle = $arr['varsStr']['strTitle3'] . $arr['strSpace'] .$strTitle;
			}
			$value['value'] = str_replace(',', $arr['strEscape'], $value['value']);
			$varsCsv[] = array($strTitle, $value['value']);
		}

		return $varsCsv;
	}

	protected function _getVarsCsv4($arr)
	{
		$varsCsv = array();
		$array = $arr['varsList'];
		foreach ($array as $key => $value) {
			$str = 'str' . $value['id'];
			$strTitle = $arr['varsStr'][$str];
			if (preg_match("/^(SelectUseCheck|SelectUseNone)/", $value['id'])) {
				$strTitle = $arr['varsStr']['strTitle1'] . $arr['strSpace'] .$strTitle;

			} elseif (preg_match("/^(SelectNetCheck|SelectNetNone)$/", $value['id'])) {
				$strTitle = $arr['varsStr']['strTitle2'] . $arr['strSpace'] .$strTitle;

			} elseif (preg_match("/^(SelectProgram)/", $value['id'])) {
				$strTitle = $arr['varsStr']['strTitle3'] . $arr['strSpace'] .$strTitle;

			} elseif (preg_match("/^(SelectApp)/", $value['id'])) {
				$strTitle = $arr['varsStr']['strTitle4'] . $arr['strSpace'] .$strTitle;

			} elseif (preg_match("/^(Type)/", $value['id'])) {
				$strTitle = $arr['varsStr']['strTitle5'] . $arr['strSpace'] .$strTitle;

			} elseif (preg_match("/^(SoftName)/", $value['id'])) {
				$strTitle = $arr['varsStr']['strTitle6'] . $arr['strSpace'] .$strTitle;

			} elseif (preg_match("/^(Charge)/", $value['id'])) {
				$strTitle = $arr['varsStr']['strTitle7'] . $arr['strSpace'] .$strTitle;

			} elseif (preg_match("/^(SelectWirelessLan|SelectWireLan|SelectNone)/", $value['id'])) {
				$strTitle = $arr['varsStr']['strTitle8'] . $arr['strSpace'] .$strTitle;

			} else {
				$strTitle = $arr['varsStr']['strTitle9'] . $arr['strSpace'] .$strTitle;
			}
			$value['value'] = str_replace(',', $arr['strEscape'], $value['value']);
			$varsCsv[] = array($strTitle, $value['value']);
		}

		return $varsCsv;
	}

	protected function _getVarsCsv5($arr)
	{
		$varsCsv = array();
		$array = $arr['varsList'];
		foreach ($array as $key => $value) {
			$str = 'str' . $value['id'];
			$strTitle = $arr['varsStr'][$str];
			if (preg_match("/^(NameCash)/", $value['id'])) {
				$strTitle = $arr['varsStr']['strTitle1'] . $arr['strSpace'] . $arr['varsStr']['strSection'] . $arr['strSpace'] . $strTitle . $arr['strSpace'] . $arr['varsStr']['strName'];

			} elseif (preg_match("/^(SelectCashFamily)/", $value['id'])) {
				$strTitle = $arr['varsStr']['strTitle1'] . $arr['strSpace'] . $arr['varsStr']['strSection'] . $arr['strSpace'] . $arr['varsStr']['strLeader'] . $arr['strSpace'] . $strTitle;

			} elseif (preg_match("/^(SelectCashOther)/", $value['id'])) {
				$strTitle = $arr['varsStr']['strTitle1'] . $arr['strSpace'] . $arr['varsStr']['strSection'] . $arr['strSpace'] . $arr['varsStr']['strLeader'] . $arr['strSpace'] . $strTitle;

			} elseif (preg_match("/^(NameCheck)/", $value['id'])) {
				$strTitle = $arr['varsStr']['strTitle1'] . $arr['strSpace'] . $arr['varsStr']['strSection']  . $arr['strSpace'] . $strTitle . $arr['strSpace'] . $arr['varsStr']['strName'];

			} elseif (preg_match("/^(SelectCheckFamily)/", $value['id'])) {
				$strTitle = $arr['varsStr']['strTitle1'] . $arr['strSpace'] . $arr['varsStr']['strSection'] . $arr['strSpace'] . $arr['varsStr']['strLeader'] . $arr['strSpace'] . $strTitle;

			} elseif (preg_match("/^(SelectCheckOther)/", $value['id'])) {
				$strTitle = $arr['varsStr']['strTitle1'] . $arr['strSpace'] . $arr['varsStr']['strSection'] . $arr['strSpace'] . $arr['varsStr']['strLeader'] . $arr['strSpace'] . $strTitle;

			} elseif (preg_match("/^(SelectMonthly|SelectMultiMonthly|SelectYear)$/", $value['id'])) {
				$strTitle = $arr['varsStr']['strTitle2'] . $arr['strSpace'] .$strTitle;

			} elseif (preg_match("/^(SelectSalary|SelectPay|SelectLoan|SelectAllot|SelectNonResident|SelectRetire)/", $value['id'])) {
				$strTitle = $arr['varsStr']['strTitle3'] . $arr['strSpace'] .$strTitle;

			} elseif (preg_match("/^(SelectSales|SelectFixedAssets|SelectStock|SelectCost)/", $value['id'])) {
				$strTitle = $arr['varsStr']['strTitle4'] . $arr['strSpace'] . $arr['varsStr']['strAccounting'] . $arr['strSpace'] . $strTitle;

			} else {
				$strTitle = $arr['varsStr']['strTitle4'] . $arr['strSpace'] .$strTitle;
			}
			$value['value'] = str_replace(',', $arr['strEscape'], $value['value']);
			$varsCsv[] = array($strTitle, $value['value']);
		}

		return $varsCsv;
	}

	protected function _getVarsCsv7PL($arr)
	{
		global $classEscape;

		$varsCsv = array();
		$array = $arr['varsList'];
		foreach ($array as $key => $value) {
			$str = $classEscape->toLower(array('str' => $value['id']));
			$strTitle = $arr['varsStr'][$str];
			if (preg_match("/^(opening|purchase|employee|outsourcing|closing|dep|rents)$/", $str)) {
				$strTitle = $arr['varsStr']['costOfSalesOf'] . $arr['strSpace'] .$strTitle;

			} elseif (preg_match("/^(directors|salaries|entertainment|depreciation|rentsSellAdmin)$/", $str)) {
				$strTitle = $arr['varsStr']['sellAdmin'] . $arr['strSpace'] .$strTitle;

			}
			$value['value'] = str_replace(',', $arr['strEscape'], $value['value']);
			$varsCsv[] = array($strTitle, $value['value']);
		}

		return $varsCsv;
	}

	protected function _getVarsCsv7BS($arr)
	{
		global $classEscape;

		$varsCsv = array();
		$array = $arr['varsList'];
		foreach ($array as $key => $value) {
			$str = $classEscape->toLower(array('str' => $value['id']));
			$strTitle = $arr['varsStr'][$str];
			if (preg_match("/^(cash|notesReceivable|accountsReceivable|inventries|loansReceivable|buildings|machineryAndEquipment|car|land)$/", $str)) {
				$strTitle = $arr['varsStr']['assetsOf'] . $arr['strSpace'] .$strTitle;

			} elseif (preg_match("/^(notesPayable|accountsAmountPayable|loans|loansElse)$/", $str)) {
				$strTitle = $arr['varsStr']['liabilitiesOf'] . $arr['strSpace'] .$strTitle;

			}
			$value['value'] = str_replace(',', $arr['strEscape'], $value['value']);
			$varsCsv[] = array($strTitle, $value['value']);
		}

		return $varsCsv;
	}

	protected function _getVarsCsv8($arr)
	{
		$varsCsv = array();
		$array = $arr['varsList'];
		foreach ($array as $key => $value) {
			$str = 'str' . $value['id'];
			$strTitle = $arr['varsStr'][$str];
			if (preg_match("/^(SelectNetCheck|SelectNetNone)/", $value['id'])) {
				$strTitle = $arr['varsStr']['strTitle1'] . $arr['strSpace'] .$strTitle;

			} else {
				$strTitle = $arr['varsStr']['strTitle2'] . $arr['strSpace'] .$strTitle;
			}
			$value['value'] = str_replace(',', $arr['strEscape'], $value['value']);
			$varsCsv[] = array($strTitle, $value['value']);
		}

		return $varsCsv;
	}

	protected function _getVarsCsv11($arr)
	{
		$varsCsv = array();
		$array = $arr['varsList'];
		foreach ($array as $key => $value) {
			$str = 'str' . $value['id'];
			$strTitle = $arr['varsStr'][$str];
			if (preg_match("/(SideJob)/", $value['id'])) {
				$strTitle = $arr['varsStr']['strTitle1'] . $arr['strSpace'] .$strTitle;

			} elseif (preg_match("/^(Text)/", $value['id'])) {
				$strTitle = $arr['varsStr']['strTitle2'] . $arr['strSpace'] .$strTitle;

			} else {
				$strTitle = $arr['varsStr']['strTitle3'] . $arr['strSpace'] .$strTitle;
			}
			$value['value'] = str_replace(',', $arr['strEscape'], $value['value']);
			$varsCsv[] = array($strTitle, $value['value']);
		}

		return $varsCsv;
	}

	protected function _getVarsCsv13($arr)
	{
		$varsCsv = array();
		$array = $arr['varsList'];
		foreach ($array as $key => $value) {
			$str = 'str' . $value['id'];
			$strTitle = $arr['varsStr'][$str];
			if (preg_match("/(Sales)$/", $value['id'])) {
				$strTitle = $arr['varsStr']['strSales'] . $arr['strSpace'] .$strTitle;

			} elseif (preg_match("/(Purchase)$/", $value['id'])) {
				$strTitle = $arr['varsStr']['strPurchase'] . $arr['strSpace'] .$strTitle;

			} elseif (preg_match("/(Outsourcing)$/", $value['id'])) {
				$strTitle = $arr['varsStr']['strOutsourcing'] . $arr['strSpace'] .$strTitle;

			} else {
				$strTitle = $arr['varsStr']['strEmployee'] . $arr['strSpace'] .$strTitle;
			}
			$value['value'] = str_replace(',', $arr['strEscape'], $value['value']);
			$varsCsv[] = array($strTitle, $value['value']);
		}

		return $varsCsv;
	}

	protected function _getVarsCsv15($arr)
	{
		$varsCsv = array();
		$array = $arr['varsList'];
		foreach ($array as $key => $value) {
			$str = 'str' . $value['id'];
			$strTitle = $arr['varsStr'][$str];
			if (preg_match("/^(TextName)$/", $value['id'])) {
				$strTitle = $arr['varsStr']['strTitle1'] . $arr['strSpace'] .$strTitle;

			} elseif (preg_match("/^(TextAddress)$/", $value['id'])) {
				$strTitle = $arr['varsStr']['strTitle2'] . $arr['strSpace'] .$strTitle;

			} elseif (preg_match("/^(TextPhone)$/", $value['id'])) {
				$strTitle = $arr['varsStr']['strTitle3'] . $arr['strSpace'] .$strTitle;

			} else {
				$strTitle = $arr['varsStr']['strTitle4'] . $arr['strSpace'] .$strTitle;
			}
			$value['value'] = str_replace(',', $arr['strEscape'], $value['value']);
			$varsCsv[] = array($strTitle, $value['value']);
		}

		return $varsCsv;
	}

	protected function _getVarsCsv16($arr)
	{
		$varsCsv = array();
		$array = $arr['varsList'];
		foreach ($array as $key => $value) {
			$str = 'str' . $value['id'];
			$strTitle = $arr['varsStr'][$str];
			if (preg_match("/^(SelecOpenHour)$/", $value['id'])) {
				$strTitle = $arr['varsStr']['strOpenHour'] . $arr['strSpace'] . $arr['varsStr']['strOpenStore'] . $arr['strSpace'] .$strTitle;

			} elseif (preg_match("/^(SelecCloseHour)$/", $value['id'])) {
				$strTitle = $arr['varsStr']['strCloseHour'] . $arr['strSpace'] . $arr['varsStr']['strCloseStore'] . $arr['strSpace'] .$strTitle;

			} elseif (preg_match("/^(TextDay|TextDate)$/", $value['id'])) {
				$strTitle = $arr['varsStr']['strHoliday'] . $arr['strSpace'] . $arr['varsStr']['strWeek'] . $arr['strSpace'] .$strTitle;

			}
			$value['value'] = str_replace(',', $arr['strEscape'], $value['value']);
			$varsCsv[] = array($strTitle, $value['value']);
		}

		return $varsCsv;
	}

	protected function _getVarsCsv17($arr)
	{
		$varsCsv = array();
		if ($arr['varsStr']['strTitle']) {
			$varsCsv[] = array($arr['varsStr']['strTitleColumn'], $arr['varsStr']['strTitle']);
		}
		$array = $arr['varsList'];
		foreach ($array as $key => $value) {
			$str = $value['id'];
			$strTitle = $arr['varsStr'][$str];
			if (preg_match("/NumValue(.*?)$/", $str, $arrMatch)) {
				list($str, $numMonth) = $arrMatch;
				$strTitle = $numMonth . $arr['varsStr']['strMonth'];

			} elseif (preg_match("/^(SumPrev)/", $str)) {
				$strTitle = $arr['varsStr']['strSumPrev'];

			} elseif (preg_match("/^(Sum)/", $str)) {
				$strTitle = $arr['varsStr']['strSum'];

			} else {
				$strTitle = $arr['varsStr']['strTitleColumn'];
			}
			$value['value'] = str_replace(',', $arr['strEscape'], $value['value']);
			$varsCsv[] = array($strTitle, $value['value']);
		}


		return $varsCsv;
	}


}
