<?php /* Smarty version 3.1.24, created on 2022-08-13 00:23:38
         compiled from "/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/search.js" */ ?>
<?php
/*%%SmartyHeaderCode:195431433662f6ef0a566110_02893465%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '925447b32a3956ac7e65ba974313d5a3c331b6a5' => 
    array (
      0 => '/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/search.js',
      1 => 1373720912,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '195431433662f6ef0a566110_02893465',
  'variables' => 
  array (
    'varsLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_62f6ef0a58af72_07012101',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_62f6ef0a58af72_07012101')) {
function content_62f6ef0a58af72_07012101 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '195431433662f6ef0a566110_02893465';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Search = Class.create(Code_Lib_ExtLib,
{

	varsLoad : <?php echo $_smarty_tpl->tpl_vars['varsLoad']->value;?>
,


	/**
	 *
	*/
	initialize : function(obj)
	{

		this._extAllot(obj);
		this._iniVars(obj);
		this._iniWrap();
		this._iniForm();
		this._iniSwitch();
		this._iniItem();
		this._iniSort();
		this._iniMyRecord();
	},

	/**
	 *
	*/
	iniReload : function()
	{
		this.eventRemove();
		this.removeWrap();
		this._iniWrap();
		this._iniForm();
		this._iniSwitch();
		this._iniItem();
		this._iniSort();
		this._iniMyRecord();
		this.setScroll();
	},

	/**
	 *
	*/
	iniReloadVars : function(obj)
	{
		this.updateVars(obj);
		this._iniCake();
		this._setVarsForm({arr : this.vars.varsStatus.switchList});
		this.iniReload();
	},

	/**
	 *
	*/
	_iniVars : function(obj)
	{
		this._extVars(obj);
		this._iniCake();
		this._setVarsForm({arr : this.vars.varsStatus.switchList});
		this._varsMyRecord = null;
		this.allot({
			insCurrent : this.insCurrent,
			from       : '_iniVars'
		});
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
	_updateVarsForm : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var strVars = 'vars' + obj.arr[i].capitalize();
			this.vars.varsDetail[strVars][0].value = this.vars.varsStatus.flagNow;
		}
		var strVars = 'vars' + this.vars.varsStatus.flagNow.capitalize();
		this.vars.varsDetail.varsDetail = this.vars.varsDetail[strVars];
	},

	/**
	 *
	*/
	_setVarsForm : function(obj)
	{
		var objData = (Object.toJSON(this.vars.varsDetail.templateDetail)).evalJSON();
		for (var i = 0; i < obj.arr.length; i++) {
			var array = [];
			var str = obj.arr[i];
			var strTarget = str + 'Target';
			array.push(objData.switchTarget);
			array.push(objData[strTarget]);
			array.push(objData.jsonSort);
			array.push(objData.myRecord);
			var strVars = 'vars' + str.capitalize();

			this.vars.varsDetail[strVars] = array;
		}
		this._updateVarsForm({arr : this.vars.varsStatus.switchList});
	},

	/**
	 * Cake
	*/
	_iniCake : function()
	{
		this._getCake();
	},

	/**
	 *
	*/
	_getCakeVarsUpdate : function(obj)
	{
		var str = 'flagNow';
		this.vars.varsStatus.flagNow = obj.data[str];
	},

	/**
	 *
	*/
	_setCakeVars : function(obj)
	{
		var str = 'flagNow';
		this._varsCake[str] = this.vars.varsStatus.flagNow;
	},

	/**
	 * Switch
	*/
	_iniSwitch : function()
	{
		if (!this.vars.varsStatus.flagSwitchUse) return;
		this._setSwitch();
	},

	/**
	 *
	*/
	insSwitch : null,
	_setSwitch : function()
	{
		this.insSwitch = new Code_Lib_FormSelect({
			eleInsert  : $(this.insForm.idSelf + 'SwitchTarget'),
			insRoot    : this.insRoot,
			insCurrent : this,
			allot      : this._getSwitchAllot()
		});
	},

	/**
	 *
	*/
	_getSwitchAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == 'eventRemove') insCurrent.insSwitch.stopListener();
			else {
				insCurrent.eventSwitch({arr : insCurrent.vars.varsStatus.switchList, vars : obj.vars});
			}
		};

		return allot;
	},

	/**
	 *
	*/
	eventSwitch : function(obj)
	{
		this.vars.varsStatus.flagNow = obj.vars;
		this._updateVarsForm({arr : this.vars.varsStatus.switchList});
		this.setCake();
		this.iniReload();
	},

	/**
	 * Wrap
	*/
	_iniWrap : function()
	{
		this._extWrap();
	},

	/**
	 * MyRecord
	*/
	_iniMyRecord : function()
	{
		if (!this.vars.varsStatus.flagMyRecordUse) {
			this.eleWrap.down('.codeLibFormWrap', 3).hide();
			return;
		}
		this._setMyRecordWrap();
		this._setMyRecord();
	},

	/**
	 *
	*/
	eleMyRecordWrap : null,
	_setMyRecordWrap : function()
	{
		var eleWrap = $(document.createElement('div'));
		eleWrap.addClassName('codeLibSearchMyRecordWrap');
		this.eleWrap.down('.codeLibFormWrap', 3).insert(eleWrap);
		this.eleMyRecordWrap = eleWrap;
		this.eleMyRecordWrap.setStyle({width : this._getContentWidth() + 'px'});
		if (!this.vars.varsMyRecord.varsFormList.varsDetail.length) {
			this.eleWrap.down('.codeLibFormWrap', 3).hide();
			this.insMyRecord = null;
			return;
		}
		else this.eleWrap.down('.codeLibFormWrap', 3).show();
	},

	/**
	 *
	*/
	insMyRecord : null,
	_setMyRecord : function()
	{
		this.insMyRecord = new Code_Lib_FormList({
			eleInsert  : this.eleMyRecordWrap,
			insRoot    : this.insRoot,
			insCurrent : this.insSelf,
			idSelf     : this.idSelf + 'MyRecord',
			allot      : this._getMyRecordAllot(),
			vars       : this.vars.varsMyRecord.varsFormList
		});
	},




	/**
	 *
	*/
	_getMyRecordAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == 'eventLayout') {
				obj.eleWidth.setStyle({width : insCurrent._getContentWidth() + 'px'});
				insCurrent.insMyRecord.iniReload();
			}
			else if (obj.from == 'eventRemove') insCurrent.insMyRecord.stopListener();
			else if (obj.from.match( /^_mousedownRemove|_mouseupSort|_blurForm$/ )) {
				insCurrent._updateMyRecord({arr : insCurrent.insMyRecord.vars.varsDetail});
				return 1;
			}
			else if (obj.from == '_mousedownBtn') {
				insCurrent._mousedownMyRecord({
					vars : obj.vars,
					arr  : insCurrent.vars.varsStatus.switchList
				});
			}
		};

		return allot;
	},

	/**
	 *
	*/
	_updateMyRecord : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			obj.arr[i].id = i;
		}
		this.vars.varsMyRecord.varsFormList.varsDetail = obj.arr;
		this.rebuildMyRecord();
	},

	/**
		{
			flagNow : str ,
			varsData : [] or str,
		}
	*/
	iniAutoSearch : function(obj)
	{
		this.vars.varsStatus.flagNow = obj.flagNow;
		if (this.vars.varsStatus.flagNow == 'item') {
			this.vars.varsSearchItem.varsDetail = obj.varsData;
		} else {
			var str = 'vars' + this.vars.varsStatus.flagNow.capitalize();
			this.vars.varsDetail[str][1].value = obj.varsData;
		}
		this._updateVarsForm({arr : this.vars.varsStatus.switchList});
		this.setCake();
		this.iniReload();
		this.insForm.hideBtnBottom();
		this._eventFormBtn();
	},


	/**
	 *
	*/
	_mousedownMyRecord : function(obj)
	{
		this.vars.varsStatus.flagNow = obj.vars.vars.flagNow;
		if (this.vars.varsStatus.flagNow == 'item') {
			this.vars.varsSearchItem.varsDetail = obj.vars.vars.varsItem;
		} else {
			var str = 'vars' + this.vars.varsStatus.flagNow.capitalize();
			this.vars.varsDetail[str][1].value = obj.vars.vars.varsValue;
		}
		this.vars.varsSearchSort.varsDetail = obj.vars.vars.varsSort;
		this._updateVarsForm({arr : this.vars.varsStatus.switchList});
		this.setCake();
		this.iniReload();
	},

	/**
	 *
	*/
	_varsMyRecord : null,
	_getMyRecord : function()
	{

		var objData = (Object.toJSON(this.vars.varsMyRecord.varsFormList.templateDetail)).evalJSON();
		var strValue = this._checkMyRecordName({arr : this.vars.varsMyRecord.varsFormList.varsDetail});
		objData.id = new Date().getTime();
		if (this.vars.varsStatus.flagNow == 'item') {
			this.insItem.updateVarsValue();
			objData.value = strValue;
			objData.vars = {
				flagNow  : this.vars.varsStatus.flagNow,
				varsItem : this.insItem.vars.varsDetail,
				varsSort : this.insSort.getValue()
			};
		} else {
			this._getValueForm({arr : this.insForm.vars.varsDetail});
			objData.value = (this.insForm.vars.varsDetail[1].value == '')?
						strValue : this.insForm.vars.varsDetail[1].value;
			objData.vars = {
				flagNow   : this.vars.varsStatus.flagNow,
				varsValue : this.insForm.vars.varsDetail[1].value,
				varsSort  : this.insSort.getValue()
			};
		}
		var jsonData = Object.toJSON(objData.vars);
		if (this._varsMyRecord != jsonData) {
			this.vars.varsMyRecord.varsFormList.varsDetail.unshift(objData);
			this._varsMyRecord = jsonData;
		}

		var arr = this.vars.varsMyRecord.varsFormList.varsDetail;
		for (var i = 1; i < arr.length; i++) {
			arr[i].id = i;
			arr[i].numSort = i;
		}

		return this.vars.varsMyRecord.varsFormList.varsDetail;

	},

	/**
	 *
	*/
	_checkMyRecordName : function(obj)
	{
		if (!obj.arr.length) return this.varsLoad.varsWhole.str.noneTitle;
		var data = {};
		for (var i = 0, j = 1; i < obj.arr.length; i++, j++) {
			if (obj.arr[i].value == this.varsLoad.varsWhole.str.noneTitle) continue;
			var array = obj.arr[i].value.split('-');
			if (array[0] != this.varsLoad.varsWhole.str.noneTitle) continue;
			var str = 'id'+ array[1];
			data[str] = 1;
		}
		for (var i = 0, j = 1; i < obj.arr.length; i++, j++) {
			var str = 'id'+ j;
			if (!data[str]) {
				var data = this.varsLoad.varsWhole.str.noneTitle + '-' + j;
				return data;
			}
		}

		var str = this.varsLoad.varsWhole.str.noneTitle + '-' + obj.arr.length;

		return str;
	},

	/**
	 *
	*/
	rebuildMyRecord : function()
	{
		if (this.insMyRecord) {
			this.insMyRecord.stopListener();
			this.eleMyRecordWrap.remove();
		}
		this._iniCake();
		this._iniMyRecord();
	},

	/**
	 * Value
	*/
	getValue : function()
	{

		var varsSort = this.insSort.getValue();
		var arrayItem;
		this._getValueForm({arr : this.insForm.vars.varsDetail});
		if (this.vars.varsStatus.flagNow == 'item') {
			var varsItem = this.insItem.getValue();
			arrayItem = this._setValueItem({arr : varsItem});
		} else if (this.vars.varsStatus.flagNow == 'tag') {
			arrayItem = this._setValueForm({arr : this.vars.varsStatus.idColumnTagList});
		} else if (this.vars.varsStatus.flagNow == 'word') {
			arrayItem = this._setValueForm({arr : this.vars.varsStatus.idColumnWordList});
		}

		return {
			arrWhere : (arrayItem.length)? arrayItem : [],
			arrOrder : {
				strColumn : varsSort.itemValue,
				flagDesc  : (varsSort.sortValue == 'desc')? 1 : 0
			}
		};

	},

	/**
	 *
	*/
	_getValueForm : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if ($(this.insForm.idSelf + obj.arr[i].id)) obj.arr[i].value = $(this.insForm.idSelf + obj.arr[i].id).value;
		}
	},

	/**
	 *
	*/
	_setValueForm : function(obj)
	{
		var str = this.insForm.vars.varsDetail[1].value;
		str = str.replace(RegExp(this.insRoot.vars.varsSystem.str.space, "g"), ' ');
		str = str.replace(RegExp(',', "g"), this.insRoot.vars.varsSystem.str.comma);
		var arrayValue = str.split(' ');

		var array = [];
		for (var i = 0; i < arrayValue.length; i++) {
			if (arrayValue[i] == '') continue;
			for (var j = 0; j < obj.arr.length; j++) {
				var data;
				if (this.vars.varsStatus.flagNow == 'tag') {
					data = {
						flagType      : 'tag',
						strColumn     : obj.arr[j],
						flagCondition : 'like',
						value         : ' ' + arrayValue[i] + ' '
					};

				} else {
					data = {
						flagType      : 'str',
						strColumn     : obj.arr[j],
						flagCondition : 'like',
						value         : arrayValue[i]
					};
				}

				array.push(data);
			}
		}

		return array;
	},

	/**
	 *
	*/
	_setValueItem : function(obj)
	{
		var array = [];
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].restValue == '') continue;
			var data;
			if (obj.arr[i].flagType == 'tag') {
				data = {
					flagType      : 'tag',
					strColumn     : obj.arr[i].firstValue,
					flagCondition : obj.arr[i].secondValue,
					value         : ' ' + obj.arr[i].restValue + ' '
				};

			} else if (obj.arr[i].flagType.match(/^comma/)) {
				str = obj.arr[i].restValue;
				str = str.replace(RegExp(',', "g"), this.insRoot.vars.varsSystem.str.comma);
				obj.arr[i].restValue = str;
				if (obj.arr[i].flagType.match(/^comma$/)) {
					data = {
						flagType      : 'comma',
						strColumn     : obj.arr[i].firstValue,
						flagCondition : obj.arr[i].secondValue,
						value         : ',' + obj.arr[i].restValue + ','
					};

				} else {
					data = {
						flagType      : obj.arr[i].flagType,
						strColumn     : obj.arr[i].firstValue,
						flagCondition : obj.arr[i].secondValue,
						value         : ',' + obj.arr[i].restValue + ','
					};
				}


			} else {
				data = {
					flagType      : obj.arr[i].flagType,
					strColumn     : obj.arr[i].firstValue,
					flagCondition : obj.arr[i].secondValue,
					value         : obj.arr[i].restValue
				};
			}


			array.push(data);
		}

		return array;
	},

	/**
	 * Form
	*/
	_iniForm : function()
	{
		this._setForm();
	},

	/**
	 *
	*/
	varsForm : null, insForm : null,
	_setForm : function()
	{
		this.insForm = new Code_Lib_Form({
			eleInsertBtnLeft  : this.eleInsertBtnLeft,
			eleInsertBtnRight : this.eleInsertBtnRight,
			eleInsert         : this.eleWrap,
			insRoot           : this.insRoot,
			insCurrent        : this.insSelf,
			idSelf            : this.idSelf + 'Form',
			allot             : this._getFormAllot(),
			vars              : this.vars.varsDetail
		});
	},

	/**
	 *
	*/
	_getFormAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == 'eventLayout') {
				insCurrent.insForm.allot({
					insCurrent : insCurrent.insForm,
					from       : '_getFormAllot-' + obj.from
				});

			} else if (obj.from == 'eventRemove') {
				insCurrent.insForm.stopListener();

			} else if (obj.from == 'eventBtnBottom') {
				var idTarget = obj.vars.vars.vars.idTarget;
				if (idTarget == 'eventFormBtn') insCurrent._eventFormBtn();
				else if (idTarget == 'eventFormBtnSave') insCurrent._eventFormBtnSave();
				else if (idTarget == 'eventFormBtnDelete') insCurrent._eventFormBtnDelete();

			} else {
				obj.insCurrent = insCurrent.insCurrent;
				obj.from = '_getFormAllot-' + obj.from;
				insCurrent.allot(obj);
			}
		};

		return allot;
	},

	/**
	 *
	*/
	_eventFormBtn : function()
	{
		this.allot({
			insCurrent : this.insCurrent,
			from       : 'eventBtn',
			vars       : this.getValue()
		});
	},

	/**
	 *
	*/
	_eventFormBtnSave : function()
	{
		var vars = this._getMyRecord();
		var len = (Object.toJSON(vars)).length;
		if (len >= this.varsLoad.varsWhole.num.limit) {
			alert(this.varsLoad.varsWhole.str.limit);
			return;
		}
		this.allot({
			insCurrent : this.insCurrent,
			from       : 'eventBtnSave',
			vars       : vars
		});
	},


	/**
	 *
	*/
	_eventFormBtnDelete : function()
	{
		this.allot({
			insCurrent : this.insCurrent,
			from       : 'eventBtnDelete',
			vars       : []
		});
	},

	/**
	 *
	*/
	showBtnBottom : function()
	{
		this.insForm.showBtnBottom();
	},

	/**
	 * Sort
	*/
	_iniSort : function()
	{
		this._setSort();
	},

	/**
	 *
	*/
	insSort : null,
	_setSort : function()
	{
		var eleInsert = (this.vars.varsStatus.flagNow == 'item')?
						  this.eleWrap.down('.codeLibFormContent', 1)
						: this.eleWrap.down('.codeLibFormContent', 0);
		eleInsert.setStyle({
			width : this._getContentWidth() + 'px'
		});
		this.insSort = new Code_Lib_SearchSort({
			eleInsert  : eleInsert,
			insRoot    : this.insRoot,
			insCurrent : this.insSelf,
			idSelf     : this.idSelf + 'Sort',
			allot      : this._getSortAllot(),
			vars       : this.vars.varsSearchSort
		});
	},

	/**
	 *
	*/
	_getSortAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == 'eventLayout') {
				obj.eleWidth.setStyle({
					width : insCurrent._getContentWidth() + 'px'
				});
				insCurrent.insSort.iniReload();
			} else if (obj.from == 'eventRemove') {
				insCurrent.insSort.stopListener();
			}
		};

		return allot;
	},

	/**
	 * Item
	*/
	_iniItem : function()
	{
		if (this.vars.varsStatus.flagNow != 'item') return;
		this._setItem();
	},

	/**
	 *
	*/
	insItem : null,
	_setItem : function()
	{
		this.eleWrap.down('.codeLibFormContent', 0).setStyle({
			width : this._getContentWidth() + 'px'
		});
		this.insItem = new Code_Lib_SearchItem({
			eleInsert  : this.eleWrap.down('.codeLibFormContent', 0),
			insRoot    : this.insRoot,
			insCurrent : this.insSelf,
			idSelf     : this.idSelf + 'Item',
			allot      : this._getItemAllot(),
			vars       : this.vars.varsSearchItem
		});
	},

	/**
	 *
	*/
	_getItemAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == 'eventLayout') {
				obj.eleWidth.setStyle({
					width : insCurrent._getContentWidth() + 'px'
				});
				insCurrent.insItem.iniReload();
			} else if (obj.from == 'eventRemove') {
				insCurrent.insItem.stopListener();
			}
		};

		return allot;
	},

	/**
	 *
	*/
	_staticContentForm : { numMarginLeft : 30, numBar : 17},
	_getContentWidth : function()
	{
		var array = (this.eleInsert).style.width.split('px');
		var width = parseFloat(array[0]) - this._staticContentForm.numMarginLeft;

		return  width - this._staticContentForm.numBar;
	},

	/**
	 * Remove
	*/
	eventRemove : function()
	{
		if (this.insForm) this.insForm.allot({insCurrent : this, from : 'eventRemove'});
		if (this.insSwitch) this.insSwitch.allot({insCurrent : this, from : 'eventRemove'});
		if (this.insItem && this.vars.varsStatus.flagNow == 'item') {
			this.insItem.allot({insCurrent : this, from : 'eventRemove'});
		}
		if (this.insSort) this.insSort.allot({insCurrent : this, from : 'eventRemove'});
		if (this.insMyRecord) this.insMyRecord.allot({insCurrent : this, from : 'eventRemove'});
	},

	/**
	 * Layout
	*/
	eventLayout : function()
	{
		if (this.insItem && this.vars.varsStatus.flagNow == 'item') {
			this.insItem.allot({
				insCurrent : this, from : 'eventLayout', eleWidth : this.eleWrap.down('.codeLibFormContent', 0)
			});
		}
		if (this.insSort) {
			this.insSort.allot({
				insCurrent : this,
				from       : 'eventLayout',
				eleWidth   : (this.vars.varsStatus.flagNow == 'item')?
							  this.eleWrap.down('.codeLibFormContent', 1)
							: this.eleWrap.down('.codeLibFormContent', 0)
			});
		}
		if (this.insMyRecord) {
			this.insMyRecord.allot({
				insCurrent : this,
				from       : 'eventLayout',
				eleWidth   : this.eleWrap.down('.codeLibSearchMyRecordWrap', 0)
			});
		}

	}

});
<?php }
}
?>