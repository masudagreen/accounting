
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Core_Login = Class.create(Code_Lib_Ext,{

	vars : {"varsSystem":{"path":{"post":"index.php"},"token":"","id":{"root":"Root","login":"Login"},"num":{"expiresLang":315360000,"expiresSession":90000,"limit":3,"zIndex":0},"str":{"errorRequest":"\u30b5\u30fc\u30d0\u63a5\u7d9a\u30a8\u30e9\u30fc","errorServer":"\u30b5\u30fc\u30d0\u30a8\u30e9\u30fc","errorConnect":"\u30b5\u30fc\u30d0\u63a5\u7d9a\u30a8\u30e9\u30fc"}},"varsWindow":{"id":"Login","strTitle":"\u30ed\u30b0\u30a4\u30f3","strClass":"codeLoginImgIcon","flagLockUse":0,"flagLockNow":"","flagCakeUse":1,"flagRemoveUse":0,"flagCoverUse":0,"flagHideUse":0,"flagHideNow":0,"flagReloadUse":0,"flagFoldUse":0,"flagFoldNow":0,"flagMoveUse":1,"flagZIndexUse":0,"flagResizeUse":0,"flagResizeIni":"all","flagResizeNow":"all","flagSkeletonUse":0,"flagBootUse":"auto","flagSwitchUse":1,"numWidthTitle":0,"numLeft":50,"numTop":50,"numWidth":400,"numHeight":370,"numWidthMin":300,"numHeightMin":250,"numZIndex":0},"portal":[]},


	initialize : function()
	{
		this._iniVars();
		if (this._iniBrowser()) return;
		this._iniListener();
		this._iniRequest();
		this._iniKey();
		this._iniCake();
	},

	/**
	 *
	*/
	iniLoad : function()
	{
		this._iniWindow();
	},

	/**
	 *
	*/
	_iniRequest : function ()
	{
		this._extRequest();
	},

	/**
	 *
	*/
	_iniCake : function()
	{
		this._extCake({
			id       : this.vars.varsSystem.id.login,
			pathSelf : this.vars.varsSystem.path.post
		});
	},

	/**
	 *
	*/
	_iniBrowser : function () {
		return this._extBrowser();
	},

	/**
	 *
	*/
	_iniVars : function ()
	{
		this._extVars();
	},

	/**
	 *
	*/
	_iniListener : function()
	{
		this._extListener();
	},

	/**
	 *
	*/
	_iniKey : function()
	{
		this._extKey();
	},

	/**
	 *
	*/
	_iniWindow : function ()
	{
		this._extWindow({
			eleInsert : $(this.vars.varsSystem.id.root),
			vars      : this.vars.varsWindow
		});
	},

	/**
	 *
	*/
	_getWindowAllot : function ()
	{
		var allot = function (obj) {
			var insCurrent = obj.insCurrent;
			if (obj.from == '_mousedownBoot') {
				insCurrent.insCurrent._sendWindow();
			}
		};

		return allot;
	},

	/**
	 *
	*/
	_varsWindow : null,
	_sendWindow : function()
	{
		var arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db'];
		var arrayValue = ['Core', 'Login', 'Portal', '', 'Vars','slave'];
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
		$(this.insWindow.idWindow).down('.codeLibWindowBodyTopMiddleWrap',0).insert(ele);
		ele.addClassName('codeLibRequestImgLoading');
		ele.addClassName('codeLibRequestImgLoadingPos');
		this._varsWindow = {ele : ele};
	},

	/**
	 *
	*/
	_sendWindowSuccess : function(obj)
	{
		if (obj.response.responseText.isJSON()) {
			var json = obj.response.responseText.evalJSON();
			if (json.flag == 1) {
				this.vars.varsSystem.token = json.data.token;
				this.vars.portal = json.data.portal;
				this._iniLayout();
				this._iniDetail();
			}
		}
		else alert(this.vars.varsSystem.str.errorServer);
		this._varsWindow.ele.remove();
		this._varsWindow = {};
	},

	/**
	 *
	*/
	_sendWindowFail : function(obj)
	{
		this._varsWindow.ele.remove();
		alert(this.vars.varsSystem.str.fail);
	},

	/**
	 *
	*/
	_iniLayout : function()
	{
		this._extLayout({vars  : this.vars.portal.varsTemplateLayout});
	},

	/**
	 *
	*/
	_iniDetail : function()
	{
		this._setDetail();
	},

	/**
	 *
	*/
	insDetail : null,
	_setDetail : function()
	{
		this.insDetail = new Code_Lib_ControlDetail({
			insUnder   : this.insLayout.insDetailUnder,
			insTool    : null,
			insRoot    : this.insRoot,
			insCurrent : this.insSelf,
			idSelf     : 'Detail',
			allot      : this._getDetailAllot(),
			vars       : this.vars.portal.varsDetail
		});
		this._resetDetail({flag: 'start'});
	},

	/**
	 *
	*/
	_resetDetail : function(obj)
	{
		var str = obj.flag;
		this.insDetail.eventList({
			strTitle : this.vars.portal.varsDetail.varsDetail[str].strTitle,
			strClass : null,
			vars : {
				varsDetail : this.vars.portal.varsDetail.varsDetail[str].varsDetail,
				varsBtn    : this.vars.portal.varsDetail.varsDetail[str].varsBtn,
				varsEdit   : {}
			}
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
			var array = obj.from.split('-');
			if (obj.from == 'eventLayout');
			else if (obj.from == 'eventRemove');
			else if (obj.from == 'form-eventBtnBottom') {
				insCurrent._iniSwitch({flag: obj.vars.vars.vars.flag});
			}

		};

		return allot;
	},

	/**
	 *
	*/
	_varsSwitch: {flag : 'start'},
	_iniSwitch : function(obj)
	{
		if (obj.flag == 'save') {
			var str = '_ini' + this._varsSwitch.flag.capitalize();
			this[str]();
		} else {
			this._varsSwitch.flag = obj.flag;
			this._resetDetail({flag: this._varsSwitch.flag});
		}
	},

	/**
	 *
	*/
	_iniStart : function()
	{
		if (this.insDetail.checkForm({flagType : 'common'})) return;
		this._sendStart();
	},

	/**
	 *
	*/
	_sendStart : function(obj)
	{
		var jsonValue = {
			flag     : '',
			vars     : this.insDetail.getFormValue(),
			idTarget : ''
		};
		var jsonValue = Object.toJSON(jsonValue);
		var arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonValue'];
		var arrayValue = ['Core', 'Login', 'Start', '', 'Value', 'master', jsonValue];

		this.insRequest.set({
			flagLock        : 0,
			insCurrent      : this,
			flagEscape      : 1,
			path            : this.vars.varsSystem.path.post,
			querysKey       : arrayKey,
			querysValue     : arrayValue,
			functionSuccess : '_sendStartSuccess',
			functionFail    : '_sendStartFail',
			eleLoadStatus   : this.insDetail.getEleLoading()
		});
	},

	/**
	 *
	*/
	_sendStartSuccess : function(obj)
	{
		if (obj.response.responseText.isJSON()) {
			var json = obj.response.responseText.evalJSON();
			if (json.flag == 1) {
				this._setStart({data: json.data});

			} else if (json.flag == 'second') {
				this._setSecond();

			} else {
				this.insDetail.showBtnBottom();
				this.insDetail.showFormAttestError({flagType : 'common'});
				this._checkStart();
			}
		}
		else alert(this.vars.varsSystem.str.errorServer);
	},

	/**
	 *
	*/
	_setSecond : function(obj)
	{
		this.insDetail.eventList({
			strTitle : this.vars.portal.varsDetail.varsDetail.endSecond.strTitle,
			strClass : null,
			vars : {
				varsDetail : this.vars.portal.varsDetail.varsDetail.endSecond.varsDetail,
				varsBtn    : [],
				varsEdit   : {}
			}
		});
	},

	/**
	 *
	*/
	_sendStartFail : function(obj)
	{
		alert(this.vars.varsSystem.str.fail);
	},


	/**
	 *
	*/
	_setStart : function(obj)
	{
		var insCookie = new Code_Lib_Cookie();
		insCookie.setData({
			strKey     : 'id',
			value      : obj.data.id,
			numExpires : this.vars.varsSystem.num.expiresSession,
			path       : '',
			strDomain  : ''
		});
		location.href = this.vars.varsSystem.path.post;
	},

	/**
	 *
	*/
	_varsStart : {num : 0},
	_checkStart : function(obj)
	{
		this._varsStart.num++;
		if (this._varsStart.num > this.vars.varsSystem.num.limit) {
			location.href = this.vars.varsSystem.path.post;
		}
	},

	/**
	 *
	*/
	_iniLang : function()
	{
		if (this.insDetail.checkForm({flagType : 'common'})) return;
		var strLang = this._getLang({arr: this.insDetail.getForm()});
		this._setLang({strLang : strLang});
	},

	/**
	 *
	*/
	_getLang : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			return obj.arr[i].value;
		}
	},

	/**
	 *
	*/
	_setLang : function(obj)
	{
		var insCookie = new Code_Lib_Cookie();
		insCookie.setData({
			strKey     : 'lang',
			value      : obj.strLang,
			numExpires : this.vars.varsSystem.num.expiresLang,
			path       : '',
			strDomain  : ''
		});
		location.href = this.vars.varsSystem.path.post;
	},


	/**
	 *
	*/
	_iniSign : function()
	{

		if (this.insDetail.checkForm({flagType : 'common'})) return;
		var vars = this.insDetail.getFormValue();
		if (vars.StrPassword != vars.StrPasswordConfirm) {
			this.insDetail.showFormAttestError({flagType : 'common'});
			return;
		}
		this._eventValue({
			vars     : vars,
			idTarget : ''
		});
		this._sendSignConnect({vars : vars});
	},

	/**
	 *
	*/
	_sendSignConnect : function(obj)
	{
		var jsonValue = Object.toJSON(this._varsValue);
		var arrayKey = [], arrayValue = [];

		arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonValue'];
		arrayValue = ['Core', 'Login', 'Sign', '', 'Value', 'master', jsonValue];

		this.insRoot.insRequest.set({
			flagLock        : 0,
			numZIndex       : this.insRoot.getZIndex(),
			insCurrent      : this,
			flagEscape      : 1,
			path            : this.insRoot.vars.varsSystem.path.post,
			querysKey       : arrayKey,
			querysValue     : arrayValue,
			functionSuccess : '_sendSignConnectSuccess',
			functionFail    : '_sendDetailConnectFail',
			eleLoadStatus   : this.insDetail.insUnder.eleFormat.header.down('.codeLibBaseMarginRightFive', 0)
		});
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
	_sendSignConnectSuccess : function(obj)
	{
		if (obj.response.responseText.isJSON()) {
			var json = obj.response.responseText.evalJSON();
			if (json.flag) {
				this._eventSignConnectSuccess({json : json});
			}
		}
		else alert(this.insRoot.vars.varsSystem.str.errorRequest);
	},

	/**
	 *
	*/
	_eventSignConnectSuccess : function(obj)
	{
		if (obj.json.flag == 1) {
			this.insDetail.eventList({
				strTitle : this.vars.portal.varsDetail.varsDetail.endSign.strTitle,
				strClass : null,
				vars : {
					varsDetail : this.vars.portal.varsDetail.varsDetail.endSign.varsDetail,
					varsBtn    : [],
					varsEdit   : {}
				}
			});

		} else if (obj.json.flag == 40) {
			this.insDetail.showBtnBottom();
			this.insDetail.showFormAttestError({flagType : 'mail'});

		} else if (obj.json.flag == 41) {
			this.insDetail.showBtnBottom();
			this.insDetail.showFormAttestError({flagType : 'mailHost'});

		} else if (obj.json.flag == 42) {
			alert(this.insRoot.vars.varsSystem.str.errorServer);

		}
	},

	/**
	 *
	*/
	_iniForgot : function()
	{

		if (this.insDetail.checkForm({flagType : 'common'})) return;
		var vars = this.insDetail.getFormValue();
		this._eventValue({
			vars     : vars,
			idTarget : ''
		});
		this._sendForgotConnect({vars : vars});
	},

	/**
	 *
	*/
	_sendForgotConnect : function(obj)
	{
		var jsonValue = Object.toJSON(this._varsValue);
		var arrayKey = [], arrayValue = [];

		arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonValue'];
		arrayValue = ['Core', 'Login', 'Forgot', '', 'Value', 'master', jsonValue];

		this.insRoot.insRequest.set({
			flagLock        : 0,
			numZIndex       : this.insRoot.getZIndex(),
			insCurrent      : this,
			flagEscape      : 1,
			path            : this.insRoot.vars.varsSystem.path.post,
			querysKey       : arrayKey,
			querysValue     : arrayValue,
			functionSuccess : '_sendForgotConnectSuccess',
			functionFail    : '_sendDetailConnectFail',
			eleLoadStatus   : this.insDetail.insUnder.eleFormat.header.down('.codeLibBaseMarginRightFive', 0)
		});
	},

	/**
	 *
	*/
	_sendForgotConnectSuccess : function(obj)
	{
		if (obj.response.responseText.isJSON()) {
			var json = obj.response.responseText.evalJSON();
			if (json.flag) {
				this._eventForgotConnectSuccess({json : json});
			}
		}
		else alert(this.insRoot.vars.varsSystem.str.errorRequest);
	},

	/**
	 *
	*/
	_eventForgotConnectSuccess : function(obj)
	{

		if (obj.json.flag == 1) {
			this.insDetail.eventList({
				strTitle : this.vars.portal.varsDetail.varsDetail.endForgot.strTitle,
				strClass : null,
				vars : {
					varsDetail : this.vars.portal.varsDetail.varsDetail.endForgot.varsDetail,
					varsBtn    : [],
					varsEdit   : {}
				}
			});

		} else if (obj.json.flag == 42) {
			alert(this.insRoot.vars.varsSystem.str.errorServer);

		}
	}
});


