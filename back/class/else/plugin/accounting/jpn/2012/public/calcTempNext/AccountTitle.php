<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcTempNext_AccountTitle_2012_Public extends Code_Else_Plugin_Accounting_Jpn_CalcTempNext_AccountTitle
{

	/**
		(array(
			'arrValue'    => $arrValue,
			'varsItem'    => $varsItem,
			'flagFS'      => $flagFS,
			'vars'        => $vars,
			'idTarget'    => $idTarget,
		))
	 */
	protected function _checkEditValue($arr)
	{
		global $classEscape;

		$numFiscalPeriodTemp = $arr['numFiscalPeriod'] - 1;
		$varsFS = $arr['varsItem']['varsFS']['jsonJgaapAccountTitle'. $arr['flagFS']];
		$varsFS = $this->_getFlagUseLog(array(
			'vars'                  => $varsFS,
			'numFiscalPeriod'       => $arr['numFiscalPeriod'],
			'numFiscalPeriodTemp'   => $numFiscalPeriodTemp,
			'classCalcAccountTitle' => $this->_getClassCalc(array('flagType' => 'AccountTitle')),
			'idTarget'              => $arr['idTarget'],
			'flagFS'                => $arr['flagFS'],
		));

		$arrayBlock = $this->_getTreeBlock(array(
			'vars'     => $varsFS,
			'idTarget' => $arr['idTarget'],
			'arrNew'   => array(),
		));
		if (!$arrayBlock) {
			return 'none';
		}

		$varsTarget = array();
		foreach ($arrayBlock as $key => $value) {
			if ($value['vars']['idTarget'] == $arr['idTarget']) {
				$varsTarget = $value;
				$arr['arrValue']['flagDefault'] = (int) $varsTarget['flagDefault'];
				break;
			}
		}

		$strTitle = $arr['arrValue']['strTitle'];
		$flagNum = $this->_checkTreeStrTitle(array(
			'vars'      => $varsFS,
			'strTarget' => $strTitle,
			'num'       => 0,
			'strSelf'   => $varsTarget['strTitle'],
			'idSelf'    => $varsTarget['vars']['idTarget'],
		));

		if ($flagNum) {
			return 'strTitleTempNext';
		}

		$idAccountTitle = $arr['arrValue']['idAccountTitle'];
		$flag = 0;
		if ((int) $varsTarget['flagDefault']) {
			if ($idAccountTitle != $varsTarget['vars']['idTarget']) {
				$idAccountTitle = $varsTarget['vars']['idTarget'];
				$arr['arrValue']['idAccountTitle'] = $idAccountTitle;
			}

		} else {
			$idAccountTitleCustom = 'custom_' . $arr['flagFS'] . '_' . $idAccountTitle;
			if ($idAccountTitleCustom != $varsTarget['vars']['idTarget']) {
				$flag = 1;
			}
		}

		//idAccountTitle
		if ($flag) {
			$arrayIdAccountTitle = array($idAccountTitle);
			$arrayFSList = $arr['varsItem']['arrayFSList'];
			foreach ($arrayFSList as $keyFSList => $valueFSList) {
				$arrayIdAccountTitle[] = 'custom_' . $keyFSList . '_' . $idAccountTitle;
			}
			foreach ($arrayIdAccountTitle as $key => $value) {
				$flag = $this->_checkValueIdAccountTitleFS(array(
					'varsItem' => $arr['varsItem'],
					'idTarget' => $value,
					'idSelf'   => $arr['idTarget'],
				));
				if ($flag) {
					return 'idAccountTitleTempNext';
				}
			}
			if ($varsTarget['vars']['flagUseAllLog']) {
				$idAccountTitle = $varsTarget['vars']['idTarget'];
				$arr['arrValue']['idAccountTitle'] = $idAccountTitle;
			}
		}

		//flagDebit
		$flagDebit = $arr['arrValue']['flagDebit'];
		if ($flagDebit != $varsTarget['vars']['flagDebit']) {
			if ((int) $varsTarget['flagDefault']) {
				$arr['arrValue']['flagDebit'] = $varsTarget['vars']['flagDebit'];
			}
		}

		//
		$idAccountTitleJgaapFS = $arr['arrValue']['idAccountTitleJgaapFS'];
		if ($idAccountTitleJgaapFS != $varsTarget['vars']['idAccountTitleJgaapFS']) {
			if (!$arr['varsItem']['varsJgaapFS']['arrStrTitle'][$idAccountTitleJgaapFS]) {
				$classCalcTempNextAccountTitleFS = $this->_getClassCalc(array(
					'flagType'   => 'TempNext',
					'flagDetail' => 'AccountTitleFS',
				));
				$flag = $classCalcTempNextAccountTitleFS->allot(array(
					'flagStatus'      => 'back',
					'numFiscalPeriod' => $arr['numFiscalPeriod'],
					'flagFS'          => $arr['flagFS'],
					'idTarget'        => $arr['arrValue']['idAccountTitleJgaapFS'],
				));
				if ($flag) {
					return $flag;
				}
			}
			if ($varsTarget['vars']['flagUseLogElse']
				|| ($varsTarget['vars']['flagUseLogElseTemp'] && $varsTarget['vars']['flagFS'] == 'BS')
			) {
				$arr['arrValue']['idAccountTitleJgaapFS'] = $varsTarget['vars']['idAccountTitleJgaapFS'];
			}

/*unique public*/
			if ($idAccountTitle == 'profitBroughtForward'
				 || $idAccountTitle == 'accountsReceivables'
				 || $idAccountTitle == 'accountsPayables'
			) {
				$arr['arrValue']['idAccountTitleJgaapFS'] = $varsTarget['vars']['idAccountTitleJgaapFS'];
			}
		}

		//
		$varsConsumptionTax = $arr['varsItem']['varsConsumptionTax'];
		if (!is_null($arr['arrValue']['flagConsumptionTaxGeneralRule'])) {
			if ($arr['varsItem']['varsEntityNation']['flagConsumptionTaxDeducted']) {
				if (!$varsConsumptionTax['arrStrGeneralEach'][$arr['arrValue']['flagConsumptionTaxGeneralRule']]) {
					return 'consumptionTax';
				}
			} else {
				if (!$varsConsumptionTax['arrStrGeneralProration'][$arr['arrValue']['flagConsumptionTaxGeneralRule']]) {
					return 'consumptionTax';
				}
			}
		}
		if (!is_null($arr['arrValue']['flagConsumptionTaxSimpleRule'])) {
			if (!$varsConsumptionTax['arrStrSimple'][$arr['arrValue']['flagConsumptionTaxSimpleRule']]) {
				return 'consumptionTax';
			}
		}

		//$flagUse = $arr['arrValue']['flagUse'];

	}

}
