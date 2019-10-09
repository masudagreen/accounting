<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_NotesFSOutput extends Code_Else_Plugin_Accounting_Jpn_NotesFS
{
	protected $_childSelf = array(
		'pathVarsPrint' => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/printNotesFS.php',
	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;

		$this->_checkEntity();

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
	protected function _iniDetailPrint()
	{
		global $classRequest;

		global $varsRequest;
		global $varsPluginAccountingAccount;

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'output',
			'idTarget'      => $this->_extSelf['idPreference'],
		));

		if (!$flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}

		$flagCurrentFlagNow = $this->_getCurrentFlagNow(array());
		if (preg_match("/^(tempNext)$/", $flagCurrentFlagNow)) {
			$this->_sendOld();
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$varsItem = $this->_updateVarsItem(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$varsPrint = $this->_getVarsPrint(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$this->sendVars(array(
			'flag'    => 1,
			'stamp'   => $this->getStamp(),
			'numNews' => $this->getNumNews(),
			'vars'    => $varsPrint,
		));
	}

	/**
		(array(
			'strFileType'   => '',
			'varsFlag'   => $arr['varsFlag'],
			'varsItem'   => $arr['varsItem'],
			'vars'       => $arr['vars'],
		))

	 */
	protected function _updateVarsItem($arr)
	{
		$varsPrintItem = $this->_getVarsPrintItem();
		$arr['varsItem']['varsPrintItem'] = $varsPrintItem;

		return $arr['varsItem'];
	}

	/**
		(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
			'varsItem' => $varsItem,
		))

	 */
	protected function _getVarsPrint($arr)
	{
		$varsPrint = array();
		$arr['vars'] = $this->_updateVars(array(
			'vars'     => $arr['vars'],
			'varsItem' => $arr['varsItem'],
		));
		$varsPrint = $this->_getVarsPrintLoop(array(
			'vars'       => $arr['vars'],
			'varsPrint'  => $varsPrint,
			'varsItem'   => $arr['varsItem'],
			'strComment' => $arr['vars']['portal']['varsDetail']['varsDetail']['strComment'],
		));

		return $varsPrint;
	}

	/**

	 */
	protected function _getVarsPrintItem()
	{
		$vars = $this->getVars(array(
			'path' => $this->_childSelf['pathVarsPrint'],
		));

		return $vars;
	}

	/**
		(array(
			'vars'       => $arr['vars'],
			'varsPrint'  => $varsPrint,
			'varsItem'   => $arr['varsItem'],
			'strComment' => $arr['vars']['portal']['varsDetail']['varsDetail']['strComment'],
		))
	 */
	protected function _getVarsPrintLoop($arr)
	{
		$varsData = array();

		$varsData = $this->_getVarsStatus(array(
			'vars'     => $arr['vars'],
			'varsItem' => $arr['varsItem'],
		));

		$varsPrint = $arr['varsPrint'];
		if (!$varsPrint) {
			$varsPrint = $arr['vars']['varsPrint'];
			$varsPrint['varsStatus'] = $this->_getVarsStatusPrint(array(
				'varsData'   => $varsData,
				'vars'       => $arr['vars'],
				'varsPrint'  => $varsPrint,
				'varsItem'   => $arr['varsItem'],
			));
		}

		$varsPrint = $this->_getVarsLoopPrint(array(
			'varsData'   => $varsData,
			'varsPrint'  => $varsPrint,
			'vars'       => $arr['vars'],
			'strComment' => $arr['strComment'],
			'varsItem'   => $arr['varsItem'],
		));

		return $varsPrint;
	}

	/**
		(array(
			'vars'     => $arr['vars'],
			'varsFlag' => $arr['varsFlag'],
			'varsItem' => $arr['varsItem'],
		))
	 */
	protected function _getVarsStatus($arr)
	{
		global $varsPluginAccountingEntity;
		global $varsPluginAccountingAccount;

		$idEntity = $varsPluginAccountingAccount['idEntityCurrent'];

		$varsData = $arr['vars']['varsItem']['varsOutput'];

		//strEntity
		$strEntity = $varsPluginAccountingEntity[$idEntity]['strTitle'];
		$varsData['strEntityExt'] = str_replace('<%replace%>', $strEntity, $arr['vars']['varsItem']['varsOutput']['strEntityExt']);
		$varsData['strEntity'] = $strEntity;

		//strNum
		$strNumRep = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];
		$varsData['strNum'] = str_replace('<%replace%>', $strNumRep, $arr['vars']['varsItem']['varsOutput']['strNum']);
		$varsData['strNumExt'] = str_replace('<%replace%>', $strNumRep, $arr['vars']['varsItem']['varsOutput']['strNumExt']);

		//strPeriod
		$varsPeriod = $this->_getVarsFiscalPeriod(array(
			'flagFiscalPeriod' => 'f1',
			'numFiscalPeriod'  => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
		));

		/*20190401 start*/
		/*
		 $str = $arr['vars']['varsItem']['varsOutput']['strPeriodExt'];
		 $strPeriod = str_replace('<%strStartHeisei%>', $varsPeriod['numStartHeisei'], $str);
		 $strPeriod = str_replace('<%strEndHeisei%>', $varsPeriod['numEndHeisei'], $strPeriod);
		 */
		$str = $arr['vars']['varsItem']['varsOutput']['strPeriodExt20190401'];
		$strPeriod = str_replace('<%strStartNengoYear%>', $varsPeriod['strStartNengoYear'], $str);
		$strPeriod = str_replace('<%strEndNengoYear%>', $varsPeriod['strEndNengoYear'], $strPeriod);
		/*20190401 end*/
		$strPeriod = str_replace('<%strStartMonth%>', $varsPeriod['numStartMonth'], $strPeriod);
		$strPeriod = str_replace('<%strEndMonth%>', $varsPeriod['numEndMonth'], $strPeriod);
		$varsData['strPeriodExt'] = $strPeriod;
		//

		$varsData['strPeriodSub'] = $varsData['strPeriodExt'];

		$varsData['strTitleSub'] = $strEntity . '(' . $varsData['strNum'] . ') ';

		return $varsData;
	}

	/**
		(array(
			'varsData'   => $varsData,
			'varsPrint'  => $varsPrint,
			'vars'       => $arr['vars'],
			'strComment' => $arr['strComment'],
			'varsItem'   => $arr['varsItem'],
		))

	 */
	protected function _getVarsLoopPrint($arr)
	{
		$varsPrint = &$arr['varsPrint'];
		$varsPrintItem = $arr['varsItem']['varsPrintItem'];

		$array = array(
			array('before' => "\r\n", 'after' => "\n"),
			array('before' => "\r",   'after' => "\n"),
		);
		$strComment = $arr['strComment'];
		foreach ($array as $key => $value) {
			$strComment = mb_ereg_replace($value['before'], $value['after'], $strComment);
		}

		$num = 0;
		$array = preg_split( "/\n/", $strComment);

		foreach ($array as $key => $value) {
			$tmplRow = $varsPrint['varsDetailTmpl'];
			$tmplRow['id'] = $num;
			$tmplRow['numTr'] = 1;
			$tmplRow['strRow'] = $this->_getVarsHtml(array(
				'varsData' => $arr['varsData'],
				'value'    => array('strComment' => $value),
				'tmplStr'  => $varsPrintItem['tmplRow']['tmplTr1'],
			));
			$varsPrint['varsDetail'][] = $tmplRow;
			$num++;
		}

		return $varsPrint;
	}

	/**
		(array(
			'varsData'   => $varsData,
			'vars'       => $arr['vars'],
			'varsPrint'  => $varsPrint,
			'varsItem'   => $arr['varsItem'],
		))
	 */
	protected function _getVarsStatusPrint($arr)
	{
		$varsPrint = $arr['varsPrint'];
		$varsPrintItem = $arr['varsItem']['varsPrintItem'];

		//tmplWrap
		$varsPrint['varsStatus']['varsTmpl']['tmplWrap'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'tmplStr'  => $varsPrintItem['tmplWrap'],
		));

		$varsPrint['varsStatus']['varsTmpl']['tmplColumn'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'tmplStr'  => $varsPrintItem['tmplColumn'],
		));

		$varsPrint['varsStatus']['varsTmpl']['tmplTable'] = $this->_getVarsHtml(array(
			'varsData' => $arr['varsData'],
			'tmplStr'  => $varsPrintItem['tmplTable'],
		));

		$varsPrint['varsStatus']['varsTmpl']['tmplPage'] = $varsPrintItem['tmplPage'];

		$varsPrint['varsStatus']['strTitle'] = $this->_getStrTitle(array(
			'varsFlag' => $arr['varsFlag'],
			'varsItem' => $arr['varsItem'],
			'vars'     => $arr['vars'],
		));

		return $varsPrint['varsStatus'];
	}

	/**
		(array(
			'strFileType'   => '',
			'varsFlag'   => $arr['varsFlag'],
			'varsItem'   => $arr['varsItem'],
			'vars'       => $arr['vars'],
		))

	 */
	protected function _getStrTitle($arr)
	{
		$strMenu = $arr['vars']['varsItem']['varsOutput']['strTitleFile'];

		$strFileName = $this->_getFileTitle(array(
			'strMenu'     => $strMenu,
			'strFileType' => $arr['strFileType'],
		));

		return $strFileName;
	}

	/**
	 *
	 */
	protected function _iniDetailOutput()
	{
		global $classRequest;

		global $varsRequest;
		global $varsPluginAccountingAccount;

		$flag = $this->_checkAccess(array(
			'flagAllUse'    => 1,
			'flagAuthority' => 'output',
			'idTarget'      => $this->_extSelf['idPreference'],
		));

		if (!$flag) {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}

		$vars = $this->getVars(array(
			'path' => $this->_extSelf['pathVarsJs'],
		));

		$varsItem = $this->_getVarsItem(array(
			'vars' => $vars,
		));

		$varsItem = $this->_updateVarsItem(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$text = $this->_getCsv(array(
			'vars'      => $vars,
			'varsItem'  => $varsItem,
		));

		$strFileName = $this->_getStrTitle(array(
			'varsItem'    => $varsItem,
			'vars'        => $vars,
			'strFileType' => 'csv',
		));
		$text = mb_convert_encoding($text, 'sjis', 'utf8');
		$classRequest->output(array(
			'text'         => $text,
			'strFileType'  => 'csv',
			'strFileName'  => $strFileName,
		));
	}

	/**
		(array(
			'vars'     => $vars,
			'varsFlag' => $varsFlag,
			'varsItem' => $varsItem,
		))

	 */
	protected function _getCsv($arr)
	{
		global $classFile;

		$arrayCsv = array();

		$arr['vars'] = $this->_updateVars(array(
			'vars'     => $arr['vars'],
			'varsItem' => $arr['varsItem'],
		));

		$arrayCsv = $this->_getVarsCsvLoop(array(
			'vars'       => $arr['vars'],
			'arrayCsv'   => $arrayCsv,
			'varsItem'   => $arr['varsItem'],
			'strComment' => $arr['vars']['portal']['varsDetail']['varsDetail']['strComment'],
		));

		$text = $classFile->getCsvText(array(
			'delimiter' => ',',
			'rows'      => $arrayCsv,
		));

		return $text;
	}

	/**
		(array(
			'flagCount'  => $flagCount,
			'vars'       => $arr['vars'],
			'arrayCsv'   => $arrayCsv,
			'varsItem'   => $arr['varsItem'],
			'strComment' => $arr['vars']['portal']['varsDetail']['varsDetail']['strComment'],
		))
	 */
	protected function _getVarsCsvLoop($arr)
	{
		$varsData = array();

		$varsData = $this->_getVarsStatus(array(
			'vars'     => $arr['vars'],
			'varsItem' => $arr['varsItem'],
		));

		$arrayCsv = $this->_getVarsLoopCsv(array(
			'varsData'   => $varsData,
			'arrayCsv'   => $arr['arrayCsv'],
			'vars'       => $arr['vars'],
			'varsItem'   => $arr['varsItem'],
			'strComment' => $arr['strComment'],
		));

		return $arrayCsv;
	}

	/**
		(array(
			'varsData'   => $varsData,
			'arrayCsv'   => $arr['arrayCsv'],
			'vars'       => $arr['vars'],
			'varsItem'   => $arr['varsItem'],
			'strComment' => $arr['strComment'],
		))
	 */
	protected function _getVarsLoopCsv($arr)
	{
		global $classEscape;

		$arrayCsv = &$arr['arrayCsv'];

		$arrayCsv[] = array($arr['varsData']['strTitle']);
		$arrayCsv[] = array($arr['varsData']['strPeriodSub']);
		$arrayCsv[] = array($classEscape->toComma(array('data' => $arr['varsData']['strTitleSub'])));
		$arrayCsv[] = array($classEscape->toComma(array('data' => $arr['strComment'])));

		return $arrayCsv;
	}
}
