{literal}
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Config = Class.create(Code_Lib_Ext,{
{/literal}
	vars   : {$varsLoad},
{literal}

	initialize : function()
	{
		this._iniVars();
		if (this._iniBrowser()) return;
		this._iniListener();
		this._iniRequest();
		this._iniKey();
		this._iniCake();
		this._iniLoad();
	},

	/**
	 *
	*/
	_iniLoad : function()
	{
		this._iniWindow();
	},

	/**
	 *
	*/
	_iniCake : function()
	{
		this._extCake({pathSelf : this.vars.varsSystem.path.post});
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
	_iniVars : function (obj)
	{
		this._extVars(obj);
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
	_iniRequest : function ()
	{
		this._extRequest();
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
			vars  : this.vars.varsWindow
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
				insCurrent.insCurrent._iniLayout();
				insCurrent.insCurrent._iniDetail();
			}
		};

		return allot;
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
		this.insDetail.eventList({
			strTitle : null,
			strClass : null,
			vars : {
				varsDetail : this.vars.portal.varsDetail.varsDetail.start.varsDetail,
				varsBtn    : this.vars.portal.varsDetail.varsDetail.start.varsBtn,
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
			else if (obj.from == 'form-eventBtnBottom') insCurrent._checkConnect();

		};

		return allot;
	},


	/**
	 *
	*/
	_checkConnect : function()
	{
		if (this._flagIgnore) {
			if (!confirm(this.vars.varsSystem.str.strConfirm)) {
				this.insDetail.showBtnBottom();
				return;
			}
			this._sendConnectIgnore();
			return;
		}
		var ins = this.insDetail.insForm;
		ins.setValue();
		ins.checkValue();
		ins.resetValueError();
		var flag = ins.checkValueError();
		if (flag) {
			ins.showValueError({flagType: 'common'});
		} else {
			this._sendConnect({arr: ins.getValue()});
		}

	},


	/**
	 *
	*/
	_sendConnect : function(obj)
	{
		var arrayKey = [], arrayValue = [];
		var url = document.URL;
		this._arrayKey = ['class','func','url'];
		this._arrayValue = ['config', 'checkDatabase', url];
		for (var i = 0; i < obj.arr.length; i++) {
			this._arrayKey.push(obj.arr[i].id);
			this._arrayValue.push(obj.arr[i].value);
		}
		this.insRequest.set({
			flagLock        : 1,
			insCurrent      : this.insSelf,
			path            : this.vars.varsSystem.path.post,
			querysKey       : this._arrayKey,
			querysValue     : this._arrayValue,
			functionSuccess : '_sendConnectSuccess',
			functionFail    : '_sendConnectFail',
			eleLoadStatus   : this.insDetail.getEleLoading()
		});

	},

	/**
	 *
	*/
	_sendConnectIgnore : function()
	{
		var arrayKey = (Object.toJSON(this._arrayKey)).evalJSON();
		var arrayValue = (Object.toJSON(this._arrayValue)).evalJSON();
		var url = document.URL;
		arrayKey.push('flagIgnore');
		arrayValue.push(1);
		this.insRequest.set({
			flagLock        : 1,
			insCurrent      : this.insSelf,
			path            : this.vars.varsSystem.path.post,
			querysKey       : arrayKey,
			querysValue     : arrayValue,
			functionSuccess : '_sendConnectSuccess',
			functionFail    : '_sendConnectFail',
			eleLoadStatus   : this.insDetail.getEleLoading()
		});

	},

	/**
	 *
	*/
	_flagIgnore:0,
	_sendConnectSuccess : function(obj)
	{
		this.insDetail.showBtnBottom();
		if (obj.response.responseText.isJSON()) {
			var json = obj.response.responseText.evalJSON();
			if (json.flag == 'end') {
				this._iniDetailEnd();

			} else if (json.flag == 'db') {
				this._iniDetailDb();

			} else if (json.flag == 'rebuild') {
				this._iniDetailRebuild();

			} else if (json.flag == 'php') {
				this._iniDetailPhp();

			} else if (json.flag == 'class') {
				this._iniDetailClass({arr: json.data});

			} else if (json.flag == 'func') {
				this._iniDetailFunc({arr: json.data});
				this._flagIgnore = 1;
			}
		}
		else alert(this.vars.varsSystem.str.errorServer);
	},

	/**
	 *
	*/
	_sendConnectFail : function(obj)
	{
		alert(this.vars.varsSystem.str.errorConnect);
	},

	/**
	 *
	*/
	_iniDetailEnd : function()
	{
		this.insDetail.eventList({
			strTitle : this.vars.portal.varsDetail.varsDetail.end.strTitle,
			strClass : null,
			vars : {
				varsDetail : this.vars.portal.varsDetail.varsDetail.end.varsDetail,
				varsBtn    : this.vars.portal.varsDetail.varsDetail.end.varsBtn,
				varsEdit   : {}
			}
		});
	},

	/**
	 *
	*/
	_iniDetailRebuild : function()
	{
		this.insDetail.eventList({
			strTitle : this.vars.portal.varsDetail.varsDetail.rebuild.strTitle,
			strClass : null,
			vars : {
				varsDetail : this.vars.portal.varsDetail.varsDetail.rebuild.varsDetail,
				varsBtn    : this.vars.portal.varsDetail.varsDetail.rebuild.varsBtn,
				varsEdit   : {}
			}
		});
	},

	/**
	 *
	*/
	_iniDetailDb : function()
	{
		this.insDetail.showFormAttestError({flagType : 'common'});
	},

	/**
	 *
	*/
	_iniDetailPhp : function()
	{
		this.insDetail.eventList({
			strTitle : this.vars.portal.varsDetail.varsDetail.php.strTitle,
			strClass : null,
			vars : {
				varsDetail : this.vars.portal.varsDetail.varsDetail.php.varsDetail,
				varsBtn    : [],
				varsEdit   : {}
			}
		});
	},

	/**
	 *
	*/
	_iniDetailFunc : function(obj)
	{
		var str = '';
		for (var i = 0; i < obj.arr.length; i++) {
			str += '<p class="codeLibBaseFontRed" style="float:none;">* ' + obj.arr[i] + '</p>'
		}
		var strNew = this.vars.portal.varsDetail.varsDetail['func'].varsDetail[0].strComment;
		this.vars.portal.varsDetail.varsDetail['func'].varsDetail[0].strComment = strNew.replace('<%replace%>', str);
		this.insDetail.eventList({
			strTitle : this.vars.portal.varsDetail.varsDetail['func'].strTitle,
			strClass : null,
			vars : {
				varsDetail : this.vars.portal.varsDetail.varsDetail['func'].varsDetail,
				varsBtn    : this.vars.portal.varsDetail.varsDetail['func'].varsBtn,
				varsEdit   : {}
			}
		});
	},

	/**
	 *
	*/
	_iniDetailClass : function(obj)
	{
		var str = '';
		for (var i = 0; i < obj.arr.length; i++) {
			str += '<p class="codeLibBaseFontRed" style="float:none;">* ' + obj.arr[i] + '</p>'
		}
		var strNew = this.vars.portal.varsDetail.varsDetail['class'].varsDetail[0].strComment;
		this.vars.portal.varsDetail.varsDetail['class'].varsDetail[0].strComment = strNew.replace('<%replace%>', str);
		this.insDetail.eventList({
			strTitle : this.vars.portal.varsDetail.varsDetail['class'].strTitle,
			strClass : null,
			vars : {
				varsDetail : this.vars.portal.varsDetail.varsDetail['class'].varsDetail,
				varsBtn    : [],
				varsEdit   : {}
			}
		});
	}
});
{/literal}

