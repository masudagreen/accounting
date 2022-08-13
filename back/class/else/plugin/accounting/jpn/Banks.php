<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_Banks extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference' => 'banksWindow',
		'idLog'        => 'logWindow',
		'idCash'       => 'cashWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/banks.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/banks.php',
		'tplComment'   => 'else/plugin/accounting/html/banksComment.html',
		'varsOption'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/banks.php',
		'pathDir'      => 'back/tpl/vars/else/plugin/accounting/ja/dat/jpn/banks/',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 0,
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
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__ . '/' . $path);
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
		$this->_setJs(array(
			'pathVars'        => $this->_extSelf['pathVarsJs'],
			'pathTpl'         => $this->_extSelf['pathTplJs'],
			'arrFolder' => array(),
			'arrSearch'       => array(
				'idModule'  => 'accounting',
				'numLotNow' => 0,
				'strTable'  => 'accountingLogBanks',
				'arrOrder'  => array(
					'strColumn' => 'id',
					'flagDesc'  => 1,
				),
				'insCurrent' => $this,
				'arrWhere'  => array(
				),
			),
		));

	}

	/**
		$insCurrent->getDBAuthority(array(
			'flagSqlType' => $arr['flagSqlType'],ex) Select,Update,Delete,Output
			'arrData'     => ($arr['arrData']),ex) flag
		));
		return $array = array(
			'strSql'   => '',
			'arrValue' => array(),
		);
		or
		return 0
	 */
	public function getDBAuthority($arr)
	{
		global $classCheck;
		global $varsPluginAccountingAccount;
		global $varsRequest;
		global $varsAccount;

		$idAccount = $varsAccount['id'];
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];

		$strStatus = 'Select';
		if ($varsRequest['query']['func'] == 'ListOutput'
		) {
			$strStatus = 'Output';
		}

		$varsAuthority = $this->_getVarsAuthority(array());

		$strSql = 'idEntity = ? && numFiscalPeriod = ? ';
		$arrValue = array($idEntity, $numFiscalPeriod);

		$flag = $varsRequest['query']['jsonSearch']['ph']['flagApply'];
		if (!$flag) {
			$flag = 'none';
		}

		if ($flag != 'none') {
			$flagRemove = ($flag == 'remove')? 1 : 0;
			$strSql .= '&& flagRemove = ? ';
			$arrValue[] = $flagRemove;
		}

		$flagSql = 0;
		if ($varsAuthority == 'admin' || $varsAuthority['flagAll' . $strStatus]) {
			if ($flag == 'caution') {
				$flagCaution = 1;
				$strSql .= '&& flagCaution = ? ';
				$arrValue[] = $flagCaution;
			}
			$flagSql = 1;

		} elseif ($varsAuthority['flagMy' . $strStatus]) {
			if ($flag == 'caution') {
				$flagCaution = 1;
				$strSql .= '&& idAccount = ? && flagCaution = ?';
				$arrValue[] = $idAccount;
				$arrValue[] = $flagCaution;

			} else {
				$strSql .= '&& idAccount = ?';
				$arrValue[] = $idAccount;
			}
			$flagSql = 1;
		}

		if ($flagSql) {
			$array = array(
				'strSql'   => $strSql,
				'arrValue' => $arrValue,
			);
			return $array;
		}
		return 0;

	}

	/**
		$this->_setJs(array(
			'pathVars'        => '',
			'pathTpl'         => '',
			'arrFolder'       => array(),
			'strTableSearch'  => '',
			'strColumnSearch' => '',
			'flagEntitySearch'  => 0,
			'flagAccountSearch' => 0,
			'arrSearch'       => array(
				'idModule' => '',
				'numLotNow' => 0,
				'strTable'  => '',
				'arrOrder'  => array(),
				'arrWhere'  => array(),
			),
		));
	 */
	protected function _setJs($arr)
	{
		global $varsPluginAccountingAccount;
		global $classSmarty;

		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$array = $varsItem;
		foreach ($array as $key => $value) {
			$vars['varsItem'][$key] = $value;
		}

		$varsAuthority = $this->_getVarsAuthority(array());

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$flagCurrent = 1;
		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done)$/", $flagCurrentFlagNow)) {
			$flagCurrent = 0;
		}

		$vars['portal']['varsList']['varsBtn'] = $this->_updateVarsListBtn(array(
			'vars'        => $vars['portal']['varsList']['varsBtn'],
			'flagCurrent' => $flagCurrent,
		));

		if (!($varsAuthority == 'admin' || $varsAuthority['flagMyOutput'] || $varsAuthority['flagAllOutput'])) {
			$vars['portal']['varsList']['varsEdit']['flagOutputUse'] = 0;
			$vars['portal']['varsList']['varsStart']['varsEdit']['flagOutputUse'] = 0;
			$vars['portal']['varsDetail']['varsEdit']['flagOutputUse'] = 0;
			$vars['portal']['varsDetail']['view']['varsEdit']['flagOutputUse'] = 0;
		}

		if (!($varsAuthority == 'admin' || $varsAuthority['flagMyInsert'] || $varsAuthority['flagAllInsert'])) {
			$vars['portal']['varsList']['varsEdit']['flagImportUse'] = 0;
			$vars['portal']['varsList']['varsStart']['varsEdit']['flagImportUse'] = 0;
		}

		$flagPreferenceUse = 0;
		if ($varsAuthority == 'admin' || $varsAuthority['flagAllSelect']) {
			$flagPreferenceUse = 1;
		}

		$vars['portal']['varsList']['varsEdit']['flagPreferenceUse'] = $flagPreferenceUse;
		$vars['portal']['varsList']['varsStart']['varsEdit']['flagPreferenceUse'] = $flagPreferenceUse;

		if (!$flagCurrent) {
			$vars['portal']['varsDetail']['varsBtn'] = array();
			$this->_updateVarsEditLock(array(
				'vars' => $vars['portal']['varsDetail']['view']['varsEdit'],
			));
			$vars['portal']['varsList']['varsEdit']['flagImportUse'] = 0;
			$vars['portal']['varsList']['varsStart']['varsEdit']['flagImportUse'] = 0;
		}

		$rows = $this->getSearch($arr['arrSearch']);

		$vars = $this->_updateSearch(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'rows'     => $rows,
		));

		$vars['portal']['varsNavi'] = array();

		$json = json_encode($vars);
		$classSmarty->assign('varsLoad', $json);

		$classSmarty->assign('numNews', $this->getNumNews());
		$contents = $classSmarty->fetch($arr['pathTpl']);

		$this->sendJs(array(
			'data' => $contents,
		));
	}

	/**
	 *
	 */
	protected function _updateVarsEditLock($arr)
	{
		$array = &$arr['vars'];
		foreach ($array as $key => $value) {
			if ($key == 'flagReloadUse') {
				continue;
			}
			$array[$key] = 0;
		}
	}

	/**
		(array(
			'vars' => $vars,
		))
	 */
	protected function _getVarsItem($arr)
	{
		global $varsPluginAccountingAccount;

		$varsOption = $this->getVars(array(
			'path' => $this->_extSelf['varsOption'],
		));

		$varsBanksList = array();
		$array = scandir($this->_extSelf['pathDir']);
		foreach ($array as $key => $value) {
			if ( preg_match( "/^\.{1,2}$/", $value)) {
				continue;
			}
			$path = $this->_extSelf['pathDir'] . $value;
			preg_match("/^(.*?)\.php$/", $value, $arrMatch);
			list($str, $id) = $arrMatch;
			$varsBanksList[$id] = $this->getVars(array(
				'path' => $path,
			));
		}

		$array = $this->_getLog(array());
		$varsBanksAccountList = array(
			'arrSelectTag' => array(),
			'arrStrTitle'  => array(),
		);
		foreach ($array as $key => $value) {
			$varsBanksAccountList['arrSelectTag'][] = array(
				'strTitle' => $varsBanksList[$value['flagBank']]['strTitle'] . '(' . $value['strTitle'] . ')',
				'value'    => $value['idLogAccount'],
			);
			$varsBanksAccountList['arrStrTitle'][$value['idLogAccount']] = $value;
		}

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsStampTerm = $this->_getVarsStampTerm(array(
			'varsFlag'         => array(
				'flagFiscalPeriod' => 'f1',
			),
			'varsEntityNation' => $varsEntityNation,
			'numFiscalPeriod'  => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsPreference = $this->_getVarsPreference(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
		));

		$data = array(
			'varsOption'           => $varsOption,
			'varsBanksList'        => $varsBanksList,
			'varsBanksAccountList' => $varsBanksAccountList,
			'varsEntityNation'     => $varsEntityNation,
			'varsStampTerm'        => $varsStampTerm,
			'varsPreference'       => $varsPreference,
		);

		return $data;
	}

	/**
		(array(
		))
	 */
	protected function _getVarsPreference($arr)
	{
		global $classDb;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingBanks',
			'arrLimit' => array(),
			'arrOrder'  => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idEntity',
					'flagCondition' => 'eq',
					'value'         => $arr['idEntity'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'numFiscalPeriod',
					'flagCondition' => 'eq',
					'value'         => $arr['numFiscalPeriod'],
				),
			),
		));

		return $rows['arrRows'][0];
	}

	/**
		(array(
			'flagType' => 'LogCalc'
		))
	 */
	protected function _getLog($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogBanksAccount',
			'arrLimit' => array(),
			'arrOrder'  => array(
				'strColumn' => 'id',
				'flagDesc'  => 1,
			),
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
					'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				),
			),
		));

		return $rows['arrRows'];
	}

	/**
		(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		))
	 */
	protected function _updateVars($arr)
	{
		$arr['vars']['portal']['varsDetail']['templateDetail'] = $this->_updateVarsTemplateDetail((array(
			'vars'     => &$arr['vars'],
			'varsItem' => $arr['varsItem'],
		)));

		return $arr['vars'];
	}

	/**
	 *
	 */
	protected function _updateVarsListBtn($arr)
	{
		$array = $arr['vars'];
		$arrayNew = array();
		foreach ($array as $key => $value) {
			if ($arr['flagCurrent']) {
				$arrayNew[] = $value;
			}
		}

		return $arrayNew;
	}

	/**
		(array(
			'vars'             => $vars,
			'varsItem'         => $arr['varsItem'],
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _updateVarsTemplateDetail($arr)
	{
		$arrayNew = array();
		$array = &$arr['vars']['portal']['varsDetail']['templateDetail'];
		foreach ($array as $key => $value) {
			$method = '_updateVarsTemplateDetail' . $value['id'];
			if (method_exists($this, $method)) {
				$value = $this->$method(array(
					'value'    => $value,
					'vars'     => $arr['vars'],
					'varsItem' => $arr['varsItem'],
				));
			}
			$arrayNew[] = $value;
		}

		return $arrayNew;
	}

	/**
		(array(
			'value'    => $value,
			'vars'     => $vars,
			'varsItem' => $varsItem,
		))
	 */
	protected function _updateVarsTemplateDetailIdLogAccount($arr)
	{
		$array = $arr['varsItem']['varsBanksAccountList']['arrSelectTag'];
		foreach ($array as $key => $value) {
			$arr['value']['arrayOption'][] = $value;
		}

		return $arr['value'];
	}

	/**
		(array(
			'value'    => $value,
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		))
	 */
	protected function _updateVarsTemplateDetailStampBook($arr)
	{
		global $classTime;

		$data = $this->_getNumFiscalTermStamp(array(
			'varsEntityNation' => $arr['varsItem']['varsEntityNation']
		));

		$stampMin = $data['stampMin'];
		$strMin = $classTime->getDisplay(array(
			'flagType' => 'year/date',
			'stamp'    => $stampMin,
		));

		$stampMax = $data['stampMax'];
		$strMax = $classTime->getDisplay(array(
			'flagType' => 'year/date',
			'stamp'    => $stampMax,
		));
		$stampMain = TIMESTAMP;
		if ($stampMin > $stampMain) {
			$stampMain = $stampMin;
		}

		$arr['value']['strExplain'] = str_replace('<%stampMin%>', $strMin, $arr['value']['strExplain']);
		$arr['value']['strExplain'] = str_replace('<%stampMax%>', $strMax, $arr['value']['strExplain']);
		$arr['value']['varsFormCalender']['varsStatus']['stampMin'] = $stampMin * 1000;
		$arr['value']['varsFormCalender']['varsStatus']['stampMain'] = TIMESTAMP * 1000;
		$arr['value']['varsFormCalender']['varsStatus']['stampMax'] = $data['stampMaxLimit'] * 1000;

		return $arr['value'];
	}

	/**
	 *
	 */
	protected function _getNumFiscalTermStamp($arr)
	{
		$numTimeZone = PLUGIN_ACCOUNTING_NUM_TIME_ZONE;

		$varsEntityNation = $arr['varsEntityNation'];
		$numFiscalBeginningYear = $varsEntityNation['numFiscalBeginningYear'];
		$numCurrentYear = $numFiscalBeginningYear;

		$strTimeZone = (-1 * $numTimeZone) . 'hours';
		$numYear = $numCurrentYear;
		$numMonth = $varsEntityNation['numFiscalBeginningMonth'];
		$dateTime = new DateTime("$numYear-$numMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stampMin = $dateTime->format('U');
		$numEndMonth = $varsEntityNation['numFiscalBeginningMonth'] + $varsEntityNation['numFiscalTermMonth'];
		if ($numEndMonth > 12) {
			$numCurrentYear++;
			$numEndMonth -= 12;
		}

		$numYear = $numCurrentYear;
		$numMonth = $numEndMonth;
		$dateTime = new DateTime("$numYear-$numMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stampMax = $dateTime->format('U') - 1;

		$numEndMonth = 1;
		$numAppLimit = 100;
		$numYear = $numCurrentYear + $numAppLimit;
		$numMonth = $numEndMonth;
		$dateTime = new DateTime("$numYear-$numMonth-1 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stampMaxLimit = $dateTime->format('U') - 1;

		$data = array(
			'stampMin'      => $stampMin,
			'stampMax'      => $stampMax,
			'stampMaxLimit' => $stampMaxLimit,
		);

		return $data;
	}

	/**
		(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'rows'     => $rows,
		))
	 */
	protected function _updateSearch($arr)
	{
		global $classCheck;
		global $classEscape;
		global $classHtml;

		global $varsRequest;
		global $varsPluginAccountingAccounts;
		global $varsPluginAccountingAccountsId;

		global $varsAccounts;
		global $varsAccount;
		global $varsPluginAccountingAccount;

		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$idEntity  = $varsPluginAccountingAccount['idEntityCurrent'];
		$strCheckStamp = 'accountingLogBanks_' . $idEntity . '_' . $numFiscalPeriod;

		$vars = $arr['vars'];
		$varsItem = $arr['varsItem'];
		$rows = $arr['rows'];

		$flagCurrent = 1;
		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done)$/", $flagCurrentFlagNow)) {
			$flagCurrent = 0;
		}

		$flagLogSelect = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'select',
			'idTarget'      => $this->_extSelf['idLog'],
		));
		$flagLogInsert = $this->_checkAccess(array(
			'flagAllUse'    => 0,
			'flagAuthority' => 'insert',
			'idTarget'      => $this->_extSelf['idLog'],
		));

		$varsAuthority = $this->_getVarsAuthority(array());

		$idAccount = $varsAccount['id'];

		$array = $rows['arrRows'];
		$arrayNew = array();
		$num = 0;
		$flag = 0;
		foreach ($array as $key => $value) {
			$varsTmpl = $vars['portal']['varsList']['templateDetail'];
			$varsTmpl['id'] = $value['idLogBanks'];
			$varsTmpl['vars']['idTarget'] = $value['idLogBanks'];
			$varsTmpl['vars']['numFiscalPeriod'] = $value['numFiscalPeriod'];
			$varsTmpl['numSort'] = (int) $key;

			if ($varsAccount['jsonStampCheck'][$strCheckStamp] < $value['stampRegister']) {
				$flag = 1;

			} else {
				$varsTmpl['strClassLoad'] = '';
			}
			$varsTmpl['idAccount'] = $value['idAccount'];
			$varsTmpl['idAccountSelf'] = $idAccount;
			$varsTmpl['strTitle'] = ($value['strTitle'])? $value['strTitle'] : '';

			$varsTmpl['stampRegister'] = $value['stampRegister'];
			$varsTmpl['stampUpdate'] = $value['stampUpdate'];
			$varsTmpl['flagCaution'] = (int) $value['flagCaution'];
			$varsTmpl['flagRemove'] = (int) $value['flagRemove'];
			$varsTmpl['stampRemove'] = $value['stampRemove'];

			$varsTmpl['jsonVersion'] = $this->_updateSearchJsonVersion(array(
				'vars'  => $vars,
				'value' => $value['jsonVersion'],
			));

			$numVersionEnd = count($varsTmpl['jsonVersion']) - 1;
			$varsTmpl['jsonDetail'] = $varsTmpl['jsonVersion'][$numVersionEnd];
			$varsTmpl['numVersion'] = count($varsTmpl['jsonVersion']);

			$tempData = $this->_getJsonChargeHistoryVarsDetail(array(
				'vars'  => $vars['varsItem']['varsJsonChargeHistory'],
				'value' => $value['jsonChargeHistory'],
			));
			$temp = $classHtml->allot(array(
				'strClass'    => 'TableSimple',
				'flagStatus'  => 'Html',
				'varsDetail'  => $tempData['varsDetail'],
				'varsColumn'  => $vars['varsItem']['varsJsonChargeHistory']['varsColumn'],
				'varsStatus'  => $vars['varsItem']['varsJsonChargeHistory']['varsStatus'],
			));
			$varsTmpl['jsonChargeHistory'] = $temp['strHtml'];
			$varsTmpl['vars']['jsonChargeHistory'] = $tempData['varsData'];

			$varsTmpl['jsonWriteHistory'] = '';
			$varsTmpl['vars']['jsonWriteHistory'] = array();
			if ($value['jsonWriteHistory']) {
				$tempData = $this->_getJsonWriteHistoryVarsDetail(array(
					'vars'    => $vars['varsItem']['varsJsonWriteHistory'],
					'value'   => $value['jsonWriteHistory'],
					'flagLog' => $flagLogSelect,
				));
				if (!$flagLogSelect) {
					$arrayColumndNew = array();
					$arrayColumnIdNew = array();
					$arrayColumn = $vars['varsItem']['varsJsonWriteHistory']['varsColumn'];
					$arrayColumnId = $vars['varsItem']['varsJsonWriteHistory']['varsStatus']['varsColumnId'];
					foreach ($arrayColumnId as $keyColumnId => $valueColumnId) {
						if ($valueColumnId == 'idLog') {
							continue;
						}
						$arrayColumndNew[] = $arrayColumn[$keyColumnId];
						$arrayColumnIdNew[] = $valueColumnId;
					}
					$vars['varsItem']['varsJsonWriteHistory']['varsStatus']['varsColumnId'] = $arrayColumnIdNew;
					$vars['varsItem']['varsJsonWriteHistory']['varsColumn'] = $arrayColumndNew;
				}
				$temp = $classHtml->allot(array(
					'strClass'    => 'TableSimple',
					'flagStatus'  => 'Html',
					'varsDetail'  => $tempData['varsDetail'],
					'varsColumn'  => $vars['varsItem']['varsJsonWriteHistory']['varsColumn'],
					'varsStatus'  => $vars['varsItem']['varsJsonWriteHistory']['varsStatus'],
				));
				$varsTmpl['jsonWriteHistory'] = $temp['strHtml'];
				$varsTmpl['vars']['jsonWriteHistory'] = $tempData['varsData'];
			}


			$varsTmpl['arrSpaceStrTag'] = preg_replace('/^ /', '', $value['arrSpaceStrTag']);

			$varsTmpl['flagBtnDelete'] = 0;
			$varsTmpl['flagBtnWrite'] = 0;
			$varsTmpl['flagBtnAdd'] = 0;
			$varsTmpl['flagBtnEdit'] = 0;
			$varsTmpl['flagCheckboxUse'] = 0;
			$varsTmpl['flagAdmin'] = ($varsAuthority == 'admin')? 1 : 0;
			$varsTmpl['flagCurrent'] = $flagCurrent;

			if ($flagCurrent) {
				if ($varsAuthority == 'admin') {
					if (!$varsTmpl['flagRemove']) {
						$varsTmpl['flagBtnDelete'] = 1;
						$varsTmpl['flagBtnEdit'] = 1;
						$varsTmpl['flagBtnWrite'] = 1;
					}
					$varsTmpl['flagBtnAdd'] = 1;

				} else {
					if (!$varsTmpl['flagRemove']) {
						if (($value['idAccount'] == $varsAccount['id'] && $varsAuthority['flagMyDelete'])
							|| $varsAuthority['flagAllDelete']
						) {
							$varsTmpl['flagBtnDelete'] = 1;
						}

						if (($value['idAccount'] == $varsAccount['id'] && $varsAuthority['flagMyUpdate'])
							|| $varsAuthority['flagAllUpdate']
						) {
							$varsTmpl['flagBtnEdit'] = 1;
						}

						if ($flagLogInsert) {
							if (($value['idAccount'] == $varsAccount['id'] && $varsAuthority['flagMyInsert'])
								|| $varsAuthority['flagAllInsert']
							) {
								$varsTmpl['flagBtnWrite'] = 1;
							}
						}
					}

					if ($varsAuthority['flagMyInsert'] || $varsAuthority['flagAllInsert']) {
						$varsTmpl['flagBtnAdd'] = 1;
					}
				}

				if (!$varsItem['varsPreference']['flagAutoImport']) {
					$varsTmpl['flagBtnWrite'] = 0;
				}

				if ($varsTmpl['flagBtnDelete'] || $varsTmpl['flagBtnWrite']) {
					$varsTmpl['flagCheckboxUse'] = 1;
				}
			}

			$varsTmpl['flagBtnOutput'] = 0;
			if ($varsAuthority == 'admin' || $varsAuthority['flagAllOutput']) {
				$varsTmpl['flagBtnOutput'] = 1;

			} elseif ($varsAuthority['flagMyOutput'] && $varsTmpl['idAccount'] == $varsTmpl['idAccountSelf']) {
				$varsTmpl['flagBtnOutput'] = 1;
			}

			$varsTmpl['varsColumnDetail']['id'] = $varsTmpl['id'];

			$numWrite = count($varsTmpl['vars']['jsonWriteHistory']);
			if (!$numWrite) {
				$numWrite = '';
			}
			$varsTmpl['varsColumnDetail']['numWrite'] = $numWrite;
			$varsTmpl['varsColumnDetail']['strVersion'] = 'Ver.' . count($varsTmpl['jsonVersion']);
			$varsTmpl['varsColumnDetail']['stampRegister'] = $value['stampRegister'];
			$varsTmpl['varsColumnDetail']['stampUpdate'] = $value['stampUpdate'];
			$varsTmpl['varsColumnDetail']['stampBook'] = $value['stampBook'];
			$varsTmpl['varsColumnDetail']['strTitle'] = $value['strTitle'];
			$varsTmpl['varsColumnDetail']['numValueIn'] = number_format($value['numValueIn']);
			$varsTmpl['varsColumnDetail']['numValueOut'] = number_format($value['numValueOut']);
			$varsTmpl['varsColumnDetail']['numBalance'] = number_format($value['numBalance']);

			$varsBanksAccount = $arr['varsItem']['varsBanksAccountList']['arrStrTitle'][$value['idLogAccount']];
			$strTemp = $arr['varsItem']['varsBanksList'][$varsBanksAccount['flagBank']]['strTitle'];
			$strTemp .= '(' . $varsBanksAccount['strTitle'] . ')';
			$varsTmpl['varsColumnDetail']['flagBank'] = $strTemp;

			if ($value['flagRemove']) {
				$varsTmpl['strClassFont'] = $vars['varsItem']['strClassNone'];
				$varsTmpl['varsColumnDetail']['flagStatus'] = $vars['varsItem']['strRemoveFake'];

			} elseif ($value['flagCaution']) {
				$varsTmpl['strClassFont'] = $vars['varsItem']['strClassCaution'];
				$varsTmpl['varsColumnDetail']['flagStatus'] = $vars['varsItem']['strCaution'];

			} else {
				$varsTmpl['varsColumnDetail']['flagStatus'] = $vars['varsItem']['strDone'];
			}

			$strCodeName = $varsAccounts[$value['idAccount']]['strCodeName'];
			if (!$strCodeName) {
				$strCodeName = $varsPluginAccountingAccountsId[$value['idAccount']]['strCodeName'];
			}
			$varsTmpl['varsColumnDetail']['idAccount'] = $strCodeName;

			$varsTmpl['vars']['id'] = $varsTmpl['id'];
			$varsTmpl['vars']['strTitle'] = $value['strTitle'];
			$varsTmpl['vars']['idLogAccount'] = $value['idLogAccount'];
			$varsTmpl['vars']['stampBook'] = $value['stampBook'];
			$varsTmpl['vars']['numValueIn'] = $value['numValueIn'];
			$varsTmpl['vars']['numValueOut'] = $value['numValueOut'];
			$varsTmpl['vars']['flagIn'] = (int) $value['flagIn'];
			$varsTmpl['vars']['numBalance'] = $value['numBalance'];
			$varsTmpl['vars']['idAccount'] = $value['idAccount'];
			$varsTmpl['vars']['flagRemove'] = $value['flagRemove'];
			$varsTmpl['vars']['flagCaution'] = $value['flagCaution'];
			$varsTmpl['vars']['arrSpaceStrTag'] = $classEscape->splitSpaceArrayData(array('data' => $varsTmpl['arrSpaceStrTag']));

			$arrayColumnDetail = &$varsTmpl['varsColumnDetail'];
			foreach ($arrayColumnDetail as $keyColumnDetail => $valueColumnDetail) {
				if (is_null($valueColumnDetail)) {
					$arrayColumnDetail[$keyColumnDetail] = '';
				}
			}

			$varsTmpl['varsScheduleDetail']['stamp'] = $value['stampBook'];
			$varsTmpl['varsScheduleDetail']['strTitle'] = $value['strTitle'];
			$arrayNew[$num] = $varsTmpl;
			$num++;
		}
		$vars['portal']['varsList']['varsPage']['varsStatus']['numRows'] = $rows['numRows'];
		$vars['portal']['varsList']['varsDetail'] = $arrayNew;

		$flagAddUse = 0;
		if ($flagCurrent) {
			if ($varsAuthority == 'admin' || $varsAuthority['flagAllInsert'] || $varsAuthority['flagMyInsert']) {
				$flagAddUse = 1;
			}
		}
		$vars['portal']['varsDetail']['varsStart']['varsEdit']['flagAddUse'] = $flagAddUse;

		if (!$arr['flagVars']) {
			$varsTemp = $classHtml->allot(array(
				'strClass'    => 'Table',
				'flagStatus'  => 'Html',
				'numTimeZone' => $varsAccount['numTimeZone'],
				'varsDetail'  => $arrayNew,
				'varsColumn'  => $vars['portal']['varsList']['table']['varsDetail']['varsColumn'],
				'varsStatus'  => $vars['portal']['varsList']['table']['varsDetail']['varsStatus'],
			));
			$vars['portal']['varsList']['varsHtml'] = $varsTemp['strHtml'];
			if ($flag) {
				$this->_setDbStampCheck(array(
					'strColumnAccount'    => $strCheckStamp,
					'strColumnPreference' => 'accounts',
				));
			}
		}

		return $vars;
	}

	/**
		(array(
			'vars'  => array,
			'value' => array,
		))
	 */
	protected function _updateSearchJsonVersion($arr)
	{
		global $classCheck;
		global $classEscape;

		$array = $arr['value'];
		$arrayNew = array();
		$num = 1;
		foreach ($array as $key => $value) {
			$data = array();
			$data['stampRegister'] = $value['stampRegister'];
			$data['stampUpdate'] = $value['stampUpdate'];
			$data['stampBook'] = $value['stampBook'];
			$data['strTitle'] = $value['strTitle'];
			$data['flagIn'] = (int) $value['flagIn'];
			$data['numValueIn'] = number_format($value['numValueIn']);
			$data['numValueOut'] = number_format($value['numValueOut']);
			$data['numBalance'] = number_format($value['numBalance']);
			$data['vars']['numValueIn'] = $value['numValueIn'];
			$data['vars']['numValueOut'] = $value['numValueOut'];
			$data['vars']['numBalance'] = $value['numBalance'];
			$data['idLogAccount'] = $value['idLogAccount'];
			$data['arrSpaceStrTag'] = preg_replace('/^ /', '', $value['arrSpaceStrTag']);
			$data['vars']['arrSpaceStrTag'] = $classEscape->splitSpaceArrayData(array('data' => $value['arrSpaceStrTag']));
			$data['strVersion'] = 'Ver.' . $num;
			$data['numVersion'] = $num;
			$arrayNew[] = $data;
			$num++;
		}

		return $arrayNew;
	}

	/**
		(array(

			'vars'  => $vars['varsItem']['varsJsonChargeHistory'],
			'value' => $value['jsonChargeHistory'],

		))
	 */
	protected function _getJsonChargeHistoryVarsDetail($arr)
	{
		$classTime = new Code_Else_Lib_Time();

		global $varsAccount;
		global $varsAccounts;
		global $varsPluginAccountingAccountsId;

		$array = $arr['value'];
		if (!$arr['value']) {
			$array = array();
		}
		$varsDetail = array();
		$varsData = array();
		$num = 1;
		foreach ($array as $key => $value) {
			$tmplDetail = $arr['tmplDetail'];
			$tmplDetail['id'] = $num;

			$strNo = $num;
			$tmplData = $arr['tmplData'];
			$tmplData['value'] = $strNo;
			$tmplDetail['varsDetail']['id'] = $tmplData;

			$classTime->setTimeZone(array('data' => $varsAccount['numTimeZone']));
			$strTime = $classTime->getDisplay(array(
				'stamp'    => $value['stampRegister'],
				'flagType' => 1,
			));
			$tmplData = $arr['tmplData'];
			$tmplData['value'] = $strTime;
			$tmplDetail['varsDetail']['stampRegister'] = $tmplData;


			$strCodeName = $varsAccounts[$value['idAccount']]['strCodeName'];
			if (!$varsAccounts[$value['idAccount']]['strCodeName']) {
				$strCodeName = $varsPluginAccountingAccountsId[$value['idAccount']]['strCodeName'];
			}
			$tmplData = $arr['tmplData'];
			$tmplData['value'] = $strCodeName;
			$tmplDetail['varsDetail']['idAccount'] = $tmplData;

			$varsDetail[] = $tmplDetail;

			$tempVars = array();
			$tempVars['idAccount'] = $value['idAccount'];
			$tempVars['strCodeName'] = $strCodeName;
			$varsData[] = $tempVars;

			$num++;
		}

		$data = array(
			'varsDetail' => $varsDetail,
			'varsData' => $varsData,
		);

		return $data;
	}

	/**
		(array(

			'vars'  => $vars['varsItem']['varsJsonChargeHistory'],
			'value' => $value['jsonChargeHistory'],

		))
	 */
	protected function _getJsonWriteHistoryVarsDetail($arr)
	{
		$classTime = new Code_Else_Lib_Time();
		global $varsPluginAccountingAccount;


		global $varsAccount;
		global $varsAccounts;
		global $varsPluginAccountingAccountsId;

		$array = $arr['value'];
		if (!$arr['value']) {
			$array = array();
		}
		$varsDetail = array();
		$varsData = array();
		$num = 1;
		foreach ($array as $key => $value) {
			$tmplDetail = $arr['tmplDetail'];
			$tmplDetail['id'] = $num;

			$strNo = $num;
			$tmplData = $arr['tmplData'];
			$tmplData['value'] = $strNo;
			$tmplDetail['varsDetail']['id'] = $tmplData;

			$classTime->setTimeZone(array('data' => $varsAccount['numTimeZone']));
			$strTime = $classTime->getDisplay(array(
				'stamp'    => $value['stampRegister'],
				'flagType' => 1,
			));
			$tmplData = $arr['tmplData'];
			$tmplData['value'] = $strTime;
			$tmplDetail['varsDetail']['stampRegister'] = $tmplData;

			$strCodeName = $varsAccounts[$value['idAccount']]['strCodeName'];
			if (!$varsAccounts[$value['idAccount']]['strCodeName']) {
				$strCodeName = $varsPluginAccountingAccountsId[$value['idAccount']]['strCodeName'];
			}
			$tmplData = $arr['tmplData'];
			$tmplData['value'] = $strCodeName;
			$tmplDetail['varsDetail']['idAccount'] = $tmplData;

			if ($arr['flagLog']) {
				$tmplData = $arr['tmplData'];
				$tmplData['value'] = $value['idLog'];
				$tmplDetail['varsDetail']['idLog'] = $tmplData;
			}

			$varsDetail[] = $tmplDetail;
			$tempVars = array();
			$tempVars['idAccount'] = $value['idAccount'];
			$tempVars['strCodeName'] = $strCodeName;
			if ($arr['flagLog']) {
				$tempVars['idLog'] = $value['idLog'];
			}
			$varsData[] = $tempVars;

			$num++;
		}

		$data = array(
			'varsDetail' => $varsDetail,
			'varsData'   => $varsData,
		);

		return $data;
	}

	/**
	 *
	 */
	protected function _iniNaviSearch()
	{
		$this->_setSearch(array('flag' => 1));
	}


	/**
	 * array(
	 * 	'flag' => int
	 * )
	 */
	protected function _setSearch($arr)
	{
		global $varsRequest;
		global $varsPluginAccountingPreference;
		global $varsPluginAccountingAccount;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingPreference['jsonStampUpdate']['logBanks'],
				'flagSearch'  => 1,
			));
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$array = $varsItem;
		foreach ($array as $key => $value) {
			$vars['varsItem'][$key] = $value;
		}

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$this->checkSearch(array(
			'arrOrder' => $vars['portal']['varsNavi']['search']['varsDetail']['varsSearchSort'],
			'arrWhere' => $vars['portal']['varsNavi']['search']['varsDetail']['varsSearchItem'],
		));

		if (!$varsRequest['query']['jsonSearch']['ph']['arrOrder']) {
			$varsRequest['query']['jsonSearch']['ph']['arrOrder']= array(
				'strColumn' => 'id',
				'flagDesc'  => 1,
			);
		}

		$rows = $this->getSearch(array(
			'idModule'   => 'accounting',
			'numLotNow'  => $varsRequest['query']['jsonSearch']['numLotNow'],
			'strTable'   => 'accountingLogBanks',
			'arrJoin'    => array(),
			'arrOrder'   => $varsRequest['query']['jsonSearch']['ph']['arrOrder'],
			'insCurrent' => $this,
			'arrWhere'   => $varsRequest['query']['jsonSearch']['ph']['arrWhere'],
		));

		$vars = $this->_updateSearch(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'rows'     => $rows,
		));

		$this->sendVars(array(
			'flag'    => ($arr['flag'])? $arr['flag'] : 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'numRows'    => $rows['numRows'],
				'varsDetail' => $vars['portal']['varsList']['varsDetail'],
				'strComment' => ($arr['strComment'])? $arr['strComment'] : '',
				'varsHtml'   => $vars['portal']['varsList']['varsHtml'],
			),
		));
	}

	/**
	 *
	 */
	protected function _iniListReload()
	{
		$this->_setSearch(array('flag' => 1));
	}


	/**
	 *
	 */
	protected function _iniDetailReload()
	{
		$this->_iniSearchDetail();
	}

	/**
	 *
	 */
	protected function _iniSearchDetail()
	{
		global $varsRequest;
		global $varsPluginAccountingPreference;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingPreference['jsonStampUpdate']['logBanks'],
				'flagSearch'  => 1,
			));
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$array = $varsItem;
		foreach ($array as $key => $value) {
			$vars['varsItem'][$key] = $value;
		}

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$this->checkSearch(array(
			'arrOrder' => $vars['portal']['varsNavi']['search']['varsDetail']['varsSearchSort'],
			'arrWhere' => $vars['portal']['varsNavi']['search']['varsDetail']['varsSearchItem'],
		));

		if (!$varsRequest['query']['jsonSearch']['ph']['arrOrder']) {
			$varsRequest['query']['jsonSearch']['ph']['arrOrder']= array(
				'strColumn' => 'id',
				'flagDesc'  => 1,
			);
		}

		$rows = $this->getSearch(array(
			'idModule'   => 'accounting',
			'numLotNow'  => $varsRequest['query']['jsonSearch']['numLotNow'],
			'strTable'   => 'accountingLogBanks',
			'arrJoin'    => array(),
			'arrOrder'   => $varsRequest['query']['jsonSearch']['ph']['arrOrder'],
			'insCurrent' => $this,
			'arrWhere'   => $varsRequest['query']['jsonSearch']['ph']['arrWhere'],
		));

		$vars = $this->_updateSearch(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'rows'     => $rows,
		));

		$varsTarget = $vars;

		$rowsTarget = $this->getSearch(array(
			'idModule'  => 'accounting',
			'numLotNow' => 0,
			'strTable'   => 'accountingLogBanks',
			'arrJoin'   => array(),
			'arrOrder'  => array(),
			'arrWhere'  => array(array(
				'flagType'      => 'num',
				'strColumn'     => 'idLogBanks',
				'flagCondition' => 'eq',
				'value'         => $varsRequest['query']['jsonValue']['idTarget'],
			)),
			'insCurrent'  => $this,
		));

		if (!$rowsTarget['numRows']) {
			$this->_sendOldError();
		}

		$varsTarget = $this->_updateSearch(array(
			'vars'     => $varsTarget,
			'rows'     => $rowsTarget,
			'varsItem' => $varsItem,
			'flagVars' => 1,
		));

		$this->sendVars(array(
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'varsDetail' => $varsTarget['portal']['varsList']['varsDetail'][0],
				'numRows'    => $rows['numRows'],
				'numLotNow'  => $varsRequest['query']['jsonSearch']['numLotNow'],
				'varsList'   => $vars['portal']['varsList']['varsDetail'],
				'varsHtml'   => $vars['portal']['varsList']['varsHtml'],
				'idTarget'   => $varsRequest['query']['jsonValue']['idTarget'],
			),
		));
	}

	/**
	 *
	 */
	protected function _iniNaviSearchSave()
	{
		$this->_setNaviSearchSave(array(
			'pathVars'    => $this->_extSelf['pathVarsJs'],
			'strTable'    => 'accountingAccountMemo',
			'strColumn'   => 'jsonBanksNaviSearch',
			'flagEntity'  => 1,
			'flagAccount' => 1,
		));
	}

	/**
		$this->_setNaviSearchSave(array(
			'pathVars'  => '',
			'strTable'  => '',
			'strColumn' => '',
			'flagEntity' => 0,
			'flagAccount' => 0,
		));
	 */
	protected function _setNaviSearchSave($arr)
	{
		global $varsRequest;
		global $classDb;
		$dbh = $classDb->getHandle();
		global $varsAccount;
		global $varsPluginAccountingAccount;

		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$array = $varsItem;
		foreach ($array as $key => $value) {
			$vars['varsItem'][$key] = $value;
		}

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$varsJson = $this->checkValueSearch(array(
			'varsValue'  => ($varsRequest['query']['jsonValue']['vars'])? $varsRequest['query']['jsonValue']['vars'] : array(),
			'varsSearch' => $vars['portal']['varsNavi']['search'],
		));

		$strJson = json_encode($varsJson);
		$this->checkTextSize(array(
			'flag' => 'errorDataMax',
			'str'  => $strJson,
		));
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$idAccount = $varsAccount['id'];
		$arrWhere = array();
		if ($arr['flagEntity']) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idEntity',
				'flagCondition' => 'eq',
				'value'         => $idEntity,
			);
		} else {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idEntity',
				'flagCondition' => 'eq',
				'value'         => 0,
			);
		}

		if ($arr['flagAccount']) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idAccount',
				'flagCondition' => 'eq',
				'value'         => $idAccount,
			);
		}

		$arrWhere[] = array(
			'flagType'      => '',
			'strColumn'     => 'flagColumn',
			'flagCondition' => 'eq',
			'value'         => $arr['strColumn'],
		);

		try {
			$dbh->beginTransaction();

			$classDb->updateRow(array(
				'idModule' => 'accounting',
				'strTable'  => $arr['strTable'],
				'arrColumn' => array('jsonData'),
				'flagAnd'   => 1,
				'arrWhere'  => $arrWhere,
				'arrValue'  => array($strJson),
			));

			if ($arr['flagAccount']) {
				$this->updateDbAccountStamp();
				$this->_updateDbPreferenceStamp(array('strColumn' => 'accounts'));
			}

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$this->sendVars(array(
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $this->_getMemo(array(
				'strTable'    => $arr['strTable'],
				'strColumn'   => $arr['strColumn'],
				'flagEntity'  => $arr['flagEntity'],
				'flagAccount' => $arr['flagAccount'],
			)),
		));
	}

	/**
	 *
	 */
	protected function _iniNaviSearchDelete()
	{
		$this->_setNaviSearchDelete(array(
			'strTable'  => 'accountingAccountMemo',
			'strColumn' => 'jsonBanksNaviSearch',
			'flagEntity'  => 1,
			'flagAccount' => 1,
		));
	}


	/**
	 *
	 */
	protected function _iniNaviSearchReload()
	{
		global $varsPluginAccountingPreference;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingPreference['jsonStampUpdate']['accounts'],
				'flagSearch'  => 0,
			));
		}

		$this->_setNaviSearchReload(array(
			'pathVars'  => $this->_extSelf['pathVarsJs'],
			'strTable'  => 'accountingAccountMemo',
			'strColumn' => 'jsonBanksNaviSearch',
			'flagEntity'  => 1,
			'flagAccount' => 1,
		));
	}

	/**
		$this->_setNaviSearchReload(array(
			'pathVars'  => '',
			'strTable'  => '',
			'strColumn' => '',
			'flagEntity' => 0,
			'flagAccount' => 0,
		));
	 */
	protected function _setNaviSearchReload($arr)
	{
		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$vars = $this->_updateVars(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$vars['portal']['varsNavi']['search']['varsDetail']['varsMyRecord']['varsFormList']['varsDetail'] = $this->_getMemo(array(
			'strTable'    => $arr['strTable'],
			'strColumn'   => $arr['strColumn'],
			'flagEntity'  => $arr['flagEntity'],
			'flagAccount' => $arr['flagAccount'],
		));

		$this->sendVars(array(
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $vars['portal']['varsNavi']['search']['varsDetail'],
		));
	}

	/**
	 *
	 */
	protected function _iniDetailDelete()
	{
		global $varsRequest;

		$this->_setDelete(array(
			'arrId' => array($varsRequest['query']['jsonValue']['idTarget']),
		));
	}

	/**
	 *
	 */
	protected function _iniListDelete()
	{
		global $varsRequest;

		$this->_setDelete(array(
			'arrId' => $varsRequest['query']['jsonValue']['vars'],
		));

	}

	/**
		$this->_setDelete(array(
			'arrId' => array(),
		));
	 */
	protected function _setDelete($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $classCheck;

		global $varsRequest;
		global $varsAccount;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (!$this->_checkCurrent()) {
			$this->_sendOldError();
		}

		$varsAuthority = $this->_getVarsAuthority(array());

		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllDelete'] || $varsAuthority['flagMyDelete'])) {
			$this->_sendOldError();
		}

		$array = $arr['arrId'];
		$flag = $classCheck->checkValueFormat(array(
			'flagType' => 'num',
			'flagArr'  => 1,
			'value'    => $array,
		));
		if ($flag) {
			$this->_sendOldError();
		}
		$arrVarsLog = array();
		foreach ($array as $key => $value) {
			$varsLog = $this->_getVarsLog(array('idTarget' => $value, 'flagRemove' => 0));
			if (!$varsLog) {
				$this->_sendOldError();

			} else {
				if (($varsAuthority != 'admin' && !$varsAuthority['flagAllDelete'] && $varsAuthority['flagMyDelete'])
					&& $varsLog['idAccount'] != $varsAccount['id']
				) {
					continue;
				}
			}
			$arrVarsLog[$value] = $varsLog;
		}
		if (!$arrVarsLog) {
			$this->_sendOldError();
		}

		$stampRemove = TIMESTAMP;
		$flagRemove = 1;

		try {
			$dbh->beginTransaction();

			$arrayNew = array();
			foreach ($array as $key => $value) {
				$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
				$classDb->updateRow(array(
					'idModule'  => 'accounting',
					'strTable'  => 'accountingLogBanks',
					'arrColumn' => array('stampRemove', 'flagRemove'),
					'flagAnd'  => 1,
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
							'value'         => $numFiscalPeriod,
						),
						array(
							'flagType'      => 'num',
							'strColumn'     => 'idLogBanks',
							'flagCondition' => 'eq',
							'value'         => $value,
						),
					),
					'arrValue'  => array($stampRemove, $flagRemove),
				));
			}
			$this->_updateDbPreferenceStamp(array('strColumn' => 'logBanks'));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}
		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
		$this->_setSearch(array('flag' => 1));
	}

	/**
		(array(
			'idTarget' => '',
		))
	 */
	protected function _getVarsLog($arr)
	{
		global $classDb;
		global $varsRequest;

		global $varsPluginAccountingAccount;

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
				'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			),
			array(
				'flagType'      => 'num',
				'strColumn'     => 'idLogBanks',
				'flagCondition' => 'eq',
				'value'         => $arr['idTarget'],
			),
		);

		if (!is_null($arr['flagRemove'])) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'flagRemove',
				'flagCondition' => 'eq',
				'value'         => $arr['flagRemove'],
			);
		}


		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogBanks',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => $arrWhere,
		));

		return $rows['arrRows'][0];
	}

	/**
	 *
	 */
	protected function _iniDetailWrite()
	{
		$this->_setClassExt(array('strClass' => 'BanksWrite'));
	}

	/**
	 *
	 */
	protected function _iniListWrite()
	{
		$this->_setClassExt(array('strClass' => 'BanksWrite'));
	}

	/**
	 *
	 */
	protected function _iniListOutput()
	{
		$this->_setClassExt(array('strClass' => 'BanksOutput'));
	}
	/**
	 *
	 */
	protected function _iniDetailOutput()
	{
		$this->_setClassExt(array('strClass' => 'BanksOutput'));
	}

	/**
		$varsJson = $this->checkValueSearch(array(
			'varsValue'  => array(),
			'varsSearch' => array(),
		));
	 */
	public function checkValueSearch($arr)
	{
		$array = $arr['varsValue'];
		$arrayNew = array();
		$num = 0;
		foreach ($array as $key => $value) {

			$tmplVars = $arr['varsSearch']['varsDetail']['varsMyRecord']['varsFormList']['templateDetail'];
			$tmplVars['id'] = $key + 1;
			$tmplVars['numSort'] = $key + 1;
			$tmplVars['value'] = $value['value'];
			if (is_null($value['vars'])) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
				}
				exit;
			}
			$tmplVars['vars'] = $value['vars'];

			$tmplVars['vars']['flagNow'] = $this->_checkValueSearchFlagNow(array(
				'varsSearch' => $arr['varsSearch'],
				'flagNow'    => $tmplVars['vars']['flagNow'],
			));

			$tmplVars['vars']['flagApply'] = $this->_checkValueFlagApply(array(
				'varsOption' => $arr['varsSearch']['varsDetail']['varsDetail']['templateDetail']['logApply']['arrayOption'],
				'flagApply'  => $tmplVars['vars']['flagApply'],
			));

			$tmplVars['vars']['varsSort'] = $this->_checkValueSearchSort(array(
				'varsSearch' => $arr['varsSearch'],
				'vars'       => $tmplVars['vars']['varsSort'],
			));

			if ($tmplVars['vars']['flagNow'] == 'item') {
				$tmplVars['vars']['varsItem'] = $this->_checkValueSearchItem(array(
					'varsSearch' => $arr['varsSearch'],
					'vars'       => $tmplVars['vars']['varsItem'],
				));
			}
			$arrayNew[$num] = $tmplVars;
			$num++;
		}

		return $arrayNew;
	}

	/**
	 *
	 */
	protected function _checkValueFlagApply($arr)
	{
		$array = $arr['varsOption'];
		$flag = 0;
		foreach ($array as $key => $value) {
			if ($arr['flagApply'] == $value['value']) {
				$flag = 1;
				break;
			}
		}
		if (!$flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
			}
			exit;
		}

		return $arr['flagApply'];
	}

	/**
		(array(
			'flagType' => 'LogCalc'
		))
	 */
	protected function _getDetailLogTarget($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogBanks',
			'arrLimit' => array(),
			'arrOrder'  => array(
				'strColumn' => 'numFiscalPeriod',
				'flagDesc'  => 0,
			),
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
					'value'         =>  $arr['numFiscalPeriod'],
				),
				array(
					'flagType'      => '',
					'strColumn'     => 'idLogBanks',
					'flagCondition' => 'eq',
					'value'         => $arr['idTarget'],
				),
			),
		));

		return $rows;
	}
}
