<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_FixedAssetsPreference extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference' => 'fixedAssetsWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/fixedAssetsPreference.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/fixedAssetsPreference.php',
		'tplSum'       => 'else/plugin/accounting/html/fixedAssetsSum.html',
		'tplSumElse'   => 'else/plugin/accounting/html/fixedAssetsSumElse.html',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'select',
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

		$vars = $this->_getVarsJs();

		$json = json_encode($vars);
		$classSmarty->assign('varsLoad', $json);

		$classSmarty->assign('numNews', $this->getNumNews());
		$contents = $classSmarty->fetch($this->_extSelf['pathTplJs']);

		$this->sendJs(array(
			'data' => $contents,
		));

	}

	/**
	 *
	 */
	protected function _getVarsJs()
	{
		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_updateVars(
			$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail']
		);

		return $vars;
	}

	/**
	 *
	 */
	protected function _updateVars($vars)
	{
		foreach ($vars as $key => $value) {
			$idTarget = $value['vars']['idTarget'];
			$id = $value['id'];

			if (!preg_match( "/Window$/", $idTarget) && !$value['flagFoldUse']) {
				$method = '_updateVars' . ucwords($idTarget);

				if (method_exists($this, $method)) {

					$vars[$key] = $this->$method(array('vars' => $vars[$key]));
				}
			}

			if ($value['child']) {
				$vars[$key]['child'] = $this->_updateVars($vars[$key]['child']);
			}
		}

		return $vars;
	}


	/**
	 *
	 */
	protected function _iniNaviReload()
	{
		global $varsPluginAccountingPreference;
		global $varsRequest;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingPreference['stampUpdate'],
				'flagSearch'  => 0,
			));
		}

		$vars = $this->_getVarsJs();

		$this->sendValue(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
		));
	}


	/**
	 *
	 */
	protected function _iniDetailReload()
	{
		global $varsPluginAccountingPreference;
		global $varsRequest;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingPreference['stampUpdate'],
				'flagSearch'  => 0,
			));
		}

		$vars = $this->_getVarsJs();
		$varsTarget = $this->getVarsTarget(array(
			'vars' => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail']
		));
		if (!$varsTarget) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}

		$method = '_updateVars' . ucwords($varsTarget['vars']['idTarget']);

		if (method_exists($this, $method)) {
			$varsTarget = $this->$method(array('vars' => $varsTarget));
		}


		$this->sendValue(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $varsTarget,
		));
	}

	/**
	 *
	 */
	protected function _updateVarsSumList($arr)
	{
		global $classEscape;

		$vars = $arr['vars'];
		$arrayNewDetail = array();
		$array = $vars['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			$str = $classEscape->toLower(array('str' => $value['id']));
			$arrData = $value['arrayOption'];
			if ($value['id'] == 'DummyList') {
				$value['varsData'] = $this->_getVarsData(array(
					'vars' => $value,
				));
				$value['strComment'] = $this->_getHtml(array(
					'pathTpl' => $value['varsData']['pathTpl'],
					'varsStr' => $value['varsData']['varsStr'],
				));
			}
			$arrayNewDetail[] = $value;
		}
		$vars['vars']['varsDetail'] = $arrayNewDetail;

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllOutput'])) {
			$vars['vars']['varsEdit']['flagOutputUse'] = 0;
		}

		return $vars;
	}

	/**
		(array(

		))
	 */
	protected function _getVarsData($arr)
	{
		global $varsPluginAccountingAccount;

		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $numFiscalPeriod
		));

		$varsFixedAssets = $this->_getVarsFixedAssets(array(
			'numFiscalPeriod' => $numFiscalPeriod
		));

		$varsStampFiscalPeriod = $this->_getVarsStampFiscalPeriod(array(
			'numFiscalPeriod'  => $numFiscalPeriod,
			'varsEntityNation' => $varsEntityNation,
		));

		$pathTpl = $this->_extSelf['tplSum'];
		$varsStr = $this->_getVarsSumList(array(
			'varsTmpl'               => $arr['vars']['varsTmpl']['varsCorporation'],
			'varsItem'               => $arr['vars']['varsItem']['varsCorporation'],
			'varsFixedAssets'        => $varsFixedAssets,
			'varsStampFiscalPeriod'  => $varsStampFiscalPeriod,
			'varsEntityNation'       => $varsEntityNation,
			'numFiscalPeriod'        => $numFiscalPeriod,
		));

		$data = array(
			'pathTpl' => $pathTpl,
			'varsStr' => $varsStr,
		);

		return $data;
	}

	/**
		(array(
				'varsTmpl'               => $arr['vars']['varsTmpl']['varsCorporation'],
				'varsItem'               => $arr['vars']['varsItem']['varsCorporation'],
				'varsFixedAssets'        => $varsFixedAssets,
				'varsStampFiscalPeriod'  => $varsStampFiscalPeriod,
				'varsEntityNation'       => $varsEntityNation,
				'numFiscalPeriod'        => $numFiscalPeriod,
		))
	 */
	protected function _getVarsSumList($arr)
	{
		$numValueDep = 0;
		$numValueNetClosingSum = 0;
		$varsStr = $arr['varsStr'];

		if (!$arr['varsFixedAssets']['jsonDepSum']) {
			$arrRows = array();
			$arrRows[] = $this->_getVarsBlank(array(
				'varsTmpl'              => $arr['varsTmpl'],
				'varsItem'              => $arr['varsItem'],
				'numFiscalPeriod'       => $arr['numFiscalPeriod'],
				'varsStampFiscalPeriod' => $arr['varsStampFiscalPeriod'],
			));
			$arr['varsTmpl']['numValueNetClosingSum'] = $numValueNetClosingSum;
			$arr['varsTmpl']['numValueNetClosingSumComma'] = number_format($numValueNetClosingSum);
			$arr['varsTmpl']['numValueDep'] = $numValueDep;
			$arr['varsTmpl']['numValueDepComma'] = number_format($numValueDep);
			$arr['varsTmpl']['arrRows'] = $arrRows;

			return $arr['varsTmpl'];
		}

		$arrRows = array();

		$array = $arr['varsFixedAssets']['jsonDepSum'];
		krsort($array);
		foreach ($array as $key => $value) {
			$numFiscalPeriodStart = $key;
			$rowData = array();

			$rowData['numValue'] = $value['numValue'];
			$rowData['numValueCompression'] = $value['numValueCompression'];
			$rowData['numValueNet'] = $rowData['numValue'] - $rowData['numValueCompression'];

			$varsEntityNation = $this->_getVarsEntityNation(array(
				'numFiscalPeriod' => $numFiscalPeriodStart
			));

			$varsStampFiscalPeriod = $this->_getVarsStampFiscalPeriod(array(
				'numFiscalPeriod'  => $numFiscalPeriodStart,
				'varsEntityNation' => $varsEntityNation,
			));

			$rowData['strPeriod'] = $this->_getVarsStrPeriod(array(
				'numFiscalPeriod'       => $numFiscalPeriodStart,
				'varsStampFiscalPeriod' => $varsStampFiscalPeriod,
				'varsTmpl'              => $arr['varsTmpl'],
				'varsItem'              => $arr['varsItem'],
			));

			$rowData['numValueDepLimit'] = $value['varsDetail'][$arr['numFiscalPeriod']]['numValueDepLimit'];
			$numValueDep += $rowData['numValueDepLimit'];

			$sum = 0;
			$arrayDetail = $value['varsDetail'];
			foreach ($arrayDetail as $keyDetail => $valueDetail) {
				if ($arr['numFiscalPeriod'] >= $keyDetail) {
					$sum += $valueDetail['numValueDepLimit'];
				}
			}
			$rowData['numValueNetClosing'] = $rowData['numValueNet'] - $sum;
			$rowData['numValueNetClosing'] = ($rowData['numValueNetClosing'] < 0)? 0 : $rowData['numValueNetClosing'];
			$numValueNetClosingSum += $rowData['numValueNetClosing'];
			if (!$value['varsDetail'][$arr['numFiscalPeriod']]) {
				continue;
			}
			$rowData['numValueNet'] = $rowData['numValue'] - $rowData['numValueCompression'];
			$arrayItem = $arr['varsItem'];
			foreach ($arrayItem as $keyItem => $valueItem) {
				if (preg_match("/^numValue/", $keyItem)) {
					$rowData[$keyItem . 'Comma'] = number_format($rowData[$keyItem]);
				}
			}
			$arrRows[] = $rowData;
		}

		$arr['varsTmpl']['numValueNetClosingSum'] = $numValueNetClosingSum;
		$arr['varsTmpl']['numValueNetClosingSumComma'] = number_format($numValueNetClosingSum);
		$arr['varsTmpl']['numValueDep'] = $numValueDep;
		$arr['varsTmpl']['numValueDepComma'] = number_format($numValueDep);
		$arr['varsTmpl']['arrRows'] = $arrRows;

		return $arr['varsTmpl'];
	}

	/**
	 */
	protected function _getVarsBlank($arr)
	{
		$rowData = array();
		$rowData['strPeriod'] = $this->_getVarsStrPeriod(array(
			'numFiscalPeriod'       => $arr['numFiscalPeriod'],
			'varsStampFiscalPeriod' => $arr['varsStampFiscalPeriod'],
			'varsTmpl'               => $arr['varsTmpl'],
			'varsItem'               => $arr['varsItem'],
		));

		$array = $arr['varsItem'];
		foreach ($array as $key => $value) {
			if (preg_match("/^numValue/", $key)) {
				$rowData[$key] = 0;
				$rowData[$key . 'Comma'] = 0;
			}
		}

		return $rowData;
	}

	/**
	 */
	protected function _getVarsStrPeriod($arr)
	{
		$varsPeriod = $this->_getVarsFiscalPeriod(array(
			'flagFiscalPeriod' => 'f1',
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
		));

		/*20190401 start*/
		$str = '('
			//. $arr['varsTmpl']['strHeisei']
			. $varsPeriod['strStartNengoYear']
			. ')'
			. ' '
			. $varsPeriod['numStartYear']
			. '/'
			. $varsPeriod['numStartMonth']
			. '/'
			. 1;
		$str .= ' ~ ';

		$str .= '('
			//. $arr['varsTmpl']['strHeisei']
			. $varsPeriod['strEndNengoYear']
			. ')'
			. ' '
			. $varsPeriod['numEndYear']
			. '/'
			. $varsPeriod['numEndMonth']
			. '/'
			. $arr['varsTmpl']['strPeriodEnd'];
		/*20190401 end*/



		return $str;
	}

	/**
		(array(
			'varsStr' => $arrayStr
		))
	 */
	protected function _getHtml($arr)
	{
		global $classSmarty;

		$pathTpl = $arr['pathTpl'];

		$array = $arr['varsStr'];
		foreach ($array as $key => $value) {
			$classSmarty->assign($key, $value);
		}
		$path = $pathTpl;
		$data = $classSmarty->fetch($path);

		return $data;
	}

	/**
	 *
	 */
	protected function _updateVarsFlagDepWrite($arr)
	{
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;
		global $classEscape;

		$vars = $arr['vars'];

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent']
		));

		$varsFixedAssets = $this->_getVarsFixedAssets(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent']
		));

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done)$/", $flagCurrentFlagNow)) {
			$vars['vars']['varsBtn'] = array();
		}

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'])) {
			$vars['vars']['varsBtn'] = array();
		}

		$arrayNewDetail = array();
		$array = $vars['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			$str = $classEscape->toLower(array('str' => $value['id']));
			$arrData = $value['arrayOption'];
			if ($value['id'] == 'FlagDepWrite') {
				$value = $this->_updateVarsFlagDepWriteFlagFiscalPeriod(array(
					'vars'             => $value,
					'varsEntityNation' => $varsEntityNation,
				));

			} elseif ($value['id'] == 'FlagFractionRatioOperate') {
				if ($varsEntityNation['flagCorporation'] == 1) {
					continue;
				}

			} elseif ($value['id'] == 'NumRatioOperateDepSum') {
				if ($varsEntityNation['flagCorporation'] == 1) {
					continue;
				}
			}

			$arrStrTitle = array();
			foreach ($arrData as $keyData => $valueData) {
				$arrStrTitle[$valueData['value']] = $valueData['strTitle'];
			}
			$value['value'] = $varsFixedAssets[$str];

			if (preg_match("/^(done)$/", $flagCurrentFlagNow)) {
				$value['flagDisabled'] = 1;
				$value['strExplain'] = $value['varsTmpl']['strDone'];
				$str = $arrStrTitle[$value['value']];
				$value['strExplain']
							= str_replace("<%replace%>", $str, $value['strExplain']);

			} else {
				$value['strExplain'] = $value['varsTmpl']['strNormal'];
			}
			$arrayNewDetail[] = $value;
		}
		$vars['vars']['varsDetail'] = $arrayNewDetail;

		return $vars;
	}

	/**
		(array(
			'vars'             => $value,
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _updateVarsFlagDepWriteFlagFiscalPeriod($arr)
	{
		$varsEntityNation = $arr['varsEntityNation'];

		$arrayNew = array();
		$array = $arr['vars']['varsTmpl']['arrayOption'];
		foreach ($array as $key => $value) {
			if ($varsEntityNation['numFiscalTermMonth'] == 12 && !PLUGIN_ACCOUNTING_FLAG_CORPORATION) {
				$arrayNew[] = $value;

			} else {
				if (preg_match( "/^(f1)$/", $value['value'])) {
					$arrayNew[] = $value;
				}
			}
		}
		$arr['vars']['varsTmpl']['arrayOption'] = $arrayNew;

		$arrayOption = array();
		if ($varsEntityNation['numFiscalTermMonth'] == 12) {
			$arrayOption = $arr['vars']['varsTmpl']['arrayOption'];

		} else {
			$arrayOption[] = $arr['vars']['varsTmpl']['varsPeriod'];
		}

		$numMonth = (int) $varsEntityNation['numFiscalBeginningMonth'];
		$numEnd = (int) $varsEntityNation['numFiscalTermMonth'];
		for ($i = 0; $i < $numEnd; $i++) {
			$data = array(
				'strTitle' => $numMonth . $arr['vars']['varsTmpl']['strMonth'],
				'value'    => $numMonth,
			);
			$arrayOption[] = $data;
			$numMonth++;
			if ($numMonth > 12) {
				$numMonth = 1;
			}
		}
		$arr['vars']['arrayOption'] = $arrayOption;

		return $arr['vars'];
	}

		/**
	 *
	 */
	protected function _iniDetailOutput()
	{
		global $classEscape;
		global $classRequest;

		global $varsRequest;

		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllOutput'])) {
			$this->_send403Output();
		}

		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];

		$vars = $this->_getVarsJs();

		$varsTarget = $this->getVarsTarget(array(
			'vars' => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail']
		));

		if (!$varsTarget) {
			$this->_send404Output();
		}

		$data = array();
		$array = $varsTarget['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			$str = $classEscape->toLower(array('str' => $value['id']));
			if ($value['id'] == 'DummyList') {
				$value['varsData'] = $this->_getVarsData(array(
					'vars' => $value,
				));
				$data = $value;
				break;
			}
		}

		$varsFlag = array(
			'flagFiscalPeriod' => 'f1',
		);
		$text = $this->_getCsv(array(
			'varsFlag' => $varsFlag,
			'vars'     => $data,
		));

		$text = mb_convert_encoding($text, 'sjis', 'utf8');

		$strFileName = $this->_getFileTitle(array(
			'strMenu'     => $vars['varsItem']['varsMenu']['strList'],
			'strFileType' => 'csv',
		));

		$classRequest->output(array(
			'text'         => $text,
			'strFileType'  => 'csv',
			'strFileName'  => $strFileName,
		));
	}

	/**
		'varsFlag' => $varsFlag,
		'vars'     => $vars,
		'varsItem' => $varsItem,
	 */
	protected function _getCsv($arr)
	{
		global $classFile;

		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrayCsv = array();

		//strEntity
		$strEntityRep = $varsPluginAccountingEntity[$idEntity]['strTitle'];
		$strEntity = str_replace('<%replace%>', $strEntityRep, $arr['vars']['varsItem']['strEntity']);
		$arrayCsv[] = array($strEntity);

		//strNum
		if (preg_match("/^f1$/", $arr['varsFlag']['flagFiscalPeriod'])) {
			$strNumRep = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
			$strNum = str_replace('<%replace%>', $strNumRep, $arr['vars']['varsItem']['strNum']);
			$arrayCsv[] = array($strNum);
		}

		//strPeriod
		$varsPeriod = $this->_getVarsFiscalPeriod(array(
			'flagFiscalPeriod' => $arr['varsFlag']['flagFiscalPeriod'],
			'numFiscalPeriod'  => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));
		/*20190401 start*/
/*
		$str = $arr['vars']['varsItem']['strPeriod'];
		$strPeriod = str_replace('<%strStartHeisei%>', $varsPeriod['numStartHeisei'], $str);
		$strPeriod = str_replace('<%strEndHeisei%>', $varsPeriod['numEndHeisei'], $strPeriod);
		$strPeriod = str_replace('<%strStartMonth%>', $varsPeriod['numStartMonth'], $strPeriod);
		$strPeriod = str_replace('<%strEndMonth%>', $varsPeriod['numEndMonth'], $strPeriod);
		*/
		$str = $arr['vars']['varsItem']['strPeriod20190401'];
		$strPeriod = str_replace('<%strStartNengoYear%>', $varsPeriod['strStartNengoYear'], $str);
		$strPeriod = str_replace('<%strEndNengoYear%>', $varsPeriod['strEndNengoYear'], $strPeriod);
		$strPeriod = str_replace('<%strStartMonth%>', $varsPeriod['numStartMonth'], $strPeriod);
		$strPeriod = str_replace('<%strEndMonth%>', $varsPeriod['numEndMonth'], $strPeriod);
		/*20190401 end*/


		$arrayCsv[] = array($strPeriod);

		//strUnit
		$arrayCsv[] = array($arr['vars']['varsItem']['strUnit']);

		$dataColumn = array();
		$array = $arr['vars']['varsItem']['varsCorporation'];
		foreach ($array as $key => $value) {
			$dataColumn[] = $value;
		}
		$arrayCsv[] = $dataColumn;

		$array = $arr['vars']['varsData']['varsStr']['arrRows'];
		foreach ($array as $key => $value) {
			$dataRow = array();
			$arrayChild = $arr['vars']['varsItem']['varsCorporation'];
			foreach ($arrayChild as $keyChild => $valueChild) {
				$dataRow[] = $value[$keyChild];
			}
			$arrayCsv[] = $dataRow;
		}
		$numValueDep = $arr['vars']['varsData']['varsStr']['numValueDep'];
		$numValueNetClosingSum = $arr['vars']['varsData']['varsStr']['numValueNetClosingSum'];
		$arrayCsv[] = array('','','','',$numValueDep, $numValueNetClosingSum);

		$text = $classFile->getCsvText(array(
			'delimiter' => ',',
			'rows'      => $arrayCsv,
		));

		return $text;
	}


	/**
	 *
	 */
	protected function _iniDetailEdit()
	{
		global $classInit;
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsRequest;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'])) {
			$this->_sendOld();
		}

		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];

		$vars = $this->_getVarsJs();

		$varsTarget = $this->getVarsTarget(array(
			'vars' => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail']
		));
		if (!$varsTarget) {
			$this->sendValue(array(
				'flag'    => 8,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}

		$varsTarget['vars']['varsDetail'] = $this->getValue(array(
			'vars' => $varsTarget['vars']['varsDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget['vars']['varsDetail']
		));

		try {
			$dbh->beginTransaction();

			$this->_updateDb($arrValue);

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$method = '_updateVars' . ucwords($varsTarget['vars']['idTarget']);
		if (method_exists($this, $method)) {
			$varsTarget = $this->$method(array(
				'vars'     => $varsTarget,
				'flagDone' => 1,
			));
		}

		$this->sendValue(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $varsTarget,
		));
	}

	/**
	 *
	 */
	protected function _updateDb($arr)
	{
		global $varsRequest;

		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];
		$method = '_updateDb' . ucwords($idTarget);
		if (method_exists($this, $method)) {
			$this->$method($arr);
		}
	}

	/**
		(array(

		))
	 */
	protected function _updateDbFlagDepWrite($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;
		global $classPluginAccountingInit;
		global $varsPluginAccountingEntity;

		if (!$this->_checkCurrent()) {
			$this->_sendOld();
		}

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable' => 'accountingFixedAssets' . $strNation,
			'arrColumn' => $arr['arrColumn'],
			'flagAnd'  => 1,
			'arrWhere' => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idEntity',
					'flagCondition' => 'eq',
					'value'         => $varsPluginAccountingAccount['idEntityCurrent'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'numFiscalPeriod',
					'flagCondition' => 'eq',
					'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				),
			),
			'arrValue'  => $arr['arrValue'],
		));

		$this->_updateDbPreferenceStamp(array('strColumn' => 'fixedAssets'));
	}
}
