<?php /* Smarty version 3.1.24, created on 2016-08-20 07:30:04
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/check.js" */ ?>
<?php
/*%%SmartyHeaderCode:1626710057b806fc653a85_11681404%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a8ba3d6f204b797aa31a199a55a488d091f83bb1' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/check.js',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1626710057b806fc653a85_11681404',
  'variables' => 
  array (
    'varsLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b807053327a9_59759214',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b807053327a9_59759214')) {
function content_57b807053327a9_59759214 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '1626710057b806fc653a85_11681404';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_CheckValue = Class.create({

	vars : <?php echo $_smarty_tpl->tpl_vars['varsLoad']->value;?>
,


	/**
	 * obj = {
	 * 	flagMustUse  : int,
	 * 	flagMayUse   : int,
	 * 	flagErrorNow : int,
	 * 	error        : [],
	 * }
	*/
	checkValue : function(obj)
	{

		for (var i = 0; i < obj.arr.length; i++) {
			obj.arr[i].flagErrorNow = 0;
			for (var k = 0; k < obj.arr[i].arrayError.length; k++) {
				obj.arr[i].arrayError[k].flagNow = 0;
				if (obj.arr[i].arrayError[k].flagCheck == 'blank') {
					if (obj.arr[i].arrayError[k].flagUse && obj.arr[i].flagMustUse) {
						obj.arr[i].arrayError[k].flagNow = this.checkValueBlank({
							flagType  : obj.arr[i].arrayError[k].flagType,
							flagArr : obj.arr[i].arrayError[k].flagArr,
							value     : obj.arr[i].value
						});

						if (obj.arr[i].arrayError[k].flagNow) obj.arr[i].flagErrorNow = 1;
						if (obj.arr[i].arrayError[k].flagNow) continue;
					}
				}
				else {
					if (obj.arr[i].arrayError[k].flagUse) {
						if (obj.arr[i].arrayError[k].flagCheck == 'attest') {
							continue;
						}
						var flag = this.checkValueBlank({
							flagType  : 'empty',
							flagArr   : obj.arr[i].arrayError[k].flagArr,
							value     : obj.arr[i].value
						});
						if (obj.arr[i].arrayError[k].flagCheck == 'word') {
							if (obj.arr[i].flagMustUse || (!obj.arr[i].flagMustUse && !flag)) {
								obj.arr[i].arrayError[k].flagNow = this.checkValueWord({
									flagType  : obj.arr[i].arrayError[k].flagType,
									flagArr : obj.arr[i].arrayError[k].flagArr,
									value     : obj.arr[i].value
								});
							}
							if (obj.arr[i].arrayError[k].flagNow) obj.arr[i].flagErrorNow = 1;
							if (obj.arr[i].arrayError[k].flagNow) continue;
						}
						else if (obj.arr[i].arrayError[k].flagCheck == 'format') {
							if (obj.arr[i].flagMustUse || (!obj.arr[i].flagMustUse && !flag)) {
								obj.arr[i].arrayError[k].flagNow = this.checkValueFormat({
									flagType  : obj.arr[i].arrayError[k].flagType,
									flagArr : obj.arr[i].arrayError[k].flagArr,
									value     : obj.arr[i].value
								});
							}
							if (obj.arr[i].arrayError[k].flagNow) obj.arr[i].flagErrorNow = 1;
							if (obj.arr[i].arrayError[k].flagNow) continue;
						}
						else if (obj.arr[i].arrayError[k].flagCheck == 'max') {
							if (obj.arr[i].flagMustUse || (!obj.arr[i].flagMustUse && !flag)) {
								obj.arr[i].arrayError[k].flagNow = this.checkValueMax({
									flagType  : obj.arr[i].arrayError[k].flagType,
									value     : obj.arr[i].value,
									flagArr : obj.arr[i].arrayError[k].flagArr,
									num       : obj.arr[i].arrayError[k].num
								});
							}
							if (obj.arr[i].arrayError[k].flagNow) obj.arr[i].flagErrorNow = 1;
							if (obj.arr[i].arrayError[k].flagNow) continue;
						}
						else if (obj.arr[i].arrayError[k].flagCheck == 'min') {
							if (obj.arr[i].flagMustUse || (!obj.arr[i].flagMustUse && !flag)) {
								obj.arr[i].arrayError[k].flagNow = this.checkValueMin({
									flagType  : obj.arr[i].arrayError[k].flagType,
									value     : obj.arr[i].value,
									flagArr : obj.arr[i].arrayError[k].flagArr,
									num       : obj.arr[i].arrayError[k].num
								});
							}
							if (obj.arr[i].arrayError[k].flagNow) obj.arr[i].flagErrorNow = 1;
							if (obj.arr[i].arrayError[k].flagNow) continue;
						}
						else if (obj.arr[i].arrayError[k].flagCheck == 'strUnique') {
							if (obj.arr[i].flagMustUse || (!obj.arr[i].flagMustUse && !flag)) {
								obj.arr[i].arrayError[k].flagNow = this.checkValueStrUnique({
									value : obj.arr[i].value
								});
							}
							if (obj.arr[i].arrayError[k].flagNow) obj.arr[i].flagErrorNow = 1;
							if (obj.arr[i].arrayError[k].flagNow) continue;
						}
					}
				}
			}
		}

		return obj.arr;
	},

	/**
	 * obj = {
	 * 	flagType  : string,
	 * 	flagArr   : string,
	 * 	value     : string,
	 * }
	*/
	checkValueBlank : function(obj)
	{
		if (obj.flagArr) {
			var array = [];
			if (obj.flagArr == 'json') array = obj.value.evalJSON();
			else if (obj.flagArr == 'comma') {
				var insEscape = new Code_Lib_Escape();
				array = insEscape.fromCommnaArr({str : obj.value});
			}
			for (var i = 0; i < array.length; i++) {
				var str = '' + array[i];
				if (obj.flagType == 'blank') return (str.blank()) ? 1 : 0;
				else if (obj.flagType == 'empty') return (str == '' || str == undefined) ? 1 : 0;
				else if (obj.flagType == 'http') return (str == 'http://' || str.blank()) ? 1 : 0;
				else if (obj.flagType == 'https') return (str == 'https://' || str.blank()) ? 1 : 0;
			}
		} else {
			if (obj.flagType == 'blank') return (obj.value.blank()) ? 1 : 0;
			else if (obj.flagType == 'empty') return (obj.value == '' || obj.value == undefined) ? 1 : 0;
			else if (obj.flagType == 'http') return (obj.value == 'http://' || obj.value.blank()) ? 1 : 0;
			else if (obj.flagType == 'https') return (obj.value == 'https://' || obj.value.blank()) ? 1 : 0;
		}
	},

	/**
	 * obj = {
		flagType : string,
		flagArr  : string,
		value    : string,
	 * }
	*/
	checkValueWord : function(obj)
	{
		if (obj.flagArr) {
			var array = [];
			if (obj.flagArr == 'json') array = obj.value.evalJSON();
			else if (obj.flagArr == 'comma') {
				var insEscape = new Code_Lib_Escape();
				array = insEscape.fromCommnaArr({str : obj.value});
			}
			for (var i = 0; i < array.length; i++) {
				var str = '' + array[i];
				if (obj.flagType == 'half' && str.match(/[^a-zA-Z0-9_]+/)) return 1;
				else if (obj.flagType == 'halfhyphen' && str.match(/[^a-zA-Z0-9_-]+/)) return 1;
				else if (obj.flagType == 'ip' && str.match(/[^0-9.]+/)) return 1;
				else if (obj.flagType == 'ipSubnet' && str.match(/[^0-9.\/\-]+/)) return 1;
				else if (obj.flagType == 'num' && str.match(/[^0-9]+/)) return 1;
				else if (obj.flagType == 'numminus' && str.match(/[^0-9\-]+/)) return 1;
				else if	 (obj.flagType == 'number') {
					if (str.match(/^(0|-?[1-9][0-9]*|-?(0|[1-9][0-9]*)\.[0-9]+)$/)) return 1;
				}
				else if (obj.flagType == 'url' && str.match(/[^a-zA-Z0-9_\:\/.\-~=%&#?]+/)) return 1;
				else if (obj.flagType == 'mail' && str.match(/[^a-zA-Z0-9_@~.-]+/)) return 1;
				else if (obj.flagType == 'mailHost' && str.match(/[^a-zA-Z0-9_~.-]+/)) return 1;
				else if (obj.flagType == 'file' && str.match(/[^a-zA-Z0-9_.\-]+/)) return 1;
				else if (obj.flagType == 'space' && str.match(/\s/)) return 1;
			}
		} else {
			if (obj.flagType == 'half') return (obj.value.match( /[^a-zA-Z0-9_]+/)) ? 1 : 0;
			else if (obj.flagType == 'halfhyphen') return (obj.value.match( /[^a-zA-Z0-9_-]+/)) ? 1 : 0;
			else if (obj.flagType == 'ip') return (obj.value.match( /[^0-9.]+/)) ? 1 : 0;
			else if (obj.flagType == 'ipSubnet') return (obj.value.match( /[^0-9.\/\-]+/)) ? 1 : 0;
			else if (obj.flagType == 'num') return (obj.value.match( /[^0-9]+/)) ? 1 : 0;
			else if (obj.flagType == 'numminus') return (obj.value.match( /[^0-9\-]+/)) ? 1 : 0;
			else if (obj.flagType == 'number' ) {
				return (obj.value.match( /^(0|-?[1-9][0-9]*|-?(0|[1-9][0-9]*)\.[0-9]+)$/)) ? 0 : 1;
			}
			else if (obj.flagType == 'url') return (obj.value.match( /[^a-zA-Z0-9_\:\/.\-~=%&#?]+/)) ? 1 : 0;
			else if (obj.flagType == 'mail') return (obj.value.match( /[^a-zA-Z0-9_@~.-]+/)) ? 1 : 0;
			else if (obj.flagType == 'mailHost') return (obj.value.match( /[^a-zA-Z0-9_~.-]+/)) ? 1 : 0;
			else if (obj.flagType == 'file') return (obj.value.match( /[^a-zA-Z0-9_.\-]+/)) ? 1 : 0;
			else if (obj.flagType == 'termStamp') return (obj.value.match( /[^0-9\-]+/)) ? 1 : 0;
			else if (obj.flagType == 'termTime') return (obj.value.match( /[^0-9\-\/]+/)) ? 1 : 0;
			else if (obj.flagType == 'space') return (obj.value.match( /\s/)) ? 1 : 0;
			else if (obj.flagType == 'password') return (obj.value.match( /[^a-zA-Z0-9!#$%"'()=~|^@[;:?,.`}{+*-]+/)) ? 1 : 0;
		}
	},

	/**
	 * obj = {
	 * 	flagType    : string,
	 * 	flagArr     : string,
	 * 	value       : string,
	 * 	insTimeZone : instance,
	 * }
	*/
	checkValueFormat : function(obj)
	{
		if (obj.flagArr) {
			var array = [];
			if (obj.flagArr == 'json') array = obj.value.evalJSON();
			else if (obj.flagArr == 'comma') {
				var insEscape = new Code_Lib_Escape();
				array = insEscape.fromCommnaArr({str : obj.value});
			}
			for (var i = 0; i < array.length; i++) {
				var str = '' + array[i];
				if (obj.flagType == 'ip' && !str.match(/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/)) return 1;
				else if (obj.flagType == 'ipSubnet') {
					if (!str.match(/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\/[0-9]{1,2}$/)) return 1;
				}
				else if (obj.flagType == 'post' && !str.match(/^[0-9]{3}\-+[0-9]{4}$/)) return 1;
				else if (obj.flagType == 'born' && !str.match(/^[0-9]{4}\-+[0-9]{2}\-+[0-9]{2}$/)) return 1;
				else if (obj.flagType == 'phone' && !str.match(/^[0-9]{2,4}\-+[0-9]{2,4}\-+[0-9]{2,4}$/)) return 1;
				else if (obj.flagType == 'numminus') {
					var num = str;
					if (num != '') {
						num = parseFloat(str);
						if (num < 0) {
							var arr = str.split('-');
							if (arr.length != 2) {
								return 1;
							}
							num *= -1;
							str = '' + num;
						}
						return (str.match( /[^0-9]+/)) ? 1 : 0;
					}
				}
				else if (obj.flagType == 'mail' && !str.match(/.+@.+\..+/)) return 1;
				else if (obj.flagType == 'mailHost' && !str.match(/.+\..+/)) return 1;
				else if (obj.flagType == 'url' && !str.match(/^(http|https|ftp|telnet|nttp|file|news):\/\/.+/)) {
					return 1;
				}
			}
		} else {
			if (obj.flagType == 'ip') {
				return (!obj.value.match( /^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/)) ? 1 : 0;
			} else if (obj.flagType == 'ipSubnet') {
				if (!obj.value.match( /^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\/[0-9]{1,2}$/)) return 1;
			}
			else if (obj.flagType == 'post') return (!obj.value.match( /^[0-9]{3}\-+[0-9]{4}$/)) ? 1 : 0;
			else if (obj.flagType == 'born') return (!obj.value.match( /^[0-9]{4}\-+[0-9]{2}\-+[0-9]{2}$/)) ? 1 : 0;
			else if (obj.flagType == 'phone') {
				return (!obj.value.match( /^[0-9]{2,4}\-+[0-9]{2,4}\-+[0-9]{2,4}$/)) ? 1 : 0;
			}
			else if (obj.flagType == 'numminus') {
				var str = '' + obj.value;
				var num = str;
				if (num != '') {
					num = parseFloat(str);
					if (num < 0) {
						var arr = str.split('-');
						if (arr.length != 2) {
							return 1;
						}
						num *= -1;
						str = '' + num;
					}
					return (str.match( /[^0-9]+/)) ? 1 : 0;
				}
			}
			else if (obj.flagType == 'mail') return (!obj.value.match( /.+@.+\..+/)) ? 1 : 0;
			else if (obj.flagType == 'mailHost') return (!obj.value.match( /.+\..+/)) ? 1 : 0;
			else if (obj.flagType == 'url') {
				if (!obj.value.match( /^(http|https|ftp|telnet|nttp|file|news):\/\/.+/)) return 1;
			} else if (obj.flagType == 'termStamp' ) {
				var array = obj.value.evalJSON();
				return (array[0] > array[1]) ? 1 : 0;
			} else if (obj.flagType == 'password' ) {
				var flaga = (obj.value.match(/[a-z]+/))? 1: 0;
				var flagA = (obj.value.match(/[A-Z]+/))? 1: 0;
				var flag0 = (obj.value.match(/[0-9]+/))? 1: 0;
				var flagMark = (obj.value.match(/[!"#$%&'()=~|^@[;:\],.\/`{+*}?-]+/))? 1: 0;
				var flag = (flaga && flagA && flag0 && flagMark)? 0: 1;
				return flag;

			} else if (obj.flagType == 'date' || obj.flagType == 'dateNone1970') {
				if (!obj.value.match( /^[0-9]{4}\/[0-9]{1,2}\/[0-9]{1,2}$/)) return 'format';
				var array = obj.value.split('/');
				var year = array[0];
				if (year < 1970 && obj.flagType != 'dateNone1970') return 'year';
				var month = array[1];
				if (month.match( /^0[1-9]{1}$/)) {
					var arrayMonth = month.split('0');
					month = parseFloat(arrayMonth[1]);
				}
				if (month > 12 || month < 1) return 'month';
				var date = array[2];
				if (date.match( /^0[1-9]{1}$/)) {
					var arrayDate = date.split('0');
					date = parseFloat(arrayDate[1]);
				}
				var ins = new Date(year, month ,1-1);
				if (date > ins.getDate() || date < 1) return 'date';

			} else if (obj.flagType == 'date-time' || obj.flagType == 'date-timeNone1970') {
				if (!obj.value.match( /^[0-9]{4}\/[0-9]{1,2}\/[0-9]{1,2}-[0-9]{1,2}:[0-9]{1,2}$/)) return 'format';
				var array = obj.value.split('-');
				var strDate = array[0];
				var strTime = array[1];
				array = strTime.split(':');
				var numHour = array[0];
				if (numHour.match( /^0[1-9]{1}$/)) {
					var arrayHour = numHour.split('0');
					numHour = parseFloat(arrayHour[1]);
				}
				if (numHour > 23 || numHour < 0) return 'hour';

				var numMin = array[1];
				if (numMin.match( /^0[1-9]{1}$/)) {
					var arrayMin = numMin.split('0');
					numMin = parseFloat(arrayMin[1]);
				}
				if (numMin > 59 || numMin < 0) return 'min';

				array = strDate.split('/');
				var year = array[0];
				if (year < 1970 && obj.flagType != 'date-timeNone1970') return 'year';
				var month = array[1];
				if (month.match( /^0[1-9]{1}$/)) {
					var arrayMonth = month.split('0');
					month = parseFloat(arrayMonth[1]);
				}
				if (month > 12 || month < 1) return 'month';
				var date = array[2];
				if (date.match( /^0[1-9]{1}$/)) {
					var arrayDate = date.split('0');
					date = parseFloat(arrayDate[1]);
				}
				var ins = new Date(year, month ,1-1);
				if (date > ins.getDate() || date < 1) return 'date';

			} else if (obj.flagType == 'date-time-date') {
				if (obj.value.match( /^[0-9]{4}\/[0-9]{1,2}\/[0-9]{1,2}$/)) {
					var array = obj.value.split('/');
					var year = array[0];
					if (year < 1970 && obj.flagType != 'dateNone1970') return 'year';
					var month = array[1];
					if (month.match( /^0[1-9]{1}$/)) {
						var arrayMonth = month.split('0');
						month = parseFloat(arrayMonth[1]);
					}
					if (month > 12 || month < 1) return 'month';
					var date = array[2];
					if (date.match( /^0[1-9]{1}$/)) {
						var arrayDate = date.split('0');
						date = parseFloat(arrayDate[1]);
					}
					var ins = new Date(year, month ,1-1);
					if (date > ins.getDate() || date < 1) return 'date';

				} else if (obj.value.match( /^[0-9]{4}\/[0-9]{1,2}\/[0-9]{1,2}-[0-9]{1,2}:[0-9]{1,2}$/)) {
					var array = obj.value.split('-');
					var strDate = array[0];
					var strTime = array[1];
					array = strTime.split(':');
					var numHour = array[0];
					if (numHour.match( /^0[1-9]{1}$/)) {
						var arrayHour = numHour.split('0');
						numHour = parseFloat(arrayHour[1]);
					}
					if (numHour > 23 || numHour < 0) return 'hour';

					var numMin = array[1];
					if (numMin.match( /^0[1-9]{1}$/)) {
						var arrayMin = numMin.split('0');
						numMin = parseFloat(arrayMin[1]);
					}
					if (numMin > 59 || numMin < 0) return 'min';

					array = strDate.split('/');
					var year = array[0];
					if (year < 1970 && obj.flagType != 'date-timeNone1970') return 'year';
					var month = array[1];
					if (month.match( /^0[1-9]{1}$/)) {
						var arrayMonth = month.split('0');
						month = parseFloat(arrayMonth[1]);
					}
					if (month > 12 || month < 1) return 'month';
					var date = array[2];
					if (date.match( /^0[1-9]{1}$/)) {
						var arrayDate = date.split('0');
						date = parseFloat(arrayDate[1]);
					}
					var ins = new Date(year, month ,1-1);
					if (date > ins.getDate() || date < 1) return 'date';

				} else {
					return 'format';
				}

			} else if (obj.flagType == 'termTime' ) {
				var pattern = "/^[0-9]{4}\/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{1,2}\-[0-9]{4}"
							+ "\/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{1,2}$/";
				if (!obj.value.match(pattern)) return 'format';
				var array = obj.value.split('-');
				var arrayStamp = [];
				for (var i = 0; i < array.length; i++) {
					var arrayA = array[i].split('/');
					var year = arrayA[0];
					if (year < 1970) return 'year';
					var month = arrayA[1];
					if (month.match( /^0[1-9]{1}$/)) {
						var arrayMonth = month.split('0');
						month = parseFloat(arrayMonth[1]);
					}
					if (month > 12 || month < 1) return 'month';
					var date = arrayA[2];
					if (date.match( /^0[1-9]{1}$/)) {
						var arrayDate = date.split('0');
						date = parseFloat(arrayDate[1]);
					}

					var objTime = insTimeZone.adjustTime({
						stamp : new Date(year, month ,1-1).getTime()
					});
					if (date > objTime.date || date < 1) return 'date';
					var hour = arrayA[3];
					if (hour == 00 || hour == '00') {
						hour = 0;
					}
					else if (hour.match( /^0[1-9]{1}$/)) {
						var arrayHour = hour.split('0');
						hour = parseFloat(arrayHour[1]);
					}
					if (hour > 23 || hour < 0) return 'hour';
					var min = arrayA[4];
					if (min == 00 || min == '00') {
						min = 0;
					}
					else if (min.match( /^0[1-9]{1}$/)) {
						var arrayMin = min.split('0');
						min = parseFloat(arrayMin[1]);
					}
					if (min > 59 || min < 0) return 'min';

					var objTimeDate = insTimeZone.adjustTime({
						stamp : new Date(year, month-1 ,date).getTime()
					});
					var stamp = objTimeDate.stamp + hour*60*60*1000 + min*60*1000;
					arrayStamp.push(stamp);
				}
				if (arrayStamp[0] > arrayStamp[1]) return 'stamp';
			}
		}
	},

	/**
	 * obj = {
	 * 	flagType : string,
	 * 	flagArr  : string,
	 * 	value    : string,
	 * 	num      : int,
	 * }
	*/
	checkValueMax : function(obj)
	{
		var insEscape = new Code_Lib_Escape();
		if (obj.flagArr) {
			var array = [];
			if (obj.flagArr == 'json') array = obj.value.evalJSON();
			else if (obj.flagArr == 'comma') {
				array = insEscape.fromCommnaArr({str : obj.value});

			}
			for (var i = 0; i < array.length; i++) {
				if (obj.flagType == 'num' && parseFloat(array[i]) > obj.num) return 1;
				else if (obj.flagType == 'str') {
					var str = insEscape.get({
						flagType : 'standard',
						data     : array[i]
					});
					if (str.length > obj.num) {
						return 1;
					}
				}
			}

		} else {
			if (obj.flagType == 'num') return (parseFloat(obj.value) > obj.num) ? 1  : 0;
			else if (obj.flagType == 'str') {
				var str = insEscape.get({
					flagType : 'standard',
					data     : obj.value
				});
				return (str.length > obj.num) ? 1  : 0;
			}

		}
	},

	/**
	 * obj = {
	 * 	flagType : string,
	 * 	flagArr  : string,
	 * 	value    : string,
	 * 	num      : int,
	 * }
	*/
	checkValueMin : function(obj)
	{
		if (obj.flagArr) {
			var array = [];
			if (obj.flagArr == 'json') array = obj.value.evalJSON();
			else if (obj.flagArr == 'comma') {
				var insEscape = new Code_Lib_Escape();
				array = insEscape.fromCommnaArr({str : obj.value});
			}
			for (var i = 0; i < array.length; i++) {
				if (obj.flagType == 'num' && parseFloat(array[i]) < obj.num) return 1;
				else if (obj.flagType == 'str' && array[i].length < obj.num) return 1;
			}
		}
		else {
			if (obj.flagType == 'num') return (parseFloat(obj.value) < obj.num) ? 1  : 0;
			else if (obj.flagType == 'str') return (obj.value.length < obj.num) ? 1  : 0;
		}
	},

	/**
	 * obj = {
	 * 	value : string,
	 * }
	*/
	checkValueStrUnique : function(obj)
	{
		var array = this.vars.strUnique;
		for( var j = 0; j < array.length; j++ ) {
			if ( obj.value.match( new RegExp( array[j] )))  return array[j];
		}

		return 0;
	}
});


<?php }
}
?>