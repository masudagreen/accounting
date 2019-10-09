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
			<td class="codeLibPrintMiddle" style="width:120px;">{$strEntityName}</td>
			<td class="codeLibPrintMiddle" style="width:100px;">{$strName}</td>
			<td class="codeLibPrintMiddle" style="width:100px;" rowspan=2>{$strContent}</td>

			<td class="codeLibPrintMiddle" style="width:100px;" rowspan=2>{$strValueSales}</td>
			<td class="codeLibPrintMiddle" style="width:100px;"rowspan=2>{$strValue}</td>
			<td class="codeLibPrintMiddle" style="width:65px;font-size:8px;">{$strEmployee}</td>
			<td class="codeLibPrintMiddle" style="width:65px;font-size:8px;" rowspan=2>{$strOffice}</td>
			<td class="codeLibPrintMiddle" style="width:100px;" rowspan=2>{$strMemo}</td>
		</tr>
		<tr valign="middle">
			<td class="codeLibPrintMiddle" >{$strAddress}</td>
			<td class="codeLibPrintMiddle" style="font-size:10px;">{$strRelation}</td>
			<td class="codeLibPrintMiddle" style="font-size:8px;">{$strSize}</td>
		</tr>
	',
	'tmplPage' => '',
	'tmplRow' => array(
		'tmplTrTop' => '',
		'tmplTr1' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintLeft" >{$value.strTextEntityName}</td>
				<td class="codeLibPrintLeft" >{$value.strTextName}</td>
				<td class="codeLibPrintLeft" rowspan=2>{$value.strTextContent}</td>
				<td class="codeLibPrintRight" rowspan=2>{$value.strTextValueSales}</td>
				<td class="codeLibPrintRight" rowspan=2>{$value.strTextValue}</td>

				<td class="codeLibPrintLeft" >{$value.strTextEmployee}</td>
				<td class="codeLibPrintLeft" rowspan=2>{$value.strTextOffice}</td>
				<td class="codeLibPrintLeft" rowspan=2>{$value.strTextMemo}</td>
			</tr>
		',
		'tmplTr2' => '
			<tr id="row#{idRow}_#{idTr2}" valign="top">
				<td class="codeLibPrintLeft" >{$value.strTextAddress}</td>
				<td class="codeLibPrintLeft" >{$value.strTextRelation}</td>
				<td class="codeLibPrintLeft" >{$value.strTextSize}</td>
			</tr>
		',
		'tmplTrSum' => '
			<tr id="row#{idRow}_#{idTr1}" valign="middle">
				<td class="codeLibPrintMiddle" >{$strSum}</td>
				<td class="codeLibPrintMiddle" ></td>
				<td class="codeLibPrintMiddle" ></td>
				<td class="codeLibPrintRight" >{$value.strTextSumSales}</td>
				<td class="codeLibPrintRight" >{$value.strTextSum}</td>
				<td class="codeLibPrintMiddle" ></td>
				<td class="codeLibPrintMiddle" ></td>
				<td class="codeLibPrintMiddle" ></td>
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
