<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_BalanceSubEditor_2012_Public extends Code_Else_Plugin_Accounting_Jpn_BalanceSubEditor
{

	/**
	 *
	 */
	protected function _iniDetailEdit()
	{
		global $classDb;
		global $classEscape;

		global $varsRequest;
		global $varsAccount;
		$dbh = $classDb->getHandle();
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		if (!$this->_checkCurrent()) {
			$this->_sendOld();
		}

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		if ($varsPluginAccountingAccount['numFiscalPeriodCurrent'] != $varsPluginAccountingEntity[$idEntity]['numFiscalPeriodStart']) {
			$this->_sendOld();
		}

		$varsAuthority = $this->_getVarsAuthority(array());

		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'])) {
			$this->_sendOld();
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsFlag = array(
			'idDepartment'   => $varsRequest['query']['jsonValue']['vars']['VarsFlag']['idDepartment'],
			'idAccountTitle' => $varsRequest['query']['jsonValue']['idTarget'],
		);

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$vars['portal']['varsList']['templateDetailEditor'] = $this->_updateVarsList((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$vars['portal']['varsDetail']['varsDetail'] = $this->_updateVarsDetail((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $vars['varsFlag'],
		));

		if (is_null($varsItem['varsDepartment']['arrStrTitle'][$varsFlag['idDepartment']])) {
			$this->_sendOld();
		}

		$varsAccountTitle = $varsItem['arrAccountTitle']['arrStrTitle'][$varsFlag['idAccountTitle']];
		if (is_null($varsAccountTitle)
			|| $varsFlag['idAccountTitle'] == 'profitBroughtForward'
			|| $varsFlag['idAccountTitle'] == 'suspenseReceiptOfConsumptionTaxes'
			|| $varsFlag['idAccountTitle'] == 'suspensePaymentConsumptionTaxes'
			|| $varsAccountTitle['idParent'] == 'accountsReceivablesWrap'
			|| $varsAccountTitle['idParent'] == 'accountsPayablesWrap'
			|| $varsAccountTitle['idParent'] == 'netAssets'
		) {
			$this->_sendOld();
		}

		$varsTarget = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget
		));

		$arrData = $this->_checkValueDetail(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'arrValue' => $arrValue,
			'varsFlag' => $varsFlag,
		));

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		$classCalcTempNextLog = $this->_getClassCalc(array(
			'flagType'   => 'TempNext',
			'flagDetail' => 'Log',
		));
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriodTempNext = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];

		try {
			$dbh->beginTransaction();

			$flag = $classCalcTempNextLog->allot(array(
				'flagStatus'      => 'edit',
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				'arrRowsAdd'      => $arrData['arrRowsAdd'],
				'arrRowsDelete'   => $arrData['arrRowsDelete'],
				'flagBalance'     => ($varsFlag['idDepartment'] == 0)? 'all' : 'department',
				'flagBalanceSub'  => 1,
			));
			if ($flag) {
				$this->sendVars(array(
					'flag'    => $flag,
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));
			}
			if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
				$flag = $classCalcTempNextLog->allot(array(
					'flagStatus'      => 'edit',
					'numFiscalPeriod' => $numFiscalPeriodTempNext,
					'arrRowsAdd'      => $arrData['arrRowsAdd'],
					'arrRowsDelete'   => $arrData['arrRowsDelete'],
					'flagBalance'     => ($varsFlag['idDepartment'] == 0)? 'all' : 'department',
					'flagBalanceSub'  => 1,
				));
				if ($flag) {
					$this->sendVars(array(
						'flag'    => $flag,
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(),
					));
				}
			}

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$varsSubValue = $this->_getVarsSubValue(array(
			'varsDepartment'     => $varsItem['varsDepartment'],
			'arrSubAccountTitle' => $varsItem['arrSubAccountTitle'],
			'numFiscalPeriod'    => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$this->sendVars(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'varsSubValue' => $varsSubValue,
			),
		));
	}


}
