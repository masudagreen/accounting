{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_FinancialAnalyze = Class.create(Code_Lib_ExtPortal,
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
	_iniDetail : function()
	{
		this._extDetail();
	},

	/**
	 *
	*/
	_setDetailStart : function()
	{
		var objDetail = (Object.toJSON(this.vars.portal.varsDetail.templateDetail)).evalJSON();
		this.insDetail.eventNavi({
			strTitle : this.vars.portal.varsDetail.varsStart.strTitle,
			strClass : this.vars.portal.varsDetail.varsStart.strClass,
			vars     : {
				varsDetail : this._setNaviDetailChild({arr : objDetail}),
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
		var vars = {};
		var flagFiscalPeriod = 'month';
		if (this.insDetail) {
			if (this.insDetail.insForm) {
				this.insDetail.setValue();
				vars = this.insDetail.getFormValue();
				flagFiscalPeriod = vars.FlagFiscalPeriod;
			}
		}

		var objDetail = (Object.toJSON(this.vars.portal.varsDetail.templateDetail)).evalJSON();
		for (var i = 0; i < objDetail.length; i++) {
			if (objDetail[i].id == 'FlagFiscalPeriod' || objDetail[i].id == 'Graph') {
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
			if (obj.arr[i].id == 'FlagFiscalPeriod') {
				if (obj.vars) {
					obj.arr[i].value = obj.vars.FlagFiscalPeriod;
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
	},

	/**
	 *
	*/
	_insDetailFormSelect : {},
	_setDetailFormSelect : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'FlagFiscalPeriod') {
				var insFormSelect = new Code_Lib_FormSelect({
					eleInsert  : $(this.insDetail.insForm.idSelf + obj.arr[i].id),
					insRoot    : this.insRoot,
					insCurrent : this,
					idSelf     : this.idSelf + 'FormSelect' + obj.arr[i].id,
					allot      : this._getDetailFormSelectAllot(),
					vars       : null
				});
				this._insDetailFormSelect[obj.arr[i].id] = insFormSelect;
				break;
			}
		}
	},

	/**
	 *
	*/
	_setDetailFormChart : function(obj)
	{
		var flagFiscalPeriod = 'month';
		if (this.insDetail) {
			if (this.insDetail.insForm) {
				this.insDetail.setValue();
				var vars = this.insDetail.getFormValue();
				flagFiscalPeriod = vars.FlagFiscalPeriod;
			}
		}

		var varsCollect = this.vars.portal.varsDetail.varsCollect;
		var varsOptions = (Object.toJSON(varsCollect.tmplOptions)).evalJSON();

		/**/
		var num = 1;
		varsOptions.xaxis.min = 0;
		varsOptions.xaxis.ticks.push([0, '']);
		var arr = varsCollect.varsFlagFiscalPeriod;
		var arrPeriod = [];
		for (var i = 0; i < arr.length; i++) {
			var str = arr[i] + '';
			var tempData = [];
			if (flagFiscalPeriod == 'f1') {
				if (str.match(/^f1$/)) {
					tempData.push(num);
					tempData.push(varsCollect.varsStrFlagFiscalPeriod[str]);
					varsOptions.xaxis.ticks.push(tempData);
					arrPeriod.push(arr[i]);
					break;
				}
			} else if (flagFiscalPeriod == 'f2') {
				if (str.match(/^f22$/)) {
					tempData.push(num);
					tempData.push(varsCollect.varsStrFlagFiscalPeriod[str]);
					varsOptions.xaxis.ticks.push(tempData);
					arrPeriod.push(arr[i]);
					break;

				} else if (str.match(/^f2/)) {
					tempData.push(num);
					tempData.push(varsCollect.varsStrFlagFiscalPeriod[str]);
					varsOptions.xaxis.ticks.push(tempData);
					num++;
					arrPeriod.push(arr[i]);
				}

			} else if (flagFiscalPeriod == 'f4') {
				if (str.match(/^f44$/)) {
					tempData.push(num);
					tempData.push(varsCollect.varsStrFlagFiscalPeriod[str]);
					varsOptions.xaxis.ticks.push(tempData);
					arrPeriod.push(arr[i]);
					break;

				} else if (str.match(/^f4/)) {
					tempData.push(num);
					tempData.push(varsCollect.varsStrFlagFiscalPeriod[str]);
					varsOptions.xaxis.ticks.push(tempData);
					num++;
					arrPeriod.push(arr[i]);
				}
			} else {
				if (!str.match(/^f/)) {
					tempData.push(num);
					tempData.push(varsCollect.varsStrFlagFiscalPeriod[str]);
					varsOptions.xaxis.ticks.push(tempData);
					num++;
					arrPeriod.push(arr[i]);
				}
			}
		}
		varsOptions.xaxis.ticks.push([num+1, ' ']);
		varsOptions.xaxis.max = num+1;

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
				var numValue = varsCollect.varsBase[strPeriod][arr[i]];
				tempData.data.push([num, numValue]);
				num++;
			}
			varsData.push(tempData);
		}
		var data = {};
		data.varsData = varsData;
		data.varsOptions = varsOptions;
		obj.vars.varsSpace.varsDetail = data;
	},

	/**
	 *
	*/
	_getDetailFormSelectAllot : function()
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
	_setDetailFormSelectValue : function()
	{
		this._setDetailNavi();
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

			if (obj.arr[i].id == 'Graph') {
				this._setDetailFormChart({
					vars : obj.arr[i]
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
		this._insDetailFormSelect.FlagFiscalPeriod.stopListener();
	},

	/**
	 *
	*/
	_eventDetailConnect : function(obj)
	{
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
				this.vars.portal.varsDetail.varsCollect.varsBase = obj.json.data.varsBase;
				this.vars.portal.varsDetail.templateDetail = obj.json.data.varsDetail;
				this._setDetailNavi();
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