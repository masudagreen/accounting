<?php /* Smarty version 3.1.24, created on 2019-06-16 09:02:08
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/table.js" */ ?>
<?php
/*%%SmartyHeaderCode:13132318075d06059013af27_81099919%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b6841a8858e87c9070ef3d09b3cfc66b49721b19' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/js/lib/table.js',
      1 => 1560675140,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '13132318075d06059013af27_81099919',
  'variables' => 
  array (
    'varsLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d060590145461_11321428',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d060590145461_11321428')) {
function content_5d060590145461_11321428 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '13132318075d06059013af27_81099919';
?>

/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
var Code_Lib_Table = Class.create(Code_Lib_ExtLib,
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
		this._iniWrap();
		this._iniFormat();
		this._iniColumn();
		this._iniLine();
		this._iniBtn();
		this._iniMove();
		this._iniPage();
		this._iniBtnBottom();
		this._iniBtnSelect();
		this._iniKey();
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
		this._iniWrap();
		this._iniFormat();
		this._iniCake();
		this._iniColumn();
		this._iniLine();
		this._iniBtn();
		this._iniMove();
		this._iniPage();
		this._iniBtnBottom();
		this._iniBtnSelect();
		this._iniKey();
		this._iniPosition();
	},

	/**
	 *
	*/
	iniReloadPage : function()
	{
		this.stopListenerPage();
		this.removeWrap();
		this._iniWrap();
		this._iniFormat();
		this._iniCake();
		this._iniColumn();
		this._iniLine();
		this._iniBtn();
		this._iniMove();
		this._iniBtnSelect();
		this._iniKey();
		this._iniPosition();
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
		if (this.vars.varsStatus.flagInnerPageUse) { num += this._staticPosition.numHeight_; }
		var data = parseFloat(array[0]) - num;

		return data;
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
	_iniListener : function()
	{
		this._extListener();
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
		ele.addClassName('codeLibTableNaviX');
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
		cut.vars.numWidth = (cut.vars.numWidthMin > cut.vars.numWidth)?
								  cut.vars.numWidthMin
								: cut.vars.numWidth;
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
		var str = 'statusFlagSortColumnLineNow';
		obj.arr = this.vars.varsColumn;
		this.vars.varsStatus.flagSortColumnLineNow = obj.data[str];
		for (var i = 0; i < obj.arr.length; i++) {
			str = 'columnWidth' + obj.arr[i].id ;
			if (obj.data[str]) {
				obj.arr[i].numWidth  = obj.data[str];
				str = 'columnSort' + obj.arr[i].id ;
				obj.arr[i].numSort  = obj.data[str];
				str = 'columnFlagCheckNow' + obj.arr[i].id ;
				obj.arr[i].flagCheckNow  = obj.data[str];
				str = 'columnFlagSortColumnLineNow' + obj.arr[i].id ;
				obj.arr[i].flagSortColumnLineNow  = obj.data[str];
			}
		}
	},

	/**
	 *
	*/
	_setCakeVars : function(obj)
	{
		var str = 'statusFlagSortColumnLineNow';
		this._varsCake[str] = this.vars.varsStatus.flagSortColumnLineNow;
		obj = {};
		obj.arr = this.vars.varsColumn;
		for (var i = 0; i < obj.arr.length; i++) {
			str = 'columnWidth' + obj.arr[i].id ;
			this._varsCake[str] = obj.arr[i].numWidth;
			str = 'columnSort' + obj.arr[i].id ;
			this._varsCake[str] = obj.arr[i].numSort;
			str = 'columnFlagCheckNow' + obj.arr[i].id ;
			this._varsCake[str] = obj.arr[i].flagCheckNow;
			str = 'columnFlagSortColumnLineNow' + obj.arr[i].id ;
			this._varsCake[str] = obj.arr[i].flagSortColumnLineNow;
		}
	},

	/**
	 * BtnBottom
	*/
	_iniBtnBottom : function()
	{
		this._extBtnBottom();
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
	_setMoveListener : function()
	{
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'mousemove',
			strFunc : '_mousemoveMove', ele : document, vars : ''
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
	_mousedownMove : function(evt,obj) {
		if (obj) evt.stop();
		else obj = evt;
		this._setMoveListener({
			arr : this.vars.varsDetail
		});
		this._removeCursorLine({arr : this.vars.varsDetail});
		this._varsMove = {};
		this._varsMove = {
			flag     : 1,
			ele      : evt.element(),
			vars     : obj.vars,
			varsOver : null,
			eleNavi  : null
		};
		this._setMoveNavi({
			vars : obj.vars,
			evt  : evt
		});
		this.allot({
			insCurrent : this.insCurrent,
			from       : '_mousedownMove',
			vars       : obj.vars
		});
	},

	/**
	 *
	*/
	_mouseoverMove : function(obj)
	{
		if (!this._varsMove.flag || !this.vars.varsStatus.flagMoveSortUse) return;
		this._varsMove.varsOver = obj.vars;
		var id = this.idSelf + 'Line' + obj.vars.id;
		$(id).next('.codeLibTableLineSort', 0).addClassName('codeLibTableMoveSortOver');
	},

	/**
	 *
	*/
	_mouseoutMove : function(obj)
	{
		if (!this._varsMove.flag || !this.vars.varsStatus.flagMoveSortUse) return;
		this._varsMove.varsOver = '';
		var id = this.idSelf + 'Line' + obj.vars.id;
		$(id).next('.codeLibTableLineSort',0).removeClassName('codeLibTableMoveSortOver');
	},

	/**
	 *
	*/
	_staticMove : {numArea : 25, numAreaNone : 16, numNaviLeft : 15, numNaviTop : 5, numPosShift : 5},
	_setMoveNavi : function(obj)
	{
		var zIndex = this.insRoot.setZIndex();
		var ele = $(this.idSelf + 'Line' + obj.vars.id).cloneNode(true);
		$(this.insRoot.vars.varsSystem.id.root).insert(ele);
		this._varsMove.eleNavi = ele;
		ele.addClassName('codeLibTableNavi');
		ele.removeClassName('codeLibTableLineBtnOver');
		ele.removeClassName('codeLibTableBtnSelect');
		ele.setStyle({
			top    : (obj.evt.pointerY() + this._staticMove.numNaviTop) + 'px',
			left   : (obj.evt.pointerX() + this._staticMove.numNaviLeft) + 'px',
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

		this._varsMove.eleNavi.setStyle({
			top  : (evt.pointerY() + this._staticMove.numNaviTop) + 'px',
			left : (evt.pointerX() + this._staticMove.numNaviLeft) + 'px'
		});
	},

	/**
	 *
	*/
	_updateMove : function()
	{
		this._varsBlock = {};
		this._getBlock({
			arr      : this.vars.varsDetail,
			idTarget : this._varsMove.vars.id
		});
		this.vars.varsDetail = this._removeBlock({
			arr      : this.vars.varsDetail,
			idTarget : this._varsMove.vars.id
		});
		this.vars.varsDetail = this._setBlockMove({
			arr      : this.vars.varsDetail,
			idTarget : this._varsMove.varsOver.id,
			block    : this._varsBlock
		});
		this._updateMoveSort({
			arr : this.vars.varsDetail
		});
	},

	/**
	 *
	*/
	_updateMoveSort : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			obj.arr[i].numSort = i;
		}
	},

	/**
	 *
	*/
	_mouseupMove : function(evt,obj) {
		if (obj) evt.stop();
		else obj = evt;
		if (!this._varsMove.flag) return;
		if (this._varsMove.varsOver) {
			if (this._varsMove.vars.id != this._varsMove.varsOver.id) this._updateMove();
		}
		this.setCake();
		this._varsMove.eleNavi.remove();
		this._varsMove = {};
		this._setCursorLine({arr : this.vars.varsDetail});
/*
		this.iniReload();
*/
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
		this.vars.varsContext = this._setMenuVars({
			arr  : (Object.toJSON(this.vars.varsColumn)).evalJSON(),
			vars : this.vars.varsContext
		});
		this.insMenu = new Code_Lib_Context({
			insRoot    : this.insRoot,
			eleInsert  : $(this.insRoot.vars.varsSystem.id.root),
			insCurrent : this.insSelf,
			idSelf     : this.idSelf + 'Menu',
			allot      : this._getMenuAllot(),
			vars       : this.vars.varsContext
		});
	},

	/**
	 *
	*/
	_setMenuVars : function(obj)
	{
		obj.vars.varsStatus.numTop = $(this.idSelf).up('.codeLibWindow', 0).offsetTop + this.eleInsert.offsetTop;
		obj.vars.varsStatus.numLeft = $(this.idSelf).up('.codeLibWindow', 0).offsetLeft + this.eleInsert.offsetLeft;

		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].flagType == 'checkbox') {
				obj.arr[i].strTitle = this.varsLoad.strSelect;
			}
		}
		obj.vars.varsDetail = obj.arr;

		return obj.vars;
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
	_mousedownSortColumnLine : function(evt, obj)
	{
		if (obj) evt.stop();
		else obj = evt;

		this.vars.varsStatus.flagSortColumnLineNow = obj.vars.id;
		this.vars.varsColumn = this._updateSortColumnLineColumn({
			arr  : this.vars.varsColumn,
			vars : obj.vars
		});
		if (this.vars.varsStatus.flagPageUse) {
			this.allot({
				insCurrent : this.insCurrent,
				from       : '_mousedownSortColumnLine',
				vars       : obj.vars
			});
			this.setCake();
			return;
		}
		this.vars.varsDetail = this._updateSortColumnLineDetail({
			arr : this.vars.varsDetail
		});
		this.setCake();
		this.iniReload({from : '_mousedownSortColumnLine'});
	},

	/**
	 *
	*/
	_updateSortColumnLineColumn : function(obj)
	{
		var arrection = null;
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == obj.vars.id) {
				if (!obj.arr[i].flagSortColumnLineNow) obj.arr[i].flagSortColumnLineNow = 1;
				else  obj.arr[i].flagSortColumnLineNow = 0;
				arrection = obj.arr[i].flagSortColumnLineNow;
			}
			else obj.arr[i].flagSortColumnLineNow = 0;
		}

		return obj.arr;
	},

	/**
	 *
	*/
	_updateSortColumnLineDetail : function(obj)
	{
		var arrection = this._checkSortColumnLineDirection({
			arr : this.vars.varsColumn
		});
		var str = this.vars.varsStatus.flagSortColumnLineNow;
		if (!arrection) {
			obj.arr = obj.arr.sortBy(function(v, i) {
				return v.varsColumnDetail[str];
			});

		} else {
			obj.arr = obj.arr.sortBy(function(v, i) {
				return v.varsColumnDetail[str] * (-1);
			});
		}

		return obj.arr;
	},

	/**
	 *
	*/
	_checkSortColumnLineDirection : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == this.vars.varsStatus.flagSortColumnLineNow) {
				return obj.arr[i].flagSortColumnLineNow;
			}
		}
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
		var ele = $(this.idSelf + 'ColumnBox' + obj.vars.id).down('.codeLibTableColumnBoxSortColumn', 0);
		ele.addClassName('codeLibTableSortColumnOver');
	},

	/**
	 *
	*/
	_mouseoutSortColumn : function(obj)
	{
		if (!this._varsSortColumn.flag) return;
		this._varsSortColumn.varsOver = '';
		var ele = $(this.idSelf + 'ColumnBox' + obj.vars.id).down('.codeLibTableColumnBoxSortColumn', 0);
		ele.removeClassName('codeLibTableSortColumnOver');
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
		ele.addClassName('codeLibTableNavi');
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
	_removeCursorColumn : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagCheckNow) continue;
			if (obj.arr[i].flagSortColumnLineUse && this.vars.varsStatus.flagSortColumnLineUse) {
				var ele = $(this.idSelf + 'ColumnBox' + obj.arr[i].id).down('.codeLibTableColumnBoxSortLine', 0);
				ele.removeClassName('codeLibBaseCursorPointer');
			}
			if (obj.arr[i].flagType == 'checkbox' && obj.arr[i].flagAllCheckbox) {
				var ele  = $(this.idSelf + 'ColumnBox' + obj.arr[i].id).down('.codeLibTableColumnBoxCheckbox', 0);
				ele.removeClassName('codeLibBaseCursorPointer');
				ele = $(this.idSelf + 'ColumnBox' + obj.arr[i].id).down('.codeLibTableColumnBoxCheckbox', 0);
				ele.addClassName('codeLibBaseCursorDefault');
			}
			if (this.vars.varsStatus.flagSortColumnUse) {
				var ele = $(this.idSelf + 'ColumnBox' + obj.arr[i].id).down('.codeLibTableColumnBoxTitle', 0);
				ele.removeClassName('codeLibBaseCursorMove');
				ele = $(this.idSelf + 'ColumnBox' + obj.arr[i].id).down('.codeLibTableColumnBoxTitle', 0);
				ele.addClassName('codeLibBaseCursorDefault');
			}
			if (this.vars.varsStatus.flagResizeUse) {
				var ele = $(this.idSelf + 'ColumnBox' + obj.arr[i].id).down('.codeLibTableColumnBoxResize', 0);
				ele.removeClassName('codeLibBaseCursorColResize');
			}
		}
	},

	/**
	 *
	*/
	_removeCursorLine : function(obj)
	{
		if (!this.vars.varsStatus.flagMoveUse) return;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagMoveUse) continue;
			var ele = $(this.idSelf + 'Line' + obj.arr[i].id).down('.codeLibTableMove', 0);
			ele.removeClassName('codeLibBaseCursorMove');
		}
	},


	/**
	 *
	*/
	_setCursorLine : function(obj)
	{
		if (!this.vars.varsStatus.flagMoveUse) return;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagMoveUse) continue;
			var ele = $(this.idSelf + 'Line' + obj.arr[i].id).down('.codeLibTableMove', 0);
			ele.addClassName('codeLibBaseCursorMove');
		}
	},

	/**
	 *
	*/
	_dblclickTitle : function(evt, obj)
	{
		evt.stop();
		this.allot({
			insCurrent : this.insCurrent,
			from       : '_dblclickTitle',
			vars       : {
				idColumn : obj.idColumn,
				vars     : obj.vars
			}
		});
	},

	/**
	 *
	*/
	_mouseoverTitle : function(obj)
	{
		obj.ele.addClassName('codeLibTableLineTitleOver');
	},

	/**
	 *
	*/
	_mouseoutTitle : function(obj)
	{
		obj.ele.removeClassName('codeLibTableLineTitleOver');
	},

	/**
	 * Key
	*/
	_varsKey : {shiftkey : null, ctrlkey : null},
	_iniKey : function()
	{
		this._setKeyLisener();
	},

	/**
	 *
	*/
	_setKeyLisener : function()
	{
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'keydown',
			strFunc : '_keydownKey', ele : document, vars : ''
		});
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'keyup',
			strFunc : '_keyupKey', ele : document, vars : ''
		});
	},

	/**
	 *
	*/
	_keydownKey : function(evt) {
		this._varsKey = {
			shiftkey : evt.shiftKey,
			ctrlkey  : evt.ctrlKey
		};
	},

	/**
	 *
	*/
	_keyupKey : function(evt) {
		this._resetKey();
	},

	/**
	 *
	*/
	_resetKey : function()
	{
		this._varsKey = {
			shiftkey : null,
			ctrlkey  : null
		};
	},

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
	setVarsBtn : function(obj)
	{
		this._varsBtn = obj.vars;
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
		this._removeBtnSelect({arr:this.vars.varsDetail});
		if (!this._varsBtn.length) {
			if (this._varsKey.shiftkey && this.vars.varsStatus.flagKeyBtnUse) {
				this._varBtnShiftkey({
					idStart  : this.vars.varsDetail[0].id,
					vars     : obj.vars
				});
			}
			else this._varsBtn.push(obj.vars);

		} else {
			if (this._varsKey.shiftkey && this.vars.varsStatus.flagKeyBtnUse) {
				this._varBtnShiftkey({
					idStart  : this._varsBtn[0].id,
					vars     : obj.vars
				});

			} else if (this._varsKey.ctrlkey && this.vars.varsStatus.flagKeyBtnUse) {
				this._resetKey();
				var flag = this._checkBtnSelectCtrlkey({
					arr : this._varsBtn,
					id  : obj.vars.id
				});
				if (flag) {
					this._varsBtn = this._varBtnSelectRemove({
						arr : this._varsBtn,
						id  : obj.vars.id
					});
				} else {
					this._varsBtn.push(obj.vars);
				}

			} else {
				this._resetKey();
				this._varsBtn = [];
				this._varsBtn.push(obj.vars);
			}
		}
		if (obj.vars.flagCheckboxUse) {
			this._updateLineCheckboxSelect({
				idTarget  : obj.vars.id,
				arrColumn : this.vars.varsColumn,
				arr       : this.vars.varsDetail
			});
		}
		this._setBtnSelect({
			arr       : this.vars.varsDetail,
			arrSelect : this._varsBtn,
		});
		this._setBtnSelect({
			arr       : this.vars.varsDetail,
			arrSelect : this._varsBtn,
		});
		this.updateVars();
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
	_updateLineCheckboxSelect : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagCheckboxUse) continue;
			if (obj.arr[i].id != obj.idTarget) continue;
			for (var j = 0; j < obj.arrColumn.length; j++) {
				if (!obj.arrColumn[j].flagCheckNow) continue;
				if ($(this.idSelf + 'Line' + obj.arr[i].id + 'Checkbox' + obj.arrColumn[j].id)) {
					if (obj.arr[i].flagCheckboxNow) {
						obj.arr[i].flagCheckboxNow = 0;
						$(this.idSelf + 'Line' + obj.arr[i].id + 'Checkbox' + obj.arrColumn[j].id).checked = false;
					} else {
						obj.arr[i].flagCheckboxNow = 1;
						$(this.idSelf + 'Line' + obj.arr[i].id + 'Checkbox' + obj.arrColumn[j].id).checked = true;
					}
				}
			}
		}
	},

	/**
	 *
	*/
	_varBtnShiftkey : function(obj)
	{
		this._resetKey();
		var flag = this._checkBtnSelectShiftkey({
			arr      : this.vars.varsDetail,
			idFirst  : obj.idStart,
			idSecond : obj.vars.id
		});

		if (flag == 0) {
			this._varsBtn = [];
			this._varsBtn.push(obj.vars);

		} else if (flag < 0) {
			var idStart = obj.idStart;
			this._varsBtn = [];
			this._varsBtn = this._varBtnSelectShiftkey({
				arr     : this.vars.varsDetail,
				idStart : idStart,
				idEnd   : obj.vars.id
			});

		} else if (flag > 0) {
			var idStart = this._varsBtn[0].id;
			this._varsBtn = [];
			this._varsBtn = this._varBtnSelectShiftkeyReverse({
				arr     : this.vars.varsDetail,
				idStart : idStart,
				idEnd   : obj.vars.id
			});
		}
	},

	/**
	 *
	*/
	_mouseoverBtn : function(obj)
	{
		$(this.idSelf + 'Line' + obj.vars.id).addClassName('codeLibTableLineBtnOver');
	},

	/**
	 *
	*/
	_mouseoutBtn : function(obj)
	{
		$(this.idSelf + 'Line' + obj.vars.id).removeClassName('codeLibTableLineBtnOver');
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
	_varBtnSelect : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagBtnUse) continue;
			for (var j = 0; j < obj.arrSelect.length; j++) {
				if (obj.arr[i].id == obj.arrSelect[j].id) {
					$(this.idSelf + 'Line' + obj.arr[i].id).addClassName('codeLibTableBtnSelect');
					break;
				}
			}
		}
	},

	/**
	 *
	*/
	_checkBtnSelectCtrlkey : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == obj.id) return 1;
		}
	},

	/**
	 *
	*/
	_checkBtnSelectShiftkey : function(obj)
	{
		var numFirst;
		var numSecond;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagBtnUse) continue;
			if (obj.arr[i].id == obj.idFirst) numFirst = i;
			if (obj.arr[i].id == obj.idSecond) numSecond = i;
		}

		return numFirst - numSecond;
	},

	/**
	 *
	*/
	_varBtnSelectShiftkey : function(obj)
	{
		var array = [];
		var flag;
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagBtnUse) continue;
			if (obj.arr[i].id == obj.idStart) flag = 1;
			if (flag) array.push(obj.arr[i]);
			if (obj.arr[i].id == obj.idEnd) return array;
		}
	},

	/**
	 *
	*/
	_varBtnSelectShiftkeyReverse : function(obj)
	{
		var array = [];
		var flag;
		for (var i = obj.arr.length - 1; i >= 0; i--) {
			if (!obj.arr[i].flagBtnUse) continue;
			if (obj.arr[i].id == obj.idStart) flag = 1;
			if (flag) array.push(obj.arr[i]);
			if (obj.arr[i].id == obj.idEnd) return array;
		}
	},

	/**
	 *
	*/
	_varBtnSelectRemove : function(obj)
	{
		var num = 0;
		for (var i = 0; i < obj.arr.length; i++) {
			if (obj.arr[i].id == obj.id) {
				num = i;
			}
		}
		obj.arr.splice(num, 1);

		return obj.arr;
	},

	/**
	 *
	*/
	_setBtnSelect : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagBtnUse) continue;
			for (var j = 0; j < obj.arrSelect.length; j++) {
				if (obj.arr[i].id == obj.arrSelect[j].id) {
					this._removeBtnSelectLoad({
						vars : obj.arr[i]
					});
					this._removeBtnSelectBold({
						arr  : this.vars.varsColumn,
						vars : obj.arr[i]
					});
					$(this.idSelf + 'Line' + obj.arr[i].id).addClassName('codeLibTableBtnSelect');
				}
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
	_removeBtnSelect : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagBtnUse) continue;
			$(this.idSelf + 'Line' + obj.arr[i].id).removeClassName('codeLibTableBtnSelect');
		}
	},

	/**
	 *
	*/
	_removeBtnSelectLoad : function(obj)
	{
		if (obj.vars.strClassLoad == '') return;
		$(this.idSelf + 'Line' + obj.vars.id).down('.codeLibTableLineImg', 0).removeClassName(obj.vars.strClassLoad);
		obj.vars.strClassLoad = '';

		$(this.idSelf + 'Line' + obj.vars.id).down('.codeLibTableLineImg', 0).addClassName(obj.vars.strClass);
		this.updateVars();
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
			if ($(this.idSelf + 'Line' + obj.vars.id).down('.codeLibTableLineTitle', i)) {
				var ele = $(this.idSelf + 'Line' + obj.vars.id).down('.codeLibTableLineTitle',i);
				ele.removeClassName('codeLibBaseFontBold');
			}
		}
	},

	/**
	 *
	*/
	_staticLine : {numIdle : 10, numLength : 25, numPadding : 5},
	_iniLine : function()
	{
		this._setLineWrap();
		if (this.vars.varsStatus.flagSortColumnLineUse) {
			if (this.vars.varsStatus.flagSortColumnLineNow) {
				this.vars.varsDetail = this._updateSortColumnLineDetail({
					arr : this.vars.varsDetail
				});
			}
		}

		if (!this.vars.varsDetail.length) {
			this.eleWrapLine.setStyle({
				width  : this._getColumnWidth() + 'px',
				height : '1px'
			});

		} else {
			var temp = this.vars.varsHtml.interpolate({idSelf : this.idSelf});
			this.eleWrapLine.insert(temp);
			this._setLine({
				arrColumn : this.vars.varsColumn,
				arr       : this.vars.varsDetail
			});
		}
	},

	/**
	 *
	*/
	eleWrapLine : null,
	_setLineWrap : function()
	{
		var ele = $(document.createElement('div'));
		ele.addClassName('codeLibTableLineWrap');
		this.insFormat.eleTemplate.body.insert(ele);
		this.eleWrapLine = ele;
	},

	/**
	 *
	*/
	_setLine : function(obj)
	{
		var insEscape = new Code_Lib_Escape();

		for (var i = 0; i < obj.arr.length; i++) {
			var eleLine = $(this.idSelf + 'Line' + obj.arr[i].id);
			eleLine.setStyle({
				width : (this._getColumnWidth()) + 'px'
			});

			if (obj.arr[i].flagBtnUse) {
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
				this.insListener.set({
					bindAsEvent : 0, insCurrent : this, event : 'mouseout',
					strFunc : '_mouseoutBtn', ele : eleLine, vars : { vars : obj.arr[i] }
				});
			}

			var eleImg = $(this.idSelf + 'Line' + obj.arr[i].id + '_eleImg');

			if (!obj.arr[i].strClassLoad) {
				if (eleImg.hasClassName('codeLibTableLineLoad')) {
					eleImg.removeClassName('codeLibTableLineLoad');
					eleImg.addClassName(obj.arr[i].strClass);
				}
			}

			if (this.vars.varsStatus.flagMoveUse && obj.arr[i].flagMoveUse) {
				this.insListener.set({
					bindAsEvent : 1, insCurrent : this, event : 'mousedown',
					strFunc : '_mousedownMove', ele : eleImg, vars : { vars : obj.arr[i] }
				});
				this.insListener.set({
					bindAsEvent : 0, insCurrent : this, event : 'mouseover',
					strFunc : '_mouseoverMove', ele : eleImg, vars : { vars : obj.arr[i] }
				});
				this.insListener.set({
					bindAsEvent : 0, insCurrent : this, event : 'mouseout',
					strFunc : '_mouseoutMove', ele : eleImg, vars : { vars : obj.arr[i] }
				});
			}

			for (var j = 0; j < obj.arrColumn.length; j++) {

				var eleWrapItem = $(this.idSelf + 'Line' + obj.arr[i].id + '_eleWrapItem_' + obj.arrColumn[j].id);
				/*var eleItemIdle = $(this.idSelf + 'Line' + obj.arr[i].id + '_eleItemIdle_' + obj.arrColumn[j].id);*/

				if (!obj.arrColumn[j].flagCheckNow) {
					eleWrapItem.hide();
				}
				eleLine.insert(eleWrapItem);

				var eleItem = $(this.idSelf + 'Line' + obj.arr[i].id + '_eleItem_' + obj.arrColumn[j].id);

				var eleTitle = $(this.idSelf + 'Line' + obj.arr[i].id + '_eleTitle_' + obj.arrColumn[j].id);

				eleItem.setStyle({width : (obj.arrColumn[j].numWidth - this._staticLine.numIdle) + 'px'});

				var str = insEscape.strLowCapitalize({data : obj.arrColumn[j].id});

				if (obj.arrColumn[j].flagType == 'str') {
					if (obj.arrColumn[j].flagAlign) {
						var numWidthEleTitle = obj.arrColumn[j].numWidth - this._staticLine.numIdle - this._staticLine.numPadding;
						eleTitle.setStyle({
							width        : numWidthEleTitle + 'px',
							paddingRight : this._staticLine.numPadding + 'px',
							textAlign    : obj.arrColumn[j].flagAlign
						});
					}

				} else if (obj.arrColumn[j].flagType == 'checkbox') {
					if (obj.arr[i].flagCheckboxUse) {
						this._iniCheckboxLine({
							id              : this.idSelf + 'Line' + obj.arr[i].id + 'Checkbox' + obj.arrColumn[j].id,
							flagCheckboxNow : obj.arr[i].flagCheckboxNow,
							eleInsert       : eleTitle
						});
					}
				}

				if (obj.arrColumn[j].flagType == 'str' || obj.arrColumn[j].flagType == 'stamp') {
					if (this.vars.varsStatus.flagTextBtnUse) {
						this.insListener.set({
							bindAsEvent : 1, insCurrent : this, event : 'dblclick',
							strFunc : '_dblclickTitle', ele : eleTitle, vars : { vars : obj.arr[i], idColumn : obj.arrColumn[j].id}
						});
					}

					this.insListener.set({
						bindAsEvent : 0, insCurrent : this, event : 'mouseover',
						strFunc : '_mouseoverTitle', ele : eleTitle, vars : { ele : eleTitle }
					});
					this.insListener.set({bindAsEvent : 0, insCurrent : this, event : 'mouseout',
						strFunc : '_mouseoutTitle', ele : eleTitle, vars : { ele : eleTitle }
					});
				}
			}
			var eleLineSort = $(this.idSelf + 'Line' + obj.arr[i].id + '_eleLineSort');
			eleLineSort.setStyle({
				width : this._getColumnWidth() + 'px'
			});
		}
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
	_staticColumn : {numWidth : 5, numHeight : 16, numOther : 10, numBlock : 16, numBar : 17, numMin : 26},
	eleWrapColumn : null,
	_wrapColumn : function()
	{
		var ele = $(document.createElement('div'));
		ele.addClassName('codeLibTableColumnWrap');
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
		var eleMenu = $(document.createElement('span'));
		eleMenu.addClassName('codeLibTableColumn');
		if (this.vars.varsStatus.flagMenuUse) {
			eleMenu.addClassName('codeLibTableColumnBoxImgMenu');
			eleMenu.addClassName('codeLibBaseCursorPointer');
		} else {
			eleMenu.addClassName('codeLibBaseMarginRightFive');
			eleMenu.addClassName('codeLibTableBlock');
		}
		eleMenu.unselectable = 'on';
		eleMenu.addClassName('unselect');
		this.eleWrapColumn.insert(eleMenu);
		if (this.vars.varsStatus.flagMenuUse) {
			this.insListener.set({
				bindAsEvent : 1, insCurrent : this, event : 'mousedown',
				strFunc : '_mousedownMenu', ele : eleMenu, vars : {}
			});
		}
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagCheckNow) continue;
			var eleColumn = $(document.createElement('span'));
			eleColumn.addClassName('codeLibTableColumnBoxWrap');
			eleColumn.addClassName('codeLibTableColumnBox');
			eleColumn.id = this.idSelf + 'ColumnBox' + obj.arr[i].id;
			eleColumn.addClassName('clearfix');
			eleColumn.unselectable = 'on';
			eleColumn.addClassName('unselect');
			eleColumn.title = obj.arr[i].strTitle;
			this.eleWrapColumn.insert(eleColumn);
			var eleSortColumn = $(document.createElement('span'));
			eleSortColumn.addClassName('codeLibTableColumnBoxSortColumn');
			eleSortColumn.setStyle({
				height : this._staticColumn.numHeight + 'px',
				width  : this._staticColumn.numWidth + 'px'
			});
			eleColumn.insert(eleSortColumn);

			/*sortLine*/
			var eleSort = $(document.createElement('span'));
			eleSort.addClassName('codeLibTableColumnBoxSortLine');
			eleColumn.insert(eleSort);
			if (obj.arr[i].flagCheckNow) {
				if (obj.arr[i].flagSortColumnLineUse
					&& this.vars.varsStatus.flagSortColumnLineUse
				) {
					eleSort.addClassName('codeLibBaseCursorPointer');
				}
			}
			if (obj.arr[i].flagCheckNow && this.vars.varsStatus.flagSortColumnLineUse) {
				if (obj.arr[i].flagSortColumnLineUse) {
					if (this.vars.varsStatus.flagSortColumnLineNow == obj.arr[i].id) {
						if (obj.arr[i].flagSortColumnLineNow) {
							eleSort.addClassName('codeLibTableColumnBoxImgDown');
						}
						else eleSort.addClassName('codeLibTableColumnBoxImgUp');
					}
					else eleSort.addClassName('codeLibTableColumnBoxImgPoint');
				}
				else eleSort.addClassName('codeLibTableColumnBoxImgNone');
			}
			if (obj.arr[i].flagSortColumnLineUse
				&& this.vars.varsStatus.flagSortColumnLineUse
				&& obj.arr[i].flagCheckNow
			) {
				this.insListener.set({
					bindAsEvent : 1, insCurrent : this, event : 'mousedown',
					strFunc : '_mousedownSortColumnLine', ele : eleSort, vars : { vars : obj.arr[i] }
				});
			}
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
			eleTitle.addClassName('codeLibTableColumnBoxTitle');
			eleTitle.insert(obj.arr[i].strTitle);
			if (obj.arr[i].flagCheckNow) {
				if (this.vars.varsStatus.flagSortColumnUse) {
					eleTitle.removeClassName('codeLibBaseCursorDefault');
					eleTitle.addClassName('codeLibBaseCursorMove');
				}
				else eleTitle.addClassName('codeLibBaseCursorDefault');
			}
			var eleResize = $(document.createElement('span'));
			eleResize.addClassName('codeLibTableColumnBoxResize');
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

			var numWidth = 0;
			if ((obj.arr[i].flagSortColumnLineUse && this.vars.varsStatus.flagSortColumnLineUse)
				|| (obj.arr[i].flagType == 'checkbox' && obj.arr[i].flagAllCheckbox)
			) {
				numWidth = obj.arr[i].numWidth - this._staticColumn.numOther;
				eleCheckbox.setStyle({
					width : numWidth + 'px'
				});
				if (numWidth > 16) {
					eleTitle.setStyle({
						width : numWidth - 16 + 'px'
					});
				}
				eleTitle.innerHTML = '';
				eleResize.hide();
				eleColumn.setStyle({
					height : this._staticColumn.numHeight + 'px',
					width  : (obj.arr[i].numWidth) + 'px'
				});

			} else {
				eleColumn.setStyle({
					height : this._staticColumn.numHeight + 'px',
					width  : obj.arr[i].numWidth + 'px'
				});
				numWidth = obj.arr[i].numWidth - this._staticColumn.numOther;
				eleTitle.setStyle({width : numWidth + 'px'});

			}



			if (obj.arr[i].flagCheckNow && this.vars.varsStatus.flagSortColumnUse) {
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
	 * Checkbox
	*/
	_iniCheckboxLine : function(obj)
	{
		this._setCheckboxLine({
			id              : obj.id,
			flagCheckboxNow : obj.flagCheckboxNow,
			eleInsert       : obj.eleInsert
		});
		this._setCheckboxLineListener({
			id : obj.id
		});

	},

	/**
	 *
	*/
	_setCheckboxLine : function(obj)
	{
		if (obj.flagCheckboxNow) {
			$(obj.id).checked = true;
		} else {
			$(obj.id).checked = false;
		}
		var ele = obj.eleInsert;
		if (Prototype.Browser.IE) ele.addClassName('ie');
		else if (Prototype.Browser.Gecko) ele.addClassName('firefox');
		else if (navigator.userAgent.match("Chrome")) ele.addClassName('chrome');
	},

	/**
	 *
	*/
	_setCheckboxLineListener : function(obj)
	{
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'mousedown',
			strFunc : '_mousedownCheckboxLine', ele : $(obj.id), vars : { id : obj.id }
		});
	},

	/**
	 *
	*/
	_mousedownCheckboxLine : function(evt, obj) {
		evt.stop();
		this._updateCheckboxLine({
			idTarget  : obj.id,
			arrColumn : this.vars.varsColumn,
			arr       : this.vars.varsDetail
		});
	},

	/**
	 *
	*/
	_updateCheckboxLine : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagCheckboxUse) continue;
			for (var j = 0; j < obj.arrColumn.length; j++) {
				if (!obj.arrColumn[j].flagCheckNow) continue;
				if (this.idSelf + 'Line' + obj.arr[i].id + 'Checkbox' + obj.arrColumn[j].id == obj.idTarget) {
					obj.arr[i].flagCheckboxNow = (obj.arr[i].flagCheckboxNow)? 0 : 1;
					return;
				}
			}
		}
	},

	/**
	 *
	*/
	getCheckboxLineId : function()
	{
		return this._getCheckboxLineId({
			arrColumn : this.vars.varsColumn,
			arr       : this.vars.varsDetail
		});
	},

	/**
	 *
	*/
	_getCheckboxLineId : function(obj)
	{
		var arrayNew = [];
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagCheckboxUse) continue;
			for (var j = 0; j < obj.arrColumn.length; j++) {
				if (!obj.arrColumn[j].flagCheckNow) continue;
				if (obj.arr[i].flagCheckboxNow) {
					arrayNew.push(obj.arr[i].vars.idTarget);
					break;
				}
			}
		}

		return arrayNew;
	},

	/**
	 *
	*/
	getCheckboxLineIdTitle : function()
	{
		return this._getCheckboxLineIdTitle({
			arrColumn : this.vars.varsColumn,
			arr       : this.vars.varsDetail
		});
	},

	/**
	 *
	*/
	_getCheckboxLineIdTitle : function(obj)
	{
		var objNew = {};
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagCheckboxUse) continue;
			for (var j = 0; j < obj.arrColumn.length; j++) {
				if (!obj.arrColumn[j].flagCheckNow) continue;
				if (obj.arr[i].flagCheckboxNow) {
					objNew[obj.arr[i].id] = obj.arr[i].strTitle;
					break;
				}
			}
		}

		return objNew;
	},

	/**
	 *
	*/
	_iniCheckboxColumn : function(obj)
	{
		this._setCheckboxColumn({
			id        : obj.id,
			vars      : obj.vars,
			eleInsert : obj.eleInsert
		});
		this._setCheckboxColumnListener({
			vars : obj.vars,
			id   : obj.id
		});
	},

	/**
	 *
	*/
	_setCheckboxColumn : function(obj)
	{
		var ele = obj.eleInsert;
		var eleCheckbox = $(document.createElement('span'));
		ele.insert(eleCheckbox);
		eleCheckbox.addClassName('codeLibTableColumnBoxCheckbox');
		eleCheckbox.id = obj.id;
		if (obj.vars.flagAllCheckboxNow) eleCheckbox.addClassName('codeLibTableColumnBoxCheckboxChecked');
		else eleCheckbox.addClassName('codeLibTableColumnBoxCheckboxUnchecked');
		if (obj.vars.flagType == 'checkbox' && obj.vars.flagAllCheckbox) {
			eleCheckbox.removeClassName('codeLibBaseCursorDefault');
			eleCheckbox.addClassName('codeLibBaseCursorPointer');
		}
	},

	/**
	 *
	*/
	_setCheckboxColumnListener : function(obj)
	{
		this.insListener.set({
			bindAsEvent : 1, insCurrent : this, event : 'mousedown',
			strFunc : '_mousedownCheckboxColumn', ele : $(obj.id), vars : { vars : obj.vars }
		});
	},

	/**
	 *
	*/
	_mousedownCheckboxColumn : function(evt, obj)
	{
		if (obj) evt.stop();
		else obj = evt;
		var ele = $(this.idSelf + 'ColumnBox' + obj.vars.id + 'Checkbox');
		ele.removeClassName('codeLibTableColumnBoxCheckboxChecked');
		ele.removeClassName('codeLibTableColumnBoxCheckboxUnchecked');
		if (obj.vars.flagAllCheckboxNow) {
			obj.vars.flagAllCheckboxNow = 0;
			ele.addClassName('codeLibTableColumnBoxCheckboxUnchecked');
		} else {
			obj.vars.flagAllCheckboxNow = 1;
			ele.addClassName('codeLibTableColumnBoxCheckboxChecked');
		}
		this._updateCheckboxColumn({
			flag      : obj.vars.flagAllCheckboxNow,
			id        : obj.vars.id,
			arrColumn : this.vars.varsColumn,
			arr       : this.vars.varsDetail
		});
	},

	/**
	 *
	*/
	_updateCheckboxColumn : function(obj)
	{
		for (var i = 0; i < obj.arr.length; i++) {
			if (!obj.arr[i].flagCheckboxUse) continue;
			for (var j = 0; j < obj.arrColumn.length; j++) {
				if (!obj.arrColumn[j].flagCheckNow) continue;
				if (obj.arrColumn[j].id == obj.id) {
					if (!obj.flag) {
						obj.arr[i].flagCheckboxNow = 0;
						$(this.idSelf + 'Line' + obj.arr[i].id + 'Checkbox' + obj.id).checked = false;
					} else {
						obj.arr[i].flagCheckboxNow = 1;
						$(this.idSelf + 'Line' + obj.arr[i].id + 'Checkbox' + obj.id).checked = true;
					}
				}
			}
		}
	},

	/**
	 * Format
	*/
	_iniFormat : function()
	{
		this._extFormat();
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