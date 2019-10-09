<?php /* Smarty version 3.1.24, created on 2016-08-20 07:30:13
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/formSelect.js" */ ?>
<?php
/*%%SmartyHeaderCode:202664070557b807058d47c9_19834178%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e32034040f198aac19aad3d75d95445e2d61d0be' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/formSelect.js',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '202664070557b807058d47c9_19834178',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b80705918813_44616751',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b80705918813_44616751')) {
function content_57b80705918813_44616751 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '202664070557b807058d47c9_19834178';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_FormSelect = Class.create(Code_Lib_ExtLib,
{

	/**
	 *
	*/
	initialize : function(obj)
	{
		this._extAllot(obj);
		this._iniVars(obj);
		this._iniListener();
		this._iniTemplate();
	},

	/**
	 *
	*/
	_iniVars : function(obj)
	{
		this._extVars(obj);
	},

	/**
	 *
	*/
	_iniListener : function()
	{
		this._extListener();
	},

	/**
	 *
	*/
	_iniTemplate : function()
	{
		this._setTemplateListener();
	},

	/**
	 *
	*/
	_setTemplateListener : function(obj)
	{
		this.insListener.set({ bindAsEvent : 0, insCurrent : this, event : 'change',
			strFunc : '_changeTemplate', ele : this.eleInsert, vars : ''
		});
	},

	/**
	 *
	*/
	_changeTemplate : function()
	{
		this.allot({
			insCurrent : this.insCurrent,
			vars       : this.eleInsert.value
		});
	}

});
<?php }
}
?>