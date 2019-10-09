<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
require_once("back/class/else/lib/Time.php");
require_once("back/class/else/lib/Escape.php");
class Code_Else_Lib_Html
{
	protected $_self = array(

	);

	/**
	 * array(
			'strClass'    => $strClass,
			'flagStatus'  => $flagStatus,
			'numTimeZone' => $numTimeZone,
			'varsDetail'  => $varsDetail,
			'varsColumn'  => $vars['portal']['varsList']['tableTree']['varsDetail']['varsColumn'],
			'varsStatus'  => $vars['portal']['varsList']['tableTree']['varsDetail']['varsStatus'],
	 * )
	 */
	public function allot($arr)
	{
		if ($arr['strClass']) {
			$path = 'back/class/else/lib/Html/' . $arr['strClass'] . ".php";
			$strClass = 'Code_Else_Lib_Html_' . $arr['strClass'];
			require_once($path);
			$classCall = new $strClass;

			return $classCall->allot($arr);
		}
	}
}
