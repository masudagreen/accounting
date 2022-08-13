<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */

/* 改訂注意点
 * 定数排除、Batchを先頭に付ける
* */

class Code_Batch14310
{
	protected $_selfBatch = array(
		'numVersion' => 0,
		'numVersionThis' => 14310,
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
			//smarty 差し替え
			$this->_replaceBatchSmarty();
		}


}

/*
	 *
	 * */
	protected function _setBatchPath()
	{
		//define('PATH_Batch14310_CLASS',PATH_BACK_DAT_VERSION . 'Batch14310/class/');
		//define('PATH_Batch14310_VARS',PATH_BACK_DAT_VERSION . 'Batch14310/vars/');
		define('PATH_Batch14310',PATH_BACK_DAT_VERSION . 'Batch14310/');
		define('PATH_Batch14310_TEMPLATES',PATH_BACK_DAT_VERSION . 'Batch14310/templates/');
	}

	/**

	 */
	protected function _replaceBatchSmarty()
	{
		$pathSmarty = PATH_BACK_CLASS_ELSE_LIB . 'Smarty';
		$this->loopDirDelete($pathSmarty, true);

		$pathZip = PATH_Batch14310 . 'Smarty.zip';

		$zip = new ZipArchive();
		$flag = $zip->open($pathZip);
		if ($flag === TRUE) {
			$pathCopy = PATH_BACK_CLASS_ELSE_LIB;
			$zip->extractTo($pathCopy);
			$zip->close();

		} else {
			exit;
		}

		global $classSmarty;
		$classSmarty = (FLAG_APC)? apc_fetch('classSmarty') : null;
		if (is_null($classSmarty)) {
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
	}

	function loopDirDelete($dir, $flagDeleteTop)
	{
		if(!$dh = @opendir($dir)) {
			return;
		}
		while (false !== ($obj = readdir($dh))) {
			if($obj == '.' || $obj == '..') {
				continue;
			}
			if (!@unlink($dir . '/' . $obj)) {
				$this->loopDirDelete($dir.'/'.$obj, true);
			}
		}
		closedir($dh);
		if ($flagDeleteTop)
		{
			@rmdir($dir);
		}
		return;
	}




}