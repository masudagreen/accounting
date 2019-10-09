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
			<td class="codeLibPrintMiddle" style="width:60px" rowspan=2>{$strCategory}</td>
			<td class="codeLibPrintMiddle" style="width:160px" colspan=2>{$strAddressGoods}</td>
			<td class="codeLibPrintMiddle" style="width:60px" colspan=2 rowspan=2>{$strSalesExt}</td>
			<td class="codeLibPrintMiddle" style="width:40px" rowspan=2>{$strAboutExt}</td>
			<td class="codeLibPrintMiddle" style="width:190px" >{$strName}</td>
			<td class="codeLibPrintMiddle" style="width:60px" rowspan=2>{$strSizeExt}</td>
			<td class="codeLibPrintMiddle" style="width:120px" >{$strValue}</td>
			<td class="codeLibPrintMiddle" style="width:60px" rowspan=2>{$strYearGetExt}</td>
		</tr>
		<tr valign="middle">
			<td class="codeLibPrintMiddle" style="width:80px">{$strType}</td>
			<td class="codeLibPrintMiddle" style="width:80px">{$strAllSize}</td>
			<td class="codeLibPrintMiddle" >{$strAddress}</td>
			<td class="codeLibPrintMiddle" >{$strValueMargin}</td>
		</tr>
	',
	'tmplPage' => '',
	'tmplRow' => array(
		'tmplTrTop' => '',
		'tmplTr1' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintLeft" rowspan=2>{$value.strTextCategory}</td>
				<td class="codeLibPrintLeft" colspan=2>{$value.strTextAddressGoods}</td>
				<td class="codeLibPrintLeft" style="width:30px" rowspan=2>{$value.strTextYear}</td>
				<td class="codeLibPrintLeft" style="width:30px" rowspan=2>{$value.strTextMonth}</td>
				<td class="codeLibPrintLeft" colspan=2>{$value.strTextName}</td>
				<td class="codeLibPrintLeft" rowspan=2>{$value.strTextSize}</td>
				<td class="codeLibPrintRight" >{$value.strTextValue}</td>
				<td class="codeLibPrintLeft" rowspan=2>{$value.strTextYearGet}</td>
			</tr>
		',
		'tmplTr2' => '
			<tr id="row#{idRow}_#{idTr2}" valign="top">
				<td class="codeLibPrintLeft" >{$value.strTextType}</td>
				<td class="codeLibPrintRight" >{$value.strTextAllSize}</td>
				<td class="codeLibPrintLeft" colspan=2>{$value.strTextAddress}</td>
				<td class="codeLibPrintRight" >{$value.strTextValueMargin}</td>
			</tr>
		',
		'tmplTrSum1' => '
			<tr id="row#{idRow}_#{idTr1}" valign="middle">
				<td class="codeLibPrintMiddle" rowspan=2>{$strSum}</td>
				<td class="codeLibPrintMiddle" rowspan=2 colspan=2></td>
				<td class="codeLibPrintMiddle" rowspan=2></td>
				<td class="codeLibPrintMiddle" rowspan=2 ></td>
				<td class="codeLibPrintMiddle" rowspan=2 colspan=2></td>
				<td class="codeLibPrintMiddle" rowspan=2></td>
				<td class="codeLibPrintRight" >{$value.strTextSum}</td>
				<td class="codeLibPrintMiddle" rowspan=2></td>
			</tr>
		',
		'tmplTrSum2' => '
			<tr id="row#{idRow}_#{idTr2}" valign="middle">
				<td class="codeLibPrintRight" >{$value.strTextSumMargin}</td>
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
					{$strCaution4}
				</td>
			</tr>
		',
	),
);
