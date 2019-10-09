<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CashOutput extends Code_Else_Plugin_Accounting_Jpn_Cash
{
	protected $_childSelf = array(

	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;

		$this->_checkEntity();

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
		global $classSmarty;
		global $classEscape;
		global $classRequest;

		global $varsAccount;
		global $varsRequest;
		global $varsPluginAccountingAccount;

		$varsAuthority = $this->_getVarsAuthority(array());

		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllOutput'] || $varsAuthority['flagMyOutput'])) {
			$this->_send404Output();
		}

		$varsLog = $this->_getVarsLog(array(
			'idTarget'        => $varsRequest['query']['jsonValue']['idTarget'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
		));
		if (!$varsLog) {
			$this->_send404Output();
		}

		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllOutput']) && $varsAuthority['flagMyOutput']) {
			if ($varsLog['idAccount'] != $varsAccount['id']) {
				$this->_send404Output();
			}
		}

		$id = $varsRequest['query']['jsonValue']['vars']['idTarget'];
		if (!preg_match( "/,$id,/", $varsLog['arrCommaIdLogFile'])) {
			$this->_send404Output();
		}

		$vars = $this->_getFileLog(array(
			'value' => $varsRequest['query']['jsonValue']['vars']['idTarget'],
		));
		if (!$vars) {
			$this->_send404Output();
		}

		$varsVersion = $this->_getVarsVersion(array(
			'vars'       => $vars,
			'numVersion' => $varsRequest['query']['jsonValue']['vars']['numVersion'],
		));
		$strFileName = $varsVersion['strTitle'] . '.' . $varsVersion['strFileType'];

		if (!file_exists($varsVersion['strUrl'])) {
			$this->_send404Output();
		}

		$classRequest->output(array(
			'path'         => $varsVersion['strUrl'],
			'strFileType'  => $varsVersion['strFileType'],
			'strFileName'  => $strFileName,
		));
	}

	/**
		(array(
			'vars'       => array(),
			'numVersion' => 0,
		))
	 */
	protected function _getVarsVersion($arr)
	{
		$array = $arr['vars']['jsonVersion'];
		$num = 1;
		foreach ($array as $key => $value) {
			if ($arr['numVersion'] == $num) {
				return $value;
			}
			$num++;
		}
		if (FLAG_TEST) {
			var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
		}
		exit;
	}
}
