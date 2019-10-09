{literal}
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Color=Class.create({

	/**
	 * obj = {
	 * 	r : int,
	 * 	g : int,
	 * 	b : int,
	 * }
	*/
	rgbToHex : function(obj)
	{
		var r = parseFloat(obj.r,10);
		var g = parseFloat(obj.g,10);
		var b = parseFloat(obj.b,10);
		if(isNaN(r)) r=0;
		if(isNaN(g)) g=0;
		if(isNaN(b)) b=0;
		if( r>=256 || g>=256 || b>=256) return -1;
		var red = r.toString(16);
		var green = g.toString(16);
		var blue = b.toString(16);
		if(red.length == 1) red = '0' + red;
		if(green.length == 1) green = '0' + green;
		if(blue.length == 1) blue = '0' + blue;

		return '#'+ red + green + blue;
	}
});
{/literal}
