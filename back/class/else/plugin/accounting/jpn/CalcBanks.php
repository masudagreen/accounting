<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcBanks extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extChildSelf = array(
		'varsOption'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/banks.php',
		'pathDir'      => 'back/tpl/vars/else/plugin/accounting/ja/dat/jpn/banks/',
	);

	private $_checkRoad = array();

	/**
	 *
	 */
	public function run()
	{
		if (FLAG_TEST) {
			var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
		}
		exit;
	}

	public function allot($arr)
	{
		$method = '_ini' . ucwords($arr['flagStatus']);
		if (method_exists($this, $method)) {
			return $this->$method($arr);

		} else {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__ . $method);
			}
			exit;
		}
	}

	/**
		 (array(
			'flagStatus'          => 'varsItem',
			'idEntity'            => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		 ))
	 */
	protected function _iniVarsItem($arr)
	{
		$varsItem = $this->_extChildSelf['varsItem'][$arr['idEntity']][$arr['numFiscalPeriod']];
		if (!$varsItem) {
			$varsItem = $this->_getVarsItem(array(
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
				'idEntity'        => $arr['idEntity'],
			));
			$this->_extChildSelf['varsItem'][$arr['idEntity']][$arr['numFiscalPeriod']] = $varsItem;
		}

		return $varsItem;
	}

	/**
		(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $arr['idEntity'],
		))
	 */
	protected function _getVarsItem($arr)
	{
		$varsOption = $this->getVars(array(
			'path' => $this->_extChildSelf['varsOption'],
		));

		$varsBanksList = array();
		$array = scandir($this->_extChildSelf['pathDir']);
		foreach ($array as $key => $value) {
			if ( preg_match( "/^\.{1,2}$/", $value)) {
				continue;
			}
			$path = $this->_extChildSelf['pathDir'] . $value;
			preg_match("/^(.*?)\.php$/", $value, $arrMatch);
			list($str, $id) = $arrMatch;
			$varsBanksList[$id] = $this->getVars(array(
				'path' => $path,
			));
		}

		$array = $this->_getLogBanksAccount(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $arr['idEntity'],
		));
		$varsBanksAccountList = array(
			'arrSelectTag' => array(),
			'arrStrTitle'  => array(),
		);
		foreach ($array as $key => $value) {
			$strTitleAccount = $varsBanksList[$value['flagBank']]['strTitle'] . '(' . $value['strTitle'] . ')';
			$varsBanksAccountList['arrSelectTag'][] = array(
				'strTitle' => $strTitleAccount,
				'value'    => $value['idLogAccount'],
			);
			$varsBanksAccountList['arrStrTitle'][$value['idLogAccount']] = $value;
			$varsBanksAccountList['arrStrTitle'][$value['idLogAccount']]['strTitleAccount'] = $strTitleAccount;
		}

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $arr['idEntity'],
		));

		$varsStampTerm = $this->_getVarsStampTerm(array(
			'varsFlag'         => array(
				'flagFiscalPeriod' => 'f1',
			),
			'varsEntityNation' => $varsEntityNation,
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'idEntity'         => $arr['idEntity'],
		));

		$varsPreference = $this->_getVarsPreference(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $arr['idEntity'],
		));

		$data = array(
			'varsOption'           => $varsOption,
			'varsBanksList'        => $varsBanksList,
			'varsBanksAccountList' => $varsBanksAccountList,
			'varsEntityNation'     => $varsEntityNation,
			'varsStampTerm'        => $varsStampTerm,
			'varsPreference'       => $varsPreference,
		);

		return $data;
	}

	/**
		(array(
		))
	 */
	protected function _getVarsPreference($arr)
	{
		global $classDb;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingBanks',
			'arrLimit' => array(),
			'arrOrder'  => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
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
			),
		));

		return $rows['arrRows'][0];
	}

	/**
		(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $arr['idEntity'],
		))
	 */
	protected function _getLogBanksAccount($arr)
	{
		global $classDb;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogBanksAccount',
			'arrLimit' => array(),
			'arrOrder'  => array(
				'strColumn' => 'id',
				'flagDesc'  => 1,
			),
			'flagAnd'  => 1,
			'arrWhere' => array(
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
			),
		));

		return $rows['arrRows'];
	}

	/**
		(array(
			'flagStatus'      => 'updateArrayCsv',
			'arrayCSV'        => $arrayCSV,
			'flagBank'        => $varsBanksAccount['flagBank'],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		))
	 */
	protected function _iniUpdateArrayCsv($arr)
	{
		$arr['varsItem'] = $this->_iniVarsItem(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $arr['idEntity'],
		));

		$str = ucwords($arr['flagBank']);

		$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/' . PLUGIN_ACCOUNTING_STR_NATION . '/calcBanks/' . $str . ".php";
		$strClass = 'Code_Else_Plugin_Accounting_' . ucwords(PLUGIN_ACCOUNTING_STR_NATION) . '_CalcBanks_' . $str;
		if (!$this->_checkRoad[$path]) {
			require_once($path);
			$this->_checkRoad[$path] = 1;
		}
		$classCall = new $strClass;

		return $classCall->allot($arr);
	}

	/**
		 (array(
			'flagStatus'       => 'checkVarsAttest',
			'flagBank'         => $varsBanksAccount['flagBank'],
			'varsBanksAccount' => $varsBanksAccount,
			'varsBanks'        => $varsBanks,
		 ))
	 */
	protected function _iniCheckVarsAttest($arr)
	{
		$str = ucwords($arr['flagBank']);

		$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/' . PLUGIN_ACCOUNTING_STR_NATION . '/calcBanks/' . $str . ".php";
		$strClass = 'Code_Else_Plugin_Accounting_' . ucwords(PLUGIN_ACCOUNTING_STR_NATION) . '_CalcBanks_' . $str;
		if (!$this->_checkRoad[$path]) {
			require_once($path);
			$this->_checkRoad[$path] = 1;
		}
		$classCall = new $strClass;

		return $classCall->allot($arr);
	}

	/**
		 (array(
			'flagStatus'          => 'check',
			'flagType'            => $arr['flagType'],
			'arrayCSV'            => $arr['arrayCSV'],
			'flagBank'            => $varsBanksAccount['flagBank'],
			'idEntity'            => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'numFiscalPeriodTemp' => $arr['numFiscalPeriodTemp'],
			'idAccount'           => $varsAccount['id'],
			'arrValue'            => array(
				'arrSpaceStrTag' => $arr['strTitle'],
				'idLogAccount'   => $arr['varsFlag']['idLogAccount'],
			),
		 ))
	 */
	protected function _iniCheck($arr)
	{
		$arr['varsItem'] = $this->_iniVarsItem(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $arr['idEntity'],
		));

		$arr['varsItemTemp'] = array();
		if ($arr['numFiscalPeriodTemp']) {
			$arr['varsItemTemp'] = $this->_iniVarsItem(array(
				'numFiscalPeriod' => $arr['numFiscalPeriodTemp'],
				'idEntity'        => $arr['idEntity'],
			));
		}

		$str = ucwords($arr['flagBank']);

		$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/' . PLUGIN_ACCOUNTING_STR_NATION . '/calcBanks/' . $str . ".php";
		$strClass = 'Code_Else_Plugin_Accounting_' . ucwords(PLUGIN_ACCOUNTING_STR_NATION) . '_CalcBanks_' . $str;
		if (!$this->_checkRoad[$path]) {
			require_once($path);
			$this->_checkRoad[$path] = 1;
		}
		$classCall = new $strClass;

		return $classCall->allot($arr);
	}

	/**
	 (array(
		 'valueLog'    => $valueLog,
	 ))
	 */
	protected function _getStrStampBook($arr)
	{
		return $arr['valueLog']['stampBook'];
	}

	/**
		(array(
			'idEntity'        => $arr['idEntity'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'varsItem'        => $varsItem,
			'arrayCSV'        => $arr['arrOrder'],
			'idAccount'       => $arr['idAccount'],
		))
	 */
	protected function _setCheck($arr)
	{
		global $classCheck;

		$varsStatus = array(
			'arrImport'      => array(),
			'arrPassNumRow'  => array(),
			'arrPass'        => array(),
			'arrErrorNumRow' => array(),
			'arrError'       => array(),
			'arrImportTime'  => array(),
			'arrPassTime'    => array(),
			'arrErrorTime'   => array(),
			'numAll'         => count($arr['arrayCSV']),
			'idLogAccount'   => $arr['arrValue']['idLogAccount'],
		);

		if (!$arr['arrayCSV']) {
			$data = array(
				'arrValues'         => array(),
				'arrayCSV'          => array(),
				'arrLogCaution'     => array(),
				'arrValuesTemp'     => array(),
				'arrayCSVTemp'      => array(),
				'arrLogCautionTemp' => array(),
				'varsStatus'        => $varsStatus,
				'arrLogFlagUpdate'  => array(),
			);

			return $data;
		}

		$varsStamp = $this->_getNumFiscalTermStamp(array(
			'varsItem'        => $arr['varsItem'],
			'idEntity'        => $arr['idEntity'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$varsStampTemp = array();
		if ($arr['numFiscalPeriodTemp']) {
			$varsStampTemp = $this->_getNumFiscalTermStamp(array(
				'varsItem'        => $arr['varsItemTemp'],
				'idEntity'        => $arr['idEntity'],
				'numFiscalPeriod' => $arr['numFiscalPeriodTemp'],
			));
		}

		$arrayColumnNew = array();
		$array = reset($arr['arrayCSV']);
		foreach ($array as $key => $value) {
			if (is_null($key) || $key === '') {
				continue;
			}
			$arrayColumnNew[$key] = 1;
		}

		$arrayLogNew = array();
		$arrayLog = $arr['arrayCSV'];
		foreach ($arrayLog as $keyLog => $valueLog) {
			$tempRow = array();
			$arrayColumn = $arrayColumnNew;
			foreach ($arrayColumn as $keyColumn => $valueColumn) {
				$tempRow[$keyColumn] = $valueLog[$keyColumn];
			}
			$arrayLogNew[$keyLog] = $tempRow;
		}

		$stampCheck = $arr['varsItem']['varsBanksAccountList']['arrStrTitle'][$arr['arrValue']['idLogAccount']]['stampCheck'];
		if (is_null($stampCheck)) {
			$stampCheck = 0;
		}
		$arrVarsLog = $this->_getVarsLogBanks(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $arr['idEntity'],
			'idLogAccount'    => $arr['arrValue']['idLogAccount'],
			'stampBook'       => $stampCheck,
		));

		$stampCheckTemp = $arr['varsItemTemp']['varsBanksAccountList']['arrStrTitle'][$arr['arrValue']['idLogAccount']]['stampCheck'];
		if (is_null($stampCheckTemp)) {
			$stampCheckTemp = 0;
		}
		$arrVarsLogTemp = $this->_getVarsLogBanks(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $arr['idEntity'],
			'idLogAccount'    => $arr['arrValue']['idLogAccount'],
			'stampBook'       => $stampCheckTemp,
		));

		$arrValues = array();
		$arrValuesTemp = array();

		$arrValuesTime = array();
		$arrValuesTimeTemp = array();

		$arrayCSV = array();
		$arrayCSVTemp = array();

		$arrayLog = $arrayLogNew;
		foreach ($arrayLog as $keyLog => $valueLog) {
			$numRow = $keyLog + 1;
			$flagCurrentStamp = '';
			$strBook = $this->_getStrStampBook(array(
				'valueLog' => $valueLog,
			));
			if ($strBook != '') {
				$stampBook = $this->_getStampBook(array(
					'strBook'  => $strBook,
				));
				if ($stampBook) {
					if ($varsStamp['stampMin'] <= $stampBook && $stampBook <= $varsStamp['stampMax']) {
						$flagCurrentStamp = 'stampBook';

					} elseif ($varsStampTemp['stampMin'] <= $stampBook && $stampBook <= $varsStampTemp['stampMax']) {
						$flagCurrentStamp = 'stampBookTemp';
					}
				}
			}

			if ($arr['flagType'] == 'banksWeb') {
				if ($flagCurrentStamp == 'stampBook') {
					$temp = $this->_checkValueDetailVars(array(
						'valueLog'   => $valueLog,
						'varsStamp'  => $varsStamp,
						'arrValues'  => $arrValues,
						'arrValuesTime'  => $arrValuesTime,
						'arrayCSV'   => $arrayCSV,
						'varsStatus' => $varsStatus,
						'idAccount'  => $arr['idAccount'],
						'numRow'     => $numRow,
						'varsItem'   => $arr['varsItem'],
						'arrValue'   => $arr['arrValue'],
					));
					$arrValues = $temp['arrValues'];
					$arrValuesTime = $temp['arrValuesTime'];
					$arrayCSV = $temp['arrayCSV'];

				} elseif ($flagCurrentStamp == 'stampBookTemp') {
					$temp = $this->_checkValueDetailVars(array(
						'valueLog'   => $valueLog,
						'varsStamp'  => $varsStampTemp,
						'arrValues'  => $arrValuesTemp,
						'arrValuesTime'  => $arrValuesTimeTemp,
						'arrayCSV'   => $arrayCSVTemp,
						'varsStatus' => $varsStatus,
						'idAccount'  => $arr['idAccount'],
						'numRow'     => $numRow,
						'varsItem'   => $arr['varsItemTemp'],
						'arrValue'   => $arr['arrValue'],
					));
					$arrValuesTemp = $temp['arrValues'];
					$arrValuesTimeTemp = $temp['arrValuesTime'];
					$arrayCSVTemp = $temp['arrayCSV'];

				} else {
					$temp = $this->_setCheckStampBook(array(
						'valueLog'      => $valueLog,
						'varsStamp'     => $varsStamp,
						'varsStampTemp' => $varsStampTemp,
						'varsStatus'    => $varsStatus,
						'numRow'        => $numRow,
					));
				}

			} else {
				if ($flagCurrentStamp == 'stampBook') {
					$temp = $this->_checkValueDetailCSV(array(
						'valueLog'   => $valueLog,
						'varsStamp'  => $varsStamp,
						'arrValues'  => $arrValues,
						'arrValuesTime'  => $arrValuesTime,
						'arrayCSV'   => $arrayCSV,
						'varsStatus' => $varsStatus,
						'idAccount'  => $arr['idAccount'],
						'numRow'     => $numRow,
						'varsItem'   => $arr['varsItem'],
						'arrValue'   => $arr['arrValue'],
					));
					$arrValues = $temp['arrValues'];
					$arrValuesTime = $temp['arrValuesTime'];
					$arrayCSV = $temp['arrayCSV'];

				} elseif ($flagCurrentStamp == 'stampBookTemp') {
					$temp = $this->_checkValueDetailCSV(array(
						'valueLog'   => $valueLog,
						'varsStamp'  => $varsStampTemp,
						'arrValues'  => $arrValuesTemp,
						'arrValuesTime'  => $arrValuesTimeTemp,
						'arrayCSV'   => $arrayCSVTemp,
						'varsStatus' => $varsStatus,
						'idAccount'  => $arr['idAccount'],
						'numRow'     => $numRow,
						'varsItem'   => $arr['varsItemTemp'],
						'arrValue'   => $arr['arrValue'],
					));
					$arrValuesTemp = $temp['arrValues'];
					$arrValuesTimeTemp = $temp['arrValuesTime'];
					$arrayCSVTemp = $temp['arrayCSV'];

				} else {
					$temp = $this->_setCheckStampBook(array(
						'valueLog'      => $valueLog,
						'varsStamp'     => $varsStamp,
						'varsStampTemp' => $varsStampTemp,
						'varsStatus'    => $varsStatus,
						'numRow'        => $numRow,
					));
				}
			}


			$varsStatus = $temp['varsStatus'];
		}

		$tempData = $this->_checkSyncLog(array(
			'arrVarsLog'       => $arrVarsLog,
			'arrValues'        => $arrValues,
			'arrValuesTime'    => $arrValuesTime,
			'arrayCSV'         => $arrayCSV,
			'varsStatus'       => $varsStatus,
			'arrLogFlagUpdate' => array(),
		));
		$data = array(
			'arrValues'         => $tempData['arrValues'],
			'arrayCSV'          => $tempData['arrayCSV'],
			'arrLogCaution'     => $tempData['arrLogCaution'],
			'arrValuesTemp'     => array(),
			'arrayCSVTemp'      => array(),
			'arrLogCautionTemp' => array(),
			'varsStatus'        => $tempData['varsStatus'],
			'arrLogFlagUpdate'  => $tempData['arrLogFlagUpdate'],
		);

		if ($arrValuesTemp) {
			$tempDataTemp = $this->_checkSyncLog(array(
				'arrVarsLog'       => $arrVarsLogTemp,
				'arrValues'        => $arrValuesTemp,
				'arrValuesTime'    => $arrValuesTimeTemp,
				'arrayCSV'         => $arrayCSVTemp,
				'varsStatus'       => $tempData['varsStatus'],
				'arrLogFlagUpdate' => $tempData['arrLogFlagUpdate'],
			));
			$data = array(
				'arrValues'         => $tempData['arrValues'],
				'arrayCSV'          => $tempData['arrayCSV'],
				'arrLogCaution'     => $tempData['arrLogCaution'],
				'arrValuesTemp'     => $tempDataTemp['arrValues'],
				'arrayCSVTemp'      => $tempDataTemp['arrayCSV'],
				'arrLogCautionTemp' => $tempDataTemp['arrLogCaution'],
				'varsStatus'        => $tempDataTemp['varsStatus'],
				'arrLogFlagUpdate'  => $tempDataTemp['arrLogFlagUpdate'],
			);
		}

		ksort($data['varsStatus']['arrPassNumRow']);
		ksort($data['varsStatus']['arrPass']);
		ksort($data['varsStatus']['arrPassTime']);

		return $data;
	}

	/**
		(array(
			'valueLog'   => $valueLog,
			'varsStamp'  => $varsStamp,
			'arrValues'  => $arrValues,
			'varsStatus' => $varsStatus,
			'idAccount'  => $arr['idAccount'],
			'numRow'     => $numRow,
			'arrValue'   => $arr['arrValue'],
		))

		'stampBook',
		'strTitle',
		'flagIn',
		'numValueIn',
		'numValueOut',
		'numBalance',
		'arrSpaceStrTag'

	 */
	protected function _checkValueDetailVars($arr)
	{
		global $classCheck;
		global $classEscape;
		$classTime = new Code_Else_Lib_Time();

		$strStampBook = $arr['valueLog']['stampBook'];
		$stampBook = $this->_getStampBook(array(
			'strBook'  => $strStampBook,
		));
		$strTitle = mb_substr($arr['valueLog']['strTitle'], 0, 100);
		$numValueIn = str_replace(',', '', $arr['valueLog']['numValueIn']);
		$numValueOut = str_replace(',', '', $arr['valueLog']['numValueOut']);
		$numBalance = str_replace(',', '', $arr['valueLog']['numBalance']);
		$strMemo = $arr['arrValue']['arrSpaceStrTag'];
		$strMemo = mb_substr($strMemo, 0, 1000);
		$idLogAccount = $arr['arrValue']['idLogAccount'];
		if ($numValueIn === '') {
			$flagIn = 0;

		} else {
			$flagIn = 1;
		}
		$numValue = ($flagIn)? $numValueIn : $numValueOut;
		$arrSpaceStrTag = array();
		$array = $classEscape->splitSpaceArrayData(array('data' => $strMemo));
		foreach ($array as $key => $value) {
			$arrSpaceStrTag[] = $value;
		}
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));
		if (!$flagIn) {
			$numValueIn = 0;

		} else {
			$numValueOut = 0;
		}

		$flagError = $this->_checkValueDetailError(array(
			'varsItem'     => $arr['varsItem'],
			'varsStamp'    => $arr['varsStamp'],
			'idLogAccount' => $arr['arrValue']['idLogAccount'],
			'strTitle'     => $strTitle,
			'stampBook'    => $stampBook,
			'numValue'     => $numValue,
			'numBalance'   => $numBalance,
		));
		if ($flagError) {
			$arr['varsStatus']['arrErrorNumRow'][] = $arr['numRow'];
			$arr['varsStatus']['arrError'][] = $flagError;
			$arr['varsStatus']['arrErrorTime'][] = $strStampBook;
			return $arr;
		}

		$stampCheck = $arr['varsItem']['varsBanksAccountList']['arrStrTitle'][$arr['arrValue']['idLogAccount']]['stampCheck'];
		if ($stampBook < $stampCheck) {
			$arr['varsStatus']['arrPassNumRow'][$arr['numRow']] = $arr['numRow'];
			$arr['varsStatus']['arrPass'][$arr['numRow']] = 'strPassCheck';
			$arr['varsStatus']['arrPassTime'][$arr['numRow']] = $strStampBook;
			return $arr;
		}

		$arr['varsStatus']['arrImport'][] = $arr['numRow'];
		$arr['varsStatus']['arrImportTime'][] = $strStampBook;

		$arrValue = compact(
			'idLogAccount',
			'stampBook',
			'strTitle',
			'flagIn',
			'numValueIn',
			'numValueOut',
			'numBalance',
			'arrSpaceStrTag'
		);

		$stampBook = $strStampBook;
		$arrayCSV = array();
		$arrayCSV[$arr['varsItem']['varsOption']['varsStr']['stampBook']] = $stampBook;
		$arrayCSV[$arr['varsItem']['varsOption']['varsStr']['numValueIn']] = $numValueIn;
		$arrayCSV[$arr['varsItem']['varsOption']['varsStr']['numValueOut']] = $numValueOut;
		$arrayCSV[$arr['varsItem']['varsOption']['varsStr']['strTitle']] = $strTitle;

		$arr['arrValues'][$arr['numRow']] = $arrValue;
		$arr['arrValuesTime'][$arr['numRow']] = array('strStampBook' => $strStampBook);
		$arr['arrayCSV'][$arr['numRow']] = $arrayCSV;

		return $arr;
	}



	protected function _checkSyncLog($arr)
	{
		global $classCheck;

		$data = array(
			'arrValues'        => array(),
			'arrayCSV'         => array(),
			'arrLogCaution'    => array(),
			'varsStatus'       => $arr['varsStatus'],
			'arrLogFlagUpdate' => $arr['arrLogFlagUpdate'],
		);

		$arrayCheck = array();
		foreach ($arr['arrValues'] as $key => $value) {
			$numRow = $key;
			$flag = 0;
			foreach ($arr['arrVarsLog'] as $keyLog => $valueLog) {
				if ($valueLog['stampBook'] == $value['stampBook']
					&& $valueLog['strTitle'] == $value['strTitle']
					&& $valueLog['flagIn'] == $value['flagIn']
					&& $valueLog['numValueIn'] == $value['numValueIn']
					&& $valueLog['numValueOut'] == $value['numValueOut']
					&& $valueLog['numBalance'] == $value['numBalance']
				) {
					$data['varsStatus']['arrPassNumRow'][$numRow] = $numRow;
					$data['varsStatus']['arrPass'][$numRow] = 'strPass';
					$data['varsStatus']['arrPassTime'][$numRow] = $arr['arrValuesTime'][$key]['strStampBook'];
					$flag = 1;
					if ($valueLog['flagCaution']) {
						$data['arrLogFlagUpdate'][] = $valueLog;
					}

					unset($arr['arrVarsLog'][$keyLog]);
					break;
				}
			}
			if (!$flag) {
				$data['arrValues'][$numRow] = $value;
				$data['arrayCSV'][$numRow] = $arr['arrayCSV'][$numRow];
			}
		}
		if ($arr['arrVarsLog']) {
			$data['arrLogCaution'] = $arr['arrVarsLog'];
		}

		$arrayNew = array();
		$arrayTimeNew = array();
		$array = $arr['varsStatus']['arrImport'];
		foreach ($array as $key => $value) {
			$numRow = $value;
			if (!$data['varsStatus']['arrPassNumRow'][$numRow]) {
				$arrayNew[] = $numRow;
				$arrayTimeNew[] = $arr['varsStatus']['arrImportTime'][$key];
			}
		}
		$data['varsStatus']['arrImport'] = $arrayNew;
		$data['varsStatus']['arrImportTime'] = $arrayTimeNew;

		return $data;
	}


	/**
	 (array(
		 'stampBook'    => $stampBook,
	 ))
	 */
	protected function _getStampZero($arr)
	{
		global $varsAccount;
		global $classTime;

		$arrDate = $classTime->getLocal(array('stamp' => $arr['stamp']));
		$numYear = $arrDate['strYear'];
		$numMonth = $arrDate['strMonth'];
		$numDate = $arrDate['strDate'];

		$strTimeZone = (-1 * $varsAccount['numTimeZone']) . 'hours';
		$dateTime = new DateTime("$numYear-$numMonth-$numDate 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stamp = $dateTime->format('U');

		return $stamp;
	}

	/**
		(array(
			'varsItem'     => $arr['varsItem'],
			'varsStamp'    => $arr['varsStamp'],
			'idLogAccount' => $arr['arrValue']['idLogAccount'],
			'strTitle'     => $strTitle,
			'stampBook'    => $stampBook,
			'numValue'     => $numValue,
			'numBalance'   => $numBalance,

		))
	 */
	protected function _checkValueDetailError($arr)
	{
		global $classCheck;

		if (!$arr['varsItem']['varsBanksAccountList']['arrStrTitle'][$arr['idLogAccount']]) {
			return 'strMissBank';
		}

		if ($arr['varsItem']['varsBanksAccountList']['arrStrTitle'][$arr['idLogAccount']]['flagLock']) {
			return 'strLockBank';
		}

		$numValue = $arr['numValue'];
		$numBalance = $arr['numBalance'];
		$strTitle = $arr['strTitle'];
		$stampBook = $arr['stampBook'];

		//blank
		if ($numValue == '') {
			return 'strMissNumValue';

		} elseif ($numBalance === '') {
			return 'strMissNumBalance';

		} elseif ($strTitle === '') {
			return 'strMissStrTitle';
		}

		//stampBook
		if (!$stampBook) {
			return 'strFormat';
		}

		if (!($arr['varsStamp']['stampMin'] <= $stampBook && $stampBook <= $arr['varsStamp']['stampMax'])) {
			return 'strTime';
		}

		//numValue
		if ($numValue <= 0) {
			return 'strNumMin';
		}

		//numValue
		$flag = $classCheck->checkValueWord(array(
			'flagType' => 'num',
			'value'    => $numValue
		));
		if ($flag) {
			return 'strFormatNumValue';
		}

		//numBalance
		$flag = $classCheck->checkValueWord(array(
			'flagType' => 'numminus',
			'value'    => $numBalance,
		));
		if ($flag) {
			return 'strFormatNumBalance';
		}

		//numValue 11
		if ($numValue > 99999999999) {
			return 'strNumMax';
		}

		//numBalance 12
		if ($numBalance > 999999999999 || $numBalance < -99999999999) {
			return 'strNumBalanceMax';
		}
	}

	/**
		(array(
			'varsItem'        => $arr['varsItem'],
			'idEntity'        => $arr['idEntity'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _getNumFiscalTermStamp($arr)
	{
		global $varsPluginAccountingEntity;

		$numTimeZone = PLUGIN_ACCOUNTING_NUM_TIME_ZONE;

		$numFiscalPeriod = $arr['numFiscalPeriod'];
		$numFiscalPeriodStart = $varsPluginAccountingEntity[$arr['idEntity']]['numFiscalPeriodStart'];

		$varsEntityNation = $arr['varsItem']['varsEntityNation'];

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
		(array(
			'strBook'  => $stampBook,
		))
	 */
	protected function _getStampBook($arr)
	{
		global $classCheck;
		global $varsAccount;

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

		} elseif (preg_match( "/^([0-9]{4})\.([0-9]{1,2})\.([0-9]{1,2})$/", $strStamp)) {
			preg_match( "/^([0-9]{4})\.([0-9]{1,2})\.([0-9]{1,2})$/", $strStamp, $arrMatch);
			list($strStamp, $numYear, $numMonth, $numDate) = $arrMatch;
			$numHour = 0;
			$numMin = 0;
			$flag = $classCheck->checkValueFormat(array(
				'flagType' => 'date',
				'value'    => $numYear . '/' . $numMonth . '/' . $numDate
			));
			if ($flag) {
				return 0;
			}

		} elseif (preg_match( "/^([0-9]{2})\.([0-9]{1,2})\.([0-9]{1,2})$/", $strStamp)) {
			preg_match( "/^([0-9]{2})\.([0-9]{1,2})\.([0-9]{1,2})$/", $strStamp, $arrMatch);
			list($strStamp, $numYear, $numMonth, $numDate) = $arrMatch;
			$numYear += 2000;
			$numHour = 0;
			$numMin = 0;
			$flag = $classCheck->checkValueFormat(array(
				'flagType' => 'date',
				'value'    => $numYear . '/' . $numMonth . '/' . $numDate
			));
			if ($flag) {
				return 0;
			}

		} elseif (preg_match( "/^([0-9]{8})$/", $strStamp)) {
			$numYear = substr($strStamp, 0, 4);
			$numMonth = substr($strStamp, 4, 2);
			$numDate = substr($strStamp, 6, 2);
			$numHour = 0;
			$numMin = 0;
			$flag = $classCheck->checkValueFormat(array(
				'flagType' => 'date',
				'value'    => $numYear . '/' . $numMonth . '/' . $numDate
			));
			if ($flag) {
				return 0;
			}

		} elseif (preg_match( "/^([0-9]{4})\-([0-9]{1,2})\-([0-9]{1,2})$/", $strStamp)) {
			preg_match( "/^([0-9]{4})\-([0-9]{1,2})\-([0-9]{1,2})$/", $strStamp, $arrMatch);
			list($strStamp, $numYear, $numMonth, $numDate) = $arrMatch;
			$numHour = 0;
			$numMin = 0;
			$flag = $classCheck->checkValueFormat(array(
				'flagType' => 'date',
				'value'    => $numYear . '/' . $numMonth . '/' . $numDate
			));
			if ($flag) {
				return 0;
			}

		} else {
			return 0;
		}

		$strTimeZone = (-1 * $varsAccount['numTimeZone']) . 'hours';
		$dateTime = new DateTime("$numYear-$numMonth-$numDate 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stampBook = $dateTime->format('U') + $numHour * 3600 + $numMin * 60;

		return $stampBook;
	}

	/**
		(array(
			'valueLog'      => $valueLog,
			'varsStamp'     => $varsStamp,
			'varsStampTemp' => $varsStampTemp,
			'varsStatus'    => $varsStatus,
			'numRow'        => $numRow,
		))
	 */
	protected function _setCheckStampBook($arr)
	{
		$strStampBook = $this->_getStrStampBook(array(
			'valueLog' => $arr['valueLog'],
		));

		if ($strStampBook == '') {
			$flagError = __LINE__;
			$strError = '';
			if ($strStampBook == '') {
				$strError = 'strMissStampBook';
			}
			$arr['varsStatus']['arrErrorNumRow'][] = $arr['numRow'];
			$arr['varsStatus']['arrError'][] = $strError;
			$arr['varsStatus']['arrErrorTime'][] = $strStampBook;
		}

		//stampBook
		if (!$flagError) {
			$stampBook = $this->_getStampBook(array(
				'strBook'  => $strStampBook,
			));
			if (!$stampBook) {
				$flagError = __LINE__;
				$arr['varsStatus']['arrErrorNumRow'][] = $arr['numRow'];
				$arr['varsStatus']['arrError'][] = 'strFormat';
				$arr['varsStatus']['arrErrorTime'][] = $strStampBook;
			}
		}

		if (!$flagError) {
			if ($arr['varsStampTemp']) {
				if (!($arr['varsStamp']['stampMin'] <= $stampBook && $stampBook <= $arr['varsStamp']['stampMax']
					&& $arr['varsStampTemp']['stampMin'] <= $stampBook && $stampBook <= $arr['varsStampTemp']['stampMax']
				)) {
					$arr['varsStatus']['arrErrorNumRow'][] = $arr['numRow'];
					$arr['varsStatus']['arrError'][] = 'strTime';
					$arr['varsStatus']['arrErrorTime'][] = $strStampBook;
				}

			} else {
				if (!($arr['varsStamp']['stampMin'] <= $stampBook && $stampBook <= $arr['varsStamp']['stampMax'])) {
					$arr['varsStatus']['arrErrorNumRow'][] = $arr['numRow'];
					$arr['varsStatus']['arrError'][] = 'strTime';
					$arr['varsStatus']['arrErrorTime'][] = $strStampBook;
				}
			}
		}

		return $arr;
	}

	/**
		(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $arr['idEntity'],
			'idLogAccount'    => $arr['arrValue']['idLogAccount'],
			'stampBook'       => $stampCheck,
		))
	 */
	protected function _getVarsLogBanks($arr)
	{
		global $classDb;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogBanks',
			'arrLimit' => array(),
			'arrOrder'  => array(),
			'arrOrder'  => array(
				'strColumn' => 'id',
				'flagDesc'  => 1,
			),
			'flagAnd'  => 1,
			'arrWhere' => array(
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
					'strColumn'     => 'idLogAccount',
					'flagCondition' => 'eq',
					'value'         => $arr['idLogAccount'],
				),
				array(
					'flagType'      => '',
					'strColumn'     => 'stampBook',
					'flagCondition' => 'eqBig',
					'value'         => $arr['stampBook'],
				),
				array(
					'flagType'      => '',
					'strColumn'     => 'flagRemove',
					'flagCondition' => 'eq',
					'value'         => 0,
				),
			),
		));

		return $rows['arrRows'];
	}

	/**
		(array(
		))
	 */
	protected function _getVarsTargetLogBanks($arr)
	{
		global $classDb;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogBanks',
			'arrLimit' => array(),
			'arrOrder'  => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
				array(
					'flagType'      => '',
					'strColumn'     => 'id',
					'flagCondition' => 'eq',
					'value'         => $arr['idTarget'],
				),
			),
		));

		return $rows['arrRows'][0];
	}



	/**
		(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $arr['idEntity'],
			'idAccount'       => $varsAccount['id'],
			'arrValue' => array(

			),
		))
	 */
	protected function _iniAdd($arr)
	{
		global $classEscape;
		global $classDb;
		$dbh = $classDb->getHandle();

		$arrValue = $arr['arrValue'];

		$stampRegister = TIMESTAMP;
		$stampUpdate = TIMESTAMP;
		$idEntity = $arr['idEntity'];
		$numFiscalPeriod = $arr['numFiscalPeriod'];
		$idAccount = $arr['idAccount'];

		$idLogAccount = $arrValue['idLogAccount'];
		$strTitle = $arrValue['strTitle'];
		$flagIn = $arrValue['flagIn'];
		$numValueIn = $arrValue['numValueIn'];
		$numValueOut = $arrValue['numValueOut'];
		$numBalance = $arrValue['numBalance'];
		$stampBook = $arrValue['stampBook'];

		$arrVersion = array();
		$arrVersion[] = $this->_iniVarsVersion(array(
			'arrValue' => $arrValue,
		));
		$jsonVersion = json_encode($arrVersion);

		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrValue['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));

		$arrChargeHistory = array(
			array(
				'stampRegister' => TIMESTAMP,
				'idAccount'     => $idAccount,
			),
		);
		$jsonChargeHistory = json_encode($arrChargeHistory);
		$this->checkTextSize(array(
			'flag' => 'errorDataMax',
			'str'  => $jsonChargeHistory,
		));

		$varsIdNumber = $this->_getIdAutoIncrement(array(
			'idTarget' => 'idLogBanks'
		));
		if (!$varsIdNumber[$idEntity][$numFiscalPeriod]) {
			$varsIdNumber[$idEntity][$numFiscalPeriod] = 1;
		}
		$idLogBanks = $varsIdNumber[$idEntity][$numFiscalPeriod];

		$arrayTemp = compact(
			'stampRegister',
			'stampUpdate',
			'idEntity',
			'numFiscalPeriod',
			'idLogBanks',
			'idLogAccount',
			'idAccount',
			'stampBook',
			'strTitle',
			'flagIn',
			'numValueIn',
			'numValueOut',
			'numBalance',
			'arrSpaceStrTag',
			'jsonChargeHistory',
			'jsonVersion'
		);

		$arrDbColumn = array();
		$arrDbValue = array();
		foreach ($arrayTemp as $keyTemp => $valueTemp) {
			$arrDbColumn[] = $keyTemp;
			$arrDbValue[] = $valueTemp;
		}

		$id = $classDb->insertRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingLogBanks',
			'arrColumn' => $arrDbColumn,
			'arrValue'  => $arrDbValue,
		));

		$varsIdNumber[$idEntity][$numFiscalPeriod]++;
		$this->_updateIdAutoIncrement(array(
			'idTarget'   => 'idLogBanks',
			'varsTarget' => $varsIdNumber
		));

		$varsLogBanks = $this->_getVarsTargetLogBanks(array('idTarget' => $id));
		$this->_updateDbPreferenceStamp(array('strColumn' => 'logBanks'));

		$this->_iniPreferenceStampCheck(array(
			'idTarget'        => $idLogAccount,
			'stampCheck'      => $stampBook,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $arr['idEntity'],
		));

		return $varsLogBanks;
	}

	/**
		(array(
			'flagStatus'      => 'flagUpdate',
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idTarget'        => $valueLogFlagCaution,
			'flagCaution'     => $flagCaution,
		))
	 */
	protected function _iniFlagUpdate($arr)
	{
		global $classEscape;
		global $classDb;
		$dbh = $classDb->getHandle();

		$flagCaution = $arr['flagCaution'];

		$arrayTemp = compact(
			'flagCaution'
		);

		$arrDbColumn = array();
		$arrDbValue = array();
		foreach ($arrayTemp as $keyTemp => $valueTemp) {
			$arrDbColumn[] = $keyTemp;
			$arrDbValue[] = $valueTemp;
		}

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable' => 'accountingLogBanks',
			'arrColumn' => $arrDbColumn,
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
					'flagType'      => '',
					'strColumn'     => 'idLogBanks',
					'flagCondition' => 'eq',
					'value'         => $arr['idTarget'],
				),
			),
			'arrValue'  => $arrDbValue,
		));

		$this->_updateDbPreferenceStamp(array('strColumn' => 'logBanks'));
	}

	/**
	 *
	 */
	protected function _iniPreferenceStampCheck($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $classTime;
		global $varsAccount;

		$stampCheck = $this->_getStampZero(array(
			'stamp' => $arr['stampCheck']
		));

		$arrayTemp = compact(
			'stampCheck'
		);
		$arrDbColumn = array();
		$arrDbValue = array();
		foreach ($arrayTemp as $keyTemp => $valueTemp) {
			$arrDbColumn[] = $keyTemp;
			$arrDbValue[] = $valueTemp;
		}

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable' => 'accountingLogBanksAccount',
			'arrColumn' => $arrDbColumn,
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
					'flagType'      => '',
					'strColumn'     => 'idLogAccount',
					'flagCondition' => 'eq',
					'value'         => $arr['idTarget'],
				),
				array(
					'flagType'      => '',
					'strColumn'     => 'stampCheck',
					'flagCondition' => 'small',
					'value'         => $arr['stampCheck'],
				),
			),
			'arrValue'  => $arrDbValue,
		));

		$this->_updateDbPreferenceStamp(array('strColumn' => 'logBanksAccount'));
	}


	/**
		(array(
			'flagStatus' => 'varsVersion',
			'arrValue'   => $arrValue['arr'],
		))
	 */
	protected function _iniVarsVersion($arr)
	{
		global $classEscape;

		$arrColumn = array(
			'stampRegister',
			'stampUpdate',
			'idLogAccount',
			'stampBook',
			'strTitle',
			'flagIn',
			'numValueIn',
			'numValueOut',
			'numBalance',
			'arrSpaceStrTag',
		);

		$arrayNew = array();
		$array = $arrColumn;
		foreach ($array as $key => $value) {
			if ($value == 'stampRegister' || $value == 'stampUpdate') {
				$arrayNew[$value] = TIMESTAMP;

			} elseif ($value == 'arrSpaceStrTag') {
				$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arr['arrSpaceStrTag']));
				$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));
				$arrayNew[$value] = $arrSpaceStrTag;

			} else {
				$arrayNew[$value] = $arr['arrValue'][$value];
			}
		}

		return $arrayNew;
	}

	/**
		(array(
			'flagStatus'      => 'WriteHistory',
			'varsLog'         => $arrVarsLogWrite[$numberRow],
			'varsLogBanks'    => $arrLogBanks[$numberRow],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idAccount'       => $arr['idAccount'],
		))
	 */
	protected function _iniWriteHistory($arr)
	{
		$flag = $this->_setWriteHistory(array(
			'idEntity'        => $arr['idEntity'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'varsLog'         => $arr['varsLog'],
			'varsLogBanks'    => $arr['varsLogBanks'],
			'idAccount'       => $arr['idAccount'],
		));
		if ($flag) {
			return $flag;
		}
	}

	/**
		(array(
			'idEntity'        => $arr['idEntity'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'varsLog'         => $arr['varsLog'],
			'varsLogBanks'    => $arr['varsLogBanks'],
			'idAccount'       => $arr['idAccount'],
		))
	 */
	protected function _setWriteHistory($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$arrColumn = array(
			'jsonWriteHistory',
		);

		if (!$arr['varsLogBanks']['jsonWriteHistory']) {
			$arrWriteHistory = array();

		} else {
			$arrWriteHistory = $arr['varsLogBanks']['jsonWriteHistory'];
		}

		$arrWriteHistory[] = array(
			'stampRegister'   => TIMESTAMP,
			'idAccount'       => $arr['idAccount'],
			'idLog'           => ($arr['varsLog']['idLog'])? $arr['varsLog']['idLog'] : 0,
		);

		$jsonWriteHistory = json_encode($arrWriteHistory);

		$flag = $this->checkTextSize(array(
			'flag'       => 'errorDataMax',
			'str'        => $jsonWriteHistory,
			'flagReturn' => 1,
		));
		if ($flag) {
			return $flag;
		}
		$arrValue = array($jsonWriteHistory);

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable' => 'accountingLogBanks',
			'arrColumn' => $arrColumn,
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
					'strColumn'     => 'idLogBanks',
					'flagCondition' => 'eq',
					'value'         => $arr['varsLogBanks']['idLogBanks'],
				),
			),
			'arrValue'  => $arrValue,
		));

		$this->_updateDbPreferenceStamp(array('strColumn' => 'logBanks'));
	}

}
