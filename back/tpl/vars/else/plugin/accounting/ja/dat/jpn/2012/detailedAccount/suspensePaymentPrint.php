<?php

$vars = array(
	'tmplWrap' => '
		<div id="idHeight">
			<div id="insertTable">
				<table cellspacing="1" cellpadding="3" border="0"  bgcolor="" width="750px" >
					<tbody>
						<tr>
							<td class="codeLibPrintLeft codeLibPrintTitleBold">{$strTitle}</td>
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
					<td class="codeLibPrintLeft codeLibPrintTitleBold">{$strTitle}</td>
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
			<td class="codeLibPrintMiddle" style="width:120px;" rowspan=2>{$strAccountTitle}</td>
			<td class="codeLibPrintMiddle" style="width:410px;" colspan=3>{$strTarget}</td>
			<td class="codeLibPrintMiddle" style="width:120px;" rowspan=2>{$strValue}</td>
			<td class="codeLibPrintMiddle" style="width:100px;" rowspan=2>{$strMemo}</td>
		</tr>
		<tr valign="middle">
			<td class="codeLibPrintMiddle" style="width:160px;">{$strName}</td>
			<td class="codeLibPrintMiddle" style="width:190px;">{$strAddress}</td>
			<td class="codeLibPrintMiddle" style="width:60px;font-size:6px;">{$strRelation}</td>
		</tr>
	',
	'tmplPage' => '',
	'tmplRow' => array(
		'tmplTrTop' => '',
		'tmplTr1' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintLeft">{$value.strTextAccountTitle}</td>
				<td class="codeLibPrintLeft">{$value.strTextName}</td>
				<td class="codeLibPrintLeft">{$value.strTextAddress}</td>
				<td class="codeLibPrintLeft">{$value.strTextRelation}</td>
				<td class="codeLibPrintRight">{$value.strTextValue}</td>
				<td class="codeLibPrintLeft">{$value.strTextMemo}</td>
			</tr>
		',
		'tmplTrSum' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintMiddle">{$strSum}</td>
				<td class="codeLibPrintLeft"></td>
				<td class="codeLibPrintLeft"></td>
				<td class="codeLibPrintLeft"></td>
				<td class="codeLibPrintRight">{$value.strTextSum}</td>
				<td class="codeLibPrintLeft"></td>
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
					{$strCaution2}<br>
					{$strCaution3}
				</td>
			</tr>
		',
	),
);
