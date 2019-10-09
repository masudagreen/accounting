{literal}
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_PopupTimer = Class.create(Code_Lib_ExtLib, {

	/**
	 *
	*/
	initialize : function(obj)
	{
		this._extAllot(obj);
		this._iniVars(obj);
		this._iniTimer();
	},

	/**
	 *
	*/
	_iniVars : function(obj)
	{
		this._extVars(obj);
	},

	/**
	 *
	*/
	_iniTimer : function()
	{
		this._setTimer();
	},

	/**
	 *
	*/
	_varsTimer : null,
	_setTimer : function()
	{
		var set = this.insRoot.vars.varsSystem.status.numAutoPopup * 1000;
		this._varsTimer = {
			interval : setInterval(this._runTimer.bind(this), set),
			stamp    : (new Date()).getTime(),
			flagUse  : 1,
			loop     : 1
		};
	},

	/**
	 *
	*/
	_runTimer : function()
	{
		if(this.insRoot.vars.varsSystem.status.numAutoPopup == 0 || !this._varsTimer.flagUse) return;
		var cut = this.insRoot.vars.varsSystem.status;
		var num = cut.numAutoPopup * 60 * 1000 * this._varsTimer.loop;
		var run = (new Date()).getTime() - this._varsTimer.stamp;
		if(run > num) {
			this._varsTimer.loop++;
			this._sendTimer();
		}
	},

	/**
	 *
	*/
	_sendTimer : function()
	{
		var arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db'];
		var arrayValue = ['Core', 'Base', 'Popup', '', 'Send', 'slave'];
		this.insRoot.insRequest.set({
			flagLock        : 1,
			idInsert        : this.insRoot.vars.varsSystem.id.root,
			numZiIndex      : this.insRoot.vars.varsSystem.num.zIndex,
			insCurrent      : this.insSelf,
			flagEscape      : 1,
			path            : this.insRoot.vars.varsSystem.path.post,
			querysKey       : arrayKey,
			querysValue     : arrayValue,
			functionSuccess : '_sendTimerSuccess',
			functionFail    : '_sendTimerFail'
		});
	},

	/**
	 *
	*/
	_sendTimerSuccess : function(obj)
	{
		if (obj.response.responseText.isJSON()) {
			var json = obj.response.responseText.evalJSON();
			var numNews = parseFloat(json.numNews);
			if (json.flag == 1 && numNews) {
				this.insRoot.iniPopup({flag : 'news', numNews : numNews});
			}

		} else {
			this._varsTimer.flagUse = 0;
			alert(this.insRoot.vars.varsSystem.str.errorRequest);
		}
	},

	/**
	 *
	*/
	_sendTimerFail : function(obj)
	{
		this._varsTimer.flagUse = 0;
		alert(this.insRoot.vars.varsSystem.str.errorRequest);
	}
});
{/literal}