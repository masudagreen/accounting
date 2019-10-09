{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_LogHouseEditor = Class.create(Code_Lib_ExtEditor,
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

	_flagAutoSearchOver : '',
	_varsAutoSearchOver : {},
	bootAutoSearchOver : function(obj)
	{
		var insEscape = new Code_Lib_Escape();
		this._varsAutoSearchOver = obj.vars;
		this._flagAutoSearchOver = obj.flag;
		if (obj.flag == 'reloadMemo') {
			this.insNavi.setVarsTreePast({vars : obj.vars});

		} else if (obj.flag == 'getMemo') {
			return this.insNavi.getVarsTreePast();
		}
	},

	/**
	 *
	*/
	_iniVars : function(obj)
	{
		this._extVars(obj);
		this.vars.portal.varsDetail.varsDetail = this.varsChild.varsDetail;
		this._varsSearch = this.insCurrent.getVarsSearch();

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
	_eventNaviConnectSuccess : function(obj)
	{
		var insEscape = new Code_Lib_Escape();
		if (obj.json.flag == 1){
			if (this._varsNaviConnect.flag.match(/^folder(.*?)-search$/)) {
				this.insList.resetVars({vars : obj.json.data, numLotNow : this._varsSearch.numLotNow});
				this.insList.eventNavi({strTitle : null, strClass : null});
				if (obj.json.data.numRows) {
					this._eventDetailList({vars : obj.json.data.varsDetail[0]});
				}

			} else if (this._varsNaviConnect.flag.match(/^folder(.*?)-save$/)
				|| this._varsNaviConnect.flag.match(/^format(.*?)-save$/)
			) {
				this.insNavi.updateFolderVars({vars : obj.json.data.varsDetail});

			} else if (this._varsNaviConnect.flag.match(/^folder(.*?)-reload$/)
				|| this._varsNaviConnect.flag.match(/^format(.*?)-reload$/)
			) {
				this.insLayout.updateTool({flagLock : 0, from : 'navi', idTarget : 'Reload'});
				this.insNavi.updateFolderVars({vars : obj.json.data.varsDetail});
				if (obj.json.stamp) {
					var data = (Object.toJSON(obj.json.data.varsDetail)).evalJSON();
					if (obj.json.stamp.id) this._varsStampCheck[obj.json.stamp.id] = data;
				}

			}

		} else if (obj.json.flag == 10) {
			if (this._varsNaviConnect.flag.match(/^folder(.*?)-reload$/)
				|| this._varsNaviConnect.flag.match(/^format(.*?)-reload$/)
				|| this._varsNaviConnect.flag == 'tree-reload'
			) {
				this.insLayout.updateTool({flagLock : 0, from : 'navi', idTarget : 'Reload'});
				if (obj.json.stamp) {
					this.insNavi.updateFolderVars({vars : this._varsStampCheck[obj.json.stamp.id]});
				}

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
	_getDetailFormFormat : function()
	{
		var vars = this.insDetail.getFormValue();
		arr = this.insDetail.insForm.vars.varsDetail;
		for (var i = 0; i < arr.length; i++) {
			if (arr[i].varsFormArea) {
				var data = {};
				data = this._getDetailFormAreaFormat({arr : arr, idTarget : arr[i].id});
				vars[arr[i].id] = data;

			} else if (arr[i].varsFormJournal) {
				var data = {};
				data = this._getDetailFormJournalFormat({arr : arr, idTarget : arr[i].id});
				vars[arr[i].id] = data;
			}
		}

		return vars;

	},

	/**
	 *
	*/
	_getDetailFormAreaFormat : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormArea) continue;
			if (obj.idTarget == obj.arr[i].id) {
				var arr = (Object.toJSON(this._varsDetailFormArea[num].insArea.vars.varsDetail)).evalJSON();
				return this._getDetailFormAreaFormatChild({arr : arr});
			}
			num++;
		}
	},

	/**
	 *
	*/
	_getDetailFormAreaFormatChild : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].vars) {
				if (obj.arr[i].vars.strTitle) {
					obj.arr[i].strTitle = obj.arr[i].vars.strTitle;
					obj.arr[i].vars.strTitle = null;
					obj.arr[i].strClassFont = null;
				}
			}
		}

		return obj.arr;
	},


	/**
	 *
	*/
	_getDetailFormJournalFormat : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormJournal) continue;
			if (obj.idTarget == obj.arr[i].id) {
				return this._varsDetailFormJournal.insFormJournal.vars.varsDetail;
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
	_setDetailStart : function()
	{
		var str = 'strTitle' + this.varsChild.flagType.capitalize();

		this.insDetail.eventList({
			flagMoveUse : 0,
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
		this._iniDetailFormView();
		this._iniDetailFormJournal();
		this._iniDetailFormArea();
	},

	/**
	 *
	*/
	_iniDetailFormView : function()
	{
		var num = this._getDetailFlagPermit({arr : this.insDetail.insForm.vars.varsDetail});
		this._setDetailNumSumMax({
			numMax : num,
			arr    : this.insDetail.insForm.vars.varsDetail
		});
	},

	/**
	 *
	*/
	_setDetailNumSumMax : function(obj)
	{
		this.insDetail.setValue();
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'NumSumMax') {
				var numMax = obj.numMax;
				var arrayNewMax = [];
				for (var j = 0; j < numMax; j++) {
					var data = {};
					var numValue = j + 1;
					var strTitle = numValue + obj.arr[i].varsTmpl.strPerson;
					data.value = numValue;
					data.strTitle = strTitle;
					arrayNewMax.push(data);
				}
				obj.arr[i].arrayOption = arrayNewMax;
				obj.arr[i].value = (parseFloat(obj.arr[i].value) > numMax)? 0 : obj.arr[i].value;
				$(this.insDetail.insForm.idSelf + obj.arr[i].id).innerHTML = '';
				this.insDetail.insForm.setTemplateSelect({
					arr       : obj.arr[i].arrayOption,
					now       : obj.arr[i].value,
					eleInsert : $(this.insDetail.insForm.idSelf + obj.arr[i].id),
					vars      : obj.arr[i]
				});
				if (numMax != 0) {
					this.insDetail.insForm.viewForm({
						idTarget    : obj.arr[i].id,
						flagHideNow : 0
					});

				} else {
					this.insDetail.insForm.viewForm({
						idTarget    : obj.arr[i].id,
						flagHideNow : 1
					});
				}
			}
		}
	},

	/**
	 *
	*/
	_getDetailFlagPermit : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'ArrCommaIdAccountPermit') {
				return obj.arr[i].varsFormArea.varsDetail.length;
			}
		}
	},

	/**
	 *
	*/
	_getDetailFormContentVars : function(obj)
	{
		this._getDetailFormJournalVars(obj);
		this._getDetailFormAreaVars(obj);
	},

	/**
	 *
	*/
	_varsDetailFormJournal : {},
	_iniDetailFormJournal : function()
	{
		this._varsDetailFormJournal = {};
		this._setDetailFormJournal({arr : this.insDetail.insForm.vars.varsDetail});
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
			if (!this.insFormJournal) {
				var varsRule = (Object.toJSON(this.insCurrent.insFormJournal.vars.varsRule)).evalJSON();
				this.insFormJournal = new Code_Plugin_Accounting_Lib_JournalHouse({varsRule : varsRule});
				/*obj.arr[i].varsFormJournal.varsRule = varsRule;*/
			}

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
				var data = insCurrent.insDetail.getFormScrollVars();

				return data;

			} else if (obj.from == 'preIniReload') {
				insCurrent.insDetail.getFormScrollVars();

			} else if (obj.from == 'afterIniReload') {
				insCurrent.insDetail.setFormScrollVars();

			} else if (obj.from == '_mousedownBtnTitle') {
				insCurrent._eventDetailConnect({flag : 'estimate'});
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
			this._varsDetailFormJournal.insFormJournal.stopListener();
			return;
		}
	},

	/**
	 *
	*/
	_setDetailFormJournalValue : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormJournal) continue;
			var str = this._varsDetailFormJournal.insFormJournal.getValue();
			this.insDetail.setFormValue({
				idTarget : obj.arr[i].id,
				value    : str,
			});
			return;
		}
	},

	/**
	 *
	*/
	_getDetailFormJournalVars : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormJournal) continue;
			obj.arr[i].varsFormJournal.varsDetail = this._varsDetailFormJournal.insFormJournal.vars.varsDetail;
		}
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
	_setDetailFormArea : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormArea) continue;
			var ele = this.insDetail.insForm.eleWrap.down('.codeLibFormContent', this._varsContent.num);
			this._varsContent.num++;

			this._updateDetailFormAreaVars({
				arr           : obj.arr[i].varsFormArea.varsDetail,
				varsTmpl      : obj.arr[i].varsFormArea.varsTmpl,
				varsIdAccount : this._checkDetailFormAreaIdAccount()
			});

			var insArea = new Code_Lib_FormArea({
				eleInsert  : ele,
				insRoot    : this.insRoot,
				insCurrent : this,
				idSelf     : this.idSelf + 'FormArea' + obj.arr[i].id,
				allot      : this._getDetailFormAreaAllotPermit(),
				vars       : obj.arr[i].varsFormArea
			});
			this._varsDetailFormArea.push({
				id      : obj.arr[i].id,
				insArea : insArea
			});
		}
	},

	/**
	 *
	*/
	_updateDetailFormAreaVars : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].vars) {
				if (obj.arr[i].vars.strTitle) {
					obj.arr[i].strTitle = obj.arr[i].vars.strTitle;
					obj.arr[i].strClassFont = '';
				}
			}
			obj.arr[i].vars.strTitle = obj.arr[i].strTitle;
			if (obj.varsIdAccount.idAccountApply == obj.arr[i].vars.idTarget) {
				obj.arr[i].strClassFont = obj.varsTmpl.strClassFont;
			}
			if (obj.varsIdAccount.idAccountCharge == obj.arr[i].vars.idTarget) {
				obj.arr[i].strClassFont = obj.varsTmpl.strClassFont;
			}
		}
	},

	/**
	 *
	*/
	_getDetailFormAreaAllotPermit : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			var insParent = insCurrent.insCurrent;

			if (obj.from == '_mousedownBarLink') {
				obj.arr = insParent.vars.portal.varsDetail.templateDetail;

				for (var i = 0; i < obj.arr.length; i++) {
					if (!obj.arr[i].varsFormArea || obj.arr[i].id == 'ArrCommaIdLogFile') continue;
					insCurrent._eventValue({
						vars     : '',
						idTarget : insCurrent.varsChild.vars.idAccount
					});

					insParent.insRoot.insChoice.setBoot({
						flagId       : obj.arr[i].id,
						varsValue    : insCurrent._varsValue,
						idTarget     : obj.arr[i].varsFormArea.varsChoice.idTarget,
						idModule     : obj.arr[i].varsFormArea.varsChoice.idModule,
						flagCheckUse : obj.arr[i].varsFormArea.varsChoice.flagCheckUse,
						strFunc      : 'setDetailFormAreaChoiceValuePermit',
						numTop       : insParent._staticDetailFormArea.numTop + $(insCurrent.insWindow.idWindow).offsetTop,
						numLeft      : insParent._staticDetailFormArea.numLeft + $(insCurrent.insWindow.idWindow).offsetLeft,
						insCurrent   : insCurrent
					});
					break;
				}

				return 1;
			} else if (obj.from == '_mousedownRemove') {
				insCurrent._setDetailNumSumMax({
					numMax : obj.vars.insCurrent.vars.varsDetail.length,
					vars   : obj.vars.insCurrent.vars.varsDetail,
					arr    : insCurrent.insDetail.insForm.vars.varsDetail
				});

			} else if (obj.from == '_mousedownBarRemove') {
				insCurrent._setDetailNumSumMax({
					numMax : 0,
					arr    : insCurrent.insDetail.insForm.vars.varsDetail
				});
				insCurrent.insDetail.showFormAttestError({flagType : 'dummy'});
			}
		};

		return allot;
	},

	/**
	 * {
	 * 	flagId : string
	 * 	vars   : array
	 * }
	*/
	setDetailFormAreaChoiceValuePermit : function(obj)
	{
		if (!obj.vars) return;
		var varsIdAccount = this._checkDetailFormAreaIdAccount();
		this._setDetailFormAreaChoiceValue({
			idTarget      : obj.flagId,
			vars          : obj.vars,
			varsIdAccount : varsIdAccount,
			arr           : this.insDetail.insForm.vars.varsDetail
		});
		this._setDetailNumSumMax({
			numMax : obj.vars.length,
			arr    : this.insDetail.insForm.vars.varsDetail
		});
	},

	/**
	 *
	*/
	_setDetailFormAreaChoiceValueChild : function(obj)
	{
		var array = [];
		var objCheck = {};
		for (var i = 0; i < obj.arr.length; i++) {
			var varsTmpl = (Object.toJSON(obj.insArea.vars.templateDetail)).evalJSON();
			if (obj.arr[i].strClass) {
				varsTmpl.strClass = obj.arr[i].strClass;
			}
			varsTmpl.strTitle = obj.arr[i].strTitle;
			varsTmpl.vars.idTarget = obj.arr[i].vars.idTarget;
			varsTmpl.vars.strTitle = obj.arr[i].strTitle;

			if (obj.varsIdAccount.idAccountApply == obj.arr[i].vars.idTarget) {
				varsTmpl.strClassFont = obj.insArea.vars.varsTmpl.strClassFont;
			}

			if (obj.varsIdAccount.idAccountCharge == obj.arr[i].vars.idTarget) {
				varsTmpl.strClassFont = obj.insArea.vars.varsTmpl.strClassFont;
			}

			array[i] = varsTmpl;
			var str = 'id' + varsTmpl.vars.idTarget;
			objCheck[str] = 1;
		}
		for (var i = 0, j = array.length; i < obj.arrDetail.length; i++) {
			var str = 'id' + obj.arrDetail[i].vars.idTarget;
			if (objCheck[str]) continue;
			array[j] = obj.arrDetail[i];
			j++;
		}

		return array;
	},

	/**
	 *
	*/
	_setDetailFormAreaChoiceValue : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormArea) continue;
			if (obj.idTarget == obj.arr[i].id) {
				var varsDetail = this._setDetailFormAreaChoiceValueChild({
					insArea       : this._varsDetailFormArea[num].insArea,
					arr           : obj.vars,
					varsIdAccount : obj.varsIdAccount,
					arrDetail     : this._varsDetailFormArea[num].insArea.vars.varsDetail
				});
				this._varsDetailFormArea[num].insArea.vars.varsDetail = varsDetail;
				this._varsDetailFormArea[num].insArea.iniReload();
			}
			num++;
		}
	},

	/**
	 *
	*/
	_checkDetailFormAreaIdAccount : function(obj)
	{
		var objData = {};
		objData.idAccountSelf = parseFloat(this.insCurrent.insFormJournal.vars.varsRule.idAccount);
		objData.idAccountCharge = objData.idAccountSelf;
		objData.idAccountApply = objData.idAccountSelf;

		return objData;
	},

	/**
	 *
	*/
	_eventLayoutDetailContent : function()
	{
		if (!this.insDetail.insForm) return;
		this._setDetailContent();
	},

	/**
	 *
	*/
	_preEventLayoutDetailContent : function()
	{
		if (!this.insDetail.insForm) return;
		this._getDetailFormJournalVars({arr : this.insDetail.insForm.vars.varsDetail});
		this._getDetailFormAreaVars({arr : this.insDetail.insForm.vars.varsDetail});
	},

	/**
	 *
	*/
	_eventRemoveDetailContent : function()
	{
		if (!this.insDetail.insForm) return;
		this._eventRemoveDetailFormJournal({arr : this.insDetail.insForm.vars.varsDetail});
		this._eventRemoveDetailFormArea({arr : this.insDetail.insForm.vars.varsDetail});
	},

	/**
	 *
	*/
	_setDetailContentValue : function()
	{
		this._setDetailFormJournalValue({arr : this.insDetail.insForm.vars.varsDetail});
		this._setDetailFormAreaValue({arr : this.insDetail.insForm.vars.varsDetail});
	},

	/**
	 *
	*/
	_backDetailContentValue : function()
	{

	},

	/**
	 *
	*/
	_eventDetailConnect : function(obj)
	{
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

		} else if (obj.flag == 'estimate') {
			this.insDetail.setValue();
			this._setDetailContentValue();
			var vars = this.insDetail.getFormValue();
			if (!parseFloat(this.insTop.vars.flagIdAccountTitle)) {
				if (parseFloat(this.insTop.vars.flagAdmin)) {
					this.insDetail.showFormAttestError({flagType : 'strFlagIdAccountTitleAdmin'});

				} else {
					this.insDetail.showFormAttestError({flagType : 'strFlagIdAccountTitle'});
				}
				return;
			}
			if (vars.StrTitle == '') {
				this.insDetail.showFormAttestError({flagType : 'blank'});
				return;
			}
			var varsPrev = this._varsDetailFormJournal.insFormJournal.getVarsBtnTitleValue();
			if (varsPrev) {
				if (vars.StrTitle == varsPrev.strTitle) {
					this._setDetailJournalEstimate(varsPrev);
					return;
				}
			}
			if (this._varsValue) {
				if (this._varsValue.vars) {
					if (vars.StrTitle == this._varsValue.vars.StrTitle) {
						return;
					}
				}
			}
			this._eventValue({
				vars     : vars,
				idTarget : ''
			});

		} else if (obj.flag == 'add' || obj.flag == 'edit') {
			this._setDetailContentValue();
			if (this.insDetail.checkForm({flagType : 'common'})) return;
			this._setDetailContentValue();

			var varsFormat = this._getDetailFormFormat();
			var data = {vars : varsFormat, strTitle : varsFormat.StrTitle};
			obj.varsFormat = data;

			var vars = this.insDetail.getFormValue();
			if (!vars.NumRatio.match(/^[0-9]{1,3}\.[0-9]{2,2}$/)) {
				this.insDetail.showFormAttestError({flagType : 'strFormat'});
				return;
			}
			if (parseFloat(vars.NumRatio) > 100) {
				this.insDetail.showFormAttestError({flagType : 'strMax'});
				return;
			}

			var flagError = this._checkDetailJsonDetailBlank({vars : vars.JsonDetail.varsDetail});
			if (flagError) {
				this.insDetail.showFormAttestError({flagType : flagError});
				return;
			}

			this._eventValue({
				vars     : vars,
				idTarget : (obj.flag == 'edit')? this.varsChild.idTarget : ''
			});

		} else if (!obj.flag.match(/^format(.*?)-save$/)) {
			obj.arr = (Object.toJSON(this.varsChild.varsDetail)).evalJSON();
			for (var i = 0; i < obj.arr.length; i++) {
				if (obj.vars[obj.arr[i].id] == undefined) continue;
				if (obj.arr[i].id == 'ArrCommaIdAccountPermit') {
					obj.arr[i].varsFormArea.varsDetail = obj.vars[obj.arr[i].id];
					obj.arr[i].value = 'dummy';
					continue;

				} else if (obj.arr[i].id == 'JsonDetail') {
					obj.arr[i].varsFormJournal.varsDetail = this._updateDetailJsonDetail({
						vars   : obj.vars[obj.arr[i].id]
					});
					flagJsonDetailReset = this._checkDetailJsonDetail({
						vars   : obj.vars[obj.arr[i].id]
					});
					obj.arr[i].value = 'dummy';
					if (flagJsonDetailReset) {
						obj.arr[i].varsFormJournal.varsDetail = this._resetVarsJsonDetail({vars : obj.vars.JsonDetail});
					}
					continue;

				} else if (obj.arr[i].id == 'NumSumMax') {
					if (obj.vars.ArrCommaIdAccountPermit) {
						var numMax = obj.vars.ArrCommaIdAccountPermit.length;
						var arrayNewMax = [];
						for (var j = 0; j < numMax; j++) {
							var data = {};
							var numValue = j + 1;
							var strTitle = numValue + obj.arr[i].varsTmpl.strPerson;
							data.value = numValue;
							data.strTitle = strTitle;
							arrayNewMax.push(data);
						}
						obj.arr[i].arrayOption = arrayNewMax;
						obj.arr[i].value = (parseFloat(obj.vars[obj.arr[i].id]) > numMax)? 0 : obj.vars[obj.arr[i].id];
						continue;
					}
				}
				obj.arr[i].value = obj.vars[obj.arr[i].id];
			}
			this.vars.portal.varsDetail.varsDetail = obj.arr;
			this._eventRemoveDetailContent();
			this._setDetailStart();
			if (flagJsonDetailReset) {
				this._varsDetailFormJournal.insFormJournal.resetVarsDetail();
				this.insDetail.showFormAttestError({flagType : flagJsonDetailReset});
			}
			return;

		}

		this._varsDetailConnect = obj;
		this._sendDetailConnect();
	},

	/**
	 *
	*/
	_setDetailJournalEstimate : function(obj)
	{
		this.insDetail.showFormAttestError({flagType : 'dummy'});
		this._varsDetailFormJournal.insFormJournal.addBtnTitle(obj);
	},

	/**
	 *
	*/
	_checkDetailJsonDetailBlank : function(obj)
	{
		obj.arr = obj.vars;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].arrDebit.idAccountTitle || !obj.arr[i].arrCredit.idAccountTitle) {
				return 'strRowIdAccountTitle';
			}
		}
	},

	/**
	 *
	*/
	_resetVarsJsonDetail : function(obj)
	{
		var cut = this._varsDetailFormJournal.insFormJournal.vars.varsRule;

		obj.vars.idAccountTitleDebit = '';
		obj.vars.idAccountTitleCredit = '';
		obj.vars.numSum = 0;
		obj.vars.numSumDebit = 0;
		obj.vars.numSumCredit = 0;
		obj.vars.varsDetail = [];
		obj.vars.varsEntityNation = [];
		obj.vars.varsEntityNation.flagConsumptionTaxFree = parseFloat(cut.varsEntityNation.flagConsumptionTaxFree);
		obj.vars.varsEntityNation.flagConsumptionTaxGeneralRule = parseFloat(cut.varsEntityNation.flagConsumptionTaxGeneralRule);
		obj.vars.varsEntityNation.flagConsumptionTaxDeducted = parseFloat(cut.varsEntityNation.flagConsumptionTaxDeducted);
		obj.vars.varsEntityNation.flagConsumptionTaxIncluding = parseFloat(cut.varsEntityNation.flagConsumptionTaxIncluding);

		return obj.vars;
	},

	/**
	 *
	*/
	_checkDetailJsonDetail : function(obj)
	{
		if (!this._varsDetailFormJournal.insFormJournal) return;
		var cut = this._varsDetailFormJournal.insFormJournal.vars.varsRule;

		obj.arr = obj.vars.varsDetail;
		var arrSide = ['arrDebit', 'arrCredit'];
		for (var i = 0; i < obj.arr.length; i++) {

			for (var j = 0; j < arrSide.length; j++) {
				var strSide = arrSide[j];

				var idAccountTitle = obj.arr[i][strSide].idAccountTitle;
				var idDepartment = obj.arr[i][strSide].idDepartment;
				var idSubAccountTitle = obj.arr[i][strSide].idSubAccountTitle;

				if (idAccountTitle) {
					if (!cut.arrAccountTitle.arrStrTitle[idAccountTitle]) {
						return 'strOldIdAccountTitle';
					}

					if (idDepartment) {
						if (!cut.arrDepartment.arrSelectTag.length > 1) {
							return 'strOldIdDepartment';

						} else {
							if (!cut.arrDepartment.arrStrTitle[idDepartment]) {
								return 'strOldIdDepartment';
							}
						}
					}

					if (idSubAccountTitle) {
						if (!cut.arrSubAccountTitle.arrStrTitle[idAccountTitle]) {
							return 'strOldIdSubAccountTitle';

						} else {
							if (!cut.arrSubAccountTitle.arrStrTitle[idAccountTitle][idSubAccountTitle]) {
								return 'strOldIdSubAccountTitle';
							}
						}
					}

					/*
					 * version x < 1.36.00 myfolder
					 */
					if (obj.arr[i][strSide].numRateConsumptionTax == undefined) {
						return 'strOldConsumption';
					}
				}
			}
		}

		if (parseFloat(obj.vars.varsEntityNation.flagConsumptionTaxFree) != parseFloat(cut.varsEntityNation.flagConsumptionTaxFree)
			 || parseFloat(obj.vars.varsEntityNation.flagConsumptionTaxGeneralRule) != parseFloat(cut.varsEntityNation.flagConsumptionTaxGeneralRule)
			 || parseFloat(obj.vars.varsEntityNation.flagConsumptionTaxDeducted) != parseFloat(cut.varsEntityNation.flagConsumptionTaxDeducted)
			 || parseFloat(obj.vars.varsEntityNation.flagConsumptionTaxIncluding) != parseFloat(cut.varsEntityNation.flagConsumptionTaxIncluding)
		) {
			return 'strOldConsumption';
		}
	},

	/**
	 *
	*/
	_updateDetailJsonDetail : function(obj)
	{
		if (!this._varsDetailFormJournal.insFormJournal) return;

		var cut = this._varsDetailFormJournal.insFormJournal.vars.varsRule;

		obj.arr = obj.vars.varsDetail;

		for (var i = 0; i < obj.arr.length; i++) {
			var flagConsumptionTaxCalc = parseFloat(cut.varsEntityNation.flagConsumptionTaxCalc);
			obj.arr[i].arrDebit.flagConsumptionTaxCalc = flagConsumptionTaxCalc;
			obj.arr[i].arrCredit.flagConsumptionTaxCalc = flagConsumptionTaxCalc;
		}

		return obj.vars;
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
			} else if (this._varsDetailConnect.flag.match(/^estimate$/)) {
				if (obj.json.data.strTitle != this._varsValue.vars.StrTitle) {
					this.insDetail.showFormAttestError({flagType : 'dummy'});
					return;
				}
				var varsData = this._checkDetailJournalEstimate({
					arr : obj.json.data.varsData
				});
				this._setDetailJournalEstimate({
					arr      : varsData,
					strTitle : obj.json.data.strTitle
				});
				return;
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
			this.insDetail.showFormAttestError({flagType : obj.json.flag});
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
	_checkDetailJournalEstimate : function(obj)
	{
		var varsData = [];
		var cut = this._varsDetailFormJournal.insFormJournal.vars.varsRule;

		if (obj.arr.length == 1) {
			if (parseFloat(obj.arr[0].flagDisabled)) {
				return obj.arr;
			}
		}

		for (var i = 0; i < obj.arr.length; i++) {
			var idTarget = obj.arr[i];
			if (parseFloat(obj.arr[i].flagDisabled)) {
				continue;
			}
			if (obj.arr[i].idAccountTitleDebit != 'accountsReceivables'
				|| !cut.arrAccountTitleCost.arrStrTitle[obj.arr[i].idAccountTitleCredit]
			) {
				continue;
			}
			varsData.push(obj.arr[i]);
		}
		if (!varsData.length) {
			var temp = {
				strTitle             : cut.varsDictionaryItem.varsStr.none,
				idAccountTitleDebit  : '',
				idAccountTitleCredit : '',
				flagDisabled         : 1
			};
			varsData.push(temp);
		}

		return varsData;
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