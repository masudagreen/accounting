<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Base_Rebuild extends Code_Else_Lib_Rebuild
{
	protected $_extSelf = array(
		'path' => array(
			'dir' => array(
				'tplCss'     => 'back/tpl/templates/else/core/base/css/',
				'tplCssLib'  => 'back/tpl/templates/else/core/base/css/lib/',
				'langJs'     => 'back/tpl/vars/else/core/base/',
				'tplJsLib'   => 'else/core/base/js/lib/',
				'varsJsLib'  => 'back/tpl/vars/else/core/base/<strLang>/js/lib/',
				'strHoliday' => 'back/tpl/vars/else/core/base/<strLang>/js/lib/holiday/',
			),
			'file' => array(
				'outCss'     => 'front/else/core/base/css/style.css',
				'outCssLib'  => 'front/else/lib/css/style.css',
				'tplJsRoot'  => 'else/core/base/js/root.js',
				'varsJsRoot' => 'back/tpl/vars/else/core/base/<strLang>/js/root.php',
				'outJsRoot'  => 'front/else/core/base/js/<strLang>/root.js',
				'outJsLib'   => 'front/else/lib/js/<strLang>/code.js',
				'db'         => 'back/tpl/templates/else/core/base/db/config.php',
				'varsConfig' => 'back/tpl/vars/else/core/base/<strLang>/dat/config.php',
		    ),
		),
		'arrIdModule' => array(),
	);

    function __construct()
    {
    }


    /**
     *
     */
	public function run($arr)
	{
		if ($arr['flagType'] == 'rebuildCss') {
			$this->_iniCss(array());
			$this->_iniCssLib();

		} elseif ($arr['flagType'] == 'rebuildJs') {
			$this->_iniJsRoot();
			$this->_iniJsLib();

		} elseif ($arr['flagType'] == 'rebuildDbTable') {
			return $this->_iniDbTable(array());

		} elseif ($arr['flagType'] == 'rebuildDbInsert') {
			$this->_iniDbInsert($arr);

		}
	}


	/**
	 *
	 */
	protected function _iniCss($arr)
	{
		$this->_setAllCss(array(
			'pathInDir'   => $this->_extSelf['path']['dir']['tplCss'],
			'pathOutFile' => $this->_extSelf['path']['file']['outCss'],
		));
	}

	/**
	 *
	 */
	protected function _iniCssLib()
	{
		$this->_setAllCss(array(
			'pathInDir'   => $this->_extSelf['path']['dir']['tplCssLib'],
			'pathOutFile' => $this->_extSelf['path']['file']['outCssLib'],
		));

	}


	/**
	 *
	 */
	protected function _iniJsRoot()
	{
		$this->_setJsRoot(array(
			'arrLang'  => $this->_getLang(array('path' => $this->_extSelf['path']['dir']['langJs'])),
			'pathTpl'  => $this->_extSelf['path']['file']['tplJsRoot'],
			'pathVars' => $this->_extSelf['path']['file']['varsJsRoot'],
			'pathOut'  => $this->_extSelf['path']['file']['outJsRoot'],
		));
	}

	/**
	 * $arr = array(
	 *	 'arrLang' => array(),
	 *	 'pathTpl' => string,
	 *	 'pathVars' => string,
	 *	 'pathOut' => string,
	 * )
	 */

	protected function _setJsRoot($arr)
	{
		global $classSmarty;
		global $classEscape;
		global $classFile;

		$arrayLang = $arr['arrLang'];
		foreach ($arrayLang as $keyLang => $valueLang) {
			$vars = $this->_getVars(array(
				'path'    => $arr['pathVars'],
				'strLang' => $valueLang,
			));

			$arrayPlugin = scandir(PATH_BACK_CLASS_ELSE_PLUGIN);
			foreach ($arrayPlugin as $keyPlugin => $valuePlugin) {
				if ( preg_match( "/^\.{1,2}$/", $valuePlugin)) {
					continue;
				}
				$strDir = $valuePlugin;
				$strFile = ucwords($valuePlugin);
				$path = PATH_BACK_CLASS_ELSE_PLUGIN . $strDir . '/' . $strFile . '.php';
				if (!file_exists($path)) {
					continue;
				}
				require_once($path);
				$strClass = 'Code_Else_Plugin_' . $strFile . '_' . $strFile;
				$classCall = new $strClass;
				$varsPlugin = $classCall->loop(array(
					'flagType' => 'rebuildJsRoot',
					'strLang'  => $valueLang,
				));
				$vars['varsChoice']['varsDetail'] = array_merge($vars['varsChoice']['varsDetail'], $varsPlugin['varsChoice']);
				$vars['varsWindow'][] = $varsPlugin['varsWindow'];
				$varsPlugin['varsGlobal']['numTop'] = $varsPlugin['varsGlobal']['numTop'];
				$varsPlugin['varsGlobal']['numLeft'] = $varsPlugin['varsGlobal']['numLeft'];
				$vars['varsGlobal']['varsDetail'][] = $varsPlugin['varsGlobal'];
			}

			$json = json_encode($vars);
			$classSmarty->assign('varsLoad', $json);
			$contents = $classSmarty->fetch($arr['pathTpl']);
			if (FLAG_OBFUSCATE) {
				$contents = $classEscape->obfuscate(array( 'data' => $contents) );
			}
			$path = $this->_getPath(array(
				'path'    => $arr['pathOut'],
				'strLang' => $valueLang,
			));
			$classFile->setData(array(
				'path' => $path,
				'data' => $contents,
			));
		}
	}

	/**
	 *
	 */
	protected function _iniJsLib()
	{
		$this->_setJsLib(array(
			'arrLang'        => $this->_getLang(array('path' => $this->_extSelf['path']['dir']['langJs'])),
			'pathDirTpl'     => $this->_extSelf['path']['dir']['tplJsLib'],
			'pathDirVars'    => $this->_extSelf['path']['dir']['varsJsLib'],
			'pathDirHoliday' => $this->_extSelf['path']['dir']['strHoliday'],
			'pathFileOut'    => $this->_extSelf['path']['file']['outJsLib'],
		));
	}


	/**
	 * $arr = array(
	 *	 'arrLang' => array(),
	 *	 'pathDirTpl' => string,
	 *	 'pathDirVars' => string,
	 *	 'pathDirHoliday' => string,
	 *	 'pathFileOut' => string,
	 * )
	 */
	protected  function _setJsLib($arr)
	{
		global $classSmarty;
		global $classEscape;
		global $classFile;

		$arrayLang = $arr['arrLang'];
		foreach ($arrayLang as $keyLang => $valueLang) {
			$contents = '';
			$arrayTpl = scandir(PATH_BACK_TPL_TEMPLATES . $arr['pathDirTpl']);
			foreach ($arrayTpl as $keyTpl => $valueTpl) {
				if ( preg_match( "/^\.{1,2}$/", $valueTpl)) {
					continue;
				}

				$array = preg_split("/\./", $valueTpl);
				$fileName = $array[0];
				$pathVars = $this->_getPath(array(
					'path' => $arr['pathDirVars'] . $array[0] . '.php',
					'strLang' => $valueLang,
				));
				if (file_exists($pathVars)) {
					$vars = $this->_getVars(array(
						'path'    => $pathVars,
						'strLang' => $valueLang,
					));

					if ($valueTpl == 'calenderVars.js') {
						$arrayContentsHoliday = array();
						$path = $this->_getPath(array(
							'path' => $arr['pathDirHoliday'],
							'strLang' => $valueLang,
						));
						$arrayHoliday = scandir($path);

						foreach ($arrayHoliday as $keyHoliday => $valueHoliday) {
							$strFileHoliday = $valueHoliday;
							$pathHolidayFile = $path . $strFileHoliday;

							if ( preg_match( "/^\.{1,2}$/", $strFileHoliday) || is_dir($pathHolidayFile)) {
								continue;
							}

							$varsHoliday = $this->_getVars(array(
								'path'    => $pathHolidayFile,
								'strLang' => '',
							));
							$arrayContentsHoliday[$varsHoliday['id']] = $varsHoliday;

						}
						$vars['varsHoliday'] = $arrayContentsHoliday;
					}
					$json = json_encode($vars);
					$classSmarty->assign('varsLoad', $json);

				}
				$path = $arr['pathDirTpl'] . $valueTpl;
				$contents .= $classSmarty->fetch($path);
			}

			if (FLAG_OBFUSCATE) {
				$contents = $classEscape->obfuscate(array( 'data' => $contents) );
			}

			$path = $this->_getPath(array(
				'path' => $arr['pathFileOut'],
				'strLang' => $valueLang,
			));

			$classFile->setData(array(
				'path' => $path,
				'data' => $contents,
			));
		}
	}

	/**
	 *
	 */
	protected function _iniDbTable($arr)
	{
		return $this->_setDbTable(array(
			'path'  => $this->_extSelf['path']['file']['db'],
		));
	}

	/**
	 *
	 */
	protected function _iniDbInsert($arr)
	{
		$this->_extSelf['arrIdModule'] = $arr['arrIdModule'];
		$this->_setDbInsert(array(
			'path'  => $this->_extSelf['path']['file']['db'],
		));
	}

	/**
	 *
	 */
	protected function _iniDbInsertAccount($arr)
	{
		$this->_extSelf['arrIdModule'] = $arr['arrIdModule'];
		$this->_setDbInsertAccount(array(
			'varsAccount' => $arr['varsAccount'],
			'flagAdmin'   => $arr['flagAdmin'],
		));
	}

	/**
		$this->_setDbInsertAccount(array(
			'varsAccount' => array(),
			'flagAdmin'   => int,
		));
	 */
	protected function _setDbInsertAccount($arr)
	{
		$array = scandir(PATH_BACK_CLASS_ELSE_PLUGIN);
		foreach ($array as $key => $value) {
			if ( preg_match( "/^\.{1,2}$/", $value)) {
				continue;

			}
			if ($this->_extSelf['arrIdModule']) {
				if (!$this->_extSelf['arrIdModule'][$value]) {
					continue;
				}
			}
			$strDir = $value;
			$strFile = ucwords($value);
			$path = PATH_BACK_CLASS_ELSE_PLUGIN . $strDir . '/' . $strFile . '.php';
			if (!file_exists($path)) {
				continue;

			}
			require_once($path);
			$strClass = 'Code_Else_Plugin_' . $strFile . '_' . $strFile;
			$classCall = new $strClass;
			$classCall->loop(array(
				'flagType'    => 'rebuildDbInsertAccount',
				'varsAccount' => $arr['varsAccount'],
				'flagAdmin'   => $arr['flagAdmin'],
			));
		}
	}

	/**
	 *
	 */
	protected function _setDbInsertBasePreference()
	{
		global $varsRequest;
		global $classDb;
		global $classFile;
		$dbh = $classDb->getHandle();

		$vars = $this->_getVars(array(
			'path'    => $this->_extSelf['path']['file']['varsConfig'],
			'strLang' => STR_SYSTEM_LANG,
		));
		$tm = TIMESTAMP;
		$stampRegister = $tm;
		$stampUpdate = $tm;

		$numTimeZone = NUM_SYSTEM_TIME_ZONE;
		$strTopUrl = str_replace('config.php', '', $varsRequest['query']['url']);
		$strSiteName = $vars['strSiteName'];
		$strSiteUrl = $vars['strSiteUrl'];
		$strSiteMailPc = $vars['strMailPc'];
		$jsonModule = json_encode($classFile->getIdModule(array('faglPlugin' => 0)));
		$jsonStampUpdate = json_encode(array());
		$arrVersion = array();
		$arrVersion[NUM_VERSION] = 1;
		$jsonVersion = json_encode($arrVersion);
		$strVersion = NUM_VERSION;

		$stmt = $dbh->prepare('insert into basePreference (stampRegister, stampUpdate, jsonStampUpdate, numTimeZone, strTopUrl, strSiteName, strSiteUrl, strSiteMailPc, jsonModule, jsonVersion, strVersion) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);');
		$stmt->execute(array($stampRegister, $stampUpdate, $jsonStampUpdate, $numTimeZone, $strTopUrl, $strSiteName, $strSiteUrl, $strSiteMailPc, $jsonModule, $jsonVersion, $strVersion));
	}

	/**
	 *
	 */
	protected function _setDbInsertBaseAccount()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$vars = $this->_getVars(array(
			'path'    => $this->_extSelf['path']['file']['varsConfig'],
			'strLang' => STR_SYSTEM_LANG,
		));

		$tm = TIMESTAMP;
		$stampRegister = $tm;
		$stampUpdate = $tm;
		$flagWebmaster = 1;
		$strCodeName = $vars['strCodeName'];
		$idLogin = $vars['idLogin'];
		$strPassword = hash('sha256', $vars['strPassword']);
		$stampUpdatePassword = $tm;
		$strMailPc = $vars['strMailPc'];
		$numTimeZone = NUM_SYSTEM_TIME_ZONE;
		$strLang = STR_SYSTEM_LANG;
		$strHoliday = STR_SYSTEM_HOLIDAY;
		$idTerm = 1;
		$idModule = 1;
		$flagDefault = 1;
		$jsonStampCheck = json_encode(array());

		//baseAccount
		$stmt = $dbh->prepare('insert into baseAccount (stampRegister, stampUpdate, flagWebmaster, strCodeName, idLogin, strPassword, stampUpdatePassword, strMailPc, numTimeZone, strLang, strHoliday, idTerm, idModule, flagDefault, jsonStampCheck) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);');
		$stmt->execute(array($stampRegister, $stampUpdate, $flagWebmaster, $strCodeName, $idLogin, $strPassword, $stampUpdatePassword, $strMailPc, $numTimeZone, $strLang, $strHoliday, $idTerm, $idModule, $flagDefault, $jsonStampCheck));

		$idAccount = 1;

		//baseLoginPassword
		$stmt = $dbh->prepare('insert into baseLoginPassword (stampRegister, idAccount, strPassword) values (?, ?, ?);');
		$stmt->execute(array($stampRegister, $idAccount, $strPassword));

		//memo
		$array = array(
			'jsonTermNaviSearch',
			'jsonModuleNaviSearch',
			'jsonAccountNaviSearch',
			'jsonLogNaviSearch',
			'jsonApiAccountNaviSearch',
		);

		foreach ($array as $key => $value) {
			$flagColumn = $value;
			$stmt = $dbh->prepare('insert into baseAccountMemo (stampRegister, stampUpdate, idAccount, flagColumn) values (?, ?, ?, ?);');
			$stmt->execute(array($stampRegister, $stampUpdate, $idAccount, $flagColumn));
		}


		$stmt = $dbh->prepare('insert into baseLoginIdLogin (stampRegister, idLogin) values (?, ?);');
		$stmt->execute(array($stampRegister, $idLogin));

		$stmt = $dbh->prepare('select * from baseAccount;');
		$stmt->execute(array($idAccount));

		$varsAccount = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			foreach ($row as $key => $value) {
				if (preg_match("/^json/", $key)) {
					$row[$key] = (!is_null($value))? json_decode($value, true) : array();
				}
			}
			$varsAccount = $row;
		}

		$this->_setDbInsertAccount(array(
			'varsAccount' => $varsAccount,
			'flagAdmin'   => 1,
		));
	}

	/**
	 *
	 */
	protected function _setDbInsertBaseTerm()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$vars = $this->_getVars(array(
			'path'    => $this->_extSelf['path']['file']['varsConfig'],
			'strLang' => STR_SYSTEM_LANG,
		));

		$tm = TIMESTAMP;
		$stampRegister = $tm;
		$stampUpdate = $tm;
		$strTitle = $vars['strNoLimit'];
		$stampStart = $tm;
		$stampEnd = 0;
		$arrSpaceStrTag = '';
		$flagDefault = 1;

		$stmt = $dbh->prepare('insert into baseTerm(stampRegister, stampUpdate, strTitle, stampStart, stampEnd, arrSpaceStrTag, flagDefault) values (?, ?, ?, ?, ?, ?, ?);');
		$stmt->execute(array($stampRegister, $stampUpdate, $strTitle, $stampStart, $stampEnd, $arrSpaceStrTag, $flagDefault));

		$strTitle = $vars['strLost'];
		$stampStart = $tm - 86400;
		$stampEnd = $tm - 1;
		$flagDefault = 1;

		$stmt = $dbh->prepare('insert into baseTerm(stampRegister, stampUpdate, strTitle, stampStart, stampEnd, arrSpaceStrTag, flagDefault) values (?, ?, ?, ?, ?, ?, ?);');
		$stmt->execute(array($stampRegister, $stampUpdate, $strTitle, $stampStart, $stampEnd, $arrSpaceStrTag, $flagDefault));
	}

	/**
	 *
	 */
	protected function _setDbInsertBaseModule()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		$vars = $this->_getVars(array(
			'path'    => $this->_extSelf['path']['file']['varsConfig'],
			'strLang' => STR_SYSTEM_LANG,
		));

		$tm = TIMESTAMP;
		$stampRegister = $tm;
		$stampUpdate = $tm;
		$arrCommaIdModuleAdmin = ',base,';
		$arrCommaIdModuleUser = ',base,';
		$strTitle = $vars['strAllModule'];
		$arrSpaceStrTag = '';
		$flagDefault = 1;

		$stmt = $dbh->prepare('insert into baseModule(stampRegister, stampUpdate, arrCommaIdModuleUser, arrCommaIdModuleAdmin, strTitle, arrSpaceStrTag, flagDefault) values (?, ?, ?, ?, ?, ?, ?);');
		$stmt->execute(array($stampRegister, $stampUpdate, $arrCommaIdModuleUser, $arrCommaIdModuleAdmin, $strTitle, $arrSpaceStrTag, $flagDefault));

		$arrCommaIdModuleAdmin = '';
		$arrCommaIdModuleUser = ',base,';
		$strTitle = $vars['strBaseModule'];

		$stmt = $dbh->prepare('insert into baseModule(stampRegister, stampUpdate, arrCommaIdModuleUser, arrCommaIdModuleAdmin, strTitle, arrSpaceStrTag, flagDefault) values (?, ?, ?, ?, ?, ?, ?);');
		$stmt->execute(array($stampRegister, $stampUpdate, $arrCommaIdModuleUser, $arrCommaIdModuleAdmin, $strTitle, $arrSpaceStrTag, $flagDefault));

	}


}
