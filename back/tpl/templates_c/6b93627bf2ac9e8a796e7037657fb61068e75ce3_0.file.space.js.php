<?php /* Smarty version 3.1.24, created on 2019-10-06 06:26:37
         compiled from "/app/rucaro/back/tpl/templates/else/core/base/js/lib/space.js" */ ?>
<?php
/*%%SmartyHeaderCode:9839342735d99891df2c087_95651043%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6b93627bf2ac9e8a796e7037657fb61068e75ce3' => 
    array (
      0 => '/app/rucaro/back/tpl/templates/else/core/base/js/lib/space.js',
      1 => 1570328741,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9839342735d99891df2c087_95651043',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d99891e00ce02_60395006',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d99891e00ce02_60395006')) {
function content_5d99891e00ce02_60395006 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '9839342735d99891df2c087_95651043';
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