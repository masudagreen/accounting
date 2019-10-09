<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Base_ModuleSearch extends Code_Else_Core_Base_Module
{
	protected $_childSelf = array(
		'pathTplJs'  => 'else/core/base/js/moduleSearch.js',
		'pathVarsJs' => 'back/tpl/vars/else/core/base/<strLang>/js/moduleSearch.php',
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
			'strTableSearch' => 'baseAccountMemo',
			'strColumnSearch' => 'jsonModuleNaviSearch',
			'arrSearch'       => array(),
		));
	}

	/**
	 *
	 */
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

		$json = json_encode($vars);
		$classSmarty->assign('varsLoad', $json);

		$classSmarty->assign('numNews', $this->getNumNews());
		$contents = $classSmarty->fetch($arr['pathTpl']);

		$this->sendJs(array(
			'data' => $contents,
		));
	}



}
