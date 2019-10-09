{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_CashPlan = Class.create(Code_Lib_ExtPortal,
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
		this._iniList();
		this._iniDetail();
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
					insCurrent._eventDetailConnect({flag : obj.vars.id, flagType : insCurrent.insDetail.vars.varsStatus.flagOutputNow});

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
				varsDetail : this._setListNaviChild({arr : objDetail}),
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
			if (obj.arr[i].id == 'FlagFiscalPeriod') {
				var insFormSelect = new Code_Lib_FormSelect({
					eleInsert  : $(this.insList.insForm.idSelf + obj.arr[i].id),
					insRoot    : this.insRoot,
					insCurrent : this,
					idSelf     : this.idSelf + 'FormSelect' + obj.arr[i].id,
					allot      : this._getListFormSelectAllot(),
					vars       : null
				});
				this._insListFormSelect[obj.arr[i].id] = insFormSelect;
				break;
			}
		}
	},

	/**
	 *
	*/
	_getListFormSelectAllot : function()
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
		this._setDetailNavi();
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

			if (obj.arr[i].id == 'Graph') {
				this._setListFormChart({
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
	_setListFormChart : function(obj)
	{
		var flagFiscalPeriod = 'month';
		if (this.insList) {
			if (this.insList.insForm) {
				this.insList.setValue();
				var vars = this.insList.getFormValue();
				flagFiscalPeriod = vars.FlagFiscalPeriod;
			}
		}

		var varsCollect = this.vars.varsCollect;
		var varsOptions = (Object.toJSON(varsCollect.tmplOptions)).evalJSON();

		/**/
		var num = 1;
		varsOptions.xaxis.min = 0;
		varsOptions.xaxis.ticks.push([0, '']);
		var arrPeriodNum = varsCollect.varsNumPeriod;
		var arr = varsCollect.varsFlagFiscalPeriod;
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
						tempData.push('<br>2/2');
						tempData.push(strTitle);
						varsOptions.xaxis.ticks.push(tempData);
						num++;
						arrPeriod.push(arr[i]);
						arrPeriodNumFiscalPeriod.push(numFiscalPeriod);
						break;

					} else if (str.match(/^f2(.*?)$/)) {
						tempData.push(num);
						var strTicks = strPeriod + '<br>' + RegExp.$1 + '/2';
						if (!str.match(/^f21$/)) {
							strTicks = '<br>' + RegExp.$1 + '/2';
						}
						tempData.push(strTicks);
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

		varsOptions.xaxis.ticks.push([num+1, ' ']);
		varsOptions.xaxis.max = num+1;
		var numMaxValue = 0;
		var varsData = [];
		var arr = varsCollect.varsLabelId;
		for (var i = 0; i < arr.length; i++) {
			var tempData = {
				data  : [],
				label : varsCollect.varsLabel[arr[i]]
			};
			var num = 1;
			for (var j = 0; j < arrPeriod.length; j++) {
				var strPeriod = arrPeriod[j] + '';
				var numFiscalPeriod = arrPeriodNumFiscalPeriod[j];
				var numValue = varsCollect.varsBase[numFiscalPeriod][strPeriod][arr[i]];
				if (numValue !== '') {
					if (numValue > numMaxValue) {
						numMaxValue = numValue;
					}
				}
				tempData.data.push([num, numValue]);
				num++;
			}
			varsData.push(tempData);
		}

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
			this._eventValue({
				vars     : {},
				idTarget : ''
			});

		} else if (obj.flag == 'output') {
			this._eventValue({
				vars     : {},
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
				this.vars.portal.varsList.templateDetail = obj.json.data.varsDetail;
				this._setListNavi();
				this._setDetailNavi();
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
	_iniDetail : function()
	{
		this._extDetail();
	},

	/**
	 *
	*/
	_setDetailStart : function()
	{
		this.insList.setValue();

		var objDetail = (Object.toJSON(this.vars.portal.varsDetail.templateDetail)).evalJSON();
		this.insDetail.eventNavi({
			strTitle : this.vars.portal.varsDetail.varsStart.strTitle,
			strClass : this.vars.portal.varsDetail.varsStart.strClass,
			vars     : {
				varsDetail : this._setNaviDetailChild({arr : objDetail, flagStart : 1}),
				varsEdit   : this.vars.portal.varsDetail.varsEdit,
				varsBtn    : null,
				vars       : {}
			}
		});

		this._setDetailContent();
	},

	/**
	 *
	*/
	_setDetailNavi : function()
	{
		this.insList.setValue();

		var vars = {};
		vars.IdAccountTitle = '';
		vars.IdSubAccountTitle = 'none';

		if (this.insDetail) {
			if (this.insDetail.insForm) {
				this.insDetail.setValue();
				vars = this.insDetail.getFormValue();
			}
		}

		var objDetail = (Object.toJSON(this.vars.portal.varsDetail.templateDetail)).evalJSON();
		this.insDetail.eventNavi({
			strTitle : this.vars.portal.varsDetail.varsStart.strTitle,
			strClass : this.vars.portal.varsDetail.varsStart.strClass,
			vars     : {
				varsDetail : this._setNaviDetailChild({arr : objDetail, vars : vars}),
				varsEdit   : this.vars.portal.varsDetail.varsEdit,
				varsBtn    : null,
				vars       : {}
			}
		});

		this._setDetailContent();
	},

	/**
	 *
	*/
	_setNaviDetailChild : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'IdAccountTitle') {
				if (obj.flagStart) {
					obj.arr[i].value = obj.arr[i].valueIni;

				} else {
					obj.arr[i].value = obj.vars[obj.arr[i].id];
				}

			} else if (obj.arr[i].id == 'IdSubAccountTitle') {
				if (obj.flagStart) {
					obj.arr[i].value = this._getOptionFirstValue({arr : obj.arr[i].arrayOption});

				} else {
					obj.arr[i].value = obj.vars[obj.arr[i].id];
				}
			}
		}

		return obj.arr;
	},

	_varsContent : {num : 0},
	_setDetailContent : function()
	{
		this._varsContent.num = 0;
		this._iniDetailFormSelect();
		this._iniDetailSpace();
	},

	/**
	 *
	*/
	_iniDetailFormSelect : function()
	{
		this._setDetailFormSelect({arr : this.insDetail.insForm.vars.varsDetail});
		this._setDetailFormSelectIdAccountTitle({
			arr      : this.insDetail.insForm.vars.varsDetail,
			flagIni  : 1
		});
	},

	/**
	 *
	*/
	_insDetailFormSelect : {},
	_setDetailFormSelect : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'IdAccountTitle'
				|| obj.arr[i].id == 'IdSubAccountTitle'
			) {
				var str = '_getDetailFormSelectAllot' + obj.arr[i].id;
				var insFormSelect = new Code_Lib_FormSelect({
					eleInsert  : $(this.insDetail.insForm.idSelf + obj.arr[i].id),
					insRoot    : this.insRoot,
					insCurrent : this,
					idSelf     : this.idSelf + 'FormSelect' + obj.arr[i].id,
					allot      : this[str](),
					vars       : null
				});
				this._insDetailFormSelect[obj.arr[i].id] = insFormSelect;
			}
		}
	},

	/**
	 *
	*/
	_getDetailFormSelectAllotIdSubAccountTitle : function()
	{
		var allot = function(obj) {
			var insCurrent = obj.insCurrent;
			insCurrent._setDetailFormSelectValue();
		};

		return allot;
	},

	/**
	 *
	*/
	_getDetailFormSelectAllotIdAccountTitle : function()
	{
		var allot = function(obj) {
			var insCurrent = obj.insCurrent;
			insCurrent._setDetailFormSelectIdAccountTitle({
				arr : insCurrent.insDetail.insForm.vars.varsDetail
			});
			insCurrent._setDetailFormSelectValue();
		};

		return allot;
	},

	/**
	 *
	*/
	_setDetailFormSelectIdAccountTitle : function(obj)
	{
		if (!obj.flagIni) {
			this.insDetail.setValue();
		}
		var idAccountTitle = '';
		var arrSelectTag = (Object.toJSON(this.vars.varsCollect.arrSubAccountTitle.arrSelectTag)).evalJSON();
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'IdAccountTitle') {
				idAccountTitle = obj.arr[i].value;

			} else if (obj.arr[i].id == 'IdSubAccountTitle') {
				var varsNone = (Object.toJSON(obj.arr[i].varsTmpl.varsNone)).evalJSON();
				if (!this.vars.varsCollect.arrSubAccountTitle.arrStrTitle[idAccountTitle]) {
					obj.arr[i].arrayOption = [varsNone];

				} else {
					arrSelectTag[idAccountTitle].unshift(varsNone);
					obj.arr[i].arrayOption = arrSelectTag[idAccountTitle];
					if (!obj.flagIni) {
						obj.arr[i].value = 'none';
					}
				}
				$(this.insDetail.insForm.idSelf + obj.arr[i].id).innerHTML = '';
				this.insDetail.insForm.setTemplateSelect({
					arr       : obj.arr[i].arrayOption,
					now       : obj.arr[i].value,
					eleInsert : $(this.insDetail.insForm.idSelf + obj.arr[i].id),
					vars      : obj.arr[i]
				});
			}
		}
	},

	/**
	 *
	*/
	_setDetailFormSelectValue : function(obj)
	{
		this._updateDetailSpace({arr : this.insDetail.insForm.vars.varsDetail});
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
		var varsTable = {};
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsSpace) continue;
			var ele = this.insDetail.insForm.eleWrap.down('.codeLibFormContent', this._varsContent.num);
			this._varsContent.num++;
			if (obj.arr[i].id == 'GraphBar') {
				this._setDetailFormChartBar({
					vars : obj.arr[i]
				});
				varsTable = obj.arr[i].varsSpace.varsDetail;

			} else if (obj.arr[i].id == 'TableDetail') {
				this._setDetailFormTableDetail({
					vars      : obj.arr[i],
					varsTable : varsTable
				});
			}

			var insSpace = new Code_Lib_Space({
				eleScroll  : this.insDetail.insForm.eleInsert,
				eleInsert  : ele,
				insRoot    : this.insRoot,
				insCurrent : this,
				idSelf     : this.idSelf + 'Space' + obj.arr[i].id,
				allot      : {},
				vars       : obj.arr[i].varsSpace
			});
			this._varsDetailSpace[obj.arr[i].id] = insSpace;
		}
	},

	/**
	 *
	*/
	_setDetailFormChartBar : function(obj)
	{
		var flagFiscalPeriod = 'month';
		if (this.insList) {
			if (this.insList.insForm) {
				this.insList.setValue();
				var vars = this.insList.getFormValue();
				flagFiscalPeriod = vars.FlagFiscalPeriod;
			}
		}

		var varsCollect = this.vars.varsCollect;
		var varsOptions = (Object.toJSON(varsCollect.tmplOptions)).evalJSON();

		/**/
		var num = 1;
		varsOptions.xaxis.min = 0;
		varsOptions.xaxis.ticks.push([0, '']);
		var arrPeriodNum = varsCollect.varsNumPeriod;
		var arr = varsCollect.varsFlagFiscalPeriod;
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
						tempData.push('<br>2/2');
						tempData.push(strTitle);
						varsOptions.xaxis.ticks.push(tempData);
						num++;
						arrPeriod.push(arr[i]);
						arrPeriodNumFiscalPeriod.push(numFiscalPeriod);
						break;

					} else if (str.match(/^f2(.*?)$/)) {
						tempData.push(num);
						var strTicks = strPeriod + '<br>' + RegExp.$1 + '/2';
						if (!str.match(/^f21$/)) {
							strTicks = '<br>' + RegExp.$1 + '/2';
						}
						tempData.push(strTicks);
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
		varsOptions.xaxis.ticks.push([num+1, ' ']);
		varsOptions.xaxis.max = num+1;

		var insDisplayComma = new Code_Lib_DisplayComma();

		this.insDetail.setValue();
		var vars = this.insDetail.getFormValue();
		var idAccountTitle = vars.IdAccountTitle;
		var idSubAccountTitle = vars.IdSubAccountTitle;

		var numMaxValue = 0;
		var varsTable = {};
		var varsData = [];
		var arr = varsCollect.varsLabelId;
		for (var i = 0; i < arr.length; i++) {
			if (arr[i] == 'sumNext') {
				continue;
			}
			var tempData = {
				data  : [],
				label : varsCollect.varsLabel[arr[i]]
			};
			varsTable[arr[i]] = {};
			var num = 1;
			for (var j = 0; j < arrPeriod.length; j++) {
				var flagFiscalPeriod = arrPeriod[j] + '';
				var numFiscalPeriod = arrPeriodNumFiscalPeriod[j];
				if (varsTable[arr[i]][numFiscalPeriod] == undefined) {
					varsTable[arr[i]][numFiscalPeriod] = {};
				}
				var strDetail = 'all';
				if (idSubAccountTitle != 'none') {
					strDetail = idSubAccountTitle;
				}

				var data = {};
				if (idSubAccountTitle == 'none') {
					data.strTitle = varsCollect.arrAccountTitle.arrStrTitle[idAccountTitle].strTitleFS;

				} else {
					data.strTitle = varsCollect.arrSubAccountTitle.arrStrTitle[idAccountTitle][idSubAccountTitle].strTitle;
				}
				var numValue = this._getDetailNumValue({
					varsCollect      : varsCollect,
					idAccountTitle   : idAccountTitle,
					flagFiscalPeriod : flagFiscalPeriod,
					strDetail        : strDetail,
					flagIn           : arr[i],
					numFiscalPeriod  : numFiscalPeriod
				});
				if (numValue > numMaxValue) {
					numMaxValue = numValue;
				}
				data.numValue = numValue;
				data.numValueComma = insDisplayComma.get({
					num : data.numValue
				});
				varsTable[arr[i]][numFiscalPeriod][flagFiscalPeriod] = data;
				tempData.data.push([num, numValue]);
				num++;
			}
			varsData.push(tempData);
		}

		varsOptions.yaxis.max = numMaxValue;
		if (numMaxValue < 1000) {
			varsOptions.yaxis.max = 1000;
		}

		var data = {};
		data.varsData = varsData;
		data.varsOptions = varsOptions;
		data.arrPeriod = arrPeriod;
		data.arrPeriodNumFiscalPeriod = arrPeriodNumFiscalPeriod;
		data.varsTable = varsTable;
		obj.vars.varsSpace.varsDetail = data;
	},

	/**
	 *
	*/
	_getDetailNumValue : function(obj)
	{

		if (!obj.varsCollect.varsCashValue[obj.numFiscalPeriod]) {
			return 0;
		}
		if (!obj.varsCollect.varsCashValue[obj.numFiscalPeriod][obj.flagFiscalPeriod]) {
			return 0;
		}
		if (!obj.varsCollect.varsCashValue[obj.numFiscalPeriod][obj.flagFiscalPeriod].varsContra) {
			return 0;
		}
		if (!obj.varsCollect.varsCashValue[obj.numFiscalPeriod][obj.flagFiscalPeriod].varsContra[obj.idAccountTitle]) {
			return 0;
		}
		if (!obj.varsCollect.varsCashValue[obj.numFiscalPeriod][obj.flagFiscalPeriod].varsContra[obj.idAccountTitle][obj.strDetail]) {
			return 0;
		}
		var numValue = parseFloat(obj.varsCollect.varsCashValue[obj.numFiscalPeriod][obj.flagFiscalPeriod].varsContra[obj.idAccountTitle][obj.strDetail][obj.flagIn]);

		return numValue;
	},

	/**
	 *
	*/
	_getDetailTableNumWidth : function()
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
		var objDetail = this.vars.portal.varsList.templateDetail;
		for (var i = 0; i < objDetail.length; i++) {
			if (objDetail[i].id == 'FlagFiscalPeriod') {
				continue;
			}
			if (flagFiscalPeriod == 'f1') {
				if (objDetail[i].id == 'TableF1') {
					return objDetail[i].tmplTable.numWidth;
				}

			} else if (flagFiscalPeriod == 'f2') {
				if (objDetail[i].id == 'TableF2') {
					return objDetail[i].tmplTable.numWidth;
				}

			} else if (flagFiscalPeriod == 'f4') {
				if (objDetail[i].id == 'TableF4') {
					return objDetail[i].tmplTable.numWidth;
				}

			} else if (flagFiscalPeriod == 'month') {
				if (objDetail[i].id == 'TableMonth') {
					return objDetail[i].tmplTable.numWidth;
				}
			}
		}
	},

	/**
	 *
	*/
	_setDetailFormTableDetail : function(obj)
	{
		var varsCollect = this.vars.varsCollect;

		var numWidth = this._getDetailTableNumWidth();

		var strContent = '';
		var strColumn = '';
		var tagTdColumn = obj.vars.tagTdColumn;
		strColumn += tagTdColumn.interpolate({insertPoint : ''});

		var arr = obj.varsTable.arrPeriod;
		for (var i = 0; i < arr.length; i++) {
			var numFiscalPeriod = obj.varsTable.arrPeriodNumFiscalPeriod[i];
			var tagTdColumn = obj.vars.tagTdColumn;
			var str = varsCollect.varsStrFlagFiscalPeriod[numFiscalPeriod][arr[i]];
			var temp = '' + arr[i];
			str = numFiscalPeriod + this.vars.varsItem.tmplFiscalPeriod.strPeriod + str;
			strColumn += tagTdColumn.interpolate({insertPoint : str, numWidth : numWidth});
		}
		var tagTr = obj.vars.tagTr;
		strColumnTr = tagTr.interpolate({insertPoint : strColumn});
		strContent = strColumnTr;

		var arr = varsCollect.varsLabelId;
		for (var i = 0; i < arr.length; i++) {
			if (arr[i] == 'sumNext') {
				continue;
			}
			var strRow = '';
			var tagTdRowColumn = obj.vars.tagTdRowColumn;
			var str = varsCollect.varsLabel[arr[i]];
			strRow += tagTdRowColumn.interpolate({insertPoint : str});

			var arrPeriod = obj.varsTable.arrPeriod;
			for (var j = 0; j < arrPeriod.length; j++) {
				var numFiscalPeriod = obj.varsTable.arrPeriodNumFiscalPeriod[j];
				var tagTdRow = obj.vars.tagTdRow;
				var str = obj.varsTable.varsTable[arr[i]][numFiscalPeriod][arrPeriod[j]].numValueComma;
				strRow += tagTdRow.interpolate({insertPoint : str});
			}
			var tagTr = obj.vars.tagTr;
			var strRowTr = tagTr.interpolate({insertPoint : strRow});
			strContent += strRowTr;
		}
		var tagTable = obj.vars.tagTable;
		var strTable = tagTable.interpolate({insertPoint : strContent});
		obj.vars.varsSpace.varsDetail.strHtml = strTable;
	},

	/**
	 *
	*/
	_updateDetailSpace : function(obj)
	{
		var varsTable = {};
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsSpace) continue;
			if (obj.arr[i].id == 'GraphBar') {
				this._setDetailFormChartBar({
					vars : obj.arr[i]
				});
				varsTable = obj.arr[i].varsSpace.varsDetail;
				this._varsDetailSpace[obj.arr[i].id].iniReload({
					vars : obj.arr[i].varsSpace
				});

			} else if (obj.arr[i].id == 'TableDetail') {
				this._setDetailFormTableDetail({
					vars      : obj.arr[i],
					varsTable : varsTable
				});
				this._varsDetailSpace[obj.arr[i].id].iniReload({
					vars : obj.arr[i].varsSpace
				});
			}
		}
	},

	/**
	 *
	*/
	_eventDetailConnect : function(obj)
	{
		var insEscape = new Code_Lib_Escape();

		obj.flag = insEscape.strLowCapitalize({data : obj.flag});
		if (obj.flag == 'output') {
			this.insDetail.setValue();
			var vars = this.insDetail.getFormValue();
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
		var insEscape = new Code_Lib_Escape();

		if (this._varsDetailConnect.flag == 'reload') {
			var strFunc = 'DetailReload';
			jsonStamp = this._getJsonStamp({strFunc : strFunc});
			this.insLayout.updateTool({flagLock : 1, from : 'detail', idTarget : 'Reload'});
			arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonValue', 'jsonSearch'];
			arrayValue = [strClass, idModule, strExt, strChild, strFunc, 'slave', jsonStamp, jsonValue, jsonSearch];

		} else {
			var strDb = 'master';
			var strFunc = 'Detail' + insEscape.strCapitalize({data : this._varsDetailConnect.flag});
			if (this._varsDetailConnect.flag == 'output') {
				strDb = 'slave';
			}
			arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonValue', 'jsonSearch'];
			arrayValue = [strClass, idModule, strExt, strChild, strFunc, strDb, jsonStamp, jsonValue, jsonSearch];
		}

		if (this._varsDetailConnect.flag == 'output') {
			this.insRoot.setOutput({
				querysKey   : arrayKey,
				querysValue : arrayValue,
			});
			this.insLayout.updateTool({flagLock : 0, from : 'detail', idTarget : 'Output'});

		} else {
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
	_setDetailContentValue : function()
	{
	},

	/**
	 *
	*/
	_eventLayoutDetailContent : function()
	{
		if (!this.insDetail.insForm) return;
		this._setDetailNavi();
	},

	/**
	 *
	*/
	_preEventLayoutDetailContent : function()
	{
		if (!this.insDetail.insForm) return;
		this.insDetail.setValue();
	},

	/**
	 *
	*/
	_eventRemoveDetailContent : function()
	{
		this._insDetailFormSelect.IdAccountTitle.stopListener();
		this._insDetailFormSelect.IdSubAccountTitle.stopListener();
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