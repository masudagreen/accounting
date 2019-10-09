{literal}
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Ext = Class.create({

	/**
	 * Request
	*/
	insRequest  : null,
	_extRequest : function ()
	{
		this.insRequest = new Code_Lib_Request({insRoot: this.insRoot});
	},

	/**
	 * obj = {
	 * 	id       : string,
	 * 	pathSelf : string,
	 * }
	*/
	insCake : null,
	_extCake : function(obj)
	{
		this.insCake = new Code_Lib_Cake({
			insRoot  : this.insSelf,
			idSelf   : (obj.id)? 'Cake' + obj.id : 'Cake',
			pathSelf : obj.pathSelf
		});
	},

	/**
	 * Browser
	*/
	insBrowser : null,
	_extBrowser : function () {
		this.insBrowser = new Code_Lib_Browser();
		var flag = this.insBrowser.iniLoad({
			insRoot  : this.insSelf,
			idSelf   : 'Browser'
		});

		return flag;
	},

	/**
	 *
	*/
	insSelf : null, insRoot : null,
	_extVars : function ()
	{
		this.insSelf = this;
		this.insRoot = this;
	},

	/**
	 * Listener
	*/
	insListener : null,
	_extListener : function()
	{
		this.insListener = new Code_Lib_Listener();
	},

	/**
	 * Key
	*/
	_extKey : function()
	{
		this._setKeyLisener();
	},

	/**
	 *
	*/
	_setKeyLisener : function()
	{
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'keydown',
			strFunc : '_keydownKey', ele : document, vars : ''
		});
	},

	/**
	 *
	*/
	_keydownKey : function(evt)
	{
		this._resetLogout();
		if (evt.keyCode == 13) {
			if (!evt.findElement('textarea')) {
				evt.stop();
			}
		}
	},

	/**
	 *
	*/
	setZIndex : function()
	{
		this._resetLogout();
		this.vars.varsSystem.num.zIndex++;

		return this.vars.varsSystem.num.zIndex;
	},

	/**
	 *
	*/
	getZIndex : function()
	{
		return this.vars.varsSystem.num.zIndex;
	},

	/**
	 *
	*/
	_varsLogout : null,
	_resetLogout : function()
	{

	},

	/**
	 * obj = {
	 * 	numTimeZone : int,
	 * }
	*/
	_extTimeZone : function(obj)
	{
		this.insTimeZone = new Code_Lib_TimeZone({
			numTimeZone : obj.numTimeZone
		});
	},

	/**
	 * obj = {
	 * 	eleInsert : ele,
	 * 	vars  : object,
	 * }
	*/
	insWindow  : null,
	_extWindow : function (obj)
	{
		this.insWindow = new Code_Lib_Window();
		this.insWindow.iniLoad({
			eleInsert  : obj.eleInsert,
			insRoot    : this.insRoot,
			insCurrent : this.insSelf,
			idSelf     : 'Window',
			allot      : this._getWindowAllot(),
			vars       : obj.vars
		});

	},

	/**
	 * obj = {
	 * 	vars  : obj,
	 * }
	*/
	insLayout : null,
	_extLayout : function(obj)
	{
		this.insLayout = new Code_Lib_TemplateLayout({
			eleInsert  : $(this.insWindow.idWindow).down('.codeLibWindowBodyTopMiddleWrap', 0),
			idWindow   : this.insWindow.idWindow,
			insRoot    : this.insRoot,
			insCurrent : this.insSelf,
			idSelf     : 'Layout',
			allot      : function(){},
			vars       : obj.vars
		});
	},

	/**
	 *
	*/
	_varsValue : null,
	_eventValue : function(obj)
	{
		this._varsValue = {
			vars     : (obj.vars)? obj.vars : '',
			idTarget : (obj.idTarget)? obj.idTarget : ''
		};
	}

});
{/literal}
