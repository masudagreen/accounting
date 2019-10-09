{literal}
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_BanksAccountEditor = Class.create(Code_Lib_ExtEditor,
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
		this._iniDetailFormCalender();
		this._iniDetailFormView();
		this._iniDetailFormSensitive();
	},

	/**
	 *
	*/
	_iniDetailFormCalender : function()
	{
		this._extDetailFormCalender();
	},

	/**
	 *
	*/
	_iniDetailFormView : function()
	{
		var flagBank = this._getDetailFlagBank({arr : this.insDetail.insForm.vars.varsDetail});
		this._setDetailJsonDetail({
			flagBank : flagBank,
			arr      : this.insDetail.insForm.vars.varsDetail
		});
	},

	/**
	 *
	*/
	_getDetailFlagBank : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'FlagBank') {
				return obj.arr[i].value;
			}
		}
	},

	/**
	 *
	*/
	_setDetailJsonDetail : function(obj)
	{
		this.insDetail.setValue();
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'JsonDetail') {

				this.insDetail.insForm.viewForm({
					idTarget    : obj.arr[i].id,
					flagHideNow : (obj.flagBank)? 0 : 1
				});
			}
		}
	},

	/**
	 *
	*/
	_varsSensitive : {},
	_iniDetailFormSensitive : function()
	{
		var num = this._varsKeyNum.JsonDetail;
		var flagBank = this._getDetailFlagBank({arr : this.insDetail.insForm.vars.varsDetail});
		if (!flagBank) {
			return;
		}
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
		this._setDetailFormSensitiveValue({arr : this.insDetail.insForm.vars.varsDetail});
	},

	/**
	 *
	*/
	_eventSelectShortCut : function()
	{
		this._eventRemoveDetailFormSensitive();
		if (this._varsSensitive.ins) {
			this._varsSensitive.ins.eleInsert.innerHTML = '';
		}
		var num = this._varsKeyNum.JsonDetail;
		this.insDetail.insForm.vars.varsDetail[num].varsFormSensitive.varsDetail = [];
		this._setDetailContent();
	},

	/**
	 *
	*/
	_setDetailFormSensitive : function(obj)
	{
		var num = this._varsKeyNum.JsonDetail;
		var flagBank = this._getDetailFlagBank({arr : this.insDetail.insForm.vars.varsDetail});
		var ele = this.insDetail.insForm.eleWrap.down('.codeLibFormContent', this._varsContent.num);
		if (!obj.arr[num].varsFormSensitive.varsDetail.length) {
			obj.arr[num].varsFormSensitive.varsDetail = this.insCurrent.vars.varsItem.varsBanksList[flagBank].varsDetail;
		}
		var strTable = this.insCurrent.vars.varsItem.varsBanksList[flagBank].tplTable;
		var varsStr = {'idSelf'  : this.idSelf};
		var arr = obj.arr[num].varsFormSensitive.varsDetail;
		for (var i = 0; i < arr.length; i++) {
			varsStr[arr[i].id] =  arr[i].value;
			if (arr[i].value === '') {
				varsStr[arr[i].id] = this.insCurrent.vars.varsItem.strSpace;
			}
		}
		var data = strTable.interpolate(varsStr);
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
	_getDetailFormSensitiveVars : function(obj)
	{
		var num = this._varsKeyNum.JsonDetail;
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
	_setDetailFormSensitiveValue : function(obj)
	{
		var temp = {};
		var num = this._varsKeyNum.JsonDetail;
		var arr = obj.arr[num].varsFormSensitive.varsDetail;
		for (var i = 0; i < arr.length; i++) {
			temp[arr[i].id] = arr[i].value;
		}
		obj.arr[num].value = temp;
	},

	/**
	 *
	*/
	_setDetailFormSensitiveView : function(obj)
	{
		var num = this._varsKeyNum.JsonDetail;
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
		var data = '';
		if (vars.flagTag == 'textarea') {
			data = insEscape.get({data : vars.value, flagType : 'toHtml'});

		} else if (vars.flagInputType == 'password') {
			var numAll = vars.value.length;
			var str = '';
			for (var i = 0; i < numAll; i++) {
				str += '*';
			}
			data = str;

		} else {
			data = insEscape.get({data : vars.value, flagType : 'fromTag'});
		}
		if (data == '') {
			data = this.insCurrent.vars.varsItem.strSpace;
		}
		ele.innerHTML = data;
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
	},

	/**
	 *
	*/
	_updateVarsDetailSensitive : function(obj)
	{
		this.insDetail.resetValueError();

		if (obj.strError) {
			this._varsSensitive.ins.updateVarsTarget({
				idTarget : obj.vars.id,
				strKey   : 'value',
				vars     : obj.varsPrev.value
			});
			this.insDetail.showFormAttestError({flagType : 'flagErrorComment', str : obj.strError});
			return;
		}

		this._updateDetailFormSensitiveView({
			idTarget  : obj.vars.id
		});
		this._getDetailFormSensitiveVars({arr : this.insDetail.insForm.vars.varsDetail});
		this._setDetailFormSensitiveView({arr : this.insDetail.insForm.vars.varsDetail});
		this._setDetailFormSensitiveValue({arr : this.insDetail.insForm.vars.varsDetail});
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
		var flagBank = this._getDetailFlagBank({arr : this.insDetail.insForm.vars.varsDetail});
		if (!flagBank) {
			return;
		}
		this._getDetailFormSensitiveVars({arr : this.insDetail.insForm.vars.varsDetail});
	},

	/**
	 *
	*/
	_eventRemoveDetailContent : function()
	{
		if (!this.insDetail.insForm) return;
		this._eventRemoveDetailFormSensitive();
		this._eventRemoveDetailFormCalender({arr : this.insDetail.insForm.vars.varsDetail});
	},

	/**
	 *
	*/
	_eventRemoveDetailFormSensitive : function()
	{
		if (this._varsSensitive.ins) {
			this._varsSensitive.ins.stopListener();
		}

	},

	/**
	 *
	*/
	_setDetailContentValue : function()
	{
		var flagBank = this._getDetailFlagBank({arr : this.insDetail.insForm.vars.varsDetail});
		if (!flagBank) {
			return;
		}
		this._getDetailFormSensitiveVars({arr : this.insDetail.insForm.vars.varsDetail});
		this._setDetailFormSensitiveValue({arr : this.insDetail.insForm.vars.varsDetail});
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

		} else if (obj.flag == 'add' || obj.flag == 'edit') {
			this._setDetailContentValue();
			if (this.insDetail.checkForm({flagType : 'common'})) return;
			this._setDetailContentValue();
			var vars = this.insDetail.getFormValue();
			var flag = 0;
			var num = this._varsKeyNum.JsonDetail;
			var arr = this.insDetail.insForm.vars.varsDetail[num].varsFormSensitive.varsDetail;
			for (var i = 0; i < arr.length; i++) {
				if (arr[i].value) {
					flag = 1;
				}
			}
			if (flag) {
				for (var i = 0; i < arr.length; i++) {
					if (arr[i].value === '') {
						this.insDetail.showFormAttestError({flagType : 'strBlank'});
						return;
					}
				}
			}
			this._eventValue({
				vars     : vars,
				idTarget : (obj.flag == 'edit')? this.varsChild.idTarget : ''
			});

		} else if (!obj.flag.match(/^format(.*?)-save$/)) {

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
				this.insCurrent.vars.varsItem = obj.json.data.varsItem;
				this._setDetailEnd();

			} else if (this._varsDetailConnect.flag.match(/^edit/)) {
				this.insCurrent.eventDetailConnectSuccessListDetailUpdate(obj);
				this.insCurrent.vars.varsItem = obj.json.data.varsItem;
				this._setDetailEnd();
			}

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
	_iniCss : function()
	{
		this._extCss();
	}
});
{/literal}