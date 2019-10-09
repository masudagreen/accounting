{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_FileEditor = Class.create(Code_Lib_ExtEditor,
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
	_backDetailContentValue : function()
	{
		this._backDetailFormListValue({arr : this.insDetail.insForm.vars.varsDetail});
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
					insParent.insRoot.insChoice.setBoot({
						flagId       : obj.arr[i].id,
						idTarget     : obj.arr[i].varsChoice.idTarget,
						idModule     : obj.arr[i].varsChoice.idModule,
						flagCheckUse : obj.arr[i].varsChoice.flagCheckUse,
						strFunc      : 'setDetailFormListChoiceValue',
						numTop       : insParent._staticDetailFormList.numTop + $(insParent.insWindow.idWindow).offsetTop,
						numLeft      : insParent._staticDetailFormList.numLeft + $(insParent.insWindow.idWindow).offsetLeft,
						insCurrent   : insParent
					});
					break;
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
		if (!obj.vars) return;
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
	_eventLayoutDetailContent : function()
	{
		this._iniDetailFormList();
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
		this._eventRemoveDetailFormList({arr : this.insDetail.insForm.vars.varsDetail});
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

			if (obj.flag == 'add') {
				var varsDetail = this._getVarsDetail({
					arr      : this.insDetail.insForm.vars.varsDetail,
					idTarget : 'Upload'
				});
				if (vars.Upload == '') {
					this.insDetail.showFormAttestError({flagType : 'strBlank'});
					return;
				}
				arr = vars.Upload;
				for (var i = 0; i < arr.length; i++) {
					var array = arr[i].split('.');
					var strFileType = array[array.length - 1];
					strFileType = strFileType.toLowerCase();
					if (!varsDetail.arrFileType[strFileType]) {
						this.insDetail.showFormAttestError({flagType : 'strFileType'});
						return;
					}
				}
			}

			this._varsDetailConnect = obj;

			this.insRoot.setUpload({
				id         : this.idSelf,
				insClass   : this,
				strFunc    : 'eventDetailUpload',
				eleLoading : this.insDetail.insUnder.eleFormat.header.down('.codeLibBaseMarginRightFive', 0)
			});
			this.insDetail.insForm.eleForm.submit();
			return;
		}

		this._varsDetailConnect = obj;
		this._sendDetailConnect();
	},

	/**
	 *
	*/
	_getVarsDetail : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == obj.idTarget) {
				return obj.arr[i];
			}
		}
		return {};
	},

	/**
	 *
	*/
	eventDetailUpload : function(obj)
	{
		if (obj.vars) {
			if (obj.vars.stamp) {
				if (obj.vars.stamp.id) this._varsStamp[obj.vars.stamp.id] = obj.vars.stamp.stamp;
			}
			if (obj.vars.flag) {
				if (obj.vars.numNews) this.insRoot.iniPopup({flag : 'news', numNews : obj.vars.numNews});
				this._eventDetailConnectSuccess({json : obj.vars});
			}
			else if (obj.vars.flag == 0) alert(this.insRoot.vars.varsSystem.str.errorSession);
		}
		else alert(this.insRoot.vars.varsSystem.str.errorRequest);

	},

	/**
	 *
	*/
	_eventDetailConnectSuccess : function(obj)
	{
		if (obj.json.flag == 1) {
			if (this._varsDetailConnect.flag == 'add') {
				this.insCurrent.eventEditorSendSuccess({flag : this._varsDetailConnect.flag});
				this._setDetailEnd();
				if (this.varsChild.flagBack) {
					var arr = obj.json.data.arrLogFile;
					var arrNew = [];
					for (var i = 0; i < arr.length; i++) {
						var temp = {
							strTitle  : (arr[i].strTitle)? arr[i].strTitle : this._varsDetailConnect.strTitle,
							vars      : {
								idTarget : arr[i].idLogFile
							}
						};
						arrNew.push(temp);
					}
					this.insCurrent.checkAutoSearch({
						flag : this.varsChild.flagBack,
						vars : arrNew
					});
				}

			} else if (this._varsDetailConnect.flag.match(/^edit/)) {
				this.insCurrent.eventEditorSendSuccess({flag : 'edit', idTarget : obj.json.data.idTarget});
				this._setDetailEnd();
			}

		} else if (obj.json.flag == 40) {
			this.insCurrent.eventEditorSendSuccess({flag : 'reset'});
			alert(this.insRoot.vars.varsSystem.str.oldData);
			if (!this.insWindow.vars.flagHideNow) this.insWindow.updateHide({ flagEffect : 1 });

		} else if (obj.json.flag == 42) {
			alert(this.insRoot.vars.varsSystem.str.errorMail);

		} else if (obj.json.flag == 8) {
			alert(this.insRoot.vars.varsSystem.str.oldData);

		} else {
			this.insDetail.showFormAttestError({flagType : obj.json.flag});
			if (obj.json) {
				if (obj.json.flag) {
					if (this.insRoot.vars.varsSystem.str[obj.json.flag]) {
						alert(this.insRoot.vars.varsSystem.str[obj.json.flag]);
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