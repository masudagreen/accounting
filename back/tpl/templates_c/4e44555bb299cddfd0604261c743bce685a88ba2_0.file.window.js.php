<?php /* Smarty version 3.1.24, created on 2022-08-13 00:23:38
         compiled from "/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/window.js" */ ?>
<?php
/*%%SmartyHeaderCode:1693986462f6ef0a8b7de1_78706018%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4e44555bb299cddfd0604261c743bce685a88ba2' => 
    array (
      0 => '/var/www/html/accounting/back/tpl/templates/else/core/base/js/lib/window.js',
      1 => 1374740336,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1693986462f6ef0a8b7de1_78706018',
  'variables' => 
  array (
    'varsLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_62f6ef0a921632_58361554',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_62f6ef0a921632_58361554')) {
function content_62f6ef0a921632_58361554 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '1693986462f6ef0a8b7de1_78706018';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Window = Class.create(Code_Lib_ExtLib,
{

	varsLoad : <?php echo $_smarty_tpl->tpl_vars['varsLoad']->value;?>
,


	/**
	 *
	*/
	iniLoad : function(obj)
	{
		this._extAllot(obj);
		this._iniVars(obj);
		this._iniListener();
		this._iniLock();
		this._iniWrap();
		this._iniTemplate();
		this._iniCursor();
		this._iniZIndex();
		this._iniMove();
		this._iniHide();
		this._iniRemove();
		this._iniFold();
		this._iniSkeleton();
		this._iniResize();
		this._iniBoot();
		this._iniCover();
		this._iniMenu();

	},

	/**
	 * Menu
	*/
	_iniMenu : function()
	{
		if (!this.vars.flagMenuUse) return;
		this._setMenu();
		this._updateMenuHide();
	},

	_setMenu : function()
	{
		this.insRoot.collectGlobalMenu({insWindow : this});
	},

	_updateMenuHide : function()
	{
		if (!this.vars.flagMenuUse) return;
		this.insRoot.updateGlobalMenu({insWindow : this});
	},

	/**
	 *
	*/
	eleInsert : null, vars : null,
	_iniVars : function(obj)
	{
		this._extVars(obj);
		this.idWindow = this.idSelf + this.vars.id;
		this._iniCake();
		this.setCake();
		this.allot({
			insCurrent : this.insCurrent,
			from       : '_iniVars'
		});
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
		var str = 'numLeft';
		this.vars.numLeft = obj.data[str];
		str = 'numTop';
		this.vars.numTop = obj.data[str];
		str = 'numHeight';
		this.vars.numHeight = obj.data[str];
		str = 'numWidth';
		this.vars.numWidth = obj.data[str];
		str = 'flagHideNow';
		this.vars.flagHideNow = obj.data[str];
		str = 'flagFoldNow';
		this.vars.flagFoldNow = obj.data[str];
	},

	/**
	 *
	*/
	_setCakeVars : function()
	{
		if (!this.vars.flagCakeUse) return;
		var str = 'numLeft';
		this._varsCake[str] = this.vars.numLeft;
		str = 'numTop';
		this._varsCake[str] = this.vars.numTop;
		str = 'numWidth';
		this._varsCake[str] = this.vars.numWidth;
		str = 'numHeight';
		this._varsCake[str] = this.vars.numHeight;
		str = 'flagHideNow';
		this._varsCake[str] = this.vars.flagHideNow;
		str = 'flagFoldNow';
		this._varsCake[str] = this.vars.flagFoldNow;
		str = 'numZIndex';
		this._varsCake[str] = this.vars.numZIndex;
		str = 'id';
		this._varsCake[str] = this.vars.id;
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
	 *
	*/
	_iniCover : function()
	{
		if (!this.vars.flagCoverUse) return;
		this._setCover();
		this._setCoverListener();
	},

	/**
	 *
	*/
	_setCover : function(obj)
	{
		$(this.idWindow).down('.codeLibWindowHeaderBottomMiddle', 1).addClassName('codeLibBaseCursorDefault');
	},

	/**
	 *
	*/
	_setCoverListener : function(obj)
	{
		this.insListener.set({
			flagType15 : 1, bindAsEvent : 0, insCurrent : this, event : 'resize',
			strFunc : '_resizeCover', ele : window, vars : ''
		});
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'mousedown',
			strFunc : '_mousedownCover', ele : $(this.idWindow).down('.codeLibWindowNaviCoverMax', 0),
			vars : ''
		});
		this.insListener.set({
			bindAsEvent : 0, insCurrent : this, event : 'mouseover',
			strFunc : '_mouseoverCoverMax', ele : $(this.idWindow).down('.codeLibWindowNaviCoverMax', 0),
			vars : ''
		});
		this.insListener.set({
			bindAsEvent : 0, insCurrent : this, event : 'mouseout',
			strFunc : '_mouseoutCoverMax', ele : $(this.idWindow).down('.codeLibWindowNaviCoverMax', 0),
			vars : ''
		});
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'mousedown',
			strFunc : '_mousedownCover', ele : $(this.idWindow).down('.codeLibWindowNaviCoverNormal', 0),
			vars : ''
		});
		this.insListener.set({
			bindAsEvent : 0, insCurrent : this, event : 'mouseover',
			strFunc : '_mouseoverCoverNormal', ele : $(this.idWindow).down('.codeLibWindowNaviCoverNormal', 0),
			vars : ''
		});
		this.insListener.set({
			bindAsEvent : 0, insCurrent : this, event : 'mouseout',
			strFunc : '_mouseoutCoverNormal', ele : $(this.idWindow).down('.codeLibWindowNaviCoverNormal', 0),
			vars : ''
		});
		this.insListener.set({bindAsEvent : 1, insCurrent : this, event : 'dblclick',
			strFunc : '_mousedownCover', ele : $(this.idWindow).down('.codeLibWindowHeaderBottomMiddle', 0),
			vars : ''
		});
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'dblclick',
			strFunc : '_mousedownCover', ele : $(this.idWindow).down('.codeLibWindowHeaderBottomMiddle', 1),
			vars : ''
		});
	},

	/**
	 *
	*/
	_staticCover : {headerWidth : 12, headerHeight : 46, fooderHeight : 16},
	_varsCover : null,
	_mousedownCover : function(evt)
	{
		evt.stop();
		if (this.vars.flagFoldNow) return;
		this._mousedownZIndex();
		if (!this.vars.flagCoverNow) {
			this.vars.flagCoverNow = 1;
			this._varsCover = {
				numLeft           : $(this.idWindow).offsetLeft,
				numTop            : $(this.idWindow).offsetTop,
				posVarsWindow : this.vars,
				varsWindow    : {
					id            : this.vars.id,
					numWidthMin   : this.vars.numWidthMin,
					numHeightMin  : this.vars.numHeightMin,
					flagRemoveUse : this.vars.flagRemoveUse,
					flagHideUse   : this.vars.flagHideUse,
					flagCoverUse  : this.vars.flagCoverUse,
					flagSwitchUse : this.vars.flagSwitchUse
				}
			};
			var numWidth = (document.viewport.getDimensions()).width
						- this._staticCover.headerWidth * 2;
			var numHeight = (document.viewport.getDimensions()).height
						- this._staticCover.headerHeight
						- this._staticCover.fooderHeight;
			this._varsCover.varsWindow.numWidth = (numWidth > this.vars.numWidthMin)?
														  numWidth
														: this.vars.numWidthMin;
			this._varsCover.varsWindow.numHeight = (numHeight > this.vars.numHeightMin)?
														  numHeight
														: this.vars.numHeightMin;
			this._setResizeWindowSize({
				numLeft : 0,
				numTop  : 0,
				vars    : this._varsCover.varsWindow,
				arr     : this._staticResize
			});
			this._removeCursor({arr : this._staticResize});
			$(this.idWindow).addClassName('codeLibWindowCover');
			$(this.idWindow).down('.codeLibWindowHeaderBottomMiddle', 0).hide();
			$(this.idWindow).down('.codeLibWindowHeaderBottomMiddle', 1).show();
		} else {
			this.vars.flagCoverNow = 0;
			$(this.idWindow).removeClassName('codeLibWindowCover');
			this._setResizeWindowSize({
				numLeft  : this._varsCover.numLeft,
				numTop   : this._varsCover.numTop,
				vars     : this._varsCover.posVarsWindow,
				arr      : this._staticResize
			});
			this._setCursor({arr : this._staticResize});
			this._varsCover = null;
			$(this.idWindow).down('.codeLibWindowHeaderBottomMiddle', 0).show();
			$(this.idWindow).down('.codeLibWindowHeaderBottomMiddle', 1).hide();
		}
		this.allot({
			from       : '_mousedownCover',
			insCurrent : this.insSelf
		});
	},



	/**
	 *
	*/
	_resizeCover : function()
	{
		if (!this._varsCover) return;
		var numWidth = (document.viewport.getDimensions()).width
					- this._staticCover.headerWidth * 2;
		var numHeight = (document.viewport.getDimensions()).height
					- this._staticCover.headerHeight
					- this._staticCover.fooderHeight;
		this._varsCover.varsWindow.numWidth = (numWidth > this._varsCover.varsWindow.numWidthMin)?
													   numWidth
													: this._varsCover.varsWindow.numWidthMin;
		this._varsCover.varsWindow.numHeight = (numHeight > this._varsCover.varsWindow.numHeightMin)?
													  numHeight
													: this._varsCover.varsWindow.numHeightMin;
		this._setResizeWindowSize({
			numLeft  : 0,
			numTop   : 0,
			vars     : this._varsCover.varsWindow,
			arr      : this._staticResize
		});
		this.allot({
			from       : '_resizeCover',
			insCurrent : this.insSelf
		});
	},

	/**
	 *
	*/
	_mouseoverCoverMax : function(obj)
	{
		var ele = $(this.idWindow).down('.codeLibWindowNaviCoverMax', 0);
		ele.addClassName('codeLibWindowNaviCoverMaxOver');
	},

	/**
	 *
	*/
	_mouseoutCoverMax : function(obj)
	{
		var ele = $(this.idWindow).down('.codeLibWindowNaviCoverMax', 0);
		ele.removeClassName('codeLibWindowNaviCoverMaxOver');
	},

	/**
	 *
	*/
	_mouseoverCoverNormal : function(obj)
	{
		var ele = $(this.idWindow).down('.codeLibWindowNaviCoverNormal', 0);
		ele.addClassName('codeLibWindowNaviCoverNormalOver');
	},

	/**
	 *
	*/
	_mouseoutCoverNormal : function(obj)
	{
		var ele = $(this.idWindow).down('.codeLibWindowNaviCoverNormal', 0);
		ele.removeClassName('codeLibWindowNaviCoverNormalOver');
	},

	/**
	 * Resize
	*/
	_iniResize : function()
	{
		if (!this.vars.flagResizeUse) return;
		this._setResizeListener({arr : this._staticResize});
	},

	/**
	 *
	*/
	_setResizeListener : function(obj)
	{
		for (var j = 0; j < obj.arr.length; j++) {
			if (obj.arr[j].flagListener) {
				this.insListener.set({
					bindAsEvent : 1, insCurrent : this, event : 'mousedown',
					strFunc : '_mousedownResize', ele : $(this.idWindow).down('.' + obj.arr[j].flagName, 0),
					vars : { className : obj.arr[j].flagName, arr : obj.arr }
				});
			}
		}
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'mousemove',
			strFunc : '_mousemoveResize', ele : document, vars : ''
		});
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'mouseup',
			strFunc : '_mouseupResize', ele : document, vars : ''
		});
	},

	/**
	 *
	*/
	_varsResize : {},
	_mousedownResize : function(evt, obj)
	{
		evt.stop();
		this._mousedownZIndex();
		if (this._varsCover) return;
		if (this.vars.flagResizeIni == 'none' || this.vars.flagResizeNow == 'none') return;
		var objWindowClass = {};
		for (var j = 0; j < obj.arr.length; j++) {
			if (obj.arr[j].flagName == obj.className) {
				objWindowClass = obj.arr[j];
				break;
			}
		}

		this._varsResize = {};
		this._varsResize = {
			flag            : 1,
			ele             : evt.element(),
			window          : this.vars,
			windowClass     : objWindowClass,
			windowLeft      : $(this.idWindow).offsetLeft,
			windowTop       : $(this.idWindow).offsetTop,
			mouseLeft       : evt.pointerX(),
			mouseTop        : evt.pointerY(),
			naviBarXLeft    : evt.pointerX(),
			naviBarXTop     : evt.pointerY(),
			naviBarYLeft    : evt.pointerX(),
			naviBarYTop     : evt.pointerY(),
			eleBarX         : null,
			eleBarY         : null,
			endWindowWidth  : 0,
			endWindowHeight : 0,
			eleLockX        : null,
			eleLockY        : null
		};
		if (this._varsResize.windowClass.flagArrection == 'x') {
			this._setResizeNavi({evt : evt, navi : 'x'});
		} else if (this._varsResize.windowClass.flagArrection == 'y') {
			this._setResizeNavi({evt : evt, navi : 'y'});
		} else if (this._varsResize.windowClass.flagArrection == 'xy') {
			this._setResizeNavi({evt : evt, navi : 'x'});
			this._setResizeNavi({evt : evt, navi : 'y'});
		}
	},

	/**
	 *
	*/
	_setResizeNavi : function(obj)
	{
		var viewSize = document.viewport.getDimensions();
		var scroll = document.viewport.getScrollOffsets();
		var numZIndex = this.insRoot.vars.varsSystem.num.zIndex;
		/*lock*/
		var eleLock = $(document.createElement('div'));
		eleLock.addClassName('codeLibLockView');
		eleLock.setStyle({
			zIndex : numZIndex
		});
		$(this.insRoot.vars.varsSystem.id.root).insert(eleLock);

		var ele = $(document.createElement('span'));
		if (obj.navi == 'x') {
			if (this._varsResize.window.flagResizeNow == 'all' || this._varsResize.window.flagResizeNow == 'x') {
				ele.setStyle({
					height : viewSize.height + scroll.top + 'px',
					top    : '0px',
					left   : obj.evt.pointerX() + 'px',
					zIndex : numZIndex
				});
			}
			ele.addClassName('codeLibWindowNaviX');
			this._varsResize.eleBarX = ele;
			this._varsResize.eleLockX = eleLock;
		} else if (obj.navi == 'y') {
			if (this._varsResize.window.flagResizeNow == 'all' || this._varsResize.window.flagResizeNow == 'y') {
				ele.setStyle({
					width  : viewSize.width + scroll.left + 'px',
					top    : obj.evt.pointerY() + 'px',
					left   : '0px',
					zIndex : numZIndex
				});
			}
			ele.addClassName('codeLibWindowNaviY');
			this._varsResize.eleBarY = ele;
			this._varsResize.eleLockY = eleLock;
		}
		$(this.insRoot.vars.varsSystem.id.root).insert(ele);
	},

	/**
	 *
	*/
	_mousemoveResize : function(evt)
	{
		if (!this._varsResize.flag) return;
		if (this._varsResize.window.flagResizeNow == 'none') return;

		if (this._varsResize.windowClass.flagName == 'codeLibWindowHeaderTopLeft') {
			if (!this._checkResizeWindowMinLeft({evt : evt, window : this._varsResize.window })) {
				this._varsResize.eleBarX.setStyle({
					left : evt.pointerX() + 'px',
					top  : '0px'
				});
				this._varsResize.naviBarXLeft = evt.pointerX();
				this._varsResize.naviBarXTop = 0;
			}
			if (!this._checkResizeWindowMinTop({evt : evt, window : this._varsResize.window })) {
				this._varsResize.eleBarY.setStyle({
					left : '0px',
					top  : evt.pointerY() + 'px'
				});
				this._varsResize.naviBarYLeft = 0;
				this._varsResize.naviBarYTop = evt.pointerY();
			}
		} else if (this._varsResize.windowClass.flagName == 'codeLibWindowHeaderTopRight') {
			if (!this._checkResizeWindowMinRight({evt : evt, window : this._varsResize.window })) {
				this._varsResize.eleBarX.setStyle({
					left : evt.pointerX() + 'px',
					top  : '0px'
				});
				this._varsResize.naviBarXLeft = evt.pointerX();
				this._varsResize.naviBarXTop = 0;
			}
			if (!this._checkResizeWindowMinTop({evt : evt, window : this._varsResize.window })) {
				this._varsResize.eleBarY.setStyle({
					left : '0px',
					top  : evt.pointerY() + 'px'
				});
				this._varsResize.naviBarYLeft = 0;
				this._varsResize.naviBarYTop = evt.pointerY();
			}
		} else if (this._varsResize.windowClass.flagName == 'codeLibWindowBodyBottomLeft') {
			if (!this._checkResizeWindowMinLeft({evt : evt, window : this._varsResize.window })) {
				this._varsResize.eleBarX.setStyle({
					left : evt.pointerX() + 'px',
					top  : '0px'
				});
				this._varsResize.naviBarXLeft = evt.pointerX();
				this._varsResize.naviBarXTop = 0;
			}
			if (!this._checkResizeWindowMinBottom({evt : evt, window : this._varsResize.window })) {
				this._varsResize.eleBarY.setStyle({
					left : '0px',
					top  : evt.pointerY() + 'px'
				});
				this._varsResize.naviBarYLeft = 0;
				this._varsResize.naviBarYTop = evt.pointerY();
			}
		} else if (this._varsResize.windowClass.flagName == 'codeLibWindowBodyBottomRight') {
			if (!this._checkResizeWindowMinRight({evt : evt, window : this._varsResize.window })) {
				this._varsResize.eleBarX.setStyle({
					left : evt.pointerX() + 'px',
					top  : '0px'
				});
				this._varsResize.naviBarXLeft = evt.pointerX();
				this._varsResize.naviBarXTop = 0;
			}
			if (!this._checkResizeWindowMinBottom({evt : evt, window : this._varsResize.window })) {
				this._varsResize.eleBarY.setStyle({
					left : '0px',
					top  : evt.pointerY() + 'px'
				});
				this._varsResize.naviBarYLeft = 0;
				this._varsResize.naviBarYTop = evt.pointerY();
			}
		} else if (this._varsResize.windowClass.flagName == 'codeLibWindowHeaderTopMiddle') {
			if (this._checkResizeWindowMinTop({evt : evt, window : this._varsResize.window })) return;
			this._varsResize.eleBarY.setStyle({
				left : '0px',
				top  : evt.pointerY() + 'px'
			});
			this._varsResize.naviBarYLeft = 0;
			this._varsResize.naviBarYTop = evt.pointerY();
		} else if (this._varsResize.windowClass.flagName == 'codeLibWindowBodyBottomMiddle') {
			if (this._checkResizeWindowMinBottom({evt : evt, window : this._varsResize.window })) return;
			this._varsResize.eleBarY.setStyle({
				left : '0px',
				top  : evt.pointerY() + 'px'
			});
			this._varsResize.naviBarYLeft = 0;
			this._varsResize.naviBarYTop = evt.pointerY();
		} else if (this._varsResize.windowClass.flagName == 'codeLibWindowHeaderBottomLeft'
				|| this._varsResize.windowClass.flagName == 'codeLibWindowBodyTopLeft'
		) {
			if (this._checkResizeWindowMinLeft({evt : evt, window : this._varsResize.window })) return;
			this._varsResize.eleBarX.setStyle({
				left : evt.pointerX() + 'px',
				top  : '0px'
			});
			this._varsResize.naviBarXLeft = evt.pointerX();
			this._varsResize.naviBarXTop = 0;
		} else if (this._varsResize.windowClass.flagName == 'codeLibWindowHeaderBottomRight'
			|| this._varsResize.windowClass.flagName == 'codeLibWindowBodyTopRight'
		) {
			if (this._checkResizeWindowMinRight({evt : evt, window : this._varsResize.window })) return;
			this._varsResize.eleBarX.setStyle({
				left : evt.pointerX() + 'px',
				top  : '0px'
			});
			this._varsResize.naviBarXLeft = evt.pointerX();
			this._varsResize.naviBarXTop = 0;
		}
		evt.stop();
	},

	/**
	 *
	*/
	_mouseupResize : function(evt)
	{
		if (!this._varsResize.flag) return;
		if (this._varsResize.window.flagResizeNow == 'none') return;

		if (this._varsResize.windowClass.flagName == 'codeLibWindowHeaderTopLeft') {
			if (this._varsResize.window.flagResizeNow == 'all' || this._varsResize.window.flagResizeNow == 'x') {
				this._setResizeWindowLeft(this._varsResize);
			}
			if (this._varsResize.window.flagResizeNow == 'all' || this._varsResize.window.flagResizeNow == 'y') {
				this._setResizeWindowTop(this._varsResize);
			}
		} else if (this._varsResize.windowClass.flagName == 'codeLibWindowHeaderTopRight') {
			if (this._varsResize.window.flagResizeNow == 'all' || this._varsResize.window.flagResizeNow == 'x') {
				this._setResizeWindowRight(this._varsResize);
			}
			if (this._varsResize.window.flagResizeNow == 'all' || this._varsResize.window.flagResizeNow == 'y') {
				this._setResizeWindowTop(this._varsResize);
			}
		} else if (this._varsResize.windowClass.flagName == 'codeLibWindowBodyBottomLeft') {
			if (this._varsResize.window.flagResizeNow == 'all' || this._varsResize.window.flagResizeNow == 'x') {
				this._setResizeWindowLeft(this._varsResize);
			}
			if (this._varsResize.window.flagResizeNow == 'all' || this._varsResize.window.flagResizeNow == 'y') {
				this._setResizeWindowBottom(this._varsResize);
			}
		} else if (this._varsResize.windowClass.flagName == 'codeLibWindowBodyBottomRight') {
			if (this._varsResize.window.flagResizeNow == 'all' || this._varsResize.window.flagResizeNow == 'x') {
				this._setResizeWindowRight(this._varsResize);
			}
			if (this._varsResize.window.flagResizeNow == 'all' || this._varsResize.window.flagResizeNow == 'y') {
				this._setResizeWindowBottom(this._varsResize);
			}
		} else if (this._varsResize.windowClass.flagName == 'codeLibWindowHeaderTopMiddle'
			&& (this._varsResize.window.flagResizeNow == 'all' || this._varsResize.window.flagResizeNow == 'y')
		) {
			this._setResizeWindowTop(this._varsResize);
		} else if (this._varsResize.windowClass.flagName == 'codeLibWindowBodyBottomMiddle'
			&& (this._varsResize.window.flagResizeNow == 'all' || this._varsResize.window.flagResizeNow == 'y')
		) {
			this._setResizeWindowBottom(this._varsResize);
		} else if ((this._varsResize.windowClass.flagName == 'codeLibWindowHeaderBottomLeft'
			|| this._varsResize.windowClass.flagName == 'codeLibWindowBodyTopLeft')
			&& (this._varsResize.window.flagResizeNow == 'all'
			|| this._varsResize.window.flagResizeNow == 'x')
		) {
			this._setResizeWindowLeft(this._varsResize);
		} else if ((this._varsResize.windowClass.flagName == 'codeLibWindowHeaderBottomRight'
			|| this._varsResize.windowClass.flagName == 'codeLibWindowBodyTopRight')
			&& (this._varsResize.window.flagResizeNow == 'all'
			|| this._varsResize.window.flagResizeNow == 'x')
		) {
			this._setResizeWindowRight(this._varsResize);
		}

		this._setResizeWindowSize({
			numLeft     : this._varsResize.windowLeft,
			numTop      : this._varsResize.windowTop,
			vars : this._varsResize.window,
			arr      : this._staticResize
		});

		this.vars.numTop = $(this.idWindow).offsetTop;
		this.vars.numLeft = $(this.idWindow).offsetLeft;
		this.setCake();

		this.allot({
			from       : '_mouseupResize',
			insCurrent : this.insSelf
		});

		if (this._varsResize.windowClass.flagArrection == 'x') {
			this._varsResize.eleBarX.remove();
			this._varsResize.eleLockX.remove();
		} else if (this._varsResize.windowClass.flagArrection == 'y') {
			this._varsResize.eleBarY.remove();
			this._varsResize.eleLockY.remove();
		} else if (this._varsResize.windowClass.flagArrection == 'xy') {
			this._varsResize.eleBarX.remove();
			this._varsResize.eleBarY.remove();
			this._varsResize.eleLockX.remove();
			this._varsResize.eleLockY.remove();
		}
		evt.stop();
		this._varsResize={};
	},

	/**
	 *
	*/
	_setResizeWindowSize : function(obj)
	{
		var numWidth= obj.vars.numWidth;
		var numHeight= obj.vars.numHeight;
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].flagWidth == 'width') {
				$(this.idWindow).down('.' + obj.arr[i].flagName, obj.arr[i].numPos).setStyle({
					'width' : numWidth + 'px'
				});
			} else if (obj.arr[i].flagWidth == 'bodyWidth') {
				$(this.idWindow).down('.' + obj.arr[i].flagName, obj.arr[i].numPos).setStyle({
					'width' : numWidth + 'px'
				});
			}
			if (obj.arr[i].flagHeight == 'height') {
				$(this.idWindow).down('.' + obj.arr[i].flagName, obj.arr[i].numPos).setStyle({
					'height' : numHeight + 'px'
				});
			}
		}
		var windowWidth = obj.vars.numWidth + this._staticTemplate.headerTopLeftWidth * 2;
		$(this.idWindow).setStyle({
			width : windowWidth + 'px',
			left  : obj.numLeft + 'px',
			top   : obj.numTop + 'px'
		});
		this._varWindowWidthTitle(obj.vars);
		$(this.idWindow).down('.codeLibWindowHeaderWidthTitle', 0).setStyle({
			width : obj.vars.numWidthTitle + 'px'
		});
		$(this.idWindow).down('.codeLibWindowHeaderWidthTitle', 1).setStyle({
			width : obj.vars.numWidthTitle + 'px'
		});
	},



	/**
	 *
	*/
	_setResizeWindowLeft : function(obj)
	{
		var difX = this._varsResize.naviBarXLeft - this._varsResize.mouseLeft;
		var a = obj.window.numWidth - difX;
		if (a < obj.window.numWidthMin) {
			obj.window.numWidth = obj.window.numWidthMin;
			var dif = parseFloat(this._varsResize.naviBarXLeft) - this._varsResize.mouseLeft;
			this._varsResize.windowLeft = parseFloat(this._varsResize.windowLeft) + dif;
		} else {
			obj.window.numWidth -= difX;
			this._varsResize.windowLeft = parseFloat(this._varsResize.windowLeft) + difX;
		}
		this._varsResize.endWindowWidth = obj.window.numWidth;
	},

	/**
	 *
	*/
	_setResizeWindowRight : function(obj)
	{
		var difX = this._varsResize.naviBarXLeft - this._varsResize.mouseLeft;
		var a = obj.window.numWidth + difX;
		if (a < obj.window.numWidthMin) {
			obj.window.numWidth = obj.window.numWidthMin;
		} else {
			obj.window.numWidth += difX;
		}
		this._varsResize.endWindowWidth = obj.window.numWidth;
	},

	/**
	 *
	*/
	_setResizeWindowTop : function(obj)
	{
		var difY = this._varsResize.naviBarYTop - this._varsResize.mouseTop;
		var a = obj.window.numHeight - difY;
		if (a < obj.window.numHeightMin) {
			obj.window.numHeight = obj.window.numHeightMin;
			var dif = parseFloat(this._varsResize.naviBarYTop) - this._varsResize.mouseTop;
			this._varsResize.windowTop = parseFloat(this._varsResize.windowTop) + dif;
		} else {
			obj.window.numHeight-=difY;
			this._varsResize.windowTop = parseFloat(this._varsResize.windowTop) + difY;
		}
		this._varsResize.endWindowHeight = obj.window.numHeight;
	},

	/**
	 *
	*/
	_setResizeWindowBottom : function(obj)
	{
		var difY = this._varsResize.naviBarYTop - this._varsResize.mouseTop;
		var a = obj.window.numHeight + difY;
		if (a < obj.window.numHeightMin) {
			obj.window.numHeight = obj.window.numHeightMin;
		} else {
			obj.window.numHeight += difY;
		}
		this._varsResize.endWindowHeight = obj.window.numHeight;
	},

	/**
	 *
	*/
	_checkResizeWindowMinLeft : function(obj)
	{
		var a = obj.window.numWidth - (obj.evt.pointerX() - this._varsResize.mouseLeft);
		var data = (a < obj.window.numWidthMin)? 1  : 0;

		return  data;
	},

	/**
	 *
	*/
	_checkResizeWindowMinRight : function(obj)
	{
		var a = obj.window.numWidth + (obj.evt.pointerX() - this._varsResize.mouseLeft);
		var data = (a < obj.window.numWidthMin)? 1  : 0;

		return  data;
	},

	/**
	 *
	*/
	_checkResizeWindowMinTop : function(obj)
	{
		var a = obj.window.numHeight - (obj.evt.pointerY() - this._varsResize.mouseTop);
		var data = (a < obj.window.numHeightMin)? 1  : 0;

		return  data;
	},

	/**
	 *
	*/
	_checkResizeWindowMinBottom : function(obj)
	{
		var a = obj.window.numHeight + (obj.evt.pointerY() - this._varsResize.mouseTop);
		var data = (a < obj.window.numHeightMin)? 1  : 0;

		return  data;
	},

	/**
	 * Skeleton
	*/
	_iniSkeleton : function()
	{
		if (!this.vars.flagSkeletonUse) return;
		this._setSkeletonListener();
	},

	/**
	 *
	*/
	_setSkeletonListener : function()
	{
		this.insListener.set({
			bindAsEvent : 0, insCurrent : this, event : 'mouseover',
			strFunc : '_mouseoverSkeleton', ele : $(this.idWindow), vars : ''
		});
		this.insListener.set({
			bindAsEvent : 0, insCurrent : this, event : 'mouseout',
			strFunc : '_mouseoutSkeleton', ele : $(this.idWindow), vars : ''
		});
		this._mouseoutSkeleton();
	},

	/**
	 *
	*/
	_staticSkeleton : [
		'codeLibWindowHeaderTopLeft',
		'codeLibWindowHeaderTopMiddle',
		'codeLibWindowHeaderTopRight',
		'codeLibWindowHeaderBottomLeft',
		'codeLibWindowHeaderBottomMiddle',
		'codeLibWindowHeaderBottomRight',
		'codeLibWindowBodyTopLeft',
		'codeLibWindowBodyTopRight',
		'codeLibWindowBodyBottomLeft',
		'codeLibWindowBodyBottomMiddle',
		'codeLibWindowBodyBottomRight'
	],

	/**
	 *
	*/
	_mouseoverSkeleton : function()
	{
		for (var i = 0; i < this._staticSkeleton.length; i++) {
			$(this.idWindow).down('.' + this._staticSkeleton[i], 0).removeClassName('codeLibBaseOpacityZero');
		}
	},

	/**
	 *
	*/
	_mouseoutSkeleton : function()
	{
		for (var i = 0; i < this._staticSkeleton.length; i++) {
			$(this.idWindow).down('.' + this._staticSkeleton[i], 0).addClassName('codeLibBaseOpacityZero');
		}
	},

	/**
	 * Fold
	*/
	_iniFold : function()
	{
		if (!this.vars.flagFoldUse) return;
		this._setFold();
		this._setFoldListener();
		this._removeCursor({arr : this._staticResize});
		this._setCursor({arr : this._staticResize});
	},

	/**
	 *
	*/
	_setFold : function()
	{
		if (this.vars.flagFoldNow) {
			$(this.idWindow).down('.codeLibWindowBodyTop', 0).hide();
			$(this.idWindow).down('.codeLibWindowNaviFold', 0).addClassName('codeLibWindowNaviFoldPlus');
			var data = this.varsLoad.varsWhole.str.foldOpenTitle;
			$(this.idWindow).down('.codeLibWindowNaviFold', 0).title = data;
		} else {
			$(this.idWindow).down('.codeLibWindowNaviFold', 0).addClassName('codeLibWindowNaviFoldMinus');
			var data = this.varsLoad.varsWhole.str.foldCloseTitle;
			$(this.idWindow).down('.codeLibWindowNaviFold', 0).title = data;
		}
	},

	/**
	 *
	*/
	_setFoldListener : function()
	{
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'mousedown',
			strFunc : '_mousedownFold', ele : $(this.idWindow).down('.codeLibWindowNaviFold', 0),
			vars : ''
		});
		this.insListener.set({bindAsEvent : 0, insCurrent : this, event : 'mouseover',
			strFunc : '_mouseoverFold', ele : $(this.idWindow).down('.codeLibWindowNaviFold', 0),
			vars : ''
		});
		this.insListener.set({bindAsEvent : 0, insCurrent : this, event : 'mouseout',
			strFunc : '_mouseoutFold', ele : $(this.idWindow).down('.codeLibWindowNaviFold', 0),
			vars : ''
		});
	},

	/**
	 *
	*/
	_mousedownFold : function(evt)
	{
		evt.stop();
		this.updateFold();
		this._removeCursor({arr : this._staticResize});
		this._setCursor({arr : this._staticResize});
		this.setCake();
	},

	/**
	 *
	*/
	updateFold : function(obj)
	{
		this._removeFold();
		if (!this.vars.flagFoldNow) this.vars.flagFoldNow = 1;
		else this.vars.flagFoldNow = 0;
		var idInsert = this.insRoot.vars.varsSystem.id.root;
		var numZIndex = this.insRoot.setZIndex();
		var insLock = new Code_Lib_Lock();
		if (this.vars.flagFoldNow) {
			if (this.vars.flagResizeIni == 'all') this.vars.flagResizeNow = 'x';
			else if (this.vars.flagResizeIni == 'y') this.vars.flagResizeNow = 'none';
			new Effect.BlindUp($(this.idWindow).down('.codeLibWindowBodyTop', 0),{
				beforeStart:function()
				{
					insLock.setLock({
						action    : 'wait',
						idInsert  : idInsert,
						numZIndex : numZIndex
					});
				},
				afterFinish:function()
				{
					insLock.removeLock();
				}
			});
			$(this.idWindow).down('.codeLibWindowNaviFold', 0).addClassName('codeLibWindowNaviFoldPlus');
			var data = this.varsLoad.varsWhole.str.foldOpenTitle;
			$(this.idWindow).down('.codeLibWindowNaviFold', 0).title = data;
		} else {
			if (this.vars.flagResizeIni == 'all') this.vars.flagResizeNow = 'all';
			else if (this.vars.flagResizeIni == 'y') this.vars.flagResizeNow = 'y';
			new Effect.BlindDown($(this.idWindow).down('.codeLibWindowBodyTop', 0),{
				beforeStart:function()
				{
					insLock.setLock({
						action    : 'wait',
						idInsert  : idInsert,
						numZIndex : numZIndex
					});
				},
				afterFinish:function()
				{
					insLock.removeLock();
				}
			});
			var ele = $(this.idWindow).down('.codeLibWindowNaviFold', 0);
			ele.addClassName('codeLibWindowNaviFoldMinus');
			ele.title=this.varsLoad.varsWhole.str.foldCloseTitle;
		}
		this._mouseoutFold();
	},

	/**
	 *
	*/
	_mouseoverFold : function()
	{
		this._removeFold();
		if (this.vars.flagFoldNow) {
			$(this.idWindow).down('.codeLibWindowNaviFold', 0).addClassName('codeLibWindowNaviFoldPlusOver');
		}
		else $(this.idWindow).down('.codeLibWindowNaviFold', 0).addClassName('codeLibWindowNaviFoldMinusOver');
	},

	/**
	 *
	*/
	_mouseoutFold : function()
	{
		this._removeFold();
		if (this.vars.flagFoldNow) {
			$(this.idWindow).down('.codeLibWindowNaviFold', 0).addClassName('codeLibWindowNaviFoldPlus');
		}
		else $(this.idWindow).down('.codeLibWindowNaviFold', 0).addClassName('codeLibWindowNaviFoldMinus');
	},

	/**
	 *
	*/
	_removeFold : function()
	{
		$(this.idWindow).down('.codeLibWindowNaviFold', 0).removeClassName('codeLibWindowNaviFoldPlus');
		$(this.idWindow).down('.codeLibWindowNaviFold', 0).removeClassName('codeLibWindowNaviFoldMinus');
		$(this.idWindow).down('.codeLibWindowNaviFold', 0).removeClassName('codeLibWindowNaviFoldPlusOver');
		$(this.idWindow).down('.codeLibWindowNaviFold', 0).removeClassName('codeLibWindowNaviFoldMinusOver');
	},

	/**
	 * Boot
	*/
	_iniBoot : function()
	{
		this._setBoot();
		this._setBootListener();
	},

	/**
	 *
	*/
	_setBoot : function()
	{
		if (this.vars.flagBootUse == 0) {
			$(this.idWindow).down('.codeLibWindowBoot', 0).remove();
			$(this.idWindow).down('.codeLibWindowBodyTopMiddle', 0).removeClassName('codeLibBaseCursorPointer');

		} else if (this.vars.flagBootUse == 'auto') {
			this._mousedownBoot();
		}
	},

	/**
	 *
	*/
	_setBootListener : function(obj)
	{
		if (this.vars.flagBootUse == 1) {
			this.insListener.set({
				bindAsEvent : 0, insCurrent : this, event : 'mousedown',
				strFunc : '_mousedownBoot', ele : $(this.idWindow).down('.codeLibWindowBoot', 0),
				vars : ''
			});
		}
	},

	/**
	 *
	*/
	_mousedownBoot : function()
	{
		this._mousedownZIndex();
		if (!$(this.idWindow).down('.codeLibWindowBoot', 0)) return;
		$(this.idWindow).down('.codeLibWindowBoot', 0).remove();
		$(this.idWindow).down('.codeLibWindowBodyTopMiddle', 0).removeClassName('codeLibBaseCursorPointer');
		this.allot({
			from       : '_mousedownBoot',
			insCurrent : this.insSelf
		});
		this.setCake();
	},

	/**
	 *
	*/
	setBoot : function()
	{
		this._mousedownBoot();
	},

	/**
	 * Remove
	*/
	_iniRemove : function()
	{
		if (!this.vars.flagRemoveUse) return;
		this._setRemoveListener();
	},

	/**
	 *
	*/
	_setRemoveListener : function()
	{
		for (var j = 0; j < 2; j++) {
			this.insListener.set({
				bindAsEvent : 1, insCurrent : this, event : 'mousedown',
				strFunc : '_mousedownRemove', ele : $(this.idWindow).down('.codeLibWindowNaviRemove', j),
				vars : ''
			});
			this.insListener.set({
				bindAsEvent : 0, insCurrent : this, event : 'mouseover',
				strFunc : '_mouseoverRemove', ele : $(this.idWindow).down('.codeLibWindowNaviRemove', j),
				vars : ''
			});
			this.insListener.set({
				bindAsEvent : 0, insCurrent : this, event : 'mouseout',
				strFunc : '_mouseoutRemove', ele : $(this.idWindow).down('.codeLibWindowNaviRemove', j),
				vars : ''
			});
		}
	},

	/**
	 *
	*/
	_mousedownRemove : function(evt)
	{
		if (evt) evt.stop();
		this.allot({
			from       : '_mousedownRemove',
			insCurrent : this.insSelf
		});
	},

	/**
	 *
	*/
	_mouseoverRemove : function(obj)
	{
		$(this.idWindow).down('.codeLibWindowNaviRemove', 0).addClassName('codeLibWindowNaviRemoveOver');
	},

	/**
	 *
	*/
	_mouseoutRemove : function(obj)
	{
		$(this.idWindow).down('.codeLibWindowNaviRemove', 0).removeClassName('codeLibWindowNaviRemoveOver');
	},

	/**
	 * Hide
	*/
	_iniHide : function()
	{
		if (!this.vars.flagHideUse) return;
		this._setHideListener();
	},

	/**
	 *
	*/
	_setHideListener : function()
	{
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'mousedown',
			strFunc : '_mousedownHide', ele : $(this.idWindow).down('.codeLibWindowNaviHide', 0),
			vars : ''
		});
		this.insListener.set({
			bindAsEvent : 0, insCurrent : this, event : 'mouseover',
			strFunc : '_mouseoverHide', ele : $(this.idWindow).down('.codeLibWindowNaviHide', 0),
			vars : ''
		});
		this.insListener.set({
			bindAsEvent : 0, insCurrent : this, event : 'mouseout',
			strFunc : '_mouseoutHide', ele : $(this.idWindow).down('.codeLibWindowNaviHide', 0),
			vars : ''
		});
		if (this.vars.flagHideNow) $(this.idWindow).hide();
	},

	/**
	 *
	*/
	_mousedownHide : function(evt, obj)
	{
		evt.stop();
		this._mousedownZIndex();
		var flag = this.allot({
			from       : '_mousedownHide',
			insCurrent : this,
			vars       : this.vars
		});
		if (flag) return;
		if (!this.vars.flagHideNow && this.vars.flagLockUse) {
			this.hideLockWindow();
		} else {
			this.updateHide({ flagEffect : 1 });
			this.setCake();
		}
	},

	/**
	 *
	*/
	checkHide : function()
	{
		return this.vars.flagHideNow;
	},

	/**
	 * {
	 * 	flagEffect : int
	 * }
	*/
	updateHide : function(obj)
	{
		var idInsert = this.insRoot.vars.varsSystem.id.root;
		var numZIndex = this.insRoot.setZIndex();
		var insLock = new Code_Lib_Lock();
		if (this.vars.flagHideNow) {
 			this.vars.flagHideNow = 0;
			new Effect.Appear($(this.idWindow),{
				beforeStart:function()
				{
					insLock.setLock({
						action    : 'wait',
						idInsert  : idInsert,
						numZIndex : numZIndex
					});
				},
				afterFinish:function()
				{
					insLock.removeLock();
				}
			});
			this._setScroll({insLock : insLock});
		} else {
			this.vars.flagHideNow = 1;
			if (obj.flagEffect) {
				new Effect.Fade($(this.idWindow),{
					beforeStart:function()
					{
						insLock.setLock({
							action    : 'wait',
							idInsert  : idInsert,
							numZIndex : numZIndex
						});
					},
					afterFinish:function()
					{
						insLock.removeLock();
					}
				});
			} else {
				$(this.idWindow).hide();
			}
		}
		this._updateMenuHide();
	},

	/**
	 *
	*/
	_mouseoverHide : function()
	{
		$(this.idWindow).down('.codeLibWindowNaviHide', 0).addClassName('codeLibWindowNaviHideOver');
	},

	/**
	 *
	*/
	_mouseoutHide : function()
	{
		$(this.idWindow).down('.codeLibWindowNaviHide', 0).removeClassName('codeLibWindowNaviHideOver');
	},

	/**
	 * obj = {
	 * 	insLock : instance
	 * }
	*/
	_staticScroll : {numTopbarHeight : 10, numSpeed : 5},
	_varsScroll : null,
	_setScroll : function(obj)
	{
		this._varsScroll = {
			targetLeft : 0,
			targetTop  : 0,
			checkLeft  : 0,
			checkTop   : 0,
			flagLeft   : 0,
			flagTop    : 0,
			interval   : null,
			insLock    : obj.insLock
		};
		this._mousedownZIndex();
		this._varsScroll.targetLeft = parseFloat($(this.idWindow).getStyle('left') || '0');
		this._varsScroll.targetTop = parseFloat($(this.idWindow).getStyle('top') || '0')
									- this._staticScroll.numTopbarHeight;
		this._varsScroll.interval = setInterval(this.setScrollMove.bind(this), 15);
	},

	/**
	 *
	*/
	setScroll : function()
	{
		this._setScroll({insLock : null});
	},

	/**
	 *
	*/
	setScrollMove : function()
	{
		var nowTop = (document.viewport.getScrollOffsets()).top;
		var nowLeft = (document.viewport.getScrollOffsets()).left;
		var offsetWidth = this._varsScroll.targetLeft - nowLeft;
		nowLeft += offsetWidth / this._staticScroll.numSpeed;
		if ( Math.abs(offsetWidth) < 1 ) nowLeft = this._varsScroll.targetLeft;
		if ( nowLeft == this._varsScroll.checkLeft ) this._varsScroll.flagLeft++;
		else this._varsScroll.checkLeft = nowLeft;
		if ( nowLeft == this._varsScroll.targetLeft || this._varsScroll.flagLeft > 10 ) {
			var offsetHeight = this._varsScroll.targetTop - nowTop;
			nowTop += offsetHeight / this._staticScroll.numSpeed;
			if ( Math.abs(offsetHeight) < 1 ) nowTop = this._varsScroll.targetTop;
			if ( nowTop == this._varsScroll.checkTop ) this._varsScroll.flagTop++;
			else this._varsScroll.checkTop = nowTop;
			if ( nowTop == this._varsScroll.targetTop || this._varsScroll.flagTop > 10) {
				clearInterval(this._varsScroll.interval);
				if (this._varsScroll.insLock) this._varsScroll.insLock.removeLock();
				this._varsScroll = null;
			}
			else scrollTo( nowLeft, nowTop );
		}
		else scrollTo( nowLeft, nowTop );
	},

	/**
	 *
	*/
	eventGlobal : function()
	{
		var flag = this.allot({
			from       : 'eventGlobal',
			insCurrent : this
		});
		if (flag) return;
		this.updateHide({ flagEffect : 1 });
		this.setCake();

	},

	/**
	 * Move
	*/
	_iniMove : function()
	{
		if (!this.vars.flagMoveUse) return;
		this._setMoveListener();
	},

	/**
	 *
	*/
	_setMoveListener : function(obj)
	{
		$(this.idWindow).down('.codeLibWindowHeaderBottomMiddle', 0).addClassName('codeLibBaseCursorMove');
		new Draggable(this.idWindow,{
			handle      : 'codeLibWindowHeaderBottomMiddle',
			starteffect : '',
			endeffect   : '',
			zIndex      : 100000000
		});
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'mousedown',
			strFunc : '_mousedownMove', ele : $(this.idWindow).down('.codeLibWindowHeaderBottomMiddle', 0),
			vars : ''
		});
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'mouseup',
			strFunc : '_mouseupMove', ele : document, vars : ''
		});
	},

	/**
	 *
	*/
	_varsMove : {},
	_mousedownMove : function(evt, obj)
	{
		this._mousedownZIndex();
		this._varsMove = {};
		this._varsMove = {
			flag : 1
		};
		evt.stop();
	},

	/**
	 *
	*/
	_mouseupMove : function(evt, obj)
	{
		if (!this._varsMove.flag) return;
		evt.stop();
		this.vars.numTop = ($(this.idWindow).offsetTop <= 0)? 0  : $(this.idWindow).offsetTop;
		this.vars.numLeft = ($(this.idWindow).offsetLeft <= 0)? 0  : $(this.idWindow).offsetLeft;
		this.setCake();
		this._varsMove = {};
	},

	/**
	 *
	*/
	_iniZIndex : function()
	{
		if (!this.vars.flagZIndexUse) return;
		this._setZIndexListener({arr : this.vars});
	},

	/**
	 *
	*/
	_setZIndexListener : function(obj)
	{
		this.insListener.set({
			bindAsEvent : 0, insCurrent : this, event : 'mousedown',
			strFunc : '_mousedownZIndex', ele : $(this.idWindow), vars : ''
		});
	},

	/**
	 *
	*/
	_mousedownZIndex : function()
	{
		this.vars.numZIndex = this.insRoot.setZIndex();
		$(this.idWindow).setStyle({
			zIndex : this.vars.numZIndex
		});
	},

	/**
	 *
	*/
	setZIndex : function()
	{
		this._mousedownZIndex();
	},

	/**
	 *
	*/
	_iniCursor : function()
	{
		this._removeCursor({arr : this._staticResize});
		this._setCursor({arr : this._staticResize});
	},

	/**
	 *
	*/
	_setCursor : function(obj)
	{
		if (!this.vars.flagResizeUse) return;
		if (this.vars.flagResizeNow == 'all') {
			for (var j = 0; j < obj.arr.length; j++) {
				$(this.idWindow).down('.' + obj.arr[j].flagName, 0).addClassName(obj.arr[j].flagCursor);
			}
		} else if (this.vars.flagResizeNow == 'x') {
			for (var j = 0; j < obj.arr.length; j++) {
				if (obj.arr[j].flagArrection == 'x') {
					$(this.idWindow).down('.' + obj.arr[j].flagName, 0).addClassName(obj.arr[j].flagCursor);
				}
			}
		} else if (this.vars.flagResizeNow == 'y') {
			for (var j = 0; j < obj.arr.length; j++) {
				if (obj.arr[j].flagArrection == 'y') {
					$(this.idWindow).down('.' + obj.arr[j].flagName, 0).addClassName(obj.arr[j].flagCursor);
				}
			}
		}
	},

	/**
	 *
	*/
	_removeCursor : function(obj)
	{
		if (!this.vars.flagResizeUse) return;
		for (var j = 0; j < obj.arr.length; j++) {
			$(this.idWindow).down('.' + obj.arr[j].flagName, 0).removeClassName(obj.arr[j].flagCursor);
		}
	},

	/**
	 *
	*/
	_staticResize : [
		{
			flagListener : 1, numPos : 0, flagName : 'codeLibWindowHeaderTopLeft',
			flagCursor : 'codeLibBaseCursorSe-resize', flagArrection : 'xy', flagWidth : '', flagHeight : ''
		},
		{
			flagListener : 1, numPos : 0, flagName : 'codeLibWindowHeaderTopMiddle',
			flagCursor : 'codeLibBaseCursorS-resize', flagArrection : 'y', flagWidth : 'width', flagHeight : ''
		},
		{
			flagListener : 1, numPos : 0, flagName : 'codeLibWindowHeaderTopRight',
			flagCursor : 'codeLibBaseCursorNe-resize', flagArrection : 'xy', flagWidth : '', flagHeight : ''
		},
		{
			flagListener : 1, numPos : 0, flagName : 'codeLibWindowHeaderBottomLeft',
			flagCursor : 'codeLibBaseCursorE-resize', flagArrection : 'x', flagWidth : '', flagHeight : ''
		},
		{
			flagListener : 0, numPos : 0, flagName : 'codeLibWindowHeaderBottomMiddle',
			flagCursor : '', flagArrection : '', flagWidth : 'width', flagHeight : ''
		},
		{
			flagListener : 0, numPos : 1, flagName : 'codeLibWindowHeaderBottomMiddle',
			flagCursor : '', flagArrection : '', flagWidth : 'width', flagHeight : ''
		},
		{
			flagListener : 1, numPos : 0, flagName : 'codeLibWindowHeaderBottomRight',
			flagCursor : 'codeLibBaseCursorE-resize', flagArrection : 'x', flagWidth : '', flagHeight : ''
		},
		{
			flagListener : 1, numPos : 0, flagName : 'codeLibWindowBodyTopLeft',
			flagCursor : 'codeLibBaseCursorE-resize', flagArrection : 'x', flagWidth : '', flagHeight : 'height'
		},
		{
			flagListener : 0, numPos : 0, flagName : 'codeLibWindowBodyTopMiddle',
			flagCursor : '', flagArrection : '', flagWidth : 'bodyWidth', flagHeight : 'height'
		},
		{
			flagListener : 0, numPos : 0, flagName : 'codeLibWindowBodyTopMiddleWrap',
			flagCursor : '', flagArrection : '', flagWidth : 'bodyWidth', flagHeight : 'height'
		},
		{
			flagListener : 1, numPos : 0, flagName : 'codeLibWindowBodyTopRight',
			flagCursor : 'codeLibBaseCursorE-resize', flagArrection : 'x', flagWidth : '', flagHeight : 'height'
		},
		{
			flagListener : 1, numPos : 0, flagName : 'codeLibWindowBodyBottomLeft',
			flagCursor : 'codeLibBaseCursorNe-resize', flagArrection : 'xy', flagWidth : '', flagHeight : ''
		},
		{
			flagListener : 1, numPos : 0, flagName : 'codeLibWindowBodyBottomMiddle',
			flagCursor : 'codeLibBaseCursorS-resize', flagArrection : 'y', flagWidth : 'bodyWidth', flagHeight : ''
		},
		{
			flagListener : 1, numPos : 0, flagName : 'codeLibWindowBodyBottomRight',
			flagCursor : 'codeLibBaseCursorSe-resize', flagArrection : 'xy', flagWidth : '', flagHeight : ''
		}
	],

	/**
	 * Wrap
	*/
	_iniWrap : function()
	{
		var ele = $(document.createElement('div'));
		ele.id = this.idSelf;
		ele.addClassName('codeLibWindowWrap');
		this.eleInsert.insert(ele);
	},

	/**
	 *
	*/
	removeWrap : function()
	{
		this.insListener.stop();
		this._removeLock();
		$(this.idSelf).remove();
	},

	/**
	 * Lock
	*/
	insLock : null,
	_iniLock : function()
	{
		if (!this.vars.flagLockUse) return;
		this.vars.numZIndex = this.insRoot.vars.varsSystem.num.zIndex;
		if (this.vars.flagLockNow == 'fixed') {
			this.insLock = new Code_Lib_Lock();
			this.insLock.setLock({
				action    : 'window',
				idInsert  : this.insRoot.vars.varsSystem.id.root,
				numZIndex : this.vars.numZIndex
			});
		} else if (this.vars.flagLockNow == 'temp') {
			this.insLock = new Code_Lib_LockTemp();
			this.insLock.iniLoad({
				idSelf      : this.idSelf + 'Lock',
				idInsert    : this.insRoot.vars.varsSystem.id.root,
				numZIndex   : this.vars.numZIndex,
				insCurrent  : this,
				flagHideUse : (this.vars.flagHideUse)? this.vars.flagHideUse : 0,
				strFunc     : (this.vars.flagHideUse)? 'hideLockWindow' : '_mousedownRemove'
			});
		}
	},

	/**
	 *
	*/
	hideLockWindow : function()
	{
		if (this.vars.flagLockNow == 'temp') {
			if (this.insLock) {
				this.insLock.hideLock();
				this.updateHide({ flagEffect : 1 });
				this.setCake();
				this.allot({
					from       : 'hideLockWindow',
					insCurrent : this
				});
			}
		}
	},

	/**
	 *
	*/
	showLockWindow : function()
	{
		if (this.vars.flagLockNow == 'temp') {
			if (this.insLock) {
				this.insLock.vars.numZIndex = this.insRoot.vars.varsSystem.num.zIndex;
				this.insLock.showLock();
				this.updateHide({ flagEffect : 1 });
				this.setCake();
			}
		}
	},

	/**
	 *
	*/
	_removeLock : function()
	{
		if (!this.vars.flagLockUse) return;
		if (this.vars.flagLockNow == 'fixed') {
			this.insLock._removeLock();
		} else if (this.vars.flagLockNow == 'temp') {
			if ($(this.idSelf + 'Lock')) {
				this.insLock.insListener.stop();
				this.insLock.eleLock.remove();
			}
		}
	},

	/**
	 * Template
	*/
	_iniTemplate : function()
	{
		this._setTemplate();
	},

	/**
	 *
	*/
	_setTemplate : function()
	{
		var windowWidth = this.vars.numWidth + this._staticTemplate.headerTopLeftWidth * 2;
		this.vars.strCoverMaxTitle = this.varsLoad.varsWhole.str.coverMaxTitle;
		this.vars.strCoverNormalTitle = this.varsLoad.varsWhole.str.coverNormalTitle;
		this.vars.strRemoveTitle = this.varsLoad.varsWhole.str.removeTitle;
		this.vars.strHideTitle = this.varsLoad.varsWhole.str.hideTitle;
		this.vars.strFoldOpenTitle =this.varsLoad.varsWhole.str.foldOpenTitle;
		this.vars.strFoldCloseTitle = this.varsLoad.varsWhole.str.foldCloseTitle;
		this.vars.windowBodyWidth = this.vars.numWidth;
		this.vars.windowWidth = windowWidth;
		var data = this._templateWindow( this.vars );
		$(this.idSelf).insert(data);
	},

	/**
	 *
	*/
	_staticTemplate : {headerTopLeftWidth : 12},
	_varWindowWidthTitle : function(obj)
	{
		var numWidthIcon = 24;
		obj.numHeightIcon = 19;
		var numMargin = 8 * 2;
		var numPadding = 30 * 2;
		var numWidthTitle = obj.numWidth - numMargin - numPadding;
		if (obj.flagRemoveUse) { numWidthTitle -= numWidthIcon; }
		if (obj.flagHideUse) { numWidthTitle -= numWidthIcon; }
		if (obj.flagCoverUse) { numWidthTitle -= numWidthIcon; }
		if (obj.flagFoldUse) { numWidthTitle -= numWidthIcon; }
		if (obj.flagSwitchUse) { numWidthTitle -= numWidthIcon; }
		obj.numWidthTitle = numWidthTitle;
	},


	/**
	 *
	*/
	addStrWindowTitle : function(obj)
	{
		$(this.idWindow).down('.codeLibWindowHeaderWidthTitle', 0).innerHTML = this.vars.strTitle + obj.strTitle;
		$(this.idWindow).down('.codeLibWindowHeaderWidthTitle', 1).innerHTML = this.vars.strTitle + obj.strTitle;
	},

	/**
	 *
	*/
	_templateWindow : function(obj)
	{
		this._varWindowWidthTitle(obj);
		var tmplstr='<div class="codeLibWindow" id="' + this.idWindow + '" style="top : #{numTop}px; left : #{numLeft}px; width : #{windowWidth}px; z-index : #{numZIndex};">';
				tmplstr += '<div class="codeLibWindowHeader">';
					tmplstr += '<div class="codeLibWindowHeaderTop clearfix">';
						tmplstr += '<span class="codeLibWindowHeaderTopLeft unselect"></span>';
						tmplstr += '<span class="codeLibWindowHeaderTopMiddle unselect" style="width : #{numWidth}px; "></span>';
						tmplstr += '<span class="codeLibWindowHeaderTopRight unselect"></span>';
					tmplstr += '</div>';
					tmplstr += '<div class="codeLibWindowHeaderBottom clearfix" >';
						tmplstr += '<span class="codeLibWindowHeaderBottomLeft unselect"></span>';

						tmplstr += '<span class="codeLibWindowHeaderBottomMiddle unselect" style="width : #{numWidth}px;">';
							tmplstr += '<div class="codeLibWindowAreaLeft">';
								tmplstr += '<ul>';
								if (obj.strClass) tmplstr += '<li class="codeLibWindowHeaderTitleImg codeLibWindowHeaderWidthTitle #{strClass}" style="width : #{numWidthTitle}px; height : #{numHeightIcon}px;">#{strTitle}</li>';
								else tmplstr += '<li class="codeLibWindowHeaderTitle codeLibWindowHeaderWidthTitle codeLibBaseCursorDefault" style="width : #{numWidthTitle}px;">#{strTitle}</li>';
								tmplstr += '</ul>';
							tmplstr += '</div>';
							tmplstr += '<div class="codeLibWindowAreaRight">';
								if (obj.flagHideUse || obj.flagCoverUse || obj.flagFoldUse || obj.flagRemoveUse) {
									tmplstr += '<ul class="codeLibWindowNavi">';
										if (obj.flagRemoveUse) {tmplstr += '<li class="codeLibWindowNaviRemove codeLibBaseCursorPointer" title="#{strRemoveTitle}"></li>';}
										if (obj.flagHideUse) {tmplstr += '<li class="codeLibWindowNaviHide codeLibBaseCursorPointer" title="#{strHideTitle}"></li>';}
										if (obj.flagCoverUse) { tmplstr += '<li class="codeLibWindowNaviCover codeLibWindowNaviCoverMax codeLibBaseCursorPointer" title="#{strCoverMaxTitle}"></li>'; }
										if (obj.flagFoldUse) {tmplstr += '<li class="codeLibWindowNaviFold codeLibBaseCursorPointer" title="#{strFoldCloseTitle}"></li>';}
									tmplstr += '</ul>';
								}
							tmplstr += '</div>';
						tmplstr += '</span>';

						tmplstr += '<span class="codeLibWindowHeaderBottomMiddle unselect" style="width : #{numWidth}px; display : none;">';
							tmplstr += '<div class="codeLibWindowAreaLeft">';
								tmplstr += '<ul>';
								if (obj.strClass) tmplstr += '<li class="codeLibWindowHeaderTitleImg codeLibWindowHeaderWidthTitle #{strClass}" style="width : #{numWidthTitle}px; height : #{numHeightIcon}px;">#{strTitle}</li>';
								else tmplstr += '<li class="codeLibWindowHeaderTitle codeLibWindowHeaderWidthTitle codeLibBaseCursorDefault" style="width : #{numWidthTitle}px;">#{strTitle}</li>';
								tmplstr += '</ul>';
							tmplstr += '</div>';
							tmplstr += '<div class="codeLibWindowAreaRight">';
								if (obj.flagCoverUse || obj.flagRemoveUse) {
									tmplstr += '<ul class="codeLibWindowNavi">';
										if (obj.flagRemoveUse) {tmplstr += '<li class="codeLibWindowNaviRemove codeLibBaseCursorPointer" title="#{strRemoveTitle}"></li>';}
										if (obj.flagCoverUse) {tmplstr += '<li class="codeLibWindowNaviCover codeLibWindowNaviCoverNormal codeLibBaseCursorPointer" title="#{strCoverNormalTitle}"></li>';}
									tmplstr += '</ul>';
								}
							tmplstr += '</div>';
						tmplstr += '</span>';

						tmplstr += '<span class="codeLibWindowHeaderBottomRight unselect"></span>';
					tmplstr += '</div>';
				tmplstr += '</div>';
				tmplstr += '<div class="codeLibWindowBody">';
					tmplstr += '<div class="codeLibWindowBodyTop clearfix">';
						tmplstr += '<span class="codeLibWindowBodyTopLeft unselect" style="height : #{numHeight}px;"></span>';
						tmplstr += '<span class="codeLibWindowBodyTopMiddle codeLibBaseCursorPointer" style="width : #{windowBodyWidth}px; height : #{numHeight}px;"><span class="codeLibWindowBodyTopMiddleWrap" style="width : #{windowBodyWidth}px; height : #{numHeight}px;"><span class="codeLibWindowBoot" style="width : #{windowBodyWidth}px; height : #{numHeight}px;"></span></span></span>';
						tmplstr += '<span class="codeLibWindowBodyTopRight unselect" style="height : #{numHeight}px;"></span>';
					tmplstr += '</div>';
					tmplstr += '<div class="codeLibWindowBodyBottom clearfix">';
						tmplstr += '<span class="codeLibWindowBodyBottomLeft unselect"></span>';
						tmplstr += '<span class="codeLibWindowBodyBottomMiddle unselect" style="width : #{windowBodyWidth}px;"></span>';
						tmplstr += '<span class="codeLibWindowBodyBottomRight unselect"></span>';
					tmplstr += '</div>';
				tmplstr += '</div>';
			tmplstr += '</div>';
		var data=tmplstr.interpolate(obj);
		return data;
	}

});
<?php }
}
?>