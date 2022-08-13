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
			'varsTextNum' => array(
				'flagMustUse' => 0,
				'id' => 'TextNum',
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
			'varsTextUnit' => array(
				'flagMustUse' => 0,
				'id' => 'TextUnit',
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
			'varsSelectMethod' => array(
				'flagMustUse' => 0,
				'id' => 'SelectMethod',
				'strTitle' => '',
				'value' => 0, 'valueStr' => '未選択', 'flagValueType' => 'select',
				'arrayError' => array(
					array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ),),
				),
				'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
				'numWidth' => 40, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => 'px',
				'arrayOption' => array(
					array( 'strTitle' => '未選択', 'value' => 0, ),
					array( 'strTitle' => 'Ａ 実地棚卸', 'value' => 1, ),
					array( 'strTitle' => 'Ｂ 帳簿棚卸', 'value' => 2, ),
					array( 'strTitle' => 'Ｃ ＡとＢとの併用', 'value' => 3, ),
				),
				'flagForm' => 'active',
			),
			'varsTextYear' => array(
				'flagMustUse' => 0,
				'id' => 'TextYear',
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
			),
			'varsTextMonth' => array(
				'flagMustUse' => 0,
				'id' => 'TextMonth',
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
			),
			'varsTextDate' => array(
				'flagMustUse' => 0,
				'id' => 'TextDate',
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
			),
		),
	),
	'varsStr' => array(
		'detail' => array(
			'arrRows' => array(),
			'flagDebit' => 1,
			'numRows' => 24,
			'strSub0' => '補助科目未指定残高',
			'strBlank' => '空欄',

			'strSync' => 'データ参照先',

			'strAccountTitle' => '科目',
			'strType' => '品目',
			'strNum' => '数量',
			'strUnitTitle' => '単価',
			'strValue' => '期末現在高',
			'strMemo' => '摘要',

			'strYear' => '年',
			'strMonth' => '月',
			'strDate' => '日',
			'strTime' => '棚卸を行った時期',
			'strMethod' => '期末棚卸の方法',

			'strTitle' => '棚卸資産(商品又は製品、半製品、仕掛品、原材料、貯蔵品)の内訳書',
			'strTitleSub' => '',
			'strEntityExt' => '',
			'strNumExt' => '',
			'strPageExt' => '',
			'strTitleNum' => '⑤',
			'strBlank' => '　',

			'strSelectA' => '実地棚卸',
			'strSelectB' => '帳簿棚卸',
			'strSelectC' => 'ＡとＢとの併用',
			'strSelect' => '<span class="codeLibPrintSelect"><%replace%></span>',
			'strA' => 'Ａ',
			'strB' => 'Ｂ',
			'strC' => 'Ｃ',

			'strIdAccountTitle' => '勘定科目',
			'strIdSubAccountTitle' => '補助科目',
			'strSum' => '計',
			'strUnit' => '(単位：円)',
			'strSeparate' => '/',
			'strCautionLaw' => '(法0302－5)',
			'strCautionMark' => '(注)',
			'strCaution1' => '１．期末棚卸の方法を次の欄に記入してください。',
			'strCaution2' => '２．「科目」欄には、商品又は製品、半製品、仕掛品(半成工事を含みます。)、原材料、貯蔵品、作業くず、副産物等のように記入してください。',
			'strCaution3' => '３．「品目」欄には、例えば「紳士用皮靴」のように記入し、それ以上細分して記入しなくても差し支えありません。',
			'strCaution4' => '４．評価換えを行った場合には、「摘要」欄に「評価損○○○円」のようにその評価増減額を記入してください。',
		),
		'analyze' => array(
			'strTitle' => '棚卸資産(商品又は製品、半製品、仕掛品、原材料、貯蔵品)の内訳書',

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
