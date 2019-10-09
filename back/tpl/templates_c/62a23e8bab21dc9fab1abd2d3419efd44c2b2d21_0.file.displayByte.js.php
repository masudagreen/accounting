<?php /* Smarty version 3.1.24, created on 2016-08-20 07:30:13
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/displayByte.js" */ ?>
<?php
/*%%SmartyHeaderCode:181394635857b807055fcee2_50941493%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '62a23e8bab21dc9fab1abd2d3419efd44c2b2d21' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/displayByte.js',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '181394635857b807055fcee2_50941493',
  'variables' => 
  array (
    'varsLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b80705616a00_92026971',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b80705616a00_92026971')) {
function content_57b80705616a00_92026971 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '181394635857b807055fcee2_50941493';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_DisplayByte = Class.create(
{

	varsLoad : <?php echo $_smarty_tpl->tpl_vars['varsLoad']->value;?>
,


	/**
	 {
		num      : 0,
		flagFrom : 'b',
		flagTo   : 'mb'
	 }
	*/

	get : function(obj)
	{
		if(obj.flagFrom == 'b') {
			if(obj.flagTo == 'b') return obj.num;
			else if(obj.flagTo == 'kb') return Math.floor(obj.num/1024);
			else if(obj.flagTo == 'mb') return Math.floor(obj.num/1024/1024);
			else if(obj.flagTo == 'gb') return Math.floor(obj.num/1024/1024/1024);
		}
		else if(obj.flagFrom == 'kb') {
			if(obj.flagTo == 'b') return obj.num*1024;
			else if(obj.flagTo == 'kb') return obj.num;
			else if(obj.flagTo == 'mb') return Math.floor(obj.num/1024);
			else if(obj.flagTo == 'gb') return Math.floor(obj.num/1024/1024);
		}
		else if(obj.flagFrom == 'mb') {
			if(obj.flagTo == 'b') return obj.num*1024*1024;
			else if(obj.flagTo == 'kb') return obj.num*1024;
			else if(obj.flagTo == 'mb') return obj.num;
			else if(obj.flagTo == 'gb') return Math.floor(obj.num/1024);
		}
		else if(obj.flagFrom == 'gb') {
			if(obj.flagTo == 'b') return obj.num*1024*1024*1024;
			else if(obj.flagTo == 'kb') return obj.num*1024*1024;
			else if(obj.flagTo == 'mb') return obj.num*1024;
			else if(obj.flagTo == 'gb') return obj.num;
		}
	}
});
<?php }
}
?>