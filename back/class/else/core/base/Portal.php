<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Base_Portal extends Code_Else_Core_Base_Base
{
	protected $_extSelf = array(
		'flagUser'           => 0,
		'pathHoliday'        => 'back/tpl/vars/else/core/base/<strLang>/js/lib/holiday/',
		'pathLangDat'        => 'back/dat/lang/<strLang>/list.csv',
		'pathLangDir'        => 'back/tpl/vars/else/core/base/',
		'pathCacheDat'       => 'back/tpl/templates_c/',
		'pathTplJs'          => 'else/core/base/js/portal.js',
		'pathVarsJs'         => 'back/tpl/vars/else/core/base/<strLang>/js/portal.php',
		'pathVarsChangeUser' => 'back/tpl/vars/else/core/base/<strLang>/mail/changeUser.php',
		'pathTplChangeUser'  => 'back/tpl/vars/else/core/base/<strLang>/mail/changeUser.tpl',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;

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

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));
		$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_checkVars(
			$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail']
		);
		$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_updateVars(
			$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail']
		);

		$vars['token'] = $this->setToken();

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
	protected function _iniNaviReload()
	{
		global $varsPreference;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPreference['stampUpdate'],
				'flagSearch'  => 0,
			));
		}

		$vars = $this->_getNaviReload();

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
	protected function _getNaviReload()
	{
		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));
		$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_checkVars(
			$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail']
		);
		$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_updateVars(
			$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail']
		);

		return $vars;
	}

	/**
	 *
	 */
	protected function _iniDetailEdit()
	{
		global $classInit;
		global $classDb;
		global $varsRequest;
		$dbh = $classDb->getHandle();

		if ($varsRequest['query']['jsonValue']['idTarget'] == 'strSiteName') {
			$temp = $varsRequest['query']['jsonValue']['vars']['StrSiteMailPc'];
			$varsRequest['query']['jsonValue']['vars']['StrSiteMailPc'] = strtolower($temp);

		} elseif ($varsRequest['query']['jsonValue']['idTarget'] == 'strCodeName') {
			$temp = $varsRequest['query']['jsonValue']['vars']['StrMailPc'];
			$varsRequest['query']['jsonValue']['vars']['StrMailPc'] = strtolower($temp);
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_checkVars(
			$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail']
		);

		$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_updateVars(
			$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail']
		);

		$varsTarget = $this->getVarsTarget(array(
			'vars' => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail']
		));

		if (!$varsTarget) {
			$this->sendVars(array(
				'flag'    => 40,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => '',
			));
			exit;
		}

		$varsTarget['vars']['varsDetail'] = $this->getValue(array(
			'vars' => $varsTarget['vars']['varsDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget['vars']['varsDetail']
		));
		$arrValue['varsTarget'] = $varsTarget;


		if ($varsTarget['vars']['idTarget'] == 'rebuild') {
			$this->_updateDb($arrValue);

		} else {
			try {
				$dbh->beginTransaction();

				$flag = $this->_updateDb($arrValue);

				$dbh->commit();
			} catch (PDOException $e) {
				$dbh->rollBack();
				if (FLAG_TEST) {
					var_dump($e->getMessage());
				}
				exit;
			}
		}
if (FLAG_TEST) {
var_dump('done');
exit;
}

		$classInit->updateVarsAll(array(
			'vars'     => &$varsAccounts,
			'strVars'  => 'varsAccounts',
			'strTable' => 'baseAccount',
		));
		$classInit->updateVarsPreference();

		if ($varsTarget['vars']['idTarget'] == 'numAutoMustLogout'
			|| $varsTarget['vars']['idTarget'] == 'numPasswordLimit'
		) {
			$vars = $this->_getNaviReload();
			$this->sendValue(array(
				'flag'    => 1,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(
					'vars'     => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'],
					'idTarget' => $varsTarget['vars']['idTarget'],
				),
			));
		}

		$method = '_updateVars' . ucwords($varsTarget['vars']['idTarget']);
		if (method_exists($this, $method)) {
			$vars = $this->getVars(array(
				'path' => $this->_extSelf['pathVarsJs'],
			));
			$varsTarget = $this->getVarsTarget(array(
				'vars' => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail']
			));
			if ($varsTarget['vars']['idTarget'] == 'version' && !is_null($arrValue['arr']['dummyChange'])) {
				//バージョン更新
				$varsTarget['vars']['flagVersion'] = 'dll';
			}
			if ($varsTarget['vars']['idTarget'] == 'version') {

				//vars.FlagVersionUpdate = 1; version info on
				//vars.FlagVersionUpdate = 2; version info off

				//バージョン更新有効指示があったら
				$flag = (int) $varsRequest['query']['jsonValue']['vars']['FlagVersionUpdate'];
				if ($flag > 0) {
					$varsTarget['vars']['flagVersion'] = 'flagVersionUpdate';
				}
			}
			$varsTarget = $this->$method($varsTarget);
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
	protected function _iniDetailReload()
	{
		global $varsPreference;
		global $varsRequest;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPreference['stampUpdate'],
				'flagSearch'  => 0,
			));
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail'] = $this->_checkVars(
			$vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail']
		);
		$varsTarget = $this->getVarsTarget(array(
			'vars' => $vars['portal']['varsNavi']['tree']['varsDetail']['varsDetail']
		));
		if (!$varsTarget) {
			$this->sendVars(array(
				'flag'    => 40,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => '',
			));
			exit;
		}

		$method = '_updateVars' . ucwords($varsTarget['vars']['idTarget']);
		if (method_exists($this, $method)) {
			$varsTarget = $this->$method($varsTarget);
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
	protected function _checkVars($vars)
	{
		$vars = $this->_checkVarsAdmin($vars);
		$vars = $this->_checkVarsNumAutoMustLogout($vars);
		$vars = $this->_checkVarsFlagLoginSecond($vars);

		return $vars;
	}

	/**
	 *
	 */
	protected function _checkVarsNumAutoMustLogout($vars)
	{
		global $varsPreference;

		$varsNew = array();
		$num = 0;
		foreach ($vars as $key => $value) {
			$idTarget = $vars[$key]['vars']['idTarget'];
			if ($idTarget == 'numAutoLogout') {
				if ($varsPreference['numAutoMustLogout'] == 0) {
					$varsNew[$num] = $vars[$key];
					if ($vars[$key]['child']) {
						$varsNew[$num]['child'] = $this->_checkVarsNumAutoMustLogout($vars[$key]['child']);
					}
					$num++;
				}
			} else {
				$varsNew[$num] = $vars[$key];
				if ($vars[$key]['child']) {
					$varsNew[$num]['child'] = $this->_checkVarsNumAutoMustLogout($vars[$key]['child']);
				}
				$num++;
			}

		}

		return $varsNew;
	}

	/**
	 *
	 */
	protected function _checkVarsFlagLoginSecond($vars)
	{
		global $varsPreference;

		$varsNew = array();
		$num = 0;
		foreach ($vars as $key => $value) {
			$idTarget = $vars[$key]['vars']['idTarget'];
			if ($idTarget == 'flagLoginSecondAccount') {
				if (!$varsPreference['flagLoginSecond']) {
					$varsNew[$num] = $vars[$key];
					if ($vars[$key]['child']) {
						$varsNew[$num]['child'] = $this->_checkVarsFlagLoginSecond($vars[$key]['child']);
					}
					$num++;
				}
			} else {
				$varsNew[$num] = $vars[$key];
				if ($vars[$key]['child']) {
					$varsNew[$num]['child'] = $this->_checkVarsFlagLoginSecond($vars[$key]['child']);
				}
				$num++;
			}

		}

		return $varsNew;
	}

	/**
	 *
	 */
	protected function _checkVarsAdmin($vars)
	{
		global $varsRequest;
		global $classCheck;

		$_extSelf['flagUser'] = $classCheck->checkModule(array(
			'idModule' => $varsRequest['query']['module'],
			'flagType' => 'Admin'
		));

		if (!$_extSelf['flagUser']) {
			$array = $vars;
			$arrayNew = array();
			$num = 0;
			foreach ($array as $key => $value) {
				if ($array[$key]['id'] == 'Admin') {
					continue;
				}
				$arrayNew[$num] = $array[$key];
				$num++;
			}
			$vars = $arrayNew;
		}

		return $vars;
	}

	/**
	 *
	 */
	protected function _updateVars($vars)
	{
		foreach ($vars as $key => $value) {
			$idTarget = $vars[$key]['vars']['idTarget'];
			if (!preg_match( "/Window$/", $idTarget) && !$vars[$key]['flagFoldUse']) {
				$method = '_updateVars' . ucwords($idTarget);
				if (method_exists($this, $method)) {
					$vars[$key] = $this->$method($vars[$key]);
				}
			}
			if ($vars[$key]['child']) {
				$vars[$key]['child'] = $this->_updateVars($vars[$key]['child']);
			}
		}

		return $vars;
	}


	/**
	 *
	 */
	protected function _updateVarsPreference($vars)
	{
		global $varsPreference;
		global $classEscape;

		$array = &$vars['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			$str = $classEscape->toLower(array('str' => $array[$key]['id']));
			$array[$key]['value'] = (is_null($varsPreference[$str]))? '' : $varsPreference[$str];
		}

		return $vars;
	}


	/**
	 *
	 */
	protected function _updateVarsAccount($vars)
	{
		global $varsAccount;
		global $classEscape;

		$array = &$vars['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			$str = $classEscape->toLower(array('str' => $array[$key]['id']));
			$array[$key]['value'] = (is_null($varsAccount[$str]))? '' : $varsAccount[$str];
		}

		return $vars;
	}


	/**
	 *
	 */
	protected function _updateVarsStrSiteName($vars)
	{
		return $this->_updateVarsPreference($vars);
	}

	/**
	 *
	 */
	protected function _updateVarsFlagReject($vars)
	{
		return $this->_updateVarsPreference($vars);
	}

	/**
	 *
	 */
	protected function _updateVarsFlagAccessUnknownMail($vars)
	{
		return $this->_updateVarsPreference($vars);
	}

	/**
	 *
	 */
	protected function _updateVarsFlagMaintenance($vars)
	{
		return $this->_updateVarsPreference($vars);
	}

	/**
	 *
	 */
	protected function _updateVarsArrCommaIdAccountMaintenance($vars)
	{
		global $classEscape;
		global $varsAccounts;
		global $varsPreference;

		$array = &$vars['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			if ($array[$key]['id'] == 'ArrCommaIdAccountMaintenance') {
				$str = $classEscape->toLower(array('str' => $array[$key]['id']));
				$arrId = $classEscape->splitCommaArrayData(array('data' => $varsPreference[$str]));

				$num = 0;
				$arrayNew = array();
				foreach ($arrId as $keyId => $valueId) {
					$varsTmpl = $array[$key]['varsFormArea']['templateDetail'];
					$varsTmpl['strTitle'] = $varsAccounts[$valueId]['strCodeName'];
					$varsTmpl['vars']['idTarget'] = $varsAccounts[$valueId]['id'];
					$arrayNew[$num] = $varsTmpl;
					$num++;
				}
				$array[$key]['varsFormArea']['varsDetail'] = $arrayNew;
				break;
			}
		}

		return $vars;
	}

	/**
	 *
	 */
	protected function _updateVarsRebuild($vars)
	{
		global $varsPreference;

		$flag = 0;
		$array = scandir(PATH_BACK_CLASS_ELSE_PLUGIN);
		$arrayNew = array();
		foreach ($array as $key => $value) {
			if ( preg_match( "/^\.{1,2}$/", $value)) {
				continue;

			}
			if ($varsPreference['jsonModule'][$value]) {
				continue;
			}
			$flag = 1;
			$varsTmpl = $vars['vars']['templateDetail'];
			$varsTmpl['id'] = ucwords($value);
			$varsTmpl['strComment'] = str_replace('<%replace%>', ucwords($value), $varsTmpl['strComment']);
			$arrayNew[] = $varsTmpl;

		}

		if (!$flag) {
			$vars['vars']['varsBtn'] = array();
		}

		$array = $vars['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'DummyNg' && !$flag) {
				array_unshift($arrayNew, $value);

			} elseif ($value['id'] == 'DummyOk' && $flag) {
				array_unshift($arrayNew, $value);
			}
		}

		$vars['vars']['varsDetail'] = $arrayNew;

		return $vars;
	}

	/**
	 *
	 */
	protected function _checkUpdateVersion()
	{
		$flag = 0;
		$array = scandir(PATH_BACK_DAT_TEMP);
		foreach ($array as $key => $value) {
			if (preg_match( "/^\.{1,2}$/", $value)) {
				continue;
			}
			if ($value == 'flagUpdate.cgi') {
				$flag = 1;
				break;
			}
		}

		return $flag;
	}

	/**
	 *
	 */
	protected function _deleteUpdateVersion()
	{
		$array = scandir(PATH_BACK_DAT_TEMP);
		foreach ($array as $key => $value) {
			if (preg_match( "/^\.{1,2}$/", $value)) {
				continue;
			}
			if ($value == 'flagUpdate.cgi') {
				$path = PATH_BACK_DAT_TEMP . $value;
				unlink($path);
				return;
			}
		}
	}

	/**
	 *
	 */
	protected function _updateVarsVersion($vars)
	{
		global $varsPreference;
		global $varsRequest;
		global $classCheck;

		$flag = $classCheck->checkModule(array(
			'idModule' => $varsRequest['query']['module'],
			'flagType' => 'Admin'
		));

		$flagUpdateVersion = $this->_checkUpdateVersion();
		if (!$flagUpdateVersion) {
			if ($varsPreference['flagVersionUpdate'] === '0') {
				$flag = 0;
				$strLastVersion = NUM_VERSION;
			}
		}

		if ($flag) {
			if ($flagUpdateVersion) {
				$strLastVersion = NUM_VERSION;

			} else {
				$path = PATH_INFO_SSL . 'version.php';
				$params = array(
					'version' => NUM_VERSION,
				);
				if (!FLAG_TEST) {
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $path);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch, CURLOPT_HEADER, false);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
					curl_setopt($ch, CURLOPT_FAILONERROR, true);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
					curl_setopt($ch, CURLOPT_TIMEOUT, 30);
					$output = curl_exec($ch);
					curl_close($ch);

					$strLastVersion = $output;
				}
			}
		}

		$vars['vars']['strLastVersion'] = $strLastVersion;

		$arrVersion = preg_split("/\./", $varsPreference['strVersion']);
		$numDBVersion = (int) join('', $arrVersion);
		//rucaro update
		$flagNew = 0;
		if ($strLastVersion) {
			$arrVersion = preg_split("/\./", $strLastVersion);
			$numLastVersion = (int) join('', $arrVersion);
			if ($numDBVersion < $numLastVersion) {
				$flagNew = 1;
			}
		}

		//local update
		$flagUpdate = 0;
		$arrVersion = preg_split("/\./", NUM_VERSION);
		$numFileVersion = (int) join('', $arrVersion);
		if ($numDBVersion < $numFileVersion) {
			$flagUpdate = 1;
		}

		//バージョン更新が有効か無効か
		if ($vars['vars']['flagVersion']) {
			$flagUpdate = 1;

			//vars.FlagVersionUpdate = 1; version info on
			//vars.FlagVersionUpdate = 2; version info off

			$flag = (int) $varsRequest['query']['jsonValue']['vars']['FlagVersionUpdate'];
			if ($flag == 1) {
				$flagUpdate = 0;
			}
		}


		if (!$flagUpdateVersion) {
			if ($varsPreference['flagVersionUpdate'] === '0') {
				$flagUpdate = 0;
				$flagNew = 0;
			}
		}

