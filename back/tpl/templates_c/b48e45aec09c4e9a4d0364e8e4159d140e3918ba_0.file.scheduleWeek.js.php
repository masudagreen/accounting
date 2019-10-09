<?php /* Smarty version 3.1.24, created on 2019-06-16 09:02:08
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/scheduleWeek.js" */ ?>
<?php
/*%%SmartyHeaderCode:8517429245d0605900d3743_30278453%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b48e45aec09c4e9a4d0364e8e4159d140e3918ba' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/scheduleWeek.js',
      1 => 1560675140,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8517429245d0605900d3743_30278453',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d0605900dacc2_12681728',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d0605900dacc2_12681728')) {
function content_5d0605900dacc2_12681728 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '8517429245d0605900d3743_30278453';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_ScheduleWeek = Class.create(Code_Lib_Schedule,
{

	/**
	 *
	*/
	initialize : function(obj)
	{
		this._iniVars(obj);
		this._iniListener();
		this._iniGraduation();
		this._iniBody();
		this.insCurrent.iniBtnBottom();
		this.insCurrent.iniPage();
		this._iniFold();
		this._iniPosition();
		this._iniLog();
		this.iniBtnSelect();
	},

	/**
	 *
	*/
	eleInsertGraduation : null,
	_iniVars : function(obj)
	{
		this._extVars(obj);
		this.eleInsertGraduation = obj.eleInsertGraduation;
		this._iniCake();
	},

	/**
	 *
	*/
	updateVars : function()
	{
		this.insCurrent.updateVars();
	},

	/**
	 * Fold
	*/
	_iniFold : function()
	{
		if (!this.insCurrent.vars.varsStatus.flagFoldUse) return;
		this._setFoldListener({arr : this.vars.varsFold});
		this._setFold({arr:this.vars.varsFold});
	},

	/**
	 *
	*/
	_setFoldListener : function(obj)
	{
		this.insListener.set({bindAsEvent : 1, insCurrent : this, event : 'mousedown',
			strFunc : '_mousedownFoldAll',
			ele : $(this.idSelf + 'GraduationWrap').down('.codeLibScheduleGraduationFold', 0), vars : ''
		});
		for (var i = 0; i < obj.arr.length; i++) {
			this.insListener.set({bindAsEvent : 1, insCurrent : this, event : 'mousedown',
				strFunc : '_mousedownFold',
				ele     : $(this.idSelf + 'BodyLineWrap' + i).down('.codeLibScheduleWeekBodyLineFold', 0),
				vars    : {vars : {id : i}}
			});
		}
	},

	/**
	 *
	*/
	_setFold : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			this._styleFoldUpdate({
				flagEffect : 0,
				arr        : obj.arr,
				vars       : {id : i}
			});
		}
	},

	/**
	 *
	*/
	_mousedownFold : function(evt,obj) {
		if (obj) evt.stop();
		else obj = evt;
		this._varFoldUpdate({arr:this.vars.varsFold, vars:obj.vars});
		this._styleFoldUpdate({
			flagEffect : 1,
			arr        : this.vars.varsFold,
			vars       : obj.vars
		});
		this.setCake();
	},

	/**
	 *
	*/
	_varFoldUpdate : function(obj)
	{
		var num = obj.vars.id;
		if (obj.arr[num].flagFoldNow) obj.arr[num].flagFoldNow = 0;
		else obj.arr[num].flagFoldNow = 1;
	},

	/**
	 *
	*/
	_styleFoldUpdate : function(obj)
	{
		var num = obj.vars.id;
		var eleBox = $(this.idSelf + 'BodyLineWrap' + num ).down('.codeLibScheduleWeekBodyLineFold', 0);
		eleBox.removeClassName('codeLibScheduleFoldClose');
		eleBox.removeClassName('codeLibScheduleFoldOpen');
		var eleBodyWrap = $(this.idSelf + 'BodyLineWrap' + num).down('.codeLibScheduleWeekBodyLineBodyWrap', 0);
		if (obj.arr[num].flagFoldNow) {
			eleBox.addClassName('codeLibScheduleFoldOpen');
			if (obj.flagEffect) {
				new Effect.BlindDown(eleBodyWrap,{
					duration : 0.5
				});
			} else {
				eleBodyWrap.show();
			}
		} else {
			eleBox.addClassName('codeLibScheduleFoldClose');
			if (obj.flagEffect) {
				new Effect.BlindUp(eleBodyWrap,{
					duration : 0.5
				});
			} else {
				eleBodyWrap.hide();
			}
		}
	},

	/**
	 *
	*/
	_mousedownFoldAll : function(evt) {
		evt.stop();
		if (this.insCurrent.vars.varsStatus.flagFoldNow) {
			this.insCurrent.vars.varsStatus.flagFoldNow = 0;
			this._varFoldUpdateAll({
				arr         : this.vars.varsFold,
				flagFoldNow : this.insCurrent.vars.varsStatus.flagFoldNow
			});
		} else {
			this.insCurrent.vars.varsStatus.flagFoldNow = 1;
			this._varFoldUpdateAll({
				arr         : this.vars.varsFold,
				flagFoldNow : this.insCurrent.vars.varsStatus.flagFoldNow
			});
		}
		this._styleFoldUpdateAll({
			arr         : this.vars.varsFold,
			flagEffect  : 1,
			flagFoldNow : this.insCurrent.vars.varsStatus.flagFoldNow
		});
		this.setCake();
	},

	/**
	 *
	*/
	_varFoldUpdateAll : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			obj.arr[i].flagFoldNow = obj.flagFoldNow;
		}
	},

	/**
	 *
	*/
	_styleFoldUpdateAll : function(obj)
	{
		var eleGraduation = $(this.idSelf + 'GraduationWrap').down('.codeLibScheduleGraduationFold', 0);
		eleGraduation.removeClassName('codeLibScheduleFoldClose');
		eleGraduation.removeClassName('codeLibScheduleFoldOpen');
		if (obj.flagFoldNow) eleGraduation.addClassName('codeLibScheduleFoldOpen');
		else eleGraduation.addClassName('codeLibScheduleFoldClose');
		for (var i = 0; i < obj.arr.length; i++) {
			var eleBox = $(this.idSelf + 'BodyLineWrap' + i).down('.codeLibScheduleWeekBodyLineFold', 0);
			var eleBodyWrap = $(this.idSelf + 'BodyLineWrap' + i).down('.codeLibScheduleWeekBodyLineBodyWrap', 0);
			eleBox.removeClassName('codeLibScheduleFoldClose');
			eleBox.removeClassName('codeLibScheduleFoldOpen');
			if (obj.arr[i].flagFoldNow) {
				eleBox.addClassName('codeLibScheduleFoldOpen');
				eleBodyWrap.show();
			} else {
				eleBox.addClassName('codeLibScheduleFoldClose');
				eleBodyWrap.hide();
			}
		}
	},

	/**
	 * Graduation
	*/
	_iniGraduation : function()
	{
		this._varGraduation();
		this._setGraduation();
	},

	/**
	 *
	*/
	_getGraduationHeight : function()
	{
		var array = this.eleInsertGraduation.style.height.split('px');

		return parseFloat(array[0]);
	},


	/**
	 *
	*/
	_getGraduationWidth : function()
	{
		var array = this.eleInsertGraduation.style.width.split('px');

		return parseFloat(array[0]);
	},

	/**
	 *
	*/
	_varsGraduation : {numWidthWrap : 0, numWidth : 0},
	_staticGraduation : {
		numWidth : 41, numSeparateSingle : 1, numSeparateDouble : 3,
		numPadding : 5, numMargin : 5, numBlock : 16, numBar : 17
	},
	eleGraduationWrap : null,
	_varGraduation : function(obj)
	{
		this._varGraduationWidth({numWidth : this._staticGraduation.numWidth});
		var numWidthGraduation = this._getGraduationWidth();
		if (numWidthGraduation > this._varsGraduation.numWidthWrap) {
			var num = this._staticGraduation.numSeparateDouble * 10
					+ this._staticGraduation.numSeparateSingle * (24 - 9)
					+ this._staticGraduation.numMargin
					+ this._staticGraduation.numBlock;
					- this._staticGraduation.numBar;
			var numWidth = Math.floor((numWidthGraduation - num)/24);
			this._varGraduationWidth({numWidth : numWidth});
		}
	},

	/**
	 *
	*/
	_varGraduationWidth : function(obj)
	{
		this._varsGraduation.numWidth = obj.numWidth
										- this._staticGraduation.numPadding * 2;
		this._varsGraduation.numWidthWrap = obj.numWidth * 24
											+ this._staticGraduation.numSeparateDouble * 10
											+ this._staticGraduation.numSeparateSingle * (24 - 9)
											+ this._staticGraduation.numMargin
											+ this._staticGraduation.numBlock;
	},

	/**
	 *
	*/
	_setGraduation : function(obj) {
		this.eleInsertGraduation.setStyle({
			position : 'relative'
		});

		var eleWrap = $(document.createElement('div'));
		eleWrap.addClassName('codeLibScheduleGraduationWrap');
		eleWrap.id = this.idSelf + 'GraduationWrap';
		eleWrap.setStyle({
			width  : this._varsGraduation.numWidthWrap + 'px',
			height : this._getGraduationHeight() + 'px'
		});
		this.eleInsertGraduation.insert(eleWrap);
		this.eleGraduationWrap = eleWrap;

		/*fold*/
		var eleFold = $(document.createElement('span'));
		eleFold.addClassName('codeLibScheduleGraduationFold');
		if (this.insCurrent.vars.varsStatus.flagFoldUse) {
			eleFold.addClassName('codeLibBaseCursorPointer');
			if (!this.insCurrent.vars.varsStatus.flagFoldNow) {
				eleFold.addClassName('codeLibScheduleFoldClose');
			}
			else  eleFold.addClassName('codeLibScheduleFoldOpen');
			eleFold.addClassName('codeLibScheduleBlock');
		}
		eleFold.unselectable = 'on';
		eleFold.addClassName('unselect');
		eleWrap.insert(eleFold);
		for (var i = 0; i < 25; i++) {
			var ele = $(document.createElement('span'));
			if ((i % 3) == 0) ele.addClassName('codeLibScheduleGraduationDouble');
			else ele.addClassName('codeLibScheduleGraduation');
			eleWrap.insert(ele);
			if (i == 24) break;
			var eleHour = $(document.createElement('span'));
			eleHour.addClassName('codeLibScheduleWeekLineBox');
			eleHour.setStyle({
				textAlign : 'left',
				width     : this._varsGraduation.numWidth + 'px',
				height    : this._getGraduationHeight() + 'px'
			});
			eleHour.addClassName('codeLibBaseCursorDefault');
			if ((i % 3) != 0) eleHour.addClassName('codeLibBaseFontCcc');
			eleHour.insert(i);
			eleWrap.insert(eleHour);
		}
	},

	/**
	 * Body
	*/
	_iniBody : function()
	{
		this._varBody();
		this._setBodyWrap();
		this._setBodyLine({arr : this.vars.varsFold});
	},

	/**
	 *
	*/
	_getBodyHeight : function()
	{
		var array = this.eleInsert.style.height.split('px');
		var data = parseFloat(array[0]) - this._staticBody.numBar;

		return data;
	},

	/**
	 *
	*/
	eleBodyWrap : null,
	_setBodyWrap : function(obj)
	{
		var id = this.idSelf + 'BodyWrap';
		var eleWrap = $(document.createElement('div'));
		eleWrap.addClassName('codeLibScheduleWeekBodyWrap');
		eleWrap.id = id;
		eleWrap.setStyle({
			width  : this._varsGraduation.numWidthWrap + 'px',
			height : this._getBodyHeight() + 'px'
		});
		eleWrap.unselectable = 'on';
		eleWrap.addClassName('unselect');
		this.eleBodyWrap = eleWrap;
		this.eleInsert.insert(eleWrap);
	},

	/**
	 *
	*/
	_staticBody : {
		numBar : 17, numLine : 7, numSeparateLine : 1,
		numHeight : 20, numBlock : 16, numMargin : 5
	},
	_varBody : function()
	{
		this._varsBody.numHeightBodyWrap = Math.floor( (
			this._getBodyHeight() - this._staticBody.numHeight * 7 - this._staticBody.numSeparateLine * 13
		) / this._staticBody.numLine );
		this._varsBody.numHeightBody = this._varsBody.numHeightBodyWrap
										- this._staticBody.numSeparateLine;
	},

	/**
	 *
	*/
	_varsBody : {numHeightBodyWrap : 0, numHeightBody : 0},
	_setBodyLine : function(obj)
	{
		var insDisplay = new Code_Lib_TimeDisplay();
		for (var i = 0; i < obj.arr.length; i++) {
			var eleLineWrap = $(document.createElement('div'));
			eleLineWrap.addClassName('codeLibScheduleWeekBodyLineWrap');
			eleLineWrap.id = this.idSelf + 'BodyLineWrap' + i;
			this.eleBodyWrap.insert(eleLineWrap);
			eleLineWrap.setStyle({
				width : this._varsGraduation.numWidthWrap + 'px'
			});
			if (i > 0) {
				var eleSeparate = $(document.createElement('div'));
				eleSeparate.addClassName('codeLibScheduleWeekBodyLineSeparate');
				eleLineWrap.insert(eleSeparate);
			}

			var eleTopWrap = $(document.createElement('div'));
			eleTopWrap.addClassName('codeLibScheduleWeekBodyTopWrap');
			eleTopWrap.setStyle({
				width : this._varsGraduation.numWidthWrap + 'px'
			});
			eleLineWrap.insert(eleTopWrap);

			var eleTop = $(document.createElement('div'));
			eleTop.addClassName('codeLibScheduleWeekBodyTop');
			eleTopWrap.insert(eleTop);

			if (this.insCurrent.varsVar.week.flagDateNow == this.insCurrent.varsVar.week.dateTime[i].numDate) {
				eleTopWrap.addClassName('codeLibScheduleNow');
			}

			/*fold*/
			var eleFold = $(document.createElement('span'));
			eleFold.addClassName('codeLibScheduleWeekBodyLineFold');
			if (this.insCurrent.vars.varsStatus.flagFoldUse) {
				eleFold.addClassName('codeLibBaseCursorPointer');
				if (!obj.arr[i].flagFoldNow) eleFold.addClassName('codeLibScheduleFoldClose');
				else  eleFold.addClassName('codeLibScheduleFoldOpen');
				eleFold.addClassName('codeLibScheduleBlock');
			}
			eleFold.unselectable = 'on';
			eleFold.addClassName('unselect');
			eleTop.insert(eleFold);

			var strClass = '',strHoliday = ' ';
			if (this.insCurrent.varsVar.week.flagActive[i]) {
				if (this.insCurrent.varsVar.week.flagSunday[i]) strClass = 'codeLibBaseFontRed';
				if (this.insCurrent.varsVar.week.flagHoliday[i]) {
					strHoliday = this.insCurrent.varsVar.week.flagHoliday[i].strTitle;
					strClass = 'codeLibBaseFontRed';
				}
				if (this.insCurrent.varsVar.week.flagHolidayTransfer[i]) {
					var str = this.insRoot.vars.varsSystem.status.strHoliday;
					strHoliday = this.insCurrent.varsLoad.varsHoliday[str].transfer;
					strClass = 'codeLibBaseFontRed';
				}
			}
			else strClass = 'codeLibBaseFontCcc';
			var eleTitle = $(document.createElement('span'));
			eleTitle.addClassName('codeLibScheduleWeekBodyLineTopTitle');
			eleTitle.addClassName('codeLibBaseCursorDefault');
			eleTitle.addClassName('clearfix');
			eleTitle.addClassName(strClass);
			var str = insDisplay.get({
				flagType : 2,
				vars     : this.insCurrent.varsVar.week.dateTime[i]
			});
			str += ' ' + strHoliday;
			eleTitle.title = str;
			eleTitle.insert(str);
			eleTop.insert(eleTitle);

			var eleLineBodyWrap = $(document.createElement('div'));
			eleLineBodyWrap.addClassName('codeLibScheduleWeekBodyLineBodyWrap');
			eleLineWrap.insert(eleLineBodyWrap);
			eleLineBodyWrap.setStyle({
				width  : this._varsGraduation.numWidthWrap + 'px',
				height : this._varsBody.numHeightBodyWrap + 'px'
			});

			var eleSeparate = $(document.createElement('div'));
			eleSeparate.addClassName('codeLibScheduleWeekBodyLineSeparate');
			eleLineBodyWrap.insert(eleSeparate);

			var eleLineBody = $(document.createElement('div'));
			eleLineBody.addClassName('codeLibScheduleWeekBodyLineBody');
			eleLineBodyWrap.insert(eleLineBody);
			eleLineBodyWrap.setStyle({
				height : this._varsBody.numHeightBody + 'px'
			});
			var str = 'hourTime' + i;
			for (var j = 0; j < 24; j++) {
				if (j == 0) {
					var eleIdle = $(document.createElement('span'));
					eleIdle.setStyle({
						width  : (this._staticBody.numBlock + this._staticBody.numMargin) + 'px',
						height : this._varsBody.numHeightBody + 'px'
					});
					eleLineBody.insert(eleIdle);
				}
				var ele = $(document.createElement('span'));

				if (this.insCurrent.varsVar.week.flagDateNow == this.insCurrent.varsVar.week.dateTime[i].numDate
					&& this.insCurrent.varsVar.week.flagHourNow == this.insCurrent.varsVar.week[str][j].numHour
				) {
					if ((j % 3) == 0) ele.addClassName('codeLibScheduleWeekBodyLineBoxSeparateDoubleNow');
					else ele.addClassName('codeLibScheduleWeekBodyLineBoxSeparateNow');
				} else {
					if ((j % 3) == 0) ele.addClassName('codeLibScheduleWeekBodyLineBoxSeparateDouble');
					else ele.addClassName('codeLibScheduleWeekBodyLineBoxSeparate');
				}
				ele.setStyle({
					height : this._varsBody.numHeightBody + 'px'
				});
				eleLineBody.insert(ele);

				var eleHour = $(document.createElement('span'));
				eleHour.id = this.idSelf + 'BodyLine' + i + '-' + j;
				eleHour.addClassName('codeLibScheduleWeekLineBox');
				eleHour.setStyle({
					width  : this._varsGraduation.numWidth + 'px',
					height : this._varsBody.numHeightBody + 'px'
				});
				eleLineBody.insert(eleHour);
			}
			if (i == (obj.arr.length - 1)) {
				var eleSeparate = $(document.createElement('div'));
				eleSeparate.addClassName('codeLibScheduleWeekBodyLineSeparate');
				eleLineWrap.insert(eleSeparate);
			}
		}
	},

	/**
	 *
	*/
	_varsBodyLineHoliday : {flag : null},
	_checkBodyLineHoliday : function(obj)
	{
		for (var i = obj.num; i >= 0; i--) {
			if (obj.arrHoliday[i] && obj.arrSunday[i]) {
				this._varsBodyLineHoliday.flag = 1;
				return;
			}
			else if (!obj.arrHoliday[i]) return;
		}
	},

	/**
	 * Resize
	*/
	_setResizeListener : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var strLine = 'hourTime' + i;
			var omit = this.insCurrent.varsVar.week[strLine];
			for (var j = 0; j < omit.length; j++) {
				var id = this.idSelf + 'BodyLine' + i + '-' + j;
				var ele = $(id);
				this.insListener.set({bindAsEvent : 0, insCurrent : this, event : 'mouseover',
					strFunc : '_mouseoverResize', ele : ele, vars : { numLine : i, objTime : omit[j]}
				});
				this.insListener.set({bindAsEvent : 0, insCurrent : this, event : 'mouseout',
					strFunc : '_mouseoutResize', ele : ele, vars : { numLine : i, objTime : omit[j]}
				});
			}
		}
		this.insListener.set({bindAsEvent : 1, insCurrent : this, event : 'mousemove',
			strFunc : '_mousemoveResize', ele : document, vars : ''
		});
		this.insListener.set({bindAsEvent : 1, insCurrent : this, event : 'mouseup',
			strFunc : '_mouseupResize', ele : document, vars : ''
		});
	},

	/**
	 *
	*/
	_staticResize : {numAreaNone : 16, numPosShift : 10, numNaviLeft : 15, numNaviTop : 5},
	_varsResize : {},
	mousedownResize : function(evt, obj) {
		/*term only*/
		this._setResizeListener({arr : this.vars.varsFold});
		this._styleLogUpdate({arr : this.vars.varsFold});
		this._styleLogDateUpdate({
			arr  : this.vars.varsFold,
			vars : obj.vars
		});
		var leftMin = $(this.insCurrent.idSelf).up('.codeLibWindow', 0).offsetLeft
					+ this.insCurrent.insFormat.eleTemplate.body.offsetLeft + this._staticResize.numAreaNone;
		var leftMax = leftMin + this.insCurrent.insFormat.eleTemplate.body.offsetWidth
					- this._staticResize.numAreaNone * 2;
		this._varsResize = {};
		this._varsResize = {
			flag     : 1,
			flagType : obj.flagType,
			ele      : evt.element(),
			vars     : obj.vars,
			varsOver : null,
			leftMax  : leftMax,
			leftMin  : leftMin,
			fixedObj : this._checkResizeFixed({
				arr      : this.insCurrent.varsVar.week.dateTime,
				flagType : obj.flagType,
				vars     : obj.vars
			})
		};
	},

	/**
	 *
	*/
	_checkResizeFixed : function(obj)
	{
		var insCheck = new Code_Lib_CheckTime();
		var cut = obj.vars.vars.varsScheduleDetail;
		var objTargetStart = this.insRoot.insTimeZone.adjustDate({
			stamp : cut.term.stampStart * 1000
		});
		var objTargetEnd = this.insRoot.insTimeZone.adjustDate({
			stamp : cut.term.stampEnd * 1000
		});
		var omit = this.insCurrent.varsVar.week.dateTime;
		var flag = insCheck.getTerm({
			stampStart     : objTargetStart.stamp,
			stampEnd       : objTargetEnd.stamp,
			stampWrapStart : omit[0].stamp,
			stampWrapEnd   : omit[omit.length - 1].stamp
		});
		var objTime;
		if (obj.flagType == 'right') {
			if (flag == 'left') objTime = omit[0];
			else if (flag == 'all') objTime = objTargetStart;
			else if (flag == 'right') objTime = objTargetStart;
		} else if (obj.flagType == 'left') {
			if (flag == 'right') objTime = omit[omit.length-1];
			else if (flag == 'all') objTime = objTargetEnd;
			else if (flag == 'left') objTime = objTargetEnd;
		}
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].numDate == objTime.numDate) {
				return {
					numHour : (obj.flagType == 'left' && flag == 'right')? 23 : objTime.numHour,
					numLine : i
				};
			}
		}
	},

	/**
	 *
	*/
	_mouseoverResize : function(obj) {

		if (!this._varsResize.flag) return;
		this._varsResize.varsOver = obj.objTime;
		if (this._varsResize.flagType == 'right') {
			var a = this._varsResize.fixedObj.numLine;
			var aA = this._varsResize.fixedObj.numHour;
			var b = obj.numLine;
			var bB = obj.objTime.numHour;
			for (var i = a; i <= b; i++) {
				var strLine = 'hourTime' + i;
				var omit = this.insCurrent.varsVar.week[strLine];
				var numStart = 0;
				var numEnd = omit.length - 1;
				if (i == a) numStart = aA;
				if (i == b) numEnd = bB;
				for (var j = numStart; j <= numEnd; j++) {
					$(this.idSelf + 'BodyLine' + i + '-' + j).addClassName('codeLibScheduleWeekBodyLineBoxOverRange');
				}
			}
		} else if (this._varsResize.flagType == 'left') {
			var a = obj.numLine;
			var aA = obj.objTime.numHour;
			var b = this._varsResize.fixedObj.numLine;
			var bB = this._varsResize.fixedObj.numHour;
			for (var i = a; i <= b; i++) {
				var strLine = 'hourTime' + i;
				var omit = this.insCurrent.varsVar.week[strLine];
				var numStart = 0;
				var numEnd = omit.length-1;
				if (i == a) numStart = aA;
				if (i == b) numEnd = bB;
				for (var j = numStart; j <= numEnd; j++) {
					$(this.idSelf + 'BodyLine' + i + '-' + j).addClassName('codeLibScheduleWeekBodyLineBoxOverRange');
				}
			}

		}
	},

	/**
	 *
	*/
	_mouseoutResize : function(obj)
	{
		if (!this._varsResize.flag) return;
		this._varsResize.varsOver = null;
		this._removeResize({arr : this.vars.varsFold});
	},

	/**
	 *
	*/
	_removeResize : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var strLine = 'hourTime' + i;
			var omit = this.insCurrent.varsVar.week[strLine];
			for (var j = 0; j < omit.length; j++) {
				$(this.idSelf + 'BodyLine' + i + '-' + j).removeClassName('codeLibScheduleWeekBodyLineBoxOverRange');
			}
		}
	},

	/**
	 *
	*/
	_mouseupResize : function(evt, obj)
	{
		if (obj) evt.stop();
		else obj = evt;
		if (!this._varsResize.flag) return;
		this.insCurrent.allot({
			insCurrent : this.insCurrent.insCurrent,
			from       : '_mouseupResize',
			vars       : this._varsResize.vars,
			objTime    : {
				mousedown : this._varsResize.vars.mainTime,
				mouseup   : this._varsResize.varsOver
			}
		});
		this._varsResize = {};
		this._removeResize({arr : this.vars.varsFold});
		this.insCurrent.iniReload();
	},

	/**
	 *
	*/
	_mousemoveResize : function(evt,obj) {
		if (!this._varsResize.flag) return;
		if (obj) evt.stop();
		else obj = evt;
		if (evt.pointerX() < this._varsResize.leftMin) {
			this.insCurrent.insFormat.eleTemplate.body.scrollLeft -= this._staticResize.numPosShift;
		} else if (evt.pointerX() > this._varsResize.leftMax) {
			this.insCurrent.insFormat.eleTemplate.body.scrollLeft += this._staticResize.numPosShift;
		}
	},

	/**
	 *
	*/
	_setMoveListener : function(obj)
	{
		if (this.insCurrent.vars.varsStatus.flagMoveRangeUse) {
			for (var i = 0; i < obj.arr.length; i++) {
				var strLine = 'hourTime' + i;
				var omit = this.insCurrent.varsVar.week[strLine];
				for (var j = 0; j < omit.length; j++) {
					var id = this.idSelf + 'BodyLine' + i + '-' + j;
					var ele = $(id);
					this.insListener.set({bindAsEvent : 0, insCurrent : this, event : 'mouseover',
						strFunc : '_mouseoverMove', ele : ele,
						vars : { numLine : i, numHour : j, objTime : omit[j]}
					});
					this.insListener.set({bindAsEvent : 0, insCurrent : this, event : 'mouseout',
						strFunc : '_mouseoutMove', ele : ele,
						vars : { numLine : i, numHour : j, objTime : omit[j]}
					});
				}
			}
		}
		this.insListener.set({bindAsEvent : 1, insCurrent : this, event : 'mousemove',
			strFunc : '_mousemoveMove', ele : document, vars : ''
		});
		this.insListener.set({bindAsEvent : 1, insCurrent : this, event : 'mouseup',
			strFunc : '_mouseupMove', ele : document, vars : ''
		});
	},

	/**
	 *
	*/
	_styleLogDateUpdate : function(obj)
	{
		var cut = obj.vars.vars.varsScheduleDetail;
		var insCheck = new Code_Lib_CheckTime();
		if (cut.flagType == 'stamp') {
			for (var i = 0; i < obj.arr.length; i++) {
				var strLine = 'hourTime' + i;
				var omit = this.insCurrent.varsVar.week[strLine];
				if (omit[0].numDate == obj.vars.mainTime.numDate) {
					var ele = $(this.idSelf + 'BodyLine' + i + '-' + obj.vars.mainTime.numHour);
					ele.addClassName(obj.vars.strClassBg);
				}
			}
		} else if (cut.flagType == 'Term') {
			var objTargetStart = this.insRoot.insTimeZone.adjustDate({
				stamp : cut.term.stampStart*1000
			});
			var objTargetEnd = this.insRoot.insTimeZone.adjustDate({
				stamp : cut.term.stampEnd*1000
			});
			for (var i = 0; i < obj.arr.length; i++) {
				var strLine = 'hourTime' + i;
				var omit = this.insCurrent.varsVar.week[strLine];
				for (var j = 0; j < omit.length; j++) {
					var flag = insCheck.getTerm({
						stampStart     : objTargetStart.stamp,
						stampEnd       : objTargetEnd.stamp,
						stampWrapStart : omit[j].stamp,
						stampWrapEnd   : omit[j].stamp + 60 * 60 * 1000
					});
					if (!flag) continue;
					var id = this.idSelf + 'BodyLine' + i + '-' + j;
					var ele = $(id);
					ele.addClassName(obj.vars.strClassBg);
				}
			}
		}
	},

	/**
	 *
	*/
	_varsMove : {},
	mousedownMove : function(evt,obj) {
		this._setMoveListener({arr : this.vars.varsFold});
		if (this.insCurrent.vars.varsStatus.flagMoveRangeUse) {
			this._styleLogUpdate({arr : this.vars.varsFold});
			this._styleLogDateUpdate({
				arr  : this.vars.varsFold,
				vars : obj
			});
		}
		this._varsMove = {};
		var leftMin = $(this.insCurrent.idSelf).up('.codeLibWindow', 0).offsetLeft
					+ this.insCurrent.insFormat.eleTemplate.body.offsetLeft + this._staticMove.numAreaNone;
		var leftMax = leftMin + this.insCurrent.insFormat.eleTemplate.body.offsetWidth
					- this._staticMove.numAreaNone * 2;
		this._varsMove = {
			flag     : 1,
			ele      : evt.element(),
			vars     : obj,
			varsOver : null,
			eleNavi  : null,
			leftMax  : leftMax,
			leftMin  : leftMin
		};
		this._setMoveNavi({
			vars : obj,
			evt  : evt
		});
		this.insCurrent.allot({
			insCurrent : this.insCurrent.insCurrent,
			from       : 'mousedownMove',
			vars       : obj.vars
		});
	},

	/**
	 *
	*/
	_staticMove : {numAreaNone : 16, numPosShift : 10, numNaviLeft : 15, numNaviTop : 5},
	_setMoveNavi : function(obj)
	{
		var zIndex = this.insRoot.setZIndex();
		var ele = $(obj.vars.id).cloneNode(true);
		$(this.insRoot.vars.varsSystem.id.root).insert(ele);
		this._varsMove.eleNavi = ele;
		ele.addClassName('codeLibScheduleNavi');
		ele.setStyle({
			left   : (obj.evt.pointerX() + this._staticMove.numNaviLeft) + 'px',
			top    : (obj.evt.pointerY() + this._staticMove.numNaviTop) + 'px',
			zIndex : zIndex
		});
	},

	/**
	 *
	*/
	_mousemoveMove : function(evt,obj) {
		if (!this._varsMove.flag) return;
		if (obj) evt.stop();
		else obj = evt;
		if (evt.pointerX() < this._varsMove.leftMin) {
			this.insCurrent.insFormat.eleTemplate.body.scrollLeft -= this._staticMove.numPosShift;
		} else if (evt.pointerX() > this._varsMove.leftMax) {
			this.insCurrent.insFormat.eleTemplate.body.scrollLeft += this._staticMove.numPosShift;
		}
		this._varsMove.eleNavi.setStyle({
			top  : (evt.pointerY() + this._staticMove.numNaviTop) + 'px',
			left : (evt.pointerX() + this._staticMove.numNaviLeft) + 'px'
		});
	},

	/**
	 *
	*/
	_mouseoverMove : function(obj)
	{
		if (!this._varsMove.flag) return;
		this._varsMove.varsOver = obj.objTime;
		var cut = this._varsMove.vars.vars.varsScheduleDetail;
		if (cut.flagType == 'stamp') {
			var id = this.idSelf + 'BodyLine' + obj.numLine + '-' + obj.numHour;
			$(id).addClassName('codeLibScheduleWeekBodyLineBoxOverRange');
		} else if (cut.flagType == 'Term') {
			obj.arr = this.vars.varsFold;
			var stampLimit = obj.objTime.stamp + (cut.term.stampEnd - cut.term.stampStart) * 1000;
			for (var i=obj.numLine; i<obj.arr.length; i++) {
				var strLine = 'hourTime' + i;
				var omit = this.insCurrent.varsVar.week[strLine];
				var num = (i == obj.numLine)? obj.numHour : 0;
				var flag = 0;
				for (var j = num; j < omit.length; j++) {
					if (omit[j].stamp <= stampLimit) {
						$(this.idSelf + 'BodyLine' + i + '-' + j).addClassName('codeLibScheduleWeekBodyLineBoxOverRange');
					} else {
						flag = 1;
						break;
					}
				}
				if (flag) break;
			}
		}
	},

	/**
	 *
	*/
	_mouseoutMove : function(obj)
	{
		if (!this._varsMove.flag) return;
		this._varsMove.varsOver = null;
		obj.arr = this.vars.varsFold;
		var cut = this._varsMove.vars.vars.varsScheduleDetail;
		if (cut.flagType == 'stamp') {
			var id = this.idSelf + 'BodyLine' + obj.numLine + '-' + obj.numHour;
			$(id).removeClassName('codeLibScheduleWeekBodyLineBoxOverRange');
		} else if (cut.flagType == 'Term') {
			this._removeMove({arr : obj.arr});
		}
	},

	/**
	 *
	*/
	_mouseupMove : function(evt,obj) {
		if (obj) evt.stop();
		else obj = evt;
		if (!this._varsMove.flag) return;

		if (this.insCurrent.vars.varsStatus.flagMoveRangeUse) {
			this.insCurrent.allot({
				insCurrent : this.insCurrent.insCurrent,
				from       : '_mouseupMove',
				vars       : this._varsMove.vars,
				objTime    : {
					mousedown : this._varsMove.vars.mainTime,
					mouseup   : this._varsMove.varsOver
				}
			});
			this._removeMove({arr : this.vars.varsFold});
		} else {
			this.insCurrent.allot({
				insCurrent : this.insCurrent.insCurrent,
				from       : '_mouseupMove',
				vars       : this._varsMove.vars
			});
		}
		this._varsMove.eleNavi.remove();
		this._varsMove = {};
		this.insCurrent.iniReload();
	},

	/**
	 *
	*/
	_removeMove : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var strLine = 'hourTime' + i;
			var omit = this.insCurrent.varsVar.week[strLine];
			for (var j=0; j<omit.length; j++) {
				var id = this.idSelf + 'BodyLine' + i + '-' + j;
				var ele = $(id);
				ele.removeClassName('codeLibScheduleWeekBodyLineBoxOverRange');
			}
		}
	},

	/**
	 * Log
	*/
	_iniLog : function()
	{
		this._varLog({
			arrDetail : (Object.toJSON(this.insCurrent.vars.varsDetail)).evalJSON(),
			arrLine   : this.vars.varsFold,
			arrMain   : this.insCurrent.varsVar.week.dateTime
		});
		this._checkLogLineHeight({
			arrDetail : this.insCurrent.vars.varsDetail,
			arrLine   : this.vars.varsFold,
			arrMain   : this.insCurrent.varsVar.week.dateTime
		});
		this._updateLogLineStyle({arr : this.vars.varsFold});
		this._setLogLineWrap({arr : this.vars.varsFold});
		this._setLog({arr : this.vars.varsFold});
	},

	/**
	 *
	*/
	_styleLogUpdate : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			/*log hide*/
			$(this.idSelf + 'BodyLineWrap' + i).down('.codeLibScheduleWeekBodyLineMiddleLogWrap', 0).hide();
			/*show all area*/
			$(this.idSelf + 'BodyLineWrap' + i).down('.codeLibScheduleWeekBodyLineBodyWrap', 0).show();
			/*style all area*/
			$(this.idSelf + 'BodyLineWrap' + i).down('.codeLibScheduleWeekBodyLineBodyWrap', 0).setStyle({
				height : this._varsBody.numHeightBodyWrap + 'px'
			});
			$(this.idSelf + 'BodyLineWrap' + i).down('.codeLibScheduleWeekBodyLineBody', 0).setStyle({
				height : this._varsBody.numHeightBodyWrap + 'px'
			});
			var array = $(this.idSelf + 'BodyLineWrap' + i).down('.codeLibScheduleWeekBodyLineBody', 0).childNodes;
			for (var j = 0; j < array.length; j++) {
				array[j].setStyle({
					height : this._varsBody.numHeightBodyWrap + 'px'
				});
			}
		}
	},

	/**
	 *
	*/
	_setLog : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var strLine = 'hourTime' + i;
			var omit = this.insCurrent.varsVar.week[strLine];
			var eleInsert = $(this.idSelf + 'BodyLineWrap' + i).down('.codeLibScheduleWeekBodyLineMiddleLogWrap', 0);
			var str = 'line' + i;
			var array = this._varsLog[str];
			for (var j = 0; j < array.length; j++) {
				var id = this.idSelf + 'BodyLineLog' + i + '-' + array[j].mainTime.numHour + '-' + array[j].vars.id;
				var flagLeftUse = 0 , flagRightUse = 0;
				if (array[j].flag == 'all') {
					flagLeftUse = 0 , flagRightUse = 0;
				} else if (array[j].flag == 'left') {
					flagLeftUse = 1 , flagRightUse = 0;
				} else if (array[j].flag == 'right') {
					flagLeftUse = 0 , flagRightUse = 1;
				} else if (array[j].flag == 'middle') {
					flagLeftUse = 1 , flagRightUse = 1;
				}
				this.insCurrent.iniLog({
					mainTime           : array[j].mainTime,
					vars               : array[j].vars,
					eleInsert          : eleInsert,
					id                 : id,
					numTop             : array[j].numTop,
					numLeft            : array[j].numLeft,
					numWidth           : array[j].numWidth,
					strTime            : array[j].strTime,
					strTitle           : array[j].vars.strTitle,
					strClass           : array[j].vars.strClass,
					strClassBg         : array[j].vars.strClassBg,
					strClassFont       : array[j].vars.strClassFont,
					strClassLoad       : array[j].vars.strClassLoad,
					flagBoldNow        : array[j].vars.flagBoldNow,
					flagBtnUse         : array[j].vars.flagBtnUse,
					flagLeftUse        : flagLeftUse,
					flagMoveUse        : (this.insCurrent.vars.varsStatus.flagMoveUse
								&& array[j].vars.flagMoveUse)? 1 : 0,
					flagResizeUse : (this.insCurrent.vars.varsStatus.flagResizeUse
								&& array[j].vars.varsScheduleDetail.flagResizeUse)? 1 : 0,
					flagResizeLeftUse  : (array[j].flagResizeLeftUse)? 1 : 0,
					flagResizeRightUse : (array[j].flagResizeRightUse)? 1 : 0,
					flagRightUse       : flagRightUse
				});

			}
		}
	},

	/**
	 *
	*/
	_setLogLineWrap : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var eleInsert = $(this.idSelf + 'BodyLineWrap' + i).down('.codeLibScheduleWeekBodyLineBodyWrap', 0);
			eleInsert.setStyle({
				position : 'relative'
			});
			var eleWrap = $(document.createElement('div'));
			eleWrap.addClassName('codeLibScheduleWeekBodyLineMiddleLogWrap');
			eleWrap.id = this.idSelf + 'BodyLineLogWrap' + i;
			eleInsert.insert(eleWrap);
		}
	},

	/**
	 *
	*/
	_updateLogLineStyle : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var str = 'line' + i;
			if (this._varsLog[str].length > 0) {
				var ele = $(this.idSelf + 'BodyLineWrap' + i).down('.codeLibScheduleWeekBodyLineTopTitle', 0);
				ele.innerHTML += ' ' + this._varsLog[str].length
							+ this.insCurrent.varsLoad.varsWhole.str.item
							+ this.insCurrent.varsLoad.varsWhole.str.hit;
				ele.title = ele.innerHTML;
			}
			var strLineHeight = 'lineHeight' + i;
			var num = this._varsLog[strLineHeight];
			var ele = $(this.idSelf + 'BodyLineWrap' + i).down('.codeLibScheduleWeekBodyLineBodyWrap', 0);
			var a = ele.offsetHeight;
			var b = num * this._staticLog.numHeight;
			if (b > a) {
				ele.setStyle({
					height : (num * this._staticLog.numHeight) + 'px'
				});
				var eleBody = $(this.idSelf + 'BodyLineWrap' + i).down('.codeLibScheduleWeekBodyLineBody', 0);
				eleBody.setStyle({
					height : (num * this._staticLog.numHeight) + 'px'
				});
				var array = eleBody.childNodes;
				for (var j = 0; j < array.length; j++) {
					array[j].setStyle({
						height : (num * this._staticLog.numHeight) + 'px'
					});
				}
			}
		}
	},

	/**
	 *
	*/
	_checkLogLineHeight : function(obj)
	{
		for (var i = 0; i < obj.arrLine.length; i++) {
			var strLine = 'hourTime' + i;
			var omit = this.insCurrent.varsVar.week[strLine];
			var num = 0;
			for (var j = 0; j < omit.length; j++) {
				var strNum = 'n' + omit[j].numDate + '-' + omit[j].numHour;
				if (this._varsLog.numLogMain[strNum]) {
					if (num < this._varsLog.numLogMain[strNum]) num = this._varsLog.numLogMain[strNum];
				}
			}
			var strLineHeight = 'lineHeight' + i;
			this._varsLog[strLineHeight] = num;
		}

	},

	/**
	 *
	*/
	_varsLog : null,
	_staticLog : {numHeight : 21, numStartLeft : 21, numPadding : 5, numSeparate : 1, numSeparateDouble : 3},
	_varLog : function(obj)
	{
		this._varsLog = {
			flagTop    : {},/*d_{date}_{top} : 1*/
			numLogMain : {},

			line0 : [], lineHeight0 : 0,
			line1 : [], lineHeight1 : 0,
			line2 : [], lineHeight2 : 0,
			line3 : [], lineHeight3 : 0,
			line4 : [], lineHeight4 : 0,
			line5 : [], lineHeight5 : 0,
			line6 : [], lineHeight6 : 0
		};

		var insDisplay = new Code_Lib_TimeDisplay();
		var insCheck = new Code_Lib_CheckTime();
		var numWidth = this._varsGraduation.numWidth + this._staticLog.numPadding * 2;

		/*sort*/
		obj.arrDetail = obj.arrDetail.sortBy(function(v,i) {
			return obj.arrDetail[i].stampRegister;
		});

		var arrayStamp = [], arrayHour = [], arrayTerm = [];
		for (var i=0; i<obj.arrDetail.length; i++) {
			var cut = obj.arrDetail[i].varsScheduleDetail;
			if (cut.flagType == 'stamp') arrayStamp.push(obj.arrDetail[i]);
			else if (cut.flagType == 'loop') arrayHour.push(obj.arrDetail[i]);
			else if (cut.flagType == 'Term') {
				var net = (cut.term.stampEnd - cut.term.stampStart) * 1000;
				if (net > 86400 * 1000) arrayTerm.push(obj.arrDetail[i]);
				else arrayHour.push(obj.arrDetail[i]);
			}
		}
		arrayHour = arrayHour.sortBy(function(v, i) {
			var cut = arrayHour[i].varsScheduleDetail;
			if (cut.flagType == 'loop') {
				var stampStart = cut.loop.start.numHour * 60 * 60 + cut.loop.start.min * 60;
				var stampEnd = (cut.loop.end.numHour == 0 && cut.loop.end.min == 0)?
							24 * 60 * 60 : (cut.loop.end.numHour * 60*60 + cut.loop.end.min * 60);
				var data = (stampStart - stampEnd) * 1000;
				return data;
			} else if (cut.flagType == 'Term') {
				var data = (cut.term.stampStart - cut.term.stampEnd) * 1000;
				return data;
			}
		});
		arrayTerm = arrayTerm.sortBy(function(v, i) {
			var cut = arrayTerm[i].varsScheduleDetail;
			var data = cut.term.stampStart - cut.term.stampEnd;
			return data;
		});

		var arrayNew1 = arrayTerm.concat(arrayHour);
		var arrayNew2 = arrayNew1.concat(arrayStamp);
		obj.arrDetail = [];
		obj.arrDetail = arrayNew2;

		for (var i=0; i<obj.arrDetail.length; i++) {
			var cut = obj.arrDetail[i].varsScheduleDetail;
			if (cut.flagType == 'stamp') {
				var objTarget = this.insRoot.insTimeZone.adjustDate({stamp : cut.stamp * 1000});
				var str = insDisplay.get({
					flagType : 1,
					vars     : objTarget
				});
				var strTime = str;
				var numHeight = 0;
				for (var j = 0; j < obj.arrLine.length; j++) {
					var strLine = 'hourTime' + j;
					var omit = this.insCurrent.varsVar.week[strLine];
					var stamp = objTarget.stamp;
					var stampWrapStart = omit[0].stamp;
					var stampWrapEnd = omit[omit.length-1].stamp + 60*60*1000;
					var flag = insCheck.getStamp({
						stamp          : stamp,
						stampWrapStart : stampWrapStart,
						stampWrapEnd   : stampWrapEnd
					});
					if (!flag) continue;

					var rowData = {
						flag     : flag,
						strTime  : strTime,
						numLeft  : 0,
						numTop   : 0,
						numWidth : numWidth,
						mainTime : null,
						vars     : this._getLogVars({
							arr  : this.insCurrent.vars.varsDetail,
							vars : obj.arrDetail[i]
						})
					};
					for (var k = 0; k < omit.length; k++) {
						var stamp = objTarget.stamp;
						var stampWrapStart = omit[k].stamp;
						var stampWrapEnd = omit[k].stamp + 60 * 60 * 1000;
						var flag = insCheck.getStamp({
							stamp          : objTarget.stamp,
							stampWrapStart : stampWrapStart,
							stampWrapEnd   : stampWrapEnd
						});
						if (!flag) continue;
						rowData.mainTime = omit[k];
						var numLeft = 0;
						for (var p = 0; p <= omit[k].numHour; p++) {
							var numSeparate = (p % 3 == 0)?
											this._staticLog.numSeparateDouble : this._staticLog.numSeparate;
							numLeft += numSeparate;
						}
						numLeft += omit[k].numHour * numWidth;
						rowData.numLeft = numLeft + this._staticLog.numStartLeft;
						var numTop = this._checkLogTop({
							arr     : obj.arrDetail,
							objTime : omit[k]
						});
						rowData.numTop = this._staticLog.numHeight * numTop + 1;
						var str = 'd' + omit[k].numDate + '-' + omit[k].numHour + '-' + numTop;
						this._varsLog.flagTop[str] = {id : obj.arrDetail[i].id};

						var strNum = 'n' + omit[k].numDate + '-' +omit[k].numHour;
						if (this._varsLog.numLogMain[strNum]) this._varsLog.numLogMain[strNum]++;
						else this._varsLog.numLogMain[strNum] = 1;

						var str = 'line' + j;
						this._varsLog[str].push(rowData);
						break;
					}
				}
			} else if (cut.flagType == 'Term') {
				var objTargetStart = this.insRoot.insTimeZone.adjustDate({stamp : cut.term.stampStart * 1000});
				var objTargetEnd = this.insRoot.insTimeZone.adjustDate({stamp : cut.term.stampEnd * 1000});

				var strStart = insDisplay.get({
					flagType : 1,
					vars     : objTargetStart
				});
				var strEnd = insDisplay.get({
					flagType : 1,
					vars     : objTargetEnd
				});
				var strTime = '(' + strStart +' ~ '+ strEnd + ')';
				var numHeight = 0;
				for (var j=0; j<obj.arrLine.length; j++) {

					var strLine = 'hourTime' + j;
					var omit = this.insCurrent.varsVar.week[strLine];

					var flagLine = insCheck.getTerm({
						stampStart     : objTargetStart.stamp,
						stampEnd       : objTargetEnd.stamp,
						stampWrapStart : omit[0].stamp,
						stampWrapEnd   : omit[omit.length - 1].stamp + 60*60*1000
					});
					if (!flagLine) continue;

					var rowData = {
						flag               : flagLine,
						flagResizeLeftUse  : 0,
						flagResizeRightUse : 0,
						strTime            : strTime,
						numLeft            : 0,
						numTop             : 0,
						numWidth           : 0,
						mainTime           : null,
						vars               : this._getLogVars({
							arr  : this.insCurrent.vars.varsDetail,
							vars : obj.arrDetail[i]
						})
					};

					/*numLeft numWidth*/
					var numStart = 0, numEnd = 0,flagStart = 0, flagEnd = 0;
					for (var k = 0; k < omit.length; k++) {
						flagStart = insCheck.getTerm({
							stampStart     : objTargetStart.stamp,
							stampEnd       : objTargetEnd.stamp,
							stampWrapStart : omit[k].stamp,
							stampWrapEnd   : omit[k].stamp + 60 * 60 * 1000
						});
						if (!flagStart) continue;
						numStart = k;
						break;
					}
					for (var k = omit.length - 1; k >= 0; k--) {
						flagEnd = insCheck.getTerm({
							stampStart     : objTargetStart.stamp,
							stampEnd       : objTargetEnd.stamp,
							stampWrapStart : omit[k].stamp,
							stampWrapEnd   : omit[k].stamp + 60 * 60 * 1000
						});
						if (!flagEnd) continue;
						numEnd = k;
						break;
					}
					rowData.mainTime = omit[numStart];
					var numLeft = 0;
					for (var p = 0; p <= omit[numStart].numHour; p++) {
						var numSeparate = (p % 3 == 0)? this._staticLog.numSeparateDouble : this._staticLog.numSeparate;
						numLeft += numSeparate;
					}
					numLeft += omit[numStart].numHour * numWidth;
					rowData.numLeft = numLeft + this._staticLog.numStartLeft;
					var numRight = 0;
					for (var p = 0; p <= omit[numEnd].numHour + 1; p++) {
						var numSeparate = (p % 3 == 0)? this._staticLog.numSeparateDouble : this._staticLog.numSeparate;
						if ((omit[numEnd].numHour + 1) != p) numRight += numSeparate;
					}
					numRight += (omit[numEnd].numHour + 1) * numWidth;
					rowData.numWidth = numRight - numLeft;

					/*flagResize*/
					var omitWeek = this.insCurrent.varsVar.week.dateTime;
					var flagWeek = insCheck.getTerm({
						stampStart     : objTargetStart.stamp,
						stampEnd       : objTargetEnd.stamp,
						stampWrapStart : omitWeek[0].stamp,
						stampWrapEnd   : omitWeek[omitWeek.length - 1].stamp
					});
					if ((flagWeek == 'right' || flagWeek == 'all') && flagStart == 'right') {
						rowData.flagResizeLeftUse = 1;
					}
					if ((flagWeek == 'left' || flagWeek == 'all') && flagEnd == 'left') {
						rowData.flagResizeRightUse = 1;
					}

					/*numTop*/
					var flagTop = 0, numTop = 0;
					for (var k = 0; k < omit.length; k++) {
						numTop = this._checkLogTopId({
							arr     : obj.arrDetail,
							id      : obj.arrDetail[i].id,
							objTime : omit[k]
						});
						if (numTop == 'false') continue;
						flagTop = 1;
						break;
					}
					if (!flagTop) {
						numTop = this._checkLogTop({
							arr     : obj.arrDetail,
							objTime : omit[numStart]
						});

						for (var p = j; p < obj.arrLine.length; p++) {
							var num = (p == j)? numStart : 0;
							var strLine = 'hourTime' + p;
							var omitA = this.insCurrent.varsVar.week[strLine];
							var flag_ = 0;
							for (var m = num; m < omitA.length; m++) {
								var flag__ = insCheck.getTerm({
									stampStart     : objTargetStart.stamp,
									stampEnd       : objTargetEnd.stamp,
									stampWrapStart : omitA[m].stamp,
									stampWrapEnd   : omitA[m].stamp + 60 * 60 * 1000
								});
								if (!flag__) {
									flag_ = 1;
									break;
								}

								/*for numTop same id*/
								var str = 'd' + omitA[m].numDate + '-' +omitA[m].numHour+ '-' + numTop;
								this._varsLog.flagTop[str] = {id : obj.arrDetail[i].id};

								/*numLog/hour*/
								var strNum = 'n' + omitA[m].numDate + '-' + omitA[m].numHour;
								if (this._varsLog.numLogMain[strNum]) this._varsLog.numLogMain[strNum]++;
								else this._varsLog.numLogMain[strNum] = 1;
							}
							if (!flag_) break;
						}
					}
					rowData.numTop = this._staticLog.numHeight * numTop + 1;
					var str = 'line' + j;
					this._varsLog[str].push(rowData);

				}
			}
			else if (cut.flagType == 'loop') {
				for (var j=0; j<obj.arrLine.length; j++) {
					var strLine = 'hourTime' + j;
					var omit = this.insCurrent.varsVar.week[strLine];
					for (var k = 0; k < omit.length; k++) {
						objEndWrap = this.insRoot.insTimeZone.adjustDate({stamp : omit[k].stamp + 60 * 60 * 1000});
						cut.loop.objStartWrap = omit[k];
						cut.loop.objEndWrap = objEndWrap;
						cut.loop.insTimeZone = this.insRoot.insTimeZone;
						var flag = insCheck.getLoop(cut.loop);
						if (!flag) continue;
						var stampStart = omit[k].stamp;
						var stampEnd =  omit[k].stamp + ((cut.loop.end.numHour - cut.loop.start.numHour) * 60 * 60
									+ (cut.loop.end.min - cut.loop.start.min) * 60) * 1000;
						var strTime = '';
						if (stampEnd == omit[k].stamp && stampStart == omit[k].stamp) {
							strTime = insDisplay.get({
								flagType : 2,
								vars     : this.insRoot.insTimeZone.adjustDate({stamp : stampStart })
							});
						} else if (stampStart == stampEnd) {
							strTime = insDisplay.get({
								flagType : 1,
								vars     : this.insRoot.insTimeZone.adjustDate({stamp : stampStart })
							});
						} else {
							var strStart = insDisplay.get({
								flagType : 1,
								vars     : this.insRoot.insTimeZone.adjustDate({stamp : stampStart })
							});
							var strEnd = insDisplay.get({
								flagType : 1,
								vars     : this.insRoot.insTimeZone.adjustDate({stamp : stampEnd })
							});
							strTime = '('+strStart+' ~ '+strEnd+')';
						}

						var rowData = {
							flag     : 'all',
							strTime  : strTime,
							numLeft  : 0,
							numTop   : 0,
							numWidth : 0,
							mainTime : omit[k],
							vars     : this._getLogVars({
								arr  : this.insCurrent.vars.varsDetail,
								vars : obj.arrDetail[i]
							})
						};

						var numStart = cut.loop.start.numHour;
						var numEnd = (cut.loop.end.numHour == 0 && cut.loop.end.min == 0)? 23 : cut.loop.end.numHour;

						var numLeft = 0;
						for (var p = 0; p <= omit[numStart].numHour; p++) {
							var numSeparate =
								(p % 3 == 0)? this._staticLog.numSeparateDouble : this._staticLog.numSeparate;
							numLeft += numSeparate;
						}
						numLeft += omit[numStart].numHour * numWidth;
						rowData.numLeft = numLeft + this._staticLog.numStartLeft;
						var numRight = 0;
						for (var p = 0; p <= omit[numEnd].numHour + 1; p++) {
							var numSeparate =
								(p % 3 == 0)? this._staticLog.numSeparateDouble : this._staticLog.numSeparate;
							if ((omit[numEnd].numHour+1) != p) numRight += numSeparate;
						}
						numRight += (omit[numEnd].numHour+1) * numWidth;
						rowData.numWidth = numRight - numLeft;

						/*numTop*/
						var flagTop = 0, numTop = 0;
						for (var k = 0; k < omit.length; k++) {
							numTop = this._checkLogTopId({
								arr     : obj.arrDetail,
								id      : obj.arrDetail[i].id,
								objTime : omit[k]
							});
							if (numTop == 'false') continue;
							flagTop = 1;
							break;
						}
						if (!flagTop) {
							numTop = this._checkLogTop({
								arr     : obj.arrDetail,
								objTime : omit[numStart]
							});
							for (var m = numStart; m < omit.length; m++) {
								var flag = insCheck.getTerm({
									stampStart     : stampStart,
									stampEnd       : stampEnd,
									stampWrapStart : omit[m].stamp,
									stampWrapEnd   : omit[m].stamp + 60 * 60 * 1000
								});
								if (!flag) break;
								/*for numTop same id*/
								var str = 'd' + omit[m].numDate + '-' + omit[m].numHour + '-' + numTop;
								this._varsLog.flagTop[str] = {id : obj.arrDetail[i].id};
								/*numLog/hour*/
								var strNum = 'n' + omit[m].numDate + '-' + omit[m].numHour;
								if (this._varsLog.numLogMain[strNum]) this._varsLog.numLogMain[strNum]++;
								else this._varsLog.numLogMain[strNum] = 1;
							}
						}
						rowData.numTop = this._staticLog.numHeight * numTop + 1;
						var str = 'line' + j;
						this._varsLog[str].push(rowData);
					}
				}
			}
		}
	},

	/**
	 *
	*/
	_getLogVars : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == obj.vars.id) return obj.arr[i];
		}
	},

	/**
	 *
	*/
	_checkLogTopId : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var str = 'd' + obj.objTime.numDate + '-' + obj.objTime.numHour + '-' + i;
			if (this._varsLog.flagTop[str]) {
				if (obj.id == this._varsLog.flagTop[str].id) {
					return i;
				}
			}
		}

		return 'false';
	},

	/**
	 *
	*/
	_checkLogTop : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var str = 'd' + obj.objTime.numDate + '-' + obj.objTime.numHour + '-' + i;
			if (!this._varsLog.flagTop[str]) {
				return i;
			}
		}
	},

	/**
	 * Select
	*/
	iniBtnSelect : function()
	{
		if (!this.insCurrent.varsLogBtn) return;
		this._setBtnSelect({
			arr       : this.vars.varsFold,
			arrSelect : this.insCurrent.varsLogBtn
		});
		this.updateVars();
	},

	/**
	 *
	*/
	_setBtnSelect : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var str = 'line' + i;
			if (!this._varsLog[str].length) continue;
			var array = this._varsLog[str];
			for (var j = 0; j < array.length; j++) {
				if (!array[j].vars.flagBtnUse) continue;
				var id = this.idSelf + 'BodyLineLog' + i + '-' + array[j].mainTime.numHour + '-' + array[j].vars.id;
				$(id).down('.codeLibScheduleTemplateTop', 0).removeClassName('codeLibScheduleSelect');
				$(id).down('.codeLibScheduleTemplateMiddle', 0).removeClassName('codeLibScheduleSelect');
				$(id).down('.codeLibScheduleTemplateBottom', 0).removeClassName('codeLibScheduleSelect');
				for (var k = 0; k < obj.arrSelect.length; k++) {
					if (array[j].vars.id == obj.arrSelect[k].id) {
						this._removeBtnSelectLoad({
							vars : array[j].vars,
							id   : id
						});
						this._removeBtnSelectBold({
							vars : array[j].vars,
							arr  : obj.arr
						});
						$(id).down('.codeLibScheduleTemplateTop', 0).addClassName('codeLibScheduleSelect');
						$(id).down('.codeLibScheduleTemplateMiddle', 0).addClassName('codeLibScheduleSelect');
						$(id).down('.codeLibScheduleTemplateBottom', 0).addClassName('codeLibScheduleSelect');
					}
				}
			}
		}
	},

	/**
	 *
	*/
	_removeBtnSelectLoad : function(obj)
	{
		if (obj.vars.strClassLoad == '') return;
		$(obj.id).down('.codeLibScheduleTemplateMiddleIcon', 0).removeClassName(obj.vars.strClassLoad);
		obj.vars.strClassLoad = '';
		$(obj.id).down('.codeLibScheduleTemplateMiddleIcon', 0).addClassName(obj.vars.strClass);
	},

	/**
	 *
	*/
	_removeBtnSelectBold : function(obj)
	{
		if (!this.insCurrent.vars.varsStatus.flagBoldUse || !obj.vars.flagBoldNow) return;
		obj.vars.flagBoldNow = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			var str = 'line' + i;
			if (!this._varsLog[str].length) continue;
			var array = this._varsLog[str];
			for (var j = 0; j < array.length; j++) {
				if (!array[j].vars.flagBtnUse || array[j].vars.id != obj.vars.id) continue;
				var id = this.idSelf + 'BodyLineLog' + i + '-' +array[j].mainTime.numHour + '-' + array[j].vars.id;
				if ($(id).down('.codeLibScheduleTemplateMiddleStrTitle', 0)) {
					var ele = $(id).down('.codeLibScheduleTemplateMiddleStrTitle', 0);
					ele.removeClassName('codeLibBaseFontBold');
				}
			}
		}
	},

	/**
	 * Position
	*/
	_iniPosition : function()
	{
		this.eleInsert.scrollLeft = (-1) * this._varsPosition.numLeft;
		this._setPosition({
			numLeft : (-1) * this._varsPosition.numLeft
		});
		this._setPositionListener();
	},

	/**
	 *
	*/
	_setPosition : function(obj)
	{
		this.eleGraduationWrap.setStyle({
			left : (-1 * obj.numLeft) + 'px'
		});
	},

	/**
	 *
	*/
	_setPositionListener : function()
	{
		this.insListener.set({flagType15 : 1, bindAsEvent : 1, insCurrent : this, event : 'scroll',
			strFunc : '_scrollPosition', ele : this.eleInsert, vars : ''
		});
	},

	/**
	 *
	*/
	_varsPosition : {numLeft : 0},
	_scrollPosition : function()
	{
		var numLeft = this.eleInsert.scrollLeft;
		var numTop = this.eleInsert.scrollTop;
		this._setPosition({
			numLeft : numLeft
		});
		this.setCake();
	},

	/**
	 * Cake
	*/
	_iniCake : function(obj)
	{
		this._getCake();
	},

	/**
	 *
	*/
	_getCakeVarsUpdate : function(obj)
	{
		obj.arr = this.vars.varsFold;
		var str = 'left';
		this._varsPosition.numLeft = obj.data[str];
		for (var i = 0; i < obj.arr.length; i++) {
			str = 'flagFoldNow' + i;
			obj.arr[i].flagFoldNow = obj.data[str];
		}
	},

	/**
	 *
	*/
	_setCakeVars : function()
	{
		var obj = {};
		obj.arr = this.vars.varsFold;
		var str;
		str = 'left';
		this._varsCake[str] = this._getCakeLeft();
		for (var i = 0; i < obj.arr.length; i++) {
			str = 'flagFoldNow' + i;
			this._varsCake[str] = obj.arr[i].flagFoldNow;
		}
	},

	/**
	 *
	*/
	_getCakeLeft : function()
	{
		var array = this.eleGraduationWrap.style.left.split('px');

		return parseFloat(array[0]);
	},

	/**
	 * Wrap
	*/
	removeWrap : function()
	{
		this.eleInsert.innerHTML = '';
		this.eleInsertGraduation.innerHTML = '';
		if (this.eleInsertBtnLeft) this.eleInsertBtnLeft.innerHTML = '';
		if (this.eleInsertBtnRight) this.eleInsertBtnRight.innerHTML = '';
	}

});
<?php }
}
?>