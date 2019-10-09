<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_LogHouseSearch extends Code_Else_Plugin_Accounting_Jpn_LogHouse
{
	protected $_childSelf = array(
		'pathTplJs'  => 'else/plugin/accounting/js/jpn/logHouseSearch.js',
		'pathVarsJs' => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/logHouseSearch.php',
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
			'pathVars'        => $this->_childSelf['pathVarsJs'],
			'pathTpl'         => $this->_childSelf['pathTplJs'],
			'arrFolder' => array(),
			'strTableSearch'    => 'accountingAccountMemo',
			'strColumnSearch'   => 'jsonLogHouseNaviSearch',
			'flagEntitySearch'  => 1,
			'flagAccountSearch' => 1,
			'arrSearch'       => array(),
		));
	}

	protected function _setJs($arr)
	{
		global $classSmarty;

		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
		));

		$varsParent = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$vars['portal']['varsNavi'] = $varsParent['portal']['varsNavi'];
		$vars['portal']['varsDetail'] = $varsParent['portal']['varsDetail'];

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$vars['portal']['varsDetail']['templateDetail'] = $this->_updateVarsTemplateDetail((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$vars['portal']['varsNavi']['search']['varsDetail']['varsMyRecord']['varsFormList']['varsDetail'] = $this->_getMemo(array(
			'strTable'    => $arr['strTableSearch'],
			'strColumn'   => $arr['strColumnSearch'],
			'flagEntity'  => $arr['flagEntitySearch'],
			'flagAccount' => $arr['flagAccountSearch'],
		));

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
