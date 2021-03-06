<?php

$vars = array(
	'tmplWrap' => '
		<div id="idHeight">
			<div id="insertTable">
				<table cellspacing="1" cellpadding="3" border="0"  bgcolor="" width="750px" >
					<tbody>
						<tr>
							<td class="codeLibPrintRight" valign="bottom">#{numPage}</td>
						</tr>
					</tbody>
				</table>
				<table cellspacing="1" cellpadding="3" border="0"  bgcolor="" width="750px" >
					<tbody>
						<tr>
							<td class="codeLibPrintMiddle" valign="middle" colspan=2><span class="codeLibPrintTitleNone">{$strTitle}</span></td>
						</tr>
						<tr>
							<td class="codeLibPrintMiddle" valign="middle" colspan=2><span>{$strPeriodSub}</span></td>
						</tr>
						<tr>
							<td class="codeLibPrintLeft">{$strTitleSub}</td>
							<td class="codeLibPrintRight">{$strUnit}</td>
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
					<td class="codeLibPrintMiddle" valign="middle" colspan=2><span class="codeLibPrintTitleNone">#{strTitle}</span></td>
				</tr>
					<tr>
						<td class="codeLibPrintMiddle" valign="middle" colspan=2><span>#{strPeriodSub}</span></td>
					</tr>
				<tr>
					<td class="codeLibPrintLeft">#{strTitleSub}</td>
					<td class="codeLibPrintRight">#{strUnit}</td>
				</tr>
			</tbody>
		</table>
	',
	'tmplColumn' => '
		<tr valign="top">
			<td class="codeLibPrintColumnCenter"  style="width:350px;">{$strAccountTitle}</td>
			<td class="codeLibPrintColumnCenter"  style="width:200px;">{$strStart}</td>
			<td class="codeLibPrintColumnCenter"  style="width:200px;">{$strEnd}</td>
		</tr>
	',
	'tmplPage' => '
		<table cellspacing="1" cellpadding="3" border="0"  bgcolor="" width="750px" >
			<tbody>
				<tr>
					<td class="codeLibPrintRight" valign="bottom">#{numPage}</td>
				</tr>
			</tbody>
		</table>
	',
	'tmplRow' => array(
		'tmplTrTop' => '
			<tr valign="top"></tr>
		',
		'tmplTr1' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintLeft" >{$value.strTitle}</td>
				<td class="codeLibPrintRight" >{$value.sumPrev}</td>
				<td class="codeLibPrintRight" >{$value.sumNext}</td>
			</tr>
		',
	),
);
