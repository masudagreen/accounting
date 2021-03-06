<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_BreakEvenPointAccountTitleEditor extends Code_Else_Plugin_Accounting_Jpn_BreakEvenPointAccountTitle
{
	protected $_childSelf = array(
		'pathTplJs' => 'else/plugin/accounting/js/jpn/breakEvenPointAccountTitleEditor.js',
		'pathVarsJs'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/breakEvenPointAccountTitleEditor.php',
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

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'update',
			'idTarget'      => $this->_extSelf['idPreference'],
		));

		if (!$flag) {
			$this->_sendOld();
		}

		$flag = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done)$/", $flag)) {
			$this->_sendOld();
		}

		$varsFlag = array(
			'flagFS'       => $varsRequest['query']['jsonValue']['vars']['FlagFS'],
			'idDepartment' => $varsRequest['query']['jsonValue']['vars']['IdDepartment'],
		);

		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsTarget = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail'],
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget
		));

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsFlag' => array('idDepartment' => $arrValue['arr']['idDepartment']),
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$this->_checkValueDetail(array(
			'varsDetail' => $vars['portal']['varsNavi']['varsDetail'],
			'varsFlag'   => $varsFlag,
		));

		$this->_checkValueDetailMore(array(
			'arrValue'    => &$arrValue,
			'vars'        => $vars,
			'idTarget'    => $idTarget,
			'varsItem'    => $varsItem,
		));

		try {
			$dbh->beginTransaction();

			$this->_updateDb(array(
				'arrValue'    => &$arrValue,
				'vars'        => $vars,
				'idTarget'    => $idTarget,
				'varsItem'    => $varsItem,
			));
			$this->_updateDbPreferenceStamp(array('strColumn' => 'braekEvenPoint'));

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

	 */
	protected function _updateDb($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$stampRegister = TIMESTAMP;
		$stampUpdate = TIMESTAMP;
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];

		$flagFS = $arr['arrValue']['arr']['flagFS'];
		$idDepartment = $arr['arrValue']['arr']['idDepartment'];
		$varsSave = $arr['varsItem']['varsSave'];

		$jsonJgaapAccountTitlePL = '';
		$jsonJgaapAccountTitleCR = '';

		//update
		if ($varsSave) {
			$array = $arr['vars']['varsItem']['varsFS'];

			$arrColumn = array(
				'stampUpdate',
			);
			$arrValue = array(
				$stampUpdate,
			);

			foreach ($array as $key => $value) {
				if (!$arr['varsItem']['varsEntityNation']['flagCR'] && $key == 'CR') {
					continue;
				}
				if (!$varsSave['jsonJgaapAccountTitle' . $key]) {
					$varsSave['jsonJgaapAccountTitle' . $key] = array();
				}
				$data = $varsSave['jsonJgaapAccountTitle'.$key];
				$data[$arr['idTarget']]['flagType'] = $arr['arrValue']['arr']['flagType'];
				$jsonData = json_encode($data);
				$this->checkTextSize(array(
					'flag' => 'errorDataMax',
					'str'  => $jsonData,
				));
				$arrColumn[] = 'jsonJgaapAccountTitle' . $flagFS;
				$arrValue[] = $jsonData;
			}

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingBreakEvenPoint' . $strNation,
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
						'strColumn'     => 'idDepartment',
						'flagCondition' => 'eq',
						'value'         => $idDepartment,
					),
				),
				'arrValue'  => $arrValue,
			));

		//insert
		} else {
			$data = array();
			$data[$arr['idTarget']]['flagType'] = $arr['arrValue']['arr']['flagType'];
			if ($flagFS == 'PL') {
				$jsonJgaapAccountTitlePL = json_encode($data);

			} elseif ($flagFS == 'CR') {
				$jsonJgaapAccountTitleCR = json_encode($data);
			}
			$arrColumn = array(
				'stampRegister',
				'stampUpdate',
				'idEntity',
				'numFiscalPeriod',
				'idDepartment',
				'jsonJgaapAccountTitlePL',
				'jsonJgaapAccountTitleCR',
			);
			$arrValue = array(
				$stampRegister,
				$stampUpdate,
				$idEntity,
				$numFiscalPeriod,
				$idDepartment,
				$jsonJgaapAccountTitlePL,
				$jsonJgaapAccountTitleCR,
			);

			$id = $classDb->insertRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingBreakEvenPoint' . $strNation,
				'arrColumn' => $arrColumn,
				'arrValue'  => $arrValue,
			));
		}
	}

	/**
		(array(
			'arrValue'    => &$arrValue,
			'vars'        => $vars,
			'idTarget'    => $idTarget,
			'varsItem'    => $varsItem,
		))
	 */
	protected function _checkValueDetailMore($arr)
	{
		global $varsPluginAccountingAccount;

		if (!$arr['varsItem']['varsFlagType']['arrStrTitle'][$arr['arrValue']['arr']['flagType']]) {
			$this->_sendOld();
		}

		$flag = $this->_checkTreeBlock(array(
			'vars'     => $arr['varsItem']['varsFS']['jsonJgaapAccountTitle'. $arr['arrValue']['arr']['flagFS']],
			'idTarget' => $arr['idTarget'],
		));

		if (!$flag) {
			$this->_sendOld();
		}
	}

	/**
		(array(

		))
	 */
	protected function _checkTreeBlock($arr)
	{
		$array = &$arr['vars'];
		foreach ($array as $key => $value) {
			if (!is_null($value['vars']['varsValue']) && $value['flagBtnUse']) {
				if (!($value['vars']['flagCalc'] == 'sum' || $value['vars']['flagCalc'] == 'net')) {
					if ($value['vars']['idTarget'] == $arr['idTarget']) {
						return 1;
					}
				}
			}
			if ($value['child']) {
				$flag = $this->_checkTreeBlock(array(
					'vars'     => $array[$key]['child'],
					'idTarget' => $arr['idTarget'],
				));
				if ($flag) {
					return $flag;
				}
			}
		}

	}
}
