{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_SummaryStatementPublicAccountTitleEditor = Class.create(Code_Lib_ExtEditor,
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
		this._varsValue = this.insCurrent._varsValue;
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
	},


	/**
	 *
	*/
	_setDetailContent : function(obj)
	{
	},

	/**
	 *
	*/
	_eventLayoutDetailContent : function()
	{
		if (!this.insDetail.insForm) return;

	},

	/**
	 *
	*/
	_preEventLayoutDetailContent : function()
	{
		if (!this.insDetail.insForm) return;

	},

	/**
	 *
	*/
	_eventRemoveDetailContent : function()
	{
		if (!this.insDetail.insForm) return;
	},

	/**
	 *
	*/
	_setDetailContentValue : function()
	{

	},

	/**
	 *
	*/
	_eventDetailConnect : function(obj)
	{
		if (obj.flag == 'reload') {
			this.vars.portal.varsDetail.varsDetail = this.varsChild.varsDetail;
			this._setDetailStart();
			return;

		} else if (obj.flag == 'edit' || obj.flag == 'add') {
			this._setDetailContentValue({arr : this.insDetail.insForm.vars.varsDetail});
			if (this.insDetail.checkForm({flagType : 'common'})) return;
			var vars = this.insDetail.getFormValue();

			this._eventValue({
				vars     : vars,
				idTarget : this.varsChild.idTarget
			});

		} else if (!obj.flag.match(/^format(.*?)-save$/)) {
			obj.arr = (Object.toJSON(this.varsChild.varsDetail)).evalJSON();
			for (var i = 0; i < obj.arr.length; i++) {
				if (!obj.vars[obj.arr[i].id] || obj.arr[i].flagDisabled) continue;
				obj.arr[i].value = obj.vars[obj.arr[i].id];
			}
			this.vars.portal.varsDetail.varsDetail = obj.arr;
			this._eventRemoveDetailContent();
			this._setDetailStart();
			return;
		}

		this._varsDetailConnect = obj;
		this._sendDetailConnect();
	},

	/**
	 *
	*/
	_eventDetailConnectSuccess : function(obj)
	{
		if (obj.json.flag == 1) {
			if (this._varsDetailConnect.flag == 'add') {
				this.insCurrent.eventDetailResetList(obj);
				this._setDetailEnd();

			} else if (this._varsDetailConnect.flag == 'edit') {
				this.insCurrent.eventDetailResetList(obj);
				this._setDetailEnd();
			}

		} else if (obj.json.flag == 40) {
			alert(this.insRoot.vars.varsSystem.str.oldData);
			if (!this.insWindow.vars.flagHideNow) this.insWindow.updateHide({ flagEffect : 1 });

		} else {
			if (obj.json) {
				if (obj.json.flag) {
					if (this.insRoot.vars.varsSystem.str[obj.json.flag]) {
						alert(this.insRoot.vars.varsSystem.str[obj.json.flag]);
					}
				}
			}
		}
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