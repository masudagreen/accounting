<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Base_Popup extends Code_Else_Core_Base_Base
{
	protected $_extSelf = array(

	);

    /**
     *
     */
	public function run()
	{
		global $varsRequest;
		if ($varsRequest['query']['func']) {
			$method = '_ini' . $varsRequest['query']['func'];
			if (method_exists($this, $method)) {
				$this->$method();
			} else {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__);
				}
				exit;
			}
		}
		exit;
	}

    /**
     *
     */
	protected function _iniSend()
	{
		$this->sendVars(array(
			'flag'    => 1,
			'stamp'   => 0,
			'numNews' => $this->getNumNews(),
			'vars'    => '',
		));
	}

}
