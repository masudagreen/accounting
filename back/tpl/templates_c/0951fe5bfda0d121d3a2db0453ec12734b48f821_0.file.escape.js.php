<?php /* Smarty version 3.1.24, created on 2016-08-20 07:30:13
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/escape.js" */ ?>
<?php
/*%%SmartyHeaderCode:120122450057b80705686169_91501425%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0951fe5bfda0d121d3a2db0453ec12734b48f821' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/escape.js',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '120122450057b80705686169_91501425',
  'variables' => 
  array (
    'varsLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b8070569bf68_72582489',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b8070569bf68_72582489')) {
function content_57b8070569bf68_72582489 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '120122450057b80705686169_91501425';
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