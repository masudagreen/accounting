{literal}
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Core_Base_AccountChoice = Class.create(Code_Lib_ExtChoice,
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
	iniReload : function(obj)
	{
		this._extReload(obj);
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
		this.strFunc = obj.strFunc;
		this.flagId = (obj.flagId)? obj.flagId : '';
		this.flagCheckUse = obj.flagCheckUse;
		this._setVarsFlagCheckUse();
	},

	/**
	 *
	*/
	_setVarsFlagCheckUse : function()
	{
		if (this.flagCheckUse) {
			this.vars.portal.varsList.tree.varsDetail.varsStatus.flagCheckUse = 1;
			this.vars.portal.varsList.tree.varsDetail.varsStatus.flagBtnUse = 0;
			if (this.insList) {
				this.insList.vars.tree.varsDetail.varsStatus.flagCheckUse = 1;
				this.insList.vars.tree.varsDetail.varsStatus.flagBtnUse = 0;
				this.insList.insTree.vars.varsStatus.flagCheckUse = 1;
				this.insList.insTree.vars.varsStatus.flagBtnUse = 0;
			}

		} else {
			this.vars.portal.varsList.tree.varsDetail.varsStatus.flagCheckUse = 0;
			this.vars.portal.varsList.tree.varsDetail.varsStatus.flagBtnUse = 1;
			if (this.insList) {
				this.insList.vars.tree.varsDetail.varsStatus.flagCheckUse = 0;
				this.insList.vars.tree.varsDetail.varsStatus.flagBtnUse = 1;
				this.insList.insTree.vars.varsStatus.flagCheckUse = 0;
				this.insList.insTree.vars.varsStatus.flagBtnUse = 1;
			}
		}
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
	_getLayoutAllot : function()
	{
		var allot =  function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == 'eventLayout') {
				insCurrent.insNavi.eventLayout();
				insCurrent.insList.eventLayout();
			} else if (obj.from == 'list-_mousedownNavi') {
				if (obj.vars.id == 'Reload') {
					insCurrent._eventListConnect({flag : obj.vars.id});
				}
			} else if (obj.from == 'navi-_mousedownNavi') {
				if (obj.vars.id == 'Switch') {
					insCurrent.insNavi.eventTool({
						idTarget : insCurrent.insNavi.vars.varsStatus.flagNow,
						flagLoop : 1
					});

				} else if (obj.vars.id == 'Reload') {
					insCurrent._eventNaviConnect({vars : obj.vars, flag : insCurrent.insNavi.vars.varsStatus.flagNow + '-reload'});
				}
			} else if (obj.from == 'navi-_mousedownMenu') {
				return insCurrent.insNavi.vars.varsStatus.flagNow;

			} else if (obj.from == 'navi-_mousedownLine') {
				insCurrent.insNavi.eventTool({idTarget : obj.vars});

			}
		};

		return allot;
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
	_getNaviAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			var array = obj.from.split('-');
			var flagNow = array[0];
			var flagType = array[1];
			if (obj.from == 'search-eventBtn') insCurrent._eventNaviConnect({vars : obj.vars, flag : 'search'});
			else if (obj.from == 'search-eventBtnSave') insCurrent._eventNaviConnect({vars : obj.vars, flag : 'search-save'});
			else if (obj.from == 'search-eventBtnDelete') insCurrent._eventNaviConnect({vars : obj.vars, flag : 'search-delete'});
			else if (flagNow.match(/^folder/)) {
				if (flagType == '_mousedownBtn') insCurrent._eventNaviConnect({vars : obj.vars, flag : flagNow + '-search'});
				else if (flagType == 'eventBtnBottom') insCurrent._eventNaviConnect({vars : obj.vars, flag : flagNow + '-save'});

			}
		};

		return allot;
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
	_getListAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			var array = obj.from.split('-');
			if (array[0] == 'tree') {
				if (array[1] == '_mousedownMove') insCurrent.insNavi.eventMove({vars : obj.vars});
				else if (array[1] == 'eventPage') insCurrent._eventListConnect({vars : obj.vars, flag : 'Search'});
				else if (array[1] == '_dblclickBtn') insCurrent._getListVars();
				else if (array[1] == 'eventBtnBottom') insCurrent._getListVars();
			}
		};

		return allot;
	},

	/**
	 *
	*/
	_getListVars : function(obj)
	{
		this.insWindow.hideLockWindow();
		var vars;
		if (this.flagCheckUse) {
			this.insList.insTree.setCheckAllValue();
			vars = this._getListVarsChild({
				arr : this.insList.insTree.vars.varsDetail
			});
		} else {
			vars = this.insList.insTree.getBtnSelect();
		}
		this.insReturn[this.strFunc]({vars : vars, flagId : this.flagId});
	},

	/**
	 *
	*/
	_getListVarsChild : function(obj)
	{

		var array = [];
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagUse) continue;
			if (obj.arr[i].flagCheckUse) {
				if (obj.arr[i].flagCheckNow) {
					array.push(obj.arr[i]);
				}
			}
		}

		return array;
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