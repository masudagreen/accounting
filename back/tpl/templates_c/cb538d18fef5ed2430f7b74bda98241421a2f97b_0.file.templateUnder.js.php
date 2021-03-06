<?php /* Smarty version 3.1.24, created on 2019-10-06 06:26:38
         compiled from "/app/rucaro/back/tpl/templates/else/core/base/js/lib/templateUnder.js" */ ?>
<?php
/*%%SmartyHeaderCode:10328787345d99891e80f6d4_79705512%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cb538d18fef5ed2430f7b74bda98241421a2f97b' => 
    array (
      0 => '/app/rucaro/back/tpl/templates/else/core/base/js/lib/templateUnder.js',
      1 => 1570328741,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '10328787345d99891e80f6d4_79705512',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d99891e82cba6_81966131',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d99891e82cba6_81966131')) {
function content_5d99891e82cba6_81966131 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '10328787345d99891e80f6d4_79705512';
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