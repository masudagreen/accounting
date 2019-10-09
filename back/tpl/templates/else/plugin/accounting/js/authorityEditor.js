{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_AuthorityEditor = Class.create(Code_Lib_ExtEditor,
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
	_iniDetailFormCheck : function()
	{
		this._extDetailFormCheck();
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
				var insEscape = new Code_Lib_Escape();
				if (obj.vars.varsColumn.id == 'FlagSelect' && obj.vars.vars.id == 'All') {
					obj.arr = insCurrent.vars.varsDetail;
					obj.arrColumn = insCurrent.vars.varsColumn;
					for (var i = 0; i < obj.arr.length; i++) {
						if (obj.arr[i].id == 'All') {
							for (var j = 0; j < obj.arrColumn.length; j++) {
								if (obj.arrColumn[j].id == 'StrType') continue;
								var id = insEscape.strLowCapitalize({data : obj.arrColumn[j].id});
								var strNow = id + 'Now';
								var strLock = id + 'Lock';

								if (obj.vars.vars.varsColumnDetail.flagSelectNow) {
									obj.arr[i].varsColumnDetail[strLock] = 0;

								} else {
									if (obj.arrColumn[j].id == 'FlagSelect') {
										obj.arr[i].varsColumnDetail[strLock] = 0;
										continue;
									}
									obj.arr[i].varsColumnDetail[strNow] = 0;
									obj.arr[i].varsColumnDetail[strLock] = 1;
									obj.arr[0].varsColumnDetail[strLock] = 0;
								}

							}
							return;
						}
					}

				} else if (obj.vars.vars.id == 'All') {
					obj.arr = insCurrent.vars.varsDetail;
					obj.arrColumn = insCurrent.vars.varsColumn;
					for (var i = 0; i < obj.arr.length; i++) {
						if (obj.arr[i].id == 'All') {
							for (var j = 0; j < obj.arrColumn.length; j++) {
								if (obj.arrColumn[j].id == 'StrType') continue;
								var id = insEscape.strLowCapitalize({data : obj.arrColumn[j].id});
								var strNow = id + 'Now';
								var strLock = id + 'Lock';
								if (obj.arrColumn[j].id == obj.vars.varsColumn.id) {
									if (obj.vars.vars.varsColumnDetail[strNow]) {
										obj.arr[0].varsColumnDetail[strNow] = 1;
										obj.arr[0].varsColumnDetail[strLock] = 1;

									} else {
										obj.arr[0].varsColumnDetail[strLock] = 0;
									}
									return;
								}

							}
							return;
						}
					}

				}
			}
		};

		return allot;
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

			obj.arr = vars.JsonAuthority;
			var data = {};
			for (var i = 0; i < obj.arr.length; i++) {
				if (obj.arr[i].id == 'My') {
					data.flagMySelect = obj.arr[i].varsColumnDetail.flagSelectNow;
					data.flagMyInsert = obj.arr[i].varsColumnDetail.flagInsertNow;
					data.flagMyDelete = obj.arr[i].varsColumnDetail.flagDeleteNow;
					data.flagMyUpdate = obj.arr[i].varsColumnDetail.flagUpdateNow;
					data.flagMyOutput = obj.arr[i].varsColumnDetail.flagOutputNow;

				} else {
					data.flagAllSelect = obj.arr[i].varsColumnDetail.flagSelectNow;
					data.flagAllInsert = obj.arr[i].varsColumnDetail.flagInsertNow;
					data.flagAllDelete = obj.arr[i].varsColumnDetail.flagDeleteNow;
					data.flagAllUpdate = obj.arr[i].varsColumnDetail.flagUpdateNow;
					data.flagAllOutput = obj.arr[i].varsColumnDetail.flagOutputNow;
				}
			}
			vars.JsonAuthority = 'dummy';
			vars.ArrAuthority = data;

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