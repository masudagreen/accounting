<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Base_API_Session extends Code_Else_Core_Base_API
{
	protected $_extSelf = array(

	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;

		$method = '_ini' . ucwords($varsRequest['query']['api']['method']);
		$this->$method();
		exit;
	}

	/**
		'session' => string,
		'module'  => 'base',
		'method'  => 'deleteSessionAPI',
		'params'  => array(),
	 */
	protected function _iniDeleteSessionAPI()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsMedia;
		global $varsSession;
		global $varsApiAccount;

		$flagAPI = 1;
		$ip = $varsMedia['ip'];

		$classDb->setDbhMaster();
		try {
			$dbh->beginTransaction();

			$stmt = $dbh->prepare('delete from baseSession where ip = ? and flagAPI = ?;');
			$stmt->execute(array($ip, $flagAPI));

			$stmt = $dbh->prepare('select * from baseSession;');
			$stmt->execute();
			$array = array();
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$jsonToken = $row['jsonToken'];
				$row['jsonToken'] = ($jsonToken)? json_decode($jsonToken, true) : array();
				$str = ($row['idCookie'])? $row['idCookie'] : $row['idMobile'];
				$array[$str] = $row;
			}
			$varsSession = $array;
			if (FLAG_APC) {
				apc_store('varsSession', $varsSession);
			}

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$this->_sendJSON(array(
			'data' => array(
				'flag' => 'success',
			)
		));
	}

	/**
		'session' => string,
		'module'  => 'base',
		'method'  => 'deleteAllSessionAPI',
		'params'  => array(),
	 */
	protected function _iniDeleteAllSessionAPI()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsMedia;
		global $varsSession;
		global $varsApiAccount;

		$flagAPI = 1;

		$classDb->setDbhMaster();
		try {
			$dbh->beginTransaction();

			$stmt = $dbh->prepare('delete from baseSession where flagAPI = ?;');
			$stmt->execute(array($flagAPI));

			$array = array();
			$varsSession = $array;
			if (FLAG_APC) {
				apc_store('varsSession', $varsSession);
			}

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$this->_sendJSON(array(
			'data' => array(
				'flag' => 'success',
			)
		));
	}

	/**
		'session' => string,
		'module'  => 'base',
		'method'  => 'updateSessionAPI',
		'params'  => array(),
	 */
	protected function _iniUpdateSessionAPI()
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $classDisplay;

		global $varsMedia;
		global $varsSession;
		global $varsAccount;
		global $varsPreference;
		global $varsApiAccount;

		$stampRegister = TIMESTAMP;
		$flagAPI = 1;
		$idAccount = $varsApiAccount['idAccount'];
		$ip = $varsMedia['ip'];

		$varsCookie = array();
		$array = $varsApiAccount['arrStrSiteUrl'];
		foreach ($array as $key => $value) {
			$str = MICROTIMESTAMP . $value . $classDisplay->getPassword(array(
				'numMark'  => 5,
				'numNum'   => 5,
				'numBig'   => 5,
				'numSmall' => 5,
			));
			$varsCookie[$key] = hash('sha256', $str);
		}

		$classDb->setDbhMaster();
		try {
			$dbh->beginTransaction();

			$stampLimit = TIMESTAMP - NUM_SESSION * 1;
			$stmt = $dbh->prepare('delete from baseSession where stampRegister < ?;');
			$stmt->execute(array($stampLimit));

			$stmt = $dbh->prepare('delete from baseSession where ip = ? and flagAPI = ?;');
			$stmt->execute(array($ip, $flagAPI));

			foreach ($array as $key => $value) {
				$idCookie = $varsCookie[$key];
				$stmt = $dbh->prepare('insert into baseSession(stampRegister, ip, idCookie, idAccount, flagAPI) values (?, ?, ?, ?, ?);');
				$stmt->execute(array($stampRegister, $ip, $idCookie, $idAccount, $flagAPI));
				$stmt = $dbh->prepare('select * from baseSession where idCookie = ?;');
				$stmt->execute(array($idCookie));
				$session = array();
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$session = $row;
					break;
				}
				$varsSession[$idCookie] = $session;
			}
			if (FLAG_APC) {
				apc_store('varsSession', $varsSession);
			}

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$this->_sendSessionData(array(
			'varsCookie' => $varsCookie,
		));

		$this->_sendJSON(array(
			'data' => array(
				'flag' => 'success',
			)
		));
	}

	/**
	 *
	 */
	protected function _sendSessionData($arr)
	{
		global $varsApiAccount;

		$array = $varsApiAccount['arrStrSiteUrl'];
		foreach ($array as $key => $value) {
			$idCookie = $arr['varsCookie'][$key];
			$data = json_encode(array(
				'flag' => 'success',
				'data' => $idCookie,
			));
			$header = array(
				"Content-Type: application/json; charset=UTF-8",
				'X-FRAME-OPTIONS: SAMEORIGIN',
				'X-Content-Type-Options: nosniff',
				"Content-Length: ".strlen($data)
			);
			$context = array(
				"http" => array(
					"method"  => "POST",
					"header"  => implode("\r\n", $header),
					"content" => $data,
					'timeout' => 30
				)
			);
			$strUrl = $value;
			$res = @file_get_contents($strUrl, false, stream_context_create($context));
		}
	}

	/**
		'session' => string,
		'module'  => 'base',
		'method'  => 'updateAllSessionAPI',
		'params'  => array(),
	 */
	protected function _iniUpdateAllSessionAPI()
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $classDisplay;

		global $varsMedia;
		global $varsSession;
		global $varsAccount;
		global $varsPreference;
		global $varsApiAccounts;

		$stampRegister = TIMESTAMP;
		$flagAPI = 1;

		$arrayNew = array();
		$array = $varsApiAccounts;
		foreach ($array as $key => $value) {
			$temp = array();
			$temp['ip'] = $key;
			$temp['arrStrSiteUrl'] = array();
			$temp['arrIdCookie'] = array();
			$arrayData = $value;
			foreach ($arrayData as $keyData => $valueData) {
				$str = MICROTIMESTAMP . $value['strSiteUrl'] . $classDisplay->getPassword(array(
					'numMark'  => 5,
					'numNum'   => 5,
					'numBig'   => 5,
					'numSmall' => 5,
				));
				$idCookie = hash('sha256', $str);
				$temp['arrIdCookie'][] = $idCookie;
				$temp['arrStrSiteUrl'][] = $valueData['strSiteUrl'];
				$temp['idAccount'] = $valueData['idAccount'];
			}
			$arrayNew[] = $temp;
		}

		$classDb->setDbhMaster();
		try {
			$dbh->beginTransaction();

			$stampLimit = TIMESTAMP - NUM_SESSION * 1;
			$stmt = $dbh->prepare('delete from baseSession where stampRegister < ?;');
			$stmt->execute(array($stampLimit));

			$stmt = $dbh->prepare('delete from baseSession where flagAPI = ?;');
			$stmt->execute(array($flagAPI));

			$array = $arrayNew;
			foreach ($array as $key => $value) {
				$idAccount = $value['idAccount'];
				$ip = $value['ip'];
				$arrayData = $value['arrIdCookie'];
				foreach ($arrayData as $keyData => $valueData) {
					$idCookie = $valueData;
					$stmt = $dbh->prepare('insert into baseSession(stampRegister, ip, idCookie, idAccount, flagAPI) values (?, ?, ?, ?, ?);');
					$stmt->execute(array($stampRegister, $ip, $idCookie, $idAccount, $flagAPI));
				}
			}

			$stmt = $dbh->prepare('select * from baseSession;');
			$stmt->execute();
			$array = array();
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$jsonToken = $row['jsonToken'];
				$row['jsonToken'] = ($jsonToken)? json_decode($jsonToken, true) : array();
				$str = ($row['idCookie'])? $row['idCookie'] : $row['idMobile'];
				$array[$str] = $row;
			}
			$varsSession = $array;
			if (FLAG_APC) {
				apc_store('varsSession', $varsSession);
			}

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$this->_sendSessionDataAll(array(
			'varsData' => $arrayNew,
		));

		$this->_sendJSON(array(
			'data' => array(
				'flag' => 'success',
			)
		));
	}

	/**
	 *
	 */
	protected function _sendSessionDataAll($arr)
	{
		$array = $arr['varsData'];
		foreach ($array as $key => $value) {
			$idAccount = $value['idAccount'];
			$ip = $value['ip'];
			$arrayData = $value['arrIdCookie'];
			foreach ($arrayData as $keyData => $valueData) {
				$idCookie = $valueData;
				$strUrl = $value['arrStrSiteUrl'][$keyData];
				$data = json_encode(array(
					'flag' => 'success',
					'data' => $idCookie,
				));
				$header = array(
					"Content-Type: application/json; charset=UTF-8",
					'X-FRAME-OPTIONS: SAMEORIGIN',
					'X-Content-Type-Options: nosniff',
					"Content-Length: ".strlen($data)
				);
				$context = array(
					"http" => array(
						"method"  => "POST",
						"header"  => implode("\r\n", $header),
						"content" => $data,
						'timeout' => 30
					)
				);
				$res = @file_get_contents($strUrl, false, stream_context_create($context));
			}
		}
	}
}
