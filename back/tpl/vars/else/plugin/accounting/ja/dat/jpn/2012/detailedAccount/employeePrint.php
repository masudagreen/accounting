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
			<td class="codeLibPrintMiddle" colspan=11><p style="margin:10px;">{$strTitleEmployee}</p></td>
		</tr>
		<tr valign="middle">
			<td class="codeLibPrintMiddle" style="width:70px;" rowspan=3>{$strPositionTop}<br><span style="font-size:8px">{$strPositionBottom}</span></td>
			<td class="codeLibPrintMiddle" style="width:175px;" rowspan=2>{$strName}</td>
			<td class="codeLibPrintMiddle" style="width:60px;font-size:8px;" rowspan=2>{$strRelationTop}<br>{$strRelationBottom}</td>
			<td class="codeLibPrintMiddle" style="width:40px;font-size:8px;" rowspan=3>{$strType}</td>
			<td class="codeLibPrintMiddle" style="width:55px;" rowspan=3>{$strValueRewardSumTop}<br>{$strValueRewardSumBottom}</td>
			<td class="codeLibPrintMiddle" style="width:275px;" colspan=5>{$strDetail}</td>
			<td class="codeLibPrintMiddle" style="width:55px;" rowspan=3>{$strValue}</td>
		</tr>
		<tr valign="middle">
			<td class="codeLibPrintMiddle" style="width:55px;" rowspan=2>{$strValueEmployeeTop}<br>{$strValueEmployeeBottom}</td>
			<td class="codeLibPrintMiddle" style="width:220px;" colspan=4>{$strEmployeeElse}</td>
		</tr>
		<tr valign="middle">
			<td class="codeLibPrintMiddle" colspan=2>{$strAddress}</td>
			<td class="codeLibPrintMiddle" style="width:55px;">{$strValueEmployeeRegularTop}<br>{$strValueEmployeeRegularBottom}</td>
			<td class="codeLibPrintMiddle" style="width:55px;">{$strValueEmployeePrevTop}<br>{$strValueEmployeePrevBottom}</td>
			<td class="codeLibPrintMiddle" style="width:55px;">{$strValueEmployeeProfitTop}<br>{$strValueEmployeeProfitBottom}</td>
			<td class="codeLibPrintMiddle" style="width:55px;">{$strValueOthers}</td>
		</tr>
	',
	'tmplPage' => '',
	'tmplRow' => array(
		'tmplTrTop' => '',
		'tmplTr1' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintLeft" rowspan=2>{$value.strTextPosition}</td>
				<td class="codeLibPrintLeft" >{$value.strTextName}</td>
				<td class="codeLibPrintLeft" >{$value.strTextRelation}</td>
				<td class="codeLibPrintMiddle">{$value.strSelectType1}</td>
				<td class="codeLibPrintRight" style="font-size:8px;" rowspan=2>{$value.strTextValueRewardSum}</td>
				<td class="codeLibPrintRight" style="font-size:8px;" rowspan=2>{$value.strTextValueEmployee}</td>
				<td class="codeLibPrintRight" style="font-size:8px;" rowspan=2>{$value.strTextValueEmployeeRegular}</td>
				<td class="codeLibPrintRight" style="font-size:8px;" rowspan=2>{$value.strTextValueEmployeePrev}</td>
				<td class="codeLibPrintRight" style="font-size:8px;" rowspan=2>{$value.strTextValueEmployeeProfit}</td>
				<td class="codeLibPrintRight" style="font-size:8px;" rowspan=2>{$value.strTextValueOthers}</td>
				<td class="codeLibPrintRight" style="font-size:8px;" rowspan=2>{$value.strTextValue}</td>
			</tr>
		',
		'tmplTr2' => '
			<tr id="row#{idRow}_#{idTr2}" valign="top">
				<td class="codeLibPrintLeft" colspan=2>{$value.strTextAddress}</td>
				<td class="codeLibPrintMiddle">{$value.strSelectType2}</td>
			</tr>
		',
		'tmplTrSum' => '
			<tr id="row#{idRow}_#{idTr1}" valign="middle">
				<td class="codeLibPrintMiddle" >{$strSum}</td>
				<td class="codeLibPrintMiddle" colspan=2></td>
				<td class="codeLibPrintMiddle" ></td>
				<td class="codeLibPrintRight" style="font-size:8px;">{$value.strTextSumRewardSum}</td>
				<td class="codeLibPrintRight" style="font-size:8px;">{$value.strTextSumEmployee}</td>
				<td class="codeLibPrintRight" style="font-size:8px;">{$value.strTextSumEmployeeRegular}</td>
				<td class="codeLibPrintRight" style="font-size:8px;">{$value.strTextSumEmployeePrev}</td>
				<td class="codeLibPrintRight" style="font-size:8px;">{$value.strTextSumEmployeeProfit}</td>
				<td class="codeLibPrintRight" style="font-size:8px;">{$value.strTextSumOthers}</td>
				<td class="codeLibPrintRight" style="font-size:8px;">{$value.strTextSum}</td>
			</tr>
		',
		'tmplTrBottom1' => '
			<tr id="row#{idRow}_#{idTr1}" valign="middle">
				<td class="codeLibPrintMiddle" colspan=11><p style="margin:10px;">{$strTitleLaborCost}</p></td>
			</tr>
		',
		'tmplTrBottom2' => '
			<tr id="row#{idRow}_#{idTr2}" valign="middle">
				<td class="codeLibPrintMiddle" colspan=3>{$strCategory}</td>
				<td class="codeLibPrintMiddle" colspan=4>{$strValueAll}</td>
				<td class="codeLibPrintMiddle" colspan=4>{$strValueAllElse}</td>
			</tr>
		',
		'tmplTrBottom3' => '
			<tr id="row#{idRow}_#{idTr3}" valign="middle">
				<td class="codeLibPrintMiddle" colspan=3>{$strDirectors}</td>
				<td class="codeLibPrintRight" colspan=4>{$value.strTextValueAll1}</td>
				<td class="codeLibPrintRight" colspan=4>{$value.strTextValueAllElse1}</td>
			</tr>
		',
		'tmplTrBottom4' => '
			<tr id="row#{idRow}_#{idTr4}" valign="middle">
				<td class="codeLibPrintMiddle" rowspan=2>{$strLabor}</td>
				<td class="codeLibPrintMiddle" colspan=2>{$strLaborValue}</td>
				<td class="codeLibPrintRight" colspan=4>{$value.strTextValueAll2}</td>
				<td class="codeLibPrintRight" colspan=4>{$value.strTextValueAllElse2}</td>
			</tr>
		',
		'tmplTrBottom5' => '
			<tr id="row#{idRow}_#{idTr5}" valign="middle">
				<td class="codeLibPrintMiddle" colspan=2>{$strWagesValue}</td>
				<td class="codeLibPrintRight" colspan=4>{$value.strTextValueAll3}</td>
				<td class="codeLibPrintRight" colspan=4>{$value.strTextValueAllElse3}</td>
			</tr>
		',
		'tmplTrBottom6' => '
			<tr id="row#{idRow}_#{idTr6}" valign="middle">
				<td class="codeLibPrintMiddle" colspan=3>{$strSum}</td>
				<td class="codeLibPrintRight" colspan=4>{$value.strTextSumAll}</td>
				<td class="codeLibPrintRight" colspan=4>{$value.strTextSumAllElse}</td>
			</tr>
		',
		'tmplTrCautionLaw' => '
			<tr id="row#{idRow}_#{idTr1}" valign="top">
				<td class="codeLibPrintRight" colspan=2>{$strCautionLaw}</td>
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
					{$strCaution5}<br>
					{$strCaution6}<br>
					{$strCaution7}
				</td>
			</tr>
		',
	),
);
