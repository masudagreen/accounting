<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_ExportLog extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference' => 'exportLogWindow',
		'pathTplJs' => 'else/plugin/accounting/js/jpn/exportLog.js',
		'pathVarsJs'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/exportLog.php',
		'varsIframe' => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/tmplIframe.php',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;
		global $classCheck;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'select',
			'idTarget'      => $this->_extSelf['idPreference'],
		));

		if (!$flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}

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
	protected function _iniJs()
	{
		global $classSmarty;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllOutput'])) {
			$vars['portal']['varsNavi']['varsBtn'] = array();
		}

		$json = json_encode($vars);
		$classSmarty->assign('varsLoad', $json);
		$classSmarty->assign('numNews', $this->getNumNews());
		$contents = $classSmarty->fetch($this->_extSelf['pathTplJs']);

		$this->sendJs(array(
			'data' => $contents,
		));
	}

	/**

	 */
	protected function _getLog()
	{
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingAccountsEntity;
		global $varsAccount;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$flagBookCurrent = $varsPluginAccountingAccountsEntity[$varsAccount['id']][$idEntity]['flagBookCurrent'];
		if ($flagBookCurrent == 'Ifrs') {
			$flagIfrs = 1;
			$flagJgaap = 0;

		} else {
			$flagIfrs = 0;
			$flagJgaap = 1;
		}

		$rows = $this->getSearch(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingLog',
			'arrOrder'  => array(
				'strColumn' => 'idLog',
				'flagDesc'  => 1,
			),
			'flagAnd'  => 1,
			'arrWhere'  => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idEntity',
					'flagCondition' => 'eq',
					'value'         => $idEntity,
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'numFiscalPeriod',
					'flagCondition' => 'eq',
					'value'         => $numFiscalPeriod,
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'flagIfrs',
					'flagCondition' => 'eq',
					'value'         => $flagIfrs,
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'flagJgaap',
					'flagCondition' => 'eq',
					'value'         => $flagJgaap,
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'flagRemove',
					'flagCondition' => 'eq',
					'value'         => 0,
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'flagApply',
					'flagCondition' => 'eq',
					'value'         => 0,
				),
			),
		));

		return $rows;
	}



	/**
	 *
	 */
	protected function _iniNaviReload()
	{
		global $varsRequest;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$numLotNow = (int) $varsRequest['query']['jsonValue']['vars']['NumLotNow'];

		$rows = $this->_getLog();

		$this->sendVars(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'numRows'    => $rows['numRows'],
				'varsDetail' => $vars['portal']['varsNavi']['varsDetail'],
			),
		));
	}

	/**
	 *
	 */
	protected function _iniNaviOutput()
	{
		global $classDb;
		global $classTime;
		global $classRequest;

		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;
		global $varsRequest;

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'output',
			'idTarget'      => $this->_extSelf['idPreference'],
		));

		if (!$flag) {
			$this->_sendMessage(array('flag' => 40));
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$vars = $this->_updateVarsRule(array(
			'vars' => $vars,
		));

		$rows = $this->_getLog();

		$text = $this->_getCsv(array(
			'vars' => $vars,
			'rows' => $rows,
		));
		$text = mb_convert_encoding($text, 'sjis', 'utf8');

		$strFileName = $this->_getFileTitle(array(
			'strMenu'     => $vars['varsItem']['varsMenu']['strList'],
			'strFileType' => 'csv',
		));

		$classRequest->output(array(
			'text'         => $text,
			'strFileType'  => 'csv',
			'strFileName'  => $strFileName,
		));

	}

	/**
		array(
			'vars'      => $vars,
			'arrDetail' => $value['jsonDetail']['varsDetail'],
		)
	 */
	protected function _getCsvJsonDetail($arr)
	{
		$vars = &$arr['vars'];
		$flagConsumptionTaxFree = (int) $vars['varsRule']['varsEntityNation']['flagConsumptionTaxFree'];
		$flagConsumptionTaxIncluding = (int) $vars['varsRule']['varsEntityNation']['flagConsumptionTaxIncluding'];
		$flagConsumptionTaxGeneralRule = (int) $vars['varsRule']['varsEntityNation']['flagConsumptionTaxGeneralRule'];
		$flagConsumptionTaxDeducted = (int) $vars['varsRule']['varsEntityNation']['flagConsumptionTaxDeducted'];
		$flagConsumptionTaxCalc = (int) $vars['varsRule']['varsEntityNation']['flagConsumptionTaxCalc'];
		$numConsumptionTax = (int) $vars['varsRule']['varsEntityNation']['numConsumptionTax'];

		$arrDebit = array();
		$arrCredit = array();
		$arrDetail = array();
		$array = $arr['arrDetail'];

		foreach ($array as $key => $value) {
			if ($value['arrDebit']['idAccountTitle'] != '') {
				$arrDebit[] = $value['arrDebit'];
			}
			if ($value['arrCredit']['idAccountTitle'] != '') {
				$arrCredit[] = $value['arrCredit'];
			}
		}
		foreach ($array as $key => $value) {
			$varsDetail = $vars['varsItem']['varsDetail'];
			if ($arrDebit[$key]) {
				$varsDetail['arrDebit'] = $arrDebit[$key];
			}
			if ($arrCredit[$key]) {
				$varsDetail['arrCredit'] = $arrCredit[$key];
			}
			if ($varsDetail['arrDebit']['idAccountTitle'] == ''
				&& $varsDetail['arrCredit']['idAccountTitle'] == ''
			) {
				continue;
			}
			$arrDetail[] = $varsDetail;
		}

		$arrayNew = array();
		$array = $arrDetail;
		foreach ($array as $key => $value) {
			$data = array();
			$arrayStr = array('Debit', 'Credit');
			foreach ($arrayStr as $keyStr => $valueStr) {
				$str = 'arr' . $valueStr;
				$idAccountTitle = $value[$str]['idAccountTitle'];
				if ($idAccountTitle != '') {

					$data['idAccountTitle' . $valueStr] = $vars['varsRule']['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['strTitle'];
					$data['flagFS' . $valueStr] = $vars['varsRule']['arrAccountTitle']['arrStrTitle'][$idAccountTitle]['flagFS'];

					$data['numValue' . $valueStr] = $value[$str]['numValue'];

					$data['idSubAccountTitle' . $valueStr] = '';
					if ($value[$str]['idSubAccountTitle'] != '') {
						$data['idSubAccountTitle' . $valueStr] = $vars['varsRule']['arrSubAccountTitle']['arrStrTitle'][$idAccountTitle][$value[$str]['idSubAccountTitle']]['strTitle'];
					}

					$data['idDepartment' . $valueStr] = '';
					if ($value[$str]['idDepartment'] != '') {
						$data['idDepartment' . $valueStr] = $vars['varsRule']['arrDepartment']['arrStrTitle'][$value[$str]['idDepartment']]['strTitle'];
					}

					$data['numValueConsumptionTax' . $valueStr] = 0;
					$data['flagConsumptionTax' . $valueStr] = '';
					$data['flagConsumptionTaxWithoutCalc' . $valueStr] = '';
					$flagTax = 0;
					if (!$flagConsumptionTaxFree) {
						if ($flagConsumptionTaxGeneralRule) {
							if ($flagConsumptionTaxDeducted) {
								$data['flagConsumptionTax' . $valueStr] = $vars['varsRule']['varsConsumptionTax']['arrStrGeneralEach'][$value[$str]['flagConsumptionTaxGeneralRuleEach']];

							} else {
								$data['flagConsumptionTax' . $valueStr] = $vars['varsRule']['varsConsumptionTax']['arrStrGeneralProration'][$value[$str]['flagConsumptionTaxGeneralRuleProration']];
							}

						} else {
							$data['flagConsumptionTax' . $valueStr] = $vars['varsRule']['varsConsumptionTax']['arrStrSimple'][$value[$str]['flagConsumptionTaxSimpleRule']];
						}

						if (!$flagConsumptionTaxIncluding) {
							if ($flagConsumptionTaxGeneralRule) {
								if ($flagConsumptionTaxDeducted) {
									if (preg_match( "/^tax/", $value[$str]['flagConsumptionTaxGeneralRuleEach'])) {
										$flagTax = 1;
									}

								} else {
									if (preg_match( "/^tax/", $value[$str]['flagConsumptionTaxGeneralRuleProration'])) {
										$flagTax = 1;
									}
								}

							} else {
								if (preg_match( "/^tax/", $value[$str]['flagConsumptionTaxSimpleRule'])) {
									$flagTax = 1;
								}
							}
							if ($flagTax) {
								$data['flagConsumptionTaxWithoutCalc' . $valueStr] = $vars['varsRule']['varsConsumptionTax']['arrStrWithoutCalc'][$value[$str]['flagConsumptionTaxWithoutCalc']];
							}
							if ($idAccountTitle == 'suspenseReceiptOfConsumptionTaxes' || $idAccountTitle == 'suspensePaymentConsumptionTaxes') {
								$flagTax = 0;
								$data['flagConsumptionTaxWithoutCalc' . $valueStr] = '';
							}
						}
					}

					if ($flagTax) {
						$numValue =  $value[$str]['numValue'];
						$numValueConsumptionTax =  $value[$str]['numValueConsumptionTax'];
						$flagConsumptionTaxWithoutCalc = $value[$str]['flagConsumptionTaxWithoutCalc'];

						if ($flagConsumptionTaxWithoutCalc == 1) {
							$data['numValueConsumptionTax' . $valueStr] = $numValueConsumptionTax;

						} elseif ($flagConsumptionTaxWithoutCalc == 2) {
							$data['numValueConsumptionTax' . $valueStr] = $numValueConsumptionTax;
							$data['numValue' . $valueStr] = $value[$str]['numValue'] + $numValueConsumptionTax;
						}
					}

				} else {
					$data['idAccountTitle' . $valueStr] = '';
					$data['flagFS' . $valueStr] = '';
					$data['numValue' . $valueStr] = 0;
					$data['numValueConsumptionTax' . $valueStr] = 0;
					$data['idSubAccountTitle' . $valueStr] = '';
					$data['idDepartment' . $valueStr] = '';
					$data['flagConsumptionTax' . $valueStr] = '';
					$data['flagConsumptionTaxWithoutCalc' . $valueStr] = '';
				}
			}
			$arrayNew[] = $data;
		}

		return $arrayNew;
	}

	/**
		array(
			'vars' => $vars,
			'rows' => $rows,
		)
	 */
	protected function _getCsv($arr)
	{
		global $classTime;
		global $classFile;

		global $varsRequest;
		global $varsAccounts;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingAccountsId;

		$vars = &$arr['vars'];
		$varsId = $arr['vars']['varsItem']['varsId'];

		//id
		$arrayCsv = array();
		$rowCsv = array();
		$array = $varsId;
		foreach ($array as $key => $value) {
			$rowCsv[] = $value;
		}
		$arrayCsv[] = $rowCsv;

		if (!(int) $arr['rows']['numRows']) {
			$text = $classFile->getCsvText(array(
				'delimiter' => ',',
				'rows'      => $arrayCsv,
			));

			return $text;
		}

		$array = $arr['rows']['arrRows'];
		$num = 1;
		$flag = 0;
		foreach ($array as $key => $value) {
			$varsVersion = end($value['jsonVersion']);

			$dataDetail = $this->_getCsvJsonDetail(array(
				'vars'      => $vars,
				'arrDetail' => $varsVersion['jsonDetail']['varsDetail'],
			));

			$arrayDetail = $dataDetail;
			foreach ($arrayDetail as $keyDetail => $valueDetail) {
				$rowCsv = array();

				$rowCsv[] = $num;

				$rowCsv[] = $classTime->getDisplay(array(
					'flagType' => 'yearmin',
					'stamp'    => $value['stampBook'],
				));

				if (preg_match("/^f1$/", $value['flagFiscalReport'])) {
					$rowCsv[] = 1;

				} elseif (preg_match("/^f21$/", $value['flagFiscalReport'])) {
					$rowCsv[] = 2;

				} else {
					$rowCsv[] = 0;
				}

				$rowCsv[] = str_replace(',', $vars['varsItem']['strEscape'], $value['strTitle']);

				$arrayStr = array('Debit', 'Credit');
				foreach ($arrayStr as $keyStr => $valueStr) {

					$rowCsv[] = str_replace(',', $vars['varsItem']['strEscape'], $valueDetail['idAccountTitle' . $valueStr]);

					$rowCsv[] = str_replace(',', $vars['varsItem']['strEscape'], $valueDetail['flagFS' . $valueStr]);

					$rowCsv[] = $valueDetail['numValue' . $valueStr];

					$rowCsv[] = str_replace(',', $vars['varsItem']['strEscape'], $valueDetail['idSubAccountTitle' . $valueStr]);

					$rowCsv[] = str_replace(',', $vars['varsItem']['strEscape'], $valueDetail['idDepartment' . $valueStr]);

					$rowCsv[] = $valueDetail['flagConsumptionTax' . $valueStr];

					$rowCsv[] = $valueDetail['flagConsumptionTaxWithoutCalc' . $valueStr];

					$rowCsv[] = $valueDetail['numValueConsumptionTax' . $valueStr];
				}

				$idAccount = $varsAccounts[$value['idAccount']]['strCodeName'];
				if (!$idAccount) {
					$idAccount = $varsPluginAccountingAccountsId[$value['idAccount']]['strCodeName'];
				}
				$rowCsv[] = str_replace(',', $vars['varsItem']['strEscape'], $idAccount);

				$value['arrSpaceStrTag'] = str_replace(',', $vars['varsItem']['strEscape'], $value['arrSpaceStrTag']);
				$rowCsv[] = preg_replace('/^ /', '', $value['arrSpaceStrTag']);

				$arrayCsv[] = $rowCsv;
			}
			$num++;
		}

		$text = $classFile->getCsvText(array(
			'delimiter' => ',',
			'rows'      => $arrayCsv,
		));

		return $text;
	}

	/**
		(array(
			'vars' => $vars,
		))
	 */
	protected function _updateVarsRule($arr)
	{
		global $varsPluginAccountingPreference;
		global $varsPluginAccountingAccount;

		$vars = $arr['vars'];
		$arrSubAccountTitle = $this->_getVarsSubAccountTitle(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrAccountTitle = $this->_getAccountTitle(array(
			'vars'               => $this->_getVarsFSItem(),
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'numFiscalPeriod'    => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$vars['varsRule'] = array(
			'arrAccountTitle'    => $arrAccountTitle,
			'arrDepartment'      => $this->_getVarsDepartment(array(
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			)),
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'varsEntityNation'   => $varsEntityNation,
			'varsConsumptionTax' => $this->_getVarsConsumptionTax(array()),
			'varsFSItem'         => $this->_getVarsFSItem(),
		);

		return $vars;
	}


}
