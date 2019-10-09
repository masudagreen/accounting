<?php /* Smarty version 3.1.24, created on 2016-08-18 12:50:21
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/displayNumber.js" */ ?>
<?php
/*%%SmartyHeaderCode:68252728057b5af0ddee2d3_51246354%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3fc864547293f018e77159199ae4b8c354566fcc' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/displayNumber.js',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '68252728057b5af0ddee2d3_51246354',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b5af0de00ea6_70871764',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b5af0de00ea6_70871764')) {
function content_57b5af0de00ea6_70871764 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '68252728057b5af0ddee2d3_51246354';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_DisplayNumber = Class.create({

	/**
	 * obj = {
	 * 	numBase   : int,
	 * 	numTarget : int,
	 * }
	*/
	get : function(obj)
	{
		var numBase = this._getLength({num : obj.base});
		var numTarget = this._getLength({num : obj.target});
		var str = '' + obj.target;
		var flag = numBase - numTarget;
		for(var i = 0; i < flag; i++) {
			str = '0'+ str;
		}

		return str;
	},

	/**
	 * obj = {
	 * 	num   : int,
	 * 	numPoint : int,
	 * 	flagType : floor, round, ceil,
	 * }
	*/
	getPoint: function(obj)
	{
		var num = obj.num;
		var numPoint = obj.numPoint;

		var numCheck = Math.floor(num);
		if (isNaN(numCheck) || numCheck == 'Infinity' || numCheck == '-Infinity') {
			return '';
		}

		var str = '';
		if (obj.flagType == 'floor') {
			str = new String(Math.floor(num * Math.pow(10, numPoint)) / Math.pow(10, numPoint));
		} else if (obj.flagType == 'round') {
			str = new String(Math.round(num * Math.pow(10, numPoint)) / Math.pow(10, numPoint));
		} else if (obj.flagType == 'ceil') {
			str = new String(Math.ceil(num * Math.pow(10, numPoint)) / Math.pow(10, numPoint));
		} else {
			str = new String(Math.floor(num * Math.pow(10, numPoint)) / Math.pow(10, numPoint));
		}

		if (str.indexOf('.') < 0) {
			str += '.';
		}

		var strPoint = '';
		for (var i = 0; i < numPoint; i++) {
			strPoint += '0';
		}
		str += strPoint;
		str = str.split('.')[0] + '.' + str.split('.')[1].substr(0, numPoint);

		return str;
	},

	/**
	 *
	*/
	_getLength : function(obj)
	{
		var str = '' + obj.num;
		var data = str.length;
		return data;
	}
});
<?php }
}
?>