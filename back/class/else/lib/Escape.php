<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Lib_Escape
{

	public $self = array(
		'pathLoad' => 'back/class/else/lib/Escape/<strLang>.php',
		'strLang' => 'ja',
	);

	function __construct($arr = null)
	{
		// $arr = @func_get_arg(0); = $this->self['strLang'];
		$strLang = $this->self['strLang'];
		$this->setVarsEscape(array('strLang' => $strLang));
		// $arr = @func_get_arg(0);
		if (!$arr) {
			return;
		}
		foreach ($arr as $key => $value) {
			if (empty($this->self[$key])) {
				$this->self[$key] = $value;
			}
		}
		if ($strLang != $this->self['strLang']) {
			$this->setVarsEscape(array('strLang' => $this->self['strLang']));
		}
	}

	/**
	 * $arr = array(
	 *     strLang  => str,
	 * )
	 */
	public function setVarsEscape($arr)
	{
		$path = str_replace('<strLang>', $arr['strLang'], $this->self['pathLoad']);
		if (!file_exists($path)) {
			return;
		}
		require($path);
		$array = $vars;
		foreach ($array as $key => $value) {
			if (empty($this->self[$key])) {
				$this->self[$key] = $value;
			}
		}
	}

	/**
	 * $arr = array(
	 *     data => mixed,
	 * )
	 */
	public function to($arr)
	{
		return $this->loopReplace(array('data' => $arr['data'], 'arr' => $this->self['to']));
	}

	/**
	 * $arr = array(
	 *     data => mixed,
	 * )
	 */
	public function toFrom($arr)
	{
		return $this->loopReplace(array('data' => $arr['data'], 'arr' => $this->self['toFrom']));
	}

	/**
	 * $arr = array(
	 *     data => mixed,
	 * )
	 */
	public function toBr($arr)
	{
		return $this->loopReplace(array('data' => $arr['data'], 'arr' => $this->self['br']));
	}

	/**
	 * $arr = array(
	 *     data => mixed,
	 * )
	 */
	public function toComma($arr)
	{
		return $this->loopReplace(array('data' => $arr['data'], 'arr' => $this->self['comma']));
	}

	/**
	 * $arr = array(
	 *     data => mixed,
	 * )
	 */
	public function obfuscate($arr)
	{
		$array = $this->self['obfuscate'];
		$numAll = count($array);
		for ($j = 0; $j < $numAll; $j++) {
			$arr['data'] = mb_ereg_replace($array[$j]['before'], $array[$j]['after'], $arr['data']);
		}

		return $arr['data'];
	}

	/**
	 * $arr = array(
	 *     data => mixed,
	 * )
	 */
	public function strUnique($arr)
	{
		return $this->loopReplace(array('data' => $arr['data'], 'arr' => $this->self['strUnique']));
	}

	/**
	 * $arr = array(
	 *     data => mixed,
	 *     arr => array,
	 * )
	 */
	public function loopReplace($arr)
	{
		$array = $arr['arr'];
		$numAll = count($array);
		for ($j = 0; $j < $numAll; $j++) {
			$arr['data'] = str_replace($array[$j]['before'], $array[$j]['after'], $arr['data']);
		}

		return $arr['data'];
	}

	/**
	 * $arr = array(
	 *     'data' => string,
	 *     'arr' => array(array(
	 *          'before' => string,
	 *          'after'  => string,
	 *     ),),
	 * )
	 */
	public function getVars($arr)
	{
		$path = $this->loopReplace(array('data' => $arr['data'], 'arr' => $arr['arr']));
		if (file_exists($path)) {
			require $path;
			return $vars;
		}
	}

	/**
		$classEscape->getFileType(array('strUrl' => ''));
	 */
	public function getFileType($arr)
	{
		$array = preg_split("/\//", $arr['strUrl']);
		$data = $array[count($array) - 1];
		$array = preg_split("/\./", $data);
		$data = $array[count($array) - 1];
		$data = strtolower($data);

		return $data;
	}

	/**
		$classEscape->splitCommaArray(array(
			'data'      => '',
			'flagSort' => 0,
			'flagKsort' => 0,
		));
	 */
	public function splitCommaArray($arr)
	{
		if (is_null($arr['data'])) {
			return array();
		}

		$arr['data'] = preg_split("/,/", $arr['data']);
		$arr['data'] = array_filter($arr['data'], "strlen");
		$arr['data'] = array_merge($arr['data']);
		$arr['data'] = array_unique($arr['data']);
		if ($arr['flagSort']) {
			if ($arr['flagKsort']) {
				ksort($arr['data']);

			} else {
				arsort($arr['data']);
			}
		}

		return $arr['data'];
	}
	/**
		$classEscape->splitCommaArrayData(array(
			'data'      => '',
		));
	 */
	public function splitCommaArrayData($arr)
	{
		if (is_null($arr['data'])) {
			return array();
		}

		$arr['data'] = preg_split("/,/", $arr['data']);
		$arr['data'] = array_filter($arr['data'], "strlen");
		$arr['data'] = array_merge($arr['data']);
		$arr['data'] = array_unique($arr['data']);

		return $arr['data'];
	}

	/**
		$classEscape->splitCommaArray(array(
			'data'      => '',
		));
	 */
	public function splitCommaHash($arr)
	{
		$arr['data'] = $this->splitCommaArray($arr);

		$array = $arr['data'];
		$arrayNew = array();
		$num = 0;
		foreach ($array as $key => $value) {
			$arrayNew[$value] = 1;
			$num++;
		}

		return $arrayNew;
	}

	/**
		(array(
			'data'      => '',
		));
	 */
	public function joinCommaStr($arr)
	{
		$array = $arr['data'];
		$arrayNew = array();
		$num = 0;
		foreach ($array as $key => $value) {
			if ((int) $value) {
				$arrayNew[$num] = $key;
				$num++;
			}
		}

		$strComma = $this->joinCommaArray(array(
			'arr' => $arrayNew,
		));

		return $strComma;
	}

	/**
		$classEscape->removeCommaArray(array(
			'data'      => '',
			'idTarget'  => '',
			'flagKsort' => 0,
		));
	 */
	public function removeCommaArray($arr)
	{
		$array = $this->splitCommaArray($arr);
		$arrayNew = array();
		$num = 0;
		foreach ($array as $key => $value) {
			if ($arr['idTarget'] != $value) {
				$arrayNew[$num] = $value;
				$num++;
			}
		}
		$arrData = $this->joinCommaArray(array(
			'arr' => $arrayNew
		));

		return $arrData;
	}

	/**
		$classEscape->splitSpaceArray(array(
			'data'      => '',
			'flagKsort' => 0,
		));
	 */
	public function splitSpaceArray($arr)
	{
		if (is_null($arr['data'])) {
			return array();
		}

		$arr['data'] = preg_replace("/　+/", ' ', $arr['data']);
		$arr['data'] = preg_split("/ /", $arr['data']);

		$arr['data'] = array_filter($arr['data'], "strlen");
		$arr['data'] = array_merge($arr['data']);
		$arr['data'] = array_unique($arr['data']);
		if ($arr['flagKsort']) {
			ksort($arr['data']);
		} else {
			arsort($arr['data']);
		}

		return $arr['data'];
	}

	/**
		$classEscape->splitJoinStr(array(
			'data'       => '',
			'delimiter'  => '',
			'flagUnique' => 1,
			'flagArray'  => 1,
			'flagRep'    => '',//strKanji, strHiragana, strKana
		));
	 */
	public function splitJoinStr($arr)
	{
		$str = $arr['data'];

		$strRep = $this->self['strSplit'];
		if ($arr['flagRep']) {
			$strRep = $this->self[$arr['flagRep']];
		}

		preg_match_all("/$strRep+/u", $str, $arrMatches);
		$arrMatch = $arrMatches[0];

		if ($arr['flagUnique']) {
			$arrMatch = array_filter($arrMatch, "strlen");
			$arrMatch = array_merge($arrMatch);
			$arrMatch = array_unique($arrMatch);
		}
		if ($arr['flagArray']) {
			return $arrMatch;
		}

		if (!$arrMatch) {
			return '';
		}

		$delimiter = '';
		if (!is_null($arr['delimiter'])) {
			$delimiter = $arr['delimiter'];
		}

		$str = join($delimiter, $arrMatch);

		return $str;
	}

	/**
		$classEscape->splitSpaceArrayData(array(
			'data'      => '',
		));
	 */
	public function splitSpaceArrayData($arr)
	{
		if (is_null($arr['data'])) {
			return array();
		}

		$arr['data'] = preg_replace("/　+/", ' ', $arr['data']);
		$arr['data'] = preg_split("/ /", $arr['data']);

		$arr['data'] = array_filter($arr['data'], "strlen");
		$arr['data'] = array_merge($arr['data']);
		$arr['data'] = array_unique($arr['data']);

		return $arr['data'];
	}

	/**
		$classEscape->addCommaArray(array(
			'flagUnique' => 1,
			'strComma'   => '',
			'arrTarget'  => array(),
		));
	 */
	public function addCommaArray($arr)
	{
		$arrComma = $this->splitCommaArray(array(
			'data' => $arr['strComma'],
			'flagKsort' => 0,
		));

		$arrComma = array_filter($arrComma, "strlen");
		$array = $arr['arrTarget'];
		foreach ($array as $key => $value) {
			$arrComma[] = $value;
		}


		if ($arr['flagUnique']) {
			$arrComma = array_merge($arrComma);
			$arrComma = array_unique($arrComma);
		}

		$str = ',' . join(',', $arrComma) . ',';

		return $str;
	}

	/**
		$classEscape->joinCommaArray(array(
			'arr'  => array(),
		));
	 */
	public function joinCommaArray($arr)
	{
		if (!$arr['arr']) {
			return '';
		}
		$str = ',' . join(',', $arr['arr']) . ',';

		return $str;
	}

	/**
	 * $arr = array(
	 *     'arr' => array,
	 * )
	 */
	public function joinSpaceArray($arr)
	{
		if (!$arr['arr']) {
			return '';
		}

		$str = ' ' . join(' ', $arr['arr']) . ' ';

		return $str;
	}

	/**
	 * $arr = array(
	 *     'str' => string,
	 * )
	 */
	public function toLower($arr)
	{
		$str = $arr['str'];
		$strTop = substr($str, 0, 1);
		$strTop = strtolower($strTop);
		$strBottom = substr($str, 1, strlen($str));
		$str = $strTop . $strBottom;

		return $str;
	}

	/**
	 * $arr = array(
	 *     'data' => string,
	 * )
	 */
	public function toInt($arr)
	{
		$str = $arr['data'];
		if ($arr['data'] > PHP_INT_MAX) {
			return $arr['data'];
		}
		return (int) $arr['data'];
	}


	/*

	$classEscape->convertSJIS($str)

	 * */
	public function convertSJIS($str)
	{
		return (mb_convert_encoding($str, "SJIS", STR_ENCODING));
	}


}
