<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
require_once("back/class/else/lib/File.php");
require_once("back/class/else/lib/Display.php");
 /**
  *
  */
class Code_Else_Lib_Crypte
{
    protected $_self = array(
        'path' => 'back/dat/crypte/data.cgi',
    );

	/*
	 *
	* */
	public function setDecrypt($arr)
	{
		$td = mcrypt_module_open(MCRYPT_BLOWFISH, '', MCRYPT_MODE_CBC, '');
		$key = $this->_checkKey();

		$ks = mcrypt_enc_get_key_size($td);
		$key = substr(md5($key), 0, $ks);

		$ivsize = mcrypt_enc_get_iv_size($td);
		$iv = substr(md5($key), 0, $ivsize);

		mcrypt_generic_init($td, $key, $iv);
		$decrypted = mdecrypt_generic($td, $arr['data']);

		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);

		return rtrim($decrypted, "\0");
	}

	/*
	 *
	* */
	public function setEncrypt($arr)
	{
		$td = mcrypt_module_open(MCRYPT_BLOWFISH, '', MCRYPT_MODE_CBC, '');
		$key = $this->_checkKey();

		$ks = mcrypt_enc_get_key_size($td);
		$key = substr(md5($key), 0, $ks);

		$ivsize = mcrypt_enc_get_iv_size($td);
		$iv = substr(md5($key), 0, $ivsize);

		mcrypt_generic_init($td, $key, $iv);
		$encrypted = mcrypt_generic($td, $arr['data']);

		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);

		return $encrypted;
	}

	/*
	 *
	* */
	private function _checkKey()
	{
		$classFile = new Code_Else_Lib_File();

		if (file_exists($this->_self['path'])) {
			$data = $classFile->getArrayFirst(array(
				'path' => $this->_self['path'],
			));

			return $data;
		}

		$classDisplay = new Code_Else_Lib_Display();
		$data = MICROTIMESTAMP . $classDisplay->getPassword(array(
			'numMark'  => 5,
			'numNum'   => 5,
			'numBig'   => 5,
			'numSmall' => 5,
		));
		$data = hash('sha256', $data);

		$classFile->setData(array(
			'path' => $this->_self['path'],
			'data' => $data,
		));

		return $data;
	}
}
