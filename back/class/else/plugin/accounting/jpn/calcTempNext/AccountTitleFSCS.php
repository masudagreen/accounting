<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcTempNext_AccountTitleFSCS extends Code_Else_Plugin_Accounting_Jpn_Jpn
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

	 * */
	public function allot($arr)
	{
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

	/**
		(array(
			'flagStatus'      => $flagStatus,
			'numFiscalPeriod' => $numFiscalPeriod,
			'flagFS'          => $flagFS,
			'idTarget'        => $idTarget,
			'flagDirect'      => $flagDirect,
			'arrValue' => array(
				'strTitle'               => $arrValue['arr']['strTitle'],
				'idAccountTitle'         => $arrValue['arr']['idAccountTitle'],
				'arrCommaIdAccountTitle' => $arrValue['arr']['arrCommaIdAccountTitle'],
				'flagUse'                => 1,
			),
		))
	 */
	protected function _iniAdd($arr)
	{
		$varsItem = $this->_getVarsItem(array(
			'flagFS'          => $arr['flagFS'],
			'flagDirect'      => $arr['flagDirect'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$flag = $this->_checkAddValue(array(
			'varsItem'   => $varsItem,
			'flagFS'     => $arr['flagFS'],
			'flagDirect' => $arr['flagDirect'],
			'idTarget'   => &$arr['idTarget'],
			'arrValue'   => $arr['arrValue'],
		));
		if ($flag) {
			return $flag;
		}

		$flag = $this->_setAddDb(array(
			'flagStatus'      => $arr['flagStatus'],
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
		(array(
			'flagFS'          => $arr['flagFS'],
			'flagDirect'      => $arr['flagDirect'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _getVarsItem($arr)
	{
		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$varsFSItem = $this->_getVarsFSItem();

		$varsJgaapFS = $this->_getVarsItemJgaapFS(array(
			'varsFS'          => $varsFS,
			'flagDirect'      => $arr['flagDirect'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$data = array(
			'varsFS'       => $varsFS,
			'varsFSItem'   => $varsFSItem,
			'varsJgaapFS'  => $varsJgaapFS,
		);

		return $data;
	}

	/**
		(array(
			'varsFS'          => $varsFS,
			'flagDirect'      => $arr['flagDirect'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _getVarsItemJgaapFS($arr)
	{
		$strFlagDirect = 'varsInDirect';
		if ($arr['flagDirect']) {
			$strFlagDirect = 'varsDirect';
		}

		$array = $this->_getFSList(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$arrayNew = array();
		$arrayNewFS = array();
		foreach ($array as $key => $value) {
			$data = $this->_getVarsItemJgaapFSLoop(array(
				'arrIdAccountTitle' => array(),
				'vars'              => $arr['varsFS']['jsonJgaapAccountTitle'. $key],
				'strFlagDirect'     => $strFlagDirect,
			));

			$arrayNewFS[$key] = $data['arrIdAccountTitle'];
			$arrayAccount = $data['arrIdAccountTitle'];
			foreach ($arrayAccount as $keyAccount => $valueAccount) {
				if (is_null($arrayNew[$keyAccount])) {
					$arrayNew[$keyAccount] = $valueAccount;

				} else {
					$arrayAccountChild = $valueAccount;
					foreach ($arrayAccountChild as $keyAccountChild => $valueAccountChild) {
						$arrayNew[$keyAccount][$keyAccountChild] = 1;
					}
				}
			}
		}

		$data = array(
			'arrIdAccountTitle'   => $arrayNew,
			'arrIdAccountTitleFS' => $arrayNewFS,
		);

		return $data;
	}

	/**
		 (array(
			'arrIdAccountTitle' => array(),
			'vars'              => $arr['varsFS']['jsonJgaapAccountTitle'. $key],
			'strFlagDirect'     => $strFlagDirect,
		 ))
	 */
	protected function _getVarsItemJgaapFSLoop($arr)
	{
		$arrIdAccountTitle = &$arr['arrIdAccountTitle'];

		$array = &$arr['vars'];
		foreach ($array as $key => $value) {
			if (!is_null($value['vars']['flagUse'])) {
				$id = $value['vars']['varsJgaapCS'][$arr['strFlagDirect']]['idAccountTitleMinus'];
				$arr['arrIdAccountTitle'][$id][$value['vars']['idTarget']] = 1;
				$id = $value['vars']['varsJgaapCS'][$arr['strFlagDirect']]['idAccountTitlePlus'];
				$arr['arrIdAccountTitle'][$id][$value['vars']['idTarget']] = 1;
			}

			if ($value['child']) {
				$data = $this->_getVarsItemJgaapFSLoop(array(
					'vars'              => $array[$key]['child'],
					'strFlagDirect'     => $arr['strFlagDirect'],
					'arrIdAccountTitle' => $arr['arrIdAccountTitle'],
				));
				$array[$key]['child'] = $data['vars'];
				$arrIdAccountTitle =  $data['arrIdAccountTitle'];
			}
		}

		return $arr;
	}

	/**
	 (array(
			'varsItem'   => $varsItem,
			'flagFS'     => $arr['flagFS'],
			'flagDirect' => $arr['flagDirect'],
			'idTarget'   => $arr['idTarget'],
			'arrValue'   => $arr['arrValue'],
	 ))
	 */
	protected function _checkAddValue($arr)
	{
		global $classEscape;

		$strFlagDirect = 'varsInDirect';
		if ($arr['flagDirect']) {
			$strFlagDirect = 'varsDirect';
		}

		$arrayBlock =array();
		$arrayBlock = $this->_getTreeBlock(array(
			'vars'     => $arr['varsItem']['varsFS']['jsonJgaapFS'. $arr['flagFS']][$strFlagDirect],
			'idTarget' => $arr['idTarget'],
		));

		if (!$arrayBlock) {
			if ($arr['arrValue']['arrCommaIdAccountTitle']) {
				$arrayData = $classEscape->splitCommaArrayData(array('data' => $arr['arrValue']['arrCommaIdAccountTitle']));
				foreach ($arrayData as $key => $value) {
					if ($value == 'insertPoint') {
						continue;
					}
					$arrayBlockTemp = $this->_getTreeBlock(array(
						'vars'     => $arr['varsItem']['varsFS']['jsonJgaapFS'. $arr['flagFS']][$strFlagDirect],
						'idTarget' => $value,
					));
					if ($arrayBlockTemp) {
						$arr['idTarget'] = $value;
						$arrayBlock = $arrayBlockTemp;
						break;
					}
				}
			}
			if (!$arrayBlock) {
				return 'arrayblock';
			}
		}

		//
		$varsTarget = array();
		foreach ($arrayBlock as $key => $value) {
			if ($value['vars']['idTarget'] == $arr['idTarget']) {
				$varsTarget = $value;
				break;
			}
		}

		if (!(int) $varsTarget['vars']['flagSortUse']) {
			return 'flagSortUse';
		}

		//
		$strTitle = $arr['arrValue']['strTitle'];
		$flagNum = $this->_checkTreeStrTitle(array(
			'vars'      => $arr['varsItem']['varsFS']['jsonJgaapFS'. $arr['flagFS']][$strFlagDirect],
			'strTarget' => $strTitle,
			'num'       => 0,
		));
		if ($flagNum) {
			return 'strTitleTempNext';
		}

		$strDirect = 'inDirect';
		if ($arr['flagDirect']) {
			$strDirect = 'direct';
		}
		$idAccountTitle = $arr['arrValue']['idAccountTitle'];
		$idAccountTitleCustom = 'custom_' . $arr['flagFS'] . '_' . $strDirect . '_' . $idAccountTitle;
		$arrayIdAccountTitle = array($idAccountTitle, $idAccountTitleCustom);
		foreach ($arrayIdAccountTitle as $key => $value) {
			$flag = $this->_checkValueIdAccountTitleFS(array(
				'vars'      => $arr['varsItem']['varsFS']['jsonJgaapFS'. $arr['flagFS']][$strFlagDirect],
				'strTarget' => $value,
				'num'       => 0,
			));
			if ($flag) {
				return 'idAccountTitleTempNext';
			}
		}
	}

	protected function _checkValueIdAccountTitleFS($arr)
	{
		$array = &$arr['vars'];
		$num = $arr['num'];

		foreach ($array as $key => $value) {
			if (!is_null($value['vars']['flagUse'])) {
				if ($value['vars']['idTarget'] == $arr['strTarget']) {
					if ($arr['strSelf']) {
						if ($value['vars']['idTarget'] != $arr['strSelf']) {
							$num++;
						}
					} else {
						$num++;
					}
				}
			}
			if ($value['child']) {
				$num += $this->_checkValueIdAccountTitleFS(array(
					'vars'      => $array[$key]['child'],
					'strTarget' => $arr['strTarget'],
					'num'       => 0,
					'strSelf'   => $arr['strSelf'],
				));
			}
		}

		return $num;
	}

	/**
		(array(
			'flagStatus'      => $arr['flagStatus'],
			'varsItem'        => $varsItem,
			'flagFS'          => $arr['flagFS'],
			'flagDirect'      => $arr['flagDirect'],
			'idTarget'        => $arr['idTarget'],
			'arrValue'        => $arr['arrValue'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _setAddDb($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$varsFS = $this->_getValueVarsFS(array(
			'flagStatus'  => $arr['flagStatus'],
			'arrValue'    => $arr['arrValue'],
			'varsItem'    => $arr['varsItem'],
			'flagFS'      => $arr['flagFS'],
			'flagDirect'  => $arr['flagDirect'],
			'idTarget'    => $arr['idTarget'],
		));

		$jsonAccountTitle = json_encode($varsFS);
		$flag = $this->checkTextSize(array(
			'flagReturn' => 1,
			'str'        => $jsonAccountTitle,
		));
		if ($flag) {
			return 'errorDataMax';
		}

		$strAccountTitle = 'jsonJgaapFS'. $arr['flagFS'];

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
			'flagStatus'  => $arr['flagStatus'],
			'varsItem'    => $varsItem,
			'flagFS'      => $arr['flagFS'],
			'idTarget'    => $arr['idTarget'],
			'arrValue'    => $arr['arrValue'],
			'flagDefault' => $arr['arrValue']['flagDefault'],
		))
	 */
	protected function _getValueVarsFS($arr)
	{
		global $classEscape;

		$strDirect = 'inDirect';
		if ($arr['flagDirect']) {
			$strDirect = 'direct';
		}

		$strFlagDirect = 'varsInDirect';
		if ($arr['flagDirect']) {
			$strFlagDirect = 'varsDirect';
		}

		$idTarget = $arr['arrValue']['idAccountTitle'];
		if ($arr['flagStatus'] == 'add' || ($arr['flagStatus'] == 'edit' && !$arr['flagDefault'])) {
			$idTarget = 'custom_' . $arr['flagFS'] . '_' . $strDirect . '_' . $arr['arrValue']['idAccountTitle'];
		}

		$arrayBlock = $this->_getTreeBlock(array(
			'vars'     => $arr['varsItem']['varsFS']['jsonJgaapFS' . $arr['flagFS']][$strFlagDirect],
			'idTarget' => $arr['idTarget'],
		));

		$varsTarget = array();
		if ($arr['flagStatus'] == 'edit') {
			foreach ($arrayBlock as $key => $value) {
				if ($value['vars']['idTarget'] == $arr['idTarget']) {
					$varsTarget = $value;
					break;
				}
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


		$arrayNext = array();
		$arrayData = $classEscape->splitCommaArrayData(array('data' => $arr['arrValue']['arrCommaIdAccountTitle']));
		if ($arrayBlock[0]['vars']['idTarget'] == 'currentTermProfitOrLossPre') {
			array_unshift($arrayData, 'currentTermProfitOrLossPre');
		}

		foreach ($arrayData as $key => $value) {
			if ($value == 'insertPoint') {
				continue;
			}
			$arrayNext[] = $value;
		}

		$flag = 0;
		$arrayPrev = $arrayIdAccountTitle;
		foreach ($arrayPrev as $key => $value) {
			if ($arrayNext[$key] != $value) {
				$flag = 1;
			}
		}

		if (count($arrayPrev) != count($arrayNext)) {
			$flag = 1;
		}

		if ($arr['flagStatus'] == 'add') {
			if (!$flag) {
				$arrayIdAccountTitle = $arrayData;

			} else {
				$flagSaleNet = 0;
				foreach ($arrayPrev as $key => $value) {
					if ($value == 'saleNet') {
						$flagSaleNet = 1;
					}
				}
				if ($flagSaleNet) {
					$flagCheckSaleNetUp = 1;
					foreach ($arrayData as $key => $value) {
						if ($value == 'saleNet') {
							$flagCheckSaleNetUp = 0;

						} elseif ($value == 'insertPoint') {
							break;
						}
					}

					if ($flagCheckSaleNetUp) {
						$arrayNew = array();
						foreach ($arrayPrev as $key => $value) {
							if ($value == 'saleNet') {
								$arrayNew[] = 'insertPoint';
							}
							$arrayNew[] = $value;
						}
						$arrayIdAccountTitle = $arrayNew;

					} else {
						$arrayPrev[] = 'insertPoint';
						$arrayIdAccountTitle = $arrayPrev;
					}

				} else {
					$arrayPrev[] = 'insertPoint';
					$arrayIdAccountTitle = $arrayPrev;
				}
			}

		} else {
			if (!$flag) {
				$arrayIdAccountTitle = $arrayData;
			}
		}

		$array = $arrayIdAccountTitle;
		$arrayNew = array();
		$tmplVars = $arr['varsItem']['varsFSItem']['varsAccountTitleFSCS'];
		foreach ($array as $key => $value) {
			if ($arr['flagStatus'] == 'add') {
				if ($value == 'insertPoint') {
					$tmplVars['strTitle'] = $arr['arrValue']['strTitle'];
					$tmplVars['vars'] = array(
						'idTarget'    => $idTarget,
						'flagUse'     => (int) $arr['arrValue']['flagUse'],
						'flagSortUse' => 1,
						'varsValue'   => array(),
					);
					$arrayNew[] = $tmplVars;

				} else {
					$arrayNew[] = $arrayCheck[$value];
				}

			} else {
				if ($value == $arr['idTarget']) {
					$flagSortUse = (int) $varsTarget['vars']['flagSortUse'];
					$arrayCheck[$value]['strTitle'] = $arr['arrValue']['strTitle'];
					$arrayCheck[$value]['vars'] = array(
						'idTarget'    => $idTarget,
						'flagUse'     => (int) $arr['arrValue']['flagUse'],
						'flagSortUse' => $flagSortUse,
						'varsValue'   => array(),
					);
				}
				$arrayNew[] = $arrayCheck[$value];
			}
		}

		$varsFS = $this->_insertTreeBlock(array(
			'vars'       => $arr['varsItem']['varsFS']['jsonJgaapFS'. $arr['flagFS']][$strFlagDirect],
			'idTarget'   => $arr['idTarget'],
			'varsTarget' => $arrayNew,
		));

		$arr['varsItem']['varsFS']['jsonJgaapFS'. $arr['flagFS']][$strFlagDirect] = $varsFS;

		return $arr['varsItem']['varsFS']['jsonJgaapFS'. $arr['flagFS']];
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

	/**
		(array(
			'flagStatus'      => $flagStatus,
			'numFiscalPeriod' => $numFiscalPeriod,
			'flagFS'          => $flagFS,
			'idTarget'        => $idTarget,
			'arrValue' => array(
				'strTitle'               => $arrValue['arr']['strTitle'],
				'idAccountTitle'         => $arrValue['arr']['idAccountTitle'],
				'flagDebit'              => $arrValue['arr']['flagDebit'],
				'arrCommaIdAccountTitle' => $arrValue['arr']['arrCommaIdAccountTitle'],
				'flagUse'                => 1,
			),
		))
	 */
	protected function _iniEdit($arr)
	{
		$varsItem = $this->_getVarsItem(array(
			'flagFS'          => $arr['flagFS'],
			'flagDirect'      => $arr['flagDirect'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$flag = $this->_checkEditValue(array(
			'varsItem'        => $varsItem,
			'flagFS'          => $arr['flagFS'],
			'flagDirect'      => $arr['flagDirect'],
			'idTarget'        => $arr['idTarget'],
			'arrValue'        => &$arr['arrValue'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		if ($flag) {
			if ($flag == 'none') {
				return;

			} elseif ($flag == 'idAccountTitleTempNext' || $flag == 'strTitleTempNext') {
				return $flag;
			}
		}

		$flag = $this->_setEditDb(array(
			'flagStatus'      => $arr['flagStatus'],
			'flagEditCheck'   => $flag,
			'varsItem'        => $varsItem,
			'flagFS'          => $arr['flagFS'],
			'flagDirect'      => $arr['flagDirect'],
			'idTarget'        => $arr['idTarget'],
			'arrValue'        => $arr['arrValue'],
			'flagDefault'     => $arr['arrValue']['flagDefault'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		if ($flag) {
			return $flag;
		}
	}

	/**
		(array(
			'varsItem'        => $varsItem,
			'flagFS'          => $arr['flagFS'],
			'idTarget'        => $arr['idTarget'],
			'arrValue'        => &$arr['arrValue'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _checkEditValue($arr)
	{
		$strFlagDirect = 'varsInDirect';
		if ($arr['flagDirect']) {
			$strFlagDirect = 'varsDirect';
		}

		$varsFS = $arr['varsItem']['varsFS']['jsonJgaapFS' . $arr['flagFS']][$strFlagDirect];
		$varsFS = $this->_getFlagUseLog(array(
			'vars'              => $varsFS,
			'arrIdAccountTitle' => $arr['varsItem']['varsJgaapFS']['arrIdAccountTitle'],
			'numFiscalPeriod'   => $arr['numFiscalPeriod'],
			'idTarget'          => $arr['idTarget'],
		));

		$arrayBlock = $this->_getTreeBlock(array(
			'vars'     => $varsFS,
			'idTarget' => $arr['idTarget'],
		));
		if (!$arrayBlock) {
			return 'none';
		}

		$varsTarget = array();
		foreach ($arrayBlock as $key => $value) {
			if ($value['vars']['idTarget'] == $arr['idTarget']) {
				$varsTarget = $value;
				$arr['arrValue']['flagDefault'] = (int) $varsTarget['flagDefault'];
				break;
			}
		}

		$strTitle = $arr['arrValue']['strTitle'];
		$flagNum = $this->_checkTreeStrTitle(array(
			'vars'      => $varsFS,
			'strTarget' => $strTitle,
			'num'       => 0,
			'strSelf'   => $varsTarget['strTitle'],
			'idSelf'    => $varsTarget['vars']['idTarget'],
		));
		if ($flagNum) {
			return 'strTitleTempNext';
		}

		$strDirect = 'inDirect';
		if ($arr['flagDirect']) {
			$strDirect = 'direct';
		}

		$flagEditCheck = array();

		$idAccountTitle = $arr['arrValue']['idAccountTitle'];
		$flag = 0;
		if ((int) $varsTarget['flagDefault']) {
			if ($idAccountTitle != $varsTarget['vars']['idTarget']) {
				$idAccountTitle = $varsTarget['vars']['idTarget'];
				$arr['arrValue']['idAccountTitle'] = $idAccountTitle;
			}

		} else {
			$idAccountTitleCustom = 'custom_' . $arr['flagFS'] . '_' . $strDirect . '_' . $idAccountTitle;
			if ($idAccountTitleCustom != $varsTarget['vars']['idTarget']) {
				$flag = 1;
				$flagEditCheck['idAccountTitle'] = 1;
			}
		}

		//idAccountTitle
		if ($flag) {
			$idAccountTitleCustom = 'custom_' . $arr['flagFS'] . '_' . $strDirect . '_' . $idAccountTitle;
			$arrayIdAccountTitle = array($idAccountTitle, $idAccountTitleCustom);
			foreach ($arrayIdAccountTitle as $key => $value) {
				$flag = $this->_checkValueIdAccountTitleFS(array(
					'vars'      => $arr['varsItem']['varsFS']['jsonJgaapFS'. $arr['flagFS']][$strFlagDirect],
					'strTarget' => $value,
					'num'       => 0,
					'strSelf'   => $arr['idTarget'],
				));
				if ($flag) {
					return 'idAccountTitleTempNext';
				}
			}

			if ($varsTarget['vars']['flagUseAccountTitle']) {
				$idAccountTitle = $varsTarget['vars']['idTarget'];
				$arr['arrValue']['idAccountTitle'] = $idAccountTitle;
				$flagEditCheck['idAccountTitle'] = 0;
			}
		}

		return $flagEditCheck;
	}

	/**
			'flagStatus'      => $arr['flagStatus'],
			'flagEditCheck'   => $flag,
			'varsItem'        => $varsItem,
			'flagFS'          => $arr['flagFS'],
			'flagDirect'      => $arr['flagDirect'],
			'idTarget'        => $arr['idTarget'],
			'arrValue'        => $arr['arrValue'],
			'flagDefault'     => $arr['arrValue']['flagDefault'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
	 */
	protected function _setEditDb($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$varsFS = $this->_getValueVarsFS(array(
			'flagStatus'  => $arr['flagStatus'],
			'arrValue'    => $arr['arrValue'],
			'varsItem'    => $arr['varsItem'],
			'flagFS'      => $arr['flagFS'],
			'flagDirect'  => $arr['flagDirect'],
			'idTarget'    => $arr['idTarget'],
			'flagDefault' => $arr['flagDefault'],
		));

		$jsonAccountTitle = json_encode($varsFS);
		$flag = $this->checkTextSize(array(
			'flagReturn' => 1,
			'str'        => $jsonAccountTitle,
		));
		if ($flag) {
			return 'errorDataMax';
		}
		$strAccountTitle = 'jsonJgaapFS'. $arr['flagFS'];

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

		if ($arr['flagEditCheck']['idAccountTitle']) {
			$flag = $this->_setEditDbDetail(array(
				'flagEditCheck'   => $arr['flagEditCheck'],
				'arrValue'        => $arr['arrValue'],
				'varsItem'        => $arr['varsItem'],
				'flagFS'          => $arr['flagFS'],
				'flagDirect'      => $arr['flagDirect'],
				'idTarget'        => $arr['idTarget'],
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
			));
			if ($flag) {
				return $flag;
			}
		}
	}

	/**
		(array(
			'flagEditCheck'   => $arr['flagEditCheck'],
			'arrValue'        => $arr['arrValue'],
			'varsItem'        => $arr['varsItem'],
			'flagFS'          => $arr['flagFS'],
			'flagDirect'      => $arr['flagDirect'],
			'idTarget'        => $arr['idTarget'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _setEditDbDetail($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$strDirect = 'inDirect';
		if ($arr['flagDirect']) {
			$strDirect = 'direct';
		}
		$idAccountTitle = $arr['arrValue']['idAccountTitle'];
		$idAccountTitleCustom = 'custom_' . $arr['flagFS'] . '_' . $strDirect . '_' . $idAccountTitle;

		$strFlagDirect = 'varsInDirect';
		if ($arr['flagDirect']) {
			$strFlagDirect = 'varsDirect';
		}

		$arrayFSList = $this->_getFSList(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		foreach ($arrayFSList as $keyFSList => $valueFSList) {
			if (!$varsFS['jsonJgaapAccountTitle'. $keyFSList]) {
				continue;
			}
			$arrIdTarget = $arr['varsItem']['varsJgaapFS']['arrIdAccountTitleFS'][$keyFSList][$arr['idTarget']];
			if (!$arrIdTarget) {
				continue;
			}
			foreach ($arrIdTarget as $keyTarget => $valueTarget) {
				$varsFS['jsonJgaapAccountTitle'. $keyFSList] = $this->_setEditDbDetailLoop(array(
					'idValue'       => $idAccountTitleCustom,
					'arrValue'      => $arr['arrValue'],
					'vars'          => $varsFS['jsonJgaapAccountTitle'. $keyFSList],
					'strFlagDirect' => $strFlagDirect,
					'idTarget'      => $keyTarget,
					'idTargetCS'    => $arr['idTarget'],
					'flagEditCheck' => $arr['flagEditCheck'],
				));
			}

			$jsonAccountTitle = json_encode($varsFS['jsonJgaapAccountTitle'. $keyFSList]);
			$flag = $this->checkTextSize(array(
				'flagReturn' => 1,
				'str'        => $jsonAccountTitle,
			));
			if ($flag) {
				return 'errorDataMax';
			}
			$strAccountTitle = 'jsonJgaapAccountTitle'. $keyFSList;

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
						'strColumn'     => 'id',
						'flagCondition' => 'eq',
						'value'         => $varsFS['id'],
					),
				),
				'arrValue'  => $arrDbValue,
			));
		}
	}

	/**
		(array(
			'idValue'       => $idAccountTitleCustom,
			'arrValue'      => $arr['arrValue'],
			'vars'          => $varsFS['jsonJgaapAccountTitle'. $keyFSList],
			'strFlagDirect' => $strFlagDirect,
			'idTarget'      => $keyTarget,
			'idTargetCS'    => $arr['idTarget'],
			'flagEditCheck' => $arr['flagEditCheck'],
		))
	 */
	protected function _setEditDbDetailLoop($arr)
	{
		$array = &$arr['vars'];

		foreach ($array as $key => $value) {
			if (!is_null($value['vars']['flagUse'])) {
				if ($value['vars']['idTarget'] == $arr['idTarget']) {
					if ($arr['flagEditCheck']['idAccountTitle']) {
						if ($value['vars']['varsJgaapCS'][$arr['strFlagDirect']]['idAccountTitleMinus'] == $arr['idTargetCS']) {
							$array[$key]['vars']['varsJgaapCS'][$arr['strFlagDirect']]['idAccountTitleMinus'] = $arr['idValue'];
						}
						if ($value['vars']['varsJgaapCS'][$arr['strFlagDirect']]['idAccountTitlePlus'] == $arr['idTargetCS']) {
							$array[$key]['vars']['varsJgaapCS'][$arr['strFlagDirect']]['idAccountTitlePlus'] = $arr['idValue'];
						}
					}
				}
			}
			if ($value['child']) {
				$array[$key]['child'] = $this->_setEditDbDetailLoop(array(
					'vars'          => $array[$key]['child'],
					'idTarget'      => $arr['idTarget'],
					'idTargetCS'    => $arr['idTargetCS'],
					'strFlagDirect' => $arr['strFlagDirect'],
					'flagEditCheck' => $arr['flagEditCheck'],
					'idValue'       => $arr['idValue'],
				));
			}
		}

		return $array;
	}

	/**
		'flagStatus'      => 'delete',
		'numFiscalPeriod' => $numFiscalPeriod,
		'flagFS'          => $vars['flagFS'],
		'flagDirect'      => $flagDirect,
		'idTarget'        => $idTarget,
	 */
	protected function _iniDelete($arr)
	{
		$varsItem = $this->_getVarsItem(array(
			'flagFS'          => $arr['flagFS'],
			'flagDirect'      => $arr['flagDirect'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$flag = $this->_checkDeleteValue(array(
			'varsItem'        => $varsItem,
			'flagFS'          => $arr['flagFS'],
			'flagDirect'      => $arr['flagDirect'],
			'idTarget'        => $arr['idTarget'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		if ($flag) {
			return;
		}

		$this->_setDeleteDb(array(
			'varsItem'        => $varsItem,
			'flagFS'          => $arr['flagFS'],
			'flagDirect'      => $arr['flagDirect'],
			'idTarget'        => $arr['idTarget'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
	}

	/**
		(array(
			'varsItem'        => $varsItem,
			'flagFS'          => $arr['flagFS'],
			'flagDirect'      => $arr['flagDirect'],
			'idTarget'        => $arr['idTarget'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _checkDeleteValue($arr)
	{
		$strFlagDirect = 'varsInDirect';
		if ($arr['flagDirect']) {
			$strFlagDirect = 'varsDirect';
		}

		$varsFS = $arr['varsItem']['varsFS']['jsonJgaapFS'. $arr['flagFS']][$strFlagDirect];
		$varsFS = $this->_getFlagUseLog(array(
			'vars'              => $varsFS,
			'arrIdAccountTitle' => $arr['varsItem']['varsJgaapFS']['arrIdAccountTitle'],
			'numFiscalPeriod'   => $arr['numFiscalPeriod'],
			'idTarget'          => $arr['idTarget'],
		));
		$arrayBlock = $this->_getTreeBlock(array(
			'vars'     => $varsFS,
			'idTarget' => $arr['idTarget'],
		));
		if (!$arrayBlock) {
			return __LINE__;
		}

		$varsTarget = array();
		foreach ($arrayBlock as $key => $value) {
			if ($value['vars']['idTarget'] == $arr['idTarget']) {
				$varsTarget = $value;
				break;
			}
		}
		if ((int) $varsTarget['flagDefault']) {
			return __LINE__;
		}

		if ($varsTarget['vars']['flagUseAccountTitle']) {
			return __LINE__;
		}
	}

	/**
			'varsItem'        => $varsItem,
			'flagFS'          => $arr['flagFS'],
			'idTarget'        => $arr['idTarget'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
	 */
	protected function _setDeleteDb($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$strFlagDirect = 'varsInDirect';
		if ($arr['flagDirect']) {
			$strFlagDirect = 'varsDirect';
		}

		$arr['varsItem']['varsFS']['jsonJgaapFS' . $arr['flagFS']][$strFlagDirect] = $this->_getVarsDelete(array(
			'vars'     => $arr['varsItem']['varsFS']['jsonJgaapFS'. $arr['flagFS']][$strFlagDirect],
			'idTarget' => $arr['idTarget'],
		));
		$varsFS = $arr['varsItem']['varsFS']['jsonJgaapFS' . $arr['flagFS']];

		$jsonAccountTitle = json_encode($varsFS);

		$strAccountTitle = 'jsonJgaapFS'. $arr['flagFS'];

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
			'flagStatus'      => 'back',
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'flagFS'          => $value['flagFS'],
			'idTarget'        => $key,
			'flagDirect'      => ($valueStrDirect == 'varsDirect')? 1 : 0,
			'flagReverse'    => 1,
		))
	 */
	protected function _iniBack($arr)
	{
		$numFiscalPeriodPrev = $arr['numFiscalPeriod'] - 1;
		if ($arr['flagReverse']) {
			$numFiscalPeriodPrev = $arr['numFiscalPeriod'];
			$arr['numFiscalPeriod'] = $numFiscalPeriodPrev - 1;
		}

		if ($arr['idTarget'] == 'none' || $arr['idTarget'] == 'cash') {
			return;
		}



		$varsItem = $this->_getBackVarsItem(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$varsItemPrev = $this->_getBackVarsItem(array(
			'numFiscalPeriod' => $numFiscalPeriodPrev,
		));

		$flag = $this->_setBackDb(array(
			'varsItem'        => $varsItem,
			'varsItemPrev'    => $varsItemPrev,
			'flagFS'          => $arr['flagFS'],
			'idTarget'        => $arr['idTarget'],
			'flagDirect'      => $arr['flagDirect'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		return $flag;
	}

	/**

	 */
	protected function _getBackVarsItem($arr)
	{
		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$data = array(
			'varsFS' => $varsFS,
		);

		return $data;
	}

	/**
		(array(
			'varsItem'        => $varsItem,
			'varsItemPrev'    => $varsItemPrev,
			'flagFS'          => $arr['flagFS'],
			'idTarget'        => $arr['idTarget'],
			'flagDirect'      => $arr['flagDirect'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _setBackDb($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$varsFS = $this->_getBackValueVarsFS(array(
			'varsItem'     => $arr['varsItem'],
			'varsItemPrev' => $arr['varsItemPrev'],
			'flagFS'       => $arr['flagFS'],
			'idTarget'     => $arr['idTarget'],
			'flagDirect'   => $arr['flagDirect'],
		));

		$jsonAccountTitle = json_encode($varsFS);
		$flag = $this->checkTextSize(array(
			'flagReturn' => 1,
			'str'        => $jsonAccountTitle,
		));
		if ($flag) {
			return 'errorDataMax';
		}

		$strAccountTitle = 'jsonJgaapFS'. $arr['flagFS'];

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

		$this->_setBackDbFSId(array(
			'idColumn'   => $strAccountTitle,
			'idTarget'   => $arr['idTarget'],
			'flagDirect' => $arr['flagDirect'],
		));
	}

	/**
	 (array(
		 'idColumn'     =>'',
		 'idTarget' => '',
	 ))
	 */
	protected function _setBackDbFSId($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strFlagDirect = 'varsInDirect';
		if ($arr['flagDirect']) {
			$strFlagDirect = 'varsDirect';
		}

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingFSId' . $strNation,
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
			),
		));

		$arrDbColumn = array($arr['idColumn']);

		$varsFSId = $rows['arrRows'][0][$arr['idColumn']];
		if (!$varsFSId) {
			$varsFSId = array();
		}

		unset($varsFSId[$strFlagDirect][$arr['idTarget']]);

		$jsonId = json_encode($varsFSId);
		$arrDbValue = array($jsonId);

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable' => 'accountingFSId' . $strNation,
			'arrColumn' => $arrDbColumn,
			'flagAnd'  => 1,
			'arrWhere' => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idEntity',
					'flagCondition' => 'eq',
					'value'         => $varsPluginAccountingAccount['idEntityCurrent'],
				),
			),
			'arrValue'  => $arrDbValue,
		));

		$this->_updateDbPreferenceStamp(array('strColumn' => 'fSId'));
	}

	/**
		(array(
			'varsItem'     => $arr['varsItem'],
			'varsItemPrev' => $arr['varsItemPrev'],
			'flagFS'       => $arr['flagFS'],
			'idTarget'     => $arr['idTarget'],
			'flagDirect'   => $arr['flagDirect'],
		))
	 */
	protected function _getBackValueVarsFS($arr)
	{
		$strFlagDirect = 'varsInDirect';
		if ($arr['flagDirect']) {
			$strFlagDirect = 'varsDirect';
		}

		$arrayBlockPrev = $this->_getTreeBlock(array(
			'vars'     => $arr['varsItemPrev']['varsFS']['jsonJgaapFS' . $arr['flagFS']][$strFlagDirect],
			'idTarget' => $arr['idTarget'],
		));

		$idTargetInsert = $arrayBlockPrev[0]['vars']['idTarget'];
		$varsTargetPrev = array();
		foreach ($arrayBlockPrev as $key => $value) {
			if ($value['vars']['idTarget'] == $arr['idTarget']) {
				$varsTargetPrev = $value;
				break;
			}
			$idTargetInsert = $value['vars']['idTarget'];
		}

		$arrayPrev = array();
		foreach ($arrayBlockPrev as $key => $value) {
			if ($varsTargetPrev['vars']['idTarget'] == $value['vars']['idTarget']) {
				continue;
			}
			$arrayPrev[] = $value['vars']['idTarget'];
		}

		$idTarget = '';
		foreach ($arrayBlockPrev as $key => $value) {
			$arrayBlockNext = $this->_getTreeBlock(array(
				'vars'     => $arr['varsItem']['varsFS']['jsonJgaapFS' . $arr['flagFS']][$strFlagDirect],
				'idTarget' => $value['vars']['idTarget'],
			));
			if ($arrayBlockNext) {
				$idTarget = $value['vars']['idTarget'];
				break;
			}
		}

		$arrayNext = array();
		foreach ($arrayBlockNext as $key => $value) {
			$arrayNext[] = $value['vars']['idTarget'];
		}

		$flag = 0;
		foreach ($arrayPrev as $key => $value) {
			if ($arrayNext[$key] != $value) {
				$flag = 1;
			}
		}
		if (count($arrayPrev) != count($arrayNext)) {
			$flag = 1;
		}

		$arrayNew = array();
		if ($flag) {
			$flagSaleNet = 0;
			foreach ($arrayPrev as $key => $value) {
				if ($value == 'saleNet') {
					$flagSaleNet = 1;
				}
			}
			if ($flagSaleNet) {
				$flagCheckSaleNetUp = 1;
				foreach ($arrayBlockPrev as $key => $value) {
					if ($value == 'saleNet') {
						$flagCheckSaleNetUp = 0;

					} elseif ($value['vars']['idTarget'] == $arr['idTarget']) {
						break;
					}
				}

				if ($flagCheckSaleNetUp) {
					foreach ($arrayBlockNext as $key => $value) {
						if ($value['vars']['idTarget'] == 'saleNet') {
							$arrayNew[] = $varsTargetPrev;
						}
						$arrayNew[] = $value;
					}

				} else {
					$arrayNew = $arrayBlockNext;
					$arrayNew[] = $varsTargetPrev;
				}

			} else {
				$arrayNew = $arrayBlockNext;
				$arrayNew[] = $varsTargetPrev;
			}

		} else {
			foreach ($arrayBlockNext as $key => $value) {
				if ($idTargetInsert == $value['vars']['idTarget']) {
					$arrayNew[] = $value;
					$arrayNew[] = $varsTargetPrev;
					continue;
				}
				$arrayNew[] = $value;
			}
		}

		$varsFS = $this->_insertTreeBlock(array(
			'vars'       => $arr['varsItem']['varsFS']['jsonJgaapFS'. $arr['flagFS']][$strFlagDirect],
			'idTarget'   => $idTarget,
			'varsTarget' => $arrayNew,
		));

		$arr['varsItem']['varsFS']['jsonJgaapFS'. $arr['flagFS']][$strFlagDirect] = $varsFS;

		return $arr['varsItem']['varsFS']['jsonJgaapFS'. $arr['flagFS']];
	}
}
