<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Login_Login extends Code_Else_Core_Base_ModuleAbstract
{
	protected $_self = array(
		'path'        => array(
			'file' => array(
				'tplHtml'    => 'else/core/login/html/index.html',
				'tplJs'      => 'else/core/login/js/index.js',
				'varsHtml'    => 'back/tpl/vars/else/core/login/<strLang>/html/index.php',
				'varsPortal'      => 'back/tpl/vars/else/core/login/<strLang>/js/portal.php',
				'datLang'      => 'back/dat/lang/<strLang>/list.csv',
				'datSession'  => 'back/dat/error/sessionTimeOut.txt',
			),
			'dir'  => array(
				'varLang'      => 'back/tpl/vars/else/core/base/',
			),
		),
	);

	function __construct()
	{
		$arr = @func_get_arg(0);
		if (!$arr) {
			return;
		}
		foreach ($arr as $key => $value) {
			if (empty($this->_self[$key])) {
				$this->_self[$key] = $value;
			}
		}
	}

    /**
     *
     */
	public function run()
	{
		global $varsRequest;
		global $classRequest;

		if ($varsRequest['query']['ext']) {
			$str = ucwords($varsRequest['query']['ext']);
			$path = PATH_BACK_CLASS_ELSE_CORE_LOGIN . $str . ".php";
			$module = ucwords($varsRequest['query']['module']);
			if (!file_exists($path) || $module != 'Login') {
				if (preg_match("/output/is", $varsRequest['query']['func'])) {
					$classRequest->output(array(
						'path'         => $this->_self['path']['file']['datSession'],
						'strFileType'  => 'txt',
						'strFileName'  => 'SessionTimeOut.txt',
					));

				} else {
					$this->sendVars(array(
						'flag'    => 0,
						'stamp'   => array(),
						'numNews' => array(),
						'vars'    => array(),
					));
				}
			}
			require_once($path);
			$strClass = 'Code_Else_Core_Login_' . $str;
			$classCall = new $strClass;
			$classCall->run();

		} else {
			require_once(PATH_BACK_CLASS_ELSE_CORE_LOGIN . "Portal.php");
			$classCall = new Code_Else_Core_Login_Portal();
			$classCall->run();
		}
	}

    /**
     *
     */
	public function loop($arr)
	{
		if (preg_match("/^rebuild/", $arr['flagType'])) {
			require_once(PATH_BACK_CLASS_ELSE_CORE_LOGIN . "Rebuild.php");
			$classCall = new Code_Else_Core_Login_Rebuild();
			$classCall->run($arr);

		} elseif ($arr['flagType'] == 'routine') {


		}
	}
}

