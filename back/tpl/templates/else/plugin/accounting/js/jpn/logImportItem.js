{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_LogImportItem = Class.create(Code_Lib_ExtPortal,
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
	_getLayoutAllot : function()
	{
		var allot =  function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == 'eventLayout') {
				if (insCurrent.insNavi) insCurrent.insNavi.eventLayout();
				if (insCurrent.insList) insCurrent.insList.eventLayout();
				if (insCurrent.insDetail) insCurrent.insDetail.eventLayout();

			} else if (obj.from == 'preEventLayout') {
				insCurrent._preEventLayout({flag : 'dummy'});

			} else if (obj.from == 'navi-_mousedownNavi') {
				if (obj.vars.id == 'Switch') {
					insCurrent.insNavi.eventTool({
						idTarget : insCurrent.insNavi.vars.varsStatus.flagNow,
						flagLoop : 1
					});

				} else if (obj.vars.id == 'Reload') {
					insCurrent._preEventLayout({flag : 'reset'});
					insCurrent._eventNaviConnect({vars : obj.vars, flag : insCurrent.insNavi.vars.varsStatus.flagNow + '-reload'});

				} else if (obj.vars.id == 'Preference') {
					insCurrent._iniChild({
						strTitleParent  : insCurrent.insWindow.vars.strTitle,
						strTitleChild   : insCurrent.vars.child.varsTitle[obj.vars.id],
						strExt          : obj.vars.id,
						strChild        : '',
						strClass        : insCurrent.strClass,
						idModule        : insCurrent.idModule
					});
				}

			} else if (obj.from == 'navi-_mousedownMenu') {
				return insCurrent.insNavi.vars.varsStatus.flagNow;

			} else if (obj.from == 'navi-_mousedownLine') {
				insCurrent.insNavi.eventTool({idTarget : obj.vars});

			} else if (obj.from == 'list-_mousedownNavi') {
				if (obj.vars.id == 'Switch') {
					insCurrent.insList.eventTool({
						idTarget : insCurrent.insList.vars.varsStatus.flagNow,
						flagLoop : 1
					});

				} else if (obj.vars.id == 'Reload') {
					insCurrent._preEventLayout({flag : 'reset'});
					insCurrent._eventListConnect({flag : obj.vars.id});

				} else if (obj.vars.id == 'Output') {
					insCurrent._eventListConnect({flag : obj.vars.id, flagType : insCurrent.insList.vars.varsStatus.flagOutputNow});

				} else if (obj.vars.id == 'Print') {
					insCurrent._eventListConnect({flag : obj.vars.id, flagType : insCurrent.insList.vars.varsStatus.flagPrintNow});
				}

			} else if (obj.from == 'list-_mousedownMenu') {
				if (obj.vars.id == 'Switch') {
					return insCurrent.insList.vars.varsStatus.flagNow;

				} else if (obj.vars.id == 'Output') {
					return insCurrent.insList.vars.varsStatus.flagOutputNow;

				} else if (obj.vars.id == 'Print') {
					return insCurrent.insList.vars.varsStatus.flagPrintNow;
				}

			} else if (obj.from == 'list-_mousedownLine') {
				if (obj.varsTarget == 'Switch') {
					return insCurrent.insList.eventTool({idTarget : obj.vars});

				} else if (obj.varsTarget) {
					insCurrent.insList.eventTool({idTarget : obj.vars, flagStr : obj.varsTarget});
					insCurrent._eventListConnect({flag : obj.varsTarget, flagType : obj.vars});
					return;
				}

			} else if (obj.from == 'detail-_mousedownNavi') {

				if (obj.vars.id == 'Reload') {
					insCurrent._preEventLayout({flag : 'reset'});
					insCurrent._eventDetailConnect({flag : 'reload'});

				} else if (obj.vars.id == 'Output') {
					insCurrent._eventDetailConnect({flag : 'output', flagType : insCurrent.insDetail.vars.varsStatus.flagOutputNow});

				} else if (obj.vars.id == 'Print') {
					insCurrent._eventDetailConnect({flag : obj.vars.id, flagType : insCurrent.insDetail.vars.varsStatus.flagPrintNow});

				} else if (obj.vars.id == 'Add'
						|| obj.vars.id == 'Copy'
						|| obj.vars.id == 'Edit'
				) {
					insCurrent._setDetailChild({flag : obj.vars.id.toLowerCase()});

				} else if (obj.vars.id == 'Preference') {
					insCurrent._checkAutoSearch({idTarget : 'LogImport'});
				}

			} else if (obj.from == 'detail-_mousedownMenu') {
				if (obj.vars.id == 'Switch') {
					return insCurrent.insDetail.vars.varsStatus.flagNow;

				} else if (obj.vars.id == 'Output') {
					return insCurrent.insDetail.vars.varsStatus.flagOutputNow;

				} else if (obj.vars.id == 'Print') {
					return insCurrent.insDetail.vars.varsStatus.flagPrintNow;
				}

			} else if (obj.from == 'detail-_mousedownLine') {
				if (obj.varsTarget == 'Switch') {
					return insCurrent.insDetail.eventTool({idTarget : obj.vars});

				} else if (obj.varsTarget) {
					insCurrent.insDetail.eventTool({idTarget : obj.vars, flagStr : obj.varsTarget});
					if (obj.varsTarget == 'Output') {
						insCurrent._eventDetailConnect({flag : 'output', flagType : obj.vars});

					} else {
						insCurrent._eventDetailConnect({flag : obj.varsTarget, flagType : obj.vars});
					}
					return;
				}

			}
		};

		return allot;
	},

	_flagAutoData : '',
	_varsAutoData : {},
	_checkAutoSearch : function(obj)
	{
		var insEscape = new Code_Lib_Escape();
		this._varsAutoData = {};
		this._flagAutoData = obj.idTarget;

		this.eventAutoSearch();
	},

	eventAutoSearch : function()
	{
		if (this._flagAutoData == 'LogImport') {
			var varsData = this.insTop.checkChildData({idTarget : 'Log'});
			varsData.insClass.bootAutoSearchOver({flag : 'addLogImport'});

		} else if (this._flagAutoData == 'LogImportRetry') {
			var varsData = this.insTop.checkChildData({idTarget : 'Log'});
			varsData.insClass.bootAutoSearchOver({flag : 'showLogImportRetry'});
		}
	},

	/**
	 *
	*/
	_iniVars : function(obj)
	{
		this._extVars(obj);
		this._updateVarsDetailForm({arr : this.vars.portal.varsDetail.templateDetail});
	},

	/**
	 *
	*/
	_varsIdUpload : null,
	_updateVarsDetailForm : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'Upload') {
				var strChild = 'ImportItem';
				var strFunc = 'DetailAdd';
				this._varsIdUpload = this.idSelf + this.strExt + strChild;
				obj.arr[i].arrayHidden = [
					{id : 'class',      value : this.strClass},
					{id : 'module',     value : this.idModule},
					{id : 'ext',        value : this.strExt},
					{id : 'child',      value : strChild},
					{id : 'func',       value : strFunc},
					{id : 'db',         value : 'master'},
					{id : 'idUpload',   value : this._varsIdUpload},
					{id : 'idTag',      value : obj.arr[i].id},
					{id : 'cache',      value : (new Date()).getTime()},
					{id : 'token',      value : (this.insRoot.vars.varsSystem.token)? this.insRoot.vars.varsSystem.token : ''},
				];
				obj.arr[i].value = 'dummy';
			}
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
	_preEventLayoutNaviContent : function()
	{

	},

	/**
	 *
	*/
	_eventLayoutNaviContent : function()
	{

	},

	/**
	 *
	*/
	_setNaviContent : function()
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
	_getDetailAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == 'form-preEventLayout') insCurrent._preEventLayoutDetailContent();
			else if (obj.from == '_mousedownMove') insCurrent.insNavi.eventMove({vars : obj.vars});
			else if (obj.from == 'form-eventLayout') insCurrent._eventLayoutDetailContent();
			else if (obj.from == 'view-eventLayout') insCurrent._eventLayoutDetailContent();
			else if (obj.from == 'view-eventBtnBottom') insCurrent._eventDetailConnect({flag : obj.vars.vars.vars.idTarget});
			else if (obj.from == 'form-eventBtnBottom') {
				if (obj.vars.vars.vars.flagBack) {
					insCurrent._backDetailEnd();

				} else if (obj.vars.vars.vars.flagHide) {
					if (!insCurrent.insWindow.vars.flagHideNow) insCurrent.insWindow.updateHide({ flagEffect : 1 });

				} else if (obj.vars.vars.vars.flagRetry) {
					insCurrent._checkAutoSearch({idTarget : 'LogImportRetry'});
					insCurrent.insDetail.showBtnBottom();

				} else {
					if (obj.vars.vars.vars.idTarget == 'save') {
						insCurrent._eventDetailConnect({flag : obj.vars.vars.vars.idTarget});
					}
				}

			}
		};

		return allot;
	},

	/**
	 *
	*/
	eventWindowAppear : function(obj)
	{
		this._setDetailStart();
	},

	/**
	 *
	*/
	_backDetailEnd : function()
	{
		this._setDetailStart();
	},

	/**
	 *
	*/
	_setDetailStart : function()
	{
		var objDetail = (Object.toJSON(this.vars.portal.varsDetail.templateDetail)).evalJSON();
		this.insDetail.eventList({
			strTitle : this.vars.portal.varsDetail.varsStart.strTitle,
			strClass : this.vars.portal.varsDetail.varsStart.strClass,
			vars     : {
				varsDetail : objDetail,
				varsEdit   : this.vars.portal.varsDetail.varsEdit,
				varsBtn    : this.vars.portal.varsDetail.varsBtn
			}
		});
	},

	_setDetailEnd : function(obj)
	{
		var objDetail = (Object.toJSON(this.vars.portal.varsDetail.varsEnd.templateDetail)).evalJSON();
		var strTitle = this.vars.portal.varsDetail.varsEnd.strTitle;
		var objData = {
			strTitle : strTitle,
			strClass : this.vars.portal.varsDetail.varsEnd.strClass,
			vars     : {
				varsDetail : this._updateDetailEndVars({
					arr     : objDetail,
					strHtml : obj.strHtml
				}),
				varsBtn : this._updateDetailEndVarsBtn({
					arr       : this.vars.portal.varsDetail.varsEnd.varsBtn,
					flagRetry : obj.flagRetry
				}),

				varsEdit   : {},
				vars       : {}
			}
		};
		this.insDetail.eventList(objData);
		this._setDetailContent();
	},

	_updateDetailEndVarsBtn : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'Retry') {
				obj.arr[i].flagBtnUse = 0;
				if (obj.flagRetry) {
					obj.arr[i].flagBtnUse = 1;
				}
			}
		}

		return obj.arr;
	},

	_updateDetailEndVars : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'Table') {
				obj.arr[i].varsSpace.varsDetail.strHtml = obj.strHtml;
			}
		}

		return obj.arr;
	},

	_varsContent : {num : 0},
	_setDetailContent : function(obj)
	{
		this._varsContent.num = 0;
		this._iniDetailSpace();
	},

	/**
	 *
	*/
	_varsDetailSpace : {},
	_iniDetailSpace : function()
	{
		this._varsDetailSpace = {};
		this._setDetailSpace({arr : this.insDetail.insForm.vars.varsDetail});
	},

	/**
	 *
	*/
	_setDetailSpace : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsSpace) continue;
			var ele = this.insDetail.insForm.eleWrap.down('.codeLibFormContent', this._varsContent.num);
			this._varsContent.num++;
			var insSpace = new Code_Lib_Space({
				eleInsert  : ele,
				insRoot    : this.insRoot,
				insCurrent : this,
				idSelf     : this.idSelf + 'Space' + obj.arr[i].id,
				allot      : {},
				vars       : obj.arr[i].varsSpace
			});
			this._varsDetailSpace.ins = insSpace;
			break;
		}
	},

	/**
	 *
	*/
	_eventLayoutDetailContent : function()
	{
		if (!this.insDetail.insForm) return;
		this._setDetailContent();
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
	_eventLayoutDetailContent : function()
	{
		if (!this.insDetail.insForm) return;
		this._setDetailContent();
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
	_eventDetailConnect : function(obj)
	{
		if (obj.flag.match(/^reload$/)) {
			this._setDetailStart();
			return;

		} else if (obj.flag == 'save') {
			if (this.insDetail.checkForm({flagType : 'common'})) return;
			var vars = this.insDetail.getFormValue();

			if (vars.Upload == '') {
				this.insDetail.showFormAttestError({flagType : 'strBlank'});
				return;
			}

			arr = vars.Upload;
			for (var i = 0; i < arr.length; i++) {
				var array = arr[i].split('.');
				var strFileType = array[array.length - 1];
				strFileType = strFileType.toLowerCase();
				if (strFileType != 'csv') {
					this.insDetail.showFormAttestError({flagType : 'strFileType'});
					return;
				}
			}

			this._varsDetailConnect = obj;
			this.insRoot.setUpload({
				id         : this._varsIdUpload,
				insClass   : this,
				strFunc    : 'eventDetailUpload',
				eleLoading : this.insDetail.insUnder.eleFormat.header.down('.codeLibBaseMarginRightFive', 0)
			});
			this.insDetail.insForm.eleForm.submit();

			return;
		}
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

	_varsDetailConnect : null,
	/**
	 *
	*/
	_eventDetailConnectSuccess : function(obj)
	{
		if (obj.json.flag == 1) {
			if (this._varsDetailConnect.flag == 'save') {
				var strHtml = '';
				var flagImport = 0;
				var flagRetry = 0;
				arr = obj.json.data;
				for (var i = 0; i < arr.length; i++) {
					var tmplstr = '';
					if (arr[i].flagConvertError) {
						tmplstr = this.vars.varsItem.varsComment.strConvertError;

					} else {
						tmplstr = this.vars.varsItem.varsComment.strStatusLog;
						if (arr[i].replaceAllImport > 0) {
							flagImport = 1;
						}
						if (arr[i].replaceAllNone > 0) {
							flagRetry = 1;
						}
					}
					var strHtmlItem = tmplstr.interpolate(arr[i]);
					strHtml += strHtmlItem + '<br>';
				}
				this._setDetailEnd({
					strHtml   : strHtml,
					flagRetry : flagRetry
				});
				if (flagImport) {
					this.insCurrent.eventImportLog();
				}
			}

		} else if (obj.json.flag == 40) {
			alert(this.insRoot.vars.varsSystem.str.oldData);
			if (!this.insWindow.vars.flagHideNow) this.insWindow.updateHide({ flagEffect : 1 });

		} else if (obj.json.flag == 10) {
			this.insDetail.showBtnBottom();

		} else if (obj.json.flag == 8) {
			alert(this.insRoot.vars.varsSystem.str.oldData);

		} else {
			var str = (obj.json.data)? obj.json.data : '';
			if (this.insRoot.vars.varsSystem.str[obj.json.flag]) {
				str = this.insRoot.vars.varsSystem.str[obj.json.flag];
			}
			this.insDetail.showFormAttestError({flagType : obj.json.flag, str : str});
		}
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