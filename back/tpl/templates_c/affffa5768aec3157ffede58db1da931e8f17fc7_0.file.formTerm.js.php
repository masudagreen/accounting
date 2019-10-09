<?php /* Smarty version 3.1.24, created on 2016-08-18 12:50:22
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/formTerm.js" */ ?>
<?php
/*%%SmartyHeaderCode:150523695357b5af0e2ba7d5_27086500%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'affffa5768aec3157ffede58db1da931e8f17fc7' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/formTerm.js',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '150523695357b5af0e2ba7d5_27086500',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b5af0e2ed4d9_92373084',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b5af0e2ed4d9_92373084')) {
function content_57b5af0e2ed4d9_92373084 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '150523695357b5af0e2ba7d5_27086500';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_FormTerm = Class.create({

	/**
	 *
	*/
	initialize : function(obj)
	{
		this._iniAllot(obj);
		this.iniVars(obj);
		this.iniWrap();
		this.iniLine();
	},

	/**
	 *
	*/
	iniReload : function()
	{
		this.stopListener();
		this.removeWrap();
		this.iniWrap();
		this.iniLine();
	},

	/**
	 *
	*/
	insRoot : null, insCurrent : null, insSelf : null, idSelf : null, eleInsert : null, vars : null,
	iniVars : function(obj)
	{
		this.eleInsert = obj.eleInsert;
		this.insRoot = obj.insRoot;
		this.insCurrent = obj.insCurrent;
		this.insSelf = this;
		this.idSelf = obj.idSelf;
		this.vars = (Object.toJSON(obj.vars)).evalJSON();
	},

	/**
	 *
	*/
	eleWrap : null,
	iniWrap : function()
	{
		var ele = $(document.createElement('div'));
		ele.id = this.idSelf;
		ele.addClassName('codeLibFormTermWrap');
		ele.setStyle({
			width : this.getWrapWidth() + 'px'
		});
		this.eleInsert.insert(ele);
		this.eleWrap = ele;
	},

	/**
	 *
	*/
	getWrapWidth : function()
	{
		var array = this.eleInsert.style.width.split('px');
		var width = parseFloat(array[0]);

		return  width;
	},

	/**
	 *
	*/
	removeWrap : function()
	{
		$(this.idSelf).remove();
	},

	/**
	 *
	*/
	getValue : function()
	{
		return {
			stampStart : this.insCalenderStart.eleInsert.value,
			stampEnd   : this.insCalenderEnd.eleInsert.value
		};
	},

	/**
	 *
	*/
	insCalenderStart : null, insCalenderEnd : null,
	iniCalender : function(obj)
	{
		var str = obj.flag.capitalize();
		this['insCalender' + str] = new Code_Lib_CalenderFormNavi({
			eleInsert  : obj.eleInsert,
			insRoot    : this.insRoot,
			insCurrent : this.insSelf,
			idSelf     : obj.id + 'Calender' + str,
			allot      : this['getCalender' + str + 'Allot'](),
			vars   : this.vars.varsCalender[str]
		});
	},

	/**
	 *
	*/
	getCalenderEndAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if(obj.from.match(/^mousedownDate|blurCheck$/)) {
				insCurrent.checkCalender({ flag : 'end', vars : obj.vars });
			}
		};

		return allot;
	},

	/**
	 *
	*/
	getCalenderStartAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if(obj.from.match(/^mousedownDate|blurCheck$/)) {
				insCurrent.checkCalender({ flag : 'start', vars : obj.vars });
			}
		};

		return allot;
	},

	/**
	 *
	*/
	checkCalender : function(obj)
	{
		var str = obj.flag;
		var startValue = this.insCalenderStart.eleInsert.value;
		if(startValue == '') return;
		var array = startValue.split('/');
		if(parseFloat(array[0]) < 1970) {
			this.insCalenderStart.resetValue();
			return;
		}
		var objTimeStart = this.insRoot.insTimeZone.insTimeZone.adjustTime({
			stamp : new Date(array[0], parseFloat(array[1]) - 1, array[2]).getTime()
		});

		var endValue = this.insCalenderEnd.eleInsert.value;
		if(endValue == '') return;
		var array = endValue.split('/');
		if(parseFloat(array[0]) < 1970) {
			this.insCalenderEnd.resetValue();
			return;
		}
		var objTimeEnd = this.insRoot.insTimeZone.insTimeZone.adjustTime({
			stamp : new Date(array[0], parseFloat(array[1])-1, array[2]).getTime()
		});
		if(objTimeStart.stamp <= objTimeEnd.stamp) return;
		this.insCalenderEnd.resetValue();
	},

	/**
	 * Listener
	*/
	stopListener : function()
	{
		if(this.insCalenderEnd) this.insCalenderEnd.stopListener();
		if(this.insCalenderStart) this.insCalenderStart.stopListener();
	},

	/**
	 * Line
	*/
	iniLine : function()
	{
		this.templateLine();
	},

	/**
	 *
	*/
	staticLine : {numMargin : 5, numBlock : 16},
	templateLine : function(obj)
	{
		var eleForm = $(document.createElement('form'));
		this.eleWrap.insert(eleForm);
		var eleLine = $(document.createElement('div'));
		eleLine.unselectable = 'on';
		eleLine.addClassName('codeLibFormTermLine');
		eleLine.id = this.idSelf + 'Line';
		eleLine.setStyle({
			width : this.getWrapWidth() + 'px'
		});
		eleLine.setStyle({
			marginTop : (this.staticLine.numMargin) + 'px'
		});
		eleForm.insert(eleLine);
		var width = this.getWrapWidth();
		width -= this.staticLine.numBlock;
		var widthStart = Math.floor(width*0.5);
		var widthEnd = Math.floor(width*0.5);
		this.templateLineInput({
			flag      : 'start',
			numWidth  : widthStart,
			eleInsert : eleLine,
		});
		this.templateLineInput({
			flag      : 'end',
			numWidth  : widthStart,
			eleInsert : eleLine,
		});
	},

	/**
	 *
	*/
	templateLineInput : function(obj)
	{
		var str = obj.flag.capitalize();
		var ele = $(document.createElement('input'));
		ele.type = 'text';
		ele.id = this.idSelf + 'Line' + str;
		ele.addClassName('codeLibFormTerm_' + str);
		if(str == 'end') ele.addClassName('codeLibBaseMarginLeftFive');
		var stamp = this.vars.varsCalender[str].varsStatus.stampMain;
		if(stamp) {
			var objTime = this.insRoot.insTimeZone.adjustDate({
				stamp : this.vars.varsCalender[str].varsStatus.stampMain
			});
			var insDisplay = new Code_Lib_TimeDisplay();
			ele.value = insDisplay.get({flagType : 7, vars : objTime});
		}
		else ele.value = '';
		ele.style.width = obj.numWidth + 'px';
		obj.eleInsert.insert(ele);
		this.iniCalender({
			flag : str,
			eleInsert : ele
		});
	},

	/**
	 *
	*/
	allot : {},
	_iniAllot : function(obj)
	{
		this.allot = obj.allot;
	}

});

<?php }
}
?>