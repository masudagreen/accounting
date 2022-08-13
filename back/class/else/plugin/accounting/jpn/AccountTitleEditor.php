<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_AccountTitleEditor extends Code_Else_Plugin_Accounting_Jpn_AccountTitle
{
	protected $_childSelf = array(
		'pathTplJs'      => 'else/plugin/accounting/js/jpn/accountTitleEditor.js',
		'pathTplJsTemp'  => 'else/plugin/accounting/js/jpn/accountTitleEditorTemp.js',
		'pathVarsJs'     => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/accountTitleEditor.php',
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
		global $varsRequest;

		$pathTpl = $this->_childSelf['pathTplJs'];
		if (ucwords($varsRequest['query']['child']) == 'EditorTemp') {
			$pathTpl = $this->_childSelf['pathTplJsTemp'];
		}

		$this->_setJsEditor(array(
			'pathVars'  => $this->_childSelf['pathVarsJs'],
			'pathTpl'   => $pathTpl,
			'arrFolder' => array(),
		));
	}

	/**

	 */
	protected function _iniDetailAdd()
	{
		$this->_iniDetailEdit();
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

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());

		if ($varsRequest['query']['func'] == 'DetailAdd') {
			$flag = $this->_checkAccess(array(
				'flagAllUse'    => 1,
				'flagAuthority' => 'insert',
				'idTarget'      => $this->_extSelf['idPreference'],
			));

		} else {
			$flag = $this->_checkAccess(array(
				'flagAllUse'    => 1,
				'flagAuthority' => 'update',
				'idTarget'      => $this->_extSelf['idPreference'],
			));
		}

		if (!$flag) {
			$this->_sendOld();
		}

		$varsRequest['query']['jsonValue']['vars']['StrTitle'] = $classEscape->toComma(array(
			'data' => $varsRequest['query']['jsonValue']['vars']['StrTitle']
		));

		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];
		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$flagFS = $this->_checkValueFS(array(
			'vars' => $vars,
		));


		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
			'flag' => $flagFS,
		));

		if (preg_match("/^(tempNext)$/", $flagCurrentFlagNow)
			&& $varsRequest['query']['func'] == 'DetailEdit'
		) {
			$varsItem = $this->_updateVarsItem(array(
				'flagFS'   => $flagFS,
				'varsItem' => $varsItem,
			));
		}

		$vars = $this->_updateVars(array(
			'flag'     => $flagFS,
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$flagDefault = $this->_checkValueDetailDefault(array(
			'vars'     => $vars['portal']['varsList']['varsDetail'],
			'idTarget' => $idTarget,
		));

		$varsTarget = $this->_setValueTemplate(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail'],
		));

		$varsTarget = $this->getValue(array(
			'vars' => $varsTarget,
		));

		$str = $varsRequest['query']['jsonValue']['vars']['StrAccountTitleJgaapFS'];
		if (!$varsItem['varsJgaapFS']['arrStrTitle'][$str]) {
			$this->_sendOld();
		}

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget
		));

		if ($varsRequest['query']['func'] == 'DetailAdd') {
			$flagEditCheck = $this->_checkValueDetailAdd(array(
				'arrValue'    => &$arrValue,
				'varsItem'    => $varsItem,
				'flagFS'      => $flagFS,
				'vars'        => $vars,
				'idTarget'    => $idTarget,
				'flagDefault' => $flagDefault,
			));

		} else {
			$flagEditCheck = $this->_checkValueDetailEdit(array(
				'arrValue'    => &$arrValue,
				'varsItem'    => $varsItem,
				'flagFS'      => $flagFS,
				'vars'        => $vars,
				'idTarget'    => $idTarget,
				'flagDefault' => $flagDefault,
			));
		}

		$varsFS = $this->_setValueDetailDb(array(
			'arrValue'    => $arrValue,
			'varsItem'    => $varsItem,
			'flagFS'      => $flagFS,
			'vars'        => $vars,
			'idTarget'    => $idTarget,
			'flagDefault' => $flagDefault,
		));

		$jsonAccountTitle = json_encode($varsFS);
		$this->checkTextSize(array(
			'flag' => 'errorDataMax',
			'str'  => $jsonAccountTitle,
		));
		$strAccountTitle = 'jsonJgaapAccountTitle'. $flagFS;

		$arrDbColumn = array($strAccountTitle);
		$arrDbValue = array($jsonAccountTitle);

		try {
			$dbh->beginTransaction();

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingFS' . $strNation,
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

			if ($flagEditCheck['idAccountTitle']
				|| $flagEditCheck['flagDebit']
				|| $flagEditCheck['idAccountTitleJgaapFSInsert']
				|| $flagEditCheck['idAccountTitleJgaapFSUpdate']
			) {
				$this->_setValueDetailDbEdit(array(
					'arrValue'      => $arrValue,
					'flagFS'        => $flagFS,
					'idTarget'      => $idTarget,
					'flagEditCheck' => $flagEditCheck,
				));
			}

			if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
				$classCalcTempNextAccountTitle = $this->_getClassCalc(array(
					'flagType'   => 'TempNext',
					'flagDetail' => 'AccountTitle',
				));
				$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
				$numFiscalPeriod = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];

				if ($varsRequest['query']['func'] == 'DetailAdd') {
					$flagStatus = 'add';

				} else {
					$flagStatus = 'edit';
				}
				$flag = $classCalcTempNextAccountTitle->allot(array(
					'flagStatus'      => $flagStatus,
					'numFiscalPeriod' => $numFiscalPeriod,
					'flagFS'          => $flagFS,
					'idTarget'        => $idTarget,
					'arrValue' => array(
						'strTitle'                      => $arrValue['arr']['strTitle'],
						'idAccountTitle'                => $arrValue['arr']['idAccountTitle'],
						'flagDebit'                     => $arrValue['arr']['flagDebit'],
						'flagConsumptionTaxGeneralRule' => $arrValue['arr']['flagConsumptionTaxGeneralRule'],
						'flagConsumptionTaxSimpleRule'  => $arrValue['arr']['flagConsumptionTaxSimpleRule'],
						'arrCommaIdAccountTitle'        => $arrValue['arr']['arrCommaIdAccountTitle'],
						'flagUse'                       => $arrValue['arr']['flagUse'],
						'idAccountTitleJgaapFS'         => $arrValue['arr']['strAccountTitleJgaapFS'],
					),
				));

				if ($flag) {
					if ($flag == 'arrayblock' || $flag == 'flagSortUse' || $flag == 'consumptionTax') {
						$this->_sendOld();
					}
					$this->sendVars(array(
						'flag'    => $flag,
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(),
					));
				}
			}

			$this->_updateDbPreferenceStamp(array('strColumn' => 'fS'));
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
			'flagFS'   => $flagFS,
			'varsItem' => $varsItem,
		))
	 */
	protected function _updateVarsItem($arr)
	{
		global $varsPluginAccountingAccount;

		$numFiscalPeriodTempPrev = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] - 1;
		$arrAccountTitleTempPrev = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => array(),
			'numFiscalPeriod'    => $numFiscalPeriodTempPrev,
		));

		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $numFiscalPeriodTempPrev,
		));

		$varsJgaapFSTempPrev = $this->_getVarsItemJgaapFS(array(
			'arrStrTitle'  => array(),
			'arrSelectTag' => array(),
			'vars'         => $varsFS['jsonJgaapFS' . $arr['flagFS']],
		));

		$arr['varsItem']['flagTempNext'] = 1;
		$arr['varsItem']['arrAccountTitleTempPrev'] = $arrAccountTitleTempPrev;
		$arr['varsItem']['varsJgaapFSTempPrev'] = $varsJgaapFSTempPrev;

		return $arr['varsItem'];

	}

	/**
		(array(
			'vars'     => $arr['vars']['portal']['varsList']['varsDetail'],
			'idTarget' => $idTarget,
		))

	 */
	protected function _checkValueDetailDefault($arr)
	{
		global $varsRequest;

		$array = &$arr['vars'];

		foreach ($array as $key => $value) {
			if (!is_null($value['vars']['flagUse'])) {
				if ($value['vars']['idTarget'] == $arr['idTarget']
					&& $value['flagDefault']
				) {
					return 1;
				}

			}
			if ($value['child']) {
				$flag = $this->_checkValueDetailDefault(array(
					'vars'     => $array[$key]['child'],
					'idTarget' => $arr['idTarget'],
				));
				if ($flag) {
					return 1;
				}
			}
		}

		return 0;
	}

	/**
		(array(
			'vars'        => $vars,
		))
	 */
	public function _setValueTemplate($arr)
	{
		global $varsRequest;

		$arrayNew = array();
		$array = $arr['vars'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'StrTitle'
				|| $value['id'] == 'IdAccountTitle'
				|| $value['id'] == 'FlagDebit'
				|| $value['id'] == 'FlagConsumptionTaxGeneralRule'
				|| $value['id'] == 'FlagConsumptionTaxSimpleRule'
				|| $value['id'] == 'ArrCommaIdAccountTitle'
				|| $value['id'] == 'FlagUse'
				|| $value['id'] == 'StrAccountTitleJgaapFS'
			) {
				$arrayNew[] = $value;

			}
		}

		return $arrayNew;
	}

	/**
		(array(
			'arrValue'    => $arrValue,
			'varsItem'    => $varsItem,
			'flagFS'      => $flagFS,
			'vars'        => $vars,
			'idTarget'    => $idTarget,
			'flagDefault' => $flagDefault,
		))
	 */
	protected function _checkValueDetailAdd($arr)
	{
		global $classEscape;
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varAccount;
		global $varsRequest;

		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$arrayBlock = $this->_getTreeBlock(array(
			'vars'     => $arr['varsItem']['varsFS']['jsonJgaapAccountTitle'. $arr['flagFS']],
			'idTarget' => $arr['idTarget'],
		));
		if (!$arrayBlock) {
			$this->_sendOld();
		}

		$varsTarget = array();
		foreach ($arrayBlock as $key => $value) {
			if ($value['vars']['idTarget'] == $arr['idTarget']) {
				$varsTarget = $value;
				break;
			}
		}
		if (!(int) $varsTarget['vars']['flagSortUse']) {
			$this->_sendOld();
		}


		$strTitle = $arr['arrValue']['arr']['strTitle'];
		$arrayRows = $arr['varsItem']['varsFSRows'];
		foreach ($arrayRows as $key => $value) {
			$flagNum = $this->_checkTreeStrTitle(array(
				'vars'      => $value['jsonJgaapAccountTitle'. $arr['flagFS']],
				'strTarget' => $strTitle,
				'num'       => 0,
			));
			if ($flagNum) {
				$this->sendVars(array(
					'flag'    => 'strTitle',
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));
			}
		}

		$idAccountTitle = $arr['arrValue']['arr']['idAccountTitle'];
		$arrayIdAccountTitle = array($idAccountTitle);
		$arrayFSList = $arr['varsItem']['arrayFSList'];
		foreach ($arrayFSList as $keyFSList => $valueFSList) {
			$arrayIdAccountTitle[] = 'custom_' . $keyFSList . '_' . $idAccountTitle;
		}

		foreach ($arrayIdAccountTitle as $key => $value) {
			$this->_checkValueDetailIdAccountTitle(array(
				'flagFS'    => $arr['flagFS'],
				'strTarget' => $value,
			));
		}

		$arrayCheck = array();
		foreach ($arrayBlock as $key => $value) {
			$arrayCheck[$value['vars']['idTarget']] = $value;
		}
		$arrCommaIdAccountTitle = $classEscape->splitCommaArrayData(array('data' => $arr['arrValue']['arr']['arrCommaIdAccountTitle']));
		$array = $arrCommaIdAccountTitle;
		$num = 0;
		foreach ($array as $key => $value) {
			if ($value == 'insertPoint' && $varsRequest['query']['func'] == 'DetailAdd') {
				continue;

			} else {
				if (!$arrayCheck[$value]) {
					$this->_sendOld();
				}
				$num++;
			}
		}
		if ($num != count($arrayCheck)) {
			$this->_sendOld();
		}
	}

	/**
		(array(
			'arrValue'    => $arrValue,
			'varsItem'    => $varsItem,
			'flagFS'      => $flagFS,
			'vars'        => $vars,
			'idTarget'    => $idTarget,
			'flagDefault' => $flagDefault,
		))
	 */
	protected function _checkValueDetailEdit($arr)
	{
		global $classEscape;
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varAccount;
		global $varsRequest;

		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;
		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());

		$numFiscalPeriodTemp = '';
		if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
			$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] + 1;

		} elseif (preg_match("/^(tempNext)$/", $flagCurrentFlagNow)) {
			$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] - 1;
		}

		$classCalcCash = $this->_getClassCalc(array('flagType' => 'Cash'));
		$varsCash = $classCalcCash->allot(array(
			'flagStatus'      => 'varsPreference',
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
		));
		$varsCashTemp = array();
		if ($numFiscalPeriodTemp) {
			$varsCashTemp = $classCalcCash->allot(array(
				'flagStatus'      => 'varsPreference',
				'numFiscalPeriod' => $numFiscalPeriodTemp,
				'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			));
		}

		$varsFS = $arr['varsItem']['varsFS']['jsonJgaapAccountTitle'. $arr['flagFS']];
		$varsFS = $this->_getFlagUseLog(array(
			'vars'                  => $varsFS,
			'idTarget'              => $arr['idTarget'],
			'numFiscalPeriod'       => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'numFiscalPeriodTemp'   => $numFiscalPeriodTemp,
			'varsCash'              => $varsCash['jsonCash'],
			'varsCashTemp'          => $varsCashTemp['jsonCash'],
			'classCalcAccountTitle' => $this->_getClassCalc(array('flagType' => 'AccountTitle')),
			'flagFS'                => $arr['flagFS'],
		));

		$arrayBlock = $this->_getTreeBlock(array(
			'vars'     => $varsFS,
			'idTarget' => $arr['idTarget'],
			'arrNew'   => array(),
		));
		if (!$arrayBlock) {
			$this->_sendOld();
		}

		$varsTarget = array();
		foreach ($arrayBlock as $key => $value) {
			if ($value['vars']['idTarget'] == $arr['idTarget']) {
				$varsTarget = $value;
				break;
			}
		}

		$strTitle = $arr['arrValue']['arr']['strTitle'];
		$arrayRows = $arr['varsItem']['varsFSRows'];
		foreach ($arrayRows as $key => $value) {
			$flagNum = $this->_checkTreeStrTitle(array(
				'vars'      => $value['jsonJgaapAccountTitle'. $arr['flagFS']],
				'strTarget' => $strTitle,
				'num'       => 0,
				'strSelf'   => $varsTarget['strTitle'],
				'idSelf'    => $varsTarget['vars']['idTarget'],
			));
			if ($flagNum) {
				$this->sendVars(array(
					'flag'    => 'strTitle',
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));
			}
		}

		$idAccountTitle = $arr['arrValue']['arr']['idAccountTitle'];
		$flag = 0;
		if ((int) $varsTarget['flagDefault']) {
			if ($idAccountTitle != $varsTarget['vars']['idTarget']) {
				$flag = 1;
			}

		} else {
			$idAccountTitleCustom = 'custom_' . $arr['flagFS'] . '_' . $idAccountTitle;
			if ($idAccountTitleCustom != $varsTarget['vars']['idTarget']) {
				$flag = 1;
			}
		}

		$flagEdit = array();

		//idAccountTitle
		if ($flag) {
			if ((int) $varsTarget['flagDefault']) {
				$this->_sendOld();

			} elseif ((int) $varsTarget['vars']['flagUseCash']
				|| (int) $varsTarget['vars']['flagUseCashTemp']
			) {
				$this->_sendOld();
			}
			$arrayIdAccountTitle = array($idAccountTitle);
			$arrayFSList = $arr['varsItem']['arrayFSList'];
			foreach ($arrayFSList as $keyFSList => $valueFSList) {
				$arrayIdAccountTitle[] = 'custom_' . $keyFSList . '_' . $idAccountTitle;
			}
			foreach ($arrayIdAccountTitle as $key => $value) {
				$this->_checkValueDetailIdAccountTitle(array(
					'flagFS'    => $arr['flagFS'],
					'strTarget' => $value,
					'strSelf'   => $arr['idTarget'],
				));
			}
			if ($varsTarget['vars']['flagUseAllLog']) {
				$this->_sendOld();
			}
			$flagEdit['idAccountTitle'] = 1;
		}

		//flagDebit
		$flagDebit = $arr['arrValue']['arr']['flagDebit'];
		if ($flagDebit != $varsTarget['vars']['flagDebit']) {
			if ((int) $varsTarget['flagDefault']) {
				$this->_sendOld();
			}
			if ($varsTarget['vars']['flagUseAllLogElse']) {
				$this->_sendOld();
			}
			$flagEdit['flagDebit'] = 1;
		}


		//$flagUse = $arr['arrValue']['arr']['flagUse'];

		$idAccountTitleJgaapFS = $arr['arrValue']['arr']['strAccountTitleJgaapFS'];
		if ($idAccountTitleJgaapFS != $varsTarget['vars']['idAccountTitleJgaapFS']) {
			if ($varsTarget['vars']['flagUseLogElse']
				|| ($varsTarget['vars']['flagUseLogElseTemp'] && $varsTarget['vars']['flagFS'] == 'BS')
			) {
				$this->_sendOld();
			}
			if ($idAccountTitle == 'profitBroughtForward'
				 || $idAccountTitle == 'suspenseReceiptOfConsumptionTaxes'
				 || $idAccountTitle == 'suspensePaymentConsumptionTaxes'
			) {
				$this->_sendOld();
			}
			$flagEdit['idAccountTitleJgaapFS'] = 1;
		}

		$arrayCheck = array();
		foreach ($arrayBlock as $key => $value) {
			$arrayCheck[$value['vars']['idTarget']] = 1;
		}

		$arrCommaIdAccountTitle = $classEscape->splitCommaArrayData(array('data' => $arr['arrValue']['arr']['arrCommaIdAccountTitle']));
		$array = $arrCommaIdAccountTitle;
		$num = 0;

		foreach ($array as $key => $value) {
			if (!$arrayCheck[$value]) {
				$this->_sendOld();
			}
			$num++;
		}

		if ($num != count($arrayCheck)) {
			$this->_sendOld();
		}

		if ($arr['varsItem']['flagTempNext']) {
			$idTarget = $arr['idTarget'];
			$varsTempPrev = $arr['varsItem']['arrAccountTitleTempPrev']['arrStrTitle'][$idTarget];

			if (!$varsTempPrev) {
				return $flagEdit;
			}
			if (!$arr['varsItem']['varsJgaapFSTempPrev']['arrStrTitle'][$arr['arrValue']['arr']['strAccountTitleJgaapFS']]) {
				$flagEdit['idAccountTitleJgaapFSInsert'] = 1;
			}

			if ($varsTempPrev['idAccountTitleJgaapFS'] != $arr['arrValue']['arr']['strAccountTitleJgaapFS']) {
				$flagEdit['idAccountTitleJgaapFSUpdate'] = 1;
			}
		}

		return $flagEdit;
	}



	/**
		(array(
			'arrValue'      => $arrValue,
			'flagFS'        => $flagFS,
			'idTarget'      => $idTarget,
			'flagEditCheck' => $flagEditCheck,
		))

	 */
	protected function _setValueDetailDbEdit($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $varsAccount;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		//x < numFiscalPeriodCurrent
		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingFS' . $strNation,
			'arrLimit' => array(),
			'arrOrder'  => array(
				'strColumn' => 'numFiscalPeriod',
				'flagDesc'  => 1,
			),
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
					'flagCondition' => 'small',
					'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				),
			),
		));

		$idAccountTitle = $arr['arrValue']['arr']['idAccountTitle'];
		$idAccountTitleCustom = 'custom_' . $arr['flagFS'] . '_' . $idAccountTitle;

		$array = $rows['arrRows'];
		foreach ($array as $key => $value) {
			if (!$value['jsonJgaapAccountTitle'. $arr['flagFS']]) {
				continue;
			}

			$varsFS = $this->_setValueDetailDbEditLoop(array(
				'idValue'       => $idAccountTitleCustom,
				'arrValue'      => $arr['arrValue'],
				'vars'          => $value['jsonJgaapAccountTitle'. $arr['flagFS']],
				'idTarget'      => $arr['idTarget'],
				'flagEditCheck' => $arr['flagEditCheck'],
			));

			//$numFiscalPeriodTempPrev only
			$arr['flagEditCheck']['idAccountTitleJgaapFSUpdate'] = 0;

			$jsonAccountTitle = json_encode($varsFS);
			$this->checkTextSize(array(
				'flag' => 'errorDataMax',
				'str'  => $jsonAccountTitle,
			));
			$strAccountTitle = 'jsonJgaapAccountTitle'. $arr['flagFS'];

			$arrDbColumn = array($strAccountTitle);
			$arrDbValue = array($jsonAccountTitle);

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingFS' . $strNation,
				'arrColumn' => $arrDbColumn,
				'flagAnd'  => 1,
				'arrWhere' => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'id',
						'flagCondition' => 'eq',
						'value'         => $value['id'],
					),
				),
				'arrValue'  => $arrDbValue,
			));
		}

		if ($arr['flagEditCheck']['idAccountTitleJgaapFSInsert']) {
			$classCalcTempNextAccountTitleFS = $this->_getClassCalc(array(
				'flagType'   => 'TempNext',
				'flagDetail' => 'AccountTitleFS',
			));
			$flag = $classCalcTempNextAccountTitleFS->allot(array(
				'flagStatus'      => 'back',
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				'flagFS'          => $arr['flagFS'],
				'idTarget'        => $arr['arrValue']['arr']['strAccountTitleJgaapFS'],
				'flagReverse'     => 1,
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

		//SubAccountTitle
		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingSubAccountTitle' . $strNation,
			'arrColumn' => array('idAccountTitle'),
			'flagAnd'  => 1,
			'arrWhere'  => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idEntity',
					'flagCondition' => 'eq',
					'value'         => $varsPluginAccountingAccount['idEntityCurrent'],
				),
				array(
					'flagType'      => '',
					'strColumn'     => 'idAccountTitle',
					'flagCondition' => 'eq',
					'value'         => $arr['idTarget'],
				),
			),
			'arrValue'  => array($idAccountTitleCustom),
		));

		//FixedAssets
		$classCalcFixedAssets = $this->_getClassCalc(array('flagType' => 'FixedAssets'));
		$classCalcFixedAssets->allot(array(
			'flagStatus'      => 'updateIdAccountTitle',
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idTargetOld'     => $arr['idTarget'],
			'idTargetNew'     => $idAccountTitleCustom,
		));
	}

	/**

	 */
	protected function _setValueDetailDbEditLoop($arr)
	{
		$array = &$arr['vars'];

		foreach ($array as $key => $value) {
			if (!is_null($value['vars']['flagUse'])) {
				if ($value['vars']['idTarget'] == $arr['idTarget']) {
					if ($arr['flagEditCheck']['idAccountTitle']) {
						$array[$key]['vars']['idTarget'] = $arr['idValue'];
					}
					if ($arr['flagEditCheck']['flagDebit']) {
						$array[$key]['vars']['flagDebit'] = (int) $arr['arrValue']['arr']['flagDebit'];
					}
					if ($arr['flagEditCheck']['idAccountTitleJgaapFSUpdate']) {
						$array[$key]['vars']['idAccountTitleJgaapFS'] = $arr['arrValue']['arr']['strAccountTitleJgaapFS'];
					}
				}
			}
			if ($value['child']) {
				$array[$key]['child'] = $this->_setValueDetailDbEditLoop(array(
					'vars'          => $array[$key]['child'],
					'idTarget'      => $arr['idTarget'],
					'flagEditCheck' => $arr['flagEditCheck'],
					'arrValue'      => $arr['arrValue'],
					'idValue'       => $arr['idValue'],
				));
			}
		}

		return $array;
	}

	/**
		(array(
			'arrValue'    => $arrValue,
			'varsItem'    => $varsItem,
			'flagFS'      => $flagFS,
			'vars'        => $vars,
			'idTarget'    => $idTarget,
			'flagDefault' => $flagDefault,
		))
	 */
	protected function _setValueDetailDb($arr)
	{
		global $classEscape;
		global $varsRequest;

		$idTarget = $arr['arrValue']['arr']['idAccountTitle'];
		if ($varsRequest['query']['func'] == 'DetailAdd'
			|| ($varsRequest['query']['func'] == 'DetailEdit' && !$arr['flagDefault'])
		) {
			$idTarget = 'custom_' . $arr['flagFS'] . '_' . $arr['arrValue']['arr']['idAccountTitle'];
		}

		$array = $this->_getTreeBlock(array(
			'vars'     => $arr['varsItem']['varsFS']['jsonJgaapAccountTitle'. $arr['flagFS']],
			'idTarget' => $arr['idTarget'],
			'arrNew'   => array(),
		));

		$varsTarget = array();
		if ($varsRequest['query']['func'] == 'DetailEdit') {
			foreach ($array as $key => $value) {
				if ($value['vars']['idTarget'] == $arr['idTarget']) {
					$varsTarget = $value;
					break;
				}
			}
		}

		$arrayCheck = array();
		foreach ($array as $key => $value) {
			$arrayCheck[$value['vars']['idTarget']] = $value;
		}

		$arrayIdAccountTitle = array();
		$arrayIdAccountTitle = $classEscape->splitCommaArrayData(array('data' => $arr['arrValue']['arr']['arrCommaIdAccountTitle']));

		$array = $arrayIdAccountTitle;
		$arrayNew = array();
		$tmplVars = $arr['varsItem']['varsFSItem']['varsAccountTitle'];
		foreach ($array as $key => $value) {
			if ($varsRequest['query']['func'] == 'DetailAdd') {

				$flagConsumptionTaxGeneralRuleEach = 'none';
				$flagConsumptionTaxGeneralRuleProration = 'none';
				$flagConsumptionTaxSimpleRule = 'none';

				if (!is_null($arr['arrValue']['arr']['flagConsumptionTaxGeneralRule'])) {
					if ($arr['varsItem']['varsEntityNation']['flagConsumptionTaxDeducted']) {
						$flagConsumptionTaxGeneralRuleEach = $arr['arrValue']['arr']['flagConsumptionTaxGeneralRule'];
						$flagConsumptionTaxGeneralRuleProration = $flagConsumptionTaxGeneralRuleEach;
						$flagConsumptionTaxSimpleRule = $flagConsumptionTaxGeneralRuleEach;
						$flagVars = $arr['varsItem']['varsConsumptionTax']['generalEachChange'][$flagConsumptionTaxGeneralRuleEach];
						if ($flagVars) {
							$flagConsumptionTaxGeneralRuleProration = $flagVars['generalProration'];
							$flagConsumptionTaxSimpleRule = $flagVars['simple'];
						}

					} else {
						$flagConsumptionTaxGeneralRuleProration = $arr['arrValue']['arr']['flagConsumptionTaxGeneralRule'];
						$flagConsumptionTaxGeneralRuleEach = $flagConsumptionTaxGeneralRuleProration;
						$flagConsumptionTaxSimpleRule = $flagConsumptionTaxGeneralRuleProration;
						$flagVars = $arr['varsItem']['varsConsumptionTax']['generalProrationChange'][$flagConsumptionTaxGeneralRuleProration];
						if ($flagVars) {
							$flagConsumptionTaxGeneralRuleEach = $flagVars['generalEach'];
							$flagConsumptionTaxSimpleRule = $flagVars['simple'];
						}
					}
					if ($flagConsumptionTaxSimpleRule == 'tax-unknown') {
						$flagConsumptionTaxSimpleRule = 'tax-Default';
					}
				}

				if (!is_null($arr['arrValue']['arr']['flagConsumptionTaxSimpleRule'])) {
					$flagConsumptionTaxSimpleRule = $arr['arrValue']['arr']['flagConsumptionTaxSimpleRule'];
					$flagConsumptionTaxGeneralRuleProration = $flagConsumptionTaxSimpleRule;
					$flagConsumptionTaxGeneralRuleEach = $flagConsumptionTaxSimpleRule;

					$flagVars = $arr['varsItem']['varsConsumptionTax']['simpleChange'][$flagConsumptionTaxSimpleRule];
					if ($flagVars) {
						$flagConsumptionTaxGeneralRuleProration = $flagVars['generalProration'];
						$flagConsumptionTaxGeneralRuleEach = $flagVars['generalEach'];
					}
					$flagConsumptionTaxBusinessType = $arr['varsItem']['varsEntityNation']['flagConsumptionTaxBusinessType'];
					$strTax = 'tax-' . $flagConsumptionTaxBusinessType;
					$strTaxBack = 'tax-Back-' . $flagConsumptionTaxBusinessType;
					if ($flagConsumptionTaxSimpleRule == $strTax) {
						$flagConsumptionTaxSimpleRule = 'tax-Default';

					} elseif ($flagConsumptionTaxSimpleRule == $strTaxBack) {
						$flagConsumptionTaxSimpleRule = 'tax-Back-Default';
					}
				}

				if ($value == 'insertPoint') {
					$tmplVars['strTitle'] = $arr['arrValue']['arr']['strTitle'];
					$tmplVars['vars'] = array(
						'idTarget'                               => $idTarget,
						'idAccountTitleJgaapFS'                  => $arr['arrValue']['arr']['strAccountTitleJgaapFS'],
						'flagUse'                                => (int) $arr['arrValue']['arr']['flagUse'],
						'flagDebit'                              => (int) $arr['arrValue']['arr']['flagDebit'],
						'flagConsumptionTaxGeneralRuleEach'      => $flagConsumptionTaxGeneralRuleEach,
						'flagConsumptionTaxGeneralRuleProration' => $flagConsumptionTaxGeneralRuleProration,
						'flagConsumptionTaxSimpleRule'           => $flagConsumptionTaxSimpleRule,
						'flagSortUse'                            => 1,
						'varsValue'                              => array(),
						'varsJgaapCS' => array(
							'varsDirect' => array(
								'idAccountTitleMinus' => 'none', 'flagMethodPlus' => '',
								'idAccountTitlePlus' => 'none', 'flagMethodMinus' => '',
							),
							'varsInDirect' => array(
								'idAccountTitleMinus' => 'none', 'flagMethodPlus' => '',
								'idAccountTitlePlus' => 'none', 'flagMethodMinus' => '',
							),
						),
					);
					if ($arr['varsItem']['varsEntityNation']['flagCorporation'] != 1) {
						$tmplVars['vars']['varsJgaapCS'] = array();
					}
					$arrayNew[] = $tmplVars;

				} else {
					$arrayNew[] = $arrayCheck[$value];
				}

			} else {
				if ($value == $arr['idTarget']) {
					$flagConsumptionTaxGeneralRuleEach = $varsTarget['vars']['flagConsumptionTaxGeneralRuleEach'];
					$flagConsumptionTaxGeneralRuleProration = $varsTarget['vars']['flagConsumptionTaxGeneralRuleProration'];
					if (!is_null($arr['arrValue']['arr']['flagConsumptionTaxGeneralRule'])) {
						if ($arr['varsItem']['varsEntityNation']['flagConsumptionTaxDeducted']) {
							$flagConsumptionTaxGeneralRuleEach = $arr['arrValue']['arr']['flagConsumptionTaxGeneralRule'];

						} else {
							$flagConsumptionTaxGeneralRuleProration = $arr['arrValue']['arr']['flagConsumptionTaxGeneralRule'];
						}
					}

					$flagConsumptionTaxSimpleRule = $varsTarget['vars']['flagConsumptionTaxSimpleRule'];
					if (!is_null($arr['arrValue']['arr']['flagConsumptionTaxSimpleRule'])) {
						$flagConsumptionTaxSimpleRule = $arr['arrValue']['arr']['flagConsumptionTaxSimpleRule'];
						$flagConsumptionTaxBusinessType = $arr['varsItem']['varsEntityNation']['flagConsumptionTaxBusinessType'];
						$strTax = 'tax-' . $flagConsumptionTaxBusinessType;
						$strTaxBack = 'tax-Back-' . $flagConsumptionTaxBusinessType;
						if ($flagConsumptionTaxSimpleRule == $strTax) {
							$flagConsumptionTaxSimpleRule = 'tax-Default';

						} elseif ($flagConsumptionTaxSimpleRule == $strTaxBack) {
							$flagConsumptionTaxSimpleRule = 'tax-Back-Default';
						}
					}

					$flagSortUse = (int) $varsTarget['vars']['flagSortUse'];
					$arrayCheck[$value]['strTitle'] = $arr['arrValue']['arr']['strTitle'];
					$arrayCheck[$value]['vars'] = array(
						'idTarget'                               => $idTarget,
						'idAccountTitleJgaapFS'                  => $arr['arrValue']['arr']['strAccountTitleJgaapFS'],
						'flagUse'                                => (int) $arr['arrValue']['arr']['flagUse'],
						'flagDebit'                              => (int) $arr['arrValue']['arr']['flagDebit'],
						'flagConsumptionTaxGeneralRuleEach'      => $flagConsumptionTaxGeneralRuleEach,
						'flagConsumptionTaxGeneralRuleProration' => $flagConsumptionTaxGeneralRuleProration,
						'flagConsumptionTaxSimpleRule'           => $flagConsumptionTaxSimpleRule,
						'flagSortUse'                            => $flagSortUse,
						'varsValue'                              => array(),
						'varsJgaapCS'                            => $arrayCheck[$value]['vars']['varsJgaapCS'],
					);
					if ($arr['varsItem']['varsEntityNation']['flagCorporation'] != 1) {
						$tmplVars['vars']['varsJgaapCS'] = array();
					}
					$arrayNew[] = $arrayCheck[$value];

				} else {
					$arrayNew[] = $arrayCheck[$value];
				}
			}
		}

		$varsFS = $this->_insertTreeBlock(array(
			'vars'       => $arr['varsItem']['varsFS']['jsonJgaapAccountTitle'. $arr['flagFS']],
			'idTarget'   => $arr['idTarget'],
			'varsTarget' => $arrayNew,
		));

		return $varsFS;
	}


	/**
		(array(

		))
	 */
	protected function _getIdAccountTitle($arr)
	{
		$arrayNew = &$arr['arrNew'];
		$array = &$arr['vars'];

		foreach ($array as $key => $value) {
			$arr['arrNew'][$value['vars']['idTarget']] = 1;

			if ($value['child']) {
				$data = $this->_getIdAccountTitle(array(
					'vars'   => $array[$key]['child'],
					'arrNew' => $arrayNew,
				));
				$arrayNew =  $data['arrNew'];
			}
		}

		return $arr;
	}

	/**
		(array(
			'vars'       => array(),
			'idTarget'   => array(),
			'varsTarget' => array(),
		));
	 */
	protected function _insertTreeBlock($arr)
	{
		$array = &$arr['vars'];
		foreach ($array as $key => $value) {
			if ($value['vars']['idTarget'] == $arr['idTarget']) {
				$array = $arr['varsTarget'];
				break;
			}
			if ($value['child']) {
				$array[$key]['child'] = $this->_insertTreeBlock(array(
					'vars'       => $array[$key]['child'],
					'idTarget'   => $arr['idTarget'],
					'varsTarget' => $arr['varsTarget'],
				));

			}
		}

		return $array;
	}

	/**
		(array(

		))

	 */
	protected function _checkValueDetailIdAccountTitle($arr)
	{
		$this->_checkValueDetailIdAccountTitleFS($arr);
		$this->_checkValueDetailIdAccountTitleFSId($arr);
	}

	/**
		(array(

		))

	 */
	protected function _checkValueDetailIdAccountTitleFS($arr)
	{
		global $classDb;
		global $varsAccount;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingFS' . $strNation,
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
					'flagCondition' => 'eqSmall',
					'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				),
			),
		));

		$varsFSItem = $this->_getVarsFSItem();
		$arrayFS = $varsFSItem['varsItem']['arrayFS'];
		$array = $rows['arrRows'];
		foreach ($array as $key => $value) {
			foreach ($arrayFS as $keyFS => $valueFS) {
				if (!$value['jsonJgaapAccountTitle'. $keyFS]) {
					continue;
				}
				$flag = $this->_checkValueDetailIdAccountTitleFSLoop(array(
					'vars'      => $value['jsonJgaapAccountTitle'. $keyFS],
					'strTarget' => $arr['strTarget'],
					'num'       => 0,
					'strSelf'   => $arr['strSelf'],
				));

				if ($flag) {
					$this->sendVars(array(
						'flag'    => 'idAccountTitle',
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(),
					));
				}

			}

		}
	}

	protected function _checkValueDetailIdAccountTitleFSLoop($arr)
	{
		$array = &$arr['vars'];
		$num = $arr['num'];

		foreach ($array as $key => $value) {
			if (!is_null($value['vars']['flagUse'])) {
				if ($value['vars']['idTarget'] == $arr['strTarget']) {
					if ($arr['strSelf']) {
						if ($value['vars']['idTarget'] != $arr['strSelf']) {
							$num++;
						}
					} else {
						$num++;
					}
				}
			}
			if ($value['child']) {
				$num += $this->_checkValueDetailIdAccountTitleFSLoop(array(
					'vars'      => $array[$key]['child'],
					'strTarget' => $arr['strTarget'],
					'num'       => 0,
					'strSelf'   => $arr['strSelf'],
				));
			}
		}

		return $num;
	}

	/**
		(array(

		))

	 */
	protected function _checkValueDetailIdAccountTitleFSId($arr)
	{
		//not need this check
		return;

		global $classDb;
		global $varsAccount;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingFSId' . $strNation,
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
			),
		));
		$varsFSItem = $this->_getVarsFSItem();
		$arrayFS = $varsFSItem['varsItem']['arrayFS'];
		$array = $rows['arrRows'];
		foreach ($array as $key => $value) {
			foreach ($arrayFS as $keyFS => $valueFS) {
				if (!$value['jsonJgaapAccountTitle'. $keyFS]) {
					continue;
				}
				$arrId = $value['jsonJgaapAccountTitle'. $keyFS];
				if ($arrId[$arr['strTarget']]) {
					$this->sendVars(array(
						'flag'    => 'idAccountTitleRemove',
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(),
					));
				}

			}

		}
	}


}
