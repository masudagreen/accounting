<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
require_once(PATH_BACK_CLASS_ELSE_LIB . "/Escape.php");
require_once(PATH_BACK_CLASS_ELSE_LIB . "/File.php");

 /**
  *
  */
class Code_Else_Lib_Request
{
	protected $_self = array(
		'flagEscape' => 1,
		'flagType'	=> 'or',
		'path'	   => array(
			'content' => 'back/dat/content/list.csv',
		),
	);

	function __construct()
	{
		$arr = @func_get_arg(0);
		if (!$arr) {
			return;
		}
		foreach ($arr as $key => $value) {
			if (!empty($this->_self[$key])) {
				$this->_self[$key] = $value;

			}
		}
	}

	/**
	 *
	 */
	public function load($arr = array())
	{
		global $classEscape;

//		if (get_magic_quotes_gpc()) {
//			function strip_magic_quotes_slashes($arr)
//			{
//				return is_array($arr) ? array_map('strip_magic_quotes_slashes', $arr) : stripslashes($arr);
//			}
//			$_GET = strip_magic_quotes_slashes($_GET);
//			$_POST = strip_magic_quotes_slashes($_POST);
//		}

		$arr['query'] = array();
		if ($this->_self['flagType'] == 'or') {
			if ($_POST) {
				$arr['query'] = $this->getPost();

			} elseif($_GET) {
				$arr['query'] = $this->getGet();
			}

		} elseif ($this->_self['flagType'] == 'post') {
			if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_SERVER['QUERY_STRING']) {
				exit;
			}
			$arr['query'] = $this->getPost();

		} elseif ($this->_self['flagType'] == 'get') {
			if (($_SERVER['REQUEST_METHOD'] != 'GET' && $_SERVER['QUERY_STRING'])
				|| (!$_POST && !$_GET)
			) {
				exit;
			}
			$arr['query'] = $this->getGet();

		} elseif ($this->_self['flagType'] == 'json') {
			$json = file_get_contents('php://input');
			if (is_null($json)) {
				exit;
			}
			$json = $classEscape->to(array( 'data' => $json));
			$arr['query']['api'] = ($json)? json_decode($json, true) : array();
		}

		$arr['cookie'] = $this->getCookie();
		$arr['referer'] = $this->getReferer();
		$arr['flagGetPermit'] = 0;

		return $arr;
	}

	/**
	 *
	 */
	public function getCookie($arr = array())
	{
		global $classEscape;
		foreach ($_COOKIE as $key => $value) {
			if ($this->_self['flagEscape']) {
				$arr[$key] = $classEscape->to(array( 'data' => $value ));

			} else {
				$arr[$key] = $value;

			}
		}

		return $arr;
	}

    /**
     *
     */
    public function getReferer()
    {
    	if (!isset($_SERVER['HTTP_REFERER'])) {
    		return '';
    	}

        return $_SERVER['HTTP_REFERER'];
    }

	/**
	 *
	 */
	public function getPost($arr = array())
	{
		global $classEscape;

		foreach ($_POST as $key => $value) {
			if ($key == 'cache') {
				continue;
			}

			if ($this->_self['flagEscape']) {
				$arr[$key] = $classEscape->to(array( 'data' => $value ));

			} else {
				$arr[$key] = $value;

			}
		}

		foreach ($arr as $key => $value) {
			if (preg_match("/^json/i", $key)) {
				$arr[$key] = ($value)? json_decode($value, true) : '';

			}
		}

		return $arr;
	}

	/**
	 *
	 */
	public function getGet($arr = array())
	{
		global $classEscape;
		foreach ($_GET as $key => $value) {
			if ($this->_self['flagEscape']) {
				$arr[$key] = $classEscape->to(array( 'data' => $value ));

			} else {
				$arr[$key] = $value;

			}
		}

		return $arr;
	}

	/**
	 * $arr = array(
	 *	 data      => mixed,
	 *	 flagType  => mixed,
	 * )
	 */
	public function send($arr, $flagType = '')
	{
		global $varsRequest;

		if ($_SERVER['REQUEST_METHOD'] == 'GET'
			&& $_SERVER['QUERY_STRING']
			&& !$varsRequest['flagGetPermit']
		) {
			exit;
		}

		if ($arr['flagType'] == 'html') {
			$flagType = 'Content-Type: text/html; charset=UTF-8';

		} elseif($arr['flagType'] == 'javascript') {
			$flagType = 'Content-Type: text/javascript';

		} elseif($arr['flagType'] == 'json') {
			$flagType = 'Content-Type: application/json;  charset=UTF-8';

		} else {
			$classFile = new Code_Else_Lib_File();
			$arrType = $classFile->getCsvRow(array(
				'path'      => $this->_self['path']['content'],
				'strColumn' => 'file',
				'value'     => $arr['flagType'],
			));
			$flagType = 'Content-Type: ' . $arrType['content'];

		}

		header($flagType);
		header("Cache-Control:");
		header("Expires:");
		header('X-FRAME-OPTIONS: SAMEORIGIN');
		header('X-Content-Type-Options: nosniff');
		echo $arr['data'];
		exit;
	}

	/**
	 * $arr = array(
			'path'         => '',
			'strFileType'  => '',
	 * )
	 */
	public function outputImg($arr)
	{
		$strContentType = 'Content-Type: : image/' . $arr['strFileType'];
		header($strContentType);
		header('X-Content-Type-Options: nosniff');
		readfile($arr['path']);
		exit;
	}

	/**
	 * $arr = array(
			'path'         => '',
			'text'         => '',
			'strFileType'  => '',
			'strFileName'  => '',
	 * )
	 */
	public function output($arr)
	{
		global $varsRequest;

		if ($_SERVER['REQUEST_METHOD'] == 'GET'
			&& $_SERVER['QUERY_STRING']
			&& !$varsRequest['flagGetPermit']
		) {
			exit;
		}

		$classFile = new Code_Else_Lib_File();

		$arrType = $classFile->getCsvRow(array(
			'path'      => $this->_self['path']['content'],
			'strColumn' => 'file',
			'value'     => $arr['strFileType'],
		));
		$strContentType = 'Content-Type: ' . $arrType['content'];
		$strFileName = $arr['strFileName'];

		header($strContentType);
		header("Content-Disposition: attachment; filename=\"{$strFileName}\"");
		header('X-Content-Type-Options: nosniff');

		if ($arr['text']) {
			print $arr['text'];

		} else {
			readfile($arr['path']);
		}
		exit;
	}

	/**
	 * $arr = array(
			'strUrl'         => '',
	 * )
	 */
	public function curlGetContents($arr){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $arr['strUrl'] );
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);

		$result = curl_exec($ch);
		if($result === false) {
			return array(
				'flagError' => 1,
				'data'      => curl_error($ch),
			);
		}
		curl_close($ch);

		return array(
			'flagError' => 0,
			'data'      => $result
		);
	}
}
