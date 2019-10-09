<?php

$vars = array(
	'tmplWrap' => '
		<div id="idHeight">
			<div id="insertTable">
				<table cellspacing="1" cellpadding="3" border="0"  bgcolor="" width="300px" >
					<tbody>
						<tr>
							<td class="codeLibPrintLeft codeLibPrintTitleBold">{$strTitle}</td>
							<td class="codeLibPrintRight"></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	',
	'tmplTable' => '
		<table id="tableWrap#{id}" cellspacing="1" cellpadding="3" border="0" bgcolor="#222222" width="300px" >
			<tbody id="#{id}">

			</tbody>
		</table>
	',
	'tmplTableTop' => '
		<table cellspacing="1" cellpadding="3" border="0"  bgcolor="" width="300px" >
			<tbody>
				<tr>
					<td class="codeLibPrintLeft codeLibPrintTitleBold">{$strTitle}</td>
					<td class="codeLibPrintRight"></td>
				</tr>
			</tbody>
		</table>
	',
	'tmplTableBottom' => '',
	'tmplTableBottomCaution' => '',
	'tmplColumn' => '
		<tr valign="top">
			<td class="codeLibPrintMiddle" style="width:90px;" colspan=3>{$strDecision}</td>
			<td class="codeLibPrintMiddle" style="width:210px;">{$strValue}</td>
		</tr>
	',
	'tmplPage' => '',
	'tmplRow' => array(
		'tmplTrTop' => '',
		'tmplTr1' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintLeft" style="width:30px;">{$value.strTextYear}</td>
				<td class="codeLibPrintLeft" style="width:30px;">{$value.strTextMonth}</td>
				<td class="codeLibPrintLeft" style="width:30px;">{$value.strTextDate}</td>
				<td class="codeLibPrintRight" >{$value.strTextValue}</td>
			</tr>
		',
		'tmplTrSum' => '',
		'tmplTrCautionLaw' => '',
		'tmplTrCaution' => '',
	),
);
