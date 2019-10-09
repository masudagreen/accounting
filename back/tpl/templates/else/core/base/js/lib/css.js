{literal}
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Css = Class.create({

	/**
	 * obj = {
	 * 	path : string,
	 * }
	*/
	initialize : function(obj)
	{
		var head;
		$$('head').each(function(ele)
		{
			head = ele;
		});
		var link = document.createElement('link');
		link.href = obj.path;
		link.rel = 'stylesheet';
		link.type = 'text/css';

		var array = $$('link');
		for(var i = 0; i < array.length; i++) {
			if(array[i].href == link.href) return;
		}
		head.insert(link);
	}

});
{/literal}