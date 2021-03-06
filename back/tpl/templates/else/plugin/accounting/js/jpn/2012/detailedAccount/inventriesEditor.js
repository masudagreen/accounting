{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_DetailedAccountInventriesEditor = Class.create(Code_Lib_ExtEditor,
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

	_varsDetailConfig : null,
	_iniVars : function(obj)
	{
		this._extVars(obj);
		this.vars.portal.varsDetail.varsDetail = this.varsChild.varsDetail;
		this._varsSearch = this.insCurrent.getVarsSearch();
		this._setVarsKeyNum({arr : this.vars.portal.varsDetail.varsDetail});

		this._setVarsData({arr : this.varsChild.varsDetail});
		this.vars.portal.varsDetail.varsDetail = this.varsChild.varsDetail;

		this._setVarsSensitiveKeyNum({arr : this.vars.portal.varsDetail.varsDetail});
		this._varsDetailConfig = (Object.toJSON(this.vars.portal.varsDetail.varsDetail)).evalJSON();
		this._setVarsConfigDetail({arr : this._varsDetailConfig});
	},

	/**
	 *
	*/
	_tmplHtml : '',
	_varsList : [],
	_varsFlag : {},
	_varsCommon : {},
	_arrAccountTitle : {},
	_arrSubAccountTitle : {},
	_varsPreference : {},
	_setVarsData : function(obj)
	{
		this._varsFlag = (Object.toJSON(this.insCurrent.vars.varsFlag)).evalJSON();
		if (this._varsFlag.flagMenu.match(/^detail/)) {
			this._arrAccountTitle = (Object.toJSON(this.insCurrent.vars.varsItem.arrAccountTitle)).evalJSON();
			var num = 0;
			var data = [this.insCurrent.vars.varsItem.arrNoneAccountTitle];
			arrayOption = (Object.toJSON(this._arrAccountTitle.arrSubAccountSelectTag)).evalJSON();
			for (var j = 0; j < arrayOption.length; j++) {
				num++;
				data[num] = arrayOption[j];
			}
			this._arrAccountTitle.arrSubAccountSelectTag = data;
			this._arrSubAccountTitle = (Object.toJSON(this.insCurrent.vars.varsItem.arrSubAccountTitle)).evalJSON();
		}
		this._varsPreference = (Object.toJSON(this.insCurrent.vars.varsItem.varsPreference)).evalJSON();
		this._varsCommon = (Object.toJSON(this.insCurrent.vars.varsItem.varsCommon)).evalJSON();
		this._varsList = (Object.toJSON(this.insCurrent.vars.varsItem.varsList)).evalJSON();
		this._tmplHtml = (Object.toJSON(this.insCurrent.vars.portal.varsDetail.varsDetail.varsHtml)).evalJSON();

		var num = this._varsKeyNum.JsonData;
		var vars = obj.arr[num];
		obj.arr[num].varsFormSensitive.varsHtml = this._tmplHtml;
		this._varsIni = this.insCurrent.vars.varsItem.varsIni;
		obj.arr[num].varsFormSensitive.varsDetail = (obj.flagIni)? this._varsIni : this._varsList;

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
	_getVarsDetailIniSensitive : function(obj)
	{
		var num = this._varsKeyNum.JsonData;
		var numDetail = this._varsSensitiveKeyNum[obj.idTarget];

		return this.varsChild.varsDetail[num].varsFormSensitive.varsDetail[numDetail];
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

		this._setVarsKeyNum({arr : this.vars.portal.varsDetail.varsDetail});

		this._setVarsData({arr : this.varsChild.varsDetail});

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
			flagMoveUse : 0,
			strTitle    : this.vars.portal.varsDetail.varsStart[str],
			strClass    : null,
			vars        : {
				varsDetail : this.vars.portal.varsDetail.varsDetail,
				varsBtn    : this._updateVarsBtn({arr : this.vars.portal.varsDetail.varsBtn}),
				varsEdit   : this.vars.portal.varsDetail.form.varsEdit,
				vars       : {}
			}
		});
		this._setDetailContent();
	},

	/**
	 *
	*/
	_updateVarsBtn : function(obj)
	{
		var flag = parseFloat(this.insCurrent.vars.varsItem.flagBtnCalc);
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'BtnCalc') {
				if (flag) {
					obj.arr[i].flagBtnUse = 1;
				} else {
					obj.arr[i].flagBtnUse = 0;
				}
			}
		}

		return obj.arr;
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
		this._updateSensitiveStep({arr : this.insDetail.insForm.vars.varsDetail});
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
	_updateSensitiveStep : function(obj)
	{
		var flagMenu = this._varsFlag.flagMenu;
		if (!flagMenu.match(/^detail/)) {
			return;
		}

		var varsValue = {};
		var num = this._varsKeyNum.JsonData;
		var arr = obj.arr[num].varsFormSensitive.varsDetail;
		for (var i = 0; i < arr.length; i++) {
			varsValue[arr[i].id] = arr[i].value;
		}

		var numEnd = this._varsCommon.varsStr[flagMenu].numRows;
		for (var i = 1; i <= numEnd; i++) {
			var id = 'SelectIdAccountTitle' + i;
			var idSub = 'SelectIdSubAccountTitle' + i;
			var data = varsValue[id];
			if (data == 'none') {
				this._varsSensitive.ins.updateVarsTarget({
					idTarget : idSub,
					strKey   : 'flagForm',
					vars     : 'none'
				});

			} else {
				this._varsSensitive.ins.updateVarsTarget({
					idTarget : idSub,
					strKey   : 'flagForm',
					vars     : 'active'
				});
			}
		}
		this._varsSensitive.ins.resetSense();
		this._updateVarsTextSum({arr : obj.arr});
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

			} else if (obj.from == '_setEdit') {
				insCurrent.insCurrent._setDetailFormSensitiveEditVars(obj);
			}
		};

		return allot;
	},

	/**
	 *
	*/
	_setDetailFormSensitiveEditVars : function(obj)
	{
		if (obj.vars.flagTag != 'select') {
			return;
		}
		if (obj.vars.id.match(/^SelectIdAccountTitle(.*?)$/)) {
			var idNum = RegExp.$1;
			var idSubAccountTitle = 'SelectIdSubAccountTitle' + idNum;
			obj.vars.arrayOption = this._arrAccountTitle.arrSubAccountSelectTag;

			var arrayOption = this._arrSubAccountTitle.arrSelectTag[obj.vars.value];
			if (!arrayOption) {
				arrayOption = [this.insCurrent.vars.varsItem.arrNoneSub];

			} else {
				var num = 0;
				var data = [this.insCurrent.vars.varsItem.arrNoneSub];
				arrayOption = (Object.toJSON(arrayOption)).evalJSON();
				for (var j = 0; j < arrayOption.length; j++) {
					num++;
					data[num] = arrayOption[j];
				}
				arrayOption = data;
			}
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : idSubAccountTitle,
				strKey   : 'arrayOption',
				vars     : arrayOption
			});

		} else if (obj.vars.id.match(/^SelectIdSubAccountTitle(.*?)$/)) {
			var idNum = RegExp.$1;
			var idAccountTitle = 'SelectIdAccountTitle' + idNum;
			var vars = this._varsSensitive.ins.getVarsTarget({idTarget : idAccountTitle});
			var arrayOption = this._arrSubAccountTitle.arrSelectTag[vars.value];
			if (!arrayOption) {
				obj.vars.arrayOption = [this.insCurrent.vars.varsItem.arrNoneSub];

			} else {
				var num = 0;
				var data = [this.insCurrent.vars.varsItem.arrNoneSub];
				arrayOption = (Object.toJSON(arrayOption)).evalJSON();
				for (var j = 0; j < arrayOption.length; j++) {
					num++;
					data[num] = arrayOption[j];
				}
				obj.vars.arrayOption = data;
			}
		}

	},

	/**
	 *
	*/
	_updateVarsTextSum : function(obj)
	{
		var flagMenu = this._varsFlag.flagMenu;
		if (!flagMenu.match(/^detail/)) {
			return;
		}
		var idTarget = 'TextSum';
		var numPage = this._varsFlag.numPage;
		var numPageMax = this._varsPreference.jsonData.numPageMax;
		if (numPage != numPageMax) {
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : idTarget,
				strKey   : 'value',
				vars     : ''
			});
			return;
		}

		var varsValue = {};
		var num = this._varsKeyNum.JsonData;
		var arr = obj.arr[num].varsFormSensitive.varsDetail;
		for (var i = 0; i < arr.length; i++) {
			varsValue[arr[i].id] = arr[i].value;
		}

		var varsTextSum = this._getVarsDetailIniSensitive({idTarget : idTarget});
		var numSum = varsTextSum.value;

		if (numSum == '') {
			numSum = 0;

		} else {
			numSum = parseFloat(numSum);
		}

		if (numPage == 1) {
			numSum = 0;
		}

		var numEnd = this._varsCommon.varsStr[flagMenu].numRows;
		for (var i = 1; i <= numEnd; i++) {
			var id = 'TextValue' + i;
			var data = varsValue[id];
			if (data == '' || data == 0) {
				continue;

			} else {
				numSum += data;
			}
		}

		this._varsSensitive.ins.updateVarsTarget({
			idTarget : idTarget,
			strKey   : 'value',
			vars     : numSum
		});
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
		var insEscape = new Code_Lib_Escape();
		var insDisplay = new Code_Lib_TimeDisplay();

		var vars = this._varsSensitive.ins.getVarsTarget({idTarget : obj.idTarget});
		var ele = $(this._varsSensitive.ins.vars.varsStatus.id + vars.id);

		var insDisplayComma = new Code_Lib_DisplayComma();
		var insEscape = new Code_Lib_Escape();
		if (vars.flagValueType.match(/^num/)) {
			if (vars.value === 0) {
				ele.innerHTML = this.insCurrent.vars.varsItem.strSpace;

			} else if (vars.value == '') {
				ele.innerHTML = this.insCurrent.vars.varsItem.strSpace;

			} else {
				var num = insDisplayComma.get({
					num : vars.value
				});
				ele.innerHTML = num;
			}

		} else if (vars.flagValueType == 'select') {
			var arr = vars.arrayOption;
			if (!arr.length || vars.value == 'none') {
				ele.innerHTML = vars.valueStr;

			} else {
				if (vars.id.match(/^SelectIdAccountTitle(.*?)$/)) {
					ele.innerHTML = this._arrAccountTitle.arrStrTitle[vars.value].strTitleFS;

				} else {
					for (var i = 0; i < arr.length; i++) {
						if (arr[i].value == vars.value) {
							ele.innerHTML = arr[i].strTitle;
							return;
						}
					}
				}

			}

		} else {
			var data = '';
			if (vars.flagTag == 'textarea') {
				data = insEscape.get({data : vars.value, flagType : 'toHtml'});

			} else {
				data = insEscape.get({data : vars.value, flagType : 'fromTag'});
			}
			if (data == '') {
				data = this.insCurrent.vars.varsItem.strSpace;
			}
			ele.innerHTML = data;
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
	_updateVarsDetailSensitive : function(obj)
	{
		var insEscape = new Code_Lib_Escape();

		if (obj.strError) {
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
			return;
		}

		if (obj.vars.flagValueType.match(/^num/)) {
			if (obj.vars.value != '') {
				var num = parseFloat(obj.vars.value);
				if (obj.vars.value == 0) {
					if (obj.vars.flagValueType.match(/^num$/)) {
						num = '';
					}
				}
				this._varsSensitive.ins.updateVarsTarget({
					idTarget : obj.vars.id,
					strKey   : 'value',
					vars     : num
				});
			}
		}

		if (obj.vars.id.match(/^SelectIdAccountTitle(.*?)$/)) {
			var idNum = RegExp.$1;
			var idSubAccountTitle = 'SelectIdSubAccountTitle' + idNum;
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : idSubAccountTitle,
				strKey   : 'value',
				vars     : 0
			});
			if (obj.vars.value == 'none') {
				this._varsSensitive.ins.updateVarsTarget({
					idTarget : idSubAccountTitle,
					strKey   : 'arrayOption',
					vars     : []
				});
				this._varsSensitive.ins.updateVarsTarget({
					idTarget : idSubAccountTitle,
					strKey   : 'valueStr',
					vars     : this.vars.varsItem.strSpace
				});
				this._varsSensitive.ins.updateVarsTarget({
					idTarget : obj.vars.id,
					strKey   : 'valueStr',
					vars     : this.vars.varsItem.strSpace
				});
			}
		}

		this._updateSensitiveStep({arr : this.insDetail.insForm.vars.varsDetail});
		this._updateDetailFormSensitiveView({
			idTarget  : obj.vars.id
		});

		this._getDetailFormSensitiveVars({arr : this.insDetail.insForm.vars.varsDetail});
		this._setDetailFormSensitiveView({arr : this.insDetail.insForm.vars.varsDetail});
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

		return vars;
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
				this._setVarsData({arr : this.varsChild.varsDetail, flagIni : 1});

			} else {
				this._setVarsData({arr : this.varsChild.varsDetail});
			}
			this.vars.portal.varsDetail.varsDetail = this.varsChild.varsDetail;
			this._setDetailStart();
			return;

		} else if (obj.flag == 'add'
			|| obj.flag == 'edit'
			|| obj.flag == 'calc'
		) {
			if (this.insDetail.checkForm({flagType : 'common'})) return;
			var vars = this.insDetail.getFormValue();
			vars.JsonData = this._getVarsSensitiveValue();

			vars.VarsFlag = this._varsFlag;

			this._eventValue({
				vars     : vars,
				idTarget : (obj.flag == 'edit')? this.varsChild.idTarget : ''
			});

		} else if (!obj.flag.match(/^format(.*?)-save$/)) {
			obj.arr = (Object.toJSON(this.varsChild.varsDetail)).evalJSON();
			for (var i = 0; i < obj.arr.length; i++) {
				if (!obj.vars[obj.arr[i].id] || obj.arr[i].flagDisabled) continue;
			}
			this.vars.portal.varsDetail.varsDetail = obj.arr;
			this._eventRemoveDetailContent();
			this._setDetailStart();
			return;
		}

		this._varsDetailConnect = obj;
		this._sendDetailConnect();
	},

	/**
	 *
	*/
	_getVarsSensitiveValue : function()
	{
		var arr = this._varsSensitive.ins.vars.varsDetail;
		var data = {};
		for (var i = 0; i < arr.length; i++) {
			data[arr[i].idTarget] = arr[i].value;
		}

		return data;
	},

	/**
	 *
	*/
	_updateVarsSensitiveValue : function(obj)
	{
		var num = this._varsKeyNum.JsonData;
		var arr = this.insDetail.insForm.vars.varsDetail[num].varsFormSensitive.varsDetail;
		for (var i = 0; i < arr.length; i++) {
			var data = obj.varsValue['value' + arr[i].id];
			if (data == undefined) {
				continue;
			}
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : arr[i].id,
				strKey   : 'value',
				vars     : data
			});
			this._updateDetailFormSensitiveView({
				idTarget  : arr[i].id
			});
		}
		this._updateSensitiveStep({arr : this.insDetail.insForm.vars.varsDetail});
		this._getDetailFormSensitiveVars({arr : this.insDetail.insForm.vars.varsDetail});
		this._setDetailFormSensitiveView({arr : this.insDetail.insForm.vars.varsDetail});
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
				if (this.insCurrent.vars.varsFlag.flagMenu == this._varsFlag.flagMenu
					&& this.insCurrent.vars.varsFlag.numPage == this._varsFlag.numPage
				) {
					this.insCurrent.eventNaviConnectSuccessVars(obj);
				}
				this._setDetailEnd();

			} else if (this._varsDetailConnect.flag.match(/^calc$/)) {
				this._updateVarsSensitiveValue({varsValue : obj.json.data.varsValue});
				this.insDetail.showBtnBottom();
			}

		} else if (obj.json.flag == 40) {
			alert(this.insRoot.vars.varsSystem.str.oldData);
			if (!this.insWindow.vars.flagHideNow) this.insWindow.updateHide({ flagEffect : 1 });

		} else if (obj.json.flag == 42) {
			alert(this.insRoot.vars.varsSystem.str.errorMail);

		} else if (obj.json.flag == 8) {
			alert(this.insRoot.vars.varsSystem.str.oldData);

		} else {
			if (obj.json.flag == 'strOver') {
				this._updateVarsSensitiveValue({varsValue : obj.json.data.varsValue});
				this.insDetail.showBtnBottom();
				this.insDetail.showFormAttestError({flagType : obj.json.flag});

			} else if (obj.json.flag == 'strDouble') {
				this.insDetail.showBtnBottom();
				this.insDetail.showFormAttestError({flagType : obj.json.flag});

			} else {
				if (obj.json) {
					if (obj.json.flag) {
						if (this.insRoot.vars.varsSystem.str[obj.json.flag]) {
							alert(this.insRoot.vars.varsSystem.str[obj.json.flag]);
						}
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