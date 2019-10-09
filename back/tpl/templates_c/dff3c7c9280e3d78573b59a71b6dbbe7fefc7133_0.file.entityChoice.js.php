<?php /* Smarty version 3.1.24, created on 2019-10-06 09:31:26
         compiled from "/app/rucaro/back/tpl/templates/else/plugin/accounting/js/entityChoice.js" */ ?>
<?php
/*%%SmartyHeaderCode:15068053575d99b46ec83195_03416444%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'dff3c7c9280e3d78573b59a71b6dbbe7fefc7133' => 
    array (
      0 => '/app/rucaro/back/tpl/templates/else/plugin/accounting/js/entityChoice.js',
      1 => 1570328744,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15068053575d99b46ec83195_03416444',
  'variables' => 
  array (
    'varsLoad' => 0,
    'numNews' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d99b46ecf0260_65753089',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d99b46ecf0260_65753089')) {
function content_5d99b46ecf0260_65753089 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '15068053575d99b46ec83195_03416444';
?>

/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_EntityChoice = Class.create(Code_Lib_ExtChoice,
{

	vars : <?php echo $_smarty_tpl->tpl_vars['varsLoad']->value;?>
,
	numNews : <?php echo $_smarty_tpl->tpl_vars['numNews']->value;?>
,


	/**
	 *
	*/
	initialize : function()
	{
		this._iniCss();
	},

	/**
	 *
	*/
	iniLoad : function(obj)
	{
		this._iniVars(obj);
		this._iniPopup();
		this._iniLayout();
		this._iniNavi();
		this._iniList();
	},

	/**
	 *
	*/
	iniReload : function(obj)
	{
		this._extReload(obj);
	},

	/**
	 *
	*/
	_iniPopup : function()
	{
		this._extPopup();
	},

	/**
	 *
	*/
	_iniVars : function(obj)
	{
		this._extVars(obj);
		this.insReturn = obj.insReturn;
		this.strFunc = obj.strFunc;
		this.flagId = (obj.flagId)? obj.flagId : '';
		this.flagCheckUse = obj.flagCheckUse;
		this._setVarsFlagCheckUse();
	},


	/**
	 *
	*/
	_iniLayout : function()
	{
		this._extLayout();
	},

	/**
	 *
	*/
	_iniNavi : function()
	{
		this._extNavi();
	},

	/**
	 *
	*/
	_iniList : function()
	{
		this._extList();
	},

	/**
	 *
	*/
	_iniCss : function()
	{
		this._extCss();
	}
});
<?php }
}
?>