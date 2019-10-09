{literal}
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_ScheduleMonth = Class.create(Code_Lib_Schedule,
{

	/**
	 *
	*/
	initialize : function(obj)
	{
		this._iniVars(obj);
		this._iniListener();
		this._iniGraduation();
		this._iniBody();
		this.insCurrent.iniBtnBottom();
		this.insCurrent.iniPage();
		this._iniLog();
		this._iniFold();
		this.iniBtnSelect();

	},

	/**
	 *
	*/
	eleInsertGraduation : null,
	_iniVars : function(obj)
	{
		this._extVars(obj);
		this.eleInsertGraduation = obj.eleInsertGraduation;
		this._iniCake();
	},

	/**
	 *
	*/
	updateVars : function()
	{
		this.insCurrent.updateVars();
	},

	/**
	 * Fold
	*/
	_iniFold : function()
	{
		if (!this.insCurrent.vars.varsStatus.flagFoldUse) return;
		this._setFoldListener({arr : this.vars.varsFold});
		this._setFold({arr : this.vars.varsFold});
	},

	/**
	 *
	*/
	_setFoldListener : function(obj)
	{
		this.insListener.set({ bindAsEvent : 1, insCurrent : this, event : 'mousedown',
			strFunc : '_mousedownFoldAll',
			ele : $(this.idSelf + 'GraduationWrap').down('.codeLibScheduleGraduationFold', 0),
			vars : '' });
		for (var i = 0; i < obj.arr.length; i++) {
			this.insListener.set({ bindAsEvent : 1, insCurrent : this, event : 'mousedown',
				strFunc : '_mousedownFold',
				ele : $(this.idSelf + 'BodyLineWrap' + i).down('.codeLibScheduleMonthBodyLineFold', 0),
				vars : { vars : {id : i }}
			});
		}
	},

	/**
	 *
	*/
	_setFold : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			this._styleFoldUpdate({
				flagEffect : 0,
				arr        : obj.arr,
				vars       : {id : i}
			});
		}
	},

	/**
	 *
	*/
	_mousedownFold : function(evt,obj) {
		if (obj) evt.stop();
		else obj = evt;
		this._varFoldUpdate({arr : this.vars.varsFold, vars : obj.vars});
		this._styleFoldUpdate({
			flagEffect : 1,
			arr        : this.vars.varsFold,
			vars       : obj.vars
		});
		this.setCake();
	},

	/**
	 *
	*/
	_varFoldUpdate : function(obj)
	{
		var num = obj.vars.id;
		if (obj.arr[num].flagFoldNow) obj.arr[num].flagFoldNow = 0;
		else obj.arr[num].flagFoldNow = 1;
	},

	/**
	 *
	*/
	_styleFoldUpdate : function(obj)
	{
		var num = obj.vars.id;
		var eleBox = $(this.idSelf + 'BodyLineWrap' + num ).down('.codeLibScheduleMonthBodyLineFold', 0);
		eleBox.removeClassName('codeLibScheduleFoldClose');
		eleBox.removeClassName('codeLibScheduleFoldOpen');
		var eleMiddle = $(this.idSelf + 'BodyLineWrap' + num).down('.codeLibScheduleMonthBodyLineMiddle', 0);
		if (obj.arr[num].flagFoldNow) {
			eleBox.addClassName('codeLibScheduleFoldOpen');
			if (obj.flagEffect) {
				new Effect.BlindDown(eleMiddle,{
					duration : 0.5
				});

			} else {
				eleMiddle.show();
			}

		} else {
			eleBox.addClassName('codeLibScheduleFoldClose');
			if (obj.flagEffect) {
				new Effect.BlindUp(eleMiddle,{
					duration : 0.5
				});

			} else {
				eleMiddle.hide();
			}
		}
	},

	/**
	 *
	*/
	_mousedownFoldAll : function(evt) {
		evt.stop();
		if (this.insCurrent.vars.varsStatus.flagFoldNow) {
			this.insCurrent.vars.varsStatus.flagFoldNow = 0;
			this._varFoldUpdateAll({
				arr         : this.vars.varsFold,
				flagFoldNow : this.insCurrent.vars.varsStatus.flagFoldNow
			});

		} else {
			this.insCurrent.vars.varsStatus.flagFoldNow = 1;
			this._varFoldUpdateAll({
				arr         : this.vars.varsFold,
				flagFoldNow : this.insCurrent.vars.varsStatus.flagFoldNow
			});
		}

		this._styleFoldUpdateAll({
			arr         : this.vars.varsFold,
			flagEffect  : 1,
			flagFoldNow : this.insCurrent.vars.varsStatus.flagFoldNow
		});
		this.setCake();
	},

	/**
	 *
	*/
	_varFoldUpdateAll : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			obj.arr[i].flagFoldNow = obj.flagFoldNow;
		}
	},

	/**
	 *
	*/
	_styleFoldUpdateAll : function(obj)
	{
		var eleGraduation = $(this.idSelf + 'GraduationWrap').down('.codeLibScheduleGraduationFold', 0);
		eleGraduation.removeClassName('codeLibScheduleFoldClose');
		eleGraduation.removeClassName('codeLibScheduleFoldOpen');
		if (obj.flagFoldNow) eleGraduation.addClassName('codeLibScheduleFoldOpen');
		else eleGraduation.addClassName('codeLibScheduleFoldClose');

		for (var i = 0; i < obj.arr.length; i++) {
			var eleBox = $(this.idSelf + 'BodyLineWrap' + i).down('.codeLibScheduleMonthBodyLineFold', 0);
			var eleMiddle = $(this.idSelf + 'BodyLineWrap' + i).down('.codeLibScheduleMonthBodyLineMiddle', 0);

			eleBox.removeClassName('codeLibScheduleFoldClose');
			eleBox.removeClassName('codeLibScheduleFoldOpen');

			if (obj.arr[i].flagFoldNow) {
				eleBox.addClassName('codeLibScheduleFoldOpen');
				eleMiddle.show();

			} else {
				eleBox.addClassName('codeLibScheduleFoldClose');
				eleMiddle.hide();
			}
		}
	},

	/**
	 * Log
	*/
	_iniLog : function()
	{
		this._varLog({
			arrDetail : (Object.toJSON(this.insCurrent.vars.varsDetail)).evalJSON(),
			arrLine   : this.vars.varsFold,
			arrMain   : this.insCurrent.varsVar.month.mainTime
		});
		this._checkLogLineHeight({
			arrDetail : this.insCurrent.vars.varsDetail,
			arrLine   : this.vars.varsFold,
			arrMain   : this.insCurrent.varsVar.month.mainTime
		});
		this._updateLogLineStyle({arr : this.vars.varsFold});
		this._setLogMainBottom({
			arrLine : this.vars.varsFold,
			arrMain : this.insCurrent.varsVar.month.mainTime
		});
		this._setLogLineWrap({arr : this.vars.varsFold});
		this._setLog({arr : this.vars.varsFold});
	},

	/**
	 *
	*/
	_styleLogUpdate : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			$(this.idSelf + 'BodyLineLogWrap' + i).hide();
			$(this.idSelf + 'BodyLineWrap' + i).down('.codeLibScheduleMonthBodyLineMiddle', 0).show();
			$(this.idSelf + 'BodyLineWrap' + i).down('.codeLibScheduleMonthBodyLineMiddle', 0).setStyle({
				height : this.varsBody.boxMiddleHeight + 'px'
			});
			$(this.idSelf + 'BodyLineWrap' + i).down('.codeLibScheduleMonthBodyLineMiddle', 0).setStyle({
				height : this.varsBody.boxMiddleHeight + 'px'
			});
			var ele = $(this.idSelf + 'BodyLineWrap' + i).down('.codeLibScheduleMonthBodyLineMiddle', 0);
			var array = ele.childNodes;
			for (var j = 0; j < array.length; j++) {
				array[j].setStyle({
					height : this.varsBody.boxMiddleHeight + 'px'
				});
			}
		}
		for (var j = 0; j < obj.arrMain.length; j++) {
			if ($(this.idSelf + 'BodyLineBoxBottom' + obj.arrMain[j] + 'BottomBtn')) {
				$(this.idSelf + 'BodyLineBoxBottom' + obj.arrMain[j] + 'BottomBtn').hide();
			}
		}
	},

	/**
	 *
	*/
	_styleLogDateUpdate : function(obj)
	{
		var cut = obj.vars.vars.varsScheduleDetail;
		var insCheck = new Code_Lib_CheckTime();
		if (cut.flagType == 'stamp') {
			for (var j = 0; j < obj.arrLevel.length; j++) {
				var ele = $(this.idSelf + 'BodyLineBox' + obj.arrLevel[j] + obj.vars.mainTime.numDate);
				ele.addClassName(obj.vars.strClassBg);
			}

			return;

		} else if (cut.flagType == 'Term') {

			var objTargetStart = this.insRoot.insTimeZone.adjustDate({ stamp : cut.term.stampStart * 1000 });
			var objTargetEnd = this.insRoot.insTimeZone.adjustDate({ stamp : cut.term.stampEnd * 1000 });
			for (var j = objTargetStart.date - 1; j < obj.arrMain.length; j++) {
				var flag = insCheck.getTerm({
					stampStart     : objTargetStart.stamp,
					stampEnd       : objTargetEnd.stamp,
					stampWrapStart : obj.arrMain[j].stamp,
					stampWrapEnd   : obj.arrMain[j].stamp + 86400 * 1000
				});
				if (!flag) continue;

				for (var k = 0; k < obj.arrLevel.length; k++) {
					var ele = $(this.idSelf + 'BodyLineBox' + obj.arrLevel[k] + obj.arrMain[j].date);
					ele.addClassName(obj.vars.strClassBg);
					ele.removeClassName('codeLibScheduleNow');
				}
			}
		}

	},


	/**
	 *
	*/
	_setLogLineWrap : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var eleInsert = $(this.idSelf + 'BodyLineWrap' + i).down('.codeLibScheduleMonthBodyLineMiddle', 0);
			var eleWrap = $(document.createElement('div'));
			eleWrap.addClassName('codeLibScheduleMonthBodyLineMiddleLogWrap');
			eleWrap.id = this.idSelf + 'BodyLineLogWrap' + i;
			eleWrap.setStyle({
				position : 'relative',
				height   : eleInsert.offsetHeight + 'px'
			});
			eleInsert.insert(eleWrap);
		}
	},

	/**
	 *
	*/
	_setLog : function(obj)
	{

		for (var i = 0; i < obj.arr.length; i++) {
			var strLine = 'line' + i;
			var omit = this._varsBodyMainLine[strLine];
			if (!omit.length) continue;
			var eleInsert =
				$(this.idSelf + 'BodyLineWrap' + i).down('.codeLibScheduleMonthBodyLineMiddleLogWrap', 0);
			var str = 'line' + i;
			var array = this._varsLog[str];
			for (var j = 0; j < array.length; j++) {

				var id = this.idSelf + 'BodyLineLog' + i + array[j].mainTime.numDate + array[j].vars.id;
				var flagLeftUse = 0 , flagRightUse = 0, flagResizeUse = 0;
				if (array[j].flag == 'all') {
					flagLeftUse = 0 , flagRightUse = 0;
				} else if (array[j].flag == 'left') {
					flagLeftUse = 1 , flagRightUse = 0;
				} else if (array[j].flag == 'right') {
					flagLeftUse = 0 , flagRightUse = 1;
				} else if (array[j].flag == 'middle') {
					flagLeftUse = 1 , flagRightUse = 1;
				}

				this.insCurrent.iniLog({
					mainTime           : array[j].mainTime,
					vars               : array[j].vars,
					eleInsert          : eleInsert,
					id                 : id,
					numTop             : array[j].numTop,
					numLeft            : array[j].numLeft,
					numWidth           : array[j].numWidth,
					strTime            : array[j].strTime,
					strTitle           : array[j].vars.strTitle,
					strClass           : array[j].vars.strClass,
					strClassLoad       : array[j].vars.strClassLoad,
					strClassBg         : (this.insCurrent.vars.varsStatus.flagBgUse)?
											array[j].vars.strClassBg : '',
					strClassFont       : (this.insCurrent.vars.varsStatus.flagFontUse)?
											array[j].vars.strClassFont : '',
					flagBoldNow        : array[j].vars.flagBoldNow,
					flagBtnUse         : array[j].vars.flagBtnUse,
					flagLeftUse        : flagLeftUse,
					flagMoveUse        : (this.insCurrent.vars.varsStatus.flagMoveUse
											&& array[j].vars.flagMoveUse)? 1 : 0,
					flagResizeUse      : (this.insCurrent.vars.varsStatus.flagResizeUse
											&& array[j].vars.varsScheduleDetail.flagResizeUse)? 1 : 0,
					flagResizeLeftUse  : (array[j].flagResizeLeftUse)? 1 : 0,
					flagResizeRightUse : (array[j].flagResizeRightUse)? 1 : 0,
					flagRightUse       : flagRightUse
				});
			}
		}
	},

	/**
	 *
	*/
	_checkLogLineHeight : function(obj)
	{
		for (var i = 0; i < obj.arrLine.length; i++) {
			var strLine = 'line' + i;
			var omit = this._varsBodyMainLine[strLine];
			if (!omit.length) continue;
			var strLineHeight = 'lineHeight' + i;
			for (var j = 0; j < omit.length; j++) {
				var num = this._checkLogLineHeightTop({
					arr     : obj.arrDetail,
					numDate : omit[j].numDate
				});

				if ( num > this._varsLog[strLineHeight] ) this._varsLog[strLineHeight] = num;

			}


		}
	},

	/**
	 *
	*/
	_checkLogLineHeightTop : function(obj)
	{
		for (var i = obj.arr.length - 1; i >= 0; i--) {
			var strDate = 'd' + obj.numDate + '-'+ i;

			if (this._varsLog.flagTop[strDate]) {
				return i;
			}
		}

		return 0;
	},

	/**
	 *
	*/
	_varsLog : null,
	_updateLogLineStyle : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var strLineHeight = 'lineHeight' + i;
			var num = this._varsLog[strLineHeight]+1;
			var ele = $(this.idSelf + 'BodyLineWrap' + i).down('.codeLibScheduleMonthBodyLineMiddle', 0);
			var a = ele.offsetHeight;
			var b = num * this._staticLog.numHeight;
			if (b > a) {
				ele.setStyle({
					height : (num * this._staticLog.numHeight) + 'px'
				});
				var array = ele.childNodes;
				for (var j = 0; j < array.length; j++) {
					array[j].setStyle({
						height : (num * this._staticLog.numHeight) + 'px'
					});
				}
			}
		}
	},

	/**
	 *
	*/
	_setLogMainBottom : function(obj)
	{
		for (var i = 0; i < obj.arrLine.length; i++) {
			var strLine = 'line' + i;
			var omit = this._varsBodyMainLine[strLine];
			if (!omit.length) continue;
			for (var j = 0; j < omit.length; j++) {
				var strNum = 'n' + omit[j].numDate;
				if (this._varsLog.numLogMain[strNum]) {
					var strLogs = this._varsLog.numLogMain[strNum]
								+ this.insCurrent.varsLoad.varsWhole.str.item
								+ this.insCurrent.varsLoad.varsWhole.str.hit;
					var insBtn = new Code_Lib_Btn();
					var id = this.idSelf + 'BodyLineBoxBottom' + omit[j].numDate + 'BottomBtn';
					var ele = $(this.idSelf + 'BodyLineBoxBottom' + omit[j].numDate);
					insBtn.iniBtnText({
						eleInsert  : ele,
						id         : id,
						strFunc    : '_mousedownLogMainBottom',
						strTitle   : strLogs,
						insCurrent : this.insSelf,
						vars       : omit[j]
					});
					$(id).setStyle({
						width     : (ele.offsetWidth - this._staticLog.numPadding * 2) + 'px',
						textAlign : 'center'
					});
					this._setListener({ins : insBtn});
				}
			}
		}
	},

	/**
	 *
	*/
	_mousedownLogMainBottom : function(obj)
	{
		this.insCurrent.vars.varsStatus.stampMain = obj.vars.vars.stamp;
		if (this.insCurrent.vars.varsStatus.flagNow == 'month') {
			this.insCurrent.vars.varsStatus.flagNow = 'week';
		}
		else this.insCurrent.vars.varsStatus.flagNow = 'month';
		this.setCake();
		this.insCurrent.iniReload();
	},

	/**
	 *
	*/
	_staticLog : {numHeight : 21, numStartLeft : 21, numPadding : 5, numSeparate : 1 },
	_varLog : function(obj)
	{
		this._varsLog = {
			flagTop    : {},/*d_{date}_{top} : 1*/
			numLogMain : {},

			line0 : [], lineHeight0 : 0,
			line1 : [], lineHeight1 : 0,
			line2 : [], lineHeight2 : 0,
			line3 : [], lineHeight3 : 0,
			line4 : [], lineHeight4 : 0,
			line5 : [], lineHeight5 : 0
		};
		var insDisplay = new Code_Lib_TimeDisplay();
		var insCheck = new Code_Lib_CheckTime();
		var numWidth = this._varsGraduation.numWidthGraph;
		obj.arrDetail = obj.arrDetail.sortBy(function(v, i) {
			return obj.arrDetail[i].stampRegister;
		});
		obj.arrDetail = obj.arrDetail.sortBy(function(v, i) {
			var cut = obj.arrDetail[i].varsScheduleDetail;
			if (cut.flagType == 'stamp' || cut.flagType == 'loop') return 0;
			else if (cut.flagType == 'Term') return -1 * (cut.term.stampEnd - cut.term.stampStart);
		});

		for (var i = 0; i < obj.arrDetail.length; i++) {
			var cut = obj.arrDetail[i].varsScheduleDetail;
			if (cut.flagType == 'stamp') {
				var objTarget = this.insRoot.insTimeZone.adjustDate({ stamp : cut.stamp * 1000 });
				var str = insDisplay.get({
					flagType : 1,
					vars     : objTarget
				});
				var strTime = str;
				var numHeight = 0;
				for (var j = 0; j < obj.arrLine.length; j++) {
					/*check week*/
					var strLine = 'line' + j;
					var omit = this._varsBodyMainLine[strLine];
					if (!omit.length) continue;
					var stamp = objTarget.stamp;
					var stampWrapStart = omit[0].stamp;
					var stampWrapEnd = omit[omit.length - 1].stamp + 86400 * 1000;
					var flag = insCheck.getStamp({
						stamp          : stamp,
						stampWrapStart : stampWrapStart,
						stampWrapEnd   : stampWrapEnd
					});
					if (!flag) continue;

					var rowData = {
						flag     : flag,
						strTime  : strTime,
						numLeft  : 0,
						numTop   : 0,
						numWidth : numWidth,
						mainTime : null,
						vars     : this._getLogVars({
							arr  : this.insCurrent.vars.varsDetail,
							vars : obj.arrDetail[i]
						})
					};

					for (var k = 0; k < omit.length; k++) {
						var stamp = objTarget.stamp;
						var stampWrapStart = omit[k].stamp;
						var stampWrapEnd = omit[k].stamp + 86400 * 1000;
						var flag = insCheck.getStamp({
							stamp          : objTarget.stamp,
							stampWrapStart : stampWrapStart,
							stampWrapEnd   : stampWrapEnd
						});

						if (!flag) continue;

						rowData.mainTime = omit[k];
						rowData.numWidth = numWidth;
						rowData.numLeft = omit[k].numDay
										* (numWidth
										+ this._staticLog.numSeparate)
										+ this._staticLog.numStartLeft
										+ this._staticLog.numSeparate;

						var numTop = this._checkLogTop({
							arr     : obj.arrDetail,
							numDate : omit[k].numDate
						});
						rowData.numTop = this._staticLog.numHeight * numTop;
						var strDate = 'd' + omit[k].numDate + '-' + numTop;
						this._varsLog.flagTop[strDate] = {id : obj.arrDetail[i].id};

						var strNum = 'n' + omit[k].numDate;
						if (this._varsLog.numLogMain[strNum]) this._varsLog.numLogMain[strNum]++;
						else this._varsLog.numLogMain[strNum] = 1;

						var str = 'line' + j;
						this._varsLog[str].push(rowData);
						break;
					}
				}

			} else if (cut.flagType == 'Term') {
				var objTargetStart = this.insRoot.insTimeZone.adjustDate({stamp : cut.term.stampStart * 1000});
				var objTargetEnd = this.insRoot.insTimeZone.adjustDate({stamp : cut.term.stampEnd * 1000});
				var strStart = insDisplay.get({
					flagType : 1,
					vars     : objTargetStart
				});
				var strEnd = insDisplay.get({
					flagType : 1,
					vars     : objTargetEnd
				});
				var strTime = '(' + strStart +' ~ '+ strEnd + ')';
				var numHeight = 0;
				for (var j = 0; j < obj.arrLine.length; j++) {
					var strLine = 'line' + j;
					var omit = this._varsBodyMainLine[strLine];
					if (!omit.length) continue;
					var flagLine = insCheck.getTerm({
						stampStart     : objTargetStart.stamp,
						stampEnd       : objTargetEnd.stamp,
						stampWrapStart : omit[0].stamp,
						stampWrapEnd   : omit[omit.length - 1].stamp + 86400*1000
					});
					if (!flagLine) continue;
					var rowData = {
						flag               : flagLine,
						flagResizeLeftUse  : 0,
						flagResizeRightUse : 0,
						strTime            : strTime,
						numLeft            : 0,
						numTop             : 0,
						numWidth           : 0,
						mainTime           : null,
						vars               : this._getLogVars({
							arr  : this.insCurrent.vars.varsDetail,
							vars : obj.arrDetail[i]
						})
					};

					/*left width*/
					var numStart = 0, numEnd = 0, flagStart = null, flagEnd = null;
					for (var k = 0; k < omit.length; k++) {
						flagStart = insCheck.getTerm({
							stampStart     : objTargetStart.stamp,
							stampEnd       : objTargetEnd.stamp,
							stampWrapStart : omit[k].stamp,
							stampWrapEnd   : omit[k].stamp + 86400 * 1000
						});
						if (!flagStart) continue;
						numStart = k;
						break;
					}

					for (var k = omit.length - 1; k >= 0; k--) {
						flagEnd = insCheck.getTerm({
							stampStart     : objTargetStart.stamp,
							stampEnd       : objTargetEnd.stamp,
							stampWrapStart : omit[k].stamp,
							stampWrapEnd   : omit[k].stamp + 86400 * 1000
						});
						if (!flagEnd) continue;
						numEnd = k;
						break;
					}
					rowData.mainTime = omit[numStart];
					rowData.numLeft = omit[numStart].numDay
									* (numWidth + this._staticLog.numSeparate)
									+ this._staticLog.numStartLeft
									+ this._staticLog.numSeparate;
					rowData.numWidth = (numEnd - numStart  + 1)
									* (numWidth + this._staticLog.numSeparate);
					if (flagLine == 'left') {
						rowData.numWidth -= this._staticLog.numSeparate;
					}

					/*flagResize*/
					var omitMonth = this.insCurrent.varsVar.month.mainTime;
					var flagMonth = insCheck.getTerm({
						stampStart     : objTargetStart.stamp,
						stampEnd       : objTargetEnd.stamp,
						stampWrapStart : omitMonth[0].stamp,
						stampWrapEnd   : omitMonth[omitMonth.length - 1].stamp
					});

					if ((flagMonth == 'right' || flagMonth == 'all') && flagStart == 'right') {
						rowData.flagResizeLeftUse = 1;
					}
					if ((flagMonth == 'left' || flagMonth == 'all') && flagEnd == 'left') {
						rowData.flagResizeRightUse = 1;
					}

					var flagTop = 0, numTop = 0;
					for (var k = 0; k < omit.length; k++) {
						numTop = this._checkLogTopId({
							arr     : obj.arrDetail,
							id      : obj.arrDetail[i].id,
							numDate : omit[k].numDate
						});
						if (numTop == 'false') continue;
						flagTop = 1;
						break;
					}
					if (!flagTop) {
						numTop = this._checkLogTop({
							arr     : obj.arrDetail,
							numDate : omit[numStart].numDate
						});
						for (var p = omit[numStart].numDate - 1; p < obj.arrMain.length; p++) {
							var flag = insCheck.getTerm({
								stampStart     : objTargetStart.stamp,
								stampEnd       : objTargetEnd.stamp,
								stampWrapStart : obj.arrMain[p].stamp,
								stampWrapEnd   : obj.arrMain[p].stamp + 86400 * 1000
							});
							if (!flag) break;

							/*for top same id*/
							var strDate = 'd' + obj.arrMain[p].date + numTop;
							this._varsLog.flagTop[strDate] = {id : obj.arrDetail[i].id};

							/*numLog/date*/
							var strNum = 'n' + obj.arrMain[p].numDate;
							if (this._varsLog.numLogMain[strNum]) this._varsLog.numLogMain[strNum]++;
							else this._varsLog.numLogMain[strNum] = 1;

						}
					}
					rowData.numTop = this._staticLog.numHeight * numTop;
					var str = 'line' + j;
					this._varsLog[str].push(rowData);
				}

			} else if (cut.flagType == 'loop') {
				for (var j = 0; j < obj.arrLine.length; j++) {
					var strLine = 'line' + j;
					var omit = this._varsBodyMainLine[strLine];
					if (!omit.length) continue;
					for (var k = 0; k < omit.length; k++) {
						objEndWrap = this.insRoot.insTimeZone.adjustDate({stamp : omit[k].stamp + 86400 * 1000});
						cut.loop.objStartWrap = omit[k];
						cut.loop.objEndWrap = objEndWrap;
						cut.loop.insTimeZone = this.insRoot.insTimeZone;
						var flag = insCheck.getLoop(cut.loop);
						if (!flag) continue;

						var stampStart = omit[k].stamp
										+ (cut.loop.start.hour * 60 * 60 + cut.loop.start.min * 60) * 1000;
						var stampEnd =  omit[k].stamp
										+ (cut.loop.end.hour * 60 * 60 + cut.loop.end.min * 60) * 1000;

						var strTime = '';
						if (stampEnd == omit[k].stamp && stampStart == omit[k].stamp) {
							strTime = insDisplay.get({
								flagType : 2,
								vars : this.insRoot.insTimeZone.adjustDate({ stamp : stampStart })
							});
						} else if (stampStart == stampEnd) {
							strTime = insDisplay.get({
								flagType : 1,
								vars : this.insRoot.insTimeZone.adjustDate({ stamp : stampStart })
							});
						} else {
							var strStart = insDisplay.get({
								flagType : 1,
								vars : this.insRoot.insTimeZone.adjustDate({ stamp : stampStart })
							});
							var strEnd = insDisplay.get({
								flagType : 1,
								vars : this.insRoot.insTimeZone.adjustDate({ stamp : stampEnd })
							});
							strTime = '(' + strStart + ' ~ ' + strEnd + ')';
						}

						var rowData = {
							flag     : 'all',
							strTime  : strTime,
							numLeft  : 0,
							numTop   : 0,
							numWidth : 0,
							mainTime : omit[k],
							vars : this._getLogVars({
								arr  : this.insCurrent.vars.varsDetail,
								vars : obj.arrDetail[i]
							})
						};

						rowData.numWidth = numWidth;
						rowData.numLeft = omit[k].numDay
										* (numWidth + this._staticLog.numSeparate)
										+ this._staticLog.numStartLeft
										+ this._staticLog.numSeparate;

						var numTop = this._checkLogTop({
							arr     : obj.arrDetail,
							numDate : omit[k].numDate
						});
						rowData.numTop = this._staticLog.numHeight * numTop;
						var strDate = 'd' + omit[k].numDate + '-' + numTop;
						this._varsLog.flagTop[strDate] = {id : obj.arrDetail[i].id};

						var strNum = 'n' + omit[k].numDate;
						if (this._varsLog.numLogMain[strNum]) this._varsLog.numLogMain[strNum]++;
						else this._varsLog.numLogMain[strNum] = 1;

						var str = 'line' + j;
						this._varsLog[str].push(rowData);

					}
				}
			}
		}
	},

	/**
	 *
	*/
	_checkLogTopId : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var strDate = 'd' + obj.numDate + '-' + i;
			if (this._varsLog.flagTop[strDate]) {
				if (obj.id == this._varsLog.flagTop[strDate].id) {
					return i;
				}
			}
		}

		return 'false';
	},

	/**
	 *
	*/
	_getLogVars : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == obj.vars.id) return obj.arr[i];
		}
	},

	/**
	 *
	*/
	_checkLogTop : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var strDate = 'd' + obj.numDate + '-' + i;
			if (!this._varsLog.flagTop[strDate]) {
				return i;
			}
		}
	},

	/**
	 *
	*/
	_setResizeListener : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			for (var j = 0; j < obj.arrLevel.length; j++) {
				var id = this.idSelf + 'BodyLineBox' + obj.arrLevel[j] + obj.arr[i];
				var ele = $(id);
				this.insListener.set({ bindAsEvent : 0, insCurrent : this, event : 'mouseover',
					strFunc : '_mouseoverResize', ele : ele,
					vars : { id : obj.arr[i], mainTime : this.insCurrent.varsVar.month.mainTime[i] } });
				this.insListener.set({ bindAsEvent : 0, insCurrent : this, event : 'mouseout',
					strFunc : '_mouseoutResize', ele : ele, vars : { id : obj.arr[i] } });
			}
		}
		this.insListener.set({ bindAsEvent : 1, insCurrent : this, event : 'mouseup',
			strFunc : '_mouseupResize', ele : document, vars : '' });
	},

	/**
	 *
	*/
	_varsResize : {},
	mousedownResize : function(evt,obj) {
		/*term only*/
		this._setResizeListener({
			arr      : this.insCurrent.varsVar.month.main,
			arrLevel : this._staticResize.arrayLevel
		});
		this._styleLogUpdate({
			arrMain : this.insCurrent.varsVar.month.main,
			arr     : this.vars.varsFold
		});
		this._styleLogDateUpdate({
			arrMain  : this.insCurrent.varsVar.month.mainTime,
			vars     : obj.vars,
			arrLevel : this._staticMove.arrayLevel
		});
		this._varsResize = {};
		this._varsResize = {
			flag         : 1,
			flagType     : obj.flagType,
			ele          : evt.element(),
			vars         : obj.vars,
			varsOver     : null,
			fixedTime    : this._checkResizeFixed({
				arr      : this.vars.varsFold,
				flagType : obj.flagType,
				vars     : obj.vars
			})
		};
	},

	/**
	 *
	*/
	_checkResizeFixed : function(obj)
	{
		var insCheck = new Code_Lib_CheckTime();
		var cut = obj.vars.vars.varsScheduleDetail;
		var objTargetStart = this.insRoot.insTimeZone.adjustDate({
			stamp : cut.term.stampStart * 1000
		});
		var objTargetEnd = this.insRoot.insTimeZone.adjustDate({
			stamp : cut.term.stampEnd*1000
		});
		var omit = this.insCurrent.varsVar.month.mainTime;
		var flag = insCheck.getTerm({
			stampStart     : objTargetStart.stamp,
			stampEnd       : objTargetEnd.stamp,
			stampWrapStart : omit[0].stamp,
			stampWrapEnd   : omit[omit.length - 1].stamp
		});
		if (obj.flagType == 'right') {
			if (flag == 'left') return omit[0];
			else if (flag == 'all') return objTargetStart;
			else if (flag == 'right') return objTargetStart;

		} else if (obj.flagType == 'left') {
			if (flag == 'right') return omit[omit.length-1];
			else if (flag == 'all') return objTargetEnd;
			else if (flag == 'left') return objTargetEnd;
		}
	},

	/**
	 *
	*/
	_mouseoverResize : function(obj)
	{
		if (!this._varsResize.flag) return;
		this._varsResize.varsOver = obj.mainTime;

		obj.arrLevel = this._staticResize.arrayLevel;
		if (this._varsResize.flagType == 'right') {
			var a = this._varsResize.fixedTime.numDate;
			var b = obj.mainTime.numDate;
			for (var i = a; i <= b; i++) {
				for (var j = 0; j < obj.arrLevel.length; j++) {
					var id = this.idSelf + 'BodyLineBox' + obj.arrLevel[j] + i;
					$(id).addClassName('codeLibScheduleMonthBodyLineBoxOverRange');
				}
			}
		} else if (this._varsResize.flagType == 'left') {
			var a = obj.mainTime.numDate;
			var b = this._varsResize.fixedTime.numDate;
			for (var i = b; i >= a; i--) {
				for (var j = 0; j < obj.arrLevel.length; j++) {
					var id = this.idSelf + 'BodyLineBox' + obj.arrLevel[j] + i;
					$(id).addClassName('codeLibScheduleMonthBodyLineBoxOverRange');
				}
			}
		}
	},

	/**
	 *
	*/
	_mouseoutResize : function(obj)
	{
		if (!this._varsResize.flag) return;
		this._varsResize.varsOver = null;
		obj.arrLevel = this._staticResize.arrayLevel;
		this._removeResize({
			arr      : this.insCurrent.varsVar.month.main,
			arrLevel : this._staticResize.arrayLevel
		});
	},

	/**
	 *
	*/
	_removeResize : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			for (var j = 0; j < obj.arrLevel.length; j++) {
				var id = this.idSelf + 'BodyLineBox' + obj.arrLevel[j] + obj.arr[i];
				var ele = $(id);
				ele.removeClassName('codeLibScheduleMonthBodyLineBoxOverRange');
			}
		}
	},

	/**
	 *
	*/
	_staticResize : {arrayLevel : ['top','middle','bottom']},
	_mouseupResize : function(evt,obj) {
		if (obj) evt.stop();
		else obj = evt;
		if (!this._varsResize.flag) return;
		this.insCurrent.allot({
			insCurrent : this.insCurrent.insCurrent,
			from       : '_mouseupResize',
			vars   : this._varsResize.vars,
			objTime    : {
				mousedown : this._varsResize.vars.mainTime,
				mouseup   : this._varsResize.varsOver
			}
		});
		this._varsResize = {};
		this._removeResize({
			arr      : this.insCurrent.varsVar.month.main,
			arrLevel : this._staticResize.arrayLevel,
			arrPoint : []
		});
		this.insCurrent.iniReload();
	},

	/**
	 *
	*/
	_setMoveListener : function(obj)
	{
		if (this.insCurrent.vars.varsStatus.flagMoveRangeUse) {
			for (var i = 0; i < obj.arr.length; i++) {
				for (var j = 0; j < obj.arrLevel.length; j++) {
					var id = this.idSelf + 'BodyLineBox' + obj.arrLevel[j] + obj.arr[i];
					var ele = $(id);
					this.insListener.set({ bindAsEvent : 0, insCurrent : this, event : 'mouseover',
						strFunc : '_mouseoverMove', ele : ele,
						vars : { id : obj.arr[i], mainTime : this.insCurrent.varsVar.month.mainTime[i] } });
					this.insListener.set({ bindAsEvent : 0, insCurrent : this, event : 'mouseout',
						strFunc : '_mouseoutMove', ele : ele, vars : { id : obj.arr[i] } });
				}
			}
		}
		this.insListener.set({ bindAsEvent : 1, insCurrent : this, event : 'mousemove',
			strFunc : '_mousemoveMove', ele : document, vars : '' });
		this.insListener.set({ bindAsEvent : 1, insCurrent : this, event : 'mouseup',
			strFunc : '_mouseupMove', ele : document, vars : '' });
	},

	/**
	 *
	*/
	_varsMove : {},
	mousedownMove : function(evt, obj)
	{
		this._setMoveListener({
			arr      : this.insCurrent.varsVar.month.main,
			arrLevel : this._staticMove.arrayLevel
		});
		if (this.insCurrent.vars.varsStatus.flagMoveRangeUse) {
			this._styleLogUpdate({
				arrMain : this.insCurrent.varsVar.month.main,
				arr     : this.vars.varsFold
			});
			this._styleLogDateUpdate({
				arrMain  : this.insCurrent.varsVar.month.mainTime,
				vars     : obj,
				arrLevel : this._staticMove.arrayLevel
			});
		}
		this._varsMove = {};
		var numDate = 0;
		if (obj.vars.varsScheduleDetail.flagType == 'Term') {
			numDate = Math.floor((obj.vars.varsScheduleDetail.term.stampEnd
					- obj.vars.varsScheduleDetail.term.stampStart) / 86400);
		}
		this._varsMove = {
			flag     : 1,
			ele      : evt.element(),
			vars     : obj,
			varsOver : null,
			eleNavi  : null,
			numDate  : numDate
		};
		this._setMoveNavi({
			vars : obj,
			evt  : evt
		});
		this.insCurrent.allot({
			insCurrent : this.insCurrent.insCurrent,
			from       : 'mousedownMove',
			vars       : obj.vars
		});
	},

	/**
	 *
	*/
	_mouseoverMove : function(obj)
	{
		if (!this._varsMove.flag) return;
		this._varsMove.varsOver = obj.mainTime;
		obj.arrLevel = this._staticMove.arrayLevel;
		obj.arr = this.insCurrent.varsVar.month.main;
		var cut = this._varsMove.vars.vars.varsScheduleDetail;
		if (cut.flagType == 'stamp') {
			for (var j = 0; j < obj.arrLevel.length; j++) {
				var id = this.idSelf + 'BodyLineBox' + obj.arrLevel[j] + obj.id;
				$(id).addClassName('codeLibScheduleMonthBodyLineBoxOverRange');
			}

		} else if (cut.flagType == 'Term') {
			var a = obj.mainTime.numDate;
			var b = obj.mainTime.numDate + this._varsMove.numDate;
			for (var i = a; i <= obj.arr.length; i++) {
				for (var j = 0; j < obj.arrLevel.length; j++) {
					var id = this.idSelf + 'BodyLineBox' + obj.arrLevel[j] + i;
					$(id).addClassName('codeLibScheduleMonthBodyLineBoxOverRange');
				}
				if (b==i) break;
			}
		}
	},

	/**
	 *
	*/
	_mouseoutMove : function(obj)
	{
		if (!this._varsMove.flag) return;
		this._varsMove.varsOver = null;
		obj.arrLevel = this._staticMove.arrayLevel;
		obj.arr = this.insCurrent.varsVar.month.main;
		var cut = this._varsMove.vars.vars.varsScheduleDetail;
		if (cut.flagType == 'stamp') {
			for (var j = 0; j < obj.arrLevel.length; j++) {
				var id = this.idSelf + 'BodyLineBox' + obj.arrLevel[j] + obj.id;
				$(id).removeClassName('codeLibScheduleMonthBodyLineBoxOverRange');
			}

		} else if (cut.flagType == 'Term') {
			this._removeMove({
				arr      : this.insCurrent.varsVar.month.main,
				arrLevel : this._staticMove.arrayLevel
			});
		}
	},

	/**
	 *
	*/
	_staticMove : {numNaviLeft : 15, numNaviTop : 5, arrayLevel : ['top','middle','bottom']},
	_setMoveNavi : function(obj)
	{
		var zIndex = this.insRoot.setZIndex();
		var ele = $(obj.vars.id).cloneNode(true);
		$(this.insRoot.vars.varsSystem.id.root).insert(ele);
		this._varsMove.eleNavi = ele;
		ele.addClassName('codeLibScheduleNavi');
		ele.setStyle({
			left   : (obj.evt.pointerX() + this._staticMove.numNaviLeft) + 'px',
			top    : (obj.evt.pointerY() + this._staticMove.numNaviTop) + 'px',
			zIndex : zIndex
		});
	},

	/**
	 *
	*/
	_mousemoveMove : function(evt, obj) {
		if (!this._varsMove.flag) return;
		if (obj) evt.stop();
		else obj = evt;
		this._varsMove.eleNavi.setStyle({
			top : (evt.pointerY() + this._staticMove.numNaviTop) + 'px',
			left : (evt.pointerX() + this._staticMove.numNaviLeft) + 'px'
		});
	},

	/**
	 *
	*/
	_mouseupMove : function(evt,obj) {
		if (obj) evt.stop();
		else obj = evt;
		if (!this._varsMove.flag) return;
		if (this.insCurrent.vars.varsStatus.flagMoveRangeUse) {
			this.insCurrent.allot({
				insCurrent : this.insCurrent.insCurrent,
				from       : '_mouseupMove',
				vars       : this._varsMove.vars,
				objTime    : {
					mousedown : this._varsMove.vars.mainTime,
					mouseup   : this._varsMove.varsOver
				}
			});
			this._removeMove({
				arr      : this.insCurrent.varsVar.month.main,
				arrLevel : this._staticMove.arrayLevel
			});

		} else {
			this.insCurrent.allot({
				insCurrent : this.insCurrent.insCurrent,
				from       : '_mouseupMove',
				vars       : this._varsMove.vars
			});
		}
		this._varsMove.eleNavi.remove();
		this._varsMove = {};
		this.insCurrent.iniReload();
	},

	/**
	 *
	*/
	_removeMove : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			for (var j = 0; j < obj.arrLevel.length; j++) {
				var id = this.idSelf + 'BodyLineBox' + obj.arrLevel[j] + obj.arr[i];
				var ele = $(id);
				ele.removeClassName('codeLibScheduleMonthBodyLineBoxOverRange');
			}
		}
	},

	/**
	 * BtnSelect
	*/
	iniBtnSelect : function()
	{
		if (!this.insCurrent.varsLogBtn) return;
		this._setBtnSelect({
			arr       : this.vars.varsFold,
			arrSelect : this.insCurrent.varsLogBtn
		});
		this.updateVars();
	},

	/**
	 *
	*/
	_setBtnSelect : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			var strLine = 'line' + i;
			var omit = this._varsBodyMainLine[strLine];
			if (!omit.length) continue;
			var str = 'line' + i;
			var array = this._varsLog[str];
			for (var j = 0; j < array.length; j++) {
				if (!array[j].vars.flagBtnUse) continue;
				var id = this.idSelf + 'BodyLineLog' + i + array[j].mainTime.numDate + array[j].vars.id;
				$(id).down('.codeLibScheduleTemplateTop', 0).removeClassName('codeLibScheduleSelect');
				$(id).down('.codeLibScheduleTemplateMiddle', 0).removeClassName('codeLibScheduleSelect');
				$(id).down('.codeLibScheduleTemplateBottom', 0).removeClassName('codeLibScheduleSelect');
				for (var k = 0; k < obj.arrSelect.length; k++) {
					if (array[j].vars.id == obj.arrSelect[k].id) {
						this._removeBtnSelectLoad({
							vars : array[j].vars,
							id   : id
						});
						this._removeBtnSelectBold({
							vars : array[j].vars,
							arr  : obj.arr
						});
						$(id).down('.codeLibScheduleTemplateTop', 0).addClassName('codeLibScheduleSelect');
						$(id).down('.codeLibScheduleTemplateMiddle', 0).addClassName('codeLibScheduleSelect');
						$(id).down('.codeLibScheduleTemplateBottom', 0).addClassName('codeLibScheduleSelect');
					}
				}
			}
		}
	},

	/**
	 *
	*/
	_removeBtnSelectLoad : function(obj)
	{
		if (obj.vars.strClassLoad == '') return;
		$(obj.id).down('.codeLibScheduleTemplateMiddleIcon', 0).removeClassName(obj.vars.strClassLoad);
		obj.vars.strClassLoad = '';
		$(obj.id).down('.codeLibScheduleTemplateMiddleIcon', 0).addClassName(obj.vars.strClass);
	},

	/**
	 *
	*/
	_removeBtnSelectBold : function(obj)
	{
		if (!this.insCurrent.vars.varsStatus.flagBoldUse || !obj.vars.flagBoldNow) return;
		obj.vars.flagBoldNow = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			var strLine = 'line' + i;
			var omit = this._varsBodyMainLine[strLine];
			if (!omit.length) continue;
			var str = 'line' + i;
			var array = this._varsLog[str];
			for (var j = 0; j < array.length; j++) {
				if (!array[j].vars.flagBtnUse || array[j].vars.id != obj.vars.id) continue;
				var id = this.idSelf + 'BodyLineLog' + i + array[j].mainTime.numDate + array[j].vars.id;
				if ($(id).down('.codeLibScheduleTemplateMiddleStrTitle', 0)) {
					var ele = $(id).down('.codeLibScheduleTemplateMiddleStrTitle', 0);
					ele.removeClassName('codeLibBaseFontBold');
				}
			}
		}
	},

	/**
	 * Cake
	*/
	_iniCake : function(obj)
	{
		this._getCake();
	},

	/**
	 *
	*/
	_getCakeVarsUpdate : function(obj)
	{
		obj.arr = this.vars.varsFold;
		for (var i = 0; i < obj.arr.length; i++) {
			var str = 'flagFoldNow' + i;
			obj.arr[i].flagFoldNow = obj.data[str];
		}
	},

	/**
	 *
	*/
	_setCakeVars : function()
	{
		var obj = {};
		obj.arr = this.vars.varsFold;
		var str;
		for (var i = 0; i < obj.arr.length; i++) {
			str = 'flagFoldNow' + i;
			this._varsCake[str] = obj.arr[i].flagFoldNow;
		}
	},

	/**
	 * Body
	*/
	_iniBody : function()
	{

		this._varBody();
		this._setBodyWrap();
		this._setBodyLine({
			arr     : this.vars.varsFold,
			arrPrev : this.insCurrent.varsVar.month.prev,
			arrMain : this.insCurrent.varsVar.month.main,
			arrNext : this.insCurrent.varsVar.month.next
		});
	},

	/**
	 *
	*/
	_varsBodyMainLine : {line0 : [], line1 : [], line2 : [], line3 : [], line4 : [], line5:[]},
	_getBodyHeight : function()
	{
		var array = this.eleInsert.style.height.split('px');

		return parseFloat(array[0]);
	},

	/**
	 *
	*/
	_varBody : function()
	{
		this.varsBody.boxHeight = Math.floor( (this._getBodyHeight() - this._staticGraduation.numSeparate * 6)
									/ this._staticBody.numBoxLine );
		this.varsBody.boxMiddleHeight = this.varsBody.boxHeight - this._staticBody.numHeight * 2;
	},

	/**
	 *
	*/
	varsBody : {boxHeight : 0, boxMiddleHeight : 0},
	_staticBody : {numDay : 7, numBoxLine : 6, numHeight : 16, numWidth : 16, numMargin : 5, numLevel : 3 },
	eleBodyWrap : null,
	_setBodyWrap : function(obj)
	{
		var id = this.idSelf + 'BodyWrap';
		var eleWrap = $(document.createElement('div'));
		eleWrap.addClassName('codeLibScheduleMonthBodyWrap');
		eleWrap.id = id;
		eleWrap.setStyle({
			width  : this._getGraduationWidth() + 'px',
			height : this._getBodyHeight() + 'px'
		});
		eleWrap.unselectable = 'on';
		eleWrap.addClassName('unselect');
		this.eleBodyWrap = eleWrap;
		this.eleInsert.insert(eleWrap);
	},

	/**
	 *
	*/
	_setBodyLineBoxSeparate : function(obj)
	{
		var eleSeparate = $(document.createElement('span'));
		eleSeparate.addClassName('codeLibScheduleMonthBodyLineBoxSeparate');
		eleSeparate.setStyle({
			height : obj.numHeight + 'px'
		});
		obj.ele.insert(eleSeparate);
	},

	/**
	 *
	*/
	_setBodyLineBoxTop : function(obj)
	{
		var ele = $(document.createElement('span'));
		if (obj.flagType == 'main') {
			ele.id = this.idSelf + 'BodyLineBoxTop' + obj.id;
			if (this.insCurrent.varsVar.month.flagDateNow == obj.id) ele.addClassName('codeLibScheduleNow');
		}
		ele.addClassName('codeLibBaseCursorDefault');
		ele.addClassName('codeLibScheduleMonthBodyLineBoxTop');
		if (!obj.flagFold) ele.addClassName('codeLibScheduleMonthBodyLineBox');
		ele.setStyle({
			width  : obj.numWidth + 'px',
			height : this._staticBody.numHeight + 'px'
		});
		if (obj.strHoliday) ele.title = obj.str + ' - ' + obj.strHoliday + ' - ';
		else ele.title = obj.str;

		if (obj.color == 'red') ele.addClassName('codeLibBaseFontRed');
		else if (obj.color == 'ccc') ele.addClassName('codeLibBaseFontCcc');
		obj.ele.insert(ele);

		ele.insert(obj.str);
		if (obj.flagDay) this._setBodyLineBoxSeparate({ numHeight : this._staticBody.numHeight, ele : obj.ele});
	},

	/**
	 *
	*/
	_setBodyLineBoxMiddle : function(obj) {

		var ele = $(document.createElement('span'));
		if (obj.flagType == 'main') {
			ele.id = this.idSelf + 'BodyLineBoxMiddle' + obj.id;
			if (this.insCurrent.varsVar.month.flagDateNow == obj.id) ele.addClassName('codeLibScheduleNow');
		}
		if (!obj.flagFold) ele.addClassName('codeLibScheduleMonthBodyLineBox');
		ele.addClassName('codeLibScheduleMonthBodyLineBoxMiddle');
		ele.setStyle({
			width  : obj.numWidth + 'px',
			height : this.varsBody.boxMiddleHeight + 'px'
		});
		if (obj.strHoliday) ele.title = obj.strHoliday;
		obj.ele.insert(ele);
		if (obj.flagDay) {
			this._setBodyLineBoxSeparate({numHeight : this.varsBody.boxMiddleHeight, ele : obj.ele});
		}
	},

	/**
	 *
	*/
	_setBodyLineBoxBottom : function(obj) {
		var ele = $(document.createElement('span'));
		if (obj.flagType == 'main') {
			ele.id = this.idSelf + 'BodyLineBoxBottom' + obj.id;
			if (this.insCurrent.varsVar.month.flagDateNow == obj.id) ele.addClassName('codeLibScheduleNow');
		}
		if (!obj.flagFold) ele.addClassName('codeLibScheduleMonthBodyLineBox');
		ele.addClassName('codeLibScheduleMonthBodyLineBoxBottom');
		ele.setStyle({
			width  : obj.numWidth + 'px',
			height : this._staticBody.numHeight + 'px'
		});
		obj.ele.insert(ele);
		if (obj.strHoliday) ele.title = obj.strHoliday;
		if (obj.flagDay) this._setBodyLineBoxSeparate({numHeight : this._staticBody.numHeight, ele : obj.ele});
	},

	/**
	 *
	*/
	_setBodyLine : function(obj) {

		var numBox = 1;
		var numPrev = 0;
		var numMain = 0;
		var numNext = 0;

		this._varsBodyMainLine = {
			line0 : [], line1 : [], line2 : [], line3 : [], line4 : [], line5:[]
		};

		for (var i = 0; i < obj.arr.length; i++) {
			var day = 0;
			var eleLineWrap = $(document.createElement('div'));
			eleLineWrap.addClassName('codeLibScheduleMonthBodyLineWrap');
			eleLineWrap.id = this.idSelf + 'BodyLineWrap' + i;
			this.eleBodyWrap.insert(eleLineWrap);

			var eleSeparate = $(document.createElement('div'));
			eleSeparate.addClassName('codeLibScheduleMonthBodyLineSeparate');
			this.eleBodyWrap.insert(eleSeparate);

			var eleLineTop = $(document.createElement('div'));
			eleLineTop.addClassName('codeLibScheduleMonthBodyLineTop');
			eleLineWrap.insert(eleLineTop);

			var eleLineMiddle = $(document.createElement('div'));
			eleLineMiddle.addClassName('codeLibScheduleMonthBodyLineMiddle');
			eleLineWrap.insert(eleLineMiddle);
			eleLineMiddle.setStyle({
				height : this.varsBody.boxMiddleHeight + 'px'
			});

			var eleLineBottom = $(document.createElement('div'));
			eleLineBottom.addClassName('codeLibScheduleMonthBodyLineBottom');
			eleLineWrap.insert(eleLineBottom);

			for (var k = 0; k < this._staticBody.numLevel; k++) {
				if (k == 0) {
					var eleBoxTop = $(document.createElement('span'));
					eleBoxTop.setStyle({
						width  : (this._staticBody.numMargin + this._staticBody.numWidth) + 'px',
						height : this._staticBody.numHeight + 'px'
					});
					eleLineTop.insert(eleBoxTop);
					this._setBodyLineBoxSeparate({
						flagDay : 1, numHeight : this._staticBody.numHeight, ele : eleLineTop
					});

					var eleFold = $(document.createElement('span'));
					eleFold.addClassName('codeLibScheduleMonthBodyLineFold');
					if (this.insCurrent.vars.varsStatus.flagFoldUse) {
						eleFold.addClassName('codeLibBaseCursorPointer');
						if (!obj.arr[i].flagFoldNow) eleFold.addClassName('codeLibScheduleFoldClose');
						else  eleFold.addClassName('codeLibScheduleFoldOpen');
						eleFold.addClassName('codeLibScheduleBlock');
					}
					eleFold.unselectable = 'on';
					eleFold.addClassName('unselect');
					eleBoxTop.insert(eleFold);

				} else if (k == 1) {
					this._setBodyLineBoxMiddle({
						flagType : 'fold', id : '', flagFold : 1, flagDay : 1,
						numWidth : (this._staticBody.numMargin + this._staticBody.numWidth), ele : eleLineMiddle
					});

				} else if (k == 2) {
					this._setBodyLineBoxBottom({
						flagType : 'fold', id : '', flagFold : 1, flagDay : 1,
						numWidth : (this._staticBody.numMargin + this._staticBody.numWidth), ele : eleLineBottom
					});
				}
			}

			var flag = 0;
			for (var j = numPrev; j < obj.arrPrev.length; j++, numBox++, day++, numPrev++) {
				if ((day/7) == 1) {
					flag = 1;
					break;
				}
				var flagDay = 0;
				if ((day/6) != 1) flagDay = 1;
				var str = obj.arrPrev[j];
				if (j == (obj.arrPrev.length - 1)) str = this.insCurrent.varsVar.month.strPrevEnd;
				for (var k = 0; k < this._staticBody.numLevel; k++) {
					if (k == 0) {
						this._setBodyLineBoxTop({
							flagType : 'prev', id : '', flagFold : 0, flagDay : flagDay,
							numWidth : this._varsGraduation.numWidth, ele : eleLineTop,
							str : str, color : 'ccc'
						});

					} else if (k == 1) {
						this._setBodyLineBoxMiddle({ flagType : 'prev', id : '', flagFold : 0, flagDay : flagDay,
							numWidth : this._varsGraduation.numWidth, ele : eleLineMiddle
						});

					} else if (k == 2) {
						this._setBodyLineBoxBottom({ flagType : 'prev', id : '', flagFold : 0, flagDay : flagDay,
							numWidth : this._varsGraduation.numWidth, ele : eleLineBottom
						});
					}
				}
			}

			for (var j = numMain; j < obj.arrMain.length; j++, numBox++, day++, numMain++) {
				if ((day/7) == 1) {
					flag = 1;
					break;
				}
				var flagDay = 0;
				if ((day/6) != 1) flagDay = 1;
				var str = obj.arrMain[j];
				if (j == 0) str = this.insCurrent.varsVar.month.strMainStart;
				else if (j == (obj.arrMain.length - 1)) str = this.insCurrent.varsVar.month.strMainEnd;

				var color = '', strHoliday = '';
				if (this.insCurrent.varsVar.month.flagActive[j]) {
					if (this.insCurrent.varsVar.month.flagSunday[j]) color = 'red';
					if (this.insCurrent.varsVar.month.flagHoliday[j]) {
						strHoliday = this.insCurrent.varsVar.month.flagHoliday[j].strTitle;
						color = 'red';
					}
					if (this.insCurrent.varsVar.month.flagHolidayTransfer[j]) {
						var strHoliday = this.insRoot.vars.varsSystem.status.strHoliday;
						strHoliday = this.insCurrent.varsLoad.varsHoliday[strHoliday].transfer;
						color = 'red';
					}
				}
				else color = 'ccc';
				for (var k = 0; k < this._staticBody.numLevel; k++) {
					if (k == 0) {
						this._setBodyLineBoxTop({
							flagType : 'main', id : obj.arrMain[j], flagFold : 0, flagDay : flagDay,
							numWidth : this._varsGraduation.numWidth, ele : eleLineTop, str : str,
							strHoliday : strHoliday, color : color
						});
					} else if (k == 1) {
						this._setBodyLineBoxMiddle({
							flagType : 'main', id : obj.arrMain[j], flagFold : 0, flagDay : flagDay,
							numWidth : this._varsGraduation.numWidth,  ele : eleLineMiddle,
							strHoliday : strHoliday
						});
					} else if (k == 2) {
						this._setBodyLineBoxBottom({
							flagType : 'main', id : obj.arrMain[j], flagFold : 0, flagDay : flagDay,
							numWidth : this._varsGraduation.numWidth,  ele : eleLineBottom,
							strHoliday : strHoliday
						});
					}
				}
				var strLine = 'line' + i;
				this._varsBodyMainLine[strLine].push(this.insCurrent.varsVar.month.mainTime[numMain]);
			}

			for (var j = numNext; j < obj.arrMain.length; j++, numBox++, day++, numNext++) {
				if ((day / 7) == 1) {
					flag = 1;
					break;
				}
				var flagDay = 0;
				if ((day / 6) != 1) flagDay = 1;
				var str = obj.arrNext[j];
				if (j == 0) str = this.insCurrent.varsVar.month.strNextStart;
				for (var k = 0; k < this._staticBody.numLevel; k++) {
					if (k == 0) {
						this._setBodyLineBoxTop({
							flagType : 'next', id : numBox,  flagFold : 0, flagDay : flagDay,
							numWidth : this._varsGraduation.numWidth, ele : eleLineTop, str : str, color : 'ccc'
						});
					} else if (k == 1) {
						this._setBodyLineBoxMiddle({
							flagType : 'next', id : '',  flagFold : 0, flagDay : flagDay,
							numWidth : this._varsGraduation.numWidth,  ele : eleLineMiddle
						});
					} else if (k == 2) {
						this._setBodyLineBoxBottom({ flagType : 'next', id : '',  flagFold : 0, flagDay : flagDay,
							numWidth : this._varsGraduation.numWidth,  ele : eleLineBottom
						});
					}
				}
			}
		}
	},

	/**
	 *
	*/
	_varsBodyLineHoliday : {flag : null},
	_checkBodyLineHoliday : function(obj)
	{
		for (var i = obj.num; i >= 0; i--) {
			if (obj.arrHoliday[i] && obj.arrSunday[i]) {
				this._varsBodyLineHoliday.flag = 1;
				return;
			}
			else if (!obj.arrHoliday[i]) return;
		}
	},

	/**
	 * Wrap
	*/
	removeWrap : function()
	{
		this.eleInsert.innerHTML = '';
		this.eleInsertGraduation.innerHTML = '';
		if (this.eleInsertBtnLeft) this.eleInsertBtnLeft.innerHTML = '';
		if (this.eleInsertBtnRight) this.eleInsertBtnRight.innerHTML = '';
	},

	/**
	 * Graduation
	*/
	_iniGraduation : function()
	{
		this._varGraduation();
		this._setGraduation({
			arr : this.insCurrent.varsLoad.varsWhole.week
		});
	},

	/**
	 *
	*/
	_getGraduationWidth : function()
	{
		var array = this.eleInsertGraduation.style.width.split('px');

		return parseFloat(array[0]) - this._staticGraduation.numBar;
	},

	/**
	 *
	*/
	_getGraduationHeight : function()
	{
		var array = this.eleInsertGraduation.style.height.split('px');
		return parseFloat(array[0]);
	},

	/**
	 *
	*/
	_varGraduation : function()
	{
		this._varsGraduation.numWidth = Math.floor( (
			this._getGraduationWidth()
			- this._staticGraduation.numPadding * 2 * 7
			- this._staticGraduation.numSeparate * 7
			- this._staticGraduation.numWidth
			- this._staticGraduation.numMargin
		) / 7 );
		this._varsGraduation.numWidthGraph = this._varsGraduation.numWidth
											+ this._staticGraduation.numPadding * 2;
	},

	/**
	 *
	*/
	_varsGraduation : {numWidth : 0, numWidthGraph : 0},
	_staticGraduation : {
		numBar : 17, numSeparate : 1, numPadding : 5,
		numMargin : 5, numWidth : 16, numHeight : 16
	},

	/**
	 *
	*/
	eleGraduationWrap : null,
	_setGraduation : function(obj) {

		var eleWrap = $(document.createElement('div'));
		eleWrap.addClassName('codeLibScheduleGraduationWrap');
		eleWrap.id = this.idSelf + 'GraduationWrap';
		eleWrap.setStyle({
			width  : this._getGraduationWidth() + 'px',
			height : this._getGraduationHeight() + 'px'
		});
		this.eleInsertGraduation.insert(eleWrap);
		this.eleGraduationWrap = eleWrap;

		/*fold*/
		var eleFold = $(document.createElement('span'));
		eleFold.addClassName('codeLibScheduleGraduationFold');
		if (this.insCurrent.vars.varsStatus.flagFoldUse) {
			eleFold.addClassName('codeLibBaseCursorPointer');
			if (!this.insCurrent.vars.varsStatus.flagFoldNow) {
				eleFold.addClassName('codeLibScheduleFoldClose');
			}
			else  eleFold.addClassName('codeLibScheduleFoldOpen');
			eleFold.addClassName('codeLibScheduleBlock');
		}
		eleFold.unselectable = 'on';
		eleFold.addClassName('unselect');
		eleWrap.insert(eleFold);

		var width = this._varsGraduation.numWidth;
		for (var i = 0; i < obj.arr.length; i++) {
			var ele = $(document.createElement('span'));
			ele.addClassName('codeLibScheduleGraduation');
			eleWrap.insert(ele);
			var eleDay = $(document.createElement('span'));
			eleDay.addClassName('codeLibScheduleMonthLineBox');
			eleDay.setStyle({
				textAlign    : 'right',
				paddingRight : '5px',
				width        : width + 'px',
				height       : this._getGraduationHeight() + 'px'
			});
			eleDay.addClassName('codeLibBaseCursorDefault');
			if (!i) eleDay.addClassName('codeLibBaseFontRed');
			eleDay.insert(obj.arr[i]);
			eleWrap.insert(eleDay);
		}
	}

});
{/literal}
