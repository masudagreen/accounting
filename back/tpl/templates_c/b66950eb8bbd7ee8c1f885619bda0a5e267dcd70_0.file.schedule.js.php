<?php /* Smarty version 3.1.24, created on 2019-06-16 09:02:08
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/schedule.js" */ ?>
<?php
/*%%SmartyHeaderCode:10664458685d0605900a8a76_92759618%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b66950eb8bbd7ee8c1f885619bda0a5e267dcd70' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/schedule.js',
      1 => 1560675139,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '10664458685d0605900a8a76_92759618',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d0605900afb15_70355256',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d0605900afb15_70355256')) {
function content_5d0605900afb15_70355256 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '10664458685d0605900a8a76_92759618';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Schedule = Class.create(Code_Lib_ExtLib,
{

	/**
	 *
	*/
	iniLoad : function(obj)
	{
		this._extAllot(obj);
		this._iniVars(obj);
		this._iniListener();
		this._iniWrap();
		this._iniFormat();
		this._iniVar();
		this._iniNavi();
		this._iniChild();
	},

	/**
	 *
	*/
	iniReload : function()
	{
		this.insChild.stopListener();
		this.stopListener();
		$(this.idSelf).remove();
		this._iniWrap();
		this._iniFormat();
		this._iniVar();
		this._iniNavi();
		this._iniChild();
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
		this._iniCake();
		this._iniLogBtnVars();
		this.allot({
			insCurrent : this.insCurrent,
			from       : '_iniVars'
		});
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
	 *
	*/
	updateVars : function()
	{
		this.allot({
			insCurrent : this.insCurrent,
			from : 'updateVars',
			vars : {
				vars       : this.vars,
				varsSelect : this.varsLogBtn
			}
		});
	},

	/**
	 * Cake
	*/
	_iniCake : function()
	{
		this._getCake();
	},

	/**
	 *
	*/
	_getCakeVarsUpdate : function(obj)
	{
		var str = 'stampMain';
		this.vars.varsStatus.stampMain = obj.data[str];
		str = 'flagNow';
		this.vars.varsStatus.flagNow = obj.data[str];
	},

	/**
	 *
	*/
	_setCakeVars : function()
	{
		var str;
		str = 'stampMain';
		this._varsCake[str] = this.vars.varsStatus.stampMain;
		str = 'flagNow';
		this._varsCake[str] = this.vars.varsStatus.flagNow;
	},

	/**
	 * Wrap
	*/
	_iniWrap : function()
	{
		this._extWrap();
		this.eleWrap.style.width = this._getWrapWidth() + 'px';
		this.eleWrap.style.height = (this._getWrapHeight() - 1) + 'px';
	},

	/**
	 *
	*/
	_iniListener : function()
	{
		this._extListener();
	},

	/**
	 * Format
	*/
	_iniFormat : function()
	{
		this._extFormat();
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
	_setVarTime : function() {
		this._varsVarTime = {
			now  : this.insTimeZone.adjustTime({stamp : new Date().getTime()}),
			main : this._setVarTime_({str : 'main'}),
			max  : this._setVarTime_({str : 'max'}),
			min  : this._setVarTime_({str : 'min'})
		};
	},

	/**
	 *
	*/
	_setVarTime_ : function(obj)
	{
		var objTime = null;
		var str = obj.str.capitalize();
		if (this.vars.varsStatus[ 'flag' + str + 'Use' ]) {
			objTime = this.insTimeZone.adjustDate({
				stamp : this.vars.varsStatus[ 'stamp' + str ]
			});
		}

		return objTime;
	},

	/**
	 *
	*/
	varsVar : null,
	_setVar : function()
	{

		var cut = this._varsVarTime.main;
		var numYear = cut.numYear;
		var numMonth = cut.numMonth;
		var numDate = cut.numDate;
		var numDay = cut.numDay;

		var numWeek = this._getNumWeek({numYear : numYear, numMonth : numMonth, numDate : numDate});

		this.varsVar = {
			navi : {
				numWeek        : numWeek,
				strMain        : this._getVarMain({numWeek : numWeek }),
				flagDoublePrev : this._getVarDoublePrevFlag(),
				flagDoubleNext : this._getVarDoubleNextFlag(),
				flagSinglePrev : this._getVarSinglePrevFlag(),
				flagSingleNext : this._getVarSingleNextFlag()
			},
			month : {
				prev                : [],
				main                : [],
				next                : [],
				strPrevEnd          : '',
				strMainStart        : '',
				strMainEnd          : '',
				strNextStart        : '',
				flagDateNow         : 0,
				mainTime            : [],
				flagActive          : [],
				flagHoliday         : [],
				flagHolidayTransfer : [],
				flagSunday          : []
			},
			week : {
				flagDateNow         : 0,
				flagHourNow         : 0,
				dateTime            : [],
				hourTime0           : [],
				hourTime1           : [],
				hourTime2           : [],
				hourTime3           : [],
				hourTime4           : [],
				hourTime5           : [],
				hourTime6           : [],
				flagActive          : [],
				flagHoliday         : [],
				flagHolidayTransfer : [],
				flagSunday          : []
			}
		};

		this._getVarDateMonth();


		this._getVarDateWeek();

	},

	/**
	 *
	*/
	_getNumWeek : function(obj)
	{
		var numYear = obj.numYear;
		var numMonth = obj.numMonth;
		var numDate = obj.numDate;

		var objTime = this.insTimeZone.adjustTime({
			stamp : new Date(numYear, numMonth + 1 , 1 - 1).getTime()
		});
		var mainSpan = objTime.numDate;

		var num = 1;
		for (var j = 1; j <= mainSpan; j++) {
			var objTime = this.insTimeZone.adjustTime({
				stamp : new Date(numYear, numMonth, j).getTime()
			});
			if (j == numDate) return num;
			if (objTime.numDay == 6) num++;
		}
	},

	/**
	 *
	*/
	_getVarDoubleNextFlag : function(obj)
	{
		var cut = this._varsVarTime.main;
		var numYear = cut.numYear;
		var numMonth = cut.numMonth;
		var objTime;
		if (this.vars.varsStatus.flagNow == 'month') {
			numYear++;
			objTime = this.insTimeZone.adjustTime({
				stamp : new Date(numYear, numMonth, 1).getTime()
			});

		} else if (this.vars.varsStatus.flagNow == 'week') {
			numMonth++;
			if (numMonth == 12) {
				numMonth = 0;
				numYear++;
			}
			objTime = this.insTimeZone.adjustTime({
				stamp : new Date(numYear, numMonth, 1).getTime()
			});
		}
		if (this.vars.varsStatus.flagMaxUse) {
			if (this._varsVarTime.max.stamp >= objTime.stamp) return 1;
			else return 0;
		}
		else return 1;

	},

	/**
	 *
	*/
	_getVarDoublePrevFlag : function()
	{
		var cut = this._varsVarTime.main;
		var numYear = cut.numYear;
		var numMonth = cut.numMonth;
		var objTime;
		if (this.vars.varsStatus.flagNow == 'month') {
			numYear--;
			objTime = this.insTimeZone.adjustTime({
				stamp : new Date(numYear, numMonth, 1).getTime()
			});
		} else if (this.vars.varsStatus.flagNow == 'week') {
			numMonth--;
			if (numMonth == -1) {
				numMonth = 11;
				numYear--;
			}
			objTime = this.insTimeZone.adjustTime({
				stamp : new Date(numYear, numMonth, 1).getTime()
			});
		}
		if (this.vars.varsStatus.flagMinUse) {
			if (this._varsVarTime.min.stamp <= objTime.stamp) return 1;
			else return 0;
		}
		else return 1;
	},

	/**
	 *
	*/
	_getVarSingleNextFlag : function()
	{
		var cut = this._varsVarTime.main;
		var numYear = cut.numYear;
		var numMonth = cut.numMonth;
		var stamp = cut.stamp;
		var objTime;
		if (this.vars.varsStatus.flagNow == 'month') {
			numMonth++;
			if (numMonth == 12) {
				numMonth = 0;
				numYear++;
			}
			objTime = this.insTimeZone.adjustTime({
				stamp : new Date(numYear, numMonth, 1).getTime()
			});

		} else if (this.vars.varsStatus.flagNow == 'week') {
			stamp += 7 * 86400 * 1000;
			objTime = this.insTimeZone.adjustTime({
				stamp : stamp
			});
		}

		if (this.vars.varsStatus.flagMaxUse) {
			if (this._varsVarTime.max.stamp >= objTime.stamp) return 1;
			else return 0;
		}
		else return 1;
	},

	/**
	 *
	*/
	_getVarSinglePrevFlag : function()
	{
		var cut = this._varsVarTime.main;
		var numYear = cut.numYear;
		var numMonth = cut.numMonth;
		var stamp = cut.stamp;
		var objTime;
		if (this.vars.varsStatus.flagNow == 'month') {
			numMonth--;
			if (numMonth == -1) {
				numMonth = 11;
				numYear--;
			}
			objTime = this.insTimeZone.adjustTime({
				stamp : new Date(numYear, numMonth, 1).getTime()
			});

		} else if (this.vars.varsStatus.flagNow == 'week') {
			stamp -= 7*86400*1000;
			objTime = this.insTimeZone.adjustTime({
				stamp : stamp
			});
		}

		if (this.vars.varsStatus.flagMinUse) {
			if (this._varsVarTime.min.stamp <= objTime.stamp) return 1;
			else return 0;
		}
		else return 1;
	},

	/**
	 *
	*/
	_getVarDateMonth : function()
	{
		var cut = this._varsVarTime.main;
		var numYear = cut.numYear;
		var numMonth = cut.numMonth;
		var numDate = cut.numDate;

		/*prev*/
		var objTime = this.insTimeZone.adjustTime({
			stamp : new Date(numYear, numMonth, 1).getTime()
		});
		var prevSpan = objTime.numDay;

		objTime = this.insTimeZone.adjustTime({
			stamp : new Date(numYear, numMonth, 1 - 1).getTime()
		});

		var prevStart = objTime.numDate - prevSpan;
		this.varsVar.month.strPrevEnd = objTime.numYear + '/' + (objTime.numMonth + 1) + '/' + objTime.numDate;

		/*main*/
		objTime = this.insTimeZone.adjustTime({
			stamp : new Date(numYear, numMonth + 1 , 1 - 1).getTime()
		});
		var mainSpan = objTime.numDate;
		this.varsVar.month.strMainStart = objTime.numYear + '/' + (objTime.numMonth + 1) + '/' + 1;
		this.varsVar.month.strMainEnd = objTime.numYear + '/' + (objTime.numMonth + 1) + '/' + mainSpan;

		/*next*/
		objTime = this.insTimeZone.adjustTime({
			stamp : new Date(numYear, numMonth + 1 , 1).getTime()
		});
		this.varsVar.month.strNextStart = objTime.numYear + '/' + (objTime.numMonth + 1) + '/' + 1;

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
			this.varsVar.month.prev.push(num);
		}

		var insCheck = new Code_Lib_CheckTime();
		var str = this.insRoot.vars.varsSystem.status.strHoliday;

		var arrayHoliday = [];
		if (this.varsLoad.varsHoliday[str]) {
			arrayHoliday = this.varsLoad.varsHoliday[str].varsDetail;
		}

		var cut = this._varsVarTime.now;

		/*areaMain*/
		for (var j = 1; j <= mainSpan; j++, i++) {
			objTime = this.insTimeZone.adjustTime({
				stamp : new Date(numYear, numMonth, j).getTime()
			});

			/*flagHoliday*/
			var objStartWrap = this.insTimeZone.adjustTime({
				stamp : new Date(numYear, numMonth, j).getTime()
			});

			var objEndWrap = this.insTimeZone.adjustTime({
				stamp : new Date(numYear, numMonth, j + 1).getTime()
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

			if (flagHoliday) this.varsVar.month.flagHoliday.push(arrayHoliday[k]);
			else this.varsVar.month.flagHoliday.push(0);

			/*holidayTransfer*/
			this.varsVar.month.flagHolidayTransfer.push(0);

			/*sunday*/
			if (objTime.numDay == 0) this.varsVar.month.flagSunday.push(1);
			else this.varsVar.month.flagSunday.push(0);

			/*now*/
			if (objTime.numYear == cut.numYear && objTime.numMonth == cut.numMonth && objTime.numDate == cut.numDate) {
				this.varsVar.month.flagDateNow = j;
			}

			/*display number*/
			this.varsVar.month.main.push(j);

			/*display active number*/
			if (this.vars.varsStatus.flagMaxUse && this.vars.varsStatus.flagMinUse) {
				if (objTime.stamp <= this._varsVarTime.max.stamp &&
					objTime.stamp >= this._varsVarTime.min.stamp
				) {
					this.varsVar.month.flagActive.push(1);

				} else {
					this.varsVar.month.flagActive.push(0);
				}

			} else if (this.vars.varsStatus.flagMaxUse) {
				if (objTime.stamp <= this._varsVarTime.max.stamp) this.varsVar.month.flagActive.push(1);
				else this.varsVar.month.flagActive.push(0);

			} else if (this.vars.varsStatus.flagMinUse) {
				if (objTime.stamp >= this._varsVarTime.min.stamp) this.varsVar.month.flagActive.push(1);
				else this.varsVar.month.flagActive.push(0);

			} else {
				this.varsVar.month.flagActive.push(1);
			}

			this.varsVar.month.mainTime.push(objTime);
		}

		if (this.insRoot.vars.varsSystem.status.strHoliday == 'jp') {
			for (var j = 0; j <= mainSpan + 1; j++) {
				if (this.varsVar.month.flagHoliday[j] && this.varsVar.month.flagSunday[j]) {
					var num = this._checkVarHolidayTransfer({
						allNum     : mainSpan,
						arrHoliday : this.varsVar.month.flagHoliday,
						arrSunday  : this.varsVar.month.flagSunday,
						num        : j + 1
					});
					this.varsVar.month.flagHolidayTransfer[num] = 1;
				}
			}
		}

		/*areaNext*/
		for (var j = 1; i <= 42; j++, i++) {
			/*display number*/
			this.varsVar.month.next.push(j);
		}

	},

	/**
	 *
	*/
	_getVarDateWeek : function() {

		var omit = this._varsVarTime.main;
		var numYear = omit.numYear;
		var numMonth = omit.numMonth;
		var numDate = omit.numDate;

		var objTime = this.insTimeZone.adjustTime({
			stamp : new Date(numYear, numMonth, numDate).getTime()
		});
		var stamp = objTime.stamp;

		var insCheck = new Code_Lib_CheckTime();
		var str = this.insRoot.vars.varsSystem.status.strHoliday;
		var arrayHoliday = [];
		if (this.varsLoad.varsHoliday[str]) {
			arrayHoliday = this.varsLoad.varsHoliday[str].varsDetail;
		}
		var cut = this._varsVarTime.now;

		for (var i = 0; i < 7; i++) {


			/*hour*/
			for (var j = 0; j < 24; j++) {
				objTime = this.insTimeZone.adjustDate({stamp : stamp});
				var str = 'hourTime' + i;
				this.varsVar.week[str].push(objTime);
				/*now*/
				if (objTime.numYear == cut.numYear
					&& objTime.numMonth == cut.numMonth
					&& objTime.numDate == cut.numDate
					&& objTime.hour == cut.hour
				) {
					this.varsVar.week.flagHourNow = cut.hour;
				}
				stamp += 3600 * 1000;
			}
			/*flagHoliday*/
			var objStartWrap = objTime;
			var objEndWrap = this.insTimeZone.adjustDate({stamp : stamp});

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
			if (flagHoliday) this.varsVar.week.flagHoliday.push(arrayHoliday[k]);
			else this.varsVar.week.flagHoliday.push(0);

			/*holidayTransfer*/
			this.varsVar.week.flagHolidayTransfer.push(0);

			/*sunday*/
			if (objTime.numDay == 0) this.varsVar.week.flagSunday.push(1);
			else this.varsVar.week.flagSunday.push(0);

			/*now*/
			if (objTime.numYear == cut.numYear
				&& objTime.numMonth == cut.numMonth
				&& objTime.numDate == cut.numDate
			) {
				this.varsVar.week.flagDateNow =  cut.numDate;
			}

			/*display active number*/
			if (this.vars.varsStatus.flagMaxUse && this.vars.varsStatus.flagMinUse) {
				if (objTime.stamp <= this._varsVarTime.max.stamp
					&& objTime.stamp >= this._varsVarTime.min.stamp
				) {
					this.varsVar.week.flagActive.push(1);
				}
				else this.varsVar.week.flagActive.push(0);

			} else if (this.vars.varsStatus.flagMaxUse) {
				if (objTime.stamp <= this._varsVarTime.max.stamp) this.varsVar.week.flagActive.push(1);
				else this.varsVar.week.flagActive.push(0);

			} else if (this.vars.varsStatus.flagMinUse) {
				if (objTime.stamp >= this._varsVarTime.min.stamp) this.varsVar.week.flagActive.push(1);
				else this.varsVar.week.flagActive.push(0);

			} else {
				this.varsVar.week.flagActive.push(1);
			}
			this.varsVar.week.dateTime.push(objTime);

		}

		/*jpUnique*/
		if (this.insRoot.vars.varsSystem.status.strHoliday == 'jp') {

			for (var j = 0; j < 7; j++) {
				if (this.varsVar.week.flagHoliday[j] && this.varsVar.week.flagSunday[j]) {
					var num = this._checkVarHolidayTransfer({
						allNum     : 7,
						arrHoliday : this.varsVar.week.flagHoliday,
						arrSunday  : this.varsVar.week.flagSunday,
						num        : j + 1
					});
					this.varsVar.week.flagHolidayTransfer[num] = 1;
				}
			}
			if (this.varsVar.week.flagSunday[0]) return;

			objTime = this.insTimeZone.adjustTime({
				stamp : new Date(numYear, numMonth, numDate).getTime()
			});
			var stamp = objTime.stamp;
			for (var j = 1; j <= 31; j++) {
				/*holiday*/
				var objStartWrap = this.insTimeZone.adjustDate({stamp : stamp - 86400 * 1000 * j});
				var objEndWrap = this.insTimeZone.adjustDate({stamp : stamp - 86400 * 1000 * j + 86400 * 1000 });
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
				/*sunday*/
				var flagSunday = 0;
				if (objStartWrap.numDay == 0) flagSunday = 1;

				if (!flagSunday && flagHoliday) continue;
				else if (flagSunday && flagHoliday) this.varsVar.week.flagHolidayTransfer[0] = 1;

				return;
			}
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
	 *
	*/
	_getVarMain : function(obj)
	{
		var cut = this._varsVarTime.main;
		var numYear = cut.numYear;
		var numMonth = cut.numMonth;
		var numDay = cut.numDay;
		var numWeek = obj.numWeek;
		numMonth++;

		if (numMonth < 10) numMonth = '0' + numMonth;
		var str = numYear + '/' + numMonth;
		var numDate = cut.numDate;

		if (numDate < 10) numDate = '0' + numDate;
		str = str + '/' + numDate + ' (' + this.varsLoad.varsWhole.week[numDay] + ') '
			+ numWeek + this.varsLoad.varsWhole.str.week;

		return str;
	},

	/**
	 * Log
	 * obj = {
	 * 	mainTime      : object
	 * 	vars      : object
	 * 	eleInsert     : element
	 * 	id            : string
	 * 	numTop        : int
	 * 	numLeft       : int
	 * 	numWidth      : int
	 * 	strTitle      : string
	 * 	strClass      : string
	 * 	strClassBg    : string
	 * 	strClassFont  : string
	 * 	flagBoldNow   : int
	 * 	flagBtnUse    : int
	 * 	flagLeftUse   : int
	 * 	flagMoveUse   : int
	 * 	flagResizeUse : int
	 * 	flagRightUse  : int
	 * }
	*/
	_staticLog : {numWidthSide : 5, numLeft : 5, numRight : 5, numResize : 5,
		numResize : 5, numMargin : 1, numBlock : 16, numIdle : 5
	},
	iniLog : function(obj)
	{
		var data = this._setLog(obj);
		obj.eleInsert.insert(data);
		this._setLogListener(obj);
	},

	/**
	 *
	*/
	_setLogListener : function(obj) {
		this._setLogBtnListener(obj);
		this._setLogMoveListener(obj);
		this._setLogResizeListener(obj);
	},

	/**
	 * Btn
	*/
	_iniLogBtnVars : function()
	{
		this.varsLogBtn = [];
	},

	/**
	 *
	*/
	varsLogBtn : [],
	_setLogBtnListener : function(obj)
	{
		if (!obj.flagBtnUse) return;
		var ele = $(obj.id);
		var eleText = $(obj.id).down('.codeLibScheduleTemplateMiddleStrTitle', 0);
		this.insListener.set({bindAsEvent : 1, insCurrent : this, event : 'mousedown',
			strFunc : '_mousedownLogBtn', ele : ele, vars : { vars : obj.vars, mainTime : obj.mainTime}});
		this.insListener.set({bindAsEvent : 0, insCurrent : this, event : 'mouseover',
			strFunc : '_mouseoverLogBtn', ele : eleText, vars : { ele : eleText}});
		this.insListener.set({bindAsEvent : 0, insCurrent : this, event : 'mouseout',
			strFunc : '_mouseoutLogBtn', ele : eleText, vars : { ele : eleText}});
		this.insListener.set({bindAsEvent : 1, insCurrent : this, event : 'dblclick',
			strFunc : '_dblclickLogBtn', ele : ele, vars : {
				ele : ele, vars : obj.vars,
				mainTime : obj.mainTime
			}
		});
	},

	/**
	 *
	*/
	removeBtnSelect : function()
	{
		this.varsLogBtn = [];
	},

	/**
	 *
	*/
	_dblclickLogBtn : function(evt, obj)
	{
		if (obj) evt.stop();
		else obj = evt;
		this.allot({
			insCurrent : this.insCurrent,
			from       : '_dblclickLogBtn',
			vars       : {
				detailLog : obj.vars,
				mainTime  : obj.mainTime
			}
		});
	},

	/**
	 *
	*/
	_mousedownLogBtn : function(evt, obj)
	{
		if (obj) evt.stop();
		else obj = evt;
		this.varsLogBtn = [];
		this.varsLogBtn.push(obj.vars);
		this.insChild.iniBtnSelect();
		this.allot({
			insCurrent : this.insCurrent,
			from       : '_mousedownLogBtn',
			vars       : {
				detailLog : obj.vars,
				mainTime  : obj.mainTime
			}
		});
	},

	/**
	 *
	*/
	_mouseoverLogBtn : function(obj)
	{
		obj.ele.addClassName('codeLibBaseUnderline');
	},

	/**
	 *
	*/
	_mouseoutLogBtn : function(obj)
	{
		obj.ele.removeClassName('codeLibBaseUnderline');
	},

	/**
	 * Resize
	*/
	_setLogResizeListener : function(obj)
	{
		if (!obj.flagResizeUse) return;
		if (obj.flagResizeLeftUse) this.insListener.set({bindAsEvent : 1, insCurrent : this,
			event : 'mousedown',strFunc : '_mousedownLogResize',
			ele : $(obj.id).down('.codeLibScheduleTemplateMiddleResize', 0),
			vars : { vars : obj, flagType : 'left' }
		});
		if (obj.flagResizeRightUse) this.insListener.set({bindAsEvent : 1, insCurrent : this,
			event : 'mousedown',strFunc : '_mousedownLogResize',
			ele : $(obj.id).down('.codeLibScheduleTemplateMiddleResize',1),
			vars : { vars : obj, flagType : 'right' }
		});
	},

	/**
	 *
	*/
	_mousedownLogResize : function(evt, obj) {
		if (obj) evt.stop();
		else obj = evt;
		this.insChild.mousedownResize(evt, obj);
	},

	/**
	 * Move
	*/
	_setLogMoveListener : function(obj)
	{
		if (!obj.flagMoveUse) return;
		var ele = $(obj.id).down('.codeLibScheduleTemplateMiddleMove', 0);
		this.insListener.set({bindAsEvent : 1, insCurrent : this, event : 'mousedown',
			strFunc : '_mousedownLogMove', ele : ele, vars : obj
		});
	},

	/**
	 *
	*/
	_mousedownLogMove : function(evt, obj) {
		if (obj) evt.stop();
		else obj = evt;
		this.insChild.mousedownMove(evt, obj);
	},

	/**
	 * obj = {
	 * 	id            : string
	 * 	numTop        : int
	 * 	numLeft       : int
	 * 	numWidth      : int
	 * 	strTitle      : string
	 * 	strTime       : string
	 * 	strClass      : string
	 * 	strClassBg    : string
	 * 	strClassFont  : string
	 * 	strClassLoad  : string
	 * 	flagBoldNow   : int
	 * 	flagLeftUse   : int
	 * 	flagMoveUse   : int
	 * 	flagResizeUse : int
	 * 	flagResizeLeftUse : int
	 * 	flagResizeRightUse : int
	 * 	flagRightUse  : int
	 * }
	*/
	_setLog : function(obj) {
		/*if (!obj.strClassBg) obj.strClassBg = 'codeLibBaseBgYellow';*/
		obj.strClassBg = 'codeLibBaseBgYellow';
		obj.numWidthMiddle = obj.numWidth;
		obj.numHeight = this._staticLog.numBlock;
		obj.numWidthSide = this._staticLog.numWidthSide;
		obj.numWidthTop = obj.numWidth - this._staticLog.numMargin * 2;
		if (!obj.flagRightUse) {
			obj.numWidthTitle = obj.numWidthMiddle - this._staticLog.numIdle * 2;
		}
		else {
			obj.numWidthTitle = obj.numWidthMiddle - this._staticLog.numIdle;
		}
		if (obj.flagLeftUse) {
			obj.numWidthTitle -= this._staticLog.numLeft + this._staticLog.numIdleLeft;
		}
		if (obj.strClass) {
			obj.numWidthTitle -= this._staticLog.numBlock + this._staticLog.numIdle;
		}
		if (obj.flagRightUse) {
			obj.numWidthTitle -= this._staticLog.numRight + this._staticLog.numIdle + this._staticLog.numIdleLeft;
		}

		var tmplstr='<span id="#{id}" class="codeLibScheduleTemplateWrap codeLibBaseCursorPointer" style = "top : #{numTop}px; left : #{numLeft}px; width : #{numWidth}px;" title = "#{strTitle} #{strTime}" >';
			tmplstr += '<span class="codeLibScheduleTemplateTop #{strClassBg}" style = "width : #{numWidthTop}px;"></span>';
			tmplstr += '<span class="codeLibScheduleTemplateMiddle #{strClassBg}" style = " width : #{numWidthMiddle}px;">';
				if (obj.flagLeftUse) {
					if (obj.flagResizeUse) {
						if (obj.flagResizeLeftUse) {
							tmplstr += '<span class="codeLibScheduleTemplateMiddleLeft codeLibScheduleTemplateMiddleResize codeLibBaseCursorE-resize" style = "margin-left : 2px;"></span>';
						}
						else {
							tmplstr += '<span class="codeLibScheduleTemplateMiddleLeft codeLibScheduleTemplateMiddleResize" style = "margin-left : 2px;"></span>';
						}
					}
					else {
						tmplstr += '<span class="codeLibScheduleTemplateMiddleLeft" style = "margin-left : 2px;"></span>';
					}
				}
				else {
					if (obj.flagResizeUse) {
						if (obj.flagResizeLeftUse) {
							tmplstr += '<span class="codeLibScheduleTemplateMiddleResize codeLibBaseCursorE-resize" style = "width : #{numWidthSide}px; height : #{numHeight}px;"></span>';
						}
						else {
							tmplstr += '<span class="codeLibScheduleTemplateMiddleResize" style = "width : #{numWidthSide}px; height : #{numHeight}px;"></span>';
						}
					}
				}
				if (obj.strClass) {
					if (obj.flagMoveUse) {
						if (!obj.flagLeftUse && obj.flagResizeUse) {
							if (obj.strClassLoad) { tmplstr += '<span class="codeLibScheduleTemplateMiddleIcon codeLibScheduleTemplateMiddleMove codeLibBaseCursorMove #{strClassLoad}"></span>'; }
							else { tmplstr += '<span class="codeLibScheduleTemplateMiddleIcon codeLibScheduleTemplateMiddleMove codeLibBaseCursorMove #{strClass}"></span>'; }
						}
						else {
							if (obj.strClassLoad) { tmplstr += '<span class="codeLibScheduleTemplateMiddleIcon codeLibBaseMarginLeftFive codeLibScheduleTemplateMiddleMove codeLibBaseCursorMove #{strClassLoad}"></span>'; }
							else { tmplstr += '<span class="codeLibScheduleTemplateMiddleIcon codeLibBaseMarginLeftFive codeLibScheduleTemplateMiddleMove codeLibBaseCursorMove #{strClass}"></span>'; }
						}
					}
					else {
						if (!obj.flagLeftUse && obj.flagResizeUse) {
							if (obj.strClassLoad) { tmplstr += '<span class="codeLibScheduleTemplateMiddleIcon #{strClassLoad}"></span>'; }
							else { tmplstr += '<span class="codeLibScheduleTemplateMiddleIcon #{strClass}"></span>'; }
						}
						else {
							if (obj.strClassLoad) { tmplstr += '<span class="codeLibScheduleTemplateMiddleIcon codeLibBaseMarginLeftFive #{strClassLoad}"></span>'; }
							else { tmplstr += '<span class="codeLibScheduleTemplateMiddleIcon codeLibBaseMarginLeftFive #{strClass}"></span>'; }
						}
					}
				}
				if (obj.flagBoldNow) {
					if (!obj.flagLeftUse && obj.flagResizeUse && !obj.strClass) { tmplstr += '<span class="codeLibScheduleTemplateMiddleTitle" style = "width : #{numWidthTitle}px;"><span class="codeLibScheduleTemplateMiddleStrTitle codeLibBaseFontBold #{strClassFont}">#{strTitle}</span></span>'; }
					else { tmplstr += '<span class="codeLibScheduleTemplateMiddleTitle codeLibBaseMarginLeftFive" style = "width : #{numWidthTitle}px;"><span class="codeLibScheduleTemplateMiddleStrTitle codeLibBaseFontBold #{strClassFont}">#{strTitle}</span></span>'; }
				}
				else {
					if (!obj.flagLeftUse && obj.flagResizeUse && !obj.strClass) { tmplstr += '<span class="codeLibScheduleTemplateMiddleTitle codeLibBaseCursorPointer" style = "width : #{numWidthTitle}px;"><span class="codeLibScheduleTemplateMiddleStrTitle #{strClassFont}">#{strTitle}</span></span>'; }
					else { tmplstr += '<span class="codeLibScheduleTemplateMiddleTitle codeLibBaseMarginLeftFive codeLibBaseCursorPointer" style = "width : #{numWidthTitle}px;"><span class="codeLibScheduleTemplateMiddleStrTitle #{strClassFont}">#{strTitle}</span></span>'; }
				}
				if (obj.flagRightUse) {
					if (obj.flagResizeUse) {
						if (obj.flagResizeRightUse) {
							tmplstr += '<span class="codeLibScheduleTemplateMiddleRight codeLibScheduleTemplateMiddleResize codeLibBaseCursorE-resize" style = "margin-left : 5px;"></span>';
						}
						else {
							tmplstr += '<span class="codeLibScheduleTemplateMiddleRight codeLibScheduleTemplateMiddleResize" style = "margin-left : 5px;"></span>';
						}
					}
					else {
						tmplstr += '<span class="codeLibScheduleTemplateMiddleRight" style = "margin-left : 5px;"></span>';
					}
				}
				else {
					if (obj.flagResizeUse) {
						if (obj.flagResizeRightUse) {
							tmplstr += '<span class="codeLibScheduleTemplateMiddleResize codeLibBaseCursorE-resize" style = "width : #{numWidthSide}px; height : #{numHeight}px;"></span>';
						}
						else {
							tmplstr += '<span class="codeLibScheduleTemplateMiddleResize" style = "width : #{numWidthSide}px; height : #{numHeight}px;"></span>';
						}
					}
				}
			tmplstr += '</span>';
			tmplstr += '<span class="codeLibScheduleTemplateBottom #{strClassBg}" style = "width : #{numWidthTop}px;"></span>';
		tmplstr += '</span>';
		var data = tmplstr.interpolate(obj);

		return data;
	},

	/**
	 * BtnBottom
	*/
	iniBtnBottom : function()
	{
		this._extBtnBottom();
	},

	/**
	 * Page
	*/
	iniPage : function()
	{
		this._extPage();
	},

	/**
	 * Child
	*/
	_iniChild : function()
	{
		this._setChild();
	},

	/**
	 *
	*/
	insChild : null,
	_setChild : function()
	{
		var cut = this.vars.varsStatus.flagNow;
		var ele = this.insFormat.eleTemplate.header.down('.codeLibTemplateListFormatHeaderGraduationWrap', 0);
		var vars = {
			eleInsertBtnLeft    : this.eleInsertBtnLeft,
			eleInsertBtnRight   : this.eleInsertBtnRight,
			eleInsert           : this.insFormat.eleTemplate.body,
			eleInsertGraduation : ele,
			insRoot             : this.insRoot,
			insCurrent          : this.insSelf,
			idSelf              : this.idSelf + cut,
			vars                : this.vars[cut]
		};
		if (cut == 'week') {
			this.insChild = new Code_Lib_ScheduleWeek(vars);
		} else {
			this.insChild = new Code_Lib_ScheduleMonth(vars);
		}

	},

	/**
	 *
	*/
	insNavi : null,
	_iniNavi : function()
	{
		this._setNaviWrap();
		this._setNaviPrevSingle();
		this._setNaviNextSingle();
		this._setNaviSwitch();
		this._setNaviPrevDouble();
		this._setNaviNextDouble();
		this._setNaviMain();
		this._setNaviWrapWidth();
	},

	/**
	 *
	*/
	eleWrapNavi : null,
	_setNaviWrap : function()
	{
		var ele = $(document.createElement('div'));
		ele.addClassName('codeLibScheduleNaviWrap');
		ele.unselectable = 'on';
		ele.addClassName('unselect');
		this.insFormat.eleTemplate.header.down('.codeLibTemplateListFormatHeaderNaviWrap', 0).insert(ele);
		this.eleWrapNavi = ele;
		this.eleWrapNavi.style.height = this._getNaviHeight() + 'px';
	},

	/**
	 *
	*/
	_setNaviWrapWidth : function()
	{
		this.eleWrapNavi.style.width = this._getNaviWidth() + 'px';
	},

	/**
	 *
	*/
	_getNaviWidth : function()
	{
		var width = 0;
		width += $(this.idSelf + 'NaviSwitch').offsetWidth + this._staticNavi.numMargin;
		width += $(this.idSelf + 'NaviMain').offsetWidth + this._staticNavi.numMargin;
		if (this.varsVar.navi.flagSinglePrev) {
			width += $(this.idSelf + 'NaviPrevSingle').offsetWidth + this._staticNavi.numMargin;
		}
		if (this.varsVar.navi.flagDoublePrev) {
			width += $(this.idSelf + 'NaviPrevDouble').offsetWidth + this._staticNavi.numMargin;
		}
		if (this.varsVar.navi.flagSingleNext) {
			width += $(this.idSelf + 'NaviNextSingle').offsetWidth + this._staticNavi.numMargin;
		}
		if (this.varsVar.navi.flagDoubleNext) {
			width += $(this.idSelf + 'NaviNextDouble').offsetWidth + this._staticNavi.numMargin;
		}

		return width;
	},

	/**
	 *
	*/
	_getNaviHeight : function()
	{
		var ele = this.insFormat.eleTemplate.header.down('.codeLibTemplateListFormatHeaderNaviWrap', 0);
		var array = ele.style.height.split('px');
		var data = parseFloat(array[0]);

		return data;
	},

	/**
	 *
	*/
	_setNaviSwitch : function()
	{
		var strTitle = '';
		var cut = this.vars.varsStatus.flagNow;
		var omit = this.varsLoad.varsWhole.str;
		if (cut == 'month') {
			strTitle = omit.week + omit.unit;
			var insBtn = new Code_Lib_Btn();
			insBtn.iniBtn({
				eleInsert  : this.eleWrapNavi,
				id         : this.idSelf + 'NaviSwitch',
				strFunc    : '_mousedownNaviSwitch',
				strTitle   : strTitle,
				numWidth   : this._staticNavi.numMargin*2 + this._staticNavi.numImg,
				strClass   : 'codeLibScheduleImgSingleBottomWhite',
				insCurrent : this.insSelf,
				vars   : ''
			});
			this._setListener({ins : insBtn});
			$(this.idSelf + 'NaviSwitch').down('.codeLibBtnMiddle', 0).setStyle({
				paddingTop    : this._staticNavi.numPadding + 'px',
				paddingBottom : this._staticNavi.numPadding + 'px'
			});
		} else {
			strTitle = omit.month + omit.unit;
			var insBtn = new Code_Lib_Btn();
			insBtn.iniBtn({
				eleInsert  : this.eleWrapNavi,
				id         : this.idSelf + 'NaviSwitch',
				strFunc    : '_mousedownNaviSwitch',
				strTitle   : strTitle,
				numWidth   : this._staticNavi.numMargin*2 + this._staticNavi.numImg,
				strClass   : 'codeLibScheduleImgSingleTopWhite',
				insCurrent : this.insSelf,
				vars   : ''
			});
			this._setListener({ins : insBtn});
			$(this.idSelf + 'NaviSwitch').down('.codeLibBtnMiddle', 0).setStyle({
				paddingTop    : this._staticNavi.numPadding + 'px',
				paddingBottom : this._staticNavi.numPadding + 'px'
			});
		}
		$(this.idSelf + 'NaviSwitch').setStyle({
			marginLeft : this._staticNavi.numMargin + 'px',
		});
	},

	/**
	 *
	*/
	_mousedownNaviSwitch : function()
	{
		if (this.vars.varsStatus.flagNow == 'month') this.vars.varsStatus.flagNow = 'week';
		else this.vars.varsStatus.flagNow = 'month';
		this.setCake();
		this.iniReload();
	},

	/**
	 *
	*/
	_setNaviPrevDouble : function()
	{
		var strTitle = '';
		var cut = this.vars.varsStatus.flagNow;
		var omit = this.varsLoad.varsWhole.str;
		if (this.varsVar.navi.flagDoublePrev) {
			if (cut == 'month') strTitle = omit.prevYear;
			else if (cut == 'week') strTitle = omit.prevMonth;
			var insBtn = new Code_Lib_Btn();
			insBtn.iniBtn({
				eleInsert  : this.eleWrapNavi,
				id         : this.idSelf + 'NaviPrevDouble',
				strFunc    : '_mousedownNaviPrevDouble',
				strTitle   : strTitle,
				numWidth   : this._staticNavi.numMargin*2 + this._staticNavi.numImg,
				strClass   : 'codeLibScheduleImgDoubleLeftWhite',
				insCurrent : this.insSelf,
				vars   : ''
			});
			this._setListener({ins : insBtn});
			$(this.idSelf + 'NaviPrevDouble').down('.codeLibBtnMiddle', 0).setStyle({
				paddingTop    : this._staticNavi.numPadding + 'px',
				paddingBottom : this._staticNavi.numPadding + 'px'
			});
		} else {
			var insTemplate = new Code_Lib_Template();
			var ele = insTemplate.get({
				flagType : 'circleBox',
				id       : this.idSelf + 'NaviPrevDouble',
				bgColor  : this._staticNavi.bgColor,
				strTitle : strTitle,
				numWidth : this._staticNavi.numMargin*2 + this._staticNavi.numImg,
				strClass : 'codeLibScheduleImgDoubleLeftWhite'
			});
			this.eleWrapNavi.insert(ele);
			$(this.idSelf + 'NaviPrevDouble').down('.codeLibTemplateCircleBoxMiddle', 0).setStyle({
				paddingTop    : this._staticNavi.numPadding + 'px',
				paddingBottom : this._staticNavi.numPadding + 'px'
			});
		}
		$(this.idSelf + 'NaviPrevDouble').setStyle({
			marginLeft : this._staticNavi.numMargin + 'px',
		});
	},

	/**
	 *
	*/
	_staticNavi : {numPadding : 1, numMargin : 5, numImg : 16, bgColor : '#eee'},
	_mousedownNaviPrevDouble : function()
	{
		var cut = this.vars.varsStatus.flagNow;
		if (cut == 'month') {
			var numYear = this._varsVarTime.main.numYear;
			var numMonth = this._varsVarTime.main.numMonth;
			numYear--;
			var objTime = this.insTimeZone.adjustTime({
				stamp : new Date(numYear, numMonth, 1).getTime()
			});
			this.vars.varsStatus.stampMain = objTime.stamp;

		} else if (cut == 'week') {
			var numYear = this._varsVarTime.main.numYear;
			var numMonth = this._varsVarTime.main.numMonth;
			numMonth--;
			if (numMonth == -1) {
				numYear--;
				numMonth = 11;
			}
			var objTime = this.insTimeZone.adjustTime({
				stamp : new Date(numYear, numMonth, 1).getTime()
			});
			this.vars.varsStatus.stampMain = objTime.stamp;
		}
		this.setCake();
		this.iniReload();
	},

	/**
	 *
	*/
	_setNaviPrevSingle : function()
	{
		var strTitle = '';
		var cut = this.vars.varsStatus.flagNow;
		var omit = this.varsLoad.varsWhole.str;
		if (this.varsVar.navi.flagSinglePrev) {
			if (cut == 'month') strTitle = omit.prevMonth;
			else if (cut == 'week') strTitle = omit.prevWeek;
			var insBtn = new Code_Lib_Btn();
			insBtn.iniBtn({
				eleInsert  : this.eleWrapNavi,
				id         : this.idSelf + 'NaviPrevSingle',
				strFunc    : '_mousedownNaviPrevSingle',
				strTitle   : strTitle,
				numWidth   : this._staticNavi.numMargin * 2 + this._staticNavi.numImg * 2,
				strClass   : 'codeLibScheduleImgSingleLeftWhite',
				insCurrent : this.insSelf,
				vars   : ''
			});
			this._setListener({ins : insBtn});
			$(this.idSelf + 'NaviPrevSingle').down('.codeLibBtnMiddle', 0).setStyle({
				paddingTop    : this._staticNavi.numPadding + 'px',
				paddingBottom : this._staticNavi.numPadding + 'px'
			});

		} else {
			var insTemplate = new Code_Lib_Template();
			var ele = insTemplate.get({
				flagType : 'circleBox',
				id       : this.idSelf + 'NaviPrevSingle',
				bgColor  : this._staticNavi.bgColor,
				strTitle : strTitle,
				numWidth : this._staticNavi.numMargin * 2 + this._staticNavi.numImg,
				strClass : 'codeLibScheduleImgDoubleLeftWhite'
			});
			this.eleWrapNavi.insert(ele);
			$(this.idSelf + 'NaviPrevSingle').down('.codeLibTemplateCircleBoxMiddle', 0).setStyle({
				paddingTop    : this._staticNavi.numPadding + 'px',
				paddingBottom : this._staticNavi.numPadding + 'px'
			});
		}
		$(this.idSelf + 'NaviPrevSingle').setStyle({
			marginLeft : this._staticNavi.numMargin + 'px',
		});
	},

	/**
	 *
	*/
	_mousedownNaviPrevSingle : function()
	{
		var cut = this.vars.varsStatus.flagNow;
		if (cut == 'month') {
			var numYear = this._varsVarTime.main.numYear;
			var numMonth = this._varsVarTime.main.numMonth;
			numMonth--;
			if (numMonth == -1) {
				numYear--;
				numMonth = 11;
			}
			var objTime = this.insTimeZone.adjustTime({
				stamp : new Date(numYear, numMonth, 1).getTime()
			});
			this.vars.varsStatus.stampMain = objTime.stamp;

		} else if (cut == 'week') {
			this.vars.varsStatus.stampMain = this._varsVarTime.main.stamp - 7*86400*1000;
		}
		this.setCake();
		this.iniReload();
	},

	/**
	 *
	*/
	_setNaviNextSingle : function()
	{
		var strTitle = '';
		var cut = this.vars.varsStatus.flagNow;
		var omit = this.varsLoad.varsWhole.str;
		if (this.varsVar.navi.flagSingleNext) {
			if (cut == 'month') strTitle = omit.nextMonth;
			else if (cut == 'week') strTitle = omit.nextWeek;
			var insBtn = new Code_Lib_Btn();
			insBtn.iniBtn({
				eleInsert  : this.eleWrapNavi,
				id         : this.idSelf + 'NaviNextSingle',
				strFunc    : '_mousedownNaviNextSingle',
				strTitle   : strTitle,
				numWidth   : this._staticNavi.numMargin*2 + this._staticNavi.numImg*2,
				strClass   : 'codeLibScheduleImgSingleRightWhite',
				insCurrent : this.insSelf,
				vars   : ''
			});
			this._setListener({ins : insBtn});
			$(this.idSelf + 'NaviNextSingle').down('.codeLibBtnMiddle', 0).setStyle({
				paddingTop    : this._staticNavi.numPadding + 'px',
				paddingBottom : this._staticNavi.numPadding + 'px'
			});

		} else {
			var insTemplate = new Code_Lib_Template();
			var ele = insTemplate.get({
				flagType : 'circleBox',
				id       : this.idSelf + 'NaviNextSingle',
				bgColor  : this._staticNavi.bgColor,
				strTitle : strTitle,
				numWidth : this._staticNavi.numMargin*2 + this._staticNavi.numImg,
				strClass : 'codeLibScheduleImgSingleRightWhite'
			});
			this.eleWrapNavi.insert(ele);
			$(this.idSelf + 'NaviNextSingle').down('.codeLibTemplateCircleBoxMiddle', 0).setStyle({
				paddingTop    : this._staticNavi.numPadding + 'px',
				paddingBottom : this._staticNavi.numPadding + 'px'
			});
		}

		$(this.idSelf + 'NaviNextSingle').setStyle({
			marginLeft : this._staticNavi.numMargin + 'px',
		});
	},

	/**
	 *
	*/
	_mousedownNaviNextSingle : function()
	{
		var cut = this.vars.varsStatus.flagNow;
		if (cut == 'month') {
			var numYear = this._varsVarTime.main.numYear;
			var numMonth = this._varsVarTime.main.numMonth;
			numMonth++;
			if (numMonth == 12) {
				numYear++;
				numMonth = 0;
			}
			var objTime = this.insTimeZone.adjustTime({
				stamp : new Date(numYear, numMonth, 1).getTime()
			});
			this.vars.varsStatus.stampMain = objTime.stamp;

		} else if (cut == 'week') {
			this.vars.varsStatus.stampMain = this._varsVarTime.main.stamp + 7*86400*1000;
		}

		this.setCake();
		this.iniReload();
	},

	/**
	 *
	*/
	_setNaviNextDouble : function()
	{
		var strTitle = '';
		var cut = this.vars.varsStatus.flagNow;
		var omit = this.varsLoad.varsWhole.str;
		if (this.varsVar.navi.flagDoubleNext) {
			if (cut == 'month') strTitle = omit.nextYear;
			else if (cut == 'week') strTitle = omit.nextMonth;
			var insBtn = new Code_Lib_Btn();
			insBtn.iniBtn({
				eleInsert  : this.eleWrapNavi,
				id         : this.idSelf + 'NaviNextDouble',
				strFunc    : '_mousedownNaviNextDouble',
				strTitle   : strTitle,
				numWidth   : this._staticNavi.numMargin*2 + this._staticNavi.numImg,
				strClass   : 'codeLibScheduleImgDoubleRightWhite',
				insCurrent : this.insSelf,
				vars   : ''
			});
			this._setListener({ins : insBtn});
			$(this.idSelf + 'NaviNextDouble').down('.codeLibBtnMiddle', 0).setStyle({
				paddingTop    : this._staticNavi.numPadding + 'px',
				paddingBottom : this._staticNavi.numPadding + 'px'
			});

		} else {
			var insTemplate = new Code_Lib_Template();
			var ele = insTemplate.get({
				flagType : 'circleBox',
				id       : this.idSelf + 'NaviNextDouble',
				bgColor  : this._staticNavi.bgColor,
				strTitle : strTitle,
				numWidth : this._staticNavi.numMargin*2 + this._staticNavi.numImg,
				strClass : 'codeLibScheduleImgDoubleRightWhite',
			});
			this.eleWrapNavi.insert(ele);
			$(this.idSelf + 'NaviNextDouble').down('.codeLibTemplateCircleBoxMiddle', 0).setStyle({
				paddingTop    : this._staticNavi.numPadding + 'px',
				paddingBottom : this._staticNavi.numPadding + 'px'
			});
		}

		$(this.idSelf + 'NaviNextDouble').setStyle({
			marginLeft : this._staticNavi.numMargin + 'px',
		});
	},

	/**
	 *
	*/
	_mousedownNaviNextDouble : function()
	{
		var cut = this.vars.varsStatus.flagNow;
		if (cut == 'month') {
			var numYear = this._varsVarTime.main.numYear;
			var numMonth = this._varsVarTime.main.numMonth;
			numYear++;
			var objTime = this.insTimeZone.adjustTime({
				stamp : new Date(numYear, numMonth, 1).getTime()
			});

			this.vars.varsStatus.stampMain = objTime.stamp;
		} else if (cut == 'week') {
			var numYear = this._varsVarTime.main.numYear;
			var numMonth = this._varsVarTime.main.numMonth;
			numMonth++;
			if (numMonth == 12) {
				numYear++;
				numMonth = 0;
			}
			var objTime = this.insTimeZone.adjustTime({
				stamp : new Date(numYear, numMonth, 1).getTime()
			});

			this.vars.varsStatus.stampMain = objTime.stamp;
		}
		this.setCake();
		this.iniReload();
	},

	/**
	 *
	*/
	_setNaviMain : function()
	{
		var insBtn = new Code_Lib_Btn();
		insBtn.iniBtn({
			eleInsert  : this.eleWrapNavi,
			id         : this.idSelf + 'NaviMain',
			strFunc    : '_mousedownNaviMain',
			strTitle   : this.varsVar.navi.strMain,
			insCurrent : this.insSelf,
			vars   : ''
		});
		this._setListener({ins : insBtn});
		$(this.idSelf + 'NaviMain').setStyle({
			marginLeft : this._staticNavi.numMargin + 'px',
		});
	},

	/**
	 *
	*/
	_mousedownNaviMain : function()
	{
		this.vars.varsStatus.stampMain = this._varsVarTime.now.stamp;
		this.setCake();
		this.iniReload();
	},

	/**
	 *
	*/
	_setNaviMainListener : function(obj)
	{
		this.insListener.set({bindAsEvent : 1, insCurrent : this, event : 'mousedown',
			strFunc : '_mousedownNaviMain', ele : obj.ele, vars : ''
		});
		this.insListener.set({bindAsEvent : 0, insCurrent : this, event : 'mouseover',
			strFunc : '_mouseoverNaviMain', ele : obj.ele, vars : { vars : obj }
		});
		this.insListener.set({bindAsEvent : 0, insCurrent : this, event : 'mouseout',
			strFunc : '_mouseoutNaviMain', ele : obj.ele, vars : { vars : obj }
		});
	},

	/**
	 *
	*/
	_mouseoverNaviMain : function(obj)
	{
		$(obj.vars.ele).addClassName('codeLibScheduleBoxMainOver');
	},

	/**
	 *
	*/
	_mouseoutNaviMain : function(obj)
	{
		$(obj.vars.ele).removeClassName('codeLibScheduleBoxMainOver');
	}
});


<?php }
}
?>