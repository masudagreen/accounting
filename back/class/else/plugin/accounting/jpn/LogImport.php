<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_LogImport extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference'   => 'logWindow',
		'pathTplJs'      => 'else/plugin/accounting/js/jpn/logImport.js',
		'pathVarsJs'     => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/logImport.php',
		'pathVarsJournal'=> 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/dictionary.php',
	);

	/**
	 *
	 */
	public function run()
	{
		$this->_checkCorporationClass(array('flagChild' => 0));

		global $varsRequest;
		global $classCheck;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

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

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done)$/", $flagCurrentFlagNow)) {
			$this->_sendOldFlag();
		}

		$this->_checkEntity();

		if ($varsRequest['query']['child']) {
			$strChild = ucwords($varsRequest['query']['child']);
			if ($strChild == 'EditorTemp') {
				$strChild = 'Editor';
			}
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
						var_dump(__CLASS__ . '/' .__FUNCTION__);
					}
					exit;
				}
			}
		}
		exit;
	}

	/**
		$insCurrent->getDBAuthority(array(
			'flagSqlType' => $arr['flagSqlType'],ex) Select,Update,Delete,Output
			'arrData'     => ($arr['arrData']),ex) flag
		));
		return $array = array(
			'strSql'   => '',
			'arrValue' => array(),
		);
		or
		return 0
	 */
	public function getDBAuthority($arr)
	{
		global $classCheck;
		global $varsPluginAccountingAccount;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];

		$flagAuthority = $classCheck->checkModuleAuthority(array(
			'idModule' => 'accounting',
		));

		if ($flagAuthority) {
			$array = array(
				'strSql'   => 'idEntity = ? && numFiscalPeriod = ? ',
				'arrValue' => array($idEntity, $numFiscalPeriod),
			);
			return $array;
		}
		return 0;

	}


	/**
	 *
	 */
	protected function _iniJs()
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;


		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$this->_setJs(array(
			'pathVars'        => $this->_extSelf['pathVarsJs'],
			'pathTpl'         => $this->_extSelf['pathTplJs'],
			'arrFolder' => array(),
			'arrSearch'       => array(
				'idModule'  => 'accounting',
				'numLotNow' => 0,
				'strTable'  => 'accountingLogImport' . $strNation,
				'arrJoin'   => array(),
				'arrOrder'  => array(
					'strColumn' => 'id',
					'flagDesc'  => 1,
				),
				'insCurrent' => $this,
				'arrWhere'  => array(
				),
			),
		));

	}


	/**
		$this->_setJs(array(
			'pathVars'        => '',
			'pathTpl'         => '',
			'arrFolder'       => array(),
			'strTableSearch'  => '',
			'strColumnSearch' => '',
			'arrSearch'       => array(
				'idModule' => '',
				'numLotNow' => 0,
				'strTable'  => '',
				'arrOrder'  => array(),
				'arrWhere'  => array(),
			),
		));
	 */
	protected function _setJs($arr)
	{
		global $classSmarty;

		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$vars['varsRule'] = $varsItem;

		$vars['portal']['varsDetail']['templateDetail'] = $this->_updateVarsTemplateDetail((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$rows = $this->getSearch($arr['arrSearch']);

		$vars = $this->_updateSearch(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'rows'     => $rows,
		));

		$vars['portal']['varsNavi'] = array();

		$json = json_encode($vars);
		$classSmarty->assign('varsLoad', $json);

		$classSmarty->assign('numNews', $this->getNumNews());
		$contents = $classSmarty->fetch($arr['pathTpl']);

		$this->sendJs(array(
			'data' => $contents,
		));
	}

	/**
		(array(
			'vars' => $vars,
		))
	 */
	protected function _getVarsItem($arr)
	{
		global $varsPluginAccountingAccount;
		global $varsAccount;

		$arrDepartment = $this->_getVarsDepartment(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrSubAccountTitle = $this->_getVarsSubAccountTitle(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrAccountTitle = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'numFiscalPeriod'    => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrayFSList = $this->_getFSList(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsConsumptionTax = $this->_getVarsConsumptionTax(array());

		$varsFlagAttest = $this->_getVarsSelect(array(
			'vars'     => &$arr['vars'],
			'idTarget' => 'FlagAttest',
		));

		$classCalcDictionary = $this->_getClassCalc(array('flagType' => 'Dictionary'));
		$varsDictionaryItem = $classCalcDictionary->allot((array('flagStatus' => 'varsItem')));

		$data = array(
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'arrAccountTitle'    => $arrAccountTitle,
			'arrDepartment'      => $arrDepartment,
			'varsEntityNation'   => $varsEntityNation,
			'arrayFSList'        => $arrayFSList,
			'varsFlagAttest'     => $varsFlagAttest,
			'varsConsumptionTax' => $varsConsumptionTax,
			'idAccount'          => $varsAccount['id'],
			'varsDictionaryItem' => $varsDictionaryItem,
		);

		return $data;

	}

	/**
		(array(
			'vars'             => $vars,
			'varsItem'         => $arr['varsItem'],
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _getVarsSelect($arr)
	{
		$arrayNew = array();
		$array = &$arr['vars']['portal']['varsDetail']['templateDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == $arr['idTarget']) {
				$arrayOption = $value['arrayOption'];
				foreach ($arrayOption as $keyOption => $valueOption) {
					$arrayNew['arrStrTitle'][$valueOption['value']] = $valueOption;
				}
				return $arrayNew;
			}
		}
	}

	/**
		(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		))
	 */
	protected function _updateVars($arr)
	{
		$vars = $arr['vars'];
		$varsSearch = &$vars['portal']['varsNavi']['search']['varsDetail']['varsSearchItem']['templateDetail'];

		$varsSearch['restOption']['commaFs'] = $arr['varsItem']['arrAccountTitle']['arrSelectTag'];

		if (!$arr['varsItem']['arrDepartment']['arrSelectTag']) {
			$arrayNew = array();
			$array = $varsSearch['firstOption'];
			foreach ($array as $key => $value) {
				if (preg_match("/^commaDepartment/", $value['value'])) {
					continue;
				}
				$arrayNew[] = $value;
			}
			$varsSearch['firstOption'] = $arrayNew;

		} else {
			$varsSearch['restOption']['commaDepartment'] = $arr['varsItem']['arrDepartment']['arrSelectTag'];
		}

		if (!$arr['varsItem']['varsEntityNation']['flagConsumptionTaxFree']) {
			$arrayOption = array();
			if ($arr['varsItem']['varsEntityNation']['flagConsumptionTaxGeneralRule']) {
				if ($arr['varsItem']['varsEntityNation']['flagConsumptionTaxDeducted']) {
					$arrayOption = $arr['varsItem']['varsConsumptionTax']['generalEach'];

				} else {
					$arrayOption = $arr['varsItem']['varsConsumptionTax']['generalProration'];
				}

			} else {
				$arrayOption = $arr['varsItem']['varsConsumptionTax']['simple'];
			}
			$varsSearch['restOption']['commaTax'] = $arrayOption;

		} else {
			$arrayNew = array();
			$array = $varsSearch['firstOption'];
			foreach ($array as $key => $value) {
				if (preg_match("/^commaTax\-/", $value['value'])) {
					continue;
				}
				$arrayNew[] = $value;
			}
			$varsSearch['firstOption'] = $arrayNew;
		}

		if (!$arr['varsItem']['varsEntityNation']['flagConsumptionTaxFree'] && !$arr['varsItem']['varsEntityNation']['flagConsumptionTaxIncluding']) {
			$arrWithoutCalc = $this->_getWithoutCalc(array('vars' => $arr['varsItem']['varsConsumptionTax']['arrStrWithoutCalc']));
			$varsSearch['restOption']['commaTaxWithoutCalc'] = $arrWithoutCalc['arrSelectTag'];

		} else {
			$arrayNew = array();
			$array = $varsSearch['firstOption'];
			foreach ($array as $key => $value) {
				if (preg_match("/^commaTaxWithoutCalc/", $value['value'])) {
					continue;
				}
				$arrayNew[] = $value;
			}
			$varsSearch['firstOption'] = $arrayNew;
		}

		return $vars;
	}

	/**
		(array(
			'vars' => $vars,
		))
	 */
	protected function _getWithoutCalc($arr)
	{
		global $varsPluginAccountingAccount;

		$vars = $arr['vars'];

		$arrayNew = array();
		$array = $vars;
		foreach ($array as $key => $value) {
			$temp = array();
			$temp['strTitle'] = $value;
			$temp['value'] = $key;
			$arrayNew[] = $temp;
		}
		$data = array(
			'arrStrTitle'  => $vars,
			'arrSelectTag' => $arrayNew,
		);

		return $data;
	}

	/**
		(array(
			'vars'             => $vars,
			'varsItem'         => $arr['varsItem'],
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _updateVarsTemplateDetail($arr)
	{
		$arrayNew = array();
		$array = &$arr['vars']['portal']['varsDetail']['templateDetail'];
		foreach ($array as $key => $value) {
			$method = '_updateVarsTemplateDetail' . $value['id'];
			if (method_exists($this, $method)) {
				$value = $this->$method(array(
					'value'    => $value,
					'vars'     => $arr['vars'],
					'varsItem' => $arr['varsItem'],
				));
			}
			$arrayNew[] = $value;
		}

		return $arrayNew;
	}

	/**
		(array(
			'value'    => $value,
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		))
	 */
	protected function _updateVarsTemplateDetailNumColStampBook($arr)
	{
		for ($i = 1; $i <= 20; $i++) {
			$arr['value']['arrayOption'][] = array(
				'strTitle' => $i . $arr['value']['varsTmpl']['strCol'],
				'value'    => $i
			);
		}

		return $arr['value'];
	}

	/**
		(array(
			'value'    => $value,
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		))
	 */
	protected function _updateVarsTemplateDetailNumColNumValue($arr)
	{
		for ($i = 1; $i <= 20; $i++) {
			$arr['value']['arrayOption'][] = array(
				'strTitle' => $i . $arr['value']['varsTmpl']['strCol'],
				'value'    => $i
			);
		}

		return $arr['value'];
	}

	/**
		(array(
			'value'    => $value,
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		))
	 */
	protected function _updateVarsTemplateDetailJsonDetail($arr)
	{
		$varsJournalItem = $this->_getVarsJournalItem();
		$arrayOption = $varsJournalItem['arrayOption'];
		$i = 0;
		foreach ($arrayOption as $keyOption => $valueOption) {
			//$i dummy data cause not same value
			$arrayOption[$keyOption]['value'] = $valueOption['value'] . ',' . $i;
			$i++;
		}
		$arr['value']['varsFormJournal']['varsTmpl']['varsSelectTag']['varsBtnDictionary'] = $arrayOption;

		return $arr['value'];
	}

	/**

	 */
	protected function _getVarsJournalItem()
	{
		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJournal'],
		));

		return $vars;
	}

	/**
		(array(
			'value'    => $value,
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		))
	 */
	protected function _updateVarsTemplateDetailNumColStrTitle($arr)
	{
		for ($i = 1; $i <= 20; $i++) {
			$arr['value']['arrayOption'][] = array(
				'strTitle' => $i . $arr['value']['varsTmpl']['strCol'],
				'value'    => $i
			);
		}

		return $arr['value'];
	}

	/**
	 *
	 */
	protected function _updateSearch($arr)
	{
		global $classHtml;
		global $classEscape;

		global $varsAccount;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccountsEntity;
		global $varsPluginAccountingAuthority;

		global $varsPluginAccountingAccount;

		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$idEntity  = $varsPluginAccountingAccount['idEntityCurrent'];
		$strCheckStamp = 'accountingLogImportJpn_' . $idEntity . '_' . $numFiscalPeriod;

		$vars = $arr['vars'];
		$rows = $arr['rows'];

		$classCalcLog = $this->_getClassCalc(array('flagType' => 'Log'));

		$varsEntityNation = $vars['varsRule']['varsEntityNation'];

		$varsAuthority = $this->_getVarsAuthority(array());
		$dataAuthority = array(
			'flagInsert' => ($varsAuthority == 'admin' || $varsAuthority['flagAllInsert'])? 1 : 0,
			'flagUpdate' => ($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'])? 1 : 0,
			'flagDelete' => ($varsAuthority == 'admin' || $varsAuthority['flagAllDelete'])? 1 : 0,
		);

		$flagAddUse = 0;
		if ($varsAuthority == 'admin' || $varsAuthority['flagAllInsert']) {
			$flagAddUse = 1;
		}
		$vars['portal']['varsDetail']['varsStart']['varsEdit']['flagAddUse'] = $flagAddUse;

		$numLine = 0;
		$array = $rows['arrRows'];
		$arraySide = array('Debit', 'Credit');
		$arrayNew = array();
		$num = 0;
		$flag = 0;
		foreach ($array as $key => $value) {
			$varsTmpl = $vars['portal']['varsList']['templateDetail'];
			$varsTmpl['id'] = $value['idLogImport'];
			$varsTmpl['vars']['idTarget'] = $value['idLogImport'];
			$varsTmpl['numSort'] = (int) $key;

			if ($varsAccount['jsonStampCheck'][$strCheckStamp] < $value['stampRegister']) {
				$flag = 1;

			} else {
				$varsTmpl['strClassLoad'] = '';

			}

			$varsTmpl['jsonPermitHistory'] = $this->_updateSearchJsonPermitHistory(array(
				'vars'  => $vars,
				'value' => $value['jsonPermitHistory'],
			));

			$flagPermitLost = $this->_checkPermitLost(array(
				'classCalcLog' => $classCalcLog,
				'value'        => $value,
			));
			$varsTmpl['vars']['flagPermitLost'] = $flagPermitLost;

			$numPermitEnd = count($varsTmpl['jsonPermitHistory']) - 1;
			if (!$value['arrCommaIdAccountPermit']) {
				$varsTmpl['arrCommaIdAccountPermit'] = array();

			} else {
				$varsTmpl['arrCommaIdAccountPermit'] = $this->_updateSearchArrIdAccountPermit(array(
					'arrIdAccountPermit' => $value['jsonPermitHistory'][$numPermitEnd]['arrIdAccountPermit'],
				));
			}

			$varsTmpl['strTitle'] = $value['strTitle'];
			$varsTmpl['stampRegister'] = $value['stampRegister'];
			$varsTmpl['stampUpdate'] = $value['stampUpdate'];
			$varsTmpl['arrSpaceStrTag'] = preg_replace('/^ /', '', $value['arrSpaceStrTag']);
			$varsTmpl['varsAuthority'] = $dataAuthority;

			if (!($varsAuthority == 'admin' || $varsAuthority['flagAllDelete'])) {
				$varsTmpl['flagDefault'] = 1;
			}

			$varsTmpl['flagCheckboxUse'] = ($varsTmpl['flagDefault'])? 0 : 1;

			$varsTmpl['flagAttest'] = $value['flagAttest'];

			$varsTmpl['varsColumnDetail']['id'] = $varsTmpl['id'];
			$varsTmpl['varsColumnDetail']['strTitle'] = $value['strTitle'];
			$varsTmpl['varsColumnDetail']['stampRegister'] = $value['stampRegister'];
			$varsTmpl['varsColumnDetail']['stampUpdate'] = $value['stampUpdate'];

			$varsTmpl['varsColumnDetail']['flagAttest']
				= $arr['varsItem']['varsFlagAttest']['arrStrTitle'][$value['flagAttest']]['strTitle'];

			$varsTmpl['numColStampBook'] = (int) $value['numColStampBook'];
			$varsTmpl['numColNumValue'] = (int) $value['numColNumValue'];
			$varsTmpl['numColStrTitle'] = (int) $value['numColStrTitle'];

			$varsTmpl['varsColumnDetail']['numColStampBook']
				= $value['numColStampBook'] . $vars['varsItem']['strCol'];

			$varsTmpl['varsColumnDetail']['numColNumValue']
				= $value['numColNumValue'] . $vars['varsItem']['strCol'];

			$varsTmpl['varsColumnDetail']['numColStrTitle']
				= $value['numColStrTitle'] . $vars['varsItem']['strCol'];

			$varsTmpl['vars']['flagAttest'] = $value['flagAttest'];
			$varsTmpl['vars']['id'] = $varsTmpl['id'];
			$varsTmpl['vars']['numColStampBook'] = $varsTmpl['numColStampBook'];
			$varsTmpl['vars']['numColNumValue'] = $varsTmpl['numColNumValue'];
			$varsTmpl['vars']['numColStrTitle'] = $varsTmpl['numColStrTitle'];
			$varsTmpl['vars']['strTitle'] = $value['strTitle'];
			$varsTmpl['vars']['arrSpaceStrTag'] = $classEscape->splitSpaceArrayData(array('data' => $varsTmpl['arrSpaceStrTag']));

			$varsTmpl['varsScheduleDetail']['stamp'] = $value['stampRegister'];


			if ($numLine % 2 == 0) {
				$varsTmpl['strClassBg'] = $vars['varsItem']['strClassBg'];
			}

			$varsTmpl['jsonVersion'] = $this->_updateSearchJsonVersion(array(
				'vars'  => $vars,
				'value' => $value['jsonVersion'],
			));
			$numVersionEnd = count($varsTmpl['jsonVersion']) - 1;
			$arrayDetail = $varsTmpl['jsonVersion'][$numVersionEnd]['jsonDetail']['varsDetail'];
			$varsTmpl['jsonDetail'] = $varsTmpl['jsonVersion'][$numVersionEnd];
			$varsTmpl['jsonVersion'] = array();

			$numLine++;
			$numLoop = 0;
			$flagIdLost = 0;
			foreach ($arrayDetail as $keyDetail => $valueDetail) {
				foreach ($arraySide as $keySide => $valueSide) {

					$varsTmpl['vars']['idAccountTitle' . $valueSide] = '';
					$varsTmpl['vars']['idSubAccountTitle' . $valueSide] = '';
					$varsTmpl['vars']['idDepartment' . $valueSide] = '';

					$varsTmpl['varsColumnDetail']['idAccountTitle' . $valueSide] = '';
					$varsTmpl['varsColumnDetail']['idSubAccountTitle' . $valueSide] = '';
					$varsTmpl['varsColumnDetail']['idDepartment' . $valueSide] = '';
					$varsTmpl['varsColumnDetail']['flagConsumptionTax' . $valueSide] = '';

					$idAccountTitle = $valueDetail['arr' . $valueSide]['idAccountTitle'];
					$idDepartment = $valueDetail['arr' . $valueSide]['idDepartment'];
					$idSubAccountTitle = $valueDetail['arr' . $valueSide]['idSubAccountTitle'];
					$flagConsumptionTaxGeneralRuleEach = $valueDetail['arr' . $valueSide]['flagConsumptionTaxGeneralRuleEach'];
					$flagConsumptionTaxGeneralRuleProration = $valueDetail['arr' . $valueSide]['flagConsumptionTaxGeneralRuleProration'];
					$flagConsumptionTaxSimpleRule = $valueDetail['arr' . $valueSide]['flagConsumptionTaxSimpleRule'];
					$flagConsumptionTaxWithoutCalc = $valueDetail['arr' . $valueSide]['flagConsumptionTaxWithoutCalc'];
					$flagConsumptionTaxCalc = $valueDetail['arr' . $valueSide]['flagConsumptionTaxCalc'];
					$flagConsumptionTaxIncluding = $valueDetail['arr' . $valueSide]['flagConsumptionTaxIncluding'];
					$flagConsumptionTaxFree = $valueDetail['arr' . $valueSide]['flagConsumptionTaxFree'];

					if ($idAccountTitle) {

						//strAccountTitle
						$strAccountTitle = $vars['varsRule']['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['strTitleFS'];

						$varsTmpl['varsColumnDetail']['idAccountTitle' . $valueSide] = $strAccountTitle;
						$varsTmpl['vars']['idAccountTitle' . $valueSide] = $idAccountTitle;
						if (!$strAccountTitle) {
							$flagIdLost = 1;
							$varsTmpl['varsColumnDetail']['idAccountTitle' . $valueSide] = $vars['varsItem']['strLostItem'];
							$varsTmpl['vars']['idAccountTitle' . $valueSide] = '';
						}

						//strSubAccountTitle
						$idSubAccountTitle = $valueDetail['arr' . $valueSide]['idSubAccountTitle'];
						$strSubAccountTitle = $vars['varsRule']['arrSubAccountTitle']['arrStrTitle'][$idAccountTitle][$idSubAccountTitle]['strTitle'];
						if ($strSubAccountTitle) {
							$varsTmpl['varsColumnDetail']['idSubAccountTitle' . $valueSide] = $strSubAccountTitle;
							$varsTmpl['vars']['idSubAccountTitle' . $valueSide] = $idSubAccountTitle;
						}
						if ($idSubAccountTitle && !$strSubAccountTitle) {
							$flagIdLost = 1;
							$varsTmpl['varsColumnDetail']['idSubAccountTitle' . $valueSide] = $vars['varsItem']['strLostItem'];
							$varsTmpl['vars']['idSubAccountTitle' . $valueSide] = '';
						}

						if (!$flagConsumptionTaxFree) {
							$flagTax = 0;

							//strNumValueConsumptionTax
							if ((int) $varsEntityNation['flagConsumptionTaxGeneralRule']) {
								if ((int) $varsEntityNation['flagConsumptionTaxDeducted']) {
									if (preg_match("/^tax/", $flagConsumptionTaxGeneralRuleEach)) {
										$flagTax = 1;
									}

								} else {
									if (preg_match("/^tax/", $flagConsumptionTaxGeneralRuleProration)) {
										$flagTax = 1;
									}
								}

							} else {
								if (preg_match("/^tax/", $flagConsumptionTaxSimpleRule)) {
									$flagTax = 1;
								}
							}

							//strConsumptionTax
							if ((int) $varsEntityNation['flagConsumptionTaxGeneralRule']) {
								if ((int) $varsEntityNation['flagConsumptionTaxDeducted']) {
									if ($flagConsumptionTaxGeneralRuleEach
										&& $flagConsumptionTaxGeneralRuleEach != 'none'
									) {
										$strConsumptionTax = $vars['varsRule']['varsConsumptionTax']['arrStrGeneralEach'][$flagConsumptionTaxGeneralRuleEach];
										$varsTmpl['varsColumnDetail']['flagConsumptionTax' . $valueSide] = $strConsumptionTax;
									}

								} else {
									if ($flagConsumptionTaxGeneralRuleProration
										&& $flagConsumptionTaxGeneralRuleProration != 'none'
									) {
										$strConsumptionTax = $vars['varsRule']['varsConsumptionTax']['arrStrGeneralProration'][$flagConsumptionTaxGeneralRuleProration];
										$varsTmpl['varsColumnDetail']['flagConsumptionTax' . $valueSide] = $strConsumptionTax;
									}
								}

							} else {
								if ($flagConsumptionTaxSimpleRule
									&& $flagConsumptionTaxSimpleRule != 'none'
								) {
									$strConsumptionTax = $vars['varsRule']['varsConsumptionTax']['arrStrSimple'][$flagConsumptionTaxSimpleRule];
									$varsTmpl['varsColumnDetail']['flagConsumptionTax' . $valueSide] = $strConsumptionTax;
								}
							}

							//strConsumptionTaxCalc
							if ($flagTax
								&& $idAccountTitle != 'suspenseReceiptOfConsumptionTaxes'
								&& $idAccountTitle != 'suspensePaymentConsumptionTaxes'
								&& !$flagConsumptionTaxIncluding
							) {
								if (!$flagConsumptionTaxIncluding) {
									if (!$flagConsumptionTaxWithoutCalc) {
										$flagConsumptionTaxWithoutCalc = (int) $varsEntityNation['flagConsumptionTaxWithoutCalc'];
									}
									//$rowData['strConsumptionTaxCalc' . $valueSide] = $vars['varsRule']['varsConsumptionTax']['arrStrWithoutCalc'][$flagConsumptionTaxWithoutCalc];
								}
							}
						}

						//strDepartment
						$strDepartment = $vars['varsRule']['arrDepartment']['arrStrTitle'][$idDepartment]['strTitle'];
						if ($strDepartment) {
							$varsTmpl['varsColumnDetail']['idDepartment' . $valueSide] = $strDepartment;
							$varsTmpl['vars']['idDepartment' . $valueSide] = $idDepartment;
						}
						if ($idDepartment && !$strDepartment) {
							$flagIdLost = 1;
							$varsTmpl['varsColumnDetail']['idDepartment' . $valueSide] = $vars['varsItem']['strLostItem'];
							$varsTmpl['vars']['idDepartment' . $valueSide] = '';
						}
					}
				}

				$varsTmpl['vars']['flagIdLost'] = $flagIdLost;

				if ($flagIdLost || $flagPermitLost) {
					$varsTmpl['strClassFont'] = $vars['varsItem']['strClassLost'];
				}

				$varsTmpl['id'] .= '_' . $numLoop;
				if ($numLoop) {
					$varsTmpl['flagCheckboxUse'] = 0;
					$varsTmpl['strClassLoad'] = '';
					$varsTmpl['strClass'] = $vars['varsItem']['strClassBlank'];
					$varsTmpl['flagBtnUse'] = 0;
					$varsTmpl['flagMoveUse'] = 0;
					$varsTmpl['varsColumnDetail']['flagAttest'] = '';
					$varsTmpl['varsColumnDetail']['strTitle'] = '';
					$varsTmpl['varsColumnDetail']['id'] = '';
					$varsTmpl['varsColumnDetail']['numColStampBook'] = '';
					$varsTmpl['varsColumnDetail']['numColNumValue'] = '';
					$varsTmpl['varsColumnDetail']['numColStrTitle'] = '';
					$varsTmpl['varsScheduleDetail']['flagType'] = '';
				}
				$numLoop++;
				$arrayColumnDetail = &$varsTmpl['varsColumnDetail'];
				foreach ($arrayColumnDetail as $keyColumnDetail => $valueColumnDetail) {
					if (is_null($valueColumnDetail)) {
						$arrayColumnDetail[$keyColumnDetail] = '';
					}
				}
				$arrayNew[] = $varsTmpl;
			}
		}
		$vars['portal']['varsList']['varsPage']['varsStatus']['numRows'] = $rows['numRows'];
		$vars['portal']['varsList']['varsDetail'] = $arrayNew;

		if (!$arr['flagVars']) {
			$varsTemp = $classHtml->allot(array(
				'strClass'    => 'Table',
				'flagStatus'  => 'Html',
				'numTimeZone' => $varsAccount['numTimeZone'],
				'varsDetail'  => $arrayNew,
				'varsColumn'  => $vars['portal']['varsList']['table']['varsDetail']['varsColumn'],
				'varsStatus'  => $vars['portal']['varsList']['table']['varsDetail']['varsStatus'],
			));
			$vars['portal']['varsList']['varsHtml'] = $varsTemp['strHtml'];
			if ($flag) {
				$this->_setDbStampCheck(array(
					'strColumnAccount'    => $strCheckStamp,
					'strColumnPreference' => 'accounts',
				));
			}
		}

		return $vars;
	}

	/**
		(array(
			'vars' => array,
			'value' => array,
		))
	 */
	protected function _updateSearchJsonPermitHistory($arr)
	{
		global $varsAccounts;
		global $varsAccount;
		global $varsPluginAccountingAccountsId;

		$array = $arr['value'];
		$arrayNew = array();
		foreach ($array as $key => $value) {
			$data = array();
			$data['stampRegister'] = $value['stampRegister'];
			$strCodeName = $varsAccounts[$value['idAccountApply']]['strCodeName'];
			if (!$strCodeName) {
				$strCodeName = $varsPluginAccountingAccountsId[$value['idAccountApply']]['strCodeName'];
			}
			$data['strCodeName'] = $strCodeName;
			$data['flagInvalid'] = (int) $value['flagInvalid'];
			$data['numSumMax'] = (int) $value['numSumMax'];

			$arrayNewPermit = array();
			$numPermit = 0;
			$numPermitBack = 0;
			$stampPermit = 0;
			$arrayPermit = $value['arrIdAccountPermit'];
			$strStatus = '';
			foreach ($arrayPermit as $keyPermit => $valuePermit) {
				if ($valuePermit['flagPermit'] == 'done') {
					$numPermit++;
					if ($stampPermit < $valuePermit['stampRegister']) {
						$stampPermit = $valuePermit['stampRegister'];
					}

				} elseif ($valuePermit['flagPermit'] == 'back') {
					$numPermitBack++;
				}

				$dataPermit = $valuePermit;
				$strCodeName = $varsAccounts[$dataPermit['idAccount']]['strCodeName'];
				if (!$varsAccounts[$dataPermit['idAccount']]['strCodeName']) {
					$strCodeName = $varsPluginAccountingAccountsId[$dataPermit['idAccount']]['strCodeName'];
				}
				$dataPermit['strCodeName'] = $strCodeName;
				if ($varsAccount['id'] == $dataPermit['idAccount']) {
					if ($valuePermit['flagPermit'] == 'done') {
						$strStatus = $arr['vars']['varsItem']['strPermitDone'];

					} elseif ($valuePermit['flagPermit'] == 'back') {
						$strStatus = $arr['vars']['varsItem']['strPermitDone'];

					} elseif ($valuePermit['flagPermit'] == 'none') {
						$strStatus = $arr['vars']['varsItem']['strPermitNeed'];
					}
				}
				$arrayNewPermit[] = $dataPermit;
			}
			$data['stampPermit'] = $stampPermit;
			$data['numSumPermit'] = $numPermit;
			$data['numSumBack'] = $numPermitBack;

			$numSumMax = count($arrayPermit) - $data['numSumBack'];
			if ($data['flagInvalid']) {
				$data['stampPermit'] = 0;
				if ($numSumMax < $data['numSumMax']) {
					$data['strStatus'] = $arr['vars']['varsItem']['strApplyBack'];
				} else {
					$data['strStatus'] = $arr['vars']['varsItem']['strRevocation'];
				}


			} else {
				if ($data['numSumPermit'] >= $data['numSumMax']) {
					$data['strStatus'] = $arr['vars']['varsItem']['strDone'];

				} elseif ($numSumMax < $data['numSumMax']) {
					$data['stampPermit'] = 0;
					$data['strStatus'] = $arr['vars']['varsItem']['strApplyBack'];

				} else {
					$data['stampPermit'] = 0;
					if ($strStatus) {
						$data['strStatus'] = $strStatus;

					} else {
						$data['strStatus'] = $arr['vars']['varsItem']['strApply'];
					}
				}

			}

			$data['arrIdAccountPermit'] = $arrayNewPermit;

			$arrayNew[] = $data;
		}

		return $arrayNew;
	}

	/**
		(array(
			'classCalcLog' => $classCalcLog,
			'value'        => $value,
		))
	 */
	protected function _checkPermitLost($arr)
	{
		global $varsPluginAccountingAccount;

		$value = $arr['value'];
		$classCalcLog = &$arr['classCalcLog'];

		$varsPermitHistory = end($value['jsonPermitHistory']);
		$varsOrder = array(
			'numFiscalPeriod'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'                => $varsPluginAccountingAccount['idEntityCurrent'],
			'idAccount'               => $value['idAccount'],
			'idAccountApply'          => $value['idAccountApply'],
			'flagFiscalReport'        => 'none',
			'stampBook'               => '',
			'strTitle'                => '',
			'jsonDetail'              => '',
			'arrCommaIdLogFile'       => '',
			'arrCommaIdAccountPermit' => $value['arrCommaIdAccountPermit'],
			'numSumMax'               => $varsPermitHistory['numSumMax'],
			'arrSpaceStrTag'          => '',
		);

		$flag = $classCalcLog->allot(array(
			'flagStatus'      => 'check',
			'varsOrder'       => $varsOrder,
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagCheck'       => 'Permit',
			'varsItem'        => array('dummy'),
		));

		return ($flag)? 1 : 0;
	}

	/**
	 	'arrIdAccountPermit' => $value['jsonPermitHistory'][$numPermitEnd]['arrIdAccountPermit'],
	 */
	protected function _updateSearchArrIdAccountPermit($arr)
	{
		global $varsAccounts;
		global $classEscape;
		global $varsPluginAccountingAccountsId;

		$arrayNew = array();

		if (!$arr['arrIdAccountPermit']) {
			return $arrayNew;
		}

		$array = $arr['arrIdAccountPermit'];
		$num = 0;
		foreach ($array as $key => $value) {
			$data = array();
			$strCodeName = $varsAccounts[$value['idAccount']]['strCodeName'];
			if (!$strCodeName) {
				$strCodeName = $varsPluginAccountingAccountsId[$value['idAccount']]['strCodeName'];
			}
			$data['strTitle'] = $strCodeName;
			$data['id'] = $value['idAccount'];
			$arrayNew[$num] = $data;
			$num++;
		}

		return $arrayNew;
	}

	/**
		(array(
				'vars'          => $vars,
				'value'         => $value['jsonVersion'],
		))
	 */
	protected function _updateSearchJsonVersion($arr)
	{
	    /*
	     * 20191001 start
	     */
	    $classCalcConsumptionTax = $this->_getClassCalc(array('flagType' => 'ConsumptionTax'));
	    /*
	     * 20191001 end
	     */

	    $array = $arr['value'];
		$arrayNew = array();
		$num = 1;
		foreach ($array as $key => $value) {
			$data = array();
			$data['stampRegister'] = $value['stampRegister'];
			$data['stampUpdate'] = $value['stampUpdate'];
			/*
			 * 20191001 start
			 */
			$value['jsonDetail'] = $classCalcConsumptionTax->allot(array(
			    'flagStatus' => 'sendValueConsumptionTaxReduced',
			    'jsonDetail'   => $value['jsonDetail'],
			));
			/*
			 * 20191001 end
			 */
			$data['jsonDetail'] = $value['jsonDetail'];
			$data['strVersion'] = 'Ver.' . $num;
			$data['numVersion'] = $num;
			$arrayNew[] = $data;
			$num++;
		}
		return $arrayNew;
	}

	/**
	 *
	 */
	protected function _iniNaviSearchSave()
	{
		$this->_setNaviSearchSave(array(
			'pathVars'    => $this->_extSelf['pathVarsJs'],
			'strTable'    => 'accountingAccountMemo',
			'strColumn'   => 'jsonLogImportNaviSearch',
			'flagEntity'  => 1,
			'flagAccount' => 1,
		));
	}

	/**
	 *
	 */
	protected function _iniNaviSearchDelete()
	{
		$this->_setNaviSearchDelete(array(
			'strTable'  => 'accountingAccountMemo',
			'strColumn' => 'jsonLogImportNaviSearch',
			'flagEntity'  => 1,
			'flagAccount' => 1,
		));
	}

	/**
		$this->_setNaviSearchSave(array(
			'pathVars'  => '',
			'strTable'  => '',
			'strColumn' => '',
			'flagEntity' => 0,
			'flagAccount' => 0,
		));
	 */
	protected function _setNaviSearchSave($arr)
	{
		global $varsRequest;
		global $classDb;
		$dbh = $classDb->getHandle();
		global $varsAccount;
		global $varsPluginAccountingAccount;

		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$vars['varsRule'] = $varsItem;

		$varsJson = $this->checkValueSearch(array(
			'varsValue'  => ($varsRequest['query']['jsonValue']['vars'])? $varsRequest['query']['jsonValue']['vars'] : array(),
			'varsSearch' => $vars['portal']['varsNavi']['search'],
		));

		$strJson = json_encode($varsJson);
		$this->checkTextSize(array(
			'flag' => 'errorDataMax',
			'str'  => $strJson,
		));
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$idAccount = $varsAccount['id'];
		$arrWhere = array();
		if ($arr['flagEntity']) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idEntity',
				'flagCondition' => 'eq',
				'value'         => $idEntity,
			);
		} else {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idEntity',
				'flagCondition' => 'eq',
				'value'         => 0,
			);
		}

		if ($arr['flagAccount']) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idAccount',
				'flagCondition' => 'eq',
				'value'         => $idAccount,
			);
		}

		$arrWhere[] = array(
			'flagType'      => '',
			'strColumn'     => 'flagColumn',
			'flagCondition' => 'eq',
			'value'         => $arr['strColumn'],
		);

		try {
			$dbh->beginTransaction();

			$classDb->updateRow(array(
				'idModule' => 'accounting',
				'strTable'  => $arr['strTable'],
				'arrColumn' => array('jsonData'),
				'flagAnd'   => 1,
				'arrWhere'  => $arrWhere,
				'arrValue'  => array($strJson),
			));

			if ($arr['flagAccount']) {
				$this->updateDbAccountStamp();
				$this->_updateDbPreferenceStamp(array('strColumn' => 'accounts'));
			}

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$this->sendVars(array(
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $this->_getMemo(array(
				'strTable'    => $arr['strTable'],
				'strColumn'   => $arr['strColumn'],
				'flagEntity'  => $arr['flagEntity'],
				'flagAccount' => $arr['flagAccount'],
			)),
		));
	}

	/**
	 *
	 */
	protected function _iniNaviSearchReload()
	{
		global $varsPluginAccountingAccount;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingAccount['stampUpdate'],
				'flagSearch'  => 0,
			));
		}

		$this->_setNaviSearchReload(array(
			'pathVars'  => $this->_extSelf['pathVarsJs'],
			'strTable'  => 'accountingAccountMemo',
			'strColumn' => 'jsonLogImportNaviSearch',
			'flagEntity'  => 1,
			'flagAccount' => 1,
		));
	}

	/**
		$this->_setNaviSearchReload(array(
			'pathVars'  => '',
			'strTable'  => '',
			'strColumn' => '',
			'flagEntity' => 0,
			'flagAccount' => 0,
		));
	 */
	protected function _setNaviSearchReload($arr)
	{
		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$vars['portal']['varsNavi']['search']['varsDetail']['varsMyRecord']['varsFormList']['varsDetail'] = $this->_getMemo(array(
			'strTable'    => $arr['strTable'],
			'strColumn'   => $arr['strColumn'],
			'flagEntity'  => $arr['flagEntity'],
			'flagAccount' => $arr['flagAccount'],
		));

		$this->sendVars(array(
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $vars['portal']['varsNavi']['search']['varsDetail'],
		));
	}

	/**
	 *
	 */
	protected function _iniNaviSearch()
	{
		$this->_setSearch(array('flag' => 1));
	}

	/**
	 * array(
	 * 	'flag' => int
	 * )
	 */
	protected function _setSearch($arr)
	{
		global $varsRequest;
		global $varsPluginAccountingPreference;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingPreference['jsonStampUpdate']['entity'],
				'flagSearch'  => 1,
			));
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$vars['varsRule'] = $varsItem;

		$vars['portal']['varsDetail']['templateDetail'] = $this->_updateVarsTemplateDetail((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$this->checkSearch(array(
			'arrOrder' => $vars['portal']['varsNavi']['search']['varsDetail']['varsSearchSort'],
			'arrWhere' => $vars['portal']['varsNavi']['search']['varsDetail']['varsSearchItem'],
		));

		if (!$varsRequest['query']['jsonSearch']['ph']['arrOrder']) {
			$varsRequest['query']['jsonSearch']['ph']['arrOrder']= array(
				'strColumn' => 'id',
				'flagDesc'  => 1,
			);
		}

		$rows = $this->getSearch(array(
			'idModule'   => 'accounting',
			'numLotNow'  => $varsRequest['query']['jsonSearch']['numLotNow'],
			'strTable'   => 'accountingLogImport' . $strNation,
			'arrOrder'   => $varsRequest['query']['jsonSearch']['ph']['arrOrder'],
			'insCurrent' => $this,
			'arrWhere'   => $varsRequest['query']['jsonSearch']['ph']['arrWhere'],
		));

		$vars = $this->_updateSearch(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'rows'     => $rows,
		));

		$this->sendVars(array(
			'flag'    => ($arr['flag'])? $arr['flag'] : 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'numRows'    => $rows['numRows'],
				'varsDetail' => $vars['portal']['varsList']['varsDetail'],
				'varsHtml'   => $vars['portal']['varsList']['varsHtml'],
			),
		));
	}

	/**
	 *
	 */
	protected function _iniListReload()
	{
		$this->_setSearch(array('flag' => 1));
	}

	/**
	 *
	 */
	protected function _iniDetailReload()
	{
		$this->_iniSearchDetail();
	}

	/**
	 *
	 */
	protected function _iniSearchDetail()
	{
		global $varsRequest;
		global $varsPluginAccountingPreference;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;


		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingPreference['jsonStampUpdate']['log'],
				'flagSearch'  => 1,
			));
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$vars['varsRule'] = $varsItem;

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$vars['portal']['varsDetail']['templateDetail'] = $this->_updateVarsTemplateDetail((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$this->checkSearch(array(
			'arrOrder' => $vars['portal']['varsNavi']['search']['varsDetail']['varsSearchSort'],
			'arrWhere' => $vars['portal']['varsNavi']['search']['varsDetail']['varsSearchItem'],
		));

		if (!$varsRequest['query']['jsonSearch']['ph']['arrOrder']) {
			$varsRequest['query']['jsonSearch']['ph']['arrOrder']= array(
				'strColumn' => 'id',
				'flagDesc'  => 1,
			);
		}

		$rows = $this->getSearch(array(
			'idModule'   => 'accounting',
			'numLotNow'  => $varsRequest['query']['jsonSearch']['numLotNow'],
			'strTable'   => 'accountingLogImport' . $strNation,
			'arrOrder'   => $varsRequest['query']['jsonSearch']['ph']['arrOrder'],
			'insCurrent' => $this,
			'arrWhere'   => $varsRequest['query']['jsonSearch']['ph']['arrWhere'],
		));

		$vars = $this->_updateSearch(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'rows'     => $rows,
		));

		$varsTarget = $vars;

		$rowsTarget = $this->getSearch(array(
			'idModule'  => 'accounting',
			'numLotNow' => 0,
			'strTable'  => 'accountingLogImport' . $strNation,
			'arrOrder'  => array(),
			'flagAnd'   => 1,
			'arrWhere'  => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idLogImport',
					'flagCondition' => 'eq',
					'value'         => $varsRequest['query']['jsonValue']['idTarget'],
				),
			),
			'insCurrent'  => $this,
		));

		if (!$rowsTarget['numRows']) {
			$this->_sendOldError();
		}

		$varsTarget = $this->_updateSearch(array(
			'vars'     => $varsTarget,
			'rows'     => $rowsTarget,
			'varsItem' => $varsItem,
			'flagVars' => 1,
		));

		$this->sendVars(array(
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'varsDetail' => $varsTarget['portal']['varsList']['varsDetail'][0],
				'numRows'    => $rows['numRows'],
				'numLotNow'  => $varsRequest['query']['jsonSearch']['numLotNow'],
				'varsList'   => $vars['portal']['varsList']['varsDetail'],
				'varsHtml'   => $vars['portal']['varsList']['varsHtml'],
			),
		));
	}

	/**
	 *
	 */
	protected function _iniDetailDelete()
	{
		global $varsRequest;

		$this->_setDelete(array(
			'arrId' => array($varsRequest['query']['jsonValue']['idTarget']),
		));
	}

	/**
	 *
	 */
	protected function _iniListDelete()
	{
		global $varsRequest;

		$this->_setDelete(array(
			'arrId' => $varsRequest['query']['jsonValue']['vars'],
		));
	}

	/**
	 (array(

	 ))
	 */
	protected function _getVarsLog($arr)
	{
		global $classDb;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogImport' . $strNation,
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere'  => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idEntity',
					'flagCondition' => 'eq',
					'value'         => $arr['idEntity'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'numFiscalPeriod',
					'flagCondition' => 'eq',
					'value'         => $arr['numFiscalPeriod'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idLogImport',
					'flagCondition' => 'eq',
					'value'         => $arr['idLogImport'],
				),
			),
		));

		return $rows['arrRows'];
	}

	/**
		$this->_setDelete(array(
			'arrId' => array(),
		));
	 */
	protected function _setDelete($arr)
	{
		global $classDb;
		global $classCheck;

		$dbh = $classDb->getHandle();
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllDelete'])) {
			$this->_sendOldFlag();
		}

		$array = $arr['arrId'];
		$flag = $classCheck->checkValueFormat(array(
			'flagType' => 'num',
			'flagArr'  => 1,
			'value'    => $array,
		));

		if ($flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
			}
			exit;
		}

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());

		try {
			$dbh->beginTransaction();
			foreach ($array as $key => $value) {
				$classDb->deleteRow(array(
					'idModule'  => 'accounting',
					'strTable'  => 'accountingLogImport' . $strNation,
					'flagAnd'   => 1,
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
						array(
							'flagType'      => 'num',
							'strColumn'     => 'idLogImport',
							'flagCondition' => 'eq',
							'value'         => $value,
						),
					),
				));

				$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
				if (preg_match("/^temp/", $flagCurrentFlagNow)) {
					if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
						$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] + 1;

					} elseif (preg_match("/^(tempNext)$/", $flagCurrentFlagNow)) {
						$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] - 1;
					}

					$classDb->deleteRow(array(
						'idModule'  => 'accounting',
						'strTable'  => 'accountingLogImport' . $strNation,
						'flagAnd'   => 1,
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
								'value'         => $numFiscalPeriodTemp,
							),
							array(
								'flagType'      => 'num',
								'strColumn'     => 'idLogImport',
								'flagCondition' => 'eq',
								'value'         => $value,
							),
						),
					));
				}
			}

			$array = array('logImport');
			foreach ($array as $key => $value) {
				$this->_updateDbPreferenceStamp(array('strColumn' => $value));
			}

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
}
