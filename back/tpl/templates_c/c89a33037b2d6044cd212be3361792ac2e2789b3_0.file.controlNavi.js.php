<?php /* Smarty version 3.1.24, created on 2022-08-13 00:24:08
         compiled from "/var/www/html/accounting/back/tpl/templates/else/plugin/accounting/js/lib/controlNavi.js" */ ?>
<?php
/*%%SmartyHeaderCode:30720420762f6ef28b31d32_98701836%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c89a33037b2d6044cd212be3361792ac2e2789b3' => 
    array (
      0 => '/var/www/html/accounting/back/tpl/templates/else/plugin/accounting/js/lib/controlNavi.js',
      1 => 1329214576,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '30720420762f6ef28b31d32_98701836',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_62f6ef28b36137_87052065',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_62f6ef28b36137_87052065')) {
function content_62f6ef28b36137_87052065 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '30720420762f6ef28b31d32_98701836';
?>

/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_Lib_ControlNavi = Class.create(Code_Lib_ControlNavi,
{

	/**
	 *
	*/
	_setSearch : function()
	{
		this.insSearch = new Code_Plugin_Accounting_Lib_Search({
			eleInsertBtnLeft  : this.insUnder.eleFormat.fooder.down('.codeLibBaseMarginLeftFive', 0),
			eleInsertBtnRight : this.insUnder.eleFormat.fooder.down('.codeLibBaseMarginRightFive', 0),
			eleInsert         : this.insUnder.eleFormat.body,
			insRoot           : this.insRoot,
			insCurrent        : this.insSelf,
			idSelf            : this.idSelf + 'Search',
			allot             : this._getSearchAllot(),
			vars              : this.vars.search.varsDetail
		});
	}
});

<?php }
}
?>