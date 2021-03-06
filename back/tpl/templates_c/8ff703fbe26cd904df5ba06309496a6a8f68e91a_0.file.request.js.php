<?php /* Smarty version 3.1.24, created on 2019-10-06 06:26:37
         compiled from "/app/rucaro/back/tpl/templates/else/core/base/js/lib/request.js" */ ?>
<?php
/*%%SmartyHeaderCode:15192851265d99891d3965c2_13179102%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8ff703fbe26cd904df5ba06309496a6a8f68e91a' => 
    array (
      0 => '/app/rucaro/back/tpl/templates/else/core/base/js/lib/request.js',
      1 => 1570328741,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15192851265d99891d3965c2_13179102',
  'variables' => 
  array (
    'varsLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d99891d3bb8c2_33832890',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d99891d3bb8c2_33832890')) {
function content_5d99891d3bb8c2_33832890 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '15192851265d99891d3965c2_13179102';
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