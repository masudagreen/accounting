<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_EntityChoiceWithoutConfig extends Code_Else_Plugin_Accounting_Entity
{
	protected $_childSelf = array(

	);

	protected $_extSelf = array(
		'pathTplJs'  => 'else/plugin/accounting/js/entityChoiceWithoutConfig.js',
		'pathVarsJs' => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/entityChoice.php',
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
			'strTableSearch'    => 'accountingAccountMemo',
			'strColumnSearch'   => 'jsonEntityNaviSearch',
			'flagEntitySearch'  => 0,
			'flagAccountSearch' => 1,
			'arrSearch'       => array(
				'idModule'  => 'accounting',
				'numLotNow' => 0,
				'strTable'  => 'accountingEntity',
				'arrOrder'  => array(
					'strColumn' => 'id',
					'flagDesc'  => 1,
				),
				'insCurrent' => $this,
				'arrWhere'   => array(),
			),
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
	 */
	public function getDBAuthority($arr)
	{
		global $classCheck;

		$flagAuthority = $classCheck->checkModuleAuthority(array(
			'idModule' => 'accounting',
		));

		$flagConfig = 0;
		if ($flagAuthority == 'webmaster' || $flagAuthority == 'admin') {
			$array = array(
				'strSql'   => 'flagConfig = ?',
				'arrValue' => array($flagConfig),
			);
			return $array;

		}

		return 0;

	}

	/**
	 *
	 */
	protected function _updateSearch($arr)
	{
		$vars = $arr['vars'];
		$rows = $arr['rows'];

		$array = $rows['arrRows'];
		$arrayNew = array();
		$num = 0;
		foreach ($array as $key => $value) {
			$var = $vars['portal']['varsList']['templateDetail'];
			$var['id'] = $value['id'];
			$var['vars']['idTarget'] = $value['id'];
			$var['strTitle'] = $value['strTitle'];
			$var['numFiscalPeriodStart'] = $value['numFiscalPeriodStart'];
			$var['numFiscalPeriod'] = $value['numFiscalPeriod'];
			$arrayNew[$num] = $var;
			$num++;
		}

		$vars['portal']['varsList']['varsPage']['varsStatus']['numRows'] = $rows['numRows'];
		$vars['portal']['varsList']['varsDetail'] = $arrayNew;

		return $vars;
	}

}
