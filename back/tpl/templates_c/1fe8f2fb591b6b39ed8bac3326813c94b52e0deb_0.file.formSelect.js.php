<?php /* Smarty version 3.1.24, created on 2022-08-13 00:23:38
         compiled from "/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/formSelect.js" */ ?>
<?php
/*%%SmartyHeaderCode:62521219362f6ef0a291954_46997523%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1fe8f2fb591b6b39ed8bac3326813c94b52e0deb' => 
    array (
      0 => '/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/formSelect.js',
      1 => 1329210306,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '62521219362f6ef0a291954_46997523',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_62f6ef0a295193_80783325',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_62f6ef0a295193_80783325')) {
function content_62f6ef0a295193_80783325 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '62521219362f6ef0a291954_46997523';
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