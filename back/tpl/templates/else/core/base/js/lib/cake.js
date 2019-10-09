{literal}
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Cake = Class.create({
{/literal}
	vars : {$varsLoad},
{literal}

	/**
	 *
	*/
	initialize : function(obj)
	{
		this._iniVars(obj);
	},

	/**
	 *
	*/
	_pathSelf : null, insRoot : null, insSelf : null, idSelf : null,
	_iniVars : function(obj)
	{
		this.insRoot = obj.insRoot;
		this.insSelf = this;
		this.idSelf = obj.idSelf;
		this._pathSelf = obj.pathSelf;
	},

	/**
	 * obj = {
	 * 	key    : string
	 * 	value : mix
	 * 	numExpires : int
	 * }
	*/
	setStorageCake : function(obj)
	{
		var data = ({
			value      : obj.value,
			numExpires : obj.numExpires
		});
		var strJson = Object.toJSON(data);
		this._varsStorage.idReturn = (obj.idReturn)? obj.idReturn : null;
		var parentKey = obj.parentKey;
		if (this.insRoot.vars.varsSystem.status) {
			parentKey = this._getTitleKey({parentKey : obj.parentKey});
		}
		this._varsStorage.cakeParentKey = parentKey ;
		this._varsStorage.cakeValue = strJson;
		localStorage.setItem(this._varsStorage.cakeParentKey, this._varsStorage.cakeValue);
	},

	/**
	 *
	*/
	getStorageCake : function(obj)
	{
		this._varsStorage.funcReturn = obj.funcReturn;
		this._varsStorage.insReturn = obj.insReturn;
		var parentKey = obj.parentKey;
		if (this.insRoot.vars.varsSystem.status) {
			parentKey = this._getTitleKey({parentKey : obj.parentKey});
		}
		var data = localStorage.getItem(parentKey);
		this.checkStorageCake([data]);

	},

	/**
	 *
	*/
	checkStorageCake : function(obj)
	{
		if(obj[0] != null) {
			var data = this._getStorageCakeData({
				jsonData : obj[0],
				insTimeZone : this.insRoot.insTimeZone
			});
			this._varsStorage.funcReturn({
				data      : data,
				insReturn : this._varsStorage.insReturn
			});
		} else {
			this._varsStorage.funcReturn({
				data      : '',
				insReturn : this._varsStorage.insReturn
			});
		}
	},

	/**
	 *
	*/
	_varsStorage : {
		id : 'externalstorage',
		allowScriptAccess : 'sameDomain',
		numWidth : 1,
		numHeight : 1,
		cakeParentKey : null,
		cakeKey : null,
		idReturn : null,
		insLock : null,
		funcReturn : null ,
		insReturn : null,
		flagNumSort : 0
	},

	/**
	 * obj = {
	 * 	jsonData    : json
	 * 	insTimeZone : instance
	 * }
	*/
	_getStorageCakeData: function(obj)
	{
		var objData = (obj.jsonData).evalJSON();
		var stampLimit = parseFloat(objData.numExpires);
		if(stampLimit){
			var objTime = obj.insTimeZone.adjustTime({
				stamp : new Date().getTime()
			});
			if( stampLimit < objTime.stamp ) return ''
		}

		return objData.value;
	},

	/**
	 *
	*/
	removeStorageCake : function(obj)
	{
		var parentKey = obj.parentKey;
		if (this.insRoot.vars.varsSystem.status) {
			parentKey = this._getTitleKey({parentKey : obj.parentKey});
		}
		localStorage.setItem(parentKey, '');
	},

	_getTitleKey : function(obj)
	{
		var strUrl = document.URL;
		var arr = strUrl.split('/');
		var strKey = arr.join('') + '_' + this.insRoot.vars.varsSystem.status.idAccount + '_' + obj.parentKey;

		return strKey;
	},

	/**
	 *
	*/
	removeStorageAllCake : function()
	{
		localStorage.clear();
	}

});
{/literal}