<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_ConsumptionTax extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference' => 'consumptionTaxWindow',
		'idSelf'       => 'ConsumptionTax',
		'flagReport' => '2012',
		'pathTplJs' => 'else/plugin/accounting/js/jpn/consumptionTax.js',
		'pathVarsJs'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/consumptionTax.php',
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

		$this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent']
		));

		if ($varsEntityNation['flagConsumptionTaxFree']) {
			$this->sendValue(array(
				'flag'    => 8,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}

		$this->_checkEntity();
		$idSelf = $this->_extSelf['idSelf'];
		$str = ucwords($varsRequest['query']['ext']);
		if ($str != $idSelf) {
			$this->_updateFlagReport();

			list($dummy, $str) = preg_split("/^$idSelf/", $str);

			$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/' . PLUGIN_ACCOUNTING_STR_NATION . '/' . $this->_extSelf['flagReport'] . '/consumptionTax/' . $str . ".php";
			$strClass = 'Code_Else_Plugin_Accounting_' . ucwords(PLUGIN_ACCOUNTING_STR_NATION) . '_' . $this->_extSelf['flagReport'] . '_ConsumptionTax_' . $str;

			if (!file_exists($path)) {
				$this->sendValue(array(
					'flag'    => 8,
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));
			}
			require_once($path);
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

		$arrWhere = array(
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
				'strColumn'     => 'flagReport',
				'flagCondition' => 'eq',
				'value'         => $arr['flagReport'],
			),
			array(
				'flagType'      => '',
				'strColumn'     => 'flagDetail',
				'flagCondition' => 'eq',
				'value'         => $arr['flagDetail'],
			),
		);

		if (!is_null($arr['id'])) {
			$arrWhere[] = array(
				'flagType'      => '',
				'strColumn'     => 'id',
				'flagCondition' => 'eq',
				'value'         => $arr['id'],
			);
		}

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingConsumptionTax' . $strNation,
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => $arrWhere,
		));

		if (!$rows['numRows']) {
			return array();
		}

		if ($arr['flagVars']) {
			return $rows['arrRows'][0];
		}

		$arrayNew = array();
		$array = $rows['arrRows'];
		foreach ($array as $key => $value) {
			$arrayNew[$value['id']] = $value;
		}

		return $arrayNew;
	}

	/**
		(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'flagReport'      => $arr['flagReport'],
		))
	 */
	protected function _getVarsPreference($arr)
	{
		$varsPreference = $this->_getVarsSave(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'flagReport'      => $arr['flagReport'],
			'flagDetail'      => 'preference',
			'flagVars'        => 1,
		));

		return $varsPreference;
	}



	/**
	 *
	 */
	protected function _updateFlagReport()
	{
		global $varsPluginAccountingAccount;

		$varsJsonFlag = $this->_getJsonFlag(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idTarget'        => 'consumptionTax',
		));

		if ($varsJsonFlag) {
			$this->_extSelf['flagReport'] = $varsJsonFlag['flagReport'];
		}
	}

	/**
	 *
	 */
	protected function _iniJs()
	{
		global $classSmarty;

		$vars = $this->_getVarsJs();

		$json = json_encode($vars);
		$classSmarty->assign('varsLoad', $json);

		$classSmarty->assign('numNews', $this->getNumNews());
		$contents = $classSmarty->fetch($this->_extSelf['pathTplJs']);

		$this->sendJs(array(
			'data' => $contents,
		));

	}

