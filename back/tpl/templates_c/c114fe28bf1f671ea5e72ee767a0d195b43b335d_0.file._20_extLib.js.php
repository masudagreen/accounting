<?php /* Smarty version 3.1.24, created on 2022-08-13 00:23:37
         compiled from "/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/_20_extLib.js" */ ?>
<?php
/*%%SmartyHeaderCode:49473977362f6ef09cab7f0_62354719%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c114fe28bf1f671ea5e72ee767a0d195b43b335d' => 
    array (
      0 => '/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/_20_extLib.js',
      1 => 1329210256,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '49473977362f6ef09cab7f0_62354719',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_62f6ef09cc2250_01807538',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_62f6ef09cc2250_01807538')) {
function content_62f6ef09cc2250_01807538 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '49473977362f6ef09cab7f0_62354719';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_ExtLib = Class.create({

	/**
	 *
	*/
	_extVars : function(obj)
	{
		this.insRoot = (obj.insRoot)? obj.insRoot : null;
		this.insCurrent = (obj.insCurrent)? obj.insCurrent : null;
		this.insSelf = this;
		this.idSelf = (obj.idSelf)? obj.idSelf : null;
		this.varsTarget = (obj.varsTarget)? obj.varsTarget : null;
		this.vars = (obj.vars)? (Object.toJSON(obj.vars)).evalJSON() : null;
		this.eleInsert = (obj.eleInsert)? obj.eleInsert : null;
		this.eleInsertBtnLeft = (obj.eleInsertBtnLeft)? obj.eleInsertBtnLeft : null;
		this.eleInsertBtnRight = (obj.eleInsertBtnRight)? obj.eleInsertBtnRight : null;
		this.idWindow = (obj.idWindow)? obj.idWindow : null;
		this._varsSatmp = {};
	},

	/**
	 *
	*/
	_getCake : function()
	{
		if (!this.insRoot.insCake) return;
		this.insRoot.insCake.getStorageCake({
			parentKey  : this.idSelf,
			funcReturn : this._getCakeVars,
			insReturn  : this
		});
	},

	/**
	 *
	*/
	_getCakeVars : function(obj)
	{
		var insCurrent = obj.insReturn;
		if(obj.data) {
			insCurrent._getCakeVarsUpdate({data : obj.data});
		}
	},


	/**
	 *
	*/
	_varsScroll : {numTop : 0, numLeft : 0},
	getScroll : function()
	{
		this.resetScroll();
		if (this.insFormat) {
			if (this.insFormat.eleTemplate.body) {
				this._varsScroll = {
					numTop  : this.insFormat.eleTemplate.body.scrollTop,
					numLeft : this.insFormat.eleTemplate.body.scrollLeft
				};
			}
		}
	},

	/**
	 *
	*/
	resetScroll : function()
	{
		this._varsScroll = {
			numTop  : 0,
			numLeft : 0
		};
	},

	/**
	 *
	*/
	setScroll : function()
	{
		if (this.insFormat) {
			if (this.insFormat.eleTemplate.body) {
				this.insFormat.eleTemplate.body.scrollTop = this._varsScroll.numTop;
				this.insFormat.eleTemplate.body.scrollLeft = this._varsScroll.numLeft;
			}
		}
	},

	/**
	 *
	*/
	_getCakeVarsUpdate : function(obj)
	{

	},

	/**
	 *
	*/
	_varsCake : {},
	setCake : function()
	{
		if (!this.insRoot.insCake) return;
		this._varsCake = {};
		this._setCakeVars();
		this.insRoot.insCake.setStorageCake({
			parentKey  : this.idSelf,
			value      : this._varsCake,
			numExpires : 0
		});
	},


	/**
	 *
	*/
	_setCakeVars : function()
	{

	},


	/**
	 * Listener
	*/
	insListener : null,
	_extListener : function()
	{
		this.insListener = new Code_Lib_Listener();
		this._varsListener = [];
	},

	/**
	 *
	*/
	_varsListener : null,
	_setListener : function(obj)
	{
		var data = {ins : obj.ins};
		this._varsListener.push(data);
	},

	/**
	 *
	*/
	stopListener : function()
	{
		this.insListener.stop();
		this._stopListenerChild({arr : this._varsListener});
		if (this.insPage) this.insPage.stopListener();
		this._resetListener();
	},

	/**
	 *
	*/
	_stopListenerChild : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			obj.arr[i].ins.insListener.stop();
		}
	},

	/**
	 *
	*/
	_resetListener : function()
	{
		this._varsListener = [];
	},

	/**
	 *
	*/
	eleWrap : null,
	_extWrap : function()
	{
		var ele = $(document.createElement('div'));
		ele.id = this.idSelf;
		this.eleInsert.insert(ele);
		this.eleWrap = ele;
	},

	/**
	 *
	*/
	_getWrapWidth : function(obj)
	{
		var array = this.eleInsert.style.width.split('px');
		var data = parseFloat(array[0]);

		return  data;
	},

	/**
	 *
	*/
	_getWrapHeight : function()
	{
		var array = this.eleInsert.style.height.split('px');
		var data = parseFloat(array[0]);

		return  data;
	},

	/**
	 *
	*/
	removeWrap : function()
	{
		if ($(this.idSelf)) $(this.idSelf).remove();
		if (this.eleInsertBtnLeft) this.eleInsertBtnLeft.innerHTML = '';
		if (this.eleInsertBtnRight) this.eleInsertBtnRight.innerHTML = '';
	},

	/**
	 * Block
	*/
	_varsBlock : {},
	_getBlock : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if(obj.arr[i].id == obj.idTarget) {
				this._varsBlock = obj.arr[i];
			}
		}
	},


	/**
	 *
	*/
	_removeBlock : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if(obj.arr[i].id == obj.idTarget) num = i;
		}
		obj.arr = obj.arr.slice(0, num).concat(obj.arr.slice((num + 1), obj.arr.length));

		return obj.arr;
	},

	/**
	 *
	*/
	_setBlock : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if(obj.arr[i].id == obj.idTarget) num = i;
		}
		var array = [obj.block];
		obj.arr = obj.arr.slice(0, num + 1).concat(array.concat(obj.arr.slice((num + 1), obj.arr.length)));

		return obj.arr;
	},

	/**
	 *
	*/
	_setBlockMove : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == obj.idTarget) num = i;
		}
		var array = [obj.block];
		obj.arr = obj.arr.slice(0, num + 1).concat(array.concat(obj.arr.slice((num + 1), obj.arr.length)));

		return obj.arr;
	},

	/**
	 *
	*/
	insFormat : null,
	_extFormat : function()
	{
		this._setFormat();
	},

	/**
	 *
	*/
	_setFormat : function(obj)
	{
		this.insFormat = new Code_Lib_TemplateFormat({
			eleInsert  : this.eleWrap,
			insRoot    : this.insRoot,
			insCurrent : this.insSelf,
			idSelf     : this.idSelf + 'Format',
			vars       : this.vars.varsFormat
		});
	},

	/**
	 *
	*/
	_updateFormatStyle : function()
	{
		this.insFormat.updateTemplateStyle();
	},

	/**
	 * BtnBottom
	*/
	_staticBtnBottom : {numMargin : 5},
	_extBtnBottom : function()
	{
		if (!this.vars.varsStatus.flagBtnBottomUse) return;
		if (!this.eleInsertBtnLeft && this.eleInsertBtnRight) return;
		if (this.eleInsertBtnLeft) {
			this.eleInsertBtnLeft.innerHTML = '';
			this.eleInsertBtnLeft.style.marginTop = this._staticBtnBottom.numMargin + 'px';
		}
		if (this.eleInsertBtnRight) {
			this.eleInsertBtnRight.innerHTML = '';
			this.eleInsertBtnRight.style.marginTop = this._staticBtnBottom.numMargin + 'px';
		}
		this._setBtnBottom({arr : this.vars.varsBtn});
	},

	/**
	 *
	*/
	_setBtnBottom : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagUse) continue;
			if (obj.arr[i].flagBtnUse) this._setBtnBottomBtn({vars : obj.arr[i]});
			else if (obj.arr[i].flagTextUse) this._setBtnBottomText({vars : obj.arr[i]});
		}
	},

	/**
	 *
	*/
	_setBtnBottomBtn : function(obj)
	{
		var insBtn = new Code_Lib_Btn();
		insBtn.iniBtn({
			eleInsert  : (obj.vars.flagLeftUse)? this.eleInsertBtnLeft : this.eleInsertBtnRight,
			id         : this.idSelf + 'BtnBottom' + obj.vars.id,
			strFunc    : '_mousedownBtnBottom',
			strTitle   : obj.vars.strTitle,
			insCurrent : this,
			flagATag   : (obj.vars.flagATag)? obj.vars.flagATag : null,
			path       : (obj.vars.path)? obj.vars.path : null,
			vars       : obj.vars
		});
		this._setListener({ins : insBtn});
		$(this.idSelf + 'BtnBottom' + obj.vars.id).style.marginRight = this._staticBtnBottom.numMargin + 'px';
	},

	/**
	 *
	*/
	_setBtnBottomText : function(obj)
	{
		var insBtn = new Code_Lib_Btn();
		insBtn.iniBtnText({
			eleInsert : (obj.vars.flagLeftUse)? this.eleInsertBtnLeft : this.eleInsertBtnRight,
			id         : this.idSelf + 'BtnBottom' + obj.vars.id,
			strFunc    : '_mousedownBtnBottom',
			strTitle   : obj.vars.strTitle,
			insCurrent : this,
			vars       : obj.vars
		});
		this._setListener({ins : insBtn});
		$(this.idSelf + 'BtnBottom' + obj.vars.id).style.marginRight = this._staticBtnBottom.numMargin + 'px';
	},

	/**
	 *
	*/
	removeBtnBottom : function()
	{
		if (this.eleInsertBtnLeft) this.eleInsertBtnLeft.innerHTML = '';
		if (this.eleInsertBtnRight) this.eleInsertBtnRight.innerHTML = '';
	},

	/**
	 *
	*/
	showBtnBottom : function()
	{
		this._showBtnBottomChild({arr : this.vars.varsBtn});
	},

	/**
	 *
	*/
	_showBtnBottomChild : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if ($(this.idSelf + 'BtnBottom' + obj.arr[i].id)) $(this.idSelf + 'BtnBottom' + obj.arr[i].id).show();
		}
	},

	/**
	 *
	*/
	hideBtnBottom : function()
	{
		this._hideBtnBottom({arr : this.vars.varsBtn});
	},

	/**
	 *
	*/
	_hideBtnBottom : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].flagATag) continue;
			if ($(this.idSelf + 'BtnBottom' + obj.arr[i].id)) $(this.idSelf + 'BtnBottom' + obj.arr[i].id).hide();
		}
	},

	/**
	 *
	*/
	_mousedownBtnBottom : function(obj)
	{
		this.hideBtnBottom({arr : this.vars.varsBtn});
		this.allot({
			insCurrent : this.insCurrent,
			from       : 'eventBtnBottom',
			vars       : obj.vars
		});
	},

	/**
	 * Page
	*/
	insPage : null,
	_extPage : function()
	{
		this._setPage();
	},

	/**
	 *
	*/
	_getPageWidth : function()
	{
		var array = this.eleInsert.style.width.split('px');
		var data = parseFloat(array[0]);

		return data;
	},

	/**
	 *
	*/
	_setPage : function()
	{
		if (!this.vars.varsStatus.flagPageUse) return;
		if (this.vars.varsStatus.flagInnerPageUse) {
			this.insFormat.eleTemplate.fooder.setStyle({
				width : this._getPageWidth() + 'px'
			});
		}
		this.insPage = new Code_Lib_BtnPage({
			eleInsertBtnLeft  : (this.vars.varsStatus.flagInnerPageUse)?
								  this.insFormat.eleTemplate.fooder.down('.codeLibBaseMarginLeftFive',0)
								: this.eleInsertBtnLeft ,
			eleInsertBtnRight : (this.vars.varsStatus.flagInnerPageUse)?
								  this.insFormat.eleTemplate.fooder.down('.codeLibBaseMarginRightFive',0)
								: this.eleInsertBtnRight,
			insRoot           : this.insRoot,
			insCurrent        : this.insSelf,
			idSelf            : this.idSelf + 'Page',
			allot             : this._getPageAllot(),
			vars              : this.vars.varsPage
		});
	},

	/**
	 *
	*/
	_getPageAllot : function()
	{
		var allot =  function(obj)
		{
			var insCurrent = obj.insCurrent;
			if (obj.from == 'eventPage') {
				var flag = insCurrent.allot({
					insCurrent : insCurrent.insCurrent,
					from       : 'eventPage',
					vars       : obj.vars
				});
				if (flag) insCurrent.iniReload();
			}
		};

		return allot;
	},

	/**
	 *
	*/
	allot : {},
	_extAllot : function(obj)
	{
		this.allot = obj.allot;
	}

});

<?php }
}
?>