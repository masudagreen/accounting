{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_AccountTitleCSEditor = Class.create(Code_Lib_ExtEditor,
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
		this._getDetailFormAreaVars(obj);
	},


	/**
	 *
	*/
	_setDetailContent : function(obj)
	{

		this._iniDetailFormSelect();
	},

	/**
	 *
	*/
	_iniDetailFormSelect : function()
	{
		this._setDetailFormSelect({arr : this.insDetail.insForm.vars.varsDetail});
	},

	insFormSelectMinus : null,
	insFormSelectPlus : null,
	_setDetailFormSelect : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'IdAccountTitleMinus') {
				var ele = $(this.insDetail.insForm.idSelf + obj.arr[i].id);
				this.insFormSelectMinus = new Code_Lib_FormSelect({
					eleInsert  : ele,
					insRoot    : this.insRoot,
					insCurrent : this,
					idSelf     : this.idSelf + 'FormSelectMinus' + obj.arr[i].id,
					allot      : this._getDetailFormSelectMinusAllot()
				});

			} else if (obj.arr[i].id == 'IdAccountTitlePlus') {
				var ele = $(this.insDetail.insForm.idSelf + obj.arr[i].id);
				this.insFormSelectPlus = new Code_Lib_FormSelect({
					eleInsert  : ele,
					insRoot    : this.insRoot,
					insCurrent : this,
					idSelf     : this.idSelf + 'FormSelectPlus' + obj.arr[i].id,
					allot      : this._getDetailFormSelectPlusAllot()
				});
			}

		}
	},

	/**
	 *
	*/
	_getDetailFormSelectMinusAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			var flag = obj.vars;
			insCurrent._getDetailFormSelectMinusVars({
				arr  : insCurrent.insDetail.insForm.vars.varsDetail,
				flag : flag
			});
		};

		return allot;
	},

	/**
	 *
	*/
	_getDetailFormSelectMinusVars : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'IdAccountTitleMinus') {
				obj.arr[i].value = obj.flag;

			} else if (obj.arr[i].id == 'FlagMethodMinus') {
				obj.arr[i].flagHideNow = 0;
				if (obj.flag == 'none' || obj.flag == 'cash') {
					obj.arr[i].flagHideNow = 1;
				}
				this.vars.portal.varsDetail.varsDetail = obj.arr;
				this._eventRemoveDetailContent();
				this._setDetailStart();
				return;
			}
		}
	},

	/**
	 *
	*/
	_getDetailFormSelectPlusAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			var flag = obj.vars;
			insCurrent._getDetailFormSelectPlusVars({
				arr  : insCurrent.insDetail.insForm.vars.varsDetail,
				flag : flag
			});
		};

		return allot;
	},

	/**
	 *
	*/
	_getDetailFormSelectPlusVars : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'IdAccountTitlePlus') {
				obj.arr[i].value = obj.flag;

			} else if (obj.arr[i].id == 'FlagMethodPlus') {
				obj.arr[i].flagHideNow = 0;
				if (obj.flag == 'none' || obj.flag == 'cash') {
					obj.arr[i].flagHideNow = 1;
				}
				this.vars.portal.varsDetail.varsDetail = obj.arr;
				this._eventRemoveDetailContent();
				this._setDetailStart();
				return;
			}
		}
	},

	/**
	 *
	*/
	_eventRemoveDetailFormSelect : function()
	{
		this.insFormSelectMinus.stopListener();
		this.insFormSelectPlus.stopListener();

	},



	/**
	 *
	*/
	_eventLayoutDetailContent : function()
	{
		if (!this.insDetail.insForm) return;
		this._iniDetailFormSelect();
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
		this._eventRemoveDetailFormSelect();

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

		} else if (obj.flag == 'edit' || obj.flag == 'add') {
			this._setDetailContentValue({arr : this.insDetail.insForm.vars.varsDetail});
			if (this.insDetail.checkForm({flagType : 'common'})) return;

			var vars = this.insDetail.getFormValue();

			vars.FlagAccountTitle = this._varsValue.vars.FlagAccountTitle;
			vars.FlagDirect = this._varsValue.vars.FlagDirect;


			if (vars.IdAccountTitlePlus != 'cash' && vars.IdAccountTitleMinus == 'cash') {
				this.insDetail.showFormAttestError({flagType : 'cashPlus'});
				return;
			}

			if (vars.IdAccountTitlePlus == 'cash' && vars.IdAccountTitleMinus != 'cash') {
				this.insDetail.showFormAttestError({flagType : 'cashMinus'});
				return;
			}


			if (vars.IdAccountTitlePlus == 'none') {
				if (!(vars.IdAccountTitleMinus == 'none' || vars.IdAccountTitleMinus == 'cash')) {
					if (vars.FlagMethodMinus != 'net') {
						this.insDetail.showFormAttestError({flagType : 'netMinus'});
						return;
					}
				}
			}
			if (vars.IdAccountTitleMinus == 'none') {
				if (!(vars.IdAccountTitlePlus == 'none' || vars.IdAccountTitlePlus == 'cash')) {
					if (vars.FlagMethodPlus != 'net') {
						this.insDetail.showFormAttestError({flagType : 'netPlus'});
						return;
					}
				}
			}

			if (!(vars.IdAccountTitlePlus == 'none' || vars.IdAccountTitlePlus == 'cash')) {
				if (!(vars.IdAccountTitleMinus == 'none' || vars.IdAccountTitleMinus == 'cash')) {
					if (vars.FlagMethodPlus == 'sumDebit') {
						if (vars.FlagMethodMinus != 'sumCredit') {
							this.insDetail.showFormAttestError({flagType : 'sumCreditMinus'});
							return;
						}

					} else if (vars.FlagMethodPlus == 'sumCredit') {
						if (vars.FlagMethodMinus != 'sumDebit') {
							this.insDetail.showFormAttestError({flagType : 'sumDebitMinus'});
							return;
						}

					} else if (vars.FlagMethodPlus == 'net') {
						if (vars.FlagMethodMinus != 'net') {
							this.insDetail.showFormAttestError({flagType : 'sumNetMinus'});
							return;
						}
					}
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

		} else if (obj.json.flag == 40) {
			alert(this.insRoot.vars.varsSystem.str.oldData);
			if (!this.insWindow.vars.flagHideNow) this.insWindow.updateHide({ flagEffect : 1 });

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