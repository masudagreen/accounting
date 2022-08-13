<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Base_ModuleChoice extends Code_Else_Core_Base_Module
{
	protected $_childSelf = array(

	);

	protected $_extSelf = array(
		'pathTplJs'  => 'else/core/base/js/moduleChoice.js',
		'pathVarsJs' => 'back/tpl/vars/else/core/base/<strLang>/js/moduleChoice.php',
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
			'pathVars'        => $this->_extSelf['pathVarsJs'],
			'pathTpl'         => $this->_extSelf['pathTplJs'],
			'arrFolder' => array(),
			'strTableSearch' => 'baseAccountMemo',
			'strColumnSearch' => 'jsonModuleNaviSearch',
			'arrSearch'       => array(
				'idModule'  => 'base',
				'numLotNow' => 0,
				'strTable'  => 'baseModule',
				'arrOrder'  => array(
					'strColumn' => 'id',
					'flagDesc'  => 1,
				),
				'arrWhere'  => array(),
			),
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

		$rows = $this->getSearch($arr['arrSearch']);

		$vars = $this->_updateSearch(array(
			'vars' => $vars,
			'rows' => $rows,
		));

		$json = json_encode($vars);
		$classSmarty->assign('varsLoad', $json);

		$classSmarty->assign('numNews', $this->getNumNews());
		$contents = $classSmarty->fetch($arr['pathTpl']);

		$this->sendJs(array(
			'data' => $contents,
		));
	}

	/**
	 *
	 */
	protected function _updateSearch($arr)
	{
		global $varsRequest;
		global $varsAccount;

		$vars = $arr['vars'];
		$rows = $arr['rows'];

		$array = $rows['arrRows'];
		$arrayNew = array();
		$num = 0;
		foreach ($array as $key => $value) {
			$var = $vars['portal']['varsList']['templateDetail'];
			$var['id'] = $array[$key]['id'];
			$var['vars']['idTarget'] = $array[$key]['id'];
			$var['strTitle'] = $array[$key]['strTitle'];
			$arrayNew[$num] = $var;
			$num++;
		}
		$vars['portal']['varsList']['varsPage']['varsStatus']['numRows'] = $rows['numRows'];
		$vars['portal']['varsList']['varsDetail'] = $arrayNew;

		return $vars;
	}

}
