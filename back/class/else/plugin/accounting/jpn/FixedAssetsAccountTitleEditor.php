<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_FixedAssetsAccountTitleEditor extends Code_Else_Plugin_Accounting_Jpn_FixedAssetsAccountTitle
{
	protected $_childSelf = array(
		'pathTplJs'  => 'else/plugin/accounting/js/jpn/fixedAssetsAccountTitleEditor.js',
		'pathVarsJs' => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/fixedAssetsAccountTitleEditor.php',
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
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
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
		$this->_setJsEditor(array(
			'pathVars'  => $this->_childSelf['pathVarsJs'],
			'pathTpl'   => $this->_childSelf['pathTplJs'],
			'arrFolder' => array(),
		));
	}

	/**

	 */
	protected function _iniDetailEdit()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $classEscape;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;
		global $varsRequest;

		if (!$this->_checkCurrent()) {
			$this->_sendOldError();
		}

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'update',
			'idTarget'      => $this->_extSelf['idPreference'],
		));

		if (!$flag) {
			$this->_sendOld();
		}

		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];
		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'idTarget' => $this->_extendSelf['idFixedAssets'],
		));

		$vars = $this->_updateVars(array(
			'idTarget'   => $this->_extendSelf['idFixedAssets'],
			'vars'       => $vars,
			'varsItem'   => $varsItem,
		));

		$varsTarget = $this->_getValueTemplate(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail'],
		));

		$varsTarget = $this->getValue(array(
			'vars' => $varsTarget,
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget
		));

		$this->_checkDetail(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'idTarget' => $idTarget,
			'arrValue' => $arrValue['arr']
		));

		$stampUpdate = TIMESTAMP;
		$jsonAccountTitle = $varsItem['varsFixedAssets'];
		if (!$jsonAccountTitle) {
			$jsonAccountTitle = array();
		}
		$jsonAccountTitle[$idTarget] = $arrValue['arr'];

		$jsonAccountTitle = json_encode($jsonAccountTitle);
		$this->checkTextSize(array(
			'flag' => 'errorDataMax',
			'str'  => $jsonAccountTitle,
		));
		$strAccountTitle = 'jsonAccountTitle';

		$arrDbColumn = array($strAccountTitle);
		$arrDbValue = array($jsonAccountTitle);

		try {
			$dbh->beginTransaction();

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingFixedAssets' . $strNation,
				'arrColumn' => $arrDbColumn,
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
						'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					),
				),
				'arrValue'  => $arrDbValue,
			));

			$this->_updateDbPreferenceStamp(array('strColumn' => 'fixedAssets'));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$this->_setSearch();
	}

	/**
		(array(
			'vars'        => $vars,
		))
	 */
	protected function _getValueTemplate($arr)
	{
		$arrayNew = array();
		$array = $arr['vars'];
		foreach ($array as $key => $value) {
			if (!preg_match("/^Dummy/", $value['id'])) {
				$arrayNew[] = $value;
			}
		}

		return $arrayNew;
	}


	/**
		(array(
			'varsItem' => $varsItem,
			'idTarget' => $idTarget,
			'arrValue' => $arrValue['arr']
		))
	 */
	protected function _checkDetail($arr)
	{
		$array = array(
			'numRatioSellingAdminCost',
			'numRatioProductsCost',
			'numRatioNonOperatingExpenses',
			'numRatioAgricultureCost'
		);
		foreach ($array as $key => $value) {
			if ($arr['arrValue'][$value]) {
				if (!preg_match("/^[0-9]{1,3}\.[0-9]{2,2}$/", $arr['arrValue'][$value])) {
					if (FLAG_TEST) {
						var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
					}
					exit;
				}
			}
		}

		if ($arr['arrValue']['numSurvivalRateLimit'] > $arr['arrValue']['numSurvivalRate']) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}

		$flag = $this->_checkIdTarget(array(
			'vars'     => $arr['vars']['portal']['varsList']['varsDetail'],
			'idTarget' => $arr['idTarget'],
		));
		if (!$flag) {
			$this->_sendOld();
		}

	}

	/**
		(array(
			'vars'     => $arr['vars']['portal']['varsList']['varsDetail'],
			'idTarget' => $arr['idTarget']
		))
	 */
	protected function _checkIdTarget($arr)
	{
		$array = &$arr['vars'];
		foreach ($array as $key => $value) {
			if (!is_null($value['vars']['flagUse'])) {
				if ($value['vars']['idTarget'] == $arr['idTarget']) {
					if ($value['vars']['flagDebit'] == 1
						&& !$value['vars']['flagCalc']
						&& $value['vars']['idTarget'] != 'accumulatedDepreciation'
						&& $value['vars']['idTarget'] != 'allowanceForBadDebtsLongTermOther'
					) {
						return 1;
					}
				}
			}

			if ($value['child']) {
				$flag = $this->_checkIdTarget(array(
					'vars'     => $array[$key]['child'],
					'idTarget' => $arr['idTarget'],
				));
				if ($flag) {
					return 1;
				}
			}
		}

		return $flag;
	}



}
