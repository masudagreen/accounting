{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_Banks = Class.create(Code_Lib_ExtPortal,
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
					if (!insCurrent.vars.varsItem.varsBanksAccountList.arrSelectTag.length) {
						alert(insCurrent.vars.varsItem.strNoneBank);
						insCurrent.bootAutoSearchOver({flag : 'addBanksAccount'});
						return;
					}
					if (insCurrent.insList.vars.varsStatus.flagImportNow == 'file') {
						insCurrent._iniChild({
							strTitleParent : insCurrent.insWindow.vars.strTitle,
							strTitleChild  : insCurrent.vars.child.varsTitle.file,
							strExt         : 'BanksImportFile',
							strChild       : '',
							strClass       : insCurrent.strClass,
							idModule       : insCurrent.idModule
						});

					} else if (insCurrent.insList.vars.varsStatus.flagImportNow == 'web') {
						insCurrent._iniChild({
							strTitleParent : insCurrent.insWindow.vars.strTitle,
							strTitleChild  : insCurrent.vars.child.varsTitle.web,
							strExt         : 'BanksImportWeb',
							strChild       : '',
							strClass       : insCurrent.strClass,
							idModule       : insCurrent.idModule
						});

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
						if (!insCurrent.vars.varsItem.varsBanksAccountList.arrSelectTag.length) {
							alert(insCurrent.vars.varsItem.strNoneBank);
							insCurrent.bootAutoSearchOver({flag : 'addBanksAccount'});
							return;
						}
						insCurrent.insList.eventTool({idTarget : obj.vars, flagStr : obj.varsTarget});
						if (obj.vars == 'file') {
							insCurrent._iniChild({
								strTitleParent : insCurrent.insWindow.vars.strTitle,
								strTitleChild  : insCurrent.vars.child.varsTitle.file,
								strExt         : 'BanksImportFile',
								strChild       : '',
								strClass       : insCurrent.strClass,
								idModule       : insCurrent.idModule
							});

						} else if (obj.vars == 'web') {
							insCurrent._iniChild({
								strTitleParent : insCurrent.insWindow.vars.strTitle,
								strTitleChild  : insCurrent.vars.child.varsTitle.web,
								strExt         : 'BanksImportWeb',
								strChild       : '',
								strClass       : insCurrent.strClass,
								idModule       : insCurrent.idModule
							});

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
		var flagWrite = 0;

		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].flagBtnDelete) flagDelete = 1;
			if (obj.arr[i].flagBtnWrite) flagWrite = 1;
		}

		for (var i = 0; i < obj.arrBtn.length; i++) {
			obj.arrBtn[i].flagUse = 0;
			if (obj.arrBtn[i].vars.idTarget == 'Delete' && flagDelete) obj.arrBtn[i].flagUse = 1;
			else if (obj.arrBtn[i].vars.idTarget == 'Write' && flagWrite) obj.arrBtn[i].flagUse = 1;
		}
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

		} else if (obj.flag == 'Output' || obj.flag == 'Print') {
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

		} else if (obj.flag == 'Search') {
			this._eventSearch({
				numLotNow : obj.vars.numLotNow,
				ph : {
					flagApply : this._varsSearch.ph.flagApply,
					arrWhere  : this._varsSearch.ph.arrWhere,
					arrOrder  : this._varsSearch.ph.arrOrder
				}
			});

		} else if (obj.flag == 'Delete' || obj.flag == 'Write') {
			var arrId = this.insList.getTableCheckBoxArrId();
			if (!arrId.length) {
				alert(this.insRoot.vars.varsSystem.str.selectRequire);
				this.insList.eventNavi({strTitle : null, strClass : null});
				return;
			}
			this._eventValue({
				vars     : arrId,
				idTarget : ''
			});

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


			} else if (this._varsListConnect.flag == 'Delete') {
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
				this.insLayout.updateTool({flagLock : 0, from : 'list', idTarget : 'Reload'});
				this.insList.eventNavi({strTitle : null, strClass : null});
			}
		} else if (obj.json.flag == 40) {
			this.insList.resetVars({vars : obj.json.data, numLotNow : this._varsSearch.numLotNow});
			this.insList.eventNavi({strTitle : null, strClass : null});

		} else {
			if (this._varsListConnect.flag == 'Write') {
				this.insList.resetVars({vars : obj.json.data, numLotNow : this._varsSearch.numLotNow});
				this.insList.eventNavi({strTitle : null, strClass : null});
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
	 *
	*/
	_idLog : 'Log',
	_varsAutoData : {},
	_checkAutoSearch : function(obj)
	{
		var insEscape = new Code_Lib_Escape();
		this._varsAutoData = {
			vars       : {
				vars : {
					idTarget : 'idLog',
					idLog    : obj.idTarget
				}
			},
			flagDetail : 1
		};

		var varsData = this.insTop.checkChildData({idTarget : this._idLog});
		if (!varsData) {
			var idTarget = insEscape.strLowCapitalize({data : this._idLog});
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
	},

	eventAutoSearch : function()
	{
		var varsData = this.insTop.checkChildData({idTarget : this._idLog});
		varsData.insClass.bootAutoSearchOver(this._varsAutoData);
	},

	_flagAutoDetail : 0,
	_flagAutoSearchOver : '',
	_varsAutoSearchOver : '',
	bootAutoSearchOver : function(obj)
	{
		var insEscape = new Code_Lib_Escape();
		this._varsAutoSearchOver = {};
		this._flagAutoSearchOver = obj.flag;
		if (obj.flag == 'showLog') {
			this._eventListConnect({flag : 'Reload', flagType : 'start'});

		} else if (obj.flag == 'showBanks') {
			var vars = {};
			vars.vars = obj.vars;
			this._flagAutoDetail = 1;
			this.bootAutoSearch({vars : vars});

		} else if (obj.flag == 'showBanksAccount' || obj.flag == 'addBanksAccount') {
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
					flagHideWindow : 0,
					insBack        : this,
					strBackFunc    : 'eventAutoSearchOver'
				});

			} else {
				varsData.insClass.bootAutoSearchOver({flag : obj.flag});
			}

		} else {
			this._flagAutoDetail = (obj.flagDetail)? 1 : 0;
			this.bootAutoSearch(obj);
		}
	},

	eventAutoSearchOver : function()
	{
		if (this._flagAutoSearchOver == 'showBanksAccount' || this._flagAutoSearchOver == 'addBanksAccount') {
			var varsData = this.checkChildData({idTarget : 'BanksPreference'});
			if (varsData) {
				varsData.insClass.bootAutoSearchOver({flag : this._flagAutoSearchOver});
			}
		}
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
		if (obj.vars.vars.idTarget == 'idLog') {
			this._checkAutoSearch({idTarget : obj.vars.vars[obj.vars.vars.idTarget]});
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
			varsTmpl.strColumn = 'idLogBanks';
			varsTmpl.value = obj.vars.vars[str];
			varsData.push(varsTmpl);

		} else if (str == 'idLogAccount') {
			varsTmpl.flagType = 'id';
			varsTmpl.strColumn = 'idLogAccount';
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

		} else if (str.match(/^numBalance$/)) {
			varsTmpl.flagType = 'numminus';
			varsTmpl.strColumn = str;
			varsTmpl.value = obj.vars.vars[str];
			varsData.push(varsTmpl);

		} else if (str.match(/^num/)) {
			varsTmpl.flagType = 'num';
			varsTmpl.strColumn = str;
			varsTmpl.value = obj.vars.vars[str];
			varsData.push(varsTmpl);
		}

		this._varsSearch.ph.arrWhere = varsData;
		if (str == 'strStatus') {
			this._varsSearch.ph.flagApply = obj.vars.vars[str];
			this._varsSearch.ph.arrWhere = [];

		} else {
			this._varsSearch.ph.flagApply = 'none';
		}
		this._eventListConnect({flag : flag});
	},

	/**
	 *
	*/
	_setDetailChild : function(obj)
	{
		var objDetail = (Object.toJSON(this.vars.portal.varsDetail.templateDetail)).evalJSON();
		var varsDetail = [];
		var vars = {};
		var varsIni = null;

		if (!this.vars.varsItem.varsBanksAccountList.arrSelectTag.length) {
			alert(this.vars.varsItem.strNoneBank);
			this.bootAutoSearchOver({flag : 'addBanksAccount'});
			return;
		}

		if (obj.flag == 'add') {
			varsDetail = this._getDetailChildVars({
				arr  : objDetail,
				flag : obj.flag
			});

		} else if (obj.flag == 'copy') {
			varsIni = this._getDetailChildVars({
				flag : 'add',
				arr  : (Object.toJSON(objDetail)).evalJSON(),
				vars : this.insDetail.varsEventList.vars
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
				flag : 'add',
				arr  : (Object.toJSON(objDetail)).evalJSON(),
				vars : this.insDetail.varsEventList.vars
			});
			varsDetail = this._getDetailChildVars({
				flag : obj.flag,
				arr  : objDetail,
				vars : this.insDetail.varsEventList.vars
			});
			vars = this.insDetail.varsEventList.vars;

		} else if (obj.flag == 'output') {
			this._eventDetailConnect({flag : 'output'});
			return;
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
				idTarget   : idTarget,
				varsIni    : varsIni,
				varsDetail : varsDetail,
				vars       : vars
			}
		});

	},

	/**
	 *
	*/
	_eventDetailConnect : function(obj)
	{
		if (obj.flag == 'reload' || obj.flag == 'delete' || obj.flag == 'write' || obj.flag == 'output') {
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
	_eventDetailConnectSuccess : function(obj)
	{
		if (obj.json.flag == 1) {
			if (this._varsDetailConnect.flag == 'reload') {
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
				this.insList.resetVars({vars : obj.json.data, numLotNow : this._varsSearch.numLotNow});
				this.insList.eventNavi({strTitle : null, strClass : null});
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
	_varsContent : {num : 0},
	_setDetailContent : function(obj)
	{
		this._varsDetailComment = '';
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
					var num = 0;
					for (var j = 0; j < arr.length; j++) {
						num++;
						var idTr = this.idSelf + obj.arr[i].id + '_Tr' + num;
						var idTd = idTr + '_Td' + arrStr[k];
						if (!$(idTd)) {
							break;
						}
						if (parseFloat(arr[j][arrStr[k]]) === 0) {
							$(idTd).innerHTML = this.vars.varsItem.strLost;
							continue;
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
					}
				}
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
		vars.stampUpdate = varsVersion.stampUpdate;
		vars.stampBook = varsVersion.stampBook;
		vars.vars.stampBook = vars.stampBook;
		vars.vars.numValueIn = varsVersion.vars.numValueIn;
		vars.vars.numValueOut = varsVersion.vars.numValueOut;
		vars.vars.numBalance = varsVersion.vars.numBalance;

		vars.varsColumnDetail.numValueIn = varsVersion.numValueIn;
		vars.varsColumnDetail.numValueOut = varsVersion.numValueOut;
		vars.varsColumnDetail.numBalance = varsVersion.numBalance;

		vars.vars.idLogAccount = varsVersion.idLogAccount;
		vars.arrSpaceStrTag = varsVersion.arrSpaceStrTag;
		vars.vars.arrSpaceStrTag = varsVersion.vars.arrSpaceStrTag;
		vars.varsColumnDetail.strVersion = varsVersion.strVersion;
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
		this._setDetailContent({idTarget : obj.vars.vars.idTarget});
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
		}

		if (obj.vars.flagBtnEdit) {
			obj.arr.flagEditUse = 1;
		}

		if (obj.vars.flagRemove) {
			obj.arr.flagEditUse = 0;
			obj.arr.flagCopyUse = 0;
		}

		if (obj.vars.flagBtnAdd && obj.flagVersion) {
			obj.arr.flagCopyUse = 1;
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
		var insEscape = new Code_Lib_Escape();
		var arrayNew = [];
		var idAccountTitle = '';
		var stampStart = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'StampRegister'
				 || obj.arr[i].id == 'StampUpdate'
				 || obj.arr[i].id == 'JsonChargeHistory'
				 || obj.arr[i].id == 'StrVersion'
				 || obj.arr[i].id == 'Id'
				 || obj.arr[i].id == 'JsonWriteHistory'
				 || obj.arr[i].id == 'StrTitleAccount'
				 || obj.arr[i].id.match(/^Dummy/)
			) {
				continue;
			}
			var id = insEscape.strLowCapitalize({data : obj.arr[i].id});
			if (obj.arr[i].id == 'StrTitle') {
				if (obj.flag == 'add') {
					obj.arr[i].value = '';

				} else {
					obj.arr[i].value = obj.vars.strTitle;
				}

			} else if (obj.arr[i].id == 'StampBook') {
				if (obj.flag == 'add') {
					obj.arr[i].value = '';

				} else if (!parseFloat(obj.vars.vars.stampBook)) {
					obj.arr[i].value = '';

				} else {
					obj.vars.vars.stampBook = parseFloat(obj.vars.vars.stampBook);
					if (obj.flag == 'edit') {
						obj.arr[i].varsFormCalender.varsStatus.stampPoint = obj.vars.stampBook * 1000;
					}
					obj.arr[i].varsFormCalender.varsStatus.flagMainAutoUse = 0;
					obj.arr[i].varsFormCalender.varsStatus.stampMain = obj.vars.vars.stampBook * 1000;
					var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.vars.vars.stampBook * 1000});
					obj.arr[i].value = insDisplay.get({flagType : 9, vars : objTime});
				}

			} else if (obj.arr[i].id == 'IdLogAccount') {
				if (obj.flag == 'add') {
					obj.arr[i].value = '';

				} else {
					obj.arr[i].value = obj.vars.vars[id];
				}

			} else if (obj.arr[i].id == 'FlagIn') {
				if (obj.flag == 'add') {
					obj.arr[i].value = 1;

				} else {
					obj.arr[i].value = obj.vars.vars[id];
				}

			} else if (obj.arr[i].id.match(/^Num/)) {
				if (obj.flag == 'add') {
					obj.arr[i].value = 0;

				} else {
					obj.arr[i].value = obj.vars.vars[id];
				}

			} else if (obj.arr[i].id == 'ArrSpaceStrTag') {
				if (obj.flag == 'add') {
					obj.arr[i].value = '';

				} else {
					obj.arr[i].value = (obj.vars.arrSpaceStrTag)? obj.vars.arrSpaceStrTag : '';
				}
			}
			arrayNew.push(obj.arr[i]);
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
			else if (obj.arr[i].vars.idTarget == 'write' && obj.vars.flagBtnWrite) obj.arr[i].flagUse = 1;
		}

		return obj.arr;
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
			if (obj.arr[i].id == 'StampRegister') {
				var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.vars.stampRegister * 1000});
				obj.arr[i].value = insDisplay.get({flagType : 1, vars : objTime});
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'StampUpdate') {
				var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.vars.stampUpdate * 1000});
				obj.arr[i].value = insDisplay.get({flagType : 1, vars : objTime});
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'StampBook') {
				var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.vars.vars.stampBook * 1000});
				obj.arr[i].value = insDisplay.get({flagType : 1, vars : objTime});
				var temp = {};
				temp.id = obj.arr[i].id;
				temp.strTitle = obj.arr[i].value;
				temp.vars = {};
				temp.vars[id] = obj.vars.vars[id];
				temp.vars.idTarget = obj.arr[i].id;
				obj.arr[i].varsTextBtn.push(temp);
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'StrTitle') {
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
				temp.vars.idTarget = obj.arr[i].id;
				obj.arr[i].varsTextBtn.push(temp);
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'DummyFlagBank') {
				obj.arr[i].value = obj.vars.varsColumnDetail.flagBank;
				id = 'idLogAccount';
				var temp = {};
				temp.id = 'IdLogAccount';
				temp.strTitle = obj.arr[i].value;
				temp.vars = {};
				temp.vars[id] = obj.vars.vars[id];
				temp.vars.idTarget = 'IdLogAccount';
				obj.arr[i].varsTextBtn.push(temp);
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id.match(/^Num/)) {
				var temp = {};
				obj.arr[i].value = obj.vars.varsColumnDetail[id];
				temp.id = obj.arr[i].id;
				temp.strTitle = obj.arr[i].value;
				temp.vars = {};
				temp.vars[id] = obj.vars.vars[id];
				temp.vars.idTarget = obj.arr[i].id;
				obj.arr[i].varsTextBtn.push(temp);
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'DummyStatus') {
				obj.arr[i].value = (obj.vars.varsColumnDetail.flagStatus)? obj.vars.varsColumnDetail.flagStatus : '-';
				if (obj.vars.flagRemove) {
					var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.vars.stampRemove * 1000});
					obj.arr[i].value += '<br>( ' + insDisplay.get({flagType : 1, vars : objTime}) + ' ) ';

				} else if (obj.vars.flagCaution) {
					obj.arr[i].value += '<br>' + this.vars.varsItem.strCautionReason;
				}
				var temp = {};
				temp.id = obj.arr[i].id;
				temp.strTitle = obj.arr[i].value;
				temp.vars = {};
				if (parseFloat(obj.vars.vars.flagRemove)) {
					temp.vars.strStatus = 'remove';

				} else if (parseFloat(obj.vars.vars.flagCaution)) {
					temp.vars.strStatus = 'caution';

				} else {
					temp.vars.strStatus = 'done';
				}
				temp.vars.idTarget = 'strStatus';
				obj.arr[i].varsTextBtn.push(temp);
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'StrVersion') {
				obj.arr[i].value = obj.vars.varsColumnDetail.strVersion;
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
				obj.arr[i].value = obj.vars.id;
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

			} else if (obj.arr[i].id == 'JsonWriteHistory') {
				var temp = obj.vars.jsonWriteHistory.interpolate({idSelf : this.idSelf + obj.arr[i].id});
				obj.arr[i].strHtml = temp;
				obj.arr[i].jsonWriteHistory = obj.vars.vars.jsonWriteHistory;
				arrayNew.push(obj.arr[i]);
			}
		}

		return arrayNew;
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

		if (strChild == 'Search' || strChild == 'Preference' || strChild == 'Editor') {
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
	_iniCss : function()
	{
		this._extCss();
	}
});
{/literal}