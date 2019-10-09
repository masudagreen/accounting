{literal}
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_TemplateFormat = Class.create({

	/**
	 *
	*/
	initialize : function(obj)
	{
		this.iniVars(obj);
		this.iniWrap();
		this.iniTemplate();
	},

	/**
	 *
	*/
	insRoot : null, insCurrent : null, insSelf : null, idSelf : null, vars : null,
	iniVars : function(obj)
	{
		this.eleInsert = obj.eleInsert;
		this.insRoot = obj.insRoot;
		this.insCurrent = obj.insCurrent;
		this.insSelf = this;
		this.idSelf = obj.idSelf;
		this.vars = obj.vars;
	},

	/**
	 *
	*/
	eleWrap : null,
	iniWrap : function()
	{
		var ele = $(document.createElement('div'));
		ele.id = this.idSelf;
		this.eleInsert.insert(ele);
		this.eleWrap = ele;
	},

	/**
	 *
	*/
	removeWrap : function()
	{
		this.eleWrap.remove();
	},

	/**
	 *
	*/
	eleTemplate : null,
	iniTemplate : function()
	{
		this.setTemplate();
	},

	/**
	 *
	*/
	setTemplate : function()
	{
		var cut = this.vars;
		cut.id = this.idSelf + 'Template';
		cut.numWidth = this.getTemplateWidth();
		cut.numHeight = this.getTemplateHeight();
		var insTemplate = new Code_Lib_Template();
		var data = insTemplate.get(cut);
		this.eleWrap.insert(data);
		this.eleTemplate = {};
		this.eleTemplate.header = this.eleWrap.down('.codeLibTemplateListFormatHeader', 0);
		this.eleTemplate.body = this.eleWrap.down('.codeLibTemplateListFormatBody', 0);
		this.eleTemplate.fooder = (this.eleWrap.down('.codeLibTemplateListFormatFooder', 0))?
								  this.eleWrap.down('.codeLibTemplateListFormatFooder',0)
								: null;
	},

	/**
	 *
	*/
	getTemplateWidth : function()
	{
		var array = this.eleInsert.style.width.split('px');
		var data = parseFloat(array[0]);

		return data;
	},

	/**
	 *
	*/
	getTemplateHeight : function()
	{
		array = this.eleInsert.style.height.split('px');
		var data = parseFloat(array[0]);

		return data;
	},

	/**
	 *
	*/
	updateTemplateStyle : function()
	{
		var cut = this.vars;
		cut.id = this.idSelf + 'Template';
		cut.numWidth = this.getTemplateWidth();
		cut.numHeight = this.getTemplateHeight();
		var insTemplate = new Code_Lib_Template();
		insTemplate.updateStyle(cut);
	}
});
{/literal}
