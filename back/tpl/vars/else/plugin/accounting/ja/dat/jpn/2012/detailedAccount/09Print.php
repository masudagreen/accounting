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
			<td class="codeLibPrintMiddle" style="width:120px;" rowspan=2>{$strAccountTitle}</td>
			<td class="codeLibPrintMiddle" style="width:410px;" colspan=2>{$strTarget}</td>
			<td class="codeLibPrintMiddle" style="width:100px;" rowspan=2>{$strValue}</td>
			<td class="codeLibPrintMiddle" style="width:120px;" rowspan=2>{$strMemo}</td>
		</tr>
		<tr valign="middle">
			<td class="codeLibPrintMiddle" style="width:180px;">{$strName}</td>
			<td class="codeLibPrintMiddle" style="width:230px;">{$strAddress}</td>
		</tr>
	',
	'tmplPage' => '',
	'tmplRow' => array(
		'tmplTrTop' => '',
		'tmplTr1' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintLeft" >{$value.strTextAccountTitle}</td>
				<td class="codeLibPrintLeft" >{$value.strTextName}</td>
				<td class="codeLibPrintLeft" >{$value.strTextAddress}</td>
				<td class="codeLibPrintRight">{$value.strTextValue}</td>
				<td class="codeLibPrintLeft" >{$value.strTextMemo}</td>
			</tr>
		',
		'tmplTrSum' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintMiddle">{$strSum}</td>
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
					{$strCaution3}<br>
					{$strCaution4}<br>
					<table style="margin-left:30px;margin-top:10px;margin-bottom:10px;" cellspacing="1" cellpadding="3" border="0" bgcolor="#222222" width="650px">
						<tbody>
							<tr valign="top">
								<td class="codeLibPrintMiddle" style="width:20px;font-size:8px;" rowspan=3>{$strColumnDividendsPayable}</td>
								<td class="codeLibPrintMiddle" style="width:150px;" colspan=3>{$strDecision}</td>
								<td class="codeLibPrintMiddle" style="width:155px;">{$strValue}</td>
								<td class="codeLibPrintMiddle" style="width:20px;font-size:8px;" rowspan=3>{$strColumnAccruedBonusToDirectors}</td>
								<td class="codeLibPrintMiddle" style="width:150px;" colspan=3>{$strDecision}</td>
								<td class="codeLibPrintMiddle" style="width:155px;">{$strValue}</td>
							</tr>
							<tr valign="top">
								<td class="codeLibPrintLeft" style="width:50px;">{$value.strTextYearDividendsPayable1}</td>
								<td class="codeLibPrintLeft" style="width:50px;">{$value.strTextMonthDividendsPayable1}</td>
								<td class="codeLibPrintLeft" style="width:50px;">{$value.strTextDateDividendsPayable1}</td>
								<td class="codeLibPrintRight" >{$value.strTextValueDividendsPayable1}</td>
								<td class="codeLibPrintLeft" style="width:50px;">{$value.strTextYearAccruedBonusToDirectors1}</td>
								<td class="codeLibPrintLeft" style="width:50px;">{$value.strTextMonthAccruedBonusToDirectors1}</td>
								<td class="codeLibPrintLeft" style="width:50px;">{$value.strTextDateAccruedBonusToDirectors1}</td>
								<td class="codeLibPrintRight" >{$value.strTextValueAccruedBonusToDirectors1}</td>
							</tr>
							<tr valign="top">
								<td class="codeLibPrintLeft">{$value.strTextYearDividendsPayable2}</td>
								<td class="codeLibPrintLeft">{$value.strTextMonthDividendsPayable2}</td>
								<td class="codeLibPrintLeft">{$value.strTextDateDividendsPayable2}</td>
								<td class="codeLibPrintRight" >{$value.strTextValueDividendsPayable2}</td>
								<td class="codeLibPrintLeft">{$value.strTextYearAccruedBonusToDirectors2}</td>
								<td class="codeLibPrintLeft">{$value.strTextMonthAccruedBonusToDirectors2}</td>
								<td class="codeLibPrintLeft">{$value.strTextDateAccruedBonusToDirectors2}</td>
								<td class="codeLibPrintRight" >{$value.strTextValueAccruedBonusToDirectors2}</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		',
	),
);
