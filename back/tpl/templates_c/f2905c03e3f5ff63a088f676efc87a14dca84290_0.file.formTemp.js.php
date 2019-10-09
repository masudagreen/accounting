<?php /* Smarty version 3.1.24, created on 2019-10-06 06:26:36
         compiled from "/app/rucaro/back/tpl/templates/else/core/base/js/lib/formTemp.js" */ ?>
<?php
/*%%SmartyHeaderCode:21274023175d99891c564430_59296727%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f2905c03e3f5ff63a088f676efc87a14dca84290' => 
    array (
      0 => '/app/rucaro/back/tpl/templates/else/core/base/js/lib/formTemp.js',
      1 => 1570328740,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '21274023175d99891c564430_59296727',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d99891c5d1e75_73617815',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d99891c5d1e75_73617815')) {
function content_5d99891c5d1e75_73617815 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '21274023175d99891c564430_59296727';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_FormTemp = Class.create(Code_Lib_ExtLib,
{

	initialize : function(obj)
	{
		this._extAllot(obj);
		this._iniVars(obj);
		this._iniListener();
		this._iniWrap();
		this._iniForm();
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
	_iniVars : function(obj)
	{
		this._extVars(obj);
		this.insTimeZone = this.insRoot.insTimeZone;
	},

	/**
	 * Lock
	*/
	_iniLock : function(obj)
	{
		this._setLock();
	},

	/**
	 *
	*/
	insLock : null,
	_setLock : function() {
		this.insRoot.vars.varsSystem.num.zIndex ++;
		this.insLock = new Code_Lib_LockTemp();
		this.insLock.iniLoad({
			idSelf     : this.idSelf + 'Lock',
			idInsert   : this.insRoot.vars.varsSystem.id.root,
			eleInsert  : this.eleInsert,
			numZIndex  : this.insRoot.vars.varsSystem.num.zIndex,
			insCurrent : this.insSelf,
			strFunc    : 'changeForm'
		});
	},

	/**
	 * Wrap
	*/
	eleWrap : null,
	_iniWrap : function()
	{
		this._iniLock();
		this._setWrap();
	},

	/**
	 *
	*/
	_setWrap : function()
	{
		var ele = $(document.createElement('form'));
		this.eleInsert.insert(ele);
		ele.id = this.idSelf;
		ele.addClassName('codeLibFormTempWrap');
		this.eleWrap = ele;
		this.eleWrap.setStyle({
			zIndex : this.insRoot.setZIndex(),
			top    : this.vars.varsStatus.numTop + 'px',
			left   : this.vars.varsStatus.numLeft + 'px'
		});
	},

	/**
	 *
	*/
	removeWrap : function()
	{
		if(this.insLock.eleLock) this.insLock.eleLock.remove();
		this.stopListener();
		this._blurCalenderCheck();
		this.eleWrap.remove();
		this.allot({
			insCurrent : this.insCurrent,
			from       : 'removeWrap',
			vars       : this.vars.varsDetail
		});
	},

	/**
	 *
	*/
	_iniForm : function()
	{
		var cut = this.vars.varsDetail;
		if (cut.flagTag == 'selectShortCut') {
			this._setFormSC();

		} else {
			this._setForm();
		}
	},

	_setFormSC : function()
	{
		this._resetFormSCVars();
		this._setFormSCWrap();
		this._setFormSCForm();
		this._setFormSCTitle();
		this._setFormSCInput();
		this._setFormSCList();
		this._setFormSCStyle();
		this._setFormSCSelected();
		this._eleSCInput.focus();
	},

	/**
	 *
	*/
	_varsFormSCWidth : 200,
	_resetFormSCVars : function()
	{
		var cut = this.vars.varsDetail;
		this._varsFormSCWidth = 200;
		if (cut.numWidth > 200) {
			this._varsFormSCWidth = cut.numWidth;
		}
	},

	_staticFormSCICon : 16,
	_staticFormSCIdle : 23,

	_setFormSCStyle : function()
	{
		this._eleSCWrap.style.width = this._varsFormSCWidth + 'px';

		this._eleSCInputWrap.style.width = this._varsFormSCWidth + 'px';
		this._eleSCInput.style.width = (this._varsFormSCWidth - this._staticFormSCIdle) + 'px';

		this._eleSCListWrap.style.width = this._varsFormSCWidth + 'px';
		this._eleSCListWrap.style.height = this._varsFormSCListHeight + 'px';

		this._resetFormSCListAuto();

		var cut = this.vars.varsDetail;
		var arr = cut.arrayOption;
		for (var i = 0; i < arr.length; i++) {
			$(this.idSelf + '_FormSCList_' + i).style.width = (this._varsFormSCWidth - 16) + 'px';
		}
		$(this.eleWrap).insert(this._eleSCWrap);
	},

	_calcFormSCWidth : function(obj)
	{
		if (obj.num > this._varsFormSCWidth) {
			this._varsFormSCWidth = obj.num;
		}

	},

	_staticFormSCHeight : 20,
	_staticFormSCListHeight : 300,
	_calcFormSCListHeight : function(obj)
	{
		this._varsFormSCListHeight = this._staticFormSCListHeight;
		if (obj.num < this._varsFormSCListHeight) {
			this._varsFormSCListHeight = obj.num;
		}
	},

	/**
	 *
	*/
	_eleSCWrap : null,
	_staticFormSCPos : 6,
	_setFormSCWrap : function()
	{
		this.eleWrap.setStyle({
			top    : (this.vars.varsStatus.numTop - this._staticFormSCPos) + 'px',
			left   : (this.vars.varsStatus.numLeft - this._staticFormSCPos) + 'px'
		});
		var ele = $(document.createElement('div'));
		$(this.insRoot.vars.varsSystem.id.root).insert(ele);
		ele.id = this.idSelf + '_SCWrap';
		ele.addClassName('codeLibFormTempSCWrap');
		this._eleSCWrap = ele;
	},

	_setFormSCForm : function()
	{
		var cut = this.vars.varsDetail;
		var eleTag = $(document.createElement('select'));
		this.eleTag = eleTag;
		this.eleWrap.insert(eleTag);
		this.eleTag.hide();
		eleTag.id = this.idSelf + '_Form';
		var temp = cut.value;
		if (temp == '') {
			temp = this._getFormSelectValue({
				arr : cut.arrayOption
			});
		}
		this._setFormSCFormSelect({
			arr       : cut.arrayOption,
			now       : temp,
			eleInsert : eleTag,
			vars      : cut
		});
		eleTag.value = temp;
	},

	/**
	 *
	*/
	_varsFormSCTitle : '',
	_numFormSCSelected : 0,
	_setFormSCFormSelect : function(obj)
	{
		this._varsFormSCTitle = '';
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].flagDisabled || obj.arr[i].flagNotShortCut) {
				continue;
			}
			if(obj.now == obj.arr[i].value) {
				var ele = $(document.createElement('option'));
				ele.value = obj.arr[i].value;
				ele.selected = true;
				ele.insert(obj.arr[i].strTitle);
				this._varsFormSCTitle = obj.arr[i].strTitle;
				this._numFormSCSelected = i;
				obj.eleInsert.insert(ele);
				break;
			}
		}
	},

	_eleSCTitle : null,
	_setFormSCTitle : function(obj)
	{
		var cut = this.vars.varsDetail;
		var ele = $(document.createElement('span'));
		ele.addClassName('codeLibFormTempSCTitle');
		ele.insert(this._varsFormSCTitle);
		this._eleSCWrap.insert(ele);
		ele.id = this.idSelf + '_FormSCTitle';
		this._eleSCTitle = ele;
		this._calcFormSCWidth({num : ele.offsetWidth});
	},

	_eleSCInputWrap : null,
	_eleSCInput : null,
	_setFormSCInput : function()
	{
		var eleWrap = $(document.createElement('div'));
		eleWrap.addClassName('codeLibFormTempSCInputWrap');
		eleWrap.addClassName('clearfix');
		this._eleSCWrap.insert(eleWrap);
		this._eleSCInputWrap = eleWrap;

		var eleTag = $(document.createElement('input'));
		eleTag.addClassName('codeLibFormTempSCInput');
		eleTag.type = 'text';

		this._eleSCInputWrap.insert(eleTag);
		eleTag.value = '';
		this._eleSCInput = eleTag;
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'keyup',
			strFunc : '_keyupInput', ele : eleTag, vars : {ele : eleTag}
		});

		var eleBox = $(document.createElement('p'));
		eleBox.addClassName('codeLibFormTempSCIcon');
		eleBox.addClassName('unselect');
		eleBox.addClassName('codeLibBaseCursorDefault');
		eleBox.unselectable = 'on';
		eleBox.insert(this.insRoot.vars.varsSystem.str.space);
		eleBox.style.width = this._staticFormSCICon + 'px';
		eleBox.style.height = this._staticFormSCICon + 'px';
		this._eleSCInputWrap.insert(eleBox);
	},

	_keyupInput : function (evt, obj)
	{
		var cut = this.vars.varsDetail;
		var arr = cut.arrayOption;
		if (obj.ele.value === '') {
			this._resetFormSCList({arr : arr});

		} else {
			this._updateFormSCList({arr : arr, str : obj.ele.value});
		}
		this._eleSCListWrap.style.height = this._varsFormSCListHeight + 'px';
		this._resetFormSCListAuto();


	},

	_resetFormSCListAuto : function ()
	{
		this._eleSCListWrap.removeClassName('codeLibFormTempSCListWrapAuto');
		if (this._varsFormSCListHeight == this._staticFormSCListHeight) {
			this._eleSCListWrap.addClassName('codeLibFormTempSCListWrapAuto');
		}
	},

	_resetFormSCList : function (obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var ele = $(this.idSelf + '_FormSCList_' + i);
			ele.show();
		}
		this._calcFormSCListHeight({num : this._staticFormSCListHeight});
	},

	_updateFormSCList : function (obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			var ele = $(this.idSelf + '_FormSCList_' + i);
			ele.show();
			if (obj.arr[i].flagDisabled || obj.arr[i].flagNotShortCut) {
				ele.hide();
				continue;
			}
			var str = obj.str.replace(/([.*+?^=!:${}()|[\]\/\\])/g, "\\$1");
			if (!obj.arr[i].strTitle.match(new RegExp(str,'im'))) {
				ele.hide();
				continue;
			}
			num += this._staticFormSCHeight;
		}
		this._calcFormSCListHeight({num : num});
		if (!num) {
			this._resetFormSCList({arr : obj.arr});
		}
	},

	_eleSCListWrap : null,
	_eleSCList : null,
	_staticFormSCLine : {numLength : 100, numIdle : 16},
	_setFormSCList : function()
	{
		var eleWrap = $(document.createElement('div'));
		eleWrap.addClassName('codeLibFormTempSCListWrap');
		eleWrap.addClassName('clearfix');
		this._eleSCWrap.insert(eleWrap);
		this._eleSCListWrap = eleWrap;

		var cut = this.vars.varsDetail;
		var num = 0;
		var arr = cut.arrayOption;
		var strHtml = '';
		for (var i = 0; i < arr.length; i++) {
			var id = this.idSelf + '_FormSCList_' + i;
			var strPointer = 'codeLibBaseCursorPointer';
			var strFont = '';
			if (arr[i].flagDisabled) {
				strPointer = 'codeLibBaseCursorDefault';
				strFont = 'codeLibFormTempSCLineNoactive';
			}
			var strTitle = arr[i].strTitle;
			strHtml += '<span id="' + id + '" class="codeLibFormTempSCLine unselect '
				+ strPointer + ' ' + strFont + '" style="">' + strTitle + '</span>';

			num += this._staticFormSCHeight;
		}
		this._calcFormSCListHeight({num : num});
		this._eleSCListWrap.insert(strHtml);
		this._setFormSCListListener({arr : arr});
	},

	/**
	 *
	*/
	_setFormSCListListener : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].flagDisabled) {
				continue;
			}
			var ele = $(this.idSelf + '_FormSCList_' + i);
			this.insListener.set({ bindAsEvent : 1, insCurrent : this, event : 'mousedown',strFunc : '_mousedownFormSCList',
				ele : ele, vars : {vars : obj.arr[i], ele : ele}
			});
			this.insListener.set({ bindAsEvent : 0, insCurrent : this, event : 'mouseover',strFunc : '_mouseoverFormSCList',
				ele : ele, vars : {vars : obj.arr[i], ele : ele}
			});
			this.insListener.set({ bindAsEvent : 0, insCurrent : this, event : 'mouseout',strFunc : '_mouseoutFormSCList',
				ele : ele, vars : {vars : obj.arr[i], ele : ele}
			});
		}
	},

	/**
	 *
	*/
	_mousedownFormSCList : function(evt,obj)
	{
		if (obj) evt.stop();
		else obj = evt;
		this.vars.varsDetail.value = obj.vars.value;
		this.removeWrap();
	},

	/**
	 *
	*/
	_mouseoverFormSCList : function(obj)
	{
		obj.ele.removeClassName('codeLibFormTempSCLineOver');
		obj.ele.addClassName('codeLibFormTempSCLineOver');
	},

	/**
	 *
	*/
	_mouseoutFormSCList : function(obj)
	{
		obj.ele.removeClassName('codeLibFormTempSCLineOver');
	},

	_setFormSCSelected : function()
	{
		var id = this.idSelf + '_FormSCList_' + this._numFormSCSelected;
		if (this._numFormSCSelected >= 3) {
			idTarget = this.idSelf + '_FormSCList_' + (this._numFormSCSelected - 3);
			this._eleSCListWrap.scrollTop = $(idTarget).offsetTop;

		} else {
			this._eleSCListWrap.scrollTop = 0;
		}
		$(id).addClassName('codeLibFormTempSCLineOver');
	},

	/**
	 *
	*/
	eleTag : null,
	_setForm : function()
	{
		var cut = this.vars.varsDetail;
		var eleTag = $(document.createElement(cut.flagTag));
		this.eleTag = eleTag;
		this.eleWrap.insert(eleTag);
		eleTag.addClassName('codeLibFormTempTag');
		eleTag.id = this.idSelf + 'Tag';
		if(cut.flagTag == 'input') eleTag.type = cut.flagInputType;
		eleTag.style.width = cut.numWidth + cut.unitWidth;
		eleTag.style.height = cut.numHeight + cut.unitHeight;
		if(cut.numMaxlength) eleTag.maxLength = cut.numMaxlength;
		if(cut.flagDisabled) eleTag.disabled = true;
		eleTag.value = cut.value;

		if (cut.flagTag == 'select') {
			if(cut.flagMultiple) eleTag.multiple = true;
			if(cut.numSize) eleTag.size = cut.numSize;

			this._setFormSelect({
				arr       : cut.arrayOption,
				now       : cut.value,
				eleInsert : eleTag,
				vars      : cut
			});
			if (cut.value == '') {
				eleTag.value = this._getFormSelectValue({
					arr : cut.arrayOption
				});
			}

		}

		if (cut.varsFormCalender) {
			this._setCalenderListener({
				eleInsert : eleTag,
				vars      : cut
			});
		}

		this.insListener.set({
			bindAsEvent : 0, insCurrent : this, event : 'change', strFunc : 'changeForm',
			ele : eleTag, vars : {vars : cut}
		});

		eleTag.focus();
		if (cut.flagTag == 'input' && cut.flagInputType == 'text') {
			eleTag.select();
		}
	},

	/**
	 *
	*/
	_setCalenderListener : function(obj)
	{
		this.insListener.set({ flagType15 : 1, bindAsEvent : 1, insCurrent : this, event : 'focus',
		strFunc : '_focusCalenderCheck',	ele : obj.eleInsert, vars : '' });
		this.insListener.set({ flagType15 : 1, bindAsEvent : 1, insCurrent : this, event : 'blur',
		strFunc : '_blurCalenderCheck', ele : obj.eleInsert, vars : '' });
	},

	/**
	 *
	*/
	_focusCalenderCheck : function(obj,evt)
	{
		if (obj) evt.stop();
		else obj = evt;
		if ($(this.idSelf + 'Calender')) return;
		this._setCalender();
	},

	/**
	 *
	*/
	_blurCalenderCheck : function(obj,evt)
	{
		if (obj) evt.stop();
		else obj = evt;
		if (!$(this.idSelf + 'Calender')) return;
		this.insCalender.removeWrap();
	},


	/**
	 *
	*/
	_staticCalender : {numTop : 21, numLeft : 3},
	insCalender : null,
	_setCalender : function(obj)
	{
		var cut = this.vars.varsDetail;
		cut.varsFormCalender.varsStatus.numLeft = this.vars.varsStatus.numLeft - this._staticCalender.numLeft;
		cut.varsFormCalender.varsStatus.numTop = this.vars.varsStatus.numTop + this._staticCalender.numTop;
		this.insCalender = new Code_Lib_Calender({
			eleInsert  : this.eleTag.up('.codeLibWindow', 0),
			idRoot     : this.insRoot.vars.varsSystem.id.root,
			insRoot    : this.insRoot,
			insCurrent : this.insSelf,
			idSelf     : this.idSelf + 'Calender',
			allot      : this._getCalenderAllot(),
			vars       : cut.varsFormCalender
		});
		this._setListener({ins : this.insCalender});
	},

	/**
	 *
	*/
	_getCalenderAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == '_mousedownDate') {
				insCurrent.insCalender.removeWrap();
				insCurrent._setValue({objTime : obj.objTime});
			}
		};

		return allot;
	},

	/**
	 *
	*/
	_setValue : function(obj)
	{
		var cut = this.vars.varsDetail;
		var objTime = obj.objTime;
		var insDisplay = new Code_Lib_TimeDisplay();
		if (cut.varsFormCalender.varsStatus.flagFormatDisplay) {
			this.eleTag.value = insDisplay.get({flagType : cut.varsFormCalender.varsStatus.flagFormatDisplay, vars : objTime});

		} else {
			this.eleTag.value = insDisplay.get({flagType : 4, vars : objTime});
		}
	},

	/**
	 *
	*/
	changeForm : function(obj)
	{
		this.vars.varsDetail.value = this.eleTag.value;
		this.removeWrap();
	},

	/**
	 *
	*/
	_getFormSelectValue : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].flagDisabled || obj.arr[i].flagNotShortCut) continue;
			return obj.arr[i].value;
		}
	},

	/**
	 *
	*/
	_setFormSelect : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var ele = $(document.createElement('option'));
			ele.value = obj.arr[i].value;
			if(obj.now == obj.arr[i].value) ele.selected = true;
			if (obj.arr[i].flagDisabled)  ele.disabled = true;
			ele.insert(obj.arr[i].strTitle);
			obj.eleInsert.insert(ele);
		}
	}
});

<?php }
}
?>