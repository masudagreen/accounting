<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_AccountTitleCS extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference' => 'accountTitleCSWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/accountTitleCS.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/accountTitleCS.php',
		'pathTplList'  => 'else/plugin/accounting/html/accountTitleCSList.html',
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
						var_dump(__CLASS__ . '/' .__FUNCTION__);
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

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars'       => $vars,
			'flagFS'     => $vars['flagFS'],
			'flagDirect' => $vars['flagDirect'],
		));

		$vars = $this->_updateVars(array(
			'flagFS'     => $vars['flagFS'],
			'flagDirect' => $vars['flagDirect'],
			'vars'       => $vars,
			'varsItem'   => $varsItem,
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
		global $varsPluginAccountingAccount;

		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsFSItem = $this->_getVarsFSItem();

		$varsFlagMethod = $this->_getVarsItemFlagMethod(array(
			'vars' => $varsFSItem['varsCSOption'],
		));

		//select form view prepare
		$strFlagDirect = 'varsInDirect';
		if ($arr['flagDirect']) {
			$strFlagDirect = 'varsDirect';
		}

		$varsFS['jsonJgaapFSCS'][$strFlagDirect] = $this->_setTreeId(array(
			'idParent' => '',
			'vars'     => $varsFS['jsonJgaapFSCS'][$strFlagDirect],
		));

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
			'varsFS'         => $varsFS,
			'varsFlagMethod' => $varsFlagMethod,
			'varsJgaapFSCS'  => $varsJgaapFSCS,
		);

		return $data;

	}

	/**
		'vars' => $arr['vars'],
	 */
	protected function _getVarsItemFlagMethod($arr)
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;
		global $varsAccount;

		$arrSelectTag = $arr['vars']['arrayOption'];
		$arrStrTitle = array();

		$array = $arrSelectTag;
		foreach ($array as $key => $value) {
			$arrStrTitle[$value['value']] = $value['strTitle'];
		}

		$data = array(
			'arrSelectTag' => $arrSelectTag,
			'arrStrTitle'  => $arrStrTitle,
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
			'flag' => '',
			'vars' => array(),
			'varsItem' => array(),
		))
	 */
	protected function _updateVars($arr)
	{
		global $classDb;
		global $classHtml;

		global $varsAccount;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingAccountsEntity;
		global $varsPluginAccountingAuthority;

		$idAccount = $varsPluginAccountingAccount['id'];
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$idAuthority = $varsPluginAccountingAccountsEntity[$idAccount][$idEntity]['idAuthority'];
		$varsAuthority = $varsPluginAccountingAuthority[$idAuthority];

		$vars = &$arr['vars'];
		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done)$/", $flagCurrentFlagNow)) {
			$vars['portal']['varsDetail']['varsBtn'] = array();
			$vars['portal']['varsDetail']['view']['varsEdit'] = array();

		} else {
			$numFiscalPeriodTemp = '';
			if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
				$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] + 1;

			} elseif (preg_match("/^(tempNext)$/", $flagCurrentFlagNow)) {
				$numFiscalPeriodTemp = $varsPluginAccountingAccount['numFiscalPeriodCurrent'] - 1;
			}
			$str = 'jsonJgaapAccountTitle'. $arr['flagFS'];
			$arr['varsItem']['varsFS'][$str] = $this->_getFlagUseLog(array(
				'vars'                  => $arr['varsItem']['varsFS'][$str],
				'numFiscalPeriod'       => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				'numFiscalPeriodTemp'   => $numFiscalPeriodTemp,
				'classCalcAccountTitle' => $this->_getClassCalc(array('flagType' => 'AccountTitle')),
				'flagCurrentFlagNow'    => $flagCurrentFlagNow,
				'flagFS'                => $arr['flagFS'],
				'varsAuthority' => array(
					'flagUpdate' => ($varsAuthority['flagAllUpdate'])? 1 : 0,
				),
			));
		}

		$vars['portal']['varsNavi']['templateDetail'] = $this->_updateVarsNaviFS((array(
			'vars'             => $vars,
			'varsEntityNation' => $varsEntityNation,
		)));



		$str = 'jsonJgaapAccountTitle'. $arr['flagFS'];
		$varsDetail = $this->_updateVarsList(array(
			'varsDetail'       => array(),
			'vars'             => $vars,
			'flagDirect'       => $arr['flagDirect'],
			'varsFS'           => $arr['varsItem']['varsFS'][$str],
			'varsItem'         => $arr['varsItem'],
			'varsEntityNation' => $arr['varsEntityNation']
		));

		$vars['portal']['varsList']['varsDetail'] = $varsDetail;

		$varsTemp = $classHtml->allot(array(
			'strClass'    => 'TableTree',
			'flagStatus'  => 'Html',
			'numTimeZone' => $varsAccount['numTimeZone'],
			'varsDetail'  => $varsDetail,
			'varsColumn'  => $vars['portal']['varsList']['tableTree']['varsDetail']['varsColumn'],
			'varsStatus'  => $vars['portal']['varsList']['tableTree']['varsDetail']['varsStatus'],
		));
		$vars['portal']['varsList']['varsHtml'] = $varsTemp['strHtml'];


		$strFlagDirect = 'varsInDirect';
		if ($arr['flagDirect']) {
			$strFlagDirect = 'varsDirect';
		}

		$vars['portal']['varsDetail']['templateDetail'] = $this->_updateVarsTemplateDetail((array(
			'vars'                   => $vars,
			'flagDirect'             => $arr['flagDirect'],
			'varsEntityNation'       => $varsEntityNation,
			'arrSelectTagFlagMethod' => $arr['varsItem']['varsFlagMethod']['arrSelectTag'],
			'arrSelectTagJgaapFSCS'  => $arr['varsItem']['varsJgaapFSCS'][$strFlagDirect]['arrSelectTag'],
		)));

		return $vars;
	}

	/**
		(array(
			'vars' => array(),
			'varsEntityNation' => array(),
		))
	 */
	protected function _updateVarsNaviFS($arr)
	{
		$vars = &$arr['vars'];
		$varsEntityNation = &$arr['varsEntityNation'];
		$arrayNew = array();
		$array = $vars['portal']['varsNavi']['templateDetail'][0]['arrayOption'];
		foreach ($array as $key => $value) {
			if ($value['value'] == 'CR' && !(int) $varsEntityNation['flagCR']) {

			} else {
				$arrayNew[] = $value;
			}
		}

		$vars['portal']['varsNavi']['templateDetail'][0]['arrayOption'] = $arrayNew;
		$vars['portal']['varsNavi']['templateDetail'][0]['numSize'] = count($arrayNew);

		return $vars['portal']['varsNavi']['templateDetail'];

	}

	/**
		(array(
			'vars'             => $vars,
			'flagDirect'       => $arr['flagDirect'],
			'varsFS'           => $arr['varsItem']['varsFS'][$str],
			'varsItem'         => $arr['varsItem'],
			'varsEntityNation' => $arr['varsEntityNation']
		))

	 */
	protected function _updateVarsList($arr)
	{
		$array = &$arr['varsFS'];

		foreach ($array as $key => $value) {
			$array[$key]['id'] = '';
			$array[$key]['flagBoldNow'] = 0;
			$array[$key]['strClassFont'] = '';
			$array[$key]['strClassBg'] = '';
			$array[$key]['vars']['flagUseComment'] = 1;
			$array[$key]['varsColumnDetail'] = array(
				'flagPlus' => '',
				'flagMinus' => '',
				'idAccountTitleMinus' => '',
				'flagMethodMinus' => '',
				'idAccountTitlePlus' => '',
				'flagMethodPlus' => '',
				'strFlagDirect' => '',
			);

			if (!is_null($value['vars']['flagUse'])) {

				//JgaapFS
				$strFlagDirect = 'varsInDirect';
				if ($arr['flagDirect']) {
					$strFlagDirect = 'varsDirect';
				}
				$array[$key]['varsColumnDetail']['strFlagDirect'] = $strFlagDirect;

				//flagUse
				if ((int) $value['vars']['flagUse']) {

				} else {
					$array[$key]['strClassFont'] = $arr['vars']['varsItem']['strClassNone'];
				}

				$idAccountTitlePlus = $value['vars']['varsJgaapCS'][$strFlagDirect]['idAccountTitlePlus'];
				if ($idAccountTitlePlus) {
					$strIdAccountTitlePlus = $arr['varsItem']['varsJgaapFSCS'][$strFlagDirect]['arrStrTitle'][$idAccountTitlePlus];
					$array[$key]['varsColumnDetail']['idAccountTitlePlus'] = $strIdAccountTitlePlus;
					if (!($idAccountTitlePlus == 'none' || $idAccountTitlePlus == 'cash')) {
						$flagMethodPlus = $value['vars']['varsJgaapCS'][$strFlagDirect]['flagMethodPlus'];
						if ($flagMethodPlus) {
							$strFlagMethodPlus = $arr['varsItem']['varsFlagMethod']['arrStrTitle'][$flagMethodPlus];
							$array[$key]['varsColumnDetail']['flagMethodPlus'] = $strFlagMethodPlus;
							$array[$key]['varsColumnDetail']['flagPlus'] = $strIdAccountTitlePlus . ' - ' . $strFlagMethodPlus;
						}

					} else {
						$array[$key]['varsColumnDetail']['flagPlus'] = $strIdAccountTitlePlus;
					}
				}

				$idAccountTitleMinus = $value['vars']['varsJgaapCS'][$strFlagDirect]['idAccountTitleMinus'];
				if ($idAccountTitleMinus) {
					$strIdAccountTitleMinus = $arr['varsItem']['varsJgaapFSCS'][$strFlagDirect]['arrStrTitle'][$idAccountTitleMinus];
					$array[$key]['varsColumnDetail']['idAccountTitleMinus'] = $strIdAccountTitleMinus;
					if (!($idAccountTitleMinus == 'none' || $idAccountTitleMinus == 'cash')) {
						$flagMethodMinus = $value['vars']['varsJgaapCS'][$strFlagDirect]['flagMethodMinus'];
						if ($flagMethodMinus) {
							$strFlagMethodMinus = $arr['varsItem']['varsFlagMethod']['arrStrTitle'][$flagMethodMinus];
							$array[$key]['varsColumnDetail']['flagMethodMinus'] = $strFlagMethodMinus;
							$array[$key]['varsColumnDetail']['flagMinus'] = $strIdAccountTitleMinus . ' - ' . $strFlagMethodMinus;
						}

					} else {
						$array[$key]['varsColumnDetail']['flagMinus'] = $strIdAccountTitleMinus;
					}
				}

			}


			if ($value['child']) {
				$array[$key]['child'] = $this->_updateVarsList(array(
					'vars'             => $arr['vars'],
					'flagDirect'       => $arr['flagDirect'],
					'varsFS'           => $array[$key]['child'],
					'varsItem'         => $arr['varsItem'],
					'varsEntityNation' => $arr['varsEntityNation'],
				));
			}
		}

		return $array;
	}

	/**
		(array(
			'vars'             => array(),
			'varsEntityNation' => array(),
		))
	 */
	protected function _updateVarsTemplateDetail($arr)
	{
		$vars = &$arr['vars'];

		$arrayNew = array();
		$array = $vars['portal']['varsDetail']['templateDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'IdAccountTitlePlus' || $value['id'] == 'IdAccountTitleMinus') {
				$value['arrayOption'] = $arr['arrSelectTagJgaapFSCS'];
				$arrayNew[] = $value;

			} elseif ($value['id'] == 'FlagMethodPlus' || $value['id'] == 'FlagMethodMinus') {
				$value['arrayOption'] = $arr['arrSelectTagFlagMethod'];
				$arrayNew[] = $value;

			} else {
				$arrayNew[] = $value;
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
		global $varsPluginAccountingPreference;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$flagFS = $this->_checkValueFS(array(
			'vars' => $vars,
		));
		$flagDirect = (int) $varsRequest['query']['jsonValue']['vars']['FlagDirect'];

		if (FLAG_CHECK_UPDATE) {
			$str = 'acountTitle';
			if ($varsPluginAccountingPreference['jsonStampUpdate'][$str] <= $varsRequest['query']['jsonStamp']['stamp']) {
				$this->sendVars(array(
					'flag'    => 10,
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => '',
				));
			}
		}

		$varsItem = $this->_getVarsItem(array(
			'vars'       => $vars,
			'flagFS'     => $flagFS,
			'flagDirect' => $flagDirect,
		));

		$vars = $this->_updateVars(array(
			'flagFS'     => $flagFS,
			'flagDirect' => $flagDirect,
			'vars'       => $vars,
			'varsItem'   => $varsItem,
		));

		$this->sendVars(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'varsDetail'     => $vars['portal']['varsList']['varsDetail'],
				'varsHtml'       => $vars['portal']['varsList']['varsHtml'],
				'varsColumn'     => $vars['portal']['varsList']['tableTree']['varsDetail']['varsColumn'],
				'templateDetail' => $vars['portal']['varsDetail']['templateDetail']
			),
		));
	}

	/**
		(array(
			'vars' => array(),
		))
	 */
	protected function _checkValueFS($arr)
	{
		global $varsRequest;
		global $varsPluginAccountingAccount;

		$flagFS = $varsRequest['query']['jsonValue']['vars']['FlagAccountTitle'];
		$array = $arr['vars']['portal']['varsNavi']['templateDetail'][0]['arrayOption'];
		$varsEntity = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));
		$flag = 0;
		foreach ($array as $key => $value) {
			if ($value['value'] == 'CR' && !$varsEntity['flagCR']) {
				continue;
			}
			if ($value['value'] == $flagFS) {
				$flag = 1;
			}
		}
		if (!$flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}

		return $flagFS;
	}

	/**
	 *
	 */
	protected function _iniListReload()
	{
		$this->_setSearch();
	}


}
