<?php /* Smarty version 3.1.24, created on 2016-08-18 12:51:01
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/js/lib/search.js" */ ?>
<?php
/*%%SmartyHeaderCode:194042921257b5af35eb3c91_63491961%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '334ab6c2f2cdee6c718d17071c3d36be2b17f360' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/js/lib/search.js',
      1 => 1471523679,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '194042921257b5af35eb3c91_63491961',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b5af35ed6054_69220819',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b5af35ed6054_69220819')) {
function content_57b5af35ed6054_69220819 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '194042921257b5af35eb3c91_63491961';
?>

/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_Lib_Search = Class.create(Code_Lib_Search,
{
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
			array.push(objData.logApply);
			array.push(objData.jsonSort);
			array.push(objData.myRecord);
			var strVars = 'vars' + str.capitalize();

			this.vars.varsDetail[strVars] = array;
		}
		this._updateVarsForm({arr : this.vars.varsStatus.switchList});
	},

	/**
	 * MyRecord
	*/
	_iniMyRecord : function()
	{
		if (!this.vars.varsStatus.flagMyRecordUse) {
			this.eleWrap.down('.codeLibFormWrap', 4).hide();
			return;
		}
		this._setMyRecordWrap();
		this._setMyRecord();
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
		var str = 'vars' + this.vars.varsStatus.flagNow.capitalize();
		this.vars.varsDetail[str][2].value = obj.vars.vars.flagApply;
		/*
		this._setMyRecordFlagApply({
			arr         : this.vars.varsDetail,
			valueTarget : obj.vars.vars.flagApply
		});
		*/

		this.vars.varsSearchSort.varsDetail = obj.vars.vars.varsSort;
		this._updateVarsForm({arr : this.vars.varsStatus.switchList});
		this.setCake();
		this.iniReload();
	},

	/**
	 *
	*/
	_setMyRecordFlagApply : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == 'FlagApply') {
				obj.arr[i].value = obj.valueTarget;
			}

		}
	},

	/**
	 *
	*/
	eleMyRecordWrap : null,
	_setMyRecordWrap : function()
	{
		var eleWrap = $(document.createElement('div'));
		eleWrap.addClassName('codeLibSearchMyRecordWrap');
		this.eleWrap.down('.codeLibFormWrap', 4).insert(eleWrap);
		this.eleMyRecordWrap = eleWrap;
		this.eleMyRecordWrap.setStyle({width : this._getContentWidth() + 'px'});
		if (!this.vars.varsMyRecord.varsFormList.varsDetail.length) {
			this.eleWrap.down('.codeLibFormWrap', 4).hide();
			this.insMyRecord = null;
			return;
		}
		else this.eleWrap.down('.codeLibFormWrap', 4).show();
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
				flagNow   : this.vars.varsStatus.flagNow,
				flagApply : $(this.insForm.idSelf + 'FlagApply').value,
				varsItem  : this.insItem.vars.varsDetail,
				varsSort  : this.insSort.getValue()
			};
		} else {
			this._getValueForm({arr : this.insForm.vars.varsDetail});
			objData.value = (this.insForm.vars.varsDetail[1].value == '')?
						strValue : this.insForm.vars.varsDetail[1].value;
			objData.vars = {
				flagNow   : this.vars.varsStatus.flagNow,
				flagApply : $(this.insForm.idSelf + 'FlagApply').value,
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
			arrWhere  : (arrayItem.length)? arrayItem : [],
			flagApply : $(this.insForm.idSelf + 'FlagApply').value,
			arrOrder  : {
				strColumn : varsSort.itemValue,
				flagDesc  : (varsSort.sortValue == 'desc')? 1 : 0
			}
		};
	}




});
<?php }
}
?>