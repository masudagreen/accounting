<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_LogImportListYayoi extends Code_Else_Plugin_Accounting_Jpn_LogImportList
{
	protected $_childSelf = array(
		'pathVarsYayoiConvert' => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/yayoiConvert.php',
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

	/**
	 *
	 */
	protected function _iniDetailAdd()
	{
		$this->_setDetailAdd();
	}

	/**
		(array(
			'vars' => $vars,
		))
	 */
	protected function _updateVarsRule($arr)
	{
		global $varsPluginAccountingAccount;

		$vars = $arr['vars'];


		$arrSubAccountTitle = $this->_getVarsSubAccountTitle(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrayFSList = $this->_getFSList(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrAccountTitle = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'numFiscalPeriod'    => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsYayoiConvert = $this->getVars(array(
			'path' => $this->_childSelf['pathVarsYayoiConvert'],
		));

		$varsTaxConvert = $this->_getVarsTaxConvert(array(
			'varsEntityNation' => $varsEntityNation,
			'varsYayoiConvert' => $varsYayoiConvert,
		));

		$vars['varsItem']['varsComment']['strStatus'] = $varsYayoiConvert['varsComment']['strStatus'];

		$vars['varsRule'] = array(
			'arrayFSList'        => $arrayFSList,
			'arrAccountTitle'    => $arrAccountTitle,
			'arrDepartment'      => $this->_getVarsDepartment(array(
				'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			)),
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'varsEntityNation'   => $varsEntityNation,
			'varsConsumptionTax' => $this->_getVarsConsumptionTax(array()),
			'varsFSItem'         => $this->_getVarsFSItem(),
			'varsTaxConvert'   => $varsTaxConvert,
			'varsYayoiConvert' => $varsYayoiConvert,
		);

		return $vars;
	}




	/**
		array(
			'varsItem'         => $arr['varsItem'],
			'varsEntityNation' => $vars['varsRule']['varsEntityNation'],
			'valueDetail'      => $valueDetail,
			'valueStr'         => $valueStr,
		)
	 */
	protected function _getVarsTaxConvert($arr)
	{
		$varsItem = array();

		$varsEntityNation = $arr['varsEntityNation'];

		$flagConsumptionTaxFree = (int) $varsEntityNation['flagConsumptionTaxFree'];
		$flagConsumptionTaxIncluding = (int) $varsEntityNation['flagConsumptionTaxIncluding'];
		$flagConsumptionTaxGeneralRule = (int) $varsEntityNation['flagConsumptionTaxGeneralRule'];
		$flagConsumptionTaxDeducted = (int) $varsEntityNation['flagConsumptionTaxDeducted'];
		$flagConsumptionTaxWithoutCalc = (int) $varsEntityNation['flagConsumptionTaxWithoutCalc'];

		if ($flagConsumptionTaxFree) {
			return array();
		}

		$varsStr = $arr['varsYayoiConvert']['varsStr'];
		$arrayRate = $varsStr['varsRate'];
		$arrayWithoutCalc = $varsStr['varsWithoutCalc'];
		$varsNew = array();

		$varsTaxList = $arr['varsYayoiConvert']['simple'];
		if ($flagConsumptionTaxGeneralRule) {
			if ($flagConsumptionTaxDeducted) {
				$varsTaxList = $arr['varsYayoiConvert']['generalEach'];
			} else {
				$varsTaxList = $arr['varsYayoiConvert']['generalProration'];
			}
		}

		if ($flagConsumptionTaxIncluding) {
			$flagConsumptionTaxWithoutCalc = 1;
			$array = $varsTaxList;
			foreach ($array as $key => $value) {
				$tmpl = $value;
				$tmpl['flagConsumptionTax'] = $tmpl['value'];
				$arrayTemp = array();
				$strTitle = $value['strYayoi'];
				$strTitleTmpl = $strTitle;

				if (preg_match( "/^tax/", $value['value'])) {
					$strTitle = str_replace('<>', $varsStr['strIncluding'], $strTitle);
					$strTitleTmpl = $strTitle;
				}

				if (preg_match( "/^tax/", $value['value']) || preg_match( "/^else/", $value['value'])) {
					$str = '';
					if (preg_match( "/^tax/", $value['value'])) {
						$str = 'tax';

					} else {
						if (preg_match( "/^else-TaxLocal$/", $value['value'])) {
							$str = 'else-TaxLocal';
						} else {
							$str = 'else';
						}
					}

					foreach ($arrayRate as $keyRate => $valueRate) {
						if ($keyRate == 5) {
							$strTitle = $strTitleTmpl;
							$strTitle = str_replace('[]', '', $strTitle);
							$row = $tmpl;
							$row['strYayoi'] = $strTitle;
							$row['numRateConsumptionTax'] = $keyRate;
							$row['flagConsumptionTaxWithoutCalc'] = $flagConsumptionTaxWithoutCalc;
							$varsNew[$row['strYayoi']] = $row;
						}
						$strTitle = $strTitleTmpl;
						$strTitle = str_replace('[]', $valueRate[$str], $strTitle);
						$row = $tmpl;
						$row['strYayoi'] = $strTitle;
						$row['numRateConsumptionTax'] = $keyRate;
						$row['flagConsumptionTaxWithoutCalc'] = $flagConsumptionTaxWithoutCalc;
						$varsNew[$row['strYayoi']] = $row;
						/*
						 * 20191001 start
						 */
						if ($keyRate == 8) {
						    $strTitle = $strTitleTmpl;
						    $strTitle = str_replace('[]',  $varsStr['varsRateConsumptionTaxReduced'][$str], $strTitle);
						    $row = $tmpl;
						    $row['strYayoi'] = $strTitle;
						    $row['numRateConsumptionTax'] = $keyRate . $varsStr['strRateConsumptionTaxReduced'];
						    $row['flagConsumptionTaxWithoutCalc'] = $flagConsumptionTaxWithoutCalc;
						    $varsNew[$row['strYayoi']] = $row;
						}
						/*
						 * 20191001 end
						 */
					}

				} else {
					$row = $tmpl;
					$row['strYayoi'] = $strTitle;
					$row['numRateConsumptionTax'] = '';
					$row['flagConsumptionTaxWithoutCalc'] = $flagConsumptionTaxWithoutCalc;
					$varsNew[$row['strYayoi']] = $row;
				}
			}

		} else {
			$array = $varsTaxList;
			foreach ($array as $key => $value) {
				$tmpl = $value;
				$tmpl['flagConsumptionTax'] = $tmpl['value'];
				$arrayTemp = array();
				$strTitle = $value['strYayoi'];
				$strTitleTmpl = $strTitle;

				if (preg_match( "/^tax/", $value['value'])) {
					$str = 'tax';
					foreach ($arrayWithoutCalc as $keyWithoutCalc => $valueWithoutCalc) {
						$strTitle = $strTitleTmpl;
						$strTitle = str_replace('<>', $valueWithoutCalc, $strTitle);
						$strTitleTmpl2 = $strTitle;
						foreach ($arrayRate as $keyRate => $valueRate) {
							if ($keyRate == 5) {
								$strTitle = $strTitleTmpl2;
								$strTitle = str_replace('[]', '', $strTitle);
								$row = $tmpl;
								$row['strYayoi'] = $strTitle;
								$row['numRateConsumptionTax'] = $keyRate;
								$row['flagConsumptionTaxWithoutCalc'] = $keyWithoutCalc;
								$varsNew[$row['strYayoi']] = $row;
							}
							$strTitle = $strTitleTmpl2;
							$strTitle = str_replace('[]', $valueRate[$str], $strTitle);
							$row = $tmpl;
							$row['strYayoi'] = $strTitle;
							$row['numRateConsumptionTax'] = $keyRate;
							$row['flagRateConsumptionTaxReduced'] = 0;
							$row['flagConsumptionTaxWithoutCalc'] = $keyWithoutCalc;
							$varsNew[$row['strYayoi']] = $row;
							/*
							 * 20191001 start
							 */
							if ($keyRate == 8) {
							    $strTitle = $strTitleTmpl2;
							    $strTitle = str_replace('[]', $varsStr['varsRateConsumptionTaxReduced'][$str], $strTitle);
							    $row = $tmpl;
							    $row['strYayoi'] = $strTitle;
							    $row['numRateConsumptionTax'] = $keyRate . $varsStr['strRateConsumptionTaxReduced'];
							    $row['flagConsumptionTaxWithoutCalc'] = $keyWithoutCalc;
							    $varsNew[$row['strYayoi']] = $row;
							}
							/*
							 * 20191001 end
							 */

						}
					}

				} elseif (preg_match( "/^else/", $value['value'])) {
					$str = '';
					if (preg_match( "/^else-TaxLocal$/", $value['value'])) {
						$str = 'else-TaxLocal';
					} else {
						$str = 'else';
					}

					foreach ($arrayRate as $keyRate => $valueRate) {
						if ($keyRate == 5) {
							$strTitle = $strTitleTmpl;
							$strTitle = str_replace('[]', '', $strTitle);
							$row = $tmpl;
							$row['strYayoi'] = $strTitle;
							$row['numRateConsumptionTax'] = $keyRate;
							$row['flagConsumptionTaxWithoutCalc'] = $flagConsumptionTaxWithoutCalc;
							$varsNew[$row['strYayoi']] = $row;
						}
						$strTitle = $strTitleTmpl;
						$strTitle = str_replace('[]', $valueRate[$str], $strTitle);
						$row = $tmpl;
						$row['strYayoi'] = $strTitle;
						$row['numRateConsumptionTax'] = $keyRate;
						$row['flagConsumptionTaxWithoutCalc'] = $flagConsumptionTaxWithoutCalc;
						$varsNew[$row['strYayoi']] = $row;
					}

				} else {
					$row = $tmpl;
					$row['strYayoi'] = $strTitle;
					$row['numRateConsumptionTax'] = '';
					$row['flagConsumptionTaxWithoutCalc'] = $flagConsumptionTaxWithoutCalc;
					$varsNew[$row['strYayoi']] = $row;
				}
			}
		}
		$strLost = $arr['varsYayoiConvert']['varsCheck']['strLost'];
		unset($varsNew[$strLost]);
//var_dump($varsNew);exit;
		return $varsNew;
	}


	/**
			'arrValueFile'                   => $arrValueFile,
			'vars'                           => $vars,
			'classCalcSubAccountTitleImport' => $classCalcSubAccountTitleImport,
			'classCalcDepartment'            => $classCalcDepartment,
	 */
	protected function _getArrayCSV($arr)
	{
		global $classFile;
		global $classEscape;

		$arrValueFile = $arr['arrValueFile'];

		$arrayCSV = $classFile->getArray(array(
			'path' => $arrValueFile['strUrl'],
		));

		$array = $arrayCSV;
		foreach ($array as $key => $value) {
			$arrayCSV[$key] = $classEscape->to(array('data' => $arrayCSV[$key]));
		}

		$arrTemp = array();
		$array = $arr['vars']['varsRule']['varsYayoiConvert']['varsId'];
		foreach ($array as $key => $value) {
			$arrTemp[] = $key;
		}
		$str = join(',', $arrTemp) . "\n";
		array_unshift($arrayCSV, $str);

		file_put_contents($arrValueFile['strUrl'], $arrayCSV);

		$arrayCSV = $classFile->getCsvRows(array(
			'path' => $arrValueFile['strUrl'],
		));

		$arrayCSV = $this->_updateYayoiArrayCSV(array(
			'vars'     => $arr['vars'],
			'arrayCSV' => $arrayCSV,
			'strUrl'   => $arrValueFile['strUrl'],
		));

		return $arrayCSV;
	}

	/**
弥生からRUCAROcsvに変換
	 */
	protected function _updateYayoiArrayCSV($arr)
	{
		global $classCheck;
		global $varsAccount;
		global $classFile;

		$flagConsumptionTaxFree = (int) $arr['vars']['varsRule']['varsEntityNation']['flagConsumptionTaxFree'];
		$flagConsumptionTaxIncluding = (int) $arr['vars']['varsRule']['varsEntityNation']['flagConsumptionTaxIncluding'];
		$flagConsumptionTaxGeneralRule = (int) $arr['vars']['varsRule']['varsEntityNation']['flagConsumptionTaxGeneralRule'];
		$flagConsumptionTaxDeducted = (int) $arr['vars']['varsRule']['varsEntityNation']['flagConsumptionTaxDeducted'];
		$flagConsumptionTaxCalc = (int) $arr['vars']['varsRule']['varsEntityNation']['flagConsumptionTaxCalc'];
		$flagConsumptionTaxWithoutCalcDefault = (int) $arr['vars']['varsRule']['varsEntityNation']['flagConsumptionTaxWithoutCalc'];

		$varsYayoiConvert =  $arr['vars']['varsRule']['varsYayoiConvert'];
		$varsTaxConvert =  $arr['vars']['varsRule']['varsTaxConvert'];
		$varsId = $varsYayoiConvert['varsId'];
		$varsComment = $varsYayoiConvert['varsComment'];
		$array = $arr['arrayCSV'];

		$numTemp = 0;
		$array = &$arr['arrayCSV'];
		foreach ($array as $key => $value) {
			if ($value['no'] == '') {
				$numTemp++;
			}
		}

		$flagBlank = 0;
		$numRow = 1;
		if ($numTemp == count($arr['arrayCSV'])) {
			$flagBlank = 1;
			foreach ($array as $key => $value) {
				$array[$key]['no'] = $numRow;
				$numRow++;
			}
		}

		if (!$flagBlank) {
			$numRow = 0;
			$array = $arr['arrayCSV'];
			foreach ($array as $key => $value) {
				$numRow++;

				$strStatus = $varsComment['strStatusRow'];
				$strStatus = str_replace("<%replace%>", $numRow, $strStatus);

				$keyId = 'no';
				if (!$value[$keyId]) {
					$this->_sendError(array('comment' => $strStatus . $varsId[$keyId] . $varsComment['strNo'], 'strUrl' => $arr['strUrl'],));
				}

				$flag = $classCheck->checkValueWord(array(
						'flagType' => 'num',
						'value'    => $value[$keyId]
				));
				if ($flag) {
					$this->_sendError(array('comment' => $strStatus . $varsId[$keyId] .$varsComment['strFormat'], 'strUrl' => $arr['strUrl'],));
				}
			}
		}

		$arrayStr = array('Debit', 'Credit');
		$arrayCsv = array();
		$arrTemp = array();
		$arrayId = $arr['vars']['varsItem']['varsId'];
		foreach ($arrayId as $key => $value) {
			$arrTemp[] = $key;
		}
		$arrayCsv[] = $arrTemp;

		$array = $arr['arrayCSV'];
		foreach ($array as $key => $value) {
			$arrTemp = array();

			//'id' => '識別番号',
			$keyId = 'no';
			$id = $value[$keyId];

			$strStatus = $varsComment['strStatus'];
			if ($flagBlank) {
				$strStatus = $varsComment['strStatusRow'];
			}
			$strStatus = str_replace("<%replace%>", $id, $strStatus);

			if ($value['flags'] == 2100 || $value['flags'] == 2101) {
				$id = $idPrev;
			}
			$arrTemp[] = $id;
			$idPrev = $id;

			//R: 'stampBook' => '取引日時',   Y : 'stampBook' => '取引日付',
			$keyId = 'stampBook';
			$strColumn = '';
			if ($value[$keyId]) {
				$strColumn = $value[$keyId];
			}
			$arrTemp[] = $strColumn;

			//R: 'flagFiscalReport' => '決算整理仕訳',空白 年決 中決,   Y : 'flagFiscalReport' => '決算' 空白 本決 中決,
			$keyId = 'flagFiscalReport';
			$strColumn = '';
			if ($value[$keyId]) {
				$strColumn = $varsYayoiConvert['varsCheck']['varsFlagFiscalReport'][$value[$keyId]];
				if (!$strColumn) {
					$strColumn = '';
				}
			}
			$arrTemp[] = $strColumn;

			//R: 'strTitle' => '摘要',   Y : 'strTitle' => '摘要',
			$keyId = 'strTitle';
			$strColumn = '';
			if ($value[$keyId]) {
				$strColumn = $value[$keyId];
			}
			$arrTemp[] = $strColumn;

			foreach ($arrayStr as $keyStr => $valueStr) {
				//R: 'idAccountTitleDebit' => '借方勘定科目',   Y : 'idAccountTitleDebit' => '借方勘定科目',
				$keyId = 'idAccountTitle' . $valueStr;
				$strColumn = '';
				if ($value[$keyId]) {
					$strColumn = $value[$keyId];
				}
				$arrTemp[] = $strColumn;

				//R: 'flagFSDebit' => '借方F/S',   Y :
				$arrTemp[] = '';

				//R: 'numValueDebit' => '借方金額',   Y : 'numValueDebit' => '借方金額',
				$keyId = 'numValue' . $valueStr;
				$strColumn = 0;
				if ($value[$keyId]) {
					$strColumn = $value[$keyId];
				}
				$arrTemp[] = $strColumn;

				//R: 'idSubAccountTitleDebit' => '借方補助科目',   Y : 'idSubAccountTitleDebit' => '借方補助科目',
				$keyId = 'idSubAccountTitle' . $valueStr;
				$strColumn = '';
				if ($value[$keyId] !== '') {
					$strColumn = $value[$keyId];
				}
				$arrTemp[] = $strColumn;

				//R: 'idDepartmentDebit' => '借方部門',   Y : 'idDepartmentDebit' => '借方部門',
				$keyId = 'idDepartment' . $valueStr;
				$strColumn = '';
				if ($value[$keyId]) {
					$strColumn = $value[$keyId];
				}
				$arrTemp[] = $strColumn;

				/*
				 * R:
				* 'flagConsumptionTaxDebit' => '借方消費税区分',
				* 'numRateConsumptionTaxDebit' => '借方消費税率',
				* 'flagConsumptionTaxWithoutCalcDebit' => '借方消費税入力方法',
				*
				* Y : 'flagConsumptionTaxDebit' => '借方税区分',
				*
				*  区分が不明のときエラー
				*/
				$strLost = $varsYayoiConvert['varsCheck']['strLost'];
				$keyId = 'flagConsumptionTax' . $valueStr;
				$strColumn = '';
				if ($value[$keyId] && !$flagConsumptionTaxFree) {
					if (!$varsTaxConvert[$value[$keyId]]) {
						if ($value[$keyId] == $strLost) {
							$this->_sendError(array('comment' => $strStatus . $varsId[$keyId] . $varsComment['strFlagConsumptionTaxLost'], 'strUrl' => $arr['strUrl'],));
						} else {
							$this->_sendError(array('comment' => $strStatus . $varsId[$keyId] . $varsComment['strFlagConsumptionTax'], 'strUrl' => $arr['strUrl'],));
						}
					}
					$strColumn = $value[$keyId];
					$arrTemp[] = $varsTaxConvert[$value[$keyId]]['strTitle'];
					$arrTemp[] = $varsTaxConvert[$value[$keyId]]['numRateConsumptionTax'];
					/*
					 * 20191001 start
					 */
					//$arrTemp[] = $varsTaxConvert[$value[$keyId]]['flagRateConsumptionTaxReduced'];
					/*
					 * 20191001 end
					 */
					$flagConsumptionTaxWithoutCalc = $varsTaxConvert[$value[$keyId]]['flagConsumptionTaxWithoutCalc'];
					$arrTemp[] = $varsYayoiConvert['varsStr']['varsWithoutCalc2'][$flagConsumptionTaxWithoutCalc];

				} else {
					$arrTemp[] = '';
					$arrTemp[] = '';
					$arrTemp[] = '';
				}

				//R: 'numValueConsumptionTaxDebit' => '借方消費税金額',   Y : 'numValueConsumptionTaxDebit' => '借方消費税金額',
				$keyId = 'numValueConsumptionTax' . $valueStr;
				$strColumn = 0;
				if ($value[$keyId]) {
					$strColumn = $value[$keyId];
				}
				$arrTemp[] = $strColumn;
			}

			//R: 'idAccount' => '担当者',
			$strColumn = $varsAccount['strCodeName'];
			$arrTemp[] = $strColumn;

			//R: 'arrSpaceStrTag' => 'タグ',
			$strColumn = '';
			if ($value['no']) {
				$strColumn = $varsId['no'] . $value['no'];
				if ($flagBlank) {
					$strColumn = '';
				}
			}
			$arrTemp[] = $strColumn;
			$arrayCsv[] = $arrTemp;
		}

		$text = $classFile->getCsvText(array(
			'delimiter' => ',',
			'rows'      => $arrayCsv,
		));

		file_put_contents($arr['strUrl'], $text);

		$arrayCSV = $classFile->getCsvRows(array(
			'path' => $arr['strUrl'],
		));

		return $arrayCSV;
	}




}
