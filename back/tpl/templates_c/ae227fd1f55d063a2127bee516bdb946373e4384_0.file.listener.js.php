<?php /* Smarty version 3.1.24, created on 2016-08-18 12:50:22
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/listener.js" */ ?>
<?php
/*%%SmartyHeaderCode:57297096857b5af0e4ca9e5_26484601%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ae227fd1f55d063a2127bee516bdb946373e4384' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/listener.js',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '57297096857b5af0e4ca9e5_26484601',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b5af0e4f4589_80170473',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b5af0e4f4589_80170473')) {
function content_57b5af0e4f4589_80170473 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '57297096857b5af0e4ca9e5_26484601';
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