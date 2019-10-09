<?php /* Smarty version 3.1.24, created on 2019-06-16 09:02:07
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/formSensitive.js" */ ?>
<?php
/*%%SmartyHeaderCode:10825533125d06058ff19e70_57222331%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fa9080308cd7b05c04464422ffafd532dcd415cf' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/formSensitive.js',
      1 => 1560675139,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '10825533125d06058ff19e70_57222331',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d06058ff1e196_74084780',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d06058ff1e196_74084780')) {
function content_5d06058ff1e196_74084780 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '10825533125d06058ff19e70_57222331';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_FormSensitive = Class.create(Code_Lib_ExtLib,
{

	/**
	 *
	*/
	initialize : function(obj)
	{
		this._extAllot(obj);
		this._iniVars(obj);
		this._iniListener();
		this._iniSense();
	},

	/**
	 *
	 */
	iniReload : function()
	{

	},

	/**
	 *
	*/
	_iniVars : function(obj)
	{
		this._extVars(obj);
		this._setVarsKeyNum({arr : this.vars.varsDetail});
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
		idTarget : '',
		strKey   : '',
		vars     : multi,
	*/
	updateVarsTarget : function(obj)
	{
		var num = this._varsKeyNum[obj.idTarget];
		if (obj.strKey) {
			this.vars.varsDetail[num][obj.strKey] = obj.vars;
		} else {
			this.vars.varsDetail[num] = obj.vars;
		}

	},

	/**
	 * {idTarget : ''}
	*/
	getVarsTarget : function(obj)
	{
		var num = this._varsKeyNum[obj.idTarget];

		return this.vars.varsDetail[num];
	},

	/**
	 *
	*/
	_iniListener : function()
	{
		this._extListener();
	},

	/**
	 * sense
	*/
	_iniSense : function()
	{
		this._setSense({arr : this.vars.varsDetail});
	},

	/**
	 *
	 */
	resetSense : function()
	{
		this.stopListener();
		this._setSense({arr : this.vars.varsDetail});
	},

	/**
	 *
	*/
	_setSense : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var ele = $(this.vars.varsStatus.id + obj.arr[i].id);
			if (ele) {
				ele.removeClassName('codeLibBaseCursorPointer');
				ele.removeClassName('codeLibBaseBgNoactive');
				ele.removeClassName('codeLibFormSensitiveBgActive');
			}


			if (obj.arr[i].flagForm == 'none' || obj.arr[i].flagForm == '') {
				continue;
			}
			var id = obj.arr[i].id;

			if (obj.arr[i].flagForm == 'active') {
				ele.addClassName('codeLibBaseCursorPointer');
				ele.addClassName('codeLibFormSensitiveBgActive');
				this.insListener.set({
					bindAsEvent : 1, insCurrent : this, event : 'mousedown',
					strFunc : '_mousedownBtn', ele : ele, vars : { ele : ele, idTarget : id }
				});

			} else {
				ele.addClassName('codeLibBaseBgNoactive');
			}
		}

	},

	/**
	 *
	 */
	_mousedownBtn : function(evt, obj)
	{
		evt.stop();
		var ele = $(this.vars.varsStatus.id + obj.idTarget + 'Offset');
		if (!ele) {
			ele = obj.ele;
		}
		this._setEdit({ele : ele, idTarget : obj.idTarget});
	},

	/**
	 *
	 */
	_varsEditPrev : {},
	_setEdit : function(obj)
	{
		var num = this._varsKeyNum[obj.idTarget];
		var vars = this.vars.varsDetail[num];
		this._varsEditPrev = (Object.toJSON(vars)).evalJSON();

		this.allot({
			insCurrent : this,
			from       : '_setEdit',
			vars       : vars
		});

		this.insFormTemp = new Code_Lib_FormTemp({
			insRoot    : this.insRoot,
			eleInsert  : $(this.vars.varsStatus.id + obj.idTarget).up('.codeLibWindow', 0),
			insCurrent : this,
			idSelf     : this.idSelf + 'Edit',
			allot      : this._getEditAllot(),
			vars       : this._getEditVars({
				ele  : obj.ele,
				vars : vars
			})
		});
	},

	/**
	 *
	 */
	_getEditAllot : function()
	{
		var allot = function(obj) {
			var insCurrent = obj.insCurrent;
			if (obj.from == 'removeWrap') {
				var strError = insCurrent._checkValueEdit({
					vars       : obj.vars
				});
				insCurrent.allot({
					insCurrent : insCurrent.insCurrent,
					from       : 'removeWrap',
					vars       : obj.vars,
					varsPrev   : insCurrent._varsEditPrev,
					strError   : strError
				});
			}
		};

		return allot;
	},

	/**
	 *
	 */
	checkVarsTarget : function(obj)
	{
		var insCheck = new Code_Lib_CheckValue();

		var num = this._varsKeyNum[obj.idTarget];
		var varsTemp = (Object.toJSON(this.vars.varsDetail[num])).evalJSON();
		varsTemp.value = obj.value;

		var varsChecked = insCheck.checkValue({
			arr : [varsTemp]
		});
		var strError = this._getErrorStr({
			flagType : 'common',
			arr      : varsChecked
		});

		return strError;
	},

	/**
	 *
	 */
	_checkValueEdit : function(obj)
	{
		var num = this._varsKeyNum[obj.vars.id];
		if (this.vars.varsDetail[num].flagNumEscape) {
			obj.vars.value = this._checkNumValue({value : obj.vars.value});
		}
		this.vars.varsDetail[num].value = obj.vars.value;

		var insCheck = new Code_Lib_CheckValue();
		var varsChecked = insCheck.checkValue({
			arr : [obj.vars]
		});
		var strError = this._getErrorStr({
			flagType : 'common',
			arr      : varsChecked
		});

		return strError;
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

		return strValue;
	},

	/**
	 *
	*/
	_getErrorStr : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var flag = this._getValueErrorComment({
				arr      : obj.arr[i].arrayError,
				id       : obj.arr[i].id,
				flagType : obj.flagType
			});
			return flag;
		}
	},

	/**
	 *
	*/
	_getValueErrorComment : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].flagNow) {
				return obj.arr[i].strComment[obj.flagType];
			}
		}
		return '';
	},

	/**
	 *
	 */
	_getEditVars : function(obj)
	{
		var varsStyle = this.allot({
			insCurrent : this.insCurrent,
			from       : '_getEditVars'
		});

		var varsFormTemp = (Object.toJSON(this.vars.varsTmpl.varsFormTemp)).evalJSON();
		varsFormTemp.varsStatus.numTop = obj.ele.offsetTop + varsStyle.numTop;
		varsFormTemp.varsStatus.numLeft = obj.ele.offsetLeft + varsStyle.numLeft;
		varsFormTemp.varsDetail = obj.vars;
		varsFormTemp.varsDetail.numWidth = obj.ele.offsetWidth + varsStyle.numWidth;
		varsFormTemp.varsDetail.numHeight = obj.ele.offsetHeight + varsStyle.numHeight;

		return varsFormTemp;
	}

});

<?php }
}
?>