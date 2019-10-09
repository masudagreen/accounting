<?php

$vars = array(
	'arrSelectTag' => array(
		'detail' => array(

		),
	),
	'varsTmpl' => array(
		'detail' => array(
			'varsTextBank' => array(
				'flagMustUse' => 0,
				'id' => 'TextBank',
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
			'varsTextBranch' => array(
				'flagMustUse' => 0,
				'id' => 'TextBranch',
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
			'varsTextType' => array(
				'flagMustUse' => 0,
				'id' => 'TextType',
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
			'varsTextAccount' => array(
				'flagMustUse' => 0,
				'id' => 'TextAccount',
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
			'flagDebit' => 1,
			'numRows' => 23,
			'strSub0' => '補助科目未指定残高',
			'strBlank' => '空欄',
			'strSync' => 'データ参照先',
			'strBank' => '金融機関名',
			'strBranch' => '支店名',
			'strType' => '種類',
			'strAccount' => '口座番号',
			'strValue' => '期末現在高',
			'strMemo' => '摘要',
			'strIdAccountTitle' => '勘定科目',
			'strIdSubAccountTitle' => '補助科目',
			'strSum' => '計',
			'strUnit' => '(単位：円)',
			'strUnitBank' => '(金融機関名/支店名等)',
			'strSeparate' => '/',

			'strTitle' => '預貯金等の内訳書',
			'strTitleSub' => '',
			'strEntityExt' => '',
			'strNumExt' => '',
			'strPageExt' => '',
			'strTitleNum' => '①',
			'strBlank' => '　',

			'strCautionLaw' => '(法0302－1)',
			'strCautionMark' => '(注)',
			'strCaution1' => '１．取引金融機関別に、かつ、預貯金の種類別に記入してください。',
			'strCaution2' => '２．「金融機関名」欄には、斜線の左側に金融機関名を、右側にその支店等の名称を、例えば○○銀行大手町支店の場合には、「○○／大手町」のように記入してください。',
			'strCaution3' => '３．預貯金等の名義人が代表者になっているなど法人名と異なる場合には、「摘要」欄に「名義人○○○○」のようにその名義人を記入してください。',
		),
		'analyze' => array(
			'strTitle' => '預貯金等の内訳書',

			'strTitleOld' => '【残高分析】',
			'strExplainOld' => 'データ参照先として設定した補助科目の期末帳簿残高と内訳書の期末現在高にズレが生じているものが表示されます。',
			'strNoneOld' => '内訳書の期末現在高は最新のようです。',
			'strPageOld' => '頁',
			'strRowOld' => '行',
			'strValueUpdateOld' => '期末帳簿残高',
			'strValueSaveOld' => '内訳書期末現在高',
			'arrRowsOld' => array(),
			'flagOld' => 0,
			'flagDone' => 0,
			'strPast' => '<span class="codeLibBaseFontOrange" style="float:none;">※ データが確定しているため分析できません。</span>',

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
