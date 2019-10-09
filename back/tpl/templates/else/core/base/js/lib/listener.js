{literal}
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Listener = Class.create({

	/**
	 *
	*/
	vars : null,
	initialize : function()
	{
		this.vars = [];
	},

	/**
	 * obj = {
	 * 	flagType15  : int
	 * 	bindAsEvent : string
	 * 	ele         : element
	 * 	event       : string
	 * 	insCurrent  : instance
	 * 	vars    : object
	 * 	strFunc     : string
	 * 	bindAsEvent : string
	 * }
	*/
	set : function(obj)
	{
		var wrapper;
		if(!obj.flagType15) obj.flagType15 = 0;
		if(obj.flagType15) {
			wrapper = obj.insCurrent[obj.strFunc].bind(obj.insCurrent, obj.vars);
			Event.observe(obj.ele, obj.event, wrapper);
		} else {
			if(obj.bindAsEvent) {
				if(obj.vars) {
					wrapper = obj.insCurrent[obj.strFunc].bindAsEventListener(obj.insCurrent, obj.vars);
				} else {
					wrapper = obj.insCurrent[obj.strFunc].bindAsEventListener(obj.insCurrent);
				}
				obj.ele.observe(obj.event, wrapper);
			} else {
				if(obj.vars) {
					wrapper = obj.insCurrent[obj.strFunc].bind(obj.insCurrent, obj.vars);
				} else {
					wrapper = obj.insCurrent[obj.strFunc].bind(obj.insCurrent);
				}
				obj.ele.observe(obj.event, wrapper);
			}
		}
		var data = {
			flagType15 : obj.flagType15,
			insCurrent : obj.insCurrent,
			strFunc    : obj.strFunc,
			ele        : obj.ele,
			event      : obj.event,
			wrapper    : wrapper
		};
		this.vars.push(data);
	},

	/**
	 *
	*/
	stop : function()
	{
		this._stopChild({arr : this.vars});
		this.reset();
	},

	/**
	 *
	*/
	_stopChild : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if(obj.arr[i].flagType15) {
				Event.stopObserving(obj.arr[i].ele, obj.arr[i].event, obj.arr[i].wrapper);
			} else {
				obj.arr[i].ele.stopObserving(obj.arr[i].event, obj.arr[i].wrapper);
			}
		}
	},

	/**
	 *
	*/
	reset : function()
	{
		this.vars = [];
	}
});
{/literal}
