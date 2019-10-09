{literal}
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_FileAccountEntitySearch = Class.create(Code_Lib_ExtPortal,
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
	_eventNaviConnect : function(obj)
	{
		if (obj.flag == 'search') {
			this._eventSearch({
				numLotNow : 0,
				ph : {
					arrWhere : obj.vars.arrWhere,
					arrOrder : obj.vars.arrOrder
				}
			});
			this.insCurrent.eventChildSearchConnect({
				varsSearch  : this._varsSearch,
				strBackFunc : 'eventParentConnectSuccess',
				insBack     : this,
				flag        : obj.flag.capitalize()
			});
			return;

		} else if (obj.flag.match(/^folder(.*?)-search$/)) {
			this._eventSearch({
				numLotNow : 0,
				ph : {
					arrWhere : obj.vars.arrWhere,
					arrOrder : obj.vars.arrOrder
				}
			});
		}
		this._varsNaviConnect = obj;
		this._sendNaviConnect();
	},

	eventParentConnectSuccess : function()
	{
		this.insNavi.showBtn();
	},

	/**
	 *
	*/
	_eventLayoutDetailContent : function()
	{

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