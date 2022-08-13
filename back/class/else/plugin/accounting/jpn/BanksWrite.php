<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_BanksWrite extends Code_Else_Plugin_Accounting_Jpn_Banks
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

		$this->_setWrite(array(
			'arrId' => array($varsRequest['query']['jsonValue']['idTarget']),
		));
	}

	/**
	 *
	 */
	protected function _iniListWrite()
	{
		global $varsRequest;

		$this->_setWrite(array(
			'arrId' => $varsRequest['query']['jsonValue']['vars'],
		));

	}

	/**
		(array(
			'arrId' => array(),
		))
	 */
	protected function _setWrite($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $classCheck;
		global $classEscape;

		global $varsAccount;
		global $varsRequest;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		if (!$this->_checkCurrent()) {
			$this->_sendOldError();
		}

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllInsert'] || $varsAuthority['flagMyInsert'])) {
			$this->_sendOldError();
		}

		$classCalcBanks = $this->_getClassCalc(array('flagType' => 'Banks'));
		$classCalcBanksImport = $this->_getClassCalc(array('flagType' => 'BanksImport'));

		$varsItem = $classCalcBanks->allot(array(
			'flagStatus'      => 'varsItem',
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		if (!$varsItem['varsPreference']['flagAutoImport']) {
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
		$tm = TIMESTAMP;
		$arrVarsLog = array();
		foreach ($array as $key => $value) {
			$varsLog = $this->_getVarsLog(array('idTarget' => $value, 'flagRemove' => 0,));
			if (!$varsLog) {
				$this->_sendOldError();

			} else {
				if (($varsAuthority != 'admin' && !$varsAuthority['flagAllInsert'] && $varsAuthority['flagMyInsert'])
					&& $varsLog['idAccount'] != $varsAccount['id']
				) {
					continue;
				}
			}
			$arrVarsLog[$value] = $varsLog;
		}
		if (!$arrVarsLog) {
			$this->_sendOldError();
		}

		$flagCashInsert = $this->_checkAccess(array(
			'flagAllUse'    => 0,
			'flagAuthority' => 'insert',
			'idTarget'      => $this->_extSelf['idCash'],
		));
		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		$numFiscalPeriodTemp = $this->_getNumFiscalPeriodTemp();

		$flagVarsAdd = $this->_getVarsAdd(array(
			'arrVarsLog' => $arrVarsLog,
			'varsItem'   => $varsItem,
		));

		try {
			$dbh->beginTransaction();

			$arrayDataLog = array();
			$arrayLoop = array($flagVarsAdd['arrayCSV']);
			foreach ($arrayLoop as $keyLoop => $valueLoop) {
				if (!$valueLoop['arrayCSV']) {
					continue;
				}
				$arrayDataLog = array();
				$array = $valueLoop['arrayCSV'];
				foreach ($array as $key => $value) {
					$arrayDataLog[] = $classCalcBanksImport->allot(array(
						'flagStatus'          => 'checkVarsCSVLog',
						'arrayCSV'            => $value,
						'strTitle'            => $valueLoop['strTitle'][$key],
						'flagTemp'            => $valueLoop['flagTemp'][$key],
						'idEntity'            => $varsPluginAccountingAccount['idEntityCurrent'],
						'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
						'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
						'idAccount'           => $varsAccount['id'],
					));
				}

				/*
					'arrVarsLog'          => array(),
					'arrVarsLogTemp'      => array(),
					'arrVarsLogWrite'     => array(),
					'arrVarsLogWriteTemp' => array(),
					'arrayData'           => $arr['arrayData'],
				 */
				$flagVarsAddLog = $classCalcBanksImport->allot(array(
					'flagStatus'          => 'runAddLog',
					'flagType'            => 'banksFile',
					'arrayData'           => $arrayDataLog,
					'varsItem'            => $varsItem,
					'idEntity'            => $varsPluginAccountingAccount['idEntityCurrent'],
					'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
					'flagCurrentFlagNow'  => $flagCurrentFlagNow,
					'idAccount'           => $varsAccount['id'],
					'flagCashInsert'      => $flagCashInsert,
				));
				$arrayDataLog = $flagVarsAddLog['arrayData'];

				if ($flagVarsAddLog['flag'] == 'errorDataMax') {
					$this->sendVars(array(
						'flag'       => $flagVarsAddLog['flag'],
						'stamp'      => $this->getStamp(),
						'numNews'    => $this->getNumNews(),
						'vars'       => array(),
					));
				}

				$flagVarsWriteHistory = $classCalcBanksImport->allot(array(
					'flagStatus'          => 'runWriteHistory',
					'arrayData'           => $flagVarsAddLog['arrayData'],
					'arrLogBanks'         => $flagVarsAdd['arrLogBanks'],
					'arrLogBanksTemp'     => $flagVarsAdd['arrLogBanksTemp'],
					'arrVarsLogWrite'     => $flagVarsAddLog['arrVarsLogWrite'],
					'arrVarsLogWriteTemp' => $flagVarsAddLog['arrVarsLogWriteTemp'],
					'classCalcBanks'      => $classCalcBanks,
					'idEntity'            => $varsPluginAccountingAccount['idEntityCurrent'],
					'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					'numFiscalPeriodTemp' => $numFiscalPeriodTemp,
					'idAccount'           => $varsAccount['id'],
				));
				if ($flagVarsWriteHistory['flag'] == 'errorDataMax') {
					$this->sendVars(array(
						'flag'       => $flagVarsWriteHistory['flag'],
						'stamp'      => $this->getStamp(),
						'numNews'    => $this->getNumNews(),
						'vars'       => array(),
					));
				}
			}

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$strComment = '';
		foreach ($arrayDataLog as $keyDataLog => $valueDataLog) {
			$data = $valueDataLog;
			$arrStatus = $classCalcBanksImport->allot(array(
				'flagStatus'      => 'varsStatusLog',
				'varsStatus'      => $data['varsStatus'],
				'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			));
			$arrStatus['strTitle'] = $data['strTitle'];
			$tplHtml = $varsItem['varsOption']['varsComment']['strStatusLog'];
			$array = $arrStatus;
			foreach ($array as $key => $value) {
				$str = '#{' . $key . '}';
				$tplHtml = str_replace($str, $value, $tplHtml);
			}
			$strComment .= $tplHtml;
		}

		$this->_setSearch(array(
			'flag'       => 'dummy',
			'strComment' => $strComment,
		));
	}

	/**
		(array(
			'valueLog'   => $valueLog,
			'varsStamp'  => $varsStamp,
			'arrValues'  => $arrValues,
			'varsStatus' => $varsStatus,
			'idAccount'  => $arr['idAccount'],
			'numRow'     => $numRow,
			'arrValue'   => $arr['arrValue'],
		))

	 */
	protected function _getVarsAdd($arr)
	{
		$classTime = new Code_Else_Lib_Time();

		$varsStatus = array(
			'numAll' => count($arr['arrVarsLog']),
		);
		$flagVarsData = array(
			'flag'            => '',
			'arrayCSV'        => array(),
			'arrayCSVTemp'    => array(),
			'arrLogBanks'     => array(),
			'arrLogBanksTemp' => array(),
		);

		$arrayNew = array();
		$array = $arr['arrVarsLog'];
		foreach ($array as $key => $value) {
			$strStampBook = $classTime->getDisplay(array(
				'stamp'    => $value['stampBook'],
				'flagType' => 'yearmin',
			));
			$numValueIn = $value['numValueIn'];
			$numValueOut = $value['numValueOut'];
			$arrayCSV = array();
			$arrayCSV[$arr['varsItem']['varsOption']['varsStr']['stampBook']] = $strStampBook;
			$arrayCSV[$arr['varsItem']['varsOption']['varsStr']['numValueIn']] = $numValueIn;
			$arrayCSV[$arr['varsItem']['varsOption']['varsStr']['numValueOut']] = $numValueOut;
			$arrayCSV[$arr['varsItem']['varsOption']['varsStr']['strTitle']] = $value['strTitle'];

			//for import
			$numRowForCalcLogImport = $value['idLogBanks'] - 1;
			$flagVarsData['arrayCSV']['arrayCSV']['dummy'][$numRowForCalcLogImport] = $arrayCSV;
			$flagVarsData['arrayCSV']['strTitle']['dummy'] = '';
			$flagVarsData['arrayCSV']['flagTemp']['dummy'] = 0;

			//for write
			$flagVarsData['arrLogBanks'][$value['idLogBanks']] = $value;
		}

		return $flagVarsData;
	}

	/**

	 */
	protected function _getNumFiscalPeriodTemp()
	{
		global $varsPluginAccountingAccount;

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		$numFiscalPeriodTemp = 0;
		if ((preg_match("/^(tempPrev)$/", $flagCurrentFlagNow))) {
			$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] + 1;

		} elseif ((preg_match("/^(tempNext)$/", $flagCurrentFlagNow))) {
			$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] - 1;
		}

		return $numFiscalPeriodTemp;
	}

	/**
	 *
	 */
	protected function _sendComment($arr)
	{
		global $varsRequest;

		$this->sendVars(array(
			'flag'    => 'dummy',
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'strComment' => $arr['strComment'],
				'idTarget' => ($arr['idTarget'])? $arr['idTarget'] : '',
			),
		));
	}
}
