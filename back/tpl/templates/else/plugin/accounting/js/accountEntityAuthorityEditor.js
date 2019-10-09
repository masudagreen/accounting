{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_AccountEntityAuthorityEditor = Class.create(Code_Lib_ExtEditor,
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
		this._getDetailFormListVars(obj);
	},


	/**
	 *
	*/
	_setDetailContent : function(obj)
	{
		this._iniDetailFormList();
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
	_setDetailFormList : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormList) continue;
			var ele = this.insDetail.insForm.eleWrap.down('.codeLibFormContent', num);
			ele.setStyle({width : this._getDetailFormListWidth() + 'px'});
			var allot = {};
			if (obj.arr[i].id == 'IdAuthority') {
				allot = this._getDetailFormListAuthorityAllot();
			} else {
				allot = this._getDetailFormListAccessAllot();
			}
			var insList = new Code_Lib_FormList({
				eleInsert  : ele,
				insRoot    : this.insRoot,
				insCurrent : this,
				idSelf     : this.insDetail.insForm.idSelf + 'DetailFormList' + obj.arr[i].id,
				allot      : allot,
				vars       : obj.arr[i].varsFormList
			});
			this._varsDetailFormList.push({
				id       : obj.arr[i].id,
				insList  : insList
			});
			num++;
		}
	},


	/**
	 *
	*/
	_getDetailFormListAuthorityAllot : function()
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
					insParent._eventValue({
						vars     : {},
						idTarget : insParent.varsChild.idTarget
					});
					if (obj.arr[i].id == 'IdAuthority') {
						insParent.insRoot.insChoice.setBoot({
							flagId       : obj.arr[i].id,
							varsValue    : insParent._varsValue,
							idTarget     : obj.arr[i].varsChoice.idTarget,
							idModule     : obj.arr[i].varsChoice.idModule,
							flagCheckUse : obj.arr[i].varsChoice.flagCheckUse,
							strFunc      : 'setDetailFormListChoiceValue',
							numTop       : insParent._staticDetailFormCheck.numTop + $(insParent.insWindow.idWindow).offsetTop,
							numLeft      : insParent._staticDetailFormCheck.numLeft + $(insParent.insWindow.idWindow).offsetLeft,
							insCurrent   : insParent
						});
						break;
					}
				}
				return 1;
			}
		};

		return allot;
	},

	/**
	 *
	*/
	_getDetailFormListAccessAllot : function()
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
					insParent._eventValue({
						vars     : {},
						idTarget : insParent.varsChild.idTarget
					});
					if (obj.arr[i].id == 'IdAccess') {
						insParent.insRoot.insChoice.setBoot({
							flagId       : obj.arr[i].id,
							varsValue    : insParent._varsValue,
							idTarget     : obj.arr[i].varsChoice.idTarget,
							idModule     : obj.arr[i].varsChoice.idModule,
							flagCheckUse : obj.arr[i].varsChoice.flagCheckUse,
							strFunc      : 'setDetailFormListChoiceValue',
							numTop       : insParent._staticDetailFormCheck.numTop + $(insParent.insWindow.idWindow).offsetTop,
							numLeft      : insParent._staticDetailFormCheck.numLeft + $(insParent.insWindow.idWindow).offsetLeft,
							insCurrent   : insParent
						});
						break;
					}
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
	_backDetailContentValue : function()
	{
		this._backDetailFormListValue({arr : this.insDetail.insForm.vars.varsDetail});
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
