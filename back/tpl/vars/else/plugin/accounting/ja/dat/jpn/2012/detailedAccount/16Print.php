<?php

$vars = array(
	'tmplWrap' => '
		<div id="idHeight">
			<div id="insertTable">
				<table cellspacing="1" cellpadding="3" border="0"  bgcolor="" width="750px" >
					<tbody>
						<tr>
							<td class="codeLibPrintLeft codeLibPrintTitleBold">{$strTitleExt}</td>
							<td class="codeLibPrintRight">{$strTitleNum}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	',
	'tmplTable' => '
		<table id="tableWrap#{id}" cellspacing="1" cellpadding="3" border="0" bgcolor="#222222" width="750px" >
			<tbody id="#{id}">

			</tbody>
		</table>
	',
	'tmplTableTop' => '
		<table cellspacing="1" cellpadding="3" border="0"  bgcolor="" width="750px" >
			<tbody>
				<tr>
					<td class="codeLibPrintLeft codeLibPrintTitleBold">{$strTitleExt}</td>
					<td class="codeLibPrintRight">{$strTitleNum}</td>
				</tr>
			</tbody>
		</table>
	',
	'tmplTableBottom' => '
		<table id="tableWrap#{id}" cellspacing="1" cellpadding="3" border="0" bgcolor="#fff" width="750px" >
			<tbody id="#{id}">

			</tbody>
		</table>
	',
	'tmplTableBottomCaution' => '
		<table id="tableWrap#{id}" cellspacing="1" cellpadding="3" border="0" bgcolor="#fff" width="750px" >
			<tbody id="#{id}">

			</tbody>
		</table>
	',
	'tmplColumn' => '
		<tr valign="middle">
			<td class="codeLibPrintMiddle" style="width:175px;" colspan=2>{$strAccountTitle}</td>
			<td class="codeLibPrintMiddle" style="width:145px;">{$strMemo}</td>
			<td class="codeLibPrintMiddle" style="width:145px;">{$strTarget}</td>
			<td class="codeLibPrintMiddle" style="width:180px;">{$strAddress}</td>
			<td class="codeLibPrintMiddle" style="width:95px;">{$strValue}</td>
		</tr>
	',
	'tmplPage' => '',
	'tmplRow' => array(
		'tmplTrTop' => '',
		'tmplTrFirst' => '
			<tr id="row#{idRow}_#{idTr{$value.idTr}}" valign="top">
				<td class="codeLibPrintMiddle" style="width:20px;" rowspan={$numRows}>{$strColumn}</td>
				<td class="codeLibPrintLeft" >{$value.strTextAccountTitle{$value.idTr}}</td>
				<td class="codeLibPrintLeft" >{$value.strTextMemo{$value.idTr}}</td>
				<td class="codeLibPrintLeft" >{$value.strTextTarget{$value.idTr}}</td>
				<td class="codeLibPrintLeft" >{$value.strTextAddress{$value.idTr}}</td>
				<td class="codeLibPrintRight" >{$value.strTextValue{$value.idTr}}</td>
			</tr>
		',
		'tmplTrMiddle' => '
			<tr id="row#{idRow}_#{idTr{$value.idTr}}" valign="top">
				<td class="codeLibPrintLeft" >{$value.strTextAccountTitle{$value.idTr}}</td>
				<td class="codeLibPrintLeft" >{$value.strTextMemo{$value.idTr}}</td>
				<td class="codeLibPrintLeft" >{$value.strTextTarget{$value.idTr}}</td>
				<td class="codeLibPrintLeft" >{$value.strTextAddress{$value.idTr}}</td>
				<td class="codeLibPrintRight" >{$value.strTextValue{$value.idTr}}</td>
			</tr>
		',
		'tmplTrCautionLaw' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintRight" colspan=2>{$strCautionLaw}</td>
			</tr>
		',
		'tmplTrCaution' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintRight" style="width:10px;">{$strCautionMark}</td>
				<td class="codeLibPrintLeft" style="width:740px;">
					{$strCaution1}<br>
					{$strCaution2}
				</td>
			</tr>
		',
	),
);
