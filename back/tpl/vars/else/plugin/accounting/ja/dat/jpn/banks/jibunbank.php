<?php

$vars = array(
	'strTitle' => 'じぶん銀行',
	'flagCsv' => 1,
	'strDelimiter' => ',',
	'strFileType' => 'csv',
	'varsCsv' => array(
		'stampBook' => '年月日',
		'numValueIn' => '入金',
		'numValueOut' => '出金',
		'strTitle' => 'お取引内容',
		'numBalance' => '残高',
	),
	'varsDetail' => array(
		array(
			'flagMustUse' => 1,
			'id' => 'IdNumAccount',
			'strTitle' => 'お客さま番号（10桁）' , 'strExplain' => '' ,
			'value' => '',
			'flagErrorNow' => 0,
			'arrayError' => array(
				array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
				array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字で記入する必要があるようです。',  ), ),
				array( 'flagCheck' => 'min', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'str', 'num' => 10, 'strComment' => array( 'common' => '入力文字数が少なすぎるようです。', ),),
			),
			'flagContentUse' => 0,
			'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
			'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 10,
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
				array( 'flagCheck' => 'min', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'str', 'num' => 6, 'strComment' => array( 'common' => '入力文字数が少なすぎるようです。', ),),
			),
			'flagContentUse' => 0,
			'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
			'flagTag' => 'input', 'flagInputType' => 'password', 'numMaxlength' => 16,
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
			<td class="codeLibBaseTableColumnMiddle" colspan=2>じぶん銀行</td>
		</tr>
		<tr>
			<td class="codeLibBaseTableRow" colspan=2>
			<p class="codeLibBaseLinkNormalOver">・事前に<a href="http://www.jibunbank.co.jp/" rel=noreferrer target="_blank">じぶん銀行</a>でログインができるか確認してください。</p>
			<p>・ログインパスワードは、設定の都度入力をしてください。</p>
		</tr>
		<tr id="#{idSelf}IdNumAccountWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}IdNumAccountStrTitle" style="width:150px;">お客さま番号（10桁）</td>
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
			<td class="codeLibBaseTableColumnMiddle codeLibBaseLinkNormalOver" colspan=2><a href="http://www.jibunbank.co.jp/" rel=noreferrer target="_blank">金融機関ログイン画面</a></td>
		</tr>
		<tr id="#{idSelf}IdNumAccountWrap">
			<td class="codeLibBaseTableRow" style="width:150px;">お客さま番号（10桁）</td>
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