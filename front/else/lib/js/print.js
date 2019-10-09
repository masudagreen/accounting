/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Print = Class.create({

	initialize : function(obj)
	{
		this._iniVars(obj);
		this._iniCss();
		this._iniPrint();
	},

	/**
	 *
	*/
	vars : null,
	_iniVars : function(obj)
	{
		this.vars = obj.vars;
	},

	setVars : function(obj)
	{
		this.vars = obj.vars;
	},

	_iniCss : function()
	{
		if(!this.vars.pathCss) return;

		var head = {};
		$$('head').each(function(ele)
		{
			head = ele;
		});
		var link = document.createElement('link');
		link.href = this.vars.varsStatus.pathCss;
		link.rel = 'stylesheet';
		link.type = 'text/css';

		var array = $$('link');
		for(var i = 0; i < array.length; i++) {
			if(array[i].href == link.href) return;
		}
		head.insert(link);

	},

	_iniPrint : function()
	{
		this._setPrint();
	},

	_setPrint : function()
	{
		document.title = this.vars.varsStatus.strTitle;
		this._numBreakHeight = 0;
		this._setPrintInsert({
			arr : this.vars.varsDetail
		});

		window.print();
	},

	_numBreakHeight : 0,
	_setPrintInsert : function(obj)
	{
		var numPage = 1;
		var numAllPage = 1;
		var tmplstr = this.vars.varsStatus.varsTmpl.tmplWrap;
		var strWrap = '';
		if (this.vars.varsStatus.flagPageHide) {
			strWrap = tmplstr.interpolate({numPage : '', numAllPage : ''});
		} else {
			strWrap = tmplstr.interpolate({numPage : numPage, numAllPage : numAllPage});
		}
		$('Root').innerHTML = strWrap;

		var flagChangeTable = '';

		var flagFirst = 1;
		var flagBlankTableCheck = 0;

		for (var i = 0; i < obj.arr.length; i++) {
			var strTable = '';
			var strPage = '';
			var idTable = obj.arr[i].idTmplTable + '_' + numAllPage;

			if (!flagFirst && idTable != flagChangeTable) {
				/*tmplTable_1page->tmplTableBottom_1->tmplTable_1(flagBreak)*/
				if (!$(idTable)) {
					tmplstr = this.vars.varsStatus.varsTmpl[obj.arr[i].idTmplTable];
					strTable = tmplstr.interpolate({id : idTable});
					$(this.vars.varsStatus.idInsertTable).insert(strTable);
					flagChangeTable = idTable;
					flagBlankTableCheck = 1;
					if (!obj.arr[i].flagColumnNone && !obj.arr[i].flagBreak) {
						$(idTable).insert(this.vars.varsStatus.varsTmpl[obj.arr[i].idTmplColumn]);
					}
				}
			}

			if (flagFirst) {
				flagFirst = 0;
				flagChangeTable = idTable;
				tmplstr = this.vars.varsStatus.varsTmpl[obj.arr[i].idTmplTable];
				if (this.vars.varsStatus.flagPageHide) {
					strTable = tmplstr.interpolate({id : idTable, numPage : '', numAllPage : ''});
				} else {
					strTable = tmplstr.interpolate({id : idTable, numPage : numPage, numAllPage : numAllPage});
				}
				$(this.vars.varsStatus.idInsertTable).insert(strTable);
				$(idTable).insert(this.vars.varsStatus.varsTmpl[obj.arr[i].idTmplColumn]);

			}

			tmplstr = obj.arr[i].strRow;
			var tplObj = {};
			tplObj.idRow = obj.arr[i].id;
			for (var j = 1; j <= obj.arr[i].numTr; j++) {
				var str = 'idTr' + j;
				tplObj[str] = j;
			}
			var strRow = tmplstr.interpolate(tplObj);
			if ($(idTable)) {
				$(idTable).insert(strRow);
			}

			var numHeight = $(this.vars.varsStatus.idHeight).offsetHeight + this._numBreakHeight;
			var numTarget = this.vars.varsStatus.numHeight * numAllPage;
			var flagNet = numHeight - numTarget;
			if (flagNet > 0) {
				for (var j = 1; j <= obj.arr[i].numTr; j++) {
					idRow = 'row' + obj.arr[i].id + '_' + j;
					if ($(idRow)) {
						$(idRow).remove();
					}
				}

				var eleLine = $(document.createElement('hr'));
				$(this.vars.varsStatus.idInsertTable).insert(eleLine);

				numPage++;
				numAllPage++;
				tmplstr = this.vars.varsStatus.varsTmpl.tmplPage;
				if (this.vars.varsStatus.flagPageHide) {
					strPage = tmplstr.interpolate({numPage : '', numAllPage : ''});
				} else {
					strPage = tmplstr.interpolate({numPage : numPage, numAllPage : numAllPage});
				}
				$(this.vars.varsStatus.idInsertTable).insert(strPage);

				if (obj.arr[i].flagBreak) {
					if (obj.arr[i].idTmplTableTop) {
						tmplstr = this.vars.varsStatus.varsTmpl[obj.arr[i].idTmplTableTop];
						strTable = tmplstr.interpolate(obj.arr[i]);
						$(this.vars.varsStatus.idInsertTable).insert(strTable);
					}
				}

				idTable = obj.arr[i].idTmplTable + '_' + numAllPage;

				if (idTable != flagChangeTable) {
					tmplstr = this.vars.varsStatus.varsTmpl[obj.arr[i].idTmplTable];
					strTable = tmplstr.interpolate({id : idTable});
					$(this.vars.varsStatus.idInsertTable).insert(strTable);

				} else {
					tmplstr = this.vars.varsStatus.varsTmpl[obj.arr[i].idTmplTable];
					strTable = tmplstr.interpolate({id : idTable});
					$(this.vars.varsStatus.idInsertTable).insert(strTable);

				}
				flagChangeTable = idTable;

				if (!obj.arr[i].flagColumnNone) {
					$(idTable).insert(this.vars.varsStatus.varsTmpl[obj.arr[i].idTmplColumn]);
				}
				$(idTable).insert(strRow);
				continue;

			}
			if (obj.arr[i].flagBreak) {
				for (var j = 1; j <= obj.arr[i].numTr; j++) {
					idRow = 'row' + obj.arr[i].id + '_' + j;
					if ($(idRow)) {
						$(idRow).remove();
					}
				}
				/*tmplTable_1page->row insert->remove row->tmplTable_2page insert blank table remain*/
				if (flagBlankTableCheck) {
					var arrEle = $(idTable).getElementsByTagName('tr');
					var numLen = arrEle.length;
					if (!numLen) {
						$(idTable).remove();
					}
					flagBlankTableCheck = 0;
				}

				var eleLine = $(document.createElement('hr'));
				$(this.vars.varsStatus.idInsertTable).insert(eleLine);
				numHeight = $(this.vars.varsStatus.idHeight).offsetHeight + this._numBreakHeight;
				numTarget = this.vars.varsStatus.numHeight * numAllPage;
				this._numBreakHeight += numTarget - numHeight;

				numPage = 1;
				numAllPage++;
				tmplstr = this.vars.varsStatus.varsTmpl.tmplPage;
				if (this.vars.varsStatus.flagPageHide) {
					strPage = tmplstr.interpolate({numPage : '', numAllPage : ''});
				} else {
					strPage = tmplstr.interpolate({numPage : numPage, numAllPage : numAllPage});
				}
				$(this.vars.varsStatus.idInsertTable).insert(strPage);

				if (obj.arr[i].idTmplTableTop) {
					tmplstr = this.vars.varsStatus.varsTmpl[obj.arr[i].idTmplTableTop];
					strTable = tmplstr.interpolate(obj.arr[i]);
					$(this.vars.varsStatus.idInsertTable).insert(strTable);
				}

				idTable = obj.arr[i].idTmplTable + '_' + numAllPage;
				flagChangeTable = idTable;

				tmplstr = this.vars.varsStatus.varsTmpl[obj.arr[i].idTmplTable];
				strTable = tmplstr.interpolate({id : idTable});
				$(this.vars.varsStatus.idInsertTable).insert(strTable);

				$(idTable).insert(this.vars.varsStatus.varsTmpl[obj.arr[i].idTmplColumn]);
				$(idTable).insert(strRow);
			}
		}
	}

});

