<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_BlueSheet_2012_Public extends Code_Else_Plugin_Accounting_Jpn_BlueSheet
{
	protected $_extSelf = array(
		'idPreference'          => 'blueSheetWindow',
		'numYearSheet'          => '2014',
		'pathTplJs'             => 'else/plugin/accounting/js/jpn/2012/public/blueSheet.js',
		'pathVarsJs'            => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/2012/public/blueSheet.php',
		'varsFixedAssetsOption' => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/depreciation.php',
		'pathItem'              => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/2012/public/blueSheet.php',
		'pathItemZeimusho'      => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/2012/public/zeimushoList.csv',
		'pathTplHtml'           => 'else/plugin/accounting/html/2012/public/blueSheet<%replace%>.html',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;
		global $varsPluginAccountingAccount;

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

		$varsFiscalPeriod = $this->_getVarsFiscalPeriod(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagFiscalPeriod' => 'f1',
		));

		if ($varsFiscalPeriod['numStartYear'] >= 2015) {
			$this->_extSelf['numYearSheet'] = '2015';
		}

		$this->_checkEntity();

		if ($varsRequest['query']['child']) {
			$str = ucwords($varsRequest['query']['ext']);
			$strChild = ucwords($varsRequest['query']['child']);
			$str = $str . $strChild;

			$path = PATH_BACK_CLASS_ELSE_PLUGIN
			. 'accounting'
			. '/' . PLUGIN_ACCOUNTING_STR_NATION
			. '/' . PLUGIN_ACCOUNTING_NUM_YEAR_SHEET
			. '/' . PLUGIN_ACCOUNTING_FLAG_CORPORATION
			. '/' . $str . '.php';


			$strClass = 'Code_Else_Plugin_Accounting'
				. '_' . ucwords(PLUGIN_ACCOUNTING_STR_NATION)
				. '_' . $str
				. '_' . PLUGIN_ACCOUNTING_NUM_YEAR_SHEET
				. '_' . ucwords(PLUGIN_ACCOUNTING_FLAG_CORPORATION);

			require_once($path);
			$classCall = new $strClass;
			$classCall->run();
			exit;

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
			'varsFlag' => $vars['varsFlag'],
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

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsLogFixedAssets = $this->_getVarsLogFixedAssets(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsCommon = $this->getVars(array(
			'path' => $this->_extSelf['pathItem'],
		));

		$varsSave = $this->_getVarsSave(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'numYearSheet'    => $this->_extSelf['numYearSheet'],
		));

		$varsSave['jsonData'] = $this->_getVarsJsonData(array(
			'blobData' => $varsSave['blobData'],
		));
		$varsSave['blobData'] = array();

		$varsZeimushoList = $this->_getVarsZeimushoList();

		$array = array('PL', 'BS', 'CR');
		foreach ($array as $key => $value) {
			if ($value == 'CR' && !$varsEntityNation['flagCR']) {
				continue;
			}

			//select form view prepare
			$str = 'jsonJgaapFS' . $value;
			$varsFS[$str] = $this->_setTreeId(array(
				'idParent' => '',
				'vars'     => $varsFS[$str],
			));

			//select form view
			$varsJgaapFS[$str] = $this->_getVarsItemJgaapFS(array(
				'arrStrTitle'  => array(),
				'arrSelectTag' => array(),
				'vars'         => $varsFS[$str],
			));
		}

		$varsFiscalPeriod = $this->_getVarsFiscalPeriod(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagFiscalPeriod' => 'f1',
		));

		$varsFixedAssets = $this->_getVarsFixedAssets(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsFixedAssetsOptions = $this->_getVarsFixedAssetsOptions();

		$data = array(
			'varsFS'                 => $varsFS,
			'varsFSValue'            => $varsFSValue,
			'varsEntityNation'       => $varsEntityNation,
			'varsJgaapFS'            => $varsJgaapFS,
			'varsLogFixedAssets'     => $varsLogFixedAssets,
			'varsFixedAssets'        => $varsFixedAssets,
			'varsFixedAssetsOptions' => $varsFixedAssetsOptions,
			'varsFlag'               => $arr['vars']['varsFlag'],
			'varsCommon'             => $varsCommon,
			'varsSave'               => $varsSave,
			'varsZeimushoList'       => $varsZeimushoList,
			'varsFiscalPeriod'       => $varsFiscalPeriod,
		);

		return $data;

	}

	/**
	 *
	 */
	protected function _getVarsJsonData($arr)
	{
		global $classCrypte;

		if (!$arr['blobData']) {
			return array();
		}
		$jsonDetail = $classCrypte->setDecrypt(array('data' => $arr['blobData']));
		$varsDetail = json_decode($jsonDetail, true);

		return $varsDetail;
	}

	/**
		(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent']
		))
	 */
	protected function _getVarsSave($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingBlueSheet' . $strNation,
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
					'flagType'      => '',
					'strColumn'     => 'numYearSheet',
					'flagCondition' => 'eq',
					'value'         => $this->_extSelf['numYearSheet'],
				),
			),
		));

		if (!$rows['numRows']) {
			return array();
		}

		return $rows['arrRows'][0];
	}

	/**
		(array(

		))
	 */
	protected function _getVarsZeimushoList()
	{
		global $classFile;

		$path = str_replace('<strLang>', STR_SYSTEM_LANG, $this->_extSelf['pathItemZeimusho']);

		$array = $classFile->getCsvRows(array('path' => $path));
		$arrStrTitle = array();
		foreach ($array as $key => $value) {
			$arrStrTitle[$value['id']] = $value;
			if (!$value['flagDisabled']) {
				unset($data['flagDisabled']);
			}
		}

		$arrayNew = array(
			'arrSelectTag' => array(),
			'arrStrTitle' => $arrStrTitle,
		);

		$array = $arrStrTitle;
		foreach ($array as $key => $value) {
			$data = array();
			$data['value'] = $value['id'];
			$data['strTitle'] = $value['strTitle'];
			if ($value['flagDisabled']) {
				$data['flagDisabled'] = 1;
			}
			$arrayNew['arrSelectTag'][] = $data;
		}

		return $arrayNew;
	}

	/**
		(array(

		))
	 */
	protected function _getVarsFixedAssetsOptions()
	{
		$arrStrTitle = $this->getVars(array(
			'path' => $this->_extSelf['varsFixedAssetsOption'],
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

		))
	 */
	protected function _getVarsLogFixedAssets($arr)
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
					'flagCondition' => 'eq',
					'value'         =>  $arr['numFiscalPeriod'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'flagRemove',
					'flagCondition' => 'ne',
					'value'         => 1,
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

		return $rows['arrRows'];
	}



	/**
		(array(

		))
	 */
	protected function _getVarsItemJgaapFS($arr)
	{
		$arrSelectTag = &$arr['arrSelectTag'];
		$arrStrTitle = &$arr['arrStrTitle'];

		$array = &$arr['vars'];
		foreach ($array as $key => $value) {
			$arr['arrStrTitle'][$value['vars']['idTarget']] = $value;

			$arrLevel = preg_split("/-/", $value['id']);
			$num = count($arrLevel) - 2;
			$arrLevel = array();
			for ($i = 0 ; $i < $num; $i++) {
				$arrLevel[$i] = '';
			}
			$str =  ' ' . join('.', $arrLevel) . ' ';
			$strTitle = $str . $value['strTitle'];

			if (is_null($value['vars']['flagUse'])) {
				$arr['arrSelectTag'][] = array(
					'strTitle'     => $strTitle,
					'value'        => '',
					'flagDisabled' => 1,
				);

			} else {
				$arr['arrSelectTag'][] = array(
					'strTitle'     => $strTitle,
					'value'        => $value['vars']['idTarget'],
				);
			}

			if ($value['child']) {
				$data = $this->_getVarsItemJgaapFS(array(
					'vars'          => $array[$key]['child'],
					'arrSelectTag'  => $arr['arrSelectTag'],
					'arrStrTitle'   => $arr['arrStrTitle'],
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
			if ($value['id'] == 'DummyNoneVersion') {
				if ($arr['varsItem']['varsFiscalPeriod']['numStartYear'] != 2014 && $arr['varsItem']['varsFiscalPeriod']['numStartYear'] != 2015) {
					$arrayNew[] = $value;
				}
				continue;
			}

			if ($arr['varsItem']['varsFiscalPeriod']['numStartYear'] != 2014 && $arr['varsItem']['varsFiscalPeriod']['numStartYear'] != 2015) {
				continue;
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
		if (preg_match("/^(tempNext)$/", $flag)
			|| ($arr['varsItem']['varsFiscalPeriod']['numStartYear'] != 2014 && $arr['varsItem']['varsFiscalPeriod']['numStartYear'] != 2015)
		) {
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

		}

		$arr['vars']['portal']['varsDetail']['varsDetail']['varsHtml'] = $this->_getDetailHtml(array(
			'strFile'    => $data['strFile'],
			'vars'       => $arr['vars'],
			'varsData'   => $data['varsData'],
		));



		if (method_exists($this, $method)) {
			$dataIni = $this->$method(array(
				'flagType'  => $flagMenu,
				'varsValue' => array(),
				'vars'      => $arr['vars'],
				'varsItem'  => $arr['varsItem'],
			));
		}
		$arr['vars']['varsItem']['varsIni'] = $dataIni['varsList'];

		$arr['vars']['varsItem']['varsList'] = $data['varsList'];
		$data = $arr['varsItem']['varsSave']['varsData'][$flagMenu];
		$arr['vars']['varsItem']['varsSave'] = ($data)? $data : array();

		$arr['vars']['varsItem']['varsCommon'] = $arr['varsItem']['varsCommon'];

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

	 */
	protected function _getDetailVars0($arr)
	{
		$varsList = array();
		$flagType = '0';
		$varsData = $arr['varsItem']['varsCommon']['varsStr'][$flagType];

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

			if ($value['id'] == 'SelectTypeZeimusho') {
				$tmplList['arrayOption'] = array_merge($tmplList['arrayOption'], $arr['varsItem']['varsZeimushoList']['arrSelectTag']);
				$tmplList['value'] = $dataValue;
				$tmplList['valueStr'] = $arr['varsItem']['varsZeimushoList']['arrStrTitle'][$dataValue]['strTitle'];

				if (is_null($dataValue)) {
					$tmplList['value'] = '';
					$tmplList['valueStr'] = '';
				}

			} elseif ($value['id'] == 'SelectTypeKoujo') {
				$tmp = $this->_getTargetOption(array(
					'arrayOption' => $tmplList['arrayOption'],
					'idTarget'   => $tmplList['value'],
				));
				$tmplList['valueStr'] = $tmp['strTitle'];

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
		);

		return $data;
	}

	/**
	 *
	 */
	protected function _getTargetOption($arr)
	{
		$array = $arr['arrayOption'];
		foreach ($array as $key => $value) {
			if ($value['value'] == $arr['idTarget']) {
				return $value;
			}
		}
		return array();
	}

	/**

	 */
	protected function _iniNaviSearch()
	{
		$this->_setSearch();
	}

	/**
	 *
	 */
	protected function _iniDetailReload()
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
			),
		));
	}

	/**
		(array(

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
	protected function _iniDetailOutput()
	{
		$this->_setClassExt(array('strClass' => 'BlueSheetOutput'));
	}
}
