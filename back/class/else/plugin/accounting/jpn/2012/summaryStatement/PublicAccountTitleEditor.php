<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_2012_SummaryStatement_PublicAccountTitleEditor extends Code_Else_Plugin_Accounting_Jpn_2012_SummaryStatement_PublicAccountTitle
{
	protected $_childSelf = array(
		'pathTplJs' => 'else/plugin/accounting/js/jpn/2012/summaryStatement/publicAccountTitleEditor.js',
		'pathVarsJs'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/2012/summaryStatement/publicAccountTitleEditor.php',
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
		if (preg_match("/^(done|tempNext)$/", $flag)) {
			$this->_sendOld();
		}

		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$vars['varsFlag']['flagFS'] = $varsRequest['query']['jsonValue']['vars']['FlagFS'];

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsFlag' => $vars['varsFlag'],
		));

		$vars = $this->_updateVars(array(
			'varsFlag' => $vars['varsFlag'],
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$varsTarget = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail'],
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget
		));

		$this->_checkValueDetail(array(
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
			$this->_updateDbPreferenceStamp(array('strColumn' => 'summryStatement'));

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
		$flagReport = $this->_extSelf['flagReport'];
		$flagDetail = $this->_extSelf['flagDetail'];
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];

		$flagFS = $arr['arrValue']['arr']['flagFS'];
		$varsSave = $arr['varsItem']['varsSave'];

		$jsonJgaapAccountTitleBS = '';
		$jsonJgaapAccountTitlePL = '';
		$jsonJgaapAccountTitleCR = '';

		//update
		if ($varsSave) {
			$array = $arr['varsItem']['arrayFSList'];

			$arrColumn = array(
				'stampUpdate',
			);
			$arrValue = array(
				$stampUpdate,
			);

			foreach ($array as $key => $value) {
				if ($key != $flagFS) {
					continue;
				}
				if (!$varsSave['jsonJgaapAccountTitle' . $key]) {
					$varsSave['jsonJgaapAccountTitle' . $key] = array();
				}
				$data = $varsSave['jsonJgaapAccountTitle'.$key];
				$data[$arr['idTarget']]['flag7'] = $arr['arrValue']['arr']['flag7'];
				$data[$arr['idTarget']]['flag17'] = $arr['arrValue']['arr']['flag17'];
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
				'strTable' => 'accountingSummaryStatement' . $strNation,
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
				),
				'arrValue'  => $arrValue,
			));

		//insert
		} else {
			$data = array();
			$data[$arr['idTarget']]['flag7'] = $arr['arrValue']['arr']['flag7'];
			$data[$arr['idTarget']]['flag17'] = $arr['arrValue']['arr']['flag17'];
			if ($flagFS == 'PL') {
				$jsonJgaapAccountTitlePL = json_encode($data);

			} elseif ($flagFS == 'BS') {
				$jsonJgaapAccountTitleBS = json_encode($data);

			} elseif ($flagFS == 'CR') {
				$jsonJgaapAccountTitleCR = json_encode($data);
			}
			$arrColumn = array(
				'stampRegister',
				'stampUpdate',
				'idEntity',
				'numFiscalPeriod',
				'flagReport',
				'flagDetail',
				'jsonJgaapAccountTitlePL',
				'jsonJgaapAccountTitleBS',
				'jsonJgaapAccountTitleCR',
			);
			$arrValue = array(
				$stampRegister,
				$stampUpdate,
				$idEntity,
				$numFiscalPeriod,
				$flagReport,
				$flagDetail,
				$jsonJgaapAccountTitlePL,
				$jsonJgaapAccountTitleBS,
				$jsonJgaapAccountTitleCR,
			);

			$id = $classDb->insertRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingSummaryStatement' . $strNation,
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
	protected function _checkValueDetail($arr)
	{
		//flag7
		$arrayCheck = array();
		$array = $arr['varsItem']['varsCommon']['arrSelectTag']['7'];
		foreach ($array as $key => $value) {
			$arrayCheck[$value['value']] = 1;
		}
		if (!$arrayCheck[$arr['arrValue']['arr']['flag7']]) {
			$this->_sendOld();
		}

		//flag17
		$arrayCheck = array();
		$array = $arr['varsItem']['varsCommon']['arrSelectTag']['17'];
		foreach ($array as $key => $value) {
			$arrayCheck[$value['value']] = 1;
		}
		if (!$arrayCheck[$arr['arrValue']['arr']['flag17']]) {
			$this->_sendOld();
		}

		//flagFS
		$arrayCheck = array();
		$array = $arr['varsItem']['arrayFSList'];
		foreach ($array as $key => $value) {
			$arrayCheck[$key] = 1;
		}
		if (!$arrayCheck[$arr['arrValue']['arr']['flagFS']]) {
			$this->_sendOld();
		}

		//accounttitle
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
