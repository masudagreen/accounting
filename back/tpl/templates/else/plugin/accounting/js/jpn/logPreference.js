{literal}
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_LogPreference = Class.create(Code_Lib_ExtPreference,
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

	_varsAutoSearchOver : {},
	_flagAutoSearchOver : '',
	bootAutoSearchOver : function(obj)
	{
		var insEscape = new Code_Lib_Escape();

		this._flagAutoSearchOver = obj.flag;
		this._varsAutoSearchOver = obj.vars;

		if (obj.flag == 'showLogImportRetry'
			|| obj.flag == 'showLogImport'
		) {
			obj.flag.match(/^show(.*?)$/);
			var idTarget = RegExp.$1;
			var varsData = this.checkChildData({idTarget : idTarget});
			if (!varsData) {
				this._varsBlock = null;
				var idTargetWindow = insEscape.strLowCapitalize({data : idTarget}) + 'Window';
				this._getBlock({arr : this.vars.portal.varsNavi.tree.varsDetail.varsDetail, idTarget : idTargetWindow});
				if (this._varsBlock) {
					if (this._varsBlock.vars.idTarget.match(/(.*?)Window$/)) {
						var insEscape = new Code_Lib_Escape();
						var strExt = insEscape.strCapitalize({data : RegExp.$1});
						this._iniChild({
							strTitleParent : this.insWindow.vars.strTitle,
							strTitleChild  : this._varsBlock.strTitle,
							strExt         : strExt,
							strChild       : '',
							strClass       : this.strClass,
							idModule       : this.idModule,
							insBack        : {},
							strBackFunc    : ''
						});
					}
				}

			} else {
				if (varsData.insWindow.vars.flagHideNow) {
					varsData.insWindow.updateHide({ flagEffect : 1 });
				} else {
					varsData.insWindow.setZIndex();
				}
				varsData.insClass.bootAutoSearchOver({flag : 'showLog'});
			}

		} else if (obj.flag == 'showLogHouse') {
			obj.flag.match(/^show(.*?)$/);
			var idTarget = RegExp.$1;
			var varsData = this.checkChildData({idTarget : idTarget});
			if (!varsData) {
				this._varsBlock = null;
				var idTargetWindow = insEscape.strLowCapitalize({data : idTarget}) + 'Window';
				this._getBlock({arr : this.vars.portal.varsNavi.tree.varsDetail.varsDetail, idTarget : idTargetWindow});
				if (this._varsBlock) {
					if (this._varsBlock.vars.idTarget.match(/(.*?)Window$/)) {
						var insEscape = new Code_Lib_Escape();
						var strExt = insEscape.strCapitalize({data : RegExp.$1});
						this._iniChild({
							strTitleParent : this.insWindow.vars.strTitle,
							strTitleChild  : this._varsBlock.strTitle,
							strExt         : strExt,
							strChild       : '',
							strClass       : this.strClass,
							idModule       : this.idModule,
							insBack        : this,
							strBackFunc    : 'eventAutoSearchOver'
						});
					}
				}

			} else {
				if (varsData.insWindow.vars.flagHideNow) {
					varsData.insWindow.updateHide({ flagEffect : 1 });
				} else {
					varsData.insWindow.setZIndex();
				}
				this.eventAutoSearchOver();
			}

		} else if (obj.flag == 'addLogImport' || obj.flag == 'addLogImportDetail') {
			var idTarget = 'LogImport';
			var varsData = this.checkChildData({idTarget : idTarget});
			if (!varsData) {
				this._varsBlock = null;
				var idTargetWindow = insEscape.strLowCapitalize({data : idTarget}) + 'Window';
				this._getBlock({arr : this.vars.portal.varsNavi.tree.varsDetail.varsDetail, idTarget : idTargetWindow});
				if (this._varsBlock) {
					if (this._varsBlock.vars.idTarget.match(/(.*?)Window$/)) {
						var insEscape = new Code_Lib_Escape();
						var strExt = insEscape.strCapitalize({data : RegExp.$1});
						this._iniChild({
							strTitleParent : this.insWindow.vars.strTitle,
							strTitleChild  : this._varsBlock.strTitle,
							strExt         : strExt,
							strChild       : '',
							strClass       : this.strClass,
							idModule       : this.idModule,
							flagHideWindow : (obj.flag == 'addLogImport')? 0 : 1,
							insBack        : this,
							strBackFunc    : 'eventAutoSearchOver'
						});
					}
				}

			} else {
				this.eventAutoSearchOver();
			}

		} else if (obj.flag == 'showRetryBtn' || obj.flag == 'loopFilter') {
			this.eventAutoSearchOver();
		}
	},

	eventAutoSearchOver : function()
	{
		if (this._flagAutoSearchOver == 'addLogImport') {
			this._flagAutoSearchOver.match(/^add(.*?)$/);
			var idTarget = RegExp.$1;
			var varsData = this.checkChildData({idTarget : idTarget});
			if (varsData) {
				varsData.insClass.bootAutoSearchOver({flag : 'addLog'});
			}

		} else if (this._flagAutoSearchOver == 'showLogHouse') {
			var idTarget = 'LogHouse';
			var varsData = this.checkChildData({idTarget : idTarget});
			if (varsData) {
				varsData.insClass.bootAutoSearchOver({
					flag : this._flagAutoSearchOver,
					vars : this._varsAutoSearchOver.vars
				});
			}

		} else if (this._flagAutoSearchOver == 'addLogImportDetail') {
			var idTarget = 'LogImport';
			var varsData = this.checkChildData({idTarget : idTarget});
			if (varsData) {
				varsData.insClass.bootAutoSearchOver({
					flag : 'addLogDetail',
					vars : this._varsAutoSearchOver
				});
			}

		} else if (this._flagAutoSearchOver == 'showRetryBtn' || this._flagAutoSearchOver == 'loopFilter') {
			var idTarget = 'LogImportRetry';
			var varsData = this.checkChildData({idTarget : idTarget});
			if (varsData) {
				varsData.insClass.bootAutoSearchOver({
					flag : this._flagAutoSearchOver
				});
			}

		}
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
	_varsDetailEnd : null,
	_setDetailEnd : function()
	{
		this._varsDetailEnd = (Object.toJSON(this.insDetail.varsEventNavi)).evalJSON();
		var objData = {
			strTitle : null,
			strClass : null,
			vars     : {
				varsDetail : this.vars.portal.varsDetail.varsEnd.varsDetail,
				varsBtn    : this.vars.portal.varsDetail.varsEnd.varsBtn,
				varsEdit   : {},
				vars       : {}
			}
		};
		this.insDetail.eventNavi(objData);
	},

	/**
	 *
	*/
	_setDetailReset : function()
	{
		var objData = {
			strTitle : null,
			strClass : null,
			vars     : {
				varsDetail : this.vars.portal.varsDetail.varsEnd.varsDetail,
				varsBtn    : [],
				varsEdit   : {},
				vars       : {}
			}
		};
		this.insDetail.eventNavi(objData);
	},

	/**
	 *
	*/
	_backDetailEnd : function()
	{
		this._setNaviDetail({vars : this._varsDetailEnd.vars});
		this._varsDetailEnd = null;
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
		}
		else this._setNaviDetail({vars : obj.vars});
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
		if (obj.idTarget == 'jsonFileType'
			|| obj.idTarget == 'jsonMail'
		) {
			this._iniDetailFormList();
		}
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
	_iniDetailFormArea : function()
	{
		this._extDetailFormArea();
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
		if (!this.insDetail.varsEventNavi) return;
		var idTarget = this.insDetail.varsEventNavi.vars.vars.idTarget;
		if (idTarget == 'jsonFileType'
			|| idTarget == 'jsonMail'
		) {
			this._iniDetailFormList();

		}

	},

	/**
	 *
	*/
	_preEventLayoutDetailContent : function()
	{
		if (!this.insDetail.varsEventNavi) return;
		var idTarget = this.insDetail.varsEventNavi.vars.vars.idTarget;
		if (idTarget == 'jsonFileType'
			|| idTarget == 'jsonMail'
		) {
			this._getDetailFormListVars({arr : this.insDetail.insForm.vars.varsDetail});

		}
	},

	/**
	 *
	*/
	_eventRemoveDetailContent : function()
	{
		if (!this.insDetail.varsEventNavi) return;
		if (!this.insDetail.varsEventNavi.vars.vars) return;
		var idTarget = this.insDetail.varsEventNavi.vars.vars.idTarget;
		if (idTarget == 'jsonFileType'
			|| idTarget == 'jsonMail'
		) {
			this._eventRemoveDetailFormList({arr : this.insDetail.insForm.vars.varsDetail});

		}
	},


	/**
	 *
	*/
	_numVersionTry : 0,
	_eventDetailConnect : function(obj)
	{
		this._numVersionTry = 0;
		if (obj.flag == 'reload') {
			this._eventValue({
				vars     : '',
				idTarget : obj.idTarget
			});

		} else if (obj.flag == 'edit') {
			if (obj.idTarget == 'arrCommaIdAccountMaintenance') {
				this._setDetailFormAreaValue({arr : this.insDetail.insForm.vars.varsDetail});

			} else if (obj.idTarget == 'jsonFileType'
				|| obj.idTarget == 'jsonMail'
			) {
				this._setDetailFormListValue({arr : this.insDetail.insForm.vars.varsDetail});

			} else if (obj.idTarget == 'local') {
				this._iniLocal();
				return;
			}

			if (obj.idTarget != 'strReset') {
				if (this.insDetail.checkForm({flagType : 'common'})) return;
			}

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
	_varsBlock : {},
	_getBlock : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].vars.idTarget == obj.idTarget) {
				this._varsBlock = (Object.toJSON(obj.arr[i])).evalJSON();
			}
			else this._getBlock({arr : obj.arr[i].child, idTarget : obj.idTarget});
		}
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
					if (this.insDetail.varsEventNavi.vars.vars.idTarget == obj.json.data.idTarget) {
						this.insDetail.showFormAttestError({flagType : obj.json.flag});
						return;
					}
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