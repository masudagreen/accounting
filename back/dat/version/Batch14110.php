<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Batch14110
{
	protected $_selfBatch = array(
		'numVersion'     => 0,
		'numVersionThis' => 14110,
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

		if (FLAG_TEST) {
			//$this->_setBatchColumnAdd();
			//exit;

		} else {
			$this->_setBatchColumnAdd();
		}
    }

    /**

	 */
	protected function _setBatchColumnAdd()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$stmt = $dbh->prepare('alter table basePreference add flagReject int(1) unsigned default 0 after jsonIpSubnetAccessAccept;');
		$stmt->execute();

	}


}