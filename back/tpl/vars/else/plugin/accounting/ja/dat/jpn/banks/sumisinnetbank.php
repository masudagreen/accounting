<?php

$vars = array(
	'strTitle' => '住信SBIネット銀行',
	'flagCsv' => 1,
	'strDelimiter' => ',',
	'strFileType' => 'csv',
	'varsCsv' => array(
		'stampBook' => '日付',
		'strTitle' => '内容',
		'numValueOut' => '出金金額(円)',
		'numValueIn' => '入金金額(円)',
		'numBalance' => '残高(円)',
		'strMemo' => 'メモ',
	),
	'varsDetail' => array(
		array(
			'flagMustUse' => 1,
			'id' => 'IdLogin',
			'strTitle' => 'ユーザネーム' , 'strExplain' => '' ,
			'value' => '',
			'flagErrorNow' => 0,
			'arrayError' => array(
				array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
			),
			'flagContentUse' => 0,
			'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
			'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 100,
			'numWidth' => 0, 'unitWidth' => 'px', 'numHeight' => 0, 'unitHeight' => '',
			'arrayOption' => array(),
			'flagForm' => 'active',
			'flagSecret' => 0,
			'flagFirst' => 1,
		),
		array(
			'flagMustUse' => 1,
			'id' => 'StrPassword',
			'strTitle' => 'WEBログインパスワード' , 'strExplain' => '' ,
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
			<td class="codeLibBaseTableColumnMiddle" colspan=2>住信SBIネット銀行</td>
		</tr>
		<tr>
			<td class="codeLibBaseTableRow" colspan=2>
			<p class="codeLibBaseLinkNormalOver">・事前に<a href="https://www.netbk.co.jp/wpl/NBGate" rel=noreferrer target="_blank">住信SBIネット銀行</a>でログインができるか確認してください。</p>
			<p>・WEBログインパスワードは、その都度入力をしてください。</p>
		</tr>
		<tr id="#{idSelf}IdLoginWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}IdLoginStrTitle" style="width:150px;">ユーザネーム</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}IdLoginOffset"><div id="#{idSelf}IdLogin" style="text-align:right;"></div></td>
		</tr>
		<tr id="#{idSelf}StrPasswordWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}StrPasswordStrTitle">WEBログインパスワード</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}StrPasswordOffset"><div id="#{idSelf}StrPassword" style="text-align:right;"></div></td>
		</tr>
	</tbody>
</table>
	',
	'tplTableView' => '
<table cellspacing="1" cellpadding="3" border="0" bgcolor="#cccccc" width="100%">
	<tbody>
		<tr>
			<td class="codeLibBaseTableColumnMiddle codeLibBaseLinkNormalOver" colspan=2><a href="https://www.netbk.co.jp/wpl/NBGate" rel=noreferrer target="_blank">金融機関ログイン画面</a></td>
		</tr>
		<tr id="#{idSelf}IdLoginWrap">
			<td class="codeLibBaseTableRow" style="width:150px;">ユーザネーム</td>
			<td class="codeLibBaseTableRow" ><div style="text-align:right;">#{IdLogin}</div></td>
		</tr>
		<tr id="#{idSelf}StrPasswordWrap">
			<td class="codeLibBaseTableRow">WEBログインパスワード</td>
			<td class="codeLibBaseTableRow"><div style="text-align:right;">非表示</div></td>
		</tr>
	</tbody>
</table>
	',
);