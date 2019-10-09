<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_BanksOutput extends Code_Else_Plugin_Accounting_Jpn_Banks
{
	protected $_childSelf = array(

	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;

		$this->_checkEntity();

		if ($varsRequest['query']['func']) {
			$method = '_ini' . $varsRequest['query']['func'];
			if (method_exists($this, $method)) {
				$this->$method();

			} else {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
				}
				exit;
			}
		}
		exit;
	}

	/**
	 *金融機関別明細
	 */
	protected function _iniListOutput()
	{
		global $classRequest;

		global $varsRequest;
		global $varsPluginAccountingPreference;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagMyOutput'] || $varsAuthority['flagAllOutput'])) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__);
			}
			exit;
		}

		$varsFlag = array(
			'flagType' => $varsRequest['query']['flagType'],
		);

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$array = $varsItem;
		foreach ($array as $key => $value) {
			$vars['varsItem'][$key] = $value;
		}

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$this->checkSearch(array(
			'arrOrder' => $vars['portal']['varsNavi']['search']['varsDetail']['varsSearchSort'],
			'arrWhere' => $vars['portal']['varsNavi']['search']['varsDetail']['varsSearchItem'],
		));

		if (!$varsRequest['query']['jsonSearch']['ph']['arrOrder']) {
			$varsRequest['query']['jsonSearch']['ph']['arrOrder']= array(
				'strColumn' => 'id',
				'flagDesc'  => 1,
			);
		}

		$rows = $this->getSearch(array(
			'idModule'   => 'accounting',
			'strTable'   => 'accountingLogBanks',
			'arrJoin'    => array(),
			'arrOrder'   => $varsRequest['query']['jsonSearch']['ph']['arrOrder'],
			'insCurrent' => $this,
			'arrWhere'   => $varsRequest['query']['jsonSearch']['ph']['arrWhere'],
		));

		$text = $this->_getCsv(array(
			'vars'     => $vars,
			'rows'     => $rows,
			'varsFlag' => $varsFlag
		));

		$strMenu = $vars['varsItem']['varsOutput']['strTitleItem'];
		if ($varsFlag['flagType'] == 'listAll') {
			$strMenu = $vars['varsItem']['varsOutput']['strTitleList'];
		}

		$strFileName = $this->_getFileTitle(array(
			'strMenu'     => $strMenu,
			'strFileType' => 'csv',
		));
		$text = mb_convert_encoding($text, 'sjis', 'utf8');
		$classRequest->output(array(
			'text'         => $text,
			'strFileType'  => 'csv',
			'strFileName'  => $strFileName,
		));
	}

	/**
		(array(
			'vars' => $vars,
			'rows' => $rows,
		))
	 */
	protected function _getCsv($arr)
	{
		global $classFile;

		$varsData = array();
		$varsData = $this->_getVarsStatus(array(
			'vars' => $arr['vars'],
		));

		$varsData['arrLoop'] = $this->_getVarsLoop(array(
			'vars' => $arr['vars'],
			'rows' => $arr['rows'],
		));

		$arrayCsv = array();
		$array = $this->_getVarsStatusCsv(array(
			'vars' => $varsData,
		));
		foreach ($array as $key => $value) {
			$arrayCsv[] = $value;
		}

		//'strTitleList' => '金融機関別明細', 'strTitleItem' => '日付別明細',
		if ($arr['varsFlag']['flagType'] == 'listAll') {
			$array = $this->_getVarsLoopCsvList(array(
				'varsData' => $varsData,
				'vars'     => $arr['vars'],
			));

		} elseif ($arr['varsFlag']['flagType'] == 'itemAll') {
			$array = $this->_getVarsLoopCsvItem(array(
				'varsData' => $varsData,
				'vars'     => $arr['vars'],
			));
		}
		foreach ($array as $key => $value) {
			$arrayCsv[] = $value;
		}

		$text = $classFile->getCsvText(array(
			'delimiter' => ',',
			'rows'      => $arrayCsv,
		));

		return $text;
	}

	/**
		(array(
			'vars'     => $arr['vars'],
			'flagVars' => 0,
		))
	 */
	protected function _getVarsStatus($arr)
	{
		global $varsRequest;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$vars = &$arr['vars'];

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];

		$varsData = $vars['varsItem']['varsOutput'];

		//strEntity
		$strEntity = $varsPluginAccountingEntity[$idEntity]['strTitle'];
		$varsData['strEntityExt'] = str_replace('<%replace%>', $strEntity, $vars['varsItem']['varsOutput']['strEntityExt']);
		$varsData['strEntity'] = $strEntity;

		//strNum
		$strNumRep = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$varsData['strNum'] = str_replace('<%replace%>', $strNumRep, $vars['varsItem']['varsOutput']['strNum']);
		$varsData['strNumExt'] = str_replace('<%replace%>', $strNumRep, $vars['varsItem']['varsOutput']['strNumExt']);

		//strPeriod
		$varsPeriod = $this->_getVarsFiscalPeriod(array(
			'flagFiscalPeriod' => 'f1',
			'numFiscalPeriod'  => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));
		$str = $vars['varsItem']['varsOutput']['strPeriodExt'];
		$strPeriod = str_replace('<%strStartHeisei%>', $varsPeriod['numStartHeisei'], $str);
		$strPeriod = str_replace('<%strEndHeisei%>', $varsPeriod['numEndHeisei'], $strPeriod);
		$strPeriod = str_replace('<%strStartMonth%>', $varsPeriod['numStartMonth'], $strPeriod);
		$strPeriod = str_replace('<%strEndMonth%>', $varsPeriod['numEndMonth'], $strPeriod);
		$varsData['strPeriodExt'] = $strPeriod;

		return $varsData;
	}

	/**
	 *
	 */
	protected function _getVarsLoop($arr)
	{
		global $classTime;
		global $classEscape;

		$vars = &$arr['vars'];
		$array = $arr['rows']['arrRows'];
		$arrayData = array();
		foreach ($array as $key => $value) {

			$rowData = $value;
			$arrDate = $classTime->getLocal(array('stamp' => $value['stampBook']));

			//date
			$rowData['strDateYear'] = $arrDate['strYear'] . '/' . $arrDate['strMonth'] . '/' . $arrDate['strDate'];

			//status
			if ((int) $value['flagRemove']) {
				$rowData['strStatus'] = $vars['varsItem']['strRemoveFake'];

			} elseif ((int) $value['flagCaution']) {
				$rowData['strStatus'] = $vars['varsItem']['strCaution'];

			} else {
				$rowData['strStatus'] = $vars['varsItem']['strDone'];
			}

			$varsBanksAccount = $vars['varsItem']['varsBanksAccountList']['arrStrTitle'][$value['idLogAccount']];
			$rowData['strBank'] = $vars['varsItem']['varsBanksList'][$varsBanksAccount['flagBank']]['strTitle'];
			$rowData['strBankAccount'] = str_replace(',', $vars['varsItem']['strEscape'], $varsBanksAccount['strTitle']);
			$rowData['strTitle'] = str_replace(',', $vars['varsItem']['strEscape'], $value['strTitle']);
			$arrayData[] = $rowData;
		}

		return $arrayData;
	}

	/**
		(array(
			'vars' => $varsData,
		))
	 */
	protected function _getVarsStatusCsv($arr)
	{
		$vars = &$arr['vars'];

		$arrayCsv = array();
		$arrayCsv[] = array($vars['strEntityExt']);
		$arrayCsv[] = array($vars['strNumExt']);
		$arrayCsv[] = array($vars['strPeriodExt']);

		return $arrayCsv;
	}

	/**
		(array(
			'vars' => $varsLoop,
		))

		金融機関別明細
	 */
	protected function _getVarsLoopCsvList($arr)
	{
		//'日付', '状態', '摘要', '入金', '出金', '口座残高', '担当者通番', '通番'
		$arrayCsv = array();
		$array = $arr['varsData']['arrLoop'];
		$arrayBank = array();
		foreach ($array as $key => $value) {
			$arrayBank[$value['idLogAccount']][] = $value;
		}

		foreach ($arrayBank as $keyBank => $valueBank) {
			$array = $valueBank;
			$num = 0;
			foreach ($array as $key => $value) {
				if ($num == 0) {
					$rowData = array();
					$rowData[] = $value['strBank'];
					$rowData[] = $value['strBankAccount'];
					$arrayCsv[] = $rowData;
					$arrayCsv[] = $arr['vars']['varsItem']['arrColumnList'];
				}
				$num++;
				$rowData = array();
				$rowData[] = $value['strDateYear'];
				$rowData[] = $value['strStatus'];
				$rowData[] = $value['strTitle'];
				$rowData[] = $value['numValueIn'];
				$rowData[] = $value['numValueOut'];
				$rowData[] = $value['numBalance'];
				$rowData[] = $value['idAccount'];
				$rowData[] = $value['idLogBanks'];
				$arrayCsv[] = $rowData;
			}
		}

		return $arrayCsv;
	}

	/**
		(array(
			'vars' => $varsLoop,
		))
		日付別明細
	 */
	protected function _getVarsLoopCsvItem($arr)
	{
		$arrayCsv = array();
		//'日付', '状態', '金融機関', '口座', '摘要', '入金', '出金', '口座残高', '担当者通番', '通番'
		$arrayCsv[] = $arr['vars']['varsItem']['arrColumnItem'];
		$array = $arr['varsData']['arrLoop'];
		foreach ($array as $key => $value) {
			$rowData = array();
			$rowData[] = $value['strDateYear'];
			$rowData[] = $value['strStatus'];
			$rowData[] = $value['strBank'];
			$rowData[] = $value['strBankAccount'];
			$rowData[] = $value['strTitle'];
			$rowData[] = $value['numValueIn'];
			$rowData[] = $value['numValueOut'];
			$rowData[] = $value['numBalance'];
			$rowData[] = $value['idAccount'];
			$rowData[] = $value['idLogBanks'];
			$arrayCsv[] = $rowData;
		}

		return $arrayCsv;
	}
}
