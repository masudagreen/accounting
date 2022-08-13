<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_BalanceEditor extends Code_Else_Plugin_Accounting_Jpn_Balance
{
	protected $_childSelf = array(
		'pathTplJs'   => 'else/plugin/accounting/js/jpn/balanceEditor.js',
		'pathVarsJs'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/balanceEditor.php',
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
			'idDepartment' => $varsRequest['query']['jsonValue']['vars']['VarsFlag']['idDepartment'],
		);

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
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
			'varsFlag' => $varsFlag,
		));

		if (is_null($varsItem['varsDepartment']['arrStrTitle'][$varsFlag['idDepartment']])) {
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
/*
		var_dump($arrData);
exit;
*/
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
		$varsRequest['query']['jsonValue']['vars']['IdDepartment'] = $varsFlag['idDepartment'];
		$this->_setSearch();
	}

	/**
		(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'arrValue' => $arrValue,
			'varsFlag' => $varsFlag,
		))
	 */
	protected function _checkValueDetail($arr)
	{
		$arrData = array(
			'arrRowsAdd'    => array(),
			'arrRowsDelete' => array(),
		);
		$idTarget = 'profitBroughtForward';
		$arr['varsItem']['arrAccountTitle']['arrStrTitle'][$idTarget]['idAccountTitle'] = $idTarget;

		$this->_getValueList(array(
			'varsFS'     => $arr['vars']['portal']['varsList']['varsDetail'],
			'arrData'    => &$arrData,
			'varsFlag'   => $arr['varsFlag'],
			'arrValue'   => $arr['arrValue']['arr']['jsonData'],
			'varsContra' => $arr['varsItem']['arrAccountTitle']['arrStrTitle']['profitBroughtForward'],
		));

		return $arrData;
	}

	/**
		(array(
			'varsFS'   => $arr['vars']['portal']['varsList']['varsDetail'],
			'arrData'  => &$arrData,
			'varsFlag' => $arr['varsFlag'],
			'arrValue' => $arr['arrValue']['arr']['jsonData'],
		))

	 */
	protected function _getValueList($arr)
	{
		$array = &$arr['varsFS'];

		foreach ($array as $key => $value) {
			if (!is_null($value['vars']['varsValue']) && is_null($value['vars']['flagCalc'])) {

				$idAccountTitle = $value['vars']['idTarget'];
				$idDepartment = $arr['varsFlag']['idDepartment'];

				//numBalance
				$numBalanceNew = $arr['arrValue'][$idAccountTitle];
				if (is_null($numBalanceNew)) {
					$numBalanceNew = 0;
				}

				$flag = 0;
				if ($value['varsValue']['numBalance'] != $numBalanceNew) {
					$flag = 1;
				}
				if ($arr['varsContra']['idAccountTitle'] == $idAccountTitle) {
					$flag = 0;
				}
				if ($flag) {
					$numValue = $value['varsValue']['numBalance'];
					$flagDebit = (int) $value['vars']['flagDebit'];
					if ($numValue < 0) {
						$flagDebit = ($flagDebit)? 0 : 1;
						$numValue *= (-1);
					}

					//delete target
					$arr['arrData']['arrRowsDelete'][] = $this->_getValueListData(array(
						'flagDebit'      => $flagDebit,
						'idAccountTitle' => $idAccountTitle,
						'numValue'       => $numValue,
						'idDepartment'   => ($idDepartment == 0)? '' : $idDepartment,
						'vars'           => $value['vars'],
					));

					//delete contra
					$arr['arrData']['arrRowsDelete'][] = $this->_getValueListData(array(
						'flagDebit'      => ($flagDebit)? 0 : 1,
						'idAccountTitle' => $arr['varsContra']['idAccountTitle'],
						'numValue'       => $numValue,
						'idDepartment'   => ($idDepartment == 0)? '' : $idDepartment,
						'vars'           => $arr['varsContra'],
					));

					$numValue = $numBalanceNew;
					$flagDebit = (int) $value['vars']['flagDebit'];
					if ($numValue < 0) {
						$flagDebit = ($flagDebit)? 0 : 1;
						$numValue *= (-1);
					}

					//add target
					$arr['arrData']['arrRowsAdd'][] = $this->_getValueListData(array(
						'flagDebit'      => $flagDebit,
						'idAccountTitle' => $idAccountTitle,
						'numValue'       => $numValue,
						'idDepartment'   => ($idDepartment == 0)? '' : $idDepartment,
						'vars'           => $value['vars'],
					));

					//add contra
					$arr['arrData']['arrRowsAdd'][] = $this->_getValueListData(array(
						'flagDebit'      => ($flagDebit)? 0 : 1,
						'idAccountTitle' => $arr['varsContra']['idAccountTitle'],
						'numValue'       => $numValue,
						'idDepartment'   => ($idDepartment == 0)? '' : $idDepartment,
						'vars'           => $arr['varsContra'],
					));
				}
			}

			if ($value['child']) {
				$array[$key]['child'] = $this->_getValueList(array(
					'varsFS'     => $array[$key]['child'],
					'arrData'    => &$arr['arrData'],
					'varsFlag'   => $arr['varsFlag'],
					'arrValue'   => $arr['arrValue'],
					'varsContra' => $arr['varsContra'],
				));
			}
		}

		return $array;
	}

	/**
		(array(
			'varsFS'    => $arr['varsItem']['varsFS'][$strFS . 'BS'],
			'varsValue' => array(),
		));
	 */
	protected function _getValueListData($arr)
	{
		global $varsPluginAccountingAccount;

		$data = array(
			'idLog'                   => '',
			'stampRegister'           => '',
			'stampBook'               => '',
			'idEntity'                => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod'         => '',
			'idAccount'               => '',
			'strTitle'                => '',
			'flagFiscalReport'        => '',
			'flagDebit'               => $arr['flagDebit'],
			'idAccountTitle'          => $arr['idAccountTitle'],
			'idAccountTitleContra'    => '',
			'idDepartmentContra'      => '',
			'idSubAccountTitleContra' => '',
			'numValue'                => $arr['numValue'],
			'idDepartment'            => $arr['idDepartment'],
			'idSubAccountTitle'       => '',
		);
		$arrayData = $data;
		$arrColumn = array();
		$arrValue = array();
		$num = 0;
		foreach ($arrayData as $keyData => $valueData) {
			$arrColumn[$num] = $keyData;
			$arrValue[$num] = $valueData;
			$num++;
		}
		$data['flagConsumptionTaxGeneralRuleEach'] = '';
		$data['flagConsumptionTaxGeneralRuleProration'] = '';
		$data['flagConsumptionTaxSimpleRule'] = '';
		$data['flagConsumptionTaxWithoutCalc'] = '';
		$data['flagConsumptionTaxCalc'] = '';
		$data['arrDate'] = array();
		$data['flagDebitAccountTitle'] = $arr['vars']['flagDebit'];
		$data['flagFS'] = 'BS';
		$data['idAccountTitleJgaapFS'] = $arr['vars']['idAccountTitleJgaapFS'];
		$data['varsJgaapCS'] = $arr['vars']['varsJgaapCS'];
		$data['arrColumn'] = $arrColumn;
		$data['arrValue'] = $arrValue;

		return $data;
	}
}
