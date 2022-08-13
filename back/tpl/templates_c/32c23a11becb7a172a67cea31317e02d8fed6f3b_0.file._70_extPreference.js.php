<?php /* Smarty version 3.1.24, created on 2022-08-13 00:23:37
         compiled from "/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/_70_extPreference.js" */ ?>
<?php
/*%%SmartyHeaderCode:83801950362f6ef09d93974_15486847%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '32c23a11becb7a172a67cea31317e02d8fed6f3b' => 
    array (
      0 => '/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/_70_extPreference.js',
      1 => 1329210310,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '83801950362f6ef09d93974_15486847',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_62f6ef09d9bbb6_47061179',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_62f6ef09d9bbb6_47061179')) {
function content_62f6ef09d9bbb6_47061179 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '83801950362f6ef09d93974_15486847';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_ExtPreference = Class.create(Code_Lib_ExtPortal,
{

	/**
	 *
	*/
	_sendNaviConnect : function() {

		var str = this.strClass
			+ '-' + this.idModule
			+ '-' + this.strExt
			+ '-' + this.strChild
			+ '-' + 'NaviReload';
		var objStamp = {
			id    : str,
			stamp : (this._varsStamp[str])? this._varsStamp[str] : 0
		};
		var jsonStamp = (Object.toJSON(objStamp));
		var arrayKey = [], arrayValue = [];

		arrayKey = ['class', 'module', 'ext', 'child', 'func', 'db', 'jsonStamp'];
		arrayValue = [this.strClass, this.idModule, this.strExt, this.strChild, 'NaviReload', 'slave', jsonStamp];

		this.insRoot.insRequest.set({
			flagLock        : 0,
			numZIndex       : this.insRoot.getZIndex(),
			insCurrent      : this,
			flagEscape      : 1,
			path            : this.insRoot.vars.varsSystem.path.post,
			querysKey       : arrayKey,
			querysValue     : arrayValue,
			functionSuccess : '_sendNaviConnectSuccess',
			functionFail    : '_sendNaviConnectFail',
			eleLoadStatus   : this.insNavi.insUnder.eleFormat.header.down('.codeLibBaseMarginRightFive', 0)
		});
	},


	/**
	 *
	*/
	_eventNaviConnectSuccess : function(obj)
	{
		this.insLayout.updateTool({flagLock : 0, from : 'navi', idTarget : 'Reload'});
		if (obj.json.flag == 1) this.insNavi.updateTreeVars({vars : obj.json.data});
		else if (obj.json.flag == 0) alert(this.insRoot.vars.varsSystem.str.errorSession);
		else if (obj.json.flag == 4) alert(this.insRoot.vars.varsSystem.str.maintenance);
		else if (obj.json.flag == 8) alert(this.insRoot.vars.varsSystem.str.oldData);
		else {
			if (obj.json) {
				if (obj.json.flag) {
					if (this.insRoot.vars.varsSystem.str[obj.json.flag]) {
						alert(this.insRoot.vars.varsSystem.str[obj.json.flag]);
					}
				}
			}
		}
	},

	/**
	 *
	*/
	_varsDetailEnd : null,
	_setDetailEnd : function()
	{
		this._varsDetailEnd = (Object.toJSON(this.insDetail.varsEventNavi)).evalJSON();
		var objData = {
			strTitle : null,
			strClass : null,
			vars     : {
				varsDetail : this.vars.portal.varsDetail.varsEnd.varsDetail,
				varsBtn    : this.vars.portal.varsDetail.varsEnd.varsBtn,
				varsEdit   : {},
				vars       : {}
			}
		};
		this.insDetail.eventNavi(objData);
	},

	/**
	 *
	*/
	_setDetailReset : function()
	{
		var objData = {
			strTitle : null,
			strClass : null,
			vars     : {
				varsDetail : this.vars.portal.varsDetail.varsEnd.varsDetail,
				varsBtn    : [],
				varsEdit   : {},
				vars       : {}
			}
		};
		this.insDetail.eventNavi(objData);
	},

	/**
	 *
	*/
	_backDetailEnd : function()
	{
		this._setNaviDetail({vars : this._varsDetailEnd.vars});
		this._varsDetailEnd = null;
	}


});

<?php }
}
?>