<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_FileImport extends Code_Else_Plugin_Accounting_File
{
	protected $_childSelf = array(

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
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
				}
				exit;
			}
		}
		exit;
	}

	protected function _sendEnd()
	{
		global $varsRequest;

		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
		$this->_setSearch(array('flag' => 1,));
	}


	/**
	 *
	 */
	protected function _iniListImport()
	{
		global $classDb;
		global $varsRequest;
		$dbh = $classDb->getHandle();

		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 0,
			'flagAuthority' => 'insert',
			'idTarget'      => $this->_extSelf['idPreference'],
		));

		if (!$flag) {
			$this->_sendEnd();
		}

		if (!$this->_checkCurrent()) {
			$this->_sendEnd();
		}

		$classCalcFileImport = $this->_getClassAccountingCalc(array('flagType' => 'FileImport'));
		$varsRows = $classCalcFileImport->allot(array(
			'flagStatus'      => 'check',
			'pathDir'         => $this->_self['pathDirFile'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
		));

		if (!$varsRows['numRows']) {
			$this->_sendEnd();
		}

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());

		$arrIdTarget = array();
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
				$arrSql = $this->_updateDbValueAdd(array(
					'vars'         => $value,
					'varsIdNumber' => $varsIdNumber,
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

				$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
				if (preg_match("/^temp/", $flagCurrentFlagNow)) {
					if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
						$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] + 1;

					} elseif (preg_match("/^(tempNext)$/", $flagCurrentFlagNow)) {
						$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] - 1;
					}
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

		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
		$this->_setSearch(array('flag' => 1, 'arrIdTarget' => $arrIdTarget));
	}

	/**
		'arr'          => $arrValue['arr'],
		'varsIdNumber' => $varsIdNumber,
	 */
	protected function _updateDbValueAdd($arr)
	{
		global $classEscape;
		global $varsPluginAccountingAccount;
		global $varsAccount;

		$stampRegister = TIMESTAMP;
		$stampUpdate = $arr['vars']['stampUpdate'];
		$stampArrive = $arr['vars']['stampArrive'];
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$idAccount = $arr['vars']['idAccount'];
		$idAccountUpload = $arr['vars']['idAccountUpload'];
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arr['vars']['arrSpaceStrTag']));

		$strUrl = $arr['vars']['strUrl'];

		$strTitle = $this->_getStrTitle(array(
			'strTitle' => $arr['vars']['strTitle'],
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
				'strTitle'       => $strTitle,
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
			'strTitle'        => $arr['strTitle'],
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
			$flagCheck = $this->_checkStrTitle(array('strTitle' => $strTitle,));
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

		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$arrWhere = array(
			array(
				'flagType'      => 'num',
				'strColumn'     => 'idEntity',
				'flagCondition' => 'eq',
				'value'         => $varsPluginAccountingAccount['idEntityCurrent'],
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
}
