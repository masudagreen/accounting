<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_AccountTitleFSCS extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference' => 'accountTitleFSCSWindow',
		'pathTplJs' => 'else/plugin/accounting/js/jpn/accountTitleFSCS.js',
		'pathVarsJs'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/accountTitleFSCS.php',
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

		if ($varsRequest['query']['child']) {
			$strChild = ucwords($varsRequest['query']['child']);
			$strExt = ucwords($varsRequest['query']['ext']);
			$str = $strExt . $strChild;

			$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/jpn/' . $str . ".php";
			if (!file_exists($path)) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
				}
				exit;
			}
			require_once($path);
			$strClass = 'Code_Else_Plugin_Accounting_Jpn_' . $str;
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
		exit;
	}

	/**
	 *
	 */
	protected function _iniJs()
	{
		global $classSmarty;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars'       => $vars,
			'flagFS'     => $vars['flagFS'],
			'flagDirect' => $vars['flagDirect'],
		));

		$vars = $this->_updateVars(array(
			'flagDirect' => $vars['flagDirect'],
			'vars'       => $vars,
			'flagFS'     => $vars['flagFS'],
			'varsItem'   => $varsItem,
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
			'vars'       => $vars,
			'flagDirect' => $vars['flagDirect'],
		))
	 */
	protected function _getVarsItem($arr)
	{
		global $classDb;
		global $varsAccount;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsJgaapFS = $this->_getVarsItemJgaapFS(array(
			'varsFS'          => $varsFS,
			'vars'            => $arr['vars'],
			'flagDirect'      => $arr['flagDirect'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		$numFiscalPeriodTemp = '';
		if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
			$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] + 1;
		}

		$varsJgaapFSTemp = array();
		if ($numFiscalPeriodTemp) {
			$varsFSTemp = $this->_getVarsFS(array(
				'numFiscalPeriod' => $numFiscalPeriodTemp,
			));
			$varsJgaapFSTemp = $this->_getVarsItemJgaapFS(array(
				'varsFS'          => $varsFSTemp,
				'vars'            => $arr['vars'],
				'flagDirect'      => $arr['flagDirect'],
				'numFiscalPeriod' => $numFiscalPeriodTemp,
			));
		}

		$varsFSItem = $this->_getVarsFSItem();

		$varsFSRows = $this->_getVarsFSRows(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsJgaapFSs = array();
		foreach ($varsFSRows as $key => $value) {
			$varsJgaapFSs[] = $this->_getVarsItemJgaapFS(array(
				'varsFS'          => $value,
				'vars'            => $arr['vars'],
				'flagDirect'      => $arr['flagDirect'],
				'numFiscalPeriod' => $value['numFiscalPeriod'],
			));
		}

		$data = array(
			'varsFS'          => $varsFS,
			'varsFSRows'      => $varsFSRows,
			'varsJgaapFS'     => $varsJgaapFS,
			'varsJgaapFSs'    => $varsJgaapFSs,
			'varsJgaapFSTemp' => $varsJgaapFSTemp,
			'varsFSItem'      => $varsFSItem,
		);

		return $data;

	}

	/**
		(array(
			'arrIdAccountTitle'  => array(),
			'varsFS'         => array(),
		))
	 */
	protected function _getVarsItemJgaapFS($arr)
	{
		global $varsPluginAccountingAccount;

		$strFlagDirect = 'varsInDirect';
		if ($arr['flagDirect']) {
			$strFlagDirect = 'varsDirect';
		}

		$array = $this->_getFSList(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		$arrayNew = array();
		$arrayNewFS = array();
		foreach ($array as $key => $value) {
			$data = $this->_getVarsItemJgaapFSLoop(array(
				'arrIdAccountTitle' => array(),
				'vars'              => $arr['varsFS']['jsonJgaapAccountTitle'. $key],
				'strFlagDirect'     => $strFlagDirect,
			));
			$arrayNewFS[$key] = $data['arrIdAccountTitle'];
			$arrayAccount = $data['arrIdAccountTitle'];
			foreach ($arrayAccount as $keyAccount => $valueAccount) {
				if (is_null($arrayNew[$keyAccount])) {
					$arrayNew[$keyAccount] = $valueAccount;

				} else {
					$arrayAccountChild = $valueAccount;
					foreach ($arrayAccountChild as $keyAccountChild => $valueAccountChild) {
						$arrayNew[$keyAccount][$keyAccountChild] = 1;
					}
				}
			}
		}

		$data = array(
			'arrIdAccountTitle'   => $arrayNew,
			'arrIdAccountTitleFS' => $arrayNewFS,
		);

		return $data;
	}

	protected function _getVarsItemJgaapFSLoop($arr)
	{
		$arrIdAccountTitle = &$arr['arrIdAccountTitle'];

		$array = &$arr['vars'];
		foreach ($array as $key => $value) {
			if (!is_null($value['vars']['flagUse'])) {
				$id = $value['vars']['varsJgaapCS'][$arr['strFlagDirect']]['idAccountTitleMinus'];
				$arr['arrIdAccountTitle'][$id][$value['vars']['idTarget']] = 1;
				$id = $value['vars']['varsJgaapCS'][$arr['strFlagDirect']]['idAccountTitlePlus'];
				$arr['arrIdAccountTitle'][$id][$value['vars']['idTarget']] = 1;
			}

			if ($value['child']) {
				$data = $this->_getVarsItemJgaapFSLoop(array(
					'vars'              => $array[$key]['child'],
					'strFlagDirect'     => $arr['strFlagDirect'],
					'arrIdAccountTitle' => $arr['arrIdAccountTitle'],
				));
				$array[$key]['child'] = $data['vars'];
				$arrIdAccountTitle =  $data['arrIdAccountTitle'];
			}
		}

		return $arr;
	}

	/**
		(array(
			'flag' => '',
			'vars' => array(),
			'varsItem' => array(),
		))
	 */
	protected function _updateVars($arr)
	{
		global $classHtml;

		global $varsAccount;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingAccountsEntity;
		global $varsPluginAccountingAuthority;

		$idAccount = $varsPluginAccountingAccount['id'];
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$idAuthority = $varsPluginAccountingAccountsEntity[$idAccount][$idEntity]['idAuthority'];
		$varsAuthority = $varsPluginAccountingAuthority[$idAuthority];

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));
		$strFlagDirect = 'varsInDirect';
		if ($arr['flagDirect']) {
			$strFlagDirect = 'varsDirect';
		}
		$vars = &$arr['vars'];

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done)$/", $flagCurrentFlagNow)) {
			$vars['portal']['varsDetail']['varsBtn'] = array();
			$vars['portal']['varsDetail']['view']['varsEdit'] = array();

		} else {
			$arr['varsItem']['varsFS']['jsonJgaapFS' . $arr['flagFS']][$strFlagDirect] = $this->_getFlagUseLog(array(
				'vars'                  => $arr['varsItem']['varsFS']['jsonJgaapFS' . $arr['flagFS']][$strFlagDirect],
				'arrIdAccountTitle'     => $arr['varsItem']['varsJgaapFS']['arrIdAccountTitle'],
				'arrIdAccountTitleTemp' => $arr['varsItem']['varsJgaapFSTemp']['arrIdAccountTitle'],
				'arrIdAccountTitles'    => $arr['varsItem']['varsJgaapFSs'],
				'numFiscalPeriod'       => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				'varsAuthority' => array(
					'flagInsert' => ($varsAuthority['flagAllInsert'])? 1 : 0,
					'flagUpdate' => ($varsAuthority['flagAllUpdate'])? 1 : 0,
					'flagDelete' => ($varsAuthority['flagAllDelete'])? 1 : 0,
				),
			));

		}

		$varsDetail = $this->_updateVarsList(array(
			'vars'     => $vars,
			'varsFS'   => $arr['varsItem']['varsFS']['jsonJgaapFS' . $arr['flagFS']][$strFlagDirect],
		));

		$vars['portal']['varsList']['varsDetail'] = $varsDetail;

		$varsTemp = $classHtml->allot(array(
			'strClass'    => 'TableTree',
			'flagStatus'  => 'Html',
			'numTimeZone' => $varsAccount['numTimeZone'],
			'varsDetail'  => $varsDetail,
			'varsColumn'  => $vars['portal']['varsList']['tableTree']['varsDetail']['varsColumn'],
			'varsStatus'  => $vars['portal']['varsList']['tableTree']['varsDetail']['varsStatus'],
		));
		$vars['portal']['varsList']['varsHtml'] = $varsTemp['strHtml'];

		return $vars;
	}

	/**
		(array(
			'vars'             => array(),
			'varsFS'           => array(),
		))
	 */
	protected function _updateVarsList($arr)
	{
		$array = &$arr['varsFS'];

		foreach ($array as $key => $value) {
			$array[$key]['id'] = '';
			$array[$key]['flagBoldNow'] = 0;
			$array[$key]['strClassFont'] = '';
			$array[$key]['strClassBg'] = '';
			$array[$key]['varsColumnDetail'] = array(
				'flagUse' => '',
			);

			if (!is_null($value['vars']['flagUse'])) {

				//flagUse
				if ((int) $value['vars']['flagUse']) {
					$array[$key]['varsColumnDetail']['flagUse'] = $arr['vars']['varsItem']['strShow'];

				} else {
					$array[$key]['varsColumnDetail']['flagUse'] = $arr['vars']['varsItem']['strHide'];
					$array[$key]['strClassFont'] = $arr['vars']['varsItem']['strClassNone'];
				}

			}
			if ($value['child']) {
				$array[$key]['child'] = $this->_updateVarsList(array(
					'vars'     => $arr['vars'],
					'varsFS'   => $array[$key]['child'],
				));
			}
		}

		return $array;
	}




	/**

	 */
	protected function _iniNaviSearch()
	{
		$this->_setSearch();
	}

	/**

	 */
	protected function _setSearch()
	{
		global $varsRequest;
		global $varsPluginAccountingPreference;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));


		$flagDirect = (int) $varsRequest['query']['jsonValue']['vars']['FlagDirect'];

		if (FLAG_CHECK_UPDATE) {
			$str = 'acountTitle';
			if ($varsPluginAccountingPreference['jsonStampUpdate'][$str] <= $varsRequest['query']['jsonStamp']['stamp']) {
				$this->sendVars(array(
					'flag'    => 10,
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => '',
				));
			}
		}


		$varsItem = $this->_getVarsItem(array(
			'vars'       => $vars,
			'flagFS'     => $vars['flagFS'],
			'flagDirect' => $flagDirect,
		));

		$vars = $this->_updateVars(array(
			'flagDirect' => $flagDirect,
			'vars'       => $vars,
			'flagFS'     => $vars['flagFS'],
			'varsItem'   => $varsItem,
		));

		$this->sendVars(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'varsDetail'     => $vars['portal']['varsList']['varsDetail'],
				'varsHtml'       => $vars['portal']['varsList']['varsHtml'],
				'varsColumn'     => $vars['portal']['varsList']['tableTree']['varsDetail']['varsColumn'],
				'templateDetail' => $vars['portal']['varsDetail']['templateDetail']
			),
		));
	}

	/**
	 *
	 */
	protected function _iniListReload()
	{
		$this->_setSearch();
	}

	/**
	 *
	 */
	protected function _iniDetailDelete()
	{
		$this->_setDelete();
	}

	/**

	 */
	protected function _setDelete()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsRequest;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		if (!$this->_checkCurrent()) {
			$this->_sendOldError();
		}

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'delete',
			'idTarget'      => $this->_extSelf['idPreference'],
		));

		if (!$flag) {
			$this->_sendOld();
		}

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);
		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$flagDirect = (int) $varsRequest['query']['jsonValue']['vars']['FlagDirect'];

		$varsItem = $this->_getVarsItem(array(
			'vars'       => $vars,
			'flagFS'     => $vars['flagFS'],
			'flagDirect' => $flagDirect,
		));

		$strFlagDirect = 'varsInDirect';
		if ($flagDirect) {
			$strFlagDirect = 'varsDirect';
		}

		$vars = $this->_updateVars(array(
			'flagDirect' => $flagDirect,
			'vars'       => $vars,
			'flagFS'     => $vars['flagFS'],
			'varsItem'   => $varsItem,
		));

		$this->_checkValueDetailDelete(array(
			'varsItem'   => $varsItem,
			'flagFS'     => $vars['flagFS'],
			'vars'       => $vars,
			'idTarget'   => $idTarget,
			'flagDirect' => $flagDirect,
		));

		$varsItem['varsFS']['jsonJgaapFS' . $vars['flagFS']][$strFlagDirect] = $this->_getVarsDelete(array(
			'vars'     => $varsItem['varsFS']['jsonJgaapFS' . $vars['flagFS']][$strFlagDirect],
			'idTarget' => $idTarget,
		));
		$varsFS = $varsItem['varsFS']['jsonJgaapFS' . $vars['flagFS']];

		$jsonJgaapFS = json_encode($varsFS);
		$this->checkTextSize(array(
			'flag' => 'errorDataMax',
			'str'  => $jsonJgaapFS,
		));
		$strJgaapFS = 'jsonJgaapFS' . $vars['flagFS'];

		$arrDbColumn = array($strJgaapFS);
		$arrDbValue = array($jsonJgaapFS);

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
				'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			),
		);

		try {
			$dbh->beginTransaction();

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingFS' . $strNation,
				'arrColumn' => $arrDbColumn,
				'flagAnd'  => 1,
				'arrWhere' => $arrWhere,
				'arrValue'  => $arrDbValue,
			));

			$rows = $classDb->getSelect(array(
				'idModule' => 'accounting',
				'strTable' => 'accountingFSId' . $strNation,
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
				),
			));
			$varsId = ($rows['arrRows'][0][$strJgaapFS])? $rows['arrRows'][0][$strJgaapFS] : array();
			$varsId[$strFlagDirect][$idTarget] = 1;

			$jsonId = json_encode($varsId);
			$this->checkTextSize(array(
				'flag' => 'errorDataMax',
				'str'  => $jsonId,
			));
			$arrDbValue = array($jsonId);

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingFSId' . $strNation,
				'arrColumn' => $arrDbColumn,
				'flagAnd'  => 1,
				'arrWhere' => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idEntity',
						'flagCondition' => 'eq',
						'value'         => $varsPluginAccountingAccount['idEntityCurrent'],
					),
				),
				'arrValue'  => $arrDbValue,
			));

			if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
				$classCalcTempNextAccountTitleFSCS = $this->_getClassCalc(array(
					'flagType'   => 'TempNext',
					'flagDetail' => 'AccountTitleFSCS',
				));
				$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
				$numFiscalPeriod = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];

				$classCalcTempNextAccountTitleFSCS->allot(array(
					'flagStatus'      => 'delete',
					'numFiscalPeriod' => $numFiscalPeriod,
					'flagFS'          => $vars['flagFS'],
					'flagDirect'      => $flagDirect,
					'idTarget'        => $idTarget,
				));
			}

			$this->_updateDbPreferenceStamp(array('strColumn' => 'fSId'));
			$this->_updateDbPreferenceStamp(array('strColumn' => 'fS'));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$this->_setSearch();
	}

	/**
		array(
			'varsItem'   => $varsItem,
			'flagFS'     => $vars['flagFS'],
			'vars'       => $vars,
			'idTarget'   => $idTarget,
			'flagDirect' => $flagDirect,
		)
	 */
	protected function _checkValueDetailDelete($arr)
	{
		global $varsPluginAccountingAccount;

		$strFlagDirect = 'varsInDirect';
		if ($arr['flagDirect']) {
			$strFlagDirect = 'varsDirect';
		}

		$varsFS = $arr['varsItem']['varsFS']['jsonJgaapFS' . $arr['flagFS']][$strFlagDirect];
		$varsFS = $this->_getFlagUseLog(array(
			'vars'                  => $varsFS,
			'arrIdAccountTitle'     => $arr['varsItem']['varsJgaapFS']['arrIdAccountTitle'],
			'arrIdAccountTitleTemp' => $arr['varsItem']['varsJgaapFSTemp']['arrIdAccountTitle'],
			'numFiscalPeriod'       => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idTarget'              => $arr['idTarget'],
		));

		$arrayBlock = $this->_getTreeBlock(array(
			'vars'     => $varsFS,
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

		$varsTarget = array();
		foreach ($arrayBlock as $key => $value) {
			if ($value['vars']['idTarget'] == $arr['idTarget']) {
				$varsTarget = $value;
				break;
			}
		}

		if ($value['flagDefault']
			|| $varsTarget['vars']['flagUseAccountTitle']
			|| $varsTarget['vars']['flagUseAccountTitleTemp']
		) {
			$this->_sendOld();
		}
	}
}
