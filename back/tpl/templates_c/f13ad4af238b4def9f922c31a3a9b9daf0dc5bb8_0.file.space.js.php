<?php /* Smarty version 3.1.24, created on 2016-08-20 07:30:24
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/space.js" */ ?>
<?php
/*%%SmartyHeaderCode:137729192157b807103216f7_10647445%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f13ad4af238b4def9f922c31a3a9b9daf0dc5bb8' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/space.js',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '137729192157b807103216f7_10647445',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b8071033c728_79472008',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b8071033c728_79472008')) {
function content_57b8071033c728_79472008 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '137729192157b807103216f7_10647445';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Space = Class.create(Code_Lib_ExtLib,
{

	/**
	 *
	*/
	initialize : function(obj)
	{
		this._extAllot(obj);
		this._iniVars(obj);
		this._iniWrap();
	},

	/**
	 *
	*/
	iniReload : function(obj)
	{
		this._updateWrapStyle();
		this._updateWrapContent(obj);
	},

	/**
	 *
	*/
	getGraphData : function()
	{
		if (!this.insChart) {
			return [];
		}
		return this.insChart.getGraphData();
	},

	/**
	 *
	*/
	_iniVars : function(obj)
	{
		this._extVars(obj);
		this.eleScroll = obj.eleScroll;
	},

	/**
	 *
	*/
	_iniWrap : function()
	{
		this._extWrap();
		this.eleWrap.addClassName('codeLibSpaceWrap');

		if (this.vars.varsStatus.numPadding) {
			this.eleWrap.style.padding = this.vars.varsStatus.numPadding + 'px';
		}
		if (this.vars.varsStatus.numMargin) {
			this.eleWrap.style.margin = this.vars.varsStatus.numMargin + 'px';
		}
		if (this.vars.varsStatus.strBorderColor) {
			this.eleWrap.setStyle({
				borderColor : this.vars.varsStatus.strBorderColor,
				borderStyle : 'solid',
				borderWidth : '2px',
			});
		}
		if (this.vars.varsStatus.flagOverflowXUse) {
			this.eleWrap.setStyle({
				overflowX : 'auto'
			});
		}
		if (this.vars.varsStatus.flagOverflowYUse) {
			this.eleWrap.setStyle({
				overflowY : 'auto'
			});
		}
		var numWidth = this._getWrapWidth();
		if (this.vars.varsStatus.unitWidth == '%') {
			numWidth *= this.vars.varsStatus.numWidth/100;
		}

		this.eleWrap.style.width = numWidth + 'px';
		if (this.vars.varsStatus.numHeight) {
			this.eleWrap.style.height = this.vars.varsStatus.numHeight + 'px';
		}


		if (this.vars.varsStatus.flagChartUse) {
			this.insChart = new Proto.Chart(this.eleWrap, this.vars.varsDetail.varsData, this.vars.varsDetail.varsOptions, this.eleScroll, this.insRoot);
		} else {
			this.eleWrap.insert(this.vars.varsDetail.strHtml);
		}

	},

	/**
	 *
	*/
	updateChartStyle : function(obj)
	{
		varsOptions = obj.varsOptions;
		if (!varsOptions) {
			varsOptions = this.vars.varsDetail.varsOptions;
		} else {
			this.vars.varsDetail.varsOptions = obj.varsOptions;
		}

		varsData = obj.varsData;
		if (!varsData) {
			varsData = this.vars.varsDetail.varsData;
		} else {
			this.vars.varsDetail.varsData = obj.varsData;
		}

		this.insChart = new Proto.Chart(this.eleWrap, varsData, varsOptions, this.insRoot);
	},

	/**
	 *
	*/
	_updateWrapContent : function(obj)
	{
		this.vars = (Object.toJSON(obj.vars)).evalJSON();
		this.eleWrap.innerHTML = '';
		if (this.vars.varsStatus.flagChartUse) {
			this.eleWrap.innerHTML = '';
			this.insChart = new Proto.Chart(this.eleWrap, this.vars.varsDetail.varsData, this.vars.varsDetail.varsOptions, this.eleScroll, this.insRoot);
		} else {
			this.eleWrap.insert(this.vars.varsDetail.strHtml);
		}
	},

	/**
	 *
	*/
	_updateWrapStyle : function()
	{
		var numWidth = this._getWrapWidth();
		if (this.vars.varsStatus.unitWidth == '%') {
			numWidth *= this.vars.varsStatus.numWidth/100;
		}
		this.eleWrap.style.width = numWidth + 'px';
		this.eleWrap.style.height = this.vars.varsStatus.numHeight + 'px';
	}
});

<?php }
}
?>