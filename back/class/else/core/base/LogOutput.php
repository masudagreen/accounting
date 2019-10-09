<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Base_LogOutput extends Code_Else_Core_Base_Log
{
	function __construct()
	{
	}

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
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
				}
				exit;
			}
		}
		exit;
	}

	/**
	 *
	 */
	protected function _iniDetailOutput()
	{
		global $classRequest;
		global $classCheck;

		global $varsAccount;
		global $varsRequest;

		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];
		$flag = $classCheck->checkValueFormat(array(
			'flagType' => 'num',
			'value'    => $idTarget,
		));

		if ($flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
			}
			exit;
		}

		$vars = $this->_getLog(array('value' => $idTarget));
		if (!$vars) {
			$this->_send404Output();
		}

		$strFileType = 'txt';
		$strFileName = $this->_getFileTitle(array(
			'strFileType' => $strFileType,
		));
		$classRequest->output(array(
			'text'         => $vars['jsonQuery'],
			'strFileType'  => $strFileType,
			'strFileName'  => $strFileName,
		));
	}

	/**
		(array(
			'numPage'     => 0,
			'strMenu'     => '',
			'strFileType' => '',
		))
	 */
	protected function _getFileTitle($arr)
	{
		global $classTime;
		global $varsAccount;

		$classTime->setTimeZone(array('data' => $varsAccount['numTimeZone']));
		$arrDate = $classTime->getLocal(array('stamp' => TIMESTAMP));
		$strFileType = $arr['strFileType'];

		$strFileName = $arrDate['strYear']
					 . $arrDate['strMonth']
					 . $arrDate['strDate']
					 . '_'
					 . $arrDate['strHour']
					 . $arrDate['strMin'];

		if ($strFileType) {
			$strFileName .= '.' . $strFileType;
		}

		return $strFileName;
	}

	/**
		(array(
			'value' => 0,
		))
	 */
	protected function _getLog($arr)
	{
		global $classDb;

		$rows = $classDb->getSelect(array(
			'idModule'     => 'base',
			'strTable'     => 'baseAccessLog',
			'arrLimit'     => array(),
			'arrOrder'     => array(),
			'flagAnd'      => 1,
			'flagJsonNone' => 1,
			'arrWhere'     => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'id',
					'flagCondition' => 'eq',
					'value'         => $arr['value'],
				),
			),
		));

		return $rows['arrRows'][0];
	}

}
