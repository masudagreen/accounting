<?php /* Smarty version 3.1.24, created on 2019-06-16 09:02:07
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/checkTime.js" */ ?>
<?php
/*%%SmartyHeaderCode:14905651155d06058fda26f3_96961011%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7b69b8d8eb2dcd2bd441f155faa6437b961819cd' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/checkTime.js',
      1 => 1560675139,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14905651155d06058fda26f3_96961011',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d06058fda77d5_79986589',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d06058fda77d5_79986589')) {
function content_5d06058fda77d5_79986589 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '14905651155d06058fda26f3_96961011';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_CheckTime = Class.create({

	/**
	 * obj = {
	 * 	stamp          : stamp,
	 * 	stampWrapStart : stamp,
	 * 	stampWrapEnd   : stamp,
	 * }
	*/
	getStamp : function(obj)
	{
		if (obj.stampWrapStart <= obj.stamp && obj.stampWrapEnd > obj.stamp) return 'all';

		return 0;
	},

	/**
	 * obj = {
	 * 	stamp          : stamp,
	 * 	stampWrapStart : stamp,
	 * 	stampWrapEnd   : stamp,
	 * }
	*/
	getStampPosition : function(obj)
	{
		if (obj.stampWrapStart <= obj.stamp && obj.stampWrapEnd >= obj.stamp) return 'middle';
		else if (obj.stampWrapEnd <= obj.stamp) return 'right';
		else if (obj.stampWrapStart >= obj.stamp) return 'left';
	},

	/**
	 * obj = {
	 * 	stampStart     : stamp,
	 * 	stampEnd       : stamp,
	 * 	stampWrapStart : stamp,
	 * 	stampWrapEnd   : stamp,
	 * }
	*/
	getTerm : function(obj)
	{
		var flag;
		if (obj.stampWrapStart <= obj.stampStart && obj.stampStart < obj.stampWrapEnd
			&& obj.stampWrapStart <= obj.stampEnd && obj.stampEnd < obj.stampWrapEnd
		) {
			flag = 'all';

		} else if (obj.stampWrapStart <= obj.stampStart && obj.stampStart < obj.stampWrapEnd
			&& obj.stampWrapEnd <= obj.stampEnd
		) {

			flag = 'right';

		} else if (obj.stampStart < obj.stampWrapStart && obj.stampWrapStart <= obj.stampEnd
			&& obj.stampEnd < obj.stampWrapEnd
		) {
			flag = 'left';

		} else if (obj.stampStart < obj.stampWrapStart && obj.stampStart < obj.stampWrapEnd
			&& obj.stampEnd > obj.stampWrapStart && obj.stampEnd > obj.stampWrapEnd
		) {
			flag = 'middle';

		} else if (obj.stampWrapStart <= obj.stampStart && obj.stampStart <= obj.stampWrapEnd
			&& obj.stampWrapStart <= obj.stampEnd && obj.stampEnd < obj.stampWrapEnd
		) {
			flag = 'all';
		}

		return flag;
	},

	/**
	 * obj = {
	 * 	objStartWrap     : object,
	 * 	objEndWrap       : object,
	 * 	flagType : string,//date,week,month,year
	 * 	stampLimit   : stamp,
	 * 	arrayMonth : [int,int],//1,2,5
	 * 	arrayWeek : [
	 * 		{
	 * 			flagNum : mix,//0,1,2,3,4,5,'last'
	 * 			numDay : int //0,1,2,3,4,5,6
	 * 		},
	 * 	],
	 * 	arrayDate : [int,int],//1,2,5
	 * 	arrayDay : [int,int],//1,2,5
	 * 	start : { numHour : int, numMin : int },
	 * 	end : { numHour : int, numMin : int },
	 * 	insTimeZone : instance
	 * }
	*/
	getLoop : function(obj)
	{
		if (obj.stampLimit && obj.objStartWrap.stamp > obj.stampLimit) {
			if (obj.objEndWrap.stamp > obj.stampLimit) return 0;
		}
		if (obj.flagType == 'date') {
			return this.getLoopDate(obj);
		} else if (obj.flagType == 'week') {
			for (var i = 0;  i < obj.arrayDay.length; i++) {
				if (obj.arrayDay[i] == obj.objStartWrap.numDay) return this.checkLoopTerm(obj);
			}
		} else if (obj.flagType == 'month') {
			return this.getLoopMonth(obj);
		} else if (obj.flagType == 'year') {
			for (var i = 0; i < obj.arrayMonth.length; i++) {
				if (obj.arrayMonth[i] == obj.objStartWrap.numMonth) return this.getLoopMonth(obj);
			}
		}

		return 0;
	},

	/**
	 *
	*/
	getLoopDate : function(obj)
	{
		var stampStart = obj.start.numHour * 60 + obj.start.numMin;
		var stampEnd = obj.end.numHour*60 + obj.end.numMin;
		for (var i = 0; i < obj.arrayDate.length; i++) {
			if (obj.arrayDate[i] == 'last') {
				var objTime = obj.insTimeZone.adjustTime({
					stamp : new Date(obj.objStartWrap.numYear, obj.objStartWrap.numMonth ,1 - 1).getTime()
				});

				if (obj.objStartWrap.numDate == objTime.numDate) {
					if (stampStart == 0 && stampEnd == 0) return 'all';
					else return this.checkLoopTerm(obj);
				}
			} else {
				if (obj.arrayDate[i] == obj.objStartWrap.numDate) {
					if (stampStart == 0 && stampEnd == 0) return 'all';
					else return this.checkLoopTerm(obj);
				}
			}
		}

		return 0;
	},

	/**
	 *
	*/
	getLoopMonth : function(obj)
	{
		var stampStart = obj.start.numHour * 60 + obj.start.numMin;
		var stampEnd = obj.end.numHour * 60 + obj.end.numMin;
		for (var i = 0; i <obj.arrayDate.length; i++) {
			if (obj.arrayDate[i] == 'last') {
				var objTime = obj.insTimeZone.adjustTime({
					stamp : new Date(obj.objStartWrap.numYear, obj.objStartWrap.numMonth , 1 - 1).getTime()
				});
				if (obj.objStartWrap.numDate == objTime.numDate) {
					if (stampStart == 0 && stampEnd == 0) return 'all';
					else return this.checkLoopTerm(obj);
				}
			} else {
				if (obj.arrayDate[i] == obj.objStartWrap.numDate) {
					if (stampStart == 0 && stampEnd == 0) return 'all';
					else return this.checkLoopTerm(obj);
				}
			}
		}
		var objDay = this.getLoopNumDay({
			insTimeZone : obj.insTimeZone,
			numYear     : obj.objStartWrap.numYear,
			numMonth    : obj.objStartWrap.numMonth,
			numDate     : obj.objStartWrap.numDate,
			numDay      : obj.objStartWrap.numDay
		});

		for (var i = 0; i < obj.arrayWeek.length; i++) {
			if (obj.arrayWeek[i].flagNum == 'last') {
				if (objDay.flagLast && obj.arrayWeek[i].flagNum == objDay.num) {
					if (obj.arrayWeek[i].numDay == obj.objStartWrap.numDay) return this.checkLoopTerm(obj);
				}
			} else {
				if (obj.arrayWeek[i].flagNum == objDay.num && obj.arrayWeek[i].numDay == obj.objStartWrap.numDay) {
					return this.checkLoopTerm(obj);
				}
			}
		}
	},

	/**
	 *
	*/
	getLoopNumDay : function(obj)
	{
		var objTime = obj.insTimeZone.adjustTime({
			stamp : new Date(obj.numYear, obj.numMonth + 1 , 1 - 1).getTime()
		});

		var mainSpan = objTime.numDate;
		var num = 1, numDay = 0;
		for (var j = 1; j <= mainSpan; j++) {
			var objTime = obj.insTimeZone.adjustTime({
				stamp : new Date(obj.numYear, obj.numMonth ,j).getTime()
			});
			if (j == obj.numDate) numDay = num;
			if (objTime.numDay == obj.numDay) num++;
		}
		var flag = 0;
		if (num == numDay) flag = 1;

		return {
			flagLast : flag,
			num      : numDay
		};
	},

	/**
	 *
	*/
	checkRateConsumptionTax : function(obj)
	{
		var stamp = (obj.stamp)? obj.stamp : new Date().getTime();
		var objTime = obj.insTimeZone.adjustTime({
			stamp : stamp
		});
		var num = 5;
		var stamp20140401 = 1396278000 * 1000;
		var stamp20151001 = 1443625200 * 1000;
		var stamp = objTime.stamp;
		if (stamp20140401 <= stamp && stamp < stamp20151001) {
			num = 8;

		} else if (stamp20151001 <= stamp) {
			num = 10;
		}

		return num;
	},

	/**
	 *
	*/
	checkLoopTerm : function(obj)
	{
		var stampStart = obj.start.numHour * 60 + obj.start.numMin;
		var stampEnd = obj.end.numHour * 60 + obj.end.numMin;
		var stampWrapStart = obj.objStartWrap.numHour * 60 + obj.objStartWrap.numMin;
		var stampWrapEnd = obj.objEndWrap.numHour * 60 + obj.objEndWrap.numMin;
		var num = 60 * 24;
		var flag = this.getTerm({
			stampStart     : stampStart,
			stampEnd       : (stampStart == 0 && stampEnd == 0)? num : stampEnd,
			stampWrapStart : stampWrapStart,
			stampWrapEnd   : (stampWrapStart == 0 && stampWrapEnd == 0)? num : stampWrapEnd
		});

		return flag;
	}
});

<?php }
}
?>