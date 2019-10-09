<?php /* Smarty version 3.1.24, created on 2016-08-18 12:50:21
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/btnTool.js" */ ?>
<?php
/*%%SmartyHeaderCode:144112187857b5af0d743475_42013909%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1280fd49797c9b72cc479fabb5836ca743fcb4f9' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/btnTool.js',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '144112187857b5af0d743475_42013909',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b5af0d78e408_14895165',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b5af0d78e408_14895165')) {
function content_57b5af0d78e408_14895165 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '144112187857b5af0d743475_42013909';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_BtnTool = Class.create(Code_Lib_ExtLib,
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
		this._iniTemplate();
	},

	/**
	 *
	*/
	iniReload : function()
	{
		this.insListener.stop();
		this.removeWrap();
		this._iniWrap();
		this._iniTemplate();
	},

	/**
	 * Listener
	*/
	_iniListener : function()
	{
		this._extListener();
	},

	/**
	 * Vars
	*/
	_iniVars : function(obj)
	{
		this._extVars(obj);
	},

	/**
	 * Wrap
	*/
	_iniWrap : function()
	{
		this._extWrap();
	},

	/**
	 *
	*/
	removeWrap : function()
	{
		this.eleWrap.remove();
	},

	/**
	 * Template
	*/
	_iniTemplate : function()
	{
		if(!this.vars.varsDetail) return;

		this._setTemplate({
			arr : this.vars.varsDetail
		});
	},

	/**
	 *
	*/
	_setTemplate : function(obj)
	{
		for (var j = 0; j < obj.arr.length; j++) {
			if (!obj.arr[j].flagUse) continue;
			var ele = $(document.createElement('span'));
			ele.id = this.idSelf + obj.arr[j].id;
			ele.addClassName('codeLibBtnToolImg');
			ele.title = obj.arr[j].strTitle;

			var eleTitle = $(document.createElement('span'));
			eleTitle.addClassName('codeLibBtnToolImgTitle');
			eleTitle.id = this.idSelf + obj.arr[j].id + '_title';
			eleTitle.addClassName('unselect');
			eleTitle.unselectable = 'on';
			eleTitle.insert(obj.arr[j].strTitle);
			$(this.insRoot.vars.varsSystem.id.root).insert(eleTitle);
			var numWidth = eleTitle.offsetWidth;

			var eleTitleWrap = $(document.createElement('span'));
			eleTitleWrap.addClassName('unselect');
			eleTitleWrap.unselectable = 'on';

			var eleTitleIdleLeft = $(document.createElement('span'));
			eleTitleIdleLeft.addClassName('unselect');
			eleTitleIdleLeft.unselectable = 'on';

			var eleTitleIdleRight = $(document.createElement('span'));
			eleTitleIdleRight.addClassName('unselect');
			eleTitleIdleRight.unselectable = 'on';

			var eleImgWrap = $(document.createElement('span'));
			eleImgWrap.addClassName('unselect');
			eleImgWrap.unselectable = 'on';

			var eleImg = $(document.createElement('span'));
			eleImg.addClassName('unselect');
			eleImg.unselectable = 'on';
			eleImg.id = this.idSelf + obj.arr[j].id + '_img';

			var eleImgIdleLeft = $(document.createElement('span'));
			eleImgIdleLeft.addClassName('unselect');
			eleImgIdleLeft.unselectable = 'on';

			var eleImgIdleRight = $(document.createElement('span'));
			eleImgIdleRight.addClassName('unselect');
			eleImgIdleRight.unselectable = 'on';

			eleTitleWrap.insert(eleTitleIdleLeft);
			eleTitleWrap.insert(eleTitle);
			eleTitleWrap.insert(eleTitleIdleRight);

			eleImgWrap.insert(eleImgIdleLeft);
			eleImgWrap.insert(eleImg);
			eleImgWrap.insert(eleImgIdleRight);

			ele.insert(eleImgWrap);
			ele.insert(eleTitleWrap);

			var numHeightTitle = 10;
			var numHeightImg = 20;

			eleTitleWrap.style.height = numHeightTitle + 'px';
			eleTitle.style.height = numHeightTitle + 'px';
			eleTitleIdleLeft.style.height = numHeightTitle + 'px';
			eleTitleIdleRight.style.height = numHeightTitle + 'px';

			eleImgWrap.style.height = numHeightImg + 'px';
			eleImg.style.height = numHeightImg + 'px';
			eleImgIdleLeft.style.height = numHeightImg + 'px';
			eleImgIdleRight.style.height = numHeightImg + 'px';

			var numBase = 30;
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

			} else {
				ele.style.width = numBase + 'px';
				eleTitleWrap.style.width = numBase + 'px';
				eleTitle.style.width = numBase + 'px';

				eleImgWrap.style.width = numBase + 'px';
				eleImg.style.width = numBase + 'px';
			}

			var eleList = null;
			if (obj.arr[j].flagNow) {
				eleImg.addClassName(obj.arr[j].strClass);
				ele.addClassName('codeLibBaseCursorPointer');
				if (obj.arr[j].varsContext) {
					eleList = $(document.createElement('span'));
					eleList.addClassName('codeLibBtnImgMenu');
					eleList.addClassName('codeLibBaseCursorPointer');
				}

			} else {
				eleImg.addClassName(obj.arr[j].strClassNoactive);
				eleTitle.addClassName('codeLibBtnToolNoactiveTitle');
				ele.addClassName('codeLibBtnToolNoactive');
				ele.addClassName('codeLibBaseCursorDefault');
			}

			this._setTemplateListener({
				ele     : ele,
				eleList : eleList,
				vars    : obj.arr[j]
			});
			this.eleWrap.insert(ele);
			if (eleList) this.eleWrap.insert(eleList);
		}
	},

	/**
	 *
	*/
	_setTemplateListener : function(obj)
	{
		this.insListener.set({ bindAsEvent : 1, insCurrent : this, event : 'mousedown', strFunc : '_mousedownNavi',
			ele : obj.ele, vars : {vars : obj.vars}
		});
		this.insListener.set({ bindAsEvent : 0, insCurrent : this, event : 'mouseover', strFunc : '_mouseoverNavi',
			ele : obj.ele, vars : { vars : obj.vars}
		});
		this.insListener.set({ bindAsEvent : 0, insCurrent : this, event : 'mouseout', strFunc : '_mouseoutNavi',
			ele : obj.ele, vars : { vars : obj.vars}
		});
		if (obj.eleList) {
			this.insListener.set({ bindAsEvent : 1, insCurrent : this, event : 'mousedown', strFunc : '_mousedownMenu',
				ele : obj.eleList, vars : {vars : obj.vars, ele : obj.eleList}
			});
			this.insListener.set({ bindAsEvent : 0, insCurrent : this, event : 'mouseover', strFunc : '_mouseoverMenu',
				ele : obj.eleList, vars : { vars : obj.vars, ele : obj.eleList}
			});
			this.insListener.set({ bindAsEvent : 0, insCurrent : this, event : 'mouseout', strFunc : '_mouseoutMenu',
				ele : obj.eleList, vars : { vars : obj.vars, ele : obj.eleList}
			});
		}
	},

	/**
	 * Navi
	*/
	_mousedownNavi : function(evt, obj)
	{
		evt.stop();
		if (obj.vars.flagNow) {
			this.allot({
				insCurrent : this.insCurrent,
				from       : '_mousedownNavi',
				vars       : obj.vars
			});
		}
	},

	/**
	 *
	*/
	_mousedownMenu : function(evt, obj)
	{
		evt.stop();
		if (obj.vars.flagNow) {
			obj.flagNow = this.allot({
				insCurrent : this.insCurrent,
				from       : '_mousedownMenu',
				vars       : obj.vars
			});
			this._setMenuVars(obj);
			this._setMenu(obj);
		}
	},

	/**
	 *
	*/
	_setMenuVars : function(obj)
	{
		var cut = obj.vars.varsContext;
		cut.varsStatus.flagNow = obj.flagNow;
		obj.arr = cut.varsDetail;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagCheckUse) continue;
			obj.arr[i].flagCheckNow = 0;
			if (obj.arr[i].vars.idTarget == cut.varsStatus.flagNow) {
				obj.arr[i].flagCheckNow = 1;
			}
		}
		cut.varsStatus.numTop = $(this.idSelf).up('.codeLibWindow', 0).offsetTop + obj.ele.offsetTop;
		cut.varsStatus.numLeft = $(this.idSelf).up('.codeLibWindow', 0).offsetLeft + obj.ele.offsetLeft;
	},

	/**
	 *
	*/
	insMenu : null,
	_setMenu : function(obj)
	{
		this._updateMenuVars({
			arr  : obj.vars.varsContext.varsDetail,
			vars : obj.vars
		});

		this.insMenu = new Code_Lib_Context({
			insRoot    : this.insRoot,
			eleInsert  : $(this.insRoot.vars.varsSystem.id.root),
			insCurrent : this.insSelf,
			idSelf     : this.idSelf + 'Menu',
			allot      : this._getMenuAllot(),
			vars       : obj.vars.varsContext
		});
	},

	/**
	 *
	*/
	_updateMenuVars : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			obj.arr[i].vars.idParent = obj.vars.id;
		}
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
					vars       : obj.vars.vars.idTarget,
					varsTarget : obj.vars.vars.idParent
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
		if (obj.vars.flagNow) {
			if (obj.vars.varsContext) {
				var flagNow = this.allot({
					insCurrent : this.insCurrent,
					from       : '_mousedownMenu',
					vars       : obj.vars
				});
				var strTitle = '';
				for (var k = 0; k < obj.vars.varsContext.varsDetail.length; k++) {
					if (obj.vars.varsContext.varsDetail[k].vars.idTarget == flagNow) {
						strTitle = obj.vars.varsContext.varsDetail[k].strTitle;
					}
				}
				obj.ele.title = strTitle;
			}
			obj.ele.addClassName('codeLibBtnImgMenuOver');
			$(this.idSelf + obj.vars.id + '_img').addClassName(obj.vars.strClassOver);
			$(this.idSelf + obj.vars.id + '_title').addClassName('codeLibBtnToolOverTitle');
		}
	},

	/**
	 *
	*/
	_mouseoutMenu : function(obj)
	{
		if (obj.vars.flagNow) {
			obj.ele.removeClassName('codeLibBtnImgMenuOver');
			$(this.idSelf + obj.vars.id + '_img').removeClassName(obj.vars.strClassOver);
			$(this.idSelf + obj.vars.id + '_title').removeClassName('codeLibBtnToolOverTitle');
		}
	},

	/**
	 *
	*/
	_mouseoverNavi : function(obj)
	{
		if (obj.vars.flagNow) {
			$(this.idSelf + obj.vars.id + '_img').addClassName(obj.vars.strClassOver);
			$(this.idSelf + obj.vars.id + '_title').addClassName('codeLibBtnToolOverTitle');
		}
	},

	/**
	 *
	*/
	_mouseoutNavi : function(obj)
	{
		if (obj.vars.flagNow) {
			$(this.idSelf + obj.vars.id + '_img').removeClassName(obj.vars.strClassOver);
			$(this.idSelf + obj.vars.id + '_title').removeClassName('codeLibBtnToolOverTitle');
		}
	}
});

<?php }
}
?>