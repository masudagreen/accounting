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
	'tmplTableRents' => '
		<table id="tableWrap#{id}" cellspacing="1" cellpadding="3" border="0" bgcolor="#222222" width="750px" >
			<tbody id="#{id}">

			</tbody>
		</table>
	',
	'tmplTableKeyMoney' => '
		<table id="tableWrap#{id}" cellspacing="1" cellpadding="3" border="0" bgcolor="#222222" width="750px" >
			<tbody id="#{id}">

			</tbody>
		</table>
	',
	'tmplTableIndustrialProperty' => '
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
	'tmplTableBottomCaution1' => '
		<table id="tableWrap#{id}" cellspacing="1" cellpadding="3" border="0" bgcolor="#fff" width="750px" >
			<tbody id="#{id}">

			</tbody>
		</table>
	',
	'tmplTableBottomCaution2' => '
		<table id="tableWrap#{id}" cellspacing="1" cellpadding="3" border="0" bgcolor="#fff" width="750px" >
			<tbody id="#{id}">

			</tbody>
		</table>
	',
	'tmplColumnRents' => '
		<tr valign="middle">
			<td class="codeLibPrintMiddle"  colspan=7><p style="margin:10px;">{$strTitle}</p></td>
		</tr>
		<tr valign="middle">
			<td class="codeLibPrintMiddle" style="width:80px;" rowspan=2>{$strCategory}</td>
			<td class="codeLibPrintMiddle" style="width:230px;" >{$strUse}</td>
			<td class="codeLibPrintMiddle" style="width:230px;" >{$strName}</td>
			<td class="codeLibPrintMiddle" style="width:90px;" colspan=3>{$strSpan}</td>
			<td class="codeLibPrintMiddle" style="width:120px;" rowspan=2>{$strMemo}</td>
		</tr>
		<tr valign="middle">
			<td class="codeLibPrintMiddle" >{$strPlace}</td>
			<td class="codeLibPrintMiddle" >{$strAddress}</td>
			<td class="codeLibPrintMiddle" colspan=3>{$strValue}</td>
		</tr>
	',
	'tmplColumnKeyMoney' => '
		<tr valign="middle">
			<td class="codeLibPrintMiddle" colspan=7><p style="margin:10px;">{$strTitle}</p></td>
		</tr>
		<tr valign="middle">
			<td class="codeLibPrintMiddle" style="width:160px;">{$strName}</td>
			<td class="codeLibPrintMiddle" style="width:120px;" rowspan=2 colspan=3>{$strTime}</td>
			<td class="codeLibPrintMiddle" style="width:150px;" rowspan=2>{$strValue}</td>
			<td class="codeLibPrintMiddle" style="width:160px;" rowspan=2>{$strContent}</td>
			<td class="codeLibPrintMiddle" style="width:160px;" rowspan=2>{$strMemo}</td>
		</tr>
		<tr valign="middle">
			<td class="codeLibPrintMiddle" >{$strAddress}</td>
		</tr>
	',
	'tmplColumnIndustrialProperty' => '
		<tr valign="middle">
			<td class="codeLibPrintMiddle" colspan=14><p style="margin:10px;">{$strTitle}</p></td>
		</tr>
		<tr valign="middle">
			<td class="codeLibPrintMiddle" style="width:80px;" rowspan=2>{$strLicense}</td>
			<td class="codeLibPrintMiddle" style="width:160px;">{$strName}</td>
			<td class="codeLibPrintMiddle" style="width:120px;" colspan=4 rowspan=2>{$strSpan}</td>
			<td class="codeLibPrintMiddle" style="width:220px;" colspan=5>{$strPay}</td>
			<td class="codeLibPrintMiddle" style="width:170px;" rowspan=2>{$strMemo}</td>

		</tr>
		<tr valign="middle">
			<td class="codeLibPrintMiddle" >{$strAddress}</td>
			<td class="codeLibPrintMiddle" style="width:120px;" colspan=4>{$strSpanPay}</td>
			<td class="codeLibPrintMiddle" style="width:100px;">{$strValue}</td>
		</tr>
	',
	'tmplPage' => '',
	'tmplRow' => array(
		'tmplTrTop' => '',
		'tmplTr1Rents' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintLeft" rowspan=3>{$value.strTextCategory}</td>
				<td class="codeLibPrintLeft" rowspan=2>{$value.strTextUse}</td>
				<td class="codeLibPrintLeft" rowspan=2>{$value.strTextName}</td>
				<td class="codeLibPrintLeft" style="width:30px;">{$value.strTextYearStart}</td>
				<td class="codeLibPrintLeft" style="width:30px;">{$value.strTextMonthStart}</td>
				<td class="codeLibPrintLeft" style="width:30px;">{$value.strTextDateStart}</td>
				<td class="codeLibPrintLeft" rowspan=3>{$value.strTextMemo}</td>
			</tr>
		',
		'tmplTr2Rents' => '
			<tr id="row#{idRow}_#{idTr2}" valign="top">
				<td class="codeLibPrintLeft" style="width:30px;">{$value.strTextYearEnd}</td>
				<td class="codeLibPrintLeft" style="width:30px;">{$value.strTextMonthEnd}</td>
				<td class="codeLibPrintLeft" style="width:30px;">{$value.strTextDateEnd}</td>
			</tr>
		',
		'tmplTr3Rents' => '
			<tr id="row#{idRow}_#{idTr3}" valign="top">
				<td class="codeLibPrintLeft" >{$value.strTextPlace}</td>
				<td class="codeLibPrintLeft" >{$value.strTextAddress}</td>
				<td class="codeLibPrintRight" colspan=3>{$value.strTextValue}</td>
			</tr>
		',
		'tmplTr1KeyMoney' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintLeft">{$value.strTextName}</td>
				<td class="codeLibPrintLeft" style="width:40px;" rowspan=2>{$value.strTextYear}</td>
				<td class="codeLibPrintLeft" style="width:40px;" rowspan=2>{$value.strTextMonth}</td>
				<td class="codeLibPrintLeft" style="width:40px;" rowspan=2>{$value.strTextDate}</td>
				<td class="codeLibPrintRight" rowspan=2>{$value.strTextValue}</td>
				<td class="codeLibPrintLeft" rowspan=2>{$value.strTextContent}</td>
				<td class="codeLibPrintLeft" rowspan=2>{$value.strTextMemo}</td>
			</tr>
		',
		'tmplTr2KeyMoney' => '
			<tr id="row#{idRow}_#{idTr2}" valign="top">
				<td class="codeLibPrintLeft">{$value.strTextAddress}</td>
			</tr>
		',
		'tmplTr1IndustrialProperty' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintLeft"  rowspan=2>{$value.strTextLicense}</td>
				<td class="codeLibPrintLeft" >{$value.strTextName}</td>
				<td class="codeLibPrintLeft" style="width:30px;" rowspan=2>{$value.strTextYearSpanStart}</td>
				<td class="codeLibPrintLeft" style="width:30px;" rowspan=2>{$value.strTextMonthSpanStart}</td>
				<td class="codeLibPrintLeft" style="width:30px;" rowspan=2>{$value.strTextYearSpanEnd}</td>
				<td class="codeLibPrintLeft" style="width:30px;" rowspan=2>{$value.strTextMonthSpanEnd}</td>
				<td class="codeLibPrintLeft" style="width:30px;" rowspan=2>{$value.strTextYearSpanPayStart}</td>
				<td class="codeLibPrintLeft" style="width:30px;" rowspan=2>{$value.strTextMonthSpanPayStart}</td>
				<td class="codeLibPrintLeft" style="width:30px;" rowspan=2>{$value.strTextYearSpanPayEnd}</td>
				<td class="codeLibPrintLeft" style="width:30px;" rowspan=2>{$value.strTextMonthSpanPayEnd}</td>
				<td class="codeLibPrintRight" rowspan=2>{$value.strTextValue}</td>
				<td class="codeLibPrintLeft" rowspan=2>{$value.strTextMemo}</td>
			</tr>
		',
		'tmplTr2IndustrialProperty' => '
			<tr id="row#{idRow}_#{idTr2}" valign="top">
				<td class="codeLibPrintLeft" >{$value.strTextAddress}</td>
			</tr>
		',

		'tmplTrCaution1' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintRight" style="width:10px;">{$strCautionMark}</td>
				<td class="codeLibPrintLeft" style="width:740px;">
					{$strCaution1}<br>
					{$strCaution2}<br>
					{$strCaution3}
				</td>
			</tr>
		',
		'tmplTrCautionLaw' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintRight" colspan=2>{$strCautionLaw}</td>
			</tr>
		',
		'tmplTrCaution2' => '
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
