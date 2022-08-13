<?php

$vars = array(
	'arrSelectTag' => array(
		'detail' => array(

		),
	),
	'varsTmpl' => array(
		'detail' => array(
			'varsTextAccountTitle' => array(
				'flagMustUse' => 0,
				'id' => 'TextAccountTitle',
				'strTitle' => '',
				'value' => '', 'valueStr' => '', 'flagValueType' => 'str',
				'arrayError' => array(
					array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ),),
					array( 'flagCheck' => 'max', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'str', 'num' => 15, 'strComment' => array( 'common' => '入力文字数が多すぎるようです。', ),),
				),
				'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 15,
				'numWidth' => 40, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => 'px',
				'arrayOption' => array(),
				'flagForm' => 'active',
				'flagLoop' => 1,
				'flagCsv' => 1,
			),
			'varsTextName' => array(
				'flagMustUse' => 0,
				'id' => 'TextName',
				'strTitle' => '',
				'value' => '', 'valueStr' => '', 'flagValueType' => 'str',
				'arrayError' => array(
					array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ),),
					array( 'flagCheck' => 'max', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'str', 'num' => 15, 'strComment' => array( 'common' => '入力文字数が多すぎるようです。', ),),
				),
				'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 15,
				'numWidth' => 40, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => 'px',
				'arrayOption' => array(),
				'flagForm' => 'active',
				'flagLoop' => 1,
				'flagCsv' => 1,
			),
			'varsTextAddress' => array(
				'flagMustUse' => 0,
				'id' => 'TextAddress',
				'strTitle' => '',
				'value' => '', 'valueStr' => '', 'flagValueType' => 'str',
				'arrayError' => array(
					array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ),),
					array( 'flagCheck' => 'max', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'str', 'num' => 30, 'strComment' => array( 'common' => '入力文字数が多すぎるようです。', ),),
				),
				'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 30,
				'numWidth' => 40, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => 'px',
				'arrayOption' => array(),
				'flagForm' => 'active',
				'flagLoop' => 1,
				'flagCsv' => 1,
			),
			'varsTextValue' => array(
				'flagMustUse' => 0,
				'id' => 'TextValue',
				'strTitle' => '',
				'value' => '', 'valueStr' => '', 'flagValueType' => 'num',
				'flagErrorNow' => 0,
				'arrayError' => array(
					array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ),),
					array( 'flagCheck' => 'format', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'numminus', 'strComment' => array( 'common' => '半角数字で記入する必要があるようです。',  ), ),
					array( 'flagCheck' => 'max', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'num', 'num' => 999999999, 'strComment' => array( 'common' => '最大値を上回ってしまうようです。', ),),
					array( 'flagCheck' => 'min', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'num', 'num' => -99999999, 'strComment' => array( 'common' => '最小値を下回ってしまうようです。', ),),
				),
				'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 9,
				'numWidth' => 40, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => 'px',
				'arrayOption' => array(),
				'flagForm' => 'active',
				'flagLoop' => 1,
				'flagCsv' => 1,
			),
			'varsTextMemo' => array(
				'flagMustUse' => 0,
				'id' => 'TextMemo',
				'strTitle' => '',
				'value' => '', 'valueStr' => '', 'flagValueType' => 'str',
				'arrayError' => array(
					array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ),),
					array( 'flagCheck' => 'max', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'str', 'num' => 15, 'strComment' => array( 'common' => '入力文字数が多すぎるようです。', ),),
				),
				'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 15,
				'numWidth' => 40, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => 'px',
				'arrayOption' => array(),
				'flagForm' => 'active',
				'flagLoop' => 1,
				'flagCsv' => 1,
			),
			'varsSelectIdAccountTitle' => array(
				'flagMustUse' => 0,
				'id' => 'SelectIdAccountTitle',
				'strTitle' => '',
				'value' => 'none', 'valueStr' => '', 'flagValueType' => 'select',
				'arrayError' => array(
					array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ),),
				),
				'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
				'numWidth' => 40, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => 'px',
				'arrayOption' => array(),
				'flagForm' => 'active',
				'flagLoop' => 1,
			),
			'varsSelectIdSubAccountTitle' => array(
				'flagMustUse' => 0,
				'id' => 'SelectIdSubAccountTitle',
				'strTitle' => '',
				'value' => 0, 'valueStr' => '', 'flagValueType' => 'select',
				'arrayError' => array(
					array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ),),
				),
				'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
				'numWidth' => 40, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => 'px',
				'arrayOption' => array(),
				'flagForm' => 'active',
				'flagLoop' => 1,
			),
			'varsTextSum' => array(
				'flagMustUse' => 0,
				'id' => 'TextSum',
				'strTitle' => '',
				'value' => '', 'valueStr' => '', 'flagValueType' => 'numStr',
				'flagErrorNow' => 0,
				'arrayError' => array(
					array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ),),
					array( 'flagCheck' => 'format', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'numminus', 'strComment' => array( 'common' => '半角数字で記入する必要があるようです。',  ), ),
				),
				'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 0,
				'numWidth' => 40, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => 'px',
				'arrayOption' => array(),
				'flagForm' => 'none',
			),
		),
	),
	'varsStr' => array(
		'detail' => array(
			'arrRows' => array(),
			'flagDebit' => 0,
			'numRows' => 23,
			'strSub0' => '補助科目未指定残高',
			'strBlank' => '空欄',

			'strSync' => 'データ参照先',

			'strAccountTitle' => '科目',
			'strTarget' => '相手先',
			'strName' => '名称(氏名)',
			'strAddress' => '所在地(住所)',
			'strValue' => '期末現在高',
			'strMemo' => '摘要',

			'strTitleExt' => '買掛金（未払金・未払費用）の内訳書',
			'strTitle' => '買掛金(未払金・未払費用)の内訳書',
			'strTitleSub' => '',
			'strEntityExt' => '',
			'strNumExt' => '',
			'strPageExt' => '',
			'strTitleNum' => '⑨',
			'strBlank' => '　',

			'strIdAccountTitle' => '勘定科目',
			'strIdSubAccountTitle' => '補助科目',
			'strSum' => '計',
			'strUnit' => '(単位：円)',
			'strSeparate' => '/',
			'strCautionLaw' => '(法0302－9)',
			'strCautionMark' => '(注)',
			'strCaution1' => '１．「科目」欄には、買掛金、未払金、未払費用の別を記入してください。',
			'strCaution2' => '２．相手先別期末現在高が５０万円以上のもの(５０万円以上のものが５口未満のときは期末現在高の多額のものから５口程度)については各別に記入し、その他は一括して記入してください。',
			'strCaution3' => '３．未払金については、その取引内容を摘要欄に記入してください。',
			'strCaution4' => '４．未払配当金又は未払役員賞与がある場合には、次の欄にその内訳を記入してください。',
		),
		'analyze' => array(
			'strTitle' => '買掛金(未払金・未払費用)の内訳書',

			'strTitleOld' => '【残高分析】',
			'strExplainOld' => 'データ参照先として設定した補助科目の期末帳簿残高と内訳書の期末現在高にズレが生じているものが表示されます。',
			'strNoneOld' => '内訳書の期末現在高は最新のようです。',
			'flagDone' => 0,
			'strPast' => '<span class="codeLibBaseFontOrange" style="float:none;">※ データが確定しているため分析できません。</span>',

			'strPageOld' => '頁',
			'strRowOld' => '行',
			'strValueUpdateOld' => '期末帳簿残高',
			'strValueSaveOld' => '内訳書期末現在高',
			'arrRowsOld' => array(),
			'flagOld' => 0,

			'strTitleLost' => '【記載漏れ分析】',
			'strExplainLost' => 'データ参照先として設定した補助科目の期末帳簿残高がゼロでないもので、かつ内訳書に記載していないものが表示されます。',
			'strNoneLost' => '内訳書に記載漏れはないようです。',
			'strPageLost' => '頁',
			'strRowLost' => '行',
			'strValueLost' => '期末現在高',
			'arrRowsLost' => array(),
			'flagLost' => 0,

			'strIdAccountTitleLost' => '勘定科目',
			'strIdSubAccountTitleLost' => '補助科目',
		),
	),
	'varsFlagCalcBtn' => array(
		'detail' => 1,
	),


);
