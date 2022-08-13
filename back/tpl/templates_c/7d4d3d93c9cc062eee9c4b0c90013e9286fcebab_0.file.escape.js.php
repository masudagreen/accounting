<?php /* Smarty version 3.1.24, created on 2022-08-13 00:23:38
         compiled from "/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/escape.js" */ ?>
<?php
/*%%SmartyHeaderCode:70363581562f6ef0a189596_58129332%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7d4d3d93c9cc062eee9c4b0c90013e9286fcebab' => 
    array (
      0 => '/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/escape.js',
      1 => 1334115060,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '70363581562f6ef0a189596_58129332',
  'variables' => 
  array (
    'varsLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_62f6ef0a1947e0_17398789',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_62f6ef0a1947e0_17398789')) {
function content_62f6ef0a1947e0_17398789 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '70363581562f6ef0a189596_58129332';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Escape = Class.create({

	varsLoad : <?php echo $_smarty_tpl->tpl_vars['varsLoad']->value;?>
,


	/**
	 * obj = {
	 * 	flagType: string,
	 * 	data: mix,
	 * }
	*/
	get : function(obj)
	{
		return this.set({
			arr  : this.varsLoad[obj.flagType],
			data : obj.data
		});
	},

	/**
	 *
	*/
	set : function(obj) {
		var data = obj.data;
		if(data == undefined || data == '') return '';
		if (typeof(data) != 'string') return data;
		for (var i = 0; i < obj.arr.length; i++) {
			data = data.replace(RegExp(obj.arr[i].before, "g"), obj.arr[i].after);
		}

		return data;
	},

	/**
	 * obj = {
	 * 	arr: array,
	 * }
	*/
	toCommnaArr : function(obj)
	{
		if (!obj.arr.length) return '';
		var str = ',' + obj.arr.join(',') + ',';

		return str;
	},

	/**
	 * obj = {
	 * 	data : string,
	 * 	insTimeZone : ins,
	 * }
	*/
	toStampFromTerm : function(obj)
	{
		var array = obj.data.split('/');

		var objTime = obj.insTimeZone.adjustTime({
			stamp : new Date(array[0], parseFloat(array[1]) - 1 , array[2]).getTime()
		});

		return objTime.stampServer;
	},

	/**
	 * obj = {
	 * 	data: string,
	 * }
	*/
	strLowCapitalize : function(obj)
	{
		if (obj.data == '') return;
		var str = obj.data;
		var numStr = str.length;
		var strTop = str.slice(0, 1);
		var strbottom = str.slice(1, numStr);
		str = strTop.toLowerCase() + strbottom;

		return str;
	},

	/**
	 * obj = {
	 * 	data: string,
	 * }
	*/
	strCapitalize : function(obj)
	{
		if (obj.data == '') return;
		var str = obj.data;
		var numStr = str.length;
		var strTop = str.slice(0, 1);
		var strbottom = str.slice(1, numStr);
		str = strTop.capitalize() + strbottom;

		return str;
	},

	/**
	 * obj = {
	 * 	str: string,
	 * }
	*/
	fromCommnaArr : function(obj)
	{
		if (obj.str == '') return [];
		obj.arr = obj.str.split(',');
		var objData = {};
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i] == '' || obj.arr[i] == null) continue;
			var str = 'id' + obj.arr[i];
			objData[str] = obj.arr[i];
		}
		var hash = $H(objData);
		var arrayNew = [];
		var num = 0;
		hash.each( function(pair){
			arrayNew[num] = pair.value;
			num++;
		} );

		return arrayNew;
	}

});
<?php }
}
?>