{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_Cash = Class.create(Code_Lib_ExtPortal,
{
{/literal}
	vars : {$varsLoad},
	numNews : {$numNews},
{literal}

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
		var varsCheck = {};
		var arr = varsRule.arrAccountTitleCash.arrSelectTag;
		for (var i = 0; i < arr.length; i++) {
			varsCheck[arr[i].value] = 1;
		}
		var arrNew = [];
		var arr = varsRule.arrAccountTitle.arrSelectTag;
		for (var i = 0; i < arr.length; i++) {
			if (varsCheck[arr[i].value]) {
				continue;
			}
			arrNew.push(arr[i]);
		}
		varsRule.arrAccountTitle.arrSelectTag = arrNew;
		varsRule.arrAccountTitle.arrSelectTag.unshift(this.vars.varsItem.arrBlank);
		varsRule.arrAccountTitleCash.arrSelectTag.unshift(this.vars.varsItem.arrBlank);
		varsRule.arrDepartment.arrSelectTag.unshift(this.vars.varsItem.arrBlank);
		this.insFormJournal = new Code_Plugin_Accounting_Lib_JournalCash({varsRule : varsRule});
	},

	/**

	*/
	_flagAutoDetail : 0,
	_flagAutoSearchOver : '',
	_varsAutoSearchOver : '',
	bootAutoSearchOver : function(obj)
	{
		var insEscape = new Code_Lib_Escape();
		this._varsAutoSearchOver = {};
		this._flagAutoSearchOver = obj.flag;
		if (obj.flag == 'setFile') {
			this._setDetailChild({flag : obj.flag, vars : obj.vars});

		} else if (obj.flag == 'addFile') {
			var varsData = this.checkChildData({idTarget : this.strExt + 'Editor'});
			varsData.insClass.bootAutoSearchOver({flag : obj.flag, vars : obj.vars});

		} else if (obj.flag == 'showLog') {
			this._eventListConnect({flag : 'Reload', flagType : 'start'});

		} else if (obj.flag == 'showCash') {
			var vars = {};
			vars.vars = obj.vars;
			this._flagAutoDetail = 1;
			this.bootAutoSearch({vars : vars});

		} else if (obj.flag == 'showCashDefer') {
			var idTarget = 'Preference';
			var varsData = this.checkChildData({idTarget : this.strExt + idTarget});

			if (!varsData) {
				this._iniChild({
					strTitleParent : this.insWindow.vars.strTitle,
					strTitleChild  : this.vars.child.varsTitle[idTarget],
					strExt         : this.strExt,
					strChild       : idTarget,
					strClass       : this.strClass,
					idModule       : this.idModule,
					flagHideWindow : 1,
					insBack        : this,
					strBackFunc    : 'eventAutoSearchOver'
				});

			} else {

				if (varsData.insWindow.vars.flagHideNow) {
					varsData.insWindow.updateHide({ flagEffect : 1 });
				} else {
					varsData.insWindow.setZIndex();
				}

				varsData.insClass.bootAutoSearchOver({flag : obj.flag});
			}

		} else if (obj.flag == 'showFile') {
			var flag = 'File';
			this._varsAutoSearchOver.flag = obj.flag;
			this._varsAutoSearchOver.vars = obj.vars;
			var varsData = this.insTop.checkChildData({idTarget : flag});
			if (!varsData) {
				var idTarget = insEscape.strLowCapitalize({data : flag});
				this.insTop.iniAutoBoot({
					idTarget     : idTarget + 'Window',
					insBack      : this,
					strBackFunc  : 'eventAutoSearchOver'
				});

			} else {
				if (varsData.insWindow.vars.flagHideNow) {
					varsData.insWindow.updateHide({ flagEffect : 1 });
				} else {
					varsData.insWindow.setZIndex();
				}
				this.eventAutoSearchOver();
			}

		} else {

			this._flagAutoDetail = (obj.flagDetail)? 1 : 0;
			this.bootAutoSearch(obj);
		}
	},

	eventAutoSearchOver : function()
	{
		if (this._flagAutoSearchOver == 'showCashDefer') {
			var varsData = this.checkChildData({idTarget : 'CashPreference'});
			if (varsData) {
				varsData.insClass.bootAutoSearchOver({flag : this._flagAutoSearchOver});
			}

		} else if (this._flagAutoSearchOver == 'showFile') {
			var varsData = this.insTop.checkChildData({idTarget : 'File'});
			if (varsData) {
				varsData.insClass.bootAutoSearchOver(this._varsAutoSearchOver);
			}

		}
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
		var flagDetail = obj.flag;

		if (obj.flag == 'add') {
			varsDetail = this._getDetailChildVars({
				arr  : objDetail,
				flag : obj.flag
			});

		} else if (obj.flag == 'setFile') {
			varsIni = this._getDetailChildVars({
				arr  : (Object.toJSON(objDetail)).evalJSON(),
				flag : 'add'
			});
			varsDetail = this._getDetailChildVars({
				arr     : objDetail,
				flag    : obj.flag,
				arrFile : obj.vars.arrFile
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

		this._extChild({
			strTitleParent : this.insWindow.vars.strTitle,
			strTitleChild  : this.vars.child.varsTitle.editor,
			strExt         : this.strExt,
			strChild       : 'Editor',
			strClass       : this.strClass,
			idModule       : this.idModule,
			varsChild      : {
				flagType   : obj.flag,
				flagDetail : flagDetail,
				idTarget   : idTarget,
				varsDetail : varsDetail,
				varsIni    : varsIni,
				vars       : vars
			}
		});

	},

	_flagAutoSearch : '',
	_varsAutoData : {},
	_checkAutoSearch : function(obj)
	{
		var insEscape = new Code_Lib_Escape();
		this._flagAutoSearch = obj.idTarget;

		if (this._flagAutoSearch == 'Ledger') {
			this._varsAutoData = {
				flagFiscalPeriod  : 'f1',
				idDepartment      : obj.idDepartment,
				idAccountTitle    : obj.idAccountTitle,
				idSubAccountTitle : obj.idSubAccountTitle
			};

			var varsData = this.insTop.checkChildData({idTarget : obj.idTarget});
			if (!varsData) {
				var idTarget = insEscape.strLowCapitalize({data : obj.idTarget});
				this.insTop.iniAutoBoot({
					idTarget     : idTarget + 'Window',
					insBack      : this,
					strBackFunc  : 'eventAutoSearch'
				});

			} else {

				if (varsData.insWindow.vars.flagHideNow) {
					varsData.insWindow.updateHide({ flagEffect : 1 });

				} else {
					varsData.insWindow.setZIndex();
				}

				this.eventAutoSearch();
			}

		} else if (this._flagAutoSearch == 'Log') {
			this._varsAutoData = {
				vars       : {
					vars : {
						idTarget : 'idLog',
						idLog    : obj.idLog
					}
				},
				flagDetail : 1
			};

			var varsData = this.insTop.checkChildData({idTarget : obj.idTarget});
			if (!varsData) {
				var idTarget = insEscape.strLowCapitalize({data : obj.idTarget});
				this.insTop.iniAutoBoot({
					idTarget     : idTarget + 'Window',
					insBack      : this,
					strBackFunc  : 'eventAutoSearch'
				});

			} else {

				if (varsData.insWindow.vars.flagHideNow) {
					varsData.insWindow.updateHide({ flagEffect : 1 });
				} else {
					varsData.insWindow.setZIndex();
				}

				this.eventAutoSearch();
			}

		} else if (this._flagAutoSearch == 'File') {
			this._varsAutoData = {
				flag       : 'showLog',
				idLogFile  : obj.idLogFile
			};

			var varsData = this.insTop.checkChildData({idTarget : obj.idTarget});
			if (!varsData) {
				var idTarget = insEscape.strLowCapitalize({data : obj.idTarget});
				this.insTop.iniAutoBoot({
					idTarget       : idTarget + 'Window',
					flagHideWindow : 1,
					insBack        : this,
					strBackFunc    : 'eventAutoSearch'
				});

			} else {

				if (varsData.insWindow.vars.flagHideNow) {
					varsData.insWindow.updateHide({ flagEffect : 1 });

				} else {
					varsData.insWindow.setZIndex();
				}

				this.eventAutoSearch();
			}
		}


	},

	eventAutoSearch : function()
	{
		if (this._flagAutoSearch == 'Ledger'
			|| this._flagAutoSearch == 'File'
			|| this._flagAutoSearch == 'Log'
		) {
			var varsData = this.insTop.checkChildData({idTarget : this._flagAutoSearch});
			varsData.insClass.bootAutoSearchOver(this._varsAutoData);
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
						strTitleParent : insCurrent.insWindow.vars.strTitle,
						strTitleChild  : insCurrent.vars.child.varsTitle[obj.vars.id],
						strExt         : insCurrent.strExt,
						strChild       : obj.vars.id,
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
					insCurrent._eventListConnect({flag : obj.vars.id, flagType : insCurrent.insList.vars.varsStatus.flagReloadNow});

				} else if (obj.vars.id == 'Preference' || obj.vars.id == 'Search') {
					insCurrent._iniChild({
						strTitleParent : insCurrent.insWindow.vars.strTitle,
						strTitleChild  : insCurrent.vars.child.varsTitle[obj.vars.id],
						strExt         : insCurrent.strExt,
						strChild       : obj.vars.id,
						strClass       : insCurrent.strClass,
						idModule       : insCurrent.idModule
					});

				} else if (obj.vars.id == 'Output') {
					insCurrent._eventListConnect({flag : obj.vars.id, flagType : insCurrent.insList.vars.varsStatus.flagOutputNow});

				} else if (obj.vars.id == 'Print') {
					insCurrent._eventListConnect({flag : obj.vars.id, flagType : insCurrent.insList.vars.varsStatus.flagPrintNow});

				} else if (obj.vars.id == 'Import') {
					if (insCurrent.insList.vars.varsStatus.flagImportNow == 'itemAll') {
						insCurrent._iniChild({
							strTitleParent : insCurrent.insWindow.vars.strTitle,
							strTitleChild  : insCurrent.vars.child.varsTitle.itemAll,
							strExt         : 'LogImportItem',
							strChild       : '',
							strClass       : insCurrent.strClass,
							idModule       : insCurrent.idModule
						});

					} else if (insCurrent.insList.vars.varsStatus.flagImportNow == 'listAll') {
						insCurrent._iniChild({
							strTitleParent : insCurrent.insWindow.vars.strTitle,
							strTitleChild  : insCurrent.vars.child.varsTitle.listAll,
							strExt         : 'LogImportList',
							strChild       : '',
							strClass       : insCurrent.strClass,
							idModule       : insCurrent.idModule
						});

					} else if (insCurrent.insList.vars.varsStatus.flagImportNow == 'mailAll') {
						insCurrent._preEventLayout({flag : 'reset'});
						insCurrent._eventListConnect({flag : obj.vars.id});
					}
				}

			} else if (obj.from == 'list-_mousedownMenu') {
				if (obj.vars.id == 'Switch') {
					return insCurrent.insList.vars.varsStatus.flagNow;

				} else if (obj.vars.id == 'Output') {
					return insCurrent.insList.vars.varsStatus.flagOutputNow;

				} else if (obj.vars.id == 'Print') {
					return insCurrent.insList.vars.varsStatus.flagPrintNow;

				} else if (obj.vars.id == 'Import') {
					return insCurrent.insList.vars.varsStatus.flagImportNow;

				} else if (obj.vars.id == 'Reload') {
					return insCurrent.insList.vars.varsStatus.flagReloadNow;
				}

			} else if (obj.from == 'list-_mousedownLine') {
				if (obj.varsTarget == 'Switch') {
					return insCurrent.insList.eventTool({idTarget : obj.vars});

				} else if (obj.varsTarget) {
					if (obj.varsTarget == 'Import') {
						insCurrent.insList.eventTool({idTarget : obj.vars, flagStr : obj.varsTarget});
						if (obj.vars == 'itemAll') {
							insCurrent._iniChild({
								strTitleParent : insCurrent.insWindow.vars.strTitle,
								strTitleChild  : insCurrent.vars.child.varsTitle.itemAll,
								strExt         : 'LogImportItem',
								strChild       : '',
								strClass       : insCurrent.strClass,
								idModule       : insCurrent.idModule
							});

						} else if (obj.vars == 'listAll') {
							insCurrent._iniChild({
								strTitleParent : insCurrent.insWindow.vars.strTitle,
								strTitleChild  : insCurrent.vars.child.varsTitle.listAll,
								strExt         : 'LogImportList',
								strChild       : '',
								strClass       : insCurrent.strClass,
								idModule       : insCurrent.idModule
							});

						} else if (obj.vars == 'mailAll') {
							insCurrent.insList.eventTool({idTarget : obj.vars, flagStr : obj.varsTarget});
							insCurrent._eventListConnect({flag : obj.varsTarget, flagType : obj.vars});
						}
						return;
					}
					insCurrent.insList.eventTool({idTarget : obj.vars, flagStr : obj.varsTarget});
					insCurrent._eventListConnect({flag : obj.varsTarget, flagType : obj.vars});
					return;
				}

			} else if (obj.from == 'detail-_mousedownLine') {
				insCurrent._eventDetailVersion({vars : obj.vars, idTarget : obj.vars});

			} else if (obj.from == 'detail-_mousedownNavi') {

				if (obj.vars.id == 'Reload') {
					insCurrent._preEventLayout({flag : 'reset'});
					insCurrent._eventDetailConnect({flag : 'reload'});

				} else if (obj.vars.id == 'Add'
						|| obj.vars.id == 'Copy'
						|| obj.vars.id == 'Edit'
				) {
					insCurrent._setDetailChild({flag : obj.vars.id.toLowerCase()});

				} else if (obj.vars.id == 'Preference') {
					insCurrent._iniChild({
						strTitleParent : insCurrent.insWindow.vars.strTitle,
						strTitleChild  : insCurrent.vars.child.varsTitle[obj.vars.id],
						strExt         : obj.vars.id,
						strChild       : '',
						strClass       : insCurrent.strClass,
						idModule       : insCurrent.idModule
					});
				}
			}
		};

		return allot;
	},

	/**
		{
		strFunc  : ''
		}
	 */
	_getJsonStamp : function(obj)
	{
		var str = this.strClass
			+ '-' + this.idModule
			+ '-' + this.strExt
			+ '-' + this.strChild
			+ '-' + obj.strFunc;

		var stamp = (this._varsStamp[str])? this._varsStamp[str] : 0;
		var objStamp = {
			id        : str,
			stamp     : stamp,
			stampRule : this.vars.varsRule.stampUpdate
		};
		var jsonStamp = (Object.toJSON(objStamp));

		return jsonStamp;
	},

	/**
	 *
	*/
	_varsSearch : {
		flagReload : 0,
		numLotNow  : 0,
		ph : {
			flagApply  : 'none',
			arrWhere   : [],
			arrOrder   : {}
		}
	},

	/**
	 *
	*/
	_resetSearch : function()
	{
		this._varsSearch = {
			flagReload : 0,
			numLotNow  : 0,
			ph : {
				flagApply  : 'none',
				arrWhere   : [],
				arrOrder   : {}
			}
		};
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
	_extList : function()
	{
		this._updateVarsBtnHide({
			arr    : this.vars.portal.varsList.varsDetail,
			arrBtn : this.vars.portal.varsList.varsBtn
		});
		this._setList();
		this._setListStart();
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
	_getListAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			var array = obj.from.split('-');
			if (array[0] == 'table') {
				if (array[1] == '_mousedownMove') insCurrent.insNavi.eventMove({vars : obj.vars});
				else if (array[1] == '_dblclickTitle') insCurrent._eventDetailListColumn({vars : obj.vars});
				else if (array[1] == '_mousedownBtn') insCurrent._eventDetailList({vars : obj.vars[0]});
				else if (array[1] == 'eventPage') insCurrent._eventListConnect({vars : obj.vars, flag : 'Search'});
				else if (array[1] == 'eventBtnBottom') insCurrent._eventListConnect({flag : obj.vars.vars.vars.idTarget});

			} else if (array[0] == 'schedule') {
				if (array[1] == 'eventPage') insCurrent._eventListConnect({vars : obj.vars, flag : 'Search'});
				else if (array[1] == '_mousedownLogBtn') insCurrent._eventDetailList({vars : obj.vars.detailLog});
				else if (array[1] == 'mousedownMove') insCurrent.insNavi.eventMove({vars : obj.vars});

			} else if (array[0] == 'tableTree') {
				if (array[1] == '_mousedownBtn') insCurrent._eventDetailList({vars : obj.vars[0]});
				else if (array[1] == 'eventPage') insCurrent._eventListConnect({vars : obj.vars, flag : 'Search'});
				else if (array[1] == 'eventBtnBottom') insCurrent._eventListConnect({flag : obj.vars.vars.vars.idTarget});

			} else if (array[0] == 'tree') {
				if (array[1] == '_mousedownBtn') insCurrent._eventDetailList({vars : obj.vars});
				else if (array[1] == 'eventPage') insCurrent._eventListConnect({vars : obj.vars, flag : 'Search'});
				else if (array[1] == 'eventBtnBottom') insCurrent._eventListConnect({flag : obj.vars.vars.vars.idTarget});

			} else if (array[0] == 'resetVars') {
				insCurrent._checkListBtn({
					varsDetail : obj.vars.varsDetail,
					varsBtn    : obj.vars.varsBtn
				});
				insCurrent._checkListToolEdit({
					varsDetail : obj.vars.varsDetail,
					varsEdit   : obj.vars.varsEdit
				});
			}
		};

		return allot;
	},

	/**
	 *
	*/
	_eventDetailListColumn : function(obj)
	{

	},

	/**
	 *
	*/
	_checkListBtn : function(obj)
	{
		this._updateVarsBtnHide({
			arr     : obj.varsDetail,
			arrBtn  : obj.varsBtn
		});
	},

	/**
	 *
	*/
	_updateVarsBtnHide : function(obj)
	{
		var flagDelete = 0;
		var flagPay = 0;
		var flagWrite = 0;

		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].flagBtnDelete) flagDelete = 1;
			if (obj.arr[i].flagBtnPay) flagPay = 1;
			if (obj.arr[i].flagBtnWrite) flagWrite = 1;
		}

		for (var i = 0; i < obj.arrBtn.length; i++) {
			obj.arrBtn[i].flagUse = 0;
			if (obj.arrBtn[i].vars.idTarget == 'Delete' && flagDelete) obj.arrBtn[i].flagUse = 1;
			else if (obj.arrBtn[i].vars.idTarget == 'Pay' && flagPay) obj.arrBtn[i].flagUse = 1;
			else if (obj.arrBtn[i].vars.idTarget == 'Write' && flagWrite) obj.arrBtn[i].flagUse = 1;
		}
	},

	/**
	 *
	*/
	eventImportLog : function()
	{
		this._varsSearch.numLotNow = 0;
		this._eventListConnect({flag : 'Reload'});
	},

	/**
	 *
	*/
	_eventListConnect : function(obj)
	{
		if (obj.flag == 'Reload') {
			if (obj.flagType == 'start') {
				this._resetSearch();
			}
			this._eventSearch({
				numLotNow : this._varsSearch.numLotNow,
				ph : {
					flagApply : this._varsSearch.ph.flagApply,
					arrWhere  : this._varsSearch.ph.arrWhere,
					arrOrder  : this._varsSearch.ph.arrOrder
				}
			});

		} else if (obj.flag == 'Output') {
			var vars = {};
			vars.FlagType = obj.flagType;
			this._eventSearch({
				numLotNow : this._varsSearch.numLotNow,
				ph : {
					flagApply : this._varsSearch.ph.flagApply,
					arrWhere  : this._varsSearch.ph.arrWhere,
					arrOrder  : this._varsSearch.ph.arrOrder
				}
			});
			this._eventValue({
				vars     : vars,
				idTarget : ''
			});

		} else if (obj.flag == 'Print') {
			this._eventSearch({
				numLotNow : this._varsSearch.numLotNow,
				ph : {
					flagApply : this._varsSearch.ph.flagApply,
					arrWhere  : this._varsSearch.ph.arrWhere,
					arrOrder  : this._varsSearch.ph.arrOrder
				}
			});

		} else if (obj.flag == 'Search') {
			this._eventSearch({
				numLotNow : obj.vars.numLotNow,
				ph : {
					flagApply : this._varsSearch.ph.flagApply,
					arrWhere  : this._varsSearch.ph.arrWhere,
					arrOrder  : this._varsSearch.ph.arrOrder
				}
			});

		} else if (obj.flag == 'Delete' || obj.flag == 'Pay' || obj.flag == 'Write') {
			var arrId = this.insList.getTableCheckBoxArrId();
			if (!arrId.length) {
				alert(this.insRoot.vars.varsSystem.str.selectRequire);
				this.insList.eventNavi({strTitle : null, strClass : null});
				return;
			}
			var arrNew = [];
			var arr = arrId;
			for (var i = 0; i < arr.length; i++) {
				var arrTemp = arr[i].split('_');
				arrNew.push(arrTemp[0]);
			}
			this._eventValue({
				vars     : arrNew,
				idTarget : ''
			});

		} else if (obj.flag == 'Import') {
			this._eventSearch({
				numLotNow : this._varsSearch.numLotNow,
				ph        : {
					flagApply : this._varsSearch.ph.flagApply,
					arrWhere  : this._varsSearch.ph.arrWhere,
					arrOrder  : this._varsSearch.ph.arrOrder
				}
			});
			this.insLayout.updateTool({flagLock : 1, from : 'list', idTarget : 'Import'});

		}

		this._varsListConnect = obj;
		this._sendListConnect();
	},

	/**
	 *
	*/
	_eventListConnectSuccess : function(obj)
	{
		if (obj.json.flag == 1){
			if (this._varsListConnect.flag == 'Search'
				|| this._varsListConnect.flag == 'Reload'
				|| this._varsListConnect.flag == 'Import'
			) {
				this.insLayout.updateTool({flagLock : 0, from : 'list', idTarget : 'Reload'});
				this.insList.resetVars({vars : obj.json.data, numLotNow : this._varsSearch.numLotNow});
				this.insList.eventNavi({strTitle : null, strClass : null});

				if (this._varsListConnect.flag == 'Reload') {
					if (this._flagAutoDetail) {
						if (obj.json.data.numRows) {
							this._eventDetailList({vars : obj.json.data.varsDetail[0]});
							this._flagAutoDetail = 0;
						}
					}
				}

			} else if (this._varsListConnect.flag == 'Delete'
				 || this._varsListConnect.flag == 'Pay'
				 || this._varsListConnect.flag == 'Write'
			) {
				this.insList.resetVars({vars : obj.json.data, numLotNow : 0});
				this.insList.eventNavi({strTitle : null, strClass : null});
				this._resetDetail();

			} else if (this._varsListConnect.flag == 'Print' ) {
				this.insRoot.setPrint({data : obj.json.data, insCurrent : this, strFunc : 'eventListConnectSuccessPrint'});
			}

		} else if (obj.json.flag == 10) {
			if (this._varsListConnect.flag == 'Search'
				|| this._varsListConnect.flag == 'Reload'
			) {
				this.insList.eventNavi({strTitle : null, strClass : null});
			}
		} else if (obj.json.flag == 40) {
			this.insList.resetVars({vars : obj.json.data, numLotNow : this._varsSearch.numLotNow});
			this.insList.eventNavi({strTitle : null, strClass : null});

		} else {
			if (this._varsListConnect.flag == 'Write') {
				this._setDetailComment({data : obj.json.data.strComment});
				return;
			}
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
	_getDetailAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == 'eventRemove-detail') insCurrent._eventRemoveDetailContent();
			else if (obj.from == '_mousedownMove') insCurrent.insNavi.eventMove({vars : obj.vars});
			else if (obj.from == 'view-preEventLayout') insCurrent._preEventLayoutDetailContent();
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
		vars : {
			vars : {
				idTarget : '', column
				data : multi
			}
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
		if (obj.vars.vars.idTarget == 'idLog') {
			this._checkAutoSearch({
				idTarget : 'Log',
				idLog    : obj.vars.vars[obj.vars.vars.idTarget]
			});
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

		} else if (str == 'id' || str == 'idLogCash') {
			varsTmpl.flagType = 'num';
			varsTmpl.strColumn = 'idLogCash';
			varsTmpl.value = obj.vars.vars[str];
			varsData.push(varsTmpl);

		} else if (str == 'strTitle') {
			varsTmpl.flagType = 'str';
			varsTmpl.strColumn = str;
			varsTmpl.value = obj.vars.vars[str];
			varsData.push(varsTmpl);

		} else if (str.match(/^stamp/)) {
			varsTmpl.flagType = 'stamp';
			varsTmpl.strColumn = str;
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

		} else if (str.match(/^arrCommaRateConsumptionTax/)) {
			varsTmpl.flagType = 'commaTaxRate';
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

		} else if (str.match(/^arrComma/)) {
			varsTmpl.flagType = 'comma';
			varsTmpl.strColumn = str;
			varsTmpl.flagCondition = 'like';
			varsTmpl.value = ',' + obj.vars.vars[str] + ',';
			varsData.push(varsTmpl);

		} else if (str == 'flagIn') {
			varsTmpl.flagType = 'flagIn';
			varsTmpl.strColumn = str;
			varsTmpl.value = obj.vars.vars[str];
			varsData.push(varsTmpl);

		} else if (str == 'idAccount') {
			varsTmpl.flagType = 'account';
			varsTmpl.strColumn = str;
			varsTmpl.value = obj.vars.vars[str];
			varsData.push(varsTmpl);

		} else if (str.match(/^numValue/)) {
			varsTmpl.flagType = 'num';
			varsTmpl.strColumn = str;
			varsTmpl.value = obj.vars.vars[str];
			varsData.push(varsTmpl);
		}

		this._varsSearch.ph.arrWhere = varsData;
		if (str == 'strStatus') {
			this._varsSearch.ph.flagApply = 'none';
			if (obj.vars.vars.flagRemove) {
				this._varsSearch.ph.flagApply = 'remove';

			} else {
				if (!obj.vars.vars.flagPay) {
					if (obj.vars.vars.flagStampBook) {
						this._varsSearch.ph.flagApply = 'over';
					} else {
						this._varsSearch.ph.flagApply = 'apply';
					}

				} else {
					this._varsSearch.ph.flagApply = 'done';
				}
			}
			this._varsSearch.ph.arrWhere = [];

		} else {
			this._varsSearch.ph.flagApply = 'none';
		}
		this._eventListConnect({flag : flag});
	},

	/**
	 *
	*/
	_eventDetailConnect : function(obj)
	{
		if (obj.flag == 'reload' || obj.flag == 'delete' || obj.flag == 'pay' || obj.flag == 'write') {
			this._eventValue({
				vars     : '',
				idTarget : this.insDetail.varsEventList.vars.vars.idTarget
			});

		} else if (obj.flag == 'edit') {
			if (this.insDetail.checkForm({flagType : 'common'})) return;
			this._eventValue({
				vars     : this.insDetail.getFormValue(),
				idTarget : obj.idTarget
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
			if (this._varsDetailConnect.flag == 'reload'
				 || this._varsDetailConnect.flag == 'pay'
				 || this._varsDetailConnect.flag == 'write'
			) {
				this.eventDetailConnectSuccessDetailUpdate(obj);
				if (obj.json.stamp) {
					var data = (Object.toJSON(obj.json)).evalJSON();
					if (obj.json.stamp.id) this._varsStampCheck[obj.json.stamp.id] = data;
				}

			} else if (this._varsDetailConnect.flag == 'delete') {
				this.eventDetailConnectSuccessListUpdateDetailReset(obj);

			}

		} else if (obj.json.flag == 10) {
			if (obj.json.stamp) {
				this.eventDetailConnectSuccessDetailUpdate({json : this._varsStampCheck[obj.json.stamp.id]});
			}

		} else if (obj.json.flag == 40) {
			this.eventDetailConnectSuccessLost(obj);

		} else {
			if (this._varsDetailConnect.flag == 'write') {
				this._setDetailComment({data : obj.json.data.strComment});
				return;
			}
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
	_varsDetailComment : '',
	_setDetailComment : function(obj)
	{
		this._varsDetailComment = obj.data;
		this.insDetail.eventList({
			strTitle : this.vars.portal.varsDetail.varsComment.varsStatus.strTitle,
			strClass : this.vars.portal.varsDetail.varsComment.varsStatus.strClass,
			vars     : {
				varsDetail : [],
				varsBtn    : [],
				varsEdit   : {},
				vars       : {}
			}
		});
		var ele = this.insDetail.insView.eleWrap.down('.codeLibViewLineWrap', 0);
		ele.insert(obj.data);
	},

	/**
	 *
	*/
	_varsContent : {num : 0, numPermit : 0},
	_setDetailContent : function()
	{
		this._varsDetailComment = '';
		this._varsContent.num = 0;
		this._varsContent.numPermit = 0;
		this._iniDetailFormJournal();
		this._iniDetailFormCheck();
		this._iniDetailSpace();
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
	_varsDetailSpace : {},
	_iniDetailSpace : function()
	{
		this._varsDetailSpace = {};
		this._setDetailSpace({arr : this.insDetail.insView.vars.varsDetail});
	},

	/**
	 *
	*/
	_setDetailSpace : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'JsonChargeHistory') {
				var ele = this.insDetail.insView.eleWrap.down('.codeLibViewLineContent', this._varsContent.num);
				this._varsContent.num++;
				ele.insert(obj.arr[i].strHtml);
				var num = 1;
				var arr = obj.arr[i].jsonChargeHistory;
				for (var j = 0; j < arr.length; j++) {
					var idTr = this.idSelf + obj.arr[i].id + '_Tr' + num;
					var idTd = idTr + '_Td' + 'idAccount';
					$(idTd).innerHTML = '';
					var insBtn = new Code_Lib_Btn();
					var vars = {};
					vars.idTarget = 'idAccount';
					vars.idAccount = arr[j].idAccount;
					insBtn.iniBtnTextTarget({
						eleInsert  : $(idTd),
						id         : idTd + '_' + num,
						strFunc    : '_checkDetailContentTextBtn',
						strTitle   : arr[j].strCodeName,
						insCurrent : this,
						vars       : vars
					});
					this._setListener({ins : insBtn});
					num++;
				}

			} else if (obj.arr[i].id == 'JsonWriteHistory') {
				var ele = this.insDetail.insView.eleWrap.down('.codeLibViewLineContent', this._varsContent.num);
				this._varsContent.num++;
				var arr = obj.arr[i].jsonWriteHistory;
				if (!arr.length) {
					ele.insert('-');
					continue;
				}
				ele.insert(obj.arr[i].strHtml);
				var arr = obj.arr[i].jsonWriteHistory;
				var arrStr = ['idLog'];
				for (var k = 0; k < arrStr.length; k++) {
					var num = 1;
					for (var j = 0; j < arr.length; j++) {
						var idTr = this.idSelf + obj.arr[i].id + '_Tr' + num;
						var idTd = idTr + '_Td' + arrStr[k];
						if (!$(idTd)) {
							break;
						}
						$(idTd).innerHTML = '';
						var insBtn = new Code_Lib_Btn();
						var vars = {};
						vars.idTarget = arrStr[k];
						vars[arrStr[k]] = arr[j][arrStr[k]];
						insBtn.iniBtnTextTarget({
							eleInsert  : $(idTd),
							id         : idTd + '_' + num,
							strFunc    : '_checkDetailContentTextBtn',
							strTitle   : (arrStr[k] == 'idAccount')? arr[j].strCodeName : arr[j][arrStr[k]],
							insCurrent : this,
							vars       : vars
						});
						this._setListener({ins : insBtn});
						num++;
					}
				}
			}
		}
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
			if (obj.arr[i].id == 'JsonFile') strAllot = '_getDetailFormCheck' + obj.arr[i].id + 'Allot';
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
			if (obj.arr[i].id == 'JsonFile') {
				num = 1;
				var arr = insFormCheck.vars.varsDetail;
				for (var j = 0; j < arr.length; j++) {
					if (!arr[j].flagFileAccess) {
						break;
					}
					var id = insFormCheck.idSelf + 'Line' + arr[j].id + 'Column' + 'StrTitle';
					$(id).innerHTML = '';
					var insBtn = new Code_Lib_Btn();
					var vars = {};
					vars.idTarget = 'idLogFile';
					vars.idLogFile = arr[j].idLogFile;
					insBtn.iniBtnTextTarget({
						eleInsert  : $(id),
						id         : '',
						strFunc    : '_checkDetailContentTextBtnFile',
						strTitle   : arr[j].varsColumnDetail.strTitle,
						insCurrent : this,
						vars       : vars
					});
					this._setListener({ins : insBtn});
					num++;
				}
			}
		}
	},

	/**
	 *
	*/
	_checkDetailContentTextBtnFile : function(obj)
	{
		this.bootAutoSearchOver({flag : 'showFile', vars : obj.vars.vars})
	},

	/**
	 *
	*/
	_getDetailFormCheckJsonFileAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent.insCurrent;
			if (obj.from == '_mousedownBtn') {
				insCurrent._setFormCheckJsonFile({
					numVersion : obj.vars.vars.numVersion,
					idTarget   : obj.vars.vars.idTarget
				});
				return 1;
			}
		};

		return allot;
	},

	/**
	 *
	*/
	_setFormCheckJsonFile : function(obj)
	{
		this._eventValue({
			vars     : {
				numVersion : obj.numVersion,
				idTarget   : obj.idTarget
			},
			idTarget : this.insDetail.varsEventList.vars.vars.idTarget
		});
		var strExt = this.strExt;
		var strClass = this.strClass;
		var idModule = this.idModule;
		var strChild = this.strChild;
		var jsonValue = Object.toJSON(this._varsValue);
		var arrayKey = [], arrayValue = [];
		var jsonStamp = {};

		var strFunc = 'DetailOutput';
		jsonStamp = this._getJsonStamp({strFunc : strFunc});
		arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonValue'];
		arrayValue = [strClass, idModule, strExt, strChild, strFunc, 'slave', jsonStamp, jsonValue];

		this.insRoot.setOutput({
			querysKey   : arrayKey,
			querysValue : arrayValue,
		});
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
		if (this._varsDetailComment) {
			this._setDetailComment({data : this._varsDetailComment});
			return;
		}
		this._varsContent.num = 0;
		this._varsContent.numPermit = 0;
		this._iniDetailFormJournal();
		this._iniDetailFormCheck();
		this._iniDetailSpace();
	},

	/**
	 *
	*/
	_preEventLayoutDetailContent : function()
	{
		if (!this.insDetail.varsEventList) return;
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
	_eventDetailVersion : function(obj)
	{
		if (obj.idTarget == 'search') {
			this._eventDetailConnect({flag : 'reload'});
			return;
		}
		var vars = (Object.toJSON(this.insDetail.varsEventList.vars)).evalJSON();
		var varsVersion = obj.vars;
		vars.strTitle = varsVersion.strTitle;
		vars.vars.strTitle = varsVersion.strTitle;
		vars.jsonFile = varsVersion.jsonFile;
		vars.jsonPermitHistory = varsVersion.jsonPermitHistory;
		vars.stampUpdate = varsVersion.stampUpdate;
		vars.arrSpaceStrTag = varsVersion.arrSpaceStrTag;
		vars.vars.arrSpaceStrTag = varsVersion.vars.arrSpaceStrTag;
		vars.varsColumnDetail.strVersion = varsVersion.strVersion;

		vars.stampBook = varsVersion.stampBook;
		vars.vars.stampBook = varsVersion.stampBook;
		vars.varsColumnDetail.flagIn = varsVersion.strFlagIn;
		vars.vars.flagIn = varsVersion.flagIn;
		vars.flagIn = varsVersion.flagIn;
		vars.jsonDetail.jsonDetail = varsVersion.jsonDetail;

		this._eventDetailList({
			flagVersion : 1,
			vars        : vars
		});
	},

	/**
	 *
	*/
	_eventDetailList : function(obj)
	{
		var objData = this._updateDetailListVars({
			flagVersion  : (obj.flagVersion)? 1 : 0,
			vars         : obj.vars
		});
		this.insDetail.eventList(objData);
		this._setDetailContent();
	},


	/**
	 *
	*/
	_updateDetailListVars : function(obj)
	{
		var objDetail = (Object.toJSON(this.vars.portal.varsDetail.templateDetail)).evalJSON();
		var varsBtn = (Object.toJSON(this.vars.portal.varsDetail.varsBtn)).evalJSON();
		var varsEdit = (Object.toJSON(this.vars.portal.varsDetail.view.varsEdit)).evalJSON();

		this._updateDetailTool({
			arr  : this.insDetail.insTool.vars.varsDetail,
			vars : obj.vars
		});

		var objData = {
			flagMoveUse : 1,
			strTitle    : this.insDetail.vars.varsStart.strTitle,
			strClass    : obj.vars.strClass,
			vars        : {
				varsDetail : this._updateDetailListVarsChild({
					flagVersion  : (obj.flagVersion)? 1 : 0,
					arr          : objDetail,
					vars         : obj.vars
				}),
				varsBtn    : this._updateDetailListVarsBtn({
					arr  : varsBtn,
					vars : obj.vars
				}),
				varsEdit   : this._updateDetailListVarsEdit({
					flagVersion  : (obj.flagVersion)? 1 : 0,
					arr          : varsEdit,
					vars         : obj.vars
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
		obj.arr.flagAddUse = 0;
		obj.arr.flagCopyUse = 0;
		obj.arr.flagEditUse = 0;

		if (obj.vars.flagBtnAdd) {
			obj.arr.flagAddUse = 1;
			obj.arr.flagCopyUse = 1;
			if (obj.flagVersion) {
				obj.arr.flagCopyUse = 0;
				var cutRule = this.vars.varsRule.varsEntityNation;
				var cutLog = obj.vars.jsonDetail.jsonDetail.varsEntityNation;
				if (obj.vars.jsonDetail.jsonDetail.varsEntityNation) {
					if (cutRule.flagConsumptionTaxFree == cutLog.flagConsumptionTaxFree
						&& cutRule.flagConsumptionTaxGeneralRule == cutLog.flagConsumptionTaxGeneralRule
						&& cutRule.flagConsumptionTaxDeducted == cutLog.flagConsumptionTaxDeducted
						&& cutRule.flagConsumptionTaxIncluding == cutLog.flagConsumptionTaxIncluding
					) {
						obj.arr.flagCopyUse = 1;
					}
				}
			}
		}

		if (obj.vars.flagBtnEdit) {
			obj.arr.flagEditUse = 1;
			if (obj.flagVersion) {
				obj.arr.flagEditUse = 0;
				var cutRule = this.vars.varsRule.varsEntityNation;
				var cutLog = obj.vars.jsonDetail.jsonDetail.varsEntityNation;
				if (obj.vars.jsonDetail.jsonDetail.varsEntityNation) {
					if (cutRule.flagConsumptionTaxFree == cutLog.flagConsumptionTaxFree
						&& cutRule.flagConsumptionTaxGeneralRule == cutLog.flagConsumptionTaxGeneralRule
						&& cutRule.flagConsumptionTaxDeducted == cutLog.flagConsumptionTaxDeducted
						&& cutRule.flagConsumptionTaxIncluding == cutLog.flagConsumptionTaxIncluding
					) {
						obj.arr.flagEditUse = 1;
					}
				}
			}
		}

		if (obj.vars.flagRemove) {
			obj.arr.flagEditUse = 0;
			obj.arr.flagCopyUse = 0;
		}

		return obj.arr;
	},

	/**
	 *
	*/
	_updateDetailTool : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'Reload') {
				obj.arr[i].varsContext.varsDetail = this._updateDetailToolReload({
					arr  : obj.vars.jsonVersion,
					data : obj.arr[i],
					vars : obj.vars
				});
			}
		}
	},


	/**
	 *
	*/
	_updateDetailToolReload : function(obj)
	{
		var arrNew = [];
		var insDisplay = new Code_Lib_TimeDisplay();

		for (var i = 0; i < obj.arr.length; i++) {
			var strVersion = 'Ver.' + (i + 1);
			var varsTmpl = (Object.toJSON(obj.data.varsContext.varsTmpl)).evalJSON();

			var objData = this.insRoot.insTimeZone.adjustDate({
				stamp : obj.arr[i].stampUpdate * 1000
			});

			var strTime = insDisplay.get({
				flagType : 3,
				vars     : objData
			});

			var str = strVersion + ' - ' + strTime;
			varsTmpl.id = varsTmpl.id + i;
			varsTmpl.strTitle = str;
			varsTmpl.vars.idTarget = obj.arr[i];
			arrNew.unshift(varsTmpl);

		}
		arrNew.unshift((Object.toJSON(obj.data.varsContext.varsTmpl)).evalJSON());

		return arrNew;
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
				if (obj.vars) {
					if (!obj.vars.flagCurrent) continue;
				}
				if (obj.flag == 'add' || obj.flag == 'setFile') {
					obj.arr[i].value = '';
					arrayNew.push(obj.arr[i]);

				} else {
					obj.arr[i].value = obj.vars.strTitle;
					arrayNew.push(obj.arr[i]);
				}

			} else if (obj.arr[i].id == 'FlagIn') {
				if (obj.vars) {
					if (!obj.vars.flagCurrent) continue;
				}
				if (obj.flag == 'add' || obj.flag == 'setFile') {
					obj.arr[i].value = 1;

				} else {
					obj.arr[i].value = obj.vars.flagIn;
				}
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'StampBook') {
				if (obj.flag == 'add' || obj.flag == 'setFile') {
					obj.arr[i].value = '';

				} else if (!obj.vars.stampBook) {
					obj.arr[i].value = '';

				} else {
					if (obj.flag == 'edit') {
						obj.arr[i].varsFormCalender.varsStatus.stampPoint = obj.vars.stampBook * 1000;
					}
					obj.arr[i].varsFormCalender.varsStatus.flagMainAutoUse = 0;
					obj.arr[i].varsFormCalender.varsStatus.stampMain = obj.vars.stampBook * 1000;
					var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.vars.stampBook * 1000});
					obj.arr[i].value = insDisplay.get({flagType : 9, vars : objTime});
				}
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'JsonDetail') {
				if (obj.vars) {
					if (!obj.vars.flagCurrent) continue;
				}
				obj.arr[i].varsFormJournal.varsStatus.flagEditUse = 1;
				obj.arr[i].varsFormJournal.varsStatus.flagBtnTextUse = 0;
				if (obj.flag == 'add' || obj.flag == 'setFile') {
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


			} else if (obj.arr[i].id == 'ArrCommaIdLogFile') {
				if (obj.vars) {
					if (!obj.vars.flagCurrent) continue;
				}
				if (obj.flag == 'add') {
					obj.arr[i].varsFormArea.varsDetail = [];
					obj.arr[i].value = 'dummy';

				} else if (obj.flag == 'setFile') {
					var arr = obj.arrFile;
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

				} else {
					var arr = obj.vars.arrCommaIdLogFile;
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

			} else if (obj.arr[i].id == 'ArrCommaIdAccountPermit') {
				if (obj.vars) {
					if (!obj.vars.flagCurrent) continue;
				}
				if (obj.flag == 'add' || obj.flag == 'setFile') {
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

			} else if (obj.arr[i].id == 'ArrSpaceStrTag') {
				if (obj.vars) {
					if (!obj.vars.flagCurrent) continue;
				}
				if (obj.flag == 'add' || obj.flag == 'setFile') {
					obj.arr[i].value = '';

				} else {
					obj.arr[i].value = (obj.vars.arrSpaceStrTag)? obj.vars.arrSpaceStrTag : '';
				}
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'NumSumMax') {
				if (obj.vars) {
					if (!obj.vars.flagCurrent) continue;
				}
				if (obj.flag == 'add' || obj.flag == 'setFile') {
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
	_updateDetailListVarsBtn : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			obj.arr[i].flagUse = 0;
			if (obj.arr[i].vars.idTarget == 'delete' && obj.vars.flagBtnDelete) obj.arr[i].flagUse = 1;
			else if (obj.arr[i].vars.idTarget == 'pay' && obj.vars.flagBtnPay) obj.arr[i].flagUse = 1;
			else if (obj.arr[i].vars.idTarget == 'write' && obj.vars.flagBtnWrite) obj.arr[i].flagUse = 1;
		}

		return obj.arr;
	},

	/**
	 *
	*/
	_checkDetailListLost : function(obj)
	{
		var flag = '';
		var cut = this.vars.varsRule;
		var arrSide = ['arrDebit', 'arrCredit'];
		for (var i = 0; i < obj.arr.length; i++) {

			for (var j = 0; j < arrSide.length; j++) {
				var strSide = arrSide[j];

				var idAccountTitle = obj.arr[i][strSide].idAccountTitle;
				var idDepartment = obj.arr[i][strSide].idDepartment;
				var idSubAccountTitle = obj.arr[i][strSide].idSubAccountTitle;

				if (idAccountTitle) {
					if (!cut.arrAccountTitle.arrStrTitle[idAccountTitle]) {
						return 'strOldIdAccountTitle';
					}

					if (idDepartment) {
						if (!cut.arrDepartment.arrSelectTag.length > 1) {
							return 'strOldIdDepartment';

						} else {
							if (!cut.arrDepartment.arrStrTitle[idDepartment]) {
								return 'strOldIdDepartment';
							}
						}
					}

					if (idSubAccountTitle) {
						if (!cut.arrSubAccountTitle.arrStrTitle[idAccountTitle]) {
							return 'strOldIdSubAccountTitle';

						} else {
							if (!cut.arrSubAccountTitle.arrStrTitle[idAccountTitle][idSubAccountTitle]) {
								return 'strOldIdSubAccountTitle';
							}
						}
					}

					if (strSide == 'arrDebit') {
						if (obj.flagIn) {
							if (!cut.arrAccountTitleCash.arrStrTitle[idAccountTitle]) {
								return 'strOldIdAccountTitleCash';
							}
						}
					} else {
						if (!obj.flagIn) {
							if (!cut.arrAccountTitleCash.arrStrTitle[idAccountTitle]) {
								return 'strOldIdAccountTitleCash';
							}
						}
					}
				}
			}
		}

		return flag;
	},

	/**
	 *
	*/
	_updateDetailListVarsChild : function(obj)
	{
		var insDisplay = new Code_Lib_TimeDisplay();
		var insEscape = new Code_Lib_Escape();
		var flagLost = this._checkDetailListLost({
			flagIn : parseFloat(obj.vars.vars.flagIn),
			arr    : obj.vars.jsonDetail.jsonDetail.varsDetail
		});

		var arrayNew = [];
		for (var i = 0; i < obj.arr.length; i++) {
			var id = insEscape.strLowCapitalize({data : obj.arr[i].id});
			if (obj.arr[i].id == 'StampRegister') {
				var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.vars.stampRegister * 1000});
				obj.arr[i].value = insDisplay.get({flagType : 1, vars : objTime});
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'StampUpdate') {
				var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.vars.stampUpdate * 1000});
				obj.arr[i].value = insDisplay.get({flagType : 1, vars : objTime});
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'DummyStampBook') {
				var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.vars.stampBook * 1000});
				obj.arr[i].value = insDisplay.get({flagType : 1, vars : objTime});
				var temp = {};
				temp.id = obj.arr[i].id;
				temp.strTitle = obj.arr[i].value;
				temp.vars = {};
				temp.vars.stampBook = obj.vars.vars.stampBook;
				temp.vars.idTarget = 'stampBook';
				obj.arr[i].varsTextBtn.push(temp);
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'DummyFlagIn') {
				id = 'flagIn';
				obj.arr[i].value = obj.vars.varsColumnDetail[id];
				obj.arr[i][id] = obj.vars.vars[id];
				var temp = {};
				temp.id = obj.arr[i].id;
				temp.strTitle = obj.arr[i].value;
				temp.vars = {};
				temp.vars[id] = obj.vars.vars[id];
				temp.vars.idTarget = id;
				obj.arr[i].varsTextBtn.push(temp);
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'DummyStrTitle') {
				id = 'strTitle';
				obj.arr[i].value = (obj.vars[id])? obj.vars[id] : '-';
				if (!obj.vars[id]) {
					arrayNew.push(obj.arr[i]);
					obj.arr[i].varsTextBtn = null;
					continue;
				}
				var temp = {};
				temp.id = obj.arr[i].id;
				temp.strTitle = obj.arr[i].value;
				temp.vars = {};
				temp.vars[id] = obj.vars.vars[id];
				temp.vars.idTarget = id;
				obj.arr[i].varsTextBtn.push(temp);
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'DummyStatus') {
				obj.arr[i].value = obj.vars.varsColumnDetail.strStatus;
				if (obj.vars.flagRemove) {
					var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.vars.stampRemove * 1000});
					obj.arr[i].value += '<br>( ' + insDisplay.get({flagType : 1, vars : objTime}) + ' ) ';

				} else if (obj.vars.flagLost) {
					obj.arr[i].value = obj.vars.varsColumnDetail.strStatus;
					arrayNew.push(obj.arr[i]);
					obj.arr[i].varsTextBtn = null;
					continue;

				} else if (obj.vars.flagPay) {
					var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.vars.stampPay * 1000});
					obj.arr[i].value += '<br>( ' + insDisplay.get({flagType : 1, vars : objTime}) + ' ) ';

				}
				var temp = {};
				temp.id = obj.arr[i].id;
				temp.strTitle = obj.arr[i].value;
				temp.vars = {};
				temp.vars.flagPay = obj.vars.vars.flagPay;
				temp.vars.flagStampBook = obj.vars.vars.flagStampBook;
				temp.vars.flagRemove = obj.vars.vars.flagRemove;
				temp.vars.idTarget = 'strStatus';
				obj.arr[i].varsTextBtn.push(temp);
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'StrVersion') {
				obj.arr[i].value = obj.vars.varsColumnDetail.strVersion;
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'DummyInfo') {
				if (parseFloat(obj.vars.vars.flagRemove) || obj.flagVersion) {
					if (flagLost) {
						obj.arr[i].value = obj.arr[i].varsTmpl.strLostRemove;
						arrayNew.push(obj.arr[i]);
					}

				} else {
					if (flagLost) {
						obj.arr[i].value = obj.arr[i].varsTmpl.strLost;
						if (parseFloat(obj.vars.vars.flagPermitLost)) {
							obj.arr[i].value += '<br>' + obj.arr[i].varsTmpl.strPermitLost;
						}
						arrayNew.push(obj.arr[i]);

					} else {
						if (parseFloat(obj.vars.vars.flagPermitLost)) {
							obj.arr[i].value = obj.arr[i].varsTmpl.strPermitLost;
							arrayNew.push(obj.arr[i]);
						}
					}
				}

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

			} else if (obj.arr[i].id == 'JsonChargeHistory') {
				var temp = obj.vars.jsonChargeHistory.interpolate({idSelf : this.idSelf + obj.arr[i].id});
				obj.arr[i].strHtml = temp;
				obj.arr[i].jsonChargeHistory = obj.vars.vars.jsonChargeHistory;
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'DummyJsonFile') {
				if (obj.vars.jsonFile.length) continue;
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'JsonFile') {
				if (!obj.vars.jsonFile.length) continue;
				this._updateDetailListVarsChildJsonFile({
					arr   : obj.vars.jsonFile,
					data  : obj.arr[i]
				});
				obj.arr[i].value = 'dummy';
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'JsonWriteHistory') {
				var temp = obj.vars.jsonWriteHistory.interpolate({idSelf : this.idSelf + obj.arr[i].id});
				obj.arr[i].strHtml = temp;
				obj.arr[i].jsonWriteHistory = obj.vars.vars.jsonWriteHistory;
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'JsonDetail') {
				obj.arr[i].varsFormJournal.varsDetail = obj.vars.jsonDetail.jsonDetail;
				obj.arr[i].varsFormJournal.flagIn = parseFloat(obj.vars.vars.flagIn);
				obj.arr[i].value = 'dummy';

				if (obj.flagVersion) {
					var cutRule = this.vars.varsRule.varsEntityNation;
					var cutLog = obj.vars.jsonDetail.jsonDetail.varsEntityNation;
					if (obj.vars.jsonDetail.jsonDetail.varsEntityNation) {
						if (!(cutRule.flagConsumptionTaxFree == cutLog.flagConsumptionTaxFree
							&& cutRule.flagConsumptionTaxGeneralRule == cutLog.flagConsumptionTaxGeneralRule
							&& cutRule.flagConsumptionTaxDeducted == cutLog.flagConsumptionTaxDeducted
							&& cutRule.flagConsumptionTaxIncluding == cutLog.flagConsumptionTaxIncluding
						)) {
							obj.arr[i].varsFormJournal.varsStatus.flagBtnTextTaxUse = 0;
						}
					}
				}
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
	_updateDetailListVarsChildJsonFile : function(obj)
	{
		var insDisplay = new Code_Lib_TimeDisplay();
		var insByte = new Code_Lib_DisplayByte();
		var insComma = new Code_Lib_DisplayComma();

		obj.data.varsFormCheck.varsDetail = [];
		for (var i = 0; i < obj.arr.length; i++) {
			var varsTmpl = (Object.toJSON(obj.data.varsFormCheck.tmplDetail)).evalJSON();
			varsTmpl.id = i;
			varsTmpl.varsColumnDetail.strNo = i + 1;
			if (obj.arr[i].flagRemove) {
				varsTmpl.varsColumnDetail.strStatus = this.vars.varsItem.strRemoveFake;
				varsTmpl.varsColumnDetail.btnDetailLock = 1;

			} else {
				if (!obj.arr[i].flagAuthority) {
					varsTmpl.varsColumnDetail.btnDetailLock = 1;
				}
				varsTmpl.varsColumnDetail.strStatus = this.vars.varsItem.strDone;
			}

			var strSize = 0;
			if (obj.arr[i].numByte >= 1048576) {
				var numSize = insByte.get({
					num      : obj.arr[i].numByte,
					flagFrom : 'b',
					flagTo   : 'mb'
				});
				strSize = insComma.get({num : parseFloat(numSize)}) + 'MB';

			} else {
				var numSize = insByte.get({
					num      : obj.arr[i].numByte,
					flagFrom : 'b',
					flagTo   : 'kb'
				});
				strSize = insComma.get({num : parseFloat(numSize)}) + 'KB';
			}
			varsTmpl.numVersion = obj.arr[i].numVersion;
			varsTmpl.flagFileAccess = obj.arr[i].flagFileAccess;
			varsTmpl.idLogFile = obj.arr[i].id;
			varsTmpl.idTarget = obj.arr[i].id;

			varsTmpl.varsColumnDetail.strTitle = obj.arr[i].strTitle;
			varsTmpl.varsColumnDetail.strTitle += ' ( ' + strSize + ' )';

			obj.data.varsFormCheck.varsDetail.push(varsTmpl);
		}
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

	_extChild : function(obj)
	{
		this._setVarChild(obj);
		var strExt = this._varsChild.strExt;
		var strChild = this._varsChild.strChild;
		var flagDetail = this._varsChild.varsChild.flagDetail;
		if (this['ins' + strExt + strChild]) {
			if (this['ins' + strExt + strChild].vars.flagHideNow) {
				if (this['ins' + strExt + strChild + 'Class']) {
					this['ins' + strExt + strChild + 'Class'].eventWindowAppear({vars : this._varsChild.varsChild});
					this['ins' + strExt + strChild].updateHide({ flagEffect : 1 });
				}

			} else {
				if (flagDetail == 'setFile') {
					if (this['ins' + strExt + strChild + 'Class']) {
						this['ins' + strExt + strChild + 'Class'].eventWindowAppear({vars : this._varsChild.varsChild});
						this['ins' + strExt + strChild].setZIndex();
					}
				} else {
					this.eventHide();
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
	_varChild : function()
	{
		var vars = {};
		var insEscape = new Code_Lib_Escape();
		var strChild = this._varsChild.strChild;
		var str = this._varsChild.strExt;
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

		if (strChild == 'Search' || strChild == 'Preference') {
			str = insEscape.strLowCapitalize({data : strChild});
			if (this.vars.child[str]) {
				vars = (Object.toJSON(this.vars.child[str])).evalJSON();
			} else {
				vars = (Object.toJSON(this.vars.child.templateWindow)).evalJSON();
			}
		}

		var strExt = this._varsChild.strExt;
		var strChild = this._varsChild.strChild;
		vars.id =  strExt + strChild;
		vars.strTitle = vars.strTitle.replace(/<%child%>/, this._varsChild.strTitleChild);
		vars.strTitle = vars.strTitle.replace(/<%parent%>/, this._varsChild.strTitleParent);
		if (this._varsChild.strExt == 'Preference') {
			vars.flagMenuShowUse = 1;
		}

		this._varsChild.varsWindow[strExt + strChild] = vars;
	},

	/**
	 *
	*/
	_iniChild : function(obj)
	{
		this._extChild(obj);
	}
});
{/literal}