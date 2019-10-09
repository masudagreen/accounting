<?php /* Smarty version 3.1.24, created on 2016-08-20 07:30:04
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/calender.js" */ ?>
<?php
/*%%SmartyHeaderCode:52842859857b806fc467b94_39559384%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1c311ba0668e66914096029440fe49865dc0fbf6' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/calender.js',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '52842859857b806fc467b94_39559384',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b806fc500e73_94105153',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b806fc500e73_94105153')) {
function content_57b806fc500e73_94105153 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '52842859857b806fc467b94_39559384';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Calender = Class.create(Code_Lib_ExtLib,
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
		this._iniVar();
		this._iniTemplate();
	},

	/**
	 *
	*/
	iniReload : function()
	{
		this.stopListener();
		$(this.idSelf).remove();
		this._iniWrap();
		this._iniVar();
		this._iniTemplate();
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
	eleWrap : null, eleWrapInsert : null,
	_staticWrap : {numHeight : 200, numWidth : 213, templateHeight : 6, templateWidth : 6},
	_iniWrap : function()
	{
		this._setWrap();
	},

	/**
	 *
	*/
	_setWrap : function()
	{
		var eleWrap = $(document.createElement('div'));
		eleWrap.id = this.idSelf;
		eleWrap.addClassName('codeLibCalenderWrap');
		this.eleInsert.insert(eleWrap);
		var insTemplate = new Code_Lib_Template();
		var dataSha = insTemplate.get({
			flagType  : 'menuBox',
			numWidth  : this._staticWrap.numWidth,
			numHeight : this._staticWrap.numHeight,
			id        : ''
		});
		eleWrap.insert(dataSha);

		var dataNor = insTemplate.get({
			flagType  : 'normalBox',
			numWidth  : this._staticWrap.numWidth - this._staticWrap.templateWidth,
			numHeight : this._staticWrap.numHeight - this._staticWrap.templateHeight,
			id        : ''
		});
		eleWrap.setStyle({
			position : 'absolute',
			zIndex   : this.insRoot.setZIndex(),
			left     : this.vars.varsStatus.numLeft + 'px',
			top      : this.vars.varsStatus.numTop + 'px'
		});
		eleWrap.down('.codeLibTemplateMenuBoxMiddleMiddle', 0).insert(dataNor);
		eleWrap.down('.codeLibTemplateNormalBoxMiddleMiddle', 0).addClassName('codeLibBaseBgFff');
		this.eleWrapInsert = eleWrap.down('.codeLibTemplateNormalBoxMiddleMiddle', 0);
		this.eleWrap = eleWrap;
	},

	/**
	 *
	*/
	removeWrap : function()
	{
		this.stopListener();
		$(this.idSelf).remove();
	},

	/**
	 * Var
	*/
	_iniVar : function(obj)
	{
		this._setVarTime();
		this._setVar();
	},

	/**
	 *
	*/
	_varsVarTime : null,
	_setVarTime : function()
	{
		this._varsVarTime = {
			now   : this.insTimeZone.adjustTime({stamp : new Date().getTime()}),
			point : this._setVarTimePoint(),
			main  : this._setVarTime_({str : 'Main'}),
			max   : this._setVarTime_({str : 'Max'}),
			min   : this._setVarTime_({str : 'Min'})
		};
	},

	/**
	 *
	*/
	_setVarTimePoint : function(obj)
	{
		var objTime = null;
		if (this.vars.varsStatus.stampPoint) {
			objTime = this.insTimeZone.adjustDate({
				stamp : this.vars.varsStatus.stampPoint
			});
		}

		return objTime;
	},

	/**
	 *
	*/
	_setVarTime_ : function(obj)
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
	_varsVar : null,
	_setVar : function()
	{
		var year = this._varsVarTime.main.numYear;
		var month = this._varsVarTime.main.numMonth;
		var date = this._varsVarTime.main.numDate;



		this._varsVar = {
			strMain : this._getVarMain({year : year, month : month}),

			flagMonthPrev : this._getVarMonthPrevFlag({year : year, month : month}),
			flagMonthNext : this._getVarMonthNextFlag({year : year, month : month}),

			flagYearPrev : this._getVarYearPrevFlag({year : year, month : month}),
			flagYearNext : this._getVarYearNextFlag({year : year, month : month}),

			monthPrev : this._getVarMonthPrev({year : year, month : month}),
			monthNext : this._getVarMonthNext({year : year, month : month}),

			yearPrev : this._getVarYearPrev({year : year}),
			yearNext : this._getVarYearNext({year : year}),

			/*display date number*/
			prev : [],
			main : [],
			next : [],

			flagDatePoint : 0,
			flagDateNow   : 0,
			objTimeMain   : [],
			flagActive    : [],
			flagSunday    : [],

			flagHoliday         : [],
			flagHolidayTransfer : []
		};
		this._getVarDate({
			year  : year,
			month : month,
			date  : date
		});
	},

	/**
	 *
	*/
	_getVarMain : function(obj)
	{
		var year = obj.year;
		var month = obj.month;
		month++;
		var str = year + '/' + month;

		return str;
	},

	/**
	 *
	*/
	_getVarMonthNextFlag : function(obj)
	{
		if (this.vars.varsStatus.flagMaxUse) {

			var year = obj.year;
			var month = obj.month;
			month++;
			if (month == 12) {
				month = 0;
				year++;
			}
			var objTime = this.insTimeZone.adjustTime({
				stamp : new Date(year, month ,1).getTime()
			});
			if (this._varsVarTime.max.stamp >= objTime.stamp) return 1;
			else return 0;

		} else return 1;
	},

	/**
	 *
	*/
	_getVarMonthPrevFlag : function(obj)
	{
		if (this.vars.varsStatus.flagMinUse) {

			var year = obj.year;
			var month = obj.month;
			month--;
			if (month == -1) {
				month = 11;
				year--;
			}
			var objTime = this.insTimeZone.adjustTime({
				stamp : new Date(year, month ,1).getTime()
			});
			if (this._varsVarTime.min.stamp <= objTime.stamp) return 1;
			else return 0;

		}
		else return 1;
	},

	/**
	 *
	*/
	_getVarYearNextFlag : function(obj)
	{
		if (this.vars.varsStatus.flagMaxUse) {

			var year = obj.year;
			var month = obj.month;
			year++;
			var objTime = this.insTimeZone.adjustTime({
				stamp : new Date(year, month ,1).getTime()
			});
			if (this._varsVarTime.max.stamp >= objTime.stamp) return 1;
			else return 0;

		} else return 1;

	},

	/**
	 *
	*/
	_getVarYearPrevFlag : function(obj)
	{

		if (this.vars.varsStatus.flagMinUse) {

			var year = obj.year;
			var month = obj.month;
			year--;
			var objTime = this.insTimeZone.adjustTime({
				stamp : new Date(year, month ,1).getTime()
			});
			if (this._varsVarTime.min.stamp <= objTime.stamp) return 1;
			else return 0;

		} else return 1;
	},

	/**
	 *
	*/
	_getVarYearNext : function(obj)
	{
		var year = obj.year;
		year++;

		return year;
	},

	/**
	 *
	*/
	_getVarYearPrev : function(obj)
	{
		var year = obj.year;
		year--;

		return year;
	},

	/**
	 *
	*/
	_getVarMonthNext : function(obj)
	{
		var year = obj.year;
		var month = obj.month;
		month++;
		if (month == 12) month = 0;

		return {
			displayMonth : month+1,
			varMonth     : month
		};
	},

	/**
	 *
	*/
	_getVarMonthPrev : function(obj)
	{
		var year = obj.year;
		var month = obj.month;
		month--;
		if (month == -1) month = 11;

		return {
			displayMonth : month+1,
			varMonth     : month
		};
	},

	/**
	 *
	*/
	_getVarDate : function(obj)
	{
		var year = obj.year;
		var month = obj.month;
		var date = obj.date;

		/*prev*/
		var objTime = this.insTimeZone.adjustTime({
			stamp : new Date(year, month ,1).getTime()
		});
		var prevSpan = objTime.numDay;

		objTime = this.insTimeZone.adjustTime({
			stamp : new Date(year, month , 1 - 1).getTime()
		});
		var prevStart = objTime.numDate - prevSpan;

		/*main*/
		objTime = this.insTimeZone.adjustTime({
			stamp : new Date(year, month + 1 , 1 - 1).getTime()
		});
		var mainSpan = objTime.numDate;

		/*areaPrev*/
		var i = 0;
		var p = 0;
		var num = prevStart;
		for (i = 1; i <= prevSpan; i++) {
			num++;
			if (num >= 7) {
				p++;
			}
			/*display number*/
			this._varsVar.prev.push(num);
		}

		var insCheck = new Code_Lib_CheckTime();
		var str = this.insRoot.vars.varsSystem.status.strHoliday;
		var arrayHoliday = [];
		if (this.varsLoad.varsHoliday[str]) {
			arrayHoliday = this.varsLoad.varsHoliday[str].varsDetail;
		}


		var cut = this._varsVarTime.now;
		var cutPoint = this._varsVarTime.point;

		/*areaMain*/
		for (var j = 1; j <= mainSpan; j++, i++) {

			objTime = this.insTimeZone.adjustTime({
				stamp : new Date(year, month ,j).getTime()
			});

			/*flagHoliday*/
			var objStartWrap = (Object.toJSON(objTime)).evalJSON();

			var objEndWrap = this.insTimeZone.adjustTime({
				stamp : new Date(year, month , j + 1).getTime()
			});

			var flagHoliday = 0;
			for (k = 0; k < arrayHoliday.length; k++) {
				var str = arrayHoliday[k].flagType;
				var strFunc = 'get' + str.capitalize();
				if (str == 'term') {
					arrayHoliday[k][str].stampWrapStart = objStartWrap.stamp;
					arrayHoliday[k][str].stampWrapEnd = objEndWrap.stamp;

					flagHoliday = insCheck[strFunc](arrayHoliday[k][str]);

				} else if (str == 'loop') {
					arrayHoliday[k][str].objStartWrap = objStartWrap;
					arrayHoliday[k][str].objEndWrap = objEndWrap;
					arrayHoliday[k][str].insTimeZone = this.insTimeZone;
				}
				flagHoliday = insCheck[strFunc](arrayHoliday[k][str]);
				if (flagHoliday) break;
			}

			if (flagHoliday) this._varsVar.flagHoliday.push(arrayHoliday[k]);
			else this._varsVar.flagHoliday.push(0);

			/*holidayTransfer*/
			this._varsVar.flagHolidayTransfer.push(0);

			/*flagSunday*/
			if (objTime.numDay == 0) this._varsVar.flagSunday.push(1);
			else this._varsVar.flagSunday.push(0);

			/*now*/
			if (objTime.numYear == cut.numYear && objTime.numMonth == cut.numMonth && objTime.numDate == cut.numDate) {
				this._varsVar.flagDateNow = j;
			}

			/*point*/
			if (this.vars.varsStatus.stampPoint) {
				if (objTime.numYear == cutPoint.numYear
					&& objTime.numMonth == cutPoint.numMonth
					&& objTime.numDate == cutPoint.numDate
				) {
					this._varsVar.flagDatePoint = j;
				}
			}

			/*display number*/
			this._varsVar.main.push(j);

			/*display active number*/
			if (this.vars.varsStatus.flagMaxUse && this.vars.varsStatus.flagMinUse) {
				if (this._varsVarTime.min.stamp <= objTime.stamp && objTime.stamp <= this._varsVarTime.max.stamp) {
					this._varsVar.flagActive.push(1);

				} else {
					this._varsVar.flagActive.push(0);
				}

			} else if (this.vars.varsStatus.flagMaxUse) {
				if (objTime.stamp <= this._varsVarTime.max.stamp) {
					this._varsVar.flagActive.push(1);
				} else {
					this._varsVar.flagActive.push(0);
				}

			} else if (this.vars.varsStatus.flagMinUse) {
				if (objTime.stamp >= this._varsVarTime.min.stamp) {
					this._varsVar.flagActive.push(1);
				} else {
					this._varsVar.flagActive.push(0);
				}

			} else {
				this._varsVar.flagActive.push(1);
			}
			this._varsVar.objTimeMain.push(objTime);
		}

		if (this.insRoot.vars.varsSystem.status.strHoliday == 'jp') {
			for (var j = 0; j <= mainSpan + 1; j++) {
				if (this._varsVar.flagHoliday[j] && this._varsVar.flagSunday[j]) {
					var num = this._checkVarHolidayTransfer({
						allNum     : mainSpan,
						arrHoliday : this._varsVar.flagHoliday,
						arrSunday  : this._varsVar.flagSunday,
						num        : j + 1
					});
					this._varsVar.flagHolidayTransfer[num] = 1;
				}
			}
		}
		/*areaNext*/
		for (var j = 1; i <= 42; j++, i++) {
			/*display number*/
			this._varsVar.next.push(j);
		}
	},

	/**
	 *
	*/
	_checkVarHolidayTransfer : function(obj)
	{
		for (var i = obj.num; i < obj.allNum; i++) {
			if (obj.arrHoliday[i] || obj.arrSunday[i]) continue;
			return i;
		}
	},

	/**
	 * Template
	*/
	_staticTemplate : {
		numPadding : 1,
		numMargin  : 3,
		numYear    : 26,
		numMonth   : 36,
		numMain    : 64,
		bgColor    : '#eee'
	},

	/**
	 *
	*/
	_iniTemplate : function()
	{
		this._templatePrevYear();
		this._templatePrevMonth();
		this._templateMainMonth();
		this._templateNextMonth();
		this._templateNextYear();

		this._templateWeek({arr : this.varsLoad.varsWhole.week});
		this._templatePrevDate({arr : this._varsVar.prev});
		this._templateMainDate({arr : this._varsVar.main});
		this._templateNextDate({arr : this._varsVar.next});
	},

	/**
	 *
	*/
	_templatePrevYear : function()
	{
		var strTitle = '';
		if (this._varsVar.flagYearPrev) {
			strTitle = this.varsLoad.varsWhole.str.prevYear;
			var insBtn = new Code_Lib_Btn();
			insBtn.iniBtn({
				eleInsert  : this.eleWrapInsert,
				id         : this.idSelf + 'PrevYear',
				strFunc    : '_mousedownPrevYear',
				strTitle   : strTitle,
				numWidth   : this._staticTemplate.numYear,
				strClass   : 'codeLibCalenderImgDoubleLeftWhite',
				insCurrent : this.insSelf,
				vars       : ''
			});
			this._setListener({ins : insBtn});
			$(this.idSelf + 'PrevYear').down('.codeLibBtnMiddle',0).setStyle({
				paddingTop : this._staticTemplate.numPadding + 'px',
				paddingBottom : this._staticTemplate.numPadding + 'px'
			});

		} else {
			var insTemplate = new Code_Lib_Template();
			var ele = insTemplate.get({
				flagType : 'circleBox',
				id       : this.idSelf + 'PrevYear',
				bgColor  : this._staticTemplate.bgColor,
				strTitle : strTitle,
				numWidth : this._staticTemplate.numYear,
				strClass : 'codeLibCalenderImgDoubleLeftWhite'
			});
			this.eleWrapInsert.insert(ele);
			$(this.idSelf + 'PrevYear').down('.codeLibTemplateCircleBoxMiddle',0).setStyle({
				paddingTop    : this._staticTemplate.numPadding + 'px',
				paddingBottom : this._staticTemplate.numPadding + 'px'
			});
		}
		$(this.idSelf + 'PrevYear').setStyle({
			marginTop  : this._staticTemplate.numMargin + 'px',
			marginLeft : this._staticTemplate.numMargin + 'px',
		});
	},

	/**
	 *
	*/
	_mousedownPrevYear : function()
	{
		var year = this._varsVarTime.main.numYear;
		var month = this._varsVarTime.main.numMonth;
		year--;
		var objTime = this.insTimeZone.adjustTime({
			stamp : new Date(year, month ,1).getTime()
		});
		this.vars.varsStatus.stampMain = objTime.stamp;
		this.iniReload();
	},

	/**
	 *
	*/
	_templatePrevMonth : function()
	{
		var strTitle = '';
		if (this._varsVar.flagMonthPrev) {
			strTitle = this.varsLoad.varsWhole.str.prevMonth;
			var insBtn = new Code_Lib_Btn();
			insBtn.iniBtn({
				eleInsert  : this.eleWrapInsert,
				id         : this.idSelf + 'PrevMonth',
				strFunc    : '_mousedownPrevMonth',
				strTitle   : strTitle,
				numWidth   : this._staticTemplate.numMonth,
				strClass   : 'codeLibCalenderImgSingleLeftWhite',
				insCurrent : this.insSelf,
				vars   : ''
			});
			this._setListener({ins : insBtn});
			$(this.idSelf + 'PrevMonth').down('.codeLibBtnMiddle',0).setStyle({
				paddingTop    : this._staticTemplate.numPadding + 'px',
				paddingBottom : this._staticTemplate.numPadding + 'px'
			});

		} else {
			var insTemplate = new Code_Lib_Template();
			var ele = insTemplate.get({
				flagType : 'circleBox',
				id       : this.idSelf + 'PrevMonth',
				bgColor  : this._staticTemplate.bgColor,
				strTitle : strTitle,
				numWidth : this._staticTemplate.numMonth,
				strClass : 'codeLibCalenderImgSingleLeftWhite'
			});
			this.eleWrapInsert.insert(ele);
			$(this.idSelf + 'PrevMonth').down('.codeLibTemplateCircleBoxMiddle',0).setStyle({
				paddingTop    : this._staticTemplate.numPadding + 'px',
				paddingBottom : this._staticTemplate.numPadding + 'px'
			});
		}
		$(this.idSelf + 'PrevMonth').setStyle({
			marginTop  : this._staticTemplate.numMargin + 'px',
			marginLeft : this._staticTemplate.numMargin + 'px',
		});
	},

	/**
	 *
	*/
	_mousedownPrevMonth : function()
	{
		var year = this._varsVarTime.main.numYear;
		var month = this._varsVarTime.main.numMonth;
		month--;
		if (month == -1) {
			year--;
			month = 11;
		}
		var objTime = this.insTimeZone.adjustTime({
			stamp : new Date(year, month ,1).getTime()
		});
		this.vars.varsStatus.stampMain = objTime.stamp;
		this.iniReload();
	},

	/**
	 *
	*/
	_templateNextMonth : function()
	{
		var strTitle = '';
		if (this._varsVar.flagMonthNext) {
			strTitle = this.varsLoad.varsWhole.str.nextMonth;
			var insBtn = new Code_Lib_Btn();
			insBtn.iniBtn({
				eleInsert  : this.eleWrapInsert,
				id         : this.idSelf + 'NextMonth',
				strFunc    : '_mousedownNextMonth',
				strTitle   : strTitle,
				numWidth      : this._staticTemplate.numMonth,
				strClass   : 'codeLibCalenderImgSingleRightWhite',
				insCurrent : this.insSelf,
				vars   : ''
			});
			this._setListener({ins : insBtn});
			$(this.idSelf + 'NextMonth').down('.codeLibBtnMiddle',0).setStyle({
				paddingTop    : this._staticTemplate.numPadding + 'px',
				paddingBottom : this._staticTemplate.numPadding + 'px'
			});

		} else {
			var insTemplate = new Code_Lib_Template();
			var ele = insTemplate.get({
				flagType    : 'circleBox',
				id          : this.idSelf + 'NextMonth',
				bgColor     : this._staticTemplate.bgColor,
				strTitle    : strTitle,
				numWidth    : this._staticTemplate.numMonth,
				strClass    : 'codeLibCalenderImgSingleRightWhite'
			});
			this.eleWrapInsert.insert(ele);
			$(this.idSelf + 'NextMonth').down('.codeLibTemplateCircleBoxMiddle',0).setStyle({
				paddingTop    : this._staticTemplate.numPadding + 'px',
				paddingBottom : this._staticTemplate.numPadding + 'px'
			});
		}
		$(this.idSelf + 'NextMonth').setStyle({
			marginTop  : this._staticTemplate.numMargin + 'px',
			marginLeft : this._staticTemplate.numMargin + 'px',
		});

	},

	/**
	 *
	*/
	_mousedownNextMonth : function()
	{
		var year = this._varsVarTime.main.numYear;
		var month = this._varsVarTime.main.numMonth;
		month++;
		if (month == 12) {
			year++;
			month = 0;
		}
		var objTime = this.insTimeZone.adjustTime({
			stamp : new Date(year, month ,1).getTime()
		});
		this.vars.varsStatus.stampMain = objTime.stamp;
		this.iniReload();
	},

	/**
	 *
	*/
	_templateNextYear : function()
	{
		var strTitle = '';
		if (this._varsVar.flagYearNext) {
			strTitle = this.varsLoad.varsWhole.str.nextYear;
			var insBtn = new Code_Lib_Btn();
			insBtn.iniBtn({
				eleInsert  : this.eleWrapInsert,
				id         : this.idSelf + 'NextYear',
				strFunc    : '_mousedownNextYear',
				strTitle   : strTitle,
				numWidth   : this._staticTemplate.numYear,
				strClass   : 'codeLibCalenderImgDoubleRightWhite',
				insCurrent : this.insSelf,
				vars       : ''
			});
			this._setListener({ins : insBtn});
			$(this.idSelf + 'NextYear').down('.codeLibBtnMiddle',0).setStyle({
				paddingTop    : this._staticTemplate.numPadding + 'px',
				paddingBottom : this._staticTemplate.numPadding + 'px'
			});

		} else {
			var insTemplate = new Code_Lib_Template();
			var ele = insTemplate.get({
				flagType : 'circleBox',
				id       : this.idSelf + 'NextYear',
				bgColor  : this._staticTemplate.bgColor,
				strTitle : strTitle,
				numWidth : this._staticTemplate.numYear,
				strClass : 'codeLibCalenderImgDoubleRightWhite'
			});
			this.eleWrapInsert.insert(ele);
			$(this.idSelf + 'NextYear').down('.codeLibTemplateCircleBoxMiddle',0).setStyle({
				paddingTop    : this._staticTemplate.numPadding + 'px',
				paddingBottom : this._staticTemplate.numPadding + 'px'
			});
		}
		$(this.idSelf + 'NextYear').setStyle({
			marginTop  : this._staticTemplate.numMargin + 'px',
			marginLeft : this._staticTemplate.numMargin + 'px',
		});
	},

	/**
	 *
	*/
	_mousedownNextYear : function()
	 {
		var year = this._varsVarTime.main.numYear;
		var month = this._varsVarTime.main.numMonth;
		year++;
		var objTime = this.insTimeZone.adjustTime({
			stamp : new Date(year, month ,1).getTime()
		});
		this.vars.varsStatus.stampMain = objTime.stamp;
		this.iniReload();
	},

	/**
	 *
	*/
	_templateMainMonth : function()
	{
		var strTitle = this.varsLoad.varsWhole.str.prevMonth;
		var insBtn = new Code_Lib_Btn();
		insBtn.iniBtn({
			eleInsert  : this.eleWrapInsert,
			id         : this.idSelf + 'MainMonth',
			strFunc    : '_mousedownMainMonth',
			strTitle   : this._varsVar.strMain,
			numWidth   : this._staticTemplate.numMain,
			insCurrent : this.insSelf,
			vars   : ''
		});
		this._setListener({ins : insBtn});
		$(this.idSelf + 'MainMonth').setStyle({
			marginTop : this._staticTemplate.numMargin + 'px',
			marginLeft : this._staticTemplate.numMargin + 'px',
		});
	},

	/**
	 *
	*/
	_mousedownMainMonth : function()
	{
		this.vars.varsStatus.stampMain = this._varsVarTime.now.stamp;
		this.iniReload();
	},

	/**
	 *
	*/
	_templateWeek : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var ele = $(document.createElement('span'));
			ele.addClassName('codeLibCalenderBox');
			ele.addClassName('codeLibBaseCursorDefault');
			if (!i) ele.addClassName('codeLibBaseFontRed');
			ele.insert(obj.arr[i]);
			this.eleWrapInsert.insert(ele);
		}
	},

	/**
	 *
	*/
	_templatePrevDate : function(obj)
	{
	var id = this.idSelf + 'PrevDate';
		for (var i = 0; i < obj.arr.length; i++) {
			var ele = $(document.createElement('span'));
			ele.id = id + obj.arr[i];
			ele.unselectable = 'on';
			ele.addClassName('unselect');
			ele.addClassName('codeLibBaseCursorDefault');
			ele.addClassName('codeLibCalenderBox');
			ele.addClassName('codeLibBaseFontCcc');
			ele.insert(obj.arr[i]);
			this.eleWrapInsert.insert(ele);
		}
	},

	/**
	 *
	*/
	_templateMainDate : function(obj)
	{
		var id = this.idSelf + 'MainDate';
		for (var i = 0; i < obj.arr.length; i++) {
			var ele = $(document.createElement('span'));
			ele.id = id + obj.arr[i];
			ele.unselectable = 'on';
			ele.addClassName('unselect');
			if (this._varsVar.flagActive[i]) {
				ele.addClassName('codeLibBaseCursorPointer');
				ele.addClassName('codeLibCalenderBox');
				if (this._varsVar.flagSunday[i] || this._varsVar.flagHoliday[i]) {
					ele.addClassName('codeLibBaseFontRed');

				} else if (this._varsVar.flagHolidayTransfer[i]) {
					ele.addClassName('codeLibBaseFontRed');
				}

				if (this._varsVar.flagSunday[i]) ele.addClassName('codeLibBaseFontRed');
				this._templateMainDateListener({
					ele        : ele,
					objTime    : this._varsVar.objTimeMain[i],
					flagSunday : this._varsVar.flagSunday[i]
				});

			} else {
				ele.addClassName('codeLibCalenderBox');
				ele.addClassName('codeLibBaseFontCcc');
			}
			if (this._varsVar.flagDateNow == obj.arr[i]) ele.addClassName('codeLibCalenderBoxNow');
			if (this._varsVar.flagDatePoint == obj.arr[i]) ele.addClassName('codeLibCalenderBoxPoint');
			ele.insert(obj.arr[i]);
			this.eleWrapInsert.insert(ele);
		}
	},

	/**
	 *
	*/
	_templateMainDateListener : function(obj)
	{
		this.insListener.set({bindAsEvent : 1, insCurrent : this, event : 'mousedown', strFunc : '_mousedownDate',
			 ele : obj.ele, vars : {vars : obj}
		});
		this.insListener.set({bindAsEvent : 0, insCurrent : this, event : 'mouseover', strFunc : '_mouseoverDate',
			ele : obj.ele, vars : {vars : obj}
		});
		this.insListener.set({bindAsEvent : 0, insCurrent : this, event : 'mouseout', strFunc : '_mouseoutDate',
			ele : obj.ele, vars : {vars : obj}
		});
	},

	/**
	 *
	*/
	_templateNextDate : function(obj)
	{
		var id = this.idSelf + 'NextDate';
		for (var i = 0; i < obj.arr.length; i++) {
			var ele = $(document.createElement('span'));
			ele.id = id + obj.arr[i];
			ele.unselectable = 'on';
			ele.addClassName('unselect');
			ele.addClassName('codeLibBaseCursorDefault');
			ele.addClassName('codeLibCalenderBox');
			ele.addClassName('codeLibBaseFontCcc');
			ele.insert(obj.arr[i]);
			this.eleWrapInsert.insert(ele);
		}
	},

	/**
	 * Date
	*/
	_mousedownDate : function(evt,obj)
	{
		evt.stop();
		$(obj.vars.ele).addClassName('codeLibCalenderBoxOver');

		this.allot({
			insCurrent : this.insCurrent,
			from       : '_mousedownDate',
			objTime    : obj.vars.objTime
		});
	},

	/**
	 *
	*/
	_mouseoverDate : function(obj)
	{
		$(obj.vars.ele).addClassName('codeLibCalenderBoxOver');
	},

	/**
	 *
	*/
	_mouseoutDate : function(obj)
	{
		$(obj.vars.ele).removeClassName('codeLibCalenderBoxOver');
	}
});

<?php }
}
?>