<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcDetailsSync extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_varsCalc = array(
		'sumSync'   => 0,
		'sumCustom' => 0,
		'sum'       => 0,
		'sumNext'   => 0,
		'sumLost'   => 0,
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

	/**
		(array(
			'flagStatus'      => 'calc',
			'strTable'        => '',
			'flagFS'          => 'BS',
			'flagDebit'       => 1,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	public function allot($arr)
	{
		$method = '_ini' . ucwords($arr['flagStatus']);

		return $this->$method($arr);
	}

	/**
		(array(
		))
	 */
	protected function _iniCalc($arr)
	{
		$varsItem = $this->_getVarsItem(array(
			'flagFS'          => $arr['flagFS'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$varsLog = $this->_getVarsLog(array(
			'strTable'        => $arr['strTable'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		if (!$varsLog) {
			return $this->_varsCalc;
		}

		$this->_varsCalc = $this->_getVarsCalc(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'flagDebit'       => $arr['flagDebit'],
			'varsLog'         => $varsLog,
			'varsItem'        => $varsItem,
		));

		return $this->_varsCalc;
	}

	/**
		(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'varsLog'         => $varsLog,
		))

	 */
	protected function _getVarsCalc($arr)
	{
		$data = array(
			'sumSync'   => 0,
			'sumCustom' => 0,
			'sum'       => 0,
			'sumLost'   => 0,
		);

		$arrStrTitle = $arr['varsItem']['arrAccountTitle']['arrStrTitle'];
		$arrStrTitle['none']['strTitleFS'] = 'dummy';
		$arrSubStrTitle = $arr['varsItem']['arrSubAccountTitle']['arrStrTitle'];

		$arrIdAccountTitle = array();
		$array = $arr['varsLog'];
		foreach ($array as $key => $value) {
			$strIdAccountTitle = $arrStrTitle[$value['idAccountTitle']]['strTitleFS'];
			$arrSubStrTitle[$value['idAccountTitle']]['0']['strTitle'] = 'dummy';
			$strIdSubAccountTitle = $arrSubStrTitle[$value['idAccountTitle']][$value['idSubAccountTitle']]['strTitle'];

			if (is_null($strIdAccountTitle) || is_null($strIdSubAccountTitle)) {
				continue;
			}

			//none Sync
			if ($value['idAccountTitle'] == 'none') {
				$data['sum'] += $value['numValue'];
				$data['sumCustom'] += $value['numValue'];

			//Sync
			} else {
				$num = 0;
				$arrId = array();
				if ($value['idSubAccountTitle'] == 0) {
					$sumValue = $arr['varsItem']['varsFSValue']['f1'][$value['idAccountTitle']]['sumNext'];
					if (is_null($sumValue)) {
						$sumValue = 0;
					}
					$sumSubValue = 0;
					$arrId = $arr['varsItem']['arrSubAccountTitle']['arrStrTitle'][$value['idAccountTitle']];
					if ($arrId) {
						$sumSubValue = $this->_getVarsSubValue(array(
							'arrId'           => $arrId,
							'numFiscalPeriod' => $arr['numFiscalPeriod'],
						));
					}
					$num = $sumValue - $sumSubValue;

				} else {
					$arrId[$value['idSubAccountTitle']] = 1;
					$sumSubValue = $this->_getVarsSubValue(array(
						'arrId'           => $arrId,
						'numFiscalPeriod' => $arr['numFiscalPeriod'],
					));
					$num = $sumSubValue;
				}
				$flagDebit = $arrStrTitle[$value['idAccountTitle']]['flagDebit'];
				if ($flagDebit != $arr['flagDebit']) {
					$num *= -1;
				}
				$data['sum'] += $num;
				$data['sumSync'] += $num;
				$arrIdAccountTitle[$value['idAccountTitle']] = 1;
			}
		}

		$sumNext = 0;
		$array = $arrIdAccountTitle;
		foreach ($array as $key => $value) {
			$idAccountTitle = $key;
			$num = $arr['varsItem']['varsFSValue']['f1'][$idAccountTitle]['sumNext'];
			$flagDebit = $arrStrTitle[$idAccountTitle]['flagDebit'];
			if ($flagDebit != $arr['flagDebit']) {
				$num *= -1;
			}
			$sumNext += $num;
		}
		$data['sumNext'] = $sumNext;
		$data['sumLost'] = $data['sumNext'] - $data['sumSync'];

		return $data;
	}

	/**
		(array(
			'vars' => $vars,
		))
	 */
	protected function _getVarsItem($arr)
	{
		$arrSubAccountTitle = $this->_getVarsSubAccountTitle(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$arrAccountTitle = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'numFiscalPeriod'    => $arr['numFiscalPeriod'],
		));

		$varsFSValue = $this->_getVarsFSValue(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$data = array(
			'varsFSValue'        => $varsFSValue['jsonJgaapAccountTitle' . $arr['flagFS']],
			'arrAccountTitle'    => $arrAccountTitle,
			'arrSubAccountTitle' => $arrSubAccountTitle,
		);

		return $data;
	}

	/**
		(array(
			'strTable'        => '',
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		))

	 */
	protected function _getVarsLog($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => $arr['strTable'],
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

		return $rows['arrRows'];
	}

	/**
		(array(
			'arrId'           => $arr['varsItem']['arrSubAccountTitle']['arrStrTitle'][$value['idAccountTitle']],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		))

	 */
	protected function _getVarsSubValue($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$sum = 0;
		$array = $arr['arrId'];
		foreach ($array as $key => $value) {
			$rows = $classDb->getSelect(array(
				'idModule' => 'accounting',
				'strTable' => 'accountingSubAccountTitleValue' . $strNation,
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
						'strColumn'     => 'idSubAccountTitle',
						'flagCondition' => 'eq',
						'value'         => $key,
					),
				),
			));
			$num = $rows['arrRows'][0]['jsonData']['all']['f1']['sumNext'];
			if (is_null($num)) {
				$num = 0;
			}
			$sum += $num;
		}

		return $sum;
	}


}
