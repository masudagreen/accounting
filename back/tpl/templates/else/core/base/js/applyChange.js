{literal}
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Core_Base_ApplyChange = Class.create(Code_Lib_ExtPortal,
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
					insCurrent._eventDetailConnect({flag : 'reload'});

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
		this._setNaviDetail({vars : obj.vars});
	},

	/**
	 *
	*/
	_resetDetail : function()
	{
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

	/**
	 *
	*/
	_setDetailEnd : function()
	{
		var objData = {
			strTitle : this.vars.portal.varsDetail.varsEnd.strTitle,
			strClass : this.vars.portal.varsDetail.varsEnd.strClass,
			vars     : {
				varsDetail : this.vars.portal.varsDetail.varsEnd.varsDetail,
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

	/**
	 *
	*/
	_setNaviDetail : function(obj)
	{
		var objDetail = (Object.toJSON(this.vars.portal.varsDetail.templateDetail)).evalJSON();

		this.insDetail.eventNavi({
			strTitle : this.vars.portal.varsDetail.varsStart.strTitle,
			strClass : this.vars.portal.varsDetail.varsStart.strClass,
			vars     : {
				varsDetail : this._setNaviDetailChild({arr : objDetail, vars : obj.vars }),
				varsEdit   : this.vars.portal.varsDetail.varsEdit,
				varsBtn    : this._updateDetailListVarsBtn({
					arr  : this.insDetail.vars.varsBtn,
					flag : 0
				}),
				vars       : obj.vars
			}
		});
	},

	/**
	 *
	*/
	_setNaviDetailChild : function(obj)
	{
		var insDisplay = new Code_Lib_TimeDisplay();
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'StampRegister') {
				var objTime = this.insRoot.insTimeZone.adjustDate({stamp : obj.vars.vars.stampRegister * 1000});
				var strTime = insDisplay.get({flagType : 1, vars : objTime});
				obj.arr[i].strComment = obj.arr[i].strComment.replace(/<%strTitle%>/, obj.vars.vars.pastStrCodeName);
				obj.arr[i].strComment = obj.arr[i].strComment.replace(/<%strTime%>/, strTime);

			} else if (obj.arr[i].id == 'StrCodeName') {
				obj.arr[i].value = obj.vars.vars.strCodeName;
				obj.arr[i].strExplain = obj.arr[i].strExplain.replace(/<%replace%>/, obj.vars.vars.pastStrCodeName);

			} else if (obj.arr[i].id == 'IdLogin') {
				obj.arr[i].value = obj.vars.vars.idLogin;
				obj.arr[i].strExplain = obj.arr[i].strExplain.replace(/<%replace%>/, obj.vars.vars.pastIdLogin);

			} else if (obj.arr[i].id == 'StrMailPc') {
				obj.arr[i].value = obj.vars.vars.strMailPc;
				obj.arr[i].strExplain = obj.arr[i].strExplain.replace(/<%replace%>/, obj.vars.vars.pastStrMailPc);
			}
		}

		return obj.arr;
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
					arrOrder : this._varsSearch.ph.arrOrder
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

			} else if (this._varsNaviConnect.flag == 'tree-search') {
				this._varsSearch.numLotNow = obj.json.data.numLotNow;
				this.insNavi.updateTreePageVars({vars : obj.json.data});

			}

		} else if (obj.json.flag == 10) {
			if (this._varsNaviConnect.flag == 'tree-reload') {
				this.insLayout.updateTool({flagLock : 0, from : 'navi', idTarget : 'Reload'});

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


	/**
	 *
	*/
	_getDetailAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == 'form-eventBtnBottom') insCurrent._eventDetailConnect({flag : obj.vars.vars.vars.idTarget});
		};

		return allot;
	},

	/**
	 *
	*/
	_eventDetailConnect : function(obj)
	{
		if (obj.flag == 'reload') {
			this._eventValue({
				vars     : '',
				idTarget : this.insDetail.varsEventNavi.vars.vars.idTarget
			});

		} else if (obj.flag == 'edit' || obj.flag == 'delete') {
			if (this.insDetail.checkForm({flagType : 'common'})) return;
			this._eventValue({
				vars     : this.insDetail.getFormValue(),
				idTarget : this.insDetail.varsEventNavi.vars.vars.idTarget
			});

		}

		this._varsDetailConnect = obj;
		this._sendDetailConnect();
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
			if (this._varsDetailConnect.flag == 'reload') {
				this.insLayout.updateTool({flagLock : 0, from : 'detail', idTarget : 'Reload'});
				this.insNavi.updateTreeDetailLineVars({vars : obj.json.data});
				if (this.insDetail.varsEventNavi.vars.vars.idTarget == obj.json.data.vars.idTarget) {
					this._eventNaviDetail({vars : obj.json.data});
				}

			} else if (this._varsDetailConnect.flag == 'delete' || this._varsDetailConnect.flag == 'edit') {
				this._varsSearch.numLotNow = obj.json.data.numLotNow;
				this.insNavi.updateTreePageVars({vars : obj.json.data});
				this._setDetailEnd();
			}

		} else if (obj.json.flag == 8) {
			alert(this.insRoot.vars.varsSystem.str.oldData);

		} else if (obj.json.flag == 10) {
			this.insLayout.updateTool({flagLock : 0, from : 'detail', idTarget : 'Reload'});
			if (this.insDetail.varsEventNavi.vars.vars.idTarget == obj.json.data.vars.idTarget) {
				this._eventNaviDetail({vars : this.insDetail.varsEventNavi.vars});
			}

		} else if (obj.json.flag == 40) {
			this.insLayout.updateTool({flagLock : 0, from : 'detail', idTarget : 'Reload'});
			this._varsSearch.numLotNow = obj.json.data.numLotNow;
			this.insNavi.updateTreePageVars({vars : obj.json.data});
			this._resetDetail();

		} else if (obj.json.flag == 41) {
			this._varsSearch.numLotNow = obj.json.data.numLotNow;
			this.insNavi.updateTreePageVars({vars : obj.json.data});
			this._resetDetail();

		} else if (obj.json.flag == 'strCodeName'
				|| obj.json.flag == 'strCodeNamePast'
				|| obj.json.flag == 'idLoginPast'
				|| obj.json.flag == 'idLogin'
				|| obj.json.flag == 'strMailPc'
		) {
			if (this.insDetail.varsEventNavi.vars.vars.idTarget == obj.json.data.vars.idTarget) {
				this.insDetail.showFormAttestError({flagType : obj.json.flag});
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