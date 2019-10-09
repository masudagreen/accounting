<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_AccountTitleCSEditor extends Code_Else_Plugin_Accounting_Jpn_AccountTitleCS
{
	protected $_childSelf = array(
		'pathTplJs'  => 'else/plugin/accounting/js/jpn/accountTitleCSEditor.js',
		'pathVarsJs' => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/accountTitleCSEditor.php',
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

		if (!$this->_checkCurrent()) {
			$this->_sendOldError();
		}

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'update',
			'idTarget'      => $this->_extSelf['idPreference'],
		));

		if (!$flag) {
			$this->_sendOld();
		}

		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];
		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$flagFS = $this->_checkValueFS(array(
			'vars' => $vars,
		));

		$flagDirect = (int) $varsRequest['query']['jsonValue']['vars']['FlagDirect'];

		$varsItem = $this->_getVarsItem(array(
			'vars'       => $vars,
			'flagFS'     => $flagFS,
			'flagDirect' => $flagDirect,
		));

		if (preg_match("/^(tempNext)$/", $flagCurrentFlagNow)) {
			$varsItem = $this->_updateVarsItem(array(
				'flagFS'     => $flagFS,
				'flagDirect' => $flagDirect,
				'varsItem'   => $varsItem,
			));
		}

		$vars = $this->_updateVars(array(
			'flagFS'     => $flagFS,
			'flagDirect' => $flagDirect,
			'vars'       => $vars,
			'varsItem'   => $varsItem,
		));

		$varsTarget = $this->_getValueTemplate(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail'],
		));

		$varsTarget = $this->getValue(array(
			'vars' => $varsTarget,
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget
		));

		$flagEditCheck = $this->_checkValueDetailEdit(array(
			'arrValue'    => &$arrValue,
			'varsItem'    => $varsItem,
			'flagFS'      => $flagFS,
			'flagDirect'  => $flagDirect,
			'vars'        => $vars,
			'idTarget'    => $idTarget,
		));

		$varsFS = $this->_setValueDetailDb(array(
			'arrValue'    => $arrValue,
			'varsItem'    => $varsItem,
			'flagFS'      => $flagFS,
			'flagDirect'  => $flagDirect,
			'vars'        => $vars,
			'idTarget'    => $idTarget,
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

			if ($flagEditCheck['varsJgaapCSUpdate']
				|| $flagEditCheck['idAccountTitleMinusInsert']
				|| $flagEditCheck['idAccountTitlePlusInsert']
			) {
				$this->_setValueDetailDbEdit(array(
					'arrValue'      => $arrValue,
					'flagFS'        => $flagFS,
					'flagDirect'    => $flagDirect,
					'idTarget'      => $idTarget,
					'flagEditCheck' => $flagEditCheck,
				));
			}

			if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
				$classCalcTempNextAccountTitleCS = $this->_getClassCalc(array(
					'flagType'   => 'TempNext',
					'flagDetail' => 'AccountTitleCS',
				));
				$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
				$numFiscalPeriod = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];

				$flag = $classCalcTempNextAccountTitleCS->allot(array(
					'flagStatus'      => 'edit',
					'numFiscalPeriod' => $numFiscalPeriod,
					'flagFS'          => $flagFS,
					'flagDirect'      => $flagDirect,
					'idTarget'        => $idTarget,
					'arrValue' => array(
						'idAccountTitlePlus'  => $arrValue['arr']['idAccountTitlePlus'],
						'flagMethodPlus'      => $arrValue['arr']['flagMethodPlus'],
						'idAccountTitleMinus' => $arrValue['arr']['idAccountTitleMinus'],
						'flagMethodMinus'     => $arrValue['arr']['flagMethodMinus'],
					),
				));

				if ($flag) {
					if ($flag == 'sendOld') {
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
			'flagDirect'    => $flagDirect,
			'idTarget'      => $idTarget,
			'flagEditCheck' => $flagEditCheck,
		))

	 */
	protected function _setValueDetailDbEdit($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsPluginAccountingAccount;

		$numFiscalPeriodTempPrev = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] - 1;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $numFiscalPeriodTempPrev,
		));

		if ($arr['flagEditCheck']['varsJgaapCSUpdate']) {
			$id = $varsFS['id'];
			$varsFS = $this->_setValueDetailDbEditLoop(array(
				'arrValue'   => $arr['arrValue'],
				'vars'       => $varsFS['jsonJgaapAccountTitle'. $arr['flagFS']],
				'idTarget'   => $arr['idTarget'],
				'flagDirect' => $arr['flagDirect'],
			));

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
						'value'         => $id,
					),
				),
				'arrValue'  => $arrDbValue,
			));
		}

		if ($arr['flagEditCheck']['idAccountTitleMinusInsert']
			|| $arr['flagEditCheck']['idAccountTitlePlusInsert']
		) {
			$classCalcTempNextAccountTitleFSCS = $this->_getClassCalc(array(
				'flagType'   => 'TempNext',
				'flagDetail' => 'AccountTitleFSCS',
			));
			$array = array();
			if ($arr['flagEditCheck']['idAccountTitleMinusInsert']) {
				$array[$arr['arrValue']['arr']['idAccountTitleMinus']] = 1;
			}
			if ($arr['flagEditCheck']['idAccountTitlePlusInsert']) {
				$array[$arr['arrValue']['arr']['idAccountTitlePlus']] = 1;
			}
			foreach ($array as $key => $value) {
				$flag = $classCalcTempNextAccountTitleFSCS->allot(array(
					'flagStatus'      => 'back',
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					'flagFS'          => 'CS',
					'idTarget'        => $key,
					'flagDirect'      => $arr['flagDirect'],
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
		}
	}

	/**
				'arrValue'   => $arr['arrValue'],
				'vars'       => $varsFS['jsonJgaapAccountTitle'. $arr['flagFS']],
				'idTarget'   => $arr['idTarget'],
				'flagDirect' => $arr['flagDirect'],
	 */
	protected function _setValueDetailDbEditLoop($arr)
	{
		$strFlagDirect = 'varsInDirect';
		if ($arr['flagDirect']) {
			$strFlagDirect = 'varsDirect';
		}
		$array = &$arr['vars'];
		foreach ($array as $key => $value) {
			if (!is_null($value['vars']['flagUse'])) {
				if ($value['vars']['idTarget'] == $arr['idTarget']) {
					$varsJgaapCS = array(
						'idAccountTitleMinus' => $arr['arrValue']['arr']['idAccountTitleMinus'],
						'flagMethodMinus'     => $arr['arrValue']['arr']['flagMethodMinus'],
						'idAccountTitlePlus'  => $arr['arrValue']['arr']['idAccountTitlePlus'],
						'flagMethodPlus'      => $arr['arrValue']['arr']['flagMethodPlus'],
					);
					$array[$key]['vars']['varsJgaapCS'][$strFlagDirect] = $varsJgaapCS;
				}
			}
			if ($value['child']) {
				$array[$key]['child'] = $this->_setValueDetailDbEditLoop(array(
					'vars'       => $array[$key]['child'],
					'idTarget'   => $arr['idTarget'],
					'arrValue'   => $arr['arrValue'],
					'flagDirect' => $arr['flagDirect'],
				));
			}
		}

		return $array;
	}

	/**
		(array(
				'flagFS'     => $flagFS,
				'flagDirect' => $flagDirect,
				'varsItem'   => $varsItem,
		))
	 */
	protected function _updateVarsItem($arr)
	{
		global $varsPluginAccountingAccount;

		$numFiscalPeriodTempPrev = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] - 1;

		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $numFiscalPeriodTempPrev,
		));

		$strFlagDirect = 'varsInDirect';
		if ($arr['flagDirect']) {
			$strFlagDirect = 'varsDirect';
		}

		$arrAccountTitleTempPrev = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => array(),
			'numFiscalPeriod'    => $numFiscalPeriodTempPrev,
		));

		$arrStrTitle = array();
		$arrStrTitle[$arr['varsFSItem']['varsCSOption']['arrayOptionNone']['value']] = $arr['varsFSItem']['varsCSOption']['arrayOptionNone']['strTitle'];
		$arrStrTitle[$arr['varsFSItem']['varsCSOption']['arrayOptionCash']['value']] = $arr['varsFSItem']['varsCSOption']['arrayOptionCash']['strTitle'];
		$varsJgaapFSCSTempPrev = array();
		$varsJgaapFSCSTempPrev[$strFlagDirect] = $this->_getVarsItemJgaapFSCS(array(
			'arrStrTitle'  => $arrStrTitle,
			'arrSelectTag' => array($arr['varsFSItem']['varsCSOption']['arrayOptionNone'], $arr['varsFSItem']['varsCSOption']['arrayOptionCash']),
			'vars'         => $varsFS['jsonJgaapFSCS'][$strFlagDirect],
		));

		$arr['varsItem']['flagTempNext'] = 1;
		$arr['varsItem']['arrAccountTitleTempPrev'] = $arrAccountTitleTempPrev;
		$arr['varsItem']['varsJgaapFSCSTempPrev'] = $varsJgaapFSCSTempPrev;

		return $arr['varsItem'];

	}



	/**
		(array(
			'vars'        => $vars,
		))
	 */
	public function _getValueTemplate($arr)
	{
		global $varsRequest;

		$arrayNew = array();
		$array = $arr['vars'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'IdAccountTitlePlus'
				|| $value['id'] == 'FlagMethodPlus'
				|| $value['id'] == 'IdAccountTitleMinus'
				|| $value['id'] == 'FlagMethodMinus'
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
	protected function _checkValueDetailEdit($arr)
	{
		global $varsPluginAccountingAccount;

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());

		$numFiscalPeriodTemp = '';
		if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
			$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] + 1;

		} elseif (preg_match("/^(tempNext)$/", $flagCurrentFlagNow)) {
			$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] - 1;
		}

		$varsFS = $arr['varsItem']['varsFS']['jsonJgaapAccountTitle'. $arr['flagFS']];
		$varsFS = $this->_getFlagUseLog(array(
			'vars'                  => $varsFS,
			'idTarget'              => $arr['idTarget'],
			'numFiscalPeriod'       => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'numFiscalPeriodTemp'   => $numFiscalPeriodTemp,
			'classCalcAccountTitle' => $this->_getClassCalc(array('flagType' => 'AccountTitle')),
			'flagCurrentFlagNow'    => $flagCurrentFlagNow,
			'flagFS'                => $arr['flagFS'],
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

		$strFlagDirect = 'varsInDirect';
		if ($arr['flagDirect']) {
			$strFlagDirect = 'varsDirect';
		}

		$idAccountTitlePlus = $arr['arrValue']['arr']['idAccountTitlePlus'];
		$idAccountTitleMinus = $arr['arrValue']['arr']['idAccountTitleMinus'];
		$flagMethodPlus = $arr['arrValue']['arr']['flagMethodPlus'];
		$flagMethodMinus = $arr['arrValue']['arr']['flagMethodMinus'];

		if ($varsTarget['vars']['flagUseLogElse']
			 || ($varsTarget['vars']['flagUseLogElseTemp'] && $varsTarget['vars']['flagFS'] == 'BS')
		) {
			$tempVars = $varsTarget['vars']['varsJgaapCS'][$strFlagDirect];
			if (!($idAccountTitleMinus == $tempVars['idAccountTitleMinus']
				&& $flagMethodMinus == $tempVars['flagMethodMinus']
				&& $idAccountTitlePlus == $tempVars['idAccountTitlePlus']
				&& $flagMethodPlus == $tempVars['flagMethodPlus']
			)) {
				$this->_sendOld();
			}
		}

		if ($idAccountTitlePlus != 'cash' && $idAccountTitleMinus == 'cash') {
			$this->_sendOld();
		}

		if ($idAccountTitlePlus == 'cash' && $idAccountTitleMinus != 'cash') {
			$this->_sendOld();
		}

		if ($idAccountTitlePlus == 'none') {
			if (!($idAccountTitleMinus == 'none' || $idAccountTitleMinus == 'cash')) {
				if ($flagMethodMinus != 'net') {
					$this->_sendOld();
				}
			}
		}
		if ($idAccountTitleMinus == 'none') {
			if (!($idAccountTitlePlus == 'none' || $idAccountTitlePlus == 'cash')) {
				if ($flagMethodPlus != 'net') {
					$this->_sendOld();
				}
			}
		}

		if (!($idAccountTitlePlus == 'none' || $idAccountTitlePlus == 'cash')) {
			if (!($idAccountTitleMinus == 'none' || $idAccountTitleMinus == 'cash')) {
				if ($flagMethodPlus == 'sumDebit') {
					if ($flagMethodMinus != 'sumCredit') {
						$this->_sendOld();
					}

				} elseif ($flagMethodPlus == 'sumCredit') {
					if ($flagMethodMinus != 'sumDebit') {
						$this->_sendOld();
					}

				} elseif ($flagMethodPlus == 'net') {
					if ($flagMethodMinus != 'net') {
						$this->_sendOld();
					}
				}
			}
		}
		$flagEdit = array();
		if ($arr['varsItem']['flagTempNext']) {
			$idTarget = $arr['idTarget'];
			$varsTempPrev = $arr['varsItem']['arrAccountTitleTempPrev']['arrStrTitle'][$idTarget];
			if (!$varsTempPrev) {
				return $flagEdit;
			}

			if (!$arr['varsItem']['varsJgaapFSCSTempPrev'][$strFlagDirect]['arrStrTitle'][$idAccountTitleMinus]) {
				if (!($idAccountTitleMinus == 'none' || $idAccountTitleMinus == 'cash')) {
					$flagEdit['idAccountTitleMinusInsert'] = 1;
				}
			}
			if (!$arr['varsItem']['varsJgaapFSCSTempPrev'][$strFlagDirect]['arrStrTitle'][$idAccountTitlePlus]) {
				if (!($idAccountTitlePlus == 'none' || $idAccountTitlePlus == 'cash')) {
					$flagEdit['idAccountTitlePlusInsert'] = 1;
				}
			}
			if ($varsTempPrev['vars']['varsJgaapCS'][$strFlagDirect]['idAccountTitleMinus'] != $idAccountTitleMinus
				|| $varsTempPrev['vars']['varsJgaapCS'][$strFlagDirect]['idAccountTitlePlus'] != $idAccountTitlePlus
				|| $varsTempPrev['vars']['varsJgaapCS'][$strFlagDirect]['flagMethodMinus'] != $flagMethodMinus
				|| $varsTempPrev['vars']['varsJgaapCS'][$strFlagDirect]['flagMethodPlus'] != $flagMethodPlus
			) {
				$flagEdit['varsJgaapCSUpdate'] = 1;
			}
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
		foreach ($array as $key => $value) {
			$arrayIdAccountTitle[] = $value['vars']['idTarget'];
		}

		$idAccountTitlePlus = $arr['arrValue']['arr']['idAccountTitlePlus'];
		$idAccountTitleMinus = $arr['arrValue']['arr']['idAccountTitleMinus'];
		$flagMethodPlus = $arr['arrValue']['arr']['flagMethodPlus'];
		$flagMethodMinus = $arr['arrValue']['arr']['flagMethodMinus'];


		if ($idAccountTitlePlus == 'cash' || $idAccountTitlePlus == 'none') {
			$flagMethodPlus = '';
		}
		if ($idAccountTitleMinus == 'cash' || $idAccountTitleMinus == 'none') {
			$flagMethodMinus = '';
		}

		$strFlagDirect = 'varsInDirect';
		if ($arr['flagDirect']) {
			$strFlagDirect = 'varsDirect';
		}
		$array = $arrayIdAccountTitle;
		$arrayNew = array();
		foreach ($array as $key => $value) {
			if ($value == $arr['idTarget']) {
				$arrayCheck[$value]['vars']['varsJgaapCS'][$strFlagDirect] = array(
					'idAccountTitleMinus' => $idAccountTitleMinus, 'flagMethodPlus' => $flagMethodPlus,
					'idAccountTitlePlus' => $idAccountTitlePlus, 'flagMethodMinus' => $flagMethodMinus,
				);
				$arrayNew[] = $arrayCheck[$value];

			} else {
				$arrayNew[] = $arrayCheck[$value];
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
}
