<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_LogHouseWrite extends Code_Else_Plugin_Accounting_Jpn_LogHouse
{

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
	protected function _iniDetailWrite()
	{
		global $varsRequest;

		$this->_setWriteData(array(
			'arrId' => array($varsRequest['query']['jsonValue']['idTarget']),
		));
	}

	/**
	 *
	 */
	protected function _iniListWrite()
	{
		global $varsRequest;

		$this->_setWriteData(array(
			'arrId' => $varsRequest['query']['jsonValue']['vars'],
		));
	}

	/**
	 *
	 */
	protected function _setWriteData($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $varsRequest;
		global $varsPluginAccountingAccount;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$vars['varsRule'] = $varsItem;

		$arrVarsLog = $this->_checkWrite(array(
			'arrId'    => $arr['arrId'],
			'varsItem' => $varsItem,
		));

		try {
			$dbh->beginTransaction();

			$vars = $this->_setWrite(array(
				'arrVarsLog' => $arrVarsLog,
				'vars'       => $vars,
			));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$this->_sendComment(array(
			'vars'      => $vars,
			'flagWrite' => 1,
			'idTarget'  => $varsRequest['query']['jsonValue']['idTarget']
		));
	}

	/**
		(array(
			'arrId' => array(),
		))
	 */
	protected function _checkWrite($arr)
	{
		global $classCheck;
		global $classEscape;

		global $varsAccount;
		global $varsPluginAccountingAccount;

		$flagCurrent = $this->_checkCurrent();

		if (!$flagCurrent) {
			$this->_sendOldError();
		}

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllInsert'])) {
			$this->_sendOldError();
		}

		$array = $arr['arrId'];
		$flag = $classCheck->checkValueFormat(array(
			'flagType' => 'num',
			'flagArr'  => 1,
			'value'    => $array,
		));
		if ($flag) {
			$this->_sendOldError();
		}

		$arrVarsLogAdd = array();
		foreach ($array as $key => $value) {
			$varsLog = $this->_getVarsLog(array(
				'idTarget' => $value,
			));
			if (!$varsLog) {
				$this->_sendOldError();
			}
			$arrVarsLogAdd[$value] = $varsLog;
		}
		if (!$arrVarsLogAdd) {
			$this->_sendOldError();
		}

		return $arrVarsLogAdd;
	}

	/**
		(array(
			'arrVarsLog' => $tempData['arrVarsLogAdd'],
			'vars'       => $vars,
		))
	 */
	protected function _setWrite($arr)
	{
		$classCalcLog = $this->_getClassCalc(array('flagType' => 'Log'));

		$data = $this->_getWriteLog(array(
			'arrVarsLog'   => $arr['arrVarsLog'],
			'vars'         => $arr['vars'],
			'classCalcLog' => $classCalcLog,
		));

		$arr['vars']['varsItem']['varsComment']['arrRows'] = $data['arrRows'];

		if (!$data['arrOrder']) {
			return $arr['vars'];
		}

		$tempVarsLog = $this->_setWriteLog(array(
			'arrOrder'     => $data['arrOrder'],
			'classCalcLog' => $classCalcLog,
		));

		$arr['vars'] = $this->_setWriteHistory(array(
			'vars'           => $arr['vars'],
			'arrVarsLog'     => $tempVarsLog,
			'arrVarsLogHouse' => $data['arrOrderLog'],
		));

		return $arr['vars'];
	}

	/**
		array(
			'arrVarsLog'   => $arr['arrVarsLog'],
			'vars'         => $arr['vars'],
			'classCalcLog' => $classCalcLog,
		)
	 */
	protected function _getWriteLog($arr)
	{
		global $varsPluginAccountingAccount;
		global $varsAccount;

		global $classEscape;
		$classCalcLog = &$arr['classCalcLog'];

		$varsWrite = $arr['vars']['varsItem']['varsWrite'];
		$arrRows = array();
		$arrOrder = array();
		$arrOrderLog = array();
		$arraySide = array('Debit', 'Credit');
		$array = $arr['arrVarsLog'];
		foreach ($array as $key => $value) {

			$arrSpaceStrTag = $value['arrSpaceStrTag'];
			$strAddTag = $arr['vars']['varsItem']['strTitle'];

			if (!$arrSpaceStrTag) {
				$arrSpaceStrTag = $strAddTag;

			} else {
				$arrSpaceStrTag .= ' ' . $strAddTag;
			}
			$arrSpaceStrTag .= ' house:' . $value['idLogHouse'];
			$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrSpaceStrTag));
			$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));

			$numVersionEnd = count($value['jsonVersion']) - 1;
			$arrayDetail = &$value['jsonVersion'][$numVersionEnd]['jsonDetail']['varsDetail'];
			$varsEntityNationTemp = $value['jsonVersion'][$numVersionEnd]['jsonDetail']['varsEntityNation'];
			$stampBook = $arr['vars']['varsRule']['varsStampFiscalPeriod']['f1']['stampMax'];
			foreach ($arrayDetail as $keyDetail => $valueDetail) {
				foreach ($arraySide as $keySide => $valueSide) {
					if ($valueSide == 'Debit') {
						continue;
					}
					$flagLost = 0;
					$idAccountTitle = $valueDetail['arr' . $valueSide]['idAccountTitle'];
					$idDepartment = $valueDetail['arr' . $valueSide]['idDepartment'];
					$idSubAccountTitle = $valueDetail['arr' . $valueSide]['idSubAccountTitle'];

					if (!$idSubAccountTitle) {
						$idSubAccountTitle = 'none';
					}

					if (!$idDepartment) {
						$idDepartment = 'none';
					}

					$numValue = $this->_getNumValueTemp(array(
						'varsFlag' => array(
							'flagFS'            => $arr['vars']['varsRule']['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['flagFS'],
							'idAccountTitle'    => $idAccountTitle,
							'idSubAccountTitle' => $idSubAccountTitle,
							'idDepartment'      => $idDepartment,
							'flagFiscalPeriod'  => 'f1',
						),
					));
					$arrayDetail[$keyDetail]['numSum'] = $numValue;

					$numValue = floor($numValue *  $value['numRatio'] / 100);

					$arrayDetail[$keyDetail]['arr' . $valueSide]['numValue'] = $numValue;
					$arrayDetail[$keyDetail]['arr' . $valueSide] = $this->_updateVarsDebit(array(
						'vars'             => $arrayDetail[$keyDetail]['arr' . $valueSide],
						'stampBook'        => $stampBook,
						'varsEntityNation' => $varsEntityNationTemp,
					));

					$arrayDetail[$keyDetail]['arrDebit']['numValue'] = $numValue;
					$arrayDetail[$keyDetail]['arrDebit'] = $this->_updateVarsDebit(array(
						'vars'             => $arrayDetail[$keyDetail]['arrDebit'],
						'stampBook'        => $stampBook,
						'varsEntityNation' => $varsEntityNationTemp,
					));
				}
			}
			$value['jsonVersion'][$numVersionEnd]['jsonDetail']['numSum'] = $numValue;
			$value['jsonVersion'][$numVersionEnd]['jsonDetail']['numSumDebit'] = $numValue;
			$value['jsonVersion'][$numVersionEnd]['jsonDetail']['numSumCredit'] = $numValue;

			if ($numValue <= 0) {
				$arrRows[] = array(
					'strTitle'   => $value['idLogHouse'],
					'strComment' => $varsWrite['strZero'],
					'flagError'  => 1,
				);
				continue;
			}

			$varsPermitHistory = end($value['jsonPermitHistory']);
			$varsOrder = array(
				'numFiscalPeriod'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				'idEntity'                => $varsPluginAccountingAccount['idEntityCurrent'],
				'idAccount'               => $varsAccount['id'],
				'idAccountApply'          => $value['idAccountApply'],
				'flagFiscalReport'        => 'f1',
				'stampBook'               => '',
				'strTitle'                => $value['strTitle'],
				'jsonDetail'              => $value['jsonVersion'][$numVersionEnd]['jsonDetail'],
				'arrCommaIdLogFile'       => '',
				'arrCommaIdAccountPermit' => $value['arrCommaIdAccountPermit'],
				'numSumMax'               => $varsPermitHistory['numSumMax'],
				'arrSpaceStrTag'          => $arrSpaceStrTag,
			);

			$flag = $classCalcLog->allot(array(
				'flagStatus'      => 'check',
				'varsOrder'       => $varsOrder,
				'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				'flagCheck'       => 'Journal',
				'varsItem'        => array(),
			));

			if ($flag) {
				if ($varsWrite['str' . ucwords($flag)]) {
					$arrRows[] = array(
						'strTitle'   => $value['idLogHouse'],
						'strComment' => $varsWrite['str' . ucwords($flag)],
					);

				} else {
					$arrRows[] = array(
						'strTitle'   => $value['idLogHouse'],
						'strComment' => $varsWrite['strData'],
					);
				}
				continue;
			}

			$flag = $classCalcLog->allot(array(
				'flagStatus'      => 'check',
				'varsOrder'       => $varsOrder,
				'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				'flagCheck'       => 'Permit',
				'varsItem'        => array(),
			));
			if ($flag) {
				$arrRows[] = array(
					'strTitle'   => $value['idLogHouse'],
					'strComment' => $varsWrite['strPermit'],
					'flagError'  => 1,
				);
				continue;
			}
			$arrOrder[] = $varsOrder;
			$arrOrderLog[] = $value;
		}

		$data = array(
			'arrOrder'    => $arrOrder,
			'arrOrderLog' => $arrOrderLog,
			'arrRows'     => $arrRows,
		);

		return $data;
	}

	/**
		'arrOrder'     => $data['arrOrder'],
		'classCalcLog' => $classCalcLog,
	 */
	protected function _setWriteLog($arr)
	{
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;
		global $varsAccount;

		$classCalcLog = &$arr['classCalcLog'];

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriodTempNext = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];

		$flag = $classCalcLog->allot(array(
			'flagStatus'              => 'add',
			'arrOrder'                => $arr['arrOrder'],
			'idEntity'                => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagTempPrev'            => (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow))? 1 : 0,
			'numFiscalPeriodTempNext' => $numFiscalPeriodTempNext,
		));
		if ($flag == 'errorDataMax') {
			$this->sendVars(array(
				'flag'    => $flag,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));

		} else if (gettype($flag) != 'array') {
			$this->_sendOldError();
		}
		$arrVarsLog = $flag;

		return $arrVarsLog;
	}

	/**
		(array(
			'vars'           => $arr['vars'],
			'arrVarsLog'     => $tempVarsLog,
			'arrVarsLogHouse' => $data['arrOrderLog'],
		))
	 */
	protected function _setWriteHistory($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsAccount;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$arrColumn = array(
			'jsonWriteHistory',
		);

		$arrError = array();
		$array = $arr['arrVarsLogHouse'];
		foreach ($array as $key => $value) {
			if (!$value['jsonWriteHistory']) {
				$arrWriteHistory = array();

			} else {
				$arrWriteHistory = $value['jsonWriteHistory'];
			}

			$arrWriteHistory[] = array(
				'stampRegister'   => TIMESTAMP,
				'idAccount'       => $varsAccount['id'],
				'idLog'           => $arr['arrVarsLog'][$key]['idLog'],
			);

			$jsonWriteHistory = json_encode($arrWriteHistory);

			$flag = $this->checkTextSize(array(
				'flag'       => 'errorDataMax',
				'str'        => $jsonVersion,
				'flagReturn' => 1,
			));

			if ($flag) {
				$arr['vars']['varsItem']['varsComment']['arrRows'][] = array(
					'strTitle'   => $value['idLogHouse'],
					'strComment' => $arr['vars']['varsItem']['varsWrite']['strSizeHistory'],
				);
				continue;
			}
			$arrValue = array($jsonWriteHistory);
			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingLogHouse' . $strNation,
				'arrColumn' => $arrColumn,
				'flagAnd'  => 1,
				'arrWhere'  => array(
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
						'strColumn'     => 'idLogHouse',
						'flagCondition' => 'eq',
						'value'         => $value['idLogHouse'],
					),
				),
				'arrValue'  => $arrValue,
			));
		}

		return $arr['vars'];
	}



	/**
	 *
	 */
	protected function _sendComment($arr)
	{
		global $varsRequest;

		if ($arr['flagWrite']) {
			if (!$arr['vars']['varsItem']['varsComment']['arrRows']) {
				if (preg_match("/^Detail/", $varsRequest['query']['func'])) {
					$this->_iniDetailReload();

				} else if (preg_match("/^List/", $varsRequest['query']['func'])) {
					$this->_iniListReload();
				}
			}

		} else {
			if (preg_match("/^Detail/", $varsRequest['query']['func'])) {
				$this->_iniDetailReload();

			} else if (preg_match("/^List/", $varsRequest['query']['func'])) {
				$this->_iniListReload();
			}
		}

		$strComment = $this->getHtml(array(
			'vars' => $arr['vars']['varsItem']['varsComment'],
			'path' => $this->_extSelf['tplComment'],
		));

		$this->sendVars(array(
			'flag'    => 'dummy',
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'strComment' => $strComment,
				'idTarget'   => ($arr['idTarget'])? $arr['idTarget'] : '',
			),
		));
	}
}
