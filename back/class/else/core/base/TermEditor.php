<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Base_TermEditor extends Code_Else_Core_Base_Term
{
	protected $_childSelf = array(
		'pathTplJs'  => 'else/core/base/js/termEditor.js',
		'pathVarsJs' => 'back/tpl/vars/else/core/base/<strLang>/js/termEditor.php',
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
					var_dump(__CLASS__ . '/' .__FUNCTION__);
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
	protected function _iniDetailEdit()
	{
		global $classDb;
		global $classInit;
		$dbh = $classDb->getHandle();

		global $varsAccount;
		global $varsTerm;
		global $varsRequest;

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsTarget['vars']['varsDetail'] = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget['vars']['varsDetail']
		));

		$this->_checkStrTitle(array(
			'strTitle' => $arrValue['arr']['strTitle'],
			'idTarget' => $varsRequest['query']['jsonValue']['idTarget'],
		));

		$arrSql = $this->_updateDbValue(array(
			'arr'      => $arrValue['arr'],
			'flagEdit' => 1,
		));

		if (!$varsTerm[$varsRequest['query']['jsonValue']['idTarget']]) {
			$varsRequest['query']['jsonSearch']['flagReload'] = 0;
			$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
			$this->_setSearch(array('flag' => 40));
		}

		try {
			$dbh->beginTransaction();

			$classDb->updateRow(array(
				'idModule'  => 'base',
				'strTable'  => 'baseTerm',
				'arrColumn' => $arrSql['arrColumn'],
				'arrWhere'  => array(
					array(
						'flagType'      => 'num',
						'strColumn'     => 'id',
						'flagCondition' => 'eq',
						'value'         => $varsRequest['query']['jsonValue']['idTarget'],
					),
				),
				'arrValue'  => $arrSql['arrValue'],
			));

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
		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$this->_iniSearchDetail();
	}

	/**
		$this->_updateDbValue(array(
			'arr'      => array(),
			'flagEdit' => 0,
		));
	 */
	protected function _updateDbValue($arr)
	{
		global $classEscape;
		global $varsAccount;

		$tm = TIMESTAMP;
		$arrSpaceStrTag = $classEscape->splitSpaceArrayData(array('data' => $arr['arr']['arrSpaceStrTag']));
		$arrSpaceStrTag = $classEscape->joinSpaceArray(array('arr' => $arrSpaceStrTag));
		$strTitle = $arr['arr']['strTitle'];

		$numTimeZone = (int) $varsAccount['numTimeZone'];
		list($numYear, $numMonth, $numDate) = preg_split("/\//", $arr['arr']['stampStart']);

		$strTimeZone = (-1 * $numTimeZone) . 'hours';
		$dateTime = new DateTime("$numYear-$numMonth-$numDate 0:0 $strTimeZone", new DateTimeZone("UTC"));
		$stampStart = $dateTime->format('U');
		$stampEnd = 0;
		if ($arr['arr']['stampEnd']) {
			list($numYear, $numMonth, $numDate) = preg_split("/\//", $arr['arr']['stampEnd']);
			$dateTime = new DateTime("$numYear-$numMonth-$numDate 0:0 $strTimeZone", new DateTimeZone("UTC"));
			$stampEnd = $dateTime->format('U') + 86400 - 1;
		}

		if ($arr['flagEdit']) {
			$data = array(
				'arrColumn' => array('strTitle', 'stampStart', 'stampEnd', 'arrSpaceStrTag'),
				'arrValue' => array($strTitle, $stampStart, $stampEnd, $arrSpaceStrTag),
			);

		} else {
			$data = array(
				'arrColumn' => array('stampRegister', 'stampUpdate', 'strTitle', 'stampStart', 'stampEnd', 'arrSpaceStrTag'),
				'arrValue' => array($tm, $tm, $strTitle, $stampStart, $stampEnd, $arrSpaceStrTag),
			);
		}

		return $data;
	}

	/**
		$this->_checkStrTitle(array(
			'strTitle' => '',
			'idTarget' => 0,
		));
	 */
	protected function _checkStrTitle($arr)
	{
		global $varsTerm;

		$array = &$varsTerm;
		foreach ($array as $key => $value) {
			$flag = 0;
			if ($arr['idTarget']) {
				if ($value['strTitle'] == $arr['strTitle'] && $arr['idTarget'] != $value['id']) {
					$flag = 1;
				}

			} else {
				if ($value['strTitle'] == $arr['strTitle']) {
					$flag = 1;
				}

			}

			if ($flag) {
				$this->sendVars(array(
					'flag'    => 'strTitle',
					'stamp'   => $this->getStamp(),
					'numNews' => $this->getNumNews(),
					'vars'    => array(),
				));
			}
		}
	}

	/**
	 *
	 */
	protected function _iniDetailAdd()
	{
		global $classDb;
		global $varsTerm;

		global $classInit;
		global $varsRequest;
		$dbh = $classDb->getHandle();

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsTarget['vars']['varsDetail'] = $this->getValue(array(
			'vars' => $vars['portal']['varsDetail']['templateDetail']
		));

		$arrValue = $this->checkValue(array(
			'values' => $varsTarget['vars']['varsDetail']
		));

		$this->_checkStrTitle(array(
			'strTitle' => $arrValue['arr']['strTitle'],
			'idTarget' => 0,
		));

		$arrSql = $this->_updateDbValue(array(
			'arr' => $arrValue['arr']
		));

		try {
			$dbh->beginTransaction();

			$classDb->insertRow(array(
				'idModule'  => 'Base',
				'strTable'  => 'baseTerm',
				'arrColumn' => $arrSql['arrColumn'],
				'arrValue'  => $arrSql['arrValue'],
			));

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

		$varsRequest['query']['jsonSearch']['flagReload'] = 0;
		$varsRequest['query']['jsonSearch']['numLotNow'] = 0;
		$this->_setSearch(array('flag' => 1));
	}
}
