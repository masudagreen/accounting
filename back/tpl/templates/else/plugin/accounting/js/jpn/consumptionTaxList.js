{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_ConsumptionTaxList = Class.create(Code_Lib_ExtPortal,
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
		this._iniListener();
		this._iniPopup();
		this._iniLayout();
		this._iniNavi();
		this._iniDetail();

	},

	/**
	 * Listener
	*/
	insListener : null,
	_iniListener : function()
	{
		this.insListener = new Code_Lib_Listener();
		this._varsListener = [];
	},

	/**
	 *
	*/
	_varsListener : null,
	_setListener : function(obj)
	{
		var data = {ins : obj.ins};
		this._varsListener.push(data);
	},

	/**
	 *
	*/
	stopListener : function()
	{
		this.insListener.stop();
		this._stopListenerChild({arr : this._varsListener});
		if (this.insPage) this.insPage.stopListener();
		this._resetListener();
	},

	/**
	 *
	*/
	_stopListenerChild : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			obj.arr[i].ins.insListener.stop();
		}
	},

	/**
	 *
	*/
	_resetListener : function()
	{
		this._varsListener = [];
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
	_idLog : 'Log',
	_varsAutoData : {},
	_checkAutoSearch : function(obj)
	{
		var insEscape = new Code_Lib_Escape();

		this._varsAutoData = {
			flag       : 'consumptionTaxList',
			flagDetail : 1,
			vars       : {
				stampStart            : this.vars.varsFlag.stampStart,
				stampEnd              : this.vars.varsFlag.stampEnd,
				numRateConsumptionTax : this.vars.varsFlag.numRateConsumptionTax,
				idDepartment          : obj.vars.vars.idDepartment,
				flagConsumptionTax    : obj.vars.vars.flagTax,
				idAccountTitle        : obj.vars.vars.idAccountTitle,
				flagDebit             : obj.vars.flagDebit
			}
		};

		var varsLogData = this.insTop.checkChildData({idTarget : this._idLog});
		if (!varsLogData) {
			var idTarget = insEscape.strLowCapitalize({data : this._idLog});
			this.insTop.iniAutoBoot({
				idTarget     : idTarget + 'Window',
				insBack      : this,
				strBackFunc  : 'eventAutoSearch'
			});

		} else {
			if (varsLogData.insWindow.vars.flagHideNow) {
				varsLogData.insWindow.updateHide({ flagEffect : 1 });
			} else {
				varsLogData.insWindow.setZIndex();
			}
			this.eventAutoSearch();
		}
	},

	eventAutoSearch : function()
	{
		var varsLogData = this.insTop.checkChildData({idTarget : this._idLog});
		varsLogData.insClass.bootAutoSearchOver(this._varsAutoData);
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
					insCurrent._eventListConnect({flag : obj.vars.id, flagType : insCurrent.insList.vars.varsStatus.flagReloadNow});

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

				} else if (obj.vars.id == 'Reload') {
					return insCurrent.insDetail.vars.varsStatus.flagReloadNow;
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

			} else if (obj.from == 'eventRemove-detail') {
				insCurrent._eventRemoveNaviContent();
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
		this._setNaviContent();
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
	_varsNaviContent : {num : 0},
	_setNaviContent : function()
	{
		this._varsNaviContent.num = 0;
		this._iniNaviFormCalender();
	},

	/**
	 *
	*/
	_varsNaviFormCalender : [],
	_iniNaviFormCalender : function()
	{
		this._varsNaviFormCalender = [];
		this._setNaviFormCalender({arr : this.insNavi.insForm.vars.varsDetail});
	},

	/**
	 *
	*/
	_setNaviFormCalender : function(obj)
	{
		var num = 0;

		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormCalender) continue;
			var insCalender = new Code_Lib_CalenderFormNavi({
				eleInsert  : $(this.insNavi.insForm.idSelf + obj.arr[i].id),
				insRoot    : this.insRoot,
				insCurrent : this,
				idSelf     : this.idSelf + 'Calender' + obj.arr[i].id,
				allot      : this._getNaviFormCalenderAllot(),
				vars       : obj.arr[i].varsFormCalender
			});

			this._varsNaviFormCalender.push({
				id          : obj.arr[i].id,
				insCalender : insCalender
			});
			num++;
		}

	},

	/**
	 *
	*/
	_getNaviFormCalenderAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == '_mousedownDate') {

			}
		};

		return allot;
	},

	/**
	 *
	*/
	_eventRemoveNaviFormCalender : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormCalender) continue;
			this._varsNaviFormCalender[num].insCalender.removeWrap();
			num++;
		}
	},

	/**
	 *
	*/
	_eventRemoveNaviContent : function()
	{
		if (!this.insNavi.insForm) return;
		this._eventRemoveNaviFormCalender({arr : this.insNavi.insForm.vars.varsDetail});
	},

	/**
	 *
	*/
	_eventNaviConnect : function(obj)
	{
		if (obj.flag == 'search') {
			if (this.insNavi.checkForm({flagType : 'common'})) return;

			var insEscape = new Code_Lib_Escape();
			var vars = this.insNavi.getFormValue();

			var stampStart = insEscape.toStampFromTerm({
				data        : vars.StampStart,
				insTimeZone : this.insRoot.insTimeZone
			});

			var stampEnd = insEscape.toStampFromTerm({
				data        : vars.StampEnd,
				insTimeZone : this.insRoot.insTimeZone
			});

			if (stampStart > stampEnd) {
				this.insNavi.showFormAttestError({flagType : 'common'});
				return;
			}

			if (!(this.vars.varsStampTerm.stampMin <= stampStart && stampStart <= this.vars.varsStampTerm.stampMax)) {
				this.insNavi.showFormAttestError({flagType : 'termStart'});
				return;
			}

			if (!(this.vars.varsStampTerm.stampMin <= stampEnd && stampEnd <= this.vars.varsStampTerm.stampMax)) {
				this.insNavi.showFormAttestError({flagType : 'termEnd'});
				return;
			}

			this._eventValue({
				vars     : this.insNavi.getFormValue(),
				idTarget : ''
			});
			this._eventSearch({
				numLotNow : 0,
				ph : {
					arrWhere : {},
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
	_eventNaviConnectSuccess : function(obj)
	{
		if (obj.json.flag == 1) {
			if (this._varsNaviConnect.flag == 'search') {
				this.vars.portal.varsDetail.varsDetail = obj.json.data.varsDetail;
				this.vars.varsFlag = obj.json.data.varsFlag;
				this._setDetailContent();
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

	_iniDetailSheet : function()
	{
		var insEscape = new Code_Lib_Escape();

		var vars = {
			idSelf  : this.idSelf
		};
		var tmplstr = this.vars.portal.varsDetail.varsDetail.varsHtml;
		var data = tmplstr.interpolate(vars);
		this.insDetail.insUnder.eleFormat.body.innerHTML = data;
		if (this.vars.flagAuthorityLog) {
			var arrColumn = ['NumBodyDebit', 'NumTaxDebit', 'NumBodyCredit', 'NumTaxCredit'];
			var arr = this.vars.portal.varsDetail.varsDetail.varsList;

			for (var i = 0; i < arr.length; i++) {

				for (var j = 0; j < arrColumn.length; j++) {
					var insBtn = new Code_Lib_Btn();
					var id = this.idSelf + arrColumn[j] + arr[i].id;
					var idColumn = insEscape.strLowCapitalize({data : arrColumn[j]});

					if (arr[i][idColumn] == '0') {
						continue;
					}
					arr[i].idColumn = idColumn;
					$(id + 'Wrap').innerHTML = '';
					insBtn.iniBtnTextTarget({
						eleInsert  : $(id + 'Wrap'),
						id         : id,
						strFunc    : 'checkTextBtn',
						strTitle   : arr[i][idColumn],
						insCurrent : this,
						vars       : {
							vars      : arr[i],
							flagDebit : idColumn.match(/Debit$/)? 1 : 0
						}
					});
					this._setListener({ins : insBtn});
				}
			}
		}
	},

	/**
	 *
	*/
	checkTextBtn : function(obj)
	{
		if (this.vars.flagAuthorityLog) {
			this._checkAutoSearch({vars : obj.vars.vars});
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
		this.stopListener();
	},

	/**
	 *
	*/
	_eventDetailConnect : function(obj)
	{
		if (obj.flag == 'reload' || obj.flag == 'output') {
			if (this.insNavi.checkForm({flagType : 'common'})) return;

			var vars = this.insNavi.getFormValue();
			var insEscape = new Code_Lib_Escape();

			this._eventValue({
				vars     : vars,
				idTarget : ''
			});

			var stampStart = insEscape.toStampFromTerm({
				data        : vars.StampStart,
				insTimeZone : this.insRoot.insTimeZone
			});

			var stampEnd = insEscape.toStampFromTerm({
				data        : vars.StampEnd,
				insTimeZone : this.insRoot.insTimeZone
			});

			if (stampStart > stampEnd) {
				this.insNavi.showFormAttestError({flagType : 'common'});
				return;
			}

			if (!(this.vars.varsStampTerm.stampMin <= stampStart && stampStart <= this.vars.varsStampTerm.stampMax)) {
				this.insNavi.showFormAttestError({flagType : 'termStart'});
				return;
			}

			if (!(this.vars.varsStampTerm.stampMin <= stampEnd && stampEnd <= this.vars.varsStampTerm.stampMax)) {
				this.insNavi.showFormAttestError({flagType : 'termEnd'});
				return;
			}
		}

		this._varsDetailConnect = obj;
		this._sendDetailConnect();
	},

	/**
	 *
	*/
	_varsDetailConnect : null,
	_sendDetailConnect : function(obj)
	{
		var strExt = this.strExt;
		var strClass = this.strClass;
		var idModule = this.idModule;
		var strChild = this.strChild;
		var jsonSearch = Object.toJSON(this._varsSearch);
		var jsonValue = Object.toJSON(this._varsValue);
		var arrayKey = [], arrayValue = [];
		var jsonStamp = {};

		var strFunc = 'Detail' + this._varsDetailConnect.flag.capitalize();
		jsonStamp = this._getJsonStamp({strFunc : strFunc});
		arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonValue', 'jsonSearch'];
		arrayValue = [strClass, idModule, strExt, strChild, strFunc, 'slave', jsonStamp, jsonValue, jsonSearch];

		if (this._varsDetailConnect.flag == 'output') {
			this.insRoot.setOutput({
				querysKey   : arrayKey,
				querysValue : arrayValue,
			});
			this.insLayout.updateTool({flagLock : 0, from : 'detail', idTarget : 'Output'});

		} else {
			this.insLayout.updateTool({flagLock : 1, from : 'detail', idTarget : 'Reload'});
			this.insRoot.insRequest.set({
				flagLock        : 0,
				numZIndex       : this.insRoot.getZIndex(),
				insCurrent      : this,
				flagEscape      : 1,
				path            : this.insRoot.vars.varsSystem.path.post,
				querysKey       : arrayKey,
				querysValue     : arrayValue,
				functionSuccess : '_sendDetailConnectSuccess',
				functionFail    : '_sendDetailConnectFail',
				eleLoadStatus   : this.insDetail.insUnder.eleFormat.header.down('.codeLibBaseMarginRightFive', 0)
			});
		}


	},


	/**
	 *
	*/
	_eventDetailConnectSuccess : function(obj)
	{
		if (obj.json.flag == 1){

			if (this._varsDetailConnect.flag == 'reload') {

				this.insLayout.updateTool({flagLock : 0, from : 'detail', idTarget : 'Reload'});
				this.vars.portal.varsDetail.varsDetail = obj.json.data.varsDetail;
				this._setDetailContent();
			}

		} else if (obj.json.flag == 10) {
			if (this._varsDetailConnect.flag == 'reload') {
				this.insLayout.updateTool({flagLock : 0, from : 'detail', idTarget : 'Reload'});
			}

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