if (FLAG_TEST) {
	$flagUpdate = 1;
	$flagNew = 1;
}

		if ($flagNew) {
			if ($flagUpdate) {
				$arrayNew = array();
				$array = $vars['vars']['varsBtn'];
				foreach ($array as $key => $value) {
					if ($value['id'] == 'Version') {
						$arrayNew[] = $value;
						break;
					}
				}
				$vars['vars']['varsBtn'] = $arrayNew;
				$arrayNew = array();
				$array = $vars['vars']['varsDetail'];
				foreach ($array as $key => $value) {
					if ($value['id'] == 'DummyUpdate') {
						$arrayNew[] = $value;
						break;
					}
				}
				$vars['vars']['varsDetail'] = $arrayNew;

			//need update file
			} else {
				$arrayNew = array();
				$array = $vars['vars']['varsBtn'];
				foreach ($array as $key => $value) {
					if ($value['id'] == 'Download') {
						$arrayNew[] = $value;
						break;
					}
				}
				$vars['vars']['varsBtn'] = $arrayNew;
				$arrayNew = array();
				$array = $vars['vars']['varsDetail'];
				foreach ($array as $key => $value) {
					if ($value['id'] == 'DummyChange' || $value['id'] == 'DummyError' || $value['id'] == 'DummyUpdateNow') {
						$value['strComment'] = str_replace('<%replace%>', $varsPreference['strVersion'], $value['strComment']);
						$value['strComment'] = str_replace('<%replaceNew%>', $vars['vars']['strLastVersion'], $value['strComment']);
						$arrayNew[] = $value;
					}
				}

				$vars['vars']['varsDetail'] = $arrayNew;
			}

		//not release
		} else {
			$arrayNew = array();
			$array = $vars['vars']['varsBtn'];
			foreach ($array as $key => $value) {
				if ($varsPreference['flagVersionUpdate']) {
					if ($value['id'] == 'FlagFalse') {
						$arrayNew[] = $value;
						break;
					}
				} else {
					if ($value['id'] == 'FlagTrue') {
						$arrayNew[] = $value;
						break;
					}
				}
			}
			$vars['vars']['varsBtn'] = $arrayNew;
			$arrayNew = array();
			$array = $vars['vars']['varsDetail'];
			foreach ($array as $key => $value) {
				if ($value['id'] == 'DummyNone' && $varsPreference['flagVersionUpdate']) {
					$value['strComment'] = str_replace('<%replace%>', NUM_VERSION, $value['strComment']);
					$arrayNew[] = $value;
					break;

				} elseif ($value['id'] == 'DummyUse' && !$varsPreference['flagVersionUpdate']) {
					$value['strComment'] = str_replace('<%replace%>', NUM_VERSION, $value['strComment']);
					$arrayNew[] = $value;
					break;
				}
			}
			$vars['vars']['varsDetail'] = $arrayNew;
		}


		return $vars;
	}


	/**
	 *
	 */
	protected function _updateVarsFlagSign($vars)
	{
		return $this->_updateVarsPreference($vars);
	}

	/**
	 *
	 */
	protected function _updateVarsFlagForgot($vars)
	{
		return $this->_updateVarsPreference($vars);
	}

	/**
	 *
	 */
	protected function _updateVarsFlagVersionUpdate($vars)
	{
		global $varsPreference;
		global $classEscape;

		$array = &$vars['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			$str = $classEscape->toLower(array('str' => $array[$key]['id']));
			if ($value['id'] == 'FlagVersionUpdate') {
				$array[$key]['value'] = $varsPreference[$str];
			}
		}

		return $vars;
	}

	/**
	 *
	 */
	protected function _updateVarsPreferenceJson($vars)
	{
		global $varsPreference;
		global $classEscape;

		$array = &$vars['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			$str = $classEscape->toLower(array('str' => $array[$key]['id']));
			$arrData = (!$varsPreference[$str])? array('') : $varsPreference[$str];
			$num = 0;
			$arrayNew = array();
			foreach ($arrData as $keyData => $valueData) {
				$varsTmpl = $array[$key]['varsFormList']['templateDetail'];
				$varsTmpl['id'] = $keyData;
				$varsTmpl['numSort'] = $keyData;
				$varsTmpl['value'] = $valueData;
				$arrayNew[$num] = $varsTmpl;
				$num++;
			}
			$array[$key]['varsFormList']['varsDetail'] = $arrayNew;
		}

		return $vars;
	}

    /**
     *
     */
	protected function _updateVarsJsonIpSignReject($vars)
	{
		return $this->_updateVarsPreferenceJson($vars);
	}

    /**
     *
     */
	protected function _updateVarsJsonMailSignReject($vars)
	{
		return $this->_updateVarsPreferenceJson($vars);
	}

    /**
     *
     */
	protected function _updateVarsJsonIpAccessAccept($vars)
	{
		global $varsMedia;

		$array = &$vars['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'JsonIpAccessAccept') {
				$str = 'ip : ' . $varsMedia['ip'] . ' ,  host : ' . $varsMedia['host'];
				$array[$key]['strExplain'] = str_replace('<%replace%>', $str, $value['strExplain']);
			}
		}


		return $this->_updateVarsPreferenceJson($vars);
	}

	/**
     *
     */
	protected function _updateVarsJsonIpAccessReject($vars)
	{
		return $this->_updateVarsPreferenceJson($vars);
	}

    /**
     *
     */
	protected function _updateVarsNumAutoMustLogout($vars)
	{
		return $this->_updateVarsPreference($vars);
	}

    /**
     *
     */
	protected function _updateVarsNumPasswordLimit($vars)
	{
		return $this->_updateVarsPreference($vars);
	}

	/**
     *
     */
	protected function _updateVarsFlagLoginMail($vars)
	{
		return $this->_updateVarsPreference($vars);
	}

	/**
     *
     */
	protected function _updateVarsFlagLoginSecond($vars)
	{
		return $this->_updateVarsPreference($vars);
	}

    /**
     *
     */
	protected function _updateVarsStrCodeName($vars)
	{
		global $varsPreference;
		global $varsAccount;
		global $varsTerm;

		global $classDb;

		$vars = $this->_updateVarsAccount($vars);

		$rows = $classDb->getSelect(array(
			'idModule' => 'base',
			'strTable' => 'baseApplyChange',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'arrWhere' => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idAccount',
					'flagCondition' => 'eq',
					'value'         => $varsAccount['id'],
				),
			),
			'arrColumn' => array('stampRegister', 'flagAttest'),
		));

		$flagAttestCheck = 0;
		$numLimit = 0;
		if ($rows['numRows']) {
			$num = NUM_SESSION - (TIMESTAMP - $rows['arrRows'][0]['stampRegister']);
			$numLimit = round($num /60/60);
			if ($num > 0) {
				$flagAttestCheck = 1;
			}


		}

		$flagApplyCheck = 0;
		if ($rows['arrRows'][0]['flagAttest']) {
			$flagAttestCheck = 1;
			$flagApplyCheck = 1;
		}

		$array = $vars['vars']['varsDetail'];
		$arrayNew = array();
		foreach ($array as $key => $value) {
			if ($value['id'] == 'DummyAccount') {
				$id = $varsAccount['idTerm'];
				if (!$varsAccount['flagWebmaster'] && $varsTerm[$id]['stampEnd']) {
					$value['stamp'] = $varsTerm[$id]['stampEnd'];
					$arrayNew[] = $value;
				}

			} elseif ($value['id'] == 'DummyCommentApply') {
				if ($flagApplyCheck && $flagAttestCheck) {
					$arrayNew[] = $value;
					$vars['vars']['varsBtn'] = array();

				} else if ($flagAttestCheck) {

				} else {

				}

			} elseif ($value['id'] == 'DummyCommentAttest') {
				if ($flagApplyCheck && $flagAttestCheck) {

				} else if ($flagAttestCheck) {
					$value['strComment'] = str_replace('<%replace%>', $numLimit, $value['strComment']);
					$arrayNew[] = $value;

				} else {

				}

			} elseif ($value['id'] == 'DummyCommentStart') {
				if ($flagApplyCheck && $flagAttestCheck) {

				} else if ($flagAttestCheck) {

				} else {
					$arrayNew[] = $value;
				}

			} else {
				if (!($flagApplyCheck && $flagAttestCheck)) {
					$arrayNew[] = $value;

				}
			}
		}

		$vars['vars']['varsDetail'] = $arrayNew;

		return $vars;
	}

    /**
     *
     */
	protected function _updateVarsStrPassword($vars)
	{
		global $varsPreference;
		global $varsAccount;
		global $varsTerm;

		$array = $vars['vars']['varsDetail'];
		$arrayNew = array();
		$id = $varsAccount['idTerm'];
		$stampEnd = 0;
		if (!$varsAccount['flagWebmaster'] && $varsTerm[$id]['stampEnd']) {
			$stampEnd = $varsTerm[$id]['stampEnd'];
		}

		foreach ($array as $key => $value) {
			if ($value['id'] == 'DummyPassword') {
				if ($varsPreference['numPasswordLimit']) {
					$value['stamp'] = $varsAccount['stampUpdatePassword'] + $varsPreference['numPasswordLimit'] * 86400;
					if ($stampEnd && $value['stamp'] > $stampEnd) {
						$value['stamp'] = $stampEnd;
					}
					$arrayNew[] = $value;
				}

			} elseif ($value['id'] == 'StrPassword') {
				$value['strExplain'] = str_replace('<%replace%>', $varsPreference['numPassword'], $value['strExplain']);
				$value['value'] = '';
				$arrayErr = $value['arrayError'];
				foreach ($arrayErr as $keyErr => $valueErr) {
					if ($arrayErr[$keyErr]['flagCheck'] == 'min') {
						$arrayErr[$keyErr]['num'] = (int) $varsPreference['numPassword'];
						break;
					}
				}
				$value['arrayError'] = $arrayErr;
				$arrayNew[] = $value;

			} elseif ($value['id'] == 'StrPasswordConfirm') {
				$value['value'] = '';
				$arrayNew[] = $value;

			} else {
				$arrayNew[] = $value;
			}
		}

		$vars['vars']['varsDetail'] = $arrayNew;

		return $vars;
	}

    /**
     *
     */
	protected function _updateVarsNumTimeZone($vars)
	{
		return $this->_updateVarsAccount($vars);
	}

    /**
     *
     */
	protected function _updateVarsStrHoliday($vars)
	{
		$path = $this->getPath(array('path' => $this->_extSelf['pathHoliday']));
		$array = scandir($path);
		$arrayOption = array();
		$num = 0;
		foreach ($array as $key => $value) {
			$pathFile = $path . $value;
			if ( preg_match( "/^\.{1,2}$/", $value) || is_dir($pathFile)) {
				continue;
			}
			$varsHoliday = $this->getVars(array(
				'path' => $pathFile,
			));

			$row = array();
			$row['strTitle'] = $varsHoliday['strTitle'];
			$row['value'] = $varsHoliday['id'];
			$arrayOption[$num] = $row;
			$num++;

		}

		$array = &$vars['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'StrHoliday') {
				$array[$key]['arrayOption'] = array_merge($value['arrayOption'], $arrayOption);
				$array[$key]['numSize'] = count($array[$key]['arrayOption']);
				break;
			}
		}

		return $this->_updateVarsAccount($vars);
	}

    /**
     *
     */
	protected function _updateVarsStrLang($vars)
	{
		$arrayOption = $this->getLangOption(array(
			'pathLangDat' => $this->_extSelf['pathLangDat'],
			'pathLangDir' => $this->_extSelf['pathLangDir'],
		));
		$array = &$vars['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			if ($array[$key]['id'] == 'StrLang') {
				$array[$key]['arrayOption'] = $arrayOption;
				break;
			}
		}

		return $this->_updateVarsAccount($vars);
	}

    /**
     *
     */
	protected function _updateVarsNumList($vars)
	{
		return $this->_updateVarsAccount($vars);
	}

	/**
     *
     */
	protected function _updateVarsFlagLoginMailAccount($vars)
	{
		global $varsAccount;
		global $classEscape;

		$array = &$vars['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			$str = 'flagLoginMail';
			$array[$key]['value'] = (is_null($varsAccount[$str]))? 0 : $varsAccount[$str];
		}

		return $vars;
	}

	/**
     *
     */
	protected function _updateVarsFlagLoginSecondAccount($vars)
	{
		global $varsAccount;
		global $classEscape;

		$array = &$vars['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			$str = 'flagLoginSecond';
			$array[$key]['value'] = (is_null($varsAccount[$str]))? 0 : $varsAccount[$str];
		}

		return $vars;
	}

    /**
     *
     */
	protected function _updateVarsStrAutoBoot($vars)
	{
		global $varsAccount;
		global $varsPreference;
		global $varsModule;

		global $classEscape;

		$array = array();
		$id = $varsAccount['idModule'];

		if ($varsAccount['flagWebmaster']
			|| preg_match( "/,base,/", $varsModule[$id]['arrCommaIdModuleAdmin'])
		) {
			$array = $varsPreference['jsonModule'];

		} else {
			$array = $classEscape->splitCommaArrayData(array(
				'data' => $varsModule[$id]['arrCommaIdModuleUser']
			));
			$arrayNew = array();
			foreach ($array as $key => $value) {
				$arrayNew[$value] = 1;
			}
			$array = $arrayNew;
		}


		$arrStrModule = $this->getStrModuleTitle();

		$num = 0;
		$arrayOption = array();
		foreach ($array as $key => $value) {
			if ($key == 'base') {
				continue;
			}
			$row = array();
			$row['strTitle'] = $arrStrModule[$key];
			$row['value'] = $key;
			$arrayOption[$num] = $row;
			$num++;
		}

		$array = &$vars['vars']['varsDetail'];
		foreach ($array as $key => $value) {
			if ($array[$key]['id'] == 'StrAutoBoot') {
				$array[$key]['arrayOption'] = array_merge(array($value['varsTmpl']['arrayOption']), $arrayOption);
				$array[$key]['numSize'] = count($array[$key]['arrayOption']);
				break;
			}
		}

		return $this->_updateVarsAccount($vars);
	}

    /**
     *
     */
	protected function _updateVarsNumAutoLogout($vars)
	{
		return $this->_updateVarsAccount($vars);
	}

    /**
     *
     */
	protected function _updateVarsNumAutoPopup($vars)
	{
		return $this->_updateVarsAccount($vars);
	}

    /**
     *
     */
	protected function _updateVarsRelease($vars)
	{
		$array = $vars['vars']['varsBtn'];
		foreach ($array as $key => $value) {
			if ($array[$key]['id'] == 'Release') {
				$array[$key]['path'] = PATH_INFO;
				break;
			}
		}
		$vars['vars']['varsBtn'] = $array;

		return $vars;
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
			return $this->$method($arr);
		}
	}

	/**
	 * $arr = array(
	 *     'arrColumn' => array(),
	 *     'arrValue'  => array(),
	 * )
	 */
	protected function _updateDbPreference($arr)
	{
		global $classDb;

		$classDb->updateRow(array(
			'idModule'  => 'base',
			'strTable'  => 'basePreference',
			'arrColumn' => $arr['arrColumn'],
			'arrWhere'  => array(),
			'arrValue'  => $arr['arrValue'],
		));

		global $classInit;
		$classInit->updateVarsPreference();

	}

	/**
	 * $arr = array(
	 *     'arrColumn' => array(),
	 *     'arrValue'  => array(),
	 * )
	 */
	protected function _updateDbAccount($arr)
	{
		global $classDb;
		global $varsAccount;

		$classDb->updateRow(array(
			'idModule'  => 'base',
			'strTable'  => 'baseAccount',
			'arrColumn' => $arr['arrColumn'],
			'arrWhere'  => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'id',
					'flagCondition' => 'eq',
					'value'         => $varsAccount['id'],
				),
			),
			'arrValue'  => $arr['arrValue'],
		));
		$this->updateDbPreferenceStamp(array('strColumn' => 'account'));

		global $classInit;
		$classInit->updateVarsAccount();
	}


	/**
	 *
	 */
	protected function _updateDbStrSiteName($arr)
	{
		global $classEscape;
		global $classCheck;
		global $varsAccounts;
		global $varsPreference;

		$array = $arr['arrColumn'];
		foreach ($array as $key => $value) {
			if ($array[$key] == 'strSiteMailPc') {
				$strSiteMailPc = $arr['arrValue'][$key];
			}
		}
/*
		$strTemp = str_replace("\\", '/', $varsPreference['strTopUrl']);
		$arrTemp = preg_split("/\//", $strTemp);
		$strDomainUrl = $arrTemp[2];
		$arrTemp = preg_split("/@/", $strSiteMailPc);
		$strDomainMail = $arrTemp[1];
		if ($strDomainUrl == $strDomainMail) {
			$this->sendVars(array(
				'flag'    => 'strSameDomain',
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(
					'idTarget' => 'strSiteName',
				),
			));
		}
*/
		if ($varsPreference['strSiteMailPc'] != $strSiteMailPc) {
			$array = $varsAccounts;
			foreach ($array as $key => $value) {
				if ($value['strMailPc'] == $strSiteMailPc) {
					$this->sendVars(array(
						'flag'    => 'strSiteMailPc',
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(
							'idTarget' => 'strSiteName',
						),
					));
				}
			}
		}

		$this->_updateDbPreference($arr);
	}

	/**
	 *
	 */
	protected function _updateDbFlagMaintenance($arr)
	{
		$this->_updateDbPreference($arr);
	}


	/**
	 *
	 */
	protected function _updateDbArrCommaIdAccountMaintenance($arr)
	{
		global $classEscape;
		global $classCheck;
		global $varsAccounts;

		$str = $arr['arr']['arrCommaIdAccountMaintenance'];
		if ($str == '') {
			$this->_updateDbPreference($arr);
			return;
		}

		$arrayId  = $classEscape->splitCommaArrayData(array('data' => $str));
		foreach ($arrayId as $key => $value) {
			if (!$varsAccounts[$value]) {
				$this->sendVars(array(
					'flag'    => 40,
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => '',
				));
			}
		}
		$array = $arr['arrColumn'];
		foreach ($array as $key => $value) {
			if ($array[$key] == 'arrCommaIdAccountMaintenance') {
				$arr['arrValue'][$key]  = $classEscape->joinCommaArray(array('arr' => $arrayId));
			}
		}
		$this->_updateDbPreference($arr);
	}

	/**
	 *
	 */
	protected function _updateDbRebuild($arr)
	{
		global $classRebuild;
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsPreference;
		global $varsAccounts;

		$array = scandir(PATH_BACK_CLASS_ELSE_PLUGIN);
		$arrayNew = array();
		$jsonModule = $varsPreference['jsonModule'];
		foreach ($array as $key => $value) {
			if ( preg_match( "/^\.{1,2}$/", $value)) {
				continue;

			}
			if ($varsPreference['jsonModule'][$value]) {
				continue;
			}
			$arrayNew[$value] = 1;
			$jsonModule[$value] = 1;
		}

		if (!$arrayNew) {
			$this->sendVars(array(
				'flag'    => 40,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(
					'idTarget' => 'rebuild',
				),
			));
		}

		$array = array('DbTable');
		$flag = 0;
		foreach ($array as $key => $value) {
			$flag = $classRebuild->run(array(
				'flagType'    => $value,
				'arrIdModule' => $arrayNew,
			));
		}

		if ($flag) {
			exit;
		}

		$jsonModule = json_encode($jsonModule);

		try {
			$dbh->beginTransaction();

			$array = array('DbInsert');
			foreach ($array as $key => $value) {
				$classRebuild->run(array(
					'flagType'    => $value,
					'arrIdModule' => $arrayNew,
				));
			}

			$arrayAccount = &$varsAccounts;
			foreach ($arrayAccount as $keyAccount => $valueAccount) {
				$classRebuild->run(array(
					'flagType'    => 'DbInsertAccount',
					'varsAccount' => $valueAccount,
					'arrIdModule' => $arrayNew,
				));
			}

			$classDb->updateRow(array(
				'idModule'  => 'base',
				'strTable'  => 'basePreference',
				'arrColumn' => array('jsonModule'),
				'arrWhere'  => array(),
				'arrValue'  => array($jsonModule),
			));

			$dbh->commit();

		} catch (PDOException $e) {
			$dbh->rollBack();
			$flag = 1;
			if (FLAG_TEST) {
				var_dump($e->getMessage());
				exit;
			}
		}

		if ($flag) {
			exit;
		}

		$array = array('Css', 'Js');
		foreach ($array as $key => $value) {
			$classRebuild->run(array(
			    'flagType' => $value,
			));
		}

	}


	/**
	 *
	 */
	protected function _getVersionFile($arr)
	{
		global $classFile;

		$array = scandir(PATH_BACK_DAT_VERSION);
		$arrayNew = array();
		foreach ($array as $key => $value) {
			if ( preg_match( "/^\.{1,2}$/", $value)) {
				continue;
			}
			if ( preg_match("/^rucaro(.*?)\.zip$/", $value)) {
				$pathZip = PATH_BACK_DAT_VERSION . $value;
				unlink($pathZip);
			}
		}

		// 'version' param start x >= 1.36.55
		$path = PATH_INFO_SSL;
		$params = array(
			'p'        => 'katachi_download',
			'flagPost' => 2,
			'version'  => NUM_VERSION,
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $path);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		$output = curl_exec($ch);
		curl_close($ch);

		$data = $output;

 		$strVersion = $arr['strVersion'];
 		$pathZip = PATH_BACK_DAT_VERSION . 'rucaro' .$strVersion . '.zip';
 		if (!$data || $data === FALSE) {
 			$this->_sendVersionError(__LINE__);

 		} else {
 			file_put_contents($pathZip, $data);
 		}

		if (!file_exists($pathZip)) {
			$this->_sendVersionError(__LINE__);
		}

		if (!class_exists('ZipArchive')) {
			$this->_sendVersionError(__LINE__);
		}

		$zip = new ZipArchive();
		$flag = $zip->open($pathZip);
		if ($flag === TRUE) {
			$dirTop = dirname(PATH_TOP);
			$strDir = str_replace("\\", '/', PATH_TOP);
			$arrDir = preg_split("/\//", $strDir);
			$strDirName = end($arrDir);
			if ($strDirName == 'rucaro') {
				$pathCopy = $dirTop;
				$zip->extractTo($pathCopy);

			} else {
				$pathBase = PATH_BACK_DAT_VERSION . 'rucaro';
				$pathCopy = $dirTop . '/' . $strDirName;
				$zip->extractTo($pathBase);
				$classFile->copyAll($pathBase, $pathCopy);
				$classFile->deleteAll($pathBase);
			}
			$zip->close();
			unlink($pathZip);

		} else {
			unlink($pathZip);
			$this->_sendVersionError(__LINE__);
		}
		sleep(5);
	}

		/**
	 *
	 */
	protected function _sendVersionError($flag)
	{
		$this->sendVars(array(
			'flag'    => 'connect',
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'idTarget'  => 'version',
				'flagError' => (FLAG_TEST)? $flag : '',
			),
		));

	}

		/**
	 *
	 */
	protected function _sendDlled()
	{
		$this->sendVars(array(
			'flag'    => 'dlled',
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'idTarget'  => 'version',
			),
		));
	}

	/**
	 *
	 */
	protected function _updateDbVersion($arr)
	{
		global $classRebuild;
		global $varsPreference;
		global $varsRequest;

		//バージョン更新機能有効選択画面だったら
		$flag = (int) $varsRequest['query']['jsonValue']['vars']['FlagVersionUpdate'];
		if ($flag > 0) {
			$flagVersionUpdate = ($flag == 1)? 1 : 0;
			$arrColumn = array('flagVersionUpdate');
			$arrValue = array($flagVersionUpdate);

			$this->_updateDbPreference(array(
				'arrColumn' => $arrColumn,
				'arrValue'  => $arrValue,
			));
			return;
		}

		$flagUpdateVersion = $this->_checkUpdateVersion();




		if (!$flagUpdateVersion) {
			if ($varsPreference['flagVersionUpdate'] === '0') {
if (!FLAG_TEST) {
				$this->sendVars(array(
					'flag'    => 40,
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => '',
				));
}
			}
		}

if (!FLAG_TEST) {


		//get update file
		//1巡目 zipを入手し展開する
		if (NUM_VERSION == $varsPreference['strVersion']) {
			if (!is_null($arr['arr']['dummyChange'])) {
				$this->_getVersionFile(array(
					'strVersion' => $arr['varsTarget']['vars']['strLastVersion']
				));
				$this->_sendDlled();
				return;
			}
		}
}
//error_reporting(E_ALL ^ E_NOTICE);
//ini_set('display_errors', 1);

//var_dump(__LINE__,NUM_VERSION,$varsPreference);


		//2巡目 データの再構築

		//キャッシュクリア
		$array = scandir($this->_extSelf['pathCacheDat']);
		foreach ($array as $key => $value) {
			if ( preg_match( "/^\.{1,2}$/", $value)) {
				continue;
			}
			$strFile = $value;
			unlink($this->_extSelf['pathCacheDat'] . $strFile);
		}
//var_dump(__LINE__);
		$arrVersion = preg_split("/\./", $varsPreference['strVersion']);
		$numVersion = (int) join('', $arrVersion);
		$array = scandir(PATH_BACK_DAT_VERSION);

		$arrayNew = array();
		foreach ($array as $key => $value) {
			if ( preg_match( "/^\.{1,2}$/", $value)) {
				continue;
			}
			preg_match("/^Batch(.*?)\.php$/", $value, $arrMatch);
			list($str, $numVer) = $arrMatch;
			if (is_null($numVer)) {
				continue;
			}
			$arrayNew[] = (int) $numVer;
		}
//var_dump(__LINE__);
		sort($arrayNew);
		$array = $arrayNew;
		foreach ($array as $key => $value) {
			$strFile = 'Batch' . $value . '.php';
			$path = PATH_BACK_DAT_VERSION . $strFile;
			if (is_dir($path)) {
				continue;
			}

			require_once($path);

			$arrFile = preg_split("/\./", $strFile);
			$str = $arrFile[0];
			$strClass = 'Code_' . $str;
			$classCall = new $strClass(array(
				'numVersion' => $numVersion
			));
			$classCall->run();
			$pathName = PATH_BACK_DAT_VERSION . '_' . $strFile;

if (!FLAG_TEST) {
				$path = PATH_BACK_DAT_VERSION . $strFile;
				rename($path, $pathName);
}
		}

//var_dump(__LINE__);

if (!FLAG_TEST) {

		//front file reload
		$array = array('Css', 'Js');
		foreach ($array as $key => $value) {
			$classRebuild->run(array(
			    'flagType' => $value,
			));
		}
}

//var_dump(__LINE__);


if (!FLAG_TEST) {
			if (file_exists(PATH_CONFIG_FILE)) {
				unlink(PATH_CONFIG_FILE);
			}
//var_dump(__LINE__);
			$this->_deleteUpdateVersion();
//var_dump(__LINE__);
}
if (FLAG_TEST) {
	//exit;
}
		$num = NUM_VERSION;



//var_dump(__LINE__,NUM_VERSION,$varsPreference);

		$varsPreference['jsonVersion'][$num] = TIMESTAMP;

		$jsonVersion = json_encode($varsPreference['jsonVersion']);
if (!FLAG_TEST) {
		$this->_updateDbPreference(array(
			'arrColumn' => array('strVersion', 'jsonVersion'),
			'arrValue'  => array(NUM_VERSION, $jsonVersion),
		));
}

//var_dump(__LINE__,NUM_VERSION,$varsPreference);
//exit;


	}

	/**
	 *
	 */
	protected function _updateDbFlagSign($arr)
	{
		$this->_updateDbPreference($arr);
	}

	/**
	 *
	 */
	protected function _updateDbFlagLoginMail($arr)
	{
		$this->_updateDbPreference($arr);
	}

	/**
	 *
	 */
	protected function _updateDbFlagLoginSecond($arr)
	{
		$this->_updateDbPreference($arr);
	}

	/**
	 *
	 */
	protected function _updateDbFlagForgot($arr)
	{
		$this->_updateDbPreference($arr);
	}

	/**
	 *
	 */
	protected function _updateDbFlagReject($arr)
	{
		global $varsPreference;
		global $classFile;

		if ((int) $arr['arr']['flagReject'] != (int) $varsPreference['flagReject']) {
			$path = PATH_BACK_DAT . 'htaccess/normal.cgi';
			if ((int) $arr['arr']['flagReject']) {
				$path = PATH_BACK_DAT . 'htaccess/foreign.cgi';
			}
			copy($path, PATH_TOP . '/.htaccess');
		}
		$this->_updateDbPreference($arr);
	}

	/**
	 *
	 */
	protected function _updateDbFlagAccessUnknownMail($arr)
	{
		global $classDb;
		global $varsMedia;

		if ((int) $arr['arr']['flagAccessUnknownMailReset']) {
			$classDb->deleteRow(array(
				'idModule'  => 'base',
				'strTable'  => 'baseAccessUnknown',
				'arrWhere'  => array(),
			));
		}

		if ((int) $arr['arr']['flagAccessUnknownMail']) {
			$rows = $classDb->getSelect(array(
				'idModule' => 'base',
				'strTable' => 'baseAccessUnknown',
				'arrLimit' => array(),
				'arrOrder' => array(),
				'flagAnd'  => 1,
				'arrWhere' => array(
					array(
						'flagType'      => '',
						'strColumn'     => 'ip',
						'flagCondition' => 'eq',
						'value'         => $varsMedia['ip'],
					),
				),
			));
			if (!$rows['numRows']) {
				$classDb->insertRow(array(
					'idModule'  => 'base',
					'strTable'  => 'baseAccessUnknown',
					'arrColumn' => array('ip'),
					'arrValue'  => array($varsMedia['ip']),
				));
			}
		}

		$classDb->updateRow(array(
			'idModule'  => 'base',
			'strTable'  => 'basePreference',
			'arrColumn' => array('flagAccessUnknownMail'),
			'arrWhere'  => array(),
			'arrValue'  => array($arr['arr']['flagAccessUnknownMail']),
		));

		global $classInit;
		$classInit->updateVarsPreference();
	}

	/**
	 *
	 */
	protected function _updateDbJsonIpSignReject($arr)
	{
		$this->_updateDbPreference($arr);
	}

	/**
	 *
	 */
	protected function _updateDbJsonMailSignReject($arr)
	{
		$this->_updateDbPreference($arr);
	}

	/**
	 *
	 */
	protected function _updateDbJsonIpAccessAccept($arr)
	{
		global $varsMedia;
		global $classCheck;

		$array = json_decode($arr['arr']['jsonIpAccessAccept'], true);
		foreach ($array as $key => $value) {
			$flag = $classCheck->checkValueFormat(array(
				'flagType' => 'ip',
				'value'    => $value,
			));
			if (!$flag) {
				$flagIp = $classCheck->ipRange(array(
					'ip'  => $varsMedia['ip'],
					'arr' => array($value),
				));

			} else {
				$value = preg_quote($value);
				$value = str_replace('/', '\/', $value);
				if (preg_match("/$value/", $varsMedia['host'])) {
					$flagIp = 1;
				}
			}
		}

		$arrayIpSubnet = json_decode($arr['arr']['jsonIpSubnetAccessAccept'], true);
		$flagIpSubnet = $classCheck->ipRange(array(
			'ip'  => $varsMedia['ip'],
			'arr' => $arrayIpSubnet
		));

		if ($arrayIp || $arrayIpSubnet) {
			if (!($flagIp || $flagIpSubnet)) {
				$this->sendVars(array(
					'flag'    => 'strIpSelf',
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(
						'idTarget' => 'jsonIpAccessAccept',
					),
				));
			}
		}
		$this->_updateDbPreference($arr);
	}

	/**
	 *
	 */
	protected function _updateDbJsonIpAccessReject($arr)
	{
		global $varsMedia;
		global $classCheck;

		$array = json_decode($arr['arr']['jsonIpAccessReject'], true);
		foreach ($array as $key => $value) {
			$flag = $classCheck->checkValueFormat(array(
				'flagType' => 'ip',
				'value'    => $value,
			));
			if (!$flag) {
				$flagIp = $classCheck->ipRange(array(
					'ip'  => $varsMedia['ip'],
					'arr' => array($value),
				));

			} else {
				$value = preg_quote($value);
				$value = str_replace('/', '\/', $value);
				if (preg_match("/$value/", $varsMedia['host'])) {
					$flagIp = 1;
				}
			}
		}

		$arrayIpSubnet = json_decode($arr['arr']['jsonIpSubnetAccessReject'], true);
		$flagIpSubnet = $classCheck->ipRange(array(
			'ip'  => $varsMedia['ip'],
			'arr' => $arrayIpSubnet
		));

		if ($flagIp || $flagIpSubnet) {
			$this->sendVars(array(
				'flag'    => 'strIpSelf',
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(
					'idTarget' => 'jsonIpAccessReject',
				),
			));
		}

		$this->_updateDbPreference($arr);
	}

	/**
	 *
	 */
	protected function _updateDbNumAutoMustLogout($arr)
	{
		$this->_updateDbPreference($arr);
	}


	/**
	 *
	 */
	protected function _updateDbNumPasswordLimit($arr)
	{
		global $classDb;
		global $classInit;

		global $varsAccounts;

		$this->_updateDbPreference($arr);

		$tm = TIMESTAMP;
		$classDb->updateRow(array(
			'idModule'  => 'base',
			'strTable'  => 'baseAccount',
			'arrColumn' => array('stampUpdatePassword'),
			'arrWhere'  => array(),
			'arrValue'  => array($tm),
		));
		$this->updateDbPreferenceStamp(array('strColumn' => 'account'));

	}


	/**
	 *
	 */
	protected function _updateDbNumTimeZone($arr)
	{
		$this->_updateDbAccount($arr);
	}

	/**
	 *
	 */
	protected function _updateDbStrHoliday($arr)
	{
		$this->_updateDbAccount($arr);
	}

	/**
	 *
	 */
	protected function _updateDbStrLang($arr)
	{
		$this->_updateDbAccount($arr);
	}

	/**
	 *
	 */
	protected function _updateDbNumList($arr)
	{
		$this->_updateDbAccount($arr);
	}

	/**
	 * $arr = array(
	 *     'arrColumn' => array(),
	 *     'arrValue'  => array(),
	 * )
	 */
	protected function _updateDbFlagLoginMailAccount($arr)
	{
		global $classDb;
		global $varsAccount;

		$classDb->updateRow(array(
			'idModule'  => 'base',
			'strTable'  => 'baseAccount',
			'arrColumn' => array('flagLoginMail'),
			'arrWhere'  => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'id',
					'flagCondition' => 'eq',
					'value'         => $varsAccount['id'],
				),
			),
			'arrValue'  => array($arr['arr']['flagLoginMailAccount']),
		));
		$this->updateDbPreferenceStamp(array('strColumn' => 'account'));

		global $classInit;
		$classInit->updateVarsAccount();
	}

	/**
	 * $arr = array(
	 *     'arrColumn' => array(),
	 *     'arrValue'  => array(),
	 * )
	 */
	protected function _updateDbFlagLoginSecondAccount($arr)
	{
		global $classDb;
		global $varsAccount;

		$classDb->updateRow(array(
			'idModule'  => 'base',
			'strTable'  => 'baseAccount',
			'arrColumn' => array('flagLoginSecond'),
			'arrWhere'  => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'id',
					'flagCondition' => 'eq',
					'value'         => $varsAccount['id'],
				),
			),
			'arrValue'  => array($arr['arr']['flagLoginSecondAccount']),
		));
		$this->updateDbPreferenceStamp(array('strColumn' => 'account'));

		global $classInit;
		$classInit->updateVarsAccount();
	}

	/**
	 *
	 */
	protected function _updateDbStrAutoBoot($arr)
	{
		$this->_updateDbAccount($arr);
	}

	/**
	 *
	 */
	protected function _updateDbNumAutoLogout($arr)
	{
		$this->_updateDbAccount($arr);
	}

	/**
	 *
	 */
	protected function _updateDbNumAutoPopup($arr)
	{
		$this->_updateDbAccount($arr);
	}

	/**
	 *
	 */
	protected function _updateDbStrCodeName($arr)
	{
		global $classDb;
		global $classMail;
		global $classDisplay;

		global $varsAccount;
		global $varsAccounts;
		global $varsPreference;
		global $varsMedia;
		global $varsModule;

		$tm = TIMESTAMP;
		$str = MICROTIMESTAMP . $classDisplay->getPassword(array(
			'numMark'  => 5,
			'numNum'   => 5,
			'numBig'   => 5,
			'numSmall' => 5,
		));
		$session = hash('sha256', $str);
        $pathConfirm = $varsPreference['strTopUrl'] . 'confirm.php?type=change&id=' . $session;

		$numLimit = round(NUM_SESSION /60/60);

        $strName = $varsPreference['strSiteName'];

        $strUrl = $varsPreference['strSiteUrl'];

		$rows = $classDb->getSelect(array(
			'idModule' => 'base',
			'strTable' => 'baseApplyChange',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'arrWhere' => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idAccount',
					'flagCondition' => 'eq',
					'value'         => $varsAccount['id'],
				),
			),
			'arrColumn' => array('id'),
		));

		$arrColumn = array('stampRegister', 'idAccount', 'session', 'ip', 'strCodeName', 'idLogin', 'strMailPc');
		$arrValue = array($tm, $varsAccount['id'], $session, $varsMedia['ip'], $arr['arr']['strCodeName'], $arr['arr']['idLogin'], $arr['arr']['strMailPc']);

		if ($rows['numRows']) {

			$classDb->updateRow(array(
				'idModule'  => 'base',
				'strTable'  => 'baseApplyChange',
				'arrColumn' => $arrColumn,
				'arrWhere'  => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idAccount',
						'flagCondition' => 'eq',
						'value'         => $varsAccount['id'],
					),
				),
				'arrValue'  => $arrValue,
			));

		} else {
			$classDb->insertRow(array(
				'idModule'  => 'base',
				'strTable'  => 'baseApplyChange',
				'arrColumn' => $arrColumn,
				'arrValue'  => $arrValue,
			));
		}

		$flag = $classMail->setMail(array(
			'pathVars'    => $this->_extSelf['pathVarsChangeUser'],
			'pathTpl'     => $this->_extSelf['pathTplChangeUser'],
			'arrValue'    => array(
				'numLimit' => $numLimit,
				'strName'  => $strName,
				'session'  => $pathConfirm,
				'strUrl'   => $strUrl,
			),
			'mailTo'      => $arr['arr']['strMailPc'],
			'arrMailBcc'  => array(),
			'arrMailCc'   => array(),
			'mailFrom'    => $varsPreference['strSiteMailPc'],
			'strNameFrom' => $varsPreference['strSiteName'],
		));

		if (!$flag) {
			$this->sendVars(array(
				'flag'    => 'strCodeName',
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(
					'idTarget' => 'strCodeName',
				),
			));
		}
	}

	/**
	 *
	 */
	protected function _updateDbStrPassword($arr)
	{
		global $classInit;
		global $classDb;
		global $varsAccount;
		global $varsPreference;

		if ($arr['arr']['strPassword'] != $arr['arr']['strPasswordConfirm']
			||  mb_strlen($arr['arr']['strPassword']) < $varsPreference['numPassword']
		) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__);
			}
			exit;
		}

		$strPassword = hash('sha256', $arr['arr']['strPassword']);

		$rows = $classDb->getSelect(array(
			'idModule' => 'base',
			'strTable' => 'baseLoginPassword',
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idAccount',
					'flagCondition' => 'eq',
					'value'         => $varsAccount['id'],
				),
				array(
					'flagType'      => '',
					'strColumn'     => 'strPassword',
					'flagCondition' => 'eq',
					'value'         => $strPassword,
				),
			),
		));

		if ($rows['numRows']) {
			$this->sendVars(array(
				'flag'    => 'strPassword',
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(
					'idTarget' => 'strPassword',
				),
			));

		}

		$tm = TIMESTAMP;

		$classDb->updateRow(array(
			'idModule'  => 'base',
			'strTable'  => 'baseAccount',
			'arrColumn' => array('strPassword', 'stampUpdatePassword'),
			'arrWhere'  => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'id',
					'flagCondition' => 'eq',
					'value'         => $varsAccount['id'],
				),
			),
			'arrValue'  => array($strPassword, $tm),
		));

		$this->updateDbPreferenceStamp(array('strColumn' => 'account'));

		$classDb->insertRow(array(
			'idModule'  => 'base',
			'strTable'  => 'baseLoginPassword',
			'arrColumn' => array('stampRegister', 'idAccount', 'strPassword'),
			'arrValue'  => array($tm, $varsAccount['id'], $strPassword),
		));

		$classInit->updateVarsAccount();
	}

}
