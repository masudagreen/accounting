<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Batch13700
{
	protected $_selfBatch = array(
		'numVersion'     => 0,
		'numVersionThis' => 13700,
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
			$this->_setBatchTable();
			$this->_setBatchColumn();
			exit;

		} else {
			$this->_setBatchTable();
			$this->_setBatchColumn();
		}
   }

   /*
	 *
	 * */
	protected function _setBatchPath()
	{
		//define('PATH_BATCH13700_CLASS',   PATH_BACK_DAT_VERSION . 'Batch13700/class/');
		//define('PATH_BATCH13700_VARS',   PATH_BACK_DAT_VERSION . 'Batch13700/vars/');
		define('PATH_BATCH13700_TEMPLATES',   PATH_BACK_DAT_VERSION . 'Batch13700/templates/');
	}

	/**

	 */
	protected function _setBatchTable()
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $classEscape;

		$vars = $classEscape->getVars(array(
			'data' => PATH_BATCH13700_TEMPLATES . 'config.php',
			'arr'  => array(),
		));

		$flag55 = $classDb->checkVersion55();

		$array = $vars;
		foreach ($array as $key => $value) {
			if ($value['table'] == 'baseLoginSecond') {
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

   /**

	 */
	protected function _setBatchColumn()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$stmt = $dbh->prepare('alter table basePreference add flagLoginSecond int(1) unsigned default 0 after flagLoginMail;');
		$stmt->execute();

		$stmt = $dbh->prepare('alter table baseAccount add flagLoginSecond int(1) unsigned default 0 after flagLoginMail;');
		$stmt->execute();

	}
}