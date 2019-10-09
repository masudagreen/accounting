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
			<td class="codeLibPrintMiddle" style="width:80px" rowspan=2>{$strType}</td>
			<td class="codeLibPrintMiddle" style="width:70px" rowspan=2>{$strUse}</td>
			<td class="codeLibPrintMiddle" style="width:50px" rowspan=2>{$strSize}</td>
			<td class="codeLibPrintMiddle" style="width:100px" rowspan=3>{$strValue}</td>
			<td class="codeLibPrintMiddle" style="width:450px" colspan=7>{$strDetail}</td>
		</tr>
		<tr valign="middle">
			<td class="codeLibPrintMiddle" style="width:90px" colspan=3>{$strMoveTime}</td>
			<td class="codeLibPrintMiddle" style="width:110px;font-size:8px;" >{$strValueAssets}</td>
			<td class="codeLibPrintMiddle" style="width:190px;font-size:8px;">{$strName}</td>
			<td class="codeLibPrintMiddle" style="width:60px" rowspan=2 colspan=2>{$strTimeGetExt}</td>
		</tr>
		<tr valign="middle">
			<td class="codeLibPrintMiddle" colspan=3>{$strAddressAssets}</td>
			<td class="codeLibPrintMiddle" style="font-size:8px;" colspan=3>{$strMoveReason}</td>
			<td class="codeLibPrintMiddle" style="font-size:8px;" >{$strValueAssetsPrev}</td>
			<td class="codeLibPrintMiddle" style="font-size:8px;" >{$strAddress}</td>
		</tr>
	',
	'tmplPage' => '',
	'tmplRow' => array(
		'tmplTrTop' => '',
		'tmplTr1' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintLeft" >{$value.strTextType}</td>
				<td class="codeLibPrintLeft" >{$value.strTextUse}</td>
				<td class="codeLibPrintRight" >{$value.strTextSize}</td>
				<td class="codeLibPrintRight" rowspan=2>{$value.strTextValue}</td>

				<td class="codeLibPrintLeft" style="width:30px">{$value.strTextMoveYear}</td>
				<td class="codeLibPrintLeft" style="width:30px">{$value.strTextMoveMonth}</td>
				<td class="codeLibPrintLeft" style="width:30px">{$value.strTextMoveDate}</td>

				<td class="codeLibPrintRight" >{$value.strTextValueAssets}</td>
				<td class="codeLibPrintLeft" >{$value.strTextName}</td>
				<td class="codeLibPrintLeft" style="width:30px" rowspan=2>{$value.strTextGetYear}</td>
				<td class="codeLibPrintLeft" style="width:30px" rowspan=2>{$value.strTextGetMonth}</td>
			</tr>
		',
		'tmplTr2' => '
			<tr id="row#{idRow}_#{idTr2}" valign="top">
				<td class="codeLibPrintLeft" colspan=3>{$value.strTextAddressAssets}</td>
				<td class="codeLibPrintLeft" colspan=3>{$value.strTextMoveReason}</td>
				<td class="codeLibPrintRight" >{$value.strTextValueAssetsPrev}</td>
				<td class="codeLibPrintLeft" >{$value.strTextAddress}</td>
			</tr>
		',
		'tmplTrSum' => '',
		'tmplTrCautionLaw' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintRight">{$strCautionLaw}</td>
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
