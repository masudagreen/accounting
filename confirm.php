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
		'flagRequest' => 'or',
		'pathTop'     => $pathTop
	));
	$classInit->run();

	global $varsMedia;
	global $varsRequest;

	if ($varsMedia['device'] == 'else') {
		require_once(PATH_BACK_CLASS_ELSE_CORE_CONFIRM . "Confirm.php");
		$classCall = new Code_Else_Core_CONFIRM_CONFIRM();
		$classCall->run();
	}

