<?php /* Smarty version 3.1.24, created on 2019-10-06 06:26:33
         compiled from "/app/rucaro/back/tpl/templates/else/core/base/js/lib/_70_extPreference.js" */ ?>
<?php
/*%%SmartyHeaderCode:10078113465d998919722e55_06142019%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '72ad332d57e6275be5848e79b15db0dacd830bb8' => 
    array (
      0 => '/app/rucaro/back/tpl/templates/else/core/base/js/lib/_70_extPreference.js',
      1 => 1570328741,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '10078113465d998919722e55_06142019',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d998919744532_52849365',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d998919744532_52849365')) {
function content_5d998919744532_52849365 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '10078113465d998919722e55_06142019';
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