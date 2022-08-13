<?php

$vars = array(
	'tmplWrap' => '
		<div id="idHeight">
			<div id="insertTable">
				<table cellspacing="1" cellpadding="3" border="0"  bgcolor="" width="750px" >
					<tbody>
						<tr>
							<td class="codeLibPrintRight" valign="bottom">#{numAllPage}</td>
						</tr>
					</tbody>
				</table>
				<table cellspacing="1" cellpadding="3" border="0"  bgcolor="" width="750px" >
					<tbody>
						<tr>
							<td class="codeLibPrintMiddle" valign="middle" colspan=2><span class="codeLibPrintTitle">{$strTitleSheet}</span></td>
						</tr>
						<tr>
							<td class="codeLibPrintMiddle" valign="middle" colspan=2>{$strPeriodExt}</td>
						</tr>
						<tr>
							<td class="codeLibPrintLeft">{$strEntity}({$strNum})</td>
							<td class="codeLibPrintRight">{$flagStatusFirst}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	',
	'tmplTableTop' => '
		<table cellspacing="1" cellpadding="3" border="0"  bgcolor="" width="750px" >
			<tbody>
				<tr>
					<td class="codeLibPrintLeft">{$strEntity}({$strNum})</td>
					<td class="codeLibPrintRight">#{flagStatus}</td>
				</tr>
			</tbody>
		</table>
	',
	'tmplTable' => '
		<table id="tableWrap#{id}" cellspacing="1" cellpadding="3" border="0" bgcolor="#222222" width="750px" style="margin-top:10px;">
			<tbody id="#{id}">

			</tbody>
		</table>
	',
	'tmplTableStatus' => '
		<table id="tableWrap#{id}" cellspacing="1" cellpadding="3" border="0" bgcolor="#222222" width="750px">
			<tbody id="#{id}">

			</tbody>
		</table>
	',
	'tmplColumn' => '
		<tr valign="top">
			<td class="codeLibPrintColumnCenter"  style="width:200px;">{$strDate}</td>
			<td class="codeLibPrintColumnCenter"  style="width:200px;">{$strMemo}</td>
			<td class="codeLibPrintColumnCenter"  style="width:116px;">{$numValue}</td>
			<td class="codeLibPrintColumnCenter"  style="width:116px;">{$strDepColumn}</td>
			<td class="codeLibPrintColumnCenter"  style="width:117px;">{$numValueNetClosingColumn}</td>
		</tr>
	',
	'tmplColumnStatus' => '

	',
	'tmplPage' => '
		<table cellspacing="1" cellpadding="3" border="0"  bgcolor="" width="750px" >
			<tbody>
				<tr>
					<td class="codeLibPrintRight" valign="bottom">#{numAllPage}</td>
				</tr>
			</tbody>
		</table>
	',
	'tmplRow' => array(
		'tmplTrTop' => '
			<tr valign="top"></tr>
		',
		'tmplTrStatus1' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintColumnCenter">{$id}</td>
				<td class="codeLibPrintRight">{$value.id}</td>
				<td class="codeLibPrintColumnCenter" style="width:100px;">{$flagDepUp}</td>
				<td class="codeLibPrintRight" style="width:150px;">{$value.flagDepUp}</td>
				<td class="codeLibPrintColumnCenter" style="width:100px;">{$numSurvivalRate}</td>
				<td class="codeLibPrintRight" style="width:150px;">{$value.numSurvivalRate}</td>
			</tr>
		',
		'tmplTrStatus2' => '
			<tr id="row#{idRow}_#{idTr2}" valign="top">
				<td class="codeLibPrintColumnCenter" style="width:100px;">{$strTitle}</td>
				<td class="codeLibPrintRight" style="width:150px;">{$value.strTitle}</td>
				<td class="codeLibPrintColumnCenter">{$flagDepDown}</td>
				<td class="codeLibPrintRight">{$value.flagDepDown}</td>
				<td class="codeLibPrintColumnCenter" style="font-size:10px;">{$numSurvivalRateLimit}</td>
				<td class="codeLibPrintRight">{$value.numSurvivalRateLimit}</td>
			</tr>
		',
		'tmplTrStatus3' => '
			<tr id="row#{idRow}_#{idTr3}" valign="top">
		<td class="codeLibPrintColumnCenter">{$idAccountTitle}</td>
				<td class="codeLibPrintRight">{$value.idAccountTitle}</td>
				<td class="codeLibPrintColumnCenter">{$stampBuy}</td>
				<td class="codeLibPrintRight">{$value.stampBuyExt}</td>
				<td class="codeLibPrintColumnCenter">{$numValueRemainingBook}</td>
				<td class="codeLibPrintRight">{$value.numValueRemainingBook}</td>
			</tr>
		',
		'tmplTrStatus4' => '
			<tr id="row#{idRow}_#{idTr4}" valign="top">
				<td class="codeLibPrintColumnCenter">{$flagDepMethod}</td>
				<td class="codeLibPrintRight">{$value.flagDepMethod}</td>
				<td class="codeLibPrintColumnCenter">{$stampStart}</td>
				<td class="codeLibPrintRight">{$value.stampStartExt}</td>
				<td class="codeLibPrintColumnCenter">{$arrCommaDepMonth}</td>
				<td class="codeLibPrintRight">{$value.arrCommaDepMonth}</td>
			</tr>
		',
		'tmplTrStatus5' => '
			<tr id="row#{idRow}_#{idTr5}" valign="top">
				<td class="codeLibPrintColumnCenter">{$numUsefulLife}</td>
				<td class="codeLibPrintRight">{$value.numUsefulLife}</td>
				<td class="codeLibPrintColumnCenter">{$stampDrop}</td>
				<td class="codeLibPrintRight">{$value.stampDropExt}</td>
				<td class="codeLibPrintColumnCenter" style="font-size:10px;">{$strNumRateDep}</td>
				<td class="codeLibPrintRight">{$value.numRateDep}</td>
			</tr>
		',
		'tmplTrStatus6' => '
			<tr id="row#{idRow}_#{idTr6}" valign="top">
				<td class="codeLibPrintColumnCenter">{$idDepartment}</td>
				<td class="codeLibPrintRight">{$value.idDepartment}</td>
				<td class="codeLibPrintColumnCenter">{$stampEnd}</td>
				<td class="codeLibPrintRight">{$value.stampEndExt}</td>
				<td class="codeLibPrintColumnCenter">{$numRatioOperate}</td>
				<td class="codeLibPrintRight">{$value.numRatioOperate}</td>
			</tr>
		',
		'tmplTrStatus7' => '
			<tr id="row#{idRow}_#{idTr7}" valign="top">
				<td class="codeLibPrintColumnCenter">{$numVolume}</td>
				<td class="codeLibPrintRight">{$value.numVolume}</td>
				<td class="codeLibPrintColumnCenter">{$numValue}</td>
				<td class="codeLibPrintRight">{$value.numValue}</td>
				<td class="codeLibPrintColumnCenter">{$strBlank}</td>
				<td class="codeLibPrintRight">{$strBlank}</td>
			</tr>
		',
		'tmplTrStatus8' => '
			<tr id="row#{idRow}_#{idTr8}" valign="top">
				<td class="codeLibPrintColumnCenter">{$flagTaxFixed}</td>
				<td class="codeLibPrintRight">{$value.flagTaxFixed} {$value.flagTaxFixedType}</td>
				<td class="codeLibPrintColumnCenter">{$numValueCompression}</td>
				<td class="codeLibPrintRight">{$value.numValueCompression}</td>
				<td class="codeLibPrintColumnCenter">{$strBlank}</td>
				<td class="codeLibPrintRight">{$strBlank}</td>
			</tr>
		',
		'tmplTrStatus9' => '
			<tr id="row#{idRow}_#{idTr9}" valign="top">
				<td class="codeLibPrintColumnCenter">{$strMemo}</td>
				<td class="codeLibPrintLeft" colspan=5>{$value.strMemo}</td>
			</tr>
		',
		'tmplTrSum1' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintColumnCenter" colspan=2>{$strSum}</td>
				<td class="codeLibPrintColumnRight" >{$value.numValue}</td>
				<td class="codeLibPrintColumnRight" >{$value.numValueAccumulatedClosing}</td>
				<td class="codeLibPrintColumnRight" >{$value.numValueNetClosing}</td>
			</tr>
		',
		'tmplTr1' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintLeft" >{$value.strDate}</td>
				<td class="codeLibPrintLeft" >{$value.strMemo}</td>
				<td class="codeLibPrintRight" >{$value.numValue}</td>
				<td class="codeLibPrintRight" >{$value.numValueDep}</td>
				<td class="codeLibPrintRight" >{$value.numValueNetClosing}</td>
			</tr>
		',
	),
);
