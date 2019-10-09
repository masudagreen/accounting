<?php

$vars = array(
	'strTitle' => 'ゆうちょ銀行',
	'flagCsv' => 0,
	'strDelimiter' => '',
	'strFileType' => '',
	'varsCsv' => array(
		'stampBook' => '日付',
		'numValueIn' => 'お預り金額',
		'numValueOut' => 'お支払金額',
		'strTitle' => 'お預り／お支払内容',
		'numBalance' => '現在（貸付）高',
	),
	'flagSignBtn' => 1,
	'varsDetail' => array(
		array(
			'flagMustUse' => 1,
			'id' => 'IdNumAccount1',
			'strTitle' => 'お客さま番号（４桁 左）' , 'strExplain' => '' ,
			'value' => '',
			'flagErrorNow' => 0,
			'arrayError' => array(
				array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
				array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字で記入する必要があるようです。',  ), ),
				array( 'flagCheck' => 'min', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'str', 'num' => 4, 'strComment' => array( 'common' => '入力文字数が少なすぎるようです。', ),),
			),
			'flagContentUse' => 0,
			'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
			'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 4,
			'numWidth' => 0, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => '',
			'arrayOption' => array(),
			'flagForm' => 'active',
			'flagSecret' => 0,
		),
		array(
			'flagMustUse' => 1,
			'id' => 'IdNumAccount2',
			'strTitle' => 'お客さま番号（４桁 中央）' , 'strExplain' => '' ,
			'value' => '',
			'flagErrorNow' => 0,
			'arrayError' => array(
				array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
				array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字で記入する必要があるようです。',  ), ),
				array( 'flagCheck' => 'min', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'str', 'num' => 4, 'strComment' => array( 'common' => '入力文字数が少なすぎるようです。', ),),
			),
			'flagContentUse' => 0,
			'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
			'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 4,
			'numWidth' => 0, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => '',
			'arrayOption' => array(),
			'flagForm' => 'active',
			'flagSecret' => 0,
		),
		array(
			'flagMustUse' => 1,
			'id' => 'IdNumAccount3',
			'strTitle' => 'お客さま番号（５桁 右）' , 'strExplain' => '' ,
			'value' => '',
			'flagErrorNow' => 0,
			'arrayError' => array(
				array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
				array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字で記入する必要があるようです。',  ), ),
				array( 'flagCheck' => 'min', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'str', 'num' => 5, 'strComment' => array( 'common' => '入力文字数が少なすぎるようです。', ),),
			),
			'flagContentUse' => 0,
			'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
			'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 5,
			'numWidth' => 0, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => '',
			'arrayOption' => array(),
			'flagForm' => 'active',
			'flagSecret' => 0,
		),
		array(
			'flagMustUse' => 1,
			'id' => 'StrPassword',
			'strTitle' => 'ログインパスワード' , 'strExplain' => '' ,
			'value' => '',
			'flagErrorNow' => 0,
			'arrayError' => array(
				array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
				array( 'flagCheck' => 'min', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'str', 'num' => 6, 'strComment' => array( 'common' => '入力文字数が少なすぎるようです。', ),),
			),
			'flagContentUse' => 0,
			'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
			'flagTag' => 'input', 'flagInputType' => 'password', 'numMaxlength' => 12,
			'numWidth' => 0, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => '',
			'arrayOption' => array(),
			'flagForm' => 'active',
			'flagSecret' => 1,
		),
		array(
			'flagMustUse' => 0,
			'id' => 'StrSignQuestion1',
			'strTitle' => '質問1',
			'strExplain' => '',
			'value' => '',
			'flagErrorNow' => 0,
			'arrayError' => array(
				array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
			),
			'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
			'numWidth' => 40, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => 'px',
			'arrayOption' => array(
				array( 'strTitle' => '未選択', 'value' => '',),
			),
			'flagForm' => 'active',
			'flagSecret' => 0,
			'flagSignBtn' => 1,
			'varsTmpl' => array(
				'varsNone' => array( 'strTitle' => '未選択', 'value' => '',),
			),
		),
		array(
			'flagMustUse' => 0,
			'id' => 'StrSignQuestion2',
			'strTitle' => '質問2',
			'strExplain' => '',
			'value' => '',
			'flagErrorNow' => 0,
			'arrayError' => array(
				array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
			),
			'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
			'numWidth' => 40, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => 'px',
			'arrayOption' => array(
				array( 'strTitle' => '未選択', 'value' => '',),
			),
			'flagForm' => 'active',
			'flagSecret' => 0,
			'flagSignBtn' => 1,
			'varsTmpl' => array(
				'varsNone' => array( 'strTitle' => '未選択', 'value' => '',),
			),
		),
		array(
			'flagMustUse' => 0,
			'id' => 'StrSignQuestion3',
			'strTitle' => '質問3',
			'strExplain' => '',
			'value' => '',
			'flagErrorNow' => 0,
			'arrayError' => array(
				array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
			),
			'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
			'numWidth' => 40, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => 'px',
			'arrayOption' => array(
				array( 'strTitle' => '未選択', 'value' => '',),
			),
			'flagForm' => 'active',
			'flagSecret' => 0,
			'flagSignBtn' => 1,
			'varsTmpl' => array(
				'varsNone' => array( 'strTitle' => '未選択', 'value' => '',),
			),
		),
		array(
			'flagMustUse' => 0,
			'id' => 'StrSignAnswer1',
			'strTitle' => '合言葉1' , 'strExplain' => '' ,
			'value' => '',
			'flagErrorNow' => 0,
			'arrayError' => array(
				array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
			),
			'flagContentUse' => 0,
			'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
			'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 10,
			'numWidth' => 0, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => '',
			'arrayOption' => array(),
			'flagForm' => 'active',
			'flagSecret' => 1,
			'flagSignBtn' => 1,
		),
		array(
			'flagMustUse' => 0,
			'id' => 'StrSignAnswer2',
			'strTitle' => '合言葉2',
			'strExplain' => '',
			'value' => '',
			'flagErrorNow' => 0,
			'arrayError' => array(
				array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
			),
			'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
			'numWidth' => 40, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => 'px',
			'arrayOption' => array(),
			'flagForm' => 'active',
			'flagSecret' => 1,
			'flagSignBtn' => 1,
		),
		array(
			'flagMustUse' => 0,
			'id' => 'StrSignAnswer3',
			'strTitle' => '合言葉3',
			'strExplain' => '',
			'value' => '',
			'flagErrorNow' => 0,
			'arrayError' => array(
				array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
			),
			'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
			'numWidth' => 40, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => 'px',
			'arrayOption' => array(),
			'flagForm' => 'active',
			'flagSecret' => 1,
			'flagSignBtn' => 1,
		),
	),
	'tplTable' => '
<table id="#{idSelf}TableWrap" cellspacing="1" cellpadding="3" border="0" bgcolor="#cccccc" width="100%">
	<tbody>
		<tr>
			<td class="codeLibBaseTableColumnMiddle" colspan=2>ゆうちょ銀行</td>
		</tr>
		<tr>
			<td class="codeLibBaseTableRow" colspan=2>
			<p class="codeLibBaseLinkNormalOver">・事前に<a href="https://direct.jp-bank.japanpost.jp/tp1web/U010101SCK.do" rel=noreferrer target="_blank">ゆうちょ銀行</a>でログインができるか確認してください。</p>
			<p>・事前にログインにトークンを使っていないことを確認してください。</p>
			<p>・ログインパスワードは、設定の都度入力をしてください。</p>
		</tr>
		<tr id="#{idSelf}IdNumAccount1Wrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}IdNumAccount1StrTitle" style="width:150px;">お客さま番号（４桁 左）</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}IdNumAccount1Offset"><div id="#{idSelf}IdNumAccount1" style="text-align:right;"></div></td>
		</tr>
		<tr id="#{idSelf}IdNumAccount2Wrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}IdNumAccount2StrTitle">お客さま番号（４桁 中央）</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}IdNumAccount2Offset"><div id="#{idSelf}IdNumAccount2" style="text-align:right;"></div></td>
		</tr>
		<tr id="#{idSelf}IdNumAccount3Wrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}IdNumAccount3StrTitle">お客さま番号（５桁 右）</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}IdNumAccount3Offset"><div id="#{idSelf}IdNumAccount3" style="text-align:right;"></div></td>
		</tr>
		<tr id="#{idSelf}StrPasswordWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}StrPasswordStrTitle">ログインパスワード</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}StrPasswordOffset"><div id="#{idSelf}StrPassword" style="text-align:right;"></div></td>
		</tr>
		<tr>
			<td class="codeLibBaseTableRow" colspan=2>
			<p>・3つの質問に対する合言葉を入力してください。</p>
			<p>・選択肢の中に該当する質問がない場合は、「質問挿入」ボタンを押してください。</p>
		</tr>
		<tr>
			<td class="codeLibBaseTableRow" >質問1</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}StrSignQuestion1Offset"><div id="#{idSelf}StrSignQuestion1"></div></td>
		</tr>
		<tr>
			<td class="codeLibBaseTableRow" >合言葉1</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}StrSignAnswer1Offset"><div id="#{idSelf}StrSignAnswer1"></div></td>
		</tr>
		<tr>
			<td class="codeLibBaseTableRow" >質問2</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}StrSignQuestion2Offset"><div id="#{idSelf}StrSignQuestion2"></div></td>
		</tr>
		<tr>
			<td class="codeLibBaseTableRow" >合言葉2</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}StrSignAnswer2Offset"><div id="#{idSelf}StrSignAnswer2"></div></td>
		</tr>
		<tr>
			<td class="codeLibBaseTableRow" >質問3</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}StrSignQuestion3Offset"><div id="#{idSelf}StrSignQuestion3"></div></td>
		</tr>
		<tr>
			<td class="codeLibBaseTableRow" >合言葉3</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}StrSignAnswer3Offset"><div id="#{idSelf}StrSignAnswer3"></div></td>
		</tr>
	</tbody>
