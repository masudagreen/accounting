{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_LogImportList = Class.create(Code_Lib_ExtPortal,
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

	_flagAutoSearchOver : '',
	_varsAutoSearchOver : {},
	bootAutoSearchOver : function(obj)
	{
		if (obj.flag == 'loopAddAccountTitle') {
			var vars = this.insDetail.getFormValue();

			var arr = this._varsStrTitleList;
			var arrayNew = [];
			for (var i = 0; i < arr.length; i++) {
				if (arr[i] == vars.StrTitle) {
					continue;
				}
				arrayNew.push(arr[i]);
			}
			if (!arrayNew.length) {
				this._setDetailMake({flag : 'strAddAccountTitle'});

			} else {
				this._setDetailAccountTitle({vars : arrayNew});
			}

			var varsData = this.insTop.checkChildData({idTarget : 'Log'});
			varsData.insClass.bootAutoSearchOver({flag : 'updateVarsRule'});

		} else if (obj.flag == 'showAddAccountTitleBtn') {
			this.insDetail.showBtnBottom();

		} else if (obj.flag == 'showCashDefer') {
			var varsData = this.insTop.checkChildData({idTarget : 'Log'});
			varsData.insClass.bootAutoSearchOver({flag : obj.flag});

		} else if (obj.flag == 'updateVarsRuleImport') {
			var varsData = this.insTop.checkChildData({idTarget : 'Log'});
			varsData.insClass.bootAutoSearchOver({flag : obj.flag, vars : obj.vars});
		}
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
					insCurrent._iniChild({
						strTitleParent  : insCurrent.insWindow.vars.strTitle,
						strTitleChild   : insCurrent.vars.child.varsTitle[obj.vars.id],
						strExt          : obj.vars.id,
						flagMenuShowUse : 1,
						strChild        : '',
						strClass        : insCurrent.strClass,
						idModule        : insCurrent.idModule
					});
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
				var strChild = 'ImportList';
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

				} else if (obj.vars.vars.vars.flagAccountTitle) {
					insCurrent._eventDetailConnect({flag : 'accountTitle'});

				} else if (obj.vars.vars.vars.flagDefer) {
					insCurrent.insCurrent.bootAutoSearchOver({flag : 'showCashDefer'});
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

	_setDetailEnd : function()
	{
		var objData = {
			strTitle : this.vars.portal.varsDetail.varsEnd.strTitle,
			strClass : this.vars.portal.varsDetail.varsEnd.strClass,
			vars     : {
				varsDetail : this.vars.portal.varsDetail.varsEnd.varsDetail,
				varsBtn    : this.vars.portal.varsDetail.varsEnd.varsBtn,
				varsEdit   : {},
				vars       : {}
			}
		};

		this.insDetail.eventList(objData);
	},

	_setDetailEndDefer : function()
	{
		var objData = {
			strTitle : this.vars.portal.varsDetail.varsEndDefer.strTitle,
			strClass : this.vars.portal.varsDetail.varsEndDefer.strClass,
			vars     : {
				varsDetail : this.vars.portal.varsDetail.varsEndDefer.varsDetail,
				varsBtn    : this.vars.portal.varsDetail.varsEndDefer.varsBtn,
				varsEdit   : {},
				vars       : {}
			}
		};

		this.insDetail.eventList(objData);
	},

	_setDetailEndDeferReject : function()
	{
		var objData = {
			strTitle : this.vars.portal.varsDetail.varsEndDeferReject.strTitle,
			strClass : this.vars.portal.varsDetail.varsEndDeferReject.strClass,
			vars     : {
				varsDetail : this.vars.portal.varsDetail.varsEndDeferReject.varsDetail,
				varsBtn    : this.vars.portal.varsDetail.varsEndDeferReject.varsBtn,
				varsEdit   : {},
				vars       : {}
			}
		};

		this.insDetail.eventList(objData);
	},

	_varsStrTitleList : null,
	_setDetailAccountTitle : function(obj)
	{
		this._varsStrTitleList = [];
		this._varsStrTitleList = obj.vars;
		var objDetail = (Object.toJSON(this.vars.portal.varsDetail.varsAccountTitle.templateDetail)).evalJSON();
		var objData = {
			strTitle : this.vars.portal.varsDetail.varsAccountTitle.strTitle,
			strClass : this.vars.portal.varsDetail.varsAccountTitle.strClass,
			vars     : {
				varsDetail : this._updateDetailAccountTitleChild({
					arr  : objDetail,
					vars : obj.vars
				}),
				varsBtn    : this.vars.portal.varsDetail.varsAccountTitle.varsBtn,
				varsEdit   : {},
				vars       : {}
			}
		};

		this.insDetail.eventList(objData);
	},

	/**
	 *
	*/
	_updateDetailAccountTitleChild : function(obj)
	{
		var insDisplay = new Code_Lib_TimeDisplay();
		var insEscape = new Code_Lib_Escape();
		var arrayNew = [];
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'StrTitle') {
				var arrayOption = [];
				for (var j = 0; j < obj.vars.length; j++) {
					var row = {};
					row.strTitle = obj.vars[j];
					row.value = obj.vars[j];
					arrayOption.push(row);
				}
				obj.arr[i].arrayOption = arrayOption;
				obj.arr[i].value = this._getOptionFirstValue({arr : obj.arr[i].arrayOption});

			} else if (obj.arr[i].id == 'IdAccountTitle') {
				obj.arr[i].value = this._getOptionFirstValue({arr : obj.arr[i].arrayOption});
			}
			arrayNew.push(obj.arr[i]);
		}

		return arrayNew;
	},

	_setDetailError : function(obj)
	{
		var objDetail = (Object.toJSON(this.vars.portal.varsDetail.varsError.templateDetail)).evalJSON();

		var objData = {
			strTitle : this.vars.portal.varsDetail.varsError.strTitle,
			strClass : this.vars.portal.varsDetail.varsError.strClass,
			vars     : {
				varsDetail : this._updateDetailErrorChild({
					arr  : objDetail,
					flag : obj.flag,
					vars : obj.vars
				}),
				varsBtn    : this.vars.portal.varsDetail.varsError.varsBtn,
				varsEdit   : {},
				vars       : {}
			}
		};

		this.insDetail.eventList(objData);
	},

	/**
	 *
	*/
	_updateDetailErrorChild : function(obj)
	{
		var arrayNew = [];
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'End') {
				obj.arr[i].strComment = obj.arr[i].varsTmpl[obj.flag];
				if (obj.flag == 'strTitleSubAccountTitle' || obj.flag == 'strTitleDepartment') {
					for (var j = 0; j < obj.vars.length; j++) {
						var strTitle = obj.arr[i].varsTmpl.strTitle;
						obj.vars[j] = strTitle.replace(RegExp("<%replace%>", "g"), obj.vars[j]);
					}

					var str = obj.vars.join(' ');
					obj.arr[i].strComment = obj.arr[i].strComment.replace(RegExp("<%replace%>", "g"), str);
				}
			}
			arrayNew.push(obj.arr[i]);
		}

		return arrayNew;
	},

	_setDetailMake : function(obj)
	{
		var objDetail = (Object.toJSON(this.vars.portal.varsDetail.varsMake.templateDetail)).evalJSON();

		var objData = {
			strTitle : this.vars.portal.varsDetail.varsMake.strTitle,
			strClass : this.vars.portal.varsDetail.varsMake.strClass,
			vars     : {
				varsDetail : this._updateDetailMakeChild({
					arr  : objDetail,
					flag : obj.flag,
					vars : obj.vars
				}),
				varsBtn    : this.vars.portal.varsDetail.varsMake.varsBtn,
				varsEdit   : {},
				vars       : {}
			}
		};

		this.insDetail.eventList(objData);
	},

	/**
	 *
	*/
	_updateDetailMakeChild : function(obj)
	{
		var arrayNew = [];
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'End') {
				obj.arr[i].strComment = obj.arr[i].varsTmpl[obj.flag];
			}
			arrayNew.push(obj.arr[i]);
		}

		return arrayNew;
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

			var array = vars.Upload.split('/');
			array = array[array.length - 1].split('.');
			var strFileType = array[array.length - 1];
			strFileType = strFileType.toLowerCase();

			if (vars.Upload == '') {
				this.insDetail.showFormAttestError({flagType : 'strBlank'});
				return;
			}

			if (strFileType != 'csv') {
				this.insDetail.showFormAttestError({flagType : 'strFileType'});
				return;
			}

			this._varsDetailConnect = obj;
			this.insRoot.setUpload({
				id         : this._varsIdUpload,
				insClass   : this,
				strFunc    : 'eventDetailUpload',
				eleLoading : this.insDetail.insUnder.eleFormat.header.down('.codeLibBaseMarginRightFive', 0)
			});
			this.insDetail.insForm.eleForm.submit();

		} else if (obj.flag == 'accountTitle') {
			if (this.insDetail.checkForm({flagType : 'common'})) return;
			var vars = this.insDetail.getFormValue();
			var arrTemp = vars.IdAccountTitle.split(',');
			var flagFS = arrTemp[1];
			var idTargetDir = arrTemp[0];
			var varsValue = {
				idTargetDir : idTargetDir,
				flagFS      : flagFS,
				strTitle    : vars.StrTitle,
			};
			this._checkAutoSearch({flag : obj.flag, vars : varsValue});
		}

		/*this.insDetail.showBtnBottom();*/
	},

	_idWindowAccountTitle : 'AccountTitle',
	_varsAutoData : {},
	_checkAutoSearch : function(obj)
	{
		var insEscape = new Code_Lib_Escape();
		this._varsAutoData = {
			flag : 'addAccountTitle',
			vars : obj.vars
		};

		var varsData = this.insTop.checkChildData({idTarget : this._idWindowAccountTitle});
		if (!varsData) {
			var idTarget = insEscape.strLowCapitalize({data : this._idWindowAccountTitle});
			this.insTop.iniAutoBoot({
				idTarget       : idTarget + 'Window',
				insBack        : this,
				flagHideWindow : 1,
				strBackFunc    : 'eventAutoSearch'
			});

		} else {
			varsData.insClass.bootAutoSearchOver(this._varsAutoData);
		}
	},

	eventAutoSearch : function()
	{
		var varsData = this.insTop.checkChildData({idTarget : this._idWindowAccountTitle});
		varsData.insClass.bootAutoSearchOver(this._varsAutoData);
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
				this._setDetailEnd();
				this.bootAutoSearchOver({flag : 'updateVarsRuleImport', vars : obj.json.data});
				this.insCurrent.eventImportLog();
			}

		} else if (obj.json.flag == 'defer') {
			if (this._varsDetailConnect.flag == 'save') {
				this._setDetailEndDefer();
				this.bootAutoSearchOver({flag : 'updateVarsRuleImport', vars : obj.json.data});
				this.insCurrent.eventImportLog();
			}

		} else if (obj.json.flag == 'deferReject') {
			if (this._varsDetailConnect.flag == 'save') {
				this._setDetailEndDeferReject();
				this.bootAutoSearchOver({flag : 'updateVarsRuleImport', vars : obj.json.data});
				this.insCurrent.eventImportLog();
			}

		} else if (obj.json.flag == 40) {
			alert(this.insRoot.vars.varsSystem.str.oldData);
			if (!this.insWindow.vars.flagHideNow) this.insWindow.updateHide({ flagEffect : 1 });

		} else if (obj.json.flag == 10) {
			this.insDetail.showBtnBottom();

		} else if (obj.json.flag == 8) {
			alert(this.insRoot.vars.varsSystem.str.oldData);

		} else if (obj.json.flag == 'strAccountTitle') {
			if (!obj.json.data) {
				this._setDetailError({vars : obj.json.data, flag : obj.json.flag});

			} else {
				this._setDetailAccountTitle({vars : obj.json.data});
			}

		} else if (obj.json.flag == 'strSubAccountTitle'
			|| obj.json.flag == 'strTitleSubAccountTitle'
			|| obj.json.flag == 'strDepartment'
			|| obj.json.flag == 'strTitleDepartment'
		) {
			this._setDetailError({vars : obj.json.data, flag : obj.json.flag});

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