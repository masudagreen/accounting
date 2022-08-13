<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_FixedAssetsWrite extends Code_Else_Plugin_Accounting_Jpn_FixedAssets
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

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$array = $arr['arrId'];
		$flag = $classCheck->checkValueFormat(array(
			'flagType' => 'num',
			'flagArr'  => 1,
			'value'    => $array,
		));
		if ($flag) {
			$this->_sendOldError();
		}
		$arrVarsLog = array();
		foreach ($array as $key => $value) {
			$varsLog = $this->_getVarsLog(array('idTarget' => $value, 'flagRemove' => 0));
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

		$data = $this->_getWriteLog(array(
			'vars'       => $vars,
			'varsItem'   => $varsItem,
			'arrVarsLog' => $arrVarsLog,
		));

		$vars['varsItem']['varsComment']['arrRows'] = $data['arrRows'];
		if (!$data['arrOrder']) {
			$this->_sendComment(array(
				'vars' => $vars,
			));
		}

		try {
			$dbh->beginTransaction();

			$tempVarsLog = $this->_setWriteLog(array(
				'arrOrder' => $data['arrOrder'],
			));

			$this->_setWriteHistory(array(
				'vars'            => &$vars,
				'arrVarsLog'      => $tempVarsLog,
				'arrVarsLogFixed' => $data['arrOrderLog'],
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
			'vars'     => $vars,
			'idTarget' => $varsRequest['query']['jsonValue']['idTarget']
		));
	}

	/**
		(array(
			'arrVarsLog'      => $temp,
			'arrVarsLogFixed' => $arrVarsLog,
		))
	 */
	protected function _setWriteHistory($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsAccount;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$arrIdLog = array();
		$array = $arr['arrVarsLog'];
		foreach ($array as $key => $value) {
			$idFixedAssets = $arr['arrVarsLogFixed'][$key]['idFixedAssets'];
			if (is_null($arrIdLog[$idFixedAssets])) {
				$arrIdLog[$idFixedAssets] = array();
			}
			$arrIdLog[$idFixedAssets][] = $value['idLog'];
		}

		$arrColumn = array(
			'jsonWriteHistory',
		);

		$arrayCheck = array();
		$array = $arr['arrVarsLogFixed'];
		foreach ($array as $key => $value) {
			$idFixedAssets = $value['idFixedAssets'];
			$arrayCheck[$idFixedAssets] = $value;
		}

		$arrError = array();
		$array = $arrayCheck;
		foreach ($array as $key => $value) {
			if (!$value['jsonWriteHistory']) {
				$arrWriteHistory = array();
			} else {
				$arrWriteHistory = $value['jsonWriteHistory'];
			}
			$idFixedAssets = $key;
			$arrayData = $arrIdLog[$idFixedAssets];
			foreach ($arrayData as $keyData => $valueData) {
				$arrWriteHistory[] = array(
					'stampRegister'   => TIMESTAMP,
					'idAccount'       => $varsAccount['id'],
					'idLog'           => $valueData,
				);
			}
			$jsonWriteHistory = json_encode($arrWriteHistory);

			$flag = $this->checkTextSize(array(
				'flag'       => 'errorDataMax',
				'str'        => $jsonVersion,
				'flagReturn' => 1,
			));
			if ($flag) {
				$arr['vars']['varsItem']['varsComment']['arrRows'][] = array(
					'strTitle'   => $value['strTitle'],
					'strComment' => $arr['vars']['varsItem']['varsWrite']['strSizeHistory'],
					'flagError'  => 1,
				);
				continue;
			}
			$arrValue = array($jsonWriteHistory);
			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingLogFixedAssets' . $strNation,
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
						'strColumn'     => 'idFixedAssets',
						'flagCondition' => 'eq',
						'value'         => $value['idFixedAssets'],
					),
				),
				'arrValue'  => $arrValue,
			));
		}

	}

	/**
	 *
	 */
	protected function _sendComment($arr)
	{
		global $varsRequest;

		if (!$arr['vars']['varsItem']['varsComment']['arrRows']) {
			if ($varsRequest['query']['func'] == 'DetailWrite') {
				$this->_iniDetailReload();

			} else if ($varsRequest['query']['func'] == 'ListWrite') {
				$this->_iniListReload();
			}
		}

		$strComment = $this->_getHtml(array(
			'varsStr' => $arr['vars']['varsItem']['varsComment'],
			'pathTpl' => $this->_extSelf['tplComment'],
		));

		$this->sendVars(array(
			'flag'    => 'dummy',
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'strComment' => $strComment,
				'idTarget' => ($arr['idTarget'])? $arr['idTarget'] : '',
			),
		));
	}

	/**
	 *
	 */
	protected function _setWriteLog($arr)
	{
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriodTempNext = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];

		$classCalcLog = $this->_getClassCalc(array('flagType' => 'Log'));
		$flag = $classCalcLog->allot(array(
			'flagStatus'              => 'add',
			'arrOrder'                => $arr['arrOrder'],
			'idEntity'                => $idEntity,
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
		}
		$arrVarsLog = $flag;

		return $arrVarsLog;
	}


	/**
		array(
			'vars'       => $vars,
			'varsItem'   => $varsItem,
			'arrVarsLog' => $arrVarsLog,
		)
	 */
	protected function _getWriteLog($arr)
	{
		$arrRows = &$arr['vars']['varsItem']['varsComment']['arrRows'];
		$arrOrder = array();
		$arrOrderLog = array();
		$array = $arr['arrVarsLog'];
		foreach ($array as $key => $value) {
			$flag = $this->_checkWriteOrder(array(
				'vars'     => $arr['vars'],
				'varsLog'  => $value,
				'varsItem' => $arr['varsItem'],
			));
			if ($flag) {
				$arrRows[] = array(
					'strTitle'   => $value['strTitle'],
					'strComment' => $flag,
					'flagError'  => 1,
				);
				continue;
			}

			$flag = $this->_checkWriteOrderDep(array(
				'vars'     => $arr['vars'],
				'varsLog'  => $value,
				'varsItem' => $arr['varsItem'],
			));
			if ($flag) {
				$arrRows[] = array(
					'strTitle'   => $value['strTitle'],
					'strComment' => $flag,
					'flagError'  => 0,
				);
				continue;

			} else {
				$varsOrderDep = $this->_getWriteOrderDep(array(
					'vars'     => $arr['vars'],
					'varsLog'  => $value,
					'varsItem' => $arr['varsItem'],
				));
				$arrOrderLog[] = $value;
				$arrOrder[] = $varsOrderDep;

			}

			$flag = $arr['varsItem']['varsFixedAssets']['flagLossWrite'];
			if (!$flag) {
				continue;
			}

			$flag = $this->_checkWriteOrderLoss(array(
				'vars'     => $arr['vars'],
				'varsLog'  => $value,
				'varsItem' => $arr['varsItem'],
			));

			if ($flag) {
				$arrRows[] = array(
					'strTitle'   => $value['strTitle'],
					'strComment' => $flag,
				);
				continue;

			} else {
				$varsOrderLoss = $this->_getWriteOrderLoss(array(
					'vars'     => $arr['vars'],
					'varsLog'  => $value,
					'varsItem' => $arr['varsItem'],
				));
				if ($varsOrderLoss) {
					$arrOrderLog[] = $value;
					$arrOrder[] = $varsOrderLoss;
				}
			}
		}

		return array(
			'arrOrder'    => $arrOrder,
			'arrOrderLog' => $arrOrderLog,
			'arrRows'     => $arrRows,
		);
	}

	/**
		array(

		)
	 */
	protected function _checkWriteOrder($arr)
	{

		$idAccountTitle = $arr['varsLog']['idAccountTitle'];
		if (!$arr['varsItem']['arrAccountTitle']['arrStrTitle'][$idAccountTitle]) {
			return $arr['vars']['varsItem']['varsWrite']['strIdAccountTitle'];
		}

		$idDepartment = $arr['varsLog']['idDepartment'];
		if ($idDepartment) {
			if (!$arr['varsItem']['arrDepartment']['arrStrTitle'][$idDepartment]) {
				return $arr['vars']['varsItem']['varsWrite']['strIdAccountTitle'];
			}
		}
	}

	/**
		array(

		)
	 */
	protected function _checkWriteOrderDep($arr)
	{
		global $classEscape;

		if ($arr['varsLog']['flagDepMethod'] == 'sum') {
			return $arr['vars']['varsItem']['varsWrite']['strNoneDepSum'];
		}

		if (!$arr['varsLog']['numValueDep'] || $arr['varsLog']['flagDepMethod'] == 'noneDep') {
			return $arr['vars']['varsItem']['varsWrite']['strNoneDep'];
		}

		$sumRatio = 0;
		$array = array('sellingAdminCost', 'productsCost', 'nonOperatingExpenses', 'agricultureCost');
		foreach ($array as $key => $value) {
			$str = ucwords($value);
			if ($arr['varsLog'][$value] != 'none') {
				$idAccountTitle = $arr['varsLog'][$value];
				if ($arr['varsItem']['varsFlagAllot']['flag' . $str]) {
					if (!$arr['varsItem']['arrAccountTitle']['arrStrTitle'][$idAccountTitle]) {
						return $arr['vars']['varsItem']['varsWrite']['strIdAccountTitle'];
					}
					$sumRatio += $arr['varsLog']['numRatio' . $str];
				}
			}
		}

		if (!$sumRatio) {
			return $arr['vars']['varsItem']['varsWrite']['strNoneRatio'];
		}

		$flagType = $arr['varsItem']['varsFixedAssets']['flagFractionDepWrite'];
		$numFiscalTermMonth = $arr['varsItem']['varsEntityNation']['numFiscalTermMonth'];

		$arrCommaDepMonth = $classEscape->splitCommaArray(array('data' => $arr['varsLog']['arrCommaDepMonth']));
		$arrayCheck = array();
		$array = $arrCommaDepMonth;
		foreach ($array as $key => $value) {
			$arrayCheck[$value] = 1;
		}

		$numCommaDepMonth = count($arrCommaDepMonth);
		$numDepMonthStart = reset($arrCommaDepMonth);
		$numDepMonthEnd = end($arrCommaDepMonth);

		$numFiscalMonthStart = $arr['varsItem']['varsEntityNation']['numFiscalBeginningMonth'];
		$numFiscalMonthEnd = $numFiscalMonthStart + $numFiscalTermMonth - 1;
		if ($numFiscalMonthEnd > 12) {
			$numFiscalMonthEnd -= 12;
		}

		$numMonthWrite = 0;
		$flagDepWrite = $arr['varsItem']['varsFixedAssets']['flagDepWrite'];

		if (preg_match("/^f1$/", $flagDepWrite)) {
			if (!$numCommaDepMonth) {
				return $arr['vars']['varsItem']['varsWrite']['strNoneDepPeriod'];
			}

		} elseif (preg_match("/^f2$/", $flagDepWrite)) {
			$numPeriodMonth = 6;
			for ($i = 0 ; $i < 1; $i++) {
				$str = 'f2' . ($i + 1);
				$numStart = $arr['varsItem']['varsEntityNation']['numFiscalBeginningMonth'] + $numPeriodMonth * $i;
				if ($numStart > 12) {
					$numStart -= 12;
				}
				$numEnd = $numStart + $numPeriodMonth;

				$numMonthWrite = $this->_checkWriteNumMonth(array(
					'numStart'   => $numStart,
					'numEnd'     => $numEnd,
					'arrayCheck' => $arrayCheck,
				));
				break;
			}
			if (!$numMonthWrite) {
				return $arr['vars']['varsItem']['varsWrite']['strNoneDepPeriod'];
			}

		} elseif (preg_match("/^f2/", $flagDepWrite)) {
			$numPeriodMonth = 6;
			for ($i = 0 ; $i < 2; $i++) {
				$str = 'f2' . ($i + 1);
				if ($flagDepWrite != $str) {
					continue;
				}
				$numStart = $arr['varsItem']['varsEntityNation']['numFiscalBeginningMonth'] + $numPeriodMonth * $i;
				if ($numStart > 12) {
					$numStart -= 12;
				}
				$numEnd = $numStart + $numPeriodMonth;
				$numMonthWrite = $this->_checkWriteNumMonth(array(
					'numStart'   => $numStart,
					'numEnd'     => $numEnd,
					'arrayCheck' => $arrayCheck,
				));
				break;
			}
			if (!$numMonthWrite) {
				return $arr['vars']['varsItem']['varsWrite']['strNoneDepPeriod'];
			}

		} elseif (preg_match("/^f4/", $flagDepWrite)) {
			$numPeriodMonth = 3;
			for ($i = 0 ; $i < 4; $i++) {
				$str = 'f4' . ($i + 1);
				if ($flagDepWrite != $str) {
					continue;
				}
				$numStart = $arr['varsItem']['varsEntityNation']['numFiscalBeginningMonth'] + $numPeriodMonth * $i;
				if ($numStart > 12) {
					$numStart -= 12;
				}
				$numEnd = $numStart + $numPeriodMonth;
				$numMonthWrite = $this->_checkWriteNumMonth(array(
					'numStart'   => $numStart,
					'numEnd'     => $numEnd,
					'arrayCheck' => $arrayCheck,
				));
				break;
			}
			if (!$numMonthWrite) {
				return $arr['vars']['varsItem']['varsWrite']['strNoneDepPeriod'];
			}

		} else {
			if (!$arrayCheck[$flagDepWrite]) {
				return $arr['vars']['varsItem']['varsWrite']['strNoneDepPeriod'];
			}
		}
	}

	/**
		array(

		)
	 */
	protected function _checkWriteOrderLoss($arr)
	{
		if ($arr['varsLog']['flagDepMethod'] == 'sum') {
			return $arr['vars']['varsItem']['varsWrite']['strNoneLossSum'];
		}

		if ($arr['varsLog']['flagDepDown'] != 'exclusion') {
			return $arr['vars']['varsItem']['varsWrite']['strNoneLoss'];
		}

		if (!($arr['varsLog']['stampDrop'] >= $arr['varsItem']['varsStampFiscalPeriod']['f1']['stampMin']
			&& $arr['varsLog']['stampDrop'] <= $arr['varsItem']['varsStampFiscalPeriod']['f1']['stampMax']
		)) {
			return $arr['vars']['varsItem']['varsWrite']['strNoneLossStamp'];
		}

		$idAccountTitle = $arr['varsLog']['lossOnDisposalOfFixedAssets'];
		if (!$arr['varsItem']['arrAccountTitle']['arrStrTitle'][$idAccountTitle]
			|| $idAccountTitle == 'none'
		) {
			return $arr['vars']['varsItem']['varsWrite']['strNoneLossAccountTitle'];
		}
	}


	protected function _getWriteOrderDep($arr)
	{
		global $varsPluginAccountingAccount;
		global $varsAccount;
		global $classEscape;
		$classTime = new Code_Else_Lib_Time();

		$flagFiscalReport = 'none';
		if ($arr['varsItem']['varsFixedAssets']['flagDepWrite'] == 'f1') {
			$flagFiscalReport = 'f1';

		} elseif ($arr['varsItem']['varsFixedAssets']['flagDepWrite'] == 'f2') {
			$flagFiscalReport = 'f21';
		}

		$stampBook = $this->_getWriteOrderDepStampBook(array(
			'flagFiscalReport' => $flagFiscalReport,
			'varsLog'          => $arr['varsLog'],
			'varsItem'         => $arr['varsItem'],
		));

		$classTime->setTimeZone(array('data' => $varsAccount['numTimeZone']));
		$strTime = $classTime->getDisplay(array(
			'stamp'    => $stampBook,
			'flagType' => 'yearmin',
		));

		$strTitle = $arr['varsLog']['strMemo'];

		$jsonDetail = $this->_getWriteOrderDepJsonDetail(array(
			'varsLog'  => $arr['varsLog'],
			'varsItem' => $arr['varsItem'],
		));

		$arrSpaceStrTag = $arr['varsLog']['arrSpaceStrTag'];
		$strAddTag = $arr['vars']['varsItem']['strTagTitle'] . ' ' . $arr['varsLog']['strTitle'];
		if (!$arrSpaceStrTag) {
			$arrSpaceStrTag = $strAddTag;

		} else {
			$arrSpaceStrTag .= ' ' . $strAddTag;
		}
		$arrSpaceStrTag .= ' fixedAssets:' . $arr['varsLog']['idFixedAssets'];

		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrSpaceStrTag));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));

		$varsOrder = array(
			'numFiscalPeriod'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'                => $varsPluginAccountingAccount['idEntityCurrent'],
			'idAccount'               => $varsAccount['id'],
			'idAccountApply'          => $varsAccount['id'],
			'flagFiscalReport'        => $flagFiscalReport,
			'stampBook'               => $strTime,
			'strTitle'                => $strTitle,
			'jsonDetail'              => $jsonDetail,
			'arrCommaIdLogFile'       => '',
			'arrCommaIdAccountPermit' => '',
			'numSumMax'               => 0,
			'arrSpaceStrTag'          => $arrSpaceStrTag,
		);

		return $varsOrder;
	}

	protected function _getWriteOrderLoss($arr)
	{
		global $varsPluginAccountingAccount;
		global $varsAccount;
		global $classEscape;

		$classTime = new Code_Else_Lib_Time();

		$flagFiscalReport = 'none';

		$stampBook = $arr['varsLog']['stampDrop'];

		$classTime->setTimeZone(array('data' => $varsAccount['numTimeZone']));
		$strTime = $classTime->getDisplay(array(
			'stamp'    => $stampBook,
			'flagType' => 'yearmin',
		));

		$strTitle = $arr['varsLog']['strMemo'];

		$jsonDetail = $this->_getWriteOrderLossJsonDetail(array(
			'varsLog'  => $arr['varsLog'],
			'varsItem' => $arr['varsItem'],
		));

		$arrSpaceStrTag = $arr['varsLog']['arrSpaceStrTag'];
		$strAddTag = $arr['vars']['varsItem']['strTagTitle'] . ' ' . $arr['varsLog']['strTitle'];
		if (!$arrSpaceStrTag) {
			$arrSpaceStrTag = $strAddTag;

		} else {
			$arrSpaceStrTag .= ' ' . $strAddTag;
		}
		$arrSpaceStrTag .= ' fixedAssets:' . $arr['varsLog']['idFixedAssets'];

		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrSpaceStrTag));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));

		$varsOrder = array(
			'numFiscalPeriod'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'                => $varsPluginAccountingAccount['idEntityCurrent'],
			'idAccount'               => $varsAccount['id'],
			'flagFiscalReport'        => $flagFiscalReport,
			'stampBook'               => $strTime,
			'strTitle'                => $strTitle,
			'jsonDetail'              => $jsonDetail,
			'arrCommaIdLogFile'       => '',
			'arrCommaIdAccountPermit' => '',
			'numSumMax'               => 0,
			'arrSpaceStrTag'          => $arrSpaceStrTag,
		);

		return $varsOrder;
	}

	/**
		array(

		)
	 */
	protected function _getWriteOrderDepStampBook($arr)
	{
		$flagDepWrite = $arr['varsItem']['varsFixedAssets']['flagDepWrite'];
		if ($flagDepWrite == 'f2') {
			$stamp = $arr['varsItem']['varsStampFiscalPeriod'][$flagDepWrite . '1']['stampMax'];

		} else {
			$stamp = $arr['varsItem']['varsStampFiscalPeriod'][$flagDepWrite]['stampMax'];
		}

		if (!($arr['flagFiscalReport'] == 'f1' || $arr['flagFiscalReport'] == 'f21')) {
			$stamp += 1 - 86400;
		}

		return $stamp;
	}

	protected function _getWriteOrderLossJsonDetail($arr)
	{
		$varsEntityNation = $arr['varsItem']['varsEntityNation'];

		$flagGeneral = (int) $varsEntityNation['flagConsumptionTaxGeneralRule'];
		$flagConsumptionTaxDeducted = (int) $varsEntityNation['flagConsumptionTaxDeducted'];
		$flagConsumptionTaxIncluding = (int) $varsEntityNation['flagConsumptionTaxIncluding'];
		$flagConsumptionTaxWithoutCalc = (int) $varsEntityNation['flagConsumptionTaxWithoutCalc'];
		$flagConsumptionTaxCalc = (int) $varsEntityNation['flagConsumptionTaxCalc'];
		$flagConsumptionTaxFree = (int) $varsEntityNation['flagConsumptionTaxFree'];

		$jsonDetail = array(
			'idAccountTitleDebit'  => '',
			'idAccountTitleCredit' => '',
			'numSum'               => 0,
			'numSumDebit'          => 0,
			'numSumCredit'         => 0,
			'varsEntityNation'     => array(
				'flagConsumptionTaxFree'         => $varsEntityNation['flagConsumptionTaxFree'],
				'flagConsumptionTaxGeneralRule'  => $varsEntityNation['flagConsumptionTaxGeneralRule'],
				'flagConsumptionTaxDeducted'     => $varsEntityNation['flagConsumptionTaxDeducted'],
				'flagConsumptionTaxIncluding'    => $varsEntityNation['flagConsumptionTaxIncluding'],
				/*journal.js insert
				'flagConsumptionTaxCalc'         => $varsEntityNation['flagConsumptionTaxCalc'],
				'flagConsumptionTaxWithoutCalc'  => $varsEntityNation['flagConsumptionTaxWithoutCalc'],
				'flagConsumptionTaxBusinessType' => $varsEntityNation['flagConsumptionTaxBusinessType'],
				*/
			),
			'varsDetail'               => array(),
			'numVersionConsumptionTax' => 0,
		);

		if ($flagConsumptionTaxFree || $flagConsumptionTaxIncluding) {
			$flagConsumptionTaxWithoutCalc = 1;
		}

		$flagConsumptionTaxGeneralRuleEach = 'none';
		$flagConsumptionTaxGeneralRuleProration = 'none';
		$flagConsumptionTaxSimpleRule = 'none';

		if ((int) $varsEntityNation['flagConsumptionTaxFree']) {
			$flagConsumptionTaxGeneralRuleEach = '';
			$flagConsumptionTaxGeneralRuleProration = '';
			$flagConsumptionTaxSimpleRule = '';

		} else {
			if ((int) $varsEntityNation['flagConsumptionTaxGeneralRule']) {
				if ((int) $varsEntityNation['flagConsumptionTaxDeducted']) {
					$flagConsumptionTaxGeneralRuleProration = '';

				} else {
					$flagConsumptionTaxGeneralRuleEach = '';
				}
				$flagConsumptionTaxSimpleRule = '';

			} else {
				$flagConsumptionTaxGeneralRuleProration = '';
				$flagConsumptionTaxGeneralRuleEach = '';
			}
		}


		$idDepartment = '';
		if ($arr['varsLog']['idDepartment']) {
			$idDepartment = $arr['varsLog']['idDepartment'];
		}

		$idAccountTitleCredit = $arr['varsLog']['idAccountTitle'];
		$numValueCredit = $arr['varsLog']['numValueNet'];

		$idAccountTitleDebit = $arr['varsLog']['accumulatedDepreciation'];
		$numValueDebit = $arr['varsLog']['numValueAccumulated'] + $arr['varsLog']['numValueDep'];

		$idAccountTitleDebitLoss = $arr['varsLog']['lossOnDisposalOfFixedAssets'];
		$numValueDebitLoss = $numValueCredit - $numValueDebit;

		if ($arr['varsLog']['accumulatedDepreciation'] == 'none') {
			$numValueCredit -= $numValueDebit;
		}

		if ($arr['varsLog']['numRatioOperate'] < 100) {
			$flagType = $arr['varsItem']['varsFixedAssets']['flagFractionRatioOperate'];
			$idAccountTitleDebitOperateElse = 'accountsReceivables';
			$num = $numValueDebitLoss * (100 - $arr['varsLog']['numRatioOperate']) / 100;
			$numValueDebitOperateElse = $this->_updateCalc(array(
				'flagType' => $flagType,
				'num'      => $num,
				'numLevel' => 0,
			));
			$numValueDebitLoss -= $numValueDebitOperateElse;
		}

		$jsonDetail['numSum'] = $numValueCredit;
		$jsonDetail['numSumDebit'] = $jsonDetail['numSum'];
		$jsonDetail['numSumCredit'] = $jsonDetail['numSum'];

		$vars = array(
			'id' => '',
			'arrDebit' => array(
				'idAccountTitle'                         => $idAccountTitleDebitLoss,
				'numValue'                               => $numValueDebitLoss,
				'numValueConsumptionTax'                 => '',
				'numRateConsumptionTax'                  => '',
				'idDepartment'                           => $idDepartment,
				'idSubAccountTitle'                      => '',
				'flagConsumptionTaxFree'                 => $flagConsumptionTaxFree,
				'flagConsumptionTaxIncluding'            => $flagConsumptionTaxIncluding,
				'flagConsumptionTaxGeneralRuleEach'      => $flagConsumptionTaxGeneralRuleEach,
				'flagConsumptionTaxGeneralRuleProration' => $flagConsumptionTaxGeneralRuleProration,
				'flagConsumptionTaxSimpleRule'           => $flagConsumptionTaxSimpleRule,
				'flagConsumptionTaxWithoutCalc'          => $flagConsumptionTaxWithoutCalc,
				'flagConsumptionTaxCalc'                 => $flagConsumptionTaxCalc,
			),
			'arrCredit' => array(
				'idAccountTitle'                         => $idAccountTitleCredit,
				'numValue'                               => $numValueCredit,
				'numValueConsumptionTax'                 => '',
				'numRateConsumptionTax'                  => '',
				'idDepartment'                           => $idDepartment,
				'idSubAccountTitle'                      => '',
				'flagConsumptionTaxFree'                 => $flagConsumptionTaxFree,
				'flagConsumptionTaxIncluding'            => $flagConsumptionTaxIncluding,
				'flagConsumptionTaxGeneralRuleEach'      => $flagConsumptionTaxGeneralRuleEach,
				'flagConsumptionTaxGeneralRuleProration' => $flagConsumptionTaxGeneralRuleProration,
				'flagConsumptionTaxSimpleRule'           => $flagConsumptionTaxSimpleRule,
				'flagConsumptionTaxWithoutCalc'          => $flagConsumptionTaxWithoutCalc,
				'flagConsumptionTaxCalc'                 => $flagConsumptionTaxCalc,
			),
		);
		$jsonDetail['varsDetail'][] = $vars;
		if ($arr['varsLog']['accumulatedDepreciation'] != 'none') {
			$vars = array(
				'id' => '',
				'arrDebit' => array(
					'idAccountTitle'                         => $idAccountTitleDebit,
					'numValue'                               => $numValueDebit,
					'numValueConsumptionTax'                 => '',
					'numRateConsumptionTax'                  => '',
					'idDepartment'                           => $idDepartment,
					'idSubAccountTitle'                      => '',
					'flagConsumptionTaxFree'                 => $flagConsumptionTaxFree,
					'flagConsumptionTaxIncluding'            => $flagConsumptionTaxIncluding,
					'flagConsumptionTaxGeneralRuleEach'      => $flagConsumptionTaxGeneralRuleEach,
					'flagConsumptionTaxGeneralRuleProration' => $flagConsumptionTaxGeneralRuleProration,
					'flagConsumptionTaxSimpleRule'           => $flagConsumptionTaxSimpleRule,
					'flagConsumptionTaxWithoutCalc'          => $flagConsumptionTaxWithoutCalc,
					'flagConsumptionTaxCalc'                 => $flagConsumptionTaxCalc,
				),
				'arrCredit' => array(
					'idAccountTitle'                         => '',
					'numValue'                               => '',
					'numValueConsumptionTax'                 => '',
					'numRateConsumptionTax'                  => '',
					'idDepartment'                           => '',
					'idSubAccountTitle'                      => '',
					'flagConsumptionTaxFree'                 => '',
					'flagConsumptionTaxIncluding'            => '',
					'flagConsumptionTaxGeneralRuleEach'      => '',
					'flagConsumptionTaxGeneralRuleProration' => '',
					'flagConsumptionTaxSimpleRule'           => '',
					'flagConsumptionTaxWithoutCalc'          => '',
					'flagConsumptionTaxCalc'                 => '',
				),
			);
			$jsonDetail['varsDetail'][] = $vars;
		}
		if ($numValueDebitOperateElse > 0) {
			$vars = array(
				'id' => '',
				'arrDebit' => array(
					'idAccountTitle'                         => $idAccountTitleDebitOperateElse,
					'numValue'                               => $numValueDebitOperateElse,
					'numValueConsumptionTax'                 => '',
					'numRateConsumptionTax'                  => '',
					'idDepartment'                           => $idDepartment,
					'idSubAccountTitle'                      => '',
					'flagConsumptionTaxFree'                 => $flagConsumptionTaxFree,
					'flagConsumptionTaxIncluding'            => $flagConsumptionTaxIncluding,
					'flagConsumptionTaxGeneralRuleEach'      => $flagConsumptionTaxGeneralRuleEach,
					'flagConsumptionTaxGeneralRuleProration' => $flagConsumptionTaxGeneralRuleProration,
					'flagConsumptionTaxSimpleRule'           => $flagConsumptionTaxSimpleRule,
					'flagConsumptionTaxWithoutCalc'          => $flagConsumptionTaxWithoutCalc,
					'flagConsumptionTaxCalc'                 => $flagConsumptionTaxCalc,
				),
				'arrCredit' => array(
					'idAccountTitle'                         => '',
					'numValue'                               => '',
					'numValueConsumptionTax'                 => '',
					'numRateConsumptionTax'                  => '',
					'idDepartment'                           => '',
					'idSubAccountTitle'                      => '',
					'flagConsumptionTaxFree'                 => '',
					'flagConsumptionTaxIncluding'            => '',
					'flagConsumptionTaxGeneralRuleEach'      => '',
					'flagConsumptionTaxGeneralRuleProration' => '',
					'flagConsumptionTaxSimpleRule'           => '',
					'flagConsumptionTaxWithoutCalc'          => '',
					'flagConsumptionTaxCalc'                 => '',
				),
			);
			$jsonDetail['varsDetail'][] = $vars;
		}

		return $jsonDetail;
	}


	protected function _getWriteOrderDepJsonDetail($arr)
	{
		$varsEntityNation = $arr['varsItem']['varsEntityNation'];

		$flagGeneral = (int) $varsEntityNation['flagConsumptionTaxGeneralRule'];
		$flagConsumptionTaxDeducted = (int) $varsEntityNation['flagConsumptionTaxDeducted'];
		$flagConsumptionTaxIncluding = (int) $varsEntityNation['flagConsumptionTaxIncluding'];
		$flagConsumptionTaxWithoutCalc = (int) $varsEntityNation['flagConsumptionTaxWithoutCalc'];
		$flagConsumptionTaxCalc = (int) $varsEntityNation['flagConsumptionTaxCalc'];
		$flagConsumptionTaxFree = (int) $varsEntityNation['flagConsumptionTaxFree'];

		$jsonDetail = array(
			'idAccountTitleDebit'  => '',
			'idAccountTitleCredit' => '',
			'numSum'               => 0,
			'numSumDebit'          => 0,
			'numSumCredit'         => 0,
			'varsEntityNation'     => array(
				'flagConsumptionTaxFree'         => $varsEntityNation['flagConsumptionTaxFree'],
				'flagConsumptionTaxGeneralRule'  => $varsEntityNation['flagConsumptionTaxGeneralRule'],
				'flagConsumptionTaxDeducted'     => $varsEntityNation['flagConsumptionTaxDeducted'],
				'flagConsumptionTaxIncluding'    => $varsEntityNation['flagConsumptionTaxIncluding'],
				/*journal.js insert
				'flagConsumptionTaxCalc'         => $varsEntityNation['flagConsumptionTaxCalc'],
				'flagConsumptionTaxWithoutCalc'  => $varsEntityNation['flagConsumptionTaxWithoutCalc'],
				'flagConsumptionTaxBusinessType' => $varsEntityNation['flagConsumptionTaxBusinessType'],
				*/
			),
			'varsDetail'               => array(),
			'numVersionConsumptionTax' => 0,
		);

		if ($flagConsumptionTaxFree || $flagConsumptionTaxIncluding) {
			$flagConsumptionTaxWithoutCalc = 1;
		}

		$flagConsumptionTaxGeneralRuleEach = 'none';
		$flagConsumptionTaxGeneralRuleProration = 'none';
		$flagConsumptionTaxSimpleRule = 'none';

		if ((int) $varsEntityNation['flagConsumptionTaxFree']) {
			$flagConsumptionTaxGeneralRuleEach = '';
			$flagConsumptionTaxGeneralRuleProration = '';
			$flagConsumptionTaxSimpleRule = '';

		} else {
			if ((int) $varsEntityNation['flagConsumptionTaxGeneralRule']) {
				if ((int) $varsEntityNation['flagConsumptionTaxDeducted']) {
					$flagConsumptionTaxGeneralRuleProration = '';

				} else {
					$flagConsumptionTaxGeneralRuleEach = '';
				}
				$flagConsumptionTaxSimpleRule = '';

			} else {
				$flagConsumptionTaxGeneralRuleProration = '';
				$flagConsumptionTaxGeneralRuleEach = '';
			}
		}

		$idDepartment = '';
		if ($arr['varsLog']['idDepartment']) {
			$idDepartment = $arr['varsLog']['idDepartment'];
		}

		$idAccountTitleCredit = $arr['varsLog']['accumulatedDepreciation'];
		if ($arr['varsLog']['accumulatedDepreciation'] == 'none') {
			$idAccountTitleCredit = $arr['varsLog']['idAccountTitle'];
		}

		$varsNumValue = $this->_getWriteOrderDepNumValue(array(
			'varsLog'  => $arr['varsLog'],
			'varsItem' => $arr['varsItem'],
		));

		$jsonDetail['numSum'] = $varsNumValue['numValueDep'];
		$jsonDetail['numSumDebit'] = $jsonDetail['numSum'];
		$jsonDetail['numSumCredit'] = $jsonDetail['numSum'];


		$arrayNew = array();
		$array = array('sellingAdminCost', 'productsCost', 'nonOperatingExpenses', 'agricultureCost');
		foreach ($array as $key => $value) {
			$str = ucwords($value);
			if (!$arr['varsItem']['varsFlagAllot']['flag' . $str]) {
				continue;
			}
			if ($arr['varsLog']['numRatio' . $str] <= 0) {
				continue;
			}
			$idAccountTitleDebit = $arr['varsLog'][$value];
			$numValue = $varsNumValue[$value];

			$vars = array(
				'id'       => '',
				'arrDebit' => array(
					'idAccountTitle'                         => $idAccountTitleDebit,
					'numValue'                               => $numValue,
					'numValueConsumptionTax'                 => '',
					'numRateConsumptionTax'                  => '',
					'idDepartment'                           => $idDepartment,
					'idSubAccountTitle'                      => '',
					'flagConsumptionTaxFree'                 => $flagConsumptionTaxFree,
					'flagConsumptionTaxIncluding'            => $flagConsumptionTaxIncluding,
					'flagConsumptionTaxGeneralRuleEach'      => $flagConsumptionTaxGeneralRuleEach,
					'flagConsumptionTaxGeneralRuleProration' => $flagConsumptionTaxGeneralRuleProration,
					'flagConsumptionTaxSimpleRule'           => $flagConsumptionTaxSimpleRule,
					'flagConsumptionTaxWithoutCalc'          => $flagConsumptionTaxWithoutCalc,
					'flagConsumptionTaxCalc'                 => $flagConsumptionTaxCalc,
				),
				'arrCredit' => array(
					'idAccountTitle'                         => $idAccountTitleCredit,
					'numValue'                               => $numValue,
					'numValueConsumptionTax'                 => '',
					'numRateConsumptionTax'                  => '',
					'idDepartment'                           => $idDepartment,
					'idSubAccountTitle'                      => '',
					'flagConsumptionTaxFree'                 => $flagConsumptionTaxFree,
					'flagConsumptionTaxIncluding'            => $flagConsumptionTaxIncluding,
					'flagConsumptionTaxGeneralRuleEach'      => $flagConsumptionTaxGeneralRuleEach,
					'flagConsumptionTaxGeneralRuleProration' => $flagConsumptionTaxGeneralRuleProration,
					'flagConsumptionTaxSimpleRule'           => $flagConsumptionTaxSimpleRule,
					'flagConsumptionTaxWithoutCalc'          => $flagConsumptionTaxWithoutCalc,
					'flagConsumptionTaxCalc'                 => $flagConsumptionTaxCalc,
				),
			);
			$jsonDetail['varsDetail'][] = $vars;
		}

		if ($varsNumValue['numValueDepOperateElse']) {
			$idAccountTitleDebit = 'accountsReceivables';
			$numValue = $varsNumValue['numValueDepOperateElse'];
			$vars = array(
				'id'       => '',
				'arrDebit' => array(
					'idAccountTitle'                         => $idAccountTitleDebit,
					'numValue'                               => $numValue,
					'numValueConsumptionTax'                 => '',
					'numRateConsumptionTax'                  => '',
					'idDepartment'                           => $idDepartment,
					'idSubAccountTitle'                      => '',
					'flagConsumptionTaxFree'                 => $flagConsumptionTaxFree,
					'flagConsumptionTaxIncluding'            => $flagConsumptionTaxIncluding,
					'flagConsumptionTaxGeneralRuleEach'      => $flagConsumptionTaxGeneralRuleEach,
					'flagConsumptionTaxGeneralRuleProration' => $flagConsumptionTaxGeneralRuleProration,
					'flagConsumptionTaxSimpleRule'           => $flagConsumptionTaxSimpleRule,
					'flagConsumptionTaxWithoutCalc'          => $flagConsumptionTaxWithoutCalc,
					'flagConsumptionTaxCalc'                 => $flagConsumptionTaxCalc,
				),
				'arrCredit' => array(
					'idAccountTitle'                         => $idAccountTitleCredit,
					'numValue'                               => $numValue,
					'numValueConsumptionTax'                 => '',
					'numRateConsumptionTax'                  => '',
					'idDepartment'                           => $idDepartment,
					'idSubAccountTitle'                      => '',
					'flagConsumptionTaxFree'                 => $flagConsumptionTaxFree,
					'flagConsumptionTaxIncluding'            => $flagConsumptionTaxIncluding,
					'flagConsumptionTaxGeneralRuleEach'      => $flagConsumptionTaxGeneralRuleEach,
					'flagConsumptionTaxGeneralRuleProration' => $flagConsumptionTaxGeneralRuleProration,
					'flagConsumptionTaxSimpleRule'           => $flagConsumptionTaxSimpleRule,
					'flagConsumptionTaxWithoutCalc'          => $flagConsumptionTaxWithoutCalc,
					'flagConsumptionTaxCalc'                 => $flagConsumptionTaxCalc,
				),
			);
			$jsonDetail['varsDetail'][] = $vars;
		}

		return $jsonDetail;
	}

	/**
		array(

		)
	 */
	protected function _getWriteOrderDepNumValue($arr)
	{
		global $classEscape;

		$flagType = $arr['varsItem']['varsFixedAssets']['flagFractionDepWrite'];

		$numValueDepOperate = $arr['varsLog']['numValueDepOperate'];
		$numValueDepOperateElse = $arr['varsLog']['numValueDep'] - $arr['varsLog']['numValueDepOperate'];

		$numFiscalTermMonth = $arr['varsItem']['varsEntityNation']['numFiscalTermMonth'];

		$arrCommaDepMonth = $classEscape->splitCommaArray(array('data' => $arr['varsLog']['arrCommaDepMonth']));
		$numCommaDepMonth = count($arrCommaDepMonth);

		$numOne = $this->_updateCalc(array(
			'flagType' => $flagType,
			'num'      => $numValueDepOperate / $numCommaDepMonth,
			'numLevel' => 0,
		));

		$numElseOne = $this->_updateCalc(array(
			'flagType' => $flagType,
			'num'      => $numValueDepOperateElse / $numCommaDepMonth,
			'numLevel' => 0,
		));

		$varsDepMonth = $this->_getWriteOrderDepNumValueMonth($arr);

		$flagDepWrite = $arr['varsItem']['varsFixedAssets']['flagDepWrite'];
		if (preg_match("/^f1$/", $flagDepWrite)) {
			$numValueDepOperate = $numValueDepOperate;
			$numValueDepOperateElse = $numValueDepOperateElse;

		} elseif (preg_match("/^f2$/", $flagDepWrite)) {
			if ($varsDepMonth['flagLast'] == $flagDepWrite . '1') {
				$numValueDepOperate = $numValueDepOperate;
				$numValueDepOperateElse = $numValueDepOperateElse;

			} else {
				$numValueDepOperate = $numOne * $varsDepMonth[$flagDepWrite . '1'];
				$numValueDepOperateElse = $numElseOne * $varsDepMonth[$flagDepWrite . '1'];
			}

		} elseif (preg_match("/^(f21|f41)$/", $flagDepWrite)) {
			if ($varsDepMonth['flagLast'] == $flagDepWrite) {
				$numValueDepOperate = $numValueDepOperate;
				$numValueDepOperateElse = $numValueDepOperateElse;

			} else {
				$numValueDepOperate = $numOne * $varsDepMonth[$flagDepWrite];
				$numValueDepOperateElse = $numElseOne * $varsDepMonth[$flagDepWrite];
			}

		} elseif (preg_match("/^f22$/", $flagDepWrite)) {
			if (!$varsDepMonth['f21']) {
				$numValueDepOperate = $numValueDepOperate;
				$numValueDepOperateElse = $numValueDepOperateElse;

			} else {
				$numValueDepOperate = $numValueDepOperate - $numOne * $varsDepMonth['f21'];
				$numValueDepOperateElse = $numValueDepOperateElse - $numElseOne * $varsDepMonth['f21'];
			}

		} elseif (preg_match("/^f42$/", $flagDepWrite)) {
			if ($varsDepMonth['flagLast'] == $flagDepWrite) {
				$numValueDepOperate = $numValueDepOperate - $numOne * $varsDepMonth['f41'];
				$numValueDepOperateElse = $numValueDepOperateElse - $numElseOne * $varsDepMonth['f41'];

			} else {
				$numValueDepOperate = $numOne * $varsDepMonth[$flagDepWrite];
				$numValueDepOperateElse = $numElseOne * $varsDepMonth[$flagDepWrite];
			}

		} elseif (preg_match("/^f43$/", $flagDepWrite)) {
			if ($varsDepMonth['flagLast'] == $flagDepWrite) {
				$numValueDepOperate = $numValueDepOperate
									 - $numOne * $varsDepMonth['f41']
									 - $numOne * $varsDepMonth['f42'];

				$numValueDepOperateElse = $numValueDepOperateElse
										 - $numElseOne * $varsDepMonth['f41']
										 - $numElseOne * $varsDepMonth['f42'];

			} else {
				$numValueDepOperate = $numOne * $varsDepMonth[$flagDepWrite];
				$numValueDepOperateElse = $numElseOne * $varsDepMonth[$flagDepWrite];
			}

		} elseif (preg_match("/^f44$/", $flagDepWrite)) {
			if (!$varsDepMonth['f43']) {
				$numValueDepOperate = $numValueDepOperate;
				$numValueDepOperateElse = $numValueDepOperateElse;

			} else {
				$numValueDepOperate = $numValueDepOperate
									 - $numOne * $varsDepMonth['f41']
									 - $numOne * $varsDepMonth['f42']
									 - $numOne * $varsDepMonth['f43'];

				$numValueDepOperateElse = $numValueDepOperateElse
										 - $numElseOne * $varsDepMonth['f41']
										 - $numElseOne * $varsDepMonth['f42']
										 - $numElseOne * $varsDepMonth['f43'];
			}

		} else {
			$numDepMonthStart = reset($arrCommaDepMonth);
			$numDepMonthEnd = end($arrCommaDepMonth);
			if ($flagDepWrite == $numDepMonthStart) {
				if ($varsDepMonth['flagLast'] == $flagDepWrite) {
					$numValueDepOperate = $numValueDepOperate;
					$numValueDepOperateElse = $numValueDepOperateElse;

				} else {
					$numValueDepOperate = $numOne;
					$numValueDepOperateElse = $numElseOne;
				}

			} elseif ($flagDepWrite != $numDepMonthStart && $flagDepWrite != $numDepMonthEnd) {
				$numValueDepOperate = $numOne;
				$numValueDepOperateElse = $numElseOne;

			} elseif ($flagDepWrite == $numDepMonthEnd) {
				$numPrevMonth = $flagDepWrite - 1;
				if ($numPrevMonth < 1) {
					$numPrevMonth += 12;
				}
				$numStart = $numDepMonthStart;
				$numEnd = $numDepMonthStart + $numFiscalTermMonth;
				$numMonth = $numDepMonthStart;
				for ($i = $numStart; $i < $numEnd; $i++) {
					if ($numMonth > 12) {
						$numMonth -= 12;
					}
					$numValueDepOperate -= $numOne * $varsDepMonth[$numMonth];
					$numValueDepOperateElse -= $numElseOne * $varsDepMonth[$numMonth];
					if ($numPrevMonth == $numMonth) {
						break;
					}
					$numMonth++;
				}
			}
		}

		$numValueDep = $numValueDepOperate + $numValueDepOperateElse;
		$data = array(
			'numValueDep'            => $numValueDep,
			'numValueDepOperate'     => $numValueDepOperate,
			'numValueDepOperateElse' => $numValueDepOperateElse,
		);

		$sumRatio = 0;
		$array = array('sellingAdminCost', 'productsCost', 'nonOperatingExpenses', 'agricultureCost');
		foreach ($array as $key => $value) {
			$str = ucwords($value);
			if (!$arr['varsItem']['varsFlagAllot']['flag' . $str]) {
				continue;
			}
			$sumRatio += $arr['varsLog']['numRatio' . $str];
		}

		$sum = 0;
		$idFraction = '';
		foreach ($array as $key => $value) {
			$str = ucwords($value);
			if (!$arr['varsItem']['varsFlagAllot']['flag' . $str]) {
				continue;
			}
			$strRatio = 'numRatio' . $str;
			if ($strRatio == $arr['varsLog']['flagFraction']) {
				$idFraction = $value;
				continue;
			}
			if ($arr['varsLog']['numRatio' . $str] <= 0) {
				continue;
			}

			$num = $this->_updateCalc(array(
				'flagType' => $flagType,
				'num'      => $data['numValueDepOperate'] * $arr['varsLog']['numRatio' . $str] / $sumRatio,
				'numLevel' => 0,
			));
			$sum += $num;
			$data[$value] = $num;
		}
		$data[$idFraction] = $data['numValueDepOperate'] - $sum;

		return $data;

	}

	/**
		array(

		)
	 */
	protected function _getWriteOrderDepNumValueMonth($arr)
	{
		global $classEscape;

		$data = array();

		$flagDepWrite = $arr['varsItem']['varsFixedAssets']['flagDepWrite'];
		$numFiscalTermMonth = $arr['varsItem']['varsEntityNation']['numFiscalTermMonth'];
		$arrCommaDepMonth = $classEscape->splitCommaArray(array('data' => $arr['varsLog']['arrCommaDepMonth']));

		$arrayCheck = array();

		$numStart = $arr['varsItem']['varsEntityNation']['numFiscalBeginningMonth'];
		$numMonth = $numStart;
		$numEnd = $numStart + $numFiscalTermMonth;
		for ($i = $numStart ; $i < $numEnd; $i++) {
			$arrayCheck[$numMonth] = 0;
			$numMonth++;
			if ($numMonth > 12) {
				$numMonth -= 12;
			}
		}

		$array = $arrCommaDepMonth;
		foreach ($array as $key => $value) {
			$arrayCheck[$value] = 1;
		}

		if (preg_match("/^f1$/", $flagDepWrite)) {
			$data[$flagDepWrite] = count($arrCommaDepMonth);

			return $data;

		} elseif (preg_match("/^f2/", $flagDepWrite)) {
			$numPeriodMonth = 6;
			for ($i = 0 ; $i < 2; $i++) {
				$str = 'f2' . ($i + 1);
				$numStart = $arr['varsItem']['varsEntityNation']['numFiscalBeginningMonth'] + $numPeriodMonth * $i;
				if ($numStart > 12) {
					$numStart -= 12;
				}
				$numEnd = $numStart + $numPeriodMonth;
				$data[$str] = $this->_checkWriteNumMonth(array(
					'numStart'   => $numStart,
					'numEnd'     => $numEnd,
					'arrayCheck' => $arrayCheck,
				));
			}
			for ($i = 2 ; $i > 0; $i--) {
				$str = 'f2' . $i;
				if ($data[$str]) {
					$data['flagLast'] = $str;
					break;
				}
			}

		} elseif (preg_match("/^f4/", $flagDepWrite)) {
			$numPeriodMonth = 3;
			for ($i = 0 ; $i < 4; $i++) {
				$str = 'f4' . ($i + 1);
				$numStart = $arr['varsItem']['varsEntityNation']['numFiscalBeginningMonth'] + $numPeriodMonth * $i;
				if ($numStart > 12) {
					$numStart -= 12;
				}
				$numEnd = $numStart + $numPeriodMonth;
				$data[$str] = $this->_checkWriteNumMonth(array(
					'numStart'   => $numStart,
					'numEnd'     => $numEnd,
					'arrayCheck' => $arrayCheck,
				));
			}
			for ($i = 4 ; $i > 0; $i--) {
				$str = 'f4' . $i;
				if ($data[$str]) {
					$data['flagLast'] = $str;
					break;
				}
			}

		} else {
			for ($i = 1 ; $i <= $numFiscalTermMonth; $i++) {
				if (!$arrayCheck[$i]) {
					$arrayCheck[$i] = 0;
				}
			}
			$data = $arrayCheck;
			$data['flagLast'] = end($array);
		}

		return $data;
	}

	/**
		array(

		)
	 */
	protected function _checkWriteNumMonth($arr)
	{
		$numDepMonth = 0;
		$numMonth = $arr['numStart'];
		for ($i = $arr['numStart'] ; $i < $arr['numEnd']; $i++) {
			if ($arr['arrayCheck'][$numMonth]) {
				$numDepMonth++;
			}
			$numMonth++;
			if ($numMonth > 12) {
				$numMonth -= 12;
			}
		}

		return $numDepMonth;
	}
}
