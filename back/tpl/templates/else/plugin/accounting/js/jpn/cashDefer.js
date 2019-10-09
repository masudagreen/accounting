{literal}
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_CashDefer = Class.create(Code_Lib_ExtPortal,
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
		this._iniDetail();
	},


	_flagAutoSearchOver : '',
	_varsAutoSearchOver : '',
	bootAutoSearchOver : function(obj)
	{
		var insEscape = new Code_Lib_Escape();

		this._varsAutoSearchOver = {};
		this._flagAutoSearchOver = obj.flag;
		if (obj.flag == 'showLog') {
			this._resetSearch();
			this._eventNaviConnect({flag : 'tree-reload'});
		}
	},

	eventAutoSearchOver : function()
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
		this.insFormJournal = new Code_Plugin_Accounting_Lib_Journal({varsRule : this.vars.varsRule});
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
				if (insCurrent.insDetail) insCurrent.insDetail.eventLayout();

			} else if (obj.from == 'navi-_mousedownNavi') {
				if (obj.vars.id == 'Reload') {
					insCurrent._eventNaviConnect({vars : obj.vars, flag : insCurrent.insNavi.vars.varsStatus.flagNow + '-reload'});

				}

			} else if (obj.from == 'detail-_mousedownNavi') {
				if (obj.vars.id == 'Reload') {
					if (insCurrent._flagScene == 'varsStart') {
						insCurrent._eventNaviDetail({vars : insCurrent._varsStart});
					}
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
		this._extNavi();
		this._setNaviBtn();
	},

	_setNaviBtn : function()
	{
		if (!this.insNavi.insTree.vars.varsDetail[0]) {
			this.insNavi.hideBtn();
		} else {
			this.insNavi.showBtn();
		}
	},

	/**
	 *
	*/
	_getNaviAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			var array = obj.from.split('-');
			var flagNow = array[0];
			var flagType = array[1];

			if (obj.from == 'search-eventBtn') insCurrent._eventNaviConnect({vars : obj.vars, flag : 'search'});
			else if (obj.from == 'search-eventBtnSave') insCurrent._eventNaviConnect({vars : obj.vars, flag : 'search-save'});
			else if (obj.from == 'search-eventBtnDelete') insCurrent._eventNaviConnect({vars : obj.vars, flag : 'search-delete'});
			else if (flagNow.match(/^folder/)) {
				if (flagType == '_mousedownBtn') insCurrent._eventNaviConnect({vars : obj.vars, flag : flagNow + '-search'});
				else if (flagType == 'eventBtnBottom') insCurrent._eventNaviConnect({vars : obj.vars, flag : flagNow + '-save'});
			}
			else if (obj.from == 'tree-_mousedownBtn') insCurrent._eventNaviDetail({vars : obj.vars});
			else if (obj.from == 'tree-_dblclickBtn') insCurrent._eventNaviDetail({flag : 'dblclick', vars : obj.vars});
			else if (obj.from == 'tree-eventPage') insCurrent._eventNaviConnect({vars : obj.vars, flag : 'tree-search'});
			else if (obj.from == 'tree-eventBtnBottom') insCurrent._eventNaviConnect({flag : 'tree-delete'});
		};

		return allot;
	},

	/**
	 *
	*/
	_eventNaviConnect : function(obj)
	{
		if (obj.flag == 'tree-reload') {
			this._eventSearch({
				numLotNow : this._varsSearch.numLotNow,
				ph : {
					arrWhere : this._varsSearch.ph.arrWhere,
					arrOrder : this._varsSearch.ph.arrOrder,
				}
			});

		} else if (obj.flag == 'tree-search') {
			this._eventSearch({
				numLotNow : obj.vars.numLotNow,
				ph : {
					arrWhere : this._varsSearch.ph.arrWhere,
					arrOrder : this._varsSearch.ph.arrOrder,
				}
			});
		}

		this._varsNaviConnect = obj;
		this._sendNaviConnect();
	},

	/**
	 *
	*/
	_eventNaviConnectSuccess : function(obj)
	{
		if (obj.json.flag == 1){
			if (this._varsNaviConnect.flag == 'tree-reload') {
				this.insLayout.updateTool({flagLock : 0, from : 'navi', idTarget : 'Reload'});
				this.insNavi.updateTreePageVars({vars : obj.json.data});
				this._setNaviBtn();

			} else if (this._varsNaviConnect.flag == 'tree-search') {
				this._varsSearch.numLotNow = obj.json.data.numLotNow;
				this.insNavi.updateTreePageVars({vars : obj.json.data});
				this._setNaviBtn();

			} else if (this._varsNaviConnect.flag == 'tree-delete') {
				this._varsSearch.numLotNow = obj.json.data.numLotNow;
				this.insNavi.updateTreePageVars({vars : obj.json.data});
				this._resetDetail();
				this._setNaviBtn();
			}

		} else if (obj.json.flag == 10) {
			if (this._varsNaviConnect.flag == 'tree-reload') {
				this.insLayout.updateTool({flagLock : 0, from : 'navi', idTarget : 'Reload'});
				this._setNaviBtn();

			}
		} else if (obj.json.flag == 8) {
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

	_sendNaviConnect : function() {

		var jsonStamp = {};
		var flag = this._varsNaviConnect.flag;
		var arrayKey = [], arrayValue = [];
		var jsonSearch = Object.toJSON(this._varsSearch);
		var insEscape = new Code_Lib_Escape();

		if (this._varsNaviConnect.flag == 'search' || this._varsNaviConnect.flag.match(/^folder(.*?)-search$/)) {
			var strFunc = 'NaviSearch';
			jsonStamp = this._getJsonStamp({strFunc : strFunc});
			arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonSearch'];
			arrayValue = [this.strClass, this.idModule, this.strExt, this.strChild, strFunc, 'slave', jsonStamp, jsonSearch];

		} else if (this._varsNaviConnect.flag.match(/^folder(.*?)-reload$/)) {
			var strFunc = '';
			if (RegExp.$1) strFunc = 'NaviFolder' + insEscape.strCapitalize({data : RegExp.$1}) + 'Reload';
			else strFunc = 'NaviFolderReload';
			jsonStamp = this._getJsonStamp({strFunc : strFunc});
			arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp'];
			arrayValue = [this.strClass, this.idModule, this.strExt, this.strChild, strFunc, 'slave', jsonStamp];
			this.insLayout.updateTool({flagLock : 1, from : 'navi', idTarget : 'Reload'});

		} else if (this._varsNaviConnect.flag.match(/^folder(.*?)-save$/)) {
			var strFunc = '';
			if (RegExp.$1) strFunc = 'NaviFolder' + insEscape.strCapitalize({data : RegExp.$1}) + 'Save';
			else strFunc = 'NaviFolderSave';
			this._eventValue({vars : this._varsNaviConnect.vars});
			var jsonValue = Object.toJSON(this._varsValue);
			arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonValue'];
			arrayValue = [this.strClass, this.idModule, this.strExt, this.strChild, strFunc, 'master', jsonStamp, jsonValue];

		} else if (this._varsNaviConnect.flag.match(/^format(.*?)-reload$/)) {
			var strFunc = '';
			if (RegExp.$1) strFunc = 'NaviFormat' + insEscape.strCapitalize({data : RegExp.$1}) + 'Reload';
			else strFunc = 'NaviFormatReload';
			jsonStamp = this._getJsonStamp({strFunc : strFunc});
			arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp'];
			arrayValue = [this.strClass, this.idModule, this.strExt, this.strChild, strFunc, 'slave', jsonStamp];
			this.insLayout.updateTool({flagLock : 1, from : 'navi', idTarget : 'Reload'});

		} else if (this._varsNaviConnect.flag.match(/^format(.*?)-save$/)) {
			var strFunc = '';
			if (RegExp.$1) strFunc = 'NaviFormat' + insEscape.strCapitalize({data : RegExp.$1}) + 'Save';
			else strFunc = 'NaviFormatSave';
			this._eventValue({vars : this._varsNaviConnect.vars});
			var jsonValue = Object.toJSON(this._varsValue);
			arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonValue'];
			arrayValue = [this.strClass, this.idModule, this.strExt, this.strChild, strFunc, 'master', jsonStamp, jsonValue];

		} else if (this._varsNaviConnect.flag == 'search-reload') {
			var strFunc = 'NaviSearchReload';
			jsonStamp = this._getJsonStamp({strFunc : strFunc});
			arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp'];
			arrayValue = [this.strClass, this.idModule, this.strExt, this.strChild, strFunc, 'slave', jsonStamp];
			this.insLayout.updateTool({flagLock : 1, from : 'navi', idTarget : 'Reload'});

		} else if (this._varsNaviConnect.flag == 'search-save') {
			this._eventValue({vars : this._varsNaviConnect.vars});
			var jsonValue = Object.toJSON(this._varsValue);
			var strFunc = 'NaviSearchSave';
			arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonValue'];
			arrayValue = [this.strClass, this.idModule, this.strExt, this.strChild, strFunc, 'master', jsonStamp, jsonValue];

		} else if (this._varsNaviConnect.flag == 'search-delete') {
			this._eventValue({vars : this._varsNaviConnect.vars});
			var jsonValue = Object.toJSON(this._varsValue);
			var strFunc = 'NaviSearchDelete';
			jsonStamp = this._getJsonStamp({strFunc : strFunc});
			arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonValue'];
			arrayValue = [this.strClass, this.idModule, this.strExt, this.strChild, strFunc, 'master', jsonStamp, jsonValue];

		} else if (this._varsNaviConnect.flag == 'tree-reload') {
			var strFunc = 'NaviReload';
			jsonStamp = this._getJsonStamp({strFunc : strFunc});
			arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonSearch'];
			arrayValue = [this.strClass, this.idModule, this.strExt, this.strChild, strFunc, 'slave', jsonStamp, jsonSearch];
			this.insLayout.updateTool({flagLock : 1, from : 'navi', idTarget : 'Reload'});

		} else if (this._varsNaviConnect.flag == 'tree-search') {
			var strFunc = 'NaviSearch';
			arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp', 'jsonSearch'];
			arrayValue = [this.strClass, this.idModule, this.strExt, this.strChild, strFunc, 'slave', jsonStamp, jsonSearch];

		} else if (this._varsNaviConnect.flag == 'tree-delete') {
			var strFunc = 'NaviDelete';
			arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp'];
			arrayValue = [this.strClass, this.idModule, this.strExt, this.strChild, strFunc, 'master', jsonStamp];
		}

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
	_iniDetail : function()
	{
		this._extDetail();
	},

	/**
	 *
	*/
	_eventNaviDetail : function(obj)
	{
		this._flagScene = 'varsStart';
		this._setNaviDetail({vars : obj.vars});
	},

	/**
	 *
	*/
	_flagScene : '',
	_setNaviDetail : function(obj)
	{
		var objDetail = (Object.toJSON(this.vars.portal.varsDetail.tmplDetail[this._flagScene])).evalJSON();
		this.insDetail.eventNavi({
			strTitle : this.vars.portal.varsDetail.varsStart.strTitle,
			strClass : this.vars.portal.varsDetail.varsStart.strClass,
			vars     : {
				varsDetail : this._setNaviDetailChild({arr : objDetail, vars : obj.vars}),
				varsEdit   : this.vars.portal.varsDetail.varsEdit,
				varsBtn    : this._updateDetailListVarsBtnDetail({
					arr  : this.insDetail.vars.tmplBtn[this._flagScene],
					vars : obj.vars
				}),
				vars       : obj.vars
			}
		});
		this._setDetailContent({vars : obj.vars});
	},

	/**
	 *
	*/
	_updateDetailListVarsBtnDetail : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			obj.arr[i].flagUse = 1;
			if (obj.arr[i].id == 'AddBtn') {
				if (parseFloat(obj.vars.vars.flagPermitLost)
				 || parseFloat(obj.vars.vars.flagLostJournal)
				) {
					obj.arr[i].flagUse = 0;
				}
			}
		}

		return obj.arr;
	},

	/**
	 *
	*/
	_setNaviDetailChild : function(obj)
	{
		var insDisplay = new Code_Lib_TimeDisplay();
		var insEscape = new Code_Lib_Escape();
		var arrayNew = [];
		this._varsStart = {};
		this._varsStart = obj.vars;
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'Status') {
				var strComment = obj.arr[i].varsTmpl.strComment;

				var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.vars.vars.stampRegister * 1000});
				var strStampRegister = insDisplay.get({flagType : 6, vars : objTime});
				strComment = strComment.replace(/<%stampRegister%>/, strStampRegister);

				var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.vars.vars.stampBook * 1000});
				var strStampBook = insDisplay.get({flagType : 6, vars : objTime});
				strComment = strComment.replace(/<%stampBook%>/, strStampBook);

				obj.arr[i].strComment = strComment;
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'IdLogCash') {
				obj.arr[i].flagDisabled = 0;
				obj.arr[i].strExplain = obj.arr[i].varsTmpl.strNormal;
				if (parseFloat(obj.vars.vars.flagPermitLost)
					 || parseFloat(obj.vars.vars.flagLostJournal)
				) {
					obj.arr[i].flagDisabled = 1;
					obj.arr[i].value = 'dummy';
					obj.arr[i].strExplain = obj.arr[i].varsTmpl.strLost;
					arrayNew.push(obj.arr[i]);
					continue;

				} else if (!parseFloat(obj.vars.vars.flagLogCash)) {
					obj.arr[i].flagDisabled = 1;
					obj.arr[i].value = 'dummy';
					obj.arr[i].strExplain = obj.arr[i].varsTmpl.strNone;
					arrayNew.push(obj.arr[i]);
					continue;

				}
				obj.arr[i].arrayOption = obj.vars.vars.arrayOptionIdLogCash;
				obj.arr[i].value = obj.vars.vars.valueIdLogCash;
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'JsonDetail') {
				obj.arr[i].varsFormJournal.varsDetail = obj.vars.jsonDetail.jsonDetail;
				obj.arr[i].strExplain = '';
				if (parseFloat(obj.vars.vars.flagLostJournal)) {
					obj.arr[i].strExplain = obj.arr[i].varsTmpl.strLost;

				} else if (parseFloat(obj.vars.vars.flagPermitLost)) {
					obj.arr[i].strExplain = obj.arr[i].varsTmpl.strPermitLost;
				}
				obj.arr[i].value = 'dummy';
				arrayNew.push(obj.arr[i]);

			} else if (obj.arr[i].id == 'JsonPermitHistory') {
				if (!obj.vars.jsonPermitHistory.length) continue;
				if (parseFloat(this.vars.varsRule.varsPreference.flagPermitImport)) continue;
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
			arr : this.insDetail.insForm.vars.varsDetail
		});

	},

	/**
	 *
	*/
	_setDetailFormJournal : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormJournal) continue;
			var ele = this.insDetail.insForm.eleWrap.down('.codeLibFormContent', this._varsContent.num);
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
				var numLeft = insCurrent.insDetail.insForm.insFormat.eleTemplate.body.scrollLeft;
				var numTop = insCurrent.insDetail.insForm.insFormat.eleTemplate.body.scrollTop;
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
	_varsDetailFormCheck : [],
	_iniDetailFormCheck : function()
	{
		this._varsDetailFormCheck = [];
		this._setDetailFormCheck({arr : this.insDetail.insForm.vars.varsDetail});
	},

	/**
	 *
	*/
	_setDetailFormCheck : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormCheck) continue;
			var ele = this.insDetail.insForm.eleWrap.down('.codeLibFormContent', this._varsContent.num);
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
		var dataStyle = this._getFormCheckMenuStyle({arr : this.insDetail.insForm.vars.varsDetail});
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
				data.numTop = this.insDetail.insForm.eleWrap.down('.codeLibFormContent', this._varsContent.numPermit).offsetTop;
							/*- this.insDetail.insForm.insFormat.eleTemplate.body.scrollTop;*/
				data.numLeft = this.insDetail.insForm.eleWrap.down('.codeLibFormContent', this._varsContent.numPermit).offsetLeft;
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
	_eventDetailConnect : function(obj)
	{
		if (obj.flag == 'add') {
			if (this.insDetail.checkForm({flagType : 'common'})) return;
			var vars = this.insDetail.getFormValue();
			this._eventValue({
				vars     : vars,
				idTarget : this._varsStart.vars.idTarget
			});

		} else if (obj.flag == 'delete') {
			this._eventValue({
				vars     : {},
				idTarget : this._varsStart.vars.idTarget
			});

		}

		this._varsDetailConnect = obj;
		this._sendDetailConnect();
	},

	/**
	 *
	*/
	_resetDetail : function()
	{
		this._varsStart = {};
		var objData = {
			strTitle : '',
			strClass : '',
			vars     : {
				varsDetail : [],
				varsBtn    : this._updateDetailListVarsBtn({
					arr  : this.insDetail.vars.varsBtn,
					flag : 1
				}),
				varsEdit   : {},
				vars       : {}
			}
		};
		this.insDetail.eventNavi(objData);
	},

	_setNaviDetailEnd : function(obj)
	{
		var objDetail = (Object.toJSON(this.vars.portal.varsDetail.tmplDetail.varsEnd)).evalJSON();
		this._varsStart = {};
		this.insDetail.eventNavi({
			strTitle : this.vars.portal.varsDetail.varsEnd.strTitle,
			strClass : this.vars.portal.varsDetail.varsEnd.strClass,
			vars     : {
				varsDetail : objDetail,
				varsEdit   : {},
				varsBtn    : this.insDetail.vars.tmplBtn.varsEnd
			}
		});
	},

	/**
	 *
	*/
	_eventLayoutDetailContent : function()
	{
		if (!this.insDetail.insForm) return;
		this._setNaviBtn();
		this._varsContent.num = 0;
		this._varsContent.numPermit = 0;
		this._iniDetailFormJournal();
		this._iniDetailFormCheck();
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
			else if (obj.from == 'form-preEventLayout') insCurrent._preEventLayoutDetailContent();
			else if (obj.from == 'form-eventLayout') insCurrent._eventLayoutDetailContent();
			else if (obj.from == 'form-eventBtnBottom') {
				if (obj.vars.vars.vars.flagHide) {
					if (!insCurrent.insWindow.vars.flagHideNow) insCurrent.insWindow.updateHide({ flagEffect : 1 });

				} else {
					insCurrent._eventDetailConnect({flag : obj.vars.vars.vars.idTarget});
				}

			}
		};

		return allot;
	},

	/**
	 *
	*/
	_preEventLayoutDetailContent : function()
	{
		if (!this.insDetail.insForm) return;

	},

	/**
	 *
	*/
	_eventRemoveDetailContent : function()
	{
		if (!this.insDetail.insForm) return;
		this._eventRemoveDetailFormJournal({arr : this.insDetail.insForm.vars.varsDetail});
		this._eventRemoveDetailFormCheck({arr : this.insDetail.insForm.vars.varsDetail});
	},

	/**
	 *
	*/
	_sendDetailConnectSuccess : function(obj)
	{
		if (obj.response.responseText.isJSON()) {
			var json = obj.response.responseText.evalJSON();
			if (json.stamp) {
				if (json.stamp.id) this._varsStamp[json.stamp.id] = json.stamp.stamp;
			}
			if (json.flag) {
				if (json.numNews) this.insRoot.iniPopup({flag : 'news', numNews : json.numNews});
				this._eventDetailConnectSuccess({json : json});
			}
			else if (json.flag == 0) alert(this.insRoot.vars.varsSystem.str.errorSession);
		}
		else alert(this.insRoot.vars.varsSystem.str.errorRequest);
	},


	/**
	 *
	*/
	_eventDetailConnectSuccess : function(obj)
	{
		if (obj.json.flag == 1) {
			if (this._varsDetailConnect.flag == 'add') {
				this.insNavi.updateTreePageVars({vars : obj.json.data});
				this._setNaviDetailEnd();
				this._setNaviBtn();

			} else if (this._varsDetailConnect.flag == 'delete') {
				this._varsSearch.numLotNow = obj.json.data.numLotNow;
				this.insNavi.updateTreePageVars({vars : obj.json.data});
				this._setNaviDetailEnd();
				this._setNaviBtn();
			}

		} else if (obj.json.flag == 8) {
			alert(this.insRoot.vars.varsSystem.str.oldData);

		} else if (obj.json.flag == 10) {


		} else if (obj.json.flag == 40) {
			alert(this.insRoot.vars.varsSystem.str.oldData);
			this._varsSearch.numLotNow = obj.json.data.numLotNow;
			this.insNavi.updateTreePageVars({vars : obj.json.data});
			this._resetDetail();
			this._setNaviBtn();

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