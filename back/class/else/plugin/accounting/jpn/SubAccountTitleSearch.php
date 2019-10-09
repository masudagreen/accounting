<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_SubAccountTitleSearch extends Code_Else_Plugin_Accounting_Jpn_SubAccountTitle
{
	protected $_childSelf = array(
		'pathTplJs'  => 'else/plugin/accounting/js/jpn/subAccountTitleSearch.js',
		'pathVarsJs' => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/subAccountTitleSearch.php',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;

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

		exit;
	}

	/**
	 *
	 */
	protected function _iniJs()
	{
		$this->_setJs(array(
			'pathVars'          => $this->_childSelf['pathVarsJs'],
			'pathTpl'           => $this->_childSelf['pathTplJs'],
			'arrFolder'         => array(),
			'strTableSearch'    => 'accountingAccountMemo',
			'strColumnSearch'   => 'jsonSubAccountTitleNaviSearch',
			'flagEntitySearch'  => 1,
			'flagAccountSearch' => 1,
			'arrSearch'         => array(),
		));
	}

	protected function _setJs($arr)
	{
		global $varsPluginAccountingAccount;
		global $classSmarty;

		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
		));

		$varsParent = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$vars['portal']['varsNavi'] = $varsParent['portal']['varsNavi'];
		$vars['portal']['varsDetail'] = $varsParent['portal']['varsDetail'];

		$vars = $this->_updateVars(array(
			'vars' => $vars
		));

		$vars['portal']['varsNavi']['search']['varsDetail']['varsMyRecord']['varsFormList']['varsDetail'] = $this->_getMemo(array(
			'strTable'    => $arr['strTableSearch'],
			'strColumn'   => $arr['strColumnSearch'],
			'flagEntity'  => $arr['flagEntitySearch'],
			'flagAccount' => $arr['flagAccountSearch'],
		));

		if ($arr['flagEntitySearch']) {
			$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
			$arr['arrSearch']['arrWhere'][] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idEntity',
				'flagCondition' => 'eq',
				'value'         => $idEntity,
			);
		}

		$vars['portal']['varsDetail'] = array();

		$json = json_encode($vars);
		$classSmarty->assign('varsLoad', $json);

		$classSmarty->assign('numNews', $this->getNumNews());
		$contents = $classSmarty->fetch($arr['pathTpl']);

		$this->sendJs(array(
			'data' => $contents,
		));
	}

	/**
		(array(
			'vars' => $vars,
		))
	 */
	protected function _updateVars($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;
		global $varsPluginAccountingAccountsEntity;
		global $varsPluginAccountingAuthority;

		$idAccount = $varsPluginAccountingAccount['id'];
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$idAuthority = $varsPluginAccountingAccountsEntity[$idAccount][$idEntity]['idAuthority'];
		$varsAuthority = $varsPluginAccountingAuthority[$idAuthority];

		if (!$varsAuthority['flagAllInsert']) {
			$arr['vars']['portal']['varsDetail']['varsStart']['varsEdit']['flagAddUse'] = 0;
		}

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done)$/", $flagCurrentFlagNow)) {
			$arr['vars']['portal']['varsDetail']['varsBtn'] = array();
			$arr['vars']['portal']['varsDetail']['view']['varsEdit'] = array();
			$arr['vars']['portal']['varsList']['varsBtn'] = array();
		}

		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrayFSList = $this->_getFSList(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$strCR = $this->_getStrCR(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrRestOption = array();
		$arrStrTitleData = array();
		$arrStrTitleVarsDetail = array();

		$array = $arrayFSList;
		foreach ($array as $key => $value) {

			$str = 'jsonJgaapAccountTitle'. $key;
			$varsFS[$str] = $this->_setTreeId(array(
				'idParent' => '-',
				'vars'     => $varsFS[$str],
			));

			$arrStrTitle = array();
			$varsAccountTitle = $this->_getArrSelectOption(array(
				'arrStrTitle'     => $arrStrTitle,
				'arrSelectTag'    => array(),
				'vars'            => $varsFS[$str],
				'flagFS'          => $key,
				'strCR'           => $strCR,
			));

			$arrRestOption[$key] = $varsAccountTitle['arrSelectTag'];

			$arrStrTitleData = array_merge($arrStrTitleData, $varsAccountTitle['arrStrTitle']);

			$dataStrTitleVarsDetail = array(
				'strTitle'     => $value,
				'value'        => 'dummy' . $key,
				'flagDisabled' => 1,
			);
			$arrStrTitleVarsDetail[] = $dataStrTitleVarsDetail;
			$arrStrTitleVarsDetail = array_merge($arrStrTitleVarsDetail, $varsAccountTitle['arrSelectTag']);
			$arrRestOption = $arrStrTitleVarsDetail;
		}
		$arr['vars']['varsItem']['arrStrTitle'] = $arrStrTitleData;
		$arr['vars']['portal']['varsNavi']['search']['varsDetail']['varsSearchItem']['templateDetail']['restOption']['fs'] = $arrRestOption;

		$arrayNew = array();
		$array = $arr['vars']['portal']['varsNavi']['search']['varsDetail']['varsSearchItem']['templateDetail']['firstOption'];

		foreach ($array as $key => $value) {
			$arrdata = preg_split("/-/", $value['value']);
			if ($arr['vars']['varsItem']['arrayFS'][$arrdata[0]]) {
				if (!$arrayFSList[$arrdata[0]]) {
					continue;
				}
			}
			$arrayNew[] = $value;
		}
		$arr['vars']['portal']['varsNavi']['search']['varsDetail']['varsSearchItem']['templateDetail']['firstOption'] = $arrayNew;

		return $arr['vars'];
	}
}
