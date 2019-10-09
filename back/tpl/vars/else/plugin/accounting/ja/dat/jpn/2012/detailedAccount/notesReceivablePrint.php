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
			<td class="codeLibPrintMiddle" style="width:140px;" rowspan=2>{$strDrawer}</td>
			<td class="codeLibPrintMiddle" style="width:70px;" colspan=3>{$strDrawerYear}</td>
			<td class="codeLibPrintMiddle" style="width:120px;" rowspan=2 colspan=2>{$strBankPay}</td>
			<td class="codeLibPrintMiddle" style="width:110px;" rowspan=2>{$strValue}</td>
			<td class="codeLibPrintMiddle" style="width:120px;" rowspan=2 colspan=2>{$strBankDiscount}</td>
			<td class="codeLibPrintMiddle" style="width:190px;" rowspan=2>{$strMemo}</td>
		</tr>
		<tr valign="middle">
			<td class="codeLibPrintMiddle" colspan=3>{$strLimitYear}</td>
		</tr>
	',
	'tmplPage' => '',
	'tmplRow' => array(
		'tmplTrTop' => '',
		'tmplTr1' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintLeft" rowspan=2>{$value.strTextDrawer}</td>

				<td class="codeLibPrintLeft" style="width:30px;">{$value.strTextDrawerYear}</td>
				<td class="codeLibPrintLeft" style="width:20px;">{$value.strTextDrawerMonth}</td>
				<td class="codeLibPrintLeft" style="width:20px;">{$value.strTextDrawerDate}</td>

				<td class="codeLibPrintLeft" style="width:60px;" rowspan=2>{$value.strTextBankPay}</td>
				<td class="codeLibPrintLeft" style="width:60px;" rowspan=2>{$value.strTextBranchPay}</td>

				<td class="codeLibPrintRight" rowspan=2>{$value.strTextValue}</td>

				<td class="codeLibPrintLeft" style="width:60px;" rowspan=2>{$value.strTextBankDiscount}</td>
				<td class="codeLibPrintLeft" style="width:60px;" rowspan=2>{$value.strTextBranchDiscount}</td>

				<td class="codeLibPrintLeft" rowspan=2>{$value.strTextMemo}</td>
			</tr>
		',
		'tmplTr2' => '
			<tr id="row#{idRow}_#{idTr2}" valign="top">
				<td class="codeLibPrintLeft">{$value.strTextLimitYear}</td>
				<td class="codeLibPrintLeft">{$value.strTextLimitMonth}</td>
				<td class="codeLibPrintLeft">{$value.strTextLimitDate}</td>
			</tr>
		',
		'tmplTrSum' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintMiddle">{$strSum}</td>
				<td class="codeLibPrintLeft" colspan=3></td>
				<td class="codeLibPrintLeft" colspan=2></td>
				<td class="codeLibPrintRight">{$value.strTextSum}</td>
				<td class="codeLibPrintLeft" colspan=2></td>
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
					{$strCaution3}<br>
					{$strCaution4}<br>
					{$strCaution5}<br>
					{$strCaution6}
				</td>
			</tr>
		',
	),
);
