<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcTempNext_AccountTitleCS extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extChildSelf = array(

	);

	/**
	 * tempNext only
	 */
	public function run()
	{
		if (FLAG_TEST) {
			var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
		}
		exit;
	}

	/*
		(array(

		))
	 * */
	public function allot($arr)
	{
		if (PLUGIN_ACCOUNTING_FLAG_CORPORATION) {
			return;
		}
		$method = '_ini' . ucwords($arr['flagStatus']);
		if (method_exists($this, $method)) {
			return $this->$method($arr);

		} else {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}
	}

	/*
		(array(
			'flagStatus'      => 'edit',
			'numFiscalPeriod' => $numFiscalPeriod,
			'flagFS'          => $flagFS,
			'flagDirect'      => $flagDirect,
			'idTarget'        => $idTarget,
			'arrValue' => array(
				'idAccountTitlePlus'  => $arrValue['idAccountTitlePlus'],
				'flagMethodPlus'      => $arrValue['flagMethodPlus'],
				'idAccountTitleMinus' => $arrValue['idAccountTitleMinus'],
				'flagMethodMinus'     => $arrValue['flagMethodMinus'],
			),
		))
	 * */
	protected function _iniEdit($arr)
	{
		$varsItem = $this->_getVarsItem(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'flagDirect'      => $arr['flagDirect'],
		));

		$flag = $this->_checkEditValue(array(
			'varsItem'        => $varsItem,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'flagDirect'      => $arr['flagDirect'],
			'flagFS'          => $arr['flagFS'],
			'idTarget'        => $arr['idTarget'],
			'arrValue'        => &$arr['arrValue'],
		));

		if ($flag) {
			if ($flag == 'none') {
				return;
			}
			return $flag;
		}

		$flag = $this->_setEditDb(array(
			'varsItem'        => $varsItem,
			'flagFS'          => $arr['flagFS'],
			'flagDirect'      => $arr['flagDirect'],
			'idTarget'        => $arr['idTarget'],
			'arrValue'        => $arr['arrValue'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		if ($flag) {
			return $flag;
		}
	}

	/**

	 */
	protected function _getVarsItem($arr)
	{
		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$arrAccountTitle = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => array(),
			'numFiscalPeriod'    => $arr['numFiscalPeriod'],
		));

		$strFlagDirect = 'varsInDirect';
		if ($arr['flagDirect']) {
			$strFlagDirect = 'varsDirect';
		}

		$varsFSItem = $this->_getVarsFSItem();

		$arrStrTitle = array();
		$arrStrTitle[$varsFSItem['varsCSOption']['arrayOptionNone']['value']] = $varsFSItem['varsCSOption']['arrayOptionNone']['strTitle'];
		$arrStrTitle[$varsFSItem['varsCSOption']['arrayOptionCash']['value']] = $varsFSItem['varsCSOption']['arrayOptionCash']['strTitle'];
		$varsJgaapFSCS = array();
		$varsJgaapFSCS[$strFlagDirect] = $this->_getVarsItemJgaapFSCS(array(
			'arrStrTitle'  => $arrStrTitle,
			'arrSelectTag' => array($varsFSItem['varsCSOption']['arrayOptionNone'], $varsFSItem['varsCSOption']['arrayOptionCash']),
			'vars'         => $varsFS['jsonJgaapFSCS'][$strFlagDirect],
		));

		$data = array(
			'varsFS'          => $varsFS,
			'varsFSItem'      => $varsFSItem,
			'varsJgaapFSCS'   => $varsJgaapFSCS,
			'arrAccountTitle' => $arrAccountTitle,
		);

		return $data;
	}

	/**
		(array(
			'arrStrTitle'  => array(),
			'vars'         => array(),
			'arrSelectTag' => array(),
		))
	 */
	protected function _getVarsItemJgaapFSCS($arr)
	{

		$arrSelectTag = &$arr['arrSelectTag'];
		$arrStrTitle = &$arr['arrStrTitle'];

		$array = &$arr['vars'];
		foreach ($array as $key => $value) {
			$arr['arrStrTitle'][$value['vars']['idTarget']] = $value['strTitle'];

			$arrLevel = preg_split("/-/", $value['id']);
			$num = count($arrLevel) - 2;
			$arrLevel = array();
			for ($i = 0 ; $i < $num; $i++) {
				$arrLevel[$i] = '';
			}
			$str =  ' ' . join('.', $arrLevel) . ' ';
			$strTitle = $str . $value['strTitle'];

			if ($value['vars']['idTarget'] == 'cashRate') {
				$arr['arrSelectTag'][] = array(
					'strTitle'     => $strTitle,
					'value'        => $value['vars']['idTarget'],
				);

			} else {
				if (is_null($value['vars']['flagUse'])) {
					$arr['arrSelectTag'][] = array(
						'strTitle'     => $strTitle,
						'value'        => '',
						'flagDisabled' => 1,
					);

				} else {
					$arr['arrSelectTag'][] = array(
						'strTitle'     => $strTitle,
						'value'        => $value['vars']['idTarget'],
					);
				}
			}

			if ($value['child']) {
				$data = $this->_getVarsItemJgaapFSCS(array(
					'vars'          => $array[$key]['child'],
					'arrSelectTag'  => $arr['arrSelectTag'],
					'arrStrTitle'   => $arr['arrStrTitle'],
				));
				$array[$key]['child'] = $data['vars'];
				$arrSelectTag =  $data['arrSelectTag'];
				$arrStrTitle =  $data['arrStrTitle'];
			}
		}

		return $arr;
	}

	/**
		(array(
			'varsItem'        => $varsItem,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'flagFS'          => $arr['flagFS'],
			'idTarget'        => $arr['idTarget'],
			'arrValue'        => &$arr['arrValue'],
		))
	 */
	protected function _checkEditValue($arr)
	{
		$numFiscalPeriodTemp = $arr['numFiscalPeriod'] - 1;
		$varsFS = $arr['varsItem']['varsFS']['jsonJgaapAccountTitle'. $arr['flagFS']];
		$varsFS = $this->_getFlagUseLog(array(
			'vars'                  => $varsFS,
			'numFiscalPeriod'       => $arr['numFiscalPeriod'],
			'numFiscalPeriodTemp'   => $numFiscalPeriodTemp,
			'classCalcAccountTitle' => $this->_getClassCalc(array('flagType' => 'AccountTitle')),
			'idTarget'              => $arr['idTarget'],
			'flagFS'                => $arr['flagFS'],
		));

		$arrayBlock = $this->_getTreeBlock(array(
			'vars'     => $varsFS,
			'idTarget' => $arr['idTarget'],
			'arrNew'   => array(),
		));
		if (!$arrayBlock) {
			return 'none';
		}

		$varsTarget = array();
		foreach ($arrayBlock as $key => $value) {
			if ($value['vars']['idTarget'] == $arr['idTarget']) {
				$varsTarget = $value;
				break;
			}
		}

		if ($varsTarget['vars']['flagUseLogElse']
			|| ($varsTarget['vars']['flagUseLogElseTemp'] && $varsTarget['vars']['flagFS'] == 'BS')
		) {
			return 'sendOld';
		}

		$strFlagDirect = 'varsInDirect';
		if ($arr['flagDirect']) {
			$strFlagDirect = 'varsDirect';
		}

		$classCalcTempNextAccountTitleFSCS = $this->_getClassCalc(array(
			'flagType'   => 'TempNext',
			'flagDetail' => 'AccountTitleFSCS',
		));

		$arrayCheck = array();
		$arrayCheck[$arr['arrValue']['idAccountTitlePlus']] = 1;
		$arrayCheck[$arr['arrValue']['idAccountTitleMinus']] = 1;
		foreach ($arrayCheck as $key => $value) {
			if (!$arr['varsItem']['varsJgaapFSCS'][$strFlagDirect]['arrStrTitle'][$key]) {
				if ($key == 'none' || $key == 'cash') {
					continue;
				}
				$flag = $classCalcTempNextAccountTitleFSCS->allot(array(
					'flagStatus'      => 'back',
					'numFiscalPeriod' => $arr['numFiscalPeriod'],
					'flagFS'          => 'CS',
					'idTarget'        => $key,
					'flagDirect'      => $arr['flagDirect'],
				));
				if ($flag) {
					return $flag;
				}
			}
		}

	}

	/**
			'varsItem'        => $varsItem,
			'flagFS'          => $arr['flagFS'],
			'flagDirect'      => $arr['flagDirect'],
			'idTarget'        => $arr['idTarget'],
			'arrValue'        => $arr['arrValue'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
	 */
	protected function _setEditDb($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$varsFS = $this->_getValueVarsFS(array(
			'arrValue'    => $arr['arrValue'],
			'varsItem'    => $arr['varsItem'],
			'flagFS'      => $arr['flagFS'],
			'idTarget'    => $arr['idTarget'],
			'flagDirect'  => $arr['flagDirect'],
		));

		$jsonAccountTitle = json_encode($varsFS);
		$flag = $this->checkTextSize(array(
			'flagReturn' => 1,
			'str'        => $jsonAccountTitle,
		));
		if ($flag) {
			return 'errorDataMax';
		}

		$strAccountTitle = 'jsonJgaapAccountTitle'. $arr['flagFS'];

		$arrDbColumn = array($strAccountTitle);
		$arrDbValue = array($jsonAccountTitle);

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable' => 'accountingFS' . $strNation,
			'arrColumn' => $arrDbColumn,
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
			'arrValue'  => $arrDbValue,
		));
	}

	/**
		(array(
			'arrValue'    => $arr['arrValue'],
			'varsItem'    => $arr['varsItem'],
			'flagFS'      => $arr['flagFS'],
			'idTarget'    => $arr['idTarget'],
			'flagDirect'  => $arr['flagDirect'],
		))
	 */
	protected function _getValueVarsFS($arr)
	{
		global $classEscape;

		$arrayBlock = $this->_getTreeBlock(array(
			'vars'     => $arr['varsItem']['varsFS']['jsonJgaapAccountTitle'. $arr['flagFS']],
			'idTarget' => $arr['idTarget'],
			'arrNew'   => array(),
		));

		$varsTarget = array();
		foreach ($arrayBlock as $key => $value) {
			if ($value['vars']['idTarget'] == $arr['idTarget']) {
				$varsTarget = $value;
				break;
			}
		}

		$arrayCheck = array();
		foreach ($arrayBlock as $key => $value) {
			$arrayCheck[$value['vars']['idTarget']] = $value;
		}

		$arrayIdAccountTitle = array();
		foreach ($arrayBlock as $key => $value) {
			$arrayIdAccountTitle[] = $value['vars']['idTarget'];
		}

		$idAccountTitlePlus = $arr['arrValue']['idAccountTitlePlus'];
		$idAccountTitleMinus = $arr['arrValue']['idAccountTitleMinus'];
		$flagMethodPlus = $arr['arrValue']['flagMethodPlus'];
		$flagMethodMinus = $arr['arrValue']['flagMethodMinus'];

		if ($idAccountTitlePlus == 'cash' || $idAccountTitlePlus == 'none') {
			$flagMethodPlus = '';
		}
		if ($idAccountTitleMinus == 'cash' || $idAccountTitleMinus == 'none') {
			$flagMethodMinus = '';
		}

		$strFlagDirect = 'varsInDirect';
		if ($arr['flagDirect']) {
			$strFlagDirect = 'varsDirect';
		}
		$array = $arrayIdAccountTitle;
		$arrayNew = array();
		foreach ($array as $key => $value) {
			if ($value == $arr['idTarget']) {
				$arrayCheck[$value]['vars']['varsJgaapCS'][$strFlagDirect] = array(
					'idAccountTitleMinus' => $idAccountTitleMinus, 'flagMethodPlus' => $flagMethodPlus,
					'idAccountTitlePlus' => $idAccountTitlePlus, 'flagMethodMinus' => $flagMethodMinus,
				);
				$arrayNew[] = $arrayCheck[$value];

			} else {
				$arrayNew[] = $arrayCheck[$value];
			}

		}

		$varsFS = $this->_insertTreeBlock(array(
			'vars'       => $arr['varsItem']['varsFS']['jsonJgaapAccountTitle'. $arr['flagFS']],
			'idTarget'   => $arr['idTarget'],
			'varsTarget' => $arrayNew,
		));

		return $varsFS;
	}

	/**
		(array(
			'vars'       => array(),
			'idTarget'   => array(),
			'varsTarget' => array(),
		));
	 */
	protected function _insertTreeBlock($arr)
	{
		$array = &$arr['vars'];
		foreach ($array as $key => $value) {
			if ($value['vars']['idTarget'] == $arr['idTarget']) {
				$array = $arr['varsTarget'];
				break;
			}
			if ($value['child']) {
				$array[$key]['child'] = $this->_insertTreeBlock(array(
					'vars'       => $array[$key]['child'],
					'idTarget'   => $arr['idTarget'],
					'varsTarget' => $arr['varsTarget'],
				));

			}
		}

		return $array;
	}
}
