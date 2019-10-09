<?php /* Smarty version 3.1.24, created on 2016-08-20 07:30:13
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/controlDetail.js" */ ?>
<?php
/*%%SmartyHeaderCode:124823474757b80705445914_16326256%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '44850c49eb1c0005142839f420900eaacbb71820' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/controlDetail.js',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '124823474757b80705445914_16326256',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b807054b0654_98188966',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b807054b0654_98188966')) {
function content_57b807054b0654_98188966 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '124823474757b80705445914_16326256';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_ControlDetail = Class.create(Code_Lib_ExtControl,
{

	/**
	 *
	*/
	initialize : function(obj)
	{
		this._extAllot(obj);
		this._iniVars(obj);
		this._iniListener();
		this._iniWrap();
	},

	/**
	 *
	*/
	iniReload : function(obj)
	{
		this.eventRemove();
		this.removeWrap();
		this._resetTool();
	},

	/**
	 *
	*/
	_iniVars : function(obj)
	{
		this._extVars(obj);
		this.iniCake();
	},

	/**
	 * Cake
	*/
	iniCake : function()
	{
		this._getCake();
	},

	/**
	 *
	*/
	_getCakeVars : function(obj)
	{
		var insCurrent = obj.insReturn;
		if (obj.data) {
			insCurrent._getCakeVarsUpdateStatus({
				arr  : insCurrent.vars.varsDetail,
				data : obj.data
			});
			insCurrent._varsCake = obj.data;
		}
	},

	/**
	 *
	*/
	_getCakeVarsUpdateStatus : function(obj)
	{
		if (!this.vars.varsStatus.flagCakeUse) return;

		if (this.vars.varsStatus.flagOutputUse) {
			str = 'flagOutputNow';
			if (obj.data[str] != undefined) {
				this.vars.varsStatus.flagOutputNow = obj.data[str];
			}
		}

		if (this.vars.varsStatus.flagPrintUse) {
			str = 'flagPrintNow';
			if (obj.data[str] != undefined) {
				this.vars.varsStatus.flagPrintNow = obj.data[str];
			}
		}

		if (this.vars.varsStatus.flagReloadUse) {
			str = 'flagReloadNow';
			if (obj.data[str] != undefined) {
				this.vars.varsStatus.flagReloadNow = obj.data[str];
			}
		}
	},

	/**
	 *
	*/
	_iniListener : function()
	{
		this._extListener();
	},

	/**
	 *
	*/
	updateVars : function(obj)
	{
		this.vars = (Object.toJSON(obj.vars)).evalJSON();
	},

	/**
	 *
	*/
	updateVarsEvent : function(obj)
	{
		this.vars.varsDetail = obj.vars.varsDetail;
		if(obj.vars.varsBtn) this.vars.varsBtn = obj.vars.varsBtn;
		this._updateVarsDetail({arr : this.vars.varsStatus.switchList});
	},

	/**
	 *
	*/
	_updateVarsDetail : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var str = obj.arr[i];
			this.vars[str].varsDetail.varsDetail = this.vars.varsDetail;
			this.vars[str].varsDetail.varsBtn = this.vars.varsBtn;
		}
	},

	/**
	 * Wrap
	*/
	_iniWrap : function()
	{
		this._extWrap();
	},

	/**
	 *
	*/
	_iniUnder : function(obj)
	{
		this._extUnder(obj);
	},

	/**
	 *
	*/
	_updateUnder : function(obj)
	{
		if (this.vars.varsStatus.flagFormUse) {
			if (obj.strTitleHeaderLeft != null) {
				this.vars.form.varsFormat.strTitleHeaderLeft = obj.strTitleHeaderLeft;
			}
			if (obj.strClassHeaderLeft != null) {
				this.vars.form.varsFormat.strClassHeaderLeft = obj.strClassHeaderLeft;
			}
		}
		if (this.vars.varsStatus.flagViewUse) {
			if (obj.strTitleHeaderLeft != null) {
				this.vars.view.varsFormat.strTitleHeaderLeft = obj.strTitleHeaderLeft;
			}
			if (obj.strClassHeaderLeft != null) {
				this.vars.view.varsFormat.strClassHeaderLeft = obj.strClassHeaderLeft;
			}
		}
	},

	/**
	 * Tool
	*/
	_iniTool : function()
	{
		this._extTool();
	},

	/**
	 *
	*/
	_resetTool : function(obj)
	{
		this._varsTool = {};
		this._iniTool();
	},


	/**
	 * Form
	*/
	_iniForm : function()
	{
		if (!this.vars.varsStatus.flagFormUse) return;
		this._setForm();
	},

	/**
	 *
	*/

	insForm : null,
	_setForm : function()
	{
		this._varsForm = {
			numTop  : 0,
			numLeft : 0
		};
		this.insForm = new Code_Lib_Form({
			eleInsertBtnLeft  : this.insUnder.eleFormat.fooder.down('.codeLibBaseMarginLeftFive',0),
			eleInsertBtnRight : this.insUnder.eleFormat.fooder.down('.codeLibBaseMarginRightFive',0),
			eleInsert         : this.insUnder.eleFormat.body,
			insRoot           : this.insRoot,
			insCurrent        : this,
			idSelf            : this.idSelf + 'Form',
			allot             : this._getFormAllot(),
			vars              : this.vars.form.varsDetail
		});
	},

	/**
	 *
	*/
	_varsForm : {numTop : 0, numLeft : 0},


	/**
	 *
	*/
	setFormScrollVars : function()
	{
		this._setFormScrollVars();
	},

	_setFormScrollVars : function()
	{
		if (this.insForm) this.insForm.setVarsScroll(this._varsForm);
		this._varsForm = {
			numTop  : 0,
			numLeft : 0
		};
	},

	/**
	 *
	*/
	getFormScrollVars : function()
	{
		return this._getFormScrollVars();
	},

	/**
	 *
	*/
	_getFormScrollVars : function()
	{
		if (this.insForm) {
			this._varsForm = this.insForm.getVarsScroll();

			return this._varsForm;
		}
	},

	/**
	 *
	*/
	_getFormAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			obj.insCurrent = insCurrent.insCurrent;
			obj.from = 'form-' + obj.from;
			insCurrent.allot(obj);

		};

		return allot;
	},

	/**
	 *
	*/
	checkForm : function(obj)
	{
		this.insForm.setValue();
		this.insForm.checkValue();
		this.insForm.resetValueError();
		var flag = this.insForm.checkValueError();
		if (flag) {
			this.insForm.showValueError({flagType: obj.flagType});
			return 1;

		} else {
			return 0;
		}
	},

	/**
	 *
	*/
	resetValueError : function()
	{
		this.insForm.resetValueError();
	},

	/**
	 *
	*/
	getForm : function(obj)
	{
		return this.insForm.getValue();
	},

	/**
	 * View
	*/
	_iniView : function()
	{
		if (!this.vars.varsStatus.flagViewUse) return;
		this._varView();
		this._setView();
	},

	/**
	 *
	*/
	_varView : function()
	{
		this._getCake();
	},

	/**
	 *
	*/
	insView : null,
	_setView : function()
	{
		this.insView = new Code_Lib_View({
			eleInsertBtnLeft  : this.insUnder.eleFormat.fooder.down('.codeLibBaseMarginLeftFive', 0),
			eleInsertBtnRight : this.insUnder.eleFormat.fooder.down('.codeLibBaseMarginRightFive', 0),
			eleInsert         : this.insUnder.eleFormat.body,
			insRoot           : this.insRoot,
			insCurrent        : this.insSelf,
			idSelf            : this.idSelf + 'View',
			allot             : this._getViewAllot(),
			vars              : this.vars.view.varsDetail
		});
	},

	/**
	 *
	*/
	_getCakeVarsUpdate : function(obj)
	{
		if (!this.vars.varsStatus.flagCakeUse) return;

		if (this.vars.varsStatus.flagOutputUse) {
			str = 'flagOutputNow';
			if (obj.data[str] != undefined) {
				this.vars.varsStatus.flagOutputNow = obj.data[str];
			}
		}

		if (this.vars.varsStatus.flagPrintUse) {
			str = 'flagPrintNow';
			if (obj.data[str] != undefined) {
				this.vars.varsStatus.flagPrintNow = obj.data[str];
			}
		}

		if (this.vars.varsStatus.flagImportUse) {
			str = 'flagImportNow';
			if (obj.data[str] != undefined) {
				this.vars.varsStatus.flagImportNow = obj.data[str];
			}
		}

		if (this.vars.varsStatus.flagReloadUse) {
			str = 'flagReloadNow';
			if (obj.data[str] != undefined) {
				this.vars.varsStatus.flagReloadNow = obj.data[str];
			}
		}

		obj.arr = this.vars.view.varsDetail.varsDetail;
		var str;
		var numEnd = (obj.arr.length > 50)? obj.arr.length : 50;
		for (var i = 0; i < numEnd; i++) {
			if (!obj.arr[i]) return;
			if (!obj.arr[i].flagFoldUse) continue;
			if (this.vars.varsStatus.flagFoldUse) {
				str = 'flagFoldNow' + i;
				if (obj.data[str]) obj.arr[i].flagFoldNow = obj.data[str];
			}
		}
	},

	/**
	 *
	*/
	_checkCake : function()
	{
		if (!this.insRoot.insCake) return;
		this.insRoot.insCake.getStorageCake({
			parentKey  : this.idSelf,
			funcReturn : this._checkCakeVars,
			insReturn  : this
		});
	},


	/**
	 *
	*/
	_checkCakeVars : function(obj)
	{
		var insCurrent = obj.insReturn;
		if(obj.data) {
			insCurrent._varsCake = obj.data;
		}
	},

	/**
	 *
	*/
	_setCakeVars : function()
	{
		if (!this.vars.varsStatus.flagCakeUse) return;

		if (this.vars.varsStatus.flagOutputUse) {
			str = 'flagOutputNow';
			this._varsCake[str] = this.vars.varsStatus.flagOutputNow;
		}
		if (this.vars.varsStatus.flagPrintUse) {
			str = 'flagPrintNow';
			this._varsCake[str] = this.vars.varsStatus.flagPrintNow;
		}
		if (this.vars.varsStatus.flagImportUse) {
			str = 'flagImportNow';
			this._varsCake[str] = this.vars.varsStatus.flagImportNow;
		}
		if (this.vars.varsStatus.flagReloadUse) {
			str = 'flagReloadNow';
			this._varsCake[str] = this.vars.varsStatus.flagReloadNow;
		}

		if (!this.insView) return;
		this._checkCake();
		var obj = {};
		obj.arr = this.insView.vars.varsDetail;
		var str;
		var numEnd = (obj.arr.length > 50)? obj.arr.length : 50;
		for (var i = 0; i < numEnd; i++) {
			if (obj.arr[i]) {
				if (!obj.arr[i].flagFoldUse) continue;
				if (this.vars.varsStatus.flagFoldUse) {
					str = 'flagFoldNow' + i;
					this._varsCake[str] = obj.arr[i].flagFoldNow;
				}
			}
		}
	},

	/**
	 *
	*/
	_getViewAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			obj.insCurrent = insCurrent.insCurrent;
			obj.from = 'view-' + obj.from;
			insCurrent.allot(obj);

		};

		return allot;
	},

	/**
	 * Sheet
	*/
	_iniSheet : function()
	{
		if (!this.vars.varsStatus.flagSheetUse) return;
	},

	/**
	 * eventRemove
	*/
	eventRemove : function()
	{
		this.stopListener();
		if (this.insView) this.insView.stopListener();
		if (this.insForm) this.insForm.stopListener();
		this.allot({
			insCurrent : this.insCurrent,
			from       : 'eventRemove-detail'
		});
	},

	/**
	 * eventNavi
	*/
	varsEventNavi : null,
	eventNavi : function(obj)
	{
		this.setCake();
		this.eventRemove();
		var str = this.vars.varsStatus.flagNow;
		this.varsEventNavi = (Object.toJSON(obj.vars)).evalJSON();
		this._updateUnder({
			strTitleHeaderLeft : obj.strTitle,
			strClassHeaderLeft : obj.strClass
		});
		this._iniUnder({vars : this.vars[str].varsFormat});
		if (obj.flagMoveUse) {
			this._iniMove({
				vars     : this.varsEventNavi,
				strTitle : obj.strTitle,
				strClass : obj.strClass
			});
		}
		if (this.vars.varsStatus['flag' + str.capitalize() + 'Use']) {
			this.updateVarsEvent({vars : this.varsEventNavi});
			this['_ini' + str.capitalize()]();
			if (this.insTool) {
				this._varsTool = this.varsEventNavi.varsEdit;
				this._iniTool();
			}
		}
	},

	/**
	 * eventList
	*/
	varsEventList : null,
	eventList : function(obj)
	{
		this.setCake();
		this.eventRemove();
		var str = this.vars.varsStatus.flagNow;
		this.varsEventList = (Object.toJSON(obj.vars)).evalJSON();
		this._updateUnder({
			strTitleHeaderLeft : obj.strTitle,
			strClassHeaderLeft : obj.strClass
		});
		this._iniUnder({vars : this.vars[str].varsFormat});

		if (obj.flagMoveUse) {
			this._iniMove({
				vars     : this.varsEventList,
				strTitle : obj.strTitle,
				strClass : obj.strClass
			});
		}

		if (this.vars.varsStatus['flag' + str.capitalize() + 'Use']) {
			this.updateVarsEvent({vars : this.varsEventList});
			this['_ini' + str.capitalize()]();
			if (this.insTool) {
				this._varsTool = this.varsEventList.varsEdit;
				this._iniTool();
			}
		}
	},

	/**
	 *
	*/
	eventTool : function(obj)
	{
		this._extEventTool(obj);
	},

	/**
	 * Switch
	*/
	_iniSwitch : function(obj)
	{
		this._extSwitch(obj);
	},

	/**
	 *
	*/
	eventLayout : function()
	{
		if ($(this.idSelf + 'View')) {
			this.allot({
				insCurrent : this.insCurrent,
				from       : 'view-preEventLayout'
			});
			this.insView.iniReload();
			this.allot({
				insCurrent : this.insCurrent,
				from       : 'view-eventLayout'
			});

		}
		if ($(this.idSelf + 'Form')) {
			this.allot({
				insCurrent : this.insCurrent,
				from       : 'form-preEventLayout'
			});
			this.insForm.iniReload();
			this.allot({
				insCurrent : this.insCurrent,
				from       : 'form-eventLayout'
			});
			this._setFormScrollVars();
		}
	},

	/**
	 *
	*/
	preEventLayout : function(obj)
	{
		var str = '';
		if (obj.flag == 'reset') str = 'resetScroll';
		else str = 'getScroll';

		if ($(this.idSelf + 'View')) {
			this.insView[str]();
		}
		if ($(this.idSelf + 'Form')) {
			this._getFormScrollVars();
			this.insForm[str]();
		}
	},

	/**
	 *
	*/
	getEleLoading : function()
	{
		return this.insUnder.getEleLoading();
	},

	/**
	 *
	*/
	setValue : function()
	{
		return this.insForm.setValue();
	},

	/**
	 *
	*/
	getFormValue : function()
	{
		return this.insForm.getFormValue();
	},

	/**
	 *
	*/
	setFormValueAll : function()
	{
		this.insForm.setValue();
	},


	/**
	 * 	flag    : '', 'selectMultiple'
	 * idTarget : mix
	 * 	value    : mix
	*/
	setFormValue : function(obj)
	{
		this.insForm.setValueVars(obj);
	},


	/**
	 *
	*/
	_iniMove : function(obj)
	{
		if (!this.vars.varsStatus.flagMoveUse) return;
		this._removeMove();
		this._setMove(obj);
	},

	/**
	 *
	*/
	_removeMove : function(obj)
	{
		this.stopListener();
		var ele = this.insUnder.eleFormat.header.down('.codeLibTemplateNormalFormatHeaderImg', 0);
		ele.removeClassName('codeLibBaseCursorMove');
	},

	/**
	 *
	*/
	_setMove : function(obj)
	{
		var ele = this.insUnder.eleFormat.header.down('.codeLibTemplateNormalFormatHeaderImg', 0);
		ele.addClassName('codeLibBaseCursorMove');
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'mousedown',
			strFunc : '_mousedownMove', ele : ele, vars : obj
		});
	},

	/**
	 *
	*/
	_setMoveListener : function(obj)
	{
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'mousemove',
			strFunc : '_mousemoveMove', ele : document, vars : {}
		});
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'mouseup',
			strFunc : '_mouseupMove', ele : document, vars : {}
		});
	},

	/**
	 *
	*/
	_varsMove : {},
	_mousedownMove : function(evt, obj) {
		if (obj) evt.stop();
		else obj = evt;
		this._setMoveListener(obj);

		this._varsMove = {};
		this._varsMove = {
			flag     : 1,
			ele      : evt.element(),
			vars     : obj.vars,
			varsOver : null,
			eleNavi  : null
		};
		this._setMoveNavi({
			vars : obj.vars,
			evt  : evt
		});
		this.allot({
			insCurrent : this.insCurrent,
			from       : '_mousedownMove',
			vars       : obj.vars.vars
		});
	},

	/**
	 *
	*/
	_mousemoveMove : function(evt) {
		if (!this._varsMove.flag) return;
		evt.stop();

		this._varsMove.eleNavi.setStyle({
			top  : (evt.pointerY() + this._staticMove.numNaviTop) + 'px',
			left : (evt.pointerX() + this._staticMove.numNaviLeft) + 'px'
		});
	},

	/**
	 *
	*/
	_mouseupMove : function(evt) {
		evt.stop();
		if (!this._varsMove.flag) return;
		this._varsMove.eleNavi.remove();
		this._varsMove = {};
	},

	/**
	 *
	*/
	_staticMove : {numNaviLeft : 15, numNaviTop : 5},
	_setMoveNavi : function(obj)
	{
		var zIndex = this.insRoot.setZIndex();
		var ele = this.insUnder.eleFormat.header.down('.codeLibTemplateNormalFormatHeaderImg', 0).cloneNode(true);
		$(this.insRoot.vars.varsSystem.id.root).insert(ele);
		this._varsMove.eleNavi = ele;
		ele.addClassName('codeLibControlNavi');
		ele.setStyle({
			top    : (obj.evt.pointerY() + this._staticMove.numNaviTop) + 'px',
			left   : (obj.evt.pointerX() + this._staticMove.numNaviLeft) + 'px',
			zIndex : zIndex
		});
	},

	/**
	 *
	*/
	viewForm : function(obj)
	{
		this.insForm.viewForm({
			idTarget    : obj.idTarget,
			flagHideNow : obj.flagHideNow,
		});
	},

	/**
	 *
	*/
	hideBtnBottom : function()
	{
		var str = 'ins' + this.vars.varsStatus.flagNow.capitalize();
		if (this[str]) {
			return this[str].hideBtnBottom();
		}
	},

	/**
	 *
	*/
	showBtnBottom : function()
	{
		var str = 'ins' + this.vars.varsStatus.flagNow.capitalize();
		if (this[str]) {
			return this[str].showBtnBottom();
		}

	}
});
<?php }
}
?>