<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_LogImportRetry extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference' => 'logWindow',
		'idCash'       => 'cashWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/logImportRetry.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/logImportRetry.php',
		'pathTplHtml'  => 'else/plugin/accounting/html/logImportRetry.html',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;
		global $classCheck;

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'select',
			'idTarget'      => $this->_extSelf['idPreference'],
		));

		if (!$flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__);
			}
			exit;
		}

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done)$/", $flagCurrentFlagNow)) {
			$this->_sendOldFlag();
		}

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

		$vars = $this->getStamp();
		$json = json_encode($vars);
		$classSmarty->assign('stamp', $json);

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$rows = $this->getSearch(array('numLotNow' => 0));
		$vars = $this->_updateSearch(array(
			'vars' => $vars,
			'rows' => $rows,
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
	 * array(
	 *  'numLotNow' => int
	 * )
	 */
	public function getSearch($arr)
	{
		global $varsAccount;
		global $varsPluginAccountingAccount;
		global $classDb;

		$numStart = $arr['numLotNow'] * $varsAccount['numList'];
		$numEnd = $numStart + $varsAccount['numList'];

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		//idEntity
		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogImportRetry' . $strNation,
			'arrLimit' => array(
				'numStart' => $numStart, 'numEnd' => $numEnd,
			),
			'arrOrder' => array(
				'strColumn' => 'id',
				'flagDesc'  => 1,
			),
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
			),
		));

		return $rows;
	}

	/**
	 *
	 */
	protected function _updateSearch($arr)
	{
		$classTime = new Code_Else_Lib_Time();

		global $classHtml;
		global $varsAccount;

		global $varsPluginAccountingAccount;

		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$idEntity  = $varsPluginAccountingAccount['idEntityCurrent'];
		$strCheckStamp = 'accountingLogImportRetryJpn_' . $idEntity . '_' . $numFiscalPeriod;

		$vars = $arr['vars'];
		$rows = $arr['rows'];

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllInsert'])) {
			$arrayNew = array();
			$array = $vars['portal']['varsDetail']['tmplBtn']['varsStart'];
			foreach ($array as $key => $value) {
				if ($value['vars']['idTarget'] == 'filter') {
					continue;
				}
				$arrayNew[] = $value;
			}
			$vars['portal']['varsDetail']['tmplBtn']['varsStart'] = $arrayNew;
		}

		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllDelete'])) {
			$vars['portal']['varsNavi']['tree']['varsDetail']['varsBtn'] = array();
			$arrayNew = array();
			$array = $vars['portal']['varsDetail']['tmplBtn']['varsStart'];
			foreach ($array as $key => $value) {
				if ($value['vars']['idTarget'] == 'delete') {
					continue;
				}
				$arrayNew[] = $value;
			}
			$vars['portal']['varsDetail']['tmplBtn']['varsStart'] = $arrayNew;
		}

		$array = &$rows['arrRows'];
		$arrayNew = array();
		foreach ($array as $key => $value) {

			$varsTmpl = $vars['portal']['varsNavi']['tree']['templateDetail'];
			$varsTmpl['id'] = $value['idLogRetry'];

			$classTime->setTimeZone(array('data' => $varsAccount['numTimeZone']));
			$strTime = $classTime->getDisplay(array(
				'stamp'    => $value['stampRegister'],
				'flagType' => 'year-sec',
			));
			$varsTmpl['strTitle'] = $strTime . ' (' . count($value['jsonData']['varsDetail']) . ')';

			if ($varsAccount['jsonStampCheck'][$strCheckStamp] < $value['stampRegister']) {
				$flag = 1;

			} else {
				$varsTmpl['strClassLoad'] = '';
			}

			$varsTmpl['vars']['idTarget'] = $value['idLogRetry'];
			$varsTmpl['vars']['stampRegister'] = $value['stampRegister'];
			$varsTmpl['vars']['stampUpdate'] = $value['stampUpdate'];
			$varsTmpl['vars']['flagType'] = $value['flagType'];
			$varsTmpl['vars']['jsonData'] = $value['jsonData'];
			$value['jsonData']['varsColumn'][0] = $vars['varsItem']['strColNum'];
			if (preg_match("/^banks/", $value['flagType'])) {
				$value['jsonData']['varsColumn'][0] = $vars['varsItem']['strColId'];
			}

			$numWidthTable = 0;
			$varsColumnWidth = array();
			$arrayColumn = &$value['jsonData']['varsColumn'];
			foreach ($arrayColumn as $keyColumn => $valueColumn) {
				$varsColumnWidth[$keyColumn] = $vars['varsItem']['numWidthColumn'];
				$numWidthTable += $vars['varsItem']['numWidthColumn'];
			}
			$varsStatus = array();
			$varsStatus['varsColumnWidth'] = $varsColumnWidth;
			$varsStatus['numWidthTable'] = $numWidthTable;
			$varsStatus['numFontSize'] = $vars['varsItem']['numFontSize'];

			$arrayDetail = &$value['jsonData']['varsDetail'];
			foreach ($arrayDetail as $keyDetail => $valueDetail) {
				$arrayDetail[$keyDetail]['varsDetail'][0]['strClass'] = 'codeLibBaseTableColumnMiddle';
			}

			$varsTemp = $classHtml->allot(array(
				'strClass'    => 'TableSimple',
				'flagStatus'  => 'Html',
				'varsDetail'  => $value['jsonData']['varsDetail'],
				'varsColumn'  => $value['jsonData']['varsColumn'],
				'varsStatus'  => $varsStatus,
			));
			$varsTmpl['vars']['strHtml'] = $varsTemp['strHtml'];

			$arrayNew[] = $varsTmpl;
		}

		$vars['portal']['varsNavi']['tree']['varsDetail']['varsPage']['varsStatus']['numRows'] = $rows['numRows'];
		$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $arrayNew;

		if ($flag) {
			$this->_setDbStampCheck(array(
				'strColumnAccount'    => $strCheckStamp,
				'strColumnPreference' => 'accounts',
			));
		}

		return $vars;
	}

	/**
	 */
	protected function _iniNaviSearch()
	{
		$this->_setNaviSearch(array('flag' => 1));
	}

	/**
	 *
	 */
	protected function _iniNaviReload()
	{
		$this->_setNaviSearch(array('flag' => 1));
	}

	/**
	 * array(
	 *  flag => int
	 * )
	 */
	protected function _setNaviSearch($arr)
	{
		global $varsPluginAccountingPreference;

		global $varsRequest;
		global $classCheck;

		$numLotNow = $varsRequest['query']['jsonSearch']['numLotNow'];
		$flag = $classCheck->checkValueWord(array(
			'flagType' => 'num',
			'value'    => $numLotNow,
		));

		if ($flag && $numLotNow != '') {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__);
			}
			exit;
		}

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingPreference['jsonStampUpdate']['logRetry'],
				'flagSearch'  => 1,
			));
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$rows = $this->getSearch(array('numLotNow' => $numLotNow));

		if (!count($rows['arrRows'])) {
			$numLotNow = 0;
			$rows = $this->getSearch(array('numLotNow' => $numLotNow));
		}

		$vars = $this->_updateSearch(array(
			'vars' => $vars,
			'rows' => $rows,
		));

		$data = array(
			'numLotNow'  => $numLotNow,
			'numRows'    => $rows['numRows'],
			'varsDetail' => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
		);

		if ($arr['flagEnd']) {
			$data['idTarget'] = $varsRequest['query']['jsonValue']['idTarget'];
			$data['varsStart'] = array();
			$data['strHtml'] = '';
			$array = $data['varsDetail'];
			foreach ($array as $key => $value) {
				if ($data['idTarget'] == $value['id']) {
					$data['varsStart'] = $value;
					break;
				}
			}
			return $data;
		}

		$this->sendVars(array(
			'flag'    => $arr['flag'],
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $data,
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
	protected function _iniNaviDelete()
	{
		$arrayNew = array();
		$array = $this->_getLogImportRetry(array());
		foreach ($array as $key => $value) {
			$arrayNew[] = $value['idLogRetry'];
		}

		$this->_setDelete(array(
			'arrId' => $arrayNew,
		));

	}

	/**
	 *
	 */
	protected function _setDelete($arr)
	{
		global $varsRequest;
		global $classCheck;
		global $varsPluginAccountingAccount;

		global $classDb;
		$dbh = $classDb->getHandle();

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllDelete'])) {
			$this->_sendOldFlag();
		}

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

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

		try {
			$dbh->beginTransaction();

			foreach ($array as $key => $value) {
				$classDb->deleteRow(array(
					'idModule'  => 'accounting',
					'strTable'  => 'accountingLogImportRetry' . $strNation,
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
							'strColumn'     => 'idLogRetry',
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
						'strTable'  => 'accountingLogImportRetry' . $strNation,
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
								'strColumn'     => 'idLogRetry',
								'flagCondition' => 'eq',
								'value'         => $value,
							),
						),
					));
				}
			}

			$array = array('logImportRetry');
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
		$this->_setNaviSearch(array('flag' => 1));
	}

	/**
	 *
	 */
	protected function _iniDetailAdd()
	{
		global $classDb;
		global $classEscape;

		global $varsRequest;
		$dbh = $classDb->getHandle();

		global $varsAccount;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllInsert'])) {
			$this->_sendOldFlag();
		}

		$classCalcLogImport = $this->_getClassCalc(array('flagType' => 'LogImport'));
		$classCalcCashPay = $this->_getClassCalc(array('flagType' => 'CashPay'));
		$classCalcCash = $this->_getClassCalc(array('flagType' => 'Cash'));
		$classCalcLog = $this->_getClassCalc(array('flagType' => 'Log'));

		$classCalcBanks = $this->_getClassCalc(array('flagType' => 'Banks'));
		$classCalcBanksImport = $this->_getClassCalc(array('flagType' => 'BanksImport'));

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$flagCashInsert = $this->_checkAccess(array(
			'flagAllUse'    => 0,
			'flagAuthority' => 'insert',
			'idTarget'      => $this->_extSelf['idCash'],
		));

		$numFiscalPeriodTemp = $this->_getNumFiscalPeriodTemp();

		try {
			$dbh->beginTransaction();

			$arrVarsLog = array();
			$arrVarsLogTemp = array();

			$varsCSVData = $this->_checkVarsCSVData(array(
				'classCalcLogImport' => $classCalcLogImport,
			));

			$array = &$varsCSVData['varsCSVs'];
			foreach ($array as $key => $value) {
				$data = $value;
				$idTarget = $key;
				$arrSpaceStrTag = $varsCSVData['varsTag'][$idTarget];
				$flagType = $varsCSVData['varsType'][$idTarget];

				$arrayLog = $data['arrayRequests'];
				foreach ($arrayLog as $keyLog => $valueLog) {
					$valueLog['arrSpaceStrTag'] = $arrSpaceStrTag;
					if (preg_match("/^banks/", $flagType)) {
						$valueLog['arrSpaceStrTag'] .= ' banks:' . $valueLog['id'];
					}
					$valueLog['numRow'] = $valueLog['id'];
					$valueLog['flagType'] = $flagType;

					$flagCashVars = $classCalcCashPay->allot(array(
						'flagStatus'      => 'check',
						'arrValue'        => $valueLog,
						'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
						'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
						'classCalcLog'    => $classCalcLog,
					));
					if ($flagCashVars['flag'] && $flagCashInsert) {
						if ($flagCashVars['flag'] == 'pay' || $flagCashVars['flag'] == 'paid') {
							$flagVars = $classCalcCashPay->allot(array(
								'flagStatus'      => $flagCashVars['flag'],
								'arrValue'        => $valueLog,
								'arrRowsCash'     => $flagCashVars['arrRowsCash'],
								'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
								'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
								'classCalcCash'   => $classCalcCash,
							));
							if ($flagVars == 'errorDataMax') {
								$this->sendVars(array(
									'flag'    => $flagVars,
									'stamp'   => $this->getStamp(),
									'numNews' => $this->getNumNews(),
									'vars'    => array(),
								));
							}
							$dataLog = $classCalcLogImport->allot(array(
								'flagStatus'      => 'insertDbLog',
								'arrValue'        => $flagVars['arrValue'],
								'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
								'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
								'classCalcLog'    => $classCalcLog,
							));
							if (!$dataLog['flagApply']) {
								$arrVarsLog[] = $dataLog;
							}

							$flagVars = $classCalcCashPay->allot(array(
								'flagStatus'      => 'WriteHistory',
								'varsLog'         => $dataLog,
								'varsLogCash'     => $flagVars['arrVarsLogAdd'][0],
								'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
								'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
							));
							if ($flagVars == 'errorDataMax') {
								$this->sendVars(array(
									'flag'    => $flagVars,
									'stamp'   => $this->getStamp(),
									'numNews' => $this->getNumNews(),
									'vars'    => array(),
								));
							}
							if (preg_match("/^banks/", $flagType)) {
								$flagVars = $classCalcBanksImport->allot(array(
									'flagStatus'      => 'logImportWriteHistory',
									'classCalcBanks'  => $classCalcBanks,
									'idLogBanks'      => $valueLog['id'],
									'varsLog'         => $dataLog,
									'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
									'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
									'idAccount'       => $varsAccount['id'],
								));
								if ($flagVars == 'errorDataMax') {
									$this->sendVars(array(
										'flag'    => $flagVars,
										'stamp'   => $this->getStamp(),
										'numNews' => $this->getNumNews(),
										'vars'    => array(),
									));
								}
							}

						} elseif ($flagCashVars['flag'] == 'caution') {
							$numberRow = $valueLog['id'];
							$array[$key]['varsStatus']['arrCashDefer'][$numberRow] = 1;
							$flagVars = $classCalcCashPay->allot(array(
								'flagStatus'      => $flagCashVars['flag'],
								'varsLog'         => $flagCashVars['varsLog'],
								'arrRowsCash'     => $flagCashVars['arrRowsCash'],
								'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
								'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
							));
						}

					} else {
						$dataLog = $classCalcLogImport->allot(array(
							'flagStatus'      => 'insertDbLog',
							'arrValue'        => $valueLog,
							'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
							'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
							'classCalcLog'    => $classCalcLog,
						));
						if (!$dataLog['flagApply']) {
							$arrVarsLog[] = $dataLog;
						}
					}
				}

				$arrayLog = $data['arrayRequestsTemp'];
				foreach ($arrayLog as $keyLog => $valueLog) {
					$valueLog['arrSpaceStrTag'] = $arrSpaceStrTag;
					if (preg_match("/^banks/", $flagType)) {
						$valueLog['arrSpaceStrTag'] .= ' banks:' . $valueLog['id'];
					}
					$valueLog['numRow'] = $valueLog['id'];
					$valueLog['flagType'] = $flagType;
					$flagCashVars = $classCalcCashPay->allot(array(
						'flagStatus'      => 'check',
						'arrValue'        => $valueLog,
						'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
						'numFiscalPeriod' => $numFiscalPeriodTemp,
						'classCalcLog'    => $classCalcLog,
					));
					if ($flagCashVars['flag'] && $flagCashInsert) {
						if ($flagCashVars['flag'] == 'pay' || $flagCashVars['flag'] == 'paid') {
							$flagVars = $classCalcCashPay->allot(array(
								'flagStatus'      => $flagCashVars['flag'],
								'arrValue'        => $valueLog,
								'arrRowsCash'     => $flagCashVars['arrRowsCash'],
								'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
								'numFiscalPeriod' => $numFiscalPeriodTemp,
								'classCalcCash'   => $classCalcCash,
							));
							if ($flagVars == 'errorDataMax') {
								$this->sendVars(array(
									'flag'    => $flagVars,
									'stamp'   => $this->getStamp(),
									'numNews' => $this->getNumNews(),
									'vars'    => array(),
								));
							}
							$dataLog = $classCalcLogImport->allot(array(
								'flagStatus'      => 'insertDbLog',
								'arrValue'        => $flagVars['arrValue'],
								'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
								'numFiscalPeriod' => $numFiscalPeriodTemp,
								'classCalcLog'    => $classCalcLog,
							));
							if (!$dataLog['flagApply']) {
								$arrVarsLogTemp[] = $dataLog;
							}
							$flagVars = $classCalcCashPay->allot(array(
								'flagStatus'      => 'WriteHistory',
								'varsLog'         => $dataLog,
								'varsLogCash'     => $flagVars['arrVarsLogAdd'][0],
								'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
								'numFiscalPeriod' => $numFiscalPeriodTemp,
							));
							if ($flagVars == 'errorDataMax') {
								$this->sendVars(array(
									'flag'    => $flagVars,
									'stamp'   => $this->getStamp(),
									'numNews' => $this->getNumNews(),
									'vars'    => array(),
								));
							}
							if (preg_match("/^banks/", $flagType)) {
								$flagVars = $classCalcBanksImport->allot(array(
									'flagStatus'      => 'logImportWriteHistory',
									'classCalcBanks'  => $classCalcBanks,
									'idLogBanks'      => $valueLog['id'],
									'varsLog'         => $dataLog,
									'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
									'numFiscalPeriod' => $numFiscalPeriodTemp,
									'idAccount'       => $varsAccount['id'],
								));
								if ($flagVars == 'errorDataMax') {
									$this->sendVars(array(
										'flag'    => $flagVars,
										'stamp'   => $this->getStamp(),
										'numNews' => $this->getNumNews(),
										'vars'    => array(),
									));
								}
							}

						} elseif ($flagCashVars['flag'] == 'caution') {
							$numberRow = $valueLog['id'];
							$array[$key]['varsStatus']['arrCashDefer'][$numberRow] = 1;

							$flagVars = $classCalcCashPay->allot(array(
								'flagStatus'      => $flagCashVars['flag'],
								'varsLog'         => $flagCashVars['varsLog'],
								'arrRowsCash'     => $flagCashVars['arrRowsCash'],
								'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
								'numFiscalPeriod' => $numFiscalPeriodTemp,
							));
						}

					} else {
						$dataLog = $classCalcLogImport->allot(array(
							'flagStatus'      => 'insertDbLog',
							'arrValue'        => $valueLog,
							'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
							'numFiscalPeriod' => $numFiscalPeriodTemp,
							'classCalcLog'    => $classCalcLog,
						));
						if (!$dataLog['flagApply']) {
							$arrVarsLogTemp[] = $dataLog;
						}
					}
				}

				if ($data['varsRetry']['varsDetail']) {
					$classCalcLogImport->allot(array(
						'flagStatus'          => 'updateDbRetry',
						'idTarget'            => $idTarget,
						'arrSpaceStrTag'      => $arrSpaceStrTag,
						'vars'                => $data['varsRetry'],
						'idEntity'            => $varsPluginAccountingAccount['idEntityCurrent'],
						'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
						'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
					));

				} else {
					$classCalcLogImport->allot(array(
						'flagStatus'          => 'deleteDbRetry',
						'idTarget'            => $idTarget,
						'idEntity'            => $varsPluginAccountingAccount['idEntityCurrent'],
						'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
						'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
					));
				}
			}

			$classCalcAccountTitle = $this->_getClassCalc(array('flagType' => 'AccountTitle'));
			$classCalcSubAccountTitle = $this->_getClassCalc(array('flagType' => 'SubAccountTitle'));
			$classCalcConsumptionTax = $this->_getClassCalc(array('flagType' => 'ConsumptionTax'));
			$classCalcLogCalc = $this->_getClassCalc(array('flagType' => 'LogCalc'));
			$classCalcTempNextLog = $this->_getClassCalc(array(
				'flagType'   => 'TempNext',
				'flagDetail' => 'Log',
			));

			if ($arrVarsLog) {
				$arrRows = $this->_getVarsLogCalcLoop(array(
					'arrVarsLog'      => $arrVarsLog,
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				));

				$flag = $classCalcAccountTitle->allot(array(
					'flagStatus'      => 'add',
					'arrRows'         => $arrRows,
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				));
				if ($flag == 'errorDataMax') {
					$this->sendVars(array(
						'flag'    => $flag,
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(),
					));
				}

				$flag = $classCalcSubAccountTitle->allot(array(
					'flagStatus'      => 'add',
					'arrRows'         => $arrRows,
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				));
				if ($flag == 'errorDataMax') {
					$this->sendVars(array(
						'flag'    => $flag,
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(),
					));
				}

				$flag = $classCalcConsumptionTax->allot(array(
					'flagStatus'      => 'add',
					'arrRows'         => $arrRows,
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				));
				if ($flag == 'errorDataMax') {
					$this->sendVars(array(
						'flag'    => $flag,
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(),
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
						$this->sendVars(array(
							'flag'    => $flag,
							'stamp'   => $this->getStamp(),
							'numNews' => $this->getNumNews(),
							'vars'    => array(),
						));
					}
				}
				$this->_updateDbPreferenceStamp(array('strColumn' => 'log'));
			}

			if ($arrVarsLogTemp) {
				$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
				if ((preg_match("/^(tempPrev)$/", $flagCurrentFlagNow))) {
					$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] + 1;

				} elseif ((preg_match("/^(tempNext)$/", $flagCurrentFlagNow))) {
					$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] - 1;
				}
				$arrRows = $this->_getVarsLogCalcLoop(array(
					'arrVarsLog'      => $arrVarsLogTemp,
					'numFiscalPeriod' => $numFiscalPeriodTemp,
				));

				$flag = $classCalcAccountTitle->allot(array(
					'flagStatus'      => 'add',
					'arrRows'         => $arrRows,
					'numFiscalPeriod' => $numFiscalPeriodTemp,
				));
				if ($flag == 'errorDataMax') {
					$this->sendVars(array(
						'flag'    => $flag,
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(),
					));
				}

				$flag = $classCalcSubAccountTitle->allot(array(
					'flagStatus'      => 'add',
					'arrRows'         => $arrRows,
					'numFiscalPeriod' => $numFiscalPeriodTemp,
				));
				if ($flag == 'errorDataMax') {
					$this->sendVars(array(
						'flag'    => $flag,
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(),
					));
				}

				$flag = $classCalcConsumptionTax->allot(array(
					'flagStatus'      => 'add',
					'arrRows'         => $arrRows,
					'numFiscalPeriod' => $numFiscalPeriodTemp,
				));
				if ($flag == 'errorDataMax') {
					$this->sendVars(array(
						'flag'    => $flag,
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(),
					));
				}

				$classCalcLogCalc->allot(array(
					'flagStatus'      => 'add',
					'arrRows'         => $arrRows,
					'numFiscalPeriod' => $numFiscalPeriodTemp,
				));

				//tempNext is not wrong
				if ((preg_match("/^(tempNext)$/", $flagCurrentFlagNow))) {
					$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
					$flag = $classCalcTempNextLog->allot(array(
						'flagStatus'      => 'add',
						'numFiscalPeriod' => $numFiscalPeriod,
						'arrRows'         => $arrRows,
					));
					if ($flag == 'errorDataMax') {
						$this->sendVars(array(
							'flag'    => $flag,
							'stamp'   => $this->getStamp(),
							'numNews' => $this->getNumNews(),
							'vars'    => array(),
						));
					}
				}
				$this->_updateDbPreferenceStamp(array('strColumn' => 'log'));
			}

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$strHtml = $this->_getHtmlStatus(array(
			'vars'        => $vars,
			'varsCSVData' => $varsCSVData,
		));

		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
		$data = $this->_setNaviSearch(array('flag' => 1, 'flagEnd' => 1));

		$data['strHtml'] = $strHtml;

		$this->sendVars(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $data,
		));
	}

	/**

	 */
	protected function _getNumFiscalPeriodTemp()
	{
		global $varsPluginAccountingAccount;

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		$numFiscalPeriodTemp = 0;
		if ((preg_match("/^(tempPrev)$/", $flagCurrentFlagNow))) {
			$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] + 1;

		} elseif ((preg_match("/^(tempNext)$/", $flagCurrentFlagNow))) {
			$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] - 1;
		}

		return $numFiscalPeriodTemp;
	}

	/**
		(array(
			'vars'        => $vars,
			'varsCSVData' => $varsCSVData,
		))
	 */
	protected function _getHtmlStatus($arr)
	{
		global $classTime;
		global $varsAccount;

		$varsCSVs = $arr['varsCSVData']['varsCSVs'];
		$varsRetryLog = $arr['varsCSVData']['varsRetryLog'];

		$varsComment = $arr['vars']['varsItem']['varsComment'];
		$strHtml = '';
		$arrayLog = $varsRetryLog;
		foreach ($arrayLog as $keyLog => $valueLog) {
			$varsStr = $arr['vars']['varsItem']['varsStr'];
			$varsStatus = $varsCSVs[$valueLog['idLogRetry']]['varsStatus'];
			$varsStr['numLoadAll'] = $varsStatus['numAll'];
			$varsStr['numImportAll'] = count($varsStatus['arrImport']);
			$varsStr['numNoneAll'] = count($varsStatus['arrNone']);
			$varsStr['numErrorAll'] = count($varsStatus['arrError']);

			$classTime->setTimeZone(array('data' => $varsAccount['numTimeZone']));
			$varsStr['idLog'] = $classTime->getDisplay(array(
				'stamp'    => $valueLog['stampRegister'],
				'flagType' => 'year-sec',
			));

			$strVars = '';
			if (preg_match("/^banks/", $valueLog['flagType'])) {
				$strVars = 'Banks';
			}

			$arrImport = array();
			$array = $varsStatus['arrImport'];
			foreach ($array as $key => $value) {
				$strStatusRow = $varsComment['strStatusRow' . $strVars];
				$strStatusRowCash = $varsComment['strStatusRowCash' . $strVars];
				if ($varsStatus['arrCashDefer'][$value]) {
					$arrImport[] = str_replace("<%replace%>", $value, $strStatusRowCash);
				} else {
					$arrImport[] = str_replace("<%replace%>", $value, $strStatusRow);
				}
			}
			if (!$arrImport) {
				$strImport = $varsComment['strStatusNone'];
			} else {
				$strImport = join(' ', $arrImport);
			}
			$varsStr['strImport'] = $strImport;

			$arrError = array();
			$array = $varsStatus['arrErrorNumRow'];
			foreach ($array as $key => $value) {
				$strStatusRowError = $varsComment['strStatusRowError' . $strVars];
				$strStatusRowError = str_replace("<%numRow%>", $value, $strStatusRowError);
				$str = $varsComment[$varsStatus['arrError'][$key]];
				$arrError[] = str_replace("<%replace%>", $str, $strStatusRowError);
			}
			if (!$arrError) {
				$strError = $varsComment['strStatusNone'];
			} else {
				$strError = join(' ', $arrError);
			}
			$varsStr['strError'] = $strError;

			$arrNone = array();
			$array = $varsStatus['arrNone'];
			foreach ($array as $key => $value) {
				$strStatusRow = $varsComment['strStatusRow' . $strVars];
				$arrNone[] = str_replace("<%replace%>", $value, $strStatusRow);
			}
			if (!$arrNone) {
				$strNone = $varsComment['strStatusNone'];
			} else {
				$strNone = join(' ', $arrNone);
			}
			$varsStr['strNone'] = $strNone;

			if (preg_match("/^banks/", $valueLog['flagType'])) {
				$varsStr['strImportNum'] = $varsStr['strImportNumBanks'];
				$varsStr['strNoneNum'] = $varsStr['strNoneNumBanks'];
			}

			$strHtml .= $this->getHtml(array(
				'vars' => $varsStr,
				'path' => $this->_extSelf['pathTplHtml'],
			));
		}

		return $strHtml;
	}

	/**
		(array(
			'classCalcLogImport' => $classCalcLogImport,
		))
	 */
	protected function _checkVarsCSVData($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;
		global $varsAccount;
		global $varsAccounts;
		$classCalcLogImport = &$arr['classCalcLogImport'];

		$numFiscalPeriodTemp = $this->_getNumFiscalPeriodTemp();

		$varsTag = array();
		$varsType = array();
		$varsCSVs = array();
		$varsRowIds = array();
		$array = $this->_getLogImportRetry(array());
		foreach ($array as $key => $value) {
			$arrOrder = array();
			$varsType[$value['idLogRetry']] = $value['flagType'];
			$arrayColumn = $value['jsonData']['varsColumn'];
			$arrayDetail = $value['jsonData']['varsDetail'];
			foreach ($arrayDetail as $keyDetail => $valueDetail) {
				$rowData = array();
				$numColumn = 0;
				foreach ($arrayColumn as $keyColumn => $valueColumn) {
					if (!$numColumn) {
						$numColumn++;
						continue;
					}
					$rowData[$valueColumn] = $valueDetail['varsDetail'][$keyColumn]['value'];
				}
				$arrOrder[$valueDetail['id'] - 1] = $rowData;
			}
			$varsCSV = $classCalcLogImport->allot(array(
				'flagStatus'          => 'check',
				'arrOrder'            => $arrOrder,
				'idEntity'            => $varsPluginAccountingAccount['idEntityCurrent'],
				'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
				'idAccount'           => ($varsAccounts[$value['idAccount']])? $value['idAccount'] : $varsAccount['id'],
			));
			$varsTag[$value['idLogRetry']] = $value['arrSpaceStrTag'];
			$varsCSVs[$value['idLogRetry']] = $varsCSV;
		}

		$data = array(
			'varsCSVs'      => $varsCSVs,
			'varsType'      => $varsType,
			'varsTag'       => $varsTag,
			'varsRetryLog'  => $array,
		);

		return $data;
	}

	/**
		(array(

		))
	 */
	protected function _getLogImportRetry($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogImportRetry' . $strNation,
			'arrLimit' => array(),
			'arrOrder' => array(
				'strColumn' => 'id',
				'flagDesc'  => 1,
			),
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

			),
		));

		return $rows['arrRows'];
	}
}
