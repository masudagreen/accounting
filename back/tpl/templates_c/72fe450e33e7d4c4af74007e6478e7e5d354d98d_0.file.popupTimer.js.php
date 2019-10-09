<?php /* Smarty version 3.1.24, created on 2019-10-06 06:26:37
         compiled from "/app/rucaro/back/tpl/templates/else/core/base/js/lib/popupTimer.js" */ ?>
<?php
/*%%SmartyHeaderCode:8012456555d99891d2a6f66_22665346%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '72fe450e33e7d4c4af74007e6478e7e5d354d98d' => 
    array (
      0 => '/app/rucaro/back/tpl/templates/else/core/base/js/lib/popupTimer.js',
      1 => 1570328741,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8012456555d99891d2a6f66_22665346',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d99891d2fdce4_89615318',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d99891d2fdce4_89615318')) {
function content_5d99891d2fdce4_89615318 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '8012456555d99891d2a6f66_22665346';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_PopupTimer = Class.create(Code_Lib_ExtLib, {

	/**
	 *
	*/
	initialize : function(obj)
	{
		this._extAllot(obj);
		this._iniVars(obj);
		this._iniTimer();
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
	_iniTimer : function()
	{
		this._setTimer();
	},

	/**
	 *
	*/
	_varsTimer : null,
	_setTimer : function()
	{
		var set = this.insRoot.vars.varsSystem.status.numAutoPopup * 1000;
		this._varsTimer = {
			interval : setInterval(this._runTimer.bind(this), set),
			stamp    : (new Date()).getTime(),
			flagUse  : 1,
			loop     : 1
		};
	},

	/**
	 *
	*/
	_runTimer : function()
	{
		if(this.insRoot.vars.varsSystem.status.numAutoPopup == 0 || !this._varsTimer.flagUse) return;
		var cut = this.insRoot.vars.varsSystem.status;
		var num = cut.numAutoPopup * 60 * 1000 * this._varsTimer.loop;
		var run = (new Date()).getTime() - this._varsTimer.stamp;
		if(run > num) {
			this._varsTimer.loop++;
			this._sendTimer();
		}
	},

	/**
	 *
	*/
	_sendTimer : function()
	{
		var arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db'];
		var arrayValue = ['Core', 'Base', 'Popup', '', 'Send', 'slave'];
		this.insRoot.insRequest.set({
			flagLock        : 1,
			idInsert        : this.insRoot.vars.varsSystem.id.root,
			numZiIndex      : this.insRoot.vars.varsSystem.num.zIndex,
			insCurrent      : this.insSelf,
			flagEscape      : 1,
			path            : this.insRoot.vars.varsSystem.path.post,
			querysKey       : arrayKey,
			querysValue     : arrayValue,
			functionSuccess : '_sendTimerSuccess',
			functionFail    : '_sendTimerFail'
		});
	},

	/**
	 *
	*/
	_sendTimerSuccess : function(obj)
	{
		if (obj.response.responseText.isJSON()) {
			var json = obj.response.responseText.evalJSON();
			var numNews = parseFloat(json.numNews);
			if (json.flag == 1 && numNews) {
				this.insRoot.iniPopup({flag : 'news', numNews : numNews});
			}

		} else {
			this._varsTimer.flagUse = 0;
			alert(this.insRoot.vars.varsSystem.str.errorRequest);
		}
	},

	/**
	 *
	*/
	_sendTimerFail : function(obj)
	{
		this._varsTimer.flagUse = 0;
		alert(this.insRoot.vars.varsSystem.str.errorRequest);
	}
});
<?php }
}
?>