</table>
	',
	'tplTableView' => '
<table cellspacing="1" cellpadding="3" border="0" bgcolor="#cccccc" width="100%">
	<tbody>
		<tr>
			<td class="codeLibBaseTableColumnMiddle codeLibBaseLinkNormalOver" colspan=2><a href="https://direct.jp-bank.japanpost.jp/tp1web/U010101SCK.do" rel=noreferrer target="_blank">金融機関ログイン画面</a></td>
		</tr>
		<tr id="#{idSelf}IdNumAccountWrap">
			<td class="codeLibBaseTableRow" style="width:150px;">お客さま番号</td>
			<td class="codeLibBaseTableRow" ><div style="text-align:right;">#{IdNumAccount}</div></td>
		</tr>
		<tr id="#{idSelf}StrPasswordWrap">
			<td class="codeLibBaseTableRow">ログインパスワード</td>
			<td class="codeLibBaseTableRow"><div style="text-align:right;">非表示</div></td>
		</tr>
		<tr>
			<td class="codeLibBaseTableRow"><div>質問1：#{StrSignQuestion1}</div></td>
			<td class="codeLibBaseTableRow"><div>合言葉1：非表示</div></td>
		</tr>
		<tr>
			<td class="codeLibBaseTableRow"><div>質問2：#{StrSignQuestion2}</div></td>
			<td class="codeLibBaseTableRow"><div>合言葉2：非表示</div></td>
		</tr>
		<tr>
			<td class="codeLibBaseTableRow"><div>質問3：#{StrSignQuestion3}</div></td>
			<td class="codeLibBaseTableRow"><div>合言葉3：非表示</div></td>
		</tr>
	</tbody>
</table>
	',
);