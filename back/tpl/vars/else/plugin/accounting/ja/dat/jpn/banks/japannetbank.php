<?php

$vars = array(
	'strTitle' => 'ジャパンネット銀行',
	'flagCsv' => 1,
	'strDelimiter' => ',',
	'strFileType' => 'csv',
	'varsCsv' => array(
		'strYear' => '操作日(年)',
		'strMonth' => '操作日(月)',
		'strDate' => '操作日(日)',
		'strHour' => '操作時刻(時)',
		'strMin' => '操作時刻(分)',
		'strSec' => '操作時刻(秒)',
		'strNum' => '取引順番号',
		'strTitle' => '摘要',
		'numValueOut' => 'お支払金額',
		'numValueIn' => 'お預り金額',
		'numBalance' => '残高',
	),
	'varsDetail' => array(
		array(
			'flagMustUse' => 1,
			'id' => 'IdNumBranch',
			'strTitle' => '店番号（3桁）' , 'strExplain' => '' ,
			'value' => '',
			'flagErrorNow' => 0,
			'arrayError' => array(
				array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
				array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字で記入する必要があるようです。',  ), ),
				array( 'flagCheck' => 'min', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'str', 'num' => 3, 'strComment' => array( 'common' => '入力文字数が少なすぎるようです。', ),),
			),
			'flagContentUse' => 0,
			'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
			'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 3,
			'numWidth' => 0, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => '',
			'arrayOption' => array(),
			'flagForm' => 'active',
			'flagSecret' => 0,
			'flagFirst' => 1,
		),
		array(
			'flagMustUse' => 1,
			'id' => 'IdNumAccount',
			'strTitle' => '口座番号（7桁）' , 'strExplain' => '' ,
			'value' => '',
			'flagErrorNow' => 0,
			'arrayError' => array(
				array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
				array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字で記入する必要があるようです。',  ), ),
				array( 'flagCheck' => 'min', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'str', 'num' => 7, 'strComment' => array( 'common' => '入力文字数が少なすぎるようです。', ),),
			),
			'flagContentUse' => 0,
			'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
			'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 7,
			'numWidth' => 0, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => '',
			'arrayOption' => array(),
			'flagForm' => 'active',
			'flagSecret' => 0,
			'flagFirst' => 1,
		),
		array(
			'flagMustUse' => 1,
			'id' => 'StrPassword',
			'strTitle' => 'ログインパスワード' , 'strExplain' => '' ,
			'value' => '',
			'flagErrorNow' => 0,
			'arrayError' => array(
				array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
			),
			'flagContentUse' => 0,
			'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
			'flagTag' => 'input', 'flagInputType' => 'password', 'numMaxlength' => 100,
			'numWidth' => 0, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => '',
			'arrayOption' => array(),
			'flagForm' => '',
			'varsForm' => array(
				'First' => 'active',
				'Second' => 'active',
			),
			'flagForm' => 'active',
			'flagSecret' => 1,
			'flagFirst' => 1,
		),
	),
	'tplTable' => '
<table id="#{idSelf}TableWrap" cellspacing="1" cellpadding="3" border="0" bgcolor="#cccccc" width="100%">
	<tbody>
		<tr>
			<td class="codeLibBaseTableColumnMiddle" colspan=2>ジャパンネット銀行</td>
		</tr>
		<tr>
			<td class="codeLibBaseTableRow" colspan=2>
			<p class="codeLibBaseLinkNormalOver">・事前に<a href="https://login.japannetbank.co.jp/login_L.html" rel=noreferrer target="_blank">ジャパンネット銀行</a>でログインができるか確認してください。</p>
			<p>・ログインパスワードは、設定の都度入力をしてください。</p>
		</tr>
		<tr id="#{idSelf}IdNumBranchWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}IdNumBranchStrTitle" style="width:150px;">店番号（3桁）</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}IdNumBranchOffset"><div id="#{idSelf}IdNumBranch" style="text-align:right;"></div></td>
		</tr>
		<tr id="#{idSelf}IdNumAccountWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}IdNumAccountStrTitle" style="width:150px;">口座番号（7桁）</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}IdNumAccountOffset"><div id="#{idSelf}IdNumAccount" style="text-align:right;"></div></td>
		</tr>
		<tr id="#{idSelf}StrPasswordWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}StrPasswordStrTitle">ログインパスワード</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}StrPasswordOffset"><div id="#{idSelf}StrPassword" style="text-align:right;"></div></td>
		</tr>
	</tbody>
</table>
	',
	'tplTableView' => '
<table cellspacing="1" cellpadding="3" border="0" bgcolor="#cccccc" width="100%">
	<tbody>
		<tr>
			<td class="codeLibBaseTableColumnMiddle codeLibBaseLinkNormalOver" colspan=2><a href="https://login.japannetbank.co.jp/login_L.html" rel=noreferrer target="_blank">金融機関ログイン画面</a></td>
		</tr>
		<tr id="#{idSelf}IdNumBranchWrap">
			<td class="codeLibBaseTableRow" style="width:150px;">店番号（3桁）</td>
			<td class="codeLibBaseTableRow" ><div style="text-align:right;">#{IdNumBranch}</div></td>
		</tr>
		<tr id="#{idSelf}IdNumAccountWrap">
			<td class="codeLibBaseTableRow">口座番号（7桁）</td>
			<td class="codeLibBaseTableRow" ><div style="text-align:right;">#{IdNumAccount}</div></td>
		</tr>
		<tr id="#{idSelf}StrPasswordWrap">
			<td class="codeLibBaseTableRow">ログインパスワード</td>
			<td class="codeLibBaseTableRow"><div style="text-align:right;">非表示</div></td>
		</tr>
	</tbody>
</table>
	',
);