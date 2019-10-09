{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_FixedAssetsEditor = Class.create(Code_Lib_ExtEditor,
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
	_varsDetailConfig : null,
	_iniVars : function(obj)
	{
		this._extVars(obj);
		this.vars.portal.varsDetail.varsDetail = this.varsChild.varsDetail;
		this._varsSearch = this.insCurrent.getVarsSearch();
		this._setVarsKeyNum({arr : this.vars.portal.varsDetail.varsDetail});
		this._setVarsSensitiveKeyNum({arr : this.vars.portal.varsDetail.varsDetail});
		this._varsDetailConfig = (Object.toJSON(this.vars.portal.varsDetail.varsDetail)).evalJSON();
		this._setVarsConfigDetail({arr : this._varsDetailConfig});
	},

	_setVarsConfigDetail : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'JsonDetail') {
				var arr = obj.arr[i].varsFormSensitive.varsDetail;
				for (var j = 0; j < arr.length; j++) {
					arr[j].value = arr[j].valueConfig;
				}

			} else {
				obj.arr[i].value = obj.arr[i].valueConfig;
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
		var idTarget = 'JsonDetail';
		var num = this._varsKeyNum[idTarget];
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
		var idTarget = 'JsonDetail';
		var num = this._varsKeyNum[idTarget];
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
	eventNaviBtnSave : function()
	{
		this.insDetail.setValue();
		this._setDetailContentValue();
		var vars = this._getDetailFormFormat();
		var data = {vars : vars, strTitle : vars.StrTitle};

		return data;
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
	_setDetailStart : function()
	{
		var str = 'strTitle' + this.varsChild.flagType.capitalize();

		this._updateVarsDetailStart({
			arr      : this.vars.portal.varsDetail.varsDetail,
			flagType : this.varsChild.flagType
		});

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
	_updateVarsDetailStart : function(obj)
	{
		/*
		this._checkSensitiveStyle({
			arr : obj.arr
		});
		*/

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
	_setDetailEnd : function()
	{
		this._varsDetailEnd = (Object.toJSON(this.insDetail.varsEventList)).evalJSON();
		this._getDetailFormContentVars({arr : this.insDetail.insForm.vars.varsDetail});
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
	_getDetailFormSensitiveVars : function(obj)
	{
		var idTarget = 'JsonDetail';
		var num = this._varsKeyNum[idTarget];
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
	_varsSensitive : {},
	_iniDetailFormSensitive : function()
	{
		var idTarget = 'JsonDetail';
		var num = this._varsKeyNum[idTarget];
		if (!this.insDetail.insForm.vars.varsDetail[num]) {
			return;
		}
		if (!this.insDetail.insForm.vars.varsDetail[num].varsFormSensitive) {
			return;
		}
		this._varsSensitive = {};
		this._setDetailFormSensitive({arr : this.insDetail.insForm.vars.varsDetail});
		this._checkSensitiveStyle();
		this._varsSensitive.ins.resetSense();
		this._getDetailFormSensitiveVars({arr : this.insDetail.insForm.vars.varsDetail});
	},

	/**
	 *
	*/
	_setDetailFormSensitive : function(obj)
	{
		var idTarget = 'JsonDetail';
		var num = this._varsKeyNum[idTarget];
		var ele = this.insDetail.insForm.eleWrap.down('.codeLibFormContent', this._varsContent.num);
		var str = obj.arr[num].varsFormSensitive.varsHtml;
		var data = str.interpolate({
			'idSelf'  : this.idSelf,
			'strMust' : this.insCurrent.vars.varsItem.strMust
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
			id        : obj.arr[num].id,
			ins       : insFormSensitive
		};
		this._checkSensitiveStyle();
		this._setVarsError({arr : this.insDetail.insForm.vars.varsDetail});
	},

	/**
	 *
	*/
	_varsError : [],
	_setVarsError : function(obj)
	{
		var idTarget = 'JsonDetail';
		var num = this._varsKeyNum[idTarget];
		var arr = obj.arr[num].varsFormSensitive.varsDetail;

		this._varsError = [];
		for (var i = 0; i < arr.length; i++) {
			if ($(this._varsSensitive.ins.vars.varsStatus.id + arr[i].id + 'Error')) {
				this._varsError.push(arr[i].id);
			}
		}
	},

	/**
	 *
	*/
	_resetViewError : function()
	{
		var arr = this._varsError;
		for (var i = 0; i < arr.length; i++) {
			var ele = $(this._varsSensitive.ins.vars.varsStatus.id + arr[i] + 'Error');
			ele.down('.codeLibBaseTableError', 0).innerHTML = '';
			ele.hide();
		}
	},

	/**
	 *
	*/
	_setViewError : function(obj)
	{
		this._resetViewError();
		var ele = $(this._varsSensitive.ins.vars.varsStatus.id + obj.idTarget);
		ele.down('.codeLibBaseTableError', 0).innerHTML = obj.strComment;
		ele.show();
	},


	/**
	 *
	*/
	_setDetailFormSensitiveView : function(obj)
	{
		var idTarget = 'JsonDetail';
		var num = this._varsKeyNum[idTarget];
		var arr = obj.arr[num].varsFormSensitive.varsDetail;
		for (var i = 0; i < arr.length; i++) {
			var strFuncView = '_updateView' + arr[i].id;
			if (this[strFuncView]) {
				this[strFuncView]({
					vars : arr[i]
				});
			}
		}
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
	_updateNumValueNumber : function(obj)
	{
		if (obj.vars.value) {
			obj.vars.value = parseFloat(obj.vars.value);
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.vars.value
			});
		}

		return obj;
	},

	/**
	 *
	*/
	_updateVarsDetailSensitive : function(obj)
	{
		this._resetViewError();
		if (obj) {
			var strFunc = '_updateVars' + obj.vars.id;
			if (this[strFunc]) {
				this[strFunc]({
					arr      : this.insDetail.insForm.vars.varsDetail,
					vars     : obj.vars,
					varsPrev : obj.varsPrev,
					strError : obj.strError
				});

			} else {
				this._updateVarsElse({
					vars     : obj.vars,
					varsPrev : obj.varsPrev,
					strError : obj.strError
				});
			}
		}

		this._checkSensitiveStyle();
		this._varsSensitive.ins.resetSense();
		this._getDetailFormSensitiveVars({arr : this.insDetail.insForm.vars.varsDetail});
	},



	/**
	 *
	*/
	_checkSensitiveStyle : function()
	{
		var idTarget = 'JsonDetail';
		var num = this._varsKeyNum[idTarget];

		if (this._checkStepFirst()) {
			this._resetSensitiveStepFirst({arr : this._varsDetailConfig});
			return;
		}
		this._resetSensitiveStepDepMethod({arr : this._varsDetailConfig});
	},

	/**
	 *
	*/
	_checkStepFirst : function()
	{
		var idTarget = 'JsonDetail';
		var num = this._varsKeyNum[idTarget];

		var varsIdAccountTitle =this._varsSensitive.ins.getVarsTarget({idTarget : 'IdAccountTitle'});
		var varsFlagDepMethod = this._varsSensitive.ins.getVarsTarget({idTarget : 'FlagDepMethod'});

		if (varsIdAccountTitle.value == 'none' || varsFlagDepMethod.value == 'none') {
			return 1;
		}

		return 0;
	},

	/**
	 *
	*/
	_resetSensitiveStepDepMethod : function(obj)
	{
		var insEscape = new Code_Lib_Escape();

		var idTarget = 'JsonDetail';
		var num = this._varsKeyNum[idTarget];
		var flagFirst = this._checkStepFirst();

		var varsValue = {};
		var arrValue = this.insDetail.insForm.vars.varsDetail[num].varsFormSensitive.varsDetail;
		for (var i = 0; i < arrValue.length; i++) {
			varsValue[arrValue[i].id] = arrValue[i].value;
		}

		var arr = obj.arr[num].varsFormSensitive.varsDetail;
		for (var i = 0; i < arr.length; i++) {

			if (arr[i].id == 'IdAccountTitle' || arr[i].id == 'FlagDepMethod') {
				this._varsSensitive.ins.updateVarsTarget({
					idTarget : arr[i].id,
					strKey   : 'flagForm',
					vars     : 'active'
				});
				continue;
			}

			if (flagFirst) {
				this._varsSensitive.ins.updateVarsTarget({
					idTarget : arr[i].id,
					strKey   : 'flagForm',
					vars     : ''
				});
				continue;
			}

			if (!arr[i].varsForm.FlagDepMethod[varsValue.FlagDepMethod]) {
				this._varsSensitive.ins.updateVarsTarget({
					idTarget : arr[i].id,
					strKey   : 'flagForm',
					vars     : ''
				});
				continue;
			}

			/*show*/
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : arr[i].id,
				strKey   : 'flagForm',
				vars     : arr[i].varsForm.FlagDepMethod[varsValue.FlagDepMethod]
			});

			if (arr[i].varsForm.FlagTaxFixed) {
				if (varsValue.FlagTaxFixed == 'none' || varsValue.FlagTaxFixed == 'free') {
					this._varsSensitive.ins.updateVarsTarget({
						idTarget : arr[i].id,
						strKey   : 'flagForm',
						vars     : arr[i].varsForm.FlagTaxFixed.none
					});
					continue;

				} else {
					this._varsSensitive.ins.updateVarsTarget({
						idTarget : arr[i].id,
						strKey   : 'flagForm',
						vars     : arr[i].varsForm.FlagTaxFixed.other
					});
				}
			}

			if (arr[i].varsForm.StampStart) {
				if (varsValue.StampStart == '') {
					this._varsSensitive.ins.updateVarsTarget({
						idTarget : arr[i].id,
						strKey   : 'flagForm',
						vars     : arr[i].varsForm.StampStart.none
					});
					continue;

				} else {
					var stampStart = insEscape.toStampFromTerm({
						data        : varsValue.StampStart,
						insTimeZone : this.insRoot.insTimeZone
					});
					if (arr[i].varsForm.StampStart.flag20070401 && (varsValue.FlagDepMethod == 'straight' || varsValue.FlagDepMethod == 'declining')) {
						var flag = 0;
						if (stampStart < this.insCurrent.vars.varsItem.varsStamp.flagDepMethod) {
							this._varsSensitive.ins.updateVarsTarget({
								idTarget : arr[i].id,
								strKey   : 'flagForm',
								vars     : arr[i].varsForm.StampStart.flag20070331
							});

						} else {
							this._varsSensitive.ins.updateVarsTarget({
								idTarget : arr[i].id,
								strKey   : 'flagForm',
								vars     : arr[i].varsForm.StampStart.flag20070401
							});
						}

					} else {
						this._varsSensitive.ins.updateVarsTarget({
							idTarget : arr[i].id,
							strKey   : 'flagForm',
							vars     : arr[i].varsForm.StampStart.other
						});
					}


				}
			}

			if (arr[i].varsForm.FlagDepDown) {
				if (varsValue.FlagDepDown == 'none') {
					this._varsSensitive.ins.updateVarsTarget({
						idTarget : arr[i].id,
						strKey   : 'flagForm',
						vars     : arr[i].varsForm.FlagDepDown.none
					});
					continue;

				} else {
					this._varsSensitive.ins.updateVarsTarget({
						idTarget : arr[i].id,
						strKey   : 'flagForm',
						vars     : arr[i].varsForm.FlagDepDown.other
					});
				}
			}

			if (arr[i].varsForm.NumValue) {
				if (!varsValue.NumValue) {
					this._varsSensitive.ins.updateVarsTarget({
						idTarget : arr[i].id,
						strKey   : 'flagForm',
						vars     : arr[i].varsForm.NumValue.none
					});
					continue;

				} else {
					this._varsSensitive.ins.updateVarsTarget({
						idTarget : arr[i].id,
						strKey   : 'flagForm',
						vars     : arr[i].varsForm.NumValue.other
					});
					var arrStr = ['SellingAdminCost', 'ProductsCost', 'NonOperatingExpenses', 'AgricultureCost'];
					for (var j = 0; j < arrStr.length; j++) {
						if (arr[i].varsForm[arrStr[j]]) {
							if (varsValue[arrStr[j]] == 'none') {
								this._varsSensitive.ins.updateVarsTarget({
									idTarget : arr[i].id,
									strKey   : 'flagForm',
									vars     : arr[i].varsForm[arrStr[j]].none
								});

							} else {
								this._varsSensitive.ins.updateVarsTarget({
									idTarget : arr[i].id,
									strKey   : 'flagForm',
									vars     : arr[i].varsForm[arrStr[j]].other
								});
							}
						}
					}
					var stampStart = insEscape.toStampFromTerm({
						data        : varsValue.StampStart,
						insTimeZone : this.insRoot.insTimeZone
					});
					if (arr[i].varsForm.NumValue.flagCurrent && (stampStart >= this.insCurrent.vars.varsItem.varsStampTerm.stampMin)) {
						this._varsSensitive.ins.updateVarsTarget({
							idTarget : arr[i].id,
							strKey   : 'flagForm',
							vars     : arr[i].varsForm.NumValue.flagCurrent
						});
					}
				}
			}

		}
		for (var i = 0; i < arr.length; i++) {
			this._allotSensitiveStepDepMethod({
				idTarget  : arr[i].id,
				flagFirst : flagFirst,
				varsValue : varsValue
			});
		}
	},

	/**
	 *
	*/
	_allotSensitiveStepDepMethod : function(obj)
	{
		var insEscape = new Code_Lib_Escape();

		var vars = this._varsSensitive.ins.getVarsTarget({idTarget : obj.idTarget});
		var ele = $(this._varsSensitive.ins.vars.varsStatus.id + vars.id);
		var eleStrTitle = $(this._varsSensitive.ins.vars.varsStatus.id + vars.id + 'StrTitle');
		eleStrTitle.removeClassName(this.insCurrent.vars.varsItem.strClassNone);
		var flag = '';
		if (!(vars.id == 'IdAccountTitle' || vars.id == 'FlagDepMethod')) {

			if (obj.flagFirst) {
				ele.innerHTML = '';
				eleStrTitle.addClassName(this.insCurrent.vars.varsItem.strClassNone);
				return;
			}

			flag = vars.varsForm.FlagDepMethod[obj.varsValue.FlagDepMethod];
			if (flag == '') {
				ele.innerHTML = '';
				eleStrTitle.addClassName(this.insCurrent.vars.varsItem.strClassNone);
				return;
			}

			if (vars.varsForm.StampStart) {
				if (obj.varsValue.StampStart == '') {
					flag = vars.varsForm.StampStart.none;
					if (flag == '') {
						ele.innerHTML = '';
						eleStrTitle.addClassName(this.insCurrent.vars.varsItem.strClassNone);
						return;
					}
				} else {
					if (vars.varsForm.StampStart.flag20070401 && (obj.varsValue.FlagDepMethod == 'straight' || obj.varsValue.FlagDepMethod == 'declining')) {
						var flag = 0;
						var stampStart = insEscape.toStampFromTerm({
							data        : obj.varsValue.StampStart,
							insTimeZone : this.insRoot.insTimeZone
						});
						if (stampStart >= this.insCurrent.vars.varsItem.varsStamp.flagDepMethod) {
							ele.innerHTML = '';
							eleStrTitle.addClassName(this.insCurrent.vars.varsItem.strClassNone);
							return;
						}

					} else {
						flag = vars.varsForm.StampStart.other;
						if (flag == '') {
							ele.innerHTML = '';
							eleStrTitle.addClassName(this.insCurrent.vars.varsItem.strClassNone);
						}
						if (vars.varsForm.NumValue) {
							eleStrTitle.removeClassName(this.insCurrent.vars.varsItem.strClassNone);
							if (!obj.varsValue.NumValue) {
								flag = vars.varsForm.NumValue.none;
								if (flag == '') {
									ele.innerHTML = '';
									eleStrTitle.addClassName(this.insCurrent.vars.varsItem.strClassNone);
									return;
								}

							} else {
								flag = vars.varsForm.NumValue.other;
								if (flag == '') {
									ele.innerHTML = '';
									eleStrTitle.addClassName(this.insCurrent.vars.varsItem.strClassNone);
									return;
								}
							}
						}
					}
				}
			}
		}

		var strFuncView = '_updateView' + vars.id;
		if (this[strFuncView]) {
			this[strFuncView]({
				vars          : vars,
				ele           : ele,
				eleStrTitle   : eleStrTitle,
				flagDepMethod : obj.varsValue.FlagDepMethod
			});

		} else {
			this._updateViewElse({
				vars          : vars,
				ele           : ele,
				eleStrTitle   : eleStrTitle,
				flagDepMethod : obj.varsValue.FlagDepMethod
			});
		}
	},

	/**
	 *
	*/
	_resetSensitiveStepFirst : function(obj)
	{
		var idTarget = 'JsonDetail';
		var num = this._varsKeyNum[idTarget];
		var flagFirst = this._checkStepFirst();
		var varsFlagDepMethod = this._varsSensitive.ins.getVarsTarget({idTarget : 'FlagDepMethod'});

		var varsValue = {};
		var arr = obj.arr[num].varsFormSensitive.varsDetail;
		for (var i = 0; i < arr.length; i++) {
			varsValue[arr[i].id] = arr[i].value;
		}

		for (var i = 0; i < arr.length; i++) {
			var data = '';
			if (!arr[i].flagForm) {
				data = '';

			} else if (arr[i].id == 'IdAccountTitle' || arr[i].id == 'FlagDepMethod') {
				data = 'active';

			}
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : arr[i].id,
				strKey   : 'flagForm',
				vars     : data
			});

			this._allotSensitiveStepDepMethod({
				idTarget      : arr[i].id,
				flagFirst     : flagFirst,
				varsValue     : varsValue
			});
		}
	},



	/**
	 *
	*/
	_escapeValue : function(obj)
	{
		this.insEscape = new Code_Lib_Escape();
		var data = this.insEscape.get({data : obj.value, flagType : 'fromTag'});

		return data;
	},

	/**
	 *
	*/
	_updateVarsIdAccountTitle : function(obj)
	{
		this._resetVarsStepFirst({arr : this._varsDetailConfig, flagDepMethod : 0});
	},

	/**
	 *
	*/
	_resetVarsSensitive : function()
	{
		var idTarget = 'JsonDetail';
		var num = this._varsKeyNum[idTarget];
		var arr = this._varsDetailConfig[num].varsFormSensitive.varsDetail;
		for (var i = 0; i < arr.length; i++) {
			this._resetVarsDetailConfigSensitive({arr : [arr[i].id]});
		}
		this._updateVarsDetailSensitive();
	},

	/**
	 *
	*/
	_resetVarsStepFirst : function(obj)
	{
		var insEscape = new Code_Lib_Escape();

		var idTarget = 'JsonDetail';
		var num = this._varsKeyNum[idTarget];
		var arr = obj.arr[num].varsFormSensitive.varsDetail;
		for (var i = 0; i < arr.length; i++) {
			if (arr[i].id == 'IdAccountTitle' || arr[i].id == 'FlagDepMethod') {
				continue;
			}
			this._resetVarsDetailConfigSensitive({arr : [arr[i].id]});
		}

		var varsIdAccountTitle = this._varsSensitive.ins.getVarsTarget({idTarget : 'IdAccountTitle'});
		var idAccountTitle = varsIdAccountTitle.value;
		if (idAccountTitle != 'none') {
			var cut = this.insCurrent.vars.varsItem.arrAccountTitleFixedAssets.arrStrTitle;
			if (!cut[idAccountTitle]) {
				return;

			} else if (!cut[idAccountTitle].varsFixedAssets) {
				return;
			}
			for (var i = 0; i < arr.length; i++) {
				if (obj.flagDepMethod) {
					if (arr[i].id == 'FlagDepMethod') {
						continue;
					}
				}
				var id = insEscape.strLowCapitalize({data : arr[i].id});
				if (cut[idAccountTitle].varsFixedAssets[id]) {
					var data = cut[idAccountTitle].varsFixedAssets[id];
					this._varsSensitive.ins.updateVarsTarget({
						idTarget : arr[i].id,
						strKey   : 'value',
						vars     : data
					});
				}
			}
		}
	},

	/**
	 *
	*/
	_updateViewIdAccountTitle : function(obj)
	{
		if (obj.vars.value == 'none') {
			obj.ele.innerHTML = '';

		} else {
			if (!this.insCurrent.vars.varsItem.arrAccountTitle.arrStrTitle[obj.vars.value]) {
				obj.ele.innerHTML = this.insCurrent.vars.varsItem.strLost;

			} else {
				obj.ele.innerHTML = this.insCurrent.vars.varsItem.arrAccountTitle.arrStrTitle[obj.vars.value].strTitleFS;
			}
		}
	},

	/**
	 *
	*/
	_updateVarsFlagDepMethod : function(obj)
	{
		this._resetVarsStepFirst({arr : this._varsDetailConfig, flagDepMethod : 1});
	},

	_updateViewFlagDepMethod : function(obj)
	{
		var insEscape = new Code_Lib_Escape();

		var id = insEscape.strLowCapitalize({data : obj.vars.id});
		if (obj.vars.value == 'none') {
			obj.ele.innerHTML = '';

		} else {
			obj.ele.innerHTML = this.insCurrent.vars.varsItem.varsOptions[id].arrStrTitle[obj.vars.value];
		}
	},

	/**
	 *
	*/
	_updateVarsNumUsefulLife : function(obj)
	{
		if (obj.varsPrev.value != obj.vars.value) {
			this._resetVarsNumValueDepCalc();
			this._updateVarsCalcDep();
		}
	},

	/**
	 *
	*/
	_updateViewNumUsefulLife : function(obj)
	{
		obj.ele.innerHTML = obj.vars.value + obj.vars.varsTmpl.strYear;
	},

	/**
	 *
	*/
	_updateVarsNumVolume : function(obj)
	{
		if (obj.strError) {
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
			return;
		}
		if (obj.vars.value == '') {
			this._resetVarsDetailConfigSensitive({arr : [obj.vars.id]});

		} else {
			if (!obj.vars.value.match(/^[0-9]{1,3}\.[0-9]{2,2}$/)) {
				this._setViewError({
					idTarget   : obj.vars.idError,
					strComment : obj.vars.varsError.strFormat
				});
				this._varsSensitive.ins.updateVarsTarget({
					idTarget : obj.vars.id,
					strKey   : 'value',
					vars     : obj.varsPrev.value
				});
			}
		}

	},

	/**
	 *
	*/
	_updateViewFlagDepUnit : function(obj)
	{
		obj.ele.innerHTML = this._escapeValue({value : obj.vars.value});
	},

	/**
	 *
	*/
	_updateViewIdDepartment : function(obj)
	{
		if (obj.vars.value == '' || obj.vars.value == 0) {
			obj.ele.innerHTML = '';

		} else {
			if (!this.insCurrent.vars.varsItem.arrDepartment.arrStrTitle[obj.vars.value]) {
				obj.ele.innerHTML = this.insCurrent.vars.varsItem.strLost;

			} else {
				obj.ele.innerHTML = this.insCurrent.vars.varsItem.arrDepartment.arrStrTitle[obj.vars.value].strTitle;
			}
		}
	},

	_updateViewFlagTaxFixed : function(obj)
	{
		this._updateViewFlagTaxFixedWrap(obj);
	},

	_updateVarsFlagTaxFixed : function(obj)
	{
		if (obj.vars.value == 'none' || obj.vars.value == 'free') {
			this._resetVarsDetailConfigSensitive({arr : ['FlagTaxFixedType']});
		}
	},

	_updateViewFlagTaxFixedType : function(obj)
	{
		this._updateViewFlagTaxFixedWrap(obj);
	},

	_updateViewFlagTaxFixedWrap : function(obj)
	{
		var insEscape = new Code_Lib_Escape();

		var id = insEscape.strLowCapitalize({data : obj.vars.id});
		if (obj.vars.value == 'none') {
			obj.ele.innerHTML = '';

		} else {
			obj.ele.innerHTML = this.insCurrent.vars.varsItem.varsOptions[id].arrStrTitle[obj.vars.value];
		}
	},

	_updateViewFlagDepUp : function(obj)
	{
		this._updateViewFlagTaxFixedWrap(obj);
	},

	_updateViewFlagDepDown : function(obj)
	{
		this._updateViewFlagTaxFixedWrap(obj);
	},

	_updateVarsFlagDepDown : function(obj)
	{
		if (obj.vars.value == 'none') {
			this._resetVarsDetailConfigSensitive({arr : ['StampDrop']});
		}
	},

	/**
	 *
	*/
	_updateVarsStampBuy : function(obj)
	{
		var flag = this._checkVarsStamp(obj);

		if (obj.strError) {
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
			return;

		} else if (flag) {
			this._setViewError({
				idTarget   : obj.vars.idError,
				strComment : obj.vars.varsError[flag]
			});
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
			return;
		}

		var insEscape = new Code_Lib_Escape();
		var varsStampStart = this._varsSensitive.ins.getVarsTarget({idTarget : 'StampStart'});
		var valueStampStart = varsStampStart.value;
		var strStampStart = obj.vars.value;
		this._varsSensitive.ins.updateVarsTarget({
			idTarget : 'StampStart',
			strKey   : 'value',
			vars     : strStampStart
		});

		if (this._checkVarsStampStartStampEnd()) {
			this._setViewError({
				idTarget   : obj.vars.idError,
				strComment : obj.vars.varsError.strStampStartStampEnd
			});
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
			stampStart = valueStampStart;
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : 'StampStart',
				strKey   : 'value',
				vars     : strStampStart
			});
		}

		var stampBuy = insEscape.toStampFromTerm({
			data        : obj.vars.value,
			insTimeZone : this.insRoot.insTimeZone
		});

		var stampStart = insEscape.toStampFromTerm({
			data        : strStampStart,
			insTimeZone : this.insRoot.insTimeZone
		});

		var varsNumValueAccumulated = this._varsSensitive.ins.getVarsTarget({idTarget : 'NumValueAccumulated'});
		if (varsNumValueAccumulated.varsForm.NumValue.flagCurrent
			&& (stampStart >= this.insCurrent.vars.varsItem.varsStampTerm.stampMin)
		) {
			this._resetVarsDetailConfigSensitive({arr : ['NumValueAccumulated', 'NumValueDepPrevOver', 'NumValueDepPrevOverData']});
		}
		if (obj.varsPrev.value != obj.vars.value) {
			this._resetVarsNumValueDepCalc();
		}
		this._updateVarsCalcDep();

		var varsIdAccountTitle = this._varsSensitive.ins.getVarsTarget({idTarget : 'IdAccountTitle'});
		var varsFlagDepMethod = this._varsSensitive.ins.getVarsTarget({idTarget : 'FlagDepMethod'});

		if (stampBuy >= this.insCurrent.vars.varsItem.varsStamp.buildings
			&& varsIdAccountTitle.value == 'buildings'
			&& varsFlagDepMethod.value == 'declining'
		) {
			this._setViewError({
				idTarget   : obj.vars.idError,
				strComment : obj.vars.varsError.strBuildings
			});
		}

		if (stampBuy >= this.insCurrent.vars.varsItem.varsStamp.buildingsHeisei28
			&& (varsIdAccountTitle.value == 'buildingsAndAccessoyEquipment'
				|| varsIdAccountTitle.value == 'structures')
			&& varsFlagDepMethod.value == 'declining'
		) {
			this._setViewError({
				idTarget   : obj.vars.idError,
				strComment : obj.vars.varsError.strBuildingsHeisei28
			});
		}
	},

	/**
	 *
	*/
	_updateViewStampBuy : function(obj)
	{
		this._updateViewStamp(obj);
	},

	/**
	 *
	*/
	_updateVarsStampStart : function(obj)
	{
		var flag = this._checkVarsStamp(obj);

		if (obj.strError) {
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
			return;

		} else if (flag) {
			this._setViewError({
				idTarget   : obj.vars.idError,
				strComment : obj.vars.varsError[flag]
			});
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
			return;
		}

		var insEscape = new Code_Lib_Escape();

		var varsStampBuy = this._varsSensitive.ins.getVarsTarget({idTarget : 'StampBuy'});
		var valueStampBuy = varsStampBuy.value;

		var stampBuy = insEscape.toStampFromTerm({
			data        : varsStampBuy.value,
			insTimeZone : this.insRoot.insTimeZone
		});
		var stampStart = insEscape.toStampFromTerm({
			data        : obj.vars.value,
			insTimeZone : this.insRoot.insTimeZone
		});

		if (stampBuy > stampStart) {
			this._setViewError({
				idTarget   : obj.vars.idError,
				strComment : obj.vars.varsError.strStampBuy
			});
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
			return;
		}

		if (this._checkVarsStampStartStampEnd()) {
			this._setViewError({
				idTarget   : obj.vars.idError,
				strComment : obj.vars.varsError.strStampStartStampEnd
			});
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : 'StampBuy',
				strKey   : 'value',
				vars     : valueStampBuy
			});
		}

		var varsNumValueAccumulated = this._varsSensitive.ins.getVarsTarget({idTarget : 'NumValueAccumulated'});
		if (varsNumValueAccumulated.varsForm.NumValue.flagCurrent
			&& (stampStart >= this.insCurrent.vars.varsItem.varsStampTerm.stampMin)
		) {
			this._resetVarsDetailConfigSensitive({arr : ['NumValueAccumulated', 'NumValueDepPrevOver', 'NumValueDepPrevOverData']});
		}
		if (obj.varsPrev.value != obj.vars.value) {
			this._resetVarsNumValueDepCalc();
		}
		this._updateVarsCalcDep();

		var varsIdAccountTitle = this._varsSensitive.ins.getVarsTarget({idTarget : 'IdAccountTitle'});
		var varsFlagDepMethod = this._varsSensitive.ins.getVarsTarget({idTarget : 'FlagDepMethod'});

		if (stampBuy >= this.insCurrent.vars.varsItem.varsStamp.buildings
			&& varsIdAccountTitle.value == 'buildings'
			&& varsFlagDepMethod.value == 'declining'
		) {
			this._setViewError({
				idTarget   : obj.vars.idError,
				strComment : obj.vars.varsError.strBuildings
			});
		}

		if (stampBuy >= this.insCurrent.vars.varsItem.varsStamp.buildingsHeisei28
			&& (varsIdAccountTitle.value == 'buildingsAndAccessoyEquipment'
				|| varsIdAccountTitle.value == 'structures')
			&& varsFlagDepMethod.value == 'declining'
		) {
			this._setViewError({
				idTarget   : obj.vars.idError,
				strComment : obj.vars.varsError.strBuildingsHeisei28
			});
		}

	},

	/**
	 *
	*/
	_updateViewStampStart : function(obj)
	{
		this._updateViewStamp(obj);
	},

	/**
	 *
	*/
	_updateVarsStampDrop : function(obj)
	{
		var flag = this._checkVarsStamp(obj);

		if (obj.strError) {
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
			return;

		} else if (flag) {
			this._setViewError({
				idTarget   : obj.vars.idError,
				strComment : obj.vars.varsError[flag]
			});
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
			return;
		}

		var insEscape = new Code_Lib_Escape();
		var varsStampEnd = this._varsSensitive.ins.getVarsTarget({idTarget : 'StampEnd'});
		var valueStampEnd = varsStampEnd.value;

		this._varsSensitive.ins.updateVarsTarget({
			idTarget : 'StampEnd',
			strKey   : 'value',
			vars     : obj.vars.value
		});

		if (this._checkVarsStampStartStampEnd()) {
			this._setViewError({
				idTarget   : obj.vars.idError,
				strComment : obj.vars.varsError.strStampStartStampEnd
			});
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : 'StampEnd',
				strKey   : 'value',
				vars     : valueStampEnd
			});
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
		}
		if (obj.varsPrev.value != obj.vars.value) {
			this._resetVarsNumValueDepCalc();
		}
		this._updateVarsCalcDep();
	},

	/**
	 *
	*/
	_updateViewStampDrop : function(obj)
	{
		this._updateViewStamp(obj);
	},

	/**
	 *
	*/
	_updateVarsStampEnd : function(obj)
	{
		var flag = this._checkVarsStamp(obj);

		if (obj.strError) {
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
			return;

		} else if (flag) {
			this._setViewError({
				idTarget   : obj.vars.idError,
				strComment : obj.vars.varsError[flag]
			});
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
			return;
		}

		var insEscape = new Code_Lib_Escape();

		var varsFlagDepDown = this._varsSensitive.ins.getVarsTarget({idTarget : 'FlagDepDown'});
		var varsStampDrop = this._varsSensitive.ins.getVarsTarget({idTarget : 'StampDrop'});
		var valueStampDrop = varsStampDrop.value;

		if (varsFlagDepDown.value == 'none') {
			this._resetVarsDetailConfigSensitive({arr : ['StampDrop']});

		} else {
			if (varsStampDrop.value == '') {
				this._varsSensitive.ins.updateVarsTarget({
					idTarget : 'StampDrop',
					strKey   : 'value',
					vars     : obj.vars.value
				});

			} else {
				var stampDrop = insEscape.toStampFromTerm({
					data        : varsStampDrop.value,
					insTimeZone : this.insRoot.insTimeZone
				});
				var stampEnd = insEscape.toStampFromTerm({
					data        : obj.vars.value,
					insTimeZone : this.insRoot.insTimeZone
				});
				if (stampDrop < stampEnd) {
					this._setViewError({
						idTarget   : obj.vars.idError,
						strComment : obj.vars.varsError.strStampDrop
					});
					this._varsSensitive.ins.updateVarsTarget({
						idTarget : obj.vars.id,
						strKey   : 'value',
						vars     : varsStampDrop.value
					});
				}
			}
		}

		if (this._checkVarsStampStartStampEnd()) {
			this._setViewError({
				idTarget   : obj.vars.idError,
				strComment : obj.vars.varsError.strStampStartStampEnd
			});
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : 'StampDrop',
				strKey   : 'value',
				vars     : valueStampDrop
			});
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
		}
		if (obj.varsPrev.value != obj.vars.value) {
			this._resetVarsNumValueDepCalc();
		}
		this._updateVarsCalcDep();
	},

	/**
	 *
	*/
	_updateViewStampEnd : function(obj)
	{
		this._updateViewStamp(obj);
	},

	/**
	 *
	*/
	_checkVarsStamp : function(obj)
	{
		var insEscape = new Code_Lib_Escape();

		var flag = 0;
		if (obj.vars.value == '') {
			return '';
		}

		var stamp = insEscape.toStampFromTerm({
			data        : obj.vars.value,
			insTimeZone : this.insRoot.insTimeZone
		});

		if (stamp > this.insCurrent.vars.varsItem.varsStampTerm.stampMax) {
			flag = 'strStampTermMax';

		} else if (stamp < this.insCurrent.vars.varsItem.varsStamp.stampMeiji) {
			flag = 'strStampTermMin';
		}

		return flag;
	},

	/**
	 *
	*/
	_checkVarsStampStartStampEnd : function()
	{
		var insEscape = new Code_Lib_Escape();

		var varsStampStart = this._varsSensitive.ins.getVarsTarget({idTarget : 'StampStart'});
		var varsStampEnd = this._varsSensitive.ins.getVarsTarget({idTarget : 'StampEnd'});
		if (varsStampStart.value != '' && varsStampEnd.value != '') {
			var stampStart = insEscape.toStampFromTerm({
				data        : varsStampStart.value,
				insTimeZone : this.insRoot.insTimeZone
			});
			var stampEnd = insEscape.toStampFromTerm({
				data        : varsStampEnd.value,
				insTimeZone : this.insRoot.insTimeZone
			});
			if (stampStart > stampEnd) {
				return 1;
			}
		}
	},

	/**
	 *
	*/
	_updateViewStamp : function(obj)
	{
		var insEscape = new Code_Lib_Escape();
		var insDisplay = new Code_Lib_TimeDisplay();

		if (obj.vars.value == '') {
			obj.ele.innerHTML = '';

		} else {
			var stamp = insEscape.toStampFromTerm({
				data        : obj.vars.value,
				insTimeZone : this.insRoot.insTimeZone
			});
			var arrTime = obj.vars.value.split('/');
			/*20190401 start*/
			var strNengo = insDisplay.getStrNengoYear({
				stamp   : stamp,
				numYear : arrTime[0]
			});
			/*20190401 end*/
			obj.ele.innerHTML = '('+ strNengo +') ' + obj.vars.value;
		}
	},

	/**
	 *
	*/
	_resetVarsNumValue : function()
	{
		var idTarget = 'JsonDetail';
		var num = this._varsKeyNum[idTarget];
		var arr = this._varsDetailConfig[num].varsFormSensitive.varsDetail;
		for (var i = 0; i < arr.length; i++) {
			if (!arr[i].flagNumValueConfig) {
				continue;
			}
			this._resetVarsDetailConfigSensitive({arr : [arr[i].id]});
		}
	},

	/**
	 *
	*/
	_resetVarsNumValueDep : function()
	{
		var idTarget = 'JsonDetail';
		var num = this._varsKeyNum[idTarget];
		var arr = this._varsDetailConfig[num].varsFormSensitive.varsDetail;
		for (var i = 0; i < arr.length; i++) {
			if (!arr[i].flagNumValueDepConfig) {
				continue;
			}
			this._resetVarsDetailConfigSensitive({arr : [arr[i].id]});
		}
	},

	/**
	 *
	*/
	_resetVarsNumValueDepCalc : function()
	{
		var idTarget = 'JsonDetail';
		var num = this._varsKeyNum[idTarget];
		var arr = this._varsDetailConfig[num].varsFormSensitive.varsDetail;
		for (var i = 0; i < arr.length; i++) {
			if (!arr[i].flagNumValueDepCalcConfig) {
				continue;
			}
			this._resetVarsDetailConfigSensitive({arr : [arr[i].id]});
		}
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
			flagType : 'num',
			value    : strValue
		});

		if (flag) return '';

		return parseFloat(strValue);
	},

	/**
	 *
	*/
	_updateVarsNumValue : function(obj)
	{
		if (obj.strError || !obj.vars.value) {
			if (!obj.vars.value) {
				this._resetVarsNumValue();

			} else {
				this._varsSensitive.ins.updateVarsTarget({
					idTarget : obj.vars.id,
					strKey   : 'value',
					vars     : obj.varsPrev.value
				});
			}
			return;
		}

		obj = this._updateNumValueNumber(obj);

		var flagCalc = this._checkVarsCalc();
		if (flagCalc) {
			this._setViewError({
				idTarget   : obj.vars.idError,
				strComment : obj.vars.varsError[flagCalc]
			});
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
			return;
		}
		this._updateVarsCalc();
		if (obj.vars.value != obj.varsPrev.value) {
			this._resetVarsNumValueDep();
			this._updateVarsCalcDep();
		}

		var varsNumValueNet = this._varsSensitive.ins.getVarsTarget({idTarget : 'NumValueNet'});
		var varsFlagDepMethod = this._varsSensitive.ins.getVarsTarget({idTarget : 'FlagDepMethod'});
		if (varsNumValueNet.value >= this.insCurrent.vars.varsItem.numValueNetSumLimit
			&& varsFlagDepMethod.value == 'sum'
		) {
			this._setViewError({
				idTarget   : obj.vars.idError,
				strComment : obj.vars.varsError.strSumLawOver
			});
		}
	},

	/**
	 *
	*/
	_checkVarsCalc : function()
	{
		var numValue = this._getNumValueTarget({idTarget : 'NumValue'});
		var numValueCompression = this._getNumValueTarget({idTarget : 'NumValueCompression'});
		var numValueAccumulated = this._getNumValueTarget({idTarget : 'NumValueAccumulated'});
		var numValueRemainingBook = this._getNumValueTarget({idTarget : 'NumValueRemainingBook'});

		var varsFlagDepMethod = this._varsSensitive.ins.getVarsTarget({idTarget : 'FlagDepMethod'});
		var numValueNet = numValue - numValueCompression;
		if (numValueNet < 0) {
			return 'strNumValueNet';
		}

		if (varsFlagDepMethod.value != 'sum' && varsFlagDepMethod.value != 'noneDep') {
			if (numValueNet < numValueRemainingBook) {
				return 'strNumValueRemainingBook';
			}
		}

		var numValueNetOpening = numValueNet - numValueAccumulated;
		if (varsFlagDepMethod.value != 'sum') {
			if (numValueNetOpening < 0) {
				return 'strNumValueOpening';
			}
		}

		if (varsFlagDepMethod.value != 'sum' && varsFlagDepMethod.value != 'noneDep') {
			if (numValueNetOpening < numValueRemainingBook) {
				return 'strNumValueRemainingBook';
			}
		}
	},

	/**
	 *
	*/
	_updateVarsCalc : function()
	{
		var numValue = this._getNumValueTarget({idTarget : 'NumValue'});
		var numValueCompression = this._getNumValueTarget({idTarget : 'NumValueCompression'});
		var numValueAccumulated = this._getNumValueTarget({idTarget : 'NumValueAccumulated'});
		var varsFlagDepMethod = this._varsSensitive.ins.getVarsTarget({idTarget : 'FlagDepMethod'});

		var numValueNet = numValue - numValueCompression;
		this._varsSensitive.ins.updateVarsTarget({
			idTarget : 'NumValueNet',
			strKey   : 'value',
			vars     : numValueNet
		});

		if (varsFlagDepMethod.value != 'sum') {
			var numValueNetOpening = numValueNet - numValueAccumulated;
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : 'NumValueNetOpening',
				strKey   : 'value',
				vars     : numValueNetOpening
			});
		}
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
	_getVarsValueCalc : function(obj)
	{
		var arr = this._varsSensitive.ins.vars.varsDetail;
		var data = {};
		for (var i = 0; i < arr.length; i++) {
			if (arr[i].flagNumValueConfig == undefined) {
				continue;
			}
			if (obj.flagNumber) {
				var num = this._getNumValueTarget({idTarget : arr[i].id});
				data[arr[i].id] = num;

			} else {
				data[arr[i].id] = arr[i].value;
			}
		}

		return data;
	},

	/**
	 *
	*/
	_setVarsValueCalcPrev : function(obj)
	{
		var idTarget = 'JsonDetail';
		var num = this._varsKeyNum[idTarget];
		var arr = this._varsDetailConfig[num].varsFormSensitive.varsDetail;
		var data = {};
		for (var i = 0; i < arr.length; i++) {
			if (arr[i].flagNumValueConfig == undefined) {
				continue;
			}
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : arr[i].id,
				strKey   : 'value',
				vars     : obj.varsValue[arr[i].id]
			});
		}
	},

	/**
	 *
	*/
	_checkVarsCalcDep : function()
	{
		var varsValuePrev = this._getVarsValueCalc({flagNumber : 0});
		this._updateVarsCalcDep();
		var varsValue = this._getVarsValueCalc({flagNumber : 1});

		var varsStampDrop = this._varsSensitive.ins.getVarsTarget({idTarget : 'StampDrop'});

		var flag = '';
		if (varsValue.NumValueNetClosing < 0) {
			flag = 'strNumValueNetClosing';

		} else if (varsValue.NumValueNetClosing < varsValue.NumValueRemainingBook) {
			if (varsStampDrop.value == '') {
				flag = 'strNumValueRemainingBook';
			}
		}

		this._setVarsValueCalcPrev({
			varsValue : varsValuePrev
		});

		return flag;

	},

	/**
	 *
	*/
	_updateVarsCalcDep : function(obj)
	{
		var varsFlagDepMethod = this._varsSensitive.ins.getVarsTarget({idTarget : 'FlagDepMethod'});
		var varsValue = this._getVarsValueCalc({flagNumber : 1});
		this._updateVarsCalcDepNumValueDepCalcBase({
			varsValue     : varsValue,
			flagDepMethod : varsFlagDepMethod.value
		});
		this._updateVarsCalcDepNumValueDepLimit({
			varsValue     : varsValue,
			flag          : (obj)? obj.flag : '',
			flagDepMethod : varsFlagDepMethod.value
		});
		this._updateVarsCalcDepNumValueAccumulatedClosing({
			varsValue     : varsValue,
			flagDepMethod : varsFlagDepMethod.value
		});
		this._updateVarsCalcDepNumValueNetClosing({
			varsValue     : varsValue,
			flagDepMethod : varsFlagDepMethod.value
		});
		this._updateVarsCalcDepNumValueDepNextOver({
			varsValue     : varsValue,
			flagDepMethod : varsFlagDepMethod.value
		});
		this._updateVarsCalcDepNumValueDepSpecialShortNext({
			varsValue     : varsValue,
			flagDepMethod : varsFlagDepMethod.value
		});

	},

	/**
	 *
	*/
	_updateVarsCalcDepNumValueDepCalcBase : function(obj)
	{
		if (obj.flagDepMethod == 'declining') {
			if (this._getFlag20070331()) {
				var numValueNetOpeningTax = obj.varsValue.NumValueNetOpening + obj.varsValue.NumValueDepPrevOver;
				var numSurvivalRateLimit = this._getNumValueMathCalc({
					flagCalc : 'flagFractionDepSurvivalRateLimit',
					num      : obj.varsValue.NumValueNet * (obj.varsValue.NumSurvivalRateLimit /100)
				});

				var num = obj.varsValue.NumValueNetOpening
						+ obj.varsValue.NumValueDepPrevOver
						- obj.varsValue.NumValueDepSpecialShortPrev;

				if (this._getFlag20070401f1() && numValueNetOpeningTax <= numSurvivalRateLimit) {
					num = numSurvivalRateLimit;
				}

				this._varsSensitive.ins.updateVarsTarget({
					idTarget : 'NumValueDepCalcBase',
					strKey   : 'value',
					vars     : num
				});

			} else {
				var num = obj.varsValue.NumValueNetOpening
				+ obj.varsValue.NumValueDepPrevOver
				- obj.varsValue.NumValueDepSpecialShortPrev;

				this._varsSensitive.ins.updateVarsTarget({
					idTarget : 'NumValueDepCalcBase',
					strKey   : 'value',
					vars     : num
				});
			}

		} else if (obj.flagDepMethod == 'straight') {
			if (this._getFlag20070331()) {
				var numValueNetOpeningTax = obj.varsValue.NumValueNetOpening + obj.varsValue.NumValueDepPrevOver;
				var numSurvivalRate = this._getNumValueMathCalc({
					flagCalc : 'flagFractionDepSurvivalRate',
					num      : obj.varsValue.NumValueNet * (obj.varsValue.NumSurvivalRate /100)
				});

				var numSurvivalRateLimit = this._getNumValueMathCalc({
					flagCalc : 'flagFractionDepSurvivalRateLimit',
					num      : obj.varsValue.NumValueNet * (obj.varsValue.NumSurvivalRateLimit /100)
				});

				var num = obj.varsValue.NumValueNet - numSurvivalRate;
				if (this._getFlag20070401f1() && numValueNetOpeningTax <= numSurvivalRateLimit) {
					num = numSurvivalRateLimit;
				}

				this._varsSensitive.ins.updateVarsTarget({
					idTarget : 'NumValueDepCalcBase',
					strKey   : 'value',
					vars     : num
				});

			} else {
				this._varsSensitive.ins.updateVarsTarget({
					idTarget : 'NumValueDepCalcBase',
					strKey   : 'value',
					vars     : obj.varsValue.NumValueNet
				});
			}


		} else if (obj.flagDepMethod == 'average'
			|| obj.flagDepMethod == 'one'
		) {
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : 'NumValueDepCalcBase',
				strKey   : 'value',
				vars     : obj.varsValue.NumValueNet
			});

		}
	},

	/**
	 *
	*/
	_updateVarsCalcDepNumValueDepLimit : function(obj)
	{
		var arr = ['NumValueDepCalc', 'NumValueDepUp', 'NumValueDepExtra', 'NumValueDepSpecial', 'NumValueDepSpecialShortPrev'];
		var sum = 0;
		for (var i = 0; i < arr.length; i++) {
			sum += obj.varsValue[arr[i]];
		}

		if (!(obj.flagDepMethod == 'sum' || obj.flagDepMethod == 'voluntary' || obj.flagDepMethod == 'noneDep')) {
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : 'NumValueDepLimit',
				strKey   : 'value',
				vars     : sum
			});
			if (obj.flag != 'NumValueDep') {
				this._varsSensitive.ins.updateVarsTarget({
					idTarget : 'NumValueDep',
					strKey   : 'value',
					vars     : sum
				});
			}
		}
	},

	/**
	 *
	*/
	_updateVarsCalcDepNumValueNetClosing : function(obj)
	{
		var numValueDep = this._getNumValueTarget({idTarget : 'NumValueDep'});
		var varsStampDrop = this._varsSensitive.ins.getVarsTarget({idTarget : 'StampDrop'});
		var num = obj.varsValue.NumValueNetOpening - numValueDep;
		if (varsStampDrop.value != '') {
			num = 0;
		}

		if (!(obj.flagDepMethod == 'sum')) {
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : 'NumValueNetClosing',
				strKey   : 'value',
				vars     : num
			});
		}

	},

	/**
	 *
	*/
	_updateVarsCalcDepNumValueAccumulatedClosing : function(obj)
	{
		var numValueDep = this._getNumValueTarget({idTarget : 'NumValueDep'});
		var num = obj.varsValue.NumValueAccumulated + numValueDep;

		if (!(obj.flagDepMethod == 'sum')) {
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : 'NumValueAccumulatedClosing',
				strKey   : 'value',
				vars     : num
			});
		}
	},

	/**
	 *
	*/
	_updateVarsCalcDepNumValueDepNextOver : function(obj)
	{
		var numValueDepPrevOver = this._getNumValueTarget({idTarget : 'NumValueDepPrevOver'});
		var numValueDepLimit = this._getNumValueTarget({idTarget : 'NumValueDepLimit'});
		var numValueDep = this._getNumValueTarget({idTarget : 'NumValueDep'});

		if (obj.flagDepMethod == 'straight'
			|| obj.flagDepMethod == 'declining'
			|| obj.flagDepMethod == 'average'
			|| obj.flagDepMethod == 'one'
		) {
			var numValueDepCurrentOver = numValueDep - numValueDepLimit;
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : 'NumValueDepCurrentOver',
				strKey   : 'value',
				vars     : numValueDepCurrentOver
			});

			var numValueDepNextOver = numValueDepPrevOver + numValueDepCurrentOver;
			if (numValueDepNextOver < 0) {
				numValueDepNextOver = 0;
			}

			this._varsSensitive.ins.updateVarsTarget({
				idTarget : 'NumValueDepNextOver',
				strKey   : 'value',
				vars     : numValueDepNextOver
			});
		}

	},

	/**
	 *
	*/
	_updateVarsCalcDepNumValueDepSpecialShortNext : function(obj)
	{
		var numValueDepLimit = this._getNumValueTarget({idTarget : 'NumValueDepLimit'});
		var numValueDep = this._getNumValueTarget({idTarget : 'NumValueDep'});

		var numValueDepCalc = this._getNumValueTarget({idTarget : 'NumValueDepCalc'});
		var numValueDepUp = this._getNumValueTarget({idTarget : 'NumValueDepUp'});

		if (obj.flagDepMethod == 'straight'
			|| obj.flagDepMethod == 'declining'
			|| obj.flagDepMethod == 'average'
			|| obj.flagDepMethod == 'one'
		) {
			var sumValueDepLaw = numValueDepLimit - numValueDepCalc - numValueDepUp;

			var numValueDepCurrentOver = numValueDep - numValueDepLimit;
			var numValueDepSpecialShortCurrent = 0;
			if (numValueDepCurrentOver < 0 && sumValueDepLaw > 0) {
				if (Math.abs(numValueDepCurrentOver) < Math.abs(sumValueDepLaw)) {
					numValueDepSpecialShortCurrent = Math.abs(numValueDepCurrentOver);
				} else {
					numValueDepSpecialShortCurrent = Math.abs(sumValueDepLaw);
				}
			}
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : 'NumValueDepSpecialShortCurrent',
				strKey   : 'value',
				vars     : numValueDepSpecialShortCurrent
			});

			this._resetVarsDetailConfigSensitive({arr : ['NumValueDepSpecialShortCurrentCut']});

			var numValueDepSpecialShortNext = numValueDepSpecialShortCurrent;
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : 'NumValueDepSpecialShortNext',
				strKey   : 'value',
				vars     : numValueDepSpecialShortNext
			});
		}
	},

	/**
	 *
	*/
	_updateVarsNumValueCompression : function(obj)
	{
		if (obj.strError) {
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
			return;
		}

		obj = this._updateNumValueNumber(obj);

		var flagCalc = this._checkVarsCalc();
		if (flagCalc) {
			this._setViewError({
				idTarget   : obj.vars.idError,
				strComment : obj.vars.varsError[flagCalc]
			});
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
			return;
		}
		this._updateVarsCalc();
		if (obj.vars.value != obj.varsPrev.value) {
			this._resetVarsNumValueDep();
			this._updateVarsCalcDep();
		}

		var varsNumValueNet = this._varsSensitive.ins.getVarsTarget({idTarget : 'NumValueNet'});
		var varsFlagDepMethod = this._varsSensitive.ins.getVarsTarget({idTarget : 'FlagDepMethod'});
		if (varsNumValueNet.value >= this.insCurrent.vars.varsItem.numValueNetSumLimit
			&& varsFlagDepMethod.value == 'sum'
		) {
			this._setViewError({
				idTarget   : obj.vars.idError,
				strComment : obj.vars.varsError.strSumLawOver
			});
		}
	},

	/**
	 *
	*/
	_updateVarsNumValueAccumulated : function(obj)
	{
		if (obj.strError) {
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
			return;
		}

		obj = this._updateNumValueNumber(obj);

		var flagCalc = this._checkVarsCalc();
		if (flagCalc) {
			this._setViewError({
				idTarget   : obj.vars.idError,
				strComment : obj.vars.varsError[flagCalc]
			});
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
			return;
		}
		this._updateVarsCalc();
		if (obj.vars.value != obj.varsPrev.value) {
			this._resetVarsNumValueDep();
			this._updateVarsCalcDep();
		}
	},

	/**
	 *
	*/
	_updateVarsNumValueRemainingBook : function(obj)
	{
		if (obj.strError) {
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
			return;
		}

		obj = this._updateNumValueNumber(obj);

		var numValue = this._getNumValueTarget({idTarget : 'NumValue'});
		if (numValue) {
			if (obj.vars.value > numValue) {
				this._setViewError({
					idTarget   : obj.vars.idError,
					strComment : obj.vars.varsError.strNumValueRemainingBook
				});
				this._varsSensitive.ins.updateVarsTarget({
					idTarget : obj.vars.id,
					strKey   : 'value',
					vars     : obj.varsPrev.value
				});
			}
			if (obj.vars.value != obj.varsPrev.value) {
				this._resetVarsNumValueDep();
				this._updateVarsCalcDep();
			}
		}
	},

	/**
	 *
	*/
	_updateViewNumSurvivalRate : function(obj)
	{
		var insDisplayComma = new Code_Lib_DisplayComma();

		var varsNumValueNet = this._varsSensitive.ins.getVarsTarget({idTarget : 'NumValueNet'});
		if (!varsNumValueNet.value) {
			obj.ele.innerHTML = obj.vars.value + '%';
		} else {
			var num = this._getNumValueMathCalc({
				flagCalc : 'flagFractionDepSurvivalRate',
				num      : varsNumValueNet.value * (obj.vars.value /100)
			});
			num = insDisplayComma.get({
				num : num
			});
			obj.ele.innerHTML = obj.vars.value + '% (' + num +  ')';
		}
	},

	/**
	 *
	*/
	_getNumValueMathCalc : function(obj)
	{
		var flagCalc = this.insCurrent.vars.varsItem.varsCalc[obj.flagCalc];

		if (flagCalc == 'floor') {
			return Math.floor(obj.num);

		} else if (flagCalc == 'round') {
			return Math.round(obj.num);

		} else if (flagCalc == 'ceil') {
			return Math.ceil(obj.num);
		}

		return obj.num;
	},

	/**
	 *
	*/
	_updateViewNumSurvivalRateLimit : function(obj)
	{
		var insDisplayComma = new Code_Lib_DisplayComma();

		var varsNumValueNet = this._varsSensitive.ins.getVarsTarget({idTarget : 'NumValueNet'});
		if (!varsNumValueNet.value) {
			obj.ele.innerHTML = obj.vars.value + '%';

		} else {
			var num = this._getNumValueMathCalc({
				flagCalc : 'flagFractionDepSurvivalRateLimit',
				num      : varsNumValueNet.value * (obj.vars.value /100)
			});
			num = insDisplayComma.get({
				num : num
			});
			obj.ele.innerHTML = obj.vars.value + '% (' + num +  ')';
		}
	},

	/**
	 *
	*/
	_getNumValueTarget : function(obj)
	{
		var vars = this._varsSensitive.ins.getVarsTarget({idTarget : obj.idTarget});
		var num = (!vars.value)? 0 : parseFloat(vars.value);

		return num;
	},

	/**
	 *
	*/
	_getFlag20070331 : function()
	{
		var insEscape = new Code_Lib_Escape();

		var varsStampStart = this._varsSensitive.ins.getVarsTarget({idTarget : 'StampStart'});
		var stampStart = insEscape.toStampFromTerm({
			data        : varsStampStart.value,
			insTimeZone : this.insRoot.insTimeZone
		});
		if (stampStart < this.insCurrent.vars.varsItem.varsStamp.flagDepMethod) {
			return 1;
		}
	},

	/**
	 *
	*/
	_getFlag20070401f1 : function()
	{
		if (this.insCurrent.vars.varsItem.varsStampTerm.stampMin >= this.insCurrent.vars.varsItem.varsStamp.flagDepMethod) {
			return 1;
		}
	},

	/**
	 *
	*/
	_updateVarsDepCalc : function(obj)
	{
		if (obj.strError) {
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
			return;
		}

		obj = this._updateNumValueNumber(obj);

		var flag = this._checkVarsCalcDep();
		if (flag) {
			this._setViewError({
				idTarget   : obj.vars.idError,
				strComment : obj.vars.varsError[flag]
			});
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
		}
		this._updateVarsCalcDep({flag : obj.flag});
	},

	/**
	 *
	 */
	_updateViewArrCommaDepMonth : function(obj)
	{
		var str = '';
		if (obj.vars.value == '') {
			str = 0;

		} else {
			obj.vars.value.match(/^,(.*?),$/);
			var arrStr = RegExp.$1.split(',');
			str = arrStr.length;
		}

		var varsNumValueDepCalc = this._varsSensitive.ins.getVarsTarget({idTarget : 'NumValueDepCalc'});
		if (str) {
			obj.ele.innerHTML = str + '/' + this.insCurrent.vars.varsItem.numFiscalTermMonth;

		} else {
			if (varsNumValueDepCalc.value === 0) {
				obj.ele.innerHTML = 0 + '/' + this.insCurrent.vars.varsItem.numFiscalTermMonth;

			} else if (varsNumValueDepCalc.value == '') {
				obj.ele.innerHTML = '';

			} else {
				obj.ele.innerHTML = str + '/' + this.insCurrent.vars.varsItem.numFiscalTermMonth;
			}
		}

	},

	/**
	 *
	*/
	_updateVarsNumValueDepPrevOver : function(obj)
	{
		if (obj.strError) {
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
			return;
		}

		obj = this._updateNumValueNumber(obj);

		var numValueNet = this._getNumValueTarget({idTarget : 'NumValueNet'});
		var numValueNetOpening = this._getNumValueTarget({idTarget : 'NumValueNetOpening'});
		var numValueDepPrevOver = (!obj.vars.value)? 0 : parseFloat(obj.vars.value);

		var numValueNetOpeningTax = numValueNetOpening + numValueDepPrevOver;

		var numValueDepLimit = this._getNumValueTarget({idTarget : 'NumValueDepLimit'});
		var numValueRemainingBook = this._getNumValueTarget({idTarget : 'NumValueRemainingBook'});

		var numValueNetClosingTax = numValueNetOpening + numValueDepPrevOver - numValueDepLimit;
		var flag = '';
		if (numValueNetOpeningTax > numValueNet) {
			flag = 'strNumValueNet';

		} else if (numValueNetClosingTax < 0) {
			flag = 'strNumValueNetClosingTax';

		} else if (numValueNetClosingTax < numValueRemainingBook) {
			flag = 'strNumValueRemainingBookTax';
		}

		if (flag) {
			this._setViewError({
				idTarget   : obj.vars.idError,
				strComment : obj.vars.varsError[flag]
			});
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
			return;
		}

		this._varsSensitive.ins.updateVarsTarget({
			idTarget : 'NumValueDepPrevOverData',
			strKey   : 'value',
			vars     : obj.vars.value
		});

		if (obj.varsPrev.value != obj.vars.value) {
			this._resetVarsNumValueDepCalc();
		}

		this._updateVarsCalcDep();
	},

	/**
	 *
	*/
	_updateVarsNumValueDepCalc : function(obj)
	{
		this._updateVarsDepCalc(obj);
	},

	/**
	 *
	*/
	_updateVarsNumValueDepUp : function(obj)
	{
		this._updateVarsDepCalc(obj);
	},

	/**
	 *
	*/
	_updateVarsNumValueDepExtra : function(obj)
	{
		this._updateVarsDepCalc(obj);
	},

	/**
	 *
	*/
	_updateVarsNumValueDepSpecial : function(obj)
	{
		this._updateVarsDepCalc(obj);
	},

	/**
	 *
	*/
	_updateVarsNumValueDepSpecialShortPrev : function(obj)
	{
		if (obj.strError) {
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
			return;
		}

		obj = this._updateNumValueNumber(obj);

		this._varsSensitive.ins.updateVarsTarget({
			idTarget : 'NumValueDepSpecialShortPrevData',
			strKey   : 'value',
			vars     : obj.vars.value
		});

		var flag = this._checkVarsCalcDep();
		if (flag) {
			this._setViewError({
				idTarget   : obj.vars.idError,
				strComment : obj.vars.varsError[flag]
			});
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : 'NumValueDepSpecialShortPrevData',
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
			return;
		}
		if (obj.varsPrev.value != obj.vars.value) {
			var varsFlagDepMethod = this._varsSensitive.ins.getVarsTarget({idTarget : 'FlagDepMethod'});
			if (varsFlagDepMethod.value == 'declining') {
				this._resetVarsNumValueDepCalc();
			}
		}
		this._updateVarsCalcDep();
	},

	/**
	 *
	*/
	_updateVarsNumValueDep : function(obj)
	{
		if (obj.strError) {
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
			return;
		}

		obj = this._updateNumValueNumber(obj);

		var flag = this._checkVarsCalcDep();
		if (flag) {
			this._setViewError({
				idTarget   : obj.vars.idError,
				strComment : obj.vars.varsError[flag]
			});
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
		}

		var numValueDep = obj.vars.value;
		var numValueNetOpening = this._getNumValueTarget({idTarget : 'NumValueNetOpening'});
		var numValueRemainingBook = this._getNumValueTarget({idTarget : 'NumValueRemainingBook'});

		var varsStampDrop = this._varsSensitive.ins.getVarsTarget({idTarget : 'StampDrop'});
		var numValueNetClosing = numValueNetOpening - numValueDep;
		flag = '';
		if (numValueNetClosing < 0) {
			flag = 'strNumValueNetClosing';

		} else if (numValueNetClosing < numValueRemainingBook) {
			if (varsStampDrop.value == '') {
				flag = 'strNumValueRemainingBook';
			}
		}

		if (flag) {
			this._setViewError({
				idTarget   : obj.vars.idError,
				strComment : obj.vars.varsError[flag]
			});
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
			return;
		}
		this._updateVarsCalcDep({flag : 'NumValueDep'});
	},

	/**
	 *
	*/
	_updateVarsNumValueDepSpecialShortCurrentCut : function(obj)
	{
		if (obj.strError) {
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
			return;
		}

		obj = this._updateNumValueNumber(obj);

		var numValueDepSpecialShortCurrent = this._getNumValueTarget({idTarget : 'NumValueDepSpecialShortCurrent'});
		var numValueDepSpecialShortCurrentCut = (!obj.vars.value)? 0 : parseFloat(obj.vars.value);

		var numValueDepSpecialShortNext = numValueDepSpecialShortCurrent - numValueDepSpecialShortCurrentCut;
		if (numValueDepSpecialShortNext < 0) {
			this._setViewError({
				idTarget   : obj.vars.idError,
				strComment : obj.vars.varsError.strNumValueDepSpecialShortCurrent
			});
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
			return;
		}
		this._varsSensitive.ins.updateVarsTarget({
			idTarget : 'NumValueDepSpecialShortNext',
			strKey   : 'value',
			vars     : numValueDepSpecialShortNext
		});
	},

	/**
	 *
	*/
	_checkNumSurvivalRateNumSurvivalRateLimit : function()
	{
		var varsNumSurvivalRate = this._varsSensitive.ins.getVarsTarget({idTarget : 'NumSurvivalRate'});
		var varsNumSurvivalRateLimit = this._varsSensitive.ins.getVarsTarget({idTarget : 'NumSurvivalRateLimit'});
		if (varsNumSurvivalRate.value < varsNumSurvivalRateLimit.value) {
			return 1;
		}
	},

	/**
	 *
	*/
	_updateVarsNumSurvivalRate : function(obj)
	{
		var varsNumSurvivalRateLimit = this._varsSensitive.ins.getVarsTarget({idTarget : 'NumSurvivalRateLimit'});
		if (this._checkNumSurvivalRateNumSurvivalRateLimit()) {
			this.insDetail.showFormAttestError({flagType : 'numSurvivalRateLimit'});
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : varsNumSurvivalRateLimit.value
			});
		}
	},



	/**
	 *
	*/
	_updateVarsNumSurvivalRateLimit : function(obj)
	{
		var varsNumSurvivalRate = this._varsSensitive.ins.getVarsTarget({idTarget : 'NumSurvivalRate'});
		if (this._checkNumSurvivalRateNumSurvivalRateLimit()) {
			this.insDetail.showFormAttestError({flagType : 'numSurvivalRateLimit'});
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : varsNumSurvivalRate.value
			});
		}
	},
	/**
	 *
	*/
	_updateVarsNumRatio : function(obj)
	{
		if (obj.vars.value == '') {
			this._resetVarsDetailConfigSensitive({arr : [obj.vars.id]});

		} else {
			if (!obj.vars.value.match(/^[0-9]{1,3}\.[0-9]{2,2}$/)) {
				this._setViewError({
					idTarget   : obj.vars.idError,
					strComment : obj.vars.varsError.strFormat
				});
				this._varsSensitive.ins.updateVarsTarget({
					idTarget : obj.vars.id,
					strKey   : 'value',
					vars     : obj.varsPrev.value
				});
			}
		}
	},

	_updateVarsNumRatioSellingAdminCost : function(obj)
	{
		this._updateVarsNumRatio(obj);
	},

	_updateVarsNumRatioProductsCost : function(obj)
	{
		this._updateVarsNumRatio(obj);
	},

	_updateVarsNumRatioNonOperatingExpenses : function(obj)
	{
		this._updateVarsNumRatio(obj);
	},

	_updateVarsNumRatioAgricultureCost : function(obj)
	{
		this._updateVarsNumRatio(obj);
	},

	_updateViewFlagFraction : function(obj)
	{
		var insEscape = new Code_Lib_Escape();

		var id = insEscape.strLowCapitalize({data : obj.vars.id});
		obj.ele.innerHTML = this.insCurrent.vars.varsItem.varsOptions[id].arrStrTitle[obj.vars.value];
	},

	/**
	 *
	 */
	_updateViewNum : function(obj)
	{
		var insDisplayComma = new Code_Lib_DisplayComma();

		if (obj.vars.value === 0) {
			obj.ele.innerHTML = 0;

		} else if (obj.vars.value == '') {
			obj.ele.innerHTML = '';

		} else {
			var num = insDisplayComma.get({
				num : obj.vars.value
			});
			obj.ele.innerHTML = num;
		}

	},

	/**
	 *
	*/
	_resetVarsDetailConfigSensitive : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var vars = this._getVarsDetailConfigSensitive({idTarget : obj.arr[i]});
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : vars.id,
				strKey   : 'value',
				vars     : vars.value
			});
		}
	},

	_updateViewLossOnDisposalOfFixedAssets : function(obj)
	{
		this._updateViewIdAccountTitle(obj);
	},

	_updateViewAccumulatedDepreciation : function(obj)
	{
		this._updateViewIdAccountTitle(obj);
	},

	_updateViewSellingAdminCost : function(obj)
	{
		this._updateViewIdAccountTitle(obj);
	},

	_updateViewProductsCost : function(obj)
	{
		this._updateViewIdAccountTitle(obj);
	},

	_updateViewNonOperatingExpenses : function(obj)
	{
		this._updateViewIdAccountTitle(obj);
	},

	_updateViewAgricultureCost : function(obj)
	{
		this._updateViewIdAccountTitle(obj);
	},

	_updateViewNumRatioSellingAdminCost : function(obj)
	{
		var varsSellingAdminCost = this._varsSensitive.ins.getVarsTarget({idTarget : 'SellingAdminCost'});
		if (varsSellingAdminCost.value == 'none') {
			obj.ele.innerHTML = '';

		} else {
			obj.ele.innerHTML = obj.vars.value;
		}
	},

	_updateViewNumRatioProductsCost : function(obj)
	{
		var varsProductsCost = this._varsSensitive.ins.getVarsTarget({idTarget : 'ProductsCost'});
		if (varsProductsCost.value == 'none') {
			obj.ele.innerHTML = '';

		} else {
			obj.ele.innerHTML = obj.vars.value;
		}
	},

	_updateViewNumRatioNonOperatingExpenses : function(obj)
	{
		var varsNonOperatingExpenses = this._varsSensitive.ins.getVarsTarget({idTarget : 'NonOperatingExpenses'});
		if (varsNonOperatingExpenses.value == 'none') {
			obj.ele.innerHTML = '';

		} else {
			obj.ele.innerHTML = obj.vars.value;
		}
	},

	_updateViewNumRatioAgricultureCost : function(obj)
	{
		var varsAgricultureCost = this._varsSensitive.ins.getVarsTarget({idTarget : 'AgricultureCost'});
		if (varsAgricultureCost.value == 'none') {
			obj.ele.innerHTML = '';

		} else {
			obj.ele.innerHTML = obj.vars.value;
		}
	},

	/**
	 *
	*/
	_updateViewElse : function(obj)
	{
		if (obj.vars.id.match(/^NumValue/)) {
			this._updateViewNum(obj);
			return;
		}

		if (obj.vars.value == 'none') {
			obj.ele.innerHTML = '';

		} else {
			obj.ele.innerHTML = obj.vars.value;
		}

	},

	/**
	 *
	*/
	_updateVarsElse : function(obj)
	{
		if (obj.strError) {
			this._resetVarsDetailConfigSensitive({arr : [obj.vars.id]});
		}
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
	_setDetailContentValue : function()
	{
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
				insCurrent._setDetailContentValue();
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

					} else if (obj.vars.vars.vars.idTarget == 'folder') {
						insCurrent.insDetail.setValue();
						insCurrent._setDetailContentValue();
						var varsFormat = insCurrent._getDetailFormFormat();
						insCurrent.insNavi.addVars({vars : {vars : varsFormat, strTitle : varsFormat.StrTitle}});
						insCurrent.insDetail.showBtnBottom();
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
			if (arr[i].id == 'JsonDetail') {
				vars[arr[i].id] = this._getVarsSensitiveValue();
			}
		}

		return vars;
	},

	/**
	 *
	*/
	_eventDetailConnect : function(obj)
	{
		var flagJsonDetailReset = 0;
		var insEscape = new Code_Lib_Escape();

		if (obj.flag == 'reload') {
			if (obj.flagType == 'start') {
				if (this.varsChild.varsIni) {
					this.vars.portal.varsDetail.varsDetail = this.varsChild.varsIni;
				} else {
					this.vars.portal.varsDetail.varsDetail = this.varsChild.varsDetail;
				}

			} else {
				this.vars.portal.varsDetail.varsDetail = this.varsChild.varsDetail;
			}
			this._setDetailStart();
			return;

		} else if (obj.flag == 'add'
			|| obj.flag == 'edit'
			|| obj.flag == 'calc'
		) {
			if (this.insDetail.checkForm({flagType : 'common'})) return;
			this.insDetail.setValue();
			this._setDetailContentValue();
			var varsFormat = this._getDetailFormFormat();
			var data = {vars : varsFormat, strTitle : varsFormat.StrTitle};
			obj.varsFormat = data;

			var vars = this.insDetail.getFormValue();
			vars.JsonDetail = this._getVarsSensitiveValue();

			var flag = this._checkDetailValueJsonDetail({
				vars : vars.JsonDetail
			});
			if (flag) {
				this.insDetail.showFormAttestError({flagType : 'strBlank'});
				return;
			}

			if (obj.flag == 'calc') {
				if (vars.JsonDetail.FlagDepMethod.match(/^(sum|noneDep)$/)) {
					this.insDetail.showFormAttestError({flagType : 'strCalcNone'});
					return;
				}
			}

			this._eventValue({
				vars     : vars,
				idTarget : (obj.flag == 'edit')? this.varsChild.idTarget : ''
			});

		} else if (!obj.flag.match(/^format(.*?)-save$/)) {
			obj.arr = (Object.toJSON(this.varsChild.varsDetail)).evalJSON();
			for (var i = 0; i < obj.arr.length; i++) {
				if (!obj.vars[obj.arr[i].id] || obj.arr[i].flagDisabled) continue;
				if (obj.arr[i].id == 'JsonDetail') {
					obj.arr[i].value = 'dummy';
					flagJsonDetailReset = this._checkDetailJsonDetail({
						vars : obj.vars[obj.arr[i].id]
					});
					if (!flagJsonDetailReset) {
						this._setDetailJsonDetail({
							vars : obj.vars[obj.arr[i].id]
						});
						this._getDetailFormSensitiveVars({arr : obj.arr});
					}

				} else {
					if (obj.vars[obj.arr[i].id] != undefined) {
						obj.arr[i].value = obj.vars[obj.arr[i].id];
					}
				}
			}
			this.vars.portal.varsDetail.varsDetail = obj.arr;
			this._eventRemoveDetailContent();
			this._setDetailStart();
			if (flagJsonDetailReset) {
				this._resetVarsSensitive();
				this.insDetail.showFormAttestError({flagType : 'strOld'});
			}
			return;
		}

		this._varsDetailConnect = obj;
		this._sendDetailConnect();
	},

	/**
	 *
	*/
	_checkDetailValueJsonDetail : function(obj)
	{
		var idTarget = 'JsonDetail';
		var num = this._varsKeyNum[idTarget];
		var arr = this._varsDetailConfig[num].varsFormSensitive.varsDetail;
		for (var i = 0; i < arr.length; i++) {
			if (!arr[i].flagMustUse) {
				continue;
			}
			if (obj.vars[arr[i].id] == arr[i].value) {
				return arr[i].id;
			}
		}
	},

	/**
	 *
	*/
	_checkDetailJsonDetail : function(obj)
	{
		var idTarget = 'JsonDetail';
		var num = this._varsKeyNum[idTarget];
		var arr = this._varsDetailConfig[num].varsFormSensitive.varsDetail;
		for (var i = 0; i < arr.length; i++) {
			if (!arr[i].flagSaveCheck) {
				continue;
			}
			if (obj.vars[arr[i].id] == undefined) {
				continue;
			}
			if (obj.vars[arr[i].id] == arr[i].value) {
				continue;
			}

			if (arr[i].id =='IdDepartment') {
				if (!this.insCurrent.vars.varsItem.arrDepartment.arrStrTitle[obj.vars[arr[i].id]]) {
					return arr[i].id;
				}

			} else {
				if (!this.insCurrent.vars.varsItem.arrAccountTitle.arrStrTitle[obj.vars[arr[i].id]]) {
					return arr[i].id;
				}
			}
		}

		if (obj.vars.ArrCommaDepMonth != '') {
			obj.vars.ArrCommaDepMonth.match(/^,(.*?),$/);
			var arrStrMonth = RegExp.$1.split(',');
			var numMonths = arrStrMonth.length;

			if (numMonths > this.insCurrent.vars.varsItem.numFiscalTermMonth) {
				return 'dummy1';
			}

			var arrCheck = {};
			var numStart = this.insCurrent.vars.varsItem.numFiscalBeginningMonth;
			var numMonth = numStart;
			var numEnd = numStart + this.insCurrent.vars.varsItem.numFiscalTermMonth;
			for (var i = numStart; i < numEnd; i++) {
				arrCheck[numMonth] = 1;
				numMonth++;
				if (numMonth > 12) {
					numMonth -= 12;
				}
			}

			var arr = arrStrMonth;
			for (var i = 0; i < arr.length; i++) {
				var str = arr[i];
				if (!arrCheck[str]) {
					return 'dummy2';
				}
			}
		}

	},

	/**
	 *
	*/
	_setDetailJsonDetail : function(obj)
	{
		var idTarget = 'JsonDetail';
		var num = this._varsKeyNum[idTarget];
		var arr = this._varsDetailConfig[num].varsFormSensitive.varsDetail;
		for (var i = 0; i < arr.length; i++) {
			if (obj.vars[arr[i].id] == undefined) {
				continue;
			}
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : arr[i].id,
				strKey   : 'value',
				vars     : obj.vars[arr[i].id]
			});
		}
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
				this.insCurrent.eventDetailConnectSuccessListDetailUpdate(obj);
				this._setDetailEnd();
			}
			this.insNavi.addLog({vars : this._varsDetailConnect.varsFormat});
			this.insNavi.eventBtnSave();

		} else if (obj.json.flag == 40) {
			this.insCurrent.eventDetailConnectSuccessListUpdateDetailReset(obj);
			alert(this.insRoot.vars.varsSystem.str.oldData);
			if (!this.insWindow.vars.flagHideNow) this.insWindow.updateHide({ flagEffect : 1 });

		} else if (obj.json.flag == 42) {
			alert(this.insRoot.vars.varsSystem.str.errorMail);

		} else if (obj.json.flag == 8) {
			alert(this.insRoot.vars.varsSystem.str.oldData);

		} else {
			if (obj.json.flag == 'strCheck') {
				this._resetVarsSensitive();

			} else if (obj.json.flag == 'calc') {
				this._setVarsCalc({vars : obj.json.data.varsDetail});
				this.insDetail.showBtnBottom();
				return;
			}
			this.insDetail.showFormAttestError({flagType : obj.json.flag});

		}
	},

	/**
	 *
	*/
	_setVarsCalc : function(obj)
	{
		var insEscape = new Code_Lib_Escape();

		var idTarget = 'JsonDetail';
		var num = this._varsKeyNum[idTarget];
		var arr = this._varsDetailConfig[num].varsFormSensitive.varsDetail;

		for (var i = 0; i < arr.length; i++) {
			if (!arr[i].flagCalcUse) {
				continue;
			}
			var id = insEscape.strLowCapitalize({data : arr[i].id});
			if (obj.vars[id] == undefined) {
				continue;
			}
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : arr[i].id,
				strKey   : 'value',
				vars     : obj.vars[id]
			});
		}

		var id = 'NumValueDepCalc';
		var vars = this._varsSensitive.ins.getVarsTarget({idTarget : id});
		var idLow = insEscape.strLowCapitalize({data : id});
		var varsPrev = (Object.toJSON(vars)).evalJSON();
		vars.value = obj.vars[idLow];
		this._updateVarsDetailSensitive({
			vars     : vars,
			varsPrev : varsPrev,
			strError : ''
		});

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