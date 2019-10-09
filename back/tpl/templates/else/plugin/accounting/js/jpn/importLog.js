{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_ImportLog = Class.create(Code_Lib_ExtPortal,
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
		this._updateVarsNaviForm({arr : this.vars.portal.varsNavi.templateDetail});
	},

	/**
	 *
	*/
	_varsIdUpload : null,
	_updateVarsNaviForm : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'Upload') {

				var strChild = 'Editor';
				var strFunc = 'NaviAdd';
				this._varsIdUpload = this.idSelf + this.strExt + strChild;
				obj.arr[i].arrayHidden = [
					{id : 'class',      value : this.strClass},
					{id : 'module',     value : this.idModule},
					{id : 'ext',        value : this.strExt},
					{id : 'child',      value : strChild},
					{id : 'func',       value : strFunc},
					{id : 'db',         value : 'master'},
					{id : 'idUpload',   value : this._varsIdUpload},
					{id : 'idTag',      value : obj.arr[i].id},
					{id : 'cache',      value : (new Date()).getTime()},
					{id : 'token',      value : (this.insRoot.vars.varsSystem.token)? this.insRoot.vars.varsSystem.token : ''},
				];
				obj.arr[i].value = 'dummy';
			}
		}
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
		if (obj.flag.match(/-reload$/)) {
			this._setNaviStart();
			return;

		} else if (obj.flag == 'search') {
			if (this.insNavi.checkForm({flagType : 'common'})) return;
			this._setDetailFormCheckValue({arr : this.insNavi.insForm.vars.varsDetail});
			var vars = this.insNavi.getFormValue();

			var varsDetail = this._getVarsDetail({
				arr      : this.insNavi.insForm.vars.varsDetail,
				idTarget : 'Upload'
			});

			var array = vars.Upload.split('/');
			array = array[array.length - 1].split('.');
			var strFileType = array[array.length - 1];

			if (vars.Upload == '') {
				this.insNavi.showFormAttestError({flagType : 'strBlank'});
				return;
			}

			if (strFileType != 'csv') {
				this.insNavi.showFormAttestError({flagType : 'strFileType'});
				return;
			}

			this._varsNaviConnect = obj;
			this._setDetailContent();
			this.insRoot.setUpload({
				id         : this._varsIdUpload,
				insClass   : this,
				strFunc    : 'eventDetailUpload',
				eleLoading : this.insNavi.insUnder.eleFormat.header.down('.codeLibBaseMarginRightFive', 0)
			});
			this.insNavi.insForm.eleForm.submit();

			return;
		}
	},

	/**
	 *
	*/
	_getVarsDetail : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == obj.idTarget) {
				return obj.arr[i];
			}
		}
		return {};
	},

	/**
	 *
	*/
	eventDetailUpload : function(obj)
	{
		if (obj.vars) {
			if (obj.vars.stamp) {
				if (obj.vars.stamp.id) this._varsStamp[obj.vars.stamp.id] = obj.vars.stamp.stamp;
			}
			if (obj.vars.flag) {
				if (obj.vars.numNews) this.insRoot.iniPopup({flag : 'news', numNews : obj.vars.numNews});
				this._eventNaviConnectSuccess({json : obj.vars});
			}
			else if (obj.vars.flag == 0) alert(this.insRoot.vars.varsSystem.str.errorSession);
		}
		else alert(this.insRoot.vars.varsSystem.str.errorRequest);

	},

	/**
	 *
	*/
	_varsNaviConnect : null,

	/**
	 *
	*/
	_eventNaviConnectSuccess : function(obj)
	{
		if (obj.json.flag == 1) {
			if (this._varsNaviConnect.flag == 'search') {
				this.insDetail.insUnder.eleFormat.body.innerHTML = this.vars.varsItem.varsComment.strEnd;
				this._setNaviStart();
			}

		} else if (obj.json.flag == 40) {
			alert(this.insRoot.vars.varsSystem.str.oldData);
			if (!this.insWindow.vars.flagHideNow) this.insWindow.updateHide({ flagEffect : 1 });

		} else if (obj.json.flag == 10) {
			this.insNavi.showBtnBottom();

		} else if (obj.json.flag == 8) {
			alert(this.insRoot.vars.varsSystem.str.oldData);

		} else {
			var str = (obj.json.data)? obj.json.data : '';
			this.insNavi.showFormAttestError({flagType : obj.json.flag, str : str});

		}
	},

	/**
	 *
	*/
	_iniDetail : function()
	{
		this._extDetail();
		this._setDetailContent();
	},

	/**
	 *
	*/
	_setDetailContent : function()
	{
		this._iniDetailSheet();
	},

	_iniDetailSheet : function()
	{
		this.insDetail.insUnder.eleFormat.body.innerHTML = this.vars.varsItem.varsComment.strStart;
	},


	/**
	 *
	*/
	_setDetailContentValue : function()
	{
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
		if (obj.flag == 'reload') {
			this._setDetailContent();
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