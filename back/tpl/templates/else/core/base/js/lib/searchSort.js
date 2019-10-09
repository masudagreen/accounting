{literal}
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_SearchSort = Class.create(Code_Lib_Search,
{

	/**
	 *
	*/
	initialize : function(obj)
	{
		this._extAllot(obj);
		this._iniVars(obj);
		this._iniListener();
		this._iniWrap();
		this._iniLine();
	},

	/**
	 *
	*/
	iniReload : function()
	{
		this.updateVarsValue();
		this.stopListener();
		this.removeWrap();
		this._iniWrap();
		this._iniLine();
	},

	/**
	 *
	*/
	_iniVars : function(obj)
	{
		this._extVars(obj);
	},

	/**
	 *
	*/
	_iniLine : function()
	{
		this._setLineWrap();
		this._setLine();
	},

	/**
	 *
	*/
	eleWrapLine : null,
	_setLineWrap : function()
	{
		var ele = $(document.createElement('div'));
		ele.addClassName('codeLibSearchSortLineWrap');
		this.eleWrap.insert(ele);
		this.eleWrapLine = ele;
	},

	/**
	 *
	*/
	_staticLine : {numIdle : 10, numMargin : 5},
	_setLine : function()
	{

		var eleForm = $(document.createElement('form'));
		this.eleWrapLine.insert(eleForm);

		var eleLine = $(document.createElement('div'));
		eleLine.unselectable = 'on';
		eleLine.addClassName('codeLibSearchSortLine');
		eleLine.id = this.idSelf + 'Line' + this.vars.varsDetail.id;
		eleLine.setStyle({
			width     : this._getWrapWidth() + 'px',
			marginTop : (this._staticLine.numMargin) + 'px'
		});
		eleForm.insert(eleLine);

		var width = this._getLineWidth();
		var widthItem = Math.floor(width * 0.5);
		var widthSort = Math.floor(width * 0.5);

		var eleItem;
		eleItem = $(document.createElement('select'));
		eleItem.addClassName('codeLibSearchSortItem');
		eleItem.value = this.vars.varsDetail.itemValue;
		eleItem.id = this.idSelf + 'LineItem' + this.vars.varsDetail.id;
		eleItem.style.width = widthItem + 'px';
		this._setLineSelect({
			arr       : this.vars.itemOption,
			now       : this.vars.varsDetail.itemValue,
			eleInsert : eleItem
		});
		eleLine.insert(eleItem);

		eleSort = $(document.createElement('select'));
		eleSort.addClassName('codeLibSearchSortSort');
		eleSort.addClassName('codeLibBaseMarginLeftFive');
		eleSort.value = this.vars.varsDetail.sortValue;
		eleSort.id = this.idSelf + 'LineSort' + this.vars.varsDetail.id;
		eleSort.style.width = widthSort + 'px';
		this._setLineSelect({
			arr       : this.vars.sortOption,
			now       : this.vars.varsDetail.sortValue,
			eleInsert : eleSort
		});
		eleLine.insert(eleSort);

	},

	/**
	 *
	*/
	_setLineSelect : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var ele = $(document.createElement('option'));
			ele.value = obj.arr[i].value;
			if (obj.now == obj.arr[i].value) ele.selected = 'true';
			ele.insert(obj.arr[i].strTitle);
			obj.eleInsert.insert(ele);
		}
	},

	/**
	 *
	*/
	_getLineWidth : function()
	{
		var width = this._getWrapWidth();
		width -= this._staticLine.numMargin * 2;
		width -= this._staticLine.numIdle;

		return width;
	},

	/**
	 *
	*/
	_iniListener : function()
	{
		this._extListener();
	},

	/**
	 * Wrap
	*/
	_iniWrap : function()
	{
		this._extWrap();
		this.eleWrap.style.width = this._getWrapWidth() + 'px';
	},

	/**
	 *
	*/
	updateVarsValue : function()
	{
		if ($(this.idSelf + 'LineItem' + this.vars.varsDetail.id)) {
			var id = this.idSelf + 'LineItem' + this.vars.varsDetail.id;
			this.vars.varsDetail.itemValue = $(id).value;
		}
		if ($(this.idSelf + 'LineSort' + this.vars.varsDetail.id)) {
			var id = this.idSelf + 'LineSort' + this.vars.varsDetail.id;
			this.vars.varsDetail.sortValue = $(id).value;
		}
	},

	/**
	 * Value
	*/
	getValue : function()
	{
		this.updateVarsValue();

		return this.vars.varsDetail;
	}

});
{/literal}