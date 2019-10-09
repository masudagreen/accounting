<?php /* Smarty version 3.1.24, created on 2016-08-20 07:29:52
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/_60_extPortal.js" */ ?>
<?php
/*%%SmartyHeaderCode:330976257b806f0542f60_75060485%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7d8a58e0736a15b28d0480d342fe13e6dd5c9138' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/_60_extPortal.js',
      1 => 1471523677,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '330976257b806f0542f60_75060485',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b806f060d5a4_16000365',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b806f060d5a4_16000365')) {
function content_57b806f060d5a4_16000365 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '330976257b806f0542f60_75060485';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_ExtPortal = Class.create({

	insRoot : null,
	idSelf : null,
	insSelf : null,
	insTop : null,
	insCurrent : null,
	insWindow : null,
	eleInsert : null,
	strClass : null,
	idModule : null,
	strExt : null,
	strChild : null,
	varsChild : null,
	_varsStamp : {},
	_varsStampCheck : {},

	/**
	 *
	*/
	_extVars : function(obj)
	{
		this.eleInsert = (obj.eleInsert)? obj.eleInsert : null;
		this.insRoot = (obj.insRoot)? obj.insRoot : null;
		this.idSelf = (obj.idSelf)? obj.idSelf : null;
		this.insSelf = this;
		this.insTop = (obj.insTop)? obj.insTop : null;
		this.strClass = (obj.strClass)? obj.strClass : null;
		this.idModule = (obj.idModule)? obj.idModule : null;
		this.strExt = (obj.strExt)? obj.strExt : null;
		this.strChild = (obj.strChild)? obj.strChild : '';
		this.insWindow = (obj.insWindow)? obj.insWindow : null;
		this.insCurrent = (obj.insCurrent)? obj.insCurrent : null;
		this.varsChild = (obj.varsChild)? (Object.toJSON(obj.varsChild)).evalJSON() : null;
		this._resetVarChild();
		this._iniSearch();
		if (this.stamp) this.insRoot.updateStamp({arr : this.stamp});
		this._varsStamp = {};
		this._varsStampCheck = {};

	},

	/**
	 *
	*/
	insLayout : null,
	_extLayout : function()
	{
		this.insLayout = new Code_Lib_TemplateLayout({
			eleInsert  : this.eleInsert,
			idWindow   : this.insWindow.idWindow,
			insRoot    : this.insRoot,
			insCurrent : this.insSelf,
			idSelf     : this.idSelf + 'Layout',
			allot      : this._getLayoutAllot(),
			vars       : this.vars.portal.varsTemplateLayout
		});
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
				if (insCurrent.insDetail) insCurrent.insDetail.eventLayout();

			} else if (obj.from == 'preEventLayout') {
				insCurrent._preEventLayout({flag : 'dummy'});

			} else if (obj.from == 'navi-_mousedownNavi') {
				if (obj.vars.id == 'Switch') {
					insCurrent.insNavi.eventTool({
						idTarget : insCurrent.insNavi.vars.varsStatus.flagNow,
						flagLoop : 1
					});

				} else if (obj.vars.id == 'Reload') {
					insCurrent._preEventLayout({flag : 'reset'});
					insCurrent._eventNaviConnect({vars : obj.vars, flag : insCurrent.insNavi.vars.varsStatus.flagNow + '-reload'});

				} else if (obj.vars.id == 'Preference') {
					insCurrent._iniChild({
						strTitleParent  : insCurrent.insWindow.vars.strTitle,
						strTitleChild   : insCurrent.vars.child.varsTitle[obj.vars.id],
						strExt          : obj.vars.id,
						strChild        : '',
						strClass        : insCurrent.strClass,
						idModule        : insCurrent.idModule
					});
				}

			} else if (obj.from == 'navi-_mousedownMenu') {
				return insCurrent.insNavi.vars.varsStatus.flagNow;

			} else if (obj.from == 'navi-_mousedownLine') {
				insCurrent.insNavi.eventTool({idTarget : obj.vars});

			} else if (obj.from == 'list-_mousedownNavi') {
				if (obj.vars.id == 'Switch') {
					insCurrent.insList.eventTool({
						idTarget : insCurrent.insList.vars.varsStatus.flagNow,
						flagLoop : 1
					});

				} else if (obj.vars.id == 'Reload') {
					insCurrent._preEventLayout({flag : 'reset'});
					insCurrent._eventListConnect({flag : obj.vars.id});

				} else if (obj.vars.id == 'Output') {
					insCurrent._eventListConnect({flag : obj.vars.id, flagType : insCurrent.insList.vars.varsStatus.flagOutputNow});

				} else if (obj.vars.id == 'Print') {
					insCurrent._eventListConnect({flag : obj.vars.id, flagType : insCurrent.insList.vars.varsStatus.flagPrintNow});

				}

			} else if (obj.from == 'list-_mousedownMenu') {
				if (obj.vars.id == 'Switch') {
					return insCurrent.insList.vars.varsStatus.flagNow;

				} else if (obj.vars.id == 'Output') {
					return insCurrent.insList.vars.varsStatus.flagOutputNow;

				} else if (obj.vars.id == 'Print') {
					return insCurrent.insList.vars.varsStatus.flagPrintNow;
				}

			} else if (obj.from == 'list-_mousedownLine') {
				if (obj.varsTarget == 'Switch') {
					return insCurrent.insList.eventTool({idTarget : obj.vars});

				} else if (obj.varsTarget) {
					insCurrent.insList.eventTool({idTarget : obj.vars, flagStr : obj.varsTarget});
					insCurrent._eventListConnect({flag : obj.varsTarget, flagType : obj.vars});
					return;
				}

			} else if (obj.from == 'detail-_mousedownNavi') {

				if (obj.vars.id == 'Reload') {
					insCurrent._preEventLayout({flag : 'reset'});
					insCurrent._eventDetailConnect({flag : 'reload'});

				} else if (obj.vars.id == 'Output') {
					insCurrent._eventDetailConnect({flag : 'output', flagType : insCurrent.insDetail.vars.varsStatus.flagOutputNow});

				} else if (obj.vars.id == 'Print') {
					insCurrent._eventDetailConnect({flag : obj.vars.id, flagType : insCurrent.insDetail.vars.varsStatus.flagPrintNow});

				} else if (obj.vars.id == 'Add'
						|| obj.vars.id == 'Copy'
						|| obj.vars.id == 'Edit'
				) {
					insCurrent._setDetailChild({flag : obj.vars.id.toLowerCase()});

				} else if (obj.vars.id == 'Preference') {
					insCurrent._iniChild({
						strTitleParent  : insCurrent.insWindow.vars.strTitle,
						strTitleChild   : insCurrent.vars.child.varsTitle[obj.vars.id],
						strExt          : obj.vars.id,
						flagMenuShowUse : 1,
						strChild        : '',
						strClass        : insCurrent.strClass,
						idModule        : insCurrent.idModule
					});
				}

			} else if (obj.from == 'detail-_mousedownMenu') {
				if (obj.vars.id == 'Switch') {
					return insCurrent.insDetail.vars.varsStatus.flagNow;

				} else if (obj.vars.id == 'Output') {
					return insCurrent.insDetail.vars.varsStatus.flagOutputNow;

				} else if (obj.vars.id == 'Print') {
					return insCurrent.insDetail.vars.varsStatus.flagPrintNow;
				}

			} else if (obj.from == 'detail-_mousedownLine') {
				if (obj.varsTarget == 'Switch') {
					return insCurrent.insDetail.eventTool({idTarget : obj.vars});

				} else if (obj.varsTarget) {
					insCurrent.insDetail.eventTool({idTarget : obj.vars, flagStr : obj.varsTarget});
					if (obj.varsTarget == 'Output') {
						insCurrent._eventDetailConnect({flag : 'output', flagType : obj.vars});

					} else {
						insCurrent._eventDetailConnect({flag : obj.varsTarget, flagType : obj.vars});
					}
					return;
				}

			}
		};

		return allot;
	},

	/**
	 *
	*/
	_preEventLayout : function(obj)
	{
		if (this.insNavi) this.insNavi.preEventLayout(obj);
		if (this.insList) this.insList.preEventLayout(obj);
		if (this.insDetail) this.insDetail.preEventLayout(obj);
	},

	/**
	 *
	*/
	_extNavi : function()
	{
		this._setNaviVars({arr : this.vars.portal.varsNavi.varsStatus.switchList});
		this._setNavi();
	},

	/**
	 *
	*/
	_setNaviVars : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].match(/^folder/)) {
				var str = obj.arr[i];
				var cut = this.vars.portal.varsNavi.varsFolder[str];
				var varsTmpl = (Object.toJSON(this.vars.portal.varsNavi.templateFolder)).evalJSON();
				varsTmpl.varsFormat.strTitleHeaderLeft = cut.strTitle;
				varsTmpl.varsDetail.varsDetail = cut.varsDetail;
				this.vars.portal.varsNavi[str] = varsTmpl;
			}
		}

	},

	/**
	 *
	*/
	insNavi : null,
	_setNavi : function()
	{
		this.insNavi = new Code_Lib_ControlNavi({
			insUnder   : this.insLayout.insNaviUnder,
			insTool    : this.insLayout.insNaviTool,
			insRoot    : this.insRoot,
			insCurrent : this,
			idSelf     : this.idSelf + 'Navi',
			allot      : this._getNaviAllot(),
			vars       : this.vars.portal.varsNavi
		});
	},

	/**
	 *
	*/
	_getNaviAllot : function()
	{
		var allot = function(obj)
		{

			var insCurrent = obj.insCurrent;
			var array = obj.from.split('-');
			var flagNow = array[0];
			var flagType = array[1];

			if (obj.from == 'search-eventBtn') insCurrent._eventNaviConnect({vars : obj.vars, flag : 'search'});
			else if (obj.from == 'search-eventBtnSave') insCurrent._eventNaviConnect({vars : obj.vars, flag : 'search-save'});
			else if (obj.from == 'search-eventBtnDelete') insCurrent._eventNaviConnect({vars : obj.vars, flag : 'search-delete'});
			else if (flagNow.match(/^folder/)) {
				if (flagType == '_mousedownBtn') insCurrent._eventNaviConnect({vars : obj.vars, flag : flagNow + '-search'});
				else if (flagType == 'eventBtnBottom') insCurrent._eventNaviConnect({vars : obj.vars, flag : flagNow + '-save'});
			}
			else if (obj.from == 'tree-_mousedownBtn') insCurrent._eventNaviDetail({vars : obj.vars});
			else if (obj.from == 'tree-_dblclickBtn') insCurrent._eventNaviDetail({flag : 'dblclick', vars : obj.vars});
			else if (obj.from == 'tree-eventPage') insCurrent._eventNaviConnect({vars : obj.vars, flag : 'tree-search'});
		};

		return allot;
	},

	/**
	 *
	*/
	_varsValue : null,
	_eventValue : function(obj)
	{
		if (!obj) return;
		this._varsValue = {
			vars     : (obj.vars)? obj.vars : '',
			idTarget : (obj.idTarget)? obj.idTarget : ''
		};
	},

	/**
	 *
	*/
	_eventNaviConnect : function(obj)
	{
		if (obj.flag == 'search') {
			this._eventSearch({
				numLotNow : 0,
				ph : {
					arrWhere : obj.vars.arrWhere,
					arrOrder : obj.vars.arrOrder
				}
			});

		} else if (obj.flag.match(/^folder(.*?)-search$/)) {
			this._eventSearch({
				numLotNow : 0,
				ph : {
					arrWhere : obj.vars.arrWhere,
					arrOrder : obj.vars.arrOrder
				}
			});
		}
		this._varsNaviConnect = obj;
		this._sendNaviConnect();
	},

	/**
		{
			strFunc  : ''
		}
	*/
	_getJsonStamp : function(obj)
	{
		var str = this.strClass
			+ '-' + this.idModule
			+ '-' + this.strExt
			+ '-' + this.strChild
			+ '-' + obj.strFunc;

		var stamp = (this._varsStamp[str])? this._varsStamp[str] : 0;
		var objStamp = {
			id    : str,
			stamp : stamp
		};
		var jsonStamp = (Object.toJSON(objStamp));

		return jsonStamp;
	},

	/**
	 *
	*/
	_varsNaviConnectLock : 0,
	_varsNaviConnect : null,
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
			arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonSearch'];
			arrayValue = [this.strClass, this.idModule, this.strExt, this.strChild, strFunc, 'slave', jsonStamp, jsonSearch];

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

		} else if (this._varsNaviConnect.flag.match(/^format(.*?)-reload$/)) {
			var strFunc = '';
			if (RegExp.$1) strFunc = 'NaviFormat' + insEscape.strCapitalize({data : RegExp.$1}) + 'Reload';
			else strFunc = 'NaviFormatReload';
			jsonStamp = this._getJsonStamp({strFunc : strFunc});
			arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp'];
			arrayValue = [this.strClass, this.idModule, this.strExt, this.strChild, strFunc, 'slave', jsonStamp];
			this.insLayout.updateTool({flagLock : 1, from : 'navi', idTarget : 'Reload'});

		} else if (this._varsNaviConnect.flag.match(/^format(.*?)-save$/)) {
			var strFunc = '';
			if (RegExp.$1) strFunc = 'NaviFormat' + insEscape.strCapitalize({data : RegExp.$1}) + 'Save';
			else strFunc = 'NaviFormatSave';
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
			jsonStamp = this._getJsonStamp({strFunc : strFunc});
			arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonValue'];
			arrayValue = [this.strClass, this.idModule, this.strExt, this.strChild, strFunc, 'master', jsonStamp, jsonValue];

		} else if (this._varsNaviConnect.flag == 'tree-reload') {
			var strFunc = 'NaviReload';
			jsonStamp = this._getJsonStamp({strFunc : strFunc});
			arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonSearch'];
			arrayValue = [this.strClass, this.idModule, this.strExt, this.strChild, strFunc, 'slave', jsonStamp, jsonSearch];
			this.insLayout.updateTool({flagLock : 1, from : 'navi', idTarget : 'Reload'});

		} else if (this._varsNaviConnect.flag == 'tree-search') {
			var strFunc = 'NaviSearch';
			arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonSearch'];
			arrayValue = [this.strClass, this.idModule, this.strExt, this.strChild, strFunc, 'slave', jsonStamp, jsonSearch];

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
	_sendNaviConnectFail : function(obj)
	{
		alert(this.insRoot.vars.varsSystem.str.errorConnect);
	},


	/**
	 *
	*/
	_sendNaviConnectSuccess : function(obj)
	{
		if (obj.response.responseText.isJSON()) {
			var json = obj.response.responseText.evalJSON();
			if (json.stamp) {
				if (json.stamp.id) this._varsStamp[json.stamp.id] = json.stamp.stamp;
			}
			this.insNavi.showBtn();
			if (json.flag) {
				if (json.numNews) this.insRoot.iniPopup({flag : 'news', numNews : json.numNews});
				this._eventNaviConnectSuccess({json : json});
			}
			else if (json.flag == 0) alert(this.insRoot.vars.varsSystem.str.errorSession);
		}
		else alert(this.insRoot.vars.varsSystem.str.errorRequest);
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
				if (obj.json.data.numRows) {
					this._eventDetailList({vars : obj.json.data.varsDetail[0]});
				}

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
	_extList : function()
	{
		this._checkListBtn({
			varsDetail : this.vars.portal.varsList.varsDetail,
			varsBtn    : this.vars.portal.varsList.varsBtn
		});
		if (this.vars.portal.varsList.varsEdit) {
			this._checkListToolEdit({
				varsDetail : this.vars.portal.varsList.varsDetail,
				varsEdit   : this.vars.portal.varsList.varsEdit
			});
		}
		this._setList();
		this._setListStart();
	},

	/**
	 *
	*/
	_checkListToolEdit : function(obj)
	{

	},

	/**
	 *
	*/
	insList : null,
	_setList : function()
	{
		this.insList = new Code_Lib_ControlList({
			insUnder   : this.insLayout.insListUnder,
			insTool    : this.insLayout.insListTool,
			insRoot    : this.insRoot,
			insCurrent : this,
			idSelf     : this.idSelf + 'List',
			allot      : this._getListAllot(),
			vars       : this.vars.portal.varsList
		});
		this.insList.resetVarsChild({arr : this.insList.vars.varsStatus.switchList});
	},

	/**
	 *
	*/
	_setListStart : function()
	{
		var cut = this.vars.portal.varsList.varsStart;
		this.insList.eventNavi({
			strTitle : cut.varsStatus.strTitle,
			strClass : cut.varsStatus.strClass,
			varsEdit : (cut.varsEdit)? cut.varsEdit : {}
		});
	},

	/**
	 *
	*/
	_eventListNavi : function(obj)
	{
		var objData = this._updateListNaviVars({vars : obj.vars});
		this.insDetail.eventList(objData);
	},

	/**
	 *
	*/
	_checkListBtn : function(obj)
	{
		var flag = this._checkListBtnChild({arr : obj.varsDetail});
		this._updateVarsBtnHide({
			arr     : obj.varsBtn,
			flagUse : flag
		});
	},

	/**
	 *
	*/
	_checkListBtnChild : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagDefault) return 1;
		}
		return 0;
	},

	/**
	 *
	*/
	_updateVarsBtnHide : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].vars.idTarget == 'Delete') {
				obj.arr[i].flagUse = obj.flagUse;
				return;
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
			if (array[0] == 'table') {
				if (array[1] == '_mousedownMove') insCurrent.insNavi.eventMove({vars : obj.vars});
				else if (array[1] == '_mousedownBtn') insCurrent._eventDetailList({vars : obj.vars[0]});
				else if (array[1] == 'eventPage') insCurrent._eventListConnect({vars : obj.vars, flag : 'Search'});
				else if (array[1] == 'eventBtnBottom') insCurrent._eventListConnect({flag : obj.vars.vars.vars.idTarget});

			} else if (array[0] == 'schedule') {
				if (array[1] == 'eventPage') insCurrent._eventListConnect({vars : obj.vars, flag : 'Search'});
				else if (array[1] == '_mousedownLogBtn') insCurrent._eventDetailList({vars : obj.vars.detailLog});
				else if (array[1] == 'mousedownMove') insCurrent.insNavi.eventMove({vars : obj.vars});

			} else if (array[0] == 'tableTree') {
				if (array[1] == '_mousedownBtn') insCurrent._eventDetailList({vars : obj.vars[0]});
				else if (array[1] == 'eventPage') insCurrent._eventListConnect({vars : obj.vars, flag : 'Search'});
				else if (array[1] == 'eventBtnBottom') insCurrent._eventListConnect({flag : obj.vars.vars.vars.idTarget});

			} else if (array[0] == 'tree') {
				if (array[1] == '_mousedownBtn') insCurrent._eventDetailList({vars : obj.vars});
				else if (array[1] == 'eventPage') insCurrent._eventListConnect({vars : obj.vars, flag : 'Search'});
				else if (array[1] == 'eventBtnBottom') insCurrent._eventListConnect({flag : obj.vars.vars.vars.idTarget});

			} else if (array[0] == 'resetVars') {
				insCurrent._checkListBtn({
					varsDetail : obj.vars.varsDetail,
					varsBtn    : obj.vars.varsBtn
				});
				insCurrent._checkListToolEdit({
					varsDetail : obj.vars.varsDetail,
					varsEdit   : obj.vars.varsEdit
				});
			}
		};

		return allot;
	},

	/**
	 *
	*/
	_eventListConnect : function(obj)
	{
		if (obj.flag == 'Reload' || obj.flag == 'Output' || obj.flag == 'Print') {
			this._eventSearch({
				numLotNow : this._varsSearch.numLotNow,
				ph : {
					arrWhere : this._varsSearch.ph.arrWhere,
					arrOrder : this._varsSearch.ph.arrOrder
				}
			});

		} else if (obj.flag == 'Search') {
			this._eventSearch({
				numLotNow : obj.vars.numLotNow,
				ph : {
					arrWhere : this._varsSearch.ph.arrWhere,
					arrOrder : this._varsSearch.ph.arrOrder
				}
			});

		} else if (obj.flag == 'Delete') {
			var arrId = this.insList.getTableCheckBoxArrId();
			if (!arrId.length) {
				alert(this.insRoot.vars.varsSystem.str.selectRequire);
				this.insList.eventNavi({strTitle : null, strClass : null});
				return;
			}
			this._eventValue({
				vars     : this.insList.getTableCheckBoxArrId(),
				idTarget : ''
			});
		}

		this._varsListConnect = obj;
		this._sendListConnect();
	},

	/**
	 *
	*/
	_varsListConnect : null,
	_sendListConnect : function(obj)
	{
		var arrayKey = [], arrayValue = [];
		var jsonSearch = Object.toJSON(this._varsSearch);
		var jsonStamp = {};
		var jsonValue = Object.toJSON(this._varsValue);
		var insEscape = new Code_Lib_Escape();

		if (this._varsListConnect.flag == 'Search') {
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

		} else {
			if (this._varsListConnect.flag == 'Print') {
				this.insLayout.updateTool({flagLock : 1, from : 'list', idTarget : this._varsListConnect.flag});
			}
			var strFunc = 'List' + insEscape.strCapitalize({data : this._varsListConnect.flag});
			arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonValue', 'jsonSearch'];
			arrayValue = [this.strClass, this.idModule, this.strExt, this.strChild, strFunc, 'master', jsonStamp, jsonValue, jsonSearch];
		}

		if (this._varsListConnect.flag == 'Output') {
			if (this._varsListConnect.flagType) {
				arrayKey.push('flagType');
				arrayValue.push(this._varsListConnect.flagType);
			}
			this.insRoot.setOutput({
				querysKey   : arrayKey,
				querysValue : arrayValue
			});
			this.insLayout.updateTool({flagLock : 0, from : 'list', idTarget : 'Output'});

		} else {
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
	},

	/**
	 *
	*/
	_sendListConnectFail : function(obj)
	{
		alert(this.insRoot.vars.varsSystem.str.errorConnect);
	},

	/**
	 *
	*/
	_sendListConnectSuccess : function(obj)
	{
		if (obj.response.responseText.isJSON()) {
			var json = obj.response.responseText.evalJSON();
			if (json.stamp) {
				if (json.stamp.id) this._varsStamp[json.stamp.id] = json.stamp.stamp;
			}
			this.insList.showBtnBottom();
			if (this._varsListConnect.strBackFunc != undefined) {
				this._varsListConnect.insBack[this._varsListConnect.strBackFunc]();
			}
			if (json.flag) {
				if (json.numNews) this.insRoot.iniPopup({flag : 'news', numNews : json.numNews});
				this._eventListConnectSuccess({json : json});
			}
			else if (json.flag == 0) alert(this.insRoot.vars.varsSystem.str.errorSession);
		}
		else alert(this.insRoot.vars.varsSystem.str.errorRequest);
	},

	/**
	 *
	*/
	_eventListConnectSuccess : function(obj)
	{
		if (obj.json.flag == 1){
			if (this._varsListConnect.flag == 'Search'
				|| this._varsListConnect.flag == 'Reload'
			) {
				this.insLayout.updateTool({flagLock : 0, from : 'list', idTarget : 'Reload'});
				this.insList.resetVars({vars : obj.json.data, numLotNow : this._varsSearch.numLotNow});
				this.insList.eventNavi({strTitle : null, strClass : null});

			} else if (this._varsListConnect.flag == 'Delete' ) {
				this.insList.resetVars({vars : obj.json.data, numLotNow : 0});
				this.insList.eventNavi({strTitle : null, strClass : null});
				this._resetDetail();

			} else if (this._varsListConnect.flag == 'Print' ) {
				this.insRoot.setPrint({data : obj.json.data, insCurrent : this, strFunc : 'eventListConnectSuccessPrint'});
			}

		} else if (obj.json.flag == 10) {
			if (this._varsListConnect.flag == 'Search'
				|| this._varsListConnect.flag == 'Reload'
			) {
				this.insLayout.updateTool({flagLock : 0, from : 'list', idTarget : 'Reload'});
				this.insList.eventNavi({strTitle : null, strClass : null});
			}

		} else if (obj.json.flag == 40) {
			this.insList.resetVars({vars : obj.json.data, numLotNow : this._varsSearch.numLotNow});
			this.insList.eventNavi({strTitle : null, strClass : null});

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
	eventListConnectSuccessPrint : function()
	{
		this.insLayout.updateTool({flagLock : 0, from : 'list', idTarget : this._varsListConnect.flag});
	},

	/**
	 *
	*/
	_resetDetail : function()
	{
		var flag = 0;
		if (this.vars.portal.varsDetail.varsStart) {
			if (this.vars.portal.varsDetail.varsStart.varsEdit) flag = 1;
		}

		this.insDetail.eventList({
			strTitle : '',
			strClass : '',
			vars     : {
				varsDetail : [],
				varsBtn    : [],
				varsEdit   : (flag)? this.vars.portal.varsDetail.varsStart.varsEdit : {},
				vars       : {}
			}
		});
	},

	/**
	 *
	*/
	_extDetail : function()
	{
		this._setDetail();
		this._setDetailStart();
	},

	/**
	 *
	*/
	insDetail : null,
	_setDetail : function()
	{
		this.insDetail = new Code_Lib_ControlDetail({
			insUnder   : this.insLayout.insDetailUnder,
			insTool    : this.insLayout.insDetailTool,
			insRoot    : this.insRoot,
			insCurrent : this,
			idSelf     : this.idSelf + 'Detail',
			allot      : this._getDetailAllot(),
			vars       : this.vars.portal.varsDetail
		});
	},

	/**
	 *
	*/
	_getDetailAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == 'form-preEventLayout') insCurrent._preEventLayoutDetailContent();
			else if (obj.from == '_mousedownMove') insCurrent.insNavi.eventMove({vars : obj.vars});
			else if (obj.from == 'form-eventLayout') insCurrent._eventLayoutDetailContent();
			else if (obj.from == 'view-eventLayout') insCurrent._eventLayoutDetailContent();
			else if (obj.from == 'view-checkTextBtn') insCurrent._checkDetailContentTextBtn({vars : obj.vars});
			else if (obj.from == 'view-eventBtnBottom') insCurrent._eventDetailConnect({flag : obj.vars.vars.vars.idTarget});
			else if (obj.from == 'form-eventBtnBottom') {
				if (obj.vars.vars.vars.flagBack) {
					insCurrent._backDetailEnd();

				} else if (obj.vars.vars.vars.flagHide) {
					if (!insCurrent.insWindow.vars.flagHideNow) insCurrent.insWindow.updateHide({ flagEffect : 1 });

				} else {
					if (obj.vars.vars.vars.idTarget == 'save') {
						insCurrent._eventDetailConnect({flag : insCurrent.varsChild.flagType});
					}
				}

			}
		};

		return allot;
	},

	/**
	 *
	*/
	_checkDetailContentTextBtn : function(obj)
	{

	},

	/**
	 *
	*/
	_preEventLayoutDetailContent : function()
	{

	},

	/**
	 *
	*/
	_eventLayoutDetailContent : function()
	{

	},

	/**
	 *
	*/
	_setDetailStart : function()
	{
		var flag = 0;
		if (this.vars.portal.varsDetail.varsStart) {
			if (this.vars.portal.varsDetail.varsStart.varsEdit) flag = 1;
		}

		this.insDetail.eventList({
			strTitle : '',
			strClass : '',
			vars     : {
				varsDetail : [],
				varsBtn    : null,
				varsEdit   : (flag)? this.vars.portal.varsDetail.varsStart.varsEdit : {},
				vars       : {}
			}
		});
	},

	/**
	 *
	*/
	_eventDetailList : function(obj)
	{
		var objData = this._updateDetailListVars({vars : obj.vars});
		this.insDetail.eventList(objData);
		this._setDetailContent({idTarget : obj.vars.vars.idTarget});
	},

	/**
	 *
	*/
	_setDetailChild : function(obj)
	{
		var objDetail = (Object.toJSON(this.vars.portal.varsDetail.templateDetail)).evalJSON();
		var varsDetail = [];
		var varsIni = null;
		var vars = {};

		if (obj.flag == 'add') {
			varsDetail = this._getDetailChildVars({
				arr  : objDetail,
				flag : obj.flag
			});

		} else if (obj.flag == 'copy') {
			varsIni = this._getDetailChildVars({
				flag    : 'add',
				arr     : (Object.toJSON(objDetail)).evalJSON(),
				vars    : this.insDetail.varsEventList.vars
			});
			varsDetail = this._getDetailChildVars({
				flag : obj.flag,
				arr  : objDetail,
				vars : this.insDetail.varsEventList.vars
			});
			obj.flag = 'add';
			vars = this.insDetail.varsEventList.vars;

		} else if (obj.flag == 'edit') {
			varsIni = this._getDetailChildVars({
				flag    : 'add',
				arr     : (Object.toJSON(objDetail)).evalJSON(),
				vars    : this.insDetail.varsEventList.vars
			});
			varsDetail = this._getDetailChildVars({
				flag : obj.flag,
				arr  : objDetail,
				vars : this.insDetail.varsEventList.vars
			});
			vars = this.insDetail.varsEventList.vars;
		}

		var idTarget = 0;

		if (this.insDetail.varsEventList.vars) {
			if (this.insDetail.varsEventList.vars.vars) {
				idTarget = this.insDetail.varsEventList.vars.vars.idTarget;

			}
		}

		this._extChild({
			strTitleParent : this.insWindow.vars.strTitle,
			strTitleChild  : this.vars.child.varsTitle.editor,
			strExt         : this.strExt,
			strChild       : 'Editor',
			strClass       : this.strClass,
			idModule       : this.idModule,
			varsChild      : {
				flagType   : obj.flag,
				idTarget   : idTarget,
				varsDetail : varsDetail,
				varsIni    : varsIni,
				vars       : vars
			}
		});

	},

	/**
	 *
	*/
	_getDetailChildVars : function(obj)
	{
		return obj.arr;
	},

	/**
	 *
	*/
	_setDetailContent : function(obj)
	{

	},

	/**
	 *
	*/
	_updateDetailListVars : function(obj)
	{
		var objDetail = (Object.toJSON(this.vars.portal.varsDetail.templateDetail)).evalJSON();
		var varsBtn = (Object.toJSON(this.vars.portal.varsDetail.varsBtn)).evalJSON();

		var objData = {
			flagMoveUse : 1,
			strTitle    : this.insDetail.vars.varsStart.strTitle,
			strClass    : obj.vars.strClass,
			vars        : {
				varsDetail : this._updateDetailListVarsChild({arr : objDetail, vars : obj.vars }),
				varsBtn    : this._updateDetailListVarsBtn({
					arr  : varsBtn,
					flag : obj.vars.flagDefault
				}),
				varsEdit   : this._updateDetailListVarsEdit({
					arr  : this.insDetail.vars.view.varsEdit,
					flag : obj.vars.flagDefault
				}),
				vars       : obj.vars
			}
		};

		return objData;
	},

	/**
	 *
	*/
	_updateDetailListVarsEdit : function(obj)
	{
		if (obj.flag) obj.arr.flagEditUse = 0;
		else obj.arr.flagEditUse = 1;

		return obj.arr;
	},

	/**
	 *
	*/
	_updateDetailListVarsBtn : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.flag) obj.arr[i].flagUse = 0;
			else obj.arr[i].flagUse = 1;
		}

		return obj.arr;
	},

	/**
	 *
	*/
	_updateDetailListVarsChild : function(obj)
	{

	},


	/**
	 *
	*/
	_varsDetailFormCalender : [],
	_extDetailFormCalender : function()
	{
		this._varsDetailFormCalender = [];
		this._setDetailFormCalender({arr : this.insDetail.insForm.vars.varsDetail});
	},

	/**
	 *
	*/
	_setDetailFormCalender : function(obj)
	{
		var num = 0;

		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormCalender) continue;
			var insCalender = new Code_Lib_CalenderFormNavi({
				eleInsert  : $(this.insDetail.insForm.idSelf + obj.arr[i].id),
				insRoot    : this.insRoot,
				insCurrent : this,
				idSelf     : this.idSelf + 'Calender' + obj.arr[i].id,
				allot      : this._getDetailFormCalenderAllot(),
				vars       : obj.arr[i].varsFormCalender
			});

			this._varsDetailFormCalender.push({
				id          : obj.arr[i].id,
				insCalender : insCalender
			});
			num++;
		}

	},

	/**
	 *
	*/
	_getDetailFormCalenderAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == '_mousedownDate') {

			}
		};

		return allot;
	},

	/**
	 *
	*/
	_eventRemoveDetailFormCalender : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormCalender) continue;
			this._varsDetailFormCalender[num].insCalender.removeWrap();
			num++;
		}
	},

	/**
	 *
	*/
	_varsDetailFormCalenderNum : [],
	_extDetailFormCalenderNum : function()
	{
		this._varsDetailFormCalenderNum = [];
		this._setDetailFormCalenderNum({arr : this.insDetail.insForm.vars.varsDetail});
	},

	/**
	 *
	*/
	_setDetailFormCalenderNum : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsCalenderFormNum) continue;
			var insCalender = new Code_Lib_CalenderFormNum({
				eleInsert  : this.insDetail.insForm.eleWrap.down('.codeLibFormContent', num),
				insRoot    : this.insRoot,
				insCurrent : this,
				idSelf     : this.idSelf + 'CalenderNum' + obj.arr[i].id,
				allot      : this._getDetailFormCalenderNumAllot(),
				vars       : obj.arr[i].varsCalenderFormNum
			});

			this._varsDetailFormCalenderNum.push({
				id          : obj.arr[i].id,
				insCalender : insCalender
			});
			num++;
		}
	},

	/**
	 *
	*/
	_setDetailFormCalenderNumValue : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsCalenderFormNum) continue;
			var str = this._varsDetailFormCalenderNum[num].insCalender.getValue();
			this.insDetail.setFormValue({
				idTarget : obj.arr[i].id,
				value    : str
			});
			num++;
		}
	},

	/**
	 *
	*/
	_getDetailFormCalenderNumAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;

		};

		return allot;
	},

	/**
	 *
	*/
	_eventRemoveDetailFormCalenderNum : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsCalenderFormNum) continue;
			this._varsDetailFormCalenderNum[num].insCalender.removeWrap();
			num++;
		}
	},

	/**
	 *
	*/
	_varsDetailFormList : [],
	_extDetailFormList : function()
	{
		this._varsDetailFormList = [];
		this._setDetailFormList({arr : this.insDetail.insForm.vars.varsDetail});
	},

	/**
	 *
	*/
	_setDetailFormList : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormList) continue;
			var ele = this.insDetail.insForm.eleWrap.down('.codeLibFormContent', num);
			ele.setStyle({width : this._getDetailFormListWidth() + 'px'});
			var insList = new Code_Lib_FormList({
				eleInsert  : ele,
				insRoot    : this.insRoot,
				insCurrent : this,
				idSelf     : this.insDetail.insForm.idSelf + 'DetailFormList' + obj.arr[i].id,
				allot      : this._getDetailFormListAllot(),
				vars       : obj.arr[i].varsFormList
			});
			this._varsDetailFormList.push({
				id       : obj.arr[i].id,
				insList  : insList
			});
			num++;
		}
	},


	/**
	 *
	*/
	_getDetailFormListAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;

		};

		return allot;
	},

	/**
	 *
	*/
	_getDetailFormListVars : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormList) continue;
			this._varsDetailFormList[num].insList.updateVarsValue();
			obj.arr[i].varsFormList.varsDetail = this._varsDetailFormList[num].insList.vars.varsDetail;
			num++;
		}
	},

	/**
	 *
	*/
	_staticDetailFormList : { numMarginLeft : 30, numBar : 17, numTop : 30, numLeft : 10 },
	_getDetailFormListWidth : function()
	{
		var array = (this.insDetail.insUnder.eleFormat.body).style.width.split('px');
		var width = parseFloat(array[0]) - this._staticDetailFormList.numMarginLeft - this._staticDetailFormList.numBar;

		return  width;
	},

	/**
	 *
	*/
	_eventRemoveDetailFormList : function(obj)
	{

		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormList) continue;
			this._varsDetailFormList[num].insList.stopListener();
			num++;
		}

	},

	/**
	 *
	*/
	_setDetailFormListValue : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormList) continue;
			var str = this._varsDetailFormList[num].insList.getValueJson();
			this.insDetail.setFormValue({
				idTarget : obj.arr[i].id,
				value    : str
			});
			num++;
		}
	},


	/**
	 *
	*/
	_varsDetailFormArea : [],
	_extDetailFormArea : function()
	{
		this._varsDetailFormArea = [];
		this._setDetailFormAreaVars({arr : this.insDetail.insForm.vars.varsDetail});
		this._setDetailFormArea({arr : this.insDetail.insForm.vars.varsDetail});
	},

	/**
	 *
	*/
	_setDetailFormAreaVars : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormArea) continue;
			obj.arr[i].varsFormArea.varsStatus.numWidth = this._getDetailFormAreaWidth();
		}
	},

	/**
	 *
	*/
	_getDetailFormAreaVars : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormArea) continue;
			obj.arr[i].varsFormArea.varsDetail = this._varsDetailFormArea[num].insArea.vars.varsDetail;
			num++;
		}
	},

	/**
	 *
	*/
	_staticDetailFormArea : {
		numMarginLeft : 20,
		numPadding    : 5,
		numBorder     : 1,
		numBar        : 17,
		numLeft       : 10,
		numTop        : 30,
		numIdle       : 10
	},

	/**
	 *
	*/
	_getDetailFormAreaWidth : function()
	{
		var array = this.insDetail.insUnder.eleFormat.body.style.width.split('px');
		var data = parseFloat(array[0])
					 - this._staticDetailFormArea.numMarginLeft
					 - this._staticDetailFormArea.numPadding * 6
					 - this._staticDetailFormArea.numBorder * 2
					 - this._staticDetailFormArea.numIdle
					 - this._staticDetailFormArea.numBar;

		return  data;
	},

	/**
	 *
	*/
	_setDetailFormArea : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormArea) continue;
			var insArea = new Code_Lib_FormArea({
				eleInsert  : this.insDetail.insForm.eleWrap.down('.codeLibFormContent', num),
				insRoot    : this.insRoot,
				insCurrent : this,
				idSelf     : this.insDetail.insForm.idSelf + 'DetailFormArea' + obj.arr[i].id,
				allot      : this._getDetailFormAreaAllot(),
				vars       : obj.arr[i].varsFormArea
			});
			this._varsDetailFormArea.push({
				id       : obj.arr[i].id,
				insArea  : insArea
			});
			num++;
		}
	},

	/**
	 *
	*/
	_getDetailFormAreaAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == '_mousedownBarAdd' || obj.from == '_mousedownBarLink') {
				insCurrent._setDetailFormAreaChoice({
					idTarget     : obj.vars.insCurrent.vars.varsChoice.idTarget,
					idModule     : obj.vars.insCurrent.vars.varsChoice.idModule,
					flagCheckUse : obj.vars.insCurrent.vars.varsChoice.flagCheckUse,
					flagId       : obj.vars.insCurrent.vars.varsChoice.flagId,
					strFunc      : obj.vars.insCurrent.vars.varsChoice.strFunc
				});
			}
		};

		return allot;
	},

	/**
	 *
	*/
	_setDetailFormAreaChoice : function(obj)
	{
		this.insRoot.insChoice.setBoot({
			flagId       : obj.flagId,
			idTarget     : obj.idTarget,
			idModule     : obj.idModule,
			flagCheckUse : obj.flagCheckUse,
			strClassTitle: obj.strClassTitle,
			strFunc      : 'setDetailFormAreaChoiceValue',
			numTop       : this._staticDetailFormArea.numTop + $(this.insWindow.idWindow).offsetTop,
			numLeft      : this._staticDetailFormArea.numLeft + $(this.insWindow.idWindow).offsetLeft,
			insCurrent   : this
		});

	},

	/**
	 * {
	 * 	flagId : string
	 * 	vars   : array
	 * }
	*/
	setDetailFormAreaChoiceValue : function(obj)
	{
		if (!obj.vars) return;
		this._setDetailFormAreaChoiceValue({
			idTarget : obj.flagId,
			vars     : obj.vars,
			arr      : this.insDetail.insForm.vars.varsDetail
		});
	},

	/**
	 *
	*/
	_setDetailFormAreaChoiceValue : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormArea) continue;
			if (obj.idTarget == obj.arr[i].id) {
				var varsDetail = this._setDetailFormAreaChoiceValueChild({
					insArea   : this._varsDetailFormArea[num].insArea,
					arr       : obj.vars,
					arrDetail : this._varsDetailFormArea[num].insArea.vars.varsDetail
				});
				this._varsDetailFormArea[num].insArea.vars.varsDetail = varsDetail;
				this._varsDetailFormArea[num].insArea.iniReload();
			}
			num++;
		}
	},

	/**
	 *
	*/
	_setDetailFormAreaChoiceValueChild : function(obj)
	{
		var array = [];
		var objCheck = {};
		for (var i = 0; i < obj.arr.length; i++) {
			var varsTmpl = (Object.toJSON(obj.insArea.vars.templateDetail)).evalJSON();
			if (obj.arr[i].strClass) {
				varsTmpl.strClass = obj.arr[i].strClass;
			}
			varsTmpl.strTitle = obj.arr[i].strTitle;
			varsTmpl.vars.idTarget = obj.arr[i].vars.idTarget;
			array[i] = varsTmpl;
			var str = 'id' + varsTmpl.vars.idTarget;
			objCheck[str] = 1;
		}
		for (var i = 0, j = array.length; i < obj.arrDetail.length; i++) {
			var str = 'id' + obj.arrDetail[i].vars.idTarget;
			if (objCheck[str]) continue;
			array[j] = obj.arrDetail[i];
			j++;
		}

		return array;
	},


	/**
	 *
	*/
	_setDetailFormAreaValue : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormArea) continue;
			var str = this._varsDetailFormArea[num].insArea.getTreeValueToConmmaArr();
			this.insDetail.setFormValue({
				idTarget : obj.arr[i].id,
				value    : str
			});
			num++;
		}
	},


	/**
	 *
	*/
	_eventRemoveDetailFormArea : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormArea) continue;
			this._varsDetailFormArea[num].insArea.stopListener();
			num++;
		}
	},

	/**
	 *
	*/
	_varsDetailFormCheck : [],
	_extDetailFormCheck : function()
	{
		this._varsDetailFormCheck = [];
		this._setDetailFormCheck({arr : this.insDetail.insForm.vars.varsDetail});
	},


	/**
	 *
	*/
	_staticDetailFormCheck : { numTop : 30, numLeft : 10 },
	_setDetailFormCheck : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormCheck) continue;
			var ele = this.insDetail.insForm.eleWrap.down('.codeLibFormContent', num);
			var insFormCheck = new Code_Lib_FormCheck({
				eleInsert  : ele,
				insRoot    : this.insRoot,
				insCurrent : this,
				idSelf     : this.idSelf + 'FormCheck' + obj.arr[i].id,
				allot      : this._getDetailFormCheckAllot(),
				vars       : obj.arr[i].varsFormCheck
			});

			this._varsDetailFormCheck.push({
				id           : obj.arr[i].id,
				insFormCheck : insFormCheck,
			});
			num++;
		}
	},

	/**
	 *
	*/
	_getDetailFormCheckAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
		};

		return allot;
	},



	/**
	 *
	*/
	_eventRemoveDetailFormCheck : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormCheck) continue;
			this._varsDetailFormCheck[num].insFormCheck.stopListener();
			num++;
		}
	},

	/**
	 *
	*/
	_getDetailFormCheckVars : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormCheck) continue;
			obj.arr[i].varsFormCheck.varsDetail = this._varsDetailFormCheck[num].insFormCheck.vars.varsDetail;
			num++;
		}
	},

	/**
	 *
	*/
	_setDetailFormCheckValue : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormCheck) continue;
			this.insDetail.setFormValue({
				idTarget : obj.arr[i].id,
				value    : this._varsDetailFormCheck[num].insFormCheck.vars.varsDetail
			});
			num++;
		}
	},


	/**
	 *
	*/
	_eventDetailConnect : function(obj)
	{
		if (obj.flag == 'reload' || obj.flag == 'delete') {
			this._eventValue({
				vars     : '',
				idTarget : this.insDetail.varsEventList.vars.vars.idTarget
			});

		} else if (obj.flag == 'edit') {
			if (this.insDetail.checkForm({flagType : 'common'})) return;
			this._eventValue({
				vars     : this.insDetail.getFormValue(),
				idTarget : obj.idTarget
			});

		} else if (obj.flag == 'output') {
			var vars = {};
			if (obj.flagType) {
				vars.FlagType = obj.flagType;
			}
			if (this.insDetail.varsEventList) {
				if (this.insDetail.varsEventList.vars) {
					vars.numVersion = this.insDetail.varsEventList.vars.numVersion;
				}
			}
			this._eventValue({
				vars     : vars,
				idTarget : this.insDetail.varsEventList.vars.vars.idTarget
			});

		} else if (obj.flag == 'Print') {
			var vars = {};
			if (obj.flagType) {
				vars.FlagType = obj.flagType;
			}
			this._eventValue({
				vars     : vars,
				idTarget : this.insDetail.varsEventList.vars.vars.idTarget
			});
		}

		this._varsDetailConnect = obj;
		this._sendDetailConnect();
	},


	/**
	 *
	*/
	_varsDetailConnect : null,
	_sendDetailConnect : function(obj)
	{
		var strExt = this.strExt;
		var strClass = this.strClass;
		var idModule = this.idModule;
		var strChild = this.strChild;
		var jsonSearch = Object.toJSON(this._varsSearch);
		var jsonValue = Object.toJSON(this._varsValue);
		var arrayKey = [], arrayValue = [];
		var jsonStamp = {};
		var insEscape = new Code_Lib_Escape();

		if (this._varsDetailConnect.flag == 'reload') {
			var strFunc = 'DetailReload';
			jsonStamp = this._getJsonStamp({strFunc : strFunc});
			this.insLayout.updateTool({flagLock : 1, from : 'detail', idTarget : 'Reload'});
			arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonValue', 'jsonSearch'];
			arrayValue = [strClass, idModule, strExt, strChild, strFunc, 'slave', jsonStamp, jsonValue, jsonSearch];

		} else {
			var strDb = 'master';
			if (this._varsDetailConnect.flag == 'estimate') {
				strDb = 'slave';
			}
			var strFunc = 'Detail' + insEscape.strCapitalize({data : this._varsDetailConnect.flag});
			if (this._varsDetailConnect.flag == 'output') {
				strChild = 'Output';
				strDb = 'slave';

			} else if (this._varsDetailConnect.flag == 'Print') {
				strChild = 'Output';
				strDb = 'slave';
				this.insLayout.updateTool({flagLock : 1, from : 'detail', idTarget : this._varsDetailConnect.flag});
			}
			arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonValue', 'jsonSearch'];
			arrayValue = [strClass, idModule, strExt, strChild, strFunc, strDb, jsonStamp, jsonValue, jsonSearch];
		}

		if (this._varsDetailConnect.flag == 'output') {
			this.insRoot.setOutput({
				querysKey   : arrayKey,
				querysValue : arrayValue,
			});

			this.insDetail.showBtnBottom();
			this.insLayout.updateTool({flagLock : 0, from : 'list', idTarget : 'Output'});

		} else {
			this.insRoot.insRequest.set({
				flagLock        : 0,
				numZIndex       : this.insRoot.getZIndex(),
				insCurrent      : this,
				flagEscape      : 1,
				path            : this.insRoot.vars.varsSystem.path.post,
				querysKey       : arrayKey,
				querysValue     : arrayValue,
				functionSuccess : '_sendDetailConnectSuccess',
				functionFail    : '_sendDetailConnectFail',
				eleLoadStatus   : this.insDetail.insUnder.eleFormat.header.down('.codeLibBaseMarginRightFive', 0)
			});
		}

	},

	/**
	 *
	*/
	_sendDetailConnectSuccess : function(obj)
	{
		if (obj.response.responseText.isJSON()) {
			var json = obj.response.responseText.evalJSON();
			if (json.stamp) {
				if (json.stamp.id) this._varsStamp[json.stamp.id] = json.stamp.stamp;
			}
			if (json.flag) {
				if (json.numNews) this.insRoot.iniPopup({flag : 'news', numNews : json.numNews});
				this._eventDetailConnectSuccess({json : json});
			}
			else if (json.flag == 0) alert(this.insRoot.vars.varsSystem.str.errorSession);
		}
		else alert(this.insRoot.vars.varsSystem.str.errorRequest);
	},

	/**
	 *
	*/
	_sendDetailConnectFail : function(obj)
	{
		alert(this.insRoot.vars.varsSystem.str.errorRequest);
	},

	/**
	 *
	*/
	_eventDetailConnectSuccess : function(obj)
	{
		if (obj.json.flag == 1) {
			if (this._varsDetailConnect.flag == 'reload') {

				this.eventDetailConnectSuccessDetailUpdate(obj);
				if (obj.json.stamp) {
					var data = (Object.toJSON(obj.json)).evalJSON();
					if (obj.json.stamp.id) this._varsStampCheck[obj.json.stamp.id] = data;
				}

			} else if (this._varsDetailConnect.flag == 'delete') {
				this.eventDetailConnectSuccessListUpdateDetailReset(obj);

			} else if (this._varsDetailConnect.flag == 'Print') {
				this.insRoot.setPrint({data : obj.json.data, insCurrent : this, strFunc : 'eventDetailConnectSuccessPrint'});

			}

		} else if (obj.json.flag == 10) {
			if (obj.json.stamp) {
				this.eventDetailConnectSuccessDetailUpdate({json : this._varsStampCheck[obj.json.stamp.id]});
			}

		} else if (obj.json.flag == 40) {
			this.eventDetailConnectSuccessLost(obj);

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
	eventDetailConnectSuccessPrint : function()
	{
		this.insLayout.updateTool({flagLock : 0, from : 'detail', idTarget : this._varsDetailConnect.flag});
	},

	/**
	 *
	*/
	_varsDetailEnd : null,
	_setDetailEnd : function()
	{
		this._varsDetailEnd = (Object.toJSON(this.insDetail.varsEventList)).evalJSON();
		var objData = {
			strTitle : null,
			strClass : null,
			vars     : {
				varsDetail : this.vars.portal.varsDetail.varsEnd.varsDetail,
				varsBtn    : this.vars.portal.varsDetail.varsEnd.varsBtn,
				varsEdit   : {},
				vars       : {}
			}
		};

		this.insDetail.eventList(objData);
	},

	/**
	 *
	*/
	_backDetailEnd : function()
	{
		this.insDetail.eventList({
			strTitle : null,
			strClass : null,
			vars     : this._varsDetailEnd
		});
		this._varsDetailEnd = null;
	},

	/**
	 *
	*/
	eventDetailConnectSuccessDetailUpdate : function(obj)
	{
		this.insLayout.updateTool({flagLock : 0, from : 'detail', idTarget : 'Reload'});
		if (obj.json.data) {
			this.insList.updateVarsDetailLine({vars : obj.json.data});
			this.insList.eventNavi({strTitle : null, strClass : null});
			if (this.insDetail.varsEventList.vars.vars.idTarget == obj.json.data.varsDetail.vars.idTarget) {
				this._eventDetailList({vars : obj.json.data.varsDetail});
			}

		} else {
			this._resetDetail();
		}
	},

	/**
	 *
	*/
	eventDetailConnectSuccessListDetailUpdate : function(obj)
	{
		this.insList.updateVarsDetailLine({vars : obj.json.data});
		this.insList.eventNavi({strTitle : null, strClass : null});
		if (this.insDetail.varsEventList.vars.vars.idTarget == obj.json.data.varsDetail.vars.idTarget) {
			this._eventDetailList({vars : obj.json.data.varsDetail});
		}
	},

	/**
	 *
	*/
	eventDetailConnectSuccessListUpdate : function(obj)
	{
		this._varsSearch.numLotNow = 0;
		this.insList.resetVars({vars : obj.json.data, numLotNow : 0});
		this.insList.eventNavi({strTitle : null, strClass : null});
	},

	/**
	 *
	*/
	eventDetailConnectSuccessListUpdateDetailReset : function(obj)
	{
		this.eventDetailConnectSuccessListUpdate(obj);
		this._resetDetail();
	},

	/**
	 *
	*/
	eventDetailConnectSuccessDetailReset : function(obj)
	{
		this.insLayout.updateTool({flagLock : 0, from : 'detail', idTarget : 'Reload'});
		if (this.insDetail.varsEventList.vars.vars.idTarget == this._varsValue.idTarget) {
			this._eventDetailList({vars : this.insDetail.varsEventList.vars});
		}
	},

	/**
	 *
	*/
	eventDetailConnectSuccessLost : function(obj)
	{
		this.insLayout.updateTool({flagLock : 0, from : 'detail', idTarget : 'Reload'});
		this.eventDetailConnectSuccessListUpdateDetailReset(obj);
	},

	/**
	 *
	*/
	getVarsSearch : function()
	{
		return this._varsSearch;
	},

	/**
	 *
	*/
	_varsChild : {
		strTitleChild : '',
		strTitleParent : '',
		strExt : '',
		strChild : '',
		eleLoading : {},
		strClass : '',
		idModule : '',
		insBack : {},
		strBackFunc : '',
		arrIns : [],
		varsWindow : {},
		varsChild : {}
	},
	_extChild : function(obj)
	{
		this._setVarChild(obj);
		var strExt = this._varsChild.strExt;
		var strChild = this._varsChild.strChild;
		if (this['ins' + strExt + strChild]) {
			if (this['ins' + strExt + strChild].vars.flagHideNow) {
				if (this['ins' + strExt + strChild + 'Class']) {
					this['ins' + strExt + strChild + 'Class'].eventWindowAppear({vars : this._varsChild.varsChild});
					this['ins' + strExt + strChild].updateHide({ flagEffect : 1 });
				}

			} else {
				this.eventHide();
			}

		} else {
			this._setVarChild(obj);
			this._varChild();
			this._setChild();
		}
	},

	/**
	 *
	*/
	eventWindowAppear : function(obj)
	{

	},

	/**
	 *
	*/
	_resetVarChild : function()
	{
		this._varsChild = {
			strTitleChild : '',
			strTitleParent : '',
			strExt : '',
			strChild : '',
			eleLoading : {},
			strClass : '',
			idModule : '',
			insBack : {},
			strBackFunc : '',
			flagHideWindow : 0,
			arrIns : [],
			varsWindow : {},
			varsCall : {},
			varsChild : {}
		};
	},

	/**
	 *
	*/
	_setVarChild : function(obj)
	{
		this._varsChild.strExt = obj.strExt;
		this._varsChild.strChild = obj.strChild;
		this._varsChild.strClass = obj.strClass;
		this._varsChild.idModule = obj.idModule;
		this._varsChild.strTitleChild = obj.strTitleChild;
		this._varsChild.strTitleParent = obj.strTitleParent;
		this._varsChild.varsCall = (obj.varsCall)? obj.varsCall : {};
		this._varsChild.varsChild = (obj.varsChild)? obj.varsChild : {};
		this._varsChild.strBackFunc = (obj.strBackFunc)? obj.strBackFunc : '';
		this._varsChild.insBack = (obj.strBackFunc)? obj.insBack : {};
		this._varsChild.flagHideWindow = (obj.flagHideWindow)? obj.flagHideWindow : 0;
	},

	/**
	 *
	*/
	_varChild : function()
	{
		var vars = (Object.toJSON(this.vars.child.templateWindow)).evalJSON();
		var strExt = this._varsChild.strExt;
		var strChild = this._varsChild.strChild;
		vars.id =  strExt + strChild;
		vars.strTitle = vars.strTitle.replace(/<?php echo '<%'; ?>
child<?php echo '%>'; ?>
/, this._varsChild.strTitleChild);
		vars.strTitle = vars.strTitle.replace(/<?php echo '<%'; ?>
parent<?php echo '%>'; ?>
/, this._varsChild.strTitleParent);
		if (this._varsChild.strExt == 'Preference') {
			vars.flagMenuShowUse = 1;
		}
		if (this._varsChild.varsCall) {
			if (this._varsChild.varsCall.vars) {
				if (this._varsChild.varsCall.vars) {
					if (this._varsChild.varsCall.vars.numHeight) {
						vars.numHeight = this._varsChild.varsCall.vars.numHeight;
					}
					if (this._varsChild.varsCall.vars.numWidth) {
						vars.numWidth = this._varsChild.varsCall.vars.numWidth;
					}
				}
			}
		}
		this._varsChild.varsWindow[strExt + strChild] = vars;
	},


	/**
	 *
	*/
	_setChild : function()
	{
		var strExt = this._varsChild.strExt;
		var strChild = this._varsChild.strChild;
		this['ins' + strExt + strChild] = new Code_Lib_Window();
		this['ins' + strExt + strChild].iniLoad({
			eleInsert  : $(this.insRoot.vars.varsSystem.id.root),
			insRoot    : this.insRoot,
			insCurrent : this,
			insTop     : this.insTop,
			idSelf     : this.insRoot.vars.varsSystem.id.window + this.idSelf + strExt + strChild,
			allot      : this._getChildAllot(),
			vars       : this._varsChild.varsWindow[strExt + strChild],
			varsTarget : strExt + strChild
		});
		this._varsChild.arrIns.push({
			insWindow : this['ins' + strExt + strChild],
			idWindow  : this['ins' + strExt + strChild].idWindow,
			insClass  : null,
			idTarget  : strExt + strChild
		});
	},



	/**
	 *
	*/
	_getChildAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == '_iniVars') insCurrent._updateChild();
			else if (obj.from == '_mousedownBoot') insCurrent.insCurrent._sendChild();
			else if (obj.from.match(/^_mouseupResize|_mousedownCover|_resizeCover$/)) {
				obj.arr = insCurrent.insCurrent._varsChild.arrIns;
				insCurrent.insCurrent._eventWindow(obj);

			} else if (obj.from == '_mousedownHide') {
				if (!insCurrent.vars.flagHideNow) {
					obj.arr = insCurrent.insCurrent._varsChild.arrIns;
					insCurrent.insCurrent._hideWindow(obj);
				}

			} else if (obj.from == 'hideLockWindow') {
				insCurrent.insCurrent._hideLockWindow(obj);
			}
		};

		return allot;
	},

	/**
	 *
	*/
	_hideLockWindow : function(obj)
	{

	},

	/**
	 *
	*/
	_eventWindow : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].idTarget == obj.insCurrent.varsTarget && obj.arr[i].insClass) {
				obj.arr[i].insClass.eventWindow();
			}
		}
	},

	/**
	 *
	*/
	_hideWindow : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].idTarget == obj.insCurrent.varsTarget && obj.arr[i].insClass) {
				obj.arr[i].insClass.eventHide();
			}
		}
	},

	/**
	 *
	*/
	_staticChild : { numTop : 30, numLeft : 10 },
	_updateChild : function()
	{
		var strExt = this._varsChild.strExt;
		var strChild = this._varsChild.strChild;

		var array = $(this.insWindow.idWindow).style.top.split('px');
		var numTop = parseFloat(array[0]);

		array = $(this.insWindow.idWindow).style.left.split('px');
		var numLeft = parseFloat(array[0]);

		this['ins' + strExt + strChild].vars.numZIndex = this.insRoot.setZIndex();
		this['ins' + strExt + strChild].vars.numTop = numTop + this._staticChild.numTop;
		this['ins' + strExt + strChild].vars.numLeft = numLeft + this._staticChild.numLeft;
		this['ins' + strExt + strChild].vars.flagHideNow = 0;
		if (this._varsChild.flagHideWindow) {
			this['ins' + strExt + strChild].vars.flagHideNow = 1;
			this._varsChild.flagHideWindow = 0;
		}
	},

	/**
	 *
	*/
	_sendChild : function()
	{
		var strChild = this._varsChild.strChild;
		var strExt = this._varsChild.strExt;
		var strClass = this._varsChild.strClass;
		var idModule = this._varsChild.idModule;
		var jsonStamp = {};
		var arrayKey = [], arrayValue = [];

		arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp'];
		arrayValue = [strClass, idModule, strExt, strChild, 'Js', 'slave', jsonStamp];

		this.insRoot.insRequest.set({
			flagLock        : 0,
			numZIndex       : this.insRoot.getZIndex(),
			insCurrent      : this,
			flagEscape      : 1,
			path            : this.insRoot.vars.varsSystem.path.post,
			querysKey       : arrayKey,
			querysValue     : arrayValue,
			functionSuccess : '_sendChildSuccess',
			functionFail    : '_sendChildFail'
		});
		var ele = $(document.createElement('span'));
		$(this['ins' + strExt + strChild].idWindow).down('.codeLibWindowBodyTopMiddleWrap', 0).insert(ele);
		ele.addClassName('codeLibRequestImgLoading');
		ele.addClassName('codeLibRequestImgLoadingPos');
		this._varsChild.eleLoading[strExt + strChild] = ele;
	},

	/**
	 *
	*/
	_sendChildSuccess : function(obj)
	{

		var strChild = this._varsChild.strChild;
		var strExt = this._varsChild.strExt;
		var strClass = this._varsChild.strClass;
		var idModule = this._varsChild.idModule;

		if (obj.response.responseText.isJSON()) {
			var json = obj.response.responseText.evalJSON();
			if (json.flag == 0) {
				alert(this.insRoot.vars.varsSystem.str.errorSession);
				return;

			} else if (json.flag == 4) {
				alert(this.insRoot.vars.varsSystem.str.maintenance);
				return;

			} else if (json.flag == 8) {
				alert(this.insRoot.vars.varsSystem.str.oldData);
				return;
			}
		}

		var eleScript = $(document.createElement('script'));
		eleScript.type = 'text/javascript';
		eleScript.text = obj.response.responseText;
		var eleHead = document.getElementsByTagName('head').item(0);
		eleHead.appendChild(eleScript);

		var newClass = eval('Code_' + strClass + '_' + idModule + '_' + strExt + strChild);
		this['ins' + strExt + strChild + 'Class'] = new newClass();
		this['ins' + strExt + strChild + 'Class'].iniLoad({
			eleInsert  : $(this['ins' + strExt + strChild].idWindow).down('.codeLibWindowBodyTopMiddleWrap', 0),
			insRoot    : this.insRoot,
			insCurrent : this,
			insTop     : this.insTop,
			strExt     : strExt,
			strChild   : strChild,
			strClass   : strClass,
			idModule   : idModule,
			idSelf     : this.idSelf + strExt + strChild,
			insWindow  : this['ins' + strExt + strChild],
			varsChild  : this._varsChild.varsChild
		});
		for (var i = 0; i < this._varsChild.arrIns.length; i++) {
			if (this._varsChild.arrIns[i].idTarget == this['ins' + strExt + strChild].varsTarget) {
				this._varsChild.arrIns[i].insClass = this['ins' + strExt + strChild + 'Class'];
			}
		}
		this._varsChild.eleLoading[strExt + strChild].remove();
		this._varsChild.eleLoading[strExt + strChild] = null;
		if (this._varsChild.strBackFunc) {
			this._varsChild.insBack[this._varsChild.strBackFunc]();
		}
	},

	/**
	 *
	*/
	checkChildData : function(obj)
	{
		for (var i = 0; i < this._varsChild.arrIns.length; i++) {
			if (this._varsChild.arrIns[i].idTarget == obj.idTarget) {
				return this._varsChild.arrIns[i];
			}
		}
	},

	/**
	 *
	*/
	_sendChildFail : function(obj)
	{
		var strExt = this._varsChild.strExt;
		var strChild = this._varsChild.strChild;
		this._varsChild.eleLoading[strExt + strChild].remove();
		this._varsChild.eleLoading[strExt + strChild] = null;
		alert(this.insRoot.vars.varsSystem.str.errorConnect);
	},

	/**
	 * Block
	*/
	_varsBlock : null,
	_getBlock : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].vars.idTarget == obj.idTarget) {
				var objTemp = {};
				objTemp = obj.arr[i];
				if (obj.arr[i].child.length) objTemp.child = this._getBlockChild({arr : obj.arr[i].child});
				else objTemp.child = [];
				this._varsBlock = objTemp;
			}
			else this._getBlock({arr : obj.arr[i].child, idTarget : obj.idTarget});
		}
	},

	/**
	 *
	*/
	_getBlockChild : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			this._getBlockChild({arr : obj.arr[i].child});
		}

		return obj.arr;
	},

	/**
	 *
	*/
	_iniSearch : function()
	{
		this._resetSearch();
	},

	/**
	 *
	*/
	_resetSearch : function()
	{
		this._varsSearch = {
			flagReload : 0,
			numLotNow  : 0,
			ph : {
				arrWhere   : [],
				arrOrder   : {}
			},
		};
	},

	/**
	 * {
	 * 	idEntity  : int,
	 * 	numLotNow : int,
	 * 	ph : {
	 * 		arrWhere   : [],
	 * 		arrOrder   : { strColumn : string, flagDesc : int }
	 * 	},
	 * }
	*/
	_eventSearch : function(obj)
	{
		var jsonNext = Object.toJSON(obj.ph);
		var jsonBefore = Object.toJSON(this._varsSearch.ph);
		if (jsonNext == jsonBefore) {
			this._varsSearch.flagReload = 0;
			if (obj.numLotNow == this._varsSearch.numLotNow) this._varsSearch.flagReload = 1;
			this._varsSearch.numLotNow = obj.numLotNow;

		} else {
			this._varsSearch.numLotNow = 0;
			this._varsSearch.flagReload = 0;
		}
		this._varsSearch.ph = obj.ph;
	},

	/**
	 *
	*/
	_varsSearch : {
		flagReload : 0,
		numLotNow  : 0,
		ph : {
			arrWhere   : [],
			arrOrder   : {}
		}
	},

	/**
	 *
	*/
	_extPopup : function()
	{
		if (this.numNews) this.insRoot.iniPopup({flag : 'news', numNews : this.numNews});
	},

	/**
	 *
	*/
	_extCss : function()
	{
		if(!this.vars.pathCss) return;
		new Code_Lib_Css({
			path : this.vars.pathCss
		});
	},


	/**
	 *
	*/
	eventHide : function()
	{
		var array = this._varsChild.arrIns;
		for (var i = 0; i < array.length; i++) {
			if (array[i].insWindow) {
				if (!array[i].insWindow.vars.flagHideNow) {
					array[i].insWindow.updateHide({ flagEffect : 1 });
				}
			}
			if (array[i].insClass) {
				array[i].insClass.eventHide();
			}
		}
	},

	/**
	 *
	*/
	eventWindowRemove : function()
	{
		this.insLayout.allot({
			from       : 'eventWindowRemove',
			insCurrent : this
		});
	},

	/**
	 *
	*/
	_getOptionTitle : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.idTarget == obj.arr[i].value) {
				return obj.arr[i].strTitle;
			}
		}

		return '';
	},

	/**
	 *
	*/
	_getOptionFirstValue : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagDisabled) {
				return obj.arr[i].value;
			}
		}
		return '';
	},

	/**
	 *
	*/
	eventWindow : function()
	{
		this.insLayout.eventWindow();
	}
});

<?php }
}
?>