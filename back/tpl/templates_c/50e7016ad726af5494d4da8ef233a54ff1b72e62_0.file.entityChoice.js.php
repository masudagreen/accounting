<?php /* Smarty version 3.1.24, created on 2022-08-13 00:28:20
         compiled from "/var/www/html/accounting/back/tpl/templates/else/plugin/accounting/js/entityChoice.js" */ ?>
<?php
/*%%SmartyHeaderCode:75294257462f6f02491a6c8_73976485%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '50e7016ad726af5494d4da8ef233a54ff1b72e62' => 
    array (
      0 => '/var/www/html/accounting/back/tpl/templates/else/plugin/accounting/js/entityChoice.js',
      1 => 1329214576,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '75294257462f6f02491a6c8_73976485',
  'variables' => 
  array (
    'varsLoad' => 0,
    'numNews' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_62f6f024930679_63570162',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_62f6f024930679_63570162')) {
function content_62f6f024930679_63570162 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '75294257462f6f02491a6c8_73976485';
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