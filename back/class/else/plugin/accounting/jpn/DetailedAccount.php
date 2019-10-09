<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_DetailedAccount extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idSelf'     => 'DetailedAccount',
		'flagReport' => '2012',
		'pathTplJs'  => 'else/plugin/accounting/js/jpn/detailedAccount.js',
		'pathVarsJs' => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/detailedAccount.php',
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

		$this->_checkEntity();
		$idSelf = $this->_extSelf['idSelf'];
		$str = ucwords($varsRequest['query']['ext']);
		if ($str != $idSelf) {
			$this->_updateFlagReport();

			list($dummy, $str) = preg_split("/^$idSelf/", $str);

			$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/' . PLUGIN_ACCOUNTING_STR_NATION . '/' . $this->_extSelf['flagReport'] . '/detailedAccount/' . $str . ".php";
			$strClass = 'Code_Else_Plugin_Accounting_' . ucwords(PLUGIN_ACCOUNTING_STR_NATION) . '_' . $this->_extSelf['flagReport'] . '_DetailedAccount_' . $str;

			if (!file_exists($path)) {
				$this->sendValue(array(
					'flag'    => 8,
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));
			}
			require_once($path);
			$classCall = new $strClass;
			$classCall->run();

		} else {
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
		}
	}

	protected function _getVarsSubValue($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$sum = 0;
		$array = $arr['arrId'];
		foreach ($array as $key => $value) {
			$rows = $classDb->getSelect(array(
				'idModule' => 'accounting',
				'strTable' => 'accountingSubAccountTitleValue' . $strNation,
				'arrLimit' => array(),
				'arrOrder' => array(),
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
						'value'         => $arr['numFiscalPeriod'],
					),
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idSubAccountTitle',
						'flagCondition' => 'eq',
						'value'         => $key,
					),
				),
			));
			$num = $rows['arrRows'][0]['jsonData']['all']['f1']['sumNext'];

			if (is_null($num)) {
				$num = 0;
			}

			$sum += $num;
		}

		return $sum;
	}

	protected function _sendData($arr)
	{
		$this->sendVars(array(
			'flag'    => $arr['flag'],
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $arr['vars'],
		));
	}

	/**
		(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent']
		))
	 */
	protected function _getVarsSave($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$arrWhere = array(
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
				'value'         => $arr['flagReport'],
			),
			array(
				'flagType'      => '',
				'strColumn'     => 'flagDetail',
				'flagCondition' => 'eq',
				'value'         => $arr['flagDetail'],
			),
		);

		if (!is_null($arr['numPage'])) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'numPage',
				'flagCondition' => 'eq',
				'value'         => $arr['numPage'],
			);

		}

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingDetailedAccount' . $strNation,
			'arrLimit' => array(),
			'arrOrder'  => array(
				'strColumn' => 'numPage',
				'flagDesc'  => 0,
			),
			'flagAnd'  => 1,
			'arrWhere' => $arrWhere,
		));

		if (!$rows['numRows']) {
			return array();
		}

		if ($arr['flagRows']) {
			return $rows['arrRows'];
		}

		return $rows['arrRows'][0];
	}

	/**

	 */
	protected function _deleteVarsSave($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$classDb->deleteRow(array(
			'idModule'  => 'accounting',
			'strTable' => 'accountingDetailedAccount' . $strNation,
			'flagAnd'   => 1,
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
					'value'         => $arr['flagReport'],
				),
				array(
					'flagType'      => '',
					'strColumn'     => 'flagDetail',
					'flagCondition' => 'eq',
					'value'         => $arr['flagDetail'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'numPage',
					'flagCondition' => 'big',
					'value'         => $arr['numPage'],
				),
			),
		));
	}

	/**
	 *
	 */
	protected function _updateFlagReport()
	{
		global $varsPluginAccountingAccount;

		$varsJsonFlag = $this->_getJsonFlag(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idTarget'        => 'detailedAccount',
		));

		if ($varsJsonFlag) {
			$this->_extSelf['flagReport'] = $varsJsonFlag['flagReport'];
		}
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
		(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'flagReport'      => $arr['flagReport'],
			'flagDetail'      => $arr['flagDetail'],
		))
	 */
	protected function _getVarsPreference($arr)
	{
		$varsPreference = $this->_getVarsSave(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'flagReport'      => $arr['flagReport'],
			'flagDetail'      => $arr['flagDetail'],
			'numPage'         => 0,
		));
		if (!$varsPreference) {
			$varsPreference['jsonData']['numPageMax'] = 1;
		}

		return $varsPreference;
	}


	/**
		(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'flagReport'      => $arr['flagReport'],
			'flagDetail'      => $arr['flagDetail'],
		))
	 */
	protected function _updateDbPage($arr)
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
				$idTarget = 'valueTextValue' . $i;
				$data = $varsSave[$idTarget];

				if ($data) {
					$sum += $data;
				}
			}

			if ($value['numPage'] == $numPageMax) {
				$value['jsonData'][$flagMenu]['valueTextSum'] = $sum;

			} else {
				$value['jsonData'][$flagMenu]['valueTextSum'] = '';
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
	protected function _updateVarsFlagReport($arr)
	{
		$vars = $arr['vars'];

		return $vars;
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
	protected function _updateDbFlagReport($arrValue)
	{
		global $classEscape;
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsRequest;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;


		$flagReport = $arrValue['arr']['flagReport'];

		$this->_updateJsonFlag(array(
			'numFiscalPeriod'=> $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idTarget'       => 'detailedAccount',
			'varsTarget'     => array(
				'flagReport' => $flagReport,
			),
		));
		$this->_updateDbPreferenceStamp(array('strColumn' => 'detailedAccount'));

	}

	/**

	 */
	protected function _checkVarsValueDouble($arr)
	{
		if (!$arr['arrCheck']) {
			$arr['arrCheck'] = array();
		}
		$flagMenu = $arr['varsFlag']['flagMenu'];
		$arrStrTitle = $arr['varsItem']['arrAccountTitle']['arrStrTitle'];
		$arrStrTitle['none']['strTitleFS'] = 'dummy';
		$arrSubStrTitle = $arr['varsItem']['arrSubAccountTitle']['arrStrTitle'];

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
			$flag = $arr['arrCheck'][$idAccountTitle][$idSubAccountTitle];
			if (!is_null($flag)) {
				$this->_sendData(array(
					'flag' => 'strDouble',
					'vars' => array(),
				));
			}
			$arr['arrCheck'][$idAccountTitle][$idSubAccountTitle] = 1;
		}

		return $arr['arrCheck'];
	}

	/**

	 */
	protected function _checkVarsValueDoublePage($arr)
	{
		global $varsPluginAccountingAccount;

		$varsPage = $this->_getVarsSave(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagReport'      => $this->_extSelf['flagReport'],
			'flagDetail'      => $this->_extSelf['flagDetail'],
			'flagRows'        => 1,
		));

		$arrCheck = array();
		$arrCheck = $this->_checkVarsValueDouble(array(
			'varsItem' => $arr['varsItem'],
			'varsFlag' => $arr['varsFlag'],
			'varsData' => $arr['varsData'],
			'arrCheck' => $arrCheck,
		));

		if (!$varsPage) {
			return;
		}

		$flagMenu = $arr['varsFlag']['flagMenu'];
		$numPage = $arr['varsFlag']['numPage'];
		$array = $varsPage;
		foreach ($array as $key => $value) {
			if ($value['numPage'] == 0 || $value['numPage'] == $numPage) {
				continue;
			}
			$arrCheck = $this->_checkVarsValueDouble(array(
				'varsItem' => $arr['varsItem'],
				'varsFlag' => $arr['varsFlag'],
				'varsData' => $value['jsonData'][$flagMenu],
				'arrCheck' => $arrCheck,
			));

		}
	}
	/**
	 *
	 */
	protected function _extDetailPrint()
	{
		global $varsRequest;

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'output',
			'idTarget'      => $this->_extSelf['idPreference'],
		));

		if (!$flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsFlag = array(
			'flagMenu' => $varsRequest['query']['jsonValue']['vars']['FlagMenu'],
			'numPage'  => $varsRequest['query']['jsonValue']['vars']['NumPage'],
			'flagType' => $varsRequest['query']['jsonValue']['vars']['FlagType'],
		);

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$this->_checkValueDetail(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
		));

		$varsItem = $this->_updateVarsItem(array(
			'varsItem' => $varsItem,
		));

		$varsPrint = $this->_getVarsPrint(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
			'varsItem' => $varsItem,
		));

		$this->sendVars(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $varsPrint,
		));
	}

	/**
		(array(

		))

	 */
	protected function _updateVarsItem($arr)
	{
		$varsPrintItem = $this->_getVarsPrintItem();

		$arr['varsItem']['varsPrintItem'] = $varsPrintItem;

		return $arr['varsItem'];
	}

	/**
		(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
			'varsItem' => $varsItem,
		))

	 */
	protected function _getVarsPrint($arr)
	{
		global $varsPluginAccountingAccount;

		$flagCount = 0;
		$varsPrint = array();
		if ($arr['varsFlag']['flagType'] == 'item') {
			$varsPrint = $this->_getVarsPrintLoop(array(
				'flagCount'  => $flagCount,
				'vars'       => $arr['vars'],
				'varsItem'   => $arr['varsItem'],
				'varsFlag'   => $arr['varsFlag'],
				'varsPrint'  => $varsPrint,
			));

		} elseif ($arr['varsFlag']['flagType'] == 'itemAll') {
			$numEnd = (int) $arr['varsItem']['varsPreference']['jsonData']['numPageMax'];
			if (!$numEnd) {
				$numEnd = 1;
			}
			for ($i = 1; $i <= $numEnd; $i++) {
				$numPage = $i;
				$arr['varsFlag']['numPage'] = $numPage;
				$arr['varsItem']['varsSave'] = $this->_getVarsSave(array(
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					'flagReport'      => $this->_extSelf['flagReport'],
					'flagDetail'      => $this->_extSelf['flagDetail'],
					'numPage'         => $arr['varsFlag']['numPage'],
				));
				$arr['vars']['portal']['varsDetail']['varsDetail']['varsHtml'] = array();
				$varsPrint = $this->_getVarsPrintLoop(array(
					'flagCount'  => $flagCount,
					'vars'       => $arr['vars'],
					'varsItem'   => $arr['varsItem'],
					'varsFlag'   => $arr['varsFlag'],
					'varsPrint'  => $varsPrint,
				));
				$flagCount++;
			}
		}

		return $varsPrint;
	}

	/**

	 */
	protected function _getVarsPrintItem()
	{
		$vars = $this->getVars(array(
			'path' => $this->_childSelf['pathVarsPrint'],
		));

		return $vars;
	}

	/**
		(array(
			'flagCount'  => $flagCount,
			'flagLast'   => ($numPage == $numEnd)? 1 : 0,
			'vars'       => $arr['vars'],
			'varsItem'   => $arr['varsItem'],
			'varsFlag'   => $arr['varsFlag'],
			'varsPrint'  => $varsPrint,
		))
	 */
	protected function _getVarsPrintLoop($arr)
	{
		$varsData = array();

		$varsData = $this->_getVarsStatus(array(
			'vars'     => $arr['vars'],
			'varsFlag' => $arr['varsFlag'],
			'varsItem' => $arr['varsItem'],
		));

		$varsPrint = $arr['varsPrint'];
		if (!$varsPrint) {
			$varsPrint = $arr['vars']['varsPrint'];
			$varsPrint['varsStatus'] = $this->_getVarsStatusPrint(array(
				'varsData'   => $varsData,
				'vars'       => $arr['vars'],
				'varsPrint'  => $varsPrint,
				'varsItem'   => $arr['varsItem'],
				'varsFlag'   => $arr['varsFlag'],
			));
		}

		$varsPrint = $this->_getVarsLoopPrint(array(
			'flagCount'  => $arr['flagCount'],
			'flagFirst'  => 1,
			'varsData'   => $varsData,
			'varsPrint'  => $varsPrint,
			'vars'       => $arr['vars'],
			'varsItem'   => $arr['varsItem'],
			'varsFlag'   => $arr['varsFlag'],
		));

		return $varsPrint;
	}

	/**
		(array(
			'vars'     => $arr['vars'],
			'varsFlag' => $arr['varsFlag'],
			'varsItem' => $arr['varsItem'],
		))
	 */
	protected function _getVarsStatus($arr)
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];

		$flagMenu = $arr['varsFlag']['flagMenu'];
		$varsData = $arr['varsItem']['varsCommon']['varsStr'][$flagMenu];

		//strEntity
		$strEntity = $varsPluginAccountingEntity[$idEntity]['strTitle'];
		$varsData['strEntityExt'] = str_replace('<%replace%>', $strEntity, $arr['vars']['varsItem']['varsOutput']['strEntity']);

		//strNum
		$strNumRep = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$varsData['strNumExt'] = str_replace('<%replace%>', $strNumRep, $arr['vars']['varsItem']['varsOutput']['strNum']);

		//strPage
		$strPageRep = $arr['varsFlag']['numPage'];
		$varsData['strPageExt'] = str_replace('<%replace%>', $strPageRep, $arr['vars']['varsItem']['varsOutput']['strPage']);

		$varsData['strTitleSub'] = $strEntity . '(' . $varsData['strNum'] . ') ';

		return $varsData;
	}



	/**
		(array(
			'flagCount'  => $arr['flagCount'],
			'flagFirst'  => 1,
			'varsData'   => $varsData,
			'varsPrint'  => $varsPrint,
			'vars'       => $arr['vars'],
			'varsItem'   => $arr['varsItem'],
			'varsFlag'   => $arr['varsFlag'],
		))

	 */
	protected function _getVarsLoopPrint($arr)
	{
		$varsPrint = &$arr['varsPrint'];

		return $varsPrint;
	}

	/**
		(array(
			'varsData'   => $varsData,
			'vars'       => $arr['vars'],
			'varsDetail' => $arr['varsDetail'],
		))
	 */
	protected function _getVarsStatusPrint($arr)
	{
		$varsPrint = $arr['varsPrint'];
		$varsPrintItem = $arr['varsItem']['varsPrintItem'];

		//tmplWrap
		$varsPrint['varsStatus']['varsTmpl']['tmplWrap'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'tmplStr'  => $varsPrintItem['tmplWrap'],
		));

		$varsPrint['varsStatus']['varsTmpl']['tmplColumn'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'tmplStr'  => $varsPrintItem['tmplColumn'],
		));

		$varsPrint['varsStatus']['varsTmpl']['tmplTable'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'tmplStr'  => $varsPrintItem['tmplTable'],
		));

		$varsPrint['varsStatus']['varsTmpl']['tmplTableTop'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'tmplStr'  => $varsPrintItem['tmplTableTop'],
		));

		$varsPrint['varsStatus']['varsTmpl']['tmplTableBottom'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'tmplStr'  => $varsPrintItem['tmplTableBottom'],
		));

		$varsPrint['varsStatus']['varsTmpl']['tmplTableBottomCaution'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'tmplStr'  => $varsPrintItem['tmplTableBottomCaution'],
		));

		$varsPrint['varsStatus']['varsTmpl']['tmplPage'] = $varsPrintItem['tmplPage'];

		$varsPrint['varsStatus']['strTitle'] = $this->_getStrTitle(array(
			'varsFlag' => $arr['varsFlag'],
			'varsItem' => $arr['varsItem'],
			'vars'     => $arr['vars'],
		));

		return $varsPrint['varsStatus'];
	}

	/**
		(array(
			'strFileType'   => '',
			'varsFlag'   => $arr['varsFlag'],
			'varsItem'   => $arr['varsItem'],
			'vars'       => $arr['vars'],
		))

	 */
	protected function _getStrTitle($arr)
	{
		$flagMenu = $arr['varsFlag']['flagMenu'];
		$varsData = $arr['varsItem']['varsCommon']['varsStr'][$flagMenu];

		$strMenu = $varsData['strTitle'];

		$strFileName = $this->_getFileTitle(array(
			'strMenu'     => $strMenu,
			'strFileType' => $arr['strFileType'],
		));

		return $strFileName;
	}

	/**
	 *
	 */
	protected function _extDetailOutput()
	{
		global $classRequest;
		global $varsRequest;

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'output',
			'idTarget'      => $this->_extSelf['idPreference'],
		));

		if (!$flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsFlag = array(
			'flagMenu' => $varsRequest['query']['jsonValue']['vars']['FlagMenu'],
			'numPage'  => $varsRequest['query']['jsonValue']['vars']['NumPage'],
			'flagType' => $varsRequest['query']['jsonValue']['vars']['FlagType'],
		);

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$this->_checkValueDetail(array(
			'vars'       => $vars,
			'varsFlag'   => $varsFlag,
			'flagOutput' => 1,
		));

		$varsItem = $this->_updateVarsItem(array(
			'varsItem' => $varsItem,
		));

		$text = $this->_getCsv(array(
			'vars'      => $vars,
			'varsFlag'  => $varsFlag,
			'varsItem'  => $varsItem,
		));

		$strFileName = $this->_getStrTitle(array(
			'varsFlag'    => $varsFlag,
			'varsItem'    => $varsItem,
			'vars'        => $vars,
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
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
			'varsItem' => $varsItem,
		))

	 */
	protected function _getCsv($arr)
	{
		global $varsPluginAccountingAccount;
		global $classFile;

		$arrayCsv = array();
		$flagCount = 0;
		if ($arr['varsFlag']['flagType'] == 'item') {
			$arrayCsv = $this->_getVarsCsvLoop(array(
				'flagCount'  => $flagCount,
				'vars'       => $arr['vars'],
				'arrayCsv'   => $arrayCsv,
				'varsItem'   => $arr['varsItem'],
				'varsFlag'   => $arr['varsFlag'],
			));

		} elseif ($arr['varsFlag']['flagType'] == 'itemAll') {
			$numEnd = (int) $arr['varsItem']['varsPreference']['jsonData']['numPageMax'];
			if (!$numEnd) {
				$numEnd = 1;
			}
			for ($i = 1; $i <= $numEnd; $i++) {
				$numPage = $i;
				$arr['varsFlag']['numPage'] = $numPage;
				$arr['varsItem']['varsSave'] = $this->_getVarsSave(array(
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					'flagReport'      => $this->_extSelf['flagReport'],
					'flagDetail'      => $this->_extSelf['flagDetail'],
					'numPage'         => $arr['varsFlag']['numPage'],
				));
				$arr['vars']['portal']['varsDetail']['varsDetail']['varsHtml'] = array();
				$arrayCsv = $this->_getVarsCsvLoop(array(
					'flagCount'  => $flagCount,
					'vars'       => $arr['vars'],
					'arrayCsv'   => $arrayCsv,
					'varsItem'   => $arr['varsItem'],
					'varsFlag'   => $arr['varsFlag'],
				));
				$flagCount++;
			}
		}

		$text = $classFile->getCsvText(array(
			'delimiter' => ',',
			'rows'      => $arrayCsv,
		));

		return $text;
	}

	/**
		(array(
			'flagCount'  => $flagCount,
			'numPage'    => $arr['varsFlag']['numPage'],
			'vars'       => $arr['vars'],
			'arrayCsv'   => $arrayCsv,
			'varsItem'   => $arr['varsItem'],
			'varsFlag'   => $arr['varsFlag'],
		))
	 */
	protected function _getVarsCsvLoop($arr)
	{
		$varsData = array();

		$varsData = $this->_getVarsStatus(array(
			'vars'      => $arr['vars'],
			'varsFlag'  => $arr['varsFlag'],
			'varsItem'  => $arr['varsItem'],
		));

		$data = $this->_getVarsLoopCsv(array(
			'flagCount'  => $arr['flagCount'],
			'flagFirst'  => 1,
			'varsData'   => $varsData,
			'arrayCsv'   => $arr['arrayCsv'],
			'vars'       => $arr['vars'],
			'varsItem'   => $arr['varsItem'],
			'varsFlag'   => $arr['varsFlag'],
		));

		return $data;
	}

	/**
		(array(
			'flagCount'  => $arr['flagCount'],
			'flagFirst'  => 1,
			'varsData'   => $varsData,
			'arrayCsv'   => $arr['arrayCsv'],
			'vars'       => $arr['vars'],
			'varsItem'   => $arr['varsItem'],
			'varsFlag'   => $arr['varsFlag'],
		))
	 */
	protected function _getVarsLoopCsv($arr)
	{
		$arrayCsv = &$arr['arrayCsv'];

		return $arrayCsv;
	}

	/**
	 array(
	 'strClass' => ''
	 )
	 */
	protected function _getClassExtra($arr)
	{
		$str = $arr['strClass'];
		$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/' . PLUGIN_ACCOUNTING_STR_NATION . '/' . $this->_extSelf['flagReport'] . '/detailedAccount/' . $str . ".php";
		$strClass = 'Code_Else_Plugin_Accounting_' . ucwords(PLUGIN_ACCOUNTING_STR_NATION) . '_' . $this->_extSelf['flagReport'] . '_DetailedAccount_' . $str;
		if (!file_exists($path)) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}
		require_once($path);
		$classCall = new $strClass;

		return $classCall;
	}
}
