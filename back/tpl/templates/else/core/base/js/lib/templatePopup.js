{literal}
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_TemplatePopup = Class.create({

	/**
	 *
	*/
	initialize : function(obj)
	{
		this._iniAllot(obj);
		this.iniVars(obj);
		this.iniLock();
		this.iniWrap();
		this.iniTemplate();
		this.iniUnder();
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
	updateStyle : function(obj)
	{
		this.updateStyle();
	},

	/**
	 *
	*/
	insUnder : null,
	iniUnder : function()
	{
		this.setUnder({
			vars : this.vars.varsFormat
		});
	},

	/**
	 *
	*/
	setUnder : function(obj)
	{
		this.insUnder = new Code_Lib_TemplateUnder({
			eleInsert  : this.eleTemplate,
			insRoot    : this.insRoot,
			insCurrent : this.insSelf,
			idSelf     : this.idSelf + 'Under',
			allot      : this.getUnderAllot(),
			vars   : {
				flagBeforeBox  : 0,
				varsFormat : obj.vars
			}
		});
	},

	/**
	 *
	*/
	getUnderAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == 'eventLayout') {
				insCurrent.insUnder.updateStyle();
			}
		};

		return allot;
	},

	/**
	 *
	*/
	iniLock : function()
	{
		if (!this.vars.varsStatus.flagLockUse) return;
		this.templateLock();
	},

	/**
	 *
	*/
	insLock : null,
	templateLock : function()
	{
		this.insLock = new Code_Lib_LockTemp({
			idSelf     : this.idSelf + 'Lock',
			numZIndex  : this.insRoot.setZIndex(),
			idInsert   : this.insRoot.vars.varsSystem.id.root,
			insCurrent : this.insSelf,
			strFunc    : 'removeWrap'
		});
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
		this.eleWrap.setStyle({
			'zIndex' : this.insRoot.setZIndex()
		});
	},

	/**
	 *
	*/
	removeWrap : function()
	{
		if (this.insLock) this.insLock.insListener.stop();
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
	staticTemplate : {numMenu : 6},
	setTemplate : function()
	{
		var insTemplate = new Code_Lib_Template();
		var dataSha = insTemplate.get({
			flagType  : 'menuBox',
			numWidth  : this.vars.varsMenu.numWidth,
			numHeight : this.vars.varsMenu.numHeight,
			id        : ''
		});
		this.eleWrap.insert(dataSha);
		this.eleTemplate = this.eleWrap.down('.codeLibTemplateMenuBoxMiddleMiddle', 0);
	},

	/**
	 *
	*/
	allot : {},
	_iniAllot : function(obj)
	{
		this.allot = obj.allot;
	}
});
{/literal}