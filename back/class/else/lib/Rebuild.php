<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Lib_Rebuild
{
	protected $_self = array(
		'path' => array(
			'dir' => array(
			),
			'file' => array(
				'outCss'     => 'front/else/core/base/css/style.css',
		    ),
		),
	);

	function __construct()
	{
		ignore_user_abort(true);
		set_time_limit(0);
	}

	/**

	 */
	public function run($arr)
	{
		$method = '_ini' . $arr['flagType'];
		if (method_exists($this, $method)) {
			return $this->$method($arr);
		}
	}

	/**
	 *
	 */
	protected function _iniCss($arr)
	{
		$arr['flagType'] = 'rebuild' . ucwords($arr['flagType']);
		$this->_setCore($arr);
		$this->_setPlugin($arr);
	}

	/**
	 *
	 */
	protected function _iniJs($arr)
	{
		$arr['flagType'] = 'rebuild' . ucwords($arr['flagType']);
		$this->_setCore($arr);
	}

	/**
	 *
	 */
	protected function _iniDbTable($arr)
	{
		$arr['flagType'] = 'rebuild' . ucwords($arr['flagType']);
		$flag = $this->_setCore($arr);
		if ($flag) {
			return $flag;
		}
		return $this->_setPlugin($arr);
	}
	/**
	 *
	 */
	protected function _iniDbInsert($arr)
	{
		$arr['flagType'] = 'rebuild' . ucwords($arr['flagType']);
		$this->_setCore($arr);
		$this->_setPlugin($arr);
	}

	/**
	 *
	 */
	protected function _setCore($arr)
	{
		$array = scandir(PATH_BACK_CLASS_ELSE_CORE);
		$arrayNew = array();
		foreach ($array as $key => $value) {
			if ( preg_match( "/^\.{1,2}$/", $value)) {
				continue;
			}
			if ($arr['arrIdModule']) {
				if (!$arr['arrIdModule'][$value]) {
					continue;
				}
			}
			$strDir = $value;
			$strFile = ucwords($value);
			$path = PATH_BACK_CLASS_ELSE_CORE . $strDir . '/' . $strFile . '.php';
			if (!file_exists($path)) {
				continue;
			}
			require_once($path);

			$strClass = 'Code_Else_Core_' . $strFile . '_' . $strFile;
			$classCall = new $strClass;

			$flag = $classCall->loop($arr);
			if ($flag) {
				return $flag;
			}

		}
	}

	/**
	 *
	 */
	protected function _setPlugin($arr)
	{
		$array = scandir(PATH_BACK_CLASS_ELSE_PLUGIN);
		$contents = '';
		foreach ($array as $key => $value) {
			if ( preg_match( "/^\.{1,2}$/", $value)) {
				continue;

			}
			if ($arr['arrIdModule']) {
				if (!$arr['arrIdModule'][$value]) {
					continue;
				}
			}
			$strDir = $value;
			$strFile = ucwords($value);
			$path = PATH_BACK_CLASS_ELSE_PLUGIN . $strDir . '/' . $strFile . '.php';
			if (!file_exists($path)) {
				continue;

			}
			require_once($path);
			$strClass = 'Code_Else_Plugin_' . $strFile . '_' . $strFile;
			$classCall = new $strClass;
			$flag = $classCall->loop($arr);
			if ($flag) {
				return $flag;
			}
		}
	}

	/**
	 * $arr => array(
	 *	 'pathInDir'  => string
	 *	 'pathOutFile' => string
	 * ),
	 */
	protected function _setAllCss($arr, $contents = '')
	{
		global $classFile;

		$array = scandir($arr['pathInDir']);
		foreach ($array as $key => $value) {
			$strFile = $value;
			$pathFile = $arr['pathInDir'] .  $strFile;
			if ( preg_match( "/^\.{1,2}$/", $strFile) || is_dir($pathFile)) {
				continue;
			}
			$contents .= file_get_contents($pathFile);
		}

		$classFile->setData(array(
			'path' => $arr['pathOutFile'],
			'data' => $contents,
		));
	}

    /**
     * $arr = array(
     *     'path'    => string,
     *     'strLang' => string,
     * )
     */
	protected function _getPath($arr)
	{
		global $classEscape;

		if (!$arr['strTitle']) {
			$arr['strTitle'] = '';
		}

		if (!$arr['strLang']) {
			$arr['strLang'] = '';
		}

		$path = $classEscape->loopReplace(array(
			'data' => $arr['path'],
			'arr'  => array(
				array('before' => '<strTitle>', 'after' => $arr['strTitle'],),
				array('before' => '<strLang>', 'after' => $arr['strLang'],),
			),
		));

		return $path;
	}



	/**
	 * $arr = array(
	 *	 'arrLang' => array(),
	 *	 'pathTpl' => string,
	 *	 'pathVars' => string,
	 *	 'pathOut' => string,
	 * )
	 */
	protected function _setJs($arr)
	{
		global $classSmarty;
		global $classEscape;
		global $classFile;

		$arrayLang = $arr['arrLang'];
		foreach ($arrayLang as $keyLang => $valueLang) {
			$vars = $this->_getVars(array(
				'path'    => $arr['pathVars'],
				'strLang' => $valueLang,
			));
			$json = json_encode($vars);
			$classSmarty->assign('varsLoad', $json);
			$contents = $classSmarty->fetch($arr['pathTpl']);

			if (FLAG_OBFUSCATE) {
				$contents = $classEscape->obfuscate(array( 'data' => $contents) );
			}
			$path = $this->_getPath(array(
				'path' => $arr['pathOut'],
				'strLang' => $valueLang,
			));

			$classFile->setData(array(
				'path' => $path,
				'data' => $contents,
			));
		}
	}


    /**
		$this->_getVars(array(
			'path'    => '',
			'strLang' => '',
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

		$vars = $classEscape->getVars(array(
			'data'    => $arr['path'],
			'arr' => array(
				array('before' => '<strTitle>', 'after' => $arr['strTitle'],),
				array('before' => '<strLang>', 'after' => $arr['strLang'],),
			),
		));

		return $vars;
	}

	/**
	 * $arr = array(
	 *	 'path' => string,
	 * )
	 */
	protected function _setDbTable($arr)
	{
		global $classRequest;
		global $classDb;
		$dbh = $classDb->getHandle();

		$vars = $this->_getVars(array(
			'path'    => $arr['path'],
			'strLang' => '',
		));

		$flag55 = $classDb->checkVersion55();

		$arrTableList = $classDb->getTableList();

		$array = $vars;
		$flag = 0;
		try {
			$dbh->beginTransaction();

			foreach ($array as $key => $value) {

				//save
				$strTable = strtolower($array[$key]['table']);
				if ($arrTableList[$strTable]) {
					$strTempTable = $array[$key]['table'] . 'Temp';
					$sql = 'create table ' . $strTempTable . ' like ' . $array[$key]['table'] . ';';
					$stmt = $dbh->prepare($sql);
					$stmt->execute();

					$sql = 'insert into ' . $strTempTable . ' select * from ' . $array[$key]['table'] . ';';
					$stmt = $dbh->prepare($sql);
					$stmt->execute();

				}

				//drop
				$sql = 'drop table if exists ' . $array[$key]['table'] . ';';
				$stmt = $dbh->prepare($sql);
				$stmt->execute();

				//create
				$sql = 'create table ';
				$sql .= $array[$key]['table'] . '(';

				$arrayChild = $array[$key]['index'];
				$numLimit = count($arrayChild) - 1;
				$strColumn = '';
				foreach ($arrayChild as $keyChild => $valueChild) {
					$strColumn .= ' '
								. $arrayChild[$keyChild]['column']
								. ' '
								. $arrayChild[$keyChild]['type'];

					if ($keyChild != $numLimit) {
						$strColumn .= ',';

					}
				}
				if ($flag55) {
					$array[$key]['db'] = str_replace('type', 'engine', $array[$key]['db']);
				}
				$sql .= $strColumn . ')' . $array[$key]['db'] . ';';

				$stmt = $dbh->prepare($sql);
				$stmt->execute();
			}

			//drop
			foreach ($array as $key => $value) {
				$strTempTable = $array[$key]['table'] . 'Temp';
				$sql = 'drop table if exists ' . $strTempTable . ';';
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
			}


			$dbh->commit();

		} catch (PDOException $e) {

			$dbh->rollBack();
			$flag = 1;
			if (FLAG_TEST) {
				var_dump($e->getMessage());
				exit;
			}
			foreach ($array as $key => $value) {
				if (!$arraySuccess[$num]) {
					break;
				}
				$num++;
				//drop
				$sql = 'drop table if exists ' . $array[$key]['table'] . ';';
				$stmt = $dbh->prepare($sql);
				$stmt->execute();

				//restore
				$strTable = strtolower($array[$key]['table']);
				if ($arrTableList[$strTable]) {
					$strTempTable = $array[$key]['table'] . 'Temp';
					$sql = 'create table ' . $array[$key]['table'] . ' like ' . $strTempTable . ';';
					$stmt = $dbh->prepare($sql);
					$stmt->execute();

					$sql = 'insert into ' . $array[$key]['table'] . ' select * from ' . $strTempTable . ';';
					$stmt = $dbh->prepare($sql);
					$stmt->execute();

					//drop
					$sql = 'drop table if exists ' . $strTempTable . ';';
					$stmt = $dbh->prepare($sql);
					$stmt->execute();
				}
			}
		}

		return $flag;
	}


	/**
	 * $arr = array(
	 *	 'path' => string,
	 * )
	 */
	protected function _setDbInsert($arr)
	{
		global $classRequest;
		global $classDb;
		$dbh = $classDb->getHandle();

		$vars = $this->_getVars(array(
			'path'    => $arr['path'],
			'strLang' => '',
		));

		$array = $vars;

		foreach ($array as $key => $value) {
			$method = '_setDbInsert' . ucwords($array[$key]['table']);
			if (method_exists($this, $method)) {
				$this->$method();
			}
		}
	}

     /**
     * $arr = array(
     *     'path' => string,
     * )
     */
	protected function _getLang($arr)
	{
		$array = scandir($arr['path']);
		$arrayNew = array();
		$num = 0;
		foreach ($array as $key => $value) {
			$pathFile = $arr['path'] . $value;
			if ( preg_match( "/^\.{1,2}$/", $value) || !is_dir($pathFile)) {
				continue;
			}
			$arrayNew[$num] = $value;
			$num++;
		}

		return $arrayNew;
	}
}