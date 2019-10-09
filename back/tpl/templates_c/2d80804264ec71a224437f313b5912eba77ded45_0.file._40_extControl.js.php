<?php /* Smarty version 3.1.24, created on 2016-08-20 07:29:52
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/_40_extControl.js" */ ?>
<?php
/*%%SmartyHeaderCode:174421732557b806f05135c8_93059253%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2d80804264ec71a224437f313b5912eba77ded45' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/_40_extControl.js',
      1 => 1471523677,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '174421732557b806f05135c8_93059253',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b806f0529710_78565302',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b806f0529710_78565302')) {
function content_57b806f0529710_78565302 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '174421732557b806f05135c8_93059253';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_ExtControl = Class.create({

	insRoot : null,
	idSelf : null,
	insSelf : null,
	insCurrent : null,
	eleInsertBtnLeft : null,
	eleInsertBtnRight : null,
	eleInsert : null,
	vars : null,
	insTool : null,
	insUnder : null,

	/**
	 *
	*/
	_extVars : function(obj)
	{
		this.insUnder = obj.insUnder;
		this.insTool = obj.insTool;
		this.insRoot = obj.insRoot;
		this.insCurrent = obj.insCurrent;
		this.insSelf = this;
		this.idSelf = obj.idSelf;
		this.vars = (Object.toJSON(obj.vars)).evalJSON();
	},

	/**
	 *
	*/
	_getCake : function()
	{
		if (!this.insRoot.insCake) return;
		this.insRoot.insCake.getStorageCake({
			parentKey  : this.idSelf,
			funcReturn : this._getCakeVars,
			insReturn  : this
		});
	},

	/**
	 *
	*/
	_getCakeVars : function(obj)
	{
		var insCurrent = obj.insReturn;
		if(obj.data) {
			insCurrent._getCakeVarsUpdate({data : obj.data});
		}
	},

	/**
	 *
	*/
	_getCakeVarsUpdate : function(obj)
	{

	},

	/**
	 *
	*/
	_varsCake : {},
	setCake : function()
	{
		if (!this.insRoot.insCake) return;
		this._varsCake = {};
		this._setCakeVars();
		this.insRoot.insCake.setStorageCake({
			parentKey  : this.idSelf,
			value      : this._varsCake,
			numExpires : 0
		});
	},


	/**
	 *
	*/
	_setCakeVars : function()
	{

	},

	/**
	 * Listener
	*/
	insListener : null,
	_extListener : function()
	{
		this.insListener = new Code_Lib_Listener();
		this._varsListener = [];
	},

	/**
	 *
	*/
	_varsListener : null,
	_setListener : function(obj)
	{
		var data = {ins : obj.ins};
		this._varsListener.push(data);
	},

	/**
	 *
	*/
	stopListener : function()
	{
		this.insListener.stop();
		this._stopListenerChild({arr : this._varsListener});
		this._resetListener();
	},

	/**
	 *
	*/
	_stopListenerChild : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			obj.arr[i].ins.insListener.stop();
		}
	},

	/**
	 *
	*/
	_resetListener : function()
	{
		this._varsListener = [];
	},

	/**
	 * Under
	*/
	insUnder : null,
	_extUnder : function(obj)
	{
		this.insUnder.vars.varsFormat = obj.vars;
		this.insUnder.iniReload();
	},

	/**
	 *
	*/
	eleWrap : null,
	_extWrap : function()
	{
		this.eleWrap = (Object.toJSON(this.insUnder.vars.varsFormat)).evalJSON();
	},

	/**
	 *
	*/
	removeWrap : function()
	{
		this.insUnder.vars.varsFormat = this.eleWrap;
		this.insUnder.iniReload();
	},

	insTool : null,
	_varsTool : null,
	_extTool : function()
	{
		this._updateTool({arr : this.insTool.vars.varsDetail});
		this.insTool.iniReload();
	},

	/**
	 *
	*/
	_updateTool : function(obj)
	{
		this.insEscape = new Code_Lib_Escape();
		for (var i = 0; i < obj.arr.length; i++) {
			var strId = this.insEscape.strCapitalize({data : obj.arr[i].id});
			var str = 'flag' + strId + 'Use';
			obj.arr[i].flagNow = 0;
			if(this._varsTool[str]) obj.arr[i].flagNow = 1;
		}
	},

	/**
	 *
	*/
	setToolLock : function(obj)
	{
		this.insTool.setLock({
			idAttest : obj.idAttest
		});
	},

	/**
	 *
	*/
	cancelToolLock : function(obj)
	{
		this.insTool.cancelLock({
			idAttest : obj.idAttest
		});
	},

	/**
	 *
	*/
	_extEventTool : function(obj)
	{
		this._iniSwitch(obj);
		if (!obj.flagStr) {
			this.iniReload();
		}
	},

	/**
	 *
	*/
	_extSwitch : function(obj)
	{
		var arr = [];
		if (obj.flagStr) {
			var str = 'switch' + obj.flagStr + 'List';
			arr = this.vars.varsStatus[str];

		} else {
			arr = this.vars.varsStatus.switchList;
		}
		this._updateSwitch({arr : arr, flagStr : obj.flagStr, idTarget : obj.idTarget, flagLoop : obj.flagLoop});
	},

	/**
	 *
	*/
	_updateSwitch : function(obj)
	{
		var str = 'flagNow';
		if (obj.flagStr) {
			str = 'flag' + obj.flagStr + 'Now';
		}
		if (obj.flagLoop) {
			var now = 0;
			for (var i = 0; i < obj.arr.length; i++) {
				if(obj.arr[i] == this.vars.varsStatus[str]) now = i;
			}
			now++;
			if((obj.arr.length) == now) {
				now = 0;
			}
			this.vars.varsStatus[str] = obj.arr[now];

		} else {
			this.vars.varsStatus[str] = obj.idTarget;
		}

		this.setCake();
	},


	/**
	 *
	*/
	_extReload : function()
	{
		this.insEscape = new Code_Lib_Escape();
		this.eventRemove();
		this._iniUnder({
			vars : this.vars[this.vars.varsStatus.flagNow].varsFormat
		});
		this.insEscape = new Code_Lib_Escape();
		var str = '_ini' + this.insEscape.strCapitalize({data : this.vars.varsStatus.flagNow});

		if (this.vars.varsStatus.flagNow.match(/^folder/)) {
			this._iniFolder();
		}
		else this[str]();

		if(this.insTool) {
			this._varsTool = this.vars[this.vars.varsStatus.flagNow].varsEdit;
			this._iniTool();
		}
	},

	/**
	 *
	*/
	showFormAttestError : function(obj)
	{
		if (this.insForm) {
			this.insForm.showValueAttestError({
				flagType : obj.flagType,
				str      : (obj.str)? obj.str : ''
			});
		}
	},

	/**
	 *
	*/
	resetFormError : function()
	{
		if (this.insForm) {
			this.insForm.resetValueError();
		}
	},

	/**
	 *
	*/
	allot : {},
	_extAllot : function(obj)
	{
		this.allot = obj.allot;
	}

});

<?php }
}
?>