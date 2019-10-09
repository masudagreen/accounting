<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_Balance_2012_Public extends Code_Else_Plugin_Accounting_Jpn_Balance
{
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

		$this->_checkEntity();

		if ($varsRequest['query']['child']) {
			$this->_checkCorporationClass(array('flagChild' => 1));

		} else {
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
		}
		exit;
	}

	/**
		(array(
			'varsFS'                 => $arr['varsItem']['varsFS'],
			'varsFSValue'            => $arr['varsItem']['varsFSValue'],
		))

	 */
	protected function _getAccountTitleValueColumn($arr)
	{
		$array = &$arr['varsFS'];

		foreach ($array as $key => $value) {
			$array[$key]['varsValue']['numBalance'] = '';
			$array[$key]['varsColumnDetail']['numBalance'] = '';
			$array[$key]['flagBtnUse'] = 0;

			$idTarget = $value['vars']['idTarget'];

			if (!is_null($array[$key]['vars']['varsValue'])) {

				//numBalance
				$numBalance = $arr['varsFSValue']['f1'][$idTarget]['sumPrev'];
				if (is_null($numBalance)) {
					$numBalance = 0;
				}
				$cut = &$arr['varsItem']['arrAccountTitle']['arrSubAccountStrTitles']['BS'][$idTarget];
				$array[$key]['flagBtnUse'] = 1;
				if (!$cut['numSub']
					|| $idTarget == 'profitBroughtForward'
					|| $idTarget == 'suspenseReceiptOfConsumptionTaxes'
					|| $idTarget == 'suspensePaymentConsumptionTaxes'
					|| $arr['idParent'] == 'accountsReceivablesWrap'
					|| $arr['idParent'] == 'accountsPayablesWrap'
					|| $arr['idParent'] == 'netAssets'
				) {
					$array[$key]['flagBtnUse'] = 0;
				}
				$array[$key]['vars']['idAccountTitle'] = $idTarget;
				$array[$key]['strTitle'] = ($idTarget == 'departmentNet')? $array[$key]['strTitle'] : $cut['strTitleTree'];
				$array[$key]['varsValue']['numBalance'] = $numBalance;
				$array[$key]['varsColumnDetail']['numBalance'] = number_format($numBalance);
			}

			if ($value['child']) {
				$array[$key]['child'] = $this->_getAccountTitleValueColumn(array(
					'varsFS'      => $array[$key]['child'],
					'varsFSValue' => $arr['varsFSValue'],
					'varsItem'    => $arr['varsItem'],
					'idParent'    => $value['vars']['idTarget'],
				));
			}
		}

		return $array;
	}
}
