<?php /* Smarty version 3.1.24, created on 2022-08-13 00:23:38
         compiled from "/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/flash.js" */ ?>
<?php
/*%%SmartyHeaderCode:5959441962f6ef0a19d3a5_74266539%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a97a5e6e175f9fa35d99e262ea07081a15cf644d' => 
    array (
      0 => '/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/flash.js',
      1 => 1329210264,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5959441962f6ef0a19d3a5_74266539',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_62f6ef0a1a60e9_83286943',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_62f6ef0a1a60e9_83286943')) {
function content_62f6ef0a1a60e9_83286943 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '5959441962f6ef0a19d3a5_74266539';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Flash = Class.create({

	/**
	 * obj = {
	 * 	path    : string
	 * 	id : string
	 * 	allowFullScreen : string
	 * 	allowScriptAccess : string
	 * 	numWidth : int
	 * 	numHeight : int
	 * 	wmode : int
	 * 	varsKey : [string, string]
	 * 	varsValue : [mix, mix]
	 * }
	*/
	getTemplate : function(obj)
	{
		if(obj.varsKey.length) {
			var vars = '';
			for (var i = 0; i < obj.varsKey.length; i++) {
				vars += obj.varsKey[i] + '=' + obj.varsValue[i] + '&';
			}
			obj.vars = vars;
		}
		else obj.vars = '';

		/*protocol*/
		obj.protocol = 'http';
		var flag = document.URL.match(/^https/);
		if(flag) obj.protocol = 'https';

		var tmplstr = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" ';
		tmplstr += 'codebase=';
		tmplstr += '"#{protocol}://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0"';
		tmplstr += ' width="#{numWidth}" height="#{numHeight}" id="#{id}" align="middle" >';
		tmplstr += '<param name="movie" value="#{path}" />';
		tmplstr += '<param name="quality" value="high" />';
		tmplstr += '<param name="allowFullScreen" value="#{allowFullScreen}">';
		tmplstr += '<param name="allowScriptAccess" value="#{allowScriptAccess}" />';
		if(obj.wmode) {
			tmplstr += '<param name="wmode" value="transparent" />';
		}
		if(obj.varsKey.length) {
			tmplstr += '<param name="FlashVars" value="#{vars}" />';
		}
		tmplstr += '<embed class="embed" name="#{id}" src="#{path}" width="#{numWidth}" height="#{numHeight}"';
		tmplstr += ' allowScriptAccess="#{allowScriptAccess}" allowFullScreen="#{allowFullScreen}"quality="high"';
		tmplstr += 'align="middle" type="application/x-shockwave-flash" ';
		tmplstr += 'pluginspage="#{protocol}://www.macromedia.com/go/getflashplayer"';
		if(obj.wmode){
			 tmplstr += 'wmode="transparent" ';
		}
		if(obj.varsKey.length) {
			tmplstr += 'FlashVars="#{vars}">';
		}
		tmplstr += '</embed>';
		tmplstr += '</object>';
		var data = tmplstr.interpolate(obj);

		return data;
	},

	/**
	 *
	*/
	checkBrowser : function(obj)
	{
	    if (navigator.appName.indexOf("Microsoft") != -1) return window[obj.idMc];
	    else return document[obj.idMc];
	}
});

<?php }
}
?>