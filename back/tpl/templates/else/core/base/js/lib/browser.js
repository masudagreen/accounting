{literal}
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Browser = Class.create(Code_Lib_Ext, {

{/literal}
	varsLoad : {$varsLoad},
{literal}

	/**
	 *
	*/
	iniLoad : function(obj)
	{
		this._iniVars(obj);
		if (this._checkBrowser()) return 1;
		return 0;
	},

	/**
	 *
	*/
	insRoot : null, idSelf : null,
	_iniVars : function(obj)
	{
		this.insRoot = obj.insRoot;
		this.idSelf = obj.idSelf;
	},

	/**
	 *
	*/
	_checkBrowser : function()
	{
		var userAgent = window.navigator.userAgent.toLowerCase();
		var appVersion = window.navigator.appVersion.toLowerCase();
		var ieModern = (typeof document.documentElement.style.msInterpolationMode != "undefined")? 1 : 0;

		if (userAgent.indexOf("opera") > -1) {
			alert(this.varsLoad.strNone);
			return 1;

		} else if (userAgent.indexOf("msie") > -1) {
			if (appVersion.indexOf("msie 7.0") > -1 || !ieModern) {
				alert(this.varsLoad.strNone);
				return 1;
			}
		}
	}

});
{/literal}