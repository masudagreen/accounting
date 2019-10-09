<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_FileOutput extends Code_Else_Plugin_Accounting_File
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

	 */
	public function getDBAuthority($arr)
	{
		global $classCheck;
		global $varsAccount;
		global $varsRequest;

		$varsAuthority = $this->_getVarsAuthority(array());

		$strAuthorityType = 'Output';
		if ($varsRequest['query']['func'] == 'DetailImg') {
			$strAuthorityType = 'Select';
		}

		if ($varsAuthority == 'admin' || $varsAuthority['flagAll' . $strAuthorityType]) {
			$array = array(
				'strSql'   => '',
				'arrValue' => array(),
			);

			return $array;

		} elseif ($varsAuthority['flagMy' . $strAuthorityType]) {
			$array = array(
				'strSql'   => 'idAccount = ? || idAccountUpload = ?',
				'arrValue' => array($varsAccount['id'], $varsAccount['id']),
			);

			return $array;
		}
	}

	/**
	 *
	 */
	protected function _iniDetailImg()
	{
		global $classRequest;

		global $varsAccount;
		global $varsRequest;

		$this->_checkFile();
		$vars = $this->_getFileLog(array('value' => $varsRequest['query']['idTarget']));

		if (!$vars) {
			$path = $this->_self['path404Img'];
			$arrPath = preg_split("/\./", $path);
			$strFileType = end($arrPath);
			$classRequest->outputImg(array(
				'path'         => $path,
				'strFileType'  => $strFileType,
			));
		}

		$varsVersion = $this->_getVarsVersion(array(
			'vars'       => $vars,
			'numVersion' => $varsRequest['query']['numVersion'],
		));

		$strFileType = $varsVersion['strFileType'];
		if (!preg_match("/^(png|jpeg|jpg|gif|bmp)$/", $strFileType)) {
			exit;
		}

		$path = $varsVersion['strUrl'];
		if (!file_exists($path)) {
			$path = $this->_self['path404Img'];
			$arrPath = preg_split("/\./", $path);
			$strFileType = end($arrPath);
		}

		$classRequest->outputImg(array(
			'path'         => $path,
			'strFileType'  => $strFileType,
		));
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

		$this->_checkFile();
		$vars = $this->_getFileLog(array('value' => $varsRequest['query']['jsonValue']['idTarget']));

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

	 */
	protected function _checkFile()
	{
		global $varsRequest;

		$strAuthorityType = 'Output';
		if ($varsRequest['query']['func'] == 'DetailImg') {
			$strAuthorityType = 'Select';
		}

		$varsAuthority = $this->_getVarsAuthority(array());
		if (is_null($varsAuthority)) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
			}
			exit;
		}

		if ($varsAuthority != 'admin') {
			if (!$varsAuthority['flagMy' . $strAuthorityType]) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
				}
				exit;
			}
		}
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
		if (!$array) {
			$array = array();
		}
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

	/**
		(array(
			'value' => 0,
		))
	 */
	protected function _getFileLog($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$rows = $classDb->getSelect(array(
			'idModule'   => 'accounting',
			'strTable'   => 'accountingLogFile',
			'arrLimit'   => array(),
			'arrOrder'   => array(),
			'flagAnd'    => 1,
			'insCurrent' => $this,
			'arrWhere'   => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idEntity',
					'flagCondition' => 'eq',
					'value'         => $varsPluginAccountingAccount['idEntityCurrent'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'numFiscalPeriod',
					'flagCondition' => 'eq',
					'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idLogFile',
					'flagCondition' => 'eq',
					'value'         => $arr['value'],
				),
			),
		));

		return $rows['arrRows'][0];
	}

}
