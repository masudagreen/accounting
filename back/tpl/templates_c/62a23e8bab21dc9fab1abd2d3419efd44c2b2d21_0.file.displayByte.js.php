<?php /* Smarty version 3.1.24, created on 2019-06-16 09:02:07
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/displayByte.js" */ ?>
<?php
/*%%SmartyHeaderCode:2318984205d06058fe368d9_31352031%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '62a23e8bab21dc9fab1abd2d3419efd44c2b2d21' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/displayByte.js',
      1 => 1560675139,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2318984205d06058fe368d9_31352031',
  'variables' => 
  array (
    'varsLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d06058fe3cbc6_09747452',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d06058fe3cbc6_09747452')) {
function content_5d06058fe3cbc6_09747452 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '2318984205d06058fe368d9_31352031';
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