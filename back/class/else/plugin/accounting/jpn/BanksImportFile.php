<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_BanksImportFile extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference' => 'banksWindow',
		'idLog'        => 'logWindow',
		'idCash'       => 'cashWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/banksImportFile.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/banksImportFile.php',
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

		$classCalcBanks = $this->_getClassCalc(array('flagType' => 'Banks'));
		$varsItem = $classCalcBanks->allot(array(
			'flagStatus'      => 'varsItem',
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$vars['portal']['varsDetail']['varsDetail'] = $this->_updateVarsDetail((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
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
					'varsItem' => $arr['varsItem'],
				));
			}
			$arrayNew[] = $value;
		}

		$vars['portal']['varsDetail']['templateDetail'] = $arrayNew;

		return $vars['portal']['varsDetail']['templateDetail'];
	}

	/**
		(array(
			'value'    => $value,
			'vars'     => $vars,
			'varsItem' => $varsItem,
		))
	 */
	protected function _updateVarsDetailIdLogAccount($arr)
	{
		$array = $arr['varsItem']['varsBanksAccountList']['arrSelectTag'];
		foreach ($array as $key => $value) {
			$arr['value']['arrayOption'][] = $value;
		}

		return $arr['value'];
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
			$flagLogAll = $this->_checkAccess(array(
				'flagAllUse'    => 1,
				'flagAuthority' => 'select',
				'idTarget'      => $this->_extSelf['idLog'],
			));
			$flagLog = $this->_checkAccess(array(
				'flagAllUse'    => 0,
				'flagAuthority' => 'select',
				'idTarget'      => $this->_extSelf['idLog'],
			));

			if (!$arr['varsItem']['varsPreference']['flagAutoImport']) {
				$arrayNew = array();
				$array = $arr['vars']['portal']['varsDetail']['varsEnd']['varsBtn'];
				foreach ($array as $key => $value) {
					if ($value['id'] == 'Retry') {
						if (!$flagLogAll) {
							continue;
						}

					} elseif ($value['id'] == 'Log') {
						if (!$flagLog) {
							continue;
						}
					}
					$arrayNew[] = $value;
				}
				$arr['vars']['portal']['varsDetail']['varsEnd']['varsBtn'] = $arrayNew;
			}
		}

		$array = $arr['varsItem'];
		foreach ($array as $key => $value) {
			$arr['vars']['varsItem'][$key] = $value;
		}

		return $arr['vars'];
	}

	/**
	 *
	 */
	protected function _iniDetailAdd()
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		$classTime = new Code_Else_Lib_Time();

		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;
		global $varsAccount;
		global $varsRequest;

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

		$varsFlag = array();
		$varsFlag['idLogAccount'] = $varsRequest['query']['IdLogAccount'];

		$classCalcBanks = $this->_getClassCalc(array('flagType' => 'Banks'));
		$classCalcBanksImport = $this->_getClassCalc(array('flagType' => 'BanksImport'));

		$varsItem = $classCalcBanks->allot(array(
			'flagStatus'      => 'varsItem',
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrFile = $this->_checkFileValue(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		));
		$numFiscalPeriodTemp = $this->_getNumFiscalPeriodTemp();

		$arrayDataCSV = array();
		$array = &$arrFile;
		foreach ($array as $key => $value) {
			$arrayDataCSV[] = $this->_getVarsCSV(array(
				'strUrl'         => $value['strUrl'],
				'strTitle'       => $value['strTitle'] . '.' . $value['strFileType'],
				'varsItem'       => $varsItem,
				'varsFlag'       => $varsFlag,
				'classCalcBanks' => $classCalcBanks,
			));
		}

		$arrayDataBanks = array();
		$array = $arrayDataCSV;
		foreach ($array as $key => $value) {
			$varsBanksAccount = $varsItem['varsBanksAccountList']['arrStrTitle'][$varsFlag['idLogAccount']];
			$stamp = $varsBanksAccount['stampCheck'];
			$strTime = '-';
			if ($stamp) {
				$strTime = $classTime->getDisplay(array(
					'stamp'    => $stamp,
					'flagType' => 'year/date',
				));
			}
			$arrayDataBanks[] = $classCalcBanksImport->allot(array(
				'flagStatus'          => 'checkVarsCSVBanks',
				'flagType'            => 'banksFile',
				'arrayCSV'            => $value['arrayCSV'],
				'strTitle'            => $value['strTitle'],
				'strUrl'              => $value['strUrl'],
				'strTime'             => $strTime,
				'idLogAccount'        => $varsFlag['idLogAccount'],
				'flagBank'            => $varsBanksAccount['flagBank'],
				'classCalcBanks'      => $classCalcBanks,
				'idEntity'            => $varsPluginAccountingAccount['idEntityCurrent'],
				'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
				'idAccount'           => $varsAccount['id'],
			));
		}

		$flagCashInsert = $this->_checkAccess(array(
			'flagAllUse'    => 0,
			'flagAuthority' => 'insert',
			'idTarget'      => $this->_extSelf['idCash'],
		));

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());

		try {
			$dbh->beginTransaction();

			/*
			'arrayCSV'        => array(),
			'arrayCSVTemp'    => array(),
			'arrLogBanks'     => array(),
			'arrLogBanksTemp' => array(),
			 */
			$flagVarsAdd = $classCalcBanksImport->allot(array(
				'flagStatus'          => 'runAdd',
				'arrayDataBanks'      => $arrayDataBanks,
				'idEntity'            => $varsPluginAccountingAccount['idEntityCurrent'],
				'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
				'idAccount'           => $varsAccount['id'],
				'classCalcBanks'      => $classCalcBanks,
			));

			$arrayDataLog = array();
			if ($varsItem['varsPreference']['flagAutoImport']) {
				$arrayLoop = array($flagVarsAdd['arrayCSV'], $flagVarsAdd['arrayCSVTemp']);
				foreach ($arrayLoop as $keyLoop => $valueLoop) {
					if (!$valueLoop['arrayCSV']) {
						continue;
					}
					$arrayDataLog = array();
					$array = $valueLoop['arrayCSV'];
					foreach ($array as $key => $value) {
						$arrayDataLog[] = $classCalcBanksImport->allot(array(
							'flagStatus'          => 'checkVarsCSVLog',
							'arrayCSV'            => $value,
							'strTitle'            => $valueLoop['strTitle'][$key],
							'flagTemp'            => $valueLoop['flagTemp'][$key],
							'idEntity'            => $varsPluginAccountingAccount['idEntityCurrent'],
							'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
							'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
							'idAccount'           => $varsAccount['id'],
						));
					}

					/*
						'arrVarsLog'          => array(),
						'arrVarsLogTemp'      => array(),
						'arrVarsLogWrite'     => array(),
						'arrVarsLogWriteTemp' => array(),
						'arrayData'           => $arr['arrayData'],
					 */
					$flagVarsAddLog = $classCalcBanksImport->allot(array(
						'flagStatus'          => 'runAddLog',
						'flagType'            => 'banksFile',
						'arrayData'           => $arrayDataLog,
						'varsItem'            => $varsItem,
						'idEntity'            => $varsPluginAccountingAccount['idEntityCurrent'],
						'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
						'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
						'flagCurrentFlagNow'  => $flagCurrentFlagNow,
						'idAccount'           => $varsAccount['id'],
						'flagCashInsert'      => $flagCashInsert,
					));
					$arrayDataLog = $flagVarsAddLog['arrayData'];

					if ($flagVarsAddLog['flag'] == 'errorDataMax') {
						$this->_sendVars(array(
							'flagIframe' => 1,
							'flag'       => $flagVarsAddLog['flag'],
							'stamp'      => $this->getStamp(),
							'numNews'    => $this->getNumNews(),
							'vars'       => array(),
						));
					}

					$flagVarsWriteHistory = $classCalcBanksImport->allot(array(
						'flagStatus'          => 'runWriteHistory',
						'arrayData'           => $flagVarsAddLog['arrayData'],
						'arrLogBanks'         => $flagVarsAdd['arrLogBanks'],
						'arrLogBanksTemp'     => $flagVarsAdd['arrLogBanksTemp'],
						'arrVarsLogWrite'     => $flagVarsAddLog['arrVarsLogWrite'],
						'arrVarsLogWriteTemp' => $flagVarsAddLog['arrVarsLogWriteTemp'],
						'classCalcBanks'      => $classCalcBanks,
						'idEntity'            => $varsPluginAccountingAccount['idEntityCurrent'],
						'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
						'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
						'idAccount'           => $varsAccount['id'],
					));
					if ($flagVarsWriteHistory['flag'] == 'errorDataMax') {
						$this->_sendVars(array(
							'flagIframe' => 1,
							'flag'       => $flagVarsWriteHistory['flag'],
							'stamp'      => $this->getStamp(),
							'numNews'    => $this->getNumNews(),
							'vars'       => array(),
						));
					}
				}
			}

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$varsSend = array();
		foreach ($arrayDataBanks as $keyDataBanks => $valueDataBanks) {
			$data = $valueDataBanks;
			unlink($data['strUrl']);
			$arrStatus = $classCalcBanksImport->allot(array(
				'flagStatus'      => 'varsStatusBanks',
				'varsStatus'      => $data['varsStatus'],
				'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			));
			$arrStatus['strTitle'] = $data['strTitle'];
			$varsSend[] = $arrStatus;
		}

		foreach ($arrayDataLog as $keyDataLog => $valueDataLog) {
			$data = $valueDataLog;
			$arrStatus = $classCalcBanksImport->allot(array(
				'flagStatus'      => 'varsStatusLog',
				'varsStatus'      => $data['varsStatus'],
				'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			));
			$arrStatus['strTitle'] = $data['strTitle'];
			$arrStatus['flagLog'] = 1;
			$arrStatus['flagConvertError'] = (is_null($data['flagConvertError']))? 0 : $data['flagConvertError'];
			$varsSend[] = $arrStatus;
		}
//$varsSend= json_decode('[{"replaceAll":9,"replaceAllImport":9,"replaceAllPass":0,"replaceAllError":0,"replaceImport":"\u3010 1\u884c \u3011 \u3010 2\u884c \u3011 \u3010 3\u884c \u3011 \u3010 4\u884c \u3011 \u3010 5\u884c \u3011 \u3010 6\u884c \u3011 \u3010 7\u884c \u3011 \u3010 8\u884c \u3011 \u3010 9\u884c \u3011","replacePass":"\u8a72\u5f53\u306a\u3057","replaceError":"\u8a72\u5f53\u306a\u3057","strTitle":"nyushukinmeisai_20140404\u5f53\u671f\u306e\u307f.csv"},{"replaceAll":9,"replaceAllImport":1,"replaceAllNone":9,"replaceAllError":0,"replaceImport":"\u8a72\u5f53\u306a\u3057","replaceError":"\u8a72\u5f53\u306a\u3057","replaceNone":"\u3010 123 \u3011 \u3010 124 \u3011 \u3010 125 \u3011 \u3010 126 \u3011 \u3010 127 \u3011 \u3010 128 \u3011 \u3010 129 \u3011 \u3010 130 \u3011 \u3010 131 \u3011","strTitle":"nyushukinmeisai_20140404\u5f53\u671f\u306e\u307f.csv","flagLog":1,"flagConvertError":null}]');

		$this->_sendMessage(array('flag' => 1, 'vars' => $varsSend));
	}


	/**
		(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		))
	 */
	protected function _checkFileValue($arr)
	{
		global $classEscape;

		global $varsRequest;
		global $varsPluginAccountingAccount;

		if	(!$this->_checkCurrent()) {
			$this->_sendMessage(array('flag' => 40));
		}

		$varsAuthority = $this->_getVarsAuthority(array());
		if (is_null($varsAuthority)) {
			$this->_sendMessage(array('flag' => 40));
		}

		if (!$arr['varsItem']['varsBanksAccountList']['arrStrTitle']) {
			$this->_sendMessage(array('flag' => 40));
		}

		if (!$arr['varsItem']['varsBanksAccountList']['arrStrTitle'][$arr['varsFlag']['idLogAccount']]) {
			$this->_sendMessage(array('flag' => 40));
		}

		$arrFile = $this->_checkValueFile(array(
			'varsItem' => $arr['varsItem'],
			'varsFlag' => $arr['varsFlag'],
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
				$this->_sendMessage(array('flag' => 'dummy', 'vars' => $arr['varsItem']['varsOption']['varsComment']['strUploadError']));
			}
			`nkf -wLu --overwrite $strUrl`;
			$array[$key]['strUrl'] = $strUrl;
			$array[$key]['strTitle'] = $classEscape->to(array( 'data' => $value['strTitle']));
		}

		return $array;
	}

	/**
		(array(
			'vars'     => $arr['vars'],
			'varsItem' => $arr['varsItem'],
			'varsFlag' => $arr['varsFlag'],
		))
	 */
	protected function _checkValueFile($arr)
	{
		global $classEscape;
		global $varsRequest;

		$id = $varsRequest['query']['idTag'];

		$varsBanksAccount = $arr['varsItem']['varsBanksAccountList']['arrStrTitle'][$arr['varsFlag']['idLogAccount']];
		$varsBanks = $arr['varsItem']['varsBanksList'][$varsBanksAccount['flagBank']];

		$arrFile = array();
		$array = $_FILES[$id]['name'];
		foreach ($array as $key => $value) {
			$value = $classEscape->to(array( 'data' => $value ));
			$strFileType = strtolower($classEscape->getFileType(array('strUrl' => $value)));

			if ($strFileType != $varsBanks['strFileType']) {
				$temp = str_replace('<%replace%>', $varsBanks['strFileType'], $arr['varsItem']['varsOption']['varsComment']['strFileType']);
				$this->_sendMessage(array('flag' => 'dummy', 'vars' => $temp));
			}

			$strFileType = preg_quote($strFileType);
			$strFileType = str_replace('/', '\/', $strFileType);
			preg_match("/^(.*?)\.$strFileType$/i", $value, $arrMatch);
			list($dummy, $strTitle) = $arrMatch;
			$numByte = $_FILES[$id]['size'][$key];

			if ($numByte > NUM_MAX_UPLOAD_SIZE) {
				$this->_sendMessage(array('flag' => 'dummy', 'vars' => $arr['varsItem']['varsOption']['varsComment']['strUploadSize']));
			}

			if ($_FILES[$id]['error'][$key]) {
				$this->_sendMessage(array('flag' => 'dummy', 'vars' => $arr['varsItem']['varsOption']['varsComment']['strUploadError']));
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

	 */
	protected function _getVarsCSV($arr)
	{
		global $classFile;
		global $classEscape;
		global $varsPluginAccountingAccount;

		$arrayCSV = $classFile->getArray(array(
			'path' => $arr['strUrl'],
		));

		$varsBanksAccount = $arr['varsItem']['varsBanksAccountList']['arrStrTitle'][$arr['varsFlag']['idLogAccount']];

		$classCalcBanks = &$arr['classCalcBanks'];
		$arrayCSV = $classCalcBanks->allot(array(
			'flagStatus'      => 'updateArrayCsv',
			'arrayCSV'        => $arrayCSV,
			'flagBank'        => $varsBanksAccount['flagBank'],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$array = $arrayCSV;
		foreach ($array as $key => $value) {
			$arrayCSV[$key] = $classEscape->to(array( 'data' => $arrayCSV[$key] ));
			$arrayCSV[$key] = $classEscape->strUnique(array( 'data' => $arrayCSV[$key] ));
		}

		file_put_contents($arr['strUrl'], $arrayCSV);

		$arrayCSV = $classFile->getCsvRows(array(
			'path' => $arr['strUrl'],
		));

		$data = array(
			'strUrl'   => $arr['strUrl'],
			'strTitle' => $arr['strTitle'],
			'arrayCSV' => $arrayCSV,
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
