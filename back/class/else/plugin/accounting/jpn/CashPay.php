<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CashPay extends Code_Else_Plugin_Accounting_Jpn_Cash
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
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
		));

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

		if ($arr['varsItem']['varsPreference']['flagPayWrite']) {
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

		$dataTerm = $this->_getNumFiscalTermStamp(array(
			'varsEntityNation' => $arr['varsItem']['varsEntityNation']
		));

		$tm = TIMESTAMP;
		$arrVarsLogAdd = array();
		foreach ($array as $key => $value) {
			$varsLog = $this->_getVarsLog(array(
				'idTarget'        => $value,
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			));
			if (!$varsLog) {
				$this->_sendOldError();
			}

			if (!(int) $varsLog['flagPay']
				|| (int) $varsLog['flagRemove']
			) {
				continue;
			}

			if (!($dataTerm['stampMin'] <= $varsLog['stampBook'] && $varsLog['stampBook'] <= $dataTerm['stampMax'])) {
				continue;
			}

			$arrVarsLogAdd[$value] = $varsLog;
		}
		if (!$arrVarsLogAdd) {
			$this->_sendOldError();
		}

		return $arrVarsLogAdd;
	}

	/**
	 *
	 */
	protected function _iniDetailPay()
	{
		global $varsRequest;

		$this->_setPayData(array(
			'arrId' => array($varsRequest['query']['jsonValue']['idTarget']),
		));
	}

	/**
	 *
	 */
	protected function _iniListPay()
	{
		global $varsRequest;

		$this->_setPayData(array(
			'arrId' => $varsRequest['query']['jsonValue']['vars'],
		));
	}

	/**
	 *
	 */
	protected function _setPayData($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $varsRequest;
		global $varsPluginAccountingAccount;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
		));

		$tempData = $this->_checkPay(array(
			'arrId'    => $arr['arrId'],
			'varsItem' => $varsItem,
		));

		$flagWrite = 0;

		try {
			$dbh->beginTransaction();

			$this->_setPay(array(
				'arrVarsLogAdd'    => $tempData['arrVarsLogAdd'],
				'arrVarsLogDelete' => $tempData['arrVarsLogDelete'],
			));

			if ($varsItem['varsPreference']['flagPayWrite']) {
				$vars = $this->_setWrite(array(
					'arrVarsLog' => $tempData['arrVarsLogAdd'],
					'vars'       => $vars,
				));
				$flagWrite = 1;
			}

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
			'flagWrite' => $flagWrite,
			'idTarget'  => $varsRequest['query']['jsonValue']['idTarget']
		));
	}

	/**
		(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idEntity'        => $arr['idEntity'],
		))
	 */
	protected function _getVarsItem($arr)
	{
		$varsPreference = $this->_getVarsPreference(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$data = array(
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			'idEntity'         => $arr['idEntity'],
			'varsPreference'   => $varsPreference,
			'varsEntityNation' => $varsEntityNation,
		);

		return $data;
	}

	/**
		(array(
			'arrId' => array(),
		))
	 */
	protected function _checkPay($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $classCheck;
		global $classEscape;

		global $varsAccount;
		global $varsPluginAccountingAccount;

		$flagCurrent = $this->_checkCurrent();

		if (!$flagCurrent) {
			$this->_sendOldError();
		}

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'] || $varsAuthority['flagMyUpdate'])) {
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

		$dataTerm = $this->_getNumFiscalTermStamp(array(
			'varsEntityNation' => $arr['varsItem']['varsEntityNation']
		));

		$arrVarsLogAdd = array();
		$arrVarsLogDelete = array();
		foreach ($array as $key => $value) {
			$varsLog = $this->_getVarsLog(array(
				'idTarget'        => $value,
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			));
			if (!$varsLog) {
				continue;
			}

			if ((int) $varsLog['flagPay']
				|| (int) $varsLog['flagRemove']
			) {
				continue;
			}
			if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate']) && $varsAuthority['flagMyUpdate']) {
				if ($value['idAccount'] != $varsAccount['id']) {
					continue;
				}
			}

			if (!($dataTerm['stampMin'] <= $varsLog['stampBook'] && $varsLog['stampBook'] <= $dataTerm['stampMax'])) {
				continue;
			}

			$arrVarsLogDelete[$value] = $varsLog;

			$varsLog['flagPay'] = 1;
			$varsLog['stampPay'] = TIMESTAMP;

			$arrVarsLogAdd[$value] = $varsLog;
		}
		if (!$arrVarsLogAdd) {
			$this->_sendOldError();
		}

		$data = array(
			'arrVarsLogAdd'    => $arrVarsLogAdd,
			'arrVarsLogDelete' => $arrVarsLogDelete,
		);

		return $data;
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



	/**
		(array(
			'arrId' => array(),
		))
	 */
	protected function _setPay($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;

		$classCalcCash = $this->_getClassCalc(array('flagType' => 'Cash'));

		$arrVarsLogAdd = $arr['arrVarsLogAdd'];
		$arrVarsLogDelete = $arr['arrVarsLogDelete'];

		$array = $arrVarsLogAdd;
		foreach ($array as $key => $value) {

			$flagPay = $value['flagPay'];
			$stampPay = $value['stampPay'];

			$arrayTemp = compact(
				'flagPay',
				'stampPay'
			);
			$arrColumn = array();
			$arrValue = array();
			foreach ($arrayTemp as $keyTemp => $valueTemp) {
				$arrColumn[] = $keyTemp;
				$arrValue[] = $valueTemp;
			}

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingLogCash',
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
						'strColumn'     => 'idLogCash',
						'flagCondition' => 'eq',
						'value'         => $value['idLogCash'],
					),
				),
				'arrValue'  => $arrValue,
			));
		}

		$classCalcCash = $this->_getClassCalc(array('flagType' => 'Cash'));

		$flag = $classCalcCash->allot(array(
			'flagStatus'      => 'addDone',
			'arrRows'         => $arrVarsLogAdd,
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
		));
		if ($flag == 'errorDataMax') {
			$this->sendVars(array(
				'flag'    => $flag,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}

		$flag = $classCalcCash->allot(array(
			'flagStatus'      => 'deletePre',
			'arrRows'         => $arrVarsLogDelete,
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
		));
		if ($flag == 'errorDataMax') {
			$this->sendVars(array(
				'flag'    => $flag,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}

		$this->_updateDbPreferenceStamp(array('strColumn' => 'cash'));
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
			'arrVarsLogCash' => $data['arrOrderLog'],
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
		$classTime = new Code_Else_Lib_Time();

		$classCalcLog = &$arr['classCalcLog'];

		$varsWrite = $arr['vars']['varsItem']['varsWrite'];
		$arrRows = array();
		$arrOrder = array();
		$arrOrderLog = array();
		$array = $arr['arrVarsLog'];
		foreach ($array as $key => $value) {

			$classTime->setTimeZone(array('data' => $varsAccount['numTimeZone']));
			$strTime = $classTime->getDisplay(array(
				'stamp'    => $value['stampBook'],
				'flagType' => 'yearmin',
			));

			$arrSpaceStrTag = $value['arrSpaceStrTag'];
			$strAddTag = $arr['vars']['varsItem']['strTagTitle'];
			if ($value['flagIn'] == 1) {
				$strAddTag .= ' ' . $arr['vars']['varsItem']['strTagIn'];

			} elseif ($value['flagIn'] == 2) {
				$strAddTag .= ' ' . $arr['vars']['varsItem']['strTagMove'];

			} else {
				$strAddTag .= ' ' . $arr['vars']['varsItem']['strTagOut'];
			}
			if (!$arrSpaceStrTag) {
				$arrSpaceStrTag = $strAddTag;

			} else {
				$arrSpaceStrTag .= ' ' . $strAddTag;
			}
			$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrSpaceStrTag));
			$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));

			$varsDetail = end($value['jsonVersion']);
			$varsPermitHistory = end($value['jsonPermitHistory']);
			$varsOrder = array(
				'numFiscalPeriod'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				'idEntity'                => $varsPluginAccountingAccount['idEntityCurrent'],
				'idAccount'               => $value['idAccount'],
				'idAccountApply'          => $value['idAccountApply'],
				'flagFiscalReport'        => 'none',
				'stampBook'               => $strTime,
				'strTitle'                => $value['strTitle'],
				'jsonDetail'              => $varsDetail['jsonDetail'],
				'arrCommaIdLogFile'       => $value['arrCommaIdLogFile'],
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
						'strTitle'   => $value['idLogCash'],
						'strComment' => $varsWrite['str' . ucwords($flag)],
					);

				} else {
					$arrRows[] = array(
						'strTitle'   => $value['idLogCash'],
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
					'strTitle'   => $value['idLogCash'],
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
			'arrVarsLogCash' => $data['arrOrderLog'],
		))
	 */
	protected function _setWriteHistory($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsAccount;
		global $varsPluginAccountingAccount;

		$arrColumn = array(
			'jsonWriteHistory',
		);

		$arrError = array();
		$array = $arr['arrVarsLogCash'];
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
					'strTitle'   => $value['idLogCash'],
					'strComment' => $arr['vars']['varsItem']['varsWrite']['strSizeHistory'],
				);
				continue;
			}
			$arrValue = array($jsonWriteHistory);
			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingLogCash',
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
						'strColumn'     => 'idLogCash',
						'flagCondition' => 'eq',
						'value'         => $value['idLogCash'],
					),
				),
				'arrValue'  => $arrValue,
			));
		}

		return $arr['vars'];
	}
}
