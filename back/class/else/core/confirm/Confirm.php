<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Confirm_Confirm extends Code_Else_Core_Base_ModuleAbstract
{
	protected $_self = array(
		'path'        => array(
			'file' => array(
				'tplHtml'         => 'else/core/confirm/html/index.html',
				'tplJs'           => 'else/core/confirm/js/index.js',
				'varsHtml'        => 'back/tpl/vars/else/core/confirm/<strLang>/html/index.php',
				'varsPortal'      => 'back/tpl/vars/else/core/confirm/<strLang>/js/portal.php',
				'varsChangeAdmin' => 'back/tpl/vars/else/core/confirm/<strLang>/mail/changeAdmin.php',
				'tplChangeAdmin'  => 'back/tpl/vars/else/core/confirm/<strLang>/mail/changeAdmin.tpl',
				'varsSignAdmin'   => 'back/tpl/vars/else/core/confirm/<strLang>/mail/signAdmin.php',
				'tplSignAdmin'    => 'back/tpl/vars/else/core/confirm/<strLang>/mail/signAdmin.tpl',
			),
		),

	);

	function __construct()
	{

	}

    /**
     *
     */
	public function run()
	{
		global $varsRequest;

		if (!$_POST && $_GET) {
			$varsRequest['flagGetPermit'] = 1;
			require_once(PATH_BACK_CLASS_ELSE_CORE_CONFIRM . "Portal.php");
			$classCall = new Code_Else_Core_Confirm_Portal();
			$classCall->run();

		} elseif ($_POST && !$_GET) {
			$str = ucwords($varsRequest['query']['type']);
			$path = PATH_BACK_CLASS_ELSE_CORE_CONFIRM . $str . ".php";
			if (!file_exists($path)) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__);
				}
				exit;
			}
			require_once($path);
			$strClass = 'Code_Else_Core_Confirm_' . $str;
			$classCall = new $strClass;
			$classCall->run();

		}
	}

    /**
     *
     */
	public function loop($arr)
	{
		if (preg_match("/^rebuild/", $arr['flagType'])) {
			require_once(PATH_BACK_CLASS_ELSE_CORE_CONFIRM . "Rebuild.php");
			$classCall = new Code_Else_Core_Confirm_Rebuild();
			$classCall->run($arr);

		} elseif ($arr['flagType'] == 'routine') {

		}
	}
}
