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
			<td class="codeLibPrintMiddle" style="width:90px;font-size:8px;" rowspan=3>{$strType}</td>
			<td class="codeLibPrintMiddle" style="width:120px;" colspan=2>{$strValueTitle}</td>
			<td class="codeLibPrintMiddle" style="width:430px;" colspan=6>{$strDetail}</td>
			<td class="codeLibPrintMiddle" style="width:80px;" rowspan=3style="width:80px;>{$strMemo}</td>
		</tr>
		<tr valign="middle">
			<td class="codeLibPrintMiddle" style="width:40px;" rowspan=2>{$strAmount}</td>
			<td class="codeLibPrintMiddle" style="width:80px;" rowspan=2>{$strValue}</td>

			<td class="codeLibPrintMiddle" style="width:130px;" colspan=3>{$strMoveTime}</td>
			<td class="codeLibPrintMiddle" style="width:40px;" rowspan=2>{$strMoveAmount}</td>

			<td class="codeLibPrintMiddle" style="width:80px;" rowspan=2>{$strValueMove}</td>
			<td class="codeLibPrintMiddle" style="width:180px;font-size:8px;">{$strName}</td>
		</tr>
		<tr valign="middle">
			<td class="codeLibPrintMiddle" colspan=3>{$strMoveReason}</td>
			<td class="codeLibPrintMiddle" style="font-size:8px;">{$strAddress}</td>
		</tr>
	',
	'tmplPage' => '',
	'tmplRow' => array(
		'tmplTrTop' => '',
		'tmplTr1' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintLeft"  rowspan=2>{$value.strTextType}</td>
				<td class="codeLibPrintLeft" rowspan=2>{$value.strTextAmount}</td>
				<td class="codeLibPrintRight" >{$value.strTextValue}</td>

				<td class="codeLibPrintLeft" style="width:50px;">{$value.strTextMoveYear}</td>
				<td class="codeLibPrintLeft" style="width:40px;">{$value.strTextMoveMonth}</td>
				<td class="codeLibPrintLeft" style="width:40px;">{$value.strTextMoveDate}</td>

				<td class="codeLibPrintLeft" rowspan=2>{$value.strTextMoveAmount}</td>

				<td class="codeLibPrintRight" rowspan=2>{$value.strTextValueMove}</td>

				<td class="codeLibPrintLeft" >{$value.strTextName}</td>
				<td class="codeLibPrintLeft" rowspan=2>{$value.strTextMemo}</td>
			</tr>
		',
		'tmplTr2' => '
			<tr id="row#{idRow}_#{idTr2}" valign="top">
				<td class="codeLibPrintRight" >{$value.strTextValueUpdate}</td>
				<td class="codeLibPrintLeft" colspan=3>{$value.strTextMoveReason}</td>
				<td class="codeLibPrintLeft" >{$value.strTextAddress}</td>
			</tr>
		',
		'tmplTrSum' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintMiddle" >{$strSum}</td>
				<td class="codeLibPrintLeft" ></td>
				<td class="codeLibPrintRight"  >{$value.strTextSumUpdate}</td>
				<td class="codeLibPrintLeft" colspan=3 ></td>
				<td class="codeLibPrintLeft" ></td>
				<td class="codeLibPrintRight" >{$value.strTextSumMove}</td>
				<td class="codeLibPrintLeft" ></td>
				<td class="codeLibPrintLeft" ></td>
			</tr>
		',
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
					{$strCaution3}<br>
					{$strCaution4}<br>
					{$strCaution5}
				</td>
			</tr>
		',
	),
);
