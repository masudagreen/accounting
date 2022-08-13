<?php /* Smarty version 3.1.24, created on 2022-08-13 00:23:38
         compiled from "/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/space.js" */ ?>
<?php
/*%%SmartyHeaderCode:152901677162f6ef0a5d3dc9_28233894%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '72ccaa998a66cdaa03687f872b985b3ea3d22583' => 
    array (
      0 => '/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/space.js',
      1 => 1378384972,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '152901677162f6ef0a5d3dc9_28233894',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_62f6ef0a5daef5_73552226',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_62f6ef0a5daef5_73552226')) {
function content_62f6ef0a5daef5_73552226 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '152901677162f6ef0a5d3dc9_28233894';
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