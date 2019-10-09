<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Base_ApplySign extends Code_Else_Core_Base_Base
{
	protected $_extSelf = array(
		'pathTplJs'        => 'else/core/base/js/applySign.js',
		'pathVarsJs'       => 'back/tpl/vars/else/core/base/<strLang>/js/applySign.php',
		'pathVarsSignUser' => 'back/tpl/vars/else/core/base/<strLang>/mail/signUser.php',
		'pathTplSignUser'  => 'back/tpl/vars/else/core/base/<strLang>/mail/signUser.tpl',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;
		global $classCheck;

		$flag = $classCheck->checkModule(array(
			'idModule' => $varsRequest['query']['module'],
			'flagType' => 'Admin'
		));
		if (!$flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__);
			}
			exit;
		}

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

		$vars = $this->getStamp();
		$json = json_encode($vars);
		$classSmarty->assign('stamp', $json);

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$rows = $this->getSearch(array('numLotNow' => 0));
		$vars = $this->_updateSearch(array(
			'vars' => $vars,
			'rows' => $rows,
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
	 * array(
	 *  'numLotNow' => int
	 * )
	 */
	public function getSearch($arr)
	{
		global $varsAccount;
		global $classDb;

		$numStart = $arr['numLotNow'] * $varsAccount['numList'];
		$numEnd = $numStart + $varsAccount['numList'];

		$arrOrder = array(
			'strColumn' => 'id',
			'flagDesc'  => 1,
		);
		$arrWhere = array();

		$rows = $classDb->getSelect(array(
			'idModule' => 'base',
			'strTable' => 'baseApplySign',
			'arrLimit' => array(
				'numStart' => $numStart, 'numEnd' => $numEnd,
			),
			'arrOrder' => $arrOrder,
			'arrWhere' => $arrWhere,
		));

		return $rows;
	}

	/**
	 *
	 */
	protected function _updateSearch($arr)
	{
		global $varsRequest;
		global $varsAccount;
		global $varsAccounts;

		$vars = $arr['vars'];
		$rows = $arr['rows'];

		$array = &$rows['arrRows'];
		$arrayNew = array();
		$num = 0;
		foreach ($array as $key => $value) {

			$varsTmpl = $vars['portal']['varsNavi']['tree']['templateDetail'];
			$varsTmpl['id'] = $value['id'];
			$varsTmpl['strTitle'] = $value['strCodeName'];

			if ($varsAccount['jsonStampCheck']['baseApplySign'] < $value['stampRegister']) {
				$flag = 1;
				$varsTmpl['strClass'] = $varsTmpl['strClassLoad'];
			}
			unset($varsTmpl['strClassLoad']);
			$id = $value['idAccount'];

			$varsTmpl['vars']['idTarget'] = $value['id'];
			$varsTmpl['vars']['strTitle'] = $value['strCodeName'];
			$varsTmpl['vars']['stampRegister'] = $value['stampRegister'];

			$varsTmpl['vars']['strCodeName'] = $value['strCodeName'];
			$varsTmpl['vars']['idLogin'] = $value['idLogin'];
			$varsTmpl['vars']['strMailPc'] = $value['strMailPc'];

			$arrayNew[$num] = $varsTmpl;
			$num++;
		}

		$vars['portal']['varsNavi']['tree']['varsDetail']['varsPage']['varsStatus']['numRows'] = $rows['numRows'];
		$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $arrayNew;

		if ($flag) {
			$this->_setDbStampCheck(array(
				'strColumnAccount'    => 'baseApplySign',
				'strColumnPreference' => 'accounts',
			));
		}

		return $vars;
	}

	/**
	 */
	protected function _iniNaviSearch()
	{
		$this->_setNaviSearch(array('flag' => 1));
	}

	/**
	 *
	 */
	protected function _iniNaviReload()
	{
		$this->_setNaviSearch(array('flag' => 1));
	}

	/**
	 * array(
	 *  idTarget => int
	 * )
	 */
	protected function _checkIdTarget($arr)
	{
		global $classDb;
		global $classCheck;

		$idTarget = $arr['idTarget'];

		$flag = $classCheck->checkValueWord(array(
			'flagType' => 'num',
			'value'    => $idTarget,
		));

		if ($flag || !$arr['idTarget']) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__);
			}
			exit;
		}

		$rows = $classDb->getSelect(array(
			'idModule' => 'base',
			'strTable' => 'baseApplySign',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'arrWhere' => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'id',
					'flagCondition' => 'eq',
					'value'         => $idTarget,
				),
			),
		));

		if (!$rows['numRows']) {
			if ($arr['flagReload']) {
				$this->_setNaviSearch(array('flag' => 'lost'));

			} else {
				$this->sendVars(array(
					'flag'    => 40,
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));
			}

		}

		return $rows;
	}
	/**
	 * array(
	 *  flag => int
	 * )
	 */
	protected function _setNaviSearch($arr)
	{
		global $varsPreference;
		global $varsRequest;
		global $classCheck;

		$numLotNow = $varsRequest['query']['jsonSearch']['numLotNow'];
		$flag = $classCheck->checkValueWord(array(
			'flagType' => 'num',
			'value'    => $numLotNow,
		));

		if ($flag && $numLotNow != '') {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__);
			}
			exit;
		}

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPreference['jsonStampUpdate']['applySign'],
				'flagSearch'  => 1,
			));
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$rows = $this->getSearch(array('numLotNow' => $numLotNow));
		if (!count($rows['arrRows'])) {
			$numLotNow = 0;
			$rows = $this->getSearch(array('numLotNow' => $numLotNow));
		}
		$vars = $this->_updateSearch(array(
			'vars' => $vars,
			'rows' => $rows,
		));

		$data = array(
			'numLotNow'  => $numLotNow,
			'numRows'    => $rows['numRows'],
			'varsDetail' => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
		);

		$this->sendVars(array(
			'flag'    => $arr['flag'],
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $data,
		));

	}

	/**
	 *
	 */
	protected function _iniDetailReload()
	{
		global $classDb;
		global $classCheck;

		global $varsPreference;
		global $varsRequest;

		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];
		$rows = $this->_checkIdTarget(array(
			'flagReload' => 1,
			'idTarget'   => $idTarget,
		));

		if ($varsRequest['query']['jsonSearch']['flagReload'] && $varsRequest['query']['jsonStamp']['stamp']) {
			if ($varsPreference['jsonStampUpdate']['applySign'] <= $varsRequest['query']['jsonStamp']['stamp']) {
				$this->sendVars(array(
					'flag'    => 10,
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(
						'vars'=> array(
							'idTarget' => $idTarget,
						),
					),
				));
			}
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));
		$vars = $this->_updateSearch(array(
			'vars' => $vars,
			'rows' => $rows,
		));

		$this->sendVars(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'][0],
		));

	}


	/**
	 *
	 */
	protected function _iniDetailEdit()
	{
		global $classInit;
		global $classDisplay;
		global $classDb;
		global $classEscape;
		global $classRebuild;
		global $classMail;

		global $varsAccounts;
		global $varsRequest;
		global $varsPreference;
		$dbh = $classDb->getHandle();
		global $varsTerm;
		global $varsModule;

		$temp = $varsRequest['query']['jsonValue']['vars']['StrMailPc'];
		$varsRequest['query']['jsonValue']['vars']['StrMailPc'] = strtolower($temp);

		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];
		$rows = $this->_checkIdTarget(array(
			'idTarget' => $idTarget,
		));

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsDetail = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsDetail
		));


		$array = &$varsAccounts;
		foreach ($array as $key => $value) {
			$str = '';
			if ($value['strCodeName'] == $arrValue['arr']['strCodeName']){
				$str = 'strCodeName';
				$this->sendValue(array(
					'flag'    => $str,
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(
						'vars'=> array(
							'idTarget' => $idTarget,
						),
					),
				));

			}
			if ($value['strMailPc'] == $arrValue['arr']['strMailPc']
				|| $varsPreference['strSiteMailPc'] == $arrValue['arr']['strMailPc']
			){
				$str = 'strMailPc';
				$this->sendValue(array(
					'flag'    => $str,
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(
						'vars'=> array(
							'idTarget' => $idTarget,
						),
					),
				));

			}
			if ($value['idLogin'] == $arrValue['arr']['idLogin']){
				$str = 'idLogin';
				$this->sendValue(array(
					'flag'    => $str,
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(
						'vars'=> array(
							'idTarget' => $idTarget,
						),
					),
				));
			}

		}

		$rows = $classDb->getSelect(array(
			'idModule' => 'base',
			'strTable' => 'baseAccountId',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'arrWhere' => array(
				array(
					'flagType'      => '',
					'strColumn'     => 'strCodeName',
					'flagCondition' => 'eq',
					'value'         => $arrValue['arr']['strCodeName'],
				),
			),
		));

		if ($rows['numRows']) {
			$this->sendValue(array(
				'flag'    => 'strCodeNamePast',
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(
					'vars'=> array(
						'idTarget' => $idTarget,
					),
				),
			));
		}

		$rows = $classDb->getSelect(array(
			'idModule' => 'base',
			'strTable' => 'baseLoginIdLogin',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'arrWhere' => array(
				array(
					'flagType'      => '',
					'strColumn'     => 'idLogin',
					'flagCondition' => 'eq',
					'value'         => $arrValue['arr']['idLogin'],
				),
			),
		));

		if ($rows['numRows']) {
			$this->sendValue(array(
				'flag'    => 'idLoginPast',
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(
					'vars'=> array(
						'idTarget' => $idTarget,
					),
				),
			));
		}

		//idterm exist
		if (!$varsTerm[$arrValue['arr']['idTerm']]) {
			$this->sendVars(array(
				'flag'    => 40,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}
		if (!$varsModule[$arrValue['arr']['idModule']]) {
			$this->sendVars(array(
				'flag'    => 40,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}

		$tm = TIMESTAMP;
		$stampRegister = $tm;
		$stampUpdate = $tm;
		$strCodeName = $arrValue['arr']['strCodeName'];
		$idLogin = $arrValue['arr']['idLogin'];
		$strPassword = $classDisplay->getPassword(array(
			'numMark'  => 1,
			'numNum'   => 1,
			'numBig'   => 1,
			'numSmall' => abs($varsPreference['numPassword '] - 3),
		));
		$strPassword = hash('sha256', $strPassword);
		$stampUpdatePassword = $tm;
		$strMailPc = $arrValue['arr']['strMailPc'];
		$numTimeZone = NUM_SYSTEM_TIME_ZONE;
		$strLang = STR_SYSTEM_LANG;
		$strHoliday = STR_SYSTEM_HOLIDAY;
		$idTerm = $arrValue['arr']['idTerm'];
		$idModule = $arrValue['arr']['idModule'];
		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arrValue['arr']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));

		$str = MICROTIMESTAMP . $classDisplay->getPassword(array(
			'numMark'  => 5,
			'numNum'   => 5,
			'numBig'   => 5,
			'numSmall' => 5,
		));
		$session = hash('sha256', $str);
        $pathConfirm = $varsPreference['strTopUrl'] . 'confirm.php?type=publish&id=' . $session;
		$numLimit = round(NUM_SESSION /60/60/24*7);
		$strName = $varsPreference['strSiteName'];
		$strUrl = $varsPreference['strSiteUrl'];

		try {
			$dbh->beginTransaction();

			//account
			$classDb->insertRow(array(
				'idModule'  => 'base',
				'strTable'  => 'baseAccount',
				'arrColumn' => array('stampRegister', 'stampUpdate', 'strCodeName', 'idLogin', 'strPassword', 'stampUpdatePassword', 'strMailPc', 'numTimeZone', 'strLang', 'strHoliday', 'idTerm', 'idModule', 'arrSpaceStrTag'),
				'arrValue'  => array($stampRegister, $stampUpdate, $strCodeName, $idLogin, $strPassword, $stampUpdatePassword, $strMailPc, $numTimeZone, $strLang, $strHoliday, $idTerm, $idModule, $arrSpaceStrTag),
			));

			$rows = $classDb->getSelect(array(
				'idModule' => 'base',
				'strTable' => 'baseAccount',
				'arrLimit' => array(),
				'arrOrder' => array(),
				'arrWhere' => array(
					array(
						'flagType'      => '',
						'strColumn'     => 'idLogin',
						'flagCondition' => 'eq',
						'value'         => $idLogin,
					),
				),
			));

			$varsAccount = $rows['arrRows'][0];
			$idAccount = $varsAccount['id'];

			//memo
			$classDb->insertRow(array(
				'idModule'  => 'base',
				'strTable'  => 'baseAccountMemo',
				'arrColumn' => array('stampRegister', 'stampUpdate', 'idAccount'),
				'arrValue'  => array($stampRegister, $stampUpdate, $idAccount),
			));

			$classDb->insertRow(array(
				'idModule'  => 'base',
				'strTable'  => 'baseLoginIdLogin',
				'arrColumn' => array('stampRegister', 'idLogin'),
				'arrValue'  => array($stampRegister, $idLogin),
			));

			$array = array('DbInsertAccount');
			foreach ($array as $key => $value) {
				$classRebuild->run(array(
					'flagType'    => $value,
					'varsAccount' => $varsAccount,
					'arrIdModule' => $varsPreference['jsonModule'],
				));
			}

			$classDb->insertRow(array(
				'idModule'  => 'base',
				'strTable'  => 'basePublish',
				'arrColumn' => array('stampRegister', 'session', 'idAccount'),
				'arrValue'  => array($stampRegister, $session, $idAccount),
			));

			$this->updateDbPreferenceStamp(array(
				'strColumn' => 'account',
			));

			$classDb->deleteRow(array(
				'idModule'  => 'base',
				'strTable'  => 'baseApplySign',
				'arrWhere'  => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'id',
						'flagCondition' => 'eq',
						'value'         => $idTarget,
					),
				),
			));

			$this->updateDbPreferenceStamp(array('strColumn' => 'applySign',));
			$this->updateDbPreferenceStamp(array('strColumn' => 'account',));



			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$classInit->updateVarsAll(array(
			'vars'     => &$varsAccounts,
			'strVars'  => 'varsAccounts',
			'strTable' => 'baseAccount',
		));

		$flag = $classMail->setMail(array(
			'pathVars'    => $this->_extSelf['pathVarsSignUser'],
			'pathTpl'     => $this->_extSelf['pathTplSignUser'],
			'arrValue'    => array(
				'numLimit' => $numLimit,
				'strName'  => $strName,
				'session'  => $pathConfirm,
				'strUrl'   => $strUrl,
			),
			'mailTo'      => $strMailPc,
			'arrMailBcc'  => array(),
			'arrMailCc'   => array(),
			'mailFrom'    => $varsPreference['strSiteMailPc'],
			'strNameFrom' => $varsPreference['strSiteName'],
		));

		if (!$flag) {
			$this->_setNaviSearch(array('flag' => 42));

		} else {
			$this->_setNaviSearch(array('flag' => 1));

		}


	}


	/**
	 *
	 */
	protected function _iniDetailDelete()
	{
		global $varsAccounts;
		global $varsRequest;

		global $classInit;
		global $classDb;
		$dbh = $classDb->getHandle();

		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];
		$rows = $this->_checkIdTarget(array(
			'idTarget' => $idTarget,
		));

		try {
			$dbh->beginTransaction();

			$classDb->deleteRow(array(
				'idModule'  => 'base',
				'strTable'  => 'baseApplySign',
				'arrWhere'  => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'id',
						'flagCondition' => 'eq',
						'value'         => $idTarget,
					),
				),
			));

			$this->updateDbPreferenceStamp(array('strColumn' => 'applySign',));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}
		$this->_setNaviSearch(array('flag' => 1));

	}

}
