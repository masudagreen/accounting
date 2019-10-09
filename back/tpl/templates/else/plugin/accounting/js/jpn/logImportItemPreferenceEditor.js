{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_LogImportItemPreferenceEditor = Class.create(Code_Lib_ExtEditor,
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
	_getDetailAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == 'eventRemove-detail') insCurrent._eventRemoveDetailContent();
			else if (obj.from == '_mousedownMove') {

			}
			else if (obj.from == 'form-preEventLayout') insCurrent._preEventLayoutDetailContent();
			else if (obj.from == 'form-eventLayout') insCurrent._eventLayoutDetailContent();
			else if (obj.from == 'form-eventBtnBottom') {
				if (obj.vars.vars.vars.flagBack) {
					insCurrent._backDetailEnd();

				} else if (obj.vars.vars.vars.flagHide) {
					if (!insCurrent.insWindow.vars.flagHideNow) insCurrent.insWindow.updateHide({ flagEffect : 1 });

				} else {
					if (obj.vars.vars.vars.idTarget == 'save') {
						insCurrent._eventDetailConnect({flag : insCurrent.varsChild.flagType});

					}
				}

			}
		};

		return allot;
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
		this._iniDetailFormSelect();
	},

	/**
	 *
	*/
	_iniDetailFormSelect : function()
	{
		this._setDetailFormSelect({arr : this.insDetail.insForm.vars.varsDetail});
		this._setDetailFormSelectIdAccountTitle({
			arr      : this.insDetail.insForm.vars.varsDetail,
			flagIni  : 1
		});
	},

	/**
	 *
	*/
	_varsDetailFormSelect : {},
	_setDetailFormSelect : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'IdAccountTitle') {
				var strFunc = '_getDetailFormSelect' + obj.arr[i].id +'Allot';
				var insFormSelect = new Code_Lib_FormSelect({
					eleInsert  : $(this.insDetail.insForm.idSelf + obj.arr[i].id),
					insRoot    : this.insRoot,
					insCurrent : this,
					idSelf     : this.idSelf + 'FormSelect' + obj.arr[i].id,
					allot      : this[strFunc](),
					vars       : null
				});

				this._varsDetailFormSelect[obj.arr[i].id] = insFormSelect;
			}

		}
	},

	/**
	 *
	*/
	_getDetailFormSelectIdAccountTitleAllot : function()
	{
		var allot = function(obj) {
			var insCurrent = obj.insCurrent;
			insCurrent._setDetailFormSelectIdAccountTitle({
				arr : insCurrent.insDetail.insForm.vars.varsDetail
			});
		};

		return allot;
	},

	/**
	 *
	*/
	_setDetailFormSelectIdAccountTitle : function(obj)
	{
		if (!obj.flagIni) {
			this.insDetail.setValue();
		}
		var idAccountTitle = '';
		var arrSelectTag = (Object.toJSON(this.insCurrent.vars.varsRule.arrSubAccountTitle.arrSelectTag)).evalJSON();
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'IdAccountTitle') {
				idAccountTitle = obj.arr[i].value;

			} else if (obj.arr[i].id == 'IdSubAccountTitle') {
				var varsNone = (Object.toJSON(obj.arr[i].varsTmpl.varsNone)).evalJSON();

				if (!this.insCurrent.vars.varsRule.arrSubAccountTitle.arrStrTitle[idAccountTitle]) {
					obj.arr[i].arrayOption = [varsNone];

				} else {
					arrSelectTag[idAccountTitle].unshift(varsNone);
					obj.arr[i].arrayOption = arrSelectTag[idAccountTitle];
					if (!obj.flagIni) {
						obj.arr[i].value = 'none';
					}
				}

				$(this.insDetail.insForm.idSelf + obj.arr[i].id).innerHTML = '';
				this.insDetail.insForm.setTemplateSelect({
					arr       : obj.arr[i].arrayOption,
					now       : obj.arr[i].value,
					eleInsert : $(this.insDetail.insForm.idSelf + obj.arr[i].id),
					vars      : obj.arr[i]
				});
			}
		}
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
	_backDetailContentValue : function()
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

		} else if (obj.flag == 'add' || obj.flag == 'edit') {
			if (this.insDetail.checkForm({flagType : 'common'})) return;
			var vars = this.insDetail.getFormValue();
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
	_iniCss : function()
	{
		this._extCss();
	}
});
{/literal}