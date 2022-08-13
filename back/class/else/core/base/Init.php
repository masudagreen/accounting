<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Base_Init
{
	protected $_self = array(
		'numVersion'            => '1.50.10',
		'flagConfig'            => 0,
		'flagAPI'               => 0,
		'flagRequest'           => 'post',
		'flagObfuscate'         => 1,
		'flagCheckUpdate'       => 0,
		'pathTop'               => '',
		'strEncoding'           => 'UTF-8',
		'strSystemLang'         => 'ja',
		'numSystemTimeZone'     => 9,
		'numSessionLoginSecond' => 3600,
		'numSession'            => 90000,
		'flagTest'              => 0,
		'flagAPC'               => 0,
		'strSystemHoliday'      => 'jp',
		'numMaxUploadSize'      => 1048576,
		'numMaxTextSize'        => 4000000000,//longtext 4294967295
		'datLang'               => 'back/dat/lang/<strLang>/list.csv',
		'pathInfo'              => 'http://rucaro.org/',
		'pathInfoSSL'           => 'https://rucaro.org/',
	);

	function __construct()
	{
		$arr = @func_get_arg(0);
		if (!$arr) {
			return;
		}
		foreach ($arr as $key => $value) {
			$this->_self[$key] = $value;
		}
	}

	 /**
	  *
	  */
    public function run()
    {
    	$this->_setDefine();
    	$this->_setSystem();
		$this->_setAbstract();
    	$this->_setClass();
    	$this->_setVars();
		$this->_setAccess();

    }

	 /**
	  *
	  */
	protected function _setAbstract()
	{
		require_once(PATH_BACK_CLASS_ELSE_CORE_BASE . 'ModuleAbstract.php');
	}

	 /**
	  *
	  */
	protected function _setAccess()
	{
		if ($this->_self['flagConfig']) {
			return;
		}
		require_once(PATH_BACK_CLASS_ELSE_CORE_BASE . "Access.php");
		$classAccess = new Code_Else_Core_Base_Access();
		$classAccess->run();

	}

	 /**
	  *
	  */
	protected function _setDefine()
	{
		$dateTime = new DateTime(null, new DateTimeZone("UTC"));
		$stamp = $dateTime->format('U');

		define('TIMESTAMP', $stamp);
		define('MICROTIMESTAMP', microtime());

		define('PATH_TOP', $this->_self['pathTop']);
		define('PATH_BACK', PATH_TOP . "/back/");
		define('PATH_CONFIG_FILE', PATH_TOP . "/config.php");

		define('PATH_BACK_CLASS', PATH_BACK . "class/");
		define('PATH_BACK_DAT',   PATH_BACK . "dat/");
		define('PATH_BACK_DB',    PATH_BACK . "db/");
		define('PATH_BACK_TPL',   PATH_BACK . "tpl/");

		define('PATH_BACK_DAT_CONNECT',   PATH_BACK_DAT . "db/connect.cgi");
		define('PATH_BACK_DAT_LANG',   PATH_BACK_DAT . "lang/");
		define('PATH_BACK_DAT_FILE',   PATH_BACK_DAT . "file/");
		define('PATH_BACK_DAT_TEMP',   PATH_BACK_DAT . "temp/");
		define('PATH_BACK_DAT_VERSION',   PATH_BACK_DAT . "version/");


		define('PATH_BACK_TPL_VARS',   PATH_BACK_TPL . "vars/");
		define('PATH_BACK_TPL_TEMPLATES',   PATH_BACK_TPL . "templates/");

		define('PATH_BACK_CLASS_ELSE', PATH_BACK_CLASS . "else/");

		define('PATH_BACK_CLASS_ELSE_CONFIG', PATH_BACK_CLASS_ELSE . "config/");
		define('PATH_BACK_CLASS_ELSE_CORE',   PATH_BACK_CLASS_ELSE . "core/");
		define('PATH_BACK_CLASS_ELSE_PLUGIN', PATH_BACK_CLASS_ELSE . "plugin/");
		define('PATH_BACK_CLASS_ELSE_LIB',    PATH_BACK_CLASS_ELSE . "lib/");

		define('PATH_BACK_CLASS_ELSE_CORE_BASE',    PATH_BACK_CLASS_ELSE_CORE . "base/");
		define('PATH_BACK_CLASS_ELSE_CORE_LOGIN',   PATH_BACK_CLASS_ELSE_CORE . "login/");
		define('PATH_BACK_CLASS_ELSE_CORE_CONFIRM',   PATH_BACK_CLASS_ELSE_CORE . "confirm/");
		define('FLAG_APC', $this->_self['flagAPC']);
		define('FLAG_API', $this->_self['flagAPI']);
		define('FLAG_CHECK_UPDATE', $this->_self['flagCheckUpdate']);
		define('NUM_VERSION', $this->_self['numVersion']);
		define('FLAG_OBFUSCATE', $this->_self['flagObfuscate']);
		define('FLAG_TEST', $this->_self['flagTest']);
		define('STR_SYSTEM_LANG', $this->_self['strSystemLang']);
		define('NUM_SYSTEM_TIME_ZONE', $this->_self['numSystemTimeZone']);
		define('STR_ENCODING', $this->_self['strEncoding']);
		define('NUM_SESSION', $this->_self['numSession']);
		define('NUM_SESSION_LOGIN_SECOND', $this->_self['numSessionLoginSecond']);
		define('STR_SYSTEM_HOLIDAY', $this->_self['strSystemHoliday']);
		define('NUM_MAX_UPLOAD_SIZE', $this->_self['numMaxUploadSize']);
		define('PATH_INFO', $this->_self['pathInfo']);
		define('PATH_INFO_SSL', $this->_self['pathInfoSSL']);
		define('NUM_MAX_TEXT_SIZE', $this->_self['numMaxTextSize']);
	}

	 /**
	  *
	  */
	protected function _setClass()
	{
		global $classSmarty;
		$classSmarty = (FLAG_APC)? apc_fetch('classSmarty') : null;
		if (is_null($classSmarty)) {
			/*
			if ($this->_self['flagConfig']) {
				$path = PATH_BACK_TPL . 'templates_c/';
				//$stat = stat($path);
				//@chown($path, $stat['uid']);
				//@chgrp($path, $stat['gid']);
				@chmod($path, 0755);

				$path = PATH_BACK_TPL . 'cache/';
				@chmod($path, 0755);
			}
			*/

			require_once(PATH_BACK_CLASS_ELSE_LIB . 'Smarty/libs/Smarty.class.php');
			$classSmarty = new Smarty();
			$classSmarty->caching = 0;
			$classSmarty->compile_check  = true;
			$classSmarty->template_dir = PATH_BACK_TPL . 'templates/';
			$classSmarty->compile_dir  = PATH_BACK_TPL . 'templates_c/';
			$classSmarty->config_dir  = PATH_BACK_TPL . 'configs/';
			$classSmarty->cache_dir  = PATH_BACK_TPL . 'cache/';
			if (FLAG_APC) {
				apc_store('classSmarty', $classSmarty);
			}
		}

		require_once(PATH_BACK_CLASS_ELSE_LIB . "Rebuild.php");
		global $classRebuild;
		$classRebuild = new Code_Else_Lib_Rebuild();

		global $classCheck;
		$classCheck = (FLAG_APC)? apc_fetch('classCheck') : null;
		if (is_null($classCheck)) {
			require_once(PATH_BACK_CLASS_ELSE_LIB . "Check.php");
			$classCheck = new Code_Else_Lib_Check();
			if (FLAG_APC) {
				apc_store('classCheck', $classCheck);
			}
		}

		global $classTime;
		$classTime = (FLAG_APC)? apc_fetch('classTime') : null;
		if (is_null($classTime)) {
			require_once(PATH_BACK_CLASS_ELSE_LIB . "Time.php");
			$classTime = new Code_Else_Lib_Time();
			if (FLAG_APC) {
				apc_store('classTime', $classTime);
			}
		}

		global $classFile;
		$classFile = (FLAG_APC)? apc_fetch('classFile') : null;
		if (is_null($classFile)) {
			require_once(PATH_BACK_CLASS_ELSE_LIB . "File.php");
			$classFile = new Code_Else_Lib_File();
			if (FLAG_APC) {
				apc_store('classFile', $classFile);
			}
		}

		global $classEscape;
		$classEscape = (FLAG_APC)? apc_fetch('classEscape') : null;
		if (is_null($classEscape)) {
			require_once(PATH_BACK_CLASS_ELSE_LIB . "Escape.php");
			$classEscape = new Code_Else_Lib_Escape();
			if (FLAG_APC) {
				apc_store('classEscape', $classEscape);
			}
		}
		require_once(PATH_BACK_CLASS_ELSE_LIB . "Display.php");
		global $classDisplay;
		$classDisplay = new Code_Else_Lib_Display();

		require_once(PATH_BACK_CLASS_ELSE_LIB . "Media.php");
		global $classMedia;
		$classMedia = new Code_Else_Lib_Media();

		require_once(PATH_BACK_CLASS_ELSE_LIB . "Request.php");
		global $classRequest;
		$classRequest = new Code_Else_Lib_Request(array(
			'flagType' => $this->_self['flagRequest'],
		));

		require_once(PATH_BACK_CLASS_ELSE_LIB . "Db.php");
		global $classDb;
		$classDb = new Code_Else_Lib_Db();

		require_once(PATH_BACK_CLASS_ELSE_LIB . "Mail.php");
		global $classMail;
		$classMail = new Code_Else_Lib_Mail();

		require_once(PATH_BACK_CLASS_ELSE_LIB . "Crypte.php");
		global $classCrypte;
		$classCrypte = new Code_Else_Lib_Crypte();

		require_once(PATH_BACK_CLASS_ELSE_LIB . "Html.php");
		global $classHtml;
		$classHtml = new Code_Else_Lib_Html();
	}

	 /**
	  *
	  */
	protected function _setVars()
	{
		global $classMedia;
		global $varsMedia;
		$varsMedia = $classMedia->getDetail();

		global $classRequest;
		global $varsRequest;
		$varsRequest = $classRequest->load();

		if ($this->_self['flagConfig']) {
			return;
		}

		$this->_setVarsDb();
		$this->_setVarsPreferece();
		$this->_setVarsSession();
		if (FLAG_API) {
			$this->_setVarsAPI();
		}
		$this->_setVarsModule();
		$this->_setVarsTerm();
		$this->_setVarsAccount();
	}

	/**
	  *
	  */
	protected function _setVarsAPI()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsApiAccounts;

		$varsApiAccounts = (FLAG_APC)? apc_fetch('varsApiAccounts'): null;
		if (is_null($varsApiAccounts)) {
			$sql = 'select * from baseApiAccount;';
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			$array = array();
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$array[$row['ip']][$row['strSiteUrl']] = $row;
			}
			$varsApiAccounts = $array;
			if (FLAG_APC) {
				apc_store('varsApiAccounts', $varsApiAccounts);
			}
		}

	}

	 /**
	  *
	  */
	protected function _setVarsModule()
	{
		global $varsPreference;
		global $varsModule;

		$varsModule = (FLAG_APC)? apc_fetch('varsModule'): null;
		if (is_null($varsModule)) {
			$this->updateVarsAll(array(
				'vars'     => &$varsModule,
				'strVars'  => 'varsModule',
				'strTable' => 'baseModule',
			));
		}

		$array = $varsPreference['jsonModule'];
		$arrayNew = array();
		foreach ($array as $key => $value) {
			$arrayNew[] = $key;
		}
		$str = join(",", $arrayNew);
		$str = ',' . $str . ',';
		$varsModule['1']['arrCommaIdModuleAdmin'] = $str;
		$varsModule['1']['arrCommaIdModuleUser'] = $str;
	}


	 /**
	  *
	  */
	protected function _setVarsTerm()
	{
		global $varsTerm;

		$varsTerm = (FLAG_APC)? apc_fetch('varsTerm'): null;
		if (is_null($varsTerm)) {
			$this->updateVarsAll(array(
				'vars'     => &$varsTerm,
				'strVars'  => 'varsTerm',
				'strTable' => 'baseTerm',
			));
		}
	}

	 /**
	  *
	  */
	protected function _setVarsSession()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsSession;
		$varsSession = (FLAG_APC)? apc_fetch('varsSession'): null;
		if (is_null($varsSession)) {
			$stmt = $dbh->prepare('select * from baseSession;');
			$stmt->execute();
			$array = array();
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$jsonToken = $row['jsonToken'];
				$row['jsonToken'] = ($jsonToken)? json_decode($jsonToken, true) : array();
				$str = ($row['idCookie'])? $row['idCookie'] : $row['idMobile'];
				$array[$str] = $row;
			}
			$varsSession = $array;
			if (FLAG_APC) {
				apc_store('varsSession', $varsSession);
			}
		}

	}

	 /**
	  *
	  */
	protected function _setVarsAccount()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsAccounts;
		$varsAccounts = (FLAG_APC)? apc_fetch('varsAccounts'): null;

		if (is_null($varsAccounts)) {
			$stmt = $dbh->prepare('select * from baseAccount');
			$stmt->execute();
			$array = array();
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				foreach ($row as $key => $value) {
					if (preg_match("/^json/", $key)) {
						$row[$key] = (!is_null($value))? json_decode($value, true) : array();
					}
				}
				$array[$row['id']] = $row;
			}
			$varsAccounts = $array;
			if (FLAG_APC) {
				apc_store('varsAccounts', $varsAccounts);
			}
		}

		global $varsAccount;
		require_once(PATH_BACK_CLASS_ELSE_CORE_BASE . "Attest.php");
		$classAttest = new Code_Else_Core_Base_Attest();
		$classAttest->run();
		$this->_setVarsAccountLang();
	}

	 /**
	  *
	  */
	protected function _setVarsAccountLang()
	{
		global $varsAccount;
		global $varsAccounts;
		global $varsRequest;
		global $classEscape;
		global $classFile;

		if ($varsAccount) {
			if ($varsRequest['cookie']['lang']) {
				$path = $classEscape->loopReplace(array(
					'data' => $this->_self['path']['file']['datLang'],
					'arr'  => array(
						array(
							'before' => '<strLang>',
							'after' => STR_SYSTEM_LANG,
						),
					),
				));
				$array = $classFile->getCsvRows(array('path' => $path));
				$flag = 0;
				foreach ($array as $key => $value) {
					if ($array[$key]['code'] == $varsRequest['cookie']['lang']) {
						$flag = 1;
					}
				}
				if (!$flag) {
					define('STR_LANG', STR_SYSTEM_LANG);
					return;
				}
				if ($varsRequest['cookie']['lang'] != $varsAccount['strLang']) {
					try {
						$dbh->beginTransaction();
						$stmt = $dbh->prepare('update baseAccount set strLang = ? where id = ?;');
						$stmt->execute(array($varsRequest['cookie']['lang'], $varsAccount['id']));

						$dbh->commit();
					} catch (PDOException $e) {
						$dbh->rollBack();
						exit;
					}
					$varsAccounts[$varsAccount['id']]['strLang'] = $varsRequest['cookie']['lang'];
					if (FLAG_APC) {
						apc_store('varsAccounts', $varsAccounts);
					}
					define('STR_LANG', $varsRequest['cookie']['lang']);
				} else {
					define('STR_LANG', $varsAccount['strLang']);
				}
			} else {
				define('STR_LANG', $varsAccount['strLang']);
			}
		} else {
			if ($varsRequest['cookie']['lang']) {
				define('STR_LANG', $varsAccount['strLang']);
			} else {
				define('STR_LANG', STR_SYSTEM_LANG);
			}
		}
	}

	 /**
	  *
	  */
	protected function _setVarsDb()
	{

		global $classDb;
		global $classFile;
		global $varsRequest;

		$this->_checkBatch13000();
		$array = $classFile->getCsvRows(array('path' => PATH_BACK_DAT_CONNECT));
		$numAll = count($array);
		$flag = null;
		$flagElse = null;
		for ($j = 0; $j < $numAll; $j++) {
			if ($array[$j]['dbtype'] == $varsRequest['query']['db']) {
				$flag = $j;
				break;
			}
			if ($array[$j]['dbtype'] == 'slave') {
				$flagElse = $j;
			}
		}
		$num = (!is_null($flag))? $flag: $flagElse;

		$classDb->setHandle(array(
			'driver'   => $array[$num]['driver'],
			'username' => $array[$num]['username'],
			'password' => $array[$num]['password'],
			'host'     => $array[$num]['host'],
			'dbname'   => $array[$num]['dbname'],
			'dbtype'   => $array[$num]['dbtype'],
		));

	}

	/**
	 */
	private function _checkBatch13000()
	{
		//for batch version < 1.30.00
		$pathUnder13000 = PATH_BACK_DAT . "db/connect.csv";
    	if (file_exists($pathUnder13000)) {
    		copy( PATH_BACK_DAT . "db/connect.csv", PATH_BACK_DAT . "db/connect.cgi" );
    		unlink(PATH_BACK_DAT . "db/connect.csv");
		}
	}

	 /**
	  *
	  */
	protected function _setVarsPreferece()
	{
		global $varsPreference;
		global $classDb;
		$dbh = $classDb->getHandle();

		$varsPreference = (FLAG_APC)? apc_fetch('varsPreference'): null;
		if (is_null($varsPreference)) {
			$sql = 'select * from basePreference;';
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				foreach ($row as $key => $value) {
					if (preg_match("/^json/", $key)) {
						$row[$key] = ($value)? json_decode($value, true) : array();
					}
				}
				$varsPreference = $row;
				break;
			}
			if (FLAG_APC) {
				apc_store('varsPreference', $varsPreference);
			}
		}
	}

	 /**
	  *
	  */
	protected function _setSystem()
	{

		mb_regex_encoding(STR_ENCODING);
		mb_language("uni");
		mb_internal_encoding("utf-8");
		//mb_http_input("auto");
		//mb_http_output("utf-8");
		date_default_timezone_set('UTC');
		ini_set('memory_limit', '128M');
		error_reporting(E_ALL ^ E_NOTICE);
		ini_set('display_errors', 1);
	}


	 /**
	  *
	  */
	public function updateVarsPreference()
	{
		global $varsPreference;
		global $classDb;
		$dbh = $classDb->getHandle();

		$stmt = $dbh->prepare('select * from basePreference;');
		$stmt->execute();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			foreach ($row as $key => $value) {
				if (preg_match("/^json/", $key)) {
					$row[$key] = (!is_null($value))? json_decode($value, true) : array();
				}
			}
			$varsPreference = $row;
			break;
		}
		if (FLAG_APC) {
			apc_store('varsPreference', $varsPreference);
		}

	}

	 /**
	  * array(
	  * 	'vars'     => &array,
	  * 	'strVars'  => string,
	  * 	'strTable' => string,
	  * 	'id'       => string
	  * )
	  */
	public function updateVars($arr)
	{

		global $classDb;
		$dbh = $classDb->getHandle();

		$strSql = 'select * from ' . $arr['strTable'] . ' where id = ?;';
		$stmt = $dbh->prepare($strSql);
		$stmt->execute(array($arr['id']));
		$data;
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			foreach ($row as $key => $value) {
				if (preg_match("/^json/", $key)) {
					$row[$key] = (!is_null($value))? json_decode($value, true) : array();
				}
			}
			$data = $row;
		}
		$arr['vars'][$arr['id']] = $data;
		if (FLAG_APC) {
			apc_store($arr['strVars'], $arr['vars'][$arr['id']]);
		}

	}

	 /**
	  * array(
	  * 	'vars'     => &array,
	  * 	'strVars'  => string,
	  * 	'strTable' => string,
	  * )
	  */
	public function updateVar($arr)
	{

		global $classDb;
		$dbh = $classDb->getHandle();

		$strSql = 'select * from ' . $arr['strTable'];
		$stmt = $dbh->prepare($strSql);
		$stmt->execute();
		$data;
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			foreach ($row as $key => $value) {
				if (preg_match("/^json/", $key)) {
					$row[$key] = (!is_null($value))? json_decode($value, true) : array();
				}
			}
			$data = $row;
		}
		$arr['vars']= $data;
		if (FLAG_APC) {
			apc_store($arr['strVars'], $arr['vars']);
		}

	}

	 /**
	  * array(
	  * 	'vars'     => &array,
	  * 	'strVars'  => string,
	  * 	'strTable' => string,
	  * 	'strColumn' => string,
	  * )
	  */
	public function updateVarsAll($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$strSql = 'select * from ' . $arr['strTable'] . ';';
		$stmt = $dbh->prepare($strSql);
		$stmt->execute();
		$array = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			foreach ($row as $key => $value) {
				if (preg_match("/^json/", $key)) {
					$row[$key] = (!is_null($value))? json_decode($value, true) : array();
				}
			}
			$str = ($arr['strColumn'])? $row[($arr['strColumn'])] : $row['id'];
			$array[$str] = $row;
		}
		$arr['vars'] = $array;

		if (FLAG_APC) {
			apc_store($arr['strVars'], $arr['vars']);
		}
	}

	 /**
	  *
	  */
	public function updateVarsAccount()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsAccounts;
		global $varsAccount;

		$stmt = $dbh->prepare('select * from baseAccount where id = ?');
		$stmt->execute(array($varsAccount['id']));
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			foreach ($row as $key => $value) {
				if (preg_match("/^json/", $key)) {
					$row[$key] = (!is_null($value))? json_decode($value, true) : array();
				}
			}
			$varsAccount = $row;
		}
		$varsAccounts[$varsAccount['id']] = $varsAccount;
		if (FLAG_APC) {
			apc_store('varsAccounts', $varsAccounts);
		}

	}
}
