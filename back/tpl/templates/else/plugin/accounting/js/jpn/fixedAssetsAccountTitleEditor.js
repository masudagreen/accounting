{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_FixedAssetsAccountTitleEditor = Class.create(Code_Lib_ExtEditor,
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
	_iniDetailFormSelect : function()
	{

	},


	/**
	 *
	*/
	_eventRemoveDetailFormSelect : function()
	{

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
	_preEventLayoutDetailContent : function()
	{

	},

	/**
	 *
	*/
	_eventRemoveDetailContent : function()
	{
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
			this._setDetailContentValue({arr : this.insDetail.insForm.vars.varsDetail});
			if (this.insDetail.checkForm({flagType : 'common'})) return;

			var vars = this.insDetail.getFormValue();

			var arr = ['NumRatioSellingAdminCost', 'NumRatioProductsCost', 'NumRatioNonOperatingExpenses', 'NumRatioAgricultureCost'];
			for (var i = 0; i < arr.length; i++) {
				var str = arr[i];
				if (vars[str]) {
					if (!vars[str].match(/^[0-9]{1,3}\.[0-9]{2,2}$/)) {
						this.insDetail.showFormAttestError({flagType : 'strFormat' + str});
						return;
					}
				}
			}
			if (parseFloat(vars.NumSurvivalRateLimit) > parseFloat(vars.NumSurvivalRate)) {
				this.insDetail.showFormAttestError({flagType : 'NumSurvivalRateLimit'});
				return;
			}

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
	_eventDetailConnectSuccess : function(obj)
	{
		if (obj.json.flag == 1) {
			if (this._varsDetailConnect.flag == 'edit') {
				this.insCurrent.eventDetailResetList(obj);
				if (this.insCurrent.insDetail.varsEventList.vars.vars.idTarget == this._varsValue.idTarget) {
					this._setDetailEventTree({
						arr      : obj.json.data.varsDetail,
						idTarget : this._varsValue.idTarget
					});
				}
				this._setDetailEnd();
			}

		} else if (obj.json.flag == 40) {
			alert(this.insRoot.vars.varsSystem.str.oldData);
			if (!this.insWindow.vars.flagHideNow) this.insWindow.updateHide({ flagEffect : 1 });

		} else {
			this.insDetail.showFormAttestError({flagType : obj.json.flag});
		}
	},

	/**
	 *
	*/
	_setDetailEventTree : function(obj)
	{
		this._varsVarsListTreeBlock = {};
		this._getVarsListTreeBlock({
			arr      : obj.arr,
			idTarget : obj.idTarget
		});
		if (this._varsVarsListTreeBlock) {
			this.insCurrent._eventDetailList({vars : this._varsVarsListTreeBlock});
		}
	},

	/**
	 * Block
	*/
	_varsVarsListTreeBlock : {},
	_getVarsListTreeBlock : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].vars.idTarget == obj.idTarget) {
				this._varsVarsListTreeBlock = (Object.toJSON(obj.arr[i])).evalJSON();

			} else {
				this._getVarsListTreeBlock({arr : obj.arr[i].child, idTarget : obj.idTarget});
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