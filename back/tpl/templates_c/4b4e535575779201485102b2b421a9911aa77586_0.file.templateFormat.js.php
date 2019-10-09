<?php /* Smarty version 3.1.24, created on 2019-10-06 06:26:38
         compiled from "/app/rucaro/back/tpl/templates/else/core/base/js/lib/templateFormat.js" */ ?>
<?php
/*%%SmartyHeaderCode:15661102025d99891e5a77c5_35788885%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4b4e535575779201485102b2b421a9911aa77586' => 
    array (
      0 => '/app/rucaro/back/tpl/templates/else/core/base/js/lib/templateFormat.js',
      1 => 1570328741,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15661102025d99891e5a77c5_35788885',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d99891e606711_63511091',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d99891e606711_63511091')) {
function content_5d99891e606711_63511091 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '15661102025d99891e5a77c5_35788885';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_TemplateFormat = Class.create({

	/**
	 *
	*/
	initialize : function(obj)
	{
		this.iniVars(obj);
		this.iniWrap();
		this.iniTemplate();
	},

	/**
	 *
	*/
	insRoot : null, insCurrent : null, insSelf : null, idSelf : null, vars : null,
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
	eleWrap : null,
	iniWrap : function()
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
	eleTemplate : null,
	iniTemplate : function()
	{
		this.setTemplate();
	},

	/**
	 *
	*/
	setTemplate : function()
	{
		var cut = this.vars;
		cut.id = this.idSelf + 'Template';
		cut.numWidth = this.getTemplateWidth();
		cut.numHeight = this.getTemplateHeight();
		var insTemplate = new Code_Lib_Template();
		var data = insTemplate.get(cut);
		this.eleWrap.insert(data);
		this.eleTemplate = {};
		this.eleTemplate.header = this.eleWrap.down('.codeLibTemplateListFormatHeader', 0);
		this.eleTemplate.body = this.eleWrap.down('.codeLibTemplateListFormatBody', 0);
		this.eleTemplate.fooder = (this.eleWrap.down('.codeLibTemplateListFormatFooder', 0))?
								  this.eleWrap.down('.codeLibTemplateListFormatFooder',0)
								: null;
	},

	/**
	 *
	*/
	getTemplateWidth : function()
	{
		var array = this.eleInsert.style.width.split('px');
		var data = parseFloat(array[0]);

		return data;
	},

	/**
	 *
	*/
	getTemplateHeight : function()
	{
		array = this.eleInsert.style.height.split('px');
		var data = parseFloat(array[0]);

		return data;
	},

	/**
	 *
	*/
	updateTemplateStyle : function()
	{
		var cut = this.vars;
		cut.id = this.idSelf + 'Template';
		cut.numWidth = this.getTemplateWidth();
		cut.numHeight = this.getTemplateHeight();
		var insTemplate = new Code_Lib_Template();
		insTemplate.updateStyle(cut);
	}
});

<?php }
}
?>