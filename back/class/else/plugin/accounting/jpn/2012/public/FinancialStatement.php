<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_FinancialStatement_2012_Public extends Code_Else_Plugin_Accounting_Jpn_FinancialStatement
{

	protected $_extSelf = array(
		'idPreference' => 'financialStatementWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/financialStatement.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/2012/public/financialStatement.php',
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

		if ($varsRequest['query']['child']) {
			$this->_checkCorporationClass(array('flagChild' => 1));

		} else {
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
		}
		exit;
	}

	/**
		(array(
			'vars'        => $arr['vars'],
			'varsFS'      => $varsFS,
			'varsFSValue' => $arr['varsItem']['varsFSValue']['jsonJgaapFS' . $arr['varsFlag']['flagFS']],
			'varsFlag'    => $arr['varsFlag'],
		))

	 */
	protected function _getAccountTitleValueColumn($arr)
	{
		$array = &$arr['varsFS'];

		$flagUnit = (int) $arr['varsFlag']['flagUnit'];
		$flagFiscalPeriod = $arr['varsFlag']['flagFiscalPeriod'];
		$flagCalc = $arr['varsFlag']['flagCalc'];
		$varsFSValue = $arr['varsFSValue'];

		foreach ($array as $key => $value) {
			$array[$key]['varsColumnDetail']['numValue'] = '';
			$array[$key]['flagBtnUse'] = 1;
			$array[$key]['varsPrint']['strTitle'] = $value['strTitle'];
			$array[$key]['varsPrint']['sumNext'] = '';
			$array[$key]['varsPrint']['sumPrev'] = '';
			$array[$key]['varsPrint']['flagHide'] = 0;

			if (!is_null($value['vars']['flagUse'])) {
				//flagUse
				if ((int) $value['vars']['flagUse']) {

				} else {
					$array[$key]['varsPrint']['flagHide'] = 1;
					$array[$key]['strClassFont'] = $arr['vars']['varsItem']['strClassNone'];
				}
			}

			if (!is_null($array[$key]['vars']['varsValue'])) {
				$idTarget = $value['vars']['idTarget'];
				$arrayStr = array('sumPrev', 'sumNext');
				foreach ($arrayStr as $keyStr => $valueStr) {
					$numData = $varsFSValue[$flagFiscalPeriod][$idTarget][$valueStr];
					if (!is_null($numData)) {
						$numData =  $numData;
						if ($flagUnit == 0) {
							$numValue = $numData;

						} else {
							if ($flagCalc == 'floor') {
								$numValue = floor($numData / $flagUnit);

							} elseif ($flagCalc == 'round') {
								$numValue = round($numData / $flagUnit);

							} elseif ($flagCalc == 'ceil') {
								$numValue = ceil($numData / $flagUnit);
							}

						}
					} else {
						$numValue = 0;
					}
					$flag = 0;
					if ($arr['varsFlag']['flagFS'] == 'BS' && $arr['varsFlag']['flagFiscalPeriod'] == 'f1') {
						if ($valueStr == 'sumPrev') {
							if ($idTarget == 'accountsReceivables'
								|| $idTarget == 'accountsPayables'
								|| $idTarget == 'unappropriatedRetainedEarnings'
							) {
								$flag = 1;
							}
						}
					}
					if ($flag) {
						$array[$key]['vars']['varsValue'][$valueStr] = '-';
						$array[$key]['varsColumnDetail']['numValue'] = '-';
						$array[$key]['varsColumnDetail'][$valueStr] = '-';
						$array[$key]['varsPrint'][$valueStr] = '-';

					} else {
						$array[$key]['vars']['varsValue'][$valueStr] = $numValue;
						$array[$key]['varsColumnDetail']['numValue'] = number_format($numValue);
						$array[$key]['varsColumnDetail'][$valueStr] = number_format($numValue);
						$array[$key]['varsPrint'][$valueStr] = number_format($numValue);
					}

				}

			}

			if ($value['child']) {
				$array[$key]['child'] = $this->_getAccountTitleValueColumn(array(
					'varsFlag'    => $arr['varsFlag'],
					'vars'        => $arr['vars'],
					'varsFS'      => $array[$key]['child'],
					'varsFSValue' => $arr['varsFSValue'],
				));
			}
		}

		return $array;
	}

	/**
		(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $vars['varsFlag'],
		))
	 */
	protected function _updateVars($arr)
	{
		global $classHtml;

		global $varsAccount;

		if ($arr['varsFlag']['flagFS'] == 'BS' && $arr['varsFlag']['flagFiscalPeriod'] == 'f1') {
			$arr['vars']['portal']['varsList']['tableTree']['varsDetail']['varsColumn']
			 = $arr['vars']['portal']['varsList']['tableTree']['varsDetail']['varsColumnBS'];
		}

		$varsFS = $arr['varsItem']['varsFS']['jsonJgaapFS' . $arr['varsFlag']['flagFS']];

		if ($arr['varsFlag']['flagFS'] == 'BS' && $arr['varsFlag']['idDepartment'] != 'none') {
			$varsDepartmentTreeItem = $this->_getVarsDepartmentTreeItem();
			$arrayNew = array();
			$array = $varsFS;
			foreach ($array as $key => $value) {
				$arrayNew[] = $value;
//unique start
				if ($value['vars']['idTarget'] == 'liabilities') {
//unique end
					$arrayNew[] = $varsDepartmentTreeItem;
				}
			}
			$varsFS = $arrayNew;
		}

		$arr['vars']['portal']['varsList']['varsDetail'] = $this->_getAccountTitleValueColumn(array(
			'vars'        => $arr['vars'],
			'varsFS'      => $varsFS,
			'varsFSValue' => $arr['varsItem']['varsFSValue']['jsonJgaapFS' . $arr['varsFlag']['flagFS']],
			'varsFlag'    => $arr['varsFlag'],
		));

		if (!$arr['varsFlag']['flagZero']) {
			$arr['vars']['portal']['varsList']['varsDetail'] = $this->_getAccountTitleValueZero(array(
				'varsFS' => $arr['vars']['portal']['varsList']['varsDetail'],
			));
		}

		$varsDetail = $this->_setTreeId(array(
			'idParent' => '-',
			'vars'     => $arr['vars']['portal']['varsList']['varsDetail'],
		));
		$arr['vars']['portal']['varsList']['varsDetail'] = $varsDetail;

		$varsTemp = $classHtml->allot(array(
			'strClass'    => 'TableTree',
			'flagStatus'  => 'Html',
			'numTimeZone' => $varsAccount['numTimeZone'],
			'varsDetail'  => $varsDetail,
			'varsColumn'  => $arr['vars']['portal']['varsList']['tableTree']['varsDetail']['varsColumn'],
			'varsStatus'  => $arr['vars']['portal']['varsList']['tableTree']['varsDetail']['varsStatus'],
		));
		$arr['vars']['portal']['varsList']['varsHtml'] = $varsTemp['strHtml'];

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllOutput'])) {
			$arr['vars']['portal']['varsList']['varsStart']['varsEdit']['flagOutputUse'] = 0;
			$arr['vars']['portal']['varsList']['varsStart']['varsEdit']['flagPrintUse'] = 0;
		}

		return $arr['vars'];
	}


	/**
		(array(

		))
	 */
	protected function _getAccountTitleValueZero($arr)
	{
		$array = &$arr['varsFS'];
		foreach ($array as $key => $value) {
			$flag = 0;
			if (!is_null($array[$key]['vars']['varsValue']) && is_null($array[$key]['vars']['flagCalc'])) {
				if ($array[$key]['vars']['varsValue']['sumDebit'] == 0
					&& $array[$key]['vars']['varsValue']['sumCredit'] == 0
					&& $array[$key]['vars']['varsValue']['sumNext'] == 0
					&& $array[$key]['vars']['varsValue']['sumPrev'] == 0
					&& $array[$key]['vars']['idTarget'] != 'commonStock'
					&& $array[$key]['vars']['idTarget'] != 'unappropriatedRetainedEarnings'
				) {
					$flag = 1;
				}
				if ($flag) {
					unset($array[$key]);
				}
			}

			if ($array[$key]['child']) {
				$array[$key]['child'] = $this->_getAccountTitleValueZero(array(
					'varsFS' => $array[$key]['child'],
				));
			}
		}

		$arrayTemp = array();
		foreach ($array as $key => $value) {
			$arrayTemp[] = $value;
		}
		$array = $arrayTemp;

		return $array;
	}

}
