<?php /* Smarty version 3.1.24, created on 2019-06-16 09:02:07
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/btn.js" */ ?>
<?php
/*%%SmartyHeaderCode:7102214165d06058fcc45c2_39155521%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c7ce6e788237d3a45780b197b3213b5e34926fa2' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/btn.js',
      1 => 1560675138,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '7102214165d06058fcc45c2_39155521',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d06058fcc9020_57450853',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d06058fcc9020_57450853')) {
function content_5d06058fcc9020_57450853 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '7102214165d06058fcc45c2_39155521';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Btn = Class.create({



	/**
	 *
	*/
	insListener : null,
	_iniListener : function()
	{
		this.insListener = new Code_Lib_Listener();
	},

	/**
	 * obj = {
	 * 	id         : string,
	 * 	insCurrent : instance,
	 * 	strFunc    : string,
	 * 	eleInsert  : element,
	 * 	strTitle   : string
	 * }
	*/
	iniBtnTextTarget : function(obj)
	{
		this._iniListener();
		this._setBtnTextTarget(obj);
		this._setBtnTextTargetListener(obj);
	},

	/**
	 *
	*/
	_setBtnTextTarget : function(obj)
	{
		obj.eleInsert.addClassName('codeLibBtnText');
		obj.eleInsert.addClassName('codeLibBaseCursorPointer');
		obj.eleInsert.addClassName('unselect');
		obj.eleInsert.unselectable = 'on';
		obj.eleInsert.insert(obj.strTitle);
	},

	/**
	 *
	*/
	_setBtnTextTargetListener : function(obj)
	{
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'mousedown', strFunc : '_mousedownBtnTextTarget',
			ele : obj.eleInsert, vars : {vars : obj}
		});
		this.insListener.set({
			bindAsEvent : 0, insCurrent : this, event : 'mouseover', strFunc : '_mouseoverBtnTextTarget',
			ele : obj.eleInsert, vars : {vars : obj}
		});
		this.insListener.set({
			bindAsEvent : 0, insCurrent : this, event : 'mouseout', strFunc : '_mouseoutBtnTextTarget',
			ele : obj.eleInsert, vars : {vars : obj}
		});
	},

	/**
	 *
	*/
	_mousedownBtnTextTarget : function(evt,obj) {
		evt.stop();
		obj.vars.eleInsert.removeClassName('codeLibBtnTextOver');
		var insCurrent = obj.vars.insCurrent;
		var strFunc = obj.vars.strFunc;
		insCurrent[strFunc]({vars : obj.vars});
	},

	/**
	 *
	*/
	_mouseoverBtnTextTarget : function(obj)
	{
		obj.vars.eleInsert.addClassName('codeLibBtnTextOver');
	},

	/**
	 *
	*/
	_mouseoutBtnTextTarget : function(obj)
	{
		obj.vars.eleInsert.removeClassName('codeLibBtnTextOver');
	},


	/**
	 * obj = {
	 * 	id         : string,
	 * 	insCurrent : instance,
	 * 	strFunc    : string,
	 * 	eleInsert  : element,
	 * 	strTitle   : string
	 * }
	*/
	iniBtnText : function(obj)
	{
		this._iniListener();
		this._setBtnText(obj);
		this._setBtnTextListener(obj);
	},

	/**
	 *
	*/
	_setBtnText : function(obj)
	{
		var eleBtnText = $(document.createElement('span'));
		eleBtnText.id = obj.id;
		eleBtnText.addClassName('codeLibBtnText');
		eleBtnText.addClassName('codeLibBaseCursorPointer');
		eleBtnText.addClassName('unselect');
		eleBtnText.unselectable = 'on';
		eleBtnText.insert(obj.strTitle);
		$(obj.insCurrent.insRoot.vars.varsSystem.id.root).insert(eleBtnText);
		var numWidth = $(obj.id).offsetWidth;
		eleBtnText.style.width = numWidth + 'px';
		obj.eleInsert.insert(eleBtnText);
	},

	/**
	 *
	*/
	_setBtnTextListener : function(obj)
	{
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'mousedown', strFunc : '_mousedownBtnText',
			ele : $(obj.id), vars : {vars : obj}
		});
		this.insListener.set({
			bindAsEvent : 0, insCurrent : this, event : 'mouseover', strFunc : '_mouseoverBtnText',
			ele : $(obj.id), vars : {vars : obj}
		});
		this.insListener.set({
			bindAsEvent : 0, insCurrent : this, event : 'mouseout', strFunc : '_mouseoutBtnText',
			ele : $(obj.id), vars : {vars : obj}
		});
	},

	/**
	 *
	*/
	_mousedownBtnText : function(evt,obj) {
		evt.stop();
		$(obj.vars.id).removeClassName('codeLibBtnTextOver');
		var insCurrent = obj.vars.insCurrent;
		var strFunc = obj.vars.strFunc;
		insCurrent[strFunc]({vars : obj.vars});
	},

	/**
	 *
	*/
	_mouseoverBtnText : function(obj)
	{
		$(obj.vars.id).addClassName('codeLibBtnTextOver');
	},

	/**
	 *
	*/
	_mouseoutBtnText : function(obj)
	{
		$(obj.vars.id).removeClassName('codeLibBtnTextOver');
	},


	/**
	 * obj = {
	 * 	id         : string,
	 * 	insCurrent : instance,
	 * 	strFunc    : string,
	 * 	eleInsert  : element,
	 * 	strTitle   : string,
	 * 	numWidth   : int,
	 * 	strClass   : string,
	 * 	flagATag   : int,
	 * 	path       : string,
	 * }
	*/
	iniBtn : function(obj) {

		this._iniListener();
		this._setBtn(obj);

	},

	/**
	 *
	*/
	_staticBtn : {numIdle : 10},
	_setBtn : function(obj)
	{
		var eleBtnWrap;

		if (obj.flagATag) {
			eleBtnWrap = $(document.createElement('a'));
			eleBtnWrap.id = obj.id;
			eleBtnWrap.href = 'javascript:void(window.open("' + obj.path + '"));';
			eleBtnWrap.addClassName('codeLibBaseCursorPointer');
			eleBtnWrap.addClassName('unselect');
			eleBtnWrap.unselectable = 'on';
			eleBtnWrap.rel = 'noreferrer';
			eleBtnWrap.setStyle({ float : 'left' });

		} else {
			eleBtnWrap = $(document.createElement('span'));
			eleBtnWrap.id = obj.id;
			eleBtnWrap.addClassName('codeLibBaseCursorPointer');
			eleBtnWrap.addClassName('unselect');
			eleBtnWrap.unselectable='on';
		}

		var eleBtnMiddle = $(document.createElement('span'));
		eleBtnMiddle.addClassName('codeLibBtnMiddle');
		if (obj.strClass) {
			var eleClass = $(document.createElement('span'));
			eleClass.addClassName(obj.strClass);
			eleBtnWrap.title = obj.strTitle;
			eleBtnMiddle.insert(eleClass);
		} else {
			eleBtnMiddle.insert(obj.strTitle);
			$(obj.insCurrent.insRoot.vars.varsSystem.id.root).insert(eleBtnMiddle);
		}

		var width = (obj.numWidth)? obj.numWidth : eleBtnMiddle.offsetWidth + this._staticBtn.numIdle * 2;
		var eleBtnTop = $(document.createElement('span'));
		eleBtnTop.addClassName('codeLibBtnTop');
		eleBtnWrap.insert(eleBtnTop);
		eleBtnTop.setStyle({width : (width - 2) + 'px'});

		eleBtnWrap.insert(eleBtnMiddle);
		eleBtnMiddle.setStyle({width : width + 'px'});

		var eleBtnBottom = $(document.createElement('span'));
		eleBtnBottom.addClassName('codeLibBtnBottom');
		eleBtnWrap.insert(eleBtnBottom);
		eleBtnBottom.setStyle({width : (width - 2) + 'px'});

		eleBtnWrap.setStyle({width : width + 'px'});
		obj.eleInsert.insert(eleBtnWrap);

		obj.eleBtnTop = eleBtnTop;
		obj.eleBtnMiddle = eleBtnMiddle;
		obj.eleBtnBottom = eleBtnBottom;
		this._setBtnListener(obj);
	},

	/**
	 *
	*/
	_setBtnListener : function(obj)
	{
		this.insListener.set({bindAsEvent : 1, insCurrent : this, event : 'mousedown', strFunc : '_mousedownBtn',
			ele : $(obj.id), vars : {vars : obj}
		});
		this.insListener.set({bindAsEvent : 0, insCurrent : this, event : 'mouseover', strFunc : '_mouseoverBtn',
			ele : $(obj.id), vars : {vars : obj}
		});
		this.insListener.set({bindAsEvent : 0, insCurrent : this, event : 'mouseout', strFunc : '_mouseoutBtn',
			ele : $(obj.id), vars : {vars : obj}
		});
	},

	/**
	 *
	*/
	_mousedownBtn : function(evt,obj) {
		if (obj.vars.flagATag) return;
		evt.stop();
		this._mouseoutBtn(obj);
		var insCurrent = obj.vars.insCurrent;
		var strFunc = obj.vars.strFunc;
		insCurrent[strFunc]({vars : obj.vars});
	},

	/**
	 *
	*/
	_mouseoverBtn : function(obj)
	{
		obj.vars.eleBtnTop.addClassName('codeLibBtnOver');
		obj.vars.eleBtnMiddle.addClassName('codeLibBtnOver');
		obj.vars.eleBtnBottom.addClassName('codeLibBtnOver');
	},

	/**
	 *
	*/
	_mouseoutBtn : function(obj)
	{
		obj.vars.eleBtnTop.removeClassName('codeLibBtnOver');
		obj.vars.eleBtnMiddle.removeClassName('codeLibBtnOver');
		obj.vars.eleBtnBottom.removeClassName('codeLibBtnOver');
	},

	/**
	 * obj = {
	 * 	id         : string,
	 * 	insCurrent : instance,
	 * 	strFunc    : string,
	 * 	eleInsert  : element,
	 * 	strTitle   : string,
	 * 	numWidth   : int,
	 * 	unitWidth   : string,
	 * }
	*/
	iniBtnSearch : function(obj)
	{
		this._iniListener();
		this._templateBtnSearch(obj);
		this._templateBtnSearchListener(obj);
	},

	/**
	 *
	*/
	_templateBtnSearch : function(obj)
	{
		var eleWrap = $(document.createElement('form'));
		eleWrap.id = obj.id;
		eleWrap.addClassName('codeLibBtnSearch');
		obj.eleInsert.insert(eleWrap);

		var eleBox = $(document.createElement('span'));
		eleBox.addClassName('codeLibBtnSearchBox');
		eleBox.addClassName('codeLibBaseCursorPointer');
		eleBox.addClassName('unselect');
		eleBox.title = obj.strTitle;
		eleBox.unselectable='on';
		eleWrap.insert(eleBox);

		var eleTag = $(document.createElement('input'));
		eleTag.addClassName('codeLibBtnSearchInput');
		eleTag.type = 'text';
		eleTag.style.width = obj.numWidth + obj.unitWidth;
		eleWrap.insert(eleTag);
	},

	/**
	 *
	*/
	_templateBtnSearchListener : function(obj)
	{
		this.insListener.set({ bindAsEvent : 1, insCurrent : this, event : 'mousedown', strFunc : '_mousedownBtnSearch',
			ele : $(obj.id).down('.codeLibBtnSearchBox',0), vars : {vars : obj}
		});
		this.insListener.set({ bindAsEvent : 0, insCurrent : this, event : 'mouseover', strFunc : '_mouseoverBtnSearch',
			ele : $(obj.id).down('.codeLibBtnSearchBox',0), vars : {vars : obj}
		});
		this.insListener.set({ bindAsEvent : 0, insCurrent : this, event : 'mouseout', strFunc : '_mouseoutBtnSearch',
			ele : $(obj.id).down('.codeLibBtnSearchBox',0), vars : {vars : obj}
		});
	},

	/**
	 *
	*/
	_mousedownBtnSearch : function(evt, obj) {
		evt.stop();
		$(obj.vars.id).down('.codeLibBtnSearchBox',0).removeClassName('codeLibBtnSearchBoxOver');
		var value = $(obj.vars.id).down('.codeLibBtnSearchInput',0).value;
		var insCurrent = obj.vars.insCurrent;
		var strFunc = obj.vars.strFunc;
		insCurrent[strFunc]({value : value});
	},

	/**
	 *
	*/
	_mouseoverBtnSearch : function(obj)
	{
		$(obj.vars.id).down('.codeLibBtnSearchBox',0).addClassName('codeLibBtnSearchBoxOver');
	},

	/**
	 *
	*/
	_mouseoutBtnSearch : function(obj)
	{
		$(obj.vars.id).down('.codeLibBtnSearchBox',0).removeClassName('codeLibBtnSearchBoxOver');
	}

});
<?php }
}
?>