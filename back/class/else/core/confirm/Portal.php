<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Confirm_Portal  extends Code_Else_Core_Confirm_Confirm
{
	protected $_extSelf = array(

	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;

		if ($varsRequest['query']['type'] && $varsRequest['query']['id']) {
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

}
