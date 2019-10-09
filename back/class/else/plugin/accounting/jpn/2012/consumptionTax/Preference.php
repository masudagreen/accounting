<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_2012_ConsumptionTax_Preference extends Code_Else_Plugin_Accounting_Jpn_ConsumptionTax
{
	protected $_extSelf = array(
		'idPreference' => 'consumptionTaxWindow',
		'flagReport' => '2012',
		'flagDetail' => 'preference',
		'pathTplJs' => 'else/plugin/accounting/js/jpn/2012/consumptionTax/preference.js',
		'pathVarsJs'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/2012/consumptionTax/preference.php',
		'pathItem'     => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/2012/consumptionTax/preferenceItem.php',
		'pathTplHtml'  => 'else/plugin/accounting/html/2012/consumptionTax/preference<%replace%>.html',
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

			$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/jpn/' . $this->_extSelf['flagReport'] . '/consumptionTax/' . $str . ".php";
			if (!file_exists($path)) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
				}
				exit;
			}
			require_once($path);
			$strClass = 'Code_Else_Plugin_Accounting_Jpn_' . $this->_extSelf['flagReport'] . '_ConsumptionTax_' . $str;
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

		//tempNext
		$flagEditPrev = $this->_checkEditPrev();
		if ($flagEditPrev) {
			$vars['portal']['varsNavi']['varsBtn'] = array();
			$vars['portal']['varsNavi']['varsStart']['varsEdit'] = array();
			$vars['portal']['varsDetail']['varsStart']['varsEdit'] = array();
		}

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

		$data = array(
			'varsPeriod'         => $varsPeriod,
			'varsCommon'         => $varsCommon,
			'varsSave'           => $varsSave,
			'varsEntityNation'   => $varsEntityNation,
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
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'])) {
			$arr['vars']['portal']['varsDetail']['varsStart']['varsEdit']['flagEditUse'] = 0;
		}

		//tempNext
		if (preg_match("/^(tempNext)$/", $flag)) {
			$arr['vars']['portal']['varsDetail']['varsDetail']['varsHtml'] = '';

			return $arr['vars'];
		}

		$flagMenu = $arr['varsFlag']['flagMenu'];
		$varsValue = $arr['varsItem']['varsSave']['jsonData'][$flagMenu];
		$method = '_getDetailVars' . ucwords($flagMenu);

		if (method_exists($this, $method)) {
			$data = $this->$method(array(
				'varsFlag'    => $arr['varsFlag'],
				'varsValue'   => $varsValue,
				'vars'        => $arr['vars'],
				'varsItem'    => $arr['varsItem'],
			));

		} else {
			$data = $this->_getDetailVarsCommon(array(
				'varsFlag'    => $arr['varsFlag'],
				'varsValue'   => $varsValue,
				'vars'        => $arr['vars'],
				'varsItem'    => $arr['varsItem'],
			));
		}
		$arr['vars']['portal']['varsDetail']['varsDetail']['varsHtml'] = $this->_getDetailHtml(array(
			'strFile'    => $data['strFile'],
			'vars'       => $arr['vars'],
			'varsData'   => $data['varsData'],
			'flagOutput' => ($arr['flagOutput'])? 1 : 0,
		));

		$arr['vars']['varsItem']['varsList'] = $data['varsList'];
		$data = $arr['varsItem']['varsSave']['jsonData'];

		$arr['vars']['varsItem']['varsSave'] = ($data)? $data : array();

		$arr['vars']['varsItem']['varsCommon'] = $arr['varsItem']['varsCommon'];

		return $arr['vars'];
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
	protected function _getDetailVarsCalc($arr)
	{
		$varsList = array();

		$flagConsumptionTaxGeneralRule = (int) $arr['varsItem']['varsEntityNation']['flagConsumptionTaxGeneralRule'];

		$flagType = $arr['varsFlag']['flagMenu'];
		$varsData = $arr['varsItem']['varsCommon']['varsStr'][$flagType];
		$varsData['flagGeneralRule'] = $flagConsumptionTaxGeneralRule;

		$array = $arr['varsItem']['varsCommon']['varsTmpl'][$flagType];
		foreach ($array as $key => $value) {
			if ($flagConsumptionTaxGeneralRule) {
				if (!$value['flagGeneralRule']) {
					continue;
				}

			} else {
				if ($value['flagGeneralRule']) {
					continue;
				}
			}
			$varsData['str' . $value['id']] = $value['strTitle'];
			$tmplList = $value;
			$tmplList['idTarget'] = 'value' . $value['id'];

			$dataValue = $arr['varsValue'][$tmplList['idTarget']];
			if (!is_null($dataValue)) {
				$tmplList['value'] = $dataValue;
				$tmplList['valueStr'] = $dataValue;
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
			$varsList[] = $tmplList;
		}

		$data = array(
			'strFile'   => ucwords($arr['varsFlag']['flagMenu']),
			'varsData'  => ($varsData)? $varsData : array(),
			'varsList'  => $varsList,
		);

		return $data;
	}

	/**
		'varsValue' => $varsValue,
		'vars'      => $arr['vars'],
		'varsItem'  => $arr['varsItem'],
	 */
	protected function _getDetailVarsLook($arr)
	{
		$varsList = array();

		$flagType = $arr['varsFlag']['flagMenu'];
		$varsData = $arr['varsItem']['varsCommon']['varsStr'][$flagType];

		$array = $arr['varsItem']['varsCommon']['varsTmpl'][$flagType];
		foreach ($array as $key => $value) {
			$varsData['str' . $value['id']] = $value['strTitle'];
			$tmplList = $value;
			$tmplList['idTarget'] = 'value' . $value['id'];

			$dataValue = $arr['varsValue'][$tmplList['idTarget']];
			if (!is_null($dataValue)) {
				$tmplList['value'] = $dataValue;
				$tmplList['valueStr'] = $dataValue;
			}

			$value = $this->_updateDetailVarsLookOption(array(
				'vars'     => $value,
				'varsItem' => $arr['varsItem'],
			));

			$tmplList['arrayOption'] = $value['arrayOption'];

			if ($value['flagValueType'] == 'select') {
				$arrayOption = $value['arrayOption'];
				foreach ($arrayOption as $keyOption => $valueOption) {
					if ($tmplList['value'] == $valueOption['value']) {
						$tmplList['valueStr'] = $valueOption['strTitle'];
						break;
					}
				}
			}
			$varsList[] = $tmplList;
		}

		$data = array(
			'strFile'   => ucwords($arr['varsFlag']['flagMenu']),
			'varsData'  => ($varsData)? $varsData : array(),
			'varsList'  => $varsList,
		);

		return $data;
	}

	/**
		(array(
			'vars'             => $value,
			'varsItem'         => $varsItem,
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _updateDetailVarsLookOption($arr)
	{
		$varsEntityNation = $arr['varsItem']['varsEntityNation'];

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
				'varsValue' => $varsValue,
				'vars'      => $arr['vars'],
				'varsItem'  => $arr['varsItem'],
	 */
	protected function _getDetailVarsCommon($arr)
	{
		$varsList = array();

		$flagType = $arr['varsFlag']['flagMenu'];
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
			'strFile'   => ucwords($arr['varsFlag']['flagMenu']),
			'varsData'  => ($varsData)? $varsData : array(),
			'varsList'  => $varsList,
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
			'varsFlag'   => $varsFlag,
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
				'varsDetail'         => $vars['portal']['varsDetail']['varsDetail'],
				'varsFlag'           => $varsFlag,
				'varsSave'           => $vars['varsItem']['varsSave'],
				'varsList'           => $vars['varsItem']['varsList'],
				'flagBtnCalc'        => $vars['varsItem']['flagBtnCalc'],
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
		global $classEscape;

		$flag = 0;
		$array = $arr['varsDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'FlagMenu') {
				$id = $classEscape->toLower(array('str' => $value['id']));
				$arrayOption = $value['arrayOption'];
				foreach ($arrayOption as $keyOption => $valueOption) {
					if ($valueOption['value'] == $arr['varsFlag'][$id]) {
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
}
