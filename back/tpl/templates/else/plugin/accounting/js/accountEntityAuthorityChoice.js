{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_AccountEntityAuthorityChoice = Class.create(Code_Lib_ExtChoice,
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
	_iniCss : function()
	{
		this._extCss();
	}
});
{/literal}
