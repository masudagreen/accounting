<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
abstract class Code_Else_Core_Base_ModuleAbstract
{
    /**
     *
     */
	abstract public function run();

    /**
     * $arr = array(
	 * 	'flagType' => '',
	 * 	'rebuild' => array(
	 * 		'flagDb'  => int,
	 * 		'arrDbIdModule'  => array,
	 * 		'flagCss'    => int,
	 * 		'flagJs'     => int,
     * 	),
	 * 	'routine' => array(
	 * 		'flagMonth' => int,
	 * 		'flagDate'  => int,
	 * 		'flagHour'  => int,
     * 	),
     * )
     */
	abstract protected function loop($arr);


	/**
	 * $arr = array(
	 *     'path' => string,
	 * )
	 */
	public function getVars($arr)
	{
		global $classEscape;

		$arrWhere = array(
			array('before' => '<strLang>', 'after' => STR_LANG,),
		);

		if ($arr['strPlugin']) {
			$arrWhere[] = array('before' => '<strPlugin>', 'after' => $arr['strPlugin'],);
		}

		$vars = $classEscape->getVars(array(
			'data' => $arr['path'],
			'arr'  => $arrWhere,
		));

		return $vars;
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

	}

	/**
	 * $arr = array(
	 *     'path' => string,
	 *     'vars' => array,
	 * )
	 */
	public function sendHtml($arr)
	{
		global $classSmarty;
		global $classRequest;

		$array = $arr['vars'];
        foreach ($array as $key => $value) {
			$classSmarty->assign($key, $value);
        }
		$path = $this->getPath(array('path' => $arr['path']));
		$output = $classSmarty->fetch($path);

		$classRequest->send(array(
			'flagType' => 'html',
			'data'     => $output,
		));
		exit;
	}

	/**
	 * $arr = array(
	 *     'path' => string,
	 *     'vars' => array,
	 * )
	 */
	public function getHtml($arr)
	{
		global $classSmarty;
		global $classEscape;

		$array = $arr['vars'];
        foreach ($array as $key => $value) {
			$classSmarty->assign($key, $value);
        }
		$path = $this->getPath(array('path' => $arr['path']));
		$output = $classSmarty->fetch($path);

		if (!$arr['flagNone']) {
			$output = $classEscape->obfuscate(array( 'data' => $output));
		}

		return $output;
	}


	/**
	 * $arr = array(
	 *     'path' => string,
	 * )
	 */
	public function getPath($arr)
	{
		global $classEscape;

		$path = $classEscape->loopReplace(array(
			'data' => $arr['path'],
			'arr'  => array(
				array('before' => '<strLang>', 'after' => STR_LANG,),
			),
		));

		return $path;
	}

	/**
	 * $arr = array(
	 *     'pathLangDat' => string,
	 *     'pathLangDir' => string,
	 * )
	 */
	public function getLangOption($arr)
	{

		$path = $this->getPath(array(
			'path' => $arr['pathLangDat'],
		));

		global $classFile;
		$array = $classFile->getCsvRows(array('path' => $path));
		$arrayLangList = array();
        foreach ($array as $key => $value) {
			$str = $array[$key]['code'];
			$arrayLangList[$str] = $array[$key]['lang'];
		}

		$arrayLang = scandir($arr['pathLangDir']);
		$arrayList = array();
		$num = 0;
        foreach ($arrayLang as $key => $value) {
            if ( preg_match( "/^\.{1,2}$/", $value)) {
                continue;
            }
			$row = array();
			$row['strTitle'] = $arrayLangList[$value];
			$row['value'] = $value;
			$arrayList[$num] = $row;
			$num++;
		}

		return $arrayList;
	}



	/**
	 * $arr = array(
	 *     'flag'      => mix,
	 *     'stamp'     => array,
	 *     'numNews'   => int,
	 *     'vars'      => array,
	 * )
	 */
	public function sendVars($arr)
	{
		global $classRequest;

		$array = array(
			'flag'    => (!is_null($arr['flag']))? $arr['flag'] : 1,
			'numNews' => ($arr['numNews'])? $arr['numNews'] : 0,
			'stamp'   => ($arr['stamp'])? $arr['stamp'] : '',
			'data'    => $arr['vars'],
		);

		$json = json_encode($array);
		$classRequest->send(array(
			'flagType' => 'json',
			'data'     => $json,
		));
	}

