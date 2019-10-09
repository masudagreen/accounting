<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CashDefer extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference' => 'cashWindow',
		'idLog'        => 'logWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/cashDefer.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/cashDefer.php',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;
		global $classCheck;

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

		if (!$this->_checkCurrent()) {
			$this->_sendOldFlag();
		}

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
		global $classSmarty;

		$vars = $this->getStamp();
		$json = json_encode($vars);
		$classSmarty->assign('stamp', $json);

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$vars = $this->_updateVars(array(
			'vars' => $vars,
		));

		$rows = $this->getSearch(array('numLotNow' => 0));
		$vars = $this->_updateSearch(array(
			'vars' => $vars,
			'rows' => $rows,
		));

		$json = json_encode($vars);
		$classSmarty->assign('varsLoad', $json);

		$classSmarty->assign('numNews', $this->getNumNews());
		$contents = $classSmarty->fetch($this->_extSelf['pathTplJs']);

		$this->sendJs(array(
			'data' => $contents,
		));

	}

	/**
		(array(
			'vars' => $vars,
		))
	 */
	protected function _updateVars($arr)
	{
		global $varsPluginAccountingAccount;

		$vars = $arr['vars'];

		$arrSubAccountTitle = $this->_getVarsSubAccountTitle(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrAccountTitle = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'numFiscalPeriod'    => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsPreference = $this->_getVarsPreference(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrDepartment = $this->_getVarsDepartment(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsConsumptionTax = $this->_getVarsConsumptionTax(array());

		$vars['varsRule'] = array(
			'arrAccountTitle'     => $arrAccountTitle,
			'varsPreference'      => $varsPreference,
			'arrDepartment'       => $arrDepartment,
			'arrSubAccountTitle'  => $arrSubAccountTitle,
			'varsEntityNation'    => $varsEntityNation,
			'varsConsumptionTax'  => $varsConsumptionTax,
		);

		return $vars;
	}

	/**
		(array(
		))
	 */
	protected function _getVarsPreference($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingCash',
			'arrLimit' => array(),
			'arrOrder' => array(),
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
		));

		return $rows['arrRows'][0];
	}

	/**
	 * array(
	 *  'numLotNow' => int
	 * )
	 */
	public function getSearch($arr)
	{
		global $varsAccount;
		global $varsPluginAccountingAccount;
		global $classDb;

		$numStart = $arr['numLotNow'] * $varsAccount['numList'];
		$numEnd = $numStart + $varsAccount['numList'];

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogCashDefer',
			'arrLimit' => array(
				'numStart' => $numStart, 'numEnd' => $numEnd,
			),
			'arrOrder' => array(
				'strColumn' => 'id',
				'flagDesc'  => 1,
			),
			'flagAnd'   => 1,
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
					'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				),
			),
		));

		return $rows;
	}

	/**
	 *
	 */
	protected function _updateSearch($arr)
	{
		$classTime = new Code_Else_Lib_Time();

		global $classHtml;
		global $varsAccount;

		global $varsPluginAccountingAccount;

		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$idEntity  = $varsPluginAccountingAccount['idEntityCurrent'];
		$strCheckStamp = 'accountingLogCashDefer_' . $idEntity . '_' . $numFiscalPeriod;

		$vars = $arr['vars'];
		$rows = $arr['rows'];

		$flagLogInsert = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'insert',
			'idTarget'      => $this->_extSelf['idLog'],
		));

		$varsAuthority = $this->_getVarsAuthority(array());

		$classCalcLog = $this->_getClassCalc(array('flagType' => 'Log'));

		$arraySide = array('Debit', 'Credit');
		$array = &$rows['arrRows'];
		$arrayNew = array();
		foreach ($array as $key => $value) {
			$varsTmpl = $vars['portal']['varsNavi']['tree']['templateDetail'];
			$varsTmpl['id'] = $value['id'];

			$classTime->setTimeZone(array('data' => $varsAccount['numTimeZone']));
			$strTime = $classTime->getDisplay(array(
				'stamp'    => $value['stampRegister'],
				'flagType' => 'year-sec',
			));
			$varsTmpl['strTitle'] = $strTime;

			if ($varsAccount['jsonStampCheck'][$strCheckStamp] < $value['stampRegister']) {
				$flag = 1;

			} else {
				$varsTmpl['strClassLoad'] = '';
			}

			$varsTmpl['vars']['idTarget'] = $value['id'];
			$varsTmpl['vars']['stampRegister'] = $value['stampRegister'];
			$varsTmpl['vars']['stampArrive'] = $value['stampArrive'];
			$varsTmpl['vars']['stampUpdate'] = $value['stampUpdate'];
			$varsTmpl['vars']['stampBook'] = $value['stampBook'];
			$varsTmpl['vars']['arrSpaceStrTag'] = $value['arrSpaceStrTag'];

			$arrayTemp = array('flagApply', 'idAccountApply', 'arrCommaIdAccountPermit', 'jsonPermitHistory');
			foreach ($arrayTemp as $keyTemp => $valueTemp) {
				$varsTmpl['vars'][$valueTemp] = $value[$valueTemp];
			}

			$varsTmpl['jsonVersion'] = $this->_updateSearchJsonVersion(array(
				'vars'      => $vars,
				'value'     => $value['jsonVersion'],
			));

			$numVersionEnd = count($varsTmpl['jsonVersion']) - 1;
			$varsTmpl['jsonDetail'] = $varsTmpl['jsonVersion'][$numVersionEnd];
			$varsTmpl['numVersion'] = count($varsTmpl['jsonVersion']);

			$flagLogCashVars = $this->_checkLogCash(array(
				'varsLog' => $value,
			));

			$varsTmpl['vars']['flagLogCash'] = 0;
			$varsTmpl['vars']['arrayOptionIdLogCash'] = array();
			$varsTmpl['vars']['valueIdLogCash'] = '';
			if ($flagLogCashVars['numRows']) {
				$varsTmpl['vars']['flagLogCash'] = 1;
				$varsTmpl['vars']['arrayOptionIdLogCash'] = $this->_getSearchArrayOption(array(
					'vars'         => $vars,
					'classCalcLog' => $classCalcLog,
					'arrVarsLog'   => $flagLogCashVars['arrRows'],
				));
				if ($varsTmpl['vars']['arrayOptionIdLogCash']) {
					$varsTmpl['vars']['valueIdLogCash'] = $varsTmpl['vars']['arrayOptionIdLogCash'][0]['value'];
				} else {
					$varsTmpl['vars']['flagLogCash'] = 0;
				}
			}

			$varsTmpl['vars']['flagPermitLost'] = 0;
			$varsTmpl['jsonPermitHistory'] = array();
			if ($vars['varsRule']['varsPreference']['flagPermitImport']) {
				$varsTmpl['jsonPermitHistory'] = $this->_updateSearchJsonPermitHistory(array(
					'vars'  => $vars,
					'value' => $value['jsonPermitHistory'],
				));
				$varsTmpl['vars']['flagPermitLost'] = $this->_checkPermitLost(array(
					'classCalcLog' => $classCalcLog,
					'value'        => $value,
				));
			}

			$varsTmpl['vars']['flagLostJournal'] = 0;
			$arrayDetail = $varsTmpl['jsonVersion'][$numVersionEnd]['jsonDetail']['varsDetail'];
			foreach ($arrayDetail as $keyDetail => $valueDetail) {
				if ($varsTmpl['vars']['flagLostJournal']) {
					break;
				}
				foreach ($arraySide as $keySide => $valueSide) {

					$idAccountTitle = $valueDetail['arr' . $valueSide]['idAccountTitle'];
					$idDepartment = $valueDetail['arr' . $valueSide]['idDepartment'];
					$idSubAccountTitle = $valueDetail['arr' . $valueSide]['idSubAccountTitle'];

					if ($idAccountTitle) {

						//strAccountTitle
						$strAccountTitle = $vars['varsRule']['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['strTitleFS'];
						if (!$strAccountTitle) {
							$varsTmpl['vars']['flagLostJournal'] = 1;
							break;
						}

						//strSubAccountTitle
						$idSubAccountTitle = $valueDetail['arr' . $valueSide]['idSubAccountTitle'];
						$strSubAccountTitle = $vars['varsRule']['arrSubAccountTitle']['arrStrTitle'][$idAccountTitle][$idSubAccountTitle]['strTitle'];
						if ($idSubAccountTitle && !$strSubAccountTitle) {
							$varsTmpl['vars']['flagLostJournal'] = 1;
							break;
						}

						//strDepartment
						$strDepartment = $vars['varsRule']['arrDepartment']['arrStrTitle'][$idDepartment]['strTitle'];
						if ($idDepartment && !$strDepartment) {
							$varsTmpl['vars']['flagLostJournal'] = 1;
							break;
						}
					}
				}
			}
			$arrayNew[] = $varsTmpl;
		}

		$vars['portal']['varsNavi']['tree']['varsDetail']['varsPage']['varsStatus']['numRows'] = $rows['numRows'];
		$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $arrayNew;

		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllDelete'])) {
			$vars['portal']['varsNavi']['tree']['varsDetail']['varsBtn'] = array();
		}

		$array = $vars['portal']['varsDetail']['tmplBtn']['varsStart'];
		$arrayNew = array();
		foreach ($array as $key => $value) {
			if ($value['vars']['idTarget'] == 'delete') {
				if (!($varsAuthority == 'admin' || $varsAuthority['flagAllDelete'])) {
					continue;
				}

			} elseif ($value['vars']['idTarget'] == 'add') {
				if (!($varsAuthority == 'admin' || $varsAuthority['flagAllInsert'])) {
					continue;
				}
				if (!$flagLogInsert) {
					continue;
				}
			}
			$arrayNew[] = $value;
		}
		$vars['portal']['varsDetail']['tmplBtn']['varsStart'] = $arrayNew;

		if ($flag) {
			$this->_setDbStampCheck(array(
				'strColumnAccount'    => $strCheckStamp,
				'strColumnPreference' => 'accounts',
			));
		}

		return $vars;
	}

	/**
		(array(
			'vars'         => $vars,
			'classCalcLog' => $classCalcLog,
			'arrVarsLog'   => $flagLogCashVars['arrRows'],
		))
	 */
	protected function _getSearchArrayOption($arr)
	{
		$vars = &$arr['vars'];
		$classCalcLog = &$arr['classCalcLog'];

		$array = $arr['arrVarsLog'];
		$arrayNew = array();
		foreach ($array as $key => $value) {
			$flagPermitLost = $this->_checkPermitLost(array(
				'classCalcLog' => $classCalcLog,
				'value'        => $value,
			));
			if ($flagPermitLost) {
				continue;
			}
			$varsTmpl = array();
			$strTitle = $vars['varsItem']['strId'] . ' : ' . $value['idLogCash'];
			$strTitle .= '  ';
			$strTitle .= $vars['varsItem']['strMemo'] . ' : ';
			if ($value['strTitle'] == '' || is_null($value['strTitle'])) {
				$strTitle .= $vars['varsItem']['strBlank'];
			} else {
				$strTitle .= $value['strTitle'];
			}
			$varsTmpl['strTitle'] = $strTitle;
			$varsTmpl['value'] = $value['idLogCash'];
			$arrayNew[] = $varsTmpl;
		}

		return $arrayNew;
	}

	/**
		(array(
			'vars' => array,
			'value' => array,
		))
	 */
	protected function _updateSearchJsonPermitHistory($arr)
	{
		global $varsAccounts;
		global $varsAccount;
		global $varsPluginAccountingAccountsId;

		$array = $arr['value'];
		$arrayNew = array();
		foreach ($array as $key => $value) {
			$data = array();
			$data['stampRegister'] = $value['stampRegister'];
			$strCodeName = $varsAccounts[$value['idAccountApply']]['strCodeName'];
			if (!$strCodeName) {
				$strCodeName = $varsPluginAccountingAccountsId[$value['idAccountApply']]['strCodeName'];
			}
			$data['strCodeName'] = $strCodeName;
			$data['flagInvalid'] = (int) $value['flagInvalid'];
			$data['numSumMax'] = (int) $value['numSumMax'];

			$arrayNewPermit = array();
			$numPermit = 0;
			$numPermitBack = 0;
			$stampPermit = 0;
			$arrayPermit = $value['arrIdAccountPermit'];
			$strStatus = '';
			foreach ($arrayPermit as $keyPermit => $valuePermit) {
				if ($valuePermit['flagPermit'] == 'done') {
					$numPermit++;
					if ($stampPermit < $valuePermit['stampRegister']) {
						$stampPermit = $valuePermit['stampRegister'];
					}

				} elseif ($valuePermit['flagPermit'] == 'back') {
					$numPermitBack++;
				}

				$dataPermit = $valuePermit;
				$strCodeName = $varsAccounts[$dataPermit['idAccount']]['strCodeName'];
				if (!$varsAccounts[$dataPermit['idAccount']]['strCodeName']) {
					$strCodeName = $varsPluginAccountingAccountsId[$dataPermit['idAccount']]['strCodeName'];
				}
				$dataPermit['strCodeName'] = $strCodeName;
				if ($varsAccount['id'] == $dataPermit['idAccount']) {
					if ($valuePermit['flagPermit'] == 'done') {
						$strStatus = $arr['vars']['varsItem']['strPermitDone'];

					} elseif ($valuePermit['flagPermit'] == 'back') {
						$strStatus = $arr['vars']['varsItem']['strPermitDone'];

					} elseif ($valuePermit['flagPermit'] == 'none') {
						$strStatus = $arr['vars']['varsItem']['strPermitNeed'];
					}
				}
				$arrayNewPermit[] = $dataPermit;
			}
			$data['stampPermit'] = $stampPermit;
			$data['numSumPermit'] = $numPermit;
			$data['numSumBack'] = $numPermitBack;

			$numSumMax = count($arrayPermit) - $data['numSumBack'];
			if ($data['flagInvalid']) {
				$data['stampPermit'] = 0;
				if ($numSumMax < $data['numSumMax']) {
					$data['strStatus'] = $arr['vars']['varsItem']['strApplyBack'];
				} else {
					$data['strStatus'] = $arr['vars']['varsItem']['strRevocation'];
				}


			} else {
				if ($data['numSumPermit'] >= $data['numSumMax']) {
					$data['strStatus'] = $arr['vars']['varsItem']['strDone'];

				} elseif ($numSumMax < $data['numSumMax']) {
					$data['stampPermit'] = 0;
					$data['strStatus'] = $arr['vars']['varsItem']['strApplyBack'];

				} else {
					$data['stampPermit'] = 0;
					if ($strStatus) {
						$data['strStatus'] = $strStatus;

					} else {
						$data['strStatus'] = $arr['vars']['varsItem']['strApply'];
					}
				}

			}

			$data['arrIdAccountPermit'] = $arrayNewPermit;

			$arrayNew[] = $data;
		}

		return $arrayNew;
	}

	/**
		(array(
			'classCalcLog' => $classCalcLog,
			'value'        => $value,
		))
	 */
	protected function _checkPermitLost($arr)
	{
		global $varsPluginAccountingAccount;

		$value = $arr['value'];
		$classCalcLog = &$arr['classCalcLog'];

		$varsPermitHistory = end($value['jsonPermitHistory']);
		$varsOrder = array(
			'numFiscalPeriod'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'                => $varsPluginAccountingAccount['idEntityCurrent'],
			'idAccount'               => $value['idAccount'],
			'idAccountApply'          => $value['idAccountApply'],
			'flagFiscalReport'        => 'none',
			'stampBook'               => '',
			'strTitle'                => '',
			'jsonDetail'              => '',
			'arrCommaIdLogFile'       => '',
			'arrCommaIdAccountPermit' => $value['arrCommaIdAccountPermit'],
			'numSumMax'               => $varsPermitHistory['numSumMax'],
			'arrSpaceStrTag'          => '',
		);

		$flag = $classCalcLog->allot(array(
			'flagStatus'      => 'check',
			'varsOrder'       => $varsOrder,
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagCheck'       => 'Permit',
			'varsItem'        => array('dummy'),
		));

		return ($flag)? 1 : 0;
	}

	/**
		(array(
			'vars'      => $vars,
			'value'     => $value['jsonVersion'],
			'idAccount' => $value['idAccount'],
		))
	 */
	protected function _updateSearchJsonVersion($arr)
	{
		global $classEscape;

		$array = $arr['value'];
		$arrayNew = array();
		$num = 1;
		foreach ($array as $key => $value) {
			$data = array();
			$data['stampRegister'] = $value['stampRegister'];
			$data['stampUpdate'] = $value['stampUpdate'];
			$data['stampBook'] = $value['stampBook'];
			$data['strTitle'] = $value['strTitle'];
			$data['arrSpaceStrTag'] = preg_replace('/^ /', '', $value['arrSpaceStrTag']);
			$data['vars']['arrSpaceStrTag'] = $classEscape->splitSpaceArrayData(array('data' => $value['arrSpaceStrTag']));
			$data['jsonDetail'] = $value['jsonDetail'];
			$data['strVersion'] = 'Ver.' . $num;
			$data['numVersion'] = $num;
			$arrayNew[] = $data;
			$num++;
		}

		return $arrayNew;
	}

	/**
		(array(

		))
	 */
	protected function _checkLogCash($arr)
	{
		global $classDb;

		//arrCommaTaxPayment~arrCommaTaxReceipt~ not need
		$arrayTemp = array(
			'stampBook',
			'idEntity',
			'numFiscalPeriod',
			'numValue',
			'arrCommaIdDepartmentDebit',
			'arrCommaIdAccountTitleDebit',
			'arrCommaIdSubAccountTitleDebit',
			'arrCommaConsumptionTaxDebit',
			'arrCommaRateConsumptionTaxDebit',
			'arrCommaConsumptionTaxWithoutCalcDebit',
			'arrCommaIdDepartmentCredit',
			'arrCommaIdAccountTitleCredit',
			'arrCommaIdSubAccountTitleCredit',
			'arrCommaConsumptionTaxCredit',
			'arrCommaRateConsumptionTaxCredit',
			'arrCommaConsumptionTaxWithoutCalcCredit'
		);

		$array = array();
		foreach ($arrayTemp as $keyTemp => $valueTemp) {
			$array[$valueTemp] = $arr['varsLog'][$valueTemp];
		}
		$array['flagPay'] = 0;
		$array['flagRemove'] = 0;

		$arrWhere = array();
		foreach ($array as $key => $value) {
			$arrWhere[] = array(
				'flagType'      => '',
				'strColumn'     => $key,
				'flagCondition' => 'eq',
				'value'         => $value,
			);
		}

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogCash',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'   => 1,
			'arrWhere'  => $arrWhere,
		));

		return $rows;
	}


	/**
	 */
	protected function _iniNaviSearch()
	{
		$this->_setNaviSearch(array('flag' => 1));
	}

	/**
	 *
	 */
	protected function _iniNaviReload()
	{
		$this->_setNaviSearch(array('flag' => 1));
	}

	/**
	 * array(
	 *  flag => int
	 * )
	 */
	protected function _setNaviSearch($arr)
	{
		global $varsPluginAccountingPreference;

		global $varsRequest;
		global $classCheck;

		$numLotNow = $varsRequest['query']['jsonSearch']['numLotNow'];
		$flag = $classCheck->checkValueWord(array(
			'flagType' => 'num',
			'value'    => $numLotNow,
		));

		if ($flag && $numLotNow != '') {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__);
			}
			exit;
		}

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingPreference['jsonStampUpdate']['logCashDefer'],
				'flagSearch'  => 1,
			));
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$vars = $this->_updateVars(array(
			'vars' => $vars,
		));

		$rows = $this->getSearch(array('numLotNow' => $numLotNow));

		if (!count($rows['arrRows'])) {
			$numLotNow = 0;
			$rows = $this->getSearch(array('numLotNow' => $numLotNow));
		}

		$vars = $this->_updateSearch(array(
			'vars' => $vars,
			'rows' => $rows,
		));

		$data = array(
			'numLotNow'  => $numLotNow,
			'numRows'    => $rows['numRows'],
			'varsDetail' => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
		);

		$this->sendVars(array(
			'flag'    => $arr['flag'],
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $data,
		));

	}

	/**
	 *
	 */
	protected function _iniDetailDelete()
	{
		global $varsRequest;

		$this->_setDelete(array(
			'arrId' => array($varsRequest['query']['jsonValue']['idTarget']),
		));
	}

	/**
	 *
	 */
	protected function _iniNaviDelete()
	{
		$arrayNew = array();
		$array = $this->_getLogDefer(array());
		foreach ($array as $key => $value) {
			$arrayNew[] = $value['id'];
		}

		$this->_setDelete(array(
			'arrId' => $arrayNew,
		));

	}

	/**
		(array(

		))
	 */
	protected function _getLogDefer($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogCashDefer',
			'arrLimit' => array(),
			'arrOrder' => array(
				'strColumn' => 'id',
				'flagDesc'  => 1,
			),
			'flagAnd'   => 1,
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
					'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				),
			),
		));

		return $rows['arrRows'];
	}

	/**
	 *
	 */
	protected function _setDelete($arr)
	{
		global $varsRequest;
		global $classCheck;
		global $varsPluginAccountingAccount;

		global $classDb;
		$dbh = $classDb->getHandle();

		$varsAuthority = $this->_getVarsAuthority(array());

		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllDelete'])) {
			$this->_sendOldFlag();
		}

		$array = $arr['arrId'];
		$flag = $classCheck->checkValueFormat(array(
			'flagType' => 'num',
			'flagArr'  => 1,
			'value'    => $array,
		));

		if ($flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
			}
			exit;
		}

		try {
			$dbh->beginTransaction();

			foreach ($array as $key => $value) {
				$classDb->deleteRow(array(
					'idModule'  => 'accounting',
					'strTable'  => 'accountingLogCashDefer',
					'flagAnd'   => 1,
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
							'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
						),
						array(
							'flagType'      => 'num',
							'strColumn'     => 'id',
							'flagCondition' => 'eq',
							'value'         => $value,
						),
					),
				));
			}

			$array = array('logCashDefer');
			foreach ($array as $key => $value) {
				$this->_updateDbPreferenceStamp(array('strColumn' => $value));
			}

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
		$this->_setNaviSearch(array('flag' => 1));
	}



	/**
	 *
	 */
	protected function _iniDetailAdd()
	{
		global $classDb;
		global $classEscape;
		global $classCheck;

		global $varsRequest;
		$dbh = $classDb->getHandle();

		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$flagLogInsert = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'insert',
			'idTarget'      => $this->_extSelf['idLog'],
		));

		if (!$flagLogInsert) {
			$this->_sendOldFlag();
		}

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllInsert'])) {
			$this->_sendOldFlag();
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$vars = $this->_updateVars(array(
			'vars' => $vars,
		));

		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];
		$array = array($idTarget);
		$flag = $classCheck->checkValueFormat(array(
			'flagType' => 'num',
			'flagArr'  => 1,
			'value'    => $array,
		));
		if ($flag) {
			$this->_sendOldData();
		}

		$rows = $this->_getVarsLogCashDefer(array('idTarget' => $idTarget));

		$vars = $this->_updateSearch(array(
			'vars' => $vars,
			'rows' => $rows,
		));

		$varsDefer = end($vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail']);
		if (!$varsDefer) {
			$this->_sendOldData();
		}

		$classCalcLog = $this->_getClassCalc(array('flagType' => 'Log'));
		if ($varsDefer['vars']['flagLogCash']) {
			$tempData = $this->_checkPay(array(
				'vars'         => $vars,
				'varsItem'     => $varsItem,
				'classCalcLog' => $classCalcLog,
			));

		} else {
			$flagIn = $this->_checkPaid(array(
				'vars'         => $vars,
				'varsItem'     => $varsItem,
				'varsLogDefer' => end($rows['arrRows']),
			));
		}

		try {
			$dbh->beginTransaction();

			if ($varsDefer['vars']['flagLogCash']) {
				$this->_setPay(array(
					'arrVarsLogAdd'    => $tempData['arrVarsLogAdd'],
					'arrVarsLogDelete' => $tempData['arrVarsLogDelete'],
				));

			} else {
				$tempData = $this->_setPaid(array(
					'classCalcLog' => $classCalcLog,
					'vars'         => $vars,
					'varsLogDefer' => end($rows['arrRows']),
					'flagIn'       => $flagIn,
				));
			}

			$vars = $this->_setWrite(array(
				'arrVarsLog'     => $tempData['arrVarsLogAdd'],
				'stampArrive'    => $tempData['stampArrive'],
				'arrSpaceStrTag' => $tempData['arrSpaceStrTag'],
				'arrLogDefer'    => $rows['arrRows'],
				'vars'           => $vars,
				'classCalcLog'   => $classCalcLog,
			));

			$this->_setDeleteDefer(array(
				'arrId'   => array($tempData['idLogDefer']),
			));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}
		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
		$data = $this->_setNaviSearch(array('flag' => 1));
	}

	/**
		(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		))
	 */
	protected function _checkPaid($arr)
	{
		$varsDefer = end($arr['vars']['portal']['varsNavi']['tree']['varsDetail']['varsDetail']);
		if (!$varsDefer) {
			$this->_sendOldData();
		}

		if ($varsDefer['vars']['flagPermitLost']
			|| $varsDefer['vars']['flagLostJournal']
		) {
			$this->_sendOldData();
		}

		$numLast = count($arr['varsLogDefer']['jsonVersion']) - 1;

		$flagVarsCash = $this->_checkFlagVarsCash(array(
			'varsVersionLast' => $arr['varsLogDefer']['jsonVersion'][$numLast],
			'varsItem'        => $arr['vars']['varsRule'],
		));

		if (!$flagVarsCash['flagCash']) {
			$this->_sendOldData();
		}

		return $flagVarsCash['flagIn'];
	}

	/**
		(array(
			'classCalcLog' => $classCalcLog,
			'varsLogDefer' => end($rows['arrRows']),
		))
	 */
	protected function _setPaid($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $classCheck;
		global $classEscape;

		global $varsRequest;
		global $varsAccount;
		global $varsPluginAccountingAccount;

		$classCalcLog = &$arr['classCalcLog'];
		$varsLogDefer = $arr['varsLogDefer'];
		$arrVarsLogAdd = array();

		$stampRegister = TIMESTAMP;
		$stampUpdate = TIMESTAMP;
		$stampBook = $varsLogDefer['stampBook'];
		$idEntity = $varsLogDefer['idEntity'];
		$numFiscalPeriod = $varsLogDefer['numFiscalPeriod'];
		$idAccount = $varsLogDefer['idAccount'];
		$strTitle = $varsLogDefer['strTitle'];
		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $varsLogDefer['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));
		$arrCommaIdLogFile = '';
		$flagApply = ($varsLogDefer['flagApply'])? $varsLogDefer['flagApply'] : 0;
		$idAccountApply = ($varsLogDefer['idAccountApply'])? $varsLogDefer['idAccountApply'] : null;
		$arrCommaIdAccountPermit = ($varsLogDefer['arrCommaIdAccountPermit'])? $varsLogDefer['arrCommaIdAccountPermit'] : '';
		$arrPermitHistory = ($varsLogDefer['jsonPermitHistory'])? $varsLogDefer['jsonPermitHistory'] : array();
		$jsonPermitHistory = json_encode($arrPermitHistory);
		$flagPay = 1;
		$stampPay = TIMESTAMP;
		$arrChargeHistory = array(
			array(
				'stampRegister' => TIMESTAMP,
				'idAccount'     => $idAccount,
			),
		);
		$jsonChargeHistory = json_encode($arrChargeHistory);
		$flagIn = $arr['flagIn'];
		$varsVersionLast = end($varsLogDefer['jsonVersion']);
		$arrValue = array(
			'flagFiscalReport'  => '0',
			'stampRegister'     => $stampRegister,
			'stampUpdate'       => $stampUpdate,
			'stampBook'         => $stampBook,
			'strTitle'          => $strTitle,
			'jsonDetail'        => $varsVersionLast['jsonDetail'],
			'arrCommaIdLogFile' => '',
			'arrSpaceStrTag'    => $arrSpaceStrTag,
		);
		$varsVersion = $classCalcLog->allot(array(
			'flagStatus'      => 'varsVersion',
			'arrValue'        => $arrValue,
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrVersion = &$varsVersion['arrVersion'][0];
		$arrVersion['flagIn'] = $flagIn;
		$arrVersion['jsonPermitHistory'] = $arrPermitHistory;
		$jsonVersion = json_encode($varsVersion['arrVersion']);

		$numValue = $varsVersion['numValue'];

		$arrCommaIdDepartmentDebit = $varsVersion['arrCommaIdDepartmentDebit'];
		$arrCommaIdAccountTitleDebit = $varsVersion['arrCommaIdAccountTitleDebit'];
		$arrCommaIdSubAccountTitleDebit = $varsVersion['arrCommaIdSubAccountTitleDebit'];
		$arrCommaRateConsumptionTaxDebit = $varsVersion['arrCommaRateConsumptionTaxDebit'];
		$arrCommaConsumptionTaxDebit = $varsVersion['arrCommaConsumptionTaxDebit'];
		$arrCommaConsumptionTaxWithoutCalcDebit = $varsVersion['arrCommaConsumptionTaxWithoutCalcDebit'];
		$arrCommaTaxPaymentDebit = $varsVersion['arrCommaTaxPaymentDebit'];
		$arrCommaTaxReceiptDebit = $varsVersion['arrCommaTaxReceiptDebit'];

		$arrCommaIdDepartmentCredit = $varsVersion['arrCommaIdDepartmentCredit'];
		$arrCommaIdAccountTitleCredit = $varsVersion['arrCommaIdAccountTitleCredit'];
		$arrCommaIdSubAccountTitleCredit = $varsVersion['arrCommaIdSubAccountTitleCredit'];
		$arrCommaRateConsumptionTaxCredit = $varsVersion['arrCommaRateConsumptionTaxCredit'];
		$arrCommaConsumptionTaxCredit = $varsVersion['arrCommaConsumptionTaxCredit'];
		$arrCommaConsumptionTaxWithoutCalcCredit = $varsVersion['arrCommaConsumptionTaxWithoutCalcCredit'];
		$arrCommaTaxPaymentCredit = $varsVersion['arrCommaTaxPaymentCredit'];
		$arrCommaTaxReceiptCredit = $varsVersion['arrCommaTaxReceiptCredit'];

		$varsIdNumber = $this->_getIdAutoIncrement(array(
			'idTarget' => 'idLogCash'
		));
		if (!$varsIdNumber[$idEntity]) {
			$varsIdNumber[$idEntity] = 1;
		}
		$idLogCash = $varsIdNumber[$idEntity];

		$arrayTemp = compact(
			'stampRegister',
			'stampUpdate',
			'stampBook',
			'idLogCash',
			'idEntity',
			'numFiscalPeriod',
			'idAccount',
			'flagIn',
			'flagPay',
			'stampPay',
			'strTitle',
			'arrSpaceStrTag',
			'flagApply',
			'idAccountApply',
			'arrCommaIdAccountPermit',
			'arrCommaIdLogFile',
			'jsonVersion',
			'numValue',
			'arrCommaIdDepartmentDebit',
			'arrCommaIdAccountTitleDebit',
			'arrCommaIdSubAccountTitleDebit',
			'arrCommaConsumptionTaxDebit',
			'arrCommaRateConsumptionTaxDebit',
			'arrCommaConsumptionTaxWithoutCalcDebit',
			'arrCommaTaxPaymentDebit',
			'arrCommaTaxReceiptDebit',
			'arrCommaIdDepartmentCredit',
			'arrCommaIdAccountTitleCredit',
			'arrCommaIdSubAccountTitleCredit',
			'arrCommaConsumptionTaxCredit',
			'arrCommaRateConsumptionTaxCredit',
			'arrCommaConsumptionTaxWithoutCalcCredit',
			'arrCommaTaxPaymentCredit',
			'arrCommaTaxReceiptCredit',
			'jsonChargeHistory',
			'jsonPermitHistory'
		);
		$arrColumn = array();
		$arrValue = array();
		foreach ($arrayTemp as $keyTemp => $valueTemp) {
			$arrColumn[] = $keyTemp;
			$arrValue[] = $valueTemp;
		}

		$id = $classDb->insertRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingLogCash',
			'arrColumn' => $arrColumn,
			'arrValue'  => $arrValue,
		));

		$varsIdNumber[$idEntity]++;
		$this->_updateIdAutoIncrement(array(
			'idTarget'   => 'idLogCash',
			'varsTarget' => $varsIdNumber
		));

		$varsLog = $this->_getVarsLogCash(array('idTarget' => $idLogCash));
		$arrVarsLogAdd[] = $varsLog;

		$classCalcCash = $this->_getClassCalc(array('flagType' => 'Cash'));
		$flag = $classCalcCash->allot(array(
			'flagStatus'      => 'addDone',
			'arrRows'         => $arrVarsLogAdd,
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
		));

		if ($flag == 'errorDataMax') {
			$this->sendVars(array(
				'flag'    => $flag,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}

		$this->_updateDbPreferenceStamp(array('strColumn' => 'cash'));

		$data = array(
			'arrVarsLogAdd'    => $arrVarsLogAdd,
			'idLogDefer'       => $varsLogDefer['id'],
			'stampArrive'      => $varsLogDefer['stampArrive'],
			'arrSpaceStrTag'   => $varsLogDefer['arrSpaceStrTag'],
		);

		return $data;
	}

	/**
		(array(
			'varsVersion' => $varsVersion,
			'varsItem'    => $arr['varsItem'],
		))
	 */
	protected function _checkFlagVarsCash($arr)
	{
		$data = array(
			'flagCash' => 0,
			'flagIn'   => 0,
		);

		$varsVersionLast = $arr['varsVersionLast'];
		$varsDetail = end($varsVersionLast['jsonDetail']['varsDetail']);

		$idAccountTitleDebit = $varsDetail['arrDebit']['idAccountTitle'];
		$flagCashDebit = 0;
		if ($arr['varsItem']['varsPreference']['jsonCash'][$idAccountTitleDebit]) {
			$flagCashDebit = 1;
		}

		$idAccountTitleCredit = $varsDetail['arrCredit']['idAccountTitle'];
		$flagCashCredit = 0;
		if ($arr['varsItem']['varsPreference']['jsonCash'][$idAccountTitleCredit]) {
			$flagCashCredit = 1;
		}

		if (!(!$flagCashDebit && !$flagCashCredit)) {
			$data['flagCash'] = 1;
			if ($flagCashDebit && $flagCashCredit) {
				$data['flagIn'] = 2;

			} elseif ($flagCashDebit) {
				$data['flagIn'] = 1;

			} elseif ($flagCashCredit) {
				$data['flagIn'] = 0;
			}
		}

		return $data;
	}

	/**
	 *
	 */
	protected function _setDeleteDefer($arr)
	{
		global $varsPluginAccountingAccount;

		global $classDb;
		$dbh = $classDb->getHandle();

		$array = $arr['arrId'];
		foreach ($array as $key => $value) {
			$classDb->deleteRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingLogCashDefer',
				'flagAnd'   => 1,
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
						'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					),
					array(
						'flagType'      => 'num',
						'strColumn'     => 'id',
						'flagCondition' => 'eq',
						'value'         => $value,
					),
				),
			));
		}

		$array = array('logCashDefer');
		foreach ($array as $key => $value) {
			$this->_updateDbPreferenceStamp(array('strColumn' => $value));
		}
	}

	/**
		(array(
			'arrVarsLog'   => $tempData['arrVarsLogAdd'],
			'stampArrive'  => $tempData['stampArrive'],
			'vars'         => $vars,
			'classCalcLog' => $classCalcLog,
		))
	 */
	protected function _setWrite($arr)
	{
		$classCalcLog = &$arr['classCalcLog'];

		$data = $this->_getWriteLog(array(
			'arrVarsLog'     => $arr['arrVarsLog'],
			'stampArrive'    => $arr['stampArrive'],
			'arrSpaceStrTag' => $arr['arrSpaceStrTag'],
			'vars'           => $arr['vars'],
			'classCalcLog'   => $classCalcLog,
		));

		$tempVarsLog = $this->_setWriteLog(array(
			'arrOrder'     => $data['arrOrder'],
			'classCalcLog' => $classCalcLog,
		));

		$arr['vars'] = $this->_setWriteHistory(array(
			'vars'           => $arr['vars'],
			'arrVarsLog'     => $tempVarsLog,
			'arrVarsLogCash' => $data['arrOrderLog'],
		));

		$this->_setWriteHistoryBanks(array(
			'vars'           => $arr['vars'],
			'arrVarsLog'     => $tempVarsLog,
			'arrVarsLogCash' => $data['arrOrderLog'],
			'arrLogDefer'    => $arr['arrLogDefer'],
		));

		return $arr['vars'];
	}

	/**
		(array(
			'vars'           => $arr['vars'],
			'arrVarsLog'     => $tempVarsLog,
			'arrVarsLogCash' => $data['arrOrderLog'],
		))
	 */
	protected function _setWriteHistoryBanks($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsPluginAccountingAccount;
		global $varsAccount;

		$classCalcBanks = $this->_getClassCalc(array('flagType' => 'Banks'));
		$classCalcBanksImport = $this->_getClassCalc(array('flagType' => 'BanksImport'));

		$array = $arr['arrLogDefer'];
		foreach ($array as $key => $value) {
			if (!preg_match("/^banks/", $value['flagType'])) {
				continue;
			}
			$idLogBanks = $value['numRow'];
			$flag = $classCalcBanksImport->allot(array(
				'flagStatus'      => 'logImportWriteHistory',
				'classCalcBanks'  => $classCalcBanks,
				'idLogBanks'      => $idLogBanks,
				'varsLog'         => $arr['arrVarsLog'][$key],
				'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				'idAccount'       => $varsAccount['id'],
			));
			if ($flag == 'errorDataMax') {
				$this->sendVars(array(
					'flag'    => 'errorDataMax',
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));
				return;
			}
		}
	}

	/**
		(array(
			'vars'           => $arr['vars'],
			'arrVarsLog'     => $tempVarsLog,
			'arrVarsLogCash' => $data['arrOrderLog'],
		))
	 */
	protected function _setWriteHistory($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsAccount;
		global $varsPluginAccountingAccount;

		$arrColumn = array(
			'jsonWriteHistory',
		);

		$arrError = array();
		$array = $arr['arrVarsLogCash'];
		foreach ($array as $key => $value) {
			if (!$value['jsonWriteHistory']) {
				$arrWriteHistory = array();

			} else {
				$arrWriteHistory = $value['jsonWriteHistory'];
			}

			$arrWriteHistory[] = array(
				'stampRegister'   => TIMESTAMP,
				'idAccount'       => $varsAccount['id'],
				'idLog'           => $arr['arrVarsLog'][$key]['idLog'],
			);

			$jsonWriteHistory = json_encode($arrWriteHistory);

			$flag = $this->checkTextSize(array(
				'flag'       => 'errorDataMax',
				'str'        => $jsonVersion,
				'flagReturn' => 1,
			));
			if ($flag) {
				$this->sendVars(array(
					'flag'    => 'errorDataMax',
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));
				continue;
			}
			$arrValue = array($jsonWriteHistory);
			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingLogCash',
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
						'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					),
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idLogCash',
						'flagCondition' => 'eq',
						'value'         => $value['idLogCash'],
					),
				),
				'arrValue'  => $arrValue,
			));
		}

		return $arr['vars'];
	}

	/**
		array(
			'arrVarsLog'   => $arr['arrVarsLog'],
			'vars'         => $arr['vars'],
			'classCalcLog' => $classCalcLog,
		)
	 */
	protected function _getWriteLog($arr)
	{
		global $varsPluginAccountingAccount;
		global $varsAccount;

		global $classEscape;
		$classTime = new Code_Else_Lib_Time();

		$classCalcLog = &$arr['classCalcLog'];

		$arrOrder = array();
		$arrOrderLog = array();
		$array = $arr['arrVarsLog'];
		foreach ($array as $key => $value) {

			$classTime->setTimeZone(array('data' => $varsAccount['numTimeZone']));
			$strTime = $classTime->getDisplay(array(
				'stamp'    => $value['stampBook'],
				'flagType' => 'yearmin',
			));

			$arrSpaceStrTag = $value['arrSpaceStrTag'];
			$strAddTag = $arr['vars']['varsItem']['strTagTitle'];
			if ($value['flagIn'] == 1) {
				$strAddTag .= ' ' . $arr['vars']['varsItem']['strTagIn'];
			} elseif ($value['flagIn'] == 2) {
				$strAddTag .= ' ' . $arr['vars']['varsItem']['strTagMove'];
			} else {
				$strAddTag .= ' ' . $arr['vars']['varsItem']['strTagOut'];
			}
			if (!$arrSpaceStrTag) {
				$arrSpaceStrTag = $strAddTag;

			} else {
				$arrSpaceStrTag .= ' ' . $strAddTag;
			}
			$arrSpaceStrTag .= ' ' . $arr['arrSpaceStrTag'];

			$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrSpaceStrTag));
			$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));

			$varsDetail = end($value['jsonVersion']);
			$varsPermitHistory = end($value['jsonPermitHistory']);
			$varsOrder = array(
				'numFiscalPeriod'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				'idEntity'                => $varsPluginAccountingAccount['idEntityCurrent'],
				'idAccount'               => $value['idAccount'],
				'idAccountApply'          => $value['idAccountApply'],
				'flagFiscalReport'        => 'none',
				'stampBook'               => $strTime,
				'stampArrive'             => $arr['stampArrive'],
				'strTitle'                => $value['strTitle'],
				'jsonDetail'              => $varsDetail['jsonDetail'],
				'arrCommaIdLogFile'       => $value['arrCommaIdLogFile'],
				'arrCommaIdAccountPermit' => $value['arrCommaIdAccountPermit'],
				'numSumMax'               => $varsPermitHistory['numSumMax'],
				'arrSpaceStrTag'          => $arrSpaceStrTag,
			);

			$arrOrder[] = $varsOrder;
			$arrOrderLog[] = $value;
		}

		$data = array(
			'arrOrder'    => $arrOrder,
			'arrOrderLog' => $arrOrderLog,
		);

		return $data;
	}

	/**
		'arrOrder'     => $data['arrOrder'],
		'classCalcLog' => $classCalcLog,
	 */
	protected function _setWriteLog($arr)
	{
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;
		global $varsAccount;

		$classCalcLog = &$arr['classCalcLog'];

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriodTempNext = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];

		$flag = $classCalcLog->allot(array(
			'flagStatus'              => 'add',
			'arrOrder'                => $arr['arrOrder'],
			'idEntity'                => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagTempPrev'            => (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow))? 1 : 0,
			'numFiscalPeriodTempNext' => $numFiscalPeriodTempNext,
		));
		if ($flag == 'errorDataMax') {
			$this->sendVars(array(
				'flag'    => $flag,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));

		} else if (gettype($flag) != 'array') {
			$this->_sendOldData();
		}
		$arrVarsLog = $flag;

		return $arrVarsLog;
	}

	/**
		(array(
			'arrId' => array(),
		))
	 */
	protected function _sendOldData()
	{
		global $varsRequest;

		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
		$this->_setNaviSearch(array('flag' => 40));
	}

	/**
		(array(
			'arrId' => array(),
		))
	 */
	protected function _setPay($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;

		$classCalcCash = $this->_getClassCalc(array('flagType' => 'Cash'));

		$arrVarsLogAdd = $arr['arrVarsLogAdd'];
		$arrVarsLogDelete = $arr['arrVarsLogDelete'];

		$array = $arrVarsLogAdd;
		foreach ($array as $key => $value) {

			$flagPay = $value['flagPay'];
			$stampPay = $value['stampPay'];

			$arrayTemp = compact(
				'flagPay',
				'stampPay'
			);
			$arrColumn = array();
			$arrValue = array();
			foreach ($arrayTemp as $keyTemp => $valueTemp) {
				$arrColumn[] = $keyTemp;
				$arrValue[] = $valueTemp;
			}

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingLogCash',
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
						'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					),
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idLogCash',
						'flagCondition' => 'eq',
						'value'         => $value['idLogCash'],
					),
				),
				'arrValue'  => $arrValue,
			));
		}

		$classCalcCash = $this->_getClassCalc(array('flagType' => 'Cash'));

		$flag = $classCalcCash->allot(array(
			'flagStatus'      => 'addDone',
			'arrRows'         => $arrVarsLogAdd,
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
		));

		if ($flag == 'errorDataMax') {
			$this->sendVars(array(
				'flag'    => $flag,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}

		$flag = $classCalcCash->allot(array(
			'flagStatus'      => 'deletePre',
			'arrRows'         => $arrVarsLogDelete,
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
		));
		if ($flag == 'errorDataMax') {
			$this->sendVars(array(
				'flag'    => $flag,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}

		$this->_updateDbPreferenceStamp(array('strColumn' => 'cash'));
	}

	/**
		(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		))
	 */
	protected function _checkPay($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $classCheck;
		global $classEscape;

		global $varsRequest;
		global $varsAccount;
		global $varsPluginAccountingAccount;

		$classCalcLog = &$arr['classCalcLog'];
		$varsDefer = end($arr['vars']['portal']['varsNavi']['tree']['varsDetail']['varsDetail']);
		if (!$varsDefer) {
			$this->_sendOldData();
		}

		if ($varsDefer['vars']['flagPermitLost']
			|| $varsDefer['vars']['flagLostJournal']
		) {
			$this->_sendOldData();
		}

		$idLogCash = $varsRequest['query']['jsonValue']['vars']['IdLogCash'];
		$array = array($idLogCash);
		$flag = $classCheck->checkValueFormat(array(
			'flagType' => 'num',
			'flagArr'  => 1,
			'value'    => $array,
		));
		if ($flag) {
			$this->_sendOldData();
		}

		$flag = 0;
		$array = $varsDefer['vars']['arrayOptionIdLogCash'];
		foreach ($array as $key => $value) {
			if ($value['value'] == $idLogCash) {
				$flag = 1;
				continue;
			}
		}
		if (!$flag) {
			$this->_sendOldData();
		}

		$varsLog = $this->_getVarsLogCash(array('idTarget' => $idLogCash));
		$flagPermitLost = $this->_checkPermitLost(array(
			'classCalcLog' => $classCalcLog,
			'value'        => $varsLog,
		));
		if ($flagPermitLost) {
			$this->_sendOldData();
		}

		if ($arr['vars']['varsRule']['varsPreference']['flagPermitImport']) {
			$varsLog['flagApply'] = $varsDefer['vars']['flagApply'];
			$varsLog['arrCommaIdAccountPermit'] = $varsDefer['vars']['arrCommaIdAccountPermit'];
			if ($varsLog['flagApply']) {
				$idAccountApply = $varsLog['idAccountApply'];
				$arrPermitHistory = array();
				$dataPermitHistory = end($varsDefer['vars']['jsonPermitHistory']);
				$dataPermitHistory['idAccountApply'] = $idAccountApply;
				$arrPermitHistory[] = $dataPermitHistory;
				$varsLog['jsonPermitHistory'] = $arrPermitHistory;
			}
		}

		$arrVarsLogAdd = array();
		$arrVarsLogDelete = array();

		if ((int) $varsLog['flagPay']
			|| (int) $varsLog['flagRemove']
		) {
			$this->_sendOldData();
		}

		$arrVarsLogDelete[] = $varsLog;

		$varsLog['flagPay'] = 1;
		$varsLog['stampPay'] = TIMESTAMP;

		$arrVarsLogAdd[] = $varsLog;

		$data = array(
			'arrVarsLogAdd'    => $arrVarsLogAdd,
			'arrVarsLogDelete' => $arrVarsLogDelete,
			'idLogDefer'       => $varsDefer['id'],
			'stampArrive'      => $varsDefer['vars']['stampArrive'],
			'arrSpaceStrTag'   => $varsDefer['vars']['arrSpaceStrTag'],
		);

		return $data;
	}

	/**
	 * array(
	 *  'numLotNow' => int
	 * )
	 */
	protected function _getVarsLogCashDefer($arr)
	{
		global $classDb;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogCashDefer',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'   => 1,
			'arrWhere'  => array(
				array(
					'flagType'      => '',
					'strColumn'     => 'id',
					'flagCondition' => 'eq',
					'value'         => $arr['idTarget'],
				),
			),
		));

		return $rows;
	}

	/**
	 * array(
	 *  'numLotNow' => int
	 * )
	 */
	protected function _getVarsLogCash($arr)
	{
		global $varsPluginAccountingAccount;
		global $classDb;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogCash',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'   => 1,
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
					'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idLogCash',
					'flagCondition' => 'eq',
					'value'         => $arr['idTarget'],
				),
			),
		));

		return $rows['arrRows'][0];
	}





}
