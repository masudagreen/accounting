<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcDep_Straight extends Code_Else_Plugin_Accounting_Jpn_CalcDep
{
	protected $_varsChildStatic = array(
		'depStraightNew' => 'back/tpl/templates/else/plugin/accounting/dat/depStraightNew.csv',
		'depStraightOld' => 'back/tpl/templates/else/plugin/accounting/dat/depStraightOld.csv',
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

		))
	 */
	protected function _getVarsNumValueDepCalcBase($arr)
	{
		$data = $this->_getValueNumber(array(
			'arrValue' => $arr['arrValue'],
		));

		$numValueNet = $data['numValue'] - $data['numValueCompression'];
		if ($arr['varsItem']['flag20070331']) {

			$numValueNetOpeningTax = $arr['arrValue']['numValueNetOpening'] + $arr['arrValue']['numValueDepPrevOver'];

			$flagType = $arr['varsItem']['varsFixedAssets']['flagFractionDepSurvivalRate'];
			$numSurvivalRate = $this->_updateCalc(array(
				'flagType' => $flagType,
				'num'      => $numValueNet * $arr['arrValue']['numSurvivalRate'] / 100,
				'numLevel' => 0
			));

			$flagType = $arr['varsItem']['varsFixedAssets']['flagFractionDepSurvivalRateLimit'];
			$numSurvivalRateLimit = $this->_updateCalc(array(
				'flagType' => $flagType,
				'num'      => $numValueNet * $arr['arrValue']['numSurvivalRateLimit'] / 100,
				'numLevel' => 0
			));

			$numValueDepCalcBase = $numValueNet - $numSurvivalRate;

			if ($arr['varsItem']['flag20070401f1'] && $numValueNetOpeningTax <= $numSurvivalRateLimit) {
				$numValueDepCalcBase = $numSurvivalRateLimit;
			}

		} else {
			$numValueDepCalcBase = $numValueNet;
		}

		return $numValueDepCalcBase;
	}

	/**
		(array(

		))
	 */
	protected function _iniCalc($arr)
	{
		$varsItem = $this->_getVarsItem(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'arrValue'        => $arr['arrValue'],
		));

		$vars = $this->getVars(array(
			'path' => $this->_extendSelf['pathFSItem'],
		));

		$varsItem = $this->_updateVarsItem(array(
			'varsItem' => $varsItem,
		));

		$flag = $this->_checkValueStamp(array(
			'varsItem' => &$varsItem,
			'arrValue' => $arr['arrValue'],
		));

		if ($flag) {
			if (FLAG_TEST) {
				var_dump($flag);
			}
			exit;
		}

		$flag = $this->_checkValue(array(
			'varsItem'        => &$varsItem,
			'arrValue'        => $arr['arrValue'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		if ($flag) {
			if (FLAG_TEST) {
				var_dump($flag);
			}
			exit;
		}

		$varsStampTerm = $this->_getVarsStampTermCalc(array(
			'arrValue'   => $arr['arrValue'],
			'varsItem'   => $varsItem,
		));

		$this->_varsCalc['arrCommaDepMonth'] = $this->_getArrCommaDepMonth(array(
			'varsStampTerm' => $varsStampTerm,
			'varsItem'      => $varsItem,
		));

		if ($arr['arrValue']['stampEnd'] != '') {
			if ($arr['arrValue']['stampEnd'] < $varsItem['varsStampFiscalPeriod']['f1']['stampMin']) {
				$this->_resetVarsCalc();
				return $this->_varsCalc;
			}
		}

		if ($varsItem['flag20070401']) {
			$this->_setVarsCalc20070401(array(
				'varsItem' => $varsItem,
				'arrValue' => $arr['arrValue'],
			));

		} else {
			$this->_setVarsCalc20070331(array(
				'varsItem' => $varsItem,
				'arrValue' => $arr['arrValue'],
			));
		}


		return $this->_varsCalc;
	}

	/**
		(array(

		))
	 */
	protected function _updateVarsItem($arr)
	{
		$arr['varsItem']['varsDepRate20070401'] = $this->_getVarsCsvKey(array(
			'strKey' => 'numUsefulLife',
			'path'   => $this->_varsChildStatic['depStraightNew'],
		));

		$arr['varsItem']['varsDepRate20070331'] = $this->_getVarsCsvKey(array(
			'strKey' => 'numUsefulLife',
			'path'   => $this->_varsChildStatic['depStraightOld'],
		));

		return $arr['varsItem'];
	}

	/**
		(array(

		))
	 */
	protected function _setVarsCalc20070401($arr)
	{
		global $classEscape;

		$numFiscalTermMonth = $arr['varsItem']['varsEntityNation']['numFiscalTermMonth'];

		if ($numFiscalTermMonth == 12) {
			$this->_setVarsCalc20070401Data(array(
				'varsItem' => $arr['varsItem'],
				'arrValue' => $arr['arrValue'],
			));

		} else {
			$this->_setVarsCalc20070401DataUnder(array(
				'varsItem' => $arr['varsItem'],
				'arrValue' => $arr['arrValue'],
			));
		}
	}

	/**
		(array(

		))
	 */
	protected function _setVarsCalc20070401DataUnder($arr)
	{
		global $classEscape;

		$this->_varsCalc['flagDepRateType'] = 0;

		$numUsefulLife = $arr['arrValue']['numUsefulLife'];

		$numValueDepCalcBase = $this->_getVarsNumValueDepCalcBase(array(
			'varsItem' => $arr['varsItem'],
			'arrValue' => $arr['arrValue'],
		));

		$arrCommaDepMonth = $classEscape->splitCommaArray(array('data' => $this->_varsCalc['arrCommaDepMonth']));
		$numDepMonth = count($arrCommaDepMonth);
		$numFiscalTermMonth = $arr['varsItem']['varsEntityNation']['numFiscalTermMonth'];

		$numRateDep = $arr['varsItem']['varsDepRate20070401'][$numUsefulLife]['numRateDep'];
		$numRateDepUpdate = $this->_updateCalc(array(
			'flagType' => 'ceil',
			'num'      => ($numRateDep * $numFiscalTermMonth) / 12,
			'numLevel' => 3
		));

		$this->_varsCalc['numRateDep'] = $numRateDepUpdate;

		$numValueNetOpeningTax = $arr['arrValue']['numValueNetOpening'] + $arr['arrValue']['numValueDepPrevOver'];

		$numValueDepCalcRemainingBook = $numValueNetOpeningTax - $arr['arrValue']['numValueRemainingBook'];

		if ($numValueDepCalcRemainingBook == 0) {
			$this->_resetVarsCalc();
			return;
		}

		$numValueDepCalc = $this->_updateCalc(array(
			'flagType' => $arr['varsItem']['varsFixedAssets']['flagFractionDep'],
			'num'      => $numValueDepCalcBase * $numRateDepUpdate * $numDepMonth / $numFiscalTermMonth,
			'numLevel' => 0
		));

		if ($numValueDepCalc > $numValueDepCalcRemainingBook) {
			$numValueDepCalc = $numValueDepCalcRemainingBook;
		}
		$this->_varsCalc['numValueDepCalc'] = $numValueDepCalc;
	}


	/**
		(array(

		))
	 */
	protected function _setVarsCalc20070401Data($arr)
	{
		global $classEscape;

		$this->_varsCalc['flagDepRateType'] = 1;

		$numUsefulLife = $arr['arrValue']['numUsefulLife'];

		$numRateDep = $arr['varsItem']['varsDepRate20070401'][$numUsefulLife]['numRateDep'];
		$this->_varsCalc['numRateDep'] = $numRateDep;

		$numValueDepCalcBase = $this->_getVarsNumValueDepCalcBase(array(
			'varsItem' => $arr['varsItem'],
			'arrValue' => $arr['arrValue'],
		));

		$arrCommaDepMonth = $classEscape->splitCommaArray(array('data' => $this->_varsCalc['arrCommaDepMonth']));
		$numDepMonth = count($arrCommaDepMonth);
		$numFiscalTermMonth = $arr['varsItem']['varsEntityNation']['numFiscalTermMonth'];

		$numValueNetOpeningTax = $arr['arrValue']['numValueNetOpening'] + $arr['arrValue']['numValueDepPrevOver'];

		$numValueDepCalcRemainingBook = $numValueNetOpeningTax - $arr['arrValue']['numValueRemainingBook'];

		if ($numValueDepCalcRemainingBook == 0) {
			$this->_resetVarsCalc();
			return;
		}

		$numValueDepCalc = $this->_updateCalc(array(
			'flagType' => $arr['varsItem']['varsFixedAssets']['flagFractionDep'],
			'num'      => $numValueDepCalcBase * $numRateDep * $numDepMonth / $numFiscalTermMonth,
			'numLevel' => 0
		));

		if ($numValueDepCalc > $numValueDepCalcRemainingBook) {
			$numValueDepCalc = $numValueDepCalcRemainingBook;
		}
		$this->_varsCalc['numValueDepCalc'] = $numValueDepCalc;
	}


	/**
		(array(

		))
	 */
	protected function _setVarsCalc20070331($arr)
	{
		global $classEscape;

		$numFiscalTermMonth = $arr['varsItem']['varsEntityNation']['numFiscalTermMonth'];

		if ($numFiscalTermMonth == 12) {
			$this->_setVarsCalc20070331Data(array(
				'varsItem' => $arr['varsItem'],
				'arrValue' => $arr['arrValue'],
			));

		} else {
			$this->_setVarsCalc20070331DataUnder(array(
				'varsItem' => $arr['varsItem'],
				'arrValue' => $arr['arrValue'],
			));
		}
	}

	/**
		(array(

		))
	 */
	protected function _setVarsCalc20070331DataUnder($arr)
	{
		global $classEscape;

		$this->_varsCalc['flagDepRateType'] = 0;

		$numUsefulLife = $arr['arrValue']['numUsefulLife'];

		$numValueDepCalcBase = $this->_getVarsNumValueDepCalcBase(array(
			'varsItem' => $arr['varsItem'],
			'arrValue' => $arr['arrValue'],
		));

		$arrCommaDepMonth = $classEscape->splitCommaArray(array('data' => $this->_varsCalc['arrCommaDepMonth']));
		$numDepMonth = count($arrCommaDepMonth);
		$numFiscalTermMonth = $arr['varsItem']['varsEntityNation']['numFiscalTermMonth'];

		$numRateDep = $arr['varsItem']['varsDepRate20070331'][$numUsefulLife]['numRateDep'];
		$numRateDepUpdate = $this->_updateCalc(array(
			'flagType' => 'ceil',
			'num'      => ($numRateDep * $numFiscalTermMonth) / 12,
			'numLevel' => 3
		));

		$this->_varsCalc['numRateDep'] = $numRateDepUpdate;

		$numSurvivalRateLimit = $this->_updateCalc(array(
			'flagType' => $arr['varsItem']['varsFixedAssets']['flagFractionDepSurvivalRateLimit'],
			'num'      => $arr['arrValue']['numValueNet'] * $arr['arrValue']['numSurvivalRateLimit'] / 100,
			'numLevel' => 0
		));

		//tax
		$numValueNetOpeningTax = $arr['arrValue']['numValueNetOpening'] + $arr['arrValue']['numValueDepPrevOver'];

		$numValueDepCalcRemainingBook = $numValueNetOpeningTax - $arr['arrValue']['numValueRemainingBook'];

		if ($numValueDepCalcRemainingBook == 0) {
			$this->_resetVarsCalc();
			return;
		}

		//not 5%
		if ($numValueNetOpeningTax > $numSurvivalRateLimit) {
			$numValueDepCalc = $this->_updateCalc(array(
				'flagType' => $arr['varsItem']['varsFixedAssets']['flagFractionDep'],
				'num'      => $numValueDepCalcBase * $numRateDepUpdate * $numDepMonth / $numFiscalTermMonth,
				'numLevel' => 0
			));

			$numValueDepCalcData = $numValueNetOpeningTax - $numSurvivalRateLimit;
			if ($numValueDepCalc > $numValueDepCalcData) {
				$numValueDepCalc = $numValueDepCalcData;
			}
			$this->_varsCalc['numValueDepCalc'] = $numValueDepCalc;
			return;
		}

		//$numValueNetOpening <= $numSurvivalRateLimit
		//not 20070401
		if ($arr['varsItem']['varsStampFiscalPeriod']['f1']['stampMin'] < $this->_varsStatic['varsStamp']['20070401']) {
			$this->_resetVarsCalc();
			return;
		}

		//average
		$numDepMonthCalc = $this->_updateCalc(array(
			'flagType' => 'round',
			'num'      => $numValueNetOpeningTax / ($numSurvivalRateLimit / 60),
			'numLevel' => 0
		));

		if ($numDepMonthCalc >= $numFiscalTermMonth) {
			$numValueDepCalc = $this->_updateCalc(array(
				'flagType' => $arr['varsItem']['varsFixedAssets']['flagFractionDep'],
				'num'      => ($numSurvivalRateLimit / 60) * $numDepMonth,
				'numLevel' => 0
			));
			if ($numValueDepCalc > $numValueDepCalcRemainingBook) {
				$numValueDepCalc = $numValueDepCalcRemainingBook;
			}
			$this->_varsCalc['numValueDepCalc'] = $numValueDepCalc;
			return;
		}

		if ($arr['arrValue']['stampEnd'] == '') {
			$numValueDepCalc = $numValueDepCalcRemainingBook;
			$this->_varsCalc['arrCommaDepMonth'] = $this->_getArrCommaDepMonthCalc(array(
				'varsItem' => $arr['varsItem'],
				'numMonths' => $numDepMonthCalc,
			));

		} elseif ($arr['arrValue']['stampEnd'] != '') {
			if ($numDepMonth >= $numDepMonthCalc) {
				$numValueDepCalc = $numValueDepCalcRemainingBook;

			} else {
				$numValueDepCalc = $this->_updateCalc(array(
					'flagType' => $arr['varsItem']['varsFixedAssets']['flagFractionDep'],
					'num'      => ($numSurvivalRateLimit / 60) * $numDepMonth,
					'numLevel' => 0
				));
			}
		}

		$this->_varsCalc['numValueDepCalc'] = $numValueDepCalc;
	}


	/**
		(array(

		))
	 */
	protected function _setVarsCalc20070331Data($arr)
	{
		global $classEscape;

		$this->_varsCalc['flagDepRateType'] = 1;

		$numUsefulLife = $arr['arrValue']['numUsefulLife'];

		$numRateDep = $arr['varsItem']['varsDepRate20070331'][$numUsefulLife]['numRateDep'];
		$this->_varsCalc['numRateDep'] = $numRateDep;

		$numValueDepCalcBase = $this->_getVarsNumValueDepCalcBase(array(
			'varsItem' => $arr['varsItem'],
			'arrValue' => $arr['arrValue'],
		));

		$arrCommaDepMonth = $classEscape->splitCommaArray(array('data' => $this->_varsCalc['arrCommaDepMonth']));
		$numDepMonth = count($arrCommaDepMonth);
		$numFiscalTermMonth = $arr['varsItem']['varsEntityNation']['numFiscalTermMonth'];

		$numSurvivalRateLimit = $this->_updateCalc(array(
			'flagType' => $arr['varsItem']['varsFixedAssets']['flagFractionDepSurvivalRateLimit'],
			'num'      => $arr['arrValue']['numValueNet'] * $arr['arrValue']['numSurvivalRateLimit'] / 100,
			'numLevel' => 0
		));

		//tax
		$numValueNetOpeningTax = $arr['arrValue']['numValueNetOpening'] + $arr['arrValue']['numValueDepPrevOver'];
		$numValueDepCalcRemainingBook = $numValueNetOpeningTax - $arr['arrValue']['numValueRemainingBook'];
		if ($numValueDepCalcRemainingBook == 0) {
			$this->_resetVarsCalc();
			return;
		}

		//not 5%
		if ($numValueNetOpeningTax > $numSurvivalRateLimit) {
			$numValueDepCalc = $this->_updateCalc(array(
				'flagType' => $arr['varsItem']['varsFixedAssets']['flagFractionDep'],
				'num'      => $numValueDepCalcBase * $numRateDep * $numDepMonth / $numFiscalTermMonth,
				'numLevel' => 0
			));
			$numValueDepCalcData = $numValueNetOpeningTax - $numSurvivalRateLimit;
			if ($numValueDepCalc > $numValueDepCalcData) {
				$numValueDepCalc = $numValueDepCalcData;
			}
			$this->_varsCalc['numValueDepCalc'] = $numValueDepCalc;
			return;
		}

		//$numValueNetOpening <= $numSurvivalRateLimit
		//not 20070401
		if ($arr['varsItem']['varsStampFiscalPeriod']['f1']['stampMin'] < $this->_varsStatic['varsStamp']['20070401']) {
			$this->_resetVarsCalc();
			return;
		}

		//average
		$numDepMonthCalc = $this->_updateCalc(array(
			'flagType' => 'round',
			'num'      => $numValueNetOpeningTax / ($numSurvivalRateLimit / 60),
			'numLevel' => 0
		));

		if ($numDepMonthCalc >= $numFiscalTermMonth) {
			$numValueDepCalc = $this->_updateCalc(array(
				'flagType' => $arr['varsItem']['varsFixedAssets']['flagFractionDep'],
				'num'      => ($numSurvivalRateLimit / 60) * $numDepMonth,
				'numLevel' => 0
			));
			if ($numValueDepCalc > $numValueDepCalcRemainingBook) {
				$numValueDepCalc = $numValueDepCalcRemainingBook;
			}
			$this->_varsCalc['numValueDepCalc'] = $numValueDepCalc;
			return;
		}

		if ($arr['arrValue']['stampEnd'] == '') {
			$numValueDepCalc = $numValueDepCalcRemainingBook;
			$this->_varsCalc['arrCommaDepMonth'] = $this->_getArrCommaDepMonthCalc(array(
				'varsItem' => $arr['varsItem'],
				'numMonths' => $numDepMonthCalc,
			));

		} elseif ($arr['arrValue']['stampEnd'] != '') {
			if ($numDepMonth >= $numDepMonthCalc) {
				$numValueDepCalc = $numValueDepCalcRemainingBook;

			} else {
				$numValueDepCalc = $this->_updateCalc(array(
					'flagType' => $arr['varsItem']['varsFixedAssets']['flagFractionDep'],
					'num'      => ($numSurvivalRateLimit / 60) * $numDepMonth,
					'numLevel' => 0
				));
			}
		}

		$this->_varsCalc['numValueDepCalc'] = $numValueDepCalc;
	}
}
