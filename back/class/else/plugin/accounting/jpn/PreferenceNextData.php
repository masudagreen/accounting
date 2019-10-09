<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_PreferenceNextData extends Code_Else_Plugin_Accounting_Jpn_Preference
{
	protected $_extChildSelf = array(

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

	public function allot($arr)
	{
		$method = '_ini' . ucwords($arr['flagStatus']);
		if (method_exists($this, $method)) {
			$this->$method($arr);

		} else {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}
	}

	/**
	 *
	 */
	protected function _iniCalc($arr)
	{
		global $classPluginAccountingInit;

		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingAccountsEntity;
		global $varsRequest;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];
		$numFiscalPeriodLock = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriodLock'];
		$netCurrent = $numFiscalPeriod - $numFiscalPeriodLock;

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsFlag = array(
			'numFiscalClosingMonth' => (int) $varsRequest['query']['jsonValue']['vars']['NumFiscalClosingMonth'],
			'flagNext'              => (int) $varsRequest['query']['jsonValue']['vars']['NextData'],
			'flagCR'                => ((int) $varsRequest['query']['jsonValue']['vars']['FlagCR'])? 1 : 0 ,
		);

		if (!$varsPluginAccountingAccount['idEntityCurrent']) {
			$this->_sendOld();
		}

		if (!$this->_checkCurrent()) {
			$this->_sendOld();
		}
		if ($netCurrent == 2) {
			if ($this->_checkEditPrev()) {
				$this->_sendOld();

			//past
			} else {
				$varsFlag['flagNext'] = 1;
				$numFiscalBeginningMonth = $varsEntityNation['numFiscalBeginningMonth'];
				$numFiscalTermMonth = $varsEntityNation['numFiscalTermMonth'];
				$numFiscalClosingMonth = $numFiscalBeginningMonth + $numFiscalTermMonth - 1;
				if ($numFiscalClosingMonth > 12) {
					$numFiscalClosingMonth -= 12;
				}
				$varsFlag['numFiscalClosingMonth'] = $numFiscalClosingMonth;
				$varsFlag['flagCR'] = $varsEntityNation['flagCR'];
			}
		}


		$numFiscalTermMonth = $this->_checkNumFiscalTermMonth(array(
			'varsFlag'         => $varsFlag,
			'varsEntityNation' => $varsEntityNation,
		));

		$flagTax = $this->_checkConsumptionTax(array('varsFlag' => $varsFlag,));

		if ($flagTax) {
			$varsAccountTitle = $this->_getAccountTitle(array(
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			));
			$this->sendVars(array(
				'flag'    => 'nextData',
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(
					'idTarget' => 'nextData',
					'idAttest' => $flagTax,
					'str'      => $varsAccountTitle['arrStrTitle'][$flagTax]['strTitle'],
				),
			));
		}

		if ($this->_checkLogApply(array('varsFlag' => $varsFlag,))) {
			$this->sendVars(array(
				'flag'    => 'nextData',
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(
					'idTarget' => 'nextData',
					'idAttest' => 'log',
				),
			));
		}

		//accountingEntity update
		$numFiscalPeriod = $this->_updateDbEntity(array(
			'varsFlag'   => $varsFlag,
			'netCurrent' => $netCurrent,
		));

		if ($netCurrent == 1) {
			//accountingEntityJpn insert
			$this->_insertDbEntityJpn(array(
				'varsFlag'           => $varsFlag,
				'numFiscalPeriod'    => $numFiscalPeriod,
				'numFiscalTermMonth' => $numFiscalTermMonth,
				'varsEntityNation'   => $varsEntityNation,
			));

			//accountingFSJpn insert
			$this->_insertDbFSJpn(array(
				'varsFlag'         => $varsFlag,
				'numFiscalPeriod'  => $numFiscalPeriod,
				'varsEntityNation' => $varsEntityNation,
			));

			//accountingEntityDepartment insert
			$this->_insertDbEntityDepartment(array(
				'varsFlag'         => $varsFlag,
				'numFiscalPeriod'  => $numFiscalPeriod,
			));

			//accountingSubAccountTitleJpn insert
			$this->_insertDbSubAccountTitleJpn(array(
				'varsFlag'         => $varsFlag,
				'numFiscalPeriod'  => $numFiscalPeriod,
			));
		}

		//accountingFSValue insert (accountingEntityDepartmentFSValueJpn)
		if ($varsFlag['flagNext']) {
			$this->_insertDbFSValueJpn(array(
				'varsFlag'         => $varsFlag,
				'numFiscalPeriod'  => $numFiscalPeriod,
			));
		}

		//log calc insert
		if ($varsFlag['flagNext'] && $netCurrent == 2) {
			$this->_insertDbLogCalcJpn(array(
				'varsFlag'         => $varsFlag,
				'numFiscalPeriod'  => $numFiscalPeriod,
			));
		}


		//init pre
		$array = array('entityDepartment', 'departmentFSValue', 'fS', 'fSValue', 'subAccountTitle', 'entity', 'log', 'logCalc');
		foreach ($array as $key => $value) {
			$this->_updateDbPreferenceStamp(array('strColumn' => $value));
		}
		$classPluginAccountingInit->updateInitPreference();
		$classPluginAccountingInit->updateInitEntity();
		$classPluginAccountingInit->updateInitAccountsEntity();
	}


	/**
		(array(
			'varsFlag'         => $varsFlag,
			'numFiscalPeriod'  => $numFiscalPeriod,
		))
	 */
	protected function _insertDbLogCalcJpn($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLog',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
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
					'value'         => $arr['numFiscalPeriod'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'flagApply',
					'flagCondition' => 'ne',
					'value'         => 1,
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'flagRemove',
					'flagCondition' => 'ne',
					'value'         => 1,
				),
			),
		));

		if (!$rows['numRows']) {
			return;
		}

		$arrRows = $this->_getVarsLogCalcLoop(array(
			'arrVarsLog'      => $rows['arrRows'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		$classCalcAccountTitle = $this->_getCalssCalc(array('flagType' => 'AccountTitle'));
		$classCalcSubAccountTitle = $this->_getCalssCalc(array('flagType' => 'SubAccountTitle'));
		$classCalcConsumptionTax = $this->_getCalssCalc(array('flagType' => 'ConsumptionTax'));
		$classCalcLogCalc = $this->_getCalssCalc(array('flagType' => 'LogCalc'));

		$classCalcAccountTitle->allot(array(
			'flagStatus'      => 'add',
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'arrRows'         => $arrRows,
		));
		$classCalcSubAccountTitle->allot(array(
			'flagStatus'      => 'add',
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'arrRows'         => $arrRows,
		));
		$classCalcConsumptionTax->allot(array(
			'flagStatus'      => 'add',
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'arrRows'         => $arrRows,
		));
		$classCalcLogCalc->allot(array(
			'flagStatus'      => 'add',
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'arrRows'         => $arrRows,
		));
	}


	/**
		(array(
			'varsFlag'           => $varsFlag,
			'numFiscalPeriod'    => $numFiscalPeriod,
			'numFiscalTermMonth' => $numFiscalTermMonth,
			'varsEntityNation'   => $varsEntityNation,
		))
	 */
	protected function _insertDbEntityJpn($arr)
	{
		global $classDb;
		global $classTime;

		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);
		$numTimeZone = PLUGIN_ACCOUNTING_NUM_TIME_ZONE;

		$arrDate = $classTime->getLocal(array('stamp' => $arr['varsEntityNation']['stampFiscalBeginning']));
		$numFiscalBeginningYear = $arrDate['year'];
		$numFiscalBeginningMonth = $arrDate['month'] + $arr['varsEntityNation']['numFiscalTermMonth'];
		if ($numFiscalBeginningMonth > 12) {
			$numFiscalBeginningMonth -= 12;
			$numFiscalBeginningYear++;
		}

		$strTimeZone = (-1 * $numTimeZone) . 'hours';
		$numYear = $numFiscalBeginningYear;
		$numMonth = $numFiscalBeginningMonth;
		$dateTime = new DateTime("$numYear-$numMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stamp = $dateTime->format('U');

		$stampFiscalBeginning = $stamp;

		$arrDbColumn = array();
		$arrDbValue = array();
		$array = $arr['varsEntityNation'];
		foreach ($array as $key => $value) {
			if ($key == 'id') {
				continue;

			} elseif ($key == 'numFiscalPeriod') {
				$value = $arr['numFiscalPeriod'];

			} elseif ($key == 'numFiscalBeginningYear') {
				$value = $numFiscalBeginningYear;

			} elseif ($key == 'stampFiscalBeginning') {
				$value = $stampFiscalBeginning;

			} elseif ($key == 'numFiscalBeginningMonth') {
				$value = $numFiscalBeginningMonth;

			} elseif ($key == 'numFiscalTermMonth') {
				$value = $arr['numFiscalTermMonth'];

			} elseif ($key == 'flagCR') {
				$value = $arr['varsFlag']['flagCR'];

			}

			$arrDbColumn[] = $key;
			$arrDbValue[] = $value;
		}

		$classDb->insertRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingEntity' . $strNation,
			'arrColumn' => $arrDbColumn,
			'arrValue'  => $arrDbValue,
		));
	}

	/**
		(array(
			'varsFlag'         => $varsFlag,
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _checkNumFiscalTermMonth($arr)
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		if ($arr['varsFlag']['numFiscalClosingMonth'] < 1 || $arr['varsFlag']['numFiscalClosingMonth'] > 12) {
			$this->_sendOld();
		}

		$numMonth = $arr['varsEntityNation']['numFiscalBeginningMonth'] + $arr['varsEntityNation']['numFiscalTermMonth'];
		if ($numMonth > 12) {
			$numMonth -= 12;
		}
		$numFiscalClosingMonth = $arr['varsFlag']['numFiscalClosingMonth'] + 1;
		if ($numFiscalClosingMonth > 12) {
			$numFiscalClosingMonth -= 12;
		}

		$numEnd = 12;
		$numFiscalTermMonth = 0;
		if ($numFiscalClosingMonth == $numMonth) {
			$numFiscalTermMonth = 12;

		} else {
			for ($i = 0; $i < $numEnd; $i++) {
				if ($numFiscalClosingMonth == $numMonth) {
					break;
				}
				$numFiscalTermMonth++;
				$numMonth++;
				if ($numMonth > 12) {
					$numMonth -= 12;
				}
			}
		}

		return $numFiscalTermMonth;

	}

	/**
		(array(

		))
	 */
	protected function _checkLogApply($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;

		if (!$arr['varsFlag']['flagNext']) {
			return 0;
		}

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLog',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
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
					'strColumn'     => 'flagApply',
					'flagCondition' => 'eq',
					'value'         => 1,
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'flagRemove',
					'flagCondition' => 'ne',
					'value'         => 1,
				),
			),
		));

		if ($rows['numRows']) {
			return 1;
		}

		return 0;
	}


	/**
		(array(
			'varsFlag'         => $varsFlag,
			'numFiscalPeriod'  => $numFiscalPeriod,
		))
	 */
	protected function _insertDbFSValueJpn($arr)
	{
		global $varsPluginAccountingAccount;

		$classCalcAccountTitle = $this->_getCalssCalc(array('flagType' => 'AccountTitle'));
		$classCalcSubAccountTitle = $this->_getCalssCalc(array('flagType' => 'SubAccountTitle'));
		$classCalcAccountTitle->allot(array(
			'flagStatus'          => 'next',
			'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'numFiscalPeriodNext' => $arr['numFiscalPeriod'],
		));
		$classCalcSubAccountTitle->allot(array(
			'flagStatus'          => 'next',
			'numFiscalPeriod'     => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'numFiscalPeriodNext' => $arr['numFiscalPeriod'],
		));
	}

	/**
		(array(
			'varsFSValue'  => $this->_getVarsFSValue(),
			'varsItem'     => $arr['varsItem'],
		))
	 */
	protected function _loopVarsValue($arr)
	{
		$varsJgaapFS = array();
		$array = $this->_extendSelf['arrFS'];
		foreach ($array as $key => $value) {
			if ($value == 'CR') {
				if (!(int) $arr['varsItem']['varsEntityNation']['flagCR']) {
					continue;
				}
			}
			$varsJgaapFS[$value] = $this->_getVarsJgaapFS(array(
				'arrStrTitle' => array(),
				'vars'        => $arr['varsItem']['varsFS']['jsonJgaapFS' . $value],
			));

			$arr['varsFSValue']['jsonJgaapFS' . $value] = $this->_getValueFS(array(
				'flagFS'                => $value,
				'varsJgaapFS'           => $varsJgaapFS[$value]['arrStrTitle'],
				'varsAccountTitle'      => $arr['varsItem']['varsAccountTitle'][$value],
				'varsItem'              => $arr['varsItem'],
				'varsValueAccountTitle' => $arr['varsFSValue']['jsonJgaapAccountTitle' . $value],
			));
		}

		$array = $arr['varsItem']['varsFiscalPeriod'];
		$strFS = 'jsonJgaapFS';
		foreach ($array as $key => $value) {

			if ((int) $arr['varsItem']['varsEntityNation']['flagCR']) {
				//CR Loop
				$this->_loopVarsCalc(array(
					'varsFS'    => $arr['varsItem']['varsFS'][$strFS . 'CR'],
					'varsValue' => &$arr['varsFSValue'][$strFS . 'CR'][$key],
				));

				//CR->PL
				$arr['varsFSValue'][$strFS . 'PL'][$key]['currentTermProductsCost']['sumNext']
					 = $arr['varsFSValue'][$strFS . 'CR'][$key]['currentWorkInProcessNet']['sumNext'];
			}

			//PL Loop
			$this->_loopVarsCalc(array(
				'varsFS'    => $arr['varsItem']['varsFS'][$strFS . 'PL'],
				'varsValue' => &$arr['varsFSValue'][$strFS . 'PL'][$key],
			));

			//PL->BS
			$arr['varsFSValue'][$strFS . 'BS'][$key]['unappropriatedRetainedEarnings']['sumNext']
				  = $arr['varsFSValue']['jsonJgaapAccountTitleBS'][$key]['unappropriatedRetainedEarningsSum']['sumNext'];

			//BS Loop
			$this->_loopVarsCalc(array(
				'varsFS'    => $arr['varsItem']['varsFS'][$strFS . 'BS'],
				'varsValue' => &$arr['varsFSValue'][$strFS . 'BS'][$key],
			));

			$arr['varsFSValue'][$strFS . 'BS'][$key]['assetsSum']['sumNext']
				 = $arr['varsFSValue'][$strFS . 'BS'][$key]['liabilitiesNetAssetsNet']['sumNext'];
		}

		return $arr['varsFSValue'];

	}

	/**
		(array(
			'arrStrTitle'  => array(),
			'vars'         => array(),
		))
	 */
	protected function _getDataVarsFS($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingFS' . $strNation,
			'arrLimit' => array(
				'numStart' => 0, 'numEnd' => 1,
			),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
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
					'value'         => $arr['numFiscalPeriod'],
				),
			),
		));

		$varsFS = $rows['arrRows'][0];
	}

	/**
		(array(
			'arrStrTitle'  => array(),
			'vars'         => array(),
		))
	 */
	protected function _getVarsValue($arr)
	{

		$arrStrTitle = &$arr['arrStrTitle'];

		$array = &$arr['vars'];
		foreach ($array as $key => $value) {

			if ((int) $value['vars']['flagUse']) {
				$arr['arrStrTitle'][$value['vars']['idTarget']]['flagDebit'] = (int) $value['vars']['flagDebit'];
			}

			if ($value['child']) {
				$data = $this->_getVarsJgaapFS(array(
					'vars'        => $array[$key]['child'],
					'arrStrTitle' => $arr['arrStrTitle'],
				));
				$array[$key]['child'] = $data['vars'];
				$arrStrTitle =  $data['arrStrTitle'];
			}
		}

		return $arr;
	}

	/**
		(array(
			'varsFlag'         => $varsFlag,
			'numFiscalPeriod'  => $numFiscalPeriod,
		))
	 */
	protected function _insertDbSubAccountTitleJpn($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingSubAccountTitle' . $strNation,
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
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
			),
		));

		$arrayRows = $rows['arrRows'];
		foreach ($arrayRows as $keyRows => $valueRows) {
			$arrDbColumn = array();
			$arrDbValue = array();
			$array =  $valueRows;
			foreach ($array as $key => $value) {
				if ($key == 'id') {
					continue;

				} elseif ($key == 'stampRegister') {
					$value = $value;

				} elseif ($key == 'stampUpdate') {
					$value = TIMESTAMP;

				} elseif ($key == 'numFiscalPeriod') {
					$value = $arr['numFiscalPeriod'];

				}
				$arrDbColumn[] = $key;
				$arrDbValue[] = $value;
			}

			$classDb->insertRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingSubAccountTitle' . $strNation,
				'arrColumn' => $arrDbColumn,
				'arrValue'  => $arrDbValue,
			));
		}
	}

	/**
		(array(
			'varsFlag'         => $varsFlag,
			'numFiscalPeriod'  => $numFiscalPeriod,
		))
	 */
	protected function _insertDbEntityDepartment($arr)
	{
		global $classDb;

		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingEntityDepartment',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
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
			),
		));

		$arrayRows = $rows['arrRows'];

		foreach ($arrayRows as $keyRows => $valueRows) {
			$arrDbColumn = array();
			$arrDbValue = array();
			$array =  $valueRows;
			foreach ($array as $key => $value) {
				if ($key == 'id') {
					continue;

				} elseif ($key == 'stampRegister') {
					$value = $value;

				} elseif ($key == 'stampUpdate') {
					$value = TIMESTAMP;

				} elseif ($key == 'numFiscalPeriod') {
					$value = $arr['numFiscalPeriod'];

				}
				$arrDbColumn[] = $key;
				$arrDbValue[] = $value;
			}

			$classDb->insertRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingEntityDepartment',
				'arrColumn' => $arrDbColumn,
				'arrValue'  => $arrDbValue,
			));
		}
	}

	/**
		(array(
			'varsFlag'         => $varsFlag,
			'numFiscalPeriod'  => $numFiscalPeriod,
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _insertDbFSJpn($arr)
	{
		global $classDb;

		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$varsEntity = $varsPluginAccountingEntity[$idEntity];
		$strLang = $varsEntity['strLang'];
		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingFS' . $strNation,
			'arrLimit' => array(
				'numStart' => 0, 'numEnd' => 1,
			),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
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
			),
		));
		$varsFS = $rows['arrRows'][0];

		$arrDbColumn = array();
		$arrDbValue = array();
		$array = $varsFS;
		foreach ($array as $key => $value) {
			if ($key == 'id') {
				continue;

			} elseif ($key == 'stampRegister') {
				$value = TIMESTAMP;

			} elseif ($key == 'stampUpdate') {
				$value = TIMESTAMP;

			} elseif ($key == 'numFiscalPeriod') {
				$value = $arr['numFiscalPeriod'];

			} elseif ($key == 'jsonJgaapAccountTitleCR' || $key == 'jsonJgaapFSCR') {
				if ($arr['varsEntityNation']['flagCR']) {
					if ($arr['varsFlag']['flagCR']) {
						$value = ($value)? json_encode($value) : '';

					} else {
						$value = '';
					}

				} else {
					if ($arr['varsFlag']['flagCR']) {
						$str = 'varsJgaapAccountTitleCR';
						if ($key == 'jsonJgaapFSCR') {
							$str = 'varsJgaapFSCR';
						}
						$varsFS = $this->_getVars(array(
							'path'      => $this->_self[$str],
							'strLang'   => $strLang,
							'strNation' => $strNation,
						));
						$value = ($varsFS)? json_encode($varsFS) : '';

					} else {
						$value = '';
					}
				}

			} elseif ($key == 'jsonJgaapAccountTitlePL' || $key == 'jsonJgaapFSPL') {

				if ($arr['varsEntityNation']['flagCR']) {
					if (!$arr['varsFlag']['flagCR']) {
						$varsFS = $this->_removeTreeData(array(
							'vars'     => $value,
							'idTarget' => 'costOfSales',
							'strMatch' => '^(products|productsSum)$',
						));
						$value = $varsFS;
					}

				} else {
					if ($arr['varsFlag']['flagCR']) {
						$str = 'varsJgaapAccountTitlePL';
						if ($key == 'jsonJgaapFSPL') {
							$str = 'varsJgaapFSPL';
						}
						$varsFS = $this->_getVars(array(
							'path'      => $this->_self[$str],
							'strLang'   => $strLang,
							'strNation' => $strNation,
						));

						$arrayBlockProducts = $this->_getTreeBlock(array(
							'vars'     => $varsFS,
							'idTarget' => 'products',
						));
						$arrayBlockProductsSum = $this->_getTreeBlock(array(
							'vars'     => $varsFS,
							'idTarget' => 'productsSum',
						));
						$varsFS = $this->_updateTreeBlock(array(
							'vars'                  => $value,
							'arrayBlockProducts'    => $arrayBlockProducts,
							'arrayBlockProductsSum' => $arrayBlockProductsSum,
							'idTarget'              => 'costOfSales',
						));

						$value = $varsFS;
					}

				}
				$value = json_encode($value);

			} elseif (preg_match("/^json/", $key)) {
				$value = ($value)? json_encode($value) : '';
			}

			$arrDbColumn[] = $key;
			$arrDbValue[] = $value;
		}

		$classDb->insertRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingFS' . $strNation,
			'arrColumn' => $arrDbColumn,
			'arrValue'  => $arrDbValue,
		));
	}

	/**
	 *
		(array(
			'vars'                  => $value,
			'arrayBlockProducts'    => $arrayBlockProducts,
			'arrayBlockProductsSum' => $arrayBlockProductsSum,
			'idTarget'              => 'costOfSales',
		))
	 */
	protected function _updateTreeBlock($arr)
	{
		$strMatch = $arr['strMatch'];

		$arrayBlock = $this->_getTreeBlockChild(array(
			'vars'     => $arr['vars'],
			'idTarget' => $arr['idTarget'],
		));

		$varsTarget = array();
		foreach ($arrayBlock as $key => $value) {
			$varsTarget[] = $value;
		}
		$varsTarget[] = $arr['arrayBlockProducts'];
		$varsTarget[] = $arr['arrayBlockProductsSum'];

		$vars = $this->_insertTreeBlock(array(
			'vars'       => $arr['vars'],
			'idTarget'   => $arr['idTarget'],
			'varsTarget' => $varsTarget,
		));

		return $vars;

	}

	/**
		(array(
			'vars'     => $varsFS,
			'idTarget' => 'products',
		))
	 */
	protected function _getTreeBlock($arr)
	{
		$array = &$arr['vars'];
		foreach ($array as $key => $value) {
			if ($value['vars']['idTarget'] == $arr['idTarget']) {
				return $value;
			}
			if ($value['child']) {
				$flagArr = $this->_getTreeBlock(array(
					'vars'     => $array[$key]['child'],
					'idTarget' => $arr['idTarget'],
				));
				if ($flagArr) {
					return $flagArr;
				}
			}
		}
	}

	/**
		(array(
			'varsFlag'   => $varsFlag,
			'netCurrent' => $netCurrent,
		))
	 */
	protected function _updateDbEntity($arr)
	{
		global $classDb;

		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];

		$numFiscalPeriod = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];
		$numFiscalPeriodLock = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriodLock'];

		$arrDbColumn = array('numFiscalPeriod', 'numFiscalPeriodLock');
		$varsEntity = $varsPluginAccountingEntity[$idEntity];
		$numFiscalPeriodLock = $varsEntity['numFiscalPeriodLock'];
		if ($arr['netCurrent'] == 2) {
			$numFiscalPeriodLock++;

		} else {
			$numFiscalPeriod++;
			if ($arr['varsFlag']['flagNext']) {
				$numFiscalPeriodLock++;
			}
		}
		$arrDbValue = array($numFiscalPeriod, $numFiscalPeriodLock);
		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable' => 'accountingEntity',
			'arrColumn' => $arrDbColumn,
			'flagAnd'  => 1,
			'arrWhere' => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'id',
					'flagCondition' => 'eq',
					'value'         => $varsPluginAccountingAccount['idEntityCurrent'],
				),
			),
			'arrValue'  => $arrDbValue,
		));

		return $numFiscalPeriod;
	}

	/**
		(array(

		))
	 */
	protected function _checkConsumptionTax($arr)
	{
		global $classDb;
		global $varsAccount;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);
		if (!$arr['varsFlag']['flagNext']) {
			return 0;
		}

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingFSValue' . $strNation,
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
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
			),
		));
		$varsFSValue = reset($rows['arrRows']);

		$sumNextReceipt =  $varsFSValue['jsonJgaapAccountTitleBS']['f1']['suspenseReceiptOfConsumptionTaxes']['sumNext'];
		$sumNextPayment =  $varsFSValue['jsonJgaapAccountTitleBS']['f1']['suspensePaymentConsumptionTaxes']['sumNext'];

		if ($sumNextReceipt) {
			return 'suspenseReceiptOfConsumptionTaxes';
		}

		if ($sumNextPayment) {
			return 'suspensePaymentConsumptionTaxes';
		}

		return 0;
	}
}
