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
	'tmplTableBottom2' => '
		<table id="tableWrap#{id}" cellspacing="1" cellpadding="3" border="0" bgcolor="#fff" width="750px" >
			<tbody id="#{id}">
			</tbody>
		</table>
	',
	'tmplColumn' => '
		<tr valign="middle">
			<td class="codeLibPrintMiddle" style="width:110px;">{$strAccountTitle}</td>
			<td class="codeLibPrintMiddle" style="width:170px;">{$strType}</td>
			<td class="codeLibPrintMiddle" style="width:90px;">{$strNum}</td>
			<td class="codeLibPrintMiddle" style="width:90px;">{$strUnitTitle}</td>
			<td class="codeLibPrintMiddle" style="width:145px;">{$strValue}</td>
			<td class="codeLibPrintMiddle" style="width:145px;">{$strMemo}</td>
		</tr>
	',
	'tmplPage' => '',
	'tmplRow' => array(
		'tmplTrTop' => '',
		'tmplTr1' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintLeft">{$value.strTextAccountTitle}</td>
				<td class="codeLibPrintLeft" >{$value.strTextType}</td>
				<td class="codeLibPrintLeft" >{$value.strTextNum}</td>
				<td class="codeLibPrintLeft" >{$value.strTextUnit}</td>
				<td class="codeLibPrintRight" >{$value.strTextValue}</td>
				<td class="codeLibPrintLeft" >{$value.strTextMemo}</td>
			</tr>
		',
		'tmplTrSum' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintMiddle">{$strSum}</td>
				<td class="codeLibPrintLeft"></td>
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
		'tmplTrBottom' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintRight" style="width:10px;">{$strCautionMark}</td>
				<td class="codeLibPrintLeft" style="width:740px;">
					{$strCaution1}<br>
					<table style="margin-left:30px;margin-top:10px;margin-bottom:10px;" cellspacing="1" cellpadding="3" border="0" bgcolor="#222222" width="330px">
						<tbody>
							<tr valign="middle">
								<td class="codeLibPrintLeft" style="width:165px;" rowspan=2>
									<p>{$value.strSelectA}</p>
									<p>{$value.strSelectB}</p>
									<p>{$value.strSelectC}</p>
								</td>
								<td class="codeLibPrintMiddle" style="width:165px;">{$strTime}</td>
							</tr>
							<tr valign="middle">
								<td class="codeLibPrintMiddle">{$value.strTextTime}</td>
							</tr>
						</tbody>
					</table>
					{$strCaution2}<br>
					{$strCaution3}<br>
					{$strCaution4}
				</td>
			</tr>
		',
	),
);
