<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Accounting extends Code_Else_Core_Base_ModuleAbstract
{
	protected $_self = array(
		'strTitle'                => 'accounting',
		'strIniNation'            => 'jpn',
		'strIniLang'              => 'ja',
		'pathRuibuidVars'         => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/rebuild.php',
		'pathVarsJs'              => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/portal.php',
		'varsJgaapAccountTitleBS' => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/<strNation>/JgaapAccountTitleBS.php',
		'varsJgaapAccountTitlePL' => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/<strNation>/JgaapAccountTitlePL.php',
		'varsJgaapAccountTitleCR' => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/<strNation>/JgaapAccountTitleCR.php',
		'varsTax'                 => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/<strNation>/tax.php',
		'varsJgaapFSBS'           => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/<strNation>/JgaapFSBS.php',
		'varsJgaapFSPL'           => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/<strNation>/JgaapFSPL.php',
		'varsJgaapFSCR'           => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/<strNation>/JgaapFSCR.php',
		'varsJgaapFSCS'           => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/<strNation>/JgaapFSCS.php',
		'varsDepartment'          => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/<strNation>/department.php',
		'varsPrinciples'          => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/principles.php',

		'pathAccountTitleBS'      => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/<strNation>/<numYearSheet>/<flagCorporation>/JgaapAccountTitleBS.php',
		'pathAccountTitlePL'      => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/<strNation>/<numYearSheet>/<flagCorporation>/JgaapAccountTitlePL.php',
		'pathAccountTitleCR'      => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/<strNation>/<numYearSheet>/<flagCorporation>/JgaapAccountTitleCR.php',
		'pathFSBS'                => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/<strNation>/<numYearSheet>/<flagCorporation>/JgaapFSBS.php',
		'pathFSPL'                => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/<strNation>/<numYearSheet>/<flagCorporation>/JgaapFSPL.php',
		'pathFSCR'                => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/<strNation>/<numYearSheet>/<flagCorporation>/JgaapFSCR.php',

		'pathNation'              => 'back/dat/nation/<strLang>/list.csv',
		'pathLang'                => 'back/dat/lang/<strLang>/list.csv',
		'pathDirNation'           => 'back/class/else/plugin/accounting/',
		'pathDirLang'             => 'back/tpl/vars/else/plugin/accounting/',
		'varsConfig'              => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/config.php',
		'path404'                 => 'back/dat/error/404FileNotFound.txt',
		'path403'                 => 'back/dat/error/403Forbidden.txt',
		'path404Img'              => 'front/else/lib/img/thumbnail/icon.png',
		'pathDirFile'             => 'back/dat/file/accounting/',
	);

	function __construct()
	{
	}


	/*
	 *
	 * */
	public function run()
	{
		global $classCheck;

		global $varsRequest;
		global $varsPluginAccountingPreference;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$this->_setInit();

		if (FLAG_API) {
			$str = ucwords($varsRequest['query']['ext']);
			$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/' . $str . ".php";
			$strClass = 'Code_Else_Plugin_Accounting_' . $str;
			if (!file_exists($path)) {
				exit;
			}
			require_once($path);
			$classCall = new $strClass;
			$classCall->allot();
			exit;
		}

		$flag = $classCheck->checkModule(array(
			'idModule' => $varsRequest['query']['module'],
			'flagType' => 'User'
		));

		if (!$flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}

		$strClass = '';
		if ($varsRequest['query']['ext']) {
			$str = ucwords($varsRequest['query']['ext']);
			if ($str == 'Entity'
				|| $str == 'Account'
				|| $str == 'AccountEntity'
				|| $str == 'Authority'
				|| $str == 'AccountEntityAuthority'
				|| preg_match("/^File/", $str)
				|| $str == 'Access'
			) {
				$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/' . $str . ".php";
				$strClass = 'Code_Else_Plugin_Accounting_' . $str;

			} else {
				$this->_setInitNation();
				$this->_setInitLang();

				$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/' . PLUGIN_ACCOUNTING_STR_NATION . '/' . ucwords(PLUGIN_ACCOUNTING_STR_NATION) . ".php";
				$strClass = 'Code_Else_Plugin_Accounting_' . ucwords(PLUGIN_ACCOUNTING_STR_NATION) . '_' . ucwords(PLUGIN_ACCOUNTING_STR_NATION);

			}
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

		}
	}

	/**

	 */
	protected function _setInit()
	{
		$classPluginAccountingInit = $this->_getInit();
		$classPluginAccountingInit->run();
	}

	/**

	 */
	protected function _setInitNation()
	{
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$strNation = '';
		if (!$varsPluginAccountingEntity || !$varsPluginAccountingAccount['idEntityCurrent']) {
			$strNation = $this->_self['strIniNation'];

		} else {
			$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
			$strNation = $varsPluginAccountingEntity[$idEntity]['strNation'];
		}

		define('PLUGIN_ACCOUNTING_STR_NATION', $strNation);
	}

	/**
		array(
			'strClass' => ''
		)
	 */
	protected function _getClassNation($arr)
	{
		$this->_setInitNation();

		$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/' . PLUGIN_ACCOUNTING_STR_NATION . '/' . ucwords(PLUGIN_ACCOUNTING_STR_NATION) . ".php";
		if (!file_exists($path)) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}
		require_once($path);

		$str = $arr['strClass'];
		$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/' . PLUGIN_ACCOUNTING_STR_NATION . '/' . $str . ".php";
		if (!file_exists($path)) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}
		require_once($path);
		$strClass = 'Code_Else_Plugin_Accounting_' . PLUGIN_ACCOUNTING_STR_NATION  . '_' . $str;
		$classCall = new $strClass;

		return $classCall;
	}

	/**

	 */
	protected function _setInitLang()
	{
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$strNation = '';
		if (!$varsPluginAccountingEntity || !$varsPluginAccountingAccount['idEntityCurrent']) {
			$strLang = $this->_self['strIniLang'];

		} else {
			$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
			$strLang = $varsPluginAccountingEntity[$idEntity]['strLang'];
		}

		define('PLUGIN_ACCOUNTING_STR_LANG', $strLang);
	}

	/**

	 */
	protected function _getInit()
	{
		global $classPluginAccountingInit;

		$classPluginAccountingInit = (FLAG_APC)? apc_fetch('classPluginAccountingInit') : null;
		if (is_null($classPluginAccountingInit)) {
			require_once(PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/Init.php');
			$classPluginAccountingInit = new Code_Else_Plugin_Accounting_Init();
			if (FLAG_APC) {
				apc_store('classPluginAccountingInit', $classPluginAccountingInit);
			}
		}

		return $classPluginAccountingInit;
	}

	/**

	 */
	protected function _sendOldError()
	{
		global $varsRequest;

		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
		$this->_setSearch(array('flag' => 40));
	}

    /**
     * array(
     *  'flagType' => string ex)routine,rebuild,varsNews,numNews,strModuleTitle
     * )
     */
	public function loop($arr)
	{
		if (!preg_match( "/^rebuild/", $arr['flagType'])) {
			$this->_setInit();
		}

		if ($arr['flagType'] == 'numNews') {
			$method = '_getPlugin' . ucwords($arr['flagType']);
			$data = $this->$method();

			return $data;

		} elseif($arr['flagType'] == 'varsNews') {
			$method = '_getPlugin' . ucwords($arr['flagType']);
			$data = $this->$method(array(
				'numList' => $arr['numList'],
			));

			return $data;

		} elseif ($arr['flagType'] == 'strModuleTitle') {
			$vars = $this->getVars(array(
				'path' => $this->_self['pathRuibuidVars'],
			));

			return $vars['strTitle'];

		} elseif ($arr['flagType'] == 'strModuleVersion') {
			return PLUGIN_ACCOUNTING_NUM_VERSION;

		/*
			(array(
				'flagType'   => 'accountStatus',
				'flagStatus' => 'delete',
				'arrId'      => $arr['arrId'],
			))
		 * */
		} elseif ($arr['flagType'] == 'accountStatus') {
			$path = PATH_BACK_CLASS_ELSE_PLUGIN . $this->_self['strTitle'] . '/AccountStatus.php';
			require_once($path);
			$strClass = 'Code_Else_Plugin_Accounting_AccountStatus';
			$classCall = new $strClass();

			$classCall->allot($arr);

		} elseif (preg_match( "/^rebuild/", $arr['flagType'])) {
			$path = PATH_BACK_CLASS_ELSE_PLUGIN . $this->_self['strTitle'] . '/Rebuild.php';
			require_once($path);
			$strClass = 'Code_Else_Plugin_Accounting_Rebuild';
			$classCall = new $strClass();

			return $classCall->run($arr);

		} else {
			$str = ucwords($arr['flagType']);
			$path = PATH_BACK_CLASS_ELSE_PLUGIN . $this->_self['strTitle'] . '/' . $str . ".php";
			if (!file_exists($path)) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
				}
				exit;

			}
			require_once($path);
			$strClass = 'Code_Else_Plugin_Accounting_' . $str;
			$classCall = new $strClass;
			$classCall->run($arr);

		}
	}

	/**
		$insCurrent->getDBAuthority(array(
			'flagSqlType' => $arr['flagSqlType'],ex) Select,Update,Delete,Output
			'arrData'     => ($arr['arrData']),ex) flag
		));
		return $array = array(
			'strSql'   => '',
			'arrValue' => array(),
		);
		or
		return 0
	 */
	public function getDBAuthority($arr)
	{
		global $classCheck;

		$flagSqlType = $arr['flagSqlType'];

		$flagAuthority = $classCheck->checkModuleAuthority(array(
			'idModule' => 'accounting',
		));

		if ($flagAuthority == 'webmaster' || $flagAuthority == 'admin') {
			$array = array(
				'strSql'   => '',
				'arrValue' => array(),
			);
			return $array;

		} elseif ($flagAuthority == 'user') {
			return 0;
		}
	}

	/**
	 *
	 */
	protected function _getDBAuthority($arr)
	{
		return array();
	}

	/**
		(array(
			'idAccount' => 0,
		));
	 */
	protected function _getVarsAuthority($arr)
	{
		global $varsPluginAccountingAccountsEntity;
		global $varsPluginAccountingAuthority;
		global $varsAccount;
		global $classCheck;
		global $varsPluginAccountingAccount;

		$idAccount = $varsAccount['id'];
		if ($arr['idAccount']) {
			$idAccount = $arr['idAccount'];
		}

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		if ($arr['idEntity']) {
			$idEntity = $arr['idEntity'];
		}

		$flagAuthority = $classCheck->checkModuleAuthority(array(
			'idModule'  => 'accounting',
			'idAccount' => $idAccount,
		));

		if ($flagAuthority == 'webmaster' || $flagAuthority == 'admin') {
			return 'admin';

		} elseif ($flagAuthority == 'user') {
			$idAuthority = $varsPluginAccountingAccountsEntity[$idAccount][$idEntity]['idAuthority'];
			$varsAuthority = $varsPluginAccountingAuthority[$idAuthority];

			return $varsAuthority;
		}

		return null;
	}

	/**
		$this->_checkModuleAdmin(array(
			'idAccount' => 0,
		));
	 */
	protected function _checkModuleAdmin($arr)
	{
		return $this->checkModuleAdmin(array(
			'idAccount' => $arr['idAccount'],
			'strModule' => 'accounting',
		));
	}

	/**
		$this->_setJs(array(
			'pathVars'        => '',
			'pathTpl'         => '',
			'arrFolder'       => array(),
			'strTableSearch'  => '',
			'strColumnSearch' => '',
			'flagEntitySearch'  => 0,
			'flagAccountSearch' => 0,
			'arrSearch'       => array(
				'idModule' => '',
				'numLotNow' => 0,
				'strTable'  => '',
				'arrOrder'  => array(),
				'arrWhere'  => array(),
			),
		));
	 */
	protected function _setJs($arr)
	{
		global $varsPluginAccountingAccount;
		global $classSmarty;

		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
		));

		$vars['portal']['varsNavi']['search']['varsDetail']['varsMyRecord']['varsFormList']['varsDetail'] = $this->_getMemo(array(
			'strTable'    => $arr['strTableSearch'],
			'strColumn'   => $arr['strColumnSearch'],
			'flagEntity'  => $arr['flagEntitySearch'],
			'flagAccount' => $arr['flagAccountSearch'],
		));

		if ($arr['flagEntitySearch']) {
			$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
			$arr['arrSearch']['arrWhere'][] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idEntity',
				'flagCondition' => 'eq',
				'value'         => $idEntity,
			);
		}

		$rows = $this->getSearch($arr['arrSearch']);

		$vars = $this->_updateSearch(array(
			'vars' => $vars,
			'rows' => $rows,
		));
		$json = json_encode($vars);
		$classSmarty->assign('varsLoad', $json);

		$classSmarty->assign('numNews', $this->getNumNews());
		$contents = $classSmarty->fetch($arr['pathTpl']);

		$this->sendJs(array(
			'data' => $contents,
		));
	}



	/**
		(array(
			'strTable'  => '',
			'strColumn' => '',
			'flagEntity'  => 0,
			'flagAccount' => 0,
		));
	 */
	protected function _getMemo($arr)
	{
		global $classDb;

		global $varsAccount;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$idAccount = $varsAccount['id'];

		$arrWhere = array();
		if ($arr['flagEntity'] && $idEntity) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idEntity',
				'flagCondition' => 'eq',
				'value'         => $idEntity,
			);
		} else {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idEntity',
				'flagCondition' => 'eq',
				'value'         => 0,
			);
		}

		if ($arr['flagAccount']) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idAccount',
				'flagCondition' => 'eq',
				'value'         => $idAccount,
			);
		}

		$arrWhere[] = array(
			'flagType'      => '',
			'strColumn'     => 'flagColumn',
			'flagCondition' => 'eq',
			'value'         => $arr['strColumn'],
		);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => $arr['strTable'],
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => $arrWhere,
		));

		if (!$rows['numRows']){
			return array();
		}

		$data = $rows['arrRows'][0]['jsonData'];

		if (!$data) {
			return array();
		}

		return $data;
	}

	/**
		$this->_getArrOptionNation($flagHash);
	 */
	protected function _getArrOptionNation($flagHash)
	{
		global $classFile;

		$path = $this->getPath(array(
			'path' => $this->_self['pathNation'],
		));

		$array = $classFile->getCsvRows(array('path' => $path));
		$arrayNationList = array();
        foreach ($array as $key => $value) {
			$str = $value['code'];
			$arrayNationList[$str] = $value['nation'];
		}

		$arrayNation = scandir($this->_self['pathDirNation']);
		$arrayList = array();
		$num = 0;
        foreach ($arrayNation as $key => $value) {
            $strFile = $value;
			$pathFile = $this->_self['pathDirNation'] .  $strFile;
			if ( preg_match( "/^\.{1,2}$/", $strFile) || !is_dir($pathFile)) {
                continue;
            }
			$row = array();
			$row['strTitle'] = $arrayNationList[$value];
			$row['value'] = $value;
			if ($flagHash) {
				$arrayList[$row['value']] = $row;

			} else {
				$arrayList[$num] = $row;
				$num++;
			}

		}

		return $arrayList;

	}

	/**
		(array(
			'flagAllUse'    => 0,
			'flagAuthority' => 'select',
			'idTarget'      => '',
		));
	 */
	protected function _checkAccess($arr)
	{
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingAccess;
		global $varsPluginAccountingAccountsEntity;

		$idAccount = $varsPluginAccountingAccount['id'];
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$idAccess = $varsPluginAccountingAccountsEntity[$idAccount][$idEntity]['idAccess'];
		$varsAccess = $varsPluginAccountingAccess[$idEntity][$idAccess];

		$varsAuthority = $this->_getVarsAuthority(array());

		if ($varsAuthority == 'admin') {
			return 1;

		} elseif ($varsAuthority['flagMy' . ucwords($arr['flagAuthority'])]) {
			if ($varsAccess['jsonData'][$arr['idTarget']] || $varsAccess['id'] == 1) {
				if ($arr['flagAllUse']) {
					if ($varsAuthority['flagAll' . ucwords($arr['flagAuthority'])]) {
						return 1;

					} else {
						return 0;
					}
				}
				return 1;
			}
		}

		return 0;
	}

	/**
		(array(
			'idTarget' => '',
		));
	 */
	protected function _getIdAutoIncrement($arr)
	{
		global $varsPluginAccountingPreference;

		$jsonIdAutoIncrement = $varsPluginAccountingPreference['jsonIdAutoIncrement'];

		return $jsonIdAutoIncrement[$arr['idTarget']];
	}

	/**
		(array(
			'idTarget'   => '',
			'varsTarget' => array(),
		));
	 */
	protected function _updateIdAutoIncrement($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $classPluginAccountingInit;
		global $varsPluginAccountingPreference;

		$arrData = ($varsPluginAccountingPreference['jsonIdAutoIncrement'])? $varsPluginAccountingPreference['jsonIdAutoIncrement'] : array();
		$str = $arr['idTarget'];
		$arrData[$str] = ($arr['varsTarget'])? $arr['varsTarget'] : array();
		$jsonIdAutoIncrement = json_encode($arrData);

		$stmt = $dbh->prepare('update accountingPreference set jsonIdAutoIncrement = ?;');
		$stmt->execute(array($jsonIdAutoIncrement));

		$classPluginAccountingInit->updateInitPreference();
	}

	/**
		$this->_getArrOptionNation($flagHash);
	 */
	protected function _getArrOptionLang($flagHash)
	{
		global $classFile;

		$path = $this->getPath(array(
			'path' => $this->_self['pathLang'],
		));

		$array = $classFile->getCsvRows(array('path' => $path));
		$arrayNationList = array();
        foreach ($array as $key => $value) {
			$str = $value['code'];
			$arrayNationList[$str] = $value['lang'];
		}

		$arrayNation = scandir($this->_self['pathDirLang']);
		$arrayList = array();
		$num = 0;
        foreach ($arrayNation as $key => $value) {
            $strFile = $value;
			$pathFile = $this->_self['pathDirLang'] .  $strFile;
			if ( preg_match( "/^\.{1,2}$/", $strFile) || !is_dir($pathFile)) {
                continue;
            }
			$row = array();
			$row['strTitle'] = $arrayNationList[$value];
			$row['value'] = $value;
			if ($flagHash) {
				$arrayList[$row['value']] = $row;

			} else {
				$arrayList[$num] = $row;
				$num++;
			}

		}

		return $arrayList;

	}

	/**
		$this->_setDbStampCheck(array(
			'strColumnAccount'    => '',
			'strColumnPreference' => '',
		));
	 */
	protected function _setDbStampCheck($arr)
	{
		global $classDb;

		$dbh = $classDb->setDbhMaster();

		try {
			$dbh->beginTransaction();

			$this->updateDbAccountStampCheck(array(
				'strColumn' => $arr['strColumnAccount'],
			));

			$this->_updateDbPreferenceStamp(array(
				'strColumn' => $arr['strColumnPreference'],
			));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}
	}

	/**
		$this->_updateDbPreferenceStamp(array(
			'strColumn' => '',
		));
	 */
	protected function _updateDbPreferenceStamp($arr)
	{
		global $classDb;
		global $classInit;

		global $varsPluginAccountingPreference;
		global $classPluginAccountingInit;

		$varsPluginAccountingPreference['jsonStampUpdate'][$arr['strColumn']] = TIMESTAMP;
		$jsonStampUpdate = json_encode($varsPluginAccountingPreference['jsonStampUpdate']);

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingPreference',
			'arrColumn' => array('jsonStampUpdate'),
			'arrWhere'  => array(),
			'arrValue'  => array($jsonStampUpdate),
		));

		$classPluginAccountingInit->updateInitPreference();
	}

	/**
		$this->_setNaviSearchSave(array(
			'pathVars'  => '',
			'strTable'  => '',
			'strColumn' => '',
			'flagEntity' => 0,
			'flagAccount' => 0,
		));
	 */
	protected function _setNaviSearchSave($arr)
	{
		global $varsRequest;
		global $classDb;
		$dbh = $classDb->getHandle();
		global $varsAccount;
		global $varsPluginAccountingAccount;

		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
		));

		$varsJson = $this->checkValueSearch(array(
			'varsValue'  => ($varsRequest['query']['jsonValue']['vars'])? $varsRequest['query']['jsonValue']['vars'] : array(),
			'varsSearch' => $vars['portal']['varsNavi']['search'],
		));

		$strJson = json_encode($varsJson);
		$this->checkTextSize(array(
			'flag' => 'errorDataMax',
			'str'  => $strJson,
		));
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$idAccount = $varsAccount['id'];
		$arrWhere = array();
		if ($arr['flagEntity']) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idEntity',
				'flagCondition' => 'eq',
				'value'         => $idEntity,
			);
		} else {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idEntity',
				'flagCondition' => 'eq',
				'value'         => 0,
			);
		}

		if ($arr['flagAccount']) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idAccount',
				'flagCondition' => 'eq',
				'value'         => $idAccount,
			);
		}

		$arrWhere[] = array(
			'flagType'      => '',
			'strColumn'     => 'flagColumn',
			'flagCondition' => 'eq',
			'value'         => $arr['strColumn'],
		);

		try {
			$dbh->beginTransaction();

			$classDb->updateRow(array(
				'idModule' => 'accounting',
				'strTable'  => $arr['strTable'],
				'arrColumn' => array('jsonData'),
				'flagAnd'   => 1,
				'arrWhere'  => $arrWhere,
				'arrValue'  => array($strJson),
			));

			if ($arr['flagAccount']) {
				$this->updateDbAccountStamp();
				$this->_updateDbPreferenceStamp(array('strColumn' => 'account'));
			}

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$this->sendVars(array(
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $this->_getMemo(array(
				'strTable'    => $arr['strTable'],
				'strColumn'   => $arr['strColumn'],
				'flagEntity'  => $arr['flagEntity'],
				'flagAccount' => $arr['flagAccount'],
			)),
		));
	}

	/**
		$this->_setNaviSearchDelete(array(
			'strTable'  => '',
			'strColumn' => '',
			'flagEntity' => 0,
			'flagAccount' => 0,
		));
	 */
	protected function _setNaviSearchDelete($arr)
	{
		global $varsRequest;
		global $classDb;
		global $varsAccount;
		global $varsPluginAccountingAccount;
		$dbh = $classDb->getHandle();

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$idAccount = $varsAccount['id'];

		$arrWhere = array();
		if ($arr['flagEntity']) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idEntity',
				'flagCondition' => 'eq',
				'value'         => $idEntity,
			);
		} else {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idEntity',
				'flagCondition' => 'eq',
				'value'         => 0,
			);
		}

		if ($arr['flagAccount']) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idAccount',
				'flagCondition' => 'eq',
				'value'         => $idAccount,
			);
		}

		$arrWhere[] = array(
			'flagType'      => '',
			'strColumn'     => 'flagColumn',
			'flagCondition' => 'eq',
			'value'         => $arr['strColumn'],
		);

		try {
			$dbh->beginTransaction();

			$classDb->updateRow(array(
				'idModule' => 'accounting',
				'strTable'  => $arr['strTable'],
				'arrColumn' => array('jsonData'),
				'flagAnd'   => 1,
				'arrWhere'  => $arrWhere,
				'arrValue'  => array(null),
			));

			if ($arr['flagAccount']) {
				$this->updateDbAccountStamp();
				$this->_updateDbPreferenceStamp(array('strColumn' => 'account'));

			} else {
				$this->_updateDbPreferenceStamp(array('strColumn' => 'adminMemo'));
			}

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$this->sendVars(array(
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(),
		));
	}

	/**
		$this->_setNaviSearchReload(array(
			'pathVars'  => '',
			'strTable'  => '',
			'strColumn' => '',
			'flagEntity' => 0,
			'flagAccount' => 0,
		));
	 */
	protected function _setNaviSearchReload($arr)
	{
		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
		));

		$vars['portal']['varsNavi']['search']['varsDetail']['varsMyRecord']['varsFormList']['varsDetail'] = $this->_getMemo(array(
			'strTable'  => $arr['strTable'],
			'strColumn' => $arr['strColumn'],
			'flagEntity'  => $arr['flagEntity'],
			'flagAccount' => $arr['flagAccount'],
		));

		$this->sendVars(array(
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $vars['portal']['varsNavi']['search']['varsDetail'],
		));
	}

	/**
		$this->_setNaviFolderSave(array(
			'pathVars'  => '',
			'strTable'  => '',
			'strColumn' => '',
			'flagEntity' => 0,
			'flagAccount' => 0,
		));
	 */
	protected function _setNaviFolderSave($arr)
	{
		global $varsRequest;

		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
		));

		$varsJson = $this->checkValueFolder(array(
			'varsValue'    => ($varsRequest['query']['jsonValue']['vars'])? $varsRequest['query']['jsonValue']['vars'] : array(),
			'varsTemplate' => $vars['portal']['varsNavi']['templateFolder']['varsDetail']['templateDetail'],
		));

		$this->_setNaviFolderSaveUpdate(array(
			'pathVars'    => $arr['pathVars'],
			'varsJson'    => $varsJson,
			'strTable'    => $arr['strTable'],
			'strColumn'   => $arr['strColumn'],
			'flagEntity'  => $arr['flagEntity'],
			'flagAccount' => $arr['flagAccount'],
		));
	}

	/**
		$this->_setNaviFolderSaveUpdate(array(
			'pathVars'  => '',
			'varsJson'  => '',
			'strTable'  => '',
			'strColumn' => '',
			'flagEntity' => 0,
			'flagAccount' => 0,
		));
	 */
	protected function _setNaviFolderSaveUpdate($arr)
	{
		global $varsRequest;
		global $classDb;
		global $varsAccount;
		global $varsPluginAccountingAccount;

		$strJson = json_encode($arr['varsJson']);
		$this->checkTextSize(array(
			'flag' => 'errorDataMax',
			'str'  => $strJson,
		));

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$idAccount = $varsAccount['id'];
		$arrWhere = array();
		if ($arr['flagEntity']) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idEntity',
				'flagCondition' => 'eq',
				'value'         => $idEntity,
			);

		} else {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idEntity',
				'flagCondition' => 'eq',
				'value'         => 0,
			);
		}

		if ($arr['flagAccount']) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idAccount',
				'flagCondition' => 'eq',
				'value'         => $idAccount,
			);
		}

		$arrWhere[] = array(
			'flagType'      => '',
			'strColumn'     => 'flagColumn',
			'flagCondition' => 'eq',
			'value'         => $arr['strColumn'],
		);

		$dbh = $classDb->getHandle();
		try {
			$dbh->beginTransaction();

			$classDb->updateRow(array(
				'idModule' => 'accounting',
				'strTable'  => $arr['strTable'],
				'arrColumn' => array('jsonData'),
				'flagAnd'  => 1,
				'arrWhere'  => $arrWhere,
				'arrValue'  => array($strJson),
			));

			if ($arr['flagAccount']) {
				$this->updateDbAccountStamp();
				$this->_updateDbPreferenceStamp(array('strColumn' => 'account'));

			} else {
				$this->_updateDbPreferenceStamp(array('strColumn' => 'adminMemo'));
			}

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$this->_setNaviFolderReload(array(
			'pathVars'    => $arr['pathVars'],
			'strColumn'   => $arr['strColumn'],
			'strTable'    => $arr['strTable'],
			'flagEntity'  => $arr['flagEntity'],
			'flagAccount' => $arr['flagAccount'],
		));

	}

	/**
		$this->_setNaviFolderReload(array(
			'pathVars'  => '',
			'strColumn' => '',
			'strTable'  => '',
			'flagEntity' => 0,
			'flagAccount' => 0,
		));
	 */
	protected function _setNaviFolderReload($arr)
	{
		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
		));

		$vars['portal']['varsNavi']['templateFolder']['varsDetail']['varsDetail'] = $this->_getMemo(array(
			'strTable'    => $arr['strTable'],
			'strColumn'   => $arr['strColumn'],
			'flagEntity'  => $arr['flagEntity'],
			'flagAccount' => $arr['flagAccount'],
		));

		if (!$vars['portal']['varsNavi']['templateFolder']['varsDetail']['varsDetail']) {
			$varsDetail = $vars['portal']['varsNavi']['templateFolder']['varsDetail']['templateDetail']['dir'];
			$vars['portal']['varsNavi']['templateFolder']['varsDetail']['varsDetail'][] = $varsDetail;
		}

		$this->sendVars(array(
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $vars['portal']['varsNavi']['templateFolder']['varsDetail']['varsDetail'],
		));
	}

	/**
		$this->_setJsEditor(array(
			'pathVars'  => '',
			'pathTpl'   => '',
			'arrFolder' => array(),
			'flagEntity' => 0,
			'flagAccount' => 0,
		));
	 */
	protected function _setJsEditor($arr)
	{
		global $classSmarty;

		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
		));

		$array = $arr['arrFolder'];
		foreach ($array as $key => $value) {
			$vars['portal']['varsNavi']['varsFolder'][$value['flagType']]['varsDetail'] = $this->_getMemo(array(
				'strTable'    => $value['strTable'],
				'strColumn'   => $value['strColumn'],
				'flagEntity'  => $value['flagEntity'],
				'flagAccount' => $value['flagAccount'],
			));
			if (!$vars['portal']['varsNavi']['varsFolder'][$value['flagType']]['varsDetail']) {
				$varsDetail = $vars['portal']['varsNavi']['templateFolder']['varsDetail']['templateDetail']['dir'];
				$vars['portal']['varsNavi']['varsFolder'][$value['flagType']]['varsDetail'][] = $varsDetail;
			}
		}

		$json = json_encode($vars);
		$classSmarty->assign('varsLoad', $json);

		$classSmarty->assign('numNews', $this->getNumNews());
		$contents = $classSmarty->fetch($arr['pathTpl']);

		$this->sendJs(array(
			'data' => $contents,
		));
	}

	/**
		$this->_setNaviFormatSave(array(
			'pathVars'  => '',
			'strColumn' => '',
			'strTable'  => '',
			'flagEntity' => 0,
			'flagAccount' => 0,
		));
	 */
	protected function _setNaviFormatSave($arr)
	{
		global $varsRequest;

		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
		));

		$varsJson = $this->checkValueFormat(array(
			'varsValue'    => ($varsRequest['query']['jsonValue']['vars'])? $varsRequest['query']['jsonValue']['vars'] : array(),
			'varsTemplate' => $vars['portal']['varsNavi']['templateFolder']['varsDetail']['templateDetail'],
		));

		$this->_setNaviFormatSaveUpdate(array(
			'pathVars'    => $arr['pathVars'],
			'varsJson'    => $varsJson,
			'strColumn'   => $arr['strColumn'],
			'strTable'    => $arr['strTable'],
			'flagEntity'  => $arr['flagEntity'],
			'flagAccount' => $arr['flagAccount'],
		));
	}

	/**
		$this->_setNaviFormatSaveUpdate(array(
			'pathVars'  => '',
			'varsJson'  => '',
			'strColumn' => '',
			'strTable'  => '',
			'flagEntity' => 0,
			'flagAccount' => 0,
		));
	 */
	protected function _setNaviFormatSaveUpdate($arr)
	{
		$this->_setNaviFolderSaveUpdate($arr);
	}

	/**
		$this->_setNaviFormatReload(array(
			'pathVars'  => '',
			'strColumn' => '',
			'strTable'  => '',
			'flagEntity' => 0,
			'flagAccount' => 0,
		));
	 */
	protected function _setNaviFormatReload($arr)
	{
		$this->_setNaviFolderReload($arr);
	}

	/**
	 *
	 */
	protected function _checkEntity()
	{
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		if (!$varsPluginAccountingEntity
			 || !$varsPluginAccountingAccount['idEntityCurrent']
			 || $varsPluginAccountingEntity[$varsPluginAccountingAccount['idEntityCurrent']]['flagConfig']
		) {
			$this->sendValue(array(
				'flag'    => 8,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}
	}

    /**
		(array(
			'path'      => '',
			'strLang'   => '',
			'strNation' => '',
		));
     */
	protected function _getVars($arr)
	{

		global $classEscape;

		if (!$arr['strTitle']) {
			$arr['strTitle'] = '';
		}

		if (!$arr['strLang']) {
			$arr['strLang'] = '';
		}

		if (!$arr['strNation']) {
			$arr['strNation'] = '';
		}

		$vars = $classEscape->getVars(array(
			'data'    => $arr['path'],
			'arr' => array(
				array('before' => '<strTitle>', 'after' => $arr['strTitle'],),
				array('before' => '<strLang>', 'after' => $arr['strLang'],),
				array('before' => '<strNation>', 'after' => $arr['strNation'],),
			),
		));

		return $vars;
	}


	/**
	 * array(
	 * 'varsTable' => &array
	 * 	arrId      => array
	 * )
	 */
	protected function _checkTableUpdateId($arr)
	{
		global $classCheck;

		if (!$arr['arrId']) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
			}
			exit;
		}
		$flag = $classCheck->checkValueFormat(array(
			'flagType' => 'num',
			'flagArr'  => 1,
			'value'    => $arr['arrId'],
		));

		if ($flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
			}
			exit;
		}
		$array = $arr['arrId'];

		foreach ($array as $key => $value) {
			if (!$arr['varsTable'][$value] || $arr['varsTable'][$value]['flagDefault']) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
				}
				exit;
			}
		}

	}

	/**
		$this->_updateVarsTree(array(
			'vars'       => array(),
			'idTarget'   => array(),
			'varsTarget' => array(),
		));
	 */
	protected function _insertVarsTree($arr)
	{
		$array = &$arr['vars'];
		foreach ($array as $key => $value) {
			if ($value['id'] == $arr['idTarget']) {
				$array[$key]['child'] = $arr['varsTarget'];
				break;
			}
			if ($value['child']) {
				$array[$key]['child'] = $this->_insertVarsTree(array(
					'vars'       => $array[$key]['child'],
					'idTarget'   => $arr['idTarget'],
					'varsTarget' => $arr['varsTarget'],
				));

			}
		}

		return $array;
	}

	/**
		$this->_removeVarsTree(array(
			'vars'       => array(),
			'idTarget'   => array(),
		));
	 */
	protected function _removeVarsTree($arr)
	{
		$array = &$arr['vars'];
		$flag = 0;
		foreach ($array as $key => $value) {
			if ($value['id'] == $arr['idTarget']) {
				$flag = 1;
				break;
			}
			if ($value['child']) {
				$arrayNew[$key]['child'] = $this->_removeVarsTree(array(
					'vars'       => $array[$key]['child'],
					'idTarget'   => $arr['idTarget'],
				));

			}
		}
		$arrayNew = array();
		if ($flag) {
			foreach ($array as $key => $value) {
				if ($value['id'] != $arr['idTarget']) {
					$arrayNew[] = $value;
				}
			}
			$array = $arrayNew;
		}

		return $array;
	}

	/**
		$this->_removeVarsTreeChild(array(
			'vars'       => array(),
			'idTarget'   => array(),
		));
	 */
	protected function _removeVarsTreeChild($arr)
	{
		$array = &$arr['vars'];
		foreach ($array as $key => $value) {
			if ($value['id'] == $arr['idTarget']) {
				$array[$key]['child'] = array();
				break;
			}
			if ($value['child']) {
				$array[$key]['child'] = $this->_removeVarsTreeChild(array(
					'vars'       => $array[$key]['child'],
					'idTarget'   => $arr['idTarget'],
					'varsTarget' => $arr['varsTarget'],
				));

			}
		}

		return $array;
	}

	/**
	 *
	 * normal tempPrev tempNext
	 */
	protected function _checkCurrent()
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingAccountsEntity;

		if (!$varsPluginAccountingAccount['idEntityCurrent']) {
			return 0;
		}

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriodLock = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriodLock'];
		$numFiscalPeriodCurrent = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];

		if ($numFiscalPeriodLock < $numFiscalPeriodCurrent) {
			return 1;
		}

		return 0;
	}

	/**
	 (array(
	 ))
	 */
	protected function _getCurrentFlagNow($arr)
	{
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		if (!is_null($arr['idEntity'])) {
			$idEntity = $arr['idEntity'];
//var_dump(__LINE__);
		}
		$numFiscalPeriod = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];
		$numFiscalPeriodLock = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriodLock'];
		$net = $numFiscalPeriod - $numFiscalPeriodLock;
		$numFiscalPeriodCurrent = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		if (!is_null($arr['numFiscalPeriod'])) {
			$numFiscalPeriodCurrent = $arr['numFiscalPeriod'];
//var_dump(__LINE__);
		}
