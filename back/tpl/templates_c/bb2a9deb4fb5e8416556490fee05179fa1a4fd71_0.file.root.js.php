<?php /* Smarty version 3.1.24, created on 2016-08-18 12:50:21
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/root.js" */ ?>
<?php
/*%%SmartyHeaderCode:154422986657b5af0d0e29c0_88878503%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bb2a9deb4fb5e8416556490fee05179fa1a4fd71' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/root.js',
      1 => 1471523677,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '154422986657b5af0d0e29c0_88878503',
  'variables' => 
  array (
    'varsLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b5af0d23dca4_64770570',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b5af0d23dca4_64770570')) {
function content_57b5af0d23dca4_64770570 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '154422986657b5af0d0e29c0_88878503';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Core_Base_Root = Class.create(Code_Lib_Ext,
{


	vars : <?php echo $_smarty_tpl->tpl_vars['varsLoad']->value;?>
,


	initialize : function(obj)
	{
		this._iniVars(obj);
		if (this._iniBrowser()) return;
		this._iniListener();
		this._iniRequest();
		this._iniTimeZone();
		this._iniLogout();
		this._iniKey();
		this._iniCake();
		this._iniTemp();
		this._iniUpload();
		this._iniOutput();
		this._iniPrint();
		this._iniLoad();
	},

	_iniRequest : function ()
	{
		this._extRequest();
	},

	_iniCake : function()
	{
		this._extCake({pathSelf : this.vars.varsSystem.path.post});
	},

	_iniBrowser : function () {
		return this._extBrowser();
	},

	_iniListener : function()
	{
		this._extListener();
	},

	_iniKey : function()
	{
		this._extKey();
	},

	/**
	 * TimeZone
	*/
	insTimeZone : null,
	_iniTimeZone : function()
	{
		this._extTimeZone({numTimeZone : this.vars.varsSystem.status.numTimeZone});
	},

	_iniTemp : function()
	{
		var ele = $(document.createElement('div'));
		ele.addClassName('codeLibBaseTempWrap');
		ele.id = this.vars.varsSystem.id.temp;
		$(this.vars.varsSystem.id.root).insert(ele);
	},

	/**
	 *
	*/
	_iniVars : function(obj)
	{
		this._extVars();
		this.insTop = this;
		var cut = this.vars.varsSystem;
		cut.status = obj.varsStatus;
		cut.status.numAutoLogout = parseFloat(cut.status.numAutoLogout);
		cut.status.numAutoPopup = parseFloat(cut.status.numAutoPopup);
		cut.status.numList = parseFloat(cut.status.numList);
		cut.status.numTimeZone = parseFloat(cut.status.numTimeZone);
	},


	/**
	 *
	*/
	_iniLoad : function()
	{

		this._setLoadVars();
		var flag = this._iniVersion();
		if (flag) {
			return;
		}
		this._iniGlobal();
		this._iniChoice();
		this._iniWindow();
		this._iniPopupTimer();
		if (this.vars.varsSystem.status.numNews) {
			this.iniPopup({flag : 'news', numNews : this.vars.varsSystem.status.numNews});
		}
	},

	_idVersion : 'version',
	_iniVersion : function()
	{
		if (!this.insCake) return;
		this._getVersionCake();
	},

	/**
	 *
	*/
	_getVersionCake : function()
	{
		this.insCake.getStorageCake({
			parentKey  : this._idVersion,
			funcReturn : this._getVersionCakeVars,
			insReturn  : this
		});
	},

	/**
	 *
	*/
	_getVersionCakeVars : function(obj)
	{
		var insCurrent = obj.insReturn;
		if(obj.data) {
			insCurrent._checkVersion({data : obj.data});
		} else {
			insCurrent._checkVersion({data : 0});
		}
	},

	/**
	 *
	*/
	_checkVersion : function(obj)
	{
		if (obj.data) {
			if (obj.data == this.vars.varsSystem.status.strVersion) {
				return;
			}
		}
		if (obj.data == 0) {
			this._setVersionCake();

		} else {
			this.insCake.removeStorageAllCake();
			this._setVersionCake();
			this.iniPopup({flag : 'version'});
			setTimeout(function() { location.reload();}, 3000);
		}
	},

	/**
	 *
	*/
	_setVersionCake : function()
	{
		this.insCake.setStorageCake({
			parentKey  : this._idVersion,
			value      : this.vars.varsSystem.status.strVersion,
			numExpires : 0
		});
	},

	/**
	 *
	*/
	_setLoadVars : function(obj)
	{
		this._iniVarsGlobal();
		this._iniVarsWindow({arr : this.vars.varsWindow});
		this._setVarsZIndex({
			arr       : this._varsData,
			arrWindow : this.vars.varsWindow,
			arrGlobal : this.vars.varsGlobal.varsDetail
		});
		this._setVarsFlagCheckNow({
			arr       : this._varsData,
			arrGlobal : this.vars.varsGlobal.varsDetail
		});
		this.vars.varsSystem.num.zIndex = this._varsData.length;
	},


	/**
	 *
	*/
	_varsData : [],
	_iniVarsGlobal : function()
	{
		if (!this.insCake) return;
		this.insCake.getStorageCake({
			parentKey  : this.vars.varsSystem.id.global,
			funcReturn : this._iniVarsGlobalCake,
			insReturn  : this
		});
	},

	/**
	 *
	*/
	_iniVarsGlobalCake : function(obj)
	{
		var insCurrent = obj.insReturn;
		if (obj.data) {
			insCurrent._getVarsGlobal({
				arr  : insCurrent.vars.varsGlobal.varsDetail,
				data : obj.data
			});
		}
	},

	/**
	 *
	*/
	_getVarsGlobal : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var objData = {};
			objData.id = 'global' + obj.arr[i].id;
			var str = 'zIndex' + obj.arr[i].id;
			objData.zIndex = obj.data[str];
			this._varsData.push(objData);
		}
	},


	/**
	 *
	*/
	_iniVarsWindow : function(obj)
	{
		if (!this.insCake) return;
		for (var i = 0; i < obj.arr.length; i++) {
			var id = obj.arr[i].id.toLowerCase();
			if (id == this.vars.varsSystem.status.strAutoBoot) obj.arr[i].flagBootUse = 'auto';
			this.insCake.getStorageCake({
				parentKey  : this.vars.varsSystem.id.window + obj.arr[i].id,
				funcReturn : this._iniVarsWindowCake,
				insReturn  : this
			});
		}
	},

	/**
	 *
	*/
	_iniVarsWindowCake : function(obj)
	{
		var insCurrent = obj.insReturn;
		if (obj.data) {
			insCurrent._getVarsWindow({data : obj.data});
		}
	},

	/**
	 *
	*/
	_getVarsWindow : function(obj)
	{
		var objData = {};
		objData.id = obj.data.id;
		var str = 'zIndex';
		objData.zIndex = obj.data[str];
		str = 'flagHideNow';
		objData.flagHideNow = obj.data[str];
		this._varsData.push(objData);
	},


	/**
	 *
	*/
	_setVarsZIndex : function(obj)
	{
		obj.arr = obj.arr.sortBy(function(v,i) {
			return obj.arr[i].numZIndex;
		});
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			obj.arr[i].numZIndex = num;
			num++;
		}
		for (var i = 0; i < obj.arrWindow.length; i++) {
			for (var j = 0; j < obj.arr.length; j++) {
				if (obj.arr[j].id == obj.arrWindow[i].id) {
					obj.arrWindow[i].numZIndex = obj.arr[j].numZIndex;
					break;
				}
			}
		}
		for (var i = 0; i < obj.arrGlobal.length; i++) {
			for (var j = 0; j < obj.arr.length; j++) {
				if (obj.arr[j].id.match( /^global(.*?)/ )) {
					if (obj.arr[j].id == ('global' + obj.arrGlobal[i].id)) {
						obj.arrGlobal[i].numZIndex = obj.arr[j].numZIndex;
						break;
					}
				}
			}
		}
	},

	/**
	 *
	*/
	_setVarsFlagCheckNow : function(obj)
	{
		for (var i = 0; i < obj.arrGlobal.length; i++) {
			for (var j = 0; j < obj.arr.length; j++) {
				if (!obj.arr[j].id.match( /^global(.*?)/ )) {
					if (obj.arr[j].id == obj.arrGlobal[i].id) {
						obj.arrGlobal[i].flagCheckNow = (obj.arr[j].flagHideNow)? 0 : 1;
						break;
					}
				}
			}
		}
	},

	/**
	 *
	*/
	insChoice : null,
	_iniChoice : function()
	{
		this.insChoice = new Code_Lib_Choice();
		this.insChoice.iniLoad({
			insRoot    : this,
			idSelf     : this.vars.varsSystem.id.choice,
			insCurrent : this,
			vars       : this.vars.varsChoice
		});

	},

	/**
	 * Print
	*/
	_elePrint : {},
	_varsPrint : '',
	_iniPrint : function()
	{
		this._varsPrint = '';
		this._elePrint = {};
	},
	/**
		{
			strTitle : '',
			strHtml  : '',
			pathCssl : '',
		}
	*/
	setPrint : function(obj)
	{
		this._varsPrint = obj;
		this._elePrint = window.open('front/else/lib/html/print.html', null, 'width=900, height=600, menubar=yes, toolbar=no, scrollbars=yes');
	},

	/**

	*/
	eventPrintEnd : function(obj)
	{
		obj.vars.insCurrent[obj.vars.strFunc]();
	},

	removePrint : function()
	{
		if (this._elePrint == null) {
			alert(this.vars.varsSystem.str.popUp);
		}
		if(this._elePrint.closed == false){
			this._elePrint.close();
		}
	},

	/**
	 * Output
	*/
	eleOutput : null,
	_iniOutput : function()
	{
		var ele = $(document.createElement('form'));
		ele.addClassName('codeLibBaseOutputWrap');
		ele.action = this.vars.varsSystem.path.post;
		ele.method = 'POST';
		ele.id = this.vars.varsSystem.id.output;
		ele.hide();
		$(this.vars.varsSystem.id.root).insert(ele);
		this.eleOutput = ele;
	},

	/**
		{
			querysKey   : [],
			querysValue : [],
		}
	*/
	setOutput : function(obj)
	{
		this.insRoot.removePrint();
		this._resetOutput();
		var token = (this.vars.varsSystem.token)? this.vars.varsSystem.token : '';
		obj.querysKey.push('token');
		obj.querysValue.push(token);
		obj.arr = obj.querysKey;
		for (var i = 0; i < obj.arr.length; i++) {
			var ele = $(document.createElement('input'));
			ele.name = obj.querysKey[i];
			ele.value = obj.querysValue[i];
			this.eleOutput.insert(ele);
		}
		this.eleOutput.submit();
	},

	_resetOutput : function()
	{
		this.eleOutput.innerHTML = '';
	},

	/**
	 * Upload
	*/
	_varsUpload : null,
	_iniUpload : function()
	{
		this._varsUpload = {};

	},

	/**
		{
			id       : '',
			insClass : ins,
			strFunc  : '',
			eleLoading  : ele,
		}
	*/
	setUpload : function(obj)
	{
		if (obj.eleLoading) obj.eleLoading.addClassName('codeLibRequestImgLoading');
		this._varsUpload[obj.id] = {
			insClass    : obj.insClass,
			strFunc     : obj.strFunc,
			eleLoading  : (obj.eleLoading)? obj.eleLoading : ''
		};

	},

	/**
	{
		idTarget : '',
		vars     : array(),
	}
	 */
	eventUpload : function(obj)
	{
		var vars = obj.vars.evalJSON();
		var flag = this._varsUpload[obj.idTarget].insClass[this._varsUpload[obj.idTarget].strFunc]({
			vars : (Object.toJSON(vars)).evalJSON()
		});

		if (!flag) {
			this.removeUpload({idTarget : obj.idTarget});
		}
	},

	/**
	{
		idTarget : '',
	}
	 */
	removeUpload : function(obj)
	{
		if (this._varsUpload[obj.idTarget].eleLoading) {
			this._varsUpload[obj.idTarget].eleLoading.removeClassName('codeLibRequestImgLoading');
		}
		this._varsUpload[obj.idTarget] = null;
	},

	/**
	 *
	*/
	insGlobal : null,
	_iniGlobal : function()
	{
		this.insGlobal = new Code_Lib_Global({
			insRoot    : this,
			idSelf     : this.vars.varsSystem.id.global,
			insCurrent : this,
			allot      : this._getGlobalAllot(),
			vars       : this.vars.varsGlobal
		});

	},

	/**
	 * Global
	*/
	_varsGlobal : null,
	_getGlobalAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == 'dblclickNavi') {
				if (obj.vars.id == 'Logout') {
					insCurrent.setZIndex();
					if (!insCurrent._varsGlobal) {
						insCurrent._varsGlobal = 1;
						insCurrent._sendLogout({flag : 'logout'});
					}
				} else {
					obj.arr = insCurrent._arrInsWindow;
					for (var i = 0; i < obj.arr.length; i++) {
						if (obj.arr[i].id == obj.vars.id) {
							obj.arr[i].insWindow.eventGlobal();
						}
					}
					return 1;
				}
			} else if (obj.from == '_mousedownLine') {
				if (insCurrent.insGlobalMenuWindow[obj.vars.idTarget]) {
					if (obj.vars.flagCheckNow) {
						insCurrent.insGlobalMenuWindow[obj.vars.idTarget].setZIndex();
					} else {
						insCurrent.insGlobalMenuWindow[obj.vars.idTarget].updateHide({ flagEffect : 1 });
					}
				}
			}
		};

		return allot;
	},


	/**
	 * {
	 * 	globalMenu
	 * }
	*/
	insGlobalMenuWindow : {},
	collectGlobalMenu : function(obj)
	{
		var idWindow = obj.insWindow.idWindow;
		if (!this.insGlobalMenuWindow[idWindow]) {
			this.insGlobalMenuWindow[idWindow] = obj.insWindow;
		}
		this.insGlobal.eventMenu({
			id       : obj.insWindow.idWindow,
			strTitle : obj.insWindow.vars.strTitle
		});
	},

	updateGlobalMenu : function(obj)
	{
		var flagCheckUse = 0;
		var flagCheckNow = 0;
		if (obj.insWindow.vars.flagHideNow) {
			flagCheckNow = 0;
			if (obj.insWindow.vars.flagMenuShowUse) {
				flagCheckUse = 1;

			} else {
				flagCheckUse = 0;
			}

		} else {
			flagCheckUse = 1;
			flagCheckNow = 1;
		}

		this.insGlobal.updateMenuVars({
			id           : obj.insWindow.idWindow,
			flagCheckUse : flagCheckUse,
			flagCheckNow : flagCheckNow
		});
	},

	/**
	 * Logout
	*/
	_iniLogout : function()
	{
		this._setLogout();
	},

	/**
	 *
	*/
	_varsLogout : null,
	_setLogout : function()
	{
		this._varsLogout = {
			interval : setInterval(this._runLogout.bind(this), 30 * 1000),
			stamp    : (new Date()).getTime(),
			loop     : 1
		};
	},

	/**
	 *
	*/
	_runLogout : function()
	{
		if (this.vars.varsSystem.status.numAutoLogout == 0) return;
		var cut = this.vars.varsSystem.status;
		var num = cut.numAutoLogout * 60 * 1000 * this._varsLogout.loop;
		var run = (new Date()).getTime() - this._varsLogout.stamp;
		if (run >= num) {
			this._sendLogout({flag : 'autoLogout'});
		}
	},

	/**
	 *
	*/
	_resetLogout : function()
	{
		if (!this.vars.varsSystem.status.numAutoLogout) return;
		this._varsLogout.stamp = (new Date()).getTime();
		this._varsLogout.loop = 1;
	},

	/**
	 *
	*/
	_sendLogout : function(obj)
	{
		this.iniPopup({flag : obj.flag});
		var arrayKey = ['class', 'module', 'ext', 'func', 'db'];
		var arrayValue = ['core', 'Base', 'Logout', 'Value', 'master'];
		this.insRequest.set({
			flagLock        : 1,
			insCurrent      : this.insSelf,
			flagEscape      : 1,
			path            : this.vars.varsSystem.path.post,
			querysKey       : arrayKey,
			querysValue     : arrayValue,
			functionSuccess : '_sendLogoutSuccess',
			functionFail    : '_sendLogoutFail'
		});
	},

	/**
	 *
	*/
	_sendLogoutSuccess : function(obj)
	{
		var insCookie = new Code_Lib_Cookie();
		insCookie.setData({
			strKey     : 'id',
			value      : '',
			numExpires : 0,
			path       : '',
			strDomain  : ''
		});
		location.href = this.vars.varsSystem.path.post;
	},

	/**
	 *
	*/
	_sendLogoutFail : function(obj)
	{
		alert(this.vars.varsSystem.str.errorRequest);
	},

	/**
	 * Popup
	*/
	insPopup : null,
	iniPopup : function(obj)
	{
		this._setPopup(obj);
	},

	/**
	 *
	*/
	_setPopup : function(obj)
	{
		this.insPopup = new Code_Lib_Popup({
			eleInsert : $(this.vars.varsSystem.id.root),
			insRoot   : this,
			idSelf    : this.vars.varsSystem.id.popup + (new Date()).getTime(),
			allot     : function(){},
			vars      : obj
		});
	},

	/**
	 *
	*/
	_arrInsWindow : [],
	_iniWindow : function()
	{
		this._setWindow({
			arr       : this.vars.varsWindow,
			arrStatus : this.vars.varsSystem.status.arrModule
		});
	},

	/**
	 *
	*/
	_setWindow : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'Base') {
				this._setWindowChild({vars : obj.arr[i]});

			} else {
				var id = obj.arr[i].id.toLowerCase();

				if (!obj.arrStatus[id]) continue;
				if (!obj.arrStatus[id].flagUse) continue;
				this._setWindowChild({vars : obj.arr[i]});
			}
		}
	},

	/**
	 *
	*/
	_setWindowChild : function(obj)
	{
		var insWindow = new Code_Lib_Window();
		insWindow.iniLoad({
			eleInsert  : $(this.vars.varsSystem.id.root),
			insRoot    : this,
			insCurrent : this,
			idSelf     : this.vars.varsSystem.id.window + obj.vars.id,
			allot      : this._getWindowAllot(),
			vars       : obj.vars
		});
		var objData = {
			insWindow : insWindow,
			insModule : null,
			id        : obj.vars.id
		};
		this._arrInsWindow.push(objData);
	},

	/**
	 *
	*/
	_getWindowAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == '_mousedownHide') {
				if (!insCurrent.vars.flagHideNow) {
					obj.arr = insCurrent.insCurrent._arrInsWindow;
					insCurrent.insCurrent._hideWindow(obj);
				}

			} else if (obj.from == 'eventGlobal') {
				if (!insCurrent.vars.flagHideNow) {
					obj.arr = insCurrent.insCurrent._arrInsWindow;
					insCurrent.insCurrent._hideWindow(obj);
				}

			} else if (obj.from == '_mousedownBoot') {
				insCurrent.insCurrent._sendWindow({
					insCurrent : insCurrent
				});

			} else if (obj.from == '_mouseupResize' || obj.from == '_mousedownCover' || obj.from == '_resizeCover') {
				var array = insCurrent.insCurrent._arrInsWindow;
				for (var i = 0; i < array.length; i++) {
					if (array[i].id == insCurrent.vars.id && array[i].insModule) {
						array[i].insModule.eventWindow();
					}
				}

			}
		};

		return allot;
	},

	/**
	 * {
	 * 	idTarget : str
	 * }
	*/
	setBootWindow : function(obj)
	{
		/*global checknow*/
		this.insGlobal.eventBasePortal({
			idTarget : obj.idTarget
		});

		obj.arr = this._arrInsWindow;
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == obj.idTarget) {
				if (obj.arr[i].insWindow.vars.flagHideNow) {
					obj.arr[i].insWindow.updateHide({ flagEffect : 1 });

				} else {
					obj.arr[i].insWindow.setScroll();

				}
				if (!obj.arr[i].insModule) {
					obj.arr[i].insWindow.setBoot();
				}
				break;
			}
		}
	},

	/**
	 *
	*/
	_hideWindow : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == obj.insCurrent.vars.id && obj.arr[i].insModule) {
				obj.arr[i].insModule.eventHide();
			}
		}
	},

	/**
	 *
	*/
	_varsWindow : null,
	_sendWindow : function(obj)
	{
		var varsStamp = {};
		var jsonStamp = (Object.toJSON(varsStamp));
		var flagClass = (obj.insCurrent.vars.id == 'Base')? 'Core' : 'Plugin';
		var arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp'];
		var arrayValue = [flagClass, obj.insCurrent.vars.id, 'Portal', '', 'Js', 'slave' ,jsonStamp];
		this.insRequest.set({
			flagLock        : 0,
			numZIndex       : this.vars.varsSystem.num.zIndex,
			insCurrent      : this,
			flagEscape      : 1,
			path            : this.vars.varsSystem.path.post,
			querysKey       : arrayKey,
			querysValue     : arrayValue,
			functionSuccess : '_sendWindowSuccess',
			functionFail    : '_sendWindowFail'
		});
		var ele = $(document.createElement('span'));
		$(obj.insCurrent.idWindow).down('.codeLibWindowBodyTopMiddleWrap',0).insert(ele);
		ele.addClassName('codeLibRequestImgLoading');
		ele.addClassName('codeLibRequestImgLoadingPos');
		this._varsWindow = {
			insCurrent : obj.insCurrent,
			ele        : ele
		};
	},

	/**
	 *
	*/
	_sendWindowSuccess : function(obj)
	{

		if (obj.response.responseText.isJSON()) {
			var json = obj.response.responseText.evalJSON();
			if (json.flag == 0) {
				alert(this.insRoot.vars.varsSystem.str.errorSession);
				return;
			}
		}

		var eleScript = $(document.createElement('script'));
		eleScript.type = 'text/javascript';
		eleScript.text = obj.response.responseText;
		var eleHead = document.getElementsByTagName('head').item(0);
		eleHead.appendChild(eleScript);
		var idSelf = this._varsWindow.insCurrent.vars.id;
		var strClass;
		if (idSelf == 'Base') {
			strClass =  eval('Code_Core_Base_Portal');
		} else {
			strClass =  eval('Code_Plugin_'+ idSelf + '_Portal');
		}
		var insClass = new strClass();
		insClass.iniLoad({
			eleInsert  : $(this._varsWindow.insCurrent.idWindow).down('.codeLibWindowBodyTopMiddleWrap', 0),
			strClass   : (idSelf == 'Base')? 'Core' : 'Plugin',
			idModule   : idSelf,
			strExt     : 'Portal',
			insTop     : insClass,
			insRoot    : this,
			idSelf     : idSelf +'Module',
			insWindow   : this._varsWindow.insCurrent,
			insCurrent : this._varsWindow.insCurrent
		});

		for (var i = 0; i < this._arrInsWindow.length; i++) {
			if (this._arrInsWindow[i].id == this._varsWindow.insCurrent.vars.id) {
				this._arrInsWindow[i].insModule = insClass;
			}
		}
		this._varsWindow.ele.remove();
		this._varsWindow = {};
	},

	/**
	 *
	*/
	_sendWindowFail : function(obj)
	{
		this._varsWindow.ele.remove();
		alert(this.vars.varsSystem.str.errorRequest);
	},

	/**
	 * PopupTimer
	*/
	_iniPopupTimer : function()
	{
		this._setPopupTimer();
	},

	/**
	 *
	*/
	insPopupTimer : null,
	_setPopupTimer : function()
	{
		if ($(this.vars.varsSystem.id.popup)) {
			this.insPopupTimer.insWindow.removeWrap();
			this.insPopupTimer.removeWrap();
		}
		this.insPopupTimer = new Code_Lib_PopupTimer({
			eleInsert : $(this.vars.varsSystem.id.root),
			insRoot   : this,
			idSelf    : this.vars.varsSystem.id.popup,
			vars      : this.vars.varsPopup,
			allot     : function(){}
		});
	}
});
<?php }
}
?>