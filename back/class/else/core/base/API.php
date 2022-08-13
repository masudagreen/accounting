<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Base_API extends Code_Else_Core_Base_Base
{
	protected $_extMethodClass = array(
		'updateSessionAPI'    => 'Session',
		'updateAllSessionAPI' => 'Session',
		'deleteSessionAPI'    => 'Session',
		'deleteAllSessionAPI' => 'Session',
	);

	/**
	 *
	 */
	public function run()
	{
		if (FLAG_TEST) {
			var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
		}
		exit;
	}

	/**
	 *
	 */
	public function allot()
	{
		global $varsRequest;

		$str = $this->_extMethodClass[$varsRequest['query']['api']['method']];
		if (is_null($str)) {
			$this->_sendJSON(array(
				'flag' => 'methodNotExist',
				'data' => __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__,
			));
			exit;
		}

		$path = PATH_BACK_CLASS_ELSE_CORE_BASE . 'api/' . $str . ".php";
		$strClass = 'Code_Else_Core_Base_API_' . $str;
		if (!file_exists($path)) {
			$this->_sendJSON(array(
				'flag' => 'fileNotExist',
				'data' => __CLASS__ . '/' .__FUNCTION__. '/' .__LINE__,
			));
			exit;
		}
		require_once($path);
		$classCall = new $strClass;
		$classCall->run();
	}

	/**
     *
     */
	protected function _sendJSON($arr)
	{
		global $classRequest;

		$json = json_encode($arr['data']);

		$classRequest->send(array(
			'flagType' => 'json',
			'data'     => $json,
		));
		exit;
	}
}
