{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_LogImport = Class.create(Code_Lib_ExtPortal,
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
		this._iniList();
		this._iniDetail();
	},

	/**
	 *
	*/
	_hideLockWindow : function(obj)
	{
		this.bootAutoSearchOver({flag : 'showRetryBtn'});
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
	_varsListener : [],
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
		if (!this._varsListener.length) {
			return;
		}
		this.insListener.stop();
		this._stopListenerChild({arr : this._varsListener});
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

	_flagAutoSearchOver : '',
	_varsAutoSearchOver : {},
	bootAutoSearchOver : function(obj)
	{
		var insEscape = new Code_Lib_Escape();
		this._varsAutoSearchOver = obj.vars;
		this._flagAutoSearchOver = obj.flag;
		if (obj.flag == 'showLog') {
			this._eventListConnect({flag : 'Reload', flagType : 'start'});

		} else if (obj.flag == 'addLog') {
			this._setDetailChild({flag : obj.flag});

		} else if (obj.flag == 'addLogDetail') {
			this._setDetailChild({
				flag : obj.flag,
				vars : obj.vars
			});

		} else if (obj.flag == 'showRetryBtn') {
			var varsData = this.insTop.checkChildData({idTarget : 'Log'});
			varsData.insClass.bootAutoSearchOver({flag : obj.flag});

		} else if (obj.flag == 'loopFilter') {

			var varsData = this.insTop.checkChildData({idTarget : 'Log'});
			varsData.insClass.bootAutoSearchOver({flag : obj.flag});

			var strExt = this.strExt;
			var strChild = 'EditorTemp';
			var idTarget = strExt + strChild;
			var varsData = this.checkChildData({idTarget : idTarget});
			varsData.insClass.insWindow.hideLockWindow();

		} else if (obj.flag == 'hideLockWindow') {
			var strExt = this.strExt;
			var strChild = 'EditorTemp';
			var idTarget = strExt + strChild;
			var varsData = this.checkChildData({idTarget : idTarget});
			varsData.insClass.insWindow.hideLockWindow();

		} else if (obj.flag == 'reloadMemo') {
			var strExt = this.strExt;
			var strChild = 'Editor';
			var idTarget = strExt + strChild;
			var varsData = this.checkChildData({idTarget : idTarget});
			if (varsData) {
				varsData.insClass.bootAutoSearchOver({flag : obj.flag, vars : obj.vars});
			}
		}
	},

	eventAutoSearchOver : function(obj)
	{

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
		var varsRule = (Object.toJSON(this.vars.varsRule)).evalJSON();
		varsRule.arrAccountTitle.arrSelectTag.unshift(this.vars.varsItem.arrBlank);
		varsRule.arrDepartment.arrSelectTag.unshift(this.vars.varsItem.arrBlank);
		this.insFormJournal = new Code_Plugin_Accounting_Lib_JournalImport({varsRule : varsRule});
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

				} else if (obj.vars.id == 'Search') {
					insCurrent._iniChild({
						strTitleParent : insCurrent.insWindow.vars.strTitle,
						strTitleChild  : insCurrent.vars.child.varsTitle[obj.vars.id],
						strExt         : insCurrent.strExt,
						strChild       : obj.vars.id,
						strClass       : insCurrent.strClass,
						idModule       : insCurrent.idModule
					});
				}

			} else if (obj.from == 'list-_mousedownMenu') {
				if (obj.vars.id == 'Switch') {
					return insCurrent.insList.vars.varsStatus.flagNow;

				} else if (obj.vars.id == 'Output') {
					return insCurrent.insList.vars.varsStatus.flagOutputNow;

				} else if (obj.vars.id == 'Print') {
					return insCurrent.insList.vars.varsStatus.flagPrintNow;

				} else if (obj.vars.id == 'Reload') {
					return insCurrent.insList.vars.varsStatus.flagReloadNow;
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
	_setDetailChild : function(obj)
	{
		var objDetail = (Object.toJSON(this.vars.portal.varsDetail.templateDetail)).evalJSON();
		var varsDetail = [];
		var varsIni = null;
		var vars = {};
		var flagAddLogDetail = (obj.flag == 'addLogDetail')? 1 : 0;
		var flagDetail = obj.flag;

		var insBack = (obj.insBack)? obj.insBack : {};
		var strBackFunc = (obj.strBackFunc)? obj.strBackFunc : '';

		if (obj.flag == 'add' || obj.flag == 'addLog') {
			obj.flag = 'add';
			varsDetail = this._getDetailChildVars({
				arr  : objDetail,
				flag : obj.flag
			});

		} else if (obj.flag == 'addLogDetail') {
			varsIni = this._getDetailChildVars({
				arr  : (Object.toJSON(objDetail)).evalJSON(),
				flag : 'add'
			});
			varsDetail = this._getDetailChildVars({
				arr       : objDetail,
				flag      : obj.flag,
				varsValue : obj.vars
			});
			obj.flag = 'add';

		} else if (obj.flag == 'copy') {
			varsIni = this._getDetailChildVars({
				flag    : 'add',
				arr     : (Object.toJSON(objDetail)).evalJSON(),
				vars    : this.insDetail.varsEventList.vars
			});
			varsDetail = this._getDetailChildVars({
				flag : obj.flag,
				arr  : objDetail,
				vars : this.insDetail.varsEventList.vars
			});
			obj.flag = 'add';
			vars = this.insDetail.varsEventList.vars;

		} else if (obj.flag == 'edit') {
			varsIni = this._getDetailChildVars({
				flag    : 'add',
				arr     : (Object.toJSON(objDetail)).evalJSON(),
				vars    : this.insDetail.varsEventList.vars
			});
			varsDetail = this._getDetailChildVars({
				flag : obj.flag,
				arr  : objDetail,
				vars : this.insDetail.varsEventList.vars
			});
			vars = this.insDetail.varsEventList.vars;
		}

		var idTarget = 0;

		if (this.insDetail.varsEventList.vars) {
			if (this.insDetail.varsEventList.vars.vars) {
				idTarget = this.insDetail.varsEventList.vars.vars.idTarget;

			}
		}

		var varsTreePast = null;
		var strChild = 'Editor';
		if (flagAddLogDetail) {
			strChild = 'EditorTemp';
			var idTarget = this.strExt + 'Editor';
			var varsData = this.checkChildData({idTarget : idTarget});
			if (varsData) {
				varsTreePast = varsData.insClass.bootAutoSearchOver({flag : 'getMemo'});
			}
		}

		this._extChild({
			strTitleParent : this.insWindow.vars.strTitle,
			strTitleChild  : this.vars.child.varsTitle.editor,
			strExt         : this.strExt,
			strChild       : strChild,
			strClass       : this.strClass,
			idModule       : this.idModule,
			varsChild      : {
				flagType     : obj.flag,
				flagDetail   : flagDetail,
				idTarget     : idTarget,
				varsDetail   : varsDetail,
				varsIni      : varsIni,
				varsTreePast : varsTreePast,
				vars         : vars
			}
		});

	},

	_extChild : function(obj)
	{
		this._setVarChild(obj);
		var strExt = this._varsChild.strExt;
		var strChild = this._varsChild.strChild;
		var flagDetail = this._varsChild.varsChild.flagDetail;

		if (this['ins' + strExt + strChild]) {
			if (strChild == 'EditorTemp') {
				if (this['ins' + strExt + strChild].vars.flagHideNow) {
					if (this['ins' + strExt + strChild + 'Class']) {
						this['ins' + strExt + strChild + 'Class'].eventWindowAppear({vars : this._varsChild.varsChild});
						this['ins' + strExt + strChild].showLockWindow();
					}

				} else {
					this.eventHide();
				}

			} else {
				if (this['ins' + strExt + strChild].vars.flagHideNow) {
					if (this['ins' + strExt + strChild + 'Class']) {
						this['ins' + strExt + strChild + 'Class'].eventWindowAppear({vars : this._varsChild.varsChild});
						this['ins' + strExt + strChild].updateHide({ flagEffect : 1 });
					}

				} else {
					if (flagDetail == 'addLog') {
						if (this['ins' + strExt + strChild + 'Class']) {
							this['ins' + strExt + strChild + 'Class'].eventWindowAppear({vars : this._varsChild.varsChild});
							this['ins' + strExt + strChild].setZIndex();
						}
					} else {
						this.eventHide();
					}
				}
			}

		} else {
			this._setVarChild(obj);
			this._varChild();
			this._setChild();
		}
	},


	/**
	 *
	*/
	_iniList : function()
	{
		this._extList();
	},

	/**
	 *
	*/
	eventChildSearchConnect : function(obj)
	{
		this._varsSearch = obj.varsSearch;
		var temp = {};
		temp.numLotNow = this._varsSearch.numLotNow;
		this._eventListConnect({
			flag        : obj.flag,
			strBackFunc : obj.strBackFunc,
			insBack     : obj.insBack,
			vars        : temp
		});
	},

	/**
	 *
	*/
	_eventListConnect : function(obj)
	{
		if (obj.flag == 'Output' || obj.flag == 'Print') {
			this._eventSearch({
				numLotNow : this._varsSearch.numLotNow,
				ph : {
					arrWhere : this._varsSearch.ph.arrWhere,
					arrOrder : this._varsSearch.ph.arrOrder
				}
			});

		} else if (obj.flag == 'Reload') {
			if (obj.flagType == 'start') {
				this._resetSearch();
			}
			this._eventSearch({
				numLotNow : this._varsSearch.numLotNow,
				ph : {
					arrWhere : this._varsSearch.ph.arrWhere,
					arrOrder : this._varsSearch.ph.arrOrder
				}
			});

		} else if (obj.flag == 'Search') {
			this._eventSearch({
				numLotNow : obj.vars.numLotNow,
				ph : {
					arrWhere : this._varsSearch.ph.arrWhere,
					arrOrder : this._varsSearch.ph.arrOrder
				}
			});

		} else if (obj.flag == 'Delete') {
			var arrId = this.insList.getTableCheckBoxArrId();
			if (!arrId.length) {
				alert(this.insRoot.vars.varsSystem.str.selectRequire);
				this.insList.eventNavi({strTitle : null, strClass : null});
				return;
			}
			this._eventValue({
				vars     : this.insList.getTableCheckBoxArrId(),
				idTarget : ''
			});
		}

		this._varsListConnect = obj;
		this._sendListConnect();
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
			else if (obj.from == '_mousedownMove') insCurrent.insNavi.eventMove({vars : obj.vars});
			else if (obj.from == 'view-eventLayout') insCurrent._eventLayoutDetailContent();
			else if (obj.from == 'view-checkTextBtn') insCurrent._checkDetailContentTextBtn({vars : obj.vars});
			else if (obj.from == 'view-eventBtnBottom') insCurrent._eventDetailConnect({flag : obj.vars.vars.vars.idTarget});
		};

		return allot;
	},

	/**
	 *
	*/
	_checkDetailContentTextBtn : function(obj)
	{
		this.bootAutoSearch(obj);
	},

	/**
		{

		}
	*/
	bootAutoSearch : function(obj)
	{
		var insEscape = new Code_Lib_Escape();
		var flag = 'Reload';
		var flagLock = this.insLayout.checkToolLock({from : 'list', idTarget : flag});
		if (flagLock) {
			return;
		}
		this._resetSearch();
		var varsData = [];
		var varsTmpl = {flagType: '', strColumn: '', flagCondition: 'eq', value: ''};

		var str = insEscape.strLowCapitalize({data : obj.vars.vars.idTarget});

		if (str == 'arrSpaceStrTag') {
			var flagTag = this.insTop.bootWindowTag({
				strTarget : obj.vars.vars.strTag
			});
			if (flagTag) {
				return;
			}
			varsTmpl.flagType = 'tag';
			varsTmpl.flagCondition = 'like';
			varsTmpl.strColumn = str;
			varsTmpl.value = ' ' + obj.vars.vars.strTag + ' ';
			varsData.push(varsTmpl);

		} else if (str == 'id') {
			varsTmpl.flagType = 'num';
			varsTmpl.strColumn = 'idLogImport';
			varsTmpl.value = obj.vars.vars[str];
			varsData.push(varsTmpl);

		} else if (str == 'strTitle') {
			varsTmpl.flagType = 'str';
			varsTmpl.strColumn = str;
			varsTmpl.value = obj.vars.vars[str];
			varsData.push(varsTmpl);

		} else if (str == 'flagAttest') {
			varsTmpl.flagType = 'flagAttest';
			varsTmpl.strColumn = 'flagAttest';
			varsTmpl.value = obj.vars.vars[str];
			varsData.push(varsTmpl);

		} else if (str.match(/^arrCommaIdAccountTitle/)) {
			varsTmpl.flagType = 'commaFs';
			varsTmpl.strColumn = str;
			varsTmpl.flagCondition = 'like';
			varsTmpl.value = ',' + obj.vars.vars[str] + ',';
			varsData.push(varsTmpl);

		} else if (str.match(/^arrCommaIdDepartment/)) {
			varsTmpl.flagType = 'commaDepartment';
			varsTmpl.strColumn = str;
			varsTmpl.flagCondition = 'like';
			varsTmpl.value = ',' + obj.vars.vars[str] + ',';
			varsData.push(varsTmpl);

		} else if (str.match(/^arrCommaConsumptionTaxWithoutCalc/)) {
			varsTmpl.flagType = 'commaTaxWithoutCalc';
			varsTmpl.strColumn = str;
			varsTmpl.flagCondition = 'like';
			varsTmpl.value = ',' + obj.vars.vars[str] + ',';
			varsData.push(varsTmpl);

		} else if (str.match(/^arrCommaConsumptionTax/)) {
			varsTmpl.flagType = 'commaTax';
			varsTmpl.strColumn = str;
			varsTmpl.flagCondition = 'like';
			varsTmpl.value = ',' + obj.vars.vars[str] + ',';
			varsData.push(varsTmpl);
			/*
			 * 20191001 start
			*/
		} else if (str.match(/^arrCommaRateConsumptionTax/)) {
			varsTmpl.flagType = 'commaTaxRate';
			varsTmpl.strColumn = str;
			varsTmpl.flagCondition = 'like';
			varsTmpl.value = ',' + obj.vars.vars[str] + ',';
			varsData.push(varsTmpl);
			/*
			 * 20191001 end
			*/
		} else if (str.match(/^arrComma/)) {
			varsTmpl.flagType = 'comma';
			varsTmpl.strColumn = str;
			varsTmpl.flagCondition = 'like';
			varsTmpl.value = ',' + obj.vars.vars[str] + ',';
			varsData.push(varsTmpl);

		} else if (str == 'numColStrTitle'
			|| str == 'numColNumValue'
			|| str == 'numColStampBook'
		) {
			varsTmpl.flagType = 'num';
			varsTmpl.strColumn = str;
			varsTmpl.value = obj.vars.vars[str];
			varsData.push(varsTmpl);
		}
		this._varsSearch.ph.arrWhere = varsData;
		this._eventListConnect({flag : flag});
	},

	/**
	 *
	*/
	_varsDetailFormCheck : [],
	_iniDetailFormCheck : function()
	{
		this._varsDetailFormCheck = [];
		this._setDetailFormCheck({arr : this.insDetail.insView.vars.varsDetail});
	},

	/**
	 *
	*/
	_setDetailFormCheck : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormCheck) continue;
			var ele = this.insDetail.insView.eleWrap.down('.codeLibViewLineContent', this._varsContent.num);
			if (obj.arr[i].id == 'JsonPermitHistory') {
				this._varsContent.numPermit = this._varsContent.num;
			}
			this._varsContent.num++;
			var strAllot = '_getDetailFormCheckAllot';
			var insFormCheck = new Code_Lib_FormCheck({
				eleInsert  : ele,
				insRoot    : this.insRoot,
				insCurrent : this,
				idSelf     : this.idSelf + 'FormCheck' + obj.arr[i].id,
				allot      : this[strAllot](),
				vars       : obj.arr[i].varsFormCheck
			});
			this._varsDetailFormCheck.push({
				id           : obj.arr[i].id,
				insFormCheck : insFormCheck
			});
		}
	},

	/**
	 *
	*/
	_getDetailFormCheckAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent.insCurrent;
			if (obj.from == '_mousedownBtn') insCurrent._setFormCheckMenu({vars : obj.vars});
		};

		return allot;
	},

	/**
	 *
	*/
	insFormCheckMenu : null,
	_setFormCheckMenu : function(obj)
	{
		var cut = obj.vars.vars.varsContext;
		var dataStyle = this._getFormCheckMenuStyle({arr : this.insDetail.insView.vars.varsDetail});
		cut.varsStatus.numTop = $(this.insWindow.idWindow).offsetTop + dataStyle.numTop;
		cut.varsStatus.numLeft = $(this.insWindow.idWindow).offsetLeft + dataStyle.numLeft;

		this.insFormCheckMenu = new Code_Lib_Context({
			insRoot    : this.insRoot,
			eleInsert  : $(this.insRoot.vars.varsSystem.id.root),
			insCurrent : this,
			idSelf     : this.idSelf + 'Menu',
			allot      : this._getFormCheckMenuAllot(),
			vars       : cut
		});
	},

	/**
	 *
	*/
	_getFormCheckMenuStyle : function(obj)
	{
		var data = {
			numTop  : 0,
			numLeft : 0
		};

		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormCheck) continue;
			if (obj.arr[i].id == 'JsonPermitHistory') {
				data.numTop = this.insDetail.insView.eleWrap.down('.codeLibViewLineContent', this._varsContent.numPermit).offsetTop
							- this.insDetail.insView.insFormat.eleTemplate.body.scrollTop;
				data.numLeft = this.insDetail.insView.eleWrap.down('.codeLibViewLineContent', this._varsContent.numPermit).offsetLeft;
			}
		}

		return data;
	},

	/**
	 *
	*/
	_getFormCheckMenuAllot : function()
	{
		var allot = function(obj) {
			var insCurrent = obj.insCurrent;

		};

		return allot;
	},

	/**
	 *
	*/
	_eventRemoveDetailFormCheck : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormCheck) continue;
			if (this._varsDetailFormCheck[num]) {
				this._varsDetailFormCheck[num].insFormCheck.stopListener();
				num++;
			}
		}
	},

	/**
	 *
	*/
	_eventLayoutDetailContent : function()
	{
		if (!this.insDetail.varsEventList) return;
		this._varsContent.num = 0;
		this._varsContent.numPermit = 0;
		this._iniDetailFormJournal();
		this._iniDetailFormCheck();
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
		if (!this.insDetail.varsEventList) return;
		this._eventRemoveDetailFormJournal({arr : this.insDetail.insView.vars.varsDetail});
		this._eventRemoveDetailFormCheck({arr : this.insDetail.insView.vars.varsDetail});
		this.stopListener();
	},

	/**
	 *
	*/
	_getDetailChildVars : function(obj)
	{
		var insDisplay = new Code_Lib_TimeDisplay();
		var arrayNew = [];
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'StrTitle') {
				if (obj.flag == 'add') obj.arr[i].value = '';
				else if (obj.flag == 'addLogDetail') {
					if (obj.varsValue.strTitle != undefined) {
						obj.arr[i].value = obj.varsValue.strTitle;
					} else {
						obj.arr[i].value = '';
					}

				} else obj.arr[i].value = obj.vars.varsColumnDetail.strTitle;
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'FlagAttest') {
				if (obj.flag == 'add' || obj.flag == 'addLogDetail') {
					obj.arr[i].value = this._getOptionFirstValue({arr : obj.arr[i].arrayOption});

				} else {
					obj.arr[i].value = obj.vars.flagAttest;
				}
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'JsonDetail') {
				obj.arr[i].varsFormJournal.varsStatus.flagEditUse = 1;
				obj.arr[i].varsFormJournal.varsStatus.flagBtnTextUse = 0;
				if (obj.flag == 'add' || obj.flag == 'addLogDetail') {
					var varsDetail = (Object.toJSON(obj.arr[i].varsFormJournal.varsTmpl.varsDetail)).evalJSON();
					var varsDetailVarsDetail = (Object.toJSON(obj.arr[i].varsFormJournal.varsTmpl.varsDetailVarsDetail)).evalJSON();
					varsDetailVarsDetail.id = 'dummy';
					varsDetail.varsDetail.push(varsDetailVarsDetail);
					obj.arr[i].varsFormJournal.varsDetail = varsDetail;
					obj.arr[i].value = 'dummy';

				} else {
					obj.arr[i].varsFormJournal.varsDetail = obj.vars.jsonDetail.jsonDetail;
					obj.arr[i].value = 'dummy';
				}
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'ArrCommaIdAccountPermit') {
				if (obj.flag == 'add' || obj.flag == 'addLogDetail') {
					obj.arr[i].varsFormArea.varsDetail = [];
					obj.arr[i].value = 'dummy';

				} else {
					var arr = obj.vars.arrCommaIdAccountPermit;
					var arrayNewArea = [];
					for (var j = 0; j < arr.length; j++) {
						var varsDetail = (Object.toJSON(obj.arr[i].varsFormArea.templateDetail)).evalJSON();
						varsDetail.id = arr[j].id;
						varsDetail.strTitle = arr[j].strTitle;
						varsDetail.vars.idTarget = arr[j].id;
						arrayNewArea.push(varsDetail);
					}
					obj.arr[i].varsFormArea.varsDetail = arrayNewArea;
					obj.arr[i].value = 'dummy';
				}
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'NumColStampBook') {
				if (obj.flag == 'add') {

				} else if (obj.flag == 'addLogDetail') {
					if (obj.varsValue.numColStampBook != undefined) {
						obj.arr[i].value = obj.varsValue.numColStampBook;
					}

				} else {
					obj.arr[i].value = obj.vars.numColStampBook;
				}
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'NumColNumValue') {
				if (obj.flag == 'add') {

				} else if (obj.flag == 'addLogDetail') {
					if (obj.varsValue.numColNumValue != undefined) {
						obj.arr[i].value = obj.varsValue.numColNumValue;
					}

				} else {
					obj.arr[i].value = obj.vars.numColNumValue;
				}
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'NumColStrTitle') {
				if (obj.flag == 'add') {

				} else if (obj.flag == 'addLogDetail') {
					if (obj.varsValue.numColStrTitle != undefined) {
						obj.arr[i].value = obj.varsValue.numColStrTitle;
					}

				} else {
					obj.arr[i].value = obj.vars.numColStrTitle;
				}
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'ArrSpaceStrTag') {
				if (obj.flag == 'add' || obj.flag == 'addLogDetail') obj.arr[i].value = '';
				else obj.arr[i].value = obj.vars.arrSpaceStrTag;
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'NumSumMax') {
				if (obj.flag == 'add' || obj.flag == 'setFile' || obj.flag == 'addLogDetail') {
					obj.arr[i].value = 0;
					obj.arr[i].flagHideNow = 1;

				} else {
					var str = obj.vars.jsonPermitHistory.length - 1;
					if (obj.vars.jsonPermitHistory[str]) obj.arr[i].value = parseFloat(obj.vars.jsonPermitHistory[str].numSumMax);
					var numMax = obj.arr[i].value;
					var arrayNewMax = [];
					for (var j = 0; j < numMax; j++) {
						var data = {};
						var numValue = j + 1;
						var strTitle = numValue + obj.arr[i].varsTmpl.strPerson;
						data.value = numValue;
						data.strTitle = strTitle;
						arrayNewMax.push(data);
					}
					obj.arr[i].arrayOption = arrayNewMax;
					if (numMax == 0) {
						obj.arr[i].value = 0;
						obj.arr[i].flagHideNow = 1;
					}

				}
				arrayNew.push(obj.arr[i]);

			}
		}

		return arrayNew;
	},


	/**
	 *
	*/
	_updateDetailListVars : function(obj)
	{
		var objDetail = (Object.toJSON(this.vars.portal.varsDetail.templateDetail)).evalJSON();
		var varsBtn = (Object.toJSON(this.vars.portal.varsDetail.varsBtn)).evalJSON();

		var flagBtn = 0;
		if (!obj.vars.varsAuthority.flagDelete || obj.vars.flagDefault) {
			flagBtn = 1;
		}

		var objData = {
			flagMoveUse : 1,
			strTitle    : this.insDetail.vars.varsStart.strTitle,
			strClass    : obj.vars.strClass,
			vars        : {
				varsDetail : this._updateDetailListVarsChild({arr : objDetail, vars : obj.vars }),
				varsBtn    : this._updateDetailListVarsBtn({
					arr  : varsBtn,
					flag : flagBtn
				}),
				varsEdit   : this._updateDetailListVarsEdit({
					arr           : this.insDetail.vars.view.varsEdit,
					flag          : 0,
					varsAuthority : obj.vars.varsAuthority
				}),
				vars       : obj.vars
			}
		};

		return objData;
	},

	/**
	 *
	*/
	_updateDetailListVarsEdit : function(obj)
	{
		obj.arr.flagEditUse = 1;

		if (!obj.varsAuthority) {
			obj.arr.flagAddUse = 0;
			obj.arr.flagCopyUse = 0;
			obj.arr.flagEditUse = 0;

		} else {
			if (!obj.varsAuthority.flagInsert) {
				obj.arr.flagAddUse = 0;
				obj.arr.flagCopyUse = 0;
			}
			if (!obj.varsAuthority.flagUpdate) {
				obj.arr.flagEditUse = 0;
			}
		}

		return obj.arr;
	},

	/**
	 *
	*/
	_varsContent : {num : 0, numPermit : 0},
	_setDetailContent : function()
	{
		this._varsContent.num = 0;
		this._varsContent.numPermit = 0;
		this._iniDetailFormJournal();
		this._iniDetailFormCheck();
	},

	/**
	 *
	*/
	_varsDetailFormJournal : {},
	_iniDetailFormJournal : function()
	{
		this._varsDetailFormJournal = {};
		this._setDetailFormJournal({
			arr : this.insDetail.insView.vars.varsDetail
		});

	},

	/**
	 *
	*/
	_setDetailFormJournal : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormJournal) continue;
			var ele = this.insDetail.insView.eleWrap.down('.codeLibViewLineContent', this._varsContent.num);
			this._varsContent.num++;
			this.insFormJournal.iniLoad({
				eleInsert  : ele,
				insRoot    : this.insRoot,
				insCurrent : this,
				idSelf     : this.idSelf + 'FormJournal' + obj.arr[i].id,
				allot      : this._getDetailFormJournalAllot(),
				vars       : obj.arr[i].varsFormJournal
			});
			this._varsDetailFormJournal = {
				id             : obj.arr[i].id,
				insFormJournal : this.insFormJournal
			};
		}
	},

	/**
	 *
	*/
	_getDetailFormJournalAllot : function()
	{
		var allot = function(obj) {
			var insCurrent = obj.insCurrent;
			if (obj.from == '_getEditVars') {
				var numLeft = insCurrent.insDetail.insView.insFormat.eleTemplate.body.scrollLeft;
				var numTop = insCurrent.insDetail.insView.insFormat.eleTemplate.body.scrollTop;
				var data = {
					numLeft : numLeft,
					numTop  : numTop
				};

				return data;

			} else if (obj.from == '_checkTextBtn') {
				insCurrent.eventTextBtnJournal({vars : obj.vars});
			}

		};

		return allot;
	},

	/**
	 *
	*/
	eventTextBtnJournal : function(obj)
	{
		var strDebit = 'Debit';
		if (!obj.vars.vars.flagDebit) {
			strDebit = 'Credit';
		}
		if (obj.vars.vars.flagRow == 'summary') {
			var numValue = obj.vars.vars.varsSummary['numSum' + strDebit];
			var str = 'numValue';
			var temp = {};
			temp.vars = {};
			temp.vars[str] = numValue;
			temp.vars.idTarget = str;
			this.bootAutoSearch({vars : temp});

		} else if (obj.vars.vars.flagRow == 'mainAccount') {
			var idAccountTitle = obj.vars.vars['arr' + strDebit].idAccountTitle;
			var str = 'arrCommaIdAccountTitle' + strDebit;
			var temp = {};
			temp.vars = {};
			temp.vars[str] = idAccountTitle;
			temp.vars.idTarget = str;
			this.bootAutoSearch({vars : temp});

		} else if (obj.vars.vars.flagRow == 'subSystem') {
			if (obj.vars.vars.flagCol == 'key') {
				var idSubAccountTitle = obj.vars.vars['arr' + strDebit].idSubAccountTitle;
				var str = 'arrCommaIdSubAccountTitle' + strDebit;
				var temp = {};
				temp.vars = {};
				temp.vars[str] = idSubAccountTitle;
				temp.vars.idTarget = str;
				this.bootAutoSearch({vars : temp});

			} else if (obj.vars.vars.flagCol == 'value') {
				var idDepartment = obj.vars.vars['arr' + strDebit].idDepartment;
				var str = 'arrCommaIdDepartment' + strDebit;
				var temp = {};
				temp.vars = {};
				temp.vars[str] = idDepartment;
				temp.vars.idTarget = str;
				this.bootAutoSearch({vars : temp});
			}
			/*
			 * 20191001 start
			*/
		} else if (obj.vars.vars.flagRow == 'mainConsumptionTax') {
			if (obj.vars.vars.flagCol == 'key') {
				var numRateConsumptionTax = obj.vars.vars['arr' + strDebit].numRateConsumptionTax;
				var str = 'arrCommaRateConsumptionTax' + strDebit;
				var temp = {};
				temp.vars = {};
				temp.vars[str] = numRateConsumptionTax;
				temp.vars.idTarget = str;
				this.bootAutoSearch({vars : temp});
			}
			/*
			 * 20191001 end
			*/
		} else if (obj.vars.vars.flagRow == 'subConsumptionTax') {
			if (obj.vars.vars.flagCol == 'key') {
				var flagConsumptionTaxRule = obj.vars.vars['arr' + strDebit].flagConsumptionTaxRule;
				var str = 'arrCommaConsumptionTax' + strDebit;
				var temp = {};
				temp.vars = {};
				temp.vars[str] = flagConsumptionTaxRule;
				temp.vars.idTarget = str;
				this.bootAutoSearch({vars : temp});

			} else if (obj.vars.vars.flagCol == 'value') {
				var flagConsumptionTaxWithoutCalc = obj.vars.vars['arr' + strDebit].flagConsumptionTaxWithoutCalc;
				var str = 'arrCommaConsumptionTaxWithoutCalc' + strDebit;
				var temp = {};
				temp.vars = {};
				temp.vars[str] = flagConsumptionTaxWithoutCalc;
				temp.vars.idTarget = str;
				this.bootAutoSearch({vars : temp});
			}
		}
	},

	/**
	 *
	*/
	_eventRemoveDetailFormJournal : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormJournal) continue;
			if (this._varsDetailFormJournal) {
				this._varsDetailFormJournal.insFormJournal.stopListener();
			}
		}
	},

	/**
	 *
	*/
	_updateDetailListVarsChild : function(obj)
	{
		var insDisplay = new Code_Lib_TimeDisplay();
		var insEscape = new Code_Lib_Escape();
		var arrayNew = [];

		for (var i = 0; i < obj.arr.length; i++) {
			var id = insEscape.strLowCapitalize({data : obj.arr[i].id});
			id.match(/^dummy(.*?)$/);
			var idTarget = insEscape.strLowCapitalize({data : RegExp.$1});
			if (obj.arr[i].id == 'StampRegister') {
				var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.vars.stampRegister * 1000});
				obj.arr[i].value = insDisplay.get({flagType : 1, vars : objTime});
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'StampUpdate') {
				var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.vars.stampUpdate * 1000});
				obj.arr[i].value = insDisplay.get({flagType : 1, vars : objTime});
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'DummyInfo') {
				if (obj.vars.vars.flagIdLost
					 || obj.vars.vars.flagPermitLost
				) {
					obj.arr[i].value = '';
					var arr = [];
					if (obj.vars.vars.flagIdLost || obj.vars.vars.flagPermitLost) {
						if (obj.vars.vars.flagIdLost) {
							arr.push(obj.arr[i].varsTmpl.strIdLost);
						}
						if (obj.vars.vars.flagPermitLost) {
							arr.push(obj.arr[i].varsTmpl.strPermitLost);
						}
					}
					obj.arr[i].value = arr.join('<br>');
					arrayNew.push(obj.arr[i]);
				}

			} else if (obj.arr[i].id == 'DummyStrTitle') {
				obj.arr[i].value = obj.vars.strTitle;
				var temp = {};
				temp.id = idTarget;
				temp.strTitle = obj.arr[i].value;
				temp.vars = {};
				temp.vars[idTarget] = obj.vars.vars[idTarget];
				temp.vars.idTarget = idTarget;
				obj.arr[i].varsTextBtn.push(temp);
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'JsonDetail') {
				obj.arr[i].varsFormJournal.varsDetail = obj.vars.jsonDetail.jsonDetail;
				obj.arr[i].value = 'dummy';
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'DummyFlagAttest'
				|| obj.arr[i].id == 'DummyNumColStrTitle'
				|| obj.arr[i].id == 'DummyNumColNumValue'
				|| obj.arr[i].id == 'DummyNumColStampBook'
			) {
				obj.arr[i].value = obj.vars.varsColumnDetail[idTarget];
				var temp = {};
				temp.id = idTarget;
				temp.strTitle = obj.arr[i].value;
				temp.vars = {};
				temp.vars[idTarget] = obj.vars.vars[idTarget];
				temp.vars.idTarget = idTarget;
				obj.arr[i].varsTextBtn.push(temp);
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'ArrSpaceStrTag') {
				obj.arr[i].value = (obj.vars.arrSpaceStrTag)? obj.vars.arrSpaceStrTag : '-';
				if (obj.arr[i].value == '-') {
					obj.arr[i].varsTextBtn = null;
					arrayNew.push(obj.arr[i]);
					continue;
				}
				for (var j = 0; j < obj.vars.vars.arrSpaceStrTag.length; j++) {
					var str = obj.vars.vars.arrSpaceStrTag[j];
					if (str === '') {
						continue;
					}
					var temp = {};
					temp.id = obj.arr[i].id + '_' + j;
					temp.strTitle = str;
					temp.vars = {};
					temp.vars.strTag = str;
					temp.vars.idTarget = obj.arr[i].id;
					obj.arr[i].varsTextBtn.push(temp);
				}
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'Id') {
				obj.arr[i].value = obj.vars.varsColumnDetail[id];
				var temp = {};
				temp.id = obj.arr[i].id;
				temp.strTitle = obj.arr[i].value;
				temp.vars = {};
				temp.vars[id] = obj.vars.vars[id];
				temp.vars.idTarget = obj.arr[i].id;
				obj.arr[i].varsTextBtn.push(temp);
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'DummyJsonPermitHistory') {
				if (obj.vars.jsonPermitHistory.length) continue;
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'JsonPermitHistory') {
				if (!obj.vars.jsonPermitHistory.length) continue;
				this._updateDetailListVarsChildJsonPermitHistory({
					arr   : obj.vars.jsonPermitHistory,
					data  : obj.arr[i]
				});
				obj.arr[i].value = 'dummy';
				arrayNew.push(obj.arr[i]);

			}

		}

		return arrayNew;
	},

	/**
	 *
	*/
	_updateDetailListVarsChildJsonPermitHistory : function(obj)
	{
		var insDisplay = new Code_Lib_TimeDisplay();
		obj.data.varsFormCheck.varsDetail = [];
		for (var i = 0; i < obj.arr.length; i++) {
			var varsTmpl = (Object.toJSON(obj.data.varsFormCheck.tmplDetail)).evalJSON();
			varsTmpl.id = i;
			varsTmpl.varsColumnDetail.strNo = i + 1;

			varsTmpl.varsColumnDetail.strStatus = obj.arr[i].strStatus;
			varsTmpl.varsColumnDetail.strCodeName = obj.arr[i].strCodeName;

			var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.arr[i].stampRegister * 1000});
			varsTmpl.varsColumnDetail.stampRegister = insDisplay.get({flagType : 1, vars : objTime});

			objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.arr[i].stampPermit * 1000});
			varsTmpl.varsColumnDetail.stampPermit = insDisplay.get({flagType : 1, vars : objTime});
			if (!obj.arr[i].stampPermit) varsTmpl.varsColumnDetail.stampPermit = '-';

			varsTmpl.varsColumnDetail.strNumSum = obj.arr[i].numSumPermit
												 + ' / '
												 + obj.arr[i].numSumBack
												 + ' / '
												 + obj.arr[i].numSumMax
												 + ' / '
												 + obj.arr[i].arrIdAccountPermit.length;

			var varsTmplContext = [];
			if (obj.arr[i].arrIdAccountPermit.length) {
				varsTmplContext = (Object.toJSON(obj.data.varsFormCheck.tmplContext)).evalJSON();
				var arr = obj.arr[i].arrIdAccountPermit;
				for (var j = 0; j < arr.length; j++) {
					var varsTmplContextDetail = (Object.toJSON(obj.data.varsFormCheck.tmplContextDetail)).evalJSON();
					varsTmplContextDetail.id = j;

					var objTime = this.insRoot.insTimeZone.adjustDate({stamp : arr[j].stampRegister * 1000});
					var strStampRegister = insDisplay.get({flagType : 1, vars : objTime});
					varsTmplContextDetail.strTitle = strStampRegister + ' - ' + arr[j].strCodeName;

					if (arr[j].flagPermit == 'done') {
						varsTmplContextDetail.strClass = this.vars.varsItem.strClassImgDone;

					} else if (arr[j].flagPermit == 'back') {
						varsTmplContextDetail.strClass = this.vars.varsItem.strClassImgBack;

					} else {
						varsTmplContextDetail.strClass = this.vars.varsItem.strClassImgNone;
						varsTmplContextDetail.strTitle = arr[j].strCodeName;
					}
					varsTmplContext.varsDetail.push(varsTmplContextDetail);
				}

			} else {
				varsTmpl.varsColumnDetail.btnDetailLock = 1;
			}

			var numSumMax = obj.arr[i].arrIdAccountPermit.length - obj.arr[i].numSumBack;
			if (obj.arr[i].flagInvalid || (numSumMax < obj.arr[i].numSumMax)) {
				/*
				varsTmpl.varsColumnDetail.btnDetailLock = 1;
				*/
			}
			varsTmpl.varsContext = varsTmplContext;
			obj.data.varsFormCheck.varsDetail.push(varsTmpl);
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
	_varChild : function()
	{
		var vars = {};
		var insEscape = new Code_Lib_Escape();
		var str = this._varsChild.strChild;
		if (str) {
			str = insEscape.strLowCapitalize({data : str});
			if (this.vars.child[str]) {
				vars = (Object.toJSON(this.vars.child[str])).evalJSON();
			} else {
				vars = (Object.toJSON(this.vars.child.templateWindow)).evalJSON();
			}

		} else {
			vars = (Object.toJSON(this.vars.child.templateWindow)).evalJSON();
		}

		var strExt = this._varsChild.strExt;
		var strChild = this._varsChild.strChild;
		vars.id =  strExt + strChild;
		vars.strTitle = vars.strTitle.replace(/<%child%>/, this._varsChild.strTitleChild);
		vars.strTitle = vars.strTitle.replace(/<%parent%>/, this._varsChild.strTitleParent);
		this._varsChild.varsWindow[strExt + strChild] = vars;
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