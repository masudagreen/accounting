<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcLogCalc extends Code_Else_Plugin_Accounting_Jpn_Jpn
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
		(array(
			'flagStatus'    => 'edit',
			'arrRowsAdd'    => $arrRowsAdd,
			'arrRowsDelete' => $arrRowsDelete,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _iniEdit($arr)
	{
		$this->_iniDelete(array(
			'arrRows'         => $arr['arrRowsDelete'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		$this->_iniAdd(array(
			'arrRows'         => $arr['arrRowsAdd'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
	}

	/**
		(array(
			'flagStatus' => 'add',
			'arrRows'    => $arrayNew,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _iniAdd($arr)
	{
		$this->_setAdd(array(
			'arrRows'         => $arr['arrRows'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		$this->_updateBalance(array(
			'arrRows'         => $arr['arrRows'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$this->_updateDbPreferenceStamp(array('strColumn' => 'logCalc'));
	}

	/**
		(array(
			'flagStatus' => 'update',
			'arrRows'    => $arrayNew,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _iniUpdate($arr)
	{
		$this->_updateBalance(array(
			'arrRows'         => $arr['arrRows'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		$this->_updateDbPreferenceStamp(array('strColumn' => 'logCalc'));
	}

	/**
		(array(
			'arrRows' => $arr['arrRows']
		))
	 */
	protected function _setAdd($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$array = &$arr['arrRows'];
		foreach ($array as $key => $value) {
			$classDb->insertRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingLogCalc' . $strNation,
				'arrColumn' => $value['arrColumn'],
				'arrValue'  => $value['arrValue'],
			));
		}

	}

	/**
		(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _getVarsItem($arr)
	{
		global $classDb;
		global $varsAccount;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingFS' . $strNation,
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
			),
		));
		$varsFS = $rows['arrRows'][0];

		$varsAccountTitle = array();
		$array = $this->_extendSelf['arrFS'];
		foreach ($array as $key => $value) {
			if ($value == 'BS') {
				$data = $this->_getArrSelectOption(array(
					'arrStrTitle'     => array(),
					'arrSelectTag'    => array(),
					'vars'            => $varsFS['jsonJgaapAccountTitle' . $value],
					'flagBS'          => ($value == 'BS')? 1 : 0,
					'flagFS'          => $value,
				));
				$varsAccountTitle = $data['arrStrTitle'];
			}
		}

		$data = array(
			'varsAccountTitle' => $varsAccountTitle,
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
		);

		return $data;

	}

	/**
		(array(
			'arrRows' => $arr['arrRows'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _updateBalance($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$arrayIdAccountTitle = array();

		$array = $arr['arrRows'];
		foreach ($array as $key => $value) {
			$arrayIdAccountTitle[$value['idAccountTitle']]['flagDebit'] = $value['flagDebitAccountTitle'];
		}

		$varsItem = $this->_getVarsItem(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$array = $arrayIdAccountTitle;
		foreach ($array as $key => $value) {
			$idAccountTitle = $key;


			$rows = $this->_getLogCalc(array(
				'idAccountTitle'    => $idAccountTitle,
				'idSubAccountTitle' => null,
				'idDepartment'      => null,
				'numFiscalPeriod'   => $arr['numFiscalPeriod'],
				'arrLimit'          => '',
				'arrOrder'          => array(
					'strColumn' => 'stampBook, idLog, id',
				),
			));

			$varsBalance = $this->_getVarsBalance(array(
				'flagBS'          => ($varsItem['varsAccountTitle'][$idAccountTitle])? 1 : 0,
				'idAccountTitle'  => $idAccountTitle,
				'arrRows'         => $rows['arrRows'],
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
			));

			$rows['arrRows'] = $this->_updateVarsBalance(array(
				'flag'            => 'accountTitle',
				'arrRows'         => $rows['arrRows'],
				'flagDebit'       => $value['flagDebit'],
				'varsPrevBalance' => $varsBalance['numPrevAccountTitle'],
			));

			if ($varsBalance['flagSubAccountTitle']) {
				$rows['arrRows'] = $this->_updateVarsBalance(array(
					'flag'            => 'subAccountTitle',
					'arrRows'         => $rows['arrRows'],
					'flagDebit'       => $value['flagDebit'],
					'varsPrevBalance' => $varsBalance['arrPrevSubAccountTitle'],
				));
			}

			if ($varsBalance['flagDepartment']) {
				$rows['arrRows'] = $this->_updateVarsBalance(array(
					'flag'            => 'department',
					'arrRows'         => $rows['arrRows'],
					'flagDebit'       => $value['flagDebit'],
					'varsPrevBalance' => $varsBalance['arrPrevDepartment'],
				));
			}

			if ($varsBalance['flagDepartmentSubAccountTitle']) {
				$rows['arrRows'] = $this->_updateVarsBalance(array(
					'flag'            => 'departmentSubAccountTitle',
					'arrRows'         => $rows['arrRows'],
					'flagDebit'       => $value['flagDebit'],
					'varsPrevBalance' => $varsBalance['arrPrevDepartmentSubAccountTitle'],
				));
			}

			$this->_updateDbBalance(array(
				'arrRows' => $rows['arrRows'],
			));
		}

	}

	/**
		(array(
				'idAccountTitle'    => $idAccountTitle,
				'idSubAccountTitle' => null,
				'idDepartment'      => null,
				'numFiscalPeriod'   => null,
				'arrLimit'          => '',
				'arrOrder'          => array(
					'strColumn' => 'stampBook',
					'flagDesc'  => 0,
				),
		))
	 */
	protected function _getLogCalc($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$arrWhere = array(
			array(
				'flagType'      => 'num',
				'strColumn'     => 'idEntity',
				'flagCondition' => 'eq',
				'value'         => $varsPluginAccountingAccount['idEntityCurrent'],
			),
			array(
				'flagType'      => '',
				'strColumn'     => 'idAccountTitle',
				'flagCondition' => 'eq',
				'value'         => $arr['idAccountTitle'],
			),
		);

		$arrWhere[] = array(
			'flagType'      => 'num',
			'strColumn'     => 'numFiscalPeriod',
			'flagCondition' => 'eq',
			'value'         => $arr['numFiscalPeriod'],
		);


		if ($arr['idSubAccountTitle']) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idSubAccountTitle',
				'flagCondition' => 'eq',
				'value'         => $arr['idSubAccountTitle'],
			);
		}

		if ($arr['idDepartment']) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idDepartment',
				'flagCondition' => 'eq',
				'value'         => $arr['idDepartment'],
			);
		}

		$rows = $classDb->getSelect(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingLogCalc' . $strNation,
			'arrJoin'     => '',
			'arrLimit'    => $arr['arrLimit'],
			'arrOrder'    => $arr['arrOrder'],
			'arrWhere'    => $arrWhere,
			'flagAnd'     => 1,
			'insCurrent'  => '',
		));

		return $rows;
	}





	/**
		(array(
				'arrRows'         => $rows['arrRows'],
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _getVarsBalance($arr)
	{
		global $varsPluginAccountingAccount;
		global $classDb;
		$dbh = $classDb->getHandle();

		$numFiscalPeriod = $arr['numFiscalPeriod'];
		$numPrevAccountTitle = null;
		$arrPrevSubAccountTitle = array();
		$arrPrevDepartment = array();
		$arrPrevDepartmentSubAccountTitle = array();

		$flagSubAccountTitle = 0;
		$flagDepartment = 0;
		$flagDepartmentSubAccountTitle = 0;

		$array = $arr['arrRows'];
		foreach ($array as $key => $value) {
			if ($value['idDepartment']) {
				$flagDepartment = 1;
				if ($value['idSubAccountTitle']) {
					$flagDepartmentSubAccountTitle = 1;
					if (is_null($arrPrevDepartmentSubAccountTitle[$value['idDepartment']][$value['idSubAccountTitle']])) {
						$arrPrevDepartmentSubAccountTitle[$value['idDepartment']][$value['idSubAccountTitle']] = $this->_getNumPrev(array(
							'idAccountTitle'    => $value['idAccountTitle'],
							'idSubAccountTitle' => $value['idSubAccountTitle'],
							'idDepartment'      => $value['idDepartment'],
							'numFiscalPeriod'   => $numFiscalPeriod,
							'flagBS'            => $arr['flagBS'],
						));
					}
				}
				if (is_null($arrPrevDepartment[$value['idDepartment']])) {
					$arrPrevDepartment[$value['idDepartment']] = $this->_getNumPrev(array(
						'idAccountTitle'    => $value['idAccountTitle'],
						'idSubAccountTitle' => 'none',
						'idDepartment'      => $value['idDepartment'],
						'numFiscalPeriod'   => $numFiscalPeriod,
						'flagBS'            => $arr['flagBS'],
					));
				}
			}

			if ($value['idSubAccountTitle']) {
				$flagSubAccountTitle = 1;
				if (is_null($arrPrevSubAccountTitle[$value['idSubAccountTitle']])) {
					$arrPrevSubAccountTitle[$value['idSubAccountTitle']] = $this->_getNumPrev(array(
						'idAccountTitle'    => $value['idAccountTitle'],
						'idSubAccountTitle' => $value['idSubAccountTitle'],
						'idDepartment'      => 'none',
						'numFiscalPeriod'   => $numFiscalPeriod,
						'flagBS'            => $arr['flagBS'],
					));
				}
			}

			if (is_null($numPrevAccountTitle)) {
				$numPrevAccountTitle = $this->_getNumPrev(array(
					'idAccountTitle'    => $value['idAccountTitle'],
					'idSubAccountTitle' => 'none',
					'idDepartment'      => 'none',
					'numFiscalPeriod'   => $numFiscalPeriod,
					'flagBS'            => $arr['flagBS'],
				));
			}
		}

		$data = array(
			'numPrevAccountTitle'              => $numPrevAccountTitle,

			'flagSubAccountTitle'              => $flagSubAccountTitle,
			'arrPrevSubAccountTitle'           => $arrPrevSubAccountTitle,

			'flagDepartment'                   => $flagDepartment,
			'arrPrevDepartment'                => $arrPrevDepartment,

			'flagDepartmentSubAccountTitle'    => $flagDepartmentSubAccountTitle,
			'arrPrevDepartmentSubAccountTitle' => $arrPrevDepartmentSubAccountTitle,
		);

		return $data;
	}

	/**

	 */
	protected function _getNumPrev($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;

		$numValue = 0;

		if (!$arr['flagBS']) {
			return $numValue;
		}

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);
		$arrWhere = array(
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
		);

		if ($arr['idDepartment'] == 'none') {
			if ($arr['idSubAccountTitle'] == 'none') {
				//numBalance
				$strTable = 'accountingFSValue' . $strNation;

			} else {
				//numBalanceSubAccount
				$strTable = 'accountingSubAccountTitleValue' . $strNation;

				$arrWhere[] = array(
					'flagType'      => '',
					'strColumn'     => 'idSubAccountTitle',
					'flagCondition' => 'eq',
					'value'         => $arr['idSubAccountTitle'],
				);
			}

		} else {
			if ($arr['idSubAccountTitle'] == 'none') {
				//numBalanceDepartment
				$strTable = 'accountingEntityDepartmentFSValue' . $strNation;

				$arrWhere[] = array(
					'flagType'      => '',
					'strColumn'     => 'idDepartment',
					'flagCondition' => 'eq',
					'value'         => $arr['idDepartment'],
				);

			} else {
				//numBalanceDepartmentSubAccount
				$strTable = 'accountingSubAccountTitleValue' . $strNation;

				$arrWhere[] = array(
					'flagType'      => '',
					'strColumn'     => 'idSubAccountTitle',
					'flagCondition' => 'eq',
					'value'         => $arr['idSubAccountTitle'],
				);
			}
		}

		$rows = $classDb->getSelect(array(
			'idModule'    => 'accounting',
			'strTable'    => $strTable,
			'arrJoin'     => '',
			'arrLimit'    => array(
				'numStart' => 0, 'numEnd' => 1,
			),
			'arrOrder'  => array(),
			'arrWhere'    => $arrWhere,
			'flagAnd'     => 1,
		));

		if (!$rows['numRows']) {
			return $numValue;
		}

		$idAccountTitle = $arr['idAccountTitle'];

		if ($arr['idDepartment'] == 'none') {
			if ($arr['idSubAccountTitle'] == 'none') {
				//numBalance
				$numValue = $rows['arrRows'][0]['jsonJgaapAccountTitleBS']['f1'][$idAccountTitle]['sumPrev'];

			} else {
				//numBalanceSubAccount
				$numValue = $rows['arrRows'][0]['jsonData']['all']['f1']['sumPrev'];
			}

		} else {
			if ($arr['idSubAccountTitle'] == 'none') {
				//numBalanceDepartment
				$numValue = $rows['arrRows'][0]['jsonJgaapAccountTitleBS']['f1'][$idAccountTitle]['sumPrev'];

			} else {
				//numBalanceDepartmentSubAccount
				$idDepartment = $arr['idDepartment'];
				$numValue = $rows['arrRows'][0]['jsonData'][$idDepartment]['f1']['sumPrev'];
			}
		}

		if (is_null($numValue)) {
			$numValue = 0;
		}

		return $numValue;
	}

	/**
		(array(
				'flag'            => 'accountTitle',
				'arrRows'         => $rows['arrRows'],
				'flagDebit'       => $value['flagDebit'],
				'varsPrevBalance' => $varsBalance['numPrevAccountTitle'],
		))
	 */
	protected function _updateVarsBalance($arr)
	{
		$array = &$arr['arrRows'];
		foreach ($array as $key => $value) {
			$num = 0;
			if ((int) $arr['flagDebit']) {
				if ($value['flagDebit']) {
					$num += $value['numValue'];

				} else {
					$num -= $value['numValue'];
				}

			} else {
				if ($value['flagDebit']) {
					$num -= $value['numValue'];

				} else {
					$num += $value['numValue'];
				}
			}

			if ($arr['flag'] == 'accountTitle') {
				$arr['varsPrevBalance'] += $num;
				$array[$key]['numBalance'] = $arr['varsPrevBalance'];

			} elseif ($arr['flag'] == 'subAccountTitle') {
				if ($value['idSubAccountTitle']) {
					$arr['varsPrevBalance'][$value['idSubAccountTitle']] += $num;
					$array[$key]['numBalanceSubAccount']
						 = $arr['varsPrevBalance'][$value['idSubAccountTitle']];
				}

			} elseif ($arr['flag'] == 'department') {
				if ($value['idDepartment']) {
					$arr['varsPrevBalance'][$value['idDepartment']] += $num;
					$array[$key]['numBalanceDepartment']
						 = $arr['varsPrevBalance'][$value['idDepartment']];
				}

			} elseif ($arr['flag'] == 'departmentSubAccountTitle') {
				if ($value['idDepartment'] && $value['idSubAccountTitle']) {
					$arr['varsPrevBalance'][$value['idDepartment']][$value['idSubAccountTitle']] += $num;
					$array[$key]['numBalanceDepartmentSubAccount']
						 = $arr['varsPrevBalance'][$value['idDepartment']][$value['idSubAccountTitle']];
				}
			}

		}

		return $array;
	}

	protected function _updateDbBalance($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$array = &$arr['arrRows'];
		foreach ($array as $key => $value) {
			$numBalance = $value['numBalance'];
			$numBalanceSubAccount =  $value['numBalanceSubAccount'];
			$numBalanceDepartment =  $value['numBalanceDepartment'];
			$numBalanceDepartmentSubAccount =  $value['numBalanceDepartmentSubAccount'];
			$classDb->updateRow(array(
				'idModule' => 'accounting',
				'strTable'  => 'accountingLogCalc' . $strNation,
				'arrColumn' => array('numBalance', 'numBalanceSubAccount', 'numBalanceDepartment', 'numBalanceDepartmentSubAccount'),
				'flagAnd'   => 1,
				'arrWhere'  => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'id',
						'flagCondition' => 'eq',
						'value'         => $value['id'],
					),
				),
				'arrValue'  => array($numBalance, $numBalanceSubAccount, $numBalanceDepartment, $numBalanceDepartmentSubAccount),
			));
		}
	}

	/**
		(array(
			'flagStatus' => 'Delete',
			'arrRows'    => array,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _iniDelete($arr)
	{
		$this->_setDelete(array(
			'arrRows'         => $arr['arrRows'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		$this->_updateBalance(array(
			'arrRows'         => $arr['arrRows'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		$this->_updateDbPreferenceStamp(array('strColumn' => 'logCalc'));
	}

	/**
		(array(
			'arrRows' => $arr['arrRows']
		))
	 */
	protected function _setDelete($arr)
	{
		global $classEscape;
		global $classDb;
		$dbh = $classDb->getHandle();

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$array = &$arr['arrRows'];
		$arrayCheck = array();
		foreach ($array as $key => $value) {
			if ($arrayCheck[$value['idLog']]) {
				continue;
			}
			$classDb->deleteRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingLogCalc' . $strNation,
				'flagAnd'  => 1,
				'arrWhere'  => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idEntity',
						'flagCondition' => 'eq',
						'value'         => $value['idEntity'],
					),
					array(
						'flagType'      => 'num',
						'strColumn'     => 'numFiscalPeriod',
						'flagCondition' => 'eq',
						'value'         => $value['numFiscalPeriod'],
					),
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idLog',
						'flagCondition' => 'eq',
						'value'         => $value['idLog'],
					),
				),
			));
			$arrayCheck[$value['idLog']] = 1;
		}

	}



}