//var_dump($numFiscalPeriodLock,$numFiscalPeriodCurrent,$net);
		if ($numFiscalPeriodLock < $numFiscalPeriodCurrent) {
			if ($net == 2) {
				if ($numFiscalPeriod == $numFiscalPeriodCurrent) {
					return 'tempNext';

				} else {
					return 'tempPrev';
				}

			} else {
				return 'normal';
			}

		} else {
			return 'done';
		}
	}

	/**
	 *
	 * tempNext
	 */
	protected function _checkEditPrev()
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingAccountsEntity;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];
		$numFiscalPeriodLock = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriodLock'];
		$net = $numFiscalPeriod - $numFiscalPeriodLock;
		$numFiscalPeriodCurrent = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];

		if ($numFiscalPeriod == $numFiscalPeriodCurrent) {
			if ($net == 2) {
				return 1;
			}
		}

		return 0;
	}

	/**
	 *
	 * done
	 */
	protected function _checkLock()
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingAccountsEntity;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriodLock = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriodLock'];
		$numFiscalPeriodCurrent = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];

		if ($numFiscalPeriodLock >= $numFiscalPeriodCurrent) {
			return 1;
		}

		return 0;
	}

	/**
	 *
	 * normal or tempPrev
	 */
	protected function _checkEditCalc()
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingAccountsEntity;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriodCurrent = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$numFiscalPeriodLock = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriodLock'];
		$flag = $numFiscalPeriodCurrent - $numFiscalPeriodLock;

		if ($flag == 1) {
			return 1;
		}

		return 0;
	}

	/**
	 */
	protected function _sendOld()
	{
		$this->sendVars(array(
			'flag'    => 40,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(),
		));
	}

	/**
	 */
	protected function _sendOldFlag()
	{
		$this->sendVars(array(
			'flag'    => 8,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(),
		));
	}

	/**
		(array(
			'idParent' => '',
			'vars'    => array(),
		))
	 */
	protected function _setTreeId($arr)
	{
		$array = &$arr['vars'];

		foreach ($array as $key => $value) {
			$id = $arr['idParent'] . '-' . $key;
			$array[$key]['id'] = $id;

			if ($value['child']) {
				$array[$key]['child'] = $this->_setTreeId(array(
					'vars'       => $array[$key]['child'],
					'idParent'   => $id,
				));
			}
		}

		return $array;
	}

	/**

	 */
	protected function _getVarsDepartment($arr)
	{
		global $classDb;
		global $varsAccount;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingEntityDepartment',
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
			),
		));

		$array = $rows['arrRows'];
		$arrStrTitle = array();
		$arrSelectTag = array();
		$num = 0;
		foreach ($array as $key => $value) {
			$data = array();
			$data['strTitle'] = $value['strTitle'];
			$data['value'] = $value['idDepartment'];
			$arrSelectTag[$num] = $data;
			$num++;

			$arrStrTitle[$value['idDepartment']]['strTitle'] = $value['strTitle'];

		}

		$data = array(
			'arrStrTitle'  => $arrStrTitle,
			'arrSelectTag' => $arrSelectTag,
		);

		return $data;
	}

	/**
	 */
	protected function _getPluginNumNews()
	{
		global $varsPluginAccountingPreference;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAuthority;

		if (!$varsPluginAccountingEntity) {
			return 0;
		}

		return 0;

	}


	/**
     * array(
     *  'numList' => int
     * )
	 */
	public function _getPluginVarsNews($arr)
	{
		global $varsPluginAccountingPreference;
		global $classCheck;
		global $varsAccounts;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingAuthority;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccountsId;

		if (!$varsPluginAccountingEntity) {
			return array('numRows' => 0,'arrRows'=>array());
		}

		return array('numRows' => 0,'arrRows'=>array());

	}

	/**

	 */
	protected function _send403Output()
	{
		global $classRequest;

		$classRequest->output(array(
			'path'         => $this->_self['path403'],
			'strFileType'  => 'txt',
			'strFileName'  => '403.txt',
		));
		exit;
	}


	/**

	 */
	protected function _send404Output()
	{
		global $classRequest;

		$classRequest->output(array(
			'path'         => $this->_self['path404'],
			'strFileType'  => 'txt',
			'strFileName'  => '404.txt',
		));
		exit;
	}

	/**
		(array(
			'num' => 0,
			'numLevel' => 0,
			'flagType' => '',
		))
	 */
	protected function _updateCalc($arr)
	{
		global $classDisplay;

		return $classDisplay->getNumDisplay($arr);
	}

	/**
	 *
		(array(
					'vars'     => $rows['arrRows'][0]['jsonJgaapFSPL'],
					'idTarget' => 'costOfSales',
					'strMatch' => '^(products|productsSum)$',
		))
	 */
	protected function _removeTreeData($arr)
	{
		$strMatch = $arr['strMatch'];

		$arrayBlock = $this->_getTreeBlockChild(array(
			'vars'     => $arr['vars'],
			'idTarget' => $arr['idTarget'],
		));
		if (!$arrayBlock) {
			return $arr['vars'];
		}
		$varsTarget = array();
		foreach ($arrayBlock as $key => $value) {
			if (!preg_match("/$strMatch/", $value['vars']['idTarget'])) {
				$varsTarget[] = $value;
			}
		}
		$vars = $this->_insertTreeBlock(array(
			'vars'       => $arr['vars'],
			'idTarget'   => $arr['idTarget'],
			'varsTarget' => $varsTarget,
		));

		return $vars;

	}

	/**
		(array(

		))
	 */
	protected function _getTreeBlockChild($arr)
	{
		$array = &$arr['vars'];
		foreach ($array as $key => $value) {
			if ($value['vars']['idTarget'] == $arr['idTarget']) {
				return $value['child'];
			}
			if ($value['child']) {
				$flagArr = $this->_getTreeBlockChild(array(
					'vars'     => $array[$key]['child'],
					'idTarget' => $arr['idTarget'],
				));
				if ($flagArr) {
					return $flagArr;
				}
			}
		}
	}

	/**
		(array(
			'vars'       => array(),
			'idTarget'   => array(),
			'varsTarget' => array(),
		));
	 */
	protected function _insertTreeBlock($arr)
	{
		$array = &$arr['vars'];
		foreach ($array as $key => $value) {
			if ($value['vars']['idTarget'] == $arr['idTarget']) {
				$array[$key]['child'] = $arr['varsTarget'];
				break;
			}
			if ($value['child']) {
				$array[$key]['child'] = $this->_insertTreeBlock(array(
					'vars'       => $array[$key]['child'],
					'idTarget'   => $arr['idTarget'],
					'varsTarget' => $arr['varsTarget'],
				));

			}
		}

		return $array;
	}

	/**
		(array(

		))
	 */
	protected function _getTreeBlock($arr)
	{
		$array = &$arr['vars'];
		foreach ($array as $key => $value) {
			if ($value['vars']['idTarget'] == $arr['idTarget']) {
				return $array;
			}
			if ($value['child']) {
				$flagArr = $this->_getTreeBlock(array(
					'vars'     => $array[$key]['child'],
					'idTarget' => $arr['idTarget'],
				));
				if ($flagArr) {
					return $flagArr;
				}
			}
		}
	}

	/**
		(array(
			'vars'      => $arr['varsItem']['varsFS']['jsonJgaapFS' . $arr['flagFS']],
			'strTarget' => $strTitle,
			'num'       => 0,
			'strSelf'   => $varsTarget['strTitle'],
			'idSelf'    => $varsTarget['vars']['idTarget'],
		))
	 */
	protected function _checkTreeStrTitle($arr)
	{
		$array = &$arr['vars'];
		$num = $arr['num'];

		foreach ($array as $key => $value) {
			if (!is_null($value['vars']['flagUse'])) {
				if ($value['strTitle'] == $arr['strTarget']) {
					if ($arr['strSelf']) {
						if ($value['strTitle'] != $arr['strSelf']) {
							if (!is_null($arr['idSelf'])) {
								if ($value['vars']['idTarget'] != $arr['idSelf']) {
									$num++;
								}
							} else {
								$num++;
							}
						}
					} else {
						$num++;
					}
				}
			}
			if ($value['child']) {
				$num += $this->_checkTreeStrTitle(array(
					'vars'      => $array[$key]['child'],
					'strTarget' => $arr['strTarget'],
					'num'       => 0,
					'strSelf'   => $arr['strSelf'],
					'idSelf'    => $arr['idSelf'],
				));
			}
		}

		return $num;
	}

	/**
		(array(
			'vars'     => array(),
			'idTarget' => $idTarget,
		))

	 */
	protected function _getVarsDelete($arr)
	{
		$array = &$arr['vars'];

		$flag = 0;
		foreach ($array as $key => $value) {
			if (!is_null($value['vars']['flagUse'])) {
				if ($value['vars']['idTarget'] == $arr['idTarget']) {
					$flag = 1;
					break;
				}
			}
			if ($value['child']) {
				$array[$key]['child'] = $this->_getVarsDelete(array(
					'vars'       => $array[$key]['child'],
					'idTarget'   => $arr['idTarget'],
				));

			}
		}
		$arrayNew = array();
		if ($flag) {
			foreach ($array as $key => $value) {
				if ($value['vars']['idTarget'] != $arr['idTarget']) {
					$arrayNew[] = $value;
				}
			}

			return $arrayNew;
		}
		return $array;
	}

	/**
		(array(
			'numPage'     => 0,
			'strMenu'     => '',
			'strFileType' => '',
		))
	 */
	protected function _getFileTitle($arr)
	{
		global $classTime;

		global $varsAccount;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$strTitle = $varsPluginAccountingEntity[$idEntity]['strTitle'];

		$classTime->setTimeZone(array('data' => $varsAccount['numTimeZone']));
		$arrDate = $classTime->getLocal(array('stamp' => TIMESTAMP));

		$numPage = '_p' . $arr['numPage'];
		$numPage = (!is_null($arr['numPage']))? $numPage : '';

		$strMenu = '_' . $arr['strMenu'];
		$strMenu = (!is_null($arr['strMenu']))? $strMenu : '';
		$strFileType = $arr['strFileType'];

		$strFileName = $strTitle
					 . $strMenu
					 . $numPage
					 . '_'
					 . $arrDate['strYear']
					 . $arrDate['strMonth']
					 . $arrDate['strDate']
					 . '_'
					 . $arrDate['strHour']
					 . $arrDate['strMin'];

		if ($strFileType) {
			$strFileName .= '.' . $strFileType;
		}

		//$strFileName = mb_convert_encoding($strFileName, 'sjis', 'utf8');

		return $strFileName;
	}

	/**

	 */
	protected function _getVarsHtml($arr)
	{
		global $classSmarty;

		$array = $arr['varsData'];
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				continue;
			}
			$str = '{$' . $key . '}';
			$arr['tmplStr'] = str_replace($str, $value, $arr['tmplStr']);
		}

		if ($arr['value']) {
			$array = $arr['value'];
			foreach ($array as $key => $value) {
				$str = '{$value.' . $key . '}';
				$arr['tmplStr'] = str_replace($str, $value, $arr['tmplStr']);
			}
		}

		return $arr['tmplStr'];
	}

	/**
		(array(
			'flagType'   => 'File',
		))
	 */
	protected function _getClassAccountingCalc($arr)
	{
		require_once(PATH_BACK_CLASS_ELSE_PLUGIN . $this->_self['strTitle'] . '/' . 'Calc' . $arr['flagType'] . '.php');
		$strNation = ucwords($strNation);
		$strClass = 'Code_Else_Plugin_Accounting_Calc' . $arr['flagType'];

		$classCall = new $strClass($arr);
		return $classCall;
	}

	/**

	 */
	protected function _getVarsNation()
	{
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$strNation = '';
		if (!$varsPluginAccountingEntity || !$varsPluginAccountingAccount['idEntityCurrent']) {
			$strNation = $this->_self['strIniNation'];

		} else {
			$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
			$strNation = $varsPluginAccountingEntity[$idEntity]['strNation'];
		}

		return $strNation;
	}
}
