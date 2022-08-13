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
			'varsTextTarget' => array(
				'flagMustUse' => 0,
				'id' => 'TextTarget',
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
			'numRows' => 10,
			'strSub0' => '補助科目未指定残高',
			'strBlank' => '空欄',

			'strSync' => 'データ参照先',

			'strAccountTitle' => '科目',
			'strAddress' => '所在地(住所)',
			'strTarget' => '相手先',
			'strValue' => '金額',
			'strMemo' => '取引の内容',
			'strIdAccountTitle' => '勘定科目',
			'strIdSubAccountTitle' => '補助科目',

			'strTitle' => '雑損失等の内訳書',
			'strTitleExt' => '雑益、雑損失等の内訳書',


			'strColumn' => '雑　損　失　等',
			'strTitleSub' => '',
			'strEntityExt' => '',
			'strNumExt' => '',
			'strPageExt' => '',
			'strTitleNum' => '⑯',
			'strBlank' => '　',

			'strSum' => '計',
			'strUnit' => '(単位：円)',
			'strSeparate' => '/',
			'strCautionLaw' => '(法0302－17)',
			'strCautionMark' => '(注)',
			'strCaution1' => '１．雑収入、雑益(損失)、固定資産売却益(損)、税金の還付金、貸倒損失等について記入してください。',
			'strCaution2' => '２．科目別かつ相手先別の金額が10 万円以上のものについて記入してください。ただし、税金の還付金については、その金額が10 万円未満であってもすべて記入してください。',
		),
		'analyze' => array(
			'strTitle' => '雑損失等の内訳書',

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
