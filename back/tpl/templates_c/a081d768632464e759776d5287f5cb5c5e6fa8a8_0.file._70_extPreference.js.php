<?php /* Smarty version 3.1.24, created on 2016-08-18 12:50:21
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/_70_extPreference.js" */ ?>
<?php
/*%%SmartyHeaderCode:128509450757b5af0d50b9a9_00269022%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a081d768632464e759776d5287f5cb5c5e6fa8a8' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/_70_extPreference.js',
      1 => 1471523677,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '128509450757b5af0d50b9a9_00269022',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b5af0d529600_01649931',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b5af0d529600_01649931')) {
function content_57b5af0d529600_01649931 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '128509450757b5af0d50b9a9_00269022';
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