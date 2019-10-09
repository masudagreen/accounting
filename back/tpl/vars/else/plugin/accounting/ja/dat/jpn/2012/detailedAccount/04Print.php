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
	'tmplTableSuspensePayment' => '
		<table id="tableWrap#{id}" cellspacing="1" cellpadding="3" border="0" bgcolor="#222222" width="750px" >
			<tbody id="#{id}">

			</tbody>
		</table>
	',
	'tmplTableLoansReceivable' => '
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
	'tmplTableTop2' => '
		<table id="tableWrap#{id}" cellspacing="1" cellpadding="3" border="0" bgcolor="" width="750px" >
			<tbody id="#{id}">

			</tbody>
		</table>
	',
	'tmplTableTopLoansReceivable' => '',
	'tmplTableBottom' => '
		<table id="tableWrap#{id}" cellspacing="1" cellpadding="3" border="0" bgcolor="#fff" width="750px" >
			<tbody id="#{id}">

			</tbody>
		</table>
	',
	'tmplTableBottomCautionSuspensePayment' => '
		<table id="tableWrap#{id}" cellspacing="1" cellpadding="3" border="0" bgcolor="#fff" width="750px" >
			<tbody id="#{id}">

			</tbody>
		</table>
	',
	'tmplTableBottomCautionLoansReceivable' => '
		<table id="tableWrap#{id}" cellspacing="1" cellpadding="3" border="0" bgcolor="#fff" width="750px" >
			<tbody id="#{id}">

			</tbody>
		</table>
	',
	'tmplColumnSuspensePayment' => '
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
	'tmplColumnLoansReceivable' => '
		<tr valign="middle">
			<td class="codeLibPrintMiddle" style="width:160px;" >{$strBorrower}</td>
			<td class="codeLibPrintMiddle" style="width:60px;font-size:4px;" >{$strRelation}</td>
			<td class="codeLibPrintMiddle" style="width:125px;" rowspan=2>{$strValue}</td>
			<td class="codeLibPrintMiddle" style="width:115px;font-size:8px;" >{$strValueReceived}</td>
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
		'tmplTr1SuspensePayment' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintLeft">{$value.strTextAccountTitle}</td>
				<td class="codeLibPrintLeft">{$value.strTextName}</td>
				<td class="codeLibPrintLeft">{$value.strTextAddress}</td>
				<td class="codeLibPrintLeft">{$value.strTextRelation}</td>
				<td class="codeLibPrintRight">{$value.strTextValue}</td>
				<td class="codeLibPrintLeft">{$value.strTextMemo}</td>
			</tr>
		',
		'tmplTrSumSuspensePayment' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintMiddle">{$strSum}</td>
				<td class="codeLibPrintLeft"></td>
				<td class="codeLibPrintLeft"></td>
				<td class="codeLibPrintLeft"></td>
				<td class="codeLibPrintRight">{$value.strTextSum}</td>
				<td class="codeLibPrintLeft"></td>
			</tr>
		',
		'tmplTr1LoansReceivable' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintLeft">{$value.strTextBorrower}</td>
				<td class="codeLibPrintLeft">{$value.strTextRelation}</td>

				<td class="codeLibPrintRight" rowspan=2>{$value.strTextValue}</td>
				<td class="codeLibPrintRight" >{$value.strTextValueReceived}</td>

				<td class="codeLibPrintLeft" rowspan=2>{$value.strTextReason}</td>
				<td class="codeLibPrintLeft" rowspan=2>{$value.strTextMemo}</td>
			</tr>
		',
		'tmplTr2LoansReceivable' => '
			<tr id="row#{idRow}_#{idTr2}" valign="top">
				<td class="codeLibPrintLeft" colspan=2>{$value.strTextAddress}</td>
				<td class="codeLibPrintRight" >{$value.strTextRate}</td>
			</tr>
		',
		'tmplTrSum1LoansReceivable' => '
			<tr id="row#{idRow}_#{idTr1}" valign="middle">
				<td class="codeLibPrintMiddle" colspan=2 rowspan=2>{$strSum}</td>
				<td class="codeLibPrintRight" rowspan=2>{$value.strTextSum}</td>
				<td class="codeLibPrintRight">{$value.strTextSumReceived}</td>
				<td class="codeLibPrintMiddle" rowspan=2></td>
				<td class="codeLibPrintMiddle" rowspan=2></td>
			</tr>
		',
		'tmplTrSum2LoansReceivable' => '
			<tr id="row#{idRow}_#{idTr2}" valign="middle">
				<td class="codeLibPrintRight" >{$strBlank}</td>
			</tr>
		',
		'tmplTr1LoansReceivable' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintLeft">{$value.strTextBorrower}</td>
				<td class="codeLibPrintLeft">{$value.strTextRelation}</td>

				<td class="codeLibPrintRight" rowspan=2>{$value.strTextValue}</td>
				<td class="codeLibPrintRight" >{$value.strTextValueReceived}</td>

				<td class="codeLibPrintLeft" rowspan=2>{$value.strTextReason}</td>
				<td class="codeLibPrintLeft" rowspan=2>{$value.strTextMemo}</td>
			</tr>
		',
		'tmplTr2LoansReceivable' => '
			<tr id="row#{idRow}_#{idTr2}" valign="top">
				<td class="codeLibPrintLeft" colspan=2>{$value.strTextAddress}</td>
				<td class="codeLibPrintRight" >{$value.strTextRate}</td>
			</tr>
		',
		'tmplTrSum1LoansReceivable' => '
			<tr id="row#{idRow}_#{idTr1}" valign="middle">
				<td class="codeLibPrintMiddle" colspan=2 rowspan=2>{$strSum}</td>
				<td class="codeLibPrintRight" rowspan=2>{$value.strTextSum}</td>
				<td class="codeLibPrintRight">{$value.strTextSumReceived}</td>
				<td class="codeLibPrintMiddle" rowspan=2></td>
				<td class="codeLibPrintMiddle" rowspan=2></td>
			</tr>
		',
		'tmplTrSum2LoansReceivable' => '
			<tr id="row#{idRow}_#{idTr2}" valign="middle">
				<td class="codeLibPrintRight" >{$strBlank}</td>
			</tr>
		',
		'tmplTrCautionSuspensePayment' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintRight" style="width:10px;">{$strCautionMark}</td>
				<td class="codeLibPrintLeft" style="width:740px;">
					{$strCaution1}<br>
					{$strCaution2}<br>
					{$strCaution3}
				</td>
			</tr>
		',

		'tmplTrCautionLoansReceivable' => '
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
		'tmplTrTop' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintRight" colspan=2>{$strCautionLaw}</td>
			</tr>
		',
		'tmplTrTop2' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintLeft codeLibPrintTitleBold">{$strTitle}</td>
				<td class="codeLibPrintRight"></td>
			</tr>
		',

	),
);
