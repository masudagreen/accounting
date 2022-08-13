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
							<td class="codeLibPrintLeft">{$strEntity}({$strNum})</td>
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
	'tmplColumn' => '
		<tr valign="top">
			<td class="codeLibPrintColumnCenter"  style="width:40px;">{$strDate}</td>
			<td class="codeLibPrintColumnCenter"  style="width:150px;">{$strDebit}{$strAccountTitle}</td>
			<td class="codeLibPrintColumnCenter"  style="width:100px;">{$strDebit}{$strValue}</td>
			<td class="codeLibPrintColumnCenter"  style="width:150px;">{$strCredit}{$strAccountTitle}</td>
			<td class="codeLibPrintColumnCenter"  style="width:100px;">{$strCredit}{$strValue}</td>
			<td class="codeLibPrintColumnCenter"  style="width:150px;" rowspan=4 valign="middle">{$strMemo}</td>
			<td class="codeLibPrintColumnCenter"  style="width:60px;">{$strStatus}</td>
		</tr>
		<tr valign="top">
			<td class="codeLibPrintColumnCenter" >{$strFiscalReport}</td>
			<td class="codeLibPrintColumnCenter" >{$strDebit}{$strNumRateConsumptionTax}</td>
			<td class="codeLibPrintColumnCenter" >{$strDebit}{$strNumValueConsumptionTax}</td>
			<td class="codeLibPrintColumnCenter" >{$strCredit}{$strNumRateConsumptionTax}</td>
			<td class="codeLibPrintColumnCenter" >{$strCredit}{$strNumValueConsumptionTax}</td>
			<td class="codeLibPrintColumnCenter" >{$strId}</td>
		</tr>
		<tr valign="top">
			<td class="codeLibPrintColumnCenter" ></td>
			<td class="codeLibPrintColumnCenter" >{$strDebit}{$strConsumptionTax}</td>
			<td class="codeLibPrintColumnCenter" >{$strDebit}{$strConsumptionTaxCalc}</td>
			<td class="codeLibPrintColumnCenter" >{$strCredit}{$strConsumptionTax}</td>
			<td class="codeLibPrintColumnCenter" >{$strCredit}{$strConsumptionTaxCalc}</td>
			<td class="codeLibPrintColumnCenter" >{$strIdCharge}</td>
		</tr>
		<tr valign="top">
			<td class="codeLibPrintColumnCenter" ></td>
			<td class="codeLibPrintColumnCenter" >{$strDebit}{$strSubAccountTitle}</td>
			<td class="codeLibPrintColumnCenter" >{$strDebit}{$strDepartment}</td>
			<td class="codeLibPrintColumnCenter" >{$strCredit}{$strSubAccountTitle}</td>
			<td class="codeLibPrintColumnCenter" >{$strCredit}{$strDepartment}</td>
			<td class="codeLibPrintColumnCenter" >{$strIdFile}</td>
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
		'tmplTrSum1' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintColumnCenter" >{$strSumCalc}</td>
				<td class="codeLibPrintColumnRight" >{$strDebit}{$strSum}</td>
				<td class="codeLibPrintColumnRight" >{$value.strSumDebit}</td>
				<td class="codeLibPrintColumnRight" >{$strCredit}{$strSum}</td>
				<td class="codeLibPrintColumnRight" >{$value.strSumCredit}</td>
				<td class="codeLibPrintColumnRight" >{$strNumAllLog}</td>
				<td class="codeLibPrintColumnRight" >{$value.strNumAllLog}</td>
			</tr>
		',
		'tmplTrSum2' => '
			<tr id="row#{idRow}_#{idTr2}" valign="top">
				<td class="codeLibPrintColumnCenter"  rowspan=2>{$strSumDetail}</td>
				<td class="codeLibPrintColumnRight" >{$strDebit}{$strSumIn}</td>
				<td class="codeLibPrintColumnRight" >{$value.strSumInDebit}</td>
				<td class="codeLibPrintColumnRight" >{$strCredit}{$strSumIn}</td>
				<td class="codeLibPrintColumnRight" >{$value.strSumInCredit}</td>
				<td class="codeLibPrintColumnRight" >{$strNumAllLogReport1}</td>
				<td class="codeLibPrintColumnRight" >{$value.strNumAllLogReport1}</td>
			</tr>
		',
		'tmplTrSum3' => '
			<tr id="row#{idRow}_#{idTr3}" valign="top">
				<td class="codeLibPrintColumnRight" >{$strDebit}{$strSumOut}</td>
				<td class="codeLibPrintColumnRight" >{$value.strSumOutDebit}</td>
				<td class="codeLibPrintColumnRight" >{$strCredit}{$strSumOut}</td>
				<td class="codeLibPrintColumnRight" >{$value.strSumOutCredit}</td>
				<td class="codeLibPrintColumnRight" >{$strNumAllLogReport2}</td>
				<td class="codeLibPrintColumnRight" >{$value.strNumAllLogReport2}</td>
			</tr>
		',
		'tmplTr1' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintMiddle" >{$value.strDate}</td>
				<td class="codeLibPrintLeft" >{$value.strAccountTitleDebit}</td>
				<td class="codeLibPrintRight" >{$value.strValueDebit}</td>
				<td class="codeLibPrintLeft" >{$value.strAccountTitleCredit}</td>
				<td class="codeLibPrintRight" >{$value.strValueCredit}</td>
				<td class="codeLibPrintLeft"  rowspan=4>{$value.strMemo}</td>
				<td class="codeLibPrintMiddle" >{$value.strStatus}</td>
			</tr>
		',
		'tmplTr2' => '
			<tr id="row#{idRow}_#{idTr2}" valign="top">
				<td class="codeLibPrintMiddle" >{$value.strFiscalReport}</td>
				<td class="codeLibPrintLeft" >{$value.strNumRateConsumptionTaxDebit}</td>
				<td class="codeLibPrintRight" >{$value.strNumValueConsumptionTaxDebit}</td>
				<td class="codeLibPrintLeft" >{$value.strNumRateConsumptionTaxCredit}</td>
				<td class="codeLibPrintRight" >{$value.strNumValueConsumptionTaxCredit}</td>
				<td class="codeLibPrintMiddle" >{$value.strId}</td>
			</tr>
		',
		'tmplTr3' => '
			<tr id="row#{idRow}_#{idTr3}" valign="top">
				<td class="codeLibPrintMiddle" ></td>
				<td class="codeLibPrintLeft" >{$value.strConsumptionTaxDebit}</td>
				<td class="codeLibPrintLeft" >{$value.strConsumptionTaxCalcDebit}</td>
				<td class="codeLibPrintLeft" >{$value.strConsumptionTaxCredit}</td>
				<td class="codeLibPrintLeft" >{$value.strConsumptionTaxCalcCredit}</td>
				<td class="codeLibPrintMiddle" >{$value.strIdCharge}</td>
			</tr>
		',
		'tmplTr4' => '
			<tr id="row#{idRow}_#{idTr4}" valign="top">
				<td class="codeLibPrintMiddle" ></td>
				<td class="codeLibPrintLeft" >{$value.strSubAccountTitleDebit}</td>
				<td class="codeLibPrintLeft" >{$value.strDepartmentDebit}</td>
				<td class="codeLibPrintLeft" >{$value.strSubAccountTitleCredit}</td>
				<td class="codeLibPrintLeft" >{$value.strDepartmentCredit}</td>
				<td class="codeLibPrintMiddle" >{$value.strIdFile}</td>
			</tr>
		',
	),
);
