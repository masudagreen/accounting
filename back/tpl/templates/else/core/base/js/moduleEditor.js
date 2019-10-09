{literal}
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Core_Base_ModuleEditor = Class.create(Code_Lib_ExtEditor,
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
	_getDetailFormContentVars : function(obj)
	{
		this._getDetailFormCheckVars(obj);
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
	_setDetailContent : function()
	{
		this._iniDetailFormCheck();
	},

	/**
	 *
	*/
	_setDetailContentValue : function()
	{
		this._setDetailFormCheckValue({arr : this.insDetail.insForm.vars.varsDetail});
	},


	/**
	 *
	*/
	_varsDetailFormCheck : [],
	_iniDetailFormCheck : function()
	{
		this._varsDetailFormCheck = [];
		this._setDetailFormCheck({arr : this.insDetail.insForm.vars.varsDetail});
	},

	/**
	 *
	*/
	_setDetailFormCheck : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormCheck) continue;
			var ele = this.insDetail.insForm.eleWrap.down('.codeLibFormContent', num);
			var insFormCheck = new Code_Lib_FormCheck({
				eleInsert  : ele,
				insRoot    : this.insRoot,
				insCurrent : this,
				idSelf     : this.idSelf + 'FormCheck' + obj.arr[i].id,
				allot      : this._getDetailFormCheckAllot(),
				vars       : obj.arr[i].varsFormCheck
			});

			this._varsDetailFormCheck.push({
				id           : obj.arr[i].id,
				insFormCheck : insFormCheck,
			});
			num++;
		}
	},


	/**
	 *
	*/
	_getDetailFormCheckVars : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormCheck) continue;
			obj.arr[i].varsFormCheck.varsDetail = this._varsDetailFormCheck[num].insFormCheck.vars.varsDetail;
			num++;
		}
	},

	/**
	 *
	*/
	_getDetailFormCheckAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == '_mousedownBtn') {
				if (obj.vars.vars.idModule == 'base' && obj.vars.varsColumn.id == 'FlagAdmin' && obj.vars.vars.varsColumnDetail.flagAdminNow == 1) {
					obj.arr = insCurrent.vars.varsDetail;
					for (var i = 0; i < obj.arr.length; i++) {
						if (obj.arr[i].idModule != 'base') {
							obj.arr[i].varsColumnDetail.flagAdminNow = 1;
							obj.arr[i].varsColumnDetail.flagUserNow = 1;
							obj.arr[i].varsColumnDetail.flagAdminLock = 1;
							obj.arr[i].varsColumnDetail.flagUserLock = 1;
						}

					}

				} else if (obj.vars.vars.idModule == 'base' && obj.vars.varsColumn.id == 'FlagAdmin' && obj.vars.vars.varsColumnDetail.flagAdminNow == 0) {
					obj.arr = insCurrent.vars.varsDetail;
					for (var i = 0; i < obj.arr.length; i++) {
						if (obj.arr[i].idModule != 'base') {
							obj.arr[i].varsColumnDetail.flagAdminLock = 0;
							obj.arr[i].varsColumnDetail.flagUserLock = 0;
						}

					}

				} else if (obj.vars.varsColumn.id == 'FlagAdmin' && obj.vars.vars.varsColumnDetail.flagAdminNow == 1){
					obj.vars.vars.varsColumnDetail.flagUserNow = 1;

				} else if (obj.vars.varsColumn.id == 'FlagUser' && obj.vars.vars.varsColumnDetail.flagUserNow == 0){
					obj.vars.vars.varsColumnDetail.flagAdminNow = 0;
				}
			}
		};

		return allot;
	},

	/**
	 *
	*/
	_eventRemoveDetailFormCheck : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormCheck) continue;
			this._varsDetailFormCheck[num].insFormCheck.stopListener();
			num++;
		}
	},

	/**
	 *
	*/
	_setDetailFormCheckValue : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormCheck) continue;
			this.insDetail.setFormValue({
				idTarget : obj.arr[i].id,
				value    : this._varsDetailFormCheck[num].insFormCheck.vars.varsDetail
			});
			num++;
		}
	},

	/**
	 *
	*/
	_eventLayoutDetailContent : function()
	{
		this._iniDetailFormCheck();
	},

	/**
	 *
	*/
	_preEventLayoutDetailContent : function()
	{
		if (!this.insDetail.insForm) return;
		this._getDetailFormCheckVars({arr : this.insDetail.insForm.vars.varsDetail});
	},

	/**
	 *
	*/
	_eventRemoveDetailContent : function()
	{
		if (!this.insDetail.insForm) return;
		this._eventRemoveDetailFormCheck({arr : this.insDetail.insForm.vars.varsDetail});
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

		} else if (obj.flag == 'add'
			|| obj.flag == 'edit'
		) {
			if (this.insDetail.checkForm({flagType : 'common'})) return;
			this._setDetailFormCheckValue({arr : this.insDetail.insForm.vars.varsDetail});
			var vars = this.insDetail.getFormValue();


			obj.arr = vars.JsonModule;
			var data = {};
			for (var i = 0; i < obj.arr.length; i++) {
				if (obj.arr[i].varsColumnDetail.flagAdminNow) data[obj.arr[i].idModule] = 'Admin';
				else if (obj.arr[i].varsColumnDetail.flagUserNow) data[obj.arr[i].idModule] = 'User';
				else data[obj.arr[i].idModule] = '';
			}
			vars.JsonModule = 'dummy';
			vars.ArrModule = data;

			this._eventValue({
				vars     : vars,
				idTarget : (obj.flag == 'edit')? this.varsChild.idTarget : ''
			});
		}

		this._varsDetailConnect = obj;
		this._sendDetailConnect();
	},

	/**
	 *
	*/
	_eventDetailConnectJsonModule : function(obj)
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