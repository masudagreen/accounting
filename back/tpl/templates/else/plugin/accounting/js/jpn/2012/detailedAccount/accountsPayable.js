{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_DetailedAccountAccountsPayable = Class.create(Code_Lib_ExtPortal,
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
					insCurrent.insNavi.eventTool({idTarget : insCurrent.insNavi.vars.varsStatus.flagNow, flagLoop:1});

				} else if (obj.vars.id == 'Reload') {
					insCurrent._preEventLayout({flag : 'reset'});
					insCurrent._eventNaviConnect({vars : obj.vars, flag : 'reload'});

				} else if (obj.vars.id == 'Preference') {
					insCurrent._iniChild({
						strTitleParent : insCurrent.insWindow.vars.strTitle,
						strTitleChild  : insCurrent.vars.child.varsTitle[obj.vars.id],
						strExt         : insCurrent.strExt + obj.vars.id,
						strChild       : '',
						strClass       : insCurrent.strClass,
						idModule       : insCurrent.idModule
					});
				}

			} else if (obj.from == 'navi-_mousedownMenu') {
				return insCurrent.insNavi.vars.varsStatus.flagNow;

			} else if (obj.from == 'navi-_mousedownLine') {
				insCurrent.insNavi.eventTool({idTarget : obj.vars});

			} else if (obj.from == 'list-_mousedownNavi') {
				if (obj.vars.id == 'Switch') {
					insCurrent.insList.eventTool({idTarget : insCurrent.insList.vars.varsStatus.flagNow, flagLoop:1});

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
	_iniNavi : function()
	{
		this._setNavi();
		this._setNaviStart();
		this._eventValue({
			vars     : this.insNavi.getFormValue(),
			idTarget : ''
		});
	},

	/**
	 *
	*/
	insNavi : null,
	_setNavi : function()
	{
		this.insNavi = new Code_Lib_ControlDetail({
			insUnder   : this.insLayout.insNaviUnder,
			insTool    : this.insLayout.insNaviTool,
			insRoot    : this.insRoot,
			insCurrent : this,
			idSelf     : this.idSelf + 'Navi',
			allot      : this._getNaviAllot(),
			vars       : this.vars.portal.varsNavi
		});
	},

	/**
	 *
	*/
	_getNaviAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == 'form-preEventLayout') insCurrent._preEventLayoutNaviContent();
			else if (obj.from == 'form-eventLayout') insCurrent._eventLayoutNaviContent();
			else if (obj.from == 'form-eventBtnBottom') {
				insCurrent._eventNaviConnect({flag : obj.vars.vars.vars.idTarget});
			}
		};

		return allot;
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
	_setNaviStart : function()
	{
		var tmplDetail = (Object.toJSON(this.vars.portal.varsNavi.templateDetail)).evalJSON();
		var tmplBtn = (Object.toJSON(this.vars.portal.varsNavi.varsBtn)).evalJSON();
		this.insNavi.eventList({
			flagMoveUse : 0,
			strTitle    : this.vars.portal.varsNavi.varsStart.strTitle,
			strClass    : this.vars.portal.varsNavi.varsStart.strClass,
			vars        : {
				varsDetail : tmplDetail,
				varsBtn    : tmplBtn,
				varsEdit   : this.vars.portal.varsNavi.varsStart.varsEdit,
				vars       : {}
			}
		});
		this._setNaviContent();
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
	_eventNaviConnect : function(obj)
	{
		if (obj.flag == 'search' || obj.flag == 'reload') {
			if (this.insNavi.checkForm({flagType : 'common'})) return;
			this._eventValue({
				vars     : this.insNavi.getFormValue(),
				idTarget : ''
			});
			this._eventSearch({
				numLotNow : 0,
				ph : {
					arrWhere : [],
					arrOrder : {}
				}
			});
		}

		this._varsNaviConnect = obj;
		this._sendNaviConnect();
	},

	/**
	 *
	*/
	_varsNaviConnect : null,
	_sendNaviConnect : function(obj)
	{
		var strExt = this.strExt;
		var strClass = this.strClass;
		var idModule = this.idModule;
		var strChild = this.strChild;
		var jsonSearch = Object.toJSON(this._varsSearch);
		var jsonValue = Object.toJSON(this._varsValue);
		var arrayKey = [], arrayValue = [];
		var jsonStamp = {};

		var strFunc = 'Navi' + this._varsNaviConnect.flag.capitalize();
		jsonStamp = this._getJsonStamp({strFunc : strFunc});
		arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonValue', 'jsonSearch'];
		arrayValue = [strClass, idModule, strExt, strChild, strFunc, 'slave', jsonStamp, jsonValue, jsonSearch];

		if (this._varsNaviConnect.flag == 'reload') {
			this.insLayout.updateTool({flagLock : 1, from : 'navi', idTarget : 'Reload'});
		}

		this.insRoot.insRequest.set({
			flagLock        : 0,
			numZIndex       : this.insRoot.getZIndex(),
			insCurrent      : this,
			flagEscape      : 1,
			path            : this.insRoot.vars.varsSystem.path.post,
			querysKey       : arrayKey,
			querysValue     : arrayValue,
			functionSuccess : '_sendNaviConnectSuccess',
			functionFail    : '_sendNaviConnectFail',
			eleLoadStatus   : this.insNavi.insUnder.eleFormat.header.down('.codeLibBaseMarginRightFive', 0)
		});
	},


	/**
	 *
	*/
	_sendNaviConnectSuccess : function(obj)
	{
		if (obj.response.responseText.isJSON()) {
			var json = obj.response.responseText.evalJSON();
			if (json.stamp) {
				if (json.stamp.id) this._varsStamp[json.stamp.id] = json.stamp.stamp;
			}
			this.insNavi.showBtnBottom();
			if (json.flag) {
				if (json.numNews) this.insRoot.iniPopup({flag : 'news', numNews : json.numNews});
				this._eventNaviConnectSuccess({json : json});
			}
			else if (json.flag == 0) alert(this.insRoot.vars.varsSystem.str.errorSession);
		}
		else alert(this.insRoot.vars.varsSystem.str.errorRequest);
	},

	/**
	 *
	*/
	eventNaviConnectSuccessVars : function(obj)
	{
		this.vars.portal.varsNavi.templateDetail = obj.json.data.varsNavi;
		this._updateVarsNaviVarsDetail({
			arr  : this.vars.portal.varsNavi.templateDetail,
			vars : this.insNavi.getFormValue()
		});
		this._setNaviStart();
		this.vars.portal.varsDetail = obj.json.data.varsDetail;
		this.vars.varsFlag = obj.json.data.varsFlag;
		this.vars.varsItem.arrAccountTitle = obj.json.data.arrAccountTitle;
		this.vars.varsItem.arrSubAccountTitle = obj.json.data.arrSubAccountTitle;
		this.vars.varsItem.varsPreference = obj.json.data.varsPreference;
		this.vars.varsItem.varsList = obj.json.data.varsList;
		this.vars.varsItem.varsIni = obj.json.data.varsIni;
		this.vars.varsItem.flagBtnCalc = obj.json.data.flagBtnCalc;
		this._iniDetail();
	},


	/**
	 *
	*/
	_eventNaviConnectSuccess : function(obj)
	{
		if (obj.json.flag == 1) {
			if (this._varsNaviConnect.flag == 'search' || this._varsNaviConnect.flag == 'reload') {
				if (this._varsNaviConnect.flag == 'reload') {
					this.insLayout.updateTool({flagLock : 0, from : 'navi', idTarget : 'Reload'});
				}
				this.eventNaviConnectSuccessVars(obj);
			}

		} else if (obj.json.flag == 10) {
			this.insNavi.showBtnBottom();

		} else {
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
	_updateVarsNaviVarsDetail : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].arrayOption.length) {
				var arr = obj.arr[i].arrayOption;
				for (var j = 0; j < arr.length; j++) {
					if (arr[j].value == obj.vars[obj.arr[i].id]) {
						obj.arr[i].value = obj.vars[obj.arr[i].id];
						break;
					}
				}
			}
		}
	},

	/**
	 *
	*/
	_iniDetail : function()
	{
		this._extDetail();
		this._setDetailContent();
	},

	/**
	 *
	*/
	_setDetailContent : function()
	{
		this._iniDetailSheet();
	},

	/**
	 *
	*/
	_iniDetailSheet : function()
	{
		var vars = {
			idSelf  : this.idSelf
		};
		this._setDetailSheetVars({
			arr  : this.vars.varsItem.varsList,
			vars : vars
		});

		var strHtml = (Object.toJSON(this.vars.portal.varsDetail.varsDetail.varsHtml)).evalJSON();
		var strHtmlUpdate = strHtml.interpolate(vars);
		this.insDetail.insUnder.eleFormat.body.innerHTML = strHtmlUpdate;
	},

	/**
	 *
	*/
	_setDetailSheetVars : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			obj.vars[obj.arr[i].idTarget] = obj.arr[i].valueStr;
		}
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
		if (obj.flag == 'reload') {
			if (this.insNavi.checkForm({flagType : 'common'})) return;
			this._eventValue({
				vars     : this.insNavi.getFormValue(),
				idTarget : ''
			});

		} else if (obj.flag == 'Print' || obj.flag == 'output') {
			var vars = this.insNavi.getFormValue();
			vars.FlagType = obj.flagType;
			this._eventValue({
				vars     : vars,
				idTarget : ''
			});
		}

		this._varsDetailConnect = obj;
		this._sendDetailConnect();
	},

	/**
	 *
	*/
	_getDetailChildVars : function(obj)
	{
		var insDisplay = new Code_Lib_TimeDisplay();
		var arrayNew = [];
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'DummyPage') {
				if (this.vars.varsFlag.flagMenu.match(/^detail/)) {
					var strExplain = obj.arr[i].varsTmpl.strNormal;
					obj.arr[i].strCommentTitle = strExplain.replace(RegExp("<%replace%>", "g"), this.vars.varsFlag.numPage);
					arrayNew.push(obj.arr[i]);
				}

			} else if (obj.arr[i].id == 'JsonData') {
				arrayNew.push(obj.arr[i]);
			}
		}

		return arrayNew;
	},

	/**
	 *
	*/
	_eventDetailConnectSuccess : function(obj)
	{
		if (obj.json.flag == 1){
			if (this._varsDetailConnect.flag == 'reload') {
				this.insLayout.updateTool({flagLock : 0, from : 'detail', idTarget : 'Reload'});
				this.eventNaviConnectSuccessVars(obj);

			} else if (this._varsDetailConnect.flag == 'Print' ) {
				this.insRoot.setPrint({data : obj.json.data, insCurrent : this, strFunc : 'eventDetailConnectSuccessPrint'});
			}

		} else if (obj.json.flag == 10) {
			if (this._varsDetailConnect.flag == 'reload') {
				this.insLayout.updateTool({flagLock : 0, from : 'detail', idTarget : 'Reload'});
			}

		} else if (obj.json.flag == 40) {
			this._eventNaviConnect({flag : 'search'});

		} else {
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