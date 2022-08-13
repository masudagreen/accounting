<?php /* Smarty version 3.1.24, created on 2022-08-13 00:23:38
         compiled from "/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/listener.js" */ ?>
<?php
/*%%SmartyHeaderCode:95630741762f6ef0a3a81d2_35660950%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9e36dd5518b26c56d68550d9425cac4cd304114b' => 
    array (
      0 => '/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/listener.js',
      1 => 1329210274,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '95630741762f6ef0a3a81d2_35660950',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_62f6ef0a3ad6d3_57227426',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_62f6ef0a3ad6d3_57227426')) {
function content_62f6ef0a3ad6d3_57227426 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '95630741762f6ef0a3a81d2_35660950';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Listener = Class.create({

	/**
	 *
	*/
	vars : null,
	initialize : function()
	{
		this.vars = [];
	},

	/**
	 * obj = {
	 * 	flagType15  : int
	 * 	bindAsEvent : string
	 * 	ele         : element
	 * 	event       : string
	 * 	insCurrent  : instance
	 * 	vars    : object
	 * 	strFunc     : string
	 * 	bindAsEvent : string
	 * }
	*/
	set : function(obj)
	{
		var wrapper;
		if(!obj.flagType15) obj.flagType15 = 0;
		if(obj.flagType15) {
			wrapper = obj.insCurrent[obj.strFunc].bind(obj.insCurrent, obj.vars);
			Event.observe(obj.ele, obj.event, wrapper);
		} else {
			if(obj.bindAsEvent) {
				if(obj.vars) {
					wrapper = obj.insCurrent[obj.strFunc].bindAsEventListener(obj.insCurrent, obj.vars);
				} else {
					wrapper = obj.insCurrent[obj.strFunc].bindAsEventListener(obj.insCurrent);
				}
				obj.ele.observe(obj.event, wrapper);
			} else {
				if(obj.vars) {
					wrapper = obj.insCurrent[obj.strFunc].bind(obj.insCurrent, obj.vars);
				} else {
					wrapper = obj.insCurrent[obj.strFunc].bind(obj.insCurrent);
				}
				obj.ele.observe(obj.event, wrapper);
			}
		}
		var data = {
			flagType15 : obj.flagType15,
			insCurrent : obj.insCurrent,
			strFunc    : obj.strFunc,
			ele        : obj.ele,
			event      : obj.event,
			wrapper    : wrapper
		};
		this.vars.push(data);
	},

	/**
	 *
	*/
	stop : function()
	{
		this._stopChild({arr : this.vars});
		this.reset();
	},

	/**
	 *
	*/
	_stopChild : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if(obj.arr[i].flagType15) {
				Event.stopObserving(obj.arr[i].ele, obj.arr[i].event, obj.arr[i].wrapper);
			} else {
				obj.arr[i].ele.stopObserving(obj.arr[i].event, obj.arr[i].wrapper);
			}
		}
	},

	/**
	 *
	*/
	reset : function()
	{
		this.vars = [];
	}
});

<?php }
}
?>