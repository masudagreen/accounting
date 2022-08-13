<?php /* Smarty version 3.1.24, created on 2022-08-13 00:23:38
         compiled from "/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/color.js" */ ?>
<?php
/*%%SmartyHeaderCode:176444544762f6ef0a07d619_81950977%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'acda7c9754647923494d8c632760b1817542bb1c' => 
    array (
      0 => '/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/color.js',
      1 => 1334115060,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '176444544762f6ef0a07d619_81950977',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_62f6ef0a082638_00802096',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_62f6ef0a082638_00802096')) {
function content_62f6ef0a082638_00802096 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '176444544762f6ef0a07d619_81950977';
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