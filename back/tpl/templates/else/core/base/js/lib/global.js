{literal}
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Global = Class.create(Code_Lib_ExtLib, {
{/literal}
	varsLoad : {$varsLoad},
{literal}
	/**
	 *
	*/
	initialize : function(obj)
	{
		this._extAllot(obj);
		this._iniVars(obj);
		this._iniListener();
		this._iniArea();
		this._iniNavi();
		this._iniMove();
	},

	/**
	 *
	*/
	_iniVars : function(obj)
	{
		this._extVars(obj);
		this.vars.varsDetail = this._updateVars({
			arr       : this.vars.varsDetail,
			arrStatus : this.insRoot.vars.varsSystem.status.arrModule
		});
		this._iniCake();
		this.setCake();
	},

	/**
	 *
	*/
	_updateVars : function(obj)
	{
		var array = [];
		for (var i = 0; i < obj.arr.length; i++) {

			var id = obj.arr[i].id.toLowerCase();
			if (id.match(/^logout|base$/)) {
				array.push(obj.arr[i]);

			} else {
				if(!obj.arrStatus[id]) continue;
				if(!obj.arrStatus[id].flagUse) continue;
				array.push(obj.arr[i]);
			}
		}

		return array;
	},

	/**
	 * Cake
	*/
	_iniCake : function()
	{
		this._getCake();
	},

	/**
	 *
	*/
	_getCakeVarsUpdate : function(obj)
	{
		obj.arr = this.vars.varsDetail;
		var str;
		for (var i = 0; i < obj.arr.length; i++) {
			str = 'left' + obj.arr[i].id;
			if (obj.data[str] != undefined) {
				obj.arr[i].numLeft = obj.data[str];
			}
			str = 'top' + obj.arr[i].id;
			if (obj.data[str] != undefined) {
				obj.arr[i].numTop = obj.data[str];
			}
		}
	},

	/**
	 *
	*/
	_setCakeVars : function()
	{
		arr = this.vars.varsDetail;
		var str;
		for (var i = 0; i < arr.length; i++) {
			str = 'left' + arr[i].id;
			this._varsCake[str] = arr[i].numLeft;
			str = 'top' + arr[i].id;
			this._varsCake[str] = arr[i].numTop;
			str = 'zIndex' + arr[i].id;
			this._varsCake[str] = arr[i].numZIndex;
		}

	},

	/**
	 *
	*/
	insListener : null,
	_iniListener : function()
	{
		this._extListener();
	},

	/**
	 * Area
	*/
	_iniArea : function()
	{
		this._setArea();
		this._setAreaListener();
	},

	/**
	 *
	*/
	_setArea : function()
	{
		var eleArea = $(document.createElement('div'));
		eleArea.id = this.idSelf;
		eleArea.addClassName('codeLibGlobalArea');
		eleArea.unselecgloballe = 'on';
		eleArea.addClassName('unselect');
		$(this.insRoot.vars.varsSystem.id.root).insert(eleArea);
	},

	/**
	 *
	*/
	_setAreaListener : function(obj)
	{
		this.insListener.set({ bindAsEvent : 1, ele : $(this.idSelf), event : 'mousedown', insCurrent : this,
			strFunc : '_mousedownArea', vars : ''
		});
	},

	/**
	 *
	*/
	_mousedownArea : function(evt) {
		evt.stop();
		var arr = this.vars.varsDetail;
		for (var i = 0; i < arr.length; i++) {
			arr[i].numZIndex = this.insRoot.setZIndex();
			$(this.idSelf + arr[i].id + 'Wrap').setStyle({'zIndex' : arr[i].numZIndex});
		}
		this.setCake();
	},

	/**
	 * Navi
	*/
	_iniNavi : function()
	{
		this._setNavi({arr : this.vars.varsDetail});
	},

	/**
	 *
	*/
	_setNavi : function(obj)
	{
		var strCodeName = this.insRoot.vars.varsSystem.status.strCodeName + ' ' + this.varsLoad.strLogin;
		var eleTitleStrCodeName = $(document.createElement('div'));
		eleTitleStrCodeName.addClassName('codeLibGlobalNaviStrCodeName');
		eleTitleStrCodeName.insert(strCodeName);
		eleTitleStrCodeName.unselecgloballe='on';
		eleTitleStrCodeName.addClassName('unselect');
		$(this.insRoot.vars.varsSystem.id.root).insert(eleTitleStrCodeName);
		eleTitleStrCodeName.style.top = 10 + 'px';
		eleTitleStrCodeName.style.left = 10 + 'px';

		for (var i = 0; i < obj.arr.length; i++) {
			var eleWrap = $(document.createElement('div'));
			eleWrap.unselecgloballe='on';
			eleWrap.addClassName('unselect');
			eleWrap.addClassName('codeLibGlobalNaviWrap');
			eleWrap.id = this.idSelf + obj.arr[i].id + 'Wrap';
			eleWrap.style.top = obj.arr[i].numTop + 'px';
			eleWrap.style.left = obj.arr[i].numLeft + 'px';
			eleWrap.style.zIndex = obj.arr[i].numZIndex;

			$(this.insRoot.vars.varsSystem.id.root).insert(eleWrap);
			var ele = $(document.createElement('div'));

			ele.addClassName('codeLibGlobalNaviFloat');
			ele.addClassName('codeLibBaseCursorPointer');
			ele.title = obj.arr[i].strTitle;

			var eleTitle = $(document.createElement('span'));
			eleTitle.addClassName('codeLibGlobalNaviTitle');
			eleTitle.id = this.idSelf + obj.arr[i].id + '_title';
			eleTitle.addClassName('unselect');
			eleTitle.unselectable = 'on';
			eleTitle.addClassName('codeLibGlobalNaviFloat');

			var strTitle = obj.arr[i].strTitle;
			eleTitle.insert(strTitle);
			$(this.insRoot.vars.varsSystem.id.root).insert(eleTitle);
			var numWidth = eleTitle.offsetWidth;

			var eleTitleWrap = $(document.createElement('span'));
			eleTitleWrap.addClassName('unselect');
			eleTitleWrap.unselectable = 'on';
			eleTitleWrap.addClassName('codeLibGlobalNaviFloat');

			var eleTitleIdleLeft = $(document.createElement('div'));
			eleTitleIdleLeft.addClassName('unselect');
			eleTitleIdleLeft.unselectable = 'on';
			eleTitleIdleLeft.addClassName('codeLibGlobalNaviFloat');

			var eleTitleIdleRight = $(document.createElement('div'));
			eleTitleIdleRight.addClassName('unselect');
			eleTitleIdleRight.unselectable = 'on';
			eleTitleIdleRight.addClassName('codeLibGlobalNaviFloat');

			var eleImgWrap = $(document.createElement('div'));
			eleImgWrap.addClassName('unselect');
			eleImgWrap.unselectable = 'on';
			eleImgWrap.addClassName('codeLibGlobalNaviFloat');

			var eleImg = $(document.createElement('div'));
			eleImg.addClassName(obj.arr[i].strClass);
			eleImg.id = this.idSelf + obj.arr[i].id;
			eleImg.addClassName('codeLibGlobalNavi');
			eleImg.addClassName('unselect');
			eleImg.unselectable = 'on';
			eleImg.addClassName('codeLibGlobalNaviFloat');


			var eleImgIdleLeft = $(document.createElement('div'));
			eleImgIdleLeft.addClassName('unselect');
			eleImgIdleLeft.unselectable = 'on';
			eleImgIdleLeft.addClassName('codeLibGlobalNaviFloat');

			var eleImgIdleRight = $(document.createElement('div'));
			eleImgIdleRight.addClassName('unselect');
			eleImgIdleRight.unselectable = 'on';
			eleImgIdleRight.addClassName('codeLibGlobalNaviFloat');

			eleTitleWrap.insert(eleTitleIdleLeft);
			eleTitleWrap.insert(eleTitle);
			eleTitleWrap.insert(eleTitleIdleRight);

			eleImgWrap.insert(eleImgIdleLeft);
			eleImgWrap.insert(eleImg);
			eleImgWrap.insert(eleImgIdleRight);

			ele.insert(eleImgWrap);
			ele.insert(eleTitleWrap);

			var numHeightTitle = 10;
			if (obj.arr[i].id == 'Logout') {
				numHeightTitle = 20;
			}
			var numHeightImg = 24;

			eleTitleWrap.style.height = numHeightTitle + 'px';
			eleTitle.style.height = numHeightTitle + 'px';
			eleTitleIdleLeft.style.height = numHeightTitle + 'px';
			eleTitleIdleRight.style.height = numHeightTitle + 'px';

			eleImgWrap.style.height = numHeightImg + 'px';
			eleImg.style.height = numHeightImg + 'px';
			eleImgIdleLeft.style.height = numHeightImg + 'px';
			eleImgIdleRight.style.height = numHeightImg + 'px';

			var numBase = 24;
			var num = numBase - numWidth;
			var numIdleLeft = 0;
			var numIdleRight = 0;
			if (num > 0) {
				numIdleLeft = num / 2;
				numIdleRight = numBase - numWidth - numIdleLeft;

				ele.style.width = numBase + 'px';
				eleImgWrap.style.width = numBase + 'px';
				eleImg.style.width = numBase + 'px';

				eleTitleWrap.style.width = numBase + 'px';
				eleTitle.style.width = numWidth + 'px';
				eleTitleIdleLeft.style.width = numIdleLeft + 'px';
				eleTitleIdleRight.style.width = numIdleRight + 'px';
				eleWrap.style.width = (numWidth + 20) + 'px';

			} else if (num < 0) {
				numIdleLeft = Math.abs(num) / 2;
				numIdleRight = numWidth - numIdleLeft;

				ele.style.width = numWidth + 'px';
				eleImgWrap.style.width = numWidth + 'px';
				eleImg.style.width = numBase + 'px';
				eleImgIdleLeft.style.width = numIdleLeft + 'px';
				eleImgIdleRight.style.width = numIdleRight + 'px';

				eleTitleWrap.style.width = numWidth + 'px';
				eleTitle.style.width = numWidth + 'px';
				eleWrap.style.width = (numWidth + 20) + 'px';

			} else {
				ele.style.width = numBase + 'px';
				eleTitleWrap.style.width = numBase + 'px';
				eleTitle.style.width = numBase + 'px';

				eleImgWrap.style.width = numBase + 'px';
				eleImg.style.width = numBase + 'px';
				eleWrap.style.width = (numBase + 20) + 'px';
			}

			var eleList = null;
			if (obj.arr[i].id != 'Logout') {
				eleList = $(document.createElement('div'));
				eleList.addClassName('codeLibGlobalMenu');
				eleList.addClassName('codeLibBaseCursorPointer');
			}
			eleWrap.insert(ele);
			if (eleList) eleWrap.insert(eleList);
			this.insListener.set({ bindAsEvent : 1, insCurrent : this, event : 'dblclick',
				strFunc : '_dblclickNavi', ele : eleWrap, vars : { vars : obj.arr[i]}
			});
			this.insListener.set({ bindAsEvent : 1, insCurrent : this, event : 'mousedown',
				strFunc : '_mousedownNavi', ele : eleWrap, vars : { vars : obj.arr[i]}
			});
			this.insListener.set({ bindAsEvent : 0, insCurrent : this, event : 'mouseover',
				strFunc : '_mouseoverNavi', ele : eleWrap, vars : { vars : obj.arr[i]}
			});
			this.insListener.set({ bindAsEvent : 0, insCurrent : this, event : 'mouseout',
				strFunc : '_mouseoutNavi', ele : eleWrap, vars : { vars : obj.arr[i]}
			});
			new Draggable(eleWrap,{
				zIndex : 1000000000
			});
			this.insListener.set({ bindAsEvent : 1, insCurrent : this, event : 'mousedown',
				strFunc : '_mousedownMove', ele : eleWrap, vars : { vars : obj.arr[i]}
			});
			if (eleList) {
				this.insListener.set({ bindAsEvent : 1, insCurrent : this, event : 'mousedown', strFunc : '_mousedownMenu',
					ele : eleList, vars : {vars : obj.arr[i], ele : eleWrap}
				});
				this.insListener.set({ bindAsEvent : 0, insCurrent : this, event : 'mouseover', strFunc : '_mouseoverMenu',
					ele : eleList, vars : { vars : obj.arr[i], ele : eleList}
				});
				this.insListener.set({ bindAsEvent : 0, insCurrent : this, event : 'mouseout', strFunc : '_mouseoutMenu',
					ele : eleList, vars : { vars : obj.arr[i], ele : eleList}
				});
			}
		}
	},

	/**
		this.insGlobal.eventMenu({
			id : obj.insWindow.idWindow,
			strTitle : obj.insWindow.vars.strTitle
		});
	*/
	_varsMenu : [],
	eventMenu : function(obj)
	{
		var data = {
			id           : obj.id,
			strTitle     : obj.strTitle,
			flagCheckUse : 1,
			flagCheckNow : 1
		};
		this._varsMenu.push(data);
	},

	/**
		this.insGlobal.updateMenuVars({
			id           : obj.insWindow.idWindow,
			flagCheckUse : flagCheckUse,
			flagCheckNow : flagCheckNow
		});
	 */
	updateMenuVars : function(obj)
	{
		obj.arr = this._varsMenu;
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.id == obj.arr[i].id) {
				obj.arr[i].flagCheckUse = obj.flagCheckUse;
				obj.arr[i].flagCheckNow = obj.flagCheckNow;
			}
		}
	},

	/**
	 *
	*/
	_mousedownMenu : function(evt, obj)
	{
		evt.stop();
		var vars = this._getMenuVars(obj);
		this._setMenu({vars : vars});
	},

	/**
	 *
	*/
	_staticMenu : {numMargin : 24},
	_getMenuVars : function(obj)
	{
		var varsTmpl = (Object.toJSON(this.vars.tmplContext)).evalJSON();
		var idTarget = 'Window' + obj.vars.id;
		obj.arr = this._varsMenu;
		for (var i = 0; i < obj.arr.length; i++) {
			var re = new RegExp("^Window" + obj.vars.id);
			if (obj.arr[i].id.match(re)) {
				var dataDetail = (Object.toJSON(this.vars.tmplContext.tmplDetail)).evalJSON();
				dataDetail.id = obj.arr[i].id;
				if (obj.vars.strClassSmall) {
					dataDetail.strClass = obj.vars.strClassSmall;
				}
				dataDetail.vars.idTarget = obj.arr[i].id;
				dataDetail.strTitle = obj.arr[i].strTitle;
				dataDetail.flagCheckUse = obj.arr[i].flagCheckUse;
				dataDetail.vars.flagCheckUse = obj.arr[i].flagCheckUse;
				dataDetail.flagCheckNow = obj.arr[i].flagCheckNow;
				dataDetail.vars.flagCheckNow = obj.arr[i].flagCheckNow;
				varsTmpl.varsDetail.push(dataDetail);
			}
		}
		varsTmpl.varsStatus.numTop = obj.ele.offsetTop;
		varsTmpl.varsStatus.numLeft = obj.ele.offsetLeft + this._staticMenu.numMargin;

		return varsTmpl;
	},

	/**
	 *
	*/
	insMenu : null,
	_setMenu : function(obj)
	{
		this.insMenu = new Code_Lib_Context({
			insRoot    : this.insRoot,
			eleInsert  : $(this.insRoot.vars.varsSystem.id.root),
			insCurrent : this,
			idSelf     : this.idSelf + 'Menu',
			allot      : this._getMenuAllot(),
			vars       : obj.vars
		});
	},

	/**
	 *
	*/
	_getMenuAllot : function()
	{
		var allot = function(obj) {
			var insCurrent = obj.insCurrent;
			if (obj.from == '_mousedownLine') {
				insCurrent.allot({
					insCurrent : insCurrent.insCurrent,
					from       : '_mousedownLine',
					vars       : {
						idTarget : obj.vars.vars.idTarget,
						flagCheckUse : obj.vars.vars.flagCheckUse,
						flagCheckNow : obj.vars.vars.flagCheckNow
					}
				});
			}
		};

		return allot;
	},

	/**
	 *
	*/
	_mouseoverMenu : function(obj)
	{
		obj.ele.addClassName('codeLibGlobalMenuOver');
		$(this.idSelf + obj.vars.id).addClassName(obj.vars.strClassOver);
	},

	/**
	 *
	*/
	_mouseoutMenu : function(obj)
	{
		obj.ele.removeClassName('codeLibGlobalMenuOver');
		$(this.idSelf + obj.vars.id).removeClassName(obj.vars.strClassOver);
	},

	/**
	 *
	*/
	_mousedownNavi : function(evt, obj) {
		obj.vars.numZIndex = this.insRoot.setZIndex();
		$(this.idSelf + obj.vars.id + 'Wrap').setStyle({ 'zIndex' : obj.vars.numZIndex });
	},

	/**
	 *
	*/
	_dblclickNavi : function (evt, obj) {
		evt.stop();
		var flag = this.allot({
			insCurrent : this.insCurrent,
			from       : 'dblclickNavi',
			vars       : obj.vars
		});
	},

	/**
	 *
	*/
	_mouseoverNavi : function(obj)
	{
		$(this.idSelf + obj.vars.id).addClassName(obj.vars.strClassOver);
		$(this.idSelf + obj.vars.id + '_title').addClassName('codeLibGlobalNaviTitleOver');
	},

	/**
	 *
	*/
	_mouseoutNavi : function(obj)
	{
		$(this.idSelf + obj.vars.id).removeClassName(obj.vars.strClassOver);
		$(this.idSelf + obj.vars.id + '_title').removeClassName('codeLibGlobalNaviTitleOver');
	},

	/**
	 * BasePortal
	*/
	eventBasePortal : function(obj)
	{
		var vars = this._getVars({
			id  : obj.idTarget,
			arr : this.vars.varsDetail
		});
		if (!vars.flagCheckNow) {
			this._varNaviUpdate({
				vars : vars
			});
			this.setCake();
		}
	},

	/**
	 *
	*/
	_getVars : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if(obj.arr[i].id == obj.id) return obj.arr[i];
		}
	},

	/**
	 * Move
	*/
	_staticMove : {block : 34},
	_iniMove : function()
	{
		this._setMoveListener();
	},

	/**
	 *
	*/
	_setMoveListener : function(obj)
	{
		this.insListener.set({ bindAsEvent : 1, insCurrent : this, event : 'mouseup',
			strFunc : '_mouseupMove', ele : document, vars : ''
		});
	},

	/**
	 *
	*/
	_varsMove : {},
	_mousedownMove : function(evt, obj) {
		this._varsMove = {};
		this._varsMove = {
			flag : 1,
			vars : obj.vars
		};
		evt.stop();
	},

	/**
	 *
	*/
	_mouseupMove : function(evt, obj) {
		if(!this._varsMove.flag) return;
		var viewSize = document.viewport.getDimensions();
		var topMax = viewSize.height - $(this.idSelf + this._varsMove.vars.id + 'Wrap').offsetHeight;
		var leftMax = viewSize.width - $(this.idSelf + this._varsMove.vars.id + 'Wrap').offsetWidth;
		var numTop = 0;
		var numLeft = 0;

		if($(this.idSelf + this._varsMove.vars.id + 'Wrap').offsetTop <= 0) numTop = 0;
		else if($(this.idSelf + this._varsMove.vars.id + 'Wrap').offsetTop > topMax) numTop = topMax;
		else numTop = $(this.idSelf + this._varsMove.vars.id + 'Wrap').offsetTop;

		$(this.idSelf + this._varsMove.vars.id + 'Wrap').style.top = numTop + 'px';

		if($(this.idSelf + this._varsMove.vars.id + 'Wrap').offsetLeft <= 0) numLeft = 0;
		else if($(this.idSelf + this._varsMove.vars.id + 'Wrap').offsetLeft > leftMax) numLeft = leftMax;
		else numLeft =$(this.idSelf + this._varsMove.vars.id + 'Wrap').offsetLeft;

		$(this.idSelf + this._varsMove.vars.id + 'Wrap').style.left = numLeft + 'px';

		this._varsMove.vars.numTop = numTop;
		this._varsMove.vars.numLeft = numLeft;

		this.setCake();
		evt.stop();
		this._varsMove = {};
	}
});
{/literal}