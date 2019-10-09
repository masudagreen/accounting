<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_2012_DetailedAccount_NotesPayableEditor extends Code_Else_Plugin_Accounting_Jpn_2012_DetailedAccount_NotesPayable
{
	protected $_childSelf = array(
		'pathTplJs'   => 'else/plugin/accounting/js/jpn/2012/detailedAccount/notesPayableEditor.js',
		'pathVarsJs'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/2012/detailedAccount/notesPayableEditor.php',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;

		$flag = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done|tempNext)$/", $flag)) {
			$this->_sendOld();
		}

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
	protected function _iniJs()
	{
		$this->_setJsEditor(array(
			'pathVars'  => $this->_childSelf['pathVarsJs'],
			'pathTpl'   => $this->_childSelf['pathTplJs'],
			'arrFolder' => array(),
		));
	}

	protected function _getVarsValue($arr)
	{
		$flagMenu = $arr['varsFlag']['flagMenu'];
		$method = '_getVarsValue' . ucwords($flagMenu);

		$data = $this->$method(array(
			'varsFlag'  => $arr['varsFlag'],
			'arrValue'  => $arr['arrValue'],
			'vars'      => $arr['vars'],
			'varsItem'  => $arr['varsItem'],
		));

		return $data;
	}

	/**

	 */
	protected function _getVarsValueDetail($arr)
	{
		global $classCheck;
		global $varsPluginAccountingAccount;

		$flagMenu = $arr['varsFlag']['flagMenu'];

		$varsFSValue = $this->_getVarsFSValue(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));
		$arrayFSList = $this->_getFSList(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsValue = array();
		$varsData = $arr['arrValue']['arr']['jsonData'][$flagMenu];

		$flagSend = 1;
		$arrStrTitle = $arr['varsItem']['arrAccountTitle']['arrStrTitle'];
		$arrStrTitle['none']['strTitleFS'] = 'dummy';
		$arrSubStrTitle = $arr['varsItem']['arrSubAccountTitle']['arrStrTitle'];
		$flagDebit = $arr['varsItem']['varsCommon']['varsStr'][$flagMenu]['flagDebit'];

		$varsTmpl = array();
		$array = $arr['varsItem']['varsCommon']['varsTmpl'][$flagMenu];
		foreach ($array as $key => $value) {
			$varsTmpl[$value['id']] = $value;
		}

		$numEnd = $arr['varsItem']['varsCommon']['varsStr'][$flagMenu]['numRows'];
		for ($i = 1; $i <= $numEnd; $i++) {
			$idAccountTitle = $varsData['valueSelectIdAccountTitle' . $i];
			$strAccountTitle = $arrStrTitle[$idAccountTitle]['strTitleFS'];
			if (is_null($strAccountTitle) || $idAccountTitle == 'none') {
				continue;
			}
			$arrSubStrTitle[$idAccountTitle]['0']['strTitle'] = 'dummy';
			$idSubAccountTitle = $varsData['valueSelectIdSubAccountTitle' . $i];
			$strSubAccountTitle = $arrSubStrTitle[$idAccountTitle][$idSubAccountTitle]['strTitle'];
			if (is_null($strSubAccountTitle)) {
				continue;
			}

			if ($idSubAccountTitle != 0) {
				$varsValue['valueTextMemo' . $i] = mb_substr($strSubAccountTitle, 0, $varsTmpl['TextMemo']['numMaxlength']);
			}

			$arrId = array();
			if ($idSubAccountTitle == 0) {
				$sumValue = 0;
				$array = $arrayFSList;
				foreach ($array as $key => $value) {
					if ($varsFSValue['jsonJgaapAccountTitle' . $key]['f1'][$idAccountTitle]) {
						$numNext = $varsFSValue['jsonJgaapAccountTitle' . $key]['f1'][$idAccountTitle]['sumNext'];
						$sumValue = $numNext;
						break;
					}
				}
				$sumSubValue = 0;
				$arrId = $arrSubStrTitle[$idAccountTitle];
				if ($arrId) {
					$sumSubValue = $this->_getVarsSubValue(array(
						'arrId'           => $arrId,
						'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					));
				}
				$num = $sumValue - $sumSubValue;
				if ($flagDebit != $arrStrTitle[$idAccountTitle]['flagDebit']) {
					$num *= -1;
				}

				$flag = $classCheck->checkValueMax(array(
					'flagType' => 'str',
					'value'    => $num,
					'num'      => $varsTmpl['TextValue']['numMaxlength'],
				));
				if ($flag) {
					$flagSend = 'strOver';
					continue;
				}

				if ($varsTmpl['TextValue']['flagValueType'] == 'num' && $num == 0) {
					$num = '';
				}
				$varsValue['valueTextValue' . $i] = $num;
				continue;
			}
			$arrId[$idSubAccountTitle] = 1;
			$sumSubValue = $this->_getVarsSubValue(array(
				'arrId'           => $arrId,
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			));
			$num = $sumSubValue;
			if ($flagDebit != $arrStrTitle[$idAccountTitle]['flagDebit']) {
				$num *= -1;
			}

			$flag = $classCheck->checkValueMax(array(
				'flagType' => 'str',
				'value'    => $num,
				'num'      => $varsTmpl['TextValue']['numMaxlength'],
			));

			if ($flag) {
				$flagSend = 'strOver';

			} else {
				if ($varsTmpl['TextValue']['flagValueType'] == 'num' && $num == 0) {
					$num = '';
				}
				$varsValue['valueTextValue' . $i] = $num;
			}

		}

		$data = array(
			'flag'      => $flagSend,
			'varsValue' => $varsValue,
		);

		return $data;
	}

	/**
	 *
	 */
	protected function _iniDetailCalc()
	{
		global $varsRequest;
		global $varsPluginAccountingAccount;


		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'])) {
			$this->_sendOld();
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsFlag = array(
			'flagMenu' => $varsRequest['query']['jsonValue']['vars']['VarsFlag']['flagMenu'],
			'numPage' => $varsRequest['query']['jsonValue']['vars']['VarsFlag']['numPage'],
		);

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$this->_checkValueDetail(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
		));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		));

		$arrValue = $this->_checkValueDetailSave(array(
			'varsItem' => $varsItem,
			'varsList' => $vars['varsItem']['varsList'],
			'varsData' => $varsRequest['query']['jsonValue']['vars']['JsonData'],
			'varsFlag' => $varsFlag,
		));

		$data = $this->_getVarsValue(array(
			'vars'      => $vars,
			'varsItem'  => $varsItem,
			'varsList'  => $vars['varsItem']['varsList'],
			'varsFlag'  => $varsFlag,
			'arrValue'  => $arrValue,
		));

		$this->sendVars(array(
			'flag'    => $data['flag'],
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'varsValue' => $data['varsValue'],
			),
		));
	}

	/**

	 */
	protected function _iniDetailEdit()
	{
		global $classDb;
		global $classEscape;

		global $varsRequest;
		global $varsAccount;
		$dbh = $classDb->getHandle();
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllUpdate'])) {
			$this->_sendOld();
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsFlag = array(
			'flagMenu' => $varsRequest['query']['jsonValue']['vars']['VarsFlag']['flagMenu'],
			'numPage' => $varsRequest['query']['jsonValue']['vars']['VarsFlag']['numPage'],
		);

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$this->_checkValueDetail(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
		));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		));

		$arrValue = $this->_checkValueDetailSave(array(
			'varsItem' => $varsItem,
			'varsList' => $vars['varsItem']['varsList'],
			'varsData' => $varsRequest['query']['jsonValue']['vars']['JsonData'],
			'varsFlag' => $varsFlag,
		));

		try {
			$dbh->beginTransaction();

			$method = '_updateDb' . ucwords($varsFlag['flagMenu']);
			if (method_exists($this, $method)) {
				$this->$method(array(
					'vars'     => $vars,
					'varsItem' => $varsItem,
					'arrValue' => $arrValue,
					'varsFlag' => $varsFlag,
				));
			}

			$this->_updateDbPage(array(
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				'flagReport'      => $this->_extSelf['flagReport'],
				'flagDetail'      => $this->_extSelf['flagDetail'],
				'flagMenu'        => 'detail',
				'numRows'         => $varsItem['varsCommon']['varsStr']['detail']['numRows']
			));

			$this->_updateDbPreferenceStamp(array('strColumn' => 'detailedAccount'));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
		)));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		));

		$this->_sendData(array(
			'flag'     => 1,
			'vars'    => array(
				'varsNavi'           => $vars['portal']['varsNavi']['varsDetail'],
				'arrAccountTitle'    => $vars['varsItem']['arrAccountTitle'],
				'arrSubAccountTitle' => $vars['varsItem']['arrSubAccountTitle'],
				'varsPreference'     => $vars['varsItem']['varsPreference'],
				'varsDetail'         => $vars['portal']['varsDetail'],
				'varsFlag'           => $varsFlag,
				'varsIni'            => $vars['varsItem']['varsIni'],
				'varsList'           => $vars['varsItem']['varsList'],
				'flagBtnCalc'        => $vars['varsItem']['flagBtnCalc'],
			),
		));
	}




	/**
			'varsList' => $vars['varsItem']['varsList'],
			'varsData' => $varsRequest['query']['jsonValue']['vars']['JsonData'],
			'varsFlag' => $varsFlag,
	 */
	protected function _checkValueDetailSave($arr)
	{
		global $varsPluginAccountingAccount;

		$this->_checkVarsValueDoublePage(array(
			'varsItem' => $arr['varsItem'],
			'varsFlag' => $arr['varsFlag'],
			'varsData' => $arr['varsData'],
		));

		$arrStrTitle = $arr['varsItem']['arrAccountTitle']['arrStrTitle'];
		$arrStrTitle['none']['strTitleFS'] = 'dummy';
		$arrSubStrTitle = $arr['varsItem']['arrSubAccountTitle']['arrStrTitle'];

		$arrayCheck = array();
		$array = $arr['varsList'];
		foreach ($array as $key => $value) {
			$arrayCheck[$value['idTarget']] = $value;
		}
		$numAll = count($arrayCheck);
		$varsValue = array();
		$array = $arr['varsData'];

		foreach ($array as $key => $value) {
			$data = $arrayCheck[$key];
			if (is_null($data)) {
				$this->_sendOld();
			}
			$data['value'] = $value;
			$numAll--;
			$dataValue = $this->checkValue(array(
				'values' => array($data),
			));
			if ($data['flagTag'] == 'select') {
				$flag = 0;
				if (preg_match("/^SelectIdAccountTitle/", $data['id'])) {
					$str = $arrStrTitle[$data['value']]['strTitleFS'];
					if (!is_null($str)) {
						$flag = 1;
					}

				} elseif (preg_match("/^SelectIdSubAccountTitle(.*?)$/", $data['id'], $arrMatch)) {
					list($dummy, $idNum) = $arrMatch;
					$strIdAccountTitle = 'valueSelectIdAccountTitle' . $idNum;
					$idAccountTitle = $arr['varsData'][$strIdAccountTitle];
					$str = $arrStrTitle[$idAccountTitle]['strTitleFS'];
					if ($str) {
						$arrSubStrTitle[$idAccountTitle]['0']['strTitle'] = 'dummy';
						$str = $arrSubStrTitle[$idAccountTitle][$data['value']]['strTitle'];
						if (!is_null($str)) {
							$flag = 1;
						}
					}

				} else {
					$arrayOption = $data['arrayOption'];
					foreach ($arrayOption as $keyOption => $valueOption) {
						if ($data['value'] == $valueOption['value']) {
							$flag = 1;
							break;
						}
					}
				}
				if (!$flag) {
					$this->_sendOld();
				}

			}
			if ($data['flagForm']) {
				$varsValue[$key] = $value;
			}
		}

		if ($numAll != 0) {
			$this->_sendOld();
		}

		if (preg_match("/^detail/", $arr['varsFlag']['flagMenu'])) {
			$varsData = $arr['varsItem']['varsSave']['jsonData'];
		} else {
			$varsData = $arr['varsItem']['varsPreference']['jsonData'];
		}

		if (!$varsData) {
			$varsData = array();
		}

		$varsData[$arr['varsFlag']['flagMenu']] = $varsValue;
		$arrValue['arr']['jsonData'] = $varsData;

		return $arrValue;
	}

	/**

	 */
	protected function _updateDbDetail($arr)
	{
		global $classEscape;
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsRequest;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$stampRegister = TIMESTAMP;
		$stampUpdate = TIMESTAMP;
		$flagReport = $this->_extSelf['flagReport'];
		$flagDetail = $this->_extSelf['flagDetail'];

		$flagMenu = $arr['varsFlag']['flagMenu'];
		$numPage = $arr['varsFlag']['numPage'];

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];

		$jsonData = json_encode($arr['arrValue']['arr']['jsonData']);

		$arrColumn = array(
			'stampRegister',
			'stampUpdate',
			'idEntity',
			'numFiscalPeriod',
			'flagReport',
			'flagDetail',
			'numPage',
			'jsonData',
		);
		$arrValue = array(
			$stampRegister,
			$stampUpdate,
			$idEntity,
			$numFiscalPeriod,
			$flagReport,
			$flagDetail,
			$numPage,
			$jsonData,
		);
		if (is_null($arr['varsItem']['varsPreference']['id'])) {
			$numPage = 0;
			$varsData = array();
			$varsData['numPageMax'] = 1;
			$jsonData = json_encode($varsData);

			$arrValuePreference = array(
				$stampRegister,
				$stampUpdate,
				$idEntity,
				$numFiscalPeriod,
				$flagReport,
				$flagDetail,
				$numPage,
				$jsonData,
			);
			$id = $classDb->insertRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingDetailedAccount' . $strNation,
				'arrColumn' => $arrColumn,
				'arrValue'  => $arrValuePreference,
			));
		}

		//update
		if ($arr['varsItem']['varsSave']) {
			$arrColumn = array(
				'stampUpdate',
				'jsonData',
			);
			$arrValue = array(
				$stampUpdate,
				$jsonData,
			);
			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingDetailedAccount' . $strNation,
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
						'flagType'      => '',
						'strColumn'     => 'flagReport',
						'flagCondition' => 'eq',
						'value'         => $flagReport,
					),
					array(
						'flagType'      => '',
						'strColumn'     => 'flagDetail',
						'flagCondition' => 'eq',
						'value'         => $flagDetail,
					),
					array(
						'flagType'      => 'num',
						'strColumn'     => 'numPage',
						'flagCondition' => 'eq',
						'value'         => $numPage,
					),
				),
				'arrValue'  => $arrValue,
			));

		//insert
		} else {
			$id = $classDb->insertRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingDetailedAccount' . $strNation,
				'arrColumn' => $arrColumn,
				'arrValue'  => $arrValue,
			));
		}


	}

}
