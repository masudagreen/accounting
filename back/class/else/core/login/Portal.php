<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Login_Portal  extends Code_Else_Core_Login_Login
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
			'path' =>  $this->_self['path']['file']['varsHtml'],
		));
		$vars['strLang'] = STR_LANG;

		$this->sendHtml(array(
			'path' => $this->_self['path']['file']['tplHtml'],
			'vars' => $vars,
		));
	}

    /**
     *
     */
	protected function _iniVars()
	{
		global $varsPreference;

		$vars = $this->getVars(array(
			'path' => $this->_self['path']['file']['varsPortal'],
		));

		$array = &$vars['portal']['varsDetail']['varsDetail']['start']['varsBtn'];
		//sign form
		if (!(int) $varsPreference['flagSign']) {
			foreach ($array as $key => $value) {
				if ($value['id'] == 'Sign') {
					$array[$key]['flagUse'] = 0;
					$vars['portal']['varsDetail']['varsDetail']['sign'] = null;
					break;
				}
			}
		}

		//forgot form
		if (!(int) $varsPreference['flagForgot']) {
			foreach ($array as $key => $value) {
				if ($value['id'] == 'Forgot') {
					$array[$key]['flagUse'] = 0;
					$vars['portal']['varsDetail']['varsDetail']['forgot'] = null;
					break;
				}
			}
		}

		$arrayOption = $this->getLangOption(array(
			'pathLangDat' => $this->_self['path']['file']['datLang'],
			'pathLangDir' => $this->_self['path']['dir']['varLang'],
		));

		$vars['portal']['varsDetail']['varsDetail']['lang']['varsDetail'][0]['value'] = STR_LANG;
		$vars['portal']['varsDetail']['varsDetail']['lang']['varsDetail'][0]['arrayOption'] = $arrayOption;

		$vars['token'] = $this->setToken();

		$this->sendVars(array(
			'vars' => $vars,
		));
	}
}
