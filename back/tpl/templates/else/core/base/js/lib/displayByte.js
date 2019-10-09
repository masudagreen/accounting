{literal}
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_DisplayByte = Class.create(
{
{/literal}
	varsLoad : {$varsLoad},
{literal}

	/**
	 {
		num      : 0,
		flagFrom : 'b',
		flagTo   : 'mb'
	 }
	*/

	get : function(obj)
	{
		if(obj.flagFrom == 'b') {
			if(obj.flagTo == 'b') return obj.num;
			else if(obj.flagTo == 'kb') return Math.floor(obj.num/1024);
			else if(obj.flagTo == 'mb') return Math.floor(obj.num/1024/1024);
			else if(obj.flagTo == 'gb') return Math.floor(obj.num/1024/1024/1024);
		}
		else if(obj.flagFrom == 'kb') {
			if(obj.flagTo == 'b') return obj.num*1024;
			else if(obj.flagTo == 'kb') return obj.num;
			else if(obj.flagTo == 'mb') return Math.floor(obj.num/1024);
			else if(obj.flagTo == 'gb') return Math.floor(obj.num/1024/1024);
		}
		else if(obj.flagFrom == 'mb') {
			if(obj.flagTo == 'b') return obj.num*1024*1024;
			else if(obj.flagTo == 'kb') return obj.num*1024;
			else if(obj.flagTo == 'mb') return obj.num;
			else if(obj.flagTo == 'gb') return Math.floor(obj.num/1024);
		}
		else if(obj.flagFrom == 'gb') {
			if(obj.flagTo == 'b') return obj.num*1024*1024*1024;
			else if(obj.flagTo == 'kb') return obj.num*1024*1024;
			else if(obj.flagTo == 'mb') return obj.num*1024;
			else if(obj.flagTo == 'gb') return obj.num;
		}
	}
});
{/literal}