<?php /* Smarty version 3.1.24, created on 2019-10-06 06:26:36
         compiled from "/app/rucaro/back/tpl/templates/else/core/base/js/lib/formSelect.js" */ ?>
<?php
/*%%SmartyHeaderCode:10119016215d99891c39acd2_44547154%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b1f92cd4ef255f49a1cb4b13d8044ad497b49c73' => 
    array (
      0 => '/app/rucaro/back/tpl/templates/else/core/base/js/lib/formSelect.js',
      1 => 1570328740,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '10119016215d99891c39acd2_44547154',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d99891c3bfed6_29157769',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d99891c3bfed6_29157769')) {
function content_5d99891c3bfed6_29157769 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '10119016215d99891c39acd2_44547154';
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