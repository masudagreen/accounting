<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_LogImportItem extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference' => 'logWindow',
		'idImport'     => 'logImport',
		'idRetry'      => 'logRetry',
		'idCash'       => 'cashWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/logImportItem.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/logImportItem.php',
		'varsIframe'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/tmplIframe.php',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;
		global $classCheck;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

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

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 0,
			'flagAuthority' => 'insert',
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

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$vars['portal']['varsDetail']['varsDetail'] = $this->_updateVarsDetail((array(
			'vars' => &$vars,
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
			'vars'             => $vars,
			'varsItem'         => $arr['varsItem'],
		))
	 */
	protected function _updateVarsDetail($arr)
	{
		$vars = &$arr['vars'];

		$arrayNew = array();
		$array = &$vars['portal']['varsDetail']['templateDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'DummyEditCurrent') {
				if ($this->_checkCurrent()) {
					continue;
				}
			}
			$method = '_updateVarsDetail' . $value['id'];
			if (method_exists($this, $method)) {
				$value = $this->$method(array(
					'value'    => $value,
					'vars'     => $vars,
				));
			}
			$arrayNew[] = $value;
		}

		$vars['portal']['varsDetail']['templateDetail'] = $arrayNew;

		return $vars['portal']['varsDetail']['templateDetail'];
	}

	/**
		(array(
			'vars'             => $vars,
		))
	 */
	protected function _updateVars($arr)
	{
		global $varsRequest;
		global $classCheck;

		if (!$this->_checkCurrent()) {
			$arr['vars']['portal']['varsDetail']['varsBtn'] = array();
			$arr['vars']['portal']['varsDetail']['varsStart']['varsEdit'] = array();
			$arr['vars']['portal']['varsDetail']['form']['varsEdit'] = array();

		} else {
			$flag = $this->_checkAccess(array(
				'flagAllUse'    => 1,
				'flagAuthority' => 'select',
				'idTarget'      => $this->_extSelf['idImport'],
			));
			if (!$flag) {
				$arr['vars']['portal']['varsDetail']['varsStart']['varsEdit']['flagPreferenceUse'] = 0;
				$arrayNew = array();
				$array = &$arr['vars']['portal']['varsDetail']['varsEnd']['varsBtn'];
				foreach ($array as $key => $value) {
					if ($value['id'] == 'Retry') {
						continue;
					}
					$arrayNew[] = $value;
				}
				$array = $arrayNew;
				$arr['vars']['portal']['varsDetail']['varsEdit']['flagPreferenceUse'] = 0;
				$arr['vars']['portal']['varsDetail']['form']['varsEdit']['flagPreferenceUse'] = 0;
			}
		}

		return $arr['vars'];
	}

	/**
	 *
	 */
	protected function _iniDetailAdd()
	{
		global $classDb;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		global $varsRequest;
		global $varsAccount;

		$dbh = $classDb->getHandle();

		if (!$this->_checkCurrent()) {
			$this->_sendMessage(array('flag' => 40));
		}

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllInsert'] || $varsAuthority['flagMyInsert'])) {
			$this->_sendMessage(array('flag' => 40));
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$arrFile = $this->_checkFileValue(array(
			'vars' => $vars,
		));

		$classCalcLogImport = $this->_getClassCalc(array('flagType' => 'LogImport'));
		$classCalcCashPay = $this->_getClassCalc(array('flagType' => 'CashPay'));
		$classCalcCash = $this->_getClassCalc(array('flagType' => 'Cash'));
		$classCalcLog = $this->_getClassCalc(array('flagType' => 'Log'));

		$arrayData = array();
		$array = &$arrFile;
		foreach ($array as $key => $value) {
			$arrayData[] = $this->_checkVarsCSV(array(
				'strTitle'           => $value['strTitle'] . '.' . $value['strFileType'],
				'strUrl'             => $value['strUrl'],
				'vars'               => $vars,
				'classCalcLogImport' => $classCalcLogImport,
			));
		}

		$flagCashInsert = $this->_checkAccess(array(
			'flagAllUse'    => 0,
			'flagAuthority' => 'insert',
			'idTarget'      => $this->_extSelf['idCash'],
		));

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		$numFiscalPeriodTemp = $this->_getNumFiscalPeriodTemp();
		$arrConvertError = 0;
		try {
			$dbh->beginTransaction();

			$arrVarsLog = array();
			$arrVarsLogTemp = array();
			foreach ($arrayData as $keyData => $valueData) {
				$data = $valueData;
				$arrayLog = $data['arrayRequests'];
				foreach ($arrayLog as $keyLog => $valueLog) {
					$valueLog['arrSpaceStrTag'] = $data['strTitle'];

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
								$this->_sendVars(array(
									'flagIframe' => 1,
									'flag'       => $flagVars,
									'stamp'      => $this->getStamp(),
									'numNews'    => $this->getNumNews(),
									'vars'       => array(),
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
								$this->_sendVars(array(
									'flagIframe' => 1,
									'flag'       => $flagVars,
									'stamp'      => $this->getStamp(),
									'numNews'    => $this->getNumNews(),
									'vars'       => array(),
								));
							}

						} elseif ($flagCashVars['flag'] == 'caution') {
							$numberRow = $valueLog['id'];
							$arrayData[$keyData]['varsStatus']['arrCashDefer'][$numberRow] = 1;
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
					$valueLog['arrSpaceStrTag'] = $data['strTitle'];
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
								$this->_sendVars(array(
									'flagIframe' => 1,
									'flag'       => $flagVars,
									'stamp'      => $this->getStamp(),
									'numNews'    => $this->getNumNews(),
									'vars'       => array(),
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
								$this->_sendVars(array(
									'flagIframe' => 1,
									'flag'       => $flagVars,
									'stamp'      => $this->getStamp(),
									'numNews'    => $this->getNumNews(),
									'vars'       => array(),
								));
							}

						} elseif ($flagCashVars['flag'] == 'caution') {
							$numberRow = $valueLog['id'];
							$arrayData[$keyData]['varsStatus']['arrCashDefer'][$numberRow] = 1;
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
					$arrayData[$keyData]['flagConvertError'] = $classCalcLogImport->allot(array(
						'flagStatus'          => 'insertDbRetry',
						'flagType'            => 'item',
						'arrSpaceStrTag'      => $data['strTitle'],
						'vars'                => $data['varsRetry'],
						'idEntity'            => $varsPluginAccountingAccount['idEntityCurrent'],
						'idAccount'           => $varsAccount['id'],
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
					'numFiscalPeriod' => $numFiscalPeriodTemp,
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
					'numFiscalPeriod' => $numFiscalPeriodTemp,
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
			}

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$varsData = array();
		foreach ($arrayData as $keyData => $valueData) {
			$data = $valueData;
			unlink($data['strUrl']);
			$arrStatusLog = $this->_getVarsStatus(array(
				'vars'       => $vars,
				'varsStatus' => $data['varsStatus'],
			));
			$arrStatusLog['strTitle'] = $data['strTitle'];
			$arrStatusLog['flagConvertError'] = $data['flagConvertError'];
			$varsData[] = $arrStatusLog;
		}
		$this->_sendMessage(array('flag' => 1, 'vars' => $varsData));
	}

	/**
		(array(
			'vars'       => $vars,
			'varsStatus' => $data['varsStatus'],
		))
	 */
	protected function _getVarsStatus($arr)
	{
		$varsComment = $arr['vars']['varsItem']['varsComment'];

		$data = array();

		$data['replaceAll'] = $arr['varsStatus']['numAll'];

		$data['replaceAllImport'] = count($arr['varsStatus']['arrImport']);
		$data['replaceAllNone'] = count($arr['varsStatus']['arrNone']);
		$data['replaceAllError'] = count($arr['varsStatus']['arrError']);

		$arrImport = array();
		$array = $arr['varsStatus']['arrImport'];
		foreach ($array as $key => $value) {
			$strStatusRow = $varsComment['strStatusRow'];
			$strStatusRowCash = $varsComment['strStatusRowCash'];
			if ($arr['varsStatus']['arrCashDefer'][$value]) {
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
		$data['replaceImport'] = $strImport;

		$arrError = array();
		$array = $arr['varsStatus']['arrError'];
		foreach ($array as $key => $value) {
			$strStatusRowError = $varsComment['strStatusRowError'];
			$arrError[] = str_replace("<%replace%>", $value, $strStatusRowError);
		}
		if (!$arrError) {
			$strError = $varsComment['strStatusNone'];
		} else {
			$strError = join(' ', $arrError);
		}
		$data['replaceError'] = $strError;

		$arrNone = array();
		$array = $arr['varsStatus']['arrNone'];
		foreach ($array as $key => $value) {
			$strStatusRow = $varsComment['strStatusRow'];
			$arrNone[] = str_replace("<%replace%>", $value, $strStatusRow);
		}
		if (!$arrNone) {
			$strNone = $varsComment['strStatusNone'];
		} else {
			$strNone = join(' ', $arrNone);
		}
		$data['replaceNone'] = $strNone;

		return $data;
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

		if	(!$this->_checkCurrent()) {
			$this->_sendMessage(array('flag' => 40));
		}

		$varsAuthority = $this->_getVarsAuthority(array());
		if (is_null($varsAuthority)) {
			$this->_sendMessage(array('flag' => 40));
		}

		$arrFile = $this->_checkValueFile(array(
			'vars' => $arr['vars'],
		));

		$id = $varsRequest['query']['idTag'];

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$numYear = date('Y');
		$numMonth = date('m');

		$path = PATH_BACK_DAT_TEMP;
		if (!is_dir($path)) {
			mkdir($path);
		}

		$array = &$arrFile;
		foreach ($array as $key => $value) {
			$strFileName = hash('sha256', $value['strTitle'] . '_' . $key . '_' . MICROTIMESTAMP). '.' . $value['strFileType'] . '.cgi';
			$strUrl = $path . $strFileName;
			if (!move_uploaded_file($value['tmpName'], $strUrl)) {
				$this->_sendMessage(array('flag' => 'dummy', 'vars' => $arr['vars']['varsItem']['varsComment']['strUploadError']));
			}
			`nkf -wLu --overwrite $strUrl`;
			$array[$key]['strUrl'] = $strUrl;
			$array[$key]['strTitle'] = $classEscape->to(array( 'data' => $value['strTitle']));
		}

		return $array;
	}

	/**

	 */
	protected function _checkValueFile()
	{
		global $classEscape;
		global $varsRequest;

		$id = $varsRequest['query']['idTag'];

		$arrFile = array();
		$array = $_FILES[$id]['name'];
		foreach ($array as $key => $value) {
			$value = $classEscape->to(array( 'data' => $value ));
			$strFileType = strtolower($classEscape->getFileType(array('strUrl' => $value)));
			if ($strFileType != 'csv') {
				$this->_sendMessage(array('flag' => 'dummy', 'vars' => $arr['vars']['varsItem']['varsComment']['strUploadError']));
			}
			$strFileType = preg_quote($strFileType);
			$strFileType = str_replace('/', '\/', $strFileType);
			preg_match("/^(.*?)\.$strFileType$/i", $value, $arrMatch);
			list($dummy, $strTitle) = $arrMatch;
			$numByte = $_FILES[$id]['size'][$key];
			if ($numByte > NUM_MAX_UPLOAD_SIZE) {
				$this->_sendMessage(array('flag' => 'dummy', 'vars' => $arr['vars']['varsItem']['varsComment']['strUploadSize']));
			}
			if ($_FILES[$id]['error'][$key]) {
				$this->_sendMessage(array('flag' => 'dummy', 'vars' => $arr['vars']['varsItem']['varsComment']['strUploadError']));
			}
			$data = array(
				'strTitle'    => $strTitle,
				'tmpName'     => $_FILES[$id]['tmp_name'][$key],
				'strFileType' => $strFileType,
				'numByte'     => $numByte,
			);
			$arrFile[] = $data;
		}

		return $arrFile;
	}

	/**
			'arrValueFile' => $arrValueFile,
			'vars'         => $vars,
			'varsFlag'     => $varsFlag,
	 */
	protected function _checkVarsCSV($arr)
	{
		global $varsRequest;
		global $varsAccount;
		global $varsPluginAccountingAccount;

		global $classFile;
		global $classEscape;

		$arrayCSV = $classFile->getArray(array(
			'path' => $arr['strUrl'],
		));

		$array = $arrayCSV;
		foreach ($array as $key => $value) {
			$arrayCSV[$key] = $classEscape->to(array( 'data' => $arrayCSV[$key] ));
		}

		file_put_contents($arr['strUrl'], $arrayCSV);

		$arrayCSV = $classFile->getCsvRows(array(
			'path' => $arr['strUrl'],
		));

		$numFiscalPeriodTemp = $this->_getNumFiscalPeriodTemp();

		$classCalcLogImport = &$arr['classCalcLogImport'];
		$varsCSV = $classCalcLogImport->allot(array(
			'flagStatus'          => 'check',
			'arrOrder'            => $arrayCSV,
			'idEntity'            => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
			'idAccount'           => $varsAccount['id'],
		));

		$varsComment = $arr['vars']['varsItem']['varsComment'];
		$arrError = &$varsCSV['varsStatus']['arrError'];
		$array = $varsCSV['varsStatus']['arrErrorNumRow'];
		foreach ($array as $key => $value) {
			$numRow = $value;
			$strStatus = $varsComment['strStatus'];
			$strStatus = str_replace("<%replace%>", $numRow, $strStatus);
			$arrError[$key] = $strStatus . $varsComment[$arrError[$key]];
		}

		$data = array(
			'strUrl'               => $arr['strUrl'],
			'strTitle'             => $arr['strTitle'],
			'arrayRequests'        => $varsCSV['arrayRequests'],
			'arrayRequestsTemp'    => $varsCSV['arrayRequestsTemp'],
			'varsStatus'           => $varsCSV['varsStatus'],
			'varsRetry'            => $varsCSV['varsRetry'],
		);

		return $data;
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