/**
	 *
	 */
	protected function _getVarsJs()
	{
		global $varsPluginAccountingAccount;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent']
		));

		$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_updateVars(array(
			'vars'             => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
			'varsEntityNation' => $varsEntityNation,
		));

		return $vars;
	}

	/**
	 *
	 */
	protected function _updateVars($arr)
	{
		$vars = $arr['vars'];

		if ($arr['varsEntityNation']['flagConsumptionTaxGeneralRule']) {
			$vars = $this->_removeTreeData(array(
				'vars'     => $vars,
				'idTarget' => $this->_extSelf['flagReport'],
				'strMatch' => '^(consumptionTaxSimple)',
			));

		} else {
			$vars = $this->_removeTreeData(array(
				'vars'     => $vars,
				'idTarget' => $this->_extSelf['flagReport'],
				'strMatch' => '^(consumptionTaxGeneral)',
			));
		}


		foreach ($vars as $key => $value) {
			$idTarget = $value['vars']['idTarget'];
			$id = $value['id'];

			if (!preg_match( "/Window$/", $idTarget) && !$value['flagFoldUse']) {
				$method = '_updateVars' . ucwords($idTarget);

				if (method_exists($this, $method)) {

					$vars[$key] = $this->$method(array(
						'vars'             => $vars[$key],
						'varsEntityNation' => $arr['varsEntityNation'],
					));
				}
			}

			if ($value['child']) {
				$vars[$key]['child'] = $this->_updateVars(array(
					'vars'             => $vars[$key]['child'],
					'varsEntityNation' => $arr['varsEntityNation'],
				));
			}
		}

		return $vars;
	}


	/**
	 *
	 */
	protected function _iniNaviReload()
	{
		global $varsPluginAccountingPreference;
		global $varsRequest;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingPreference['stampUpdate'],
				'flagSearch'  => 0,
			));
		}

		$vars = $this->_getVarsJs();

		$this->sendValue(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
		));
	}


	/**
	 *
	 */
	protected function _iniDetailReload()
	{
		global $varsPluginAccountingPreference;
		global $varsPluginAccountingAccount;
		global $varsRequest;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingPreference['stampUpdate'],
				'flagSearch'  => 0,
			));
		}

		$vars = $this->_getVarsJs();
		$varsTarget = $this->getVarsTarget(array(
			'vars' => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail']
		));

		if (!$varsTarget) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}

		$method = '_updateVars' . ucwords($varsTarget['vars']['idTarget']);
		if (method_exists($this, $method)) {
			$varsEntityNation = $this->_getVarsEntityNation(array(
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent']
			));
			$varsTarget = $this->$method(array(
				'varsEntityNation' => $varsEntityNation,
				'vars'             => $varsTarget,
			));
		}


		$this->sendValue(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $varsTarget,
		));
	}

	/**
	 *
	 */
	protected function _updateVarsFlagReport($arr)
	{
		$this->_updateFlagReport();

		$vars = $arr['vars'];
		$array = &$vars['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			$array[$key]['value'] = $this->_extSelf['flagReport'];
		}

		return $vars;
	}

	/**
	 *
	 */
	protected function _iniDetailEdit()
	{
		global $classInit;
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsRequest;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];

		$vars = $this->_getVarsJs();

		$varsTarget = $this->getVarsTarget(array(
			'vars' => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail']
		));

		if (!$varsTarget) {
			$this->sendValue(array(
				'flag'    => 8,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}

		$varsTarget['vars']['varsDetail'] = $this->getValue(array(
			'vars' => $varsTarget['vars']['varsDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget['vars']['varsDetail']
		));

		try {
			$dbh->beginTransaction();

			$this->_updateDb($arrValue);

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$method = '_updateVars' . ucwords($varsTarget['vars']['idTarget']);
		if (method_exists($this, $method)) {
			$varsTarget = $this->$method(array(
				'vars'     => $varsTarget,
				'flagDone' => 1,
			));
		}

		$this->sendValue(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $varsTarget,
		));
	}

	/**
	 *
	 */
	protected function _updateDb($arr)
	{
		global $varsRequest;

		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];
		$method = '_updateDb' . ucwords($idTarget);
		if (method_exists($this, $method)) {
			$this->$method($arr);
		}
	}

	/**
		(array(

		))
	 */
	protected function _updateDbFlagReport($arrValue)
	{
		global $varsPluginAccountingAccount;

		$flagReport = $arrValue['arr']['flagReport'];
		$this->_updateJsonFlag(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idTarget'        => 'consumptionTax',
			'varsTarget'      => array(
				'flagReport' => $flagReport,
			),
		));
		$this->_updateDbPreferenceStamp(array('strColumn' => 'consumptionTax'));

	}
}
