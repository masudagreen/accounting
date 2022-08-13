<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Base_ApplyChange extends Code_Else_Core_Base_Base
{
	protected $_extSelf = array(
		'pathTplJs'            => 'else/core/base/js/applyChange.js',
		'pathVarsJs'           => 'back/tpl/vars/else/core/base/<strLang>/js/applyChange.php',
		'pathVarsChangeOkUser' => 'back/tpl/vars/else/core/base/<strLang>/mail/changeOkUser.php',
		'pathTplChangeOkUser'  => 'back/tpl/vars/else/core/base/<strLang>/mail/changeOkUser.tpl',
		'pathVarsChangeNgUser' => 'back/tpl/vars/else/core/base/<strLang>/mail/changeNgUser.php',
		'pathTplChangeNgUser'  => 'back/tpl/vars/else/core/base/<strLang>/mail/changeNgUser.tpl',
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
			'strTable' => 'baseApplyChange',
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

		foreach ($array as $key => $value) {

			$varsTmpl = $vars['portal']['varsNavi']['tree']['templateDetail'];
			$varsTmpl['id'] = $value['id'];
			$varsTmpl['strTitle'] = $value['strCodeName'];

			if ($varsAccount['jsonStampCheck']['baseApplyChange'] < $value['stampRegister']) {
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

			$varsTmpl['vars']['pastStrCodeName'] = $varsAccounts[$id]['strCodeName'];
			$varsTmpl['vars']['pastIdLogin'] = $varsAccounts[$id]['idLogin'];
			$varsTmpl['vars']['pastStrMailPc'] = $varsAccounts[$id]['strMailPc'];

			$arrayNew[] = $varsTmpl;

		}

		$vars['portal']['varsNavi']['tree']['varsDetail']['varsPage']['varsStatus']['numRows'] = $rows['numRows'];
		$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $arrayNew;

		if ($flag) {
			$this->_setDbStampCheck(array(
				'strColumnAccount'    => 'baseApplyChange',
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
			'strTable' => 'baseApplyChange',
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
			$this->_setNaviSearch(array('flag' => 40));
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
				'stampTarget' => $varsPreference['jsonStampUpdate']['applyChange'],
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
			'idTarget' => $idTarget,
		));
		if ($varsRequest['query']['jsonSearch']['flagReload'] && $varsRequest['query']['jsonStamp']['stamp']) {
			if ($varsPreference['jsonStampUpdate']['applyChange'] <= $varsRequest['query']['jsonStamp']['stamp']) {
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
		global $varsAccounts;
		global $varsRequest;
		global $varsPreference;

		global $classInit;
		global $classDb;
		$dbh = $classDb->getHandle();

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
		$idAccount = $rows['arrRows'][0]['idAccount'];
		foreach ($array as $key => $value) {
			if ($idAccount == $value['id']) {
				continue;
			}
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

		try {
			$dbh->beginTransaction();

			if ($arrValue['arr']['idLogin'] != $varsAccounts[$idAccount]['idLogin']) {
				$classDb->insertRow(array(
					'idModule'  => 'base',
					'strTable'  => 'baseLoginIdLogin',
					'arrColumn' => array('stampRegister', 'idLogin'),
					'arrValue'  => array(TIMESTAMP, $varsAccounts[$idAccount]['idLogin']),
				));
			}
			if ($arrValue['arr']['strCodeName'] != $varsAccounts[$idAccount]['strCodeName']) {
				$classDb->insertRow(array(
					'idModule'  => 'base',
					'strTable'  => 'baseAccountId',
					'arrColumn' => array('id', 'strCodeName'),
					'arrValue'  => array($idAccount, $varsAccounts[$idAccount]['strCodeName']),
				));
			}
			$classDb->updateRow(array(
				'idModule'  => 'base',
				'strTable'  => 'baseAccount',
				'arrColumn' => array('strCodeName', 'strMailPc', 'idLogin'),
				'arrWhere'  => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'id',
						'flagCondition' => 'eq',
						'value'         => $idAccount,
					),
				),
				'arrValue'  => array($arrValue['arr']['strCodeName'], $arrValue['arr']['strMailPc'], $arrValue['arr']['idLogin']),
			));

			$classDb->deleteRow(array(
				'idModule'  => 'base',
				'strTable'  => 'baseApplyChange',
				'arrWhere'  => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'id',
						'flagCondition' => 'eq',
						'value'         => $idTarget,
					),
				),
			));

			$this->updateDbPreferenceStamp(array(
				'strColumn' => 'account',
			));

			$this->updateDbPreferenceStamp(array(
				'strColumn' => 'applyChange',
			));

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

		$this->_sendDetailMail(array(
			'strMessage' => $arrValue['arr']['strMessage'],
			'mailTo'     => $arrValue['arr']['strMailPc'],
			'pathVars'   => $this->_extSelf['pathVarsChangeOkUser'],
			'pathTpl'    => $this->_extSelf['pathTplChangeOkUser'],
		));

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

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));
		$varsDetail = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsDetail
		));
		$idAccount = $rows['arrRows'][0]['idAccount'];

		try {
			$dbh->beginTransaction();

			$classDb->deleteRow(array(
				'idModule'  => 'base',
				'strTable'  => 'baseApplyChange',
				'arrWhere'  => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'id',
						'flagCondition' => 'eq',
						'value'         => $idTarget,
					),
				),
			));

			$this->updateDbPreferenceStamp(array(
				'strColumn' => 'applyChange',
			));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$this->_sendDetailMail(array(
			'strMessage' => $arrValue['arr']['strMessage'],
			'mailTo'     => $varsAccounts[$idAccount]['strMailPc'],
			'pathVars'   => $this->_extSelf['pathVarsChangeNgUser'],
			'pathTpl'    => $this->_extSelf['pathTplChangeNgUser'],
		));

	}


	/**
	 * array(
	 *  'strMessage' => string,
	 *  'mailTo'     => string,
	 *  'pathVars'   => string,
	 *  'pathTpl'    => string,
	 * )
	 */
	protected function _sendDetailMail($arr)
	{
		global $varsAccounts;
		global $varsPreference;
		global $varsRequest;

		global $classMail;

		$flag = $classMail->setMail(array(
			'pathVars'    => $arr['pathVars'],
			'pathTpl'     => $arr['pathTpl'],
			'arrValue'    => array(
				'strName'    => $varsPreference['strSiteName'],
				'strUrl'     => $varsPreference['strSiteUrl'],
				'strMessage' => ($arr['strMessage'])? $arr['strMessage'] : '',
			),
			'mailTo'      => $arr['mailTo'],
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

}
