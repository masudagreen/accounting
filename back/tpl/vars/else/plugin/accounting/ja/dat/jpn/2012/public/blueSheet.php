<?php

$vars = array(
	'arrSelectTag' => array(
		'0' => array(
			array(
				'flagMustUse' => 0,
				'id' => 'Nozeisha_id',
				'strTitle' => '利用者識別番号',
				'value' => '', 'valueStr' => '', 'flagValueType' => 'str',
				'arrayError' => array(
					array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ),),
					array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字で記入する必要があるようです。',  ), ),
					array( 'flagCheck' => 'min', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'str', 'flagArr' => 0, 'num' => 16, 'strComment' => array('common' => '入力文字数が足りていないようです。', ), ),
					array( 'flagCheck' => 'max', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'str', 'num' => 16, 'strComment' => array( 'common' => '入力文字数が多すぎるようです。', ),),
				),
				'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 16,
				'numWidth' => 40, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => 'px',
				'arrayOption' => array(),
				'flagForm' => 'active',
			),
			array(
				'flagMustUse' => 0,
				'id' => 'SelectTypeZeimusho',
				'strTitle' => '提出先税務署',
				'value' => 	'01101', 'valueStr' => '', 'flagValueType' => 'select',
				'arrayError' => array(
					array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ),),
				),
				'flagTag' => 'selectShortCut', 'flagInputType' => '', 'numMaxlength' => 0,
				'numWidth' => 90, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
				'arrayOption' => array(
					array('strTitle' => '未選択', 'value' => ''),
				),
				'varsFormTemp' => array(
					'varsStatus' => array(
						'numLeft' => 0,
						'numTop' => 0,
					),
					'varsDetail' => array(
						'flagTag'       => '',
						'flagInputType' => '',
						'numMaxlength'  => 100,
						'numWidth'      => 0,
						'unitWidth'     => 'px',
						'numHeight'     => 0,
						'unitHeight'    => 'px',
						'arrayOption'   => array(),
						'value'         => '',
						'vars'          => array(),
					),
				),
				'flagForm' => 'active',
			),
			array(
				'flagMustUse' => 0,
				'id' => 'SelectTypeKoujo',
				'strTitle' => '青色申告特別控除額',
				'value' => 	1, 'valueStr' => '65万円', 'flagValueType' => 'select',
				'arrayError' => array(
					array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入が必要なようです。', ),),
				),
				'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
				'numWidth' => 40, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => 'px',
				'arrayOption' => array(
					array( 'strTitle' => '10万円', 'value' => 0, ),
					array( 'strTitle' => '65万円', 'value' => 1, ),
				),
				'flagForm' => 'active',
			),
		),
	),
	'varsStr' => array(
		'0' => array(
			'strTitle' => 'e-TAXソフト取り込み必須項目',
			'strSpace' => '　',
			'strCaution' => '※<a href="javascript:void(window.open(\'http://www.e-tax.nta.go.jp/download/e-taxSoftDownLoad.htm\'));" rel="noreferrer">e-TAXソフト</a>に組み込むには以下の項目を設定する必要があります。<br>※令和元年分の青色申告決算書より「月別売上（収入）金額及び仕入金額」の下に「うち軽減税率対象」が新設されました。ただ入力は任意で省略可能なため自動入力を見送っております。',
		),

	),

);
