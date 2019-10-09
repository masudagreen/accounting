{literal}
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Core_Base_AccountEditor = Class.create(Code_Lib_ExtEditor,
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
				insCurrent.insDetail.setValue();
				insCurrent._setDetailContentValue();
				var vars = insCurrent.insDetail.getFormValue();
				vars.StrTitle = (vars.StrCodeName)? vars.StrCodeName : '';
				insCurrent.insNavi.eventMove({vars : vars});
				insCurrent._backDetailContentValue();

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
	_getDetailFormContentVars : function(obj)
	{
		this._getDetailFormListVars(obj);
	},


	/**
	 *
	*/
	_setDetailContent : function()
	{
		this._iniDetailFormList();
	},

	/**
	 *
	*/
	_eventLayoutDetailContent : function()
	{
		if (!this.insDetail.insForm) return;
		this._iniDetailFormList();

	},

	/**
	 *
	*/
	_preEventLayoutDetailContent : function()
	{
		if (!this.insDetail.insForm) return;
		this._getDetailFormListVars({arr : this.insDetail.insForm.vars.varsDetail});

	},

	/**
	 *
	*/
	_eventRemoveDetailContent : function()
	{
		if (!this.insDetail.insForm) return;
		this._eventRemoveDetailFormList({arr : this.insDetail.insForm.vars.varsDetail});

	},

	/**
	 *
	*/
	_setDetailContentValue : function()
	{
		this._setDetailFormListValue({arr : this.insDetail.insForm.vars.varsDetail});
	},


	/**
	 *
	*/
	_backDetailContentValue : function()
	{
		this._backDetailFormListValue({arr : this.insDetail.insForm.vars.varsDetail});
	},

	/**
	 *
	*/
	_iniDetailFormList : function()
	{
		this._extDetailFormList();
	},

	/**
	 *
	*/
	_getDetailFormListAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			var insParent = insCurrent.insCurrent;
			if (obj.from == '_mousedownAdd') {
				obj.arr = insParent.insDetail.insForm.vars.varsDetail;
				var num = 0;
				for (var i = 0; i < obj.arr.length; i++) {
					if (!obj.arr[i].varsFormList) continue;
					if (insCurrent.idSelf == insParent.insDetail.insForm.idSelf + 'DetailFormList' + obj.arr[i].id) {
						if (obj.arr[i].id == 'IdTerm' || obj.arr[i].id == 'IdModule') {
							var array = obj.arr[i].id.split('Id');
							insParent.insRoot.insChoice.setBoot({
								flagId       : obj.arr[i].id,
								idTarget     : array[1],
								idModule     : 'Base',
								flagCheckUse : 0,
								strFunc      : 'setDetailFormListChoiceValue',
								numTop       : insParent._staticDetailFormList.numTop + $(insParent.insWindow.idWindow).offsetTop,
								numLeft      : insParent._staticDetailFormList.numLeft + $(insParent.insWindow.idWindow).offsetLeft,
								insCurrent   : insParent
							});
						}
						break;
					}
					num++;
				}

				return 1;
			}
		};

		return allot;
	},

	/**
	 *
	*/
	setDetailFormListChoiceValue : function(obj)
	{
		this.insDetail.setValue();
		obj.arr = this.insDetail.insForm.vars.varsDetail;
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormList) continue;
			if (obj.arr[i].id == obj.flagId) {
				var data = (Object.toJSON(obj.arr[i].varsFormList.templateDetail)).evalJSON();
				data.value = obj.vars.strTitle;
				obj.arr[i].varsFormList.varsDetail[0] = data;
				obj.arr[i].value = obj.vars.vars.idTarget;
				this.vars.portal.varsDetail.varsDetail = obj.arr;
				this._eventRemoveDetailContent();
				this._setDetailStart();
				return;
			}
			num++;
		}
	},

	/**
	 *
	*/
	_setDetailFormListValue : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormList) continue;
			this.insDetail.setFormValue({
				idTarget : obj.arr[i].id,
				value    : {
					value      : obj.arr[i].value,
					varsDetail : this._varsDetailFormList[num].insList.vars.varsDetail
				}
			});
			num++;
		}
	},

	/**
	 *
	*/
	_backDetailFormListValue : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormList) continue;
			this.insDetail.setFormValue({
				idTarget : obj.arr[i].id,
				value    : obj.arr[i].value.value
			});
			num++;
		}
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

		} else if (obj.flag == 'add' || obj.flag == 'edit') {
			if (this.insDetail.checkForm({flagType : 'common'})) return;
			var vars = this.insDetail.getFormValue();
			if (obj.flag == 'edit') {
				if (vars.StrPassword != vars.StrPasswordConfirm) {
					this.insDetail.showFormAttestError({flagType : 'common'});
					return;
				}
			}
			if (obj.flag == 'edit') {
				/*dummy*/
				if (!vars.IdModule || !vars.IdTerm) {
					vars.IdModule = 1;
					vars.IdTerm = 1;
				}
			}
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