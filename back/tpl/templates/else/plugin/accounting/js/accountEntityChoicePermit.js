{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_AccountEntityChoicePermit = Class.create(Code_Lib_ExtChoice,
{
{/literal}
	vars : {$varsLoad},
	numNews : {$numNews},
{literal}

	/**
	 *
	*/
	initialize : function()
	{
		this._iniCss();
	},

	/**
	 *
	*/
	iniLoad : function(obj)
	{
		this._iniVars(obj);
		this._iniPopup();
		this._iniLayout();
		this._iniNavi();
		this._iniList();
	},

	/**
	 *
	*/
	_varsCheck : null,
	iniReload : function(obj)
	{
		this.flagId = (obj.flagId)? obj.flagId : null;
		this.flagCheckUse = (obj.flagCheckUse)? obj.flagCheckUse : null;
		this.insReturn = (obj.insReturn)? obj.insReturn : null;
		this.strFunc = (obj.strFunc)? obj.strFunc : null;
		this.varsValue = (obj.varsValue)? obj.varsValue : null;
		this._setVarsFlagCheckUse();
		this.insList.insTree.iniReload();

		if (Object.toJSON(this._varsCheck) == Object.toJSON(this.varsValue)) {
			this.insList.insTree.iniReload();

		} else {
			this._varsCheck = this.varsValue;
			this._eventValue(this.varsValue);
			this._eventListConnect({flag : 'Refresh'});
		}
	},

	/**
	 *
	*/
	_iniPopup : function()
	{
		this._extPopup();
	},

	/**
	 *
	*/
	_iniVars : function(obj)
	{
		this._extVars(obj);
		this.insReturn = obj.insReturn;
		this.varsValue = (obj.varsValue)? obj.varsValue : null;
		this._varsCheck = this.varsValue;
		this.strFunc = obj.strFunc;
		this.flagId = (obj.flagId)? obj.flagId : '';
		this.flagCheckUse = obj.flagCheckUse;
		this._setVarsFlagCheckUse();
	},

	/**
	 *
	*/
	_iniLayout : function()
	{
		this._extLayout();
	},

	/**
	 *
	*/
	_iniNavi : function()
	{
		this._extNavi();
	},


	/**
	 *
	*/
	_iniList : function()
	{
		this._extList();
	},

	/**
	 *
	*/
	_eventListConnect : function(obj)
	{
		if (obj.flag == 'Reload') {
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

		} else if (obj.flag == 'Refresh') {
			obj.flag = 'Search';
			this._eventSearch({
				numLotNow : 0,
				ph : {
					arrWhere : [],
					arrOrder : {}
				}
			});
		}
		this._varsListConnect = obj;
		this._sendListConnect();
	},

	/**
	 *
	*/
	_iniCss : function()
	{
		this._extCss();
	}
});
{/literal}
