{literal}
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_LogImportRetry = Class.create(Code_Lib_ExtPortal,
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

	_flagLoopFilter : 0,
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

		} else if (obj.flag == 'showRetryBtn') {
			if (this._flagScene == 'varsStart' && !this._flagLoopFilter) {
				this.insDetail.showBtnBottom();
			}

		} else if (obj.flag == 'loopFilter') {
			this._eventDetailConnect({flag : 'add'});
			this._flagLoopFilter = 1;
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
				varsDetail : this._setNaviDetailChild({arr : objDetail, vars : obj.vars ,flagBack : obj.flagBack}),
				varsEdit   : this.vars.portal.varsDetail.varsEdit,
				varsBtn    : this._updateDetailListVarsBtn({
					arr  : this.insDetail.vars.tmplBtn[this._flagScene],
					flag : 0
				}),
				vars       : obj.vars
			}
		});
		this._setDetailContent({vars : obj.vars});
	},

	/**
	 *
	*/
	_varsStart : {},
	_varsCol : {},
	_setNaviDetailChild : function(obj)
	{
		var insDisplay = new Code_Lib_TimeDisplay();
		var insEscape = new Code_Lib_Escape();

		if (this._flagScene == 'varsStart') {
			this._varsStart = {};
			this._varsStart = obj.vars;

			var arrayOption = [];
			var numLength = obj.vars.vars.jsonData.varsColumn.length;
			for (var i = 0; i < numLength; i++) {
				if (i == 0) {
					continue;
				}
				var strTitle = this.vars.varsItem.strCol;
				strTitle = strTitle.replace(/<%num%>/, i);
				strTitle = strTitle.replace(/<%strTitle%>/, obj.vars.vars.jsonData.varsColumn[i]);
				var row = {
					strTitle : strTitle,
					value    : i
				};
				arrayOption.push(row);
			}
			for (var i = 0; i < obj.arr.length; i++) {
				if (obj.arr[i].id == 'Status') {
					var strComment = obj.arr[i].varsTmpl.strComment;
					var strType = '';
					if (obj.vars.vars.flagType == 'mail') {
						strType = this.vars.varsItem.strMail;

					} else if (obj.vars.vars.flagType == 'item') {
						strType = this.vars.varsItem.strItem;

					} else if (obj.vars.vars.flagType == 'post') {
						strType = this.vars.varsItem.strPost;

					} else if (obj.vars.vars.flagType == 'api') {
						strType = this.vars.varsItem.strApi;

					} else if (obj.vars.vars.flagType == 'banksWeb') {
						strType = this.vars.varsItem.strBanksWeb;

					} else if (obj.vars.vars.flagType == 'banksFile') {
						strType = this.vars.varsItem.strBanksFile;

					} else if (obj.vars.vars.flagType == 'banksWrite') {
						strType = this.vars.varsItem.strBanksWrite;
					}
					strComment = strComment.replace(/<%flagType%>/, strType);

					var numAll = obj.vars.vars.jsonData.varsDetail.length;
					strComment = strComment.replace(/<%numAll%>/, numAll);

					var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.vars.vars.stampRegister * 1000});
					var strStampRegister = insDisplay.get({flagType : 6, vars : objTime});
					strComment = strComment.replace(/<%stampRegister%>/, strStampRegister);

					var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.vars.vars.stampUpdate * 1000});
					var strStampUpdate = insDisplay.get({flagType : 6, vars : objTime});
					strComment = strComment.replace(/<%stampUpdate%>/, strStampUpdate);

					obj.arr[i].strComment = strComment;

				} else if (obj.arr[i].id.match(/^NumCol(.*?)$/)) {
					obj.arr[i].arrayOption = arrayOption;
					var id = RegExp.$1;
					var arrMatch = [];
					var arr = this.vars.varsItem.varsMatch;
					var numAll = arr.length;
					for (var j = 0; j < numAll; j++) {
						if (arr[j].id != id) {
							continue;
						}
						arrMatch.push(arr[j]);
					}

					var numAllMatch = arrMatch.length;
					var flag = 0;
					var numAllOption = arrayOption.length;
					for (var k = 0; k < numAllOption; k++) {
						var strTitle = arrayOption[k].strTitle;
						for (var j = 0; j < numAllMatch; j++) {
							var str = arrMatch[j].str;
							var reg = new RegExp(str);
							if (strTitle.match(reg)) {
								obj.arr[i].value = arrayOption[k].value;
								flag = 1;
								break;
							}
						}
						if (flag) {
							break;
						}
					}
					if (obj.flagBack) {
						var id = insEscape.strLowCapitalize({data : obj.arr[i].id});
						obj.arr[i].value = this._varsCol[id];
					}

				} else if (obj.arr[i].id == 'CsvTable') {
					var temp = obj.vars.vars.strHtml.interpolate({idSelf : this.idSelf + obj.arr[i].id});
					obj.arr[i].varsSpace.varsDetail.strHtml = temp;
				}
			}

		} else if (this._flagScene == 'varsEnd') {
			for (var i = 0; i < obj.arr.length; i++) {
				if (obj.arr[i].id == 'Table') {
					var temp = obj.strHtml.interpolate({idSelf : this.idSelf + obj.arr[i].id});
					obj.arr[i].varsSpace.varsDetail.strHtml = temp;
				}
			}
		}

		return obj.arr;
	},

	_varsContent : {num : 0},
	_setDetailContent : function(obj)
	{
		this._varsContent.num = 0;
		this._iniDetailSpace({vars : obj.vars});
		this._iniDetailFormSelect();
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
	_varsDetailFormSelect : {},
	_setDetailFormSelect : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id.match(/^NumCol/)) {
				var insFormSelect = new Code_Lib_FormSelect({
					eleInsert  : $(this.insDetail.insForm.idSelf + obj.arr[i].id),
					insRoot    : this.insRoot,
					insCurrent : this,
					idSelf     : this.idSelf + 'FormSelect' + obj.arr[i].id,
					allot      : this._getDetailFormSelectAllot(),
					vars       : null
				});
				this._varsDetailFormSelect[obj.arr[i].id] = insFormSelect;
			}

		}
	},

	/**
	 *
	*/
	_getDetailFormSelectAllot : function()
	{
		var allot = function(obj) {
			var insCurrent = obj.insCurrent;
			insCurrent._updateDetailSpace();

		};

		return allot;
	},

	_updateDetailSpace : function(obj)
	{
		this._varsContent.num = 0;
		var ele = this.insDetail.insForm.eleWrap.down('.codeLibFormContent', this._varsContent.num);
		if (ele) {
			ele.innerHTML = '';
		}
		this._iniDetailSpace();
	},

	/**
	 *
	*/
	_varsDetailSpace : {},
	_iniDetailSpace : function(obj)
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
			var insSpace = new Code_Lib_Space({
				eleInsert  : ele,
				insRoot    : this.insRoot,
				insCurrent : this,
				idSelf     : this.idSelf + 'Space' + obj.arr[i].id,
				allot      : {},
				vars       : obj.arr[i].varsSpace
			});
			this._varsDetailSpace.ins = insSpace;

			if (this._flagScene != 'varsStart') {
				break;
			}
			this.insDetail.setValue();
			var vars = this.insDetail.getFormValue();

			this._varsCol = {
				numColStampBook : vars.NumColStampBook,
				numColNumValue  : vars.NumColNumValue,
				numColStrTitle  : vars.NumColStrTitle,
			};

			var idTd = '';
			var numAll = this._varsStart.vars.jsonData.varsColumn.length;
			var strTr = '<tr>';
			for (var j = 0; j < numAll; j++) {
				var strTd = '<td class="codeLibBaseTableColumnMiddle">';
				if (j == 0) {
					strTd += '</td>';
					strTr += strTd;
					continue;
				}
				if ((this._varsCol.numColStampBook) == j) {
					strTd += this.vars.varsItem.strColStampBook + '</td>';

				} else if ((this._varsCol.numColNumValue) == j) {
					strTd += this.vars.varsItem.strColNumValue + '</td>';

				} else if ((this._varsCol.numColStrTitle) == j) {
					strTd += this.vars.varsItem.strColStrTitle + '</td>';

				} else {
					strTd += '</td>';
				}
				strTr += strTd;
			}
			strTr += '</tr>';
			$(this.idSelf + obj.arr[i].id + '_Body').insert({'top': strTr});

			numAll = this._varsStart.vars.jsonData.varsDetail.length;
			for (var j = 0; j < numAll; j++) {
				var idTr = this._varsStart.vars.jsonData.varsDetail[j].id;

				idTd = this._varsCol.numColStampBook;
				$(this.idSelf + obj.arr[i].id + '_Tr' + idTr + '_Td' + idTd).addClassName('codeLibBaseTableRowSelect');

				idTd = this._varsCol.numColNumValue;
				$(this.idSelf + obj.arr[i].id + '_Tr' + idTr + '_Td' + idTd).addClassName('codeLibBaseTableRowSelect');

				idTd = this._varsCol.numColStrTitle;
				$(this.idSelf + obj.arr[i].id + '_Tr' + idTr + '_Td' + idTd).addClassName('codeLibBaseTableRowSelect');
			}

			if (vars.NumColStampBook == vars.NumColNumValue
				|| vars.NumColStampBook == vars.NumColStrTitle
				|| vars.NumColNumValue == vars.NumColStrTitle
			) {
				this.insDetail.showFormAttestError({flagType : 'strCol'});
			}
			return;
		}
	},

	/**
	 *
	*/
	_eventDetailConnect : function(obj)
	{
		if (this._flagScene == 'varsStart') {
			if (obj.flag == 'filter') {
				if (this.insDetail.checkForm({flagType : 'common'})) return;
				var vars = this.insDetail.getFormValue();
				if (vars.NumColStampBook == vars.NumColNumValue
					|| vars.NumColStampBook == vars.NumColStrTitle
					|| vars.NumColNumValue == vars.NumColStrTitle
				) {
					this.insDetail.showFormAttestError({flagType : 'strCol'});
					return;
				}
				this._varsCol = {
					numColStampBook : vars.NumColStampBook,
					numColNumValue  : vars.NumColNumValue,
					numColStrTitle  : vars.NumColStrTitle,
				};
				var varsValue = {};
				varsValue.numColStampBook = this._varsCol.numColStampBook;
				varsValue.numColNumValue = this._varsCol.numColNumValue;
				varsValue.numColStrTitle = this._varsCol.numColStrTitle;
				varsValue.strTitle = this._varsStart.vars.jsonData.varsDetail[0].varsDetail[this._varsCol.numColStrTitle].value;
				this._checkAutoSearch({idTarget : 'LogImport', vars : varsValue});
				return;

			} else if (obj.flag == 'delete') {
				this._eventValue({
					vars     : {},
					idTarget : this._varsStart.vars.idTarget
				});

			} else if (obj.flag == 'add') {
				this._eventValue({
					vars     : {},
					idTarget : this._varsStart.vars.idTarget
				});
			}

		} else if (this._flagScene == 'varsEnd') {
			if (obj.flag == 'backStart') {
				this._flagScene = 'varsStart';
				this._setNaviDetail({vars : this._varsStart, flagBack : 'varsEnd'});
				return;

			} else if (obj.flag == 'backHide') {
				if (!this.insWindow.vars.flagHideNow) this.insWindow.updateHide({ flagEffect : 1 });
				return;
			}
		}

		this._varsDetailConnect = obj;
		this._sendDetailConnect();
	},

	_flagAutoData : '',
	_varsAutoData : {},
	_checkAutoSearch : function(obj)
	{
		var insEscape = new Code_Lib_Escape();
		this._varsAutoData = (obj.vars)? obj.vars : {};
		this._flagAutoData = obj.idTarget;

		this.eventAutoSearch();
	},

	eventAutoSearch : function()
	{
		if (this._flagAutoData == 'LogImport') {
			var varsData = this.insTop.checkChildData({idTarget : 'Log'});
			varsData.insClass.bootAutoSearchOver({
				flag : 'addLogImportDetail',
				vars : this._varsAutoData
			});
		}
	},

	/**
	 *
	*/
	_resetDetail : function()
	{
		this._varsStart = {};
		this._varsCol = {};
		this._flagScene = 'varsStart';

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
		var objDetail = (Object.toJSON(this.vars.portal.varsDetail.tmplDetail[this._flagScene])).evalJSON();
		if (!obj.vars) {
			this._varsStart = {};
			this._varsCol = {};

		} else {
			this._varsStart = obj.vars;
		}
		this.insDetail.eventNavi({
			strTitle : this.vars.portal.varsDetail.varsEnd.strTitle,
			strClass : this.vars.portal.varsDetail.varsEnd.strClass,
			vars     : {
				varsDetail : this._setNaviDetailChild({arr : objDetail, strHtml : obj.strHtml}),
				varsEdit   : {},
				varsBtn    : this._updateDetailListVarsBtnEnd({
					arr      : this.insDetail.vars.tmplBtn[this._flagScene],
					vars     : obj.vars,
					flagHide : obj.flagHide
				}),
				vars       : obj.vars
			}
		});
		this._setDetailContent({});
	},

	/**
	 *
	*/
	_updateDetailListVarsBtnEnd : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			obj.arr[i].flagUse = 1;
			if (obj.flagHide) {
				if (!obj.arr[i].vars.flagHide) {
					obj.arr[i].flagUse = 0;
				}
			}
		}

		return obj.arr;
	},

	/**
	 *
	*/
	_eventLayoutDetailContent : function()
	{
		if (!this.insDetail.insForm) return;
		this._setNaviBtn();
		this._setDetailContent({});
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
			else if (obj.from == 'form-eventBtnBottom') insCurrent._eventDetailConnect({flag : obj.vars.vars.vars.idTarget});
			else if (obj.from == 'form-eventLayout') insCurrent._eventLayoutDetailContent();
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
		this._flagLoopFilter = 0;
		if (obj.json.flag == 1) {
			if (this._varsDetailConnect.flag == 'add') {
				this.insNavi.updateTreePageVars({vars : obj.json.data});
				if (this._varsStart.vars.idTarget == obj.json.data.idTarget) {
					this._flagScene = 'varsEnd';
					this._setNaviDetailEnd({
						vars     : obj.json.data.varsStart,
						strHtml  : obj.json.data.strHtml,
						flagHide : (obj.json.data.numRows == 0 && obj.json.data.numLotNow == 0)? 1 : 0
					});
				}
				this._setNaviBtn();

			} else if (this._varsDetailConnect.flag == 'delete') {
				this._varsSearch.numLotNow = obj.json.data.numLotNow;
				this.insNavi.updateTreePageVars({vars : obj.json.data});
				this._resetDetail();
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