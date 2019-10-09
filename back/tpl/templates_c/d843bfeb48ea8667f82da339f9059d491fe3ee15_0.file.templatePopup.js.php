<?php /* Smarty version 3.1.24, created on 2016-08-18 12:50:23
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/templatePopup.js" */ ?>
<?php
/*%%SmartyHeaderCode:78168902157b5af0f038743_98419827%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd843bfeb48ea8667f82da339f9059d491fe3ee15' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/templatePopup.js',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '78168902157b5af0f038743_98419827',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b5af0f0645b9_30493662',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b5af0f0645b9_30493662')) {
function content_57b5af0f0645b9_30493662 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '78168902157b5af0f038743_98419827';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_TemplatePopup = Class.create({

	/**
	 *
	*/
	initialize : function(obj)
	{
		this._iniAllot(obj);
		this.iniVars(obj);
		this.iniLock();
		this.iniWrap();
		this.iniTemplate();
		this.iniUnder();
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
	updateStyle : function(obj)
	{
		this.updateStyle();
	},

	/**
	 *
	*/
	insUnder : null,
	iniUnder : function()
	{
		this.setUnder({
			vars : this.vars.varsFormat
		});
	},

	/**
	 *
	*/
	setUnder : function(obj)
	{
		this.insUnder = new Code_Lib_TemplateUnder({
			eleInsert  : this.eleTemplate,
			insRoot    : this.insRoot,
			insCurrent : this.insSelf,
			idSelf     : this.idSelf + 'Under',
			allot      : this.getUnderAllot(),
			vars   : {
				flagBeforeBox  : 0,
				varsFormat : obj.vars
			}
		});
	},

	/**
	 *
	*/
	getUnderAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == 'eventLayout') {
				insCurrent.insUnder.updateStyle();
			}
		};

		return allot;
	},

	/**
	 *
	*/
	iniLock : function()
	{
		if (!this.vars.varsStatus.flagLockUse) return;
		this.templateLock();
	},

	/**
	 *
	*/
	insLock : null,
	templateLock : function()
	{
		this.insLock = new Code_Lib_LockTemp({
			idSelf     : this.idSelf + 'Lock',
			numZIndex  : this.insRoot.setZIndex(),
			idInsert   : this.insRoot.vars.varsSystem.id.root,
			insCurrent : this.insSelf,
			strFunc    : 'removeWrap'
		});
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
		this.eleWrap.setStyle({
			'zIndex' : this.insRoot.setZIndex()
		});
	},

	/**
	 *
	*/
	removeWrap : function()
	{
		if (this.insLock) this.insLock.insListener.stop();
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
	staticTemplate : {numMenu : 6},
	setTemplate : function()
	{
		var insTemplate = new Code_Lib_Template();
		var dataSha = insTemplate.get({
			flagType  : 'menuBox',
			numWidth  : this.vars.varsMenu.numWidth,
			numHeight : this.vars.varsMenu.numHeight,
			id        : ''
		});
		this.eleWrap.insert(dataSha);
		this.eleTemplate = this.eleWrap.down('.codeLibTemplateMenuBoxMiddleMiddle', 0);
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