<?php

$vars = array(
	'arrSelectTag' => array(
		'detail' => array(

		),
	),
	'varsTmpl' => array(
		'detail' => array(
			'varsTextDrawer' => array(
				'flagMustUse' => 0,
				'id' => 'TextDrawer',
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
			'varsTextDrawerYear' => array(
				'flagMustUse' => 0,
				'id' => 'TextDrawerYear',
				'strTitle' => '',
				'value' => '', 'valueStr' => '', 'flagValueType' => 'str',
				'arrayError' => array(
					array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ),),
					array( 'flagCheck' => 'max', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'str', 'num' => 4, 'strComment' => array( 'common' => '入力文字数が多すぎるようです。', ),),
				),
				'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 4,
				'numWidth' => 40, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => 'px',
				'arrayOption' => array(),
				'flagForm' => 'active',
				'flagLoop' => 1,
				'flagCsv' => 1,
			),
			'varsTextDrawerMonth' => array(
				'flagMustUse' => 0,
				'id' => 'TextDrawerMonth',
				'strTitle' => '',
				'value' => '', 'valueStr' => '', 'flagValueType' => 'str',
				'arrayError' => array(
					array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ),),
					array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字以外の文字が混入したようです。', ), ),
					array( 'flagCheck' => 'max', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'num', 'num' => 12, 'strComment' => array( 'common' => '最大値を上回ってしまうようです。', ),),
					array( 'flagCheck' => 'min', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'num', 'num' => 1, 'strComment' => array( 'common' => '最小値を下回ってしまうようです。', ),),
				),
				'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 2,
				'numWidth' => 40, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => 'px',
				'arrayOption' => array(),
				'flagForm' => 'active',
				'flagLoop' => 1,
			),
			'varsTextDrawerDate' => array(
				'flagMustUse' => 0,
				'id' => 'TextDrawerDate',
				'strTitle' => '',
				'value' => '', 'valueStr' => '', 'flagValueType' => 'str',
				'arrayError' => array(
					array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ),),
					array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字以外の文字が混入したようです。', ), ),
					array( 'flagCheck' => 'max', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'num', 'num' => 31, 'strComment' => array( 'common' => '最大値を上回ってしまうようです。', ),),
					array( 'flagCheck' => 'min', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'num', 'num' => 1, 'strComment' => array( 'common' => '最小値を下回ってしまうようです。', ),),
				),
				'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 2,
				'numWidth' => 40, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => 'px',
				'arrayOption' => array(),
				'flagForm' => 'active',
				'flagLoop' => 1,
			),
			'varsTextLimitYear' => array(
				'flagMustUse' => 0,
				'id' => 'TextLimitYear',
				'strTitle' => '',
				'value' => '', 'valueStr' => '', 'flagValueType' => 'str',
				'arrayError' => array(
					array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ),),
					array( 'flagCheck' => 'max', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'str', 'num' => 4, 'strComment' => array( 'common' => '入力文字数が多すぎるようです。', ),),
				),
				'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 4,
				'numWidth' => 40, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => 'px',
				'arrayOption' => array(),
				'flagForm' => 'active',
				'flagLoop' => 1,
				'flagCsv' => 1,
			),
			'varsTextLimitMonth' => array(
				'flagMustUse' => 0,
				'id' => 'TextLimitMonth',
				'strTitle' => '',
				'value' => '', 'valueStr' => '', 'flagValueType' => 'str',
				'arrayError' => array(
					array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ),),
					array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字以外の文字が混入したようです。', ), ),
					array( 'flagCheck' => 'max', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'num', 'num' => 12, 'strComment' => array( 'common' => '最大値を上回ってしまうようです。', ),),
					array( 'flagCheck' => 'min', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'num', 'num' => 1, 'strComment' => array( 'common' => '最小値を下回ってしまうようです。', ),),
				),
				'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 2,
				'numWidth' => 40, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => 'px',
				'arrayOption' => array(),
				'flagForm' => 'active',
				'flagLoop' => 1,
			),
			'varsTextLimitDate' => array(
				'flagMustUse' => 0,
				'id' => 'TextLimitDate',
				'strTitle' => '',
				'value' => '', 'valueStr' => '', 'flagValueType' => 'str',
				'arrayError' => array(
					array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ),),
					array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字以外の文字が混入したようです。', ), ),
					array( 'flagCheck' => 'max', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'num', 'num' => 31, 'strComment' => array( 'common' => '最大値を上回ってしまうようです。', ),),
					array( 'flagCheck' => 'min', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'num', 'num' => 1, 'strComment' => array( 'common' => '最小値を下回ってしまうようです。', ),),
				),
				'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 2,
				'numWidth' => 40, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => 'px',
				'arrayOption' => array(),
				'flagForm' => 'active',
				'flagLoop' => 1,
			),
			'varsTextBankPay' => array(
				'flagMustUse' => 0,
				'id' => 'TextBankPay',
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
			'varsTextBranchPay' => array(
				'flagMustUse' => 0,
				'id' => 'TextBranchPay',
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
			'varsTextBankDiscount' => array(
				'flagMustUse' => 0,
				'id' => 'TextBankDiscount',
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
			'varsTextBranchDiscount' => array(
				'flagMustUse' => 0,
				'id' => 'TextBranchDiscount',
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
			'flagDebit' => 1,
			'numRows' => 23,
			'strSub0' => '補助科目未指定残高',
			'strBlank' => '空欄',

			'strSync' => 'データ参照先',

			'strDrawer' => '振出人',
			'strDrawerYear' => '振出年月日',
			'strLimitYear' => '支払期日',

			'strBankPay' => '支払銀行名',
			'strBankDiscount' => '割引銀行名等',

			'strValue' => '金額',
			'strMemo' => '摘要',

			'strIdAccountTitle' => '勘定科目',
			'strIdSubAccountTitle' => '補助科目',
			'strSum' => '計',
			'strUnit' => '(単位：円)',
			'strUnitTime' => '(年・月・日)',
			'strSeparate' => '/',

			'strTitle' => '受取手形の内訳書',
			'strTitleSub' => '',
			'strEntityExt' => '',
			'strNumExt' => '',
			'strPageExt' => '',
			'strTitleNum' => '②',
			'strBlank' => '　',

			'strCautionLaw' => '(法0302－2)',
			'strCautionMark' => '(注)',
			'strCaution1' => '１．一取引先からの受取手形の総額が100万円以上のもの(100万円以上のものが5口未満のときは期末現在高の多額のものから5 口程度)については各別に記入し、その他は一括して記入してください。なお、一括して記入するもののうち、割引したものについては割引銀行ごとに区分して記入してください。',
			'strCaution2' => '２．融通手形については、各別に記入し摘要欄にその旨を記入してください。',
			'strCaution3' => '３．為替手形の場合は、引受人の氏名及び住所を摘要欄に記入してください。',
			'strCaution4' => '４．差出人と債務者とが異なる場合には、その債務者の氏名及び住所を摘要欄に記入してください。',
			'strCaution5' => '５．「支払銀行名」欄には、「○○／大手町」のように簡記してください。',
			'strCaution6' => '６.「割引銀行名等」欄には、割引銀行名又は裏書譲渡先名を記入してください。',
		),
		'analyze' => array(
			'strTitle' => '受取手形の内訳書',

			'strTitleOld' => '【金額分析】',
			'strExplainOld' => 'データ参照先として設定した補助科目の帳簿金額と内訳書の金額にズレが生じているものが表示されます。',
			'strNoneOld' => '内訳書の金額は最新のようです。',
			'strPageOld' => '頁',
			'strRowOld' => '行',
			'strValueUpdateOld' => '帳簿金額',
			'strValueSaveOld' => '内訳書金額',
			'arrRowsOld' => array(),
			'flagOld' => 0,
			'flagDone' => 0,
			'strPast' => '<span class="codeLibBaseFontOrange" style="float:none;">※ データが確定しているため分析できません。</span>',

			'strTitleLost' => '【記載漏れ分析】',
			'strExplainLost' => 'データ参照先として設定した補助科目の帳簿金額がゼロでないもので、かつ内訳書に記載していないものが表示されます。',
			'strNoneLost' => '内訳書に記載漏れはないようです。',
			'strPageLost' => '頁',
			'strRowLost' => '行',
			'strValueLost' => '金額',
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
