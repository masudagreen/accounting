
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Core_Base_Root = Class.create(Code_Lib_Ext,
{


	vars : {"strTitle":"\u7d71\u5236\u30e2\u30b8\u30e5\u30fc\u30eb","varsSystem":{"status":[],"num":{"zIndex":0},"token":"","id":{"root":"Root","choice":"Choice","global":"Global","window":"Window","popup":"Popup","temp":"Temp","output":"Output"},"path":{"post":"index.php","file":"output.php"},"flag":{"ssl":0,"zIndexCookieUse":1,"onstageCookieUse":1,"browser":1},"str":{"comma":"\uff0c","space":"\u3000","fail":"\u30b7\u30b9\u30c6\u30e0\u30a8\u30e9\u30fc","popUp":"\u30dd\u30c3\u30d7\u30a2\u30c3\u30d7\u304c\u30d6\u30ed\u30c3\u30af\u3055\u308c\u3066\u3044\u308b\u305f\u3081\u30a8\u30e9\u30fc\u304c\u751f\u3058\u305f\u3088\u3046\u3067\u3059\u3002\u30dd\u30c3\u30d7\u30a2\u30c3\u30d7\u3092\u6709\u52b9\u306b\u3057\u3066\u30da\u30fc\u30b8\u3092\u66f4\u65b0\u3057\u3066\u304f\u3060\u3055\u3044\u3002","selectRequire":"\u9078\u629e\u304c\u5fc5\u8981\u306a\u3088\u3046\u3067\u3059\u3002","oldData":"\u524d\u63d0\u3068\u306a\u308b\u30c7\u30fc\u30bf\u304c\u9673\u8150\u5316\u3057\u3066\u3044\u308b\u3088\u3046\u3067\u3059\u3002\u30da\u30fc\u30b8\u7b49\u3092\u66f4\u65b0\u3057\u3066\u304f\u3060\u3055\u3044\u3002","8":"\u524d\u63d0\u3068\u306a\u308b\u30c7\u30fc\u30bf\u304c\u9673\u8150\u5316\u3057\u3066\u3044\u308b\u3088\u3046\u3067\u3059\u3002\u30da\u30fc\u30b8\u7b49\u3092\u66f4\u65b0\u3057\u3066\u304f\u3060\u3055\u3044\u3002","40":"\u524d\u63d0\u3068\u306a\u308b\u30c7\u30fc\u30bf\u304c\u9673\u8150\u5316\u3057\u3066\u3044\u308b\u3088\u3046\u3067\u3059\u3002\u30da\u30fc\u30b8\u7b49\u3092\u66f4\u65b0\u3057\u3066\u304f\u3060\u3055\u3044\u3002","errorRequest":"\u30ea\u30af\u30a8\u30b9\u30c8\u30a8\u30e9\u30fc","maintenance":"\u5f53\u8a72\u30e2\u30b8\u30e5\u30fc\u30eb\u306f\u3001\u30e1\u30f3\u30c6\u30ca\u30f3\u30b9\u4e2d\u306e\u3088\u3046\u3067\u3059\u3002\u3057\u3070\u3089\u304f\u3057\u3066\u304b\u3089\u30a2\u30af\u30bb\u30b9\u3057\u3066\u304f\u3060\u3055\u3044\u3002","errorMail":"\u30e1\u30fc\u30eb\u914d\u4fe1\u304c\u6b63\u5e38\u306b\u52d5\u4f5c\u3057\u306a\u304b\u3063\u305f\u3088\u3046\u3067\u3059\u3002\u30e1\u30fc\u30eb\u8a8d\u8a3c\u624b\u7d9a\u306a\u3069\u30b7\u30b9\u30c6\u30e0\u4e0a\u5fc5\u8981\u306a\u30e1\u30fc\u30eb\u3067\u3042\u308b\u5834\u5408\u3001\u30b7\u30b9\u30c6\u30e0\u7ba1\u7406\u8005\u3078\u81f3\u6025\u9023\u7d61\u3057\u3066\u304f\u3060\u3055\u3044\u3002","errorConnect":"\u30ea\u30af\u30a8\u30b9\u30c8\u30a8\u30e9\u30fc","errorDataMax":"\u30c7\u30fc\u30bf\u4fdd\u5b58\u9818\u57df\u3092\u8d85\u904e\u3057\u3066\u3057\u307e\u3046\u305f\u3081\u30ea\u30af\u30a8\u30b9\u30c8\u304c\u898b\u9001\u3089\u308c\u305f\u3088\u3046\u3067\u3059\u3002","errorSession":"\u30ed\u30b0\u30a4\u30f3\u30bb\u30c3\u30b7\u30e7\u30f3\u304c\u5207\u308c\u3066\u3044\u308b\u3088\u3046\u3067\u3059\u3002\u304a\u624b\u6570\u3067\u3059\u304c\u3001\u30da\u30fc\u30b8\u3092\u66f4\u65b0\u3057\u3066\u518d\u5ea6\u5165\u5834\u3057\u76f4\u3057\u3066\u304f\u3060\u3055\u3044\u3002"}},"varsGlobal":{"varsStatus":{"numZIndex":0},"tmplContext":{"varsStatus":{"numTop":0,"numLeft":0,"flagNow":""},"varsDetail":[],"tmplDetail":{"id":"","flagCheckUse":1,"flagCheckNow":1,"strTitle":"","strClass":"codeLibBaseImgSheet","vars":{"idTarget":""},"child":[]}},"varsDetail":[{"flagCheckNow":0,"numZIndex":0,"numLeft":34,"numTop":38,"numSort":0,"id":"Logout","strTitle":"\u30ed\u30b0\u30a2\u30a6\u30c8","strClass":"codeCoreLogoutImgIcon","strClassOver":"codeCoreLogoutImgIconOver","strClassSmall":"codeCoreLogoutImgIconSmall"},{"flagCheckNow":0,"numZIndex":0,"numLeft":34,"numTop":68,"numSort":1,"id":"Base","strTitle":"\u7d71\u5236\u30e2\u30b8\u30e5\u30fc\u30eb","strClass":"codeCoreBaseImgIcon","strClassOver":"codeCoreBaseImgIconOver","strClassSmall":"codeCoreBaseImgIconSmall"},{"numZIndex":0,"numLeft":34,"numTop":98,"numSort":3,"id":"Accounting","strTitle":"\u4f1a\u8a08\u30e2\u30b8\u30e5\u30fc\u30eb","strClass":"codePluginAccountingImgIcon","strClassOver":"codePluginAccountingImgIconOver","strClassSmall":"codePluginAccountingImgIconSmall"},{"numZIndex":0,"numLeft":34,"numTop":128,"numSort":3,"id":"Support","strTitle":"\u30b5\u30dd\u30fc\u30c8\u30e2\u30b8\u30e5\u30fc\u30eb","strClass":"codePluginSupportImgIcon","strClassOver":"codePluginSupportImgIconOver","strClassSmall":"codePluginSupportImgIconSmall"}]},"varsChoice":{"varsDetail":[{"id":"Account","idModule":"Base","strTitle":"\u30a2\u30ab\u30a6\u30f3\u30c8","flagCheckUse":0,"varsRequest":{"strClass":"Core","idModule":"Base","strExt":"Account","strChild":"Choice","strFunc":"Js"}},{"id":"Term","idModule":"Base","strTitle":"\u6709\u52b9\u671f\u9593\u30d1\u30bf\u30fc\u30f3","flagCheckUse":0,"varsRequest":{"strClass":"Core","idModule":"Base","strExt":"Term","strChild":"Choice","strFunc":"Js"}},{"id":"Module","idModule":"Base","strTitle":"\u30e2\u30b8\u30e5\u30fc\u30eb\u30d1\u30bf\u30fc\u30f3","flagCheckUse":0,"varsRequest":{"strClass":"Core","idModule":"Base","strExt":"Module","strChild":"Choice","strFunc":"Js"}},{"id":"PluginAccountingEntity","idModule":"Accounting","strTitle":"\u4e8b\u696d\u4f53","flagCheckUse":0,"strClass":"codePluginAccountingImgIcon","varsRequest":{"strClass":"Plugin","idModule":"Accounting","strExt":"Entity","strChild":"Choice","strFunc":"Js"}},{"id":"PluginAccountingEntityWithoutConfig","idModule":"Accounting","strTitle":"\u4e8b\u696d\u4f53","flagCheckUse":0,"strClass":"codePluginAccountingImgIcon","varsRequest":{"strClass":"Plugin","idModule":"Accounting","strExt":"Entity","strChild":"ChoiceWithoutConfig","strFunc":"Js"}},{"id":"PluginAccountingAccount","idModule":"Accounting","strTitle":"\u30a2\u30ab\u30a6\u30f3\u30c8","flagCheckUse":0,"strClass":"codePluginAccountingImgIcon","varsRequest":{"strClass":"Plugin","idModule":"Accounting","strExt":"Account","strChild":"Choice","strFunc":"Js"}},{"id":"PluginAccountingAccountEntityPermit","idModule":"Accounting","strTitle":"\u30a2\u30ab\u30a6\u30f3\u30c8","flagCheckUse":0,"strClass":"codePluginAccountingImgIcon","varsRequest":{"strClass":"Plugin","idModule":"Accounting","strExt":"AccountEntity","strChild":"ChoicePermit","strFunc":"Js"}},{"id":"PluginAccountingAuthority","idModule":"Accounting","strTitle":"\u30a2\u30af\u30bb\u30b9\u6a29\u9650\u30d1\u30bf\u30fc\u30f3","flagCheckUse":0,"strClass":"codePluginAccountingImgIcon","varsRequest":{"strClass":"Plugin","idModule":"Accounting","strExt":"Authority","strChild":"Choice","strFunc":"Js"}},{"id":"PluginAccountingAccess","idModule":"Accounting","strTitle":"\u30a2\u30af\u30bb\u30b9\u53ef\u80fd\u9805\u76ee\u30d1\u30bf\u30fc\u30f3","flagCheckUse":0,"strClass":"codePluginAccountingImgIcon","varsRequest":{"strClass":"Plugin","idModule":"Accounting","strExt":"Access","strChild":"Choice","strFunc":"Js"}},{"id":"PluginAccountingLogFile","idModule":"Accounting","strTitle":"\u8a3c\u6191\u30d5\u30a1\u30a4\u30eb","flagCheckUse":0,"strClass":"codePluginAccountingImgIcon","varsRequest":{"strClass":"Plugin","idModule":"Accounting","strExt":"File","strChild":"Choice","strFunc":"Js"}},{"id":"PluginSupportLogFile","idModule":"Support","strTitle":"\u30d5\u30a1\u30a4\u30eb","flagCheckUse":0,"strClass":"codePluginSupportImgIcon","varsRequest":{"strClass":"Plugin","idModule":"Support","strExt":"File","strChild":"Choice","strFunc":"Js"}}]},"varsWindow":[{"id":"Base","strTitle":"\u7d71\u5236\u30e2\u30b8\u30e5\u30fc\u30eb","strClass":"codeCoreBaseImgIcon","flagLockUse":0,"flagLockNow":"","flagCakeUse":1,"flagRemoveUse":0,"flagCoverUse":1,"flagHideUse":1,"flagHideNow":1,"flagFoldUse":1,"flagFoldNow":0,"flagMoveUse":1,"flagZIndexUse":1,"flagResizeUse":1,"flagResizeIni":"all","flagResizeNow":"all","flagSkeletonUse":0,"flagBootUse":1,"flagSwitchUse":1,"flagMenuUse":1,"flagMenuShowUse":1,"numWidthTitle":0,"numLeft":50,"numTop":50,"numWidth":800,"numHeight":600,"numWidthMin":800,"numHeightMin":600,"numZIndex":0},{"id":"Accounting","strTitle":"\u4f1a\u8a08\u30e2\u30b8\u30e5\u30fc\u30eb","strClass":"codePluginAccountingImgIcon","flagLockUse":0,"flagLockNow":"","flagCakeUse":1,"flagRemoveUse":0,"flagCoverUse":1,"flagHideUse":1,"flagHideNow":1,"flagFoldUse":1,"flagFoldNow":0,"flagMoveUse":1,"flagZIndexUse":1,"flagResizeUse":1,"flagResizeIni":"all","flagResizeNow":"all","flagSkeletonUse":0,"flagBootUse":1,"flagSwitchUse":1,"flagMenuUse":1,"flagMenuShowUse":1,"numWidthTitle":0,"numLeft":50,"numTop":50,"numWidth":950,"numHeight":600,"numWidthMin":800,"numHeightMin":600,"numZIndex":0},{"id":"Support","strTitle":"\u30b5\u30dd\u30fc\u30c8\u30e2\u30b8\u30e5\u30fc\u30eb","strClass":"codePluginSupportImgIcon","flagLockUse":0,"flagLockNow":"","flagCakeUse":1,"flagRemoveUse":0,"flagCoverUse":1,"flagHideUse":1,"flagHideNow":1,"flagFoldUse":1,"flagFoldNow":0,"flagMoveUse":1,"flagZIndexUse":1,"flagResizeUse":1,"flagResizeIni":"all","flagResizeNow":"all","flagSkeletonUse":0,"flagBootUse":1,"flagSwitchUse":1,"flagMenuUse":1,"flagMenuShowUse":1,"numWidthTitle":0,"numLeft":50,"numTop":50,"numWidth":800,"numHeight":650,"numWidthMin":800,"numHeightMin":650,"numZIndex":0}],"varsPopup":{"varsLayout":{"varsStatus":{"flagUse":1,"flagLockUse":0,"numZIndex":0},"varsMenu":{"numWidth":200,"numHeight":100},"varsFormat":{"id":"","flagType":"normalFormat","numHeight":0,"numWidth":0,"flagHeaderLeftUse":1,"strTitleHeaderLeft":"\u65b0\u7740\u60c5\u5831","pathImgHeaderLeft":"front\/else\/lib\/img\/popup\/load.png","flagHeaderRightUse":1,"strTitleHeaderRight":"","pathImgHeaderRight":"front\/else\/lib\/img\/popup\/remove.png","flagBodyAutoUse":1,"strBody":"","pathImgBody":"","flagFooderUse":0,"flagFooderLeftUse":1,"strTitleFooderLeft":"","pathImgFooderLeft":"","flagFooderRightUse":1,"strTitleFooderRight":"","pathImgFooderRight":"","flagHeaderLeftWidth":1,"numWidthHeaderLeft":0,"flagHeaderRightWidth":0,"numWidthHeaderRight":0,"flagFooderLeftWidth":0,"numWidthFooderLeft":0,"flagFooderRightWidth":0,"numWidthFooderRight":0}},"varsDetail":{"numAll":0,"strTitle":0,"stampRegister":0}}},


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
	iniLoad : function()
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
