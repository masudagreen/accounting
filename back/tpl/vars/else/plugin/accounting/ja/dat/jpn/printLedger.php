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
							<td class="codeLibPrintMiddle" valign="middle" colspan=2><span class="codeLibPrintTitle">{$strTitle}</span></td>
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
					<td class="codeLibPrintMiddle" valign="middle" colspan=2><span class="codeLibPrintTitle">#{strTitle}</span></td>
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
			<td class="codeLibPrintColumnCenter"  style="width:40px;">{$strDate}</td>
			<td class="codeLibPrintColumnCenter"  style="width:145px;">{$strContra}{$strAccountTitleColumn}</td>
			<td class="codeLibPrintColumnCenter"  style="width:145px;">{$strBlank}</td>
			<td class="codeLibPrintColumnCenter"  style="width:150px;" rowspan=3 valign="middle">{$strMemo}</td>
			<td class="codeLibPrintColumnCenter"  style="width:100px;" rowspan=3 valign="middle">{$strDebit}</td>
			<td class="codeLibPrintColumnCenter"  style="width:100px;" rowspan=3 valign="middle">{$strCredit}</td>
			<td class="codeLibPrintColumnCenter"  style="width:100px;" rowspan=3 valign="middle">{$strBalance}</td>
		</tr>
		<tr valign="top">
			<td class="codeLibPrintColumnCenter" >{$strFiscalReport}</td>
			<td class="codeLibPrintColumnCenter" >{$strContra}{$strDepartmentColumn}</td>
			<td class="codeLibPrintColumnCenter" >{$strDepartmentColumn}</td>
		</tr>
		<tr valign="top">
			<td class="codeLibPrintColumnCenter" >{$strId}</td>
			<td class="codeLibPrintColumnCenter" >{$strContra}{$strSubAccountTitleColumn}</td>
			<td class="codeLibPrintColumnCenter" >{$strSubAccountTitleColumn}</td>
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
		'tmplTrPrev1' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintColumnCenter" >{$value.strDate}</td>
				<td class="codeLibPrintColumnRight" >{$strBlank}</td>
				<td class="codeLibPrintColumnRight" >{$strBlank}</td>
				<td class="codeLibPrintColumnRight" >{$value.strPrevTerm}</td>
				<td class="codeLibPrintColumnRight" >{$strBlank}</td>
				<td class="codeLibPrintColumnRight" >{$strBlank}</td>
				<td class="codeLibPrintColumnRight" >{$value.numBalance}</td>
			</tr>
		',
		'tmplTrNext1' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintColumnCenter" >{$value.strDate}</td>
				<td class="codeLibPrintColumnRight" >{$strBlank}</td>
				<td class="codeLibPrintColumnRight" >{$strBlank}</td>
				<td class="codeLibPrintColumnRight" >{$value.strNextTerm}</td>
				<td class="codeLibPrintColumnRight" >{$strBlank}</td>
				<td class="codeLibPrintColumnRight" >{$strBlank}</td>
				<td class="codeLibPrintColumnRight" >{$value.numBalance}</td>
			</tr>
		',
		'tmplTr1' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintMiddle" >{$value.strDate}</td>
				<td class="codeLibPrintLeft" >{$value.idAccountTitleContra}</td>
				<td class="codeLibPrintLeft" >{$strBlank}</td>
				<td class="codeLibPrintLeft"  rowspan=3>{$value.strMemo}</td>
				<td class="codeLibPrintRight" rowspan=3 valign="bottom">{$value.flagDebit}</td>
				<td class="codeLibPrintRight" rowspan=3 valign="bottom">{$value.flagCredit}</td>
				<td class="codeLibPrintRight" rowspan=3 valign="bottom">{$value.numBalance}</td>
			</tr>
		',
		'tmplTr2' => '
			<tr id="row#{idRow}_#{idTr2}" valign="top">
				<td class="codeLibPrintMiddle" >{$value.flagFiscalReportCut}</td>
				<td class="codeLibPrintLeft" >{$value.idDepartmentContra}</td>
				<td class="codeLibPrintLeft" >{$value.idDepartment}</td>
			</tr>
		',
		'tmplTr3' => '
			<tr id="row#{idRow}_#{idTr3}" valign="top">
				<td class="codeLibPrintMiddle" >{$value.idLog}</td>
				<td class="codeLibPrintLeft" >{$value.idSubAccountTitleContra}</td>
				<td class="codeLibPrintLeft" >{$value.idSubAccountTitle}</td>
			</tr>
		',
	),
);
