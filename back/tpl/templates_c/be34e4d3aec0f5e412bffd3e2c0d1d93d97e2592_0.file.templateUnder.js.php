<?php /* Smarty version 3.1.24, created on 2022-08-13 00:23:38
         compiled from "/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/templateUnder.js" */ ?>
<?php
/*%%SmartyHeaderCode:75528764662f6ef0a747104_21779795%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'be34e4d3aec0f5e412bffd3e2c0d1d93d97e2592' => 
    array (
      0 => '/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/templateUnder.js',
      1 => 1363255022,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '75528764662f6ef0a747104_21779795',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_62f6ef0a74f785_63128794',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_62f6ef0a74f785_63128794')) {
function content_62f6ef0a74f785_63128794 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '75528764662f6ef0a747104_21779795';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_TemplateUnder = Class.create({

	/**
	 *
	*/
	initialize : function(obj)
	{
		this._iniAllot(obj);
		this.iniVars(obj);
		this.setWrap();
		this.templateUnder();
		this.setWrapFormat();
		this.setFormat();
	},

	/**
	 *
	*/
	iniReload : function(obj)
	{
		this.removeWrap();
		this.setWrap();
		this.templateUnder();
		this.setWrapFormat();
		this.setFormat();
	},

	/**
	 *
	*/
	insRoot : null, idRoot : null, insCurrent : null, insSelf : null, idSelf : null, vars : null,
	iniVars : function(obj)
	{

		this.eleInsert = obj.eleInsert;
		this.insRoot = obj.insRoot;
		this.insCurrent = obj.insCurrent;
		this.insSelf = this;
		this.idSelf = obj.idSelf;
		this.vars = obj.vars;
	},

	/**
	 *
	*/
	updateStyle : function(obj)
	{
		this.updateUnderStyle();
		this.updateFormatStyle();
	},

	/**
	 *
	*/
	eleWrap : null,
	setWrap : function()
	{
		var ele = $(document.createElement('div'));
		ele.id = this.idSelf;
		this.eleInsert.insert(ele);
		this.eleWrap = ele;
	},

	/**
	 *
	*/
	removeWrap : function()
	{
		this.eleWrap.remove();
	},

	/**
	 *
	*/
	templateUnder : function()
	{
		var insTemplate = new Code_Lib_Template();
		var dataNor = insTemplate.get({
			flagType  : 'normalBox',
			numWidth  : this.getUnderWidth(),
			numHeight : this.getUnderHeight(),
			id        : this.idSelf + 'Under'
		});
		this.eleWrap.insert(dataNor);
		this.eleWrap.down('.codeLibTemplateNormalBoxMiddleMiddle', 0).addClassName('codeLibBaseBgFff');
	},

	/**
	 *
	*/
	_staticUnder : {numTemplateHeight : 6, numTemplateWidth : 6, numBeforeBox : 46},
	getUnderWidth : function()
	{
		var array = this.eleInsert.style.width.split('px');
		var data =  parseFloat(array[0]) - this._staticUnder.numTemplateWidth;

		return  data;
	},

	/**
	 *
	*/
	getUnderHeight : function()
	{
		var array = this.eleInsert.style.height.split('px');
		var data =  parseFloat(array[0]) - this._staticUnder.numTemplateHeight;
		if (this.vars.flagBeforeBox) {
			data -= this._staticUnder.numBeforeBox;
		}

		return data;
	},

	/**
	 *
	*/
	updateUnderStyle : function()
	{
		var insTemplate = new Code_Lib_Template();
		insTemplate.updateStyle({
			flagType  : 'normalBox',
			numWidth  : this.getUnderWidth(),
			numHeight : this.getUnderHeight(),
			id        : this.idSelf + 'Under'
		});
	},

	/**
	 *
	*/
	wrapFormat : null,
	setWrapFormat : function()
	{
		this.wrapFormat = this.eleWrap.down('.codeLibTemplateNormalBoxMiddleMiddle', 0);
	},

	/**
	 *
	*/
	eleFormat : {},
	setFormat : function()
	{
		this.vars.varsFormat.id = this.idSelf + 'Format';
		this.vars.varsFormat.numWidth = this.getFormatWidth();
		this.vars.varsFormat.numHeight = this.getFormatHeight();
		var insTemplate = new Code_Lib_Template();
		var data = insTemplate.get(this.vars.varsFormat);
		this.wrapFormat.insert(data);
		this.eleFormat = {};
		this.eleFormat.header = this.eleWrap.down('.codeLibTemplateNormalFormatHeader', 0);
		this.eleFormat.body = this.eleWrap.down('.codeLibTemplateNormalFormatBody', 0);
		this.eleFormat.fooder = this.eleWrap.down('.codeLibTemplateNormalFormatFooder', 0);
	},

	/**
	 *
	*/
	getFormatWidth : function()
	{
		var array = this.wrapFormat.style.width.split('px');
		var data = parseFloat(array[0]);

		return  data;
	},

	/**
	 *
	*/
	getFormatHeight : function()
	{
		var array = this.wrapFormat.style.height.split('px');
		var data = parseFloat(array[0]);

		return  data;
	},

	/**
	 *
	*/
	updateFormatStyle : function()
	{
		var cut = this.vars.varsFormat;
		cut.id = this.idSelf + 'Format';
		cut.numWidth = this.getFormatWidth();
		cut.numHeight = this.getFormatHeight();
		var insTemplate = new Code_Lib_Template();
		insTemplate.updateStyle(cut);
	},

	/**
	 *
	*/
	getEleLoading : function()
	{
		return this.eleFormat.header.down('.codeLibBaseMarginRightFive',0);
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