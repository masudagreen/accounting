<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_2012_DetailedAccount_LoansReceivablePreference extends Code_Else_Plugin_Accounting_Jpn_DetailedAccount
{
	protected $_extSelf = array(
		'idPreference' => 'detailedAccountLoansReceivableWindow',
		'flagReport'   => '2012',
		'flagDetail'   => 'loansReceivable',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/2012/detailedAccount/loansReceivablePreference.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/2012/detailedAccount/preference.php',
		'pathItem'     => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/2012/detailedAccount/loansReceivableItem.php',
		'pathTplHtml'  => 'else/plugin/accounting/html/2012/detailedAccount/<%replace%>.html',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;
		global $classCheck;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

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

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done|tempNext)$/", $flagCurrentFlagNow)) {
			$this->_sendOld();
		}

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

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_updateVars(array(
			'vars'     => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
			'varsItem' => $varsItem,
		));

		$json = json_encode($vars);
		$classSmarty->assign('varsLoad', $json);

		$classSmarty->assign('numNews', $this->getNumNews());
		$contents = $classSmarty->fetch($this->_extSelf['pathTplJs']);

		$this->sendJs(array(
			'data' => $contents,
		));

	}

	/**
		(array(
			'vars'     => $vars,
		))
	 */
	protected function _getVarsItem($arr)
	{
		global $varsPluginAccountingAccount;

		$arrSubAccountTitle = $this->_getVarsSubAccountTitle(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrAccountTitle = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'numFiscalPeriod'    => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsFSValue = $this->_getVarsFSValue(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrayFSList = $this->_getFSList(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsPreference = $this->_getVarsPreference(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagReport'      => $this->_extSelf['flagReport'],
			'flagDetail'      => $this->_extSelf['flagDetail'],
		));

		$varsCommon = $this->getVars(array(
			'path' => $this->_extSelf['pathItem'],
		));

		$data = array(
			'flagMenu'           => 'detail',
			'varsCommon'         => $varsCommon,
			'arrayFSList'        => $arrayFSList,
			'varsPreference'     => $varsPreference,
			'varsFSValue'        => $varsFSValue,
			'arrAccountTitle'    => $arrAccountTitle,
			'arrSubAccountTitle' => $arrSubAccountTitle,
		);

		return $data;
	}

	/**
	 *
	 */
	protected function _getVarsJs()
	{
		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		return $vars;
	}

	/**
	 *
	 */
	protected function _updateVars($arr)
	{
		$vars = $arr['vars'];
		foreach ($vars as $key => $value) {
			$idTarget = $value['vars']['idTarget'];
			$id = $value['id'];

			if (!preg_match( "/Window$/", $idTarget) && !$value['flagFoldUse']) {
				$method = '_updateVars' . ucwords($idTarget);
				if (method_exists($this, $method)) {
					$vars[$key] = $this->$method(array(
						'vars'     => $vars[$key],
						'varsItem' => $arr['varsItem'],
					));
				}
			}

			if ($value['child']) {
				$vars[$key]['child'] = $this->_updateVars(array(
					'vars'     => $vars[$key]['child'],
					'varsItem' => $arr['varsItem'],
				));
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

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_updateVars(array(
			'vars'     => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
			'varsItem' => $varsItem,
		));

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
		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$method = '_updateVars' . ucwords($varsTarget['vars']['idTarget']);
		if (method_exists($this, $method)) {
			$varsTarget = $this->$method(array(
				'vars'     => $varsTarget,
				'varsItem' => $varsItem,
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
	protected function _updateVarsNumPageMax($arr)
	{
		global $varsPluginAccountingAccount;

		$varsPreference = $arr['varsItem']['varsPreference'];
		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		$vars = $arr['vars'];
		$arrayNewDetail = array();
		$array = $vars['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'NumPageMax') {
				$value['value'] = $varsPreference['jsonData']['numPageMax'];
				$value['strExplain'] = $value['varsTmpl']['strNormal'];
				if (preg_match("/^(done)$/", $flagCurrentFlagNow)) {
					$value['strExplain'] = $value['varsTmpl']['strPast'];
					$value['flagDisabled'] = 1;
				}
			}
			$arrayNewDetail[] = $value;
		}

		$vars['vars']['varsDetail'] = $arrayNewDetail;
		if (preg_match("/^(done)$/", $flagCurrentFlagNow)) {
			$vars['vars']['varsBtn'] = array();
		}

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'])) {
			$vars['vars']['varsBtn'] = array();
		}

		return $vars;
	}

	/**
	 *
	 */
	protected function _updateVarsAnalyzeOld($arr)
	{
		global $classEscape;
		global $varsPluginAccountingAccount;

		$varsPage = $this->_getVarsSave(array(
			'numFiscalPeriod'    => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagReport'         => $this->_extSelf['flagReport'],
			'flagDetail'         => $this->_extSelf['flagDetail'],
			'flagRows'           => 1,
		));

		$flagMenu = $arr['varsItem']['flagMenu'];
		$varsData = $arr['varsItem']['varsCommon']['varsStr']['analyze'];
		$varsList = array();

		$array = $varsPage;
		foreach ($array as $key => $value) {
			if ($value['numPage'] == 0) {
				continue;
			}
			$varsList = $this->_checkDetailVarsAnalyzeOld(array(
				'varsItem'     => $arr['varsItem'],
				'varsData'     => $value['jsonData'][$flagMenu],
				'varsList'     => $varsList,
				'numPage'      => $value['numPage'],
			));
		}

		$varsData['arrRowsOld'] = $varsList;
		if ($varsData['arrRowsOld']) {
			$varsData['flagOld'] = 1;
		}
		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done)$/", $flagCurrentFlagNow)) {
			$varsData['flagOld'] = 0;
			$varsData['flagDone'] = 1;
		}
		$vars = $arr['vars'];
		$arrayNewDetail = array();
		$array = $vars['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'AnalyzeOld') {
				$value['strComment'] = $this->_getHtml(array(
					'strFile' => $value['id'],
					'varsStr' => $varsData,
				));
			}
			$arrayNewDetail[] = $value;
		}
		$vars['vars']['varsDetail'] = $arrayNewDetail;

		return $vars;
	}

	/**

	 */
	protected function _checkDetailVarsAnalyzeOld($arr)
	{
		global $varsPluginAccountingAccount;

		$arrayFSList = $arr['varsItem']['arrayFSList'];
		$varsFSValue = $arr['varsItem']['varsFSValue'];
		$flagMenu = $arr['varsItem']['flagMenu'];
		$arrStrTitle = $arr['varsItem']['arrAccountTitle']['arrStrTitle'];
		$arrStrTitle['none']['strTitleFS'] = 'dummy';
		$arrSubStrTitle = $arr['varsItem']['arrSubAccountTitle']['arrStrTitle'];

		$flagDebit = $arr['varsItem']['varsCommon']['varsStr'][$flagMenu]['flagDebit'];
		$strBlank = $arr['varsItem']['varsCommon']['varsStr'][$flagMenu]['strBlank'];
		$numEnd = $arr['varsItem']['varsCommon']['varsStr'][$flagMenu]['numRows'];

		$varsData = $arr['varsData'];
		for ($i = 1; $i <= $numEnd; $i++) {
			$idAccountTitle = $varsData['valueSelectIdAccountTitle' . $i];
			$str = $arrStrTitle[$idAccountTitle]['strTitleFS'];
			if (is_null($str) || $idAccountTitle == 'none') {
				continue;
			}
			$arrSubStrTitle[$idAccountTitle]['0']['strTitle'] = 'dummy';
			$idSubAccountTitle = $varsData['valueSelectIdSubAccountTitle' . $i];
			$str = $arrSubStrTitle[$idAccountTitle][$idSubAccountTitle]['strTitle'];
			if (is_null($str)) {
				continue;
			}

			$arrId = array();
			if ($idSubAccountTitle == 0) {
				$sumValue = 0;
				$array = $arrayFSList;
				foreach ($array as $key => $value) {
					if ($varsFSValue['jsonJgaapAccountTitle' . $key]['f1'][$idAccountTitle]) {
						$sumValue = $varsFSValue['jsonJgaapAccountTitle' . $key]['f1'][$idAccountTitle]['sumNext'];
						break;
					}
				}
				$sumSubValue = 0;
				$arrId = $arrSubStrTitle[$idAccountTitle];
				if ($arrId) {
					$sumSubValue = $this->_getVarsSubValue(array(
						'arrId'           => $arrId,
						'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					));

				}
				$num = $sumValue - $sumSubValue;
				if ($flagDebit != $arrStrTitle[$idAccountTitle]['flagDebit']) {
					$num *= -1;
				}

				$numUpdate = $num;
				$numValue = $varsData['valueTextValue' . $i];
				if (is_null($numValue) || $numValue == '') {
					$numValue  = 0;
				}
				if ($numUpdate != $numValue) {
					$arr['varsList'][] = array(
						'numPage'        => $arr['numPage'],
						'numRow'         => $i,
						'numValueSave'   => ($varsData['valueTextValue' . $i] == 0)? $strBlank : number_format($varsData['valueTextValue' . $i]),
						'numValueUpdate' => number_format($numUpdate),
					);
				}
				continue;
			}
			$arrId[$idSubAccountTitle] = 1;
			$sumSubValue = $this->_getVarsSubValue(array(
				'arrId'           => $arrId,
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			));

			$num = $sumSubValue;
			if ($flagDebit != $arrStrTitle[$idAccountTitle]['flagDebit']) {
				$num *= -1;
			}
			$numUpdate = $num;
			$numValue = $varsData['valueTextValue' . $i];
			if (is_null($numValue) || $numValue == '') {
				$numValue  = 0;
			}

			if ($numUpdate != $numValue) {
				$arr['varsList'][] = array(
					'numPage'        => $arr['numPage'],
					'numRow'         => $i,
					'numValueSave'   => ($varsData['valueTextValue' . $i] == 0)? $strBlank : number_format($varsData['valueTextValue' . $i]),
					'numValueUpdate' => number_format($numUpdate),
				);
			}
		}

		return $arr['varsList'];
	}



	/**
	 *
	 */
	protected function _updateVarsAnalyzeLost($arr)
	{
		global $classEscape;
		global $varsPluginAccountingAccount;

		$varsPage = $this->_getVarsSave(array(
			'numFiscalPeriod'    => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagReport'         => $this->_extSelf['flagReport'],
			'flagDetail'         => $this->_extSelf['flagDetail'],
			'flagRows'           => 1,
		));

		$flagMenu = $arr['varsItem']['flagMenu'];
		$varsData = $arr['varsItem']['varsCommon']['varsStr']['analyze'];
		$varsList = array();

		$array = $varsPage;
		foreach ($array as $key => $value) {
			if ($value['numPage'] == 0) {
				continue;
			}
			$varsList = $this->_checkDetailVarsAnalyzeLost(array(
				'varsItem'     => $arr['varsItem'],
				'varsData'     => $value['jsonData'][$flagMenu],
				'varsList'     => $varsList,
				'numPage'      => $value['numPage'],
			));
		}

		$varsData['arrRowsLost'] = $varsList;
		if ($varsData['arrRowsLost']) {
			$varsData['flagLost'] = 1;
		}
		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done)$/", $flagCurrentFlagNow)) {
			$varsData['flagLost'] = 0;
			$varsData['flagDone'] = 1;
		}
		$vars = $arr['vars'];
		$arrayNewDetail = array();
		$array = $vars['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'AnalyzeLost') {
				$value['strComment'] = $this->_getHtml(array(
					'strFile' => $value['id'],
					'varsStr' => $varsData,
				));
			}
			$arrayNewDetail[] = $value;
		}
		$vars['vars']['varsDetail'] = $arrayNewDetail;

		return $vars;
	}

	/**

	 */
	protected function _checkDetailVarsAnalyzeLost($arr)
	{
		global $varsPluginAccountingAccount;

		$arrayFSList = $arr['varsItem']['arrayFSList'];
		$varsFSValue = $arr['varsItem']['varsFSValue'];
		$flagMenu = $arr['varsItem']['flagMenu'];
		$arrStrTitle = $arr['varsItem']['arrAccountTitle']['arrStrTitle'];
		$arrStrTitle['none']['strTitleFS'] = 'dummy';
		$arrSubStrTitle = $arr['varsItem']['arrSubAccountTitle']['arrStrTitle'];

		$flagDebit = $arr['varsItem']['varsCommon']['varsStr'][$flagMenu]['flagDebit'];
		$strSub0 = $arr['varsItem']['varsCommon']['varsStr'][$flagMenu]['strSub0'];
		$numEnd = $arr['varsItem']['varsCommon']['varsStr'][$flagMenu]['numRows'];

		$arrayCheck = array();
		$arraySaveNum = array();
		$varsData = $arr['varsData'];
		for ($i = 1; $i <= $numEnd; $i++) {
			$idAccountTitle = $varsData['valueSelectIdAccountTitle' . $i];
			$str = $arrStrTitle[$idAccountTitle]['strTitleFS'];
			if (is_null($str) || $idAccountTitle == 'none') {
				continue;
			}
			$arrSubStrTitle[$idAccountTitle]['0']['strTitle'] = $strSub0;
			$idSubAccountTitle = $varsData['valueSelectIdSubAccountTitle' . $i];
			$str = $arrSubStrTitle[$idAccountTitle][$idSubAccountTitle]['strTitle'];
			if (is_null($str)) {
				continue;
			}
			$arrayCheck[$idAccountTitle][$idSubAccountTitle] = 1;
		}

		foreach ($arrayCheck as $keyCheck => $valueCheck) {
			$idAccountTitle = $keyCheck;
			$sumValue = 0;
			foreach ($arrayFSList as $keyFSList => $valueFSList) {
				if ($varsFSValue['jsonJgaapAccountTitle' . $keyFSList]['f1'][$idAccountTitle]) {
					$sumValue = $varsFSValue['jsonJgaapAccountTitle' . $keyFSList]['f1'][$idAccountTitle]['sumNext'];
					break;
				}
			}

			$array = $arrSubStrTitle[$idAccountTitle];
			foreach ($array as $key => $value) {
				$idSubAccountTitle = $key;
				if ($valueCheck[$idSubAccountTitle]) {
					continue;
				}
				$arrId = array();
				if ($idSubAccountTitle == 0) {
					$sumSubValue = 0;
					$arrId = $arrSubStrTitle[$idAccountTitle];
					if ($arrId) {
						$sumSubValue = $this->_getVarsSubValue(array(
							'arrId'           => $arrId,
							'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
						));
					}
					$num = $sumValue - $sumSubValue;
					if ($flagDebit != $arrStrTitle[$idAccountTitle]['flagDebit']) {
						$num *= -1;
					}
					if ($num != 0) {
						$arr['varsList'][] = array(
							'idAccountTitle'    => $arrStrTitle[$idAccountTitle]['strTitleFS'],
							'idSubAccountTitle' => $arrSubStrTitle[$idAccountTitle][$idSubAccountTitle]['strTitle'],
						);
					}
					continue;
				}
				$arrId[$idSubAccountTitle] = 1;
				$sumSubValue = $this->_getVarsSubValue(array(
					'arrId'           => $arrId,
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				));
				$num = $sumSubValue;
				if ($flagDebit != $arrStrTitle[$idAccountTitle]['flagDebit']) {
					$num *= -1;
				}
				if ($num != 0) {
					$arr['varsList'][] = array(
						'idAccountTitle'    => $arrStrTitle[$idAccountTitle]['strTitleFS'],
						'idSubAccountTitle' => $arrSubStrTitle[$idAccountTitle][$idSubAccountTitle]['strTitle'],
					);
				}
			}
		}

		return $arr['varsList'];
	}

	/**
		(array(
			'varsStr' => $arrayStr
		))
	 */
	protected function _getHtml($arr)
	{
		global $classSmarty;
		global $classEscape;

		$array = $arr['varsStr'];
		foreach ($array as $key => $value) {
			$classSmarty->assign($key, $value);
		}

		$arr['strFile'] = $classEscape->toLower(array('str' => $arr['strFile']));
		$path = str_replace('<%replace%>', $arr['strFile'], $this->_extSelf['pathTplHtml']);
		$data = $classSmarty->fetch($path);

		return $data;
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

			$this->_updateDb(array(
				'arrValue' => $arrValue
			));
			$this->_updateDbPreferenceStamp(array('strColumn' => 'detailedAccount'));

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
			$varsItem = $this->_getVarsItem(array(
				'vars' => $vars,
			));
			$varsTarget = $this->$method(array(
				'vars'     => $varsTarget,
				'varsItem' => $varsItem,
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

	 */
	protected function _updateDbNumPageMax($arr)
	{
		global $classEscape;
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsRequest;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$stampRegister = TIMESTAMP;
		$stampUpdate = TIMESTAMP;
		$flagReport = $this->_extSelf['flagReport'];
		$flagDetail = $this->_extSelf['flagDetail'];
		$numPage = 0;
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];

		$jsonData = json_encode($arr['arrValue']['arr']);

		$varsPreference = $this->_getVarsPreference(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagReport'      => $this->_extSelf['flagReport'],
			'flagDetail'      => $this->_extSelf['flagDetail'],
		));

		//update
		if (!is_null($varsPreference['id'])) {
			$arrColumn = array(
				'stampUpdate',
				'jsonData',
			);
			$arrValue = array(
				$stampUpdate,
				$jsonData,
			);
			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingDetailedAccount' . $strNation,
				'arrColumn' => $arrColumn,
				'flagAnd'  => 1,
				'arrWhere'  => array(
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
					array(
						'flagType'      => '',
						'strColumn'     => 'flagReport',
						'flagCondition' => 'eq',
						'value'         => $flagReport,
					),
					array(
						'flagType'      => '',
						'strColumn'     => 'flagDetail',
						'flagCondition' => 'eq',
						'value'         => $flagDetail,
					),
					array(
						'flagType'      => 'num',
						'strColumn'     => 'numPage',
						'flagCondition' => 'eq',
						'value'         => $numPage,
					),
				),
				'arrValue'  => $arrValue,
			));

		//insert
		} else {
			$arrColumn = array(
				'stampRegister',
				'stampUpdate',
				'idEntity',
				'numFiscalPeriod',
				'flagReport',
				'flagDetail',
				'numPage',
				'jsonData',
			);
			$arrValue = array(
				$stampRegister,
				$stampUpdate,
				$idEntity,
				$numFiscalPeriod,
				$flagReport,
				$flagDetail,
				$numPage,
				$jsonData,
			);
			$id = $classDb->insertRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingDetailedAccount' . $strNation,
				'arrColumn' => $arrColumn,
				'arrValue'  => $arrValue,
			));
		}

		$varsPreference = $this->_getVarsPreference(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagReport'      => $this->_extSelf['flagReport'],
			'flagDetail'      => $this->_extSelf['flagDetail'],
		));

		$this->_deleteVarsSave(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagReport'      => $this->_extSelf['flagReport'],
			'flagDetail'      => $this->_extSelf['flagDetail'],
			'numPage'         => $varsPreference['jsonData']['numPageMax'],
		));

		$varsSave = $this->_getVarsSave(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagReport'      => $this->_extSelf['flagReport'],
			'flagDetail'      => $this->_extSelf['flagDetail'],
			'numPage'         => $varsPreference['jsonData']['numPageMax'],
		));

		if (!$varsSave) {
			$numPage = $varsPreference['jsonData']['numPageMax'];
			$jsonData = '';
			$arrColumn = array(
				'stampRegister',
				'stampUpdate',
				'idEntity',
				'numFiscalPeriod',
				'flagReport',
				'flagDetail',
				'numPage',
				'jsonData',
			);
			$arrValue = array(
				$stampRegister,
				$stampUpdate,
				$idEntity,
				$numFiscalPeriod,
				$flagReport,
				$flagDetail,
				$numPage,
				$jsonData,
			);
			$id = $classDb->insertRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingDetailedAccount' . $strNation,
				'arrColumn' => $arrColumn,
				'arrValue'  => $arrValue,
			));
		}

		$varsCommon = $this->getVars(array(
			'path' => $this->_extSelf['pathItem'],
		));

		$this->_updateDbPage(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagReport'      => $this->_extSelf['flagReport'],
			'flagDetail'      => $this->_extSelf['flagDetail'],
			'flagMenu'        => 'detail',
			'numRows'         => $varsCommon['varsStr']['detail']['numRows'],
		));

		$this->_updateDbPageSum(array(
			'idTarget'        => 'detail',
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagReport'      => $this->_extSelf['flagReport'],
			'flagDetail'      => $this->_extSelf['flagDetail'],
			'flagMenu'        => 'detail',
			'numRows'         => $varsCommon['varsStr']['detail']['numRows'],
		));
	}

	/**
		(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'flagReport'      => $arr['flagReport'],
			'flagDetail'      => $arr['flagDetail'],
		))
	 */
	protected function _updateDbPageSum($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$stampUpdate = TIMESTAMP;
		$flagReport = $arr['flagReport'];
		$flagDetail = $arr['flagDetail'];
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $arr['numFiscalPeriod'];

		$varsPage = $this->_getVarsSave(array(
			'numFiscalPeriod'    => $arr['numFiscalPeriod'],
			'flagReport'         => $arr['flagReport'],
			'flagDetail'         => $arr['flagDetail'],
			'flagRows'           => 1,
		));

		$varsPreference = $this->_getVarsPreference(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'flagReport'      => $arr['flagReport'],
			'flagDetail'      => $arr['flagDetail'],
		));
		$numPageMax = $varsPreference['jsonData']['numPageMax'];
		$flagMenu = $arr['flagMenu'];
		$numEnd = $arr['numRows'];
		$arrColumn = array(
			'stampUpdate',
			'jsonData',
		);

		if (!$varsPage) {
			return;
		}
		$sum = 0;
		$array = $varsPage;
		foreach ($array as $key => $value) {
			if ($value['numPage'] == 0) {
				continue;
			}
			$varsSave = $value['jsonData'][$flagMenu];
			for ($i = 1; $i <= $numEnd; $i++) {
				$idTarget = 'valueTextValueReceived' . $i;
				$data = $varsSave[$idTarget];

				if ($data) {
					$sum += $data;
				}
			}

			if ($value['numPage'] == $numPageMax) {
				$value['jsonData'][$flagMenu]['valueTextSumReceived'] = $sum;

			} else {
				$value['jsonData'][$flagMenu]['valueTextSumReceived'] = '';
			}

			$jsonData = json_encode($value['jsonData']);
			$arrValue = array(
				$stampUpdate,
				$jsonData,
			);

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingDetailedAccount' . $strNation,
				'arrColumn' => $arrColumn,
				'flagAnd'  => 1,
				'arrWhere'  => array(
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
						'value'         => $arr['numFiscalPeriod'],
					),
					array(
						'flagType'      => '',
						'strColumn'     => 'flagReport',
						'flagCondition' => 'eq',
						'value'         => $flagReport,
					),
					array(
						'flagType'      => '',
						'strColumn'     => 'flagDetail',
						'flagCondition' => 'eq',
						'value'         => $flagDetail,
					),
					array(
						'flagType'      => 'num',
						'strColumn'     => 'numPage',
						'flagCondition' => 'eq',
						'value'         => $value['numPage'],
					),
				),
				'arrValue'  => $arrValue,
			));
		}
	}


}
