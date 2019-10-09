<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcLogConsumptionTax extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extChildSelf = array(

	);

	/**
	 *
	 */
	public function run()
	{
		if (FLAG_TEST) {
			var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
		}
		exit;
	}

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
			'varsItem' => $varsItem,
			'idEntity' => $arr['idEntity'],
		))
	 */
	protected function _updateImport($arr)
	{
		$arrRows = $arr['arrRows'];
		if (!$arrRows) {
			$arrRows = $this->_getArrVarsLogImport(array(
				'numFiscalPeriod' => $arr['varsItem']['numFiscalPeriod'],
				'idEntity'        => $arr['idEntity'],
			));
		}

		$arrVarsLogAll = $this->_updateArrVarsLog(array(
			'arrVarsLog' => $arrRows,
			'varsItem'   => $arr['varsItem'],
		));

		$classCalcLogImport = $this->_getClassCalc(array('flagType' => 'LogImport'));
		$flagErrorVars = $classCalcLogImport->allot(array(
			'flagStatus'       => 'UpdateTax',
			'arrRows'          => $arrVarsLogAll,
			'numFiscalPeriod'  => $arr['varsItem']['numFiscalPeriod'],
			'idEntity'         => $arr['idEntity'],
			'varsEntityNation' => $arr['varsItem']['varsEntityNationUpdate'],
		));
		if ($flagErrorVars) {
			return $flagErrorVars;
		}
	}

	/**
		(array(
			'varsItem' => $varsItem,
			'idEntity' => $arr['idEntity'],
		))
	 */
	protected function _updateHouse($arr)
	{
		$arrRows = $arr['arrRows'];
		if (!$arrRows) {
			$arrRows = $this->_getArrVarsLogHouse(array(
				'numFiscalPeriod' => $arr['varsItem']['numFiscalPeriod'],
				'idEntity'        => $arr['idEntity'],
			));
		}

		$arrVarsLogAll = $this->_updateArrVarsLog(array(
			'arrVarsLog' => $arrRows,
			'varsItem'   => $arr['varsItem'],
		));

		$classCalcLogHouse = $this->_getClassCalc(array('flagType' => 'LogHouse'));
		$flagErrorVars = $classCalcLogHouse->allot(array(
			'flagStatus'       => 'UpdateTax',
			'arrRows'          => $arrVarsLogAll,
			'numFiscalPeriod'  => $arr['varsItem']['numFiscalPeriod'],
			'idEntity'         => $arr['idEntity'],
			'varsEntityNation' => $arr['varsItem']['varsEntityNationUpdate'],
		));
		if ($flagErrorVars) {
			return $flagErrorVars;
		}
	}

	/**
	 (array(

	 ))
	 */
	protected function _getArrVarsLogHouse($arr)
	{
		global $classDb;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogHouse' . $strNation,
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere'  => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idEntity',
					'flagCondition' => 'eq',
					'value'         => $arr['idEntity'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'numFiscalPeriod',
					'flagCondition' => 'eq',
					'value'         => $arr['numFiscalPeriod'],
				),
			),
		));

		return $rows['arrRows'];
	}

	/**
	 (array(

	 ))
	 */
	protected function _getArrVarsLogImport($arr)
	{
		global $classDb;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogImport' . $strNation,
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere'  => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idEntity',
					'flagCondition' => 'eq',
					'value'         => $arr['idEntity'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'numFiscalPeriod',
					'flagCondition' => 'eq',
					'value'         => $arr['numFiscalPeriod'],
				),
			),
		));

		return $rows['arrRows'];
	}

	/**
		(array(
			'flagStatus'       => 'UpdateCash',
			'arrRows'          => $arrRows,
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'idEntity'         => $arr['idEntity'],
		))
	 */
	protected function _iniUpdateVars($arr)
	{
		$varsItem = $this->_getVarsItemUpdate(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$arrVarsLog = $this->_updateArrVarsLog(array(
			'arrVarsLog' => $arr['arrRows'],
			'varsItem'   => $varsItem,
		));

		return $arrVarsLog;
	}

	/**
		(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _getVarsItemUpdate($arr)
	{
		$arrAccountTitle = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => array(),
			'numFiscalPeriod'    => $arr['numFiscalPeriod'],
		));

		$varsConsumptionTax = $this->_getVarsConsumptionTax(array());

		$varsEntityNationUpdate = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$data = array(
			'varsConsumptionTax'     => $varsConsumptionTax,
			'arrAccountTitle'        => $arrAccountTitle,
			'varsEntityNationUpdate' => $varsEntityNationUpdate,
			'numFiscalPeriod'        => $arr['numFiscalPeriod'],
		);

		return $data;
	}

	/**
		(array(
			'varsItem' => $varsItem,
			'idEntity' => $arr['idEntity'],
		))
	 */
	protected function _updateCashDefer($arr)
	{
		$arrRows = $arr['arrRows'];
		if (!$arrRows) {
			$arrRows = $this->_getArrVarsLogCashDefer(array(
				'numFiscalPeriod' => $arr['varsItem']['numFiscalPeriod'],
				'idEntity'        => $arr['idEntity'],
			));
		}

		$arrVarsLogAll = $this->_updateArrVarsLog(array(
			'arrVarsLog' => $arrRows,
			'varsItem'   => $arr['varsItem'],
		));

		$classCalcCashDefer = $this->_getClassCalc(array('flagType' => 'CashDefer'));

		$flagErrorVars = $classCalcCashDefer->allot(array(
			'flagStatus'       => 'UpdateTax',
			'arrRows'          => $arrVarsLogAll,
			'arrRowsPrev'      => $arrRows,
			'numFiscalPeriod'  => $arr['varsItem']['numFiscalPeriod'],
			'idEntity'         => $arr['idEntity'],
			'varsEntityNation' => $arr['varsItem']['varsEntityNationUpdate'],
		));

		if ($flagErrorVars['flag'] == 'textMaxOver') {
			return $flagErrorVars;
		}
	}

	/**
	 (array(

	 ))
	 */
	protected function _getArrVarsLogCashDefer($arr)
	{
		global $classDb;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogCashDefer',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere'  => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idEntity',
					'flagCondition' => 'eq',
					'value'         => $arr['idEntity'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'numFiscalPeriod',
					'flagCondition' => 'eq',
					'value'         => $arr['numFiscalPeriod'],
				),
			),
		));

		return $rows['arrRows'];
	}

	/**
		(array(
			'varsItem' => $varsItem,
			'idEntity' => $arr['idEntity'],
		))
	 */
	protected function _updateCash($arr)
	{
		$arrRows = $arr['arrRows'];
		if (!$arrRows) {
			$arrRows = $this->_getArrVarsLogCash(array(
				'numFiscalPeriod' => $arr['varsItem']['numFiscalPeriod'],
				'idEntity'        => $arr['idEntity'],
			));
		}

		$arrVarsLogAll = $this->_updateArrVarsLog(array(
			'arrVarsLog' => $arrRows,
			'varsItem'   => $arr['varsItem'],
		));

		$classCalcCash = $this->_getClassCalc(array('flagType' => 'Cash'));

		$flagErrorVars = $classCalcCash->allot(array(
			'flagStatus'       => 'UpdateTax',
			'arrRows'          => $arrVarsLogAll,
			'arrRowsPrev'      => $arrRows,
			'numFiscalPeriod'  => $arr['varsItem']['numFiscalPeriod'],
			'idEntity'         => $arr['idEntity'],
			'varsEntityNation' => $arr['varsItem']['varsEntityNationUpdate'],
		));

		if ($flagErrorVars['flag'] == 'textMaxOver') {
			return $flagErrorVars;
		}

		$varsData = $flagErrorVars;

		if ($varsData['arrVarsDone']) {
			$flag = $classCalcCash->allot(array(
				'flagStatus'      => 'editDone',
				'arrRowsAdd'      => $varsData['arrVarsDoneUpdate'],
				'arrRowsDelete'   => $varsData['arrVarsDone'],
				'numFiscalPeriod' => $arr['varsItem']['numFiscalPeriod'],
				'idEntity'        => $arr['idEntity'],
			));
			if ($flag == 'errorDataMax') {
				return $flag;
			}
		}

		if ($varsData['arrVarsPre']) {
			$flag = $classCalcCash->allot(array(
				'flagStatus'      => 'editPre',
				'arrRowsAdd'      => $varsData['arrVarsPreUpdate'],
				'arrRowsDelete'   => $varsData['arrVarsPre'],
				'numFiscalPeriod' => $arr['varsItem']['numFiscalPeriod'],
				'idEntity'        => $arr['idEntity'],
			));
			if ($flag == 'errorDataMax') {
				return $flag;
			}
		}

	}

	/**
	 (array(

	 ))
	 */
	protected function _getArrVarsLogCash($arr)
	{
		global $classDb;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogCash',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere'  => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idEntity',
					'flagCondition' => 'eq',
					'value'         => $arr['idEntity'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'numFiscalPeriod',
					'flagCondition' => 'eq',
					'value'         => $arr['numFiscalPeriod'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'flagRemove',
					'flagCondition' => 'eq',
					'value'         => 0,
				),
			),
		));

		return $rows['arrRows'];
	}

	/**
		(array(
			'flagStatus'              => 'update',
			'flagType'                => $arr['flagType'],
			'idEntity'                => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagTempPrev'            => (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow))? 1 : 0,
			'numFiscalPeriodTempNext' => $numFiscalPeriodTempNext,
			'varsValue'               => $arr,
		))
	 */
	protected function _iniUpdate($arr)
	{
		global $classPluginAccountingInit;

		$flag = $this->_checkType(array(
			'flagType'        => $arr['flagType'],
			'varsValue'       => $arr['varsValue'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		if ($flag == 'none') {
			return;

		} elseif ($flag == 'varsEntityNation') {
			$this->_updateDbVarsEntityNation(array(
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
				'varsValue'       => $arr['varsValue'],
			));

		} else {
			$varsItem = $this->_getVarsItem(array(
				'flagType'        => $arr['flagType'],
				'varsValue'       => $arr['varsValue'],
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
			));

			$arrVarsLog = $this->_getArrVarsLog(array(
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
			));

			if (!$arrVarsLog['arrAll']) {
				$this->_updateDbVarsEntityNation(array(
					'numFiscalPeriod' => $arr['numFiscalPeriod'],
					'varsValue'       => $arr['varsValue'],
				));
				$this->_updateDbPreferenceStamp(array('strColumn' => 'entity'));
				$classPluginAccountingInit->updateInitPreference();

				//cash insert
				$flagErrorVarsCash = $this->_updateCash(array(
					'varsItem' => $varsItem,
					'idEntity' => $arr['idEntity'],
				));
				if ($flagErrorVarsCash == 'errorDataMax') {
					return $flagErrorVarsCash;
				}

				//cashDefer insert
				$flagErrorVarsCashDefer = $this->_updateCashDefer(array(
					'varsItem' => $varsItem,
					'idEntity' => $arr['idEntity'],
				));
				if ($flagErrorVarsCashDefer == 'errorDataMax') {
					return $flagErrorVarsCashDefer;
				}

				//import insert
				$flagErrorVarsImport = $this->_updateImport(array(
					'varsItem' => $varsItem,
					'idEntity' => $arr['idEntity'],
				));

				//house insert
				$flagErrorVarsHouse = $this->_updateHouse(array(
					'varsItem' => $varsItem,
					'idEntity' => $arr['idEntity'],
				));

				if ($flagErrorVarsCash
					|| $flagErrorVarsCashDefer
					|| $flagErrorVarsImport
					|| $flagErrorVarsHouse
				) {
					$data = array(
						'flag'           => 'textMaxOver',
						'arrIdLog'       => array(),
						'arrIdLogCash'   => $flagErrorVarsCash['arrayIdLog'],
						'arrIdLogImport' => $flagErrorVarsImport['arrayIdLog'],
						'arrIdLogHouse'  => $flagErrorVarsHouse['arrayIdLog'],
					);
					return $data;
				}
				return;
			}

			$flagError = $this->_setFSValue(array(
				'flagStatus'              => 'delete',
				'arrVarsLog'              => $arrVarsLog['arrDone'],
				'numFiscalPeriod'         => $arr['numFiscalPeriod'],
				'numFiscalPeriodTempNext' => $arr['numFiscalPeriodTempNext'],
				'flagTempPrev'            => $arr['flagTempPrev'],
			));
			if ($flagError == 'errorDataMax') {
				return $flagError;
			}

			$arrVarsLogAll = $this->_updateArrVarsLog(array(
				'arrVarsLog' => $arrVarsLog['arrAll'],
				'varsItem'   => $varsItem,
			));

			$flagErrorVars = $this->_updateDbArrVarsLog(array(
				'numFiscalPeriod'  => $arr['numFiscalPeriod'],
				'arrVarsLog'       => $arrVarsLogAll,
				'idEntity'         => $arr['idEntity'],
				'varsEntityNation' => $varsItem['varsEntityNationUpdate'],
			));

			//cash insert
			$flagErrorVarsCash = $this->_updateCash(array(
				'varsItem' => $varsItem,
				'idEntity' => $arr['idEntity'],
			));
			if ($flagErrorVarsCash == 'errorDataMax') {
				return $flagErrorVarsCash;
			}

			//cashDefer insert
			$flagErrorVarsCashDefer = $this->_updateCashDefer(array(
				'varsItem' => $varsItem,
				'idEntity' => $arr['idEntity'],
			));
			if ($flagErrorVarsCashDefer == 'errorDataMax') {
				return $flagErrorVarsCashDefer;
			}

			//import insert
			$flagErrorVarsImport = $this->_updateImport(array(
				'varsItem' => $varsItem,
				'idEntity' => $arr['idEntity'],
			));

			//house insert
			$flagErrorVarsHouse = $this->_updateHouse(array(
				'varsItem' => $varsItem,
				'idEntity' => $arr['idEntity'],
			));

			if ($flagErrorVars
				|| $flagErrorVarsCash
				|| $flagErrorVarsCashDefer
				|| $flagErrorVarsHouse
				|| $flagErrorVarsImport
			) {
				$data = array(
					'flag'              => 'textMaxOver',
					'arrIdLog'          => $flagErrorVars['arrayIdLog'],
					'arrIdLogCash'      => $flagErrorVarsCash['arrayIdLog'],
					'arrIdLogCashDefer' => $flagErrorVarsCashDefer['arrayIdLog'],
					'arrIdLogHouse'     => $flagErrorVarsHouse['arrayIdLog'],
					'arrIdLogImport'    => $flagErrorVarsImport['arrayIdLog'],
				);

				return $data;
			}

			$this->_updateDbVarsEntityNation(array(
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
				'varsValue'       => $arr['varsValue'],
			));

			$arrVarsLogDone = array();
			$array = $arrVarsLogAll;
			foreach ($array as $key => $value) {
				if (!$value['flagApply']) {
					$arrVarsLogDone[] = $value;
				}
			}

			$flagError = $this->_setFSValue(array(
				'flagStatus'              => 'add',
				'arrVarsLog'              => $arrVarsLogDone,
				'numFiscalPeriod'         => $arr['numFiscalPeriod'],
				'numFiscalPeriodTempNext' => $arr['numFiscalPeriodTempNext'],
				'flagTempPrev'            => $arr['flagTempPrev'],
			));
			if ($flagError == 'errorDataMax') {
				return $flagError;
			}

			$this->_updateDbPreferenceStamp(array('strColumn' => 'log'));
		}
		$this->_updateDbPreferenceStamp(array('strColumn' => 'entity'));
		$classPluginAccountingInit->updateInitPreference();
	}

	/**
	 (array(

	 ))
	 */
	protected function _checkValueFlagConsumptionTax($arr)
	{
		$array = $arr['arrList'];
		foreach ($array as $key => $value) {
			if ($arr['varsEntityNation'][$value] != $arr['arr'][$value]) {
				return 1;
			}
		}
	}

	/**
	 (array(

	 ))
	 */
	protected function _checkType($arr)
	{
		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$arrList = array($arr['flagType']);
		if ($arr['flagType'] == 'flagConsumptionTaxIncluding') {
			$arrList[] = 'flagConsumptionTaxCalc';
		}

		//check update value
		$flag = $this->_checkValueFlagConsumptionTax(array(
			'varsEntityNation' => $varsEntityNation,
			'arrList'          => $arrList,
			'arr'              => $arr['varsValue']['arr']
		));
		if (!$flag) {
			return 'none';
		}

		//update default value
		if ($arr['flagType'] == 'flagConsumptionTaxIncluding') {
			if ((int) $varsEntityNation['flagConsumptionTaxIncluding'] == $arr['varsValue']['arr']['flagConsumptionTaxIncluding']
				&& (int) $varsEntityNation['flagConsumptionTaxCalc'] != $arr['varsValue']['arr']['flagConsumptionTaxCalc']
			) {
				return 'varsEntityNation';
			}

		} else {
			if ((int) $varsEntityNation[$arr['flagType']] != $arr['varsValue']['arr'][$arr['flagType']]) {
				if ($arr['flagType'] == 'flagConsumptionTaxWithoutCalc' || $arr['flagType'] == 'flagConsumptionTaxBusinessType') {
					return 'varsEntityNation';
				}
			}
		}
	}

	/**
		(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _getVarsItem($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$arrAccountTitle = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => array(),
			'numFiscalPeriod'    => $arr['numFiscalPeriod'],
		));

		$varsConsumptionTax = $this->_getVarsConsumptionTax(array());

		$varsEntityNationUpdate = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$array = &$varsEntityNationUpdate;
		foreach ($array as $key => $value) {
			if ($key == 'stampUpdate') {
				$array[$key] = TIMESTAMP;
				continue;
			}
			if (!is_null($arr['varsValue']['arr'][$key])) {
				$array[$key] = (int) $arr['varsValue']['arr'][$key];
			}
		}

		$data = array(
			'varsConsumptionTax'     => $varsConsumptionTax,
			'arrAccountTitle'        => $arrAccountTitle,
			'varsEntityNationUpdate' => $varsEntityNationUpdate,
			'numFiscalPeriod'        => $arr['numFiscalPeriod'],
			'varsValue'              => $arr['varsValue'],
			'flagType'               => $arr['flagType'],
		);

		return $data;
	}

	/**
	 (array(

	 ))
	 */
	protected function _getArrVarsLog($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLog',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere'  => array(
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
					'flagType'      => 'num',
					'strColumn'     => 'flagRemove',
					'flagCondition' => 'eq',
					'value'         => 0,
				),
			),
		));

		$data = array(
			'arrAll'  => array(),
			'arrDone' => array(),
		);

		$array = $rows['arrRows'];
		foreach ($array as $key => $value) {
			$data['arrAll'][] = $value;
			if (!$value['flagApply']) {
				$data['arrDone'][] = $value;
			}
		}

		return $data;
	}

	/**
	 (array(
		'flagStatus'              => 'add',
		'arrVarsLog'              => $arrVarsLogDone,
		'numFiscalPeriod'         => $arr['numFiscalPeriod'],
		'numFiscalPeriodTempNext' => $arr['numFiscalPeriodTempNext'],
		'flagTempPrev'            => $arr['flagTempPrev'],
	 ))
	 */
	protected function _setFSValue($arr)
	{
		$arrRows = $this->_getVarsLogCalcLoop(array(
			'arrVarsLog'      => $arr['arrVarsLog'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$classCalcAccountTitle = $this->_getClassCalc(array('flagType' => 'AccountTitle'));
		$classCalcSubAccountTitle = $this->_getClassCalc(array('flagType' => 'SubAccountTitle'));
		$classCalcConsumptionTax = $this->_getClassCalc(array('flagType' => 'ConsumptionTax'));
		$classCalcLogCalc = $this->_getClassCalc(array('flagType' => 'LogCalc'));
		$classCalcTempNextLog = $this->_getClassCalc(array(
			'flagType'   => 'TempNext',
			'flagDetail' => 'Log',
		));

		$flag = $classCalcAccountTitle->allot(array(
			'flagStatus'      => $arr['flagStatus'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'arrRows'         => $arrRows,
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}

		$flag = $classCalcSubAccountTitle->allot(array(
			'flagStatus'      => $arr['flagStatus'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'arrRows'         => $arrRows,
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}

		$flag = $classCalcConsumptionTax->allot(array(
			'flagStatus'      => $arr['flagStatus'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'arrRows'         => $arrRows,
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}

		$classCalcLogCalc->allot(array(
			'flagStatus'      => $arr['flagStatus'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'arrRows'         => $arrRows,
		));

		if ($arr['flagTempPrev']) {
			$flag = $classCalcTempNextLog->allot(array(
				'flagStatus'      => $arr['flagStatus'],
				'numFiscalPeriod' => $arr['numFiscalPeriodTempNext'],
				'arrRows'         => $arrRows,
			));
			if ($flag == 'errorDataMax') {
				return $flag;
			}
		}
	}

	/**
		(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'varsValue'       => $arr['varsValue'],
		))
	 */
	protected function _updateDbVarsEntityNation($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingEntity' . $strNation,
			'arrColumn' => $arr['varsValue']['arrColumn'],
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
			'arrValue'  => $arr['varsValue']['arrValue'],
		));
	}

	/**
		(array(
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'arrVarsLog'       => $arrVarsLogAll,
			'idEntity'         => $arr['idEntity'],
			'varsEntityNation' => $varsItem['varsEntityNationUpdate'],
		));
	 */
	protected function _updateDbArrVarsLog($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$classCalcLog = $this->_getClassCalc(array('flagType' => 'Log'));

		$flagMax = 0;
		$arrayIdLog = array();

		$array = &$arr['arrVarsLog'];
		foreach ($array as $key => $value) {

			$jsonVersion = json_encode($value['jsonVersion']);

			$dataVarsion = end($value['jsonVersion']);
			$tempValue = array(
				'numFiscalPeriod'         => $arr['numFiscalPeriod'],
				'idEntity'                => $arr['idEntity'],
				'idAccount'               => '',
				'flagFiscalReport'        => '',
				'stampBook'               => '',
				'strTitle'                => '',
				'jsonDetail'              => $dataVarsion['jsonDetail'],
				'arrCommaIdLogFile'       => '',
				'arrCommaIdAccountPermit' => '',
				'numSumMax'               => '',
				'arrSpaceStrTag'          => '',
			);

			$varsVersion = $classCalcLog->allot(array(
				'flagStatus'       => 'varsVersion',
				'arrValue'         => $tempValue,
				'numFiscalPeriod'  => $arr['numFiscalPeriod'],
				'varsEntityNation' => $arr['varsEntityNation'],
			));

			$arrCommaConsumptionTaxDebit = $varsVersion['arrCommaConsumptionTaxDebit'];
			$arrCommaRateConsumptionTaxDebit = $varsVersion['arrCommaRateConsumptionTaxDebit'];
			$arrCommaConsumptionTaxWithoutCalcDebit = $varsVersion['arrCommaConsumptionTaxWithoutCalcDebit'];
			$arrCommaTaxPaymentDebit = $varsVersion['arrCommaTaxPaymentDebit'];
			$arrCommaTaxReceiptDebit = $varsVersion['arrCommaTaxReceiptDebit'];

			$arrCommaConsumptionTaxCredit = $varsVersion['arrCommaConsumptionTaxCredit'];
			$arrCommaRateConsumptionTaxCredit = $varsVersion['arrCommaRateConsumptionTaxCredit'];
			$arrCommaConsumptionTaxWithoutCalcCredit = $varsVersion['arrCommaConsumptionTaxWithoutCalcCredit'];
			$arrCommaTaxPaymentCredit = $varsVersion['arrCommaTaxPaymentCredit'];
			$arrCommaTaxReceiptCredit = $varsVersion['arrCommaTaxReceiptCredit'];

			$jsonVersion = json_encode($value['jsonVersion']);
			$stampUpdate = TIMESTAMP;

			$flag = $this->checkTextSize(array(
				'flag'        => 'errorDataMax',
				'str'         => $jsonVersion,
				'flagReturn'  => 1,
			));

			if ($flag) {
				$flagMax = 1;
				$arrayIdLog[] = $value['idLog'];
			}

			$arrayTemp = compact(
				'stampUpdate',
				'jsonVersion',
				'arrCommaConsumptionTaxDebit',
				'arrCommaRateConsumptionTaxDebit',
				'arrCommaConsumptionTaxWithoutCalcDebit',
				'arrCommaTaxPaymentDebit',
				'arrCommaTaxReceiptDebit',
				'arrCommaConsumptionTaxCredit',
				'arrCommaRateConsumptionTaxCredit',
				'arrCommaConsumptionTaxWithoutCalcCredit',
				'arrCommaTaxPaymentCredit',
				'arrCommaTaxReceiptCredit'
			);

			$arrColumn = array();
			$arrValue = array();
			foreach ($arrayTemp as $keyTemp => $valueTemp) {
				$arrColumn[] = $keyTemp;
				$arrValue[] = $valueTemp;
			}

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingLog',
				'arrColumn' => $arrColumn,
				'flagAnd'  => 1,
				'arrWhere'  => array(
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
						'flagType'      => 'num',
						'strColumn'     => 'idLog',
						'flagCondition' => 'eq',
						'value'         => $value['idLog'],
					),
				),
				'arrValue'  => $arrValue,
			));
		}

		if ($flagMax) {
			$data = array(
				'flag'      => 'textMaxOver',
				'arrIdLog'  => $arrayIdLog,
			);
			return $data;
		}
	}

	/**
		(array(
			'arrVarsLog' => $arrVarsLog,
			'varsItem'   => $varsItem,
		));
	 */
	protected function _updateArrVarsLog($arr)
	{
		$array = &$arr['arrVarsLog'];
		foreach ($array as $key => $value) {
			$varsLastVersion = end($value['jsonVersion']);

			$numVersionLast = count($value['jsonVersion']) - 1;

			$numVersionConsumptionTax = (int) $varsLastVersion['jsonDetail']['numVersionConsumptionTax'];

			$varsVersionTmpl = $value['jsonVersion'][$numVersionConsumptionTax];

			$varsVersionTmpl['jsonDetail'] = $this->_updateArrVarsLogDetail(array(
				'vars'      => $varsVersionTmpl['jsonDetail'],
				'stampBook' => $varsVersionTmpl['stampBook'],
				'varsItem'  => $arr['varsItem'],
			));

			$varsVersionTmpl['stampUpdate'] = TIMESTAMP;
			$varsVersionTmpl['jsonDetail']['numVersionConsumptionTax'] = $numVersionConsumptionTax;

			$numNet = $numVersionLast - $numVersionConsumptionTax;
			if ($numNet == 1) {
				$value['jsonVersion'][$numVersionLast] = $varsVersionTmpl;

			} else {
				$varsVersionTmpl['stampRegister'] = TIMESTAMP;
				$value['jsonVersion'][] = $varsVersionTmpl;
			}

			$array[$key] = $value;
		}

		return $array;
	}

	/**
	 (array(
		 'arrVarsLog' => $arrVarsLog,
		 'varsItem'   => $varsItem,
	 ));
	 */
	protected function _checkConsumptionTaxType($arr)
	{
		//免税
		if ((int) $arr['flagConsumptionTaxFree']) {
			//＜1:免税＞
			return 1;
			//課税
		} else {
			//本則
			if ((int) $arr['flagConsumptionTaxGeneralRule']) {
				//個別
				if ((int) $arr['flagConsumptionTaxDeducted']) {
					//税込
					if ((int) $arr['flagConsumptionTaxIncluding']) {
						//＜2:課税・本則・個別・税込＞
						return 2;
						//税抜
					} else {
						//内税
						if ((int) $arr['flagConsumptionTaxWithoutCalc'] == 1) {
							//＜3:課税・本則・個別・税抜・内税＞
							return 3;
						//外税
						} elseif ((int) $arr['flagConsumptionTaxWithoutCalc'] == 2) {
							//＜4:課税・本則・個別・税抜・外税＞
							return 4;
						//別記
						} elseif ((int) $arr['flagConsumptionTaxWithoutCalc'] == 3) {
							//＜5:課税・本則・個別・税抜・別記＞
							return 5;
						//理由：判別不要だから
						} elseif ((int) $arr['flagConsumptionTaxWithoutCalc'] == 0) {
							return 3;
						}
					}
					//比例
				} else {
					//税込
					if ((int) $arr['flagConsumptionTaxIncluding']) {
						//＜6:課税・本則・比例・税込＞
						return 6;
						//税抜
					} else {
						//内税
						if ((int) $arr['flagConsumptionTaxWithoutCalc'] == 1) {
							//＜7:課税・本則・比例・税抜・内税＞
							return 7;
						//外税
						} elseif ((int) $arr['flagConsumptionTaxWithoutCalc'] == 2) {
							//＜8:課税・本則・比例・税抜・外税＞
							return 8;
						//別記
						} elseif ((int) $arr['flagConsumptionTaxWithoutCalc'] == 3) {
							//＜9:課税・本則・比例・税抜・別記＞
							return 9;
						//理由：判別不要だから
						} elseif ((int) $arr['flagConsumptionTaxWithoutCalc'] == 0) {
							return 7;
						}
					}
				}
				//簡易
			} else {
				//税込
				if ((int) $arr['flagConsumptionTaxIncluding']) {
					//＜10:課税・簡易・税込＞
					return 10;
					//税抜
				} else {
					//内税
					if ((int) $arr['flagConsumptionTaxWithoutCalc'] == 1) {
						//＜11:課税・簡易・税抜・内税＞
						return 11;
						//外税
					} elseif ((int) $arr['flagConsumptionTaxWithoutCalc'] == 2) {
						//＜12:課税・簡易・税抜・外税＞
						return 12;
					//別記
					} elseif ((int) $arr['flagConsumptionTaxWithoutCalc'] == 3) {
						//＜13:課税・簡易・税抜・別記＞
						return 13;
					//理由：判別不要だから
					} elseif ((int) $arr['flagConsumptionTaxWithoutCalc'] == 0) {
						return 11;
					}
				}
			}
		}
	}

	/**
	 (array(
		'vars'      => $varsVersionTmpl['jsonDetail'],
		'stampBook' => $varsVersionTmpl['stampBook'],
		'varsItem'  => $arr['varsItem'],
	 ))
	 */
	protected function _updateArrVarsLogDetail($arr)
	{
		$varsEntityNationUpdate = $arr['varsItem']['varsEntityNationUpdate'];

		$arraySide = array(
			'arrDebit' => array(),
			'arrCredit' => array(),
		);

		$array = &$arr['vars']['varsDetail'];
		$arrayStr = array('arrDebit', 'arrCredit');
		foreach ($array as $key => $value) {
			foreach ($arrayStr as $keyStr => $valueStr) {
				$strSide = $valueStr;
				$idAccountTitle = $value[$strSide]['idAccountTitle'];

				if ($idAccountTitle) {
					$arraySide[$strSide][] = $idAccountTitle;
					//$array[$key][$strSide]['flagConsumptionTaxCalc'] = $varsEntityNationUpdate['flagConsumptionTaxCalc'];
					//$array[$key][$strSide]['flagConsumptionTaxWithoutCalc'] = $varsEntityNationUpdate['flagConsumptionTaxWithoutCalc'];
					$varsEntityNationStart = $arr['vars']['varsEntityNation'];

					$flagStart = $this->_checkConsumptionTaxType(array(
						'flagConsumptionTaxFree'        => $varsEntityNationStart['flagConsumptionTaxFree'],
						'flagConsumptionTaxGeneralRule' => $varsEntityNationStart['flagConsumptionTaxGeneralRule'],
						'flagConsumptionTaxDeducted'    => $varsEntityNationStart['flagConsumptionTaxDeducted'],
						'flagConsumptionTaxIncluding'   => $varsEntityNationStart['flagConsumptionTaxIncluding'],
						'flagConsumptionTaxWithoutCalc' => $value[$strSide]['flagConsumptionTaxWithoutCalc'],
					));

					$flagUpdate = $this->_checkConsumptionTaxType(array(
						'flagConsumptionTaxFree'        => $varsEntityNationUpdate['flagConsumptionTaxFree'],
						'flagConsumptionTaxGeneralRule' => $varsEntityNationUpdate['flagConsumptionTaxGeneralRule'],
						'flagConsumptionTaxDeducted'    => $varsEntityNationUpdate['flagConsumptionTaxDeducted'],
						'flagConsumptionTaxIncluding'   => $varsEntityNationUpdate['flagConsumptionTaxIncluding'],
						'flagConsumptionTaxWithoutCalc' => 0,
					));

					$method = '_updateArrVarsLogDetail' . $flagStart;
					$array[$key][$strSide] = $this->$method(array(
						'vars'                   => &$array[$key][$strSide],
						'varsEntityNationStart'  => $varsEntityNationStart,
						'flagUpdate'             => $flagUpdate,
						'varsEntityNationUpdate' => $varsEntityNationUpdate,
						'varsItem'               => $arr['varsItem'],
						'stampBook'              => $arr['stampBook'],
					));
				}

				$flagConsumptionTaxWithoutCalc = ($value[$strSide]['flagConsumptionTaxWithoutCalc'] != '')? $value[$strSide]['flagConsumptionTaxWithoutCalc'] : '';
				if ($idAccountTitle == 'suspenseReceiptOfConsumptionTaxes' || $idAccountTitle == 'suspensePaymentConsumptionTaxes') {
					$flagConsumptionTaxWithoutCalc = 3;
				}

				if (!(int) $varsEntityNationUpdate['flagConsumptionTaxFree']
					&& !(int) $varsEntityNationUpdate['flagConsumptionTaxIncluding']
					&& $idAccountTitle != 'suspenseReceiptOfConsumptionTaxes'
					&& $idAccountTitle != 'suspensePaymentConsumptionTaxes'
				) {
					if ((int) $varsEntityNationUpdate['flagConsumptionTaxGeneralRule']) {
						if ((int) $varsEntityNationUpdate['flagConsumptionTaxDeducted']) {
							if (preg_match("/^tax/", $array[$key][$strSide]['flagConsumptionTaxGeneralRuleEach'])) {
								$flagTax = 1;
							}

						} else {
							if (preg_match("/^tax/", $array[$key][$strSide]['flagConsumptionTaxGeneralRuleProration'])) {
								$flagTax = 1;
							}
						}
					}
					if (preg_match("/^tax/", $array[$key][$strSide]['flagConsumptionTaxSimpleRule'])
						&& !(int) $varsEntityNationUpdate['flagConsumptionTaxGeneralRule']
					) {
						$flagTax = 1;
					}
				}

				if ($flagTax) {
					if ($flagConsumptionTaxWithoutCalc == 1 || $flagConsumptionTaxWithoutCalc == 2) {
						$arraySide[$strSide][] = 'dummy';
					}
				}
			}
		}

		$arr['vars']['idAccountTitleDebit'] = (count($arraySide['arrDebit']) == 1)? $arraySide['arrDebit'][0] : 'else';
		$arr['vars']['idAccountTitleCredit'] = (count($arraySide['arrCredit']) == 1)? $arraySide['arrCredit'][0] : 'else';

		$arr['vars']['varsEntityNation'] = array(
			'flagConsumptionTaxFree'         => $varsEntityNationUpdate['flagConsumptionTaxFree'],
			'flagConsumptionTaxGeneralRule'  => $varsEntityNationUpdate['flagConsumptionTaxGeneralRule'],
			'flagConsumptionTaxDeducted'     => $varsEntityNationUpdate['flagConsumptionTaxDeducted'],
			'flagConsumptionTaxIncluding'    => $varsEntityNationUpdate['flagConsumptionTaxIncluding'],
		);

		return $arr['vars'];
	}

	/**
	 * ＜start1:免税＞
		(array(
			'vars'                   => &$array[$key][$strSide],
			'varsEntityNationStart'  => $varsEntityNationStart,
			'varsEntityNationUpdate' => $varsEntityNationUpdate,
			'varsItem'               => $arr['varsItem'],
		))
	 */
	protected function _updateArrVarsLogDetail1($arr)
	{
		$arrStrTitle = $arr['varsItem']['arrAccountTitle']['arrStrTitle'];
		$idAccountTitle = $arr['vars']['idAccountTitle'];

		//＜update1:免税＞
		if (preg_match("/^(1)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = 1;
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = '';
			$arr['vars']['numValueConsumptionTax'] = '';
			/*
			 * 20191001 start
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = '';
			/*
			 * 20191001 end
			 */


		//＜update2:課税・本則・個別・税込＞
		} elseif (preg_match("/^(2)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleEach = $arrStrTitle[$idAccountTitle]['flagConsumptionTaxGeneralRuleEach'];
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = $flagConsumptionTaxGeneralRuleEach;
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update3:課税・本則・個別・税抜・内税＞
		//＜update4:課税・本則・個別・税抜・外税＞
		//＜update5:課税・本則・個別・税抜・別記＞
		} elseif (preg_match("/^(3|4|5)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleEach = $arrStrTitle[$idAccountTitle]['flagConsumptionTaxGeneralRuleEach'];
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = $flagConsumptionTaxGeneralRuleEach;
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$numValueConsumptionTax = $this->_getCalcConsumptionTax(array(
				'varsEntityNation' => $arr['varsEntityNationUpdate'],
				'vars'             => $arr['vars'],
			));
			$arr['vars']['numValueConsumptionTax'] = $numValueConsumptionTax;

		//＜update6:課税・本則・比例・税込＞
		} elseif (preg_match("/^(6)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleProration = $arrStrTitle[$idAccountTitle]['flagConsumptionTaxGeneralRuleProration'];
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = $flagConsumptionTaxGeneralRuleProration;
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update7:課税・本則・比例・税抜・内税＞
		//＜update8:課税・本則・比例・税抜・外税＞
		//＜update9:課税・本則・比例・税抜・別記＞
		} elseif (preg_match("/^(7|8|9)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleProration = $arrStrTitle[$idAccountTitle]['flagConsumptionTaxGeneralRuleProration'];
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = $flagConsumptionTaxGeneralRuleProration;
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$numValueConsumptionTax = $this->_getCalcConsumptionTax(array(
				'varsEntityNation' => $arr['varsEntityNationUpdate'],
				'vars'             => $arr['vars'],
			));
			$arr['vars']['numValueConsumptionTax'] = $numValueConsumptionTax;

		//＜update10:課税・簡易・税込＞
		} elseif (preg_match("/^(10)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxSimpleRule = $this->_getDefaultConsumptionTax(array(
				'varsEntityNation' => $arr['varsEntityNationUpdate'],
				'flagTax'          => $arrStrTitle[$idAccountTitle]['flagConsumptionTaxSimpleRule'],
			));
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = $flagConsumptionTaxSimpleRule;
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update11:課税・簡易・税抜・内税＞
		//＜update12:課税・簡易・税抜・外税＞
		//＜update13:課税・簡易・税抜・別記＞
		} elseif (preg_match("/^(11|12|13)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxSimpleRule = $this->_getDefaultConsumptionTax(array(
				'varsEntityNation' => $arr['varsEntityNationUpdate'],
				'flagTax'          => $arrStrTitle[$idAccountTitle]['flagConsumptionTaxSimpleRule'],
			));
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = $flagConsumptionTaxSimpleRule;
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$numValueConsumptionTax = $this->_getCalcConsumptionTax(array(
				'varsEntityNation' => $arr['varsEntityNationUpdate'],
				'vars'             => $arr['vars'],
			));
			$arr['vars']['numValueConsumptionTax'] = $numValueConsumptionTax;
		}

		return $arr['vars'];
	}

	/**
	 * ＜start2:課税・本則・個別・税込＞
	 */
	protected function _updateArrVarsLogDetail2($arr)
	{
		$arrStrTitle = $arr['varsItem']['arrAccountTitle']['arrStrTitle'];
		$idAccountTitle = $arr['vars']['idAccountTitle'];

		//＜update1:免税＞
		if (preg_match("/^(1)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = 1;
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = '';
			$arr['vars']['flagRateConsumptionTaxReduced'] = '';
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update2:課税・本則・個別・税込＞
		} elseif (preg_match("/^(2)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			//$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update3:課税・本則・個別・税抜・内税＞
		//＜update4:課税・本則・個別・税抜・外税＞
		//＜update5:課税・本則・個別・税抜・別記＞
		} elseif (preg_match("/^(3|4|5)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			//$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$numValueConsumptionTax = $this->_getCalcConsumptionTax(array(
				'varsEntityNation' => $arr['varsEntityNationUpdate'],
				'vars'             => $arr['vars'],
			));
			$arr['vars']['numValueConsumptionTax'] = $numValueConsumptionTax;

		//＜update6:課税・本則・比例・税込＞
		} elseif (preg_match("/^(6)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleEach = $arr['vars']['flagConsumptionTaxGeneralRuleEach'];
			$flagConsumptionTaxGeneralRuleProration = $flagConsumptionTaxGeneralRuleEach;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalEachChange'][$flagConsumptionTaxGeneralRuleEach];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleProration = $flagVars['generalProration'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = $flagConsumptionTaxGeneralRuleProration;
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update7:課税・本則・比例・税抜・内税＞
		//＜update8:課税・本則・比例・税抜・外税＞
		//＜update9:課税・本則・比例・税抜・別記＞
		} elseif (preg_match("/^(7|8|9)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleEach = $arr['vars']['flagConsumptionTaxGeneralRuleEach'];
			$flagConsumptionTaxGeneralRuleProration = $flagConsumptionTaxGeneralRuleEach;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalEachChange'][$flagConsumptionTaxGeneralRuleEach];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleProration = $flagVars['generalProration'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = $flagConsumptionTaxGeneralRuleProration;
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$numValueConsumptionTax = $this->_getCalcConsumptionTax(array(
				'varsEntityNation' => $arr['varsEntityNationUpdate'],
				'vars'             => $arr['vars'],
			));
			$arr['vars']['numValueConsumptionTax'] = $numValueConsumptionTax;

		//＜update10:課税・簡易・税込＞
		} elseif (preg_match("/^(10)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleEach = $arr['vars']['flagConsumptionTaxGeneralRuleEach'];
			$flagConsumptionTaxSimpleRule = $flagConsumptionTaxGeneralRuleEach;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalEachChange'][$flagConsumptionTaxGeneralRuleEach];
			if ($flagVars) {
				$flagConsumptionTaxSimpleRule = $this->_getUnknownConsumptionTax(array(
					'varsEntityNation' => $arr['varsEntityNationUpdate'],
					'flagTax'          => $flagVars['simple'],
				));
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = $flagConsumptionTaxSimpleRule;
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update11:課税・簡易・税抜・内税＞
		//＜update12:課税・簡易・税抜・外税＞
		//＜update13:課税・簡易・税抜・別記＞
		} elseif (preg_match("/^(11|12|13)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleEach = $arr['vars']['flagConsumptionTaxGeneralRuleEach'];
			$flagConsumptionTaxSimpleRule = $flagConsumptionTaxGeneralRuleEach;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalEachChange'][$flagConsumptionTaxGeneralRuleEach];
			if ($flagVars) {
				$flagConsumptionTaxSimpleRule = $this->_getUnknownConsumptionTax(array(
					'varsEntityNation' => $arr['varsEntityNationUpdate'],
					'flagTax'          => $flagVars['simple'],
				));
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = $flagConsumptionTaxSimpleRule;
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$numValueConsumptionTax = $this->_getCalcConsumptionTax(array(
				'varsEntityNation' => $arr['varsEntityNationUpdate'],
				'vars'             => $arr['vars'],
			));
			$arr['vars']['numValueConsumptionTax'] = $numValueConsumptionTax;

		}

		return $arr['vars'];
	}

	/**
	 * ＜start3:課税・本則・個別・税抜・内税＞
	 */
	protected function _updateArrVarsLogDetail3($arr)
	{
		$arrStrTitle = $arr['varsItem']['arrAccountTitle']['arrStrTitle'];
		$idAccountTitle = $arr['vars']['idAccountTitle'];

		//＜update1:免税＞
		if (preg_match("/^(1)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = 1;
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = '';
			$arr['vars']['flagRateConsumptionTaxReduced'] = '';
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update2:課税・本則・個別・税込＞
		} elseif (preg_match("/^(2)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			//$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update3:課税・本則・個別・税抜・内税＞
		//＜update4:課税・本則・個別・税抜・外税＞
		//＜update5:課税・本則・個別・税抜・別記＞
		} elseif (preg_match("/^(3|4|5)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			//$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			//$arr['vars']['numValueConsumptionTax'] = '';

		//＜update6:課税・本則・比例・税込＞
		} elseif (preg_match("/^(6)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleEach = $arr['vars']['flagConsumptionTaxGeneralRuleEach'];
			$flagConsumptionTaxGeneralRuleProration = $flagConsumptionTaxGeneralRuleEach;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalEachChange'][$flagConsumptionTaxGeneralRuleEach];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleProration = $flagVars['generalProration'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = $flagConsumptionTaxGeneralRuleProration;
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update7:課税・本則・比例・税抜・内税＞
		//＜update8:課税・本則・比例・税抜・外税＞
		//＜update9:課税・本則・比例・税抜・別記＞
		} elseif (preg_match("/^(7|8|9)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleEach = $arr['vars']['flagConsumptionTaxGeneralRuleEach'];
			$flagConsumptionTaxGeneralRuleProration = $flagConsumptionTaxGeneralRuleEach;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalEachChange'][$flagConsumptionTaxGeneralRuleEach];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleProration = $flagVars['generalProration'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = $flagConsumptionTaxGeneralRuleProration;
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			//$arr['vars']['numValueConsumptionTax'] = '';

		//＜update10:課税・簡易・税込＞
		} elseif (preg_match("/^(10)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleEach = $arr['vars']['flagConsumptionTaxGeneralRuleEach'];
			$flagConsumptionTaxSimpleRule = $flagConsumptionTaxGeneralRuleEach;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalEachChange'][$flagConsumptionTaxGeneralRuleEach];
			if ($flagVars) {
				$flagConsumptionTaxSimpleRule = $this->_getUnknownConsumptionTax(array(
					'varsEntityNation' => $arr['varsEntityNationUpdate'],
					'flagTax'          => $flagVars['simple'],
				));
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = $flagConsumptionTaxSimpleRule;
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update11:課税・簡易・税抜・内税＞
		//＜update12:課税・簡易・税抜・外税＞
		//＜update13:課税・簡易・税抜・別記＞
		} elseif (preg_match("/^(11|12|13)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleEach = $arr['vars']['flagConsumptionTaxGeneralRuleEach'];
			$flagConsumptionTaxSimpleRule = $flagConsumptionTaxGeneralRuleEach;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalEachChange'][$flagConsumptionTaxGeneralRuleEach];
			if ($flagVars) {
				$flagConsumptionTaxSimpleRule = $this->_getUnknownConsumptionTax(array(
					'varsEntityNation' => $arr['varsEntityNationUpdate'],
					'flagTax'          => $flagVars['simple'],
				));
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = $flagConsumptionTaxSimpleRule;
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			//$arr['vars']['numValueConsumptionTax'] = '';

		}

		return $arr['vars'];
	}

	/**
	 * ＜start4:課税・本則・個別・税抜・外税＞
	 */
	protected function _updateArrVarsLogDetail4($arr)
	{
		$arrStrTitle = $arr['varsItem']['arrAccountTitle']['arrStrTitle'];
		$idAccountTitle = $arr['vars']['idAccountTitle'];

		//＜update1:免税＞
		if (preg_match("/^(1)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = 1;
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = '';
			$arr['vars']['flagRateConsumptionTaxReduced'] = '';
			$arr['vars']['numValue'] += $arr['vars']['numValueConsumptionTax'];
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update2:課税・本則・個別・税込＞
		} elseif (preg_match("/^(2)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			//$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValue'] += $arr['vars']['numValueConsumptionTax'];
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update3:課税・本則・個別・税抜・内税＞
		//＜update4:課税・本則・個別・税抜・外税＞
		//＜update5:課税・本則・個別・税抜・別記＞
		} elseif (preg_match("/^(3|4|5)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			//$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			//$arr['vars']['flagConsumptionTaxWithoutCalc'] = 2;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			//$arr['vars']['numValueConsumptionTax'] = '';

		//＜update6:課税・本則・比例・税込＞
		} elseif (preg_match("/^(6)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleEach = $arr['vars']['flagConsumptionTaxGeneralRuleEach'];
			$flagConsumptionTaxGeneralRuleProration = $flagConsumptionTaxGeneralRuleEach;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalEachChange'][$flagConsumptionTaxGeneralRuleEach];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleProration = $flagVars['generalProration'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = $flagConsumptionTaxGeneralRuleProration;
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValue'] += $arr['vars']['numValueConsumptionTax'];
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update7:課税・本則・比例・税抜・内税＞
		//＜update8:課税・本則・比例・税抜・外税＞
		//＜update9:課税・本則・比例・税抜・別記＞
		} elseif (preg_match("/^(7|8|9)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleEach = $arr['vars']['flagConsumptionTaxGeneralRuleEach'];
			$flagConsumptionTaxGeneralRuleProration = $flagConsumptionTaxGeneralRuleEach;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalEachChange'][$flagConsumptionTaxGeneralRuleEach];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleProration = $flagVars['generalProration'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = $flagConsumptionTaxGeneralRuleProration;
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			//$arr['vars']['flagConsumptionTaxWithoutCalc'] = 2;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			//$arr['vars']['numValueConsumptionTax'] = '';

		//＜update10:課税・簡易・税込＞
		} elseif (preg_match("/^(10)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleEach = $arr['vars']['flagConsumptionTaxGeneralRuleEach'];
			$flagConsumptionTaxSimpleRule = $flagConsumptionTaxGeneralRuleEach;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalEachChange'][$flagConsumptionTaxGeneralRuleEach];
			if ($flagVars) {
				$flagConsumptionTaxSimpleRule = $this->_getUnknownConsumptionTax(array(
					'varsEntityNation' => $arr['varsEntityNationUpdate'],
					'flagTax'          => $flagVars['simple'],
				));
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = $flagConsumptionTaxSimpleRule;
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValue'] += $arr['vars']['numValueConsumptionTax'];
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update11:課税・簡易・税抜・内税＞
		//＜update12:課税・簡易・税抜・外税＞
		//＜update13:課税・簡易・税抜・別記＞
		} elseif (preg_match("/^(11|12|13)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleEach = $arr['vars']['flagConsumptionTaxGeneralRuleEach'];
			$flagConsumptionTaxSimpleRule = $flagConsumptionTaxGeneralRuleEach;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalEachChange'][$flagConsumptionTaxGeneralRuleEach];
			if ($flagVars) {
				$flagConsumptionTaxSimpleRule = $this->_getUnknownConsumptionTax(array(
					'varsEntityNation' => $arr['varsEntityNationUpdate'],
					'flagTax'          => $flagVars['simple'],
				));
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = $flagConsumptionTaxSimpleRule;
			//$arr['vars']['flagConsumptionTaxWithoutCalc'] = 2;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			//$arr['vars']['numValueConsumptionTax'] = '';

		}

		return $arr['vars'];
	}

	/**
	 * ＜start5:課税・本則・個別・税抜・別記＞
	 */
	protected function _updateArrVarsLogDetail5($arr)
	{
		$arrStrTitle = $arr['varsItem']['arrAccountTitle']['arrStrTitle'];
		$idAccountTitle = $arr['vars']['idAccountTitle'];

		//＜update1:免税＞
		if (preg_match("/^(1)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = 1;
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = '';
			$arr['vars']['flagRateConsumptionTaxReduced'] = '';
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update2:課税・本則・個別・税込＞
		} elseif (preg_match("/^(2)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			//$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update3:課税・本則・個別・税抜・内税＞
		//＜update4:課税・本則・個別・税抜・外税＞
		//＜update5:課税・本則・個別・税抜・別記＞
		} elseif (preg_match("/^(3|4|5)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			//$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			//$arr['vars']['flagConsumptionTaxWithoutCalc'] = 2;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			//$arr['vars']['numValueConsumptionTax'] = '';

		//＜update6:課税・本則・比例・税込＞
		} elseif (preg_match("/^(6)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleEach = $arr['vars']['flagConsumptionTaxGeneralRuleEach'];
			$flagConsumptionTaxGeneralRuleProration = $flagConsumptionTaxGeneralRuleEach;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalEachChange'][$flagConsumptionTaxGeneralRuleEach];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleProration = $flagVars['generalProration'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = $flagConsumptionTaxGeneralRuleProration;
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update7:課税・本則・比例・税抜・内税＞
		//＜update8:課税・本則・比例・税抜・外税＞
		//＜update9:課税・本則・比例・税抜・別記＞
		} elseif (preg_match("/^(7|8|9)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleEach = $arr['vars']['flagConsumptionTaxGeneralRuleEach'];
			$flagConsumptionTaxGeneralRuleProration = $flagConsumptionTaxGeneralRuleEach;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalEachChange'][$flagConsumptionTaxGeneralRuleEach];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleProration = $flagVars['generalProration'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = $flagConsumptionTaxGeneralRuleProration;
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			//$arr['vars']['flagConsumptionTaxWithoutCalc'] = 3;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update10:課税・簡易・税込＞
		} elseif (preg_match("/^(10)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleEach = $arr['vars']['flagConsumptionTaxGeneralRuleEach'];
			$flagConsumptionTaxSimpleRule = $flagConsumptionTaxGeneralRuleEach;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalEachChange'][$flagConsumptionTaxGeneralRuleEach];
			if ($flagVars) {
				$flagConsumptionTaxSimpleRule = $this->_getUnknownConsumptionTax(array(
					'varsEntityNation' => $arr['varsEntityNationUpdate'],
					'flagTax'          => $flagVars['simple'],
				));
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = $flagConsumptionTaxSimpleRule;
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update11:課税・簡易・税抜・内税＞
		//＜update12:課税・簡易・税抜・外税＞
		//＜update13:課税・簡易・税抜・別記＞
		} elseif (preg_match("/^(11|12|13)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleEach = $arr['vars']['flagConsumptionTaxGeneralRuleEach'];
			$flagConsumptionTaxSimpleRule = $flagConsumptionTaxGeneralRuleEach;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalEachChange'][$flagConsumptionTaxGeneralRuleEach];
			if ($flagVars) {
				$flagConsumptionTaxSimpleRule = $this->_getUnknownConsumptionTax(array(
					'varsEntityNation' => $arr['varsEntityNationUpdate'],
					'flagTax'          => $flagVars['simple'],
				));
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = $flagConsumptionTaxSimpleRule;
			//$arr['vars']['flagConsumptionTaxWithoutCalc'] = 3;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		}

		return $arr['vars'];
	}

	/**
	 * ＜start6:課税・本則・比例・税込＞
	 */
	protected function _updateArrVarsLogDetail6($arr)
	{
		$arrStrTitle = $arr['varsItem']['arrAccountTitle']['arrStrTitle'];
		$idAccountTitle = $arr['vars']['idAccountTitle'];

		//＜update1:免税＞
		if (preg_match("/^(1)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = 1;
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = '';
			$arr['vars']['flagRateConsumptionTaxReduced'] = '';
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update2:課税・本則・個別・税込＞
		} elseif (preg_match("/^(2)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleProration = $arr['vars']['flagConsumptionTaxGeneralRuleProration'];
			$flagConsumptionTaxGeneralRuleEach = $flagConsumptionTaxGeneralRuleProration;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalProrationChange'][$flagConsumptionTaxGeneralRuleProration];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleEach = $flagVars['generalEach'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = $flagConsumptionTaxGeneralRuleEach;
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update3:課税・本則・個別・税抜・内税＞
		//＜update4:課税・本則・個別・税抜・外税＞
		//＜update5:課税・本則・個別・税抜・別記＞
		} elseif (preg_match("/^(3|4|5)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleProration = $arr['vars']['flagConsumptionTaxGeneralRuleProration'];
			$flagConsumptionTaxGeneralRuleEach = $flagConsumptionTaxGeneralRuleProration;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalProrationChange'][$flagConsumptionTaxGeneralRuleProration];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleEach = $flagVars['generalEach'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = $flagConsumptionTaxGeneralRuleEach;
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$numValueConsumptionTax = $this->_getCalcConsumptionTax(array(
				'varsEntityNation' => $arr['varsEntityNationUpdate'],
				'vars'             => $arr['vars'],
			));
			$arr['vars']['numValueConsumptionTax'] = $numValueConsumptionTax;

		//＜update6:課税・本則・比例・税込＞
		} elseif (preg_match("/^(6)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			//$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update7:課税・本則・比例・税抜・内税＞
		//＜update8:課税・本則・比例・税抜・外税＞
		//＜update9:課税・本則・比例・税抜・別記＞
		} elseif (preg_match("/^(7|8|9)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			//$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$numValueConsumptionTax = $this->_getCalcConsumptionTax(array(
				'varsEntityNation' => $arr['varsEntityNationUpdate'],
				'vars'             => $arr['vars'],
			));
			$arr['vars']['numValueConsumptionTax'] = $numValueConsumptionTax;



		//＜update10:課税・簡易・税込＞
		} elseif (preg_match("/^(10)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleProration = $arr['vars']['flagConsumptionTaxGeneralRuleProration'];
			$flagConsumptionTaxSimpleRule = $flagConsumptionTaxGeneralRuleProration;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalProrationChange'][$flagConsumptionTaxGeneralRuleProration];
			if ($flagVars) {
				$flagConsumptionTaxSimpleRule = $this->_getUnknownConsumptionTax(array(
					'varsEntityNation' => $arr['varsEntityNationUpdate'],
					'flagTax'          => $flagVars['simple'],
				));
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = $flagConsumptionTaxSimpleRule;
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update11:課税・簡易・税抜・内税＞
		//＜update12:課税・簡易・税抜・外税＞
		//＜update13:課税・簡易・税抜・別記＞
		} elseif (preg_match("/^(11|12|13)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleProration = $arr['vars']['flagConsumptionTaxGeneralRuleProration'];
			$flagConsumptionTaxSimpleRule = $flagConsumptionTaxGeneralRuleProration;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalProrationChange'][$flagConsumptionTaxGeneralRuleProration];
			if ($flagVars) {
				$flagConsumptionTaxSimpleRule = $this->_getUnknownConsumptionTax(array(
					'varsEntityNation' => $arr['varsEntityNationUpdate'],
					'flagTax'          => $flagVars['simple'],
				));
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = $flagConsumptionTaxSimpleRule;
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$numValueConsumptionTax = $this->_getCalcConsumptionTax(array(
				'varsEntityNation' => $arr['varsEntityNationUpdate'],
				'vars'             => $arr['vars'],
			));
			$arr['vars']['numValueConsumptionTax'] = $numValueConsumptionTax;

		}

		return $arr['vars'];
	}

	/**
	 * ＜start7:課税・本則・比例・税抜・内税＞
	 */
	protected function _updateArrVarsLogDetail7($arr)
	{
		$arrStrTitle = $arr['varsItem']['arrAccountTitle']['arrStrTitle'];
		$idAccountTitle = $arr['vars']['idAccountTitle'];

		//＜update1:免税＞
		if (preg_match("/^(1)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = 1;
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = '';
			$arr['vars']['flagRateConsumptionTaxReduced'] = '';
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update2:課税・本則・個別・税込＞
		} elseif (preg_match("/^(2)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleProration = $arr['vars']['flagConsumptionTaxGeneralRuleProration'];
			$flagConsumptionTaxGeneralRuleEach = $flagConsumptionTaxGeneralRuleProration;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalProrationChange'][$flagConsumptionTaxGeneralRuleProration];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleEach = $flagVars['generalEach'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = $flagConsumptionTaxGeneralRuleEach;
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';


		//＜update3:課税・本則・個別・税抜・内税＞
		//＜update4:課税・本則・個別・税抜・外税＞
		//＜update5:課税・本則・個別・税抜・別記＞
		} elseif (preg_match("/^(3|4|5)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleProration = $arr['vars']['flagConsumptionTaxGeneralRuleProration'];
			$flagConsumptionTaxGeneralRuleEach = $flagConsumptionTaxGeneralRuleProration;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalProrationChange'][$flagConsumptionTaxGeneralRuleProration];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleEach = $flagVars['generalEach'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = $flagConsumptionTaxGeneralRuleEach;
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			//$arr['vars']['numValueConsumptionTax'] = '';


		//＜update6:課税・本則・比例・税込＞
		} elseif (preg_match("/^(6)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			//$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';


		//＜update7:課税・本則・比例・税抜・内税＞
		//＜update8:課税・本則・比例・税抜・外税＞
		//＜update9:課税・本則・比例・税抜・別記＞
		} elseif (preg_match("/^(7|8|9)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			//$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			//$arr['vars']['numValueConsumptionTax'] = '';


		//＜update10:課税・簡易・税込＞
		} elseif (preg_match("/^(10)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleProration = $arr['vars']['flagConsumptionTaxGeneralRuleProration'];
			$flagConsumptionTaxSimpleRule = $flagConsumptionTaxGeneralRuleProration;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalProrationChange'][$flagConsumptionTaxGeneralRuleProration];
			if ($flagVars) {
				$flagConsumptionTaxSimpleRule = $this->_getUnknownConsumptionTax(array(
					'varsEntityNation' => $arr['varsEntityNationUpdate'],
					'flagTax'          => $flagVars['simple'],
				));
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = $flagConsumptionTaxSimpleRule;
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update11:課税・簡易・税抜・内税＞
		//＜update12:課税・簡易・税抜・外税＞
		//＜update13:課税・簡易・税抜・別記＞
		} elseif (preg_match("/^(11|12|13)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleProration = $arr['vars']['flagConsumptionTaxGeneralRuleProration'];
			$flagConsumptionTaxSimpleRule = $flagConsumptionTaxGeneralRuleProration;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalProrationChange'][$flagConsumptionTaxGeneralRuleProration];
			if ($flagVars) {
				$flagConsumptionTaxSimpleRule = $this->_getUnknownConsumptionTax(array(
					'varsEntityNation' => $arr['varsEntityNationUpdate'],
					'flagTax'          => $flagVars['simple'],
				));
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = $flagConsumptionTaxSimpleRule;
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			//$arr['vars']['numValueConsumptionTax'] = '';

		}

		return $arr['vars'];
	}

	/**
	 * ＜start8:課税・本則・比例・税抜・外税＞
	 */
	protected function _updateArrVarsLogDetail8($arr)
	{
		$arrStrTitle = $arr['varsItem']['arrAccountTitle']['arrStrTitle'];
		$idAccountTitle = $arr['vars']['idAccountTitle'];

		//＜update1:免税＞
		if (preg_match("/^(1)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = 1;
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = '';
			$arr['vars']['flagRateConsumptionTaxReduced'] = '';
			$arr['vars']['numValue'] += $arr['vars']['numValueConsumptionTax'];
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update2:課税・本則・個別・税込＞
		} elseif (preg_match("/^(2)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleProration = $arr['vars']['flagConsumptionTaxGeneralRuleProration'];
			$flagConsumptionTaxGeneralRuleEach = $flagConsumptionTaxGeneralRuleProration;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalProrationChange'][$flagConsumptionTaxGeneralRuleProration];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleEach = $flagVars['generalEach'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = $flagConsumptionTaxGeneralRuleEach;
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValue'] += $arr['vars']['numValueConsumptionTax'];
			$arr['vars']['numValueConsumptionTax'] = '';


		//＜update3:課税・本則・個別・税抜・内税＞
		//＜update4:課税・本則・個別・税抜・外税＞
		//＜update5:課税・本則・個別・税抜・別記＞
		} elseif (preg_match("/^(3|4|5)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleProration = $arr['vars']['flagConsumptionTaxGeneralRuleProration'];
			$flagConsumptionTaxGeneralRuleEach = $flagConsumptionTaxGeneralRuleProration;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalProrationChange'][$flagConsumptionTaxGeneralRuleProration];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleEach = $flagVars['generalEach'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = $flagConsumptionTaxGeneralRuleEach;
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			//$arr['vars']['flagConsumptionTaxWithoutCalc'] = 2;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			//$arr['vars']['numValueConsumptionTax'] = '';


		//＜update6:課税・本則・比例・税込＞
		} elseif (preg_match("/^(6)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			//$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValue'] += $arr['vars']['numValueConsumptionTax'];
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update7:課税・本則・比例・税抜・内税＞
		//＜update8:課税・本則・比例・税抜・外税＞
		//＜update9:課税・本則・比例・税抜・別記＞
		} elseif (preg_match("/^(7|8|9)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			//$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			//$arr['vars']['flagConsumptionTaxWithoutCalc'] = 2;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			//$arr['vars']['numValueConsumptionTax'] = '';

		//＜update10:課税・簡易・税込＞
		} elseif (preg_match("/^(10)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleProration = $arr['vars']['flagConsumptionTaxGeneralRuleProration'];
			$flagConsumptionTaxSimpleRule = $flagConsumptionTaxGeneralRuleProration;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalProrationChange'][$flagConsumptionTaxGeneralRuleProration];
			if ($flagVars) {
				$flagConsumptionTaxSimpleRule = $this->_getUnknownConsumptionTax(array(
					'varsEntityNation' => $arr['varsEntityNationUpdate'],
					'flagTax'          => $flagVars['simple'],
				));
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = $flagConsumptionTaxSimpleRule;
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValue'] += $arr['vars']['numValueConsumptionTax'];
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update11:課税・簡易・税抜・内税＞
		//＜update12:課税・簡易・税抜・外税＞
		//＜update13:課税・簡易・税抜・別記＞
		} elseif (preg_match("/^(11|12|13)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleProration = $arr['vars']['flagConsumptionTaxGeneralRuleProration'];
			$flagConsumptionTaxSimpleRule = $flagConsumptionTaxGeneralRuleProration;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalProrationChange'][$flagConsumptionTaxGeneralRuleProration];
			if ($flagVars) {
				$flagConsumptionTaxSimpleRule = $this->_getUnknownConsumptionTax(array(
					'varsEntityNation' => $arr['varsEntityNationUpdate'],
					'flagTax'          => $flagVars['simple'],
				));
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = $flagConsumptionTaxSimpleRule;
			//$arr['vars']['flagConsumptionTaxWithoutCalc'] = 2;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			//$arr['vars']['numValueConsumptionTax'] = '';

		}

		return $arr['vars'];
	}

	/**
	 * ＜start9:課税・本則・比例・税抜・別記＞
	 */
	protected function _updateArrVarsLogDetail9($arr)
	{
		$arrStrTitle = $arr['varsItem']['arrAccountTitle']['arrStrTitle'];
		$idAccountTitle = $arr['vars']['idAccountTitle'];

		//＜update1:免税＞
		if (preg_match("/^(1)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = 1;
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = '';
			$arr['vars']['flagRateConsumptionTaxReduced'] = '';
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update2:課税・本則・個別・税込＞
		} elseif (preg_match("/^(2)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleProration = $arr['vars']['flagConsumptionTaxGeneralRuleProration'];
			$flagConsumptionTaxGeneralRuleEach = $flagConsumptionTaxGeneralRuleProration;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalProrationChange'][$flagConsumptionTaxGeneralRuleProration];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleEach = $flagVars['generalEach'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = $flagConsumptionTaxGeneralRuleEach;
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update3:課税・本則・個別・税抜・内税＞
		//＜update4:課税・本則・個別・税抜・外税＞
		//＜update5:課税・本則・個別・税抜・別記＞
		} elseif (preg_match("/^(3|4|5)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleProration = $arr['vars']['flagConsumptionTaxGeneralRuleProration'];
			$flagConsumptionTaxGeneralRuleEach = $flagConsumptionTaxGeneralRuleProration;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalProrationChange'][$flagConsumptionTaxGeneralRuleProration];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleEach = $flagVars['generalEach'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = $flagConsumptionTaxGeneralRuleEach;
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			//$arr['vars']['flagConsumptionTaxWithoutCalc'] = 3;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update6:課税・本則・比例・税込＞
		} elseif (preg_match("/^(6)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			//$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update7:課税・本則・比例・税抜・内税＞
		//＜update8:課税・本則・比例・税抜・外税＞
		//＜update9:課税・本則・比例・税抜・別記＞
		} elseif (preg_match("/^(7|8|9)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			//$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			//$arr['vars']['flagConsumptionTaxWithoutCalc'] = 2;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			//$arr['vars']['numValueConsumptionTax'] = '';

		//＜update10:課税・簡易・税込＞
		} elseif (preg_match("/^(10)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleProration = $arr['vars']['flagConsumptionTaxGeneralRuleProration'];
			$flagConsumptionTaxSimpleRule = $flagConsumptionTaxGeneralRuleProration;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalProrationChange'][$flagConsumptionTaxGeneralRuleProration];
			if ($flagVars) {
				$flagConsumptionTaxSimpleRule = $this->_getUnknownConsumptionTax(array(
					'varsEntityNation' => $arr['varsEntityNationUpdate'],
					'flagTax'          => $flagVars['simple'],
				));
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = $flagConsumptionTaxSimpleRule;
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update11:課税・簡易・税抜・内税＞
		//＜update12:課税・簡易・税抜・外税＞
		//＜update13:課税・簡易・税抜・別記＞
		} elseif (preg_match("/^(11|12|13)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxGeneralRuleProration = $arr['vars']['flagConsumptionTaxGeneralRuleProration'];
			$flagConsumptionTaxSimpleRule = $flagConsumptionTaxGeneralRuleProration;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['generalProrationChange'][$flagConsumptionTaxGeneralRuleProration];
			if ($flagVars) {
				$flagConsumptionTaxSimpleRule = $this->_getUnknownConsumptionTax(array(
					'varsEntityNation' => $arr['varsEntityNationUpdate'],
					'flagTax'          => $flagVars['simple'],
				));
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = $flagConsumptionTaxSimpleRule;
			//$arr['vars']['flagConsumptionTaxWithoutCalc'] = 3;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		}

		return $arr['vars'];
	}

	/**
	 * ＜start10:課税・簡易・税込＞
	 */
	protected function _updateArrVarsLogDetail10($arr)
	{
		$arrStrTitle = $arr['varsItem']['arrAccountTitle']['arrStrTitle'];
		$idAccountTitle = $arr['vars']['idAccountTitle'];

		//＜update1:免税＞
		if (preg_match("/^(1)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = 1;
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = '';
			$arr['vars']['flagRateConsumptionTaxReduced'] = '';
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update2:課税・本則・個別・税込＞
		} elseif (preg_match("/^(2)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxSimpleRule = $arr['vars']['flagConsumptionTaxSimpleRule'];
			$flagConsumptionTaxGeneralRuleEach = $flagConsumptionTaxSimpleRule;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['simpleChange'][$flagConsumptionTaxSimpleRule];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleEach = $flagVars['generalEach'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = $flagConsumptionTaxGeneralRuleEach;
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update3:課税・本則・個別・税抜・内税＞
		//＜update4:課税・本則・個別・税抜・外税＞
		//＜update5:課税・本則・個別・税抜・別記＞
		} elseif (preg_match("/^(3|4|5)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxSimpleRule = $arr['vars']['flagConsumptionTaxSimpleRule'];
			$flagConsumptionTaxGeneralRuleEach = $flagConsumptionTaxSimpleRule;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['simpleChange'][$flagConsumptionTaxSimpleRule];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleEach = $flagVars['generalEach'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = $flagConsumptionTaxGeneralRuleEach;
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$numValueConsumptionTax = $this->_getCalcConsumptionTax(array(
				'varsEntityNation' => $arr['varsEntityNationUpdate'],
				'vars'             => $arr['vars'],
			));
			$arr['vars']['numValueConsumptionTax'] = $numValueConsumptionTax;

		//＜update6:課税・本則・比例・税込＞
		} elseif (preg_match("/^(6)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxSimpleRule = $arr['vars']['flagConsumptionTaxSimpleRule'];
			$flagConsumptionTaxGeneralRuleProration = $flagConsumptionTaxSimpleRule;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['simpleChange'][$flagConsumptionTaxSimpleRule];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleProration = $flagVars['generalProration'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = $flagConsumptionTaxGeneralRuleProration;
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update7:課税・本則・比例・税抜・内税＞
		//＜update8:課税・本則・比例・税抜・外税＞
		//＜update9:課税・本則・比例・税抜・別記＞
		} elseif (preg_match("/^(7|8|9)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxSimpleRule = $arr['vars']['flagConsumptionTaxSimpleRule'];
			$flagConsumptionTaxGeneralRuleProration = $flagConsumptionTaxSimpleRule;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['simpleChange'][$flagConsumptionTaxSimpleRule];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleProration = $flagVars['generalProration'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = $flagConsumptionTaxGeneralRuleProration;
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$numValueConsumptionTax = $this->_getCalcConsumptionTax(array(
				'varsEntityNation' => $arr['varsEntityNationUpdate'],
				'vars'             => $arr['vars'],
			));
			$arr['vars']['numValueConsumptionTax'] = $numValueConsumptionTax;

		//＜update10:課税・簡易・税込＞
		} elseif (preg_match("/^(10)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			//$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update11:課税・簡易・税抜・内税＞
		//＜update12:課税・簡易・税抜・外税＞
		//＜update13:課税・簡易・税抜・別記＞
		} elseif (preg_match("/^(11|12|13)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			//$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$numValueConsumptionTax = $this->_getCalcConsumptionTax(array(
				'varsEntityNation' => $arr['varsEntityNationUpdate'],
				'vars'             => $arr['vars'],
			));
			$arr['vars']['numValueConsumptionTax'] = $numValueConsumptionTax;

		}

		return $arr['vars'];
	}

	/**
	 * ＜start11:課税・簡易・税抜・内税＞
	 */
	protected function _updateArrVarsLogDetail11($arr)
	{
		$arrStrTitle = $arr['varsItem']['arrAccountTitle']['arrStrTitle'];
		$idAccountTitle = $arr['vars']['idAccountTitle'];

		//＜update1:免税＞
		if (preg_match("/^(1)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = 1;
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = '';
			$arr['vars']['flagRateConsumptionTaxReduced'] = '';
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update2:課税・本則・個別・税込＞
		} elseif (preg_match("/^(2)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxSimpleRule = $arr['vars']['flagConsumptionTaxSimpleRule'];
			$flagConsumptionTaxGeneralRuleEach = $flagConsumptionTaxSimpleRule;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['simpleChange'][$flagConsumptionTaxSimpleRule];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleEach = $flagVars['generalEach'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = $flagConsumptionTaxGeneralRuleEach;
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update3:課税・本則・個別・税抜・内税＞
		//＜update4:課税・本則・個別・税抜・外税＞
		//＜update5:課税・本則・個別・税抜・別記＞
		} elseif (preg_match("/^(3|4|5)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxSimpleRule = $arr['vars']['flagConsumptionTaxSimpleRule'];
			$flagConsumptionTaxGeneralRuleEach = $flagConsumptionTaxSimpleRule;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['simpleChange'][$flagConsumptionTaxSimpleRule];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleEach = $flagVars['generalEach'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = $flagConsumptionTaxGeneralRuleEach;
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			//$arr['vars']['numValueConsumptionTax'] = '';


		//＜update6:課税・本則・比例・税込＞
		} elseif (preg_match("/^(6)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxSimpleRule = $arr['vars']['flagConsumptionTaxSimpleRule'];
			$flagConsumptionTaxGeneralRuleProration = $flagConsumptionTaxSimpleRule;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['simpleChange'][$flagConsumptionTaxSimpleRule];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleProration = $flagVars['generalProration'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = $flagConsumptionTaxGeneralRuleProration;
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';


		//＜update7:課税・本則・比例・税抜・内税＞
		//＜update8:課税・本則・比例・税抜・外税＞
		//＜update9:課税・本則・比例・税抜・別記＞
		} elseif (preg_match("/^(7|8|9)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxSimpleRule = $arr['vars']['flagConsumptionTaxSimpleRule'];
			$flagConsumptionTaxGeneralRuleProration = $flagConsumptionTaxSimpleRule;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['simpleChange'][$flagConsumptionTaxSimpleRule];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleProration = $flagVars['generalProration'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = $flagConsumptionTaxGeneralRuleProration;
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			//$arr['vars']['numValueConsumptionTax'] = '';


		//＜update10:課税・簡易・税込＞
		} elseif (preg_match("/^(10)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			//$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';


		//＜update11:課税・簡易・税抜・内税＞
		//＜update12:課税・簡易・税抜・外税＞
		//＜update13:課税・簡易・税抜・別記＞
		} elseif (preg_match("/^(11|12|13)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			//$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			//$arr['vars']['numValueConsumptionTax'] = '';

		}

		return $arr['vars'];
	}

	/**
	 * ＜start12:課税・簡易・税抜・外税＞
	 */
	protected function _updateArrVarsLogDetail12($arr)
	{
		$arrStrTitle = $arr['varsItem']['arrAccountTitle']['arrStrTitle'];
		$idAccountTitle = $arr['vars']['idAccountTitle'];

		//＜update1:免税＞
		if (preg_match("/^(1)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = 1;
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = '';
			$arr['vars']['flagRateConsumptionTaxReduced'] = '';
			$arr['vars']['numValue'] += $arr['vars']['numValueConsumptionTax'];
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update2:課税・本則・個別・税込＞
		} elseif (preg_match("/^(2)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxSimpleRule = $arr['vars']['flagConsumptionTaxSimpleRule'];
			$flagConsumptionTaxGeneralRuleEach = $flagConsumptionTaxSimpleRule;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['simpleChange'][$flagConsumptionTaxSimpleRule];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleEach = $flagVars['generalEach'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = $flagConsumptionTaxGeneralRuleEach;
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValue'] += $arr['vars']['numValueConsumptionTax'];
			$arr['vars']['numValueConsumptionTax'] = '';


		//＜update3:課税・本則・個別・税抜・内税＞
		//＜update4:課税・本則・個別・税抜・外税＞
		//＜update5:課税・本則・個別・税抜・別記＞
		} elseif (preg_match("/^(3|4|5)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxSimpleRule = $arr['vars']['flagConsumptionTaxSimpleRule'];
			$flagConsumptionTaxGeneralRuleEach = $flagConsumptionTaxSimpleRule;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['simpleChange'][$flagConsumptionTaxSimpleRule];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleEach = $flagVars['generalEach'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = $flagConsumptionTaxGeneralRuleEach;
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			//$arr['vars']['flagConsumptionTaxWithoutCalc'] = 2;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			//$arr['vars']['numValueConsumptionTax'] = '';


		//＜update6:課税・本則・比例・税込＞
		} elseif (preg_match("/^(6)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxSimpleRule = $arr['vars']['flagConsumptionTaxSimpleRule'];
			$flagConsumptionTaxGeneralRuleProration = $flagConsumptionTaxSimpleRule;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['simpleChange'][$flagConsumptionTaxSimpleRule];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleProration = $flagVars['generalProration'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = $flagConsumptionTaxGeneralRuleProration;
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValue'] += $arr['vars']['numValueConsumptionTax'];
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update7:課税・本則・比例・税抜・内税＞
		//＜update8:課税・本則・比例・税抜・外税＞
		//＜update9:課税・本則・比例・税抜・別記＞
		} elseif (preg_match("/^(7|8|9)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxSimpleRule = $arr['vars']['flagConsumptionTaxSimpleRule'];
			$flagConsumptionTaxGeneralRuleProration = $flagConsumptionTaxSimpleRule;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['simpleChange'][$flagConsumptionTaxSimpleRule];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleProration = $flagVars['generalProration'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = $flagConsumptionTaxGeneralRuleProration;
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			//$arr['vars']['flagConsumptionTaxWithoutCalc'] = 2;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			//$arr['vars']['numValueConsumptionTax'] = '';

		//＜update10:課税・簡易・税込＞
		} elseif (preg_match("/^(10)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			//$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValue'] += $arr['vars']['numValueConsumptionTax'];
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update11:課税・簡易・税抜・内税＞
		//＜update12:課税・簡易・税抜・外税＞
		//＜update13:課税・簡易・税抜・別記＞
		} elseif (preg_match("/^(11|12|13)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			//$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			//$arr['vars']['flagConsumptionTaxWithoutCalc'] = 2;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			//$arr['vars']['numValueConsumptionTax'] = '';

		}

		return $arr['vars'];
	}

	/**
	 * ＜start13:課税・簡易・税抜・別記＞
	 */
	protected function _updateArrVarsLogDetail13($arr)
	{
		$arrStrTitle = $arr['varsItem']['arrAccountTitle']['arrStrTitle'];
		$idAccountTitle = $arr['vars']['idAccountTitle'];

		//＜update1:免税＞
		if (preg_match("/^(1)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = 1;
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = '';
			$arr['vars']['flagRateConsumptionTaxReduced'] = '';
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update2:課税・本則・個別・税込＞
		} elseif (preg_match("/^(2)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxSimpleRule = $arr['vars']['flagConsumptionTaxSimpleRule'];
			$flagConsumptionTaxGeneralRuleEach = $flagConsumptionTaxSimpleRule;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['simpleChange'][$flagConsumptionTaxSimpleRule];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleEach = $flagVars['generalEach'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = $flagConsumptionTaxGeneralRuleEach;
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update3:課税・本則・個別・税抜・内税＞
		//＜update4:課税・本則・個別・税抜・外税＞
		//＜update5:課税・本則・個別・税抜・別記＞
		} elseif (preg_match("/^(3|4|5)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxSimpleRule = $arr['vars']['flagConsumptionTaxSimpleRule'];
			$flagConsumptionTaxGeneralRuleEach = $flagConsumptionTaxSimpleRule;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['simpleChange'][$flagConsumptionTaxSimpleRule];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleEach = $flagVars['generalEach'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = $flagConsumptionTaxGeneralRuleEach;
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			//$arr['vars']['flagConsumptionTaxWithoutCalc'] = 3;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update6:課税・本則・比例・税込＞
		} elseif (preg_match("/^(6)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxSimpleRule = $arr['vars']['flagConsumptionTaxSimpleRule'];
			$flagConsumptionTaxGeneralRuleProration = $flagConsumptionTaxSimpleRule;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['simpleChange'][$flagConsumptionTaxSimpleRule];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleProration = $flagVars['generalProration'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = $flagConsumptionTaxGeneralRuleProration;
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update7:課税・本則・比例・税抜・内税＞
		//＜update8:課税・本則・比例・税抜・外税＞
		//＜update9:課税・本則・比例・税抜・別記＞
		} elseif (preg_match("/^(7|8|9)$/", $arr['flagUpdate'])) {
			$flagConsumptionTaxSimpleRule = $arr['vars']['flagConsumptionTaxSimpleRule'];
			$flagConsumptionTaxGeneralRuleProration = $flagConsumptionTaxSimpleRule;
			$flagVars = $arr['varsItem']['varsConsumptionTax']['simpleChange'][$flagConsumptionTaxSimpleRule];
			if ($flagVars) {
				$flagConsumptionTaxGeneralRuleProration = $flagVars['generalProration'];
			}
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = $flagConsumptionTaxGeneralRuleProration;
			$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			//$arr['vars']['flagConsumptionTaxWithoutCalc'] = 3;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update10:課税・簡易・税込＞
		} elseif (preg_match("/^(10)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = 1;
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			//$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			$arr['vars']['flagConsumptionTaxWithoutCalc'] = 1;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			$arr['vars']['numValueConsumptionTax'] = '';

		//＜update11:課税・簡易・税抜・内税＞
		//＜update12:課税・簡易・税抜・外税＞
		//＜update13:課税・簡易・税抜・別記＞
		} elseif (preg_match("/^(11|12|13)$/", $arr['flagUpdate'])) {
			$arr['vars']['flagConsumptionTaxFree'] = '';
			$arr['vars']['flagConsumptionTaxIncluding'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleEach'] = '';
			$arr['vars']['flagConsumptionTaxGeneralRuleProration'] = '';
			//$arr['vars']['flagConsumptionTaxSimpleRule'] = '';
			//$arr['vars']['flagConsumptionTaxWithoutCalc'] = 2;
			$arr['vars']['numRateConsumptionTax'] = $this->_getCalcRateConsumptionTax(array(
				'vars'      => $arr['vars'],
				'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 start $this->_getCalcRateConsumptionTaxのnumRateConsumptionTaxの結果を受けて処理が始まるので順序注意
			 */
			$arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));
			/*
			 * 20191001 end
			 */
			//$arr['vars']['numValueConsumptionTax'] = '';

		}

		return $arr['vars'];
	}

	/**
	(array(
				'varsEntityNation'   => $arr['varsEntityNationUpdate'],
				'flagTax' => $arrStrTitle[$idAccountTitle]['flagConsumptionTaxSimpleRule'],
	 ));
	 */
	protected function _getDefaultConsumptionTax($arr)
	{
		$flagTax = $arr['flagTax'];
		if ($flagTax == 'tax-Default') {
			$flagTax = 'tax-' . $arr['varsEntityNation']['flagConsumptionTaxBusinessType'];

		} elseif ($arr['numValue'] == 'tax-Back-Default') {
			$flagTax = 'tax-Back-' . $arr['varsEntityNation']['flagConsumptionTaxBusinessType'];
		}

		return $flagTax;
	}

	/**
	_getUnknownConsumptionTax(array(
	 'varsEntityNation' => $varsEntityNationUpdate,
	 'flagTax'         => $arr['vars']['numValue'],
	 ));
	 */
	protected function _getUnknownConsumptionTax($arr)
	{
		$flagTax = $arr['flagTax'];
		if ($flagTax == 'tax-unknown') {
			$flagTax = 'tax-' . $arr['varsEntityNation']['flagConsumptionTaxBusinessType'];

		} elseif ($flagTax == 'tax-Back-unknown') {
			$flagTax = 'tax-Back-' . $arr['varsEntityNation']['flagConsumptionTaxBusinessType'];
		}

		return $flagTax;
	}


	/**
	 (array(
		'varsEntityNation' => $varsEntityNationUpdate,
		'vars'             => $arr['vars'],
	 ));
	 */
	protected function _getCalcConsumptionTax($arr)
	{
		$flagTax = 0;
		$idAccountTitle = $arr['vars']['idAccountTitle'];
		$numValue = $arr['vars']['numValue'];
		$numValueConsumptionTax = '';
		$numRateConsumptionTax = (int) $arr['vars']['numRateConsumptionTax'];

		$flagConsumptionTaxCalc = (int) $arr['vars']['flagConsumptionTaxCalc'];
		$flagConsumptionTaxWithoutCalc = (int) $arr['vars']['flagConsumptionTaxWithoutCalc'];

		if (!(int) $arr['varsEntityNation']['flagConsumptionTaxFree']
			&& !(int) $arr['varsEntityNation']['flagConsumptionTaxIncluding']
			&& $idAccountTitle != 'suspenseReceiptOfConsumptionTaxes'
			&& $idAccountTitle != 'suspensePaymentConsumptionTaxes'
			&& $numValue
		) {
			if ((int) $arr['varsEntityNation']['flagConsumptionTaxGeneralRule']) {
				if ((int) $arr['varsEntityNation']['flagConsumptionTaxDeducted']) {
					if (preg_match("/^tax/", $arr['vars']['flagConsumptionTaxGeneralRuleEach'])) {
						$flagTax = 1;
					}

				} else {
					if (preg_match("/^tax/", $arr['vars']['flagConsumptionTaxGeneralRuleProration'])) {
						$flagTax = 1;
					}
				}

			} else {
				if (preg_match("/^tax/", $arr['vars']['flagConsumptionTaxSimpleRule'])) {
					$flagTax = 1;
				}
			}
		}

		if ($flagTax) {
			$numValueConsumptionTax = 0;
			if ($flagConsumptionTaxWithoutCalc == 1) {
				$numValueConsumptionTax = $numValue *  $numRateConsumptionTax / (100 + $numRateConsumptionTax);
				if ($flagConsumptionTaxCalc == 1) {
					$numValueConsumptionTax = floor($numValueConsumptionTax);

				} elseif ($flagConsumptionTaxCalc == 2) {
					$numValueConsumptionTax = round($numValueConsumptionTax);

				} elseif ($flagConsumptionTaxCalc == 3) {
					$numValueConsumptionTax = ceil($numValueConsumptionTax);
				}

			} elseif ($flagConsumptionTaxWithoutCalc == 2) {
				//this is ok not wrong
				$numValueConsumptionTax = $numValue *  $numRateConsumptionTax / (100 + $numRateConsumptionTax);
				if ($flagConsumptionTaxCalc == 1) {
					$numValueConsumptionTax = floor($numValueConsumptionTax);

				} elseif ($flagConsumptionTaxCalc == 2) {
					$numValueConsumptionTax = round($numValueConsumptionTax);

				} elseif ($flagConsumptionTaxCalc == 3) {
					$numValueConsumptionTax = ceil($numValueConsumptionTax);
				}

			} elseif ($flagConsumptionTaxWithoutCalc == 3) {
				$numValueConsumptionTax = '';
			}
		}

		return $numValueConsumptionTax;
	}

	/*
	* 20191001 start
	*/
	/**
            $arr['vars']['flagRateConsumptionTaxReduced'] = $this->_getCalcFlagRateConsumptionTaxReduced(array(
			    'vars'      => $arr['vars'],
			    'stampBook' => $arr['stampBook'],
			));

			function _getCalcRateConsumptionTax の結果を受けての処理
	 */
	protected function _getCalcFlagRateConsumptionTaxReduced($arr)
	{
	    global $classTime;

	    $flagRateConsumptionTaxReduced = '';

	    if ($arr['vars']['numRateConsumptionTax'] != '') {

	        $flagRateConsumptionTaxReduced = 0;

	        if ($arr['vars']['numRateConsumptionTax'] == 8) {
	            if ($arr['vars']['flagRateConsumptionTaxReduced']) {
	                $flagRateConsumptionTaxReduced = 1;
	            }
	        }
	    }

	    return $flagRateConsumptionTaxReduced;
	}

	/*
	* 20191001 end
	*/

	/**
	 (array(
		'flagConsumptionTax' => $flagConsumptionTaxGeneralRuleEach,
		'vars'               => $arr['vars'],
		'stampBook'          => $arr['stampBook'],
	 ));
	 */
	protected function _getCalcRateConsumptionTax($arr)
	{
		global $classTime;

		$flagConsumptionTax = $this->_getCalcFlagConsumptionTax(array('vars' => $arr['vars'],));

		if (!(preg_match( "/^tax/", $flagConsumptionTax)
			|| preg_match( "/^else/", $flagConsumptionTax)
		)) {
			return '';
		}

		$numRateConsumptionTax = $arr['vars']['numRateConsumptionTax'];
		if ($numRateConsumptionTax != '') {
			return $numRateConsumptionTax;
		}

		//消費税率自動挿入
		if ($arr['stampBook']) {
			$numRate = $classTime->checkRateConsumptionTax(array('stamp' => $arr['stampBook']));

		} else {
			//for import filter
			return '';
		}
/*
 * 20191001 start
 */
		if ($numRate == 10) {
		    if ($arr['vars']['flagRateConsumptionTaxReduced']) {
		        $numRate = 8;
		    }
		}
/*
 * 20191001 end
*/
		return $numRate;
	}

	/**
	 (array(
		'vars' => $arr['vars'],
	 ));
	 */
	protected function _getCalcFlagConsumptionTax($arr)
	{
		if ($arr['vars']['flagConsumptionTaxGeneralRuleEach']) {
			return $arr['vars']['flagConsumptionTaxGeneralRuleEach'];
		}

		if ($arr['vars']['flagConsumptionTaxGeneralRuleProration']) {
			return $arr['vars']['flagConsumptionTaxGeneralRuleProration'];
		}

		if ($arr['vars']['flagConsumptionTaxSimpleRule']) {
			return $arr['vars']['flagConsumptionTaxSimpleRule'];
		}

		return '';
	}


}
