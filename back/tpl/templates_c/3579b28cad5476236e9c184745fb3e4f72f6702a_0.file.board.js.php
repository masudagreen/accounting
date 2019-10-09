<?php /* Smarty version 3.1.24, created on 2019-10-06 06:34:30
         compiled from "/app/rucaro/back/tpl/templates/else/plugin/accounting/js/lib/board.js" */ ?>
<?php
/*%%SmartyHeaderCode:9517634285d998af6a68544_84072809%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3579b28cad5476236e9c184745fb3e4f72f6702a' => 
    array (
      0 => '/app/rucaro/back/tpl/templates/else/plugin/accounting/js/lib/board.js',
      1 => 1570328749,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9517634285d998af6a68544_84072809',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d998af6ae9ac9_88748655',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d998af6ae9ac9_88748655')) {
function content_5d998af6ae9ac9_88748655 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '9517634285d998af6a68544_84072809';
?>

/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_Lib_Board = Class.create(Code_Lib_ExtLib,
{
	initialize : function(obj)
	{
		this._extAllot(obj);
		this._iniVars(obj);
		this._iniListener();
		this._iniWrap();
		this._iniComment();
		this._iniProfit();
		this._iniCash();

	},

	/**
	 *
	*/
	iniReload : function()
	{
		this.stopListener();
		this.removeWrap();
		this._iniWrap();

	},

	/**
	 *
	*/
	_iniVars : function(obj)
	{
		this._extVars(obj);
		this.eleScroll = obj.eleScroll;
	},

	/**
	 *
	*/
	_iniListener : function()
	{
		this._extListener();
	},

	/**
	 * Wrap
	*/
	_staticWrap : {
		numHeightComment : 100 , numHeightDetail : 100, numPadding : 5, numIdle : 10
	},
	_iniWrap : function()
	{
		this._extWrap();
		this.eleWrap.style.width = this._getWrapWidth() + 'px';
		this.eleWrap.style.height = this._getWrapHeight() + 'px';

		this.eleWrapComment = $(document.createElement('div'));
		this.eleWrapComment.style.width = (this._getWrapWidth() - this._staticWrap.numIdle*2) + 'px';
		this.eleWrapComment.setStyle({
			/*overflowY : 'auto',
			borderColor : '#ccc',
			borderStyle : 'solid',
			borderWidth : '2px',
			marginBottom : '10px',
			padding : this._staticWrap.numPadding + 'px'*/
		});
		this.eleWrap.insert(this.eleWrapComment);

		var numWidth = (this._getWrapWidth() - this._staticWrap.numIdle) / 2;
		if (this.vars.varsStatus.flagProfitUse) {
			this.eleWrapProfit = $(document.createElement('span'));
			this.eleWrapProfit.style.width = numWidth + 'px';
			this.eleWrapProfit.style.height = this._staticWrap.numHeightDetail + 'px';
			this.eleWrapProfit.setStyle({
				marginTop : '10px'
			});
			this.eleWrap.insert(this.eleWrapProfit);
		}
		if (this.vars.varsStatus.flagCashUse) {
			this.eleWrapCash = $(document.createElement('span'));
			this.eleWrapCash.style.width = numWidth + 'px';
			this.eleWrapCash.style.height = this._staticWrap.numHeightDetail + 'px';
			this.eleWrapCash.setStyle({
				marginTop : '10px'
			});
			this.eleWrap.insert(this.eleWrapCash);
		}
	},

	/**
	*/
	_iniComment : function()
	{
		this._setComment({arr : this.vars.varsComment.varsDetail});
	},

	/**
	 *
	*/
	_setComment : function(obj)
	{
		var eleUl = $(document.createElement('ul'));
		this.eleWrapComment.insert(eleUl);
		for (var i = 0; i < obj.arr.length; i++) {
			var eleLi = $(document.createElement('li'));
			eleUl.insert(eleLi);
			if (obj.arr[i].flagDisabled) {
				eleLi.insert(obj.arr[i].strTitle);
				continue;
			}
			var insBtn = new Code_Lib_Btn();
			insBtn.iniBtnTextTarget({
				eleInsert  : eleLi,
				id         : '',
				strFunc    : '_checkTextBtn',
				strTitle   : obj.arr[i].strTitle,
				insCurrent : this,
				vars       : obj.arr[i].vars
			});
			this._setListener({ins : insBtn});
		}
	},

	/**
	 *
	*/
	_checkTextBtn : function(obj)
	{
		this.allot({
			insCurrent : this.insCurrent,
			from       : '_checkTextBtn',
			vars       : {
				vars : obj.vars.vars
			}
		});
	},

	/**
	 *
	*/
	_iniProfit : function()
	{
		if (!this.vars.varsStatus.flagProfitUse) {
			return;
		}
		this._setProfitTitle();
		this._setProfit();
		this._setProfitTable();
	},

	_setProfitTable : function()
	{
		var numWidth = (this._getWrapWidth() - this._staticWrap.numIdle*2) / 2;
		var varsRow = this.vars.varsProfit.varsRow;
		var varsBase = this.vars.varsProfit.varsCollect.varsBase.f1;
		var strUnit = this.vars.varsProfit.varsCollect.tmplOptions.yaxis.unit;
		var strHtml = '<table cellspacing="1" cellpadding="3" border="0" bgcolor="#ddd" width="' + numWidth + '"><tbody>';
		strHtml += '<tr valign="middle">';
		strHtml += '<td class="codeLibBaseTableColumnMiddle" style="width:100px;font-size:10px;">' + varsRow.numSales + '</td>';
		strHtml += '<td class="codeLibBaseTableColumnMiddle" style="width:100px;font-size:10px;">' + varsRow.numPoint + '</td>';
		strHtml += '<td class="codeLibBaseTableColumnMiddle" style="width:100px;font-size:10px;">' + varsRow.numSafe + '</td>';
		strHtml += '</tr>';
		strHtml += '<tr valign="middle">';
		strHtml += '<td class="codeLibBaseTableRowRight" style="font-size:10px;">' + varsBase.numSalesComma + strUnit + '</td>';
		strHtml += '<td class="codeLibBaseTableRowRight" style="font-size:10px;">' + varsBase.numPointComma + strUnit + '</td>';
		strHtml += '<td class="codeLibBaseTableRowRight" style="font-size:10px;">' + varsBase.numSafeComma + strUnit + '</td>';
		strHtml += '</tr>';
		strHtml += '</tbody></table>';
		this.eleWrapProfit.insert(strHtml);
	},

	_setProfitTitle : function()
	{
		var numWidth = (this._getWrapWidth() - this._staticWrap.numIdle) / 2;

		var ele = $(document.createElement('div'));
		ele.style.width = this.eleWrapProfit.style.width;
		this.eleWrapProfit.setStyle({
			textAlign : 'center'
		});
		ele.insert(this.vars.varsProfit.strTitle);
		this.eleWrapProfit.insert(ele);
	},

	/**
	 *
	*/
	_setProfit : function()
	{
		this._setProfitFormChart();
		this.insSpaceProfit = new Code_Lib_Space({
			eleScroll  : this.eleScroll,
			eleInsert  : this.eleWrapProfit,
			insRoot    : this.insRoot,
			insCurrent : this,
			idSelf     : this.idSelf + 'SpaceProfit',
			allot      : {},
			vars       : this.vars.varsProfit.varsSpace
		});
	},

	/**
	 *
	*/
	_setProfitFormChart : function(obj)
	{
		var flagFiscalPeriod = 'f1';
		var varsCollect = this.vars.varsProfit.varsCollect;
		var varsOptions = (Object.toJSON(varsCollect.tmplOptions)).evalJSON();

		/**/
		var num = 1;
		varsOptions.xaxis.min = 0;
		varsOptions.xaxis.ticks.push([0, '']);
		var arr = varsCollect.varsFlagFiscalPeriod;
		var arrPeriod = [];
		for (var i = 0; i < arr.length; i++) {
			var str = arr[i] + '';
			var tempData = [];
			if (flagFiscalPeriod == 'f1') {
				if (str.match(/^f1$/)) {
					tempData.push(num);
					tempData.push(varsCollect.strPeriodCurrent);
					varsOptions.xaxis.ticks.push(tempData);
					arrPeriod.push(arr[i]);
					break;
				}
			}
		}
		varsOptions.xaxis.ticks.push([num+1, ' ']);
		varsOptions.xaxis.max = num+1;

		var varsData = [];
		var numMaxValue = 0;
		var arr = varsCollect.varsLabelId;
		for (var i = 0; i < arr.length; i++) {
			var tempData = {
				data  : [],
				label : varsCollect.varsLabel[arr[i]]
			};
			var num = 1;
			for (var j = 0; j < arrPeriod.length; j++) {
				var strPeriod = arrPeriod[j] + '';
				var numValue = varsCollect.varsBase[strPeriod][arr[i]];
				if (numValue !== '') {
					if (numValue > 0) {
						numMaxValue = numValue;
					}
				}
				tempData.data.push([num, numValue]);
				num++;
			}
			varsData.push(tempData);
		}

		if (numMaxValue < 1000) {
			varsOptions.yaxis.max = 1000;
		}

		var data = {};
		data.varsData = varsData;
		data.varsOptions = varsOptions;
		this.vars.varsProfit.varsSpace.varsDetail = data;
	},

	/**
	 *
	*/
	_iniCash : function()
	{
		if (!this.vars.varsStatus.flagCashUse) {
			return;
		}
		this._setCashTitle();
		this._setCash();
		this._setCashTable();
	},

	_setCashTitle : function()
	{
		var numWidth = (this._getWrapWidth() - this._staticWrap.numIdle) / 2;

		var ele = $(document.createElement('div'));
		ele.style.width = this.eleWrapCash.style.width;
		this.eleWrapCash.setStyle({
			textAlign : 'center'
		});
		ele.insert(this.vars.varsCash.strTitle);
		this.eleWrapCash.insert(ele);
	},

	/**
	 *
	*/
	_setCash : function()
	{
		this._setCashFormChart();
		this.insSpaceCash = new Code_Lib_Space({
			eleScroll  : this.eleScroll,
			eleInsert  : this.eleWrapCash,
			insRoot    : this.insRoot,
			insCurrent : this,
			idSelf     : this.idSelf + 'SpaceCash',
			allot      : {},
			vars       : this.vars.varsCash.varsSpace
		});
	},

	/**
	 *
	*/
	_setCashFormChart : function(obj)
	{
		var flagFiscalPeriod = 'f1';
		var varsCollect = this.vars.varsCash.varsCollect;
		var varsOptions = (Object.toJSON(varsCollect.tmplOptions)).evalJSON();

		/**/
		var num = 1;
		varsOptions.xaxis.min = 0;
		varsOptions.xaxis.ticks.push([0, '']);
		var arr = [flagFiscalPeriod];
		var arrPeriod = [];
		for (var i = 0; i < arr.length; i++) {
			var str = arr[i] + '';
			var tempData = [];
			if (flagFiscalPeriod == 'f1') {
				if (str.match(/^f1$/)) {
					tempData.push(num);
					tempData.push(varsCollect.strPeriodCurrent);
					varsOptions.xaxis.ticks.push(tempData);
					arrPeriod.push(arr[i]);
					break;
				}
			}
		}
		varsOptions.xaxis.ticks.push([num+1, ' ']);
		varsOptions.xaxis.max = num+1;

		var varsData = [];
		var numMaxValue = 0;
		var arr = varsCollect.varsLabelId;
		for (var i = 0; i < arr.length; i++) {
			var tempData = {
				data  : [],
				label : varsCollect.varsLabel[arr[i]]
			};
			var num = 1;
			for (var j = 0; j < arrPeriod.length; j++) {
				var strPeriod = arrPeriod[j] + '';
				var numValue = varsCollect.varsBase[strPeriod][arr[i]];
				if (numValue !== '') {
					if (numValue > 0) {
						numMaxValue = numValue;
					}
				}
				tempData.data.push([num, numValue]);
				num++;
			}
			varsData.push(tempData);
		}


		if (numMaxValue < 1000) {
			varsOptions.yaxis.max = 1000;
		}

		var data = {};
		data.varsData = varsData;
		data.varsOptions = varsOptions;
		this.vars.varsCash.varsSpace.varsDetail = data;
	},

	_setCashTable : function()
	{
		var numWidth = (this._getWrapWidth() - this._staticWrap.numIdle*2) / 2;
		var varsRow = this.vars.varsCash.varsRow;
		var varsBase = this.vars.varsCash.varsCollect.varsBase.f1;
		var strUnit = this.vars.varsCash.varsCollect.tmplOptions.yaxis.unit;
		var strHtml = '<table cellspacing="1" cellpadding="3" border="0" bgcolor="#ddd" width="' + numWidth + '"><tbody>';
		strHtml += '<tr valign="middle">';
		strHtml += '<td class="codeLibBaseTableColumnMiddle" style="width:100px;font-size:10px;">' + varsRow.numIn + '</td>';
		strHtml += '<td class="codeLibBaseTableColumnMiddle" style="width:100px;font-size:10px;">' + varsRow.numOut + '</td>';
		strHtml += '<td class="codeLibBaseTableColumnMiddle" style="width:100px;font-size:10px;">' + varsRow.numNet + '</td>';
		strHtml += '</tr>';
		strHtml += '<tr valign="middle">';
		strHtml += '<td class="codeLibBaseTableRowRight" style="font-size:10px;">' + varsBase.numInComma + strUnit + '</td>';
		strHtml += '<td class="codeLibBaseTableRowRight" style="font-size:10px;">' + varsBase.numOutComma + strUnit + '</td>';
		strHtml += '<td class="codeLibBaseTableRowRight" style="font-size:10px;">' + varsBase.numNetComma + strUnit + '</td>';
		strHtml += '</tr>';
		strHtml += '</tbody></table>';
		this.eleWrapCash.insert(strHtml);
	},

	/**
	 *
	*/
	_updateWrapStyle : function()
	{
		var numWidth = this._getWrapWidth();
		if (this.vars.varsStatus.unitWidth == '%') {
			numWidth *= this.vars.varsStatus.numWidth/100;
		}
		this.eleWrap.style.width = numWidth + 'px';
		this.eleWrap.style.height = this.vars.varsStatus.numHeight + 'px';
	}
});
<?php }
}
?>