<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcTempNext_AccountTitleFS_2012_Public extends Code_Else_Plugin_Accounting_Jpn_CalcTempNext_AccountTitleFS
{

	/**
		(array(
			'varsItem'        => $varsItem,
			'flagFS'          => $arr['flagFS'],
			'idTarget'        => $arr['idTarget'],
			'arrValue'        => &$arr['arrValue'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _checkEditValue($arr)
	{
		global $classEscape;

		$varsFS = $arr['varsItem']['varsFS']['jsonJgaapFS' . $arr['flagFS']];
		$varsFS = $this->_getFlagUseLog(array(
			'vars'              => $varsFS,
			'arrIdAccountTitle' => $arr['varsItem']['varsJgaapFS']['arrIdAccountTitle'],
			'numFiscalPeriod'   => $arr['numFiscalPeriod'],
			'idTarget'          => $arr['idTarget'],
		));

		$arrayBlock = $this->_getTreeBlock(array(
			'vars'     => $varsFS,
			'idTarget' => $arr['idTarget'],
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
/*
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
*/
		$flagEditCheck = array();

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
				$flagEditCheck['idAccountTitle'] = 1;
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

			if ($varsTarget['vars']['flagUseAccountTitle']) {
				$idAccountTitle = $varsTarget['vars']['idTarget'];
				$arr['arrValue']['idAccountTitle'] = $idAccountTitle;
				$flagEditCheck['idAccountTitle'] = 0;
			}
		}

		//flagDebit
		$flagDebit = $arr['arrValue']['flagDebit'];
		if ($flagDebit != $varsTarget['vars']['flagDebit']) {
			if ((int) $varsTarget['flagDefault']) {
				$arr['arrValue']['flagDebit'] = $varsTarget['vars']['flagDebit'];
			}
			if ($varsTarget['vars']['flagUseAccountTitle']) {
				$arr['arrValue']['flagDebit'] = $varsTarget['vars']['flagDebit'];
			}
		}

		return $flagEditCheck;
	}
}
