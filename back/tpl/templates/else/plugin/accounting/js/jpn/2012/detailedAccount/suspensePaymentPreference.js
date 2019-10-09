{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_DetailedAccountSuspensePaymentPreference = Class.create(Code_Lib_ExtPreference,
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
		this._iniPopup();
		this._iniLayout();
		this._iniNavi();
		this._iniDetail();
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
	_iniPopup : function()
	{
		this._extPopup();
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
				insCurrent.insNavi.eventLayout();
				insCurrent.insDetail.eventLayout();

			} else if (obj.from == 'preEventLayout') {
				insCurrent._preEventLayout({flag : 'dummy'});

			} else if (obj.from == 'navi-_mousedownNavi') {
				if (obj.vars.id == 'Reload') {
					insCurrent._preEventLayout({flag : 'reset'});
					insCurrent.insLayout.updateTool({flagLock : 1, from : 'navi', idTarget : obj.vars.id});
					insCurrent._sendNaviConnect();
				}

			} else if (obj.from == 'detail-_mousedownNavi') {
				if (obj.vars.id == 'Reload') {
					insCurrent._preEventLayout({flag : 'reset'});
					insCurrent.insLayout.updateTool({flagLock : 1, from : 'detail', idTarget : obj.vars.id});
					insCurrent._eventDetailConnect({flag : 'reload', idTarget : insCurrent.insDetail.varsEventNavi.vars.vars.idTarget});

				} else if (obj.vars.id == 'Output') {
					insCurrent._eventDetailConnect({flag : 'output', idTarget : insCurrent.insDetail.varsEventNavi.vars.vars.idTarget});
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
	_getNaviAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == 'tree-_mousedownBtn') insCurrent._eventNaviDetail({vars : obj.vars});
		};

		return allot;
	},

	/**
	 *
	*/
	_eventNaviDetail : function(obj)
	{
		if (obj.vars.vars.idTarget.match(/(.*?)Window$/)) {
			var insEscape = new Code_Lib_Escape();
			var strExt = insEscape.strCapitalize({data : RegExp.$1});
			this._iniChild({
				strTitleParent : this.insWindow.vars.strTitle,
				strTitleChild  : obj.vars.strTitle,
				strExt         : strExt,
				strChild       : '',
				strClass       : this.strClass,
				idModule       : this.idModule
			});

		} else {
			this._updateNaviDetailVars({vars : obj.vars});
			this._setNaviDetail({vars : obj.vars});
		}
	},

	/**
	 *
	*/
	_updateNaviDetailVars : function(obj)
	{


	},

	/**
	 *
	*/
	_setNaviDetail : function(obj)
	{
		var objDetail = obj.vars.vars.varsDetail;

		this.insDetail.eventNavi({
			strTitle : obj.vars.strTitle,
			strClass : obj.vars.strClass,
			vars     : {
				varsDetail : objDetail,
				varsEdit   : obj.vars.vars.varsEdit,
				varsBtn    : obj.vars.vars.varsBtn,
				vars       : obj.vars
			}
		});
		this._setDetailContent({idTarget : obj.vars.vars.idTarget});
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
			else if (obj.from == 'form-preEventLayout') insCurrent._preEventLayoutDetailContent();
			else if (obj.from == 'form-eventLayout') insCurrent._eventLayoutDetailContent();
			else if (obj.from == 'form-eventBtnBottom') {
				if (obj.vars.vars.vars.flagBack) {
					insCurrent._backDetailEnd();

				} else if (obj.vars.vars.vars.flagHide) {
					if (!insCurrent.insWindow.vars.flagHideNow) insCurrent.insWindow.updateHide({ flagEffect : 1 });

				} else {
					insCurrent._eventDetailConnect({
						flag     : 'edit',
						idTarget : obj.vars.vars.vars.idTarget,
						id       : obj.vars.vars.id
					});

				}
			}
		};

		return allot;
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
	_preEventLayoutDetailContent : function()
	{

	},

	/**
	 *
	*/
	_eventRemoveDetailContent : function()
	{

	},


	/**
	 *
	*/
	_eventDetailConnect : function(obj)
	{
		if (obj.flag == 'reload' || obj.flag == 'output') {
			this._eventValue({
				vars     : '',
				idTarget : obj.idTarget
			});

		} else if (obj.flag == 'edit') {
			if (this.insDetail.checkForm({flagType : 'common'})) return;
			var vars = this.insDetail.getFormValue();

			this._eventValue({
				vars     : vars,
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
			if (this._varsDetailConnect.flag == 'reload') {
				if (this.insDetail.varsEventNavi.vars.vars.idTarget == obj.json.data.vars.idTarget) {
					this._setNaviDetail({vars : obj.json.data});
				}

			} else if (this._varsDetailConnect.flag == 'edit') {
				if (this.insDetail.varsEventNavi.vars.vars.idTarget == obj.json.data.vars.idTarget) {
					this._setNaviDetail({vars : obj.json.data});
					this._setDetailEnd();
				}
			}
			if (obj.json.stamp) {
				var data = (Object.toJSON(obj.json.data)).evalJSON();
				if (obj.json.stamp.id) this._varsStampCheck[obj.json.stamp.id] = data;
			}
			this.insNavi.updateTreeVarsDetail({vars : obj.json.data});

		} else if (obj.json.flag == 4) {
			alert(this.insRoot.vars.varsSystem.str.maintenance);

		} else if (obj.json.flag == 8) {
			alert(this.insRoot.vars.varsSystem.str.oldData);

		} else if (obj.json.flag == 10) {
			this.insLayout.updateTool({flagLock : 0, from : 'detail', idTarget : 'Reload'});
			if (this.insDetail.varsEventNavi.vars.vars.idTarget == this._varsValue.idTarget) {
				if (obj.json.stamp) {
					this._setNaviDetail({vars : this._varsStampCheck[obj.json.stamp.id]});
				}
			}

		} else if (obj.json.flag == 40) {
			this.insLayout.updateTool({flagLock : 0, from : 'detail', idTarget : 'Reload'});
			if (this.insDetail.varsEventNavi.vars.vars.idTarget == this._varsValue.idTarget) {
				alert(this.insRoot.vars.varsSystem.str.oldData);
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
	_iniChild : function(obj)
	{
		this._extChild(obj);
	}
});
{/literal}
