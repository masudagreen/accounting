<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_CalcFileBoard extends Code_Else_Plugin_Accounting_Accounting
{
	protected $_extChildSelf = array(
		'varsAccount' => array(),
		'flagAdmin'   => 0,
		'idEntity'    => 0,
	);

	/**

	 */
	public function run()
	{
		if (FLAG_TEST) {
			var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
		}
		exit;
	}

	/*
		(array(

		))
	 * */
	public function allot($arr)
	{
		$method = '_ini' . ucwords($arr['flagStatus']);
		if (method_exists($this, $method)) {
			return $this->$method($arr);

		} else {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}
	}

	/**
		(array(
			'flagStatus'      => 'num',
			'varsAccount'     => $varsAccount,
			'flagAdmin'       => $flagAdmin,
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
		))
	 */
	protected function _iniNum($arr)
	{
		$this->_extChildSelf['varsAccount'] = $arr['varsAccount'];
		$this->_extChildSelf['flagAdmin'] = $arr['flagAdmin'];
		$this->_extChildSelf['idEntity'] = $arr['idEntity'];

		$data = array();

		$data['numMail'] = $this->_checkNumMail($arr);
		$data['numRegister'] = $this->_checkNumRegister($arr);

		return $data;
	}

	/**
	 */
	public function getDBAuthority($arr)
	{
		global $varsPluginAccountingAccountsEntity;
		global $varsPluginAccountingAuthority;

		$varsAccount = $this->_extChildSelf['varsAccount'];
		$flagAdmin = $this->_extChildSelf['flagAdmin'];
		$idEntity = $this->_extChildSelf['idEntity'];

		$idAccount = $varsAccount['id'];
		$idAuthority = $varsPluginAccountingAccountsEntity[$idAccount][$idEntity]['idAuthority'];
		$varsAuthority = $varsPluginAccountingAuthority[$idAuthority];

		if (!($varsAuthority['flagMySelect'] || $varsAuthority['flagAllSelect'] || $flagAdmin)) {
			return 0;
		}

		$strSql = '';
		$arrValue = array();

		if ($flagAdmin || $varsAuthority['flagAllSelect']) {
			$array = array(
				'strSql'   => $strSql,
				'arrValue' => $arrValue,
			);

			return $array;

		} elseif ($varsAuthority['flagMySelect']) {
			$strSql .= 'idAccount = ? || idAccountUpload = ?';
			$arrValue[] = $idAccount;
			$arrValue[] = $idAccount;

			$array = array(
				'strSql'   => $strSql,
				'arrValue' => $arrValue,
			);

			return $array;
		}

		return 0;
	}

	/**
		(array(

		))
	 */
	protected function _checkNumMail($arr)
	{
		global $classDb;

		$idEntity = $arr['idEntity'];
		$numFiscalPeriod = $arr['numFiscalPeriod'];

		$classCalcFileImport = $this->_getClassAccountingCalc(array('flagType' => 'FileImport'));
		$varsRows = $classCalcFileImport->allot(array(
			'flagStatus'      => 'check',
			'pathDir'         => $this->_self['pathDirFile'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $arr['idEntity'],
		));

		if (!$varsRows['numRows']) {
			return $varsRows['numRows'];
		}

		$dbh = $classDb->setDbhMaster();
		try {
			$dbh->beginTransaction();

			$varsIdNumber = $this->_getIdAutoIncrement(array(
				'idTarget' => 'idLogFile'
			));
			if (!$varsIdNumber[$idEntity]) {
				$varsIdNumber[$idEntity] = 1;
			}

			$array = $varsRows['arrRows'];
			foreach ($array as $key => $value) {
				$arrSql = $this->_updateDbLogFile(array(
					'vars'            => $value,
					'varsIdNumber'    => $varsIdNumber,
					'numFiscalPeriod' => $arr['numFiscalPeriod'],
					'idEntity'        => $arr['idEntity'],
				));
				if (!$arrSql) {
					continue;
				}
				$classDb->insertRow(array(
					'idModule'  => 'accounting',
					'strTable'  => 'accountingLogFile',
					'arrColumn' => $arrSql['arrColumn'],
					'arrValue'  => $arrSql['arrValue'],
				));
				$arrIdTarget[$varsIdNumber[$idEntity]] = 1;
				$varsIdNumber[$idEntity]++;
				$this->_updateIdAutoIncrement(array(
					'idTarget'   => 'idLogFile',
					'varsTarget' => $varsIdNumber
				));

				if ($arr['numFiscalPeriodTemp']) {
					$numFiscalPeriodTemp = $arr['numFiscalPeriodTemp'];
					$array = $arrSql['arrColumn'];
					foreach ($array as $key => $value) {
						if ($value == 'numFiscalPeriod') {
							$arrSql['arrValue'][$key] = $numFiscalPeriodTemp;
							break;
						}
					}
					$classDb->insertRow(array(
						'idModule'  => 'accounting',
						'strTable'  => 'accountingLogFile',
						'arrColumn' => $arrSql['arrColumn'],
						'arrValue'  => $arrSql['arrValue'],
					));
				}
			}

			$this->_updateDbPreferenceStamp(array('strColumn' => 'logFile'));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		return $varsRows['numRows'];
	}

	/**
		'arr'          => $arrValue['arr'],
		'varsIdNumber' => $varsIdNumber,
	 */
	protected function _updateDbLogFile($arr)
	{
		global $classEscape;

		$stampRegister = TIMESTAMP;
		$stampUpdate = $arr['vars']['stampUpdate'];
		$stampArrive = $arr['vars']['stampArrive'];
		$idEntity = $arr['idEntity'];
		$numFiscalPeriod = $arr['numFiscalPeriod'];
		$idAccount = $arr['vars']['idAccount'];
		$idAccountUpload = $arr['vars']['idAccountUpload'];
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arr['vars']['arrSpaceStrTag']));

		$strUrl = $arr['vars']['strUrl'];

		$strTitle = $this->_getStrTitle(array(
			'strTitle'        => $arr['vars']['strTitle'],
			'idEntity'        => $arr['idEntity'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$strFileType = $arr['vars']['strFileType'];
		$numByte = $arr['vars']['numByte'];
		$numWidth = $arr['vars']['numWidth'];
		$numHeight = $arr['vars']['numHeight'];
		$arrVersion = array(
			array(
				'stampRegister'  => $stampRegister,
				'stampUpdate'    => $stampUpdate,
				'strTitle'       => $strTitle,
				'numByte'        => $numByte,
				'numWidth'       => $numWidth,
				'numHeight'      => $numHeight,
				'strUrl'         => $strUrl,
				'strFileType'    => $strFileType,
				'arrSpaceStrTag' => $arrSpaceStrTag,
			),
		);
		$jsonVersion = json_encode($arrVersion);
		$arrChargeHistory = array(
			array(
				'stampRegister'  => $stampRegister,
				'idAccount'      => $idAccount,
			),
		);
		$jsonChargeHistory = json_encode($arrChargeHistory);
		$idLogFile = $arr['varsIdNumber'][$idEntity];

		$flagRemove = 0;
		$data = array(
			'arrColumn' => array('stampRegister', 'stampUpdate', 'stampArrive', 'idLogFile', 'idAccount', 'idEntity', 'numFiscalPeriod', 'strTitle', 'numByte', 'numWidth', 'numHeight', 'strUrl', 'strFileType', 'arrSpaceStrTag', 'jsonVersion', 'idAccountUpload', 'jsonChargeHistory', 'flagRemove'),
			'arrValue'  => array($stampRegister, $stampUpdate, $stampArrive, $idLogFile, $idAccount, $idEntity, $numFiscalPeriod, $strTitle, $numByte, $numWidth, $numHeight, $strUrl, $strFileType, $arrSpaceStrTag, $jsonVersion, $idAccountUpload, $jsonChargeHistory, $flagRemove),
		);

		return $data;
	}

	/**
		(array(
			'strTitle'        => $arr['vars']['strTitle'],
			'idEntity'        => $arr['idEntity'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _getStrTitle($arr)
	{
		$strTitle = $arr['strTitle'];

		$flag = true;
		$flagError = 0;
		$num = 1;
		$numLimit = 95;
		while ($flag) {
			$flagCheck = $this->_checkStrTitle(array(
				'strTitle'        => $strTitle,
				'idEntity'        => $arr['idEntity'],
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
			));
			if ($flagCheck) {
				$strTitle = mb_substr($arr['strTitle'], 0, $numLimit);
				$strTitle = $strTitle . '(' . $num . ')';
				if (mb_strlen($strTitle) > 100) {
					$numLimit -= 1;
					$strTitle = mb_substr($arr['strTitle'], 0, $numLimit);
					$strTitle = $strTitle . '(' . $num . ')';
				}
				if ($numLimit <= 0) {
					$flagError = 1;
				}
				$num++;
			} else {
				return $strTitle;
				break;
			}
		}
		if ($flagError) {
			return MICROTIMESTAMP;
		}
		return $strTitle;
	}

	/**
		(array(
			'strTitle' => '',
			'idTarget' => 0,
		))
	 */
	protected function _checkStrTitle($arr)
	{
		global $classDb;

		$arrWhere = array(
			array(
				'flagType'      => 'num',
				'strColumn'     => 'idEntity',
				'flagCondition' => 'eq',
				'value'         => $arr['idEntity'],
			),
			array(
				'flagType'      => '',
				'strColumn'     => 'strTitle',
				'flagCondition' => 'eq',
				'value'         => $arr['strTitle'],
			),
		);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogFile',
			'arrLimit' => array(
				'numStart' => 0, 'numEnd' => 1,
			),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => $arrWhere,
		));

		if ($rows['numRows']) {
			return true;
		}
		return false;
	}

	/**
		(array(
			'flagStatus'      => 'data',
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
		))
	 */
	protected function _checkNumRegister($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$arrWhere = array(
			array(
				'flagType'      => 'num',
				'strColumn'     => 'idEntity',
				'flagCondition' => 'eq',
				'value'         => $arr['idEntity'],
			),
			array(
				'flagType'      => 'num',
				'strColumn'     => 'numFiscalPeriod',
				'flagCondition' => 'eq',
				'value'         => $arr['numFiscalPeriod'],
			),
			array(
				'flagType'      => 'num',
				'strColumn'     => 'flagRemove',
				'flagCondition' => 'eq',
				'value'         => 0,
			),
		);

		$idEntity = $arr['idEntity'];
		$numFiscalPeriod = $arr['numFiscalPeriod'];
		$stampRegister = $arr['varsAccount']['jsonStampCheck']['accountingLogFile_' . $idEntity . '_' . $numFiscalPeriod];
		if (!is_null($stampRegister)) {
			$arrWhere[] = array(
				'flagType'      => '',
				'strColumn'     => 'stampRegister',
				'flagCondition' => 'big',
				'value'         => $stampRegister,
			);
		}

		$rows = $classDb->getSelect(array(
			'idModule'   => 'accounting',
			'strTable'   => 'accountingLogFile',
			'arrLimit'    => array(
				'numStart' => 0, 'numEnd' => 1,
			),
			'arrOrder'   => array(),
			'flagAnd'    => 1,
			'insCurrent' => $this,
			'arrWhere'   => $arrWhere,
		));

		if (!$rows['numRows']) {
			return 0;
		}

		return $rows['numRows'];
	}


}
