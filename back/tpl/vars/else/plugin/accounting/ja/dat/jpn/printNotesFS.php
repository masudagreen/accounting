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
							<td class="codeLibPrintMiddle" valign="middle" ><span class="codeLibPrintTitleNone">{$strTitle}</span></td>
						</tr>
						<tr>
							<td class="codeLibPrintMiddle" valign="middle" ><span>{$strPeriodSub}</span></td>
						</tr>
						<tr>
							<td class="codeLibPrintLeft">{$strTitleSub}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	',
	'tmplTable' => '
		<table id="tableWrap#{id}" cellspacing="1" cellpadding="3" border="0" width="750px" >
			<tbody id="#{id}">

			</tbody>
		</table>
	',
	'tmplColumn' => '',
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
		'tmplTrTop' => '',
		'tmplTr1' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintLeft" >{$value.strComment}</td>
			</tr>
		',
	),
);
