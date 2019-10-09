<?php /* Smarty version 3.1.24, created on 2019-06-16 09:02:08
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/templateLayout.js" */ ?>
<?php
/*%%SmartyHeaderCode:2808662145d0605901890a0_90054030%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c07bad94029022f5b238947a3bd06319dbecf172' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/templateLayout.js',
      1 => 1560675140,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2808662145d0605901890a0_90054030',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d06059018d223_86693432',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d06059018d223_86693432')) {
function content_5d06059018d223_86693432 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '2808662145d0605901890a0_90054030';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_TemplateLayout = Class.create(Code_Lib_ExtLib,
{

	/**
	 *
	*/
	initialize : function(obj)
	{
		this._extAllot(obj);
		this._iniVars(obj);
		this._iniLayout();
		if (this.vars.varsStatus.flagNaviUse) this._iniNavi();
		if (this.vars.varsStatus.flagListUse) this._iniList();
		if (this.vars.varsStatus.flagDetailUse) this._iniDetail();

	},

	/**
	 *
	*/
	insLayout : null,
	_iniLayout : function()
	{

		this.insLayout = new Code_Lib_Layout({
			eleInsert  : this.eleInsert,
			idWindow   : this.idWindow,
			insRoot    : this.insRoot,
			insCurrent : this,
			idSelf     : this.idSelf + 'Layout',
			allot      : this._getLayoutAllot(),
			vars       : this.vars.varsLayout
		});

	},

	/**
	 *
	*/
	_getLayoutAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from.match(/^_mouseupResize|_mouseupMove|_mousedownSwitch$/)) {
				insCurrent.eventLayout();

			} else if (obj.from.match(/^_preMouseupResize|_preMouseupMove|_preMousedownSwitch$/)) {
				insCurrent.preEventLayout();

			}
		};

		return allot;
	},

	/**
	 *
	*/
	_iniVars : function(obj)
	{
		this._extVars(obj);
	},

	/**
	 *
	*/
	_iniNavi : function()
	{
		if (this.vars.varsStatus.flagNaviToolUse) this._iniNaviTool();
		this._iniNaviUnder();
	},

	/**
	 *
	*/
	insNaviTool : null,
	_iniNaviTool : function()
	{
		this.insNaviTool = new Code_Lib_BtnTool({
			eleInsert  : $(this.idSelf + 'LayoutNaviBox').down('.codeLibTemplateNormalBoxMiddleMiddle', 0),
			insRoot    : this.insRoot,
			insCurrent : this,
			idSelf     : this.idSelf + 'ToolNavi',
			allot      : this._getNaviToolAllot(),
			vars       : this.vars.navi.varsTool
		});
	},

	/**
	 *
	*/
	_getNaviToolAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			return insCurrent.allot({
				insCurrent : insCurrent.insCurrent,
				from       : 'navi-' + obj.from,
				vars       : obj.vars
			});

		};

		return allot;
	},

	/**
	 *
	*/
	insNaviUnder : null,
	_iniNaviUnder : function()
	{
		this._setNaviUnder({
			vars : this.vars.navi.varsFormat
		});
	},

	/**
	 *
	*/
	_setNaviUnder : function(obj)
	{
		this.insNaviUnder = new Code_Lib_TemplateUnder({
			eleInsert  : $(this.idSelf + 'LayoutNavi').down('.codeLibTemplateShadowBoxMiddleMiddle', 0),
			insRoot    : this.insRoot,
			insCurrent : this,
			idSelf     : this.idSelf + 'LayoutNavi',
			allot      : this._getNaviUnderAllot(),
			vars       : {
				flagBeforeBox : (this.vars.varsStatus.flagNaviToolUse)? 1 : 0,
				varsFormat    : obj.vars
			}
		});
	},

	/**
	 *
	*/
	_getNaviUnderAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == 'eventLayout') {
				insCurrent.insNaviUnder.updateStyle();
			}
		};

		return allot;
	},

	/**
	 *
	*/
	_iniList : function()
	{
		if (this.vars.varsStatus.flagListToolUse) this._iniListTool();
		this._iniListUnder();
	},

	/**
	 *
	*/
	insListTool : null,
	_iniListTool : function()
	{
		this.insListTool = new Code_Lib_BtnTool({
			eleInsert  : $(this.idSelf + 'LayoutListBox').down('.codeLibTemplateNormalBoxMiddleMiddle', 0),
			insRoot    : this.insRoot,
			insCurrent : this,
			idSelf     : this.idSelf + 'ToolList',
			allot      : this._getListToolAllot(),
			vars       : this.vars.list.varsTool
		});
	},

	/**
	 *
	*/
	_getListToolAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			return insCurrent.allot({
				insCurrent : insCurrent.insCurrent,
				from       : 'list-' + obj.from,
				vars       : obj.vars,
				varsTarget : obj.varsTarget
			});
		};

		return allot;
	},

	/**
	 *
	*/
	insListUnder : null,
	_iniListUnder : function()
	{
		this._setListUnder({
			vars : this.vars.list.varsFormat
		});
	},

	/**
	 *
	*/
	_setListUnder : function(obj)
	{
		this.insListUnder = new Code_Lib_TemplateUnder({
			eleInsert  : $(this.idSelf + 'LayoutList').down('.codeLibTemplateShadowBoxMiddleMiddle', 0),
			insRoot    : this.insRoot,
			insCurrent : this,
			idSelf     : this.idSelf + 'LayoutList',
			allot      : this._getListUnderAllot(),
			vars       : {
				flagBeforeBox  : (this.vars.varsStatus.flagListToolUse)? 1 : 0,
				varsFormat     : obj.vars
			}
		});
	},

	/**
	 *
	*/
	_getListUnderAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == 'eventLayout') {
				insCurrent.insListUnder.updateStyle();
			}
		};

		return allot;
	},

	/**
	 *
	*/
	_iniDetail : function()
	{
		if (this.vars.varsStatus.flagDetailToolUse) this._iniDetailTool();
		this._iniDetailUnder();
	},

	/**
	 *
	*/
	insDetailTool : null,
	_iniDetailTool : function()
	{
		this.insDetailTool = new Code_Lib_BtnTool({
			eleInsert  : $(this.idSelf + 'LayoutDetailBox').down('.codeLibTemplateNormalBoxMiddleMiddle', 0),
			insRoot    : this.insRoot,
			insCurrent : this,
			idSelf     : this.idSelf + 'ToolDetail',
			allot      : this._getDetailToolAllot(),
			vars       : this.vars.detail.varsTool
		});
	},

	/**
	 *
	*/
	_getDetailToolAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			return insCurrent.allot({
				insCurrent : insCurrent.insCurrent,
				from       : 'detail-' + obj.from,
				vars       : obj.vars,
				varsTarget : obj.varsTarget
			});
		};

		return allot;
	},

	/**
	 *
	*/
	insDetailUnder : null,
	_iniDetailUnder : function()
	{
		this._setDetailUnder({vars : this.vars.detail.varsFormat});
	},

	/**
	 *
	*/
	_setDetailUnder : function(obj)
	{
		this.insDetailUnder = new Code_Lib_TemplateUnder({
			eleInsert  : $(this.idSelf + 'LayoutDetail').down('.codeLibTemplateShadowBoxMiddleMiddle', 0),
			insRoot    : this.insRoot,
			insCurrent : this,
			idSelf     : this.idSelf + 'LayoutDetail',
			allot      : this._getDetailUnderAllot(),
			vars       : {
				flagBeforeBox  : (this.vars.varsStatus.flagDetailToolUse)? 1 : 0,
				varsFormat     : obj.vars
			}
		});
	},

	/**
	 *
	*/
	_getDetailUnderAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == 'eventLayout') {
				insCurrent.insDetailUnder.updateStyle();
			}
		};

		return allot;
	},

	/**
	 *
	*/
	updateTool : function(obj)
	{
		var str = obj.from.capitalize();
		if (this.vars.varsStatus['flag' + str + 'ToolUse']) {
			this._updateToolChild({
				arr      : this['ins' + str + 'Tool'].vars.varsDetail,
				flagLock : obj.flagLock,
				idTarget : obj.idTarget
			});
			this['ins' + str + 'Tool'].iniReload();
		}
	},

	/**
	 *
	*/
	_updateToolChild : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == obj.idTarget) {
				if (obj.flagLock) obj.arr[i].flagNow = 0;
				else obj.arr[i].flagNow = 1;
				return;
			}
		}
	},

	/**
	 *
	*/
	checkToolLock : function(obj)
	{
		var str = obj.from.capitalize();
		if (this.vars.varsStatus['flag' + str + 'ToolUse']) {
			return this._checkToolLockChild({
				arr      : this['ins' + str + 'Tool'].vars.varsDetail,
				idTarget : obj.idTarget
			});
		}
	},

	/**
	 *
	*/
	_checkToolLockChild : function(obj)
	{
		var flag = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == obj.idTarget) {
				if (obj.arr[i].flagNow == 0) {
					flag = 1;
				}
				break;
			}
		}
		return flag;
	},

	/**
	 *
	*/
	eventWindow : function()
	{
		this.insLayout.iniReload();
		this.eventLayout();
	},

	/**
	 *
	*/
	eventLayout : function()
	{
		if (this.vars.varsStatus.flagNaviUse) {
			this.insNaviUnder.allot({
				from       : 'eventLayout',
				insCurrent : this
			});
		}
		if (this.vars.varsStatus.flagListUse) {
			this.insListUnder.allot({
				from       : 'eventLayout',
				insCurrent : this
			});
		}
		if (this.vars.varsStatus.flagDetailUse) {
			this.insDetailUnder.allot({
				from       : 'eventLayout',
				insCurrent : this
			});
		}
		this.allot({
			insCurrent : this.insCurrent,
			from       : 'eventLayout'
		});
	},


	/**
	 *
	*/
	preEventLayout : function()
	{
		this.allot({
			insCurrent : this.insCurrent,
			from       : 'preEventLayout'
		});
	},

	/**
	 *
	*/
	eventRemove : function(obj)
	{
		this.allot({
			insCurrent : this.insCurrent,
			from       : 'eventRemove'
		});
		if (this.vars.varsStatus.flagDetailToolUse) this.insDetailTool.stopListener();
		if (this.vars.varsStatus.flagListToolUse) this.insListTool.stopListener();
		if (this.vars.varsStatus.flagNaviToolUse) this.insNaviTool.stopListener();
		this.insLayout.stopListener();
	}

});
<?php }
}
?>