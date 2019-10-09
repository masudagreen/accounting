<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcDictionary extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	protected $_extChildSelf = array(
		'varsOption' => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/dictionaryItem.php',
	);

	/**
	 *
	 */
	public function run()
	{
		if (FLAG_TEST) {
			var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
		}
		exit;
	}

	public function allot($arr)
	{
		$method = '_ini' . ucwords($arr['flagStatus']);
		if (method_exists($this, $method)) {
			return $this->$method($arr);

		} else {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__ . $method);
			}
			exit;
		}
	}

	/**
		 (array(
			'flagStatus' => 'varsItem',
		 ))
	 */
	protected function _iniVarsItem($arr)
	{
		$varsItem = $this->_extChildSelf['varsItem'];
		if (!$varsItem) {
			$varsItem = $this->_getVarsItem(array());
			$this->_extChildSelf['varsItem'] = $varsItem;
		}

		return $varsItem;
	}

	/**
		(array(

		))
	 */
	protected function _getVarsItem($arr)
	{
		$varsItem = $this->getVars(array(
			'path' => $this->_extChildSelf['varsOption'],
		));

		return $varsItem;
	}

	/**

	 */
	protected function _iniData($arr)
	{
		global $classEscape;

		$varsItem = $this->_iniVarsItem(array());

		/*
		 * array(
			'version'   => '',
			'strTitle' => '',
		 * )
		 */

		$strTitle = $classEscape->splitJoinStr(array(
			'data'       => $arr['strTitle'],
			'delimiter'  => ' ',
			'flagUnique' => 0,
			'flagArray'  => 0,
		));

		$params = array(
			'cache'                => MICROTIMESTAMP,
			'strTitle'             => $strTitle,
			'idAccountTitleDebit'  => '',
			'idAccountTitleCredit' => '',
		);

		$path = PATH_INFO_SSL . 'dictionary.php';
		if (FLAG_TEST) {
			$path = 'http://localhost/site/rucaro.org/dictionary.php';
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $path);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		$output = curl_exec($ch);
		curl_close($ch);
//var_dump($output);
//exit;
		/*
		 * array(
			'varsData' => array(
				array(
					'strTitle'             => 'netSales ans netSales',
					'idAccountTitleDebit'  => 'netSales',
					'idAccountTitleCredit' => 'netSales',
				),
			),
		 * )
		 */
		if (!$output) {
			$temp = array(
				'strTitle' => $arr['strTitle'],
				'varsData' => array(
					array(
						'strTitle'             => $varsItem['varsStr']['accessOver'],
						'idAccountTitleDebit'  => '',
						'idAccountTitleCredit' => '',
						'flagDisabled'         => 1,
					),
				),
			);
			return $temp;
		}

		$varsResponse = json_decode($output, true);
		if (is_null($varsResponse)) {
			$temp = array(
				'strTitle' => $arr['strTitle'],
				'varsData' => array(
					array(
						'strTitle'             => $varsItem['varsStr']['dataError'],
						'idAccountTitleDebit'  => '',
						'idAccountTitleCredit' => '',
						'flagDisabled'         => 1,
					),
				),
			);
			return $temp;
		}

		if (!$varsResponse) {
			$temp = array(
				'strTitle' => $arr['strTitle'],
				'varsData' => array(
					array(
						'strTitle'             => $varsItem['varsStr']['none'],
						'idAccountTitleDebit'  => '',
						'idAccountTitleCredit' => '',
						'flagDisabled'         => 1,
					),
				),
			);
			return $temp;
		}


		$array = &$varsResponse;
		foreach ($array as $key => $value) {
			$array[$key]['strTitle'] = $varsItem['strDot'] . $value['strTitle'];
		}

		$temp = array(
			'strTitle' => $arr['strTitle'],
			'varsData' => $varsResponse,
		);

		return $temp;
	}

}
