<?php /* Smarty version 3.1.24, created on 2022-08-13 00:23:38
         compiled from "/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/folder.js" */ ?>
<?php
/*%%SmartyHeaderCode:47828219562f6ef0a1ac400_55581258%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5c78d633efbdacbb5f238c85ec0f9bc77422cdf9' => 
    array (
      0 => '/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/folder.js',
      1 => 1375456172,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '47828219562f6ef0a1ac400_55581258',
  'variables' => 
  array (
    'varsLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_62f6ef0a1c0de3_98273486',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_62f6ef0a1c0de3_98273486')) {
function content_62f6ef0a1c0de3_98273486 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '47828219562f6ef0a1ac400_55581258';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Folder = Class.create(Code_Lib_ExtLib,
{

	varsLoad : <?php echo $_smarty_tpl->tpl_vars['varsLoad']->value;?>
,

	/**
	 *
	*/
	initialize : function(obj)
	{

		this._extAllot(obj);
		this._iniVars(obj);
		this._iniWrap();
		this._iniTree();

	},

	/**
	 *
	*/
	iniReload : function()
	{
		this.stopListener();
		this.removeWrap();
		this._iniWrap();
		this._iniTree();
	},

	/**
	 *
	*/
	stopListener : function()
	{
		this.insTree.stopListener();
	},

	/**
	 *
	*/
	iniReset : function()
	{
		this.vars.varsTree.varsDetail = this.vars.varsDetail;
		this.iniReload();
	},

	/**
	 *
	*/
	_iniVars : function(obj)
	{
		this._extVars(obj);
		this.vars.varsTree.varsDetail = this.vars.varsDetail;
	},

	/**
	 * Wrap
	*/
	_iniWrap : function()
	{
		this._extWrap();
		this.eleWrap.style.width = this._getWrapWidth() + 'px';
		this.eleWrap.style.height = this._getWrapHeight() + 'px';
	},

	/**
	 * Tree
	*/
	insTree : null,
	_iniTree : function()
	{
		this._setTree();
	},

	/**
	 *
	*/
	_setTree : function()
	{
		this.insTree = new Code_Lib_Tree({
			eleInsertBtnLeft  : this.eleInsertBtnLeft,
			eleInsertBtnRight : this.eleInsertBtnRight,
			eleInsert         : this.eleWrap,
			insRoot           : this.insRoot,
			insCurrent        : this.insSelf,
			idSelf            : this.idSelf + 'Tree',
			allot             : this._getTreeAllot(),
			vars              : this.vars.varsTree
		});
	},

	/**
	 *
	*/
	_varsTreeOutside : null,
	_varsTreePage : null,
	_getTreeAllot : function()
	{
		var allot = function(obj)
		{

			var insCurrent = obj.insCurrent;
			if(obj.from == '_mousedownBtn') {
				insCurrent.allot({
					insCurrent : insCurrent.insCurrent,
					from       : obj.from,
					vars       : obj.vars.vars
				});

			} else if (obj.from == '_mousedownRemove') {
				insCurrent.vars.varsTree.varsDetail = insCurrent.insTree.vars.varsDetail;
				return 1;

			} else if (obj.from.match( /^_preMouseupMoveEventMove$/ )) {


			} else if (obj.from.match( /^_blurEdit|_mouseupMove$/ )) {
				insCurrent.vars.varsTree.varsDetail = insCurrent.insTree.vars.varsDetail;

			} else if (obj.from.match( /^_mouseupMoveEventMove$/ )) {

			} else {
				if(obj.from == 'eventBtnBottom') {
					if (obj.vars.vars.vars.idTarget == 'eventFormBtnSave') {
						if (insCurrent.vars.varsTree.varsDetail) {
							var len = (Object.toJSON(insCurrent.vars.varsTree.varsDetail)).length;
							if (len >= insCurrent.varsLoad.varsWhole.num.limit) {
								alert(insCurrent.varsLoad.varsWhole.str.limit);
								return;
							}
							insCurrent.vars.varsTree.varsDetail = insCurrent.insTree.vars.varsDetail;
							obj.vars = insCurrent.vars.varsTree.varsDetail;
						} else {
							obj.vars = [];
						}

					} else if (obj.vars.vars.vars.idTarget == 'eventFormBtnAdd') {
						obj.from = obj.from + '-' + obj.vars.vars.vars.idTarget;
						obj.insCurrent = insCurrent.insCurrent;
						var vars = insCurrent.allot(obj);
						insCurrent._addTreeVars({vars : vars});
						insCurrent.showBtnBottom();
						return;
					}
				}
				obj.from = obj.from;
				obj.insCurrent = insCurrent.insCurrent;
				insCurrent.allot(obj);
			}
		};

		return allot;
	},

	/**
	 *
	*/
	eventBtnSave : function()
	{
		var obj = {};
		if (this.vars.varsTree.varsDetail) {
			var len = (Object.toJSON(this.vars.varsTree.varsDetail)).length;
			if (len >= this.varsLoad.varsWhole.num.limit) {
				alert(this.varsLoad.varsWhole.str.limit);
				return;
			}
			this.vars.varsTree.varsDetail = this.insTree.vars.varsDetail;
			obj.vars = this.vars.varsTree.varsDetail;
		} else {
			obj.vars = [];
		}
		obj.from = 'eventBtnBottom';
		obj.insCurrent = this.insCurrent;
		this.hideBtnBottom();
		this.allot(obj);
	},

	/**
	 *
	*/
	setVarsTreePast : function(obj)
	{
		this.insTree.vars.varsDetail[0] = obj.vars;
		this.insTree.iniReload();
		this.vars.varsTree.varsDetail[0] = this.insTree.vars.varsDetail[0];
	},

	/**
	 *
	*/
	getVarsTreePast : function(obj)
	{
		return this.insTree.vars.varsDetail[0];
	},

	/**
	 *
	*/
	addLog : function(obj)
	{
		this._updateLogVars({arr : this.insTree.vars.varsDetail, vars : obj.vars});
		this.insTree.vars.varsDetail = this.insTree.getBlockLevel();
		this.insTree.iniReload();
		this.vars.varsTree.varsDetail = this.insTree.vars.varsDetail;
	},

	/**
	 *
	*/
	_updateLogVars : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].flagFoldUse) {
				obj.arr[i].child = this._updateLogVarsChild({arr : obj.arr[i].child, vars : obj.vars});
				return;
			}
		}
	},

	/**
	 *
	*/
	_updateLogVarsChild : function(obj)
	{
		var insDisplay = new Code_Lib_TimeDisplay();
		var objData = (Object.toJSON(this.vars.templateDetail.log)).evalJSON();
		var objTime = this.insRoot.insTimeZone.adjustDate({stamp : new Date().getTime()});
		var strTime = insDisplay.get({
			flagType : 3,
			vars     : objTime
		});
		objData.strTitle = strTime;
		objData.vars = obj.vars.vars;

		var arrNew = [objData];
		for (var i = 0; i < obj.arr.length; i++) {
			var num = i + 1;
			if (num >= this.varsLoad.varsWhole.num.limitLog) {
				return arrNew;
			}
			arrNew.push(obj.arr[i]);
		}
		return arrNew;
	},

	/**
	 *
	*/
	addVars : function(obj)
	{
		this._addTreeVars(obj);
	},

	/**
	 *
	*/
	_addTreeVars : function(obj)
	{
		var num = this._checkVarsFileNum({arr : this.insTree.vars.varsDetail});
		if (num > this.varsLoad.varsWhole.num.limitFile) {
			var varsData = (Object.toJSON(this.varsLoad.varsWhole.str.limitFile)).evalJSON();
			varsData = varsData.replace(/<?php echo '<%'; ?>
replace<?php echo '%>'; ?>
/, this.varsLoad.varsWhole.num.limitFile);
			alert(varsData);
			return;
		}
		var data = this._setVarsFile(obj);
		this.insTree.vars.varsDetail.push(data);
		this.insTree.vars.varsDetail = this.insTree.getBlockLevel();
		this.insTree.iniReload();
		this.vars.varsTree.varsDetail = this.insTree.vars.varsDetail;
	},

	/**
	 *
	*/
	_checkVarsFileNum : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			num++;
		}

		return num;
	},

	/**
	 *
	*/
	_setVarsFile : function(obj)
	{
		this.insEscape = new Code_Lib_Escape();
		var objData = (Object.toJSON(this.vars.templateDetail.file)).evalJSON();
		var strTitle = this.insEscape.get({data : obj.vars.strTitle, flagType : 'fromTag'});
		if (!obj.vars.strTitle) {
			strTitle = this._checkVarsFIleName({
				flagFile : 1,
				arr      : this.insTree.vars.varsDetail
			});
		}
		objData.strTitle = strTitle;
		objData.vars = obj.vars.vars;

		return objData;
	},

	/**
	 *
	*/
	_checkVarsFIleName : function(obj)
	{
		var flag = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if ((obj.flagFile && !obj.arr[i].flagInsertUse) || (!obj.flagFile && obj.arr[i].flagInsertUse)) {
				var array = obj.arr[i].strTitle.split('-');
				if (array[0] == this.varsLoad.varsWhole.str.noneTitle) {
					flag = 1;
				}

			}
		}
		if (!flag) return this.varsLoad.varsWhole.str.noneTitle;
		var data = {};
		var numAll = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if ((obj.flagFile && !obj.arr[i].flagInsertUse) || (!obj.flagFile && obj.arr[i].flagInsertUse)) {
				var array = obj.arr[i].strTitle.split('-');

				if (array[0] != this.varsLoad.varsWhole.str.noneTitle) continue;
				if (array[1] == undefined) continue;
				var str = 'id'+ array[1];
				data[str] = 1;
				numAll++;
			}
		}

		for (var i = 1; i < obj.arr.length + 1; i++) {
			var str = 'id'+ i;
			if (!data[str]) {
				var data = this.varsLoad.varsWhole.str.noneTitle + '-' + i;
				return data;
			}

		}
		var str = this.varsLoad.varsWhole.str.noneTitle + '-' + (obj.arr.length + 1);

		return str;
	},

	/**
	 *
	*/
	eventMove : function(obj)
	{

	},

	/**
	 *
	*/
	showBtnBottom : function()
	{
		this.insTree.showBtnBottom();
	},

	/**
	 *
	*/
	hideBtnBottom : function()
	{
		this.insTree.hideBtnBottom();
	},

	/**
	 *
	*/
	cancelLock : function()
	{
		this.insTree.cancelLock();
	}
});
<?php }
}
?>