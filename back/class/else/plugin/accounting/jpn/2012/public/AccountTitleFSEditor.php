<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_AccountTitleFSEditor_2012_Public extends Code_Else_Plugin_Accounting_Jpn_AccountTitleFSEditor
{
	protected $_childSelf = array(
		'pathTplJs'  => 'else/plugin/accounting/js/jpn/accountTitleFSEditor.js',
		'pathVarsJs' => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/accountTitleFSEditor.php',
	);

	/**

	 */
	protected function _iniDetailEdit()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		global $classEscape;
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		global $varsRequest;

		if (!$this->_checkCurrent()) {
			$this->_sendOldError();
		}

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());

		if ($varsRequest['query']['func'] == 'DetailAdd') {
			exit;

		} else {
			$flag = $this->_checkAccess(array(
				'flagAllUse'    => 1,
				'flagAuthority' => 'update',
				'idTarget'      => $this->_extSelf['idPreference'],
			));
		}

		if (!$flag) {
			$this->_sendOld();
		}

		$varsRequest['query']['jsonValue']['vars']['StrTitle'] = $classEscape->toComma(array(
			'data' => $varsRequest['query']['jsonValue']['vars']['StrTitle']
		));

		$idTarget = $varsRequest['query']['jsonValue']['idTarget'];
		$strNation = ucwords(PLUGIN_ACCOUNTING_STR_NATION);

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$flagFS = $this->_checkValueFS(array(
			'vars'     => $vars,
		));

		$varsItem = $this->_getVarsItem(array(
			'flag' => $flagFS,
		));

		$vars = $this->_updateVars(array(
			'flag'     => $flagFS,
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$flagDefault = $this->_checkValueDetailDefault(array(
			'vars'     => $vars['portal']['varsList']['varsDetail'],
			'idTarget' => $idTarget,
		));

		$varsTarget = $this->_setValueTemplate(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail'],
		));

		$varsTarget = $this->getValue(array(
			'vars' => $varsTarget,
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget
		));

		if ($varsRequest['query']['func'] == 'DetailAdd') {
			$flagEditCheck = $this->_checkValueDetailAdd(array(
				'arrValue'    => $arrValue,
				'varsItem'    => $varsItem,
				'flagFS'      => $flagFS,
				'vars'        => $vars,
				'idTarget'    => $idTarget,
				'flagDefault' => $flagDefault,
			));

		} else {
			$flagEditCheck = $this->_checkValueDetailEdit(array(
				'arrValue'    => $arrValue,
				'varsItem'    => $varsItem,
				'flagFS'      => $flagFS,
				'vars'        => $vars,
				'idTarget'    => $idTarget,
				'flagDefault' => $flagDefault,
			));
		}

		$varsFS = $this->_setValueDetailDb(array(
			'arrValue'    => $arrValue,
			'varsItem'    => $varsItem,
			'flagFS'      => $flagFS,
			'vars'        => $vars,
			'idTarget'    => $idTarget,
			'flagDefault' => $flagDefault,
		));

		$jsonAccountTitle = json_encode($varsFS);
		$this->checkTextSize(array(
			'flag' => 'errorDataMax',
			'str'  => $jsonAccountTitle,
		));
		$strAccountTitle = 'jsonJgaapFS'. $flagFS;

		$arrDbColumn = array($strAccountTitle);
		$arrDbValue = array($jsonAccountTitle);

		try {
			$dbh->beginTransaction();

			$classDb->updateRow(array(
				'idModule'  => 'accounting',
				'strTable' => 'accountingFS' . $strNation,
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
						'value'         => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
					),
				),
				'arrValue'  => $arrDbValue,
			));

			if ($flagEditCheck['idAccountTitle'] || $flagEditCheck['flagDebit']) {
				$this->_setValueDetailDbEdit(array(
					'arrValue'      => $arrValue,
					'varsItem'      => $varsItem,
					'flagFS'        => $flagFS,
					'idTarget'      => $idTarget,
					'flagEditCheck' => $flagEditCheck,
				));
			}

			if (preg_match("/^(tempPrev)$/", $flagCurrentFlagNow)) {
				$classCalcTempNextAccountTitleFS = $this->_getClassCalc(array(
					'flagType'   => 'TempNext',
					'flagDetail' => 'AccountTitleFS',
				));
				$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];
				$numFiscalPeriod = $varsPluginAccountingEntity[$idEntity]['numFiscalPeriod'];

				if ($varsRequest['query']['func'] == 'DetailAdd') {
					$flagStatus = 'add';

				} else {
					$flagStatus = 'edit';
				}
				$flag = $classCalcTempNextAccountTitleFS->allot(array(
					'flagStatus'      => $flagStatus,
					'numFiscalPeriod' => $numFiscalPeriod,
					'flagFS'          => $flagFS,
					'idTarget'        => $idTarget,
					'arrValue' => array(
						'strTitle'               => $arrValue['arr']['strTitle'],
						'idAccountTitle'         => $arrValue['arr']['idAccountTitle'],
						'flagDebit'              => $arrValue['arr']['flagDebit'],
						'arrCommaIdAccountTitle' => $arrValue['arr']['arrCommaIdAccountTitle'],
						'flagUse'                => $arrValue['arr']['flagUse'],
					),
				));

				if ($flag) {
					if ($flag == 'arrayblock' || $flag == 'flagSortUse') {
						$this->_sendOld();
					}
					$this->sendVars(array(
						'flag'    => $flag,
						'stamp'   => $this->getStamp(),
						'numNews' => $this->getNumNews(),
						'vars'    => array(),
					));
				}
			}

			$this->_updateDbPreferenceStamp(array('strColumn' => 'fS'));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$this->_setSearch();
	}


	/**
		(array(
			'arrValue'    => $arrValue,
			'varsItem'    => $varsItem,
			'flagFS'      => $flagFS,
			'vars'        => $vars,
			'idTarget'    => $idTarget,
			'flagDefault' => $flagDefault,
		))
	 */
	protected function _checkValueDetailEdit($arr)
	{
		global $classEscape;
		global $classDb;
		$dbh = $classDb->getHandle();

		global $varsPluginAccountingAccount;

		$varsFS = $arr['varsItem']['varsFS']['jsonJgaapFS' . $arr['flagFS']];

		$arrayBlock = $this->_getTreeBlock(array(
			'vars'     => $varsFS,
			'idTarget' => $arr['idTarget'],
			'arrNew'   => array(),
		));
		if (!$arrayBlock) {
			$this->_sendOld();
		}

		$varsTarget = array();
		foreach ($arrayBlock as $key => $value) {
			if ($value['vars']['idTarget'] == $arr['idTarget']) {
				$varsTarget = $value;
				break;
			}
		}

/*unique*/
		if (!(int) $varsTarget['flagBtnUse']) {
			$this->_sendOld();
		}

		if ($arr['arrValue']['arr']['idAccountTitle'] != $varsTarget['vars']['idTarget']
			|| $arr['arrValue']['arr']['flagDebit'] != $varsTarget['vars']['flagDebit']
			|| $arr['arrValue']['arr']['flagUse'] != $varsTarget['vars']['flagUse']
		) {
			$this->_sendOld();
		}

		$flagEdit = array();

		$array = $arrayBlock;
		$arrayCheck = array();
		foreach ($array as $key => $value) {
			$arrayCheck[$value['vars']['idTarget']] = $value;
		}

		$arrCommaIdAccountTitle = $classEscape->splitCommaArrayData(array('data' => $arr['arrValue']['arr']['arrCommaIdAccountTitle']));
		$array = $arrCommaIdAccountTitle;
		$num = 0;
		foreach ($array as $key => $value) {
			if (!$arrayCheck[$value]) {
				$this->_sendOld();
			}
			$num++;
		}

		if ($num != count($arrayCheck)) {
			$this->_sendOld();
		}

		return $flagEdit;
	}


	//-----------------------------------------------------------
	// Code_Else_Plugin_Accounting_Jpn_AccountTitleFS overwrite _2012_Public
	//-----------------------------------------------------------


	protected $_extSelf = array(
		'idPreference' => 'accountTitleFSWindow',
		'pathTplJs'    => 'else/plugin/accounting/js/jpn/2012/public/accountTitleFS.js',
		'pathVarsJs'   => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/2012/public/accountTitleFS.php',
	);

	/**
		(array(
			'vars'             => array(),
			'varsFS'           => array(),
			'varsItem'         => array(),
		))
	 */
	protected function _updateVarsList($arr)
	{
		$array = &$arr['varsFS'];

		foreach ($array as $key => $value) {
			$array[$key]['id'] = '';
			$array[$key]['flagBoldNow'] = 0;
			$array[$key]['strClassFont'] = '';
			$array[$key]['strClassBg'] = '';
			$array[$key]['vars']['idTarget'] = $value['vars']['idTarget'];
			$array[$key]['varsColumnDetail'] = array(
				'flagDebit' => '',
				'flagUse' => '',
			);

			$array[$key]['varsValue']['strTitle'] = $array[$key]['strTitle'];
			if (!$array[$key]['strTitle']) {
				$array[$key]['strTitle'] = $arr['vars']['varsItem']['strBlank'];
			}

			if (!is_null($value['vars']['flagUse'])) {

				//flagDebit
				if ($value['vars']['flagDebit']) {
					$array[$key]['varsColumnDetail']['flagDebit'] = $arr['vars']['varsItem']['strDebit'];

				} else {
					$array[$key]['varsColumnDetail']['flagDebit'] = $arr['vars']['varsItem']['strCredit'];
				}

				//flagUse
				if ((int) $value['vars']['flagUse']) {
					$array[$key]['varsColumnDetail']['flagUse'] = $arr['vars']['varsItem']['strShow'];

				} else {
					$array[$key]['varsColumnDetail']['flagUse'] = $arr['vars']['varsItem']['strHide'];
					$array[$key]['strClassFont'] = $arr['vars']['varsItem']['strClassNone'];
				}

			}
			if ($value['child']) {
				$array[$key]['child'] = $this->_updateVarsList(array(
					'vars'     => $arr['vars'],
					'varsFS'   => $array[$key]['child'],
				));
			}
		}

		return $array;
	}


}
