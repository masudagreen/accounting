<?php /* Smarty version 3.1.24, created on 2016-08-20 07:29:55
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/_80_extChoice.js" */ ?>
<?php
/*%%SmartyHeaderCode:120393204257b806f34994e5_19876106%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fe7e9c1e8891869af8142fbe4d4f3071a16a0693' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/_80_extChoice.js',
      1 => 1471523677,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '120393204257b806f34994e5_19876106',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b806f34dedb7_74684006',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b806f34dedb7_74684006')) {
function content_57b806f34dedb7_74684006 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '120393204257b806f34994e5_19876106';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_ExtChoice = Class.create(Code_Lib_ExtPortal,
{

	strFunc : null,
	insReturn : null,
	flagCheckUse : null,
	varsValue : null,

	/**
	 *
	*/
	_extReload : function(obj)
	{
		this.flagId = (obj.flagId)? obj.flagId : null;
		this.flagCheckUse = (obj.flagCheckUse)? obj.flagCheckUse : null;
		this.insReturn = (obj.insReturn)? obj.insReturn : null;
		this.strFunc = (obj.strFunc)? obj.strFunc : null;
		this.varsValue = (obj.varsValue)? obj.strFunc : null;

		this._setVarsFlagCheckUse();
		this.insList.insTree.iniReload();
	},

	/**
	 *
	*/
	_setVarsFlagCheckUse : function()
	{
		if (this.flagCheckUse) {
			this.vars.portal.varsList.tree.varsDetail.varsStatus.flagCheckUse = 1;
			this.vars.portal.varsList.tree.varsDetail.varsStatus.flagBtnUse = 0;
			if (this.insList) {
				this.insList.vars.tree.varsDetail.varsStatus.flagCheckUse = 1;
				this.insList.vars.tree.varsDetail.varsStatus.flagBtnUse = 0;
				this.insList.insTree.vars.varsStatus.flagCheckUse = 1;
				this.insList.insTree.vars.varsStatus.flagBtnUse = 0;
			}

		} else {
			this.vars.portal.varsList.tree.varsDetail.varsStatus.flagCheckUse = 0;
			this.vars.portal.varsList.tree.varsDetail.varsStatus.flagBtnUse = 1;
			if (this.insList) {
				this.insList.vars.tree.varsDetail.varsStatus.flagCheckUse = 0;
				this.insList.vars.tree.varsDetail.varsStatus.flagBtnUse = 1;
				this.insList.insTree.vars.varsStatus.flagCheckUse = 0;
				this.insList.insTree.vars.varsStatus.flagBtnUse = 1;
			}
		}
	},

	/**
	 *
	*/
	_getLayoutAllot : function()
	{
		var allot =  function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == 'eventLayout') {
				if (insCurrent.insNavi) insCurrent.insNavi.eventLayout();
				if (insCurrent.insList) insCurrent.insList.eventLayout();

			} else if (obj.from == 'navi-_mousedownNavi') {
				if (obj.vars.id == 'Switch') {
					insCurrent.insNavi.eventTool({
						idTarget : insCurrent.insNavi.vars.varsStatus.flagNow,
						flagLoop : 1
					});

				} else if (obj.vars.id == 'Reload') {
					insCurrent._eventNaviConnect({vars : obj.vars, flag : insCurrent.insNavi.vars.varsStatus.flagNow + '-reload'});
				}

			} else if (obj.from == 'navi-_mousedownMenu') {
				return insCurrent.insNavi.vars.varsStatus.flagNow;

			} else if (obj.from == 'navi-_mousedownLine') {
				insCurrent.insNavi.eventTool({idTarget : obj.vars});

			} else if (obj.from == 'list-_mousedownNavi') {
				if (obj.vars.id == 'Reload') {
					insCurrent._eventListConnect({flag : obj.vars.id});
				}
			}
		};

		return allot;
	},

	/**
	 *
	*/
	_sendNaviConnect : function()
	{

		var jsonStamp = {};
		var flag = this._varsNaviConnect.flag;
		var arrayKey = [], arrayValue = [];
		var jsonSearch = Object.toJSON(this._varsSearch);
		var insEscape = new Code_Lib_Escape();


		if (this._varsNaviConnect.flag == 'search' || this._varsNaviConnect.flag.match(/^folder(.*?)-search$/)) {
			var strFunc = 'NaviSearch';
			jsonStamp = this._getJsonStamp({strFunc : strFunc});
			if (this.varsValue) this._eventValue(this.varsValue);
			var jsonValue = Object.toJSON(this._varsValue);

			arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonSearch', 'jsonValue'];
			arrayValue = [this.strClass, this.idModule, this.strExt, this.strChild, strFunc, 'slave', jsonStamp, jsonSearch, jsonValue];

		} else if (this._varsNaviConnect.flag.match(/^folder(.*?)-reload$/)) {
			var strFunc = '';
			if (RegExp.$1) strFunc = 'NaviFolder' + insEscape.strCapitalize({data : RegExp.$1}) + 'Reload';
			else strFunc = 'NaviFolderReload';
			jsonStamp = this._getJsonStamp({strFunc : strFunc});
			arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp'];
			arrayValue = [this.strClass, this.idModule, this.strExt, this.strChild, strFunc, 'slave', jsonStamp];
			this.insLayout.updateTool({flagLock : 1, from : 'navi', idTarget : 'Reload'});

		} else if (this._varsNaviConnect.flag.match(/^folder(.*?)-save$/)) {
			var strFunc = '';
			if (RegExp.$1) strFunc = 'NaviFolder' + insEscape.strCapitalize({data : RegExp.$1}) + 'Save';
			else strFunc = 'NaviFolderSave';
			this._eventValue({vars : this._varsNaviConnect.vars});
			var jsonValue = Object.toJSON(this._varsValue);
			arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonValue'];
			arrayValue = [this.strClass, this.idModule, this.strExt, this.strChild, strFunc, 'master', jsonStamp, jsonValue];

		} else if (this._varsNaviConnect.flag == 'search-reload') {
			var strFunc = 'NaviSearchReload';
			jsonStamp = this._getJsonStamp({strFunc : strFunc});
			arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp'];
			arrayValue = [this.strClass, this.idModule, this.strExt, this.strChild, strFunc, 'slave', jsonStamp];
			this.insLayout.updateTool({flagLock : 1, from : 'navi', idTarget : 'Reload'});

		} else if (this._varsNaviConnect.flag == 'search-save') {
			this._eventValue({vars : this._varsNaviConnect.vars});
			var jsonValue = Object.toJSON(this._varsValue);
			var strFunc = 'NaviSearchSave';
			arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonValue'];
			arrayValue = [this.strClass, this.idModule, this.strExt, this.strChild, strFunc, 'master', jsonStamp, jsonValue];

		} else if (this._varsNaviConnect.flag == 'search-delete') {
			this._eventValue({vars : this._varsNaviConnect.vars});
			var jsonValue = Object.toJSON(this._varsValue);
			var strFunc = 'NaviSearchDelete';
			arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonValue'];
			arrayValue = [this.strClass, this.idModule, this.strExt, this.strChild, strFunc, 'master', jsonStamp, jsonValue];

		}

		this.insRoot.insRequest.set({
			flagLock        : 0,
			numZIndex       : this.insRoot.getZIndex(),
			insCurrent      : this,
			flagEscape      : 1,
			path            : this.insRoot.vars.varsSystem.path.post,
			querysKey       : arrayKey,
			querysValue     : arrayValue,
			functionSuccess : '_sendNaviConnectSuccess',
			functionFail    : '_sendNaviConnectFail',
			eleLoadStatus   : this.insNavi.insUnder.eleFormat.header.down('.codeLibBaseMarginRightFive', 0)
		});
	},

	/**
	 *
	*/
	_eventNaviConnectSuccess : function(obj)
	{
		var insEscape = new Code_Lib_Escape();
		if (obj.json.flag == 1){
			if (this._varsNaviConnect.flag == 'search') {
				this.insList.resetVars({vars : obj.json.data, numLotNow : this._varsSearch.numLotNow});
				this.insList.eventNavi({strTitle : null, strClass : null});

			} else if (this._varsNaviConnect.flag.match(/^folder(.*?)-search$/)) {
				this.insList.resetVars({vars : obj.json.data, numLotNow : this._varsSearch.numLotNow});
				this.insList.eventNavi({strTitle : null, strClass : null});

			} else if (this._varsNaviConnect.flag.match(/^folder(.*?)-save$/)
				|| this._varsNaviConnect.flag.match(/^format(.*?)-save$/)
			) {
				this.insNavi.updateFolderVars({vars : obj.json.data});

			} else if (this._varsNaviConnect.flag.match(/^folder(.*?)-reload$/)
				|| this._varsNaviConnect.flag.match(/^format(.*?)-reload$/)
			) {
				this.insLayout.updateTool({flagLock : 0, from : 'navi', idTarget : 'Reload'});
				this.insNavi.updateFolderVars({vars : obj.json.data});
				if (obj.json.stamp) {
					var data = (Object.toJSON(obj.json.data)).evalJSON();
					if (obj.json.stamp.id) this._varsStampCheck[obj.json.stamp.id] = data;
				}

			} else if (this._varsNaviConnect.flag == 'search-save' || this._varsNaviConnect.flag == 'search-delete') {

				this.insNavi.updateSearchVarsSave({vars : obj.json.data});

			} else if (this._varsNaviConnect.flag == 'search-reload') {
				this.insLayout.updateTool({flagLock : 0, from : 'navi', idTarget : 'Reload'});
				this.insNavi.updateSearchVars({vars : obj.json.data});
				if (obj.json.stamp) {
					var data = (Object.toJSON(obj.json.data)).evalJSON();
					if (obj.json.stamp.id) this._varsStampCheck[obj.json.stamp.id] = data;
				}

			} else if (this._varsNaviConnect.flag == 'tree-reload') {
				this.insLayout.updateTool({flagLock : 0, from : 'navi', idTarget : 'Reload'});
				this.insNavi.updateTreeVars({vars : obj.json.data});
				if (obj.json.stamp) {
					var data = (Object.toJSON(obj.json.data)).evalJSON();
					if (obj.json.stamp.id) this._varsStampCheck[obj.json.stamp.id] = data;
				}
			}

		} else if (obj.json.flag == 10) {
			if (this._varsNaviConnect.flag.match(/^folder(.*?)-reload$/)
				|| this._varsNaviConnect.flag.match(/^format(.*?)-reload$/)
				|| this._varsNaviConnect.flag == 'tree-reload'
			) {
				this.insLayout.updateTool({flagLock : 0, from : 'navi', idTarget : 'Reload'});
				if (obj.json.stamp) {
					this.insNavi.updateFolderVars({vars : this._varsStampCheck[obj.json.stamp.id]});
				}

			} else if (this._varsNaviConnect.flag == 'search-reload') {
				this.insLayout.updateTool({flagLock : 0, from : 'navi', idTarget : 'Reload'});
				if (obj.json.stamp) {
					this.insNavi.updateSearchVars({vars : this._varsStampCheck[obj.json.stamp.id]});

				}
			}
		} else {
			if (obj.json) {
				if (obj.json.flag) {
					if (this.insRoot.vars.varsSystem.str[obj.json.flag]) {
						alert(this.insRoot.vars.varsSystem.str[obj.json.flag]);
					}
				}
			}
		}

	},

	/**
	 *
	*/
	_getListAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			var array = obj.from.split('-');
			if (array[0] == 'tree') {
				if (array[1] == '_mousedownMove') insCurrent.insNavi.eventMove({vars : obj.vars});
				else if (array[1] == 'eventPage') insCurrent._eventListConnect({vars : obj.vars, flag : 'Search'});
				else if (array[1] == '_dblclickBtn') insCurrent._getListVars();
				else if (array[1] == 'eventBtnBottom') insCurrent._getListVars();
			}
		};

		return allot;
	},


	/**
	 *
	*/
	_getListVars : function(obj)
	{
		this.insWindow.hideLockWindow();
		var vars;
		if (this.flagCheckUse) {
			this.insList.insTree.setCheckAllValue();
			vars = this._getListVarsChild({
				arr : this.insList.insTree.vars.varsDetail
			});

		} else {
			vars = this.insList.insTree.getBtnSelect();

		}
		this.insReturn[this.strFunc]({vars : vars, flagId : this.flagId});
	},


	/**
	 *
	*/
	_getListVarsChild : function(obj)
	{

		var array = [];
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagUse) continue;
			if (obj.arr[i].flagCheckUse) {
				if (obj.arr[i].flagCheckNow) {
					array.push(obj.arr[i]);
				}
			}
		}

		return array;
	},

	/**
	 *
	*/
	_sendListConnect : function(obj)
	{
		var arrayKey = [], arrayValue = [];
		var jsonSearch = Object.toJSON(this._varsSearch);
		var jsonStamp = {};
		this._eventValue(this.varsValue);
		var jsonValue = Object.toJSON(this._varsValue);
		if (this._varsListConnect.flag == 'Delete') {
			var strFunc = 'ListDelete';
			arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonValue', 'jsonSearch'];
			arrayValue = [this.strClass, this.idModule, this.strExt, this.strChild, strFunc, 'master', jsonStamp, jsonValue, jsonSearch];

		} else if (this._varsListConnect.flag == 'Search') {
			var strFunc = 'ListReload';
			jsonStamp = this._getJsonStamp({strFunc : strFunc});
			arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonValue', 'jsonSearch'];
			arrayValue = [this.strClass, this.idModule, this.strExt, this.strChild, strFunc, 'slave', jsonStamp, jsonValue, jsonSearch];

		} else if (this._varsListConnect.flag == 'Reload') {
			var strFunc = 'ListReload';
			jsonStamp = this._getJsonStamp({strFunc : strFunc});
			this.insLayout.updateTool({flagLock : 1, from : 'list', idTarget : 'Reload'});
			arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonValue', 'jsonSearch'];
			arrayValue = [this.strClass, this.idModule, this.strExt, this.strChild, strFunc, 'slave', jsonStamp, jsonValue, jsonSearch];
		}


		this.insRoot.insRequest.set({
			flagLock        : 0,
			numZIndex       : this.insRoot.getZIndex(),
			insCurrent      : this,
			flagEscape      : 1,
			path            : this.insRoot.vars.varsSystem.path.post,
			querysKey       : arrayKey,
			querysValue     : arrayValue,
			functionSuccess : '_sendListConnectSuccess',
			functionFail    : '_sendListConnectFail',
			eleLoadStatus   : this.insList.insUnder.eleFormat.header.down('.codeLibBaseMarginRightFive', 0)
		});
	}


});

<?php }
}
?>