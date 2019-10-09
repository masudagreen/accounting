{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_AccountTitleFSCS = Class.create(Code_Lib_ExtPortal,
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
				insCurrent._eventNaviConnect({flag : 'search'});
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
		var jsonValue = Object.toJSON(this._varsValue);
		var arrayKey = [], arrayValue = [];
		var jsonStamp = {};

		var strFunc = 'NaviSearch';
		jsonStamp = this._getJsonStamp({strFunc : strFunc});
		arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonValue'];
		arrayValue = [strClass, idModule, strExt, strChild, strFunc, 'slave', jsonStamp, jsonValue];

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
				this.vars.portal.varsDetail.templateDetail = obj.json.data.templateDetail;
				this._resetDetail();
				this.insList.updateTableTreeVars({vars : obj.json.data});
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
		var arrayKey = [], arrayValue = [];
		var jsonStamp = {};
		var jsonValue = Object.toJSON(this._varsValue);

		var strFunc = 'ListReload';
		jsonStamp = this._getJsonStamp({strFunc : strFunc});
		this.insLayout.updateTool({flagLock : 1, from : 'list', idTarget : 'Reload'});
		arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonValue'];
		arrayValue = [this.strClass, this.idModule, this.strExt, this.strChild, strFunc, 'slave', jsonStamp, jsonValue];

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
	},


	/**
	 *
	*/
	_eventListConnectSuccess : function(obj)
	{
		if (obj.json.flag == 1){
			if (this._varsListConnect.flag == 'Reload') {
				this.insLayout.updateTool({flagLock : 0, from : 'list', idTarget : 'Reload'});
				this.vars.portal.varsDetail.templateDetail = obj.json.data.templateDetail;
				this._resetDetail();
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
			else if (obj.from == 'view-eventBtnBottom') insCurrent._eventDetailConnect({flag : obj.vars.vars.vars.idTarget});
		};

		return allot;
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
	eventDetailResetList : function(obj)
	{
		this.vars.portal.varsDetail.templateDetail = obj.json.data.templateDetail;
		this._resetDetail();
		this.insList.updateTableTreeVars({vars : obj.json.data});
	},

	/**
	 *
	*/
	_setDetailContent : function(obj)
	{

	},


	/**
	 *
	*/
	_eventLayoutDetailContent : function()
	{
		if (!this.insDetail.varsEventList) return;
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
		var varsDetail = [];
		var varsIni = null;

		if (obj.flag == 'add') {
			varsDetail = this._getDetailChildVars({
				arr  : objDetail,
				flag : obj.flag,
				vars : this.insDetail.varsEventList.vars
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

		} else if (obj.flag == 'edit') {
			varsIni = this._getDetailChildVars({
				flag : 'editIni',
				arr  : (Object.toJSON(objDetail)).evalJSON(),
				vars : this.insDetail.varsEventList.vars
			});
			varsDetail = this._getDetailChildVars({
				flag : obj.flag,
				arr  : objDetail,
				vars : this.insDetail.varsEventList.vars
			});
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
				varsDetail : varsDetail
			}
		});
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
				if (obj.flag == 'add' || obj.flag == 'editIni') {
					obj.arr[i].value = '';

				} else {
					obj.arr[i].value = obj.vars.strTitle;

				}
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'IdAccountTitle') {
				obj.arr[i].flagDisabled = 0;
				obj.arr[i].strExplain = obj.arr[i].varsTmpl.strNormal;
				if (obj.flag == 'add') {
					obj.arr[i].value = '';

				} else {
					if (obj.flag == 'edit' || obj.flag == 'editIni') {
						if (obj.vars.flagDefault) {
							obj.arr[i].flagDisabled = 1;
							if (obj.vars.flagDefault) {
								obj.arr[i].strExplain = obj.arr[i].varsTmpl.strDefault;
							}
							obj.arr[i].value = obj.vars.vars.idTarget;

						} else if (obj.vars.vars.flagUseAccountTitle
							 || obj.vars.vars.flagUseAccountTitles
						) {
							obj.arr[i].flagDisabled = 1;
							obj.arr[i].strExplain = obj.arr[i].varsTmpl.strAllLog;
							obj.vars.vars.idTarget.match(/^custom_(.*?)_(.*?)_(.*?)$/);
							obj.arr[i].value = RegExp.$3;

						} else {
							obj.vars.vars.idTarget.match(/^custom_(.*?)_(.*?)_(.*?)$/);
							obj.arr[i].value = RegExp.$3;
							if (obj.flag == 'editIni') {
								obj.arr[i].value = '';
							}
						}

					} else {
						if (obj.vars.flagDefault) {
							obj.arr[i].value = obj.vars.vars.idTarget;

						} else {
							obj.vars.vars.idTarget.match(/^custom_(.*?)_(.*?)_(.*?)$/);
							obj.arr[i].value = RegExp.$3;
						}
					}
				}
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'ArrCommaIdAccountTitle') {
				obj.arr[i].varsFormArea.varsDetail = this.getDetailChildVarsAreaDetail({
					flag : obj.flag,
					arr  : obj.arr[i],
					vars : obj.vars
				});
				obj.arr[i].value = '';
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'FlagUse') {
				obj.arr[i].flagDisabled = 0;
				obj.arr[i].strExplain = obj.arr[i].varsTmpl.strNormal;
				if (obj.flag == 'add' || obj.flag == 'editIni') {
					obj.arr[i].value = 1;
					arrayNew.push(obj.arr[i]);

				} else {
					obj.arr[i].value = obj.vars.vars.flagUse;
					if (obj.flag == 'edit') {
						var strTitle = this._getOptionTitle({
							arr      : obj.arr[i].arrayOption,
							idTarget : obj.arr[i].value
						});
						obj.arr[i].strExplain = obj.arr[i].strExplain.replace(/<%replace%>/, strTitle);
					}
					arrayNew.push(obj.arr[i]);

				}
			}
		}

		return arrayNew;
	},


	/**
	 *
	*/

	getDetailChildVarsAreaDetail : function(obj)
	{
		this._varsDetailChildVarsTableTree = null;
		var varsDetail = (Object.toJSON(this.insList.getTableTreeVarsDetail())).evalJSON();
		this._getDetailChildVarsTableTree({
			vars : obj.vars,
			arr  : varsDetail
		});
		var arrLine = this._varsDetailChildVarsTableTree;

		var tmplInsert = (Object.toJSON(obj.arr.varsFormArea.templateDetail)).evalJSON();
		tmplInsert.vars.idTarget = 'insertPoint';
		var arrayNewArea = [];
		for (var i = 0; i < arrLine.length; i++) {
			if (arrLine[i].vars.idTarget == 'currentTermProfitOrLossPre') continue;
			var tmplDetail = (Object.toJSON(obj.arr.varsFormArea.templateDetail)).evalJSON();
			tmplDetail.strTitle = arrLine[i].strTitle;
			tmplDetail.vars.idTarget = arrLine[i].vars.idTarget;
			arrayNewArea.push(tmplDetail);
			if (obj.flag == 'add' || obj.flag == 'copy') {
				if (obj.vars.vars.idTarget == arrLine[i].vars.idTarget) {
					arrayNewArea.push(tmplInsert);

				}
				tmplDetail.strClassFont = '';

			} else {
				if (obj.vars.vars.idTarget != arrLine[i].vars.idTarget) {
					tmplDetail.strClassFont = '';
				}
			}
		}

		return arrayNewArea;

	},

	/**
	 *
	*/
	_varsDetailChildVarsTableTree : null,
	_getDetailChildVarsTableTree : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].vars.idTarget == obj.vars.vars.idTarget) {
				this._varsDetailChildVarsTableTree = obj.arr;
				return;
			}
			if (obj.arr[i].child.length) {
				this._getDetailChildVarsTableTree({
					arr  : obj.arr[i].child,
					vars : obj.vars
				});
			}
		}

	},


	/**
	 *
	*/
	_updateDetailListVars : function(obj)
	{
		var objDetail = (Object.toJSON(this.vars.portal.varsDetail.templateDetail)).evalJSON();
		var varsBtn = (Object.toJSON(this.vars.portal.varsDetail.varsBtn)).evalJSON();
		var flagBtn = 0;
		if (!obj.vars.vars.varsAuthority) {
			flagBtn = 1;
		} else {
			if (!obj.vars.vars.varsAuthority.flagDelete
				 || obj.vars.flagDefault
				 || obj.vars.vars.flagUseAccountTitle
				 || obj.vars.vars.flagUseAccountTitleTemp
			) {
				flagBtn = 1;
			}
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
					varsAuthority : obj.vars.vars.varsAuthority
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

		if (obj.flag) {
			obj.arr.flagAddUse = 0;
			obj.arr.flagCopyUse = 0;

		} else {
			obj.arr.flagAddUse = 1;
			obj.arr.flagCopyUse = 1;
		}
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
	_updateDetailListVarsChild : function(obj)
	{
		var insDisplay = new Code_Lib_TimeDisplay();
		var insEscape = new Code_Lib_Escape();
		var arrayNew = [];

		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'StrTitle') {
				obj.arr[i].value = obj.vars.strTitle;
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'IdAccountTitle') {
				if (obj.vars.flagDefault) {
					obj.arr[i].value = obj.vars.vars.idTarget;

				} else {
					obj.vars.vars.idTarget.match(/^custom_(.*?)_(.*?)_(.*?)$/);
					obj.arr[i].value = RegExp.$3;
				}
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'DummyFlagUse') {
				obj.arr[i].value = obj.vars.varsColumnDetail.flagUse;
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