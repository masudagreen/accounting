<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_FixedAssetsSearch extends Code_Else_Plugin_Accounting_Jpn_FixedAssets
{
	protected $_childSelf = array(
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/fixedAssetsSearch.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/fixedAssetsSearch.php',
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
		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$this->_setJs(array(
			'pathVars'        => $this->_childSelf['pathVarsJs'],
			'pathTpl'         => $this->_childSelf['pathTplJs'],
			'arrFolder' => array(),
			'strTableSearch'    => 'accountingAccountMemo',
			'strColumnSearch'   => 'jsonFixedAssetsNaviSearch',
			'flagEntitySearch'  => 1,
			'flagAccountSearch' => 1,
			'arrSearch'       => array(),
		));

	}

	/**
		$this->_setJs(array(
			'pathVars'        => '',
			'pathTpl'         => '',
			'arrFolder'       => array(),
			'strTableSearch'  => '',
			'strColumnSearch' => '',
			'flagEntitySearch'  => 0,
			'flagAccountSearch' => 0,
			'arrSearch'       => array(
				'idModule' => '',
				'numLotNow' => 0,
				'strTable'  => '',
				'arrOrder'  => array(),
				'arrWhere'  => array(),
			),
		));
	 */
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

		$vars['varsItem'] = $varsParent['varsItem'];
		$vars['portal']['varsNavi'] = $varsParent['portal']['varsNavi'];
		$vars['portal']['varsDetail'] = $varsParent['portal']['varsDetail'];

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
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

		$vars['varsItem']['arrAccountTitle'] = $varsItem['arrAccountTitle'];
		$vars['varsItem']['arrDepartment'] = $varsItem['arrDepartment'];
		$vars['varsItem']['varsOptions'] = $varsItem['varsOptions'];
		$vars['varsItem']['arrAccountTitleFixedAssets'] = $varsItem['arrAccountTitleFixedAssets'];
		$vars['varsItem']['varsStampTerm'] = $varsItem['varsStampTerm'];
		$vars['varsItem']['numFiscalTermMonth'] = $varsItem['varsEntityNation']['numFiscalTermMonth'];
		$vars['varsItem']['numFiscalBeginningMonth'] = $varsItem['varsEntityNation']['numFiscalBeginningMonth'];
		$vars['varsItem']['varsCalc'] = array(
			'flagFractionDepSurvivalRate' => $varsItem['varsFixedAssets']['flagFractionDepSurvivalRate'],
			'flagFractionDepSurvivalRateLimit' => $varsItem['varsFixedAssets']['flagFractionDepSurvivalRateLimit'],
			'flagFractionRatioOperate' => $varsItem['varsFixedAssets']['flagFractionRatioOperate'],
		);

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$vars['varsItem'] = array();
		$vars['portal']['varsDetail'] = array();

		$json = json_encode($vars);
		$classSmarty->assign('varsLoad', $json);

		$classSmarty->assign('numNews', $this->getNumNews());
		$contents = $classSmarty->fetch($arr['pathTpl']);

		$this->sendJs(array(
			'data' => $contents,
		));
	}



}
