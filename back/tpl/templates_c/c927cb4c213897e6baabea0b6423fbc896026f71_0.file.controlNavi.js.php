<?php /* Smarty version 3.1.24, created on 2016-08-18 12:51:01
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/js/lib/controlNavi.js" */ ?>
<?php
/*%%SmartyHeaderCode:201277656357b5af35a477c0_69172329%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c927cb4c213897e6baabea0b6423fbc896026f71' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/js/lib/controlNavi.js',
      1 => 1471523679,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '201277656357b5af35a477c0_69172329',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b5af35a67a40_35159199',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b5af35a67a40_35159199')) {
function content_57b5af35a67a40_35159199 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '201277656357b5af35a477c0_69172329';
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