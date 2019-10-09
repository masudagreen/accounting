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
			<td class="codeLibPrintMiddle" style="width:160px;" >{$strBorrower}</td>
			<td class="codeLibPrintMiddle" style="width:60px;font-size:4px;" >{$strRelation}</td>
			<td class="codeLibPrintMiddle" style="width:125px;" rowspan=2>{$strValue}</td>
			<td class="codeLibPrintMiddle" style="width:115px;font-size:8px;" >{$strValuePayable}</td>
			<td class="codeLibPrintMiddle" style="width:120px;" rowspan=2>{$strReason}</td>
			<td class="codeLibPrintMiddle" style="width:170px;" rowspan=2>{$strMemoTop}<p style="font-size:6px;">{$strMemoBottom}</p></td>
		</tr>
		<tr valign="middle">
			<td class="codeLibPrintMiddle" colspan=2>{$strAddress}</td>
			<td class="codeLibPrintMiddle" >{$strRate}</td>
		</tr>
	',
	'tmplPage' => '',
	'tmplRow' => array(
		'tmplTrTop' => '',
		'tmplTr1' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintLeft">{$value.strTextBorrower}</td>
				<td class="codeLibPrintLeft">{$value.strTextRelation}</td>

				<td class="codeLibPrintRight" rowspan=2>{$value.strTextValue}</td>
				<td class="codeLibPrintRight" >{$value.strTextValuePayable}</td>

				<td class="codeLibPrintLeft" rowspan=2>{$value.strTextReason}</td>
				<td class="codeLibPrintLeft" rowspan=2>{$value.strTextMemo}</td>
			</tr>
		',
		'tmplTr2' => '
			<tr id="row#{idRow}_#{idTr2}" valign="top">
				<td class="codeLibPrintLeft" colspan=2>{$value.strTextAddress}</td>
				<td class="codeLibPrintRight" >{$value.strTextRate}</td>
			</tr>
		',
		'tmplTrSum1' => '
			<tr id="row#{idRow}_#{idTr1}" valign="middle">
				<td class="codeLibPrintMiddle" colspan=2 rowspan=2>{$strSum}</td>
				<td class="codeLibPrintRight" rowspan=2>{$value.strTextSum}</td>
				<td class="codeLibPrintRight">{$value.strTextSumPayable}</td>
				<td class="codeLibPrintMiddle" rowspan=2></td>
				<td class="codeLibPrintMiddle" rowspan=2></td>
			</tr>
		',
		'tmplTrSum2' => '
			<tr id="row#{idRow}_#{idTr2}" valign="middle">
				<td class="codeLibPrintRight" >{$strBlank}</td>
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
					{$strCaution3}<br>
					{$strCaution4}
				</td>
			</tr>
		',
	),
);
