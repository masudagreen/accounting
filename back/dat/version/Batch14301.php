<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */

/* 改訂注意点
 * 定数排除、Batchを先頭に付ける
* */

class Code_Batch14301
{
	protected $_selfBatch = array(
		'numVersion'     => 0,
		'numVersionThis' => 14301,
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
			$this->_setBatchHtaccess();
		}
    }

    /*
	 *
	 * */
	protected function _setBatchPath()
	{
		//define('PATH_BATCH14301_CLASS',   PATH_BACK_DAT_VERSION . 'Batch14301/class/');
		//define('PATH_BATCH14301_VARS',   PATH_BACK_DAT_VERSION . 'Batch14301/vars/');
		define('PATH_BATCH14301_TEMPLATES',   PATH_BACK_DAT_VERSION . 'Batch14301/templates/');
	}

    /**

	 */
	protected function _setBatchHtaccess()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$stmt = $dbh->prepare('select * from basePreference;');
		$stmt->execute();

		$varsPreference = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$varsPreference = $row;
			break;
		}

		$path = PATH_BATCH14301_TEMPLATES . 'normal.cgi';
    	if ((int) $varsPreference['flagReject']) {
    		$path = PATH_BATCH14301_TEMPLATES . 'foreign.cgi';
    	}
    	copy($path, PATH_TOP . '/.htaccess');

	}

}