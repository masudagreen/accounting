<?php

$vars = array(

	'free' => array(
		array( 'value' => 'none', 'strTitle' => '対象外', 'strYayoi' => '対象外', ),
	),
	//比例配分
	'generalProration' => array(
		array( 'value' => 'none', 'strTitle' => '対象外', 'strYayoi' => '対象外', ),

		//税率
		array( 'value' => 'tax', 'strTitle' => '課税売上', 'strYayoi' => '課税売上<>[]', ),
		array( 'value' => 'tax-Back', 'strTitle' => '課税売上返還', 'strYayoi' => '課税売返<>[]', ),
		array( 'value' => 'tax-Bad', 'strTitle' => '課税売上貸倒', 'strYayoi' => '課税売倒<>[]', ),
		array( 'value' => 'tax-Getback', 'strTitle' => '課税売上貸倒回収', 'strYayoi' => '課税売回<>[]', ),

		array( 'value' => 'exemption', 'strTitle' => '輸出売上', 'strYayoi' => '輸出売上', ),
		array( 'value' => 'exemption-Back', 'strTitle' => '輸出売上返還', 'strYayoi' => '輸出売返',  ),
		array( 'value' => 'exemption-Bad', 'strTitle' => '輸出売上貸倒', 'strYayoi' => '輸出売倒',  ),
		array( 'value' => 'free', 'strTitle' => '非課税売上', 'strYayoi' => '非課売上', ),
		array( 'value' => 'free-Back', 'strTitle' => '非課税返還', 'strYayoi' => '非課売返',  ),
		array( 'value' => 'free-Bad', 'strTitle' => '非課税貸倒', 'strYayoi' => '非課売倒',  ),
		array( 'value' => 'free-Assets', 'strTitle' => '非課税資産輸出', 'strYayoi' => '非資輸出', ),
		array( 'value' => 'free-AssetsBack', 'strTitle' => '非課税資産輸出返還', 'strYayoi' => '非資輸返',  ),
		array( 'value' => 'free-AssetsBad', 'strTitle' => '非課税資産輸出貸倒', 'strYayoi' => '非資輸倒',  ),
		array( 'value' => 'none-Sales', 'strTitle' => '対象外売上', 'strYayoi' => '対外売上', ),
		array( 'value' => 'free-Securities', 'strTitle' => '有価証券譲渡', 'strYayoi' => '有価譲渡', ),
		array( 'value' => 'free-MonetaryClaim', 'strTitle' => '金銭債権譲渡', 'strYayoi' => '不明', ),

		//税率
		array( 'value' => 'taxDebit', 'strTitle' => '課税対応仕入', 'strYayoi' => '課対仕入<>[]', ),
		array( 'value' => 'taxDebit-Getback', 'strTitle' => '課税対応仕入返還', 'strYayoi' => '課対仕返<>[]', ),

		array( 'value' => 'else-Body', 'strTitle' => '課税対応輸入本体', 'strYayoi' => '課対輸本[]',  ),
		array( 'value' => 'else-Tax', 'strTitle' => '課税対応輸入消費税', 'strYayoi' => '課対輸税[]',  ),
		array( 'value' => 'else-TaxLocal', 'strTitle' => '地方消費税貨物割', 'strYayoi' => '地消貨割[]',  ),

		array( 'value' => 'freeDebit', 'strTitle' => '非課税仕入', 'strYayoi' => '非課仕入',  ),
		array( 'value' => 'none-Stock', 'strTitle' => '対象外仕入', 'strYayoi' => '対外仕入',  ),
	),
	//個別
	'generalEach' => array(
		array( 'value' => 'none', 'strTitle' => '対象外', 'strYayoi' => '対象外', ),

		//税率
		array( 'value' => 'tax', 'strTitle' => '課税売上', 'strYayoi' => '課税売上<>[]', ),
		array( 'value' => 'tax-Back', 'strTitle' => '課税売上返還', 'strYayoi' => '課税売返<>[]', ),
		array( 'value' => 'tax-Bad', 'strTitle' => '課税売上貸倒', 'strYayoi' => '課税売倒<>[]', ),
		array( 'value' => 'tax-Getback', 'strTitle' => '課税売上貸倒回収', 'strYayoi' => '課税売回<>[]', ),


		array( 'value' => 'exemption', 'strTitle' => '輸出売上', 'strYayoi' => '輸出売上', ),
		array( 'value' => 'exemption-Back', 'strTitle' => '輸出売上返還', 'strYayoi' => '輸出売返',  ),
		array( 'value' => 'exemption-Bad', 'strTitle' => '輸出売上貸倒', 'strYayoi' => '輸出売倒',  ),
		array( 'value' => 'free', 'strTitle' => '非課税売上', 'strYayoi' => '非課売上', ),
		array( 'value' => 'free-Back', 'strTitle' => '非課税返還', 'strYayoi' => '非課売返',  ),
		array( 'value' => 'free-Bad', 'strTitle' => '非課税貸倒', 'strYayoi' => '非課売倒',  ),
		array( 'value' => 'free-Assets', 'strTitle' => '非課税資産輸出', 'strYayoi' => '非資輸出', ),
		array( 'value' => 'free-AssetsBack', 'strTitle' => '非課税資産輸出返還', 'strYayoi' => '非資輸返',  ),
		array( 'value' => 'free-AssetsBad', 'strTitle' => '非課税資産輸出貸倒', 'strYayoi' => '非資輸倒',  ),
		array( 'value' => 'none-Sales', 'strTitle' => '対象外売上', 'strYayoi' => '対外売上', ),
		array( 'value' => 'free-Securities', 'strTitle' => '有価証券譲渡', 'strYayoi' => '有価譲渡', ),
		array( 'value' => 'free-MonetaryClaim', 'strTitle' => '金銭債権譲渡', 'strYayoi' => '不明', ),


		//税率
		array( 'value' => 'taxDebit', 'strTitle' => '課税対応仕入', 'strYayoi' => '課対仕入<>[]', ),
		array( 'value' => 'taxDebit-Getback', 'strTitle' => '課税対応仕入返還', 'strYayoi' => '課対仕返<>[]', ),
		array( 'value' => 'taxDebit-Free', 'strTitle' => '非課税対応仕入', 'strYayoi' => '非対仕入<>[]', ),
		array( 'value' => 'taxDebit-FreeGetback', 'strTitle' => '非課税対応仕入返還', 'strYayoi' => '非対仕返<>[]', ),
		array( 'value' => 'taxDebit-Common', 'strTitle' => '共通対応仕入', 'strYayoi' => '共対仕入<>[]', ),
		array( 'value' => 'taxDebit-CommonGetback', 'strTitle' => '共通対応仕入返還', 'strYayoi' => '共対仕返<>[]', ),

		array( 'value' => 'else-Body', 'strTitle' => '課税対応輸入本体', 'strYayoi' => '課対輸本[]',  ),
		array( 'value' => 'else-FreeBody', 'strTitle' => '非課対応輸入本体', 'strYayoi' => '非対輸本[]',  ),
		array( 'value' => 'else-CommonBody', 'strTitle' => '共通対応輸入本体', 'strYayoi' => '共対輸本[]',  ),

		array( 'value' => 'else-Tax', 'strTitle' => '課税対応輸入消費税', 'strYayoi' => '課対輸税[]',  ),
		array( 'value' => 'else-FreeTax', 'strTitle' => '非課対応輸入消費税', 'strYayoi' => '非対輸税[]',  ),
		array( 'value' => 'else-CommonTax', 'strTitle' => '共通対応輸入消費税', 'strYayoi' => '共対輸税[]',  ),

		array( 'value' => 'else-TaxLocal', 'strTitle' => '地方消費税貨物割', 'strYayoi' => '地消貨割[]',  ),
		array( 'value' => 'freeDebit', 'strTitle' => '非課税仕入', 'strYayoi' => '非課仕入',  ),
		array( 'value' => 'none-Stock', 'strTitle' => '対象外仕入', 'strYayoi' => '対外仕入',  ),
	),
	'simple' => array(
		array( 'value' => 'none', 'strTitle' => '対象外', 'strYayoi' => '対象外', ),

		array( 'value' => 'tax-1', 'strTitle' => '課税売上簡易一種', 'strYayoi' => '課税売上<>一[]', ),
		array( 'value' => 'tax-2', 'strTitle' => '課税売上簡易二種', 'strYayoi' => '課税売上<>二[]', ),
		array( 'value' => 'tax-3', 'strTitle' => '課税売上簡易三種', 'strYayoi' => '課税売上<>三[]', ),
		array( 'value' => 'tax-4', 'strTitle' => '課税売上簡易四種', 'strYayoi' => '課税売上<>四[]', ),
		array( 'value' => 'tax-5', 'strTitle' => '課税売上簡易五種', 'strYayoi' => '課税売上<>五[]', ),
		array( 'value' => 'tax-unknown', 'strTitle' => '課税売上簡易売上不明', 'strYayoi' => '課税売上<>[]', ),

		array( 'value' => 'tax-Back-1', 'strTitle' => '課税売上返還簡易返還一種', 'strYayoi' => '課税売返<>一[]', ),
		array( 'value' => 'tax-Back-2', 'strTitle' => '課税売上返還簡易返還二種', 'strYayoi' => '課税売返<>二[]', ),
		array( 'value' => 'tax-Back-3', 'strTitle' => '課税売上返還簡易返還三種', 'strYayoi' => '課税売返<>三[]', ),
		array( 'value' => 'tax-Back-4', 'strTitle' => '課税売上返還簡易返還四種', 'strYayoi' => '課税売返<>四[]', ),
		array( 'value' => 'tax-Back-5', 'strTitle' => '課税売上返還簡易返還五種', 'strYayoi' => '課税売返<>五[]', ),
		array( 'value' => 'tax-Back-unknown', 'strTitle' => '課税売上返還簡易売返還不明', 'strYayoi' => '課税売返<>[]', ),

		array( 'value' => 'tax-Bad', 'strTitle' => '課税売上貸倒', 'strYayoi' => '課税売倒<>[]', ),
		array( 'value' => 'tax-Getback', 'strTitle' => '課税売上貸倒回収', 'strYayoi' => '課税売回<>[]', ),

		array( 'value' => 'exemption', 'strTitle' => '輸出売上', 'strYayoi' => '輸出売上', ),
		array( 'value' => 'exemption-Back', 'strTitle' => '輸出売上返還', 'strYayoi' => '輸出売返',  ),
		array( 'value' => 'exemption-Bad', 'strTitle' => '輸出売上貸倒', 'strYayoi' => '輸出売倒',  ),
		array( 'value' => 'free', 'strTitle' => '非課税売上', 'strYayoi' => '非課売上', ),
		array( 'value' => 'free-Back', 'strTitle' => '非課税返還', 'strYayoi' => '非課売返',  ),
		array( 'value' => 'free-Bad', 'strTitle' => '非課税貸倒', 'strYayoi' => '非課売倒',  ),
		array( 'value' => 'free-Assets', 'strTitle' => '非課税資産輸出', 'strYayoi' => '非資輸出', ),
		array( 'value' => 'free-AssetsBack', 'strTitle' => '非課税資産輸出返還', 'strYayoi' => '非資輸返',  ),
		array( 'value' => 'free-AssetsBad', 'strTitle' => '非課税資産輸出貸倒', 'strYayoi' => '非資輸倒',  ),
		array( 'value' => 'none-Sales', 'strTitle' => '対象外売上', 'strYayoi' => '対外売上', ),
		array( 'value' => 'free-Securities', 'strTitle' => '有価証券譲渡', 'strYayoi' => '有価譲渡', ),
		array( 'value' => 'free-MonetaryClaim', 'strTitle' => '金銭債権譲渡', 'strYayoi' => '不明', ),

		array( 'value' => 'taxDebit', 'strTitle' => '課税対応仕入', 'strYayoi' => '課対仕入<>[]', ),
		array( 'value' => 'taxDebit-Getback', 'strTitle' => '課税対応仕入返還', 'strYayoi' => '課対仕返<>[]', ),

		array( 'value' => 'else-Body', 'strTitle' => '課税対応輸入本体', 'strYayoi' => '課対輸本[]',  ),
		array( 'value' => 'else-Tax', 'strTitle' => '課税対応輸入消費税', 'strYayoi' => '課対輸税[]',  ),

		array( 'value' => 'else-TaxLocal', 'strTitle' => '地方消費税貨物割', 'strYayoi' => '地消貨割[]',  ),

		array( 'value' => 'freeDebit', 'strTitle' => '非課税仕入', 'strYayoi' => '非課仕入',  ),
		array( 'value' => 'none-Stock', 'strTitle' => '対象外仕入', 'strYayoi' => '対外仕入',  ),

	),
	'varsStr' => array(
		'strIncluding' => '込',
		'varsWithoutCalc' => array( '1' => '内', '2' => '外', '3' => '別'),
		'varsWithoutCalc2' => array( '1' => '内税', '2' => '外税', '3' => '別記'),
		'varsRate' => array(
			'5' => array('tax' => '5%', 'else-TaxLocal'=> '1%', 'else'=> '4%'),
			'8' => array('tax' => '8%', 'else-TaxLocal'=> '1.7%', 'else'=> '6.3%'),
			'10' => array('tax' => '10%', 'else-TaxLocal'=> '2.2%', 'else'=> '7.8%'),
		),
	),
	'varsId' => array(
		'flags' => '識別フラグ',
		'no' => '伝票No',
		'flagFiscalReport' => '決算',
		'stampBook' => '取引日付',
		'idAccountTitleDebit' => '借方勘定科目',
		'idSubAccountTitleDebit' => '借方補助科目',
		'idDepartmentDebit' => '借方部門',
		'flagConsumptionTaxDebit' => '借方税区分',
		'numValueDebit' => '借方金額',
		'numValueConsumptionTaxDebit' => '借方消費税金額',

		'idAccountTitleCredit' => '貸方勘定科目',
		'idSubAccountTitleCredit' => '貸方補助科目',
		'idDepartmentCredit' => '貸方部門',
		'flagConsumptionTaxCredit' => '貸方税区分',
		'numValueCredit' => '貸方金額',
		'numValueConsumptionTaxCredit' => '貸方消費税金額',
		'strTitle' => '摘要',

		'num' => '番号',
		'strLimit' => '期日',
		'numType' => 'タイプ',
		'strOutput' => '生成元',
		'strMemo' => '仕訳メモ',
		'strFusen1' => '付箋1',
		'strFusen2' => '付箋2',
		'strFix' => '調整',
	),
	'varsCheck' => array(
		'varsFlagFiscalReport' => array(
			'本決' => '年決',
			'中決' => '中決',
		),
		'strLost' => '不明',
	),
	'varsComment' => array(
			'strStatus' => '伝票番号【 <%replace%> 】<br>',
			'strStatusRow' => '【 <%replace%>行目 】<br>',
			'strFormat' => ' : フォーマットが正しくないようです。',
			'strNo' => ' : すべての行に伝票番号が付されている必要があるようです。',
			'strFlagConsumptionTax' => ' : インポートできない税区分があるようです。消費税設定が正しいかどうかも併せて確認してください。',
			'strFlagConsumptionTaxLost' => ' : 「不明」は不明原因を解消してからインポートする必要があるようです。',
		),
);
