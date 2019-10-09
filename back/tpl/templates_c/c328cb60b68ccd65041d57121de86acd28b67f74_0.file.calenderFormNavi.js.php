<?php /* Smarty version 3.1.24, created on 2019-10-06 06:26:34
         compiled from "/app/rucaro/back/tpl/templates/else/core/base/js/lib/calenderFormNavi.js" */ ?>
<?php
/*%%SmartyHeaderCode:11055955215d99891a30f2f9_11307806%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c328cb60b68ccd65041d57121de86acd28b67f74' => 
    array (
      0 => '/app/rucaro/back/tpl/templates/else/core/base/js/lib/calenderFormNavi.js',
      1 => 1570328740,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '11055955215d99891a30f2f9_11307806',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d99891a333731_06506701',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d99891a333731_06506701')) {
function content_5d99891a333731_06506701 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '11055955215d99891a30f2f9_11307806';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_CalenderFormNavi = Class.create(Code_Lib_ExtLib,
{

	/**
	 *
	*/
	initialize : function(obj)
	{
		this._extAllot(obj);
		this._iniVars(obj);
		this._iniListener();
		this._iniTemplate();
	},

	/**
	 *
	*/
	_iniVars : function(obj)
	{
		this._extVars(obj);
		this.insTimeZone = this.insRoot.insTimeZone;
	},

	/**
	 * Template
	*/
	_iniTemplate : function()
	{
		this._setTemplateListener();
	},

	/**
	 *
	*/
	_setTemplateListener : function()
	{
		this.insListener.set({ flagType15 : 1, bindAsEvent : 1, insCurrent : this, event : 'focus',
		strFunc : '_focusCheck',	ele : this.eleInsert, vars : '' });
		this.insListener.set({ flagType15 : 1, bindAsEvent : 1, insCurrent : this, event : 'blur',
		strFunc : '_blurCheck', ele : this.eleInsert, vars : '' });

	},

	/**
	 *
	*/
	removeWrap : function()
	{
		this.stopListener();
	},

	/**
	 *
	*/
	_focusCheck : function(obj,evt)
	{
		if (obj) evt.stop();
		else obj = evt;
		if ($(this.idSelf + 'Calender')) return;
		this._iniCalender();
	},

	/**
	 *
	*/
	_blurCheck : function(obj,evt)
	{
		if (obj) evt.stop();
		else obj = evt;
		this._valueCheck();
		if (!$(this.idSelf + 'Calender')) return;
		this.insCalender.removeWrap();
		this.allot({
			from       : '_blurCheck',
			insCurrent : this,
			vars       : this.eleInsert.value
		});
	},

	/**
	 *
	*/
	_valueCheck : function()
	{
		var insCheck = new Code_Lib_CheckValue();
		var flag = 0;
		if (this.vars.varsStatus.flagFormatCheck) {
			flag = insCheck.checkValueFormat({
				flagType  : this.vars.varsStatus.flagFormatCheck,
				flagArray : 0,
				value     : this.eleInsert.value
			});

		} else {
			flag = insCheck.checkValueFormat({
				flagType  : 'date',
				flagArray : 0,
				value     : this.eleInsert.value
			});
		}

		if (flag) this._resetValue();
	},

	/**
	 *
	*/
	_staticCalender : {numTop : 21, numLeft : 3},
	insCalender : null,
	_iniCalender : function()
	{
		this._varCalender();
		this.insCalender = new Code_Lib_Calender({
			eleInsert  : this.eleInsert.up('.codeLibWindow', 0),
			idRoot     : this.insRoot.vars.varsSystem.id.root,
			insRoot    : this.insRoot,
			insCurrent : this.insSelf,
			idSelf     : this.idSelf + 'Calender',
			allot      : this._getCalenderAllot(),
			vars       : this.vars
		});
		this._setListener({ins : this.insCalender});
	},

	/**
	 *
	*/
	_getCalenderAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == '_mousedownDate') {
				insCurrent.insCalender.removeWrap();
				insCurrent._iniValue({objTime : obj.objTime});
				insCurrent.allot({
					from       : obj.from,
					insCurrent : insCurrent.insCurrent,
					vars       : insCurrent.eleInsert.value
				});
			}
		};

		return allot;
	},

	/**
	 *
	*/
	_varCalender : function()
	{
		var eleScroll = this.eleInsert.up('.codeLibTemplateNormalFormatBody', 0);
		var numLeft = eleScroll.scrollLeft;
		var numTop = eleScroll.scrollTop;
		this.vars.varsStatus.numLeft = this.eleInsert.offsetLeft
									- numLeft
									- this._staticCalender.numLeft;
		this.vars.varsStatus.numTop = this.eleInsert.offsetTop
									- numTop
									+ this._staticCalender.numTop;
	},

	/**
	 *
	*/
	_varsValue : null,
	_iniValue : function(obj)
	{
		this._setValue({objTime : obj.objTime});
	},

	/**
	 *
	*/
	_resetValue : function()
	{
		this.eleInsert.value = '';
	},

	/**
	 *
	*/
	_setValue : function(obj)
	{
		var objTime = obj.objTime;
		var insDisplay = new Code_Lib_TimeDisplay();
		if (this.vars.varsStatus.flagFormatDisplay) {
			this.eleInsert.value = insDisplay.get({flagType : this.vars.varsStatus.flagFormatDisplay, vars : objTime});

		} else {
			this.eleInsert.value = insDisplay.get({flagType : 4, vars : objTime});
		}

		this._varsValue = objTime;
	},

	/**
	 *
	*/
	_iniListener : function()
	{
		this._extListener();
	}
});

<?php }
}
?>