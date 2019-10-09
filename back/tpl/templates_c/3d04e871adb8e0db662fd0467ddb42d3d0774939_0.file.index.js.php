<?php /* Smarty version 3.1.24, created on 2019-06-16 09:02:08
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/confirm/js/index.js" */ ?>
<?php
/*%%SmartyHeaderCode:12063186125d0605904bc643_77270317%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3d04e871adb8e0db662fd0467ddb42d3d0774939' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/confirm/js/index.js',
      1 => 1560675140,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12063186125d0605904bc643_77270317',
  'variables' => 
  array (
    'varsLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d0605904c06e1_53748751',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d0605904c06e1_53748751')) {
function content_5d0605904c06e1_53748751 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '12063186125d0605904bc643_77270317';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Core_Confirm = Class.create(Code_Lib_Ext,{

	vars : <?php echo $_smarty_tpl->tpl_vars['varsLoad']->value;?>
,


	initialize : function()
	{
		this._iniVars();
		if (this._iniBrowser()) return;
		this._iniListener();
		this._iniRequest();
		this._iniKey();
		this._iniCake();
		this._iniLoad();
	},

	/**
	 *
	*/
	_iniLoad : function()
	{
		this._iniWindow();
	},

	/**
	 *
	*/
	_iniRequest : function ()
	{
		this._extRequest();
	},

	/**
	 *
	*/
	_iniCake : function()
	{
		this._extCake({
			id       : this.vars.varsSystem.id.confirm,
			pathSelf : this.vars.varsSystem.path.post
		});
	},

	/**
	 *
	*/
	_iniBrowser : function () {
		return this._extBrowser();
	},

	/**
	 *
	*/
	_iniVars : function ()
	{
		this._extVars();
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
	_iniKey : function()
	{
		this._extKey();
	},

	/**
	 *
	*/
	_iniWindow : function ()
	{
		this._extWindow({
			eleInsert : $(this.vars.varsSystem.id.root),
			vars      : this.vars.varsWindow
		});
	},

	/**
	 *
	*/
	_getWindowAllot : function ()
	{
		var allot = function (obj) {
			var insCurrent = obj.insCurrent;
			if (obj.from == '_mousedownBoot') {
				insCurrent.insCurrent._sendWindow();
			}
		};

		return allot;
	},

	/**
	 *
	*/
	_varsWindow : null,
	_sendWindow : function()
	{
		var strUrl = document.URL;
		strUrl.match(/.*?\?type=(.*?)&id=(.*?)$/);
		var flagType = RegExp.$1;
		var id = RegExp.$2;
		var arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'id', 'type'];
		var arrayValue = ['Core', 'Confirm', 'Portal', '', 'Vars','master', id, flagType];

		this.insRequest.set({
			flagLock        : 0,
			numZIndex       : this.vars.varsSystem.num.zIndex,
			insCurrent      : this,
			flagEscape      : 1,
			path            : this.vars.varsSystem.path.post,
			querysKey       : arrayKey,
			querysValue     : arrayValue,
			functionSuccess : '_sendWindowSuccess',
			functionFail    : '_sendWindowFail'
		});
		var ele = $(document.createElement('span'));
		$(this.insWindow.idWindow).down('.codeLibWindowBodyTopMiddleWrap',0).insert(ele);
		ele.addClassName('codeLibRequestImgLoading');
		ele.addClassName('codeLibRequestImgLoadingPos');
		this._varsWindow = {ele : ele};
	},

	/**
	 *
	*/
	_sendWindowSuccess : function(obj)
	{
		if (obj.response.responseText.isJSON()) {
			var json = obj.response.responseText.evalJSON();
			if (json.flag == 1) {
				this.vars.portal = json.data.portal;
				this._iniLayout();
				this._iniDetail();
			}
		}
		else alert(this.vars.varsSystem.str.errorServer);
		this._varsWindow.ele.remove();
		this._varsWindow = {};
	},

	/**
	 *
	*/
	_sendWindowFail : function(obj)
	{
		this._varsWindow.ele.remove();
		alert(this.vars.varsSystem.str.fail);
	},

	/**
	 *
	*/
	_iniLayout : function()
	{
		this._extLayout({vars  : this.vars.portal.varsTemplateLayout});
	},

	/**
	 *
	*/
	_iniDetail : function()
	{
		this._setDetail();
	},

	/**
	 *
	*/
	insDetail : null,
	_setDetail : function()
	{
		this.insDetail = new Code_Lib_ControlDetail({
			insUnder   : this.insLayout.insDetailUnder,
			insTool    : null,
			insRoot    : this.insRoot,
			insCurrent : this.insSelf,
			idSelf     : 'Detail',
			allot      : this._getDetailAllot(),
			vars       : this.vars.portal.varsDetail
		});
		this._setDetailStart();
	},

	/**
	 *
	*/
	_setDetailStart : function()
	{
		this.insDetail.eventList({
			strTitle : this.vars.portal.varsDetail.varsDetail.strTitle,
			strClass : null,
			vars : {
				varsDetail : this.vars.portal.varsDetail.varsDetail.varsDetail,
				varsBtn    : this.vars.portal.varsDetail.varsDetail.varsBtn,
				varsEdit   : {}
			}
		});
		if (this.vars.portal.varsDetail.varsDetail.idCookie) {
			this._setCookie({data: this.vars.portal.varsDetail.varsDetail.idCookie});
		}
	},

	/**
	 *
	*/
	_setCookie : function(obj)
	{
		var insCookie = new Code_Lib_Cookie();
		insCookie.setData({
			strKey     : 'id',
			value      : obj.data,
			numExpires : this.vars.varsSystem.num.expiresSession,
			path       : '',
			strDomain  : ''
		});
		this._iniTime();
	},

	/**
	 * Time
	*/
	_iniTime : function()
	{
		this._setTime();
	},

	/**
	 *
	*/
	_varsTime : null,
	_setTime : function()
	{
		this._varsTime = setInterval(this._runTime.bind(this), 3 * 1000);
	},

	/**
	 *
	*/
	_runTime : function()
	{
		this._varsTime = null;
		location.href = this.vars.varsSystem.path.login;
	},

	/**
	 *
	*/
	_getDetailAllot : function()
	{
		var allot = function(obj)
		{
			var insCurrent = obj.insCurrent;
			var array = obj.from.split('-');
			if (obj.from == 'eventLayout');
			else if (obj.from == 'eventRemove');

		};

		return allot;
	}
});



<?php }
}
?>