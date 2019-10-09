<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Jpn_BlueSheetOutput_2012_Public extends Code_Else_Plugin_Accounting_Jpn_BlueSheet_2012_Public
{
	protected $_extSelf = array(
		'idPreference'          => 'blueSheetWindow',
		'numYearSheet'          => '2014',
		'pathTplJs'             => 'else/plugin/accounting/js/jpn/2012/public/blueSheet.js',
		'pathVarsJs'            => 'back/tpl/vars/else/plugin/accounting/<strLang>/js/jpn/2012/public/blueSheet.php',
		'varsFixedAssetsOption' => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/depreciation.php',
		'pathItem'              => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/2012/public/blueSheet.php',
		'pathItemZeimusho'      => 'back/tpl/vars/else/plugin/accounting/<strLang>/dat/jpn/2012/public/zeimushoList.csv',
		'pathTplHtml'           => 'else/plugin/accounting/html/2012/public/blueSheet<%replace%>.html',
	);

	protected $_childSelf = array(
		'flagCR' => 0,
		'zeimusho_CD' => '',
		'TEISYUTSU_DAY_gen_yy' => '',
		'TEISYUTSU_DAY_gen_mm' => '',
		'TEISYUTSU_DAY_gen_dd' => '',
		'NOZEISHA_ID' => '',

		//page1
		'AMF00100' => 0,//売上（収入）金額 salesSum
		'AMF00120' => 0,//期首商品（製品）棚卸高 goodsOpeningInventory
		'AMF00130' => 0,//仕入金額（製品製造原価） goodsPurcheses
		'AMF00140' => 0,//小計goodsDebitNet
		'AMF00150' => 0,//期末商品（製品）棚卸高 goodsClosingInventory
		'AMF00160' => 0,//差引原価 goodsSum
		'AMF00170' => 0,//差引金額１ grossProfitNet
		'AMF00190' => 0,//租税公課 taxesAndDues
		'AMF00200' => 0,//荷造運賃 packingAndFreight
		'AMF00210' => 0,//水道光熱費 waterPowerExpenses
		'AMF00220' => 0,//旅費交通費 transportationExpenses
		'AMF00230' => 0,//通信費 correspondenceExpenses
		'AMF00240' => 0,//広告宣伝費 advertisingExpenses
		'AMF00250' => 0,//接待交際費 entertainmentExpenses
		'AMF00260' => 0,//損害保険料 insuranceExpenses
		'AMF00270' => 0,//修繕費 repair
		'AMF00280' => 0,//消耗品費 suppliesExpenses
		'AMF00290' => 0,//減価償却費 depreciation
		'AMF00300' => 0,//福利厚生費 welfareExpenses
		'AMF00310' => 0,//給料賃金 employeeSalariesAndAllowances
		'AMF00320' => 0,//外注工賃 outsourcingExpenses
		'AMF00330' => 0,//利子割引料 commissionPaid
		'AMF00340' => 0,//地代家賃 rents
		'AMF00350' => 0,//貸倒金 provisionOfAllowanceForDoubtfulAccounts
		'AMF00355' => array(),
		'AMF00370' => 0,//雑費 miscellaneousExpenses
		'AMF00380' => 0,//計 sellingGeneralAndAdministrationExpensesSum
		'AMF00390' => 0,//差引金額２ operatingIncomeProfitOrLossNet
		'AMF00420' => 0,//貸倒引当金 reversalOfAllowanceForDoubtfulReceivables
		'AMF00425' => array(),
		'AMF00440' => 0,//計 reserveFundInElseSum
		'AMF00460' => 0,//専従者給与 familyEmployee
		'AMF00470' => 0,//貸倒引当金 provisionOfAllowanceForDoubtfulAccountsNon
		'AMF00475' => array(),
		'AMF00490' => 0,//計 reserveFundOutElseSum
		'AMF00500' => 0,//青色申告特別控除前の所得金額 currentTermProfitOrLossNet
		'AMF00510' => 0,//青色申告特別控除額 blueTax
		'AMF00530' => 0,//所得金額 blueNet

		//page2 損益計算書内訳（個人氏名）
		'AMF00600' => 0,//１月	売上（収入）金額
		'AMF00610' => 0,//１月	仕入金額
		'AMF00630' => 0,//2月	売上（収入）金額
		'AMF00640' => 0,//2月	仕入金額
		'AMF00660' => 0,//3月	売上（収入）金額
		'AMF00670' => 0,//3月	仕入金額
		'AMF00690' => 0,//4月	売上（収入）金額
		'AMF00700' => 0,//4月	仕入金額
		'AMF00720' => 0,//5月	売上（収入）金額
		'AMF00730' => 0,//5月	仕入金額
		'AMF00750' => 0,//6月	売上（収入）金額
		'AMF00760' => 0,//6月	仕入金額
		'AMF00780' => 0,//7月	売上（収入）金額
		'AMF00790' => 0,//7月	仕入金額
		'AMF00810' => 0,//8月	売上（収入）金額
		'AMF00820' => 0,//8月	仕入金額
		'AMF00840' => 0,//9月	売上（収入）金額
		'AMF00850' => 0,//9月	仕入金額
		'AMF00870' => 0,//10月	売上（収入）金額
		'AMF00880' => 0,//10月	仕入金額
		'AMF00900' => 0,//11月	売上（収入）金額
		'AMF00910' => 0,//11月	仕入金額
		'AMF00930' => 0,//12月	売上（収入）金額
		'AMF00940' => 0,//12月	仕入金額
		'AMF00950' => 0,//家事消費等（売上（収入）金額） selfConsumption
		'AMF00960' => 0,//雑収入（売上（収入）金額） miscellaneousIncome
		'AMF00980' => 0,//月別売上（収入）金額及び仕入金額（計）	売上（収入）金額
		'AMF00990' => 0,//仕入金額
		'AMF01010' => 0,//個別評価による本年分繰入額
		'AMF01030' => 0,//年末における一括評価による貸倒引当金の繰入れの対象となる貸金の合計額
		'AMF01040' => 0,//本年分繰入限度額
		'AMF01050' => 0,//本年分繰入額
		'AMF01060' => 0,//本年分の貸倒引当金繰入額
		'AMF01510' => 0,//本年分の不動産所得の金額
		'AMF01520' => 0,//青色申告特別控除前の所得金額
		'AMF01530' => 0,//65万円青色申告特別控除額flag

		'AMF01540' => 0,//65万円と（６）のいずれか少ない方の金額
		'AMF01550' => 0,//青色申告特別控除額
		'AMF01570' => 0,//上記以外の場合：10万円と（６）のいずれか少ない方の金額
		'AMF01580' => 0,//上記以外の場合：青色申告特別控除額

		//page 3
		'AMF01600' => array(
			/*array(
				'AMF01610' => '',//減価償却費の計算	減価償却資産の名称等
				'AMF01620' => 0,//面積又は数量
				'AMF01630_gen_era' => 4,//減価償却費の計算（取得年月）	元号
				'AMF01630_gen_yy' => 1,
				'AMF01630_gen_mm' => 1,
				'AMF01640' => 0,//減価償却費の計算	取得価額
				'AMF01645' => 0,//（償却保証額）
				'AMF01650' => 0,//償却の基礎になる金額
				'AMF01660' => '',//償却方法
				'AMF01670' => 2,//耐用年数
				'AMF01690' => 0,//償却率又は改定償却率
				'AMF01720' => 12,//本年中の償却期間
				'AMF01730' => 0,//本年分の普通償却費
				'AMF01740' => 0,//割増（特別）償却費
				'AMF01750' => 0,//本年分の償却費合計
				'AMF01760' => 0,//事業専用割合
				'AMF01770' => 0,//本年分の必要経費算入額
				'AMF01780' => 0,//未償却残高
			),*/
		),
		'AMF01810' => 0,//減価償却費の計算（計）	本年分の普通償却費
		'AMF01820' => 0,//割増（特別）償却費
		'AMF01830' => 0,//本年分の償却費合計
		'AMF01840' => 0,//本年分の必要経費算入額
		'AMF01850' => 0,//未償却残高
		'AMF01910' => 0,//期末現在の借入金等の金額
		'AMF01920' => 0,//本年中の利子割引料
		'AMF01930' => 0,//左のうち必要経費算入額
		'AMF02000' => 0,//権利金
		'AMF02010' => 0,//更新料
		'AMF02020' => 0,//賃借料
		'AMF02030' => 0,//左の賃借料のうち必要経費算入額
		'AMF02080' => 0,//本年中の報酬等の金額
		'AMF02090' => 0,//左のうち必要経費算入額
		'AMF02100' => 0,//所得税及び復興特別所得税の源泉徴収税額

		//page4 貸借対照表（期末年月日）
		'AMG00025' => array(
			/*array(
				'AMG00030' => '',//資産の部（追加科目名）
				'AMG00220' => 0,//追加科目の金額
				'AMF00420' => 0,//追加科目の金額
			),*/
		),
		'AMG00060' => 0,//現金 cash
		'AMG00070' => 0,//当座預金 checkingAccounts
		'AMG00080' => 0,//定期預金 fixedDeposit
		'AMG00090' => 0,//その他の預金 depositElse
		'AMG00100' => 0,//受取手形 notesReceivable
		'AMG00110' => 0,//売掛金 accountsReceivable
		'AMG00120' => 0,//有価証券 securities
		'AMG00130' => 0,//棚卸資産 inventries
		'AMG00140' => 0,//前払金 advancesAccount
		'AMG00150' => 0,//貸付金 loansReceivable
		'AMG00160' => 0,//建物 buildings
		'AMG00170' => 0,//建物附属設備 buildingsAndAccessoyEquipment
		'AMG00180' => 0,//機械装置 machineryAndEquipment
		'AMG00190' => 0,//車両運搬具 car
		'AMG00200' => 0,//工具　器具　備品 furnitureAndFixture
		'AMG00210' => 0,//土地 land
		'AMG00230' => 0,//合計 assetsSum

		'AMG00260' => 0,//現金
		'AMG00270' => 0,//当座預金
		'AMG00280' => 0,//定期預金
		'AMG00290' => 0,//その他の預金
		'AMG00300' => 0,//受取手形
		'AMG00310' => 0,//売掛金
		'AMG00320' => 0,//有価証券
		'AMG00330' => 0,//棚卸資産
		'AMG00340' => 0,//前払金
		'AMG00350' => 0,//貸付金
		'AMG00360' => 0,//建物
		'AMG00370' => 0,//建物附属設備
		'AMG00380' => 0,//機械装置
		'AMG00390' => 0,//車両運搬具
		'AMG00400' => 0,//工具　器具　備品
		'AMG00410' => 0,//土地
		'AMG00430' => 0,//事業主貸 accountsReceivables
		'AMG00440' => 0,//合計 assetsSum

		'AMG00465' => array(
			/*
			array(
				'AMG00470' => '',//負債・資本の部（追加科目名１）
				'AMG00570' => 0,//追加科目の金額１
				'AMG00700' => 0,//追加科目の金額１
			),*/
		),

		'AMG00510' => 0,//支払手形 notesPayable
		'AMG00520' => 0,//買掛金 accountsAmountPayable
		'AMG00530' => 0,//借入金 loansPayable
		'AMG00540' => 0,//未払金 accruedAmountPayable
		'AMG00550' => 0,//前受金 advancesByCustomers
		'AMG00560' => 0,//預り金 depositePayable
		'AMG00580' => 0,//貸倒引当金 allowanceForBadDebtsTrade
		'AMG00600' => 0,//元入金 profitBroughtForward
		'AMG00610' => 0,//合計

		'AMG00640' => 0,//支払手形
		'AMG00650' => 0,//買掛金
		'AMG00660' => 0,//借入金
		'AMG00670' => 0,//未払金
		'AMG00680' => 0,//前受金
		'AMG00690' => 0,//預り金
		'AMG00710' => 0,//貸倒引当金
		'AMG00730' => 0,//事業主借 accountsPayables
		'AMG00740' => 0,//元入金
		'AMG00750' => 0,//青色申告特別控除前の所得金額 unappropriatedRetainedEarnings
		'AMG00760' => 0,//合計 liabilitiesNetAssetsSum

		//期首原材料棚卸高 materialsCostOpeningInventory
		'AMH00030' => 0,
		//原材料仕入高 materialsCostPurchase
		'AMH00040' => 0,
		//小計 materialsCostDebitNet
		'AMH00050' => 0,
		//期末原材料棚卸高  materialsCostClosingInventory
		'AMH00060' => 0,
		//差引原材料費 materialsCostSum
		'AMH00070' => 0,
		//労務費 laborCost
		'AMH00080' => 0,
		//外注工賃 manufactureOutsourcingManufactueingExpenses
		'AMH00100' => 0,
		//電力費 manufacturePowerExpense
		'AMH00110' => 0,
		//水道光熱費 manufactureWaterPowerExpenses
		'AMH00120' => 0,
		//修繕費 manufactureRepair
		'AMH00130' => 0,
		//減価償却費 manufactureDepreciation
		'AMH00140' => 0,
		//雑費 manufactureMiscellaneousExpenses
		'AMH00160' => 0,
		//計 manufactureCostSum
		'AMH00170' => 0,
		//総製造費 grossProductCostNet
		'AMH00180' => 0,
		//期首半製品・仕掛品棚卸高  workInProcessOpeningInventory
		'AMH00190' => 0,
		//小計 workInProcessOpeningInventoryWrapNet
		'AMH00200' => 0,
		//期末半製品・仕掛品棚卸高  workInProcessClosingInventory
		'AMH00210' => 0,
		//製品製造原価 currentWorkInProcessNet
		'AMH00220' => 0,

	);

	/**
	 *
	 */
	public function run()
	{
		global $varsRequest;
		global $varsPluginAccountingAccount;

		$varsFiscalPeriod = $this->_getVarsFiscalPeriod(array(
			'numFiscalPeriod' => $varsPluginAccountingAccount['numFiscalPeriodCurrent'],
			'flagFiscalPeriod' => 'f1',
		));

		if ($varsFiscalPeriod['numStartYear'] == 2015) {
			$this->_extSelf['numYearSheet'] = '2015';

		} elseif ($varsFiscalPeriod['numStartYear'] == 2016) {
			$this->_extSelf['numYearSheet'] = '2016';

		} elseif ($varsFiscalPeriod['numStartYear'] == 2017) {
			$this->_extSelf['numYearSheet'] = '2017';

		} elseif ($varsFiscalPeriod['numStartYear'] >= 2018) {
		    $this->_extSelf['numYearSheet'] = '2018';
		}

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

		$this->_setVarsValue(array(
			'vars'     => $vars,
			'varsItem' => $varsItem,
		));

		$domDoc = new DOMDocument('1.0', "UTF-8");
		$domDoc->preserveWhiteSpace = false;
		$domDoc->formatOutput = true;

		$temp = $this->_loopVars(array(
			'domDoc' => $domDoc,
		));
		$text = $temp['domDoc']->saveXML();

		$classRequest->output(array(
			'text'         => $text,
			'strFileType'  => 'xtx',
			'strFileName'  => 'aoiro.xtx',
		));
	}

	/**
		(array(
			'vars'     => $vars,
		))
	 */
	protected function _setVarsValue($arr)
	{
		$this->_setVarsValue_save($arr);

		//1pageではなく2pageから先に計算
		$this->_setVarsValue_2($arr);

		$this->_setVarsValue_1($arr);

		$this->_setVarsValue_4_BS($arr);
		if ($this->_childSelf['flagCR']) {
			$this->_setVarsValue_4_CR($arr);
		}
		$this->_setVarsValue_3($arr);
	}/**

	 */
	protected function _setVarsValue_save($arr)
	{
		global $classTime;

		$this->_childSelf['flagCR'] = $arr['varsItem']['varsEntityNation']['flagCR'];

		$this->_childSelf['NOZEISHA_ID'] = $arr['varsItem']['varsSave']['jsonData']['0']['valueNozeisha_id'];
		$this->_childSelf['zeimusho_CD'] = $arr['varsItem']['varsSave']['jsonData']['0']['valueSelectTypeZeimusho'];

		$arrDate = $classTime->getLocal(array('stamp' => TIMESTAMP));
		$numYear = $classTime->getNengoYear(array('stamp' => TIMESTAMP, 'numYear' => $arrDate['year']));
		$this->_childSelf['TEISYUTSU_DAY_gen_yy'] = $numYear;
		$this->_childSelf['TEISYUTSU_DAY_gen_mm'] = $arrDate['month'];
		$this->_childSelf['TEISYUTSU_DAY_gen_dd'] = $arrDate['date'];

		$this->_childSelf['AMF01530'] = (int) $arr['varsItem']['varsSave']['jsonData']['0']['valueSelectTypeKoujo'];
	}

	/**

	 */
	protected function _setVarsValue_1($arr)
	{
		$varsValue = $arr['varsItem']['varsFSValue']['jsonJgaapFSPL']['f1'];

		//売上（収入）金額
		$this->_childSelf['AMF00100'] = $this->_getNumValue($varsValue['salesSum']['sumNext']);

		//期首商品（製品）棚卸高
		$this->_childSelf['AMF00120'] = $this->_getNumValue($varsValue['goodsOpeningInventory']['sumNext']);

		//仕入金額（製品製造原価）
		$this->_childSelf['AMF00130'] = $this->_getNumValue($varsValue['goodsPurcheses']['sumNext']);

		//小計
		$this->_childSelf['AMF00140'] = $this->_getNumValue($varsValue['goodsDebitNet']['sumNext']);

		//期末商品（製品）棚卸高
		$this->_childSelf['AMF00150'] = $this->_getNumValue($varsValue['goodsClosingInventory']['sumNext']);

		//差引原価
		$this->_childSelf['AMF00160'] = $this->_getNumValue($varsValue['goodsSum']['sumNext']);

		//差引金額１
		$this->_childSelf['AMF00170'] = $this->_getNumValue($varsValue['grossProfitNet']['sumNext']);

		//租税公課
		$this->_childSelf['AMF00190'] = $this->_getNumValue($varsValue['taxesAndDues']['sumNext']);

		//荷造運賃
		$this->_childSelf['AMF00200'] = $this->_getNumValue($varsValue['packingAndFreight']['sumNext']);

		//水道光熱費
		$this->_childSelf['AMF00210'] = $this->_getNumValue($varsValue['waterPowerExpenses']['sumNext']);

		//旅費交通費
		$this->_childSelf['AMF00220'] = $this->_getNumValue($varsValue['transportationExpenses']['sumNext']);

		//通信費
		$this->_childSelf['AMF00230'] = $this->_getNumValue($varsValue['correspondenceExpenses']['sumNext']);

		//広告宣伝費
		$this->_childSelf['AMF00240'] = $this->_getNumValue($varsValue['advertisingExpenses']['sumNext']);

		//接待交際費
		$this->_childSelf['AMF00250'] = $this->_getNumValue($varsValue['entertainmentExpenses']['sumNext']);

		//損害保険料
		$this->_childSelf['AMF00260'] = $this->_getNumValue($varsValue['insuranceExpenses']['sumNext']);

		//修繕費
		$this->_childSelf['AMF00270'] = $this->_getNumValue($varsValue['repair']['sumNext']);

		//消耗品費
		$this->_childSelf['AMF00280'] = $this->_getNumValue($varsValue['suppliesExpenses']['sumNext']);

		//減価償却費
		$this->_childSelf['AMF00290'] = $this->_getNumValue($varsValue['depreciation']['sumNext']);

		//福利厚生費
		$this->_childSelf['AMF00300'] = $this->_getNumValue($varsValue['welfareExpenses']['sumNext']);

		//給料賃金
		$this->_childSelf['AMF00310'] = $this->_getNumValue($varsValue['employeeSalariesAndAllowances']['sumNext']);

		//外注工賃
		$this->_childSelf['AMF00320'] = $this->_getNumValue($varsValue['outsourcingExpenses']['sumNext']);

		//利子割引料
		$this->_childSelf['AMF00330'] = $this->_getNumValue($varsValue['commissionPaid']['sumNext']);

		//地代家賃
		$this->_childSelf['AMF00340'] = $this->_getNumValue($varsValue['rents']['sumNext']);

		//貸倒金
		$this->_childSelf['AMF00350'] = $this->_getNumValue($varsValue['provisionOfAllowanceForDoubtfulAccounts']['sumNext']);

		//追加科目
		$this->_childSelf['AMF00355'] = array();
		for ($i=1;$i<=5;$i++) {
			$idTarget = 'blankPL' . $i;
			$strTitle = $arr['varsItem']['varsJgaapFS']['jsonJgaapFSPL']['arrStrTitle'][$idTarget]['strTitle'];
			if ($strTitle != '' || ($strTitle == '' && $varsValue[$idTarget]['sumNext'] != 0)) {
				$this->_childSelf['AMF00355'][] = array(
					'AMF00060' => ($strTitle != '')? $strTitle : '',
					'AMF00360' => $this->_getNumValue($varsValue[$idTarget]['sumNext'])
				);
			}
		}

		//追加科目 その他の経費 追加だけど固定として扱っている
		$idTarget = 'sellingGeneralAndAdministrationExpensesElse';
		$strTitle = $arr['varsItem']['varsJgaapFS']['jsonJgaapFSPL']['arrStrTitle'][$idTarget]['strTitle'];
		$this->_childSelf['AMF00355'][] = array(
			'AMF00060' => ($strTitle != '')? $strTitle : '',
			'AMF00360' => $this->_getNumValue($varsValue[$idTarget]['sumNext'])
		);

		//雑費
		$this->_childSelf['AMF00370'] = $this->_getNumValue($varsValue['miscellaneousExpenses']['sumNext']);

		//計
		$this->_childSelf['AMF00380'] = $this->_getNumValue($varsValue['sellingGeneralAndAdministrationExpensesSum']['sumNext']);

		//差引金額２
		$this->_childSelf['AMF00390'] = $this->_getNumValue($varsValue['operatingIncomeProfitOrLossNet']['sumNext']);

		//貸倒引当金
		$this->_childSelf['AMF00420'] = $this->_getNumValue($varsValue['reversalOfAllowanceForDoubtfulReceivables']['sumNext']);

		//追加科目
		$this->_childSelf['AMF00425'] = array();
		for ($i=1;$i<=2;$i++) {
			$idTarget = 'blankPLElse' . $i;
			$strTitle = $arr['varsItem']['varsJgaapFS']['jsonJgaapFSPL']['arrStrTitle'][$idTarget]['strTitle'];
			if ($strTitle != '' || ($strTitle == '' && $varsValue[$idTarget]['sumNext'] != 0)) {
				$this->_childSelf['AMF00425'][] = array(
					'AMF00070' => $strTitle,
					'AMF00430' => $this->_getNumValue($varsValue[$idTarget]['sumNext'])
				);
			}
		}

		//計
		$this->_childSelf['AMF00440'] = $this->_getNumValue($varsValue['reserveFundInElseSum']['sumNext']);

		//専従者給与
		$this->_childSelf['AMF00460'] = $this->_getNumValue($varsValue['familyEmployee']['sumNext']);

		//貸倒引当金
		$this->_childSelf['AMF00470'] = $this->_getNumValue($varsValue['provisionOfAllowanceForDoubtfulAccountsNon']['sumNext']);

		//追加科目
		$this->_childSelf['AMF00475'] = array();
		for ($i=3;$i<=4;$i++) {
			$idTarget = 'blankPLElse' . $i;
			$strTitle = $arr['varsItem']['varsJgaapFS']['jsonJgaapFSPL']['arrStrTitle'][$idTarget]['strTitle'];
			if ($strTitle != '' || ($strTitle == '' && $varsValue[$idTarget]['sumNext'] != 0)) {
				$this->_childSelf['AMF00475'][] = array(
					'AMF00080' => $strTitle,
					'AMF00480' => $this->_getNumValue($varsValue[$idTarget]['sumNext'])
				);
			}
		}

		//計
		$this->_childSelf['AMF00490'] = $this->_getNumValue($varsValue['reserveFundOutElseSum']['sumNext']);

		//青色申告特別控除前の所得金額
		$this->_childSelf['AMF00500'] = $this->_getNumValue($varsValue['currentTermProfitOrLossNet']['sumNext']);

		//青色申告特別控除額
		//650,000
		if ($this->_childSelf['AMF01530']) {
			$this->_childSelf['AMF00510'] = $this->_childSelf['AMF01550'];

		//100,000
		} else {
			$this->_childSelf['AMF00510'] = $this->_childSelf['AMF01580'];
		}

		//所得金額
		$this->_childSelf['AMF00530'] = $this->_childSelf['AMF00500'] - $this->_childSelf['AMF00510'];

		return $arr;
	}

	/**

	 */
	protected function _setVarsValue_2($arr)
	{

		$varsValue = $arr['varsItem']['varsFSValue']['jsonJgaapFSPL'];

		//売上（収入）金額
		$num = 600;
		for ($i=1;$i<=12;$i++) {
			$str = 'AMF00' . $num;
			$this->_childSelf[$str] = $this->_getNumValue($varsValue[$i]['netSales']['sumNext']);
			$num += 30;
		}

		//仕入金額
		$num = 610;
		$sumCR = 0;
		for ($i=1;$i<=12;$i++) {
			$str = 'AMF00' . $num;
			$numCR = $this->_getNumValue($arr['varsItem']['varsFSValue']['jsonJgaapFSCR'][$i]['currentWorkInProcessNet']['sumNext']);
			$sumCR += $numCR;
			$this->_childSelf[$str] = $this->_getNumValue($varsValue[$i]['goodsPurcheses']['sumNext']) - $numCR;
			$num += 30;
		}


		//家事消費等（売上（収入）金額）
		$this->_childSelf['AMF00950'] = $this->_getNumValue($varsValue['f1']['selfConsumption']['sumNext']);

		//雑収入（売上（収入）金額） miscellaneousIncome
		$this->_childSelf['AMF00960'] = $this->_getNumValue($varsValue['f1']['miscellaneousIncome']['sumNext']);

		//月別売上（収入）金額及び仕入金額（計）	売上（収入）金額
		$this->_childSelf['AMF00980'] = $this->_getNumValue($varsValue['f1']['salesSum']['sumNext']);

		//仕入金額
		$this->_childSelf['AMF00990'] = $this->_getNumValue($varsValue['f1']['goodsPurcheses']['sumNext']) - $sumCR;

		$this->_childSelf['AMF01010'] = 0;//個別評価による本年分繰入額
		$this->_childSelf['AMF01030'] = 0;//年末における一括評価による貸倒引当金の繰入れの対象となる貸金の合計額
		$this->_childSelf['AMF01040'] = 0;//本年分繰入限度額
		$this->_childSelf['AMF01050'] = 0;//本年分繰入額
		$this->_childSelf['AMF01060'] = 0;//本年分の貸倒引当金繰入額

		//6 本年分の不動産所得の金額
		$this->_childSelf['AMF01510'] = 0;
		if ($this->_childSelf['AMF01510'] < 0) {
			$this->_childSelf['AMF01510'] = 0;
		}

		//7 青色申告特別控除前の所得金額
		$this->_childSelf['AMF01520'] = $this->_getNumValue($varsValue['f1']['currentTermProfitOrLossNet']['sumNext']);
		if ($this->_childSelf['AMF01520'] < 0) {
			$this->_childSelf['AMF01520'] = 0;
		}

		if ($this->_childSelf['AMF01530']) {
			//8 65万円と（６）のいずれか少ない方の金額
			$num = 650000 - $this->_childSelf['AMF01510'];
			$this->_childSelf['AMF01540'] = 650000;
			if ($num > 0) {
				$this->_childSelf['AMF01540'] = $this->_childSelf['AMF01510'];
			}

			//9 青色申告特別控除額
			$num = 650000 - $this->_childSelf['AMF01540'];
			$this->_childSelf['AMF01550'] = $num;
			if ($num > $this->_childSelf['AMF01520']) {
				$this->_childSelf['AMF01550'] = $this->_childSelf['AMF01520'];
			}

		} else {
			//8 上記以外の場合：10万円と（６）のいずれか少ない方の金額
			$num = 100000 - $this->_childSelf['AMF01510'];
			$this->_childSelf['AMF01570'] = 100000;
			if ($num > 0) {
				$this->_childSelf['AMF01570'] = $this->_childSelf['AMF01510'];
			}

			//9 青色申告特別控除額
			$num = 100000 - $this->_childSelf['AMF01570'];
			$this->_childSelf['AMF01580'] = $num;
			if ($num > $this->_childSelf['AMF01520']) {
				$this->_childSelf['AMF01580'] = $this->_childSelf['AMF01520'];
			}
		}
	}

	/**

	 */
	protected function _setVarsValue_4_BS($arr)
	{
		$varsValue = $arr['varsItem']['varsFSValue']['jsonJgaapFSBS']['f1'];

		///現金 cash
		$this->_childSelf['AMG00060'] = $this->_getNumValue($varsValue['cash']['sumPrev']);
		$this->_childSelf['AMG00260'] = $this->_getNumValue($varsValue['cash']['sumNext']);

		//当座預金 checkingAccounts
		$this->_childSelf['AMG00070'] = $this->_getNumValue($varsValue['checkingAccounts']['sumPrev']);
		$this->_childSelf['AMG00270'] = $this->_getNumValue($varsValue['checkingAccounts']['sumNext']);

		//定期預金 fixedDeposit
		$this->_childSelf['AMG00080'] = $this->_getNumValue($varsValue['fixedDeposit']['sumPrev']);
		$this->_childSelf['AMG00280'] = $this->_getNumValue($varsValue['fixedDeposit']['sumNext']);

		//その他の預金 depositElse
		$this->_childSelf['AMG00090'] = $this->_getNumValue($varsValue['depositElse']['sumPrev']);
		$this->_childSelf['AMG00290'] = $this->_getNumValue($varsValue['depositElse']['sumNext']);

		//受取手形 notesReceivable
		$this->_childSelf['AMG00100'] = $this->_getNumValue($varsValue['notesReceivable']['sumPrev']);
		$this->_childSelf['AMG00300'] = $this->_getNumValue($varsValue['notesReceivable']['sumNext']);

		//売掛金 accountsReceivable
		$this->_childSelf['AMG00110'] = $this->_getNumValue($varsValue['accountsReceivable']['sumPrev']);
		$this->_childSelf['AMG00310'] = $this->_getNumValue($varsValue['accountsReceivable']['sumNext']);

		//有価証券 securities
		$this->_childSelf['AMG00120'] = $this->_getNumValue($varsValue['securities']['sumPrev']);
		$this->_childSelf['AMG00320'] = $this->_getNumValue($varsValue['securities']['sumNext']);

		//棚卸資産 inventries
		$this->_childSelf['AMG00130'] = $this->_getNumValue($varsValue['inventries']['sumPrev']);
		$this->_childSelf['AMG00330'] = $this->_getNumValue($varsValue['inventries']['sumNext']);

		//前払金 advancesAccount
		$this->_childSelf['AMG00140'] = $this->_getNumValue($varsValue['advancesAccount']['sumPrev']);
		$this->_childSelf['AMG00340'] = $this->_getNumValue($varsValue['advancesAccount']['sumNext']);

		//貸付金 loansReceivable
		$this->_childSelf['AMG00150'] = $this->_getNumValue($varsValue['loansReceivable']['sumPrev']);
		$this->_childSelf['AMG00350'] = $this->_getNumValue($varsValue['loansReceivable']['sumNext']);

		//建物 buildings
		$this->_childSelf['AMG00160'] = $this->_getNumValue($varsValue['buildings']['sumPrev']);
		$this->_childSelf['AMG00360'] = $this->_getNumValue($varsValue['buildings']['sumNext']);

		//建物附属設備 buildingsAndAccessoyEquipment
		$this->_childSelf['AMG00170'] = $this->_getNumValue($varsValue['buildingsAndAccessoyEquipment']['sumPrev']);
		$this->_childSelf['AMG00370'] = $this->_getNumValue($varsValue['buildingsAndAccessoyEquipment']['sumNext']);

		//機械装置 machineryAndEquipment
		$this->_childSelf['AMG00180'] = $this->_getNumValue($varsValue['machineryAndEquipment']['sumPrev']);
		$this->_childSelf['AMG00380'] = $this->_getNumValue($varsValue['machineryAndEquipment']['sumNext']);

		//車両運搬具 car
		$this->_childSelf['AMG00190'] = $this->_getNumValue($varsValue['car']['sumPrev']);
		$this->_childSelf['AMG00390'] = $this->_getNumValue($varsValue['car']['sumNext']);

		//工具　器具　備品 furnitureAndFixture
		$this->_childSelf['AMG00200'] = $this->_getNumValue($varsValue['furnitureAndFixture']['sumPrev']);
		$this->_childSelf['AMG00400'] = $this->_getNumValue($varsValue['furnitureAndFixture']['sumNext']);

		//土地 land
		$this->_childSelf['AMG00210'] = $this->_getNumValue($varsValue['land']['sumPrev']);
		$this->_childSelf['AMG00410'] = $this->_getNumValue($varsValue['land']['sumNext']);

		//追加科目
		$this->_childSelf['AMG00025'] = array();
		for ($i=1;$i<=6;$i++) {
			$idTarget = 'blankBSDebit' . $i;
			$strTitle = $arr['varsItem']['varsJgaapFS']['jsonJgaapFSBS']['arrStrTitle'][$idTarget]['strTitle'];
			if ($strTitle != ''
				|| ($strTitle == ''
					&& ($varsValue[$idTarget]['sumPrev'] != 0 || $varsValue[$idTarget]['sumNext'] != 0))
			) {
				$this->_childSelf['AMG00025'][] = array(
					'AMG00030' => $strTitle,
					'AMG00220' => $this->_getNumValue($varsValue[$idTarget]['sumPrev']),
					'AMG00420' => $this->_getNumValue($varsValue[$idTarget]['sumNext']),
				);
			}
		}

		//その他資産　追加科目だけど固定している科目
		$idTarget = 'assetsElse';
		$strTitle = $arr['varsItem']['varsJgaapFS']['jsonJgaapFSBS']['arrStrTitle'][$idTarget]['strTitle'];
		if ($strTitle != '') {
			$this->_childSelf['AMG00025'][] = array(
				'AMG00030' => $strTitle,
				'AMG00220' => $this->_getNumValue($varsValue[$idTarget]['sumPrev']),
				'AMG00420' => $this->_getNumValue($varsValue[$idTarget]['sumNext']),
			);
		}

		//事業主貸 accountsReceivables
		$this->_childSelf['AMG00430'] = $this->_getNumValue($varsValue['accountsReceivables']['sumNext']);

		//合計 assetsSum
		$this->_childSelf['AMG00230'] = $this->_getNumValue($varsValue['assetsSum']['sumPrev']);
		$this->_childSelf['AMG00440'] = $this->_getNumValue($varsValue['assetsSum']['sumNext']);


		//支払手形 notesPayable
		$this->_childSelf['AMG00510'] = $this->_getNumValue($varsValue['notesPayable']['sumPrev']);
		$this->_childSelf['AMG00640'] = $this->_getNumValue($varsValue['notesPayable']['sumNext']);

		//買掛金 accountsAmountPayable
		$this->_childSelf['AMG00520'] = $this->_getNumValue($varsValue['accountsAmountPayable']['sumPrev']);
		$this->_childSelf['AMG00650'] = $this->_getNumValue($varsValue['accountsAmountPayable']['sumNext']);

		//借入金 loansPayable
		$this->_childSelf['AMG00530'] = $this->_getNumValue($varsValue['loansPayable']['sumPrev']);
		$this->_childSelf['AMG00660'] = $this->_getNumValue($varsValue['loansPayable']['sumNext']);

		//未払金 accruedAmountPayable
		$this->_childSelf['AMG00540'] = $this->_getNumValue($varsValue['accruedAmountPayable']['sumPrev']);
		$this->_childSelf['AMG00670'] = $this->_getNumValue($varsValue['accruedAmountPayable']['sumNext']);

		//前受金 advancesByCustomers
		$this->_childSelf['AMG00550'] = $this->_getNumValue($varsValue['advancesByCustomers']['sumPrev']);
		$this->_childSelf['AMG00680'] = $this->_getNumValue($varsValue['advancesByCustomers']['sumNext']);

		//預り金 depositePayable
		$this->_childSelf['AMG00560'] = $this->_getNumValue($varsValue['depositePayable']['sumPrev']);
		$this->_childSelf['AMG00690'] = $this->_getNumValue($varsValue['depositePayable']['sumNext']);

		//追加科目
		$this->_childSelf['AMG00465'] = array();
		for ($i=1;$i<=7;$i++) {
			$idTarget = 'blankBSCredit' . $i;
			$strTitle = $arr['varsItem']['varsJgaapFS']['jsonJgaapFSBS']['arrStrTitle'][$idTarget]['strTitle'];
			if ($strTitle != ''
				|| ($strTitle == ''
					&& ($varsValue[$idTarget]['sumPrev'] != 0 || $varsValue[$idTarget]['sumNext'] != 0))
			) {
				$this->_childSelf['AMG00465'][] = array(
					'AMG00470' => $strTitle,
					'AMG00570' => $this->_getNumValue($varsValue[$idTarget]['sumPrev']),
					'AMG00700' => $this->_getNumValue($varsValue[$idTarget]['sumNext']),
				);
			}
		}

		//貸倒引当金 allowanceForBadDebtsTrade
		$this->_childSelf['AMG00580'] = $this->_getNumValue($varsValue['allowanceForBadDebtsTrade']['sumPrev']);
		$this->_childSelf['AMG00710'] = $this->_getNumValue($varsValue['allowanceForBadDebtsTrade']['sumNext']);

		//追加科目
		$this->_childSelf['AMG00475'] = array();
		for ($i=8;$i<=13;$i++) {
			$idTarget = 'blankBSCredit' . $i;
			$strTitle = $arr['varsItem']['varsJgaapFS']['jsonJgaapFSBS']['arrStrTitle'][$idTarget]['strTitle'];
			if ($strTitle != ''
				|| ($strTitle == ''
					&& ($varsValue[$idTarget]['sumPrev'] != 0 || $varsValue[$idTarget]['sumNext'] != 0))
			) {
				$this->_childSelf['AMG00475'][] = array(
					'AMG00480' => $strTitle,
					'AMG00590' => $this->_getNumValue($varsValue[$idTarget]['sumPrev']),
					'AMG00720' => $this->_getNumValue($varsValue[$idTarget]['sumNext']),
				);
			}
		}

		//元入金 profitBroughtForward
		$this->_childSelf['AMG00600'] = $this->_getNumValue($varsValue['profitBroughtForward']['sumPrev']);
		$this->_childSelf['AMG00740'] = $this->_getNumValue($varsValue['profitBroughtForward']['sumNext']);

		//その他負債　追加科目だけど固定している科目
		$idTarget = 'liabilitiesElse';
		$strTitle = $arr['varsItem']['varsJgaapFS']['jsonJgaapFSBS']['arrStrTitle'][$idTarget]['strTitle'];
		if ($strTitle != '') {
			$this->_childSelf['AMG00475'][] = array(
				'AMG00480' => $strTitle,
				'AMG00590' => $this->_getNumValue($varsValue[$idTarget]['sumPrev']),
				'AMG00720' => $this->_getNumValue($varsValue[$idTarget]['sumNext']),
			);
		}

		//事業主借 accountsPayables
		$this->_childSelf['AMG00730'] = $this->_getNumValue($varsValue['accountsPayables']['sumNext']);

		//元入金 profitBroughtForward
		$this->_childSelf['AMG00600'] = $this->_getNumValue($varsValue['profitBroughtForward']['sumPrev']);
		$this->_childSelf['AMG00740'] = $this->_getNumValue($varsValue['profitBroughtForward']['sumNext']);

		//青色申告特別控除前の所得金額 unappropriatedRetainedEarnings
		$this->_childSelf['AMG00750'] = $this->_getNumValue($varsValue['unappropriatedRetainedEarnings']['sumNext']);

		//合計 liabilitiesNetAssetsSum
		$this->_childSelf['AMG00610'] = $this->_getNumValue($varsValue['liabilitiesNetAssetsSum']['sumPrev']);
		$this->_childSelf['AMG00760'] = $this->_getNumValue($varsValue['liabilitiesNetAssetsSum']['sumNext']);

	}

	/**

	 */
	protected function _setVarsValue_4_CR($arr)
	{
		$varsValue = $arr['varsItem']['varsFSValue']['jsonJgaapFSCR']['f1'];

		//期首原材料棚卸高 materialsCostOpeningInventory
		$this->_childSelf['AMH00030'] = $this->_getNumValue($varsValue['materialsCostOpeningInventory']['sumNext']);
		//原材料仕入高 materialsCostPurchase
		$this->_childSelf['AMH00040'] = $this->_getNumValue($varsValue['materialsCostPurchase']['sumNext']);
		//小計 materialsCostDebitNet
		$this->_childSelf['AMH00050'] = $this->_getNumValue($varsValue['materialsCostDebitNet']['sumNext']);
		//期末原材料棚卸高  materialsCostClosingInventory
		$this->_childSelf['AMH00060'] = $this->_getNumValue($varsValue['materialsCostClosingInventory']['sumNext']);
		//差引原材料費 materialsCostSum
		$this->_childSelf['AMH00070'] = $this->_getNumValue($varsValue['materialsCostSum']['sumNext']);
		//労務費 laborCost
		$this->_childSelf['AMH00080'] = $this->_getNumValue($varsValue['laborCost']['sumNext']);
		//外注工賃 manufactureOutsourcingManufactueingExpenses
		$this->_childSelf['AMH00100'] = $this->_getNumValue($varsValue['manufactureOutsourcingManufactueingExpenses']['sumNext']);
		//電力費 manufacturePowerExpense
		$this->_childSelf['AMH00110'] = $this->_getNumValue($varsValue['manufacturePowerExpense']['sumNext']);
		//水道光熱費 manufactureWaterPowerExpenses
		$this->_childSelf['AMH00120'] = $this->_getNumValue($varsValue['manufactureWaterPowerExpenses']['sumNext']);
		//修繕費 manufactureRepair
		$this->_childSelf['AMH00130'] = $this->_getNumValue($varsValue['manufactureRepair']['sumNext']);
		//減価償却費 manufactureDepreciation
		$this->_childSelf['AMH00140'] = $this->_getNumValue($varsValue['manufactureDepreciation']['sumNext']);

		//追加科目
		$this->_childSelf['AMH00145'] = array();
		for ($i=1;$i<=7;$i++) {
			$idTarget = 'blankCR' . $i;
			$strTitle = $arr['varsItem']['varsJgaapFS']['jsonJgaapFSCR']['arrStrTitle'][$idTarget]['strTitle'];
			if ($strTitle != ''
				|| ($strTitle == '' && ($varsValue[$idTarget]['sumNext'] != 0))
			) {
				$this->_childSelf['AMH00145'][] = array(
					'AMH00010' => $strTitle,
					'AMH00150' => $this->_getNumValue($varsValue[$idTarget]['sumNext']),
				);
			}
		}

		//その他の製造経費　追加科目だけど固定している科目
		$idTarget = 'manufactureCostElse';
		$strTitle = $arr['varsItem']['varsJgaapFS']['jsonJgaapFSCR']['arrStrTitle'][$idTarget]['strTitle'];
		if ($strTitle != '') {
			$this->_childSelf['AMH00145'][] = array(
				'AMH00010' => $strTitle,
				'AMH00150' => $this->_getNumValue($varsValue[$idTarget]['sumNext']),
			);
		}

		//雑費 manufactureMiscellaneousExpenses
		$this->_childSelf['AMH00160'] = $this->_getNumValue($varsValue['manufactureMiscellaneousExpenses']['sumNext']);
		//計 manufactureCostSum
		$this->_childSelf['AMH00170'] = $this->_getNumValue($varsValue['manufactureCostSum']['sumNext']);
		//総製造費 grossProductCostNet
		$this->_childSelf['AMH00180'] = $this->_getNumValue($varsValue['grossProductCostNet']['sumNext']);
		//期首半製品・仕掛品棚卸高  workInProcessOpeningInventory
		$this->_childSelf['AMH00190'] = $this->_getNumValue($varsValue['workInProcessOpeningInventory']['sumNext']);
		//小計 workInProcessOpeningInventoryWrapNet
		$this->_childSelf['AMH00200'] = $this->_getNumValue($varsValue['workInProcessOpeningInventoryWrapNet']['sumNext']);
		//期末半製品・仕掛品棚卸高  workInProcessClosingInventory
		$this->_childSelf['AMH00210'] = $this->_getNumValue($varsValue['workInProcessClosingInventory']['sumNext']);
		//製品製造原価 currentWorkInProcessNet
		$this->_childSelf['AMH00220'] = $this->_getNumValue($varsValue['currentWorkInProcessNet']['sumNext']);
	}

	/**

	 */
	protected function _setVarsValue_3($arr)
	{
		global $classEscape;
		global $classTime;

		$num  = 0;
		$this->_childSelf['AMF01600'] = array();
		$this->_childSelf['AMF01860'] = '';

		$this->_setVarsValue_3_sumDep($arr);

		$array = $this->_childSelf['AMF01600'];
		foreach ($array as $key => $value) {
			$num++;
		}

		$array = $arr['varsItem']['varsLogFixedAssets'];
		foreach ($array as $key => $value) {
			if (preg_match("/^(noneDep|sum)$/", $value['flagDepMethod'])) {
				continue;
			}
			if ($num == 12) {
				break;
			}

			$arrayNew = array();
			//AMF01610	減価償却資産の名称等（繰延資産を含む）		string(16)	0 a
			$arrayNew['AMF01610'] = mb_substr($value['strTitle'],0,16);

			//AMF01620	面積又は数量		string(13)	0 a
			$str = $value['numVolume'] . $value['flagDepUnit'];
			if ($value['numVolume'] != '') {
				$arrayNew['AMF01620'] = mb_substr($str,0,13);
			}

			//AMF01630	取得年月		yymm	0
			$arrDate = $classTime->getLocal(array('stamp' => $value['stampBuy']));
			$numYear = $classTime->getNengoYear(array('stamp' => $value['stampBuy'], 'numYear' => $arrDate['year']));
			$flagNengo = $classTime->getFlagNengo(array('stamp' => $value['stampBuy']));


			/*20190401 start*/
			if (!($flagNengo == 'Shouwa' || $flagNengo == 'Heisei' || $flagNengo == 'Reiwa')) {
				continue;
			}

    		$arrayNew['AMF01630_gen_era'] = 3;
			if ($flagNengo == 'Heisei') {
				$arrayNew['AMF01630_gen_era'] = 4;

			} elseif ($flagNengo == 'Reiwa') {
			    $arrayNew['AMF01630_gen_era'] = 5;
			}
			/*20190401 end*/


			$arrayNew['AMF01630_gen_yy'] = $numYear;
			$arrayNew['AMF01630_gen_mm'] = $arrDate['strMonth'];

			//AMF01640	取得価額		kingaku	0
			$arrayNew['AMF01640'] = $value['numValueNet'];

			//AMF01645	（償却保証額）		kingaku	0
			$arrayNew['AMF01645'] = $value['numValueAssured'];

			//AMF01650	償却の基礎になる金額		kingaku	0
			$arrayNew['AMF01650'] = $value['numValueDepCalcBase'];

			//AMF01660	償却方法		string(10)	0 a
			$arrayNew['AMF01660'] = $arr['varsItem']['varsFixedAssetsOptions']['flagDepMethod']['arrStrTitle'][$value['flagDepMethod']];

			//AMF01670	耐用年数		nonNegativeInteger(3)	0 a
			if ($value['flagDepMethod'] == 'voluntary' || $value['flagDepMethod'] == 'one') {

			} else {
				$arrayNew['AMF01670'] = $value['numUsefulLife'];
			}


			//AMF01690	償却率又は改定償却率		decimal(1.3)	0
			if ($value['flagDepMethod'] == 'voluntary' || $value['flagDepMethod'] == 'one') {

			} else {
				$arrayNew['AMF01690'] = $value['numRateDep']*1;
			}


			//AMF01720	本年中の償却期間		nonNegativeInteger(2)	0 a

			if ($value['flagDepMethod'] == 'voluntary' || $value['flagDepMethod'] == 'one') {

			} else {
				$arrayNew['AMF01720'] = 0;
				if ($value['arrCommaDepMonth']) {
					$arrCommaDepMonth = $classEscape->splitCommaArrayData(array('data' => $value['arrCommaDepMonth']));
					$arrayNew['AMF01720'] = count($arrCommaDepMonth);
				}
			}

			//AMF01730	本年分の普通償却費		kingaku	0 a
			if ($value['flagDepMethod'] == 'voluntary') {
				$arrayNew['AMF01730'] = $value['numValueDep'];
			} else {
				$arrayNew['AMF01730'] = $value['numValueDepCalc'];
			}

			$this->_childSelf['AMF01810'] += $arrayNew['AMF01730'];

			//AMF01740	割増（特別）償却費		kingaku	0
			$arrayNew['AMF01740'] = $value['numValueDepUp'] + $value['numValueDepExtra'] + $value['numValueDepSpecial'];
			$this->_childSelf['AMF01820'] += $arrayNew['AMF01740'];

			//AMF01750	本年分の償却費合計		kingaku	0
			$arrayNew['AMF01750'] = $value['numValueDep'];
			$this->_childSelf['AMF01830'] += $arrayNew['AMF01750'];

			//AMF01760	事業専用割合		decimal(3.2)	0
			$arrayNew['AMF01760'] = $value['numRatioOperate'];

			//AMF01770	本年分の必要経費算入額		kingaku	0 a
			$arrayNew['AMF01770'] = $value['numValueDepOperate'];
			$this->_childSelf['AMF01840'] += $arrayNew['AMF01770'];

			//AMF01780	未償却残高（期末残高）		kingaku	0
			$arrayNew['AMF01780'] = $value['numValueNetClosing'];
			$this->_childSelf['AMF01850'] += $arrayNew['AMF01780'];

			//AMF01790	摘要		string15
			if ($value['strMemo']) {
				$arrayNew['AMF01790'] = mb_substr($value['strMemo'],0,15);
			}

			$this->_childSelf['AMF01600'][] = $arrayNew;
			$num++;
		}

	}

	/**

	 */
	protected function _setVarsValue_3_sumDep($arr)
	{
		global $varsPluginAccountingAccount;
		global $classTime;

		$numFiscalPeriod = $varsPluginAccountingAccount['numFiscalPeriodCurrent'];

		if (!$arr['varsItem']['varsFixedAssets']['jsonDepSum']) {
			return;
		}

		$arrRows = array();
		$array = $arr['varsItem']['varsFixedAssets']['jsonDepSum'];
		krsort($array);

		foreach ($array as $key => $value) {
			$arrayNew = array();
			$numFiscalPeriodStart = $key;

			$varsPeriod = $this->_getVarsFiscalPeriod(array(
				'flagFiscalPeriod' => 'f1',
				'numFiscalPeriod'  => $numFiscalPeriodStart,
			));

			/*20190401 start*/
			/*
			$strTitle = $arr['vars']['varsOutput']['strSumDep'];
			$strTitle = str_replace('<%replace%>', $varsPeriod['numStartHeisei'], $strTitle);
			*/
			$strTitle = $arr['vars']['varsOutput']['strSumDep20190401'];
			$strTitle = str_replace('<%strStartNengoYear%>', $varsPeriod['strStartNengoYear'], $strTitle);
			/*20190401 end*/


			$rowData = array();
			$rowData['numValue'] = $value['numValue'];
			$rowData['numValueCompression'] = $value['numValueCompression'];
			$rowData['numValueNet'] = $rowData['numValue'] - $rowData['numValueCompression'];
			$rowData['numValueDepLimit'] = $value['varsDetail'][$numFiscalPeriod]['numValueDepLimit'];

			if ($rowData['numValueDepLimit'] == 0) {
				continue;
			}

			$flagType = $arr['varsItem']['varsFixedAssets']['flagFractionRatioOperate'];
			$numValueDepOperate = $this->_updateCalc(array(
				'flagType' => $flagType,
				'num'      => $rowData['numValueDepLimit'] * $arr['varsItem']['varsFixedAssets']['numRatioOperateDepSum'] / 100,
				'numLevel' => 0
			));

			$sum = 0;
			$arrayDetail = $value['varsDetail'];
			foreach ($arrayDetail as $keyDetail => $valueDetail) {
				if ($numFiscalPeriod >= $keyDetail) {
					$sum += $valueDetail['numValueDepLimit'];
				}
			}

			$rowData['numValueNetClosing'] = $rowData['numValueNet'] - $sum;
			$rowData['numValueNetClosing'] = ($rowData['numValueNetClosing'] < 0)? 0 : $rowData['numValueNetClosing'];
			$rowData['numValueNet'] = $rowData['numValue'] - $rowData['numValueCompression'];

			//AMF01610	減価償却資産の名称等（繰延資産を含む）		string(16)	0 a
			$arrayNew['AMF01610'] = mb_substr($strTitle,0,16);

			//AMF01620	面積又は数量		string(13)	0 a
			$arrayNew['AMF01620'] = '-';

			//AMF01630	取得年月		yymm	0


			/*20190401 start*/
			$numNengoYear = $varsPeriod['numStartNengoYear'];
			$flagNengo = $varsPeriod['flagStartNengo'];

			$arrayNew['AMF01630_gen_era'] = 3;
			if ($flagNengo == 'Heisei') {
			    $arrayNew['AMF01630_gen_era'] = 4;

			} elseif ($flagNengo == 'Reiwa') {
			    $arrayNew['AMF01630_gen_era'] = 5;
			}
			$arrayNew['AMF01630_gen_yy'] = $numNengoYear;
            /*
			$arrayNew['AMF01630_gen_era'] = 4;
			$arrayNew['AMF01630_gen_yy'] = $varsPeriod['numStartHeisei'];
			*/
            /*20190401 end*/


			$arrayNew['AMF01630_gen_mm'] = 1;


			//AMF01640	取得価額		kingaku	0
			$arrayNew['AMF01640'] = $rowData['numValue'];

			//AMF01645	（償却保証額）		kingaku	0
			//$arrayNew['AMF01645'] = $value['numValueAssured'];

			//AMF01650	償却の基礎になる金額		kingaku	0
			$arrayNew['AMF01650'] = $rowData['numValueNet'];

			//AMF01660	償却方法		string(10)	0 a
			$arrayNew['AMF01660'] = $arr['varsItem']['varsFixedAssetsOptions']['flagDepMethod']['arrStrTitle']['sum'];

			//AMF01670	耐用年数		nonNegativeInteger(3)	0 a
			$arrayNew['AMF01670'] = 3;

			//AMF01690	償却率又は改定償却率		decimal(1.3)	0
			//$arrayNew['AMF01690'] = 0.000;

			//AMF01720	本年中の償却期間		nonNegativeInteger(2)	0 a
			//$arrayNew['AMF01720'] = 0;

			//AMF01730	本年分の普通償却費		kingaku	0 a
			$arrayNew['AMF01730'] = $rowData['numValueDepLimit'];
			$this->_childSelf['AMF01810'] += $arrayNew['AMF01730'];

			//AMF01740	割増（特別）償却費		kingaku	0
			$arrayNew['AMF01740'] = 0;
			$this->_childSelf['AMF01820'] += $arrayNew['AMF01740'];

			//AMF01750	本年分の償却費合計		kingaku	0
			$arrayNew['AMF01750'] = $rowData['numValueDepLimit'];
			$this->_childSelf['AMF01830'] += $arrayNew['AMF01750'];

			//AMF01760	事業専用割合		decimal(3.2)	0
			$arrayNew['AMF01760'] = $arr['varsItem']['varsFixedAssets']['numRatioOperateDepSum'];

			//AMF01770	本年分の必要経費算入額		kingaku	0 a
			$arrayNew['AMF01770'] = $numValueDepOperate;
			$this->_childSelf['AMF01840'] += $arrayNew['AMF01770'];

			//AMF01780	未償却残高（期末残高）		kingaku	0
			$arrayNew['AMF01780'] = $rowData['numValueNetClosing'];
			$this->_childSelf['AMF01850'] += $arrayNew['AMF01780'];

			//AMF01790	摘要		string15


			$this->_childSelf['AMF01600'][] = $arrayNew;
		}


	}



	/**

	 */
	protected function _getNumValue($value)
	{
		if (is_null($value)) {
			return 0;
		}

		return $value;
	}


	/**

	 */
	protected function _loopVars($arr)
	{
		$domDoc = &$arr['domDoc'];

		//data
		$this->_setTagData(array('domDoc' => $domDoc,));

		return $arr;
	}


	/**
	 */
	protected function _setTagData($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('DATA');
		$domDoc->appendChild($ele);

		$attr = $domDoc->createAttribute('id');
		$attr->value = 'DATA';
		$ele->appendChild($attr);

		$attr = $domDoc->createAttribute('xmlns');
		$attr->value = 'http://xml.e-tax.nta.go.jp/XSD/shotoku';
		$ele->appendChild($attr);

		$attr = $domDoc->createAttribute('xmlns:gen');
		$attr->value = 'http://xml.e-tax.nta.go.jp/XSD/general';
		$ele->appendChild($attr);

		$attr = $domDoc->createAttribute('xmlns:xsi');
		$attr->value = 'http://www.w3.org/2001/XMLSchema-instance';
		$ele->appendChild($attr);

		$eleChild = $this->_getTagRKO0010(array('domDoc' => $domDoc,));

		$ele->appendChild($eleChild);
	}

	/**
	 */
	protected function _getTagRKO0010($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('RKO0010');

		$attr = $domDoc->createAttribute('VR');



		/*変更箇所*/
		if ($this->_extSelf['numYearSheet'] == 2014) {
			$attr->value = '14.0.0';

			//2015
		} elseif ($this->_extSelf['numYearSheet'] == 2015) {
			$attr->value = '15.0.0';

			//2016
		} elseif ($this->_extSelf['numYearSheet'] == 2016) {
			$attr->value = '16.0.0';

			//2017
		} elseif ($this->_extSelf['numYearSheet'] == 2017) {
		    $attr->value = '17.0.0';

		    //2018
		} else {
			$attr->value = '18.0.0';
		}
		$ele->appendChild($attr);

		$attr = $domDoc->createAttribute('id');
		$attr->value = 'RKO0010';
		$ele->appendChild($attr);

		$eleCATALOG = $this->_getTagCATALOG(array('domDoc' => $domDoc,));
		$ele->appendChild($eleCATALOG);

		$eleCONTENTS = $this->_getTagCONTENTS(array('domDoc' => $domDoc,));
		$ele->appendChild($eleCONTENTS);

		return $ele;
	}

	/**
	 */
	protected function _getTagCATALOG($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('CATALOG');

		$attr = $domDoc->createAttribute('id');
		$attr->value = 'CATALOG';
		$ele->appendChild($attr);

		$eleChild = $this->_getTagRdf(array('domDoc' => $domDoc,));

		$ele->appendChild($eleChild);

		return $ele;
	}

	/**
	 */
	protected function _getTagRdf($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('rdf:RDF');

		$attr = $domDoc->createAttribute('xmlns:rdf');
		$attr->value = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#';
		$ele->appendChild($attr);

		$eleChild = $this->_getTagRdfD(array('domDoc' => $domDoc,));
		$ele->appendChild($eleChild);

		return $ele;
	}

	/**
	 */
	protected function _getTagRdfD($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('rdf:Description');

		$attr = $domDoc->createAttribute('ID');
		$attr->value = 'REPORT';
		$ele->appendChild($attr);

		$eleSEND_DATA = $domDoc->createElement('SEND_DATA');
		$ele->appendChild($eleSEND_DATA);

		$eleIT_SEC = $this->_getTagIT_SEC(array('domDoc' => $domDoc,));
		$ele->appendChild($eleIT_SEC);

		$eleFORM_SEC = $this->_getTagFORM_SEC(array('domDoc' => $domDoc,));
		$ele->appendChild($eleFORM_SEC);

		return $ele;
	}



	/**
	 */
	protected function _getTagIT_SEC($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('IT_SEC');

		$eleChild = $this->_getTagIT_SECRdfD(array('domDoc' => $domDoc,));
		$ele->appendChild($eleChild);

		return $ele;
	}

	/**
	 */
	protected function _getTagIT_SECRdfD($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('rdf:description');

		$attr = $domDoc->createAttribute('about');
		$attr->value = '#IT';
		$ele->appendChild($attr);

		return $ele;
	}

	/**
	 */
	protected function _getTagFORM_SEC($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('FORM_SEC');

		$eleChild = $this->_getTagFORM_SECRdf(array('domDoc' => $domDoc,));
		$ele->appendChild($eleChild);

		return $ele;
	}

	/**
	 */
	protected function _getTagFORM_SECRdf($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('rdf:Seq');

		$eleChild = $this->_getTagFORM_SECRdfLi(array('domDoc' => $domDoc, 'value' => '#KOA210-1'));
		$ele->appendChild($eleChild);

		$eleChild = $this->_getTagFORM_SECRdfLi(array('domDoc' => $domDoc, 'value' => '#KOA210-2'));
		$ele->appendChild($eleChild);

		$eleChild = $this->_getTagFORM_SECRdfLi(array('domDoc' => $domDoc, 'value' => '#KOA210-3'));
		$ele->appendChild($eleChild);

		$eleChild = $this->_getTagFORM_SECRdfLi(array('domDoc' => $domDoc, 'value' => '#KOA210-4'));
		$ele->appendChild($eleChild);

		return $ele;
	}

	/**
	 */
	protected function _getTagFORM_SECRdfLi($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('rdf:li');

		$eleChild = $this->_getTagFORM_SECRdfLiChild(array('domDoc' => $domDoc, 'value' => $arr['value']));
		$ele->appendChild($eleChild);

		return $ele;
	}

	/**
	 */
	protected function _getTagFORM_SECRdfLiChild($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('rdf:Description');

		$attr = $domDoc->createAttribute('about');
		$attr->value = $arr['value'];
		$ele->appendChild($attr);

		return $ele;
	}

	/**
	 */
	protected function _getTagCONTENTS($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('CONTENTS');

		$attr = $domDoc->createAttribute('id');
		$attr->value = 'CONTENTS';
		$ele->appendChild($attr);

		$eleChild = $this->_getTag_CONTENTS_IT(array('domDoc' => $domDoc,));
		$ele->appendChild($eleChild);

		$eleChild = $this->_getTag_KOA210(array('domDoc' => $domDoc,));
		$ele->appendChild($eleChild);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_IT($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('IT');

		$attr = $domDoc->createAttribute('VR');
		$attr->value = '1.2';
		$ele->appendChild($attr);

		$attr = $domDoc->createAttribute('id');
		$attr->value = 'IT';
		$ele->appendChild($attr);

		$eleZEIMUSHO = $this->_getTag_CONTENTS_IT_ZEIMUSHO(array('domDoc' => $domDoc,));
		$ele->appendChild($eleZEIMUSHO);

		$eleTEISYUTSU_DAY = $this->_getTag_CONTENTS_IT_TEISYUTSU_DAY(array('domDoc' => $domDoc,));
		$ele->appendChild($eleTEISYUTSU_DAY);

		$eleNOZEISHA_ID = $this->_getTag_CONTENTS_IT_NOZEISHA_ID(array('domDoc' => $domDoc,));
		$ele->appendChild($eleNOZEISHA_ID);

		//氏名・名称読み
		$eleNOZEISHA_NM_KN = $this->_getTag_CONTENTS_IT_NOZEISHA_NM_KN(array('domDoc' => $domDoc,));
		$ele->appendChild($eleNOZEISHA_NM_KN);

		//氏名・名称
		$eleNOZEISHA_NM = $this->_getTag_CONTENTS_IT_NOZEISHA_NM(array('domDoc' => $domDoc,));
		$ele->appendChild($eleNOZEISHA_NM);

		//納税者所在地郵便番号
		$eleNOZEISHA_ZIP = $this->_getTag_CONTENTS_IT_NOZEISHA_ZIP(array('domDoc' => $domDoc,));
		$ele->appendChild($eleNOZEISHA_ZIP);

		//納税者所在地
		$eleNOZEISHA_ADR = $this->_getTag_CONTENTS_IT_NOZEISHA_ADR(array('domDoc' => $domDoc,));
		$ele->appendChild($eleNOZEISHA_ADR);

		//１月１日住所
		$eleICHIGATSUIPPI_ADR = $this->_getTag_CONTENTS_IT_ICHIGATSUIPPI_ADR(array('domDoc' => $domDoc,));
		$ele->appendChild($eleICHIGATSUIPPI_ADR);

		//納税者所在地屋号読み
		$eleNOZEISHA_YAGO = $this->_getTag_CONTENTS_IT_NOZEISHA_YAGO(array('domDoc' => $domDoc,));
		$ele->appendChild($eleNOZEISHA_YAGO);

		//納税者電話番号
		$eleNOZEISHA_TEL = $this->_getTag_CONTENTS_IT_NOZEISHA_TEL(array('domDoc' => $domDoc,));
		$ele->appendChild($eleNOZEISHA_TEL);

		//世帯主氏名
		$eleSETAINUSHI_NM = $this->_getTag_CONTENTS_IT_SETAINUSHI_NM(array('domDoc' => $domDoc,));
		$ele->appendChild($eleSETAINUSHI_NM);

		//世帯主氏名
		$eleSETAINUSHI_ZOKU = $this->_getTag_CONTENTS_IT_SETAINUSHI_ZOKU(array('domDoc' => $domDoc,));
		$ele->appendChild($eleSETAINUSHI_ZOKU);

		//職業
		$eleSHOKUGYO = $this->_getTag_CONTENTS_IT_SHOKUGYO(array('domDoc' => $domDoc,));
		$ele->appendChild($eleSHOKUGYO);

		//還付先金融機関
		$eleKANPU_KINYUKIKAN = $this->_getTag_CONTENTS_IT_KANPU_KINYUKIKAN(array('domDoc' => $domDoc,));
		$ele->appendChild($eleKANPU_KINYUKIKAN);

		//代理人等氏名読み
		$eleDAIRI_NM = $this->_getTag_CONTENTS_IT_DAIRI_NM(array('domDoc' => $domDoc,));
		$ele->appendChild($eleDAIRI_NM);

		//代理人住所
		$eleDAIRI_ADR = $this->_getTag_CONTENTS_IT_DAIRI_ADR(array('domDoc' => $domDoc,));
		$ele->appendChild($eleDAIRI_ADR);

		//手続き
		$eleTETSUZUKI = $this->_getTag_CONTENTS_IT_TETSUZUKI(array('domDoc' => $domDoc,));
		$ele->appendChild($eleTETSUZUKI);

		//年分
		$eleNENBUN = $this->_getTag_CONTENTS_IT_NENBUN(array('domDoc' => $domDoc,));
		$ele->appendChild($eleNENBUN);

		//申告の種類
		$eleSHINKOKU_KBN = $this->_getTag_CONTENTS_IT_SHINKOKU_KBN(array('domDoc' => $domDoc,));
		$ele->appendChild($eleSHINKOKU_KBN);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_IT_ZEIMUSHO($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('ZEIMUSHO');

		$attr = $domDoc->createAttribute('ID');
		$attr->value = 'ZEIMUSHO';
		$ele->appendChild($attr);

		$eleGen = $domDoc->createElement('gen:zeimusho_CD');
		$eleGen->nodeValue = $this->_childSelf['zeimusho_CD'];
		$ele->appendChild($eleGen);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_IT_TEISYUTSU_DAY($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('TEISYUTSU_DAY');

		$attr = $domDoc->createAttribute('ID');
		$attr->value = 'TEISYUTSU_DAY';
		$ele->appendChild($attr);

		$eleGen = $domDoc->createElement('gen:era');
		$eleGen->nodeValue = 4;
		$ele->appendChild($eleGen);

		$eleGen = $domDoc->createElement('gen:yy');
		$eleGen->nodeValue = $this->_childSelf['TEISYUTSU_DAY_gen_yy'];
		$ele->appendChild($eleGen);

		$eleGen = $domDoc->createElement('gen:mm');
		$eleGen->nodeValue = $this->_childSelf['TEISYUTSU_DAY_gen_mm'];
		$ele->appendChild($eleGen);

		$eleGen = $domDoc->createElement('gen:dd');
		$eleGen->nodeValue = $this->_childSelf['TEISYUTSU_DAY_gen_dd'];
		$ele->appendChild($eleGen);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_IT_NOZEISHA_ID($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('NOZEISHA_ID');

		$attr = $domDoc->createAttribute('ID');
		$attr->value = 'NOZEISHA_ID';
		$ele->appendChild($attr);
		$ele->nodeValue = $this->_childSelf['NOZEISHA_ID'];

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_IT_NOZEISHA_NM_KN($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('NOZEISHA_NM_KN');
		$ele->nodeValue = '';

		$attr = $domDoc->createAttribute('ID');
		$attr->value = 'NOZEISHA_NM_KN';
		$ele->appendChild($attr);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_IT_NOZEISHA_NM($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('NOZEISHA_NM');
		$ele->nodeValue = '';

		$attr = $domDoc->createAttribute('ID');
		$attr->value = 'NOZEISHA_NM';
		$ele->appendChild($attr);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_IT_NOZEISHA_ZIP($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('NOZEISHA_ZIP');

		$attr = $domDoc->createAttribute('ID');
		$attr->value = 'NOZEISHA_ZIP';
		$ele->appendChild($attr);

		$eleGen = $domDoc->createElement('gen:zip1');
		$eleGen->nodeValue = '';
		$ele->appendChild($eleGen);

		$eleGen = $domDoc->createElement('gen:zip2');
		$eleGen->nodeValue = '';
		$ele->appendChild($eleGen);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_IT_NOZEISHA_ADR($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('NOZEISHA_ADR');
		$ele->nodeValue = '';

		$attr = $domDoc->createAttribute('ID');
		$attr->value = 'NOZEISHA_ADR';
		$ele->appendChild($attr);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_IT_ICHIGATSUIPPI_ADR($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('ICHIGATSUIPPI_ADR');
		$ele->nodeValue = '';

		$attr = $domDoc->createAttribute('ID');
		$attr->value = 'ICHIGATSUIPPI_ADR';
		$ele->appendChild($attr);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_IT_NOZEISHA_YAGO($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('NOZEISHA_YAGO');
		$ele->nodeValue = '';

		$attr = $domDoc->createAttribute('ID');
		$attr->value = 'NOZEISHA_YAGO';
		$ele->appendChild($attr);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_IT_NOZEISHA_TEL($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('NOZEISHA_TEL');

		$attr = $domDoc->createAttribute('ID');
		$attr->value = 'NOZEISHA_TEL';
		$ele->appendChild($attr);

		$eleGen = $domDoc->createElement('gen:tel1');
		$eleGen->nodeValue = '';
		$ele->appendChild($eleGen);

		$eleGen = $domDoc->createElement('gen:tel2');
		$eleGen->nodeValue = '';
		$ele->appendChild($eleGen);

		$eleGen = $domDoc->createElement('gen:tel3');
		$eleGen->nodeValue = '';
		$ele->appendChild($eleGen);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_IT_SETAINUSHI_NM($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('SETAINUSHI_NM');
		$ele->nodeValue = '';

		$attr = $domDoc->createAttribute('ID');
		$attr->value = 'SETAINUSHI_NM';
		$ele->appendChild($attr);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_IT_SETAINUSHI_ZOKU($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('SETAINUSHI_ZOKU');
		$ele->nodeValue = '';

		$attr = $domDoc->createAttribute('ID');
		$attr->value = 'SETAINUSHI_ZOKU';
		$ele->appendChild($attr);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_IT_SHOKUGYO($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('SHOKUGYO');
		$ele->nodeValue = '';

		$attr = $domDoc->createAttribute('ID');
		$attr->value = 'SHOKUGYO';
		$ele->appendChild($attr);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_IT_KANPU_KINYUKIKAN($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('KANPU_KINYUKIKAN');
		$ele->nodeValue = '';

		$attr = $domDoc->createAttribute('ID');
		$attr->value = 'KANPU_KINYUKIKAN';
		$ele->appendChild($attr);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_IT_DAIRI_NM($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('DAIRI_NM');
		$ele->nodeValue = '';

		$attr = $domDoc->createAttribute('ID');
		$attr->value = 'DAIRI_NM';
		$ele->appendChild($attr);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_IT_DAIRI_ADR($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('DAIRI_ADR');
		$ele->nodeValue = '';

		$attr = $domDoc->createAttribute('ID');
		$attr->value = 'DAIRI_ADR';
		$ele->appendChild($attr);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_IT_TETSUZUKI($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('TETSUZUKI');
		$ele->nodeValue = '';

		$attr = $domDoc->createAttribute('ID');
		$attr->value = 'TETSUZUKI';
		$ele->appendChild($attr);

		$eleGen = $domDoc->createElement('procedure_CD');
		$eleGen->nodeValue = 'RKO0010';
		$ele->appendChild($eleGen);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_IT_NENBUN($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('NENBUN');

		$attr = $domDoc->createAttribute('ID');
		$attr->value = 'NENBUN';
		$ele->appendChild($attr);

		$eleGen = $domDoc->createElement('gen:era');
		$eleGen->nodeValue = 4;
		$ele->appendChild($eleGen);

		$eleGen = $domDoc->createElement('gen:yy');

		/*変更箇所*/
		if ($this->_extSelf['numYearSheet'] == 2014) {
			$eleGen->nodeValue = 26;

		} elseif ($this->_extSelf['numYearSheet'] == 2015) {
			$eleGen->nodeValue = 27;

		} elseif ($this->_extSelf['numYearSheet'] == 2016) {
			$eleGen->nodeValue = 28;

		} elseif ($this->_extSelf['numYearSheet'] == 2017) {
		    $eleGen->nodeValue = 29;

		} else {
			$eleGen->nodeValue = 30;
		}

		$ele->appendChild($eleGen);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_IT_SHINKOKU_KBN($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('SHINKOKU_KBN');

		$attr = $domDoc->createAttribute('ID');
		$attr->value = 'SHINKOKU_KBN';
		$ele->appendChild($attr);

		$eleGen = $domDoc->createElement('kubun_CD');
		$eleGen->nodeValue = 1;
		$ele->appendChild($eleGen);

		return $ele;
	}

	/**
	 */
	protected function _getTag_KOA210($arr)
	{
		global $classTime;

		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('KOA210');

		$attr = $domDoc->createAttribute('VR');
		$attr->value = '7.0';
		$ele->appendChild($attr);

		$attr = $domDoc->createAttribute('id');
		$attr->value = 'KOA210-1';
		$ele->appendChild($attr);

		$attr = $domDoc->createAttribute('page');
		$attr->value = '1';
		$ele->appendChild($attr);

		$strTime = $classTime->getDisplay(array(
			'flagType' => 'year-date',
			'stamp'    => TIMESTAMP,
		));

		$attr = $domDoc->createAttribute('sakuseiDay');
		$attr->value = $strTime;
		$ele->appendChild($attr);

		$attr = $domDoc->createAttribute('sakuseiNM');
		$attr->value = '';
		$ele->appendChild($attr);

		$attr = $domDoc->createAttribute('softNM');
		$attr->value = '';
		$ele->appendChild($attr);

		$eleKOA210_1 = $this->_getTag_CONTENTS_KOA210_1(array('domDoc' => $domDoc,));
		$ele->appendChild($eleKOA210_1);

		$eleKOA210_2 = $this->_getTag_CONTENTS_KOA210_2(array('domDoc' => $domDoc,));
		$ele->appendChild($eleKOA210_2);

		$eleKOA210_3 = $this->_getTag_CONTENTS_KOA210_3(array('domDoc' => $domDoc,));
		$ele->appendChild($eleKOA210_3);

		$eleKOA210_4 = $this->_getTag_CONTENTS_KOA210_4(array('domDoc' => $domDoc,));
		$ele->appendChild($eleKOA210_4);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_1($arr)
	{
		global $classTime;

		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('KOA210-1');

		$attr = $domDoc->createAttribute('page');
		$attr->value = '1';
		$ele->appendChild($attr);

		//AMA00000
		$eleAMA00000 = $domDoc->createElement('AMA00000');
		$ele->appendChild($eleAMA00000);

		$attr = $domDoc->createAttribute('IDREF');
		$attr->value = 'NENBUN';
		$eleAMA00000->appendChild($attr);

		//AMB00000
		$eleAMB00000 = $this->_getTag_CONTENTS_KOA210_1_AMB00000(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMB00000);

		//AMC00000
		$eleAMC00000 = $domDoc->createElement('AMC00000');
		$ele->appendChild($eleAMC00000);

		//AMF00000
		$eleAMF00000 = $this->_getTag_CONTENTS_KOA210_1_AMF00000(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMF00000);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_1_AMB00000($arr)
	{
		global $classTime;

		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMB00000');

		//AMB00010
		$eleAMB00010 = $domDoc->createElement('AMB00010');
		$ele->appendChild($eleAMB00010);

		//AMB00020
		$eleAMB00020 = $this->_getTag_CONTENTS_KOA210_1_AMB00000_AMB00020(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMB00020);

		//AMB00050 事業所所在地
		$eleAMB00050 = $domDoc->createElement('AMB00050');
		$eleAMB00050->nodeValue = '';
		$ele->appendChild($eleAMB00050);

		//AMB00060
		$eleAMB00060 = $this->_getTag_CONTENTS_KOA210_1_AMB00000_AMB00060(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMB00060);

		//AMB00090 業種名
		$eleAMB00090 = $domDoc->createElement('AMB00090');
		$ele->appendChild($eleAMB00090);

		$attr = $domDoc->createAttribute('IDREF');
		$attr->value = 'SHOKUGYO';
		$eleAMB00090->appendChild($attr);

		//AMB00100 屋号
		$eleAMB00100 = $domDoc->createElement('AMB00100');
		$ele->appendChild($eleAMB00100);

		$attr = $domDoc->createAttribute('IDREF');
		$attr->value = 'NOZEISHA_YAGO';
		$eleAMB00100->appendChild($attr);

		//AMB00110 加入団体名
		$eleAMB00110 = $domDoc->createElement('AMB00110');
		$eleAMB00110->nodeValue = '';
		$ele->appendChild($eleAMB00110);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_1_AMB00000_AMB00020($arr)
	{
		global $classTime;

		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMB00020');

		//AMB00030 氏名（フリガナ）
		$eleAMB00030 = $domDoc->createElement('AMB00030');
		$ele->appendChild($eleAMB00030);

		$attr = $domDoc->createAttribute('IDREF');
		$attr->value = 'NOZEISHA_NM_KN';
		$eleAMB00030->appendChild($attr);

		//AMB00040 氏名
		$eleAMB00040 = $domDoc->createElement('AMB00040');
		$ele->appendChild($eleAMB00040);

		$attr = $domDoc->createAttribute('IDREF');
		$attr->value = 'NOZEISHA_NM';
		$eleAMB00040->appendChild($attr);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_1_AMB00000_AMB00060($arr)
	{
		global $classTime;

		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMB00060');

		//AMB00070 自宅電話番号
		$eleAMB00070 = $this->_getTag_CONTENTS_KOA210_1_AMB00000_AMB00070(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMB00070);

		//AMB00080 事業所電話番号
		$eleAMB00080 = $this->_getTag_CONTENTS_KOA210_1_AMB00000_AMB00080(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMB00080);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_1_AMB00000_AMB00070($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMB00070');

		$eleGen = $domDoc->createElement('gen:tel1');
		$ele->appendChild($eleGen);

		$eleGen = $domDoc->createElement('gen:tel2');
		$ele->appendChild($eleGen);

		$eleGen = $domDoc->createElement('gen:tel3');
		$ele->appendChild($eleGen);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_1_AMB00000_AMB00080($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMB00080');

		$eleGen = $domDoc->createElement('gen:tel1');
		$ele->appendChild($eleGen);

		$eleGen = $domDoc->createElement('gen:tel2');
		$ele->appendChild($eleGen);

		$eleGen = $domDoc->createElement('gen:tel3');
		$ele->appendChild($eleGen);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_1_AMF00000($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMF00000');

		//AMF00010
		$eleAMF00010 = $this->_getTag_CONTENTS_KOA210_1_AMF00010(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMF00010);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_1_AMF00010($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMF00010');

		//AMF00020
		$eleAMF00020 = $this->_getTag_CONTENTS_KOA210_1_AMF00020(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMF00020);

		//AMF00090
		$eleAMF00090 = $this->_getTag_CONTENTS_KOA210_1_AMF00090(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMF00090);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_1_AMF00020($arr)
	{
		global $classTime;

		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMF00020');

		//AMF00030
		$eleAMF00030 = $this->_getTag_CONTENTS_KOA210_1_AMF00030(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMF00030);

		//AMF00040
		$eleAMF00040 = $this->_getTag_CONTENTS_KOA210_1_AMF00040(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMF00040);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_1_AMF00030($arr)
	{
		global $classTime;

		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMF00030');

		$eleGen = $domDoc->createElement('gen:mm');
		$eleGen->nodeValue = '1';
		$ele->appendChild($eleGen);

		$eleGen = $domDoc->createElement('gen:dd');
		$eleGen->nodeValue = '1';
		$ele->appendChild($eleGen);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_1_AMF00040($arr)
	{
		global $classTime;

		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMF00040');

		$eleGen = $domDoc->createElement('gen:mm');
		$eleGen->nodeValue = '12';
		$ele->appendChild($eleGen);

		$eleGen = $domDoc->createElement('gen:dd');
		$eleGen->nodeValue = '31';
		$ele->appendChild($eleGen);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_1_AMF00090($arr)
	{
		global $classTime;

		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMF00090');

		//AMF00100
		$eleAMF00100 = $this->_getTag_CONTENTS_KOA210_1_AMF00100(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMF00100);

		//AMF00110
		$eleAMF00110 = $this->_getTag_CONTENTS_KOA210_1_AMF00110(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMF00110);

		//AMF00170
		$eleAMF00170 = $this->_getTag_CONTENTS_KOA210_1_AMF00170(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMF00170);

		//AMF00180
		$eleAMF00180 = $this->_getTag_CONTENTS_KOA210_1_AMF00180(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMF00180);

		//AMF00390
		$eleAMF00390 = $this->_getTag_CONTENTS_KOA210_1_AMF00390(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMF00390);

		//AMF00400
		$eleAMF00400 = $this->_getTag_CONTENTS_KOA210_1_AMF00400(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMF00400);

		//AMF00500
		$eleAMF00500 = $this->_getTag_CONTENTS_KOA210_1_AMF00500(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMF00500);

		//AMF00510
		$eleAMF00510 = $this->_getTag_CONTENTS_KOA210_1_AMF00510(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMF00510);

		//AMF00530
		$eleAMF00530 = $this->_getTag_CONTENTS_KOA210_1_AMF00530(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMF00530);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_1_AMF00100($arr)
	{
		global $classTime;

		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMF00100');
		$ele->nodeValue = $this->_childSelf['AMF00100'];

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_1_AMF00110($arr)
	{
		global $classTime;

		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMF00110');

		for ($i=12;$i<=16;$i++) {
			$eleTemp = $domDoc->createElement('AMF00' . $i . '0');
			$eleTemp->nodeValue = $this->_childSelf['AMF00' . $i . '0'];
			$ele->appendChild($eleTemp);
		}

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_1_AMF00170($arr)
	{
		global $classTime;

		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMF00170');
		$ele->nodeValue = $this->_childSelf['AMF00170'];

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_1_AMF00180($arr)
	{
		global $classTime;

		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMF00180');

		for ($i=19;$i<=38;$i++) {
			if (preg_match("/^(36)$/", $i)) {
				$array = $this->_childSelf['AMF00355'];
				if ($array) {
					foreach ($array as $key => $value) {
						$eleAMF00355 = $domDoc->createElement('AMF00355');
						$ele->appendChild($eleAMF00355);

						$eleTemp = $domDoc->createElement('AMF00060');
						$eleTemp->nodeValue = $value['AMF00060'];
						$eleAMF00355->appendChild($eleTemp);

						$eleTemp = $domDoc->createElement('AMF00' . $i . '0');
						$eleTemp->nodeValue = $value['AMF00' . $i . '0'];
						$eleAMF00355->appendChild($eleTemp);
					}
				}
				continue;
			}
			$eleTemp = $domDoc->createElement('AMF00' . $i . '0');
			$eleTemp->nodeValue = $this->_childSelf['AMF00' . $i . '0'];
			$ele->appendChild($eleTemp);
		}

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_1_AMF00390($arr)
	{
		global $classTime;

		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMF00390');
		$ele->nodeValue = $this->_childSelf['AMF00390'];

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_1_AMF00400($arr)
	{
		global $classTime;

		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMF00400');

		//AMF00410
		$eleAMF00410 = $this->_getTag_CONTENTS_KOA210_1_AMF00410(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMF00410);

		//AMF00450
		$eleAMF00450 = $this->_getTag_CONTENTS_KOA210_1_AMF00450(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMF00450);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_1_AMF00410($arr)
	{
		global $classTime;

		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMF00410');

		$eleAMF00420 = $domDoc->createElement('AMF00420');
		$eleAMF00420->nodeValue = $this->_childSelf['AMF00420'];
		$ele->appendChild($eleAMF00420);

		$array = $this->_childSelf['AMF00425'];
		if ($array) {
			foreach ($array as $key => $value) {
				$eleAMF00425 = $domDoc->createElement('AMF00425');
				$ele->appendChild($eleAMF00425);

				$eleTemp = $domDoc->createElement('AMF00070');
				$eleTemp->nodeValue = $value['AMF00070'];
				$eleAMF00425->appendChild($eleTemp);

				$eleTemp = $domDoc->createElement('AMF00430');
				$eleTemp->nodeValue = $value['AMF00430'];
				$eleAMF00425->appendChild($eleTemp);
			}
		}


		$eleAMF00440 = $domDoc->createElement('AMF00440');
		$eleAMF00440->nodeValue = $this->_childSelf['AMF00440'];
		$ele->appendChild($eleAMF00440);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_1_AMF00450($arr)
	{
		global $classTime;

		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMF00450');

		$eleAMF00460 = $domDoc->createElement('AMF00460');
		$eleAMF00460->nodeValue = $this->_childSelf['AMF00460'];
		$ele->appendChild($eleAMF00460);

		$eleAMF00470 = $domDoc->createElement('AMF00470');
		$eleAMF00470->nodeValue = $this->_childSelf['AMF00470'];
		$ele->appendChild($eleAMF00470);

		$array = $this->_childSelf['AMF00475'];
		if ($array) {
			foreach ($array as $key => $value) {
				$eleAMF00475 = $domDoc->createElement('AMF00475');
				$ele->appendChild($eleAMF00475);

				$eleTemp = $domDoc->createElement('AMF00080');
				$eleTemp->nodeValue = $value['AMF00080'];
				$eleAMF00475->appendChild($eleTemp);

				$eleTemp = $domDoc->createElement('AMF00480');
				$eleTemp->nodeValue = $value['AMF00480'];
				$eleAMF00475->appendChild($eleTemp);
			}
		}

		$eleAMF00490 = $domDoc->createElement('AMF00490');
		$eleAMF00490->nodeValue = $this->_childSelf['AMF00490'];
		$ele->appendChild($eleAMF00490);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_1_AMF00500($arr)
	{
		global $classTime;

		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMF00500');
		$ele->nodeValue = $this->_childSelf['AMF00500'];

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_1_AMF00510($arr)
	{
		global $classTime;

		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMF00510');
		$ele->nodeValue = $this->_childSelf['AMF00510'];

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_1_AMF00530($arr)
	{
		global $classTime;

		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMF00530');
		$ele->nodeValue = $this->_childSelf['AMF00530'];

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_2($arr)
	{

		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('KOA210-2');

		$attr = $domDoc->createAttribute('page');
		$attr->value = '1';
		$ele->appendChild($attr);

		//AMF00538
		$eleAMF00538 = $domDoc->createElement('AMF00538');
		$ele->appendChild($eleAMF00538);

		$attr = $domDoc->createAttribute('IDREF');
		$attr->value = 'NENBUN';
		$eleAMF00538->appendChild($attr);


		//AMF00540
		$eleAMF00540 = $this->_getTag_CONTENTS_KOA210_2_AMF00540(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMF00540);

		//AMF00580
		$eleAMF00580 = $this->_getTag_CONTENTS_KOA210_2_AMF00580(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMF00580);

		//AMF01000
		$eleAMF01000 = $this->_getTag_CONTENTS_KOA210_2_AMF01000(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMF01000);

		//AMF01070
		$eleAMF01070 = $this->_getTag_CONTENTS_KOA210_2_AMF01070(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMF01070);

		//AMF01320
		$eleAMF01320 = $this->_getTag_CONTENTS_KOA210_2_AMF01320(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMF01320);

		//AMF01500
		$eleAMF01500 = $this->_getTag_CONTENTS_KOA210_2_AMF01500(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMF01500);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_2_AMF00540($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMF00540');

		//AMF00550
		$eleAMF00550 = $domDoc->createElement('AMF00550');
		$ele->appendChild($eleAMF00550);

		$attr = $domDoc->createAttribute('IDREF');
		$attr->value = 'NOZEISHA_NM_KN';
		$eleAMF00550->appendChild($attr);

		//AMF00560
		$eleAMF00560 = $domDoc->createElement('AMF00560');
		$ele->appendChild($eleAMF00560);

		$attr = $domDoc->createAttribute('IDREF');
		$attr->value = 'NOZEISHA_NM';
		$eleAMF00560->appendChild($attr);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_2_AMF00580($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMF00580');

		for ($i=59;$i<=92;$i++) {
			$eleTemp = $domDoc->createElement('AMF00' . $i . '0');
			$ele->appendChild($eleTemp);

			$strNum = 1 + $i;
			$eleChild = $domDoc->createElement('AMF00' . $strNum . '0');
			$eleTemp->appendChild($eleChild);
			$eleChild->nodeValue = $this->_childSelf['AMF00' . $strNum . '0'];

			$strNum++;
			$eleChild = $domDoc->createElement('AMF00' . $strNum . '0');
			$eleTemp->appendChild($eleChild);
			$eleChild->nodeValue = $this->_childSelf['AMF00' . $strNum . '0'];

			$i = $i + 2;
		}

		//AMF00950
		$eleAMF00950 = $domDoc->createElement('AMF00950');
		$ele->appendChild($eleAMF00950);
		$eleAMF00950->nodeValue = $this->_childSelf['AMF00950'];

		//AMF00960
		$eleAMF00960 = $domDoc->createElement('AMF00960');
		$ele->appendChild($eleAMF00960);
		$eleAMF00960->nodeValue = $this->_childSelf['AMF00960'];

		//AMF00970
		$eleAMF00970 = $this->_getTag_CONTENTS_KOA210_2_AMF00970(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMF00970);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_2_AMF00970($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMF00970');

		//AMF00980
		$eleAMF00980 = $domDoc->createElement('AMF00980');
		$eleAMF00980->nodeValue = $this->_childSelf['AMF00980'];
		$ele->appendChild($eleAMF00980);

		//AMF00990
		$eleAMF00990 = $domDoc->createElement('AMF00990');
		$eleAMF00990->nodeValue = $this->_childSelf['AMF00990'];
		$ele->appendChild($eleAMF00990);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_2_AMF01000($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMF01000');

		//AMF01010
		$eleAMF01010 = $domDoc->createElement('AMF01010');
		$eleAMF01010->nodeValue = $this->_childSelf['AMF01010'];
		$ele->appendChild($eleAMF01010);

		//AMF01020
		$eleAMF01020 = $domDoc->createElement('AMF01020');
		$ele->appendChild($eleAMF01020);

		for ($i=103;$i<=105;$i++) {
			$eleTemp = $domDoc->createElement('AMF0' . $i . '0');
			$eleTemp->nodeValue = $this->_childSelf['AMF0' . $i . '0'];
			$eleAMF01020->appendChild($eleTemp);
		}

		//AMF01060
		$eleAMF01060 = $domDoc->createElement('AMF01060');
		$eleAMF01060->nodeValue = $this->_childSelf['AMF01060'];
		$ele->appendChild($eleAMF01060);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_2_AMF01070($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMF01070');

		for ($i=1;$i<=5;$i++) {
			$eleAMF01080 = $domDoc->createElement('AMF01080');
			$ele->appendChild($eleAMF01080);

			$eleAMF01090 = $domDoc->createElement('AMF01090');
			$eleAMF01090->nodeValue = '';
			$eleAMF01080->appendChild($eleAMF01090);
		}

		//AMF01170
		$eleAMF01170 = $domDoc->createElement('AMF01170');
		$eleAMF01170->nodeValue = '';
		$ele->appendChild($eleAMF01170);

		//AMF01250
		$eleAMF01250 = $domDoc->createElement('AMF01250');
		$ele->appendChild($eleAMF01250);

		//AMF01270
		$eleAMF01270 = $domDoc->createElement('AMF01270');
		$eleAMF01270->nodeValue = '';
		$eleAMF01250->appendChild($eleAMF01270);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_2_AMF01320($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMF01320');

		for ($i=1;$i<=5;$i++) {
			$eleAMF01330 = $domDoc->createElement('AMF01330');
			$ele->appendChild($eleAMF01330);

			$eleAMF01340 = $domDoc->createElement('AMF01340');
			$eleAMF01340->nodeValue = '';
			$eleAMF01330->appendChild($eleAMF01340);

			$eleAMF01350 = $domDoc->createElement('AMF01350');
			$eleAMF01350->nodeValue = '';
			$eleAMF01330->appendChild($eleAMF01350);

		}

		//AMF01430
		$eleAMF01430 = $domDoc->createElement('AMF01430');
		$ele->appendChild($eleAMF01430);

		//AMF01450
		$eleAMF01450 = $domDoc->createElement('AMF01450');
		$eleAMF01450->nodeValue = '';
		$eleAMF01430->appendChild($eleAMF01450);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_2_AMF01500($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMF01500');

		$eleAMF01510 = $domDoc->createElement('AMF01510');
		$eleAMF01510->nodeValue = $this->_childSelf['AMF01510'];
		$ele->appendChild($eleAMF01510);

		$eleAMF01520 = $domDoc->createElement('AMF01520');
		$eleAMF01520->nodeValue = $this->_childSelf['AMF01520'];
		$ele->appendChild($eleAMF01520);

		$eleAMF01530 = $domDoc->createElement('AMF01530');
		$ele->appendChild($eleAMF01530);

		$eleAMF01540 = $domDoc->createElement('AMF01540');
		$eleAMF01540->nodeValue = $this->_childSelf['AMF01540'];
		$eleAMF01530->appendChild($eleAMF01540);

		$eleAMF01550 = $domDoc->createElement('AMF01550');
		$eleAMF01550->nodeValue = $this->_childSelf['AMF01550'];
		$eleAMF01530->appendChild($eleAMF01550);

		$eleAMF01560 = $domDoc->createElement('AMF01560');
		$ele->appendChild($eleAMF01560);

		$eleAMF01570 = $domDoc->createElement('AMF01570');
		$eleAMF01570->nodeValue = $this->_childSelf['AMF01570'];
		$eleAMF01560->appendChild($eleAMF01570);

		$eleAMF01580 = $domDoc->createElement('AMF01580');
		$eleAMF01580->nodeValue = $this->_childSelf['AMF01580'];
		$eleAMF01560->appendChild($eleAMF01580);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_3($arr)
	{
		global $classTime;

		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('KOA210-3');

		$attr = $domDoc->createAttribute('page');
		$attr->value = '1';
		$ele->appendChild($attr);

		//AMF01590
		$eleAMF01590 = $this->_getTag_CONTENTS_KOA210_3_AMF01590(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMF01590);

		//AMF01870
		$eleAMF01870 = $this->_getTag_CONTENTS_KOA210_3_AMF01870(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMF01870);

		//AMF01870 * 2
		$eleAMF01870 = $this->_getTag_CONTENTS_KOA210_3_AMF01870(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMF01870);

		//AMF01940
		$eleAMF01940 = $this->_getTag_CONTENTS_KOA210_3_AMF01940(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMF01940);

		//AMF01940 * 2
		$eleAMF01940 = $this->_getTag_CONTENTS_KOA210_3_AMF01940(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMF01940);

		//AMF02040
		$eleAMF02040 = $this->_getTag_CONTENTS_KOA210_3_AMF02040(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMF02040);

		//AMF02040 * 2
		$eleAMF02040 = $this->_getTag_CONTENTS_KOA210_3_AMF02040(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMF02040);

		//AMF02110
		$eleAMF02110 = $domDoc->createElement('AMF02110');
		$eleAMF02110->nodeValue = $this->_childSelf['AMF02110'];
		$ele->appendChild($eleAMF02110);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_3_AMF01590($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMF01590');



		for ($i=0;$i<12;$i++) {
			$vars = $this->_childSelf['AMF01600'][$i];
			if (!$vars) {
				break;
			}

			$eleAMF01600 = $domDoc->createElement('AMF01600');
			$ele->appendChild($eleAMF01600);

			$eleAMF01610 = $domDoc->createElement('AMF01610');
			$eleAMF01610->nodeValue = $vars['AMF01610'];
			$eleAMF01600->appendChild($eleAMF01610);

			if (!is_null($vars['AMF01620'])) {
				$eleAMF01620 = $domDoc->createElement('AMF01620');
				$eleAMF01620->nodeValue = $vars['AMF01620'];
				$eleAMF01600->appendChild($eleAMF01620);
			}

			$eleAMF01630 = $this->_getTag_CONTENTS_KOA210_3_AMF01630(array('domDoc' => $domDoc, 'vars' => $vars));
			$eleAMF01600->appendChild($eleAMF01630);

			$eleAMF01640 = $domDoc->createElement('AMF01640');
			$eleAMF01640->nodeValue = $vars['AMF01640'];
			$eleAMF01600->appendChild($eleAMF01640);

			if (!is_null($vars['AMF01645'])) {
				$eleAMF01645 = $domDoc->createElement('AMF01645');
				$eleAMF01645->nodeValue = $vars['AMF01645'];
				$eleAMF01600->appendChild($eleAMF01645);
			}

			$eleAMF01650 = $domDoc->createElement('AMF01650');
			$eleAMF01650->nodeValue = $vars['AMF01650'];
			$eleAMF01600->appendChild($eleAMF01650);

			$eleAMF01660 = $domDoc->createElement('AMF01660');
			$eleAMF01660->nodeValue = $vars['AMF01660'];
			$eleAMF01600->appendChild($eleAMF01660);

			if (!is_null($vars['AMF01670'])) {
				$eleAMF01670 = $domDoc->createElement('AMF01670');
				$eleAMF01670->nodeValue = $vars['AMF01670'];
				$eleAMF01600->appendChild($eleAMF01670);
			}

			if (!is_null($vars['AMF01690'])) {
				$eleAMF01680 = $domDoc->createElement('AMF01680');
				$eleAMF01600->appendChild($eleAMF01680);

				$eleAMF01690 = $domDoc->createElement('AMF01690');
				$eleAMF01690->nodeValue = $vars['AMF01690'];
				$eleAMF01680->appendChild($eleAMF01690);
			}

			if (!is_null($vars['AMF01720'])) {
				$eleAMF01720 = $domDoc->createElement('AMF01720');
				$eleAMF01720->nodeValue = $vars['AMF01720'];
				$eleAMF01600->appendChild($eleAMF01720);
			}

			if (!is_null($vars['AMF01730'])) {
				$eleAMF01730 = $domDoc->createElement('AMF01730');
				$eleAMF01730->nodeValue = $vars['AMF01730'];
				$eleAMF01600->appendChild($eleAMF01730);
			}

			if (!is_null($vars['AMF01740'])) {
				$eleAMF01740 = $domDoc->createElement('AMF01740');
				$eleAMF01740->nodeValue = $vars['AMF01740'];
				$eleAMF01600->appendChild($eleAMF01740);
			}

			if (!is_null($vars['AMF01750'])) {
				$eleAMF01750 = $domDoc->createElement('AMF01750');
				$eleAMF01750->nodeValue = $vars['AMF01750'];
				$eleAMF01600->appendChild($eleAMF01750);
			}

			if (!is_null($vars['AMF01760'])) {
				$eleAMF01760 = $domDoc->createElement('AMF01760');
				$eleAMF01760->nodeValue = $vars['AMF01760'];
				$eleAMF01600->appendChild($eleAMF01760);
			}

			if (!is_null($vars['AMF01770'])) {
				$eleAMF01770 = $domDoc->createElement('AMF01770');
				$eleAMF01770->nodeValue = $vars['AMF01770'];
				$eleAMF01600->appendChild($eleAMF01770);
			}

			if (!is_null($vars['AMF01780'])) {
				$eleAMF01780 = $domDoc->createElement('AMF01780');
				$eleAMF01780->nodeValue = $vars['AMF01780'];
				$eleAMF01600->appendChild($eleAMF01780);
			}

			if (!is_null($vars['AMF01790'])) {
				$eleAMF01790 = $domDoc->createElement('AMF01790');
				$eleAMF01790->nodeValue = $vars['AMF01790'];
				$eleAMF01600->appendChild($eleAMF01790);
			}

		}

		//AMF01800
		$eleAMF01800 = $domDoc->createElement('AMF01800');
		$ele->appendChild($eleAMF01800);

		//AMF01810
		$eleAMF01810 = $domDoc->createElement('AMF01810');
		$eleAMF01810->nodeValue = $this->_childSelf['AMF01810'];
		$eleAMF01800->appendChild($eleAMF01810);

		//AMF01820
		$eleAMF01820 = $domDoc->createElement('AMF01820');
		$eleAMF01820->nodeValue = $this->_childSelf['AMF01820'];
		$eleAMF01800->appendChild($eleAMF01820);

		//AMF01830
		$eleAMF01830 = $domDoc->createElement('AMF01830');
		$eleAMF01830->nodeValue = $this->_childSelf['AMF01830'];
		$eleAMF01800->appendChild($eleAMF01830);

		//AMF01840
		$eleAMF01840 = $domDoc->createElement('AMF01840');
		$eleAMF01840->nodeValue = $this->_childSelf['AMF01840'];
		$eleAMF01800->appendChild($eleAMF01840);

		//AMF01850
		$eleAMF01850 = $domDoc->createElement('AMF01850');
		$eleAMF01850->nodeValue = $this->_childSelf['AMF01850'];
		$eleAMF01800->appendChild($eleAMF01850);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_3_AMF01630($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMF01630');

		$eleGen = $domDoc->createElement('gen:era');
		$eleGen->nodeValue = $arr['vars']['AMF01630_gen_era'];
		$ele->appendChild($eleGen);

		$eleGen = $domDoc->createElement('gen:yy');
		$eleGen->nodeValue = $arr['vars']['AMF01630_gen_yy'];
		$ele->appendChild($eleGen);

		$eleGen = $domDoc->createElement('gen:mm');
		$eleGen->nodeValue = $arr['vars']['AMF01630_gen_mm'];
		$ele->appendChild($eleGen);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_3_AMF01870($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMF01870');

		//AMF01880
		$eleAMF01880 = $domDoc->createElement('AMF01880');
		$ele->appendChild($eleAMF01880);

		//AMF01890
		$eleAMF01890 = $domDoc->createElement('AMF01890');
		$eleAMF01890->nodeValue = $this->_childSelf['AMF01890'];
		$eleAMF01880->appendChild($eleAMF01890);

		//AMF01900
		$eleAMF01900 = $domDoc->createElement('AMF01900');
		$eleAMF01900->nodeValue = $this->_childSelf['AMF01900'];
		$eleAMF01880->appendChild($eleAMF01900);

		//AMF01910
		$eleAMF01910 = $domDoc->createElement('AMF01910');
		$eleAMF01910->nodeValue = $this->_childSelf['AMF01910'];
		$ele->appendChild($eleAMF01910);

		//AMF01920
		$eleAMF01920 = $domDoc->createElement('AMF01920');
		$eleAMF01920->nodeValue = $this->_childSelf['AMF01920'];
		$ele->appendChild($eleAMF01920);

		//AMF01930
		$eleAMF01930 = $domDoc->createElement('AMF01930');
		$eleAMF01930->nodeValue = $this->_childSelf['AMF01930'];
		$ele->appendChild($eleAMF01930);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_3_AMF01940($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMF01940');

		//AMF01950
		$eleAMF01950 = $domDoc->createElement('AMF01950');
		$ele->appendChild($eleAMF01950);

		//AMF01960
		$eleAMF01960 = $domDoc->createElement('AMF01960');
		$eleAMF01960->nodeValue = '';
		$eleAMF01950->appendChild($eleAMF01960);

		//AMF01970
		$eleAMF01970 = $domDoc->createElement('AMF01970');
		$eleAMF01970->nodeValue = '';
		$eleAMF01950->appendChild($eleAMF01970);

		//AMF01980
		$eleAMF01980 = $domDoc->createElement('AMF01980');
		$eleAMF01980->nodeValue = '';
		$ele->appendChild($eleAMF01980);

		//AMF01990
		$eleAMF01990 = $domDoc->createElement('AMF01990');
		$ele->appendChild($eleAMF01990);

		//AMF02000
		$eleAMF02000 = $domDoc->createElement('AMF02000');
		$eleAMF02000->nodeValue = $this->_childSelf['AMF02000'];
		$eleAMF01990->appendChild($eleAMF02000);

		//AMF02020
		$eleAMF02020 = $domDoc->createElement('AMF02020');
		$eleAMF02020->nodeValue = $this->_childSelf['AMF02020'];
		$eleAMF01990->appendChild($eleAMF02020);

		//AMF02030
		$eleAMF02030 = $domDoc->createElement('AMF02030');
		$eleAMF02030->nodeValue = $this->_childSelf['AMF02030'];
		$ele->appendChild($eleAMF02030);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_3_AMF02040($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMF02040');

		//AMF02050
		$eleAMF02050 = $domDoc->createElement('AMF02050');
		$ele->appendChild($eleAMF02050);

		//AMF02060
		$eleAMF02060 = $domDoc->createElement('AMF02060');
		$eleAMF02060->nodeValue = '';
		$eleAMF02050->appendChild($eleAMF02060);

		//AMF02070
		$eleAMF02070 = $domDoc->createElement('AMF02070');
		$eleAMF02070->nodeValue = '';
		$eleAMF02050->appendChild($eleAMF02070);

		//AMF02080
		$eleAMF02080 = $domDoc->createElement('AMF02080');
		$eleAMF02080->nodeValue = $this->_childSelf['AMF02080'];
		$ele->appendChild($eleAMF02080);

		//AMF02090
		$eleAMF02090 = $domDoc->createElement('AMF02090');
		$eleAMF02090->nodeValue = $this->_childSelf['AMF02090'];
		$ele->appendChild($eleAMF02090);

		//AMF02100
		$eleAMF02100 = $domDoc->createElement('AMF02100');
		$eleAMF02100->nodeValue = $this->_childSelf['AMF02100'];
		$ele->appendChild($eleAMF02100);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_4($arr)
	{
		global $classTime;

		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('KOA210-4');

		$attr = $domDoc->createAttribute('page');
		$attr->value = '1';
		$ele->appendChild($attr);

		//AMG00000
		$eleAMG00000 = $this->_getTag_CONTENTS_KOA210_4_AMG00000(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMG00000);

		//AMH00000
		if ($this->_childSelf['flagCR']) {
			$eleAMH00000 = $this->_getTag_CONTENTS_KOA210_4_AMH00000(array('domDoc' => $domDoc,));
			$ele->appendChild($eleAMH00000);
		}

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_4_AMG00000($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMG00000');

		//AMG00010
		$eleAMG00010 = $this->_getTag_CONTENTS_KOA210_4_AMG00010(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMG00010);

		//AMG00020
		$eleAMG00020 = $this->_getTag_CONTENTS_KOA210_4_AMG00020(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMG00020);

		//AMG00450
		$eleAMG00450 = $this->_getTag_CONTENTS_KOA210_4_AMG00450(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMG00450);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_4_AMG00010($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMG00010');

		$eleGen = $domDoc->createElement('gen:era');
		$eleGen->nodeValue = '4';
		$ele->appendChild($eleGen);

		//--------------------------------------------------------------------------------------------------------------------------------------
		$eleGen = $domDoc->createElement('gen:yy');

		/*変更箇所*/
		if ($this->_extSelf['numYearSheet'] == 2014) {
			$eleGen->nodeValue = '26';

		} elseif ($this->_extSelf['numYearSheet'] == 2015) {
			$eleGen->nodeValue = '27';

		} elseif ($this->_extSelf['numYearSheet'] == 2016) {
			$eleGen->nodeValue = '28';

		} elseif ($this->_extSelf['numYearSheet'] == 2017) {
		    $eleGen->nodeValue = '29';

		} else {
			$eleGen->nodeValue = '30';
		}
		$ele->appendChild($eleGen);

		$eleGen = $domDoc->createElement('gen:mm');
		$eleGen->nodeValue = '12';
		$ele->appendChild($eleGen);

		$eleGen = $domDoc->createElement('gen:dd');
		$eleGen->nodeValue = '31';
		$ele->appendChild($eleGen);

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_4_AMG00020($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMG00020');

		for ($i=0;$i<7;$i++) {
			$vars = $this->_childSelf['AMG00025'][$i];
			if (!$vars) {
				break;
			}

			$eleAMG00025 = $domDoc->createElement('AMG00025');
			$ele->appendChild($eleAMG00025);

			$eleAMG00030 = $domDoc->createElement('AMG00030');
			$eleAMG00030->nodeValue = $vars['AMG00030'];
			$eleAMG00025->appendChild($eleAMG00030);

			$eleAMG00220 = $domDoc->createElement('AMG00220');
			$eleAMG00220->nodeValue = $vars['AMG00220'];
			$eleAMG00025->appendChild($eleAMG00220);

			$eleAMG00420 = $domDoc->createElement('AMG00420');
			$eleAMG00420->nodeValue = $vars['AMG00420'];
			$eleAMG00025->appendChild($eleAMG00420);
		}

		//AMG00040
		$eleAMG00040 = $domDoc->createElement('AMG00040');
		$ele->appendChild($eleAMG00040);

		$eleAMG00050 = $domDoc->createElement('AMG00050');
		$eleAMG00040->appendChild($eleAMG00050);

		$eleGen = $domDoc->createElement('gen:mm');
		$eleGen->nodeValue = '1';
		$eleAMG00050->appendChild($eleGen);

		$eleGen = $domDoc->createElement('gen:dd');
		$eleGen->nodeValue = '1';
		$eleAMG00050->appendChild($eleGen);

		for ($i=6;$i<=23;$i++) {
			if ($i == 22) {
				continue;
			}
			$str = 'AMG00' . $i . '0';
			if ($i < 10) {
				$str = 'AMG000' . $i . '0';
			}

			$eleTemp = $domDoc->createElement($str);
			$eleTemp->nodeValue = $this->_childSelf[$str];
			$eleAMG00040->appendChild($eleTemp);
		}


		//AMG00240
		$eleAMG00240 = $domDoc->createElement('AMG00240');
		$ele->appendChild($eleAMG00240);

		$eleAMG00250 = $domDoc->createElement('AMG00250');
		$eleAMG00240->appendChild($eleAMG00250);

		$eleGen = $domDoc->createElement('gen:mm');
		$eleGen->nodeValue = '12';
		$eleAMG00250->appendChild($eleGen);

		$eleGen = $domDoc->createElement('gen:dd');
		$eleGen->nodeValue = '31';
		$eleAMG00250->appendChild($eleGen);

		for ($i=26;$i<=44;$i++) {
			if ($i == 42) {
				continue;
			}
			$str = 'AMG00' . $i . '0';

			$eleTemp = $domDoc->createElement($str);
			$eleTemp->nodeValue = $this->_childSelf[$str];
			$eleAMG00240->appendChild($eleTemp);
		}

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_4_AMG00450($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMG00450');

		$eleAMG00460 = $domDoc->createElement('AMG00460');
		$ele->appendChild($eleAMG00460);


		for ($i=0;$i<7;$i++) {
			$vars = $this->_childSelf['AMG00465'][$i];
			if (!$vars) {
				break;
			}

			$eleAMG00465 = $domDoc->createElement('AMG00465');
			$eleAMG00460->appendChild($eleAMG00465);

			$eleAMG00470 = $domDoc->createElement('AMG00470');
			$eleAMG00470->nodeValue = $vars['AMG00470'];
			$eleAMG00465->appendChild($eleAMG00470);

			$eleAMG00570 = $domDoc->createElement('AMG00570');
			$eleAMG00570->nodeValue = $vars['AMG00570'];
			$eleAMG00465->appendChild($eleAMG00570);

			$eleAMG00700 = $domDoc->createElement('AMG00700');
			$eleAMG00700->nodeValue = $vars['AMG00700'];
			$eleAMG00465->appendChild($eleAMG00700);
		}

		for ($i=0;$i<7;$i++) {
			$vars = $this->_childSelf['AMG00475'][$i];
			if (!$vars) {
				break;
			}

			$eleAMG00475 = $domDoc->createElement('AMG00475');
			$eleAMG00460->appendChild($eleAMG00475);

			$eleAMG00480 = $domDoc->createElement('AMG00480');
			$eleAMG00480->nodeValue = $vars['AMG00480'];
			$eleAMG00475->appendChild($eleAMG00480);

			$eleAMG00590 = $domDoc->createElement('AMG00590');
			$eleAMG00590->nodeValue = $vars['AMG00590'];
			$eleAMG00475->appendChild($eleAMG00590);

			$eleAMG00720 = $domDoc->createElement('AMG00720');
			$eleAMG00720->nodeValue = $vars['AMG00720'];
			$eleAMG00475->appendChild($eleAMG00720);
		}

		//AMG00490
		$eleAMG00490 = $domDoc->createElement('AMG00490');
		$ele->appendChild($eleAMG00490);

		$eleAMG00500 = $domDoc->createElement('AMG00500');
		$eleAMG00490->appendChild($eleAMG00500);

		$eleGen = $domDoc->createElement('gen:mm');
		$eleGen->nodeValue = '1';
		$eleAMG00500->appendChild($eleGen);

		$eleGen = $domDoc->createElement('gen:dd');
		$eleGen->nodeValue = '1';
		$eleAMG00500->appendChild($eleGen);

		for ($i=51;$i<=61;$i++) {
			if ($i == 57 || $i == 59) {
				continue;
			}
			$str = 'AMG00' . $i . '0';

			$eleTemp = $domDoc->createElement($str);
			$eleTemp->nodeValue = $this->_childSelf[$str];
			$eleAMG00490->appendChild($eleTemp);
		}

		//AMG00620
		$eleAMG00620 = $domDoc->createElement('AMG00620');
		$ele->appendChild($eleAMG00620);

		$eleAMG00630 = $domDoc->createElement('AMG00630');
		$eleAMG00620->appendChild($eleAMG00630);

		$eleGen = $domDoc->createElement('gen:mm');
		$eleGen->nodeValue = '12';
		$eleAMG00630->appendChild($eleGen);

		$eleGen = $domDoc->createElement('gen:dd');
		$eleGen->nodeValue = '31';
		$eleAMG00630->appendChild($eleGen);

		for ($i=64;$i<=76;$i++) {
			if ($i == 70 || $i == 72) {
				continue;
			}
			$str = 'AMG00' . $i . '0';

			$eleTemp = $domDoc->createElement($str);
			$eleTemp->nodeValue = $this->_childSelf[$str];
			$eleAMG00620->appendChild($eleTemp);
		}

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_4_AMH00000($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMH00000');

		//AMH00020
		$eleAMH00020 = $this->_getTag_CONTENTS_KOA210_4_AMH00020(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMH00020);

		//AMH00080
		$eleAMH00080 = $domDoc->createElement('AMH00080');
		$eleAMH00080->nodeValue = $this->_childSelf['AMH00080'];
		$ele->appendChild($eleAMH00080);

		//AMH00090
		$eleAMH00090 = $this->_getTag_CONTENTS_KOA210_4_AMH00090(array('domDoc' => $domDoc,));
		$ele->appendChild($eleAMH00090);

		for ($i=18;$i<=22;$i++) {
			$str = 'AMH00' . $i . '0';
			if ($i < 10) {
				$str = 'AMH000' . $i . '0';
			}
			$eleTemp = $domDoc->createElement($str);
			$eleTemp->nodeValue = $this->_childSelf[$str];
			$ele->appendChild($eleTemp);
		}

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_4_AMH00020($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMH00020');

		for ($i=3;$i<=7;$i++) {
			$str = 'AMH00' . $i . '0';
			if ($i < 10) {
				$str = 'AMH000' . $i . '0';
			}

			$eleTemp = $domDoc->createElement($str);
			$eleTemp->nodeValue = $this->_childSelf[$str];
			$ele->appendChild($eleTemp);
		}

		return $ele;
	}

	/**
	 */
	protected function _getTag_CONTENTS_KOA210_4_AMH00090($arr)
	{
		$domDoc = &$arr['domDoc'];

		$ele = $domDoc->createElement('AMH00090');

		for ($i=10;$i<=14;$i++) {
			$str = 'AMH00' . $i . '0';
			$eleTemp = $domDoc->createElement($str);
			$eleTemp->nodeValue = $this->_childSelf[$str];
			$ele->appendChild($eleTemp);
		}

		if ($this->_childSelf['AMH00145']) {
			$array = $this->_childSelf['AMH00145'];
			foreach ($array as $key => $value) {
				$eleAMH00145 = $domDoc->createElement('AMH00145');
				$ele->appendChild($eleAMH00145);

				$str = 'AMH00010';
				$eleTemp = $domDoc->createElement($str);
				$eleTemp->nodeValue = $value[$str];
				$eleAMH00145->appendChild($eleTemp);

				$str = 'AMH00150';
				$eleTemp = $domDoc->createElement($str);
				$eleTemp->nodeValue = $value[$str];
				$eleAMH00145->appendChild($eleTemp);
			}
		}

		for ($i=16;$i<=17;$i++) {
			$str = 'AMH00' . $i . '0';
			$eleTemp = $domDoc->createElement($str);
			$eleTemp->nodeValue = $this->_childSelf[$str];
			$ele->appendChild($eleTemp);
		}

		return $ele;
	}











}
