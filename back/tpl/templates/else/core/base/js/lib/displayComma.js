{literal}
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_DisplayComma = Class.create({

	/**
	 * obj = {
	 * 	num : int,
	 * }
	*/
	get : function(obj)
	{
		var num = obj.num;
		if(num < 1000 && num > -1000) return num;

		var str = "";
		if (num <= -1000) {
			num *= -1;
			while (num != (str = new String(num).replace(/^(\d+)(\d\d\d)/,"$1,$2"))) {
				num = str;
			}
			num = '-' + num;

		} else {
			while (num != (str = new String(num).replace(/^(\d+)(\d\d\d)/,"$1,$2"))) {
				num = str;
			}
		}

		return num;
	}
});
{/literal}