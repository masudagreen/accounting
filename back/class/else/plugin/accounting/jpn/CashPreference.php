<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CashPreference extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference' => 'cashWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/cashPreference.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/cashPreference.php',
	);

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
		exit;
	}

	/**
	 *
	 */
	protected function _iniJs()
	{
		global $classSmarty;

		$vars = $this->_getVarsJs();

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_updateVars(array(
			'vars'     => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
			'varsItem' => $varsItem,
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
	 *
	 */
	protected function _getVarsJs()
	{
		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		if (!$this->_getVarsFlagAdmin()) {
			$array = array('Admin');
			foreach ($array as $key => $value) {
				$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_removeVarsTree(array(
					'vars'       => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
					'idTarget'   => $value,
				));
			}
		}

		return $vars;
	}

	/**
	 *
	 */
	protected function _getVarsFlagAdmin()
	{
		global $varsAccount;

		return ($this->_checkModuleAdmin(array('idAccount' => $varsAccount['id'],)));

	}

	/**
		(array(
			'vars'     => $vars,
		))
	 */
	protected function _getVarsItem($arr)
	{
		global $varsPluginAccountingAccount;

		$varsPreference = $this->_getVarsPreference(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrAccountTitle = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => array(),
			'numFiscalPeriod'    => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$data = array(
			'varsPreference'  => $varsPreference,
			'arrAccountTitle' => $arrAccountTitle,
		);

		return $data;
	}

	/**
		(array(
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'numFiscalPeriod'    => $numFiscalPeriod,
		))
	 */
	protected function _getAccountTitle($arr)
	{
		$varsFS = $this->_getVarsFS(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$arrayFSList = array('BS' => 1);

		$strCR = $this->_getStrCR(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$arrStrTitles = array();
		$arrSelectTags = array();

		$array = $arrayFSList;
		foreach ($array as $key => $value) {
			$str = 'jsonJgaapAccountTitle'. $key;
			$varsFS[$str] = $this->_setTreeId(array(
				'idParent' => '-',
				'vars'     => $varsFS[$str],
			));
			$varsAccountTitle = $this->_getArrSelectOption(array(
				'arrStrTitle'  => array(),
				'arrSelectTag' => array(),
				'vars'         => $varsFS[$str],
				'flagBS'       => ($key == 'BS')? 1 : 0,
				'flagFS'       => $key,
				'strCR'        => $strCR,
			));
			$arrSelectTags[$key] = $varsAccountTitle['arrSelectTag'];
			$arrStrTitles[$key] = $varsAccountTitle['arrStrTitle'];
		}

		$data = array(
			'arrStrTitles'  => $arrStrTitles,
			'arrSelectTags' => $arrSelectTags,
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
				'strTitle'   => $value['strTitle'],
				'strTitleFS' => $strTitleFS,
				'flagDebit'  => (int) $value['vars']['flagDebit'],
				'flagUse'    => (is_null($value['vars']['flagUse']))? '' : (int) $value['vars']['flagUse'],
				'flagFS'     => $arr['flagFS'],
			);

			$arrLevel = preg_split("/-/", $value['id']);
			$num = count($arrLevel) - 2;
			$arrLevel = array();
			for ($i = 0 ; $i < $num; $i++) {
				$arrLevel[$i] = '';
			}
			$str =  ' ' . join('.', $arrLevel) . ' ';
			$strTitle = $str . $value['strTitle'];
			$strTitleFSTag = $str .  $strTitleFS;

			if ((int) $value['vars']['flagDebit']) {
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

					$arr['arrStrTitle'][$value['vars']['idTarget']] = $data;
				}
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
		))
	 */
	protected function _getVarsPreference($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingCash',
			'arrLimit' => array(),
			'arrOrder'  => array(),
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

		return $rows['arrRows'][0];
	}

	/**
	 *
	 */
	protected function _updateVars($arr)
	{
		$vars = $arr['vars'];
		foreach ($vars as $key => $value) {
			$idTarget = $value['vars']['idTarget'];
			$id = $value['id'];

			if (!preg_match( "/Window$/", $idTarget) && !$value['flagFoldUse']) {
				$method = '_updateVars' . ucwords($idTarget);
				if (method_exists($this, $method)) {
					$vars[$key] = $this->$method(array(
						'vars'     => $vars[$key],
						'varsItem' => $arr['varsItem'],
					));
				}
			} elseif (preg_match( "/Window$/", $idTarget)) {
				$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
				if (preg_match("/^(done)$/", $flagCurrentFlagNow)) {
					$vars[$key]['flagBtnUse'] = 0;
				}
				if (!preg_match("/^(done)$/", $flagCurrentFlagNow)) {
					$vars[$key]['strClassFont'] = '';
				}
			}

			if ($value['child']) {
				$vars[$key]['child'] = $this->_updateVars(array(
					'vars'     => $vars[$key]['child'],
					'varsItem' => $arr['varsItem'],
				));
			}
		}

		return $vars;
	}

	/**
	 *
	 */
	protected function _updateVarsPreference($arr)
	{
		global $classEscape;

		$varsPreference = $arr['varsItem']['varsPreference'];

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done)$/", $flagCurrentFlagNow)) {
			$arr['vars']['vars']['varsBtn'] = array();
		}

		$array = &$arr['vars']['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			$str = $classEscape->toLower(array('str' => $value['id']));
			$array[$key]['value'] = $varsPreference[$str];
			if (preg_match("/^(done)$/", $flagCurrentFlagNow)) {
				$array[$key]['flagDisabled'] = 1;
				$array[$key]['strExplain'] = $value['varsTmpl']['strDone'];
				$arrayOption = $value['arrayOption'];
				foreach ($arrayOption as $keyOption => $valueOption) {
					if ($array[$key]['value'] != $valueOption['value']) {
						continue;
					}
					$strTmpl = $value['varsTmpl']['strDoneItem'];
					$array[$key]['strExplain'] .= str_replace('<%replace%>', $valueOption['strTitle'], $strTmpl);
				}

			}
		}

		return $arr['vars'];
	}

	/**
	 *
	 */
	protected function _updateVarsFlagPayWrite($arr)
	{
		return $this->_updateVarsPreference($arr);
	}

	/**
	 *
	 */
	protected function _updateVarsFlagAutoImport($arr)
	{
		return $this->_updateVarsPreference($arr);
	}

	/**
	 *
	 */
	protected function _updateVarsJsonCash($arr)
	{
		global $classEscape;

		$varsCash = $arr['varsItem']['varsPreference']['jsonCash'];
		if (!$varsCash) {
			$varsCash = array();
		}
		$arrayCheck = array();
		$arrayCash = $varsCash;
		foreach ($arrayCash as $keyCash => $valueCash) {
			$flag = $this->_checkVarsLog(array(
				'idTarget' => $keyCash,
			));
			if ($flag) {
				$arrayCheck[$keyCash] = $arr['varsItem']['arrAccountTitle']['arrStrTitles']['BS'][$keyCash]['strTitleFS'];
			}
		}

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done|tempNext|tempPrev)$/", $flagCurrentFlagNow)) {
			$arr['vars']['vars']['varsBtn'] = array();
		}

		$arrayOption = array();
		$array = $arr['varsItem']['arrAccountTitle']['arrSelectTags']['BS'];
		foreach ($array as $key => $value) {
			$flag = $this->_checkVarsLog(array(
				'idTarget' => $value['value'],
			));
			if ($flag
				//|| $value['value'] == 'suspenseReceiptOfConsumptionTaxes'
				|| $value['value'] == 'suspensePaymentConsumptionTaxes'
			) {
				$value['flagDisabled'] = 1;
				$value['flagUseLog'] = 1;
			}
			$arrayOption[] = $value;
		}

		$array = &$arr['vars']['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			$array[$key]['arrayOption'] = $arrayOption;
			$array[$key]['value'] = $varsCash;
			if (preg_match("/^(done)$/", $flagCurrentFlagNow)) {
				$array[$key]['flagDisabled'] = 1;
				$array[$key]['strExplain'] = $value['varsTmpl']['strDone'];
				$arrayOption = $array[$key]['arrayOption'];
				foreach ($arrayOption as $keyOption => $valueOption) {
					if (!$array[$key]['value'][$valueOption['value']]) {
						continue;
					}
					$strTmpl = $value['varsTmpl']['strDoneItem'];
					$array[$key]['strExplain'] .= str_replace('<%replace%>', $valueOption['strTitle'], $strTmpl);
				}

			} elseif (preg_match("/^(temp)/", $flagCurrentFlagNow)) {
				$array[$key]['flagDisabled'] = 1;
				$array[$key]['strExplain'] = $value['varsTmpl']['strTemp'];
				$arrayOption = $array[$key]['arrayOption'];
				foreach ($arrayOption as $keyOption => $valueOption) {
					if (!$array[$key]['value'][$valueOption['value']]) {
						continue;
					}
					$strTmpl = $value['varsTmpl']['strDoneItem'];
					$array[$key]['strExplain'] .= str_replace('<%replace%>', $valueOption['strTitle'], $strTmpl);
				}

			} else {
				foreach ($arrayCheck as $keyCheck => $valueCheck) {
					$strTmpl = $value['varsTmpl']['strLog'];
					$array[$key]['strExplain'] .= str_replace('<%replace%>', $valueCheck, $strTmpl);
				}
			}
		}

		return $arr['vars'];
	}

	/**
		(array(
			'idTarget' => '',
		))
	 */
	protected function _checkVarsLog($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogCash',
			'arrLimit' => array(
				'numStart' => 0, 'numEnd' => 1,
			),
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
					'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				),
				array(
					'flagType'      => '',
					'strColumn'     => 'arrCommaIdAccountTitleDebit',
					'flagCondition' => 'like',
					'value'         => $arr['idTarget'],
				),
				array(
					'flagType'      => '',
					'strColumn'     => 'flagRemove',
					'flagCondition' => 'eq',
					'value'         => 0,
				),
			),
		));
		if ($rows['numRows']) {
			return 1;
		}

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogCash',
			'arrLimit' => array(
				'numStart' => 0, 'numEnd' => 1,
			),
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
					'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				),
				array(
					'flagType'      => '',
					'strColumn'     => 'arrCommaIdAccountTitleCredit',
					'flagCondition' => 'like',
					'value'         => $arr['idTarget'],
				),
				array(
					'flagType'      => '',
					'strColumn'     => 'flagRemove',
					'flagCondition' => 'eq',
					'value'         => 0,
				),
			),
		));
		if ($rows['numRows']) {
			return 1;
		}

	}

	/**
	 *
	 */
	protected function _iniNaviReload()
	{
		global $varsPluginAccountingPreference;
		global $varsRequest;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingPreference['stampUpdate'],
				'flagSearch'  => 0,
			));
		}

		$vars = $this->_getVarsJs();

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_updateVars(array(
			'vars'     => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
			'varsItem' => $varsItem,
		));

		$this->sendValue(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
		));
	}



	/**
	 *
	 */
	protected function _iniDetailReload()
	{
		global $varsPluginAccountingPreference;
		global $varsRequest;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingPreference['stampUpdate'],
				'flagSearch'  => 0,
			));
		}

		$vars = $this->_getVarsJs();

		$varsTarget = $this->getVarsTarget(array(
			'vars' => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail']
		));
		if (!$varsTarget) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}
		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$method = '_updateVars' . ucwords($varsTarget['vars']['idTarget']);
		if (method_exists($this, $method)) {
			$varsTarget = $this->$method(array(
				'vars'     => $varsTarget,
				'varsItem' => $varsItem,
			));
		}

		$this->sendValue(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $varsTarget,
		));
	}

	/**
	 *
	 */
	protected function _iniDetailEdit()
	{
		global $classInit;
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsRequest;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done)$/", $flagCurrentFlagNow)) {
			$this->_sendOld();
		}

		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];
		$vars = $this->_getVarsJs();

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$varsTarget = $this->getVarsTarget(array(
			'vars' => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail']
		));
		if (!$varsTarget) {
			$this->sendValue(array(
				'flag'    => 8,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}

		$method = '_updateVars' . ucwords($varsTarget['vars']['idTarget']);
		if (method_exists($this, $method)) {
			$varsTarget = $this->$method(array(
				'vars'     => $varsTarget,
				'varsItem' => $varsItem,
			));
		}

		$varsTarget['vars']['varsDetail'] = $this->getValue(array(
			'vars' => $varsTarget['vars']['varsDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget['vars']['varsDetail']
		));

		try {
			$dbh->beginTransaction();

			$this->_updateDb(array(
				'arrValue'   => $arrValue,
				'varsTarget' => $varsTarget,
				'varsItem'   => $varsItem,
			));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$method = '_updateVars' . ucwords($varsTarget['vars']['idTarget']);
		if (method_exists($this, $method)) {
			$varsTarget = $this->getVarsTarget(array(
				'vars' => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail']
			));
			$varsItem = $this->_getVarsItem(array(
				'vars' => $vars,
			));
			$varsTarget = $this->$method(array(
				'vars'     => $varsTarget,
				'varsItem' => $varsItem,
			));
		}

		$this->sendValue(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $varsTarget,
		));
	}

	/**
	 *
	 */
	protected function _updateDb($arr)
	{
		global $varsRequest;

		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];
		$method = '_updateDb' . ucwords($idTarget);
		if (method_exists($this, $method)) {
			$this->$method($arr);
		}
	}

	/**

	 */
	protected function _updateDbJsonCash($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(done|tempNext|tempPrev)$/", $flagCurrentFlagNow)) {
			$this->_sendOld();
		}

		$varsCashPreference = $arr['varsItem']['varsPreference']['jsonCash'];
		$varsValue = $arr['arrValue']['arr']['jsonCash'];
		$varsCash = array();
		$array = $arr['varsTarget']['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			$arrayOption = $value['arrayOption'];
			foreach ($arrayOption as $keyOption => $valueOption) {
				if ($varsCashPreference[$valueOption['value']]) {
					if ($valueOption['flagUseLog']) {
						$varsCash[$valueOption['value']] = 1;
					} else {
						if ($varsValue[$valueOption['value']]) {
							$varsCash[$valueOption['value']] = 1;
						}
					}
				} else {
					if ($valueOption['flagUseLog']) {

					} else {
						if ($varsValue[$valueOption['value']]) {
							$varsCash[$valueOption['value']] = 1;
						}
					}
				}
			}
		}

		if (!$varsCash) {
			$this->_sendOld();
		}

		$jsonCash = json_encode($varsCash);

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingCash',
			'arrColumn' => $arr['arrValue']['arrColumn'],
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
			'arrValue'  => array($jsonCash),
		));
	}

	/**

	 */
	protected function _updateDbFlagPayWrite($arr)
	{
		$this->_updateDbPreference($arr);
	}

	/**

	 */
	protected function _updateDbFlagAutoImport($arr)
	{
		$this->_updateDbPreference($arr);
	}

	/**

	 */
	protected function _updateDbPreference($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingCash',
			'arrColumn' => $arr['arrValue']['arrColumn'],
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
			'arrValue'  => $arr['arrValue']['arrValue'],
		));
	}
}
