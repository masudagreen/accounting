<?php /* Smarty version 3.1.24, created on 2016-08-20 07:30:13
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/cookie.js" */ ?>
<?php
/*%%SmartyHeaderCode:128297211657b807055bcca5_67179839%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '48c802f0cc2f6414943d1173720d8ecaa18142b2' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/cookie.js',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '128297211657b807055bcca5_67179839',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b807055cf7f1_13625498',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b807055cf7f1_13625498')) {
function content_57b807055cf7f1_13625498 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '128297211657b807055bcca5_67179839';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Cookie = Class.create({

	/**
	 * obj = {
	 * 	strKey : string,
	 * 	value : mix,
	 * 	numExpires : int,
	 * 	path : string,
	 * 	strDomain : string
	 * }
	*/
	setData : function(obj)
	{
		if(obj.numExpires != '') {
			var date = new Date();
			var second = parseFloat(obj.numExpires);
			date = new Date(date.getTime() + (second * 1000));
			obj.numExpires = '; expires=' + date.toGMTString();
		}
		if(obj.path != '') {
			obj.path = '; path=' + obj.path;
		}
		if(obj.strDomain != '') {
			obj.strDomain = '; domain=' + obj.strDomain;
		}
		var flag = document.URL.match(/^https/);
		if(flag) {
			obj.secure = '; secure';
		} else {
			obj.secure = '';
		}
		var data = obj.strKey + '=' + escape(obj.value);
		document.cookie = data + obj.numExpires + obj.path + obj.strDomain + obj.secure;
	},

	/**
	 * obj = {
	 * 	strParentKey : string,
	 * 	arrayKey : array,
	 * 	arrayValue : array,
	 * 	numExpires : int,
	 * 	path : string,
	 * 	strDomain : string
	 * }
	*/
	setObj : function(obj)
	{
		if(obj.numExpires != '') {
			var date = new Date();
			var second = parseFloat(obj.numExpires);
			date = new Date(date.getTime() + (second * 1000));
			obj.numExpires = '; expires=' + date.toGMTString();
		}
		if(obj.path != '') {
			obj.path = '; path=' + obj.path;
		}
		if(obj.strDomain != '') {
			obj.strDomain = '; domain=' + obj.strDomain;
		}
		var flag = document.URL.match(/^https/);
		if(flag) {
			obj.secure = '; secure';
		} else {
			obj.secure = '';
		}

		var data = obj.parentKey + '=';
		data += obj.strKey[0];
		data += '#' + escape(obj.value[0]);
		for (var i = 1; i < obj.strKey.length; i++) {
			data += '|' + obj.strKey[i];
			data += '#' + escape(obj.value[i]);
		}
		document.cookie = data + obj.numExpires + obj.path + obj.strDomain + obj.secure;
	},

	/**
	 * obj = {
	 * 	path : string,
	 * 	strDomain : string
	 * }
	*/
	removeAllData : function(obj)
	{
		if(obj.path != '') {
			obj.path = '; path=' + obj.path;
		}
		if(obj.strDomain != '') {
			obj.strDomain = '; domain=' + obj.strDomain;
		}
		var flag = document.URL.match(/^https/);
		if(flag) {
			obj.secure = '; secure';
		} else {
			obj.secure = '';
		}
		var cookie=document.cookie;
		var getlist = cookie.split(';');
		getlist.each (function(v, i) {
			var pair = v.split('=');
			var key = pair[0];
			var date = new Date();
			date.setTime(date.getTime() - (86400 * 1000));
			var expires = '; expires=' + date.toGMTString();
			document.cookie = key + '=' + expires + obj.path + obj.strDomain + obj.secure;
		});
	},

	/**
	 * obj = {
	 * 	strKey : string,
	 * }
	*/
	removeData : function(obj)
	{
		var flag = document.URL.match(/^https/);
		if(flag) {
			obj.secure = '; secure';
		} else {
			obj.secure = '';
		}
		document.cookie = obj.strKey + '=;expires=Thu,01-Jan-70 00:00:01 GMT'
			 			+ obj.path + obj.strDomain + obj.secure;
	},

	/**
	 *
	*/
	judge : function()
	{
		return (navigator.cookieEnabled);
	},

	/**
	 * obj = {
	 * 	strKey : string,
	 * 	flagSort : string,
	 * }
	*/
	getData : function(obj)
	{
		var data='';
		var key = obj.strKey + '=';
		var text = document.cookie + ';';
		var startNum = text.indexOf(key);

		var endNum=0;
		if (startNum != -1) {
			endNum = text.indexOf(';', startNum);
			data = unescape(text.substring(startNum + key.length, endNum));
		}
		var object={
			cookie:[]
		};
		var arrayPairs=data.split('|');
		for (var j = 0; j < arrayPairs.length; j++) {
			var arrayPair = arrayPairs[j].split('#');
			if(!arrayPair[1] && !arrayPair[0] && arrayPairs.length == 1) {
				return object;
			}
			var pair = {
				key   : arrayPair[0],
				value : arrayPair[1]
			};
			object.cookie.push(pair);
		}
		if(obj.flagSort) {
			var newSort = object.cookie.sortBy(function(v, i) {
				var data = parseFloat(v.value);
				return data;
			});
			object.cookie = newSort;
		}

		return object;
	}
});

<?php }
}
?>