{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_BalanceSubEditor = Class.create(Code_Lib_ExtEditor,
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
	_varsList : [],
	_varsDetailConfig : null,
	_iniVars : function(obj)
	{
		this._extVars(obj);
		this.vars.portal.varsDetail.varsDetail = this.varsChild.varsDetail;
		this._varsSearch = this.insCurrent.getVarsSearch();
		this._setVarsKeyNum({arr : this.vars.portal.varsDetail.varsDetail});
		this.varsList = (Object.toJSON(this.varsChild.varsList)).evalJSON();
		this.varsIni = this.varsChild.varsIni;
		this._setVarsData({
			arr     : this.varsChild.varsDetail,
			arrTree : this.varsList
		});
		this.vars.portal.varsDetail.varsDetail = this.varsChild.varsDetail;

		this._setVarsSensitiveKeyNum({arr : this.vars.portal.varsDetail.varsDetail});
		this._varsDetailConfig = (Object.toJSON(this.vars.portal.varsDetail.varsDetail)).evalJSON();
		this._setVarsConfigDetail({arr : this._varsDetailConfig});
	},

	/**
	 *
	*/
	_flagSecondLoad : 0,
	_setVarsData : function(obj)
	{
		var num = this._varsKeyNum.JsonData;
		var vars = obj.arr[num];

		var tmplItem = vars.varsFormSensitive.varsTmpl.tmplTableItem;
		this._strHtml = '';
		this._setStrHtmlLoop({
			arr      : obj.arrTree,
			tmplItem : tmplItem
		});

		var tmplDetail = vars.varsFormSensitive.varsTmpl.tmplDetail;
		this._varsData = [];
		this._setVarsDataLoop({
			flagUpdate : 0,
			arr        : obj.arrTree,
			tmplDetail : tmplDetail
		});

		var strTable = vars.varsFormSensitive.varsTmpl.tmplTable;
		var data = strTable.interpolate({
			idSelf  : this.idSelf,
			strForm : this._strHtml
		});

		obj.arr[num].varsFormSensitive.varsHtml = data;
		obj.arr[num].varsFormSensitive.varsDetail = this._varsData;
	},

	/**
	 *
	*/
	_strHtml : '',
	_setStrHtmlLoop : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var str = obj.tmplItem;

			var strClass = '';
			var strTitle = obj.arr[i].strTitle;
			var data = str.interpolate({
				idSelf           : this.idSelf,
				id               : obj.arr[i].vars.idTarget,
				strTitle         : strTitle,
				numValue         : '',
				strClassStrTitle : strClass,
				strClassNumValue : strClass
			});
			this._strHtml += data;
		}
	},

	/**
	 *
	*/
	_varsData : [],
	_setVarsDataLoop : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var vars = (Object.toJSON(obj.tmplDetail)).evalJSON();
			vars.id = obj.arr[i].vars.idTarget;
			vars.value = obj.arr[i].varsValue.numBalance;
			if (obj.flagUpdate) {
				this._varsSensitive.ins.updateVarsTarget({
					idTarget : vars.id,
					strKey   : 'value',
					vars     : vars.value
				});
			}
			this._varsData.push(vars);
		}
	},

	/**
	 *
	*/
	_setVarsConfigDetail : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'JsonData') {
				var arr = obj.arr[i].varsFormSensitive.varsDetail;
				for (var j = 0; j < arr.length; j++) {
					arr[j].value = '';
				}

			} else {
				obj.arr[i].value = '';
			}
		}
	},

	/**
	 *
	*/
	_varsKeyNum : {},
	_setVarsKeyNum : function(obj)
	{
		this._varsKeyNum = {};
		for (var i = 0; i < obj.arr.length; i++) {
			this._varsKeyNum[obj.arr[i].id] = i;
		}
	},

	/**
	 *
	*/
	_varsSensitiveKeyNum : {},
	_setVarsSensitiveKeyNum : function(obj)
	{
		var num = this._varsKeyNum.JsonData;
		var arr = obj.arr[num].varsFormSensitive.varsDetail;
		this._varsSensitiveKeyNum = {};
		for (var i = 0; i < arr.length; i++) {
			this._varsSensitiveKeyNum[arr[i].id] = i;
		}
	},

	/**
	 *
	*/
	_getVarsDetailConfigSensitive : function(obj)
	{
		var num = this._varsKeyNum.JsonData;
		var numDetail = this._varsSensitiveKeyNum[obj.idTarget];

		return this._varsDetailConfig[num].varsFormSensitive.varsDetail[numDetail];
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
	eventWindowAppear : function(obj)
	{
		this.varsChild = (Object.toJSON(obj.vars)).evalJSON();
		this.vars.portal.varsDetail.varsDetail = this.varsChild.varsDetail;

		this._varsSearch = this.insCurrent.getVarsSearch();
		this.varsIni = this.varsChild.varsIni;
		this.varsList = (Object.toJSON(this.varsChild.varsList)).evalJSON();
		this._setVarsData({
			arr     : this.varsChild.varsDetail,
			arrTree : this.varsList
		});
		this.vars.portal.varsDetail.varsDetail = this.varsChild.varsDetail;

		this._setVarsSensitiveKeyNum({arr : this.vars.portal.varsDetail.varsDetail});
		this._varsDetailConfig = (Object.toJSON(this.vars.portal.varsDetail.varsDetail)).evalJSON();
		this._setVarsConfigDetail({arr : this._varsDetailConfig});

		this._setDetailStart();
	},

	/**
	 *
	*/
	_setDetailStart : function()
	{
		var str = 'strTitle' + this.varsChild.flagType.capitalize();
		this.insDetail.eventList({
			flagMoveUse : 1,
			strTitle    : this.vars.portal.varsDetail.varsStart[str],
			strClass    : null,
			vars        : {
				varsDetail : this.vars.portal.varsDetail.varsDetail,
				varsBtn    : this.vars.portal.varsDetail.varsBtn,
				varsEdit   : this.vars.portal.varsDetail.form.varsEdit,
				vars       : {}
			}
		});
		this._setDetailContent();
	},

	/**
	 *
	*/
	_varsContent : {num : 0},
	_setDetailContent : function()
	{
		this._varsContent.num = 0;
		this._iniDetailFormSensitive();
	},

	/**
	 *
	*/
	_varsSensitive : {},
	_iniDetailFormSensitive : function()
	{
		var num = this._varsKeyNum.JsonData;
		if (!this.insDetail.insForm.vars.varsDetail[num]) {
			return;
		}
		if (!this.insDetail.insForm.vars.varsDetail[num].varsFormSensitive) {
			return;
		}
		this._varsSensitive = {};
		this._setDetailFormSensitive({arr : this.insDetail.insForm.vars.varsDetail});
		this._setDetailFormSensitiveView({arr : this.insDetail.insForm.vars.varsDetail});
		this._getDetailFormSensitiveVars({arr : this.insDetail.insForm.vars.varsDetail});
	},

	/**
	 *
	*/
	_setDetailFormSensitive : function(obj)
	{
		var num = this._varsKeyNum.JsonData;
		var ele = this.insDetail.insForm.eleWrap.down('.codeLibFormContent', this._varsContent.num);
		var str = obj.arr[num].varsFormSensitive.varsHtml;
		var data = str.interpolate({
			'idSelf'  : this.idSelf
		});
		ele.insert(data);
		obj.arr[num].varsFormSensitive.varsStatus.id = this.idSelf;
		this._varsContent.num++;

		var insFormSensitive = new Code_Lib_FormSensitive({
			eleInsert  : ele,
			insRoot    : this.insRoot,
			insCurrent : this,
			idSelf     : this.idSelf + 'FormSensitive' + obj.arr[num].id,
			allot      : this._getDetailFormSensitiveAllot(),
			vars       : obj.arr[num].varsFormSensitive
		});

		this._varsSensitive = {
			id  : obj.arr[num].id,
			ins : insFormSensitive
		};
	},

	/**
	 *
	*/
	_getDetailFormSensitiveAllot : function()
	{
		var allot = function(obj) {
			var insCurrent = obj.insCurrent;
			if (obj.from == '_getEditVars') {
				var dataScroll = insCurrent.insDetail.getFormScrollVars();
				var ele = insCurrent._varsSensitive.ins.eleInsert;
				var numPadding = 5;
				var data = {
					numTop    : -1 * dataScroll.numTop + ele.offsetTop + numPadding,
					numLeft   : -1 * dataScroll.numLeft + ele.offsetLeft + numPadding,
					numWidth  : -1 * 2 * numPadding,
					numHeight : -1 * 2 * numPadding
				};

				return data;

			} else if (obj.from == 'removeWrap') {
				insCurrent.insDetail.resetValueError();
				insCurrent._updateVarsDetailSensitive(obj);
			}
		};

		return allot;
	},

	/**
	 *
	*/
	_setDetailFormSensitiveView : function(obj)
	{
		var num = this._varsKeyNum.JsonData;
		var arr = obj.arr[num].varsFormSensitive.varsDetail;
		for (var i = 0; i < arr.length; i++) {
			this._updateDetailFormSensitiveView({
				idTarget  : arr[i].id
			});
		}
	},

	/**
	 *
	*/
	_updateDetailFormSensitiveView : function(obj)
	{
		var vars = this._varsSensitive.ins.getVarsTarget({idTarget : obj.idTarget});
		var ele = $(this._varsSensitive.ins.vars.varsStatus.id + vars.id);

		var insDisplayComma = new Code_Lib_DisplayComma();

		if (vars.value === 0) {
			ele.innerHTML = 0;

		} else if (vars.value == '') {
			ele.innerHTML = '';

		} else {
			var num = insDisplayComma.get({
				num : vars.value
			});
			ele.innerHTML = num;
		}

	},

	/**
	 *
	*/
	_getDetailFormSensitiveVars : function(obj)
	{
		var num = this._varsKeyNum.JsonData;
		if (!obj.arr[num]) {
			return;
		}
		if (!obj.arr[num].varsFormSensitive) {
			return;
		}
		obj.arr[num].varsFormSensitive.varsDetail = this._varsSensitive.ins.vars.varsDetail;
	},

	/**
	 *
	*/
	_checkNumValue : function(obj)
	{
		var insEscape = new Code_Lib_Escape();
		var insCheck = new Code_Lib_CheckValue();

		var strValue = obj.value + '';

		if (strValue === '') {
			return '';
		}

		if (strValue.match(/,/)) {
			var arr = strValue.split(',');
			strValue = arr.join('');
		}

		strValue = insEscape.get({
			flagType : 'strToNum',
			data     : strValue
		});

		var flag = insCheck.checkValueWord({
			flagType : 'numminus',
			value    : strValue
		});

		if (flag) return '';

		return parseFloat(strValue);
	},


	/**
	 *
	*/
	_updateVarsDetailSensitive : function(obj)
	{
		obj.vars.value = this._checkNumValue({
			value : obj.vars.value
		});
		if (obj.vars.value === '') {
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
			return;
		}

		this._updateDetailFormSensitiveView({
			idTarget  : obj.vars.id
		});

		this._updateVarsTreeValue({
			idTarget : obj.vars.id,
			arr      : this.varsList,
			value    : obj.vars.value
		});

		var varsValue = {};

		this._setVarsTreeValue({
			arr       : this.varsList,
			varsValue : varsValue
		});

		var num = this._varsKeyNum.JsonData;
		var vars = this.insDetail.insForm.vars.varsDetail[num];
		var tmplDetail = vars.varsFormSensitive.varsTmpl.tmplDetail;

		this._varsData = [];
		this._setVarsDataLoop({
			flagUpdate : 1,
			arr        : this.varsList,
			tmplDetail : tmplDetail
		});

		this._getDetailFormSensitiveVars({arr : this.insDetail.insForm.vars.varsDetail});
		this._setDetailFormSensitiveView({arr : this.insDetail.insForm.vars.varsDetail});
	},

	/**
	 *
	*/
	_updateVarsTreeValue : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].vars.idTarget == obj.idTarget) {
				obj.arr[i].varsValue.numBalance = parseFloat(obj.value);
				return 1;

			} else {
				var flag = this._updateVarsTreeValue({
					arr      : obj.arr[i].child,
					idTarget : obj.idTarget,
					value    : obj.value
				});
				if (flag) {
					return;
				}
			}
		}
	},

	/**
	 *
	*/
	_setVarsTreeValue : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var num = obj.varsValue[obj.arr[i].vars.idTarget];
			if (num != undefined) {
				obj.arr[i].varsValue.numBalance = parseFloat(num);

			} else {
				this._setVarsTreeValue({
					arr       : obj.arr[i].child,
					varsValue : obj.varsValue
				});
			}
		}
	},

	/**
	 *
	*/
	_setDetailEnd : function()
	{
		this._varsDetailEnd = (Object.toJSON(this.insDetail.varsEventList)).evalJSON();
		this._varsDetailEnd.varsDetail = (Object.toJSON(this.insDetail.insForm.vars.varsDetail)).evalJSON();
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
		this.insDetail.eventList(objData);
	},

	/**
	 *
	*/
	_getDetailFormContentVars : function(obj)
	{

	},

	/**
	 *
	*/
	_eventRemoveDetailFormSensitive : function(obj)
	{
		this._varsSensitive.ins.stopListener();
	},

	/**
	 *
	*/
	_eventLayoutDetailContent : function()
	{
		this._setDetailContent();
	},

	/**
	 *
	*/
	_preEventLayoutDetailContent : function()
	{
		if (!this.insDetail.insForm) return;
		this._getDetailFormSensitiveVars({arr : this.insDetail.insForm.vars.varsDetail});
	},

	/**
	 *
	*/
	_eventRemoveDetailContent : function()
	{
		if (!this.insDetail.insForm) return;
		this._eventRemoveDetailFormSensitive({arr : this.insDetail.insForm.vars.varsDetail});
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
			else if (obj.from == '_mousedownMove') {
				insCurrent.insDetail.setValue();
				var vars = insCurrent._getDetailFormFormat();
				vars.StrTitle = '';
				vars.FlagFS = insCurrent.insCurrent.vars.varsFlag.flagFS;

				insCurrent.insNavi.eventMove({vars : vars});
			}
			else if (obj.from == 'form-preEventLayout') insCurrent._preEventLayoutDetailContent();
			else if (obj.from == 'form-eventLayout') insCurrent._eventLayoutDetailContent();
			else if (obj.from == 'form-eventBtnBottom') {
				if (obj.vars.vars.vars.flagBack) {
					insCurrent._backDetailEnd();

				} else if (obj.vars.vars.vars.flagHide) {
					if (!insCurrent.insWindow.vars.flagHideNow) insCurrent.insWindow.updateHide({ flagEffect : 1 });

				} else {
					if (obj.vars.vars.vars.idTarget == 'save') {
						insCurrent._eventDetailConnect({flag : insCurrent.varsChild.flagType});

					} else if (obj.vars.vars.vars.idTarget == 'calc') {
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
	_getDetailFormFormat : function()
	{
		var vars = this.insDetail.getFormValue();
		arr = this.insDetail.insForm.vars.varsDetail;
		for (var i = 0; i < arr.length; i++) {
			if (arr[i].id == 'JsonData') {
				vars[arr[i].id] = this._getVarsSensitiveValue();
			}
		}

		return vars;
	},

	/**
	 *
	*/
	_getVarsSensitiveValue : function()
	{
		var arr = this._varsSensitive.ins.vars.varsDetail;
		var data = {};
		for (var i = 0; i < arr.length; i++) {
			data[arr[i].id] = arr[i].value;
		}

		return data;
	},

	/**
	 *
	*/
	_eventDetailConnect : function(obj)
	{
		var insEscape = new Code_Lib_Escape();

		if (obj.flag == 'reload') {
			this.vars.portal.varsDetail.varsDetail = this.varsChild.varsDetail;
			if (obj.flagType == 'start') {
				this._setVarsData({
					arr     : this.varsChild.varsDetail,
					arrTree : this.varsIni
				});

			} else {
				this.varsList = (Object.toJSON(this.varsChild.varsList)).evalJSON();
				this._setVarsData({
					arr     : this.varsChild.varsDetail,
					arrTree : this.varsList
				});
			}
			this.vars.portal.varsDetail.varsDetail = this.varsChild.varsDetail;
			this._setDetailStart();
			return;

		} else if (obj.flag == 'add'
			|| obj.flag == 'edit'
		) {
			if (this.insDetail.checkForm({flagType : 'common'})) return;
			var vars = this.insDetail.getFormValue();
			vars.JsonData = this._getVarsSensitiveValue();
			vars.VarsFlag = this.insCurrent.vars.varsFlag;

			this._eventValue({
				vars     : vars,
				idTarget : (obj.flag == 'edit')? this.varsChild.idTarget : ''
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
			if (this._varsDetailConnect.flag == 'add') {
				this.insCurrent.eventDetailConnectSuccessListUpdate(obj);
				this._setDetailEnd();

			} else if (this._varsDetailConnect.flag.match(/^edit/)) {
				this.insCurrent.evenDetailtEditorSuccess(obj);
				this._setDetailEnd();
			}

		} else if (obj.json.flag == 40) {
			alert(this.insRoot.vars.varsSystem.str.oldData);
			if (!this.insWindow.vars.flagHideNow) this.insWindow.updateHide({ flagEffect : 1 });

		} else if (obj.json.flag == 42) {
			alert(this.insRoot.vars.varsSystem.str.errorMail);

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
	_iniCss : function()
	{
		this._extCss();
	}
});
{/literal}