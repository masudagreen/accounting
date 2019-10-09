<?php /* Smarty version 3.1.24, created on 2017-12-28 06:36:27
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/js/jpn/logSearch.js" */ ?>
<?php
/*%%SmartyHeaderCode:7087345915a4490ebce99f3_13055048%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2d34b14cbe72a0004d411250d96d91af9251aba9' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/plugin/accounting/js/jpn/logSearch.js',
      1 => 1483698247,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '7087345915a4490ebce99f3_13055048',
  'variables' => 
  array (
    'varsLoad' => 0,
    'numNews' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5a4490ebe41323_74306295',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5a4490ebe41323_74306295')) {
function content_5a4490ebe41323_74306295 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '7087345915a4490ebce99f3_13055048';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Plugin_Accounting_LogSearch = Class.create(Code_Lib_ExtPortal,
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
		this._iniNavi();
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
	_iniNavi : function()
	{
		this._extNavi();
	},

	/**
	 *
	*/
	_setNavi : function()
	{
		this.insNavi = new Code_Plugin_Accounting_Lib_ControlNavi({
			insUnder   : this.insLayout.insNaviUnder,
			insTool    : this.insLayout.insNaviTool,
			insRoot    : this.insRoot,
			insCurrent : this,
			idSelf     : this.idSelf + 'Navi',
			allot      : this._getNaviAllot(),
			vars       : this.vars.portal.varsNavi
		});
	},

	/**
	 *
	*/
	_eventNaviConnect : function(obj)
	{
		if (obj.flag == 'search') {
			this._eventSearch({
				numLotNow : 0,
				ph : {
					flagApply : obj.vars.flagApply,
					arrWhere  : obj.vars.arrWhere,
					arrOrder  : obj.vars.arrOrder
				}
			});
			this.insCurrent.eventChildSearchConnect({
				varsSearch  : this._varsSearch,
				strBackFunc : 'eventParentConnectSuccess',
				insBack     : this,
				flag        : obj.flag.capitalize()
			});
			return;

		} else if (obj.flag.match(/^folder(.*?)-search$/)) {
			this._eventSearch({
				numLotNow : 0,
				ph : {
					flagApply : obj.vars.flagApply,
					arrWhere  : obj.vars.arrWhere,
					arrOrder  : obj.vars.arrOrder
				}
			});

		}
		this._varsNaviConnect = obj;
		this._sendNaviConnect();
	},

	eventParentConnectSuccess : function()
	{
		this.insNavi.showBtn();
	},

	_eventNaviConnectSuccess : function(obj)
	{
		var insEscape = new Code_Lib_Escape();
		if (obj.json.flag == 1){
			if (this._varsNaviConnect.flag == 'search') {
				this.insList.resetVars({vars : obj.json.data, numLotNow : this._varsSearch.numLotNow});
				this.insList.eventNavi({strTitle : null, strClass : null});

			} else if (this._varsNaviConnect.flag.match(/^folder(.*?)-search$/)) {
				this.insList.resetVars({vars : obj.json.data, numLotNow : this._varsSearch.numLotNow});
				this.insList.eventNavi({strTitle : null, strClass : null});
				if (obj.json.data.numRows) {
					this._eventDetailList({vars : obj.json.data.varsDetail[0]});
				}

			} else if (this._varsNaviConnect.flag.match(/^folder(.*?)-save$/)
				|| this._varsNaviConnect.flag.match(/^format(.*?)-save$/)
			) {
				this.insNavi.updateFolderVars({vars : obj.json.data.varsDetail});

			} else if (this._varsNaviConnect.flag.match(/^folder(.*?)-reload$/)
				|| this._varsNaviConnect.flag.match(/^format(.*?)-reload$/)
			) {
				this.insLayout.updateTool({flagLock : 0, from : 'navi', idTarget : 'Reload'});
				this.insNavi.updateFolderVars({vars : obj.json.data.varsDetail});
				if (obj.json.stamp) {
					var data = (Object.toJSON(obj.json.data.varsDetail)).evalJSON();
					if (obj.json.stamp.id) this._varsStampCheck[obj.json.stamp.id] = data;
				}

			} else if (this._varsNaviConnect.flag == 'search-save' || this._varsNaviConnect.flag == 'search-delete') {
				this.insNavi.updateSearchVarsSave({vars : obj.json.data.varsDetail});

			} else if (this._varsNaviConnect.flag == 'search-reload') {
				this.insLayout.updateTool({flagLock : 0, from : 'navi', idTarget : 'Reload'});
				this.insNavi.updateSearchVars({vars : obj.json.data.varsDetail});
				if (obj.json.stamp) {
					var data = (Object.toJSON(obj.json.data.varsDetail)).evalJSON();
					if (obj.json.stamp.id) this._varsStampCheck[obj.json.stamp.id] = data;
				}

			}

		} else if (obj.json.flag == 10) {
			if (this._varsNaviConnect.flag.match(/^folder(.*?)-reload$/)
				|| this._varsNaviConnect.flag.match(/^format(.*?)-reload$/)
				|| this._varsNaviConnect.flag == 'tree-reload'
			) {
				this.insLayout.updateTool({flagLock : 0, from : 'navi', idTarget : 'Reload'});
				if (obj.json.stamp) {
					this.insNavi.updateFolderVars({vars : this._varsStampCheck[obj.json.stamp.id]});
				}

			} else if (this._varsNaviConnect.flag == 'search-reload') {
				this.insLayout.updateTool({flagLock : 0, from : 'navi', idTarget : 'Reload'});
				if (obj.json.stamp) {
					this.insNavi.updateSearchVars({vars : this._varsStampCheck[obj.json.stamp.id]});

				}
			}

		} else {
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
	_eventLayoutDetailContent : function()
	{

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