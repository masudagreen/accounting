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
			<td class="codeLibPrintMiddle" style="width:150px;" rowspan=2>{$strPay}</td>
			<td class="codeLibPrintMiddle" style="width:90px;" colspan=3>{$strDrawerYear}</td>
			<td class="codeLibPrintMiddle" style="width:100px;" rowspan=2 colspan=2>{$strBankPay}</td>
			<td class="codeLibPrintMiddle" style="width:150px;" rowspan=2>{$strValue}</td>
			<td class="codeLibPrintMiddle" style="width:260px;" rowspan=2>{$strMemo}</td>
		</tr>
		<tr valign="middle">
			<td class="codeLibPrintMiddle" colspan=3>{$strLimitYear}</td>
		</tr>
	',
	'tmplPage' => '',
	'tmplRow' => array(
		'tmplTrTop' => '',
		'tmplTr1' => '
			<tr id="row#{idRow}_#{idTr1}" valign="middle">
				<td class="codeLibPrintLeft" rowspan=2>{$value.strTextPay}</td>

				<td class="codeLibPrintLeft" style="width:30px;">{$value.strTextDrawerYear}</td>
				<td class="codeLibPrintLeft" style="width:30px;">{$value.strTextDrawerMonth}</td>
				<td class="codeLibPrintLeft" style="width:30px;">{$value.strTextDrawerDate}</td>

				<td class="codeLibPrintLeft" style="width:50px;" rowspan=2>{$value.strTextBankPay}</td>
				<td class="codeLibPrintLeft" style="width:50px;" rowspan=2>{$value.strTextBranchPay}</td>

				<td class="codeLibPrintRight" rowspan=2>{$value.strTextValue}</td>

				<td class="codeLibPrintLeft" rowspan=2>{$value.strTextMemo}</td>
			</tr>
		',
		'tmplTr2' => '
			<tr id="row#{idRow}_#{idTr2}" valign="middle">
				<td class="codeLibPrintLeft" >{$value.strTextLimitYear}</td>
				<td class="codeLibPrintLeft" >{$value.strTextLimitMonth}</td>
				<td class="codeLibPrintLeft" >{$value.strTextLimitDate}</td>
			</tr>
		',
		'tmplTrSum' => '
			<tr valign="top">
				<td class="codeLibPrintMiddle" >{$strSum}</td>
				<td class="codeLibPrintMiddle" colspan=3></td>
				<td class="codeLibPrintMiddle" colspan=2></td>
				<td class="codeLibPrintRight" >{$value.strTextSum}</td>
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
