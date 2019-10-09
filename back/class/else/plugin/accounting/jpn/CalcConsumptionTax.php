<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcConsumptionTax extends Code_Else_Plugin_Accounting_Jpn_Jpn
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
			return $this->$method($arr);

		} else {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}
	}

	/*
	 * 20191001 start
	 */

	/*
	 *
	 * 受信時処理
	(array(
	'flagStatus' => 'receiveValueConsumptionTaxReduced',
	'jsonDetail'   => $arrValue['arr']['jsonDetail],
	))
	*/
	protected function _iniReceiveValueConsumptionTaxReduced($arr)
	{
	    $arr['jsonDetail'] = $this->_getReceiveValueConsumptionTaxReduced(array(
	        'jsonDetail'   => $arr['jsonDetail'],
	    ));

	    return $arr['jsonDetail'];

	}

	protected function _getReceiveValueConsumptionTaxReduced($arr)
	{
	    $array = $arr['jsonDetail']['varsDetail'];

	    foreach ($array as $key => $value) {
	        $arrayStr = array('Debit', 'Credit');
	        foreach ($arrayStr as $keyStr => $valueStr) {
	            $strSide = 'arr' . $valueStr;
	            $array[$key][$strSide]['flagRateConsumptionTaxReduced'] = '';
	            if ($value[$strSide]['numRateConsumptionTax'] != '') {
	                $array[$key][$strSide]['flagRateConsumptionTaxReduced'] = 0;

                    //数字 == 文字だと通過するため　文字で統一
	                $strTarget = $value[$strSide]['numRateConsumptionTax'] . '';
	                if ($strTarget == '8_reduced') {

	                    $array[$key][$strSide]['numRateConsumptionTax'] = 8;
	                    $array[$key][$strSide]['flagRateConsumptionTaxReduced'] = 1;
                    }
	            }
	        }
	    }


 //exit;

	    $arr['jsonDetail']['varsDetail'] = $array;

	    return $arr['jsonDetail'];
	}

	/*
	*
	* 送信時処理
	(array(
	'flagStatus' => 'sendValueConsumptionTaxReduced',
	'jsonDetail'   => $arrValue['arr']['jsonDetail],
	))
	*/
	protected function _iniSendValueConsumptionTaxReduced($arr)
	{
	    $arr['jsonDetail'] = $this->_getSendValueConsumptionTaxReduced(array(
	        'jsonDetail'   => $arr['jsonDetail'],
	    ));

	    return $arr['jsonDetail'];

	}

	protected function _getSendValueConsumptionTaxReduced($arr)
	{
	    $array = $arr['jsonDetail']['varsDetail'];
	    foreach ($array as $key => $value) {
	        $arrayStr = array('Debit', 'Credit');
	        foreach ($arrayStr as $keyStr => $valueStr) {
	            $strSide = 'arr' . $valueStr;
	            if ($array[$key][$strSide]['flagRateConsumptionTaxReduced']) {
	                $array[$key][$strSide]['numRateConsumptionTax'] = '8_reduced';
	            }
	            $array[$key][$strSide]['flagRateConsumptionTaxReduced'] = '';
	        }
	    }

	    $arr['jsonDetail']['varsDetail'] = $array;

	    return $arr['jsonDetail'];
	}



	/*
	 * 20191001 end
	 */

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
		$flag = $this->_iniAdd(array(
			'arrRows'         => $arr['arrRowsAdd'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}
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
		$varsItem = $this->_getVarsItem(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		if ($varsItem['varsEntityNation']['flagConsumptionTaxFree']) {
			return;
		}

		/*
		 * 20191001 start
		 */
		$arrRows = $arr['arrRows'];
		$varsConsumptionTax = $this->_getValueAdd(array(
		    'arrRows'  => $arrRows,
		    'varsItem' => $varsItem,
		    'varsFS'   => $varsItem['varsFS'],
		));
		$jsonConsumptionTax = json_encode($varsConsumptionTax);

		$arrRows = $arr['arrRows'];
		$temp = $this->_getVarsConsumptionTaxDetail(array(
		    'arrRows'  => $arrRows,
		));

		//軽減税率の消費税率（5%,8%,10%）、5%,10%は軽減税率とは関係ないがプログラムが汚れる可能性があるので残して処理
		$varsReduced = $this->_getValueAdd(array(
		    'arrRows'  => $temp['arrRowsReduced'],
		    'varsItem' => $varsItem,
		    'varsFS'   => $varsItem['varsFSDetail']['varsReduced'],
		));

		//軽減税率以外の消費税率（5%,8%,10%）
		$varsOther = $this->_getValueAdd(array(
		    'arrRows'  => $temp['arrRowsOther'],
		    'varsItem' => $varsItem,
		    'varsFS'   => $varsItem['varsFSDetail']['varsOther'],
		));

		$varsConsumptionTaxDetail = array(
		    'varsReduced' => $varsReduced,
		    'varsOther'   => $varsOther,
		);

		$jsonConsumptionTaxDetail = json_encode($varsConsumptionTaxDetail);
		$flag = $this->_updateDb(array(
		    'jsonConsumptionTax'       => $jsonConsumptionTax,
		    'jsonConsumptionTaxDetail' => $jsonConsumptionTaxDetail,
		    'varsItem'                 => $varsItem,
		));

		/*
		$arrRows = $arr['arrRows'];
		$varsValue = $this->_getValueAdd(array(
		    'arrRows'  => $arrRows,
		    'varsItem' => $varsItem,
		));

		$flag = $this->_updateDb(array(
		    'varsValue' => $varsValue,
		    'varsItem'  => $varsItem,
		));
		*/
		/*
		 * 20191001 end
		 */

		if ($flag == 'errorDataMax') {
		    return $flag;
		}

		$this->_updateDbPreferenceStamp(array('strColumn' => 'fsValue'));
	}

	/*
	 * 20191001 start
	 */


	/**
	(array(
        'arrRows'    => array,
	))
	*/
	protected function _getVarsConsumptionTaxDetail($arr)
	{
	    $arrayRowsNew = array();
	    $arrayRowsNew['arrRowsReduced'] = array();
	    $arrayRowsNew['arrRowsOther'] = array();

	    $arrayRows = $arr['arrRows'];
	    foreach ($arrayRows as $keyRows => $valueRows) {
	        if ($valueRows['flagRateConsumptionTaxReduced']) {
	            $arrayRowsNew['arrRowsReduced'][] = $valueRows;
	        } else {
	            $arrayRowsNew['arrRowsOther'][] = $valueRows;
	        }
	    }

	    return $arrayRowsNew;
	}

	/*
	 * 20191001 end
	 */


	/**
		(array(
			'flagStatus' => 'Delete',
			'arrRows'    => array,
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _iniDelete($arr)
	{
		$arr['arrRows'] = $this->_getArrRowsReverse(array(
			'arrRows'         => $arr['arrRows'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		$flag = $this->_iniAdd(array(
			'arrRows'         => $arr['arrRows'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		if ($flag == 'errorDataMax') {
			return $flag;
		}
	}

	/**
		(array(
			'arrRows'    => array,
		))
	 */
	protected function _getArrRowsReverse($arr)
	{
		$array = &$arr['arrRows'];
		foreach ($array as $key => $value) {
			$array[$key]['flagDebit'] = ($array[$key]['flagDebit'])? 0 : 1;
		}

		return $array;
	}


	/**

	 */
	protected function _getVarsItem($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingFSValue' . $strNation,
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

		$varsConsumptionTax = $this->_getVarsConsumptionTax(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		$arrayFiscalPeriod = $this->_getVarsFlagFiscalPeriod(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		$array = $arrayFiscalPeriod;
		$varsFiscalPeriod = array();
		foreach ($array as $key => $value) {
			$varsFiscalPeriod[$value] = $this->_getVarsStampTerm(array(
				'varsFlag'         => array('flagFiscalPeriod' => $value),
				'varsEntityNation' => $varsEntityNation,
				'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			));
		}

		$data = array(
		    'varsFS'             => $varsFS['jsonConsumptionTax'],
		    /*
		     * 20191001 start
		     */
		    'varsFSDetail'       => $varsFS['jsonConsumptionTaxDetail'],
		    /*
		     * 20191001 end
		     */
			'varsConsumptionTax' => $varsConsumptionTax,
			'varsEntityNation'   => $varsEntityNation,
			'varsFiscalPeriod'   => $varsFiscalPeriod,
			'numFiscalPeriod'    => $arr['numFiscalPeriod'],
		);

		return $data;

	}

	/**
		(array(
			'arrRows'  => $arr['arrRows'],
			'varsItem' => $varsItem,
		))
	 */
	protected function _getValueAdd($arr)
	{
		$arrayNew = array();
		$array = $arr['varsItem']['varsFiscalPeriod'];

		foreach ($array as $key => $value) {
			$arrayNew[$key] = ($arr['varsFS'][$key])? $arr['varsFS'][$key] : array();
			$arrayRowsNew = array();
			$arrayRows = &$arr['arrRows'];
			foreach ($arrayRows as $keyRows => $valueRows) {
				if ($value['stampMin'] <= $valueRows['stampBook'] && $valueRows['stampBook'] <= $value['stampMax']) {
					$arrayRowsNew[] = $valueRows;
				}
			}

			if ($arrayRowsNew) {
				$arrayNew[$key] = $this->_getValueAddLoop(array(
					'arrRows'   => $arrayRowsNew,
					'varsItem'  => $arr['varsItem'],
					'varsValue' => $arr['varsFS'][$key],
				));
			}
		}



		return $arrayNew;
	}

	/**
		(array(
			'arrRows'  => $arr['arrRows'],
			'varsItem' => $varsItem,
		))
	 */
	protected function _getValueAddLoop($arr)
	{
		$flagConsumptionTaxIncluding = (int) $arr['varsItem']['varsEntityNation']['flagConsumptionTaxIncluding'];
		$varsValue = $arr['varsValue'];

		$strTax = 'simple';
		$strTaxRule = 'flagConsumptionTaxSimpleRule';
		if ((int) $arr['varsItem']['varsEntityNation']['flagConsumptionTaxGeneralRule']) {
			if ((int) $arr['varsItem']['varsEntityNation']['flagConsumptionTaxDeducted']) {
				$strTax = 'generalEach';
				$strTaxRule = 'flagConsumptionTaxGeneralRuleEach';

			} else {
				$strTax = 'generalProration';
				$strTaxRule = 'flagConsumptionTaxGeneralRuleProration';
			}
		}

		$arrayNew = $varsValue;
		$arrayRate = array(5, 8 ,10);
		$arrayTax = $arr['varsItem']['varsConsumptionTax'][$strTax];
		$array = &$arr['arrRows'];
		foreach ($array as $key => $value) {
			$numRateConsumptionTax = $value['numRateConsumptionTax'];
			if ($value[$strTaxRule] == 'none' || !$value[$strTaxRule]) {
				continue;
			}
			foreach ($arrayTax as $keyTax => $valueTax) {
				if ($valueTax['value'] == 'none') {
					continue;
				}

				//init vars
				$data = array();
				if (is_null($arrayNew[$valueTax['value']])) {
					if (is_null($varsValue[$valueTax['value']])) {
						if (preg_match("/^tax/", $valueTax['value'])) {
							foreach ($arrayRate as $keyRate => $valueRate) {
								$data[$valueRate]['inBody'] = 0;
								$data[$valueRate]['outBody'] = 0;
								$data[$valueRate]['otherBody'] = 0;
								$data[$valueRate]['includeBody'] = 0;
								$data[$valueRate]['totalBody'] = 0;

								$data[$valueRate]['inTax'] = 0;
								$data[$valueRate]['outTax'] = 0;
								$data[$valueRate]['otherTax'] = 0;
								$data[$valueRate]['includeTax'] = 0;
								$data[$valueRate]['totalTax'] = 0;

								$data[$valueRate]['inSum'] = 0;
								$data[$valueRate]['outSum'] = 0;
								$data[$valueRate]['otherSum'] = 0;
								$data[$valueRate]['includeSum'] = 0;
								$data[$valueRate]['totalSum'] = 0;
							}

						} else {
							if (preg_match("/^else/", $valueTax['value'])) {
								foreach ($arrayRate as $keyRate => $valueRate) {
									$data[$valueRate]['inSum'] = 0;
									$data[$valueRate]['outSum'] = 0;
									$data[$valueRate]['otherSum'] = 0;
									$data[$valueRate]['includeSum'] = 0;
									$data[$valueRate]['totalSum'] = 0;
								}

							} else {
								$data['inSum'] = 0;
								$data['outSum'] = 0;
								$data['otherSum'] = 0;
								$data['includeSum'] = 0;
								$data['totalSum'] = 0;
							}
						}

					} else {
						$data = $varsValue[$valueTax['value']];
					}

				} else {
					$data = $arrayNew[$valueTax['value']];
				}

				if ($valueTax['value'] == $value[$strTaxRule]) {
					$numValue = 0;
					$strType = '';

					if ($flagConsumptionTaxIncluding) {
						if (preg_match("/^tax/", $valueTax['value'])) {
							if ($value['flagConsumptionTaxWithoutCalc'] == 3) {
								$strType = 'otherTax';

							} else {
								$strType = 'includeBody';
							}

						} else {
							$strType = 'totalSum';
						}

					} else {
						$flagTax = 0;
						if ($value['idAccountTitle'] == 'suspensePaymentConsumptionTaxes'
							 || $value['idAccountTitle'] == 'suspenseReceiptOfConsumptionTaxes'
						) {
							$flagTax = 1;
						}

						if (preg_match("/^tax/", $valueTax['value'])) {
							if ($value['flagConsumptionTaxWithoutCalc'] == 1) {
								$strType = ($flagTax)? 'inTax' : 'inBody';

							} else if ($value['flagConsumptionTaxWithoutCalc'] == 2) {
								$strType = ($flagTax)? 'outTax' : 'outBody';

							} else if ($value['flagConsumptionTaxWithoutCalc'] == 3) {
								$strType = ($flagTax)? 'otherTax' : 'otherBody';
							}

						} else {
							$strType = 'totalSum';
						}

					}
					$numValue = $value['numValue'];
					if ($valueTax['flagDebit']) {
						if (!$value['flagDebit']) {
							$numValue *= (-1);
						}

					} else {
						if ($value['flagDebit']) {
							$numValue *= (-1);
						}
					}

					if ($strType != 'totalSum') {
						if (preg_match("/^tax/", $valueTax['value'])
							|| preg_match("/^else/", $valueTax['value'])
						) {
							$data[$numRateConsumptionTax][$strType] += $numValue;
						} else {
							$data[$strType] += $numValue;
						}

						if (preg_match("/^include/", $strType)) {
							$strTypeItemSum = 'includeSum';

						} elseif (preg_match("/^out/", $strType)) {
							$strTypeItemSum = 'outSum';

						} elseif (preg_match("/^other/", $strType)) {
							$strTypeItemSum = 'otherSum';

						} elseif (preg_match("/^in/", $strType)) {
							$strTypeItemSum = 'inSum';
						}

						if (preg_match("/^tax/", $valueTax['value'])
							|| preg_match("/^else/", $valueTax['value'])
						) {
							$data[$numRateConsumptionTax][$strTypeItemSum] += $numValue;

						} else {
							$data[$strTypeItemSum] += $numValue;
						}

						if (preg_match("/Tax$/", $strType)) {
							$strTypeSum = 'totalTax';

						} elseif (preg_match("/Body$/", $strType)) {
							$strTypeSum = 'totalBody';
						}

						if (preg_match("/^tax/", $valueTax['value'])
							|| preg_match("/^else/", $valueTax['value'])
						) {
							$data[$numRateConsumptionTax][$strTypeSum] += $numValue;

						} else {
							$data[$strTypeSum] += $numValue;
						}
						$strType = 'totalSum';
					}

					//totalSum
					if (preg_match("/^tax/", $valueTax['value'])
						|| preg_match("/^else/", $valueTax['value'])
					) {
						$data[$numRateConsumptionTax][$strType] += $numValue;

					} else {
						$data[$strType] += $numValue;
					}
					$arrayNew[$valueTax['value']] = $data;

				}
				$arrayNew[$valueTax['value']] = $data;
			}
		}

		return $arrayNew;
	}

	/**
		(array(
			'varsValue' => $varsValue,
		))
	 */
	protected function _updateDb($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$jsonConsumptionTax = $arr['jsonConsumptionTax'];
		$flag = $this->checkTextSize(array(
			'flagReturn' => 1,
			'str'        => $jsonConsumptionTax,
		));
		if ($flag) {
			return 'errorDataMax';
		}

		$jsonConsumptionTaxDetail = $arr['jsonConsumptionTaxDetail'];
		$flag = $this->checkTextSize(array(
		    'flagReturn' => 1,
		    'str'        => $jsonConsumptionTaxDetail,
		));
		if ($flag) {
		    return 'errorDataMax';
		}

		$classDb->updateRow(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingFSValue' . $strNation,
			'arrColumn' => array('jsonConsumptionTax', 'jsonConsumptionTaxDetail'),
			'flagAnd'   => 1,
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
					'value'         => $arr['varsItem']['numFiscalPeriod'],
				),
			),
		    'arrValue'  => array($jsonConsumptionTax, $jsonConsumptionTaxDetail),
		));

	}
}
