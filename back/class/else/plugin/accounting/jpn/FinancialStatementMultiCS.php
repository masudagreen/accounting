<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_FinancialStatementMultiCS extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference' => 'financialStatementMultiCSWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/financialStatementMultiCS.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/financialStatementMultiCS.php',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 1,
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
			$strChild = ucwords($varsRequest['query']['child']);
			$strExt = ucwords($varsRequest['query']['ext']);
			$str = $strExt . $strChild;

			$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/jpn/' . $str . ".php";
			if (!file_exists($path)) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
				}
				exit;
			}
			require_once($path);
			$strClass = 'Code_Else_Plugin_Accounting_Jpn_' . $str;
			$classCall = new $strClass;
			$classCall->run();

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
	 *
	 */
	protected function _iniJs()
	{
		global $classSmarty;
		global $varsPluginAccountingAccount;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsFlag' => $vars['varsFlag'],
		));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $vars['varsFlag'],
		));

		$json = json_encode($vars);
		$classSmarty->assign('varsLoad', $json);
		$classSmarty->assign('numNews', $this->getNumNews());
		$contents = $classSmarty->fetch($this->_extSelf['pathTplJs']);

		$this->sendJs(array(
			'data' => $contents,
		));

	}

	/**
		(array(
			'flag' => '',
			'vars' => $vars,
		))
	 */
	protected function _getVarsItem($arr)
	{
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$numFiscalPeriodStart = (int) $varsPluginAccountingEntity[$varsPluginAccountingAccount['idEntityCurrent']]['numFiscalPeriodStart'];

		$varsPeriod = array();
		$numEnd = $arr['vars']['numCompare'];
		for ($i = 0; $i < $numEnd; $i++) {
			$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] - $i;
			if ($numFiscalPeriod < $numFiscalPeriodStart) {
				break;
			}
			$varsPeriod[] = $numFiscalPeriod;
		}
		$varsPeriod = array_reverse($varsPeriod);

		$varsFS = array();
		$varsFSValue = array();
		$varsFlagFiscalPeriod = array();
		$varsEntityNation = array();
		$varsStrFlagFiscalPeriod = array();

		$array = $varsPeriod;
		foreach ($array as $key => $value) {
			$numFiscalPeriod = $value;
			$varsFS[$numFiscalPeriod] = $this->_getVarsFS(array(
				'numFiscalPeriod' => $numFiscalPeriod,
			));
			if ($varsFS[$numFiscalPeriod]) {
				$varsFSValue[$numFiscalPeriod] = $this->_getVarsFSValue(array(
					'numFiscalPeriod' => $numFiscalPeriod,
				));
			}

			$varsFlagFiscalPeriod[$numFiscalPeriod] = $this->_getVarsFlagFiscalPeriod(array(
				'numFiscalPeriod' => $numFiscalPeriod,
			));

			$varsEntityNation[$numFiscalPeriod] = $this->_getVarsEntityNation(array(
				'numFiscalPeriod' => $numFiscalPeriod,
			));

			$varsStrFlagFiscalPeriod[$numFiscalPeriod] = $this->_getVarsStrFlagFiscalPeriod(array(
				'vars'             => $arr['vars']['varsItem']['tmplFiscalPeriod'],
				'varsEntityNation' => $varsEntityNation[$numFiscalPeriod],
			));
		}

		$data = array(
			'varsEntityNation'        => $varsEntityNation,
			'varsFS'                  => $varsFS,
			'varsFSValue'             => $varsFSValue,
			'varsFlagFiscalPeriod'    => $varsFlagFiscalPeriod,
			'varsStrFlagFiscalPeriod' => $varsStrFlagFiscalPeriod,
			'varsPeriod'              => $varsPeriod,
		);
		return $data;
	}

	/**
		(array(
			'vars'             => $value,
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _getVarsStrFlagFiscalPeriod($arr)
	{
		$varsEntityNation = $arr['varsEntityNation'];

		$arrayNew = array();
		$array = $arr['vars']['arrayOption'];
		foreach ($array as $key => $value) {
			if ($varsEntityNation['numFiscalTermMonth'] == 12) {
				$arrayNew[] = $value;

			} else {
				if (preg_match( "/^(f1)$/", $value['value'])) {
					$arrayNew[] = $value;
				}
			}
		}
		$arr['vars']['arrayOption'] = $arrayNew;

		$arrayOption = array();
		if ($varsEntityNation['numFiscalTermMonth'] == 12) {
			$arrayOption = $arr['vars']['arrayOption'];

		} else {
			$arrayOption[] = $arr['vars']['varsPeriod'];
		}

		$numMonth = (int) $varsEntityNation['numFiscalBeginningMonth'];
		$numEnd = (int) $varsEntityNation['numFiscalTermMonth'];
		for ($i = 0; $i < $numEnd; $i++) {
			$data = array(
				'strTitle' => $numMonth . $arr['vars']['strMonth'],
				'value'    => $numMonth,
			);
			$arrayOption[] = $data;
			$numMonth++;
			if ($numMonth > 12) {
				$numMonth = 1;
			}
		}

		$varsStr = array();
		$array = $arrayOption;
		foreach ($array as $key => $value) {
			$varsStr[$value['value']] = $value['strTitle'];
		}

		return $varsStr;
	}

	/**
		(array(
			'vars'             => $vars,
			'varsEntityNation' => $varsEntityNation,
			'varsItem'         => $varsItem,
			'varsFlag'         => array(),
		))
	 */
	protected function _updateVars($arr)
	{
		global $classHtml;
		global $varsPluginAccountingAccount;

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllOutput'])) {
			$arr['vars']['portal']['varsList']['varsStart']['varsEdit']['flagOutputUse'] = 0;
			$arr['vars']['portal']['varsList']['varsEdit']['flagOutputUse'] = 0;
		}

		$strFS = 'jsonJgaapFS' . $arr['varsFlag']['flagFS'];
		$strFlagDirect = 'varsInDirect';
		if ($arr['varsFlag']['flagDirect']) {
			$strFlagDirect = 'varsDirect';
		}

		$array = array_reverse($arr['varsItem']['varsPeriod']);
		$varsFS = array();
		foreach ($array as $key => $value) {
			$numFiscalPeriod = $value;
			if ($key == 0) {
				$varsFS = $arr['varsItem']['varsFS'][$numFiscalPeriod][$strFS][$strFlagDirect];
				continue;
			}
			$varsFS = $this->_checkLastAccountTitle(array(
				'varsTmpl'        => $arr['vars']['varsItem']['varsTmpl'],
				'varsFS'          => $varsFS,
				'numFiscalPeriod' => $numFiscalPeriod,
				'varsFSData'      => $arr['varsItem']['varsFS'][$numFiscalPeriod][$strFS][$strFlagDirect],
			));
		}

		$varsBase = array();
		$array = array_reverse($arr['varsItem']['varsPeriod']);
		foreach ($array as $key => $value) {
			$numFiscalPeriod = $value;
			$varsBase[$numFiscalPeriod]['varsNum'] = $arr['varsItem']['varsFSValue'][$numFiscalPeriod][$strFS][$strFlagDirect];
		}

		$varsFS = $this->_setTreeId(array(
			'idParent' => '-',
			'vars'     => $varsFS,
		));

		$varsTemp = $this->_getAccountTitleValue(array(
			'vars'         => $arr['vars'],
			'varsFS'       => $varsFS,
			'varsItem'     => $arr['varsItem'],
			'varsFlag'     => $arr['varsFlag'],
			'varsBase'     => $varsBase,
			'arrStrTitle'  => array(),
			'arrSelectTag' => array(),
		));

		$arr['vars']['varsCollect']['varsBase'] = $varsTemp['varsBase'];
		$arr['vars']['varsCollect']['arrStrTitle'] = $varsTemp['arrStrTitle'];
		$arr['vars']['varsCollect']['arrSelectTag'] = $varsTemp['arrSelectTag'];
		$arr['vars']['varsCollect']['varsPeriod'] = $arr['varsItem']['varsPeriod'];
		$arr['vars']['varsCollect']['varsFlagFiscalPeriod'] = $arr['varsItem']['varsFlagFiscalPeriod'];
		$arr['vars']['varsCollect']['varsStrFlagFiscalPeriod'] = $arr['varsItem']['varsStrFlagFiscalPeriod'];

		$arr['vars']['portal']['varsList']['varsDetail'] = $this->_updateVarsList((array(
			'vars'     => &$arr['vars'],
			'varsItem' => $arr['varsItem'],
		)));

		return $arr['vars'];
	}

	/**
		(array(
			'varsTmpl'    => $arr['vars']['varsItem']['varsTmpl'],
			'varsFS'      => $arr['varsItem']['varsFS'],
			'varsFSData'  => $arr['varsItem']['varsFSPrev'],
		))
	 */
	protected function _checkLastAccountTitle($arr)
	{
		$flagSaleNet = 0;
		$arrayCheck = array();
		$arrayData = &$arr['varsFSData'];
		foreach ($arrayData as $keyData => $valueData) {
			if ($valueData['vars']['idTarget'] == 'saleNet') {
				$flagSaleNet = 1;
			}
			if (!is_null($valueData['vars']['varsValue']) && !$valueData['vars']['flagCalc']) {
				$idTarget = $valueData['vars']['idTarget'];
				$arrayCheck[$idTarget] = ($flagSaleNet)? 'saleNet' : 'else';
			}
		}

		$array = &$arr['varsFS'];
		foreach ($array as $key => $value) {
			if (!is_null($array[$key]['vars']['varsValue']) && !$value['vars']['flagCalc']) {
				$idTarget = $value['vars']['idTarget'];
				if ($arrayCheck[$idTarget]) {
					unset($arrayCheck[$idTarget]);

				} else {
					if (is_null($array[$key]['vars']['flagCurrent'])) {
						$array[$key]['vars']['flagCurrent'] = $arr['numFiscalPeriod'];
					}
				}
			}

			if ($value['child']) {
				$array[$key]['child'] = $this->_checkLastAccountTitle(array(
					'varsTmpl'        => $arr['varsTmpl'],
					'varsFSData'      => ($arrayData[$key]['child'])? $arrayData[$key]['child'] : array(),
					'varsFS'          => $array[$key]['child'],
					'numFiscalPeriod' => $arr['numFiscalPeriod'],
				));
			}
		}

		if ($arrayCheck) {
			$varsItemTmpl = $arr['varsTmpl'];
			if ($flagSaleNet) {
				$arrayNew = array();
				foreach ($array as $key => $value) {
					if ($value['vars']['idTarget'] != 'saleNet') {
						$arrayNew[] = $value;
						continue;
					}
					foreach ($arrayCheck as $keyCheck => $valueCheck) {
						if ($valueCheck == 'saleNet') {
							$varsTmpl = $varsItemTmpl;
							$varsTmpl['vars']['idTarget'] = $keyCheck;
							$varsTmpl['vars']['flagPast'] = $arr['numFiscalPeriod'];
							$arrayNew[] = $varsTmpl;
						}
					}
					$arrayNew[] = $value;
				}
				foreach ($arrayCheck as $keyCheck => $valueCheck) {
					if ($valueCheck == 'else') {
						$varsTmpl = $varsItemTmpl;
						$varsTmpl['vars']['idTarget'] = $keyCheck;
						$varsTmpl['vars']['flagPast'] = $arr['numFiscalPeriod'];
						$arrayNew[] = $varsTmpl;
					}
				}
				return $arrayNew;

			} else {
				foreach ($arrayCheck as $keyCheck => $valueCheck) {
					$varsTmpl = $varsItemTmpl;
					$varsTmpl['vars']['idTarget'] = $keyCheck;
					$varsTmpl['vars']['flagPast'] = $arr['numFiscalPeriod'];
					$array[] = $varsTmpl;
				}
			}
		}

		return $array;
	}

	/**
		(array(
			'vars'         => $arr['vars'],
			'varsFS'       => $varsFS,
			'varsItem'     => $arr['varsItem'],
			'varsFlag'     => $arr['varsFlag'],
			'varsBase'     => $varsBase,
			'arrStrTitle'  => array(),
			'arrSelectTag' => array(),
		))

	 */
	protected function _getAccountTitleValue($arr)
	{
		$array = &$arr['varsFS'];
		$varsBase = &$arr['varsBase'];
		$arrStrTitle = &$arr['arrStrTitle'];
		$arrSelectTag = &$arr['arrSelectTag'];

		foreach ($array as $key => $value) {

			$idTarget = $value['vars']['idTarget'];
			$array[$key]['strClassFont'] = '';
			if (!is_null($value['vars']['flagUse'])) {
				//flagUse
				if ((int) $value['vars']['flagUse']) {

				} else {
					$array[$key]['strClassFont'] = $arr['vars']['varsItem']['strClassNone'];
				}
			}

			$arrLevel = preg_split("/-/", $value['id']);
			$num = count($arrLevel) - 2;
			$arrLevel = array();
			for ($i = 0 ; $i < $num; $i++) {
				$arrLevel[$i] = '';
			}
			$str =  '' . join('.', $arrLevel) . ' ';
			$strTitleFSTag = $str . $value['strTitle'];

			$data = array(
				'strTitle'      => $value['strTitle'],
				'strTitleFSTag' => $strTitleFSTag,
				'strClassFont'  => $array[$key]['strClassFont'],
			);
			$data['vars']['flagUse'] = $value['vars']['flagUse'];
			$data['vars']['varsValue'] = $value['vars']['varsValue'];

			$arr['arrStrTitle'][$idTarget] = $data;

			if (is_null($value['vars']['varsValue'])) {
				$arr['arrSelectTag'][] = array(
					'strTitle'     => $strTitleFSTag,
					'value'        => 'dummy',
					'flagDisabled' => 1,
				);

			} else {
				$arr['arrSelectTag'][] = array(
					'strTitle'     => $strTitleFSTag,
					'value'        => $idTarget,
				);
			}

			if (!is_null($array[$key]['vars']['varsValue'])) {

				$flagMakeLast = 0;
				$flagRemovePast = 0;

				$arrayPeriod = array_reverse($arr['varsItem']['varsPeriod']);
				foreach ($arrayPeriod as $keyPeriod => $valuePeriod) {
					$numFiscalPeriod = $valuePeriod;

					$flagNull = 0;
					if (!is_null($value['vars']['flagPast']) && !is_null($value['vars']['flagCurrent'])) {
						if (!($value['vars']['flagPast'] >= $numFiscalPeriod
							&& $value['vars']['flagCurrent'] < $numFiscalPeriod
						)) {
							$flagNull = 1;
						}

					} else if (!is_null($value['vars']['flagPast'])) {
						if (!($value['vars']['flagPast'] >= $numFiscalPeriod)) {
							$flagNull = 1;
						}
					} else if (!is_null($value['vars']['flagCurrent'])) {
						if (!($value['vars']['flagCurrent'] < $numFiscalPeriod)) {
							$flagNull = 1;
						}
					}

					$arrayFiscalPeriod = $arr['varsItem']['varsFlagFiscalPeriod'][$numFiscalPeriod];
					foreach ($arrayFiscalPeriod as $keyFiscalPeriod => $valueFiscalPeriod) {
						$flagFiscalPeriod = $valueFiscalPeriod;
						$numData = $arr['varsBase'][$numFiscalPeriod]['varsNum'][$flagFiscalPeriod][$idTarget]['sumNext'];
						if ($flagNull) {
							$arr['varsBase'][$numFiscalPeriod]['varsNum'][$flagFiscalPeriod][$idTarget]['sumNext'] = '';
							$arr['varsBase'][$numFiscalPeriod]['varsComma'][$flagFiscalPeriod][$idTarget]['sumNext'] = '';
							continue;
						}
						if (is_null($numData)) {
							$numData = 0;
						}
						$arr['varsBase'][$numFiscalPeriod]['varsNum'][$flagFiscalPeriod][$idTarget]['sumNext'] = $numData;
						$arr['varsBase'][$numFiscalPeriod]['varsComma'][$flagFiscalPeriod][$idTarget]['sumNext'] = number_format($numData);
					}

				}
			} else {
				$arrayPeriod = $arr['varsItem']['varsPeriod'];
				foreach ($arrayPeriod as $keyPeriod => $valuePeriod) {
					$numFiscalPeriod = $valuePeriod;
					$arrayFiscalPeriod = $arr['varsItem']['varsFlagFiscalPeriod'][$numFiscalPeriod];
					foreach ($arrayFiscalPeriod as $keyFiscalPeriod => $valueFiscalPeriod) {
						$flagFiscalPeriod = $valueFiscalPeriod;
						$arr['varsBase'][$numFiscalPeriod]['varsNum'][$flagFiscalPeriod][$idTarget]['sumNext'] = '';
						$arr['varsBase'][$numFiscalPeriod]['varsComma'][$flagFiscalPeriod][$idTarget]['sumNext'] = '';
					}
				}
			}

			if ($value['child']) {
				$dataTemp = $this->_getAccountTitleValue(array(
					'vars'         => &$arr['vars'],
					'varsFS'       => $array[$key]['child'],
					'varsItem'     => &$arr['varsItem'],
					'varsFlag'     => $arr['varsFlag'],
					'varsBase'     => $arr['varsBase'],
					'arrStrTitle'  => $arr['arrStrTitle'],
					'arrSelectTag' => $arr['arrSelectTag'],
				));
				$array[$key]['child'] = $dataTemp['vars'];
				$varsBase =  $dataTemp['varsBase'];
				$arrStrTitle =  $dataTemp['arrStrTitle'];
				$arrSelectTag =  $dataTemp['arrSelectTag'];
			}
		}

		return $arr;
	}

	/**
		(array(
			'vars'             => $vars,
			'varsItem'         => $arr['varsItem'],
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _updateVarsList($arr)
	{
		$vars = &$arr['vars'];
		$varsItem = &$arr['varsItem'];

		$arrayNew = array();
		$array = &$vars['portal']['varsList']['templateDetail'];
		foreach ($array as $key => $value) {
			$method = '_updateVarsList' . $value['id'];
			if (method_exists($this, $method)) {
				$value = $this->$method(array(
					'value'     => $value,
					'vars'      => $vars,
					'varsItem'  => $varsItem,
					'varsBase'  => $varsBase,
				));
			}
			$arrayNew[] = $value;
		}

		$vars['portal']['varsList']['templateDetail'] = $arrayNew;

		return $vars['portal']['varsList']['templateDetail'];
	}

	/**
		(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		))
	 */
	protected function _updateVarsListFlagFiscalPeriod($arr)
	{
		$varsPeriod = $arr['varsItem']['varsPeriod'];
		$numFiscalPeriod = reset($varsPeriod);
		$varsEntityNation = $arr['varsItem']['varsEntityNation'][$numFiscalPeriod];

		$arrayOption = array();
		if ($varsEntityNation['numFiscalTermMonth'] == 12) {
			$arrayOption = $arr['value']['varsTmpl']['arrayOption'];

		} else {
			$arrayOption = $arr['value']['varsTmpl']['varsPeriod'];
		}

		$arr['value']['arrayOption'] = $arrayOption;

		return $arr['value'];
	}

	protected function _updateVarsListTableF1($arr)
	{
		return $this-> _updateVarsListTable($arr);
	}

	protected function _updateVarsListTableF2($arr)
	{
		return $this-> _updateVarsListTable($arr);
	}

	protected function _updateVarsListTableF4($arr)
	{
		return $this-> _updateVarsListTable($arr);
	}

	protected function _updateVarsListTableMonth($arr)
	{
		return $this-> _updateVarsListTable($arr);
	}

	/**
		(array(
			'vars'      => $value,
			'varsItem'  => $varsItem,
			'varsData'  => $arr['varsData'],
		))
	 */
	protected function _updateVarsListTable($arr)
	{
		global $classHtml;

		$varsStrFlagFiscalPeriod = $arr['varsItem']['varsStrFlagFiscalPeriod'];
		$varsBase = $arr['varsBase'];

		$varsColumn = array('');
		$varsColumnWidth = array($arr['value']['tmplTable']['numWidthItem']);
		$varsColumnId = array('item');
		$numWidth = 0;
		$strPeriod = $arr['vars']['varsItem']['tmplFiscalPeriod']['strPeriod'];
		$array = $arr['varsItem']['varsPeriod'];
		foreach ($array as $key => $value) {
			$numFiscalPeriod = $value;
			$arrayFlagFiscalPeriod = $arr['varsItem']['varsStrFlagFiscalPeriod'][$numFiscalPeriod];
			foreach ($arrayFlagFiscalPeriod as $keyFlagFiscalPeriod => $valueFlagFiscalPeriod) {
				if ($arr['value']['id'] == 'TableF1') {
					if (!preg_match("/^f1$/", $keyFlagFiscalPeriod)) {
						continue;
					}

				} else if ($arr['value']['id'] == 'TableF2') {
					if (!preg_match("/^f2/", $keyFlagFiscalPeriod)) {
						continue;
					}

				} else if ($arr['value']['id'] == 'TableF4') {
					if (!preg_match("/^f4/", $keyFlagFiscalPeriod)) {
						continue;
					}

				} else if ($arr['value']['id'] == 'TableMonth') {
					if (preg_match("/^f/", $keyFlagFiscalPeriod)) {
						continue;
					}
				}
				$varsColumn[] = $numFiscalPeriod . $strPeriod . '' . $valueFlagFiscalPeriod;
				$varsColumnId[] = $numFiscalPeriod . '_' . $keyFlagFiscalPeriod;
				$varsColumnWidth[] = $arr['value']['tmplTable']['numWidth'];
				$numWidth += $arr['value']['tmplTable']['numWidth'];
			}
		}
		$varsBase = &$arr['vars']['varsCollect']['varsBase'];

		$arrayNew = array();
		$array = $arr['vars']['varsCollect']['arrStrTitle'];
		foreach ($array as $key => $value) {
			$varsDetail = $arr['value']['tmplTable']['tmplDetail'];
			$arrayColumn = $varsColumnId;
			foreach ($arrayColumn as $keyColumn => $valueColumn) {
				$varsDetail['varsDetail'][$valueColumn] = $arr['value']['tmplTable']['tmplData'];

				if ($valueColumn == 'item') {
					$varsDetail['varsDetail'][$valueColumn]['value'] = $value['strTitleFSTag'];
					$varsDetail['varsDetail'][$valueColumn]['strClass'] = $arr['value']['tmplTable']['strClassLeft'];
					if ($value['strClassFont']) {
						$varsDetail['varsDetail'][$valueColumn]['strClassFont'] = $value['strClassFont'];
					}
					$varsDetail['varsDetail'][$valueColumn]['flagOverflowUse'] = 1;
					continue;
				}

				$arrLevel = preg_split("/_/", $valueColumn);
				$numFiscalPeriod = reset($arrLevel);
				$flagFiscalPeriod = end($arrLevel);
				$arrLevel = array();
				if ($value['strClassFont']) {
					$varsDetail['varsDetail'][$valueColumn]['strClassFont'] = $value['strClassFont'];
				}
				$varsDetail['varsDetail'][$valueColumn]['value'] = $varsBase[$numFiscalPeriod]['varsComma'][$flagFiscalPeriod][$key]['sumNext'];
			}
			$arrayNew[] = $varsDetail;
		}

		$arr['value']['tmplTable']['varsStatus']['varsColumnId'] = $varsColumnId;
		$arr['value']['tmplTable']['varsStatus']['varsColumnWidth'] = $varsColumnWidth;

		$varsTemp = $classHtml->allot(array(
			'strClass'     => 'TableSimple',
			'flagStatus'   => 'Html',
			'varsDetail'   => $arrayNew,
			'varsColumn'   => $varsColumn,
			'varsStatus'   => $arr['value']['tmplTable']['varsStatus'],
		));
		$arr['value']['varsSpace']['varsDetail']['strHtml'] = $varsTemp['strHtml'];

		return $arr['value'];
	}

	/**

	 */
	protected function _iniNaviSearch()
	{
		$this->_setSearch();
	}

	/**

	 */
	protected function _setSearch()
	{
		global $varsRequest;
		global $varsPluginAccountingPreference;
		global $varsPluginAccountingAccount;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsFlag = array(
			'flagDirect' => (int) $varsRequest['query']['jsonValue']['vars']['FlagDirect'],
			'flagFS'     => $vars['varsFlag']['flagFS'],
		);

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
		));

		$this->_checkValueDetail(array(
			'varsDetail' => $vars['portal']['varsNavi']['varsDetail'],
			'varsFlag'   => $varsFlag,
		));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		));

		$this->sendVars(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'varsCollect'   => $vars['varsCollect'],
				'varsDetail' => $vars['portal']['varsList']['varsDetail'],
			),
		));
	}

	/**
		(array(
			'varsDetail'       => $varsDetail,
			'FlagFiscalPeriod' => $flagFiscalPeriod,
			'FlagUnit'         => $flagUnit,
		))
	 */
	protected function _checkValueDetail($arr)
	{
		global $varsRequest;
		global $classEscape;

		$flag = 0;
		$array = $arr['varsDetail'];
		foreach ($array as $key => $value) {
			$idTarget = $classEscape->toLower(array('str' => $value['id']));
			$arrayOption = $value['arrayOption'];
			foreach ($arrayOption as $keyOption => $valueOption) {
				if ($valueOption['value'] == $arr['varsFlag'][$idTarget]) {
					$flag = 1;
				}
			}
			if (!$flag) {
				if ($arr['flagOutput']) {
					$this->_send404Output();
				} else {
					$this->_sendOld();
				}
			}
			$flag = 0;
		}
	}

	/**
	 *
	 */
	protected function _iniListReload()
	{
		$this->_setSearch();
	}

	/**
	 *
	 */
	protected function _iniListOutput()
	{
		$this->_setClassExt(array('strClass' => 'FinancialStatementMultiCSOutput'));
	}
}
