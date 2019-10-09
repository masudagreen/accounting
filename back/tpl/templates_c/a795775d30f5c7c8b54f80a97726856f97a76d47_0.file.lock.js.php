<?php /* Smarty version 3.1.24, created on 2019-10-06 06:26:36
         compiled from "/app/rucaro/back/tpl/templates/else/core/base/js/lib/lock.js" */ ?>
<?php
/*%%SmartyHeaderCode:12835543255d99891cce1172_08406578%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a795775d30f5c7c8b54f80a97726856f97a76d47' => 
    array (
      0 => '/app/rucaro/back/tpl/templates/else/core/base/js/lib/lock.js',
      1 => 1570328740,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12835543255d99891cce1172_08406578',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d99891cd01379_72135902',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d99891cd01379_72135902')) {
function content_5d99891cd01379_72135902 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '12835543255d99891cce1172_08406578';
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