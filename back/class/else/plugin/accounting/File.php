<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_File extends Code_Else_Plugin_Accounting_Accounting
{
	protected $_extSelf = array(
		'idPreference' => 'fileWindow',
		'idLog'        => 'logWindow',
		'idCash'       => 'cashWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/file.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/file.php',
		'varsIframe'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/tmplIframe.php',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 0,
			'flagAuthority' => 'select',
			'idTarget'      => $this->_extSelf['idPreference'],
		));

		if (!$flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}

		$this->_checkEntity();

		if ($varsRequest['query']['child']) {
			$strChild = ucwords($varsRequest['query']['child']);
			$strExt = ucwords($varsRequest['query']['ext']);
			$str = $strExt . $strChild;

			$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/' . $str . ".php";
			if (!file_exists($path)) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
				}
				exit;
			}
			require_once($path);
			$strClass = 'Code_Else_Plugin_Accounting_' . $str;
			$classCall = new $strClass;
			$classCall->run();

		} else {
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
		}
		exit;
	}

	/**
	 */
	public function getDBAuthority($arr)
	{
		global $varsPluginAccountingAccount;
		global $varsAccount;
		global $varsRequest;

		$varsAuthority = $this->_getVarsAuthority(array());
		if (is_null($varsAuthority)) {
			return 0;
		}

		$idAccount = $varsAccount['id'];
		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];

		$strSql = 'idEntity = ? && numFiscalPeriod = ? ';
		$arrValue = array($idEntity, $numFiscalPeriod);

		$flag = $varsRequest['query']['jsonSearch']['ph']['flagApply'];
		if (!$flag) {
			$flag = 'none';
		}

		if ($flag != 'none') {
			$flagRemove = ($flag == 'remove')? 1 : 0;
			$strSql .= '&& flagRemove = ? ';
			$arrValue[] = $flagRemove;
		}

		if ($varsAuthority == 'admin' || $varsAuthority['flagAllSelect']) {
			$array = array(
				'strSql'   => $strSql,
				'arrValue' => $arrValue,
			);

			return $array;

		} elseif ($varsAuthority['flagMySelect']) {
			$strSql .= '&& (idAccount = ? || idAccountUpload = ?)';
			$arrValue[] = $idAccount;
			$arrValue[] = $idAccount;

			$array = array(
				'strSql'   => $strSql,
				'arrValue' => $arrValue,
			);

			return $array;
		}

		return 0;
	}


	/**
	 *
	 */
	protected function _iniJs()
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$this->_setJs(array(
			'pathVars'        => $this->_extSelf['pathVarsJs'],
			'pathTpl'         => $this->_extSelf['pathTplJs'],
			'arrFolder' => array(),
			'arrSearch'       => array(
				'idModule'  => 'accounting',
				'numLotNow' => 0,
				'strTable'  => 'accountingLogFile',
				'arrJoin'   => array(),
				'arrOrder'  => array(
					'strColumn' => 'idLogFile',
					'flagDesc'  => 1,
				),
				'insCurrent' => $this,
				'arrWhere'  => array(),
			),
		));

	}

	/**
		$this->_setJs(array(
			'pathVars'        => '',
			'pathTpl'         => '',
			'arrFolder'       => array(),
			'strTableSearch'  => '',
			'strColumnSearch' => '',
			'flagEntitySearch'  => 0,
			'flagAccountSearch' => 0,
			'arrSearch'       => array(
				'idModule' => '',
				'numLotNow' => 0,
				'strTable'  => '',
				'arrOrder'  => array(),
				'arrWhere'  => array(),
			),
		));
	 */
	protected function _setJs($arr)
	{
		global $classSmarty;

		$vars = $this->getVars(array(
			'path' => $arr['pathVars'],
		));

		$rows = $this->getSearch($arr['arrSearch']);

		$vars = $this->_updateSearch(array(
			'vars' => $vars,
			'rows' => $rows,
		));

		$vars['portal']['varsNavi'] = array();

		$json = json_encode($vars);
		$classSmarty->assign('varsLoad', $json);

		$classSmarty->assign('numNews', $this->getNumNews());
		$contents = $classSmarty->fetch($arr['pathTpl']);

		$this->sendJs(array(
			'data' => $contents,
		));
	}

	/**
	 *
	 */
	protected function _updateSearch($arr)
	{
		global $varsRequest;
		global $varsAccount;
		global $varsAccounts;
		global $varsPluginAccountingAccountsId;

		global $classEscape;
		global $classCheck;
		global $classHtml;

		global $varsPluginAccountingAccount;

		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$idEntity  = $varsPluginAccountingAccount['idEntityCurrent'];
		$strCheckStamp = 'accountingLogFile_' . $idEntity . '_' . $numFiscalPeriod;

		$vars = &$arr['vars'];
		$rows = &$arr['rows'];

		$flagCurrent = $this->_checkCurrent();
		$varsAuthority = $this->_getVarsAuthority(array());

		$flagAddUse = 0;
		$flagImportUse = 0;
		$flagPreferenceUse = 0;
		if ($flagCurrent) {
			if ($varsAuthority == 'admin' || $varsAuthority['flagAllInsert'] || $varsAuthority['flagMyInsert']) {
				$flagAddUse = 1;
				$flagImportUse = 1;
			}
			$flagPreferenceUse = 1;
		}
		$vars['portal']['varsList']['varsStart']['varsEdit']['flagPreferenceUse'] = $flagPreferenceUse;
		$vars['portal']['varsList']['varsEdit']['flagPreferenceUse'] = $flagPreferenceUse;
		$vars['portal']['varsList']['table']['varsEdit']['flagPreferenceUse'] = $flagPreferenceUse;
		$vars['portal']['varsList']['schedule']['varsEdit']['flagPreferenceUse'] = $flagPreferenceUse;

		$vars['portal']['varsDetail']['varsStart']['varsEdit']['flagAddUse'] = $flagAddUse;

		if (!$flagCurrent) {
			$flagImportUse = 0;
		}

		$vars['portal']['varsList']['varsEdit']['flagImportUse'] = $flagImportUse;
		$vars['portal']['varsList']['varsStart']['varsEdit']['flagImportUse'] = $flagImportUse;

		if (is_null($varsAuthority)) {
			return $vars;
		}
		$idAccount = $varsAccount['id'];

		$array = $rows['arrRows'];
		$arrayNew = array();
		$num = 0;
		$flag = 0;
		foreach ($array as $key => $value) {
			$varsTmpl = $vars['portal']['varsList']['templateDetail'];
			$varsTmpl['id'] = $value['idLogFile'];
			$varsTmpl['vars']['idTarget'] = $value['idLogFile'];
			$varsTmpl['numSort'] = (int) $key;

			if (($varsAccount['jsonStampCheck'][$strCheckStamp] < $value['stampRegister']) || $arr['arrIdTarget'][$value['idLogFile']]
			) {
				$flag = 1;

			} else {
				$varsTmpl['strClassLoad'] = '';

			}
			$varsTmpl['strTitle'] = $value['strTitle'];
			$varsTmpl['stampRegister'] = $value['stampRegister'];
			$varsTmpl['stampUpdate'] = $value['stampUpdate'];
			$varsTmpl['arrSpaceStrTag'] = preg_replace('/^ /', '', $value['arrSpaceStrTag']);
			$varsTmpl['flagRemove'] = (int) $value['flagRemove'];
			$varsTmpl['stampRemove'] = $value['stampRemove'];

			$varsTmpl['jsonVersion'] = $this->_updateSearchJsonVersion(array(
				'vars'  => $vars,
				'value' => $value['jsonVersion'],
			));
			$varsTmpl['numVersion'] = count($varsTmpl['jsonVersion']);

			$tempData = $this->_getJsonChargeHistoryVarsDetail(array(
				'vars'  => $vars['varsItem']['varsJsonChargeHistory'],
				'value' => $value['jsonChargeHistory'],
			));
			$temp = $classHtml->allot(array(
				'strClass'    => 'TableSimple',
				'flagStatus'  => 'Html',
				'varsDetail'  => $tempData['varsDetail'],
				'varsColumn'  => $vars['varsItem']['varsJsonChargeHistory']['varsColumn'],
				'varsStatus'  => $vars['varsItem']['varsJsonChargeHistory']['varsStatus'],
			));
			$varsTmpl['jsonChargeHistory'] = $temp['strHtml'];
			$varsTmpl['vars']['jsonChargeHistory'] = $tempData['varsData'];

			$varsTmpl['flagBtnDelete'] = 0;
			$varsTmpl['flagBtnLog'] = 0;
			$varsTmpl['flagBtnOutput'] = 0;
			$varsTmpl['flagBtnAdd'] = 0;
			$varsTmpl['flagBtnEdit'] = 0;
			$varsTmpl['flagCheckboxUse'] = 0;
			$varsTmpl['flagAdmin'] = ($varsAuthority == 'admin')? 1 : 0;
			$varsTmpl['flagCurrent'] = $flagCurrent;

			if ($flagCurrent) {
				if ($varsAuthority == 'admin') {
					if (!$value['flagRemove']) {
						$varsTmpl['flagBtnDelete'] = 1;
						$varsTmpl['flagBtnEdit'] = 1;
					}
					$varsTmpl['flagBtnAdd'] = 1;

				} else {
					if (!$value['flagRemove']) {
						if (($value['idAccount'] == $varsAccount['id'] && $varsAuthority['flagMyDelete'])
							|| $varsAuthority['flagAllDelete']
						) {
							$varsTmpl['flagBtnDelete'] = 1;
						}

						if (($value['idAccount'] == $varsAccount['id'] && $varsAuthority['flagMyUpdate'])
							|| $varsAuthority['flagAllUpdate']
						) {
							$varsTmpl['flagBtnEdit'] = 1;
						}
					}

					if ($varsAuthority['flagMyInsert'] || $varsAuthority['flagAllInsert']) {
						$varsTmpl['flagBtnAdd'] = 1;
					}

				}

				if ($varsTmpl['flagBtnDelete']) {
					$varsTmpl['flagCheckboxUse'] = 1;
				}


				$flagSelect = $this->_checkAccess(array(
					'flagAllUse'    => 0,
					'flagAuthority' => 'select',
					'idTarget'      => $this->_extSelf['idLog'],
				));
				$flagInsert = $this->_checkAccess(array(
					'flagAllUse'    => 0,
					'flagAuthority' => 'insert',
					'idTarget'      => $this->_extSelf['idLog'],
				));
				if ($flagSelect && $flagInsert) {
					$varsTmpl['flagBtnLog'] = 1;
				}

				$flagSelect = $this->_checkAccess(array(
					'flagAllUse'    => 0,
					'flagAuthority' => 'select',
					'idTarget'      => $this->_extSelf['idCash'],
				));
				$flagInsert = $this->_checkAccess(array(
					'flagAllUse'    => 0,
					'flagAuthority' => 'insert',
					'idTarget'      => $this->_extSelf['idCash'],
				));
				if ($flagSelect && $flagInsert) {
					$varsTmpl['flagBtnCash'] = 1;
				}
			}
			if ($varsAuthority == 'admin') {
				$varsTmpl['flagBtnOutput'] = 1;

			} else {
				if (($value['idAccount'] == $varsAccount['id'] && $varsAuthority['flagMyOutput'])
					|| $varsAuthority['flagAllOutput']
				) {
					$varsTmpl['flagBtnOutput'] = 1;
				}
			}

			$varsTmpl['varsColumnDetail']['id'] = $varsTmpl['id'];
			$varsTmpl['varsColumnDetail']['strTitle'] = $value['strTitle'];
			$varsTmpl['varsColumnDetail']['stampRegister'] = $value['stampRegister'];
			$varsTmpl['varsColumnDetail']['stampUpdate'] = $value['stampUpdate'];
			$varsTmpl['varsColumnDetail']['strFileType'] = $value['strFileType'];
			$varsTmpl['varsColumnDetail']['numSize'] = $classCheck->getDisc(array(
				'flagType' => 'str',
				'numByte'  =>  $value['numByte']
			));
			$varsTmpl['varsColumnDetail']['strVersion'] = 'Ver.' . count($varsTmpl['jsonVersion']);
			$strCodeName = $varsAccounts[$value['idAccount']]['strCodeName'];
			if (!$strCodeName) {
				$strCodeName = $varsPluginAccountingAccountsId[$value['idAccount']]['strCodeName'];
			}
			$varsTmpl['varsColumnDetail']['idAccount'] = $strCodeName;

			$varsTmpl['vars']['id'] = $varsTmpl['id'];
			$varsTmpl['vars']['strTitle'] = $value['strTitle'];
			$varsTmpl['vars']['strFileType'] = $value['strFileType'];
			$varsTmpl['vars']['idAccount'] = $value['idAccount'];
			$varsTmpl['vars']['numSize'] = $value['numByte'];
			$varsTmpl['vars']['numWidth'] = $value['numWidth'];
			$varsTmpl['vars']['numHeight'] = $value['numHeight'];
			$varsTmpl['vars']['flagRemove'] = $value['flagRemove'];
			$varsTmpl['vars']['arrSpaceStrTag'] = $classEscape->splitSpaceArrayData(array('data' => $varsTmpl['arrSpaceStrTag']));

			$varsTmpl['varsScheduleDetail']['stamp'] = $value['stampRegister'];

			if ($value['flagRemove']) {
				$varsTmpl['strClassFont'] = $vars['varsItem']['strClassNone'];
				$varsTmpl['varsColumnDetail']['strStatus'] = $vars['varsItem']['strRemoveFake'];

			} else {
				$varsTmpl['varsColumnDetail']['strStatus'] = $vars['varsItem']['strOpen'];
			}
			$varsTmpl['vars']['flagRemove'] = (int) $value['flagRemove'];
			$arrayColumnDetail = &$varsTmpl['varsColumnDetail'];
			foreach ($arrayColumnDetail as $keyColumnDetail => $valueColumnDetail) {
				if (is_null($valueColumnDetail)) {
					$arrayColumnDetail[$keyColumnDetail] = '';
				}
			}
			$arrayNew[$num] = $varsTmpl;

			$num++;
		}
		$vars['portal']['varsList']['varsPage']['varsStatus']['numRows'] = $rows['numRows'];
		$vars['portal']['varsList']['varsDetail'] = $arrayNew;

		if (!$arr['flagVars']) {
			$varsTemp = $classHtml->allot(array(
				'strClass'    => 'Table',
				'flagStatus'  => 'Html',
				'numTimeZone' => $varsAccount['numTimeZone'],
				'varsDetail'  => $arrayNew,
				'varsColumn'  => $vars['portal']['varsList']['table']['varsDetail']['varsColumn'],
				'varsStatus'  => $vars['portal']['varsList']['table']['varsDetail']['varsStatus'],
			));
			$vars['portal']['varsList']['varsHtml'] = $varsTemp['strHtml'];
			if ($flag) {
				$this->_setDbStampCheck(array(
					'strColumnAccount'    => $strCheckStamp,
					'strColumnPreference' => 'accounts',
				));
			}
		}

		$vars['portal']['varsDetail']['templateDetail'] = $this->_updateVars(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail'],
		));

		return $vars;
	}

	/**
		(array(

			'vars'  => $vars['varsItem']['varsJsonChargeHistory'],
			'value' => $value['jsonChargeHistory'],

		))
	 */
	protected function _getJsonChargeHistoryVarsDetail($arr)
	{
		$classTime = new Code_Else_Lib_Time();

		global $varsAccount;
		global $varsAccounts;
		global $varsPluginAccountingAccountsId;

		$array = $arr['value'];
		if (!$arr['value']) {
			$array = array();
		}
		$varsDetail = array();
		$varsData = array();
		$num = 1;
		foreach ($array as $key => $value) {
			$tmplDetail = $arr['tmplDetail'];
			$tmplDetail['id'] = $num;

			$strNo = $num;
			$tmplData = $arr['tmplData'];
			$tmplData['value'] = $strNo;
			$tmplDetail['varsDetail']['id'] = $tmplData;


			$classTime->setTimeZone(array('data' => $varsAccount['numTimeZone']));
			$strTime = $classTime->getDisplay(array(
				'stamp'    => $value['stampRegister'],
				'flagType' => 1,
			));
			$tmplData = $arr['tmplData'];
			$tmplData['value'] = $strTime;
			$tmplDetail['varsDetail']['stampRegister'] = $tmplData;


			$strCodeName = $varsAccounts[$value['idAccount']]['strCodeName'];
			if (!$varsAccounts[$value['idAccount']]['strCodeName']) {
				$strCodeName = $varsPluginAccountingAccountsId[$value['idAccount']]['strCodeName'];
			}
			$tmplData = $arr['tmplData'];
			$tmplData['value'] = $strCodeName;
			$tmplDetail['varsDetail']['idAccount'] = $tmplData;

			$varsDetail[] = $tmplDetail;

			$tempVars = array();
			$tempVars['idAccount'] = $value['idAccount'];
			$tempVars['strCodeName'] = $strCodeName;
			$varsData[] = $tempVars;

			$num++;
		}

		$data = array(
			'varsDetail' => $varsDetail,
			'varsData' => $varsData,
		);

		return $data;
	}

	/**
		(array(
			'vars' => array,
			'value' => array,
		))
	 */
	protected function _updateSearchJsonVersion($arr)
	{
		global $classCheck;
		global $classEscape;

		$array = $arr['value'];
		$arrayNew = array();
		$num = 1;
		foreach ($array as $key => $value) {
			$data = array();
			$data['stampRegister'] = $value['stampRegister'];
			$data['stampUpdate'] = $value['stampUpdate'];
			$data['strTitle'] = $value['strTitle'];
			$data['arrSpaceStrTag'] = preg_replace('/^ /', '', $value['arrSpaceStrTag']);
			$data['vars']['arrSpaceStrTag'] = $classEscape->splitSpaceArrayData(array('data' => $value['arrSpaceStrTag']));
			$data['numByte'] =  $value['numByte'];
			$data['numSize'] = $classCheck->getDisc(array(
				'flagType' => 'str',
				'numByte'  =>  $value['numByte']
			));
			$data['numWidth'] =  ($value['numWidth'])? $value['numWidth'] : 0;
			$data['numHeight'] =  ($value['numHeight'])? $value['numHeight'] : 0;
			$data['strFileType'] = $value['strFileType'];
			$data['strVersion'] = 'Ver.' . $num;
			$data['numVersion'] = $num;
			$arrayNew[] = $data;
			$num++;
		}

		return $arrayNew;
	}

	/**
	 *
	 */
	protected function _iniNaviSearchSave()
	{
		$this->_setNaviSearchSave(array(
			'pathVars'    => $this->_extSelf['pathVarsJs'],
			'strTable'    => 'accountingAccountMemo',
			'strColumn'   => 'jsonLogFileNaviSearch',
			'flagEntity'  => 1,
			'flagAccount' => 1,
		));
	}

	/**
	 *
	 */
	protected function _iniNaviSearchDelete()
	{
		$this->_setNaviSearchDelete(array(
			'strTable'  => 'accountingAccountMemo',
			'strColumn' => 'jsonLogFileNaviSearch',
			'flagEntity'  => 1,
			'flagAccount' => 1,
		));
	}

	/**
	 *
	 */
	protected function _iniNaviSearchReload()
	{
		global $varsPluginAccountingAccount;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingAccount['stampUpdate'],
				'flagSearch'  => 0,
			));
		}

		$this->_setNaviSearchReload(array(
			'pathVars'  => $this->_extSelf['pathVarsJs'],
			'strTable'  => 'accountingAccountMemo',
			'strColumn' => 'jsonLogFileNaviSearch',
			'flagEntity'  => 1,
			'flagAccount' => 1,
		));
	}

	/**
	 *
	 */
	protected function _iniNaviSearch()
	{
		$this->_setSearch(array('flag' => 1));
	}


	/**
		$this->_updateVars(array(
			'vars' => array(),
		));
	 */
	protected function _updateVars($arr)
	{
		global $classCheck;
		global $varsPluginAccountingPreference;

		$arrayCheck = array();
		$varsPreference = $this->_getVarsPreference(array());
		$array = $varsPreference['jsonFileType'];
		foreach ($array as $key => $value) {
			$arrayCheck[] = $key;
		}
		$strFileType =  join(' ', $arrayCheck);

		$strSize = $classCheck->getDisc(array(
			'flagType' => 'str',
			'numByte'  => NUM_MAX_UPLOAD_SIZE
		));

		$array = &$arr['vars'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'Upload') {
				$data = str_replace('<%numSize%>', $strSize, $value['varsTmpl']['add']);
				$array[$key]['varsTmpl']['add'] = str_replace('<%strFileType%>', $strFileType, $data);
				if (!$varsPreference['jsonFileType']) {
					$array[$key]['varsTmpl']['add'] = $array[$key]['varsTmpl']['none'];
				}
				$data = str_replace('<%numSize%>', $strSize, $value['varsTmpl']['edit']);
				$array[$key]['varsTmpl']['edit'] = str_replace('<%strFileType%>', $strFileType, $data);
				if (!$varsPreference['jsonFileType']) {
					$array[$key]['varsTmpl']['edit'] = $array[$key]['varsTmpl']['none'];
				}
				$array[$key]['arrFileType'] = $varsPreference['jsonFileType'];
			}
		}

		return $array;
	}

	/**
		(array(
		))
	 */
	protected function _getVarsPreference($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingFile',
			'arrLimit' => array(),
			'arrOrder'  => array(),
			'flagAnd'  => 1,
			'arrWhere' => array(
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
			),
		));

		return $rows['arrRows'][0];
	}

	/**
	 * array(
	 * 	'flag' => int
	 * 	'flagIframe' => int
	 * )
	 */
	protected function _setSearch($arr)
	{
		global $varsRequest;
		global $varsPluginAccountingPreference;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingPreference['jsonStampUpdate']['logFile'],
				'flagSearch'  => 1,
			));
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$this->checkSearch(array(
			'arrOrder' => $vars['portal']['varsNavi']['search']['varsDetail']['varsSearchSort'],
			'arrWhere' => $vars['portal']['varsNavi']['search']['varsDetail']['varsSearchItem'],
		));

		if (!$varsRequest['query']['jsonSearch']['ph']['arrOrder']) {
			$varsRequest['query']['jsonSearch']['ph']['arrOrder']= array(
				'strColumn' => 'idLogFile',
				'flagDesc'  => 1,
			);
		}
		$rows = $this->getSearch(array(
			'idModule'   => 'accounting',
			'numLotNow'  => $varsRequest['query']['jsonSearch']['numLotNow'],
			'strTable'   => 'accountingLogFile',
			'arrOrder'   => $varsRequest['query']['jsonSearch']['ph']['arrOrder'],
			'arrWhere'   => $varsRequest['query']['jsonSearch']['ph']['arrWhere'],
			'insCurrent' => $this,
		));

		$vars = $this->_updateSearch(array(
			'vars'        => $vars,
			'rows'        => $rows,
			'arrIdTarget' => ($arr['arrIdTarget'])? $arr['arrIdTarget'] : array(),
		));

		if ($arr['flagIframe']) {
			$this->_sendVarsIframe(array(
				'flag'    => ($arr['flag'])? $arr['flag'] : 1,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(
					'numRows'    => $rows['numRows'],
					'varsDetail' => $vars['portal']['varsList']['varsDetail'],
					'varsHtml'   => $vars['portal']['varsList']['varsHtml'],
				),
			));

		} else {
			$this->sendVars(array(
				'flag'    => ($arr['flag'])? $arr['flag'] : 1,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(
					'numRows'    => $rows['numRows'],
					'varsDetail' => $vars['portal']['varsList']['varsDetail'],
					'varsHtml'   => $vars['portal']['varsList']['varsHtml'],
				),
			));
		}


	}

	/**
		array(
			'flag'       => mix,
			'stamp'      => array,
			'numNews'    => int,
			'vars'       => array,
		)
	 */
	protected function _sendVarsIframe($arr)
	{
		global $varsRequest;

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

	/**
	 *
	 */
	protected function _iniListImport()
	{
		$this->_setClassExt(array('strClass' => 'FileImport'));
	}

	/**
		array(
			'strClass' => ''
		)
	 */
	protected function _setClassExt($arr)
	{
		$str = $arr['strClass'];
		$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/' . $str . ".php";
		if (!file_exists($path)) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}
		require_once($path);
		$strClass = 'Code_Else_Plugin_Accounting_' . $str;
		$classCall = new $strClass;
		$classCall->run();
	}

	/**
	 *
	 */
	protected function _iniListReload()
	{
		$this->_setSearch(array('flag' => 1));
	}

	/**
	 *
	 */
	protected function _iniDetailReload()
	{
		$this->_setSearchDetail(array());
	}

	/**
		array(
			'flagIframe' => int,
		)
	 *
	 */
	protected function _setSearchDetail($arr)
	{
		global $varsRequest;
		global $varsPluginAccountingPreference;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPluginAccountingPreference['jsonStampUpdate']['logFile'],
				'flagSearch'  => 0,
			));
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$this->checkSearch(array(
			'arrOrder' => $vars['portal']['varsNavi']['search']['varsDetail']['varsSearchSort'],
			'arrWhere' => $vars['portal']['varsNavi']['search']['varsDetail']['varsSearchItem'],
		));

		if (!$varsRequest['query']['jsonSearch']['ph']['arrOrder']) {
			$varsRequest['query']['jsonSearch']['ph']['arrOrder']= array(
				'strColumn' => 'id',
				'flagDesc'  => 1,
			);
		}
		$rows = $this->getSearch(array(
			'idModule'   => 'accounting',
			'numLotNow'  => $varsRequest['query']['jsonSearch']['numLotNow'],
			'strTable'   => 'accountingLogFile',
			'arrOrder'   => $varsRequest['query']['jsonSearch']['ph']['arrOrder'],
			'arrWhere'   => $varsRequest['query']['jsonSearch']['ph']['arrWhere'],
			'insCurrent' => $this,
		));

		$vars = $this->_updateSearch(array(
			'vars' => $vars,
			'rows' => $rows,
		));

		$varsTarget = $vars;

		$rowsTarget = $this->getSearch(array(
			'idModule'  => 'accounting',
			'numLotNow' => 0,
			'strTable'  => 'accountingLogFile',
			'arrOrder'  => array(
				'strColumn' => 'idLogFile',
				'flagDesc'  => 1,
			),
			'arrWhere'  => array(array(
				'flagType'      => 'num',
				'strColumn'     => 'idLogFile',
				'flagCondition' => 'eq',
				'value'         => $varsRequest['query']['jsonValue']['idTarget'],
			)),
			'insCurrent' => $this,
		));

		$varsTarget = $this->_updateSearch(array(
			'vars'     => $varsTarget,
			'rows'     => $rowsTarget,
			'flagVars' => 1,
		));
