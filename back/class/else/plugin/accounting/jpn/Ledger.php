<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_Ledger extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extSelf = array(
		'idPreference' => 'ledgerWindow',
		'idLog'        => 'logWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/ledger.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/ledger.php',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;
		global $classCheck;
		global $varsPluginAccountingPreference;
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

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];

		$flagAuthority = $classCheck->checkModuleAuthority(array(
			'idModule' => 'accounting',
		));

		if ($flagAuthority) {
			$array = array(
				'strSql'   => 'idEntity = ? and numFiscalPeriod = ?',
				'arrValue' => array($idEntity, $numFiscalPeriod),
			);
			return $array;

		}
		return 0;

	}

	/**
	 *
	 */
	protected function _iniJs()
	{
		global $classSmarty;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
			'varsFlag' => $vars['varsFlag'],
		)));

		$vars['varsFlag']['idAccountTitle'] = $this->_getSelectValueFirstItem(array(
			'varsItem' => $varsItem,
			'varsFlag' => $vars['varsFlag'],
		));

		if (!$vars['varsFlag']['idAccountTitle']) {
			$vars['portal']['varsList']['varsPage']['varsStatus']['numRows'] = 0;
			$vars['portal']['varsList']['varsDetail'] = array();

		} else {
			$data = $this->_getSearch(array(
				'numLotNow' => 0,
				'vars'      => $vars,
				'varsItem'  => $varsItem,
				'varsFlag'  => $vars['varsFlag'],
			));

			$vars = $this->_updateSearch(array(
				'numLotNow' => 0,
				'vars'      => $vars,
				'varsItem'  => $varsItem,
				'varsFlag'  => $vars['varsFlag'],
				'rows'      => $data['rows'],
				'numPrev'   => $data['numPrev'],
				'flagLast'  => $data['flagLast'],
			));
		}

		$vars['varsRule'] = $varsItem;

		$vars['flagAuthorityLog'] = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'select',
			'idTarget'      => $this->_extSelf['idLog'],
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

		$varsDepartment = $this->_getVarsDepartment(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrSubAccountTitle = $this->_getVarsSubAccountTitle(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$arrAccountTitle = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'numFiscalPeriod'    => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$data = array(
			'arrSubAccountTitle' => $arrSubAccountTitle,
			'arrAccountTitle'    => $arrAccountTitle,
			'varsDepartment'      => $varsDepartment,
			'varsEntityNation'   => $varsEntityNation,
		);

		return $data;

	}

	/**
		(array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
			'varsFlag' => $vars['varsFlag'],
		))
	 */
	protected function _updateVarsNavi($arr)
	{
		$vars = &$arr['vars'];

		$arrayNew = array();
		$array = &$vars['portal']['varsNavi']['templateDetail'];
		foreach ($array as $key => $value) {
			$method = '_updateVarsNavi' . $value['id'];
			$value = $this->$method(array(
				'value'    => $value,
				'vars'     => $vars,
				'varsItem' => $arr['varsItem'],
				'varsFlag' => $arr['varsFlag'],
			));
			$arrayNew[] = $value;
		}
		$vars['portal']['varsNavi']['templateDetail'] = $arrayNew;

		return $vars['portal']['varsNavi']['templateDetail'];
	}

	/**
		(array(
			'value'    => $value,
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		))
	 */
	protected function _updateVarsNaviIdDepartment($arr)
	{
		if (!$arr['varsItem']['varsDepartment']['arrSelectTag']) {
			return $arr['value'];
		}
		$arrSelectTag = $arr['varsItem']['varsDepartment']['arrSelectTag'];
		array_unshift($arrSelectTag, $arr['value']['varsTmpl']['varsNone']);
		$arr['value']['arrayOption'] = $arrSelectTag;

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
	protected function _updateVarsNaviIdAccountTitle($arr)
	{
		$arr['value']['value'] = $this->_getSelectValueFirstItem(array(
			'varsItem' => $arr['varsItem'],
			'varsFlag' => $arr['varsFlag'],
		));

		$arr['value']['arrayOption'] = $arr['varsItem']['arrAccountTitle']['arrSubAccountSelectTag'];

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
	protected function _updateVarsNaviIdSubAccountTitle($arr)
	{
		if (!$arr['varsItem']['arrSubAccountTitle']['arrSelectTag'][$arr['varsFlag']['idAccountTitle']]) {
			$arr['value']['flagHideNow'] = 1;

			return $arr['value'];
		}
		$arrSelectTag = $arr['varsItem']['arrSubAccountTitle']['arrSelectTag'][$arr['varsFlag']['idAccountTitle']];
		array_unshift($arrSelectTag, $arr['value']['varsTmpl']['varsNone']);
		$arr['value']['arrayOption'] = $arrSelectTag;

		return $arr['value'];
	}

	/**


	 */
	protected function _getSelectValueFirstItem($arr)
	{
		$array = $arr['varsItem']['arrAccountTitle']['arrSelectTag'];
		foreach ($array as $key => $value) {
			if (!$value['flagDisabled']) {
				return $value['value'];
			}
		}

		return '';
	}

	/**
		(array(
			'value'    => $value,
			'vars'     => $vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		))
	 */
	protected function _updateVarsNaviFlagFiscalPeriod($arr)
	{
		$varsEntityNation = $arr['varsItem']['varsEntityNation'];

		$arrayNew = array();
		$array = $arr['value']['varsTmpl']['arrayOption'];
		foreach ($array as $key => $value) {
			if ($varsEntityNation['numFiscalTermMonth'] == 12) {
				$arrayNew[] = $value;

			} else {
				if (preg_match( "/^(f1)$/", $value['value'])) {
					$arrayNew[] = $value;
				}
			}
		}
		$arr['value']['varsTmpl']['arrayOption'] = $arrayNew;

		$arrayOption = array();
		if ($varsEntityNation['numFiscalTermMonth'] == 12) {
			$arrayOption = $arr['value']['varsTmpl']['arrayOption'];

		} else {
			$arrayOption[] = $arr['value']['varsTmpl']['varsPeriod'];
		}

		$numMonth = (int) $varsEntityNation['numFiscalBeginningMonth'];
		$numEnd = (int) $varsEntityNation['numFiscalTermMonth'];
		for ($i = 0; $i < $numEnd; $i++) {
			$data = array(
				'strTitle' => $numMonth . $arr['value']['varsTmpl']['strMonth'],
				'value'    => $numMonth,
			);
			$arrayOption[] = $data;
			$numMonth++;
			if ($numMonth > 12) {
				$numMonth = 1;
			}
		}

		$arr['value']['arrayOption'] = $arrayOption;
		$arr['value']['numSize'] = count($arrayOption);

		return $arr['value'];
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
				'stampTarget' => $varsPluginAccountingPreference['jsonStampUpdate']['log'],
				'flagSearch'  => 1,
			));
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$varsFlag = array(
			'flagFiscalPeriod'  => $varsRequest['query']['jsonValue']['vars']['FlagFiscalPeriod'],
			'idDepartment'      => $varsRequest['query']['jsonValue']['vars']['IdDepartment'],
			'idAccountTitle'    => $varsRequest['query']['jsonValue']['vars']['IdAccountTitle'],
			'idSubAccountTitle' => $varsRequest['query']['jsonValue']['vars']['IdSubAccountTitle'],
		);

		$vars['portal']['varsNavi']['varsDetail'] = $this->_updateVarsNavi((array(
			'vars'     => &$vars,
			'varsItem' => $varsItem,
			'varsFlag' => $varsFlag,
		)));

		$this->_checkValueDetail(array(
			'varsDetail' => $vars['portal']['varsNavi']['varsDetail'],
			'varsFlag'   => $varsFlag,
		));

		$varsFlag['flagFS'] = $varsItem['arrAccountTitle']['arrStrTitle'][$varsFlag['idAccountTitle']]['flagFS'];

		$data = $this->_getSearch(array(
			'numLotNow' => (int) $varsRequest['query']['jsonSearch']['numLotNow'],
			'vars'      => $vars,
			'varsItem'  => $varsItem,
			'varsFlag'  => $varsFlag,
		));

		$vars = $this->_updateSearch(array(
			'numLotNow' => (int) $varsRequest['query']['jsonSearch']['numLotNow'],
			'vars'      => $vars,
			'varsItem'  => $varsItem,
			'varsFlag'  => $varsFlag,
			'rows'      => $data['rows'],
			'numPrev'   => $data['numPrev'],
			'flagLast'  => $data['flagLast'],
		));

		$this->sendVars(array(
			'flag'    => ($arr['flag'])? $arr['flag'] : 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'numRows'    => $data['rows']['numRows'],
				'varsDetail' => $vars['portal']['varsList']['varsDetail'],
				'varsHtml'   => $vars['portal']['varsList']['varsHtml'],
			),
		));

	}

	/**

	 */
	protected function _getSearch($arr)
	{
		global $classDb;

		global $varsRequest;
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
		);

		if ($arr['varsFlag']['idDepartment'] != 'none') {
			$arrWhere[] = array(
				'flagType'      => '',
				'strColumn'     => 'idDepartment',
				'flagCondition' => 'eq',
				'value'         => $arr['varsFlag']['idDepartment'],
			);
		}
		if ($arr['varsFlag']['idSubAccountTitle'] == 'none') {
			$arrWhere[] = array(
				'flagType'      => '',
				'strColumn'     => 'idAccountTitle',
				'flagCondition' => 'eq',
				'value'         => $arr['varsFlag']['idAccountTitle'],
			);

		} else {
			$arrWhere[] = array(
				'flagType'      => '',
				'strColumn'     => 'idAccountTitle',
				'flagCondition' => 'eq',
				'value'         => $arr['varsFlag']['idAccountTitle'],
			);
			$arrWhere[] = array(
				'flagType'      => '',
				'strColumn'     => 'idSubAccountTitle',
				'flagCondition' => 'eq',
				'value'         => $arr['varsFlag']['idSubAccountTitle'],
			);
		}
		$arrWherePrev = $arrWhere;
		$data = $this->_getVarsStampTerm(array(
			'varsFlag'         => $arr['varsFlag'],
			'varsEntityNation' => $arr['varsItem']['varsEntityNation'],
			'numFiscalPeriod'  => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));
		if ($arr['varsFlag']['flagFiscalPeriod'] != 'f1') {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'stampBook',
				'flagCondition' => 'eqBig',
				'value'         => $data['stampMin'],
			);
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'stampBook',
				'flagCondition' => 'eqSmall',
				'value'         => $data['stampMax'],
			);
			/*
			$arrWhere[] = array(
				'flagType'      => '',
				'strColumn'     => 'flagFiscalReport',
				'flagCondition' => 'ne',
				'value'         => 'f1',
			);
			*/

		} else {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'numFiscalPeriod',
				'flagCondition' => 'eq',
				'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			);
		}

		$numLotNow = $arr['numLotNow'];

		$rows = $this->getSearch(array(
			'idModule'  => 'accounting',
			'numLotNow' => $numLotNow,
			'strTable'  => 'accountingLogCalc' . $strNation,
			'arrOrder'  => array(
				'strColumn' => 'stampBook,idLog,id',
			),
			'flagAnd'  => 1,
			'arrWhere' => $arrWhere,
		));

		$numPrev = 0;
		if ($arr['varsFlag']['flagFS'] == 'BS'
			&& ($arr['numLotNow'] == 0 || is_null($arr['numLotNow']))
		) {
			$numPrev = $this->_getSearchNumPrev(array(
				'varsFlag' => $arr['varsFlag'],
			));
		}

		$arrWhereLast = $arrWhere;
		$flagLast = 0;
		if ($rows['numRows']
			&& $arr['varsFlag']['flagFS'] == 'BS'
		) {
			$rowsLast = $classDb->getSelect(array(
				'idModule'    => 'accounting',
				'strTable'    => 'accountingLogCalc' . $strNation,
				'arrJoin'     => '',
				'arrLimit'    => array(
					'numStart' => 0, 'numEnd' => 1,
				),
				'arrOrder'  => array(
					'strColumn' => 'stampBook desc,idLog desc,id desc',
				),
				'arrWhere'    => $arrWhereLast,
				'flagAnd'     => 1,
			));
			$dataLast = end($rowsLast['arrRows']);
			$idLast = $dataLast['id'];
			$dataEnd = end($rows['arrRows']);
			$idEnd = $dataEnd['id'];
			if ($idLast == $idEnd) {
				$flagLast = 1;
			}

		} else {
			if ($arr['numLotNow'] == 0 || is_null($arr['numLotNow'])) {
				$flagLast = 1;
			}
		}

		$data = array(
			'rows'     => $rows,
			'numPrev'  => $numPrev,
			'flagLast' => $flagLast,
		);

		return $data;
	}

	/**

	 */
	protected function _getSearchNumPrev($arr)
	{
		global $classDb;

		global $varsRequest;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$numValue = 0;

		if ($arr['varsFlag']['flagFS'] != 'BS') {
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
				'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			),
		);

		if ($arr['varsFlag']['idDepartment'] == 'none') {
			if ($arr['varsFlag']['idSubAccountTitle'] == 'none') {
				//numBalance
				$strTable = 'accountingFSValue' . $strNation;

			} else {
				//numBalanceSubAccount
				$strTable = 'accountingSubAccountTitleValue' . $strNation;

				$arrWhere[] = array(
					'flagType'      => '',
					'strColumn'     => 'idSubAccountTitle',
					'flagCondition' => 'eq',
					'value'         => $arr['varsFlag']['idSubAccountTitle'],
				);
			}

		} else {
			if ($arr['varsFlag']['idSubAccountTitle'] == 'none') {
				//numBalanceDepartment
				$strTable = 'accountingEntityDepartmentFSValue' . $strNation;

				$arrWhere[] = array(
					'flagType'      => '',
					'strColumn'     => 'idDepartment',
					'flagCondition' => 'eq',
					'value'         => $arr['varsFlag']['idDepartment'],
				);

			} else {
				//numBalanceDepartmentSubAccount
				$strTable = 'accountingSubAccountTitleValue' . $strNation;

				$arrWhere[] = array(
					'flagType'      => '',
					'strColumn'     => 'idSubAccountTitle',
					'flagCondition' => 'eq',
					'value'         => $arr['varsFlag']['idSubAccountTitle'],
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

		$idAccountTitle = $arr['varsFlag']['idAccountTitle'];
		$flagFiscalPeriod = $arr['varsFlag']['flagFiscalPeriod'];

		if ($arr['varsFlag']['idDepartment'] == 'none') {
			if ($arr['varsFlag']['idSubAccountTitle'] == 'none') {
				//numBalance
				$numValue = $rows['arrRows'][0]['jsonJgaapAccountTitleBS'][$flagFiscalPeriod][$idAccountTitle]['sumPrev'];

			} else {
				//numBalanceSubAccount
				$numValue = $rows['arrRows'][0]['jsonData']['all'][$flagFiscalPeriod]['sumPrev'];
			}

		} else {
			if ($arr['varsFlag']['idSubAccountTitle'] == 'none') {
				//numBalanceDepartment
				$numValue = $rows['arrRows'][0]['jsonJgaapAccountTitleBS'][$flagFiscalPeriod][$idAccountTitle]['sumPrev'];

			} else {
				//numBalanceDepartmentSubAccount
				$idDepartment = $arr['varsFlag']['idDepartment'];
				$numValue = $rows['arrRows'][0]['jsonData'][$idDepartment][$flagFiscalPeriod]['sumPrev'];
			}
		}

		if (is_null($numValue)) {
			$numValue = 0;
		}

		return $numValue;
	}



	/**
		(array(
			'varsDetail'       => $varsDetail,
			'FlagFiscalPeriod' => $flagFiscalPeriod,
			'FlagUnit'         => $flagUnit,
		))
	 */
	protected function _sendVarsBlank()
	{
		$this->sendVars(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'numRows'    => 0,
				'varsDetail' => array(),
			),
		));
	}

	/**
		(array(
			'varsDetail'       => $varsDetail,
			'FlagFiscalPeriod' => $flagFiscalPeriod,
			'FlagUnit'         => $flagUnit,
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
			'vars'             => $vars,
			'varsItem'         => $varsItem,
			'varsFlag'         => $varsFlag,
			'rows'             => $data['rows'],
			'numPrev'         => $data['numPrev'],
			'flagLast'         => $data['flagLast'],

	 */
	protected function _updateSearch($arr)
	{
		global $varsRequest;
		global $varsAccount;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		global $classEscape;
		global $classHtml;
		global $classTime;

		$vars = &$arr['vars'];
		$rows = &$arr['rows'];
		$numPrev = $arr['numPrev'];
		$varsItem = $arr['varsItem'];
		$varsFlag = $arr['varsFlag'];
		$dataStamp = $this->_getVarsStampTerm(array(
			'varsFlag'         => $arr['varsFlag'],
			'varsEntityNation' => $arr['varsItem']['varsEntityNation'],
			'numFiscalPeriod'  => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$array = $rows['arrRows'];
		$arrayNew = array();
		$num = 0;
		$flag = 0;
		$numBalancePrev = 0;

		if ($varsFlag['flagFS'] == 'BS'
			&& ($arr['numLotNow'] == 0 || is_null($arr['numLotNow']))
		) {

			$varsTmpl = $vars['portal']['varsList']['templateDetail'];
			$arrDate = $classTime->getLocal(array('stamp' => $dataStamp['stampMin']));

			//date
			$varsTmpl['varsColumnDetail']['strDate'] = $arrDate['strMonth'] . '/' . $arrDate['strDate'];
			$varsTmpl['varsColumnDetail']['strDateYear'] = $arrDate['strYear'] . '/' . $arrDate['strMonth'] . '/' . $arrDate['strDate'];
			$varsTmpl['flagBtnUse'] = 0;
			$varsTmpl['flagBoldNow'] = 0;
			$varsTmpl['strClassFont']= $vars['varsItem']['strClassFont'];
			$varsTmpl['strClass'] = $vars['varsItem']['strClass'];
			$varsTmpl['strClassLoad'] = '';
			$varsTmpl['strTitle'] = $vars['varsItem']['strPrevTerm'];
			$varsTmpl['vars']['flagPrev'] = 1;
			$varsTmpl['varsColumnDetail']['strPrevTerm'] = $vars['varsItem']['strPrevTerm'];
			$varsTmpl['varsColumnDetail']['stampBook'] = $dataStamp['stampMin'];
			$varsTmpl['varsScheduleDetail']['stamp'] = $dataStamp['stampMin'];
			$varsTmpl['varsScheduleDetail']['strTitle'] = $varsTmpl['strTitle'];
			$varsTmpl['varsColumnDetail']['strTitle'] = $varsTmpl['strTitle'];

			if (!is_null($numPrev)) {
				$varsTmpl['id'] = 'dummy';
				$varsTmpl['vars']['idTarget'] = 'dummy';
				$numBalancePrev = $numPrev;
			}
			$varsTmpl['vars']['numBalance'] = $numBalancePrev;
			$varsTmpl['varsColumnDetail']['numBalance'] = number_format($numBalancePrev);
			$arrayNew[$num] = $varsTmpl;
			$num++;
		}

		$flagFirstRow = 1;
		foreach ($array as $key => $value) {
			$varsTmpl = $vars['portal']['varsList']['templateDetail'];
			$varsTmpl['id'] = $value['id'];
			$varsTmpl['vars']['idTarget'] = $value['id'];
			$varsTmpl['numSort'] = (int) $key;
			$varsTmpl['strTitle'] = $value['strTitle'];

			$varsTmpl['vars']['flagFirstRow'] = 0;
			if ($flagFirstRow) {
				$varsTmpl['vars']['flagFirstRow'] = 1;
				$flagFirstRow = 0;
			}

			$varsTmpl['strClassLoad'] = '';

			$strTitle = ($varsTmpl['strTitle'])? $varsTmpl['strTitle'] : '-';

			$strDepartment = $varsItem['varsDepartment']['arrStrTitle'][$value['idDepartment']]['strTitle'];
			$strDepartment = ($strDepartment)? $strDepartment : '';

			$strDepartmentContra = $varsItem['varsDepartment']['arrStrTitle'][$value['idDepartmentContra']]['strTitle'];
			$strDepartmentContra = ($strDepartmentContra)? $strDepartmentContra : '';
			if ($value['idAccountTitleContra'] == 'else') {
				$strDepartmentContra = '';
			}

			$strAccountTitleContra = $varsItem['arrAccountTitle']['arrStrTitle'][$value['idAccountTitleContra']]['strTitle'];
			$strAccountTitleContra = ($strAccountTitleContra)? $strAccountTitleContra : '';
			if ($value['idAccountTitleContra'] == 'else') {
				$strAccountTitleContra = $vars['varsItem']['strSundries'];
			}
			$strSubAccountTitle = $varsItem['arrSubAccountTitle']['arrStrTitle'][$value['idAccountTitle']][$value['idSubAccountTitle']]['strTitle'];
			$strSubAccountTitle = ($strSubAccountTitle)? $strSubAccountTitle : '';

			$strSubAccountTitleContra = $varsItem['arrSubAccountTitle']['arrStrTitle'][$value['idAccountTitleContra']][$value['idSubAccountTitleContra']]['strTitle'];
			$strSubAccountTitleContra = ($strSubAccountTitleContra)? $strSubAccountTitleContra : '';
			if ($value['idAccountTitleContra'] == 'else') {
				$strSubAccountTitleContra = '';
			}

			$strFlagFiscalReport = $vars['varsItem']['varsOutput']['strBlank'];
			$strFlagFiscalReportCut = $vars['varsItem']['varsOutput']['strBlank'];
			if ($value['flagFiscalReport'] === 'f1') {
				$strFlagFiscalReport = $vars['varsItem']['strFlagFiscalReport1'];
				$strFlagFiscalReportCut = $vars['varsItem']['varsOutput']['strFiscalReport1'];

			} elseif ($value['flagFiscalReport'] === 'f21') {
				$strFlagFiscalReport = $vars['varsItem']['strFlagFiscalReport2'];
				$strFlagFiscalReportCut = $vars['varsItem']['varsOutput']['strFiscalReport2'];
			}


			if ((int) $value['flagDebit']) {
				$varsTmpl['vars']['flagDebit'] = $value['numValue'];
				$varsTmpl['vars']['flagCredit'] = '';
				$varsTmpl['varsColumnDetail']['flagDebit'] = number_format($value['numValue']);
				$varsTmpl['varsColumnDetail']['flagCredit'] = '';

			} else {
				$varsTmpl['vars']['flagDebit'] = '';
				$varsTmpl['vars']['flagCredit'] = $value['numValue'];
				$varsTmpl['varsColumnDetail']['flagDebit'] = '';
				$varsTmpl['varsColumnDetail']['flagCredit'] = number_format($value['numValue']);
			}

			if ($varsFlag['idDepartment'] == 'none') {
				if ($varsFlag['idSubAccountTitle'] == 'none') {
					$numBalance = $value['numBalance'];

				} else {
					$numBalance = $value['numBalanceSubAccount'];
				}

			} else {
				if ($varsFlag['idSubAccountTitle'] == 'none') {
					$numBalance = $value['numBalanceDepartment'];

				} else {
					$numBalance = $value['numBalanceDepartmentSubAccount'];
				}
			}

			$arrDate = $classTime->getLocal(array('stamp' => $value['stampBook']));

			//date
			$varsTmpl['varsColumnDetail']['strDate'] = $arrDate['strMonth'] . '/' . $arrDate['strDate'];
			$varsTmpl['varsColumnDetail']['strDateYear'] = $arrDate['strYear'] . '/' . $arrDate['strMonth'] . '/' . $arrDate['strDate'];
			$varsTmpl['varsColumnDetail']['flagFiscalReportCut'] = $strFlagFiscalReportCut;
			$varsTmpl['varsColumnDetail']['strMemo'] = ($value['strTitle'])? $value['strTitle'] : '';

			$varsTmpl['vars']['numBalance'] = $numBalance;
			$varsTmpl['varsColumnDetail']['numBalance'] = number_format($numBalance);

			$varsTmpl['varsColumnDetail']['idLog'] = $value['idLog'];

			$varsTmpl['varsColumnDetail']['strTitle'] = $strTitle;
			$varsTmpl['varsColumnDetail']['stampRegister'] = $value['stampRegister'];
			$varsTmpl['varsColumnDetail']['stampBook'] = $value['stampBook'];
			$varsTmpl['varsColumnDetail']['idDepartment'] = $strDepartment;
			$varsTmpl['varsColumnDetail']['idSubAccountTitle'] = $strSubAccountTitle;
			$varsTmpl['varsColumnDetail']['idDepartmentContra'] = $strDepartmentContra;
			$varsTmpl['varsColumnDetail']['idAccountTitleContra'] = $strAccountTitleContra;
			$varsTmpl['varsColumnDetail']['idSubAccountTitleContra'] = $strSubAccountTitleContra;
			$varsTmpl['varsColumnDetail']['flagFiscalReport'] = $strFlagFiscalReport;
			$varsTmpl['varsScheduleDetail']['stamp'] = $value['stampBook'];
			$varsTmpl['varsScheduleDetail']['strTitle'] = $strTitle;
			$arrayColumnDetail = &$varsTmpl['varsColumnDetail'];
			foreach ($arrayColumnDetail as $keyColumnDetail => $valueColumnDetail) {
				if (is_null($valueColumnDetail)) {
					$arrayColumnDetail[$keyColumnDetail] = '';
				}
			}
			$arrayNew[$num] = $varsTmpl;
			$num++;
		}
		$vars['portal']['varsList']['varsPage']['varsStatus']['numRows'] = $rows['numRows'];

		if ($arr['flagLast'] && $varsFlag['flagFS'] == 'BS') {
			$varsTmpl = $vars['portal']['varsList']['templateDetail'];
			$varsTmpl['id'] = 'dummyNext';
			$varsTmpl['vars']['idTarget'] = $varsTmpl['id'];

			$arrDate = $classTime->getLocal(array('stamp' => $dataStamp['stampMax']));

			//date
			$varsTmpl['varsColumnDetail']['strDate'] = $arrDate['strMonth'] . '/' . $arrDate['strDate'];
			$varsTmpl['varsColumnDetail']['strDateYear'] = $arrDate['strYear'] . '/' . $arrDate['strMonth'] . '/' . $arrDate['strDate'];

			$varsTmpl['flagBtnUse'] = 0;
			$varsTmpl['flagBoldNow'] = 0;
			$varsTmpl['strClassFont']= $vars['varsItem']['strClassFont'];
			$varsTmpl['strClass'] = $vars['varsItem']['strClass'];
			$varsTmpl['strClassLoad'] = '';
			$varsTmpl['vars']['flagNext'] = 1;
			$varsTmpl['strTitle'] = $vars['varsItem']['strNextTerm'];
			$varsTmpl['varsColumnDetail']['strNextTerm'] = $vars['varsItem']['strNextTerm'];
			$varsTmpl['varsColumnDetail']['stampBook'] = $dataStamp['stampMax'];
			$varsTmpl['varsScheduleDetail']['stamp'] = $dataStamp['stampMax'];
			$varsTmpl['varsScheduleDetail']['strTitle'] = $varsTmpl['strTitle'];
			$varsTmpl['varsColumnDetail']['strTitle'] = $varsTmpl['strTitle'];

			if (($arr['numLotNow'] == 0 || is_null($arr['numLotNow'])) && !$rows['numRows']) {
				$varsTmpl['vars']['numBalance'] = $numBalancePrev;
				$varsTmpl['varsColumnDetail']['numBalance'] = number_format($numBalancePrev);

			} else {
				$value = end($rows['arrRows']);
				if ($varsFlag['idDepartment'] == 'none') {
					if ($varsFlag['idSubAccountTitle'] == 'none') {
						$numBalance = $value['numBalance'];

					} else {
						$numBalance = $value['numBalanceSubAccount'];
					}

				} else {
					if ($varsFlag['idSubAccountTitle'] == 'none') {
						$numBalance = $value['numBalanceDepartment'];

					} else {
						$numBalance = $value['numBalanceDepartmentSubAccount'];
					}
				}
				if (is_null($numBalance)) {
					$numBalance = 0;
				}
				$varsTmpl['vars']['numBalance'] = $numBalance;
				$varsTmpl['varsColumnDetail']['numBalance'] = number_format($numBalance);
			}
			$arrayNew[$num] = $varsTmpl;
			$num++;
		}

		$vars['portal']['varsList']['varsDetail'] = $arrayNew;


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
		}

		$varsAuthority = $this->_getVarsAuthority(array());
		if (!($varsAuthority == 'admin' || $varsAuthority['flagAllOutput'])) {
			$vars['portal']['varsList']['varsStart']['varsEdit']['flagOutputUse'] = 0;
			$vars['portal']['varsList']['varsStart']['varsEdit']['flagPrintUse'] = 0;
		}

		return $vars;
	}

	/**
	 *
	 */
	protected function _iniNaviSearch()
	{
		$this->_setSearch(array('flag' => 1));
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
	protected function _iniListPrint()
	{
		$this->_setClassExt(array('strClass' => 'LedgerOutput'));
	}

	/**
	 *
	 */
	protected function _iniListOutput()
	{
		$this->_setClassExt(array('strClass' => 'LedgerOutput'));
	}

}
