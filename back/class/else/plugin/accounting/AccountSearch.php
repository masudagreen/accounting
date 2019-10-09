<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_AccountSearch extends Code_Else_Plugin_Accounting_Account
{
	protected $_childSelf = array(
		'pathTplJs'  => 'else/plugin/accounting/js/accountSearch.js',
		'pathVarsJs' => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/accountSearch.php',
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
			'strColumnSearch'   => 'jsonAccountNaviSearch',
			'flagEntitySearch'  => 0,
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

		$vars['portal']['varsNavi'] = $varsParent['portal']['varsNavi'];

		$vars['portal']['varsNavi']['search']['varsDetail']['varsMyRecord']['varsFormList']['varsDetail'] = $this->_getMemo(array(
			'strTable'  => $arr['strTableSearch'],
			'strColumn' => $arr['strColumnSearch'],
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

		$json = json_encode($vars);
		$classSmarty->assign('varsLoad', $json);

		$classSmarty->assign('numNews', $this->getNumNews());
		$contents = $classSmarty->fetch($arr['pathTpl']);

		$this->sendJs(array(
			'data' => $contents,
		));
	}
}