/*
		if (!$rowsTarget['numRows']) {
			$this->_sendOldError();
		}
*/
		if ($arr['flagIframe']) {
			$this->_sendVarsIframe(array(
				'flag'    => 1,
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(
					'varsDetail' => $varsTarget['portal']['varsList']['varsDetail'][0],
					'numRows'    => $rows['numRows'],
					'numLotNow'  => $varsRequest['query']['jsonSearch']['numLotNow'],
					'varsList'   => $vars['portal']['varsList']['varsDetail'],
					'varsHtml'   => $vars['portal']['varsList']['varsHtml'],
				),
			));

		} else {
			$this->sendVars(array(
				'stamp'   => $this->getStamp(),
				'numNews' => $this->getNumNews(),
				'vars'    => array(
					'varsDetail' => $varsTarget['portal']['varsList']['varsDetail'][0],
					'numRows'    => $rows['numRows'],
					'numLotNow'  => $varsRequest['query']['jsonSearch']['numLotNow'],
					'varsList'   => $vars['portal']['varsList']['varsDetail'],
					'varsHtml'   => $vars['portal']['varsList']['varsHtml'],
				),
			));
		}

	}

	/**
	 *
	 */
	protected function _iniDetailImg()
	{
		$this->_setClassExt(array('strClass' => 'FileOutput'));
	}


	/**
	 *
	 */
	protected function _iniDetailDelete()
	{
		global $varsRequest;

		$this->_setDelete(array(
			'arrId' => array($varsRequest['query']['jsonValue']['idTarget']),
		));
	}

	/**
	 *
	 */
	protected function _iniListDelete()
	{
		global $varsRequest;

		$this->_setDelete(array(
			'arrId' => $varsRequest['query']['jsonValue']['vars'],
		));

	}

	/**
		$this->_setDelete(array(
			'arrId' => array(),
		));
	 */
	protected function _setDelete($arr)
	{
		global $classDb;
		$dbh = $classDb->getHandle();
		global $classCheck;
		global $varsAccount;
		global $varsPluginAccountingAccount;
		global $varsPluginAccountingEntity;

		if (!$this->_checkCurrent()) {
			$this->_setSearchDetail(array(
				'flagIframe' => 0,
			));
		}

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 0,
			'flagAuthority' => 'delete',
			'idTarget'      => $this->_extSelf['idPreference'],
		));

		if (!$flag) {
			$this->_setSearchDetail(array(
				'flagIframe' => 0,
			));
		}

		$array = $arr['arrId'];
		$flag = $classCheck->checkValueFormat(array(
			'flagType' => 'num',
			'flagArr'  => 1,
			'value'    => $array,
		));

		if ($flag) {
			$this->_setSearchDetail(array(
				'flagIframe' => 0,
			));
		}

		$varsAuthority = $this->_getVarsAuthority(array());

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());

		$tm = TIMESTAMP;
		$stampRemove = $tm;
		$flagRemove = 1;

		try {
			$dbh->beginTransaction();

			foreach ($array as $key => $value) {
				$varsLog = $this->_getLogFile(array(
					'idTarget'        => $value,
					'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
				));
				if (!(($varsLog['idAccount'] == $varsAccount['id'] && $varsAuthority['flagMyDelete'])
					|| $varsAuthority['flagAllDelete']
					|| $varsAuthority == 'admin'
				)) {
					continue;
				}

				$arrColumn = array('stampRemove', 'flagRemove');
				$arrValue = array($stampRemove, $flagRemove);

				$classDb->updateRow(array(
					'idModule'  => 'accounting',
					'strTable'  => 'accountingLogFile',
					'arrColumn' => $arrColumn,
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
							'value'         => $value,
						),
					),
					'arrValue'  => $arrValue,
				));
				if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
					$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
					$numFiscalPeriodTemp = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];
					$classDb->deleteRow(array(
						'idModule'  => 'accounting',
						'strTable' => 'accountingLogFile',
						'flagAnd'   => 1,
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
								'value'         => $value,
							),
						),
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
		$this->_setSearch(array('flag' => 1, 'flagIframe' => 0,));
	}

	/**

	 */
	protected function _getLogFile($arr)
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
				'flagType'      => 'num',
				'strColumn'     => 'numFiscalPeriod',
				'flagCondition' => 'eq',
				'value'         => $arr['numFiscalPeriod'],
			),
			array(
				'flagType'      => 'num',
				'strColumn'     => 'idLogFile',
				'flagCondition' => 'eq',
				'value'         => $arr['idTarget'],
			),
		);

		if (!is_null($arr['flagRemove'])) {
			$arrWhere[] = array(
				'flagType'      => 'num',
				'strColumn'     => 'flagRemove',
				'flagCondition' => 'eq',
				'value'         => $arr['flagRemove'],
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

		return $rows['arrRows'][0];

	}
}
