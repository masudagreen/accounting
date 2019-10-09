
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Core_Confirm = Class.create(Code_Lib_Ext,{

	vars : {"varsSystem":{"path":{"login":"index.php","post":"confirm.php"},"id":{"root":"Root","confirm":"Confirm"},"num":{"limit":3,"expiresSession":90000,"zIndex":0},"str":{"errorServer":"\u30b5\u30fc\u30d0\u30a8\u30e9\u30fc","errorConnect":"\u30b5\u30fc\u30d0\u63a5\u7d9a\u30a8\u30e9\u30fc"}},"varsWindow":{"id":"Confirm","strTitle":"\u8a8d\u8a3c\u624b\u7d9a","strClass":"codeCoreConfirmImgIcon","flagLockUse":0,"flagLockNow":"","flagCakeUse":1,"flagRemoveUse":0,"flagCoverUse":0,"flagHideUse":0,"flagHideNow":0,"flagReloadUse":0,"flagFoldUse":0,"flagFoldNow":0,"flagMoveUse":1,"flagZIndexUse":0,"flagResizeUse":0,"flagResizeIni":"all","flagResizeNow":"all","flagSkeletonUse":0,"flagBootUse":"auto","flagSwitchUse":1,"numWidthTitle":0,"numLeft":50,"numTop":50,"numWidth":500,"numHeight":350,"numWidthMin":500,"numHeightMin":350,"numZIndex":0},"portal":[]},


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
			id       : this.vars.varsSystem.id.confirm,
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
		var strUrl = document.URL;
		strUrl.match(/.*?\?type=(.*?)&id=(.*?)$/);
		var flagType = RegExp.$1;
		var id = RegExp.$2;
		var arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'id', 'type'];
		var arrayValue = ['Core', 'Confirm', 'Portal', '', 'Vars','master', id, flagType];

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
		this._setDetailStart();
	},

	/**
	 *
	*/
	_setDetailStart : function()
	{
		this.insDetail.eventList({
			strTitle : this.vars.portal.varsDetail.varsDetail.strTitle,
			strClass : null,
			vars : {
				varsDetail : this.vars.portal.varsDetail.varsDetail.varsDetail,
				varsBtn    : this.vars.portal.varsDetail.varsDetail.varsBtn,
				varsEdit   : {}
			}
		});
		if (this.vars.portal.varsDetail.varsDetail.idCookie) {
			this._setCookie({data: this.vars.portal.varsDetail.varsDetail.idCookie});
		}
	},

	/**
	 *
	*/
	_setCookie : function(obj)
	{
alert(this.vars.varsSystem.num.expiresSession);
		var insCookie = new Code_Lib_Cookie();
		insCookie.setData({
			strKey     : 'id',
			value      : obj.data,
			numExpires : this.vars.varsSystem.num.expiresSession,
			path       : '',
			strDomain  : ''
		});
		location.href = this.vars.varsSystem.path.login;
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

		};

		return allot;
	}
});


