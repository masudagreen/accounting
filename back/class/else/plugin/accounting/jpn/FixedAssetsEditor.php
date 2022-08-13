<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_FixedAssetsEditor extends Code_Else_Plugin_Accounting_Jpn_FixedAssets
{
	protected $_childSelf = array(
		'pathTplJs'   => 'else/plugin/accounting/js/jpn/fixedAssetsEditor.js',
		'pathVarsJs'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/fixedAssetsEditor.php',
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
			'arrFolder' => array(
				array(
					'flagType'  => 'folder',
					'strTable'  => 'accountingAccountMemo',
					'strColumn' => 'jsonFixedAssetsEditorNaviFormat',
					'flagEntity'  => 1,
					'flagAccount' => 1,
				),
			),
		));
	}

	/**
	 *
	 */
	protected function _iniNaviFormatSave()
	{
		$this->_setNaviFormatSave(array(
			'pathVars'    => $this->_childSelf['pathVarsJs'],
			'strColumn'   => 'jsonFixedAssetsEditorNaviFormat',
			'strTable'    => 'accountingAccountMemo',
			'flagEntity'  => 1,
			'flagAccount' => 1,
		));
	}

	/**
	 *
	 */
	protected function _iniNaviFormatReload()
	{
		global $varsPluginAccountingAccount;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingAccount['stampUpdate'],
				'flagSearch'  => 0,
			));
		}

		$this->_setNaviFormatReload(array(
			'pathVars'    => $this->_childSelf['pathVarsJs'],
			'strColumn'   => 'jsonFixedAssetsEditorNaviFormat',
			'strTable'    => 'accountingAccountMemo',
			'flagEntity'  => 1,
			'flagAccount' => 1,
		));
	}

	/**
	 *
	 */
	protected function _iniDetailCalc()
	{
		global $classEscape;

		global $varsRequest;
		global $varsAccount;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		if (!$this->_checkCurrent()) {
			$this->_sendOldError();
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$data = $this->_getValueJsonDetailConfigValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));
		$arrValueConfig = $data['data'];
		$arrColumnJsonDetail = $data['dataColumn'];


		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$varsTarget = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget
		));

		$varsTargetJsonDetail = $this->_getValueJsonDetail(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValueJsonDetail = $this->_checkValueJsonDetail(array(
			'values' => $varsTargetJsonDetail
		));
		$arrValue['arr']['jsonDetail'] = $arrValueJsonDetail['arr'];

		$arrValue = $this->_checkValueDetail(array(
			'flagCalc'       => 1,
			'vars'           => $vars,
			'varsItem'       => $varsItem,
			'arrValue'       => $arrValue,
			'arrValueConfig' => $arrValueConfig,
		));

		$classCall = $this->_getClassCalcFixedAssets();
		$varsCalc = $classCall->allot((array(
			'flagStatus'      => 'calc',
			'flagDepMethod'   => $arrValue['arr']['jsonDetail']['flagDepMethod'],
			'arrValue'        => $arrValue['arr']['jsonDetail'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		)));

		$this->sendVars(array(
			'flag'    => 'calc',
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'varsDetail' => $varsCalc
			),
		));
	}

	/**
	 *
	 */
	protected function _iniDetailAdd()
	{
		global $classDb;
		global $classEscape;
		$dbh = $classDb->getHandle();

		global $varsRequest;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		if (!$this->_checkCurrent()) {
			$this->_sendOldError();
		}

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllInsert'] || $varsAuthority['flagMyInsert'])) {
			$this->_sendOldError();
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$data = $this->_getValueJsonDetailConfigValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValueConfig = $data['data'];
		$arrColumnJsonDetail = $data['dataColumn'];

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$varsTarget = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget
		));

		$varsTargetJsonDetail = $this->_getValueJsonDetail(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValueJsonDetail = $this->_checkValueJsonDetail(array(
			'values' => $varsTargetJsonDetail,
		));
		$arrValue['arr']['jsonDetail'] = $arrValueJsonDetail['arr'];

		$arrValue = $this->_checkValueDetail(array(
			'idTarget'       => 0,
			'vars'           => $vars,
			'varsItem'       => $varsItem,
			'arrValue'       => $arrValue,
			'arrValueConfig' => $arrValueConfig,
		));

		try {
			$dbh->beginTransaction();

			$this->_setDbLog($vars, $arrValue, $arrValueConfig, $arrColumnJsonDetail);
			$this->_updateDbPreferenceStamp(array('strColumn' => 'logFixedAssets'));
			$this->_updateDbPreferenceStamp(array('strColumn' => 'fixedAssets'));

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
		$this->_setSearch(array('flag' => 1));
	}

	/**

	 */
	protected function _getValueJsonDetailConfigValue($arr)
	{
		global $classEscape;

		$data = array();
		$dataColumn = array();
		$array = $arr['vars'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'JsonDetail') {
				$arrayDetail = $value['varsFormSensitive']['varsTmpl']['varsDetail'];
				foreach ($arrayDetail as $keyDetail => $valueDetail) {
					if ($valueDetail['flagVersionColumnNone']) {
						continue;
					}
					$id = $classEscape->toLower(array('str' => $arrayDetail[$keyDetail]['id']));
					$data[$id] = $arrayDetail[$keyDetail]['value'];
					$dataColumn[] = $id;
				}
				$dataColumn[] = 'flagDepRateType';
				return array(
					'data'       => $data,
					'dataColumn' => $dataColumn,
				);
			}
		}
	}

	/**
	 * $arr = array(
	 *     'values' => array,
	 * )
	 */
	protected function _checkValueJsonDetail($arr)
	{
		global $classCheck;
		global $classEscape;

		$array = $classCheck->checkValue(array('arr' => $arr['values']));

		$arrayNew = array();
		$arrayNewColumn = array();
		$arrayNewValue = array();
		foreach ($array as $key => $value) {
			$array[$key] = $this->_checkValueSelect($array[$key]);
			if ($array[$key]['flagErrorNow']) {
				if ($array[$key]['flagTag'] == 'select') {
					$this->sendVars(array(
						'flag'    => 'strRemove',
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(),
					));

				} else {
					if (FLAG_TEST) {
						var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
						var_dump($value);
					}
					exit;
				}
			}

			$id = $array[$key]['id'];
			$id = $classEscape->toLower(array('str' => $id));
			$arrayNew[$id] = $array[$key]['value'];
			$arrayNewColumn[] = $id;
			$arrayNewValue[] = $array[$key]['value'];
		}

		$data = array(
			'arr'       => $arrayNew,
			'arrColumn' => $arrayNewColumn,
			'arrValue'  => $arrayNewValue,
		);

		return $data;
	}

	protected function _checkValueSelect(&$vars)
	{
		if ($vars['flagTag'] != 'select') {
			return $vars;
		}

		$array = $vars['arrayOption'];
		if (!$array) {
			return $vars;
		}

		if ($vars['flagMultiple']) {
			$flag = 0;
			$num = 0;
			if (!$vars['value']) {
				return $vars;
			}
			foreach ($array as $key => $value) {
				if (is_null($vars['value'][$value['value']])) {
					$flag = 1;
				}
				$num++;
				$vars['value'][$value['value']] = ((int) $vars['value'][$value['value']])? 1 : 0;
			}
			if (count($array) != $num) {
				$flag = 2;
			}

			if (!$flag) {
				return $vars;
			}

		} else {
			foreach ($array as $key => $value) {
				if ($value['value'] == $vars['value']) {
					return $vars;
				}
			}
		}

		$vars['flagErrorNow'] = 1;

		return $vars;
	}



	/**

	 */
	protected function _getValueJsonDetail($arr)
	{
		global $varsRequest;

		$array = $arr['vars'];
		$formValue = $varsRequest['query']['jsonValue']['vars']['JsonDetail'];

		foreach ($array as $key => $value) {
			if ($value['id'] == 'JsonDetail') {
				$arrayDetail = $value['varsFormSensitive']['varsTmpl']['varsDetail'];
				foreach ($arrayDetail as $keyDetail => $valueDetail) {
					$str = $arrayDetail[$keyDetail]['id'];
					$data = $formValue[$str];
					if	(is_null($data) && $arrayDetail[$keyDetail]['flagMustUse']) {
						if (FLAG_TEST) {
							var_dump($varsRequest['query']);
							var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
							var_dump($valueDetail);
						}
						exit;
					}
					$arrayDetail[$keyDetail]['value'] = $data;
				}
				return $arrayDetail;
			}
		}
	}

	/**

	 */
	protected function _checkValueDetail($arr)
	{
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		//strTitle
		if (!$arr['flagCalc']) {
			$flag = $this->_checkStrTitle(array(
				'strTitle'        => $arr['arrValue']['arr']['strTitle'],
				'idTarget'        => $arr['idTarget'],
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			));
			if ($flag) {
				$this->sendVars(array(
					'flag'    => 'strTitle',
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));
			}
			$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
			if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
				$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
				$numFiscalPeriod = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];
				$flag = $this->_checkStrTitle(array(
					'strTitle'        => $arr['arrValue']['arr']['strTitle'],
					'idTarget'        => $arr['idTarget'],
					'numFiscalPeriod' => $numFiscalPeriod,
					'flagTempNext'    => 1,
				));
				if ($flag) {
					$this->sendVars(array(
						'flag'    => 'strTitleTempNext',
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(),
					));
				}
			}
		}

		//tax
		$this->_checkValueDetailTax(array(
			'arrValue'       => &$arr['arrValue']['arr']['jsonDetail'],
			'arrValueConfig' => $arr['arrValueConfig'],
		));

		//stamp
		$this->_checkValueDetailStamp(array(
			'vars'     => $arr['vars'],
			'varsItem' => $arr['varsItem'],
			'arrValue' => &$arr['arrValue']['arr']['jsonDetail']
		));

		//format
		$this->_checkValueDetailFormat(array(
			'arrValue'       => &$arr['arrValue']['arr']['jsonDetail'],
			'arrValueConfig' => $arr['arrValueConfig'],
		));

		//num
		if (!$arr['flagCalc']) {
			$this->_checkValueDetailNum(array(
				'vars'           => $arr['vars'],
				'varsItem'       => $arr['varsItem'],
				'arrValue'       => &$arr['arrValue']['arr']['jsonDetail'],
				'arrValueConfig' => $arr['arrValueConfig'],
			));
			$arr['arrValue']['arr']['jsonDetail'] = $this->_checkValueDetailNumCalc(array(
				'vars'           => $arr['vars'],
				'varsItem'       => $arr['varsItem'],
				'arrValue'       => $arr['arrValue']['arr']['jsonDetail'],
				'arrValueConfig' => $arr['arrValueConfig'],
			));

		}

		return $arr['arrValue'];
	}

	/**

	 */
	protected function _checkValueDetailNumCalc($arr)
	{
		global $varsPluginAccountingAccount;

		if ($arr['arrValue']['flagDepMethod'] == 'sum' || $arr['arrValue']['flagDepMethod'] == 'noneDep') {
			return $arr['arrValue'];
		}

		$classCall = $this->_getClassCalcFixedAssets();
		$varsCalc = $classCall->allot((array(
			'flagStatus'      => 'calc',
			'flagDepMethod'   => $arr['arrValue']['flagDepMethod'],
			'arrValue'        => $arr['arrValue'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		)));

		$arrayCheck = array();
		$array = $varsCalc;
		foreach ($array as $key => $value) {
			if ($key == 'flagDepRateType') {
				$arr['arrValue'][$key] = $value;
			}
			$arrayCheck[$key] = $arr['arrValue'][$key];
		}

		$arrayCheck = json_decode(json_encode($arrayCheck), true);
		$varsCalc = json_decode(json_encode($varsCalc), true);
		$array = $varsCalc;
		foreach ($array as $key => $value) {
			if ($arrayCheck[$key] != $value) {
				$this->sendVars(array(
					'flag'    => 'strCalcMust',
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => (FLAG_TEST)? array($key) : array(),
				));
			}
		}

		return $arr['arrValue'];
	}

	/**

	 */
	protected function _checkValueDetailTax($arr)
	{
		if ($arr['arrValue']['flagTaxFixed'] == 'none' || $arr['arrValue']['flagTaxFixed'] == 'free') {
			$arr['arrValue']['flagTaxFixedType'] = $arr['arrValueConfig']['flagTaxFixedType'];
		}
		if ($arr['arrValue']['flagDepDown'] == 'none') {
			$arr['arrValue']['stampDrop'] = $arr['arrValueConfig']['stampDrop'];
		}
	}

	/**

	 */
	protected function _checkValueDetailFormat($arr)
	{
		$arrayStr = $arr['arrValue'];
		foreach ($arrayStr as $key => $value) {
			if ($arr['arrValue'][$key] == '') {
				$arr['arrValue'][$key] = $arr['arrValueConfig'][$key];
				continue;
			}
			if (preg_match( "/^numRatio$/", $key) || $key == 'numVolume') {
				if (!preg_match( "/^[0-9]{1,3}\.[0-9]{2,2}$/", $arr['arrValue'][$key])) {
					$this->_sendVarsCheck(__LINE__);
				}
			} elseif ($key == 'numRateDep') {
				if (!(preg_match( "/^[0]\.[0-9]{3,5}$/", $arr['arrValue'][$key]) || $arr['arrValue'][$key] == 1)) {
					$this->_sendVarsCheck(__LINE__);
				}
			}
		}
	}

	/**

	 */
	protected function _checkValueDetailNum($arr)
	{
		$data = array();
		$arrayStr = $arr['arrValue'];
		foreach ($arrayStr as $key => $value) {
			if (preg_match( "/^numValue/", $key)) {
				$data[$key] = $arr['arrValue'][$key];
				if ($arr['arrValue'][$key] == '') {
					$data[$key] = 0;
				}
				if ($key == 'numValueDepCurrentOver') {
					continue;
				}
				if ($data[$key] < 0) {
					$this->_sendVarsCheck($key.$value.__LINE__);
				}
			}
		}

		//numValue
		if ($data['numValue'] <= 0) {
			$this->_sendVarsCheck(__LINE__);
		}

		if ($arr['arrValue']['flagDepMethod'] != 'sum' && $arr['arrValue']['flagDepMethod'] != 'noneDep') {
			if ($data['numValue'] < $data['numValueRemainingBook']) {
				$this->_sendVarsCheck(__LINE__);
			}
		} else {
			$arr['arrValue']['numValueRemainingBook'] = $arr['arrValueConfig']['numValueRemainingBook'];
		}


		//NumValueCompression

		//NumValueNet
		$numValueNet = $data['numValue'] - $data['numValueCompression'];
		if ($data['numValueNet'] != $numValueNet) {
			$this->_sendVarsCheck(__LINE__);
		}

		//NumValueAccumulated
		if ($arr['arrValue']['stampStart'] < $arr['vars']['varsItem']['varsStampTerm']['stampMin']) {
			if ($data['numValueAccumulated']) {
				$this->_sendVarsCheck(__LINE__);
			}
		}

		if ($arr['arrValue']['flagDepMethod'] == 'sum') {
			return;
		}

		//NumValueNetOpening
		$numValueNetOpening = $numValueNet - $data['numValueAccumulated'];
		if ($data['numValueNetOpening'] != $numValueNetOpening) {
			$this->_sendVarsCheck(__LINE__);

		}
		if ($arr['arrValue']['flagDepMethod'] != 'noneDep') {
			if ($data['numValueNetOpening']  < $data['numValueRemainingBook']) {
				$this->_sendVarsCheck(__LINE__);
			}
		}

		$flag20070331 = 0;
		if ($arr['arrValue']['stampStart'] < $arr['vars']['varsItem']['varsStamp']['flagDepMethod']) {
			$flag20070331 = 1;
		}

		//NumValueDepCalcBase
		if ($arr['arrValue']['flagDepMethod'] == 'declining') {
			$numValueDepCalcBase = $data['numValueNetOpening']
					+ $data['numValueDepPrevOver']
					- $data['numValueDepSpecialShortPrev'];

		} elseif ($arr['arrValue']['flagDepMethod'] == 'straight') {
			if ($flag20070331) {
				$flagType = $arr['varsItem']['varsFixedAssets']['flagFractionDepSurvivalRate'];
				$numSurvivalRate = $this->_updateCalc(array(
					'flagType' => $flagType,
					'num'      => $numValueNet * $arr['arrValue']['numSurvivalRate'] / 100,
					'numLevel' => 0
				));
				$numValueDepCalcBase = $numValueNet - $numSurvivalRate;
			} else {
				$numValueDepCalcBase = $numValueNet;
			}

		} elseif ($arr['arrValue']['flagDepMethod'] == 'average'
			|| $arr['arrValue']['flagDepMethod'] == 'one'
		) {
			$numValueDepCalcBase = $numValueNet;
		}

		if ($data['numValueDepCalcBase'] != $numValueDepCalcBase) {
			$this->_sendVarsCheck(__LINE__);
		}

		//NumValueDepPrevOver
		if ($arr['arrValue']['stampStart'] < $arr['vars']['varsItem']['varsStampTerm']['stampMin']) {
			if ($data['numValueDepPrevOver']) {
				$this->_sendVarsCheck(__LINE__);
			}
		}

		$numValueNetOpeningTax = $data['numValueNetOpening'] + $data['numValueDepPrevOver'];
		$numValueNetClosingTax = $numValueNetOpening + $data['numValueDepPrevOver'] - $data['numValueDepLimit'];
		if ($arr['arrValue']['flagDepMethod'] == 'straight'
			|| $arr['arrValue']['flagDepMethod'] == 'declining'
			|| $arr['arrValue']['flagDepMethod'] == 'average'
			|| $arr['arrValue']['flagDepMethod'] == 'one'
		) {
			if ($numValueNetOpeningTax > $numValueNet) {
				$this->_sendVarsCheck(__LINE__);

			} elseif ($numValueNetClosingTax < 0) {
				$this->_sendVarsCheck(__LINE__);

			} elseif ($numValueNetClosingTax < $data['numValueRemainingBook']) {
				$this->_sendVarsCheck(__LINE__);
			}
		}


		//NumValueAssured

		//NumValueDepCalc

		//arrCommaDepMonth

		//NumRateDep

		//numValueAssured

		//NumValueDepUp
		//NumValueDepExtra
		//NumValueDepSpecial
		//NumValueDepSpecialShortPrev
		if ($arr['arrValue']['stampStart'] < $arr['vars']['varsItem']['varsStampTerm']['stampMin']) {
			if ($data['numValueDepSpecialShortPrev']) {
				$this->_sendVarsCheck(__LINE__);
			}
		}

		//NumValueDepLimit
		$numValueDepLimit = $data['numValueDepCalc']
						+ $data['numValueDepUp']
						+ $data['numValueDepExtra']
						+ $data['numValueDepSpecial']
						+ $data['numValueDepSpecialShortPrev'];

		if ($data['numValueDepLimit'] != $numValueDepLimit) {
			$this->_sendVarsCheck(__LINE__);

		} elseif ($data['numValueDepLimit'] < 0) {
			$this->_sendVarsCheck(__LINE__);
		}

		//NumValueDep


		//NumValueAccumulatedClosing
		$numValueAccumulatedClosing = $data['numValueAccumulated'] + $data['numValueDep'];
		if ($data['numValueAccumulatedClosing'] != $numValueAccumulatedClosing) {
			$this->_sendVarsCheck(__LINE__);
		}

		//NumValueNetClosing
		$numValueNetClosing = $data['numValueNetOpening'] - $data['numValueDep'];
		if ($arr['arrValue']['stampDrop'] != '') {
			$numValueNetClosing = 0;
		}

		if ($data['numValueNetClosing'] != $numValueNetClosing) {
			$this->_sendVarsCheck(__LINE__);

		} elseif ($data['numValueNetClosing']  < 0) {
			$this->_sendVarsCheck(__LINE__);

		} elseif ($data['numValueNetClosing']  < $data['numValueRemainingBook']) {
			if ($arr['arrValue']['stampDrop'] == '') {
				$this->_sendVarsCheck(__LINE__);
			}

		}

		//NumValueDepOperate
		$varsEntityNation = $arr['varsItem']['varsEntityNation'];
		if ($varsEntityNation['flagCorporation'] != 1) {
			$flagType = $arr['varsItem']['varsFixedAssets']['flagFractionRatioOperate'];
			$numValueDepOperate = $this->_updateCalc(array(
				'flagType' => $flagType,
				'num'      => $numValueDep * $arr['arrValue']['numRatioOperate'] / 100,
				'numLevel' => 0
			));
			if ($data['numValueDepOperate'] != $numValueDepOperate) {
				$this->_sendVarsCheck(__LINE__);
			}
		} else {
			$arr['arrValue']['numValueDepOperate'] = $data['numValueDep'];
		}

		//NumValueDepCurrentOver
		$numValueDepCurrentOver = $data['numValueDep'] - $numValueDepLimit;
		if ($data['numValueDepCurrentOver'] != $numValueDepCurrentOver) {
			$this->_sendVarsCheck(__LINE__);
		}

		//NumValueDepNextOver
		$numValueDepNextOver = $data['numValueDepPrevOver'] + $numValueDepCurrentOver;
		if ($numValueDepNextOver < 0) {
			$numValueDepNextOver = 0;
		}
		if ($data['numValueDepNextOver'] != $numValueDepNextOver) {
			$this->_sendVarsCheck(__LINE__);
		}

		//NumValueDepSpecialShortCurrent
		$sumValueDepLaw = $numValueDepLimit - $data['numValueDepCalc'] - $data['numValueDepUp'];
		$numValueDepSpecialShortCurrent = 0;
		if ($numValueDepCurrentOver < 0 && $sumValueDepLaw > 0) {
			if (abs($numValueDepCurrentOver) < abs($sumValueDepLaw)) {
				$numValueDepSpecialShortCurrent = abs(numValueDepCurrentOver);

			} else {
				$numValueDepSpecialShortCurrent = abs($sumValueDepLaw);
			}
		}
		if ($data['numValueDepSpecialShortCurrent'] != $numValueDepSpecialShortCurrent) {
			$this->_sendVarsCheck(__LINE__);
		}

		//NumValueDepSpecialShortCurrentCut
		if ($data['numValueDepSpecialShortCurrentCut'] > $numValueDepSpecialShortCurrent) {
			$this->_sendVarsCheck(__LINE__);
		}

		//NumValueDepSpecialShortNext
		$numValueDepSpecialShortNext = $numValueDepSpecialShortCurrent - $data['numValueDepSpecialShortCurrentCut'];
		if ($data['numValueDepSpecialShortNext'] != $numValueDepSpecialShortNext) {
			$this->_sendVarsCheck(__LINE__);

		} elseif ($data['numValueDepSpecialShortNext'] < 0) {
			$this->_sendVarsCheck(__LINE__);
		}

	}

	/**

	 */
	protected function _checkValueDetailStamp($arr)
	{
		$data = array();
		$arrayStr = array('stampBuy', 'stampStart', 'stampEnd', 'stampDrop');
		foreach ($arrayStr as $key => $value) {
			$data[$value] = $this->_getValueDetailStamp(array(
				'strValue' => $arr['arrValue'][$value]
			));
			if ($data[$value] > $arr['varsItem']['varsStampTerm']['stampMax']) {
				$this->_sendVarsCheck(__LINE__);
			}
			if ($data[$value] < $arr['vars']['varsItem']['varsStamp']['stampMeiji']) {
				$this->_sendVarsCheck(__LINE__);
			}
			$arr['arrValue'][$value] = $data[$value];
		}

		if ($data['stampBuy'] > $data['stampStart']) {
			$this->_sendVarsCheck(__LINE__);
		}

		if ($data['stampEnd'] != '') {
			if ($data['stampStart'] > $data['stampEnd']) {
				$this->_sendVarsCheck(__LINE__);
			}
		}

		if ($data['stampDrop'] != '') {
			if ($data['stampStart'] > $data['stampDrop']) {
				$this->_sendVarsCheck(__LINE__);
			}
		}

		if ($data['stampDrop'] != '' && $data['stampEnd'] != '') {
			if ($data['stampDrop'] < $data['stampEnd']) {
				$this->_sendVarsCheck(__LINE__);
			}
		}

	}

	/**
	 */
	protected function _sendVarsCheck($str)
	{
		$this->sendVars(array(
			'flag'    => 'strCheck',
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => (FLAG_TEST)? array($str) : array(),
		));
	}

	/**
		(array(
			'value' => $arr['arrValue']['arr']['stampBuy']
		))
	 */
	protected function _getValueDetailStamp($arr)
	{
		global $varsAccount;

		$numTimeZone = PLUGIN_ACCOUNTING_NUM_TIME_ZONE;

		if ($arr['strValue'] == '') {
			return '';
		}
		$strValue = $arr['strValue'];
		preg_match( "/^([0-9]{4})\/([0-9]{1,2})\/([0-9]{1,2})$/", $strValue, $arrMatch);
		list($strStamp, $numYear, $numMonth, $numDate, $numHour, $numMin) = $arrMatch;

		$strTimeZone = (-1 * $varsAccount['numTimeZone']) . 'hours';
		$dateTime = new DateTime("$numYear-$numMonth-$numDate 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stamp = $dateTime->format('U') + $numHour * 3600 + $numMin * 60;

		return $stamp;
	}

	/**
		$this->_checkStrTitle(array(
			'strTitle' => '',
			'idTarget' => 0,
		));
	 */
	protected function _checkStrTitle($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $varsPluginAccountingAccount;

		$arrWhere = array(
			array(
				'flagType'      => 'num',
				'strColumn'     => 'idEntity',
				'flagCondition' => 'eq',
				'value'         => $varsPluginAccountingAccount['idEntityCurrent'],
			),
			array(
				'flagType'      => '',
				'strColumn'     => 'strTitle',
				'flagCondition' => 'eq',
				'value'         => $arr['strTitle'],
			),
		);

		//because allot temp error now error
		if ($arr['flagTempNext']) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'numFiscalPeriod',
				'flagCondition' => 'eq',
				'value'         => $arr['numFiscalPeriod'],
			);

		} else {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'numFiscalPeriod',
				'flagCondition' => 'eqSmall',
				'value'         => $arr['numFiscalPeriod'],
			);
		}

		if ($arr['idTarget']) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idFixedAssets',
				'flagCondition' => 'ne',
				'value'         => $arr['idTarget'],
			);
		}

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);
		$flag = 0;
		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogFixedAssets' . $strNation,
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => $arrWhere,
		));

		if ($rows['numRows']) {
			return 1;
		}
	}




	/**

	 */
	protected function _setDbLog($vars, $arrValue, $arrValueConfig, $arrColumnJsonDetail)
	{
		global $classEscape;
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsAccount;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$arrColumn = array(
			'stampRegister',
			'stampUpdate',
			'idFixedAssets',
			'idAccount',
			'idEntity',
			'numFiscalPeriod',
			'strTitle',
			'strMemo',
			'arrSpaceStrTag',
			'jsonChargeHistory',
			'jsonVersion',
		);

		$flagSum = 0;
		if ($arrValue['arr']['jsonDetail']['flagDepMethod'] == 'sum') {
			$flagSum = 1;
		}

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$varsIdNumber = $this->_getIdAutoIncrement(array(
			'idTarget' => 'idFixedAssets'
		));
		if (!$varsIdNumber[$idEntity]) {
			$varsIdNumber[$idEntity] = 1;
		}
		$idFixedAssets = $varsIdNumber[$idEntity];

		$arrayNew = array();
		$array = $arrColumn;
		foreach ($array as $key => $value) {
			if ($value == 'stampRegister' || $value == 'stampUpdate') {
				$arrayNew[] = TIMESTAMP;

			} elseif ($value == 'idAccount') {
				$arrayNew[] = $varsAccount['id'];

			} elseif ($value == 'idFixedAssets') {
				$arrayNew[] = $idFixedAssets;

			} elseif ($value == 'idEntity') {
				$arrayNew[] = $idEntity;

			} elseif ($value == 'numFiscalPeriod') {
				$arrayNew[] = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];

			} elseif ($value == 'arrSpaceStrTag') {
				$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrValue['arr']['arrSpaceStrTag']));
				$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));
				$arrayNew[] = $arrSpaceStrTag;

			} elseif ($value == 'jsonVersion') {
				$arrVersion = array();
				$arrVersion[] = $this->_getDbLogVarsVersion($vars, $arrValue, $arrValueConfig, $arrColumnJsonDetail);
				$jsonVersion = json_encode($arrVersion);
				$this->checkTextSize(array(
					'flag' => 'errorDataMax',
					'str'  => $jsonVersion,
				));
				$arrayNew[] = $jsonVersion;

			} elseif ($value == 'jsonChargeHistory') {
				$arrChargeHistory = array(
					array(
						'stampRegister' => TIMESTAMP,
						'idAccount'     => $varsAccount['id'],
					),
				);
				$jsonChargeHistory = json_encode($arrChargeHistory);
				$this->checkTextSize(array(
					'flag' => 'errorDataMax',
					'str'  => $jsonChargeHistory,
				));
				$arrayNew[] = $jsonChargeHistory;

			} else {
				$arrayNew[] = $arrValue['arr'][$value];
			}
		}


		$array = $arrColumnJsonDetail;
		foreach ($array as $key => $value) {
			$arrColumn[] = $value;
			if (is_null($arrValue['arr']['jsonDetail'][$value])) {
				$arrayNew[] = $arrValueConfig[$value];
			} else {
				$arrayNew[] = $arrValue['arr']['jsonDetail'][$value];
			}
		}
		$arrValue = $arrayNew;

		$id = $classDb->insertRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingLogFixedAssets' . $strNation,
			'arrColumn' => $arrColumn,
			'arrValue'  => $arrValue,
		));

		$varsIdNumber[$idEntity]++;
		$this->_updateIdAutoIncrement(array(
			'idTarget'   => 'idFixedAssets',
			'varsTarget' => $varsIdNumber
		));

		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		if ($flagSum) {
			$this->_updateDbFixedAssets(array(
				'numFiscalPeriod' => $numFiscalPeriod,
			));
		}

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^tempPrev$/", $flagCurrentFlagNow)) {
			$numFiscalPeriod++;
			$array = $arrColumn;
			foreach ($array as $key => $value) {
				if ($value == 'numFiscalPeriod') {
					$arrValue[$key] = $numFiscalPeriod;
					break;
				}
			}
			$id = $classDb->insertRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingLogFixedAssets' . $strNation,
				'arrColumn' => $arrColumn,
				'arrValue'  => $arrValue,
			));
			$classFixedAssets = $this->_getClassPreference(array('flagType' => 'NextFixedAssets'));
			$classFixedAssets->allot(array(
				'flagStatus'       => 'update',
				'numFiscalPeriod'  => $numFiscalPeriod,
				'idTarget'         => $idFixedAssets,
			));
			if ($flagSum) {
				$this->_updateDbFixedAssets(array(
					'numFiscalPeriod' => $numFiscalPeriod,
				));
			}
		}
	}

	/**

	 */
	protected function _getDbLogVarsVersion($vars, $arrValue, $arrValueConfig, $arrColumnJsonDetail)
	{
		global $classEscape;

		global $varsAccount;
		global $varsPluginAccountingAccount;

		$arrColumn = array(
			'stampRegister',
			'stampUpdate',
			'strTitle',
			'strMemo',
			'arrSpaceStrTag',
		);

		$arrayNew = array();
		$array = $arrColumn;
		foreach ($array as $key => $value) {
			if ($value == 'stampRegister' || $value == 'stampUpdate') {
				$arrayNew[$value] = TIMESTAMP;

			} elseif ($value == 'arrSpaceStrTag') {
				$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrValue['arr']['arrSpaceStrTag']));
				$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));
				$arrayNew[$value] = $arrSpaceStrTag;

			} else {
				$arrayNew[$value] = $arrValue['arr'][$value];
			}
		}

		$array = $arrColumnJsonDetail;
		foreach ($array as $key => $value) {
			if (is_null($arrValue['arr']['jsonDetail'][$value])) {
				$arrayNew['jsonDetail'][$value] = $arrValueConfig[$value];
			} else {
				$arrayNew['jsonDetail'][$value] = $arrValue['arr']['jsonDetail'][$value];
			}
		}

		return $arrayNew;
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

		$flag = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done)$/", $flag)) {
			$this->_sendOldError();
		}

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'] || $varsAuthority['flagMyUpdate'])) {
			$this->_sendOldError();
		}

		$varsLog = $this->_getVarsLog(array(
			'idTarget'   => $varsRequest['query']['jsonValue']['idTarget'],
			'flagRemove' => 0,
		));
		if (!$varsLog) {
			$this->_sendOldError();

		} else {
			if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'])) {
				if ($varsAuthority['flagMyUpdate']) {
					if ($varsLog['idAccount'] != $varsAccount['id']) {
						$this->_sendOldError();
					}
				}
			}
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$data = $this->_getValueJsonDetailConfigValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));
		$arrValueConfig = $data['data'];
		$arrColumnJsonDetail = $data['dataColumn'];

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$varsTarget = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget
		));

		$varsTargetJsonDetail = $this->_getValueJsonDetail(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValueJsonDetail = $this->_checkValueJsonDetail(array(
			'values' => $varsTargetJsonDetail
		));
		$arrValue['arr']['jsonDetail'] = $arrValueJsonDetail['arr'];

		$arrValue = $this->_checkValueDetail(array(
			'idTarget'       => $varsRequest['query']['jsonValue']['idTarget'],
			'vars'           => $vars,
			'varsItem'       => $varsItem,
			'arrValue'       => $arrValue,
			'arrValueConfig' => $arrValueConfig,
		));

		try {
			$dbh->beginTransaction();

			$this->_updateDbLog($vars, $arrValue, $arrValueConfig, $arrColumnJsonDetail, $varsLog);
			$this->_updateDbPreferenceStamp(array('strColumn' => 'logFixedAssets'));
			$this->_updateDbPreferenceStamp(array('strColumn' => 'fixedAssets'));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$this->_iniSearchDetail();

	}

	/**

	 */
	protected function _updateDbLog($vars, $arrValue, $arrValueConfig, $arrColumnJsonDetail, $varsLog)
	{
		global $classEscape;
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsAccount;
		global $varsRequest;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$arrColumn = array(
			'stampUpdate',
			'strTitle',
			'strMemo',
			'arrSpaceStrTag',
			'jsonVersion',
		);

		$idFixedAssets = $varsRequest['query']['jsonValue']['idTarget'];

		$flagSum = 0;
		if ($arrValue['arr']['jsonDetail']['flagDepMethod'] == 'sum') {
			$flagSum = 1;
		}

		$arrayNew = array();
		$array = $arrColumn;
		foreach ($array as $key => $value) {
			if ($value == 'stampUpdate') {
				$arrayNew[] = TIMESTAMP;

			} elseif ($value == 'arrSpaceStrTag') {
				$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrValue['arr']['arrSpaceStrTag']));
				$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));
				$arrayNew[] = $arrSpaceStrTag;

			} elseif ($value == 'jsonVersion') {
				$arrVersion = $varsLog['jsonVersion'];
				$arrVersion[] = $this->_getDbLogVarsVersion($vars, $arrValue, $arrValueConfig, $arrColumnJsonDetail);
				$jsonVersion = json_encode($arrVersion);
				$this->checkTextSize(array(
					'flag' => 'errorDataMax',
					'str'  => $jsonVersion,
				));
				$arrayNew[] = $jsonVersion;

			} else {
				$arrayNew[] = $arrValue['arr'][$value];
			}
		}

		$array = $arrColumnJsonDetail;
		foreach ($array as $key => $value) {
			$arrColumn[] = $value;
			if (is_null($arrValue['arr']['jsonDetail'][$value])) {
				$arrayNew[] = $arrValueConfig[$value];
			} else {
				$arrayNew[] = $arrValue['arr']['jsonDetail'][$value];
			}
		}
		$arrValue = $arrayNew;

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable' => 'accountingLogFixedAssets' . $strNation,
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
					'flagCondition' => 'eqBig',
					'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idFixedAssets',
					'flagCondition' => 'eq',
					'value'         => $idFixedAssets,
				),
			),
			'arrValue'  => $arrValue,
		));

		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		if ($flagSum) {
			$this->_updateDbFixedAssets(array(
				'numFiscalPeriod' => $numFiscalPeriod,
			));
		}

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
			$numFiscalPeriod++;
			$classFixedAssets = $this->_getClassPreference(array('flagType' => 'NextFixedAssets'));
			$classFixedAssets->allot(array(
				'flagStatus'       => 'update',
				'numFiscalPeriod'  => $numFiscalPeriod,
				'idTarget'         => $idFixedAssets,
			));
			if ($flagSum) {
				$this->_updateDbFixedAssets(array(
					'numFiscalPeriod' => $numFiscalPeriod,
				));
			}
		}
	}
}
