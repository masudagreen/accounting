<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_LogHouse extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference'    => 'logWindow',
		'pathTplJs'       => 'else/plugin/accounting/js/jpn/logHouse.js',
		'pathVarsJs'      => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/logHouse.php',
		'pathVarsJournal' => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/dictionary.php',
		'tplComment'     => 'else/plugin/accounting/html/houseComment.html',
		'arrLogTemp'   => array(),
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
		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$this->_setJs(array(
			'pathVars'        => $this->_extSelf['pathVarsJs'],
			'pathTpl'         => $this->_extSelf['pathTplJs'],
			'arrFolder' => array(),
			'arrSearch'       => array(
				'idModule'  => 'accounting',
				'numLotNow' => 0,
				'strTable'  => 'accountingLogHouse' . $strNation,
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

		$arrAccountTitleCost = $this->_getAccountTitleCost(array(
			'arrAccountTitle' => $arrAccountTitle,
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrayFSList = $this->_getFSList(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsStampFiscalPeriod = $this->_getVarsStampFiscalPeriod(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'varsEntityNation' => $varsEntityNation,
		));

		$classCalcDictionary = $this->_getClassCalc(array('flagType' => 'Dictionary'));
		$varsDictionaryItem = $classCalcDictionary->allot((array('flagStatus' => 'varsItem')));

		$varsConsumptionTax = $this->_getVarsConsumptionTax(array());

		$data = array(
			'arrSubAccountTitle'    => $arrSubAccountTitle,
			'arrAccountTitle'       => $arrAccountTitle,
			'arrAccountTitleCost'   => $arrAccountTitleCost,
			'arrDepartment'         => $arrDepartment,
			'varsEntityNation'      => $varsEntityNation,
			'arrayFSList'           => $arrayFSList,
			'varsConsumptionTax'    => $varsConsumptionTax,
			'idAccount'             => $varsAccount['id'],
			'varsStampFiscalPeriod' => $varsStampFiscalPeriod,
			'varsDictionaryItem'    => $varsDictionaryItem,
		);

		return $data;

	}

	/**
		(array(
			'arrAccountTitle' => $arrAccountTitle,
			'varsPreference'  => $varsPreference,
		))
	 */
	protected function _getAccountTitleCost($arr)
	{
		$arrStrTitle = array();
		$arrSelectTag = array();
		$array = $arr['arrAccountTitle']['arrSelectTag'];
		foreach ($array as $key => $value) {
			if ($value['flagDisabled']) {
				continue;
			}
			if ($arr['arrAccountTitle']['arrStrTitle'][$value['value']]['idParent'] != 'expense') {
				continue;
			}
			$arrStrTitle[$value['value']] = $arr['arrAccountTitle']['arrStrTitle'][$value['value']];
			$arrSelectTag[] = $value;
		}

		$data = array(
			'arrSelectTag' => $arrSelectTag,
			'arrStrTitle'  => $arrStrTitle,
		);

		return $data;
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

		$varsSearch['restOption']['commaFs'] = $arr['varsItem']['arrAccountTitleCost']['arrSelectTag'];

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
	protected function _updateVarsTemplateDetailJsonDetail($arr)
	{
		$varsJournalItem = $this->_getVarsJournalItem();
		$arrayOption = $varsJournalItem['arrayOptionHouse'];
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
		$strCheckStamp = 'accountingLogHouseJpn_' . $idEntity . '_' . $numFiscalPeriod;

		$vars = $arr['vars'];
		$rows = $arr['rows'];

		$stampBook = $vars['varsRule']['varsStampFiscalPeriod']['f1']['stampMax'];

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
			$varsTmpl['id'] = $value['idLogHouse'];
			$varsTmpl['vars']['idTarget'] = $value['idLogHouse'];
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

			$varsTmpl['jsonWriteHistory'] = '';
			$varsTmpl['vars']['jsonWriteHistory'] = array();
			if ($value['jsonWriteHistory']) {
				$tempData = $this->_getJsonWriteHistoryVarsDetail(array(
					'vars'    => $vars['varsItem']['varsJsonWriteHistory'],
					'value'   => $value['jsonWriteHistory'],
				));
				$temp = $classHtml->allot(array(
					'strClass'    => 'TableSimple',
					'flagStatus'  => 'Html',
					'varsDetail'  => $tempData['varsDetail'],
					'varsColumn'  => $vars['varsItem']['varsJsonWriteHistory']['varsColumn'],
					'varsStatus'  => $vars['varsItem']['varsJsonWriteHistory']['varsStatus'],
				));
				$varsTmpl['jsonWriteHistory'] = $temp['strHtml'];
				$varsTmpl['vars']['jsonWriteHistory'] = $tempData['varsData'];
			}

			$numWrite = count($varsTmpl['vars']['jsonWriteHistory']);
			if (!$numWrite) {
				$numWrite = '';
			}
			$varsTmpl['varsColumnDetail']['numWrite'] = $numWrite;

			$varsTmpl['strTitle'] = $value['strTitle'];
			$varsTmpl['stampRegister'] = $value['stampRegister'];
			$varsTmpl['stampUpdate'] = $value['stampUpdate'];
			$varsTmpl['arrSpaceStrTag'] = preg_replace('/^ /', '', $value['arrSpaceStrTag']);
			$varsTmpl['varsAuthority'] = $dataAuthority;

			$varsTmpl['flagBtnDelete'] = 0;
			$varsTmpl['flagBtnWrite'] = 0;
			if ($varsAuthority == 'admin') {
				$varsTmpl['flagBtnDelete'] = 1;
				$varsTmpl['flagBtnWrite'] = 1;

			} else {
				if ($varsAuthority['flagAllDelete']) {
					$varsTmpl['flagBtnDelete'] = 1;
				}
				if ($varsAuthority['flagAllInsert']) {
					$varsTmpl['flagBtnWrite'] = 1;
				}
			}

			if ($varsTmpl['flagBtnDelete'] || $varsTmpl['flagBtnWrite']) {
				$varsTmpl['flagCheckboxUse'] = 1;
			}

			$varsTmpl['numRatio'] = $value['numRatio'];

			$varsTmpl['varsColumnDetail']['id'] = $varsTmpl['id'];
			$varsTmpl['varsColumnDetail']['strTitle'] = $value['strTitle'];
			$varsTmpl['varsColumnDetail']['stampRegister'] = $value['stampRegister'];
			$varsTmpl['varsColumnDetail']['stampUpdate'] = $value['stampUpdate'];

			$varsTmpl['varsColumnDetail']['numRatio'] = $varsTmpl['numRatio'] . '%';

			$varsTmpl['vars']['numRatio'] = $value['numRatio'];
			$varsTmpl['vars']['id'] = $varsTmpl['id'];
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
			$varsEntityNationTemp = $varsTmpl['jsonVersion'][$numVersionEnd]['jsonDetail']['varsEntityNation'];
			$varsTmpl['jsonDetail'] = $varsTmpl['jsonVersion'][$numVersionEnd];
			$varsTmpl['jsonVersion'] = array();

			foreach ($arrayDetail as $keyDetail => $valueDetail) {
				foreach ($arraySide as $keySide => $valueSide) {
					if ($valueSide == 'Debit') {
						continue;
					}
					$flagLost = 0;
					$idAccountTitle = $valueDetail['arr' . $valueSide]['idAccountTitle'];
					$idDepartment = $valueDetail['arr' . $valueSide]['idDepartment'];
					$idSubAccountTitle = $valueDetail['arr' . $valueSide]['idSubAccountTitle'];

					$strAccountTitle = $vars['varsRule']['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['strTitleFS'];
					if (!$strAccountTitle) {
						$flagLost = 1;
					}

					$strSubAccountTitle = $vars['varsRule']['arrSubAccountTitle']['arrStrTitle'][$idAccountTitle][$idSubAccountTitle]['strTitle'];
					if (!$idSubAccountTitle) {
						$idSubAccountTitle = 'none';

					} elseif (!$strSubAccountTitle) {
						$flagLost = 1;
					}

					//strDepartment
					$strDepartment = $vars['varsRule']['arrDepartment']['arrStrTitle'][$idDepartment]['strTitle'];
					if (!$idDepartment) {
						$idDepartment = 'none';

					} elseif (!$strDepartment) {
						$flagLost = 1;
					}

					if ($flagLost) {
						continue;
					}

					$numValue = $this->_getNumValueTemp(array(
						'varsFlag' => array(
							'flagFS'            => $vars['varsRule']['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['flagFS'],
							'idAccountTitle'    => $idAccountTitle,
							'idSubAccountTitle' => $idSubAccountTitle,
							'idDepartment'      => $idDepartment,
							'flagFiscalPeriod'  => 'f1',
						),
					));
					$arrayDetail[$keyDetail]['numSum'] = $numValue;

					$numValue = floor($numValue *  $value['numRatio'] / 100);

					$arrayDetail[$keyDetail]['arr' . $valueSide]['numValue'] = $numValue;
					$arrayDetail[$keyDetail]['arr' . $valueSide] = $this->_updateVarsDebit(array(
						'vars'             => $arrayDetail[$keyDetail]['arr' . $valueSide],
						'stampBook'        => $stampBook,
						'varsEntityNation' => $varsEntityNationTemp,
					));

					$arrayDetail[$keyDetail]['arrDebit']['numValue'] = $numValue;
					$arrayDetail[$keyDetail]['arrDebit'] = $this->_updateVarsDebit(array(
						'vars'             => $arrayDetail[$keyDetail]['arrDebit'],
						'stampBook'        => $stampBook,
						'varsEntityNation' => $varsEntityNationTemp,
					));


				}
			}

			$numLine++;
			$numLoop = 0;
			$flagIdLost = 0;
			foreach ($arrayDetail as $keyDetail => $valueDetail) {
				foreach ($arraySide as $keySide => $valueSide) {

					$varsTmpl['vars']['idAccountTitle' . $valueSide] = '';
					$varsTmpl['vars']['idSubAccountTitle' . $valueSide] = '';
					$varsTmpl['vars']['idDepartment' . $valueSide] = '';
					$varsTmpl['vars']['idDepartment' . $valueSide] = '';

					$varsTmpl['varsColumnDetail']['idAccountTitle' . $valueSide] = '';
					$varsTmpl['varsColumnDetail']['idSubAccountTitle' . $valueSide] = '';
					$varsTmpl['varsColumnDetail']['idDepartment' . $valueSide] = '';
					$varsTmpl['varsColumnDetail']['numValue' . $valueSide] = '';
					$varsTmpl['varsColumnDetail']['numRateConsumptionTax' . $valueSide] = '';
					$varsTmpl['varsColumnDetail']['numValueConsumptionTax' . $valueSide] = '';
					$varsTmpl['varsColumnDetail']['flagConsumptionTax' . $valueSide] = '';

					$idAccountTitle = $valueDetail['arr' . $valueSide]['idAccountTitle'];
					$idDepartment = $valueDetail['arr' . $valueSide]['idDepartment'];
					$idSubAccountTitle = $valueDetail['arr' . $valueSide]['idSubAccountTitle'];

					$numValue = $valueDetail['arr' . $valueSide]['numValue'];
					$numValueConsumptionTax = $valueDetail['arr' . $valueSide]['numValueConsumptionTax'];
					$numRateConsumptionTax = $valueDetail['arr' . $valueSide]['numRateConsumptionTax'];

					$varsTmpl['vars']['flagNumValueZero'] = 0;
					if ($numValue <= 0) {
						$varsTmpl['vars']['flagNumValueZero'] = 1;
					}

					$varsTmpl['vars']['numSum'] = $valueDetail['numSum'];
					$varsTmpl['varsColumnDetail']['numSum'] = number_format($valueDetail['numSum']);
					$varsTmpl['vars']['numValue' . $valueSide] = $numValue;
					$varsTmpl['vars']['numValueConsumptionTax' . $valueSide] = $numValueConsumptionTax;

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

						//strValue
						$varsTmpl['varsColumnDetail']['numValue' . $valueSide] = number_format($numValue);

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
							$flagRate = 0;

							//strNumValueConsumptionTax
							if ((int) $varsEntityNation['flagConsumptionTaxGeneralRule']) {
								if ((int) $varsEntityNation['flagConsumptionTaxDeducted']) {
									if (preg_match("/^tax/", $flagConsumptionTaxGeneralRuleEach)) {
										$flagTax = 1;
									}
									if (preg_match("/^tax/", $flagConsumptionTaxGeneralRuleEach)
										|| preg_match("/^else/", $flagConsumptionTaxGeneralRuleEach)
									) {
										$flagRate = 1;
									}

								} else {
									if (preg_match("/^tax/", $flagConsumptionTaxGeneralRuleProration)) {
										$flagTax = 1;
									}
									if (preg_match("/^tax/", $flagConsumptionTaxGeneralRuleProration)
										|| preg_match("/^else/", $flagConsumptionTaxGeneralRuleProration)
									) {
										$flagRate = 1;
									}
								}

							} else {
								if (preg_match("/^tax/", $flagConsumptionTaxSimpleRule)) {
									$flagTax = 1;
								}
								if (preg_match("/^tax/", $flagConsumptionTaxSimpleRule)
									|| preg_match("/^else/", $flagConsumptionTaxSimpleRule)
								) {
									$flagRate = 1;
								}
							}

							if ($numValue
								&& $flagTax
								&& $idAccountTitle != 'suspenseReceiptOfConsumptionTaxes'
								&& $idAccountTitle != 'suspensePaymentConsumptionTaxes'
								&& $flagConsumptionTaxWithoutCalc != 3
								&& !$flagConsumptionTaxIncluding
								&& $numValueConsumptionTax != ''
							) {
								if ($flagConsumptionTaxWithoutCalc == 1) {
									$varsTmpl['varsColumnDetail']['numValueConsumptionTax' . $valueSide] = '( ' . number_format($numValueConsumptionTax);

								} elseif ($flagConsumptionTaxWithoutCalc == 2) {
									$varsTmpl['varsColumnDetail']['numValueConsumptionTax' . $valueSide] = number_format($numValueConsumptionTax);
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
							if ($flagRate) {
								$varsTmpl['varsColumnDetail']['numRateConsumptionTax' . $valueSide] = $numRateConsumptionTax . '%';
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

				if ($flagIdLost || $flagPermitLost || $varsTmpl['vars']['flagNumValueZero']) {
					$varsTmpl['strClassFont'] = $vars['varsItem']['strClassLost'];
					$varsTmpl['flagBtnWrite'] = 0;
					$varsTmpl['flagCheckboxUse'] = 0;
					if ($varsTmpl['flagBtnDelete'] || $varsTmpl['flagBtnWrite']) {
						$varsTmpl['flagCheckboxUse'] = 1;
					}
					if ($varsTmpl['vars']['flagNumValueZero']) {
						$varsTmpl['strClassFont'] = $vars['varsItem']['strClassZero'];
					}
				}

				$varsTmpl['id'] .= '_' . $numLoop;
				if ($numLoop) {
					$varsTmpl['flagCheckboxUse'] = 0;
					$varsTmpl['strClassLoad'] = '';
					$varsTmpl['strClass'] = $vars['varsItem']['strClassBlank'];
					$varsTmpl['flagBtnUse'] = 0;
					$varsTmpl['flagMoveUse'] = 0;
					$varsTmpl['varsColumnDetail']['numRatio'] = '';
					$varsTmpl['varsColumnDetail']['strTitle'] = '';
					$varsTmpl['varsColumnDetail']['id'] = '';
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

			'vars'  => $vars['varsItem']['varsJsonChargeHistory'],
			'value' => $value['jsonChargeHistory'],

		))
	 */
	protected function _getJsonWriteHistoryVarsDetail($arr)
	{
		$classTime = new Code_Else_Lib_Time();
		global $varsPluginAccountingAccount;

		global $varsAccount;
		global $varsAccounts;
		global $varsPluginAccountingAccountsId;

		$array = $arr['value'];
		if (!$arr['value']) {
			$array = array();
		}
		$varsDetail = array();
		$varsData = array();
		$num = 1;
		foreach ($array as $key => $value) {
			$tmplDetail = $arr['tmplDetail'];
			$tmplDetail['id'] = $num;

			$strNo = $num;
			$tmplData = $arr['tmplData'];
			$tmplData['value'] = $strNo;
			$tmplDetail['varsDetail']['id'] = $tmplData;

			$classTime->setTimeZone(array('data' => $varsAccount['numTimeZone']));
			$strTime = $classTime->getDisplay(array(
				'stamp'    => $value['stampRegister'],
				'flagType' => 1,
			));
			$tmplData = $arr['tmplData'];
			$tmplData['value'] = $strTime;
			$tmplDetail['varsDetail']['stampRegister'] = $tmplData;

			$strCodeName = $varsAccounts[$value['idAccount']]['strCodeName'];
			if (!$varsAccounts[$value['idAccount']]['strCodeName']) {
				$strCodeName = $varsPluginAccountingAccountsId[$value['idAccount']]['strCodeName'];
			}
			$tmplData = $arr['tmplData'];
			$tmplData['value'] = $strCodeName;
			$tmplDetail['varsDetail']['idAccount'] = $tmplData;

			$tmplData = $arr['tmplData'];
			$tmplData['value'] = $value['idLog'];
			$tmplDetail['varsDetail']['idLog'] = $tmplData;


			$varsDetail[] = $tmplDetail;
			$tempVars = array();
			$tempVars['idAccount'] = $value['idAccount'];
			$tempVars['strCodeName'] = $strCodeName;
			$tempVars['idLog'] = $value['idLog'];

			$varsData[] = $tempVars;

			$num++;
		}

		$data = array(
			'varsDetail' => $varsDetail,
			'varsData'   => $varsData,
		);

		return $data;
	}

	/**
		(array(
			'varsFlag' => array(
				'flagBS'            => $valueDetail['arr' . $valueSide]['flagBS'],
				'idAccountTitle'    => $idAccountTitle,
				'idSubAccountTitle' => $idSubAccountTitle,
				'idDepartment'      => $idDepartment,
				'flagFiscalPeriod'  => 'f1',
			),
		))
	 */
	protected function _getNumValueTemp($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;

		$num = 0;

		$arrWhereTmpl = array(
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
				'strColumn'     => 'flagRemove',
				'flagCondition' => 'ne',
				'value'         => 1,
			),
		);
		$arrWhere = $arrWhereTmpl;
		$arrWhere[] = array(
			'flagType'      => '',
			'strColumn'     => 'arrCommaIdAccountTitleDebit',
			'flagCondition' => 'like',
			'value'         => ',' . $arr['varsFlag']['idAccountTitle'] . ',',
		);

		$arrLogDebitTemp = $this->_extSelf['arrLogDebitTemp'][$idAccountTitle];
		if (!$arrLogDebitTemp) {
			$rows = $classDb->getSelect(array(
				'idModule'    => 'accounting',
				'strTable'    => 'accountingLog',
				'arrJoin'     => '',
				'arrLimit'    => array(),
				'arrOrder'    => array(),
				'arrWhere'    => $arrWhere,
				'flagAnd'     => 1,
			));
			$this->_extSelf['arrLogDebitTemp'][$idAccountTitle] = $rows['arrRows'];
			$arrLogDebitTemp = $rows['arrRows'];
		}

		$sumDebit = 0;
		$array = $arrLogDebitTemp;
		$arraySide = array('Debit');
		foreach ($array as $key => $value) {
			$numVersionEnd = count($value['jsonVersion']) - 1;
			$arrayDetail = $value['jsonVersion'][$numVersionEnd]['jsonDetail']['varsDetail'];
			$varsEntityNationTemp = $value['jsonVersion'][$numVersionEnd]['jsonDetail']['varsEntityNation'];
			$varsTmpl['jsonDetail'] = $value['jsonVersion'][$numVersionEnd];

			foreach ($arrayDetail as $keyDetail => $valueDetail) {
				foreach ($arraySide as $keySide => $valueSide) {
					$idAccountTitle = $valueDetail['arr' . $valueSide]['idAccountTitle'];
					$idDepartment = $valueDetail['arr' . $valueSide]['idDepartment'];
					$idSubAccountTitle = $valueDetail['arr' . $valueSide]['idSubAccountTitle'];
					$numValue = $valueDetail['arr' . $valueSide]['numValue'];

					if ($arr['varsFlag']['idAccountTitle'] != $idAccountTitle) {
						continue;
					}

					if ($arr['varsFlag']['idDepartment'] != 'none') {
						if ($arr['varsFlag']['idDepartment'] != $idDepartment) {
							continue;
						}
					}

					if ($arr['varsFlag']['idSubAccountTitle'] != 'none') {
						if ($arr['varsFlag']['idSubAccountTitle'] != $idSubAccountTitle) {
							continue;
						}
					}
					$sumDebit += $numValue;
				}
			}
		}

		$arrWhere = $arrWhereTmpl;
		$arrWhere[] = array(
			'flagType'      => '',
			'strColumn'     => 'arrCommaIdAccountTitleCredit',
			'flagCondition' => 'like',
			'value'         => ',' . $arr['varsFlag']['idAccountTitle'] . ',',
		);

		$arrLogCreditTemp = $this->_extSelf['arrLogCreditTemp'][$idAccountTitle];
		if (!$arrLogCreditTemp) {
			$rows = $classDb->getSelect(array(
				'idModule'    => 'accounting',
				'strTable'    => 'accountingLog',
				'arrJoin'     => '',
				'arrLimit'    => array(),
				'arrOrder'    => array(),
				'arrWhere'    => $arrWhere,
				'flagAnd'     => 1,
			));
			$this->_extSelf['arrLogCreditTemp'][$idAccountTitle] = $rows['arrRows'];
			$arrLogCreditTemp = $rows['arrRows'];
		}

		$sumCredit = 0;
		$array = $arrLogCreditTemp;
		$arraySide = array('Credit');
		foreach ($array as $key => $value) {
			$numVersionEnd = count($value['jsonVersion']) - 1;
			$arrayDetail = $value['jsonVersion'][$numVersionEnd]['jsonDetail']['varsDetail'];
			$varsEntityNationTemp = $value['jsonVersion'][$numVersionEnd]['jsonDetail']['varsEntityNation'];
			$varsTmpl['jsonDetail'] = $value['jsonVersion'][$numVersionEnd];

			foreach ($arrayDetail as $keyDetail => $valueDetail) {
				foreach ($arraySide as $keySide => $valueSide) {
					$idAccountTitle = $valueDetail['arr' . $valueSide]['idAccountTitle'];
					$idDepartment = $valueDetail['arr' . $valueSide]['idDepartment'];
					$idSubAccountTitle = $valueDetail['arr' . $valueSide]['idSubAccountTitle'];
					$numValue = $valueDetail['arr' . $valueSide]['numValue'];

					if ($arr['varsFlag']['idAccountTitle'] != $idAccountTitle) {
						continue;
					}

					if ($arr['varsFlag']['idDepartment'] != 'none') {
						if ($arr['varsFlag']['idDepartment'] != $idDepartment) {
							continue;
						}
					}

					if ($arr['varsFlag']['idSubAccountTitle'] != 'none') {
						if ($arr['varsFlag']['idSubAccountTitle'] != $idSubAccountTitle) {
							continue;
						}
					}
					$sumCredit += $numValue;
				}
			}
		}

		$num = $sumDebit - $sumCredit;

		return $num;
	}

	/**
		(array(
			'vars'             => $varsDetail['arrDebit'],
			'varsEntityNation' => $varsVersion['jsonDetail']['varsEntityNation'],
		))
	 */
	protected function _updateVarsDebit($arr)
	{
		global $classTime;

		$flagTax = 0;
		$idAccountTitle = $arr['vars']['idAccountTitle'];
		$numValue = $arr['vars']['numValue'];
		$numValueConsumptionTax = '';

		$numRateConsumptionTax = $arr['vars']['numRateConsumptionTax'];

		$flagConsumptionTaxCalc = (int) $arr['vars']['flagConsumptionTaxCalc'];
		$flagConsumptionTaxWithoutCalc = (int) $arr['vars']['flagConsumptionTaxWithoutCalc'];

		if (!(int) $arr['varsEntityNation']['flagConsumptionTaxFree']
			&& !(int) $arr['varsEntityNation']['flagConsumptionTaxIncluding']
			&& $idAccountTitle != 'suspenseReceiptOfConsumptionTaxes'
			&& $idAccountTitle != 'suspensePaymentConsumptionTaxes'
			&& $numValue
		) {
			if ((int) $arr['varsEntityNation']['flagConsumptionTaxGeneralRule']) {
				if ((int) $arr['varsEntityNation']['flagConsumptionTaxDeducted']) {
					if (preg_match("/^tax/", $arr['vars']['flagConsumptionTaxGeneralRuleEach'])) {
						$flagTax = 1;
					}

				} else {
					if (preg_match("/^tax/", $arr['vars']['flagConsumptionTaxGeneralRuleProration'])) {
						$flagTax = 1;
					}
				}

			} else {
				if (preg_match("/^tax/", $arr['vars']['flagConsumptionTaxSimpleRule'])) {
					$flagTax = 1;
				}
			}
		}

		if ($flagTax) {
			$numValueConsumptionTax = 0;
			if ($flagConsumptionTaxWithoutCalc == 1) {
				$numValueConsumptionTax = $numValue *  $numRateConsumptionTax / (100 + $numRateConsumptionTax);
				if ($flagConsumptionTaxCalc == 1) {
					$numValueConsumptionTax = floor($numValueConsumptionTax);

				} elseif ($flagConsumptionTaxCalc == 2) {
					$numValueConsumptionTax = round($numValueConsumptionTax);

				} elseif ($flagConsumptionTaxCalc == 3) {
					$numValueConsumptionTax = ceil($numValueConsumptionTax);
				}

			} elseif ($flagConsumptionTaxWithoutCalc == 2) {
				//this is ok not wrong
				$numValueConsumptionTax = $numValue *  $numRateConsumptionTax / (100 + $numRateConsumptionTax);
				if ($flagConsumptionTaxCalc == 1) {
					$numValueConsumptionTax = floor($numValueConsumptionTax);

				} elseif ($flagConsumptionTaxCalc == 2) {
					$numValueConsumptionTax = round($numValueConsumptionTax);

				} elseif ($flagConsumptionTaxCalc == 3) {
					$numValueConsumptionTax = ceil($numValueConsumptionTax);
				}
				$arr['vars']['numValue'] = $numValue - $numValueConsumptionTax;

			} elseif ($flagConsumptionTaxWithoutCalc == 3) {
				$numValueConsumptionTax = '';
			}
		}

		$arr['vars']['numValueConsumptionTax'] = $numValueConsumptionTax;
		$arr['vars']['numRateConsumptionTax'] = $numRateConsumptionTax;

		return $arr['vars'];
	}

	/**
	 (array(
		'flagConsumptionTax' => $flagConsumptionTaxGeneralRuleEach,
		'vars'               => $arr['vars'],
		'stampBook'          => $arr['stampBook'],
	 ));
	 */
	protected function _getCalcRateConsumptionTax($arr)
	{
		global $classTime;

		$flagConsumptionTax = $this->_getCalcFlagConsumptionTax(array('vars' => $arr['vars'],));

		if (!(preg_match( "/^tax/", $flagConsumptionTax)
			|| preg_match( "/^else/", $flagConsumptionTax)
		)) {
			return '';
		}

		$numRate = $classTime->checkRateConsumptionTax(array('stamp' => $arr['stampBook']));
/*
 * 2014-2015 start
 */
		if ($numRate == 10) {
			$numRate = 8;
		}
/*
 * 2014-2015 end
*/
		return $numRate;
	}

	/**
	 (array(
		'vars' => $arr['vars'],
	 ));
	 */
	protected function _getCalcFlagConsumptionTax($arr)
	{
		if ($arr['vars']['flagConsumptionTaxGeneralRuleEach']) {
			return $arr['vars']['flagConsumptionTaxGeneralRuleEach'];
		}

		if ($arr['vars']['flagConsumptionTaxGeneralRuleProration']) {
			return $arr['vars']['flagConsumptionTaxGeneralRuleProration'];
		}

		if ($arr['vars']['flagConsumptionTaxSimpleRule']) {
			return $arr['vars']['flagConsumptionTaxSimpleRule'];
		}

		return '';
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
		$array = $arr['value'];
		$arrayNew = array();
		$num = 1;
		foreach ($array as $key => $value) {
			$data = array();
			$data['stampRegister'] = $value['stampRegister'];
			$data['stampUpdate'] = $value['stampUpdate'];
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
			'strColumn'   => 'jsonLogHouseNaviSearch',
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
			'strColumn' => 'jsonLogHouseNaviSearch',
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
			'strColumn' => 'jsonLogHouseNaviSearch',
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
			'strTable'   => 'accountingLogHouse' . $strNation,
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
			'strTable'   => 'accountingLogHouse' . $strNation,
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
			'strTable'  => 'accountingLogHouse' . $strNation,
			'arrOrder'  => array(),
			'flagAnd'   => 1,
			'arrWhere'  => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idLogHouse',
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
					'strTable'  => 'accountingLogHouse' . $strNation,
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
							'strColumn'     => 'idLogHouse',
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
						'strTable'  => 'accountingLogHouse' . $strNation,
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
								'strColumn'     => 'idLogHouse',
								'flagCondition' => 'eq',
								'value'         => $value,
							),
						),
					));
				}
			}

			$array = array('logHouse');
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

	/**
		(array(
			'idTarget' => '',
		))
	 */
	protected function _getVarsLog($arr)
	{
		global $classDb;
		global $varsRequest;

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
				'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			),
			array(
				'flagType'      => 'num',
				'strColumn'     => 'idLogHouse',
				'flagCondition' => 'eq',
				'value'         => $arr['idTarget'],
			),
		);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogHouse' . $strNation,
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => $arrWhere,
		));

		return $rows['arrRows'][0];
	}

	/**
	 *
	 */
	protected function _iniDetailWrite()
	{
		$this->_setClassExt(array('strClass' => 'LogHouseWrite'));
	}

	/**
	 *
	 */
	protected function _iniListWrite()
	{
		$this->_setClassExt(array('strClass' => 'LogHouseWrite'));
	}
}
