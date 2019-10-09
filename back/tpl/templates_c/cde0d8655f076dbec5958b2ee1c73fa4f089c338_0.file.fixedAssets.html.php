<?php /* Smarty version 3.1.24, created on 2019-07-06 10:40:42
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/html/fixedAssets.html" */ ?>
<?php
/*%%SmartyHeaderCode:17448927495d207aaa4d65c7_21258397%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cde0d8655f076dbec5958b2ee1c73fa4f089c338' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/html/fixedAssets.html',
      1 => 1560675141,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '17448927495d207aaa4d65c7_21258397',
  'variables' => 
  array (
    'sumBasic' => 0,
    'strBasic' => 0,
    'strIdAccountTitle' => 0,
    'strFlagDepMethod' => 0,
    'strNumUsefulLife' => 0,
    'strNumVolume' => 0,
    'strFlagDepUnit' => 0,
    'flagIdDepartment' => 0,
    'strIdDepartment' => 0,
    'strTax' => 0,
    'strFlagTaxFixed' => 0,
    'strFlagTaxFixedType' => 0,
    'strTaxReason' => 0,
    'strFlagDepUp' => 0,
    'strFlagDepDown' => 0,
    'strTime' => 0,
    'strStampBuy' => 0,
    'strStampStart' => 0,
    'strStampDrop' => 0,
    'strStampEnd' => 0,
    'strValue' => 0,
    'strNumValue' => 0,
    'strNumValueCompression' => 0,
    'strNumValueNet' => 0,
    'strNumSurvivalRate' => 0,
    'strNumSurvivalRateLimit' => 0,
    'strNumValueRemainingBook' => 0,
    'strNumValueAccumulated' => 0,
    'strNumValueNetOpening' => 0,
    'sumCurrentDep' => 0,
    'strCurrentDep' => 0,
    'strNumValueDepCalcBase' => 0,
    'flagNumValueDepPrevOver' => 0,
    'strNumValueDepPrevOver' => 0,
    'strNumValueDepCalc' => 0,
    'strArrCommaDepMonth' => 0,
    'strNumRateDep' => 0,
    'strNumValueAssured' => 0,
    'strNumValueDepUp' => 0,
    'strNumValueDepExtra' => 0,
    'strNumValueDepSpecial' => 0,
    'flagNumValueDepSpecialShortPrev' => 0,
    'strNumValueDepSpecialShortPrev' => 0,
    'strNumValueDepLimit' => 0,
    'strNumValueDep' => 0,
    'strNumValueAccumulatedClosing' => 0,
    'strNumValueNetClosing' => 0,
    'flagNumRatioOperate' => 0,
    'strNumRatioOperate' => 0,
    'flagNumValueDepOperate' => 0,
    'strNumValueDepOperate' => 0,
    'strDepLawOver' => 0,
    'strNumValueDepPrevOverData' => 0,
    'strNumValueDepCurrentOver' => 0,
    'strNumValueDepNextOver' => 0,
    'flagNumValueDepSpecialShortCurrent' => 0,
    'strSpecialDepLawShort' => 0,
    'strNumValueDepSpecialShortPrevData' => 0,
    'strNumValueDepSpecialShortCurrent' => 0,
    'strNumValueDepSpecialShortCurrentCut' => 0,
    'strNumValueDepSpecialShortNext' => 0,
    'sumWrite' => 0,
    'strWrite' => 0,
    'strLossOnDisposalOfFixedAssets' => 0,
    'strAccumulatedDepreciation' => 0,
    'strSellingAdminCost' => 0,
    'flagProductsCost' => 0,
    'strProductsCost' => 0,
    'flagNonOperatingExpenses' => 0,
    'strNonOperatingExpenses' => 0,
    'flagAgricultureCost' => 0,
    'strAgricultureCost' => 0,
    'sumRatio' => 0,
    'strRatio' => 0,
    'strNumRatioSellingAdminCost' => 0,
    'flagNumRatioProductsCost' => 0,
    'strNumRatioProductsCost' => 0,
    'flagNumRatioNonOperatingExpenses' => 0,
    'strNumRatioNonOperatingExpenses' => 0,
    'flagNumRatioAgricultureCost' => 0,
    'strNumRatioAgricultureCost' => 0,
    'strFlagFraction' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d207aaa7e4a62_67896954',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d207aaa7e4a62_67896954')) {
function content_5d207aaa7e4a62_67896954 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '17448927495d207aaa4d65c7_21258397';
?>
<table id="#{idSelf}TableWrap" cellspacing="1" cellpadding="3" border="0" bgcolor="#cccccc" width="100%" style="font-size:10px;">
	<tbody>
		<tr id="#{idSelf}IdAccountTitleWrap">
			<td class="codeLibBaseTableColumn" style="width:70px;" rowspan="<?php echo $_smarty_tpl->tpl_vars['sumBasic']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['strBasic']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}IdAccountTitleStrTitle" style="width:150px;" ><?php echo $_smarty_tpl->tpl_vars['strIdAccountTitle']->value;?>
 #{strMust}</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}IdAccountTitleOffset"><div id="#{idSelf}IdAccountTitle" style="height:16px;text-align:right;">#{IdAccountTitle}</div></td>
		</tr>
		<tr id="#{idSelf}FlagDepMethodWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}FlagDepMethodStrTitle"><?php echo $_smarty_tpl->tpl_vars['strFlagDepMethod']->value;?>
 #{strMust}</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}FlagDepMethodOffset"><div id="#{idSelf}FlagDepMethod" style="height:16px;text-align:right;">#{FlagDepMethod}</div></td>
		</tr>
		<tr id="#{idSelf}NumUsefulLifeWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}NumUsefulLifeStrTitle"><?php echo $_smarty_tpl->tpl_vars['strNumUsefulLife']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}NumUsefulLifeOffset"><div id="#{idSelf}NumUsefulLife" style="height:16px;text-align:right;">#{NumUsefulLife}</div></td>
		</tr>
		<tr id="#{idSelf}NumVolumeWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}NumVolumeStrTitle"><?php echo $_smarty_tpl->tpl_vars['strNumVolume']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}NumVolumeOffset"><div id="#{idSelf}NumVolume" style="height:16px;text-align:right;">#{NumVolume}</div></td>
		</tr>
		<tr id="#{idSelf}FlagDepUnitWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}FlagDepUnitStrTitle"><?php echo $_smarty_tpl->tpl_vars['strFlagDepUnit']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}FlagDepUnitOffset"><div id="#{idSelf}FlagDepUnit" style="height:16px;text-align:right;">#{FlagDepUnit}</div></td>
		</tr>
		<?php if ($_smarty_tpl->tpl_vars['flagIdDepartment']->value) {?>
			<tr id="#{idSelf}IdDepartmentWrap">
				<td class="codeLibBaseTableRow" id="#{idSelf}IdDepartmentStrTitle"><?php echo $_smarty_tpl->tpl_vars['strIdDepartment']->value;?>
</td>
				<td class="codeLibBaseTableRow" id="#{idSelf}IdDepartmentOffset"><div id="#{idSelf}IdDepartment" style="height:16px;text-align:right;">#{IdDepartment}</div></td>
			</tr>
		<?php }?>
		<tr id="#{idSelf}IdAccountTitleError" style="display:none;">
			<td class="codeLibBaseTableError codeLibBaseFontRed codeLibBaseFontSizeSeventy" colspan="3"></td>
		</tr>


		<tr id="#{idSelf}FlagTaxFixedWrap">
			<td class="codeLibBaseTableColumn" style="width:70px;" rowspan="2"><?php echo $_smarty_tpl->tpl_vars['strTax']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}FlagTaxFixedStrTitle" style="width:150px;" ><?php echo $_smarty_tpl->tpl_vars['strFlagTaxFixed']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}FlagTaxFixedOffset"><div id="#{idSelf}FlagTaxFixed" style="height:16px;text-align:right;">#{FlagTaxFixed}</div></td>
		</tr>
		<tr id="#{idSelf}FlagTaxFixedTypeWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}FlagTaxFixedTypeStrTitle"><?php echo $_smarty_tpl->tpl_vars['strFlagTaxFixedType']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}FlagTaxFixedTypeOffset"><div id="#{idSelf}FlagTaxFixedType" style="height:16px;text-align:right;">#{FlagTaxFixedType}</div></td>
		</tr>
		<tr id="#{idSelf}FlagTaxFixedError" style="display:none;">
			<td class="codeLibBaseTableError codeLibBaseFontRed codeLibBaseFontSizeSeventy" colspan="3"></td>
		</tr>


		<tr id="#{idSelf}FlagDepUpWrap">
			<td class="codeLibBaseTableColumn" style="width:70px;" rowspan="2"><?php echo $_smarty_tpl->tpl_vars['strTaxReason']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}FlagDepUpStrTitle"><?php echo $_smarty_tpl->tpl_vars['strFlagDepUp']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}FlagDepUpOffset"><div id="#{idSelf}FlagDepUp" style="height:16px;text-align:right;">#{FlagDepUp}</div></td>
		</tr>
		<tr id="#{idSelf}FlagDepDownWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}FlagDepDownStrTitle"><?php echo $_smarty_tpl->tpl_vars['strFlagDepDown']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}FlagDepDownOffset"><div id="#{idSelf}FlagDepDown" style="height:16px;text-align:right;">#{FlagDepDown}</div></td>
		</tr>
		<tr id="#{idSelf}FlagDepUpError" style="display:none;">
			<td class="codeLibBaseTableError codeLibBaseFontRed codeLibBaseFontSizeSeventy" colspan="3"></td>
		</tr>


		<tr id="#{idSelf}StampBuyWrap">
			<td class="codeLibBaseTableColumn" style="width:70px;" rowspan="4"><?php echo $_smarty_tpl->tpl_vars['strTime']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}StampBuyStrTitle" style="width:150px;" ><?php echo $_smarty_tpl->tpl_vars['strStampBuy']->value;?>
 #{strMust}</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}StampBuyOffset"><div id="#{idSelf}StampBuy" style="height:16px;text-align:right;">#{StampBuy}</div></td>
		</tr>
		<tr id="#{idSelf}StampStartWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}StampStartStrTitle"><?php echo $_smarty_tpl->tpl_vars['strStampStart']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}StampStartOffset"><div id="#{idSelf}StampStart" style="height:16px;text-align:right;">#{StampStart}</div></td>
		</tr>
		<tr id="#{idSelf}StampDropWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}StampDropStrTitle"><?php echo $_smarty_tpl->tpl_vars['strStampDrop']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}StampDropOffset"><div id="#{idSelf}StampDrop" style="height:16px;text-align:right;">#{StampDrop}</div></td>
		</tr>
		<tr id="#{idSelf}StampEndWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}StampEndStrTitle"><?php echo $_smarty_tpl->tpl_vars['strStampEnd']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}StampEndOffset"><div id="#{idSelf}StampEnd" style="height:16px;text-align:right;">#{StampEnd}</div></td>
		</tr>
		<tr id="#{idSelf}StampBuyError" style="display:none;">
			<td class="codeLibBaseTableError codeLibBaseFontRed codeLibBaseFontSizeSeventy" colspan="3"></td>
		</tr>


		<tr id="#{idSelf}NumValueWrap">
			<td class="codeLibBaseTableColumn" style="width:70px;" rowspan="8"><?php echo $_smarty_tpl->tpl_vars['strValue']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueStrTitle" style="width:150px;" ><?php echo $_smarty_tpl->tpl_vars['strNumValue']->value;?>
 #{strMust}</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueOffset"><div id="#{idSelf}NumValue" style="height:16px;text-align:right;">#{NumValue}</div></td>
		</tr>
		<tr id="#{idSelf}NumValueCompressionWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueCompressionStrTitle"><?php echo $_smarty_tpl->tpl_vars['strNumValueCompression']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueCompressionOffset"><div id="#{idSelf}NumValueCompression" style="height:16px;text-align:right;">#{NumValueCompression}</div></td>
		</tr>
		<tr id="#{idSelf}NumValueNetWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueNetStrTitle"><?php echo $_smarty_tpl->tpl_vars['strNumValueNet']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueNetOffset"><div id="#{idSelf}NumValueNet" style="height:16px;text-align:right;">#{NumValueNet}</div></td>
		</tr>
		<tr id="#{idSelf}NumSurvivalRateWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}NumSurvivalRateStrTitle" style="width:150px;" ><?php echo $_smarty_tpl->tpl_vars['strNumSurvivalRate']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}NumSurvivalRateOffset"><div id="#{idSelf}NumSurvivalRate" style="height:16px;text-align:right;">#{NumSurvivalRate}</div></td>
		</tr>
		<tr id="#{idSelf}NumSurvivalRateLimitWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}NumSurvivalRateLimitStrTitle"><?php echo $_smarty_tpl->tpl_vars['strNumSurvivalRateLimit']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}NumSurvivalRateLimitOffset"><div id="#{idSelf}NumSurvivalRateLimit" style="height:16px;text-align:right;">#{NumSurvivalRateLimit}</div></td>
		</tr>
		<tr id="#{idSelf}NumValueRemainingBookWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueRemainingBookStrTitle"><?php echo $_smarty_tpl->tpl_vars['strNumValueRemainingBook']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueRemainingBookOffset"><div id="#{idSelf}NumValueRemainingBook" style="height:16px;text-align:right;">#{NumValueRemainingBook}</div></td>
		</tr>
		<tr id="#{idSelf}NumValueAccumulatedWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueAccumulatedStrTitle"><?php echo $_smarty_tpl->tpl_vars['strNumValueAccumulated']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueAccumulatedOffset"><div id="#{idSelf}NumValueAccumulated" style="height:16px;text-align:right;">#{NumValueAccumulated}</div></td>
		</tr>
		<tr id="#{idSelf}NumValueNetOpeningWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueNetOpeningStrTitle"><?php echo $_smarty_tpl->tpl_vars['strNumValueNetOpening']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueNetOpeningOffset"><div id="#{idSelf}NumValueNetOpening" style="height:16px;text-align:right;">#{NumValueNetOpening}</div></td>
		</tr>
		<tr id="#{idSelf}NumValueError" style="display:none;">
			<td class="codeLibBaseTableError codeLibBaseFontRed codeLibBaseFontSizeSeventy" colspan="3"></td>
		</tr>

		<tr id="#{idSelf}NumValueDepCalcBaseWrap">
			<td class="codeLibBaseTableColumn" style="width:70px;" rowspan="<?php echo $_smarty_tpl->tpl_vars['sumCurrentDep']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['strCurrentDep']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepCalcBaseStrTitle"><?php echo $_smarty_tpl->tpl_vars['strNumValueDepCalcBase']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepCalcBaseOffset"><div id="#{idSelf}NumValueDepCalcBase" style="height:16px;text-align:right;">#{NumValueDepCalcBase}</div></td>
		</tr>
		<?php if ($_smarty_tpl->tpl_vars['flagNumValueDepPrevOver']->value) {?>
			<tr id="#{idSelf}NumValueDepPrevOverWrap">
				<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepPrevOverStrTitle" style="width:150px;" ><?php echo $_smarty_tpl->tpl_vars['strNumValueDepPrevOver']->value;?>
</td>
				<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepPrevOverOffset"><div id="#{idSelf}NumValueDepPrevOver" style="height:16px;text-align:right;">#{NumValueDepPrevOver}</div></td>
			</tr>
		<?php }?>
		<tr id="#{idSelf}NumValueDepCalcWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepCalcStrTitle"><?php echo $_smarty_tpl->tpl_vars['strNumValueDepCalc']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepCalcOffset"><div id="#{idSelf}NumValueDepCalc" style="height:16px;text-align:right;">#{NumValueDepCalc}</div></td>
		</tr>
		<tr id="#{idSelf}ArrCommaDepMonthWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}ArrCommaDepMonthStrTitle"><?php echo $_smarty_tpl->tpl_vars['strArrCommaDepMonth']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}ArrCommaDepMonthOffset"><div id="#{idSelf}ArrCommaDepMonth" style="height:16px;text-align:right;">#{ArrCommaDepMonth}</div></td>
		</tr>
		<tr id="#{idSelf}NumRateDepWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}NumRateDepStrTitle"><?php echo $_smarty_tpl->tpl_vars['strNumRateDep']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}NumRateDepOffset"><div id="#{idSelf}NumRateDep" style="height:16px;text-align:right;">#{NumRateDep}</div></td>
		</tr>
		<tr id="#{idSelf}NumValueAssuredWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueAssuredStrTitle"><?php echo $_smarty_tpl->tpl_vars['strNumValueAssured']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueAssuredOffset"><div id="#{idSelf}NumValueAssured" style="height:16px;text-align:right;">#{NumValueAssured}</div></td>
		</tr>
		<tr id="#{idSelf}NumValueDepUpWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepUpStrTitle"><?php echo $_smarty_tpl->tpl_vars['strNumValueDepUp']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepUpOffset"><div id="#{idSelf}NumValueDepUp" style="height:16px;text-align:right;">#{NumValueDepUp}</div></td>
		</tr>
		<tr id="#{idSelf}NumValueDepExtraWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepExtraStrTitle"><?php echo $_smarty_tpl->tpl_vars['strNumValueDepExtra']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepExtraOffset"><div id="#{idSelf}NumValueDepExtra" style="height:16px;text-align:right;">#{NumValueDepExtra}</div></td>
		</tr>
		<tr id="#{idSelf}NumValueDepSpecialWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepSpecialStrTitle"><?php echo $_smarty_tpl->tpl_vars['strNumValueDepSpecial']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepSpecialOffset"><div id="#{idSelf}NumValueDepSpecial" style="height:16px;text-align:right;">#{NumValueDepSpecial}</div></td>
		</tr>
		<?php if ($_smarty_tpl->tpl_vars['flagNumValueDepSpecialShortPrev']->value) {?>
			<tr id="#{idSelf}NumValueDepSpecialShortPrevWrap">
				<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepSpecialShortPrevStrTitle"><?php echo $_smarty_tpl->tpl_vars['strNumValueDepSpecialShortPrev']->value;?>
</td>
				<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepSpecialShortPrevOffset"><div id="#{idSelf}NumValueDepSpecialShortPrev" style="height:16px;text-align:right;">#{NumValueDepSpecialShortPrev}</div></td>
			</tr>
		<?php }?>
		<tr id="#{idSelf}NumValueDepLimitWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepLimitStrTitle"><?php echo $_smarty_tpl->tpl_vars['strNumValueDepLimit']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepLimitOffset"><div id="#{idSelf}NumValueDepLimit" style="height:16px;text-align:right;">#{NumValueDepLimit}</div></td>
		</tr>
		<tr id="#{idSelf}NumValueDepWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepStrTitle"><?php echo $_smarty_tpl->tpl_vars['strNumValueDep']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepOffset"><div id="#{idSelf}NumValueDep" style="height:16px;text-align:right;">#{NumValueDep}</div></td>
		</tr>
		<tr id="#{idSelf}NumValueAccumulatedClosingWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueAccumulatedClosingStrTitle"><?php echo $_smarty_tpl->tpl_vars['strNumValueAccumulatedClosing']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueAccumulatedClosingOffset"><div id="#{idSelf}NumValueAccumulatedClosing" style="height:16px;text-align:right;">#{NumValueAccumulatedClosing}</div></td>
		</tr>
		<tr id="#{idSelf}NumValueNetClosingWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueNetClosingStrTitle"><?php echo $_smarty_tpl->tpl_vars['strNumValueNetClosing']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueNetClosingOffset"><div id="#{idSelf}NumValueNetClosing" style="height:16px;text-align:right;">#{NumValueNetClosing}</div></td>
		</tr>
		<?php if ($_smarty_tpl->tpl_vars['flagNumRatioOperate']->value) {?>
			<tr id="#{idSelf}NumRatioOperateWrap">
				<td class="codeLibBaseTableRow" id="#{idSelf}NumRatioOperateStrTitle"><?php echo $_smarty_tpl->tpl_vars['strNumRatioOperate']->value;?>
</td>
				<td class="codeLibBaseTableRow" id="#{idSelf}NumRatioOperateOffset"><div id="#{idSelf}NumRatioOperate" style="height:16px;text-align:right;">#{NumRatioOperate}</div></td>
			</tr>
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['flagNumValueDepOperate']->value) {?>
		<tr id="#{idSelf}NumValueDepOperateWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepOperateStrTitle"><?php echo $_smarty_tpl->tpl_vars['strNumValueDepOperate']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepOperateOffset"><div id="#{idSelf}NumValueDepOperate" style="height:16px;text-align:right;">#{NumValueDepOperate}</div></td>
		</tr>
		<?php }?>
		<tr id="#{idSelf}NumValueDepCalcBaseError" style="display:none;">
			<td class="codeLibBaseTableError codeLibBaseFontRed codeLibBaseFontSizeSeventy" colspan="3"></td>
		</tr>


		<?php if ($_smarty_tpl->tpl_vars['flagNumValueDepPrevOver']->value) {?>
			<tr id="#{idSelf}NumValueDepPrevOverDataWrap">
				<td class="codeLibBaseTableColumn" style="width:70px;" rowspan="3"><?php echo $_smarty_tpl->tpl_vars['strDepLawOver']->value;?>
</td>
				<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepPrevOverDataStrTitle" style="width:150px;" ><?php echo $_smarty_tpl->tpl_vars['strNumValueDepPrevOverData']->value;?>
</td>
				<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepPrevOverDataOffset"><div id="#{idSelf}NumValueDepPrevOverData" style="height:16px;text-align:right;">#{NumValueDepPrevOverData}</div></td>
			</tr>
			<tr id="#{idSelf}NumValueDepCurrentOverWrap">
				<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepCurrentOverStrTitle"><?php echo $_smarty_tpl->tpl_vars['strNumValueDepCurrentOver']->value;?>
</td>
				<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepCurrentOverOffset"><div id="#{idSelf}NumValueDepCurrentOver" style="height:16px;text-align:right;">#{NumValueDepCurrentOver}</div></td>
			</tr>
			<tr id="#{idSelf}NumValueDepNextOverWrap">
				<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepNextOverStrTitle"><?php echo $_smarty_tpl->tpl_vars['strNumValueDepNextOver']->value;?>
</td>
				<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepNextOverOffset"><div id="#{idSelf}NumValueDepNextOver" style="height:16px;text-align:right;">#{NumValueDepNextOver}</div></td>
			</tr>
			<tr id="#{idSelf}NumValueDepPrevOverDataError" style="display:none;">
				<td class="codeLibBaseTableError codeLibBaseFontRed codeLibBaseFontSizeSeventy" colspan="3"></td>
			</tr>
		<?php }?>

		<?php if ($_smarty_tpl->tpl_vars['flagNumValueDepSpecialShortCurrent']->value) {?>
			<tr id="#{idSelf}NumValueDepSpecialShortPrevDataWrap">
				<td class="codeLibBaseTableColumn" style="width:70px;" rowspan="4"><?php echo $_smarty_tpl->tpl_vars['strSpecialDepLawShort']->value;?>
</td>
				<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepSpecialShortPrevDataStrTitle"><?php echo $_smarty_tpl->tpl_vars['strNumValueDepSpecialShortPrevData']->value;?>
</td>
				<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepSpecialShortPrevDataOffset"><div id="#{idSelf}NumValueDepSpecialShortPrevData" style="height:16px;text-align:right;">#{NumValueDepSpecialShortPrevData}</div></td>
			</tr>
			<tr id="#{idSelf}NumValueDepSpecialShortCurrentWrap">
				<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepSpecialShortCurrentStrTitle" style="width:150px;" ><?php echo $_smarty_tpl->tpl_vars['strNumValueDepSpecialShortCurrent']->value;?>
</td>
				<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepSpecialShortCurrentOffset"><div id="#{idSelf}NumValueDepSpecialShortCurrent" style="height:16px;text-align:right;">#{NumValueDepSpecialShortCurrent}</div></td>
			</tr>
			<tr id="#{idSelf}NumValueDepSpecialShortCurrentCutWrap">
				<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepSpecialShortCurrentCutStrTitle"><?php echo $_smarty_tpl->tpl_vars['strNumValueDepSpecialShortCurrentCut']->value;?>
</td>
				<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepSpecialShortCurrentCutOffset"><div id="#{idSelf}NumValueDepSpecialShortCurrentCut" style="height:16px;text-align:right;">#{NumValueDepSpecialShortCurrentCut}</div></td>
			</tr>
			<tr id="#{idSelf}NumValueDepSpecialShortNextWrap">
				<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepSpecialShortNextStrTitle"><?php echo $_smarty_tpl->tpl_vars['strNumValueDepSpecialShortNext']->value;?>
</td>
				<td class="codeLibBaseTableRow" id="#{idSelf}NumValueDepSpecialShortNextOffset"><div id="#{idSelf}NumValueDepSpecialShortNext" style="height:16px;text-align:right;">#{NumValueDepSpecialShortNext}</div></td>
			</tr>
			<tr id="#{idSelf}NumValueDepSpecialShortPrevDataError" style="display:none;">
				<td class="codeLibBaseTableError codeLibBaseFontRed codeLibBaseFontSizeSeventy" colspan="3"></td>
			</tr>
		<?php }?>


		<tr id="#{idSelf}LossOnDisposalOfFixedAssetsWrap">
			<td class="codeLibBaseTableColumn" style="width:70px;" rowspan="<?php echo $_smarty_tpl->tpl_vars['sumWrite']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['strWrite']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}LossOnDisposalOfFixedAssetsStrTitle" style="width:150px;" ><?php echo $_smarty_tpl->tpl_vars['strLossOnDisposalOfFixedAssets']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}LossOnDisposalOfFixedAssetsOffset"><div id="#{idSelf}LossOnDisposalOfFixedAssets" style="height:16px;text-align:right;">#{LossOnDisposalOfFixedAssets}</div></td>
		</tr>
		<tr id="#{idSelf}AccumulatedDepreciationWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}AccumulatedDepreciationStrTitle"><?php echo $_smarty_tpl->tpl_vars['strAccumulatedDepreciation']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}AccumulatedDepreciationOffset"><div id="#{idSelf}AccumulatedDepreciation" style="height:16px;text-align:right;">#{AccumulatedDepreciation}</div></td>
		</tr>
		<tr id="#{idSelf}SellingAdminCostWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}SellingAdminCostStrTitle"><?php echo $_smarty_tpl->tpl_vars['strSellingAdminCost']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}SellingAdminCostOffset"><div id="#{idSelf}SellingAdminCost" style="height:16px;text-align:right;">#{SellingAdminCost}</div></td>
		</tr>
		<?php if ($_smarty_tpl->tpl_vars['flagProductsCost']->value) {?>
			<tr id="#{idSelf}ProductsCostWrap">
				<td class="codeLibBaseTableRow" id="#{idSelf}ProductsCostStrTitle"><?php echo $_smarty_tpl->tpl_vars['strProductsCost']->value;?>
</td>
				<td class="codeLibBaseTableRow" id="#{idSelf}ProductsCostOffset"><div id="#{idSelf}ProductsCost" style="height:16px;text-align:right;">#{ProductsCost}</div></td>
			</tr>
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['flagNonOperatingExpenses']->value) {?>
			<tr id="#{idSelf}NonOperatingExpensesWrap">
				<td class="codeLibBaseTableRow" id="#{idSelf}NonOperatingExpensesStrTitle"><?php echo $_smarty_tpl->tpl_vars['strNonOperatingExpenses']->value;?>
</td>
				<td class="codeLibBaseTableRow" id="#{idSelf}NonOperatingExpensesOffset"><div id="#{idSelf}NonOperatingExpenses" style="height:16px;text-align:right;">#{NonOperatingExpenses}</div></td>
			</tr>
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['flagAgricultureCost']->value) {?>
			<tr id="#{idSelf}AgricultureCostWrap">
				<td class="codeLibBaseTableRow" id="#{idSelf}AgricultureCostStrTitle"><?php echo $_smarty_tpl->tpl_vars['strAgricultureCost']->value;?>
</td>
				<td class="codeLibBaseTableRow" id="#{idSelf}AgricultureCostOffset"><div id="#{idSelf}AgricultureCost" style="height:16px;text-align:right;">#{AgricultureCost}</div></td>
			</tr>
		<?php }?>
		<tr id="#{idSelf}LossOnDisposalOfFixedAssetsError" style="display:none;">
			<td class="codeLibBaseTableError codeLibBaseFontRed codeLibBaseFontSizeSeventy" colspan="3"></td>
		</tr>




		<tr id="#{idSelf}NumRatioSellingAdminCostWrap">
			<td class="codeLibBaseTableColumn" style="width:70px;" rowspan="<?php echo $_smarty_tpl->tpl_vars['sumRatio']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['strRatio']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}NumRatioSellingAdminCostStrTitle" style="width:150px;" ><?php echo $_smarty_tpl->tpl_vars['strNumRatioSellingAdminCost']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}NumRatioSellingAdminCostOffset"><div id="#{idSelf}NumRatioSellingAdminCost" style="height:16px;text-align:right;">#{NumRatioSellingAdminCost}</div></td>
		</tr>
		<?php if ($_smarty_tpl->tpl_vars['flagNumRatioProductsCost']->value) {?>
			<tr id="#{idSelf}NumRatioProductsCostWrap">
				<td class="codeLibBaseTableRow" id="#{idSelf}NumRatioProductsCostStrTitle"><?php echo $_smarty_tpl->tpl_vars['strNumRatioProductsCost']->value;?>
</td>
				<td class="codeLibBaseTableRow" id="#{idSelf}NumRatioProductsCostOffset"><div id="#{idSelf}NumRatioProductsCost" style="height:16px;text-align:right;">#{NumRatioProductsCost}</div></td>
			</tr>
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['flagNumRatioNonOperatingExpenses']->value) {?>
			<tr id="#{idSelf}NumRatioNonOperatingExpensesWrap">
				<td class="codeLibBaseTableRow" id="#{idSelf}NumRatioNonOperatingExpensesStrTitle"><?php echo $_smarty_tpl->tpl_vars['strNumRatioNonOperatingExpenses']->value;?>
</td>
				<td class="codeLibBaseTableRow" id="#{idSelf}NumRatioNonOperatingExpensesOffset"><div id="#{idSelf}NumRatioNonOperatingExpenses" style="height:16px;text-align:right;">#{NumRatioNonOperatingExpenses}</div></td>
			</tr>
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['flagNumRatioAgricultureCost']->value) {?>
			<tr id="#{idSelf}NumRatioAgricultureCostWrap">
				<td class="codeLibBaseTableRow" id="#{idSelf}NumRatioAgricultureCostStrTitle"><?php echo $_smarty_tpl->tpl_vars['strNumRatioAgricultureCost']->value;?>
</td>
				<td class="codeLibBaseTableRow" id="#{idSelf}NumRatioAgricultureCostOffset"><div id="#{idSelf}NumRatioAgricultureCost" style="height:16px;text-align:right;">#{NumRatioAgricultureCost}</div></td>
			</tr>
		<?php }?>
		<tr id="#{idSelf}FlagFractionWrap">
			<td class="codeLibBaseTableRow" id="#{idSelf}FlagFractionStrTitle"><?php echo $_smarty_tpl->tpl_vars['strFlagFraction']->value;?>
</td>
			<td class="codeLibBaseTableRow" id="#{idSelf}FlagFractionOffset"><div id="#{idSelf}FlagFraction" style="height:16px;text-align:right;">#{FlagFraction}</div></td>
		</tr>
		<tr id="#{idSelf}NumRatioSellingAdminCostError" style="display:none;">
			<td class="codeLibBaseTableError codeLibBaseFontRed codeLibBaseFontSizeSeventy" colspan="3"></td>
		</tr>






	</tbody>
</table>
<?php }
}
?>