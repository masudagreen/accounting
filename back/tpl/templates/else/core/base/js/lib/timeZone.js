{literal}
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_TimeZone = Class.create({

	/**
	 * obj = {
	 * 	numTimeZone : int,
	 * }
	*/
	numTimeZone : null, numTimeZoneDif : null,
	initialize : function(obj)
	{
		this.numTimeZone = obj.numTimeZone;
		this.numTimeZoneDif = new Date().getTimezoneOffset() * 60 * 1000;
	},

	/**
	 * obj = {

	 * }
	*/
	adjustStamp : function(obj)
	{
		var stamp = (parseFloat(obj.stamp) - this.numTimeZoneDif) - this.numTimeZone * 60 * 60 * 1000;

		return stamp;
	},

	/**
	 * obj = {
	 * 	stamp : stamp,
	 * }
	*/
	adjustDate : function(obj)
	{
		var ins = new Date( (this.numTimeZoneDif + parseFloat(obj.stamp)) + this.numTimeZone * 60 * 60 * 1000 );
		var objTime = this.getObjTime({ins : ins});

		return objTime;
	},

	/**

	*/
	adjustTime : function(obj)
	{
		var adjustStamp = this.adjustStamp({stamp : obj.stamp});
		var adjustDate = this.adjustDate({stamp : adjustStamp});
		adjustDate.stamp = adjustStamp;
		adjustDate.stampServer = Math.floor(adjustStamp/1000);

		return adjustDate;
	},

	/**
	 * obj = {
	 * 	ins : instance,
	 * }
	*/
	getObjTime : function(obj)
	{
		var ins = obj.ins;

		var arrDay = {
			'0' : 'Sun',
			'1' : 'Mon',
			'2' : 'Tue',
			'3' : 'Wed',
			'4' : 'Thu',
			'5' : 'Fri',
			'6' : 'Sat',
		};

		return {
			stamp          : ins.getTime(),
			numYear        : (new Date().getYear() > 1900) ? ins.getYear() + 0  : ins.getYear() + 1900,
			numMonth       : ins.getMonth(),
			numDate        : ins.getDate(),
			numDay         : ins.getDay(),
			strDay         : arrDay[ins.getDay()],
			numHour        : ins.getHours(),
			numMin         : ins.getMinutes(),
			numSec         : ins.getSeconds(),
			numMsec        : ins.getMilliseconds()
		};

	}
});
{/literal}