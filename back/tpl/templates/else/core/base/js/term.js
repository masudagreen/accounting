{literal}
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Core_Base_Term = Class.create(Code_Lib_ExtPortal,
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
	_iniList : function()
	{
		this._extList();
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
	_eventLayoutDetailContent : function()
	{

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
		var varsTmpl = {flagType: 'str', strColumn: '', flagCondition: 'eq', value: ''};

		var str = insEscape.strLowCapitalize({data : obj.vars.vars.idTarget});
		if (str == 'arrSpaceStrTag') {
			varsTmpl.flagType = 'tag';
			varsTmpl.flagCondition = 'like';
			varsTmpl.strColumn = str;
			varsTmpl.value = ' ' + obj.vars.vars.strTag + ' ';
			varsData.push(varsTmpl);

		} else if (str == 'id') {
			varsTmpl.flagType = 'num';
			varsTmpl.strColumn = str;
			varsTmpl.value = obj.vars.vars[str];
			varsData.push(varsTmpl);

		} else if (str == 'stampStart' || str == 'stampEnd') {
			varsTmpl.flagType = 'stamp';
			varsTmpl.strColumn = str;
			varsTmpl.value = obj.vars.vars[str];
			varsData.push(varsTmpl);

		} else if (str == 'strTitle') {
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
	_getDetailChildVars : function(obj)
	{
		var insDisplay = new Code_Lib_TimeDisplay();
		var arrayNew = [];
		for (var i = 0; i < obj.arr.length; i++) {
			obj.arr[i].flagCommentUse = 0;
			if (obj.arr[i].id == 'StampStart') {
				if (obj.flag == 'add') {
					obj.arr[i].value = '';

				} else if (!obj.vars.stampStart) {
					obj.arr[i].value = '';

				} else {
					if (obj.vars.id == 1 || obj.vars.id == 2) {
						obj.arr[i].value = '';

					} else {
						if (obj.flag == 'edit') {
							obj.arr[i].varsFormCalender.varsStatus.stampPoint = obj.vars.stampStart * 1000;
						}
						obj.arr[i].varsFormCalender.varsStatus.flagMainAutoUse = 0;
						obj.arr[i].varsFormCalender.varsStatus.stampMain = obj.vars.stampStart * 1000;
						var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.vars.stampStart * 1000});
						obj.arr[i].value = insDisplay.get({flagType : 4, vars : objTime});

					}

				}
				this._getDetailChildVarsChild({arr : obj.arr[i].arrayError});
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'StampEnd') {
				if (obj.flag == 'add') {
					obj.arr[i].value = '';

				} else if (obj.vars.stampEnd === '') {
					obj.arr[i].value = '';

				} else if (!parseFloat(obj.vars.stampEnd)) {
					obj.arr[i].value = '';

				} else {
					if (obj.vars.id == 1 || obj.vars.id == 2) {
						obj.arr[i].value = '';

					} else {
						if (obj.flag == 'edit') {
							obj.arr[i].varsFormCalender.varsStatus.stampPoint = obj.vars.stampEnd * 1000;
						}
						obj.arr[i].varsFormCalender.varsStatus.flagMainAutoUse = 0;
						obj.arr[i].varsFormCalender.varsStatus.stampMain = obj.vars.stampEnd * 1000;
						var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.vars.stampEnd * 1000});
						obj.arr[i].value = insDisplay.get({flagType : 4, vars : objTime});
					}

				}
				this._getDetailChildVarsChild({arr : obj.arr[i].arrayError});
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'StrTitle') {
				if (obj.flag == 'add') {
					obj.arr[i].value = '';

				} else {
					obj.arr[i].value = obj.vars.strTitle;

				}
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'ArrSpaceStrTag') {
				if (obj.flag == 'add') {
					obj.arr[i].value = '';

				} else {
					obj.arr[i].value = (obj.vars.arrSpaceStrTag)? obj.vars.arrSpaceStrTag : '';

				}
				arrayNew.push(obj.arr[i]);

			}
		}

		return arrayNew;
	},

	/**
	 *
	*/
	_getDetailChildVarsChild : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].flagCheck == 'word' && obj.arr[i].flagType == 'num') {
				obj.arr[i].flagUse = 0;

			} else if (obj.arr[i].flagCheck == 'format' && obj.arr[i].flagType == 'date') {
				obj.arr[i].flagUse = 1;

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
			if (obj.arr[i].id == 'StampRegister') {
				var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.vars.stampRegister * 1000});
				obj.arr[i].value = insDisplay.get({flagType : 1, vars : objTime});
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'StampUpdate') {
				var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.vars.stampUpdate * 1000});
				obj.arr[i].value = insDisplay.get({flagType : 1, vars : objTime});
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'StampStart') {
				if (obj.vars.id == 1 || obj.vars.id == 2) {
					obj.arr[i].value = '-';
					obj.arr[i].varsTextBtn = null;
					arrayNew.push(obj.arr[i]);
					continue;

				} else {
					var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.vars.stampStart * 1000});
					obj.arr[i].value = insDisplay.get({flagType : 2, vars : objTime});

					var temp = {};
					temp.id = obj.arr[i].id;
					temp.strTitle = obj.arr[i].value;
					temp.vars = {};
					temp.vars[id] = obj.vars.vars[id];
					temp.vars.idTarget = obj.arr[i].id;
					obj.arr[i].varsTextBtn.push(temp);

				}
				arrayNew.push(obj.arr[i]);


			} else if (obj.arr[i].id == 'StampEnd') {
				if (obj.vars.id == 1 || obj.vars.id == 2) {
					obj.arr[i].value = '-';
					obj.arr[i].varsTextBtn = null;
					arrayNew.push(obj.arr[i]);
					continue;

				} else {
					if (obj.vars.stampEnd === '') {
						obj.arr[i].value = '-';
						arrayNew.push(obj.arr[i]);
						obj.arr[i].varsTextBtn = null;
						continue;

					} else if (!parseFloat(obj.vars.stampEnd)) {
						obj.arr[i].value = '-';
						arrayNew.push(obj.arr[i]);
						obj.arr[i].varsTextBtn = null;
						continue;
					}

					var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.vars.stampEnd * 1000});
					obj.arr[i].value = insDisplay.get({flagType : 2, vars : objTime});

					var temp = {};
					temp.id = obj.arr[i].id;
					temp.strTitle = obj.arr[i].value;
					temp.vars = {};
					temp.vars[id] = obj.vars.vars[id];
					temp.vars.idTarget = obj.arr[i].id;
					obj.arr[i].varsTextBtn.push(temp);

				}
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'StrTitle') {
				obj.arr[i].value = obj.vars.strTitle;
				var temp = {};
				temp.id = obj.arr[i].id;
				temp.strTitle = obj.arr[i].value;
				temp.vars = {};
				temp.vars[id] = obj.vars.vars[id];
				temp.vars.idTarget = obj.arr[i].id;
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
				obj.arr[i].value = obj.vars.id;
				var temp = {};
				temp.id = obj.arr[i].id;
				temp.strTitle = obj.arr[i].value;
				temp.vars = {};
				temp.vars[id] = obj.vars.vars[id];
				temp.vars.idTarget = obj.arr[i].id;
				obj.arr[i].varsTextBtn.push(temp);
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'DummyDefault') {
				if (obj.vars.flagDefault) {
					arrayNew.push(obj.arr[i]);
				}

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