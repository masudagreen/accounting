<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Base_Base extends Code_Else_Core_Base_ModuleAbstract
{
	protected $_self = array(
		'strTitle' => 'base',
		'path' => array(
			'file' => array(
				'tplHtml'     => 'else/core/base/html/index.html',
				'varsHtml'    => 'back/tpl/vars/else/core/base/<strLang>/html/index.php',
				'datLang'     => 'back/dat/lang/<strLang>/list.csv',
				'varsJsRoot'  => 'back/tpl/vars/else/core/base/<strLang>/js/root.php',
			),
			'dir'  => array(

			),
		),
	);

    function __construct()
    {

    }

    /**
     *
     */
	public function run()
	{
		global $varsRequest;
		global $classCheck;

		if (FLAG_API) {
			$str = ucwords($varsRequest['query']['ext']);
			$path = PATH_BACK_CLASS_ELSE_CORE_BASE . $str . ".php";
			$strClass = 'Code_Else_Core_Base_' . $str;
			require_once($path);
			$classCall = new $strClass;
			$classCall->allot();
			exit;
		}

		$flag = $classCheck->checkModule(array(
			'idModule' => 'Base',
			'flagType' => 'User'
		));

		if (!$flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}
		if ($varsRequest['query']['ext']) {
			$str = ucwords($varsRequest['query']['ext']);
			$path = PATH_BACK_CLASS_ELSE_CORE_BASE . $str . ".php";
			if (!file_exists($path)
				|| preg_match( "/^(Access|Rebuild|Init|Routine|ModuleAbstract|Attest)$/", $str)
			) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__ . '/' .$str. '/' .$path);
				}
				exit;
			}
			require_once($path);
			$strClass = 'Code_Else_Core_Base_' . $str;
			$classCall = new $strClass;
			$classCall->run();

		} else {
			require_once(PATH_BACK_CLASS_ELSE_CORE_BASE . "Root.php");
			$classCall = new Code_Else_Core_Base_Root();
			$classCall->run();
		}
	}

    /**
     * array(
     *  'flagType' => string
     * )
     */
	public function loop($arr)
	{
		if($arr['flagType'] == 'strModuleTitle') {
			$vars = $this->getVars(array(
				'path' => $this->_self['path']['file']['varsJsRoot'],
			));

			return $vars['strTitle'];

		} elseif (preg_match( "/^rebuild/", $arr['flagType'])) {
			$path = PATH_BACK_CLASS_ELSE_CORE_BASE . 'Rebuild.php';
			require_once($path);
			$strClass = 'Code_Else_Core_Base_Rebuild';
			$classCall = new $strClass();
			$classCall->run($arr);

		} else {
			$str = ucwords($arr['flagType']);
			$path = PATH_BACK_CLASS_ELSE_CORE_BASE . $str . ".php";
			if (!file_exists($path)) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
				}
				exit;

			}
			require_once($path);
			$strClass = 'Code_Else_Core_Base_' . $str;
			$classCall = new $strClass;

			return $classCall->run($arr);

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
		global $classEscape;
		global $classCheck;

		$flagSqlType = $arr['flagSqlType'];

		$flagAuthority = $classCheck->checkModuleAuthority(array(
			'idModule' => 'base',
		));

		if ($flagAuthority == 'webmaster' || $flagAuthority == 'admin') {
			$array = array(
				'strSql'   => '',
				'arrValue' => array(),
			);
			return $array;

		} elseif ($flagAuthority == 'user') {
			return $this->_getDBAuthority($arr);

		}

	}

	/**
	 *
	 */
	public function getStrModuleTitle()
	{
		global $varsAccount;
		global $varsPreference;
		global $varsModule;

		global $classEscape;

		$vars = $arr['vars'];
		$id = $varsAccount['idModule'];

		if ($varsAccount['flagWebmaster']
			|| preg_match( "/,base,/", $varsModule[$id]['arrCommaIdModuleAdmin'])
		) {
			$array = $varsPreference['jsonModule'];

		} else {
			$array = $classEscape->splitCommaArrayData(array(
				'data' => $varsModule[$id]['arrCommaIdModuleUser']
			));
			$arrayNew = array();
			foreach ($array as $key => $value) {
				$arrayNew[$value] = 1;
			}
			$array = $arrayNew;
		}

		$arrayNew = array();
		foreach ($array as $key => $value) {
			$strDir = $key;
			if ($key == 'base') {
				$strTitle = $this->loop(array('flagType'=>'strModuleTitle'));
				$arrayNew[$key] = $strTitle;

			} else {
				$strFile = ucwords($key);
				$path = PATH_BACK_CLASS_ELSE_PLUGIN . $strDir . '/' . $strFile . '.php';
				if (!file_exists($path)) {
					continue;
				}
				require_once($path);
				$strClass = 'Code_Else_Plugin_' . $strFile . '_' . $strFile;
				$classCall = new $strClass;
				$strTitle = $classCall->loop(array(
					'flagType' => 'strModuleTitle',
				));
				$arrayNew[$key] = $strTitle;
			}

		}

		return $arrayNew;
	}

	/**
		$this->_setNaviSearchSave(array(
			'pathVars'  => '',
			'strTable'  => '',
			'strColumn' => '',
		));
	 */
	protected function _setNaviSearchSave($arr)
	{
		global $varsRequest;
		global $classDb;
		$dbh = $classDb->getHandle();
		global $classInit;
		global $varsAccount;

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

		try {
			$dbh->beginTransaction();

			if ($arr['strTable'] == 'baseAccountMemo') {
				$classDb->updateRow(array(
					'idModule' => 'base',
					'strTable'  => $arr['strTable'],
					'arrColumn' => array('jsonData'),
					'flagAnd'   => 1,
					'arrWhere'  => array(
						array(
							'flagType'      => '',
							'strColumn'     => 'flagColumn',
							'flagCondition' => 'eq',
							'value'         => $arr['strColumn'],
						),
						array(
							'flagType'      => 'num',
							'strColumn'     => 'idAccount',
							'flagCondition' => 'eq',
							'value'         => $varsAccount['id'],
						),
					),
					'arrValue'  => array($strJson),
				));
				$this->updateDbAccountStamp();
				$this->updateDbPreferenceStamp(array('strColumn' => 'account'));
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
				'strTable'  => $arr['strTable'],
				'strColumn' => $arr['strColumn'],
			)),
		));
	}


	/**
		$this->_setNaviSearchDelete(array(
			'strTable'  => '',
			'strColumn' => '',
		));
	 */
	protected function _setNaviSearchDelete($arr)
	{
		global $varsRequest;
		global $classDb;
		global $classInit;
		global $varsAccount;
		$dbh = $classDb->getHandle();

		try {
			$dbh->beginTransaction();

			if ($arr['strTable'] == 'baseAccountMemo') {
				$classDb->updateRow(array(
					'idModule' => 'base',
					'strTable'  => $arr['strTable'],
					'arrColumn' => array('jsonData'),
					'flagAnd'   => 1,
					'arrWhere'  => array(
						array(
							'flagType'      => '',
							'strColumn'     => 'flagColumn',
							'flagCondition' => 'eq',
							'value'         => $arr['strColumn'],
						),
						array(
							'flagType'      => 'num',
							'strColumn'     => 'idAccount',
							'flagCondition' => 'eq',
							'value'         => $varsAccount['id'],
						),
					),
					'arrValue'  => array(null),
				));
				$this->updateDbAccountStamp();
				$this->updateDbPreferenceStamp(array('strColumn' => 'account'));

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
		));

		$this->sendVars(array(
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $vars['portal']['varsNavi']['search']['varsDetail'],
		));
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

			$this->updateDbPreferenceStamp(array(
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
		$this->_setJs(array(
			'pathVars'        => '',
			'pathTpl'         => '',
			'arrFolder'       => array(),
			'strTableSearch'  => '',
			'strColumnSearch' => '',
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
		global $classSmarty;

		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
		));

		$vars['portal']['varsNavi']['search']['varsDetail']['varsMyRecord']['varsFormList']['varsDetail'] = $this->_getMemo(array(
			'strTable'  => $arr['strTableSearch'],
			'strColumn' => $arr['strColumnSearch'],
		));

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
		$this->_setJsEditor(array(
			'pathVars'  => '',
			'pathTpl'   => '',
			'arrFolder' => array(),
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
				'strTable'  => $value['strTable'],
				'strColumn' => $value['strColumn'],
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
		$this->_getMemo(array(
			'strTable'  => '',
			'strColumn' => '',
		));
	 */
	protected function _getMemo($arr)
	{
		global $varsAccount;
		global $classDb;

		$rows = $classDb->getSelect(array(
			'idModule' => 'base',
			'strTable' => 'baseAccountMemo',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'   => 1,
			'arrWhere'  => array(
				array(
					'flagType'      => '',
					'strColumn'     => 'flagColumn',
					'flagCondition' => 'eq',
					'value'         => $arr['strColumn'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idAccount',
					'flagCondition' => 'eq',
					'value'         => $varsAccount['id'],
				),
			),
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
		$this->_setNaviFolderSave(array(
			'pathVars'  => '',
			'strTable'  => '',
			'strColumn' => '',
		));
	 */
	protected function _setNaviFolderSave($arr)
	{
		global $varsRequest;
		global $varsAccount;

		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
		));

		$varsJson = $this->checkValueFolder(array(
			'varsValue'    => ($varsRequest['query']['jsonValue']['vars'])? $varsRequest['query']['jsonValue']['vars'] : array(),
			'varsTemplate' => $vars['portal']['varsNavi']['templateFolder']['varsDetail']['templateDetail'],
		));

		$this->_setNaviFolderSaveUpdate(array(
			'pathVars'  => $arr['pathVars'],
			'varsJson'  => $varsJson,
			'strTable'  => $arr['strTable'],
			'strColumn' => $arr['strColumn'],
		));
	}

	/**
		$this->_setNaviFolderSaveUpdate(array(
			'pathVars'  => '',
			'varsJson'  => '',
			'strTable'  => '',
			'strColumn' => '',
		));
	 */
	protected function _setNaviFolderSaveUpdate($arr)
	{
		global $varsRequest;
		global $classDb;
		global $classInit;
		global $varsAccount;

		$strJson = json_encode($arr['varsJson']);
		$this->checkTextSize(array(
			'flag' => 'errorDataMax',
			'str'  => $strJson,
		));

		$dbh = $classDb->getHandle();
		try {
			$dbh->beginTransaction();

			if ($arr['strTable'] == 'baseAccountMemo') {
				$classDb->updateRow(array(
					'idModule'  => 'base',
					'strTable'  => $arr['strTable'],
					'arrColumn' => array('jsonData'),
					'flagAnd'   => 1,
					'arrWhere'  => array(
						array(
							'flagType'      => '',
							'strColumn'     => 'flagColumn',
							'flagCondition' => 'eq',
							'value'         => $arr['strColumn'],
						),
						array(
							'flagType'      => 'num',
							'strColumn'     => 'idAccount',
							'flagCondition' => 'eq',
							'value'         => $varsAccount['id'],
						),
					),
					'arrValue'  => array($strJson),
				));
				$this->updateDbAccountStamp();
				$this->updateDbPreferenceStamp(array('strColumn' => 'account'));
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
			'pathVars'  => $arr['pathVars'],
			'strColumn' => $arr['strColumn'],
			'strTable'  => $arr['strTable'],
		));
	}

	/**
		$this->_setNaviFolderReload(array(
			'pathVars'  => '',
			'strColumn' => '',
			'strTable'  => '',
		));
	 */
	protected function _setNaviFolderReload($arr)
	{
		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
		));

		$vars['portal']['varsNavi']['templateFolder']['varsDetail']['varsDetail'] = $this->_getMemo(array(
			'strTable'  => $arr['strTable'],
			'strColumn' => $arr['strColumn'],
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
		$this->_setNaviFormatSave(array(
			'pathVars'  => '',
			'strColumn' => '',
			'strTable'  => '',
		));
	 */
	protected function _setNaviFormatSave($arr)
	{
		global $varsRequest;
		global $varsAccount;

		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
		));

		$varsJson = $this->checkValueFormat(array(
			'varsValue'    => ($varsRequest['query']['jsonValue']['vars'])? $varsRequest['query']['jsonValue']['vars'] : array(),
			'varsTemplate' => $vars['portal']['varsNavi']['templateFolder']['varsDetail']['templateDetail'],
		));

		$this->_setNaviFormatSaveUpdate(array(
			'pathVars'  => $arr['pathVars'],
			'varsJson'  => $varsJson,
			'strColumn' => $arr['strColumn'],
			'strTable'  => $arr['strTable'],
		));
	}

	/**
		$this->_setNaviFormatSaveUpdate(array(
			'pathVars'  => '',
			'varsJson'  => '',
			'strColumn' => '',
			'strTable'  => '',
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
		));
	 */
	protected function _setNaviFormatReload($arr)
	{
		$this->_setNaviFolderReload($arr);
	}

}
