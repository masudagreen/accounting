<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcTempNext_AccountTitleBatch14311 extends Code_Else_Plugin_Accounting_Jpn_JpnBatch14311
{
	protected $_extChildSelf = array(

	);

	/**
	 * tempNext only
	 */
	public function run()
	{
		if (FLAG_TEST) {
			var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
		}
		exit;
	}

	/*

	 * */
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
			'flagStatus'      => 'add',
			'numFiscalPeriod' => $numFiscalPeriod,
			'flagFS'          => $arr['flagFS'],
			'idTarget'        => $arr['idTarget'],
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
		))
	 */
	protected function _iniAdd($arr)
	{
		$varsItem = $this->_getVarsItem(array(
			'flagFS'          => $arr['flagFS'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$flag = $this->_checkAddValue(array(
			'varsItem'        => $varsItem,
			'flagFS'          => $arr['flagFS'],
			'idTarget'        => &$arr['idTarget'],
			'arrValue'        => &$arr['arrValue'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		if ($flag) {
			return $flag;
		}

		$flag = $this->_setAddDb(array(
			'flagStatus'      => $arr['flagStatus'],
			'varsItem'        => $varsItem,
			'flagFS'          => $arr['flagFS'],
			'idTarget'        => $arr['idTarget'],
			'arrValue'        => $arr['arrValue'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		if ($flag) {
			return $flag;
		}
	}

	/**

	 */
	protected function _getVarsItem($arr)
	{
		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$arrAccountTitle = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => array(),
			'numFiscalPeriod'    => $arr['numFiscalPeriod'],
		));

		$varsJgaapFS = $this->_getVarsItemJgaapFS(array(
			'arrStrTitle'  => array(),
			'arrSelectTag' => array(),
			'vars'         => $varsFS['jsonJgaapFS' . $arr['flagFS']],
		));

		$arrayFSList = $this->_getFSList(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$varsFSItem = $this->_getVarsFSItem();

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$varsConsumptionTax = $this->_getVarsConsumptionTax(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$data = array(
			'varsFS'              => $varsFS,
			'arrAccountTitle'     => $arrAccountTitle,
			'varsJgaapFS'         => $varsJgaapFS,
			'arrayFSList'         => $arrayFSList,
			'varsFSItem'          => $varsFSItem,
			'varsEntityNation'    => $varsEntityNation,
			'varsConsumptionTax'  => $varsConsumptionTax,
		);

		return $data;
	}

	/**
		(array(
			'arrStrTitle'  => array(),
			'vars'         => array(),
			'arrSelectTag' => array(),
		))
	 */
	protected function _getVarsItemJgaapFS($arr)
	{
		$arrSelectTag = &$arr['arrSelectTag'];
		$arrStrTitle = &$arr['arrStrTitle'];

		$array = &$arr['vars'];
		foreach ($array as $key => $value) {
			$arr['arrStrTitle'][$value['vars']['idTarget']] = $value['strTitle'];

			$arrLevel = preg_split("/-/", $value['id']);
			$num = count($arrLevel) - 2;
			$arrLevel = array();
			for ($i = 0 ; $i < $num; $i++) {
				$arrLevel[$i] = '';
			}
			$str =  ' ' . join('.', $arrLevel) . ' ';
			$strTitle = $str . $value['strTitle'];

			if (is_null($value['vars']['flagUse'])) {
				$arr['arrSelectTag'][] = array(
					'strTitle'     => $strTitle,
					'value'        => '',
					'flagDisabled' => 1,
				);

			} else {
				$arr['arrSelectTag'][] = array(
					'strTitle'     => $strTitle,
					'value'        => $value['vars']['idTarget'],
				);
			}

			if ($value['child']) {
				$data = $this->_getVarsItemJgaapFS(array(
					'vars'          => $array[$key]['child'],
					'arrSelectTag'  => $arr['arrSelectTag'],
					'arrStrTitle'   => $arr['arrStrTitle'],
				));
				$array[$key]['child'] = $data['vars'];
				$arrSelectTag =  $data['arrSelectTag'];
				$arrStrTitle =  $data['arrStrTitle'];
			}
		}

		return $arr;
	}

	/**
	 (array(
			'varsItem' => $varsItem,
			'flagFS'   => $arr['flagFS'],
			'idTarget' => $arr['idTarget'],
			'arrValue' => $arr['arrValue'],
	 ))
	 */
	protected function _checkAddValue($arr)
	{
		global $classEscape;

		//
		$arrayBlock =array();
		$arrayBlock = $this->_getTreeBlock(array(
			'vars'     => $arr['varsItem']['varsFS']['jsonJgaapAccountTitle'. $arr['flagFS']],
			'idTarget' => $arr['idTarget'],
		));

		if (!$arrayBlock) {
			if ($arr['arrValue']['arrCommaIdAccountTitle']) {
				$arrayData = $classEscape->splitCommaArrayData(array('data' => $arr['arrValue']['arrCommaIdAccountTitle']));
				foreach ($arrayData as $key => $value) {
					if ($value == 'insertPoint') {
						continue;
					}
					$arrayBlockTemp = $this->_getTreeBlock(array(
						'vars'     => $arr['varsItem']['varsFS']['jsonJgaapAccountTitle'. $arr['flagFS']],
						'idTarget' => $value,
					));

					if ($arrayBlockTemp) {
						$arr['idTarget'] = $value;
						$arrayBlock = $arrayBlockTemp;
						break;
					}
				}
			}
			if (!$arrayBlock) {
				return 'arrayblock';
			}
		}

		//
		$varsTarget = array();
		foreach ($arrayBlock as $key => $value) {
			if ($value['vars']['idTarget'] == $arr['idTarget']) {
				$varsTarget = $value;
				break;
			}
		}

		if (!(int) $varsTarget['vars']['flagSortUse']) {
			return 'flagSortUse';
		}

		//
		$strTitle = $arr['arrValue']['strTitle'];
		$flagNum = $this->_checkTreeStrTitle(array(
			'vars'      => $arr['varsItem']['varsFS']['jsonJgaapAccountTitle'. $arr['flagFS']],
			'strTarget' => $strTitle,
			'num'       => 0,
		));

		if ($flagNum) {
			return 'strTitleTempNext';
		}

		$idAccountTitle = $arr['arrValue']['idAccountTitle'];
		$arrayIdAccountTitle = array($idAccountTitle);
		$arrayFSList = $arr['varsItem']['arrayFSList'];
		foreach ($arrayFSList as $keyFSList => $valueFSList) {
			$arrayIdAccountTitle[] = 'custom_' . $keyFSList . '_' . $idAccountTitle;
		}
		foreach ($arrayIdAccountTitle as $key => $value) {
			$flag = $this->_checkValueIdAccountTitleFS(array(
				'varsItem' => $arr['varsItem'],
				'idTarget' => $value,
			));
			if ($flag) {
				return 'idAccountTitleTempNext';
			}
		}

		//
		$varsConsumptionTax = $arr['varsItem']['varsConsumptionTax'];
		if (!is_null($arr['arrValue']['flagConsumptionTaxGeneralRule'])) {
			if ($arr['varsItem']['varsEntityNation']['flagConsumptionTaxDeducted']) {
				if (!$varsConsumptionTax['arrStrGeneralEach'][$arr['arrValue']['flagConsumptionTaxGeneralRule']]) {
					return 'consumptionTax';
				}
			} else {
				if (!$varsConsumptionTax['arrStrGeneralProration'][$arr['arrValue']['flagConsumptionTaxGeneralRule']]) {
					return 'consumptionTax';
				}
			}
		}
		if (!is_null($arr['arrValue']['flagConsumptionTaxSimpleRule'])) {
			if (!$varsConsumptionTax['arrStrSimple'][$arr['arrValue']['flagConsumptionTaxSimpleRule']]) {
				return 'consumptionTax';
			}
		}

		//back
		if (!$arr['varsItem']['varsJgaapFS']['arrStrTitle'][$arr['arrValue']['idAccountTitleJgaapFS']]) {
			$classCalcTempNextAccountTitleFS = $this->_getClassCalc(array(
				'flagType'   => 'TempNext',
				'flagDetail' => 'AccountTitleFS',
			));
			$flag = $classCalcTempNextAccountTitleFS->allot(array(
				'flagStatus'      => 'back',
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
				'flagFS'          => $arr['flagFS'],
				'idTarget'        => $arr['arrValue']['idAccountTitleJgaapFS'],
			));
			if ($flag) {
				return $flag;
			}
		}

	}

	/**
		(array(

		))

	 */
	protected function _checkValueIdAccountTitleFS($arr)
	{
		if ($arr['varsItem']['arrAccountTitle']['arrStrTitle'][$arr['idTarget']]) {
			if ($arr['idSelf']) {
				if ($arr['idTarget'] != $arr['idSelf']) {
					return 1;
				}
			} else {
				return 1;
			}
		}
	}

	/**
		(array(
			'flagStatus'      => $arr['flagStatus'],
			'varsItem'        => $varsItem,
			'flagFS'          => $arr['flagFS'],
			'idTarget'        => $arr['idTarget'],
			'arrValue'        => $arr['arrValue'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _setAddDb($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$varsFS = $this->_getValueVarsFS(array(
			'flagStatus'  => $arr['flagStatus'],
			'arrValue'    => $arr['arrValue'],
			'varsItem'    => $arr['varsItem'],
			'flagFS'      => $arr['flagFS'],
			'idTarget'    => $arr['idTarget'],
		));

		$jsonAccountTitle = json_encode($varsFS);
		$flag = $this->checkTextSize(array(
			'flagReturn' => 1,
			'str'        => $jsonAccountTitle,
		));
		if ($flag) {
			return 'errorDataMax';
		}

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
			'arrValue'  => $arrDbValue,
		));
	}

	/**
		(array(
			'flagStatus'  => $arr['flagStatus'],
			'varsItem'    => $varsItem,
			'flagFS'      => $arr['flagFS'],
			'idTarget'    => $arr['idTarget'],
			'arrValue'    => $arr['arrValue'],
			'flagDefault' => $arr['arrValue']['flagDefault'],
		))
	 */
	protected function _getValueVarsFS($arr)
	{
		global $classEscape;

		$idTarget = $arr['arrValue']['idAccountTitle'];
		if ($arr['flagStatus'] == 'add' || ($arr['flagStatus'] == 'edit' && !$arr['flagDefault'])) {
			$idTarget = 'custom_' . $arr['flagFS'] . '_' . $arr['arrValue']['idAccountTitle'];
		}

		$arrayBlock = $this->_getTreeBlock(array(
			'vars'     => $arr['varsItem']['varsFS']['jsonJgaapAccountTitle'. $arr['flagFS']],
			'idTarget' => $arr['idTarget'],
			'arrNew'   => array(),
		));

		$varsTarget = array();
		if ($arr['flagStatus'] == 'edit') {
			foreach ($arrayBlock as $key => $value) {
				if ($value['vars']['idTarget'] == $arr['idTarget']) {
					$varsTarget = $value;
					break;
				}
			}
		}

		$arrayCheck = array();
		foreach ($arrayBlock as $key => $value) {
			$arrayCheck[$value['vars']['idTarget']] = $value;
		}

		$arrayIdAccountTitle = array();
		foreach ($arrayBlock as $key => $value) {
			$arrayIdAccountTitle[] = $value['vars']['idTarget'];
		}

		$arrayNext = array();
		$arrayData = $classEscape->splitCommaArrayData(array('data' => $arr['arrValue']['arrCommaIdAccountTitle']));
		foreach ($arrayData as $key => $value) {
			if ($value == 'insertPoint') {
				continue;
			}
			$arrayNext[] = $value;
		}

		$flag = 0;
		$arrayPrev = $arrayIdAccountTitle;
		foreach ($arrayPrev as $key => $value) {
			if ($arrayNext[$key] != $value) {
				$flag = 1;
			}
		}
		if (count($arrayPrev) != count($arrayNext)) {
			$flag = 1;
		}

		if ($arr['flagStatus'] == 'add') {
			if (!$flag) {
				$arrayIdAccountTitle = $arrayData;

			} else {
				$arrayPrev[] = 'insertPoint';
				$arrayIdAccountTitle = $arrayPrev;
			}

		} else {
			if (!$flag) {
				$arrayIdAccountTitle = $arrayData;
			}
		}

		$array = $arrayIdAccountTitle;
		$arrayNew = array();
		$tmplVars = $arr['varsItem']['varsFSItem']['varsAccountTitle'];
		foreach ($array as $key => $value) {
			if ($arr['flagStatus'] == 'add') {

				$flagConsumptionTaxGeneralRuleEach = 'none';
				$flagConsumptionTaxGeneralRuleProration = 'none';
				$flagConsumptionTaxSimpleRule = 'none';

				if (!is_null($arr['arrValue']['flagConsumptionTaxGeneralRule'])) {
					if ($arr['varsItem']['varsEntityNation']['flagConsumptionTaxDeducted']) {
						$flagConsumptionTaxGeneralRuleEach = $arr['arrValue']['flagConsumptionTaxGeneralRule'];
						$flagConsumptionTaxGeneralRuleProration = $flagConsumptionTaxGeneralRuleEach;
						$flagConsumptionTaxSimpleRule = $flagConsumptionTaxGeneralRuleEach;
						$flagVars = $arr['varsItem']['varsConsumptionTax']['generalEachChange'][$flagConsumptionTaxGeneralRuleEach];
						if ($flagVars) {
							$flagConsumptionTaxGeneralRuleProration = $flagVars['generalProration'];
							$flagConsumptionTaxSimpleRule = $flagVars['simple'];
						}

					} else {
						$flagConsumptionTaxGeneralRuleProration = $arr['arrValue']['flagConsumptionTaxGeneralRule'];
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

				if (!is_null($arr['arrValue']['flagConsumptionTaxSimpleRule'])) {
					$flagConsumptionTaxSimpleRule = $arr['arrValue']['flagConsumptionTaxSimpleRule'];
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
					$tmplVars['strTitle'] = $arr['arrValue']['strTitle'];
					$tmplVars['vars'] = array(
						'idTarget'                               => $idTarget,
						'idAccountTitleJgaapFS'                  => $arr['arrValue']['idAccountTitleJgaapFS'],
						'flagUse'                                => (int) $arr['arrValue']['flagUse'],
						'flagDebit'                              => (int) $arr['arrValue']['flagDebit'],
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
					if (!is_null($arr['arrValue']['flagConsumptionTaxGeneralRule'])) {
						if ($arr['varsItem']['varsEntityNation']['flagConsumptionTaxDeducted']) {
							$flagConsumptionTaxGeneralRuleEach = $arr['arrValue']['flagConsumptionTaxGeneralRule'];

						} else {
							$flagConsumptionTaxGeneralRuleProration = $arr['arrValue']['flagConsumptionTaxGeneralRule'];
						}
					}

					$flagConsumptionTaxSimpleRule = $varsTarget['vars']['flagConsumptionTaxSimpleRule'];
					if (!is_null($arr['arrValue']['flagConsumptionTaxSimpleRule'])) {
						$flagConsumptionTaxSimpleRule = $arr['arrValue']['flagConsumptionTaxSimpleRule'];

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
					$arrayCheck[$value]['strTitle'] = $arr['arrValue']['strTitle'];
					$arrayCheck[$value]['vars'] = array(
						'idTarget'                               => $idTarget,
						'idAccountTitleJgaapFS'                  => $arr['arrValue']['idAccountTitleJgaapFS'],
						'flagUse'                                => (int) $arr['arrValue']['flagUse'],
						'flagDebit'                              => (int) $arr['arrValue']['flagDebit'],
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


	protected function _iniEdit($arr)
	{
		$varsItem = $this->_getVarsItem(array(
			'flagFS'          => $arr['flagFS'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$flag = $this->_checkEditValue(array(
			'varsItem'        => $varsItem,
			'flagFS'          => $arr['flagFS'],
			'idTarget'        => $arr['idTarget'],
			'arrValue'        => &$arr['arrValue'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		if ($flag) {
			if ($flag == 'none') {
				return;

			} else {
				return $flag;
			}
		}

		$flag = $this->_setEditDb(array(
			'flagStatus'      => $arr['flagStatus'],
			'varsItem'        => $varsItem,
			'flagFS'          => $arr['flagFS'],
			'idTarget'        => $arr['idTarget'],
			'arrValue'        => $arr['arrValue'],
			'flagDefault'     => $arr['arrValue']['flagDefault'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		if ($flag) {
			return $flag;
		}
	}

	/**
		(array(
			'arrValue'    => $arrValue,
			'varsItem'    => $varsItem,
			'flagFS'      => $flagFS,
			'vars'        => $vars,
			'idTarget'    => $idTarget,
		))
	 */
	protected function _checkEditValue($arr)
	{
		global $classEscape;

		$numFiscalPeriodTemp = $arr['numFiscalPeriod'] - 1;
		$varsFS = $arr['varsItem']['varsFS']['jsonJgaapAccountTitle'. $arr['flagFS']];
		$varsFS = $this->_getFlagUseLog(array(
			'vars'                  => $varsFS,
			'numFiscalPeriod'       => $arr['numFiscalPeriod'],
			'numFiscalPeriodTemp'   => $numFiscalPeriodTemp,
			'classCalcAccountTitle' => $this->_getClassCalc(array('flagType' => 'AccountTitle')),
			'idTarget'              => $arr['idTarget'],
			'flagFS'                => $arr['flagFS'],
		));

		$arrayBlock = $this->_getTreeBlock(array(
			'vars'     => $varsFS,
			'idTarget' => $arr['idTarget'],
			'arrNew'   => array(),
		));
		if (!$arrayBlock) {
			return 'none';
		}

		$varsTarget = array();
		foreach ($arrayBlock as $key => $value) {
			if ($value['vars']['idTarget'] == $arr['idTarget']) {
				$varsTarget = $value;
				$arr['arrValue']['flagDefault'] = (int) $varsTarget['flagDefault'];
				break;
			}
		}

		$strTitle = $arr['arrValue']['strTitle'];
		$flagNum = $this->_checkTreeStrTitle(array(
			'vars'      => $varsFS,
			'strTarget' => $strTitle,
			'num'       => 0,
			'strSelf'   => $varsTarget['strTitle'],
			'idSelf'    => $varsTarget['vars']['idTarget'],
		));

		if ($flagNum) {
			return 'strTitleTempNext';
		}

		$idAccountTitle = $arr['arrValue']['idAccountTitle'];
		$flag = 0;
		if ((int) $varsTarget['flagDefault']) {
			if ($idAccountTitle != $varsTarget['vars']['idTarget']) {
				$idAccountTitle = $varsTarget['vars']['idTarget'];
				$arr['arrValue']['idAccountTitle'] = $idAccountTitle;
			}

		} else {
			$idAccountTitleCustom = 'custom_' . $arr['flagFS'] . '_' . $idAccountTitle;
			if ($idAccountTitleCustom != $varsTarget['vars']['idTarget']) {
				$flag = 1;
			}
		}

		//idAccountTitle
		if ($flag) {
			$arrayIdAccountTitle = array($idAccountTitle);
			$arrayFSList = $arr['varsItem']['arrayFSList'];
			foreach ($arrayFSList as $keyFSList => $valueFSList) {
				$arrayIdAccountTitle[] = 'custom_' . $keyFSList . '_' . $idAccountTitle;
			}
			foreach ($arrayIdAccountTitle as $key => $value) {
				$flag = $this->_checkValueIdAccountTitleFS(array(
					'varsItem' => $arr['varsItem'],
					'idTarget' => $value,
					'idSelf'   => $arr['idTarget'],
				));
				if ($flag) {
					return 'idAccountTitleTempNext';
				}
			}
			if ($varsTarget['vars']['flagUseAllLog']) {
				$idAccountTitle = $varsTarget['vars']['idTarget'];
				$arr['arrValue']['idAccountTitle'] = $idAccountTitle;
			}
		}

		//flagDebit
		$flagDebit = $arr['arrValue']['flagDebit'];
		if ($flagDebit != $varsTarget['vars']['flagDebit']) {
			if ((int) $varsTarget['flagDefault']) {
				$arr['arrValue']['flagDebit'] = $varsTarget['vars']['flagDebit'];
			}
		}

		//
		$idAccountTitleJgaapFS = $arr['arrValue']['idAccountTitleJgaapFS'];
		if ($idAccountTitleJgaapFS != $varsTarget['vars']['idAccountTitleJgaapFS']) {
			if (!$arr['varsItem']['varsJgaapFS']['arrStrTitle'][$idAccountTitleJgaapFS]) {
				$classCalcTempNextAccountTitleFS = $this->_getClassCalc(array(
					'flagType'   => 'TempNext',
					'flagDetail' => 'AccountTitleFS',
				));
				$flag = $classCalcTempNextAccountTitleFS->allot(array(
					'flagStatus'      => 'back',
					'numFiscalPeriod' => $arr['numFiscalPeriod'],
					'flagFS'          => $arr['flagFS'],
					'idTarget'        => $arr['arrValue']['idAccountTitleJgaapFS'],
				));
				if ($flag) {
					return $flag;
				}
			}
			if ($varsTarget['vars']['flagUseLogElse']
				|| ($varsTarget['vars']['flagUseLogElseTemp'] && $varsTarget['vars']['flagFS'] == 'BS')
			) {
				$arr['arrValue']['idAccountTitleJgaapFS'] = $varsTarget['vars']['idAccountTitleJgaapFS'];
			}
			if ($idAccountTitle == 'profitBroughtForward'
				 || $idAccountTitle == 'suspenseReceiptOfConsumptionTaxes'
				 || $idAccountTitle == 'suspensePaymentConsumptionTaxes'
			) {
				$arr['arrValue']['idAccountTitleJgaapFS'] = $varsTarget['vars']['idAccountTitleJgaapFS'];
			}
		}

		//
		$varsConsumptionTax = $arr['varsItem']['varsConsumptionTax'];
		if (!is_null($arr['arrValue']['flagConsumptionTaxGeneralRule'])) {
			if ($arr['varsItem']['varsEntityNation']['flagConsumptionTaxDeducted']) {
				if (!$varsConsumptionTax['arrStrGeneralEach'][$arr['arrValue']['flagConsumptionTaxGeneralRule']]) {
					return 'consumptionTax';
				}
			} else {
				if (!$varsConsumptionTax['arrStrGeneralProration'][$arr['arrValue']['flagConsumptionTaxGeneralRule']]) {
					return 'consumptionTax';
				}
			}
		}
		if (!is_null($arr['arrValue']['flagConsumptionTaxSimpleRule'])) {
			if (!$varsConsumptionTax['arrStrSimple'][$arr['arrValue']['flagConsumptionTaxSimpleRule']]) {
				return 'consumptionTax';
			}
		}

		//$flagUse = $arr['arrValue']['flagUse'];

	}

	/**

	 */
	protected function _setEditDb($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$varsFS = $this->_getValueVarsFS(array(
			'flagStatus'  => $arr['flagStatus'],
			'arrValue'    => $arr['arrValue'],
			'varsItem'    => $arr['varsItem'],
			'flagFS'      => $arr['flagFS'],
			'idTarget'    => $arr['idTarget'],
			'flagDefault' => $arr['flagDefault'],
		));

		$jsonAccountTitle = json_encode($varsFS);
		$flag = $this->checkTextSize(array(
			'flagReturn' => 1,
			'str'        => $jsonAccountTitle,
		));
		if ($flag) {
			return 'errorDataMax';
		}

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
			'arrValue'  => $arrDbValue,
		));

		$idAccountTitle = $arr['arrValue']['idAccountTitle'];
		$idAccountTitleCustom = 'custom_' . $arr['flagFS'] . '_' . $idAccountTitle;
		if ($arr['idTarget'] != $idAccountTitleCustom) {

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
	}

	/**
	 *
	 */
	protected function _iniDelete($arr)
	{
		$varsItem = $this->_getVarsItem(array(
			'flagFS'          => $arr['flagFS'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$flag = $this->_checkDeleteValue(array(
			'varsItem'        => $varsItem,
			'flagFS'          => $arr['flagFS'],
			'idTarget'        => $arr['idTarget'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		if ($flag) {
			return;
		}

		$this->_setDeleteDb(array(
			'varsItem'        => $varsItem,
			'flagFS'          => $arr['flagFS'],
			'idTarget'        => $arr['idTarget'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
	}

	/**
		(array(
			'varsItem' => $varsItem,
			'flagFS'   => $arr['flagFS'],
			'idTarget' => $arr['idTarget'],
		))
	 */
	protected function _checkDeleteValue($arr)
	{
		global $classEscape;

		$varsFS = $arr['varsItem']['varsFS']['jsonJgaapAccountTitle'. $arr['flagFS']];
		$varsFS = $this->_getFlagUseLog(array(
			'vars'                  => $varsFS,
			'numFiscalPeriod'       => $arr['numFiscalPeriod'],
			'classCalcAccountTitle' => $this->_getClassCalc(array('flagType' => 'AccountTitle')),
			'idTarget'              => $arr['idTarget'],
		));

		$arrayBlock = $this->_getTreeBlock(array(
			'vars'     => $varsFS,
			'idTarget' => $arr['idTarget'],
			'arrNew'   => array(),
		));
		if (!$arrayBlock) {
			return __LINE__;
		}

		$varsTarget = array();
		foreach ($arrayBlock as $key => $value) {
			if ($value['vars']['idTarget'] == $arr['idTarget']) {
				$varsTarget = $value;
				break;
			}
		}
		if ((int) $varsTarget['flagDefault']) {
			return __LINE__;
		}

		if ($varsTarget['vars']['flagUseLog']) {
			return __LINE__;
		}
	}

	/**
			'varsItem'        => $varsItem,
			'flagFS'          => $arr['flagFS'],
			'idTarget'        => $arr['idTarget'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
	 */
	protected function _setDeleteDb($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$varsFS = $this->_getVarsDelete(array(
			'vars'     => $arr['varsItem']['varsFS']['jsonJgaapAccountTitle'. $arr['flagFS']],
			'idTarget' => $arr['idTarget'],
		));

		$jsonAccountTitle = json_encode($varsFS);

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
			'arrValue'  => $arrDbValue,
		));

		$this->_setDeleteSubAccountTitle(array(
			'idTarget'        => $arr['idTarget'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
	}

	protected function _setDeleteSubAccountTitle($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$arrWhere = array(
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
				'flagType'      => '',
				'strColumn'     => 'idAccountTitle',
				'flagCondition' => 'eq',
				'value'         => $arr['idTarget'],
			),
		);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingSubAccountTitle' . $strNation,
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => $arrWhere,
		));
		$array = $rows['arrRows'];

		$classDb->deleteRow(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingSubAccountTitle' . $strNation,
			'flagAnd'  => 1,
			'arrWhere' => $arrWhere,
		));

		foreach ($array as $key => $value) {
			$classDb->deleteRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingSubAccountTitleValue' . $strNation,
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
						'flagType'      => '',
						'strColumn'     => 'idSubAccountTitle',
						'flagCondition' => 'eq',
						'value'         => $value['idSubAccountTitle'],
					),
				),
			));
		}
	}

	/**
		(array(
			'flagStatus'      => 'back',
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'flagFS'          => $value['flagFS'],
			'idTarget'        => $key,
			'flagReverse'    => 1,
		))
	 */
	protected function _iniBack($arr)
	{
		$numFiscalPeriodPrev = $arr['numFiscalPeriod'] - 1;
		if ($arr['flagReverse']) {
			$numFiscalPeriodPrev = $arr['numFiscalPeriod'];
			$arr['numFiscalPeriod'] = $numFiscalPeriodPrev - 1;
		}

		$varsItem = $this->_getBackVarsItem(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$varsItemPrev = $this->_getBackVarsItem(array(
			'numFiscalPeriod' => $numFiscalPeriodPrev,
		));

		$flag = $this->_setBackDb(array(
			'varsItem'        => $varsItem,
			'varsItemPrev'    => $varsItemPrev,
			'flagFS'          => $arr['flagFS'],
			'idTarget'        => $arr['idTarget'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		return $flag;
	}

	/**

	 */
	protected function _getBackVarsItem($arr)
	{
		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$data = array(
			'varsFS' => $varsFS,
		);

		return $data;
	}

	/**
		(array(
			'varsItem'        => $varsItem,
			'varsItemPrev'    => $varsItemPrev,
			'flagFS'          => $arr['flagFS'],
			'idTarget'        => $arr['idTarget'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _setBackDb($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$varsFS = $this->_getBackValueVarsFS(array(
			'varsItem'     => $arr['varsItem'],
			'varsItemPrev' => $arr['varsItemPrev'],
			'flagFS'       => $arr['flagFS'],
			'idTarget'     => $arr['idTarget'],
		));

		$jsonAccountTitle = json_encode($varsFS);
		$flag = $this->checkTextSize(array(
			'flagReturn' => 1,
			'str'        => $jsonAccountTitle,
		));
		if ($flag) {
			return 'errorDataMax';
		}

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
			'arrValue'  => $arrDbValue,
		));

		$this->_setBackDbFSId(array(
			'idColumn' => $strAccountTitle,
			'idTarget' => $arr['idTarget'],
		));
	}

	/**
	 (array(
		 'idColumn'     =>'',
		 'idTarget' => '',
	 ))
	 */
	protected function _setBackDbFSId($arr)
	{
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

		$arrDbColumn = array($arr['idColumn']);

		$varsFSId = $rows['arrRows'][0][$arr['idColumn']];
		if (!$varsFSId) {
			$varsFSId = array();
		}
		unset($varsFSId[$arr['idTarget']]);

		$jsonId = json_encode($varsFSId);
		$arrDbValue = array($jsonId);

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable' => 'accountingFSId' . $strNation,
			'arrColumn' => $arrDbColumn,
			'flagAnd'  => 1,
			'arrWhere' => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idEntity',
					'flagCondition' => 'eq',
					'value'         => $varsPluginAccountingAccount['idEntityCurrent'],
				),
			),
			'arrValue'  => $arrDbValue,
		));

		$this->_updateDbPreferenceStamp(array('strColumn' => 'fSId'));
	}

	/**
		(array(
			'varsItem'     => $arr['varsItem'],
			'varsItemPrev' => $arr['varsItemPrev'],
			'flagFS'       => $arr['flagFS'],
			'idTarget'     => $arr['idTarget'],
		))
	 */
	protected function _getBackValueVarsFS($arr)
	{
		$arrayBlockPrev = $this->_getTreeBlock(array(
			'vars'     => $arr['varsItemPrev']['varsFS']['jsonJgaapAccountTitle'. $arr['flagFS']],
			'idTarget' => $arr['idTarget'],
		));

		$idTargetInsert = $arrayBlockPrev[0]['vars']['idTarget'];
		$varsTargetPrev = array();
		foreach ($arrayBlockPrev as $key => $value) {
			if ($value['vars']['idTarget'] == $arr['idTarget']) {
				$varsTargetPrev = $value;
				break;
			}
			$idTargetInsert = $value['vars']['idTarget'];
		}

		$arrayPrev = array();
		foreach ($arrayBlockPrev as $key => $value) {
			if ($varsTargetPrev['vars']['idTarget'] == $value['vars']['idTarget']) {
				continue;
			}
			$arrayPrev[] = $value['vars']['idTarget'];
		}

		$idTarget = '';
		foreach ($arrayBlockPrev as $key => $value) {
			$arrayBlockNext = $this->_getTreeBlock(array(
				'vars'     => $arr['varsItem']['varsFS']['jsonJgaapAccountTitle'. $arr['flagFS']],
				'idTarget' => $value['vars']['idTarget'],
			));
			if ($arrayBlockNext) {
				$idTarget = $value['vars']['idTarget'];
				break;
			}
		}

		$arrayNext = array();
		foreach ($arrayBlockNext as $key => $value) {
			$arrayNext[] = $value['vars']['idTarget'];
		}

		$flag = 0;
		foreach ($arrayPrev as $key => $value) {
			if ($arrayNext[$key] != $value) {
				$flag = 1;
			}
		}
		if (count($arrayPrev) != count($arrayNext)) {
			$flag = 1;
		}

		$arrayNew = array();
		if ($flag) {
			$arrayNew = $arrayBlockNext;
			$arrayNew[] = $varsTargetPrev;

		} else {
			foreach ($arrayBlockNext as $key => $value) {
				if ($idTargetInsert == $value['vars']['idTarget']) {
					$arrayNew[] = $value;
					$arrayNew[] = $varsTargetPrev;
					continue;
				}
				$arrayNew[] = $value;
			}
		}

		$varsFS = $this->_insertTreeBlock(array(
			'vars'       => $arr['varsItem']['varsFS']['jsonJgaapAccountTitle'. $arr['flagFS']],
			'idTarget'   => $idTarget,
			'varsTarget' => $arrayNew,
		));

		return $varsFS;
	}
}
