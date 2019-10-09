<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_FinancialStatementSS extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference' => 'financialStatementSSWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/financialStatementSS.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/financialStatementSS.php',
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
			$strChild = ucwords($varsRequest['query']['child']);
			$strExt = ucwords($varsRequest['query']['ext']);
			$str = $strExt . $strChild;

			$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/jpn/' . $str . ".php";
			if (!file_exists($path)) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
				}
				exit;
			}
			require_once($path);
			$strClass = 'Code_Else_Plugin_Accounting_Jpn_' . $str;
			$classCall = new $strClass;
			$classCall->run();

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
	 *
	 */
	protected function _iniJs()
	{
		global $classSmarty;
		global $varsPluginAccountingAccount;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsFlag' => $vars['varsFlag'],
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars' => &$vars,
		)));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $vars['varsFlag'],
		));

		$json = json_encode($vars);
		$classSmarty->assign('varsLoad', $json);
		$classSmarty->assign('numNews', $this->getNumNews());
		$contents = $classSmarty->fetch($this->_extSelf['pathTplJs']);

		$this->sendJs(array(
			'data' => $contents,
		));

	}


	/**
		(array(
			'flag' => '',
			'vars' => $vars,
		))
	 */
	protected function _getVarsItem($arr)
	{
		global $classDb;
		global $varsAccount;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsJgaapFS = $this->_getVarsItemJgaapFS(array(
			'arrIdAccountTitle' => array(),
			'vars'              => $varsFS['jsonJgaapAccountTitleBS'],
		));

		$varsFSValue = $this->_getVarsFSValue(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$numFiscalPeriodPrev = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] - 1;
		if ($numFiscalPeriodPrev < 0) {
			$numFiscalPeriodPrev = 0;
		}
		$varsFSValuePrev = $this->_getVarsFSValue(array(
			'numFiscalPeriod' => $numFiscalPeriodPrev,
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsFlagFiscalPeriod = $this->_getVarsFlagFiscalPeriod(array(
			'vars' => &$arr['vars'],
		));

		$varsFlagUnit = $this->_getVarsFlagUnit(array(
			'vars' => &$arr['vars'],
		));

		$varsAccountTitlePL = $this->_getArrSelectOption(array(
			'arrStrTitle'     => array(),
			'arrSelectTag'    => array(),
			'vars'            => $varsFS['jsonJgaapFSPL'],
			'flagBS'          => 0,
			'flagFS'          => 'PL',
			'strCR'           => '',
		));

		$data = array(
			'varsFS'               => $varsFS['jsonJgaapFSBS'],
			'varsJgaapFS'          => $varsJgaapFS,
			'varsAccountTitlePL'   => $varsAccountTitlePL,
			'varsFSBSValue'        => $varsFSValue['jsonJgaapFSBS'],
			'varsFSBSValuePrev'    => $varsFSValuePrev['jsonJgaapFSBS'],
			'varsFSPLValue'        => $varsFSValue['jsonJgaapFSPL'],
			'varsEntityNation'     => $varsEntityNation,
			'varsFlagFiscalPeriod' => $varsFlagFiscalPeriod,
			'varsFlagUnit'         => $varsFlagUnit,
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
	protected function _getArrSelectOption($arr)
	{
		$arrSelectTag = &$arr['arrSelectTag'];
		$arrStrTitle = &$arr['arrStrTitle'];

		$array = &$arr['vars'];
		foreach ($array as $key => $value) {
			$strTitleFS = ($arr['flagFS'] == 'CR')? '['. $arr['strCR']  .']' . $value['strTitle'] : $value['strTitle'];

			$data = array(
				'strTitle'                               => $value['strTitle'],
				'strTitleFS'                             => $strTitleFS,
				'flagDebit'                              => (int) $value['vars']['flagDebit'],
				'flagUse'                                => (is_null($value['vars']['flagUse']))? '' : (int) $value['vars']['flagUse'],
				'idAccountTitleJgaapFS'                  => (is_null($value['vars']['idAccountTitleJgaapFS']))? '' : $value['vars']['idAccountTitleJgaapFS'],
				'flagFS'                                 => $arr['flagFS'],
			);
			$arr['arrStrTitle'][$value['vars']['idTarget']] = $data;

			$arrLevel = preg_split("/-/", $value['id']);
			$num = count($arrLevel) - 2;
			$arrLevel = array();
			for ($i = 0 ; $i < $num; $i++) {
				$arrLevel[$i] = '';
			}
			$str =  ' ' . join('.', $arrLevel) . ' ';
			$strTitle = $str . $value['strTitle'];
			$strTitleFSTag = $str .  $strTitleFS;

			if (is_null($value['vars']['flagUse'])) {
				$arr['arrSelectTag'][] = array(
					'strTitle'     => $strTitle,
					'value'        => '',
					'flagDisabled' => 1,
				);

			} else {
				$arr['arrSelectTag'][] = array(
					'strTitle'     => $strTitleFSTag,
					'value'        => $value['vars']['idTarget'],
				);
			}

			if ($value['child']) {
				$dataTemp = $this->_getArrSelectOption(array(
					'vars'            => $array[$key]['child'],
					'arrSelectTag'    => $arr['arrSelectTag'],
					'arrStrTitle'     => $arr['arrStrTitle'],
					'flagBS'          => $arr['flagBS'],
					'flagFS'          => $arr['flagFS'],
					'strCR'           => $arr['strCR'],
				));
				$array[$key]['child'] = $dataTemp['vars'];
				$arrSelectTag =  $dataTemp['arrSelectTag'];
				$arrStrTitle =  $dataTemp['arrStrTitle'];
			}
		}

		return $arr;
	}

	/**
		(array(
			'arrIdAccountTitle'  => array(),
			'vars'         => array(),
		))
	 */
	protected function _getVarsItemJgaapFS($arr)
	{

		$arrIdAccountTitle = &$arr['arrIdAccountTitle'];

		$array = &$arr['vars'];
		foreach ($array as $key => $value) {
			if (!is_null($value['vars']['flagUse'])) {
				$arr['arrIdAccountTitle'][$value['vars']['idAccountTitleJgaapFS']][] = $value['vars']['idTarget'];
			}

			if ($value['child']) {
				$data = $this->_getVarsItemJgaapFS(array(
					'vars'              => $array[$key]['child'],
					'arrIdAccountTitle' => $arr['arrIdAccountTitle'],
				));
				$array[$key]['child'] = $data['vars'];
				$arrIdAccountTitle =  $data['arrIdAccountTitle'];
			}
		}

		return $arr;
	}

	/**

	 */
	protected function _getVarsFlagUnit($arr)
	{
		$array = $arr['vars']['portal']['varsNavi']['templateDetail'];
		$arrStrTitle = array();
		foreach ($array as $key => $value) {
			if ($value['id'] == 'FlagUnit') {
				$arrayOption = $value['arrayOption'];
				foreach ($arrayOption as $keyOption => $valueOption) {
					$arrStrTitle[$valueOption['value']]['strTitle'] = $valueOption['strTitle'];
				}
				break;
			}
		}

		$data = array(
			'arrStrTitle'  => $arrStrTitle,
		);

		return $data;
	}

	/**

	 */
	protected function _getVarsFlagFiscalPeriod($arr)
	{
		$array = $arr['vars']['portal']['varsNavi']['templateDetail'];
		$arrStrTitle = array();
		foreach ($array as $key => $value) {
			if ($value['id'] == 'FlagFiscalPeriod') {
				$arrayOption = $value['arrayOption'];
				foreach ($arrayOption as $keyOption => $valueOption) {
					$arrStrTitle[$valueOption['value']]['strTitle'] = $valueOption['strTitle'];
				}
				break;
			}
		}

		$data = array(
			'arrStrTitle'  => $arrStrTitle,
		);

		return $data;
	}

	/**
		(array(
			'vars'             => $vars,
		))
	 */
	protected function _updateVarsNavi($arr)
	{
		$vars = &$arr['vars'];

		$arrayNew = array();
		$array = &$vars['portal']['varsNavi']['templateDetail'];
		foreach ($array as $key => $value) {
			$arrayNew[] = $value;
		}
		$vars['portal']['varsNavi']['templateDetail'] = $arrayNew;


		return $vars['portal']['varsNavi']['templateDetail'];
	}


	/**
		(array(
			'vars'             => $vars,
			'varsEntityNation' => $varsEntityNation,
			'varsItem'         => $varsItem,
			'varsFlagFS'       => $varsFlagFS,
			'varsFlag'         => array(
				'flagFiscalPeriod'  => $flagFiscalPeriod,
				'idDepartment'      => $idDepartment,
				'flagFS'            => $flagFS,
				'flagUnit'          => $flagUnit,
				'flagCalc'          => $flagCalc,
			),
		))
	 */
	protected function _updateVars($arr)
	{
		global $classHtml;

		global $varsAccount;

		$varsFS = array();
		$array = $arr['varsItem']['varsFS'];
		foreach ($array as $key => $value) {
			if (preg_match("/^(netAssets|netAssetsSum)$/", $value['vars']['idTarget'])) {
				$varsFS[] = $value;
			}
		}

		$arr['vars']['portal']['varsList']['varsDetail'] = $this->_getAccountTitleValueColumn(array(
			'vars'     => $arr['vars'],
			'varsFS'   => $varsFS,
			'varsItem' => $arr['varsItem'],
			'varsFlag' => $arr['varsFlag'],
		));

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
			'vars'        => $arr['vars'],
			'varsFS'      => $arr['varsItem']['varsFS'],
			'varsFSValue' => $arr['varsItem']['varsFSValue'],
			'varsFlag'         => array(
				'flagFiscalPeriod'  => $flagFiscalPeriod,
				'flagUnit'          => $flagUnit,
				'flagCalc'          => $flagCalc,
			),
		))

	 */
	protected function _getAccountTitleValueColumn($arr)
	{
		global $classEscape;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$array = &$arr['varsFS'];

		$flagFiscalPeriod = $arr['varsFlag']['flagFiscalPeriod'];
		$varsTmpl = $arr['vars']['varsItem']['varsChild'];

		$varsFSValue = $arr['varsFSValue'];

		foreach ($array as $key => $value) {
			$idTarget = $value['vars']['idTarget'];
			$array[$key]['varsValue']['numValue'] = '';
			$array[$key]['varsValue']['strReason'] = '';
			$array[$key]['varsColumnDetail']['numValue'] = '';
			$array[$key]['varsColumnDetail']['strReason'] = '';
			$array[$key]['flagBtnUse'] = 0;
			$array[$key]['flagChildrenUse'] = 1;

			$array[$key]['varsPrint']['strTitle'] = $value['strTitle'];
			$array[$key]['varsPrint']['strTitle2'] = '';
			$array[$key]['varsPrint']['sumNext'] = '';
			$array[$key]['varsPrint']['strReason'] = '';

			if ($value['child']) {
				$array[$key]['child'] = $this->_getAccountTitleValueColumn(array(
					'varsFlag' => $arr['varsFlag'],
					'varsFS'   => $array[$key]['child'],
					'varsItem' => $arr['varsItem'],
					'vars'     => $arr['vars'],
				));
			} else {
				$array[$key]['child'] = array();
			}

			if (!is_null($value['vars']['flagUse'])) {
				if (!(int) $value['vars']['flagUse']) {
					$array[$key]['strClassFont'] = $arr['vars']['varsItem']['strClassNone'];
				}
			}

			if (!is_null($array[$key]['vars']['varsValue'])) {
				$array[$key]['strClass'] = $arr['vars']['varsItem']['strClassParent'];

				//当期残高にある期末残高
				$sumNext = $this->_getVarsNumValue(array(
					'varsFlag' => $arr['varsFlag'],
					'num'      => $arr['varsItem']['varsFSBSValue'][$flagFiscalPeriod][$idTarget]['sumNext'],
				));

				//当期残高にある期首残高
				$sumNextPrev = $this->_getVarsNumValue(array(
				    'varsFlag' => $arr['varsFlag'],
				    'num'      => $arr['varsItem']['varsFSBSValue'][$flagFiscalPeriod][$idTarget]['sumPrev'],
				));

				//前期残高にある期末残高
				$sumPrev = $this->_getVarsNumValue(array(
					'varsFlag' => $arr['varsFlag'],
					'num'      => $arr['varsItem']['varsFSBSValuePrev'][$flagFiscalPeriod][$idTarget]['sumNext'],
				));


				//前期残高にある期末残高がない場合あり＝期首残高が発現する年の場合
				//期首残高を入力した場合は当期を参照するようにしないといけない
				$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
				if ($varsPluginAccountingAccount['numFiscalPeriodCurrent'] == $varsPluginAccountingEntity[$idEntity]['numFiscalPeriodStart']) {
				    if ($sumNextPrev != $sumPrev) {
				        $sumPrev = $sumNextPrev;
				    }
				}
/*
if ($idTarget == 'netAssetsSum') {
    var_dump($idEntity,$varsPluginAccountingAccount['numFiscalPeriodCurrent'] , $varsPluginAccountingEntity[$idEntity]['numFiscalPeriodStart'],$sumNext,$sumNextPrev,$sumPrev);exit;
}
*/

				$arrId = $arr['varsItem']['varsJgaapFS']['arrIdAccountTitle'][$idTarget];
				if (!$arrId) {
					$arrId = array();
				}
				$varsUseLog = $this->_checkUseLog(array('arrId' => $arrId));
				if ($varsUseLog
					|| $idTarget == 'unappropriatedRetainedEarnings'
					|| ($value['vars']['flagCalc'] && $sumNext != $sumPrev)
				) {
/*
$numPrev = 0;
$numInOut = 0;
$numNext = 0;
*/


				    //期首残高
				    $varsChild = $varsTmpl;
					$varsChild['strTitle'] = $arr['vars']['varsItem']['strPrev'];
					$varsChild['vars']['varsValue']['numValue'] = $sumPrev;
					$varsChild['vars']['varsValue']['strReason'] = '';
					$varsChild['varsColumnDetail']['numValue'] = number_format($sumPrev);
					$varsChild['varsColumnDetail']['strReason'] = '';
					$varsChild['varsPrint']['strTitle'] = '';
					$varsChild['varsPrint']['strTitle2'] = $varsChild['strTitle'];
					$varsChild['varsPrint']['sumNext'] = number_format($varsChild['vars']['varsValue']['numValue']);
					$varsChild['varsPrint']['strReason'] = $varsChild['varsColumnDetail']['strReason'];
					if (!is_null($value['vars']['flagUse'])) {
						if (!(int) $value['vars']['flagUse']) {
							$varsChild['strClassFont'] = $arr['vars']['varsItem']['strClassNone'];
						}
					}
					$array[$key]['child'][] = $varsChild;

//$numPrev = $sumPrev;

					//当期増減
					$arrayLog = $varsUseLog;
					foreach ($arrayLog as $keyLog => $valueLog) {
						$varsChild = $varsTmpl;
						if (count($array[$key]['child']) > 1) {
							$varsChild['strTitle'] = '';
							$varsChild['strClass'] = $arr['vars']['varsItem']['strClassBlank'];
						} else {
							$varsChild['strTitle'] = $arr['vars']['varsItem']['strChange'];

						}
						$numValue = $valueLog['numValue'];
						if ($value['vars']['flagDebit'] != $valueLog['flagDebit']) {
							$numValue *= -1;
						}
						$varsChild['vars']['varsValue']['numValue'] = $numValue;
						$varsChild['varsColumnDetail']['numValue'] = number_format($numValue);

						$varsChild['vars']['varsValue']['strReason'] = $valueLog['strTitle'];
						$varsChild['varsColumnDetail']['strReason'] = $valueLog['strTitle'];
						if ($valueLog['strTitle'] == '') {
							$varsChild['varsColumnDetail']['strReason'] = $arr['vars']['varsItem']['strLost'];
						}
						if (!is_null($value['vars']['flagUse'])) {
							if (!(int) $value['vars']['flagUse']) {
								$varsChild['strClassFont'] = $arr['vars']['varsItem']['strClassNone'];
							}
						}
						$varsChild['varsPrint']['strTitle'] = '';
						$varsChild['varsPrint']['strTitle2'] = $varsChild['strTitle'];
						$varsChild['varsPrint']['sumNext'] = number_format($varsChild['vars']['varsValue']['numValue']);
						$varsChild['varsPrint']['strReason'] = $varsChild['varsColumnDetail']['strReason'];
						$array[$key]['child'][] = $varsChild;

//$numInOut = $numValue;
					}

					//繰越利益剰余金
					if ($idTarget == 'unappropriatedRetainedEarnings') {
						$varsChild = $varsTmpl;
						if (count($array[$key]['child']) > 1) {
							$varsChild['strTitle'] = '';
							$varsChild['strClass'] = $arr['vars']['varsItem']['strClassBlank'];
						} else {
							$varsChild['strTitle'] = $arr['vars']['varsItem']['strChange'];
						}
						$sum = $this->_getVarsNumValue(array(
							'varsFlag' => $arr['varsFlag'],
							'num'      => $arr['varsItem']['varsFSPLValue'][$flagFiscalPeriod]['currentTermProfitOrLossNet']['sumNext'],
						));
						$varsChild['vars']['varsValue']['numValue'] = $sum;
						$varsChild['varsColumnDetail']['numValue'] = number_format($sum);

						$varsChild['vars']['varsValue']['strReason'] = $arr['varsItem']['varsAccountTitlePL']['arrStrTitle']['currentTermProfitOrLossNet']['strTitleFS'];
						$varsChild['varsColumnDetail']['strReason'] = $varsChild['vars']['varsValue']['strReason'];
						if (!is_null($value['vars']['flagUse'])) {
							if (!(int) $value['vars']['flagUse']) {
								$varsChild['strClassFont'] = $arr['vars']['varsItem']['strClassNone'];
							}
						}
						$varsChild['varsPrint']['strTitle'] = '';
						$varsChild['varsPrint']['strTitle2'] = $varsChild['strTitle'];
						$varsChild['varsPrint']['sumNext'] = number_format($varsChild['vars']['varsValue']['numValue']);
						$varsChild['varsPrint']['strReason'] = $varsChild['varsColumnDetail']['strReason'];
						$array[$key]['child'][] = $varsChild;
					}

					if (!$varsUseLog && ($value['vars']['flagCalc'] && $sumNext != $sumPrev)) {
						$varsChild = $varsTmpl;
						if (count($array[$key]['child']) > 1) {
							$varsChild['strTitle'] = '';
							$varsChild['strClass'] = $arr['vars']['varsItem']['strClassBlank'];
						} else {
							$varsChild['strTitle'] = $arr['vars']['varsItem']['strChange'];
						}
						$sum = $sumNext - $sumPrev;
						$sum = $this->_getVarsNumValue(array(
							'varsFlag' => $arr['varsFlag'],
							'num'      => $sum,
						));
						$varsChild['vars']['varsValue']['numValue'] = $sum;
						$varsChild['varsColumnDetail']['numValue'] = number_format($sum);

						$varsChild['vars']['varsValue']['strReason'] = '';
						$varsChild['varsColumnDetail']['strReason'] = '';
						if (!is_null($value['vars']['flagUse'])) {
							if (!(int) $value['vars']['flagUse']) {
								$varsChild['strClassFont'] = $arr['vars']['varsItem']['strClassNone'];
							}
						}
						$varsChild['varsPrint']['strTitle'] = '';
						$varsChild['varsPrint']['strTitle2'] = $varsChild['strTitle'];
						$varsChild['varsPrint']['sumNext'] = number_format($varsChild['vars']['varsValue']['numValue']);
						$varsChild['varsPrint']['strReason'] = $varsChild['varsColumnDetail']['strReason'];
						$array[$key]['child'][] = $varsChild;
					}

					//期末残高
					$varsChild = $varsTmpl;
					$varsChild['strTitle'] = $arr['vars']['varsItem']['strNext'];
					$varsChild['vars']['varsValue']['numValue'] = $sumNext;
					$varsChild['varsColumnDetail']['numValue'] = number_format($sumNext);
					$varsChild['vars']['varsValue']['strReason'] = '';
					$varsChild['varsColumnDetail']['strReason'] = '';
					if (!is_null($value['vars']['flagUse'])) {
						if (!(int) $value['vars']['flagUse']) {
							$varsChild['strClassFont'] = $arr['vars']['varsItem']['strClassNone'];
						}
					}
					$varsChild['varsPrint']['strTitle'] = '';
					$varsChild['varsPrint']['strTitle2'] = $varsChild['strTitle'];
					$varsChild['varsPrint']['sumNext'] = number_format($varsChild['vars']['varsValue']['numValue']);
					$varsChild['varsPrint']['strReason'] = $varsChild['varsColumnDetail']['strReason'];
					$array[$key]['child'][] = $varsChild;

//$numNext = $sumNext;


				} else {
					$varsChild = $varsTmpl;
					$varsChild['strTitle'] = $arr['vars']['varsItem']['strEven'];
					$varsChild['vars']['varsValue']['numValue'] = $sumNext;
					$varsChild['varsColumnDetail']['numValue'] = number_format($sumNext);
					$varsChild['vars']['varsValue']['strReason'] = '';
					$varsChild['varsColumnDetail']['strReason'] = '';
					if (!is_null($value['vars']['flagUse'])) {
						if (!(int) $value['vars']['flagUse']) {
							$varsChild['strClassFont'] = $arr['vars']['varsItem']['strClassNone'];
						}
					}
					$varsChild['varsPrint']['strTitle'] = '';
					$varsChild['varsPrint']['strTitle2'] = $varsChild['strTitle'];
					$varsChild['varsPrint']['sumNext'] = number_format($varsChild['vars']['varsValue']['numValue']);
					$varsChild['varsPrint']['strReason'] = $varsChild['varsColumnDetail']['strReason'];
					$array[$key]['child'][] = $varsChild;
				}

			}
		}

		return $array;
	}

	/**

	 */
	protected function _getVarsNumValue($arr)
	{
		$flagUnit = (int) $arr['varsFlag']['flagUnit'];
		$flagCalc = $arr['varsFlag']['flagCalc'];
		$num = $arr['num'];

		if (!is_null($num)) {
			$num = $this->_updateCalc(array(
				'flagType' => $flagCalc,
				'num'      => $num,
				'numLevel' => $flagUnit
			));
		} else {
			$num = 0;
		}

		return $num;
	}

	/**
		(array(

		))
	 */
	protected function _checkUseLog($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;
		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$tmplWhere = array(
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
		);

		$arrayNew = array();
		$array = $arr['arrId'];
		foreach ($array as $key => $value) {
			$arrWhere = $tmplWhere;
			$arrWhere[] = array(
				'flagType'      => '',
				'strColumn'     => 'idAccountTitle',
				'flagCondition' => 'eq',
				'value'         => $value,
			);
			$rows = $classDb->getSelect(array(
				'idModule' => 'accounting',
				'strTable' => 'accountingLogCalc' . $strNation,
				'arrLimit' => array(),
				'arrOrder' => array(),
				'flagAnd'  => 1,
				'arrWhere' => $arrWhere,
			));

			if ($rows['numRows']) {
				$arrayChild = $rows['arrRows'];
				foreach ($arrayChild as $keyChild => $valueChild) {
					$arrayNew[] = $valueChild;
				}
			}
		}

		return $arrayNew;
	}



	/**

	 */
	protected function _iniNaviSearch()
	{
		$this->_setSearch();
	}

	/**

	 */
	protected function _setSearch()
	{
		global $varsRequest;
		global $varsPluginAccountingAccount;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsFlag = array(
			'flagFiscalPeriod' => $varsRequest['query']['jsonValue']['vars']['FlagFiscalPeriod'],
			'flagUnit'         => (int) $varsRequest['query']['jsonValue']['vars']['FlagUnit'],
			'flagCalc'         => $varsRequest['query']['jsonValue']['vars']['FlagCalc'],
		);

		$varsItem = $this->_getVarsItem(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars' => &$vars,
		)));

		$this->_checkValueDetail(array(
			'varsDetail' => $vars['portal']['varsNavi']['varsDetail'],
			'varsFlag'   => $varsFlag,
		));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		));

		$this->sendVars(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'varsDetail' => $vars['portal']['varsList']['varsDetail'],
				'varsHtml'   => $vars['portal']['varsList']['varsHtml'],
				'varsColumn' => $vars['portal']['varsList']['tableTree']['varsDetail']['varsColumn'],
			),
		));
	}

	/**
		(array(
			'varsDetail' => $vars['portal']['varsNavi']['varsDetail'],
			'varsFlag'   => $varsFlag,
		))
	 */
	protected function _checkValueDetail($arr)
	{
		global $varsRequest;
		global $classEscape;

		$flag = 0;
		$array = $arr['varsDetail'];
		foreach ($array as $key => $value) {
			$idTarget = $classEscape->toLower(array('str' => $value['id']));
			$arrayOption = $value['arrayOption'];
			foreach ($arrayOption as $keyOption => $valueOption) {
				if ($valueOption['value'] == $arr['varsFlag'][$idTarget]) {
					$flag = 1;
				}
			}
			if (!$flag) {
				if ($arr['flagOutput']) {
					$this->_send404Output();
				} else {
					$this->_sendOld();
				}
			}
			$flag = 0;
		}
	}

	/**
	 *
	 */
	protected function _iniListPrint()
	{
		$this->_setClassExt(array('strClass' => 'FinancialStatementSSOutput'));
	}

	/**
	 *
	 */
	protected function _iniListOutput()
	{
		$this->_setClassExt(array('strClass' => 'FinancialStatementSSOutput'));
	}

	/**
	 *
	 */
	protected function _iniListReload()
	{
		$this->_setSearch();
	}
}
