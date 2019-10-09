<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
	$pathTop = realpath(dirname( __FILE__));
	require_once($pathTop . "/back/class/else/core/base/Init.php");
	global $classInit;
	$classInit = new Code_Else_Core_Base_Init(array(
		'pathTop'     => $pathTop,
		'flagRequest' => 'get'
	));
	$classInit->run();

	global $varsAccount;
	global $varsMedia;
	global $varsRequest;

	if ($varsMedia['device'] == 'else') {
		if ($varsAccount) {
			if ($varsRequest['query']['class'] == 'Plugin') {
				$module = strtolower($varsRequest['query']['module']);
				$Module = ucwords($varsRequest['query']['module']);
				$path = PATH_BACK_CLASS_ELSE_PLUGIN . $module . '/' . $Module . ".php";

				if (!file_exists($path)) {
					exit;
				}
				require_once($path);
				$strClass = 'Code_Else_Plugin_' . $Module . '_' . $Module;
				$classCall = new $strClass;
				$classCall->run();

			} else {
				require_once(PATH_BACK_CLASS_ELSE_CORE_BASE . "Base.php");
				$classCall = new Code_Else_Core_Base_Base();
				$classCall->run();
			}

		}
	}


