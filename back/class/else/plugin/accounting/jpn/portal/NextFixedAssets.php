<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_Portal_NextFixedAssets extends Code_Else_Plugin_Accounting_Jpn_Portal
{
	protected $_extChildSelf = array(
		'pathVarsJs'  => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/fixedAssets.php',
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
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}
	}

	/**
		(array(
			'numFiscalPeriod'  => $numFiscalPeriod,
			'varsEntityNation'   => $varsEntityNation,
		))
	 */
	protected function _iniInsertFixedAssets($arr)
	{
		$this->_setInsertFixedAssets($arr);
	}

	/**
		(array(
			'numFiscalPeriod'  => $numFiscalPeriod,
			'varsEntityNation'   => $varsEntityNation,
		))
	 */
	protected function _iniUpdateFixedAssets($arr)
	{
		$this->_updateFixedAssets($arr);
	}

	/**

	 */
	protected function _updateFixedAssets($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$numFiscalPeriod = $arr['numFiscalPeriod'];
		$varsFixedAssets = $this->_getVarsFixedAssets(array(
			'numFiscalPeriod' => $numFiscalPeriod,
		));

		if (!$varsFixedAssets['jsonDepSum']) {
			$varsFixedAssets['jsonDepSum'] = array();
		}
		$varsValue = $varsFixedAssets['jsonDepSum'][$numFiscalPeriod];
		if (!$varsValue) {
			$varsValue = array();
		}

		$classCall = $this->_getClassCalcFixedAssets();
		$varsDepSum = $classCall->allot((array(
			'flagStatus'      => 'start',
			'flagDepMethod'   => 'sum',
			'arrValue'        => $varsValue,
			'numFiscalPeriod' => $numFiscalPeriod,
		)));
		$jsonDepSum = json_encode($varsDepSum);
		$arrDbColumn = array('jsonDepSum');
		$arrDbValue = array($jsonDepSum);

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable' => 'accountingFixedAssets' . $strNation,
			'arrColumn' => $arrDbColumn,
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
					'value'         => $numFiscalPeriod,
				),
			),
			'arrValue'  => $arrDbValue,
		));

	}

	/**
		(array(
			'numFiscalPeriod'  => $numFiscalPeriod,
			'varsEntityNation' => $varsEntityNation,
		))
	 */
	protected function _setInsertFixedAssets($arr)
	{
		global $classDb;

		$varsFixedAssets = $this->_getVarsFixedAssets(array(
			'numFiscalPeriod' => $arr['varsEntityNation']['numFiscalPeriod'],
		));

		$arrAccountTitle = $this->_getAccountTitle(array(
			'arrSubAccountTitle' => array(),
			'numFiscalPeriod'    => $arr['numFiscalPeriod'],
		));

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$varsEntityNation = $this->_getVarsEntityNation(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		));

		$arrDbColumn = array();
		$arrDbValue = array();
		$array = $varsFixedAssets;
		foreach ($array as $key => $value) {
			if ($key == 'id') {
				continue;

			} elseif ($key == 'stampUpdate') {
				$value = TIMESTAMP;

			} elseif ($key == 'numFiscalPeriod') {
				$value = $arr['numFiscalPeriod'];

			} elseif ($key == 'flagDepWrite') {
				if ($varsEntityNation['numFiscalTermMonth'] != 12) {
					$value = 'f1';
				}

			} elseif ($key == 'jsonAccountTitle') {
				$value = $this->_checkJsonAccountTitle(array(
					'vars'            => ($value)? $value : array(),
					'arrAccountTitle' => $arrAccountTitle,
				));

			} elseif ($key == 'jsonDepSum') {
				$value = '';
			}

			$arrDbColumn[] = $key;
			$arrDbValue[] = $value;
		}

		$classDb->insertRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingFixedAssets' . $strNation,
			'arrColumn' => $arrDbColumn,
			'arrValue'  => $arrDbValue,
		));
	}

	/**
		(array(
			'numFiscalPeriod'  => $numFiscalPeriod,
			'varsEntityNation'   => $varsEntityNation,
		))
	 */
	protected function _iniInsertLogFixedAssets($arr)
	{
		$this->_setInsertLogFixedAssets($arr);
	}

	/**
		(array(
			'numFiscalPeriod'  => $numFiscalPeriod,
			'varsEntityNation'   => $varsEntityNation,
		))
	 */
	protected function _setInsertLogFixedAssets($arr)
	{
		$rows = $this->_getVarsLog(array(
			'numFiscalPeriod' => $arr['varsEntityNation']['numFiscalPeriod'],
		));

		$array = $rows['arrRows'];
		foreach ($array as $key => $value) {
			$this->_setInsertLogFixedAssetsLoop(array(
				'varsLog'          => $value,
				'varsEntityNation' => $arr['varsEntityNation'],
				'numFiscalPeriod'  => $arr['numFiscalPeriod'],
			));
		}
	}

	/**
		(array(
			'varsLog'          => $value,
			'varsEntityNation' => $arr['varsEntityNation'],
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
		))
	 */
	protected function _setInsertLogFixedAssetsLoop($arr)
	{
		global $classDb;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$arrDbColumn = array();
		$arrDbValue = array();
		$array = $arr['varsLog'];
		foreach ($array as $key => $value) {
			if ($key == 'id') {
				continue;

			} elseif ($key == 'numFiscalPeriod') {
				$value = $arr['numFiscalPeriod'];

			} elseif (preg_match("/^jsonWriteHistory$/", $key)) {
				$value = json_encode(array());

			} elseif (preg_match("/^json/", $key)) {
				if (!$value) {
					$value = array();
				}
				$value = json_encode($value);
			}
			$arrDbColumn[] = $key;
			$arrDbValue[] = $value;
		}

		$classDb->insertRow(array(
			'idModule'  => 'accounting',
			'strTable'  => 'accountingLogFixedAssets' . $strNation,
			'arrColumn' => $arrDbColumn,
			'arrValue'  => $arrDbValue,
		));
	}

	/**
		(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idTarget'        => $arr['idTarget'],
		))
	 */
	protected function _getVarsLog($arr)
	{
		global $classDb;

		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

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
				'strColumn'     => 'flagRemove',
				'flagCondition' => 'ne',
				'value'         => 1,
			),
		);
		if ($arr['idTarget']) {
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
					'strColumn'     => 'idFixedAssets',
					'flagCondition' => 'eq',
					'value'         => $arr['idTarget'],
				),
			);
		}

		$rows = $classDb->getSelect(array(
			'idModule' => 'accounting',
			'strTable' => 'accountingLogFixedAssets' . $strNation,
			'arrLimit' => array(),
			'arrOrder' => array(),
			'flagAnd'  => 1,
			'arrWhere' => $arrWhere,
		));

		$array = &$rows['arrRows'];
		foreach ($array as $key => $value) {
			if ($array[$key]['stampEnd'] === '0' || is_null($array[$key]['stampEnd'])) {
				$array[$key]['stampEnd'] = '';
			}
			if ($array[$key]['stampDrop'] === '0' || is_null($array[$key]['stampDrop'])) {
				$array[$key]['stampDrop'] = '';
			}
		}

		return $rows;
	}

	/**
		(array(
			'flagStatus'       => 'update',
			'numFiscalPeriod'  => $numFiscalPeriod,
			'idTarget'         => $id,
		))
	 */
	protected function _iniUpdate($arr)
	{
		return $this->_updateLogFixedAssets($arr);
	}

	/**
		(array(
			'flagStatus'       => 'update',
			'numFiscalPeriod'  => $numFiscalPeriod,
			'idTarget'         => $id,
		))
	 */
	protected function _updateLogFixedAssets($arr)
	{
		$vars = $this->getVars(array(
			'path' => $this->_extChildSelf['pathVarsJs'],
		));

		$data = $this->_getValueJsonDetailConfigValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValueConfig = $data['data'];
		$arrColumnJsonDetail = $data['dataColumn'];

		$varsLogs = $this->_getVarsLog(array(
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
			'idTarget'        => $arr['idTarget'],
		));
		$array = $varsLogs['arrRows'];
		foreach ($array as $key => $value) {
			$varsLogUpdate = $this->_updatetLogFixedAssetsCalc(array(
				'arrValueConfig'      => $arrValueConfig,
				'varsLog'             => $value,
				'numFiscalPeriod'     => $arr['numFiscalPeriod'],
			));

			$flag = $this->_updatetLogFixedAssetsLoop(array(
				'varsLog'             => $value,
				'varsLogUpdate'       => $varsLogUpdate,
				'arrColumnJsonDetail' => $arrColumnJsonDetail,
				'numFiscalPeriod'     => $arr['numFiscalPeriod'],
			));
			if ($flag) {
				return $flag;
			}
		}
	}

	/**

	 */
	protected function _getValueJsonDetailConfigValue($arr)
	{
		global $classEscape;

		$data = array();
		$dataColumn = array();
		$array = $arr['vars'];
		foreach ($array as $key => $value) {
			if ($value['id'] == 'JsonDetail') {
				$arrayDetail = $value['varsFormSensitive']['varsTmpl']['varsDetail'];
				foreach ($arrayDetail as $keyDetail => $valueDetail) {
					if ($valueDetail['flagVersionColumnNone']) {
						continue;
					}
					$id = $classEscape->toLower(array('str' => $arrayDetail[$keyDetail]['id']));
					$dataColumn[] = $id;

					if (!$valueDetail['flagNumValueConfig']) {
						continue;
					}
					$data[$id] = $arrayDetail[$keyDetail]['value'];
				}
				$dataColumn[] = 'flagDepRateType';
				return array(
					'data'       => $data,
					'dataColumn' => $dataColumn,
				);
			}
		}
	}

	/**
		(array(
			'arrValueConfig'   => $arrValueConfig,
			'varsLog'          => $value,
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
		))
	 */
	protected function _updatetLogFixedAssetsCalc($arr)
	{
		$data = $this->_getValueNumber(array(
			'arrValue' => $arr['varsLog'],
		));

		$varsLog = $arr['varsLog'];

		$array = $arr['arrValueConfig'];
		foreach ($array as $key => $value) {
			$arr['varsLog'][$key] = $value;
		}

		$arr['varsLog']['numValue'] = $varsLog['numValue'];
		$arr['varsLog']['numValueCompression'] = $varsLog['numValueCompression'];
		$arr['varsLog']['numValueAccumulated'] = $varsLog['numValueAccumulatedClosing'];
		$arr['varsLog']['numValueDepPrevOver'] = $varsLog['numValueDepNextOver'];
		$arr['varsLog']['numValueDepSpecialShortPrev'] = $varsLog['numValueDepSpecialShortNext'];
		$arr['varsLog']['numRatioOperate'] = $varsLog['numRatioOperate'];

		$classCall = $this->_getClassCalcFixedAssets();
		$arr['varsLog'] = $classCall->allot((array(
			'flagStatus'      => 'update',
			'flagDepMethod'   => $arr['varsLog']['flagDepMethod'],
			'arrValue'        => $arr['varsLog'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		)));

		$varsCalc = $classCall->allot((array(
			'flagStatus'      => 'calc',
			'flagDepMethod'   => $arr['varsLog']['flagDepMethod'],
			'arrValue'        => $arr['varsLog'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		)));

		$array = $varsCalc;
		foreach ($array as $key => $value) {
			$arr['varsLog'][$key] = $value;
		}

		$arr['varsLog'] = $classCall->allot((array(
			'flagStatus'      => 'update',
			'flagDepMethod'   => $arr['varsLog']['flagDepMethod'],
			'arrValue'        => $arr['varsLog'],
			'numFiscalPeriod' => $arr['numFiscalPeriod'],
		)));

		return $arr['varsLog'];
	}

	/**
		(array(
			'arrValue'        => $arrValue,
		))
	 */
	protected function _getValueNumber($arr)
	{
		$data = array();
		$arrayStr = $arr['arrValue'];
		foreach ($arrayStr as $key => $value) {
			if (preg_match( "/^numValue/", $key)) {
				$data[$key] = $arr['arrValue'][$key];
				if ($arr['arrValue'][$key] == '') {
					$data[$key] = 0;
				}
			}
		}

		return $data;
	}

	/**
	 *
	 */
	protected function _getClassCalcFixedAssets()
	{
		$str = 'CalcDep';
		$path = PATH_BACK_CLASS_ELSE_PLUGIN . 'accounting/jpn/' . $str . ".php";
		if (!file_exists($path)) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}
		require_once($path);
		$strClass = 'Code_Else_Plugin_Accounting_Jpn_' . $str;
		$classCall = new $strClass;

		return $classCall;
	}

	/**
		(array(
			'varsLog'          => $varsLog,
			'varsLogUpdate'    => $varsLogUpdate,
			'arrColumnJsonDetail' => $arrColumnJsonDetail,
			'numFiscalPeriod'  => $arr['numFiscalPeriod'],
		))
	 */
	protected function _updatetLogFixedAssetsLoop($arr)
	{
		global $classDb;
		global $varsPluginAccountingAccount;

		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);


		$arrDbColumn = array();
		$arrDbValue = array();
		$array = $arr['varsLogUpdate'];
		foreach ($array as $key => $value) {
			if ($key == 'id') {
				continue;

			} elseif ($key == 'stampUpdate') {
				$value = TIMESTAMP;

			} elseif ($key == 'jsonChargeHistory') {
				$arrChargeHistory = $arr['varsLog'][$key];
				$value = json_encode($arrChargeHistory);

			} elseif ($key == 'jsonVersion') {
				$arrVersion = $arr['varsLog'][$key];
				$varsVersion = $this->_getDbLogVarsVersion(array(
					'varsLog'             => $arr['varsLog'],
					'varsLogUpdate'       => $arr['varsLogUpdate'],
					'arrColumnJsonDetail' => $arr['arrColumnJsonDetail'],
				));
				if ($varsVersion) {
					$arrVersion[] = $varsVersion;
					$jsonVersion = json_encode($arrVersion);
					$flag = $this->checkTextSize(array(
						'flagReturn' => 1,
						'flag'       => 'errorDataMax',
						'str'        => $jsonVersion,
					));
					if ($flag) {
						return 'errorDataMax';
					}
					$value = $jsonVersion;

				} else {
					$value = json_encode($value);
				}

			} elseif (preg_match("/^jsonWriteHistory$/", $key)) {
				if ($value) {
					$value = json_encode($value);
				} else {
					$value = json_encode(array());
				}

			} elseif (preg_match("/^stampRemove$/", $key)) {
				if ($value) {
					$value = TIMESTAMP;
				}
			}

			$arrDbColumn[] = $key;
			$arrDbValue[] = $value;
		}

		$classDb->updateRow(array(
			'idModule'  => 'accounting',
			'strTable' => 'accountingLogFixedAssets' . $strNation,
			'arrColumn' => $arrDbColumn,
			'flagAnd'  => 1,
			'arrWhere' => array(
				array(
					'flagType'      => 'num',
					'strColumn'     => 'id',
					'flagCondition' => 'eq',
					'value'         => $arr['varsLog']['id'],
				),
			),
			'arrValue'  => $arrDbValue,
		));
	}




	/**
		'varsLog'             => $arr['varsLog'],
		'varsLogUpdate'       => $arr['varsLogUpdate'],
		'arrColumnJsonDetail' => $arr['arrColumnJsonDetail'],
	 */
	protected function _getDbLogVarsVersion($arr)
	{
		global $classEscape;

		$arrColumn = array(
			'stampRegister',
			'stampUpdate',
			'strTitle',
			'strMemo',
			'arrSpaceStrTag',
		);

		$flagCheck = 0;
		$arrayNew = array();
		$array = $arrColumn;
		foreach ($array as $key => $value) {
			if ($value == 'stampRegister' || $value == 'stampUpdate') {
				$arrayNew[$value] = TIMESTAMP;

			} else {
				if ($arr['varsLog'][$value] != $arr['varsLogUpdate'][$value]) {
					$flagCheck = 1;
				}
				$arrayNew[$value] = $arr['varsLog'][$value];
			}
		}

		$array = $arr['arrColumnJsonDetail'];
		foreach ($array as $key => $value) {
			if ($arr['varsLog'][$value] != $arr['varsLogUpdate'][$value]) {
				$flagCheck = 1;
			}
			if ($arr['varsLog'] != $arr['varsLogUpdate']) {
				$arrayNew['jsonDetail'][$value] = $arr['varsLogUpdate'][$value];
			} else {
				$arrayNew['jsonDetail'][$value] = $arr['varsLog'][$value];
			}
		}

		if (!$flagCheck) {
			return array();
		}

		return $arrayNew;
	}

}
