<?php /* Smarty version 3.1.24, created on 2019-10-06 06:26:37
         compiled from "/app/rucaro/back/tpl/templates/else/core/base/js/lib/searchItem.js" */ ?>
<?php
/*%%SmartyHeaderCode:17044051505d99891dd4acb1_72152248%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '61253d8b6b7c342f3a5cb632f4966d4c632dcbf1' => 
    array (
      0 => '/app/rucaro/back/tpl/templates/else/core/base/js/lib/searchItem.js',
      1 => 1570328741,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '17044051505d99891dd4acb1_72152248',
  'variables' => 
  array (
    'varsLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d99891ddd1750_26841159',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d99891ddd1750_26841159')) {
function content_5d99891ddd1750_26841159 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '17044051505d99891dd4acb1_72152248';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_SearchItem = Class.create(Code_Lib_Search,
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
		this._iniListener();
		this._iniWrap();
		this._iniAdd();
		this._iniLine();
		this._iniRemove();
		this._iniCopy();
	},

	/**
	 *
	*/
	iniReload : function(obj)
	{
		this.updateVarsValue(obj);
		this.stopListener();
		this.removeWrap();
		this._iniWrap();
		this._iniAdd();
		this._iniLine();
		this._iniRemove();
		this._iniCopy();
	},

	/**
	 *
	*/
	_iniVars : function(obj)
	{
		this._extVars(obj);
	},


	/**
	 * Wrap
	*/
	_iniWrap : function()
	{
		this._extWrap();
		this.eleWrap.style.width = this._getWrapWidth() + 'px';
	},


	/**
	 *
	*/
	updateVarsValue : function(obj)
	{
		this._updateVarsValueChild({
			arr         : this.vars.varsDetail,
			flagThrough : (obj)? obj.flagThrough : 0
		});
	},

	/**
	 *
	*/
	_updateVarsValueChild : function(obj)
	{
		if (obj.flagThrough) return;
		for (var i = 0; i < obj.arr.length; i++) {
			if ($(this.idSelf + 'LineFirst' + obj.arr[i].id)) {
				obj.arr[i].firstValue = $(this.idSelf + 'LineFirst' + obj.arr[i].id).value;
			}
			if ($(this.idSelf + 'LineSecond' + obj.arr[i].id)) {
				obj.arr[i].secondValue = $(this.idSelf + 'LineSecond' + obj.arr[i].id).value;
			}
			if ($(this.idSelf + 'LineRest' + obj.arr[i].id)) {
				obj.arr[i].restValue = $(this.idSelf + 'LineRest' + obj.arr[i].id).value;
			}
		}
	},

	/**
	 *
	*/
	_iniLine : function()
	{
		this._setLineWrap();
		this._setLine({arr : this.vars.varsDetail});
	},

	/**
	 *
	*/
	eleWrapLine : null,
	_setLineWrap : function()
	{
		var ele = $(document.createElement('div'));
		ele.addClassName('codeLibSearchItemLineWrap');
		this.eleWrap.insert(ele);
		this.eleWrapLine = ele;
	},

	/**
	 *
	*/
	_staticLine : {numIdle : 10, numMargin : 5, numBlock : 16, numFirstOption : 20},
	_setLine : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {

			var eleForm = $(document.createElement('form'));
			this.eleWrapLine.insert(eleForm);

			var eleLine = $(document.createElement('div'));
			eleLine.unselectable = 'on';
			eleLine.addClassName('codeLibSearchItemLine');
			eleLine.id = this.idSelf + 'Line' + obj.arr[i].id;
			eleLine.setStyle({
				width : this._getWrapWidth() + 'px'
			});
			eleLine.setStyle({
				marginTop : (this._staticLine.numMargin) + 'px'
			});

			eleForm.insert(eleLine);
			if (this.vars.varsStatus.flagCopyUse) {
				var ele = $(document.createElement('span'));
				ele.addClassName('codeLibSearchItemCopy');
				ele.addClassName('codeLibBaseCursorPointer');
				eleLine.insert(ele);
			}

			if (this.vars.varsStatus.flagRemoveUse) {
				var ele = $(document.createElement('span'));
				ele.addClassName('codeLibSearchItemRemove');
				ele.addClassName('codeLibBaseCursorPointer');
				eleLine.insert(ele);
			}

			var width = this._getLineWidth();
			var widthFirst = Math.floor(width * 0.3);
			var widthSecond = Math.floor(width * 0.3);
			var numBlock = 0;
			var widthRest = width - widthFirst - widthSecond - numBlock;

			var eleFirst;
			eleFirst = $(document.createElement('select'));
			eleFirst.addClassName('codeLibSearchItemFirst');
			eleFirst.addClassName('codeLibBaseMarginLeftFive');
			eleFirst.value = obj.arr[i].firstValue;
			eleFirst.id = this.idSelf + 'LineFirst' + obj.arr[i].id;
			eleFirst.style.width = widthFirst + 'px';
			var optionFirst = (Object.toJSON(this.vars.templateDetail.firstOption)).evalJSON();
			this._setLineSelect({
				flag      : 'first',
				arr       : optionFirst,
				now       : obj.arr[i].firstValue,
				eleInsert : eleFirst,
				vars      : obj.arr[i]
			});
			eleLine.insert(eleFirst);

			var eleRest;
			if (obj.arr[i].flagOption) {
				eleRest = $(document.createElement('select'));
				var str = obj.arr[i].flagOption;
				if (str == 'window') {


				} else {
					var optionRest = (Object.toJSON(this.vars.templateDetail.restOption[str])).evalJSON();
					this._setLineSelect({
						flag      : 'rest',
						arr       : optionRest,
						now       : obj.arr[i].restValue,
						eleInsert : eleRest,
						vars      : obj.arr[i]
					});
					if (obj.arr[i].restValue) {
						eleRest.value = obj.arr[i].restValue;

					} else {
						if (optionRest.length) {
							eleRest.value = this._setLineSelectValue({
								arr : optionRest,
							});

						} else {
							eleRest.value = obj.arr[i].restValue;
						}
					}
				}

			} else {
				eleRest = $(document.createElement('input'));
				eleRest.type = 'text';
				eleRest.value = obj.arr[i].restValue;
			}

			eleRest.addClassName('codeLibSearchItemRest');
			eleRest.addClassName('codeLibBaseMarginLeftFive');


			eleRest.id = this.idSelf + 'LineRest' + obj.arr[i].id;
			eleRest.style.width = widthRest + 'px';
			eleLine.insert(eleRest);
			if (this.vars.varsStatus.flagUse && obj.arr[i].flagType == 'stamp') {
				this._iniCalender({
					id        : this.idSelf + 'Line' + obj.arr[i].id,
					eleInsert : eleRest
				});

			} else if (obj.arr[i].flagType == 'num') {
				this._iniCheck({
					id        : this.idSelf + 'Line' + obj.arr[i].id,
					eleInsert : eleRest
				});
			}

			var eleSecond;
			eleSecond = $(document.createElement('select'));
			eleSecond.addClassName('codeLibSearchItemSecond');
			eleSecond.addClassName('codeLibBaseMarginLeftFive');
			eleSecond.value = obj.arr[i].secondValue;
			eleSecond.id = this.idSelf + 'LineSecond' + obj.arr[i].id;
			eleSecond.style.width = widthSecond + 'px';
			var optionSecond;
			var str = obj.arr[i].flagType + 'Option';
			optionSecond = (Object.toJSON(this.vars.templateDetail[str])).evalJSON();
			this._setLineSelect({
				flag      : 'second',
				arr       : optionSecond,
				now       : obj.arr[i].secondValue,
				eleInsert : eleSecond,
				vars      : obj.arr[i]
			});
			eleLine.insert(eleSecond);
		}
	},

	/**
	 *
	*/
	_setLineCalender : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var ele = $(document.createElement('option'));
			ele.value = obj.arr[i].value;
			if (obj.now == obj.arr[i].value) ele.selected = 'true';
			ele.insert(obj.arr[i].title);
			obj.eleInsert.insert(ele);
		}
		if (obj.flag == 'first') {
			this.insListener.set({
				bindAsEvent : 0, insCurrent : this, event : 'change',strFunc : '_updateLineSelectFirstVar',
				ele : obj.eleInsert, vars : {vars : obj.vars}
			});
		}
	},

	/**
	 *
	*/
	_setLineSelect : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var ele = $(document.createElement('option'));
			ele.value = obj.arr[i].value;
			if (obj.now == obj.arr[i].value) ele.selected = 'true';
			if (obj.arr[i].flagDisabled)  ele.disabled = true;
			ele.insert(obj.arr[i].strTitle);
			obj.eleInsert.insert(ele);
		}
		if (obj.flag == 'first') {
			this.insListener.set({
				bindAsEvent : 0, insCurrent : this, event : 'change', strFunc : '_updateLineSelectFirstVar',
				ele : obj.eleInsert, vars : {vars : obj.vars}
			});
		}

	},

	/**
	 *
	*/
	_setLineSelectValue : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].flagDisabled) continue;
			return obj.arr[i].value;
		}

	},
	/**
	 *
	*/
	_updateLineSelectFirstVar : function(obj)
	{
		this._updateLineSelectFirstVarChild({
			arr  : this.vars.varsDetail,
			vars : obj.vars
		});
		this.iniReload({flagThrough : 1});
	},

	/**
	 *
	*/
	_updateLineSelectFirstVarChild : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.vars.id == obj.arr[i].id) {

				obj.arr[i].firstValue = $(this.idSelf + 'LineFirst' + obj.arr[i].id).value;
				var array = obj.arr[i].firstValue.split('-');
				obj.arr[i].flagType = array[0];
				obj.arr[i].flagOption = (array[2])? array[2] : '';
				if (obj.arr[i].flagType == 'str') {
					obj.arr[i].secondValue = this.vars.templateDetail.strOption[0];

				} else if (obj.arr[i].flagType == 'stamp') {
					obj.arr[i].secondValue = this.vars.templateDetail.stampOption[0];

				} else if (obj.arr[i].flagType == 'num') {
					obj.arr[i].secondValue = this.vars.templateDetail.numOption[0];

				}
				$(this.idSelf + 'LineRest' + obj.arr[i].id).value = '';

				obj.arr[i].restValue = '';
				return;
			}
		}
	},

	/**
	 *
	*/
	_getLineWidth : function()
	{
		var width = this._getWrapWidth();
		if (this.vars.varsStatus.flagAddUse) {
			width = width - this._staticLine.numMargin - this._staticLine.numBlock;
		}
		if (this.vars.varsStatus.flagRemoveUse) {
			width = width - this._staticLine.numMargin - this._staticLine.numBlock;
		}
		width -= this._staticLine.numMargin * 2;
		width -= this._staticLine.numIdle;
		return width;
	},

	/**
	 * Calender
	*/
	insCalender : null,
	_iniCalender : function(obj)
	{
		var insCalender = new Code_Lib_CalenderFormNavi({
			eleInsert  : obj.eleInsert,
			insRoot    : this.insRoot,
			insCurrent : this.insSelf,
			idSelf     : obj.id + 'Calender',
			allot      : function(){},
			vars       : this.vars.varsCalender
		});
		this._setListener({ins : insCalender});
	},

	/**
	 * Calender
	*/
	insCheck : null,
	_iniCheck : function(obj)
	{
		var insCheck = new Code_Lib_FormFocus({
			eleInsert  : obj.eleInsert,
			insRoot    : this.insRoot,
			insCurrent : this,
			idSelf     : obj.id + 'Check',
			allot      : this._getCheckAllot()
		});
		this._setListener({ins : insCheck});
	},

	/**
	 *
	*/
	_getCheckAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == '_blurCheck') return insCurrent._setCheck({value : obj.vars});
		};

		return allot;
	},

	/**
	 *
	*/
	_setCheck : function(obj)
	{
		var insEscape = new Code_Lib_Escape();
		var insCheck = new Code_Lib_CheckValue();
		obj.value = insEscape.get({
			flagType : 'strToNum',
			data     : obj.value
		});
		var flag = insCheck.checkValueWord({
			flagType : 'num',
			value    : obj.value
		});
		if (flag) return '';
		else return obj.value;

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
	_removeBlock : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == obj.idAttest) num = i;
		}
		obj.arr = obj.arr.slice(0, num).concat(obj.arr.slice((num + 1), obj.arr.length));

		return obj.arr;
	},

	/**
	 * Remove
	*/
	_iniRemove : function()
	{
		if (!this.vars.varsStatus.flagRemoveUse) return;
		this._setRemoveListener({arr : this.vars.varsDetail});
	},

	/**
	 *
	*/
	_setRemoveListener : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			this.insListener.set({
				bindAsEvent : 1, insCurrent : this, event : 'mousedown',
				strFunc : '_mousedownRemove',
				ele : $(this.idSelf + 'Line' + obj.arr[i].id).down('.codeLibSearchItemRemove',0),
				vars : {vars : obj.arr[i]}
			});
		}
	},

	/**
	 *
	*/
	_mousedownRemove : function(evt, obj) {
		if (obj) evt.stop();
		else obj = evt;
		this._updateRemove({vars : obj.vars});
		this.iniReload();
	},

	/**
	 *
	*/
	_updateRemove : function(obj)
	{
		this.vars.varsDetail = this._removeBlock({
			arr      : this.vars.varsDetail,
			idAttest : obj.vars.id
		});
	},

	/**
	 * Copy
	*/
	_iniCopy : function()
	{
		if (!this.vars.varsStatus.flagCopyUse) return;
		this._setCopyListener({arr : this.vars.varsDetail});
	},

	/**
	 *
	*/
	_setCopyListener : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			this.insListener.set({
				bindAsEvent : 1, insCurrent : this, event : 'mousedown', strFunc : '_mousedownCopy',
				ele : $(this.idSelf + 'Line' + obj.arr[i].id).down('.codeLibSearchItemCopy', 0),
				vars : {vars : obj.arr[i]}
			});
		}
	},

	/**
	 *
	*/
	_mousedownCopy : function(evt, obj) {
		if (obj) evt.stop();
		else obj = evt;

		if (this.vars.varsDetail.length >= this.varsLoad.varsWhole.num.limit) {
			var varsData = (Object.toJSON(this.varsLoad.varsWhole.str.limit)).evalJSON();
			varsData = varsData.replace(/<?php echo '<%'; ?>
replace<?php echo '%>'; ?>
/, this.varsLoad.varsWhole.num.limit);
			alert(varsData);
			return;
		}
		this._updateCopy({vars : obj.vars});
		this.iniReload();
	},

	/**
	 *
	*/
	_updateCopy : function(obj)
	{
		this.updateVarsValue();
		this._updateCopyChild({
			arr  : this.vars.varsDetail,
			vars : obj.vars
		});
	},

	/**
	 *
	*/
	_updateCopyChild : function(obj)
	{
		var data;
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.vars.id == obj.arr[i].id) {
				data = (Object.toJSON(obj.arr[i])).evalJSON();
				data.id = new Date().getTime();
				break;
			}
		}
		this.vars.varsDetail.unshift(data);
	},

	/**
	 * Add
	*/
	_iniAdd : function()
	{
		this._setAddWrap();
		this._setAdd();
	},

	/**
	 *
	*/
	eleWrapAdd : null,
	_setAddWrap : function()
	{
		var ele = $(document.createElement('div'));
		ele.addClassName('codeLibSearchItemAddWrap');
		this.eleWrap.insert(ele);
		this.eleWrapAdd = ele;
		this.eleWrapAdd.style.width = this._getLineWidth() + 'px';
		this.eleWrapAdd.style.height = '20px';
	},

	/**
	 *
	*/
	_setAdd : function()
	{
		var insBtn = new Code_Lib_Btn();
		insBtn.iniBtn({
			eleInsert  : this.eleWrapAdd,
			id         : this.idSelf + 'BtnAdd',
			strFunc    : '_mousedownAdd',
			strTitle   : this.varsLoad.varsWhole.str.add,
			insCurrent : this.insSelf
		});
		this._setListener({ins : insBtn});
	},

	/**
	 *
	*/
	_mousedownAdd : function()
	{
		if (this.vars.varsDetail.length >= this.varsLoad.varsWhole.num.limit) {
			var varsData = (Object.toJSON(this.varsLoad.varsWhole.str.limit)).evalJSON();
			varsData = varsData.replace(/<?php echo '<%'; ?>
replace<?php echo '%>'; ?>
/, this.varsLoad.varsWhole.num.limit);
			alert(varsData);
			return;
		}
		this._updateAdd();
		this.iniReload();
	},

	/**
	 *
	*/
	_updateAdd : function(obj)
	{
		var data = (Object.toJSON(this.vars.templateDetail.varsDetail)).evalJSON();
		data.id = new Date().getTime();
		this.vars.varsDetail.unshift(data);
	},


	/**
	 * Value
	*/
	getValue : function()
	{
		this.updateVarsValue();
		this._checkValue({arr : this.vars.varsDetail});
		this._blurValue({arr : this.vars.varsDetail});

		return this._varsValue;
	},

	/**
	 *
	*/
	_varsValue : null,
	_blurValue : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if ($(this.idSelf + 'LineFirst' + obj.arr[i].id)) $(this.idSelf + 'LineFirst' + obj.arr[i].id).blur();
			if ($(this.idSelf + 'LineSecond' + obj.arr[i].id)) $(this.idSelf + 'LineSecond' + obj.arr[i].id).blur();
			if ($(this.idSelf + 'LineRest' + obj.arr[i].id)) $(this.idSelf + 'LineRest' + obj.arr[i].id).blur();
		}
	},

	/**
	 *
	*/
	_checkValue : function(obj)
	{
		this._varsValue = [];
		var insEscape = new Code_Lib_Escape();
		var insCheck = new Code_Lib_CheckValue();
		for (var i = 0; i < obj.arr.length; i++) {
			var flag;
			var rowData = (Object.toJSON(obj.arr[i])).evalJSON();
			var array = obj.arr[i].firstValue.split('-');
			rowData.firstValue = array[1];
			if (obj.arr[i].flagType == 'stamp') {
				flag = insCheck.checkValueFormat({
					flagType  : 'date',
					flagArray : 0,
					value     : obj.arr[i].restValue
				});
				if (flag) obj.arr[i].restValue = '';
				else {
					var array = obj.arr[i].restValue.split('/');
					var objTime = this.insRoot.insTimeZone.adjustTime({
						stamp : new Date(array[0], parseFloat(array[1]) - 1 , array[2]).getTime()
					});
					if (array[0] < 1970 ) {
						flag = 1;
						obj.arr[i].restValue = '';
					}
					else rowData.restValue = objTime.stampServer;
				}
			} else if (obj.arr[i].flagType == 'num') {
				obj.arr[i].restValue = insEscape.get({
					flagType : 'strToNum',
					data     : obj.arr[i].restValue
				});
				flag = insCheck.checkValueWord({
					flagType : 'num',
					value    : obj.arr[i].restValue
				});
				if (flag) obj.arr[i].restValue = '';
				else obj.arr[i].restValue = parseFloat(obj.arr[i].restValue);
			}
			if (!flag) this._varsValue.push(rowData);
		}
	}

});


<?php }
}
?>