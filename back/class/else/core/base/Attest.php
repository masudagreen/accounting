<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Base_Attest
{

	protected $_self = array(
		'pathVarsMaintenance' => 'back/tpl/vars/else/core/base/<strLang>/html/maintenance.php',
		'pathHtmlMaintenance' => 'else/core/base/html/maintenance.html',
		'pathPlugin'          => 'back/class/else/plugin/',
		'pathVarsLogin'       => 'back/tpl/vars/else/core/base/<strLang>/mail/login.php',
		'pathTplLogin'        => 'back/tpl/vars/else/core/base/<strLang>/mail/login.tpl',
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
		global $varsMedia;
		global $varsRequest;

		if (FLAG_API) {
			$this->_checkAPI();
			return;
		}
		$this->_checkReject();
		if ($varsMedia['device'] == 'deviceName') {

		} else  {
			if (is_null($varsRequest['cookie']['id'])) {
				return;
			} else {
				$this->_checkAccount();
			}
		}
		$this->_checkMaintenance(array());
    }

    /**
     *
     */
	protected function _checkReject()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsPreference;
		global $varsMedia;
		global $varsRequest;
		global $varsSession;
		global $classCheck;

		if ($varsRequest['query']['module'] != 'Confirm') {

			if ($classDb->getFlagMaster()) {
/*
if ($varsRequest['query']['module'] != 'Login') {
	if (!preg_match("/output$/i", $varsRequest['query']['func'])) {
		exit;
	}
}
*/
				$token = $varsRequest['query']['token'];
				$stmt = $dbh->prepare('select * from baseToken where token = ?;');
				$stmt->execute(array($token));
				$vars = array();
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$vars = $row;
					break;
				}
				if (!$vars) {
					if (FLAG_TEST) {
						var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' . __LINE__);
					}
					exit;
				}

				$referer = $varsRequest['referer'];
				$strTopUrl = $varsPreference['strTopUrl'];
				$strTopUrl = preg_quote($strTopUrl);
				$strTopUrl = str_replace('/', '\/', $strTopUrl);
			}
		}
		$this->_checkRejectIp();
	}

	/**
     *
     */
	protected function _checkRejectIp()
	{
		global $varsPreference;
		global $varsMedia;
		global $classCheck;

		//array( 'flagCheck' => 'format', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 'json', 'flagType' => 'ip', 'strComment' => array( 'common' => 'IPのフォーマットにエラーがあるようです。', ), ),

		if ($varsPreference['jsonIpSubnetAccessAccept']) {
			$flag = $classCheck->ipRange(array(
				'ip'  => $varsMedia['ip'],
				'arr' => $varsPreference['jsonIpSubnetAccessAccept']
			));
			if (!$flag) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
				}
				$this->_send404();
				exit;
			}
		}

		if ($varsPreference['jsonIpAccessAccept']) {
			$flagIp = 0;
			$array = $varsPreference['jsonIpAccessAccept'];
			foreach ($array as $key => $value) {
				$flag = $classCheck->checkValueFormat(array(
					'flagType' => 'ip',
					'value'    => $value,
				));
				if (!$flag) {
					$flagIp = $classCheck->ipRange(array(
						'ip'  => $varsMedia['ip'],
						'arr' => array($value),
					));

				} else {
					$value = preg_quote($value);
					$value = str_replace('/', '\/', $value);
					if (preg_match("/$value/", $varsMedia['host'])) {
						$flagIp = 1;
					}
				}
				if ($flagIp) {
					break;
				}
			}
			if (!$flagIp) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
				}
				$this->_send404();
				exit;
			}
		}

		if (!$varsPreference['jsonIpAccessReject'] && !$varsPreference['jsonIpSubnetAccessReject']) {
			return;
		}

		$flag = $classCheck->ipRange(array(
			'ip'  => $varsMedia['ip'],
			'arr' => $varsPreference['jsonIpSubnetAccessReject']
		));
		if ($flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
			}
			$this->_send404();
			exit;
		}

		$flagIp = 0;
		$array = $varsPreference['jsonIpAccessReject'];
		foreach ($array as $key => $value) {
			$flag = $classCheck->checkValueFormat(array(
				'flagType' => 'ip',
				'value'    => $value,
			));
			if (!$flag) {
				$flagIp = $classCheck->ipRange(array(
					'ip'  => $varsMedia['ip'],
					'arr' => array($value),
				));

			} else {
				$value = preg_quote($value);
				$value = str_replace('/', '\/', $value);
				if (preg_match("/$value/", $varsMedia['host'])) {
					$flagIp = 1;
				}
			}
			if ($flagIp) {
				break;
			}
		}
		if ($flagIp) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
			}
			$this->_send404();
			exit;
		}
	}

	/**
     *
     */
	protected function _send404()
	{
		/*
		global $varsPreference;

		$dummy = 'dummy.php';
		$path =  $varsPreference['strTopUrl'] . $dummy;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $path);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$output = curl_exec($ch);
		curl_close($ch);

		$temp = $_SERVER['REQUEST_URI'];
		$array = preg_split("/\//", $temp);
		$strEnd = end($array);
		$output = str_replace($dummy, $strEnd, $output);
		header("HTTP/1.0 404 Not Found");
		print $output;
		*/
		header("HTTP/1.0 404 Not Found");
		exit;
	}

	/**
     *
     */
	protected function _checkAPI()
	{
		global $varsAccounts;
		global $varsRequest;

		$this->_checkRejectIp();

		$varsApiAccount = $this->_checkVarsApiAccount();

		$flag = $this->_checkVarsAPISession(array(
			'varsApiAccount' => $varsApiAccount
		));
		if (!$flag) {
			$this->_checkMaintenance(array('flagAPI' => 1));
			//maintenance

			global $varsAccount;

			$varsAccount = $varsAccounts[$varsApiAccount['idAccount']];

			//global $varsRequest make
			$varsRequest['query']['ext'] = 'API';
			$varsRequest['query']['db'] = 'slave';
			$varsRequest['query']['module'] = 'Accounting';
			$varsRequest['query']['class'] = 'Plugin';
			if (ucwords($varsRequest['query']['api']['module']) == 'Base') {
				$varsRequest['query']['module'] = ucwords($varsRequest['query']['api']['module']);
				$varsRequest['query']['class'] = 'Core';
			}
			return;
		}

		$this->_sendSession(array(
			'varsApiAccount' => $varsApiAccount
		));

		$this->_sendSessionError();
	}

	/**
     *
     */
	protected function _sendSessionError()
	{
		global $classRequest;

		$temp = array(
			'flag' => 'sessionError',
		);
		$json = json_encode($temp);

		$classRequest->send(array(
			'flagType' => 'json',
			'data'     => $json,
		));
		exit;
	}

	/**
     *
     */
	protected function _checkVarsApiAccount()
	{
		global $varsAccounts;
		global $varsMedia;
		global $varsApiAccounts;
		global $varsApiAccount;

		if (!$varsApiAccounts) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
			}
			exit;
		}

		$arrStrSiteUrl = array();
		$idAccount = '';
		$array = $varsApiAccounts[$varsMedia['ip']];
		if (!$array) {
			$array = array();
		}
		foreach ($array as $key => $value) {
			$arrStrSiteUrl[] = $value['strSiteUrl'];
			$idAccount = $value['idAccount'];
		}

		if (!$idAccount) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
			}
			exit;
		}

		if (!$varsAccounts[$idAccount]) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
			}
			exit;
		}

		$varsApiAccount = array(
			'arrStrSiteUrl' => $arrStrSiteUrl,
			'idAccount'     => $idAccount,
		);

		return $varsApiAccount;
	}

	/**
     *
     */
	protected function _checkVarsAPISession($arr)
	{
		global $varsRequest;
		global $varsSession;
		global $varsMedia;

		$idAccount = $arr['varsApiAccount']['idAccount'];

		$session = $varsRequest['query']['api']['session'];
		if (is_null($session)) {
			return __LINE__;
		}

		if (is_null($varsSession[$session])) {
			return __LINE__;
		}

		if ($varsMedia['ip'] != $varsSession[$session]['ip']) {
			return __LINE__;
		}

		$stampLimit = TIMESTAMP - NUM_SESSION;
		if ($stampLimit > $varsSession[$session]['stampRegister']) {
			return __LINE__;
		}

		if ($varsSession[$session]['idAccount'] != $idAccount) {
			return __LINE__;
		}
	}

	/**
	 *
	 */
	protected function _sendSession()
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $classDisplay;

		global $varsMedia;
		global $varsSession;
		global $varsPreference;
		global $varsApiAccount;
		global $varsAccounts;
		$classTime = new Code_Else_Lib_Time();

		$stampRegister = TIMESTAMP;
		$flagAPI = 1;
		$idAccount = $varsApiAccount['idAccount'];
		$ip = $varsMedia['ip'];
		$varsAccount = $varsAccounts[$varsApiAccount['idAccount']];

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

		$pathVars = str_replace('<strLang>', $varsAccount['strLang'], $this->_self['pathVarsLogin']);
		$pathTpl = str_replace('<strLang>', $varsAccount['strLang'], $this->_self['pathTplLogin']);
		$classTime->setTimeZone(array('data' => $varsAccount['numTimeZone']));
		$strTime = $classTime->getDisplay(array(
			'stamp'    => TIMESTAMP,
			'flagType' => 'year-sec',
		));

		if ($varsPreference['flagLoginMail']) {
			$this->_sendMailAdmin(array(
				'pathVars' => $pathVars,
				'pathTpl'  => $pathTpl,
				'arrValue' => array(
					'strName'     => $varsPreference['strSiteName'],
					'strUrl'      => $varsPreference['strSiteUrl'],
					'id'          => $varsAccount['id'],
					'strCodeName' => $varsAccount['strCodeName'],
					'strIp'       => $varsMedia['ip'],
					'strHost'     => $varsMedia['host'],
					'strTime'     => $strTime,
				),
			));
		}
		if ($varsAccount['flagLoginMail']) {
			$this->_sendMailAccount(array(
				'pathVars' => $pathVars,
				'pathTpl'  => $pathTpl,
				'strMail'  => $varsAccount['strMailPc'],
				'arrValue' => array(
					'strName'     => $varsPreference['strSiteName'],
					'strUrl'      => $varsPreference['strSiteUrl'],
					'id'          => $varsAccount['id'],
					'strCodeName' => $varsAccount['strCodeName'],
					'strIp'       => $varsMedia['ip'],
					'strHost'     => $varsMedia['host'],
					'strTime'     => $strTime,
				),
			));
		}


	}

	/**
     *
     */
	protected function _sendMailAccount($arr)
	{
		global $classMail;

		global $varsAccounts;
		global $varsModule;
		global $varsPreference;

		$array = $varsAccounts;

		if ($varsPreference['flagLoginMail']) {
			foreach ($array as $key => $value) {
				if ($value['flagWebmaster']) {
					if ($value['strMailPc'] == $arr['strMail']) {
						return;
					}
				}
				$id = $value['idModule'];
				$data = $varsModule[$id]['arrCommaIdModuleAdmin'];
				if (preg_match( "/,base,/", $data)) {
					if ($value['strMailPc'] == $arr['strMail']) {
						return;
					}
				}
			}
		}

		$array = array($arr['strMail']);
		foreach ($array as $key => $value) {
			$flag = $classMail->setMail(array(
				'pathVars'    => $arr['pathVars'],
				'pathTpl'     => $arr['pathTpl'],
				'arrValue'    => $arr['arrValue'],
				'mailTo'      => $value,
				'arrMailBcc'  => array(),
				'arrMailCc'   => array(),
				'mailFrom'    => $varsPreference['strSiteMailPc'],
				'strNameFrom' => $varsPreference['strSiteName'],
			));
		}
	}

	/**
     *
     */
	protected function _sendMailAdmin($arr)
	{
		global $classMail;

		global $varsAccounts;
		global $varsModule;
		global $varsPreference;

		$array = $varsAccounts;
		$arrayNew = array();
        foreach ($array as $key => $value) {
			if ($value['flagWebmaster']) {
				$arrayNew[] = $value['strMailPc'];
				continue;
			}
			$id = $value['idModule'];
			$data = $varsModule[$id]['arrCommaIdModuleAdmin'];
			if (preg_match( "/,base,/", $data)) {
				$arrayNew[] = $value['strMailPc'];
			}
		}

		$array = $arrayNew;
		foreach ($array as $key => $value) {
			$flag = $classMail->setMail(array(
				'pathVars'    => $arr['pathVars'],
				'pathTpl'     => $arr['pathTpl'],
				'arrValue'    => $arr['arrValue'],
				'mailTo'      => $value,
				'arrMailBcc'  => array(),
				'arrMailCc'   => array(),
				'mailFrom'    => $varsPreference['strSiteMailPc'],
				'strNameFrom' => $varsPreference['strSiteName'],
			));
		}
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
     *
     */
	protected function _checkAccount()
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $varsRequest;
		global $varsSession;
		global $varsTerm;

		if (!$varsSession) {
			return;
		}
		$session = $varsRequest['cookie']['id'];
		if (is_null($varsSession[$session])) {
			return;
		}

		global $varsMedia;
		if ($varsMedia['ip'] != $varsSession[$session]['ip']) {
			return;
		}

		$tm = TIMESTAMP;
		$stampLimit = $tm - NUM_SESSION;
		if ($stampLimit > $varsSession[$session]['stampRegister']) {
			return;
		}

		global $varsPreference;
		global $varsAccounts;
		$idAccount = $varsSession[$session]['idAccount'];

		if ($varsAccounts[$idAccount]['flagLock']) {
			return;
		}
		$tm = TIMESTAMP;

		if ($varsTerm) {
			$idTerm = $varsAccounts[$idAccount]['idTerm'];
			$stampStart = $varsTerm[$idTerm]['stampStart'];
			$stampEnd = $varsTerm[$idTerm]['stampEnd'];

			if (!$varsAccounts[$idAccount]['flagWebmaster']) {
				if ($varsTerm[$idTerm]['stampEnd'] == 0) {
					if ($varsTerm[$idTerm]['stampStart'] > $tm) {
						return;
					}
				} else {
					if (!($varsTerm[$idTerm]['stampStart'] < $tm && $tm < $varsTerm[$idTerm]['stampEnd'])) {
						return;
					}
				}
			}
		}

		if ($varsPreference['numPasswordLimit']) {
			$stampUpdateLimit = $varsAccounts[$idAccount]['stampUpdatePassword'] + $varsPreference['numPasswordLimit'] * 86400;
			if ($tm > $stampUpdateLimit) {
				return;
			}
		}

		global $varsAccount;
		$varsAccount = $varsAccounts[$idAccount];
	}



    /**
     *
     */
	protected function _checkMaintenance($arr)
	{
		global $varsPreference;
		global $varsAccount;
		global $varsModule;

		if (!$varsPreference['flagMaintenance']) {
			return;
		}

		if ($varsAccount) {
			if ($varsAccount['flagWebmaster']
				|| preg_match('/,' . $varsAccount['id'] . ',/', $varsPreference['arrCommaIdAccountMaintenance'])
			) {
				return;
			}
		}

		if ($arr['flagAPI']) {
			$this->_showMaintenanceAPI();
		} else {
			$this->_showMaintenance();
		}

	}

	/**
     *
     */
	protected function _showMaintenanceAPI()
	{
		global $classRequest;

		$temp = array(
			'flag' => 'maintenance',
		);
		$json = json_encode($temp);

		$classRequest->send(array(
			'flagType' => 'json',
			'data'     => $json,
		));
		exit;
	}

    /**
     *
     */
	protected function _showMaintenance()
	{
		global $classSmarty;
		global $classRequest;
		global $varsAccount;

		$array = $this->_getVars(array(
			'path'     => $this->_self['pathVarsMaintenance'],
			'strLang'  => STR_SYSTEM_LANG,
		));

		foreach ($array as $key => $value) {
			$classSmarty->assign($key, $value);
		}

		$classSmarty->assign('strLang', STR_SYSTEM_LANG);
		$classSmarty->assign('strCheset', STR_ENCODING);

		$path = $this->_self['pathHtmlMaintenance'];
		$output = $classSmarty->fetch($path);

		$classRequest->send(array(
			'flagType' => 'html',
			'data'     => $output,
		));
		exit;
	}

    /**
     * $arr = array(
     *     'path' => string,
     *     'strLang' => string,
     * )
     */
	protected function _getVars($arr)
	{
		$path = $arr['path'];

		$path = str_replace('<strLang>', $arr['strLang'], $path);
		require $path;

		return $vars;
	}
}
