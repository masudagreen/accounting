{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_Balance = Class.create(Code_Lib_ExtPortal,
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
						strExt          : 'Balance',
						strChild        : obj.vars.id,
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
				} else if (obj.vars.id == 'Edit') {
					insCurrent._setListChild({flag : obj.vars.id.toLowerCase()});
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
					insCurrent._eventDetailConnect({flag : 'output'});

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
			}
		};

		return allot;
	},

	/**
	 *
	*/
	_setListChild : function(obj)
	{
		var objDetail = (Object.toJSON(this.vars.portal.varsList.templateDetailEditor)).evalJSON();
		var varsList = (Object.toJSON(this.insList.vars.tableTree.varsDetail.varsDetail)).evalJSON();

		var varsDetail = this._getListChildVars({
			flag : obj.flag,
			arr  : objDetail,
			vars : this.vars.varsFlag
		});
		var idTarget = 'dummy';
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
				varsDetail : varsDetail,
				varsIni    : this.vars.portal.varsList.varsIni,
				varsList   : varsList
			}
		});

	},

	/**
	 *
	*/
	_getListChildVars : function(obj)
	{
		var arrayNew = [];
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'DummyStatus') {
				var strDepartment = this._getDetailChildVarsStrDepartment({
					arr   : this.insNavi.insForm.vars.varsDetail,
					vars  : obj.vars,
					str   : obj.arr[i].varsTmpl.strDepartment
				});
				var str = strDepartment;
				var strExplain = obj.arr[i].varsTmpl.strNormal;
				obj.arr[i].strCommentTitle = strExplain.replace(RegExp("<%replace%>", "g"), str);
			}
			arrayNew.push(obj.arr[i]);
		}

		return arrayNew;
	},

	/**
	 *
	*/
	_getDetailChildVarsStrDepartment : function(obj)
	{
		var str = '';
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'IdDepartment') {
				var arr = obj.arr[i].arrayOption;
				for (var j = 0; j < arr.length; j++) {
					if (arr[j].value == obj.vars.idDepartment) {
						if (obj.vars.idDepartment == 0) {
							str = arr[j].strTitle;
						} else {
							str = obj.str + arr[j].strTitle;
						}
						return str;
					}
				}
				return str;
			}
		}
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
				varsDetail : this._getNaviChildVars({arr : tmplDetail}),
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
	_getNaviChildVars : function(obj)
	{
		return obj.arr;
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
		var arrayKey = [];
		var arrayValue = [];
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
				this.vars.varsFlag = obj.json.data.varsFlag;
				this.vars.portal.varsList.varsIni = obj.json.data.varsIni;
				this.insList.updateTableTreeVars({vars : obj.json.data});
				this._setDetailStart();
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
		this._extList();
	},

	/**
	 *
	*/
	_eventListConnect : function(obj)
	{
		if (obj.flag == 'Reload' || obj.flag == 'Output' || obj.flag == 'Print') {
			if (this.insNavi.checkForm({flagType : 'common'})) return;
			this._eventValue({
				vars     : this.insNavi.getFormValue(),
				idTarget : ''
			});

		} else if (obj.flag == 'Search') {
			this._eventSearch({
				numLotNow : obj.vars.numLotNow,
				ph : {
					arrWhere : {},
					arrOrder : {}
				}
			});

		}

		this._varsListConnect = obj;
		this._sendListConnect();
	},

	/**
	 *
	*/
	eventListConnect : function(obj)
	{
		this._eventListConnect({flag : 'Reload'});
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
		var arrayKey = [];
		var arrayValue = [];
		var jsonStamp = {};

		var strFunc = 'List' + this._varsListConnect.flag.capitalize();
		var strDb = 'master';
		if (this._varsListConnect.flag == 'Reload') {
			strDb = 'slave';
		}
		jsonStamp = this._getJsonStamp({strFunc : strFunc});
		arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonValue', 'jsonSearch'];
		arrayValue = [strClass, idModule, strExt, strChild, strFunc, strDb, jsonStamp, jsonValue, jsonSearch];

		if (this._varsListConnect.flag == 'Output') {
			if (this._varsListConnect.flagType) {
				arrayKey.push('flagType');
				arrayValue.push(this._varsListConnect.flagType);
			}
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
			if (this._varsListConnect.flag == 'Reload') {
				this.insLayout.updateTool({flagLock : 0, from : 'list', idTarget : 'Reload'});
				this.vars.varsFlag = obj.json.data.varsFlag;
				this.vars.portal.varsList.varsIni = obj.json.data.varsIni;
				this.insList.updateTableTreeVars({vars : obj.json.data});
			}

		} else if (obj.json.flag == 10) {
			if (this._varsListConnect.flag == 'Reload') {
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
	eventListEditorSuccess : function(obj)
	{
		this.vars.varsFlag = obj.json.data.varsFlag;
		this.vars.portal.varsList.varsIni = obj.json.data.varsIni;
		this.insList.updateTableTreeVars({vars : obj.json.data});
	},

	/**
	 *
	*/
	_eventDetailList : function(obj)
	{
		var objData = this._updateDetailListVars({vars : obj.vars});
		this.insDetail.eventList(objData);
		this._setDetailContent({vars : obj.vars});
	},

	/**
	 *
	*/
	_updateDetailListVarsEdit : function(obj)
	{
		var idAccountTitle = obj.vars.vars.idTarget;
		var arr = this.vars.varsItem.arrSubAccountTitle.arrSelectTag[idAccountTitle];
		if (arr == undefined) {
			obj.arr.flagEditUse = 0;

			return obj.arr;
		}
		obj.arr.flagEditUse = 1;

		return obj.arr;
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
		this.insDetail.eventList({
			strTitle    : this.vars.portal.varsDetail.varsStart.strTitle,
			strClass    : this.vars.portal.varsDetail.varsStart.strClass,
			vars        : {
				varsDetail : [],
				varsBtn    : [],
				varsEdit   : {},
				vars       : {}
			}
		});
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
			else if (obj.from == 'view-eventBtnBottom') insCurrent._eventDetailConnect({flag : obj.vars.vars.vars.idTarget});
		};

		return allot;
	},



	/**
	 *
	*/
	_varsContent : {num : 0},
	_setDetailContent : function(obj)
	{
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
			if (!obj.arr[i].varsSpace) continue;
			var ele = this.insDetail.insView.eleWrap.down('.codeLibViewLineContent', this._varsContent.num);
			this._varsContent.num++;
			if (obj.arr[i].id == 'TableDetail') {
				this._setDetailViewTableDetail({
					vars : obj.arr[i]
				});
			}

			var insSpace = new Code_Lib_Space({
				eleScroll  : this.insDetail.insView.eleInsert,
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
	_varsDetailList : [],
	_setDetailViewTableDetail : function(obj)
	{

		var varsTmpl = obj.vars.tmplVars;
		var idDepartment = this.vars.varsFlag.idDepartment;
		if (idDepartment == 0) {
			idDepartment = 'all';
		} else {
			idDepartment += '';
		}
		var idAccountTitle = this.insDetail.varsEventList.vars.vars.idTarget;

		var strContent = '';
		var strColumn = '';
		var tagTdColumn = obj.vars.tagTdColumn;
		strColumn += tagTdColumn.interpolate({insertPoint : obj.vars.varsStr.strTitle, numWidth : obj.vars.varsSpace.varsStatus.numWidth});

		tagTdColumn = obj.vars.tagTdColumn;
		strColumn += tagTdColumn.interpolate({insertPoint : obj.vars.varsStr.strBalance, numWidth : obj.vars.varsSpace.varsStatus.numWidth});

		var tagTr = obj.vars.tagTr;
		strColumnTr = tagTr.interpolate({insertPoint : strColumn});
		strContent = strColumnTr;
		this._varsDetailList = [];
		var arr = this.vars.varsItem.arrSubAccountTitle.arrSelectTag[idAccountTitle];
		if (arr == undefined) {
			obj.vars.varsSpace.varsDetail.strHtml = this.vars.varsItem.strNoneSub;
			return;
		}
		for (var i = 0; i < arr.length; i++) {

			var strRow = '';
			var idSubAccountTitle = arr[i].value + '';

			var varsNum = this._getDetailViewTableDetailVarsValue({
				idDepartment      : idDepartment,
				idSubAccountTitle : idSubAccountTitle
			});

			var tagTdRow = obj.vars.tagTdRow;
			var str = arr[i].strTitle;
			strRow += tagTdRow.interpolate({insertPoint : str});

			tagTdRow = obj.vars.tagTdRowRight;
			var str = varsNum.numValueComma;
			strRow += tagTdRow.interpolate({insertPoint : str});

			var tagTr = obj.vars.tagTr;
			strColumnTr = tagTr.interpolate({insertPoint : strRow});
			strContent += strColumnTr;

			var tmplVars = (Object.toJSON(varsTmpl)).evalJSON();
			tmplVars.strTitle = arr[i].strTitle;
			tmplVars.vars.idTarget = idSubAccountTitle;
			tmplVars.varsValue.numBalance = varsNum.numValue;
			this._varsDetailList.push(tmplVars);
		}

		var tagTable = obj.vars.tagTable;
		var strTable = tagTable.interpolate({insertPoint : strContent});
		obj.vars.varsSpace.varsDetail.strHtml = strTable;
	},

	/**
	 *
	*/
	_getDetailViewTableDetailVarsValue : function(obj)
	{
		var varsSubValue = this.vars.varsItem.varsSubValue;
		var data = {
			numValue      : 0,
			numValueComma : '0'
		};

		if (!varsSubValue) {
			 return data;
		}
		if (!varsSubValue[obj.idSubAccountTitle]) {
			 return data;
		}
		if (!varsSubValue[obj.idSubAccountTitle][obj.idDepartment]) {
			 return data;
		}

		return varsSubValue[obj.idSubAccountTitle][obj.idDepartment];
	},

	/**
	 *
	*/
	_eventLayoutDetailContent : function()
	{
		if (!this.insDetail.varsEventList) return;
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
	},

	/**
	 *
	*/
	_setDetailChild : function(obj)
	{
		var objDetail = (Object.toJSON(this.vars.portal.varsDetail.templateDetail)).evalJSON();
		var varsList = this._varsDetailList;
		var varsIni = this._getDetailChildVarsIni({
			arr  : (Object.toJSON(this._varsDetailList)).evalJSON()
		});
		var varsDetail = this._getDetailChildVars({
			flag : obj.flag,
			arr  : objDetail,
			vars : this.insDetail.varsEventList.vars
		});
		var idTarget = this.insDetail.varsEventList.vars.vars.idTarget;
		this._extChild({
			strTitleParent : this.insWindow.vars.strTitle,
			strTitleChild  : this.vars.child.varsTitle.editor,
			strExt         : this.strExt,
			strChild       : 'SubEditor',
			strClass       : this.strClass,
			idModule       : this.idModule,
			varsChild      : {
				flagType   : obj.flag,
				idTarget   : idTarget,
				varsDetail : varsDetail,
				varsIni    : varsIni,
				varsList   : varsList
			}
		});

	},

	/**
	 *
	*/
	_getDetailChildVarsIni : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			obj.arr[i].varsValue.numBalance = 0;
		}

		return obj.arr;
	},

	/**
	 *
	*/
	_getDetailChildVars : function(obj)
	{
		var insDisplay = new Code_Lib_TimeDisplay();
		var arrayNew = [];
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'DummyStatus') {
				var strDepartment = this._getDetailChildVarsStrDepartment({
					arr   : this.insNavi.insForm.vars.varsDetail,
					vars  : this.vars.varsFlag,
					str   : obj.arr[i].varsTmpl.strDepartment
				});
				var idAccountTitle = obj.vars.vars.idTarget;
				var strAccountTitle = obj.arr[i].varsTmpl.strAccountTitle + this.vars.varsItem.arrAccountTitle.arrStrTitle[idAccountTitle].strTitleFS;
				var str = strDepartment + ',  ' + strAccountTitle;
				strTmpl = obj.arr[i].varsTmpl.strNormal;
				obj.arr[i].strCommentTitle += strTmpl.replace(RegExp("<%replace%>", "g"), str);
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'JsonData') {
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
		var objData = {
			strTitle    : this.insDetail.vars.varsStart.strTitle,
			strClass    : obj.vars.strClass,
			vars        : {
				varsDetail : this._updateDetailListVarsChild({arr : objDetail, vars : obj.vars }),
				varsBtn    : [],
				varsEdit   : this._updateDetailListVarsEdit({
					arr  : this.insDetail.vars.view.varsEdit,
					vars : obj.vars
				}),
				vars       : obj.vars
			}
		};

		return objData;
	},

	/**
	 *
	*/
	_eventDetailConnect : function(obj)
	{
		if (obj.flag == 'delete') {
			this._eventValue({
				vars     : this.insNavi.getFormValue(),
				idTarget : this.insDetail.varsEventList.vars.vars.idTarget
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
			if (this._varsDetailConnect.flag == 'delete') {
				this.vars.portal.varsDetail.templateDetail = obj.json.data.templateDetail;
				this._resetDetail();
				this.insList.updateTableTreeVars({vars : obj.json.data});

			}

		} else if (obj.json.flag == 40) {
			alert(this.insRoot.vars.varsSystem.str.oldData);

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
	evenDetailtEditorSuccess : function(obj)
	{
		this.vars.varsItem.varsSubValue = obj.json.data.varsSubValue;
		this._eventDetailList({vars : this.insDetail.varsEventList.vars});
	},


	/**
	 *
	*/
	eventDetailResetList : function(obj)
	{
		this.vars.portal.varsDetail.templateDetail = obj.json.data.templateDetail;
		this._resetDetail();
		this.insList.updateTableTreeVars({vars : obj.json.data});
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
			if (obj.arr[i].id == 'TableDetail') {
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
	_iniCss : function()
	{
		this._extCss();
	}
});
{/literal}