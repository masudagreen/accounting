<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Lib_Display
{
	protected $_self = array(
		'mark'  => array('!','"','#', '$' ,'%' ,'\'' ,'(' ,')' ,'=' ,'~' ,'|' ,'^' ,'@' ,'[' ,';' ,':' ,']' ,',' ,'.' ,'/' ,'`' ,'{' ,'+' ,'*' ,'}' ,'?' ,'-'),
		'num'   => array(0,1,2,3,4,5,6,7,8,9),
		'big'   => array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'),
		'small' => array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'),
	);

	function __construct()
	{

	}

	/**
	 * array(
	 *  'numMark' => int,
	 *  'numNum' => int,
	 *  'numBig' => int,
	 *  'numSmall' => int,
	 * )
	 */
	public function getPassword($arr, $str = '')
	{
		$arrayNew = array();
		$numMax = count($this->_self['mark']) - 1;
		$numAll = $arr['numMark'];
		for ($j = 0; $j < $numAll; $j++) {
			$num = mt_rand(0, $numMax);
			$arrayNew[] = $this->_self['mark'][$num];
		}

		$numMax = count($this->_self['num']) - 1;
		$numAll = $arr['numNum'];
		for ($j = 0; $j < $numAll; $j++) {
			$num = mt_rand(0, $numMax);
			$arrayNew[] = $this->_self['num'][$num];
		}


		$numMax = count($this->_self['big']) - 1;
		$numAll = $arr['numBig'];
		for ($j = 0; $j < $numAll; $j++) {
			$num = mt_rand(0, $numMax);
			$arrayNew[] = $this->_self['big'][$num];
		}

		$numMax = count($this->_self['small']) - 1;
		$numAll = $arr['numSmall'];
		for ($j = 0; $j < $numAll; $j++) {
			$num = mt_rand(0, $numMax);
			$arrayNew[] = $this->_self['small'][$num];
		}

		shuffle($arrayNew);
		$array = $arrayNew;

		foreach ($array as $key => $value) {
			$str .= $value;
		}

		return $str;
	}

	/**
		(array(
			'num'      => 0,
			'numLevel' => 0,
			'flagType' => '',
		))
	 */
	public function getNumDisplay($arr)
	{
		if (!preg_match("/\./", $arr['num'])) {
			return $arr['num'];
		}

		if (!$arr['numLevel']) {
			if ($arr['flagType'] == 'ceil') {
				$arr['num'] = ceil($arr['num']);

			} elseif ($arr['flagType'] == 'floor') {
				$arr['num'] = floor($arr['num']);

			} elseif ($arr['flagType'] == 'round') {
				$arr['num'] = round($arr['num']);
			}

		} else {
			list($num, $str) = preg_split("/\./", $arr['num']);
			$numStr = mb_strlen($str);
			if ($numStr > $arr['numLevel']) {
				$numLevel = pow(10, $arr['numLevel']);
				if ($arr['flagType'] == 'ceil') {
					$arr['num'] = ceil($arr['num'] * $numLevel) / $numLevel;

				} elseif ($arr['flagType'] == 'floor') {
					$arr['num'] = floor($arr['num'] * $numLevel) / $numLevel;

				} elseif ($arr['flagType'] == 'round') {
					$arr['num'] = round($arr['num'] * $numLevel) / $numLevel;
				}

			}
		}

		return $arr['num'];
	}

}
