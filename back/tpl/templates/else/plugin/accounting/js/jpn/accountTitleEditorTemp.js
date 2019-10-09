{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_AccountTitleEditorTemp = Class.create(Code_Lib_ExtEditor,
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
	_getDetailFormFormat : function()
	{

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
		this._getDetailFormAreaVars(obj);
	},


	/**
	 *
	*/
	_setDetailContent : function(obj)
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
	_getDetailFormAreaAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == '_mousedownBarAdd') {
				insCurrent._setDetailFormAreaChoice({
					idTarget     : obj.vars.insCurrent.vars.varsChoice.idTarget,
					idModule     : obj.vars.insCurrent.vars.varsChoice.idModule,
					flagCheckUse : obj.vars.insCurrent.vars.varsChoice.flagCheckUse,
					flagId       : obj.vars.insCurrent.vars.varsChoice.flagId,
					strFunc      : obj.vars.insCurrent.vars.varsChoice.strFunc
				});

			} else if (obj.from == 'eventMove') {
				var numTop = insCurrent.insDetail.insForm.eleInsert.scrollTop * (-1);
				obj.insSelf.modifyVarsMove({numTopMax : numTop, numTopMin : numTop});
			}
		};

		return allot;
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

		} else if (obj.flag == 'edit' || obj.flag == 'add') {
			this._setDetailContentValue({arr : this.insDetail.insForm.vars.varsDetail});
			if (this.insDetail.checkForm({flagType : 'common'})) return;

			var vars = this.insDetail.getFormValue();

			vars.FlagAccountTitle = this._varsValue.vars.FlagAccountTitle;
			if (obj.flag == 'edit') {
				if (!vars.IdAccountTitle) {
					vars.IdAccountTitle = this.varsChild.idTarget;
				}
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
			if (this._varsDetailConnect.flag == 'add') {
				this.insCurrent.eventDetailResetList(obj);
				this._setDetailEnd();

			} else if (this._varsDetailConnect.flag == 'edit') {
				this.insCurrent.eventDetailResetList(obj);
				this._setDetailEnd();
			}
			this.insCurrent.bootAutoSearchOver({flag : 'loopAddAccountTitle'});

		} else if (obj.json.flag == 40) {
			alert(this.insRoot.vars.varsSystem.str.oldData);
			this.insCurrent.bootAutoSearchOver({flag : 'hideLockWindow'});

		} else {
			if (obj.json) {
				if (obj.json.flag) {
					if (this.insRoot.vars.varsSystem.str[obj.json.flag]) {
						alert(this.insRoot.vars.varsSystem.str[obj.json.flag]);

					} else {
						this.insDetail.showFormAttestError({flagType : obj.json.flag});
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