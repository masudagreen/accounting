<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcFixedAssets extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extChildSelf = array(

	);

	/**
	 * tempNext only
	 */
	public function run()
	{
		if (FLAG_TEST) {
			var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
		}
		exit;
	}

	/*
		(array(

		))
	 * */
	public function allot($arr)
	{
		$method = '_ini' . ucwords($arr['flagStatus']);
		if (method_exists($this, $method)) {
			return $this->$method($arr);

		} else {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}
	}

	/**
		(array(
			'flagStatus'      => 'updateIdAccountTitle',
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idTargetOld'     => $arr['idTarget'],
			'idTargetNew'     => $idAccountTitleCustom,
		))
	 */
	protected function _iniUpdateIdAccountTitle($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$varsFixedAssets = $this->_getVarsFixedAssets(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$jsonAccountTitle = $varsFixedAssets['jsonAccountTitle'];
		if (!$jsonAccountTitle) {
			return;
		}
		$jsonAccountTitle[$arr['idTargetNew']] = $jsonAccountTitle[$arr['idTargetOld']];
		unset($jsonAccountTitle[$arr['idTargetOld']]);

		$jsonAccountTitle = json_encode($jsonAccountTitle);
		$strAccountTitle = 'jsonAccountTitle';

		$arrDbColumn = array($strAccountTitle);
		$arrDbValue = array($jsonAccountTitle);

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
					'value'         => $arr['numFiscalPeriod'],
				),
			),
			'arrValue'  => $arrDbValue,
		));
		$this->_updateDbPreferenceStamp(array('strColumn' => 'fixedAssets'));
	}


}
