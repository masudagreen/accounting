<?php /* Smarty version 3.1.24, created on 2019-10-06 06:26:37
         compiled from "/app/rucaro/back/tpl/templates/else/core/base/js/lib/popup.js" */ ?>
<?php
/*%%SmartyHeaderCode:11893942145d99891d103db1_26619653%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '915c64cb8ebc5c5db981065b2ada7ecfaadd670c' => 
    array (
      0 => '/app/rucaro/back/tpl/templates/else/core/base/js/lib/popup.js',
      1 => 1570328741,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '11893942145d99891d103db1_26619653',
  'variables' => 
  array (
    'varsLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d99891d1f2d87_86024617',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d99891d1f2d87_86024617')) {
function content_5d99891d1f2d87_86024617 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '11893942145d99891d103db1_26619653';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Popup = Class.create(Code_Lib_ExtLib,
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
		this._iniWindow();
		this._iniForm();
		this._iniAppear();
	},

	/**
	 *
	*/

	_iniVars : function(obj)
	{
		this._extVars(obj);
		this.varsLoad.varsStatus.flagNow = obj.vars.flag;
		if(this.varsLoad.varsStatus.flagNow == 'news') {
			var varsTmpl = (Object.toJSON(this.varsLoad.news.varsForm.templateComment)).evalJSON();
			varsTmpl = varsTmpl.replace(/<?php echo '<%'; ?>
replace<?php echo '%>'; ?>
/, obj.vars.numNews);
			this.varsLoad.varsStatus.flagAppearUse = 1;
			this.varsLoad.news.varsForm.varsDetail[0].strComment = varsTmpl;

		} else if (this.varsLoad.varsStatus.flagNow == 'logout') {
			this.varsLoad.varsStatus.flagAppearUse = 0;

		} else if (this.varsLoad.varsStatus.flagNow == 'autoLogout') {
			var varsTmpl = (Object.toJSON(this.varsLoad.autoLogout.varsForm.templateComment)).evalJSON();
			varsTmpl = varsTmpl.replace(/<?php echo '<%'; ?>
replace<?php echo '%>'; ?>
/, this.insRoot.vars.varsSystem.status.numAutoLogout);
			this.varsLoad.varsStatus.flagAppearUse = 0;
			this.varsLoad.autoLogout.varsForm.varsDetail[0].strComment = varsTmpl;

		} else if (this.varsLoad.varsStatus.flagNow == 'reload') {
			this.varsLoad.varsStatus.flagAppearUse = 0;
		}
	},

	/**
	 * Wrap
	*/
	eleWrap : null,
	_iniWrap : function()
	{
		this._setWrap();
	},

	/**
	 *
	*/
	_setWrap : function()
	{
		var ele = $(document.createElement('div'));
		var str = 'codeLibPopupWrap' + this.varsLoad.varsStatus.flagNow.capitalize();
		ele.addClassName(str);
		ele.id = this.idSelf;
		this.eleInsert.insert(ele);
		this.eleWrap = ele;
		this.eleWrap.setStyle({
			'zIndex' : this.insRoot.vars.varsSystem.num.zIndex + 1
		});
		if(this.varsLoad.varsStatus.flagAppearUse) this.eleWrap.hide();
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
	insForm : null,
	_setForm : function()
	{
		this.insForm = new Code_Lib_Form({
			eleInsertBtnLeft  : null,
			eleInsertBtnRight : null,
			eleInsert         : $(this.insWindow.idSelf).down('.codeLibWindowBodyTopMiddleWrap', 0),
			insRoot           : this.insRoot,
			insCurrent        : this.insSelf,
			idSelf            : this.idSelf + 'Form',
			allot             : function(){},
			vars              : this.varsLoad[this.varsLoad.varsStatus.flagNow].varsForm
		});
	},

	/**
	 *
	*/
	removeWrap : function()
	{
		if(this._varsStay) this._resetStay();
		this.eleWrap.remove();
		this.allot({
			insCurrent : this.insCurrent,
			from       : 'removeWrap'
		});
	},

	/**
	 * Window
	*/
	insWindow : null,
	_iniWindow : function()
	{
		this._setWindow();
	},

	/**
	 *
	*/
	_setWindow : function()
	{

		this.insWindow = new Code_Lib_Window();

		this.insWindow.iniLoad({
			eleInsert  : this.eleWrap,
			insRoot    : this.insRoot,
			insCurrent : this,
			idSelf     : this.idSelf + this.varsLoad[this.varsLoad.varsStatus.flagNow].varsWindow.id,
			allot      : this._getWindowAllot(),
			vars       : this.varsLoad[this.varsLoad.varsStatus.flagNow].varsWindow
		});

		$(this.insWindow.idWindow).down('.codeLibWindowBodyTopMiddleWrap', 0).setStyle({overFlow : 'auto'});

	},

	/**
	 *
	*/
	_getWindowAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			if(obj.from == '_mousedownRemove') insCurrent.insCurrent._fadeWindow(obj);
		};

		return allot;
	},

	/**
	 *
	*/
	_fadeWindow : function(obj)
	{
		new Effect.Fade($(obj.insCurrent.idWindow), {
			afterFinish : function()
			{
				obj.insCurrent.removeWrap();
				obj.insCurrent.insCurrent.removeWrap();
			}
		});
	},

	/**
	 * Appear
	*/
	_iniAppear : function()
	{
		if(!this.varsLoad.varsStatus.flagAppearUse) return;
		this._setAppear();
	},

	/**
	 *
	*/
	_setAppear : function()
	{
		var insCurrent = this.insSelf;
		new Effect.Appear(this.eleWrap, {
			afterFinish:function()
			{
				insCurrent._iniStay();
			}
		});
	},

	/**
	 * Stay
	*/
	_iniStay : function()
	{
		this._setStay();
	},

	/**
	 *
	*/
	_varsStay : null,
	_staticStay : { numLimit : 5000 },
	_setStay : function()
	{
		this._varsStay = {
			interval : setInterval(this._runStay.bind(this), 1000),
			stamp    : (new Date()).getTime()
		};
	},

	/**
	 *
	*/
	_runStay : function()
	{
		var run = (new Date()).getTime() - this._varsStay.stamp;
		if(run >= this._staticStay.limit) {
			this._resetStay();
			this._fadeWindow({insCurrent : this.insWindow});
		}
	},

	/**
	 *
	*/
	_resetStay : function()
	{
		clearInterval(this._varsStay.interval);
		this._varsStay = null;
	}
});
<?php }
}
?>