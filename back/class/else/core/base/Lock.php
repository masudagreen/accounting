<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Base_Lock extends Code_Else_Core_Base_Base
{
	protected $_extSelf = array(
		'pathTplJs' => 'else/core/base/js/lock.js',
		'pathVarsJs'  => 'back/tpl/vars/else/core/base/<strLang>/js/lock.php',
		'pathVarsUser'  => 'back/tpl/vars/else/core/base/<strLang>/mail/lockUser.php',
		'pathTplUser'  => 'back/tpl/vars/else/core/base/<strLang>/mail/lockUser.tpl',
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

		$rows = $classDb->getSelect(array(
			'idModule' => 'base',
			'strTable' => 'baseLock',
			'arrLimit' => array(
				'numStart' => $numStart, 'numEnd' => $numEnd,
			),
			'arrOrder' => $arrOrder,
			'arrWhere' => array(),
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
			$id = $value['idAccount'];

			$varsTmpl['id'] = $value['id'];
			$varsTmpl['strTitle'] = $varsAccounts[$id]['strCodeName'];

			if ($varsAccount['jsonStampCheck']['baseLock'] < $value['stampRegister']) {
				$flag = 1;
				$varsTmpl['strClass'] = $varsTmpl['strClassLoad'];
			}
			unset($varsTmpl['strClassLoad']);

			$varsTmpl['vars']['idTarget'] = $value['id'];
			$varsTmpl['vars']['stampRegister'] = $value['stampRegister'];
			$varsTmpl['vars']['strCodeName'] = $varsAccounts[$id]['strCodeName'];
			$varsTmpl['vars']['idLogin'] = $varsAccounts[$id]['idLogin'];

			$arrayNew[$num] = $varsTmpl;
			$num++;
		}

		$vars['portal']['varsNavi']['tree']['varsDetail']['varsPage']['varsStatus']['numRows'] = $rows['numRows'];
		$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $arrayNew;

		if ($flag) {
			$this->_setDbStampCheck(array(
				'strColumnAccount'    => 'baseLock',
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
				'stampTarget' => $varsPreference['jsonStampUpdate']['lock'],
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
			if ($varsPreference['jsonStampUpdate']['lock'] <= $varsRequest['query']['jsonStamp']['stamp']) {
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
			'strTable' => 'baseLock',
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
	 *
	 */
	protected function _iniDetailEdit()
	{
		global $varsAccounts;
		global $varsTerm;
		global $varsRequest;
		global $varsPreference;

		global $classMail;
		global $classDisplay;
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

		$array = &$varsAccounts;
		$idAccount = $rows['arrRows'][0]['idAccount'];
		$varsAccount = $varsAccounts[$idAccount];

		foreach ($array as $key => $value) {
			$str = '';
			if ($value['idLogin'] == $arrValue['arr']['idLogin']){
				$str = 'idLogin';
			}
			if ($str) {
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

		$tm = TIMESTAMP;
		$flag = 0;
		if ($varsTerm  && !$varsAccount['flagWebmaster']) {
			$idTerm = $varsAccount['idTerm'];
			if (($varsTerm[$idTerm]['stampStart'] > 0 && !$varsTerm[$idTerm]['stampEnd'])
				&& $varsTerm[$idTerm]['stampStart'] > $tm
			) {
				$flag = 1;

			} elseif (($varsTerm[$idTerm]['stampStart'] > 0 && $varsTerm[$idTerm]['stampEnd'] > 0)
				&& $varsTerm[$idTerm]['stampStart'] > $tm && $tm > $varsTerm[$idTerm]['stampEnd']
			) {
				$flag = 1;

			}
		}
		if ($rows['numRows']) {
			$this->sendValue(array(
				'flag'    => 'term',
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(
					'vars'=> array(
						'idTarget' => $idTarget,
					),
				),
			));
		}

		$tm = TIMESTAMP;
		$str = $classDisplay->getPassword(array(
			'numMark'  => 5,
			'numNum'   => 5,
			'numBig'   => 5,
			'numSmall' => 5,
		));
		$session = hash('sha256', $str);
        $pathConfirm = $varsPreference['strTopUrl'] . 'confirm.php?type=publish&id=' . $session;

		$numLimit = round(NUM_SESSION * 7 /60/60/24);
		try {
			$dbh->beginTransaction();

			$classDb->insertRow(array(
				'idModule'  => 'base',
				'strTable'  => 'baseLoginIdLogin',
				'arrColumn' => array('stampRegister', 'idLogin'),
				'arrValue'  => array($tm, $varsAccounts[$idAccount]['idLogin']),
			));

			$classDb->updateRow(array(
				'idModule'  => 'base',
				'strTable'  => 'baseAccount',
				'arrColumn' => array('flagLock', 'idLogin'),
				'arrWhere'  => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'id',
						'flagCondition' => 'eq',
						'value'         => $idAccount,
					),
				),
				'arrValue'  => array(0, $arrValue['arr']['idLogin']),
			));

			$classDb->insertRow(array(
				'idModule'  => 'base',
				'strTable'  => 'basePublish',
				'arrColumn' => array('stampRegister', 'session', 'idAccount'),
				'arrValue'  => array($tm, $session, $idAccount),
			));

			$classDb->deleteRow(array(
				'idModule'  => 'base',
				'strTable'  => 'baseLock',
				'arrWhere'  => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idAccount',
						'flagCondition' => 'eq',
						'value'         => $idAccount,
					),
				),
			));

			$this->updateDbPreferenceStamp(array(
				'strColumn' => 'account',
			));
			$this->updateDbPreferenceStamp(array(
				'strColumn' => 'lock',
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

		$flag = $classMail->setMail(array(
			'pathVars'    => $this->_extSelf['pathVarsUser'],
			'pathTpl'     => $this->_extSelf['pathTplUser'],
			'arrValue'    => array(
				'strName'  => $varsPreference['strSiteName'],
				'strUrl'   => $varsPreference['strSiteUrl'],
				'session'  => $pathConfirm,
				'numLimit' => $numLimit,
			),
			'mailTo'      => $varsAccounts[$idAccount]['strMailPc'],
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
