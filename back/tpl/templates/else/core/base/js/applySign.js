{literal}
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Core_Base_ApplySign = Class.create(Code_Lib_ExtPortal,
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
		this._setDetailContent();
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
				obj.arr[i].strComment = obj.arr[i].strComment.replace(/<%strTime%>/, strTime);

			} else if (obj.arr[i].id == 'StrCodeName') {
				obj.arr[i].value = obj.vars.vars.strCodeName;

			} else if (obj.arr[i].id == 'IdLogin') {
				obj.arr[i].value = obj.vars.vars.idLogin;

			} else if (obj.arr[i].id == 'StrMailPc') {
				obj.arr[i].value = obj.vars.vars.strMailPc;

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
	_getDetailAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == 'eventRemove-detail') insCurrent._eventRemoveDetailContent();
			else if (obj.from == 'form-preEventLayout') insCurrent._preEventLayoutDetailContent();
			else if (obj.from == 'form-eventLayout') insCurrent._eventLayoutDetailContent();
			else if (obj.from == 'form-eventBtnBottom') {
				if (obj.vars.vars.vars.flagBack) {
					insCurrent._backDetailEnd();

				} else {
					if (obj.vars.vars.vars.idTarget == 'edit' || obj.vars.vars.vars.idTarget == 'delete') {
						insCurrent._eventDetailConnect({flag : obj.vars.vars.vars.idTarget});

					}
				}

			}
		};

		return allot;
	},


	/**
	 *
	*/
	_setDetailContent : function()
	{
		this._iniDetailFormList();
	},


	/**
	 *
	*/
	_iniDetailFormList : function()
	{
		this._extDetailFormList();
	},


	/**
	 *
	*/
	_preEventLayoutDetailContent : function()
	{
		if (!this.insDetail.insForm) return;
		this._getDetailFormListVars({arr : this.insDetail.insForm.vars.varsDetail});

	},

	/**
	 *
	*/
	_eventRemoveDetailContent : function()
	{
		if (!this.insDetail.insForm) return;
		this._eventRemoveDetailFormList({arr : this.insDetail.insForm.vars.varsDetail});

	},

	/**
	 *
	*/
	_eventLayoutDetailContent : function()
	{
		if (!this.insDetail.insForm) return;
		this._iniDetailFormList();

	},


	/**
	 *
	*/
	_getDetailFormListAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			var insParent = insCurrent.insCurrent;
			if (obj.from == '_mousedownAdd') {
				obj.arr = insParent.insDetail.insForm.vars.varsDetail;
				var num = 0;
				for (var i = 0; i < obj.arr.length; i++) {
					if (!obj.arr[i].varsFormList) continue;
					if (insCurrent.idSelf == insParent.insDetail.insForm.idSelf + 'DetailFormList' + obj.arr[i].id) {
						if (obj.arr[i].id == 'IdTerm' || obj.arr[i].id == 'IdModule') {
							var array = obj.arr[i].id.split('Id');
							insParent.insRoot.insChoice.setBoot({
								flagId       : obj.arr[i].id,
								idTarget     : array[1],
								idModule     : 'Base',
								flagCheckUse : 0,
								strFunc      : 'setDetailFormListChoiceValue',
								numTop       : insParent._staticDetailFormList.numTop + $(insParent.insWindow.idWindow).offsetTop,
								numLeft      : insParent._staticDetailFormList.numLeft + $(insParent.insWindow.idWindow).offsetLeft,
								insCurrent   : insParent
							});
						}

						break;
					}
					num++;
				}


				return 1;
			}
		};

		return allot;
	},


	/**
	 *
	*/
	setDetailFormListChoiceValue : function(obj)
	{
		this.insDetail.setValue();

		obj.arr = this.insDetail.insForm.vars.varsDetail;
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormList) continue;
			if (obj.arr[i].id == obj.flagId) {
				var data = (Object.toJSON(obj.arr[i].varsFormList.templateDetail)).evalJSON();
				data.value = obj.vars.strTitle;
				obj.arr[i].varsFormList.varsDetail[0] = data;
				obj.arr[i].value = obj.vars.vars.idTarget;

				this.vars.portal.varsDetail.varsDetail = obj.arr;
				this._eventRemoveDetailContent();

				this.insDetail.eventNavi({
					strTitle : this.vars.portal.varsDetail.varsStart.strTitle,
					strClass : this.vars.portal.varsDetail.varsStart.strClass,
					vars     : {
						varsDetail : this.vars.portal.varsDetail.varsDetail,
						varsEdit   : this.vars.portal.varsDetail.varsEdit,
						varsBtn    : this.insDetail.vars.varsBtn,
						vars       : this.insDetail.varsEventNavi.vars
					}
				});
				this._setDetailContent();




				return;
			}
			num++;
		}

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

		} else if (obj.flag == 'edit') {
			if (this.insDetail.checkForm({flagType : 'common'})) return;
			this._eventValue({
				vars     : this.insDetail.getFormValue(),
				idTarget : this.insDetail.varsEventNavi.vars.vars.idTarget
			});

		} else if (obj.flag == 'delete') {
			this._eventValue({
				vars     : '',
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

		} else if (obj.json.flag == 10) {
			this.insLayout.updateTool({flagLock : 0, from : 'detail', idTarget : 'Reload'});
			if (this.insDetail.varsEventNavi.vars.vars.idTarget == obj.json.data.vars.idTarget) {
				this._eventNaviDetail({vars : this.insDetail.varsEventNavi.vars});
			}

		} else if (obj.json.flag == 8) {
			alert(this.insRoot.vars.varsSystem.str.oldData);

		} else if (obj.json.flag == 40) {
			alert(this.insRoot.vars.varsSystem.str.oldData);
			this.insLayout.updateTool({flagLock : 0, from : 'detail', idTarget : 'Reload'});
			this._varsSearch.numLotNow = obj.json.data.numLotNow;
			this.insNavi.updateTreePageVars({vars : obj.json.data});
			this._resetDetail();

		} else if (obj.json.flag == 41) {
			this._varsSearch.numLotNow = obj.json.data.numLotNow;
			this.insNavi.updateTreePageVars({vars : obj.json.data});
			this._resetDetail();

		} else if (obj.json.flag == 42) {
			alert(this.insRoot.vars.varsSystem.str.errorMail);

		} else if (obj.json.flag == 'lost') {
			this.insLayout.updateTool({flagLock : 0, from : 'detail', idTarget : 'Reload'});
			this._varsSearch.numLotNow = obj.json.data.numLotNow;
			this.insNavi.updateTreePageVars({vars : obj.json.data});
			this._resetDetail();

		} else {
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