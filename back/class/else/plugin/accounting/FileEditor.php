<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_FileEditor extends Code_Else_Plugin_Accounting_File
{
	protected $_childSelf = array(
		'pathTplJs'  => 'else/plugin/accounting/js/fileEditor.js',
		'pathVarsJs' => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/fileEditor.php',
		'datPath'    => 'back/dat/file/accounting/',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;

		if ($varsRequest['query']['func']) {
			$method = '_ini' . $varsRequest['query']['func'];
			if (method_exists($this, $method)) {
				$this->$method();

			} else {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
				}
				exit;
			}
		}
		exit;
	}

	/**
	 *
	 */
	protected function _iniJs()
	{
		$this->_setJsEditor(array(
			'pathVars'  => $this->_childSelf['pathVarsJs'],
			'pathTpl'   => $this->_childSelf['pathTplJs'],
			'arrFolder' => array(),
		));
	}

	/**
	 *
	 */
	protected function _iniDetailAdd()
	{
		global $classDb;
		global $varsRequest;
		$dbh = $classDb->getHandle();

		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 0,
			'flagAuthority' => 'insert',
			'idTarget'      => $this->_extSelf['idPreference'],
		));

		if (!$flag) {
			$this->_sendVarsIframe(array(
				'flag'    => 40,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());

		$arrFile = $this->_checkValueAdd();
		$arrLogFile = array();
		try {
			$dbh->beginTransaction();

			$array = &$arrFile;
			foreach ($array as $key => $value) {
				$varsIdNumber = $this->_getIdAutoIncrement(array(
					'idTarget' => 'idLogFile'
				));
				if (!$varsIdNumber[$idEntity]) {
					$varsIdNumber[$idEntity] = 1;
				}
				$idLogFile = $varsIdNumber[$idEntity];


				$arrSql = $this->_updateDbValueAdd(array(
					'arr'          => $value,
					'varsIdNumber' => $varsIdNumber,
				));

				$arrLogFile[] = array('strTitle' => $arrSql['strTitle'], 'idLogFile' => $idLogFile);

				$classDb->insertRow(array(
					'idModule'  => 'accounting',
					'strTable'  => 'accountingLogFile',
					'arrColumn' => $arrSql['arrColumn'],
					'arrValue'  => $arrSql['arrValue'],
				));

				$varsIdNumber[$idEntity]++;
				$this->_updateIdAutoIncrement(array(
					'idTarget'   => 'idLogFile',
					'varsTarget' => $varsIdNumber
				));

				if (preg_match("/^tempPrev$/", $flagCurrentFlagNow)) {
					$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
					$numFiscalPeriodTemp = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];
					$arrayColumn = $arrSql['arrColumn'];
					foreach ($arrayColumn as $keyColumn => $valueColumn) {
						if ($valueColumn == 'numFiscalPeriod') {
							$arrSql['arrValue'][$keyColumn] = $numFiscalPeriodTemp;
							break;
						}
					}

					$classDb->insertRow(array(
						'idModule'  => 'accounting',
						'strTable'  => 'accountingLogFile',
						'arrColumn' => $arrSql['arrColumn'],
						'arrValue'  => $arrSql['arrValue'],
					));
				}
			}

			$this->_updateDbPreferenceStamp(array('strColumn' => 'logFile'));
			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}
		$this->_sendVarsIframe(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array('arrLogFile' => $arrLogFile),
		));
	}

	protected function _sendFile($tmp, $data)
	{
		$fp = fopen ('error' . $tmp . '.cgi', "w") or die;
		flock($fp, LOCK_EX);
		fputs($fp, $data);
		flock($fp, LOCK_UN);
		fclose ($fp);
		exit;
	}

	/**

	 */
	protected function _checkValueAdd()
	{
		global $classCheck;
		global $classEscape;
		global $varsRequest;
		global $varsPluginAccountingPreference;
		global $varsPluginAccountingAccount;

		$idAccount = $varsAccount['id'];
		$arrSpaceStrTag = $varsRequest['query']['ArrSpaceStrTag'];

		if (!$this->_checkCurrent()) {
			$this->_sendVarsIframe(array(
				'flag'    => 40,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}

		$varsAuthority = $this->_getVarsAuthority(array());
		if	(is_null($varsAuthority)) {
			$this->_sendVarsIframe(array(
				'flag'    => 40,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}

		$this->_checkValueElseTag();

		$arrFile = $this->_checkValueFile();
		$id = $varsRequest['query']['idTag'];

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$numYear = date('Y');
		$numMonth = date('m');

		$path = PATH_BACK_DAT_FILE . 'accounting/';
		if (!is_dir($path)) {
			mkdir($path);
		}
		$path = PATH_BACK_DAT_FILE . 'accounting/' . $idEntity;
		if (!is_dir($path)) {
			mkdir($path);
		}
		$strCurrent = '/' . $numFiscalPeriod . '-' . $numYear . $numMonth;
		$path .= $strCurrent;
		if (!is_dir($path)) {
			mkdir($path);
		}

		$array = &$arrFile;
		foreach ($array as $key => $value) {
			$strFileName = hash('sha256', $value['strTitle'] . '_' . $key . '_' . MICROTIMESTAMP). '.' . $value['strFileType'] . '.cgi';
			$strFile = '/' . $strFileName;
			$strUrl = $path . $strFile;
			if (!move_uploaded_file($value['tmpName'], $strUrl)) {
				$this->_sendVars(array(
					'flagIframe' => 1,
					'flag'       => 'strError',
					'stamp'      => $this->getStamp(),
					'numNews'    => $this->getNumNews(),
					'vars'       => array(),
				));
			}

			$strUrl = $this->_childSelf['datPath'] . $idEntity . $strCurrent . $strFile;

			$numWidth = 0;
			$numHeight = 0;
			if (preg_match("/^(png|jpeg|jpg|gif|bmp)$/i", $value['strFileType'])) {
				list($numWidth, $numHeight) = getimagesize($strUrl);
			}
			$array[$key]['arrSpaceStrTag'] = $arrSpaceStrTag;
			$array[$key]['numWidth'] = $numWidth;
			$array[$key]['numHeight'] = $numHeight;
			$array[$key]['strUrl'] = $strUrl;
		}

		return $array;
	}

	/**
		$this->_getStrTitle(array(
			'strTitle'        => $arr['strTitle'],
				'idEntity'        => $arr['idEntity'],
				'numFiscalPeriod' => $arr['numFiscalPeriod'],
		))
	 */
	protected function _getStrTitle($arr)
	{
		$strTitle = $arr['strTitle'];

		$flag = true;
		$flagError = 0;
		$num = 1;
		$numLimit = 95;
		while ($flag) {
			$flagCheck = $this->_checkStrTitleAdd(array('strTitle' => $strTitle,));
			if ($flagCheck) {
				$strTitle = mb_substr($arr['strTitle'], 0, $numLimit);
				$strTitle = $strTitle . '(' . $num . ')';
				if (mb_strlen($strTitle) > 100) {
					$numLimit -= 1;
					$strTitle = mb_substr($arr['strTitle'], 0, $numLimit);
					$strTitle = $strTitle . '(' . $num . ')';
				}
				if ($numLimit <= 0) {
					$flagError = 1;
				}
				$num++;
			} else {
				return $strTitle;
			}
		}
		if ($flagError) {
			return MICROTIMESTAMP;
		}
		return $strTitle;
	}

	/**
		(array(
			'strTitle' => '',
			'idTarget' => 0,
		))
	 */
	protected function _checkStrTitleAdd($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$arrWhere = array(
			array(
				'flagType'      => 'num',
				'strColumn'     => 'idEntity',
				'flagCondition' => 'eq',
				'value'         => $varsPluginAccountingAccount['idEntityCurrent'],
			),
			array(
				'flagType'      => '',
				'strColumn'     => 'strTitle',
				'flagCondition' => 'eq',
				'value'         => $arr['strTitle'],
			),
		);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogFile',
			'arrLimit' => array(
				'numStart' => 0, 'numEnd' => 1,
			),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => $arrWhere,
		));

		if ($rows['numRows']) {
			return true;
		}
		return false;
	}

	/**
		array(
			'flagIframe' => int,
			'flag'       => mix,
			'stamp'      => array,
			'numNews'    => int,
			'vars'       => array,
		)
	 */
	protected function _sendVars($arr)
	{
		global $varsRequest;

		if (!$arr['flagIframe']) {
			$this->sendVars(array(
				'flag'    => 'strSize',
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));

		} else {
			$array = array(
				'flag'    => (!is_null($arr['flag']))? $arr['flag'] : 1,
				'numNews' => ($arr['numNews'])? $arr['numNews'] : 0,
				'stamp'   => ($arr['stamp'])? $arr['stamp'] : '',
				'data'    => $arr['vars'],
			);

			$jsonVars = json_encode($array);
			$jsonIdUpload = json_encode($varsRequest['query']['idUpload']);

			$varsIframe = $this->getVars(array(
				'path' => $this->_extSelf['varsIframe'],
			));
			$tmplIframe = str_replace('<%idUpload%>', $jsonIdUpload, $varsIframe['tmpl']);
			$tmplIframe = str_replace('<%vars%>', $jsonVars, $tmplIframe);

			print $tmplIframe;
			exit;

		}
	}


	/**

	 */
	protected function _checkValueFile()
	{
		global $classEscape;
		global $varsRequest;
		global $varsPluginAccountingPreference;

		$varsPreference = $this->_getVarsPreference(array());
		$id = $varsRequest['query']['idTag'];

		$arrFile = array();
		$array = $_FILES[$id]['name'];
		foreach ($array as $key => $value) {
			$value = $classEscape->to(array( 'data' => $value ));
			$strFileType = strtolower($classEscape->getFileType(array('strUrl' => $value)));
			if (!$varsPreference['jsonFileType'][$strFileType]) {
				$this->_sendVars(array(
					'flagIframe' => 1,
					'flag'       => 'strError',
					'stamp'      => $this->getStamp(),
					'numNews'    => $this->getNumNews(),
					'vars'       => array(),
				));
			}
			$strFileType = preg_quote($strFileType);
			$strFileType = str_replace('/', '\/', $strFileType);
			preg_match("/^(.*?)\.$strFileType$/i", $value, $arrMatch);
			list($dummy, $strTitle) = $arrMatch;
			$numByte = $_FILES[$id]['size'][$key];
			if ($numByte > NUM_MAX_UPLOAD_SIZE) {
				$this->_sendVars(array(
					'flagIframe' => 1,
					'flag'       => 'strSize',
					'stamp'      => $this->getStamp(),
					'numNews'    => $this->getNumNews(),
					'vars'       => array(),
				));
			}
			if ($_FILES[$id]['error'][$key]) {
				$this->_sendVars(array(
					'flagIframe' => 1,
					'flag'       => 'strError',
					'stamp'      => $this->getStamp(),
					'numNews'    => $this->getNumNews(),
					'vars'       => array(),
				));
			}
			$data = array(
				'strTitle'    => $strTitle,
				'tmpName'     => $_FILES[$id]['tmp_name'][$key],
				'strFileType' => $strFileType,
				'numByte'     => $numByte,
			);
			$arrFile[] = $data;
		}

		return $arrFile;
	}

	/**

	 */
	protected function _checkValueElse()
	{
		global $classCheck;
		global $varsRequest;

		$strTitle = $varsRequest['query']['StrTitle'];
		$flag = $classCheck->checkValueBlank(array(
			'flagType' => 'empty',
			'flagArr'  => 0,
			'value'    => $strTitle
		));
		if	($flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
			}
			exit;
		}
		$flag = $classCheck->checkValueMax(array(
			'flagType' => 'str',
			'value'    => $strTitle,
			'flagArr'  => 0,
			'num'      => 100
		));
		if	($flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
			}
			exit;
		}
		$this->_checkValueElseTag();
	}

	/**

	 */
	protected function _checkValueElseTag()
	{
		global $classCheck;
		global $varsRequest;

		$flag = $classCheck->checkValueMax(array(
			'flagType' => 'str',
			'value'    => $varsRequest['query']['ArrSpaceStrTag'],
			'flagArr'  => 0,
			'num'      => 1000
		));
		if	($flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__ . '/' .__LINE__);
			}
			exit;
		}
	}


	/**
		'arr'          => $arrValue['arr'],
		'varsIdNumber' => $varsIdNumber,
	 */
	protected function _updateDbValueAdd($arr)
	{
		global $classEscape;
		global $varsPluginAccountingAccount;
		global $varsAccount;

		$tm = TIMESTAMP;
		$stampRegister = $tm;
		$stampUpdate = $tm;
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$idAccount = $varsAccount['id'];
		$idAccountUpload = $idAccount;
		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arr['arr']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));
		$strTitle = $this->_getStrTitle(array(
			'strTitle' => $arr['arr']['strTitle'],
		));

		$strUrl = $arr['arr']['strUrl'];
		$strFileType = $arr['arr']['strFileType'];
		$numByte = $arr['arr']['numByte'];
		$numWidth = $arr['arr']['numWidth'];
		$numHeight = $arr['arr']['numHeight'];

		$arrVersion = array(
			array(
				'stampRegister'  => $stampRegister,
				'stampUpdate'    => $stampUpdate,
				'strTitle'       => $strTitle,
				'numByte'        => $numByte,
				'numWidth'       => $numWidth,
				'numHeight'      => $numHeight,
				'strUrl'         => $strUrl,
				'strFileType'    => $strFileType,
				'arrSpaceStrTag' => $arrSpaceStrTag,
			),
		);
		$jsonVersion = json_encode($arrVersion);
		$this->checkTextSize(array(
			'flag' => 'errorDataMax',
			'str'  => $jsonVersion,
		));

		$arrChargeHistory = array(
			array(
				'stampRegister'  => $stampRegister,
				'idAccount'      => $idAccount,
			),
		);
		$jsonChargeHistory = json_encode($arrChargeHistory);
		$this->checkTextSize(array(
			'flag' => 'errorDataMax',
			'str'  => $jsonChargeHistory,
		));

		$idLogFile = $arr['varsIdNumber'][$idEntity];

		$flagRemove = 0;
		$data = array(
			'strTitle'  => $strTitle,
			'arrColumn' => array('stampRegister', 'stampUpdate', 'idLogFile', 'idAccount', 'idEntity', 'numFiscalPeriod', 'strTitle', 'numByte', 'numWidth', 'numHeight', 'strUrl', 'strFileType', 'arrSpaceStrTag', 'jsonVersion', 'idAccountUpload', 'jsonChargeHistory', 'flagRemove'),
			'arrValue'  => array($stampRegister, $stampUpdate, $idLogFile, $idAccount, $idEntity, $numFiscalPeriod, $strTitle, $numByte, $numWidth, $numHeight, $strUrl, $strFileType, $arrSpaceStrTag, $jsonVersion, $idAccountUpload, $jsonChargeHistory, $flagRemove),
		);

		return $data;
	}


	/**
	 *
	 */
	protected function _iniDetailEditCharge()
	{
		global $varsRequest;

		$varsRequest['query']['IdAccountCharge'] = $varsRequest['query']['jsonValue']['vars']['IdAccountCharge'];
		$this->_iniDetailEdit();
	}

	/**
	 *
	 */
	protected function _iniDetailEdit()
	{
		global $classDb;
		global $varsRequest;
		$dbh = $classDb->getHandle();
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 0,
			'flagAuthority' => 'update',
			'idTarget'      => $this->_extSelf['idPreference'],
		));

		if (!$flag) {
			$this->_sendVarsIframe(array(
				'flag'    => 40,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsLog = $this->_getLogFile(array(
			'idTarget'        => $varsRequest['query']['jsonValue']['idTarget'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagRemove'      => 0,
		));

		$varsAuthority = $this->_getVarsAuthority(array());

		$arrValue = $this->_checkValueEdit(array(
			'varsLog'       => $varsLog,
			'varsAuthority' => $varsAuthority,
		));

		$arrSql = $this->_updateDbValueEdit(array(
			'arr'           => $arrValue['arr'],
			'varsLog'       => $varsLog,
			'varsAuthority' => $varsAuthority,
		));

		try {
			$dbh->beginTransaction();

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable'  => 'accountingLogFile',
				'arrColumn' => $arrSql['arrColumn'],
				'flagAnd'  => 1,
				'arrWhere'  => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idEntity',
						'flagCondition' => 'eq',
						'value'         => $varsPluginAccountingAccount['idEntityCurrent'],
					),
					array(
						'flagType'      => 'num',
						'strColumn'     => 'numFiscalPeriod',
						'flagCondition' => 'eq',
						'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					),
					array(
						'flagType'      => 'num',
						'strColumn'     => 'idLogFile',
						'flagCondition' => 'eq',
						'value'         => $varsRequest['query']['jsonValue']['idTarget'],
					),
				),
				'arrValue'  => $arrSql['arrValue'],
			));
			$this->_updateDbPreferenceStamp(array('strColumn' => 'logFile'));

			$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
			if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
				$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
				$numFiscalPeriodTemp = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];
				$classDb->updateRow(array(
					'idModule'  => 'accounting',
					'strTable'  => 'accountingLogFile',
					'arrColumn' => $arrSql['arrColumn'],
					'flagAnd'  => 1,
					'arrWhere'  => array(
						array(
							'flagType'      => 'num',
							'strColumn'     => 'idEntity',
							'flagCondition' => 'eq',
							'value'         => $varsPluginAccountingAccount['idEntityCurrent'],
						),
						array(
							'flagType'      => 'num',
							'strColumn'     => 'numFiscalPeriod',
							'flagCondition' => 'eq',
							'value'         => $numFiscalPeriodTemp,
						),
						array(
							'flagType'      => 'num',
							'strColumn'     => 'idLogFile',
							'flagCondition' => 'eq',
							'value'         => $varsRequest['query']['jsonValue']['idTarget'],
						),
					),
					'arrValue'  => $arrSql['arrValue'],
				));
			}

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$this->_sendVarsIframe(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => array(
				'idTarget' => $varsRequest['query']['jsonValue']['idTarget']
			),
		));

	}



	/**
		(array(
			'arr'           => $arrValue['arr'],
			'varsLog'       => $varsLog,
			'flagCurrent'   => $flagCurrent,
			'varsAuthority' => $varsAuthority,
		))
	 */
	protected function _checkValueEdit($arr)
	{
		global $classCheck;
		global $classEscape;
		global $varsRequest;
		global $varsPluginAccountingPreference;
		global $varsPluginAccountingAccount;
		global $varsAccounts;
		global $varsPluginAccountingEntity;

		if (!$this->_checkCurrent()) {
			$this->_sendVarsIframe(array(
				'flag'    => 40,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}

		$varsAuthority = $arr['varsAuthority'];
		if	(is_null($varsAuthority)) {
			$this->_sendVarsIframe(array(
				'flag'    => 40,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}

		$varsLog = $arr['varsLog'];
		if (!$varsLog) {
			$this->_sendVarsIframe(array(
				'flag'    => 40,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(),
			));
		}


		$strTitle = $varsRequest['query']['StrTitle'];
		$arrSpaceStrTag = $varsRequest['query']['ArrSpaceStrTag'];

		$this->_checkValueElse();

		$this->_checkStrTitle(array(
			'flagIframe'      => 1,
			'strTitle'        => $strTitle,
			'idTarget'        => $varsRequest['query']['jsonValue']['idTarget'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
			$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
			$numFiscalPeriodTemp = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];
			$this->_checkStrTitle(array(
				'flagIframe'      => 1,
				'strTitle'        => $strTitle,
				'idTarget'        => $varsRequest['query']['jsonValue']['idTarget'],
				'numFiscalPeriod' => $numFiscalPeriodTemp,
				'flagTempNext'    => 1,
			));
		}

		$strUrl = '';
		$numByte = '';
		$strFileType = '';
		$idAccountCharge = 0;
		$numWidth = 0;
		$numHeight = 0;

		if ((int) $varsRequest['query']['IdAccountCharge']) {
			$idAccountCharge = $this->_checkIdAccountCharge(array('varsLog' => $varsLog));
		}

		$data = array(
			'arr' => array(
				'strTitle'        => $strTitle,
				'arrSpaceStrTag'  => $arrSpaceStrTag,
				'idAccount'       => $idAccountCharge,
			),
		);

		return $data;
	}

	/**
		(array(
			'flagIframe' => 1
			'strTitle' => '',
			'idTarget' => 0,
		))
	 */
	protected function _checkStrTitle($arr)
	{
		global $classDb;
		global $varsRequest;

		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		$arrWhere = array(
			array(
				'flagType'      => 'num',
				'strColumn'     => 'idEntity',
				'flagCondition' => 'eq',
				'value'         => $varsPluginAccountingAccount['idEntityCurrent'],
			),
			array(
				'flagType'      => '',
				'strColumn'     => 'strTitle',
				'flagCondition' => 'eq',
				'value'         => $arr['strTitle'],
			),
		);

		//because allot temp error now error
		if ($arr['flagTempNext']) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'numFiscalPeriod',
				'flagCondition' => 'eq',
				'value'         => $arr['numFiscalPeriod'],
			);

		} else {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'numFiscalPeriod',
				'flagCondition' => 'eqSmall',
				'value'         => $arr['numFiscalPeriod'],
			);
		}

		if ($arr['idTarget']) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'idLogFile',
				'flagCondition' => 'ne',
				'value'         => $arr['idTarget'],
			);

		}

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogFile',
			'arrLimit' => array(
				'numStart' => 0, 'numEnd' => 1,
			),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => $arrWhere,
		));

		if ($rows['numRows']) {
			if ($arr['flagTempNext']) {
				$this->_sendVars(array(
					'flagIframe' => $arr['flagIframe'],
					'flag'       => 'strTitleTempNext',
					'stamp'      => $this->getStamp(),
					'numNews'    => $this->getNumNews(),
					'vars'       => array(),
				));

			} else {
				$this->_sendVars(array(
					'flagIframe' => $arr['flagIframe'],
					'flag'       => 'strTitle',
					'stamp'      => $this->getStamp(),
					'numNews'    => $this->getNumNews(),
					'vars'       => array(),
				));
			}

		}

	}

	/**
		(array(
			'varsLog' => array(),
		))
	*/
	protected function _checkIdAccountCharge($arr)
	{
		global $varsRequest;
		global $varsAccounts;

		$varsLog = $arr['varsLog'];
		$idAccountCharge = $varsRequest['query']['IdAccountCharge'];
		if (!$varsAccounts[$idAccountCharge]) {
			$this->_sendVars(array(
				'flagIframe' => 1,
				'flag'       => 'strRemove',
				'stamp'      => $this->getStamp(),
				'numNews'    => $this->getNumNews(),
				'vars'       => array(),
			));

		} elseif ($varsLog['idAccount'] == $idAccountCharge) {
			$this->_sendVars(array(
				'flagIframe' => 1,
				'flag'       => 'strSame',
				'stamp'      => $this->getStamp(),
				'numNews'    => $this->getNumNews(),
				'vars'       => array(),
			));
		}

		return $idAccountCharge;

	}

	/**
		(array(
			'arr'           => $arrValue['arr'],
			'varsLog'       => $varsLog,
			'varsAuthority' => $varsAuthority,
		))
	 */
	protected function _updateDbValueEdit($arr)
	{
		global $classEscape;
		global $varsPluginAccountingAccount;
		global $varsAccount;

		$varsAuthority = $arr['varsAuthority'];

		$arrSql = array(
			'arrColumn' => array(),
			'arrValue' => array(),
		);

		$tm = TIMESTAMP;
		$stampRegister = $tm;
		$stampUpdate = $tm;
		$arrVersion = $arr['varsLog']['jsonVersion'];
		$dataVersion = array(
			'stampRegister' => $arrVersion[0]['stampRegister'],
			'stampUpdate'   => $stampUpdate,
		);
		$arrayCheck = array();

		//strTitle
		$arrSql['arrColumn'][] = 'strTitle';
		$arrSql['arrValue'][] = $arr['arr']['strTitle'];

		if ($arr['varsLog']['strTitle'] != $arr['arr']['strTitle']) {
			$dataVersion['strTitle'] = $arr['arr']['strTitle'];
			$arrayCheck['strTitle'] = 1;

		} else {
			$dataVersion['strTitle'] = $arr['varsLog']['strTitle'];
		}

		//strUrl
		$dataVersion['strUrl'] = $arr['varsLog']['strUrl'];
		$dataVersion['strFileType'] = $arr['varsLog']['strFileType'];
		$dataVersion['numByte'] = $arr['varsLog']['numByte'];

		//arrSpaceStrTag
		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arr['arr']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));
		$arrSql['arrColumn'][] = 'arrSpaceStrTag';
		$arrSql['arrValue'][] = $arrSpaceStrTag;
		if ($arr['varsLog']['arrSpaceStrTag'] != $arrSpaceStrTag) {
			$dataVersion['arrSpaceStrTag'] = $arrSpaceStrTag;
			$arrayCheck['arrSpaceStrTag'] = 1;

		} else {
			$dataVersion['arrSpaceStrTag'] = $arr['varsLog']['arrSpaceStrTag'];
		}

		if ($arrayCheck['strTitle']
			|| $arrayCheck['arrSpaceStrTag']
		) {
			//jsonVersion
			$arrVersion[] = $dataVersion;
			$jsonVersion = json_encode($arrVersion);
			$this->checkTextSize(array(
				'flag' => 'errorDataMax',
				'str'  => $jsonVersion,
			));
			$arrSql['arrColumn'][] = 'jsonVersion';
			$arrSql['arrValue'][] = $jsonVersion;

		}

		//idAccountCharge
		if ($arr['arr']['idAccount']) {
			$arrSql['arrColumn'][] = 'idAccount';
			$arrSql['arrValue'][] = $arr['arr']['idAccount'];

			//jsonChargeHistory
			$data = array(
				'stampRegister' => $stampRegister,
				'idAccount'     => $arr['arr']['idAccount'],
			);
			$arr['varsLog']['jsonChargeHistory'][] = $data;
			$jsonChargeHistory = json_encode($arr['varsLog']['jsonChargeHistory']);
			$this->checkTextSize(array(
				'flag' => 'errorDataMax',
				'str'  => $jsonChargeHistory,
			));
			$arrSql['arrColumn'][] = 'jsonChargeHistory';
			$arrSql['arrValue'][] = $jsonChargeHistory;
		}

		return $arrSql;
	}
}
