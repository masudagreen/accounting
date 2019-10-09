<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_FixedAssets_2012_Public extends Code_Else_Plugin_Accounting_Jpn_FixedAssets
{
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
	 *
	 */
	public function run()
	{
		global $varsRequest;

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 0,
			'flagAuthority' => 'select',
			'idTarget'      => $this->_extSelf['idPreference'],
		));

		if (!$flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}

		$this->_checkEntity();

		if ($varsRequest['query']['child']) {
			$this->_checkCorporationClass(array('flagChild' => 1));

		} else {
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
		}
		exit;
	}

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
		$arrayStr[$str] = 14;
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
