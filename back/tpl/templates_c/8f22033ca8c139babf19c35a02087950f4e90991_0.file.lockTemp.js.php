<?php /* Smarty version 3.1.24, created on 2022-08-13 00:23:38
         compiled from "/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/lockTemp.js" */ ?>
<?php
/*%%SmartyHeaderCode:104724746162f6ef0a3b7ff4_16543704%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8f22033ca8c139babf19c35a02087950f4e90991' => 
    array (
      0 => '/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/lockTemp.js',
      1 => 1329210270,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '104724746162f6ef0a3b7ff4_16543704',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_62f6ef0a3beb56_56711011',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_62f6ef0a3beb56_56711011')) {
function content_62f6ef0a3beb56_56711011 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '104724746162f6ef0a3b7ff4_16543704';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_LockTemp = Class.create({

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
	 * 	idSelf      : string,
	 * 	numZIndex   : int,
	 * 	idInsert    : string,
	 * 	eleInsert   : ele,
	 * 	insCurrent  : instance,
	 * 	strFunc     : string,
	 * 	flagHideUse : int,
	 * }
	*/
	vars : null, eleLock : null,
	iniLoad : function(obj)
	{
		this._iniListener();
		this.vars = obj;
		this._setLock();
		this._setLockListener();
	},

	/**
	 *
	*/
	_setLock : function(obj)
	{
		var eleLock = $(document.createElement('div'));
		eleLock.id = this.vars.idSelf;
		eleLock.addClassName('codeLibLockView');
		if (this.vars.eleInsert) this.vars.eleInsert.insert(eleLock);
		else $(this.vars.idInsert).insert(eleLock);

		this.eleLock = eleLock;
		this._styleLock();
	},

	/**
	 *
	*/
	_setLockListener : function()
	{
		this.insListener.set({ bindAsEvent : 1, insCurrent : this, event : 'mousedown',
			strFunc : '_mousedownLock', ele : this.eleLock, vars : ''
		});
	},

	/**
	 *
	*/
	_styleLock : function()
	{
		this.eleLock.setStyle({
			zIndex : this.vars.numZIndex
		});
	},

	/**
	 *
	*/
	hideLock : function()
	{
		if(this.eleLock) {
			this.eleLock.hide();
		}
	},

	/**
	 *
	*/
	showLock : function()
	{
		if(this.eleLock) {
			this.eleLock.show();
			this._styleLock();
		}
	},

	/**
	 *
	*/
	_mousedownLock : function(evt, obj) {
		if(obj) evt.stop();
		else obj = evt;
		if(this.eleLock) {
			if(this.vars.flagHideUse) {
				this.eleLock.hide();

			} else {
				this.eleLock.remove();
				this.eleLock = null;
			}
		}
		this.vars.insCurrent[this.vars.strFunc]();
	}
});

<?php }
}
?>