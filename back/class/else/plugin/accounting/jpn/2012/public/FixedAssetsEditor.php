<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_FixedAssetsEditor_2012_Public extends Code_Else_Plugin_Accounting_Jpn_FixedAssetsEditor
{
	protected $_childSelf = array(
		'pathTplJs'   => 'else/plugin/accounting/js/jpn/2012/public/fixedAssetsEditor.js',
		'pathVarsJs'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/fixedAssetsEditor.php',
	);

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

			} elseif (preg_match( "/^numRatioOperate$/", $key)) {
				if ($arr['arrValue'][$key] > 100) {
					$this->_sendVarsCheck(__LINE__);
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
				//$this->_sendVarsCheck(__LINE__);
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
				//$this->_sendVarsCheck(__LINE__);
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
		$numValueDep = $arr['arrValue']['numValueDep'];

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
			//$this->_sendVarsCheck(__LINE__);
		}

		//NumValueDepNextOver
		$numValueDepNextOver = $data['numValueDepPrevOver'] + $numValueDepCurrentOver;
		if ($numValueDepNextOver < 0) {
			$numValueDepNextOver = 0;
		}
		if ($data['numValueDepNextOver'] != $numValueDepNextOver) {
			//$this->_sendVarsCheck(__LINE__);
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
			//$this->_sendVarsCheck(__LINE__);
		}

		//NumValueDepSpecialShortCurrentCut

		if ($data['numValueDepSpecialShortCurrentCut'] > $numValueDepSpecialShortCurrent) {
			//$this->_sendVarsCheck(__LINE__);
		}

		//NumValueDepSpecialShortNext
		$numValueDepSpecialShortNext = $numValueDepSpecialShortCurrent - $data['numValueDepSpecialShortCurrentCut'];
		if ($data['numValueDepSpecialShortNext'] != $numValueDepSpecialShortNext) {
			$this->_sendVarsCheck(__LINE__);

		} elseif ($data['numValueDepSpecialShortNext'] < 0) {
			//$this->_sendVarsCheck(__LINE__);
		}

		//unique start
		foreach ($arrayStr as $key => $value) {
			if (preg_match( "/^numValue/", $key)) {
				if ($key == 'numValueDepPrevOver'
					|| $key == 'numValueDepSpecialShortPrev'
					|| $key == 'numValueDepCurrentOver'
					|| $key == 'numValueDepNextOver'
					|| $key == 'numValueDepSpecialShortPrevData'
					|| $key == 'numValueDepSpecialShortCurrent'
					|| $key == 'numValueDepSpecialShortCurrentCut'
					|| $key == 'numValueDepSpecialShortNext'
				) {
					$arr['arrValue'][$key] = 0;
					$data[$key] = 0;
				}

			}
		}
		//unique end
	}



	//-----------------------------------------------------------
	// Code_Else_Plugin_Accounting_Jpn_FixedAssets overwrite _2012_Public
	//-----------------------------------------------------------
	protected $_extSelf = array(
		'idPreference' => 'fixedAssetsWindow',
		'idLog'        => 'logWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/2012/public/fixedAssets.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/2012/public/fixedAssets.php',
		'tplDetail'    => 'else/plugin/accounting/html/2012/public/fixedAssets.html',
		'tplComment'   => 'else/plugin/accounting/html/fixedAssetsComment.html',
		'varsDefault'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/2012/public/fixedAssetsAccountTitle.php',
		'varsOption'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/depreciation.php',
	);

	/**
		(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		))
	 */
	protected function _updateVarsTemplateDetailJsonDetail($arr)
	{
		global $classEscape;

		$arrayNew = array();
		$arrayStr = $arr['varsStr'];
		$varsEntityNation = $arr['varsItem']['varsEntityNation'];
		$array = $arr['varsDetail'];
		foreach ($array as $key => $value) {
			$str = 'str' . ucwords($value['id']);
			$arrayStr[$str] = $value['strTitle'];
			$strFlag = 'flag' . ucwords($value['id']);
			$arrayStr[$strFlag] = 1;

			$flag = 0;
			if ($value['id'] == 'IdAccountTitle') {
				$flag = 1;
				$value['arrayOption'] = $arr['varsItem']['arrAccountTitleFixedAssets']['arrSelectTag'];

			} elseif ($value['id'] == 'FlagDepMethod') {
				$str = $classEscape->toLower(array('str' => $value['id']));
				if ($arr['varsItem']['varsOptions'][$str]) {
					$value['arrayOption'] = $arr['varsItem']['varsOptions'][$str]['arrSelectTag'];
				}
				$flag = 1;

			} elseif ($value['id'] == 'IdDepartment') {
				if (count($arr['varsItem']['arrDepartment']['arrSelectTag']) == 1) {
					$flag = 0;
					$arrayStr[$strFlag] = 0;

				} else {
					$flag = 1;
					$value['arrayOption'] = $arr['varsItem']['arrDepartment']['arrSelectTag'];
				}

			} elseif (preg_match("/^Stamp/", $value['id'])) {
				$flag = 1;
				$value['varsFormCalender']['varsStatus']['stampMax'] = $arr['varsItem']['varsStampTerm']['stampMax']*1000;

			} elseif ($value['id'] == 'NumUsefulLife') {
				$flag = 1;
				$value['arrayOption'] = $arr['varsItem']['varsNumUsefulLife']['arrSelectTag'];

			} elseif ($value['id'] == 'StrDepMonths') {
				$flag = 1;
				$value['varsTmpl']['numMonths'] = $varsEntityNation['numFiscalTermMonth'];

			} elseif ($value['id'] == 'NumValueDepSpecialShortPrev'
				|| $value['id'] == 'NumValueDepPrevOver'
				|| $value['id'] == 'NumValueDepSpecialShortCurrent'
			) {
				//if ($varsEntityNation['flagCorporation'] == 1) {
					$flag = 1;

				//} else {
				//	$arrayStr[$strFlag] = 0;

				//}

			//製造原価
			} elseif ($value['id'] == 'ProductsCost' || $value['id'] == 'NumRatioProductsCost') {
				//個人一般
				if ($varsEntityNation['flagCorporation'] == 2) {
					if ($varsEntityNation['flagCR']) {
						$flag = 1;
					} else {
						$arrayStr[$strFlag] = 0;
					}

				//個人不動産
				} elseif ($varsEntityNation['flagCorporation'] == 3) {
					$arrayStr[$strFlag] = 0;

				//個人農業
				} elseif ($varsEntityNation['flagCorporation'] == 4) {
					$arrayStr[$strFlag] = 0;

				//法人
				} else {
					if ($varsEntityNation['flagCR']) {
						$flag = 1;
					} else {
						$arrayStr[$strFlag] = 0;
					}
				}

			//営業外費用
			} elseif ($value['id'] == 'NonOperatingExpenses' || $value['id'] == 'NumRatioNonOperatingExpenses') {
				//個人一般
				if ($varsEntityNation['flagCorporation'] == 2) {
					$arrayStr[$strFlag] = 0;

				//個人不動産
				} elseif ($varsEntityNation['flagCorporation'] == 3) {
					$arrayStr[$strFlag] = 0;

				//個人農業
				} elseif ($varsEntityNation['flagCorporation'] == 4) {
					$arrayStr[$strFlag] = 0;

				//法人
				} else {
					$flag = 1;
				}

			//生産原価
			} elseif ($value['id'] == 'AgricultureCost' || $value['id'] == 'NumRatioAgricultureCost') {
				//個人一般
				if ($varsEntityNation['flagCorporation'] == 2) {
					$arrayStr[$strFlag] = 0;

				//個人不動産
				} elseif ($varsEntityNation['flagCorporation'] == 3) {
					$arrayStr[$strFlag] = 0;

				//個人農業
				} elseif ($varsEntityNation['flagCorporation'] == 4) {
					if ($varsEntityNation['flagCR']) {
						$flag = 1;
					} else {
						$arrayStr[$strFlag] = 0;
					}

				//法人
				} else {
					$arrayStr[$strFlag] = 0;

				}

			//余り負担先
			} elseif ($value['id'] == 'FlagFraction') {
				$str = $classEscape->toLower(array('str' => $value['id']));
				$value['arrayOption'] = $this->_getFlagFractionOption(array(
					'varsEntityNation' => $varsEntityNation,
					'arrayOption'      => $arr['varsItem']['varsOptions'][$str]['arrSelectTag'],
				));
				$flag = 1;

			//事業専用割合 経費算入額
			} elseif ($value['id'] == 'NumRatioOperate' || $value['id'] == 'NumValueDepOperate') {
				if ($varsEntityNation['flagCorporation'] != 1) {
					$flag = 1;
				} else {
					$arrayStr[$strFlag] = 0;
				}

			//残存割合
			} elseif ($value['id'] == 'NumSurvivalRate' || $value['id'] == 'NumSurvivalRateLimit') {
				$flag = 1;
				$arrayOption = array();
				for ($j = 0; $j <= 100; $j++) {
					$data = array(
						'strTitle' => $j . '%',
						'value' => $j,
					);
					$arrayOption[$j] = $data;
				}
				$value['arrayOption'] = $arrayOption;

			} else {
				$str = $classEscape->toLower(array('str' => $value['id']));
				if ($arr['varsItem']['varsOptions'][$str]) {
					$value['arrayOption'] = $arr['varsItem']['varsOptions'][$str]['arrSelectTag'];
				}
				$flag = 1;
			}

			if ($flag) {
				if ($value['id'] == 'LossOnDisposalOfFixedAssets'
					|| $value['id'] == 'AccumulatedDepreciation'
					|| $value['id'] == 'SellingAdminCost'
					|| $value['id'] == 'ProductsCost'
					|| $value['id'] == 'NonOperatingExpenses'
					|| $value['id'] == 'AgricultureCost'
				) {
					$value['arrayOption'] = $arr['varsItem']['arrAccountTitle']['arrSelectTag'];
				}

				$arrayNew[] = $value;
			}
		}

		$str = 'sumBasic';
		$arrayStr[$str] = 6;
		$array = array('flagIdDepartment');
		foreach ($array as $key => $value) {
			if (!$arrayStr[$value]) {
				$arrayStr[$str]--;
			}
		}

		$str = 'sumCurrentDep';
		$arrayStr[$str] = 17;
		$array = array('flagNumValueDepPrevOver', 'flagNumValueDepSpecialShortPrev', 'flagNumRatioOperate', 'NumValueDepOperate');
		foreach ($array as $key => $value) {
			if (!$arrayStr[$value]) {
				$arrayStr[$str]--;
			}
		}

		$str = 'sumWrite';
		$arrayStr[$str] = 6;
		$array = array('flagProductsCost', 'flagNonOperatingExpenses', 'flagAgricultureCost');
		foreach ($array as $key => $value) {
			if (!$arrayStr[$value]) {
				$arrayStr[$str]--;
			}
		}

		$str = 'sumRatio';
		$arrayStr[$str] = 5;
		$array = array('flagNumRatioProductsCost', 'flagNumRatioNonOperatingExpenses', 'flagNumRatioAgricultureCost');
		foreach ($array as $key => $value) {
			if (!$arrayStr[$value]) {
				$arrayStr[$str]--;
			}
		}

		$varsHtml = $this->_getHtml(array(
			'varsStr' => $arrayStr,
			'pathTpl' => $this->_extSelf['tplDetail']
		));

		$data = array(
			'varsDetail' => $arrayNew,
			'varsHtml'   => $varsHtml,
		);

		return $data;
	}
}

