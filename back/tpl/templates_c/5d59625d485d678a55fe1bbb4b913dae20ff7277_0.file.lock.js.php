<?php /* Smarty version 3.1.24, created on 2022-08-13 00:23:38
         compiled from "/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/lock.js" */ ?>
<?php
/*%%SmartyHeaderCode:39898694062f6ef0a3b0da8_32085654%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5d59625d485d678a55fe1bbb4b913dae20ff7277' => 
    array (
      0 => '/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/lock.js',
      1 => 1329210304,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '39898694062f6ef0a3b0da8_32085654',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_62f6ef0a3b5155_03542308',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_62f6ef0a3b5155_03542308')) {
function content_62f6ef0a3b5155_03542308 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '39898694062f6ef0a3b0da8_32085654';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Lock = Class.create({

	/**
	 * obj = {
	 * 	flagType : string,
	 * 	idInsert : string,
	 * 	numIndex : int,
	 * }
	*/
	varsLock : null, vars : null,
	setLock : function(obj)
	{
		this.vars = obj;
		this.varsLock = '';
		var ele = $(document.createElement('div'));
		this.varsLock = 'lock' + (new Date()).getTime();
		ele.id = this.varsLock;
		ele.addClassName('codeLibLock');
		if(obj.flagType == 'wait') {
			ele.addClassName('codeLibLockOpacityZero');
			ele.addClassName('codeLibLockCursorWait');
		} else if(obj.flagType == 'window') {
			ele.addClassName('codeLibLockBg');
		}
		$(obj.idInsert).insert(ele);
		this.styleLock();
	},

	/**
	 *
	*/
	styleLock : function()
	{
		$(this.varsLock).setStyle({
			zIndex : (this.vars.numZIndex)? this.vars.numZIndex : 100000
		});
	},

	/**
	 *
	*/
	hideLock : function()
	{
		if(this.varsLock) {
			$(this.varsLock).hide();
		}
	},

	/**
	 *
	*/
	showLock : function()
	{
		if(this.varsLock) {
			$(this.varsLock).show();
		}
	},

	/**
	 *
	*/
	removeLock : function()
	{
		if(this.varsLock) {
			$(this.varsLock).remove();
			this.varsLock = null;
		}
	}
});

<?php }
}
?>