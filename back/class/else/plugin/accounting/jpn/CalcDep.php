<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcDep extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_varsStatic = array(
		'varsStamp' => array(
			'20070401'   => 1175353200,//20070401
			'20120401'   => 1333206000,//20120401
			'buildings'  => 891356400,//19980401
			'stampMeiji' => -197178000,//明治
		),
	);

	protected $_varsCalc = array(
		'arrCommaDepMonth' => '',
		'numValueDepCalc'  => '',
		'numRateDep'       => '',
		'flagDepRateType'  => 1,
		'numValueAssured'  => '',
	);

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
			'arrValue'        => $arrValue,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	public function allot($arr)
	{

		$method = '_ini' . ucwords($arr['flagStatus']);
		if (method_exists($this, $method)) {
			return $this->$method($arr);
		}

		$str = ucwords($arr['flagDepMethod']);

		$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/' . PLUGIN_ACCOUNTING_STR_NATION . '/calcDep/' . $str . ".php";
		$strClass = 'Code_Else_Plugin_Accounting_' . ucwords(PLUGIN_ACCOUNTING_STR_NATION) . '_CalcDep_' . $str;
		if (!file_exists($path)) {
			return $this->_varsCalc;
		}
		require_once($path);
		$classCall = new $strClass;

		return $classCall->allot($arr);
	}

	/**
		(array(
		))
	 */
	protected function _iniUpdate($arr)
	{
		$data = $this->_getValueNumber(array(
			'arrValue' => $arr['arrValue'],
		));

		$varsItem = $this->_getVarsItem(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'arrValue'        => $arr['arrValue'],
		));

		//numValue

		//NumValueCompression

		//NumValueNet
		$numValueNet = $data['numValue'] - $data['numValueCompression'];
		$arr['arrValue']['numValueNet'] = $numValueNet;
		//NumValueAccumulated

		//NumValueNetOpening
		$numValueNetOpening = $numValueNet - $data['numValueAccumulated'];
		$arr['arrValue']['numValueNetOpening'] = $numValueNetOpening;

		//NumValueDepCalcBase
		$arr['arrValue']['numValueDepCalcBase'] = $this->_updateVarsNumValueDepCalcBase(array(
			'arrValue'  => $arr['arrValue'],
			'varsItem'  => $varsItem,
		));

		$numValueDepLimit = $data['numValueDepCalc']
						+ $data['numValueDepUp']
						+ $data['numValueDepExtra']
						+ $data['numValueDepSpecial']
						+ $data['numValueDepSpecialShortPrev'];

		$arr['arrValue']['numValueDepLimit'] = $numValueDepLimit;
		$numValueDep = $numValueDepLimit;
		$arr['arrValue']['numValueDep'] = $numValueDep;

		//NumValueAccumulatedClosing
		$numValueAccumulatedClosing = $data['numValueAccumulated'] + $numValueDep;
		$arr['arrValue']['numValueAccumulatedClosing'] = $numValueAccumulatedClosing;

		//NumValueNetClosing
		$numValueNetClosing = $numValueNetOpening - $numValueDep;
		if ($arr['arrValue']['stampDrop'] != '') {
			$numValueNetClosing = 0;
		}
		$arr['arrValue']['numValueNetClosing'] = $numValueNetClosing;

		//NumValueDepOperate
		$varsEntityNation = $varsItem['varsEntityNation'];
		if ($varsEntityNation['flagCorporation'] != 1) {
			$flagType = $varsItem['varsFixedAssets']['flagFractionRatioOperate'];
			$numValueDepOperate = $this->_updateCalc(array(
				'flagType' => $flagType,
				'num'      => $numValueDep * $arr['arrValue']['numRatioOperate'] / 100,
				'numLevel' => 0
			));
			$arr['arrValue']['numValueDepOperate'] = $numValueDepOperate;

		} else {
			$arr['arrValue']['numValueDepOperate'] = $numValueDep;
		}

		//NumValueDepCurrentOver
		$numValueDepCurrentOver = $numValueDep - $numValueDepLimit;
		$arr['arrValue']['numValueDepCurrentOver'] = $numValueDepCurrentOver;

		//NumValueDepNextOver
		$numValueDepNextOver = $data['numValueDepPrevOver'] + $numValueDepCurrentOver;
		if ($numValueDepNextOver < 0) {
			$numValueDepNextOver = 0;
		}
		$arr['arrValue']['numValueDepNextOver'] = $numValueDepNextOver;

		//NumValueDepSpecialShortCurrent
		$sumValueDepLaw = $numValueDepLimit - $data['numValueDepCalc'] - $data['numValueDepUp'];
		$numValueDepSpecialShortCurrent = 0;
		if ($numValueDepCurrentOver < 0 && $sumValueDepLaw > 0) {
			if (abs($numValueDepCurrentOver) < abs($sumValueDepLaw)) {
				$numValueDepSpecialShortCurrent = abs(numValueDepCurrentOver);

			} else {
				$numValueDepSpecialShortCurrent = abs($sumValueDepLaw);
			}
		}
		$arr['arrValue']['numValueDepSpecialShortCurrent'] = $numValueDepSpecialShortCurrent;

		//NumValueDepSpecialShortNext
		$numValueDepSpecialShortNext = $numValueDepSpecialShortCurrent - $data['numValueDepSpecialShortCurrentCut'];
		$arr['arrValue']['numValueDepSpecialShortNext'] = $numValueDepSpecialShortNext;

		return $arr['arrValue'];
	}

	/**
		(array(
		))
	 */
	protected function _updateVarsNumValueDepCalcBase($arr)
	{
		$data = $this->_getValueNumber(array(
			'arrValue' => $arr['arrValue'],
		));

		if ($arr['arrValue']['flagDepMethod'] == 'declining') {
			if (!$arr['varsItem']['flag20070401']) {
				$numValueNetOpeningTax = $data['numValueNetOpening'] + $data['numValueDepPrevOver'];

				$flagType = $arr['varsItem']['varsFixedAssets']['flagFractionDepSurvivalRateLimit'];
				$numSurvivalRateLimit = $this->_updateCalc(array(
					'flagType' => $flagType,
					'num'      => $data['numValueNet'] * $arr['arrValue']['numSurvivalRateLimit'] / 100,
					'numLevel' => 0
				));

				$num = $data['numValueNetOpening']
						+ $data['numValueDepPrevOver']
						- $data['numValueDepSpecialShortPrev'];

				if ($arr['varsItem']['flag20070401f1'] && $numValueNetOpeningTax <= $numSurvivalRateLimit) {
					$num = $numSurvivalRateLimit;
				}

				return $num;

			} else {
				$num = $data['numValueNetOpening']
						+ $data['numValueDepPrevOver']
						- $data['numValueDepSpecialShortPrev'];

				return $num;
			}

		} elseif ($arr['arrValue']['flagDepMethod'] == 'straight') {
			if (!$arr['varsItem']['flag20070401']) {
				$numValueNetOpeningTax = $data['numValueNetOpening'] + $data['numValueDepPrevOver'];
				$flagType = $arr['varsItem']['varsFixedAssets']['flagFractionDepSurvivalRate'];
				$numSurvivalRate = $this->_updateCalc(array(
					'flagType' => $flagType,
					'num'      => $data['numValueNet'] * $arr['arrValue']['numSurvivalRate'] / 100,
					'numLevel' => 0
				));

				$flagType = $arr['varsItem']['varsFixedAssets']['flagFractionDepSurvivalRateLimit'];
				$numSurvivalRateLimit = $this->_updateCalc(array(
					'flagType' => $flagType,
					'num'      => $data['numValueNet'] * $arr['arrValue']['numSurvivalRateLimit'] / 100,
					'numLevel' => 0
				));

				$num = $data['numValueNet'] - $numSurvivalRate;
				if ($arr['varsItem']['flag20070401f1'] && $numValueNetOpeningTax <= $numSurvivalRateLimit) {
					$num = $numSurvivalRateLimit;
				}
				return $num;

			} else {
				return $data['numValueNet'];
			}

		} elseif ($arr['arrValue']['flagDepMethod'] == 'average'
			|| $arr['arrValue']['flagDepMethod'] == 'one'
		) {
			return $data['numValueNet'];
		}

		return '';
	}

	/**
		(array(
			'arrValue'        => $arrValue,
		))
	 */
	protected function _getValueNumber($arr)
	{
		$data = array();
		$arrayStr = $arr['arrValue'];
		foreach ($arrayStr as $key => $value) {
			if (preg_match( "/^numValue/", $key)) {
				$data[$key] = $arr['arrValue'][$key];
				if ($arr['arrValue'][$key] == '') {
					$data[$key] = 0;
				}
			}
		}

		return $data;
	}


	/**

	 */
	protected function _checkValue($arr)
	{
		$data = $this->_getValueNumber(array(
			'arrValue' => $arr['arrValue'],
		));

		$flag = $this->_checkValueNumber(array(
			'data' => $data,
		));
		if ($flag) {
			return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;
		}

		//numValue
		if ($data['numValue'] <= 0) {
			return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;
		}

		//numValue numValueRemainingBook
		if ($data['numValue'] < $data['numValueRemainingBook']) {
			if ($arr['arrValue']['stampDrop'] == '') {
				return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;
			}
		}

		//NumValueCompression

		//NumValueNet
		$numValueNet = $data['numValue'] - $data['numValueCompression'];
		if ($data['numValueNet'] != $numValueNet) {
			return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;
		}

		//NumValueAccumulated
		if ($arr['arrValue']['stampStart'] >= $arr['varsItem']['varsStampFiscalPeriod']['f1']['stampMin']) {
			if ($data['numValueAccumulated']) {
				return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;
			}
		}

		//NumValueNetOpening
		$numValueNetOpening = $numValueNet - $data['numValueAccumulated'];
		if ($data['numValueNetOpening'] != $numValueNetOpening) {
			return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;
		}

		if ($numValueNetOpening < 0) {
			return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;
		}

		if ($numValueNetOpening  < $data['numValueRemainingBook']) {
			if ($arr['arrValue']['stampDrop'] == '') {
				return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;
			}
		}

		//NumValueDepCalcBase
		$numValueDepCalcBase = $this->_getVarsNumValueDepCalcBase(array(
			'varsItem'        => $arr['varsItem'],
			'arrValue'        => $arr['arrValue'],
		));

		if ($data['numValueDepCalcBase'] != $numValueDepCalcBase) {
			return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;
		}

		//NumValueDepPrevOver
		if ($arr['arrValue']['stampStart'] >= $arr['varsItem']['varsStampFiscalPeriod']['f1']['stampMin']) {
			if ($data['numValueDepPrevOver']) {
				return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;
			}
		}

	}

	/**
		(array(
			'stampStart' => stamp,
			'stampEnd' => stamp,
			'stampWrapStart' => stamp,
			'stampWrapEnd' => stamp,
		))
	 */
	protected function _checkTerm($arr)
	{
		$flag = '';
		if ($arr['stampWrapStart'] <= $arr['stampStart'] && $arr['stampStart'] <= $arr['stampWrapEnd']
			&& $arr['stampWrapStart'] <= $arr['stampEnd'] && $arr['stampEnd'] <= $arr['stampWrapEnd']
		) {
			$flag = 'all';

		} else if ($arr['stampWrapStart'] <= $arr['stampStart'] && $arr['stampStart'] <= $arr['stampWrapEnd']
			&& $arr['stampWrapEnd'] < $arr['stampEnd']
		) {
			$flag = 'right';

		} else if ($arr['stampStart'] < $arr['stampWrapStart'] && $arr['stampWrapStart'] <= $arr['stampEnd']
			&& $arr['stampEnd'] <= $arr['stampWrapEnd']
		) {
			$flag = 'left';

		} else if ($arr['stampStart'] < $arr['stampWrapStart'] && $arr['stampStart'] < $arr['stampWrapEnd']
			&& $arr['stampEnd'] > $arr['stampWrapStart'] && $arr['stampEnd'] > $arr['stampWrapEnd']
		) {
			$flag = 'middle';

		}

		return $flag;
	}

	/**
		(array(

		))
	 */
	protected function _resetVarsCalc()
	{
		$this->_varsCalc = array(
			'arrCommaDepMonth' => '',
			'numValueDepCalc' => 0,
			'numRateDep' => '',
			'flagDepRateType' => 1,
			'numValueAssured' => '',
		);
	}


	/**
		(array(

		))
	 */
	protected function _getVarsCsvKey($arr)
	{
		global $classFile;

		$arrayNew = array();
		$array = $classFile->getCsvRows(array(
			'path' => $arr['path'],
		));
		foreach ($array as $key => $value) {
			$str = $value[$arr['strKey']];
			$arrayNew[$str] = $value;
		}

		return $arrayNew;
	}

	/**
		(array(

		))
	 */
	protected function _getVarsStampTermCalc($arr)
	{
		$stampStart = $arr['arrValue']['stampStart'];
		$stampEnd = $arr['varsItem']['varsStampFiscalPeriod']['f1']['stampMax'];

		if ($arr['arrValue']['stampEnd'] != '') {
			$stampEnd = $arr['arrValue']['stampEnd'];
		}

		$data = array(
			'stampStart' => $stampStart,
			'stampEnd'   => $stampEnd,
		);

		return $data;
	}


	/**
		(array(

		))
	 */
	protected function _getArrCommaDepMonthCalc($arr)
	{
		global $classEscape;

		$arrCommaDepMonth = $classEscape->splitCommaArrayData(array('data' => $this->_varsCalc['arrCommaDepMonth']));
		$numMonthStart = reset($arrCommaDepMonth);
		$numMonth = $arr['numMonths'] + $numMonthStart;
		if ($numMonth > 12) {
			$numMonth -= 12;
		}

		$arrCommaDepMonth = array();
		$array = $arr['varsItem']['varsStampFiscalPeriod'];
		foreach ($array as $key => $value) {
			if (preg_match("/^f/", $key)) {
				continue;
			}
			if ($key == $numMonth) {
				break;
			}
			$arrCommaDepMonth[] = $key;
		}

		return $classEscape->joinCommaArray(array('arr' => $arrCommaDepMonth));
	}


	/**
		(array(

		))
	 */
	protected function _getArrCommaDepMonth($arr)
	{
		global $classEscape;

		if (!$arr['varsStampTerm']) {
			return '';
		}

		$stampStart = $arr['varsStampTerm']['stampStart'];
		$stampEnd = $arr['varsStampTerm']['stampEnd'];

		$arrCommaDepMonth = array();
		$array = $arr['varsItem']['varsStampFiscalPeriod'];
		foreach ($array as $key => $value) {
			if (preg_match("/^f/", $key)) {
				continue;
			}
			$stampWrapStart = $value['stampMin'];
			$stampWrapEnd = $value['stampMax'];
			$flag = $this->_checkTerm(array(
				'stampStart'     => $stampStart,
				'stampEnd'       => $stampEnd,
				'stampWrapStart' => $stampWrapStart,
				'stampWrapEnd'   => $stampWrapEnd,
			));

			if ($flag) {
				$arrCommaDepMonth[] = $key;
			}

		}

		return $classEscape->joinCommaArray(array('arr' => $arrCommaDepMonth));
	}

	/**
		(array(
			'data' => $data,
		))
	 */
	protected function _checkValueNumber($arr)
	{
		$array = $arr['data'];
		foreach ($array as $key => $value) {
			if ($key == 'numValueDepCurrentOver') {
				continue;
			}
			if ($value < 0) {
				return (__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
		}
	}

	/**
		_getVarsItem(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _getVarsItem($arr)
	{
		$varsFixedAssets = $this->_getVarsFixedAssets(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$varsStampFiscalPeriod = $this->_getVarsStampFiscalPeriod(array(
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'varsEntityNation' => $varsEntityNation,
		));

		$flag20070401 = 0;
		$flag20070331 = 1;
		if ($arr['arrValue']['stampStart'] >= $this->_varsStatic['varsStamp']['20070401']) {
			$flag20070401 = 1;
			$flag20070331 = 0;
		}

		$flag20120401 = 0;
		if ($arr['arrValue']['stampStart'] >= $this->_varsStatic['varsStamp']['20120401']) {
			$flag20120401 = 1;
		}

		$flag20070401f1 = 0;
		if ($varsStampFiscalPeriod['f1']['stampMin'] >= $this->_varsStatic['varsStamp']['20070401']) {
			$flag20070401f1 = 1;
		}

		$data = array(
			'varsFixedAssets'       => $varsFixedAssets,
			'varsEntityNation'      => $varsEntityNation,
			'varsStampFiscalPeriod' => $varsStampFiscalPeriod,
			'flag20070401'          => $flag20070401,
			'flag20070331'          => $flag20070331,
			'flag20120401'          => $flag20120401,
			'flag20070401f1'        => $flag20070401f1,
		);

		return $data;
	}




	/**
		(array(
			'varsItem'        => $varsItem,
			'arrValue'        => $arr['arrValue'],
		))
	 */
	protected function _checkValueStamp($arr)
	{
		$data = array();
		$arrayStr = array('stampBuy', 'stampStart', 'stampEnd', 'stampDrop');
		foreach ($arrayStr as $key => $value) {
			if ($arr['arrValue'][$value] == '') {
				continue;
			}
			if ($arr['arrValue'][$value] > $arr['varsItem']['varsStampFiscalPeriod']['f1']['stampMax']) {
				return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;
			}
			if ($arr['arrValue'][$value] < $this->_varsStatic['varsStamp']['stampMeiji']) {
				return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;
			}
		}

		if ($arr['arrValue']['stampBuy'] > $arr['arrValue']['stampStart']) {
			return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;
		}

		if ($arr['arrValue']['stampEnd'] != '') {
			if ($arr['arrValue']['stampStart'] > $arr['arrValue']['stampEnd']) {
				return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;
			}
		}

		if ($arr['arrValue']['stampDrop'] != '') {
			if ($arr['arrValue']['stampStart'] > $arr['arrValue']['stampDrop']) {
				return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;
			}
		}

		if ($arr['arrValue']['stampDrop'] != '' && $arr['arrValue']['stampEnd'] != '') {
			if ($arr['arrValue']['stampDrop'] < $arr['arrValue']['stampEnd']) {
				return __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__;
			}
		}

	}



}
