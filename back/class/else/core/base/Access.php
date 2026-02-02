<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Base_Access
{

	protected $_self = array(
		'pathDirLog' => 'back/dat/log/',
		'pathDirAccess' => 'back/dat/access/',
		'pathVarsMail' => 'back/tpl/vars/else/core/base/<strLang>/mail/accessUnknown.php',
		'pathTplMail' => 'back/tpl/vars/else/core/base/<strLang>/mail/accessUnknown.tpl',
		'status' => array(
			'flagMonth' => 0,
			'flagDate' => 0,
			'flagHour' => 0,
		),
	);

	function __construct($arr = null)
	{
		// $arr = @func_get_arg(0);
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
		$this->_checkRoutine();
		$this->_setLog();
		$this->_setRoutine();
		$this->_checkUnknown();
	}

	/**
	 *
	 */
	protected function _checkUnknown()
	{
		global $varsRequest;
		global $varsPreference;
		global $varsMedia;
		global $classDb;
		global $classFile;

		if (!$varsPreference['flagAccessUnknownMail']) {
			return;
		}

		$dbh = $classDb->getDbhLog();

		$ip = $varsMedia['ip'];
		$stmt = $dbh->prepare('select * from baseAccessUnknown where ip = ?;');
		$stmt->execute(array($ip));
		$num = $stmt->rowCount();

		if ($num) {
			return;
		}

		try {
			$dbh->beginTransaction();

			$stmt = $dbh->prepare('insert into baseAccessUnknown (ip) values (?);');
			$stmt->execute(array($ip));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}
		$dbh = null;

		$this->_sendMailAdmin(array(
			'pathVars' => $this->_self['pathVarsMail'],
			'pathTpl' => $this->_self['pathTplMail'],
		));
	}

	/**
	 *
	 */
	protected function _sendMailAdmin($arr)
	{
		global $classMail;

		global $varsAccounts;
		global $varsModule;
		global $varsMedia;
		global $varsPreference;
		global $classTime;

		$array = $varsAccounts;
		$arrayNew = array();
		foreach ($array as $key => $value) {
			$temp = array();
			$classTime->setTimeZone(array('data' => $value['numTimeZone']));
			$strTime = $classTime->getDisplay(array(
				'stamp' => TIMESTAMP,
				'flagType' => 'year-sec',
			));
			$temp['strTime'] = $strTime;

			if ($value['flagWebmaster']) {
				$temp['strMailPc'] = $value['strMailPc'];
				$arrayNew[] = $temp;
				continue;
			}
			$id = $value['idModule'];
			$data = $varsModule[$id]['arrCommaIdModuleAdmin'];
			if (preg_match("/,base,/", $data)) {
				$temp['strMailPc'] = $value['strMailPc'];
				$arrayNew[] = $temp;
			}
		}

		$array = $arrayNew;
		foreach ($array as $key => $value) {
			$arrValue = array(
				'strName' => $varsPreference['strSiteName'],
				'strUrl' => $varsPreference['strSiteUrl'],
				'strIp' => $varsMedia['ip'],
				'strHost' => $varsMedia['host'],
				'strTime' => $temp['strTime'],
			);

			$flag = $classMail->setMail(array(
				'pathVars' => $arr['pathVars'],
				'pathTpl' => $arr['pathTpl'],
				'arrValue' => $arrValue,
				'mailTo' => $value['strMailPc'],
				'arrMailBcc' => array(),
				'arrMailCc' => array(),
				'mailFrom' => $varsPreference['strSiteMailPc'],
				'strNameFrom' => $varsPreference['strSiteName'],
			));
		}
	}

	/**
	 *
	 */
	protected function _setLog()
	{
		global $varsRequest;
		global $varsPreference;
		global $varsAccount;
		global $varsMedia;
		global $classDb;
		global $classFile;

		$dbh = $classDb->getDbhLog();

		$stampRegister = TIMESTAMP;
		$ip = $varsMedia['ip'];
		$strHost = ($varsMedia['host']) ? $varsMedia['host'] : '';
		$idAccount = ($varsAccount) ? $varsAccount['id'] : 0;
		$strDbType = $classDb->getSelf(array('key' => 'dbtype'));
		$strDevice = $varsMedia['device'];
		$idModule = ($varsRequest['query']['module']) ? $varsRequest['query']['module'] : '';
		$strExt = ($varsRequest['query']['ext']) ? $varsRequest['query']['ext'] : '';
		$strChild = ($varsRequest['query']['child']) ? $varsRequest['query']['child'] : '';
		$strFunc = ($varsRequest['query']['func']) ? $varsRequest['query']['func'] : '';

		$array = $varsRequest['query'];
		/*
		if ($array['jsonValue']) {
			if ($array['jsonValue']['StrPassword']) {
				$value = hash('sha256', $array['jsonValue']['StrPassword']);
				$array['jsonValue']['StrPassword'] = $value;
			}
			if ($array['jsonValue']['StrPasswordConfirm']) {
				$value = hash('sha256', $array['jsonValue']['StrPasswordConfirm']);
				$array['jsonValue']['StrPasswordConfirm'] = $value;
			}
		}
		*/
		unset($array['token']);
		if (FLAG_API) {
			$strFunc = $varsRequest['query']['api']['method'];
			unset($array['api']['session']);
			$array = $array['api'];
		}

		//access log password hide
		//if (!is_null($array['jsonValue']['vars']['StrPassword'])) {
		if ($this->_batchIllegalStringOffset($array['jsonValue']['vars'], 'StrPassword')) {
			$array['jsonValue']['vars']['StrPassword'] = '***';
		}
		if ($this->_batchIllegalStringOffset($array['jsonValue']['vars'], 'StrPasswordConfirm')) {
			//if (!is_null($array['jsonValue']['vars']['StrPasswordConfirm'])) {
			$array['jsonValue']['vars']['StrPasswordConfirm'] = '***';
		}
		$jsonQuery = (count($array)) ? json_encode($array) : '';

		try {
			$dbh->beginTransaction();

			$stmt = $dbh->prepare('insert into baseAccessLog (stampRegister, ip, strHost, idAccount, strDbType, strDevice, idModule, strExt, strChild, strFunc, jsonQuery) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);');
			$stmt->execute(array($stampRegister, $ip, $strHost, $idAccount, $strDbType, $strDevice, $idModule, $strExt, $strChild, $strFunc, $jsonQuery));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}
		$dbh = null;

		global $classTime;

		$classTime->setTimeZone(array('data' => NUM_SYSTEM_TIME_ZONE));
		$fileName = $classTime->getDisplay(array(
			'stamp' => TIMESTAMP,
			'flagType' => 'yearmonth',
		));
		$path = $this->_self['pathDirLog'] . $fileName . '.cgi';

		$classFile->setData(array(
			'data' => '\n',
			'path' => $path,
		));

		$jsonQuery = str_replace("\n", "\\n", $jsonQuery);
		$jsonQuery = str_replace("\t", "\\t", $jsonQuery);

		$data = "$stampRegister\t$ip\t$strHost\t$idAccount\t$strDbType\t$strDevice\t$idModule\t$strChild\t$strExt\t$strFunc\t$jsonQuery\n";
		$path = $this->_self['pathDirAccess'] . $fileName . '.cgi';
		$classFile->addData(array(
			'data' => $data,
			'path' => $path,
		));
	}

	/**
		php5.3 -> php5.4 batch

		batch for IllegalStringOffset

		(
			'data' mix,
			'str' string
		);
	 */
	protected function _batchIllegalStringOffset($data, $str)
	{
		if (is_array($data)) {
			if ($data[$str]) {
				return true;
			}
		}

		return false;
	}

	/**
	 *
	 */
	protected function _checkRoutine($array = array())
	{
		$array['flagMonth'] = $this->_checkUpdate(array('flagType' => 'month', ));
		$array['flagDate'] = $this->_checkUpdate(array('flagType' => 'date', ));
		$array['flagHour'] = $this->_checkUpdate(array('flagType' => 'hour', ));
		if (!$array['flagMonth'] && !$array['flagDate'] && !$array['flagHour']) {
			$array = null;
		}

		$this->_self['status'] = $array;
	}

	/**
	 * $arr = array(
	 * 	'flagType' => string,
	 * )
	 */
	protected function _checkUpdate($arr)
	{
		global $classTime;

		$classTime->setTimeZone(array('data' => NUM_SYSTEM_TIME_ZONE));
		$fileName = $classTime->getDisplay(array(
			'stamp' => TIMESTAMP,
			'flagType' => 'yearmonth',
		));
		$path = $this->_self['pathDirLog'] . $fileName . '.cgi';
		if (!file_exists($path)) {
			return 1;
		}

		$flagNow = $classTime->getDisplay(array(
			'stamp' => TIMESTAMP,
			'flagType' => $arr['flagType'],
		));

		$stat = stat($path);
		$flagLast = $classTime->getDisplay(array(
			'stamp' => $stat[9],
			'flagType' => $arr['flagType'],
		));

		if ($flagNow != $flagLast) {
			return 1;
		}
		return 0;
	}
	/**
	 *
	 */
	public function _setRoutine()
	{
		global $classDb;
		global $classFile;

		if (is_null($this->_self['status'])) {
			return;
		}

		ignore_user_abort(true);
		set_time_limit(0);

		$this->_checkBatch13000();
		$array = $classFile->getCsvRows(array('path' => PATH_BACK_DAT_CONNECT));
		$num = null;
		foreach ($array as $key => $value) {
			if ($array[$key]['dbtype'] == 'master') {
				$num = $key;
				break;
			}
		}

		$classDb->setHandle(array(
			'driver' => $array[$num]['driver'],
			'username' => $array[$num]['username'],
			'password' => $array[$num]['password'],
			'host' => $array[$num]['host'],
			'dbname' => $array[$num]['dbname'],
			'dbtype' => $array[$num]['dbtype'],
		));

		$this->_setCore();
		$this->_setPlugin();
	}

	/**
	 */
	private function _checkBatch13000()
	{
		//for batch version < 1.30.00
		$pathUnder13000 = PATH_BACK_DAT . "db/connect.csv";
		if (file_exists($pathUnder13000)) {
			copy(PATH_BACK_DAT . "db/connect.csv", PATH_BACK_DAT . "db/connect.cgi");
			unlink(PATH_BACK_DAT . "db/connect.csv");
		}
	}

	/**
	 *
	 */
	protected function _setCore()
	{
		$array = scandir(PATH_BACK_CLASS_ELSE_CORE);
		foreach ($array as $key => $value) {
			if (preg_match("/^\.{1,2}$/", $value)) {
				continue;
			}
			$strDir = $value;
			$strFile = ucwords($value);
			$path = PATH_BACK_CLASS_ELSE_CORE . $strDir . '/' . $strFile . '.php';
			if (!file_exists($path)) {
				continue;
			}
			require_once($path);
			$strClass = 'Code_Else_Core_' . $strFile . '_' . $strFile;
			$classCall = new $strClass;
			$classCall->loop(array(
				'flagType' => 'routine',
				'routine' => $this->_self['status'],
			));
		}
	}

	/**
	 *
	 */
	protected function _setPlugin()
	{
		global $varsPreference;

		$array = $varsPreference['jsonModule'];
		foreach ($array as $key => $value) {
			$strDir = $key;
			$strFile = ucwords($key);
			$path = PATH_BACK_CLASS_ELSE_PLUGIN . $strDir . '/' . $strFile . '.php';

			if (!file_exists($path)) {
				continue;
			}

			require_once($path);
			$strClass = 'Code_Else_Plugin_' . $strFile . '_' . $strFile;
			$classCall = new $strClass;
			$classCall->loop(array(
				'flagType' => 'routine',
				'routine' => $this->_self['status'],
			));
		}
	}
}
