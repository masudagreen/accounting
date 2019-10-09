<?php /* Smarty version 3.1.24, created on 2016-08-18 12:50:22
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/tableTree.js" */ ?>
<?php
/*%%SmartyHeaderCode:143371815257b5af0ebfad50_71519527%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'aa71960057930e2cad27e6cacd7f85aea9e307ce' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/tableTree.js',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '143371815257b5af0ebfad50_71519527',
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b5af0ecf5471_42015928',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b5af0ecf5471_42015928')) {
function content_57b5af0ecf5471_42015928 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '143371815257b5af0ebfad50_71519527';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_TableTree = Class.create(Code_Lib_ExtLib,
{

	/**
	 *
	*/
	iniLoad : function(obj)
	{

		this._extAllot(obj);
		this._iniVars(obj);
		this._iniListener();
		this._iniWrap();
		this._iniFormat();
		this._iniColumn();
		this._iniLine();
		this._iniMove();
		this._iniBtn();
		this._iniPage();
		this._iniBtnBottom();
		this._iniBtnSelect();
		this._iniPosition();
	},

	/**
	 *
	*/
	iniReload : function()
	{
		this.stopListener();
		this.removeWrap();
		this.removeBtnBottom();
		this._iniCake();
		this._iniWrap();
		this._iniFormat();
		this._iniColumn();
		this._iniLine();
		this._iniMove();
		this._iniBtn();
		this._iniPage();
		this._iniBtnBottom();
		this._iniBtnSelect();
		this._iniPosition();
		this.setScroll();
	},

	/**
	 *
	*/
	_iniVars : function(obj)
	{
		this._extVars(obj);
		this._iniCake();
		this._iniVarsBtn();
		this.allot({
			insCurrent : this.insCurrent,
			from       : '_iniVars'
		});
	},

	/**
	 * Page
	*/
	_iniPage : function()
	{
		this._extPage();
	},

	/**
	 *
	*/
	_iniListener : function()
	{
		this._extListener();
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
		if (!obj.data) return;
		if (this.vars.varsStatus.flagCakeColumnUse) {
			this._getCakeVarsUpdateColumn({data : obj.data});
		}
/*
		if (this.vars.varsStatus.flagCakeTreeUse) {
			this._getCakeVarsUpdateDetail({data : ''});
		}
*/
	},

	/**
	 *
	*/
	_getCakeVarsUpdateColumn : function(obj)
	{
		obj.arr = this.vars.varsColumn;
		for (var i = 0; i < obj.arr.length; i++) {
			str = 'columnWidth' + obj.arr[i].id ;
			if (obj.data[str]) {
				obj.arr[i].numWidth  = parseFloat(obj.data[str]);
				str = 'columnSort' + obj.arr[i].id ;
				obj.arr[i].numSort  = parseFloat(obj.data[str]);
				str = 'columnFlagCheckNow' + obj.arr[i].id ;
				obj.arr[i].flagCheckNow  = parseFloat(obj.data[str]);
			}
		}
	},

	/**
	 *
	_getCakeVarsUpdateDetail : function(obj)
	{

	},
	*/

	setCake : function()
	{
		if (!this.insRoot.insCake) return;
		if (!this.vars.varsStatus.flagCakeUse) return;
		this._varsCake = {};

		if (this.vars.varsStatus.flagCakeColumnUse) {
			this._setCakeVarsColumn({arr : this.vars.varsColumn});
		}
/*
		if (this.vars.varsStatus.flagCakeTreeUse) {
			this._setCakeVarsDetail({arr : this.vars.varsDetail});
		}
*/

		this.insRoot.insCake.setStorageCake({
			parentKey  : this.idSelf,
			value      : this._varsCake,
			numExpires : 0
		});
	},

	/**
	 *
	*/
	_setCakeVarsColumn : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			str = 'columnWidth' + obj.arr[i].id ;
			this._varsCake[str] = obj.arr[i].numWidth;
			str = 'columnSort' + obj.arr[i].id ;
			this._varsCake[str] = obj.arr[i].numSort;
			str = 'columnFlagCheckNow' + obj.arr[i].id ;
			this._varsCake[str] = obj.arr[i].flagCheckNow;
		}
	},

	/**
	 *
	_setCakeVarsDetail : function(obj)
	{

	},
	*/

	/**
	 * Btn
	*/
	_varsBtn : null,
	_iniVarsBtn : function()
	{
		this._varsBtn = [];
	},

	/**
	 *
	*/
	resetBtnVars : function()
	{
		this._varsBtn = [];
	},

	/**
	 * Wrap
	*/
	_staticWrap : {numWidthCheck : 26},
	_iniWrap : function()
	{
		this._extWrap();
		this.eleWrap.style.width = this._getWrapWidth({
			arr : this.vars.varsColumn
		}) + 'px';
		this.eleWrap.style.height = (this._getWrapHeight() - 1) + 'px';
	},

	/**
	 *
	*/
	_getWrapWidth : function(obj)
	{
		var numWidthA = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagCheckNow) continue;
			numWidthA += obj.arr[i].numWidth;
		}

		numWidthA += this._staticWrap.numWidthCheck;
		var array = this.eleInsert.style.width.split('px');
		var numWidthB = parseFloat(array[0]);
		var data = (numWidthA > numWidthB)? numWidthA + this._staticColumn.numBar : numWidthB;

		return  data;
	},

	/**
	 * Format
	*/
	_iniFormat : function()
	{
		this._extFormat();
	},

	/**
	 * Column
	*/
	insColumn : null,
	_iniColumn : function()
	{
		this._wrapColumn();
		this.vars.varsColumn = this.vars.varsColumn.sortBy(function(v, i) {
			return v.numSort;
		});
		this._setColumn({arr : this.vars.varsColumn});
	},

	/**
	 *
	*/
	_staticColumn : {numBar : 17, numHeight : 16, numWidth : 5, numOther : 10, numBlock : 16, numMin : 26},
	eleWrapColumn : null,
	_wrapColumn : function()
	{
		var ele = $(document.createElement('div'));
		ele.addClassName('codeLibTableTreeColumnWrap');
		ele.unselectable = 'on';
		ele.addClassName('unselect');
		this.insFormat.eleTemplate.header.down('.codeLibBaseMarginLeftFive', 0).insert(ele);
		this.eleWrapColumn = ele;
	},

	/**
	 *
	*/
	_getColumnWidth : function()
	{
		var array = this.eleWrap.style.width.split('px');
		var data = parseFloat(array[0]) - this._staticColumn.numBar;

		return data;
	},

	/**
	 *
	*/
	_setColumn : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {

			if (!obj.arr[i].flagCheckNow) continue;
			var eleColumn = $(document.createElement('div'));
			this.eleWrapColumn.insert(eleColumn);
			eleColumn.addClassName('codeLibTableTreeColumnBoxWrap');
			eleColumn.addClassName('codeLibTableTreeColumnBox');
			eleColumn.id = this.idSelf + 'ColumnBox' + obj.arr[i].id;
			eleColumn.addClassName('clearfix');
			eleColumn.unselectable = 'on';
			eleColumn.addClassName('unselect');
			if (obj.arr[i].id != 'Tree') eleColumn.title = obj.arr[i].strTitle;

			if (obj.arr[i].id == 'Tree') {
				if (this.vars.varsStatus.flagFoldUse) {
					var ele = $(document.createElement('span'));
					ele.addClassName('codeLibTableTreeFold');
					if (this.vars.varsStatus.flagFoldUse) {
						if (this.vars.varsStatus.flagLockNow) {
							ele.addClassName('codeLibBaseCursorDefault');

						} else {
							ele.addClassName('codeLibBaseCursorPointer');
						}

						if (!this.vars.varsStatus.flagFoldNow) ele.addClassName('codeLibTableTreeFoldClose');
						else  ele.addClassName('codeLibTableTreeFoldOpen');
					}
					ele.unselectable = 'on';
					ele.addClassName('unselect');
					eleColumn.insert(ele);
					this.insListener.set({
						bindAsEvent : 1, insCurrent : this, event : 'mousedown',
						strFunc : '_mousedownBarFold', ele : ele, vars : ''
					});
				}
				if (this.vars.varsStatus.flagMenuUse) {
					var eleMenu = $(document.createElement('span'));
					eleMenu.addClassName('codeLibTableTreeColumn');
					eleMenu.addClassName('codeLibTableTreeColumnBoxImgMenu');
					eleMenu.addClassName('codeLibBaseCursorPointer');
					eleMenu.unselectable = 'on';
					eleMenu.addClassName('unselect');
					eleColumn.insert(eleMenu);

					this.insListener.set({
						bindAsEvent : 1, insCurrent : this, event : 'mousedown',
						strFunc : '_mousedownMenu', ele : eleMenu, vars : {}
					});
				}

			}

			var eleSortColumn = $(document.createElement('span'));
			eleSortColumn.addClassName('codeLibTableTreeColumnBoxSortColumn');
			eleSortColumn.setStyle({
				height : this._staticColumn.numHeight + 'px',
				width  : this._staticColumn.numWidth + 'px'
			});
			eleColumn.insert(eleSortColumn);

			/*sortLine*/
			var eleSort = $(document.createElement('span'));
			eleSort.addClassName('codeLibTableTreeColumnBoxSortLine');
			eleColumn.insert(eleSort);

			var eleCheckbox = $(document.createElement('span'));
			eleColumn.insert(eleCheckbox);
			if (obj.arr[i].flagType == 'checkbox' && obj.arr[i].flagAllCheckbox) {
				this._iniCheckboxColumn({
					id        : this.idSelf + 'ColumnBox' + obj.arr[i].id + 'Checkbox',
					vars      : obj.arr[i],
					eleInsert : eleCheckbox
				});
			}

			var eleTitle = $(document.createElement('span'));
			eleColumn.insert(eleTitle);
			eleTitle.addClassName('codeLibTableTreeColumnBoxTitle');
			eleTitle.insert(obj.arr[i].strTitle);
			if (obj.arr[i].flagCheckNow) {
				if (this.vars.varsStatus.flagSortColumnUse && obj.arr[i].flagSortColumn) {
					eleTitle.removeClassName('codeLibBaseCursorDefault');
					eleTitle.addClassName('codeLibBaseCursorMove');
				}
				else eleTitle.addClassName('codeLibBaseCursorDefault');
			}
			var eleResize = $(document.createElement('span'));
			eleResize.addClassName('codeLibTableTreeColumnBoxResize');
			eleResize.setStyle({
				height : this._staticColumn.numHeight + 'px',
				width  : this._staticColumn.numWidth + 'px'
			});
			if (obj.arr[i].flagCheckNow) {
				if (this.vars.varsStatus.flagResizeUse) {
					eleResize.addClassName('codeLibBaseCursorColResize');
				}
			}
			eleColumn.insert(eleResize);
			if (this.vars.varsStatus.flagResizeUse && obj.arr[i].flagCheckNow) {
				this.insListener.set({
					bindAsEvent : 1, insCurrent : this, event : 'mousedown',
					strFunc : '_mousedownResize', ele : eleResize, vars : { vars : obj.arr[i] }
				});
			}
			if (obj.arr[i].flagType == 'checkbox' && obj.arr[i].flagAllCheckbox) {
				eleTitle.setStyle({
					width : (obj.arr[i].numWidth - this._staticColumn.numBlock - this._staticColumn.numOther) + 'px'
				});

			} else {
				eleTitle.setStyle({width : (obj.arr[i].numWidth - this._staticColumn.numOther) + 'px'});
			}

			if (obj.arr[i].flagCheckNow && this.vars.varsStatus.flagSortColumnUse && obj.arr[i].flagSortColumn) {
				this.insListener.set({
					bindAsEvent : 1, insCurrent : this, event : 'mousedown',
					strFunc : '_mousedownSortColumn', ele : eleTitle, vars : { vars : obj.arr[i] }
				});
				this.insListener.set({
					bindAsEvent : 0, insCurrent : this, event : 'mouseover',
					strFunc : '_mouseoverSortColumn', ele : eleColumn, vars : { vars : obj.arr[i] }
				});
				this.insListener.set({
					bindAsEvent : 0, insCurrent : this, event : 'mouseout',
					strFunc : '_mouseoutSortColumn', ele : eleColumn, vars : { vars : obj.arr[i] }
				});
			}
		}
	},

	/**
	 *
	*/
	_mousedownBarFold : function(evt, obj)
	{
		if (obj) evt.stop();
		else obj = evt;
		this._updateBarFoldVar();
		this._updateBarFoldStyle();
		this.setCake();
	},

	/**
	 *
	*/
	_updateBarFoldVar : function()
	{
		if (this.vars.varsStatus.flagFoldNow) {
			this.vars.varsStatus.flagFoldNow = 0;
			this._updateFoldVarAll({
				arr         : this.vars.varsDetail,
				flagFoldNow : 0
			});

		} else {
			this.vars.varsStatus.flagFoldNow = 1;
			this._updateFoldVarAll({
				arr         : this.vars.varsDetail,
				flagFoldNow : 1
			});
		}
	},


	/**
	 *
	*/
	_updateFoldVarAll : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {

			if (obj.arr[i].flagFoldUse) {
				obj.arr[i].flagFoldNow = obj.flagFoldNow;
			}
			if (obj.arr[i].child.length) {
				this._updateFoldVarAll({arr : obj.arr[i].child, flagFoldNow : obj.flagFoldNow});
			}
		}
	},

	/**
	 *
	*/
	_updateBarFoldStyle : function()
	{
		var ele = this.eleWrapColumn.down('.codeLibTableTreeFold', 0);
		ele.removeClassName('codeLibTableTreeFoldOpen');
		ele.removeClassName('codeLibTableTreeFoldClose');
		if (this.vars.varsStatus.flagFoldNow) ele.addClassName('codeLibTableTreeFoldOpen');
		else ele.addClassName('codeLibTableTreeFoldClose');
		$(this.insRoot.vars.varsSystem.id.temp).insert(this.eleTree);
		this._setFoldUpdateStyleAll({
			arr        : this.vars.varsDetail,
			flagEffect : 0
		});
		this._setFoldRestoreStyleAll({
			arr : this.vars.varsDetail
		});
		this.insFormat.eleTemplate.body.insert(this.eleTree);
		this._setFoldUpdateStyleAll({
			arr        : this.vars.varsDetail,
			flagEffect : 1
		});
	},


	/**
	 *
	*/
	_setFoldUpdateStyleAll : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {

			if (obj.arr[i].flagFoldUse) {
				if (obj.flagEffect && $(obj.arr[i].id).down('.codeLibTableTreeFold', 0)) {
					$(obj.arr[i].id).down('.codeLibTableTreeFold', 0).removeClassName('codeLibTableTreeFoldOpen');
					$(obj.arr[i].id).down('.codeLibTableTreeFold', 0).removeClassName('codeLibTableTreeFoldClose');
					if (obj.arr[i].flagFoldNow) {
						$(obj.arr[i].id).down('.codeLibTableTreeFold', 0).addClassName('codeLibTableTreeFoldOpen');
						$(obj.arr[i].id).next('ul', 0).show();
					} else {
						$(obj.arr[i].id).down('.codeLibTableTreeFold', 0).addClassName('codeLibTableTreeFoldClose');
						$(obj.arr[i].id).next('ul', 0).hide();
					}
				} else {
					if (obj.arr[i].flagFoldNow) $(obj.arr[i].id).next('ul', 0).show();
					else $(obj.arr[i].id).next('ul', 0).hide();
				}
			}
			if (obj.arr[i].child.length) {
				this._setFoldUpdateStyleAll({
					arr        : obj.arr[i].child,
					flagEffect : obj.flagEffect
				});
			}
		}

		return obj.arr;
	},


	/**
	 *
	*/
	_setFoldRestoreStyleAll : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {

			if (!obj.arr[i].flagFoldNow) $(obj.arr[i].id).next('ul', 0).show();
			else $(obj.arr[i].id).next('ul', 0).hide();
			if (obj.arr[i].child.length) this._setFoldRestoreStyleAll({arr:obj.arr[i].child});
		}

		return obj.arr;
	},

	/**
	 * Menu
	*/
	_mousedownMenu : function(evt) {
		evt.stop();
		this._setMenu();
	},

	/**
	 *
	*/
	insMenu : null,
	_setMenu : function()
	{
		var cut = this.vars.varsContext;
		cut.varsDetail = this.vars.varsColumn;
		cut.varsStatus.numTop = $(this.idSelf).up('.codeLibWindow',0).offsetTop + this.eleInsert.offsetTop;
		cut.varsStatus.numLeft = $(this.idSelf).up('.codeLibWindow',0).offsetLeft + this.eleInsert.offsetLeft;
		this.insMenu = new Code_Lib_Context({
			insRoot    : this.insRoot,
			eleInsert  : $(this.insRoot.vars.varsSystem.id.root),
			insCurrent : this,
			idSelf     : this.idSelf + 'Menu',
			allot      : this._getMenuAllot(),
			vars       : cut
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
				insCurrent.setCake();

			} else if (obj.from == 'getVars') {
				insCurrent._updateMenuColumn({
					vars : obj.vars
				});
			}
		};

		return allot;
	},

	/**
	 *
	*/
	_updateMenuColumn : function(obj)
	{
		this.vars.varsColumn = obj.vars;
		this.setCake();
		this.iniReload();
	},

	/**
	 *
	*/
	_iniPosition : function()
	{
		this.eleInsert.setStyle({
			overflow : 'hidden',
			position : 'relative'
		});
		this._iniPositionColumn();
		this._iniPositionLine();
	},

	/**
	 *
	*/
	elePositionLine : null,
	_iniPositionLine : function()
	{
		var ele = $(document.createElement('div'));
		ele.setStyle({
			position : 'absolute',
			overflow : 'auto',
			top      : this._staticPosition.numHeight_ + 'px',
			width    : (this._getLineWrapWidth()) + 'px',
			height   : this._getLineWrapHeight() + 'px'
		});
		ele.insert(this.eleWrapLine);
		this.eleWrap.insert(ele);
		this.elePositionLine = ele;
		this.insListener.set({
			flagType15 : 1, bindAsEvent : 1, insCurrent : this, event : 'scroll',
			strFunc : '_scrollPosition', ele : ele, vars : ''
		});
	},

	/**
	 *
	*/
	_getPositionElePositionColumn : function()
	{
		var array = this.elePositionColumn.style.width.split('px');
		var data = parseFloat(array[0]);

		return data;
	},

	/**
	 *
	*/
	_scrollPosition : function()
	{
		var numLeft = this.elePositionLine.scrollLeft;
		var numTop = this.elePositionLine.scrollTop;
		this.elePositionColumn.setStyle({
			width : (this._getPositionElePositionColumn() + numLeft) + 'px',
			left  : (-1 * numLeft) + 'px'
		});
	},

	/**
	 *
	*/
	_staticPosition : {numHeight : 16, numHeight_ : 25, numBottom : 4, numTop : 4, numMargin : 5},
	elePositionColumn : null,
	_iniPositionColumn : function()
	{
		var array = this.eleWrap.style.width.split('px');
		this.eleWrapColumn.setStyle({
			width : (array[0]) + 'px'
		});
		var ele = $(document.createElement('div'));
		ele.setStyle({
			position   : 'absolute',
			overflow   : 'hidden',
			marginLeft : this._staticPosition.numMargin + 'px',
			left       : '0px',
			top        : this._staticPosition.numTop + 'px',
			width      : this._getLineWrapWidth() + 'px',
			height     : this._staticPosition.numHeight + 'px'
		});
		ele.insert(this.eleWrapColumn);
		this.eleWrap.insert(ele);
		this.elePositionColumn = ele;
	},

	/**
	 *
	*/
	_getLineWrapWidth : function()
	{
		var array = this.eleInsert.style.width.split('px');
		var data = parseFloat(array[0]);

		return data;
	},

	/**
	 *
	*/
	_getLineWrapHeight : function()
	{
		var array = this.eleInsert.style.height.split('px');
		var num = this._staticPosition.numHeight_ + this._staticPosition.numBottom;
		var data = parseFloat(array[0]) - num;

		return data;
	},

	/**
	 *
	*/
	updateVars : function()
	{
		this.allot({
			insCurrent : this.insCurrent,
			from       : 'updateVars',
			vars   : {
				vars       : this.vars,
				varsSelect : this._varsBtn
			}
		});
	},

	/**
	 *
	*/
	_varsResize : {},
	_setResizeListener : function(obj)
	{
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
	_mousedownResize : function(evt,obj) {
		if (obj) evt.stop();
		else obj = evt;
		this._setResizeListener();
		this._removeCursorColumn({arr : this.vars.varsColumn});
		var max = 0;
		var after = {};
		var flagLast = this._checkResizeSortLast({
			arr : this.vars.varsColumn,
			id  : obj.vars.id
		});
		if (!flagLast) {
			after = this._checkResizeSortAfter({
				arr : this.vars.varsColumn,
				id  : obj.vars.id
			});
			max = evt.pointerX() + after.numWidth - after.numWidthMin;
		}

		this._varsResize = {};
		this._varsResize = {
			flag         : 1,
			ele          : evt.element(),
			varsAfter    : after,
			vars         : obj.vars,
			numLeft      : evt.pointerX(),
			numLeftMin   : evt.pointerX() - obj.vars.numWidth + obj.vars.numWidthMin,
			numLeftMax   : max,
			mouseLeft    : evt.pointerX(),
			naviBarXLeft : evt.pointerX(),
			eleBarX      : null,
			eleLock      : null
		};
		this._setResizeNavi({evt : evt});
	},

	/**
	 *
	*/
	_removeCursorColumn : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagCheckNow) continue;
			if (this.vars.varsStatus.flagSortColumnUse) {
				var ele = $(this.idSelf + 'ColumnBox' + obj.arr[i].id).down('.codeLibTableTreeColumnBoxTitle', 0);
				ele.removeClassName('codeLibBaseCursorMove');
				ele = $(this.idSelf + 'ColumnBox' + obj.arr[i].id).down('.codeLibTableTreeColumnBoxTitle', 0);
				ele.addClassName('codeLibBaseCursorDefault');
			}
			if (this.vars.varsStatus.flagResizeUse) {
				var ele = $(this.idSelf + 'ColumnBox' + obj.arr[i].id).down('.codeLibTableTreeColumnBoxResize', 0);
				ele.removeClassName('codeLibBaseCursorColResize');
			}
		}
	},

	/**
	 *
	*/
	_checkResizeSortLast : function(obj)
	{
		var num = obj.arr.length - 1;
		for (var i = num; i >= 0; i--) {
			if (!obj.arr[i].flagCheckNow) continue;
			if (obj.arr[i].id == obj.id) return 1;
			else  return 0;
		}
	},

	/**
	 *
	*/
	_checkResizeSortAfter : function(obj)
	{
		var flag = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagCheckNow) continue;
			if (flag) {
				return obj.arr[i];

			} else {
				if (obj.arr[i].id == obj.id) flag = 1;
			}
		}
	},

	/**
	 *
	*/
	_setResizeNavi : function(obj)
	{
		var ele = $(document.createElement('span'));
		var viewSize = document.viewport.getDimensions();
		var scroll = document.viewport.getScrollOffsets();
		var zIndex = this.insRoot.setZIndex();
		/*lock*/
		var eleLock = $(document.createElement('div'));
		eleLock.addClassName('lockView');
		eleLock.setStyle({
			zIndex : zIndex
		});
		$(this.insRoot.vars.varsSystem.id.root).insert(eleLock);
		this._varsResize.eleLock = eleLock;
		ele.setStyle({
			height : viewSize.height + scroll.top + 'px',
			top    : '0px',
			left   : obj.evt.pointerX() + 'px',
			zIndex : zIndex
		});
		ele.addClassName('codeLibTableTreeNaviX');
		this._varsResize.eleBarX = ele;
		$(this.insRoot.vars.varsSystem.id.root).insert(ele);
	},

	/**
	 *
	*/
	_getResizeLeftMin : function(evt)
	{
		var data = (this._varsResize.numLeftMin > evt.pointerX()) ? 1  : 0;

		return  data;
	},

	/**
	 *
	*/
	_getResizeLeftMax : function(evt)
	{
		var data = (this._varsResize.numLeftMax < evt.pointerX()) ? 1  : 0 ;

		return data;
	},

	/**
	 *
	*/
	_mousemoveResize : function(evt, obj)
	{
		if (!this._varsResize.flag) return;
		if (obj) evt.stop();
		else obj = evt;

		if (this._getResizeLeftMin(evt)) {
			this._varsResize.eleBarX.setStyle({
				left : this._varsResize.numLeftMin + 'px',
				top  : '0px'
			});
			this._varsResize.naviBarXLeft = this._varsResize.numLeftMin;

		} else {
			this._varsResize.eleBarX.setStyle({
				left : evt.pointerX() + 'px',
				top  : '0px'
			});
			this._varsResize.naviBarXLeft = evt.pointerX();
		}

	},

	/**
	 *
	*/
	_getResizeCalc : function()
	{
		var cut = this._varsResize;
		var difX = cut.naviBarXLeft - cut.numLeft;
		cut.vars.numWidth += difX;
		cut.vars.numWidth = (cut.vars.numWidthMin > cut.vars.numWidth)? cut.vars.numWidthMin : cut.vars.numWidth;
	},

	/**
	 *
	*/
	_updateResize : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == this._varsResize.vars.id) {
				obj.arr[i].numWidth = this._varsResize.vars.numWidth;
			}
		}
	},

	/**
	 *
	*/
	_mouseupResize : function(evt, obj)
	{
		if (obj) evt.stop();
		else obj = evt;
		if (!this._varsResize.flag) return;
		this._getResizeCalc();
		this._updateResize({arr : this.vars.varsColumn});
		this.setCake();
		this._varsResize.eleBarX.remove();
		this._varsResize.eleLock.remove();
		this._varsResize = {};
		this.iniReload({from : '_mouseupResize'});
	},

	/**
	 *
	*/
	_staticLine : {numIdle : 10, numLength : 25, numIdleFive : 5, numHeight : 16, numBlock : 16, numPadding : 5},
	_iniLine : function()
	{
		this._setLineWrap();

		this.setLineId({
			idParent : this.idSelf,
			arr      : this.vars.varsDetail
		});

		this._iniCake();

		if (!this.vars.varsDetail.length) {
			this.eleWrapLine.setStyle({
				width  : this._getColumnWidth() + 'px',
				height : '1px'
			});

		} else {
			var temp = this.vars.varsHtml.interpolate({idSelf : this.idSelf});
			this.eleWrapLine.insert(temp);
			this._setLine({
				idParent  : this.idSelf,
				arrColumn : this.vars.varsColumn,
				arr       : this.vars.varsDetail
			});
		}
		this.insFormat.eleTemplate.body.insert(this.eleWrapLine);
	},

	/**
	 *
	*/
	eleWrapLine : null,
	_setLineWrap : function()
	{
		var ele = $(document.createElement('div'));
		ele.addClassName('codeLibTableTreeLineWrap');
		$(this.insRoot.vars.varsSystem.id.temp).insert(ele);
		this.eleWrapLine = ele;

	},

	/**
	 *
	*/
	setLineId : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			obj.arr[i].id = obj.idParent + '-' + i;
			if (obj.arr[i].child.length) {
				this.setLineId({
					arr      : obj.arr[i].child,
					idParent : obj.arr[i].id
				});
			}
		}
	},

	/**
	 *
	*/
	_getLineWidth : function()
	{
		var array = this.eleWrap.style.width.split('px');
		var data = parseFloat(array[0]);

		return data;
	},

	/**
	 *
	*/
	_setLine : function(obj)
	{
		if (!obj.parent) {
			obj.parent = this.eleWrapLine.down('.codeLibTableTreeLineTop', 0);
		}

		var insEscape = new Code_Lib_Escape();
		for (var i = 0; i < obj.arr.length; i++) {
			obj.arr[i].id = obj.idParent + '-' + i;

			/*btnLine*/
			var eleLine = $(obj.arr[i].id);
			eleLine.setStyle({
				width : (this._getLineWidth()) + 'px'
			});

			var eleLineSort = $(obj.arr[i].id + '_eleLineSort');
			eleLineSort.setStyle({
				width : this._getLineWidth() + 'px'
			});

			var eleUl = $(obj.arr[i].id + '_eleUl');
			if (this.vars.varsStatus.flagBtnUse && obj.arr[i].flagBtnUse) {

				this.insListener.set({
					bindAsEvent : 1, insCurrent : this, event : 'dblclick',
					strFunc : '_dblclickBtn', ele : eleLine, vars : { vars : obj.arr[i] }
				});
				this.insListener.set({
					bindAsEvent : 1, insCurrent : this, event : 'mousedown',
					strFunc : '_mousedownBtn', ele : eleLine, vars : { vars : obj.arr[i] }
				});
				this.insListener.set({
					bindAsEvent : 0, insCurrent : this, event : 'mouseover',
					strFunc : '_mouseoverBtn', ele : eleLine, vars : { vars : obj.arr[i] }
				});
				this.insListener.set({bindAsEvent : 0, insCurrent : this, event : 'mouseout',
					strFunc : '_mouseoutBtn', ele : eleLine, vars : { vars : obj.arr[i] }
				});
				eleLine.observe('contextmenu', Event.stop);
			}

			for (var j = 0; j < obj.arrColumn.length; j++) {
				var eleWrapItem = $(obj.arr[i].id + '_eleWrapItem_' + obj.arrColumn[j].id);

				if (!obj.arrColumn[j].flagCheckNow) {
					eleWrapItem.hide();
				}

				var eleItemIdle = $(obj.arr[i].id + '_eleItemIdle_' + obj.arrColumn[j].id);
				var eleItem = $(obj.arr[i].id + '_eleItem_' + obj.arrColumn[j].id);

				var numWidthEleItem = 0;
				if (obj.arrColumn[j].id == 'Tree') {

					var eleSortSepatate = $(obj.arr[i].id + '_eleSortSepatate_' + obj.arrColumn[j].id);
					eleSortSepatate.setStyle({width : (obj.arrColumn[j].numWidth + this._staticLine.numBlock*2) + 'px'});

					numWidthEleItem = obj.arrColumn[j].numWidth - this._staticLine.numIdleFive + this._staticLine.numBlock * 2;
					eleItem.setStyle({
						width : numWidthEleItem + 'px'
					});

					eleItemIdle.setStyle({
						width  : this._staticLine.numIdleFive + 'px',
						height : this._staticLine.numHeight + 'px',
					});

					if (obj.arr[i].flagFoldUse && this.vars.varsStatus.flagFoldUse && obj.arr[i].child.length) {
						var eleFold = $(obj.arr[i].id + '_eleFold_' + obj.arrColumn[j].id);
						eleFold.removeClassName('codeLibTableTreeFoldOpen');
						eleFold.removeClassName('codeLibTableTreeFoldClose');
						if (obj.arr[i].flagFoldNow) {
							eleFold.addClassName('codeLibTableTreeFoldOpen');

						} else {
							eleUl.style.display = 'none';
							eleFold.addClassName('codeLibTableTreeFoldClose');
						}
						this.insListener.set({
							bindAsEvent : 1, insCurrent : this, event : 'mousedown',
							strFunc : '_mousedownFold', ele : eleFold, vars : { vars : obj.arr[i]}
						});
					}

					var eleTitle = $(obj.arr[i].id + '_eleTitle_' + obj.arrColumn[j].id);
					if (this.vars.varsStatus.flagBtnUse && obj.arr[i].flagBtnUse) {
						this.insListener.set({
							bindAsEvent : 0, insCurrent : this, event : 'mouseover',
							strFunc : '_mouseoverTitle', ele : eleTitle, vars : { ele : eleTitle }
						});
						this.insListener.set({
							bindAsEvent : 0, insCurrent : this, event : 'mouseout',
							strFunc : '_mouseoutTitle', ele : eleTitle, vars : { ele : eleTitle }
						});
					}
					continue;

				}

				var eleSortSepatate = $(obj.arr[i].id + '_eleSortSepatate_' + obj.arrColumn[j].id);
				eleSortSepatate.setStyle({width : (obj.arrColumn[j].numWidth - this._staticLine.numIdle) + 'px'});

				var eleSeparateSort = $(obj.arr[i].id + '_eleSeparateSort_' + obj.arrColumn[j].id);

				eleLine.insert(eleWrapItem);
				eleLineSort.insert(eleSortSepatate);
				eleLineSort.insert( eleSeparateSort);

				if (!obj.arrColumn[j].flagCheckNow) {
					eleSortSepatate.hide();
					eleSeparateSort.hide();
				}

				var str = insEscape.strLowCapitalize({data : obj.arrColumn[j].id});
				numWidthEleItem = obj.arrColumn[j].numWidth - this._staticLine.numIdle;
				eleItem.setStyle({
					width : numWidthEleItem + 'px'
				});

				var eleTitle = $(obj.arr[i].id + '_eleTitle_' + obj.arrColumn[j].id);
				if (obj.arrColumn[j].flagType == 'str' || obj.arrColumn[j].flagType == 'stamp') {
					if (this.vars.varsStatus.flagBtnUse && obj.arr[i].flagBtnUse) {
						this.insListener.set({
							bindAsEvent : 0, insCurrent : this, event : 'mouseover',
							strFunc : '_mouseoverTitle', ele : eleTitle, vars : { ele : eleTitle }
						});
						this.insListener.set({bindAsEvent : 0, insCurrent : this, event : 'mouseout',
							strFunc : '_mouseoutTitle', ele : eleTitle, vars : { ele : eleTitle }
						});
					}
				}

			}
			if (obj.arr[i].child.length) {
				this._setLine({
					arr       : obj.arr[i].child,
					arrColumn : obj.arrColumn,
					idParent  : obj.arr[i].id,
					parent    : eleUl
				});
			}
		}
	},


	/**
	 *
	*/
	_dblclickBtn : function(evt, obj)
	{
		evt.stop();
		this.allot({
			insCurrent : this.insCurrent,
			from       : '_dblclickBtn',
			vars       : obj.vars
		});
	},

	/**
	 *
	*/
	_mousedownBtn : function(evt, obj)
	{
		if (obj) evt.stop();
		else obj = evt;

		this._removeBtnSelect({arr : this.vars.varsDetail});
		if (!this._varsBtn.length) {
			this._varsBtn.push(obj.vars);

		} else {
			this._varsBtn = [];
			this._varsBtn.push(obj.vars);
		}

		this._setBtnSelect({
			arr       : this.vars.varsDetail,
			arrSelect : this._varsBtn,
		});

		this.setCake();
		this.allot({
			insCurrent : this.insCurrent,
			from       : '_mousedownBtn',
			flag       : evt.isLeftClick(),
			vars       : this._varsBtn
		});

	},

	/**
	 *
	*/
	_removeBtnSelect : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			$(obj.arr[i].id).removeClassName('codeLibTableTreeBtnSelect');
			if (obj.arr[i].child.length) {
				this._removeBtnSelect({
					arr : obj.arr[i].child
				});
			}
		}
	},

	/**
	 *
	*/
	_setBtnSelect : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			for (var j = 0; j < obj.arrSelect.length; j++) {
				if (obj.arr[i].id == obj.arrSelect[j].id) {
					this._removeBtnSelectLoad({
						vars : obj.arr[i]
					});
					this._removeBtnSelectBold({
						arr  : this.vars.varsColumn,
						vars : obj.arr[i]
					});
					$(obj.arr[i].id).addClassName('codeLibTableTreeBtnSelect');
				}
			}
			if (obj.arr[i].child.length) {
				this._setBtnSelect({
					arr       : obj.arr[i].child,
					arrSelect : obj.arrSelect
				});
			}
		}
	},

	/**
	 *
	*/
	_removeBtnSelectLoad : function(obj)
	{
		if (obj.vars.strClassLoad == '') return;
		$(obj.vars.id).down('.codeLibTableTreeLineImg', 0).removeClassName(obj.vars.strClassLoad);
		obj.vars.strClassLoad = '';
		this.updateVars();
		$(obj.vars.id).down('.codeLibTableTreeLineImg', 0).addClassName(obj.vars.strClass);
	},

	/**
	 *
	*/
	_removeBtnSelectBold : function(obj)
	{
		if (!this.vars.varsStatus.flagBoldUse || !obj.vars.flagBoldNow) return;
		obj.vars.flagBoldNow = 0;
		this.updateVars();
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagCheckNow) continue;
			if ($(obj.vars.id).down('.codeLibTableTreeLineTitle', i)) {
				var ele = $(obj.vars.id).down('.codeLibTableTreeLineTitle', i);
				ele.removeClassName('codeLibBaseFontBold');
			}
		}
	},

	/**
	 *
	*/
	removeBtnSelect : function()
	{
		this._varsBtn = [];
		this._removeBtnSelect({arr : this.vars.varsDetail});
	},

	/**
	 *
	*/
	_mouseoverBtn : function(obj)
	{
		$(obj.vars.id).addClassName('codeLibTableTreeLineBtnOver');
	},

	/**
	 *
	*/
	_mouseoutBtn : function(obj)
	{
		$(obj.vars.id).removeClassName('codeLibTableTreeLineBtnOver');
	},


	/**
	 *
	*/
	_setSortColumnListener : function()
	{
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'mousemove',
			strFunc : '_mousemoveSortColumn', ele : document, vars : ''
		});
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'mouseup',
			strFunc : '_mouseupSortColumn', ele : document, vars : ''
		});
	},

	/**
	 *
	*/
	_varsSortColumn : {},
	_mousedownSortColumn : function(evt, obj) {
		if (obj) evt.stop();
		else obj = evt;
		this._setSortColumnListener();
		this._removeCursorColumn({arr : this.vars.varsColumn});
		this._varsSortColumn = {};
		this._varsSortColumn = {
			flag     : 1,
			ele      : evt.element(),
			eleNavi  : null,
			vars     : obj.vars,
			varsOver : null,
			varsNext : this._checkSortColumnNext({
				arr  : this.vars.varsColumn,
				vars : obj.vars
			})
		};
		this._setSortColumnNavi({
			vars : obj.vars,
			evt      : evt
		});
	},

	/**
	 *
	*/
	_checkSortColumnNext : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == obj.vars.id) {
				var flag = i + 1;
				if ( flag < obj.arr.length) {
					return obj.arr[flag];
				}
				return null;
			}
		}
	},

	/**
	 *
	*/
	_mouseoverSortColumn : function(obj)
	{
		if (!this._varsSortColumn.flag) return;
		this._varsSortColumn.varsOver = obj.vars;
		if (this._varsSortColumn.vars.id == this._varsSortColumn.varsOver.id) return;
		if (this._varsSortColumn.varsNext
			&& this._varsSortColumn.varsNext.id == this._varsSortColumn.varsOver.id) return;
		var ele = $(this.idSelf + 'ColumnBox' + obj.vars.id).down('.codeLibTableTreeColumnBoxSortColumn', 0);
		ele.addClassName('codeLibTableTreeSortColumnOver');
	},

	/**
	 *
	*/
	_mouseoutSortColumn : function(obj)
	{
		if (!this._varsSortColumn.flag) return;
		this._varsSortColumn.varsOver = '';
		var ele = $(this.idSelf + 'ColumnBox' + obj.vars.id).down('.codeLibTableTreeColumnBoxSortColumn', 0);
		ele.removeClassName('codeLibTableTreeSortColumnOver');
	},

	/**
	 *
	*/
	_staticSortColumn : {numNaviLeft : 15, numNaviTop : 5},
	_setSortColumnNavi : function(obj)
	{
		var zIndex = this.insRoot.setZIndex();
		var ele = $(this.idSelf + 'ColumnBox' + obj.vars.id).cloneNode(true);
		$(this.insRoot.vars.varsSystem.id.root).insert(ele);
		this._varsSortColumn.eleNavi = ele;
		ele.addClassName('codeLibTableTreeNavi');
		ele.setStyle({
			left   : (obj.evt.pointerX() + this._staticSortColumn.numNaviLeft) + 'px',
			top    : (obj.evt.pointerY() + this._staticSortColumn.numNaviTop) + 'px',
			zIndex : zIndex
		});
	},

	/**
	 *
	*/
	_mousemoveSortColumn : function(evt, obj) {
		if (!this._varsSortColumn.flag) return;
		if (obj) evt.stop();
		else obj = evt;


		if (evt.pointerY() < this._varsMoveScroll.numTopMin) {
			this.elePositionLine.scrollTop -= this._staticMove.numPosShift;
		} else if (evt.pointerY() > this._varsMoveScroll.numTopMax) {
			this.elePositionLine.scrollTop += this._staticMove.numPosShift;
		}

		if (evt.pointerX() < this._varsMoveScroll.numLeftMin) {
			this.elePositionLine.scrollLeft -= this._staticMove.numPosShift;
		} else if (evt.pointerX() > this._varsMoveScroll.numLeftMax) {
			this.elePositionLine.scrollLeft += this._staticMove.numPosShift;
		}

		this._varsSortColumn.eleNavi.setStyle({
			top  : (evt.pointerY() + this._staticSortColumn.numNaviTop) + 'px',
			left : (evt.pointerX() + this._staticSortColumn.numNaviLeft) + 'px'
		});
	},

	/**
	 *
	*/
	_iniMove : function()
	{
		this._varMove();
	},

	/**
	 *
	*/
	_varsMoveScroll : null,
	_varMove : function()
	{
		var numTopMin = $(this.idSelf).up('.codeLibWindow',0).offsetTop
						+ this.insFormat.eleTemplate.body.offsetTop
						+ this._staticMove.numAreaNone;
		var numTopMax = numTopMin
						+ this.insFormat.eleTemplate.body.offsetHeight
						- this._staticMove.numArea * 2;
		var numLeftMin = $(this.idSelf).up('.codeLibWindow',0).offsetLeft
						+ this.insFormat.eleTemplate.body.offsetLeft
						+ this._staticMove.numAreaNone;
		var numLeftMax = numLeftMin
						+ this.eleInsert.offsetWidth
						- this._staticMove.numArea * 2;
		this._varsMoveScroll = {
			numTopMin  : numTopMin,
			numTopMax  : numTopMax,
			numLeftMin : numLeftMin,
			numLeftMax : numLeftMax
		};
	},

	/**
	 *
	*/
	_updateSortColumn : function()
	{
		this._varsBlock = {};
		this._getBlock({
			arr      : this.vars.varsColumn,
			idTarget : this._varsSortColumn.vars.id
		});
		this.vars.varsColumn = this._removeBlock({
			arr      : this.vars.varsColumn,
			idTarget : this._varsSortColumn.vars.id
		});
		this.vars.varsColumn = this._setBlockColumn({
			arr    : this.vars.varsColumn,
			idTarget : this._varsSortColumn.varsOver.id,
			block  : this._varsBlock
		});
		this._updateSortColumnSort({arr : this.vars.varsColumn});
	},



	/**
	 *
	*/
	_setBlockColumn : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == obj.idTarget) num = i;
		}
		var array = [obj.block];
		obj.arr = obj.arr.slice(0, num).concat(array.concat(obj.arr.slice((num), obj.arr.length)));

		return obj.arr;
	},


	/**
	 *
	*/
	_updateSortColumnSort : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			obj.arr[i].numSort = i;
		}
	},

	/**
	 *
	*/
	_mouseupSortColumn : function(evt,obj) {
		if (obj) evt.stop();
		else obj = evt;
		if (!this._varsSortColumn.flag) return;
		if (this._varsSortColumn.varsOver) {
			if (this._varsSortColumn.vars.id != this._varsSortColumn.varsOver.id) {
				if (this._varsSortColumn.varsNext
				&& this._varsSortColumn.varsNext.id == this._varsSortColumn.varsOver.id) {}
 				else this._updateSortColumn();
			}
		}
		this.setCake();
		this._varsSortColumn.eleNavi.remove();
		this._varsSortColumn = {};
		this.iniReload({from : '_mouseupSortColumn'});
	},


	/**
	 *
	*/
	_iniBtn : function()
	{
		this._setBtnListener();
	},

	/**
	 *
	*/
	_setBtnListener : function()
	{
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'mousedown',
			strFunc : '_mousedownBtnRemove', ele : this.eleInsert, vars : ''
		});
	},

	/**
	 *
	*/
	_mousedownBtnRemove : function(evt)
	{
		evt.stop();
		if (!this._varsBtn.length) return;
		this._varsBtn = [];
		this.updateVars();
		this._removeBtnSelect({arr : this.vars.varsDetail});
	},

	/**
	 * BtnBottom
	*/
	_iniBtnBottom : function()
	{
		this._extBtnBottom();
	},

	/**
	 * BtnSelect
	*/
	_iniBtnSelect : function()
	{
		if (!this._varsBtn.length) return;
		this._removeBtnSelect({arr : this.vars.varsDetail});
		this._setBtnSelect({
			arr       : this.vars.varsDetail,
			arrSelect : this._varsBtn,
		});
	},

	/**
	 *
	*/
	_mousedownFold : function(evt, obj)
	{
		if (obj) evt.stop();
		else obj = evt;
		this._setFoldUpdate({arr : this.vars.varsDetail, vars : obj.vars});
		this._varsFold.now = 0;
		this._checkFoldNow({
			arr  : this.vars.varsDetail,
			vars : obj.vars
		});
		if (this._varsFold.now) {
			this.getScroll();
			$(this.insRoot.vars.varsSystem.id.root).insert(this.eleTree);
			this._setFoldUpdateStyle({
				arr        : this.vars.varsDetail,
				vars       : obj.vars,
				flagEffect : 0
			});
			this._setFoldRestoreStyle({
				arr  : this.vars.varsDetail,
				vars : obj.vars
			});
			this.insFormat.eleTemplate.body.insert(this.eleTree);
			this.setScroll();
		}
		this._setFoldUpdateStyle({
			arr        : this.vars.varsDetail,
			vars       : obj.vars,
			flagEffect : 1
		});
		this._mouseoutBtn({vars : obj.vars});
		this.setCake();
	},

	/**
	 *
	*/
	_varsFold : {now : 0, num : 0},
	_checkFoldNow : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {

			if (obj.arr[i].id == obj.vars.id) {
				this._varsFold.now = obj.arr[i].flagFoldNow;
				return;
			}
			if (obj.arr[i].child.length) this._checkFoldNow({arr : obj.arr[i].child, vars : obj.vars});
		}
	},

	/**
	 *
	*/
	_setFoldUpdateStyle : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == obj.vars.id) {
				var zIndex = this.insRoot.setZIndex();
				var idInsert = this.insRoot.vars.varsSystem.id.root;
				var insTree = this.insSelf;
				if (obj.flagEffect) {
					$(obj.arr[i].id).down('.codeLibTableTreeFold', 0).removeClassName('codeLibTableTreeFoldOpen');
					$(obj.arr[i].id).down('.codeLibTableTreeFold', 0).removeClassName('codeLibTableTreeFoldClose');
					if (obj.arr[i].flagFoldNow) {
						$(obj.arr[i].id).down('.codeLibTableTreeFold', 0).addClassName('codeLibTableTreeFoldOpen');
						new Effect.BlindDown($(obj.arr[i].id).next('ul', 0),{
							duration : 0.5
						});

					} else {
						$(obj.arr[i].id).down('.codeLibTableTreeFold', 0).addClassName('codeLibTableTreeFoldClose');
						new Effect.BlindUp($(obj.arr[i].id).next('ul', 0),{
							duration : 0.5
						});
					}

				} else {
					if (obj.arr[i].flagFoldNow) $(obj.arr[i].id).next('ul', 0).show();
					else $(obj.arr[i].id).next('ul', 0).hide();
				}

			}
			if (obj.arr[i].child.length) {
				this._setFoldUpdateStyle({
					arr        : obj.arr[i].child,
					vars       : obj.vars,
					flagEffect : obj.flagEffect
				});
			}
		}

		return obj.arr;
	},

	/**
	 *
	*/
	_setFoldUpdate : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {

			if (obj.arr[i].id == obj.vars.id) {
				if (obj.arr[i].flagFoldNow) obj.arr[i].flagFoldNow = 0;
				else obj.arr[i].flagFoldNow = 1;
			}
			if (obj.arr[i].child.length) this._setFoldUpdate({arr : obj.arr[i].child, vars : obj.vars});
		}

		return obj.arr;
	},


	/**
	 *
	*/
	_setFoldRestoreStyle : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {

			if (obj.arr[i].id == obj.vars.id) {
				if (!obj.arr[i].flagFoldNow) $(obj.vars.id).next('ul', 0).show();
				else $(obj.vars.id).next('ul', 0).hide();
			}
			if (obj.arr[i].child.length) {
				this._setFoldRestoreStyle({arr : obj.arr[i].child, vars : obj.vars});
			}
		}

		return obj.arr;
	},




	/**
	 *
	*/
	_staticMove : {
		numArea : 25, numAreaNone : 16, numNaviLeft : 15, numNaviTop : 5, numPosShift : 5, numTimer : 2000
	},

	/**
	 *
	*/
	_varsMoveScroll : null,


	/**
	 *
	*/
	_mouseoverTitle : function(obj)
	{
		obj.ele.addClassName('codeLibTableTreeLineTitleOver');
	},

	/**
	 *
	*/
	_mouseoutTitle : function(obj)
	{
		obj.ele.removeClassName('codeLibTableTreeLineTitleOver');
	},

	/**
	 *
	*/
	removeWrap : function()
	{
		this.eleInsert.setStyle({
			position : ''
		});
		$(this.idSelf).remove();
		if (this.eleInsertBtnLeft) this.eleInsertBtnLeft.innerHTML = '';
		if (this.eleInsertBtnRight) this.eleInsertBtnRight.innerHTML = '';
	}
});
<?php }
}
?>