<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Login_Rebuild  extends Code_Else_Core_Base_Rebuild
{
	protected $_extSelf = array(
		'path' => array(
			'dir' => array(
				'tplCss' => 'back/tpl/templates/else/core/login/css/',
				'langJs' => 'back/tpl/vars/else/core/login/',
			),
			'file' => array(
				'outCss' => 'front/else/core/login/css/style.css',
				'tplJsIndex' => 'else/core/login/js/index.js',
				'varsJsIndex' => 'back/tpl/vars/else/core/login/<strLang>/js/index.php',
				'outJsIndex' => 'front/else/core/login/js/<strLang>/index.js',
		    ),
		),
	);

    /**
     *
     */
	public function run($arr)
	{
		if ($arr['flagType'] == 'rebuildCss') {
			$this->_iniCss(array());

		} elseif ($arr['flagType'] == 'rebuildJs') {
			$this->_iniJs(array());

		}

	}

	/**
	 *
	 */
	protected function _iniJs($arr)
	{
		$this->_setJs(array(
			'arrLang'  => $this->_getLang(array('path' => $this->_extSelf['path']['dir']['langJs'])),
			'pathTpl'  => $this->_extSelf['path']['file']['tplJsIndex'],
			'pathVars' => $this->_extSelf['path']['file']['varsJsIndex'],
			'pathOut'  => $this->_extSelf['path']['file']['outJsIndex'],
		));
	}

	/**
	 * $arr = array(
	 *	 'arrLang' => array(),
	 *	 'pathTpl' => string,
	 *	 'pathVars' => string,
	 *	 'pathOut' => string,
	 * )
	 */
	protected function _setJs($arr)
	{
		global $classSmarty;
		global $classEscape;
		global $classFile;

		$arrayLang = $arr['arrLang'];
		foreach ($arrayLang as $keyLang => $valueLang) {
			$vars = $this->_getVars(array(
				'path'    => $arr['pathVars'],
				'strLang' => $valueLang,
			));
			$vars['varsSystem']['num']['expiresSession'] = NUM_SESSION;
			$json = json_encode($vars);
			$classSmarty->assign('varsLoad', $json);
			$contents = $classSmarty->fetch($arr['pathTpl']);

			if (FLAG_OBFUSCATE) {
				$contents = $classEscape->obfuscate(array( 'data' => $contents) );
			}
			$path = $this->_getPath(array(
				'path' => $arr['pathOut'],
				'strLang' => $valueLang,
			));
			$classFile->setData(array(
				'path' => $path,
				'data' => $contents,
			));
		}
	}


	/**
	 *
	 */
	protected function _iniCss($arr)
	{
		$this->_setAllCss(array(
			'pathInDir'   => $this->_extSelf['path']['dir']['tplCss'],
			'pathOutFile' => $this->_extSelf['path']['file']['outCss'],
		));
	}
}
