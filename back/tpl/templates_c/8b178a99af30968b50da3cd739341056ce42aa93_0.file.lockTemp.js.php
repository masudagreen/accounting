<?php /* Smarty version 3.1.24, created on 2016-08-20 07:30:13
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/lockTemp.js" */ ?>
<?php
/*%%SmartyHeaderCode:21492538457b80705b69bc4_82726200%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8b178a99af30968b50da3cd739341056ce42aa93' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/lockTemp.js',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '21492538457b80705b69bc4_82726200',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b80705b77323_21459520',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b80705b77323_21459520')) {
function content_57b80705b77323_21459520 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '21492538457b80705b69bc4_82726200';
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