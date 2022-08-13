<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Batch14100
{
	protected $_selfBatch = array(
		'numVersion'     => 0,
		'numVersionThis' => 14100,
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
		$this->_removeBatchPath();
   }

   /*
	 *
	 * */
	protected function _removeBatchPath()
	{
		unlink(PATH_BACK_DAT . "fla/pending.fla");
		unlink(PATH_BACK_DAT . "fla/cake.fla");
		rmdir(PATH_BACK_DAT . "fla");

		unlink(PATH_TOP . "/front/else/lib/flash/pending.swf");
		unlink(PATH_TOP . "/front/else/lib/flash/cake.swf");
		rmdir(PATH_TOP . "/front/else/lib/flash");
	}


}