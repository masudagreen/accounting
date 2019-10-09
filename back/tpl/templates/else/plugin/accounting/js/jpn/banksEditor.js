{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_BanksEditor = Class.create(Code_Lib_ExtEditor,
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
	_varsDetailConfig : null,
	_iniVars : function(obj)
	{
		this._extVars(obj);
		this.vars.portal.varsDetail.varsDetail = this.varsChild.varsDetail;
		this._varsSearch = this.insCurrent.getVarsSearch();
		this._setVarsKeyNum({arr : this.vars.portal.varsDetail.varsDetail});
		this._varsDetailConfig = (Object.toJSON(this.vars.portal.varsDetail.varsDetail)).evalJSON();
	},

	/**
	 *
	*/
	_varsKeyNum : {},
	_setVarsKeyNum : function(obj)
	{
		this._varsKeyNum = {};
		for (var i = 0; i < obj.arr.length; i++) {
			this._varsKeyNum[obj.arr[i].id] = i;
		}
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
	eventNaviBtnSave : function()
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
	_setDetailStart : function()
	{
		var str = 'strTitle' + this.varsChild.flagType.capitalize();
		this.insDetail.eventList({
			flagMoveUse : 0,
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
	_varsContent : {num : 0},
	_setDetailContent : function()
	{
		this._varsContent.num = 0;
		this._iniDetailFormCalender();
		this._iniDetailFormSelect();
		this._iniDetailFormView();
	},

	/**
	 *
	*/
	_iniDetailFormSelect : function()
	{
		this._setDetailFormSelect({arr : this.insDetail.insForm.vars.varsDetail});
	},

	/**
	 *
	*/
	_varsDetailFormSelect : {},
	_setDetailFormSelect : function()
	{
		var id = 'FlagIn';
		if (!$(this.insDetail.insForm.idSelf + id)) {
			return;
		}
		var insFormSelect = new Code_Lib_FormSelect({
			eleInsert  : $(this.insDetail.insForm.idSelf + id),
			insRoot    : this.insRoot,
			insCurrent : this,
			idSelf     : this.idSelf + 'FormSelect' + id,
			allot      : this._getDetailFormSelectAllot(),
			vars       : null
		});
		this._varsDetailFormSelect.insFormSelect = insFormSelect;
	},


	/**
	 *
	*/
	_getDetailFormSelectAllot : function()
	{
		var allot = function(obj) {
			var insCurrent = obj.insCurrent;
			insCurrent._iniDetailFormView();
		};

		return allot;
	},

	/**
	 *
	*/
	_eventRemoveDetailFormSelect : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			this._varsDetailFormSelect.insFormSelect.stopListener();
			return;
		}
	},

	/**
	 *
	*/
	_iniDetailFormView : function()
	{
		this.insDetail.setValue();
		var flagIn = this._getDetailFlagIn({arr : this.insDetail.insForm.vars.varsDetail});
		this._setDetailFormViewFlagIn({
			flagIn : flagIn,
			arr    : this.insDetail.insForm.vars.varsDetail
		});
	},

	/**
	 *
	*/
	_getDetailFlagIn : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'FlagIn') {
				return parseFloat(obj.arr[i].value);
			}
		}
	},

	/**
	 *
	*/
	_setDetailFormViewFlagIn : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'NumValueIn') {
				this.insDetail.insForm.viewForm({
					idTarget    : obj.arr[i].id,
					flagHideNow : (obj.flagIn)? 0 : 1
				});
			} else if (obj.arr[i].id == 'NumValueOut') {
				this.insDetail.insForm.viewForm({
					idTarget    : obj.arr[i].id,
					flagHideNow : (obj.flagIn)? 1 : 0
				});
			}
		}
	},

	/**
	 *
	*/
	_iniDetailFormCalender : function()
	{
		this._extDetailFormCalender();
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
	_getDetailFormContentVars : function(obj)
	{

	},

	/**
	 *
	*/
	_eventLayoutDetailContent : function()
	{
		this._setDetailContent();
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
		this._eventRemoveDetailFormSelect({arr : this.insDetail.insForm.vars.varsDetail});
		this._eventRemoveDetailFormCalender({arr : this.insDetail.insForm.vars.varsDetail});
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
	_eventSelectShortCut : function()
	{

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
				var vars = insCurrent._getDetailFormFormat();
				insCurrent.insNavi.eventMove({vars : vars});
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

					} else if (obj.vars.vars.vars.idTarget == 'calc') {
						insCurrent._eventDetailConnect({flag : obj.vars.vars.vars.idTarget});

					} else if (obj.vars.vars.vars.idTarget == 'folder') {
						insCurrent.insDetail.setValue();
						insCurrent._setDetailContentValue();
						var varsFormat = insCurrent._getDetailFormFormat();
						insCurrent.insNavi.addVars({vars : {vars : varsFormat, strTitle : varsFormat.StrTitle}});
						insCurrent.insDetail.showBtnBottom();
					}
				}

			}
		};

		return allot;
	},

	/**
	 *
	*/
	_eventDetailConnect : function(obj)
	{
		var insEscape = new Code_Lib_Escape();

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
			this.insDetail.setValue();
			this._setDetailContentValue();
			var vars = this.insDetail.getFormValue();

			if (parseFloat(vars.FlagIn)) {
				vars.NumValueOut = '0';
			} else {
				vars.NumValueIn = '0';
			}
			var array = vars.StampBook.split('-');
			if (!array[1]) {
				vars.StampBook = array[0] + '-00:00';
			}

			var cut = this.insCurrent.vars.varsItem;
			var array = vars.StampBook.split('-');
			var stamp = insEscape.toStampFromTerm({
				data        : array[0],
				insTimeZone : this.insRoot.insTimeZone
			});

			if (!(cut.varsStampTerm.stampMin <= stamp && stamp <= cut.varsStampTerm.stampMax)) {
				this.insDetail.showFormAttestError({flagType : 'term'});
				return;
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
	_eventDetailConnectSuccess : function(obj)
	{
		if (obj.json.flag == 1) {
			if (this._varsDetailConnect.flag == 'add') {
				this.insCurrent.eventDetailConnectSuccessListUpdate(obj);
				this._setDetailEnd();

			} else if (this._varsDetailConnect.flag.match(/^edit/)) {
				this.insCurrent.eventDetailConnectSuccessListDetailUpdate(obj);
				this._setDetailEnd();
			}

		} else if (obj.json.flag == 40) {
			this.insCurrent.eventDetailConnectSuccessListUpdateDetailReset(obj);
			alert(this.insRoot.vars.varsSystem.str.oldData);
			if (!this.insWindow.vars.flagHideNow) this.insWindow.updateHide({ flagEffect : 1 });

		} else if (obj.json.flag == 42) {
			alert(this.insRoot.vars.varsSystem.str.errorMail);

		} else if (obj.json.flag == 8) {
			alert(this.insRoot.vars.varsSystem.str.oldData);

		} else {
			this.insDetail.showFormAttestError({flagType : obj.json.flag});

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