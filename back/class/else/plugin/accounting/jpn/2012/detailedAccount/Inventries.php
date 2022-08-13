<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_2012_DetailedAccount_Inventries extends Code_Else_Plugin_Accounting_Jpn_DetailedAccount
{
	protected $_extSelf = array(
		'idPreference' => 'detailedAccountInventriesWindow',
		'flagReport'   => '2012',
		'flagDetail'   => 'inventries',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/2012/detailedAccount/inventries.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/2012/detailedAccount/inventries.php',
		'pathItem'     => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/2012/detailedAccount/inventriesItem.php',
		'pathTplHtml'  => 'else/plugin/accounting/html/2012/detailedAccount/inventries<%replace%>.html',
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

			$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/jpn/' . $this->_extSelf['flagReport'] . '/detailedAccount/' . $str . ".php";
			if (!file_exists($path)) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
				}
				exit;
			}
			require_once($path);
			$strClass = 'Code_Else_Plugin_Accounting_Jpn_' . $this->_extSelf['flagReport'] . '_DetailedAccount_' . $str;
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

		$arrSubAccountTitle = $this->_getVarsSubAccountTitle(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrAccountTitle = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'numFiscalPeriod'    => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsSave = $this->_getVarsSave(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagReport'      => $this->_extSelf['flagReport'],
			'flagDetail'      => $this->_extSelf['flagDetail'],
			'numPage'         => $arr['varsFlag']['numPage'],
		));

		$varsPreference = $this->_getVarsPreference(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagReport'      => $this->_extSelf['flagReport'],
			'flagDetail'      => $this->_extSelf['flagDetail'],
		));

		$varsCommon = $this->getVars(array(
			'path' => $this->_extSelf['pathItem'],
		));

		$data = array(
			'varsCommon'         => $varsCommon,
			'varsPreference'     => $varsPreference,
			'varsSave'           => $varsSave,
			'arrAccountTitle'    => $arrAccountTitle,
			'arrSubAccountTitle' => $arrSubAccountTitle,
		);

		return $data;
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
			'vars'             => $value,
			'varsItem'         => $varsItem,
		))
	 */
	protected function _updateVarsNaviNumPage($arr)
	{
		$numEnd = $arr['varsItem']['varsPreference']['jsonData']['numPageMax'];

		if (!$numEnd) {
			$numEnd = 1;
		}

		for ($i = 0; $i < $numEnd; $i++) {
			$numPage = $i + 1;
			$data = array(
				'strTitle' => $numPage . $arr['vars']['varsTmpl']['strPage'],
				'value'    => $numPage,
			);
			$arrayOption[] = $data;
		}
		$arr['vars']['arrayOption'] = $arrayOption;

		if (count($arrayOption) < $arr['vars']['numSize']) {
			$arr['vars']['numSize'] = count($arrayOption);
		}

		return $arr['vars'];
	}


	/**
		(array(
			'vars'             => $vars,
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
			$arr['vars']['portal']['varsNavi']['varsStart']['varsEdit']['flagPreferenceUse'] = 0;
		}

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllOutput'])) {
			$arr['vars']['portal']['varsDetail']['varsStart']['varsEdit']['flagOutputUse'] = 0;
			$arr['vars']['portal']['varsDetail']['varsStart']['varsEdit']['flagPrintUse'] = 0;
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
		if (preg_match("/^detail/", $flagMenu)) {
			$varsValue = $arr['varsItem']['varsSave']['jsonData'][$flagMenu];
		} else {
			$varsValue = $arr['varsItem']['varsPreference']['jsonData'][$flagMenu];
		}

		$method = '_getDetailVars' . ucwords($flagMenu);
		if (method_exists($this, $method)) {
			$data = $this->$method(array(
				'varsFlag'    => $arr['varsFlag'],
				'varsValue'   => $varsValue,
				'vars'        => $arr['vars'],
				'varsItem'    => $arr['varsItem'],
				'flagBtnCalc' => $arr['varsItem']['varsCommon']['varsFlagCalcBtn'][$flagMenu],
			));
		}

		if ($arr['flagOutput']) {
			$arr['vars']['portal']['varsDetail']['varsDetail']['varsHtml'] = $data;
		} else {
			$arr['vars']['portal']['varsDetail']['varsDetail']['varsHtml'] = $this->_getDetailHtml(array(
				'strFile'  => $data['strFile'],
				'vars'     => $arr['vars'],
				'varsData' => $data['varsData'],
			));
		}

		if (method_exists($this, $method)) {
			$dataIni = $this->$method(array(
				'varsFlag'    => $arr['varsFlag'],
				'varsValue'   => array(),
				'vars'        => $arr['vars'],
				'varsItem'    => $arr['varsItem'],
				'flagBtnCalc' => $arr['varsItem']['varsCommon']['varsFlagCalcBtn'][$flagMenu],
			));
		}
		$arr['vars']['varsItem']['varsIni'] = $dataIni['varsList'];

		$arr['vars']['varsItem']['arrAccountTitle'] = $arr['varsItem']['arrAccountTitle'];
		$arr['vars']['varsItem']['arrSubAccountTitle'] = $arr['varsItem']['arrSubAccountTitle'];
		$arr['vars']['varsItem']['varsList'] = $data['varsList'];
		$arr['vars']['varsItem']['flagBtnCalc'] = $data['flagBtnCalc'];
		$arr['vars']['varsItem']['varsPreference'] = $arr['varsItem']['varsPreference'];
		$arr['vars']['varsItem']['varsCommon'] = $arr['varsItem']['varsCommon'];

		if (!preg_match("/^detail/", $flagMenu)) {
			$arr['vars']['portal']['varsDetail']['varsStart']['varsEdit']['flagEditUse'] = 0;
			if (preg_match("/^numPageMax$/", $flagMenu)) {
				$arr['vars']['portal']['varsDetail']['varsStart']['varsEdit']['flagEditUse'] = 1;
			}

			$arr['vars']['portal']['varsDetail']['varsStart']['varsEdit']['flagOutputUse'] = 0;
			$arr['vars']['portal']['varsDetail']['varsStart']['varsEdit']['flagPrintUse'] = 0;
		}

		return $arr['vars'];
	}

	/**

	 */
	protected function _getDetailHtml($arr)
	{
		global $classSmarty;

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
	protected function _getDetailVarsDetail($arr)
	{
		$varsList = array();

		$flagMenu = $arr['varsFlag']['flagMenu'];
		$varsData = $arr['varsItem']['varsCommon']['varsStr'][$flagMenu];
		$array = $arr['varsItem']['varsCommon']['varsTmpl'][$flagMenu];

		$arrStrTitle = $arr['varsItem']['arrAccountTitle']['arrStrTitle'];

		$arrStrTitle['none']['strTitleFS'] = '';
		$arrSubStrTitle = $arr['varsItem']['arrSubAccountTitle']['arrStrTitle'];

		$arrayResetCheck = array();
		$arrRows = array();
		$numList = 0;
		$numEnd = $varsData['numRows'];
		for ($i = 1; $i <= $numEnd; $i++) {
			$rowData = array(
				'id' => $i,
			);
			$arrRows[] = $rowData;
			foreach ($array as $key => $value) {
				if (!$value['flagLoop']) {
					continue;
				}
				$tmplList = $value;
				$tmplList['id'] = $value['id'] . $i;
				$tmplList['idTarget'] = 'value' . $value['id'] . $i;
				$varsData['str' . $value['id']] = $value['strTitle'];

				$dataValue = $arr['varsValue'][$tmplList['idTarget']];
				if (!is_null($dataValue)) {
					if (preg_match("/^num/", $value['flagValueType'])) {
						$tmplList['value'] = $dataValue;
						$tmplList['valueStr'] = ($dataValue === '' || $dataValue == 0)?  '' : number_format($dataValue);

					} else {
						$tmplList['value'] = $dataValue;
						$tmplList['valueStr'] = $dataValue;
					}
				}
				$numListAccountTitle = 0;
				if (preg_match("/^SelectIdAccountTitle/", $tmplList['id'])) {
					$numListAccountTitle = $numList;
					$str = $arrStrTitle[$tmplList['value']]['strTitleFS'];
					if (is_null($str) || $tmplList['value'] == 'none') {
						$tmplList['value'] = 'none';
						$tmplList['valueStr'] = '';

					} else {
						$tmplList['valueStr'] = $str;
					}

				} elseif (preg_match("/^SelectIdSubAccountTitle(.*?)$/", $tmplList['id'], $arrMatch)) {
					list($dummy, $idNum) = $arrMatch;
					$strIdAccountTitle = 'valueSelectIdAccountTitle' . $idNum;
					$idAccountTitle = $arr['varsValue'][$strIdAccountTitle];
					$str = $arrStrTitle[$idAccountTitle]['strTitleFS'];
					if (is_null($str) || $idAccountTitle == 'none') {
						$tmplList['value'] = 0;
						$tmplList['valueStr'] = '';

					} else {
						$arrSubStrTitle[$idAccountTitle]['0']['strTitle'] = '';
						$str = $arrSubStrTitle[$idAccountTitle][$tmplList['value']]['strTitle'];
						if (is_null($str)) {
							$tmplList['value'] = 0;
							$tmplList['valueStr'] = '';
							$arrayResetCheck[$numListAccountTitle] = $idNum;

						} elseif ($tmplList['value'] == 0) {
							$tmplList['value'] = 0;
							$tmplList['valueStr'] = $arr['vars']['varsItem']['arrNoneSub']['strTitle'];

						} else {
							$tmplList['valueStr'] = $str;
						}
					}
				}

				if ($tmplList['valueStr'] == '') {
					$tmplList['valueStr'] = $arr['vars']['varsItem']['strSpace'];
				}
				$varsList[] = $tmplList;
				$numList++;
			}
		}

		$arrayStr = array('TextSum', 'SelectMethod', 'TextYear', 'TextMonth', 'TextDate');
		foreach ($arrayStr as $keyStr => $valueStr) {
			$tmplList = $array['vars' . $valueStr];
			$tmplList['idTarget'] = 'value' . $tmplList['id'];
			$varsData['str' . $tmplList['id']] = $tmplList['strTitle'];
			$dataValue = $arr['varsValue'][$tmplList['idTarget']];
			if (!is_null($dataValue)) {
				$tmplList['value'] = $dataValue;
				if (preg_match("/^num/", $tmplList['flagValueType'])) {
					$tmplList['valueStr'] = ($dataValue === '')?  '' : number_format($dataValue);

				} else {
					$tmplList['valueStr'] = ($dataValue === '')?  '' : $dataValue;
				}

			}
			if ($tmplList['valueStr'] == '') {
				$tmplList['valueStr'] = $arr['vars']['varsItem']['strSpace'];
			}

			if ($tmplList['flagValueType'] == 'select') {
				$arrayOption = $tmplList['arrayOption'];
				foreach ($arrayOption as $keyOption => $valueOption) {
					if ($tmplList['value'] == $valueOption['value']) {
						$tmplList['valueStr'] = $valueOption['strTitle'];
						if ($valueOption['value'] == 0) {
							$tmplList['valueStr'] = $arr['vars']['varsItem']['strSpace'];
						}
						break;
					}
				}
			}

			$varsList[] = $tmplList;
		}

		$array = $arrayResetCheck;
		foreach ($array as $key => $value) {
			$varsList[$key]['value'] = 'none';
			$varsList[$key]['valueStr'] = $arr['vars']['varsItem']['strSpace'];
		}
		$varsData['arrRows'] = $arrRows;

		$data = array(
			'strFile'       => ucwords($arr['varsFlag']['flagMenu']),
			'varsData'      => ($varsData)? $varsData : array(),
			'varsList'      => $varsList,
			'flagBtnCalc'   => $arr['flagBtnCalc'],
		);

		return $data;
	}

	/**

	 */
	protected function _iniNaviReload()
	{
		$this->_setSearch();
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
			'numPage'  => $varsRequest['query']['jsonValue']['vars']['NumPage'],
		);

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$flagReset = $this->_checkValueDetail(array(
			'vars'        => $vars,
			'varsFlag'    => &$varsFlag,
			'flagDefault' => 1,
		));

		if ($flagReset) {
			$varsItem = $this->_getVarsItem(array(
				'vars'     => $vars,
				'varsFlag' => $varsFlag,
			));
			$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
				'vars'     => &$vars,
				'varsItem' => $varsItem,
			)));
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
				'varsNavi'           => $vars['portal']['varsNavi']['varsDetail'],
				'arrAccountTitle'    => $vars['varsItem']['arrAccountTitle'],
				'arrSubAccountTitle' => $vars['varsItem']['arrSubAccountTitle'],
				'varsPreference'     => $vars['varsItem']['varsPreference'],
				'varsDetail'         => $vars['portal']['varsDetail'],
				'varsFlag'           => $varsFlag,
				'varsIni'            => $vars['varsItem']['varsIni'],
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

		$flagReset = 0;
		$array = $arr['vars']['portal']['varsNavi']['varsDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'FlagMenu' || $value['id'] == 'NumPage') {
				$flag = 0;
				$id = $classEscape->toLower(array('str' => $value['id']));
				$arrayOption = $value['arrayOption'];
				foreach ($arrayOption as $keyOption => $valueOption) {
					if ($valueOption['value'] == $arr['varsFlag'][$id]) {
						$flag = 1;
					}
				}
				if (!$flag) {
					if ($arr['flagDefault']) {
						$arr['varsFlag'][$id] = $arr['vars']['varsFlag'][$id];
						$flagReset = 1;

					} else {
						if ($arr['flagOutput']) {
							$this->_send404Output();
						} else {
							$this->_sendOld();
						}
					}
				}

			}
		}

		return $flagReset;
	}

	/**
	 *
	 */
	protected function _iniDetailReload()
	{
		$this->_setSearch();
	}
}
