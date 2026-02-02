<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Config
{

	protected $_self = array(
		'strTitle' => '',
		'path' => array(
			'file' => array(
				'sqlConnect' => 'back/dat/db/connect.cgi',
				'tplHtml' => 'else/config/html/index.html',
				'tplJs' => 'else/config/js/index.js',
				'varHtml' => 'back/tpl/vars/else/config/<strLang>/html/index.php',
				'varJs' => 'back/tpl/vars/else/config/<strLang>/js/index.php',
			),
			'dir' => array(
				'plugin' => '',
			),
		),
		'classList' => array(
			'Smarty',
			'DOMDocument',
			'ZipArchive',
			'DateTime',
			'PDO',
		),
		'funcList' => array(
			'json_encode',
			'abs',
			'array_filter',
			'array_merge',
			'array_unique',
			'arsort',
			'base64_encode',
			'chunk_split',
			'closedir',
			'copy',
			'curl_init',
			'date_default_timezone_set',
			'end',
			'error_reporting',
			'fclose',
			'file_exists',
			'file_get_contents',
			'flock',
			'fopen',
			'fputs',
			'fwrite',
			'gethostbyaddr',
			'hash',
			'header',
			'ignore_user_abort',
			'imap_base64',
			'imap_fetchstructure',
			'imap_header',
			'imap_mailboxmsginfo',
			'imap_mime_header_decode',
			'imap_open',
			'imap_search',
			'imap_setflag_full',
			'implode',
			'ini_set',
			'join',
			'json_decode',
			'key',
			'ksort',
			//'list',
			'mail',
			'mb_convert_encoding',
			'mb_convert_variables',
			'mb_detect_order',
			'mb_encode_mimeheader',
			'mb_http_input',
			'mb_http_output',
			'mb_internal_encoding',
			'mb_language',
			'mb_regex_encoding',
			'mb_substr',
			'mcrypt_module_open',
			'mcrypt_enc_get_key_size',
			'mcrypt_enc_get_iv_size',
			'mcrypt_generic_deinit',
			'mcrypt_module_close',
			'mcrypt_generic_init',
			'md5',
			'mdecrypt_generic',
			'method_exists',
			'mkdir',
			'mt_rand',
			'number_format',
			'ob_end_clean',
			'ob_get_contents',
			'ob_start',
			'preg_match',
			'preg_quote',
			'preg_split',
			'rmdir',
			'rtrim',
			'scandir',
			'set_time_limit',
			'shuffle',
			'stat',
			'str_replace',
			'strlen',
			'strtolower',
			'substr',
			'ucwords',
			'unlink',
			//'unset',
			'version_compare',
		),
		'vars' => array(
			'flagDriver' => 'mysql'
		),


	);

	function __construct($arr = null)
	{
		// $arr = @func_get_arg(0);
		if (!$arr) {
			return;
		}
		foreach ($arr as $key => $value) {
			if (empty($this->_self[$key])) {
				$this->_self[$key] = $value;
			}
		}
	}

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;

		if ($varsRequest['query']['func'] == 'checkDatabase') {
			$this->_checkPhp();
			$this->_checkClass();
			$this->_checkDb();
			if (!$varsRequest['query']['flagIgnore']) {
				$this->_checkFunc();
			}
			$this->_setFile();
			$this->_setRebuild();
			$this->_setHtaccess();
			$this->_sendSuccess();

		} else {
			$this->_showHtml();

		}
		exit;
	}

	/**
	 *
	 */
	protected function _setHtaccess()
	{
		$path = PATH_TOP . '/back/dat/htaccess/foreign.cgi';
		copy($path, PATH_TOP . '/.htaccess');
	}

	/**
	 *
	 */
	protected function _setRebuild()
	{
		global $classRebuild;
		global $classDb;
		$dbh = $classDb->getHandle();
		global $classRequest;

		$flag = $classRebuild->run(array(
			'flagType' => 'DbTable',
		));

		if ($flag) {
			$vars = array(
				'flag' => 'rebuild',
				'data' => '',
			);
			$json = json_encode($vars);
			$classRequest->send(array(
				'flagType' => 'json',
				'data' => $json,
			));
			exit;
		}

		try {
			$dbh->beginTransaction();

			$classRebuild->run(array(
				'flagType' => 'DbInsert',
			));

			$dbh->commit();

		} catch (PDOException $e) {
			$dbh->rollBack();
			$flag = 1;
			if (FLAG_TEST) {
				var_dump($e->getMessage());
				exit;
			}
		}

		if ($flag) {
			$vars = array(
				'flag' => 'rebuild',
				'data' => '',
			);
			$json = json_encode($vars);
			$classRequest->send(array(
				'flagType' => 'json',
				'data' => $json,
			));
			exit;
		}

		$array = array('Css', 'Js');
		foreach ($array as $key => $value) {
			$classRebuild->run(array(
				'flagType' => $value,
			));
		}

	}

	/**
	 *
	 */
	protected function _checkPhp()
	{
		global $classRequest;

		$var = explode('.', PHP_VERSION);
		$version = (int) $var[0];
		if ($version < 5) {
			$vars = array(
				'flag' => 'php',
			);
			$json = json_encode($vars);
			$classRequest->send(array(
				'flagType' => 'json',
				'data' => $json,
			));
			exit;
		}
	}

	/**
	 *
	 */
	protected function _checkFunc()
	{
		global $classRequest;
		$array = $this->_self['funcList'];
		$arrayNew = array();
		$num = 0;
		foreach ($array as $key => $value) {
			if (!function_exists($value)) {
				$arrayNew[$num] = $value;
				$num++;
			}
		}

		if (count($arrayNew)) {
			$vars = array(
				'flag' => 'func',
				'data' => $arrayNew,
			);
			$json = json_encode($vars);
			$classRequest->send(array(
				'flagType' => 'json',
				'data' => $json,
			));
			exit;
		}
	}

	/**
	 *
	 */
	protected function _checkClass()
	{
		global $classRequest;
		$array = $this->_self['classList'];
		$arrayNew = array();
		$num = 0;
		foreach ($array as $key => $value) {
			if (!class_exists($value)) {
				$arrayNew[$num] = $value;
				$num++;
			}
		}

		if (count($arrayNew)) {
			$vars = array(
				'flag' => 'class',
				'data' => $arrayNew,
			);
			$json = json_encode($vars);
			$classRequest->send(array(
				'flagType' => 'json',
				'data' => $json,
			));
			exit;
		}
	}

	/**
	 *
	 */
	protected function _checkDb()
	{
		global $varsRequest;
		global $classRequest;
		global $classDb;

		$classDb->setVar(array(
			'driver' => $this->_self['vars']['flagDriver'],
			'username' => $varsRequest['query']['StrDbUser'],
			'password' => $varsRequest['query']['StrDbPassword'],
			'host' => $varsRequest['query']['StrDbHost'],
			'dbname' => $varsRequest['query']['StrDbName'],
		));
		if (!$classDb->checkConnect()) {
			$vars = array(
				'flag' => 'db',
			);
			$json = json_encode($vars);
			$classRequest->send(array(
				'flagType' => 'json',
				'data' => $json,
			));
			exit;
		}
		$classDb->setHandle(array(
			'driver' => $this->_self['vars']['flagDriver'],
			'username' => $varsRequest['query']['StrDbUser'],
			'password' => $varsRequest['query']['StrDbPassword'],
			'host' => $varsRequest['query']['StrDbHost'],
			'dbname' => $varsRequest['query']['StrDbName'],
		));
	}

	/**
	 *
	 */
	protected function _setFile()
	{
		global $varsRequest;

		$str = '"dbtype",'
			. '"dbname",'
			. '"username",'
			. '"password",'
			. '"host",'
			. '"driver"'
			. "\n";
		$str .= '"master",'
			. '"' . $varsRequest['query']['StrDbName'] . '",'
			. '"' . $varsRequest['query']['StrDbUser'] . '",'
			. '"' . $varsRequest['query']['StrDbPassword'] . '",'
			. '"' . $varsRequest['query']['StrDbHost'] . '",'
			. '"' . $this->_self['vars']['flagDriver'] . '"'
			. "\n";
		$str .= '"slave",'
			. '"' . $varsRequest['query']['StrDbName'] . '",'
			. '"' . $varsRequest['query']['StrDbUser'] . '",'
			. '"' . $varsRequest['query']['StrDbPassword'] . '",'
			. '"' . $varsRequest['query']['StrDbHost'] . '",'
			. '"' . $this->_self['vars']['flagDriver'] . '"'
			. "\n";
		$str .= '"log",'
			. '"' . $varsRequest['query']['StrDbName'] . '",'
			. '"' . $varsRequest['query']['StrDbUser'] . '",'
			. '"' . $varsRequest['query']['StrDbPassword'] . '",'
			. '"' . $varsRequest['query']['StrDbHost'] . '",'
			. '"' . $this->_self['vars']['flagDriver'] . '"'
			. "\n";
		$classFile = new Code_Else_Lib_File();
		$classFile->setData(array(
			'path' => $this->_self['path']['file']['sqlConnect'],
			'data' => $str,
		));
	}

	/**
	 *
	 */
	protected function _sendSuccess()
	{
		global $classRequest;
		$vars = array(
			'flag' => 'end',
		);
		$json = json_encode($vars);

		if (!FLAG_TEST) {
			if (file_exists(PATH_CONFIG_FILE)) {
				unlink(PATH_CONFIG_FILE);
			}
			$this->_deleteUpdateFile();
		}

		$classRequest->send(array(
			'flagType' => 'json',
			'data' => $json,
		));
	}

	/**
	 *
	 */
	protected function _deleteUpdateFile()
	{
		$array = scandir(PATH_BACK_DAT_TEMP);
		foreach ($array as $key => $value) {
			if (preg_match("/^\.{1,2}$/", $value)) {
				continue;
			}
			if ($value == 'flagUpdate.cgi') {
				$path = PATH_BACK_DAT_TEMP . $value;
				unlink($path);
				return;
			}
		}
	}

	/**
	 *
	 */
	protected function _getJs()
	{
		global $classSmarty;
		global $classEscape;

		$vars = $classEscape->getVars(array(
			'data' => $this->_self['path']['file']['varJs'],
			'arr' => array(
				array('before' => '<strLang>', 'after' => STR_SYSTEM_LANG, ),
			),
		));
		$json = json_encode($vars);
		$classSmarty->assign('varsLoad', $json);
		$path = $this->_self['path']['file']['tplJs'];

		$output = $classSmarty->fetch($path);
		$output = $classEscape->obfuscate(array('data' => $output));

		return $output;
	}

	/**
	 *
	 */
	protected function _showHtml()
	{
		global $classSmarty;
		global $classRequest;
		global $classEscape;

		$array = $classEscape->getVars(array(
			'data' => $this->_self['path']['file']['varHtml'],
			'arr' => array(
				array('before' => '<strLang>', 'after' => STR_SYSTEM_LANG, ),
			),
		));
		$array['loadJs'] = $this->_getJs();
		foreach ($array as $key => $value) {
			$classSmarty->assign($key, $value);
		}
		$path = $classEscape->loopReplace(array(
			'data' => $this->_self['path']['file']['tplHtml'],
			'arr' => array(
				array('before' => '<strTitle>', 'after' => $this->_self['strTitle'], ),
			),
		));

		$output = $classSmarty->fetch($path);

		$classRequest->send(array(
			'flagType' => 'html',
			'data' => $output,
		));
		exit;
	}
}
