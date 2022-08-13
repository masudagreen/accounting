<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_LogImportList extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference'      => 'logWindow',
		'idCash'            => 'cashWindow',
		'pathTplJs'         => 'else/plugin/accounting/js/jpn/logImportList.js',
		'pathVarsJs'        => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/logImportList.php',
		'varsIframe'        => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/tmplIframe.php',
		'idAccountTitle'    => 'accountTitleWindow',
		'idSubAccountTitle' => 'subAccountTitleWindow',
		'idDepartment'      => 'entityDepartmentWindow',
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

		exit;
	}

	/**
	 *
	 */
	protected function _iniJs()
	{
		global $classSmarty;
		global $varsPluginAccountingAccount;

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

		$vars['portal']['varsDetail'] = $this->_updateVarsDetail((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$vars = $this->_updateVars(array(
			'vars' => $vars,
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
			'vars'     => $vars,
		))
	 */
	protected function _getVarsItem($arr)
	{
		global $varsPluginAccountingAccount;

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrSectionAccountTitle = $this->_getSectionAccountTitle(array(
			'arrSubAccountTitle' => array(),
			'numFiscalPeriod'    => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$data = array(
			'varsEntityNation'       => $varsEntityNation,
			'arrSectionAccountTitle' => $arrSectionAccountTitle,
		);

		return $data;

	}

	/**
		(array(
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'numFiscalPeriod'    => $numFiscalPeriod,
		))
	 */
	protected function _getSectionAccountTitle($arr)
	{
		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$arrayFSList = $this->_getFSList(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$strCR = $this->_getStrCR(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$arrStrTitleVarsDetail = array();
		$arrStrTitle = array();
		$arrStrTitles = array();
		$arrSelectTag = array();
		$arrSelectTags = array();

		$array = $arrayFSList;
		foreach ($array as $key => $value) {
			$str = 'jsonJgaapAccountTitle'. $key;
			$varsFS[$str] = $this->_setTreeId(array(
				'idParent' => '-',
				'vars'     => $varsFS[$str],
			));

			$varsAccountTitle = $this->_getSectionArrSelectOption(array(
				'arrStrTitle'  => array(),
				'arrSelectTag' => array(),
				'vars'         => $varsFS[$str],
				'flagBS'       => ($key == 'BS')? 1 : 0,
				'flagFS'       => $key,
				'strCR'        => $strCR,
			));

			$arrSelectTags[$key] = $varsAccountTitle['arrSelectTag'];

			$arrStrTitle = array_merge($arrStrTitle, $varsAccountTitle['arrStrTitle']);
			$arrStrTitles[$key] = $varsAccountTitle['arrStrTitle'];

			$dataStrTitleVarsDetail = array(
				'strTitle'     => $value,
				'value'        => 'dummy' . $key,
				'flagDisabled' => 1,
			);
			$arrStrTitleVarsDetail[] = $dataStrTitleVarsDetail;
			$arrStrTitleVarsDetail = array_merge($arrStrTitleVarsDetail, $varsAccountTitle['arrSelectTag']);
			$arrSelectTag = $arrStrTitleVarsDetail;
		}

		$data = array(
			'arrStrTitle'   => $arrStrTitle,
			'arrStrTitles'  => $arrStrTitles,
			'arrSelectTag'  => $arrSelectTag,
			'arrSelectTags' => $arrSelectTags,
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
	protected function _getSectionArrSelectOption($arr)
	{
		$arrSelectTag = &$arr['arrSelectTag'];
		$arrStrTitle = &$arr['arrStrTitle'];


		$array = &$arr['vars'];
		$numAll = count($array);
		$numArr = 1;
		foreach ($array as $key => $value) {
			$strTitleFS = ($arr['flagFS'] == 'CR')? '['. $arr['strCR']  .']' . $value['strTitle'] : $value['strTitle'];
			$data = array(
				'strTitle'   => $value['strTitle'],
				'strTitleFS' => $strTitleFS,
				'flagDebit'  => (int) $value['vars']['flagDebit'],
				'flagUse'    => (is_null($value['vars']['flagUse']))? '' : (int) $value['vars']['flagUse'],
				'flagFS'     => $arr['flagFS'],
			);

			$arrLevel = preg_split("/-/", $value['id']);
			$num = count($arrLevel) - 2;
			$arrLevel = array();
			for ($i = 0 ; $i < $num; $i++) {
				$arrLevel[$i] = '';
			}
			$str =  ' ' . join('.', $arrLevel) . ' ';
			$strTitle = $str . $value['strTitle'];
			$strTitleFSTag = $str .  $strTitleFS;

			if (is_null($value['vars']['flagUse'])) {
				$arr['arrSelectTag'][] = array(
					'strTitle'     => $strTitle,
					'value'        => '',
					'flagDisabled' => 1,
				);

			} else {
				if ($value['vars']['flagSortUse'] && $numAll == $numArr) {
					$arr['arrSelectTag'][] = array(
						'strTitle'     => $str . '(' . $arr['strTitleParent'] . ')',
						'value'        => $arr['idTargetParent'] . ',' . $arr['flagFS'],
					);
					$arr['arrStrTitle'][$value['vars']['idTarget']] = $data;
				}
			}
			$numArr++;

			if ($value['child']) {
				$dataTemp = $this->_getSectionArrSelectOption(array(
					'vars'            => $array[$key]['child'],
					'arrSelectTag'    => $arr['arrSelectTag'],
					'arrStrTitle'     => $arr['arrStrTitle'],
					'flagBS'          => $arr['flagBS'],
					'flagFS'          => $arr['flagFS'],
					'strCR'           => $arr['strCR'],
					'strTitleParent'  => $value['strTitle'],
					'idTargetParent'  => $value['vars']['idTarget'],
				));
				$array[$key]['child'] = $dataTemp['vars'];
				$arrSelectTag =  $dataTemp['arrSelectTag'];
				$arrStrTitle =  $dataTemp['arrStrTitle'];
			}
		}

		return $arr;
	}

	/**
		(array(
			'vars'             => $vars,
			'varsItem'         => $arr['varsItem'],
		))
	 */
	protected function _updateVarsDetail($arr)
	{
		$vars = &$arr['vars'];
		$varsItem = &$arr['varsItem'];

		$arrayNew = array();
		$array = &$vars['portal']['varsDetail']['templateDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'DummyEditCurrent') {
				if ($this->_checkCurrent()) {
					continue;
				}
			}
			$arrayNew[] = $value;
		}

		$vars['portal']['varsDetail']['templateDetail'] = $arrayNew;
		$vars['portal']['varsDetail']['varsDetail'] = $arrayNew;

		$arrayNew = array();
		$array = &$vars['portal']['varsDetail']['varsAccountTitle']['templateDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'IdAccountTitle') {
				$value['arrayOption'] = $varsItem['arrSectionAccountTitle']['arrSelectTag'];
			}
			$arrayNew[] = $value;
		}

		$vars['portal']['varsDetail']['varsAccountTitle']['templateDetail'] = $arrayNew;

		return $vars['portal']['varsDetail'];
	}

	/**
		(array(
			'vars'             => $vars,
		))
	 */
	protected function _updateVars($arr)
	{
		global $varsPluginAccountingAccount;

		if (!$this->_checkCurrent()) {
			$arr['vars']['portal']['varsDetail']['varsBtn'] = array();
			$arr['vars']['portal']['varsDetail']['varsStart']['varsEdit'] = array();
			$arr['vars']['portal']['varsDetail']['form']['varsEdit'] = array();
		}

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		if ($varsEntityNation['flagCorporation'] == 1) {
			$arr['vars']['portal']['varsDetail']['varsMake']['templateDetail'][0]['varsTmpl']['strAddAccountTitle']
				= $arr['vars']['portal']['varsDetail']['varsMake']['templateDetail'][0]['varsTmpl']['strAddAccountTitleHoujin'];
		}

		return $arr['vars'];
	}

	/**
	 *
	 */
	protected function _iniDetailAdd()
	{
		global $varsRequest;

		if (!$this->_checkCurrent()) {
			$this->_sendMessage(array('flag' => 40));
		}

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllInsert'] || $varsAuthority['flagMyInsert'])) {
			$this->_sendMessage(array('flag' => 40));
		}

		$strClass = 'LogImportList' . ucwords($varsRequest['query']['FlagData']);

		$this->_setClassExt(array('strClass' => $strClass));
		exit;
	}

	/**
	 * common
	 */

	/**
	 *
	 */
	protected function _setDetailAdd()
	{
		global $classDb;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;
		global $varsAccounts;

		global $varsRequest;
		$dbh = $classDb->getHandle();
		$classTime = new Code_Else_Lib_Time();

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$vars = $this->_updateVarsRule(array(
			'vars' => $vars,
		));

		$vars = $this->_updateVarsCheck(array(
			'vars' => $vars,
		));

		$arrValueFile = $this->_checkFileValue(array(
			'vars' => $vars,
		));

		$arrayCsv = $this->_getArrayCSV(array(
			'arrValueFile' => $arrValueFile,
			'vars'         => &$vars,
		));

		$flagCashInsert = $this->_checkAccess(array(
			'flagAllUse'    => 0,
			'flagAuthority' => 'insert',
			'idTarget'      => $this->_extSelf['idCash'],
		));
		$flagCashSelect = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'select',
			'idTarget'      => $this->_extSelf['idCash'],
		));

		$classCalcCashPay = $this->_getClassCalc(array('flagType' => 'CashPay'));
		$classCalcCash = $this->_getClassCalc(array('flagType' => 'Cash'));
		$classCalcLog = $this->_getClassCalc(array('flagType' => 'Log'));
		$classCalcSubAccountTitleImport = $this->_getClassCalc(array('flagType' => 'SubAccountTitleImport'));
		$classCalcEntityDepartmentImport = $this->_getClassCalc(array('flagType' => 'EntityDepartmentImport'));

		$data = $this->_checkVarsCSV(array(
			'arrValueFile'                    => $arrValueFile,
			'vars'                            => $vars,
			'arrayCsv'                        => $arrayCsv,
			'classCalcSubAccountTitleImport'  => $classCalcSubAccountTitleImport,
			'classCalcEntityDepartmentImport' => $classCalcEntityDepartmentImport,
		));

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		$flagSearch = 1;
		try {
			$dbh->beginTransaction();

			if ($data['arraySubAccountTitle'] || $data['arrayDepartment']) {
				if ($data['arraySubAccountTitle']) {
					$array = $data['arraySubAccountTitle'];
					foreach ($array as $key => $value) {
						$idAccountTitle = $key;
						$arrayData = $value;
						foreach ($arrayData as $keyData => $valueData) {
							$flag = $classCalcSubAccountTitleImport->allot(array(
								'flagStatus'      => 'add',
								'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
								'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
								'flagTempPrev'    => (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow))? 1 : 0,
								'arrValue'        => array(
									'strTitle'       => $keyData,
									'idAccountTitle' => $idAccountTitle,
									'arrSpaceStrTag' => '',
								),
							));
							if ($flag) {
								$this->_sendVars(array(
									'flagIframe' => 1,
									'flag'       => 'errorUnexpected',
									'stamp'      => $this->getStamp(),
									'numNews'    => $this->getNumNews(),
									'vars'       => array(),
								));
							}
						}
					}
				}

				if ($data['arrayDepartment']) {
					$array = $data['arrayDepartment'];
					foreach ($array as $key => $value) {
						$flag = $classCalcEntityDepartmentImport->allot(array(
							'flagStatus'      => 'add',
							'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
							'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
							'flagTempPrev'    => (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow))? 1 : 0,
							'arrValue'        => array(
								'strTitle'       => $value,
								'arrSpaceStrTag' => '',
							),
						));
						if ($flag) {
							$this->_sendVars(array(
								'flagIframe' => 1,
								'flag'       => 'errorUnexpected',
								'stamp'      => $this->getStamp(),
								'numNews'    => $this->getNumNews(),
								'vars'       => array(),
							));
						}
					}
				}

				$vars = $this->getVars(array(
					'path' => $this->_extSelf['pathVarsJs'],
				));

				$vars = $this->_updateVarsRule(array(
					'vars' => $vars,
				));

				$vars = $this->_updateVarsCheck(array(
					'vars' => $vars,
				));

				$classCalcSubAccountTitleImport = $this->_getClassCalc(array('flagType' => 'SubAccountTitleImport'));
				$classCalcEntityDepartmentImport = $this->_getClassCalc(array('flagType' => 'EntityDepartmentImport'));

				$data = $this->_checkVarsCSV(array(
					'arrValueFile'                    => $arrValueFile,
					'vars'                            => $vars,
					'arrayCsv'                        => $arrayCsv,
					'classCalcSubAccountTitleImport'  => $classCalcSubAccountTitleImport,
					'classCalcEntityDepartmentImport' => $classCalcEntityDepartmentImport,
				));
			}

			$arrVarsLog = array();
			$arrayLog = $data['arrayRequests'];
			foreach ($arrayLog as $keyLog => $valueLog) {
				$flagFiscalReport = $valueLog['flagFiscalReport'];
				if ($valueLog['flagFiscalReport'] == '0') {
					$flagFiscalReport = 'none';
				}
				if ($flagFiscalReport != 'none' || !$flagCashInsert) {
					$dataLog = $this->_setDbLog(array(
						'vars'     => $vars,
						'arrValue' => $valueLog,
					));
					$arrVarsLog[] = $dataLog;

				} else {
					$classTime->setTimeZone(array('data' => $varsAccounts[$valueLog['idAccount']]['numTimeZone']));
					$strTime = $classTime->getDisplay(array(
						'stamp'    => $valueLog['stampBook'],
						'flagType' => 'yearmin',
					));
					$arrOrder = array(array(
						'numFiscalPeriod'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
						'idEntity'                => $varsPluginAccountingAccount['idEntityCurrent'],
						'idAccount'               => $valueLog['idAccount'],
						'idAccountApply'          => $valueLog['idAccount'],
						'flagFiscalReport'        => $flagFiscalReport,
						'stampBook'               => $strTime,
						'strTitle'                => $valueLog['strTitle'],
						'jsonDetail'              => $valueLog['jsonDetail'],
						'arrCommaIdLogFile'       => '',
						'arrCommaIdAccountPermit' => '',
						'numSumMax'               => 0,
						'arrSpaceStrTag'          => $valueLog['arrSpaceStrTag'],
					),);
					$arrVarsDbLog = $classCalcLog->allot(array(
						'flagStatus'      => 'varsDbLog',
						'arrOrder'        => $arrOrder,
						'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
						'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					));
					$varsDbLog = end($arrVarsDbLog);

					$tempValue = array(
						'idAccount'               => $varsDbLog['idAccount'],
						'idAccountApply'          => $varsDbLog['idAccountApply'],
						'flagApply'               => $varsDbLog['flagApply'],
						'flagFiscalReport'        => $varsDbLog['flagFiscalReport'],
						'stampBook'               => $varsDbLog['stampBook'],
						'strTitle'                => $varsDbLog['strTitle'],
						'jsonDetail'              => $valueLog['jsonDetail'],
						'arrCommaIdLogFile'       => $varsDbLog['arrCommaIdLogFile'],
						'arrCommaIdAccountPermit' => $varsDbLog['arrCommaIdAccountPermit'],
						'jsonPermitHistory'       => json_decode($varsDbLog['jsonPermitHistory'], true),
						'arrSpaceStrTag'          => $varsDbLog['arrSpaceStrTag'],
					);


					$flagCashVars = $classCalcCashPay->allot(array(
						'flagStatus'      => 'check',
						'arrValue'        => $tempValue,
						'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
						'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
						'classCalcLog'    => $classCalcLog,
					));

					if ($flagCashVars['flag']) {
						if ($flagCashVars['flag'] == 'pay' || $flagCashVars['flag'] == 'paid') {
							$flagVars = $classCalcCashPay->allot(array(
								'flagStatus'      => $flagCashVars['flag'],
								'arrValue'        => $tempValue,
								'arrRowsCash'     => $flagCashVars['arrRowsCash'],
								'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
								'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
								'classCalcCash'   => $classCalcCash,
							));
							if ($flagVars == 'errorDataMax') {
								$this->_sendVars(array(
									'flagIframe' => 1,
									'flag'       => $flagVars,
									'stamp'      => $this->getStamp(),
									'numNews'    => $this->getNumNews(),
									'vars'       => array(),
								));
							}

							$valueLog['arrSpaceStrTag'] = $flagVars['arrValue']['arrSpaceStrTag'];

							$dataLog = $this->_setDbLog(array(
								'vars'     => $vars,
								'arrValue' => $valueLog,
							));
							$arrVarsLog[] = $dataLog;

							$flagVars = $classCalcCashPay->allot(array(
								'flagStatus'      => 'WriteHistory',
								'varsLog'         => $dataLog,
								'varsLogCash'     => $flagVars['arrVarsLogAdd'][0],
								'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
								'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
							));
							if ($flagVars == 'errorDataMax') {
								$this->_sendVars(array(
									'flagIframe' => 1,
									'flag'       => $flagVars,
									'stamp'      => $this->getStamp(),
									'numNews'    => $this->getNumNews(),
									'vars'       => array(),
								));
							}

						} elseif ($flagCashVars['flag'] == 'caution') {
							$flagVars = $classCalcCashPay->allot(array(
								'flagStatus'      => $flagCashVars['flag'],
								'varsLog'         => $flagCashVars['varsLog'],
								'arrRowsCash'     => $flagCashVars['arrRowsCash'],
								'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
								'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
							));
							if ($flagCashSelect) {
								$flagSearch = 'defer';
							} else {
								$flagSearch = 'deferReject';
							}
						}

					} else {
						$dataLog = $this->_setDbLog(array(
							'vars'     => $vars,
							'arrValue' => $valueLog,
						));
						$arrVarsLog[] = $dataLog;
					}
				}
			}

			$arrRows = $this->_getVarsLogCalcLoop(array(
				'arrVarsLog'      => $arrVarsLog,
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			));

			$classCalcAccountTitle = $this->_getClassCalc(array('flagType' => 'AccountTitle'));
			$classCalcSubAccountTitle = $this->_getClassCalc(array('flagType' => 'SubAccountTitle'));
			$classCalcConsumptionTax = $this->_getClassCalc(array('flagType' => 'ConsumptionTax'));
			$classCalcLogCalc = $this->_getClassCalc(array('flagType' => 'LogCalc'));
			$classCalcTempNextLog = $this->_getClassCalc(array(
				'flagType'   => 'TempNext',
				'flagDetail' => 'Log',
			));

			$flag = $classCalcAccountTitle->allot(array(
				'flagStatus'      => 'add',
				'arrRows'         => $arrRows,
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			));
			if ($flag == 'errorDataMax') {
				$this->_sendVars(array(
					'flagIframe' => 1,
					'flag'       => $flag,
					'stamp'      => $this->getStamp(),
					'numNews'    => $this->getNumNews(),
					'vars'       => array(),
				));
			}

			$flag = $classCalcSubAccountTitle->allot(array(
				'flagStatus'      => 'add',
				'arrRows'         => $arrRows,
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			));
			if ($flag == 'errorDataMax') {
				$this->_sendVars(array(
					'flagIframe' => 1,
					'flag'       => $flag,
					'stamp'      => $this->getStamp(),
					'numNews'    => $this->getNumNews(),
					'vars'       => array(),
				));
			}

			$flag = $classCalcConsumptionTax->allot(array(
				'flagStatus'      => 'add',
				'arrRows'         => $arrRows,
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			));
			if ($flag == 'errorDataMax') {
				$this->_sendVars(array(
					'flagIframe' => 1,
					'flag'       => $flag,
					'stamp'      => $this->getStamp(),
					'numNews'    => $this->getNumNews(),
					'vars'       => array(),
				));
			}

			$classCalcLogCalc->allot(array(
				'flagStatus'      => 'add',
				'arrRows'         => $arrRows,
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			));

			$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
			$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
			$numFiscalPeriod = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];

			if ((preg_match("/^(tempPrev)$/", $flagCurrentFlagNow))) {
				$flag = $classCalcTempNextLog->allot(array(
					'flagStatus'      => 'add',
					'numFiscalPeriod' => $numFiscalPeriod,
					'arrRows'         => $arrRows,
				));
				if ($flag == 'errorDataMax') {
					$this->_sendVars(array(
						'flagIframe' => 1,
						'flag'       => $flag,
						'stamp'      => $this->getStamp(),
						'numNews'    => $this->getNumNews(),
						'vars'       => array(),
					));
				}
			}
			$this->_updateDbPreferenceStamp(array('strColumn' => 'log'));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		unlink($data['strUrl']);
		$this->_sendMessage(array('flag' => $flagSearch, 'vars' => $vars['varsRule']));
	}

	/**
		(array(
			'vars' => $vars,
		))
	 */
	protected function _updateVarsRule($arr)
	{
		global $varsPluginAccountingAccount;

		$vars = $arr['vars'];

		$arrSubAccountTitle = $this->_getVarsSubAccountTitle(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrayFSList = $this->_getFSList(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrAccountTitle = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'numFiscalPeriod'    => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$vars['varsRule'] = array(
			'arrayFSList'        => $arrayFSList,
			'arrAccountTitle'    => $arrAccountTitle,
			'arrDepartment'      => $this->_getVarsDepartment(array(
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			)),
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'varsEntityNation'   => $varsEntityNation,
			'varsConsumptionTax' => $this->_getVarsConsumptionTax(array()),
			'varsFSItem'         => $this->_getVarsFSItem(),
		);

		return $vars;
	}

	/**
		(array(
			'vars' => $vars,
		))
	 */
	protected function _updateVarsCheck($arr)
	{
		global $varsAccounts;

		$vars = $arr['vars'];

		$arrAccountTitle = array();
		$array = $vars['varsRule']['arrAccountTitle']['arrStrTitle'];

		foreach ($array as $key => $value) {
			$arrAccountTitle[$value['strTitleFS']] = $key;
			if ($value['flagFS'] != 'CR') {
				$arrAccountTitle[$value['strTitle']] = $key;
			}
		}

		$arrAccountTitles = array();
		$array = $vars['varsRule']['arrAccountTitle']['arrStrTitles'];
		foreach ($array as $key => $value) {
			$arrayData = $value;
			foreach ($arrayData as $keyData => $valueData) {
				$arrAccountTitles[$key][$valueData['strTitleFS']] = $keyData;
				if ($value['flagFS'] != 'CR') {
					$arrAccountTitles[$key][$valueData['strTitle']] = $keyData;
				}
			}
		}

		$arrSubAccountTitle = array();
		$array = $vars['varsRule']['arrSubAccountTitle']['arrStrTitle'];
		foreach ($array as $key => $value) {
			$arrayChild = $value;
			foreach ($arrayChild as $keyChild => $valueChild) {
				$arrSubAccountTitle[$key][$valueChild['strTitle']] = $keyChild;
			}
		}

		$arrDepartment = array();
		$array = $vars['varsRule']['arrDepartment']['arrStrTitle'];
		foreach ($array as $key => $value) {
			$arrDepartment[$value['strTitle']] = $key;
		}

		$arrAccounts = array();
		$array = $varsAccounts;
		foreach ($array as $key => $value) {
			$arrAccounts[$value['strCodeName']] = $key;
		}

		$flagConsumptionTaxFree = (int) $vars['varsRule']['varsEntityNation']['flagConsumptionTaxFree'];
		$flagConsumptionTaxIncluding = (int) $vars['varsRule']['varsEntityNation']['flagConsumptionTaxIncluding'];
		$flagConsumptionTaxGeneralRule = (int) $vars['varsRule']['varsEntityNation']['flagConsumptionTaxGeneralRule'];
		$flagConsumptionTaxDeducted = (int) $vars['varsRule']['varsEntityNation']['flagConsumptionTaxDeducted'];

		$arrConsumptionTax = array();
		if (!$flagConsumptionTaxFree) {
			$str = 'arrStrSimple';
			if ($flagConsumptionTaxGeneralRule) {
				if ($flagConsumptionTaxDeducted) {
					$str = 'arrStrGeneralEach';
				} else {
					$str = 'arrStrGeneralProration';
				}
			}
			$array = $vars['varsRule']['varsConsumptionTax'][$str];
			foreach ($array as $key => $value) {
				$arrConsumptionTax[$value] = $key;
			}
		}

		$arrWithoutCalc = array();
		$array = $vars['varsRule']['varsConsumptionTax']['arrStrWithoutCalc'];
		foreach ($array as $key => $value) {
			$arrWithoutCalc[$value] = $key;
		}

		$arrFiscalReport = array();
		$array = $vars['varsItem']['varsFiscalReport'];
		foreach ($array as $key => $value) {
			$arrFiscalReport[$value] = $key;
		}


		$vars['varsCheck'] = array(
			'arrAccounts'        => $arrAccounts,
			'arrAccountTitle'    => $arrAccountTitle,
			'arrAccountTitles'   => $arrAccountTitles,
			'arrDepartment'      => $arrDepartment,
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'arrConsumptionTax'  => $arrConsumptionTax,
			'arrWithoutCalc'     => $arrWithoutCalc,
			'arrFiscalReport'    => $arrFiscalReport,
		);

		return $vars;
	}

	/**
			'arrValueFile'                   => $arrValueFile,
			'vars'                           => $vars,
			'classCalcSubAccountTitleImport' => $classCalcSubAccountTitleImport,
			'classCalcDepartment'            => $classCalcDepartment,
	 */
	protected function _getArrayCSV($arr)
	{
		global $classFile;
		global $classEscape;

		$arrValueFile = $arr['arrValueFile'];

		$arrayCSV = $classFile->getArray(array(
			'path' => $arrValueFile['strUrl'],
		));

		$array = $arrayCSV;
		foreach ($array as $key => $value) {
			$arrayCSV[$key] = $classEscape->to(array('data' => $arrayCSV[$key]));
		}

		$array = $arr['vars']['varsItem']['varsId'];
		foreach ($array as $key => $value) {
			$arrayCSV[0] = str_replace("$value", $key, $arrayCSV[0]);
		}

		file_put_contents($arrValueFile['strUrl'], $arrayCSV);

		$arrayCSV = $classFile->getCsvRows(array(
			'path' => $arrValueFile['strUrl'],
		));

		return $arrayCSV;
	}

	/**
			'arrValueFile'                    => $arrValueFile,
			'vars'                            => $vars,
			'arrayCsv'                        => $arrayCsv,
			'classCalcSubAccountTitleImport'  => $classCalcSubAccountTitleImport,
			'classCalcEntityDepartmentImport' => $classCalcEntityDepartmentImport,
	 */
	protected function _checkVarsCSV($arr)
	{
		$arrayLog = array();
		$array = $arr['arrayCsv'];
		$arrValueFile = $arr['arrValueFile'];

		//multi rows
		foreach ($array as $key => $value) {
			$arrayLog[$value['id']][] = $value;
		}

		$this->_checkDataJson(array(
			'vars'      => $arr['vars'],
			'arrayLog'  => $arrayLog,
			'strUrl'    => $arrValueFile['strUrl'],
		));

		$flagAuthority = $this->_checkAuthority(array(
			'idTarget' => $this->_extSelf['idAccountTitle'],
		));
		$this->_checkIdAccountTitleAuto(array(
			'vars'          => $arr['vars'],
			'arrayLog'      => $arrayLog,
			'strUrl'        => $arrValueFile['strUrl'],
			'flagAuthority' => $flagAuthority,
		));

		//subAccountTitle
		$flagAuthority = $this->_checkAuthority(array(
			'idTarget' => $this->_extSelf['idSubAccountTitle'],
		));
		$arraySubAccountTitle = $this->_checkIdSubAccountTitleAuto(array(
			'vars'          => $arr['vars'],
			'arrayLog'      => $arrayLog,
			'strUrl'        => $arrValueFile['strUrl'],
			'flagAuthority' => $flagAuthority,
			'classCalcSubAccountTitleImport' => &$arr['classCalcSubAccountTitleImport'],
		));

		//department
		$flagAuthority = $this->_checkAuthority(array(
			'idTarget' => $this->_extSelf['idDepartment'],
		));
		$arrayDepartment = $this->_checkIdDepartmentAuto(array(
			'vars'          => $arr['vars'],
			'arrayLog'      => $arrayLog,
			'strUrl'        => $arrValueFile['strUrl'],
			'flagAuthority' => $flagAuthority,
			'classCalcEntityDepartmentImport' => &$arr['classCalcEntityDepartmentImport'],
		));

		if ($arraySubAccountTitle || $arrayDepartment) {
			$data = array(
				'strUrl'               => $arrValueFile['strUrl'],
				'arrayRequests'        => array(),
				'arraySubAccountTitle' => $arraySubAccountTitle,
				'arrayDepartment'      => $arrayDepartment,
			);

			return $data;
		}

		$arrayRequests = $this->_checkVarsCSVFormat(array(
			'vars'      => $arr['vars'],
			'arrayLog'  => $arrayLog,
			'strUrl'    => $arrValueFile['strUrl'],
		));

		$data = array(
			'strUrl'               => $arrValueFile['strUrl'],
			'arrayRequests'        => $arrayRequests,
			'arraySubAccountTitle' => array(),
			'arrayDepartment'      => array(),
		);

		return $data;
	}

	/**
		(array(
			'vars'      => $arr['vars'],
			'arrayLog'  => $arrayLog,
			'strUrl'    => $arrValueFile['strUrl'],
		))
	 */
	protected function _checkDataJson($arr)
	{
		$varsComment = $arr['vars']['varsItem']['varsComment'];

		$arrayLog = $arr['arrayLog'];

		$json = json_encode($arrayLog);
		if (preg_match("/null/", $json)) {
			$this->_sendError(array('comment' => $varsComment['strConvert'], 'strUrl' => $arr['strUrl'],));
		}
	}

	/**
		(array(

		))
	 */
	protected function _checkAuthority($arr)
	{
		$flagSelect = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'select',
			'idTarget'      => $arr['idTarget'],
		));

		$flagInsert = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'insert',
			'idTarget'      => $arr['idTarget'],
		));

		if ($flagSelect && $flagInsert) {
			return 1;
		}
	}


	/**
		(array(
			'vars'      => $arr['vars'],
			'arrayLog'  => $arrayLog,
		))
	 */
	protected function _checkIdAccountTitleAuto($arr)
	{
		global $classEscape;

		$arrayNew = array();
		$arrayLog = $arr['arrayLog'];
		foreach ($arrayLog as $keyLog => $valueLog) {
			$array = &$arrayLog[$keyLog];
			foreach ($array as $key => $value) {
				$arrayStr = array('Debit', 'Credit');
				foreach ($arrayStr as $keyStr => $valueStr) {
					if ($value['idAccountTitle' . $valueStr] == '') {
						continue;
					}
					$flagIdTarget = $this->_checkIdAccountTitle(array(
						'varsRule' => $arr['vars']['varsRule'],
						'idTarget' => $value['idAccountTitle' . $valueStr],
					));
					if (!$flagIdTarget) {
						$flagFS = ($value['flagFS' . $valueStr])? $value['flagFS' . $valueStr] : '';
						if ($flagFS) {
							$flagIdTarget = $arr['vars']['varsCheck']['arrAccountTitles'][$flagFS][$value['idAccountTitle' . $valueStr]];

						} else {
							$flagIdTarget = $arr['vars']['varsCheck']['arrAccountTitle'][$value['idAccountTitle' . $valueStr]];
						}

						if (!$flagIdTarget) {
							$temp = $classEscape->toComma(array(
								'data' => $value['idAccountTitle' . $valueStr]
							));
							$temp = mb_substr($temp, 0, 100);
							$arrayNew[] = $temp;
						}
					}
				}
			}
		}
		if ($arrayNew) {
			$arrayNew = array_unique($arrayNew);
			if (!$arr['flagAuthority']) {
				$arrayNew = '';
			}
			unlink($arr['strUrl']);

			$arrayTemp = array();
			$array = $arrayNew;
			foreach ($array as $key => $value) {
				$arrayTemp[] = $value;
			}

			$this->_sendVars(array(
				'flagIframe' => 1,
				'flag'       => 'strAccountTitle',
				'stamp'      => $this->getStamp(),
				'numNews'    => $this->getNumNews(),
				'vars'       => $arrayTemp,
			));
		}
	}

	/**
		(array(
			'vars'          => $arr['vars'],
			'arrayLog'      => $arrayLog,
			'strUrl'        => $arrValueFile['strUrl'],
			'flagAuthority' => $flagAuthority,
			'classCalcSubAccountTitleImport' => &$arr['classCalcSubAccountTitleImport'],
		))
	 */
	protected function _checkIdSubAccountTitleAuto($arr)
	{
		global $classEscape;

		$classCalcSubAccountTitleImport = &$arr['classCalcSubAccountTitleImport'];
		global $varsPluginAccountingAccount;

		$arrayNew = array();
		$arrayLog = $arr['arrayLog'];
		foreach ($arrayLog as $keyLog => $valueLog) {
			$array = &$arrayLog[$keyLog];
			foreach ($array as $key => $value) {
				$arrayStr = array('Debit', 'Credit');
				foreach ($arrayStr as $keyStr => $valueStr) {
					if ($value['idAccountTitle' . $valueStr] == '') {
						continue;
					}
					$flagIdTarget = $this->_checkIdAccountTitle(array(
						'varsRule' => $arr['vars']['varsRule'],
						'idTarget' => $value['idAccountTitle' . $valueStr],
					));
					if (!$flagIdTarget) {
						$flagFS = ($value['flagFS' . $valueStr])? $value['flagFS' . $valueStr] : '';
						if ($flagFS) {
							$flagIdTarget = $arr['vars']['varsCheck']['arrAccountTitles'][$flagFS][$value['idAccountTitle' . $valueStr]];

						} else {
							$flagIdTarget = $arr['vars']['varsCheck']['arrAccountTitle'][$value['idAccountTitle' . $valueStr]];
						}
					}
					$idAccountTitle = $flagIdTarget;
					if ($value['idSubAccountTitle' . $valueStr] != '') {
						$idSubAccountTitle = $arr['vars']['varsCheck']['arrSubAccountTitle'][$idAccountTitle][$value['idSubAccountTitle' . $valueStr]];
						if (!$idSubAccountTitle) {
							$temp = $classEscape->toComma(array(
								'data' => $value['idSubAccountTitle' . $valueStr]
							));
							$temp = mb_substr($temp, 0, 100);
							$arrayNew[$idAccountTitle][$temp] = 1;
						}
					}
				}
			}
		}
		if ($arrayNew) {
			$arrayCheck = array();
			$array = $arrayNew;
			foreach ($array as $key => $value) {
				$idAccountTitle = $key;
				$arrayData = $value;
				foreach ($arrayData as $keyData => $valueData) {
					$flag = $classCalcSubAccountTitleImport->allot(array(
						'flagStatus'      => 'check',
						'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
						'strTitle'        => $keyData,
						'idAccountTitle'  => $idAccountTitle,
					));
					if ($flag) {
						$arrayCheck[] = $keyData;
					}
				}
			}
			if (!$arr['flagAuthority']) {
				$arrayCheck = '';
				unlink($arr['strUrl']);
				$this->_sendVars(array(
					'flagIframe' => 1,
					'flag'       => 'strSubAccountTitle',
					'stamp'      => $this->getStamp(),
					'numNews'    => $this->getNumNews(),
					'vars'       => $arrayCheck,
				));

			} else {
				if ($arrayCheck) {
					unlink($arr['strUrl']);
					$this->_sendVars(array(
						'flagIframe' => 1,
						'flag'       => 'strTitleSubAccountTitle',
						'stamp'      => $this->getStamp(),
						'numNews'    => $this->getNumNews(),
						'vars'       => $arrayCheck,
					));
				}
			}

			return $arrayNew;
		}

		return array();
	}

	/**
		(array(
			'vars'          => $arr['vars'],
			'arrayLog'      => $arrayLog,
			'strUrl'        => $arrValueFile['strUrl'],
			'flagAuthority' => $flagAuthority,
			'classCalcSubAccountTitleImport' => &$arr['classCalcSubAccountTitleImport'],
		))
	 */
	protected function _checkIdDepartmentAuto($arr)
	{
		global $classEscape;

		$classCalcEntityDepartmentImport = &$arr['classCalcEntityDepartmentImport'];

		global $varsPluginAccountingAccount;

		$arrayNew = array();
		$arrayLog = $arr['arrayLog'];
		foreach ($arrayLog as $keyLog => $valueLog) {
			$array = &$arrayLog[$keyLog];
			foreach ($array as $key => $value) {
				$arrayStr = array('Debit', 'Credit');
				foreach ($arrayStr as $keyStr => $valueStr) {
					if ($value['idAccountTitle' . $valueStr] == '') {
						continue;
					}
					if ($value['idDepartment' . $valueStr] != '') {
						$idDepartment = $arr['vars']['varsCheck']['arrDepartment'][$value['idDepartment' . $valueStr]];
						if (!$idDepartment) {
							$temp = $classEscape->toComma(array(
								'data' => $value['idDepartment' . $valueStr]
							));
							$temp = mb_substr($temp, 0, 100);
							$arrayNew[] = $temp;
						}
					}
				}
			}
		}

		if ($arrayNew) {
			$arrayNew = array_unique($arrayNew);
			$arrayCheck = array();
			$array = $arrayNew;
			foreach ($array as $key => $value) {
				$flag = $classCalcEntityDepartmentImport->allot(array(
					'flagStatus' => 'check',
					'idEntity'   => $varsPluginAccountingAccount['idEntityCurrent'],
					'strTitle'   => $value,
				));
				if ($flag) {
					$arrayCheck[] = $value;
				}
			}

			if (!$arr['flagAuthority']) {
				$arrayCheck = '';
				unlink($arr['strUrl']);
				$this->_sendVars(array(
					'flagIframe' => 1,
					'flag'       => 'strDepartment',
					'stamp'      => $this->getStamp(),
					'numNews'    => $this->getNumNews(),
					'vars'       => $arrayCheck,
				));

			} else {
				if ($arrayCheck) {
					unlink($arr['strUrl']);
					$this->_sendVars(array(
						'flagIframe' => 1,
						'flag'       => 'strTitleDepartment',
						'stamp'      => $this->getStamp(),
						'numNews'    => $this->getNumNews(),
						'vars'       => $arrayCheck,
					));
				}
			}

			return $arrayNew;
		}

		return array();
	}

	/**
		(array(
			'vars'      => $arr['vars'],
			'arrayLog'  => $arrayLog,
			'strUrl'    => $arrValueFile['strUrl'],
		))
	 */
	protected function _checkVarsCSVFormat($arr)
	{
		global $classCheck;
		global $classEscape;
		global $classTime;

		global $varsPluginAccountingAccounts;
		global $varsPluginAccountingAccount;


		$varsId = $arr['vars']['varsItem']['varsId'];
		$varsComment = $arr['vars']['varsItem']['varsComment'];

		$varsCheck = $arr['vars']['varsCheck'];
		$varsRule = $arr['vars']['varsRule'];

		$varsEntityNation = $arr['vars']['varsRule']['varsEntityNation'];
		$flagConsumptionTaxFree = (int) $arr['vars']['varsRule']['varsEntityNation']['flagConsumptionTaxFree'];
		$flagConsumptionTaxIncluding = (int) $arr['vars']['varsRule']['varsEntityNation']['flagConsumptionTaxIncluding'];
		$flagConsumptionTaxGeneralRule = (int) $arr['vars']['varsRule']['varsEntityNation']['flagConsumptionTaxGeneralRule'];
		$flagConsumptionTaxDeducted = (int) $arr['vars']['varsRule']['varsEntityNation']['flagConsumptionTaxDeducted'];
		$flagConsumptionTaxCalc = (int) $arr['vars']['varsRule']['varsEntityNation']['flagConsumptionTaxCalc'];

		$flagConsumptionTaxWithoutCalcDefault = (int) $arr['vars']['varsRule']['varsEntityNation']['flagConsumptionTaxWithoutCalc'];

		$varsStamp = $this->_getNumFiscalTermStamp(array(
			'varsEntityNation' => $arr['vars']['varsRule']['varsEntityNation']
		));

		$arrayRequests = array();
		$arrayLog = $arr['arrayLog'];
		foreach ($arrayLog as $keyLog => $valueLog) {

			$strStatus = $varsComment['strStatus'];
			$strStatus = str_replace("<%replace%>", $keyLog, $strStatus);

			if (count($valueLog) > 10) {
				$this->_sendError(array('comment' => $strStatus . $varsComment['strRowMax'], 'strUrl' => $arr['strUrl'],));
			}

			$varsRequest = $arr['varsItem']['varsFSItem']['varsJournalRequest']['varsRequest'];

			$array = &$arrayLog[$keyLog];
			$flagFirst = 1;
			$numSumDebit = 0;
			$numSumCredit = 0;
			$nomRows = 0;
			foreach ($array as $key => $value) {

				$id = 'id';
				if ($value[$id] == '') {
					$this->_sendError(array('comment' => $varsComment['strIdBlank'], 'strUrl' => $arr['strUrl'],));
				}
				$varsRequest[$id] = $value[$id];

				$flag = $classCheck->checkValueWord(array(
					'flagType' => 'num',
					'value'    => $value[$id]
				));
				if ($flag) {
					$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strFormat'], 'strUrl' => $arr['strUrl'],));
				}

				$varsDetail = $arr['varsItem']['varsFSItem']['varsJournalRequest']['varsDetail'];

				if ($flagFirst) {
					$id = 'stampBook';
					if ($value[$id] == '') {
						$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strMust'], 'strUrl' => $arr['strUrl'],));
					}

					$id = 'flagFiscalReport';
					if (!($varsCheck['arrFiscalReport'][$value[$id]] || $value[$id] == '')) {
						$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strFormat'], 'strUrl' => $arr['strUrl'],));
					}
					if ($varsCheck['arrFiscalReport'][$value[$id]] == 'f1') {
						$varsRequest[$id] = 'f1';

					} elseif ($varsCheck['arrFiscalReport'][$value[$id]] == 'f21') {
						$varsRequest[$id] = 'f21';
						if ((int) $arr['vars']['varsRule']['varsEntityNation']['numFiscalTermMonth'] < 12
							|| $arr['vars']['varsRule']['varsEntityNation']['flagCorporation'] != 1
						) {
							$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strTime2'], 'strUrl' => $arr['strUrl'],));
						}

					} else {
						$varsRequest[$id] = '0';
					}

					$stampBook = $this->_getStampBook(array(
						'flagFiscalReport' => $varsRequest[$id],
						'strBook'          => $value['stampBook'],
						'varsStamp'        => $varsStamp,
					));
					if (!$stampBook) {
						$this->_sendError(array('comment' => $strStatus . $varsId['stampBook'] . $varsComment['strFormat'], 'strUrl' => $arr['strUrl'],));
					}

					if (!($varsStamp['stampMin'] <= $stampBook && $stampBook <= $varsStamp['stampMax'])) {
						$this->_sendError(array('comment' => $strStatus . $varsId['stampBook'] . $varsComment['strTime'], 'strUrl' => $arr['strUrl'],));
					}
					$varsRequest['stampBook'] = $stampBook;

					$numRate = $classTime->checkRateConsumptionTax(array('stamp' => $stampBook));

					$varsRequest['strTitle'] = mb_substr($value['strTitle'], 0, 100);

					$id = 'idAccount';
					if ($value[$id] == '') {
						$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strMust'], 'strUrl' => $arr['strUrl'],));
					}
					if (!$varsCheck['arrAccounts'][$value[$id]]) {
						$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strNone'], 'strUrl' => $arr['strUrl'],));
					}
					$varsRequest[$id] = $varsCheck['arrAccounts'][$value[$id]];

					$varsAuthority = $this->_getVarsAuthority(array('idAccount' => $varsRequest[$id]));
					if (!($varsAuthority == 'admin' || $varsAuthority['flagAllInsert'] || $varsAuthority['flagMyInsert'])) {
						$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strAuthority'], 'strUrl' => $arr['strUrl'],));
					}

					$str = ',' . $varsPluginAccountingAccount['idEntityCurrent'] . ',';
					$arrCommaIdEntity = $varsPluginAccountingAccounts[$varsRequest[$id]]['arrCommaIdEntity'];

					if ($varsAuthority != 'admin') {
						if (!preg_match("/$str/", $arrCommaIdEntity)) {
							$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strAuthorityEntity'], 'strUrl' => $arr['strUrl'],));
						}
					}

					$id = 'arrSpaceStrTag';
					if ($value[$id] != '') {
						$flagStrMax = $classCheck->checkValueMax(array(
							'flagType' => 'str',
							'num'      => 1000,
							'value'    => $value[$id],
						));
						if ($flagStrMax) {
							$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strStrMax'], 'strUrl' => $arr['strUrl'],));
						}
					}

					$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $value[$id]));
					$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));
					$varsRequest[$id] = $arrSpaceStrTag;

					$arrayStr = array('Debit', 'Credit');
					foreach ($arrayStr as $keyStr => $valueStr) {
						$flagTax = 0;

						$id = 'numValueConsumptionTax' . $valueStr;
						if ($value[$id] == '') {
							$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strMust'], 'strUrl' => $arr['strUrl'],));
						}

						$flag = $classCheck->checkValueWord(array(
							'flagType' => 'num',
							'value'    => $value[$id]
						));
						if ($flag) {
							$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strFormat'], 'strUrl' => $arr['strUrl'],));
						}

						$numValueConsumptionTax =  $value[$id];
						$varsDetail['arr' . $valueStr]['numValueConsumptionTax'] = (!$numValueConsumptionTax)? '': $numValueConsumptionTax;

						$id = 'numValue' . $valueStr;
						if ($value[$id] == '') {
							$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strMust'], 'strUrl' => $arr['strUrl'],));
						}
						$flag = $classCheck->checkValueWord(array(
							'flagType' => 'num',
							'value'    => $value[$id]
						));
						if ($flag) {
							$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strFormat'], 'strUrl' => $arr['strUrl'],));
						}
						$numValue =  $value[$id];

						$id = 'idAccountTitle' . $valueStr;
						if ($value[$id] == '') {
							$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strMust'], 'strUrl' => $arr['strUrl'],));
						}

						$flagIdTarget = $this->_checkIdAccountTitle(array(
							'varsRule' => $varsRule,
							'idTarget' => $value[$id],
						));
						if (!$flagIdTarget) {
							$flagFS = ($value['flagFS' . $valueStr])? $value['flagFS' . $valueStr] : '';
							if ($flagFS) {
								$flagIdTarget = $varsCheck['arrAccountTitles'][$flagFS][$value[$id]];

							} else {
								$flagIdTarget = $varsCheck['arrAccountTitle'][$value[$id]];
							}

							if (!$flagIdTarget) {
								$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strNone'], 'strUrl' => $arr['strUrl'],));
							}
						}
						$idAccountTitle = $flagIdTarget;
						$varsDetail['arr' . $valueStr]['idAccountTitle'] = $idAccountTitle;

						if ($numValue <= 0) {
							$this->_sendError(array('comment' => $strStatus . $varsId['numValue' . $valueStr] . $varsComment['strNumValue'], 'strUrl' => $arr['strUrl'],));
						}

						$id = 'idSubAccountTitle' . $valueStr;
						if ($value[$id] != '') {
							$idSubAccountTitle = $varsCheck['arrSubAccountTitle'][$idAccountTitle][$value[$id]];
							if (!$idSubAccountTitle) {
								$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strNone'], 'strUrl' => $arr['strUrl'],));
							}
							$varsDetail['arr' . $valueStr]['idSubAccountTitle'] = $idSubAccountTitle;
						}

						$id = 'idDepartment' . $valueStr;
						if ($value[$id] != '') {
							if (!$varsCheck['arrDepartment'][$value[$id]]) {
								$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strNone'], 'strUrl' => $arr['strUrl'],));
							}
							$idDepartment = $varsCheck['arrDepartment'][$value[$id]];
							$varsDetail['arr' . $valueStr]['idDepartment'] = $idDepartment;
						}

						$flagConsumptionTax = '';
						$id = 'flagConsumptionTax' . $valueStr;
						if ($value[$id] != '') {
							if (!$varsCheck['arrConsumptionTax'][$value[$id]]) {
								$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strNone'], 'strUrl' => $arr['strUrl'],));
							}
							$flagConsumptionTax = $varsCheck['arrConsumptionTax'][$value[$id]];
						}

						$flagConsumptionTaxWithoutCalc = $flagConsumptionTaxWithoutCalcDefault;
						$id = 'flagConsumptionTaxWithoutCalc' . $valueStr;
						if ($value[$id] != '') {
							if (!$varsCheck['arrWithoutCalc'][$value[$id]]) {
								$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strNone'], 'strUrl' => $arr['strUrl'],));
							}
							$flagConsumptionTaxWithoutCalc = $varsCheck['arrWithoutCalc'][$value[$id]];
						}

						$varsDetail['arr' . $valueStr]['flagConsumptionTaxCalc'] = $flagConsumptionTaxCalc;
						if ($flagConsumptionTaxFree) {
							$varsDetail['arr' . $valueStr]['flagConsumptionTaxFree'] = 1;
							$varsDetail['arr' . $valueStr]['flagConsumptionTaxIncluding'] = 1;
							$varsDetail['arr' . $valueStr]['flagConsumptionTaxGeneralRuleEach'] = '';
							$varsDetail['arr' . $valueStr]['flagConsumptionTaxGeneralRuleProration'] = '';
							$varsDetail['arr' . $valueStr]['flagConsumptionTaxSimpleRule'] = '';
							$varsDetail['arr' . $valueStr]['flagConsumptionTaxWithoutCalc'] = 1;

						} else {
							$varsDetail['arr' . $valueStr]['flagConsumptionTaxFree'] = 0;
							if ($flagConsumptionTaxGeneralRule) {
								if ($flagConsumptionTaxDeducted) {
									$varsDetail['arr' . $valueStr]['flagConsumptionTaxGeneralRuleEach'] = $flagConsumptionTax;
								} else {
									$varsDetail['arr' . $valueStr]['flagConsumptionTaxGeneralRuleProration'] = $flagConsumptionTax;
								}

							} else {
								$varsDetail['arr' . $valueStr]['flagConsumptionTaxSimpleRule'] = $flagConsumptionTax;
							}
							if ($flagConsumptionTaxIncluding) {
								$varsDetail['arr' . $valueStr]['flagConsumptionTaxIncluding'] = 1;
								$varsDetail['arr' . $valueStr]['flagConsumptionTaxWithoutCalc'] = 1;

							} else {
								$varsDetail['arr' . $valueStr]['flagConsumptionTaxIncluding'] = 0;
								$varsDetail['arr' . $valueStr]['flagConsumptionTaxWithoutCalc'] = $flagConsumptionTaxWithoutCalc;
							}

							if (!$flagConsumptionTaxIncluding && preg_match( "/^tax/", $flagConsumptionTax)) {
								$flagTax = 1;
							}

							if ($idAccountTitle == 'suspenseReceiptOfConsumptionTaxes' || $idAccountTitle == 'suspensePaymentConsumptionTaxes') {
								$flagTax = 0;
							}
						}

						$id = 'numRateConsumptionTax' . $valueStr;
						/*
						 * 20191001 start
						 */
						$varsDetail['arr' . $valueStr]['flagRateConsumptionTaxReduced'] = '';
						if ($value[$id] != '') {

						    $varsDetail['arr' . $valueStr]['flagRateConsumptionTaxReduced'] = 0;
							if (!preg_match("/^(5|8|10)$/", $value[$id])) {
							//if (!preg_match("/^(5|8)$/", $value[$id])) {

                                $strRateConsumptionTaxReduced = 8 . $arr['vars']['varsItem']['strRateConsumptionTaxReduced'];
                                if ($value[$id] != $strRateConsumptionTaxReduced) {
							        $this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strTaxRate'] . __LINE__, 'strUrl' => $arr['strUrl'],));
							    }
							    $value[$id] = 8;
							    $varsDetail['arr' . $valueStr]['flagRateConsumptionTaxReduced'] = 1;
    						}

							/*
							 * 20191001 end
							 */
						}

						if ($flagConsumptionTaxFree) {
							$varsDetail['arr' . $valueStr]['numRateConsumptionTax'] = '';

						} else {
							if (preg_match("/^tax/", $flagConsumptionTax)
								|| preg_match("/^else/", $flagConsumptionTax)
							) {
							    if ($numRate == 8) {
									if ($value[$id] == 10
    									/*
    									 * 20191001 start
    									 */
									    || $varsDetail['arr' . $valueStr]['flagRateConsumptionTaxReduced']
									    /*
									     * 20191001 end
									     */
									    ) {
										$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strTaxRatePre'], 'strUrl' => $arr['strUrl'],));
									}

								} elseif ($numRate == 5) {
									if ($value[$id] == 8
										|| $value[$id] == 10
										/*
										 * 20191001 start
										 */
									    || $varsDetail['arr' . $valueStr]['flagRateConsumptionTaxReduced']
									    /*
									     * 20191001 end
									     */
									) {
										$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strTaxRatePre'], 'strUrl' => $arr['strUrl'],));
									}

								}
								if ($value[$id] == '') {
									$value[$id] = $numRate;
								}
								$varsDetail['arr' . $valueStr]['numRateConsumptionTax'] = $value[$id];

							} else {
								$varsDetail['arr' . $valueStr]['numRateConsumptionTax'] = '';
							}
						}

						if ($valueStr == 'Debit') {
							$numSumDebit += $numValue;
						} else {
							$numSumCredit += $numValue;
						}

						if ($flagTax) {
							if ($flagConsumptionTaxWithoutCalc == 2) {
								$numValue -= $numValueConsumptionTax;
							}
						}
						if ($numValue < 0) {
							$this->_sendError(array('comment' => $strStatus . $varsId['numValueConsumptionTax' . $valueStr] . $varsComment['strTax'], 'strUrl' => $arr['strUrl'],));
						}
						$varsDetail['arr' . $valueStr]['numValue'] = $numValue;


						if ($flagTax) {
							if ($flagConsumptionTaxWithoutCalc != 3) {

							} else {
								if ($numValueConsumptionTax != 0) {
									$this->_sendError(array('comment' => $strStatus . $varsId['numValueConsumptionTax' . $valueStr] . $varsComment['strTaxOut'], 'strUrl' => $arr['strUrl'],));
								}
							}
						}
						/*
						 * free-MonetaryClaim is H26.4.1~
						* */
						if ($flagConsumptionTax == 'free-MonetaryClaim') {
							if ($numRate == 5) {
								$flag = 'free-MonetaryClaim';
								$this->_sendError(array('comment' => $strStatus . $varsId['flagConsumptionTax' . $valueStr] . $varsComment['strMonetaryClaim'], 'strUrl' => $arr['strUrl'],));
							}
						}
					}

					$varsRequest['jsonDetail']['varsDetail'][] = $varsDetail;
					$varsRequest['jsonDetail']['idAccountTitleDebit'] = $varsDetail['arrDebit']['idAccountTitle'];
					$varsRequest['jsonDetail']['idAccountTitleCredit'] = $varsDetail['arrCredit']['idAccountTitle'];
					$varsRequest['jsonDetail']['numSum'] = $numSumDebit;
					$varsRequest['jsonDetail']['numSumDebit'] = $numSumDebit;
					$varsRequest['jsonDetail']['numSumCredit'] = $numSumCredit;
					$varsRequest['jsonDetail']['varsEntityNation'] = array(
						'flagConsumptionTaxFree'         => $varsEntityNation['flagConsumptionTaxFree'],
						'flagConsumptionTaxGeneralRule'  => $varsEntityNation['flagConsumptionTaxGeneralRule'],
						'flagConsumptionTaxDeducted'     => $varsEntityNation['flagConsumptionTaxDeducted'],
						'flagConsumptionTaxIncluding'    => $varsEntityNation['flagConsumptionTaxIncluding'],
						/* journal.js insert
						'flagConsumptionTaxCalc'         => $varsEntityNation['flagConsumptionTaxCalc'],
						'flagConsumptionTaxWithoutCalc'  => $varsEntityNation['flagConsumptionTaxWithoutCalc'],
						'flagConsumptionTaxBusinessType' => $varsEntityNation['flagConsumptionTaxBusinessType'],
						*/
					);
					$flagFirst = 0;
					continue;
				}

				$numRate = $classTime->checkRateConsumptionTax(array('stamp' => $varsRequest['stampBook']));
				$arrayStr = array('Debit', 'Credit');
				foreach ($arrayStr as $keyStr => $valueStr) {
					$flagTax = 0;

					$id = 'numValueConsumptionTax' . $valueStr;
					if ($value[$id] == '') {
						$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strMust'], 'strUrl' => $arr['strUrl'],));
					}

					$flag = $classCheck->checkValueWord(array(
						'flagType' => 'num',
						'value'    => $value[$id]
					));
					if ($flag) {
						$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strFormat'], 'strUrl' => $arr['strUrl'],));
					}

					$numValueConsumptionTax =  $value[$id];
					$varsDetail['arr' . $valueStr]['numValueConsumptionTax'] = (!$numValueConsumptionTax)? '': $numValueConsumptionTax;

					$id = 'numValue' . $valueStr;
					if ($value[$id] == '') {
						$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strMust'], 'strUrl' => $arr['strUrl'],));
					}
					$flag = $classCheck->checkValueWord(array(
						'flagType' => 'num',
						'value'    => $value[$id]
					));
					if ($flag) {
						$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strFormat'], 'strUrl' => $arr['strUrl'],));
					}
					$numValue = $value[$id];

					$id = 'idAccountTitle' . $valueStr;
					if ($value[$id] != '') {

						$flagIdTarget = $this->_checkIdAccountTitle(array(
							'varsRule' => $varsRule,
							'idTarget' => $value[$id],
						));
						if (!$flagIdTarget) {
							$flagFS = ($value['flagFS' . $valueStr])? $value['flagFS' . $valueStr] : '';
							if ($flagFS) {
								$flagIdTarget = $varsCheck['arrAccountTitles'][$flagFS][$value[$id]];

							} else {
								$flagIdTarget = $varsCheck['arrAccountTitle'][$value[$id]];
							}

							if (!$flagIdTarget) {
								$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strNone'], 'strUrl' => $arr['strUrl'],));
							}
						}
						$idAccountTitle = $flagIdTarget;
						$varsDetail['arr' . $valueStr]['idAccountTitle'] = $idAccountTitle;

						if ($numValue <= 0) {
							$this->_sendError(array('comment' => $strStatus . $varsId['numValue' . $valueStr] . $varsComment['strNumValue'], 'strUrl' => $arr['strUrl'],));
						}

						$id = 'idSubAccountTitle' . $valueStr;
						if ($value[$id] != '') {
							if (!$varsCheck['arrSubAccountTitle'][$idAccountTitle][$value[$id]]) {
								$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strNone'], 'strUrl' => $arr['strUrl'],));
							}
							$idSubAccountTitle = $varsCheck['arrSubAccountTitle'][$idAccountTitle][$value[$id]];
							if (!$varsRule['arrSubAccountTitle']['arrStrTitle'][$idAccountTitle][$idSubAccountTitle]) {
								$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strNone'], 'strUrl' => $arr['strUrl'],));
							}
							$varsDetail['arr' . $valueStr]['idSubAccountTitle'] = $idSubAccountTitle;
						}

						$id = 'idDepartment' . $valueStr;
						if ($value[$id] != '') {
							if (!$varsCheck['arrDepartment'][$value[$id]]) {
								$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strNone'], 'strUrl' => $arr['strUrl'],));
							}
							$idDepartment = $varsCheck['arrDepartment'][$value[$id]];
							$varsDetail['arr' . $valueStr]['idDepartment'] = $idDepartment;
						}

						$flagConsumptionTax = '';
						$id = 'flagConsumptionTax' . $valueStr;
						if ($value[$id] != '') {
							if (!$varsCheck['arrConsumptionTax'][$value[$id]]) {
								$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strNone'], 'strUrl' => $arr['strUrl'],));
							}
							$flagConsumptionTax = $varsCheck['arrConsumptionTax'][$value[$id]];
						}

						$flagConsumptionTaxWithoutCalc = $flagConsumptionTaxWithoutCalcDefault;
						$id = 'flagConsumptionTaxWithoutCalc' . $valueStr;
						if ($value[$id] != '') {
							if (!$varsCheck['arrWithoutCalc'][$value[$id]]) {
								$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strNone'], 'strUrl' => $arr['strUrl'],));
							}
							$flagConsumptionTaxWithoutCalc = $varsCheck['arrWithoutCalc'][$value[$id]];
						}

						$varsDetail['arr' . $valueStr]['flagConsumptionTaxCalc'] = $flagConsumptionTaxCalc;
						if ($flagConsumptionTaxFree) {
							$varsDetail['arr' . $valueStr]['flagConsumptionTaxFree'] = 1;
							$varsDetail['arr' . $valueStr]['flagConsumptionTaxIncluding'] = 1;
							$varsDetail['arr' . $valueStr]['flagConsumptionTaxGeneralRuleEach'] = '';
							$varsDetail['arr' . $valueStr]['flagConsumptionTaxGeneralRuleProration'] = '';
							$varsDetail['arr' . $valueStr]['flagConsumptionTaxSimpleRule'] = '';
							$varsDetail['arr' . $valueStr]['flagConsumptionTaxWithoutCalc'] = 1;

						} else {
							$varsDetail['arr' . $valueStr]['flagConsumptionTaxFree'] = 0;
							if ($flagConsumptionTaxGeneralRule) {
								if ($flagConsumptionTaxDeducted) {
									$varsDetail['arr' . $valueStr]['flagConsumptionTaxGeneralRuleEach'] = $flagConsumptionTax;
								} else {
									$varsDetail['arr' . $valueStr]['flagConsumptionTaxGeneralRuleProration'] = $flagConsumptionTax;
								}

							} else {
								$varsDetail['arr' . $valueStr]['flagConsumptionTaxSimpleRule'] = $flagConsumptionTax;
							}
							if ($flagConsumptionTaxIncluding) {
								$varsDetail['arr' . $valueStr]['flagConsumptionTaxIncluding'] = 1;
								$varsDetail['arr' . $valueStr]['flagConsumptionTaxWithoutCalc'] = 1;

							} else {
								$varsDetail['arr' . $valueStr]['flagConsumptionTaxIncluding'] = 0;
								$varsDetail['arr' . $valueStr]['flagConsumptionTaxWithoutCalc'] = $flagConsumptionTaxWithoutCalc;
							}

							if (!$flagConsumptionTaxIncluding && preg_match( "/^tax/", $flagConsumptionTax)) {
								$flagTax = 1;
							}

							if ($idAccountTitle == 'suspenseReceiptOfConsumptionTaxes' || $idAccountTitle == 'suspensePaymentConsumptionTaxes') {
								$flagTax = 0;
							}
						}

						$id = 'numRateConsumptionTax' . $valueStr;
						/*
						 * 20191001 start
						 */
						$varsDetail['arr' . $valueStr]['flagRateConsumptionTaxReduced'] = '';
						if ($value[$id] != '') {


						    $varsDetail['arr' . $valueStr]['flagRateConsumptionTaxReduced'] = 0;
							if (!preg_match("/^(5|8|10)$/", $value[$id])) {
							    //if (!preg_match("/^(5|8)$/", $value[$id])) {
							    $strRateConsumptionTaxReduced = 8 . $arr['vars']['varsItem']['strRateConsumptionTaxReduced'];
							    if ($value[$id] != $strRateConsumptionTaxReduced) {
							        $this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strTaxRate'], 'strUrl' => $arr['strUrl'],));
							    }
							    $value[$id] = 8;
							    $varsDetail['arr' . $valueStr]['flagRateConsumptionTaxReduced'] = 1;
							}
							/*
							 * 20191001 end
							 */
						}

						if ($flagConsumptionTaxFree) {
							$varsDetail['arr' . $valueStr]['numRateConsumptionTax'] = '';

						} else {
							if (preg_match("/^tax/", $flagConsumptionTax)
								|| preg_match("/^else/", $flagConsumptionTax)
							) {
								if ($numRate == 8) {
								    if ($value[$id] == 10
								        /*
								         * 20191001 start
								         */
								        || $varsDetail['arr' . $valueStr]['flagRateConsumptionTaxReduced']
								        /*
								         * 20191001 end
								         */
								    ) {
								            $this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strTaxRatePre'], 'strUrl' => $arr['strUrl'],));
								    }

								} elseif ($numRate == 5) {
								    if ($value[$id] == 8
								        || $value[$id] == 10
								        /*
								         * 20191001 start
								         */
								        || $varsDetail['arr' . $valueStr]['flagRateConsumptionTaxReduced']
								        /*
								         * 20191001 end
								         */
								    ) {
								            $this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strTaxRatePre'], 'strUrl' => $arr['strUrl'],));
								    }

								}
								$varsDetail['arr' . $valueStr]['numRateConsumptionTax'] = $value[$id];

							} else {
								$varsDetail['arr' . $valueStr]['numRateConsumptionTax'] = '';
							}
						}

						if ($valueStr == 'Debit') {
							$numSumDebit += $numValue;
						} else {
							$numSumCredit += $numValue;
						}

						if ($flagTax) {
							if ($flagConsumptionTaxWithoutCalc == 2) {
								$numValue -= $numValueConsumptionTax;
							}
						}
						if ($numValue < 0) {
							$this->_sendError(array('comment' => $strStatus . $varsId['numValueConsumptionTax' . $valueStr] . $varsComment['strTax'], 'strUrl' => $arr['strUrl'],));
						}
						$varsDetail['arr' . $valueStr]['numValue'] =  $numValue;

						if ($flagTax) {
							if ($flagConsumptionTaxWithoutCalc != 3) {

							} else {
								if ($numValueConsumptionTax != 0) {
									$this->_sendError(array('comment' => $strStatus . $varsId['numValueConsumptionTax' . $valueStr] . $varsComment['strTaxOut'], 'strUrl' => $arr['strUrl'],));
								}
							}
						}

					} else {
						if ($value['numValue' . $valueStr] != 0) {
							$this->_sendError(array('comment' => $strStatus . $varsId['numValue' . $valueStr] . $varsComment['strAccountTitle'], 'strUrl' => $arr['strUrl'],));

						} elseif ($value['numValueConsumptionTax' . $valueStr] != 0) {
							$this->_sendError(array('comment' => $strStatus . $varsId['numValueConsumptionTax' . $valueStr] . $varsComment['strAccountTitle'], 'strUrl' => $arr['strUrl'],));
						}
						$arrayEr = array(
							'idSubAccountTitle',
							'idDepartment',
							'flagConsumptionTax',
							'flagConsumptionTaxWithoutCalc',
						);
						foreach ($arrayEr as $keyEr => $valueEr) {
							$id = $valueEr . $valueStr;
							if ($value[$id] != '') {
								$this->_sendError(array('comment' => $strStatus . $varsId[$id] . $varsComment['strAccountTitle'], 'strUrl' => $arr['strUrl'],));
							}
						}
					}
				}
				$varsRequest['jsonDetail']['varsDetail'][] = $varsDetail;
				$varsRequest['jsonDetail']['idAccountTitleDebit'] = 'else';
				$varsRequest['jsonDetail']['idAccountTitleCredit'] = 'else';
				$varsRequest['jsonDetail']['numSum'] = $numSumDebit;
				$varsRequest['jsonDetail']['numSumDebit'] = $numSumDebit;
				$varsRequest['jsonDetail']['numSumCredit'] = $numSumCredit;
				$varsRequest['jsonDetail']['varsEntityNation'] = array(
					'flagConsumptionTaxFree'         => $varsEntityNation['flagConsumptionTaxFree'],
					'flagConsumptionTaxGeneralRule'  => $varsEntityNation['flagConsumptionTaxGeneralRule'],
					'flagConsumptionTaxDeducted'     => $varsEntityNation['flagConsumptionTaxDeducted'],
					'flagConsumptionTaxIncluding'    => $varsEntityNation['flagConsumptionTaxIncluding'],
					/* journal.js insert
					'flagConsumptionTaxCalc'         => $varsEntityNation['flagConsumptionTaxCalc'],
					'flagConsumptionTaxWithoutCalc'  => $varsEntityNation['flagConsumptionTaxWithoutCalc'],
					'flagConsumptionTaxBusinessType' => $varsEntityNation['flagConsumptionTaxBusinessType'],
					*/
				);
			}

			if ($varsRequest['jsonDetail']['numSumDebit'] != $varsRequest['jsonDetail']['numSumCredit']) {
				$this->_sendError(array('comment' => $strStatus . $varsComment['strSum'], 'strUrl' => $arr['strUrl'],));
			}


			if ($varsRequest['jsonDetail']['numSum'] > 99999999999) {
				$this->_sendError(array('comment' => $strStatus . $varsComment['strSumMax'], 'strUrl' => $arr['strUrl'],));
			}

			$arrayRequests[] = $varsRequest;
		}

		return $arrayRequests;
	}

	/**
	 *
	 */
	protected function _getNumFiscalTermStamp($arr)
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$numTimeZone = PLUGIN_ACCOUNTING_NUM_TIME_ZONE;

		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$numFiscalPeriodStart = $varsPluginAccountingEntity[$varsPluginAccountingAccount['idEntityCurrent']]['numFiscalPeriodStart'];
		$varsEntityNation = $arr['varsEntityNation'];
		$numFiscalBeginningYear = $varsEntityNation['numFiscalBeginningYear'];
		$numCurrentYear = $numFiscalBeginningYear;
		$numCurrentYear2 = $numFiscalBeginningYear;

		$strTimeZone = (-1 * $numTimeZone) . 'hours';
		$numYear = $numCurrentYear;
		$numMonth = $varsEntityNation['numFiscalBeginningMonth'];
		$dateTime = new DateTime("$numYear-$numMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stampMin = $dateTime->format('U');

		$numEndMonth = $varsEntityNation['numFiscalBeginningMonth'] + $varsEntityNation['numFiscalTermMonth'];
		if ($numEndMonth > 12) {
			$numCurrentYear++;
			$numEndMonth -= 12;
		}

		$numEndMonth2 = $varsEntityNation['numFiscalBeginningMonth'] + 6;
		if ($numEndMonth2 > 12) {
			$numCurrentYear2++;
			$numEndMonth2 -= 12;
		}

		$numYear = $numCurrentYear;
		$numMonth = $numEndMonth;
		$dateTime = new DateTime("$numYear-$numMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stampMax = $dateTime->format('U') - 1;

		$numYear = $numCurrentYear2;
		$numMonth = $numEndMonth2;
		$dateTime = new DateTime("$numYear-$numMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stampMax2 = $dateTime->format('U') - 1;

		$data = array(
			'stampMin'  => $stampMin,
			'stampMax'  => $stampMax,
			'stampMax2' => $stampMax2,
		);

		return $data;

	}

	/**

	 */
	protected function _setDbLog($arr)
	{
		global $classEscape;
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsAccount;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$vars = $arr['vars'];
		$arrValue = $arr['arrValue'];

		$stampRegister = TIMESTAMP;
		$stampUpdate = TIMESTAMP;
		$stampBook = $arrValue['stampBook'];
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = (int) $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$idAccount = $arrValue['idAccount'];
		$flagFiscalReport = $arrValue['flagFiscalReport'];
		$strTitle = $arrValue['strTitle'];

		$arrSpaceStrTag = $arrValue['arrSpaceStrTag'];
		$arrValue['arrSpaceStrTag'] = $arrSpaceStrTag;

		$arrCommaIdLogFile = $arrValue['jsonDetail']['arrCommaIdLogFile'];
		$arrValue['arrCommaIdLogFile'] = $arrCommaIdLogFile;

		$arrCommaIdAccountPermit = $arrValue['jsonDetail']['arrCommaIdAccountPermit'];

		$jsonPermitHistory = json_encode(array());
		$flagApply = 0;
		$flagApplyBack = 0;
		$idAccountApply = null;

		$classCalcLog = $this->_getClassCalc(array('flagType' => 'Log'));
		$varsVersion = $classCalcLog->allot(array(
			'flagStatus'      => 'varsVersion',
			'arrValue'        => $arrValue,
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));
		$jsonVersion = $varsVersion['jsonVersion'];

		$numValue = $varsVersion['numValue'];

		$arrCommaIdDepartmentDebit = $varsVersion['arrCommaIdDepartmentDebit'];
		$arrCommaIdAccountTitleDebit = $varsVersion['arrCommaIdAccountTitleDebit'];
		$arrCommaIdSubAccountTitleDebit = $varsVersion['arrCommaIdSubAccountTitleDebit'];
		$arrCommaConsumptionTaxDebit = $varsVersion['arrCommaConsumptionTaxDebit'];
		$arrCommaRateConsumptionTaxDebit = $varsVersion['arrCommaRateConsumptionTaxDebit'];
		$arrCommaConsumptionTaxWithoutCalcDebit = $varsVersion['arrCommaConsumptionTaxWithoutCalcDebit'];
		$arrCommaTaxPaymentDebit = $varsVersion['arrCommaTaxPaymentDebit'];
		$arrCommaTaxReceiptDebit = $varsVersion['arrCommaTaxReceiptDebit'];

		$arrCommaIdDepartmentCredit = $varsVersion['arrCommaIdDepartmentCredit'];
		$arrCommaIdAccountTitleCredit = $varsVersion['arrCommaIdAccountTitleCredit'];
		$arrCommaIdSubAccountTitleCredit = $varsVersion['arrCommaIdSubAccountTitleCredit'];
		$arrCommaConsumptionTaxCredit = $varsVersion['arrCommaConsumptionTaxCredit'];
		$arrCommaRateConsumptionTaxCredit = $varsVersion['arrCommaRateConsumptionTaxCredit'];
		$arrCommaConsumptionTaxWithoutCalcCredit = $varsVersion['arrCommaConsumptionTaxWithoutCalcCredit'];
		$arrCommaTaxPaymentCredit = $varsVersion['arrCommaTaxPaymentCredit'];
		$arrCommaTaxReceiptCredit = $varsVersion['arrCommaTaxReceiptCredit'];

		$arrCommaIdDepartmentVersion = $varsVersion['arrCommaIdDepartment'];
		$arrCommaIdAccountTitleVersion = $varsVersion['arrCommaIdAccountTitle'];
		$arrCommaIdSubAccountTitleVersion = $varsVersion['arrCommaIdSubAccountTitle'];

		$arrChargeHistory = array(
			array(
				'stampRegister' => TIMESTAMP,
				'idAccount'     => $idAccount,
			),
		);
		$jsonChargeHistory = json_encode($arrChargeHistory);

		$varsIdNumber = $this->_getIdAutoIncrement(array(
			'idTarget' => 'idLog'
		));
		if (!$varsIdNumber[$idEntity][$numFiscalPeriod]) {
			$varsIdNumber[$idEntity][$numFiscalPeriod] = 1;
		}
		$idLog = $varsIdNumber[$idEntity][$numFiscalPeriod];

		$arrayTemp = compact(
			'stampRegister',
			'stampUpdate',
			'stampBook',
			'idLog',
			'idEntity',
			'numFiscalPeriod',
			'idAccount',
			'flagFiscalReport',
			'strTitle',
			'arrSpaceStrTag',
			'flagApply',
			'idAccountApply',
			'flagApplyBack',
			'arrCommaIdAccountPermit',
			'arrCommaIdLogFile',
			'jsonVersion',
			'numValue',
			'arrCommaIdDepartmentDebit',
			'arrCommaIdAccountTitleDebit',
			'arrCommaIdSubAccountTitleDebit',
			'arrCommaConsumptionTaxDebit',
			'arrCommaRateConsumptionTaxDebit',
			'arrCommaConsumptionTaxWithoutCalcDebit',
			'arrCommaTaxPaymentDebit',
			'arrCommaTaxReceiptDebit',
			'arrCommaIdDepartmentCredit',
			'arrCommaIdAccountTitleCredit',
			'arrCommaIdSubAccountTitleCredit',
			'arrCommaConsumptionTaxCredit',
			'arrCommaRateConsumptionTaxCredit',
			'arrCommaConsumptionTaxWithoutCalcCredit',
			'arrCommaTaxPaymentCredit',
			'arrCommaTaxReceiptCredit',
			'arrCommaIdDepartmentVersion',
			'arrCommaIdAccountTitleVersion',
			'arrCommaIdSubAccountTitleVersion',
			'jsonChargeHistory',
			'jsonPermitHistory'
		);

		$arrColumn = array();
		$arrValue = array();
		foreach ($arrayTemp as $keyTemp => $valueTemp) {
			$arrColumn[] = $keyTemp;
			$arrValue[] = $valueTemp;
		}

		$id = $classDb->insertRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingLog',
			'arrColumn' => $arrColumn,
			'arrValue'  => $arrValue,
		));

		$varsIdNumber[$idEntity][$numFiscalPeriod]++;
		$this->_updateIdAutoIncrement(array(
			'idTarget'   => 'idLog',
			'varsTarget' => $varsIdNumber
		));

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLog',
			'arrLimit' => array(
				'numStart' => 0, 'numEnd' => 1,
			),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'id',
					'flagCondition' => 'eq',
					'value'         => $id,
				),
			),
		));
		$varsLog = $rows['arrRows'][0];

		return $varsLog;
	}

	/**
	 (array(
		'varsRule' => $varsRule,
		'idTarget' => $value[$id],
	 ))
	 */
	protected function _checkIdAccountTitle($arr)
	{
		if ($arr['varsRule']['arrAccountTitle']['arrStrTitle'][$arr['idTarget']]) {
			return $arr['idTarget'];
		}
		$array = $arr['varsRule']['arrayFSList'];
		foreach ($array as $key => $value) {
			$flagFS = $key;
			$strId = 'custom_' . $flagFS . '_' . $arr['idTarget'];
			if ($arr['varsRule']['arrAccountTitle']['arrStrTitle'][$strId]) {
				return $strId;
			}
		}

		return '';
	}

	/**
	 *
	 */
	protected function _getStampBook($arr)
	{
		global $classCheck;

		global $varsAccounts;
		global $varsAccount;
		global $varsPluginAccountingAccount;

		$strStamp = $arr['strBook'];
		if (preg_match( "/^([0-9]{4})\/([0-9]{1,2})\/([0-9]{1,2})-([0-9]{1,2}):([0-9]{1,2})$/", $strStamp)) {
			$flag = $classCheck->checkValueFormat(array(
				'flagType' => 'date-time',
				'value'    => $strStamp
			));
			if ($flag) {
				return 0;
			}
			preg_match( "/^([0-9]{4})\/([0-9]{1,2})\/([0-9]{1,2})-([0-9]{1,2}):([0-9]{1,2})$/", $strStamp, $arrMatch);
			list($strStamp, $numYear, $numMonth, $numDate, $numHour, $numMin) = $arrMatch;

		} elseif (preg_match( "/^([0-9]{4})\/([0-9]{1,2})\/([0-9]{1,2})$/", $strStamp)) {
			$flag = $classCheck->checkValueFormat(array(
				'flagType' => 'date',
				'value'    => $strStamp
			));
			if ($flag) {
				return 0;
			}
			preg_match( "/^([0-9]{4})\/([0-9]{1,2})\/([0-9]{1,2})$/", $strStamp, $arrMatch);
			list($strStamp, $numYear, $numMonth, $numDate) = $arrMatch;
			$numHour = 0;
			$numMin = 0;

		} elseif (preg_match( "/^H\.([0-9]{2,3})\/([0-9]{1,2})\/([0-9]{1,2})$/", $strStamp)) {
			preg_match( "/^H\.([0-9]{2,3})\/([0-9]{1,2})\/([0-9]{1,2})$/", $strStamp, $arrMatch);
			list($strDummy, $numYear, $numMonth, $numDate) = $arrMatch;
			$numYear -= 12;
			$numHour = 0;
			$numMin = 0;

		} elseif (preg_match( "/^H([0-9]{2,3})\/([0-9]{1,2})\/([0-9]{1,2})$/", $strStamp)) {
			preg_match( "/^H([0-9]{2,3})\/([0-9]{1,2})\/([0-9]{1,2})$/", $strStamp, $arrMatch);
			list($strDummy, $numYear, $numMonth, $numDate) = $arrMatch;
			$numYear -= 12;
			$numHour = 0;
			$numMin = 0;
			/*
			 * 20191001 start
			 */
		} elseif (preg_match( "/^R\.([0-9]{1,3})\/([0-9]{1,2})\/([0-9]{1,2})$/", $strStamp)) {
		    preg_match( "/^R\.([0-9]{1,3})\/([0-9]{1,2})\/([0-9]{1,2})$/", $strStamp, $arrMatch);
		    list($strDummy, $numYear, $numMonth, $numDate) = $arrMatch;
		    $numYear += 18;
		    $numHour = 0;
		    $numMin = 0;

		} elseif (preg_match( "/^R([0-9]{1,3})\/([0-9]{1,2})\/([0-9]{1,2})$/", $strStamp)) {
		    preg_match( "/^R([0-9]{1,3})\/([0-9]{1,2})\/([0-9]{1,2})$/", $strStamp, $arrMatch);
		    list($strDummy, $numYear, $numMonth, $numDate) = $arrMatch;
		    $numYear += 18;
		    $numHour = 0;
		    $numMin = 0;
		    /*
		     * 20191001 end
		     */
		} else {
			return 0;
		}

		$stampBook = 0;
		if ($arr['flagFiscalReport'] == 'f1') {
			$stampBook = $arr['varsStamp']['stampMax'];

		} elseif ($arr['flagFiscalReport'] == 'f21') {
			$stampBook = $arr['varsStamp']['stampMax2'];

		} else {
			$strTimeZone = (-1 * $varsAccount['numTimeZone']) . 'hours';
			$dateTime = new DateTime("$numYear-$numMonth-$numDate 0:0 $strTimeZone", new DateTimeZone("UTC"));
			$stampBook = $dateTime->format('U') + $numHour * 3600 + $numMin * 60;
		}

		return $stampBook;
	}

	/**

	 */
	protected function _sendError($arr)
	{
		unlink($arr['strUrl']);

		$this->_sendMessage(array('flag' => 'dummy', 'vars' => $arr['comment']));
	}

	/**

	 */
	protected function _sendMessage($arr)
	{
		$this->_sendVars(array(
			'flagIframe' => 1,
			'flag'       => ($arr['flag'])? $arr['flag'] : 1,
			'stamp'      => $this->getStamp(),
			'numNews'    => $this->getNumNews(),
			'vars'       => ($arr['vars'])? $arr['vars'] : array(),
		));
	}

	/**

	 */
	protected function _checkFileValue($arr)
	{
		global $classCheck;
		global $classEscape;

		global $varsRequest;
		global $varsPluginAccountingPreference;
		global $varsPluginAccountingAccount;

		$arrFile = $this->_checkValueFile(array(
			'vars' => $arr['vars'],
		));

		$id = $varsRequest['query']['idTag'];
		$strFileType = $classEscape->getFileType(array('strUrl' => $_FILES[$id]['name']));
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];

		$strFileName = hash('sha256', MICROTIMESTAMP). '.' . $strFileType . '-' . $idEntity . '-' . $numFiscalPeriod . '.cgi';
		$path = PATH_BACK_DAT_TEMP;
		if (!is_dir($path)) {
			mkdir($path);
		}
		$strUrl = $path . $strFileName;

		if (!move_uploaded_file($_FILES[$id]['tmp_name'], $strUrl)) {
			$this->_sendMessage(array('flag' => 'strError'));
		}

		`nkf -wLu --overwrite $strUrl`;

		$data = array(
			'strUrl'      => $strUrl,
			'strFileType' => $strFileName,
			'strFileType' => $arrFile['strFileType'],
			'numByte'     => $arrFile['numByte'],
		);

		return $data;
	}

	/**

	 */
	protected function _checkValueFile($arr)
	{
		global $classEscape;

		global $varsRequest;
		global $varsPluginAccountingPreference;

		$id = $varsRequest['query']['idTag'];

		$strFileType = $classEscape->getFileType(array('strUrl' => $_FILES[$id]['name']));

		$array = array('csv');
		foreach ($array as $key => $value) {
			$arrayCheck[$value] = 1;
		}

		if (!$arrayCheck[$strFileType]) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
			}
			exit;
		}

		$numByte = $_FILES[$id]['size'];
		if ($numByte > NUM_MAX_UPLOAD_SIZE) {
			$this->_sendMessage(array('flag' => 'dummy', 'vars' => $arr['vars']['varsItem']['varsComment']['strUploadSize']));
		}

		if ($_FILES[$id]['error']) {
			$this->_sendMessage(array('flag' => 'dummy', 'vars' => $arr['vars']['varsItem']['varsComment']['strUploadError']));
		}

		$data = array(
			'strFileType' => $strFileType,
			'numByte'     => $numByte,
		);

		return $data;
	}

	/**
		array(
			'flagIframe' => int,
			'flag'       => mix,
			'stamp'      => array,
			'numNews'    => int,
			'vars'       => array,
		)
	 */
	protected function _sendVars($arr)
	{
		global $varsRequest;

		$array = array(
			'flag'    => (!is_null($arr['flag']))? $arr['flag'] : 1,
			'numNews' => ($arr['numNews'])? $arr['numNews'] : 0,
			'stamp'   => ($arr['stamp'])? $arr['stamp'] : '',
			'data'    => $arr['vars'],
		);

		$jsonVars = json_encode($array);
		$jsonIdUpload = json_encode($varsRequest['query']['idUpload']);

		$varsIframe = $this->getVars(array(
			'path' => $this->_extSelf['varsIframe'],
		));
		$tmplIframe = str_replace('<%idUpload%>', $jsonIdUpload, $varsIframe['tmpl']);
		$tmplIframe = str_replace('<%vars%>', $jsonVars, $tmplIframe);

		print $tmplIframe;
		exit;
	}


}
