<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_CalcLogImportMail extends Code_Else_Plugin_Accounting_Jpn_Jpn
{
	/**

	 */
	public function run()
	{
		if (FLAG_TEST) {
			var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
		}
		exit;
	}

	/*
		(array(

		))
	 * */
	public function allot($arr)
	{
		$method = '_ini' . ucwords($arr['flagStatus']);
		if (method_exists($this, $method)) {
			return $this->$method($arr);

		} else {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}
	}

	/**
		 (array(
			'flagStatus'      => 'check',
			'idEntity'        => $varsPluginAccountingAccount['idEntityCurrent'],
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'pathDir'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		 ))
	 */
	protected function _iniCheck($arr)
	{
		$varsRows = $this->_checkMail($arr);

		return $varsRows;
	}

	/**

	 */
	protected function _checkMail($arr)
	{
		global $classCrypte;
		global $classFile;
		global $classEscape;

		global $varsAccounts;
		global $varsPluginAccountingAccounts;
		global $varsPluginAccountingAccountsEntity;
		global $varsPluginAccountingAccess;
		global $varsPluginAccountingAuthority;

		$varsRows = array(
			'numRows'  => 0,
			'arrRows' => array(),
		);

		$varsPreference = $this->_getVarsPreference(array(
			'idEntity'        => $arr['idEntity'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));
		if (!$varsPreference['strPassword']) {
			return $varsRows;
		}

		$varsMail = $varsPreference['jsonMail'];
		$varsMailHost = $varsPreference['jsonMailHost'];

		$idEntity = $arr['idEntity'];
		$numFiscalPeriod = $arr['numFiscalPeriod'];
		$strArrCommaIdEntity = ',' . $idEntity . ',';
		$arrCommaIdEntity = $strArrCommaIdEntity;

		$varsAccountName = array();
		$array = $varsPluginAccountingAccountsEntity;
		foreach ($array as $key => $value) {
			$idAccount = $key;
			if (!($varsPluginAccountingAccounts[$idAccount]['flagAdmin']
				|| preg_match("/$arrCommaIdEntity/", $varsPluginAccountingAccounts[$idAccount]['arrCommaIdEntity'])
			)) {
				continue;
			}

			$idAccess = $value[$idEntity]['idAccess'];
			$varsAccess = $varsPluginAccountingAccess[$idEntity][$idAccess];

			$idAuthority = $value[$idEntity]['idAuthority'];
			$varsAuthority = $varsPluginAccountingAuthority[$idAuthority];

			if (!$varsPluginAccountingAccounts[$idAccount]['flagAdmin']) {
				if (!(($varsAuthority['flagMyInsert'] || $varsAuthority['flagAllInsert'])
					&& ($varsAccess['jsonData'][$this->_extSelf['idPreference']] || $varsAccess['id'] == 1)
				)) {
					continue;
				}
			}
			$varsAccountName[$varsAccounts[$idAccount]['strCodeName']] = $idAccount;
		}

		$strHost = $varsPreference['strHost'];
		$strUser = $varsPreference['strUser'];
		$strPassword = $classCrypte->setDecrypt(array('data' => $varsPreference['strPassword']));
		$numPort = $varsPreference['numPort'];
		$flagSecure = $varsPreference['flagSecure'];

		$strSecure = '';
		if ($flagSecure == 'none') {
			$strSecure = '/imap/notls';

		} elseif ($flagSecure == 'start') {
			$strSecure = '/imap/tls/novalidate-cert';

		} elseif ($flagSecure == 'ssl') {
			$strSecure = '/imap/ssl/novalidate-cert';
		}

		$strServer = '{' . $strHost . ':' . $numPort . $strSecure . '}INBOX';
		if (preg_match("/^(localhost)$/i", $strHost)) {
			$strServer = '{localhost:' . $numPort . $strSecure . '}INBOX';
		}

		if (($mbox = @imap_open($strServer, $strUser, $strPassword)) == false) {
			return $varsRows;
		}

		$mboxes = imap_mailboxmsginfo($mbox);

		$arrNumMail = imap_search($mbox, 'UNSEEN');
		//$arrNumMail = imap_search($mbox, 'ALL');

		if (!$arrNumMail) {
			return $varsRows;
		}
		$numMail = count($arrNumMail);

		$arrayNew = array();
		for ($i = 1; $i <= $numMail; $i++) {
			$numberMail = $arrNumMail[$i-1];
			$rowData = array();
			$rowData['arrSpaceStrTag'] = '';

			$head = imap_header($mbox, $numberMail);

			$rowData['idMail'] = $numberMail;
			$rowData['stampArrive'] = $head->udate;
			$rowData['stampRegister'] = $head->udate;
			$rowData['stampUpdate'] = TIMESTAMP;

			$strPersonal = '';
			$eles = imap_mime_header_decode($head->from[0]->personal);
			foreach ($eles as $key => $value) {
				if ( $value->charset != 'default' ) {
					$strPersonal .= mb_convert_encoding($value->text, 'UTF-8', $value->charset);

				} else {
					$strPersonal .= $value->text;
				}
			}

			//check from address
			$flag = 0;
			$strHostFrom = $head->from[0]->host;
			$strMailboxFrom = $head->from[0]->mailbox;
			$strMailFrom = $strMailboxFrom . '@' . $strHostFrom;
			if ($varsMailHost[strtolower($strHostFrom)]) {
				$flag = 1;
			}
			if (!$flag) {
				if ($varsMail[strtolower($strMailFrom)]) {
					$flag = 1;
				}
			}

			if (!$flag) {
				imap_setflag_full($mbox, $numberMail ,"\\Seen");
				continue;
			}

			if (!empty($strPersonal)) {
				$rowData['arrSpaceStrTag'] = $classEscape->to(array( 'data' => $strPersonal));
			}
			$rowData['arrSpaceStrTag'] .= ' ' . $classEscape->to(array( 'data' => $strMailFrom));

			$strCodeName = '';
			if(!empty($head->subject)) {
				$eles = imap_mime_header_decode($head->subject);
				foreach ($eles as $key => $value) {
					if ( $value->charset != 'default' ) {
						$strCodeName .= mb_convert_encoding($value->text, 'UTF-8', $value->charset);

					} else {
						$strCodeName .= $value->text;
					}
				}
			}

			if (!$varsAccountName[$strCodeName]) {
				$strCodeName = '';
			}

			if ($strCodeName == '') {
				$rowData['idAccount'] = $varsAccounts[1]['id'];
			} else {
				$rowData['idAccount'] = $varsAccountName[$strCodeName];
			}

			$st = imap_fetchstructure($mbox, $numberMail);
			$numParts = count($st->parts);
			if($numParts > 0) {
				$arrSpaceStrTagTmpl = $rowData['arrSpaceStrTag'];
				for ($j = 0; $j < $numParts; $j++) {
					$rowData['arrSpaceStrTag'] = $arrSpaceStrTagTmpl;
					if (strtolower($st->parts[$j]->disposition) != 'attachment') {
						continue;
					}

					$numByte = $st->parts[$j]->bytes;
					if ($numByte > NUM_MAX_UPLOAD_SIZE) {
						continue;
					}

					if (!($st->parts[$j]->ifdparameters && count($st->parts[$j]->dparameters) > 0)) {
						continue;
					}

					$strFileName = '';
					$eles = imap_mime_header_decode($st->parts[$j]->dparameters[0]->value);
					if(!empty($eles) && is_array($eles) ) {
						foreach ($eles as $key => $value) {
							if ($value->charset != 'default') {
								$strFileName .= mb_convert_encoding($value->text, 'UTF-8', $value->charset);
							} else {
								$strFileName .= $value->text;
							}
						}
					}
					$rowData['arrSpaceStrTag'] .= ' ' . $classEscape->to(array( 'data' => $strFileName));
					list($strTitle, $ex) = explode('.', $strFileName);
					$strFileType = strtolower($ex);
					if ($strFileType != 'csv') {
						continue;
					}

					$tmp = imap_base64(imap_fetchbody($mbox , $numberMail, $j+1, FT_INTERNAL));
					if (!$tmp) {
						continue;
					}
					$tmp = trim($tmp);

					$numYear = date('Y');
					$numMonth = date('m');
					$strFileName = hash('sha256', $strTitle . '_' . $numberMail . '_' . $j . MICROTIMESTAMP). '.' . $strFileType . '.cgi';
					$path = PATH_BACK_DAT_TEMP;
					if (!is_dir($path)) {
						mkdir($path);
					}
					$strUrl = $path . $strFileName;

					$tmp = $classEscape->to(array( 'data' => $tmp ));

					$classFile->setData(array(
						'data' => $tmp,
						'path' => $strUrl,
					));

					`nkf -wLu --overwrite $strUrl`;

					$rowData['numByte'] = $numByte;
					$rowData['strFileType'] = $strFileType;
					$rowData['strUrl'] = $strUrl;
					$arrayNew[] = $rowData;
				}
			}
			imap_setflag_full($mbox, $numberMail ,"\\Seen");
		}
		imap_close($mbox);

		if (!$arrayNew) {
			return $varsRows;
		}

		$varsRows = array(
			'numRows' => count($arrayNew),
			'arrRows' => $arrayNew,
		);

		return $varsRows;
	}

	/**
		(array(
		))
	 */
	protected function _getVarsPreference($arr)
	{
		global $classDb;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogMail' . $strNation,
			'arrLimit' => array(),
			'arrOrder'  => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'idEntity',
					'flagCondition' => 'eq',
					'value'         => $arr['idEntity'],
				),
				array(
					'flagType'      => 'num',
					'strColumn'     => 'numFiscalPeriod',
					'flagCondition' => 'eq',
					'value'         => $arr['numFiscalPeriod'],
				),
			),
		));

		return $rows['arrRows'][0];
	}
}
