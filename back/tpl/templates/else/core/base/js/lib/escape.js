{literal}
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Escape = Class.create({
{/literal}
	varsLoad : {$varsLoad},
{literal}

	/**
	 * obj = {
	 * 	flagType: string,
	 * 	data: mix,
	 * }
	*/
	get : function(obj)
	{
		return this.set({
			arr  : this.varsLoad[obj.flagType],
			data : obj.data
		});
	},

	/**
	 *
	*/
	set : function(obj) {
		var data = obj.data;
		if(data == undefined || data == '') return '';
		if (typeof(data) != 'string') return data;
		for (var i = 0; i < obj.arr.length; i++) {
			data = data.replace(RegExp(obj.arr[i].before, "g"), obj.arr[i].after);
		}

		return data;
	},

	/**
	 * obj = {
	 * 	arr: array,
	 * }
	*/
	toCommnaArr : function(obj)
	{
		if (!obj.arr.length) return '';
		var str = ',' + obj.arr.join(',') + ',';

		return str;
	},

	/**
	 * obj = {
	 * 	data : string,
	 * 	insTimeZone : ins,
	 * }
	*/
	toStampFromTerm : function(obj)
	{
		var array = obj.data.split('/');

		var objTime = obj.insTimeZone.adjustTime({
			stamp : new Date(array[0], parseFloat(array[1]) - 1 , array[2]).getTime()
		});

		return objTime.stampServer;
	},

	/**
	 * obj = {
	 * 	data: string,
	 * }
	*/
	strLowCapitalize : function(obj)
	{
		if (obj.data == '') return;
		var str = obj.data;
		var numStr = str.length;
		var strTop = str.slice(0, 1);
		var strbottom = str.slice(1, numStr);
		str = strTop.toLowerCase() + strbottom;

		return str;
	},

	/**
	 * obj = {
	 * 	data: string,
	 * }
	*/
	strCapitalize : function(obj)
	{
		if (obj.data == '') return;
		var str = obj.data;
		var numStr = str.length;
		var strTop = str.slice(0, 1);
		var strbottom = str.slice(1, numStr);
		str = strTop.capitalize() + strbottom;

		return str;
	},

	/**
	 * obj = {
	 * 	str: string,
	 * }
	*/
	fromCommnaArr : function(obj)
	{
		if (obj.str == '') return [];
		obj.arr = obj.str.split(',');
		var objData = {};
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i] == '' || obj.arr[i] == null) continue;
			var str = 'id' + obj.arr[i];
			objData[str] = obj.arr[i];
		}
		var hash = $H(objData);
		var arrayNew = [];
		var num = 0;
		hash.each( function(pair){
			arrayNew[num] = pair.value;
			num++;
		} );

		return arrayNew;
	}

});
{/literal}