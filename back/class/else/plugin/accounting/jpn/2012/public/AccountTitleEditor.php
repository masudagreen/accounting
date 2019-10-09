<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_AccountTitleEditor_2012_Public extends Code_Else_Plugin_Accounting_Jpn_AccountTitleEditor
{
	/**
		(array(
			'arrValue'    => $arrValue,
			'varsItem'    => $varsItem,
			'flagFS'      => $flagFS,
			'vars'        => $vars,
			'idTarget'    => $idTarget,
			'flagDefault' => $flagDefault,
		))
	 */
	protected function _checkValueDetailAdd($arr)
	{
		global $classEscape;
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varAccount;
		global $varsRequest;

		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$arrayBlock = $this->_getTreeBlock(array(
			'vars'     => $arr['varsItem']['varsFS']['jsonJgaapAccountTitle'. $arr['flagFS']],
			'idTarget' => $arr['idTarget'],
		));
		if (!$arrayBlock) {
			$this->_sendOld();
		}

		$varsTarget = array();
		foreach ($arrayBlock as $key => $value) {
			if ($value['vars']['idTarget'] == $arr['idTarget']) {
				$varsTarget = $value;
				break;
			}
		}

		if (!(int) $varsTarget['vars']['flagSortUse']) {
			$this->_sendOld();
		}

/*unique*/
		if (!(int) $varsTarget['flagBtnUse']) {
			$this->_sendOld();
		}


		$strTitle = $arr['arrValue']['arr']['strTitle'];
		$arrayRows = $arr['varsItem']['varsFSRows'];
		foreach ($arrayRows as $key => $value) {
			$flagNum = $this->_checkTreeStrTitle(array(
				'vars'      => $value['jsonJgaapAccountTitle'. $arr['flagFS']],
				'strTarget' => $strTitle,
				'num'       => 0,
			));
			if ($flagNum) {
				$this->sendVars(array(
					'flag'    => 'strTitle',
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));
			}
		}

		$idAccountTitle = $arr['arrValue']['arr']['idAccountTitle'];
		$arrayIdAccountTitle = array($idAccountTitle);
		$arrayFSList = $arr['varsItem']['arrayFSList'];
		foreach ($arrayFSList as $keyFSList => $valueFSList) {
			$arrayIdAccountTitle[] = 'custom_' . $keyFSList . '_' . $idAccountTitle;
		}

		foreach ($arrayIdAccountTitle as $key => $value) {
			$this->_checkValueDetailIdAccountTitle(array(
				'flagFS'    => $arr['flagFS'],
				'strTarget' => $value,
			));
		}

		$arrayCheck = array();
		foreach ($arrayBlock as $key => $value) {
			$arrayCheck[$value['vars']['idTarget']] = $value;
		}
		$arrCommaIdAccountTitle = $classEscape->splitCommaArrayData(array('data' => $arr['arrValue']['arr']['arrCommaIdAccountTitle']));
		$array = $arrCommaIdAccountTitle;
		$num = 0;
		foreach ($array as $key => $value) {
			if ($value == 'insertPoint' && $varsRequest['query']['func'] == 'DetailAdd') {
				continue;

			} else {
				if (!$arrayCheck[$value]) {
					$this->_sendOld();
				}
				$num++;
			}
		}
		if ($num != count($arrayCheck)) {
			$this->_sendOld();
		}
	}

	/**
		(array(
			'arrValue'    => $arrValue,
			'varsItem'    => $varsItem,
			'flagFS'      => $flagFS,
			'vars'        => $vars,
			'idTarget'    => $idTarget,
			'flagDefault' => $flagDefault,
		))
	 */
	protected function _checkValueDetailEdit($arr)
	{
		global $classEscape;
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varAccount;
		global $varsRequest;

		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;
		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());

		$numFiscalPeriodTemp = '';
		if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
			$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] + 1;

		} elseif (preg_match("/^(tempNext)$/", $flagCurrentFlagNow)) {
			$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] - 1;
		}

		$varsFS = $arr['varsItem']['varsFS']['jsonJgaapAccountTitle'. $arr['flagFS']];
		$varsFS = $this->_getFlagUseLog(array(
			'vars'                  => $varsFS,
			'idTarget'              => $arr['idTarget'],
			'numFiscalPeriod'       => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'numFiscalPeriodTemp'   => $numFiscalPeriodTemp,
			'classCalcAccountTitle' => $this->_getClassCalc(array('flagType' => 'AccountTitle')),
			'flagFS'                => $arr['flagFS'],
		));

		$arrayBlock = $this->_getTreeBlock(array(
			'vars'     => $varsFS,
			'idTarget' => $arr['idTarget'],
			'arrNew'   => array(),
		));
		if (!$arrayBlock) {
			$this->_sendOld();
		}

		$varsTarget = array();
		foreach ($arrayBlock as $key => $value) {
			if ($value['vars']['idTarget'] == $arr['idTarget']) {
				$varsTarget = $value;
				break;
			}
		}

