<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */

/* 改訂方法
 * 定数排除、Batch14300を付ける
* */

class Code_Batch14300
{
	protected $_selfBatch = array(
		'numVersion'     => 0,
		'numVersionThis' => 14300,
	);

	function __construct()
	{
		$arr = @func_get_arg(0);
		if (!$arr) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}
		$this->_selfBatch['numVersion'] = $arr['numVersion'];
	}


	/**
	  *
	  */
    public function run()
    {
		if ($this->_selfBatch['numVersion'] >= $this->_selfBatch['numVersionThis']) {
			return;
		}
		$this->_setBatchPath();

		if (FLAG_TEST) {
			exit;

		} else {
			$this->_setBatchTable();
			$this->_setBatchColumn();
			$this->_setBatchHtaccess();
		}
    }

    /**

	 */
	protected function _setBatchHtaccess()
	{
		global $classSmarty;
		global $classFile;

		global $classDb;
		$dbh = $classDb->getHandle();

		unlink(PATH_BACK_DAT . "htaccess/reject.cgi");
		unlink(PATH_BACK_DAT . "htaccess/default.cgi");

		$stmt = $dbh->prepare('select * from basePreference;');
		$stmt->execute();

		$varsPreference = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$varsPreference = $row;
			break;
		}

		if (is_null($varsPreference['flagReject'])) {
			$stmt = $dbh->prepare('alter table basePreference add flagReject int(1) unsigned default 1 after jsonIpSubnetAccessAccept;');
			$stmt->execute();
			$varsPreference['flagReject'] = 0;
		}

		$path = PATH_BATCH14300_TEMPLATES . 'normal.cgi';
    	if ((int) $varsPreference['flagReject']) {
    		$path = PATH_BATCH14300_TEMPLATES . 'foreign.cgi';
    	}
    	copy($path, PATH_TOP . '/.htaccess');

	}

    /**

	 */
	protected function _setBatchColumn()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$stmt = $dbh->prepare('alter table basePreference add flagAccessUnknownMail int(1) unsigned default 0 after flagLoginMail;');
		$stmt->execute();
	}

    /*
	 *
	 * */
	protected function _setBatchPath()
	{
		//define('PATH_BATCH14300_CLASS',   PATH_BACK_DAT_VERSION . 'Batch14300/class/');
		//define('PATH_BATCH14300_VARS',   PATH_BACK_DAT_VERSION . 'Batch14300/vars/');
		define('PATH_BATCH14300_TEMPLATES',   PATH_BACK_DAT_VERSION . 'Batch14300/templates/');
	}

    /**

	 */
	protected function _setBatchTable()
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $classEscape;

		$vars = $classEscape->getVars(array(
			'data' => PATH_BATCH14300_TEMPLATES . 'config.php',
			'arr'  => array(),
		));

		$flag55 = $classDb->checkVersion55();

		$array = $vars;
		foreach ($array as $key => $value) {
			if ($value['table'] == 'baseAccessUnknown') {
				//drop
				$sql = 'drop table if exists ' . $value['table'] . ';';
				$stmt = $dbh->prepare($sql);
				$stmt->execute();

				//create
				$sql = 'create table ';
				$sql .= $value['table'] . '(';

				$arrayChild = $value['index'];
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
					$value['db'] = str_replace('type', 'engine', $value['db']);
				}
				$sql .= $strColumn . ')' . $value['db'] . ';';

				$stmt = $dbh->prepare($sql);
				$stmt->execute();
			}
		}
	}
}