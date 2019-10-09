<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_AccountTitleFSCSEditor extends Code_Else_Plugin_Accounting_Jpn_AccountTitleFSCS
{
	protected $_childSelf = array(
		'pathTplJs' => 'else/plugin/accounting/js/jpn/accountTitleFSCSEditor.js',
		'pathVarsJs'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/accountTitleFSCSEditor.php',
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

		$flagDirect = (int) $varsRequest['query']['jsonValue']['vars']['FlagDirect'];

		$varsItem = $this->_getVarsItem(array(
			'flagFS'     => $vars['flagFS'],
			'vars'       => $vars,
			'flagDirect' => $flagDirect,
		));

		$vars = $this->_updateVars(array(
			'flagFS'     => $vars['flagFS'],
			'flagDirect' => $flagDirect,
			'vars'       => $vars,
			'varsItem'   => $varsItem,
		));

		$flagDefault = $this->_checkValueDetailDefault(array(
			'vars'     => $vars['portal']['varsList']['varsDetail'],
			'idTarget' => $idTarget,
		));

		$varsTarget['vars']['varsDetail'] = $this->_setValueTemplate(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail'],
		));

		$varsTarget['vars']['varsDetail'] = $this->getValue(array(
			'vars' => $varsTarget['vars']['varsDetail'],
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget['vars']['varsDetail']
		));

		if ($varsRequest['query']['func'] == 'DetailAdd') {
			$flagEditCheck = $this->_checkValueDetailAdd(array(
				'arrValue'    => $arrValue,
				'varsItem'    => $varsItem,
				'flagFS'      => $vars['flagFS'],
				'vars'        => $vars,
				'idTarget'    => $idTarget,
				'flagDefault' => $flagDefault,
				'flagDirect'  => $flagDirect,
			));

		} else {
			$flagEditCheck = $this->_checkValueDetailEdit(array(
				'arrValue'    => $arrValue,
				'varsItem'    => $varsItem,
				'flagFS'      => $vars['flagFS'],
				'vars'        => $vars,
				'idTarget'    => $idTarget,
				'flagDefault' => $flagDefault,
				'flagDirect'  => $flagDirect,
			));
		}

		$varsFS = $this->_setValueDetailDb(array(
			'arrValue'    => $arrValue,
			'varsItem'    => $varsItem,
			'flagFS'      => $vars['flagFS'],
			'vars'        => $vars,
			'idTarget'    => $idTarget,
			'flagDefault' => $flagDefault,
			'flagDirect'  => $flagDirect,
		));

		$jsonAccountTitle = json_encode($varsFS);
		$this->checkTextSize(array(
			'flag' => 'errorDataMax',
			'str'  => $jsonAccountTitle,
		));
		$strAccountTitle = 'jsonJgaapFSCS';

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

			if ($flagEditCheck['idAccountTitle']) {
				$this->_setValueDetailDbEdit(array(
					'arrValue'      => $arrValue,
					'flagFS'        => $vars['flagFS'],
					'varsItem'      => $varsItem,
					'idTarget'      => $idTarget,
					'flagDirect'    => $flagDirect,
					'flagEditCheck' => $flagEditCheck,
				));
			}

			if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
				$classCalcTempNextAccountTitleFSCS = $this->_getClassCalc(array(
					'flagType'   => 'TempNext',
					'flagDetail' => 'AccountTitleFSCS',
				));
				$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
				$numFiscalPeriod = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];

				if ($varsRequest['query']['func'] == 'DetailAdd') {
					$flagStatus = 'add';

				} else {
					$flagStatus = 'edit';
				}
				$flag = $classCalcTempNextAccountTitleFSCS->allot(array(
					'flagStatus'      => $flagStatus,
					'numFiscalPeriod' => $numFiscalPeriod,
					'flagFS'          => $vars['flagFS'],
					'idTarget'        => $idTarget,
					'flagDirect'      => $flagDirect,
					'arrValue' => array(
						'strTitle'               => $arrValue['arr']['strTitle'],
						'idAccountTitle'         => $arrValue['arr']['idAccountTitle'],
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
			'flagFS'        => $flagFS,
			'idTarget'      => $idTarget,
			'flagEditCheck' => $flagEditCheck,
		))

	 */
	protected function _setValueDetailDbEdit($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

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
		$strDirect = 'inDirect';
		if ($arr['flagDirect']) {
			$strDirect = 'direct';
		}
		$idAccountTitle = $arr['arrValue']['arr']['idAccountTitle'];
		$idAccountTitleCustom = 'custom_' . $arr['flagFS'] . '_' . $strDirect . '_' . $idAccountTitle;

		$strFlagDirect = 'varsInDirect';
		if ($arr['flagDirect']) {
			$strFlagDirect = 'varsDirect';
		}

		foreach ($array as $key => $value) {
			if (!$value['jsonJgaapFSCS']) {
				continue;
			}

			$varsFS = $value['jsonJgaapFSCS'];
			$varsFS[$strFlagDirect] = $this->_setValueDetailDbEditLoop(array(
				'idValue'       => $idAccountTitleCustom,
				'vars'          => $value['jsonJgaapFSCS'][$strFlagDirect],
				'idTarget'      => $arr['idTarget'],
				'flagEditCheck' => $arr['flagEditCheck'],
			));

			$jsonAccountTitle = json_encode($varsFS);
			$this->checkTextSize(array(
				'flag' => 'errorDataMax',
				'str'  => $jsonAccountTitle,
			));
			$strAccountTitle = 'jsonJgaapFSCS';

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

		$arrayFSList = $this->_getFSList(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));


		$array = $rows['arrRows'];
		foreach ($array as $key => $value) {
			foreach ($arrayFSList as $keyFSList => $valueFSList) {
				if (!$value['jsonJgaapAccountTitle'. $keyFSList]) {
					continue;
				}
				$arrIdTarget = $arr['varsItem']['varsJgaapFS']['arrIdAccountTitleFS'][$keyFSList][$arr['idTarget']];
				if (!$arrIdTarget) {
					continue;
				}

				$varsFS = $value['jsonJgaapAccountTitle'. $keyFSList];
				foreach ($arrIdTarget as $keyTarget => $valueTarget) {
					$varsFS = $this->_setValueDetailDbEditLoopAccountTitle(array(
						'idValue'       => $idAccountTitleCustom,
						'arrValue'      => $arr['arrValue'],
						'vars'          => $varsFS,
						'strFlagDirect' => $strFlagDirect,
						'idTarget'      => $keyTarget,
						'idTargetCS'    => $arr['idTarget'],
						'flagEditCheck' => $arr['flagEditCheck'],
					));
				}

				$jsonAccountTitle = json_encode($varsFS);
				$this->checkTextSize(array(
					'flag' => 'errorDataMax',
					'str'  => $jsonAccountTitle,
				));
				$strAccountTitle = 'jsonJgaapAccountTitle'. $keyFSList;

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


	}

	/**
		(array(
			'arrValue'      => $arr['arrValue'],
			'vars'          => $varsFS,
			'strFlagDirect' => $strFlagDirect,
			'idTarget'      => $keyTarget,
			'idTargetCS'    => $arr['idTarget'],
			'flagEditCheck' => $arr['flagEditCheck'],
		))
	 */
	protected function _setValueDetailDbEditLoopAccountTitle($arr)
	{
		$array = &$arr['vars'];

		foreach ($array as $key => $value) {
			if (!is_null($value['vars']['flagUse'])) {
				if ($value['vars']['idTarget'] == $arr['idTarget']) {
					if ($arr['flagEditCheck']['idAccountTitle']) {
						if ($value['vars']['varsJgaapCS'][$arr['strFlagDirect']]['idAccountTitleMinus'] == $arr['idTargetCS']) {
							$array[$key]['vars']['varsJgaapCS'][$arr['strFlagDirect']]['idAccountTitleMinus'] = $arr['idValue'];
						}
						if ($value['vars']['varsJgaapCS'][$arr['strFlagDirect']]['idAccountTitlePlus'] == $arr['idTargetCS']) {
							$array[$key]['vars']['varsJgaapCS'][$arr['strFlagDirect']]['idAccountTitlePlus'] = $arr['idValue'];
						}
					}
				}
			}
			if ($value['child']) {
				$array[$key]['child'] = $this->_setValueDetailDbEditLoopAccountTitle(array(
					'vars'          => $array[$key]['child'],
					'idTarget'      => $arr['idTarget'],
					'idTargetCS'    => $arr['idTargetCS'],
					'strFlagDirect' => $arr['strFlagDirect'],
					'flagEditCheck' => $arr['flagEditCheck'],
					'idValue'       => $arr['idValue'],
				));
			}
		}

		return $array;
	}

	/**
		(array(

		))
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
				}
			}
			if ($value['child']) {
				$array[$key]['child'] = $this->_setValueDetailDbEditLoop(array(
					'vars'          => $array[$key]['child'],
					'idValue'       => $arr['idValue'],
					'idTarget'      => $arr['idTarget'],
					'flagEditCheck' => $arr['flagEditCheck'],
					'arrValue'      => $arr['arrValue'],
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
		$arrayNew = array();
		$array = $arr['vars'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'StrTitle'
				|| $value['id'] == 'IdAccountTitle'
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

		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];

		$strFlagDirect = 'varsInDirect';
		if ($arr['flagDirect']) {
			$strFlagDirect = 'varsDirect';
		}

		$arrayBlock = $this->_getTreeBlock(array(
			'vars'     => $arr['varsItem']['varsFS']['jsonJgaapFS'. $arr['flagFS']][$strFlagDirect],
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
				'vars'      => $value['jsonJgaapFS'. $arr['flagFS']][$strFlagDirect],
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

		$strDirect = 'inDirect';
		if ($arr['flagDirect']) {
			$strDirect = 'direct';
		}
		$idAccountTitle = $arr['arrValue']['arr']['idAccountTitle'];
		$idAccountTitleCustom = 'custom_' . $arr['flagFS'] . '_' . $strDirect . '_' . $idAccountTitle;
		$arrayIdAccountTitle = array($idAccountTitle, $idAccountTitleCustom);
		foreach ($arrayIdAccountTitle as $key => $value) {
			$this->_checkValueDetailIdAccountTitle(array(
				'flagFS'     => $arr['flagFS'],
				'strTarget'  => $value,
				'flagDirect' => $arr['flagDirect'],
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

		$allNum = count($arrayBlock);
		if ($arrayBlock[0]['vars']['idTarget'] == 'currentTermProfitOrLossPre') {
			$allNum--;
		}
		if ($num != $allNum) {
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

		$strFlagDirect = 'varsInDirect';
		if ($arr['flagDirect']) {
			$strFlagDirect = 'varsDirect';
		}
		$array = $rows['arrRows'];
		foreach ($array as $key => $value) {
			$flag = $this->_checkValueDetailIdAccountTitleFSLoop(array(
				'vars'      => $value['jsonJgaapFS'. $arr['flagFS']][$strFlagDirect],
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

		$strFlagDirect = 'varsInDirect';
		if ($arr['flagDirect']) {
			$strFlagDirect = 'varsDirect';
		}
		$array = $rows['arrRows'];
		foreach ($array as $key => $value) {
			$arrId = $value['jsonJgaapFS'. $arr['flagFS']][$strFlagDirect];
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

		$strFlagDirect = 'varsInDirect';
		if ($arr['flagDirect']) {
			$strFlagDirect = 'varsDirect';
		}

		$varsFS = $arr['varsItem']['varsFS']['jsonJgaapFS' . $arr['flagFS']][$strFlagDirect];
		$varsFS = $this->_getFlagUseLog(array(
			'vars'                  => $varsFS,
			'arrIdAccountTitle'     => $arr['varsItem']['varsJgaapFS']['arrIdAccountTitle'],
			'arrIdAccountTitles'    => $arr['varsItem']['varsJgaapFSs'],
			'numFiscalPeriod'       => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idTarget'              => $arr['idTarget'],
		));

		$arrayBlock = $this->_getTreeBlock(array(
			'vars'     => $varsFS,
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

		$strTitle = $arr['arrValue']['arr']['strTitle'];
		$arrayRows = $arr['varsItem']['varsFSRows'];
		foreach ($arrayRows as $key => $value) {
			$flagNum = $this->_checkTreeStrTitle(array(
				'vars'      => $value['jsonJgaapFS' . $arr['flagFS']][$strFlagDirect],
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

		$strDirect = 'inDirect';
		if ($arr['flagDirect']) {
			$strDirect = 'direct';
		}
		$idAccountTitle = $arr['arrValue']['arr']['idAccountTitle'];
		$flag = 0;
		if ((int) $varsTarget['flagDefault']) {
			if ($idAccountTitle != $varsTarget['vars']['idTarget']) {
				$flag = 1;
			}

		} else {
			$idAccountTitleCustom = 'custom_' . $arr['flagFS'] . '_' . $strDirect . '_' . $idAccountTitle;
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
			$idAccountTitleCustom = 'custom_' . $arr['flagFS'] . '_' . $strDirect . '_' . $idAccountTitle;
			$arrayIdAccountTitle = array($idAccountTitle, $idAccountTitleCustom);
			foreach ($arrayIdAccountTitle as $key => $value) {
				$this->_checkValueDetailIdAccountTitle(array(
					'flagFS'     => $arr['flagFS'],
					'strTarget'  => $value,
					'strSelf'    => $arr['idTarget'],
					'flagDirect' => $arr['flagDirect'],
				));
			}
			if ($varsTarget['vars']['flagUseAccountTitle']
				|| $varsTarget['vars']['flagUseAccountTitles']
			) {
				$this->_sendOld();
			}
			$flagEdit['idAccountTitle'] = 1;
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
		$allNum = count($arrayBlock);
		if ($arrayBlock[0]['vars']['idTarget'] == 'currentTermProfitOrLossPre') {
			$allNum--;
		}
		if ($num != $allNum) {
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

		$strDirect = 'inDirect';
		if ($arr['flagDirect']) {
			$strDirect = 'direct';
		}

		$strFlagDirect = 'varsInDirect';
		if ($arr['flagDirect']) {
			$strFlagDirect = 'varsDirect';
		}

		$idTarget = $arr['arrValue']['arr']['idAccountTitle'];
		if ($varsRequest['query']['func'] == 'DetailAdd'
			|| ($varsRequest['query']['func'] == 'DetailEdit' && !$arr['flagDefault'])
		) {
			$idTarget = 'custom_' . $arr['flagFS'] . '_' . $strDirect . '_' . $arr['arrValue']['arr']['idAccountTitle'];
		}

		$arrayBlock = $this->_getTreeBlock(array(
			'vars'     => $arr['varsItem']['varsFS']['jsonJgaapFS' . $arr['flagFS']][$strFlagDirect],
			'idTarget' => $arr['idTarget'],
		));

		$arrayCheck = array();
		foreach ($arrayBlock as $key => $value) {
			$arrayCheck[$value['vars']['idTarget']] = $value;
		}

		$arrayIdAccountTitle = $classEscape->splitCommaArrayData(array('data' => $arr['arrValue']['arr']['arrCommaIdAccountTitle']));
		if ($arrayBlock[0]['vars']['idTarget'] == 'currentTermProfitOrLossPre') {
			array_unshift($arrayIdAccountTitle, 'currentTermProfitOrLossPre');;
		}

		$array = $arrayIdAccountTitle;
		$arrayNew = array();
		$tmplVars = $arr['varsItem']['varsFSItem']['varsAccountTitleFSCS'];
		foreach ($array as $key => $value) {
			if ($varsRequest['query']['func'] == 'DetailAdd') {
				if ($value == 'insertPoint') {
					$tmplVars['strTitle'] = $arr['arrValue']['arr']['strTitle'];
					$tmplVars['vars'] = array(
						'idTarget'    => $idTarget,
						'flagUse'     => (int) $arr['arrValue']['arr']['flagUse'],
						'flagSortUse' => 1,
						'varsValue'   => array(),
					);
					$arrayNew[] = $tmplVars;

				} else {
					$arrayNew[] = $arrayCheck[$value];
				}

			} else {
				if ($value == $arr['idTarget']) {
					$arrayCheck[$value]['strTitle'] = $arr['arrValue']['arr']['strTitle'];
					$arrayCheck[$value]['vars'] = array(
						'idTarget'    => $idTarget,
						'flagUse'     => (int) $arr['arrValue']['arr']['flagUse'],
						'flagSortUse' => 1,
						'varsValue'   => array(),
					);
					$arrayNew[] = $arrayCheck[$value];

				} else {
					$arrayNew[] = $arrayCheck[$value];
				}
			}
		}

		$varsFS = $this->_insertTreeBlock(array(
			'vars'       => $arr['varsItem']['varsFS']['jsonJgaapFS'. $arr['flagFS']][$strFlagDirect],
			'idTarget'   => $arr['idTarget'],
			'varsTarget' => $arrayNew,
		));

		$arr['varsItem']['varsFS']['jsonJgaapFS'. $arr['flagFS']][$strFlagDirect] = $varsFS;

		return $arr['varsItem']['varsFS']['jsonJgaapFS'. $arr['flagFS']];
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
