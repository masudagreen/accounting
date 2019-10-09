<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Base_Root extends Code_Else_Core_Base_Base
{
	protected $_extSelf = array(

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
		} else {
			$this->_iniHtml();
		}
		exit;
	}

    /**
     *
     */
	protected function _iniHtml()
	{
		$vars = $this->getVars(array(
			'path' => $this->_self['path']['file']['varsHtml'],
		));
		global $varsPreference;
		global $varsAccount;

		$vars['strLang'] = STR_LANG;
		$array = array(
			'numAutoLogout' =>  ($varsPreference['numAutoMustLogout'])? $varsPreference['numAutoMustLogout'] : $varsAccount['numAutoLogout'],
			'strAutoBoot'   => $varsAccount['strAutoBoot'],
			'numAutoPopup'  => $varsAccount['numAutoPopup'],
			'numList'       =>  $varsAccount['numList'],
			'strHoliday'    =>  $varsAccount['strHoliday'],
			'numTimeZone'   =>  $varsAccount['numTimeZone'],
			'idAccount'     =>  $varsAccount['id'],
			'strVersion'    =>  NUM_VERSION,
			'strLang'       =>  STR_LANG,
			'arrModule'     =>  $this->_getModule(),
			'numNews'       =>  $this->getNumNews(),
			'strCodeName'   =>  $varsAccount['strCodeName'],
		);
		$vars['numVersion'] = NUM_VERSION;
		$vars['jsonStatus'] = json_encode($array);

		$this->sendHtml(array(
			'path' => $this->_self['path']['file']['tplHtml'],
			'vars' => $vars,
		));
	}

    /**
     *
     */
	protected function _getModule()
	{
		global $varsAccount;
		global $varsModule;
		global $varsPreference;

		$id = $varsAccount['idModule'];
		$array = $varsPreference['jsonModule'];

		$arrayNew = array();
		if ($varsAccount['flagWebmaster']
			|| preg_match( "/,base,/", $varsModule[$id]['arrCommaIdModuleAdmin'])
		) {
			foreach ($array as $key => $value) {
				$arrayNew[$key] = array('flagUse' => 1);
			}

		} else {
			foreach ($array as $key => $value) {
				if (preg_match( "/,$key,/", $varsModule[$id]['arrCommaIdModuleAdmin'])
					|| preg_match( "/,$key,/", $varsModule[$id]['arrCommaIdModuleUser'])
				) {
					$arrayNew[$key] = array('flagUse' => 1);
				}
			}
		}

		return $arrayNew;
	}

}
