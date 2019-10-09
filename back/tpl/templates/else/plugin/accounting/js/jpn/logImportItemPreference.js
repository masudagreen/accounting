{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_LogImportItemPreference = Class.create(Code_Lib_ExtPortal,
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
	_iniNavi : function()
	{
		this._extNavi();
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
			else if (obj.from == '_mousedownMove') insCurrent.insNavi.eventMove({vars : obj.vars});
			else if (obj.from == 'view-eventLayout') insCurrent._eventLayoutDetailContent();
			else if (obj.from == 'view-eventBtnBottom') insCurrent._eventDetailConnect({flag : obj.vars.vars.vars.idTarget});
		};

		return allot;
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
	_getDetailChildVars : function(obj)
	{
		var insDisplay = new Code_Lib_TimeDisplay();
		var arrayNew = [];
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'StrTitle') {
				if (obj.flag == 'add') obj.arr[i].value = '';
				else obj.arr[i].value = obj.vars.varsColumnDetail.strTitle;
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'FlagAttest') {
				if (obj.flag == 'add') {
					obj.arr[i].value = this._getOptionFirstValue({arr : obj.arr[i].arrayOption});

				} else {
					obj.arr[i].value = obj.vars.flagAttest;
				}
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'FlagReverse') {
				if (obj.flag == 'add') {
					obj.arr[i].value = this._getOptionFirstValue({arr : obj.arr[i].arrayOption});

				} else {
					obj.arr[i].value = obj.vars.flagReverse;
				}
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'IdAccountTitle') {
				obj.arr[i].strExplain = obj.arr[i].varsTmpl.strNormal;
				if (obj.flag == 'add') {
					obj.arr[i].value = this._getOptionFirstValue({arr : obj.arr[i].arrayOption});

				} else {
					if (obj.flag == 'edit') {
						if (this.vars.varsItem.strLostColumn == obj.vars.varsColumnDetail.idAccountTitle) {
							obj.arr[i].strExplain = obj.arr[i].varsTmpl.strLost;
							obj.arr[i].value = this._getOptionFirstValue({arr : obj.arr[i].arrayOption});
							arrayNew.push(obj.arr[i]);
							continue;
						}
					}
					obj.arr[i].value = obj.vars.idAccountTitle;
				}
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'IdSubAccountTitle') {
				obj.arr[i].strExplain = obj.arr[i].varsTmpl.strNormal;
				if (obj.flag == 'add') {
					obj.arr[i].value = this._getOptionFirstValue({arr : obj.arr[i].arrayOption});

				} else {
					if (obj.flag == 'edit') {
						if (this.vars.varsItem.strLostColumn == obj.vars.varsColumnDetail.idSubAccountTitle) {
							obj.arr[i].strExplain = obj.arr[i].varsTmpl.strLost;
							obj.arr[i].value = this._getOptionFirstValue({arr : obj.arr[i].arrayOption});
							arrayNew.push(obj.arr[i]);
							continue;
						}
					}
					obj.arr[i].value = obj.vars.idSubAccountTitle;
				}
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'IdDepartment') {
				obj.arr[i].strExplain = obj.arr[i].varsTmpl.strNormal;
				if (obj.flag == 'add') {
					obj.arr[i].value = this._getOptionFirstValue({arr : obj.arr[i].arrayOption});

				} else {
					if (obj.flag == 'edit') {
						if (this.vars.varsItem.strLostColumn == obj.vars.varsColumnDetail.idDepartment) {
							obj.arr[i].strExplain = obj.arr[i].varsTmpl.strLost;
							obj.arr[i].value = this._getOptionFirstValue({arr : obj.arr[i].arrayOption});
							arrayNew.push(obj.arr[i]);
							continue;
						}
					}
					obj.arr[i].value = obj.vars.idDepartment;
				}
				arrayNew.push(obj.arr[i]);

			}  else if (obj.arr[i].id == 'ArrSpaceStrTag') {
				if (obj.flag == 'add') obj.arr[i].value = '';
				else obj.arr[i].value = obj.vars.arrSpaceStrTag;
				arrayNew.push(obj.arr[i]);
			}
		}

		return arrayNew;
	},


	/**
	 *
	*/
	_updateDetailListVars : function(obj)
	{
		var objDetail = (Object.toJSON(this.vars.portal.varsDetail.templateDetail)).evalJSON();
		var varsBtn = (Object.toJSON(this.vars.portal.varsDetail.varsBtn)).evalJSON();

		var flagBtn = 0;
		if (!obj.vars.varsAuthority.flagDelete || obj.vars.flagDefault) {
			flagBtn = 1;
		}

		var objData = {
			flagMoveUse : 1,
			strTitle    : this.insDetail.vars.varsStart.strTitle,
			strClass    : obj.vars.strClass,
			vars        : {
				varsDetail : this._updateDetailListVarsChild({arr : objDetail, vars : obj.vars }),
				varsBtn    : this._updateDetailListVarsBtn({
					arr  : varsBtn,
					flag : flagBtn
				}),
				varsEdit   : this._updateDetailListVarsEdit({
					arr           : this.insDetail.vars.view.varsEdit,
					flag          : 0,
					varsAuthority : obj.vars.varsAuthority
				}),
				vars       : obj.vars
			}
		};

		return objData;
	},

	/**
	 *
	*/
	_updateDetailListVarsEdit : function(obj)
	{
		obj.arr.flagEditUse = 1;

		if (!obj.varsAuthority) {
			obj.arr.flagAddUse = 0;
			obj.arr.flagCopyUse = 0;
			obj.arr.flagEditUse = 0;

		} else {
			if (!obj.varsAuthority.flagInsert) {
				obj.arr.flagAddUse = 0;
				obj.arr.flagCopyUse = 0;
			}
			if (!obj.varsAuthority.flagUpdate) {
				obj.arr.flagEditUse = 0;
			}
		}

		return obj.arr;
	},

	/**
	 *
	*/
	_updateDetailListVarsChild : function(obj)
	{
		var insDisplay = new Code_Lib_TimeDisplay();
		var insEscape = new Code_Lib_Escape();
		var arrayNew = [];

		for (var i = 0; i < obj.arr.length; i++) {

			if (obj.arr[i].id == 'StampRegister') {
				var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.vars.stampRegister * 1000});
				obj.arr[i].value = insDisplay.get({flagType : 1, vars : objTime});
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'StampUpdate') {
				var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.vars.stampUpdate * 1000});
				obj.arr[i].value = insDisplay.get({flagType : 1, vars : objTime});
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'StrTitle') {
				obj.arr[i].value = obj.vars.strTitle;
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'DummyIdAccountTitle') {
				obj.arr[i].value = obj.vars.varsColumnDetail.idAccountTitle;
				if (this.vars.varsItem.strLostColumn == obj.vars.varsColumnDetail.idAccountTitle) {
					obj.arr[i].value = this.vars.varsItem.strLost;
				}
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'DummyIdSubAccountTitle') {
				obj.arr[i].value = obj.vars.varsColumnDetail.idSubAccountTitle;
				if (this.vars.varsItem.strLostColumn == obj.vars.varsColumnDetail.idSubAccountTitle) {
					obj.arr[i].value = this.vars.varsItem.strLost;
				}
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'DummyIdDepartment') {
				obj.arr[i].value = obj.vars.varsColumnDetail.idDepartment;
				if (this.vars.varsItem.strLostColumn == obj.vars.varsColumnDetail.idDepartment) {
					obj.arr[i].value = this.vars.varsItem.strLost;
				}
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'DummyFlagAttest') {
				obj.arr[i].value = obj.vars.varsColumnDetail.flagAttest;
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'DummyFlagReverse') {
				obj.arr[i].value = obj.vars.varsColumnDetail.flagReverse;
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'ArrSpaceStrTag') {
				obj.arr[i].value = (obj.vars.arrSpaceStrTag)? obj.vars.arrSpaceStrTag : '-';
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'Id') {
				obj.arr[i].value = obj.vars.id;
				arrayNew.push(obj.arr[i]);

			}

		}

		return arrayNew;
	},

	/**
	 *
	*/
	_iniChild : function(obj)
	{
		this._extChild(obj);
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