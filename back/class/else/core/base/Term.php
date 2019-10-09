<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Base_Term extends Code_Else_Core_Base_Base
{
	protected $_extSelf = array(
		'pathTplJs'  => 'else/core/base/js/term.js',
		'pathVarsJs' => 'back/tpl/vars/else/core/base/<strLang>/js/term.php',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;
		global $classCheck;

		$flag = $classCheck->checkModule(array(
			'idModule' => $varsRequest['query']['module'],
			'flagType' => 'Admin'
		));
		if (!$flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__);
			}
			exit;
		}

		if ($varsRequest['query']['child']) {
			$str = ucwords($varsRequest['query']['child']);
			$path = PATH_BACK_CLASS_ELSE_CORE_BASE . 'Term' . $str . ".php";
			if (!file_exists($path)) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__);
				}
				exit;
			}
			require_once($path);
			$strClass = 'Code_Else_Core_Base_Term' . $str;
			$classCall = new $strClass;
			$classCall->run();

		} else {
			if ($varsRequest['query']['func']) {
				$method = '_ini' . $varsRequest['query']['func'];
				if (method_exists($this, $method)) {
					$this->$method();

				} else {
					if (FLAG_TEST) {
						var_dump(__CLASS__ . '/' .__FUNCTION__);
					}
					exit;
				}
			}
		}
		exit;
	}

	/**
	 *
	 */
	protected function _iniJs()
	{
		$this->_setJs(array(
			'pathVars'        => $this->_extSelf['pathVarsJs'],
			'pathTpl'         => $this->_extSelf['pathTplJs'],
			'arrFolder' => array(),
			'arrSearch'       => array(
				'idModule'  => 'base',
				'numLotNow' => 0,
				'strTable'  => 'baseTerm',
				'arrOrder'  => array(
					'strColumn' => 'id',
					'flagDesc'  => 1,
				),
				'arrWhere'  => array(),
			),
		));
	}

	/**
	 *
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
	protected function _iniNaviSearch()
	{
		$this->_setSearch(array('flag' => 1));
	}

	/**
	 *
	 */
	protected function _iniNaviSearchSave()
	{
		$this->_setNaviSearchSave(array(
			'pathVars'  => $this->_extSelf['pathVarsJs'],
			'strTable'  => 'baseAccountMemo',
			'strColumn' => 'jsonTermNaviSearch',
		));
	}

	/**
	 *
	 */
	protected function _iniNaviSearchDelete()
	{
		$this->_setNaviSearchDelete(array(
			'strTable'  => 'baseAccountMemo',
			'strColumn' => 'jsonTermNaviSearch',
		));
	}


	/**
	 *
	 */
	protected function _iniNaviSearchReload()
	{
		global $varsAccount;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsAccount['stampUpdate'],
				'flagSearch'  => 0,
			));
		}

		$this->_setNaviSearchReload(array(
			'pathVars'  => $this->_extSelf['pathVarsJs'],
			'strTable'  => 'baseAccountMemo',
			'strColumn' => 'jsonTermNaviSearch',
		));
	}



	/**
	 * array(
	 * 	'flag' => int
	 * )
	 */
	protected function _setSearch($arr)
	{
		global $varsRequest;
		global $varsPreference;
		global $varsAccount;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPreference['jsonStampUpdate']['term'],
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
			$varsRequest['query']['jsonSearch']['ph']['arrOrder'] = array(
				'strColumn' => 'id',
				'flagDesc'  => 1,
			);
		}
		$rows = $this->getSearch(array(
			'idModule'  => 'base',
			'numLotNow' => $varsRequest['query']['jsonSearch']['numLotNow'],
			'strTable'  => 'baseTerm',
			'arrOrder'  => $varsRequest['query']['jsonSearch']['ph']['arrOrder'],
			'arrWhere'  => $varsRequest['query']['jsonSearch']['ph']['arrWhere'],
		));

		$vars = $this->_updateSearch(array(
			'vars' => $vars,
			'rows' => $rows,
		));

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

	/**
	 *
	 */
	protected function _updateSearch($arr)
	{
		global $varsRequest;
		global $varsAccount;
		global $classEscape;

		global $classHtml;

		$vars = $arr['vars'];
		$rows = $arr['rows'];

		$array = $rows['arrRows'];
		$arrayNew = array();
		$num = 0;
		$flag = 0;
		foreach ($array as $key => $value) {
			$varsTmpl = $vars['portal']['varsList']['templateDetail'];
			$varsTmpl['id'] = $value['id'];
			$varsTmpl['vars']['idTarget'] = $value['id'];
			$varsTmpl['numSort'] = (int) $key;

			if ($varsAccount['jsonStampCheck']['baseTerm'] < $value['stampRegister']) {
				$flag = 1;
			} else {
				$varsTmpl['strClassLoad'] = '';
			}

			if ($value['id'] == 1 || $value['id'] == 2) {
				$value['stampStart'] = 0;
				$value['stampEnd'] = 0;
			}

			$varsTmpl['strTitle'] = $value['strTitle'];
			$varsTmpl['stampRegister'] = $value['stampRegister'];
			$varsTmpl['stampUpdate'] = $value['stampUpdate'];
			$varsTmpl['stampStart'] = $value['stampStart'];
			$varsTmpl['stampEnd'] = $value['stampEnd'];
			$varsTmpl['arrSpaceStrTag'] = preg_replace('/^ /', '', $value['arrSpaceStrTag']);
			$varsTmpl['flagDefault'] = (int) $value['flagDefault'];
			$varsTmpl['flagCheckboxUse'] = ($value['flagDefault'])? 0 : 1;
			$varsTmpl['varsColumnDetail']['id'] = $varsTmpl['id'];
			$varsTmpl['varsColumnDetail']['strTitle'] = $value['strTitle'];
			$varsTmpl['varsColumnDetail']['stampRegister'] = $value['stampRegister'];
			$varsTmpl['varsColumnDetail']['stampUpdate'] = $value['stampUpdate'];
			$varsTmpl['varsColumnDetail']['stampStart'] = $value['stampStart'];
			$varsTmpl['varsColumnDetail']['stampEnd'] = $value['stampEnd'];

			$varsTmpl['vars']['id'] = $value['id'];
			$varsTmpl['vars']['strTitle'] = $value['strTitle'];
			$varsTmpl['vars']['stampStart'] = $value['stampStart'];
			$varsTmpl['vars']['stampEnd'] = (!$value['stampEnd'])? '' : $value['stampEnd'];
			$varsTmpl['vars']['arrSpaceStrTag'] = $classEscape->splitSpaceArrayData(array('data' => $varsTmpl['arrSpaceStrTag']));
			$arrayColumnDetail = &$varsTmpl['varsColumnDetail'];
			foreach ($arrayColumnDetail as $keyColumnDetail => $valueColumnDetail) {
				if (is_null($valueColumnDetail)) {
					$arrayColumnDetail[$keyColumnDetail] = '';
				}
			}
			$varsTmpl['varsScheduleDetail']['stamp'] = $value['stampRegister'];
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
					'strColumnAccount'    => 'baseTerm',
					'strColumnPreference' => 'accounts',
				));
			}
		}

		return $vars;
	}

	/**
	 *
	 */
	protected function _iniSearchDetail()
	{
		global $varsRequest;
		global $varsPreference;

		if (FLAG_CHECK_UPDATE) {
			$this->checkStampReload(array(
				'stampTarget' => $varsPreference['jsonStampUpdate']['term'],
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
			$varsRequest['query']['jsonSearch']['ph']['arrOrder'] = array(
				'strColumn' => 'id',
				'flagDesc'  => 1,
			);
		}

		$rows = $this->getSearch(array(
			'idModule'  => 'base',
			'numLotNow' => $varsRequest['query']['jsonSearch']['numLotNow'],
			'strTable'  => 'baseTerm',
			'arrOrder'  => $varsRequest['query']['jsonSearch']['ph']['arrOrder'],
			'arrWhere'  => $varsRequest['query']['jsonSearch']['ph']['arrWhere'],
		));

		$vars = $this->_updateSearch(array(
			'vars' => $vars,
			'rows' => $rows,
		));

		$varsTarget = $vars;

		$rowsTarget = $this->getSearch(array(
			'idModule'  => 'base',
			'numLotNow' => 0,
			'strTable'  => 'baseTerm',
			'arrOrder'  => array(),
			'arrWhere'  => array(array(
				'flagType'      => 'num',
				'strColumn'     => 'id',
				'flagCondition' => 'eq',
				'value'         => $varsRequest['query']['jsonValue']['idTarget'],
			)),
		));

		if (!$rowsTarget['numRows']) {
			$this->_sendOldError();
		}

		$varsTarget = $this->_updateSearch(array(
			'vars'     => $varsTarget,
			'rows'     => $rowsTarget,
			'flagVars' => 1,
		));

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
		global $varsAccounts;
		global $varsTerm;
		global $classInit;
		$dbh = $classDb->getHandle();

		$array = $arr['arrId'];

		$this->_checkTableUpdateId(array(
			'varsTable' => &$varsTerm,
			'arrId'     => $array,
		));

		$idNew = 1;

		try {
			$dbh->beginTransaction();


			foreach ($array as $key => $value) {
				$classDb->deleteRow(array(
					'idModule'  => 'base',
					'strTable'  => 'baseTerm',
					'arrWhere'  => array(
						array(
							'flagType'      => 'num',
							'strColumn'     => 'id',
							'flagCondition' => 'eq',
							'value'         => $value,
						),
					),
				));
				$classDb->updateRow(array(
					'idModule'  => 'base',
					'strTable'  => 'baseAccount',
					'arrColumn' => array('idTerm'),
					'arrWhere'  => array(
						array(
							'flagType'      => 'num',
							'strColumn'     => 'idTerm',
							'flagCondition' => 'eq',
							'value'         => $value,
						),
					),
					'arrValue'  => array($idNew),
				));
			}
			$this->updateDbPreferenceStamp(array('strColumn' => 'account'));
			$this->updateDbPreferenceStamp(array('strColumn' => 'term'));

			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

		$classInit->updateVarsAll(array(
			'vars'     => &$varsTerm,
			'strVars'  => 'varsTerm',
			'strTable' => 'baseTerm',
		));

		$classInit->updateVarsAll(array(
			'vars'     => &$varsAccounts,
			'strVars'  => 'varsAccounts',
			'strTable' => 'baseAccount',
		));

		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
		$this->_setSearch(array('flag' => 1));
	}

	/**
	 *
	 */
	protected function _iniDetailReload()
	{
		global $varsTerm;
		global $varsRequest;

		if (!$varsTerm[$varsRequest['query']['jsonValue']['idTarget']]) {
			$varsRequest['query']['jsonSearch']['flagReload'] = 0;
			$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
			$this->_setSearch(array('flag' => 40));
		}

		$this->_iniSearchDetail();
	}

	/**
	 *
	 */
	protected function _iniDetailDelete()
	{
		global $varsTerm;
		global $varsRequest;

		if (!$varsTerm[$varsRequest['query']['jsonValue']['idTarget']]) {
			$varsRequest['query']['jsonSearch']['flagReload'] = 0;
			$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
			$this->_setSearch(array('flag' => 1));
		}

		$this->_setDelete(array(
			'arrId' => array($varsRequest['query']['jsonValue']['idTarget']),
		));
	}

}
