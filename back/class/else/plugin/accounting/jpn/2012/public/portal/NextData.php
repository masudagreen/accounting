<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_Portal_NextData_2012_Public extends Code_Else_Plugin_Accounting_Jpn_Portal_NextData
{
	/**


	 */
	protected function _iniCalc($arr)
	{
		global $classPluginAccountingInit;

		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingAccountsEntity;
		global $varsRequest;

		$strNation = PLUGIN_ACCOUNTING_STR_NATION;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];
		$numFiscalPeriodLock = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriodLock'];
		$netCurrent = $numFiscalPeriod - $numFiscalPeriodLock;

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

//unique start
		$varsRequest['query']['jsonValue']['vars']['NumFiscalClosingMonth'] = 12;
//unique end

		$varsFlag = array(
			'numFiscalClosingMonth' => (int) $varsRequest['query']['jsonValue']['vars']['NumFiscalClosingMonth'],
			'flagNext'              => (int) $varsRequest['query']['jsonValue']['vars']['NextData'],
			'flagCR'                => ((int) $varsRequest['query']['jsonValue']['vars']['FlagCR'])? 1 : 0 ,
		);

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

		if ($varsFlag['flagNext']) {
			$flagStrLogId = $this->_checkConsumptionTaxSimpleUnknown(array(
				'varsEntityNation' => $varsEntityNation,
			));
			if ($flagStrLogId) {
				$this->sendVars(array(
					'flag'    => 'nextData',
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(
						'idTarget' => 'nextData',
						'idAttest' => 'unknown',
						'str'      => $flagStrLogId,
					),
				));
			}

			$flagTax = $this->_checkConsumptionTax(array());
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

			//cash
			$classCash = $this->_getClass(array('flagType' => 'NextCash'));
			$flagCash = $classCash->allot(array(
				'flagStatus'       => 'checkPay',
				'varsEntityNation' => $varsEntityNation,
			));
			if ($flagCash) {
				$this->sendVars(array(
					'flag'    => 'nextData',
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(
						'idTarget' => 'nextData',
						'idAttest' => 'logCashPay',
					),
				));
			}

			$flagCash = $classCash->allot(array(
				'flagStatus'       => 'checkDefer',
				'varsEntityNation' => $varsEntityNation,
			));
			if ($flagCash) {
				$this->sendVars(array(
					'flag'    => 'nextData',
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(
						'idTarget' => 'nextData',
						'idAttest' => 'logCashDefer',
					),
				));
			}

			$classLogImportRetry = $this->_getClass(array('flagType' => 'NextLogImportRetry'));
			$flagLogImportRetry = $classLogImportRetry->allot(array(
				'flagStatus'       => 'check',
				'varsEntityNation' => $varsEntityNation,
			));
			if ($flagLogImportRetry) {
				$this->sendVars(array(
					'flag'    => 'nextData',
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(
						'idTarget' => 'nextData',
						'idAttest' => 'logRetry',
					),
				));
			}

			if ($this->_checkLogApply(array())) {
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
		}


		/*
			繰越処理選択 $varsFlag['flagNext'] = 1, $netCurrent = 1
			仮繰越処理選択 $varsFlag['flagNext'] = 0, $netCurrent = 1
			仮繰越処理選択後、繰越処理選択 $varsFlag['flagNext'] = 1, $netCurrent = 2
		*/

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

			//accountingFixedAssetsJpn insert
			$classFixedAssets = $this->_getClass(array('flagType' => 'NextFixedAssets'));
			$classFixedAssets->allot(array(
				'flagStatus'       => 'insertFixedAssets',
				'numFiscalPeriod'  => $numFiscalPeriod,
				'varsEntityNation' => $varsEntityNation,
			));

			//accountingLogFixedAssetsJpn insert
			$classFixedAssets->allot(array(
				'flagStatus'       => 'insertLogFixedAssets',
				'numFiscalPeriod'  => $numFiscalPeriod,
				'varsEntityNation' => $varsEntityNation,
			));

			$classFixedAssets->allot(array(
				'flagStatus'       => 'updateFixedAssets',
				'numFiscalPeriod'  => $numFiscalPeriod,
			));

			//accountingLogFixedAssetsJpn dep
			$flag = $classFixedAssets->allot(array(
				'flagStatus'       => 'update',
				'numFiscalPeriod'  => $numFiscalPeriod,
			));
			if ($flag) {
				$this->sendVars(array(
					'flag'    => 'nextData',
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(
						'idTarget' => 'nextData',
						'idAttest' => 'logFiexedAssets',
					),
				));
			}

			//accountingBudgetJpn insert
			$classBudget = $this->_getClass(array('flagType' => 'NextBudget'));
			$classBudget->allot(array(
				'flagStatus'       => 'insert',
				'numFiscalPeriod'  => $numFiscalPeriod,
				'varsEntityNation' => $varsEntityNation,
			));

			//accountingBreakEvenPointJpn insert
			$classBreakEvenPoint = $this->_getClass(array('flagType' => 'NextBreakEvenPoint'));
			$classBreakEvenPoint->allot(array(
				'flagStatus'       => 'insert',
				'numFiscalPeriod'  => $numFiscalPeriod,
				'varsEntityNation' => $varsEntityNation,
			));

			//accountingFSValue insert (accountingEntityDepartmentFSValueJpn)
			$this->_insertDbFSValueJpn(array(
				'varsFlag'         => $varsFlag,
				'numFiscalPeriod'  => $numFiscalPeriod,
			));

			//accountingFile insert
			$classFile = $this->_getClass(array('flagType' => 'NextFile'));
			$classFile->allot(array(
				'flagStatus'       => 'insert',
				'numFiscalPeriod'  => $numFiscalPeriod,
				'varsEntityNation' => $varsEntityNation,
			));

			//accountingBanks insert
			$classBanks = $this->_getClass(array('flagType' => 'NextBanks'));
			$classBanks->allot(array(
				'flagStatus'       => 'insert',
				'numFiscalPeriod'  => $numFiscalPeriod,
				'varsEntityNation' => $varsEntityNation,
			));

			//accountingLogMail insert
			$classLogMail = $this->_getClass(array('flagType' => 'NextLogMail'));
			$classLogMail->allot(array(
				'flagStatus'       => 'insert',
				'numFiscalPeriod'  => $numFiscalPeriod,
				'varsEntityNation' => $varsEntityNation,
			));

			//accountingCash insert
			$classCash = $this->_getClass(array('flagType' => 'NextCash'));
			$flag = $classCash->allot(array(
				'flagStatus'       => 'insert',
				'numFiscalPeriod'  => $numFiscalPeriod,
				'varsEntityNation' => $varsEntityNation,
			));
			if ($flag) {
				$this->sendVars(array(
					'flag'    => $flag,
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));
			}

			//accountingLogImportJpn insert
			$classLogImport = $this->_getClass(array('flagType' => 'NextLogImport'));
			$classLogImport->allot(array(
				'flagStatus'       => 'insert',
				'numFiscalPeriod'  => $numFiscalPeriod,
				'varsEntityNation' => $varsEntityNation,
			));

			//accountingLogImportRetryJpn insert
			$classLogImport = $this->_getClass(array('flagType' => 'NextLogImportRetry'));
			$classLogImport->allot(array(
				'flagStatus'       => 'insert',
				'numFiscalPeriod'  => $numFiscalPeriod,
				'varsEntityNation' => $varsEntityNation,
			));
		}

		if ($varsFlag['flagNext']) {
			$array = array('SummaryStatement', 'NotesFS', 'DetailedAccount');
			foreach ($array as $key => $value) {
				$classTemp = $this->_getClass(array('flagType' => 'Next' . $value));
				$classTemp->allot(array(
					'flagStatus'       => 'insert',
					'numFiscalPeriod'  => $numFiscalPeriod,
					'varsEntityNation' => $varsEntityNation,
				));
			}
		}

		//init pre
		$array = array(
			'entityDepartment',
			'departmentFSValue',
			'fS',
			'fSValue',
			'subAccountTitle',
			'entity',
			'logFixedAssets',
			'fixedAssets',
			'log',
			'logCalc',
			'summaryStatement',
			'notesFS',
			'detailedAccount',
			'logImport',
			'cash',
			'cashValue',
		);
		foreach ($array as $key => $value) {
			$this->_updateDbPreferenceStamp(array('strColumn' => $value));
		}
		$classPluginAccountingInit->updateInitPreference();
		$classPluginAccountingInit->updateInitEntity();
		$classPluginAccountingInit->updateInitAccountsEntity();

	}
}
