{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_FinancialStatementMulti = Class.create(Code_Lib_ExtPortal,
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
					insCurrent._eventNaviConnect({vars : obj.vars, flag : insCurrent.insNavi.vars.varsStatus.flagNow + '-reload'});

				} else if (obj.vars.id == 'Preference') {
					insCurrent._iniChild({
						strTitleParent  : insCurrent.insWindow.vars.strTitle,
						strTitleChild   : insCurrent.vars.child.varsTitle[obj.vars.id],
						strExt          : insCurrent.strExt + 'AccountTitle',
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
					insCurrent.insList.eventTool({idTarget : insCurrent.insList.vars.varsStatus.flagNow, flagLoop:1});

				} else if (obj.vars.id == 'Reload') {
					insCurrent._preEventLayout({flag : 'reset'});
					insCurrent._eventListConnect({flag : obj.vars.id});

				} else if (obj.vars.id == 'Output' || obj.vars.id == 'Print') {
					insCurrent._eventListConnect({flag : obj.vars.id});
					/*
					insCurrent._eventListConnect({flag : obj.vars.id, flagType : obj.vars.varsContext.varsStatus.flagNow});
					*/
				}

			} else if (obj.from == 'list-_mousedownMenu') {
				if (obj.vars.id == 'Switch') {
					return insCurrent.insList.vars.varsStatus.flagNow;

				} else if (obj.vars.id == 'Output') {
					return insCurrent.insList.vars.varsStatus.flagOutputNow;
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
		if (obj.flag == 'search') {
			if (this.insNavi.checkForm({flagType : 'common'})) return;
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
				this.vars.varsCollect = obj.json.data.varsCollect;
				this.vars.varsFlag = obj.json.data.varsFlag;
				this.vars.portal.varsList.templateDetail = obj.json.data.varsDetail;
				this._setListStart();
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
	_iniList : function()
	{
		this._setList();
		this._setListStart();
	},

	/**
	 *
	*/
	insList : null,
	_setList : function()
	{
		this.insList = new Code_Lib_ControlDetail({
			insUnder   : this.insLayout.insListUnder,
			insTool    : this.insLayout.insListTool,
			insRoot    : this.insRoot,
			insCurrent : this,
			idSelf     : this.idSelf + 'List',
			allot      : this._getListAllot(),
			vars       : this.vars.portal.varsList
		});
	},

	/**
	 *
	*/
	_getListAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == 'form-preEventLayout') insCurrent._preEventLayoutListContent();
			else if (obj.from == '_mousedownMove') insCurrent.insNavi.eventMove({vars : obj.vars});
			else if (obj.from == 'form-eventLayout') insCurrent._eventLayoutListContent();
			else if (obj.from == 'view-eventLayout') insCurrent._eventLayoutListContent();
			else if (obj.from == 'view-eventBtnBottom') insCurrent._eventListConnect({flag : obj.vars.vars.vars.idTarget});
			else if (obj.from == 'form-eventBtnBottom') {
				if (obj.vars.vars.vars.flagBack) {
					/*insCurrent._backDetailEnd();*/

				} else if (obj.vars.vars.vars.flagHide) {
					if (!insCurrent.insWindow.vars.flagHideNow) insCurrent.insWindow.updateHide({ flagEffect : 1 });

				} else {
					if (obj.vars.vars.vars.idTarget == 'save') {
						/*insCurrent._eventDetailConnect({flag : insCurrent.varsChild.flagType});*/
					}
				}

			}
		};

		return allot;
	},

	/**
	 *
	*/
	_eventLayoutListContent : function()
	{
		if (!this.insList.insForm) return;
		this._setListNavi();
	},

	/**
	 *
	*/
	_preEventLayoutListContent : function()
	{
		if (!this.insList.insForm) return;
		this.insList.setValue();
	},

	/**
	 *
	*/
	_setListStart : function()
	{
		var objDetail = (Object.toJSON(this.vars.portal.varsList.templateDetail)).evalJSON();
		this.insList.eventNavi({
			strTitle : this.vars.portal.varsList.varsStart.strTitle,
			strClass : this.vars.portal.varsList.varsStart.strClass,
			vars     : {
				varsDetail : this._setListNaviChild({arr : objDetail, flagStart : 1}),
				varsEdit   : this.vars.portal.varsList.varsEdit,
				varsBtn    : null,
				vars       : {}
			}
		});

		this._setListContent();

	},

	/**
	 *
	*/
	_setListNavi : function()
	{
		var vars = {};
		var flagFiscalPeriod = 'f1';
		if (this.insList) {
			if (this.insList.insForm) {
				this.insList.setValue();
				vars = this.insList.getFormValue();
				flagFiscalPeriod = vars.FlagFiscalPeriod;
			}
		}
		var objDetail = (Object.toJSON(this.vars.portal.varsList.templateDetail)).evalJSON();
		for (var i = 0; i < objDetail.length; i++) {
			if (!objDetail[i].flagHideUse) {
				continue;
			}
			objDetail[i].flagHideNow = 1;
			if (flagFiscalPeriod == 'f1') {
				if (objDetail[i].id == 'TableF1') {
					objDetail[i].flagHideNow = 0;
				}

			} else if (flagFiscalPeriod == 'f2') {
				if (objDetail[i].id == 'TableF2') {
					objDetail[i].flagHideNow = 0;
				}

			} else if (flagFiscalPeriod == 'f4') {
				if (objDetail[i].id == 'TableF4') {
					objDetail[i].flagHideNow = 0;
				}

			} else if (flagFiscalPeriod == 'month') {
				if (objDetail[i].id == 'TableMonth') {
					objDetail[i].flagHideNow = 0;
				}
			}
		}
		this.insList.eventNavi({
			strTitle : this.vars.portal.varsList.varsStart.strTitle,
			strClass : this.vars.portal.varsList.varsStart.strClass,
			vars     : {
				varsDetail : this._setListNaviChild({arr : objDetail, vars : vars}),
				varsEdit   : this.vars.portal.varsList.varsEdit,
				varsBtn    : null,
				vars       : {}
			}
		});
		this._setListContent();
	},

	/**
	 *
	*/
	_setListNaviChild : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'FlagFiscalPeriod') {
				if (obj.vars) {
					obj.arr[i].value = obj.vars.FlagFiscalPeriod;
				}

			} else if (obj.arr[i].id == 'IdAccountTitle') {
				if (parseFloat(this.vars.varsFlag.flagZero)) {
					obj.arr[i].arrayOption = this.vars.varsCollect.arrSelectTag;
				} else {
					obj.arr[i].arrayOption = this.vars.varsCollect.varsZero.arrSelectTag;
				}

				if (obj.flagStart) {
					obj.arr[i].value = this._getOptionFirstValue({arr : obj.arr[i].arrayOption});

				} else {
					if (obj.vars.IdAccountTitle == '') {
						obj.arr[i].value = this._getOptionFirstValue({arr : obj.arr[i].arrayOption});

					} else {
						obj.arr[i].value = obj.vars.IdAccountTitle;
					}
				}
			}
		}

		return obj.arr;
	},

	_varsContent : {num : 0},
	_setListContent : function()
	{
		this._varsContent.num = 0;

		this._iniListFormSelect();

		this._iniListSpace();
	},

	/**
	 *
	*/
	_iniListFormSelect : function()
	{
		this._setListFormSelect({arr : this.insList.insForm.vars.varsDetail});
	},

	/**
	 *
	*/
	_insListFormSelect : {},
	_setListFormSelect : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'FlagFiscalPeriod'
				|| obj.arr[i].id == 'IdAccountTitle'
			) {
				var str = '_getListFormSelectAllot' + obj.arr[i].id;
				var insFormSelect = new Code_Lib_FormSelect({
					eleInsert  : $(this.insList.insForm.idSelf + obj.arr[i].id),
					insRoot    : this.insRoot,
					insCurrent : this,
					idSelf     : this.idSelf + 'FormSelect' + obj.arr[i].id,
					allot      : this[str](),
					vars       : null
				});
				this._insListFormSelect[obj.arr[i].id] = insFormSelect;
			}
		}
	},

	/**
	 *
	*/
	_getListFormSelectAllotIdAccountTitle : function()
	{
		var allot = function(obj) {
			var insCurrent = obj.insCurrent;
			insCurrent._setListFormSelectValue();
		};

		return allot;
	},

	/**
	 *
	*/
	_getListFormSelectAllotFlagFiscalPeriod : function()
	{
		var allot = function(obj) {
			var insCurrent = obj.insCurrent;
			insCurrent._setListFormSelectValue();
		};

		return allot;
	},

	/**
	 *
	*/
	_setListFormSelectValue : function()
	{
		this._setListNavi();
	},

	/**
	 *
	*/
	_varsListSpace : {},
	_iniListSpace : function()
	{
		this._varsListSpace = {};
		this._setListSpace({arr : this.insList.insForm.vars.varsDetail});
	},

	/**
	 *
	*/
	_setListSpace : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsSpace) continue;
			var ele = this.insList.insForm.eleWrap.down('.codeLibFormContent', this._varsContent.num);
			this._varsContent.num++;
			if (obj.arr[i].id == 'GraphBar') {
				this._setListFormChartBar({
					vars : obj.arr[i]
				});
			}
			var insSpace = new Code_Lib_Space({
				eleScroll  : this.insList.insForm.eleInsert,
				eleInsert  : ele,
				insRoot    : this.insRoot,
				insCurrent : this,
				idSelf     : this.idSelf + 'Space' + obj.arr[i].id,
				allot      : {},
				vars       : obj.arr[i].varsSpace
			});
			this._varsListSpace[obj.arr[i].id] = insSpace;
		}
	},

	/**
	 *
	*/
	_setListFormChartBar : function(obj)
	{
		var varsCollect = this.vars.varsCollect;
		var varsOptions = (Object.toJSON(varsCollect.tmplOptionsBar)).evalJSON();

		this.insList.setValue();
		var vars = this.insList.getFormValue();
		var flagFiscalPeriod = vars.FlagFiscalPeriod;
		var idAccountTitle = vars.IdAccountTitle;

		var num = 1;
		varsOptions.xaxis.min = 0;
		varsOptions.xaxis.ticks.push([0, '']);
		var arrPeriodNum = varsCollect.varsPeriod;
		var arrPeriod = [];
		var arrPeriodNumFiscalPeriod = [];
		for (var j = 0; j < arrPeriodNum.length; j++) {
			var numFiscalPeriod = arrPeriodNum[j];
			var strPeriod = numFiscalPeriod + varsCollect.strPeriod;
			var arr = varsCollect.varsFlagFiscalPeriod[numFiscalPeriod];
			var flagFirstMonth = 0;

			for (var i = 0; i < arr.length; i++) {
				var str = arr[i] + '';
				var tempData = [];

				var strTitle = strPeriod + varsCollect.varsStrFlagFiscalPeriod[numFiscalPeriod][str];

				if (flagFiscalPeriod == 'f1') {
					if (str.match(/^f1$/)) {
						tempData.push(num);
						tempData.push(strTitle);
						tempData.push(strTitle);
						varsOptions.xaxis.ticks.push(tempData);
						num++;
						arrPeriod.push(arr[i]);
						arrPeriodNumFiscalPeriod.push(numFiscalPeriod);
						break;
					}
				} else if (flagFiscalPeriod == 'f2') {
					if (str.match(/^f22$/)) {
						tempData.push(num);
						tempData.push(strTitle);
						tempData.push(strTitle);
						varsOptions.xaxis.ticks.push(tempData);
						num++;
						arrPeriod.push(arr[i]);
						arrPeriodNumFiscalPeriod.push(numFiscalPeriod);
						break;

					} else if (str.match(/^f2(.*?)$/)) {
						tempData.push(num);
						tempData.push(strTitle);
						tempData.push(strTitle);
						varsOptions.xaxis.ticks.push(tempData);
						num++;
						arrPeriod.push(arr[i]);
						arrPeriodNumFiscalPeriod.push(numFiscalPeriod);
					}

				} else if (flagFiscalPeriod == 'f4') {
					if (str.match(/^f44$/)) {
						tempData.push(num);
						tempData.push('<br>4/4');
						tempData.push(strTitle);
						varsOptions.xaxis.ticks.push(tempData);
						num++;
						arrPeriod.push(arr[i]);
						arrPeriodNumFiscalPeriod.push(numFiscalPeriod);
						break;

					} else if (str.match(/^f4(.*?)$/)) {
						tempData.push(num);
						var strTicks = strPeriod + '<br>' + RegExp.$1 + '/4';
						if (!str.match(/^f41$/)) {
							strTicks = '<br>' + RegExp.$1 + '/4';
						}
						tempData.push(strTicks);
						tempData.push(strTitle);
						varsOptions.xaxis.ticks.push(tempData);
						num++;
						arrPeriod.push(arr[i]);
						arrPeriodNumFiscalPeriod.push(numFiscalPeriod);
					}
				} else {
					if (!str.match(/^f/)) {
						tempData.push(num);
						var strTicks = numFiscalPeriod + '<br>' + str;
						if (flagFirstMonth) {
							strTicks = '<br>' + str;
						}
						tempData.push(strTicks);
						tempData.push(strTitle);
						varsOptions.xaxis.ticks.push(tempData);
						num++;
						arrPeriod.push(arr[i]);
						arrPeriodNumFiscalPeriod.push(numFiscalPeriod);
						flagFirstMonth++;
					}
				}
			}
		}
		varsOptions.xaxis.ticks.push([num, ' ']);
		varsOptions.xaxis.max = num;

		var varsTable = {};
		var varsData = [];
		var numData = 0;
		var numMaxValue = 0;

		var strLavel = '';
		if (varsCollect.arrStrTitle[idAccountTitle]) {
			strLavel = varsCollect.arrStrTitle[idAccountTitle].strTitle;
		}
		var tempData = {
			data  : [],
			label : strLavel
		};

		var insDisplayComma = new Code_Lib_DisplayComma();

		var num = 1;
		for (var j = 0; j < arrPeriod.length; j++) {
			var data = {};
			var idPeriod = arrPeriod[j] + '';
			var numFiscalPeriod = arrPeriodNumFiscalPeriod[j];
			varsTable[numFiscalPeriod] = {};
			varsTable[numFiscalPeriod][idPeriod] = {};

			var numValue =  '';
			if (varsCollect.varsBase[numFiscalPeriod].varsNum[idPeriod][idAccountTitle]) {
				numValue = varsCollect.varsBase[numFiscalPeriod].varsNum[idPeriod][idAccountTitle].sumNext;
			}
			if (numValue !== '') {
				if (numValue > numMaxValue) {
					numMaxValue = numValue;
				}
			}
			data.numValue = numValue;
			data.numValueComma = insDisplayComma.get({
				num : data.numValue
			});
			tempData.data.push([num, numValue]);
			num++;
			numData++;
			varsTable[numFiscalPeriod][idPeriod] = data;
		}
		varsData.push(tempData);

		varsOptions.yaxis.max = numMaxValue;
		if (numMaxValue < 1000) {
			varsOptions.yaxis.max = 1000;
		}

		var data = {};
		data.varsData = varsData;
		data.varsOptions = varsOptions;
		obj.vars.varsSpace.varsDetail = data;
	},



	/**
	 *
	*/
	_eventListConnect : function(obj)
	{
		var insEscape = new Code_Lib_Escape();
		obj.flag = insEscape.strLowCapitalize({data : obj.flag});

		if (obj.flag == 'reload') {
			if (this.insNavi.checkForm({flagType : 'common'})) return;
			this._eventValue({
				vars     : this.insNavi.getFormValue(),
				idTarget : ''
			});

		} else if (obj.flag == 'output') {
			var vars = this.insNavi.getFormValue();
			vars.FlagType = obj.flagType;
			this._eventValue({
				vars     : vars,
				idTarget : ''
			});

		}
		this._varsListConnect = obj;
		this._sendListConnect();
	},

	/**
	 *
	*/
	_varsListConnect : null,
	_sendListConnect : function(obj)
	{
		var strExt = this.strExt;
		var strClass = this.strClass;
		var idModule = this.idModule;
		var strChild = this.strChild;
		var jsonSearch = Object.toJSON(this._varsSearch);
		var jsonValue = Object.toJSON(this._varsValue);
		var arrayKey = [], arrayValue = [];
		var jsonStamp = {};

		var strFunc = 'List' + this._varsListConnect.flag.capitalize();
		jsonStamp = this._getJsonStamp({strFunc : strFunc});
		arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonValue', 'jsonSearch'];
		arrayValue = [strClass, idModule, strExt, strChild, strFunc, 'slave', jsonStamp, jsonValue, jsonSearch];

		if (this._varsListConnect.flag == 'output') {
			this.insRoot.setOutput({
				querysKey   : arrayKey,
				querysValue : arrayValue
			});
			this.insLayout.updateTool({flagLock : 0, from : 'list', idTarget : 'Output'});

		} else {
			this.insLayout.updateTool({flagLock : 1, from : 'list', idTarget : 'Reload'});
			this.insRoot.insRequest.set({
				flagLock        : 0,
				numZIndex       : this.insRoot.getZIndex(),
				insCurrent      : this,
				flagEscape      : 1,
				path            : this.insRoot.vars.varsSystem.path.post,
				querysKey       : arrayKey,
				querysValue     : arrayValue,
				functionSuccess : '_sendListConnectSuccess',
				functionFail    : '_sendListConnectFail',
				eleLoadStatus   : this.insList.insUnder.eleFormat.header.down('.codeLibBaseMarginRightFive', 0)
			});
		}
	},


	/**
	 *
	*/
	_eventListConnectSuccess : function(obj)
	{
		if (obj.json.flag == 1){
			if (this._varsListConnect.flag == 'reload') {
				this.insLayout.updateTool({flagLock : 0, from : 'list', idTarget : 'Reload'});
				this.vars.varsCollect = obj.json.data.varsCollect;
				this.vars.varsFlag = obj.json.data.varsFlag;
				this.vars.portal.varsList.templateDetail = obj.json.data.varsDetail;
				this._setListNavi();
			}

		} else if (obj.json.flag == 10) {
			if (this._varsListConnect.flag == 'reload') {
				this.insLayout.updateTool({flagLock : 0, from : 'list', idTarget : 'Reload'});
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