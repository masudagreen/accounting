<?php /* Smarty version 3.1.24, created on 2019-06-16 09:02:07
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/color.js" */ ?>
<?php
/*%%SmartyHeaderCode:12164670535d06058fdccd27_42271455%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2bfcfbc95a61aacc6f384a7462f0f19398e97514' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/color.js',
      1 => 1560675139,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12164670535d06058fdccd27_42271455',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d06058fdd0b47_58504324',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d06058fdd0b47_58504324')) {
function content_5d06058fdd0b47_58504324 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '12164670535d06058fdccd27_42271455';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Color=Class.create({

	/**
	 * obj = {
	 * 	r : int,
	 * 	g : int,
	 * 	b : int,
	 * }
	*/
	rgbToHex : function(obj)
	{
		var r = parseFloat(obj.r,10);
		var g = parseFloat(obj.g,10);
		var b = parseFloat(obj.b,10);
		if(isNaN(r)) r=0;
		if(isNaN(g)) g=0;
		if(isNaN(b)) b=0;
		if( r>=256 || g>=256 || b>=256) return -1;
		var red = r.toString(16);
		var green = g.toString(16);
		var blue = b.toString(16);
		if(red.length == 1) red = '0' + red;
		if(green.length == 1) green = '0' + green;
		if(blue.length == 1) blue = '0' + blue;

		return '#'+ red + green + blue;
	}
});

<?php }
}
?>