/*unique*/
		if (!(int) $varsTarget['flagBtnUse']) {
			$this->_sendOld();
		}

		$strTitle = $arr['arrValue']['arr']['strTitle'];
		$arrayRows = $arr['varsItem']['varsFSRows'];
		foreach ($arrayRows as $key => $value) {
			$flagNum = $this->_checkTreeStrTitle(array(
				'vars'      => $value['jsonJgaapAccountTitle'. $arr['flagFS']],
				'strTarget' => $strTitle,
				'num'       => 0,
				'strSelf'   => $varsTarget['strTitle'],
				'idSelf'    => $varsTarget['vars']['idTarget'],
			));
			if ($flagNum) {
				$this->sendVars(array(
					'flag'    => 'strTitle',
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));
			}
		}

		$idAccountTitle = $arr['arrValue']['arr']['idAccountTitle'];
		$flag = 0;
		if ((int) $varsTarget['flagDefault']) {
			if ($idAccountTitle != $varsTarget['vars']['idTarget']) {
				$flag = 1;
			}

		} else {
			$idAccountTitleCustom = 'custom_' . $arr['flagFS'] . '_' . $idAccountTitle;
			if ($idAccountTitleCustom != $varsTarget['vars']['idTarget']) {
				$flag = 1;
			}
		}

		$flagEdit = array();

		//idAccountTitle
		if ($flag) {
			if ((int) $varsTarget['flagDefault']) {
				$this->_sendOld();
			}
			$arrayIdAccountTitle = array($idAccountTitle);
			$arrayFSList = $arr['varsItem']['arrayFSList'];
			foreach ($arrayFSList as $keyFSList => $valueFSList) {
				$arrayIdAccountTitle[] = 'custom_' . $keyFSList . '_' . $idAccountTitle;
			}
			foreach ($arrayIdAccountTitle as $key => $value) {
				$this->_checkValueDetailIdAccountTitle(array(
					'flagFS'    => $arr['flagFS'],
					'strTarget' => $value,
					'strSelf'   => $arr['idTarget'],
				));
			}
			if ($varsTarget['vars']['flagUseAllLog']) {
				$this->_sendOld();
			}
			$flagEdit['idAccountTitle'] = 1;
		}

		//flagDebit
		$flagDebit = $arr['arrValue']['arr']['flagDebit'];
		if ($flagDebit != $varsTarget['vars']['flagDebit']) {
			if ((int) $varsTarget['flagDefault']) {
				$this->_sendOld();
			}
			if ($varsTarget['vars']['flagUseAllLogElse']) {
				$this->_sendOld();
			}
			$flagEdit['flagDebit'] = 1;
		}


		//$flagUse = $arr['arrValue']['arr']['flagUse'];

		$idAccountTitleJgaapFS = $arr['arrValue']['arr']['strAccountTitleJgaapFS'];
		if ($idAccountTitleJgaapFS != $varsTarget['vars']['idAccountTitleJgaapFS']) {
			if ($varsTarget['vars']['flagUseLogElse']
				|| ($varsTarget['vars']['flagUseLogElseTemp'] && $varsTarget['vars']['flagFS'] == 'BS')
			) {
				$this->_sendOld();
			}
			if ($idAccountTitle == 'profitBroughtForward'
				 || $idAccountTitle == 'accountsReceivables'
				 || $idAccountTitle == 'accountsPayables'
			) {
				$this->_sendOld();
			}
			$flagEdit['idAccountTitleJgaapFS'] = 1;
		}

		$arrayCheck = array();
		foreach ($arrayBlock as $key => $value) {
			$arrayCheck[$value['vars']['idTarget']] = 1;
		}

		$arrCommaIdAccountTitle = $classEscape->splitCommaArrayData(array('data' => $arr['arrValue']['arr']['arrCommaIdAccountTitle']));
		$array = $arrCommaIdAccountTitle;
		$num = 0;

		foreach ($array as $key => $value) {
			if (!$arrayCheck[$value]) {
				$this->_sendOld();
			}
			$num++;
		}

		if ($num != count($arrayCheck)) {
			$this->_sendOld();
		}

		if ($arr['varsItem']['flagTempNext']) {
			$idTarget = $arr['idTarget'];
			$varsTempPrev = $arr['varsItem']['arrAccountTitleTempPrev']['arrStrTitle'][$idTarget];

			if (!$varsTempPrev) {
				return $flagEdit;
			}
			if (!$arr['varsItem']['varsJgaapFSTempPrev']['arrStrTitle'][$arr['arrValue']['arr']['strAccountTitleJgaapFS']]) {
				$flagEdit['idAccountTitleJgaapFSInsert'] = 1;
			}

			if ($varsTempPrev['idAccountTitleJgaapFS'] != $arr['arrValue']['arr']['strAccountTitleJgaapFS']) {
				$flagEdit['idAccountTitleJgaapFSUpdate'] = 1;
			}
		}

		return $flagEdit;
	}


	//-----------------------------------------------------------
	// Code_Else_Plugin_Accounting_Jpn_AccountTitle overwrite _2012_Public
	//-----------------------------------------------------------

	protected $_extSelf = array(
		'idPreference' => 'accountTitleWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/2012/public/accountTitle.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/2012/public/accountTitle.php',
	);

	/**
		(array(
			'flag' => '',
			'vars' => $vars,
		))
	 */
	protected function _getVarsItem($arr)
	{
		global $classDb;
		global $varsAccount;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsConsumptionTax = $this->_getVarsConsumptionTax(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsTax = $this->_getVarsItemTax(array(
			'varsEntityNation'   => $varsEntityNation,
			'varsConsumptionTax' => $varsConsumptionTax,
			'numFiscalPeriod'    => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		//select form view prepare
		$str = 'jsonJgaapFS' . $arr['flag'];
		$varsFS[$str] = $this->_setTreeId(array(
			'idParent' => '',
			'vars'     => $varsFS[$str],
		));

		//select form view
		$varsJgaapFS = $this->_getVarsItemJgaapFS(array(
			'arrStrTitle'  => array(),
			'arrSelectTag' => array(),
			'vars'         => $varsFS[$str],
			'varsItem'     => $arr['vars']['varsItem'],
		));

		$arrayFSList = $this->_getFSList(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsFSItem = $this->_getVarsFSItem();

		$varsFSRows = $this->_getVarsFSRows(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$data = array(
			'varsFS'             => $varsFS,
			'varsFSRows'         => $varsFSRows,
			'varsTax'            => $varsTax,
			'varsConsumptionTax' => $varsConsumptionTax,
			'varsJgaapFS'        => $varsJgaapFS,
			'arrayFSList'        => $arrayFSList,
			'varsEntityNation'   => $varsEntityNation,
			'varsFSItem'         => $varsFSItem,
		);

		return $data;

	}

	/**
		(array(
			'arrStrTitle'  => array(),
			'vars'         => array(),
			'arrSelectTag' => array(),
		))
	 */
	protected function _getVarsItemJgaapFS($arr)
	{
		$arrSelectTag = &$arr['arrSelectTag'];
		$arrStrTitle = &$arr['arrStrTitle'];

		$array = &$arr['vars'];
		foreach ($array as $key => $value) {
			if ($value['strTitle'] == '') {
				$value['strTitle'] = $arr['varsItem']['strBlank'];
			}
			$arr['arrStrTitle'][$value['vars']['idTarget']] = $value['strTitle'];

			$arrLevel = preg_split("/-/", $value['id']);
			$num = count($arrLevel) - 1;
			$arrLevel = array();
			for ($i = 0 ; $i < $num; $i++) {
				$arrLevel[$i] = '';
			}
			$str =  ' ' . join('.', $arrLevel) . ' ';
			$strTitle = $str . $value['strTitle'];

			if (is_null($value['vars']['flagUse'])) {
				$arr['arrSelectTag'][] = array(
					'strTitle'     => $strTitle,
					'value'        => '',
					'flagDisabled' => 1,
				);

			} else {
				$arr['arrSelectTag'][] = array(
					'strTitle'     => $strTitle,
					'value'        => $value['vars']['idTarget'],
				);
			}

			if ($value['child']) {
				$data = $this->_getVarsItemJgaapFS(array(
					'vars'          => $array[$key]['child'],
					'arrSelectTag'  => $arr['arrSelectTag'],
					'arrStrTitle'   => $arr['arrStrTitle'],
					'varsItem'      => $arr['varsItem'],
					'idParent'      => $value['vars']['idTarget'],
				));
				$array[$key]['child'] = $data['vars'];
				$arrSelectTag =  $data['arrSelectTag'];
				$arrStrTitle =  $data['arrStrTitle'];
			}
		}

		return $arr;
	}


}
