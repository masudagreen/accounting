<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcBanks_Sumisinnetbank extends Code_Else_Plugin_Accounting_Jpn_CalcBanks
{
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

	/**
		(array(
			'flagStatus'      => 'check',
			'flagDepMethod'   => 'straight',
			'varsValue'       => $varsValue,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	public function allot($arr)
	{
		$method = '_ini' . ucwords($arr['flagStatus']);
		if (method_exists($this, $method)) {
			return $this->$method($arr);

		} else {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}
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
		$varsBanks = $arr['varsItem']['varsBanksList'][$arr['flagBank']];

		$arrayNew = array();
		$strDelimiter = $varsBanks['strDelimiter'];

		$array = $varsBanks['varsCsv'];
		foreach ($array as $key => $value) {
			$arrayNew[] = '"' . $key . '"';
		}
		$str =  join(',', $arrayNew) . "\n";
		$arr['arrayCSV'][0] = $str;

		return $arr['arrayCSV'];
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
		global $classCrypte;

		$flagVars = array(
			'flag'       => '',
			'varsDetail' => array(),
		);

		if (!$arr['varsBanksAccount']['blobDetail']) {
			$flagVars['flag'] = __LINE__;
			return $flagVars;
		}
		$jsonDetail = $classCrypte->setDecrypt(array('data' => $arr['varsBanksAccount']['blobDetail']));
		$varsDetail = json_decode($jsonDetail, true);

		$array = $arr['varsBanks']['varsDetail'];
		foreach ($array as $key => $value) {
			if (is_null($varsDetail[$value['id']])) {
				$flagVars['flag'] = __LINE__;
				return $flagVars;
			}
		}
		$flagVars['varsDetail'] = $varsDetail;

		return $flagVars;
	}

	/**
		(array(
			'flagStatus'          => 'check',
			'arrayCSV'            => $arr['arrayCSV'],
			'flagBank'            => $varsBanksAccount['flagBank'],
			'idEntity'            => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'numFiscalPeriodTemp' => $arr['numFiscalPeriodTemp'],
			'idAccount'           => $varsAccount['id'],
			'varsItem'            => $varsItem,
			'varsItemTemp'        => $varsItemTemp,
			'arrValue'            => array(
				'arrSpaceStrTag' => $arr['strTitle'],
				'idLogAccount'   => $arr['varsFlag']['idLogAccount'],
			),
		))
	 */
	protected function _iniCheck($arr)
	{
		$varsCSV = $this->_setCheck($arr);

		return $varsCSV;
	}

	/**
	 *
	 */
	protected function _getVarsJsonDetail($arr)
	{
		global $classCrypte;

		if (!$arr['value']['blobDetail']) {
			return array();
		}
		$jsonDetail = $classCrypte->setDecrypt(array('data' => $arr['value']['blobDetail']));
		$varsDetail = json_decode($jsonDetail, true);

		$array = $arr['varsItem']['varsBanksList'][$arr['value']['flagBank']]['varsDetail'];
		foreach ($array as $key => $value) {
			if ($value['flagSecret']) {
				$varsDetail[$value['id']] = '';
			}
		}

		return $varsDetail;
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

		'stampBook' => '日付',
		'strTitle' => '内容',
		'numValueOut' => '出金金額(円)',
		'numValueIn' => '入金金額(円)',
		'numBalance' => '残高(円)',
		'strMemo' => 'メモ',

	 */
	protected function _checkValueDetailCSV($arr)
	{
		global $classCheck;
		global $classEscape;

		$strStampBook = $this->_getStrStampBook(array(
			'valueLog' => $arr['valueLog'],
		));
		$stampBook = $this->_getStampBook(array(
			'strBook'  => $strStampBook,
		));
		$strTitle = mb_substr($arr['valueLog']['strTitle'], 0, 100);
		$numValueIn = str_replace(',', '', $arr['valueLog']['numValueIn']);
		$numValueOut = str_replace(',', '', $arr['valueLog']['numValueOut']);
		$numBalance = str_replace(',', '', $arr['valueLog']['numBalance']);
		$strMemo = $arr['valueLog']['strMemo'] . ' ' . $arr['arrValue']['arrSpaceStrTag'];
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
			if ($value == '-') {
				continue;
			}
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
			$arr['varsStatus']['arrPassTime'][] = $strStampBook;
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
}
