<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcDep_Average extends Code_Else_Plugin_Accounting_Jpn_CalcDep
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

		))
	 */
	protected function _getVarsNumValueDepCalcBase($arr)
	{
		$data = $this->_getValueNumber(array(
			'arrValue' => $arr['arrValue'],
		));

		$numValueNet = $data['numValue'] - $data['numValueCompression'];
		$numValueDepCalcBase = $numValueNet;

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

		$this->_setVarsCalc(array(
			'varsItem' => $varsItem,
			'arrValue' => $arr['arrValue'],
		));


		return $this->_varsCalc;
	}

	/**
		(array(

		))
	 */
	protected function _setVarsCalc($arr)
	{
		global $classEscape;

		$numUsefulLife = $arr['arrValue']['numUsefulLife'];

		$numValueDepCalcBase = $this->_getVarsNumValueDepCalcBase(array(
			'varsItem' => $arr['varsItem'],
			'arrValue' => $arr['arrValue'],
		));

		$arrCommaDepMonth = $classEscape->splitCommaArray(array('data' => $this->_varsCalc['arrCommaDepMonth']));
		$numDepMonth = count($arrCommaDepMonth);
		$numFiscalTermMonth = $arr['varsItem']['varsEntityNation']['numFiscalTermMonth'];

		//tax
		$numValueNetOpeningTax = $arr['arrValue']['numValueNetOpening'] + $arr['arrValue']['numValueDepPrevOver'];
		$numValueDepCalcRemainingBook = $numValueNetOpeningTax - $arr['arrValue']['numValueRemainingBook'];
		if ($numValueDepCalcRemainingBook == 0) {
			$this->_resetVarsCalc();
			return;
		}

		$numDepMonthCalc = $this->_updateCalc(array(
			'flagType' => 'round',
			'num'      => $numValueNetOpeningTax / ($numValueDepCalcBase / ($numUsefulLife * 12)),
			'numLevel' => 0
		));

		if ($numDepMonthCalc >= $numFiscalTermMonth) {
			$numValueDepCalc = $this->_updateCalc(array(
				'flagType' => $arr['varsItem']['varsFixedAssets']['flagFractionDep'],
				'num'      => ($numValueDepCalcBase / ($numUsefulLife * 12)) * $numDepMonth,
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
				'varsItem'  => $arr['varsItem'],
				'numMonths' => $numDepMonthCalc,
			));

		} elseif ($arr['arrValue']['stampEnd'] != '') {
			if ($numDepMonth >= $numDepMonthCalc) {
				$numValueDepCalc = $numValueDepCalcRemainingBook;

			} else {
				$numValueDepCalc = $this->_updateCalc(array(
					'flagType' => $arr['varsItem']['varsFixedAssets']['flagFractionDep'],
					'num'      => ($numValueDepCalcBase / ($numUsefulLife * 12)) * $numDepMonth,
					'numLevel' => 0
				));
			}
		}

		$this->_varsCalc['numValueDepCalc'] = $numValueDepCalc;
	}
}
