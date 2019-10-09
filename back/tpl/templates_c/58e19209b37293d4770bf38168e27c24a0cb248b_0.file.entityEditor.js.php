<?php /* Smarty version 3.1.24, created on 2019-10-06 09:21:24
         compiled from "/app/rucaro/back/tpl/templates/else/plugin/accounting/js/entityEditor.js" */ ?>
<?php
/*%%SmartyHeaderCode:12438675595d99b214953f96_92989176%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '58e19209b37293d4770bf38168e27c24a0cb248b' => 
    array (
      0 => '/app/rucaro/back/tpl/templates/else/plugin/accounting/js/entityEditor.js',
      1 => 1570328744,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12438675595d99b214953f96_92989176',
  'variables' => 
  array (
    'varsLoad' => 0,
    'numNews' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d99b214a11b38_80766024',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d99b214a11b38_80766024')) {
function content_5d99b214a11b38_80766024 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '12438675595d99b214953f96_92989176';
?>

/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_EntityEditor = Class.create(Code_Lib_ExtEditor,
{

	vars : <?php echo $_smarty_tpl->tpl_vars['varsLoad']->value;?>
,
	numNews : <?php echo $_smarty_tpl->tpl_vars['numNews']->value;?>
,


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
	_iniVars : function(obj)
	{
		this._extVars(obj);
		this.vars.portal.varsDetail.varsDetail = this.varsChild.varsDetail;
		this._varsSearch = this.insCurrent.getVarsSearch();

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
				var vars = insCurrent.insDetail.getFormValue();
				vars.StrTitle = (vars.StrTitle)? vars.StrTitle : '';
				insCurrent.insNavi.eventMove({vars : vars});
				insCurrent._backDetailContentValue();

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
	_getDetailFormContentVars : function(obj)
	{
		this._getDetailFormListVars(obj);
	},


	/**
	 *
	*/
	_setDetailContent : function()
	{
		this._iniDetailFormList();
	},

	/**
	 *
	*/
	_eventLayoutDetailContent : function()
	{
		if (!this.insDetail.insForm) return;
		this._iniDetailFormList();

	},

	/**
	 *
	*/
	_preEventLayoutDetailContent : function()
	{
		if (!this.insDetail.insForm) return;
		this._getDetailFormListVars({arr : this.insDetail.insForm.vars.varsDetail});

	},

	/**
	 *
	*/
	_eventRemoveDetailContent : function()
	{
		if (!this.insDetail.insForm) return;
		this._eventRemoveDetailFormList({arr : this.insDetail.insForm.vars.varsDetail});

	},

	/**
	 *
	*/
	_setDetailContentValue : function()
	{
		this._setDetailFormListValue({arr : this.insDetail.insForm.vars.varsDetail});
	},


	/**
	 *
	*/
	_backDetailContentValue : function()
	{
		this._backDetailFormListValue({arr : this.insDetail.insForm.vars.varsDetail});
	},

	/**
	 *
	*/
	_iniDetailFormList : function()
	{
		this._extDetailFormList();
	},

	/**
	 *
	*/
	_getDetailFormListAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			var insParent = insCurrent.insCurrent;
			if (obj.from == '_mousedownAdd') {
				obj.arr = insParent.insDetail.insForm.vars.varsDetail;
				var num = 0;
				for (var i = 0; i < obj.arr.length; i++) {
					if (!obj.arr[i].varsFormList) continue;
					if (insCurrent.idSelf == insParent.insDetail.insForm.idSelf + 'DetailFormList' + obj.arr[i].id) {
						if (obj.arr[i].id == 'IdEntity') {
							insParent.insRoot.insChoice.setBoot({
								flagId       : obj.arr[i].id,
								idTarget     : 'PluginAccountingEntityWithoutConfig',
								idModule     : 'Accounting',
								flagCheckUse : 0,
								strFunc      : 'setDetailFormListChoiceValue',
								numTop       : insParent._staticDetailFormList.numTop + $(insParent.insWindow.idWindow).offsetTop,
								numLeft      : insParent._staticDetailFormList.numLeft + $(insParent.insWindow.idWindow).offsetLeft,
								insCurrent   : insParent
							});
						}
						break;
					}
					num++;
				}

				return 1;
			}
		};

		return allot;
	},

	/**
	 *
	*/
	setDetailFormListChoiceValue : function(obj)
	{
		if (!obj.vars) return;
		this.insDetail.setValue();
		obj.arr = this.insDetail.insForm.vars.varsDetail;
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormList) continue;
			if (obj.arr[i].id == obj.flagId) {
				var data = (Object.toJSON(obj.arr[i].varsFormList.templateDetail)).evalJSON();
				data.value = obj.vars.strTitle;
				obj.arr[i].varsFormList.varsDetail[0] = data;
				obj.arr[i].value = obj.vars.vars.idTarget;
				this.vars.portal.varsDetail.varsDetail = obj.arr;
				this._eventRemoveDetailContent();
				this._setDetailStart();
				return;
			}
			num++;
		}

	},

	/**
	 *
	*/
	_setDetailFormListValue : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormList) continue;
			this.insDetail.setFormValue({
				idTarget : obj.arr[i].id,
				value    : {
					value      : obj.arr[i].value,
					varsDetail : this._varsDetailFormList[num].insList.vars.varsDetail
				}
			});
			num++;
		}
	},

	/**
	 *
	*/
	_backDetailFormListValue : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].varsFormList) continue;
			this.insDetail.setFormValue({
				idTarget : obj.arr[i].id,
				value    : obj.arr[i].value.value
			});
			num++;
		}
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
			if (this.insDetail.checkForm({flagType : 'common'})) return;
			var vars = this.insDetail.getFormValue();
			this._eventValue({
				vars     : vars,
				idTarget : (obj.flag == 'edit')? this.varsChild.idTarget : ''
			});
		}

		this._varsDetailConnect = obj;
		this._sendDetailConnect();
	},

	/**
	 *
	*/
	_iniCss : function()
	{
		this._extCss();
	}
});
<?php }
}
?>