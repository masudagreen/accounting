<?php /* Smarty version 3.1.24, created on 2016-08-18 12:50:21
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/calenderFormNum.js" */ ?>
<?php
/*%%SmartyHeaderCode:67083259257b5af0d8e4083_85134544%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '64ff84ab0d49364ff133e0fbd02cbda44dcf0b97' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/calenderFormNum.js',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '67083259257b5af0d8e4083_85134544',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b5af0d927739_88041771',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b5af0d927739_88041771')) {
function content_57b5af0d927739_88041771 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '67083259257b5af0d8e4083_85134544';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_CalenderFormNum = Class.create(Code_Lib_ExtLib,
{

	/**
	 *
	*/
	initialize : function(obj)
	{
		this._extAllot(obj);
		this._iniVars(obj);
		this._iniListener();
		this._iniWrap();
		this._iniVarsDetail();
		this._iniForm();
	},

	/**
	 *
	*/
	iniReload : function()
	{
		this.allot({
			insCurrent : this.insCurrent,
			from       : 'preIniReload'
		});
		this.stopListener();
		this.removeWrap();
		this._iniWrap();
		this._iniVarsDetail();
		this._iniForm();
		this.allot({
			insCurrent : this.insCurrent,
			from       : 'afterIniReload'
		});
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
	_iniVars : function(obj)
	{
		var insLoad = new Code_Lib_CalenderVars();
		this.varsLoad = insLoad.iniLoad();
		this._extVars(obj);
		this.insTimeZone = this.insRoot.insTimeZone;

		this._setVars();
	},

	/**
	 *
	*/
	_setVars : function()
	{
		var cut = this.vars.varsStatus;

		var objTime = this.insTimeZone.adjustTime({stamp : new Date().getTime()});

		if (cut.flagMainUse && cut.flagMainAutoUse) cut.stampMain = objTime.stamp;
		if (cut.flagMaxUse && cut.flagMaxAutoUse) cut.stampMax = objTime.stamp;
		if (cut.flagMinUse && cut.flagMinAutoUse) cut.stampMin = objTime.stamp;
		if (cut.flagMainUse && cut.flagMaxUse && cut.stampMax < cut.stampMain) cut.stampMain = cut.stampMax;
		if (cut.flagMainUse && cut.flagMinUse && cut.stampMin > cut.stampMain) cut.stampMain = cut.stampMin;
	},

	/**
	 * Wrap
	 */
	_varsWrapWidth : 0,
	_staticWrap : {numBar : 17, numMargin : 5, numMarginHour : 20},
	_iniWrap : function()
	{
		this._varsWrapWidth = 0;
		var ele = $(document.createElement('form'));
		ele.id = this.idSelf;
		this.eleInsert.insert(ele);
		this.eleWrap = ele;
		this.eleWrap.addClassName('codeLibCalenderFormNumWrap');
		this.eleWrap.setStyle({
			width  : (this._getWrapWidth() + this._staticWrap.numMargin * 4 + this._staticWrap.numMarginHour) + 'px'
		});
	},

	/**
	 *
	 */
	_getWrapWidth : function()
	{
		var array = this.eleInsert.style.width.split('px');
		var numWidthWrap = parseFloat(array[0]);

		var numWidth = numWidthWrap - this._staticWrap.numBar - this._staticWrap.numMargin * 4 - this._staticWrap.numMarginHour;
		if (!this._varsWrapWidth) {
			this._varsWrapWidth = numWidth;

			return numWidth;

		} else {
			return this._varsWrapWidth;
		}

	},



	/**
	 * Var
	*/
	_iniVarsDetail : function(obj)
	{
		this._setVarsDetailTime();
		this._setVarsDetail();
	},

	/**
	 *
	*/
	_varsDetailTime : null,
	_setVarsDetailTime : function()
	{
		this._varsDetailTime = {
			now   : this.insTimeZone.adjustTime({stamp : new Date().getTime()}),
			main  : this._setVarsDetailTime_({str : 'Main'}),
			max   : this._setVarsDetailTime_({str : 'Max'}),
			min   : this._setVarsDetailTime_({str : 'Min'})
		};
	},

	/**
	 *
	*/
	_setVarsDetailTime_ : function(obj)
	{
		var objTime = null;
		if (this.vars.varsStatus['flag' + obj.str + 'Use']) {
			objTime = this.insTimeZone.adjustDate({
				stamp : this.vars.varsStatus['stamp' + obj.str]
			});
		}

		return objTime;
	},

	/**
	 *
	*/
	_varsDetail : null,
	_setVarsDetail : function()
	{
		this._varsDetail = [
			this._getVarsDetailYear(),
			this._getVarsDetailMonth(),
			this._getVarsDetailDate(),
			this._getVarsDetailHour(),
			this._getVarsDetailMin()
		];
	},

	/**
	 *
	*/
	_getVarsDetailYear : function()
	{
		var numMax = this._varsDetailTime.max.numYear;
		var numMin = this._varsDetailTime.min.numYear;

		var arrayOption = [];
		for (i = numMin; i <= numMax; i++) {
			var data = {};
			data.strTitle = i + this.varsLoad.varsWhole.str.year;
			data.value = i;
			arrayOption.push(data);
		}
		var numWidth = Math.floor(this._getWrapWidth() * 0.32);

		var datas = {
			id          : 'Year',
			numWidth    : numWidth,
			arrayOption : arrayOption,
			value       : this._varsDetailTime.main.numYear
		};

		return datas;
	},

	/**
	 *
	*/
	_getVarsDetailMonth : function()
	{
		var insCheck = new Code_Lib_CheckTime();
		var numMax = 12;
		var numMin = 1;
		var arrayOption = [];
		for (i = numMin; i <= numMax; i++) {
			var numYear = this._varsDetailTime.main.numYear;
			var numMonth = i - 1;

			var objStart = this.insTimeZone.adjustTime({
				stamp : new Date(numYear, numMonth , 1).getTime()
			});

			var objEnd = this.insTimeZone.adjustTime({
				stamp : new Date(numYear, numMonth + 1 , 1).getTime()
			});

			var flag = insCheck.getTerm({
				stampStart     : objStart.stamp,
				stampEnd       : objEnd.stamp,
				stampWrapStart : this._varsDetailTime.min.stamp,
				stampWrapEnd   : this._varsDetailTime.max.stamp
			});
			if (!flag) continue;

			var data = {};
			var strTitle = i + this.varsLoad.varsWhole.str.month;
			data.strTitle = (i < 10)? '0' + strTitle : strTitle;
			data.value = i;
			arrayOption.push(data);
		}

		var numWidth = Math.floor(this._getWrapWidth() * 0.17);
		var datas = {
			id          : 'Month',
			numWidth    : numWidth,
			arrayOption : arrayOption,
			value       : this._varsDetailTime.main.numMonth + 1
		};

		return datas;
	},

	/**
	 *
	*/
	_getVarsDetailDate : function()
	{
		var insCheck = new Code_Lib_CheckTime();
		var objTime = this.insTimeZone.adjustTime({
			stamp : new Date(this._varsDetailTime.main.numYear, this._varsDetailTime.main.numMonth + 1 , 1 - 1).getTime()
		});

		var numMax = objTime.numDate;
		var numMin = 1;

		var arrayOption = [];
		for (i = numMin; i <= numMax; i++) {
			var numYear = this._varsDetailTime.main.numYear;
			var numMonth = this._varsDetailTime.main.numMonth;
			var numDate = i;

			var objStart = this.insTimeZone.adjustTime({
				stamp : new Date(numYear, numMonth , numDate).getTime()
			});

			var objEnd = this.insTimeZone.adjustTime({
				stamp : new Date(numYear, numMonth, numDate + 1).getTime()
			});

			var flag = insCheck.getTerm({
				stampStart     : objStart.stamp,
				stampEnd       : objEnd.stamp,
				stampWrapStart : this._varsDetailTime.min.stamp,
				stampWrapEnd   : this._varsDetailTime.max.stamp
			});
			if (!flag) continue;

			var data = {};
			var strTitle = i + this.varsLoad.varsWhole.str.date;
			data.strTitle = (i < 10)? '0' + strTitle : strTitle;
			data.value = i;
			arrayOption.push(data);
		}

		var numWidth = Math.floor(this._getWrapWidth() * 0.17);

		var datas = {
			id          : 'Date',
			numWidth    : numWidth,
			arrayOption : arrayOption,
			value       : this._varsDetailTime.main.numDate
		};

		return datas;
	},

	/**
	 *
	*/
	_getVarsDetailHour : function()
	{
		var insCheck = new Code_Lib_CheckTime();
		var numMax = 23;
		var numMin = 0;

		var arrayOption = [];
		for (i = numMin; i <= numMax; i++) {
			var numYear = this._varsDetailTime.main.numYear;
			var numMonth = this._varsDetailTime.main.numMonth;
			var numDate = this._varsDetailTime.main.numDate;

			var objStart = this.insTimeZone.adjustTime({
				stamp : new Date(numYear, numMonth , numDate).getTime()
			});
			objStart.stamp += i * 3600 * 1000;

			var objEnd = this.insTimeZone.adjustTime({
				stamp : new Date(numYear, numMonth, numDate).getTime()
			});
			objEnd.stamp += (i + 1) * 3600 * 1000;

			var flag = insCheck.getTerm({
				stampStart     : objStart.stamp,
				stampEnd       : objEnd.stamp,
				stampWrapStart : this._varsDetailTime.min.stamp,
				stampWrapEnd   : this._varsDetailTime.max.stamp
			});
			if (!flag) continue;

			var data = {};
			var strTitle = i + this.varsLoad.varsWhole.str.hour;
			data.strTitle = (i < 10)? '0' + strTitle : strTitle;
			data.value = i;
			arrayOption.push(data);
		}

		var numWidth = Math.floor(this._getWrapWidth() * 0.17);

		var datas = {
			id          : 'Hour',
			numWidth    : numWidth,
			arrayOption : arrayOption,
			value       : this._varsDetailTime.main.numHour
		};

		return datas;
	},

	/**
	 *
	*/
	_getVarsDetailMin : function()
	{
		var numMax = 59;
		var numMin = 0;

		var arrayOption = [];
		for (i = numMin; i <= numMax; i++) {
			var data = {};
			var strTitle = i + this.varsLoad.varsWhole.str.min;
			data.strTitle = (i < 10)? '0' + strTitle : strTitle;
			data.value = i;
			arrayOption.push(data);
		}
		var numWidth = Math.floor(this._getWrapWidth() * 0.17);

		var datas = {
			id          : 'Min',
			numWidth    : numWidth,
			arrayOption : arrayOption,
			value       : this._varsDetailTime.main.numMin
		};

		return datas;
	},

	/**
	 * Form
	*/
	_iniForm : function()
	{
		this._setForm({arr : this._varsDetail});
		this._setFormValue({arr : this._varsDetail});
	},

	/**
	 *
	*/
	_setForm : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var eleTag = $(document.createElement('select'));
			this.eleWrap.insert(eleTag);
			if (obj.arr[i].id == 'Date') eleTag.addClassName('codeLibCalenderFormNumTagHour');
			else eleTag.addClassName('codeLibCalenderFormNumTag');

			eleTag.id = this.idSelf + 'Tag' + obj.arr[i].id;
			eleTag.style.width = obj.arr[i].numWidth + 'px';
			eleTag.value = obj.arr[i].value;
			this._setFormSelect({
				arr       : obj.arr[i].arrayOption,
				now       : obj.arr[i].value,
				eleInsert : eleTag
			});
			this.insListener.set({
				bindAsEvent : 0, insCurrent : this, event : 'change', strFunc : 'changeForm',
				ele : eleTag, vars : {vars : obj.arr[i]}
			});
		}

	},

	/**
	 *
	*/
	_setFormValue : function(obj)
	{
		alert((Object.toJSON(this.vars.varsDetail)));
		for (var i = 0; i < obj.arr.length; i++) {
			var str = 'num' + obj.arr[i].id;
			this.vars.varsDetail[str] = $(this.idSelf + 'Tag' + obj.arr[i].id).value;
		}
		alert((Object.toJSON(this.vars.varsDetail)));
		var objTime = this.insTimeZone.adjustTime({
			stamp : new Date(this.vars.varsDetail.numYear, this.vars.varsDetail.numMonth - 1, this.vars.varsDetail.numDate).getTime()
					 + this.vars.varsDetail.numHour * 3600 * 1000 + this.vars.varsDetail.numMin * 60 * 1000
		});
		this.vars.varsDetail.stamp = objTime.stampServer;


	},

	/**
	 *
	*/
	getValue : function()
	{
		var data = (Object.toJSON(this.vars.varsDetail)).evalJSON();

		return data;
	},

	/**
	 *
	*/
	getValueJson : function()
	{
		var data = Object.toJSON(this.vars.varsDetail);

		return data;
	},

	/**
	 *
	*/
	changeForm : function(obj)
	{
		this._setFormValue({arr : this._varsDetail});
		this.vars.varsStatus.stampMain = this.vars.varsDetail.stamp * 1000;

		this.iniReload();
	},

	/**
	 *
	*/
	_setFormSelect : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var ele = $(document.createElement('option'));
			ele.value = obj.arr[i].value;
			if(obj.now == obj.arr[i].value) ele.selected = true;
			if (obj.arr[i].flagDisabled)  ele.disabled = true;
			ele.insert(obj.arr[i].strTitle);
			obj.eleInsert.insert(ele);
		}
	}

});

<?php }
}
?>