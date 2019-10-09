<?php /* Smarty version 3.1.24, created on 2019-06-16 09:02:08
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/lock.js" */ ?>
<?php
/*%%SmartyHeaderCode:9993285475d060590046796_43043989%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1c1b1cdef22f13ed734f3687b595003d75ee585b' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/lock.js',
      1 => 1560675139,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9993285475d060590046796_43043989',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d06059004a665_64396978',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d06059004a665_64396978')) {
function content_5d06059004a665_64396978 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '9993285475d060590046796_43043989';
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