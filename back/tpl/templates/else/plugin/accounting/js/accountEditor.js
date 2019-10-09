{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_AccountEditor = Class.create(Code_Lib_ExtEditor,
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
		this._iniDetail();
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
		this.vars.portal.varsDetail.varsDetail = this.varsChild.varsDetail;
		this._varsSearch = this.insCurrent.getVarsSearch();

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
	_iniDetail : function()
	{
		this._extDetail();
	},


	/**
	 *
	*/
	_setDetailEnd : function()
	{
		this._varsDetailEnd = (Object.toJSON(this.insDetail.varsEventList)).evalJSON();
		this._getDetailFormContentVars({arr : this.insDetail.insForm.vars.varsDetail});
		this._varsDetailEnd.varsDetail = (Object.toJSON(this.insDetail.insForm.vars.varsDetail)).evalJSON();
		var objData = {
			strTitle : null,
			strClass : null,
			vars     : {
				varsDetail : this.vars.portal.varsDetail.varsEnd.varsDetail,
				varsBtn    : this.vars.portal.varsDetail.varsEnd.varsBtn,
				varsEdit   : {},
				vars       : {}
			}
		};
		this.insDetail.eventList(objData);
	},

	/**
	 *
	*/
	_setDetailStart : function()
	{
		var str = 'strTitle' + this.varsChild.flagType.capitalize();

		this.insDetail.eventList({
			flagMoveUse : 1,
			strTitle    : this.vars.portal.varsDetail.varsStart[str],
			strClass    : null,
			vars        : {
				varsDetail : this.vars.portal.varsDetail.varsDetail,
				varsBtn    : this.vars.portal.varsDetail.varsBtn,
				varsEdit   : this.vars.portal.varsDetail.form.varsEdit,
				vars       : {}
			}
		});

		this._setDetailContent();
	},


	/**
	 *
	*/
	_getDetailFormContentVars : function(obj)
	{
		this._getDetailFormAreaVars(obj);
	},


	/**
	 *
	*/
	_setDetailContent : function()
	{
		this._iniDetailFormArea();
	},

	/**
	 *
	*/
	_iniDetailFormArea : function()
	{
		this._extDetailFormArea();
	},


	/**
	 *
	*/
	_eventLayoutDetailContent : function()
	{
		if (!this.insDetail.insForm) return;
		this._iniDetailFormArea();

	},

	/**
	 *
	*/
	_preEventLayoutDetailContent : function()
	{
		if (!this.insDetail.insForm) return;
		this._getDetailFormAreaVars({arr : this.insDetail.insForm.vars.varsDetail});

	},

	/**
	 *
	*/
	_eventRemoveDetailContent : function()
	{
		if (!this.insDetail.insForm) return;
		this._eventRemoveDetailFormArea({arr : this.insDetail.insForm.vars.varsDetail});

	},

	/**
	 *
	*/
	_setDetailContentValue : function()
	{
		this._setDetailFormAreaValue({arr : this.insDetail.insForm.vars.varsDetail});
	},

	/**
	 *
	*/
	_getDetailContentVarsDetail : function()
	{
		return this._getDetailFormAreaVarsDetail({arr : this.insDetail.insForm.vars.varsDetail});
	},

	/**
	 *
	*/
	_getDetailFormAreaVarsDetail : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].varsFormArea) return obj.arr[i].varsFormArea.varsDetail;
		}
	},


	/**
	 *
	*/
	_getDetailFormAreaAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			var insParent = insCurrent.insCurrent;

			if (obj.from == '_mousedownBarLink') {
				obj.arr = insParent.vars.portal.varsDetail.templateDetail;

				for (var i = 0; i < obj.arr.length; i++) {
					if (!obj.arr[i].varsFormArea) continue;
					insParent.insRoot.insChoice.setBoot({
						flagId       : obj.arr[i].id,
						idTarget     : obj.arr[i].varsFormArea.varsChoice.idTarget,
						idModule     : obj.arr[i].varsFormArea.varsChoice.idModule,
						flagCheckUse : obj.arr[i].varsFormArea.varsChoice.flagCheckUse,
						strFunc      : 'setDetailFormAreaChoiceValue',
						numTop       : insParent._staticDetailFormArea.numTop + $(insParent.insWindow.idWindow).offsetTop,
						numLeft      : insParent._staticDetailFormArea.numLeft + $(insParent.insWindow.idWindow).offsetLeft,
						insCurrent   : insCurrent
					});
					break;
				}

				return 1;
			}
		};

		return allot;
	},

	/**
	 *
	*/
	_eventDetailConnect : function(obj)
	{
		if (obj.flag == 'reload') {
			if (obj.flagType == 'start') {
				if (this.varsChild.varsIni) {
					this.vars.portal.varsDetail.varsDetail = this.varsChild.varsIni;
				} else {
					this.vars.portal.varsDetail.varsDetail = this.varsChild.varsDetail;
				}

			} else {
				this.vars.portal.varsDetail.varsDetail = this.varsChild.varsDetail;
			}
			this._setDetailStart();
			return;

		} else if (obj.flag == 'edit') {
			this._setDetailFormAreaValue({arr : this.insDetail.insForm.vars.varsDetail});
			if (this.insDetail.checkForm({flagType : 'common'})) return;
			var vars = this.insDetail.getFormValue();
			this._eventValue({
				vars     : vars,
				idTarget : this.varsChild.idTarget
			});
		}

		this._varsDetailConnect = obj;
		this._sendDetailConnect();
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