	/**
	 * $arr = array(
	 *     'vars' => array,
	 * )
	 */
	public function getValue($arr)
	{
		global $varsRequest;

		$array = $arr['vars'];
		$formValue = $varsRequest['query']['jsonValue']['vars'];

		foreach ($array as $key => $value) {
			$str = $array[$key]['id'];
			$data = $formValue[$str];

			if	(is_null($data) && $array[$key]['flagMustUse']) {
				if (FLAG_TEST) {
					var_dump($varsRequest['query']);
					var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
					var_dump($value);
				}
				exit;
			}
			$array[$key]['value'] = $data;
		}

		return $array;
	}

	/**
	 * $arr = array(
	 *     'values' => array,
	 * )
	 */
	public function checkValue($arr)
	{
		global $classCheck;
		global $classEscape;

		$array = $classCheck->checkValue(array('arr' => $arr['values']));

		$arrayNew = array();
		$arrayNewColumn = array();
		$arrayNewValue = array();
		foreach ($array as $key => $value) {
			$array[$key] = $this->checkValueSelect($array[$key]);
			if ($array[$key]['flagErrorNow']) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
					var_dump($value);
				}
				exit;
			}

			$id = $array[$key]['id'];
			$id = $classEscape->toLower(array('str' => $id));
			$arrayNew[$id] = $array[$key]['value'];
			$arrayNewColumn[] = $id;
			$arrayNewValue[] = $array[$key]['value'];
		}

		$data = array(
			'arr'       => $arrayNew,
			'arrColumn' => $arrayNewColumn,
			'arrValue'  => $arrayNewValue,
		);

		return $data;
	}

	/**
		$this->checkModuleAdmin(array(
			'idAccount' => 0,
			'strModule' => '',
		));
	 */
	public function checkModuleAdmin($arr)
	{
		global $varsModule;
		global $varsAccounts;

		if ($varsAccounts[$arr['idAccount']]['flagWebmaster']) {
			return 1;
		}

		$idModule = $varsAccounts[$arr['idAccount']]['idModule'];
		$strModule = ',' . $arr['strModule'] . ',';
		if ( preg_match( "/$strModule|,base,/", $varsModule[$idModule]['arrCommaIdModuleAdmin'])) {
			return 1;
		}

		return 0;
	}

	/**
		$this->checkValueSelect(array())
	 */
	public function checkValueSelect(&$vars)
	{
		if ($vars['flagTag'] != 'select') {
			return $vars;
		}

		$array = $vars['arrayOption'];
		if (!$array) {
			return $vars;
		}

		if ($vars['flagMultiple']) {
			$flag = 0;
			$num = 0;
			if (!$vars['value']) {
				return $vars;
			}
			foreach ($array as $key => $value) {
				if (is_null($vars['value'][$value['value']])) {
					$flag = 1;
				}
				$num++;
				$vars['value'][$value['value']] = ((int) $vars['value'][$value['value']])? 1 : 0;
			}
			if (count($array) != $num) {
				$flag = 2;
			}

			if (!$flag) {
				return $vars;
			}

		} else {
			foreach ($array as $key => $value) {
				if ($value['value'] == $vars['value']) {
					return $vars;
				}
			}
		}

		$this->sendOldError();

		$vars['flagErrorNow'] = 1;

		return $vars;
	}

	/**

	 */
	public function sendOldError()
	{
		global $varsRequest;

		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$varsRequest['query']['jsonSearch']['numLotNow'] = 0;

		$method = '_setSearch';
		if (method_exists($this, $method)) {
			$this->$method(array('flag' => 40));
		}
	}


	/**
		$this->checkStampReload(array(
			'stampTarget' => 0,
			'flagSearch'  => 0
		));
	 */
	public function checkStampReload($arr)
	{
		global $varsRequest;

		$flag = 0;
		if ($arr['flagSearch']) {
			if ($varsRequest['query']['jsonSearch']['flagReload'] && $varsRequest['query']['jsonStamp']['stamp']) {
				$flag = 1;
			}

		} else {
			if ($varsRequest['query']['jsonStamp']['stamp']) {
				$flag = 1;
			}
		}

		if ($flag) {
			if ($arr['stampTarget'] <= $varsRequest['query']['jsonStamp']['stamp']) {
				$this->sendVars(array(
					'flag'    => 10,
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => '',
				));
			}
		}
	}

	/**
		$this->getStamp();
	 */
	public function getStamp()
	{
		global $varsRequest;

		$id = $varsRequest['query']['jsonStamp']['id'];
		$data = array(
			'stamp' => ($id)? TIMESTAMP : 0,
			'id'    => ($id)? $id    : '',
		);

		return $data;
	}

	/**
	 *
	 */
	public function getNumNews()
	{
		global $varsPreference;
		global $varsAccount;
		global $classFile;

		$array = $varsPreference['jsonModule'];
		$num = 0;
		foreach ($array as $key => $value) {
			$strDir = $key;
			$strFile = ucwords($key);
			$path = PATH_BACK_CLASS_ELSE_PLUGIN . $strDir . '/' . $strFile . '.php';
			if (!file_exists($path)) {
				continue;
			}
			require_once($path);
			$strClass = 'Code_Else_Plugin_' . $strFile . '_' . $strFile;
			$classCall = new $strClass;
			$data = $classCall->loop(array(
				'flagType' => 'numNews',
			));
			$num += $data['numNews'];
		}

		if ($num) {
			global $classDb;
			$dbh = $classDb->setDbhMaster();
			try {
				$dbh->beginTransaction();

				$this->updateDbAccountStampCheck(array(
					'strColumn' => 'news',
				));
				$this->updateDbPreferenceStamp(array('strColumn' => 'account'));

				$dbh->commit();
			} catch (PDOException $e) {
				$dbh->rollBack();
				if (FLAG_TEST) {
					var_dump($e->getMessage());
				}
				exit;
			}
		}

		return $num;
	}

	/**
		$this->updateDbAccountArrSpaceStrTag(array(
			'idTarget'       => '',
			'arrSpaceStrTag' => '',
		));
	 */
	public function updateDbAccountArrSpaceStrTag($arr)
	{
		global $classDb;
		global $classInit;
		global $varsAccounts;

		$classDb->updateRow(array(
			'idModule'  => 'base',
			'strTable'  => 'baseAccount',
			'arrColumn' => array('arrSpaceStrTag', 'stampUpdate'),
			'arrWhere'  => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'id',
					'flagCondition' => 'eq',
					'value'         => $arr['idTarget'],
				),
			),
			'arrValue'  => array($arr['arrSpaceStrTag'], TIMESTAMP),
		));

		$classInit->updateVarsAll(array(
			'vars'     => &$varsAccounts,
			'strVars'  => 'varsAccounts',
			'strTable' => 'baseAccount',
		));

	}

	/**
		$this->updateDbAccountStampCheck(array(
			'strColumn' => '',
		));
	 */
	public function updateDbAccountStampCheck($arr)
	{
		global $classDb;
		global $classInit;
		global $varsAccount;

		$varsAccount['jsonStampCheck'][$arr['strColumn']] = TIMESTAMP;
		$jsonStampCheck = json_encode($varsAccount['jsonStampCheck']);

		$classDb->updateRow(array(
			'idModule'  => 'base',
			'strTable'  => 'baseAccount',
			'arrColumn' => array('jsonStampCheck'),
			'arrWhere'  => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'id',
					'flagCondition' => 'eq',
					'value'         => $varsAccount['id'],
				),
			),
			'arrValue'  => array($jsonStampCheck),
		));

		$classInit->updateVarsAccount();


	}

	/**
		$this->updateDbAccountStamp();
	 */
	public function updateDbAccountStamp()
	{
		global $classDb;
		global $classInit;
		global $varsAccount;

		$classDb->updateRow(array(
			'idModule'  => 'base',
			'strTable'  => 'baseAccount',
			'arrColumn' => array('stampUpdate'),
			'arrWhere'  => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'id',
					'flagCondition' => 'eq',
					'value'         => $varsAccount['id'],
				),
			),
			'arrValue'  => array(TIMESTAMP),
		));

		$classInit->updateVarsAccount();

	}

	/**
		$this->updateDbPreferenceStamp(array(
			'strColumn' => '',
		));
	 */
	public function updateDbPreferenceStamp($arr)
	{
		global $classDb;
		global $classInit;
		global $varsPreference;

		$varsPreference['jsonStampUpdate'][$arr['strColumn']] = TIMESTAMP;
		$jsonStampUpdate = json_encode($varsPreference['jsonStampUpdate']);

		$classDb->updateRow(array(
			'idModule'  => 'base',
			'strTable'  => 'basePreference',
			'arrColumn' => array('jsonStampUpdate'),
			'arrWhere'  => array(),
			'arrValue'  => array($jsonStampUpdate),
		));

		$classInit->updateVarsPreference();

	}

	/**
	 * $arr = array(
	 *		'idModule'       => string,
	 *		'strTable'       => string,
	 *		'stampUpdate'    => stamp,
	 * )
	 */
	public function getPluginNumNews($arr)
	{
		global $classDb;
		global $varsAccount;

		(int) $varsAccount['jsonStampCheck']['baseNews'];

		if ($arr['stampUpdate'] <= $varsAccount['jsonStampCheck']['baseNews']) {
			$data = array(
				'numNews'  => 0,
			);

			return $data;
		}

		$rows = $classDb->getSelect(array(
			'idModule' => $arr['idModule'],
			'strTable' => $arr['strTable'],
			'arrOrder' => array(
				'strColumn' => 'id',
				'flagDesc'  => 1,
			),
			'arrWhere' => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'stampRegister',
					'flagCondition' => 'big',
					'value'         => $varsAccount['jsonStampCheck']['baseNews'],
				),
			),
			'arrColumn' => array('id'),
		));

		$data = array(
			'numNews'  => $rows['numRows'],
		);

		return $data;
	}


	/**
	 * $arr = array(
	 *		'idModule'            => string,
	 *		'strTable'            => string,
	 *		'stampUpdate'         => stamp,
	 *		'arrColumn'           => array,
	 *		'numList'             => int,
	 * )
	 */
	public function getPluginVarsNews($arr)
	{

	}

	/**
	 * $arr = array(
	 *     'flag' => mix,
	 *     'stamp' => array,
	 *     'numNews' => int,
	 *     'vars' => array,
	 * )
	 */
	public function sendValue($arr)
	{
		$this->sendVars($arr);
	}

	/**
	 * $arr = array(
	 *     'data' => mix,
	 * )
	 */
	public function sendJs($arr)
	{
		global $classRequest;
		global $classEscape;

		if (FLAG_OBFUSCATE) {
			$arr['data'] = $classEscape->obfuscate(array( 'data' => $arr['data']) );
		}

		$classRequest->send(array(
			'flagType' => 'javascript',
			'data'     => $arr['data'],
		));
		exit;
	}

	/**

	 */
	public function setToken()
	{
		global $classDb;
		$dbh = $classDb->setDbhMaster();
		global $classDisplay;

		global $varsAccount;
		global $varsRequest;
		global $varsMedia;

		$token = $varsRequest['query']['token'];
		$stmt = $dbh->prepare('select * from baseToken where token = ?;');
		$stmt->execute(array($token));
		$vars = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$vars = $row;
			break;
		}

		if ($token) {
			if ($vars) {
				return $token;
			}
		}

		$str = MICROTIMESTAMP . $classDisplay->getPassword(array(
			'numMark'  => 5,
			'numNum'   => 5,
			'numBig'   => 5,
			'numSmall' => 5,
		));
		$token = hash('sha256', $str);
		$idAccount = ($varsAccount['id'])? $varsAccount['id'] : null;

		try {
			$dbh->beginTransaction();

			$stampLimit = TIMESTAMP - NUM_SESSION * 1;
			$stmt = $dbh->prepare('delete from baseToken where stampRegister < ?;');
			$stmt->execute(array($stampLimit));

			$stmt = $dbh->prepare('insert into baseToken(stampRegister, token, idAccount) values (?, ?, ?);');
			$stmt->execute(array(TIMESTAMP, $token, $idAccount));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		return $token;
	}

	/**
	 * $arr = array(
	 *     'vars' => array,
	 * )
	 */
	public $varsVarsTarget = array();
	public function getVarsTarget($arr)
	{
		$this->varsVarsTarget = array();
		$this->_getVarsTargetChild($arr['vars']);

		return $this->varsVarsTarget;
	}

	/**
	 *
	 */
	public function _getVarsTargetChild($arr)
	{
		global $varsRequest;
		if ($this->varsVarsDetail) {
			return;
		}
		$arrValue = $varsRequest['query']['jsonValue'];
		$array = $arr;
		foreach ($array as $key => $value) {
			if	($arrValue['idTarget'] == $array[$key]['vars']['idTarget']) {
				$this->varsVarsTarget = $array[$key];
				return;
			}
			if ($array[$key]['child']) {
				$this->_getVarsTargetChild($array[$key]['child']);
			}
		}
	}

	/**
		$rows = $this->getSearch(array(
			'idModule'    => '',
			'numLotNow'   => 0,
			'strTable'    => '',
			'arrJoin'     => array(),
			'arrJoins'    => array(),
			'arrOrder'    => array(),
			'arrWhere'    => array(),
			'flagAnd' => array(),
			'insCurrent'  => array(),
		));
	 */
	public function getSearch($arr)
	{
		global $varsRequest;
		global $varsAccount;
		global $classDb;

		$numStart = $arr['numLotNow'] * $varsAccount['numList'];
		$numEnd = $varsAccount['numList'];
		$arrLimit = array(
			'numStart' => $numStart, 'numEnd' => $numEnd,
		);
		if (is_null($arr['numLotNow'])) {
			$arrLimit = array();
		}

		$rows = $classDb->getSelect(array(
			'idModule'    => $arr['idModule'],
			'strTable'    => $arr['strTable'],
			'arrJoin'     => ($arr['arrJoin'])? $arr['arrJoin'] : '',
			'arrJoins'    => ($arr['arrJoins'])? $arr['arrJoins'] : '',
			'arrLimit'    => $arrLimit,
			'arrOrder'    => $arr['arrOrder'],
			'arrWhere'    => $arr['arrWhere'],
			'flagAnd'     => ($arr['flagAnd'])? $arr['flagAnd'] : '',
			'insCurrent'  => ($arr['insCurrent'])? $arr['insCurrent'] : '',
			'arrData'     => ($arr['arrData'])? $arr['arrData'] : '',
		));

		return $rows;
	}


	/**
		$this->checkSearch(array(
			'arrOrder' => array(),
			'arrWhere' => array(),
		));
	 */
	public function checkSearch($arr)
	{
		global $varsRequest;

		if (!$varsRequest['query']['jsonSearch']) {
			return;
		}

		$this->_checkSearchNumLotNow();
		$this->_checkSearchOrder($arr['arrOrder']);
		$this->_checkSearchWhere($arr['arrWhere']);

	}

	/**
	 *
	 */
	protected function _checkSearchWhere($arr)
	{
		global $classCheck;
		global $varsRequest;

		$array = $varsRequest['query']['jsonSearch']['ph']['arrWhere'];
		$arrayCheck = $arr['templateDetail']['firstOption'];

		foreach ($array as $key => $value) {

			$strA = $value['flagType'] . '-' . $value['strColumn'];
			$strB = $value['flagType'] . '-' . $value['strColumn'] . '-' . $value['flagType'];
			$flag = 0;

			foreach ($arrayCheck as $keyCheck => $valueCheck) {
				if ($strA == $valueCheck['value'] || $strB == $valueCheck['value']) {
					$flag = 1;
					break;
				}
			}

			if (!$flag) {
				if (FLAG_TEST) {
					var_dump($strB);
					var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
				}
				exit;
			}

			if ($value['flagType'] == 'stamp' || $value['flagType'] == 'num') {
				//(int) $array[$key]['value'];
			}
			$flag = $classCheck->checkValueFormat(array(
				'flagType' => $value['flagType'],
				'value'    => $value['value'],
			));
			if ($flag) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
				}
				exit;
			}
		}
	}

	/**
	 *
	  */
	protected function _checkSearchOrder($arr)
	{
		global $classCheck;
		global $varsRequest;

		if (!$varsRequest['query']['jsonSearch']['ph']['arrOrder']) {
			return;
		}

		$strColumn = $varsRequest['query']['jsonSearch']['ph']['arrOrder']['strColumn'];
		$array = $arr['itemOption'];

		foreach ($array as $key => $value) {
			if ($strColumn == $array[$key]['value']) {
				return;
			}
		}

		if (FLAG_TEST) {
			var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
		}
		exit;
	}

	/**
	 *
	 */
	protected function _checkSearchNumLotNow()
	{
		global $classCheck;
		global $varsRequest;

		$flag = $classCheck->checkValueWord(array(
			'flagType' => 'num',
			'value'    => $varsRequest['query']['jsonSearch']['numLotNow'],
		));

		if ($flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
			}
			exit;
		}
	}

	/**
		$varsJson = $this->checkValueSearch(array(
			'varsValue'  => array(),
			'varsSearch' => array(),
		));
	 */
	public function checkValueSearch($arr)
	{
		$array = $arr['varsValue'];
		$arrayNew = array();
		$num = 0;
		foreach ($array as $key => $value) {

			$tmplVars = $arr['varsSearch']['varsDetail']['varsMyRecord']['varsFormList']['templateDetail'];
			$tmplVars['id'] = $key + 1;
			$tmplVars['numSort'] = $key + 1;
			$tmplVars['value'] = $value['value'];
			if (is_null($value['vars'])) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
				}
				exit;
			}
			$tmplVars['vars'] = $value['vars'];

			$tmplVars['vars']['flagNow'] = $this->_checkValueSearchFlagNow(array(
				'varsSearch' => $arr['varsSearch'],
				'flagNow'    => $tmplVars['vars']['flagNow'],
			));

			$tmplVars['vars']['varsSort'] = $this->_checkValueSearchSort(array(
				'varsSearch' => $arr['varsSearch'],
				'vars'       => $tmplVars['vars']['varsSort'],
			));

			if ($tmplVars['vars']['flagNow'] == 'item') {
				$tmplVars['vars']['varsItem'] = $this->_checkValueSearchItem(array(
					'varsSearch' => $arr['varsSearch'],
					'vars'       => $tmplVars['vars']['varsItem'],
				));
			}
			$arrayNew[$num] = $tmplVars;
			$num++;
		}

		return $arrayNew;
	}

	/**
		$this->_checkValueSearchItem(array(
			'varsSearch' => array(),
			'vars'       => array(),
		));
	 */
	protected function _checkValueSearchItem($arr)
	{
		$flag = 0;
		if (is_null($arr['vars'])) {
			$flag = 1;
		}

		if ($flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
			}
			exit;
		}

		$arrayNew = array();
		$num = 0;
		$array = $arr['vars'];
		foreach ($array as $key => $value) {
			$tmplVars = $arr['varsSearch']['varsDetail']['varsSearchItem']['templateDetail']['varsDetail'];
			$tmplVars['id'] = $key + 1;
			$flag = 0;
			$arrayFirst = $arr['varsSearch']['varsDetail']['varsSearchItem']['templateDetail']['firstOption'];
			foreach ($arrayFirst as $keyFirst => $valueFirst) {
				if ($arrayFirst[$keyFirst]['value'] == $array[$key]['firstValue']) {
					$flag = 1;
					$tmplVars['firstValue'] = $arrayFirst[$keyFirst]['value'];
					break;
				}
			}
			if (!$flag) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
				}
				exit;
			}

			$flag = 0;
			$data = preg_split("/-/", $tmplVars['firstValue']);
			$tmplVars['flagType'] = $data[0];
			$flagOption = ($data[2])? $data[2] : '';

			$str = $tmplVars['flagType'] . 'Option';
			$arrayOption = $arr['varsSearch']['varsDetail']['varsSearchItem']['templateDetail'][$str];
			foreach ($arrayOption as $keyOption => $valueOption) {
				if ($arrayOption[$keyOption]['value'] == $array[$key]['secondValue']) {
					$flag = 1;
					$tmplVars['secondValue'] = $arrayOption[$keyOption]['value'];
					break;
				}
			}
			if (!$flag) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
				}
				exit;
			}

			$flag = 0;
			if ($array[$key]['flagOption']) {
				$str = $array[$key]['flagOption'];
				$arrayOption = $arr['varsSearch']['varsDetail']['varsSearchItem']['templateDetail']['restOption'][$str];
				foreach ($arrayOption as $keyOption => $valueOption) {
					if ($arrayOption[$keyOption]['value'] == $array[$key]['restValue']) {
						$flag = 1;
						$tmplVars['restValue'] = $arrayOption[$keyOption]['value'];
						$tmplVars['flagOption'] = $flagOption;
						break;
					}
				}
				if (!$flag) {
					if (FLAG_TEST) {
						var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
					}
					exit;
				}

			} else {
				$tmplVars['restValue'] = ($array[$key]['restValue'])? $array[$key]['restValue'] : '';
			}

			$arrayNew[$num] = $tmplVars;
			$num++;
		}

		return $arrayNew;
	}

	/**
		$this->_checkValueSearchSort(array(
			'varsSearch' => array(),
			'vars'       => array(),
		));
	 */
	protected function _checkValueSearchSort($arr)
	{
		$flag = 0;
		if (is_null($arr['vars'])) {
			$flag = 1;
		}
		if ($flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
			}
			exit;
		}
		$flag = 0;
		$tmplVars = $arr['varsSearch']['varsDetail']['varsSearchSort']['varsDetail'];
		$array = $arr['varsSearch']['varsDetail']['varsSearchSort']['itemOption'];
		foreach ($array as $key => $value) {
			if ($array[$key]['value'] == $arr['vars']['itemValue']) {
				$flag = 1;
				$tmplVars['itemValue'] = $array[$key]['value'];
				break;
			}
		}
		if (!$flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
			}
			exit;
		}
		$flag = 0;
		$array = $arr['varsSearch']['varsDetail']['varsSearchSort']['sortOption'];
		foreach ($array as $key => $value) {
			if ($array[$key]['value'] == $arr['vars']['sortValue']) {
				$flag = 1;
				$tmplVars['sortValue'] = $array[$key]['value'];
				break;
			}
		}
		if (!$flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
			}
			exit;
		}

		return $tmplVars;
	}

	/**
		$this->_checkValueSearchFlagNow(array(
			'varsSearch' => array(),
			'flagNow'    => 0,
		));
	 */
	protected function _checkValueSearchFlagNow($arr)
	{
		$flag = 0;
		if (is_null($arr['flagNow'])) {
			$flag = 1;
		}
		if ($flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
			}
			exit;
		}
		$array = $arr['varsSearch']['varsDetail']['varsStatus']['switchList'];
		foreach ($array as $key => $value) {
			if ($value == $arr['flagNow']) {
				$flag = 1;
				break;
			}
		}
		if (!$flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
			}
			exit;
		}

		return $arr['flagNow'];
	}

	/**
		$this->checkValueFolder(array(
			'varsValue'    => array(),
			'varsTemplate' => array(),
		));
	 */
	protected $varsValueFolder = array();
	public function checkValueFolder($arr)
	{
		$this->varsValueFolder = array();
		$this->varsValueFolder = $this->_checkValueFolderChild(array(
			'varsNew'      => array(),
			'varsValue'    => $arr['varsValue'],
			'varsTemplate' => $arr['varsTemplate'],
		));

		return $this->varsValueFolder;
	}

	/**
		$this->_checkValueFolderChild(array(
			'varsNew'      => array(),
			'varsValue'    => array(),
			'varsTemplate' => array(),
		));
	 */
	protected function _checkValueFolderChild($arr)
	{
		global $classCheck;

		$array = $arr['varsValue'];

		foreach ($array as $key => $value) {

			$vars = $arr['varsTemplate']['dir'];

			$vars['flagFoldNow'] = ($value['flagFoldNow'])? 1 : 0;
			$vars['strTitle'] = $value['strTitle'];
			$arr['varsNew'][$key] = $vars;

			if ($value['varsInside']) {
				$arrayInside = $value['varsInside'];
				$num = 0;
				foreach ($arrayInside as $keyInside => $valueInside) {
					$varsFile = $arr['varsTemplate']['file'];
					$varsFile['strTitle'] = $valueInside['strTitle'];
					$varsFile['strClass'] = $valueInside['strClass'];
					$varsWhere = $arr['varsTemplate']['templateWhere'];

					$flag = $classCheck->checkValueWord(array(
						'flagType' => $varsWhere['flagType'],
						'value'    => $valueInside['vars']['arrWhere'][0]['value'],
					));

					if ($flag) {
						if (FLAG_TEST) {
							var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
						}
						exit;
					}
					$varsWhere['value'] = $valueInside['vars']['arrWhere'][0]['value'];
					$varsFile['idTarget'] = $valueInside['vars']['arrWhere'][0]['value'];
					$varsFile['vars']['arrWhere'][0] = $varsWhere;
					$arr['varsNew'][$key]['varsInside'][$num] = $varsFile;
					$num++;
				}
			}

			if ($array[$key]['child']) {
				$arr['varsNew'][$key]['child'] = $this->_checkValueFolderChild(array(
					'varsNew'      => $arr['varsNew'][$key]['child'],
					'varsValue'    => $array[$key]['child'],
					'varsTemplate' => $arr['varsTemplate'],
				));
			}
		}

		return $arr['varsNew'];
	}

	/**
		$this->checkValueFormat(array(
			'varsValue'    => array(),
			'varsTemplate' => array(),
		));
	 */
	protected $varsValueFormat = array();
	public function checkValueFormat($arr)
	{
		$this->varsValueFormat = array();
		$this->varsValueFormat = $this->_checkValueFormatChild(array(
			'varsNew'      => array(),
			'varsValue'    => $arr['varsValue'],
			'varsTemplate' => $arr['varsTemplate'],
		));

		return $this->varsValueFormat;
	}

	/**
		$this->_checkValueFormatChild(array(
			'varsNew'      => array(),
			'varsValue'    => array(),
			'varsTemplate' => array(),
		));
	 */
	protected function _checkValueFormatChild($arr)
	{
		$array = $arr['varsValue'];
		foreach ($array as $key => $value) {
			if ($array[$key]['flagFoldUse']) {
				$vars = $arr['varsTemplate']['dir'];
				$vars['flagFoldNow'] = ($value['flagFoldNow'])? 1 : 0;
				$arr['varsNew'][$key] = $vars;
				if ($array[$key]['child']) {
					$arr['varsNew'][$key]['child'] = $this->_checkValueFormatChildren(array(
						'varsNew'      => $arr['varsNew'][$key]['child'],
						'varsValue'    => $array[$key]['child'],
						'varsTemplate' => $arr['varsTemplate'],
					));
				}

			} else {
				$vars = $arr['varsTemplate']['file'];
				$vars['strTitle'] = $value['strTitle'];
				$arrayTmpl = $arr['varsTemplate']['templateVars'];
				foreach ($arrayTmpl as $keyTmpl => $valueTmpl) {
					$vars['vars'][$keyTmpl] =
					($value['vars'][$keyTmpl])? $value['vars'][$keyTmpl] : $valueTmpl;
				}
				$arr['varsNew'][$key] = $vars;
			}
		}

		return $arr['varsNew'];
	}

	protected function _checkValueFormatChildren($arr)
	{
		$array = $arr['varsValue'];
		foreach ($array as $key => $value) {
			$vars = $arr['varsTemplate']['log'];
			$vars['strTitle'] = $value['strTitle'];
			$arrayTmpl = $arr['varsTemplate']['templateVars'];
			foreach ($arrayTmpl as $keyTmpl => $valueTmpl) {
				$vars['vars'][$keyTmpl] =
				($value['vars'][$keyTmpl])? $value['vars'][$keyTmpl] : $valueTmpl;
			}
			$arr['varsNew'][$key] = $vars;
		}

		return $arr['varsNew'];
	}

	/**
		(array(
			'flag'        => '',
			'str'         => '',
			'flagReturn'  => 0,
		));
	 */
	public function checkTextSize($arr)
	{
		$bite = strlen(bin2hex($arr['str'])) / 2;
		if ($bite > NUM_MAX_TEXT_SIZE) {
			if (!$arr['flagReturn']) {
				$this->sendVars(array(
					'flag'    => $arr['flag'],
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));
			} else {
				return 1;
			}

		}
	}

	/**
		php5.3 -> php5.4 batch

		batch for IllegalStringOffset

		(
			'data' mix,
			'str' string
		);
	 */
	public function batchIllegalStringOffset($data,$str)
	{
		if (is_array($data)) {
			if ($data[$str]) {
				return true;
			}
		}

		return false;
	}





}
