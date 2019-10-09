<?php /* Smarty version 3.1.24, created on 2016-08-20 07:30:13
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/request.js" */ ?>
<?php
/*%%SmartyHeaderCode:63402200657b80705be42d4_42231817%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4af790f0116906ae2a7b2cca711583013ff98c6e' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/request.js',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '63402200657b80705be42d4_42231817',
  'variables' => 
  array (
    'varsLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b80705bfc538_44938680',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b80705bfc538_44938680')) {
function content_57b80705bfc538_44938680 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '63402200657b80705be42d4_42231817';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Request = Class.create({

	varsLoad : <?php echo $_smarty_tpl->tpl_vars['varsLoad']->value;?>
,


	/**
	 * obj = {
	 * 	insRoot : instance
	 * }
	*/
	insRoot: null,
	initialize : function(obj)
	{
		this.insRoot = obj.insRoot;
	},

	/**
	 * obj = {
	 * 	flagLock        : int
	 * 	insCurrent      : instance
	 * 	path            : string
	 * 	querysKey       : []
	 * 	querysValue     : []
	 * 	functionSuccess : string
	 * 	functionFail    : string
	 * 	eleLoadStatus   : element
	 * }
	*/
	_vars : [],
	set : function(obj)
	{

		if (this.insRoot.removePrint) {
			this.insRoot.removePrint();
		}

		if (this._vars.length == this.varsLoad.varsWhole.num.limit && this.varsLoad.varsWhole.num.limit) {
			alert(this.varsLoad.varsWhole.str.limit);
			return;
		}
		var insLock = new Code_Lib_Lock();
		obj.cache = (new Date()).getTime();
		obj.insSelf = this;
		var data = {
			cache   : obj.cache,
			data    : obj,
			insLock : insLock
		};
		this._vars.push(data);

		if (obj.flagLock) {
			insLock.setLock({
				action    : 'wait',
				idInsert  : this.insRoot.vars.varsSystem.id.root,
				numZIndex : this.insRoot.vars.varsSystem.num.zIndex,
			});
		}
		if (data.data.eleLoadStatus) {
			data.data.eleLoadStatus.addClassName('codeLibRequestImgLoading');
		}

		var objQuery = {};
		objQuery.cache = obj.cache;
		objQuery.token = (this.insRoot.vars.varsSystem.token)? this.insRoot.vars.varsSystem.token : '';
		var insEscape = new Code_Lib_Escape();
		for (var i=0; i<obj.querysKey.length; i++) {
			var str = insEscape.get({
				flagType : 'strUnique',
				data     : obj.querysValue[i]
			});
			objQuery[obj.querysKey[i]] = insEscape.get({
				flagType : 'toTag',
				data     : str
			});
		}
		var query = $H(objQuery).toQueryString();
		new Ajax.Request(obj.path,{
			method     : 'post',
			parameters : query,
			evalJS     : false,
			onSuccess  : function(response) {
				obj.insCurrent[obj.functionSuccess]({response : response});
				obj.insSelf._setSuccess({cache : obj.cache});
			},
			onFailure:function(response) {
				obj.insCurrent[obj.functionFail]({response : response});
				obj.insSelf._setFail({cache : obj.cache});
			}
		});
	},

	/**
	 *
	*/
	_setSuccess : function(obj)
	{
		this._vars = this._removeVars({arr : this._vars, cache : obj.cache});
	},

	/**
	 *
	*/
	_setFail : function(obj)
	{
		this._vars = this._removeVars({arr : this._vars, cache : obj.cache});
	},

	/**
	 *
	*/
	_removeVars : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].cache == obj.cache) {
				num = i;
			}
		}
		if (obj.arr[num].data.eleLoadStatus) {
			obj.arr[num].data.eleLoadStatus.removeClassName('codeLibRequestImgLoading');
		}
		if (obj.arr[num].data.flagLock) {
			obj.arr[num].insLock.removeLock();
		}
		obj.arr = obj.arr.slice(0, num).concat(obj.arr.slice((num + 1), obj.arr.length));

		return (!obj.arr)? [] : obj.arr;
	}
});
<?php }
}
?>