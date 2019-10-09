<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
	$pathTop = realpath(dirname( __FILE__));

	require_once($pathTop . "/back/class/else/core/base/Init.php");
	$classInit = new Code_Else_Core_Base_Init(array(
		'flagConfig' => 1,
		'pathTop'    => $pathTop
	));

	$classInit->run();

	 /**
	  *
	  */
	require_once(PATH_BACK_CLASS_ELSE_CONFIG . "/Config.php");

	$classConfig = new Code_Else_Config();

	$classConfig->run();
