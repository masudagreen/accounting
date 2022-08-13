<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_FinancialStatementMulti_2012_Public extends Code_Else_Plugin_Accounting_Jpn_FinancialStatementMulti
{
	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;

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
			'vars'             => $vars,
			'varsEntityNation' => $varsEntityNation,
			'varsItem'         => $varsItem,
			'varsFlag'         => array(
				'idDepartment'      => $idDepartment,
				'flagFS'            => $flagFS,
			),
		))
	 */
	protected function _updateVars($arr)
	{
		global $classHtml;
		global $varsPluginAccountingAccount;

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllOutput'])) {
			$arr['vars']['portal']['varsList']['varsStart']['varsEdit']['flagOutputUse'] = 0;
			$arr['vars']['portal']['varsList']['varsEdit']['flagOutputUse'] = 0;
		}

		$array = array_reverse($arr['varsItem']['varsPeriod']);
		$varsFS = array();
		foreach ($array as $key => $value) {
			$numFiscalPeriod = $value;
			if ($key == 0) {
				$varsFS = $arr['varsItem']['varsFS'][$numFiscalPeriod]['jsonJgaapFS' . $arr['varsFlag']['flagFS']];
				if ($arr['varsFlag']['flagFS'] == 'BS' && $arr['varsFlag']['idDepartment'] != 'none') {
					$varsDepartmentTreeItem = $this->_getVarsDepartmentTreeItem();
					$arrayNew = array();
					$arrayFS = $varsFS;
					foreach ($arrayFS as $keyFS => $valueFS) {
						$arrayNew[] = $valueFS;
//unique start
						if ($valueFS['vars']['idTarget'] == 'liabilities') {
//unique end

							$arrayNew[] = $varsDepartmentTreeItem;
						}
					}
					$varsFS = $arrayNew;
				}
				continue;
			}
			$varsFS = $this->_checkLastAccountTitle(array(
				'varsTmpl'        => $arr['vars']['varsItem']['varsTmpl'],
				'varsFS'          => $varsFS,
				'numFiscalPeriod' => $numFiscalPeriod,
				'varsFSData'      => $arr['varsItem']['varsFS'][$numFiscalPeriod]['jsonJgaapFS' . $arr['varsFlag']['flagFS']],
			));
		}

		$varsBase = array();
		$array = array_reverse($arr['varsItem']['varsPeriod']);
		foreach ($array as $key => $value) {
			$numFiscalPeriod = $value;
			$varsBase[$numFiscalPeriod]['varsNum'] = $arr['varsItem']['varsFSValue'][$numFiscalPeriod]['jsonJgaapFS' . $arr['varsFlag']['flagFS']];
		}

		$varsFS = $this->_setTreeId(array(
			'idParent' => '-',
			'vars'     => $varsFS,
		));

		$varsTemp = $this->_getAccountTitleValue(array(
			'vars'         => $arr['vars'],
			'varsFS'       => $varsFS,
			'varsItem'     => $arr['varsItem'],
			'varsFlag'     => $arr['varsFlag'],
			'varsBase'     => $varsBase,
			'arrStrTitle'  => array(),
			'arrSelectTag' => array(),
		));

		$arr['vars']['varsCollect']['varsBase'] = $varsTemp['varsBase'];
		$arr['vars']['varsCollect']['arrStrTitle'] = $varsTemp['arrStrTitle'];
		$arr['vars']['varsCollect']['arrSelectTag'] = $varsTemp['arrSelectTag'];
		$arr['vars']['varsCollect']['varsPeriod'] = $arr['varsItem']['varsPeriod'];
		$arr['vars']['varsCollect']['varsFlagFiscalPeriod'] = $arr['varsItem']['varsFlagFiscalPeriod'];
		$arr['vars']['varsCollect']['varsStrFlagFiscalPeriod'] = $arr['varsItem']['varsStrFlagFiscalPeriod'];

		$arr['vars']['varsCollect']['varsZero'] = array();
		if (!$arr['varsFlag']['flagZero']) {
			$arr['vars']['varsCollect']['varsZero'] = $this->_getVarsZero((array(
				'arrSelectTag' => $varsTemp['arrSelectTag'],
				'varsBase'     => $varsTemp['varsBase'],
				'varsItem'     => $arr['varsItem'],
			)));
		}

		$arr['vars']['portal']['varsList']['varsDetail'] = $this->_updateVarsList((array(
			'vars'     => &$arr['vars'],
			'varsItem' => $arr['varsItem'],
			'varsFlag' => $arr['varsFlag'],
		)));

		return $arr['vars'];
	}

}
