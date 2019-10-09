{literal}
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_FormCheck = Class.create(Code_Lib_ExtLib,
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
		this._iniBtn();
		this._iniColumn();
		this._iniLine();
	},

	/**
	 *
	*/
	iniReload : function()
	{
		this.stopListener();
		this.removeWrap();
		this._iniWrap();
		this._iniBtn();
		this._iniColumn();
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
		this.eleWrap.addClassName('codeLibFormCheckWrap');
		var numWidth = this._getWrapWidth({arr : this.vars.varsColumn});
		var strWidth = numWidth + 'px';
		var numHeight = this._getWrapHeight({arr : this.vars.varsDetail});
		var strHeight = numHeight + 'px';

		this.eleWrap.setStyle({
			width  : strWidth,
			height : strHeight
		});

	},

	/**
	 *
	*/
	_staticWrap : {numDouble : 3, numSingle : 1, numHeight : 16},
	_getWrapWidth : function(obj)
	{
		var num = this._staticWrap.numSingle;
		for (var i = 0; i < obj.arr.length; i++) {
			if(i == 0) {
				num += this._staticWrap.numDouble;

			} else {
				num += this._staticWrap.numSingle;

			}
			num += obj.arr[i].numWidth;
		}

		return num;
	},

	/**
	 *
	*/
	_getWrapHeight : function(obj)
	{
		var num = this._staticWrap.numSingle + this._staticWrap.numHeight + this._staticWrap.numDouble;
		for (var i = 0; i < obj.arr.length; i++) {
			num += this._staticWrap.numSingle;
			num += this._staticWrap.numHeight;
		}

		return num;
	},

	/**
	 * Btn
	*/
	_iniBtn : function()
	{
		if(!this.vars.varsStatus.flagBtnUse) return;
		this._setBtnWrap();
		this._setBtn();
	},

	/**
	 *
	*/
	eleWrapBtn : null,
	_setBtnWrap : function()
	{
		var ele = $(document.createElement('div'));
		ele.addClassName('codeLibFormCheckBtnWrap');
		this.eleWrap.insert(ele);
		this.eleWrapBtn = ele;
	},

	/**
	 *
	*/
	_setBtn : function()
	{
		var insBtn = new Code_Lib_Btn();
		insBtn.iniBtn({
			eleInsert  : this.eleWrapBtn,
			id         : this.idSelf + 'Btn',
			strFunc    : '_mousedownBtn',
			strTitle   : this.vars.varsStatus.strTitleBtn,
			insCurrent : this
		});
		this._setListener({ins : insBtn});
	},

	/**
	 *
	*/
	_mousedownBtn : function()
	{
		var flag = this.allot({
			from       : '_mousedownBtn',
			insCurrent : this
		});
		if (!flag) {
			this.iniReload();
		}
	},


	/**
	 *
	*/
	_iniColumn : function()
	{
		this._setColumn({arr : this.vars.varsColumn});
	},


	/**
	 *
	*/
	_staticColumn : {numHeight : 26, numPadding : 5, numDouble : 3, numSingle : 1},
	_setColumn : function(obj)
	{
		var eleLineSeparate = $(document.createElement('div'));
		eleLineSeparate.addClassName('codeLibFormCheckLineSingle');
		this.eleWrap.insert(eleLineSeparate);

		var eleLine = $(document.createElement('div'));
		eleLine.addClassName('codeLibFormCheckLine');
		eleLine.unselectable = 'on';
		eleLine.addClassName('unselect');
		this.eleWrap.insert(eleLine);
		eleLine.setStyle({
			width  : this._getWrapWidth({arr : this.vars.varsColumn}) + 'px'
		});

		var eleLineDoubleWrap = $(document.createElement('div'));
		eleLineDoubleWrap.setStyle({
			width  : this._getWrapWidth({arr : this.vars.varsColumn}) + 'px',
			height : this._staticColumn.numDouble + 'px'
		});
		this.eleWrap.insert(eleLineDoubleWrap);

		var eleSingle = $(document.createElement('span'));
		eleSingle.addClassName('codeLibFormCheckColumnSingle');
		eleSingle.setStyle({
			height : this._staticColumn.numDouble + 'px'
		});
		eleLineDoubleWrap.insert(eleSingle);

		eleDouble = $(document.createElement('span'));
		eleDouble.addClassName('codeLibFormCheckLineDouble');
		eleDouble.setStyle({
			width  : (this._getWrapWidth({arr : this.vars.varsColumn}) - this._staticColumn.numSingle * 2) + 'px',
			height : this._staticColumn.numDouble + 'px'
		});
		eleLineDoubleWrap.insert(eleDouble);

		var eleSingle = $(document.createElement('span'));
		eleSingle.addClassName('codeLibFormCheckColumnSingle');
		eleSingle.setStyle({
			height : this._staticColumn.numDouble + 'px'
		});
		eleLineDoubleWrap.insert(eleSingle);

		for (var i = 0; i < obj.arr.length; i++) {
			var eleSeparate;
			if (i == 0) {
				eleSeparate = $(document.createElement('span'));
				eleSeparate.addClassName('codeLibFormCheckColumnSingle');
				eleSeparate.setStyle({
					height  : this._staticColumn.numHeight + 'px'
				});
				eleLine.insert(eleSeparate);
			}

			var eleColumn = $(document.createElement('span'));
			eleColumn.addClassName('codeLibFormCheckColumn');
			eleColumn.addClassName('codeLibBaseCursorDefault');
			eleColumn.title = obj.arr[i].strTitle;
			eleColumn.setStyle({
				width   : (obj.arr[i].numWidth - this._staticColumn.numPadding) + 'px'
			});
			eleColumn.insert(obj.arr[i].strTitle);
			eleLine.insert(eleColumn);

			if (i == 0) {
				eleSeparate = $(document.createElement('span'));
				eleSeparate.addClassName('codeLibFormCheckColumnDouble');
				eleSeparate.setStyle({
					height  : this._staticColumn.numHeight + 'px'
				});

			} else {
				eleSeparate = $(document.createElement('span'));
				eleSeparate.addClassName('codeLibFormCheckColumnSingle');
				eleSeparate.setStyle({
					height  : this._staticColumn.numHeight + 'px'
				});
			}

			eleLine.insert(eleSeparate);

		}
	},

	/**
	 *
	*/
	_iniLine : function()
	{
		this._setLine({
			arrColumn : this.vars.varsColumn,
			arr       : this.vars.varsDetail
		});
	},

	/**
	 *
	*/
	_staticLine : {numHeight : 26, numPadding : 5},
	_setLine : function(obj)
	{
		var insEscape = new Code_Lib_Escape();
		for (var i = 0; i < obj.arr.length; i++) {

			var eleLine = $(document.createElement('div'));
			eleLine.addClassName('codeLibFormCheckLine');
			eleLine.id = this.idSelf + 'Line' + obj.arr[i].id;
			this.eleWrap.insert(eleLine);
			eleLine.setStyle({
				width  : this._getWrapWidth({arr : this.vars.varsColumn}) + 'px'
			});

			var eleLineSeparate = $(document.createElement('div'));
			eleLineSeparate.addClassName('codeLibFormCheckLineSingle');
			this.eleWrap.insert(eleLineSeparate);


			for (var j = 0; j < obj.arrColumn.length; j++) {
				var eleSeparate;
				if (j == 0) {
					eleSeparate = $(document.createElement('span'));
					eleSeparate.addClassName('codeLibFormCheckColumnSingle');
					eleSeparate.setStyle({
						height  : this._staticLine.numHeight + 'px'
					});
					eleLine.insert(eleSeparate);
				}

				var eleColumn = $(document.createElement('span'));
				eleColumn.id = this.idSelf + 'Line' + obj.arr[i].id + 'Column' + obj.arrColumn[j].id;
				eleColumn.addClassName('codeLibFormCheckColumn');
				eleColumn.setStyle({
					width   : (obj.arrColumn[j].numWidth - this._staticLine.numPadding) + 'px'
				});
				var str = insEscape.strLowCapitalize({data : obj.arrColumn[j].id});
				eleLine.insert(eleColumn);

				if (obj.arrColumn[j].flagType == 'check') {
					var strUse = str + 'Use';
					var strNow = str + 'Now';
					var strLock = str + 'Lock';
					eleColumn.unselectable = 'on';
					eleColumn.addClassName('unselect');

					if (obj.arr[i].varsColumnDetail[strUse]) {
						var eleCheck = $(document.createElement('span'));
						if (obj.arr[i].varsColumnDetail[strLock]) {
							if (obj.arr[i].varsColumnDetail[strNow]) {
								eleCheck.addClassName('codeLibFormCheckOnLock');

							} else {
								eleCheck.addClassName('codeLibFormCheckOffLock');
							}

						} else {
							eleCheck.addClassName('codeLibBaseCursorPointer');
							if (obj.arr[i].varsColumnDetail[strNow]) {
								eleCheck.addClassName('codeLibFormCheckOn');

							} else {
								eleCheck.addClassName('codeLibFormCheckOff');
							}

							this.insListener.set({
								bindAsEvent : 1, insCurrent : this, event : 'mousedown',
								strFunc : '_mousedownBtn', ele : eleCheck,
								vars : { vars : obj.arr[i], varsColumn : obj.arrColumn[j], flagUpdate : 1, ele : eleCheck }
							});
						}
						eleColumn.insert(eleCheck);

					}

				} else if (obj.arrColumn[j].flagType == 'btn') {
					var strUse = str + 'Use';
					var strLock = str + 'Lock';
					var strClass = str + 'StrClass';
					var strClassLock = str + 'StrClassLock';
					var strAtag = str + 'ATagUse';
					var strPath = str + 'Path';

					eleColumn.unselectable = 'on';
					eleColumn.addClassName('unselect');

					if (obj.arr[i].varsColumnDetail[strUse]) {

						if (obj.arr[i].varsColumnDetail[strLock]) {
							var eleBtn = $(document.createElement('span'));
							eleColumn.insert(eleBtn);
							eleBtn.addClassName(obj.arr[i].varsColumnDetail[strClassLock]);

						} else if (obj.arr[i].varsColumnDetail[strAtag]) {
							var eleBtn = $(document.createElement('a'));
							eleBtn.href = 'javascript:void(window.open("' + obj.arr[i].varsColumnDetail[strPath] + '"));';
							eleBtn.addClassName('codeLibBaseCursorPointer');
							eleBtn.addClassName(obj.arr[i].varsColumnDetail[strClass]);
							eleBtn.setStyle({ float : 'left' });
							eleColumn.insert(eleBtn);

						} else {

							var eleBtn = $(document.createElement('span'));
							eleColumn.insert(eleBtn);
							eleBtn.addClassName('codeLibBaseCursorPointer');
							eleBtn.addClassName(obj.arr[i].varsColumnDetail[strClass]);
							this.insListener.set({
								bindAsEvent : 1, insCurrent : this, event : 'mousedown',
								strFunc : '_mousedownBtn', ele : eleBtn,
								vars : { vars : obj.arr[i], varsColumn : obj.arrColumn[j], flagUpdate : 0, ele : eleBtn }
							});
						}
					}

				} else {
					eleColumn.title = obj.arr[i].varsColumnDetail[str];
					eleColumn.insert(obj.arr[i].varsColumnDetail[str]);

				}
				if (j == 0) {
					eleSeparate = $(document.createElement('span'));
					eleSeparate.addClassName('codeLibFormCheckColumnDouble');
					eleSeparate.setStyle({
						height  : this._staticLine.numHeight + 'px'
					});

				} else {
					eleSeparate = $(document.createElement('span'));
					eleSeparate.addClassName('codeLibFormCheckColumnSingle');
					eleSeparate.setStyle({
						height  : this._staticLine.numHeight + 'px'
					});
				}
				eleLine.insert(eleSeparate);
			}
		}
	},

	/**
	 *
	*/
	_mousedownBtn : function(evt, obj)
	{
		if (obj) evt.stop();
		else obj = evt;
		if (obj.flagUpdate) {
			this._updateBtnVars({
				vars       : obj.vars,
				varsColumn : obj.varsColumn,
			});
		}
		var flag = this.allot({
			insCurrent : this,
			from       : '_mousedownBtn',
			vars       : {
				vars       : obj.vars,
				varsColumn : obj.varsColumn,
				numTop     : obj.ele.offsetTop,
				numLeft    : obj.ele.offsetLeft,
			},
		});
		if (!flag) {
			this.iniReload();
		}

	},


	/**
	 *
	*/
	_updateBtnVars : function(obj)
	{
		var insEscape = new Code_Lib_Escape();
		var strNow = insEscape.strLowCapitalize({data : obj.varsColumn.id})+ 'Now';
		if (obj.vars.varsColumnDetail[strNow]) {
			obj.vars.varsColumnDetail[strNow] = 0;

		} else {
			obj.vars.varsColumnDetail[strNow] = 1;

		}

	}

});

{/literal}