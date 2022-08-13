<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_AccountTitleFSEditor extends Code_Else_Plugin_Accounting_Jpn_AccountTitleFS
{
	protected $_childSelf = array(
		'pathTplJs' => 'else/plugin/accounting/js/jpn/accountTitleFSEditor.js',
		'pathVarsJs'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/accountTitleFSEditor.php',
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
			'vars'     => $vars,
		));

		$varsItem = $this->_getVarsItem(array(
			'flag' => $flagFS,
		));

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

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget
		));

		if ($varsRequest['query']['func'] == 'DetailAdd') {
			$flagEditCheck = $this->_checkValueDetailAdd(array(
				'arrValue'    => $arrValue,
				'varsItem'    => $varsItem,
				'flagFS'      => $flagFS,
				'vars'        => $vars,
				'idTarget'    => $idTarget,
				'flagDefault' => $flagDefault,
			));

		} else {
			$flagEditCheck = $this->_checkValueDetailEdit(array(
				'arrValue'    => $arrValue,
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
		$strAccountTitle = 'jsonJgaapFS'. $flagFS;

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

			if ($flagEditCheck['idAccountTitle'] || $flagEditCheck['flagDebit']) {
				$this->_setValueDetailDbEdit(array(
					'arrValue'      => $arrValue,
					'varsItem'      => $varsItem,
					'flagFS'        => $flagFS,
					'idTarget'      => $idTarget,
					'flagEditCheck' => $flagEditCheck,
				));
			}

			if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
				$classCalcTempNextAccountTitleFS = $this->_getClassCalc(array(
					'flagType'   => 'TempNext',
					'flagDetail' => 'AccountTitleFS',
				));
				$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
				$numFiscalPeriod = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];

				if ($varsRequest['query']['func'] == 'DetailAdd') {
					$flagStatus = 'add';

				} else {
					$flagStatus = 'edit';
				}
				$flag = $classCalcTempNextAccountTitleFS->allot(array(
					'flagStatus'      => $flagStatus,
					'numFiscalPeriod' => $numFiscalPeriod,
					'flagFS'          => $flagFS,
					'idTarget'        => $idTarget,
					'arrValue' => array(
						'strTitle'               => $arrValue['arr']['strTitle'],
						'idAccountTitle'         => $arrValue['arr']['idAccountTitle'],
						'flagDebit'              => $arrValue['arr']['flagDebit'],
						'arrCommaIdAccountTitle' => $arrValue['arr']['arrCommaIdAccountTitle'],
						'flagUse'                => $arrValue['arr']['flagUse'],
					),
				));

				if ($flag) {
					if ($flag == 'arrayblock' || $flag == 'flagSortUse') {
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
			'arrValue'      => $arrValue,
			'varsItem'      => $varsItem,
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
					'flagCondition' => 'small',
					'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				),
			),
		));

		$array = $rows['arrRows'];
		$idAccountTitle = $arr['arrValue']['arr']['idAccountTitle'];
		$idAccountTitleCustom = 'custom_' . $arr['flagFS'] . '_' . $idAccountTitle;

		foreach ($array as $key => $value) {
			if (!$value['jsonJgaapFS' . $arr['flagFS']]) {
				continue;
			}

			$varsFS = $this->_setValueDetailDbEditLoop(array(
				'arrValue'      => $arr['arrValue'],
				'idValue'       => $idAccountTitleCustom,
				'vars'          => $value['jsonJgaapFS' . $arr['flagFS']],
				'idTarget'      => $arr['idTarget'],
				'flagEditCheck' => $arr['flagEditCheck'],
			));

			$jsonAccountTitle = json_encode($varsFS);
			$this->checkTextSize(array(
				'flag' => 'errorDataMax',
				'str'  => $jsonAccountTitle,
			));
			$strAccountTitle = 'jsonJgaapFS'. $arr['flagFS'];

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
		if (!$arr['flagEditCheck']['idAccountTitle']) {
			return;
		}

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
		$array = $rows['arrRows'];
		foreach ($array as $key => $value) {
			if (!$value['jsonJgaapAccountTitle'. $arr['flagFS']]) {
				continue;
			}
			$arrIdTarget = $arr['varsItem']['varsJgaapFS']['arrIdAccountTitle'][$arr['idTarget']];
			if (!$arrIdTarget) {
				$arrIdTarget = array();
			}
			$varsFS = $value['jsonJgaapAccountTitle'. $arr['flagFS']];
			foreach ($arrIdTarget as $keyTarget => $valueTarget) {
				$varsFS = $this->_setValueDetailDbEditLoopAccountTitle(array(
					'idValue'       => $idAccountTitleCustom,
					'vars'          => $varsFS,
					'idTarget'      => $keyTarget,
					'flagEditCheck' => $arr['flagEditCheck'],
				));
			}

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
	}

	protected function _setValueDetailDbEditLoopAccountTitle($arr)
	{
		$array = &$arr['vars'];

		foreach ($array as $key => $value) {
			if (!is_null($value['vars']['flagUse'])) {
				if ($value['vars']['idTarget'] == $arr['idTarget']) {
					if ($arr['flagEditCheck']['idAccountTitle']) {
						$array[$key]['vars']['idAccountTitleJgaapFS'] = $arr['idValue'];
					}
				}
			}
			if ($value['child']) {
				$array[$key]['child'] = $this->_setValueDetailDbEditLoopAccountTitle(array(
					'vars'          => $array[$key]['child'],
					'idTarget'      => $arr['idTarget'],
					'flagEditCheck' => $arr['flagEditCheck'],
					'idValue'       => $arr['idValue'],
				));
			}
		}

		return $array;
	}

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
				|| $value['id'] == 'ArrCommaIdAccountTitle'
				|| $value['id'] == 'FlagUse'
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

		global $varsRequest;

		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];

		$arrayBlock = $this->_getTreeBlock(array(
			'vars'     => $arr['varsItem']['varsFS']['jsonJgaapFS' . $arr['flagFS']],
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

		if (!(int) $varsTarget['vars']['flagSortUse']) {
			$this->_sendOld();
		}

		$strTitle = $arr['arrValue']['arr']['strTitle'];
		$arrayRows = $arr['varsItem']['varsFSRows'];
		foreach ($arrayRows as $key => $value) {
			$flagNum = $this->_checkTreeStrTitle(array(
				'vars'      => $value['jsonJgaapFS' . $arr['flagFS']],
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

		$array = $arrayBlock;
		$arrayCheck = array();
		foreach ($array as $key => $value) {
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
				if (!$value['jsonJgaapFS'. $keyFS]) {
					continue;
				}

				$flag = $this->_checkValueDetailIdAccountTitleFSLoop(array(
					'vars'    => $value['jsonJgaapFS'. $keyFS],
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
				$arrId = $value['jsonJgaapFS'. $keyFS];
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

		global $varsPluginAccountingAccount;

		$varsFS = $arr['varsItem']['varsFS']['jsonJgaapFS' . $arr['flagFS']];
		$varsFS = $this->_getFlagUseLog(array(
			'vars'                  => $varsFS,
			'arrIdAccountTitle'     => $arr['varsItem']['varsJgaapFS']['arrIdAccountTitle'],
			'arrIdAccountTitles'    => $arr['varsItem']['varsJgaapFSs'],
			'numFiscalPeriod'       => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'arrIdAccountTitleTemp' => $arr['varsItem']['varsJgaapFSTemp']['arrIdAccountTitle'],
			'idTarget'              => $arr['idTarget'],
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
				'vars'      => $value['jsonJgaapFS' . $arr['flagFS']],
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
			}
			$idAccountTitleCustom = 'custom_' . $arr['flagFS'] . '_' . $idAccountTitle;
			$arrayIdAccountTitle = array($idAccountTitle, $idAccountTitleCustom);
			foreach ($arrayIdAccountTitle as $key => $value) {
				$this->_checkValueDetailIdAccountTitle(array(
					'flagFS'    => $arr['flagFS'],
					'strTarget' => $value,
					'strSelf'   => $arr['idTarget'],
				));
			}
			if ($varsTarget['vars']['flagUseAccountTitle']
				|| $varsTarget['vars']['flagUseAccountTitles']
			) {
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
			if ($varsTarget['vars']['flagUseAccountTitle']
				|| $varsTarget['vars']['flagUseAccountTitles']
			) {
				$this->_sendOld();
			}
			$flagEdit['flagDebit'] = 1;
		}

		$array = $arrayBlock;
		$arrayCheck = array();
		foreach ($array as $key => $value) {
			$arrayCheck[$value['vars']['idTarget']] = $value;
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

		return $flagEdit;
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
			'vars'     => $arr['varsItem']['varsFS']['jsonJgaapFS' . $arr['flagFS']],
			'idTarget' => $arr['idTarget'],
			'arrNew'   => array(),
		));

		$arrayCheck = array();
		foreach ($array as $key => $value) {
			$arrayCheck[$value['vars']['idTarget']] = $value;
		}

		$varsTarget = array();
		if ($varsRequest['query']['func'] == 'DetailEdit') {
			foreach ($array as $key => $value) {
				if ($value['vars']['idTarget'] == $arr['idTarget']) {
					$varsTarget = $value;
					break;
				}
			}
		}
		$arrayIdAccountTitle = array();
		if ((int) $varsTarget['vars']['flagSortUse'] && $varsRequest['query']['func'] == 'DetailAdd') {
			foreach ($array as $key => $value) {
				$arrayIdAccountTitle[] = $value['vars']['idTarget'];
			}

		} else {
			$arrayIdAccountTitle = $classEscape->splitCommaArrayData(array('data' => $arr['arrValue']['arr']['arrCommaIdAccountTitle']));
		}

		$array = $arrayIdAccountTitle;
		$arrayNew = array();

		$tmplVars = $arr['varsItem']['varsFSItem']['varsAccountTitleFS'];
		foreach ($array as $key => $value) {
			if ($varsRequest['query']['func'] == 'DetailAdd') {
				if ($value == 'insertPoint') {
					$tmplVars['strTitle'] = $arr['arrValue']['arr']['strTitle'];
					$tmplVars['vars'] = array(
						'idTarget'    => $idTarget,
						'flagUse'     => (int) $arr['arrValue']['arr']['flagUse'],
						'flagDebit'   => (int) $arr['arrValue']['arr']['flagDebit'],
						'flagSortUse' => 1,
						'varsValue'   => array(),
					);
					$arrayNew[] = $tmplVars;

				} else {
					$arrayNew[] = $arrayCheck[$value];
				}

			} else {
				$flagSortUse = (int) $varsTarget['vars']['flagSortUse'];

				if ($value == $arr['idTarget']) {
					$arrayCheck[$value]['strTitle'] = $arr['arrValue']['arr']['strTitle'];
					$arrayCheck[$value]['vars'] = array(
						'idTarget'    => $idTarget,
						'flagUse'     => (int) $arr['arrValue']['arr']['flagUse'],
						'flagDebit'   => (int) $arr['arrValue']['arr']['flagDebit'],
						'flagSortUse' => $flagSortUse,
						'varsValue'   => array(),
					);
					$arrayNew[] = $arrayCheck[$value];

				} else {
					$arrayNew[] = $arrayCheck[$value];
				}
			}
		}

		$varsFS = $this->_insertTreeBlock(array(
			'vars'       => $arr['varsItem']['varsFS']['jsonJgaapFS'. $arr['flagFS']],
			'idTarget'   => $arr['idTarget'],
			'varsTarget' => $arrayNew,
		));

		return $varsFS;
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
					&& $varsRequest['query']['func'] == 'DetailEdit'
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


}
