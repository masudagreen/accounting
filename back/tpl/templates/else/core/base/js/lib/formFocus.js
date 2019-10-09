{literal}
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_FormFocus = Class.create(Code_Lib_ExtLib,
{

	/**
	 *
	*/
	initialize : function(obj)
	{
		this._extAllot(obj);
		this._iniVars(obj);
		this._iniListener();
		this._iniTemplate();
	},

	/**
	 *
	*/
	_iniVars : function(obj)
	{
		this._extVars(obj);
	},

	/**
	 * Template
	*/
	_iniTemplate : function()
	{
		this._setTemplateListener();
	},

	/**
	 *
	*/
	_setTemplateListener : function()
	{
		this.insListener.set({ flagType15 : 1, bindAsEvent : 1, insCurrent : this, event : 'focus',
		strFunc : '_focusCheck',	ele : this.eleInsert, vars : '' });
		this.insListener.set({ flagType15 : 1, bindAsEvent : 1, insCurrent : this, event : 'blur',
		strFunc : '_blurCheck', ele : this.eleInsert, vars : '' });
	},

	/**
	 *
	*/
	removeWrap : function()
	{
		this.stopListener();
	},

	/**
	 *
	*/
	_focusCheck : function(obj, evt)
	{
		if (obj) evt.stop();
		else obj = evt;
		this.allot({
			from       : '_focusCheck',
			insCurrent : this.insCurrent
		});
	},

	/**
	 *
	*/
	_blurCheck : function(obj, evt)
	{
		if (obj) evt.stop();
		else obj = evt;
		var value = this.allot({
			from       : '_blurCheck',
			insCurrent : this.insCurrent,
			vars       : this.eleInsert.value
		});
		this.eleInsert.value = value;
	},

	/**
	 *
	*/
	_iniListener : function()
	{
		this._extListener();
	}
});
{/literal}
