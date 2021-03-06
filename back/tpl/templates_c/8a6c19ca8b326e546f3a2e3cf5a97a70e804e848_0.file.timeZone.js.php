<?php /* Smarty version 3.1.24, created on 2019-10-06 06:26:38
         compiled from "/app/rucaro/back/tpl/templates/else/core/base/js/lib/timeZone.js" */ ?>
<?php
/*%%SmartyHeaderCode:14516962055d99891eb4ec30_35297562%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8a6c19ca8b326e546f3a2e3cf5a97a70e804e848' => 
    array (
      0 => '/app/rucaro/back/tpl/templates/else/core/base/js/lib/timeZone.js',
      1 => 1570328741,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14516962055d99891eb4ec30_35297562',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d99891eb6c109_02660061',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d99891eb6c109_02660061')) {
function content_5d99891eb6c109_02660061 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '14516962055d99891eb4ec30_35297562';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_TimeZone = Class.create({

	/**
	 * obj = {
	 * 	numTimeZone : int,
	 * }
	*/
	numTimeZone : null, numTimeZoneDif : null,
	initialize : function(obj)
	{
		this.numTimeZone = obj.numTimeZone;
		this.numTimeZoneDif = new Date().getTimezoneOffset() * 60 * 1000;
	},

	/**
	 * obj = {

	 * }
	*/
	adjustStamp : function(obj)
	{
		var stamp = (parseFloat(obj.stamp) - this.numTimeZoneDif) - this.numTimeZone * 60 * 60 * 1000;

		return stamp;
	},

	/**
	 * obj = {
	 * 	stamp : stamp,
	 * }
	*/
	adjustDate : function(obj)
	{
		var ins = new Date( (this.numTimeZoneDif + parseFloat(obj.stamp)) + this.numTimeZone * 60 * 60 * 1000 );
		var objTime = this.getObjTime({ins : ins});

		return objTime;
	},

	/**

	*/
	adjustTime : function(obj)
	{
		var adjustStamp = this.adjustStamp({stamp : obj.stamp});
		var adjustDate = this.adjustDate({stamp : adjustStamp});
		adjustDate.stamp = adjustStamp;
		adjustDate.stampServer = Math.floor(adjustStamp/1000);

		return adjustDate;
	},

	/**
	 * obj = {
	 * 	ins : instance,
	 * }
	*/
	getObjTime : function(obj)
	{
		var ins = obj.ins;

		var arrDay = {
			'0' : 'Sun',
			'1' : 'Mon',
			'2' : 'Tue',
			'3' : 'Wed',
			'4' : 'Thu',
			'5' : 'Fri',
			'6' : 'Sat',
		};

		return {
			stamp          : ins.getTime(),
			numYear        : (new Date().getYear() > 1900) ? ins.getYear() + 0  : ins.getYear() + 1900,
			numMonth       : ins.getMonth(),
			numDate        : ins.getDate(),
			numDay         : ins.getDay(),
			strDay         : arrDay[ins.getDay()],
			numHour        : ins.getHours(),
			numMin         : ins.getMinutes(),
			numSec         : ins.getSeconds(),
			numMsec        : ins.getMilliseconds()
		};

	}
});
<?php }
}
?>