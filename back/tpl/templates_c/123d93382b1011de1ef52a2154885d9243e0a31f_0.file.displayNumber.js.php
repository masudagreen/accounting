<?php /* Smarty version 3.1.24, created on 2019-10-06 06:26:35
         compiled from "/app/rucaro/back/tpl/templates/else/core/base/js/lib/displayNumber.js" */ ?>
<?php
/*%%SmartyHeaderCode:17281773055d99891b848ce3_41642277%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '123d93382b1011de1ef52a2154885d9243e0a31f' => 
    array (
      0 => '/app/rucaro/back/tpl/templates/else/core/base/js/lib/displayNumber.js',
      1 => 1570328740,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '17281773055d99891b848ce3_41642277',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d99891b868348_39006664',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d99891b868348_39006664')) {
function content_5d99891b868348_39006664 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '17281773055d99891b848ce3_41642277';
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