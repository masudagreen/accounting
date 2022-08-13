<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Base_Log extends Code_Else_Core_Base_Base
{
	protected $_extSelf = array(
		'pathTplJs'  => 'else/core/base/js/log.js',
		'pathVarsJs' => 'back/tpl/vars/else/core/base/<strLang>/js/log.php',
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
			$path = PATH_BACK_CLASS_ELSE_CORE_BASE . 'Log' . $str . ".php";
			if (!file_exists($path)) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
				}
				exit;
			}
			require_once($path);
			$strClass = 'Code_Else_Core_Base_Log' . $str;
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
	 *
	 */
	protected function _iniJs()
	{
		$this->_setJs(array(
			'pathVars'        => $this->_extSelf['pathVarsJs'],
			'pathTpl'         => $this->_extSelf['pathTplJs'],
			'arrSearch'       => array(
				'idModule'  => 'base',
				'numLotNow' => 0,
				'strTable'  => 'baseAccessLog',
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
			'strColumn' => 'jsonLogNaviSearch',
		));
	}

	/**
	 *
	 */
	protected function _iniNaviSearchDelete()
	{
		$this->_setNaviSearchDelete(array(
			'strTable'  => 'baseAccountMemo',
			'strColumn' => 'jsonLogNaviSearch',
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
			'strColumn' => 'jsonLogNaviSearch',
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
			'strTable'  => 'baseAccessLog',
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
				'varsHtml'   => $vars['portal']['varsList']['varsHtml'],
				'varsDetail' => $vars['portal']['varsList']['varsDetail'],
			),
		));
	}

	/**
	 *
	 */
	protected function _updateSearch($arr)
	{
		global $classHtml;

		global $varsRequest;
		global $varsAccount;
		global $varsAccounts;

		$vars = $arr['vars'];
		$rows = $arr['rows'];

		$arrModuleTitle = $this->getStrModuleTitle();

		$array = $rows['arrRows'];
		$arrayNew = array();
		$num = 0;
		$flag = 0;
		foreach ($array as $key => $value) {
			$varsTmpl = $vars['portal']['varsList']['templateDetail'];
			$varsTmpl['id'] = $value['id'];
			$varsTmpl['strTitle'] = $value['id'];
			$varsTmpl['vars']['idTarget'] = $value['id'];
			$varsTmpl['numSort'] = (int) $key;

			if ($varsAccount['jsonStampCheck']['baseLog'] < $value['stampRegister']) {
				$flag = 1;
			} else {
				$varsTmpl['strClassLoad'] = '';
			}
			$varsTmpl['stampRegister'] = $value['stampRegister'];

			$varsTmpl['varsColumnDetail']['id'] = $varsTmpl['id'];
			$varsTmpl['varsColumnDetail']['strCodeName'] = ($varsAccounts[$value['idAccount']])? $varsAccounts[$value['idAccount']]['strCodeName'] : '-';
			$varsTmpl['varsColumnDetail']['stampRegister'] = $value['stampRegister'];
			$varsTmpl['varsColumnDetail']['idAccount'] = ($value['idAccount'])? $value['idAccount'] : '-';
			$varsTmpl['varsColumnDetail']['idModule'] = ($value['idModule'])? $value['idModule'] : '-';


			$varsTmpl['varsColumnDetail']['strExt'] = ($value['strExt'])? $value['strExt'] : '-';
			$varsTmpl['varsColumnDetail']['strChild'] = ($value['strChild'])? $value['strChild'] : '-';
			$varsTmpl['varsColumnDetail']['strFunc'] = ($value['strFunc'])? $value['strFunc'] : '-';
			$varsTmpl['varsColumnDetail']['ip'] = $value['ip'];
			$varsTmpl['varsColumnDetail']['strHost'] = ($value['strHost'])? $value['strHost'] : '-';
			$varsTmpl['varsColumnDetail']['strDbType'] = $value['strDbType'];

			$varsTmpl['vars']['id'] = $varsTmpl['id'];
			$varsTmpl['flagDefault'] = ($value['jsonQuery'])? 0 : 1;
			$varsTmpl['vars']['idAccount'] = $value['idAccount'];
			$varsTmpl['vars']['idModule'] = $value['idModule'];
			$varsTmpl['vars']['strExt'] = $value['strExt'];
			$varsTmpl['vars']['strChild'] = $value['strChild'];
			$varsTmpl['vars']['strFunc'] = $value['strFunc'];
			$varsTmpl['vars']['ip'] = $value['ip'];
			$varsTmpl['vars']['strHost'] = $value['strHost'];
			$varsTmpl['vars']['strDbType'] = $value['strDbType'];
			$varsTmpl['vars']['strCodeName'] = $varsTmpl['varsColumnDetail']['strCodeName'];
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
					'strColumnAccount'    => 'baseLog',
					'strColumnPreference' => 'accounts',
				));
			}
		}

		return $vars;
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
		$this->_iniSearchDetail();
	}
}
