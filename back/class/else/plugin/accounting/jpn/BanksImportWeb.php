<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_BanksImportWeb extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference' => 'banksWindow',
		'idLog'        => 'logWindow',
		'idCash'       => 'cashWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/banksImportWeb.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/banksImportWeb.php',
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
		$classCalcBanks = $this->_getClassCalc(array('flagType' => 'Banks'));

		$varsCheck = array();
		$array = $arr['varsItem']['varsBanksAccountList']['arrSelectTag'];
		foreach ($array as $key => $value) {
			$idLogAccount = $value['value'];
			if ($arr['varsItem']['varsBanksAccountList']['arrStrTitle'][$idLogAccount]['flagLock']) {
				$value['flagDisabled'] = 1;
				$value['strTitle'] .= ' - ' . $arr['value']['varsTmpl']['strLock'];
				$varsCheck[] = 1;
				$arr['value']['arrayOption'][] = $value;
				continue;
			}

			$varsBanksAccount = $arr['varsItem']['varsBanksAccountList']['arrStrTitle'][$idLogAccount];
			$varsBanks =  $arr['varsItem']['varsBanksList'][$varsBanksAccount['flagBank']];

			$flagVars = $classCalcBanks->allot(array(
				'flagStatus'       => 'checkVarsAttest',
				'flagBank'         => $varsBanksAccount['flagBank'],
				'varsBanksAccount' => $varsBanksAccount,
				'varsBanks'        => $varsBanks,
			));

			if ($flagVars['flag']) {
				$value['flagDisabled'] = 1;
				$value['strTitle'] .= ' - ' . $arr['value']['varsTmpl']['strAttest'];
			}
			if (!$value['flagDisabled']) {
				$varsCheck[] = 1;
			}
			$arr['value']['arrayOption'][] = $value;
		}

		$array = &$arr['value']['arrayOption'];
		foreach ($array as $key => $value) {
			if ($value['value'] == 'all') {
				if (count($varsCheck) == 0) {
					$array[$key]['flagDisabled'] = 1;
				}
				break;
			}
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
	protected function _iniDetailSave()
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		$classTime = new Code_Else_Lib_Time();

		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;
		global $varsAccount;
		global $varsRequest;

		if (!$this->_checkCurrent()) {
			$this->_sendOld();
		}

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllInsert'] || $varsAuthority['flagMyInsert'])) {
			$this->_sendOld();
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsFlag = array();
		$varsFlag['idLogAccount'] = $varsRequest['query']['jsonValue']['vars']['IdLogAccount'];

		$classCalcBanks = $this->_getClassCalc(array('flagType' => 'Banks'));
		$classCalcBanksImport = $this->_getClassCalc(array('flagType' => 'BanksImport'));

		$varsItem = $classCalcBanks->allot(array(
			'flagStatus'      => 'varsItem',
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$this->_checkWebValue(array(
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		));
		$numFiscalPeriodTemp = $this->_getNumFiscalPeriodTemp();

		$arrWeb = array();
		if ($varsFlag['idLogAccount'] == 'all') {
			$arrWeb = $varsItem['varsBanksAccountList']['arrStrTitle'];

		} else {
			$arrWeb[$varsFlag['idLogAccount']] = $varsItem['varsBanksAccountList']['arrStrTitle'][$varsFlag['idLogAccount']];
		}

		$varsDataResponse = $this->_getVarsDataResponse(array(
			'varsItem'       => $varsItem,
			'arrWeb'         => $arrWeb,
			'classCalcBanks' => $classCalcBanks,
		));
		$arrayDataCSV = $varsDataResponse['arrayCSVs'];

		$arrayDataBanks = array();
		$array = $arrayDataCSV;
		foreach ($array as $key => $value) {
			$varsFlag['idLogAccount'] = $key;
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
				'flagType'            => 'banksWeb',
				'flagWeb'             => $value['flagWeb'],
				'strComment'          => $value['strComment'],
				'arrayCSV'            => $value['arrayCSV'],
				'strTitle'            => $value['strTitle'],
				'strUrl'              => '',
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
						'flagType'            => 'banksWeb',
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

		$flagImport = 0;
		$flagRetry = 0;
		$flagLog = 0;
		$strComment = '';
		foreach ($arrayDataBanks as $keyDataBanks => $valueDataBanks) {
			$data = $valueDataBanks;
			$tplHtml = '';
			if ($data['flagWeb'] == 'success') {
				$arrStatus = $classCalcBanksImport->allot(array(
					'flagStatus'      => 'varsStatusBanks',
					'flagType'        => 'banksWeb',
					'varsStatus'      => $data['varsStatus'],
					'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				));
				$arrStatus['strTitle'] = $data['strTitle'];
				$tplHtml = $varsItem['varsOption']['varsComment']['strStatusBanks'];
				if ($arrStatus['replaceAllImport']) {
					$flagImport = 1;
				}

			} else {
				$arrStatus = array();
				$arrStatus['strTitle'] = $data['strTitle'];
				$arrStatus['strReason'] = $vars['varsItem'][$data['flagWeb']];
				if (!$arrStatus['strReason']) {
					$arrStatus['strReason'] = $vars['varsItem']['dataError'];
				}
				if ($data['strComment']) {
					$arrStatus['strReason'] = $data['strComment'];
				}
				$tplHtml = $varsItem['varsOption']['varsComment']['strWebError'];
			}
			$array = $arrStatus;
			foreach ($array as $key => $value) {
				$str = '#{' . $key . '}';
				$tplHtml = str_replace($str, $value, $tplHtml);
			}
			$strComment .= $tplHtml;
		}

		foreach ($arrayDataLog as $keyDataLog => $valueDataLog) {
			$data = $valueDataLog;
			$arrStatus = $classCalcBanksImport->allot(array(
				'flagStatus'      => 'varsStatusLog',
				'varsStatus'      => $data['varsStatus'],
				'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			));
			if ($arrStatus['replaceAllNone']) {
				$flagRetry = 1;
			}
			if ($arrStatus['replaceAllImport']) {
				$flagLog = 1;
			}
			$arrStatus['strTitle'] = $data['strTitle'];
			$tplHtml = $varsItem['varsOption']['varsComment']['strStatusLog'];
			$array = $arrStatus;
			foreach ($array as $key => $value) {
				$str = '#{' . $key . '}';
				$tplHtml = str_replace($str, $value, $tplHtml);
			}
			$strComment .= '<br>' . $tplHtml;
		}


		$this->sendVars(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'strComment' => $strComment,
				'flagImport' => $flagImport,
				'flagRetry'  => $flagRetry,
				'flagLog'    => $flagLog,
			),
		));
	}


	/**
		(array(
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		))
	 */
	protected function _checkWebValue($arr)
	{
		global $varsPluginAccountingPreference;

		$varsAuthority = $this->_getVarsAuthority(array());

		if ($varsPluginAccountingPreference['accessCode'] == '') {
			$this->sendVars(array(
				'flag'    => ($varsAuthority == 'admin')? 'accessCodeAdmin' : 'accessCode',
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}

		if (!$arr['varsItem']['varsBanksAccountList']['arrStrTitle']) {
			$this->_sendOld();
		}

		if ($arr['varsFlag']['idLogAccount'] == '') {
			$this->_sendOld();

		} elseif ($arr['varsFlag']['idLogAccount'] != 'all') {
			if (!$arr['varsItem']['varsBanksAccountList']['arrStrTitle'][$arr['varsFlag']['idLogAccount']]) {
				$this->_sendOld();
			}

			if ($arr['varsItem']['varsBanksAccountList']['arrStrTitle'][$arr['varsFlag']['idLogAccount']]['flagLock']) {
				$this->_sendOld();
			}
		}
	}

	/**
			'varsItem'       => $varsItem,
			'arrWeb'         => $arrWeb,
			'classCalcBanks' => $classCalcBanks,
	 */
	protected function _getVarsDataResponse($arr)
	{
		global $classEscape;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingPreference;

		$classCalcBanks = &$arr['classCalcBanks'];

		$varsCheckId = array();
		$num = 1;
		$arrTemp = array();
		$arrSort = array();
		$array = $arr['arrWeb'];
		foreach ($array as $key => $value) {
			$temp = array();
			$idLogAccount = $key;
			$varsBanksAccount = $arr['varsItem']['varsBanksAccountList']['arrStrTitle'][$idLogAccount];
			if ($arr['varsItem']['varsBanksAccountList']['arrStrTitle'][$idLogAccount]['flagLock']) {
				continue;
			}
			$varsBanks =  $arr['varsItem']['varsBanksList'][$varsBanksAccount['flagBank']];
			$flagVars = $classCalcBanks->allot(array(
				'flagStatus'       => 'checkVarsAttest',
				'flagBank'         => $varsBanksAccount['flagBank'],
				'varsBanksAccount' => $varsBanksAccount,
				'varsBanks'        => $varsBanks,
			));
			if ($flagVars['flag']) {
				continue;
			}

			$temp['stampCheck'] = $varsBanksAccount['stampCheck'];
			$arrSort[] = $temp;
			$temp['varsDetail'] = $flagVars['varsDetail'];
			$temp['flagBank'] = $varsBanksAccount['flagBank'];
			$temp['id'] = $num;
			$arrTemp[] = $temp;
			$varsCheckId[$num] = $idLogAccount;
			$num++;
		}
		array_multisort($arrSort ,SORT_ASC ,$arrTemp);

		$arrayNew = array();
		$num = 0;
		$array = $arrTemp;
		foreach ($array as $key => $value) {
			if ($num == 10) {
				break;
			}
			$num++;
			$arrayNew[] = $value;
		}

		/*
		 * array(
			'accessCode' => '',
			'version'   => '',
			'jsonData' => array(
				array(
					array(
						'id'         => '',
						'stampCheck' => '',
						'flagBank'   => '',
						'varsDetail' => array(),
					),
				),
			),
		 * )
		 */
		$params = array(
			'cache'      => MICROTIMESTAMP,
			'jsonData'   => json_encode($arrayNew),
			'accessCode' => ($varsPluginAccountingPreference['accessCode'])? $varsPluginAccountingPreference['accessCode'] : '',
			'version'    => NUM_VERSION,
		);

		$path = PATH_INFO_SSL . 'banks.php';
		if (FLAG_TEST) {
			$path = 'http://localhost/site/rucaro.org/banks.php';
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $path);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		$output = curl_exec($ch);
		curl_close($ch);
var_dump($output);
exit;


$output = '{"flag":"success","varsData":[{"id":1,"flag":"success","flagBank":"japannetbank","strComment":"","flagErrorDetail":"","strErrorComment":"","arraySign":[],"arrayCSV":[{"strYear":"2011","strMonth":"02","strDate":"24","strHour":"11","strMin":"56","strSec":"14","strNum":"00001","numValueIn":"","numValueOut":"1","flagIn":0,"strTitle":"\u632f\u8fbc \u30bf\u30b1\u30c8\u30df\u3000\u30e8\u30a6\u30ce\u30b9\u30b1","numBalance":"1"},{"strYear":"2013","strMonth":"11","strDate":"25","strHour":"11","strMin":"41","strSec":"26","strNum":"00001","numValueIn":"","numValueOut":"1","flagIn":0,"strTitle":"\u632f\u8fbc \u30bf\u30b1\u30c8\u30df\u3000\u30e8\u30a6\u30ce\u30b9\u30b1","numBalance":"2"},{"strYear":"2014","strMonth":"02","strDate":"04","strHour":"11","strMin":"08","strSec":"05","strNum":"00001","numValueIn":"","numValueOut":"6740","flagIn":0,"strTitle":"\u632f\u8fbc \u30e4\u30d5\uff0d\u30b1\u30c4\u30b5\u30a4","numBalance":"6742"},{"strYear":"2014","strMonth":"02","strDate":"10","strHour":"08","strMin":"32","strSec":"45","strNum":"00001","numValueIn":"","numValueOut":"259","flagIn":0,"strTitle":"\u632f\u8fbc \u30bf\u30b1\u30c8\u30df\u3000\u30e8\u30a6\u30ce\u30b9\u30b1","numBalance":"7001"},{"strYear":"2014","strMonth":"02","strDate":"10","strHour":"14","strMin":"41","strSec":"39","strNum":"00002","numValueIn":"7000","numValueOut":"","flagIn":1,"strTitle":"\u30bb\u30d6\u30f3\uff21\uff34\uff2d","numBalance":"1"},{"strYear":"2014","strMonth":"04","strDate":"28","strHour":"22","strMin":"22","strSec":"40","strNum":"00001","numValueIn":"","numValueOut":"1000","flagIn":0,"strTitle":"\u30bb\u30d6\u30f3\uff21\uff34\uff2d","numBalance":"1001"},{"strYear":"2014","strMonth":"04","strDate":"28","strHour":"22","strMin":"24","strSec":"09","strNum":"00002","numValueIn":"1000","numValueOut":"","flagIn":1,"strTitle":"\u30bb\u30d6\u30f3\uff21\uff34\uff2d","numBalance":"1"}]}]}';

		$varsResponse = json_decode($output, true);
		if (is_null($varsResponse)) {
			$this->sendVars(array(
				'flag'    => 'dataError',
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}

		if ($varsResponse['flag'] != 'success') {
			$this->sendVars(array(
				'flag'    => $varsResponse['flag'],
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(
					'strComment' => $varsResponse['varsData']['strComment']
				),
			));
		}

		/*
		array(
			'flag'     => '',
			'varsData' => array(
				array(
					'id'        => '',
					'flag'      => '',
					'strComment'=> '',
					'arraySign' => array(),
					'arrayCSV'  => array(
						array(
							'stampBook'      => '',
							'strTitle'       => '',
							'flagIn'         => '',
							'numValueIn'     => '',
							'numValueOut'    => '',
							'numBalance'     => '',
							'arrSpaceStrTag' => '',
						),
					),
				),
			),
		)

		array(
			'flag'     => 'flagErrorComment',
			'varsData' => array(
				'strComment' => ''
			),
		)
		 */

		$arrayCSVs = array();
		$arrayData = $varsResponse['varsData'];
		foreach ($arrayData as $keyData => $valueData) {
			$idLogAccount = $varsCheckId[$valueData['id']];
			$varsBanksAccount = $arr['varsItem']['varsBanksAccountList']['arrStrTitle'][$idLogAccount];
			$arrayCSV = array();
			$array = $valueData['arrayCSV'];
			foreach ($array as $key => $value) {
				$arrayLoop = $value;
				foreach ($arrayLoop as $keyLoop => $valueLoop) {
					$arrayCSV[$key][$keyLoop] = $classEscape->to(array( 'data' => $valueLoop));
					$arrayCSV[$key][$keyLoop] = $classEscape->strUnique(array( 'data' => $valueLoop));
				}
			}
			$data = array(
				'flagWeb'      => $valueData['flag'],
				'strComment'   => $valueData['strComment'],
				'idLogAccount' => $idLogAccount,
				'strTitle'     => $varsBanksAccount['strTitleAccount'],
				'arrayCSV'     => $arrayCSV,
			);
			$arrayCSVs[$varsBanksAccount['idLogAccount']] = $data;
		}

		$dataTemp = array(
			'arrayCSVs' => $arrayCSVs,
		);

		return $dataTemp;
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

}
