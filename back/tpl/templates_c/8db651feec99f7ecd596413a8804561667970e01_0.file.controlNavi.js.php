<?php /* Smarty version 3.1.24, created on 2019-10-06 06:34:30
         compiled from "/app/rucaro/back/tpl/templates/else/plugin/accounting/js/lib/controlNavi.js" */ ?>
<?php
/*%%SmartyHeaderCode:10368140855d998af6ba6255_47822982%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8db651feec99f7ecd596413a8804561667970e01' => 
    array (
      0 => '/app/rucaro/back/tpl/templates/else/plugin/accounting/js/lib/controlNavi.js',
      1 => 1570328749,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '10368140855d998af6ba6255_47822982',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d998af6bc6ab3_03481744',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d998af6bc6ab3_03481744')) {
function content_5d998af6bc6ab3_03481744 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '10368140855d998af6ba6255_47822982';
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